<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subpage extends Model
{
    protected $fillable = ['title', 'content', 'conference_year_id'];

    public function conferenceYear()
    {
        return $this->belongsTo(ConferenceYear::class);
    }
}