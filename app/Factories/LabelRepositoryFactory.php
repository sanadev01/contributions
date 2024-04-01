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
use App\Repositories\ColombiaLabelRepository;
use App\Services\TotalExpress\TotalExpressLabelRepository;

class LabelRepositoryFactory
{
    public static function create(Order $order)
    {
        $order = $order->updateShippingServiceFromSetting();
        $shippingService = $order->shippingService;
    
        return match (true) {
            $shippingService->is_sweden_post_service => new SwedenPostLabelRepository(),
            $shippingService->is_hound_express => new HoundExpressLabelRepository(),
            $order->recipient->country_id == Order::BRAZIL && $shippingService->is_geps_service => new GePSLabelRepository(),
            $order->recipient->country_id == Order::Japan && $shippingService->is_geps_service => new GePSLabelRepository(),
            $order->recipient->country_id == Order::BRAZIL && ($shippingService->is_correios_service || $shippingService->is_bcn_service || $shippingService->is_anjun_china_service || $shippingService->is_anjun_service) => new CorrieosBrazilLabelRepository(),
            $order->recipient->country_id == Order::BRAZIL && $shippingService->is_post_plus_service => new PostPlusLabelRepository(),
            $order->recipient->country_id == Order::BRAZIL && $shippingService->is_gss_service => new GSSLabelRepository(),
            $order->recipient->country_id == Order::BRAZIL && $shippingService->is_total_express => new TotalExpressLabelRepository(),
            $order->recipient->country_id == Order::COLOMBIA && $shippingService->isColombiaService() => new ColombiaLabelRepository(),
            in_array($order->recipient->country_id, [Order::PORTUGAL, Order::COLOMBIA]) && $shippingService->is_post_plus_service => new PostPlusLabelRepository(),
            $shippingService->is_hd_express_service => new HDExpressLabelRepository(),
            $order->recipient->country_id == Order::CHILE => new CorrieosChileLabelRepository(),
            $order->recipient->country_id == Order::US && ($shippingService->is_usps_priority || $shippingService->is_usps_firstclass || $shippingService->is_usps_ground || $shippingService->is_gde_priority || $shippingService->is_gde_first_class) => new USPSLabelRepository(),
            $order->recipient->country_id == Order::US && $shippingService->is_fedex_ground => new FedExLabelRepository(),
            $order->recipient->country_id == Order::US && $shippingService->is_ups_ground => new UPSLabelRepository(),
            $order->recipient->country_id != Order::US && ($shippingService->is_usps_priority_international || $shippingService->is_usps_firstclass_international) => new USPSLabelRepository(),
            $order->user->id == "1233" && $shippingService->is_anjun_china_service => new AnjunLabelRepository(),
            default => new CorrieosBrazilLabelRepository(),
        };
    }
    
}
