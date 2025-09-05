$(function () {
  /*==============================================================
	LISTADO DE MENSAJES

	FUNCIÓN ENCARGADA DE MOSTRAR LOS MENSAJES POR MEDIO
	DE AJAX SE MUESTRAN EN FORMA DE LISTA EN EL CHAT.
	==============================================================*/
  let bodyChat = document.getElementById("direct-chat-messages");

  if (bodyChat != null) {
    setInterval(function () {
      const idRequisicion = $("#requisicionId").val().trim();
      $.ajax({
        url:
          rutaAjax +
          `app/Ajax/MensajeRequisicionAjax.php?idRequisicion=${idRequisicion}`,
        method: "GET",
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
      })
        .done(function (respuesta) {
          // Limpiar el contenedor primero (opcional)
          $("#direct-chat-messages").html("");

          respuesta.mensajes.forEach(function (mensaje) {
            // Verificar si el idUsuario coincide con el idSesion
            const esPropio = mensaje.idUsuario == mensaje.idSesion;

            // Agregar la clase 'right' si es del propio usuario
            const claseRight = esPropio ? "right" : "";

            // Construir el mensaje HTML
            const mensajeHtml = `
            <div class="direct-chat-msg ${claseRight}">
              <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name ${
                  esPropio ? "float-right" : "float-left"
                }">
                  ${mensaje.nombreCompleto}
                </span>
                <span class="direct-chat-timestamp ${
                  esPropio ? "float-left" : "float-right"
                }">
                  ${mensaje.fechaEnviado}
                </span>
              </div>
              <div class="direct-chat-text">
                ${mensaje.mensaje}
              </div>
            </div>
          `;

            // Agregar el mensaje al contenedor
            $("#direct-chat-messages").append(mensajeHtml);
          });
        })
        .fail(function (error) {
          $("#error-message-container").html(`
            <div class="alert alert-danger mt-2 mx-2" role="alert">
                ${
                  error.errorMessage ||
                  "Error al obtener los mensajes, llama a un administrador"
                }
            </div>
        `);
        })
        .always(function () {});
    }, 4000);
  }

  /*==============================================================
	CREACIÓN DE MENSAJES

	FUNCIÓN ENCARGADA DE LA CREACION DE LOS MENSAJES 
	MANDANDO LOS DATOS A TRAVEZ DE AJAX POR MEDIO DE 
	UNA PETICIÓN POST.
	==============================================================*/
  $("#btnCrearMensaje").click(function (e) {
    e.preventDefault(); // Evita que se recargue la página

    const mensaje = $("#mensaje").val().trim();
    const idRequisicion = $("#requisicionId").val().trim();
    const _token = $("#token_id").val().trim();

    if (mensaje === "") {
      $("#mensaje-peticion").html(`
                <div class="alert alert-danger mt-2" role="alert">
                    No se ha escrito un mensaje.
                </div>
            `);
      // Limpiar después de 1 segundo (1000 ms)
      setTimeout(function () {
        $("#mensaje-peticion").html("");
      }, 1000);
    } else {
      // Si hay mensaje, limpiamos cualquier alerta anterior
      $("#mensaje-peticion").html("");

      let dataSend = new FormData();
      dataSend.append("accion", "crearMensaje");
      dataSend.append("_token", _token);
      dataSend.append("mensaje", mensaje);
      dataSend.append("id_requisicion", idRequisicion);

      // Bloqueamos el botón y mostramos cargando
      $("#btnCrearMensaje").prop("disabled", true);
      $("#btnCrearMensaje").html(`
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...
    `);

      $.ajax({
        url: rutaAjax + "app/Ajax/MensajeRequisicionAjax.php",
        method: "POST",
        data: dataSend,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
      })
        .done(function (respuesta) {
          if (!respuesta.error) {
            $("#mensaje").val("");

            $("#mensaje-peticion").html(`
                            <div class="alert alert-success mt-2" role="alert">
                                ${respuesta.mensaje}
                            </div>
                        `);
            // Limpiar después de 1 segundo (1000 ms)
            setTimeout(function () {
              $("#mensaje-peticion").html("");
            }, 1000);
          }
        })
        .fail(function (error) {
          $("#mensaje-peticion").html(`
                        <div class="alert alert-danger mt-4" role="alert">
                            Error en el sistema hable con el administrador.
                        </div>
                    `);

          // Limpiar después de 1 segundo (1000 ms)
          setTimeout(function () {
            $("#mensaje-peticion").html("");
          }, 2000);
        })
        .always(function () {
          // Siempre se ejecuta: desbloquear el botón y restaurar texto
          $("#btnCrearMensaje").prop("disabled", false);
          $("#btnCrearMensaje").html(`Enviar`);
        });
    }
  });
});
