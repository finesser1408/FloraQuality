<?php

namespace App\Http\Livewire;

use App\Models\FlowerChecklist;
use Livewire\Component;

class ChecklistView extends Component
{
    public FlowerChecklist $checklist;

    public function mount(int $id): void
    {
        $this->checklist = FlowerChecklist::with('user')->findOrFail($id);
        $this->authorize('view', $this->checklist);
    }

    public function render()
    {
        return view('livewire.checklist-view')->layout('layouts.app');
    }
}
