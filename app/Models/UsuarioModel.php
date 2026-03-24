<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuario';
    protected $primaryKey    = 'id_usuario';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_empresa', 'nombre_completo', 'email', 'password_hash',
        'telefono', 'documento', 'estado', 'rol',
        'fecha_creacion', 'ultimo_acceso',
    ];

    protected $validationRules = [
        'nombre_completo' => 'required|min_length[3]|max_length[255]',
        'email'           => 'required|valid_email|max_length[255]',
        'rol'             => 'required|in_list[admin,supervisor,operario]',
        'estado'          => 'required|in_list[activo,inactivo,suspendido]',
    ];

    // ── Autenticación ────────────────────────────────────────────

    /**
     * Busca un usuario activo por email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                    ->where('estado', 'activo')
                    ->first();
    }

    /**
     * Registra el último acceso del usuario.
     */
    public function updateLastAccess(int $id): void
    {
        $this->update($id, ['ultimo_acceso' => date('Y-m-d H:i:s')]);
    }

    // ── Consultas ────────────────────────────────────────────────

    /**
     * Lista todos los usuarios con nombre de empresa.
     */
    public function listarConEmpresa(): array
    {
        return $this->db->table('usuario u')
            ->select('u.*, e.nombre AS empresa_nombre')
            ->join('empresa e', 'e.id_empresa = u.id_empresa', 'left')
            ->orderBy('u.id_usuario', 'DESC')
            ->get()
            ->getResultArray();
    }
}
