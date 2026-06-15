<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Section extends Model
{
    protected $table = 'sections';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'semester_id',
        'program_id',
        'year_level_id',
        'adviser_id',
        'name',
        'max_capacity',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function yearLevel(): BelongsTo
    {
        return $this->belongsTo(YearLevel::class, 'year_level_id');
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'adviser_id');
    }

    public function subjectOfferings(): HasMany
    {
        return $this->hasMany(SubjectOffering::class, 'section_id');
    }

    public function sectionAssignments(): HasMany
    {
        return $this->hasMany(SectionAssignment::class, 'section_id');
    }
}
