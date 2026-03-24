<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= site_url('reportes') ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Reportes
        </a>
        <h2 class="mb-0 mt-1"><i class="bi bi-clipboard-data me-2 text-primary"></i><?= esc($title) ?></h2>
    </div>
    <a href="<?= site_url('reportes/pdf/ordenes?' . http_build_query(array_filter($filtros))) ?>"
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
        <form method="get" action="<?= site_url('reportes/ordenes') ?>" id="formFiltros">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Búsqueda libre</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="busqueda" class="form-control"
                               placeholder="Código, producto…"
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
                        <?php foreach (['pendiente','en proceso','pausada','completada','cancelada'] as $e): ?>
                        <option value="<?= $e ?>" <?= ($filtros['estado'] === $e) ? 'selected' : '' ?>>
                            <?= ucfirst($e) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach (['baja','media','alta','urgente'] as $p): ?>
                        <option value="<?= $p ?>" <?= ($filtros['prioridad'] === $p) ? 'selected' : '' ?>>
                            <?= ucfirst($p) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-medium">Empresa</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($empresas as $emp): ?>
                        <option value="<?= $emp['id_empresa'] ?>"
                            <?= ($filtros['empresa_id'] == $emp['id_empresa']) ? 'selected' : '' ?>>
                            <?= esc($emp['nombre']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Aplicar filtros
                </button>
                <a href="<?= site_url('reportes/ordenes') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- ── Resumen rápido ─────────────────────────────────────── -->
<?php
$conteo = ['pendiente'=>0,'en proceso'=>0,'pausada'=>0,'completada'=>0,'cancelada'=>0];
foreach ($ordenes as $o) { $conteo[$o['estado']] = ($conteo[$o['estado']] ?? 0) + 1; }
$colores = ['pendiente'=>'warning','en proceso'=>'primary','pausada'=>'secondary','completada'=>'success','cancelada'=>'danger'];
?>
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card border-0 bg-light">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3 flex-wrap">
                <span class="fw-bold">Resultados: <span class="text-primary"><?= count($ordenes) ?></span></span>
                <?php foreach ($conteo as $est => $cnt): if ($cnt === 0) continue; ?>
                <span class="badge bg-<?= $colores[$est] ?>"><?= ucfirst($est) ?>: <?= $cnt ?></span>
                <?php endforeach; ?>
                <?php if (array_filter($filtros)): ?>
                <span class="ms-auto badge bg-info-subtle text-info">
                    <i class="bi bi-funnel-fill me-1"></i>Filtros activos
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ── Tabla de resultados ────────────────────────────────── -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($ordenes)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <h5>Sin resultados</h5>
            <p>No se encontraron órdenes con los filtros aplicados.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaOrdenes">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Producto / Descripción</th>
                        <th>Empresa</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Prioridad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">F. Creación</th>
                        <th class="text-center">F. Límite</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ordenes as $o):
                        $prioColor = match($o['prioridad']) {
                            'urgente' => 'danger',
                            'alta'    => 'warning',
                            'media'   => 'info',
                            default   => 'secondary',
                        };
                        $estColor = match($o['estado']) {
                            'completada' => 'success',
                            'en proceso' => 'primary',
                            'pausada'    => 'secondary',
                            'cancelada'  => 'danger',
                            default      => 'warning',
                        };
                        $vencida = !empty($o['fecha_limite'])
                            && strtotime($o['fecha_limite']) < time()
                            && $o['estado'] !== 'completada'
                            && $o['estado'] !== 'cancelada';
                    ?>
                    <tr class="<?= $vencida ? 'table-danger' : '' ?>">
                        <td>
                            <a href="<?= site_url('ordenes-produccion/ver/' . $o['id_orden']) ?>"
                               class="fw-medium text-decoration-none">
                                <?= esc($o['codigo_orden']) ?>
                            </a>
                        </td>
                        <td>
                            <div class="fw-medium"><?= esc($o['descripcion_producto']) ?></div>
                            <?php if (!empty($o['especificaciones_tecnicas'])): ?>
                            <small class="text-muted"><?= esc(substr($o['especificaciones_tecnicas'], 0, 60)) ?>…</small>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($o['empresa_nombre'] ?? '-') ?></td>
                        <td class="text-center"><?= number_format($o['cantidad_solicitada']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $prioColor ?>"><?= strtoupper($o['prioridad']) ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= $estColor ?>"><?= ucfirst($o['estado']) ?></span>
                        </td>
                        <td class="text-center text-muted small">
                            <?= date('d/m/Y', strtotime($o['fecha_creacion'])) ?>
                        </td>
                        <td class="text-center small <?= $vencida ? 'text-danger fw-bold' : 'text-muted' ?>">
                            <?= !empty($o['fecha_limite']) ? date('d/m/Y', strtotime($o['fecha_limite'])) : '-' ?>
                            <?php if ($vencida): ?>
                            <i class="bi bi-exclamation-triangle-fill ms-1" title="Vencida"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-top text-muted small d-flex justify-content-between">
            <span>Total: <strong><?= count($ordenes) ?></strong> registros</span>
            <span>Unidades solicitadas: <strong><?= number_format(array_sum(array_column($ordenes, 'cantidad_solicitada'))) ?></strong></span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
