<?php
namespace App\Support\Socialite;

class Socialite
{
    protected $config;
    
    public function __construct()
    {
        $this->config = config('services.google');
    }
    
    public static function driver($driver)
    {
        if ($driver !== 'google') {
            throw new \Exception("Only Google driver is supported");
        }
        
        return (new static)->createGoogleDriver();
    }
    
    protected function createGoogleDriver()
    {
        return new GoogleProvider(
            $this->config['client_id'], 
            $this->config['client_secret'], 
            $this->config['redirect']
        );
    }
}