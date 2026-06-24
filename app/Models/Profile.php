<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Authenticatable
{
    use HasApiTokens;

    protected $table      = 'profiles';
    protected $primaryKey = 'id';
    protected $keyType    = 'string';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id',
        'role_id',
        'status_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password',
    ];

    protected $hidden = ['password'];

    // ── Computed full name ────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ])->filter()->implode(' ');
    }

    // ── Relationships ─────────────────────────────────────────
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProfileStatus::class, 'status_id');
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'profile_id');
    }

    public function enrollmentApplications(): HasMany
    {
        return $this->hasMany(EnrollmentApplication::class, 'student_id');
    }

    public function advisedSections(): HasMany
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }

    public function subjectOfferings(): HasMany
    {
        return $this->hasMany(SubjectOffering::class, 'faculty_id');
    }

    public function reviewedApplications(): HasMany
    {
        return $this->hasMany(EnrollmentApplication::class, 'reviewed_by');
    }

    public function sectionAssignments(): HasMany
    {
        return $this->hasMany(SectionAssignment::class, 'assigned_by');
    }

    public function gradedSubjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class, 'graded_by');
    }
}
