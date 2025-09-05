$(function () {
  let tableList = document.getElementById("tablaRequisiciones");
  let dataTableRequisiciones = null;
  let tablaExistencias = null;
  let tablaProveedores = null;
  let parametrosTableList = { responsive: true };
  const TIEMPO_DESCARGA = 350;

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fActualizarListado(
      rutaAjax + "app/Ajax/RequisicionAjax.php",
      "#tablaRequisiciones",
      parametrosTableList
    );

  // Variable global que guarda los seleccionados
  let selectedRequisitions = [];

  function fActualizarListado(
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
            render: function () {
              return `<input type="checkbox" class="chk-requisicion" />`;
            },
          },
          ...data.datos.columnas,
        ];

        const dataTableRequisiciones = $(idTabla).DataTable({
          data: data.datos.registros,
          columns: columnasConCheckbox,
          scrollX: true,
          language: LENGUAJE_DT,
          aaSorting: [],
        });

        // Funciones para mantener seleccionados actualizados
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

        // Checkbox principal
        $(document)
          .off("change", "#checkAll")
          .on("change", "#checkAll", function () {
            const checked = this.checked;
            $(`${idTabla} tbody input.chk-requisicion`).each(function () {
              $(this).prop("checked", checked).trigger("change");
            });
          });

        // Evento individual de cada checkbox
        $(document)
          .off("change", `${idTabla} .chk-requisicion`)
          .on("change", `${idTabla} .chk-requisicion`, function () {
            const fila = $(this).closest("tr");
            const rowData = dataTableRequisiciones.row(fila).data();
            const isChecked = this.checked;

            if (isChecked) {
              agregarSeleccionado(rowData);
            } else {
              eliminarSeleccionado(rowData);
            }

            // Este log se ejecuta en cada clic de checkbox
            console.log("Checkbox cambiado:", {
              seleccionado: isChecked,
              datos: rowData,
            });
            console.log("Seleccionados actualizados:", selectedRequisitions);
          });
      });
  }
  // Por ejemplo, fuera de la función:
  setInterval(() => {
    console.log("Desde fuera:", selectedRequisitions);
  }, 10000);

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
});
