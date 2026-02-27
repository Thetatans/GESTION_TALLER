<?php

namespace App\Controllers;

class OperarioController extends BaseController
{
    public function index(): string
    {
        return view('operarios/index', [
            'title'     => 'Operarios - Control de Produccion',
            'pageTitle' => 'Operarios',
        ]);
    }
}
