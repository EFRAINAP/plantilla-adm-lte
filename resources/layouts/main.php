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
  <link rel="stylesheet" href="<?= adminlte('css/adminlte.css') ?>">
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
  
  <!--begin::Header-->
  <nav class="app-header navbar navbar-expand bg-body">
    <!--begin::Container-->
    <div class="container-fluid">
      <!--begin::Start Navbar Links-->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="sidebar-hide-btn" href="#" role="button"><i class="bi bi-layout-sidebar-inset"></i></a>
        </li>
        <li class="nav-item d-none d-md-block">
          <a href="<?= url('dashboard') ?>" class="nav-link">Inicio</a>
        </li>
        <li class="nav-item d-none d-md-block">
          <a href="<?= url('usuarios') ?>" class="nav-link">Usuarios</a>
        </li>
      </ul>
      <!--end::Start Navbar Links-->

      <!--begin::End Navbar Links-->
      <ul class="navbar-nav ms-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="bi bi-search"></i>
          </a>
        </li>

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-chat-text"></i>
            <span class="navbar-badge badge text-bg-danger">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <a href="#" class="dropdown-item">
              <!--begin::Message-->
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <img src="<?= adminlte('img/user1-128x128.jpg') ?>" alt="User Avatar" class="img-size-50 rounded-circle me-3">
                </div>
                <div class="flex-grow-1">
                  <h3 class="dropdown-item-title">
                    Brad Diesel
                    <span class="float-end fs-7 text-danger"><i class="bi bi-star-fill"></i></span>
                  </h3>
                  <p class="fs-7">Call me whenever you can...</p>
                  <p class="fs-7 text-secondary"><i class="bi bi-clock-fill me-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!--end::Message-->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
          </div>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-bell-fill"></i>
            <span class="navbar-badge badge text-bg-warning">15</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <span class="dropdown-item dropdown-header">15 Notifications</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="bi bi-envelope me-2"></i> 4 new messages
              <span class="float-end text-secondary fs-7">3 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="bi bi-people-fill me-2"></i> 8 friend requests
              <span class="float-end text-secondary fs-7">12 hours</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
              <span class="float-end text-secondary fs-7">2 days</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
          </div>
        </li>

        <!-- Fullscreen Toggle -->
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="fullscreen" href="#" role="button">
            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
          </a>
        </li>

        <!--begin::User Menu Dropdown-->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <img
              src="<?= adminlte('img/user2-160x160.jpg') ?>"
              class="user-image rounded-circle shadow"
              alt="User Image"
            />
            <span class="d-none d-md-inline"><?= $user['name'] ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <!--begin::User Image-->
            <li class="user-header text-bg-primary">
              <img
                src="<?= adminlte('img/user2-160x160.jpg') ?>"
                class="rounded-circle shadow"
                alt="User Image"
              />
              <p>
                <?= $user['name'] . ' - ' . $user['area'] ?>
                <small><?= $user['last_login'] ?></small>
              </p>
            </li>
            <!--end::User Image-->
            <!--begin::Menu Body-->
            <li class="user-body">
              <!--begin::Row
              <div class="row">
                <div class="col-4 text-center"><a href="#">Followers</a></div>
                <div class="col-4 text-center"><a href="#">Sales</a></div>
                <div class="col-4 text-center"><a href="#">Friends</a></div>
              </div>
              end::Row-->
            </li>
            <!--end::Menu Body-->
            <!--begin::Menu Footer-->
            <li class="user-footer">
              <a href="#" class="btn btn-default btn-flat">Profile</a>
              <a href="<?= url('app/auth/logout.php') ?>" class="btn btn-default btn-flat float-end">Sign out</a>
            </li>
            <!--end::Menu Footer-->
          </ul>
        </li>
        <!--end::User Menu Dropdown-->
      </ul>
      <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
  </nav>
  <!--end::Header-->

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
    <!--begin::App Content Header-->
    <div class="app-content-header">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0"><?= htmlspecialchars($title) ?></h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Home</a></li>
              <li class="breadcrumb-item active"><?= htmlspecialchars($title) ?></li>
            </ol>
          </div>
        </div>
        <!--end::Row-->
      </div>
      <!--end::Container-->
    </div>
    <!--end::App Content Header-->

    <!--begin::App Content-->
    <div class="app-content">
      <div class="container-fluid">
        <?= $content ?? '' ?>
      </div>
    </div>
    <!--end::App Content-->
  </main>
  <!--end::App Main-->
  
  <!--begin::Footer-->
  <footer class="app-footer">
    <!--begin::To the end-->
    <div class="float-end d-none d-sm-inline">
      <b>Version</b> Adm LTE 4 Tama Version 2.0
    </div>
    <!--end::To the end-->
    <!--begin::Copyright-->
    <strong>Copyright &copy; 2025 <a href="https://tamaingenieros.pe" target="_blank">TAMA Ingenieros</a>.</strong>
    All rights reserved.
    <!--end::Copyright-->
  </footer>
  <!--end::Footer-->
</div>
<!--end::App Wrapper-->

<!-- jQuery -->
<script src="<?= jquery('jquery.min.js') ?>"></script>
<!-- Moment.js -->
<script src="<?= asset('moment/moment.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?= asset('DataTables-2.1.8/datatables.min.js') ?>"></script>
<!-- Bootstrap 5 -->
<script src="<?= bootstrap('js/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= adminlte('js/adminlte.js') ?>"></script>
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
