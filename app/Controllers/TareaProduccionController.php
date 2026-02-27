<?php

namespace App\Controllers;

use App\Models\TareaProduccionModel;
use App\Models\OrdenProduccionModel;
use App\Models\MaquinaModel;
use App\Models\OperarioModel;
use App\Models\HistorialEstadoModel;

class TareaProduccionController extends BaseController
{
    protected $tareaModel;
    protected $ordenModel;
    protected $maquinaModel;
    protected $operarioModel;
    protected $historialModel;

    // Transiciones válidas para tareas (accesibles desde OrdenProduccionController)
    public const TRANSICIONES = [
        'asignada'   => ['en proceso', 'cancelada'],
        'en proceso' => ['pausada', 'completada', 'cancelada'],
        'pausada'    => ['en proceso', 'cancelada'],
        'completada' => [],
        'cancelada'  => [],
    ];

    public function __construct()
    {
        $this->tareaModel    = new TareaProduccionModel();
        $this->ordenModel    = new OrdenProduccionModel();
        $this->maquinaModel  = new MaquinaModel();
        $this->operarioModel = new OperarioModel();
        $this->historialModel = new HistorialEstadoModel();
    }

    private function getOperariosConNombre()
    {
        $db = \Config\Database::connect();
        return $db->table('operario')
            ->select('operario.id_operario, usuario.nombre_completo, operario.nivel_experiencia')
            ->join('usuario', 'usuario.id_usuario = operario.id_usuario')
            ->where('operario.estado', 'activo')
            ->get()
            ->getResultArray();
    }

    public function index()
    {
        $builder = $this->tareaModel->builder();
        $builder->select('tarea_produccion.*, orden_produccion.codigo_orden, orden_produccion.descripcion_producto, maquina.nombre_maquina, usuario.nombre_completo as operario_nombre');
        $builder->join('orden_produccion', 'orden_produccion.id_orden = tarea_produccion.id_orden', 'left');
        $builder->join('maquina', 'maquina.id_maquina = tarea_produccion.id_maquina', 'left');
        $builder->join('operario', 'operario.id_operario = tarea_produccion.id_operario', 'left');
        $builder->join('usuario', 'usuario.id_usuario = operario.id_usuario', 'left');
        $builder->orderBy('tarea_produccion.id_tarea', 'DESC');

        $tareas = $builder->get()->getResultArray();

        return view('tareas_produccion/index', [
            'title'     => 'Tareas de Produccion - Control de Produccion',
            'pageTitle' => 'Tareas de Produccion',
            'tareas'    => $tareas,
        ]);
    }

    public function cambiarEstado($id)
    {
        $tarea = $this->tareaModel->find($id);

        if (! $tarea) {
            return redirect()->to('/tareas-produccion')->with('error', 'Tarea no encontrada.');
        }

        $nuevoEstado   = $this->request->getPost('nuevo_estado');
        $observaciones = $this->request->getPost('observaciones');
        $estadoActual  = $tarea['estado'];

        $idResponsable = (int) $this->request->getPost('id_responsable');
        $db            = \Config\Database::connect();
        $usuarioResp   = $db->table('usuario')
            ->select('nombre_completo')
            ->where('id_usuario', $idResponsable)
            ->where('estado', 'activo')
            ->get()->getRowArray();
        $responsable   = $usuarioResp['nombre_completo'] ?? 'Sistema';

        $transicionesValidas = self::TRANSICIONES[$estadoActual] ?? [];

        if (! in_array($nuevoEstado, $transicionesValidas)) {
            return redirect()->to('/ordenes-produccion/ver/' . $tarea['id_orden'])
                ->with('error', "Transición no válida: '$estadoActual' → '$nuevoEstado'.");
        }

        $updateData = ['estado' => $nuevoEstado];

        // Fechas automáticas
        if ($nuevoEstado === 'en proceso' && empty($tarea['fecha_inicio'])) {
            $updateData['fecha_inicio'] = date('Y-m-d H:i:s');
        }
        if ($nuevoEstado === 'completada' && empty($tarea['fecha_fin'])) {
            $updateData['fecha_fin'] = date('Y-m-d H:i:s');
        }

        $this->tareaModel->update($id, $updateData);

        $this->historialModel->insert([
            'tipo_entidad'    => 'tarea',
            'id_entidad'      => $id,
            'estado_anterior' => $estadoActual,
            'estado_nuevo'    => $nuevoEstado,
            'responsable'     => $responsable,
            'observaciones'   => $observaciones,
            'fecha_cambio'    => date('Y-m-d H:i:s'),
        ]);

        $etiquetas = [
            'en proceso' => 'En Proceso', 'pausada' => 'Pausada',
            'completada' => 'Completada', 'cancelada' => 'Cancelada',
        ];
        $etiqueta = $etiquetas[$nuevoEstado] ?? ucfirst($nuevoEstado);

        return redirect()->to('/ordenes-produccion/ver/' . $tarea['id_orden'])
            ->with('success', "Tarea actualizada a «{$etiqueta}» exitosamente.");
    }

    public function new()
    {
        $ordenes   = $this->ordenModel->findAll();
        $maquinas  = $this->maquinaModel->findAll();
        $operarios = $this->getOperariosConNombre();

        return view('tareas_produccion/form', [
            'title'     => 'Nueva Tarea de Produccion',
            'pageTitle' => 'Nueva Tarea de Produccion',
            'ordenes'   => $ordenes,
            'maquinas'  => $maquinas,
            'operarios' => $operarios,
            'tarea'     => null,
            'errors'    => [],
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost([
            'nombre_tarea', 'descripcion', 'id_orden', 'id_maquina', 'id_operario',
            'fecha_inicio', 'fecha_fin', 'estado', 'cantidad_producida', 'observaciones',
        ]);

        if (! $this->tareaModel->insert($data)) {
            $ordenes   = $this->ordenModel->findAll();
            $maquinas  = $this->maquinaModel->findAll();
            $operarios = $this->getOperariosConNombre();

            return view('tareas_produccion/form', [
                'title'     => 'Nueva Tarea de Produccion',
                'pageTitle' => 'Nueva Tarea de Produccion',
                'ordenes'   => $ordenes,
                'maquinas'  => $maquinas,
                'operarios' => $operarios,
                'tarea'     => $data,
                'errors'    => $this->tareaModel->errors(),
            ]);
        }

        return redirect()->to('/tareas-produccion')->with('success', 'Tarea de produccion creada exitosamente.');
    }

    public function edit($id)
    {
        $tarea = $this->tareaModel->find($id);

        if (! $tarea) {
            return redirect()->to('/tareas-produccion')->with('error', 'Tarea no encontrada.');
        }

        $ordenes   = $this->ordenModel->findAll();
        $maquinas  = $this->maquinaModel->findAll();
        $operarios = $this->getOperariosConNombre();

        return view('tareas_produccion/form', [
            'title'     => 'Editar Tarea de Produccion',
            'pageTitle' => 'Editar Tarea de Produccion',
            'ordenes'   => $ordenes,
            'maquinas'  => $maquinas,
            'operarios' => $operarios,
            'tarea'     => $tarea,
            'errors'    => [],
        ]);
    }

    public function update($id)
    {
        $tarea = $this->tareaModel->find($id);

        if (! $tarea) {
            return redirect()->to('/tareas-produccion')->with('error', 'Tarea no encontrada.');
        }

        $data = $this->request->getPost([
            'nombre_tarea', 'descripcion', 'id_orden', 'id_maquina', 'id_operario',
            'fecha_inicio', 'fecha_fin', 'estado', 'cantidad_producida', 'observaciones',
        ]);

        if (! $this->tareaModel->update($id, $data)) {
            $ordenes   = $this->ordenModel->findAll();
            $maquinas  = $this->maquinaModel->findAll();
            $operarios = $this->getOperariosConNombre();

            return view('tareas_produccion/form', [
                'title'     => 'Editar Tarea de Produccion',
                'pageTitle' => 'Editar Tarea de Produccion',
                'ordenes'   => $ordenes,
                'maquinas'  => $maquinas,
                'operarios' => $operarios,
                'tarea'     => array_merge($tarea, $data),
                'errors'    => $this->tareaModel->errors(),
            ]);
        }

        return redirect()->to('/tareas-produccion')->with('success', 'Tarea de produccion actualizada exitosamente.');
    }

    public function delete($id)
    {
        $tarea = $this->tareaModel->find($id);

        if (! $tarea) {
            return redirect()->to('/tareas-produccion')->with('error', 'Tarea no encontrada.');
        }

        $this->tareaModel->delete($id);

        return redirect()->to('/tareas-produccion')->with('success', 'Tarea de produccion eliminada exitosamente.');
    }
}
