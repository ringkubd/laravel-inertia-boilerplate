<?php

/**
 * ============================================================================
 * QUICK REFERENCE: ROUTES TO ADD TO routes/web.php
 * ============================================================================
 *
 * Copy this entire section and paste it after your other authenticated routes
 * but BEFORE any catch-all routes.
 *
 * Location in file: Inside a Route::middleware(['auth', 'verified'])->group(function () {
 * or similar authenticated route group
 *
 */

// ============ START: Support Tickets Routes ============

use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TicketMessageController;

Route::prefix('support-tickets')->group(function () {
    // Public ticket routes (students, instructors, lab attendants can create)
    Route::get('/', [SupportTicketController::class, 'index'])
        ->name('support-tickets.index');

    Route::get('/create', [SupportTicketController::class, 'create'])
        ->name('support-tickets.create');

    Route::post('/', [SupportTicketController::class, 'store'])
        ->name('support-tickets.store');

    // Ticket detail routes
    Route::get('/{supportTicket}', [SupportTicketController::class, 'show'])
        ->name('support-tickets.show');

    Route::patch('/{supportTicket}', [SupportTicketController::class, 'update'])
        ->name('support-tickets.update');

    Route::delete('/{supportTicket}', [SupportTicketController::class, 'destroy'])
        ->name('support-tickets.destroy');

    // Admin dashboard (admins only)
    Route::get('/admin/dashboard', [SupportTicketController::class, 'adminDashboard'])
        ->name('support-tickets.admin');

    // Message routes
    Route::post('/{ticket}/messages', [TicketMessageController::class, 'store'])
        ->name('ticket-messages.store');

    Route::delete('/messages/{message}', [TicketMessageController::class, 'destroy'])
        ->name('ticket-messages.destroy');
});

// ============ END: Support Tickets Routes ============

/**
 * ============================================================================
 * STEP-BY-STEP INTEGRATION:
 * ============================================================================
 *
 * 1. Open routes/web.php
 *
 * 2. Add the import statements at the top:
 *    use App\Http\Controllers\SupportTicketController;
 *    use App\Http\Controllers\TicketMessageController;
 *
 * 3. Find the authenticated routes group (look for middleware(['auth', 'verified']))
 *
 * 4. Inside that group, add all the Route definitions above
 *
 * 5. Save the file
 *
 * 6. Test the routes:
 *    php artisan route:list | grep support
 *
 * 7. You should see routes like:
 *    GET    /support-tickets
 *    POST   /support-tickets
 *    GET    /support-tickets/create
 *    GET    /support-tickets/{supportTicket}
 *    PATCH  /support-tickets/{supportTicket}
 *    DELETE /support-tickets/{supportTicket}
 *    GET    /support-tickets/admin/dashboard
 *    POST   /support-tickets/{ticket}/messages
 *    DELETE /support-tickets/messages/{message}
 *
 * ============================================================================
 * IMPORTANT NOTES:
 * ============================================================================
 *
 * The admin route intentionally uses /admin/dashboard after the prefix,
 * making the full path: /support-tickets/admin/dashboard
 *
 * Make sure the order is correct - define specific routes before {id} routes
 *
 * All routes automatically inherit the 'auth' middleware from the group
 *
 */
