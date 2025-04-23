<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como empresa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: login.php');
    exit;
}

// Verificar si se proporcionó un ID de candidato
if (!isset($_GET['id'])) {
    header('Location: dashboard-empresa.php');
    exit;
}

$candidato_id = $_GET['id'];
$candidato = obtenerUsuario('candidato', $candidato_id);
$cv = obtenerCV($candidato_id);

// Verificar que el candidato y CV existan
if (!$candidato || !$cv) {
    header('Location: dashboard-empresa.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV de <?php echo htmlspecialchars($candidato['nombre']); ?> - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-600">
                    Currículum de <?php echo htmlspecialchars($candidato['nombre']); ?>
                </h1>
                
                <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800">
                    ← Volver
                </a>
            </div>
            
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 mb-6 md:mb-0 md:pr-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="flex flex-col items-center mb-4">
                            <?php if (!empty($cv['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($cv['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($candidato['nombre']); ?>" 
                                     class="w-32 h-32 object-cover rounded-full border-4 border-blue-500 mb-4">
                            <?php else: ?>
                                <div class="w-32 h-32 bg-gray-200 rounded-full border-4 border-blue-500 flex items-center justify-center mb-4">
                                    <span class="text-4xl text-gray-500">👤</span>
                                </div>
                            <?php endif; ?>
                            
                            <h2 class="text-xl font-bold"><?php echo htmlspecialchars($cv['nombre'] . ' ' . $cv['apellidos']); ?></h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($candidato['correo']); ?></p>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="font-semibold text-blue-700 mb-2">Información de Contacto</h3>
                            <p class="mb-2"><strong>Teléfono:</strong> <?php echo htmlspecialchars($cv['telefono']); ?></p>
                            <p class="mb-2"><strong>Dirección:</strong> <?php echo htmlspecialchars($cv['direccion']); ?></p>
                            <p class="mb-2"><strong>Ciudad:</strong> <?php echo htmlspecialchars($cv['ciudad']); ?></p>
                        </div>
                        
                        <?php if (!empty($cv['redes'])): ?>
                            <div class="mt-6">
                                <h3 class="font-semibold text-blue-700 mb-2">Redes Profesionales</h3>
                                <p><?php echo htmlspecialchars($cv['redes']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-6">
                            <h3 class="font-semibold text-blue-700   ?>
                        
                        <div class="mt-6">
                            <h3 class="font-semibold text-blue-700 mb-2">Idiomas</h3>
                            <p><?php echo htmlspecialchars($cv['idiomas']); ?></p>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="font-semibold text-blue-700 mb-2">Disponibilidad</h3>
                            <p>
                                <?php 
                                $disponibilidad = [
                                    'inmediata' => 'Inmediata',
                                    '15dias' => 'En 15 días',
                                    '1mes' => 'En 1 mes',
                                    'negociar' => 'A negociar'
                                ];
                                echo isset($disponibilidad[$cv['disponibilidad']]) ? $disponibilidad[$cv['disponibilidad']] : $cv['disponibilidad'];
                                ?>
                            </p>
                        </div>
                        
                        <?php if (!empty($cv['cv_pdf'])): ?>
                            <div class="mt-6">
                                <a href="<?php echo htmlspecialchars($cv['cv_pdf']); ?>" target="_blank" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block text-center w-full">
                                    Ver CV en PDF
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="md:w-2/3">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-700 mb-4">Objetivo Profesional</h3>
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($cv['objetivo_profesional'])); ?></p>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-700 mb-4">Formación Académica</h3>
                        <div class="mb-4">
                            <p class="font-medium"><?php echo htmlspecialchars($cv['titulo_academico']); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($cv['institucion']); ?></p>
                            <?php if (!empty($cv['fecha_titulo'])): ?>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($cv['fecha_titulo']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-700 mb-4">Experiencia Laboral</h3>
                        <div class="mb-4">
                            <p class="font-medium"><?php echo htmlspecialchars($cv['ultimo_puesto']); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($cv['ultima_empresa']); ?></p>
                            <?php if (!empty($cv['fecha_experiencia'])): ?>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($cv['fecha_experiencia']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-700 mb-4">Habilidades</h3>
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($cv['habilidades'])); ?></p>
                    </div>
                    
                    <?php if (!empty($cv['logros'])): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-blue-700 mb-4">Logros y Proyectos Destacados</h3>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($cv['logros'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cv['referencias'])): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-700 mb-4">Referencias</h3>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($cv['referencias'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
