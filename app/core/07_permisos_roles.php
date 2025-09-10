<?php

// Permisos para la gestión de DOSIER
function puedeModificarDatosGenerales($user) {
    return $user['user_level'] == 1 
        || ($user['user_level'] == 2 && $user['cargo'] == 'PER-AC-01');
}

function puedeDescargarPdf($user) {
    return $user['user_level'] == 1 
        //|| ($user['user_level'] == 2 && $user['proceso'] == 'AC');
        || $user['proceso'] == 'AC';
}

function puedeModificarDatosEspecificos($user, $cod_dosier) {
    $asignado_query = find_table_field_only('dosier_calidad', 'cod_dosier', $cod_dosier);
    $asignado = $asignado_query['asignado'] ?? null;

    return $user['user_level'] == 1
        || ($user['user_level'] == 2 && $user['cargo'] == 'PER-AC-01')
        || ($user['user_level'] == 3 
            && ($user['proceso'] == 'AC')
            && $asignado == $user['username']);
}
