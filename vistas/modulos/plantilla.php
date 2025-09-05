<?php

use App\Route;

if ( !isset($_SESSION[CONST_SESSION_APP]["ingreso"]) || $_SESSION[CONST_SESSION_APP]["ingreso"]["validarSesion"] != "ok" ) {

  echo '<script>

    window.location = "'.Route::rutaServidor().'ingreso";

  </script>';

  die();

}

require_once "app/Models/Usuario.php";
$usuarioAutenticado = New App\Models\Usuario;
if ( usuarioAutenticado() ) {

    $usuarioAutenticado->consultar("usuario", usuarioAutenticado()["usuario"]);
    $usuarioAutenticado->consultarPerfiles();
    $usuarioAutenticado->consultarPermisos();

    $listaNotificaciones = array();
    require_once "app/Controllers/Autorizacion.php";
    if ( \App\Controllers\Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || \App\Controllers\Autorizacion::permiso($usuarioAutenticado, "servicios-finalizar", "ver") ) {

      // $listaNotificaciones = $usuarioAutenticado->notificaciones();
      // var_dump($listaNotificaciones);

    }

}

$arrayArchivosJS = array(); // Se deben agregar al arreglo los archivos JS que se necesiten en cada módulo (ruta) y se insertarán después de plantilla.js

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo CONST_APP_NAME . ' | ' . ucfirst(Route::getRoute()); ?></title>
  <base href="<?=Route::rutaServidor()?>">
  <link rel="icon" href="vistas/img/favicon.ico" type="image/x-icon"> 
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/fontawesome-free/css/all.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/daterangepicker/daterangepicker.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?=Route::rutaServidor()?>vistas/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-rowgroup/css/rowGroup.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-select/css/select.bootstrap4.min.css">
  <link href="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.2.1/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/date-1.5.5/fc-5.0.4/kt-2.12.1/r-3.0.3/rg-1.5.1/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css" rel="stylesheet">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Bootstrap Switch -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/css/adminlte.min.css">
  <!-- Dropzone -->
  <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
  <!-- Custom -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/css/estilos.css?v=1.03">
  <!-- Tooltipster -->
  <link rel="stylesheet" type="text/css" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/tooltipster/dist/css/tooltipster.bundle.min.css" />
</head>
<!-- <body class="hold-transition sidebar-mini"> -->
<body class="sidebar-mini sidebar-collapse layout-fixed text-sm" style="height: auto;">

  <!-- Site wrapper -->
  <div class="wrapper">

    <?php include "plantilla/encabezado.php"; ?>

    <?php include "plantilla/menu.php"; ?>

    <!-- Content Wrapper. Contains page content -->
    <?php 
      if ( isset($contenido['modulo']) ) include $contenido['modulo']; 
      else include "errores/404.php"
    ?>
    <!-- /.content-wrapper -->
    
    <?php include "plantilla/pie-de-pagina.php"; ?>

  </div>
  <!-- ./wrapper -->

  <?php
  // Eliminar las variables de sesión que hacen referencia a errores, mensajes y alertas en los formularios
  unset($_SESSION[CONST_SESSION_APP]["flash"]);
  // unset($_SESSION[CONST_SESSION_APP]["flashAlertClass"]);
  unset($_SESSION[CONST_SESSION_APP]["old"]);
  unset($_SESSION[CONST_SESSION_APP]["errors"]);
  ?>

  <!-- jQuery -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Boostrap Switch -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/sweetalert2/sweetalert2.min.js"></script>
  <!-- Moment (requerido por Tempusdominus) -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/moment/moment.min.js"></script>
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/moment/locale/es-mx.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- date-range-picker -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Inputmask -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/inputmask/jquery.inputmask.min.js"></script>
  <!-- Select2 -->
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/select2/js/select2.full.min.js"></script>
  <script src="<?=Route::rutaServidor(); ?>vistas/plugins/select2/js/i18n/es.js"></script>
  <!-- Datatables2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.2.1/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/date-1.5.5/fc-5.0.4/kt-2.12.1/r-3.0.3/rg-1.5.1/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

  <!-- DataTables  & Plugins -->
  <!-- <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/jszip/jszip.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/pdfmake/pdfmake.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/pdfmake/vfs_fonts.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-rowgroup/js/dataTables.rowGroup.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-rowgroup/js/rowGroup.bootstrap4.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-select/js/dataTables.select.min.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-select/js/dataTables.select.js"></script>
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/datatables-select/js/select.bootstrap4.min.js"></script>-->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/tooltipster/dist/js/tooltipster.bundle.min.js"></script> 
  <!-- signature -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
  <!-- Dropzone -->
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
  <!-- bootstrap color picker -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/js/adminlte.min.js"></script>
  <!-- Custom -->
  <script src="<?php echo Route::rutaServidor(); ?>vistas/js/plantilla.js?v=1.02"></script>
  <?php foreach($arrayArchivosJS as $archivoJS): ?>
    <script src="<?=Route::rutaServidor().$archivoJS?>"></script>
    <?php endforeach; ?>
  <?php if ( isset($comandoJS) ) : ?>
  <script><?php echo $comandoJS ?></script>
  <?php endif; ?>

</body>
</html>
