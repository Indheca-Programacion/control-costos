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
      <p class="login-box-msg">Ingresa tus credenciales de acceso</p>

      <form id="formSend" method="POST" action="<?php echo Route::routes('ingreso'); ?>">

        <input type="hidden" name="_token" value="<?php echo createToken(); ?>">

        <div class="input-group mb-3">
          <input type="text" class="form-control form-control-sm" placeholder="Usuario" name="usuario" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control form-control-sm" placeholder="Contraseña" name="contrasena" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>


        <div class="input-group mb-3">
          <a class="text-sm" href="<?=Route::routes('resset-password')?>"> Recuperar contraseña</a>
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
          <div class="col-4">
            <!-- <button type="button" id="btnSend" class="btn btn-primary btn-block btn-flat">Ingresar</button> -->
            <button type="button" id="btnSend" class="btn btn-primary btn-sm btn-flat">Ingresar</button>
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
  /* Desactivar autocompletado en formularios */
  $('form').attr('autocomplete','off');

  function enviar(){
    btnEnviar.disabled = true;
    mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";
    $("#btnSend").hide();

    formulario.submit();

    $("#btnSend").show();
    mensaje.innerHTML = "";
    btnEnviar.disabled = false;
  }
  formulario = document.getElementById("formSend");
  mensaje = document.getElementById("msgSend");
  btnEnviar = document.getElementById("btnSend");
  btnEnviar.addEventListener("click", enviar);
</script>

</body>
</html>