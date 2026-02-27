<?php

namespace App\Models;

use CodeIgniter\Model;

class InspeccionCalidadModel extends Model
{
    protected $table         = 'inspecciones_calidad';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['resultado', 'cantidad_aprobada', 'cantidad_rechazada', 'observaciones', 'id_tarea'];
    protected $useTimestamps = true;
}
