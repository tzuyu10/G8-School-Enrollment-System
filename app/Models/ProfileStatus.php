<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileStatus extends Model
{
    protected $table = 'profile_statuses';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code',
        'label',
        'color',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'status_id');
    }
}
