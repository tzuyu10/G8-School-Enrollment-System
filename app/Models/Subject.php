<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'code',
        'title',
        'units',
        'type',
        'prerequisite_codes',
    ];

    public function getPrerequisitesAttribute(): array
    {
        return collect(explode(',', (string) $this->prerequisite_codes))
            ->map(fn ($code) => trim($code))
            ->filter()
            ->values()
            ->all();
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function subjectOfferings(): HasMany
    {
        return $this->hasMany(SubjectOffering::class, 'subject_id');
    }
}
