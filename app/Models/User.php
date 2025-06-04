<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Add this relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Check if user has a specific role
    public function hasRole($role)
    {
        if (is_string($role)) {

            return $this->roles->contains(function($value) use ($role) {
                return strtolower($value->name) === strtolower($role);
            });
        }

        return !! $role->intersect($this->roles)->count();
    }

    // Assign role to user
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrCreate(['name' => $role]);
        }

        $this->roles()->sync($role, false);
        return $this;
    }

    // Add this method to fix the error
    public function getRoleNames()
    {
        return $this->roles->pluck('name')->toArray();
    }
}
