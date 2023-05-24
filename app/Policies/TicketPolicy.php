<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use App\Traits\ByPassAdminCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization,
        ByPassAdminCheck;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view_tickets');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function view(User $user, Ticket $ticket)
    {
        return $user->hasPermission('show_ticket') && $ticket->user_id == $user->id || $user->hasPermission('reply_ticket');
    }

    public function show_ticket(User $user)
    {
        return $user->hasPermission('show_ticket');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_ticket');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function update(User $user, Ticket $ticket)
    {
        return ($user->hasPermission('edit_ticket') && $ticket->user_id == $user->id )|| $user->hasPermission('reply_ticket');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function delete(User $user, Ticket $ticket)
    {
        return $user->hasPermission('delete_ticket') && $ticket->user_id == $user->id;
    }
 
    public function reply(User $user)
    {
        return $user->hasPermission('reply_ticket');
    }

    public function open(User $user)
    {
        return $user->hasPermission('open_ticket');
    }

    public function close(User $user,Ticket $ticket=null)
    {
        return $user->hasPermission('close_ticket') || optional($ticket)->user_id == $user->id;
    }
 

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function restore(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return mixed
     */
    public function forceDelete(User $user, Ticket $ticket)
    {
        //
    }
}
