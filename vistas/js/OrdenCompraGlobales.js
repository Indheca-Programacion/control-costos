$(function () {
  let tableRequisiciones = document.getElementById("tablaRequisiciones");
  let tablePartidas = document.getElementById("tablaPartidas");
  let tableOrdenCompraGlobal = document.getElementById("tablaOrdenes");

  let tablePartidasDetalles = document.getElementById("tablaPartidasDetalles");
  let tableRequisicionesDetalles = document.getElementById(
    "tablaRequisicionesDetalles"
  );

  let dataTableRequisiciones = null;
  let tablaPartidas = null; // ahora es global

  let selectedRequisitions = [];
  let partidasSeleccionadas = [];
  let partidasSeleccionadasActuales = [];

  const parametrosTableList = { responsive: true };
  const TIEMPO_DESCARGA = 350;

  // TABLA ORDENES DE COMPRA GLOBAL
  if (tableOrdenCompraGlobal != null)
    fActualizarListadoOrdenCompraGlobal(
      rutaAjax + "app/Ajax/OrdenCompraGlobalesAjax.php",
      "#tablaOrdenes",
      parametrosTableList
    );

  // CONFIRMACIÓN PARA ELIMINAR REGISTRO ORDEN DE COMPRA
  $(tableOrdenCompraGlobal).on("click", "button.eliminar", function (e) {
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

  // Realiza la petición para actualizar el listado de ORDENES DE COMPRA
  function fActualizarListadoOrdenCompraGlobal(
    rutaAjax,
    idTabla,
    parametros = {}
  ) {
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

  // TABLA REQUISICIONES
  if (tableRequisiciones != null) {
    fActualizarRequisiciones(
      rutaAjax +
        "app/Ajax/RequisicionAjax.php?accion=tablaRequisicionOrdenCompraGlobal",
      "#tablaRequisiciones",
      parametrosTableList
    );
  }

  // 🔁 Función para actualizar la tabla de partidas
  function actualizarTablaPartidas() {
    if (tablaPartidas) {
      tablaPartidas.clear().rows.add(partidasSeleccionadas).draw();
    }
  }

  function fActualizarRequisiciones(
    rutaAjaxTabla,
    idTabla,
    parametrosTableList = {}
  ) {
    fetch(rutaAjaxTabla, {
      method: "GET",
      cache: "no-cache",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        if ($.fn.DataTable.isDataTable(idTabla)) {
          $(idTabla).DataTable().destroy();
          $(idTabla).empty();
        }

        const columnasConCheckbox = [
          {
            title: `<input type="checkbox" id="checkAll" />`,
            data: null,
            orderable: false,
            searchable: false,
            render: () => `<input type="checkbox" class="chk-requisicion" />`,
          },
          ...data.datos.columnas,
        ];

        dataTableRequisiciones = $(idTabla).DataTable({
          data: data.datos.registros,
          columns: columnasConCheckbox,
          scrollX: true,
          language: LENGUAJE_DT,
          aaSorting: [],
        });

        function agregarSeleccionado(rowData) {
          if (
            !selectedRequisitions.some(
              (item) => JSON.stringify(item) === JSON.stringify(rowData)
            )
          ) {
            selectedRequisitions.push(rowData);
          }
        }

        function eliminarSeleccionado(rowData) {
          selectedRequisitions = selectedRequisitions.filter(
            (item) => JSON.stringify(item) !== JSON.stringify(rowData)
          );
        }

        // ✔ Checkbox principal
        $(document)
          .off("change", "#checkAll")
          .on("change", "#checkAll", function () {
            const checked = this.checked;
            $(`${idTabla} tbody input.chk-requisicion`).each(function () {
              $(this).prop("checked", checked).trigger("change");
            });
          });

        // ✔ Checkbox individual
        $(document)
          .off("change", `${idTabla} .chk-requisicion`)
          .on("change", `${idTabla} .chk-requisicion`, function () {
            const fila = $(this).closest("tr");
            const rowData = dataTableRequisiciones.row(fila).data();
            const isChecked = this.checked;

            if (!isChecked) {
              eliminarSeleccionado(rowData);
              partidasSeleccionadas = partidasSeleccionadas.filter(
                (p) => p.requisicionId != rowData.id
              );

              actualizarTablaPartidas();
              return;
            }

            agregarSeleccionado(rowData);

            $.ajax({
              url:
                rutaAjax +
                `app/Ajax/RequisicionAjax.php?accion=partidaPorRequisicion&requisicionId=${rowData.id}`,
              method: "GET",
              dataType: "json",
              cache: false,
            })
              .done(function (respuesta) {
                if (
                  respuesta.codigo === 200 &&
                  respuesta.datos.registros.length > 0
                ) {
                  const nuevasPartidas = respuesta.datos.registros.map((p) => ({
                    ...p,
                    requisicionId: rowData.id,
                  }));

                  partidasSeleccionadas =
                    partidasSeleccionadas.concat(nuevasPartidas);

                  actualizarTablaPartidas();
                }
              })
              .fail(function (error) {
                console.error("Error al obtener partidas:", error);
              });
          });
      });
  }

  // TABLA PARTIDAS
  if (tablePartidas) {
    tablaPartidas = $("#tablaPartidas").DataTable({
      data: [],
      columns: [
        {
          title: `<input type="checkbox" id="checkAllPartidas" />`,
          data: null,
          orderable: false,
          searchable: false,
          className: "text-left",
          render: () => `<input type="checkbox" class="chk-partida" />`,
        },
        {
          data: "cantidad",
          title: "Cantidad",
          className: "text-left",
          render: function (data, type, row, meta) {
            // Siempre escapa el valor para evitar problemas con caracteres especiales
            var valorSeguro = data !== undefined && data !== null ? data : 0;
            return `<input type="number" class="form-control input-cantidad" value="${valorSeguro}" min="0" step="any" />`;
          },
        },
        {
          data: "descripcion",
          title: "Descripción",
          className: "text-left",
        },
        {
          data: "valorUnitario",
          title: "Valor Unitario",
          className: "text-left",
          render: function (data, type, row) {
            return `<input type="number" class="form-control input-valor-unitario" value="${data}" min="0" step="any" />`;
          },
        },
        {
          data: "importe",
          title: "Importe",
          className: "text-left",
          render: function (data, type, row) {
            return `<input type="number" class="form-control input-importe" value="${data}" readonly />`;
          },
        },
      ],
      scrollX: true,
      language: LENGUAJE_DT,
    });

    // Checkbox principal para seleccionar/deseleccionar todas las partidas
    $(document)
      .off("change", "#checkAllPartidas")
      .on("change", "#checkAllPartidas", function () {
        const checked = this.checked;
        $("#tablaPartidas tbody input.chk-partida").each(function () {
          $(this).prop("checked", checked).trigger("change");
        });
      });

    // Checkbox individual para partidas
    $(document)
      .off("change", "#tablaPartidas .chk-partida")
      .on("change", "#tablaPartidas .chk-partida", function () {
        const fila = $(this).closest("tr");
        const rowData = tablaPartidas.row(fila).data();
        const isChecked = this.checked;

        // ✅ Actualizar la variable global con todos los que están seleccionados
        partidasSeleccionadasActuales = [];
        $("#tablaPartidas tbody input.chk-partida:checked").each(function () {
          const fila = $(this).closest("tr");
          const data = tablaPartidas.row(fila).data();
          partidasSeleccionadasActuales.push(data);
        });

        const detalles = {
          partidas: partidasSeleccionadasActuales,
          requisiciones: selectedRequisitions,
        };

        document.getElementById("detalles").value = JSON.stringify(detalles);

        // ✅ Actualizar el total al seleccionar o deseleccionar
        actualizarImporteControl();
      });
  }

  // TABLA DETALLES REQUISICIONES DETALLES
  if (tableRequisicionesDetalles != null) {
    const ordenCompraId = $("#ordenCompraId").val();

    fActualizarRequisicionesDetalles(
      rutaAjax +
        "app/Ajax/OrdenCompraGlobalesAjax.php?accion=tablaRequisicionDetallesOrdenCompraGlobal&ordenCompraId=" +
        ordenCompraId,
      "#tablaRequisicionesDetalles",
      parametrosTableList
    );
  }

  function fActualizarRequisicionesDetalles(
    rutaAjaxTabla,
    idTabla,
    parametrosTableList = {}
  ) {
    fetch(rutaAjaxTabla, {
      method: "GET",
      cache: "no-cache",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        if ($.fn.DataTable.isDataTable(idTabla)) {
          $(idTabla).DataTable().destroy();
          $(idTabla).empty();
        }

        dataTableRequisiciones = $(idTabla).DataTable({
          data: data.datos.registros,
          columns: data.datos.columnas,
          scrollX: true,
          paging: false, // Desactiva la paginación
          searching: false, // Oculta el input de búsqueda
          info: false, // Oculta el texto de información tipo "Mostrando X de Y"
          language: LENGUAJE_DT, // Lenguaje personalizado
          dom: "t",
        });
      });
  }

  // TABLA DETALLES REQUISICIONES DETALLES
  if (tablePartidasDetalles != null) {
    const ordenCompraId = $("#ordenCompraId").val();
    fActualizarPartidasDetalles(
      rutaAjax +
        "app/Ajax/OrdenCompraGlobalesAjax.php?accion=tablaPartidasDetallesOrdenCompraGlobal&ordenCompraId=" +
        ordenCompraId,
      "#tablaPartidasDetalles",
      parametrosTableList
    );
  }

  function fActualizarPartidasDetalles(
    rutaAjaxTabla,
    idTabla,
    parametrosTableList = {}
  ) {
    fetch(rutaAjaxTabla, {
      method: "GET",
      cache: "no-cache",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        if ($.fn.DataTable.isDataTable(idTabla)) {
          $(idTabla).DataTable().destroy();
          $(idTabla).empty();
        }

        dataTableRequisiciones = $(idTabla).DataTable({
          data: data.datos.registros,
          columns: data.datos.columnas,
          columnDefs: [
            {
              targets: "_all",
              className: "text-left", // Aplica a todas las columnas
            },
          ],
          scrollX: true,
          paging: false,
          searching: false,
          info: false,
          language: LENGUAJE_DT,
          dom: "t",
        });
      });
  }

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

    let empresaId = $("#filtroEmpresaId").val();
    let estatusId = $("#filtroEstatusId").val();
    let obraId = $("#filtroObraId").val();
    let fechaInicial = $("#filtroFechaInicial").val();
    let fechaFinal = $("#filtroFechaFinal").val();
    let concepto = $("#filtroConcepto").val();

    if (fechaInicial == "") fechaInicial = 0;
    if (fechaFinal == "") fechaFinal = 0;

    fActualizarListado(
      `${rutaAjax}app/Ajax/RequisicionAjax.php?empresaId=${empresaId}&estatusId=${estatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&obraId=${obraId}&concepto=${concepto}`,
      "#tablaRequisiciones",
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
});
// Habilitar observaciones al cambiar de estatus
$("#estatusId").change(function () {
  let actualEstatusId = $("#actualEstatusId").val();
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
}); // $("#servicioEstatusId").change(function(){

/* 
	FUNCIÓN ENCARGADA AL SELECCIONAR UN PROOVEDOR 
	 MUESTRE LA LISTA DE LOS DATOS BANCARIOS
	 */

$("#proveedorId").select2({
  width: "100%",
});

var proveedorId = $("#proveedorId").val();

$("#proveedorId").on("change", function () {
  const proveedorId = $(this).val();
  cargarDatosBancarios(proveedorId);
});

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
        let resultado = data.datos.find((item) => item.id == datoBancarioId);

        if (resultado) {
          let selectHTML = `<option value="${resultado.id}">[${resultado.nombreBanco} - ${resultado.cuentaClave}]</option>`;
          $("#datoBancarioId").html(selectHTML);

          // Reaplica Select2 si es necesario
          $("#datoBancarioId").select2({
            width: "100%",
          });
        } else {
          console.log("No se encontró el dato bancario con ese ID");
        }
      } else {
        let selectHTML = `<option value="">Selecciona una opción</option>`;

        data.datos.forEach((item) => {
          selectHTML += `<option value="${item.id}">[${item.nombreBanco} - ${item.cuentaClave}]</option>`;
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

$("#proveedorId").on("change", function () {
  let proveedorId = $(this).val();
  let addBancario = document.getElementById("addBancario");
  if (addBancario) {
    // Actualiza el href con el proveedorId seleccionado
    addBancario.href = rutaAjax + "proveedores/" + proveedorId + "/editar";
  }
});

/*=============================================
	ACTUALIZAR IMPORTE Y TOTAL
	=============================================*/
var campoImporteControl = document.getElementById("total");

$(".detalleOrden").on(
  "change",
  "input[name='detalles[cantidad][]']",
  function (e) {
    var cantidad = $(this).val();
    cantidad = parseFloat(cantidad);

    var valorUnitario = $(this)
      .parent()
      .parent()
      .parent()
      .find("input[name='detalles[importeUnitario][]']")
      .val();
    valorUnitario = valorUnitario.replace(/,/g, "");
    valorUnitario = parseFloat(valorUnitario);

    var importe = $(this).parent().parent().parent().find("span.importe");
    importe.html(number_format(cantidad * valorUnitario, 2));

    actualizarImporteControl();
  }
);

$("#tablaPartidas tbody").on(
  "change",
  "input.input-valor-unitario",
  function (e) {
    var cantidad = $(this)
      .parent()
      .parent()
      .parent()
      .find("input.input-cantidad")
      .val();
    cantidad = parseFloat(cantidad);

    var valorUnitario = $(this).val();
    valorUnitario = valorUnitario.replace(/,/g, "");
    valorUnitario = parseFloat(valorUnitario);

    var importe = $(this).parent().parent().find("input.input-importe");
    importe.val(cantidad * valorUnitario);

    actualizarImporteControl();
  }
);

// CHANGE INPUTS TABLA REQUICIONES TOTAL
$("#tablaPartidas tbody").on("change", "input.input-cantidad", function (e) {
  var cantidad = $(this).val();
  cantidad = parseFloat(cantidad);

  var valorUnitario = $(this)
    .parent()
    .parent()
    .parent()
    .find("input.input-valor-unitario")
    .val();
  valorUnitario = valorUnitario.replace(/,/g, "");
  valorUnitario = parseFloat(valorUnitario);

  var importe = $(this).parent().parent().find("input.input-importe");
  importe.val(cantidad * valorUnitario);

  actualizarImporteControl();
});

function actualizarImporteControl() {
  let importeControl = 0;

  // Solo sumamos los importes de filas seleccionadas
  $("#tablaPartidas tbody tr").each(function () {
    const $fila = $(this);
    const checked = $fila.find(".chk-partida").is(":checked");

    if (checked) {
      let importe = $fila.find(".input-importe").val();
      importe = importe.replace(/,/g, "");
      importe = parseFloat(importe) || 0;
      importeControl += importe;
    }
  });

  const subtotal = document.getElementById("subtotal");
  const iva = document.getElementById("iva");
  const descuento = document.getElementById("descuento");
  const retencionIva = document.getElementById("retencionIva");
  const retencionIsr = document.getElementById("retencionIsr");
  const campoImporteControl = document.getElementById("total"); // Asumimos que este es tu input de total final

  // Subtotal es la suma de los importes seleccionados
  subtotal.value = importeControl.toFixed(2);

  // Descuento (si existe)
  let descuentoValor = 0;
  if (descuento && descuento.value !== "") {
    descuentoValor = parseFloat(descuento.value.replace(/,/g, "")) || 0;
  }

  // IVA (16% sobre subtotal - descuento)
  let baseIva = importeControl - descuentoValor;

  let ivaValor = parseFloat(iva.value.replace(/,/g, "")) || 0;
  let retencionIvaValor = parseFloat(retencionIva.value.replace(/,/g, "")) || 0;
  let retencionIsrValor = parseFloat(retencionIsr.value.replace(/,/g, "")) || 0;

  // Total = base + iva - retenciones
  let total = baseIva + ivaValor - retencionIvaValor - retencionIsrValor;

  campoImporteControl.value = total.toFixed(2);
}

$("#iva, #descuento, #retencionIva, #retencionIsr").on("change", function (e) {
  actualizarImporteControl();
});

/*
FUNCION CALCULAR PORCENTAJE
*/
$("#ivaPorcentaje").on("input change", function () {
  // Actualizar el valor del IVA al cambiar el porcentaje
  calcularIVA();
});

function calcularIVA() {
  const subtotal = parseFloat($("#subtotal").val()) || 0;
  const porcentaje = parseFloat($("#ivaPorcentaje").val()) || 0;
  const iva = subtotal * (porcentaje / 100);
  $("#iva").val(iva);
  actualizarImporteControl();
}

/*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
$(".verArchivo").on("click", function () {
  var archivoRuta = $(this).attr("data-ruta");
  console.log(archivoRuta);
  $("#pdfViewer").attr("src", archivoRuta);
  // Mostrar el modal
  $("#pdfModal").modal("show");
});

//==============================================================
// Ver todos los archivos de la requisición
//==============================================================

let rutaTodosArchivos = "";

$("#modalVerPDF").on("show.bs.modal", function () {
  // Limpiar el iframe antes de mostrar el modal
  $("#iframePDF").attr("src", "");
  let requisicionId = $('input[name="requisicionIds"]').val();
  let ordenCompraId = $('input[name="ordenCompraId"]').val();


  // Abrir el modal para ver los archivos
  $("#modalVerArchivos").modal("show");

  // Cargar los archivos en la tabla
  $.ajax({
    url: `${rutaAjax}app/Ajax/OrdenCompraGlobalesAjax.php?accion=verArchivos&requisicionId=${requisicionId}&ordenCompraId=${ordenCompraId}`,
    method: "GET",
    dataType: "json",
    success: function (data) {
      $("#iframePDF").attr("src", data.ruta);
      rutaTodosArchivos = data.ruta;
    },
    error: function (error) {
      console.error("Error al cargar los archivos:", error);
    },
  });
});

// Mostrar la variable cuando se cierre el modal
$("#modalVerPDF").on("hidden.bs.modal", function () {


  $.ajax({
    url: rutaAjax + "app/Ajax/OrdenCompraGlobalesAjax.php",
    method: "POST",
    data: {
      accion: "eliminarArchivoTemporal",
      rutaArchivoTemporal: rutaTodosArchivos
    },
    dataType: "json",
  })
    .done(function (respuesta) {
    })
    .fail(function (error) {
      console.log("*** Error ***");
      console.log(error);
    })
    .always(function () {});
});

/*==============================================================
  Abrir el input al presionar el botón Cargar Comprobantes de Pago
  ==============================================================*/
$("#btnSubirComprobantes").on("click", function () {
  $("#comprobanteArchivos").click();
});
/*==============================================================
  Validar tipo y tamaño de los archivos Comprobantes de Pago
  ==============================================================*/
$("#comprobanteArchivos").change(function () {
  let archivos = this.files;
  if (archivos.length === 0) return;

  let error = false;
  let lista = $("div.subir-comprobantes span.lista-archivos");
  lista.html(""); // Limpiar lista previa

  for (let i = 0; i < archivos.length; i++) {
    let archivo = archivos[i];

    // Validar tipo PDF
    if (archivo.type !== "application/pdf") {
      error = true;
      Swal.fire({
        title: "Error en el tipo de archivo",
        text: `¡El archivo "${archivo.name}" debe ser PDF!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      break;
    }

    // Validar tamaño
    if (archivo.size > 4 * 1024 * 1024) {
      // 4MB
      error = true;
      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: `¡El archivo "${archivo.name}" no debe pesar más de 4MB!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      break;
    }
  }

  if (error) {
    $("#comprobanteArchivos").val(""); // Limpiar input
    lista.html(""); // Limpiar lista de archivos
    return;
  }

  // Mostrar nombres de archivos válidos
  for (let i = 0; i < archivos.length; i++) {
    lista.append(
      `<p class="font-italic text-info mb-0">${archivos[i].name}</p>`
    );
  }

  // Clonar input y renombrarlo para submit
  let clone = $("#comprobanteArchivos")[0].cloneNode(true);
  clone.removeAttribute("id");
  clone.name = "comprobanteArchivos[]";
  $("div.subir-comprobantes").append(clone);
});
