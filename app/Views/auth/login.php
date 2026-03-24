<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – Control de Producción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-wrapper { width: 100%; max-width: 420px; padding: 16px; }
        .login-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(0,0,0,.5);
        }
        .brand-icon {
            width: 56px; height: 56px;
            background: #3b82f6;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff; margin: 0 auto 20px;
        }
        .login-card h1 {
            font-size: 1.4rem; font-weight: 700;
            color: #f1f5f9; text-align: center; margin-bottom: 4px;
        }
        .login-card p.subtitle {
            color: #64748b; font-size: 0.875rem;
            text-align: center; margin-bottom: 28px;
        }
        .form-label { color: #94a3b8; font-size: 0.85rem; margin-bottom: 6px; }
        .form-control {
            background: #0f172a; border: 1px solid #334155;
            color: #f1f5f9; border-radius: 8px; padding: 10px 14px;
        }
        .form-control:focus {
            background: #0f172a; border-color: #3b82f6;
            color: #f1f5f9; box-shadow: 0 0 0 3px rgba(59,130,246,.2);
        }
        .form-control::placeholder { color: #475569; }
        .btn-login {
            background: #3b82f6; border: none; color: #fff;
            width: 100%; padding: 11px; border-radius: 8px;
            font-weight: 600; font-size: 0.95rem; margin-top: 8px;
            transition: background .2s;
        }
        .btn-login:hover { background: #2563eb; color: #fff; }
        .demo-box {
            background: #0f172a; border: 1px solid #334155;
            border-radius: 8px; padding: 14px 16px; margin-top: 24px;
        }
        .demo-box h6 { color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .demo-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 0.82rem; }
        .demo-row:last-child { margin-bottom: 0; }
        .badge-rol { font-size: 0.7rem; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
        .badge-admin      { background: #dc2626; color: #fff; }
        .badge-supervisor { background: #d97706; color: #fff; }
        .badge-operario   { background: #16a34a; color: #fff; }
        .demo-cred { color: #94a3b8; font-family: monospace; font-size: 0.78rem; }
        .alert { border-radius: 8px; font-size: 0.875rem; }
        .alert-danger  { background: #450a0a; border-color: #991b1b; color: #fca5a5; }
        .alert-success { background: #052e16; border-color: #166534; color: #86efac; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="brand-icon"><i class="bi bi-gear-fill"></i></div>
        <h1>Control de Producción</h1>
        <p class="subtitle">Ingrese sus credenciales para continuar</p>

        <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if ($success = session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= $success ?></div>
        <?php endif; ?>

        <?php if ($errors = session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= form_open('/login') ?>

            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control"
                       placeholder="usuario@empresa.com"
                       value="<?= esc(old('email')) ?>" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>

            <?= csrf_field() ?>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>

        <?= form_close() ?>

        <div class="demo-box">
            <h6><i class="bi bi-info-circle me-1"></i>Usuarios &mdash; contraseña: <code style="color:#94a3b8;">ilich123</code></h6>

            <div class="demo-row">
                <span class="badge-rol badge-admin">admin</span>
                <span class="demo-cred">admin@taller.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-supervisor">supervisor</span>
                <span class="demo-cred">supervisor@taller.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-supervisor">supervisor</span>
                <span class="demo-cred">carlos.castro@techparts.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-supervisor">supervisor</span>
                <span class="demo-cred">juan.mateus@techparts.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-operario">operario</span>
                <span class="demo-cred">carlos.martinez@techparts.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-operario">operario</span>
                <span class="demo-cred">ana.rodriguez@metalurgica.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-operario">operario</span>
                <span class="demo-cred">juan.perez@plasticos.com</span>
            </div>
            <div class="demo-row">
                <span class="badge-rol badge-operario">operario</span>
                <span class="demo-cred">ilich.reyes@techparts.com</span>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
