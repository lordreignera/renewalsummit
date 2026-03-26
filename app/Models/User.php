<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
        ];
    }

    // ── Role helpers ────────────────────────────────────────────

    public const ROLES = [
        'super_admin' => 'Super Admin',
        'finance'     => 'Finance',
        'registrar'   => 'Registrar',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isRegistrar(): bool
    {
        return $this->role === 'registrar';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    // ── Permission helpers ───────────────────────────────────────

    /** Cached permission slugs for the current user's role */
    private ?array $_permissionCache = null;

    public function roleModel(): ?Role
    {
        return Role::where('name', $this->role)->first();
    }

    public function roleDisplayName(): string
    {
        return Role::where('name', $this->role)->value('display_name')
            ?? static::ROLES[$this->role]
            ?? $this->role;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->_permissionCache === null) {
            $role = Role::where('name', $this->role)->with('permissions')->first();
            $this->_permissionCache = $role?->permissions->pluck('name')->all() ?? [];
        }

        return in_array($permission, $this->_permissionCache, true);
    }
}
