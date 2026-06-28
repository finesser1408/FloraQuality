<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $wasRequiredToChange = $user->require_password_change;

        $user->update([
            'password' => Hash::make($validated['password']),
            'require_password_change' => false,
        ]);

        \App\Services\AuditService::log('updated', 'User', $user->id, "User {$user->name} changed their password.");

        if ($wasRequiredToChange) {
            return redirect()->route('dashboard')->with('success', 'Password updated successfully. You now have full access to the system.');
        }

        return back()->with('status', 'password-updated');
    }
}
