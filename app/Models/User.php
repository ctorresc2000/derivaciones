<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'tipo_profesional_id'
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // 1. Relación con el Tipo (Cargo)
    public function tipoProfesional()
    {
        return $this->belongsTo(Tipoprofesional::class, 'tipo_profesional_id');
    }

    public function esTipo($nombre)
    {
        // Asumiendo que User tiene una relación 'tipoProfesional'
        return $this->tipoProfesional?->departamento === $nombre;
    }

    // 2. Las derivaciones que este usuario HA CREADO (como profesor)
    public function derivacionesCreadas()
    {
        return $this->hasMany(Derivarestudiante::class, 'user_id');
    }

    // 3. Las derivaciones que a este usuario LE HAN ASIGNADO (como profesional)
    public function derivacionesAsignadas()
    {
        return $this->hasMany(Derivarestudiante::class, 'profesional_derivado_id');
    }
}
