<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Resumen por estado -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        'en proceso' => ['label' => 'En Proceso',  'color' => 'primary',   'icon' => 'bi-play-fill'],
        'pausada'    => ['label' => 'Pausadas',     'color' => 'warning',   'icon' => 'bi-pause-fill'],
        'pendiente'  => ['label' => 'Pendientes',   'color' => 'secondary', 'icon' => 'bi-clock'],
        'completada' => ['label' => 'Completadas',  'color' => 'success',   'icon' => 'bi-check-circle-fill'],
        'cancelada'  => ['label' => 'Canceladas',   'color' => 'dark',      'icon' => 'bi-x-circle-fill'],
    ];
    ?>
    <?php foreach ($cards as $key => $card): ?>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="bi <?= $card['icon'] ?> text-<?= $card['color'] ?>" style="font-size:1.4rem"></i>
                    <div class="fs-2 fw-bold mt-1"><?= $resumen[$key] ?? 0 ?></div>
                    <div class="text-muted small"><?= $card['label'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Tabla de órdenes -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-table"></i> Estado de todas las órdenes</span>
        <a href="<?= site_url('ordenes-produccion/new') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Orden
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($ordenes)): ?>
            <p class="text-muted text-center py-5 mb-0">No hay órdenes registradas.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto / Cliente</th>
                            <th>Empresa</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha límite</th>
                            <th>Progreso</th>
                            <th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $o):
                            $badgeE = match($o['estado']) {
                                'en proceso' => 'bg-primary',
                                'pausada'    => 'bg-warning text-dark',
                                'pendiente'  => 'bg-secondary',
                                'completada' => 'bg-success',
                                'cancelada'  => 'bg-dark',
                                default      => 'bg-secondary',
                            };
                            $badgeP = match($o['prioridad']) {
                                'urgente' => 'bg-danger',
                                'alta'    => 'bg-warning text-dark',
                                'media'   => 'bg-info text-dark',
                                'baja'    => 'bg-secondary',
                                default   => 'bg-secondary',
                            };
                            $limite  = new DateTime($o['fecha_limite']);
                            $hoy     = new DateTime();
                            $vencida = ($hoy > $limite && ! in_array($o['estado'], ['completada','cancelada']));
                        ?>
                            <tr>
                                <td><code><?= esc($o['codigo_orden']) ?></code></td>
                                <td>
                                    <strong class="d-block"><?= esc($o['descripcion_producto']) ?></strong>
                                </td>
                                <td><small><?= esc($o['empresa_nombre'] ?? '—') ?></small></td>
                                <td><span class="badge <?= $badgeP ?>"><?= ucfirst(esc($o['prioridad'])) ?></span></td>
                                <td><span class="badge <?= $badgeE ?>"><?= ucfirst(esc($o['estado'])) ?></span></td>
                                <td>
                                    <small class="<?= $vencida ? 'text-danger fw-bold' : '' ?>">
                                        <?= $limite->format('d/m/Y') ?>
                                        <?= $vencida ? '<i class="bi bi-exclamation-triangle-fill"></i>' : '' ?>
                                    </small>
                                </td>
                                <td style="min-width:140px">
                                    <?php if ($o['total_tareas'] > 0): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:8px">
                                                <div class="progress-bar bg-success" style="width:<?= $o['porcentaje'] ?>%"></div>
                                            </div>
                                            <small class="text-nowrap"><?= $o['porcentaje'] ?>%</small>
                                        </div>
                                        <small class="text-muted">
                                            <?= $o['tareas_completadas'] ?>/<?= $o['total_tareas'] ?> tareas
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">Sin tareas</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('ordenes-produccion/ver/' . $o['id_orden']) ?>"
                                       class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
