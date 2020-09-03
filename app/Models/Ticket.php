<?php

namespace App\Models;

use App\Models\TicketComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{   
    protected $guarded = [];

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function scopeOpen(Builder $query)
    {
        return $query->where('open', true);
    }

    public function scopeClosed(Builder $query)
    {
        return $query->where('open', false);
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
        return $this->open;
    }

    public function addComment(Request $request)
    {
        return $this->comments()->create([
            'user_id' => Auth::id(),
            'text' => $request->get('text')
        ]);
    }

    public function markClosed()
    {
        return $this->update([
            'open' => false
        ]);
    }
}
 