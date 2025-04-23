<?php
session_start();
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EmpleoYa - Plataforma de Empleos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <section class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2 pr-4">
                    <h1 class="text-4xl font-bold text-blue-600 mb-4">Encuentra tu próximo empleo o al candidato ideal</h1>
                    <p class="text-lg text-gray-700 mb-6">EmpleoYa conecta a profesionales talentosos con las mejores empresas.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="register.php?tipo=candidato" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center">Soy Candidato</a>
                        <a href="register.php?tipo=empresa" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center">Soy Empresa</a>
                    </div>
                </div>
                <div class="md:w-1/2 mt-6 md:mt-0">
                    <img src="assets/img/hero-image.jpg" alt="Personas trabajando" class="rounded-lg shadow-md w-full">
                </div>
            </div>
        </section>
        
        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Últimas Ofertas de Empleo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $ofertas = getUltimasOfertas(6);
                if (empty($ofertas)) {
                    echo '<p class="text-gray-500 col-span-3 text-center">No hay ofertas disponibles en este momento.</p>';
                } else {
                    foreach ($ofertas as $oferta) {
                        ?>
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                            <h3 class="text-xl font-semibold text-blue-600 mb-2"><?php echo htmlspecialchars($oferta['titulo']); ?></h3>
                            <p class="text-gray-700 mb-2"><strong>Empresa:</strong> <?php echo htmlspecialchars($oferta['empresa']); ?></p>
                            <p class="text-gray-600 mb-4 line-clamp-3"><?php echo htmlspecialchars(substr($oferta['descripcion'], 0, 150)) . '...'; ?></p>
                            <a href="ver-oferta.php?id=<?php echo $oferta['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium">Ver detalles →</a>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            
            <div class="text-center mt-6">
                <a href="ver-ofertas.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Ver todas las ofertas</a>
            </div>
        </section>
        
        <section class="bg-blue-50 rounded-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">¿Por qué usar EmpleoYa?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-blue-600 text-4xl mb-4">📝</div>
                    <h3 class="text-xl font-semibold mb-2">CV Digital Completo</h3>
                    <p class="text-gray-600">Crea un currículum digital detallado que destaque tus habilidades y experiencia.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-blue-600 text-4xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold mb-2">Búsqueda Simplificada</h3>
                    <p class="text-gray-600">Encuentra fácilmente las mejores ofertas de trabajo que se ajusten a tu perfil.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-blue-600 text-4xl mb-4">🤝</div>
                    <h3 class="text-xl font-semibold mb-2">Conexión Directa</h3>
                    <p class="text-gray-600">Conecta directamente con empresas y candidatos sin intermediarios.</p>
                </div>
            </div>
        </section>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
