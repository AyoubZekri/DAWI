<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $table = 'municipalities';

    protected $fillable = [
        'name',
        'name_fr',
    ];


    public function clinics()
    {
        return $this->hasMany(Clinic::class, 'municipalitie_id');
    }
}

