<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= site_url('reportes') ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
        <h2 class="mb-0 mt-1"><i class="bi bi-list-task me-2 text-success"></i><?= esc($title) ?></h2>
    </div>
    <a href="<?= site_url('reportes/pdf/tareas?' . http_build_query(array_filter($filtros))) ?>"
       class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
    </a>
</div>

<!-- ── Panel de filtros ───────────────────────────────────── -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0"><i class="bi bi-funnel me-2 text-muted"></i>Filtros de búsqueda</h6>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('reportes/tareas') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Búsqueda libre</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="busqueda" class="form-control"
                               placeholder="Nombre tarea, código orden…"
                               value="<?= esc($filtros['busqueda']) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Fecha desde</label>
                    <input type="date" name="fecha_desde" class="form-control"
                           value="<?= esc($filtros['fecha_desde']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Fecha hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control"
                           value="<?= esc($filtros['fecha_hasta']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach (['asignada','en proceso','pausada','completada','cancelada'] as $e): ?>
                        <option value="<?= $e ?>" <?= ($filtros['estado'] === $e) ? 'selected' : '' ?>>
                            <?= ucfirst($e) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Operario</label>
                    <select name="operario_id" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($operarios as $op): ?>
                        <option value="<?= $op['id_operario'] ?>"
                            <?= ($filtros['operario_id'] == $op['id_operario']) ? 'selected' : '' ?>>
                            <?= esc($op['nombre_completo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Máquina</label>
                    <select name="maquina_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($maquinas as $m): ?>
                        <option value="<?= $m['id_maquina'] ?>"
                            <?= ($filtros['maquina_id'] == $m['id_maquina']) ? 'selected' : '' ?>>
                            <?= esc($m['nombre_maquina']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-search me-1"></i> Aplicar filtros
                </button>
                <a href="<?= site_url('reportes/tareas') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ── Resumen rápido ─────────────────────────────────────── -->
<?php
$totalUnidades = array_sum(array_column($tareas, 'cantidad_producida'));
$totalEstimado = array_sum(array_column($tareas, 'tiempo_estimado_minutos'));
$totalReal     = array_sum(array_column($tareas, 'tiempo_real_minutos'));
$eficiencia    = $totalReal > 0 ? round(($totalEstimado / $totalReal) * 100, 1) : 0;
$completadas   = count(array_filter($tareas, fn($t) => $t['estado'] === 'completada'));
?>
<div class="row g-3 mb-4">
    <?php $cards = [
        ['Tareas encontradas',   count($tareas),                      'list-task',       'primary'],
        ['Completadas',          $completadas,                        'check-circle',    'success'],
        ['Unidades producidas',  number_format($totalUnidades),       'boxes',           'info'],
        ['Eficiencia tiempo',    $eficiencia . '%',                   'speedometer2',    'warning'],
    ]; foreach ($cards as $c): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <i class="bi bi-<?= $c[3] !== 'primary' ? $c[3] : 'list-task' ?> fs-4 text-<?= $c[3] ?>"></i>
                <div class="fs-4 fw-bold mt-1 text-<?= $c[3] ?>"><?= $c[1] ?></div>
                <small class="text-muted"><?= $c[0] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Tabla de resultados ────────────────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($tareas)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <h5>Sin resultados</h5>
            <p>No se encontraron tareas con los filtros aplicados.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Tarea</th>
                        <th>Orden</th>
                        <th>Operario</th>
                        <th>Máquina</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">T.Est.</th>
                        <th class="text-center">T.Real</th>
                        <th class="text-center">Efic.</th>
                        <th class="text-center">Uds.</th>
                        <th class="text-center">F. Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $idx => $t):
                        $estColor = match($t['estado']) {
                            'completada' => 'success',
                            'en proceso' => 'primary',
                            'pausada'    => 'secondary',
                            'cancelada'  => 'danger',
                            default      => 'info',
                        };
                        $efic = (($t['tiempo_estimado_minutos'] ?? 0) > 0 && ($t['tiempo_real_minutos'] ?? 0) > 0)
                            ? round(($t['tiempo_estimado_minutos'] / $t['tiempo_real_minutos']) * 100)
                            : null;
                        $eficColor = $efic === null ? 'secondary' : ($efic >= 100 ? 'success' : ($efic >= 75 ? 'warning' : 'danger'));
                    ?>
                    <tr>
                        <td class="text-muted small"><?= $idx + 1 ?></td>
                        <td>
                            <div class="fw-medium"><?= esc($t['nombre_tarea']) ?></div>
                            <?php if (!empty($t['descripcion'])): ?>
                            <small class="text-muted"><?= esc(substr($t['descripcion'], 0, 50)) ?>…</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= site_url('ordenes-produccion/ver/' . $t['id_orden']) ?>"
                               class="text-decoration-none fw-medium small">
                                <?= esc($t['codigo_orden'] ?? '-') ?>
                            </a>
                            <div class="text-muted small"><?= esc(substr($t['descripcion_producto'] ?? '', 0, 30)) ?></div>
                        </td>
                        <td><?= esc($t['operario_nombre'] ?? '-') ?></td>
                        <td><?= esc($t['nombre_maquina'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $estColor ?>"><?= ucfirst($t['estado']) ?></span>
                        </td>
                        <td class="text-center text-muted small">
                            <?= ($t['tiempo_estimado_minutos'] ?? null) ? $t['tiempo_estimado_minutos'] . ' min' : '-' ?>
                        </td>
                        <td class="text-center text-muted small">
                            <?= ($t['tiempo_real_minutos'] ?? null) ? $t['tiempo_real_minutos'] . ' min' : '-' ?>
                        </td>
                        <td class="text-center">
                            <?php if ($efic !== null): ?>
                            <span class="badge bg-<?= $eficColor ?>"><?= $efic ?>%</span>
                            <?php else: ?>
                            <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-medium"><?= number_format($t['cantidad_producida']) ?></td>
                        <td class="text-center text-muted small">
                            <?= !empty($t['fecha_asignacion']) ? date('d/m/Y', strtotime($t['fecha_asignacion'])) : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-top text-muted small d-flex justify-content-between flex-wrap gap-2">
            <span>Total: <strong><?= count($tareas) ?></strong> tareas</span>
            <span>Unidades producidas: <strong><?= number_format($totalUnidades) ?></strong></span>
            <span>Tiempo estimado: <strong><?= number_format($totalEstimado) ?> min</strong></span>
            <span>Tiempo real: <strong><?= number_format($totalReal) ?> min</strong></span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
