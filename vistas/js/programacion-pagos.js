$(function(){

    let tableList = document.getElementById('tablaProveedores');
    let tableOrdnesCompra = document.getElementById('tablaProgramacionPagos');
    if (tableOrdnesCompra != null) {
      $('#tablaProgramacionPagos').DataTable({
        autoWidth: false,
        info: false,
        paging: true,
        pageLength: 25,
        searching: true,
        language: LENGUAJE_DT,
        aaSorting: [],
        columnDefs: [
          { orderable: false, targets: 0 }
        ]
      });
    }

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
        tags: false,
        width: '100%'
      // ,theme: 'bootstrap4'
    });

    $('.datetimepicker-input').datetimepicker({
        format: 'DD/MMMM/YYYY',
        locale: 'es'
    });

    // Eliminar un Bloque
    $("button.eliminar").on("click", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar este Bloque (Nombre: '+folio+') ?',
      text: "No podrá recuperar esta información!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, quiero eliminarlo!',
      cancelButtonText:  'No!'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    })

    });

    // Eliminar una Orden de Compra de un Bloque
    $("button[data-bloque-id][data-orden-id]").on("click", function (e) {

      e.preventDefault();
      var bloqueId = $(this).data("bloque-id");
      var ordenId = $(this).data("orden-id");

      Swal.fire({
        title: '¿Estás Seguro de querer eliminar esta Orden de Compra del Bloque?',
        text: "No podrá recuperar esta información!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, quiero eliminarlo!',
        cancelButtonText:  'No!'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
            url: rutaAjax + 'app/Ajax/ProgramacionPagosAjax.php',
            type: 'POST',
            data: {
              accion: 'eliminarOrdenBloque',
              bloqueId: bloqueId,
              ordenId: ordenId
            },
            dataType: 'json',
            success: function(response) {
              if (response.codigo === 200) {
                Swal.fire('Eliminado!', response.respuestaMessage, 'success').then(() => {
                location.reload();
                });
              } else {
              Swal.fire('Error!', response.errorMessage, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'Ocurrió un error al procesar la solicitud.', 'error');
            }
            });
        }
      });

    });

    $('.btn-marcar-pagado').on('click', function() {
      var bloqueId = $(this).attr('data-bloque-id');
      Swal.fire({
      title: '¿Marcar bloque como pagado?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, marcar',
      cancelButtonText: 'Cancelar'
      }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
        url: rutaAjax + 'app/Ajax/ProgramacionPagosAjax.php',
        method: 'POST',
        dataType: 'json',
        headers: {
          'X-CSRF-TOKEN': '<?= token() ?>'
        },
        data: {
          accion: 'marcarPagado',
          bloque_id: bloqueId
        },
        success: function(data) {
          if (!data.error) {
          Swal.fire('¡Marcado!', 'El bloque fue marcado como pagado.', 'success')
            .then(function() { location.reload(); });
          } else {
          Swal.fire('Error', data.errorMessage || 'No se pudo marcar como pagado.', 'error');
          }
        },
        error: function() {
          Swal.fire('Error', 'Ocurrió un error en la petición.', 'error');
        }
        });
      }
      });
    });
});