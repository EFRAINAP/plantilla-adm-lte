<?php
require_once('../01_General/00_load.php');

// Configurar la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// ✅ VALIDACIÓN DE SESIÓN Y PERMISOS
$user = current_user();
if (!$user) {
    echo json_encode(['error' => true, 'message' => 'Sesión no válida.']);
    exit;
}

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => true, 'message' => 'Método no permitido.']);
    exit;
}

// ✅ VALIDACIÓN Y SANITIZACIÓN DE ENTRADA
$fechaSeleccionada = $_POST['var_fecha'] ?? '';
$tipoIntervalo = $_POST['var_tipo_intervalo'] ?? 'dias'; // dias, semanal, mensual, bimensual, trimestral, cuatrimestral, anual, bianual
$intervalo = $_POST['var_intervalo'] ?? 1; // Para días y semanas
$anio = $_POST['var_anio'] ?? 0;

// Validar fecha
if (empty($fechaSeleccionada) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
    echo json_encode(['error' => true, 'message' => 'Formato de fecha inválido. Use YYYY-MM-DD.']);
    exit;
}

// Validar que la fecha sea válida
if (!strtotime($fechaSeleccionada)) {
    echo json_encode(['error' => true, 'message' => 'Fecha no válida.']);
    exit;
}

// Validar tipo de intervalo
$tiposValidos = ['Diaria', 'Semanal', 'Quincenal', 'Mensual', 'Bimensual', 'Trimestral', 'Cuatrimestral', 'Semestral', 'Anual', 'Bianual'];
if (!in_array($tipoIntervalo, $tiposValidos)) {
    echo json_encode(['error' => true, 'message' => 'Tipo de intervalo no válido.']);
    exit;
}

// Validar intervalo para días y semanas
if (in_array($tipoIntervalo, ['Diaria', 'Semanal'])) {
    $intervalo = intval($intervalo);
    if ($intervalo <= 0 || $intervalo > 365) {
        echo json_encode(['error' => true, 'message' => 'Intervalo debe ser entre 1 y 365.']);
        exit;
    }
}

// Validar año
$anio = intval($anio);
$anioActual = date('Y');
if ($anio < 2024 || $anio > ($anioActual + 3)) {
    echo json_encode(['error' => true, 'message' => 'Año no válido.']);
    exit;
}

// ✅ VALIDACIÓN DE FECHA INICIAL - USAR AÑO DEL CALENDARIO TAMA
$fechaInicial = new DateTime($fechaSeleccionada);
$calendarioInicial = find_table_field_only('calendario', 'fecha', $fechaSeleccionada);
if (!$calendarioInicial) {
    echo json_encode(['error' => true, 'message' => 'La fecha seleccionada no existe en el calendario TAMA.']);
    exit;
}

// Usar el año del calendario TAMA, no el año natural de la fecha
$anioCalendarioTama = $calendarioInicial['anio'];
if ($anioCalendarioTama != $anio) {
    echo json_encode(['error' => true, 'message' => 'La fecha seleccionada no corresponde al año ' . $anio . ' según el calendario TAMA.']);
    exit;
}

/**
 * Función para verificar si una fecha pertenece al año del calendario TAMA
 */
function perteneceAlAnioCalendario($fecha, $anioObjetivo) {
    $calendario = find_table_field_only('calendario', 'fecha', $fecha);
    if (!$calendario) {
        return false;
    }
    return $calendario['anio'] == $anioObjetivo;
}

/**
 * Función para verificar si un día es laborable
 */
function esDiaLaborable($fecha) {
    global $db;
    
    $fechaObj = new DateTime($fecha);
    $diaSemana = $fechaObj->format('N'); // 1 = Lunes, 7 = Domingo
    
    // Verificar si es feriado en la tabla calendario
    $calendario = find_table_field_only('calendario', 'fecha', $fecha);
    if ($calendario && isset($calendario['tipo_dia'])) {
        $tipoDia = strtolower($calendario['tipo_dia']);
        if (in_array($tipoDia, ['feriado', 'festivo', 'no laborable'])) {
            return false;
        }
    }
    
    // Sábado (6) y Domingo (7) no son laborables por defecto
    if ($diaSemana == 6 || $diaSemana == 7) {
        return false;
    }
    
    return true;
}

/**
 * Función para encontrar el siguiente día laborable
 */
function siguienteDiaLaborable($fecha) {
    $fechaObj = new DateTime($fecha);
    $intentos = 0;
    $maxIntentos = 14; // Máximo 2 semanas buscando
    
    while (!esDiaLaborable($fechaObj->format('Y-m-d')) && $intentos < $maxIntentos) {
        $fechaObj->add(new DateInterval('P1D'));
        $intentos++;
    }
    
    return $fechaObj->format('Y-m-d');
}

/**
 * Función para generar fechas según el tipo de intervalo
 */
function generarFechas($fechaInicial, $tipoIntervalo, $intervalo, $anioCalendario) {
    $fechas = [];
    $fechaActual = new DateTime($fechaInicial);
    $maxFechas = 365; // Aumentar límite para permitir más fechas

    switch ($tipoIntervalo) {
        case 'Diaria':
            for ($i = 0; $i < $maxFechas; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval("P{$intervalo}D"));
            }
            break;

        case 'Semanal':
            $diasSemana = $intervalo * 7;
            for ($i = 0; $i < 53; $i++) { // Máximo 53 semanas
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval("P{$diasSemana}D"));
            }
            break;
        
        case 'Quincenal':
            $diasQuincenal = $intervalo * 15;
            for ($i = 0; $i < 26; $i++) { // Máximo 26 quincenas
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval("P{$diasQuincenal}D"));
            }
            break;

        case 'Mensual':
            $diaDelMes = $fechaActual->format('d');
            for ($i = 0; $i < 12; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                // Obtener el último día del mes actual
                $ultimoDiaMes = $fechaActual->format('t');
                
                // Si el día seleccionado es mayor al último día del mes, usar el último día
                $diaFinal = min($diaDelMes, $ultimoDiaMes);
                
                $fechaFinal = $fechaActual->format('Y-m-') . str_pad($diaFinal, 2, '0', STR_PAD_LEFT);
                $fechaLaborable = siguienteDiaLaborable($fechaFinal);
                
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval('P1M'));
            }
            break;

        case 'Bimensual':
            $diaDelMes = $fechaActual->format('d');
            for ($i = 0; $i < 6; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $ultimoDiaMes = $fechaActual->format('t');
                $diaFinal = min($diaDelMes, $ultimoDiaMes);
                
                $fechaFinal = $fechaActual->format('Y-m-') . str_pad($diaFinal, 2, '0', STR_PAD_LEFT);
                $fechaLaborable = siguienteDiaLaborable($fechaFinal);
                
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval('P2M'));
            }
            break;

        case 'Trimestral':
            $diaDelMes = $fechaActual->format('d');
            for ($i = 0; $i < 4; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $ultimoDiaMes = $fechaActual->format('t');
                $diaFinal = min($diaDelMes, $ultimoDiaMes);
                
                $fechaFinal = $fechaActual->format('Y-m-') . str_pad($diaFinal, 2, '0', STR_PAD_LEFT);
                $fechaLaborable = siguienteDiaLaborable($fechaFinal);
                
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval('P3M'));
            }
            break;

        case 'Cuatrimestral':
            $diaDelMes = $fechaActual->format('d');
            for ($i = 0; $i < 3; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $ultimoDiaMes = $fechaActual->format('t');
                $diaFinal = min($diaDelMes, $ultimoDiaMes);
                
                $fechaFinal = $fechaActual->format('Y-m-') . str_pad($diaFinal, 2, '0', STR_PAD_LEFT);
                $fechaLaborable = siguienteDiaLaborable($fechaFinal);
                
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval('P4M'));
            }
            break;
        
        case 'Semestral':
            $diaDelMes = $fechaActual->format('d');
            for ($i = 0; $i < 2; $i++) {
                $fechaFormateada = $fechaActual->format('Y-m-d');
                
                // Verificar si la fecha pertenece al año del calendario TAMA
                if (!perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                    break;
                }
                
                $ultimoDiaMes = $fechaActual->format('t');
                $diaFinal = min($diaDelMes, $ultimoDiaMes);
                
                $fechaFinal = $fechaActual->format('Y-m-') . str_pad($diaFinal, 2, '0', STR_PAD_LEFT);
                $fechaLaborable = siguienteDiaLaborable($fechaFinal);
                
                $fechas[] = $fechaLaborable;
                $fechaActual->add(new DateInterval('P6M'));
            }
            break;

        case 'Anual':
            $fechaFormateada = $fechaActual->format('Y-m-d');
            
            // Verificar si la fecha pertenece al año del calendario TAMA
            if (perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
            }
            break;

        case 'Bianual':
            $fechaFormateada = $fechaActual->format('Y-m-d');
            
            // Verificar si la fecha pertenece al año del calendario TAMA
            if (perteneceAlAnioCalendario($fechaFormateada, $anioCalendario)) {
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
            }
            
            // Agregar la fecha del siguiente año
            $fechaActual->add(new DateInterval('P1Y'));
            $fechaFormateada = $fechaActual->format('Y-m-d');
            if (perteneceAlAnioCalendario($fechaFormateada, $anioCalendario + 1)) {
                $fechaLaborable = siguienteDiaLaborable($fechaFormateada);
                $fechas[] = $fechaLaborable;
            }
            break;
    }
    
    return $fechas;
}


try {
    // ✅ GENERAR FECHAS USANDO EL AÑO DEL CALENDARIO TAMA
    $fechasGeneradas = generarFechas($fechaSeleccionada, $tipoIntervalo, $intervalo, $anioCalendarioTama);
    
    // ✅ VALIDAR QUE SE GENERARON FECHAS
    if (empty($fechasGeneradas)) {
        echo json_encode(['error' => true, 'message' => 'No se pudieron generar fechas para los parámetros especificados.']);
        exit;
    }
    
    // ✅ CONSTRUIR DATOS DETALLADOS
    $data = [];
    foreach ($fechasGeneradas as $index => $fecha) {
        $fechaObj = new DateTime($fecha);
        $calendario = find_table_field_only('calendario', 'fecha', $fecha);
        
        // Determinar fecha original según el tipo de intervalo
        $fechaOriginal = '';
        switch ($tipoIntervalo) {
            case 'Diaria':
                $fechaOriginal = date('Y-m-d', strtotime($fechaSeleccionada . " + " . ($index * $intervalo) . " days"));
                break;
            case 'Semanal':
                $fechaOriginal = date('Y-m-d', strtotime($fechaSeleccionada . " + " . ($index * $intervalo * 7) . " days"));
                break;
            case 'Quincenal':
                $fechaOriginal = date('Y-m-d', strtotime($fechaSeleccionada . " + " . ($index * $intervalo * 15) . " days"));
                break;
            default:
                $fechaOriginal = $fecha; // Para intervalos complejos, usar la fecha generada
        }
        
        $fueAjustada = ($fechaOriginal !== $fecha);
        
        $data[] = [
            'entregable' => 'Entregable N°' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
            'fecha' => $fecha,
            'fecha_original' => $fechaOriginal,
            'fue_ajustada' => $fueAjustada,
            'semana' => $calendario['semana'] ?? 'S' . $fechaObj->format('W'),
            'mes' => $calendario['mes'] ?? $fechaObj->format('F'),
            'anio_calendario' => $calendario['anio'] ?? $fechaObj->format('Y'),
            'dia_semana' => $fechaObj->format('l'),
            'tipo_dia' => $calendario['tipo_dia'] ?? 'Laborable',
            'es_laborable' => esDiaLaborable($fecha),
            'ajuste_info' => $fueAjustada ? 'Fecha ajustada por día no laborable' : null
        ];
    }
    
    // ✅ RESPUESTA EXITOSA CON METADATOS
    echo json_encode([
        'error' => false,
        'message' => 'Fechas generadas correctamente',
        'data' => $data,
        'metadata' => [
            'total_fechas' => count($data),
            'tipo_intervalo' => $tipoIntervalo,
            'intervalo' => $intervalo,
            'fecha_inicio' => $fechaSeleccionada,
            'fecha_fin' => end($data)['fecha'],
            'anio_solicitado' => $anio,
            'anio_calendario_tama' => $anioCalendarioTama,
            'fechas_ajustadas' => count(array_filter($data, function($item) {
                return $item['fue_ajustada'];
            }))
        ]
    ]);
    
} catch (Exception $e) {
    // ✅ MANEJO DE ERRORES MEJORADO
    error_log("Error en 08_ajax_tareas_fechas.php: " . $e->getMessage());
    echo json_encode([
        'error' => true, 
        'message' => 'Error interno del servidor. Contacte al administrador.',
        'debug' => $e->getMessage() // Incluir debug para diagnosticar problemas
    ]);
}
?>