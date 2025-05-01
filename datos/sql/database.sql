CREATE DATABASE IF NOT EXISTS gema_sas;

USE gema_sas;

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    codigo INT NOT NULL,
    fecha_carga TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de revisores
CREATE TABLE IF NOT EXISTS revisores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45),
    apellido VARCHAR(45)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor1');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor2');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor3');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor4');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor5');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor6');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor7');
INSERT INTO `revisores` (`nombre`, `apellido`) VALUES ('Nombre', 'Revisor8');

ALTER TABLE usuarios ADD COLUMN revisor_id INT DEFAULT NULL;
ALTER TABLE usuarios ADD FOREIGN KEY (revisor_id) REFERENCES revisores(id) ON DELETE SET NULL ON UPDATE CASCADE;