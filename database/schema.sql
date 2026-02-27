-- ============================================================
-- Schema de referencia - BD: control_produccion
-- Semana 5: Modulo Principal de Produccion (CORREGIDO)
-- NOTA: Este archivo es solo referencia. La BD real ya existe.
-- ============================================================

-- ============================================================
-- TABLAS BASE
-- ============================================================

CREATE TABLE empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    nit VARCHAR(255) NOT NULL UNIQUE,
    direccion VARCHAR(255),
    telefono INT,
    email VARCHAR(255),
    fecha_creacion DATETIME,
    estado ENUM('Activa','inactiva')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE turno (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    nombre_turno VARCHAR(255) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    descripcion TEXT,
    estado ENUM('activo','inactivo'),
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE maquina (
    id_maquina INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    nombre_maquina VARCHAR(255) NOT NULL,
    tipo_maquina VARCHAR(255),
    codigo_maquina VARCHAR(255) UNIQUE,
    modelo VARCHAR(255),
    marca VARCHAR(255),
    fecha_adquisicion DATE,
    disponibilidad ENUM('disponible','en uso','en mantenimiento','fuera de servicio'),
    ubicacion VARCHAR(255),
    especificaciones TEXT,
    fecha_creacion DATETIME,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(255),
    documento VARCHAR(255),
    estado ENUM('activo','inactivo','suspendido'),
    fecha_creacion DATETIME,
    ultimo_acceso DATETIME,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE operario (
    id_operario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_turno INT,
    habilidades TEXT,
    certificaciones TEXT,
    nivel_experiencia ENUM('junior','intermedio','senior','expert'),
    fecha_ingreso DATE,
    estado ENUM('activo','inactivo','vacaciones'),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_turno) REFERENCES turno(id_turno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLAS DEL MODULO DE PRODUCCION
-- ============================================================

CREATE TABLE orden_produccion (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    codigo_orden VARCHAR(255) NOT NULL UNIQUE,
    descripcion_producto TEXT NOT NULL,
    cantidad_solicitada INT NOT NULL,
    fecha_creacion DATETIME,
    fecha_limite DATE NOT NULL,
    prioridad ENUM('baja','media','alta','urgente'),
    estado ENUM('pendiente','en proceso','pausada','completada','cancelada'),
    especificaciones_tecnicas TEXT,
    cliente VARCHAR(255),
    observaciones TEXT,
    fecha_completada DATETIME,
    id_usuario_creador INT,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa),
    FOREIGN KEY (id_usuario_creador) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tarea_produccion (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    id_maquina INT,
    id_operario INT,
    nombre_tarea VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_asignacion DATETIME,
    fecha_inicio DATETIME,
    fecha_fin DATETIME,
    tiempo_estimadu_minutos INT,
    tiempo_real_minutos INT,
    cantidad_producida INT DEFAULT 0,
    estado ENUM('asignada','en proceso','pausada','completada','cancelada'),
    observaciones TEXT,
    numero_orden_tarea INT,
    FOREIGN KEY (id_orden) REFERENCES orden_produccion(id_orden) ON DELETE CASCADE,
    FOREIGN KEY (id_maquina) REFERENCES maquina(id_maquina),
    FOREIGN KEY (id_operario) REFERENCES operario(id_operario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS DE PRUEBA (Seeds)
-- ============================================================

-- Empresas (3 ya insertadas)
-- Turnos (3 ya insertados)
-- Maquinas (6 ya insertadas)

-- Usuarios
INSERT INTO usuario (id_empresa, nombre_completo, email, password_hash, telefono, documento, estado, fecha_creacion) VALUES
(1, 'Carlos Martinez', 'carlos.martinez@techparts.com', SHA2('password123', 256), '3001234567', '1020304050', 'activo', NOW()),
(2, 'Ana Rodriguez', 'ana.rodriguez@metalurgica.com', SHA2('password123', 256), '3009876543', '1020304051', 'activo', NOW()),
(3, 'Juan Perez', 'juan.perez@plasticos.com', SHA2('password123', 256), '3005551234', '1020304052', 'activo', NOW());

-- Operarios
INSERT INTO operario (id_usuario, id_turno, habilidades, certificaciones, nivel_experiencia, fecha_ingreso, estado) VALUES
(1, 1, 'Torneado, Fresado, Programacion CNC', 'ISO 9001, Seguridad Industrial', 'senior', '2023-01-15', 'activo'),
(2, 2, 'Soldadura MIG/TIG, Corte Laser', 'AWS D1.1, Primeros Auxilios', 'intermedio', '2023-06-01', 'activo'),
(3, 1, 'Inyeccion Plastico, Control Calidad', 'Six Sigma Green Belt', 'junior', '2024-03-10', 'activo'),
(1, 3, 'Metrologia, Mantenimiento', 'Fanuc Operator Level 2', 'senior', '2022-08-20', 'activo');

-- Ordenes de Produccion
INSERT INTO orden_produccion (id_empresa, codigo_orden, descripcion_producto, cantidad_solicitada, fecha_limite, prioridad, estado, cliente, observaciones, fecha_creacion) VALUES
(1, 'ORD-2026-001', 'Fabricacion de 500 pernos hexagonales M10', 500, '2026-03-15', 'alta', 'en proceso', 'AutoPartes Colombia', 'Material: Acero inoxidable 304', NOW()),
(3, 'ORD-2026-002', 'Produccion de carcasas plasticas modelo X200', 200, '2026-03-20', 'media', 'pendiente', 'ElectroHogar S.A.', 'Color: Negro mate', NOW()),
(2, 'ORD-2026-003', 'Ensamble de 100 soportes metalicos', 100, '2026-02-28', 'urgente', 'en proceso', 'Construcciones del Valle', 'Requiere galvanizado', NOW()),
(1, 'ORD-2026-004', 'Fabricacion de ejes de transmision', 50, '2026-04-01', 'baja', 'pendiente', 'Motores del Pacifico', NULL, NOW()),
(2, 'ORD-2026-005', 'Lote de tapas roscadas para envases', 1000, '2026-03-10', 'media', 'completada', 'Envases Nacionales', 'Entrega parcial aceptada', NOW());

-- Tareas de Produccion
INSERT INTO tarea_produccion (id_orden, id_maquina, id_operario, nombre_tarea, descripcion, fecha_inicio, fecha_fin, cantidad_producida, estado, observaciones) VALUES
(1, 1, 1, 'Torneado de pernos - Lote 1', 'Torneado CNC de pernos hexagonales M10, primer lote', '2026-02-18 06:00:00', '2026-02-18 14:00:00', 150, 'completada', 'Lote completado sin novedades'),
(1, 1, 1, 'Torneado de pernos - Lote 2', 'Torneado CNC de pernos hexagonales M10, segundo lote', '2026-02-19 06:00:00', NULL, 80, 'en proceso', NULL),
(2, 6, 3, 'Inyeccion de carcasas - Preparacion', 'Preparacion de moldes y material para inyeccion', '2026-02-20 14:00:00', NULL, 0, 'asignada', 'Pendiente revision de moldes'),
(3, 5, 2, 'Corte de laminas para soportes', 'Corte laser de laminas metalicas segun planos', '2026-02-17 06:00:00', '2026-02-17 12:00:00', 100, 'completada', NULL),
(3, 4, 2, 'Soldadura de soportes', 'Soldadura MIG de soportes metalicos', '2026-02-18 06:00:00', NULL, 45, 'en proceso', 'Avance al 45%'),
(5, 1, 4, 'Roscado de tapas - Lote final', 'Roscado de tapas metalicas para envases', '2026-02-15 06:00:00', '2026-02-16 14:00:00', 1000, 'completada', 'Orden completada');
