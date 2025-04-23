<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como candidato
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'candidato') {
    header('Location: login.php');
    exit;
}

$candidato_id = $_SESSION['usuario']['id'];
$candidato = obtenerUsuario('candidato', $candidato_id);
$cv = obtenerCV($candidato_id);
$aplicaciones = obtenerAplicacionesCandidato($candidato_id);

// Contar aplicaciones pendientes
$aplicaciones_pendientes = 0;
foreach ($aplicaciones as $aplicacion) {
    if ($aplicacion['estado'] === 'pendiente') {
        $aplicaciones_pendientes++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Candidato - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row items-start">
                <div class="md:w-1/4 mb-6 md:mb-0 flex flex-col items-center">
                    <?php if ($cv && !empty($cv['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($cv['foto']); ?>" alt="Foto de perfil" 
                             class="w-40 h-40 object-cover rounded-full border-4 border-blue-500">
                    <?php else: ?>
                        <div class="w-40 h-40 bg-gray-200 rounded-full border-4 border-blue-500 flex items-center justify-center">
                            <span class="text-4xl text-gray-500">👤</span>
                        </div>
                    <?php endif; ?>
                    
                    <h1 class="text-2xl font-bold text-center mt-4"><?php echo htmlspecialchars($candidato['nombre']); ?></h1>
                    <p class="text-gray-600 text-center"><?php echo htmlspecialchars($candidato['correo']); ?></p>
                </div>
                
                <div class="md:w-3/4 md:pl-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h2 class="text-xl font-semibold text-blue-600 mb-2 md:mb-0">Resumen de tu Perfil</h2>
                        
                        <?php if ($cv): ?>
                            <a href="crear-cv.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                Actualizar CV
                            </a>
                        <?php else: ?>
                            <a href="crear-cv.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                Crear CV
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($cv): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-700 mb-2">Información Personal</h3>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cv['telefono']); ?></p>
                                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cv['direccion']); ?></p>
                                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($cv['ciudad']); ?></p>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-700 mb-2">Formación Académica</h3>
                                <p><strong>Título:</strong> <?php echo htmlspecialchars($cv['titulo_academico']); ?></p>
                                <p><strong>Institución:</strong> <?php echo htmlspecialchars($cv['institucion']); ?></p>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-700 mb-2">Experiencia Laboral</h3>
                                <p><strong>Último Puesto:</strong> <?php echo htmlspecialchars($cv['ultimo_puesto']); ?></p>
                                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($cv['ultima_empresa']); ?></p>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-700 mb-2">Habilidades</h3>
                                <p><?php echo htmlspecialchars($cv['habilidades']); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($cv['cv_pdf'])): ?>
                            <div class="mt-4">
                                <a href="<?php echo htmlspecialchars($cv['cv_pdf']); ?>" target="_blank" 
                                   class="text-blue-600 hover:text-blue-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Ver CV en PDF
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg text-center">
                            <p class="text-yellow-700 mb-4">Aún no has creado tu CV. Completa tu perfil para aumentar tus posibilidades de encontrar empleo.</p>
                            <a href="crear-cv.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg inline-block">
                                Crear mi CV ahora
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Aplicaciones</h3>
                    <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo count($aplicaciones); ?>
                    </span>
                </div>
                <p class="text-gray-600">Total de aplicaciones a ofertas de empleo</p>
                <a href="mis-aplicaciones.php" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Ver todas →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Pendientes</h3>
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo $aplicaciones_pendientes; ?>
                    </span>
                </div>
                <p class="text-gray-600">Aplicaciones en estado pendiente</p>
                <a href="mis-aplicaciones.php?estado=pendiente" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Ver pendientes →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Ofertas Disponibles</h3>
                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo count(obtenerTodasOfertas()); ?>
                    </span>
                </div>
                <p class="text-gray-600">Nuevas oportunidades de empleo</p>
                <a href="ver-ofertas.php" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Explorar ofertas →</a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Últimas Aplicaciones</h2>
            
            <?php if (empty($aplicaciones)): ?>
                <p class="text-gray-500 text-center py-4">Aún no has aplicado a ninguna oferta de empleo.</p>
                <div class="text-center mt-2">
                    <a href="ver-ofertas.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg inline-block">
                        Ver ofertas disponibles
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left">Puesto</th>
                                <th class="py-3 px-4 text-left">Empresa</th>
                                <th class="py-3 px-4 text-left">Fecha</th>
                                <th class="py-3 px-4 text-left">Estado</th>
                                <th class="py-3 px-4 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Mostrar solo las últimas 5 aplicaciones
                            $aplicaciones_recientes = array_slice($aplicaciones, 0, 5);
                            foreach ($aplicaciones_recientes as $aplicacion): 
                            ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($aplicacion['oferta']['titulo']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($aplicacion['oferta']['empresa']); ?></td>
                                    <td class="py-3 px-4"><?php echo date('d/m/Y', strtotime($aplicacion['fecha_aplicacion'])); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if ($aplicacion['estado'] === 'pendiente'): ?>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Pendiente</span>
                                        <?php elseif ($aplicacion['estado'] === 'revisado'): ?>
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Revisado</span>
                                        <?php elseif ($aplicacion['estado'] === 'entrevista'): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Entrevista</span>
                                        <?php elseif ($aplicacion['estado'] === 'rechazado'): ?>
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Rechazado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="ver-oferta.php?id=<?php echo $aplicacion['oferta_id']; ?>" class="text-blue-600 hover:text-blue-800">Ver oferta</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($aplicaciones) > 5): ?>
                    <div class="text-center mt-4">
                        <a href="mis-aplicaciones.php" class="text-blue-600 hover:text-blue-800">Ver todas mis aplicaciones →</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
