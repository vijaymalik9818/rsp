<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginInformation extends Model
{
    use HasFactory;

    protected $table = 'login_information'; // Specify the table name if it's different from the default naming convention

    protected $fillable = ['user_id', 'login_timestamp']; // Fillable fields to prevent mass assignment errors

    // Define the relationship with the User model (assuming the user model is named 'Lead')
    public function user()
    {
        return $this->belongsTo(Lead::class, 'user_id');
    }
}
