<?php
namespace App\Services\PostNL;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;


class PostNLTrackingService
{
    protected $getTrackingURL;

    public function __construct($getTrackingURL = null)
    {
        $this->client = new GuzzleClient([]);

        if (app()->isProduction()) {
            $getTrackingURL = config('postnl.production.getTrackingUrl');
        }else {
            $getTrackingURL = config('postnl.testing.getTrackingUrl');
        }
        $this->getTrackingURL = $getTrackingURL;

    }

    private function getKeys()
    {
        $headers = [
            'api_key' => "Eo3qtkGlOh6t9S1HZxMvFkBSJYDTocatwMhBNwhnEoG7Jngng89GtVFmQOrc05OzcMwyLMTeQSYU2h4GsOOp0iy9Rp0qoYlhpGLfLpjNc8CuV3xqbrTGFYNkiZW6TWzdJWVgEsVLg64hYMLY1UElGjrOvxBpA4aI5prbWIefoMrd85y5WkuL1RQrfkH9vRCwod0v8feftgdEeZLYUkQWfYa1TVeeEe4fcbdk9twD6ynpjmq4E7FSLwdeiFIhqicw7a1kY63Bksp5ECq1pefkn0ROrCNjpy3TPdeLKO5I6LBc",
            'Accept' => "application/json",
            'Content-Type' => "application/json",

        ];
        return $headers;
    }

    public function trackOrder($trackingNumber)
    {
        try {
            $response = $this->client->post($this->getTrackingURL,[
                'headers' => $this->getKeys(),
                'json' => [
                    "items" => $trackingNumber
                ]
            ]);
            $data = json_decode($response->getBody()->getContents());
            if($data->status !== 'false' && $data->data->items[0]->events[0] != [])
            {
                return (Object)[
                    'status' => true,
                    'message' => 'Order Found',
                    'data' =>  $data,
                ];
            }else{
                return (Object)[
                    'status' => false,
                    'message' => $data->data->items[0]->message,
                    'data' =>  null,
                ];
            }

       } catch (Exception $e) {
            Log::info($e->getMessage());
            return (Object)[
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
       }
    }
}
