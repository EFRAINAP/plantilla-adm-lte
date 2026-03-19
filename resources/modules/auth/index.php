<?php
$page_title = 'Iniciar Sesión';
ob_start();
require_once BASE_PATH . '/app/core/load.php';
require_once BASE_PATH . '/app/Config/Config.php';
if($session->isUserLoggedIn(true)) {redirectTo('dashboard', false);}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMPRESA - Sistema de Gestión</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <!-- Google Font: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= fontawesome('css/all.min.css') ?>">
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= asset('tailwind.css') ?>">
    
    <style>
        /* Animaciones personalizadas para partículas */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
        }
        
        @keyframes float-delayed {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
            50% { transform: translateY(-30px) rotate(-180deg); opacity: 1; }
        }
        
        .particle {
            position: absolute;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.6), rgba(147, 51, 234, 0.6));
            border-radius: 50%;
            pointer-events: none;
        }
        
        .particle:nth-child(1) { width: 8px; height: 8px; top: 20%; left: 10%; animation: float 6s ease-in-out infinite; }
        .particle:nth-child(2) { width: 12px; height: 12px; top: 60%; left: 20%; animation: float-delayed 8s ease-in-out infinite; }
        .particle:nth-child(3) { width: 6px; height: 6px; top: 80%; left: 80%; animation: float 7s ease-in-out infinite; }
        .particle:nth-child(4) { width: 10px; height: 10px; top: 30%; left: 70%; animation: float-delayed 9s ease-in-out infinite; }
        .particle:nth-child(5) { width: 14px; height: 14px; top: 70%; left: 5%; animation: float 5s ease-in-out infinite; }
        .particle:nth-child(6) { width: 8px; height: 8px; top: 10%; left: 90%; animation: float-delayed 10s ease-in-out infinite; }
        
        /* Efecto de input focus */
        .input-focused .input-line {
            transform: scaleX(1);
        }
        
        .input-line {
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: left;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 min-h-screen overflow-hidden">
    <!-- Partículas de fondo -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Header con logo -->
    <header class="relative z-10 pt-6 px-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img src="<?= assetPublicImages('logito.png') ?>" alt="EMPRESA Logo" class="h-12 w-auto">
                <div>
                    <h1 class="text-white text-xl font-bold">EMPRESA INGENIEROS</h1>
                    <p class="text-blue-200 text-sm">Sistema de Gestión Documental</p>
                </div>
            </div>
            <a href="<?= url('inicio') ?>" class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm text-white rounded-lg hover:bg-white/20 transition-all border border-white/20">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Inicio
            </a>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
            
            <!-- Imagen lateral -->
            <div class="hidden lg:flex items-center justify-center relative">
                <div class="relative w-full h-[750px] rounded-3xl overflow-hidden shadow-2xl group">
                    <!-- Imagen de fondo con efectos -->
                    <div class="absolute inset-0">
                        <img src="<?= assetPublicImages('image1-2.png') ?>" alt="Background" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <!-- Overlay degradado más sutil -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/60 via-indigo-800/50 to-purple-900/60"></div>
                        <!-- Efecto de brillo -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent"></div>
                    </div>
                    
                    <!-- Contenido con mejor estructura -->
                    <div class="relative z-10 h-full flex flex-col justify-center items-center text-white p-8">
                        <!-- Header elegante -->
                        <div class="text-center mb-12">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full mb-6 shadow-lg">
                                <i class="fas fa-building text-3xl text-white drop-shadow-lg"></i>
                            </div>
                            <h2 class="text-4xl font-bold mb-4 drop-shadow-lg tracking-wide">Bienvenido</h2>
                            <p class="text-xl opacity-90 drop-shadow-md max-w-xs leading-relaxed">Sistema de gestión documental empresarial</p>
                        </div>
                        
                        <!-- Cards de características con diseño premium -->
                        <div class="grid grid-cols-2 gap-6 max-w-xs">
                            <div class="group/card cursor-pointer">
                                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20 hover:border-white/30">
                                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl p-4 mb-4 mx-auto w-fit shadow-lg">
                                        <i class="fas fa-file-alt text-2xl text-white"></i>
                                    </div>
                                    <span class="text-sm font-semibold drop-shadow-sm">Documentos</span>
                                </div>
                            </div>
                            
                            <div class="group/card cursor-pointer">
                                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20 hover:border-white/30">
                                    <div class="bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl p-4 mb-4 mx-auto w-fit shadow-lg">
                                        <i class="fas fa-users text-2xl text-white"></i>
                                    </div>
                                    <span class="text-sm font-semibold drop-shadow-sm">Colaborativo</span>
                                </div>
                            </div>
                            
                            <div class="group/card cursor-pointer">
                                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20 hover:border-white/30">
                                    <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl p-4 mb-4 mx-auto w-fit shadow-lg">
                                        <i class="fas fa-tasks text-2xl text-white"></i>
                                    </div>
                                    <span class="text-sm font-semibold drop-shadow-sm">Tareas</span>
                                </div>
                            </div>
                            
                            <div class="group/card cursor-pointer">
                                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-6 text-center transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20 hover:border-white/30">
                                    <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl p-4 mb-4 mx-auto w-fit shadow-lg">
                                        <i class="fas fa-chart-line text-2xl text-white"></i>
                                    </div>
                                    <span class="text-sm font-semibold drop-shadow-sm">Eficiente</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicador de calidad -->
                        <div class="mt-8 flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse delay-75"></div>
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse delay-150"></div>
                            </div>
                            <span class="text-xs font-medium opacity-80">Sistema activo</span>
                        </div>
                    </div>
                    
                    <!-- Decoraciones flotantes -->
                    <div class="absolute top-4 right-4 w-16 h-16 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-8 left-6 w-24 h-24 bg-blue-400/20 rounded-full blur-2xl"></div>
                </div>
            </div>

            <!-- Formulario de login -->
            <div class="flex items-center justify-center">
                <div class="w-full max-w-md">
                    <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl p-8 border border-white/20">
                        <div class="text-center mb-8">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-full p-3 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <i class="fas fa-user-lock text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Iniciar Sesión</h3>
                            <p class="text-gray-600">Ingresa tus credenciales para acceder</p>
                        </div>

                        <form method="post" action="<?= BASE_URL ?>/app/auth/authenticate.php" class="space-y-6" id="loginForm">
                            <?php if(isset($msg) && !empty($msg)): ?>
                                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <?php echo display_msg($msg) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Campo Usuario -->
                            <div class="input-group">
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                    Usuario
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="username" 
                                        id="username" 
                                        required 
                                        class="block w-full pl-12 pr-4 py-4 border-0 ring-1 ring-gray-300 rounded-xl  focus:ring-offset-0 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                        placeholder="Ingresa tu usuario"
                                    >
                                    <div class="input-line absolute bottom-0 left-0 right-0"></div>
                                </div>
                            </div>
                            
                            <!-- Campo Contraseña -->
                            <div class="input-group">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password" 
                                        required 
                                        class="block w-full pl-12 pr-12 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 focus:bg-white"
                                        placeholder="Ingresa tu contraseña"
                                    >
                                    <button 
                                        type="button" 
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center hover:bg-gray-100 rounded-r-xl transition-colors"
                                        onclick="togglePassword()"
                                    >
                                        <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="passwordIcon"></i>
                                    </button>
                                    <div class="input-line absolute bottom-0 left-0 right-0"></div>
                                </div>
                            </div>
                            
                            <!-- Recordarme -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" name="remember" class="sr-only">
                                    <div class="relative">
                                        <div class="w-5 h-5 bg-gray-200 border border-gray-300 rounded group-hover:bg-gray-50 transition-colors"></div>
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-check text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Recordarme</span>
                                </label>
                            </div>
                            
                            <!-- Botón Submit -->
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 font-medium flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                id="loginBtn"
                            >
                                <span id="btnText" class="flex items-center">
                                    <i class="fas fa-arrow-right-circle mr-3"></i>
                                    Iniciar Sesión
                                </span>
                                <span id="btnLoader" class="hidden">
                                    <div class="flex items-center">
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                        Iniciando...
                                    </div>
                                </span>
                            </button>
                        </form>
                        
                        <!-- Footer del formulario -->
                        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                            <a href="https://empresa.com/" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium inline-flex items-center hover:underline transition-colors">
                                <i class="fas fa-globe mr-2"></i>
                                Visitar sitio web
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="relative z-10 pb-6 px-6">
        <div class="text-center text-white/80">
            <p class="text-sm mb-2">&copy; <?php echo date('Y'); ?> EMPRESA INGENIEROS S.A.C. - Sistema de Gestión v2.0</p>
            <p class="text-xs text-white/60">Desarrollado por: C. Loayza • J. Sovero • E. Acevedo</p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash text-gray-400 hover:text-gray-600';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye text-gray-400 hover:text-gray-600';
            }
        }
        
        // Form submission handling with loading animation
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
        });
        
        // Enhanced input focus effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            
            inputs.forEach(input => {
                const inputGroup = input.closest('.input-group');
                
                input.addEventListener('focus', function() {
                    if (inputGroup) {
                        inputGroup.classList.add('input-focused');
                    }
                    this.parentElement.classList.add('ring-2', 'ring-blue-500');
                });
                
                input.addEventListener('blur', function() {
                    if (inputGroup) {
                        inputGroup.classList.remove('input-focused');
                    }
                    this.parentElement.classList.remove('ring-2', 'ring-blue-500');
                });
            });
            
            // Auto focus on username
            document.getElementById('username').focus();
            
            // Checkbox animation
            const checkbox = document.querySelector('input[name="remember"]');
            const checkboxContainer = checkbox.closest('label');
            
            checkbox.addEventListener('change', function() {
                const checkIcon = checkboxContainer.querySelector('.fa-check');
                if (this.checked) {
                    checkIcon.parentElement.classList.remove('opacity-0');
                    checkIcon.parentElement.classList.add('opacity-100');
                    checkIcon.parentElement.previousElementSibling.classList.add('bg-blue-600', 'border-blue-600');
                    checkIcon.parentElement.previousElementSibling.classList.remove('bg-gray-200', 'border-gray-300');
                } else {
                    checkIcon.parentElement.classList.add('opacity-0');
                    checkIcon.parentElement.classList.remove('opacity-100');
                    checkIcon.parentElement.previousElementSibling.classList.remove('bg-blue-600', 'border-blue-600');
                    checkIcon.parentElement.previousElementSibling.classList.add('bg-gray-200', 'border-gray-300');
                }
            });
        });
    </script>
</body>
</html>
