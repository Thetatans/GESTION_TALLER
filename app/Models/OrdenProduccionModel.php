<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdenProduccionModel extends Model
{
    protected $table         = 'orden_produccion';
    protected $primaryKey    = 'id_orden';
    protected $allowedFields = [
        'id_empresa', 'codigo_orden', 'descripcion_producto', 'cantidad_solicitada',
        'fecha_limite', 'prioridad', 'estado', 'especificaciones_tecnicas',
        'observaciones', 'fecha_completada', 'fecha_creacion',
    ];
    protected $useTimestamps = false;

    protected $validationRules = [
        'descripcion_producto' => 'required|min_length[3]|max_length[500]',
        'cantidad_solicitada'  => 'required|integer|greater_than[0]',
        'fecha_limite'         => 'required|valid_date',
        'prioridad'            => 'required|in_list[baja,media,alta,urgente]',
        'estado'               => 'required|in_list[pendiente,en proceso,pausada,completada,cancelada]',
        'id_empresa'           => 'required|integer',
    ];

    protected $validationMessages = [
        'descripcion_producto' => [
            'required'   => 'La descripcion del producto es obligatoria.',
            'min_length' => 'La descripcion debe tener al menos 3 caracteres.',
            'max_length' => 'La descripcion no puede exceder 500 caracteres.',
        ],
        'cantidad_solicitada' => [
            'required'     => 'La cantidad solicitada es obligatoria.',
            'integer'      => 'La cantidad debe ser un numero entero.',
            'greater_than' => 'La cantidad debe ser mayor a 0.',
        ],
        'fecha_limite' => [
            'required'   => 'La fecha limite es obligatoria.',
            'valid_date' => 'La fecha limite no es valida.',
        ],
        'prioridad' => [
            'required' => 'La prioridad es obligatoria.',
            'in_list'  => 'La prioridad debe ser baja, media, alta o urgente.',
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list'  => 'El estado seleccionado no es valido.',
        ],
        'id_empresa' => [
            'required' => 'La empresa es obligatoria.',
            'integer'  => 'La empresa seleccionada no es valida.',
        ],
    ];
}
