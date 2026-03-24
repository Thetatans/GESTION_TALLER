<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="text-center" style="max-width: 480px;">
        <div style="font-size: 5rem; line-height: 1; color: #dc2626; margin-bottom: 16px;">
            <i class="bi bi-shield-x"></i>
        </div>
        <h1 class="fw-bold mb-2" style="font-size: 2rem; color: #1e293b;">403 – Acceso Denegado</h1>
        <p class="text-muted mb-4">
            No tiene permisos para acceder a este módulo.<br>
            Su rol (<strong><?= esc(session()->get('rol') ?? 'desconocido') ?></strong>) no tiene acceso a esta sección.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">
                <i class="bi bi-speedometer2 me-2"></i>Ir al Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
