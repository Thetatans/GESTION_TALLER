<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= site_url('reportes') ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
        <h2 class="mb-0 mt-1"><i class="bi bi-graph-up-arrow me-2 text-warning"></i><?= esc($title) ?></h2>
    </div>
    <a href="<?= site_url('reportes/pdf/resumen') ?>" class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
    </a>
</div>

<!-- ── KPIs principales ───────────────────────────────────── -->
<div class="row g-3 mb-4">
    <?php
    $eficiencia = 0;
    if ($estadTareas['total'] > 0) {
        $eficiencia = round(($estadTareas['completada'] / $estadTareas['total']) * 100, 1);
    }
    $kpis = [
        ['Total Órdenes',     $estadOrdenes['total'],      'clipboard-data',  'primary'],
        ['Órdenes Completadas', $estadOrdenes['completada'], 'check2-circle', 'success'],
        ['Total Tareas',      $estadTareas['total'],       'list-task',       'info'],
        ['Tareas Completadas', $estadTareas['completada'], 'check-circle',    'success'],
        ['En Proceso',        $estadOrdenes['en proceso'], 'arrow-repeat',    'warning'],
        ['Eficiencia Tareas', $eficiencia . '%',           'speedometer2',    'danger'],
    ];
    foreach ($kpis as $k): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body p-3">
                <i class="bi bi-<?= $k[2] ?> fs-3 text-<?= $k[3] ?>"></i>
                <div class="display-6 fw-bold mt-2 text-<?= $k[3] ?>"><?= $k[1] ?></div>
                <small class="text-muted"><?= $k[0] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Producción por empresa ─────────────────────────────── -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
        <h6 class="mb-0"><i class="bi bi-building me-2 text-muted"></i>Producción por Empresa</h6>
    </div>
    <?php if (empty($porEmpresa)): ?>
    <div class="card-body text-center text-muted py-4">Sin datos</div>
    <?php else: ?>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Empresa</th>
                        <th class="text-center">Total Órdenes</th>
                        <th class="text-center">Completadas</th>
                        <th class="text-center">En Proceso</th>
                        <th class="text-center">Uds. Producidas</th>
                        <th style="min-width:150px">Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porEmpresa as $e):
                        $pct = $e['total_ordenes'] > 0
                            ? round(($e['completadas'] / $e['total_ordenes']) * 100)
                            : 0;
                    ?>
                    <tr>
                        <td class="fw-medium"><?= esc($e['empresa']) ?></td>
                        <td class="text-center"><?= $e['total_ordenes'] ?></td>
                        <td class="text-center">
                            <span class="badge bg-success"><?= $e['completadas'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?= $e['en_proceso'] ?></span>
                        </td>
                        <td class="text-center fw-bold text-info"><?= number_format($e['unidades_producidas']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px">
                                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $pct ?>%</small>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Rendimiento operarios ──────────────────────────────── -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0"><i class="bi bi-people me-2 text-muted"></i>Rendimiento de Operarios</h6>
    </div>
    <?php if (empty($rendimiento)): ?>
    <div class="card-body text-center text-muted py-4">Sin datos de operarios</div>
    <?php else: ?>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Operario</th>
                        <th class="text-center">Total Tareas</th>
                        <th class="text-center">Completadas</th>
                        <th class="text-center">En Proceso</th>
                        <th class="text-center">Uds. Producidas</th>
                        <th class="text-center">T. Promedio</th>
                        <th style="min-width:130px">Eficiencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rendimiento as $r):
                        $pct = $r['total_tareas'] > 0
                            ? round(($r['completadas'] / $r['total_tareas']) * 100)
                            : 0;
                        $barColor = $pct >= 75 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
                    ?>
                    <tr>
                        <td class="fw-medium"><?= esc($r['operario']) ?></td>
                        <td class="text-center"><?= $r['total_tareas'] ?></td>
                        <td class="text-center"><span class="badge bg-success"><?= $r['completadas'] ?></span></td>
                        <td class="text-center"><span class="badge bg-primary"><?= $r['en_proceso'] ?></span></td>
                        <td class="text-center fw-bold"><?= number_format($r['unidades']) ?></td>
                        <td class="text-center text-muted small">
                            <?= $r['tiempo_promedio'] > 0 ? round($r['tiempo_promedio'], 1) . ' min' : '-' ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px">
                                    <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= $pct ?>%"></div>
                                </div>
                                <small class="text-muted"><?= $pct ?>%</small>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- ── Historial de cambios recientes ────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-muted"></i>Historial de cambios de estado (últimos 20)</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($historial)): ?>
        <div class="text-center py-4 text-muted">Sin historial registrado</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Tipo</th>
                        <th>Entidad</th>
                        <th>De</th>
                        <th>A</th>
                        <th>Responsable</th>
                        <th>Observaciones</th>
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
                        <td class="text-muted"><?= esc(substr($h['observaciones'] ?? '', 0, 40)) ?></td>
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
