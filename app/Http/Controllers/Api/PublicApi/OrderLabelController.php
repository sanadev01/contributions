<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\Order;
use App\Models\User;
use App\Events\OrderPaid;
use Illuminate\Http\Request;
use App\Repositories\AnjunLabelRepository;
use App\Services\GePS\Client;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\PostPlusLabelRepository;
use App\Repositories\GSSLabelRepository;
use App\Repositories\HDExpressLabelRepository;
use App\Services\TotalExpress\TotalExpressLabelRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\AutoChargeAmountEvent;

class OrderLabelController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        if (Auth::id() != $order->user_id) {
            return apiResponse(false, 'Order not found');
        }

        $this->authorize('canPrintLableViaApi', $order);
        if ($order->shippingService->isAnjunService() || $order->shippingService->is_bcn_service ||  $order->shippingService->isCorreiosService()) {
            $order = $this->updateShippingServiceFromSetting($order);
        }
        DB::beginTransaction();
        if ($order->shippingService->is_anjun_china_service_sub_class && Auth::id() != "1137") {
            return $this->rollback("service not available for this user.");
        }
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
            if ($order->shippingService->isSwedenPostService()) {
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
                if ($order->shippingService->isGePSService()) {
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
                    if ($error) {
                        return $this->rollback($error);
                    }
                }
                if ($order->shippingService->isGSSService()) {
                    $gssLabelRepository = new GSSLabelRepository();
                    $gssLabelRepository->get($order);
                    $error = $gssLabelRepository->getError();
                    if ($error) {
                        return $this->rollback($error);
                    }
                }
                if ($order->shippingService->is_total_express) {
                    $totalExpressLabelRepository = new TotalExpressLabelRepository();
                    $totalExpressLabelRepository->get($order);
                    $error = $totalExpressLabelRepository->getError();
                    if ($error) {
                        return $this->rollback((string)$error);
                    }
                }
                if ($order->shippingService->is_anjun_china_service_sub_class && Auth::id() == "1137") {
                    $anjun = new AnjunLabelRepository($order, $request);
                    $labelData = $anjun->run();
                    $order->refresh();
                    if ($labelData) {
                        Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                    }
                    if ($anjun->getError()) {
                        return $this->rollback($anjun->getError());
                    }
                } elseif ($order->shippingService->is_anjun_china_service_sub_class) {

                    return $this->rollback('service not availble for this user.');
                }
                if ($order->shippingService->isAnjunService() ||  $order->shippingService->isCorreiosService() || $order->shippingService->is_bcn_service) {
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
            }
            if ($order->recipient->country_id == Order::PORTUGAL) {
                if ($order->shippingService->isPostPlusService()) {
                    $postPlusLabelRepository = new PostPlusLabelRepository();
                    $postPlusLabelRepository->get($order);
                    $error = $postPlusLabelRepository->getError();
                    if ($error) {
                        return $this->rollback($error);
                    }
                }
            }

            if ($order->recipient->country_id == Order::PORTUGAL) {
                if ($order->shippingService->isPostPlusService()) {
                    $postPlusLabelRepository = new PostPlusLabelRepository();
                    $postPlusLabelRepository->get($order);
                    $error = $postPlusLabelRepository->getError();
                    if ($error) {
                        return $this->rollback($error);
                    }
                }
            }


            if ($order->shippingService->isHDExpressService()) {
                $hdExpressLabelRepository = new HDExpressLabelRepository();
                $hdExpressLabelRepository->run($order, false);
                $error = $hdExpressLabelRepository->getError();
                if ($error) {
                    return $this->rollback($error);
                }
            }
            return $this->commit($order);
        } catch (Exception $e) {
            return $this->rollback($e->getMessage());
        }
    }

    function swedenPostLabel($order)
    {
        if ($order->shippingService->isSwedenPostService()) {
            $swedenPostLabelRepository = new SwedenPostLabelRepository();
            $swedenPostLabelRepository->get($order);
            $error = $swedenPostLabelRepository->getError();
            if ($error) {
                return $this->rollback($error);
            }
            return $this->commit($order);
        }
    }
    private function commit($order)
    {
        DB::commit();
        return apiResponse(true, "Lable Generated successfully.", [
            'url' => $order->cn23_label_url ?? route('order.label.download',  encrypt($order->id)),
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
    public function updateShippingServiceFromSetting($order)
    {
        $service_sub_class = $order->shippingService->service_sub_class;
        if ($order->corrios_tracking_code) {
            return $order;
        }
        $standard = in_array($service_sub_class, [ShippingService::Packet_Standard, ShippingService::AJ_Packet_Standard, ShippingService::AJ_Standard_CN, ShippingService::BCN_Packet_Standard]);
        if (setting('china_anjun_api', null, User::ROLE_ADMIN)) {
            if ($standard) {
                $service_sub_class = ShippingService::AJ_Standard_CN;
            } else {
                $service_sub_class = ShippingService::AJ_Express_CN;
            }
        } else if (setting('correios_api', null, User::ROLE_ADMIN)) {
            if ($standard) {
                $service_sub_class = ShippingService::Packet_Standard;
            } else {
                $service_sub_class = ShippingService::Packet_Express;
            }
        } else if (setting('bcn_api', null, User::ROLE_ADMIN)) {
            if ($standard) {
                $service_sub_class = ShippingService::BCN_Packet_Standard;
            } else {
                $service_sub_class = ShippingService::BCN_Packet_Express;
            }
        } else if (setting('anjun_api', null, User::ROLE_ADMIN)) {
            if ($standard) {
                $service_sub_class = ShippingService::AJ_Packet_Standard;
            } else {
                $service_sub_class = ShippingService::AJ_Packet_Express;
            }
        }
        $order->update([
            'shipping_service_id' => (ShippingService::where('service_sub_class', $service_sub_class)->first())->id,
        ]);
        return $order->fresh();
    }
}