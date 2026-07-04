<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmClient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assigned_to_user_id',
        'created_by_user_id',
        'name',
        'email',
        'phone',
        'company',
        'investor_type',
        'status',
        'priority',
        'source',
        'budget_range',
        'preferred_market',
        'notes',
        'last_contacted_at',
        'next_follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'last_contacted_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
        ];
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CrmClientUpdate::class)->latest();
    }
}
