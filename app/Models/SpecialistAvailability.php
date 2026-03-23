<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialistAvailability extends Model
{
    protected $table = 'specialist_availabilities';
    protected $fillable = [
        'specialist_id',
        'date',
        'start_time',
        'end_time'
    ];
}
