<?php
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
        session()->put('state', $state);
        
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
            'state' => $state,
        ]);
        
        return redirect('https://accounts.google.com/o/oauth2/auth?' . $query);
    }
    
    public function user()
    {
        $state = $this->request->input('state');
        
        if (!$state || $state !== session()->pull('state')) {
            throw new \InvalidArgumentException('Invalid state value.');
        }
        
        $token = $this->getAccessTokenResponse($this->getCode());
        $user = $this->getUserByToken($token);
        
        return $this->mapUserToObject($user, $token);
    }
    
    protected function getCode()
    {
        return $this->request->input('code');
    }
    
    protected function getAccessTokenResponse($code)
    {
        $client = new Client([
            'verify' => false // Disable SSL verification
        ]);
        
        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
            ],
        ]);
        
        return json_decode($response->getBody(), true);
    }
    
    protected function getUserByToken($token)
    {
        $client = new Client([
            'verify' => false // Disable SSL verification
        ]);
        
        $response = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token['access_token'],
            ],
        ]);
        
        return json_decode($response->getBody(), true);
    }
    
    protected function mapUserToObject(array $user, array $token)
    {
        return (object) [
            'id' => $user['sub'],
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['picture'] ?? null,
            'token' => $token['access_token'],
            'refreshToken' => $token['refresh_token'] ?? null,
        ];
    }
}