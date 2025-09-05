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
<div class="container login-container">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <!-- <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a> -->
      <span class="h2"><b>INFORMACIÓN COMPLEMENTARIA PARA ALTA DE PROVEEDORES</b></span>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Formulario de proveedor</p>

      <form id="formSend" method="POST" >

        <input type="hidden" name="_token" value="<?php echo createToken(); ?>">

        <div class="row">

            <div class="col-md-6 form-group">
                <label for="razonSocial">Razon Social:</label>
                <input type="text" class="form-control form-control-sm" id="razonSocial" name="razonSocial" placeholder="Razon Social" required>
            </div>

            <div class="col-md-6 form-group">
                <label for="rfc">RFC:</label>
                <input type="text" class="form-control form-control-sm" id="rfc" name="rfc" placeholder="RFC" required>
            </div>

            <div class="col-md-6 form-group">
                <label for="telefono">Telefono:</label>
                <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" placeholder="Telefono" required>
            </div>

            <div class="col-md-6 form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="Email" required>
            </div>

            <div class="col-12 form-group">
                <label for="contacto">Nombre Completo:</label>
                <input type="text" class="form-control form-control-sm" id="contacto" name="contacto" placeholder="Contacto" required>
            </div>

            <div class="col-md-6 form-group">
                <label for="origen">Origen del Vendedor:</label>
                <select class="form-control form-control-sm" id="origen" name="origen" required>
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="nacional">Nacional</option>
                    <option value="internacional">Internacional</option>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label>Tipo de Proveedor:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoProveedor" id="tipoMateriales" value="materiales" required>
                    <label class="form-check-label" for="tipoMateriales">Materiales</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoProveedor" id="tipoServicios" value="servicios" required>
                    <label class="form-check-label" for="tipoServicios">Servicios</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipoProveedor" id="tipoAmbos" value="ambos" required>
                    <label class="form-check-label" for="tipoAmbos">Ambos</label>
                </div>
            </div>

            <div class="col-md-6 form-group">
                <label>Clave del Proveedor:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="claveProveedor" id="claveMayorista" value="mayorista" required>
                    <label class="form-check-label" for="claveMayorista">Mayorista</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="claveProveedor" id="claveDistribuidor" value="distribuidor" required>
                    <label class="form-check-label" for="claveDistribuidor">Distribuidor</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="claveProveedor" id="claveFabricante" value="fabricante" required>
                    <label class="form-check-label" for="claveFabricante">Fabricante</label>
                </div>
            </div>

            <div class="col-md-6 form-group">
                <label>Lugar de Entrega/Prestación del Servicio o Material:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="lugarEntrega" id="lugarLAB" value="LAB" required>
                    <label class="form-check-label" for="lugarLAB">L.A.B</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="lugarEntrega" id="lugarPS" value="PS" required>
                    <label class="form-check-label" for="lugarPS">P.en S.</label>
                </div>
            </div>

            <div class="col-md-6 form-group">
                <label for="credito">Dias de Credito</label>
                <input type="number" class="form-control form-control-sm" id="credito" name="credito" placeholder="Dias de Credito" required>
            </div>

            <div class="col-12">
                <span><strong>Se solicita agregar la siguiente información:</strong></span>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        1. Constancia de identificación fiscal actualizada o Tax ID
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i>
                        </button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        2. Comprobante de domicilio no mayor a 3 meses
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i>
                        </button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        3. Carta con los datos bancarios, incluyendo clabe y moneda firmada y sellada por su institución bancaria o carátula de estado de cuenta bancario.
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i>
                        </button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        4. Copia de la identificación del representante legal
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="col-12 form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="aceptoDatos" name="aceptoDatos" required>
                    <label class="form-check-label" for="aceptoDatos">
                        Acepto y comprendo que todos los datos solicitados en este formulario son de carácter obligatorio y que por ser información sensible y confidencial se protegerá la privacidad de la información.
                    </label>
                </div>
            </div>

        </div>

        <div class="row">
          <div class="col-4">
            <!-- <button type="button" id="btnSend" class="btn btn-primary btn-block btn-flat">Ingresar</button> -->
            <button type="button" id="btnSend" class="btn btn-primary btn-sm btn-flat">Enviar</button>
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