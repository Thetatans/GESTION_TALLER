<?php

namespace App\Controllers;

use App\Models\ReporteModel;
use App\Models\EmpresaModel;

class ReporteController extends BaseController
{
    private ReporteModel $reporteModel;

    public function __construct()
    {
        $this->reporteModel = new ReporteModel();
    }

    // ──────────────────────────────────────────────────────────────
    //  DASHBOARD DE REPORTES
    // ──────────────────────────────────────────────────────────────

    public function index(): string
    {
        $estadOrdenes = $this->reporteModel->getEstadisticasOrdenes();
        $estadTareas  = $this->reporteModel->getEstadisticasTareas();
        $historial    = $this->reporteModel->getHistorialReciente(10);

        return view('reportes/index', [
            'title'         => 'Módulo de Reportes',
            'estadOrdenes'  => $estadOrdenes,
            'estadTareas'   => $estadTareas,
            'historial'     => $historial,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  REPORTE DE ÓRDENES
    // ──────────────────────────────────────────────────────────────

    public function ordenes(): string
    {
        $filtros  = $this->_getFiltrosOrdenes();
        $ordenes  = $this->reporteModel->getOrdenes($filtros);
        $empresas = $this->reporteModel->getEmpresasLista();

        return view('reportes/ordenes', [
            'title'    => 'Reporte de Órdenes de Producción',
            'ordenes'  => $ordenes,
            'empresas' => $empresas,
            'filtros'  => $filtros,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  REPORTE DE TAREAS
    // ──────────────────────────────────────────────────────────────

    public function tareas(): string
    {
        $filtros   = $this->_getFiltrosTareas();
        $tareas    = $this->reporteModel->getTareas($filtros);
        $operarios = $this->reporteModel->getOperariosLista();
        $maquinas  = $this->reporteModel->getMaquinasLista();

        return view('reportes/tareas', [
            'title'     => 'Reporte de Tareas de Producción',
            'tareas'    => $tareas,
            'operarios' => $operarios,
            'maquinas'  => $maquinas,
            'filtros'   => $filtros,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  REPORTE RESUMEN ESTADÍSTICO
    // ──────────────────────────────────────────────────────────────

    public function resumen(): string
    {
        $estadOrdenes   = $this->reporteModel->getEstadisticasOrdenes();
        $estadTareas    = $this->reporteModel->getEstadisticasTareas();
        $porPrioridad   = $this->reporteModel->getOrdenesPorPrioridad();
        $porEmpresa     = $this->reporteModel->getProduccionPorEmpresa();
        $rendimiento    = $this->reporteModel->getRendimientoOperarios();
        $historial      = $this->reporteModel->getHistorialReciente(20);

        return view('reportes/resumen', [
            'title'        => 'Resumen Estadístico de Producción',
            'estadOrdenes' => $estadOrdenes,
            'estadTareas'  => $estadTareas,
            'porPrioridad' => $porPrioridad,
            'porEmpresa'   => $porEmpresa,
            'rendimiento'  => $rendimiento,
            'historial'    => $historial,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  PDF – ÓRDENES
    // ──────────────────────────────────────────────────────────────

    public function pdfOrdenes(): void
    {
        $filtros = $this->_getFiltrosOrdenes();
        $ordenes = $this->reporteModel->getOrdenes($filtros);

        $pdf = $this->_crearPdfBase('Reporte de Órdenes de Producción');

        // ── Filtros aplicados ──
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $linea = 'Generado: ' . date('d/m/Y H:i');
        if (!empty($filtros['fecha_desde'])) {
            $linea .= '  |  Desde: ' . $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $linea .= '  |  Hasta: ' . $filtros['fecha_hasta'];
        }
        if (!empty($filtros['estado'])) {
            $linea .= '  |  Estado: ' . strtoupper($filtros['estado']);
        }
        if (!empty($filtros['prioridad'])) {
            $linea .= '  |  Prioridad: ' . strtoupper($filtros['prioridad']);
        }
        $pdf->Cell(0, 6, $linea, 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // ── Tabla de datos ──
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);

        $cols = [
            ['text' => 'Código',      'width' => 24, 'align' => 'C'],
            ['text' => 'Producto',    'width' => 60, 'align' => 'L'],
            ['text' => 'Empresa',     'width' => 42, 'align' => 'L'],
            ['text' => 'Prioridad',   'width' => 18, 'align' => 'C'],
            ['text' => 'Estado',      'width' => 22, 'align' => 'C'],
            ['text' => 'F. Creación', 'width' => 22, 'align' => 'C'],
        ];
        foreach ($cols as $c) {
            $pdf->Cell($c['width'], 7, $c['text'], 1, 0, $c['align'], true);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;

        foreach ($ordenes as $o) {
            $pdf->SetFillColor($fill ? 241 : 255, $fill ? 245 : 255, $fill ? 251 : 255);
            $pdf->Cell(24, 6, $o['codigo_orden'],                                  1, 0, 'C', $fill);
            $pdf->Cell(60, 6, $this->_truncate($o['descripcion_producto'], 40),    1, 0, 'L', $fill);
            $pdf->Cell(42, 6, $this->_truncate($o['empresa_nombre'] ?? '-', 28),   1, 0, 'L', $fill);
            $pdf->Cell(18, 6, strtoupper($o['prioridad']),                         1, 0, 'C', $fill);
            $pdf->Cell(22, 6, strtoupper($o['estado']),                            1, 0, 'C', $fill);
            $pdf->Cell(22, 6, $this->_fecha($o['fecha_creacion']),                 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        // ── Totales ──
        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 6, 'Total de registros: ' . count($ordenes), 0, 1, 'R');

        $pdf->Output('reporte_ordenes_' . date('Ymd_His') . '.pdf', 'D');
    }

    // ──────────────────────────────────────────────────────────────
    //  PDF – TAREAS
    // ──────────────────────────────────────────────────────────────

    public function pdfTareas(): void
    {
        $filtros = $this->_getFiltrosTareas();
        $tareas  = $this->reporteModel->getTareas($filtros);

        $pdf = $this->_crearPdfBase('Reporte de Tareas de Producción');

        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $linea = 'Generado: ' . date('d/m/Y H:i');
        if (!empty($filtros['fecha_desde'])) {
            $linea .= '  |  Desde: ' . $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $linea .= '  |  Hasta: ' . $filtros['fecha_hasta'];
        }
        if (!empty($filtros['estado'])) {
            $linea .= '  |  Estado: ' . strtoupper($filtros['estado']);
        }
        $pdf->Cell(0, 6, $linea, 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // ── Encabezado tabla ──
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);

        $cols = [
            ['text' => 'Tarea',        'width' => 38, 'align' => 'L'],
            ['text' => 'Orden',        'width' => 22, 'align' => 'C'],
            ['text' => 'Operario',     'width' => 32, 'align' => 'L'],
            ['text' => 'Máquina',      'width' => 26, 'align' => 'L'],
            ['text' => 'Estado',       'width' => 20, 'align' => 'C'],
            ['text' => 'T.Est(min)',   'width' => 16, 'align' => 'C'],
            ['text' => 'T.Real(min)',  'width' => 16, 'align' => 'C'],
            ['text' => 'Uds.',         'width' => 16, 'align' => 'C'],
        ];
        foreach ($cols as $c) {
            $pdf->Cell($c['width'], 7, $c['text'], 1, 0, $c['align'], true);
        }
        $pdf->Ln();

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;

        foreach ($tareas as $t) {
            $pdf->SetFillColor($fill ? 241 : 255, $fill ? 245 : 255, $fill ? 251 : 255);
            $pdf->Cell(38, 6, $this->_truncate($t['nombre_tarea'], 24),             1, 0, 'L', $fill);
            $pdf->Cell(22, 6, $t['codigo_orden'] ?? '-',                           1, 0, 'C', $fill);
            $pdf->Cell(32, 6, $this->_truncate($t['operario_nombre'] ?? '-', 20),  1, 0, 'L', $fill);
            $pdf->Cell(26, 6, $this->_truncate($t['nombre_maquina'] ?? '-', 16),   1, 0, 'L', $fill);
            $pdf->Cell(20, 6, strtoupper($t['estado']),                            1, 0, 'C', $fill);
            $pdf->Cell(16, 6, $t['tiempo_estimado_minutos'] ?? '-',                1, 0, 'C', $fill);
            $pdf->Cell(16, 6, $t['tiempo_real_minutos'] ?? '-',                    1, 0, 'C', $fill);
            $pdf->Cell(16, 6, $t['cantidad_producida'] ?? '0',                     1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', 'B', 8);
        $unidades = array_sum(array_column($tareas, 'cantidad_producida'));
        $pdf->Cell(0, 6, 'Total registros: ' . count($tareas) . '   |   Total unidades producidas: ' . $unidades, 0, 1, 'R');

        $pdf->Output('reporte_tareas_' . date('Ymd_His') . '.pdf', 'D');
    }

    // ──────────────────────────────────────────────────────────────
    //  PDF – RESUMEN ESTADÍSTICO
    // ──────────────────────────────────────────────────────────────

    public function pdfResumen(): void
    {
        $estadOrdenes = $this->reporteModel->getEstadisticasOrdenes();
        $estadTareas  = $this->reporteModel->getEstadisticasTareas();
        $porEmpresa   = $this->reporteModel->getProduccionPorEmpresa();
        $rendimiento  = $this->reporteModel->getRendimientoOperarios();

        $pdf = $this->_crearPdfBase('Resumen Estadístico de Producción');

        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, 'Generado: ' . date('d/m/Y H:i'), 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);

        // ── KPIs globales ──
        $this->_pdfSeccion($pdf, 'Indicadores Globales');
        $pdf->SetFont('helvetica', '', 9);

        $kpis = [
            ['Órdenes totales',         $estadOrdenes['total']],
            ['Órdenes completadas',     $estadOrdenes['completada']],
            ['Órdenes en proceso',      $estadOrdenes['en proceso']],
            ['Órdenes pendientes',      $estadOrdenes['pendiente']],
            ['Tareas totales',          $estadTareas['total']],
            ['Tareas completadas',      $estadTareas['completada']],
            ['Tareas en proceso',       $estadTareas['en proceso']],
        ];
        $fill = false;
        foreach ($kpis as $k) {
            $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
            $pdf->Cell(100, 6, $k[0], 1, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(40, 6, $k[1], 1, 1, 'C', $fill);
            $pdf->SetFont('helvetica', '', 9);
            $fill = !$fill;
        }
        $pdf->Ln(4);

        // ── Producción por empresa ──
        $this->_pdfSeccion($pdf, 'Producción por Empresa');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);
        foreach ([['Empresa', 70], ['Órdenes', 25], ['Completadas', 28], ['En proceso', 28], ['Uds.Producidas', 28]] as $c) {
            $pdf->Cell($c[1], 7, $c[0], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($porEmpresa as $e) {
            $pdf->SetFillColor($fill ? 241 : 255, $fill ? 245 : 255, $fill ? 251 : 255);
            $pdf->Cell(70, 6, $e['empresa'],              1, 0, 'L', $fill);
            $pdf->Cell(25, 6, $e['total_ordenes'],        1, 0, 'C', $fill);
            $pdf->Cell(28, 6, $e['completadas'],          1, 0, 'C', $fill);
            $pdf->Cell(28, 6, $e['en_proceso'],           1, 0, 'C', $fill);
            $pdf->Cell(28, 6, $e['unidades_producidas'],  1, 1, 'C', $fill);
            $fill = !$fill;
        }
        $pdf->Ln(4);

        // ── Rendimiento de operarios ──
        $this->_pdfSeccion($pdf, 'Rendimiento de Operarios');
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(30, 41, 59);
        $pdf->SetTextColor(255, 255, 255);
        foreach ([['Operario', 65], ['Total Tareas', 28], ['Completadas', 28], ['En Proceso', 28], ['Tiempo Prom.', 30]] as $c) {
            $pdf->Cell($c[1], 7, $c[0], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $fill = false;
        foreach ($rendimiento as $r) {
            $pdf->SetFillColor($fill ? 241 : 255, $fill ? 245 : 255, $fill ? 251 : 255);
            $pdf->Cell(65, 6, $r['operario'],                          1, 0, 'L', $fill);
            $pdf->Cell(28, 6, $r['total_tareas'],                      1, 0, 'C', $fill);
            $pdf->Cell(28, 6, $r['completadas'],                       1, 0, 'C', $fill);
            $pdf->Cell(28, 6, $r['en_proceso'],                        1, 0, 'C', $fill);
            $pdf->Cell(30, 6, round($r['tiempo_promedio'], 1) . ' min', 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->Output('reporte_resumen_' . date('Ymd_His') . '.pdf', 'D');
    }

    // ──────────────────────────────────────────────────────────────
    //  HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────────

    private function _crearPdfBase(string $titulo): \TCPDF
    {
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Gestión Taller');
        $pdf->SetAuthor('Control de Producción');
        $pdf->SetTitle($titulo);

        $pdf->SetMargins(10, 22, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 15);

        // Header personalizado
        $pdf->setHeaderData('', 0, $titulo, "Control de Producción – Sistema de Gestión\nIlich Esteban Reyes Botia - DICO Telecomunicaciones", [30, 41, 59], [30, 41, 59]);
        $pdf->setHeaderFont(['helvetica', 'B', 12]);
        $pdf->setFooterFont(['helvetica', '', 8]);
        $pdf->setFooterData([30, 41, 59], [30, 41, 59]);

        $pdf->AddPage();
        return $pdf;
    }

    private function _pdfSeccion(\TCPDF $pdf, string $titulo): void
    {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(226, 232, 240);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 8, '  ' . strtoupper($titulo), 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(2);
    }

    private function _truncate(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1) . '…' : $text;
    }

    private function _fecha(?string $fecha): string
    {
        if (empty($fecha)) {
            return '-';
        }
        return date('d/m/Y', strtotime($fecha));
    }

    private function _getFiltrosOrdenes(): array
    {
        return [
            'fecha_desde' => $this->request->getGet('fecha_desde') ?? '',
            'fecha_hasta' => $this->request->getGet('fecha_hasta') ?? '',
            'estado'      => $this->request->getGet('estado') ?? '',
            'prioridad'   => $this->request->getGet('prioridad') ?? '',
            'empresa_id'  => $this->request->getGet('empresa_id') ?? '',
            'busqueda'    => $this->request->getGet('busqueda') ?? '',
        ];
    }

    private function _getFiltrosTareas(): array
    {
        return [
            'fecha_desde'  => $this->request->getGet('fecha_desde') ?? '',
            'fecha_hasta'  => $this->request->getGet('fecha_hasta') ?? '',
            'estado'       => $this->request->getGet('estado') ?? '',
            'operario_id'  => $this->request->getGet('operario_id') ?? '',
            'maquina_id'   => $this->request->getGet('maquina_id') ?? '',
            'orden_id'     => $this->request->getGet('orden_id') ?? '',
            'busqueda'     => $this->request->getGet('busqueda') ?? '',
        ];
    }

    // ──────────────────────────────────────────────────────────────
    //  PDF – MANUAL DE USUARIO
    // ──────────────────────────────────────────────────────────────

    public function pdfManualUsuario(): void
    {
        $pdf = $this->_crearPdfBase('Manual de Usuario – Sistema de Gestión de Producción');
        $pdf->setHeaderData('', 0, 'Manual de Usuario', "Sistema de Gestión de Producción\nControl de Órdenes y Tareas", [30, 41, 59], [30, 41, 59]);

        // Portada
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 40, '', 0, 1, 'C');
        $pdf->Cell(0, 20, 'MANUAL DE USUARIO', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 16);
        $pdf->Cell(0, 10, 'Sistema de Gestión de Producción', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 8, 'Versión 1.0 – Marzo 2026', 0, 1, 'C');
        $pdf->Cell(0, 8, 'Ilich Esteban Reyes Botia', 0, 1, 'C');
        $pdf->Cell(0, 8, 'DICO Telecomunicaciones', 0, 1, 'C');
        $pdf->Ln(20);

        // Tabla de contenido
        $pdf->SetTextColor(30, 41, 59);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Contenido', 0, 1, 'L');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', '', 11);
        $contenido = [
            '1. Introducción al Sistema',
            '2. Requisitos y Acceso',
            '3. Guía por Roles de Usuario',
            '4. Módulo de Órdenes de Producción',
            '5. Módulo de Tareas de Producción',
            '6. Seguimiento y Cambio de Estados',
            '7. Módulo de Reportes',
            '8. Preguntas Frecuentes',
        ];
        foreach ($contenido as $item) {
            $pdf->Cell(10, 8, '', 0, 0);
            $pdf->Cell(0, 8, $item, 0, 1);
        }

        // Sección 1: Introducción
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '1. Introducción al Sistema');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "El Sistema de Gestión de Producción es una aplicación web desarrollada para el control y seguimiento de órdenes y tareas de producción en talleres industriales. Permite gestionar el flujo completo de trabajo desde la creación de órdenes hasta la finalización de tareas individuales.", 0, 'J');
        $pdf->Ln(5);
        $pdf->MultiCell(0, 6, "Principales características:", 0, 'L');
        $pdf->Ln(2);
        $caracteristicas = [
            '• Gestión de órdenes de producción con códigos automáticos',
            '• Asignación de tareas a operarios y máquinas',
            '• Control de estados en tiempo real (pendiente, en proceso, completada)',
            '• Sistema de seguimiento con historial de cambios',
            '• Reportes exportables a PDF con estadísticas',
            '• Control de acceso por roles (Admin, Supervisor, Operario)',
            '• Gestión de empresas, máquinas y personal',
        ];
        foreach ($caracteristicas as $c) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $c, 0, 'L');
        }

        // Sección 2: Requisitos
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '2. Requisitos y Acceso');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "REQUISITOS TÉCNICOS:", 0, 'L');
        $pdf->Ln(2);
        $requisitos = [
            '• Navegador web moderno (Chrome, Firefox, Edge, Safari)',
            '• Conexión a Internet o red local',
            '• Resolución mínima recomendada: 1280x720',
        ];
        foreach ($requisitos as $r) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $r, 0, 'L');
        }
        $pdf->Ln(5);

        $pdf->MultiCell(0, 6, "ACCESO AL SISTEMA:", 0, 'L');
        $pdf->Ln(2);
        $pdf->MultiCell(0, 6, "1. Abra su navegador web e ingrese la URL del sistema", 0, 'L');
        $pdf->MultiCell(0, 6, "2. En la pantalla de login, ingrese su correo electrónico y contraseña", 0, 'L');
        $pdf->MultiCell(0, 6, "3. Haga clic en 'Iniciar Sesión'", 0, 'L');
        $pdf->Ln(5);

        $pdf->SetFillColor(254, 252, 232);
        $pdf->SetTextColor(146, 64, 14);
        $pdf->Cell(0, 8, '  Credenciales de prueba:', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(0, 6, 'Admin: admin@taller.com / Admin2026!', 0, 1);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(0, 6, 'Supervisor: supervisor@taller.com / Super2026!', 0, 1);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->Cell(0, 6, 'Operario: operario@taller.com / Oper2026!', 0, 1);

        // Sección 3: Roles
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '3. Guía por Roles de Usuario');

        // Admin
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 8, 'ADMINISTRADOR', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(0, 6, "Tiene acceso completo a todas las funcionalidades del sistema:", 0, 'L');
        $admin_funcs = [
            '• Gestión de empresas (crear, editar, eliminar)',
            '• Gestión completa de usuarios y roles',
            '• Crear, editar y eliminar órdenes de producción',
            '• Crear, editar y eliminar tareas',
            '• Acceso a todos los reportes y estadísticas',
            '• Configuración del sistema',
        ];
        foreach ($admin_funcs as $f) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $f, 0, 'L');
        }
        $pdf->Ln(5);

        // Supervisor
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 8, 'SUPERVISOR', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(0, 6, "Puede gestionar la producción pero no la configuración del sistema:", 0, 'L');
        $super_funcs = [
            '• Crear y editar órdenes de producción',
            '• Crear y editar tareas',
            '• Cambiar estados de órdenes y tareas',
            '• Ver reportes y estadísticas',
            '• Acceso a calidad, almacén y personal',
            '• NO puede eliminar registros ni gestionar usuarios',
        ];
        foreach ($super_funcs as $f) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $f, 0, 'L');
        }
        $pdf->Ln(5);

        // Operario
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 8, 'OPERARIO', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(0, 6, "Acceso limitado para ejecutar tareas asignadas:", 0, 'L');
        $op_funcs = [
            '• Ver solo sus tareas asignadas',
            '• Cambiar estado de sus tareas (iniciar, pausar, completar)',
            '• Ver órdenes de producción (solo lectura)',
            '• Acceso al seguimiento de cambios',
            '• Ver máquinas (solo lectura)',
        ];
        foreach ($op_funcs as $f) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $f, 0, 'L');
        }

        // Sección 4: Órdenes
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '4. Módulo de Órdenes de Producción');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "Las órdenes de producción representan trabajos completos solicitados por clientes. Cada orden tiene un código único generado automáticamente.", 0, 'J');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'ESTADOS DE UNA ORDEN:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $estados_orden = [
            ['Pendiente', 'La orden está creada pero aún no ha iniciado'],
            ['En proceso', 'La orden tiene tareas siendo ejecutadas'],
            ['Pausada', 'La orden está temporalmente detenida'],
            ['Completada', 'Todas las tareas han finalizado'],
            ['Cancelada', 'La orden fue anulada'],
        ];
        foreach ($estados_orden as $e) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, 6, $e[0], 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, '– ' . $e[1], 0, 'L');
        }
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'FLUJO DE TRABAJO:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $flujo = [
            '1. Crear orden: Complete el formulario con descripción, cantidad, prioridad y fecha límite',
            '2. Asignar tareas: Divida la orden en tareas específicas para operarios',
            '3. Seguimiento: Monitoree el avance desde el módulo de seguimiento',
            '4. Completar: Marque la orden como completada cuando finalice',
        ];
        foreach ($flujo as $f) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $f, 0, 'L');
        }

        // Sección 5: Tareas
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '5. Módulo de Tareas de Producción');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "Las tareas son las unidades de trabajo individuales que componen una orden. Cada tarea se asigna a un operario y opcionalmente a una máquina.", 0, 'J');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'CAMPOS IMPORTANTES:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $campos = [
            ['Nombre de tarea', 'Descripción breve de la actividad'],
            ['Orden asociada', 'La orden de producción a la que pertenece'],
            ['Operario', 'Persona responsable de ejecutar la tarea'],
            ['Máquina', 'Equipo a utilizar (opcional)'],
            ['Tiempo estimado', 'Duración prevista en minutos'],
            ['Cantidad a producir', 'Unidades esperadas de la tarea'],
        ];
        foreach ($campos as $c) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(45, 6, $c[0] . ':', 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, $c[1], 0, 'L');
        }
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'ESTADOS DE UNA TAREA:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $estados_tarea = [
            ['Asignada', 'Tarea creada, esperando inicio'],
            ['En proceso', 'El operario está trabajando en ella'],
            ['Pausada', 'Trabajo temporalmente detenido'],
            ['Completada', 'Tarea finalizada'],
            ['Cancelada', 'Tarea anulada'],
        ];
        foreach ($estados_tarea as $e) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(30, 6, $e[0], 0, 0);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, '– ' . $e[1], 0, 'L');
        }

        // Sección 6: Seguimiento
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '6. Seguimiento y Cambio de Estados');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "El sistema registra automáticamente todos los cambios de estado tanto de órdenes como de tareas, creando un historial de auditoría completo.", 0, 'J');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'PARA CAMBIAR EL ESTADO:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $cambios = [
            '1. Desde el listado, haga clic en el botón de estado actual',
            '2. Seleccione el nuevo estado del menú desplegable',
            '3. Confirme el cambio',
            '4. El sistema registrará: fecha, hora, usuario y estados (anterior/nuevo)',
        ];
        foreach ($cambios as $c) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $c, 0, 'L');
        }
        $pdf->Ln(5);

        $pdf->SetFillColor(239, 246, 255);
        $pdf->SetTextColor(30, 64, 175);
        $pdf->Cell(0, 8, '  Nota: Solo ciertas transiciones son válidas por seguridad', 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'TRANSICIONES VÁLIDAS:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->MultiCell(0, 6, '• Pendiente → En proceso, Cancelada', 0, 'L');
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->MultiCell(0, 6, '• En proceso → Pausada, Completada, Cancelada', 0, 'L');
        $pdf->Cell(5, 6, '', 0, 0);
        $pdf->MultiCell(0, 6, '• Pausada → En proceso, Cancelada', 0, 'L');

        // Sección 7: Reportes
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '7. Módulo de Reportes');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, "El módulo de reportes permite visualizar y exportar estadísticas de la producción en formato PDF.", 0, 'J');
        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'TIPOS DE REPORTES DISPONIBLES:', 0, 1);
        $pdf->Ln(2);

        $reportes = [
            ['Reporte de Órdenes', 'Listado filtrable por fecha, estado, prioridad y empresa. Incluye código, producto, empresa y estado.'],
            ['Reporte de Tareas', 'Detalle de tareas con operario, máquina, tiempos estimados vs reales y unidades producidas.'],
            ['Resumen Estadístico', 'Dashboard con KPIs, gráficos de distribución y rendimiento por operario.'],
        ];

        foreach ($reportes as $r) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->Cell(0, 6, '• ' . $r[0], 0, 1);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(10, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $r[1], 0, 'J');
            $pdf->Ln(2);
        }

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'FILTROS DISPONIBLES:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $filtros = [
            '• Rango de fechas (desde/hasta)',
            '• Estado (pendiente, en proceso, completada, etc.)',
            '• Prioridad (baja, media, alta, urgente)',
            '• Empresa, operario o máquina específica',
        ];
        foreach ($filtros as $f) {
            $pdf->Cell(5, 6, '', 0, 0);
            $pdf->MultiCell(0, 6, $f, 0, 'L');
        }

        // Sección 8: FAQ
        $pdf->AddPage();
        $this->_pdfSeccion($pdf, '8. Preguntas Frecuentes');

        $faqs = [
            ['¿Cómo recupero mi contraseña?', 'Contacte al administrador del sistema para que restablezca su contraseña.'],
            ['¿Puedo ver el sistema desde mi celular?', 'Sí, el sistema es responsive y se adapta a dispositivos móviles.'],
            ['¿Qué pasa si cancelo una orden?', 'Las tareas asociadas también se cancelan y se registra en el historial.'],
            ['¿Cómo sé cuántas unidades ha producido un operario?', 'Use el Reporte de Tareas filtrado por operario y período.'],
            ['¿Puedo editar una orden completada?', 'No, las órdenes completadas o canceladas no pueden editarse.'],
            ['¿Dónde veo quién hizo cambios?', 'En el módulo de Seguimiento o en el historial del dashboard.'],
        ];

        foreach ($faqs as $faq) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetTextColor(30, 41, 59);
            $pdf->MultiCell(0, 6, 'P: ' . $faq[0], 0, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(0, 6, 'R: ' . $faq[1], 0, 'J');
            $pdf->Ln(4);
        }

        // Pie de página final
        $pdf->Ln(10);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, 'Documento generado el ' . date('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Cell(0, 6, 'Sistema de Gestión de Producción – DICO Telecomunicaciones', 0, 1, 'C');

        $pdf->Output('manual_usuario_gestion_taller.pdf', 'D');
    }
}
