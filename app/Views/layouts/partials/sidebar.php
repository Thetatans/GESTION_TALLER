<aside class="sidebar">
    <div class="brand">
        <i class="bi bi-gear-fill"></i> Control Produccion
    </div>
    <nav class="nav flex-column mt-2">
        <a class="nav-link <?= (url_is('dashboard*')) ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="section-title">Produccion</div>
        <a class="nav-link <?= (url_is('seguimiento*')) ? 'active' : '' ?>" href="<?= site_url('seguimiento') ?>">
            <i class="bi bi-activity"></i> Seguimiento Operativo
        </a>
        <a class="nav-link <?= (url_is('ordenes-produccion*')) ? 'active' : '' ?>" href="<?= site_url('ordenes-produccion') ?>">
            <i class="bi bi-clipboard-data"></i> Ordenes de Produccion
        </a>
        <a class="nav-link <?= (url_is('tareas-produccion*')) ? 'active' : '' ?>" href="<?= site_url('tareas-produccion') ?>">
            <i class="bi bi-list-task"></i> Tareas de Produccion
        </a>
        <a class="nav-link <?= (url_is('maquinas*')) ? 'active' : '' ?>" href="<?= site_url('maquinas') ?>">
            <i class="bi bi-cpu"></i> Maquinas
        </a>

        <div class="section-title">Calidad</div>
        <a class="nav-link <?= (url_is('inspecciones*')) ? 'active' : '' ?>" href="<?= site_url('inspecciones') ?>">
            <i class="bi bi-search"></i> Inspecciones
        </a>
        <a class="nav-link <?= (url_is('tipos-defecto*')) ? 'active' : '' ?>" href="<?= site_url('tipos-defecto') ?>">
            <i class="bi bi-exclamation-triangle"></i> Tipos de Defecto
        </a>

        <div class="section-title">Almacen</div>
        <a class="nav-link <?= (url_is('productos-terminados*')) ? 'active' : '' ?>" href="<?= site_url('productos-terminados') ?>">
            <i class="bi bi-box-seam"></i> Productos Terminados
        </a>
        <a class="nav-link <?= (url_is('almacenes*')) ? 'active' : '' ?>" href="<?= site_url('almacenes') ?>">
            <i class="bi bi-building"></i> Almacenes
        </a>

        <div class="section-title">Personal</div>
        <a class="nav-link <?= (url_is('operarios*')) ? 'active' : '' ?>" href="<?= site_url('operarios') ?>">
            <i class="bi bi-people"></i> Operarios
        </a>
        <a class="nav-link <?= (url_is('turnos*')) ? 'active' : '' ?>" href="<?= site_url('turnos') ?>">
            <i class="bi bi-clock"></i> Turnos
        </a>

        <div class="section-title">Sistema</div>
        <a class="nav-link <?= (url_is('empresas*')) ? 'active' : '' ?>" href="<?= site_url('empresas') ?>">
            <i class="bi bi-briefcase"></i> Empresas
        </a>
        <a class="nav-link <?= (url_is('usuarios*')) ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">
            <i class="bi bi-person"></i> Usuarios
        </a>
        <a class="nav-link <?= (url_is('roles*')) ? 'active' : '' ?>" href="<?= site_url('roles') ?>">
            <i class="bi bi-shield-lock"></i> Roles
        </a>
    </nav>
</aside>
