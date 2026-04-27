<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'permisos',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'permisos'          => 'array',
        ];
    }

    // ── Helpers de rol ──────────────────────────────────────────────────────

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * Verifica si el usuario tiene un permiso.
     * Los admins tienen todos los permisos automáticamente.
     */
    public function tienePermiso(string $permiso): bool
    {
        if ($this->esAdmin()) return true;

        return in_array($permiso, $this->permisos ?? []);
    }

    // ── Lista de permisos disponibles ───────────────────────────────────────

    public static function permisosDisponibles(): array
    {
        return [
            'dashboard'       => 'Ver dashboard',
            'pedidos.editar'  => 'Crear y editar pedidos',
            'precios.editar'  => 'Editar precios de servicios y productos',
            'cortes'          => 'Ver y gestionar cortes de caja',
            'configuracion'   => 'Acceder a configuración',
            'usuarios'        => 'Gestionar usuarios y permisos',
        ];
    }
}
