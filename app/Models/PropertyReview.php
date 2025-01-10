<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyReview extends Model
{
    protected $fillable = [
        'review_from',
        'email',
        'address',
        'rating',
        'review',
        'listing_id'
    ];
}
