<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;
    protected $table = 'saved_search';
 
    
    protected $fillable = [
    'user_id',
    'title',
    'duration',
    'sent_at',
    'city',
    'min_price',
    'max_price',
    'beds',
    'bath',
    'community',
    'property_type',
    'min_sqft',
    'max_sqft',
    'min_acres',
    'max_acres',
    'min_yearbuilt',
    'max_yearbuilt',
    'furnishedCheckbox',
    'petsCheckbox',
    'fireplace',
    'onegarage',
    'twogarage',
    'threegarage',
    'onestory',
    'twostories',
    'threestories',
    'deck',
    'basement',
    'airconditioning',
    'allColumns',
    'just_listed',
    'frontporch',
    'patio',
    'lake',
    'playground',
    'streetlights',
    'pool',
    'laundry',
    'gazebo',
    'clubhouse',
];
    protected $casts = [
        'allColumns' => 'json', 
        'sent_at' => 'datetime',
    ];
}
