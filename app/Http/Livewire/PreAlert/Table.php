<?php

namespace App\Http\Livewire\PreAlert;

use App\Models\Order;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\AutoChargeAmountEvent;
use App\Services\IDLabel\CN23LabelMaker;

class Table extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public $pageSize = 50;

    private $query;

    /**
     * Searchable Fields.
     */
    public $date = '';
    public $name = '';
    public $pobox = '';
    public $whr_number = '';
    public $merchant = '';
    public $carrier = '';
    public $tracking_id = '';
    public $status = '';

    /**
     * Sort Asc.
     */
    public $sortAsc = false;
    public $sortBy = 'id';

    public function mount()
    {
        $this->query = $this->getQuery();
    }

    public function render()
    {
        if (! $this->query) {
            $this->query = $this->getQuery();
        }

        return view('livewire.pre-alert.table', [
            'parcels' => $this->query
                ->orderBy(
                    $this->sortBy,
                    $this->sortAsc ? 'ASC' : 'DESC'
                )
                ->paginate($this->pageSize)
        ]);
    }

    public function sortBy($name)
    {
        if ($name == $this->sortBy) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortBy = $name;
        }
    }

    public function updatedDate()
    {
        $this->query = $this->getQuery()->where('created_at', 'LIKE', "%{$this->date}%");
    }

    public function updatedName()
    {
        $this->query = $this->getQuery()->whereHas('user', function ($query) {
            return $query->where('name', 'LIKE', "%{$this->name}%");
        });
    }

    public function updatedPobox()
    {
        $this->query = $this->getQuery()->whereHas('user', function ($query) {
            return $query->where('pobox_number', 'LIKE', "%{$this->pobox}%");
        });
    }

    public function updatedWhrNumber()
    {
        if (strlen($this->whr_number) <= 0) {
            return $this->query = $this->getQuery();
        }
        $whrNumber = trim($this->whr_number);
        $this->query = $this->getQuery()->where('warehouse_number', 'LIKE', "%{$whrNumber}%");
    }

    public function updatedMerchant()
    {
        $this->query = $this->getQuery()->where('merchant', 'LIKE', "%{$this->merchant}%");
    }

    public function updatedCarrier()
    {
        $this->query = $this->getQuery()->where('carrier', 'LIKE', "%{$this->carrier}%");
    }

    public function updatedTrackingId()
    {
        $this->query = $this->getQuery()->where('tracking_id', 'LIKE', "%{$this->tracking_id}%");
    }

    public function updatedStatus()
    {
        if ($this->status === 'transit') {
            $this->query = $this->getQuery()->where('is_shipment_added',false)->where('status','<>',Order::STATUS_CONSOLIDATOIN_REQUEST);
        }

        if ($this->status === 'ready') {
            $this->query = $this->getQuery()->where('is_shipment_added',true)->where('status','<>',Order::STATUS_CONSOLIDATOIN_REQUEST);
        }

        if ($this->status === '25') {
            $this->query = $this->getQuery()->where('status',$this->status);
        }
        if ($this->status === '26') {
            $this->query = $this->getQuery()->where('status',$this->status);
        }
    }

    public function getQuery()
    {
        $orders = Order::query()
            ->where('status','>',Order::STATUS_INVENTORY_FULFILLED)
            ->where('status','<',Order::STATUS_ORDER)
            ->has('user')
            ->doesntHave('parentOrder');
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }

        return $orders;
    }

    public function generateIDLabel($id)
    {
        try {
            $orderId = decrypt($id);
            $shippingService = ShippingService::where('service_sub_class', ShippingService::ID_Label_Service)->first();

            if (!$shippingService) {
                session()->flash('livewire-alert-danger', 'Shipping service not found.');
                return $this->redirectWithError("Shipping service not found.");
            }

            $order = Order::findOrFail($orderId);

            DB::beginTransaction();

            // Update order details
            $order->update([
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                "is_invoice_created" => true,
                "tax_modality" => 'ddu',
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'sinerlog_tran_id' => 1,

                "sender_first_name" => $order->user->name,
                "sender_last_name" => $order->user->last_name,
                "sender_email" => $order->user->email,
                "sender_taxId" => $order->user->tax_id,
                'sender_country_id' => Country::US,
                'sender_state_id' => "4622",
                'sender_city' => "Miami",
                'sender_address' => "2200 NW, 129th Ave – Suite # 100",
                'sender_phone' => $order->user->phone,
                'sender_zipcode' => "33182",
            ]);

            // Extract merchant details
            $merchant = $order->merchant;
            preg_match('/^(\w+)\s+(\w+)\s*(\d+)$/', $merchant, $matches);

            if (empty($matches)) {
                DB::rollBack();
                session()->flash('livewire-alert-danger', 'Invalid merchant format. Ensure it contains first name, last name, and ZIP code.');
                return $this->redirectWithError("Invalid merchant format. Ensure it contains first name, last name, and ZIP code.");
            }

            $order->recipient()->create([
                "first_name" => $matches[1] ?? null,
                "last_name" => $matches[2] ?? null,
                "account_type" => 'individual',
                "zipcode" => $matches[3] ?? null,
                "country_id" => Country::Guatemala
            ]);

            $order->syncServices([]);
            $order->doCalculations();

            $isPayingFlag = true;

            // Check payment status
            // if (!$order->isPaid()) {
            //     if (getBalance() < $order->gross_total) {
            //         DB::rollBack();
            //         session()->flash('livewire-alert-danger', 'Not Enough Balance. Please Recharge your account.');
            //         return $this->redirectWithError("Not Enough Balance. Please Recharge your account.");
            //     } else {
            //         $order->update([
            //             'is_paid' => true,
            //             'status' => Order::STATUS_PAYMENT_DONE
            //         ]);

            //         chargeAmount($order->gross_total, $order);
            //         AutoChargeAmountEvent::dispatch($order->user);
            //         $isPayingFlag = true;
            //     }
            // }
            if ($isPayingFlag) {
                $order->update([
                    'api_response' => null,
                    'corrios_tracking_code' => 'HD' . date('d') . date('m') . substr(date('s'), 1, 1) . $order->id . 'GT',
                ]);

                $order->refresh();
                $this->printCN23($order);

                // Store order status in order tracking
                $this->addOrderTracking($order);

                DB::commit();
                session()->flash('message', 'ID Label generated successfully!');

                // Redirect to orders index page
                return redirect()->route('admin.orders.label.index',encrypt($order->id));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Error generating ID Label: ' . $e->getMessage(), [
                'order_id' => $id,
                'exception' => $e,
            ]);

            // Return error message
            session()->flash('livewire-alert-danger', 'An error occurred while generating the ID Label: ' .  $e->getMessage());

            return $this->redirectWithError("An error occurred while generating the ID Label: " . $e->getMessage());
        }
    }

    /**
     * Redirect with error message for Livewire
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithError($message)
    {
        session()->flash('error', $message);
        return redirect()->back()->withInput();
    }


    public function updating()
    {
        $this->resetPage();
    }

    private function printCN23($order)
    {
        $labelPrinter = new CN23LabelMaker();
        $labelPrinter->setOrder($order);
        $labelPrinter->saveAs(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"));
    }

    private function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()) {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_PENDING,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
            ]);
        }

        return true;
    }
}
