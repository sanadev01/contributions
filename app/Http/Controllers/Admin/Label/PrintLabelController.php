<?php

namespace App\Http\Controllers\Admin\Label;

use File;
use ZipArchive;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Repositories\LabelRepository;
use Illuminate\Support\Facades\Storage;

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
        $this->authorize('labelPrint',$order);
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

            $zip = new ZipArchive();
            $tempFileUri = storage_path('app/labels/label.zip');
            
            if(file_exists($tempFileUri)){
                unlink($tempFileUri);
            }

            if ($zip->open($tempFileUri, ZipArchive::CREATE) === TRUE) {

                foreach($request->order as $orderId){
                    $order = Order::find($orderId);
                    $relativeNameInZipFile = storage_path("app/labels/{$order->corrios_tracking_code}.pdf");
                    if(!file_exists($relativeNameInZipFile)){
                        $labelData = $labelRepository->get($order);
                    
                        if ( $labelData ){
                            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
                        }
                        $relativeNameInZipFile = storage_path("app/labels/{$order->corrios_tracking_code}.pdf");
                    }
                    
                    if (! $zip->addFile($relativeNameInZipFile, basename($relativeNameInZipFile))) {
                        echo 'Could not add file to ZIP: ' . $relativeNameInZipFile;
                    }
                    
                }

                $zip->close();
            } else {
                echo 'Could not open ZIP file.';
            }

            return response()->download($tempFileUri);
        }
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Order $scan, CorrieosBrazilLabelRepository $labelRepository)
    {
        $order = $scan;
    
        $labelData = null;

        if ( $request->update_label === 'true' ){
            $labelData = $labelRepository->update($order);
        }else{
            $labelData = $labelRepository->get($order);
        }

        $order->refresh();

        if ( $labelData ){
            Storage::put("labels/{$order->corrios_tracking_code}.pdf", $labelData);
        }

        return redirect()->route('order.label.download',[$order,'time'=>md5(microtime())]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
