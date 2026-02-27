<?php

namespace App\Controllers;

class AlmacenController extends BaseController
{
    public function index(): string
    {
        return view('almacenes/index', [
            'title'     => 'Almacenes - Control de Produccion',
            'pageTitle' => 'Almacenes',
        ]);
    }
}
