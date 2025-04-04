<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day',
        'opening_time',
        'closing_time',
    ];

   
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
