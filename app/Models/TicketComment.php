<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    protected $guarded = [];

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
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function isSent()
    {
        return $this->user_id == Auth::id();
    }
}
