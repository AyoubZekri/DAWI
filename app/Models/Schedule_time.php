<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule_time extends Model
{
    use HasFactory;



    protected $fillable = ['clinic_id', 'day', 'opening_time', 'closing_time'];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
