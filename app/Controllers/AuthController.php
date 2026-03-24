<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class AuthController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // ── GET /login ───────────────────────────────────────────────
    public function login(): string
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', ['title' => 'Iniciar Sesión']);
    }

    // ── POST /login ──────────────────────────────────────────────
    public function authenticate()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->usuarioModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Correo o contraseña incorrectos.');
        }

        // Guardar sesión
        $this->session->set([
            'logged_in'       => true,
            'id_usuario'      => $user['id_usuario'],
            'nombre_completo' => $user['nombre_completo'],
            'email'           => $user['email'],
            'rol'             => $user['rol'],
            'id_empresa'      => $user['id_empresa'],
        ]);

        $this->usuarioModel->updateLastAccess($user['id_usuario']);

        return redirect()->to('/dashboard');
    }

    // ── GET /logout ──────────────────────────────────────────────
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    // ── GET /403 ─────────────────────────────────────────────────
    public function forbidden(): string
    {
        return view('auth/forbidden', [
            'title'     => 'Acceso Denegado',
            'pageTitle' => 'Acceso Denegado',
        ]);
    }

    // ── GET /auth/setup (solo en development) ───────────────────
    public function setup()
    {
        if (ENVIRONMENT !== 'development') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $db  = \Config\Database::connect();
        $log = [];

        // 1. Agregar columna rol si no existe
        $columnas = $db->getFieldNames('usuario');
        if (! in_array('rol', $columnas)) {
            $db->query("ALTER TABLE usuario ADD COLUMN rol ENUM('admin','supervisor','operario') NOT NULL DEFAULT 'operario' AFTER estado");
            $log[] = 'Columna <code>rol</code> agregada a la tabla usuario.';
        } else {
            $log[] = 'Columna <code>rol</code> ya existe.';
        }

        // 2. Eliminar usuarios de prueba anteriores y recrearlos
        $emailsPrueba = [
            'admin@taller.com',
            'supervisor@taller.com',
            'laura.gomez@techparts.com',
            'miguel.torres@metalurgica.com',
            'sofia.vargas@plasticos.com',
        ];
        $db->table('usuario')->whereIn('email', $emailsPrueba)->delete();

        $hash = password_hash('ilich123', PASSWORD_BCRYPT);

        $usuarios = [
            [
                'id_empresa'      => 1,
                'nombre_completo' => 'Administrador Sistema',
                'email'           => 'admin@taller.com',
                'password_hash'   => $hash,
                'telefono'        => '3000000001',
                'documento'       => '1000000001',
                'estado'          => 'activo',
                'rol'             => 'admin',
                'fecha_creacion'  => date('Y-m-d H:i:s'),
            ],
            [
                'id_empresa'      => 1,
                'nombre_completo' => 'Juan Supervisor',
                'email'           => 'supervisor@taller.com',
                'password_hash'   => $hash,
                'telefono'        => '3000000002',
                'documento'       => '1000000002',
                'estado'          => 'activo',
                'rol'             => 'supervisor',
                'fecha_creacion'  => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($usuarios as $u) {
            $db->table('usuario')->insert($u);
        }
        $log[] = '2 usuarios de gestión creados: admin + supervisor.';

        // 3. Actualizar contraseña BCrypt a TODOS los usuarios
        $db->query("UPDATE usuario SET password_hash = '{$hash}'");
        $log[] = 'Contraseña BCrypt (ilich123) aplicada a todos los usuarios.';

        // 4. Asignar roles por email
        $asignaciones = [
            'admin'      => ['admin@taller.com'],
            'supervisor' => ['supervisor@taller.com', 'carlos.castro@techparts.com', 'juan.mateus@techparts.com'],
            'operario'   => ['carlos.martinez@techparts.com', 'ana.rodriguez@metalurgica.com',
                             'juan.perez@plasticos.com', 'ilich.reyes@techparts.com'],
        ];
        foreach ($asignaciones as $rol => $emails) {
            $db->table('usuario')->whereIn('email', $emails)->update(['rol' => $rol]);
        }
        $log[] = 'Roles asignados: 1 admin, 3 supervisores, 4 operarios.';

        $lista = implode('<br>', array_map(fn($l) => "✔ $l", $log));
        return redirect()->to('/login')
                         ->with('success', "$lista — Ya puede ingresar.");
    }

    // ── GET /auth/debug (solo development) ──────────────────────
    public function debug()
    {
        if (ENVIRONMENT !== 'development') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $db   = \Config\Database::connect();
        $hash = password_hash('ilich123', PASSWORD_BCRYPT);

        // Actualizar contraseña de todos
        $db->query("UPDATE usuario SET password_hash = '{$hash}'");
        $afectados = $db->affectedRows();
        $error     = $db->error();

        // Asignar roles por email
        $roles = [
            'admin'      => ['admin@taller.com'],
            'supervisor' => ['supervisor@taller.com', 'carlos.castro@techparts.com', 'juan.mateus@techparts.com'],
            'operario'   => ['carlos.martinez@techparts.com', 'ana.rodriguez@metalurgica.com',
                             'juan.perez@plasticos.com', 'ilich.reyes@techparts.com'],
        ];
        foreach ($roles as $rol => $emails) {
            $db->table('usuario')->whereIn('email', $emails)->update(['rol' => $rol]);
        }

        $usuarios = $db->table('usuario')->get()->getResultArray();

        echo '<pre style="font-family:monospace;font-size:13px;">';
        echo "UPDATE ejecutado — filas afectadas: {$afectados}\n";
        if (!empty($error['message'])) {
            echo "ERROR BD: " . $error['message'] . "\n";
        }
        echo str_repeat('-', 80) . "\n";

        foreach ($usuarios as $u) {
            $ok = password_verify('ilich123', $u['password_hash'] ?? '');
            echo "ID:     " . $u['id_usuario'] . "\n";
            echo "Nombre: " . $u['nombre_completo'] . "\n";
            echo "Email:  " . $u['email'] . "\n";
            echo "Rol:    " . ($u['rol'] ?? '⚠ sin columna rol') . "\n";
            echo "Hash:   " . substr($u['password_hash'] ?? '', 0, 30) . "...\n";
            echo "verify: " . ($ok ? '✔ TRUE' : '✘ FALSE') . "\n";
            echo str_repeat('-', 80) . "\n";
        }
        echo '</pre>';
    }
}
