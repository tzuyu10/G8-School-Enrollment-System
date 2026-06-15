<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectEnrollmentStatus extends Model
{
    protected $table = 'subject_enrollment_statuses';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code',
        'label',
        'color',
    ];

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class, 'status_id');
    }
}
