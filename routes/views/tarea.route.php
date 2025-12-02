<?php
/**
 * Rutas para el módulo de Tareas
 * routes/tarea.route.php
 */
// Ruta para administrar tareas
$router->get('/sistema/tareas/administrar', function() {
    renderView('tareas/administrar', ['title' => '🛠️ Administrar Tareas']);
});

// Ruta para editar una tarea existente
$router->get('sistema/tareas/programacion', function() {
    renderView('tareas/programacion', ['title' => '🎯 Tareas Programadas']);
});

$router->get('/sistema/tareas/agregar/programacion', function() {
    renderView('tareas/nueva-programacion', ['title' => '➕ Agregar Programación de Tareas']);
});

$router->get('/sistema/programacion/visualizar', function() {
    renderView('tareas/visualizar_programa', ['title' => '👀 Visualizar Programación de Tareas']);
});

$router->get('/sistema/alertas/programa', function() {
    renderView('tareas/alerta_programa', ['title' => '⚠️ Alerta de Programación de Tareas']);
});

// ...más rutas de Tareas...