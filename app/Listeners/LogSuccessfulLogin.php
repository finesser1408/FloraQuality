<?php

namespace App\Listeners;

use App\Services\AuditService;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $user->last_login_at = now();
        $user->save();

        AuditService::log('login', 'User', $user->id, "User {$user->name} logged in successfully.");
    }
}
