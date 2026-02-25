# Support Ticket System - Implementation Checklist

## ✅ Pre-Implementation (Already Done)

- [x] Created Models: `SupportTicket`, `TicketMessage`, `Attachment`
- [x] Created Migrations for all tables
- [x] Created Events: `TicketCreated`, `TicketMessageCreated`, `TicketStatusUpdated`, `TicketAssigned`
- [x] Created Vue Components: `TicketsList`, `CreateTicket`, `TicketDetail`, `AdminTickets`
- [x] Created Controllers: `TicketMessageController`
- [x] Created Resources: `SupportTicketResource`, `TicketMessageResource`, `AttachmentResource`

---

## 🔧 Implementation Steps (You Need to Do)

### Step 1: Update SupportTicketController
- [ ] Open `app/Http/Controllers/SupportTicketController.php`
- [ ] Replace entire file contents with code from `app/Http/Controllers/SupportTicketController.php.generation`
- [ ] Or manually add these methods:
  - `index()` - List tickets with role-based filtering
  - `create()` - Show create form
  - `store()` - Create ticket with attachments
  - `show()` - Show ticket detail
  - `update()` - Update status and assignment
  - `destroy()` - Delete ticket
  - `adminDashboard()` - Admin view

### Step 2: Add Routes to web.php
- [ ] Open `routes/web.php`
- [ ] Add imports at top:
  ```php
  use App\Http\Controllers\SupportTicketController;
  use App\Http\Controllers\TicketMessageController;
  ```
- [ ] Add support ticket routes inside authenticated route group
- [ ] Reference `ROUTES_INTEGRATION_GUIDE.php` for exact code
- [ ] Test routes: `php artisan route:list | grep support`

### Step 3: Configure Storage (if not already done)
- [ ] Run: `php artisan storage:link`
- [ ] Check `config/filesystems.php` has `'public'` disk configured
- [ ] Ensure `storage/app/public` directory exists

### Step 4: Configure WebSockets for Real-Time
- [ ] Check `.env` has PUSHER settings:
  ```
  PUSHER_APP_ID=your_id
  PUSHER_APP_KEY=your_key
  PUSHER_APP_SECRET=your_secret
  PUSHER_APP_CLUSTER=mt1
  ```
- [ ] If using laravel-websockets, start server: `php artisan websockets:serve`
- [ ] Test Echo connection in browser console

### Step 5: Verify Models Have Relationships
- [ ] Check `app/Models/SupportTicket.php`:
  - [ ] `user()` relationship
  - [ ] `attachments()` polymorphic relationship
  - [ ] `assignedTo()` relationship
  - [ ] `messages()` relationship (add if missing)
  
- [ ] Check `app/Models/TicketMessage.php`:
  - [ ] `user()` relationship
  - [ ] `ticket()` relationship  
  - [ ] `attachments()` polymorphic relationship
  
- [ ] Check `app/Models/Attachment.php`:
  - [ ] `attachable()` polymorphic relationship

### Step 6: Set Up Navigation
- [ ] Add link to Support Tickets in main navigation
- [ ] For users: `/support-tickets`
- [ ] For admins: `/support-tickets/admin/dashboard`
- [ ] Example link:
  ```html
  <Link href="/support-tickets">Support Tickets</Link>
  ```

### Step 7: Test the System
- [ ] Run migrations: `php artisan migrate` (if not done)
- [ ] Clear cache: `php artisan config:clear` and `php artisan route:clear`
- [ ] As Student: Create a ticket at `/support-tickets/create`
- [ ] As Admin: View all at `/support-tickets/admin/dashboard`
- [ ] Test real-time updates (create ticket, should appear instantly)
- [ ] Test file uploads (drag and drop)
- [ ] Test status changes and assignments

---

## 🚀 Quick Start

### For Users (Students, Instructors, Lab Attendants):
1. Click on Support Tickets in navigation
2. Click "Create Ticket"
3. Fill subject and description
4. Optionally add attachments
5. Submit

### For Admins:
1. Navigate to `/support-tickets/admin/dashboard`
2. See all tickets with statistics
3. Filter by status, assignment, search
4. Click ticket to view details
5. Reply with admin sender_type
6. Change status or assign to another admin

---

## 📊 Features Checklist

- [x] Users can create tickets
- [x] Users can view only their tickets
- [x] Admins can view all tickets
- [x] Admins can assign tickets
- [x] Admins can change status
- [x] Real-time message updates
- [x] File attachments support
- [x] Admin dashboard with statistics
- [x] Role-based access control
- [x] Message conversations
- [x] Drag-and-drop file uploads
- [x] Responsive design
- [x] Status color coding
- [x] Pagination support

---

## 🐛 Troubleshooting

### Routes not found (404 errors)
```bash
php artisan route:clear
php artisan config:clear
php artisan route:list
```

### Attachments not uploading
```bash
php artisan storage:link
# Check storage/app/public folder exists
```

### Real-time not working
- Check WebSocket server is running
- Check browser console for errors
- Verify PUSHER env settings
- Check network tab for WebSocket connection

### Models not found
- Verify models exist in `app/Models/`
- Check controller imports
- Run `composer dump-autoload`

### Components not rendering
- Check Vue components are in `resources/js/Pages/Support/`
- Check Inertia is properly configured
- Run `npm run dev` to compile assets

---

## 📝 File Locations Summary

### Models
- `app/Models/SupportTicket.php`
- `app/Models/TicketMessage.php`
- `app/Models/Attachment.php`

### Controllers
- `app/Http/Controllers/SupportTicketController.php` ← **Update this**
- `app/Http/Controllers/TicketMessageController.php` ✅ Created

### Events
- `app/Events/TicketCreated.php` ✅
- `app/Events/TicketMessageCreated.php` ✅
- `app/Events/TicketStatusUpdated.php` ✅
- `app/Events/TicketAssigned.php` ✅

### Resources
- `app/Http/Resources/SupportTicketResource.php` ✅
- `app/Http/Resources/TicketMessageResource.php` ✅
- `app/Http/Resources/AttachmentResource.php` ✅

### Vue Components
- `resources/js/Pages/Support/TicketsList.vue` ✅
- `resources/js/Pages/Support/CreateTicket.vue` ✅
- `resources/js/Pages/Support/TicketDetail.vue` ✅
- `resources/js/Pages/Support/AdminTickets.vue` ✅

### Routes
- `routes/web.php` ← **Add routes from guide**

### Migrations
- `database/migrations/2026_02_25_123145_create_support_tickets_table.php` ✅
- `database/migrations/2026_02_25_123344_create_ticket_messages_table.php` ✅
- `database/migrations/2026_02_25_123548_create_attachments_table.php` ✅

---

## 🎯 Role Permissions

### Student / Instructor / Lab Attendant
- Create own tickets
- View own tickets
- Reply to own tickets
- View ticket history

### Admin / Super Admin / Account
- View all tickets
- Create tickets
- Reply to tickets (as admin)
- Change ticket status
- Assign tickets to self/others
- View admin dashboard with statistics
- Inline ticket management

---

## ⏱️ Estimated Time

- [ ] Update SupportTicketController: **10 minutes**
- [ ] Add routes to web.php: **5 minutes**
- [ ] Configure storage: **2 minutes**
- [ ] Test all features: **15 minutes**
- [x] Total: **~30 minutes**

---

## 💡 Next Steps After Implementation

1. **Add notifications:**
   - Email when ticket is created
   - Email when status changes
   - Email when new reply is added

2. **Add enhancements:**
   - Ticket priority levels
   - Ticket categories
   - Ticket estimated resolution time
   - Ticket tags for organization

3. **Add monitoring:**
   - View ticket response time analytics
   - Track admin performance
   - Generate support reports

4. **Add automation:**
   - Auto-close tickets after inactivity
   - Auto-assign to least busy admin
   - Canned responses for common issues

---

## ✨ Key Features Implemented

### For Users:
- 📝 Create support tickets with description
- 📎 Attach files to tickets
- 💬 View and contribute to ticket conversation
- 📊 See ticket status in real-time
- 🔍 Search and filter own tickets

### For Admins:
- 👀 View all support tickets
- 📈 Dashboard with statistics
- 🎯 Assign tickets to team members
- ✅ Change ticket status
- 💬 Reply as admin to tickets
- 📎 Attach files to replies
- 🔄 Real-time updates on all actions
- 🔍 Advanced filtering and search

---

## 📞 Support & Help

If you encounter issues:
1. Check the SUPPORT_TICKETS_SETUP.md for detailed guide
2. Review controller code in SupportTicketController.php.generation
3. Verify all imports and relationships
4. Check browser console for frontend errors
5. Check Laravel logs: `storage/logs/laravel.log`

---

**Status: Ready for Implementation! ✅**

Start with Step 1 and follow through the checklist.
