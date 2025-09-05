<?php


$grupos = [
    1 => "Marco Legal",
    2 => "Marco Financiero",
    3 => "Calidad de Producto"
    // Puedes agregar más grupos aquí
];



?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
              <h3 class="card-title">Categorias con Permiso <span class="font-weight-bold">Modificar Categorias</span></h3>
            </div>

			<div class="card-body">

			<div class="accordion" id="accordionCategorias">
				<?php foreach ($categorias as $catKey => $categoria) { ?>
					<div class="card">
						<div class="card-header" id="heading-cat-<?php echo $catKey; ?>">
							<h2 class="mb-0 ">
								<button class="btn btn-link btn-block text-left collapsed  font-weight-bold" type="button" data-toggle="collapse" data-target="#collapse-cat-<?php echo $catKey; ?>" aria-expanded="false" aria-controls="collapse-cat-<?php echo $catKey; ?>">
									<?php echo mb_strtoupper(fString($categoria["nombre"])); ?>
								</button>
							</h2>
						</div>

						<div id="collapse-cat-<?php echo $catKey; ?>" class="collapse" aria-labelledby="heading-cat-<?php echo $catKey; ?>" data-parent="#accordionCategorias">
							<div class="card-body">
							<div class="accordion" id="accordionGrupos-<?php echo $catKey; ?>">
								<?php foreach ($grupos as $grpId => $grupo) { ?>
									<div class="card">
										<div class="card-header" id="heading-grp-<?php echo $catKey . '-' . $grpId; ?>">
											<h2 class="mb-0">
												<button class="btn btn-link btn-block text-left collapsed font-weight-bold" type="button" data-toggle="collapse" data-target="#collapse-grp-<?php echo $catKey . '-' . $grpId; ?>" aria-expanded="false" aria-controls="collapse-grp-<?php echo $catKey . '-' . $grpId; ?>">
													<?php echo mb_strtoupper($grupo); ?>
												</button>
											</h2>
										</div>

										<div id="collapse-grp-<?php echo $catKey . '-' . $grpId; ?>" class="collapse" aria-labelledby="heading-grp-<?php echo $catKey . '-' . $grpId; ?>" data-parent="#accordionGrupos-<?php echo $catKey; ?>">
											<div class="card-body">
												<table class="table table-bordered table-striped">
													<tbody>
														<tr>
															<td><strong>Permiso</strong></td>
															<td class="text-center" style="width:55px;"><strong>Asignar</strong></td>
														</tr>
														<?php foreach ($permisos as $permiso) {
															// Verificamos si el permiso pertenece al grupo actual
															if (mb_strtoupper($permiso["grupo"]) === mb_strtoupper($grupo)) {
														?>
															<tr>
																<td class="text-capitalize"><?php echo mb_strtoupper(fString($permiso["nombre"])); ?></td>
																<td class="text-center">
																	<!-- Usamos el ID de la categoría y el ID del grupo en los nombres de los inputs -->
																	<input type="checkbox" 
																		name="permisos[<?php echo $categoria['id']; ?>][<?php echo $grpId; ?>][]" 
																		value="<?php echo $permiso['id']; ?>"
																		<?php
																			if (
																				isset($permisosMarcados[$categoria['id']][$grpId][$permiso['id']])
																			) {
																				echo 'checked';
																			}
																		?>
																	>
																</td>
															</tr>
														<?php }} ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								<?php } // end foreach grupos ?>
							</div>

							</div>
						</div>
					</div>
				<?php } ?>
			</div>


			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-warning card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->
</div> <!-- <div class="row"> -->