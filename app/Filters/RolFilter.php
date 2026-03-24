<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RolFilter – verifica que el usuario autenticado tenga el/los rol(es) requeridos.
 *
 * Uso en rutas:  ['filter' => 'rol:admin']
 *                ['filter' => 'rol:admin,supervisor']
 *
 * Si el usuario no tiene el rol necesario, redirige a /403.
 */
class RolFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $rolUsuario = session()->get('rol');

        if (! empty($arguments) && ! in_array($rolUsuario, $arguments)) {
            return redirect()->to('/403');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
