<?php
require_once __DIR__ . '/../core/load.php';

// Intentar hacer logout
if($session->logout()) {
    // Logout exitoso, redirigir al login
    redirectTo('sistema', false);
} else {
    // Si el logout falla por alguna razón, aún así redirigir al login
    redirectTo('sistema', false);
}