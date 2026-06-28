<?php

namespace App\Http\Livewire;

use App\Models\FlowerChecklist;
use App\Services\AuditService;
use App\Services\SignatureService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChecklistForm extends Component
{
    // ─── Form Fields ───────────────────────────────────────────────────────────

    public ?int $checklistId = null;   // null = create mode

    public string $checkDate  = '';
    public string $checkTime  = '';
    public string $condition  = '';
    public string $remarks    = '';

    // Base64 data-URL strings received from JS signature pads
    public string $staffSignatureData    = '';
    public string $supplierSignatureData = '';

    // Existing saved signature paths (shown when editing)
    public ?string $existingStaffSig    = null;
    public ?string $existingSupplierSig = null;

    protected SignatureService $sigService;

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'checkDate'  => 'required|date',
            'checkTime'  => 'required',
            'condition'  => 'required|in:good,average,bad',
            'remarks'    => 'nullable|string|max:2000',
        ];
    }

    protected $messages = [
        'checkDate.required' => 'Inspection date is required.',
        'checkTime.required' => 'Inspection time is required.',
        'condition.required' => 'Please select a flower condition.',
        'condition.in'       => 'Invalid condition selected.',
    ];

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    public function boot(SignatureService $sigService): void
    {
        $this->sigService = $sigService;
    }

    public function mount(?int $id = null): void
    {
        $this->checkDate = today()->toDateString();
        $this->checkTime = now()->format('H:i');

        if ($id) {
            $checklist = FlowerChecklist::findOrFail($id);
            $this->authorize('update', $checklist);

            $this->checklistId        = $checklist->id;
            $this->checkDate          = $checklist->check_date->toDateString();
            $this->checkTime          = $checklist->check_time;
            $this->condition          = $checklist->condition;
            $this->remarks            = $checklist->remarks ?? '';
            $this->existingStaffSig   = $checklist->staff_signature;
            $this->existingSupplierSig = $checklist->supplier_signature;
        }
    }

    // ─── Actions ───────────────────────────────────────────────────────────────

    /**
     * Called by Alpine when signature pads emit data.
     */
    public function updateStaffSignature(string $data): void
    {
        $this->staffSignatureData = $data;
    }

    public function updateSupplierSignature(string $data): void
    {
        $this->supplierSignatureData = $data;
    }

    public function save(): void
    {
        $this->validate();

        // Resolve signatures
        $staffSig    = $this->sigService->store($this->staffSignatureData ?: null, 'staff')
                       ?? $this->existingStaffSig;
        $supplierSig = $this->sigService->store($this->supplierSignatureData ?: null, 'supplier')
                       ?? $this->existingSupplierSig;

        $data = [
            'check_date'         => $this->checkDate,
            'check_time'         => $this->checkTime,
            'condition'          => $this->condition,
            'remarks'            => $this->remarks ?: null,
            'staff_signature'    => $staffSig,
            'supplier_signature' => $supplierSig,
            'user_id'            => Auth::id(),
        ];

        if ($this->checklistId) {
            $checklist = FlowerChecklist::findOrFail($this->checklistId);
            $checklist->update($data);
            AuditService::log('updated', 'FlowerChecklist', $checklist->id,
                "Updated checklist #{$checklist->id}");
            session()->flash('success', 'Checklist updated successfully.');
        } else {
            $checklist = FlowerChecklist::create($data);
            AuditService::log('created', 'FlowerChecklist', $checklist->id,
                "Created checklist #{$checklist->id}");
            session()->flash('success', 'Checklist saved successfully.');
        }

        $this->redirect(route('checklists.index'));
    }

    public function clearForm(): void
    {
        $this->reset(['staffSignatureData', 'supplierSignatureData', 'remarks', 'condition']);
        $this->checkDate = today()->toDateString();
        $this->checkTime = now()->format('H:i');
        $this->dispatch('clear-signatures');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.checklist-form')->layout('layouts.app');
    }
}
