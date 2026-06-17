<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectEnrollment extends Model
{
    protected $table = 'subject_enrollments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'enrollment_id',
        'subject_offering_id',
        'status_id',
        'grade',
        'remarks',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    public function enrollmentApplication(): BelongsTo
    {
        return $this->belongsTo(EnrollmentApplication::class, 'enrollment_id');
    }

    public function subjectOffering(): BelongsTo
    {
        return $this->belongsTo(SubjectOffering::class, 'subject_offering_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SubjectEnrollmentStatus::class, 'status_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'graded_by');
    }

    public function getGradeRemarkAttribute(): string
    {
        if ($this->grade === null) {
            return 'Not encoded';
        }

        return (float) $this->grade <= 3.00 ? 'Passed' : 'Failed';
    }
}
