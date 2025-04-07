<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        "email",
        "user_id",
        "municipalities_id",
        "pharm_name_fr",
        'address',
        'latitude',
        'longitude',
        'phone',
        'type',
        'register',
        'Statue',
        'cover_image',
        'profile_image',
    ];
    public function getStatusAttribute()
    {
        $now = Carbon::now()->format('H:i:s');
        return ($this->active_from <= $now && $this->active_to >= $now);
    }

    public function schedules()
    {
        return $this->hasMany(clinic_schedules::class, "clinic_id");
    }


    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'clinic_id');
    }


    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipalitie_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


}
