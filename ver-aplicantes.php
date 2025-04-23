<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como empresa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: login.php');
    exit;
}

// Verificar si se proporcionó un ID de oferta
if (!isset($_GET['oferta_id'])) {
    header('Location: dashboard-empresa.php');
    exit;
}

$oferta_id = $_GET['oferta_id'];
$oferta = obtenerOferta($oferta_id);

// Verificar que la oferta exista y pertenezca a esta empresa
if (!$oferta || $oferta['empresa_id'] !== $_SESSION['usuario']['id']) {
    header('Location: dashboard-empresa.php');
    exit;
}

// Obtener aplicaciones para esta oferta
$aplicaciones = obtenerAplicacionesOferta($oferta_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos para <?php echo htmlspecialchars($oferta['titulo']); ?> - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-blue-600 mb-2">
                        Candidatos para: <?php echo htmlspecialchars($oferta['titulo']); ?>
                    </h1>
                    <p class="text-gray-600">
                        <?php echo count($aplicaciones); ?> candidato(s) han aplicado a esta oferta
                    </p>
                </div>
                
                <a href="dashboard-empresa.php" class="mt-4 md:mt-0 text-blue-600 hover:text-blue-800">
                    ← Volver al panel
                </a>
            </div>
            
            <?php if (empty($aplicaciones)): ?>
                <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg text-center">
                    <p class="text-yellow-700">Aún no hay candidatos que hayan aplicado a esta oferta.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left border-b">Candidato</th>
                                <th class="py-3 px-4 text-left border-b">Fecha de Aplicación</th>
                                <th class="py-3 px-4 text-left border-b">Estado</th>
                                <th class="py-3 px-4 text-left border-b">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aplicaciones as $aplicacion): 
                                $candidato = obtenerUsuario('candidato', $aplicacion['candidato_id']);
                                $cv = obtenerCV($aplicacion['candidato_id']);
                            ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <?php if ($candidato): ?>
                                            <div class="flex items-center">
                                                <?php if ($cv && !empty($cv['foto'])): ?>
                                                    <img src="<?php echo htmlspecialchars($cv['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($candidato['nombre']); ?>" 
                                                         class="w-10 h-10 rounded-full mr-3 object-cover">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full mr-3 flex items-center justify-center">
                                                        <span class="text-gray-500">👤</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="font-medium"><?php echo htmlspecialchars($candidato['nombre']); ?></p>
                                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($candidato['correo']); ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-500">Candidato no disponible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php echo date('d/m/Y H:i', strtotime($aplicacion['fecha_aplicacion'])); ?>
                                    </td>
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
                                        <?php if ($cv): ?>
                                            <a href="ver-cv-candidato.php?id=<?php echo $aplicacion['candidato_id']; ?>" class="text-blue-600 hover:text-blue-800">
                                                Ver CV
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500">CV no disponible</span>
                                        <?php endif; ?>
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
