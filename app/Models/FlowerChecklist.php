<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FlowerChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_date',
        'check_time',
        'condition',
        'remarks',
        'staff_signature',
        'supplier_signature',
        'user_id',
    ];

    protected $casts = [
        'check_date' => 'date',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('check_date', today());
    }

    public function scopeByCondition(Builder $query, string $condition): Builder
    {
        return $query->where('condition', $condition);
    }

    public function scopeInDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('check_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('check_date', '<=', $to);
        }
        return $query;
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('condition', 'like', "%{$term}%")
              ->orWhere('remarks', 'like', "%{$term}%")
              ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
        });
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Human-readable condition label.
     */
    public function getConditionLabelAttribute(): string
    {
        return ucfirst($this->condition);
    }

    /**
     * Tailwind badge colour class based on condition.
     */
    public function getConditionColourAttribute(): string
    {
        return match ($this->condition) {
            'good'    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'average' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'bad'     => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default   => 'bg-gray-100 text-gray-800',
        };
    }
}
