<?php

namespace App\Jobs\AmazonOrders;

use App\Jobs\BaseJob;
use App\Models\AmazonOrders\SaleOrderHistory;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateOrdersHistoryJob extends BaseJob implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $historic_days = 548; // 18 months
    /** @var Carbon */
    protected $start_from;
    /** @var Carbon */
    protected $end_at;
    /** @var bool */
    protected $force_end = false;

    public function setStartFromDate($start_from): self {
        $this->start_from = $start_from;
        return $this;
    }

    public function setEndAtDate($end_at): self {
        $this->end_at = $end_at;
        $this->force_end = true;
        return $this;
    }

    public function processJob() {
        if (!$this->user->hasSellingPartnerAccess()) {
            console_log('The job cannot be executed for this user');
            return;
        }

        $this->start_from = $this->getStartFromDate();
        $this->end_at = $this->getEndAtDate();
        $interval = $this->getTimeInterval();

        while ($this->start_from->lt($this->end_at)) {
            $from = $this->start_from->copy();
            $to = min($this->end_at, $this->start_from->addMinutes($interval))->copy();

            if ($from->diffInMinutes($to) < 30 && !$this->force_end) {
                break;
            }

            SaleOrderHistory::query()->create([
                "user_id"   => $this->user->id,
                "status"    => SaleOrderHistory::STATUS_PENDING,
                "from_date" => $from,
                "to_date"   => $to
            ]);

            console_log("Order History from {$from} to {$to}");
        }
    }

    protected function getStartFromDate(): Carbon {
        if ($this->start_from) {
            return $this->start_from;
        }

        /** @var SaleOrderHistory $last */
        $last = SaleOrderHistory::getLastForUser($this->user);

        if ($last) {
            return $last->to_date;
        }

        if (is_local()) {
            return Carbon::now()->subDays(15)->startOfDay();
        }

        return Carbon::now()->subDays($this->historic_days)->startOfDay();
    }

    protected function getEndAtDate(): Carbon {
        if ($this->end_at) {
            return $this->end_at;
        }

        return Carbon::now();
    }

    protected function getTimeInterval()
    {
        $difference = $this->start_from->diffInMinutes($this->end_at);

        switch (true) {
            case $difference > (15 * 1440):
                return 7 * 1440; // 7 days interval for more than 15 days
            case $difference > (7 * 1440):
                return 3 * 1440; // 3 days interval for more than 7 days
            case $difference > (2 * 1440):
                return 1440; // 1-day interval for more than 2 days
            default:
                return 30; // 30 minutes interval in routine
        }
    }
}
