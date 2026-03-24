<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i><?= esc($title) ?></h2>
        <p class="text-muted mb-0">Semana 7 – Visualización y análisis de datos de producción</p>
    </div>
    <span class="badge bg-secondary fs-6"><?= date('d/m/Y') ?></span>
</div>

<?php
// ── Helpers de colores por estado ──
function badgeOrden(string $estado): string {
    return match($estado) {
        'pendiente'  => 'warning',
        'en proceso' => 'primary',
        'pausada'    => 'secondary',
        'completada' => 'success',
        'cancelada'  => 'danger',
        default      => 'light',
    };
}
function badgeTarea(string $estado): string {
    return match($estado) {
        'asignada'   => 'info',
        'en proceso' => 'primary',
        'pausada'    => 'secondary',
        'completada' => 'success',
        'cancelada'  => 'danger',
        default      => 'light',
    };
}
?>

<!-- ── KPI Cards órdenes ───────────────────────────────── -->
<div class="row g-3 mb-4">
    <?php
    $kpisOrdenes = [
        ['label'=>'Total Órdenes',    'val'=>$estadOrdenes['total'],        'icon'=>'clipboard-data',      'color'=>'primary'],
        ['label'=>'Pendientes',       'val'=>$estadOrdenes['pendiente'],    'icon'=>'hourglass-split',     'color'=>'warning'],
        ['label'=>'En Proceso',       'val'=>$estadOrdenes['en proceso'],   'icon'=>'arrow-repeat',        'color'=>'info'],
        ['label'=>'Completadas',      'val'=>$estadOrdenes['completada'],   'icon'=>'check-circle',        'color'=>'success'],
        ['label'=>'Pausadas',         'val'=>$estadOrdenes['pausada'],      'icon'=>'pause-circle',        'color'=>'secondary'],
        ['label'=>'Canceladas',       'val'=>$estadOrdenes['cancelada'],    'icon'=>'x-circle',            'color'=>'danger'],
    ];
    foreach ($kpisOrdenes as $k): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <i class="bi bi-<?= $k['icon'] ?> fs-3 text-<?= $k['color'] ?>"></i>
                <div class="display-6 fw-bold mt-1"><?= $k['val'] ?></div>
                <small class="text-muted"><?= $k['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Accesos directos a reportes ───────────────────────── -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 p-3 me-3" style="background:#dbeafe">
                        <i class="bi bi-clipboard-data fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Órdenes de Producción</h5>
                        <small class="text-muted">Filtrar por fecha, estado y prioridad</small>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1">
                    Listado detallado de todas las órdenes con filtros por rango de fecha, estado, prioridad y empresa. Exportable a PDF.
                </p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="<?= site_url('reportes/ordenes') ?>" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Ver Reporte
                    </a>
                    <a href="<?= site_url('reportes/pdf/ordenes') ?>" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 p-3 me-3" style="background:#dcfce7">
                        <i class="bi bi-list-task fs-3 text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Tareas de Producción</h5>
                        <small class="text-muted">Filtrar por operario, máquina y estado</small>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1">
                    Análisis de tareas con tiempos estimados vs reales, unidades producidas por operario y máquina. Exportable a PDF.
                </p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="<?= site_url('reportes/tareas') ?>" class="btn btn-success flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Ver Reporte
                    </a>
                    <a href="<?= site_url('reportes/pdf/tareas') ?>" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 p-3 me-3" style="background:#fef3c7">
                        <i class="bi bi-graph-up-arrow fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Resumen Estadístico</h5>
                        <small class="text-muted">Gráficos y rendimiento por empresa</small>
                    </div>
                </div>
                <p class="text-muted small flex-grow-1">
                    Vista consolidada con gráficos de distribución, rendimiento por operario y producción por empresa. Exportable a PDF.
                </p>
                <div class="d-flex gap-2 mt-auto">
                    <a href="<?= site_url('reportes/resumen') ?>" class="btn btn-warning flex-grow-1">
                        <i class="bi bi-eye me-1"></i> Ver Resumen
                    </a>
                    <a href="<?= site_url('reportes/pdf/resumen') ?>" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Manual de Usuario ─────────────────────────────── -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-md-row flex-column align-items-md-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="rounded-3 p-3 me-3" style="background:#f3e8ff">
                        <i class="bi bi-book fs-3 text-purple" style="color:#7c3aed"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Manual de Usuario</h5>
                        <small class="text-muted">Guía completa del sistema</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="text-muted small align-self-center me-2 d-none d-md-block">
                        <i class="bi bi-info-circle me-1"></i> Versión 1.0 – Marzo 2026
                    </span>
                    <a href="<?= site_url('manual-usuario') ?>" class="btn btn-purple" style="background:#7c3aed;color:white">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Historial reciente ─────────────────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-muted"></i>Historial de cambios recientes</h6>
        <a href="<?= site_url('seguimiento') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> Ver seguimiento
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($historial)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i> Sin cambios registrados
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Entidad</th>
                        <th>Estado anterior</th>
                        <th>Estado nuevo</th>
                        <th>Responsable</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td>
                            <?php if ($h['tipo_entidad'] === 'orden'): ?>
                            <span class="badge bg-primary-subtle text-primary">Orden</span>
                            <?php else: ?>
                            <span class="badge bg-success-subtle text-success">Tarea</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($h['entidad_nombre'] ?? '-') ?></td>
                        <td><span class="badge bg-secondary"><?= esc($h['estado_anterior']) ?></span></td>
                        <td><span class="badge bg-primary"><?= esc($h['estado_nuevo']) ?></span></td>
                        <td><?= esc($h['responsable'] ?? '-') ?></td>
                        <td class="text-muted"><?= date('d/m/Y H:i', strtotime($h['fecha_cambio'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
