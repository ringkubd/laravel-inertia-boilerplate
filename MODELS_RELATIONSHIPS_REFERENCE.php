<?php

/**
 * ============================================================================
 * SUPPORT TICKET MODEL - COMPLETE RELATIONSHIPS
 * ============================================================================
 *
 * Ensure your app/Models/SupportTicket.php has all these relationships.
 *
 * Copy this if you need to add the messages() relationship:
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the user who created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin assigned to this ticket.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all attachments for this ticket.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get all messages/replies for this ticket.
     * ADD THIS IF MISSING!
     */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'support_ticket_id');
    }
}

/**
 * ============================================================================
 * TICKET MESSAGE MODEL - COMPLETE RELATIONSHIPS
 * ============================================================================
 *
 * Ensure your app/Models/TicketMessage.php looks like this:
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the user who sent this message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket this message belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    /**
     * Get all attachments for this message.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

/**
 * ============================================================================
 * ATTACHMENT MODEL - COMPLETE RELATIONSHIPS
 * ============================================================================
 *
 * Ensure your app/Models/Attachment.php looks like this:
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the parent model (SupportTicket or TicketMessage).
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this file.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

/**
 * ============================================================================
 * VERIFY YOUR MODELS
 * ============================================================================
 *
 * Use this command to verify relationships are working:
 *
 * php artisan tinker
 * >>> $ticket = App\Models\SupportTicket::with(['user', 'messages', 'attachments'])->first();
 * >>> $ticket->user  // Should return User model
 * >>> $ticket->messages  // Should return collection of messages
 * >>> $ticket->attachments  // Should return collection of attachments
 *
 * If any of these fail, add the missing relationship method.
 *
 */
