<?php
/**
 * Dashboard Principal
 * resources/modules/dashboard/index.php
 */
$title = $title ?? "Dashboard";
ob_start(); 

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
</script>
<script type="module" src="' . BASE_URL . '/public/assets/js/dashboard/dashboard.js"></script>
';
include RESOURCES_PATH . '/layouts/main-tail.php';
?>
