<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaProduccionModel extends Model
{
    protected $table         = 'tarea_produccion';
    protected $primaryKey    = 'id_tarea';
    protected $allowedFields = [
        'id_orden', 'id_maquina', 'id_operario', 'nombre_tarea', 'descripcion',
        'fecha_inicio', 'fecha_fin', 'cantidad_producida', 'estado', 'observaciones',
    ];
    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre_tarea'        => 'required|min_length[3]|max_length[255]',
        'id_orden'            => 'required|integer',
        'id_maquina'          => 'required|integer',
        'id_operario'         => 'required|integer',
        'estado'              => 'required|in_list[asignada,en proceso,pausada,completada,cancelada]',
        'fecha_inicio'        => 'permit_empty|valid_date',
        'fecha_fin'           => 'permit_empty|valid_date',
        'cantidad_producida'  => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'nombre_tarea' => [
            'required'   => 'El nombre de la tarea es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 3 caracteres.',
            'max_length' => 'El nombre no puede exceder 255 caracteres.',
        ],
        'id_orden' => [
            'required' => 'La orden de produccion es obligatoria.',
            'integer'  => 'La orden seleccionada no es valida.',
        ],
        'id_maquina' => [
            'required' => 'La maquina es obligatoria.',
            'integer'  => 'La maquina seleccionada no es valida.',
        ],
        'id_operario' => [
            'required' => 'El operario es obligatorio.',
            'integer'  => 'El operario seleccionado no es valido.',
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list'  => 'El estado seleccionado no es valido.',
        ],
        'cantidad_producida' => [
            'integer'               => 'La cantidad producida debe ser un numero entero.',
            'greater_than_equal_to' => 'La cantidad producida no puede ser negativa.',
        ],
    ];
}
