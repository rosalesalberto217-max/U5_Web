CREATE DATABASE IF NOT EXISTS contactos_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE contactos_app;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_de_usuario VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  foto VARCHAR(255) NULL,
  token VARCHAR(255) NULL,
  token_expiracion DATETIME NULL,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contactos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NULL,
  telefono VARCHAR(20) NOT NULL,
  email VARCHAR(120) NULL,
  direccion VARCHAR(255) NULL,
  notas TEXT NULL,
  foto VARCHAR(255) NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);