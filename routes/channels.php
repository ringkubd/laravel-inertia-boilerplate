<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user_online_status', function($user){
    return $user;
});

Broadcast::channel('messages.{conversation}', function($user, \App\Models\Conversation $conversation){
    return $conversation->conversationUsers()->where('user_id', $user->id)->get()->count() > 0;
});

Broadcast::channel('support.{conversation}', function($user, \App\Models\SupportConversation $conversation){
    return $conversation->creator == $user->id || $user->hasRole('Super Admin') || $user->hasRole('Admin');
});


Broadcast::channel('support', function($user){
    return $user;
});

Broadcast::channel('mail_box', function ($user, \App\Models\MailBox $message){
    return $user->email === $message->to;
});
Broadcast::channel('admin_mail', function ($user){
    return auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin');
}, ['middleware' => 'role:Super Admin']);

Broadcast::channel('post.{post}', function($user, \App\Models\Post $post){
    return (int)$user->id === (int)$post->author;
});
Broadcast::channel('activity.{id}', function ($user, $id) {
    return true;
});
Broadcast::channel('conversation.{id}', function ($user, $id) {
    return true;
});

// Support Tickets Channels
Broadcast::channel('support-tickets', function($user) {
    return $user;
});

Broadcast::channel('ticket.{ticketId}', function($user, $ticketId) {
    $ticket = \App\Models\SupportTicket::find($ticketId);
    if (!$ticket) {
        return false;
    }

    $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);
    $isOwner = $ticket->user_id === $user->id;

    return $isAdmin || $isOwner;
});
