
-- ============================================
-- BASE DE DATOS ORIENTA
-- ============================================

CREATE DATABASE IF NOT EXISTS orienta
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE orienta;

-- ============================================
-- 1. USUARIOS
-- ============================================

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(80) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(150),
    telefono VARCHAR(20),
    rol VARCHAR(30) NOT NULL DEFAULT 'usuario',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- ============================================
-- 2. TIPOS DE APOYO
-- ============================================

CREATE TABLE tipos_apoyo (
    id_apoyo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================
-- 3. USUARIO_APOYOS
-- Relaciona usuarios con sus apoyos
-- ============================================

CREATE TABLE usuario_apoyos (
    id_usuario INT NOT NULL,
    id_apoyo INT NOT NULL,

    PRIMARY KEY (id_usuario, id_apoyo),

    CONSTRAINT fk_usuario_apoyo_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

    CONSTRAINT fk_usuario_apoyo_tipo
    FOREIGN KEY (id_apoyo)
    REFERENCES tipos_apoyo(id_apoyo)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 4. CENTROS
-- ============================================

CREATE TABLE centros (
    id_centro INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    latitud DECIMAL(10,7),
    longitud DECIMAL(10,7),
    telefono VARCHAR(20),
    estado BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- ============================================
-- 5. SERVICIOS
-- ============================================

CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    horario VARCHAR(150),
    id_centro INT,
    gratuito BOOLEAN DEFAULT TRUE,
    estado BOOLEAN DEFAULT TRUE,

    CONSTRAINT fk_servicio_centro
    FOREIGN KEY (id_centro)
    REFERENCES centros(id_centro)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 6. SOLICITUDES
-- ============================================

CREATE TABLE solicitudes (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    id_servicio INT NOT NULL,
    nombre_contacto VARCHAR(150),
    telefono VARCHAR(20) NOT NULL,
    opcion_seleccionada VARCHAR(150),
    detalle TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(30) DEFAULT 'pendiente',

    CONSTRAINT fk_solicitud_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

    CONSTRAINT fk_solicitud_servicio
    FOREIGN KEY (id_servicio)
    REFERENCES servicios(id_servicio)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 7. ORIENTADORES
-- ============================================

CREATE TABLE orientadores (
    id_orientador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    especialidad VARCHAR(120),
    estado BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- ============================================
-- 8. CITAS
-- ============================================

CREATE TABLE citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_orientador INT,
    id_centro INT,
    fecha_hora DATETIME NOT NULL,
    ubicacion VARCHAR(150),
    estado VARCHAR(30) DEFAULT 'pendiente',
    observaciones TEXT,

    CONSTRAINT fk_cita_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,

    CONSTRAINT fk_cita_orientador
    FOREIGN KEY (id_orientador)
    REFERENCES orientadores(id_orientador)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

    CONSTRAINT fk_cita_centro
    FOREIGN KEY (id_centro)
    REFERENCES centros(id_centro)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- REGISTROS INICIALES
-- ============================================

-- Tipos de apoyo

INSERT INTO tipos_apoyo (nombre) VALUES
('Lector de pantalla'),
('Lengua de señas mexicana'),
('Texto grande'),
('Navegación por voz'),
('Texto en lenguaje fácil'),
('Intérprete'),
('Ninguna de las anteriores');

-- Centros comunitarios del mapa

INSERT INTO centros (nombre, direccion) VALUES
('Centro Comunitario Norte', 'Avenida Reforma'),
('Centro de Salud Sur', 'Calle Juárez 58');

-- Servicios comunitarios

INSERT INTO servicios
(nombre, descripcion, horario, id_centro, gratuito)
VALUES
(
    'Asistencia legal',
    'Orientación gratuita sobre trámites, derechos y documentos legales con abogados certificados.',
    'Lunes a viernes de 9:00 a 17:00',
    (SELECT id_centro FROM centros
     WHERE nombre = 'Centro Comunitario Norte'),
    TRUE
),
(
    'Apoyo psicológico',
    'Sesiones de apoyo emocional presencial y en línea.',
    'Lunes a viernes de 9:00 a 17:00',
    (SELECT id_centro FROM centros
     WHERE nombre = 'Centro Comunitario Norte'),
    TRUE
),
(
    'Bolsa de trabajo',
    'Oportunidades locales de empleo y apoyo para preparar tu CV y entrevistas.',
    'Lunes a viernes de 9:00 a 18:00',
    (SELECT id_centro FROM centros
     WHERE nombre = 'Centro Comunitario Norte'),
    TRUE
),
(
    'Taller de capacitación',
    'Cursos gratuitos para aprender computación, oficios y habilidades para el trabajo.',
    'Sábados de 10:00 a 13:00',
    (SELECT id_centro FROM centros
     WHERE nombre = 'Centro Comunitario Norte'),
    TRUE
);

-- ============================================
-- FIN DE LA BASE DE DATOS ORIENTA
-- ============================================
