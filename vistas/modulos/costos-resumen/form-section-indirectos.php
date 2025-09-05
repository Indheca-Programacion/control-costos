<?php 
require_once("app/Controllers/Autorizacion.php");
if(App\Controllers\Autorizacion::permiso($usuarioAutenticado, "proformas", "actualizar")){ ?>
<button type="button" id="btnAgregarIndirecto" class="btn btn-outline-primary float-right m-2" data-toggle="modal" data-target="#modalAgregarIndirecto" disabled>
	<i class="fas fa-plus"></i> Añadir indirecto
</button>
<button type="button" id="btnCrearIndirecto" class="btn btn-outline-primary float-right m-2" data-toggle="modal" data-target="#modalCrearIndirecto" disabled>
	<i class="fas fa-edit"></i> Crear indirecto
</button>
<?php }?>
<div class="clearfix"></div>

<table class="table table-sm table-bordered table-hover mb-0x" id="tablaIndirectos" width="100%">

	<thead class="thead-dark">
		<tr>
			<!-- <th style="width:10px">#</th> -->
			<th scope="col">Tipo</th>
			<th scope="col" style="min-width: 64px;">Número</th>
			<th scope="col" style="min-width: 192px;">Descripción</th>
			<th scope="col" style="min-width: 80px;">Unidad</th>
			<!-- <th scope="col" class="text-right" style="min-width: 112px;">Período 1</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 2</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 3</th> -->
			<!-- <th>Acciones</th> -->
		</tr> 
	</thead>

	<tbody>
	</tbody>

</table>

<div class="section-requisicion-indirectos d-none">

	<hr>

	<label class="text-primary">Nueva Requisición</label>

	<form id="formCrearRequiIndirectoSend">
		<div class="table-responsive">
			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequiIndirectoDetalles" width="100%">
				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th style="min-width: 160px;">Tipo Indirecto</th>
						<th style="min-width: 96px;">Número</th>
						<th style="min-width: 192px;">Descripción</th>
						<th style="min-width: 64px;">Cant.</th>
						<th style="min-width: 64px;">Costo</th>
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

	<button type="button" id="btnCrearRequiIndirecto" class="btn btn-outline-primary mt-2 float-right" data-toggle="modal" data-target="#modalCrearRequiInsumoIndirecto" disabled>
		<i class="fas fa-plus"></i> Crear requisición
	</button>

</div>
