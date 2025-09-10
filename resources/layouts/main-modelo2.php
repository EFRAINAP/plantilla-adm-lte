<?php
// resources/layouts/main.php
$title = $title ?? 'Sistema Administrativo';
// obtener datos del usuario actual
$user = current_user();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?> | TAMA</title>
  <link rel="icon" href="<?= assetPublicImages('favicon.ico') ?>" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= fontawesome('css/all.min.css') ?>">
  <!-- Bootstrap Icons (AdminLTE 4 requirement) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= adminlte('dist/css/adminlte.css') ?>">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= asset('css/custom.css') ?>">
  <link rel='stylesheet' href='<?= asset('css/dashboard.css') ?>'>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="<?= vendor('DataTables-2.1.8/datatables.min.css') ?>">
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="<?= asset('css/select2.min.css') ?>" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Estilos específicos de página -->
  <?= $pageStyles ?? '' ?>
</head>
<body class="layout-fixed sidebar-mini bg-body-tertiary">
<!--begin::App Wrapper-->
<div class="app-wrapper">

  <!--begin::Sidebar-->
  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
      <!--begin::Brand Link-->
      <a href="<?= url('dashboard') ?>" class="brand-link">
        <!--begin::Brand Image-->
        <img src="<?= assetPublicImages('logito.png') ?>" alt="TAMA Logo" class="brand-image opacity-75 shadow">

        <!--end::Brand Image-->
        <!--begin::Brand Text-->
        <span class="brand-text fw-light">TAMA</span>
        <!--end::Brand Text-->
      </a>
      <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <?php include __DIR__ . '/../partials/sidebar-menu.php'; ?>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!--end::Sidebar Wrapper-->
  </aside>
  <!--end::Sidebar-->

  <!--begin::App Main-->
  <main class="app-main">

    <!--begin::App Content-->
    <div class="app-content">
      <div class="container-fluid">
        <?= $content ?? '' ?>
      </div>
    </div>
    <!--end::App Content-->
  </main>
  <!--end::Footer-->
</div>
<!--end::App Wrapper-->

<!-- jQuery -->
<script src="<?= jquery('jquery.min.js') ?>"></script>
<!-- Moment.js -->
<script src="<?= vendor('moment/moment.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?= vendor('DataTables-2.1.8/datatables.min.js') ?>"></script>
<!-- Bootstrap 5 -->
<script src="<?= bootstrap('js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= adminlte('dist/js/adminlte.js') ?>"></script>
<!-- Select2 -->
<script src="<?= asset('js/select2.min.js') ?>"></script>

<!-- Scripts específicos de página -->
<?= $pageScripts ?? '' ?>

<script>
$(document).ready(function() {
    // Restaurar estado del sidebar
    const sidebarState = localStorage.getItem('sidebar-state');
    if (sidebarState === 'collapsed') {
        $('body').addClass('sidebar-collapse');
    } else if (sidebarState === 'hidden') {
        $('.app-sidebar').hide();
        $('body').addClass('sidebar-hidden');
    }
    
    // Botón de contraer (compatible con AdminLTE 4)
    $(document).on('click', '[data-lte-toggle="sidebar"]', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapse');
        
        setTimeout(function() {
            if ($('body').hasClass('sidebar-collapse')) {
                localStorage.setItem('sidebar-state', 'collapsed');
            } else {
                localStorage.setItem('sidebar-state', 'expanded');
            }
        }, 100);
    });
    
    // Botón de ocultar completamente
    $('#sidebar-hide-btn').click(function(e) {
        e.preventDefault();
        
        if ($('.app-sidebar').is(':visible')) {
            $('.app-sidebar').hide();
            $('body').addClass('sidebar-hidden').removeClass('sidebar-collapse');
            localStorage.setItem('sidebar-state', 'hidden');
        } else {
            $('.app-sidebar').show();
            $('body').removeClass('sidebar-hidden');
            localStorage.setItem('sidebar-state', 'expanded');
        }
    });
});
</script>
</body>
</html>
