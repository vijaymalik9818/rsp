<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingAppointment extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'listing_appointments';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'property_address',
        'community',
        'approx_age_of_property',
        'approx_size_of_property',
        'style_of_property',
        'no_of_bedrooms',
        'basement_development',
        'parking',
        'interest',
        'additional_information',
        'listing_appointments_data',
        'no_of_bathrooms'
    ];
}
