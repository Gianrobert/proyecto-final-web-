<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold text-blue-600">EmpleoYa</a>
            
            <nav class="hidden md:flex space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-blue-600">Inicio</a>
                <a href="ver-ofertas.php" class="text-gray-700 hover:text-blue-600">Ofertas</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <?php if ($_SESSION['usuario']['tipo'] === 'candidato'): ?>
                        <a href="dashboard-candidato.php" class="text-gray-700 hover:text-blue-600">Mi Perfil</a>
                        <a href="mis-aplicaciones.php" class="text-gray-700 hover:text-blue-600">Mis Aplicaciones</a>
                    <?php else: ?>
                        <a href="dashboard-empresa.php" class="text-gray-700 hover:text-blue-600">Mi Empresa</a>
                        <a href="publicar-oferta.php" class="text-gray-700 hover:text-blue-600">Publicar Oferta</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
            
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <span class="hidden md:inline text-gray-700">Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="login.php" class="text-blue-600 hover:text-blue-800">Iniciar Sesión</a>
                    <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Registrarse</a>
                <?php endif; ?>
            </div>
            
            <button class="md:hidden text-gray-700 focus:outline-none" id="menu-toggle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Menú móvil -->
        <div class="md:hidden hidden mt-4 pb-2" id="mobile-menu">
            <a href="index.php" class="block py-2 text-gray-700 hover:text-blue-600">Inicio</a>
            <a href="ver-ofertas.php" class="block py-2 text-gray-700 hover:text-blue-600">Ofertas</a>
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if ($_SESSION['usuario']['tipo'] === 'candidato'): ?>
                    <a href="dashboard-candidato.php" class="block py-2 text-gray-700 hover:text-blue-600">Mi Perfil</a>
                    <a href="mis-aplicaciones.php" class="block py-2 text-gray-700 hover:text-blue-600">Mis Aplicaciones</a>
                <?php else: ?>
                    <a href="dashboard-empresa.php" class="block py-2 text-gray-700 hover:text-blue-600">Mi Empresa</a>
                    <a href="publicar-oferta.php" class="block py-2 text-gray-700 hover:text-blue-600">Publicar Oferta</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    // Toggle para menú móvil
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
