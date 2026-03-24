<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ── Rutas públicas (sin autenticación) ───────────────────────────
$routes->get('/login',      'AuthController::login');
$routes->post('/login',     'AuthController::authenticate');
$routes->get('/logout',     'AuthController::logout');
$routes->get('/auth/setup', 'AuthController::setup');   // Solo en development
$routes->get('/auth/debug', 'AuthController::debug');   // Solo en development
$routes->get('/403',        'AuthController::forbidden');

// ── Rutas protegidas (requieren sesión – manejado por AuthFilter global) ───

// Dashboard y seguimiento: todos los roles
$routes->get('/',          'DashboardController::index');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/db-test',   'DatabaseTest::index');

$routes->get('/seguimiento', 'SeguimientoController::index');

// Maquinas: todos los roles (solo lectura para operario)
$routes->get('/maquinas', 'MaquinaController::index');

// Tareas de Produccion: todos los roles
// (el operario solo puede ver sus tareas — restricción en el controlador)
$routes->get('/tareas-produccion',                        'TareaProduccionController::index');
$routes->get('/tareas-produccion/new',                    'TareaProduccionController::new',    ['filter' => 'rol:admin,supervisor']);
$routes->post('/tareas-produccion/create',                'TareaProduccionController::create', ['filter' => 'rol:admin,supervisor']);
$routes->get('/tareas-produccion/edit/(:num)',            'TareaProduccionController::edit/$1',   ['filter' => 'rol:admin,supervisor']);
$routes->post('/tareas-produccion/update/(:num)',         'TareaProduccionController::update/$1', ['filter' => 'rol:admin,supervisor']);
$routes->post('/tareas-produccion/delete/(:num)',         'TareaProduccionController::delete/$1', ['filter' => 'rol:admin']);
$routes->post('/tareas-produccion/cambiar-estado/(:num)', 'TareaProduccionController::cambiarEstado/$1');

// ── Rutas para admin + supervisor ────────────────────────────────

// Reportes
$routes->group('', ['filter' => 'rol:admin,supervisor'], function ($routes) {
    $routes->get('/reportes',              'ReporteController::index');
    $routes->get('/reportes/ordenes',      'ReporteController::ordenes');
    $routes->get('/reportes/tareas',       'ReporteController::tareas');
    $routes->get('/reportes/resumen',      'ReporteController::resumen');
    $routes->get('/reportes/pdf/ordenes',  'ReporteController::pdfOrdenes');
    $routes->get('/reportes/pdf/tareas',   'ReporteController::pdfTareas');
    $routes->get('/reportes/pdf/resumen',  'ReporteController::pdfResumen');
});

// Manual de Usuario – accesible para todos los roles autenticados
$routes->get('/manual-usuario', 'ReporteController::pdfManualUsuario');

// Ordenes de Produccion – lectura: todos los roles / escritura: admin + supervisor
$routes->get('/ordenes-produccion',            'OrdenProduccionController::index');
$routes->get('/ordenes-produccion/ver/(:num)', 'OrdenProduccionController::ver/$1');

$routes->group('', ['filter' => 'rol:admin,supervisor'], function ($routes) {
    $routes->get('/ordenes-produccion/new',                    'OrdenProduccionController::new');
    $routes->post('/ordenes-produccion/create',                'OrdenProduccionController::create');
    $routes->get('/ordenes-produccion/edit/(:num)',            'OrdenProduccionController::edit/$1');
    $routes->post('/ordenes-produccion/update/(:num)',         'OrdenProduccionController::update/$1');
    $routes->post('/ordenes-produccion/cambiar-estado/(:num)', 'OrdenProduccionController::cambiarEstado/$1');
});
$routes->post('/ordenes-produccion/delete/(:num)', 'OrdenProduccionController::delete/$1', ['filter' => 'rol:admin']);

// Calidad
$routes->group('', ['filter' => 'rol:admin,supervisor'], function ($routes) {
    $routes->get('/inspecciones',  'InspeccionCalidadController::index');
    $routes->get('/tipos-defecto', 'TipoDefectoController::index');
});

// Almacen
$routes->group('', ['filter' => 'rol:admin,supervisor'], function ($routes) {
    $routes->get('/productos-terminados', 'ProductoTerminadoController::index');
    $routes->get('/almacenes',            'AlmacenController::index');
});

// Personal
$routes->group('', ['filter' => 'rol:admin,supervisor'], function ($routes) {
    $routes->get('/operarios', 'OperarioController::index');
    $routes->get('/turnos',    'TurnoController::index');
});

// ── Rutas solo para admin ─────────────────────────────────────────
$routes->group('', ['filter' => 'rol:admin'], function ($routes) {
    $routes->get('/empresas', 'EmpresaController::index');
    $routes->get('/usuarios', 'UsuarioController::index');
    $routes->get('/roles',    'RolController::index');
});
