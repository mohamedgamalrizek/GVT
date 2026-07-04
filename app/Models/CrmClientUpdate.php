<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmClientUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_client_id',
        'user_id',
        'type',
        'status_from',
        'status_to',
        'body',
        'contacted_at',
        'next_follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
