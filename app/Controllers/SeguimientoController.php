<?php

namespace App\Controllers;

class SeguimientoController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $ordenes = $db->table('orden_produccion')
            ->select('orden_produccion.*, empresa.nombre as empresa_nombre')
            ->join('empresa', 'empresa.id_empresa = orden_produccion.id_empresa', 'left')
            ->orderBy('FIELD(orden_produccion.estado, "en proceso", "pausada", "pendiente", "completada", "cancelada")', '', false)
            ->orderBy('FIELD(orden_produccion.prioridad, "urgente", "alta", "media", "baja")', '', false)
            ->get()->getResultArray();

        // Conteo de tareas agrupado por orden y estado
        $statsRaw = $db->table('tarea_produccion')
            ->select('id_orden, estado, COUNT(*) as total')
            ->groupBy(['id_orden', 'estado'])
            ->get()->getResultArray();

        $statsMap = [];
        foreach ($statsRaw as $row) {
            $statsMap[$row['id_orden']][$row['estado']] = (int) $row['total'];
        }

        foreach ($ordenes as &$orden) {
            $id    = $orden['id_orden'];
            $stats = $statsMap[$id] ?? [];
            $total = array_sum($stats);
            $completadas = $stats['completada'] ?? 0;

            $orden['total_tareas']       = $total;
            $orden['tareas_completadas'] = $completadas;
            $orden['tareas_en_proceso']  = ($stats['en proceso'] ?? 0);
            $orden['tareas_pausadas']    = ($stats['pausada']    ?? 0);
            $orden['tareas_asignadas']   = ($stats['asignada']   ?? 0);
            $orden['tareas_canceladas']  = ($stats['cancelada']  ?? 0);
            $orden['porcentaje']         = $total > 0 ? round(($completadas / $total) * 100) : 0;
        }
        unset($orden);

        $resumen = ['pendiente' => 0, 'en proceso' => 0, 'pausada' => 0, 'completada' => 0, 'cancelada' => 0];
        foreach ($ordenes as $o) {
            if (isset($resumen[$o['estado']])) {
                $resumen[$o['estado']]++;
            }
        }

        return view('seguimiento/index', [
            'title'     => 'Seguimiento de Produccion - Control de Produccion',
            'pageTitle' => 'Seguimiento Operativo',
            'ordenes'   => $ordenes,
            'resumen'   => $resumen,
        ]);
    }
}
