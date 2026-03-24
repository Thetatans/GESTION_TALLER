<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index(): string
    {
        $usuarios = $this->usuarioModel->listarConEmpresa();

        return view('usuarios/index', [
            'title'     => 'Usuarios - Control de Produccion',
            'pageTitle' => 'Gestión de Usuarios',
            'usuarios'  => $usuarios,
        ]);
    }
}
