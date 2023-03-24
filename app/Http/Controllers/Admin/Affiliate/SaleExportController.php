<?php

namespace App\Http\Controllers\Admin\Affiliate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Export\SaleExport;
use App\Repositories\AffiliateSaleRepository;

class SaleExportController extends Controller
{
    public function __invoke(Request $request, AffiliateSaleRepository $affiliateSaleRepository)
    {
        $sales = $affiliateSaleRepository->get($request, false);
        if(Auth::user()->isAdmin() && $request->status == 'toPay'){
            session()->flash('alert-success','Commission has been paid');
            return redirect()->back();
        }
        $exportService = new SaleExport($sales);
        return $exportService->handle();
    }
}
