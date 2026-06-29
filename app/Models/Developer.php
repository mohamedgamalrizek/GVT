<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Developer extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'slug', 'logo_path', 'website_url', 'headquarters', 'founded_year',
        'certification_status', 'rating_score', 'summary', 'evaluation_summary', 'strengths',
        'risk_flags', 'certified_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'strengths' => 'array',
            'risk_flags' => 'array',
            'certified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function theses(): HasMany
    {
        return $this->hasMany(InvestmentThesis::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function certificationHistories(): HasMany
    {
        return $this->hasMany(CertificationHistory::class);
    }
}
