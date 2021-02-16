<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'user_id',
        'name',
        'price',
        'sku',
        'status',
        'merchant',
        'carrier',
        'tracking_id',
        'order_date',
        'sh_code',
        'description',
        'quantity',
        'weight',
        'length',
        'width',
        'height',
        'warehouse_number',
        'invoice_file',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function getStatusClass()
    {
        $class = "";
        if ( $this->status == 'pending' ){
            $class = 'btn btn-sm btn-danger';
        }

        if ( $this->status == 'approved' ){
            $class = 'btn btn-sm btn-success';
        }
        return $class;
    }

    public function fileInvoice()
    {
        return $this->belongsTo(Document::class,'invoice_file');
    }

   
    public function attachInvoice(UploadedFile $file)
    {
        optional($this->fileInvoice)->delete();
        $invoiceFile = Document::saveDocument(
            $file
        );
        
        $invoice = Document::create([
            'name' => $invoiceFile->getClientOriginalName(),
            'size' => $invoiceFile->getSize(),
            'type' => $invoiceFile->getMimeType(),
            'path' => $invoiceFile->filename
        ]);

        return $this->update([
            'invoice_file' => $invoice->id
        ]);
    }
}
