-- ============================================================
-- Semana 8: Seguridad y Control de Accesos
-- BD: control_produccion
-- ============================================================

-- 1. Agregar columna rol a la tabla usuario
ALTER TABLE usuario
    ADD COLUMN rol ENUM('admin', 'supervisor', 'operario') NOT NULL DEFAULT 'operario'
    AFTER estado;

-- 2. Asignar roles a los usuarios existentes (seed de semana 5)
UPDATE usuario SET rol = 'admin'      WHERE id_usuario = 1;
UPDATE usuario SET rol = 'supervisor' WHERE id_usuario = 2;
UPDATE usuario SET rol = 'operario'   WHERE id_usuario = 3;

-- ============================================================
-- USUARIOS DE PRUEBA
-- Las contraseñas se generan con BCrypt via PHP.
-- Visitar  http://localhost/gestion_taller/public/auth/setup
-- (solo en entorno development) para insertarlos automáticamente.
--
-- Credenciales:
--   admin@taller.com        /  Admin2026!   →  rol: admin
--   supervisor@taller.com   /  Super2026!   →  rol: supervisor
--   operario@taller.com     /  Oper2026!    →  rol: operario
-- ============================================================
