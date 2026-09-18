<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'assigned' => 'To Be Done',
            'accepted', 'in_progress' => 'In Progress',
            'completed' => 'Done',
            'cannot_do' => "Can't Do",
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'assigned' => 'bg-blue-100 text-blue-700 ring-blue-600',
            'accepted' => 'bg-purple-100 text-purple-700 ring-purple-600',
            'in_progress' => 'bg-amber-100 text-amber-700 ring-amber-600',
            'completed' => 'bg-green-100 text-green-700 ring-green-600',
            'cannot_do' => 'bg-red-100 text-red-700 ring-red-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function getNextStatusAttribute(): ?string
    {
        return match ($this->status) {
            'assigned' => 'accepted',
            'accepted' => 'in_progress',
            'in_progress' => 'completed',
            default => null,
        };
    }

    public function getNextStatusLabelAttribute(): ?string
    {
        $next = $this->next_status;
        if (!$next) return null;
        $labels = [
            'accepted' => 'Mark In Progress',
            'in_progress' => 'Mark Done',
        ];
        return $labels[$next] ?? 'Advance';
    }

    public function getPipelineStageAttribute(): int
    {
        return match ($this->status) {
            'assigned' => 1,
            'accepted', 'in_progress' => 2,
            'completed' => 3,
            default => 0,
        };
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}