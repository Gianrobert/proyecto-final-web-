<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como empresa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: login.php');
    exit;
}

$empresa_id = $_SESSION['usuario']['id'];
$ofertas = obtenerOfertasEmpresa($empresa_id);

// Ordenar ofertas por fecha (más recientes primero)
usort($ofertas, function($a, $b) {
    return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Ofertas - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-blue-600 mb-2">Mis Ofertas de Empleo</h1>
                    <p class="text-gray-600">
                        <?php echo count($ofertas); ?> oferta(s) publicada(s)
                    </p>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-4">
                    <a href="dashboard-empresa.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver al panel
                    </a>
                    <a href="publicar-oferta.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Nueva Oferta
                    </a>
                </div>
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
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left border-b">Título</th>
                                <th class="py-3 px-4 text-left border-b">Fecha</th>
                                <th class="py-3 px-4 text-left border-b">Estado</th>
                                <th class="py-3 px-4 text-left border-b">Aplicaciones</th>
                                <th class="py-3 px-4 text-left border-b">Acciones</th>
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
                                                Aplicantes (<?php echo $num_aplicaciones; ?>)
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
