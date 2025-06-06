<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'report',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'report_id');
    }
}
