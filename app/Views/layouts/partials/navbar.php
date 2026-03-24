<header class="top-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="page-title"><?= $pageTitle ?? 'Dashboard' ?></span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <?php
        $rol          = session()->get('rol');
        $nombreUsuario = session()->get('nombre_completo') ?? 'Usuario';
        $rolClass = match($rol) {
            'admin'      => 'danger',
            'supervisor' => 'warning',
            'operario'   => 'success',
            default      => 'secondary',
        };
        ?>
        <span class="badge bg-<?= $rolClass ?>" style="font-size:.72rem;">
            <?= ucfirst($rol ?? '') ?>
        </span>
        <span class="text-muted" style="font-size:0.85rem;">
            <i class="bi bi-person-circle"></i> <?= esc($nombreUsuario) ?>
        </span>
        <a href="<?= site_url('logout') ?>"
           class="btn btn-sm btn-outline-danger"
           title="Cerrar sesión"
           onclick="return confirm('¿Desea cerrar sesión?')">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</header>
