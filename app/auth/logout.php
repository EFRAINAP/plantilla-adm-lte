<?php
require_once __DIR__ . '/../core/00_load.php';

// Intentar hacer logout
if($session->logout()) {
    // Logout exitoso, redirigir al login
    redirect('', false);
} else {
    // Si el logout falla por alguna razón, aún así redirigir al login
    redirect('', false);
}