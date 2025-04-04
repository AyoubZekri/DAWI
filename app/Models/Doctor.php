<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'specialties_id',
        'name',
        'email',
        'phone',
    ];

    /**
     * Get the user associated with the doctor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clinic that the doctor belongs to.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the specialty of the doctor.
     */
    public function specialty()
    {
        return $this->belongsTo(specialties::class, 'specialties_id');
    }
}

