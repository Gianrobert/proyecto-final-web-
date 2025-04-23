<?php
session_start();
include_once 'includes/functions.php';

$error = '';

// Verificar si ya está logueado
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario']['tipo'] === 'candidato') {
        header('Location: dashboard-candidato.php');
    } else {
        header('Location: dashboard-empresa.php');
    }
    exit;
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($correo) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        $resultado = verificarLogin($correo, $password);
        
        if ($resultado) {
            $_SESSION['usuario'] = $resultado;
            
            if ($resultado['tipo'] === 'candidato') {
                header('Location: dashboard-candidato.php');
            } else {
                header('Location: dashboard-empresa.php');
            }
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Iniciar Sesión</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="correo" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">¿No tienes una cuenta?</p>
                <div class="mt-2 flex justify-center space-x-4">
                    <a href="register.php?tipo=candidato" class="text-blue-600 hover:text-blue-800">Registrarse como Candidato</a>
                    <span class="text-gray-400">|</span>
                    <a href="register.php?tipo=empresa" class="text-blue-600 hover:text-blue-800">Registrarse como Empresa</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
