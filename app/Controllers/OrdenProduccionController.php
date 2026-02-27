<?php

namespace App\Controllers;

use App\Models\OrdenProduccionModel;
use App\Models\EmpresaModel;
use App\Models\HistorialEstadoModel;

class OrdenProduccionController extends BaseController
{
    protected $ordenModel;
    protected $empresaModel;
    protected $historialModel;

    // Transiciones de estado válidas para órdenes
    private const TRANSICIONES = [
        'pendiente'  => ['en proceso', 'cancelada'],
        'en proceso' => ['pausada', 'completada', 'cancelada'],
        'pausada'    => ['en proceso', 'cancelada'],
        'completada' => [],
        'cancelada'  => [],
    ];

    public function __construct()
    {
        $this->ordenModel    = new OrdenProduccionModel();
        $this->empresaModel  = new EmpresaModel();
        $this->historialModel = new HistorialEstadoModel();
    }

    public function index()
    {
        $builder = $this->ordenModel->builder();
        $builder->select('orden_produccion.*, empresa.nombre as empresa_nombre');
        $builder->join('empresa', 'empresa.id_empresa = orden_produccion.id_empresa', 'left');

        $estado    = $this->request->getGet('estado');
        $prioridad = $this->request->getGet('prioridad');

        if ($estado && in_array($estado, ['pendiente', 'en proceso', 'pausada', 'completada', 'cancelada'])) {
            $builder->where('orden_produccion.estado', $estado);
        }
        if ($prioridad && in_array($prioridad, ['baja', 'media', 'alta', 'urgente'])) {
            $builder->where('orden_produccion.prioridad', $prioridad);
        }

        $builder->orderBy('orden_produccion.id_orden', 'DESC');
        $ordenes = $builder->get()->getResultArray();

        return view('ordenes_produccion/index', [
            'title'           => 'Ordenes de Produccion - Control de Produccion',
            'pageTitle'       => 'Ordenes de Produccion',
            'ordenes'         => $ordenes,
            'filtroEstado'    => $estado,
            'filtroPrioridad' => $prioridad,
        ]);
    }

    public function ver($id)
    {
        $db = \Config\Database::connect();

        $orden = $db->table('orden_produccion')
            ->select('orden_produccion.*, empresa.nombre as empresa_nombre')
            ->join('empresa', 'empresa.id_empresa = orden_produccion.id_empresa', 'left')
            ->where('orden_produccion.id_orden', $id)
            ->get()->getRowArray();

        if (! $orden) {
            return redirect()->to('/ordenes-produccion')->with('error', 'Orden no encontrada.');
        }

        $tareas = $db->table('tarea_produccion')
            ->select('tarea_produccion.*, maquina.nombre_maquina, usuario.nombre_completo as operario_nombre')
            ->join('maquina',  'maquina.id_maquina = tarea_produccion.id_maquina',   'left')
            ->join('operario', 'operario.id_operario = tarea_produccion.id_operario','left')
            ->join('usuario',  'usuario.id_usuario = operario.id_usuario',           'left')
            ->where('tarea_produccion.id_orden', $id)
            ->orderBy('tarea_produccion.id_tarea', 'ASC')
            ->get()->getResultArray();

        // Historial de la orden
        $historialOrden = $this->historialModel
            ->where('tipo_entidad', 'orden')
            ->where('id_entidad', $id)
            ->orderBy('fecha_cambio', 'DESC')
            ->findAll();

        // Historial de sus tareas
        $idTareas       = array_column($tareas, 'id_tarea');
        $historialTareas = [];
        if (! empty($idTareas)) {
            $historialTareas = $this->historialModel
                ->where('tipo_entidad', 'tarea')
                ->whereIn('id_entidad', $idTareas)
                ->orderBy('fecha_cambio', 'DESC')
                ->findAll();
        }

        // Métricas de progreso
        $totalTareas       = count($tareas);
        $tareasCompletadas = count(array_filter($tareas, fn($t) => $t['estado'] === 'completada'));
        $tareasCanceladas  = count(array_filter($tareas, fn($t) => $t['estado'] === 'cancelada'));
        $tareasEnProceso   = count(array_filter($tareas, fn($t) => $t['estado'] === 'en proceso'));
        $tareasPausadas    = count(array_filter($tareas, fn($t) => $t['estado'] === 'pausada'));
        $tareasAsignadas   = count(array_filter($tareas, fn($t) => $t['estado'] === 'asignada'));
        $tareasActivas     = $totalTareas - $tareasCanceladas;
        $porcentaje        = $tareasActivas > 0 ? round(($tareasCompletadas / $tareasActivas) * 100) : 0;

        // Transiciones válidas para el estado actual
        $transicionesOrden = self::TRANSICIONES[$orden['estado']] ?? [];

        // Transiciones para tareas (para JS del modal)
        $transicionesTarea = TareaProduccionController::TRANSICIONES;

        // Usuarios activos para el select de responsable
        $usuarios = $db->table('usuario')
            ->select('id_usuario, nombre_completo')
            ->where('estado', 'activo')
            ->orderBy('nombre_completo', 'ASC')
            ->get()->getResultArray();

        return view('ordenes_produccion/detalle', [
            'title'             => 'Detalle ' . esc($orden['codigo_orden']) . ' - Control de Produccion',
            'pageTitle'         => 'Seguimiento: ' . esc($orden['codigo_orden']),
            'orden'             => $orden,
            'tareas'            => $tareas,
            'historialOrden'    => $historialOrden,
            'historialTareas'   => $historialTareas,
            'totalTareas'       => $totalTareas,
            'tareasCompletadas' => $tareasCompletadas,
            'tareasEnProceso'   => $tareasEnProceso,
            'tareasPausadas'    => $tareasPausadas,
            'tareasAsignadas'   => $tareasAsignadas,
            'porcentaje'        => $porcentaje,
            'transicionesOrden' => $transicionesOrden,
            'transicionesTarea' => $transicionesTarea,
            'usuarios'          => $usuarios,
        ]);
    }

    public function cambiarEstado($id)
    {
        $orden = $this->ordenModel->find($id);

        if (! $orden) {
            return redirect()->to('/ordenes-produccion')->with('error', 'Orden no encontrada.');
        }

        $nuevoEstado   = $this->request->getPost('nuevo_estado');
        $observaciones = $this->request->getPost('observaciones');
        $estadoActual  = $orden['estado'];

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
            return redirect()->to('/ordenes-produccion/ver/' . $id)
                ->with('error', "Transición de estado no válida: '$estadoActual' → '$nuevoEstado'.");
        }

        $updateData = ['estado' => $nuevoEstado];
        if ($nuevoEstado === 'completada') {
            $updateData['fecha_completada'] = date('Y-m-d H:i:s');
        }

        $this->ordenModel->update($id, $updateData);

        $this->historialModel->insert([
            'tipo_entidad'   => 'orden',
            'id_entidad'     => $id,
            'estado_anterior' => $estadoActual,
            'estado_nuevo'   => $nuevoEstado,
            'responsable'    => $responsable,
            'observaciones'  => $observaciones,
            'fecha_cambio'   => date('Y-m-d H:i:s'),
        ]);

        $etiquetas = [
            'en proceso' => 'En Proceso', 'pausada' => 'Pausada',
            'completada' => 'Completada', 'cancelada' => 'Cancelada',
        ];
        $etiqueta = $etiquetas[$nuevoEstado] ?? ucfirst($nuevoEstado);

        return redirect()->to('/ordenes-produccion/ver/' . $id)
            ->with('success', "Estado cambiado a «{$etiqueta}» exitosamente.");
    }

    public function new()
    {
        $empresas = $this->empresaModel->findAll();

        return view('ordenes_produccion/form', [
            'title'     => 'Nueva Orden de Produccion',
            'pageTitle' => 'Nueva Orden de Produccion',
            'empresas'  => $empresas,
            'orden'     => null,
            'errors'    => [],
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost([
            'descripcion_producto', 'cantidad_solicitada', 'fecha_limite',
            'prioridad', 'estado', 'id_empresa', 'observaciones',
        ]);

        // Auto-generar código único
        $db   = \Config\Database::connect();
        $last = $db->table('orden_produccion')->orderBy('id_orden', 'DESC')->limit(1)->get()->getRowArray();
        $next = $last ? (intval(substr($last['codigo_orden'], -3)) + 1) : 1;
        $data['codigo_orden']   = 'ORD-' . date('Y') . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        $data['fecha_creacion'] = date('Y-m-d H:i:s');

        if (! $this->ordenModel->insert($data)) {
            $empresas = $this->empresaModel->findAll();

            return view('ordenes_produccion/form', [
                'title'     => 'Nueva Orden de Produccion',
                'pageTitle' => 'Nueva Orden de Produccion',
                'empresas'  => $empresas,
                'orden'     => $data,
                'errors'    => $this->ordenModel->errors(),
            ]);
        }

        return redirect()->to('/ordenes-produccion')->with('success', 'Orden de produccion creada exitosamente.');
    }

    public function edit($id)
    {
        $orden = $this->ordenModel->find($id);

        if (! $orden) {
            return redirect()->to('/ordenes-produccion')->with('error', 'Orden no encontrada.');
        }

        $empresas = $this->empresaModel->findAll();

        return view('ordenes_produccion/form', [
            'title'     => 'Editar Orden de Produccion',
            'pageTitle' => 'Editar Orden de Produccion',
            'empresas'  => $empresas,
            'orden'     => $orden,
            'errors'    => [],
        ]);
    }

    public function update($id)
    {
        $orden = $this->ordenModel->find($id);

        if (! $orden) {
            return redirect()->to('/ordenes-produccion')->with('error', 'Orden no encontrada.');
        }

        $data = $this->request->getPost([
            'descripcion_producto', 'cantidad_solicitada', 'fecha_limite',
            'prioridad', 'estado', 'id_empresa', 'observaciones',
        ]);

        if (! $this->ordenModel->update($id, $data)) {
            $empresas = $this->empresaModel->findAll();

            return view('ordenes_produccion/form', [
                'title'     => 'Editar Orden de Produccion',
                'pageTitle' => 'Editar Orden de Produccion',
                'empresas'  => $empresas,
                'orden'     => array_merge($orden, $data),
                'errors'    => $this->ordenModel->errors(),
            ]);
        }

        return redirect()->to('/ordenes-produccion')->with('success', 'Orden de produccion actualizada exitosamente.');
    }

    public function delete($id)
    {
        $orden = $this->ordenModel->find($id);

        if (! $orden) {
            return redirect()->to('/ordenes-produccion')->with('error', 'Orden no encontrada.');
        }

        $this->ordenModel->delete($id);

        return redirect()->to('/ordenes-produccion')->with('success', 'Orden de produccion eliminada exitosamente.');
    }
}
