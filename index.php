<?php
	$page_title = 'Index';
	ob_start();
	define('BASE_URL', '/sistema-new');
	require_once('app/core/00_load.php');
	if($session->isUserLoggedIn(true)) { redirect('dashboard', false);}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAMA - Sistema de Gestión</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    
    <!-- CSS Único para Login -->
    <link rel="stylesheet" href="public/assets/css/login-standalone.css">
    
    <!-- Bootstrap Icons únicamente -->
    <link rel="stylesheet" href="00_Librerias/Bootstrap-icons-1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="login-container">
        <!-- Partículas de fondo -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <!-- Header con logo -->
        <header class="login-header">
            <div class="logo-container">
                <img src="public/img/logito.png" alt="TAMA Logo" class="logo">
                <div class="company-info">
                    <h1>TAMA INGENIEROS</h1>
                    <p>Sistema de Gestión Documental</p>
                </div>
            </div>
        </header>
        
        <!-- Contenido principal -->
        <main class="login-main">
            <!-- Imagen lateral -->
            <div class="image-section">
                <div class="image-overlay">
                    <h2>Bienvenido</h2>
                    <p>Accede al sistema de gestión documental</p>
                    <div class="features">
                        <!--div class="feature">
                            <i class="bi bi-shield-check"></i>
                            <span>Seguro</span>
                        </div-->
                        <div class="feature">
                            <i class="bi bi-files"></i>
                            <span>Documentos</span>
                        </div>
                        <!--div class="feature">
                            <i class="bi bi-graph-up"></i>
                            <span>Eficiente</span>
                        </div-->
                        <div class="feature">
                            <i class="bi bi-people"></i>
                            <span>Colaborativo</span>
                        </div>
                        <div class="feature">
                            <i class="bi bi-clipboard"></i>
                            <span>Tareas</span>
                        </div>
                    </div>
                </div>
                <img src="public/img/image1-2.png" alt="Background" class="bg-image">
            </div>
            
            <!-- Formulario de login -->
            <div class="form-section">
                <div class="login-card">
                    <div class="card-header">
                        <i class="bi bi-person-lock"></i>
                        <h3>Iniciar Sesión</h3>
                        <p>Ingresa tus credenciales para acceder</p>
                    </div>
                    
                    <form method="post" action="app/auth/authenticate.php" class="login-form" id="loginForm">
                        <?php if(isset($msg) && !empty($msg)): ?>
                            <div class="alert alert-error">
                                <i class="bi bi-exclamation-triangle"></i>
                                <?php echo display_msg($msg) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="bi bi-person input-icon"></i>
                                <input type="text" name="username" id="username" required>
                                <label for="username">Usuario</label>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="input-group">
                            <div class="input-wrapper">
                                <i class="bi bi-lock input-icon"></i>
                                <input type="password" name="password" id="password" required>
                                <label for="password">Contraseña</label>
                                <div class="input-line"></div>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember">
                                <span class="checkmark"></span>
                                Recordarme
                            </label>
                        </div>
                        
                        <button type="submit" class="login-btn" id="loginBtn">
                            <span class="btn-text">
                                <i class="bi bi-arrow-right-circle"></i>
                                Iniciar Sesión
                            </span>
                            <div class="btn-loader">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>
                    
                    <div class="card-footer">
                        <a href="https://tamaingenieros.pe/" target="_blank" class="website-link">
                            <i class="bi bi-globe"></i>
                            Visitar sitio web
                        </a>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="login-footer">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> TAMA INGENIEROS S.A.C. - Sistema de Gestión v2.0</p>
                <p>Desarrollado por: C. Loayza • J. Sovero • E. Acevedo</p>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }
        
        // Form animations and validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            const loginBtn = document.getElementById('loginBtn');
            
            // Input animations
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.value !== '') {
                        this.parentElement.classList.add('filled');
                    } else {
                        this.parentElement.classList.remove('filled');
                    }
                });
            });
            
            // Form submission
            form.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
                
                setTimeout(() => {
                    if (!form.checkValidity()) {
                        loginBtn.classList.remove('loading');
                        loginBtn.disabled = false;
                    }
                }, 100);
            });
            
            // Auto focus on username
            document.getElementById('username').focus();
        });
        
        // Particles animation
        function createParticle() {
            const particles = document.querySelector('.particles');
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
            particles.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 5000);
        }
        
        setInterval(createParticle, 300);
    </script>
</body>
</html>
