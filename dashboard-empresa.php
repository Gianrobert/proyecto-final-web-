<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como empresa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['usuario']['id'];
$empresa = obtenerUsuario('empresa', $empresa_id);
$ofertas = obtenerOfertasEmpresa($empresa_id);

// Contar aplicaciones totales
$total_aplicaciones = 0;
foreach ($ofertas as $oferta) {
    $aplicaciones = obtenerAplicacionesOferta($oferta['id']);
    $total_aplicaciones += count($aplicaciones);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empresa - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row items-start">
                <div class="md:w-1/4 mb-6 md:mb-0 flex flex-col items-center">
                    <div class="w-40 h-40 bg-blue-100 rounded-full border-4 border-blue-500 flex items-center justify-center">
                        <span class="text-4xl text-blue-500">🏢</span>
                    </div>
                    
                    <h1 class="text-2xl font-bold text-center mt-4"><?php echo htmlspecialchars($empresa['nombre']); ?></h1>
                    <p class="text-gray-600 text-center"><?php echo htmlspecialchars($empresa['correo']); ?></p>
                </div>
                
                <div class="md:w-3/4 md:pl-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <h2 class="text-xl font-semibold text-blue-600 mb-2 md:mb-0">Información de la Empresa</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-700 mb-2">Datos de Contacto</h3>
                            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($empresa['direccion']); ?></p>
                            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($empresa['telefono']); ?></p>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-700 mb-2">Estadísticas</h3>
                            <p><strong>Ofertas Publicadas:</strong> <?php echo count($ofertas); ?></p>
                            <p><strong>Aplicaciones Recibidas:</strong> <?php echo $total_aplicaciones; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Ofertas Activas</h3>
                    <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php 
                        $ofertas_activas = array_filter($ofertas, function($oferta) {
                            return isset($oferta['activa']) && $oferta['activa'] === true;
                        });
                        echo count($ofertas_activas); 
                        ?>
                    </span>
                </div>
                <p class="text-gray-600">Ofertas de empleo publicadas actualmente</p>
                <a href="mis-ofertas.php" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Ver todas →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Aplicaciones</h3>
                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full">
                        <?php echo $total_aplicaciones; ?>
                    </span>
                </div>
                <p class="text-gray-600">Total de candidatos que han aplicado</p>
                <a href="mis-ofertas.php" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Ver detalles →</a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Publicar Oferta</h3>
                    <span class="text-3xl text-blue-500">+</span>
                </div>
                <p class="text-gray-600">Crea una nueva oferta de empleo</p>
                <a href="publicar-oferta.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mt-4 inline-block">
                    Crear oferta
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-blue-600">Mis Ofertas de Empleo</h2>
                <a href="publicar-oferta.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Nueva Oferta
                </a>
            </div>
            
            <?php if (empty($ofertas)): ?>
                <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg text-center">
                    <p class="text-yellow-700 mb-4">Aún no has publicado ninguna oferta de empleo.</p>
                    <a href="publicar-oferta.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg inline-block">
                        Publicar mi primera oferta
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left">Título</th>
                                <th class="py-3 px-4 text-left">Fecha</th>
                                <th class="py-3 px-4 text-left">Estado</th>
                                <th class="py-3 px-4 text-left">Aplicaciones</th>
                                <th class="py-3 px-4 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ofertas as $oferta): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($oferta['titulo']); ?></td>
                                    <td class="py-3 px-4"><?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if (isset($oferta['activa']) && $oferta['activa']): ?>
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activa</span>
                                        <?php else: ?>
                                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php 
                                        $num_aplicaciones = contarAplicacionesOferta($oferta['id']);
                                        echo '<span class="font-semibold">' . $num_aplicaciones . '</span>';
                                        ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <a href="ver-oferta.php?id=<?php echo $oferta['id']; ?>" class="text-blue-600 hover:text-blue-800">Ver</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="editar-oferta.php?id=<?php echo $oferta['id']; ?>" class="text-green-600 hover:text-green-800">Editar</a>
                                            <span class="text-gray-300">|</span>
                                            <a href="ver-aplicantes.php?oferta_id=<?php echo $oferta['id']; ?>" class="text-purple-600 hover:text-purple-800">
                                                Aplicantes
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
