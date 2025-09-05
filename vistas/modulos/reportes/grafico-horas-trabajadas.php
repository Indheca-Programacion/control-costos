<?php
$columnHoras = array_column($horasTrabajadasCentro, 'actividad_detalles.horas');
$totalHoras = array_sum($columnHoras);

$cantidadCentros = count($horasTrabajadasCentro);
$columna = 'col-'.floor(12 / $cantidadCentros);
?>
<!-- solid sales graph -->
<div class="card bg-gradient-info" id="grafico-horas-trabajadas">
	<div class="card-header border-0">
		<h3 class="card-title">
		<i class="fas fa-th mr-1"></i>
		Horas Trabajadas
		</h3>
		<div class="card-tools">
			<button type="button" class="btn btn-tool" data-card-widget="collapse">
				<i class="fas fa-minus"></i>
			</button>
			<button type="button" class="btn btn-tool" data-card-widget="remove">
				<i class="fas fa-times"></i>
			</button>
		</div>
	</div>
	<div class="card-body d-none">
		<canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
	</div> <!-- /.card-body -->
	<div class="card-footer bg-transparent d-none">
		<div class="row">
			<?php foreach($horasTrabajadasCentro as $key=>$detalle) : ?>
				<?php $pctHoras = number_format( ( $detalle['actividad_detalles.horas'] / $totalHoras ) * 100, 0); ?>
				<div class="<?=$columna?> text-center">
				<input type="text" class="knob" data-readonly="true" value="<?=$pctHoras?>" data-width="60" data-height="60" data-fgColor="#39CCCC">
				<div class="text-white text-capitalize"><?=fString($detalle['servicio_centros.nombreCorto'])?></div>
			</div> <!-- ./col -->
			<?php endforeach; ?>
			<!-- <div class="col-4 text-center">
				<input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgColor="#39CCCC">
				<div class="text-white">Mail-Orders</div>
			</div> --> <!-- ./col -->
			<!-- <div class="col-4 text-center">
				<input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC">
				<div class="text-white">Online</div>
			</div> --> <!-- ./col -->
			<!-- <div class="col-4 text-center">
				<input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#39CCCC">
				<div class="text-white">In-Store</div>
			</div> --> <!-- ./col -->
		</div> <!-- /.row -->
	</div> <!-- /.card-footer -->
</div> <!-- /.card -->
<script>
	window.horasTrabajadas = <?=json_encode($horasTrabajadas)?>;
</script>
<?php
	// <!-- ChartJS -->
	array_push($arrayArchivosJS, 'vistas/plugins/chart.js/Chart.min.js');
	// <!-- jQuery Knob Chart -->
	array_push($arrayArchivosJS, 'vistas/plugins/jquery-knob/jquery.knob.min.js');
	array_push($arrayArchivosJS, 'vistas/js/dashboard.js?v=1.00');
?>
