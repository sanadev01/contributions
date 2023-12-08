<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SpToken;
use App\Models\BugReport;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\AmazonSPClients\AuthApiClient;
use App\DataTables\ConnectionsDataTable;
use App\AmazonSPClients\SellersApiClient;
use Psr\Http\Client\ClientExceptionInterface;
use AmazonSellingPartner\Model\Sellers\MarketplaceParticipation;

class ConnectionsController extends Controller
{

    public function getIndex(Request $request)
    {
        $users = User::whereHas('sp_token')->when(Auth::user()->isUser(), function ($query) {
            $query->where('id', Auth::id());
        })->get();
        return view('admin.users.amazon.connections', compact('users'));
    }

    public function getStatusChange(User $user)
    {

        $user->is_active = !$user->is_active;
        $user->save();
        return redirect('/user/amazon/connect');
    }

    /**
     * @throws Exception
     */
    public function getAuth(Request $request)
    {
        $this->validate($request, ['region' => 'required']);

        return (new AuthApiClient(Auth::user(), 'ACCESS_TOKEN'))
            ->authorizeConsent(
                Auth::user()->id,
                $request->get('region')
            );
    }

    public function getRegister(Request $request)
    {
        [$uid, $region] = explode('|', $request->get('state'));
        Log::info('getRegisterSp: ' . json_encode($request->all()));

        $user = Auth::user();
        if ($user->id != $uid) {
            session()->flash('alert-danger', 'Something went wrong');
            return redirect('/user/amazon/connect');
        }

        $seller_id = $request->get('selling_partner_id');

        $connection = false;
        // try {
            $response = (new AuthApiClient(Auth::user(), 'ACCESS_TOKEN'))
                ->exchangeLwaCode(
                    $uid,
                    $request->get('spapi_oauth_code')
                );
            $client = new SellersApiClient(Auth::user(), $response->token(), $region);
           dd($client->listParticipations());
            foreach ($client->listParticipations() as $participation) {
                /** @var Marketplace $marketplace */
                $marketplace = Marketplace::getById($participation->getMarketplace()->getId());
                if (!$marketplace || $marketplace->region_code !== $region) {
                    continue;
                }
                $account = (Auth::user())->registerSeller($marketplace, $seller_id);

                SpToken::query()->updateOrCreate([
                    'user_id' => $account->id
                ], [
                    'access_token'    => $response->token(),
                    'refresh_token'   => $response->refreshToken(),
                    'token_type'      => $response->type(),
                    'expires_at'      => Carbon::now()->addSeconds(3000),
                    'last_updated_at' => Carbon::now()
                ]);

                $connection = true;
            }

            if ($connection) {
                session()->flash('alert-success', 'Successfully connected sellers');
            } else {
                session()->flash('alert-danger', 'Failed connecting sellers');
            }
        // } catch (ClientExceptionInterface | Exception $ex) {

        //     BugReport::logException($ex, Auth::user());
        //     session()->flash('alert-danger', 'Failed connecting sellers. ' . $ex->getMessage());
        // }

        return redirect('/user/amazon/connect');
    }
}
