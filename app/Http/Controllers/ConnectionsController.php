<?php

namespace App\Http\Controllers;

use AmazonSellingPartner\Model\Sellers\MarketplaceParticipation;
use App\DataTables\ConnectionsDataTable;
use App\Models\BugReport;
use App\Models\Marketplace;
use App\Models\SpToken;
use App\Models\User;
use App\AmazonSPClients\AuthApiClient;
use App\AmazonSPClients\SellersApiClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Http\Client\ClientExceptionInterface;

class ConnectionsController extends Controller {

    public function getIndex(Request $request) {
        $data_table = new ConnectionsDataTable($this->user, request: $request);

        if ($request->ajax()) {
            return $data_table->getData();
        }

        return $this->renderView('connections', compact('data_table'));
    }

    public function getStatusChange(Request $request): JsonResponse {
        $request->validate(['account_id' => ['required']]);

        /** @var User $account */
        $account = User::query()->findOrFail($request->get('account_id'));

        $account->is_active = !$account->is_active;
        $account->save();

        return $this->resJson('Successfully changed status');
    }


    /**
     * @throws Exception
     */
    public function getAuth(Request $request) {
        $this->validate($request, ['region' => 'required']);

        return (new AuthApiClient($this->user, 'ACCESS_TOKEN'))
            ->authorizeConsent(
                $this->user->id,
                $request->get('region')
            );
    }

    public function getRegister(Request $request) {
        [$uid, $region] = explode('|', $request->get('state'));
        Log::info('getRegisterSp: ' . json_encode($request->all()));

        if ($this->user->id != $uid) {
            flash()->error('Something went wrong');
            return redirect('/');
        }

        $seller_id = $request->get('selling_partner_id');

        $connection = false;
        try {
            $response = (new AuthApiClient($this->user, 'ACCESS_TOKEN'))
                ->exchangeLwaCode(
                    $uid, $request->get('spapi_oauth_code')
                );

            $client = new SellersApiClient($this->user, $response->token(), $region);
            /** @var MarketplaceParticipation $participation */
            foreach ($client->listParticipations() as $participation) {
                /** @var Marketplace $marketplace */
                $marketplace = Marketplace::getById($participation->getMarketplace()->getId());
                if (!$marketplace || $marketplace->region_code !== $region) {
                    continue;
                }

                $account = $this->user->registerSeller($marketplace, $seller_id);

                SpToken::query()->updateOrCreate([
                    'user_id' => $account->id
                ], [
                    'access_token'    => $response->token(),
                    'refresh_token'   => $response->refreshToken(),
                    'token_type'      => $response->type(),
                    'expires_at'      => Carbon::now()->addSeconds(3000), // deliberately kept 600 secs less
                    'last_updated_at' => Carbon::now()
                ]);

                $connection = true;
            }

            if ($connection) {
                flash()->success('Successfully connected sellers');
            } else {
                flash()->error('Failed connecting sellers');
            }

        } catch (ClientExceptionInterface|Exception $ex) {
            BugReport::logException($ex, $this->user);
            flash()->error('Failed connecting sellers. ' . $ex->getMessage());
        }

        return redirect('/');
    }

}
