# 🎨 Support Ticket System - Visual Implementation Guide

## 📊 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                   SUPPORT TICKET SYSTEM                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────┐         ┌──────────────────────┐  │
│  │   USERS (Frontend)   │         │   ADMINS (Frontend)  │  │
│  ├──────────────────────┤         ├──────────────────────┤  │
│  │ • Create Ticket      │         │ • Dashboard          │  │
│  │ • View Own Tickets   │         │ • View All Tickets   │  │
│  │ • Reply to Ticket    │         │ • Assign Tickets     │  │
│  │ • Upload Files       │         │ • Change Status      │  │
│  └──────────────────────┘         │ • Inline Management  │  │
│           │                        └──────────────────────┘  │
│           │                                   │               │
│           └───────────────┬────────────────────┘               │
│                           │                                    │
│                   ┌───────▼────────┐                          │
│                   │  Inertia Adapter                         │
│                   │  (Vue 3 SSR)   │                          │
│                   └───────┬────────┘                          │
│                           │                                    │
│           ┌───────────────┼───────────────┐                  │
│           ▼               ▼               ▼                   │
│     ┌──────────┐   ┌──────────┐   ┌──────────┐              │
│     │ Laravel  │   │ Laravel  │   │ Laravel  │              │
│     │ Echo     │   │ Routes   │   │ Events   │              │
│     └──────────┘   └──────────┘   └──────────┘              │
│           │               │               │                   │
│           │               ▼               │                   │
│           │       ┌──────────────────┐   │                   │
│           │       │ Controllers      │   │                   │
│           │       │ • Support Ticket │   │                   │
│           │       │ • Ticket Message │   │                   │
│           │       └────────┬─────────┘   │                   │
│           │                │              │                   │
│           │    ┌───────────▼────────────┐ │                   │
│           │    │   Database Models      │ │                   │
│           │    │   • SupportTicket      │ │                   │
│           │    │   • TicketMessage      │ │                   │
│           │    │   • Attachment         │ │                   │
│           │    └────────────────────────┘ │                   │
│           │                                │                   │
│           ▼                                ▼                   │
│     ┌───────────┐                   ┌───────────┐            │
│     │ WebSocket │                   │ Database  │            │
│     │ (Real-time│                   │ (Storage) │            │
│     │ Updates)  │                   └───────────┘            │
│     └───────────┘                                             │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 File Organization

```
PROJECT ROOT
│
├── app/
│   ├── Events/ ✅ CREATED
│   │   ├── TicketCreated.php
│   │   ├── TicketMessageCreated.php
│   │   ├── TicketStatusUpdated.php
│   │   └── TicketAssigned.php
│   │
│   ├── Http/
│   │   ├── Controllers/ ⚠️ NEEDS UPDATE
│   │   │   ├── SupportTicketController.php ← MAIN FILE TO UPDATE
│   │   │   └── TicketMessageController.php ✅ CREATED
│   │   │
│   │   └── Resources/ ✅ CREATED
│   │       ├── SupportTicketResource.php
│   │       ├── TicketMessageResource.php
│   │       └── AttachmentResource.php
│   │
│   └── Models/ ✅ CREATED
│       ├── SupportTicket.php (+ messages() added)
│       ├── TicketMessage.php
│       └── Attachment.php
│
├── database/
│   └── migrations/ ✅ CREATED
│       ├── 2026_02_25_123145_create_support_tickets_table.php
│       ├── 2026_02_25_123344_create_ticket_messages_table.php
│       └── 2026_02_25_123548_create_attachments_table.php
│
├── resources/js/Pages/Support/ ✅ CREATED
│   ├── TicketsList.vue
│   ├── CreateTicket.vue
│   ├── TicketDetail.vue
│   └── AdminTickets.vue
│
├── routes/
│   └── web.php ⚠️ NEEDS ROUTES ADDED
│
├── SUPPORT_TICKETS_SETUP.md ✅ Guide
├── IMPLEMENTATION_CHECKLIST.md ✅ Checklist
├── ROUTES_INTEGRATION_GUIDE.php ✅ Routes reference
├── MODELS_RELATIONSHIPS_REFERENCE.php ✅ Models reference
├── README_SUPPORT_TICKETS.md ✅ Summary
└── app/Http/Controllers/SupportTicketController.php.generation ✅ Complete code
```

---

## 🔄 Data Flow Diagram

### Creating a Ticket:
```
User Interface (Vue)
        │
        │ POST /support-tickets
        ▼
SupportTicketController::store()
        │
        ├─ Validate input
        ├─ Save to database
        ├─ Save attachments
        │
        ▼
Broadcast TicketCreated event
        │
        ├─ Admin sees instantly
        ├─ Dashboard updates
        └─ Stats refresh
```

### Replying to Ticket:
```
Admin/User Interface (Vue)
        │
        │ POST /support-tickets/{id}/messages
        ▼
TicketMessageController::store()
        │
        ├─ Validate input
        ├─ Save message to database
        ├─ Save attachment
        ├─ Update ticket status (if admin)
        │
        ▼
Broadcast TicketMessageCreated event
        │
        ├─ User sees message instantly
        ├─ Admin sees message instantly
        └─ Status updates on dashboard
```

### Status Change:
```
Admin Dashboard (Vue)
        │
        │ PATCH /support-tickets/{id}
        ▼
SupportTicketController::update()
        │
        ├─ Validate input
        ├─ Save status change
        │
        ▼
Broadcast TicketStatusUpdated event
        │
        ├─ All viewers see status change
        ├─ Dashboard stats update
        └─ Dashboard tickets list updates
```

---

## 📱 UI Component Hierarchy

```
Layout (app.blade.php)
│
├── Navbar
│   └── Link: Support Tickets or Admin Dashboard
│
└── Support System
    │
    ├── TicketsList.vue
    │   ├── Filter Section
    │   ├── Ticket Cards (map over tickets)
    │   │   ├── Status Badge
    │   │   ├── Metadata (user, date, assigned)
    │   │   └── View Details Link
    │   └── Pagination
    │
    ├── CreateTicket.vue
    │   ├── Subject Input
    │   ├── Description Textarea
    │   ├── File Upload (drag & drop)
    │   ├── File List
    │   └── Submit Button
    │
    ├── TicketDetail.vue
    │   ├── Ticket Header
    │   ├── Ticket Info Panel
    │   ├── Messages Section
    │   │   ├── Message List (map over messages)
    │   │   │   └── Message Bubble
    │   │   └── Reply Form
    │   │       ├── Textarea
    │   │       ├── File Upload
    │   │       └── Send Button
    │   └── Sidebar
    │       ├── Status Select (admin only)
    │       ├── Assign Select (admin only)
    │       └── Details Panel
    │
    └── AdminTickets.vue
        ├── Statistics Cards
        ├── Filter Section
        ├── Tickets Table
        │   ├── Ticket ID
        │   ├── Subject
        │   ├── User
        │   ├── Status Select
        │   ├── Assign Select
        │   ├── Message Count
        │   ├── Created Date
        │   └── Actions
        └── Pagination
```

---

## 🔌 WebSocket Event Flow

```
Real-Time Events (Broadcasting)

┌─────────────────────────────────────┐
│  Event: TicketCreated              │
├─────────────────────────────────────┤
│ Channel: support-tickets            │
│ Listened by: TicketsList.vue        │
│ Action: Add ticket to list          │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  Event: TicketMessageCreated        │
├─────────────────────────────────────┤
│ Channel: ticket.{id}                │
│ Listened by: TicketDetail.vue       │
│ Action: Add message to conversation │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  Event: TicketStatusUpdated         │
├─────────────────────────────────────┤
│ Channels:                            │
│   • support-tickets (all)           │
│   • ticket.{id} (detail)            │
│ Listened by: Multiple components    │
│ Action: Update status display       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  Event: TicketAssigned              │
├─────────────────────────────────────┤
│ Channels:                            │
│   • support-tickets (all)           │
│   • ticket.{id} (detail)            │
│ Listened by: Multiple components    │
│ Action: Update assignment display   │
└─────────────────────────────────────┘
```

---

## 🔐 Authorization Flow

```
User Request
│
▼
Middleware: auth
│
├─ Not logged in → Redirect to login
├─ Logged in → Continue
│
▼
Route Handler (SupportTicketController method)
│
├─ For index/create/store:
│   ├─ Get user roles
│   ├─ Check: Can create? (Student/Instructor/Lab Attendant/Admin)
│   └─ If yes → Process; If no → 403 Forbidden
│
├─ For show/update/delete:
│   ├─ Get user roles
│   ├─ Is owner? OR Is admin?
│   └─ If yes → Process; If no → 403 Forbidden
│
├─ For adminDashboard:
│   ├─ Check: Is admin? (Admin/Super Admin/Account)
│   └─ If yes → Show all; If no → 403 Forbidden
│
└─ For update (status/assignment):
    ├─ Check: Is admin?
    └─ If yes → Allow; If no → 403 Forbidden
```

---

## 📊 Database Relationships

```
Users (existing)
  │
  ├─ One-to-Many ─→ SupportTicket (user_id)
  ├─ One-to-Many ─→ SupportTicket (assigned_to)
  └─ One-to-Many ─→ TicketMessage (user_id)

SupportTicket
  │
  ├─ Belongs To ─→ User (user_id)
  ├─ Belongs To ─→ User (assigned_to)
  ├─ One-to-Many ─→ TicketMessage
  └─ Morphs ─────→ Attachment

TicketMessage
  │
  ├─ Belongs To ─→ User
  ├─ Belongs To ─→ SupportTicket
  └─ Morphs ─────→ Attachment

Attachment
  │
  └─ Morphs To ───→ SupportTicket or TicketMessage
```

---

## 🎯 Implementation Order

```
Step 1: Update SupportTicketController (10 min)
│
├─ Open: app/Http/Controllers/SupportTicketController.php
├─ Copy contents from: .php.generation file
├─ Paste and save
│
Step 2: Add Routes (5 min)
│
├─ Open: routes/web.php
├─ Add: import statements
├─ Add: support-tickets Route::prefix group
│
Step 3: Verify Models (2 min)
│
├─ Check: SupportTicket has messages() ✅ Done already
├─ Check: TicketMessage has relationships ✅
├─ Check: User has roles() ✅
│
Step 4: Clear Caches (1 min)
│
├─ php artisan config:clear
├─ php artisan route:clear
│
Step 5: Test System (5 min)
│
├─ Navigate to /support-tickets
├─ Create a ticket
├─ Reply as admin
├─ Check real-time updates
└─ Test admin dashboard
```

---

## 🚀 Access Routes

```
Public Routes (Authenticated):
├─ GET  /support-tickets              → List user's tickets
├─ GET  /support-tickets/create       → Show create form
├─ POST /support-tickets              → Create ticket
├─ GET  /support-tickets/{id}         → Show ticket detail
├─ POST /support-tickets/{id}/messages→ Add message

Admin Routes (Authenticated + Admin):
├─ GET  /support-tickets/admin/dashboard → Admin dashboard
├─ PATCH /support-tickets/{id}           → Update status/assign

User Routes (Authenticated + Owner):
├─ DELETE /support-tickets/{id}              → Delete own ticket
└─ DELETE /support-tickets/messages/{id}     → Delete own message
```

---

## 📞 Contact Points

### For Users:
- Navigate: Click "Support Tickets" in nav
- Create: `/support-tickets/create`
- View List: `/support-tickets`
- View Detail: `/support-tickets/{id}`

### For Admins:
- Dashboard: `/support-tickets/admin/dashboard`
- View Detail: `/support-tickets/{id}` (can edit)

---

## ✅ Verification Checklist

After implementing, verify:

- [ ] Routes work: `php artisan route:list | grep support`
- [ ] Models load: `php artisan tinker` then `App\Models\SupportTicket::first()`
- [ ] Storage works: Files upload to `storage/app/public/tickets/*`
- [ ] WebSocket works: Check browser WebSocket connection
- [ ] Echo works: Messages appear in real-time
- [ ] Roles work: Admins can see all, users see own only
- [ ] Permissions work: Non-admins can't update status

---

**Next: Start with Step 1 in "Implementation Order"**

Good luck! 🚀
