<?php

namespace App\Jobs\AmazonOrders;

use AmazonSellingPartner\Exception\ApiException;
use App\Models\AmazonOrders\SaleOrderHistory;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class GetLatestOrdersJob extends GetOrdersJob {

    /**
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function processJob() {
        if (!$this->user->hasSellingPartnerAccess()) {
            console_log('The job cannot be executed for this user');
            return;
        }

        $executed = 0;

        do {
            /** @var SaleOrderHistory $history_job */
            $history_job = SaleOrderHistory::getExecutable($this->user->id);

            if (!$history_job) {
                console_log('There are no pending jobs to process');
                break;
            }

            $executed++;
            $this->processOrderHistory($history_job);

            $this->setJobActive();

            nap(4);

        } while ($executed < 100);
    }

}
