<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all articles for this conference year
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get all editor assignments for this conference year
     */
    public function editorAssignments(): HasMany
    {
        return $this->hasMany(EditorAssignment::class);
    }

    /**
     * Scope to get only active conference years
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full name of the conference year
     */
    public function getFullNameAttribute(): string
    {
        return $this->semester . ' ' . $this->year;
    }
}
