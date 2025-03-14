<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class specialties extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }
}
