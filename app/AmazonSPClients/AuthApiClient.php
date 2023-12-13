<?php

namespace App\AmazonSPClients;
 
use AmazonSellingPartner\Regions; 
use App\Models\Marketplace;
use Exception;  
class AuthApiClient extends Client {
  
	public function authorizeConsent(string $uid, string $region) {
		$query = http_build_query([
			'version'        => 'beta',
			'application_id' => config('services.sp-api.SP_APP_ID'),
			'redirect_uri'   => config('services.sp-api.SP_APP_REDIRECT'),
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
