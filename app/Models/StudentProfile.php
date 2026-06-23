<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $table      = 'student_profiles';
    protected $primaryKey = 'id';
    protected $keyType    = 'string';
    public $incrementing  = false;
    public $timestamps    = true;

    protected $fillable = [
        'profile_id',
        'student_number',
        'student_type',
        'birthdate',
        'gender',
        'civil_status',
        'nationality',
        'religion',
        'contact_number',
        'permanent_address',
        'current_address',
        // Father
        'father_first_name',
        'father_middle_name',
        'father_last_name',
        'father_suffix',
        // Mother
        'mother_first_name',
        'mother_middle_name',
        'mother_last_name',
        'mother_suffix',
        // Guardian
        'guardian_first_name',
        'guardian_middle_name',
        'guardian_last_name',
        'guardian_suffix',
        'guardian_relation',
        'guardian_contact',
        // Academic
        'previous_school',
        'previous_program',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    protected const REQUIRED_FIELDS = [
        'student_type',
        'birthdate',
        'gender',
        'civil_status',
        'contact_number',
        'permanent_address',
        'guardian_first_name',
        'guardian_last_name',
        'guardian_relation',
        'guardian_contact',
    ];

    // ── Computed names ────────────────────────────────────────
    public function getFatherNameAttribute(): string
    {
        return collect(['father_first_name','father_middle_name','father_last_name','father_suffix'])
            ->map(fn($f) => $this->$f)->filter()->implode(' ');
    }

    public function getMotherNameAttribute(): string
    {
        return collect(['mother_first_name','mother_middle_name','mother_last_name','mother_suffix'])
            ->map(fn($f) => $this->$f)->filter()->implode(' ');
    }

    public function getGuardianNameAttribute(): string
    {
        return collect(['guardian_first_name','guardian_middle_name','guardian_last_name','guardian_suffix'])
            ->map(fn($f) => $this->$f)->filter()->implode(' ');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function getProfileStatus(): array
    {
        $missing = collect(self::REQUIRED_FIELDS)
            ->reject(fn($field) => filled($this->$field))
            ->values()
            ->all();

        $total = count(self::REQUIRED_FIELDS);
        $filled = $total - count($missing);
        $isComplete = empty($missing);

        return [
            'is_complete'    => $isComplete,
            'label'          => $isComplete ? 'Complete' : 'Incomplete',
            'percentage'     => $total > 0 ? (int) round(($filled / $total) * 100) : 0,
            'missing_fields' => $missing,
        ];
    }

}