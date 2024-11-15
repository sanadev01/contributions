<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login; 
use App\UserLoginDetail;
use Illuminate\Support\Facades\Request;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;

class LogUserLoginDetail
{
    public function handle($event)
    {
        $this->logAttempt($event->user ?? null, $event instanceof Login);
    }

    protected function logAttempt($user, $successful)
    {
            UserLoginDetail::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => Request::ip(),
                'device' => Request::header('User-Agent'),
                'location' =>  $user ? $this->getLocation(Request::ip()):'Unknown Location', 
                'successful' => $successful,
            ]); 
    } 
    protected function getLocation($ip)
    {
            if ($ip == '127.0.0.1' || $ip == '::1'|| App::environment('local')) {
                return 'Localhost';
            }
            $client = new Client();
            $apiKey = env('IPINFO_API_KEY');
            try {
                $response = $client->get("https://ipinfo.io/{$ip}?token={$apiKey}");
                $data = json_decode($response->getBody(), true); 
                if (isset($data['city'], $data['region'], $data['country'])) {
                    return "{$data['city']}, {$data['region']}, {$data['country']}";
                }
            } catch (\Exception $e) { 
                \Log::error("Failed to retrieve location for IP {$ip}: " . $e->getMessage());
            }
    
            return 'Unknown Location'; 
    }
}
