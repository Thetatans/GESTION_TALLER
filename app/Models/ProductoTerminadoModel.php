<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoTerminadoModel extends Model
{
    protected $table         = 'productos_terminados';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['cantidad', 'fecha_ingreso', 'id_inspeccion', 'id_almacen'];
    protected $useTimestamps = true;
}
