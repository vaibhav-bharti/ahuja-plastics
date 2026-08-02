<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
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

            'last_login_at' => 'datetime',

            'password' => 'hashed',

        ];
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

        });
    }
}