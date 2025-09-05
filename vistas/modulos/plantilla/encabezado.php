<?php use App\Route; ?>

  <!-- Navbar -->
  <!-- <nav class="main-header navbar navbar-expand navbar-white navbar-light"> -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-navy">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <!-- <span class="badge badge-danger navbar-badge">3</span> -->
        </a>
        <?php if ( false ) : ?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
        <?php endif; ?>
      </li> 
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <?php if ( count($listaNotificaciones) > 0 ) : ?>
          <span class="badge badge-warning navbar-badge"><?php echo count($listaNotificaciones); ?></span>
          <?php endif; ?>
        </a>
        <?php if ( count($listaNotificaciones) > 0 ) : ?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <?php if ( count($listaNotificaciones) == 1 ) : ?>
          <span class="dropdown-item dropdown-header"><?php echo count($listaNotificaciones); ?> Notificación</span>
          <?php else: ?>
          <span class="dropdown-item dropdown-header"><?php echo count($listaNotificaciones); ?> Notificaciones</span>
          <?php endif; ?>
          <?php foreach($listaNotificaciones as $notificacion) { ?>
          <div class="dropdown-divider"></div>
          <a href="<?php echo $notificacion['ruta']; ?>" class="dropdown-item">
            <i class="fas <?php echo $notificacion['icono']; ?> mr-2"></i> <?php echo $notificacion['asunto']; ?>
            <span class="float-right text-muted text-sm"><?php echo $notificacion['tiempo']; ?></span>
          </a>
          <?php } ?>
          <!-- <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> -->
        </div>
        <?php endif; ?>
      </li>   
      <li class="nav-item">
        <!-- <a class="nav-link" data-widget="fullscreen" href="#" role="button"> -->
        <a class="nav-link" data-widget="fullscreen" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?=App\Route::routes('salir')?>" role="button">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>

    </ul>
  </nav>
  <!-- /.navbar -->