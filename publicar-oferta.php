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
        $datos_oferta = [
            'empresa_id' => $empresa_id,
            'empresa' => $empresa['nombre'],
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'requisitos' => $requisitos,
            'ubicacion' => $_POST['ubicacion'] ?? '',
            'tipo_contrato' => $_POST['tipo_contrato'] ?? '',
            'jornada' => $_POST['jornada'] ?? '',
            'salario' => $_POST['salario'] ?? ''
        ];
        
        $oferta_id = crearOferta($datos_oferta);
        
        if ($oferta_id) {
            $exito = true;
        } else {
            $error = 'Error al publicar la oferta.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Oferta de Empleo - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-blue-600 mb-6">Publicar Nueva Oferta de Empleo</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($exito): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Oferta publicada exitosamente. <a href="dashboard-empresa.php" class="underline">Volver al panel</a>
                </div>
            <?php else: ?>
                <form method="POST" action="" class="space-y-6">
                    <div class="mb-4">
                        <label for="titulo" class="block text-gray-700 font-medium mb-2">Título del Puesto *</label>
                        <input type="text" id="titulo" name="titulo" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>"
                               placeholder="Ej: Desarrollador Web Frontend">
                    </div>
                    
                    <div class="mb-4">
                        <label for="descripcion" class="block text-gray-700 font-medium mb-2">Descripción del Puesto *</label>
                        <textarea id="descripcion" name="descripcion" rows="6" required
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Detalle las responsabilidades y funciones del puesto..."><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="requisitos" class="block text-gray-700 font-medium mb-2">Requisitos *</label>
                        <textarea id="requisitos" name="requisitos" rows="4" required
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Experiencia, formación, habilidades necesarias..."><?php echo isset($_POST['requisitos']) ? htmlspecialchars($_POST['requisitos']) : ''; ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ubicacion" class="block text-gray-700 font-medium mb-2">Ubicación</label>
                            <input type="text" id="ubicacion" name="ubicacion"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo isset($_POST['ubicacion']) ? htmlspecialchars($_POST['ubicacion']) : ''; ?>"
                                   placeholder="Ej: Madrid, Remoto, etc.">
                        </div>
                        
                        <div>
                            <label for="tipo_contrato" class="block text-gray-700 font-medium mb-2">Tipo de Contrato</label>
                            <select id="tipo_contrato" name="tipo_contrato"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="indefinido" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] === 'indefinido') ? 'selected' : ''; ?>>Indefinido</option>
                                <option value="temporal" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] === 'temporal') ? 'selected' : ''; ?>>Temporal</option>
                                <option value="practicas" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] === 'practicas') ? 'selected' : ''; ?>>Prácticas</option>
                                <option value="freelance" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] === 'freelance') ? 'selected' : ''; ?>>Freelance</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="jornada" class="block text-gray-700 font-medium mb-2">Jornada Laboral</label>
                            <select id="jornada" name="jornada"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="completa" <?php echo (isset($_POST['jornada']) && $_POST['jornada'] === 'completa') ? 'selected' : ''; ?>>Completa</option>
                                <option value="parcial" <?php echo (isset($_POST['jornada']) && $_POST['jornada'] === 'parcial') ? 'selected' : ''; ?>>Parcial</option>
                                <option value="flexible" <?php echo (isset($_POST['jornada']) && $_POST['jornada'] === 'flexible') ? 'selected' : ''; ?>>Flexible</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="salario" class="block text-gray-700 font-medium mb-2">Salario (opcional)</label>
                            <input type="text" id="salario" name="salario"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo isset($_POST['salario']) ? htmlspecialchars($_POST['salario']) : ''; ?>"
                                   placeholder="Ej: 30.000€ - 35.000€ anuales">
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-6">
                        <a href="dashboard-empresa.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            Publicar Oferta
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
