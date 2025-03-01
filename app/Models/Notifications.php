<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'title',
        'content',
        'is_read',
        'user_id',
    ];

    public function users()
    {
        return $this->belongsTo('App\Models\User');
    }

    // public function getUserName()
    // {
    //     $user = $this->user;
    //     return $user->role == 2 ? $user->normalUser->name : $user->pharmUser->name;
    // }
}
