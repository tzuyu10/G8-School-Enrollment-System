<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EnrollmentApplication extends Model
{
    protected $table      = 'enrollment_applications';
    protected $primaryKey = 'id';
    protected $keyType    = 'string';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'student_id',
        'semester_id',
        'program_id',
        'year_level_id',
        'status_id',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'prior_subject_grades',
        'prior_subject_grades_verified',
        'tor_document_path',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'prior_subject_grades' => 'array',
        'prior_subject_grades_verified' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'student_id');
    }

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

    public function status(): BelongsTo
    {
        return $this->belongsTo(ApplicationStatus::class, 'status_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'reviewed_by');
    }

    public function sectionAssignment(): HasOne
    {
        return $this->hasOne(SectionAssignment::class, 'enrollment_id');
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class, 'enrollment_id');
    }
}
