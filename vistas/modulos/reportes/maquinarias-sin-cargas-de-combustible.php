<?php
// var_dump($maquinariasSinCargas);
?>
<div class="card">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-flag mr-1 text-warning"></i>
			<?=fString($diasSinCargaTitulo)?>
		</h3>
		<div class="card-tools">
			<button type="button" class="btn btn-tool text-dark" data-card-widget="collapse">
				<i class="fas fa-minus"></i>
			</button>
			<button type="button" class="btn btn-tool text-dark" data-card-widget="remove">
				<i class="fas fa-times"></i>
			</button>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-striped m-0" width="100%">
				<thead>
					<tr>
						<th>Empresa</th>
						<th>Ubicación</th>
						<th>Equipo</th>
					</tr>
				</thead>
				<tbody class="text-nowrap text-uppercase">
					<?php foreach($maquinariasSinCargas as $key => $detalle) : ?>
						<tr>
							<td><?=fString($detalle['empresas.nombreCorto'])?></td>
							<td><?=fString($detalle['ubicaciones.descripcion'])?></td>
							<td><?=fString($detalle['numeroEconomico'])?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
            </table>
		</div>
	</div> <!-- /.card-body -->
</div> <!-- /.card -->
