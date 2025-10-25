<?php
// This script installs a simplified version of Laravel Socialite

// First, let's create the directory structure
$socialiteDir = __DIR__ . '/app/Support/Socialite';
if (!is_dir($socialiteDir)) {
    mkdir($socialiteDir, 0755, true);
}

// Create the Socialite Facade
file_put_contents($socialiteDir . '/Socialite.php', '<?php
namespace App\Support\Socialite;

class Socialite
{
    protected $config;
    
    public function __construct()
    {
        $this->config = config(\'services.google\');
    }
    
    public static function driver($driver)
    {
        if ($driver !== \'google\') {
            throw new \Exception("Only Google driver is supported");
        }
        
        return (new static)->createGoogleDriver();
    }
    
    protected function createGoogleDriver()
    {
        return new GoogleProvider(
            $this->config[\'client_id\'], 
            $this->config[\'client_secret\'], 
            $this->config[\'redirect\']
        );
    }
}');

// Create the Google Provider
file_put_contents($socialiteDir . '/GoogleProvider.php', '<?php
namespace App\Support\Socialite;

use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class GoogleProvider
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUrl;
    protected $request;
    
    public function __construct($clientId, $clientSecret, $redirectUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->request = app(Request::class);
    }
    
    public function redirect()
    {
        $state = Str::random(40);
        session()->put(\'state\', $state);
        
        $query = http_build_query([
            \'client_id\' => $this->clientId,
            \'redirect_uri\' => $this->redirectUrl,
            \'response_type\' => \'code\',
            \'scope\' => \'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email\',
            \'state\' => $state,
        ]);
        
        return redirect(\'https://accounts.google.com/o/oauth2/auth?\' . $query);
    }
    
    public function user()
    {
        $state = $this->request->input(\'state\');
        
        if (!$state || $state !== session()->pull(\'state\')) {
            throw new \InvalidArgumentException(\'Invalid state value.\');
        }
        
        $token = $this->getAccessTokenResponse($this->getCode());
        $user = $this->getUserByToken($token);
        
        return $this->mapUserToObject($user, $token);
    }
    
    protected function getCode()
    {
        return $this->request->input(\'code\');
    }
    
    protected function getAccessTokenResponse($code)
    {
        $client = new Client([
            \'verify\' => false // Disable SSL verification
        ]);
        
        $response = $client->post(\'https://oauth2.googleapis.com/token\', [
            \'form_params\' => [
                \'client_id\' => $this->clientId,
                \'client_secret\' => $this->clientSecret,
                \'code\' => $code,
                \'redirect_uri\' => $this->redirectUrl,
                \'grant_type\' => \'authorization_code\',
            ],
        ]);
        
        return json_decode($response->getBody(), true);
    }
    
    protected function getUserByToken($token)
    {
        $client = new Client([
            \'verify\' => false // Disable SSL verification
        ]);
        
        $response = $client->get(\'https://www.googleapis.com/oauth2/v3/userinfo\', [
            \'headers\' => [
                \'Authorization\' => \'Bearer \' . $token[\'access_token\'],
            ],
        ]);
        
        return json_decode($response->getBody(), true);
    }
    
    protected function mapUserToObject(array $user, array $token)
    {
        return (object) [
            \'id\' => $user[\'sub\'],
            \'name\' => $user[\'name\'] ?? null,
            \'email\' => $user[\'email\'] ?? null,
            \'avatar\' => $user[\'picture\'] ?? null,
            \'token\' => $token[\'access_token\'],
            \'refreshToken\' => $token[\'refresh_token\'] ?? null,
        ];
    }
}');

// Create the service provider
file_put_contents(__DIR__ . '/app/Providers/SocialiteServiceProvider.php', '<?php
namespace App\Providers;

use App\Support\Socialite\Socialite;
use Illuminate\Support\ServiceProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\'socialite\', function ($app) {
            return new Socialite();
        });
    }

    public function boot()
    {
        // Nothing to do here
    }
}');

// Update the controller to use our custom Socialite
file_put_contents(__DIR__ . '/app/Http/Controllers/Auth/GoogleController.php', '<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Socialite\Socialite;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver(\'google\')->redirect();
        } catch (Exception $e) {
            return redirect()->route(\'login\')->with(\'error\', \'Google authentication failed: \' . $e->getMessage());
        }
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver(\'google\')->user();
            
            // Check if user exists
            $existingUser = User::where(\'google_id\', $googleUser->id)
                                ->orWhere(\'email\', $googleUser->email)
                                ->first();
            
            if ($existingUser) {
                // Update Google info if needed
                if (empty($existingUser->google_id)) {
                    $existingUser->update([
                        \'google_id\' => $googleUser->id,
                        \'google_token\' => $googleUser->token,
                        \'google_refresh_token\' => $googleUser->refreshToken,
                        \'avatar\' => $googleUser->avatar,
                    ]);
                }
                
                Auth::login($existingUser);
                return redirect()->intended(\'/\');
            }
            
            // Create new user
            $nameParts = $this->splitName($googleUser->name);
            
            $newUser = User::create([
                \'first_name\' => $nameParts[\'first_name\'],
                \'last_name\' => $nameParts[\'last_name\'],
                \'email\' => $googleUser->email,
                \'google_id\' => $googleUser->id,
                \'google_token\' => $googleUser->token,
                \'google_refresh_token\' => $googleUser->refreshToken,
                \'avatar\' => $googleUser->avatar,
                \'password\' => null,
                \'role\' => \'user\', // Default role
                \'email_verified_at\' => now(),
            ]);
            
            event(new Registered($newUser));
            
            Auth::login($newUser);
            return redirect()->intended(\'/\');
            
        } catch (Exception $e) {
            return redirect()->route(\'login\')
                ->with(\'error\', \'Google authentication failed: \' . $e->getMessage());
        }
    }
    
    /**
     * Split a full name into first name and last name parts.
     *
     * @param string $fullName
     * @return array
     */
    protected function splitName($fullName)
    {
        $nameParts = explode(\' \', $fullName, 2);
        
        return [
            \'first_name\' => $nameParts[0],
            \'last_name\' => isset($nameParts[1]) ? $nameParts[1] : \'\',
        ];
    }
}');

// Register the service provider in config/app.php
$appConfig = file_get_contents(__DIR__ . '/config/app.php');
if (strpos($appConfig, 'App\\Providers\\SocialiteServiceProvider::class') === false) {
    $appConfig = str_replace(
        'App\\Providers\\RouteServiceProvider::class,',
        'App\\Providers\\RouteServiceProvider::class,' . PHP_EOL . '        App\\Providers\\SocialiteServiceProvider::class,',
        $appConfig
    );
    file_put_contents(__DIR__ . '/config/app.php', $appConfig);
}

echo "===================================================\n";
echo "Custom Google OAuth implementation installed!\n";
echo "This solution bypasses the need for Laravel Socialite package.\n";
echo "\nPlease run: php artisan optimize:clear\n";
echo "===================================================\n";