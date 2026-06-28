<?php

namespace App\Http\Livewire;

use App\Models\ActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $action = '';
    public ?int $selectedUser = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'action' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingAction(): void { $this->resetPage(); }

    public function mount(): void
    {
        $this->authorize('manageSystem', User::class);
    }

    public function render()
    {
        $query = ActivityLog::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhere('ip_address', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->action) {
            $query->where('action', $this->action);
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        $logs = $query->latest()->paginate(25);
        $users = User::orderBy('name')->get();

        return view('livewire.audit-logs', [
            'logs' => $logs,
            'users' => $users
        ])->layout('layouts.app');
    }
}
