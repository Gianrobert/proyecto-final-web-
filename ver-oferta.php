<?php
session_start();
include_once 'includes/functions.php';

// Verificar si se proporcionó un ID de oferta
if (!isset($_GET['id'])) {
    header('Location: ver-ofertas.php');
    exit;
}

$oferta_id = $_GET['id'];
$oferta = obtenerOferta($oferta_id);

// Si la oferta no existe, redirigir
if (!$oferta) {
    header('Location: ver-ofertas.php');
    exit;
}

$empresa = obtenerUsuario('empresa', $oferta['empresa_id']);
$aplicado = false;
$mensaje = '';

// Verificar si el usuario ya aplicó a esta oferta
if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'candidato') {
    $candidato_id = $_SESSION['usuario']['id'];
    $aplicaciones = obtenerAplicacionesCandidato($candidato_id);
    
    foreach ($aplicaciones as $aplicacion) {
        if ($aplicacion['oferta_id'] === $oferta_id) {
            $aplicado = true;
            break;
        }
    }
    
    // Procesar aplicación
    if (isset($_POST['aplicar']) && !$aplicado) {
        // Verificar si tiene CV
        $cv = obtenerCV($candidato_id);
        
        if ($cv) {
            if (aplicarOferta($candidato_id, $oferta_id)) {
                $aplicado = true;
                $mensaje = [
                    'tipo' => 'exito',
                    'texto' => 'Has aplicado exitosamente a esta oferta.'
                ];
            } else {
                $mensaje = [
                    'tipo' => 'error',
                    'texto' => 'Error al aplicar a la oferta.'
                ];
            }
        } else {
            $mensaje = [
                'tipo' => 'error',
                'texto' => 'Debes crear tu CV antes de aplicar a una oferta.'
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($oferta['titulo']); ?> - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <?php if ($mensaje): ?>
                <div class="<?php echo $mensaje['tipo'] === 'exito' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> px-4 py-3 rounded mb-4 border">
                    <?php echo $mensaje['texto']; ?>
                </div>
            <?php endif; ?>
            
            <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-blue-600 mb-2"><?php echo htmlspecialchars($oferta['titulo']); ?></h1>
                    <p class="text-gray-700 mb-1"><strong>Empresa:</strong> <?php echo htmlspecialchars($oferta['empresa']); ?></p>
                    <p class="text-gray-500 text-sm">Publicada el <?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?></p>
                </div>
                
                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'candidato'): ?>
                    <div class="mt-4 md:mt-0">
                        <?php if ($aplicado): ?>
                            <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg">
                                Ya has aplicado a esta oferta
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <button type="submit" name="aplicar" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                    Aplicar a esta oferta
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php elseif (!isset($_SESSION['usuario'])): ?>
                    <div class="mt-4 md:mt-0">
                        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg inline-block">
                            Inicia sesión para aplicar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <?php if (!empty($oferta['ubicacion'])): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Ubicación</h3>
                        <p><?php echo htmlspecialchars($oferta['ubicacion']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($oferta['tipo_contrato'])): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Tipo de Contrato</h3>
                        <p>
                            <?php 
                            $tipos_contrato = [
                                'indefinido' => 'Indefinido',
                                'temporal' => 'Temporal',
                                'practicas' => 'Prácticas',
                                'freelance' => 'Freelance'
                            ];
                            echo isset($tipos_contrato[$oferta['tipo_contrato']]) ? $tipos_contrato[$oferta['tipo_contrato']] : $oferta['tipo_contrato'];
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($oferta['jornada'])): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Jornada Laboral</h3>
                        <p>
                            <?php 
                            $tipos_jornada = [
                                'completa' => 'Completa',
                                'parcial' => 'Parcial',
                                'flexible' => 'Flexible'
                            ];
                            echo isset($tipos_jornada[$oferta['jornada']]) ? $tipos_jornada[$oferta['jornada']] : $oferta['jornada'];
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($oferta['salario'])): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">Salario</h3>
                        <p><?php echo htmlspecialchars($oferta['salario']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Descripción del Puesto</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($oferta['descripcion'])); ?></p>
                </div>
            </div>
            
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Requisitos</h2>
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($oferta['requisitos'])); ?></p>
                </div>
            </div>
            
            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'candidato'): ?>
                <div class="flex justify-between items-center mt-8">
                    <a href="ver-ofertas.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver a ofertas
                    </a>
                    
                    <?php if (!$aplicado): ?>
                        <form method="POST" action="">
                            <button type="submit" name="aplicar" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                Aplicar a esta oferta
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="mt-8">
                    <a href="ver-ofertas.php" class="text-blue-600 hover:text-blue-800">
                        ← Volver a ofertas
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
