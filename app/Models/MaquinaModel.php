<?php

namespace App\Models;

use CodeIgniter\Model;

class MaquinaModel extends Model
{
    protected $table         = 'maquina';
    protected $primaryKey    = 'id_maquina';
    protected $allowedFields = ['id_empresa', 'nombre_maquina', 'tipo_maquina', 'codigo_maquina', 'modelo', 'marca', 'fecha_adquisicion', 'disponibilidad', 'ubicacion', 'especificaciones', 'fecha_creacion'];
    protected $useTimestamps = false;
}
