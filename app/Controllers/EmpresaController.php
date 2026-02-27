<?php

namespace App\Controllers;

class EmpresaController extends BaseController
{
    public function index(): string
    {
        return view('empresas/index', [
            'title'     => 'Empresas - Control de Produccion',
            'pageTitle' => 'Empresas',
        ]);
    }
}
