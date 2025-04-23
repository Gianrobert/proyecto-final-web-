<?php
// Rutas de archivos JSON
define('USUARIOS_FILE', 'data/usuarios.json');
define('CVS_FILE', 'data/cvs.json');
define('OFERTAS_FILE', 'data/ofertas.json');
define('APLICACIONES_FILE', 'data/aplicaciones.json');

// Crear directorios y archivos JSON si no existen
function inicializarArchivos() {
    // Crear directorios
    $directorios = ['data', 'uploads', 'uploads/fotos', 'uploads/pdfs', 'assets/img', 'assets/css', 'assets/js'];
    foreach ($directorios as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
    
    // Crear archivos JSON si no existen
    $archivos = [
        USUARIOS_FILE => '{"candidatos":[],"empresas":[]}',
        CVS_FILE => '[]',
        OFERTAS_FILE => '[]',
        APLICACIONES_FILE => '[]'
    ];
    
    foreach ($archivos as $archivo => $contenido) {
        if (!file_exists($archivo)) {
            file_put_contents($archivo, $contenido);
        }
    }
}

inicializarArchivos();

// Funciones de utilidad
function generarId() {
    return uniqid() . bin2hex(random_bytes(8));
}

function leerJSON($archivo) {
    if (!file_exists($archivo)) {
        return [];
    }
    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?: [];
}

function guardarJSON($archivo, $datos) {
    file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));
}

// Funciones de usuarios
function registrarUsuario($tipo, $datos) {
    $usuarios = leerJSON(USUARIOS_FILE);
    
    // Verificar si el correo ya existe
    foreach ($usuarios['candidatos'] as $candidato) {
        if ($candidato['correo'] === $datos['correo']) {
            return false;
        }
    }
    
    foreach ($usuarios['empresas'] as $empresa) {
        if ($empresa['correo'] === $datos['correo']) {
            return false;
        }
    }
    
    // Agregar nuevo usuario
    $datos['id'] = generarId();
    $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
    $datos['fecha_registro'] = date('Y-m-d H:i:s');
    
    if ($tipo === 'candidato') {
        $usuarios['candidatos'][] = $datos;
    } else {
        $usuarios['empresas'][] = $datos;
    }
    
    guardarJSON(USUARIOS_FILE, $usuarios);
    return $datos['id'];
}

function verificarLogin($correo, $password) {
    $usuarios = leerJSON(USUARIOS_FILE);
    
    // Buscar en candidatos
    foreach ($usuarios['candidatos'] as $candidato) {
        if ($candidato['correo'] === $correo && password_verify($password, $candidato['password'])) {
            return ['tipo' => 'candidato', 'id' => $candidato['id'], 'nombre' => $candidato['nombre']];
        }
    }
    
    // Buscar en empresas
    foreach ($usuarios['empresas'] as $empresa) {
        if ($empresa['correo'] === $correo && password_verify($password, $empresa['password'])) {
            return ['tipo' => 'empresa', 'id' => $empresa['id'], 'nombre' => $empresa['nombre']];
        }
    }
    
    return false;
}

function obtenerUsuario($tipo, $id) {
    $usuarios = leerJSON(USUARIOS_FILE);
    
    if ($tipo === 'candidato') {
        foreach ($usuarios['candidatos'] as $candidato) {
            if ($candidato['id'] === $id) {
                return $candidato;
            }
        }
    } else {
        foreach ($usuarios['empresas'] as $empresa) {
            if ($empresa['id'] === $id) {
                return $empresa;
            }
        }
    }
    
    return null;
}

// Funciones de CV
function guardarCV($datos) {
    $cvs = leerJSON(CVS_FILE);
    
    // Verificar si ya existe un CV para este candidato
    foreach ($cvs as $key => $cv) {
        if ($cv['candidato_id'] === $datos['candidato_id']) {
            // Actualizar CV existente
            $cvs[$key] = $datos;
            guardarJSON(CVS_FILE, $cvs);
            return true;
        }
    }
    
    // Crear nuevo CV
    $datos['id'] = generarId();
    $datos['fecha_creacion'] = date('Y-m-d H:i:s');
    $cvs[] = $datos;
    
    guardarJSON(CVS_FILE, $cvs);
    return true;
}

function obtenerCV($candidato_id) {
    $cvs = leerJSON(CVS_FILE);
    
    foreach ($cvs as $cv) {
        if ($cv['candidato_id'] === $candidato_id) {
            return $cv;
        }
    }
    
    return null;
}

// Funciones de ofertas
function crearOferta($datos) {
    $ofertas = leerJSON(OFERTAS_FILE);
    
    $datos['id'] = generarId();
    $datos['fecha_publicacion'] = date('Y-m-d H:i:s');
    $datos['activa'] = true;
    
    $ofertas[] = $datos;
    guardarJSON(OFERTAS_FILE, $ofertas);
    
    return $datos['id'];
}

function obtenerOferta($id) {
    $ofertas = leerJSON(OFERTAS_FILE);
    
    foreach ($ofertas as $oferta) {
        if ($oferta['id'] === $id) {
            return $oferta;
        }
    }
    
    return null;
}

function obtenerOfertasEmpresa($empresa_id) {
    $ofertas = leerJSON(OFERTAS_FILE);
    $resultado = [];
    
    foreach ($ofertas as $oferta) {
        if ($oferta['empresa_id'] === $empresa_id) {
            $resultado[] = $oferta;
        }
    }
    
    return $resultado;
}

function getUltimasOfertas($limite = 6) {
    $ofertas = leerJSON(OFERTAS_FILE);
    
    // Filtrar solo ofertas activas
    $ofertas_activas = array_filter($ofertas, function($oferta) {
        return isset($oferta['activa']) && $oferta['activa'] === true;
    });
    
    // Ordenar por fecha de publicación (más recientes primero)
    usort($ofertas_activas, function($a, $b) {
        return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
    });
    
    // Limitar resultados
    return array_slice($ofertas_activas, 0, $limite);
}

function obtenerTodasOfertas() {
    $ofertas = leerJSON(OFERTAS_FILE);
    
    // Filtrar solo ofertas activas
    $ofertas_activas = array_filter($ofertas, function($oferta) {
        return isset($oferta['activa']) && $oferta['activa'] === true;
    });
    
    // Ordenar por fecha de publicación (más recientes primero)
    usort($ofertas_activas, function($a, $b) {
        return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
    });
    
    return $ofertas_activas;
}

// Funciones de aplicaciones
function aplicarOferta($candidato_id, $oferta_id) {
    $aplicaciones = leerJSON(APLICACIONES_FILE);
    
    // Verificar si ya aplicó
    foreach ($aplicaciones as $aplicacion) {
        if ($aplicacion['candidato_id'] === $candidato_id && $aplicacion['oferta_id'] === $oferta_id) {
            return false; // Ya aplicó a esta oferta
        }
    }
    
    $nueva_aplicacion = [
        'id' => generarId(),
        'candidato_id' => $candidato_id,
        'oferta_id' => $oferta_id,
        'fecha_aplicacion' => date('Y-m-d H:i:s'),
        'estado' => 'pendiente'
    ];
    
    $aplicaciones[] = $nueva_aplicacion;
    guardarJSON(APLICACIONES_FILE, $aplicaciones);
    
    return true;
}

function obtenerAplicacionesOferta($oferta_id) {
    $aplicaciones = leerJSON(APLICACIONES_FILE);
    $resultado = [];
    
    foreach ($aplicaciones as $aplicacion) {
        if ($aplicacion['oferta_id'] === $oferta_id) {
            $resultado[] = $aplicacion;
        }
    }
    
    return $resultado;
}

function obtenerAplicacionesCandidato($candidato_id) {
    $aplicaciones = leerJSON(APLICACIONES_FILE);
    $resultado = [];
    
    foreach ($aplicaciones as $aplicacion) {
        if ($aplicacion['candidato_id'] === $candidato_id) {
            // Agregar información de la oferta
            $oferta = obtenerOferta($aplicacion['oferta_id']);
            if ($oferta) {
                $aplicacion['oferta'] = $oferta;
                $resultado[] = $aplicacion;
            }
        }
    }
    
    return $resultado;
}

function contarAplicacionesOferta($oferta_id) {
    $aplicaciones = leerJSON(APLICACIONES_FILE);
    $contador = 0;
    
    foreach ($aplicaciones as $aplicacion) {
        if ($aplicacion['oferta_id'] === $oferta_id) {
            $contador++;
        }
    }
    
    return $contador;
}
