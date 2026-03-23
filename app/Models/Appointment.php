<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'client_id',
        'specialist_id',
        'service_id',
        'start_time',
        'end_time',
        'status',
        'delay_minutes'
    ];
}
