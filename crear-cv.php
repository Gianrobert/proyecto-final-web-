<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como candidato
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'candidato') {
    header('Location: login.php');
    exit;
}

$candidato_id = $_SESSION['usuario']['id'];
$cv = obtenerCV($candidato_id);
$error = '';
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $datos_cv = [
        'candidato_id' => $candidato_id,
        'nombre' => $_POST['nombre'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'ciudad' => $_POST['ciudad'] ?? '',
        'titulo_academico' => $_POST['titulo_academico'] ?? '',
        'institucion' => $_POST['institucion'] ?? '',
        'fecha_titulo' => $_POST['fecha_titulo'] ?? '',
        'ultimo_puesto' => $_POST['ultimo_puesto'] ?? '',
        'ultima_empresa' => $_POST['ultima_empresa'] ?? '',
        'fecha_experiencia' => $_POST['fecha_experiencia'] ?? '',
        'habilidades' => $_POST['habilidades'] ?? '',
        'idiomas' => $_POST['idiomas'] ?? '',
        'objetivo_profesional' => $_POST['objetivo_profesional'] ?? '',
        'logros' => $_POST['logros'] ?? '',
        'disponibilidad' => $_POST['disponibilidad'] ?? '',
        'redes' => $_POST['redes'] ?? '',
        'referencias' => $_POST['referencias'] ?? ''
    ];
    
    // Si ya existe un CV, mantener los archivos existentes a menos que se suban nuevos
    if ($cv) {
        $datos_cv['foto'] = $cv['foto'] ?? '';
        $datos_cv['cv_pdf'] = $cv['cv_pdf'] ?? '';
    }
    
    // Procesar foto si se ha subido
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nombre = uniqid() . '_' . $_FILES['foto']['name'];
        $foto_destino = 'uploads/fotos/' . $foto_nombre;
        
        // Verificar que sea una imagen
        $tipo = mime_content_type($foto_tmp);
        if (strpos($tipo, 'image/') === 0) {
            if (move_uploaded_file($foto_tmp, $foto_destino)) {
                $datos_cv['foto'] = $foto_destino;
            } else {
                $error = 'Error al subir la foto.';
            }
        } else {
            $error = 'El archivo subido no es una imagen válida.';
        }
    }
    
    // Procesar PDF si se ha subido
    if (isset($_FILES['cv_pdf']) && $_FILES['cv_pdf']['error'] === UPLOAD_ERR_OK) {
        $pdf_tmp = $_FILES['cv_pdf']['tmp_name'];
        $pdf_nombre = uniqid() . '_' . $_FILES['cv_pdf']['name'];
        $pdf_destino = 'uploads/pdfs/' . $pdf_nombre;
        
        // Verificar que sea un PDF
        $tipo = mime_content_type($pdf_tmp);
        if ($tipo === 'application/pdf') {
            if (move_uploaded_file($pdf_tmp, $pdf_destino)) {
                $datos_cv['cv_pdf'] = $pdf_destino;
            } else {
                $error = 'Error al subir el PDF.';
            }
        } else {
            $error = 'El archivo subido no es un PDF válido.';
        }
    }
    
    // Guardar CV si no hay errores
    if (empty($error)) {
        if (guardarCV($datos_cv)) {
            $exito = true;
            $cv = obtenerCV($candidato_id); // Actualizar datos del CV
        } else {
            $error = 'Error al guardar el CV.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $cv ? 'Actualizar' : 'Crear'; ?> CV - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-blue-600 mb-6">
                <?php echo $cv ? 'Actualizar mi Currículum' : 'Crear mi Currículum'; ?>
            </h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($exito): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    CV guardado exitosamente. <a href="dashboard-candidato.php" class="underline">Volver al panel</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">Información Personal</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-gray-700 font-medium mb-2">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['nombre']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="apellidos" class="block text-gray-700 font-medium mb-2">Apellido(s)</label>
                            <input type="text" id="apellidos" name="apellidos" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['apellidos']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="telefono" class="block text-gray-700 font-medium mb-2">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['telefono']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="direccion" class="block text-gray-700 font-medium mb-2">Dirección</label>
                            <input type="text" id="direccion" name="direccion" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['direccion']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="ciudad" class="block text-gray-700 font-medium mb-2">Ciudad / Provincia</label>
                            <input type="text" id="ciudad" name="ciudad" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['ciudad']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="foto" class="block text-gray-700 font-medium mb-2">Foto (opcional)</label>
                              class="block text-gray-700 font-medium mb-2">Foto (opcional)</label>
                            <input type="file" id="foto" name="foto" accept="image/*"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php if ($cv && !empty($cv['foto'])): ?>
                                <div class="mt-2 flex items-center">
                                    <img src="<?php echo htmlspecialchars($cv['foto']); ?>" alt="Foto actual" class="w-16 h-16 object-cover rounded-full mr-2">
                                    <span class="text-sm text-gray-600">Foto actual</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">Formación Académica</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="titulo_academico" class="block text-gray-700 font-medium mb-2">Título Académico</label>
                            <input type="text" id="titulo_academico" name="titulo_academico" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['titulo_academico']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="institucion" class="block text-gray-700 font-medium mb-2">Institución</label>
                            <input type="text" id="institucion" name="institucion" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['institucion']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="fecha_titulo" class="block text-gray-700 font-medium mb-2">Fecha de Titulación</label>
                            <input type="text" id="fecha_titulo" name="fecha_titulo" placeholder="Ej: 2018-2022"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['fecha_titulo']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">Experiencia Laboral</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ultimo_puesto" class="block text-gray-700 font-medium mb-2">Último Puesto</label>
                            <input type="text" id="ultimo_puesto" name="ultimo_puesto" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['ultimo_puesto']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="ultima_empresa" class="block text-gray-700 font-medium mb-2">Empresa</label>
                            <input type="text" id="ultima_empresa" name="ultima_empresa" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['ultima_empresa']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="fecha_experiencia" class="block text-gray-700 font-medium mb-2">Período</label>
                            <input type="text" id="fecha_experiencia" name="fecha_experiencia" placeholder="Ej: 2020-2023"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['fecha_experiencia']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">Habilidades y Competencias</h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="habilidades" class="block text-gray-700 font-medium mb-2">Habilidades Clave</label>
                            <textarea id="habilidades" name="habilidades" rows="3" required
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $cv ? htmlspecialchars($cv['habilidades']) : ''; ?></textarea>
                            <p class="text-sm text-gray-500 mt-1">Separa las habilidades con comas</p>
                        </div>
                        
                        <div>
                            <label for="idiomas" class="block text-gray-700 font-medium mb-2">Idiomas</label>
                            <input type="text" id="idiomas" name="idiomas" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['idiomas']) : ''; ?>">
                            <p class="text-sm text-gray-500 mt-1">Ej: Español (nativo), Inglés (B2), Francés (básico)</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">Información Adicional</h2>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="objetivo_profesional" class="block text-gray-700 font-medium mb-2">Objetivo Profesional / Resumen</label>
                            <textarea id="objetivo_profesional" name="objetivo_profesional" rows="3" required
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $cv ? htmlspecialchars($cv['objetivo_profesional']) : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label for="logros" class="block text-gray-700 font-medium mb-2">Logros o Proyectos Destacados</label>
                            <textarea id="logros" name="logros" rows="3"
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $cv ? htmlspecialchars($cv['logros']) : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label for="disponibilidad" class="block text-gray-700 font-medium mb-2">Disponibilidad</label>
                            <select id="disponibilidad" name="disponibilidad" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="inmediata" <?php echo ($cv && $cv['disponibilidad'] === 'inmediata') ? 'selected' : ''; ?>>Inmediata</option>
                                <option value="15dias" <?php echo ($cv && $cv['disponibilidad'] === '15dias') ? 'selected' : ''; ?>>En 15 días</option>
                                <option value="1mes" <?php echo ($cv && $cv['disponibilidad'] === '1mes') ? 'selected' : ''; ?>>En 1 mes</option>
                                <option value="negociar" <?php echo ($cv && $cv['disponibilidad'] === 'negociar') ? 'selected' : ''; ?>>A negociar</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="redes" class="block text-gray-700 font-medium mb-2">Redes Profesionales</label>
                            <input type="text" id="redes" name="redes"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo $cv ? htmlspecialchars($cv['redes']) : ''; ?>"
                                   placeholder="Ej: LinkedIn: linkedin.com/in/usuario">
                        </div>
                        
                        <div>
                            <label for="referencias" class="block text-gray-700 font-medium mb-2">Referencias</label>
                            <textarea id="referencias" name="referencias" rows="2"
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Disponibles a petición o detalles de contacto"><?php echo $cv ? htmlspecialchars($cv['referencias']) : ''; ?></textarea>
                        </div>
                        
                        <div>
                            <label for="cv_pdf" class="block text-gray-700 font-medium mb-2">CV en PDF (opcional)</label>
                            <input type="file" id="cv_pdf" name="cv_pdf" accept="application/pdf"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php if ($cv && !empty($cv['cv_pdf'])): ?>
                                <div class="mt-2">
                                    <a href="<?php echo htmlspecialchars($cv['cv_pdf']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        CV actual en PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <a href="dashboard-candidato.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        <?php echo $cv ? 'Actualizar CV' : 'Guardar CV'; ?>
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
