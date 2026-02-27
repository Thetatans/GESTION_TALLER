<?php

namespace App\Controllers;

class InspeccionCalidadController extends BaseController
{
    public function index(): string
    {
        return view('inspecciones/index', [
            'title'     => 'Inspecciones de Calidad - Control de Produccion',
            'pageTitle' => 'Inspecciones de Calidad',
        ]);
    }
}
