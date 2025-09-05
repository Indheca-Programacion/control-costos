<div class="card">
    <div class="card-header">
        <h3 class="card-title">
			<i class="fas fa-clipboard mr-1 text-red"></i>
			Tareas Pendientes
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
						<th>Tarea</th>
						<th>Fecha Limite</th>
					</tr>
				</thead>
				<tbody class="text-nowrap text-uppercase">
					<?php foreach($arrayTareas as $key => $tarea) : ?>
						<tr onclick="window.location='<?php echo $tarea['ruta'] ?>'" style="cursor:pointer;">
							<td><?=mb_strtoupper(fString($tarea['descripcion']))?></td>
							<td><?=fFechaLarga($tarea['fecha_limite'])?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
            </table>
		</div>
	</div> <!-- /.card-body -->
</div>