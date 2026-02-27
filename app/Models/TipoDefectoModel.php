<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoDefectoModel extends Model
{
    protected $table         = 'tipos_defecto';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['descripcion'];
    protected $useTimestamps = true;
}
