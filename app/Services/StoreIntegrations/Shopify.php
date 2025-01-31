<?php 

namespace App\Services\StoreIntegrations;

use App\Models\Connect;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Shopify{

    private $client;

    private $key;
    private $secret;
    private $accessToken;


    public function __construct()
    {
        $this->key = 'b7a4cd09325d145228a156632e865176';
        $this->secret = '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';

    }

    public function getRedirectUrl($storeUrl, $data=[], $scopes=['read_orders'])
    {
        if ( substr($storeUrl,-1) == '/' ){
            $storeUrl = substr($storeUrl,0, strlen($storeUrl)-1);
        }
        if ( strpos($storeUrl,'https://')  === false ){
            $storeUrl = 'https://'.$storeUrl;
        }

        $scope = implode(',',$scopes);
        $redirectUri = route('admin.webhooks.shopify.redirect_uri');
        $nounce = md5(microtime());
        session()->put('shopify-nounce',['nounce' => $nounce, 'data' => $data]);

        return "{$storeUrl}/admin/oauth/authorize?client_id={$this->key}&scope={$scope}&redirect_uri={$redirectUri}&state={$nounce}";
    }

    public function getAccessToken(Request $request)
    {
        $session = session()->get('shopify-nounce');

        if ( !$session  || !isset($session['nounce'])){
            abort(400,"Bad Request");
        }

        if ( $session['nounce'] !== $request->state ){
            abort(400,"Bad Request");
        }

        $client = new Client([
            'base_uri' => $session['data']['connect_store_url']
        ]);

        try {
            $tokenResponse = $client->post('/admin/oauth/access_token',[
                'json' => [
                    'client_id' => $this->key,
                    'client_secret' => $this->secret,
                    'code' => $request->code
                ]
            ]);
        } catch (Exception $th) {
            abort(400,'Bad Request');
        }

        $accessTokenResponse = json_decode($tokenResponse->getBody()->getContents(),true);

        return [
            'auth_token' => $accessTokenResponse['access_token'],
            'store_name' => $session['data']['connect_name'],
            'store_url' => $session['data']['connect_store_url'],
            'type' => Connect::TYPE_SHOPIFY,
            'user_id' => Auth::id()
        ];
    }

    public function addWebook(Connect $connect)
    {
        $client = new Client([
            'base_uri' => $connect->store_url,
            'headers' => [
                'X-Shopify-Access-Token' => $connect->auth_token
            ]
        ]);

        try {
            $webhookResponse = $client->post('/admin/api/2020-10/webhooks.json',[
                'json' => [
                    'webhook' => [
                        'topic' => 'orders/create',
                        'address' => route('admin.webhooks.shopify.order.create',['callbackUser'=> base64_encode(Auth::id()),'connectId'=> base64_encode($connect->id)]),
                        // 'address' => 'https://3fe3231b56e7.ngrok.io/webhooks/shopify/shopify/order/create?callbackUser='.base64_encode(Auth::id()),
                        'format' => 'json',
                    ]
                ]
            ]);

        } catch (\Exception $th) {
            // $connect->delete();
            abort(400,'Bad Request'.$th->getMessage());
        }

        return json_decode($webhookResponse->getBody()->getContents());
    }

    public function deleteWebhook(Connect $connect)
    {
        $client = new Client([
            'base_uri' => $connect->store_url,
            'headers' => [
                'X-Shopify-Access-Token' => $connect->auth_token
            ]
        ]);

        try {
            $webhookResponse = $client->delete("/admin/api/2020-10/webhooks/{$connect->webhook['id']}.json");
            if ( $webhookResponse->getStatusCode() == 200 ){
                return true;
            }

            return false;
        } catch (\Exception $th) {
            return false;
        }
    }
}