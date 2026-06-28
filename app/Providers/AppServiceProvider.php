<?php

namespace App\Providers;

use App\Models\FlowerChecklist;
use App\Policies\ChecklistPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register authorization policies
        Gate::policy(FlowerChecklist::class, ChecklistPolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);

        // Register Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogSuccessfulLogin::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Listeners\LogSuccessfulLogout::class
        );

        // Register Livewire components
        Livewire::component('dashboard', \App\Http\Livewire\Dashboard::class);
        Livewire::component('checklist-form', \App\Http\Livewire\ChecklistForm::class);
        Livewire::component('checklist-table', \App\Http\Livewire\ChecklistTable::class);
        Livewire::component('checklist-view', \App\Http\Livewire\ChecklistView::class);
        Livewire::component('report-generator', \App\Http\Livewire\ReportGenerator::class);
        Livewire::component('user-manager', \App\Http\Livewire\UserManager::class);
        Livewire::component('audit-logs', \App\Http\Livewire\AuditLogs::class);
    }
}
