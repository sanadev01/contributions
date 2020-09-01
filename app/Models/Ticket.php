<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{   
    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function scopeOpen(Builder $query)
    {
        return $query->where('open', 1);
    }

    public function scopeClosed(Builder $query)
    {
        return $query->where('open', 0);
    }

    public function getHumanID()
    {
        return "ST-{$this->id}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen()
    {
        return $this->open == 1;
    }

    public static function getResponseTime()
    {
        $lastAdminComment = TicketComment::getLastCommentByAdmin();
        $lastUserComment = TicketComment::getLastCommentByUser();

        $responseTime = 0;

        if (! $lastUserComment) {
            $responseTime = 0;
        }
        if ($lastUserComment && ! $lastAdminComment) {
            $responseTime = Carbon::now()->diffInHours($lastUserComment->created_at);
        }
        if ($lastUserComment && $lastAdminComment) {
            $responseTime = $lastAdminComment->created_at->diffInHours($lastUserComment->created_at);
        }

        return $responseTime;
    }

    public static function newTickets($fromData = null)
    {
        if (! $fromData) {
            $fromData = Carbon::now()->subDays(7);
        }

        return SupportTicket::query()
            ->where('created_at', '>=', $fromData)
            ->count();
    }

    public static function completedTickets()
    {
        $closedTickets = self::query()
            ->closed()
            ->count();

        $totalTickets = self::query()
            ->count();

        if (! $totalTickets) {
            return 0;
        }

        return  $closedTickets * 100 / $totalTickets;
    }

    public function addComment(Request $request)
    {
        return self::comments()->create([
            'user_id' => Auth::id(),
            'text' => $request->get('text')
        ]);
    }

    public function markClosed()
    {
        return self::update([
            'open' => 'closed'
        ]);
    }
}
 