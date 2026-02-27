<?php

namespace App\Models;

use CodeIgniter\Model;

class DefectoInspeccionModel extends Model
{
    protected $table         = 'defectos_inspeccion';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id_inspeccion', 'id_tipo_defecto'];
    protected $useTimestamps = false;
}
