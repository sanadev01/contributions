<?php

namespace App\AmazonSPClients;

use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Regions;
use App\Models\SpTokenResponse;
use App\Models\Marketplace;
use Exception;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class AuthApiClient extends Client
{

	/**
	 * @throws ClientExceptionInterface
	 * @throws ApiException
	 * @throws JsonException
	 */
	public function exchangeLwaCode(string $uid, string $lwaCode)
	{
		$sdk = $this->getSellingPartnerSDK();
		try {
			$access_token = $sdk->oAuth()->exchangeLwaCode(lwaCode: $lwaCode);
		} catch (ApiException $ex) {
			SpTokenResponse::query()->create([
				'user_id'  => $uid,
				'header'   => $ex->getResponseHeaders(),
				'response' => $ex->getResponseBody()
			]);

			throw $ex;
		}

		return $access_token;
	}

	/**
	 * @throws Exception
	 */
	public function authorizeConsent(string $uid, string $region)
	{
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
	private function _getEndpoint($region): string
	{
		return match ($region) {
			Marketplace::REGION_NA, Regions::NORTH_AMERICA => 'https://sellercentral.amazon.com',
			Marketplace::REGION_EU, Regions::EUROPE => 'https://sellercentral-europe.amazon.com',
			Marketplace::REGION_FE, Regions::FAR_EAST => 'https://sellercentral.amazon.co.jp',
			default => throw new Exception('Unknown region [' . $region . '] defined in the call'),
		};
	}
}
