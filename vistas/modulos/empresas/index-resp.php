<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">
    
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Empresas <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo Route::routes('inicio'); ?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Empresas</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->

  </section>

  <section class="content">
 
    <?php if ( !is_null(flash()) ) : ?>
      <div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
    <?php endif; ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-secondary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list-ol"></i>
                Listado de Empresas
              </h3>
              <div class="card-tools">
                <a href="<?php echo Route::names('empresas.create'); ?>" class="btn btn-outline-primary"> <!-- float-right -->
                  <i class="fas fa-plus"></i> Crear empresa
                </a>                
              </div>
            </div>
            <!-- <div class="card-body pad table-responsive"> -->
            <div class="card-body">

             <!-- <table class="table table-bordered table-striped dt-responsive tablas" width="100%"> -->
             <table class="table table-bordered table-striped tablas" width="100%">
               
              <thead>
               <tr>
                 <th style="width:10px">#</th>
                 <th>Razón Social</th>
                 <th>Nombre Corto</th>
                 <th>RFC</th>
                 <th>Municipio</th>
                 <th>Estado</th>
                 <th>País</th>
                 <th>Acciones</th>
               </tr> 
              </thead>

              <tbody>

              <?php foreach($empresas as $key => $value): ?>
                <tr>
                  <td><?=($key+1)?></td>
                  <td class="text-uppercase"><?=fString($value["razonSocial"])?></td>
                  <td class="text-uppercase"><?=fString($value["nombreCorto"])?></td>
                  <td class="text-uppercase"><?=fString($value["rfc"])?></td>
                  <td class="text-uppercase"><?=fString($value["municipio"])?></td>
                  <td class="text-uppercase"><?=fString($value["estado"])?></td>
                  <td class="text-uppercase"><?=fString($value["pais"])?></td>
                  <td>
                    <a href="<?=Route::names('empresas.edit', $value['id'])?>" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                    <form method="POST" action="<?=Route::names('empresas.destroy', $value['id'])?>" style="display: inline">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="_token" value="<?=createToken()?>">
                      <button type="button" class="btn btn-xs btn-danger eliminar" folio="<?=mb_strtoupper(fString($value['razonSocial']))?>">
                         <i class="far fa-times-circle"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>

              </tbody>

             </table>

            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/empresas.js');
  $arrayArchivosJS = array('vistas/js/empresas.js');
  $archivosJS = "vistas/js/empresas.js";
?>