<?php

namespace App\Http\Livewire;

use App\Models\FlowerChecklist;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ChecklistTable extends Component
{
    use WithPagination;

    // ─── Filter/Search State ───────────────────────────────────────────────────

    public string $search      = '';
    public string $condition   = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public ?int   $staffId     = null;

    // ─── Sorting State ─────────────────────────────────────────────────────────

    public string $sortField     = 'check_date';
    public string $sortDirection = 'desc';

    // ─── Modals ────────────────────────────────────────────────────────────────

    public bool   $showDeleteModal = false;
    public ?int   $deleteId        = null;

    protected $paginationTheme = 'tailwind';

    // Reset page when filters change
    protected $queryString = [
        'search'    => ['except' => ''],
        'condition' => ['except' => ''],
        'dateFrom'  => ['except' => ''],
        'dateTo'    => ['except' => ''],
    ];

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingCondition(): void { $this->resetPage(); }
    public function updatingDateFrom(): void  { $this->resetPage(); }
    public function updatingDateTo(): void    { $this->resetPage(); }
    public function updatingStaffId(): void   { $this->resetPage(); }

    // ─── Sorting ───────────────────────────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── Delete Flow ───────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deleteId        = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId        = null;
        $this->showDeleteModal = false;
    }

    public function deleteChecklist(): void
    {
        $checklist = FlowerChecklist::findOrFail($this->deleteId);
        $this->authorize('delete', $checklist);

        AuditService::log('deleted', 'FlowerChecklist', $checklist->id,
            "Deleted checklist #{$checklist->id} ({$checklist->check_date->toDateString()})");

        $checklist->delete();

        $this->showDeleteModal = false;
        $this->deleteId        = null;
        session()->flash('success', 'Checklist deleted successfully.');
    }

    // ─── Query Builder ─────────────────────────────────────────────────────────

    private function buildQuery()
    {
        $query = FlowerChecklist::with('user');

        // Role-based scoping: staff see only their own records
        if (Auth::user()->isStaff()) {
            $query->where('user_id', Auth::id());
        }

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->condition) {
            $query->byCondition($this->condition);
        }

        if ($this->dateFrom || $this->dateTo) {
            $query->inDateRange($this->dateFrom ?: null, $this->dateTo ?: null);
        }

        if ($this->staffId && Auth::user()->isAdmin()) {
            $query->where('user_id', $this->staffId);
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $checklists = $this->buildQuery()->paginate(15);
        $staffList  = Auth::user()->isAdmin() ? User::orderBy('name')->get() : collect();

        return view('livewire.checklist-table', [
            'checklists' => $checklists,
            'staffList'  => $staffList,
        ])->layout('layouts.app');
    }
}
