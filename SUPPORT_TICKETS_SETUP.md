# Support Ticket System - Complete Implementation Guide

## 📋 Overview

This guide walks you through implementing a complete real-time support ticket system with Laravel, Vue 3, and Inertia.js. The system supports role-based access where:

- **Users (Students, Instructors, Lab Attendants)** can create and view their own tickets
- **Admins (Admin, Super Admin, Account)** can view all tickets, assign them, and reply to them
- **Real-time updates** are provided via Laravel Echo and WebSockets

---

## 🔧 Installation Steps

### Step 1: Database Models (✅ Already Created)

The following models have been created:
- `SupportTicket` - Main ticket model
- `TicketMessage` - Messages/replies on tickets
- `Attachment` - File attachments for tickets and messages

**Verify migrations have been run:**
```bash
php artisan migrate
```

---

### Step 2: Create Events (✅ Already Created)

The following event files have been created in `app/Events/`:
- `TicketCreated.php` - Triggered when a ticket is created
- `TicketMessageCreated.php` - Triggered when a message is added
- `TicketStatusUpdated.php` - Triggered when status changes
- `TicketAssigned.php` - Triggered when ticket is assigned

These events use `ShouldBroadcast` to enable real-time updates through WebSockets.

---

### Step 3: Create Vue Components (✅ Already Created)

The following Vue 3 components have been created in `resources/js/Pages/Support/`:

1. **TicketsList.vue** - List all support tickets
   - Filters by status and search
   - Real-time updates when new tickets are created
   - Pagination support

2. **CreateTicket.vue** - Form to create new ticket
   - Drag-and-drop file uploads
   - Up to 5 file attachments
   - Form validation

3. **TicketDetail.vue** - View single ticket with messages
   - Real-time message updates
   - File upload for replies
   - Status and assignment management (for admins)
   - Message conversation view

4. **AdminTickets.vue** - Admin dashboard
   - View all tickets with statistics
   - Filter by status, assignment, and search
   - Inline status and assignment updates
   - Real-time stat updates

---

### Step 4: Update SupportTicketController

**Replace the entire contents of `app/Http/Controllers/SupportTicketController.php` with:**

See file: `app/Http/Controllers/SupportTicketController.php.generation`

This controller includes:
- `index()` - List tickets (role-based)
- `create()` - Show create form
- `store()` - Create new ticket with attachments
- `show()` - Display ticket detail
- `update()` - Update status and assignment
- `destroy()` - Delete ticket
- `adminDashboard()` - Admin dashboard view

---

### Step 5: Create TicketMessageController

The file `app/Http/Controllers/TicketMessageController.php` has been created.

This controller includes:
- `store()` - Create message/reply on ticket
- `destroy()` - Delete message

---

### Step 6: Add Routes

**Add the following routes to `routes/web.php` inside the `auth` middleware group:**

```php
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TicketMessageController;

// Support Tickets Routes
Route::prefix('support-tickets')->group(function () {
    // List and create tickets
    Route::get('/', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/create', [SupportTicketController::class, 'create'])->name('support-tickets.create');
    Route::post('/', [SupportTicketController::class, 'store'])->name('support-tickets.store');
    
    // View and manage specific ticket
    Route::get('/{supportTicket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::patch('/{supportTicket}', [SupportTicketController::class, 'update'])->name('support-tickets.update');
    Route::delete('/{supportTicket}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');
    
    // Admin dashboard
    Route::get('/admin/dashboard', [SupportTicketController::class, 'adminDashboard'])->name('support-tickets.admin');
    
    // Ticket messages
    Route::post('/{ticket}/messages', [TicketMessageController::class, 'store'])->name('ticket-messages.store');
    Route::delete('/messages/{message}', [TicketMessageController::class, 'destroy'])->name('ticket-messages.destroy');
});
```

---

### Step 7: Create Resource Classes

Create API resources for proper data formatting:

**Files created:**
- `app/Http/Resources/SupportTicketResource.php`
- `app/Http/Resources/TicketMessageResource.php`
- `app/Http/Resources/AttachmentResource.php`

These are used to format data for API responses and frontend components.

---

### Step 8: Ensure WebSockets are Running

For real-time functionality, ensure Laravel WebSockets (or Pusher) is configured:

1. Check your `.env` file has correct PUSHER/WEBSOCKET settings:
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

2. Start WebSockets server (if using laravel-websockets):
```bash
php artisan websockets:serve
```

---

## ✨ Features

### Real-Time Updates
- New tickets appear instantly in the list
- Messages appear in real-time in the conversation
- Status and assignment changes update immediately
- Admin dashboard stats update in real-time

### Role-Based Access
- Students/Instructors/Lab Attendants: Can create and view their tickets
- Admins: Can view all, assign, reply, and change status
- Automatic status updates when admin replies

### File Attachments
- Support for tickets and messages
- Drag-and-drop file uploads
- File size validation (max 10MB)
- Multiple file types (images, PDFs, documents)

### Admin Features
- Dashboard with statistics
- Filter by status, assignment, search
- Inline status and assignment updates
- View all user tickets
- Real-time stat updates

---

## 🔒 Authorization

The system uses role-based authorization:

```php
$user->hasAnyRole(['Student', 'Instructor', 'Lab Attendant']) // Can create
$user->hasAnyRole(['Admin', 'Super Admin', 'Account']) // Can manage all
```

Make sure your User model has the `roles` relationship properly set up.

---

## 📝 Database Schema

### support_tickets table
- id, user_id, assigned_to, subject, description, status (open/in_progress/closed), created_at, updated_at, deleted_at

### ticket_messages table
- id, support_ticket_id, user_id, message, sender_type (user/admin), is_read, created_at, updated_at, deleted_at

### attachments table
- id, attachable_id, attachable_type (morphs), filename, filepath, mimetype, filesize, uploaded_by, created_at, updated_at, deleted_at

---

## 🧪 Testing

### Create a Ticket
1. Navigate to `/support-tickets/create`
2. Fill in subject and description
3. Optionally add attachments
4. Submit

### View Tickets
1. Navigate to `/support-tickets`
2. See your tickets listed with real-time updates

### Admin Dashboard
1. As admin, navigate to `/support-tickets/admin/dashboard`
2. See all tickets with statistics
3. Filter and manage tickets
4. Assign and update status in real-time

### Reply to Ticket
1. Click "View Details" on any ticket
2. Scroll to "Conversation" section
3. Type reply and optionally add attachment
4. Click "Send Reply"
5. Message appears in real-time to all viewers

---

## 🚀 Next Steps

1. **Test all features** in development
2. **Create navigation links** to support tickets in your main layout
3. **Add email notifications** when ticket status changes
4. **Add soft delete recovery** for admins if needed
5. **Implement ticket priority levels** (low, medium, high)
6. **Add ticket categories/tags** for better organization

---

## 📞 Support Features Summary

| Feature | Users | Admins |
|---------|-------|--------|
| Create Tickets | ✅ | ✅ |
| View Own Tickets | ✅ | ❌ |
| View All Tickets | ❌ | ✅ |
| Reply to Tickets | ✅ | ✅ |
| Change Status | ❌ | ✅ |
| Assign Tickets | ❌ | ✅ |
| Add Attachments | ✅ | ✅ |
| Real-time Updates | ✅ | ✅ |
| Admin Dashboard | ❌ | ✅ |

---

## 🐛 Troubleshooting

### Real-time updates not working
- Check WebSockets server is running
- Verify PUSHER settings in .env
- Check browser console for errors

### Attachments not uploading
- Check storage directory permissions
- Verify disk 'public' is configured in config/filesystems.php
- Use `php artisan storage:link` if needed

### Routes not found
- Run `php artisan route:clear`
- Clear config cache: `php artisan config:clear`

---

## 📦 Files Created/Modified

### Files Created:
- `app/Events/TicketCreated.php`
- `app/Events/TicketMessageCreated.php`
- `app/Events/TicketStatusUpdated.php`
- `app/Events/TicketAssigned.php`
- `app/Http/Controllers/TicketMessageController.php`
- `app/Http/Resources/SupportTicketResource.php`
- `app/Http/Resources/TicketMessageResource.php`
- `app/Http/Resources/AttachmentResource.php`
- `resources/js/Pages/Support/TicketsList.vue`
- `resources/js/Pages/Support/CreateTicket.vue`
- `resources/js/Pages/Support/TicketDetail.vue`
- `resources/js/Pages/Support/AdminTickets.vue`

### Files to Update:
- `app/Http/Controllers/SupportTicketController.php` (replace entirely)
- `routes/web.php` (add routes)
- `app/Models/SupportTicket.php` (already created)
- `app/Models/TicketMessage.php` (already created)
- `app/Models/Attachment.php` (already created)

---

## 🎨 UI/UX Features

- Clean, modern design matching your existing layout
- Responsive design (mobile-friendly)
- Real-time indicators (typing, new messages)
- Status color coding
- Smooth animations and transitions
- Inline editing for admin controls
- Drag-and-drop file uploads
- File preview icons
- User-friendly error messages

---

All components use Tailwind CSS and Vue 3 Composition API patterns matching your existing codebase!
