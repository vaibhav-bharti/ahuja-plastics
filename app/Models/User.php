<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

        'employee_code',

        'name',

        'phone',

        'email',

        'password',

        'role',

        'module_access',

        'department',

        'joining_date',

        'salary_type',

        'salary',

        'is_active',

        'remarks',

    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [

        'password',

        'remember_token',

    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'joining_date' => 'date',

            'is_active' => 'boolean',

            'module_access' => 'array',

            'last_login_at' => 'datetime',

            'password' => 'hashed',

        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin' || $this->hasRole('Admin');
    }

    public function isEmployee(): bool
    {
        return ! $this->isAdmin();
    }

    public function hasModuleAccess(string $module): bool
    {
        return $this->isAdmin()
            || in_array($module, $this->module_access ?: ['production'], true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->role !== 'Worker';
    }

    /**
     * Auto Generate Employee Code
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {

            if (blank($user->employee_code)) {

                $nextId = (static::max('id') ?? 0) + 1;

                $user->employee_code = 'EMP' . str_pad(
                    $nextId,
                    5,
                    '0',
                    STR_PAD_LEFT
                );
            }

            $user->is_active ??= true;

            if (! $user->isAdmin() && blank($user->module_access)) {
                $user->module_access = ['production'];
            }

        });
    }

    public function createdProductionJobs()
    {
        return $this->hasMany(ProductionJob::class, 'created_by');
    }

    public function createdProductionJobReturns()
    {
        return $this->hasMany(ProductionJobReturn::class, 'created_by');
    }
}
