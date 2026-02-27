<?php

namespace App\Controllers;

class MaquinaController extends BaseController
{
    public function index(): string
    {
        return view('maquinas/index', [
            'title'     => 'Maquinas - Control de Produccion',
            'pageTitle' => 'Maquinas',
        ]);
    }
}
