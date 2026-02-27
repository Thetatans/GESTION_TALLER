<?php

namespace App\Models;

use CodeIgniter\Model;

class AlmacenModel extends Model
{
    protected $table         = 'almacenes';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nombre', 'ubicacion', 'capacidad'];
    protected $useTimestamps = true;
}
