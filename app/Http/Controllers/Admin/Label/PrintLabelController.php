<?php

namespace App\Http\Controllers\Admin\Label;

use File;
use ZipArchive;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Excel\Export\ScanOrderExport;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\HandleCorreiosLabelsRepository;

class PrintLabelController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Order $order)
    {
        $this->authorize('printBulkLabel',$order);
        return view('admin.print-label.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CorrieosBrazilLabelRepository $labelRepository)
    {
        if($request->order){
            if($request->excel){
                if($request->start_date != null && $request->end_date != null)
                {
                    $start_date = $request->start_date.' 00:00:00';
                    $end_date = $request->end_date.' 23:59:59';

                    $orders = Order::whereIn('id', $request->order)
                                    ->whereBetween('order_date', [$start_date, $end_date])->get();                            
                }else{

                    $orders = Order::whereIn('id', $request->order)->get();
                }
                $exportService = new ScanOrderExport($orders);
                return $exportService->handle();
            }
            $zip = new ZipArchive();
            $tempFileUri = storage_path('app/labels/label.zip');
            
            if(file_exists($tempFileUri)){
                unlink($tempFileUri);
            }
            if ($zip->open($tempFileUri, ZipArchive::CREATE) === TRUE) {

                foreach($request->order as $orderId){
                    $order = Order::find($orderId);
                    if($order->is_paid){
                        $relativeNameInZipFile = storage_path("app/labels/{$order->corrios_tracking_code}.pdf");

                        if($order->shippingService->is_total_express) {
                            $pdfFile = file_get_contents($order->totalExpressLabelUrl());
                            file_put_contents($relativeNameInZipFile, $pdfFile);
                        }

                        if(!file_exists($relativeNameInZipFile)){
                            // $labelData = $labelRepository->get($order);
                            (new HandleCorreiosLabelsRepository($request,$order))->handle();
                            // if ( $labelData ){
                            //     Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                            // }
                            $relativeNameInZipFile = storage_path("app/labels/{$order->corrios_tracking_code}.pdf");
                        }
                        
                        if (! $zip->addFile($relativeNameInZipFile, basename($relativeNameInZipFile))) {
                            echo 'Could not add file to ZIP: ' . $relativeNameInZipFile;
                        }
                    }
                    
                }

                $zip->close();
                return response()->download($tempFileUri);
            } else {
                return 'Could not open ZIP file.';
            }
            
            
        }
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Order $scan)
    {
        $order = $scan;
        if($request->search){
            return view('admin.modals.orders.tracking',compact('order'));
        }
        $labelData = null;
        if($order->is_paid){
            if(!$order->corrios_tracking_code){
                (new HandleCorreiosLabelsRepository($request,$order))->handle();
            }
            return redirect()->route('order.label.download',[encrypt($order->id),'time'=>md5(microtime())]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $query = Order::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }
        if($request->userId != null)
        {
            $query->where('user_id', $request->userId);
        }
        $query = $query->whereBetween('arrived_date',[$request->start_date.' 00:00:00', $request->end_date.' 23:59:59'])->orderBy('arrived_date', 'DESC')->get();
        $orders = $query;
        
        if($orders != null)
        {
            $exportService = new ScanOrderExport($orders);
            return $exportService->handle();
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
}
