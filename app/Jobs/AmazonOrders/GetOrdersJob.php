<?php

namespace App\Jobs\AmazonOrders;

use AmazonSellingPartner\Exception\ApiException;
use App\Jobs\AmazonOrders\Attributes\HasOrderAttributes;
use App\Jobs\AmazonOrders\Attributes\HasOrderItemAttributes;
use App\Jobs\BaseJob;
use App\Jobs\Contracts\SPAPICallable;
use App\Models\AmazonOrders\SaleOrder;
use App\Models\AmazonOrders\SaleOrderHistory;
use App\AmazonSPClients\OrdersApiClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

abstract class GetOrdersJob extends BaseJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,
        SPAPICallable, HasOrderAttributes, HasOrderItemAttributes;

    /** @var SaleOrderHistory */
    protected $history_job;
    /** @var OrdersApiClient */
    protected $client;
    /** @var int */
    protected $start_time;
    /** @var SaleOrder */
    protected $sale_order;

    /**
     * @param SaleOrderHistory $history_job
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws Exception
     */
    public function processOrderHistory(SaleOrderHistory $history_job) {
        $this->history_job = $history_job;

        $this->history_job->status = SaleOrderHistory::STATUS_WORKING;
        $this->history_job->save();

        $this->start_time = time();

        $from_date = $this->history_job->last_update_till ?: $this->history_job->from_date;
        $to_date = $this->history_job->to_date;

        console_log('Getting orders data from ' . $from_date . ' to ' . $to_date . ' for ID: ' . $this->history_job->id);

        $orders = $this->getOrdersClient()->getOrders($from_date, $to_date);
        foreach ($orders as $order) {
            console_log('Fetched SO: ' . $order->getAmazonOrderId());
            $this->_saveOrder($order);
            $this->setJobActive();
        }

        $this->history_job->status = SaleOrderHistory::STATUS_DONE;
        $this->history_job->save();
    }

    /**
     * @param SaleOrder $sale_order
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws Exception
     */
    public function processItemsJob(SaleOrder $sale_order) {
        $this->sale_order = $sale_order;
        $items = $this->getOrdersClient()->getOrderItems($this->sale_order->amazon_order_id);

        foreach ($items as $item) {
            console_log('Fetched SOI: ', $item->getOrderItemId());
            $this->_saveOrderItem($item);
        }
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @throws Exception
     */
    public function processAmazonOrder($amazon_order_id) {
        $order = $this->getOrdersClient()->getOrder($amazon_order_id);
        console_log('Fetched SO: ' . $order->getAmazonOrderId());
        $this->_saveOrder($order);
        $this->setJobActive();
    }

    private function _savePayload($last_update_till = null) {
        if ($this->history_job) {
            !$last_update_till ?: $this->history_job->last_update_till = $last_update_till;
            $this->history_job->execution_time += time() - $this->start_time;
            $this->history_job->save();
        }

        $this->start_time = time();
    }
}
