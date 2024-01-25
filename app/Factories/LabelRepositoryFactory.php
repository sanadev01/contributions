<?php

namespace App\Factories;

use App\Models\Order;
use App\Repositories\AnjunLabelRepository;
use App\Repositories\UPSLabelRepository;
use App\Repositories\GePSLabelRepository;
use App\Repositories\USPSLabelRepository;
use App\Repositories\FedExLabelRepository;
use App\Repositories\PostPlusLabelRepository;
use App\Repositories\SwedenPostLabelRepository;
use App\Repositories\CorrieosChileLabelRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\GSSLabelRepository;
use App\Repositories\HDExpressLabelRepository;
use App\Repositories\HoundExpressLabelRepository;
use App\Services\TotalExpress\TotalExpressLabelRepository;

class LabelRepositoryFactory
{
    public static function create(Order $order)
    {
        $order = $order->updateShippingServiceFromSetting();
        $shippingService = $order->shippingService;
        if ($shippingService->is_sweden_post_service) {
            return new SwedenPostLabelRepository();
        }

        if ($shippingService->is_hound_express) {
            return new HoundExpressLabelRepository();
        }

        if ($order->recipient->country_id == Order::BRAZIL) {
            if ($shippingService->is_geps_service) {
                return new GePSLabelRepository();
            }
            if ($shippingService->is_correios_service || $shippingService->is_bcn_service || $shippingService->is_anjun_china_service || $shippingService->is_anjun_service) {
                return new CorrieosBrazilLabelRepository();
            }
            if ($shippingService->is_post_plus_service) {
                return new PostPlusLabelRepository();
            }
            if ($shippingService->is_gss_service) {
                return new GSSLabelRepository();
            }
            if ($shippingService->is_total_express) {
                return new TotalExpressLabelRepository();
            }
        }

        if (in_array($order->recipient->country_id, [Order::PORTUGAL, Order::COLOMBIA]) && $shippingService->is_post_plus_service) {
            return new PostPlusLabelRepository();
        }

        if ($shippingService->is_hd_express_service) {
            return new HDExpressLabelRepository();
        }

        if ($order->recipient->country_id == Order::CHILE) {
            return new CorrieosChileLabelRepository();
        }

        if ($order->recipient->country_id == Order::US) {
            if ($shippingService->is_usps_priority || $shippingService->is_usps_firstclass || $shippingService->is_usps_ground || $shippingService->is_gde_priority || $shippingService->is_gde_first_class) {
                return new USPSLabelRepository();
            }
            if ($shippingService->is_fedex_ground) {
                return new FedExLabelRepository();
            }
            if ($shippingService->is_ups_ground) {
                return new UPSLabelRepository();
            }
        }

        if ($order->recipient->country_id != Order::US && ($shippingService->is_usps_priority_international || $shippingService->is_usps_firstclass_international)) {
            return new USPSLabelRepository();
        }

        if ($order->user->id == "1233" && $shippingService->is_anjun_china_service) {
            return new AnjunLabelRepository();
        }

        return new CorrieosBrazilLabelRepository();
    }
}
