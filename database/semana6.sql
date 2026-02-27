-- ============================================================
-- Semana 6: Módulo de Seguimiento de Produccion
-- Ya ejecutado. Referencia para futuras instalaciones.
-- ============================================================

CREATE TABLE IF NOT EXISTS historial_estado (
    id_historial    INT AUTO_INCREMENT PRIMARY KEY,
    tipo_entidad    ENUM('orden', 'tarea') NOT NULL,
    id_entidad      INT NOT NULL,
    estado_anterior VARCHAR(50) NOT NULL,
    estado_nuevo    VARCHAR(50) NOT NULL,
    responsable     VARCHAR(255),
    observaciones   TEXT,
    fecha_cambio    DATETIME NOT NULL,
    INDEX idx_entidad (tipo_entidad, id_entidad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
