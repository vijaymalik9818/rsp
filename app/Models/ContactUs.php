<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;

    protected $table = 'contact_us';
    protected $fillable = ['first_name', 'last_name', 'phone', 'email', 'time', 'comment', 'contact_data','page_name','role'];

}
