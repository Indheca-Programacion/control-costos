$(function () {
  let tableList = document.getElementById("tablaOrdenes");
  let parametrosTableList = { responsive: true };

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fActualizarListado(
      rutaAjax + "/app/Ajax/OrdenCompraCentroServiciosAjax.php",
      "#tablaOrdenes",
      parametrosTableList
    );

  // Realiza la petición para actualizar el listado de obras
  function fActualizarListado(rutaAjax, idTabla, parametros = {}) {
    fetch(rutaAjax, {
      method: "GET", // *GET, POST, PUT, DELETE, etc.
      cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        $(idTabla)
          .DataTable({
            autoWidth: false,
            responsive:
              parametros.responsive === undefined
                ? true
                : parametros.responsive,
            data: data.datos.registros,
            columns: data.datos.columnas,

            createdRow: function (row, data, index) {
              if (data.colorTexto != "")
                $("td", row).eq(3).css("color", data.colorTexto);
              if (data.colorFondo != "")
                $("td", row).eq(3).css("background-color", data.colorFondo);
            },

            buttons: [
              { extend: "copy", text: "Copiar", className: "btn-info" },
              { extend: "csv", className: "btn-info" },
              { extend: "excel", className: "btn-info" },
              { extend: "pdf", className: "btn-info" },
              { extend: "print", text: "Imprimir", className: "btn-info" },
              {
                extend: "colvis",
                text: "Columnas visibles",
                className: "btn-info",
              },
            ],

            language: LENGUAJE_DT,
            aaSorting: [],
          })
          .buttons()
          .container()
          .appendTo(idTabla + "_wrapper .row:eq(0)"); // $(idTabla).DataTable({
      }); // .then( data => {
  } // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

  // Confirmar la eliminación de la Obra
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar esta Obra (Descripción: " +
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

  // $('#collapseFiltros').on('shown.bs.collapse', function (event) {
  $("#collapseFiltros").on("show.bs.collapse", function (event) {
    let btnVerFiltros = document.getElementById("btnVerFiltros");
    btnVerFiltros.querySelector("i").classList.remove("fa-eye");
    btnVerFiltros.querySelector("i").classList.add("fa-eye-slash");
  });

  // $('#collapseFiltros').on('hidden.bs.collapse', function (event) {
  $("#collapseFiltros").on("hide.bs.collapse", function (event) {
    let btnVerFiltros = document.getElementById("btnVerFiltros");
    btnVerFiltros.querySelector("i").classList.remove("fa-eye-slash");
    btnVerFiltros.querySelector("i").classList.add("fa-eye");
  });

  $("#btnFiltrar").on("click", function (e) {
    $(tableList).DataTable().destroy();
    tableList.querySelector("tbody").innerHTML = "";

    let estatusId = $("#filtroEstatusId").val();
    let obraId = $("#filtroObraId").val();
    let fechaInicial = $("#filtroFechaInicial").val();
    let fechaFinal = $("#filtroFechaFinal").val();

    if (fechaInicial == "") fechaInicial = 0;
    if (fechaFinal == "") fechaFinal = 0;

    fActualizarListado(
      `${rutaAjax}app/Ajax/OrdenCompraCentroServiciosAjax.php?estatusId=${estatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&obraId=${obraId}`,
      "#tablaOrdenes",
      parametrosTableList
    );
  });

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }
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

  //Date picker
  $(".input-group.date").datetimepicker({
    format: "DD/MMMM/YYYY",
  });

  $("#proveedorId").select2({
    width: "100%",
  });

  var proveedorId = $("#proveedorId").val();

  $("#proveedorId").on("change", function () {
    const proveedorId = $(this).val();
    cargarDatosBancarios(proveedorId);
  });

  // DATOS BANCARIOS INPUT
  var proveedorId = $("#proveedorId").val();
  var datoBancarioId = $("#datoBancarioInput").val();

  if (proveedorId) {
    $("#container-dato-bancario").removeClass("d-none");

    cargarDatosBancarios(proveedorId, datoBancarioId);
  } else {
    $("#container-dato-bancario").addClass("d-none");
  }

  function cargarDatosBancarios(proveedorId, datoBancarioId = null) {
    if (!proveedorId) {
      // Si no hay proveedorId, ocultamos el contenedor y limpiamos el select
      $("#container-dato-bancario").addClass("d-none");
      $("#datoBancarioId")
        .html('<option value="">Selecciona una opción</option>')
        .trigger("change");
      return;
    }

    fetch(
      rutaAjax +
        "app/Ajax/ProveedorAjax.php?accion=selectDatosBancarios&proveedorId=" +
        proveedorId,
      {
        method: "GET",
        cache: "no-cache",
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        $("#container-dato-bancario").removeClass("d-none");

        if (datoBancarioId) {
          let selectHTML = `<option value="">Selecciona una opción</option>`;
          data.datos.forEach((item) => {
            selectHTML += `<option value="${item.id}"${
              item.id == datoBancarioId ? " selected" : ""
            }>[${item.nombreBanco} - ${item.cuentaClave} - ${
              item.divisaCorto
            }]</option>`;
          });
          $("#datoBancarioId").html(selectHTML);

          // Reaplica Select2 si es necesario
          $("#datoBancarioId").select2({
            width: "100%",
          });
        } else {
          let selectHTML = `<option value="">Selecciona una opción</option>`;

          data.datos.forEach((item) => {
            selectHTML += `<option value="${item.id}">[${item.nombreBanco} - ${item.cuentaClave} - ${item.divisaCorto}]</option>`;
          });

          // Cambiar solo el contenido del select
          $("#datoBancarioId").html(selectHTML);

          // Inicializar o refrescar Select2 en el select
          $("#datoBancarioId").select2({
            width: "100%",
          });
        }
      });
  }

  //********* FUNCION PARA OBSERVACIONES EN LAS ORDENES DE COMPRA ********/

  // Habilitar observaciones al cambiar de estatus
  $("#estatusId").change(function () {
    let actualEstatusId = $("#actualServicioEstatusId").val();
    if (actualEstatusId === "") return;

    let observacion = document.getElementById("observacion");
    if (observacion === null) return;

    if (actualEstatusId == this.value) {
      // let observacion = document.getElementById('observacion');
      $(observacion).prop("disabled", true);
      observacion.parentElement.parentElement.parentElement.classList.add(
        "d-none"
      );
    } else {
      // let observacion = document.getElementById('observacion');
      if ($(observacion).prop("disabled")) {
        $(observacion).prop("disabled", false);
        observacion.parentElement.parentElement.parentElement.classList.remove(
          "d-none"
        );
      }
    }
  });

  /*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
  $(".verArchivo").on("click", function () {
    var archivoRuta = $(this).attr("archivoRuta");
    $("#pdfViewer").attr("src", archivoRuta);
    // Mostrar el modal
    $("#pdfModal").modal("show");
  });

  //==============================================================
  // Ver todos los archivos de la requisición
  //==============================================================
  $("#modalVerPDF").on("show.bs.modal", function () {
    // Limpiar el iframe antes de mostrar el modal
    $("#iframePDF").attr("src", "");
    let requisicionId = $('input[name="requisicionId"]').val();
    let ordenCompraId = $('input[name="ordenCompraId"]').val();

    // Abrir el modal para ver los archivos
    $("#modalVerArchivos").modal("show");

    // Cargar los archivos en la tabla
    $.ajax({
      url: `https://cm.atibernal.com/app/Ajax/OrdenCompraAjax.php?accion=verArchivos&requisicionId=${requisicionId}&ordenCompraId=${ordenCompraId}`,
      method: "GET",
      dataType: "json",
      success: function (data) {
        $("#iframePDF").attr("src", "https://cm.atibernal.com/" + data.ruta);
      },
      error: function (error) {
        console.error("Error al cargar los archivos:", error);
      },
    });
  });

});
