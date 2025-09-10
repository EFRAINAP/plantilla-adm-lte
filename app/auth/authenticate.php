<?php 
//$page_title = 'Authenticate';
require_once('../core/00_load.php'); 

$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
  $user_name = authenticate($username, $password);
  if($user_name){
    //create session with id
     $session->login($user_name);
    //Update Sign in time
     updateLastLogIn($user_name);
     $session->msg("s", "Bienvenido a Tamadom.");
     // Redirigir al dashboard usando el sistema de rutas
     redirect('dashboard', false);

  } else {
    $session->msg("d", "Nombre de usuario y/o contraseña incorrecto.");
    // Redirigir al login/index
    redirect('', false);
  }
 
} else {
   $session->msg("d", $errors);
   // Redirigir al login con errores
   redirect('', false);
}

?>
