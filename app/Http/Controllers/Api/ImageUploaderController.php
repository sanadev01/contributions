<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class ImageUploaderController extends Controller
{
    public function store(Request $request)
    {
        $v = \Validator::make($request->all(),[
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|file|max:4096'
        ]);

        if ( $v->fails() ){
            return response()->json([
                'success' => false,
                'message' => "Validation Errors",
                'data' => [
                    'errors' => $v->errors()->toArray()
                ]
            ],422); 
        }

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $document = Document::saveDocument($image);
                $images[] = Document::create([
                    'name' => $document->getClientOriginalName(),
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'path' => $document->filename
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Images Uploaded",
            'data' => $images
        ]); 

    }
}
