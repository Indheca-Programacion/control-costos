$(function () {
  // *************************************************
  // * VARIABLES GLOBALES
  // *************************************************
  let tableList = document.getElementById("tablaProveedores");
  let tableDatosBancarios = document.getElementById("tablaDatosBancarios");

  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // *************************************************
  // * HELPERS
  // *************************************************
  // FUNCION PARA ENVIAR FORMULARIOS
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit(); // Enviar los datos
  }
  $(".select2").select2({
    tags: false,
    width: "100%",
    // ,theme: 'bootstrap4'
  });

  //VALIDAR QUE EL ARCHIVO SEA PDF
  function validarArchivoPDF(archivo) {
    // Verificar que sea PDF
    if (archivo.type !== "application/pdf") {
      Swal.fire({
        title: "Error en el tipo de archivo",
        text: `¡El archivo "${archivo.name}" debe ser PDF!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      return false;
    }

    // Verificar tamaño máximo (4MB)
    if (archivo.size > 4000000) {
      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: `¡El archivo "${archivo.name}" no debe pesar más de 4MB!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      return false;
    }

    return true; // ✅ Pasa validaciones
  }

  // Función para mostrar SweetAlert de éxito
  function mostrarExito(mensaje, callback = null) {
    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: mensaje || "La operación se realizó correctamente.",
      confirmButtonText: "Aceptar",
    }).then(() => {
      if (typeof callback === "function") callback();
    });
  }

  // Función para mostrar SweetAlert de error
  function mostrarError(mensaje) {
    Swal.fire({
      icon: "error",
      title: "Hubo un problema",
      text: mensaje || "Ocurrió un error en el sistema",
      confirmButtonText: "Aceptar",
    });
  }

  // *************************************************
  // * REFERENTE A PROVEEDORES
  // *************************************************

  // TABLA PROVEEDORES
  if (tableList != null)
    fAjaxDataTable(
      rutaAjax + "app/Ajax/ProveedorAjax.php",
      "#tablaProveedores"
    );

  // FUNCION ELIMINAR PROVEEDOR
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Proveedor (Nombre: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

  // VARIABLE PERSONA FISICA
  var personaFisica = document.getElementById("personaFisica");
  // FUNCION ACTIVAR/DESACTIVAR PERSONA FISICA
  if (personaFisica !== null) {
    $(".personaFisica").prop("disabled", !personaFisica.checked);
    $(".personaMoral").prop("disabled", personaFisica.checked);
    $(personaFisica).on("click", function (e) {
      if (this.checked == true) {
        // $('.personaFisica').show();
        $(".personaFisica").prop("disabled", false);
        $(".personaMoral").prop("disabled", true);
      } else {
        // $('.personaFisica').hide();
        $(".personaFisica").prop("disabled", true);
        $(".personaMoral").prop("disabled", false);
      }
    });
  }

  // VARIABLE CREDITO
  var credito = document.getElementById("credito");
  // FUNCION ACTIVAR Y DESACTIVAR LIMITE DE CREDITO
  var limiteCredito = document.getElementById("limiteCredito");
  if (limiteCredito !== null) limiteCredito.disabled = !credito.checked;
  if (credito !== null) {
    $(credito).on("click", function (e) {
      if (this.checked == true) {
        limiteCredito.disabled = false;
      } else {
        limiteCredito.disabled = true;
      }
    });
  }

  // *************************************************
  // * REFERENTE A DATOS BANCARIOS
  // *************************************************
  // TABLA DATOS BANCARIOS
  if (tableDatosBancarios != null) {
    var proveedorId = $("#proveedorId").val();

    fetch(
      rutaAjax +
        "app/Ajax/ProveedorAjax.php?accion=tableDatosBancarios&proveedorId=" +
        proveedorId,
      {
        method: "GET", // *GET, POST, PUT, DELETE, etc.
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        $("#tablaDatosBancarios").DataTable({
          autoWidth: false,
          info: false,
          paging: false,
          pageLength: 100,
          searching: false,
          autoWidth: false,
          data: data.datos.registroDatosBancarios,
          columns: data.datos.columnaDatosBancarios,
          language: LENGUAJE_DT,
          aaSorting: [],
        });
      });
  }
  // FUNCION ELIMINAR DATOS BANCARIOS
  $(tableDatosBancarios).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar el dato bancario (ID: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `${rutaAjax}app/Ajax/ProveedorAjax.php`,
          method: "POST",
          data: {
            _token: $("input[name=_token]").val(),
            datoBancarioId: folio,
            accion: "eliminarDatoBancario",
          },
          dataType: "json",
          success: function (data) {
            crearToast(
              "bg-success",
              "Eliminar Dato Bancario",
              "",
              data.mensaje
            );
            window.location.reload();
          },
        });
      }
    });
  });

  // FUNCION AGREGAR DATO BANCARIO
  $("#modalAgregarDatosBancarios .btnAgregarDatosBancarios").on(
    "click",
    function () {
      let token = $("#_token").val();
      let proveedorId = $("#proveedorId").val();
      let nombreTitular = $("#nombreTitular").val();
      let nombreBanco = $("#nombreBanco").val();
      let cuenta = $("#cuenta").val();
      let cuentaClave = $("#cuentaClave").val();
      let divisaId = $("#divisaId").val();

      if (nombreTitular == "" || nombreTitular == 0) {
        crearToast(
          "bg-danger",
          "error",
          "",
          "Se debe ingresar el nombre del titular"
        );
        return;
      }
      if (nombreBanco == "" || nombreBanco == 0) {
        crearToast(
          "bg-danger",
          "error",
          "",
          "Se debe ingresar el nombre del banco"
        );
        return;
      }
      if (divisaId == "" || divisaId == 0) {
        crearToast("bg-danger", "error", "", "Se debe seleccionar una divisa");
        return;
      }

      let dataSend = new FormData();
      dataSend.append("accion", "agregarDatosBancarios");
      dataSend.append("_token", token);
      dataSend.append("proveedorId", proveedorId);
      dataSend.append("nombreTitular", nombreTitular);
      dataSend.append("nombreBanco", nombreBanco);
      dataSend.append("cuenta", cuenta);
      dataSend.append("cuentaClave", cuentaClave);
      dataSend.append("divisaId", divisaId);

      $.ajax({
        url: rutaAjax + "app/Ajax/ProveedorAjax.php",
        method: "POST",
        data: dataSend,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
      }).done(function (respuesta) {
        console.log(respuesta);
        if (respuesta.error) {
          crearToast(
            "bg-danger",
            "Agregar Datos Bancarios",
            "Error",
            respuesta.errorMessage
          );
          return;
        }

        crearToast(
          "bg-success",
          "Agregar Datos Bancarios",
          "OK",
          respuesta.mensaje
        );
        window.location.reload();
      });
    }
  );

  // MODAL EDITAR DATOS BANCARIOS
  $(tableDatosBancarios).on("click", "button.editar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    const $modal = $("#modalAgregarDatosBancarios");

    $.ajax({
      url: `${rutaAjax}app/Ajax/ProveedorAjax.php?accion=obtenerDatoBancarioPorId&datoBancarioId=${folio}`,
      method: "GET",
      dataType: "json",
      success: function (data) {
        $modal
          .find(".modal-title")
          .html('<i class="fas fa-edit"></i> Editar datos bancarios');

        $modal.find("#datoBancarioId").val(data.datos.id || "");
        $modal.find("#nombreTitular").val(data.datos.nombreTitular || "");
        $modal.find("#nombreBanco").val(data.datos.nombreBanco || "");
        $modal.find("#cuenta").val(data.datos.cuenta || "");
        $modal.find("#cuentaClave").val(data.datos.cuentaClave || "");
        $modal
          .find("#divisaId")
          .val(data.datos.divisaId || "")
          .trigger("change");

        $modal.find(".btnAgregarDatosBancarios").addClass("d-none");
        $modal.find(".btnEditarDatosBancarios").removeClass("d-none");
      },
      error: function (xhr, status, err) {
        console.error("Error al obtener datos bancarios:", err);
      },
    });

    // Muestra el modal
    $("#modalAgregarDatosBancarios").modal("show");
  });

  // FUNCION EDITAR DATOS BANCARIOS
  $(document).on("click", ".btnEditarDatosBancarios", function () {
    var $modal = $("#modalAgregarDatosBancarios");
    const $btn = $("#btnEditarDatosBancarios");

    let dataSend = new FormData();
    dataSend.append("accion", "editarDatoBancario");
    dataSend.append("_token", $("#_token").val().trim());
    dataSend.append(
      "nombreTitular",
      $modal.find("#nombreTitular").val().trim()
    );
    dataSend.append("nombreBanco", $modal.find("#nombreBanco").val().trim());
    dataSend.append("cuenta", $modal.find("#cuenta").val().trim());
    dataSend.append("cuentaClave", $modal.find("#cuentaClave").val().trim());
    dataSend.append("divisaId", $modal.find("#divisaId").val().trim());
    dataSend.append("datoBancarioId", $modal.find("#datoBancarioId").val());

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",

      beforeSend() {
        $btn.prop("disabled", true); // 🔒 Deshabilita el botón
        crearToast(
          "bg-info",
          "Agregar Datos Bancarios",
          "Enviando…",
          "Guardando la información, por favor espera."
        );
      },
    })
      .done(function (respuesta) {
        if (respuesta.error === false) {
          crearToast(
            "bg-success",
            "Agregar Datos Bancarios",
            "Éxito",
            respuesta.mensaje || "Datos guardados correctamente."
          );
          setTimeout(() => window.location.reload(), 800);
        } else {
          crearToast(
            "bg-danger",
            "Agregar Datos Bancarios",
            "Error",
            respuesta?.errorMessage || "No se pudieron guardar los datos."
          );
        }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        crearToast(
          "bg-danger",
          "Agregar Datos Bancarios",
          "Fallo de red",
          `Hubó un problema al actualizar el dato bancario.`
        );
      })
      .always(function () {
        $btn.prop("disabled", false); // 🔓 Rehabilita el botón
      });
  });

  // FUNCION LIMPIAR MODAL AL CERRARSE
  $("#modalAgregarDatosBancarios").on("hidden.bs.modal", function () {
    const $m = $(this);
    const $f = $m.find("form");

    $m.find(".modal-title").html(
      '<i class="fas fa-plus"></i> Agregar datos bancarios'
    );

    $f.trigger("reset");
    $f.find('input[type="hidden"]').val("");
    $f.find("#divisaId").val("").trigger("change");

    $m.find(".error-validacion").addClass("d-none").find("ul").empty();

    $m.find(".btnAgregarDatosBancarios").removeClass("d-none");
    $m.find(".btnEditarDatosBancarios").addClass("d-none");
  });

  // *************************************************
  // * REFERENTE A LOS ARCHIVOS
  // *************************************************

  // ACTIVAR INPUT PARA SUBIR ARCHIVO
  let tipo = 0;
  $(".btnSubirArchivo").click(function () {
    document.getElementById("archivo").click();
    tipo = $(this).attr("id"); // Get the id of the clicked button and assign it to tipo
  });

  // FUNCION SUBIR ARCHIVO
  var proveedorId = $("#proveedorId").val();
  $("#archivo").change(function () {
    let archivo = this.files[0];
    if (!archivo) return;

    if (!validarArchivoPDF(archivo)) {
      $(this).val("");
      return;
    }

    let formData = new FormData();
    formData.append("tipo", tipo);
    formData.append("archivo", archivo);
    formData.append("proveedorId", proveedorId);
    formData.append("accion", "subirArchivos");

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.mensaje, () => location.reload());
        } else {
          mostrarExito(respuesta.mensaje, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  });

  // ALERTA ELIMINAR ARCHIVO
  $("i.eliminarArchivo").on("click", function (e) {
    let folio = $(this).attr("folio");
    let archivoId = $(this).attr("archivoId");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Archivo (Folio: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarArchivo(archivoId);
      }
    });
  });

  // FUNCION ELIMINAR ARCHIVOS
  function eliminarArchivo(archivoId = null) {
    if (archivoId == null) return;

    let dataSend = new FormData();
    dataSend.append("accion", "eliminarArchivo");
    dataSend.append("archivoId", archivoId);

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.mensaje);
        } else {
          mostrarExito(respuesta.mensaje, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  }

  // FUNCION PARA ABRIR MODAL VER ARCHIVOS
  $(".verArchivo").on("click", function () {
    const archivoRuta = $(this).attr("archivoRuta");
    $("#archivoIframe").attr("src", archivoRuta);
  });

  // MODAL PARA VER ARCHIVOS
  $("#modalVerArchivos").on("shown.bs.modal", function (e) {
    let permisoId = $(e.relatedTarget).data("permiso-id"); // Obtener el permisoId del botón que abrió el modal

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
          let accordionHtml = "";

          archivos.forEach((archivo, index) => {
            accordionHtml += `
                <div class="card">
                  <div class="card-header" id="heading${index}">
                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
                      ${archivo.titulo}
                      </button>
                    </h5>
                  </div>
                  <div id="collapse${index}" class="collapse ${
              index === 0 ? "show" : ""
            }" aria-labelledby="heading${index}" data-parent="#accordionArchivos">
                    <div class="card-body">
                      <iframe src="${
                        archivo.ruta
                      }" width="100%" height="400px" frameborder="0"></iframe>
                    </div>
                  </div>
                </div>
                `;
          });

          $("#accordionArchivos").html(accordionHtml);
        } else {
          Swal.fire({
            title: "Error",
            text: respuesta.errorMessage,
            icon: "error",
            confirmButtonText: "¡Cerrar!",
          });
        }
      },
      error: function () {
        Swal.fire({
          title: "Error",
          text: "Ocurrió un error al obtener los archivos.",
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      },
    });
  });

  /*==============================================================
  MODAL PARA ESTADO LOS ARCHIVOS
  ==============================================================*/
  $(".estadoArchivo").on("click", function () {
    const estadoArchivo = $(this).attr("estadoArchivo");

    if (estadoArchivo == "AUTORIZADO") {
      $("#tituloEstado").text("autorizar").addClass("text-success");
      $(".subtituloEstado").text("Escriba una observacion..");

      $("#observacionEstadoArchivo").attr(
        "placeholder",
        "Escribe una obsevación (opcional)"
      );
    } else {
      $("#tituloEstado").text("rechazar").addClass("text-danger");
      $(".subtituloEstado").text("Escriba el motivo del rechazo.");
      $("#observacionEstadoArchivo").attr(
        "placeholder",
        "Escribe el motivo del rechazo (OBLIGATORIO)"
      );
    }

    $("#archivoId").attr("value", $(this).attr("archivoId"));
    $("#estadoArchivo").attr("value", estadoArchivo);

    // Mostrar el modal
    $("#estadoArchivoModal").modal("show");
  });

  // Al cerrar el modal, limpia el src del iframe
  $("#estadoArchivoModal").on("hidden.bs.modal", function () {
    $("#archivoId").attr("value", "");
    $("#estadoArchivo").attr("value", "");
    $("#observacionEstadoArchivo").val("").attr("placeholder", "");

    $("#tituloEstado").text("").removeAttr("class");
  });

  /*==============================================================
  BOTON PARA AUTORIZAR SOLICITUD
  ==============================================================*/
  $(".btnEstadoArchivo").click(function () {
    const estadoArchivo = $("#estadoArchivo").val();
    const archivoId = $("#archivoId").val();
    const proveedorId = $("#proveedorId").val();
    const observacionEstadoArchivo = $("#observacionEstadoArchivo").val();

    if (estadoArchivo === "RECHAZADO") {
      const observacion = $("#observacionEstadoArchivo").val();

      if (!observacion && !observacion.trim()) {
        Swal.fire({
          icon: "warning",
          title: "Observación requerida",
          text: "Por favor, indica el motivo del rechazo.",
          confirmButtonText: "Aceptar",
        });
        return;
      }
    }

    // Ocultar botones y mostrar mensaje de carga
    $("#botonesModalEstadoArchivo").addClass("d-none");
    $("#mensajeCargandoBotonesArchivos").removeClass("d-none");

    if (estadoArchivo === "RECHAZADO") {
      rechazarArchivo(archivoId, observacionEstadoArchivo, proveedorId);
    } else {
      autorizarArchivo(archivoId, observacionEstadoArchivo, proveedorId);
    }
  });

  // FUNCION AUTORIZAR ARCHIVO
  function autorizarArchivo(
    archivoId = null,
    observacionEstadoArchivo,
    proveedorId
  ) {
    if (archivoId == null) return;

    let dataSend = new FormData();
    dataSend.append("accion", "autorizarArchivo");
    dataSend.append("archivoId", archivoId);
    dataSend.append("proveedorId", proveedorId);
    dataSend.append("observacion", observacionEstadoArchivo);

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.respuestaMessage);
        } else {
          mostrarExito(respuesta.respuestaMessage, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  }

  // FUNCION AUTORIZAR ARCHIVO
  function rechazarArchivo(
    archivoId = null,
    observacionEstadoArchivo,
    proveedorId
  ) {
    if (archivoId == null) return;

    let dataSend = new FormData();
    dataSend.append("accion", "rechazarArchivo");
    dataSend.append("archivoId", archivoId);
    dataSend.append("proveedorId", proveedorId);
    dataSend.append("observacion", observacionEstadoArchivo);

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.respuestaMessage);
        } else {
          mostrarExito(respuesta.respuestaMessage, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  }

  // *************************************************
  // * REFERENTE A LOS ARCHIVOS DEL PERMISO
  // *************************************************

  // FUNCION AUTORIZAR PERMISO
  $(".autorizarPermiso").on("click", function () {
    const permisoId = $(this).attr("data-permiso-id");
    const btnEliminar = this; // importante mantener referencia

    Swal.fire({
      title:
        "¿Estás Seguro de querer autorizar este Permiso (ID: " +
        permisoId +
        ") ?",
      icon: "info",
      showCancelButton: true,
      confirmButtonText: "Sí, quiero autorizarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        let datos = new FormData();
        datos.append("accion", "autorizarPermiso");
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
              Swal.fire({
                title: "¡Éxito!",
                text: respuesta.respuestaMessage,
                icon: "success",
                confirmButtonText: "Ok",
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            } else {
              Swal.fire({
                title: "Advertencia",
                text: respuesta.errorMessage,
                icon: "warning",
                confirmButtonText: "Ok",
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            }
          },
          error: function () {
            Swal.fire({
              title: "Error",
              text: "Hubo un problema al procesar la solicitud.",
              icon: "error",
              confirmButtonText: "Cerrar",
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              }
            });
          },
        });
      }
    });
  });

  // FUNCION RECHAZAR PERMISO
  $(".rechazarPermiso").on("click", function () {
    const permisoId = $(this).attr("data-permiso-id");
    const btnEliminar = this; // importante mantener referencia

    Swal.fire({
      title:
        "¿Estás Seguro de querer rechazar este Permiso (ID: " +
        permisoId +
        ") ?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, quiero rechazarlo!",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        let datos = new FormData();
        datos.append("accion", "rechazarPermiso");
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
              Swal.fire({
                title: "¡Éxito!",
                text: respuesta.respuestaMessage,
                icon: "success",
                confirmButtonText: "Ok",
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            } else {
              Swal.fire({
                title: "Advertencia",
                text: respuesta.errorMessage,
                icon: "warning",
                confirmButtonText: "Ok",
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                }
              });
            }
          },
          error: function () {
            Swal.fire({
              title: "Error",
              text: "Hubo un problema al procesar la solicitud.",
              icon: "error",
              confirmButtonText: "Cerrar",
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              }
            });
          },
        });
      }
    });
  });
});
