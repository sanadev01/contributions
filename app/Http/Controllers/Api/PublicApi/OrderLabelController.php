<?php

namespace App\Http\Controllers\Api\PublicApi;

use Exception;
use App\Models\Order;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
// use App\Repositories\LabelRepository;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\AutoChargeAmountEvent;
use Illuminate\Support\Facades\Storage;
use App\Repositories\GSSLabelRepository;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\AnjunLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\POSTNLLabelRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\ColombiaLabelRepository;
use App\Repositories\PostPlusLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use App\Repositories\MileExpressLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;


class OrderLabelController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        if(Auth::id() != $order->user_id){
            return apiResponse(false,'Order not found');
        }
        $this->authorize('canPrintLableViaApi', $order);
        DB::beginTransaction();
        $isPayingFlag = false;
        try {
            $orders = new Collection;
            if (!$order->isPaid()) {
                if (getBalance() < $order->gross_total) {
                    return $this->rollback("Not Enough Balance. Please Recharge your account.");
                } else {
                    $order->update([
                        'is_paid' => true,
                        'status' => Order::STATUS_PAYMENT_DONE
                    ]);
                    chargeAmount($order->gross_total, $order);
                    AutoChargeAmountEvent::dispatch($order->user);
                    $isPayingFlag = true;
                }
            }
            $labelData = null;

            //sweden post
            if (isSwedenPostCountry($order) && $order->shippingService->isSwedenPostService()) {
                return $this->swedenPostLabel($order);
            }

            //For USPS International services
            if ($order->shippingService->is_usps_priority_international || $order->shippingService->is_usps_firstclass_international) {
                $uspsLabelRepository = new USPSLabelRepository();
                $uspsLabelRepository->handle($order);

                $error = $uspsLabelRepository->getUSPSErrors();
                if (!$error) {
                    return $this->commit($order);
                }
                return $this->rollback($error);
            }

            // For Correos Chile
            if (($order->shippingService->service_sub_class == ShippingService::SRP || $order->shippingService->service_sub_class == ShippingService::SRM) && $order->recipient->country_id == Order::CHILE) {
                $corrieosChileLabelRepository = new CorrieosChileLabelRepository();
                $corrieosChileLabelRepository->handle($order);
                $error = $corrieosChileLabelRepository->getChileErrors();
                if (!$error) {
                    return $this->commit($order);
                }
                return $this->rollback($error);
            }

            if ($order->recipient->country_id == Order::US) {
                // For USPS
                if (in_array($order->shippingService->service_sub_class, [ShippingService::USPS_PRIORITY, ShippingService::USPS_FIRSTCLASS, ShippingService::USPS_GROUND, ShippingService::GDE_PRIORITY_MAIL, ShippingService::GDE_FIRST_CLASS])) {
                    $uspsLabelRepository = new USPSLabelRepository();
                    $uspsLabelRepository->handle($order);

                    $error = $uspsLabelRepository->getUSPSErrors();
                }
                // For UPS
                if ($order->shippingService->service_sub_class == ShippingService::UPS_GROUND) {
                    $upsLabelRepository = new UPSLabelRepository();
                    $upsLabelRepository->handle($order);
                    $error = $upsLabelRepository->getUPSErrors();
                }
                // For FedEx
                if ($order->shippingService->service_sub_class == ShippingService::FEDEX_GROUND) {
                    $fedexLabelRepository = new FedExLabelRepository();
                    $fedexLabelRepository->handle($order);
                    $error = $fedexLabelRepository->getFedExErrors();
                }
                if (!$error) {
                    return $this->commit($order);
                }
                return $this->rollback($error);
            }
            // For Correios,  Global eParcel Brazil and Sweden Post(Prime5)
            if ($order->recipient->country_id == Order::BRAZIL) {
                if ($isPayingFlag) {
                    $orders->push($order);
                    event(new OrderPaid($orders, true));
                }
                if ($order->shippingService->isGePSService()){
                    $gepsLabelRepository = new GePSLabelRepository();
                    $gepsLabelRepository->get($order);
                    $error = $gepsLabelRepository->getError();
                    if ($error) {
                        return $this->rollback($error);
                    }
                }

                if ($order->shippingService->isPostPlusService()) {
                    $postPlusLabelRepository = new PostPlusLabelRepository();
                    $postPlusLabelRepository->get($order);
                    $error = $postPlusLabelRepository->getError();
                    if ($error){
                        return $this->rollback($error);
                    }
                }
                
                if ($order->shippingService->isGSSService()) {
                    $gssLabelRepository = new GSSLabelRepository();
                    $gssLabelRepository->get($order);
                    $error = $gssLabelRepository->getError();
                    if ($error){
                        return $this->rollback($error);
                    }
                }
                if ($order->shippingService->is_milli_express) { 
                    $mileExpressLabelRepository = new MileExpressLabelRepository();
                    $mileExpressLabelRepository->run($order, true);
                    $error = $mileExpressLabelRepository->getError();
                    if ($error){
                        return $this->rollback($error);
                    }
                }
                if ($order->shippingService->isAnjunService() ||  $order->shippingService->isCorreiosService()){
                    $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                    $labelData = $corrieosBrazilLabelRepository->run($order, $request->update_label === 'true' ? true : false);
                    $order->refresh();
                    if ($labelData) {
                        Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                    }
                    if ($corrieosBrazilLabelRepository->getError()) {
                        return $this->rollback($corrieosBrazilLabelRepository->getError());
                    }
                }
                
                if ($order->shippingService->is_anjun_china){
                    $anjunLabelRepository = new AnjunLabelRepository();
                    $anjunLabelRepository->run($order, $request->update_label === 'true' ? true : false); 

                    $order->refresh();
                    if ($labelData) {
                        Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                    }
                    if ($anjunLabelRepository->getError()) {
                        return $this->rollback($anjunLabelRepository->getError());
                    }
                }
            }
            return $this->commit($order);
        } catch (Exception $e) {
            return $this->rollback($e->getMessage());
        }
    }

    function swedenPostLabel($order){
        if ($order->shippingService->isSwedenPostService()) {
            $swedenPostLabelRepository = new SwedenPostLabelRepository();
            $swedenPostLabelRepository->get($order);
            $error = $swedenPostLabelRepository->getError();
            if ($error){
                return $this->rollback($error);
            }
            else
                return $this->commit($order);

        }
    }
    private function commit($order)
    {
        DB::commit();
        return apiResponse(true, "Lable Generated successfully.", [
            'url' => route('order.label.download',  encrypt($order->id)),
            'tracking_code' => $order->corrios_tracking_code
        ]);
    }

    private function rollback($error)
    {
        DB::rollback();
        return apiResponse(false, $error);
    }

    public function cancelGePSLabel(Order $order)
    {
        $gepsClient = new Client();
        $response = $gepsClient->cancelShipment($order->corrios_tracking_code);
        if (!$response['success']) {
            return apiResponse(false, $response['message']);
        }
        if ($response['success']) {
            $order->update([
                'corrios_tracking_code' => null,
                'cn23' => null,
                'api_response' => null
            ]);
            return apiResponse(true, "Label Cancellation is Successful.", [
                'cancelled_tracking_code' => $response['data']->cancelshipmentresponse->tracknbr
            ]);
        }
    }
}
