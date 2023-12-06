<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class JobMonitor
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property string job_class
 * @property string server_id
 * @property integer process_id
 * @property string response
 * @property array trace
 * @property string cache_key
 * @property Carbon started_at
 * @property Carbon completed_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 */
class JobMonitor extends Model {

    protected $fillable = [
        'user_id',
        'job_class',
        'server_id',
        'process_id',
        'response',
        'trace',
        'cache_key',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'trace'        => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime'
    ];

    public function setCompleted($response = '', $trace = []) {
        $this->response = !empty($response) ? $response : (empty($trace) ? 'Job Processed Successfully' : 'Failed with error');
        $this->trace = $trace;
        $this->completed_at = Carbon::now();
        $this->save();
    }

    public function logException(\Exception $ex, $trace = []) {
        $trace = !empty($trace) ? $trace : (Str::contains($ex->getMessage(), 'throttled') ? [] : $ex->getTrace());
        $this->setCompleted($ex->getMessage(), $trace);
    }
}
