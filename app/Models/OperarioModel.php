<?php

namespace App\Models;

use CodeIgniter\Model;

class OperarioModel extends Model
{
    protected $table         = 'operario';
    protected $primaryKey    = 'id_operario';
    protected $allowedFields = ['id_usuario', 'id_turno', 'habilidades', 'certificaciones', 'nivel_experiencia', 'fecha_ingreso', 'estado'];
    protected $useTimestamps = false;
}
