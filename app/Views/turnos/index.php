<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Turnos</h5>
    <button class="btn btn-primary btn-sm" disabled>
        <i class="bi bi-plus-lg"></i> Nuevo
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="text-muted text-center py-4 mb-0">
            <i class="bi bi-clock" style="font-size:2rem;"></i><br>
            Modulo de Turnos<br>
            <small>Proximamente</small>
        </p>
    </div>
</div>

<?= $this->endSection() ?>
