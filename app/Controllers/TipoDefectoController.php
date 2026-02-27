<?php

namespace App\Controllers;

class TipoDefectoController extends BaseController
{
    public function index(): string
    {
        return view('tipos_defecto/index', [
            'title'     => 'Tipos de Defecto - Control de Produccion',
            'pageTitle' => 'Tipos de Defecto',
        ]);
    }
}
