<?php

namespace App\Controllers;

class TurnoController extends BaseController
{
    public function index(): string
    {
        return view('turnos/index', [
            'title'     => 'Turnos - Control de Produccion',
            'pageTitle' => 'Turnos',
        ]);
    }
}
