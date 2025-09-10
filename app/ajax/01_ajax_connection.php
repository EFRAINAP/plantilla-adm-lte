<?php 

require_once(__DIR__ . '/../core/01_constants.php');
    
class connection_ajax{	  
    
	public static function conectar() {        
		       
        $opciones = array( 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );	
        try{
			$conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, $opciones);
            return $conexion;
        }catch (Exception $e){
            throw new Exception("Error de Conexión: " . $e->getMessage());
        }
    }
}