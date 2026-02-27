<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Control de Produccion' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 250px; }
        body { background: #f0f2f5; }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #1e293b;
            color: #cbd5e1;
            overflow-y: auto;
            transition: transform 0.3s;
            z-index: 1040;
        }
        .sidebar .brand {
            padding: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 10px 20px;
            font-size: 0.9rem;
            border-radius: 0;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: #334155;
        }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar .section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            padding: 16px 20px 6px;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .top-navbar .page-title { font-size: 1.1rem; font-weight: 600; color: #1e293b; }
        .content-area { padding: 24px; flex: 1; }
        .footer { padding: 16px 24px; text-align: center; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #e2e8f0; background: #fff; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <?= $this->include('layouts/partials/sidebar') ?>

    <div class="main-content">
        <?= $this->include('layouts/partials/navbar') ?>

        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('layouts/partials/footer') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
