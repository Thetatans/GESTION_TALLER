<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
    protected $table         = 'empresa';
    protected $primaryKey    = 'id_empresa';
    protected $allowedFields = ['nombre', 'nit', 'direccion', 'telefono', 'email', 'fecha_creacion', 'estado'];
    protected $useTimestamps = false;
}
