$(function(){
	const TIEMPO_DESCARGA = 350;

    let tableList = document.getElementById('tablaResguardos');
    let tableListPartidaResguardo = document.getElementById('tablaResguardoPartida');
    
    
    let parametrosTableList = { responsive: false };
    let elementModalBuscarInventario = document.querySelector('#modalBuscarInventario');
    let dataTableSeleccionarInventarios = $('#tablaSeleccionarInventario').DataTable();
  	let datatTablePartidaSalida = null;

    // LLamar a la funcion fAjaxDataTable() para llenar el Listado
    if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ResguardoAjax.php', '#tablaResguardos');

    // Confirmar la eliminación de la Sucursal
    // $("table tbody").on("click", "button.eliminar", function (e) {
    $(tableList).on("click", "button.eliminar", function (e) {
  
      e.preventDefault();
      var folio = $(this).attr("folio");
      var form = $(this).parents('form');
  
      Swal.fire({
        title: '¿Estás Seguro de querer eliminar este Resguardo (Descripción: '+folio+') ?',
        text: "No podrá recuperar esta información!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, quiero eliminarla!',
        cancelButtonText:  'No!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      })
  
    });

    // FUNCION PARA OBTENER LAS PARTIDAS DE LOS RESGUARDOS
    if ( tableListPartidaResguardo != null ) {

          let resguardoId = $('#resguardoId').val();

          fetch( rutaAjax+'app/Ajax/ResguardoAjax.php?resguardoId='+resguardoId, {
            method: 'GET', // *GET, POST, PUT, DELETE, etc.
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            headers: {
              'Content-Type': 'application/json'
            }
          } )
          .then( response => response.json() )
          .catch( error => console.log('Error:', error) )
          .then( data => {
            
            datatTablePartidaSalida = $('#tablaResguardoPartida').DataTable({
              info: false,
              paging: false,
              pageLength: 100,
              searching: false,
              autoWidth: false,
              data: data.datos.registros,
              columns: data.datos.columnas,
              language: LENGUAJE_DT,
              aaSorting: [],
            });
      
          });
    }

    // Envio del formulario para Crear o Editar registros
    function enviar(){
      btnEnviar.disabled = true;
      mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";
  
      padre = btnEnviar.parentNode;
      padre.removeChild(btnEnviar);
      formulario.submit();
    }
    let formulario = document.getElementById("formSend");
    let mensaje = document.getElementById("msgSend");
    let btnEnviar = document.getElementById("btnSend");
    if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

    // Activar el elemento Select2
    $('.select2').select2({
        tags: false
    });
    // input date
    $('.input-group.date').datetimepicker({
      format: 'DD/MMMM/YYYY'
    }); // $('.input-group.date').datetimepicker({

    // Buscar Inventario
    $('#btnBuscarInventario').on('click', function (e) {

      let elementErrorValidacion = elementModalBuscarInventario.querySelector('.error-validacion');
      elementErrorValidacion.querySelector('ul').innerHTML = '';
      $(elementErrorValidacion).addClass("d-none");
      let tableList = document.getElementById('tablaSeleccionarInventario');
      $(tableList).DataTable().destroy();
      tableList.querySelector('tbody').innerHTML = '';

      fetch( `${rutaAjax}app/Ajax/InventarioAjax.php?disponibles`, {
        method: 'GET', // *GET, POST, PUT, DELETE, etc.
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        headers: {
          'Content-Type': 'application/json'
        }
      } )
      .then( response => response.json() )
      .catch( error => console.log('Error:', error) )
      .then( data => {

        if ( data.error ) {
          let elementList = document.createElement('li'); // prepare a new li DOM element
          let newContent = document.createTextNode(data.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector('ul').appendChild(elementList);

          $(elementErrorValidacion).removeClass("d-none");

          return;
        }

        dataTableSeleccionarInventarios = $(tableList).DataTable({

          autoWidth: false,
          responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
          // info: false,
          // paging: false,
          // searching: false,
          data: data.datos.registros,
          columns: data.datos.columnas,

          columnDefs: [
            // { targets: [0], visible: false, searchable: false },
            // { targets: [1], className: 'col-fixed-left' },
            // { targets: arrayColumnsTextRight, className: 'text-right' },
            // { targets: arrayColumnsTextCenter, className: 'text-center' },
            // { targets: arrayColumnsOrderable, orderable: false }
          ],

          createdRow: (row, data, index) => {
            row.classList.add('seleccionable');
          },

          language: LENGUAJE_DT,
          aaSorting: [],

        }); // $(tableListResumen).DataTable({

        $(elementModalBuscarInventario).modal('show');

      }); // .then( data => {

    });

    dataTableSeleccionarInventarios.on('click', 'tbody tr.seleccionable', function () {
      let data = dataTableSeleccionarInventarios.row(this).data();
      
      const descripcion = document.getElementById("descripcion")
      const cantidad = document.getElementById('cantidad')
      const unidad = document.getElementById('unidad')
      descripcion.value = data.descripcion
      cantidad.value = data.cantidad_disponible
      unidad.value = data.unidadid

      $('#inventarioId').val(data.id)
      $('#unidad').trigger('change')
      $(elementModalBuscarInventario).modal('hide');
    });

    // Envio del formulario para Cancelar el registro
    function eliminarArchivo(btnEliminar = null){

      if ( btnEliminar == null ) return;		

      let archivoId = $(btnEliminar).attr("archivoId");
      // $(btnEliminar).prop('disabled', true);

      let token = $('input[name="_token"]').val();
      let resguardoId = $('#resguardoId').val();


      let datos = new FormData();
      datos.append("_token", token);
      datos.append("accion", "eliminarArchivo");
      datos.append("archivoId", archivoId);
      datos.append("resguardoId", resguardoId);

      $.ajax({
          url: rutaAjax+"app/Ajax/ResguardoAjax.php",
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

              $(btnEliminar).parent().parent().parent().remove();

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

    $("#btnSubirArchivos").click(function(){
      document.getElementById('archivos').click();
    })

    $('#archivos').change(function () {
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

          // $("#comprobanteArchivos").val("");
          // $("div.subir-comprobantes span.lista-archivos").html('');

          Swal.fire({
            title: 'Error en el tipo de archivo',
            text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
            icon: 'error',
            confirmButtonText: '¡Cerrar!'
          })

        } else if ( archivo["size"] > 4000000 ) {

          error = true;

          // $("#comprobanteArchivos").val("");
          // $("div.subir-comprobantes span.lista-archivos").html('');

          Swal.fire({
          title: 'Error en el tamaño del archivo',
          text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
          icon: 'error',
          confirmButtonText: '¡Cerrar!'
          })

        }

      }

      if ( error ) {
        $("#archivos").val("");

        return;
      }

      for (let i = 0; i < archivos.length; i++) {

        let archivo = archivos[i];

        $("div.subir-archivos span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

      }

      let cloneElementArchivos = this.cloneNode(true);
      cloneElementArchivos.removeAttribute('id');
      cloneElementArchivos.name = 'archivos[]';
      $("div.subir-archivos").append(cloneElementArchivos);
    })

    $("div.subir-archivos").on("click", "i.eliminarArchivo", function (e) {

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

    $("#btnDescargarArchivos").click(function(event) {

      event.preventDefault();

      let btnDescargarArchivos = this;
      let resguardoId = $('#resguardoId').val();
      
      $.ajax({
        url: `${rutaAjax}resguardos/${resguardoId}/download`,
        method: 'GET',
        dataType: "json",
        beforeSend: () => {
          btnDescargarArchivos.disabled = true;
        }
      })
      .done(function(data) {
        // console.log(data);
        data.archivos.forEach( (archivo, index) => {
          let link = document.createElement('a');
          // link.innerHTML = 'download file';

          link.addEventListener('click', function(event) {
            link.href = rutaAjax+archivo.ruta;
            link.download = archivo.archivo;
          }, false);

          setTimeout(() => {
            link.click();
          }, TIEMPO_DESCARGA * (index+1));
        });
      })
      .fail(function(error) {
        console.log(error);
        console.log(error.responseJSON);
      })
      .always(function() {
        btnDescargarArchivos.disabled = false;
      });

    })

});