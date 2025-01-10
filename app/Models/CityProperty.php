<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityProperty extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table = 'city_properties';
    protected $fillable = ['city_name','slug_url','meta_title','image','status'];

}
   