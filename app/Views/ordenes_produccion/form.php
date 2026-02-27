<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $isEdit = isset($orden['id_orden']);
    $action = $isEdit
        ? site_url('ordenes-produccion/update/' . $orden['id_orden'])
        : site_url('ordenes-produccion/create');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><?= $isEdit ? 'Editar' : 'Nueva' ?> Orden de Produccion</h5>
    <a href="<?= site_url('ordenes-produccion') ?>" class="btn btn-outline-secondary btn-sm">
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
                    <label for="descripcion_producto" class="form-label">Descripcion del Producto <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= isset($errors['descripcion_producto']) ? 'is-invalid' : '' ?>"
                              id="descripcion_producto" name="descripcion_producto" rows="3"
                              required><?= esc($orden['descripcion_producto'] ?? '') ?></textarea>
                    <?php if (isset($errors['descripcion_producto'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['descripcion_producto']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="cantidad_solicitada" class="form-label">Cantidad Solicitada <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?= isset($errors['cantidad_solicitada']) ? 'is-invalid' : '' ?>"
                           id="cantidad_solicitada" name="cantidad_solicitada"
                           value="<?= esc($orden['cantidad_solicitada'] ?? '') ?>"
                           min="1" required>
                    <?php if (isset($errors['cantidad_solicitada'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['cantidad_solicitada']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="fecha_limite" class="form-label">Fecha Limite <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?= isset($errors['fecha_limite']) ? 'is-invalid' : '' ?>"
                           id="fecha_limite" name="fecha_limite"
                           value="<?= esc($orden['fecha_limite'] ?? '') ?>"
                           required>
                    <?php if (isset($errors['fecha_limite'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['fecha_limite']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['prioridad']) ? 'is-invalid' : '' ?>"
                            id="prioridad" name="prioridad" required>
                        <option value="">Seleccione...</option>
                        <option value="baja" <?= ($orden['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
                        <option value="media" <?= ($orden['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
                        <option value="alta" <?= ($orden['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
                        <option value="urgente" <?= ($orden['prioridad'] ?? '') === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                    </select>
                    <?php if (isset($errors['prioridad'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['prioridad']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['estado']) ? 'is-invalid' : '' ?>"
                            id="estado" name="estado" required>
                        <option value="">Seleccione...</option>
                        <option value="pendiente" <?= ($orden['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="en proceso" <?= ($orden['estado'] ?? '') === 'en proceso' ? 'selected' : '' ?>>En Proceso</option>
                        <option value="pausada" <?= ($orden['estado'] ?? '') === 'pausada' ? 'selected' : '' ?>>Pausada</option>
                        <option value="completada" <?= ($orden['estado'] ?? '') === 'completada' ? 'selected' : '' ?>>Completada</option>
                        <option value="cancelada" <?= ($orden['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                    <?php if (isset($errors['estado'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['estado']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="id_empresa" class="form-label">Empresa <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['id_empresa']) ? 'is-invalid' : '' ?>"
                            id="id_empresa" name="id_empresa" required>
                        <option value="">Seleccione una empresa...</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= esc($empresa['id_empresa']) ?>"
                                <?= ($orden['id_empresa'] ?? '') == $empresa['id_empresa'] ? 'selected' : '' ?>>
                                <?= esc($empresa['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_empresa'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['id_empresa']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?= esc($orden['observaciones'] ?? '') ?></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> <?= $isEdit ? 'Actualizar' : 'Guardar' ?>
                </button>
                <a href="<?= site_url('ordenes-produccion') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
