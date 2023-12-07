<?php

namespace App\Jobs\Contracts;

use App\AmazonSPClients\OrdersApiClient;
use Exception;

trait SPAPICallable {

    public function setClient($client) {
        $this->client = $client;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function getOrdersClient(): OrdersApiClient {
        if (!$this->client) {
            $this->client = new OrdersApiClient($this->getUser());
            $this->client->setParentJob($this->parent_job ?: $this);
        }
        return $this->client;
    }

}
