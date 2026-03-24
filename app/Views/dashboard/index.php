<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Boton Manual de Usuario -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
        <p class="text-muted mb-0">Panel de control del sistema</p>
    </div>
    <a href="<?= base_url('docs/manual_usuario.pdf') ?>" class="btn btn-purple" style="background:#7c3aed;color:white" target="_blank">
        <i class="bi bi-book me-2"></i>Manual de Usuario
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-data text-primary" style="font-size:2rem;"></i>
                <h6 class="mt-2 mb-0">Ordenes</h6>
                <small class="text-muted">Produccion</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-cpu text-success" style="font-size:2rem;"></i>
                <h6 class="mt-2 mb-0">Maquinas</h6>
                <small class="text-muted">Equipos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-people text-warning" style="font-size:2rem;"></i>
                <h6 class="mt-2 mb-0">Operarios</h6>
                <small class="text-muted">Personal</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-info" style="font-size:2rem;"></i>
                <h6 class="mt-2 mb-0">Productos</h6>
                <small class="text-muted">Terminados</small>
            </div>
        </div>
    </div>
</div>

<h6 class="text-muted mb-3">Modulos del Sistema</h6>

<div class="row g-3">
    <div class="col-md-4">
        <a href="<?= site_url('ordenes-produccion') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-clipboard-data text-primary"></i> Ordenes de Produccion</h6>
                    <p class="text-muted small mb-0">Gestionar pedidos y ordenes de fabricacion</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('tareas-produccion') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-list-task text-primary"></i> Tareas de Produccion</h6>
                    <p class="text-muted small mb-0">Subprocesos: cortar, tornear, soldar, etc.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('maquinas') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-cpu text-success"></i> Maquinas</h6>
                    <p class="text-muted small mb-0">Tornos, fresadoras, cortadoras laser</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('inspecciones') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-search text-danger"></i> Inspecciones de Calidad</h6>
                    <p class="text-muted small mb-0">Aprobado, retrabajo o rechazado</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('tipos-defecto') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-exclamation-triangle text-warning"></i> Tipos de Defecto</h6>
                    <p class="text-muted small mb-0">Catalogo de defectos comunes</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('productos-terminados') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-box-seam text-info"></i> Productos Terminados</h6>
                    <p class="text-muted small mb-0">Productos listos para despacho</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('almacenes') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-building text-secondary"></i> Almacenes</h6>
                    <p class="text-muted small mb-0">Bodegas y capacidad de almacenamiento</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('operarios') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-people text-warning"></i> Operarios</h6>
                    <p class="text-muted small mb-0">Personal, habilidades y certificaciones</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('turnos') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-clock text-dark"></i> Turnos</h6>
                    <p class="text-muted small mb-0">Horarios de trabajo del personal</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('empresas') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-briefcase text-dark"></i> Empresas</h6>
                    <p class="text-muted small mb-0">Gestion multi-empresa</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('usuarios') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-person text-primary"></i> Usuarios</h6>
                    <p class="text-muted small mb-0">Cuentas de acceso al sistema</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= site_url('roles') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6><i class="bi bi-shield-lock text-danger"></i> Roles</h6>
                    <p class="text-muted small mb-0">Permisos y control de acceso</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
