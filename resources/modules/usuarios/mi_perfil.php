<?php
// resources/modules/usuarios/mi_perfil.php
    $title = 'Mi Perfil';
    require_once BASE_PATH . '/app/core/load.php';
	// Verificar si el usuario está autenticado
	if(!$session->isUserLoggedIn(true)) { redirectTo('', false); }
	$user = current_user();
ob_start();

$pageStyles = '
<style>
  .avatar-option-container {
    display: flex;
    margin-bottom: 15px;
    width: 130px;
    height: 130px;
    align-items: center;
    justify-content: center;
    margin: 10px;
  }

  .avatar-option:hover {
    border: 5px solid #4fa036ff;
    transform: scale(1.05);
  }

  .avatar-option {
    cursor: pointer;
    border: 1px solid #ccc;
    transition: border-color 0.2s ease-in-out, transform 0.1s;
  }

  .avatar-selected {
    border: 5px solid #007bff !important;
  }
  
  .avatar-option input[type="radio"] {
    display: none;
  }
</style>
';
?>

<div class="page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <?php echo display_msg($msg); ?>
      </div>
      
      <div class="col-md-12 mb-4">
        <div class="card">
          <div class="card-header">
            <h5><i class="bi bi-person"></i> Editar mi cuenta</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 text-center  mb-4">
                <img class="rounded-circle img-fluid" src="<?= assetPublicImages('/uploads/'. $user['image']) ?>" alt="user-image" style="width: 200px; height: 200px; object-fit: cover;">
              </div>
              <div class="col-md-8">
                <form method="post" id="update_profile_form">
                  <div class="mb-3">
                    <label for="name" class="form-label">Nombres</label>
                    <input type="text" class="form-control" name="name" value="<?php echo remove_junk(ucwords($user['name'])); ?>">
                  </div>
                  <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" name="username" value="<?php echo remove_junk(ucwords($user['username'])); ?>" readonly>
                  </div>
                </form>
                <button id="actualizar-datos" type="submit" name="update" class="btn btn-primary">Actualizar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5><i class="bi bi-camera"></i> Elegir Foto de Perfil</h5>
          </div>
          <div class="card-body">
            <form method="post" id="select_avatar_form">
              <div class="row">
                <?php
                $ruta = BASE_PATH . '/public/img/uploads/';
                $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $files = array_diff(scandir($ruta), ['.', '..']);
                $avatars = array_filter($files, function($file) use ($ruta, $extensiones_validas) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    return in_array($extension, $extensiones_validas) && is_file($ruta . $file);
                });
                foreach ($avatars as $avatar):
                    $isSelected = ($user['image'] === $avatar);
                    $borderStyle = $isSelected ? 'selected' : '1px solid #ccc';
                    $checked = $isSelected ? 'checked' : '';
                ?>
                  <div class="col-md-2 text-center mb-3 avatar-option-container">
                    <label>
                      <input type="radio" name="avatar" value="<?= htmlspecialchars($avatar) ?>" <?= $checked ?> style="display: none;">
                      <img 
                        src="<?= assetPublicImages('/uploads/' . $avatar) ?>" 
                        alt="Avatar <?= htmlspecialchars(pathinfo($avatar, PATHINFO_FILENAME)) ?>" 
                        class="img-fluid rounded-circle avatar-option <?= $isSelected ? 'avatar-selected' : '' ?>" 
                      >
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$pageScripts = '
<script type="text/javascript">
	// Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '"; // definir como variable global
    window.BASE_URL = BASE_URL;
</script>
<script type="module" src="' . BASE_URL . '/public/assets/js/usuarios/mi_perfil.js"></script>
';
// Agregar scripts específicos para esta página
include RESOURCES_PATH . '/layouts/main.php';
?>