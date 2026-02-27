<?php

namespace App\Controllers;

class RolController extends BaseController
{
    public function index(): string
    {
        return view('roles/index', [
            'title'     => 'Roles - Control de Produccion',
            'pageTitle' => 'Roles',
        ]);
    }
}
