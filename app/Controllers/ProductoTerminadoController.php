<?php

namespace App\Controllers;

class ProductoTerminadoController extends BaseController
{
    public function index(): string
    {
        return view('productos_terminados/index', [
            'title'     => 'Productos Terminados - Control de Produccion',
            'pageTitle' => 'Productos Terminados',
        ]);
    }
}
