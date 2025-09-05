<table class="table table-sm table-bordered table-hover mb-0" id="tablaCostosResumen" width="100%">
                 
	<thead class="thead-dark">
		<tr>
			<!-- <th style="width:10px">#</th> -->
			<th scope="col" style="min-width: 224px;">Descripción</th>
			<!-- <th scope="col" class="text-right" style="min-width: 112px;">Período 1</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 2</th>
			<th scope="col" class="text-right" style="min-width: 112px;">Período 3</th>
			<th scope="col" class="text-right" style="min-width: 128px;">Total</th> -->
		</tr>
	</thead>

	<tbody>
		<!-- <tr>
			<td class="font-weight-bold">Costo Directo</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
		</tr> -->

		<!-- <tr>
			<td class="font-weight-bold">Costo Indirecto de Obra</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
		</tr> -->

		<!-- <tr>
			<td class="font-weight-bold">Costo Total de Obra Directo + Indirecto</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
			<td class="text-right">$ 0.00</td>
		</tr> -->
	</tbody>

</table>
<div class="d-flex justify-content-between align-items-center mt-4 mb-2" <?php if (!$usuarioRoal || !$crearAnuncio) echo 'style="display:none;"'; ?>>
	<h5 class="mb-0">Anuncios</h5>
	<button type="button" class="btn btn-primary btn-sm" id="btnAgregarAnuncio" data-toggle="modal" data-target="#modalAnuncio">
		<i class="fas fa-plus"></i> Agregar anuncio
	</button>
</div>
<table class="table table-sm table-bordered table-hover" id="tablaAnuncios" width="100%">
	<thead class="thead-light">
		<tr>
			<th scope="col" style="min-width: 300px;">Mensaje</th>
			<th scope="col" style="min-width: 120px;">Fecha y Hora</th>
			<th scope="col" style="min-width: 160px;">Publicado por</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>

<div class="modal fade" id="modalAnuncio" tabindex="-1" role="dialog" aria-labelledby="modalAnuncioLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAnuncioLabel">Agregar Anuncio</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="formAgregarAnuncio">
					<div class="form-group">
						<label for="mensajeAnuncio">Mensaje</label>
						<textarea class="form-control" id="mensajeAnuncio" rows="3" required></textarea>
					</div>
					<div class="form-group">
						<label for="fechaHoraAnuncio">Fecha y hora</label>
						<input type="datetime-local" class="form-control form-control-sm" id="fechaHoraAnuncio" name="fechaHoraAnuncio" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
					</div>
					<button type="button" id="btnCrearAnuncio" class="btn btn-primary">Publicar Anuncio</button>
				</form>
			</div>
		</div>
	</div>
</div>