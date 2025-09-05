<?php
		$archivo = '';
		if ( isset($requisicion->id) ) {
			$empresaId = $requisicion->empresaId;
			$folio = $requisicion->folio;
			$actualEstatusId = $requisicion->estatus;
			$EstatusId = isset($old["EstatusId"]) ? $old["EstatusId"] : $requisicion->estatus;
			$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
			$obraNombre = $obras->descripcion;
			$justificacion = isset($old["justificacion"]) ? $old["justificacion"] : $requisicion->justificacion;
			$fechaRequerida = isset($old["fechaRequerida"]) ? $old["fechaRequerida"] : fFechaLarga($requisicion->fechaReq ?? date('Y-m-d'));
			$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : $requisicion->especificaciones;
			$direccion = isset($old["direccion"]) ? $old["direccion"] : $requisicion->direccion;
			$cantidad = '0.00';
			$unidad = '';
			$numeroParte = '';
			$concepto = '';
			$iva = isset($old["iva"]) ? $old["iva"] : $requisicion->iva;
			$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : $requisicion->retencionIva;
			$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : $requisicion->retencionIsr;
			$descuento = isset($old["descuento"]) ? $old["descuento"] : $requisicion->descuento;
			// ID DEL PROVEEDOR
			$proveedorSeleccionado = $requisicion->proveedorId;
			$proveedorName = $requisicion->nombreProveedor;
			$telefono = $requisicion->telefonoProveedor;
			$categoriaId = isset($old["categoriaId"]) ? $old["categoriaId"] : $requisicion->categoriaId;
			$divisaId = isset($old["divisa"]) ? $old["divisa"] : $requisicion->divisa;
			$tipoRequisicion = isset($old["tipoRequisicion"]) ? $old["tipoRequisicion"] : $requisicion->tipoRequisicion;
			$obraId = isset($old["obra"]) ? $old["obra"] : "";
			$presupuestoId = isset($old["presupuesto"]) ? $old["presupuesto"] : $requisicion->presupuesto;
		}
?>

<div class="card card-primary card-outline card-outline-tabs">
	<div class="card-header p-0 border-bottom-0">
		<ul class="nav nav-tabs" id="tabServicio" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="true">Requisicion</a>
			</li>
			<li class="nav-item">
				<?php if ( isset($requisicion->id) ) : ?>
				<a class="nav-link <?php if(is_null($requisicion->usuarioIdAutorizacion)) echo 'disabled' ?>" id="ordenes-compra-tab" data-toggle="pill" href="#ordenes-compra" role="tab" aria-controls="ordenes-compra" aria-selected="false">Ordenes de compra</a>
				<?php else: ?>
				<a class="nav-link disabled" id="ordenes-compra-tab" data-toggle="pill" role="tab" aria-controls="ordenes-compra" aria-selected="false">Ordenes de compra</a>
				<?php endif; ?>
			</li>
			<li class="nav-item">
				<?php if ( isset($requisicion->id) ) : ?>
				<a class="nav-link" id="cotizaciones-tab" data-toggle="pill" href="#cotizaciones" role="tab" aria-controls="cotizaciones" aria-selected="false">Cotizaciones</a>
				<?php else: ?>
				<a class="nav-link disabled" id="cotizaciones-tab" data-toggle="pill" role="tab" aria-controls="cotizaciones" aria-selected="false">Cotizaciones</a>
				<?php endif; ?>
			</li>
		</ul>
	</div>
	<div class="card-body px-2">
		<div class="tab-content" id="tabServicioContent">
			<div class="tab-pane fade show active" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">

				<?php
				include "vistas/modulos/requisiciones/form-section-requisiciones.php";
				?>
				</form>
			</div>
			<div class="tab-pane fade" id="ordenes-compra" role="tabpanel" aria-labelledby="ordenes-compra-tab">
				<?php
				if ( isset($requisicion->id) ) include "vistas/modulos/requisiciones/form-section-ordenes-compra.php";
				?>
			</div>
			<div class="tab-pane fade" id="cotizaciones" role="tabpanel" aria-labelledby="cotizaciones-tab">
				<?php
				if ( isset($requisicion->id) ) include "vistas/modulos/requisiciones/form-section-cotizaciones.php";
				?>
			</div>
		</div>
	</div> <!-- /.card -->
</div>