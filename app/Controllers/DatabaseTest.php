<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class DatabaseTest extends BaseController
{
    public function index(): string
    {
        $data = [
            'connected'  => false,
            'error'      => '',
            'dbName'     => 'control_produccion',
            'tables'     => [],
            'tableData'  => [],
        ];

        try {
            $db = \Config\Database::connect();
            $db->initialize();

            // Verificar conexión ejecutando una consulta simple
            $db->query('SELECT 1');
            $data['connected'] = true;

            // Obtener lista de tablas
            $tables = $db->listTables();
            $data['tables'] = $tables;

            // Obtener datos de cada tabla
            foreach ($tables as $table) {
                $fields = $db->getFieldNames($table);
                $rows = $db->table($table)->limit(50)->get()->getResultArray();
                $data['tableData'][$table] = [
                    'fields' => $fields,
                    'rows'   => $rows,
                    'count'  => $db->table($table)->countAllResults(),
                ];
            }
        } catch (DatabaseException $e) {
            $data['error'] = $e->getMessage();
        } catch (\Throwable $e) {
            $data['error'] = $e->getMessage();
        }

        return view('database_test', $data);
    }
}
