<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'type',
        'user_id',
        'title',
        'contact'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
