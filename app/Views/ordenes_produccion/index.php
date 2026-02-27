<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Mensajes flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Ordenes de Produccion</h5>
    <a href="<?= site_url('ordenes-produccion/new') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Nueva Orden
    </a>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="get" action="<?= site_url('ordenes-produccion') ?>" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="pendiente" <?= ($filtroEstado ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en proceso" <?= ($filtroEstado ?? '') === 'en proceso' ? 'selected' : '' ?>>En Proceso</option>
                    <option value="pausada" <?= ($filtroEstado ?? '') === 'pausada' ? 'selected' : '' ?>>Pausada</option>
                    <option value="completada" <?= ($filtroEstado ?? '') === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="cancelada" <?= ($filtroEstado ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0 small">Prioridad</label>
                <select name="prioridad" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="urgente" <?= ($filtroPrioridad ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                    <option value="alta" <?= ($filtroPrioridad ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="media" <?= ($filtroPrioridad ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="baja" <?= ($filtroPrioridad ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="<?= site_url('ordenes-produccion') ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($ordenes)): ?>
            <p class="text-muted text-center py-4 mb-0">
                <i class="bi bi-clipboard-data" style="font-size:2rem;"></i><br>
                No se encontraron ordenes de produccion.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaOrdenes">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Codigo</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Fecha Limite</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Empresa</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $orden): ?>
                            <tr>
                                <td><?= esc($orden['id_orden']) ?></td>
                                <td><code><?= esc($orden['codigo_orden']) ?></code></td>
                                <td><?= esc($orden['descripcion_producto']) ?></td>
                                <td><?= esc($orden['cantidad_solicitada']) ?></td>
                                <td><?= esc($orden['fecha_limite']) ?></td>
                                <td>
                                    <?php
                                        $badgePrioridad = match($orden['prioridad']) {
                                            'urgente' => 'bg-danger',
                                            'alta'    => 'bg-warning text-dark',
                                            'media'   => 'bg-info text-dark',
                                            'baja'    => 'bg-secondary',
                                            default   => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgePrioridad ?>"><?= ucfirst(esc($orden['prioridad'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                        $badgeEstado = match($orden['estado']) {
                                            'pendiente'  => 'bg-secondary',
                                            'en proceso' => 'bg-primary',
                                            'pausada'    => 'bg-warning text-dark',
                                            'completada' => 'bg-success',
                                            'cancelada'  => 'bg-dark',
                                            default      => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeEstado ?>"><?= ucfirst(esc($orden['estado'])) ?></span>
                                </td>
                                <td><?= esc($orden['empresa_nombre'] ?? 'N/A') ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('ordenes-produccion/ver/' . $orden['id_orden']) ?>" class="btn btn-sm btn-outline-primary" title="Ver detalle / Seguimiento">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= site_url('ordenes-produccion/edit/' . $orden['id_orden']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= site_url('ordenes-produccion/delete/' . $orden['id_orden']) ?>" method="post" class="d-inline form-delete">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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

<?= $this->section('scripts') ?>
<script>
    // Confirmacion de eliminacion
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('¿Esta seguro de que desea eliminar esta orden de produccion?')) {
                e.preventDefault();
            }
        });
    });
</script>
<?= $this->endSection() ?>
