<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $isEdit = isset($tarea['id_tarea']);
    $action = $isEdit
        ? site_url('tareas-produccion/update/' . $tarea['id_tarea'])
        : site_url('tareas-produccion/create');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><?= $isEdit ? 'Editar' : 'Nueva' ?> Tarea de Produccion</h5>
    <a href="<?= site_url('tareas-produccion') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <?php if (! empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= $action ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="nombre_tarea" class="form-label">Nombre de la Tarea <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nombre_tarea']) ? 'is-invalid' : '' ?>"
                           id="nombre_tarea" name="nombre_tarea"
                           value="<?= esc($tarea['nombre_tarea'] ?? '') ?>"
                           required>
                    <?php if (isset($errors['nombre_tarea'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['nombre_tarea']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['estado']) ? 'is-invalid' : '' ?>"
                            id="estado" name="estado" required>
                        <option value="">Seleccione...</option>
                        <option value="asignada" <?= ($tarea['estado'] ?? '') === 'asignada' ? 'selected' : '' ?>>Asignada</option>
                        <option value="en proceso" <?= ($tarea['estado'] ?? '') === 'en proceso' ? 'selected' : '' ?>>En Proceso</option>
                        <option value="pausada" <?= ($tarea['estado'] ?? '') === 'pausada' ? 'selected' : '' ?>>Pausada</option>
                        <option value="completada" <?= ($tarea['estado'] ?? '') === 'completada' ? 'selected' : '' ?>>Completada</option>
                        <option value="cancelada" <?= ($tarea['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                    <?php if (isset($errors['estado'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['estado']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="descripcion" class="form-label">Descripcion</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?= esc($tarea['descripcion'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="id_orden" class="form-label">Orden de Produccion <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['id_orden']) ? 'is-invalid' : '' ?>"
                            id="id_orden" name="id_orden" required>
                        <option value="">Seleccione una orden...</option>
                        <?php foreach ($ordenes as $orden): ?>
                            <option value="<?= esc($orden['id_orden']) ?>"
                                <?= ($tarea['id_orden'] ?? '') == $orden['id_orden'] ? 'selected' : '' ?>>
                                <?= esc($orden['codigo_orden']) ?> - <?= esc($orden['descripcion_producto']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_orden'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_orden']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="id_maquina" class="form-label">Maquina <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['id_maquina']) ? 'is-invalid' : '' ?>"
                            id="id_maquina" name="id_maquina" required>
                        <option value="">Seleccione una maquina...</option>
                        <?php foreach ($maquinas as $maquina): ?>
                            <option value="<?= esc($maquina['id_maquina']) ?>"
                                <?= ($tarea['id_maquina'] ?? '') == $maquina['id_maquina'] ? 'selected' : '' ?>>
                                <?= esc($maquina['nombre_maquina']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_maquina'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_maquina']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="id_operario" class="form-label">Operario <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['id_operario']) ? 'is-invalid' : '' ?>"
                            id="id_operario" name="id_operario" required>
                        <option value="">Seleccione un operario...</option>
                        <?php foreach ($operarios as $operario): ?>
                            <option value="<?= esc($operario['id_operario']) ?>"
                                <?= ($tarea['id_operario'] ?? '') == $operario['id_operario'] ? 'selected' : '' ?>>
                                <?= esc($operario['nombre_completo']) ?> (<?= esc($operario['nivel_experiencia']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_operario'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_operario']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="datetime-local" class="form-control"
                           id="fecha_inicio" name="fecha_inicio"
                           value="<?= esc($tarea['fecha_inicio'] ?? '') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="datetime-local" class="form-control"
                           id="fecha_fin" name="fecha_fin"
                           value="<?= esc($tarea['fecha_fin'] ?? '') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="cantidad_producida" class="form-label">Cantidad Producida</label>
                    <input type="number" class="form-control"
                           id="cantidad_producida" name="cantidad_producida"
                           value="<?= esc($tarea['cantidad_producida'] ?? '0') ?>"
                           min="0">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?= esc($tarea['observaciones'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= site_url('tareas-produccion') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
