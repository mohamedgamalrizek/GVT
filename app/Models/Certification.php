<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['developer_id', 'status', 'score', 'issued_at', 'expires_at', 'methodology_summary', 'criteria'];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
