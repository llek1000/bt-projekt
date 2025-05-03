<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConferenceYear extends Model
{
    protected $fillable = ['year', 'description'];

    public function editors()
    {
        return $this->belongsToMany(User::class, 'editor_conference_year', 'conference_year_id', 'editor_id');
    }

    public function subpages()
    {
        return $this->hasMany(Subpage::class);
    }
}