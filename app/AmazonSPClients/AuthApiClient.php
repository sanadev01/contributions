<?php

namespace App\AmazonSPClients;
 
use AmazonSellingPartner\Regions; 
use App\Models\Marketplace;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthApiClient extends Client {
  
	public function authorizeConsent(string $uid, string $region) {
		if (app()->environment('production') ){
            $applicationId = config('services.sp-api-prod.SP_APP_ID');
            $redirectUri = config('services.sp-api-prod.SP_APP_REDIRECT');
        }else{
            $applicationId  = config('services.sp-api-dev.SP_APP_ID');
            $redirectUri  = config('services.sp-api-dev.SP_APP_REDIRECT');
        }

		Log::info([
			'version'        => 'beta',
			'application_id' => $applicationId,
			'redirect_uri'   => $redirectUri,
			'state'          => $uid . '|' . $region,
		]);
		$query = http_build_query([
			'version'        => 'beta',
			'application_id' => $applicationId,
			'redirect_uri'   => $redirectUri,
			'state'          => $uid . '|' . $region,
		]);
		return redirect($this->_getEndpoint($region) . '/apps/authorize/consent?' . $query);
	}

	/**
	 * @throws Exception
	 */
	private function _getEndpoint($region): string { 
		switch ($region) {
			case Marketplace::REGION_NA:
			case Regions::NORTH_AMERICA:
				return 'https://sellercentral.amazon.com';
			case Marketplace::REGION_EU:
			case Regions::EUROPE:
				return 'https://sellercentral-europe.amazon.com';
			case Marketplace::REGION_FE:
			case Regions::FAR_EAST:
				return 'https://sellercentral.amazon.co.jp';
			default:
				throw new Exception('Unknown region [' . $region . '] defined in the call');
		}
	}
}
