<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $session;
    protected $helpers = ['form', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    // ── Helpers de seguridad ─────────────────────────────────────

    /**
     * Verifica que el usuario tenga alguno de los roles indicados.
     * Si no cumple, redirige a /403.
     *
     * Uso: $this->requireRol('admin')
     *      $this->requireRol('admin', 'supervisor')
     */
    protected function requireRol(string ...$roles)
    {
        $rolActual = $this->session->get('rol');
        if (! in_array($rolActual, $roles)) {
            return redirect()->to('/403');
        }
        return null;
    }

    /**
     * Retorna el rol del usuario en sesión.
     */
    protected function rolActual(): ?string
    {
        return $this->session->get('rol');
    }

    /**
     * Retorna el id del usuario en sesión.
     */
    protected function idUsuarioActual(): ?int
    {
        return $this->session->get('id_usuario');
    }
}
