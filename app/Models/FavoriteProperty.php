<?php

// 1. FavoriteProperty Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteProperty extends Model
{
    use HasFactory;
    protected $table = 'favorite_properties';
    protected $fillable = ['user_id', 'property_id'];

  
}
