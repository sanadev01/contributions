<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    const PATH = '/public/documents/';
    const PUBLIC_PATH = '/storage/documents/';

    protected $guarded = [];

    protected $appends = [
        'fullPath'
    ];

    public function getStoragePath()
    {
        return self::PATH.$this->path;
    }
    function getPublicPathAttribute() {
        return self::PUBLIC_PATH.$this->path;
        
    } 
    function getAbsolutePublicPathAttribute() {
        return env('APP_URL').self::PUBLIC_PATH.$this->path;
        
    }

    public function getFullPathAttribute()
    {
        return $this->getPath();
    }

    public function getPath()
    {
        return route('media.get', $this->path);
    }

    public function getRouteKeyName()
    {
        return 'path';
    }

    /**
     * Delete Document From storage as well.
     */
    protected static function boot()
    {
        parent::boot();

        self::deleting(function ($document) {
            if (Storage::exists($document->getStoragePath())) {
                Storage::delete($document->getStoragePath());
            }
        });
    }

    public static function saveDocument(UploadedFile $file,$subFolder='') : UploadedFile
    {
        $filename = $subFolder.md5(microtime()).'.'.$file->getClientOriginalExtension();
        $file->storeAs(Document::PATH, $filename);
        $file->filename = $filename;
        return $file;
    }
}
