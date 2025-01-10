<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Lead extends Model implements Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory, AuthenticatableTrait;

    protected $table = 'leads';

    protected $fillable = [
        'name',
        'type',
        'email',
        'password',
        'phone',
        'about',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'status',
        'profile_picture',
        'login_type',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
