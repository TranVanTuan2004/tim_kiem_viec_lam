<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'bio',
        'is_active',
        'last_login_at',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    // Relationships
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    public function candidateProfile()
    {
        return $this->hasOne(CandidateProfile::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // roles() relationship is provided by Spatie's HasRoles trait
    // No need to define it manually - Spatie uses model_has_roles table

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // Helper methods - compatible with both slug and name
    public function isCandidate(): bool
    {
        // Spatie hasRole() checks by 'name', so we need to use the role name
        return $this->hasRole('Candidate');
    }

    public function isEmployer(): bool
    {
        return $this->hasRole('Employer');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    // Note: The following methods are provided by Spatie's HasRoles trait:
    // - hasRole($role) - checks by name
    // - hasAnyRole($roles)
    // - hasAllRoles($roles)
    // - assignRole($role)
    // - removeRole($role)
    // - syncRoles($roles)
    // - getRoleNames()
    // - givePermissionTo($permission)
    // - revokePermissionTo($permission)
    // - can($permission)
}
