<?php

 //$page_title = 'Functions';
 $errors = array();

 /*--------------------------------------------------------------*/
 /* Function for Remove escapes special
 /* characters in a string for use in an SQL statement
 /*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  if ($con instanceof PDO) {
    // Usar PDO::quote para escapar
    return $con->quote($str);
  } elseif ($con instanceof mysqli) {
    return mysqli_real_escape_string($con, $str);
  } else {
    // Fallback: devolver el string sin modificar
    return $str;
  }
}
/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}
/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}
/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." No puede estar en blanco.";
      return $errors;
    }
  }
}
/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg ='' || array()){
   $output = '';
   if(!empty($msg)) {
      foreach ($msg as $key => $value) {
         $output  .= "<div class=\"alert alert-{$key} alert-dismissible fade show\" role=\"alert\">";
         $output  .= remove_junk(first_character($value));
         $output  .= "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";
         $output  .= "</div>";
      }
      return $output;
   } else {
     return "";
   }
}
/*--------------------------------------------------------------*/
/* Function for redirect - Simple y escalable
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    // Si no es URL completa, agregar el dominio base
    if (!preg_match('/^https?:\/\//', $url)) {
        $base = 'http://localhost/sistema-new';
        $url = $base . '/' . ltrim($url, '/');
    }
    
    header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    exit();
}

/*--------------------------------------------------------------*/
/* Function for redirect usando Config (más robusta)
/*--------------------------------------------------------------*/
function redirectTo($path, $permanent = false)
{
    // Usar la clase Config si está disponible
    if (class_exists('Config')) {
        $url = Config::url($path);
    } else {
        // Fallback al método anterior
        redirect($path, $permanent);
        return;
    }
    
    if (headers_sent() === false) {
        header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}
/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('d/m/Y g:i:s a', strtotime($str));
     else
      return null;
  }
  
  function read_date1($str){
     if($str)
      return date('d/m/Y', strtotime($str));
     else
      return null;
  }
/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}

function make_date1(){
  return strftime("%Y-%m-%d", time());
}

function  make_date2(){
     
 return strftime("%H:%M:%S", time());
    
  }

  function  HoraLima(){
 date_default_timezone_set('America/Lima');
 return strftime("%H:%M:%S", time());
    
  }
/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}
/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}

?>