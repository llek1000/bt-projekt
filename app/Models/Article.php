<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'conference_year_id',
        'author_name',
    ];

    /**
     * Get the conference year that owns the article.
     */
    public function conferenceYear()
    {
        return $this->belongsTo(ConferenceYear::class);
    }
}
