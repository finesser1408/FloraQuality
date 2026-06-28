<?php

namespace App\Http\Livewire;

use App\Models\FlowerChecklist;
use Livewire\Component;

class Dashboard extends Component
{
    // ─── Computed Stats ────────────────────────────────────────────────────────

    public function getTotalProperty(): int
    {
        return FlowerChecklist::count();
    }

    public function getGoodCountProperty(): int
    {
        return FlowerChecklist::byCondition('good')->count();
    }

    public function getAverageCountProperty(): int
    {
        return FlowerChecklist::byCondition('average')->count();
    }

    public function getBadCountProperty(): int
    {
        return FlowerChecklist::byCondition('bad')->count();
    }

    public function getTodayCountProperty(): int
    {
        return FlowerChecklist::today()->count();
    }

    public function getRecentProperty()
    {
        return FlowerChecklist::with('user')
            ->latest()
            ->take(8)
            ->get();
    }

    /**
     * Monthly inspection counts for the past 6 months (for chart).
     * Returns an array keyed by "Mon YYYY" label.
     */
    public function getMonthlyStatsProperty(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->format('M Y');
            $months[$label] = FlowerChecklist::whereYear('check_date', $date->year)
                ->whereMonth('check_date', $date->month)
                ->count();
        }
        return $months;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'total'        => $this->total,
            'goodCount'    => $this->goodCount,
            'averageCount' => $this->averageCount,
            'badCount'     => $this->badCount,
            'todayCount'   => $this->todayCount,
            'recent'       => $this->recent,
            'monthlyStats' => $this->monthlyStats,
        ])->layout('layouts.app');
    }
}
