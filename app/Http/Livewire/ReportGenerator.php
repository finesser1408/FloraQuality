<?php

namespace App\Http\Livewire;

use App\Models\FlowerChecklist;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Livewire\Component;

class ReportGenerator extends Component
{
    public string $reportType = 'daily';   // daily | weekly | monthly | condition | user_activity
    public string $exportFormat = 'csv';   // csv | pdf
    public string $dateFrom = '';
    public string $dateTo   = '';

    public function mount(): void
    {
        $this->authorize('export', \App\Models\FlowerChecklist::class);
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function getReportDataProperty()
    {
        $query = FlowerChecklist::with('user')
            ->inDateRange($this->dateFrom ?: null, $this->dateTo ?: null);

        return match ($this->reportType) {
            'condition' => $query->selectRaw('condition, count(*) as total')
                                 ->groupBy('condition')
                                 ->get(),
            'user_activity' => $query->selectRaw('user_id, count(*) as total')
                                     ->with('user')
                                     ->groupBy('user_id')
                                     ->get(),
            default => $query->latest('check_date')->get(),
        };
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('export', FlowerChecklist::class);

        $data = FlowerChecklist::with('user')
            ->inDateRange($this->dateFrom ?: null, $this->dateTo ?: null)
            ->latest('check_date')
            ->get();

        AuditService::log('exported', 'FlowerChecklist', null,
            "Exported CSV report ({$this->reportType}) from {$this->dateFrom} to {$this->dateTo}");

        $filename = "flower-checklist-report-" . now()->format('Y-m-d') . ".csv";

        return Response::streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Time', 'Condition', 'Remarks', 'Staff', 'Created At']);

            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->check_date->toDateString(),
                    $row->check_time,
                    ucfirst($row->condition),
                    $row->remarks ?? '',
                    $row->user->name ?? 'N/A',
                    $row->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        return view('livewire.report-generator', [
            'reportData' => $this->reportData,
        ])->layout('layouts.app');
    }
}
