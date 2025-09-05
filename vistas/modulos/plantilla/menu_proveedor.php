<?php use App\Route; ?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-navy elevation-4">
    <!-- Brand Logo -->
    <a href="inicio" class="brand-link navbar-navy">
      <img src="<?php echo Route::rutaServidor(); ?>vistas/img/indhecaLogo.png" alt="IndhecaE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text text-white font-weight-light">CC 1.0</span>
    </a>

    <?php
    $foto = "vistas/img/usuarios/default/anonymous.png";
    ?>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo Route::rutaServidor().$foto; ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a class="d-block text-capitalize"><?=mb_strtolower(fString(usuarioAutenticadoProveedor()["usuario"]))?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <!-- <a href="inicio" class="nav-link active"> -->
            <a href="<?php echo Route::routes('inicio'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "inicio") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>Inicio</p>
            </a>
          </li>
          
          <!----------------------
          | MIS DATOS 
          ------------------------>
            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( ( Route::getRoute() == "datos-generales" || Route::getRoute() == "datos-legales"|| Route::getRoute() == "datos-financieros" || Route::getRoute() == "calidad-producto"   ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-user"></i>
                <p>
                  Mis Datos
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo Route::names('datos-generales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "datos-generales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Datos Generales</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo Route::names('datos-legales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "datos-legales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Datos Legales</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo Route::names('datos-financieros.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "datos-financieros") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Datos Financieros</p>
                  </a>
                </li>


                <li class="nav-item">
                  <a href="<?php echo Route::names('calidad-producto.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "calidad-producto") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Calidad del Producto</p>
                  </a>
                </li>

              </ul>
            </li>


          <li class="nav-item">
            <a href="<?php echo Route::names('ordenes-compra.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "ordenes-compra") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-wallet"></i>
              <p>Ordenes de Compra</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo Route::names('cotizaciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "cotizaciones") ? 'active' : '' ); ?>">
                <i class="nav-icon fas fa-file-alt"></i>
              <p>Cotizaciones</p>
            </a>
          </li>

          <!-- <li class="nav-item">
            <a href="<?php echo Route::names('estados-cuenta.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "estados-cuenta") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-money-check-alt"></i>
              <p>Estado de Cuenta</p>
            </a>
          </li> -->

          <!-- <li class="nav-item">
            <a href="<?php echo Route::names('datos-fiscales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "datos-fiscales") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>Datos Fiscales</p>
            </a>
          </li> -->

          <!-- <li class="nav-item">
            <a href="<?php echo Route::names('debida-diligencia.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "debida-diligencia") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-balance-scale"></i>
              <p>Debida Diligencia</p>
            </a>
          </li> -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>