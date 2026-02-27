<?php

namespace App\Models;

use CodeIgniter\Model;

class TurnoModel extends Model
{
    protected $table         = 'turno';
    protected $primaryKey    = 'id_turno';
    protected $allowedFields = ['id_empresa', 'nombre_turno', 'hora_inicio', 'hora_fin', 'descripcion', 'estado'];
    protected $useTimestamps = false;
}
