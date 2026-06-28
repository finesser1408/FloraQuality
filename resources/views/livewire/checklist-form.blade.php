<div class="max-w-3xl mx-auto">
    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('checklists.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium mb-3 transition-opacity hover:opacity-70" style="color:var(--text-tertiary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Inspections
            </a>
            <h1 class="text-2xl font-extrabold tracking-tight" style="color:var(--text-primary);">
                {{ $this->checklistId ? 'Edit Inspection' : 'New Flower Quality Inspection' }}
            </h1>
            <p class="text-sm mt-1" style="color:var(--text-tertiary);">Record flower condition, observations, and collect signatures.</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="p-4 rounded-xl mb-6 flex items-start gap-3" style="background:rgba(220,38,38,0.07);border:1px solid rgba(220,38,38,0.18);">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
            <div>
                <p class="text-sm font-bold text-red-600 mb-1">Please fix the following errors:</p>
                <ul class="text-sm text-red-500 space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <form wire:submit.prevent="save" class="card overflow-hidden">

        {{-- Section 1: Basic Details --}}
        <div class="p-6 border-b" style="border-color:var(--surface-border);">
            <h2 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--text-tertiary);">Inspection Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Date <span class="text-red-500">*</span></label>
                    <input type="date" wire:model.defer="checkDate" class="form-input @error('checkDate') error @enderror">
                    @error('checkDate') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Time <span class="text-red-500">*</span></label>
                    <input type="time" wire:model.defer="checkTime" class="form-input @error('checkTime') error @enderror">
                    @error('checkTime') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Condition --}}
        <div class="p-6 border-b" style="border-color:var(--surface-border);">
            <h2 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--text-tertiary);">Flower Condition <span class="text-red-500">*</span></h2>
            <div class="condition-chips" x-data="{ selected: @entangle('condition').live }">
                @foreach([
                    ['value' => 'good',    'label' => 'Good',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',                              'desc' => 'Excellent quality'],
                    ['value' => 'average', 'label' => 'Average', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>',                                    'desc' => 'Acceptable quality'],
                    ['value' => 'bad',     'label' => 'Bad',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>', 'desc' => 'Reject / poor quality'],
                ] as $chip)
                    <button type="button"
                            wire:click="$set('condition', '{{ $chip['value'] }}')"
                            class="condition-chip {{ $condition === $chip['value'] ? 'selected-'.$chip['value'] : '' }}"
                            :class="selected === '{{ $chip['value'] }}' ? 'selected-{{ $chip['value'] }}' : ''">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $chip['icon'] !!}</svg>
                        <span class="font-bold">{{ $chip['label'] }}</span>
                        <span class="text-xs opacity-70">{{ $chip['desc'] }}</span>
                    </button>
                @endforeach
            </div>
            @error('condition') <p class="form-error mt-2">{{ $message }}</p> @enderror
        </div>

        {{-- Section 3: Remarks --}}
        <div class="p-6 border-b" style="border-color:var(--surface-border);">
            <h2 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--text-tertiary);">Remarks & Notes</h2>
            <textarea wire:model.defer="remarks" rows="4" placeholder="Describe the batch quality, variety, temperature, or any anomalies…" class="form-input resize-none"></textarea>
        </div>

        {{-- Section 4: Signatures --}}
        <div class="p-6 border-b" style="border-color:var(--surface-border);"
             x-data="signaturePads()" x-init="init()" @clear-signatures.window="clearAll()">
            <h2 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--text-tertiary);">Signatures</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Staff Signature --}}
                <div>
                    <p class="form-label mb-2">Staff Member Signature</p>
                    @if($existingStaffSig && !$this->staffSignatureData)
                        <div class="flex items-center gap-3 p-3 rounded-lg mb-3" style="background:var(--surface-1);border:1px solid var(--surface-border);">
                            <img src="{{ Storage::url($existingStaffSig) }}" class="max-h-10" alt="Current signature">
                            <span class="text-xs" style="color:var(--text-tertiary);">Draw below to replace</span>
                        </div>
                    @endif
                    <div class="sig-container" style="height:160px;">
                        <canvas id="staffCanvas" class="sig-canvas" x-ref="staffCanvas" style="height:160px;"></canvas>
                        <p class="sig-placeholder" x-show="staffEmpty">Sign here…</p>
                        <div class="sig-toolbar">
                            <button type="button" @click="clearStaff()" class="sig-btn">Clear</button>
                        </div>
                    </div>
                </div>

                {{-- Supplier Signature --}}
                <div>
                    <p class="form-label mb-2">Supplier Signature</p>
                    @if($existingSupplierSig && !$this->supplierSignatureData)
                        <div class="flex items-center gap-3 p-3 rounded-lg mb-3" style="background:var(--surface-1);border:1px solid var(--surface-border);">
                            <img src="{{ Storage::url($existingSupplierSig) }}" class="max-h-10" alt="Current signature">
                            <span class="text-xs" style="color:var(--text-tertiary);">Draw below to replace</span>
                        </div>
                    @endif
                    <div class="sig-container" style="height:160px;">
                        <canvas id="supplierCanvas" class="sig-canvas" x-ref="supplierCanvas" style="height:160px;"></canvas>
                        <p class="sig-placeholder" x-show="supplierEmpty">Sign here…</p>
                        <div class="sig-toolbar">
                            <button type="button" @click="clearSupplier()" class="sig-btn">Clear</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3" style="background:var(--surface-1);">
            <button type="button" wire:click="clearForm" class="btn btn-ghost btn-sm" style="color:var(--text-tertiary);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset
            </button>
            <div class="flex gap-3">
                <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" class="btn btn-primary">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $this->checklistId ? 'Save Changes' : 'Create Inspection' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Saving…
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function signaturePads() {
    return {
        staffPad: null, supplierPad: null,
        staffEmpty: true, supplierEmpty: true,
        init() {
            this.$nextTick(() => {
                const sc = this.$refs.staffCanvas;
                if (sc) {
                    sc.width = sc.offsetWidth;
                    this.staffPad = new SignaturePad(sc, { penColor: '#003580', minWidth: 1.5, maxWidth: 2.8 });
                    this.staffPad.addEventListener('endStroke', () => {
                        this.staffEmpty = this.staffPad.isEmpty();
                        @this.updateStaffSignature(this.staffPad.toDataURL());
                    });
                }
                const suc = this.$refs.supplierCanvas;
                if (suc) {
                    suc.width = suc.offsetWidth;
                    this.supplierPad = new SignaturePad(suc, { penColor: '#003580', minWidth: 1.5, maxWidth: 2.8 });
                    this.supplierPad.addEventListener('endStroke', () => {
                        this.supplierEmpty = this.supplierPad.isEmpty();
                        @this.updateSupplierSignature(this.supplierPad.toDataURL());
                    });
                }
            });
        },
        clearStaff() { this.staffPad?.clear(); this.staffEmpty = true; @this.updateStaffSignature(''); },
        clearSupplier() { this.supplierPad?.clear(); this.supplierEmpty = true; @this.updateSupplierSignature(''); },
        clearAll() { this.clearStaff(); this.clearSupplier(); }
    }
}
</script>
