<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// ─── helpers (closures para evitar redeclaración) ──────────
$badgeEstado = fn($e) => match($e) {
    'pendiente'  => 'bg-secondary',
    'en proceso' => 'bg-primary',
    'pausada'    => 'bg-warning text-dark',
    'completada' => 'bg-success',
    'cancelada'  => 'bg-dark',
    'asignada'   => 'bg-info text-dark',
    default      => 'bg-secondary',
};
$iconEstado = fn($e) => match($e) {
    'pendiente'  => 'bi-clock',
    'en proceso' => 'bi-play-fill',
    'pausada'    => 'bi-pause-fill',
    'completada' => 'bi-check-circle-fill',
    'cancelada'  => 'bi-x-circle-fill',
    'asignada'   => 'bi-person-check-fill',
    default      => 'bi-question-circle',
};
$badgePrioridad = fn($p) => match($p) {
    'urgente' => 'bg-danger',
    'alta'    => 'bg-warning text-dark',
    'media'   => 'bg-info text-dark',
    'baja'    => 'bg-secondary',
    default   => 'bg-secondary',
};
?>

<!-- Flash messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Encabezado -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="<?= site_url('ordenes-produccion') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <span class="fs-5 fw-semibold"><?= esc($orden['codigo_orden']) ?></span>
        <span class="ms-2 badge <?= $badgeEstado($orden['estado']) ?>">
            <i class="bi <?= $iconEstado($orden['estado']) ?>"></i> <?= ucfirst(esc($orden['estado'])) ?>
        </span>
        <span class="ms-1 badge <?= $badgePrioridad($orden['prioridad']) ?>">
            <?= ucfirst(esc($orden['prioridad'])) ?>
        </span>
    </div>
    <?php if (! empty($transicionesOrden)): ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalOrden">
            <i class="bi bi-arrow-left-right"></i> Cambiar Estado
        </button>
    <?php else: ?>
        <span class="badge bg-light text-secondary border">Estado final — sin cambios posibles</span>
    <?php endif; ?>
</div>

<!-- Info de la orden -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><span class="text-muted small">Producto</span><br>
                    <strong><?= esc($orden['descripcion_producto']) ?></strong></p>
                <p class="mb-0"><span class="text-muted small">Empresa</span><br>
                    <?= esc($orden['empresa_nombre'] ?? '—') ?></p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><span class="text-muted small">Cantidad solicitada</span><br>
                    <strong><?= number_format($orden['cantidad_solicitada']) ?></strong></p>
                <p class="mb-1"><span class="text-muted small">Fecha límite</span><br>
                    <?php
                        $limite = new DateTime($orden['fecha_limite']);
                        $hoy    = new DateTime();
                        $vencida = ($hoy > $limite && ! in_array($orden['estado'], ['completada','cancelada']));
                    ?>
                    <span class="<?= $vencida ? 'text-danger fw-bold' : '' ?>">
                        <?= $limite->format('d/m/Y') ?>
                        <?= $vencida ? '<i class="bi bi-exclamation-triangle-fill"></i>' : '' ?>
                    </span>
                </p>
                <?php if ($orden['fecha_completada']): ?>
                    <p class="mb-0"><span class="text-muted small">Completada</span><br>
                        <?= date('d/m/Y H:i', strtotime($orden['fecha_completada'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <?php if ($orden['observaciones']): ?>
                    <p class="mb-1"><span class="text-muted small">Observaciones</span><br>
                        <small><?= nl2br(esc($orden['observaciones'])) ?></small></p>
                <?php endif; ?>
                <?php if ($orden['fecha_creacion']): ?>
                    <p class="mb-0"><span class="text-muted small">Creada</span><br>
                        <small><?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></small></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Progreso -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold"><?= $totalTareas ?></div>
                <div class="text-muted small">Tareas totales</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success"><?= $tareasCompletadas ?></div>
                <div class="text-muted small">Completadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary"><?= $tareasEnProceso ?></div>
                <div class="text-muted small">En proceso</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning"><?= $tareasPausadas + $tareasAsignadas ?></div>
                <div class="text-muted small">Pendientes / Pausadas</div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Progreso de tareas</span>
                    <strong><?= $porcentaje ?>%</strong>
                </div>
                <div class="progress" style="height:12px;">
                    <div class="progress-bar bg-success progress-bar-striped" style="width:<?= $porcentaje ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de tareas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-list-task"></i> Tareas de esta orden</span>
        <a href="<?= site_url('tareas-produccion/new') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus-lg"></i> Nueva Tarea
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($tareas)): ?>
            <p class="text-muted text-center py-4 mb-0">
                <i class="bi bi-list-task" style="font-size:2rem"></i><br>
                No hay tareas registradas para esta orden.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tarea</th>
                            <th>Máquina</th>
                            <th>Operario</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Cant.</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $i => $t): ?>
                            <?php $siguientes = $transicionesTarea[$t['estado']] ?? []; ?>
                            <tr>
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= esc($t['nombre_tarea']) ?></strong>
                                    <?php if ($t['descripcion']): ?>
                                        <br><small class="text-muted"><?= esc($t['descripcion']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><small><?= esc($t['nombre_maquina'] ?? '—') ?></small></td>
                                <td><small><?= esc($t['operario_nombre'] ?? '—') ?></small></td>
                                <td>
                                    <small><?= $t['fecha_inicio'] ? date('d/m/Y H:i', strtotime($t['fecha_inicio'])) : '—' ?></small>
                                </td>
                                <td>
                                    <small><?= $t['fecha_fin'] ? date('d/m/Y H:i', strtotime($t['fecha_fin'])) : '—' ?></small>
                                </td>
                                <td><?= esc($t['cantidad_producida'] ?? 0) ?></td>
                                <td>
                                    <span class="badge <?= $badgeEstado($t['estado']) ?>">
                                        <i class="bi <?= $iconEstado($t['estado']) ?>"></i>
                                        <?= ucfirst(esc($t['estado'])) ?>
                                    </span>
                                    <?php if ($t['observaciones']): ?>
                                        <i class="bi bi-chat-dots text-muted ms-1" title="<?= esc($t['observaciones']) ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (! empty($siguientes)): ?>
                                        <button class="btn btn-sm btn-outline-primary btn-cambiar-tarea"
                                            data-tarea-id="<?= $t['id_tarea'] ?>"
                                            data-tarea-nombre="<?= esc($t['nombre_tarea']) ?>"
                                            data-estado-actual="<?= esc($t['estado']) ?>"
                                            data-siguientes='<?= json_encode($siguientes) ?>'
                                            data-bs-toggle="modal" data-bs-target="#modalTarea"
                                            title="Cambiar estado">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                    <a href="<?= site_url('tareas-produccion/edit/' . $t['id_tarea']) ?>"
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
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

<!-- Historial de estados -->
<?php $todoHistorial = array_merge($historialOrden, $historialTareas);
usort($todoHistorial, fn($a, $b) => strcmp($b['fecha_cambio'], $a['fecha_cambio'])); ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-semibold"><i class="bi bi-clock-history"></i> Historial de cambios de estado</span>
    </div>
    <div class="card-body">
        <?php if (empty($todoHistorial)): ?>
            <p class="text-muted text-center mb-0">
                <i class="bi bi-clock-history" style="font-size:1.5rem"></i><br>
                Aún no hay cambios de estado registrados.
            </p>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($todoHistorial as $h): ?>
                    <div class="d-flex gap-3 mb-3">
                        <div class="flex-shrink-0 text-center" style="width:40px;">
                            <span class="badge rounded-pill <?= $badgeEstado($h['estado_nuevo']) ?> p-2">
                                <i class="bi <?= $iconEstado($h['estado_nuevo']) ?>"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <?= $h['tipo_entidad'] === 'orden' ? '<i class="bi bi-clipboard-data text-primary"></i> Orden' : '<i class="bi bi-list-task text-secondary"></i> Tarea #' . esc($h['id_entidad']) ?>
                                    &nbsp;
                                    <span class="badge <?= $badgeEstado($h['estado_anterior']) ?>"><?= ucfirst(esc($h['estado_anterior'])) ?></span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="badge <?= $badgeEstado($h['estado_nuevo']) ?>"><?= ucfirst(esc($h['estado_nuevo'])) ?></span>
                                </span>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($h['fecha_cambio'])) ?></small>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-person"></i> <?= esc($h['responsable'] ?? 'Sistema') ?>
                                <?php if ($h['observaciones']): ?>
                                    &mdash; <?= esc($h['observaciones']) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===================== MODAL: Cambiar estado de la ORDEN ===================== -->
<div class="modal fade" id="modalOrden" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> Cambiar estado de la orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= site_url('ordenes-produccion/cambiar-estado/' . $orden['id_orden']) ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="mb-3">Estado actual:
                        <span class="badge <?= $badgeEstado($orden['estado']) ?> ms-1">
                            <?= ucfirst(esc($orden['estado'])) ?>
                        </span>
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo estado <span class="text-danger">*</span></label>
                        <?php foreach ($transicionesOrden as $t): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nuevo_estado"
                                       id="ord_<?= $t ?>" value="<?= $t ?>" required>
                                <label class="form-check-label" for="ord_<?= $t ?>">
                                    <span class="badge <?= $badgeEstado($t) ?>">
                                        <i class="bi <?= $iconEstado($t) ?>"></i> <?= ucfirst($t) ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <label for="ord_responsable" class="form-label">Responsable <span class="text-danger">*</span></label>
                        <select id="ord_responsable" name="id_responsable" class="form-select" required>
                            <option value="">— Seleccionar responsable —</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>"><?= esc($u['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ord_obs" class="form-label">Observaciones</label>
                        <textarea id="ord_obs" name="observaciones" class="form-control" rows="3"
                                  placeholder="Motivo o notas del cambio..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Confirmar cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== MODAL: Cambiar estado de TAREA ===================== -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list-task"></i> Cambiar estado de tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="formCambiarTarea">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="mb-1 text-muted small">Tarea:</p>
                    <p class="fw-semibold mb-3" id="modalTareaNombre">—</p>

                    <p class="mb-3">Estado actual:
                        <span class="badge bg-secondary ms-1" id="modalTareaEstadoActual">—</span>
                    </p>

                    <div class="mb-3" id="contenedorRadiosTarea">
                        <!-- Radios generados por JS -->
                    </div>

                    <div class="mb-3">
                        <label for="tar_responsable" class="form-label">Responsable <span class="text-danger">*</span></label>
                        <select id="tar_responsable" name="id_responsable" class="form-select" required>
                            <option value="">— Seleccionar responsable —</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>"><?= esc($u['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tar_obs" class="form-label">Observaciones</label>
                        <textarea id="tar_obs" name="observaciones" class="form-control" rows="3"
                                  placeholder="Motivo o notas del cambio..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Confirmar cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const badgeClases = {
    'asignada':   'bg-info text-dark',
    'en proceso': 'bg-primary',
    'pausada':    'bg-warning text-dark',
    'completada': 'bg-success',
    'cancelada':  'bg-dark',
    'pendiente':  'bg-secondary',
};
const iconClases = {
    'asignada':   'bi-person-check-fill',
    'en proceso': 'bi-play-fill',
    'pausada':    'bi-pause-fill',
    'completada': 'bi-check-circle-fill',
    'cancelada':  'bi-x-circle-fill',
    'pendiente':  'bi-clock',
};

document.querySelectorAll('.btn-cambiar-tarea').forEach(btn => {
    btn.addEventListener('click', () => {
        const tareaId     = btn.dataset.tareaId;
        const nombre      = btn.dataset.tareaNombre;
        const estadoActual = btn.dataset.estadoActual;
        const siguientes  = JSON.parse(btn.dataset.siguientes);

        document.getElementById('modalTareaNombre').textContent = nombre;

        const spanEstado = document.getElementById('modalTareaEstadoActual');
        spanEstado.textContent = estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1);
        spanEstado.className   = 'badge ms-1 ' + (badgeClases[estadoActual] || 'bg-secondary');

        const contenedor = document.getElementById('contenedorRadiosTarea');
        contenedor.innerHTML = '<label class="form-label fw-semibold">Nuevo estado <span class="text-danger">*</span></label>';

        siguientes.forEach(s => {
            const icon  = iconClases[s] || 'bi-question-circle';
            const badge = badgeClases[s] || 'bg-secondary';
            const label = s.charAt(0).toUpperCase() + s.slice(1);
            contenedor.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="nuevo_estado"
                           id="tar_${s}" value="${s}" required>
                    <label class="form-check-label" for="tar_${s}">
                        <span class="badge ${badge}">
                            <i class="bi ${icon}"></i> ${label}
                        </span>
                    </label>
                </div>`;
        });

        document.getElementById('formCambiarTarea').action =
            '<?= site_url('tareas-produccion/cambiar-estado/') ?>' + tareaId;

        // Limpiar campos anteriores
        document.getElementById('tar_responsable').selectedIndex = 0;
        document.getElementById('tar_obs').value = '';
    });
});
</script>
<?= $this->endSection() ?>
