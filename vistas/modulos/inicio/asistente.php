<?php
  use App\Route; 
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user-tie mr-1 text-primary"></i>
            Asistente.
		</h3>
    </div>
    <div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-striped m-0" width="100%">
				<thead>
					<tr>
                        <?php
                            date_default_timezone_set('America/Mexico_City');
                            $hora = (int)date('H');
                            if ($hora >= 6 && $hora < 13) {
                                $saludo = 'días';
                            } elseif ($hora >= 13 && $hora < 19) {
                                $saludo = 'tardes';
                            } else {
                                $saludo = 'noches';
                            }
                        ?>
                        <td>
                            <h4 class="d-block text-capitalize">
                                Buenas <?= $saludo ?> <?= mb_strtolower(fString($usuarioAutenticado->prefijo_usuario.' '.$usuarioAutenticado->nombre)) ?> ¿Qué desea hacer?
                            </h4>
                        </td>
					</tr>
				</thead>
				<tbody class="text-nowrap text-uppercase">
                    <tr>
                        <td>
                            <a class="asistente-link" href="<?= Route::names('requisiciones.create') ?>">Crear Requisición.</a>
                            <style>
                                .asistente-link {
                                    color: inherit;
                                    text-decoration: none;
                                    transition: color 0.2s, opacity 0.2s;
                                }
                                .asistente-link:hover {
                                    color: #6ba5f1ff; /* Cambia a rojo al hacer hover */
                                }
                                tr:hover .asistente-link {
                                    opacity: 1;
                                }
                            </style>
                        </td>
                    </tr>
				</tbody>
            </table>
		</div>
	</div> <!-- /.card-body -->
</div>