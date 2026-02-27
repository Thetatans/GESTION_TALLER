<?php
$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'control_produccion';
$port     = 3306;

$connected = false;
$error = '';
$tables = [];

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    if ($conn->connect_error) {
        $error = $conn->connect_error;
    } else {
        $connected = true;
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_row()) {
                $tableName = $row[0];
                $countResult = $conn->query("SELECT COUNT(*) as total FROM `$tableName`");
                $count = $countResult ? $countResult->fetch_assoc()['total'] : 0;
                $tables[] = ['name' => $tableName, 'rows' => $count];
            }
            $result->free();
        }
        $conn->close();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Check</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 24px 0; }
        .box { background: #fff; border-radius: 8px; padding: 32px; width: 380px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
        .dot { width: 14px; height: 14px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .dot.ok { background: #22c55e; }
        .dot.fail { background: #ef4444; }
        .status { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 16px; display: flex; align-items: center; justify-content: center; }
        .detail { font-size: 0.85rem; color: #888; line-height: 1.8; }
        .detail b { color: #555; }
        .err { margin-top: 12px; font-size: 0.8rem; color: #ef4444; }
        .divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }
        .tables-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #aaa; margin-bottom: 12px; }
        .table-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 6px; font-size: 0.85rem; }
        .table-row:nth-child(even) { background: #f9f9f9; }
        .table-name { color: #333; font-weight: 500; }
        .table-count { color: #888; font-size: 0.8rem; }
    </style>
</head>
<body>
<div class="box">
    <div class="status">
        <span class="dot <?= $connected ? 'ok' : 'fail' ?>"></span>
        <?= $connected ? 'ilich... porfin La base de datos esta conectada' : 'La base de datos no esta conectada' ?>
    </div>
    <div class="detail">
        <b><?= htmlspecialchars($database) ?></b><br>
        <?= htmlspecialchars($host) ?>:<?= $port ?> &middot; <?= count($tables) ?> tablas
    </div>

    <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($tables)): ?>
        <hr class="divider">
        <div class="tables-title">Tablas</div>
        <?php foreach ($tables as $t): ?>
            <div class="table-row">
                <span class="table-name"><?= htmlspecialchars($t['name']) ?></span>
                <span class="table-count"><?= $t['rows'] ?> registros</span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
