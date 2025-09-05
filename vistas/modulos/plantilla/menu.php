  <?php use App\Route; ?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-navy elevation-4">
    <!-- Brand Logo -->
    <a href="inicio" class="brand-link navbar-navy">
      <img src="<?php echo Route::rutaServidor(); ?>vistas/img/indhecaLogo.png" alt="IndhecaE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text text-white font-weight-light">CC 1.0</span>
    </a>

    <?php
    $foto = is_null($usuarioAutenticado->foto) ? "vistas/img/usuarios/default/anonymous.png" : $usuarioAutenticado->foto;
    ?>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo Route::rutaServidor().$foto; ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="<?php echo Route::routes('perfil'); ?>" class="d-block text-capitalize"><?=mb_strtolower(fString($usuarioAutenticado->nombreCompleto))?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->               

        <?php if ( usuarioAutenticado() ): ?>

          <li class="nav-item">
            <!-- <a href="inicio" class="nav-link active"> -->
            <a href="<?php echo Route::routes('inicio'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "inicio") ? 'active' : '' ); ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>Inicio</p>
            </a>
          </li>

          <!----------------------
          | COSTOS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("costos-resumen") || $usuarioAutenticado->checkPermiso("costos-insumos") || $usuarioAutenticado->checkPermiso("costos-indirectos") || $usuarioAutenticado->checkPermiso("requisiciones") || $usuarioAutenticado->checkPermiso("gastos") || $usuarioAutenticado->checkPermiso("orden-compra") || $usuarioAutenticado->checkPermiso("nota-informativa") || $usuarioAutenticado->checkPermiso("programacion-pagos") || $usuarioAutenticado->checkPermiso("orden-compra-centro-servicios") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "costos-resumen" || Route::getRoute() == "costos-insumos" || Route::getRoute() == "costos-indirectos" || Route::getRoute() == "requisiciones" || Route::getRoute() == "gastos" || Route::getRoute() == "orden-compra" || Route::getRoute() == "nota-informativa" || Route::getRoute() == "programacion-pagos" || Route::getRoute() == "orden-compra-centro-servicios" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-hard-hat"></i>
                <p>
                  Costos
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("costos-resumen") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('costos-resumen.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "costos-resumen") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Resumen</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("requisiciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('requisiciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "requisiciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Requisiciones</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("orden-compra") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('orden-compra.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "orden-compra") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ordenes de Compra</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("gastos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('gastos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "gastos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Gastos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("orden-compra-centro-servicios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('orden-compra-centro-servicios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "orden-compra-centro-servicios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ordenes de Compra Centro de Servicios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("nota-informativa") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('nota-informativa.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "nota-informativa") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Nota Informativa</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("programacion-pagos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('programacion-pagos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "programacion-pagos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Programación de Pagos</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | ORDENES COMPRA GLOBALES
          ------------------------>
          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("requisicion-ocg-resumen")  || $usuarioAutenticado->checkPermiso("OrdenCompraGlobales")
          ): ?>
            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "requisiciones-orden-compra-globales" || Route::getRoute() == "orden-compra-globales") ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>
                  Ordenes Compra Globales
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("OrdenCompraGlobales") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('orden-compra-globales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "orden-compra-globales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Orden Compra</p>
                  </a>
                </li>
                <?php endif ?>

              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | HERRAMIENTAS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("calculadora") || $usuarioAutenticado->checkPermiso("plantillas")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "calculadora" || Route::getRoute() == "plantillas" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-wrench"></i>
                <p>
                  Herramientas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("calculadora") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('calculadora.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "calculadora") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Calculadora</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("plantillas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('plantillas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "plantillas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Plantillas de Proforma</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | CARGAS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("qr-cargas") || $usuarioAutenticado->checkPermiso("cargas") || $usuarioAutenticado->checkPermiso("materiales") || $usuarioAutenticado->checkPermiso("movimientos") || $usuarioAutenticado->checkPermiso("operadores")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "qr-cargas" || Route::getRoute() == "cargas" ||  Route::getRoute() == "materiales" ||  Route::getRoute() == "movimientos" ||  Route::getRoute() == "operadores" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon	fas fa-truck-loading "></i>
                <p>
                  Cargas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">


              <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("qr-cargas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('qr-cargas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "qr-cargas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>QR Cargas</p>
                  </a>
                </li>
                <?php endif ?>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cargas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('cargas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "cargas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cargas</p>
                  </a>
                </li>
                <?php endif ?>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("movimientos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('movimientos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "movimientos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Movimientos</p>
                  </a>
                </li>
                <?php endif ?>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("materiales") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('materiales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "materiales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Materiales</p>
                  </a>
                </li> 

                <?php endif ?>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("operadores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('operadores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "operadores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Operadores</p>
                  </a>
                </li>
                <?php endif ?>

              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | INVENTARIOS
          ------------------------>
          
          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("inventarios") || $usuarioAutenticado->checkPermiso("resguardos") || $usuarioAutenticado->checkPermiso("almacenes")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "inventarios" || Route::getRoute() == "resguardos" || Route::getRoute() == "almacenes" ) ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                  Inventarios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("inventarios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('inventarios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "inventarios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Inventarios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("resguardos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('resguardos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "resguardos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Resguardos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("almacenes") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('almacenes.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "almacenes") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Almacenes</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | RH
          ------------------------>
                    
          <?php if ($usuarioAutenticado->checkAdmin()|| $usuarioAutenticado->checkPermiso("nominas")|| $usuarioAutenticado->checkPermiso("requisicion-personal") || $usuarioAutenticado->checkPermiso("asistencias") || $usuarioAutenticado->checkPermiso("nom35") ) : ?>
            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "nominas" ) || (Route::getRoute() == "requisicion-personal" ) || (Route::getRoute() == "asistencias" ) || (Route::getRoute() == "nom35" )  ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-money-check"></i>
                <p>
                  Recursos Humanos 
                  <i class="right fas fa-angle-left"></i>
                </p>  
              </a>
                <ul class="nav nav-treeview">
                  <?php if ($usuarioAutenticado->checkAdmin()|| $usuarioAutenticado->checkPermiso("nominas") ): ?>
                    <li class="nav-item">
                      <a href="<?php echo Route::names('nominas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "nominas") ? 'active' : '' ); ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Crear Nominas</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if ($usuarioAutenticado->checkAdmin()|| $usuarioAutenticado->checkPermiso("requisicion-personal") ): ?>
                    <li class="nav-item">
                      <a href="<?php echo Route::names('requisicion-personal.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "requisicion-personal") ? 'active' : '' ); ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Requisiciones de personal</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if ($usuarioAutenticado->checkAdmin()|| $usuarioAutenticado->checkPermiso("asistencias") ): ?>
                    <li class="nav-item">
                      <a href="<?php echo Route::names('asistencias.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "asistencias") ? 'active' : '' ); ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Asistencias</p>
                      </a>
                    </li>
                  <?php endif ?>

                  <?php if ($usuarioAutenticado->checkAdmin()|| $usuarioAutenticado->checkPermiso("nom35") ): ?>
                    <li class="nav-item">
                      <a href="<?php echo Route::names('nom35.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "nom35") ? 'active' : '' ); ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>NOM35</p>
                      </a>
                    </li>
                  <?php endif ?>
                </ul>
            </li>
          <?php endif ?>

          <!----------------------
          | INFORMACIÓN TECNICA
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("info-tecnica-tags") || $usuarioAutenticado->checkPermiso("informacion-tecnica") || $usuarioAutenticado->checkPermiso("sgi") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica-tags" || Route::getRoute() == "informacion-tecnica" ||  Route::getRoute() == "sgi" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-info"></i>
                <p>
                  Información Técnica
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("info-tecnica-tags") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('informacion-tecnica-tags.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica-tags") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tags</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("tags-proveedores") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('tags-proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "tags-proveedores") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Tags Proveedores</p>
                    </a>
                  </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("informacion-tecnica") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('informacion-tecnica.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "informacion-tecnica") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Información Técnica</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("sgi") ): ?>
                  <li class="nav-item">
                    <a href="<?php echo Route::names('sgi.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "sgi") ? 'active' : '' ); ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Sistema de Gestion Integral</p>
                    </a>
                  </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | CATALÓGOS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estatus") || $usuarioAutenticado->checkPermiso("unidades") || $usuarioAutenticado->checkPermiso("insumo-tipos") || $usuarioAutenticado->checkPermiso("indirecto-tipos") || $usuarioAutenticado->checkPermiso("insumos-indirectos") || $usuarioAutenticado->checkPermiso("obras") || $usuarioAutenticado->checkPermiso("empleados") || $usuarioAutenticado->checkPermiso("gastos-tipos") || $usuarioAutenticado->checkPermiso("tareas")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "estatus" || Route::getRoute() == "unidades" || Route::getRoute() == "insumo-tipos" || Route::getRoute() == "indirecto-tipos" || Route::getRoute() == "insumos-indirectos" || Route::getRoute() == "obras" || Route::getRoute() == "empleados" || Route::getRoute() == "gastos-tipos" || Route::getRoute() == "actividad-semanal" || Route::getRoute() == "tareas")  ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-list-alt"></i>
                <p>
                  Catálogos
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estatus") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('estatus.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "estatus") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Estatus</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("unidades") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('unidades.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "unidades") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Unidades</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("insumo-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('insumo-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "insumo-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Directos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("indirecto-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('indirecto-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "indirecto-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipos de Indirectos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("insumos-indirectos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('insumos-indirectos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "insumos") || (Route::getRoute() == "indirectos") || (Route::getRoute() == "insumos-indirectos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Directos e Indirectos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("obras") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('obras.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "obras") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Obras</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empleados") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('empleados.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "empleados") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Empleados</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("gastos-tipos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('gastos-tipos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "gastos-tipos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tipo de Gastos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("tareas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('tareas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "tareas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Tareas</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | CONFIGURACION
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-requisiciones") || $usuarioAutenticado->checkPermiso("conf-correo") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-requisiciones" || Route::getRoute() == "configuracion-correo-electronico" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                  Configuración
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-requisiciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-requisiciones'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-requisiciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Requisiciones</p>
                  </a>
                </li>
                <?php endif ?>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-ordenes-compra") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-ordenes-compra'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-ordenes-compra") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ordenes Compra</p>
                  </a>
                </li>
                <?php endif ?>
                
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("conf-correo") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::routes('configuracion-correo-electronico'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "configuracion-correo-electronico") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Correo Electrónico</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | EMPRESAS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") || $usuarioAutenticado->checkPermiso("sucursales") ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "empresas" || Route::getRoute() == "sucursales" ) ? 'active"' : '' ); ?>">
                <i class="nav-icon fas fa-building"></i>
                <p>
                  Empresas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('empresas.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "empresas") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Empresas</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("sucursales") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('sucursales.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "sucursales") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Sucursales</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("ubicaciones") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('ubicaciones.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "ubicaciones") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ubicaciones</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | PROVEEDORES
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-proveedores") || $usuarioAutenticado->checkPermiso("cat-per-proveedor") || $usuarioAutenticado->checkPermiso("per-proveedor") || $usuarioAutenticado->checkPermiso("proveedores")  || $usuarioAutenticado->checkPermiso("soli-proveedor")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "categoria-proveedores" || Route::getRoute() == "categoria-permiso-proveedor" || Route::getRoute() == "permiso-proveedor"  || Route::getRoute() == "proveedores" || Route::getRoute() == "solicitud-proveedor"  ) ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-dolly"></i>
                <p>
                  Proveedores
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                <!----------------------
                | PROVEEDORES
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "proveedores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Proveedores</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | SOLICITUD PROVEEDOR
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("soli-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('solicitud-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "solicitud-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Solicitud Proveedor</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | CATEGORIAS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-proveedores") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('categoria-proveedores.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "categoria-proveedores") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Categorias</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | REQUERIMIENTOS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("per-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('permiso-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "permiso-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Requerimientos</p>
                  </a>
                </li>
                <?php endif ?>

                <!----------------------
                | CONFIRGURACIÓN REQUERIMIENTOS
                ------------------------>

                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cat-per-proveedor") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('categoria-permiso-proveedor.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "categoria-permiso-proveedor") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Configuración Requerimientos</p>
                  </a>
                </li>
                <?php endif ?>

              </ul>
            </li>

          <?php endif ?>

          <!----------------------
          | USUARIOS
          ------------------------>

          <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") || $usuarioAutenticado->checkPermiso("perfiles") || $usuarioAutenticado->checkPermiso("permisos")|| $usuarioAutenticado->checkPermiso("puestos")  ): ?>

            <li class="nav-item">
              <a href="#" class="nav-link <?php echo ( (Route::getRoute() == "usuarios" || Route::getRoute() == "puestos" || Route::getRoute() == "perfiles" || Route::getRoute() == "permisos")  ? ' active"' : '' ); ?>">
                <i class="nav-icon fas fa-user"></i>
                <p>
                  Usuarios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('usuarios.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "usuarios") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Usuarios</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("perfiles") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('perfiles.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "perfiles") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Perfiles</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("permisos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('permisos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "permisos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Permisos</p>
                  </a>
                </li>
                <?php endif ?>
                <?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("puestos") ): ?>
                <li class="nav-item">
                  <a href="<?php echo Route::names('puestos.index'); ?>" class="nav-link <?php echo ( (Route::getRoute() == "puestos") ? 'active' : '' ); ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Puestos</p>
                  </a>
                </li>
                <?php endif ?>
              </ul>
            </li>

          <?php endif ?>

        <?php endif ?> <!-- if ( usuarioAutenticado() ) -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>