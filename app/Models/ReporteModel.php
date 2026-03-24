<?php

namespace App\Models;

use CodeIgniter\Model;

class ReporteModel extends Model
{
    // ──────────────────────────────────────────────────────────────
    //  REPORTE DE ÓRDENES DE PRODUCCIÓN
    // ──────────────────────────────────────────────────────────────

    public function getOrdenes(array $filtros = []): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('orden_produccion op')
            ->select('op.*, e.nombre AS empresa_nombre')
            ->join('empresa e', 'e.id_empresa = op.id_empresa', 'left');

        if (!empty($filtros['fecha_desde'])) {
            $builder->where('DATE(op.fecha_creacion) >=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('DATE(op.fecha_creacion) <=', $filtros['fecha_hasta']);
        }
        if (!empty($filtros['estado'])) {
            $builder->where('op.estado', $filtros['estado']);
        }
        if (!empty($filtros['prioridad'])) {
            $builder->where('op.prioridad', $filtros['prioridad']);
        }
        if (!empty($filtros['empresa_id'])) {
            $builder->where('op.id_empresa', $filtros['empresa_id']);
        }
        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('op.codigo_orden', $filtros['busqueda'])
                ->orLike('op.descripcion_producto', $filtros['busqueda'])
                ->orLike('op.cliente', $filtros['busqueda'])
                ->groupEnd();
        }

        $builder->orderBy('op.fecha_creacion', 'DESC');
        return $builder->get()->getResultArray();
    }

    // ──────────────────────────────────────────────────────────────
    //  REPORTE DE TAREAS DE PRODUCCIÓN
    // ──────────────────────────────────────────────────────────────

    public function getTareas(array $filtros = []): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tarea_produccion tp')
            ->select('tp.*, op.codigo_orden, op.descripcion_producto,
                      m.nombre_maquina, u.nombre_completo AS operario_nombre')
            ->join('orden_produccion op', 'op.id_orden = tp.id_orden', 'left')
            ->join('maquina m', 'm.id_maquina = tp.id_maquina', 'left')
            ->join('operario o', 'o.id_operario = tp.id_operario', 'left')
            ->join('usuario u', 'u.id_usuario = o.id_usuario', 'left');

        if (!empty($filtros['fecha_desde'])) {
            $builder->where('DATE(tp.fecha_asignacion) >=', $filtros['fecha_desde']);
        }
        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('DATE(tp.fecha_asignacion) <=', $filtros['fecha_hasta']);
        }
        if (!empty($filtros['estado'])) {
            $builder->where('tp.estado', $filtros['estado']);
        }
        if (!empty($filtros['operario_id'])) {
            $builder->where('tp.id_operario', $filtros['operario_id']);
        }
        if (!empty($filtros['maquina_id'])) {
            $builder->where('tp.id_maquina', $filtros['maquina_id']);
        }
        if (!empty($filtros['orden_id'])) {
            $builder->where('tp.id_orden', $filtros['orden_id']);
        }
        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('tp.nombre_tarea', $filtros['busqueda'])
                ->orLike('op.codigo_orden', $filtros['busqueda'])
                ->groupEnd();
        }

        $builder->orderBy('tp.fecha_asignacion', 'DESC');
        return $builder->get()->getResultArray();
    }

    // ──────────────────────────────────────────────────────────────
    //  ESTADÍSTICAS GENERALES
    // ──────────────────────────────────────────────────────────────

    public function getEstadisticasOrdenes(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->query('
            SELECT estado, COUNT(*) AS total
            FROM orden_produccion
            GROUP BY estado
        ')->getResultArray();

        $resultado = [
            'pendiente'    => 0,
            'en proceso'   => 0,
            'pausada'      => 0,
            'completada'   => 0,
            'cancelada'    => 0,
        ];
        foreach ($rows as $r) {
            $resultado[$r['estado']] = (int) $r['total'];
        }
        $resultado['total'] = array_sum($resultado);
        return $resultado;
    }

    public function getEstadisticasTareas(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->query('
            SELECT estado, COUNT(*) AS total
            FROM tarea_produccion
            GROUP BY estado
        ')->getResultArray();

        $resultado = [
            'asignada'   => 0,
            'en proceso' => 0,
            'pausada'    => 0,
            'completada' => 0,
            'cancelada'  => 0,
        ];
        foreach ($rows as $r) {
            $resultado[$r['estado']] = (int) $r['total'];
        }
        $resultado['total'] = array_sum($resultado);
        return $resultado;
    }

    public function getOrdenesPorPrioridad(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->query('
            SELECT prioridad, COUNT(*) AS total
            FROM orden_produccion
            GROUP BY prioridad
            ORDER BY FIELD(prioridad, "urgente", "alta", "media", "baja")
        ')->getResultArray();

        $resultado = ['baja' => 0, 'media' => 0, 'alta' => 0, 'urgente' => 0];
        foreach ($rows as $r) {
            $resultado[$r['prioridad']] = (int) $r['total'];
        }
        return $resultado;
    }

    public function getProduccionPorEmpresa(): array
    {
        $db = \Config\Database::connect();
        return $db->query('
            SELECT e.nombre AS empresa,
                   COUNT(op.id_orden)                                    AS total_ordenes,
                   SUM(op.estado = "completada")                         AS completadas,
                   SUM(op.estado = "en proceso")                         AS en_proceso,
                   COALESCE(SUM(tp.cantidad_producida), 0)               AS unidades_producidas
            FROM empresa e
            LEFT JOIN orden_produccion op ON op.id_empresa = e.id_empresa
            LEFT JOIN tarea_produccion tp ON tp.id_orden = op.id_orden AND tp.estado = "completada"
            GROUP BY e.id_empresa, e.nombre
            ORDER BY total_ordenes DESC
        ')->getResultArray();
    }

    public function getRendimientoOperarios(): array
    {
        $db = \Config\Database::connect();
        return $db->query('
            SELECT u.nombre_completo AS operario,
                   COUNT(tp.id_tarea)                                AS total_tareas,
                   SUM(tp.estado = "completada")                     AS completadas,
                   SUM(tp.estado = "en proceso")                     AS en_proceso,
                   COALESCE(SUM(tp.cantidad_producida), 0)           AS unidades,
                   COALESCE(AVG(tp.tiempo_real_minutos), 0)          AS tiempo_promedio
            FROM usuario u
            INNER JOIN operario o ON o.id_usuario = u.id_usuario
            LEFT JOIN tarea_produccion tp ON tp.id_operario = o.id_operario
            GROUP BY u.id_usuario, u.nombre_completo
            ORDER BY completadas DESC
        ')->getResultArray();
    }

    public function getHistorialReciente(int $limit = 15): array
    {
        $db = \Config\Database::connect();
        return $db->query("
            SELECT h.*, h.fecha_cambio,
                   CASE h.tipo_entidad
                       WHEN 'orden' THEN op.codigo_orden
                       WHEN 'tarea' THEN tp.nombre_tarea
                   END AS entidad_nombre
            FROM historial_estado h
            LEFT JOIN orden_produccion op ON h.tipo_entidad = 'orden' AND op.id_orden = h.id_entidad
            LEFT JOIN tarea_produccion tp ON h.tipo_entidad = 'tarea' AND tp.id_tarea = h.id_entidad
            ORDER BY h.fecha_cambio DESC
            LIMIT $limit
        ")->getResultArray();
    }

    // ──────────────────────────────────────────────────────────────
    //  HELPERS PARA FILTROS
    // ──────────────────────────────────────────────────────────────

    public function getEmpresasLista(): array
    {
        $db = \Config\Database::connect();
        return $db->table('empresa')
            ->select('id_empresa, nombre')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get()->getResultArray();
    }

    public function getOperariosLista(): array
    {
        $db = \Config\Database::connect();
        return $db->table('operario o')
            ->select('o.id_operario, u.nombre_completo')
            ->join('usuario u', 'u.id_usuario = o.id_usuario')
            ->where('o.estado', 'activo')
            ->orderBy('u.nombre_completo')
            ->get()->getResultArray();
    }

    public function getMaquinasLista(): array
    {
        $db = \Config\Database::connect();
        return $db->table('maquina')
            ->select('id_maquina, nombre_maquina')
            ->orderBy('nombre_maquina')
            ->get()->getResultArray();
    }
}
