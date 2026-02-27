<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexion - <?= esc($dbName) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; color: #333; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { text-align: center; margin-bottom: 20px; color: #2c3e50; }
        .status-card {
            padding: 20px; border-radius: 8px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 12px;
            font-size: 18px; font-weight: 600;
        }
        .status-ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-icon { font-size: 24px; }
        .info { font-size: 14px; font-weight: 400; margin-top: 4px; }
        .error-detail { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 16px; border-radius: 8px; margin-bottom: 24px; word-break: break-word; }
        .table-section { background: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .table-section h2 { color: #2c3e50; margin-bottom: 4px; font-size: 20px; }
        .table-meta { color: #6c757d; font-size: 14px; margin-bottom: 12px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .data-table th { background: #343a40; color: #fff; padding: 10px 12px; text-align: left; position: sticky; top: 0; }
        .data-table td { padding: 8px 12px; border-bottom: 1px solid #e9ecef; }
        .data-table tr:hover td { background: #f8f9fa; }
        .data-table td:empty::after { content: 'NULL'; color: #adb5bd; font-style: italic; }
        .table-wrapper { overflow-x: auto; max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; }
        .empty-msg { color: #6c757d; font-style: italic; padding: 12px 0; }
        .summary { background: #fff; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .summary span { font-weight: 600; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test de Conexion a Base de Datos</h1>

        <?php if ($connected): ?>
            <div class="status-card status-ok">
                <span class="status-icon">&#10004;</span>
                <div>
                    Conexion exitosa a <strong><?= esc($dbName) ?></strong>
                    <div class="info">Servidor: localhost | Driver: MySQLi</div>
                </div>
            </div>

            <div class="summary">
                Tablas encontradas: <span><?= count($tables) ?></span>
            </div>

            <?php if (empty($tables)): ?>
                <div class="table-section">
                    <p class="empty-msg">La base de datos no contiene tablas.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($tableData as $tableName => $info): ?>
                <div class="table-section">
                    <h2><?= esc($tableName) ?></h2>
                    <p class="table-meta"><?= $info['count'] ?> registro(s) total(es) — mostrando hasta 50</p>

                    <?php if (empty($info['rows'])): ?>
                        <p class="empty-msg">Esta tabla no tiene registros.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($info['fields'] as $field): ?>
                                            <th><?= esc($field) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($info['rows'] as $row): ?>
                                        <tr>
                                            <?php foreach ($info['fields'] as $field): ?>
                                                <td><?= esc($row[$field] ?? '') ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="status-card status-fail">
                <span class="status-icon">&#10008;</span>
                <div>
                    Error al conectar a <strong><?= esc($dbName) ?></strong>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error-detail">
                    <strong>Detalle del error:</strong><br>
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
