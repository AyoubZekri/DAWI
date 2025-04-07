<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role_name'];

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'roles_user', 'roles_id', 'user_id');
    }

}
