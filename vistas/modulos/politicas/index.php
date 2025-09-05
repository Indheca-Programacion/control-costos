<?php use App\Route; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Politicas</li>
                </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary card-outline">

                        <div class="card-body">

                            <div class="row">
                                <div class="col-6">
                                    <img src="vistas/img/indheca_logo.webp" alt="logoSGI" class="img-fluid" style="width: 30%;">
                                </div>
                                <div class="col-6 text-right">
                                    <img src="vistas/img/SGI.webp" alt="logoSGI" class="img-fluid " style="width: 10%;">
                                </div>
                            </div>

                            <h1 class="text-center">POLITICA INTEGRAL</h1>

                            <div class="container">
                                <p >De acuerdo al propósito y contexto nacional de la empresa, propiciando las condiciones adecuadas para estimular el trabajo en equipo, la creatividad individual, la optimización de los recursos y apoyando la dirección estratégica en todos sus procesos para llevar a cabo los servicios de Construcción, Supervisión de obra y Renta de maquinaria, INDHECA GRUPO CONSTRUCTOR, S.A. DE C.V. se compromete a:</p>
    
                                <ul>
                                    <li>Cumplir los requisitos aplicables de sus partes interesadas;</li>
                                    <li>Proteger el medio ambiente previniendo la contaminación y daños a la comunidad;</li>
                                    <li>Proteger la biodiversidad y los ecosistemas donde lleve a cabo sus trabajos;</li>
                                    <li>Proporcionar condiciones de trabajo seguras y saludables para la prevención de lesiones y deterioro de la salud relacionadas con el trabajo según la naturaleza de los riesgos y oportunidades de seguridad y salud en el trabajo;</li>
                                    <li>Eliminar los peligros y reducir los riesgos de seguridad y salud en el trabajo;</li>
                                    <li>Propiciar la consulta y la participación de los trabajadores y/o sus representantes;</li>
                                    <li>Cumplir con la normatividad y legislación mexicana en materia ambiental, de
                                    seguridad, salud en el trabajo y otros requisitos;</li>
                                </ul>
    
                                <p>
                                    Todo lo anterior estableciendo objetivos que serán revisados periódicamente para cumplir nuestro compromiso con la mejora continua en la eficacia del Sistema de Gestión Integrado en ISO 9001, ISO 14001 e ISO 45001.
                                </p>
                            </div>

                            <hr>
                            <h1 class="text-center">Filosofía Empresarial</h1>
                            <div class="container">
                                <h2 class="text">Mision</h2>
                                <p class="text-justify">Ser el proveedor líder de soluciones innovadoras en el sector de la construcción y arrendamiento de maquinaria, ofreciendo productos y servicios de alta calidad, seguros y eficientes, que superen las expectativas de nuestros clientes que contribuyan al desarrollo sostenible de la comunidad, promoviendo la mejora continua en todas nuestras operaciones.</p>
                                <h2 class="">Vision</h2>
                                <p class="text-justify">
                                Consolidarnos como empresa líder en el sector de la construcción y arrendamiento de maquinaria en la república Mexicana, reconocida por nuestra excelencia operativa, innovación sostenible, y responsabilidad social, creando valor duradero y tangible para nuestros clientes, empleados, accionistas y la comunidad.
                                </p>
                                <h2 class="">
                                    Valores
                                </h2>
                                    <ul>
                                        <li>Integridad</li>
                                        <li>Excelencia</li>
                                        <li>Responsabilidad</li>
                                        <li>Sostenibilidad</li>
                                    </ul>
                                    
                                    <a href="<?=Route::routes('ingreso')?>" class="btn btn-outline-primary ">Continuar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- ./row -->
        </div><!-- /.container-fluid -->
        
    </section>
</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/plantillas.js?v=1.00');
?>