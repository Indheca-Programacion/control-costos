<?php
  use App\Route;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo CONST_APP_NAME . ' | ' . ucfirst(Route::getRoute()); ?></title>
  <link rel="icon" href="vistas/img/favicon.ico" type="image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/css/adminlte.min.css">
  <!-- Custom -->
  <link rel="stylesheet" href="<?php echo Route::rutaServidor(); ?>vistas/css/login.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <!-- <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a> -->
      <span class="h2"><b>Control</b> Costos</span>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Ingresa tu correo electronico para restablecer la contraseña</p>

      <form id="formSend" method="POST" action="<?php echo Route::routes('resset-password'); ?>">

        <input type="hidden" name="_token" value="<?php echo createTemporaryToken(); ?>">

        <div class="input-group mb-3">
          <input type="email" class="form-control form-control-sm" placeholder="Correo Electronico" name="correo" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div id="sendingMessage" style="display: none;" class="text-muted mt-2">
          <i class="fas fa-spinner fa-spin"></i> Enviando...
        </div>

        <?php if ( !is_null(flash()) ) : ?>
          <div class="alert <?=flash()->clase?>" role="alert"><?=flash()->mensaje?></div>
        <?php endif; ?>

        <?php
          unset($_SESSION[CONST_SESSION_APP]["flash"]);
          unset($_SESSION[CONST_SESSION_APP]["flashAlertClass"]);

          include "vistas/modulos/errores/form-messages.php";

          unset($_SESSION[CONST_SESSION_APP]["errors"]);
        ?>

        <div class="row">
          <div class="col-12 d-flex justify-content-between align-items-end">
            <!-- <button type="button" id="btnSend" class="btn btn-primary btn-block btn-flat">Ingresar</button> -->
            <button type="button" id="btnSend" class="btn btn-primary btn-sm btn-flat">Recuperar contraseña </button>
              <a class="text-sm" href="<?=Route::routes('ingreso')?>">Iniciar Sesión</a>
          </div>
        </div>

        <div id="msgSend"></div>

      </form>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo Route::rutaServidor(); ?>vistas/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo Route::rutaServidor(); ?>vistas/adminlte/dist/js/adminlte.min.js"></script>

<script>
  // Desactivar autocompletado
  $('form').attr('autocomplete', 'off');

  formulario = document.getElementById("formSend");
  mensaje = document.getElementById("msgSend");
  btnEnviar = document.getElementById("btnSend");
  sendingMessage = document.getElementById("sendingMessage");

  // Manejo con botón
  function enviar() {
    btnEnviar.disabled = true;
    $("#btnSend").hide();
    sendingMessage.style.display = "block";
    formulario.submit();
  }

  // Manejo cuando se envía el formulario (con Enter o botón)
  formulario.addEventListener("submit", function (e) {
    btnEnviar.disabled = true;
    $("#btnSend").hide();
    sendingMessage.style.display = "block";
  });

  // Click del botón
  btnEnviar.addEventListener("click", enviar);
</script>


</body>
</html>