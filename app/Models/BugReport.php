<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class BugReport
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property string server_id
 * @property string env
 * @property string code
 * @property string message
 * @property string line
 * @property string file
 * @property array trace
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BugReport extends Model {

    const IGNORABLE = [];

    protected $fillable = [
        'user_id',
        'server_id',
        'env',
        'code',
        'message',
        'line',
        'file',
        'trace'
    ];

    protected $casts = [
        'trace'      => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static function logException(\Throwable $e, $user = null) {
        try {
            if (!Str::startsWith($e->getMessage(), self::IGNORABLE)) {
                self::query()->create([
                    'user_id'   => ($user instanceof User ? $user->id : $user),
                    'server_id' => config('app.instance'),
                    'env'       => config('app.env'),
                    'code'      => $e->getCode(),
                    'message'   => $e->getMessage(),
                    'line'      => $e->getLine(),
                    'file'      => $e->getFile(),
                    'trace'     => $e->getTrace(),
                ]);
            } else {
                console_log($e->getMessage());
            }
        } catch (Exception $e) {
            console_log($e->getMessage());
        }
    }
}
