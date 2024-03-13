<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class TicketComment extends Model
{
    protected $guarded = [];

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }

    public function scopebyAdmin(Builder $query)
    {
        $query->whereIn('user_id', User::select('id')->admin());
    }

    public function scopebyUser(Builder $query)
    {
        $query->whereNotIn('user_id', User::select('id')->admin());
    }

    public static function getLastCommentByAdmin()
    {
        return TicketComment::byAdmin()
            ->latest()
            ->limit(1)
            ->first();
    }

    public static function getLastCommentByUser()
    {
        return TicketComment::byUser()
            ->latest()
            ->limit(1)
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function isSent()
    {
        return $this->user_id == Auth::id();
    }

    public function images()
    {
        return $this->belongsToMany(Document::class);
    }
}
