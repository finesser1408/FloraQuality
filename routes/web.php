<?php

use App\Http\Controllers\ProfileController;
use App\Http\Livewire\ChecklistForm;
use App\Http\Livewire\ChecklistTable;
use App\Http\Livewire\ChecklistView;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\ReportGenerator;
use App\Http\Livewire\UserManager;
use App\Http\Livewire\AuditLogs;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Checklist routes
    Route::get('/checklists', ChecklistTable::class)->name('checklists.index');
    Route::get('/checklists/create', ChecklistForm::class)->name('checklists.create');
    Route::get('/checklists/{id}/edit', ChecklistForm::class)->name('checklists.edit');
    Route::get('/checklists/{id}', ChecklistView::class)->name('checklists.show');

    // Reports
    Route::get('/reports', ReportGenerator::class)->name('reports.index');

    // User Management & Audit Logs (Super Admin only)
    Route::get('/users', UserManager::class)->name('users.index');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
