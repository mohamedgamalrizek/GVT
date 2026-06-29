<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid', 'developer_id', 'investment_thesis_id', 'project_name', 'slug', 'location',
        'city', 'asset_type', 'risk_level', 'certification_status', 'expected_yield_range',
        'expected_appreciation_range', 'thesis_summary', 'investment_rationale',
        'location_intelligence', 'financial_indicators', 'gallery', 'documents', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'financial_indicators' => 'array',
            'gallery' => 'array',
            'documents' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(InvestmentThesis::class, 'investment_thesis_id');
    }
}
