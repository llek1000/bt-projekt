<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conference_year_id',
    ];

    /**
     * Get the user (editor) assigned.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conference year.
     */
    public function conferenceYear()
    {
        return $this->belongsTo(ConferenceYear::class);
    }
}
