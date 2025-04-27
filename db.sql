
CREATE DATABASE plataforma_empleos;
USE plataforma_empleos;


CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    ciudad VARCHAR(100),
    formacion_academica TEXT,
    experiencia_laboral TEXT,
    habilidades_clave TEXT,
    idiomas TEXT,
    objetivo_profesional TEXT,
    logros_proyectos TEXT,
    disponibilidad VARCHAR(50),
    redes_profesionales TEXT,
    referencias TEXT,
    foto VARCHAR(255),
    cv_pdf VARCHAR(255), 
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Ofertas de Empleo
CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo_puesto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    requisitos TEXT,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

-- Tabla de Aplicaciones a Ofertas
CREATE TABLE aplicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    candidato_id INT NOT NULL,
    fecha_aplicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (oferta_id) REFERENCES ofertas(id) ON DELETE CASCADE,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE
);
