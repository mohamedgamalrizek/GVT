<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificationHistory extends Model
{
    use HasFactory;

    protected $fillable = ['developer_id', 'certification_id', 'from_status', 'to_status', 'event_type', 'rationale', 'effective_at'];

    protected function casts(): array
    {
        return [
            'effective_at' => 'datetime',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class);
    }
}
