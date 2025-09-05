<?php 
require_once("app/Controllers/Autorizacion.php");
if(App\Controllers\Autorizacion::permiso($usuarioAutenticado, "proformas", "actualizar")){ ?>
<button type="button" id="btnAgregarInsumo" class="btn btn-outline-primary float-right m-2" data-toggle="modal" data-target="#modalAgregarInsumo" disabled>
		<i class="fas fa-plus"></i> Añadir Directo
</button>
<button type="button" id="btnCrearInsumo" class="btn btn-outline-primary float-right m-2" data-toggle="modal" data-target="#modalCrearInsumo" disabled>
		<i class="fas fa-edit"></i> Crear Directo
</button>	
<?php }?>

<div class="clearfix"></div>

<table class="table table-sm table-bordered table-hover mb-0" id="tablaInsumos" width="100%">
                 
	<thead class="thead-dark">
		<tr>
			<!-- <th style="width:10px">#</th> -->
			<th scope="col">Tipo</th>
			<th scope="col" style="min-width: 96px;">Código</th>
			<th scope="col" style="min-width: 192px;">Descripción</th>
			<th scope="col" style="min-width: 112px;">Unidad</th>
			<th scope="col" style="min-width: 112px;">Cantidad</th>
			<th scope="col" style="min-width: 112px;">Presupuesto</th>
			<th scope="col" style="min-width: 112px;">Remanente</th>
			<!-- <th scope="col" class="text-right" style="min-width: 112px;">Período 1</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 2</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 3</th> -->
			<!-- <th>Acciones</th> -->
		</tr>
	</thead>

	<tbody>
	</tbody>

</table>

<div class="section-requisicion-insumos d-none">

	<hr>

	<label class="text-primary">Nueva Requisición</label>

	<form id="formCrearRequiInsumoSend">
		<div class="table-responsive">
			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequiInsumoDetalles" width="100%">
				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th style="min-width: 160px;">Tipo Directo</th>
						<th style="min-width: 112px;">Código</th>
						<th style="min-width: 192px;">Descripción</th>
						<th style="min-width: 64px;">Cant.</th>
						<th style="min-width: 112px;">Costo</th>
						<th style="min-width: 112px;">Unidad</th>
						<th style="min-width: 320px;">Concepto</th>
						<th style="display: none;">elementId</th>
					</tr>
				</thead>
				<tbody class="text-uppercase">
				</tbody>
			</table>
		</div>
	</form>

	<button type="button" id="btnCrearRequiInsumo" class="btn btn-outline-primary mt-2 float-right" data-toggle="modal" data-target="#modalCrearRequiInsumoIndirecto" disabled>
		<i class="fas fa-plus"></i> Crear requisición
	</button>

</div>

<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Detalle del Registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="lista-resumen-costos"></ul>
      </div>
    </div>
  </div>
</div>
