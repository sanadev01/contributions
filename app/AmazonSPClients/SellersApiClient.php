<?php

namespace App\AmazonSPClients;

use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Exception\InvalidArgumentException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class SellersApiClient extends Client {

    /**
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     */
    public function listParticipations() {
        $sdk = $this->getSellingPartnerSDK();

        $response = $sdk->sellers()->getMarketplaceParticipations(
            $this->getAccessToken($sdk),
            $this->getRegion()
        );

        return $response->getPayload();
    }
}
