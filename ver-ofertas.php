<?php
session_start();
include_once 'includes/functions.php';

// Obtener todas las ofertas activas
$ofertas = obtenerTodasOfertas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas de Empleo - EmpleoYa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include_once 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-blue-600 mb-6">Ofertas de Empleo Disponibles</h1>
            
            <div class="mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-600">Encuentra tu próxima oportunidad laboral entre nuestras ofertas disponibles.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" id="buscar" placeholder="Buscar ofertas..." 
                               class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button id="btn-buscar" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (empty($ofertas)): ?>
                <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg text-center">
                    <p class="text-yellow-700">No hay ofertas disponibles en este momento.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="ofertas-container">
                    <?php foreach ($ofertas as $oferta): ?>
                        <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow oferta-card">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-blue-600 mb-2 oferta-titulo">
                                    <?php echo htmlspecialchars($oferta['titulo']); ?>
                                </h2>
                                <p class="text-gray-700 mb-2"><strong>Empresa:</strong> <span class="oferta-empresa"><?php echo htmlspecialchars($oferta['empresa']); ?></span></p>
                                
                                <?php if (!empty($oferta['ubicacion'])): ?>
                                    <p class="text-gray-700 mb-2"><strong>Ubicación:</strong> <span class="oferta-ubicacion"><?php echo htmlspecialchars($oferta['ubicacion']); ?></span></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($oferta['tipo_contrato'])): ?>
                                    <p class="text-gray-700 mb-2"><strong>Contrato:</strong> 
                                        <span class="oferta-contrato">
                                            <?php 
                                            $tipos_contrato = [
                                                'indefinido' => 'Indefinido',
                                                'temporal' => 'Temporal',
                                                'practicas' => 'Prácticas',
                                                'freelance' => 'Freelance'
                                            ];
                                            echo isset($tipos_contrato[$oferta['tipo_contrato']]) ? $tipos_contrato[$oferta['tipo_contrato']] : $oferta['tipo_contrato'];
                                            ?>
                                        </span>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="mt-4 mb-4">
                                    <p class="text-gray-600 line-clamp-3 oferta-descripcion"><?php echo htmlspecialchars(substr($oferta['descripcion'], 0, 150)) . '...'; ?></p>
                                </div>
                                
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($oferta['fecha_publicacion'])); ?>
                                    </span>
                                    <a href="ver-oferta.php?id=<?php echo $oferta['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Ver detalles →
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include_once 'includes/footer.php'; ?>
    
    <script>
        // Búsqueda simple de ofertas
        document.getElementById('btn-buscar').addEventListener('click', buscarOfertas);
        document.getElementById('buscar').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                buscarOfertas();
            }
        });
        
        function buscarOfertas() {
            const busqueda = document.getElementById('buscar').value.toLowerCase();
            const ofertas = document.querySelectorAll('.oferta-card');
            
            ofertas.forEach(oferta => {
                const titulo = oferta.querySelector('.oferta-titulo').textContent.toLowerCase();
                const empresa = oferta.querySelector('.oferta-empresa').textContent.toLowerCase();
                const descripcion = oferta.querySelector('.oferta-descripcion').textContent.toLowerCase();
                const ubicacion = oferta.querySelector('.oferta-ubicacion')?.textContent.toLowerCase() || '';
                
                if (titulo.includes(busqueda) || empresa.includes(busqueda) || 
                    descripcion.includes(busqueda) || ubicacion.includes(busqueda)) {
                    oferta.style.display = 'block';
                } else {
                    oferta.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
