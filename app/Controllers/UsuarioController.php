<?php

namespace App\Controllers;

class UsuarioController extends BaseController
{
    public function index(): string
    {
        return view('usuarios/index', [
            'title'     => 'Usuarios - Control de Produccion',
            'pageTitle' => 'Usuarios',
        ]);
    }
}
