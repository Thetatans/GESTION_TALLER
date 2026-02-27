<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialEstadoModel extends Model
{
    protected $table         = 'historial_estado';
    protected $primaryKey    = 'id_historial';
    protected $allowedFields = [
        'tipo_entidad', 'id_entidad', 'estado_anterior', 'estado_nuevo',
        'responsable', 'observaciones', 'fecha_cambio',
    ];
    protected $useTimestamps = false;
}
