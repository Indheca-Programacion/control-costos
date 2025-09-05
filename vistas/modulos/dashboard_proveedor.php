<?php
  use App\Controllers\Autorizacion;

    $nombre = $old["nombre"] ?? "";
    $apellidoPaterno = $old["apellidoPaterno"] ?? "";
    $apellidoMaterno = $old["apellidoMaterno"] ?? "";
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : "";
		$zona = isset($old["zona"]) ? $old["zona"] : "";
  $comprobantes = $comprobantes ?? [];
  	$zonas = [
		[
			"id" => 1,
			"nombre" => "Veracruz"
		],
		[
			"id" => 2,
			"nombre" => "Cotazacoalcos"
		],
		[
			"id" => 3,
			"nombre" => "Ciudad del Carmen"
		],
		[
			"id" => 4,
			"nombre" => "Villahermosa"
		],
		[
			"id" => 5,
			"nombre" => "Tampico"
		],
		[
			"id" => 6,
			"nombre" => "Monterrey"
		],
		[
			"id" => 7,
			"nombre" => "Ciudad de México"
		]
	];
?>

<div class="content-wrapper">

  <!-- Encabezado -->
  <section class="content-header bg-white py-3 mb-4 border-bottom shadow-sm">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h1 class="h3 mb-0">Tablero</h1>
          <small class="text-muted">Panel de Control</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item active">
              <i class="fas fa-tachometer-alt"></i> Inicio
            </li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Contenido principal -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Tarjeta de bienvenida -->
      <div class="card border-success shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex align-items-center">
          <i class="fas fa-user-check fa-lg mr-2"></i>
          <h2 class="h5 mb-0">Bienvenid@ <span class="font-weight-bold"><?= fString($proveedor->razonSocial) ?></span></h2>
        </div>
      </div>
      <!-- Sección de tarjetas o cajas -->
      <div class="row">
        <?php include "inicio/cajas-superiores-proveedor.php"; ?>
      </div>

      <div class="col-md-6">
        <table class="table table-bordered" id="tablaComprobantes">
          <thead>
            <tr>
              <th>Comprobante</th>
              <th>Fecha</th>
              <th>Vencimiento</th>
              <th>Importe</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($comprobantes as $comprobante): ?>
              <tr>
                <td><?= fString($comprobante["id"]) ?></td>
                <td><?= fFecha($comprobante["fecha"]) ?></td>
                <td><?= fFecha($comprobante["vencimiento"]) ?></td>
                <td><?= fString($comprobante["importe"]) ?></td>
                <td><?= fString($comprobante["estado"]) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </section>

</div>

<?php if ( $proveedor->infomacionCompleta == 0) : ?>
  <!-- Modal de bienvenida -->
  <div class="modal fade" id="bienvenidaModal"  role="dialog" aria-labelledby="bienvenidaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content border-success">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="bienvenidaModalLabel"><i class="fas fa-handshake"></i> Bienvenid@ al Portal de Proveedores</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="formSend">
          <div class="modal-body">
            <!-- Mensaje de bienvenida -->
            <div class="alert alert-success" role="alert">
              <i class="fas fa-smile-beam"></i> ¡Bienvenido/a al Portal de Proveedores! Por favor, completa la siguiente información.
            </div>

            <input type="hidden"id="idProveedor" name="idProveedor" value="<?= $proveedor->id?>">

            <!-- Formulario -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="nombre">Nombre de Contacto:</label>
                <input type="text" class="form-control form-control-sm" id="nombre" name="nombre"
                      value="<?= $nombre ?>" placeholder="Nombre del contacto" required>
              </div>
              <div class="form-group col-md-6">
                <label for="apellidoPaterno">Apellido Paterno:</label>
                <input type="text" class="form-control form-control-sm" id="apellidoPaterno"
                  name="apellidoPaterno" value="<?= $apellidoPaterno ?>" placeholder="Apellido paterno" required>
              </div>
              <div class="form-group col-md-6">
                <label for="apellidoMaterno">Apellido Materno:</label>
                <input type="text" class="form-control form-control-sm" id="apellidoMaterno"
                  name="apellidoMaterno" value="<?= $apellidoMaterno ?>" placeholder="Apellido materno" required>
              </div>
              <div class="form-group col-md-6">
                <label for="zona">Zona:*</label>
                <select id="zona" name="zona" class="form-control form-control-sm select2">
                  <option value="">Selecciona una zona</option>
                  <?php
                  foreach ($zonas as $detalle) {
                    echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $zona ? " selected" : "") . ">{$detalle["nombre"]}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="form-group col-md-12">
                <label for="domicilio">Domicilio:*</label>
                <input type="text" id="domicilio" name="domicilio" value="<?php echo fString($domicilio); ?>" class="form-control form-control-sm " placeholder="Ingresa el domicilio">
              </div>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-between align-items-center">

            <div id="mensajeCargando" class="text-success font-weight-bold d-none">
              <i class="fas fa-spinner fa-spin"></i> Enviando Datos...
            </div>

            <div id="botonesModal">
              <button type="button" class="btn btn-outline-success btnEnviar">
                <i class="fas fa-save"></i> Aceptar
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

<?php endif; ?>

<?php 
  array_push($arrayArchivosJS, 'vistas/js/dashboard-proveedor.js?v=1.01');
?>