<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoinRep extends Model
{
    use SoftDeletes;

    protected $table = 'join_rep';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'joinee',
        'experience',
        'licensed_area',
        'practice_areas',
        'reference',
        'about',
        'is_contact',
        'perceive',
        'join_rep_data',
        'board_name'

    ];

   
}
