<?php
session_start();
include_once 'includes/functions.php';

// Verificar si ya está logueado
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario']['tipo'] === 'candidato') {
        header('Location: dashboard-candidato.php');
    } else {
        header('Location: dashboard-empresa.php');
    }
    exit;
}

// Determinar tipo de registro
$tipo = isset($_GET['tipo']) && $_GET['tipo'] === 'empresa' ? 'empresa' : 'candidato';
$error = '';
$exito = false;

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    // Validaciones básicas
    if (empty($nombre) || empty($correo) || empty($password) || empty($confirmar_password)) {
        $error = 'Por favor, complete todos los campos.';
    } elseif ($password !== $confirmar_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Datos específicos según el tipo de usuario
        $datos = [
            'nombre' => $nombre,
            'correo' => $correo,
            'password' => $password
        ];
        
        if ($tipo === 'empresa') {
            $datos['direccion'] = $_POST['direccion'] ?? '';
            $datos['telefono'] = $_POST['telefono'] ?? '';
        }
        
        // Intentar registrar al usuario
        $resultado = registrarUsuario($tipo, $datos);
        
        if ($resultado) {
            $exito = true;
        } else {
            $error = 'El correo electrónico ya está registrado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de <?php echo $tipo === 'candidato' ? 'Candidato' : 'Empresa'; ?> - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">
                Registro de <?php echo $tipo === 'candidato' ? 'Candidato' : 'Empresa'; ?>
            </h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($exito): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Registro exitoso. <a href="login.php" class="underline">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="nombre" class="block text-gray-700 font-medium mb-2">
                            <?php echo $tipo === 'candidato' ? 'Nombre Completo' : 'Nombre de la Empresa'; ?>
                        </label>
                        <input type="text" id="nombre" name="nombre" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="correo" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
                    </div>
                    
                    <?php if ($tipo === 'empresa'): ?>
                        <div class="mb-4">
                            <label for="direccion" class="block text-gray-700 font-medium mb-2">Dirección</label>
                            <input type="text" id="direccion" name="direccion" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="telefono" class="block text-gray-700 font-medium mb-2">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" required
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Mínimo 6 caracteres</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="confirmar_password" class="block text-gray-700 font-medium mb-2">Confirmar Contraseña</label>
                        <input type="password" id="confirmar_password" name="confirmar_password" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
                        Registrarse
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">¿Ya tienes una cuenta?</p>
                    <a href="login.php" class="text-blue-600 hover:text-blue-800">Iniciar Sesión</a>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-gray-600">¿Quieres registrarte como 
                        <?php echo $tipo === 'candidato' ? 'empresa' : 'candidato'; ?>?</p>
                    <a href="register.php?tipo=<?php echo $tipo === 'candidato' ? 'empresa' : 'candidato'; ?>" 
                       class="text-blue-600 hover:text-blue-800">
                        Registrarse como <?php echo $tipo === 'candidato' ? 'Empresa' : 'Candidato'; ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
