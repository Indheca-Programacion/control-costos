$(function () {
  let tablaQrCargas = document.getElementById("tablaQrCargas");

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tablaQrCargas != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/QrCargaAjax.php", "#tablaQrCargas");

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }

  $(tablaQrCargas).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title: "¿Estás Seguro de querer eliminar este QR (Id: " + folio + ") ?",
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

  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
    width: "100%",
    // ,theme: 'bootstrap4'
  });

  $("#tipo").bootstrapSwitch({
    onText: "Entrada",
    offText: "Salida",
  });

  $(".select2Add").select2({
    tags: true,
    // ,theme: 'bootstrap4'
  });

  $("[name='owner']")
    .bootstrapSwitch({
      onText: "Propio",
      offText: "Externo",
    })
    .on("switchChange.bootstrapSwitch", function (event, state) {
      let divMaquinaria = document.getElementById("divMaquinaria");
      if (divMaquinaria) {
        if (state) {
          divMaquinaria.classList.remove("d-none");
        } else {
          divMaquinaria.classList.add("d-none");
        }
      }
    });

  let agregandoCatalogo = false;

  // BOTONES DROPDOWN Y AGREGAR
  let campoOperadorId = document.getElementById("operadorId");
  let campoMaterialId = document.getElementById("materialId");

  $(campoOperadorId).on("change", function (e) {
    if (agregandoCatalogo) return;
    let atributo = campoOperadorId
      .querySelector('option[value="' + this.value + '"]')
      .getAttribute("data-select2-tag");
    if (atributo) $("#btnAddOperadorId").removeAttr("disabled");
    else $("#btnAddOperadorId").attr("disabled", "disabled");
  });

  $(campoMaterialId).on("change", function (e) {
    if (agregandoCatalogo) return;
    let atributo = campoMaterialId
      .querySelector('option[value="' + this.value + '"]')
      .getAttribute("data-select2-tag");
    if (atributo) $("#btnAddMaterialId").removeAttr("disabled");
    else $("#btnAddMaterialId").attr("disabled", "disabled");
  });

  $("#btnAddOperadorId").on("click", function (e) {
    $("#btnAddOperadorId").attr("disabled", "disabled");
    $(campoOperadorId).attr("disabled", "disabled");

    agregandoCatalogo = true;
    ajaxEnviar(
      campoOperadorId,
      "operadorMaquinaria",
      rutaAjax + "app/Ajax/OperadorMaquinariaAjax.php"
    );
  });

  $("#btnAddMaterialId").on("click", function (e) {
    $("#btnAddMaterialId").attr("disabled", "disabled");
    $(campoMaterialId).attr("disabled", "disabled");

    agregandoCatalogo = true;

    ajaxEnviar(
      campoMaterialId,
      "materialCarga",
      rutaAjax + "app/Ajax/MaterialCargaAjax.php"
    );
  });

  // FUNCION PARA CREAR

  function ajaxEnviar(objetoCampo, nombreCampoPost, rutaUrl) {
    let token = $('input[name="_token"]').val();
    var nombreValor = objetoCampo.value;

    let datos = new FormData();
    datos.append("_token", token);
    // Agregar el valor de la MarcaId
    datos.append(nombreCampoPost, nombreValor);

    $.ajax({
      url: rutaUrl,
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        // Si la respuesta es positiva pudo grabar el nuevo registro
        if (respuesta.respuesta) {
          let respuestaId = respuesta.respuesta;

          $(objetoCampo)
            .parent()
            .after(
              '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.respuestaMessage +
                "</div>"
            );

          let lastOption = objetoCampo.lastChild;
          $(lastOption).after(
            '<option value="' +
              respuestaId +
              '" selected>' +
              nombreValor +
              "</option>"
          );
        } else {
          $(objetoCampo)
            .parent()
            .after(
              '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.errorMessage +
                "</div>"
            );

          $(objetoCampo).val(null).trigger("change");
        }

        setTimeout(function () {
          $(".alert").remove();
        }, 5000);

        $(objetoCampo).removeAttr("disabled");

        agregandoCatalogo = false;
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        $(objetoCampo)
          .parent()
          .after(
            '<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>Hubo un error al intentar grabar el registro, intente de nuevo.</div>'
          );

        $(objetoCampo).val(null).trigger("change");

        setTimeout(function () {
          $(".alert").remove();
        }, 5000);

        $(objetoCampo).removeAttr("disabled");

        agregandoCatalogo = false;
      },
    });
  }

  // FUNCION SUBIR ARCHIVOS
  $("#inputFiles").on("change", function (e) {
    let files = $.map(this.files, (file) => file.name).join(", ");
    $(this)
      .next()
      .text(files || "Seleccionar archivos");
  });

  // MODAL REGISTRO CARGA
  $("#modalRegistrarCarga #btnRegistrarCarga").on("click", function () {
    let _token = $('input[name="_token"]').val();
    let idObra = $("#idObra").val();
    let idMaterial = $("#materialId").val();
    let nPeso = $("#nPeso").val();
    let dFechaHora = $("#dFechaHora").val();
    let sFolio = $("#sFolio").val();
    let idMaquinaria = $("#idMaquinaria").val();
    let idCarga = $("#idCarga").val() || "";
    let idQrCarga = $("#idQrCarga").val();
    let idUbicacion = $("#idUbicacion").val();

    let archivo = $("#inputFiles")[0].files[0];

    // VALIDACIONES
    if (idObra == "" || idObra == 0) {
      crearToast("bg-danger", "error", "", "Se debe seleccionar una obra");
      return;
    }

    if (idUbicacion == "" || idUbicacion == 0) {
      crearToast("bg-danger", "error", "", "Se debe seleccionar una ubicación");
      return;
    }
    if (idMaterial == "" || idMaterial == 0) {
      crearToast("bg-danger", "error", "", "Se debe seleccionar un material");
      return;
    }
    if (nPeso == "" || nPeso == 0) {
      crearToast("bg-danger", "error", "", "Se debe ingresar un peso");
      return;
    }
    if (dFechaHora == "" || dFechaHora == 0) {
      crearToast(
        "bg-danger",
        "error",
        "",
        "Se debe seleccionar una Fecha Y Hora"
      );
      return;
    }
    if (sFolio == "" || sFolio == 0) {
      crearToast("bg-danger", "error", "", "Se debe ingresar un folio");
      return;
    }
    if (!archivo) {
      crearToast("bg-danger", "Error", "", "Debe seleccionar un archivo.");
      return;
    }

    let dataSend = new FormData();

    dataSend.append("_token", _token);
    dataSend.append("idObra", idObra);
    dataSend.append("idMaterial", idMaterial);
    dataSend.append("nPeso", nPeso);
    dataSend.append("dFechaHora", dFechaHora);
    dataSend.append("sFolio", sFolio);
    dataSend.append("idMaquinaria", idMaquinaria);
    dataSend.append("idQrCarga", idQrCarga);
    dataSend.append("idCarga", idCarga);
    dataSend.append("idUbicacion", idUbicacion);

    dataSend.append("archivo", archivo);
    dataSend.append("accion", "registroCarga");

    $.ajax({
      url: rutaAjax + "app/Ajax/QrCargaAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    }).done(function (respuesta) {
      if (respuesta.error) {
        crearToast(
          "bg-danger",
          "Registrar Carga",
          " Error",
          respuesta.errorMessage
        );
        return;
      }

      crearToast(
        "bg-success",
        "Registrar Carga",
        "OK",
        respuesta.respuestaMessage
      );

      setTimeout(function () {
        window.location.reload();
      }, 400);
    });
  });

  // MODAL VER QR
  $("#qrModal").on("show.bs.modal", function (event) {
    let button = $(event.relatedTarget); // Botón que activó el modal
    let qrData = button.data("qr"); // Obtener el data-qr del botón

    let modal = $(this);
    let imgQr = modal.find("#imgQr"); // Seleccionar la imagen dentro del modal

    // Realizar el AJAX para obtener el QR
    $.ajax({
      url: rutaAjax + "app/Ajax/QrCargaAjax.php",
      method: "POST",
      data: {
        qr: qrData,
        accion: "obtenerQr",
      },
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.error) {
          crearToast("bg-danger", "Error", "", respuesta.errorMessage);
          return;
        }

        // Actualizar la imagen en el modal con el QR recibido
        imgQr.attr("src", respuesta.qrUrl);
      },
      error: function () {
        crearToast(
          "bg-danger",
          "Error",
          "",
          "Hubo un problema al obtener el QR."
        );
      },
    });
  });

  //
  $("#btnRegistrarMovimiento").on("click", function () {
    let token = $('input[name="_token"]').val();
    let idMaquinaria = $("#idMaquinaria").val();
    let obraId = $("#obraId").val();
    let tipo = $("#tipo").is(":checked") ? 1 : 0;
    let cargado = $("#cargado").val();
    let idCarga = $("#idCarga").val();

    let dataSend = new FormData();

    dataSend.append("_token", token);
    dataSend.append("nIdMaquinariaTraslado02MaquinariaTraslado", idMaquinaria);
    dataSend.append("nIdObra", obraId);
    dataSend.append("nTipo", tipo);
    dataSend.append("nEstatus", cargado);
    dataSend.append("idCarga", idCarga);

    dataSend.append("accion", "registroMovimiento");

    // AGREGAR VALIDACION
    if (obraId == "" || obraId == 0) {
      crearToast("bg-danger", "error", "", "Se debe seleccionar una obra");
      return;
    } else if (cargado == "") {
      crearToast(
        "bg-danger",
        "error",
        "",
        "Se debe seleccionar el estatus del movimiento"
      );
      return;
    }

    $.ajax({
      url: rutaAjax + "app/Ajax/MovimientoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    }).done(function (respuesta) {
      if (respuesta.error) {
        crearToast(
          "bg-danger",
          "Crear Resguardo",
          "Error",
          respuesta.errorMessage
        );
        return;
      }

      crearToast(
        "bg-success",
        "Crear Resguardo",
        "OK",
        respuesta.respuestaMessage
      );
      window.location.href = respuesta.ruta;
    });
  });

  $("#btnDarBaja").on("click", function (e) {
    let token = $('input[name="_token"]').val();
    let idQrCarga = $("#idQrCarga").val();
    let idCarga = $("#idCarga").val();
    let idMaquinaria;

    if ($("#idMaquinaria")) {
      idMaquinaria = $("#idMaquinaria").val();
    }

    dataSend = new FormData();
    dataSend.append("_token", token);
    dataSend.append("idCarga", idCarga);
    dataSend.append("idQrCarga", idQrCarga);

    dataSend.append("idMaquinaria", idMaquinaria);

    dataSend.append("accion", "darBajaQr");

    Swal.fire({
      title: "Deseas dar de baja el equipo del QR?",
      text: "No se podran recuperar los datos!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, dar de baja",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: rutaAjax + "app/Ajax/QrCargaAjax.php",
          method: "POST",
          data: dataSend,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
        }).done(function (respuesta) {
          if (respuesta.error) {
            crearToast(
              "bg-danger",
              "Baja Correctamente",
              "Error",
              respuesta.errorMessage
            );
            return;
          }
          crearToast(
            "bg-success",
            "Baja Correctamente",
            "OK",
            respuesta.respuestaMessage
          );
          window.location.reload();
        });
      }
    });
  });

  $("#sPlaca").on("blur", function (e) {
    let placa = $(this).val();
    if (placa.trim() === "") return;

    let token = $('input[name="_token"]').val();

    $.ajax({
      url: rutaAjax + "app/Ajax/QrCargaAjax.php",
      method: "POST",
      data: {
        _token: token,
        placa: placa,
        accion: "buscarPlaca",
      },
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.error) {
          return;
        }

        respuesta = respuesta.respuesta;
        let divMaquinaria = document.getElementById("divMaquinaria");
        // Llenar otros inputs con los valores obtenidos

        $("#idMaquinaria").val(respuesta.nId02MaquinariaTraslado || "");

        $("#sPlaca").val(respuesta.sPlaca || "");
        $("#sMarca").val(respuesta.sMarca || "");
        $("#sModelo").val(respuesta.sModelo || "");
        $("#sYear").val(respuesta.sYear || "");
        $("#sCapacidad").val(respuesta.sCapacidad || "");
        $("#sNumeroEconomico").val(respuesta.sNumeroEconomico || "");

        if (respuesta.sNumeroEconomico == null) {
          $("#owner")
            .prop("checked", false)
            .bootstrapSwitch("state", false, true);
          divMaquinaria.classList.add("d-none");
        } else {
          $("#owner")
            .prop("checked", true)
            .bootstrapSwitch("state", true, true);
          divMaquinaria.classList.remove("d-none");
        }
        crearToast(
          "bg-success",
          "Placa Encontrada",
          "",
          "Se ha encontrado un registro previo con la placa: " +
            respuesta.sPlaca
        );
      },
      error: function () {
        crearToast(
          "bg-danger",
          "Error",
          "",
          "Hubo un problema al buscar la placa."
        );
      },
    });
  });

  /*==============================================================
	Abrir el input al presionar el botón Evidencias de la Carga
	==============================================================*/
  $("#btnSubirEvidencia").click(function () {
    document.getElementById("evidenciaArchivos").click();
  });

  /*========================================================
 	Validar tipo y tamaño de los archivos Evicencias de la Carga
 	========================================================*/
  $("#evidenciaArchivos").change(function () {
    // $("div.subir-comprobantes span.lista-archivos").html('');
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      /*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/

      if (!archivo["type"].startsWith("image/")) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser IMG!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      }
    }

    if (error) {
      $("#evidenciaArchivos").val("");

      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      $("div.subir-evidencias span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "evidenciaArchivos[]";
    $("div.subir-evidencias").append(cloneElementArchivos);
  }); // $("#comprobanteArchivos").change(function(){

  /*==============================================================
	Abrir el input al presionar el botón Verificación de la Carga
	==============================================================*/
  $("#btnSubirVerificacion").click(function () {
    document.getElementById("verificacionArchivos").click();
  });

  /*========================================================
	Validar tipo y tamaño de los archivos Verificación de la Carga
	========================================================*/
  $("#verificacionArchivos").change(function () {
    // $("div.subir-comprobantes span.lista-archivos").html('');
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      /*==========================================
		VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF Y IMG
		==========================================*/

      if (
        archivo["type"] !== "application/pdf" &&
        !archivo["type"].startsWith("image/")
      ) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser IMG O PDF!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      }
    }

    if (error) {
      $("#verificacionArchivos").val("");

      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      $("div.subir-verificacion span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "verificacionArchivos[]";
    $("div.subir-verificacion").append(cloneElementArchivos);
  }); // $("#comprobanteArchivos").change(function(){

  /*==============================================================
	Abrir el input al presionar el botón Verificación de la Carga
	==============================================================*/
  $("#btnSubirAcuerdo").click(function () {
    document.getElementById("acuerdoArchivos").click();
  });

  /*========================================================
	Validar tipo y tamaño de los archivos Verificación de la Carga
	========================================================*/
  $("#acuerdoArchivos").change(function () {
    // $("div.subir-comprobantes span.lista-archivos").html('');
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      /*==========================================
					VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
					==========================================*/

      if (archivo["type"] !== "application/pdf") {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      }
    }

    if (error) {
      $("#acuerdoArchivos").val("");

      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      $("div.subir-acuerdo span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "acuerdoArchivos[]";
    $("div.subir-acuerdo").append(cloneElementArchivos);
  }); // $("#comprobanteArchivos").change(function(){

  /*==============================================================
	Abrir el input al presionar el botón Verificación de la Carga
	==============================================================*/
  $("#btnSubirTarjetaCirculacion").click(function () {
    document.getElementById("tarjetaCirculacionArchivos").click();
  });

  /*========================================================
		Validar tipo y tamaño de los archivos Verificación de la Carga
		========================================================*/
  $("#tarjetaCirculacionArchivos").change(function () {
    // $("div.subir-comprobantes span.lista-archivos").html('');
    let archivos = this.files;
    if (archivos.length == 0) return;

    let error = false;

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      /*==========================================
		VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF Y IMG
		==========================================*/

      if (
        archivo["type"] !== "application/pdf" &&
        !archivo["type"].startsWith("image/")
      ) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser PDF O IMG!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      } else if (archivo["size"] > 4000000) {
        error = true;

        // $("#comprobanteArchivos").val("");
        // $("div.subir-comprobantes span.lista-archivos").html('');

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      }
    }

    if (error) {
      $("#tarjetaCirculacionArchivos").val("");
      return;
    }

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      $("div.subir-tarjeta-circulacion span.lista-archivos").append(
        '<p class="font-italic text-info mb-0">' + archivo["name"] + "</p>"
      );
    }

    let cloneElementArchivos = this.cloneNode(true);
    cloneElementArchivos.removeAttribute("id");
    cloneElementArchivos.name = "tarjetaCirculacionArchivos[]";
    $("div.subir-tarjeta-circulacion").append(cloneElementArchivos);
  }); // $("#comprobanteArchivos").change(function(){

  /*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
  $(".verArchivo").on("click", function () {
    var archivoRuta = $(this).attr("archivoRuta");
    $("#pdfViewer").attr("src", archivoRuta);
    // Mostrar el modal
    $("#pdfModal").modal("show");
  });

  // Al cerrar el modal, limpia el src del iframe
  $("#pdfModal").on("hidden.bs.modal", function () {
    $("#pdfViewer").attr("src", "");
  });

  // Confirmar la eliminación de los Archivos
  $(
    "div.subir-evidencias,div.subir-tarjeta-circulacion,div.subir-acuerdo,div.subir-verificacion"
  ).on("click", "i.eliminarArchivo", function (e) {
    let btnEliminar = this;
    // let archivoId = $(this).attr("archivoId");
    let folio = $(this).attr("folio");

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
        eliminarArchivo(btnEliminar);
      }
    });
  });

  // Envio del formulario para Cancelar el registro
  function eliminarArchivo(btnEliminar = null) {
    if (btnEliminar == null) return;

    let archivoId = $(btnEliminar).attr("archivoId");

    // $(btnEliminar).prop('disabled', true);

    let token = $('input[name="_token"]').val();
    let idMaquinaria = $("input#idMaquinaria").val();

    let datos = new FormData();
    datos.append("_token", token);
    datos.append("accion", "eliminarArchivo");
    datos.append("archivoId", archivoId);
    datos.append("idMaquinaria", idMaquinaria);

    $.ajax({
      url: rutaAjax + "app/Ajax/QrCargaAjax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        // Si la respuesta es positiva pudo eliminar el archivo
        if (respuesta.respuesta) {
          $(btnEliminar)
            .parent()
            .after(
              '<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.respuestaMessage +
                "</div>"
            );

          $(btnEliminar).parent().remove();
        } else {
          $(btnEliminar)
            .parent()
            .after(
              '<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>' +
                respuesta.errorMessage +
                "</div>"
            );

          // $(btnEliminar).prop('disabled', false);
        }

        setTimeout(function () {
          $(".alert").remove();
        }, 5000);
      },
    });
  }
});
