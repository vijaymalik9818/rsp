<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'review_from', 'review_to', 'review_feedback', 'rating', 'avg_rating','title','status'
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'review_from');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'review_to');
    }

}

