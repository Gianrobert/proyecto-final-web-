<?php
session_start();
include_once 'includes/functions.php';

// Verificar si está logueado como empresa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'empresa') {
    header('Location: login.php');
    exit;
}

// Verificar si se proporcionó un ID de oferta
if (!isset($_GET['id'])) {
    header('Location: mis-ofertas.php');
    exit;
}

$oferta_id = $_GET['id'];
$oferta = obtenerOferta($oferta_id);

// Verificar que la oferta exista y pertenezca a esta empresa
if (!$oferta || $oferta['empresa_id'] !== $_SESSION['usuario']['id']) {
    header('Location: mis-ofertas.php');
    exit;
}

$error = '';
$exito = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $requisitos = $_POST['requisitos'] ?? '';
    
    if (empty($titulo) || empty($descripcion) || empty($requisitos)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        // Actualizar datos de la oferta
        $ofertas = leerJSON(OFERTAS_FILE);
        
        foreach ($ofertas as $key => $o) {
            if ($o['id'] === $oferta_id) {
                $ofertas[$key]['titulo'] = $titulo;
                $ofertas[$key]['descripcion'] = $descripcion;
                $ofertas[$key]['requisitos'] = $requisitos;
                $ofertas[$key]['ubicacion'] = $_POST['ubicacion'] ?? '';
                $ofertas[$key]['tipo_contrato'] = $_POST['tipo_contrato'] ?? '';
                $ofertas[$key]['jornada'] = $_POST['jornada'] ?? '';
                $ofertas[$key]['salario'] = $_POST['salario'] ?? '';
                $ofertas[$key]['activa'] = isset($_POST['activa']) ? true : false;
                
                guardarJSON(OFERTAS_FILE, $ofertas);
                $oferta = $ofertas[$key]; // Actualizar oferta local
                $exito = true;
                break;
            }
        }
        
        if (!$exito) {
            $error = 'Error al actualizar la oferta.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-blue-600 mb-6">Editar Oferta de Empleo</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($exito): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Oferta actualizada exitosamente. <a href="mis-ofertas.php" class="underline">Volver a mis ofertas</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-6">
                <div class="mb-4">
                    <label for="titulo" class="block text-gray-700 font-medium mb-2">Título del Puesto *</label>
                    <input type="text" id="titulo" name="titulo" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?php echo htmlspecialchars($oferta['titulo']); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 font-medium mb-2">Descripción del Puesto *</label>
                    <textarea id="descripcion" name="descripcion" rows="6" required
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($oferta['descripcion']); ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="requisitos" class="block text-gray-700 font-medium mb-2">Requisitos *</label>
                    <textarea id="requisitos" name="requisitos" rows="4" required
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($oferta['requisitos']); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="ubicacion" class="block text-gray-700 font-medium mb-2">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo htmlspecialchars($oferta['ubicacion'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label for="tipo_contrato" class="block text-gray-700 font-medium mb-2">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            <option value="indefinido" <?php echo (isset($oferta['tipo_contrato']) && $oferta['tipo_contrato'] === 'indefinido') ? 'selected' : ''; ?>>Indefinido</option>
                            <option value="temporal" <?php echo (isset($oferta['tipo_contrato']) && $oferta['tipo_contrato'] === 'temporal') ? 'selected' : ''; ?>>Temporal</option>
                            <option value="practicas" <?php echo (isset($oferta['tipo_contrato']) && $oferta['tipo_contrato'] === 'practicas') ? 'selected' : ''; ?>>Prácticas</option>
                            <option value="freelance" <?php echo (isset($oferta['tipo_contrato']) && $oferta['tipo_contrato'] === 'freelance') ? 'selected' : ''; ?>>Freelance</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="jornada" class="block text-gray-700 font-medium mb-2">Jornada Laboral</label>
                        <select id="jornada" name="jornada"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            <option value="completa" <?php echo (isset($oferta['jornada']) && $oferta['jornada'] === 'completa') ? 'selected' : ''; ?>>Completa</option>
                            <option value="parcial" <?php echo (isset($oferta['jornada']) && $oferta['jornada'] === 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                            <option value="flexible" <?php echo (isset($oferta['jornada']) && $oferta['jornada'] === 'flexible') ? 'selected' : ''; ?>>Flexible</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="salario" class="block text-gray-700 font-medium mb-2">Salario (opcional)</label>
                        <input type="text" id="salario" name="salario"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo htmlspecialchars($oferta['salario'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="activa" class="form-checkbox h-5 w-5 text-blue-600" 
                               <?php echo (isset($oferta['activa']) && $oferta['activa']) ? 'checked' : ''; ?>>
                        <span class="ml-2 text-gray-700">Oferta activa</span>
                    </label>
                </div>
                
                <div class="flex justify-between mt-6">
                    <a href="mis-ofertas.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
