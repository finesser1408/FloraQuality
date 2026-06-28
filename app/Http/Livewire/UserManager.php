<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $role = '';
    public string $status = '';

    // Form
    public bool $showFormModal = false;
    public ?int $userId = null; // Null means create
    
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $userRole = 'staff';
    public string $password = '';
    public string $password_confirmation = '';
    public string $userStatus = 'active';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRole(): void   { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $user = User::findOrFail($id);
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone_number ?? '';
        $this->userRole = $user->role;
        $this->userStatus = $user->status;
        $this->showFormModal = true;
    }

    public function resetForm(): void
    {
        $this->reset([
            'userId', 'name', 'email', 'phone', 'userRole',
            'password', 'password_confirmation', 'userStatus'
        ]);
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->userId ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'userRole' => 'required|in:super_admin,admin,staff',
            'userStatus' => 'required|in:active,inactive',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone ?: null,
            'role' => $this->userRole,
            'status' => $this->userStatus,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            
            // Prevent changing own role or status
            if ($user->id === auth()->id()) {
                $data['role'] = $user->role;
                $data['status'] = $user->status;
            }

            $user->update($data);

            AuditService::log('updated', 'User', $user->id, "Updated user {$user->name}'s profile details.");
            session()->flash('success', 'User profile updated successfully.');
        } else {
            // New user gets require_password_change set to true by default to force initial password change
            $data['require_password_change'] = true;
            $user = User::create($data);

            AuditService::log('created', 'User', $user->id, "Created new user account: {$user->name} ({$user->role})");
            session()->flash('success', 'User account created successfully.');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function toggleUserStatus(int $id): void
    {
        $this->authorize('delete', User::findOrFail($id)); // Ensure deletion permission is required to disable accounts
        
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot deactivate your own account.');
            return;
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        AuditService::log('updated', 'User', $user->id, "Toggled user status of {$user->name} to {$user->status}");
        session()->flash('success', "User account status changed to {$user->status}.");
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        AuditService::log('deleted', 'User', $user->id, "Permanently deleted user account {$user->name}");
        $user->delete();

        session()->flash('success', 'User account permanently deleted.');
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->role) {
            $query->where('role', $this->role);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $users = $query->orderBy('name')->paginate(12);

        return view('livewire.user-manager', [
            'users' => $users
        ])->layout('layouts.app');
    }
}
