<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationStatus extends Model
{
    protected $table = 'application_statuses';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code',
        'label',
        'color',
    ];

    public function enrollmentApplications(): HasMany
    {
        return $this->hasMany(EnrollmentApplication::class, 'status_id');
    }
}
