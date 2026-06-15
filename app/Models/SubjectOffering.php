<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectOffering extends Model
{
    protected $table = 'subject_offerings';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'section_id',
        'faculty_id',
        'room',
        'schedule',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'faculty_id');
    }

    public function subjectEnrollments(): HasMany
    {
        return $this->hasMany(SubjectEnrollment::class, 'subject_offering_id');
    }
}
