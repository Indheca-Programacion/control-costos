$(function(){
    
    // Envio del formulario para Crear o Editar registros
    function enviar(){
      btnEnviar.disabled = true;
      mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";
  
      padre = btnEnviar.parentNode;
      padre.removeChild(btnEnviar);
  
      formulario.submit(); // Enviar los datos
    }
    let formulario = document.getElementById("formSend");
    let mensaje = document.getElementById("msgSend");
    let btnEnviar = document.getElementById("btnSend");
    if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

    $('.select2').select2({
      tags: true,
      width: '100%'
    });

    let tipo = 0;

    /*==============================================================
    Abrir el input al presionar el botón Cargar archivo
    ==============================================================*/
    $(".btnSubirArchivo").click(function(){
      document.getElementById('archivo').click();
      tipo = $(this).attr('id'); // Get the id of the clicked button and assign it to tipo
    });

    /*========================================================
    Validar tipo y tamaño de los archivos
    ========================================================*/
    $("#archivo").change(function() {

      let archivos = this.files;
      if ( archivos.length == 0) return;

      let error = false;

      for (let i = 0; i < archivos.length; i++) {

          let archivo = archivos[i];
          
        /*==========================================
        VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
        ==========================================*/
        
        if ( archivo["type"] != "application/pdf" ) {

          error = true;

          Swal.fire({
            title: 'Error en el tipo de archivo',
            text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          })

        } else if ( archivo["size"] > 4000000 ) {

          error = true;

          Swal.fire({
            title: 'Error en el tamaño del archivo',
            text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          })

        }

      }

      if ( error ) {
        $("#archivo").val("");

        return;
      }

      let formData = new FormData();
      formData.append("tipo", tipo);

      for (let i = 0; i < archivos.length; i++) {
        formData.append("archivos[]", archivos[i]);
      }
      formData.append("accion", "subirArchivos");

      $.ajax({
        url: rutaAjax+'app/Ajax/DatosFiscalesAjax.php', // Cambia esta URL por la ruta de tu servidor
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          Swal.fire({
            title: 'Éxito',
            text: '¡Archivos subidos correctamente!',
            icon: 'success',
            confirmButtonText: '¡Cerrar!'
          });
          location.reload(); // Recargar la página para mostrar los nuevos archivos subidos
        },
        error: function(xhr, status, error) {
          Swal.fire({
            title: 'Error',
            text: 'Hubo un problema al subir los archivos. Por favor, inténtelo de nuevo.',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          });
        }
      });

    }) // $("#archivo").change(function(){

    /*========================================================
    Ver el archivo PDF en un iframe al hacer clic en el botón "Ver Archivo"
    ========================================================*/
    $('.verArchivo').on('click', function () {
        const archivoRuta = $(this).attr('archivoRuta');
        $('#archivoIframe').attr('src', archivoRuta);
    });

    /*========================================================
    Eliminar el archivo al hacer clic en el botón "Eliminar Archivo"
    ========================================================*/
    $("i.eliminarArchivo").on("click", function (e) {

      let btnEliminar = this;
        // let archivoId = $(this).attr("archivoId");
        let folio = $(this).attr("folio");
  
        Swal.fire({
        title: '¿Estás Seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
        text: "No podrá recuperar esta información!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, quiero eliminarlo!',
        cancelButtonText:  'No!'
        }).then((result) => {
        if (result.isConfirmed) {
          eliminarArchivo(btnEliminar);
        }
        })
  
    });
  
    /*========================================================
    Eliminar el archivo al hacer clic en el botón "Eliminar Archivo"
    ========================================================*/
    function eliminarArchivo(btnEliminar = null){
  
      if ( btnEliminar == null ) return;		
  
      let archivoId = $(btnEliminar).attr("archivoId");
  
      // $(btnEliminar).prop('disabled', true);
  
      let datos = new FormData();
      datos.append("accion", "eliminarArchivo");
      datos.append("archivoId", archivoId);
  
      $.ajax({
          url: rutaAjax+"app/Ajax/DatosFiscalesAjax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success:function(respuesta){
  
            // console.log(respuesta)
            // Si la respuesta es positiva pudo eliminar el archivo
            if (respuesta.respuesta) {
  
              $(btnEliminar).parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');
  
              $(btnEliminar).parent().remove();

              // Eliminar el <small> del estatus
              $('#estatus-' + archivoId).remove();

                // Recargar después de 2 segundos
                setTimeout(function() {
                  location.reload();
                }, 2000);
  
            } else {
  
              $(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');
  
              // $(btnEliminar).prop('disabled', false);
  
            }
  
            setTimeout(function(){ 
              $(".alert").remove();
            }, 5000);
  
          }
  
      })
  
    }

    /*========================================================
    Abrir el modal para agregar un nuevo permiso
    ========================================================*/
    $('.btnAgregarPermiso').on('click', function(e) {
      let formData = new FormData();

      formData.append('accion', 'agregarPermiso');
      formData.append('tituloPermiso', $('#tituloPermiso').val());
      formData.append('proveedorId', $('#proveedorId').val());
      let archivoPermiso = document.getElementById('archivoPermiso');
      let files = archivoPermiso.files;

      if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          formData.append('archivos[]', files[i]);
        }
      }

      $.ajax({
      url: rutaAjax+'app/Ajax/PermisosProveedorAjax.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
            // Manejar la respuesta del servidor
            if (response.error) {
            Swal.fire({
              title: 'Error',
              text: response.errorMessage || 'Ocurrió un error al agregar el permiso.',
              icon: 'error',
              confirmButtonText: '¡Cerrar!'
            });
            } else {
            Swal.fire({
              title: 'Éxito',
              text: 'Permiso agregado correctamente.',
              icon: 'success',
              confirmButtonText: '¡Cerrar!'
            });
            $('#modalAgregarPermiso').modal('hide');
            location.reload(); // Recargar la página para actualizar la tabla
            }
      },
      error: function(xhr, status, error) {
          // Manejar errores
            Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al agregar el permiso.',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
            });
      }
      });
    });

    /*========================================================
    Elimina el permiso al hacer clic en el botón "Eliminar Permiso"
    ========================================================*/
    $('button.btnEliminarPermiso').on('click', function(e) {
      // console.log('Eliminar Permiso');
      // return
      let permisoId = $(this).attr('data-permiso-id');
      let btnEliminar = this;


      Swal.fire({
        title: '¿Estás Seguro de querer eliminar este Permiso?',
        text: "No podrá recuperar esta información!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, quiero eliminarlo!',
        cancelButtonText:  'No!'
      }).then((result) => {
        if (result.isConfirmed) {
          eliminarPermiso(btnEliminar, permisoId);
        }
      })
    });

    /*========================================================
    funcion para elimina el permiso al hacer clic en el botón "Eliminar Permiso"
    ========================================================*/
    function eliminarPermiso(btnEliminar = null, permisoId = null){
      if ( btnEliminar == null || permisoId == null ) return;		

      let datos = new FormData();
      datos.append("accion", "eliminarPermiso");
      datos.append("permisoId", permisoId);

      $.ajax({
          url: rutaAjax+"app/Ajax/PermisosProveedorAjax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success:function(respuesta){

            // Si la respuesta es positiva pudo eliminar el archivo
            if (!respuesta.error) {
              Swal.fire({
              title: 'Éxito',
              text: 'Permiso eliminado correctamente.',
              icon: 'success',
              confirmButtonText: '¡Cerrar!'
              }).then(() => {
              location.reload(); // Recargar la página
              });
            } else {
              Swal.fire({
              title: 'Error',
              text: respuesta.errorMessage,
              icon: 'error',
              confirmButtonText: '¡Cerrar!'
              });
            }

          }

      })
    }

    /*========================================================
    Ver archivos del permiso al hacer clic en el botón "Ver Archivos"
    ========================================================*/
    $('#modalVerArchivos').on('shown.bs.modal', function (e) {
      let permisoId = $(e.relatedTarget).data('permiso-id'); // Obtener el permisoId del botón que abrió el modal

      if (!permisoId) return;

      let datos = new FormData();
      datos.append("accion", "verArchivos");
      datos.append("permisoId", permisoId);

      $.ajax({
        url: rutaAjax + "app/Ajax/PermisosProveedorAjax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
          if (!respuesta.error) {
            let archivos = respuesta.archivos; // Suponiendo que el servidor devuelve un array de archivos
            let accordionHtml = '';

            archivos.forEach((archivo, index) => {
                accordionHtml += `
                <div class="card">
                  <div class="card-header" id="heading${index}">
                  <h5 class="mb-0 d-flex justify-content-between align-items-center">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                  ${archivo.titulo}
                  </button>
                  <button class="btn btn-danger btn-sm btnEliminarArchivo" data-archivo-id="${archivo.id}"><i class="fas fa-trash-alt"></i></button>
                  </h5>
                  </div>
                  <div id="collapse${index}" class="collapse ${index === 0 ? 'show' : ''}" aria-labelledby="heading${index}" data-parent="#accordionArchivos">
                  <div class="card-body">
                  <iframe src="${archivo.ruta}" width="100%" height="400px" frameborder="0"></iframe>
                  </div>
                  </div>
                </div>
                `;

                
            });

            $('#accordionArchivos').html(accordionHtml);
          } else {
            Swal.fire({
              title: 'Error',
              text: respuesta.errorMessage,
              icon: 'error',
              confirmButtonText: '¡Cerrar!'
            });
          }
        },
        error: function () {
          Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al obtener los archivos.',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          });
        }
      });
    });

    /*========================================================
    Eliminar el archivo al hacer clic en el botón "Eliminar Archivo"
    ========================================================*/
    $(document).on('click', `button.btnEliminarArchivo`, function () {
      let archivoId = $(this).data('archivo-id');
      Swal.fire({
      title: '¿Estás Seguro de querer eliminar este Archivo?',
      text: "No podrá recuperar esta información!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, quiero eliminarlo!',
      cancelButtonText: 'No!'
      }).then((result) => {
      if (result.isConfirmed) {
        let datos = new FormData();
        datos.append("accion", "eliminarArchivo");
        datos.append("archivoId", archivoId);

        $.ajax({
        url: rutaAjax + "app/Ajax/PermisosProveedorAjax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
          if (!respuesta.error) {
          Swal.fire({
            title: 'Éxito',
            text: 'Archivo eliminado correctamente.',
            icon: 'success',
            confirmButtonText: '¡Cerrar!'
          }).then(() => {
            location.reload(); // Recargar la página
          });
          } else {
          Swal.fire({
            title: 'Error',
            text: respuesta.errorMessage,
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          });
          }
        },
        error: function () {
          Swal.fire({
          title: 'Error',
          text: 'Ocurrió un error al eliminar el archivo.',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
          });
        }
        });
      }
      });
    });

    /*========================================================
    Subir archivos al hacer clic en el botón "Subir Archivos"
    ========================================================*/
    $('button.btnSubirArchivoPermiso').on('click', function () {
      let permisoId = $(this).data('permiso-id'); // Obtener el data-permiso-id del botón
      $('#archivoPermiso').data('permiso-id', permisoId); // Asignar el permisoId al input de archivo
      $('#archivoPermiso').click(); // Abrir el input de archivo
    });

    /*========================================================
    Subir archivos al seleccionar un archivo
    ========================================================*/
    $('#archivoPermiso').on('change', function () {
      let permisoId = $(this).data('permiso-id'); // Obtener el permisoId del input
      if (!permisoId) {
        console.error('No se encontró el permisoId.');
        return;
      }

      let archivos = this.files;
      if (archivos.length > 0) {
        let formData = new FormData();
        formData.append('accion', 'subirArchivos');
        formData.append('permisoId', permisoId); // Agregar el permisoId al FormData
        for (let i = 0; i < archivos.length; i++) {
          formData.append('archivos[]', archivos[i]);
        }
        $.ajax({
          url: rutaAjax + 'app/Ajax/PermisosProveedorAjax.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {
          // Manejar la respuesta del servidor
          Swal.fire({
            title: 'Éxito',
            text: 'Archivos subidos correctamente.',
            icon: 'success',
            confirmButtonText: '¡Cerrar!'
          });
          location.reload(); // Recargar la página para mostrar los nuevos archivos subidos
          },
          error: function (xhr, status, error) {
          // Manejar errores
          Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al subir los archivos.',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          });
          }
        });
      }
    });

});