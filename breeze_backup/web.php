<?php

use App\Http\Livewire\ChecklistForm;
use App\Http\Livewire\ChecklistTable;
use App\Http\Livewire\ChecklistView;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\ReportGenerator;
use App\Models\FlowerChecklist;
use Illuminate\Support\Facades\Route;

// ─── Guest routes (login / register) ────────────────────────────────────────
// Provided by Laravel Breeze / Jetstream — run `php artisan breeze:install` to scaffold

// ─── Authenticated routes ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Checklists
    Route::get('/checklists',          ChecklistTable::class)->name('checklists.index');
    Route::get('/checklists/create',   ChecklistForm::class)->name('checklists.create');
    Route::get('/checklists/{id}',     ChecklistView::class)->name('checklists.show');
    Route::get('/checklists/{id}/edit',ChecklistForm::class)->name('checklists.edit');

    // Print (standard controller action, no Livewire)
    Route::get('/checklists/{checklist}/print', function (FlowerChecklist $checklist) {
        abort_unless(
            auth()->user()->isAdmin() || auth()->id() === $checklist->user_id,
            403
        );
        return view('checklists.print', compact('checklist'));
    })->name('checklists.print');

    // Reports (admin only)
    Route::get('/reports', ReportGenerator::class)
         ->middleware('can:export,App\Models\FlowerChecklist')
         ->name('reports.index');
});
