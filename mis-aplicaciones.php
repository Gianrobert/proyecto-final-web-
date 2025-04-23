<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como candidato
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'candidato') {
    header('Location: login.php');
    exit;
}

$candidato_id = $_SESSION['usuario']['id'];
$aplicaciones = obtenerAplicacionesCandidato($candidato_id);

// Filtrar por estado si se especifica
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $estado = $_GET['estado'];
    $aplicaciones = array_filter($aplicaciones, function($aplicacion) use ($estado) {
        return $aplicacion['estado'] === $estado;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Aplicaciones - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-blue-600 mb-2">Mis Aplicaciones</h1>
                    <p class="text-gray-600">
                        <?php echo count($aplicaciones); ?> aplicación(es) 
                        <?php echo isset($_GET['estado']) ? 'en estado ' . $_GET['estado'] : 'total(es)'; ?>
                    </p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <a href="dashboard-candidato.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver al panel
                    </a>
                </div>
            </div>
            
            <?php if (empty($aplicaciones)): ?>
                <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg text-center">
                    <p class="text-yellow-700 mb-4">
                        <?php if (isset($_GET['estado'])): ?>
                            No tienes aplicaciones en estado "<?php echo htmlspecialchars($_GET['estado']); ?>".
                        <?php else: ?>
                            Aún no has aplicado a ninguna oferta de empleo.
                        <?php endif; ?>
                    </p>
                    <a href="ver-ofertas.php" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg inline-block">
                        Ver ofertas disponibles
                    </a>
                </div>
            <?php else: ?>
                <div class="mb-6 flex flex-wrap gap-2">
                    <a href="mis-aplicaciones.php" class="<?php echo !isset($_GET['estado']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> px-4 py-2 rounded-lg">
                        Todas
                    </a>
                    <a href="mis-aplicaciones.php?estado=pendiente" class="<?php echo (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> px-4 py-2 rounded-lg">
                        Pendientes
                    </a>
                    <a href="mis-aplicaciones.php?estado=revisado" class="<?php echo (isset($_GET['estado']) && $_GET['estado'] === 'revisado') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> px-4 py-2 rounded-lg">
                        Revisadas
                    </a>
                    <a href="mis-aplicaciones.php?estado=entrevista" class="<?php echo (isset($_GET['estado']) && $_GET['estado'] === 'entrevista') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> px-4 py-2 rounded-lg">
                        Entrevista
                    </a>
                    <a href="mis-aplicaciones.php?estado=rechazado" class="<?php echo (isset($_GET['estado']) && $_GET['estado'] === 'rechazado') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> px-4 py-2 rounded-lg">
                        Rechazadas
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left border-b">Puesto</th>
                                <th class="py-3 px-4 text-left border-b">Empresa</th>
                                <th class="py-3 px-4 text-left border-b">Fecha de Aplicación</th>
                                <th class="py-3 px-4 text-left border-b">Estado</th>
                                <th class="py-3 px-4 text-left border-b">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aplicaciones as $aplicacion): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium">
                                        <?php echo htmlspecialchars($aplicacion['oferta']['titulo']); ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php echo htmlspecialchars($aplicacion['oferta']['empresa']); ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php echo date('d/m/Y', strtotime($aplicacion['fecha_aplicacion'])); ?>
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
                                        <a href="ver-oferta.php?id=<?php echo $aplicacion['oferta_id']; ?>" class="text-blue-600 hover:text-blue-800">
                                            Ver oferta
                                        </a>
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
