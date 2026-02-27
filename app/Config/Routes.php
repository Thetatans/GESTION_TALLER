<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/db-test', 'DatabaseTest::index');

// ── Seguimiento Operativo (Semana 6) ────────────────────────
$routes->get('/seguimiento', 'SeguimientoController::index');

// ── Ordenes de Produccion ────────────────────────────────────
$routes->get('/ordenes-produccion',                     'OrdenProduccionController::index');
$routes->get('/ordenes-produccion/new',                 'OrdenProduccionController::new');
$routes->post('/ordenes-produccion/create',             'OrdenProduccionController::create');
$routes->get('/ordenes-produccion/ver/(:num)',          'OrdenProduccionController::ver/$1');
$routes->get('/ordenes-produccion/edit/(:num)',         'OrdenProduccionController::edit/$1');
$routes->post('/ordenes-produccion/update/(:num)',      'OrdenProduccionController::update/$1');
$routes->post('/ordenes-produccion/delete/(:num)',      'OrdenProduccionController::delete/$1');
$routes->post('/ordenes-produccion/cambiar-estado/(:num)', 'OrdenProduccionController::cambiarEstado/$1');

// ── Tareas de Produccion ─────────────────────────────────────
$routes->get('/tareas-produccion',                      'TareaProduccionController::index');
$routes->get('/tareas-produccion/new',                  'TareaProduccionController::new');
$routes->post('/tareas-produccion/create',              'TareaProduccionController::create');
$routes->get('/tareas-produccion/edit/(:num)',          'TareaProduccionController::edit/$1');
$routes->post('/tareas-produccion/update/(:num)',       'TareaProduccionController::update/$1');
$routes->post('/tareas-produccion/delete/(:num)',       'TareaProduccionController::delete/$1');
$routes->post('/tareas-produccion/cambiar-estado/(:num)', 'TareaProduccionController::cambiarEstado/$1');

// ── Maquinas ─────────────────────────────────────────────────
$routes->get('/maquinas', 'MaquinaController::index');

// ── Calidad ──────────────────────────────────────────────────
$routes->get('/inspecciones',  'InspeccionCalidadController::index');
$routes->get('/tipos-defecto', 'TipoDefectoController::index');

// ── Almacen ──────────────────────────────────────────────────
$routes->get('/productos-terminados', 'ProductoTerminadoController::index');
$routes->get('/almacenes',            'AlmacenController::index');

// ── Personal ─────────────────────────────────────────────────
$routes->get('/operarios', 'OperarioController::index');
$routes->get('/turnos',    'TurnoController::index');

// ── Sistema ──────────────────────────────────────────────────
$routes->get('/empresas', 'EmpresaController::index');
$routes->get('/usuarios', 'UsuarioController::index');
$routes->get('/roles',    'RolController::index');
