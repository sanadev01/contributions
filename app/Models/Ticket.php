<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TicketComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{   
    protected $guarded = [];
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
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

    public function getOpenDays()
    {
        return Carbon::parse($this->created_at)->diffInDays($this->open ? Carbon::now() :$this->updated_at);
    }
}
 