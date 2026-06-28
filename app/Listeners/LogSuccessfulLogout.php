<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            AuditService::log('logout', 'User', $event->user->id, "User {$event->user->name} logged out.");
        }
    }
}
