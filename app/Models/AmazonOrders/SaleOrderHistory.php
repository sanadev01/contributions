<?php

namespace App\Models\AmazonOrders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Class SaleOrderHistory
 * @package App\Models\AmazonOrders
 * @property integer id
 * @property integer user_id
 * @property string status
 * @property Carbon from_date
 * @property Carbon to_date
 * @property Carbon last_update_till
 * @property integer execution_time
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 */
class SaleOrderHistory extends Model {

    const STATUS_PENDING = '_PENDING_';
    const STATUS_WORKING = '_WORKING_';
    const STATUS_DONE = '_DONE_';

    protected $fillable = [
        'user_id',
        'status',
        'from_date',
        'to_date',
        'last_update_till',
        'execution_time'
    ];

    protected $casts = [
        'from_date'  => 'datetime',
        'to_date'    => 'datetime',
        'last_update_till'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @param User $user
     * @return Builder|Model|object|null
     */
    public static function getLastForUser(User $user) {
        return self::query()
            ->where('user_id', $user->id)
            ->orderBy('to_date', 'DESC')
            ->first();
    }

    /**
     * @param User $user
     * @return Builder|Model|object|null
     */
    public static function getFirstForUser(User $user) {
        return self::query()
            ->where('user_id', $user->id)
            ->orderBy('from_date', 'ASC')
            ->first();
    }

    public static function getExecutable($user_id, $latest = true) {
        $query = self::query()
            ->where('user_id', $user_id)
            ->where(function ($query) {
                $query->where('status', self::STATUS_PENDING)
                    ->orWhereRaw(DB::raw('(status = \'' . self::STATUS_WORKING . '\' AND updated_at < \'' . Carbon::now()->subMinutes(20) . '\')'));
            })
            ->where('to_date', '<', Carbon::now()->subMinutes(5));

        if ($latest) {
            $query
                ->where('from_date', '>=', Carbon::parse('30 days ago')->startOfDay())
                ->orderBy('from_date', 'ASC');
        } else {
            $query
                ->where('from_date', '<', Carbon::parse('30 days ago')->startOfDay())
                ->orderBy('from_date', 'DESC');
        }

        return $query->first();
    }

}
