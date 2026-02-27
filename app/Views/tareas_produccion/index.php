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
    <h5 class="mb-0">Tareas de Produccion</h5>
    <a href="<?= site_url('tareas-produccion/new') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Nueva Tarea
    </a>
</div>

<!-- Tabla -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($tareas)): ?>
            <p class="text-muted text-center py-4 mb-0">
                <i class="bi bi-list-task" style="font-size:2rem;"></i><br>
                No se encontraron tareas de produccion.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaTareas">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Tarea</th>
                            <th>Orden</th>
                            <th>Maquina</th>
                            <th>Operario</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Cant. Producida</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $tarea): ?>
                            <tr>
                                <td><?= esc($tarea['id_tarea']) ?></td>
                                <td><?= esc($tarea['nombre_tarea']) ?></td>
                                <td>
                                    <small><code><?= esc($tarea['codigo_orden'] ?? '') ?></code></small><br>
                                    <small class="text-muted"><?= esc($tarea['descripcion_producto'] ?? 'N/A') ?></small>
                                </td>
                                <td><?= esc($tarea['nombre_maquina'] ?? 'N/A') ?></td>
                                <td><?= esc($tarea['operario_nombre'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($tarea['fecha_inicio']): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($tarea['fecha_inicio'])) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tarea['fecha_fin']): ?>
                                        <small><?= date('d/m/Y H:i', strtotime($tarea['fecha_fin'])) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $badgeEstado = match($tarea['estado']) {
                                            'asignada'   => 'bg-info text-dark',
                                            'en proceso' => 'bg-primary',
                                            'pausada'    => 'bg-warning text-dark',
                                            'completada' => 'bg-success',
                                            'cancelada'  => 'bg-dark',
                                            default      => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeEstado ?>"><?= ucfirst(esc($tarea['estado'])) ?></span>
                                </td>
                                <td><?= esc($tarea['cantidad_producida'] ?? 0) ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('ordenes-produccion/ver/' . $tarea['id_orden']) ?>" class="btn btn-sm btn-outline-primary" title="Ver orden / Seguimiento">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= site_url('tareas-produccion/edit/' . $tarea['id_tarea']) ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= site_url('tareas-produccion/delete/' . $tarea['id_tarea']) ?>" method="post" class="d-inline form-delete">
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
            if (!confirm('¿Esta seguro de que desea eliminar esta tarea de produccion?')) {
                e.preventDefault();
            }
        });
    });
</script>
<?= $this->endSection() ?>
