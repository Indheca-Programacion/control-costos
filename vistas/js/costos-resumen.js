let tableListResumen = document.getElementById("tablaCostosResumen");
let tableListInsumos = document.getElementById("tablaInsumos");
let tableListIndirectos = document.getElementById("tablaIndirectos");
let parametrosTableList = { responsive: false };

let dataTableResumen = null;
let dataTableInsumos = null;
let dataTableIndirectos = null;

let dataTableSeleccionarInsumos = $("#tablaSeleccionarInsumos").DataTable();
let dataTableSeleccionarIndirectos = $(
  "#tablaSeleccionarIndirectos"
).DataTable();

let elementTabCostosResumen = document.getElementById("tabCostosResumen");

let tableRequiInsumoDetalles = document.getElementById(
  "tablaRequiInsumoDetalles"
);
let tableRequiIndirectoDetalles = document.getElementById(
  "tablaRequiIndirectoDetalles"
);

let elementModalCrearInsumo = document.querySelector("#modalCrearInsumo");
let elementModalAgregarInsumo = document.querySelector("#modalAgregarInsumo");
let elementModalBuscarInsumo = document.querySelector("#modalBuscarInsumo");

let elementModalCrearIndirecto = document.querySelector("#modalCrearIndirecto");
let elementModalAgregarIndirecto = document.querySelector(
  "#modalAgregarIndirecto"
);
let elementModalBuscarIndirecto = document.querySelector(
  "#modalBuscarIndirecto"
);

let elementModalCrearRequisicionPersonal = document.querySelector(
  "#modalCrearRequisicionPersonal"
);
let elementModalAgregarPartida = document.querySelector("#modalAgregarPartida");
let elementModalCrearRequisicion = document.querySelector(
  "#modalCrearRequisicion"
);
let elementModalCrearRequiInsumoIndirecto = document.querySelector(
  "#modalCrearRequiInsumoIndirecto"
);

let tipoInsumo,
  codigo,
  numeroIndirecto,
  tipoIndirecto,
  numero,
  descripcion,
  unidad,
  unidadId,
  obraDetalleId,
  tipo,
  cantidad,
  directos,
  indirectos;
let periodosGlobal;
let fechaInicioGlobal;
//
function getWeekDates(startDate, weekNumber) {
  const THURSDAY = 4; // jueves
  const WEDNESDAY = 3; // miércoles

  // Convertir la fecha inicial a un objeto Date
  const startDateObj = new Date(startDate);

  // Calcular la fecha del jueves de la semana inicial
  const initialThursday = new Date(startDateObj.getTime());
  initialThursday.setDate(
    initialThursday.getDate() + ((THURSDAY - initialThursday.getDay() + 7) % 7)
  );

  // Calcular la fecha del jueves de la semana deseada
  const targetThursday = new Date(initialThursday.getTime());
  targetThursday.setDate(targetThursday.getDate() + (weekNumber - 1) * 7);

  // Calcular la fecha de inicio y fin de la semana
  const startOfWeek = new Date(targetThursday.getTime());
  startOfWeek.setDate(targetThursday.getDate()); // jueves - 3 = lunes

  const endOfWeek = new Date(targetThursday.getTime());
  endOfWeek.setDate(targetThursday.getDate() + 6); // jueves + 6 = miércoles

  return {
    start: startOfWeek.toLocaleDateString("Es-es"),
    end: endOfWeek.toLocaleDateString("Es-es"),
  };
}
// Funcion que da formato de dinero
function formatMoney(amount) {
  const options = { style: "currency", currency: "USD" };
  const formatter = new Intl.NumberFormat("en-US", options);
  return formatter.format(amount);
}

function convertMoneyToNumber(moneyString) {
  // 1. Eliminar el símbolo de moneda ($)
  let numberString = moneyString.replace("$", "");

  // 2. Eliminar los separadores de miles (,)
  numberString = numberString.replace(/,/g, "");

  // 3. Convertir la cadena a un número
  return parseFloat(numberString);
}

function obtenerNumeroSemanaDesdeInicioNuevoFormato(
  fechaInicialStr,
  numeroSemana
) {
  // Validar el formato de la fecha inicial (asumiendo YYYY-MM-DD)
  const partesFecha = fechaInicialStr.split("-");
  if (
    partesFecha.length !== 3 ||
    isNaN(partesFecha[0]) ||
    isNaN(partesFecha[1]) ||
    isNaN(partesFecha[2])
  ) {
    return false; // Formato de fecha inicial inválido
  }

  const anio = parseInt(partesFecha[0], 10);
  const mes = parseInt(partesFecha[1], 10) - 1; // Los meses en JavaScript son 0-indexados
  const dia = parseInt(partesFecha[2], 10);

  const fechaInicial = new Date(anio, mes, dia);

  if (isNaN(fechaInicial.getTime())) {
    return false; // Fecha inicial inválida
  }

  // Calcular la fecha deseada sumando las semanas
  const milisegundosPorSemana = 7 * 24 * 60 * 60 * 1000;
  const fechaDeseadaMilisegundos =
    fechaInicial.getTime() + (numeroSemana - 1) * milisegundosPorSemana;
  const fechaDeseada = new Date(fechaDeseadaMilisegundos);

  // Función auxiliar para obtener el número de semana del año (ISO 8601)
  function getWeekNumber(date) {
    date = new Date(
      Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())
    );
    const dayNum = date.getUTCDay() || 7;
    date.setUTCDate(date.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
    return Math.ceil(((date - yearStart) / 86400000 + 1) / 7);
  }

  return getWeekNumber(fechaDeseada);
}

function format(d) {
  return d.acciones;
}

// Realiza la petición para actualizar el visor de resumen de costos
function fActualizarListados(rutaAjax) {
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
      // Inicializar DataTable solo si no existe ya una instancia
      if (!$.fn.DataTable.isDataTable("#tablaAnuncios")) {
        $("#tablaAnuncios").DataTable({
          data: data.anuncios,
          columns: [
        { data: "mensaje" },
        { data: "fechaHora" },
        { data: "usuarioNombre" }
          ],
        });
      } else {
        // Si ya existe, solo actualiza los datos
        const dt = $("#tablaAnuncios").DataTable();
        dt.clear();
        dt.rows.add(data.anuncios);
        dt.draw();
      }
      // console.log(data)
      directos = data.insumos.registros;
      indirectos = data.indirectos.registros;
      periodosGlobal = data.obra.periodos;
      $("#btnFiltrar").prop("disabled", false);
      elementTabCostosResumen.parentElement.parentElement.classList.remove(
        "d-none"
      );
      $("#btnCrearInsumo").prop("disabled", false);
      $("#btnAgregarInsumo").prop("disabled", false);
      $("#btnCrearIndirecto").prop("disabled", false);
      $("#btnAgregarIndirecto").prop("disabled", false);
      
      // document.querySelector("#monthFilterWrapper").classList.remove("d-none");
      document.querySelector("#yearFilterWrapper").classList.remove("d-none");

      // if (data.admin) {
      //   $("#btnAddSemanas").removeClass("d-none");
      // }
      // Habilitar/Deshabilitar botón Crear requisición
      let tableRequisicionDetalles = document.querySelector(
        "#tablaRequisicionDetalles tbody"
      );
      let registros = tableRequisicionDetalles.querySelectorAll("tr");
      if (registros.length > 0)
        $("#btnCrearRequisicion").prop("disabled", false);

      document
        .querySelector("#listado-insumos div.section-requisicion-insumos")
        .classList.remove("d-none");
      document
        .querySelector(
          "#listado-indirectos div.section-requisicion-indirectos"
        )
        .classList.remove("d-none");

      tableRequisicionDetalles = document.querySelector(
        "#tablaRequiInsumoDetalles tbody"
      );
      registros = tableRequisicionDetalles.querySelectorAll("tr");
      if (registros.length > 0)
        $("#btnCrearRequiInsumo").prop("disabled", false);

      tableRequisicionDetalles = document.querySelector(
        "#tablaRequiIndirectoDetalles tbody"
      );
      registros = tableRequisicionDetalles.querySelectorAll("tr");
      if (registros.length > 0)
        $("#btnCrearRequiIndirecto").prop("disabled", false);

      let elementSelectPeriodo = document.getElementById(
        "modalCrearRequiInsumoIndirecto_periodos"
      );

      //Actualizar los catalogos de presupuestos
      let elementSelectPresupuesto = document.getElementById(
        "filtroPresupuesto"
      );
      // Guardar el valor seleccionado actualmente
      let selectedPresupuesto = elementSelectPresupuesto.value;

      // Limpiar las opciones existentes y agregar la opción por defecto "General"
      $(elementSelectPresupuesto).empty();
      $(elementSelectPresupuesto).append('<option value="0">General</option>');
      data.catalogos.presupuestos.forEach((item, index) => {
        let selected = item.id == selectedPresupuesto ? 'selected' : '';
        // Si data.usuarioRoal es true y la opción contiene "ROAL", seleccionarla
        if (data.usuarioRoal && item.descripcion.toUpperCase().includes("ROAL")) {
          selected = 'selected';
        }
        let newOption = `<option value="${item.id}" ${selected}>
        ${item.descripcion}
          </option>`;
        $(elementSelectPresupuesto).append(newOption);
      });

      // Actualizar los catálogos en #modalAgregarInsumo
      let elementSelectInsumoTipo = document.getElementById(
        "modalAgregarInsumo_insumoTipoId"
      );
      data.catalogos.insumoTipos.forEach((item, index) => {
        let registro = elementSelectInsumoTipo.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectInsumoTipo).append(newOption);
        }
      });

      // Actualizar los catálogos en #modalCrearInsumo
      let elementSelectInsumoTipoCrear = document.getElementById(
        "modalCrearInsumo_insumoTipoId"
      );
      data.catalogos.insumoTipos.forEach((item, index) => {
        let registro = elementSelectInsumoTipoCrear.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectInsumoTipoCrear).append(newOption);
        }
      });

      let elementSelectUnidad = document.getElementById(
        "modalAgregarInsumo_unidadId"
      );
      data.catalogos.unidades.forEach((item, index) => {
        let registro = elementSelectUnidad.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectUnidad).append(newOption);
        }
      });

      let elementSelectUnidadCrear = document.getElementById(
        "modalCrearInsumo_unidadId"
      );
      data.catalogos.unidades.forEach((item, index) => {
        let registro = elementSelectUnidadCrear.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectUnidadCrear).append(newOption);
        }
      });

      // Actualizar los catálogos en #modalCrearIndirecto
      let elementSelectIndirectoTipo = document.getElementById(
        "modalCrearIndirecto_indirectoTipoId"
      );
      data.catalogos.indirectoTipos.forEach((item, index) => {
        let registro = elementSelectIndirectoTipo.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									[ ${item.numero} ] ${item.descripcion}
								</option>`;

          $(elementSelectIndirectoTipo).append(newOption);
        }
      });

      let elementSelectIndirectoTipoAdd = document.getElementById(
        "modalAgregarIndirecto_indirectoTipoId"
      );
      data.catalogos.indirectoTipos.forEach((item, index) => {
        let registro = elementSelectIndirectoTipoAdd.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									[ ${item.numero} ] ${item.descripcion}
								</option>`;

          $(elementSelectIndirectoTipoAdd).append(newOption);
        }
      });

      elementSelectUnidad = document.getElementById(
        "modalCrearIndirecto_unidadId"
      );
      data.catalogos.unidades.forEach((item, index) => {
        let registro = elementSelectUnidad.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectUnidad).append(newOption);
        }
      });

      elementSelectUnidad = document.getElementById(
        "modalAgregarIndirecto_unidadId"
      );
      data.catalogos.unidades.forEach((item, index) => {
        let registro = elementSelectUnidad.querySelector(
          `option[value="${item.id}"]`
        );
        if (registro === null) {
          let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

          $(elementSelectUnidad).append(newOption);
        }
      });

      // --- Actualizar tabla Resumen ---

      let arrayColumnsTextRight = [];
      let arrayColumnsOrderable = [0];

      // Agregar la columna Total
      $("#tablaCostosResumen thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Presupuestos</th>`
      );
      arrayColumnsTextRight.push(arrayColumnsTextRight.length + 1);
      arrayColumnsOrderable.push(arrayColumnsOrderable.length);
      $("#tablaCostosResumen thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Total</th>`
      );
      arrayColumnsTextRight.push(arrayColumnsTextRight.length + 1);
      arrayColumnsOrderable.push(arrayColumnsOrderable.length);

      dataTableResumen = $(tableListResumen).DataTable({
        autoWidth: false,
        responsive:
          parametrosTableList.responsive === undefined
        ? true
        : parametrosTableList.responsive,
        info: false,
        paging: false,
        searching: false,
        data: data.datos.registros,
        columns: data.datos.columnas,
        columnDefs: [
          // { targets: [0], visible: false, searchable: false },
          // { targets: [1], className: 'col-fixed-left' },
          { targets: arrayColumnsTextRight, className: "text-right" },
          // { targets: arrayColumnsTextCenter, className: 'text-center' },
          { targets: arrayColumnsOrderable, orderable: false },
          {
        targets: "_all",
        render: function (data, type, row, meta) {
          // Si es el header o el tipo no es display, no formatear
          if (type !== "display") return data;
          // Si es numérico y no es la primera columna (descripción)
          if (
            meta.col !== 0 &&
            !isNaN(parseFloat(data)) &&
            isFinite(data)
          ) {
            return formatMoney(parseFloat(data));
          }
          return data;
        },
          },
        ],
        buttons: [
          { extend: "copy", text: "Copiar", className: "btn-info" },
          { extend: "csv", className: "btn-info" },
          { extend: "excel", className: "btn-info" },
          { extend: "pdf", className: "btn-info" },
          { extend: "print", text: "Imprimir", className: "btn-info" },
          // { extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
        ],

        language: LENGUAJE_DT,
        aaSorting: [],
      }); // $(tableListResumen).DataTable({
      dataTableResumen
        .buttons()
        .container()
        .appendTo($(".col-sm-12:eq(0)", dataTableResumen.table().container()));
      tableListResumen.parentElement.classList.add("table-responsive");

      ajustaAlturaTablaCostosResumen();

      // --- Actualizar tabla Insumos ---

      arrayColumnsTextRight = [];
      let arrayClickeable = [];
      arrayColumnsOrderable = [0, 1, 2, 3, 4, 5];
      let lastColumnInsum = 5;
      // Agregar las columnas de los Períodos
      $("#tablaInsumos thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Remanente</th>`
      );
      arrayColumnsTextRight.push(lastColumnInsum++);
      arrayColumnsOrderable.push(lastColumnInsum);
      $("#tablaInsumos thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Remanente Cant.</th>`
      );
      arrayColumnsTextRight.push(lastColumnInsum++);
      arrayColumnsOrderable.push(lastColumnInsum);
      arrayColumnsTextRight.push(lastColumnInsum++);

      let fecha = data.obra.fechaInicio;
      dataTableInsumos = $(tableListInsumos).DataTable({
        autoWidth: false,
        responsive:
          parametrosTableList.responsive === undefined
        ? true
        : parametrosTableList.responsive,
        paging: false,
        pageLength: 100,
        lengthMenu: [
          [10, 15, 25, 50, 100],
          [10, 15, 25, 50, 100],
        ],
        searching: true,
        data: data.insumos.registros,
        columns: data.insumos.columnas,
        columnDefs: [
          { targets: [0], visible: false, searchable: false },
          { targets: [1], className: "col-fixed-left dt-control" },
          { targets: arrayClickeable, className: "text-right clickeable" },
          { targets: arrayColumnsTextRight, className: "text-right" },
          { targets: arrayColumnsOrderable, orderable: false },
          {
        targets: 1,
        render: function (data, type, row) {
          let button = "";
          if ("acciones" in row) {
            button = `
          <button type="button" class="btn btn-xs btn-primary rounded float-right btn-acciones" tipo="Insumo">
            <i class="fas fa-plus"></i>
          </button>`;
          }
          return `${data}${button}`;
        },
          },
        ],
        rowGroup: {
          startRender: function (rows, group, level) {
        let dataRow = rows.data()[0];
        let tr = document.createElement("tr");
        addCell(tr, "");
        addCell(tr, dataRow.tipoInsumo);
        addCell(tr, "");
        addCell(tr, "");
        addCell(tr, "");
        if (typeof dataRow[0] !== "undefined") {
          for (let i = 0; i < 12; i++) {
            addCell(tr, "");
          }
        } else {
          const currentYear = new Date().getFullYear();
          for (let i = 2024; i < currentYear; i++) {
            addCell(tr, "");
          }
        }
        addCell(tr, "");
        addCell(tr, "");
        return tr;
          },
          endRender: function (rows, group, level) {
        let dataRow = rows.data();
        let tr = document.createElement("tr");
        addCell(tr, "");
        addCell(tr, "Total " + dataRow[0].tipoInsumo);
        addCell(tr, "");
        addCell(tr, "");
        let presupuesto = 0;
        let remanente = 0;
        dataRow.each(function (rowData) {
          const valPresupuesto = convertMoneyToNumber(rowData["presupuesto"]);
          presupuesto += valPresupuesto;
          const valRemanente = convertMoneyToNumber(rowData["remanente"]);
          remanente += valRemanente;
        });
        addCell(tr, formatMoney(presupuesto), 1, "text-right");
        let totalCostos = 0;
        if (typeof dataRow[0].enero !== "undefined" && dataRow[0].enero !== null) {
          for (let i = 0; i < 12; i++) {
            let suma = 0;
            let Row = [];
            dataRow.each(function (rowData) {
          Row.push(rowData);
            });
            const meses = [
          "enero", "febrero", "marzo", "abril", "mayo", "junio",
          "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
            ];
            const nombreMes = meses[i];
            Row.forEach((element) => {
          if (typeof element[nombreMes] === "string") {
            const valorNumerico = convertMoneyToNumber(element[nombreMes]);
            suma += valorNumerico;
          }
            });
            totalCostos += suma;
            addCell(tr, formatMoney(suma), 1, "text-right");
          }
        } else {
          const currentYear = new Date().getFullYear();
          for (let i = 2024; i <= currentYear; i++) {
            let suma = 0;
            let Row = [];
            dataRow.each(function (rowData) {
          Row.push(rowData);
            });
            Row.forEach((element) => {
          if (typeof element[i] === "string") {
            const valorNumerico = convertMoneyToNumber(element[i]);
            suma += valorNumerico;
          }
            });
            totalCostos += suma;
            addCell(tr, formatMoney(suma), 1, "text-right");
          }
        }
        addCell(tr, formatMoney(presupuesto - totalCostos), 1, "text-right");
        addCell(tr, "", 1, "text-right");
        return tr;
          },
          dataSrc: ["tipoInsumo"],
        },
        // Asegúrate de que dom incluya los botones
        dom: 'Bfrtip',
        buttons: [
          { extend: "copy", text: "Copiar", className: "btn-info" },
          { extend: "csv", className: "btn-info" },
          { extend: "excel", className: "btn-info" },
          { extend: "pdf", className: "btn-info" },
          { extend: "print", text: "Imprimir", className: "btn-info" }
        ],
        language: LENGUAJE_DT,
        aaSorting: [],
      });
      // Esto asegura que los botones se muestren en el contenedor adecuado
      dataTableInsumos
        .buttons()
        .container()
        .appendTo($(".col-sm-12:eq(0)", dataTableInsumos.table().container()));
      tableListInsumos.parentElement.classList.add("table-responsive");

      ajustaAlturaTablaInsumos();

      // --- Actualizar tabla Indirectos ---

      arrayColumnsTextRight = [];
      arrayColumnsOrderable = [0, 1, 2, 3, 4, 5];
      let lastColumn = 5;
      arrayClickeable = [];
      // Agregar las columnas de los Períodos
      $("#tablaIndirectos thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Remanente</th>`
      );
      arrayColumnsTextRight.push(lastColumn++);
      arrayColumnsOrderable.push(lastColumn);
      $("#tablaIndirectos thead tr").append(
        `<th scope="col" class="text-right" style="min-width: 112px;">Remanente Cant.</th>`
      );
      arrayColumnsTextRight.push(lastColumn++);
      arrayColumnsOrderable.push(lastColumn);
      arrayColumnsTextRight.push(lastColumn++);

      fecha = data.obra.fechaInicio;
      fechaInicioGlobal = fecha;
      dataTableIndirectos = $(tableListIndirectos).DataTable({
        autoWidth: false,
        responsive:
          parametrosTableList.responsive === undefined
        ? true
        : parametrosTableList.responsive,
        paging: false,
        pageLength: 100,
        lengthMenu: [
          [10, 15, 25, 50, 100],
          [10, 15, 25, 50, 100],
        ],
        searching: true,
        // scrollX: true,
        data: data.indirectos.registros,
        columns: data.indirectos.columnas,

        columnDefs: [
          { targets: [0], visible: false, searchable: false },
          { targets: [1], className: "col-fixed-left dt-control" },
          { targets: arrayClickeable, className: "text-right clickeable" },
          { targets: arrayColumnsTextRight, className: "text-right" },
          // { targets: arrayColumnsTextCenter, className: 'text-center' },
          { targets: arrayColumnsOrderable, orderable: false },
          {
        targets: 1,
        render: function (data, type, row) {
          let button = "";
          if ("acciones" in row) {
            button = `
                    <button type="button" class="btn btn-xs btn-primary rounded float-right btn-acciones" tipo="Indirecto">
                      <i class="fas fa-plus"></i>
                    </button>`;
          }
          return `${data}${button}`;
        },
          },
        ],

        rowGroup: {
          // startRender: null,
          startRender: function (rows, group, level) {

        let dataRow = rows.data()[0];

        let tr = document.createElement("tr");

        // addCell(tr, '');
        addCell(tr, "");
        addCell(tr, dataRow.tipoIndirecto);

        if (typeof dataRow[0] !== "undefined") {
          for(let i = 0; i < 12; i++) {
            addCell(tr, "");
          }
        }else {
          const currentYear = new Date().getFullYear();
          for(let i = 2024; i < currentYear; i++) {
            addCell(tr, "");
          }
        }

        return tr;
          },
          // endRender: null,
          endRender: function (rows, group, level) {
        let dataRow = rows.data();
        let tr = document.createElement("tr");

        addCell(tr, "");
        addCell(tr, "Total " + dataRow[0].tipoIndirecto);
        addCell(tr, "");
        addCell(tr, "");
        let presupuesto = 0;
        let remanente = 0;
        // Suma los valores de presupuesto y remanente
        dataRow.each(function (rowData) {
          const valPresupuesto = convertMoneyToNumber(
            rowData["presupuesto"]
          );
          presupuesto += valPresupuesto;
          const valRemanente = convertMoneyToNumber(rowData["remanente"]);
          remanente += valRemanente;
        });
        addCell(tr, formatMoney(presupuesto), 1, "text-right");
        // Se evalua si se busca por años
        let totalCostos = 0;

        if (typeof dataRow[0].enero !== "undefined") {
          for(let i = 0; i < 12; i++) {
          let suma = 0;
          let Row = [];
          dataRow.each(function (rowData) {
            Row.push(rowData);
          });
          // Sumar por nombre del mes en vez de "periodoX"
          const meses = [
            "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
          ];
          const nombreMes = meses[i];
          Row.forEach((element) => {
            if (typeof element[nombreMes] === "string") {
            const valorNumerico = convertMoneyToNumber(
          element[nombreMes]
            );
            suma += valorNumerico;
            }
          });
          totalCostos += suma;
          addCell(tr, formatMoney(suma), 1, "text-right");
          }
        } else {
          const currentYear = new Date().getFullYear();
          for(let i = 2024; i <= currentYear; i++) {
            let suma = 0;
            let Row = [];
            dataRow.each(function (rowData) {
          Row.push(rowData);
            });
            // Sumar por nombre del mes en vez de "periodoX"
            Row.forEach((element) => {
          if (typeof element[i] === "string") {
            const valorNumerico = convertMoneyToNumber(
            element[i]
          );
          suma += valorNumerico;
          }
            });
            totalCostos += suma;
            addCell(tr, formatMoney(suma), 1, "text-right");
          }
        }
        addCell(tr, formatMoney(presupuesto-totalCostos), 1, "text-right");
        addCell(tr, "", 1, "text-right");
        return tr;
          },
          dataSrc: ["tipoIndirecto"],
        },

        dom: 'Bfrtip', // <-- Esto muestra los botones de descarga
        buttons: [
          { extend: "copy", text: "Copiar", className: "btn-info" },
          { extend: "csv", className: "btn-info" },
          { extend: "excel", className: "btn-info" },
          { extend: "pdf", className: "btn-info" },
          { extend: "print", text: "Imprimir", className: "btn-info" },
          // { extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
        ],

        language: LENGUAJE_DT,
        aaSorting: [],
      }); // $(tableListIndirectos).DataTable({
      dataTableIndirectos
        .buttons()
        .container()
        .appendTo(
          $(".col-sm-12:eq(0)", dataTableIndirectos.table().container())
        );
      tableListIndirectos.parentElement.classList.add("table-responsive");

      ajustaAlturaTablaIndirectos();
    }); // .then( data => {
} // function fActualizarListados( rutaAjax ) {

$(tableListInsumos).on("click", "td", function () {
  const celda = $(this); // La celda en sí
  const indiceColumna = celda.index(); // Índice de la columna
  const year = $("#filterYear").val(); // Obtener el año seleccionado

  if (indiceColumna >= 5 && year !== "all") {
    const fila = celda.closest("tr"); // Obtener la fila
    const valorColumna1 = fila.find("td").eq(1).text().trim(); // Índice 0 = columna 1

    // Obtener el texto del encabezado de la columna
    const encabezado = $("#tablaInsumos thead th")
      .eq(indiceColumna)
      .text()
      .trim();

    // Guardar los datos temporalmente si los necesitas en la consulta
    
    $("#miModal").data("insumo", valorColumna1);
    $("#miModal").data("fechaSeleccionada", encabezado);
    $("#miModal").data("anio", year);

    $("#miModal").modal("show");
  }
});

// Evento: cuando el modal termina de abrirse
$("#miModal").on("shown.bs.modal", function () {
  const insumo = $(this).data("insumo");
  const fechaSeleccionada = $(this).data("fechaSeleccionada");
  const anio = $(this).data("anio");
  const obraId = $("#filtroObraId").val();
  // Aquí haces la consulta (puede ser AJAX, fetch, etc.)

  $.ajax({
    url:
      rutaAjax +
      `app/Ajax/CostosResumenAjax.php?insumo=${insumo}&anio=${anio}&fecha=${fechaSeleccionada}&obra=${obraId}`,
    method: "GET",
    dataType: "json",
    success: function (respuesta) {
      const lista = $("#lista-resumen-costos");
      lista.empty(); // Limpiar lista previa

      if (respuesta.requisiciones.length === 0) {
        lista.append("<li>No se encontraron datos.</li>");
      } else {
        respuesta.requisiciones.forEach((item) => {
          lista.append(`
        <li>
            ${item.verRequi} - ${item.justificacion} - ${
            item.fechaCreacion
          } - Total: $${item.total ?? "N/A"}
        </li>
      `);
        });
      }
    },
    error: function () {
      $("#lista-resumen-costos").html("<li>Error al cargar datos.</li>");
    },
  });
});

// Confirmar la eliminación del Insumo o Indirecto
$(tableListInsumos).on("click", "button.eliminar", function (e) {
  e.preventDefault();
  var folio = $(this).attr("folio");
  var tipo = $(this).attr("tipo");
  var form = $(this).parents("form");

  Swal.fire({
    title:
      "¿Estás seguro de querer eliminar este " +
      tipo +
      " (Descripción: " +
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

$(tableListIndirectos).on("click", "button.eliminar", function (e) {
  e.preventDefault();
  var folio = $(this).attr("folio");
  var tipo = $(this).attr("tipo");
  var form = $(this).parents("form");

  Swal.fire({
    // title: '¿Estás Seguro de querer eliminar este Indirecto (Descripción: '+folio+') ?',
    title:
      "¿Estás seguro de querer eliminar este " +
      tipo +
      " (Descripción: " +
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

$("#collapseFiltros").on("show.bs.collapse", function (event) {
  let btnVerFiltros = document.getElementById("btnVerFiltros");
  btnVerFiltros.querySelector("i").classList.remove("fa-eye");
  btnVerFiltros.querySelector("i").classList.add("fa-eye-slash");
});

$("#tablaInsumos tbody").on(
  "click",
  "td.dt-control .btn-acciones",
  function () {
    let tr = $(this).closest("tr");
    tipo = $(this).attr("tipo");
    let row = dataTableInsumos.row(tr);
    let dataRow = row.data();
    // console.log(row.data());
    // console.log(tipo)
    tipoInsumo = dataRow.tipoInsumo;
    codigo = dataRow.codigo;
    cantidad = dataRow.cantidad;
    descripcion = $("<div/>").html(dataRow.descripcion).text();
    unidadId = dataRow.unidadId;
    unidad = dataRow.unidad;
    obraDetalleId = dataRow.obraDetalleId;

    if (row.child.isShown()) {
      // Si el detalle ya está visible, lo ocultamos
      tr.removeClass("details");
      row.child.hide();
    } else {
      // Si el detalle está oculto, lo mostramos y ocultamos los demás
      $(".details").removeClass("details");
      dataTableInsumos.rows().every(function () {
        if (this.child.isShown()) {
          this.child.hide();
        }
      });

      dataTableIndirectos.rows().every(function () {
        if (this.child.isShown()) {
          this.child.hide();
        }
      });

      tr.addClass("details");
      row.child(format(row.data())).show();
    }
  }
);

$("#tablaIndirectos tbody").on(
  "click",
  "td.dt-control .btn-acciones",
  function () {
    let tr = $(this).closest("tr");
    tipo = $(this).attr("tipo");
    let row = dataTableIndirectos.row(tr);
    let dataRow = row.data();
    // console.log(dataRow);
    // console.log(tipo)

    numeroIndirecto = dataRow.numeroIndirecto;
    tipoIndirecto = dataRow.tipoIndirecto;
    numero = dataRow.numero;
    cantidad = dataRow.cantidad;
    unidadId = dataRow.unidadId;
    descripcion = $("<div/>").html(dataRow.descripcion).text();
    unidad = dataRow.unidad;
    obraDetalleId = dataRow.obraDetalleId;

    if (row.child.isShown()) {
      tr.removeClass("details");
      row.child.hide();

      // Remove from the 'open' array
      // detailRows.splice(idx, 1);
    } else {
      dataTableInsumos.rows().every(function () {
        if (this.child.isShown()) {
          this.child.hide();
        }
      });

      dataTableIndirectos.rows().every(function () {
        if (this.child.isShown()) {
          this.child.hide();
        }
      });
      tr.addClass("details");
      row.child(format(row.data())).show();
    }
  }
);

$("#collapseFiltros").on("hide.bs.collapse", function (event) {
  let btnVerFiltros = document.getElementById("btnVerFiltros");
  btnVerFiltros.querySelector("i").classList.remove("fa-eye-slash");
  btnVerFiltros.querySelector("i").classList.add("fa-eye");
});

$("#filtroObraId").change(function () {
  $("#btnCrearRequisicion").prop("disabled", true);
  elementTabCostosResumen.parentElement.parentElement.classList.add("d-none");
  document
    .querySelector("#listado-insumos div.section-requisicion-insumos")
    .classList.add("d-none");
  document
    .querySelector("#listado-indirectos div.section-requisicion-indirectos")
    .classList.add("d-none");
  // document.querySelector("#btnAddSemanas").classList.add("d-none");
  // document.querySelector("#monthFilterWrapper").classList.add("d-none");
  document.querySelector("#yearFilterWrapper").classList.add("d-none");

  // document.querySelector("#btnImprimir").classList.add("d-none");
  // document.querySelector("#btnImportPlantilla").classList.add("d-none");
  $("#btnCrearInsumo").prop("disabled", true);
  $("#btnCrearIndirecto").prop("disabled", true);

  cleanTableResumen();
  cleanTableInsumos();
  cleanTableIndirectos();
}); // $("#fotos").change(function() {

$("#btnFiltrar").on("click", function (e) {
  $(this).prop("disabled", true);
  elementTabCostosResumen.parentElement.parentElement.classList.add("d-none");
  $("#btnCrearInsumo").prop("disabled", true);
  $("#btnCrearIndirecto").prop("disabled", true);
  let elementSelectPeriodo = document.getElementById(
    "modalCrearRequiInsumoIndirecto_periodos"
  );
  $(elementSelectPeriodo).html("");
  $(elementSelectPeriodo).append(
    '<option value="">Selecciona una Semana</option>'
  );

  cleanTableResumen();
  cleanTableInsumos();
  cleanTableIndirectos();

  let obraId = $("#filtroObraId").val();
  let divisaId = $("#filtroDivisas").val();
  let presupuesto = $("#filtroPresupuesto").val();
  let anio = $("#filterYear").val();
  let mes = $("#filterMonth").val();

  if (obraId == "0") {
    document.getElementById("filtroObraId").classList.add("is-invalid");

    $('span[aria-labelledby="select2-filtroObraId-container"]').css(
      "border-color",
      "#dc3545"
    );
    $('span[aria-labelledby="select2-filtroObraId-container"]').css(
      "background-image",
      "url(" + rutaAjax + "vistas/img/is-invalid.svg)"
    );
    $('span[aria-labelledby="select2-filtroObraId-container"]').css(
      "background-repeat",
      "no-repeat"
    );
    $('span[aria-labelledby="select2-filtroObraId-container"]').css(
      "background-position",
      "right calc(0.375em + 1.0875rem) center"
    );
    $('span[aria-labelledby="select2-filtroObraId-container"]').css(
      "background-size",
      "calc(0.75em + 0.375rem) calc(0.75em + 0.375rem"
    );

    $(this).prop("disabled", false);

    return;
  } else {
    document.getElementById("filtroObraId").classList.remove("is-invalid");

    $('span[aria-labelledby="select2-filtroObraId-container"]').removeAttr(
      "style"
    );
  }

  fActualizarListados(
    `${rutaAjax}app/Ajax/CostosResumenAjax.php?obraId=${obraId}&divisaId=${divisaId}&presupuesto=${presupuesto}&anio=${anio}`
  );
});

$("#filtroPresupuesto").on("change", function (e) {
  $(this).prop("disabled", true);
  elementTabCostosResumen.parentElement.parentElement.classList.add("d-none");
  $("#btnCrearInsumo").prop("disabled", true);
  $("#btnCrearIndirecto").prop("disabled", true);
  let elementSelectPeriodo = document.getElementById(
    "modalCrearRequiInsumoIndirecto_periodos"
  );
  $(elementSelectPeriodo).html("");
  $(elementSelectPeriodo).append(
    '<option value="">Selecciona una Semana</option>'
  );

  cleanTableResumen();
  cleanTableInsumos();
  cleanTableIndirectos();

  let obraId = $("#filtroObraId").val();
  let divisaId = $("#filtroDivisas").val();
  let presupuesto = $("#filtroPresupuesto").val();
  let anio = $("#filterYear").val();

  fActualizarListados(
    `${rutaAjax}app/Ajax/CostosResumenAjax.php?obraId=${obraId}&divisaId=${divisaId}&presupuesto=${presupuesto}&anio=${anio}`
  );

  $(this).prop("disabled", false);
})
// Activar el elemento Select2
$(".select2").select2({
  language: "es",
  tags: false,
  width: "100%",
  // theme: 'bootstrap4'
});
$(".select2Add").select2({
  tags: true,
  // ,theme: 'bootstrap4'
});
$(".select2ModalAgregarInsumo").select2({
  dropdownParent: $("#modalAgregarInsumo"),
  language: "es",
  tags: false,
  width: "100%",
  // theme: 'bootstrap4'
});
$(".select2ModalCrearInsumo").select2({
  dropdownParent: $("#modalCrearInsumo"),
  language: "es",
  tags: false,
  width: "100%",
  // theme: 'bootstrap4'
});
$(".select2ModalAgregarIndirecto").select2({
  dropdownParent: $("#modalCrearInsumo"),
  language: "es",
  tags: false,
  width: "100%",
  // theme: 'bootstrap4'
});
$("#reservationdate").daterangepicker({});

$("#modalCrearRequiInsumoIndirecto_fechaRequerida").datetimepicker({
  format: "DD/MMMM/YYYY",
  widgetPositioning: {
    horizontal: "auto",
    vertical: "bottom",
  },
});

// Activar el elemento Inputmask
$("[data-mask]").inputmask();

// *************************************************
// *** Eventos y Funciones de la pestaña Resumen ***
// *************************************************

// Limpia la tabla y crea el header
function cleanTableResumen() {
  $(tableListResumen).DataTable().destroy();
  dataTableResumen = null;
  $("#tablaCostosResumen thead tr").html("");
  $("#tablaCostosResumen thead tr").append(
    '<th scope="col" style="min-width: 224px;">Descripción</th>'
  );
  tableListResumen.querySelector("tbody").innerHTML = "";
  $("#tablaCostosResumen tfoot tr").html("");
}

// *************************************************
// *** Eventos y Funciones de la pestaña Directos ***
// *************************************************

// Limpia la tabla y crea el header
function cleanTableInsumos() {
  $(tableListInsumos).DataTable().destroy();
  dataTableInsumos = null;
  $("#tablaInsumos thead tr").html("");
  $("#tablaInsumos thead tr").append('<th scope="col">Tipo</th>');
  $("#tablaInsumos thead tr").append(
    '<th scope="col" style="min-width: 96px;">Código</th>'
  );
  $("#tablaInsumos thead tr").append(
    '<th scope="col" style="min-width: 192px;">Descripción</th>'
  );
  $("#tablaInsumos thead tr").append(
    '<th scope="col" style="min-width: 112px;">Unidad</th>'
  );
  $("#tablaInsumos thead tr").append(
    '<th scope="col" style="min-width: 112px;">Cantidad</th>'
  );
  $("#tablaInsumos thead tr").append(
    '<th scope="col" style="min-width: 112px;">Presupuesto</th>'
  );
  tableListInsumos.querySelector("tbody").innerHTML = "";
}

$("#tablaInsumos").on("click", "tr td.clickeable", function (e) {
  let valorCelda = $(this).closest("tr").find("td:eq(0)").text().trim(); // Reemplaza '0' con el índice de la columna deseada
  let indiceColumna = $(this).index();

  let rows = directos.filter(function (objeto) {
    return objeto.codigo == valorCelda;
  });
  var openModalButton = document.getElementById("openModalDetails");
  var modalTitle = document.getElementById("ModalDetallesLabel");
  let modalContent = document.getElementById("modalDetallesContent");
  modalTitle.textContent = "Semana " + (indiceColumna - 4);
  openModalButton.click();
  let contenidoHTML = "";
  if (rows[0].arrayRequis !== null) {
    let requis = rows[0].arrayRequis.filter(function (row) {
      return row.periodo == indiceColumna - 4;
    });

    requis.forEach(function (dato) {
      contenidoHTML +=
        "<a target='_blank' href='" +
        dato.rutas +
        "'> Requisicion " +
        dato.folio +
        "</a> <br>";
    });
  }
  if (rows[0].arrayNominas !== null) {
    let requis = rows[0].arrayNominas.filter(function (row) {
      return row.periodo == indiceColumna - 4;
    });
    requis.forEach(function (dato) {
      contenidoHTML +=
        "<a target='_blank' href='" + dato.rutas + "'> Nomina </a> <br>";
    });
  }
  modalContent.innerHTML =
    contenidoHTML == "" ? "No hay requisiciones" : contenidoHTML;
});

// Buscar insumo
$("#modalAgregarInsumo button#btnBuscarInsumo").on("click", function (e) {
  let elementErrorValidacion =
    elementModalCrearInsumo.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let tableList = document.getElementById("tablaSeleccionarInsumos");
  $(tableList).DataTable().destroy();
  tableList.querySelector("tbody").innerHTML = "";

  let obraId = document.getElementById("filtroObraId");
  // if ( insumoTipoId == '' ) return;

  fetch(`${rutaAjax}app/Ajax/InsumoAjax.php?obraId=${obraId.value}`, {
    method: "GET", // *GET, POST, PUT, DELETE, etc.
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .catch((error) => console.log("Error:", error))
    .then((data) => {
      if (data.error) {
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(data.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        elementErrorValidacion.querySelector("ul").appendChild(elementList);

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      dataTableSeleccionarInsumos = $(tableList).DataTable({
        autoWidth: false,
        responsive:
          parametrosTableList.responsive === undefined
            ? true
            : parametrosTableList.responsive,
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
          row.classList.add("seleccionable");
        },

        language: LENGUAJE_DT,
        aaSorting: [],
      }); // $(tableListResumen).DataTable({

      $(elementModalBuscarInsumo).modal("show");
    }); // .then( data => {
});

dataTableSeleccionarInsumos.on("click", "tbody tr.seleccionable", function () {
  let data = dataTableSeleccionarInsumos.row(this).data();
  // console.log(data)
  const insumoId = document.getElementById("modalAgregarInsumo_insumoId");
  const insumotipo = document.getElementById("modalAgregarInsumo_insumoTipoId");
  const codigo = document.getElementById("modalAgregarInsumo_codigo");
  const descripcion = document.getElementById("modalAgregarInsumo_descripcion");
  const unidad = document.getElementById("modalAgregarInsumo_unidadId");
  insumoId.value = data.id;
  insumotipo.value = data.insumoTipoId;
  codigo.value = data.codigo;
  descripcion.value = data.descripcion;
  unidad.value = data.unidadId;
  $("#modalAgregarInsumo_insumoTipoId").trigger("change");
  $("#modalAgregarInsumo_unidadId").trigger("change");
  $(elementModalBuscarInsumo).modal("hide");
});

// Guardar insumo
$("#modalCrearInsumo button.btnGuardar").on("click", function (e) {
  let elementErrorValidacion =
    elementModalCrearInsumo.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let btnGuardar = this;

  // Petición Ajax para guardar el insumo
  let token = $('input[name="_token"]').val();
  let datos = new FormData(document.getElementById("formInsumosSendCreate"));
  datos.append("_token", token);
  datos.append("accion", "crear");
  $.ajax({
    url: rutaAjax + "app/Ajax/InsumoAjax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    beforeSend: () => {
      $(btnGuardar).prop("disabled", true);
      // loading();
    },
  })
    .done(function (respuesta) {
      if (respuesta.error) {
        if (respuesta.errors) {
          // console.log(Object.keys(respuesta.errors))
          let errors = Object.values(respuesta.errors);

          // respuesta.errors.forEach( (item, index) => {
          errors.forEach((item) => {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(item);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          });
        } else {
          let elementList = document.createElement("li"); // prepare a new li DOM element
          let newContent = document.createTextNode(respuesta.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector("ul").appendChild(elementList);
        }

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      $(elementModalCrearInsumo).modal("hide");
      crearToast(
        "bg-success",
        "Crear Directo",
        "OK",
        respuesta.respuestaMessage
      );

      $("#modalCrearInsumo_insumoTipoId").val("");
      $("#modalCrearInsumo_insumoTipoId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalCrearInsumo_codigo").val("");
      $("#modalCrearInsumo_descripcion").val("");
      $("#modalCrearInsumo_unidadId").val("");
      $("#modalCrearInsumo_unidadId").trigger("change.select2"); // Notify only Select2 of changes

      document.getElementById("btnFiltrar").click();
    })
    .fail(function (error) {
      // console.log("*** Error ***");
      // console.log(error);
      // console.log(error.responseText);
      // console.log(error.responseJSON);
      // console.log(error.responseJSON.message);

      let elementList = document.createElement("li"); // prepare a new li DOM element
      let newContent = document.createTextNode(error.errorMessage);
      elementList.appendChild(newContent); //añade texto al div creado.
      // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
      $(elementErrorValidacion).removeClass("d-none");
    })
    .always(function () {
      // stopLoading();
      $(btnGuardar).prop("disabled", false);
    });
});

// Agregar Insumo a la obra
$("#modalAgregarInsumo button.btnAgregar").on("click", function (e) {
  let elementErrorValidacion =
    elementModalAgregarInsumo.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let btnAgregar = this;
  // Petición Ajax para insertar el insumo a la obra
  let token = $('input[name="_token"]').val();
  let datos = new FormData(document.getElementById("formInsumosSend"));
  let idObra = document.getElementById("filtroObraId");
  datos.append("_token", token);
  datos.append("obraId", idObra.value);
  datos.append("accion", "agregar");
  $.ajax({
    url: rutaAjax + "app/Ajax/ObraAjax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    beforeSend: () => {
      $(btnAgregar).prop("disabled", true);
      // loading();
    },
  })
    .done(function (respuesta) {
      if (respuesta.error) {
        if (respuesta.errors) {
          // console.log(Object.keys(respuesta.errors))
          let errors = Object.values(respuesta.errors);

          // respuesta.errors.forEach( (item, index) => {
          errors.forEach((item) => {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(item);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          });
        } else {
          let elementList = document.createElement("li"); // prepare a new li DOM element
          let newContent = document.createTextNode(respuesta.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector("ul").appendChild(elementList);
        }

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      $(elementModalAgregarInsumo).modal("hide");
      crearToast(
        "bg-success",
        "Crear Directo",
        "OK",
        respuesta.respuestaMessage
      );

      $("#modalAgregarInsumo_insumoTipoId").val("");
      $("#modalAgregarInsumo_insumoTipoId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalAgregarInsumo_codigo").val("");
      $("#modalAgregarInsumo_descripcion").val("");
      $("#modalAgregarInsumo_unidadId").val("");
      $("#modalAgregarInsumo_unidadId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalAgregarInsumo_cantidad").val("");
      $("#modalAgregarInsumo_presupuesto").val("");
      document.getElementById("btnFiltrar").click();
    })
    .fail(function (error) {
      // console.log("*** Error ***");
      // console.log(error);
      // console.log(error.responseText);
      // console.log(error.responseJSON);
      // console.log(error.responseJSON.message);
      // console.log(error);
      let elementList = document.createElement("li"); // prepare a new li DOM element
      let newContent = document.createTextNode(error.errorMessage);
      elementList.appendChild(newContent); //añade texto al div creado.
      // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
      $(elementErrorValidacion).removeClass("d-none");
    })
    .always(function () {
      // stopLoading();
      $(btnAgregar).prop("disabled", false);
    });
});

$(elementModalAgregarPartida).on("show.bs.modal", function (event) {
  if (tipo == "Insumo")
    this.querySelector(
      '.modal-body div.row[data-tipo="insumo"]'
    ).classList.remove("d-none");
  else
    this.querySelector(
      '.modal-body div.row[data-tipo="indirecto"]'
    ).classList.remove("d-none");
});

$(elementModalAgregarPartida).on("shown.bs.modal", function (event) {
  if (tipo == "Insumo") {
    $("#modalAgregarPartida_insumoTipoId").val(tipoInsumo);
    this.querySelector("#modalAgregarPartida_codigo").value = codigo;
  } else {
    $("#modalAgregarPartida_indirectoTipoId").val(tipoIndirecto);
    this.querySelector("#modalAgregarPartida_numero").value = numero;
  }

  // let descripcion = dataRow.descripcion;
  // this.querySelector('#modalAgregarPartida_cantidad').max = parseInt(cantidad);
  this.querySelector("#modalAgregarPartida_descripcion").value = descripcion;
  this.querySelector("#modalAgregarPartida_unidadId").value = unidadId;
  this.querySelector("#modalAgregarPartida_unidad").value = unidad;
  this.querySelector("#modalAgregarPartida_obraDetalleId").value =
    obraDetalleId;
});

$(elementModalAgregarPartida).on("hidden.bs.modal", function (event) {
  this.querySelector('.modal-body div.row[data-tipo="insumo"]').classList.add(
    "d-none"
  );
  this.querySelector(
    '.modal-body div.row[data-tipo="indirecto"]'
  ).classList.add("d-none");
});

// Abrir el input al presionar el botón Subir Fotos
$("#btnSubirFotos").click(function () {
  // document.getElementById('fotos').click();
  document.getElementById("modalAgregarPartida_fotos").click();
});

// Validar tipo y tamaño de los archivos Órdenes de Compra
// $("#fotos").change(function() {
$("#modalAgregarPartida_fotos").change(function () {
  $("div.subir-fotos span.previsualizar").html("");
  let archivos = this.files;

  for (let i = 0; i < archivos.length; i++) {
    let archivo = archivos[i];

    /*================================================
		VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA JPG O PNG
		================================================*/

    if (archivo["type"] != "image/jpeg" && archivo["type"] != "image/png") {
      // $("#fotos").val("");
      $("#modalAgregarPartida_fotos").val("");
      // $("div.subir-fotos span.lista-fotos").html('');
      $("div.subir-fotos span.previsualizar").html("");

      Swal.fire({
        title: "Error en el tipo de archivo",
        text: '¡El archivo "' + archivo["name"] + '" debe ser JPG o PNG!',
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else if (archivo["size"] > 1000000) {
      // $("#fotos").val("");
      $("#modalAgregarPartida_fotos").val("");
      // $("div.subir-fotos span.lista-fotos").html('');
      $("div.subir-fotos span.previsualizar").html("");

      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: '¡El archivo "' + archivo["name"] + '" no debe pesar más de 1MB!',
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else {
      // $("div.subir-fotos span.lista-fotos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

      let datosImagen = new FileReader();
      datosImagen.readAsDataURL(archivo);

      $(datosImagen).on("load", function (event) {
        let rutaImagen = event.target.result;

        let elementPicture = `<picture>
										<img src="${rutaImagen}" class="img-fluid img-thumbnail" style="width: 100%">
									</picture>
									<p class="font-italic text-info mb-0">${archivo["name"]}</p>`;
        $("div.subir-fotos span.previsualizar").append(elementPicture);
      });
    }
  }
}); // $("#fotos").change(function() {

$(elementModalAgregarPartida).on(
  "click",
  "button.btnAgregarPartida",
  function (event) {
    let elementTipoInsumoIndirecto =
      tipo == "Insumo"
        ? document.getElementById("modalAgregarPartida_insumoTipoId")
        : document.getElementById("modalAgregarPartida_indirectoTipoId");
    let elementCodigoNumero =
      tipo == "Insumo"
        ? document.getElementById("modalAgregarPartida_codigo")
        : document.getElementById("modalAgregarPartida_numero");
    let elementDescripcion = document.getElementById(
      "modalAgregarPartida_descripcion"
    );
    let elementId = document.getElementById(
      "modalAgregarPartida_obraDetalleId"
    );
    let elementCostoUnitario = document.getElementById(
      "modalAgregarPartida_costo_unitario"
    );
    // console.log(elementId.value)

    let tipoInsumoIndirecto = elementTipoInsumoIndirecto.value;
    let codigoNumero = elementCodigoNumero.value;
    let descripcion = elementDescripcion.value;

    let elementCantidad = document.getElementById(
      "modalAgregarPartida_cantidad"
    );
    let elementUnidad = document.getElementById("modalAgregarPartida_unidad");
    let elementUnidadId = document.getElementById(
      "modalAgregarPartida_unidadId"
    );
    let elementFoto = document.getElementById("modalAgregarPartida_fotos");
    let elementConcepto = document.getElementById(
      "modalAgregarPartida_concepto"
    );
    let elementFotos = document.getElementById("modalAgregarPartida_fotos");
    let elementCosto = document.getElementById("modalAgregarPartida_costo");
    // let elementISR = document.getElementById("modalAgregarPartida_ISR");
    // let elementIVA = document.getElementById("modalAgregarPartida_IVA");

    const files = elementFoto.files;

    let cantidad = elementCantidad.value;
    let costo = elementCosto.value;
    let unidad = elementUnidad.value.trim();
    let concepto = elementConcepto.value.trim();
    let costoUnitario = elementCostoUnitario.value;

    // console.log(elementUnidadId.value)

    let elementPadre = null;
    let newDiv = null;
    let newContent = null;

    elementCosto.classList.remove("is-invalid");
    elementPadre = elementCosto.parentElement;
    newDiv = elementPadre.querySelector("div.invalid-feedback");
    if (newDiv != null) elementPadre.removeChild(newDiv);

    elementCantidad.classList.remove("is-invalid");
    elementPadre = elementCantidad.parentElement;
    newDiv = elementPadre.querySelector("div.invalid-feedback");
    if (newDiv != null) elementPadre.removeChild(newDiv);

    elementUnidad.classList.remove("is-invalid");
    elementPadre = elementUnidad.parentElement;
    newDiv = elementPadre.querySelector("div.invalid-feedback");
    if (newDiv != null) elementPadre.removeChild(newDiv);

    elementConcepto.classList.remove("is-invalid");
    elementPadre = elementConcepto.parentElement;
    newDiv = elementPadre.querySelector("div.invalid-feedback");
    if (newDiv != null) elementPadre.removeChild(newDiv);

    elementCostoUnitario.classList.remove("is-invalid");
    elementPadre = elementCostoUnitario.parentElement;
    newDiv = elementPadre.querySelector("div.invalid-feedback");
    if (newDiv != null) elementPadre.removeChild(newDiv);

    let errores = false;
    if (costo == "") {
      elementCosto.classList.add("is-invalid");
      elementPadre = elementCosto.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      // newContent = document.createTextNode("La cantidad es obligatoria.");
      newContent = document.createTextNode(
        "El valor del campo Costo no puede ser menor a 0.01."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    } else if (costo.length > 28) {
      elementCosto.classList.add("is-invalid");
      elementPadre = elementCosto.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode(
        "El campo costo debe ser máximo de 8 dígitos."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    }

    if (parseFloat(cantidad) < 0.01 || cantidad == "") {
      elementCantidad.classList.add("is-invalid");
      elementPadre = elementCantidad.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      // newContent = document.createTextNode("La cantidad es obligatoria.");
      newContent = document.createTextNode(
        "El valor del campo Cantidad no puede ser menor a 0."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    } else if (cantidad.length > 10) {
      elementCantidad.classList.add("is-invalid");
      elementPadre = elementCantidad.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode(
        "El campo Cantidad debe ser máximo de 8 dígitos."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    }

    if (costoUnitario == "") {
      elementCostoUnitario.classList.add("is-invalid");
      elementPadre = elementCostoUnitario.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      // newContent = document.createTextNode("La cantidad es obligatoria.");
      newContent = document.createTextNode(
        "El valor del campo Cantidad no puede ser menor a 0."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    } else if (costoUnitario.length > 14) {
      elementCostoUnitario.classList.add("is-invalid");
      elementPadre = elementCostoUnitario.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode(
        "El campo Cantidad debe ser máximo de 8 dígitos."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    }

    if (unidad == "") {
      elementUnidad.classList.add("is-invalid");
      elementPadre = elementUnidad.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode("La unidad es obligatoria.");
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    } else if (unidad.length > 80) {
      elementUnidad.classList.add("is-invalid");
      elementPadre = elementUnidad.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode(
        "La unidad debe ser máximo de 80 caracteres."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    }

    if (concepto == "") {
      elementConcepto.classList.add("is-invalid");
      elementPadre = elementConcepto.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode("El concepto es obligatorio.");
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    } else if (concepto.length > 1000) {
      elementConcepto.classList.add("is-invalid");
      elementPadre = elementConcepto.parentElement;
      newDiv = document.createElement("div");
      newDiv.classList.add("invalid-feedback");
      newContent = document.createTextNode(
        "El concepto debe ser máximo de 1000 caracteres."
      );
      newDiv.appendChild(newContent); //añade texto al div creado.
      elementPadre.appendChild(newDiv);

      errores = true;
    }

    if (errores) return;

    // let tableRequisicionDetalles = document.querySelector('#tablaRequisicionDetalles tbody');
    let tableRequisicionDetalles =
      tipo == "Insumo"
        ? document.querySelector("#tablaRequiInsumoDetalles tbody")
        : document.querySelector("#tablaRequiIndirectoDetalles tbody");
    let registros = tableRequisicionDetalles.querySelectorAll("tr");

    let registrosNuevos =
      tableRequisicionDetalles.querySelectorAll("tr[nuevo]");
    let partida = registrosNuevos.length + 1;

    // <td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}"></td>
    // <td>${tipo}</td> (iba antes de tipoInsumoIndirecto)
    let elementRow = `<tr nuevo partida="${partida}">
						<td partida class="text-right">
							<span>${registros.length + 1}</span>
							<input type="hidden" name="detalles[partida][]" value="${partida}">
							<button type='button' class='btn btn-xs btn-danger ml-1 eliminar'>
								<i class='far fa-times-circle'></i>
							</button>
						</td>
						<td>${tipoInsumoIndirecto}</td>
						<td>${codigoNumero}</td>
						<td>${descripcion}</td>
						<td>${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
						<td>${costo}</td>
						<td>${unidad}</td>
						<td>${concepto}</td>
						<td style="display: none;">${elementId}</td>
						<td style="display: none;">${files}</td>
					</tr>`;
    $(tableRequisicionDetalles).append(elementRow);

    let rowNuevoRegistro = tableRequisicionDetalles.querySelector(
      `tr[partida="${partida}"]`
    );
    let columnaConcepto = rowNuevoRegistro.querySelector("td:last-child");

    let cloneElementUnidadId = elementUnidadId.cloneNode(true);
    cloneElementUnidadId.removeAttribute("id");
    cloneElementUnidadId.type = "hidden";
    cloneElementUnidadId.name = "detalles[unidadId][]";
    $(columnaConcepto).append(cloneElementUnidadId);

    let cloneElementUnidad = elementUnidad.cloneNode(true);
    cloneElementUnidad.removeAttribute("id");
    cloneElementUnidad.type = "hidden";
    cloneElementUnidad.name = "detalles[unidad][]";
    $(columnaConcepto).append(cloneElementUnidad);

    let cloneElementConcepto = elementConcepto.cloneNode(true);
    cloneElementConcepto.removeAttribute("id");
    cloneElementConcepto.name = "detalles[concepto][]";
    cloneElementConcepto.classList.add("d-none");
    $(columnaConcepto).append(cloneElementConcepto);

    let cloneElementCosto = elementCosto.cloneNode(true);
    cloneElementCosto.removeAttribute("id");
    cloneElementCosto.name = "detalles[costo][]";
    cloneElementCosto.classList.add("d-none");
    $(columnaConcepto).append(cloneElementCosto);

    let cloneElementFotos = elementFotos.cloneNode(true);
    cloneElementFotos.removeAttribute("id");
    cloneElementFotos.name = "detalle_imagenes[" + partida + "][]";
    $(columnaConcepto).append(cloneElementFotos);
    $("#modalAgregarPartida_fotos").val("");
    $("div.subir-fotos span.previsualizar").html("");

    let cloneElementODId = elementId.cloneNode(true);
    cloneElementODId.removeAttribute("id");
    cloneElementODId.name = "detalles[obraDetalleId][]";
    cloneElementODId.classList.add("d-none");
    $(columnaConcepto).append(cloneElementODId);

    let cloneElementCostoUnitario = elementCostoUnitario.cloneNode(true);
    cloneElementCostoUnitario.removeAttribute("id");
    cloneElementCostoUnitario.name = "detalles[costo_unitario][]";
    cloneElementCostoUnitario.classList.add("d-none");
    $(columnaConcepto).append(cloneElementCostoUnitario);

    elementCostoUnitario.value = "0";
    elementCantidad.value = "1";
    elementUnidad.value = "";
    elementConcepto.value = "";
    elementCosto.value = "0";
    // elementIVA.value = '0'
    // elementISR.value = '0'

    $(elementModalAgregarPartida).modal("hide");
    // $("#btnCrearRequisicion").prop('disabled', false);
    if (tipo == "Insumo") $("#btnCrearRequiInsumo").prop("disabled", false);
    else $("#btnCrearRequiIndirecto").prop("disabled", false);
  }
);

$(tableRequiInsumoDetalles).on("click", "button.eliminar", function (event) {
  this.parentElement.parentElement.remove();

  // Renumerar las partidas
  let tableRequisicionDetalles =
    tableRequiInsumoDetalles.querySelector("tbody");
  let registros = tableRequisicionDetalles.querySelectorAll("tr");
  registros.forEach((registro, index) => {
    registro.setAttribute("partida", index + 1);
    registro.querySelector("td[partida] span").innerHTML = index + 1;
  });

  if (registros.length == 0) $("#btnCrearRequiInsumo").prop("disabled", true);
});
let tipoReq = "";
$(elementModalCrearRequiInsumoIndirecto).on("shown.bs.modal", function (event) {
  if (event.relatedTarget.getAttribute("tipo") == "Insumo")
    this.querySelector(
      '.modal-body div.row[data-tipo="insumo"]'
    ).classList.remove("d-none");
  tipoReq = event.relatedTarget.getAttribute("id");

  let obraId = document.getElementById("filtroObraId");

  fetch(`${rutaAjax}app/Ajax/RequisicionAjax.php?obraId=${obraId.value}`, {
    method: "GET", // *GET, POST, PUT, DELETE, etc.
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .catch((error) => console.log("Error:", error))
    .then((data) => {
      if (data.error) {
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(data.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        elementErrorValidacion.querySelector("ul").appendChild(elementList);

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }
      $("#modalCrearRequiInsumoIndirecto_folio").val(data);
    });
});


$(elementModalCrearRequiInsumoIndirecto).on(
  "click",
  "button.btnGuardar",
  function (event) {
    let elementErrorValidacion =
      elementModalCrearRequiInsumoIndirecto.querySelector(".error-validacion");
    elementErrorValidacion.querySelector("ul").innerHTML = "";
    $(elementErrorValidacion).addClass("d-none");

    let btnGuardar = this;
    // Petición Ajax para guardar la requisición
    let token = $('input[name="_token"]').val();
    let datos = new FormData(
      tipoReq == "btnCrearRequiIndirecto"
        ? document.getElementById("formCrearRequiIndirectoSend")
        : document.getElementById("formCrearRequiInsumoSend")
    );
    let tipoRequisicion = document.getElementById(
      "modalCrearRequiInsumoIndirecto_tipoReq"
    );
    let fechaRequerida = document.getElementById(
      "modalCrearRequiInsumoIndirecto_fechaRequerida"
    );
    let direccion = document.getElementById(
      "modalCrearRequiInsumoIndirecto_direccion"
    );
    let especificaciones = document.getElementById(
      "modalCrearRequiInsumoIndirecto_especificaciones"
    );
    let obraId = document.getElementById("filtroObraId");
    let divisaId = document.getElementById("divisa");
    let folio = document.getElementById("modalCrearRequiInsumoIndirecto_folio");
    let categoriaId = document.getElementById("categoriaId")
    let presupuesto = document.getElementById("filtroPresupuesto").value;
    datos.append("folio", folio.value);
    datos.append("fk_IdObra", obraId.value);
    datos.append("divisa", divisaId.value);
    datos.append("_token", token);
    datos.append("tipoRequisicion", tipoRequisicion.value);
    datos.append("fechaRequerida", fechaRequerida.value);
    datos.append("direccion", direccion.value);
    datos.append("especificaciones", especificaciones.value);
    datos.append("categoriaId", categoriaId.value);
    datos.append("presupuesto", presupuesto)

    datos.append("accion", "crear");

    $.ajax({
      url: rutaAjax + "app/Ajax/RequisicionAjax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: () => {
        $(btnGuardar).prop("disabled", true);
        // loading();
      },
    })
      .done(function (respuesta) {
        if (respuesta.error) {
          if (respuesta.errors) {
            // console.log(Object.keys(respuesta.errors))
            let errors = Object.values(respuesta.errors);

            // respuesta.errors.forEach( (item, index) => {
            errors.forEach((item) => {
              let elementList = document.createElement("li"); // prepare a new li DOM element
              let newContent = document.createTextNode(item);
              elementList.appendChild(newContent); //añade texto al div creado.
              elementErrorValidacion
                .querySelector("ul")
                .appendChild(elementList);
            });
          } else {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(respuesta.errorMessage);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          }

          $(elementErrorValidacion).removeClass("d-none");

          return;
        }

        $("#modalCrearRequiInsumoIndirecto").modal("hide");
        crearToast(
          "bg-success",
          "Crear Requisicion",
          "OK",
          respuesta.respuestaMessage
        );

        let tabla =
          tipoReq == "btnCrearRequiIndirecto"
            ? "#tablaRequiIndirectoDetalles tr"
            : "#tablaRequiInsumoDetalles tr";
        $(tabla).not(":first").remove();
        $("#modalCrearRequiInsumoIndirecto_periodos").val("");
        document.getElementById("btnFiltrar").click();

        $("#btnCrearRequiIndirecto").prop("disabled", true);
      })
      .fail(function (error) {
        // console.log("*** Error ***");
        // console.log(error);
        // console.log(error.responseText);
        // console.log(error.responseJSON);
        // console.log(error.responseJSON.message);
        console.log(error);
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(error.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
        $(elementErrorValidacion).removeClass("d-none");
      })
      .always(function () {
        // stopLoading();
        $(btnGuardar).prop("disabled", false);
      });
  }
);

$(elementModalCrearRequisicionPersonal).on("show.bs.modal", function (event) {
  if (event.relatedTarget.getAttribute("tipo") == "Insumo")
    this.querySelector(
      '.modal-body div.row[data-tipo="insumo"]'
    ).classList.remove("d-none");
  else
    this.querySelector(
      '.modal-body div.row[data-tipo="indirecto"]'
    ).classList.remove("d-none");
});

$(elementModalCrearRequisicionPersonal).on("shown.bs.modal", function (event) {
  if (tipo == "Insumo") {
    this.querySelector("#modalCrearRequisicionPersonal_insumoTipoId").value =
      tipoInsumo;
    this.querySelector("#modalCrearRequisicionPersonal_codigo").value = codigo;
  } else {
    this.querySelector("#modalCrearRequisicionPersonal_indirectoTipoId").value =
      tipoIndirecto;
    this.querySelector("#modalCrearRequisicionPersonal_numero").value = numero;
  }

  this.querySelector("#modalCrearRequisicionPersonal_descripcion").value =
    descripcion;
  $("#modalCrearRequisicionPersonal_unidad").val(unidad);

  this.querySelector("#modalCrearRequisicionPersonal_unidadId").value =
    unidadId;
});

// ****************************************************
// *** Eventos y Funciones de la pestaña Indirectos ***
// ****************************************************

$("#tablaIndirectos").on("click", "tr td.clickeable", function (e) {
  let valorCelda = $(this).closest("tr").find("td:eq(0)").text().trim(); // Reemplaza '0' con el índice de la columna deseada
  let indiceColumna = $(this).index();

  let rows = indirectos.filter(function (objeto) {
    return objeto.numero == valorCelda;
  });
  let contenidoHTML = "";
  let openModalButton = document.getElementById("openModalDetails");
  let modalTitle = document.getElementById("ModalDetallesLabel");
  let modalContent = document.getElementById("modalDetallesContent");
  modalTitle.textContent = "Semana " + (indiceColumna - 4);
  openModalButton.click();
  if (rows[0].arrayRequis !== null) {
    let requis = rows[0].arrayRequis.filter(function (row) {
      return row.periodo == indiceColumna - 4;
    });
    requis.forEach(function (dato) {
      contenidoHTML +=
        "<a target='_blank' href='" +
        dato.rutas +
        "'> Requisicion " +
        dato.folio +
        "</a> <br>";
    });
  }
  if (rows[0].arrayNominas !== null) {
    let requis = rows[0].arrayNominas.filter(function (row) {
      return row.periodo == indiceColumna - 4;
    });
    requis.forEach(function (dato) {
      contenidoHTML +=
        "<a target='_blank' href='" + dato.rutas + "'> Nomina </a> <br>";
    });
  }
  modalContent.innerHTML =
    contenidoHTML == "" ? "No hay requisiciones" : contenidoHTML;
});

// Limpia la tabla y crea el header
function cleanTableIndirectos() {
  $(tableListIndirectos).DataTable().destroy();
  dataTableIndirectos = null;
  $("#tablaIndirectos thead tr").html("");
  $("#tablaIndirectos thead tr").append('<th scope="col">Tipo</th>');
  $("#tablaIndirectos thead tr").append(
    '<th scope="col" style="min-width: 64px;">Número</th>'
  );
  $("#tablaIndirectos thead tr").append(
    '<th scope="col" style="min-width: 192px;">Descripción</th>'
  );
  $("#tablaIndirectos thead tr").append(
    '<th scope="col" style="min-width: 80px;">Unidad</th>'
  );
  $("#tablaIndirectos thead tr").append(
    '<th scope="col" style="min-width: 80px;">Cantidad</th>'
  );
  $("#tablaIndirectos thead tr").append(
    '<th scope="col" style="min-width: 80px;">Presupuesto</th>'
  );
  tableListIndirectos.querySelector("tbody").innerHTML = "";
}

// Guardar indirecto
$("#modalCrearIndirecto button.btnGuardar").on("click", function (e) {
  let elementErrorValidacion =
    elementModalCrearIndirecto.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let btnGuardar = this;

  // Petición Ajax para guardar el indirecto
  let token = $('input[name="_token"]').val();

  let datos = new FormData(document.getElementById("formIndirectosSend"));
  datos.append("_token", token);
  datos.append("accion", "crear");

  $.ajax({
    url: rutaAjax + "app/Ajax/IndirectoAjax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    beforeSend: () => {
      $(btnGuardar).prop("disabled", true);
      // loading();
    },
  })
    .done(function (respuesta) {
      if (respuesta.error) {
        if (respuesta.errors) {
          // console.log(Object.keys(respuesta.errors))
          let errors = Object.values(respuesta.errors);

          // respuesta.errors.forEach( (item, index) => {
          errors.forEach((item) => {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(item);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          });
        } else {
          let elementList = document.createElement("li"); // prepare a new li DOM element
          let newContent = document.createTextNode(respuesta.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector("ul").appendChild(elementList);
        }

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      $(elementModalCrearIndirecto).modal("hide");
      crearToast(
        "bg-success",
        "Crear Indirecto",
        "OK",
        respuesta.respuestaMessage
      );

      $("#modalCrearIndirecto_indirectoTipoId").val("");
      $("#modalCrearIndirecto_indirectoTipoId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalCrearIndirecto_numero").val("");
      $("#modalCrearIndirecto_descripcion").val("");
      $("#modalCrearIndirecto_unidadId").val("");
      $("#modalCrearIndirecto_unidadId").trigger("change.select2"); // Notify only Select2 of changes

      document.getElementById("btnFiltrar").click();
    })
    .fail(function (error) {
      // console.log("*** Error ***");
      // console.log(error);
      // console.log(error.responseText);
      // console.log(error.responseJSON);
      // console.log(error.responseJSON.message);

      let elementList = document.createElement("li"); // prepare a new li DOM element
      let newContent = document.createTextNode(error.errorMessage);
      elementList.appendChild(newContent); //añade texto al div creado.
      elementErrorValidacion
        .querySelector("ul")
        .appendChild(
          "Ocurrió un error al intentar guardar el indirecto, de favor actualice o vuelva a cargar la página e intente de nuevo"
        );
      $(elementErrorValidacion).removeClass("d-none");
    })
    .always(function () {
      // stopLoading();
      $(btnGuardar).prop("disabled", false);
    });
});

$("#modalAgregarIndirecto button#btnBuscarIndirecto").on("click", function (e) {
  let elementErrorValidacion =
    elementModalAgregarIndirecto.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let tableList = document.getElementById("tablaSeleccionarIndirectos");
  $(tableList).DataTable().destroy();
  tableList.querySelector("tbody").innerHTML = "";

  let obraId = document.getElementById("filtroObraId");
  // if ( insumoTipoId == '' ) return;

  fetch(`${rutaAjax}app/Ajax/IndirectoAjax.php?obraId=${obraId.value}`, {
    method: "GET", // *GET, POST, PUT, DELETE, etc.
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .catch((error) => console.log("Error:", error))
    .then((data) => {
      if (data.error) {
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(data.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        elementErrorValidacion.querySelector("ul").appendChild(elementList);

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      dataTableSeleccionarIndirectos = $(tableList).DataTable({
        autoWidth: false,
        responsive:
          parametrosTableList.responsive === undefined
            ? true
            : parametrosTableList.responsive,
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
          row.classList.add("seleccionable");
        },

        language: LENGUAJE_DT,
        aaSorting: [],
      }); // $(tableListResumen).DataTable({

      $(elementModalBuscarIndirecto).modal("show");
    }); // .then( data => {
});

dataTableSeleccionarIndirectos.on(
  "click",
  "tbody tr.seleccionable",
  function () {
    let data = dataTableSeleccionarIndirectos.row(this).data();
    const indirectoId = document.getElementById(
      "modalAgregarIndirecto_indirectoId"
    );
    const insumotipo = document.getElementById(
      "modalAgregarIndirecto_indirectoTipoId"
    );
    const codigo = document.getElementById("modalAgregarIndirecto_codigo");
    const descripcion = document.getElementById(
      "modalAgregarIndirecto_descripcion"
    );
    const unidad = document.getElementById("modalAgregarIndirecto_unidadId");

    indirectoId.value = data.id;
    insumotipo.value = data.indirectoTipoId;
    codigo.value = data.codigo;
    descripcion.value = data.descripcion;
    unidad.value = data.unidadId;

    $("#modalAgregarIndirecto_indirectoTipoId").trigger("change");
    $("#modalAgregarIndirecto_unidadId").trigger("change");

    $(elementModalBuscarIndirecto).modal("hide");
  }
);

// Agregar Insumo a obra
$("#modalAgregarIndirecto button.btnAgregar").on("click", function (e) {
  let elementErrorValidacion =
    elementModalAgregarIndirecto.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let btnAgregar = this;
  // Petición Ajax para insertar el insumo a la obra
  let token = $('input[name="_token"]').val();
  let datos = new FormData(document.getElementById("formIndirectoSendCreate"));
  let idObra = document.getElementById("filtroObraId");
  let idIndirecto = document.getElementById(
    "modalAgregarIndirecto_indirectoId"
  );
  datos.append("_token", token);
  datos.append("obraId", idObra.value);
  datos.append("accion", "agregar");
  datos.append("indirectoId", idIndirecto.value);
  $.ajax({
    url: rutaAjax + "app/Ajax/ObraAjax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    beforeSend: () => {
      $(btnAgregar).prop("disabled", true);
      // loading();
    },
  })
    .done(function (respuesta) {
      if (respuesta.error) {
        if (respuesta.errors) {
          // console.log(Object.keys(respuesta.errors))
          let errors = Object.values(respuesta.errors);

          // respuesta.errors.forEach( (item, index) => {
          errors.forEach((item) => {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(item);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          });
        } else {
          let elementList = document.createElement("li"); // prepare a new li DOM element
          let newContent = document.createTextNode(respuesta.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector("ul").appendChild(elementList);
        }

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      $("#modalAgregarIndirecto").modal("hide");
      crearToast(
        "bg-success",
        "Crear Directo",
        "OK",
        respuesta.respuestaMessage
      );

      $("#modalAgregarIndirecto_indirectoTipoId").val("");
      $("#modalAgregarIndirecto_indirectoTipoId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalAgregarIndirecto_codigo").val("");
      $("#modalAgregarIndirecto_descripcion").val("");
      $("#modalAgregarIndirecto_unidadId").val("");
      $("#modalAgregarIndirecto_unidadId").trigger("change.select2"); // Notify only Select2 of changes
      $("#modalAgregarIndirecto_cantidad").val("");
      $("#modalAgregarIndirecto_presupuesto").val("");
      document.getElementById("btnFiltrar").click();
    })
    .fail(function (error) {
      // console.log("*** Error ***");
      // console.log(error);
      // console.log(error.responseText);
      // console.log(error.responseJSON);
      // console.log(error.responseJSON.message);
      // console.log(error);
      let elementList = document.createElement("li"); // prepare a new li DOM element
      let newContent = document.createTextNode(error.errorMessage);
      elementList.appendChild(newContent); //añade texto al div creado.
      // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
      $(elementErrorValidacion).removeClass("d-none");
    })
    .always(function () {
      // stopLoading();
      $(btnAgregar).prop("disabled", false);
    });
});

// Eliminar la partida agregada
$(tableRequiIndirectoDetalles).on("click", "button.eliminar", function (event) {
  this.parentElement.parentElement.remove();

  // Renumerar las partidas
  let tableRequisicionDetalles =
    tableRequiIndirectoDetalles.querySelector("tbody");
  let registros = tableRequisicionDetalles.querySelectorAll("tr");
  registros.forEach((registro, index) => {
    registro.setAttribute("partida", index + 1);
    registro.querySelector("td[partida] span").innerHTML = index + 1;
  });

  if (registros.length == 0)
    $("#btnCrearRequiIndirecto").prop("disabled", true);
});

// *************************************
// *** Eventos y Funciones Generales ***
// *************************************

$("#modalCrearRequiInsumoIndirecto_periodos").on("change", function () {
  const semana = this.value;
  let fechas = getWeekDates(fechaInicioGlobal, semana);

  $("#reservationdate").daterangepicker({
    startDate: fechas.start,
    endDate: fechas.end,
  });
});

$("#modalImportarPlantilla button.btnImportarPartida").on(
  "click",
  function (e) {
    let modalAgregarSemana = document.getElementById("modalAgregarSemana");
    let elementErrorValidacion =
      modalAgregarSemana.querySelector(".error-validacion");
    elementErrorValidacion.querySelector("ul").innerHTML = "";
    $(elementErrorValidacion).addClass("d-none");

    let token = $('input[name="_token"]').val();
    let datos = new FormData();
    let plantilla = $("#modalImportarPlantilla_plantilla").val();
    let id = $("#filtroObraId").val();
    datos.append("plantilla", plantilla);
    datos.append("obra", id);
    datos.append("_token", token);
    datos.append("accion", "importarPlantilla");

    let btnAgregar = this;
    $.ajax({
      url: rutaAjax + "app/Ajax/PlantillaAjax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: () => {
        $(btnAgregar).prop("disabled", true);
        // loading();
      },
    })
      .done(function (respuesta) {
        // console.log(respuesta)
        if (respuesta.error) {
          crearToast(
            "bg-error",
            "Importar Plantilla",
            "Error",
            respuesta.errorMessage
          );

          return;
        }

        $("#modalImportarPlantilla").modal("hide");
        crearToast(
          "bg-success",
          "Importar Plantilla",
          "OK",
          respuesta.respuestaMessage
        );

        $("#modalImportarPlantilla_plantilla").val(0).change();

        document.getElementById("btnFiltrar").click();
      })
      .fail(function (error) {
        // console.log("*** Error ***");
        // console.log(error);
        // console.log(error.responseText);
        // console.log(error.responseJSON);
        // console.log(error.responseJSON.message);
        // console.log(error);
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(error.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
        $(elementErrorValidacion).removeClass("d-none");
      })
      .always(function () {
        // stopLoading();
        $(btnAgregar).prop("disabled", false);
      });
  }
);

$("#modalAgregarSemana button.btnAgregarSemana").on("click", function (e) {
  let modalAgregarSemana = document.getElementById("modalAgregarSemana");
  let elementErrorValidacion =
    modalAgregarSemana.querySelector(".error-validacion");
  elementErrorValidacion.querySelector("ul").innerHTML = "";
  $(elementErrorValidacion).addClass("d-none");

  let token = $('input[name="_token"]').val();
  let datos = new FormData();
  let semana = $("#modalAgregarSemana_semana").val();
  let id = $("#filtroObraId").val();
  datos.append("id", id);
  datos.append("semanaExtra", semana);
  datos.append("_token", token);
  datos.append("accion", "agregarSemana");

  let btnAgregar = this;
  $.ajax({
    url: rutaAjax + "app/Ajax/ObraAjax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    beforeSend: () => {
      $(btnAgregar).prop("disabled", true);
      // loading();
    },
  })
    .done(function (respuesta) {
      // console.log(respuesta)
      if (respuesta.error) {
        if (respuesta.errors) {
          // console.log(Object.keys(respuesta.errors))
          let errors = Object.values(respuesta.errors);

          // respuesta.errors.forEach( (item, index) => {
          errors.forEach((item) => {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(item);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          });
        } else {
          let elementList = document.createElement("li"); // prepare a new li DOM element
          let newContent = document.createTextNode(respuesta.errorMessage);
          elementList.appendChild(newContent); //añade texto al div creado.
          elementErrorValidacion.querySelector("ul").appendChild(elementList);
        }

        $(elementErrorValidacion).removeClass("d-none");

        return;
      }

      $("#modalAgregarSemana").modal("hide");
      crearToast(
        "bg-success",
        "Crear Requisicion de personal",
        "OK",
        respuesta.respuestaMessage
      );

      $("#modalAgregarSemana_semana").val("");

      document.getElementById("btnFiltrar").click();
    })
    .fail(function (error) {
      // console.log("*** Error ***");
      // console.log(error);
      // console.log(error.responseText);
      // console.log(error.responseJSON);
      // console.log(error.responseJSON.message);
      // console.log(error);
      let elementList = document.createElement("li"); // prepare a new li DOM element
      let newContent = document.createTextNode(error.errorMessage);
      elementList.appendChild(newContent); //añade texto al div creado.
      // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
      $(elementErrorValidacion).removeClass("d-none");
    })
    .always(function () {
      // stopLoading();
      $(btnAgregar).prop("disabled", false);
    });
});

$("#modalCrearRequisicionPersonal button.btnCrearRequisicionPersonal").on(
  "click",
  function (event) {
    let modalCrearRequisicionPersonal = document.getElementById(
      "modalCrearRequisicionPersonal"
    );
    let elementErrorValidacion =
      modalCrearRequisicionPersonal.querySelector(".error-validacion");
    elementErrorValidacion.querySelector("ul").innerHTML = "";
    $(elementErrorValidacion).addClass("d-none");

    let token = $('input[name="_token"]').val();
    let datos = new FormData(
      document.getElementById("formAgregarRequisicionPersonalSend")
    );
    let obraId = $("#filtroObraId").val();

    datos.append("fk_obraDetalleId", obraDetalleId);
    datos.append("obraId", obraId);
    datos.append("_token", token);
    datos.append("accion", "crear");

    let btnAgregar = this;

    $.ajax({
      url: rutaAjax + "app/Ajax/RequisicionPersonalAjax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: () => {
        $(btnAgregar).prop("disabled", true);
        // loading();
      },
    })
      .done(function (respuesta) {
        // console.log(respuesta)
        if (respuesta.error) {
          if (respuesta.errors) {
            // console.log(Object.keys(respuesta.errors))
            let errors = Object.values(respuesta.errors);

            // respuesta.errors.forEach( (item, index) => {
            errors.forEach((item) => {
              let elementList = document.createElement("li"); // prepare a new li DOM element
              let newContent = document.createTextNode(item);
              elementList.appendChild(newContent); //añade texto al div creado.
              elementErrorValidacion
                .querySelector("ul")
                .appendChild(elementList);
            });
          } else {
            let elementList = document.createElement("li"); // prepare a new li DOM element
            let newContent = document.createTextNode(respuesta.errorMessage);
            elementList.appendChild(newContent); //añade texto al div creado.
            elementErrorValidacion.querySelector("ul").appendChild(elementList);
          }

          $(elementErrorValidacion).removeClass("d-none");

          return;
        }

        $("#modalCrearRequisicionPersonal").modal("hide");
        crearToast(
          "bg-success",
          "Crear Requisicion de personal",
          "OK",
          respuesta.respuestaMessage
        );

        $("#modalCrearRequisicionPersonal_descripcion").val("");
        $("#modalCrearRequisicionPersonal_unidad").val("");
        $("#modalCrearRequisicionPersonal_cantidad").val("");
        $("#modalCrearRequisicionPersonal_costo").val("");
        $("#modalCrearRequisicionPersonal_fechaInicio").val("");
        $("#modalCrearRequisicionPersonal_fechaTermina").val("");
        $("#modalCrearRequisicionPersonal_viaticos").val("");
        $("#modalCrearRequisicionPersonal_otros").val("");
        $("#modalCrearRequisicionPersonal_costoneto").val("");

        document.getElementById("btnFiltrar").click();
      })
      .fail(function (error) {
        // console.log("*** Error ***");
        // console.log(error);
        // console.log(error.responseText);
        // console.log(error.responseJSON);
        // console.log(error.responseJSON.message);
        // console.log(error);
        let elementList = document.createElement("li"); // prepare a new li DOM element
        let newContent = document.createTextNode(error.errorMessage);
        elementList.appendChild(newContent); //añade texto al div creado.
        // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
        $(elementErrorValidacion).removeClass("d-none");
      })
      .always(function () {
        // stopLoading();
        $(btnAgregar).prop("disabled", false);
      });
  }
);

function addCell(tr, content, colSpan = 1, clase = null) {
  let td = document.createElement("td");

  td.colSpan = colSpan;
  td.textContent = content;
  if (clase !== null) td.classList.add(clase);

  tr.appendChild(td);
}

$('a[data-toggle="pill"]').on("shown.bs.tab", function (event) {
  // event.target // newly activated tab
  // event.relatedTarget // previous active tab
  if (event.target.getAttribute("id") == "resumen-costos-tab")
    ajustaAlturaTablaCostosResumen();
  else if (event.target.getAttribute("id") == "listado-insumos-tab")
    ajustaAlturaTablaInsumos();
  else if (event.target.getAttribute("id") == "listado-indirectos-tab")
    ajustaAlturaTablaIndirectos();
});

// Función usada para ajustar el alto máximo de #tablaCostosResumen ( de la Pestaña Resumen )
function ajustaAlturaTablaCostosResumen() {
  if (dataTableResumen === null) return;
  setTimeout(() => {
    dataTableResumen.columns.adjust().draw();
  }, "250");
}

// Función usada para ajustar el alto máximo de #tablaInsumos ( de la Pestaña Insumos )
function ajustaAlturaTablaInsumos() {
  if (dataTableInsumos === null) return;
  setTimeout(() => {
    dataTableInsumos.columns.adjust().draw();
  }, "250");
}

// Función usada para ajustar el alto máximo de #tablaIndirectos ( de la Pestaña Indirectos )
function ajustaAlturaTablaIndirectos() {
  let elementBodyContainer = document.querySelector("body");
  let alturaDispositivo = elementBodyContainer.clientHeight;

  let elementTablaIndirectos = document.querySelector(
    "#tablaIndirectos_wrapper .dataTables_scrollBody"
  );
  if (elementTablaIndirectos === null) return;

  let coordenadasTablaIndirectos =
    elementTablaIndirectos.getBoundingClientRect();
  let elementRowInfoPaginate = document.querySelector(
    "#tablaIndirectos_wrapper"
  ).children[2];
  let coordenadasRowInfoPaginate =
    elementRowInfoPaginate.getBoundingClientRect();

  if (alturaDispositivo > coordenadasTablaIndirectos.top) {
    // elementTablaIndirectos.style.maxHeight = (alturaDispositivo - coordenadasTablaIndirectos.top - coordenadasRowInfoPaginate.height - 8)+'px';
    // elementTablaIndirectos.style.height = (alturaDispositivo - coordenadasTablaIndirectos.top - coordenadasRowInfoPaginate.height - 8)+'px';
  }

  if (dataTableIndirectos === null) return;
  setTimeout(() => {
    dataTableIndirectos.columns.adjust().draw();
  }, "250");
}

// Cuando cambia el tamaño del navegador
window.addEventListener("resize", function (event) {
  let tabResumenActivo = document.querySelector("#resumen-costos-tab.active");
  if (tabResumenActivo !== null) ajustaAlturaTablaCostosResumen();

  let tabInsumosActivo = document.querySelector("#listado-insumos-tab.active");
  if (tabInsumosActivo !== null) ajustaAlturaTablaInsumos();

  let tabIndirectosActivo = document.querySelector(
    "#listado-indirectos-tab.active"
  );
  if (tabIndirectosActivo !== null) ajustaAlturaTablaIndirectos();
});

//
$(
  "#modalAgregarPartida_costo_unitario, #modalAgregarPartida_cantidad, #modalAgregarPartida_IVA, #modalAgregarPartida_ISR, #modalAgregarPartida_IVA_retencion, #modalAgregarPartida_descuento"
).on("input", function () {
  let cantidad = $("#modalAgregarPartida_cantidad").val();
  let costo_unitario = $("#modalAgregarPartida_costo_unitario").val();
  // let iva = $('#modalAgregarPartida_IVA').val();
  // let iva_retenida = $('#modalAgregarPartida_IVA_retencion').val();
  // let isr = $('#modalAgregarPartida_ISR').val();
  // let descuento = $('#modalAgregarPartida_descuento').val();

  // descuento = descuento == '' ? 0 : parseFloat(descuento);
  costo_unitario = costo_unitario == "" ? 0 : costo_unitario;
  cantidad = cantidad == "" ? 0 : cantidad;

  // iva_retenida = iva_retenida == '' ? 0 : parseFloat(iva_retenida);
  // isr = isr == '' ? 0 : parseFloat(isr);
  // iva = iva == '' ? 0 : parseFloat(iva);

  let costo = 0;
  // let costo_iva = costo_unitario * (iva/100);
  // let costo_iva_retenido = costo_unitario * (iva_retenida/100);
  // let costo_isr = costo_unitario * (isr/100);
  //Cambiar isr a usar "costo_isr"
  // costo_unitario = costo_unitario + costo_iva

  if (cantidad !== 0) {
    costo = costo_unitario * cantidad;
    // let costo_iva = costo * (iva/100);
    // let costo_iva_retenido = costo * (iva_retenida/100);
    // costo = (costo + costo_iva ) - costo_iva_retenido - isr;
  }
  $("#modalAgregarPartida_costo").val(costo.toFixed(6));
});

$(
  "#modalCrearInsumo_insumoTipoId, #modalCrearIndirecto_indirectoTipoId"
).change(function (event) {
  let tipoId = this.value;
  let text = $("#" + this.id + " option:selected").text();
  let numerosYPuntos = text.match(/[0-9.]+/g);
  // Concatenamos los números y puntos encontrados en un string
  let resultado = numerosYPuntos ? numerosYPuntos.join("") : "";
  let tipo = this.name;

  $.ajax({
    url: `${rutaAjax}app/Ajax/InsumoIndirectoAjax.php?id=${tipoId}&tipo=${tipo}`,
    method: "GET",
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
  })
    .done(function (respuesta) {
      if (respuesta !== "") {
        if (tipo == "insumoTipoId") {
          $("#modalCrearInsumo_codigo").val(respuesta);
        } else {
          $("#modalCrearIndirecto_numero").val(respuesta);
        }
      } else {
        if (tipo == "insumoTipoId") {
          $("#modalCrearInsumo_codigo").val(resultado + ".1");
        } else {
          $("#modalCrearIndirecto_numero").val(resultado + ".1");
        }
      }
    })
    .fail(function (error) {
      console.log(error);
    });
});

function paginaTieneScroll() {
  return $(document).height() > $(window).height();
}
$('a[data-toggle="pill"]').on("shown.bs.tab", function (e) {
  const target = $(e.target).attr("href"); // Ej: "#listado-insumos", "#listado-indirectos"

  // Mostrar el botón si la página tiene scroll
  if (paginaTieneScroll()) {
    $('#scrollBtnContainer').show();

    // Mostrar ícono hacia abajo por defecto
    $('#scrollBtn i')
      .removeClass('fa-angle-double-up')
      .addClass('fa-angle-double-down');
  } else {
    $('#scrollBtnContainer').hide();
  }
});

function paginaTieneScroll() {
  return $(document).height() > $(window).height();
}

// Cuando se interactúa con filtroObraId, ocultar botón y cambiar a tab "Resumen"
$('#filtroObraId').on('click focus change', function() {
    $('#scrollBtnContainer').hide();

    // Cambiar al tab "Resumen"
    $('#resumen-costos-tab').tab('show');
});

// Click en el botón: bajar o subir según posición actual
$("#scrollBtn").on("click", function () {
  const isAtBottom =
    $(window).scrollTop() + $(window).height() >= $(document).height() - 10;

  if (isAtBottom) {
    // Estás al fondo → subir
    $("html, body").animate({ scrollTop: 0 }, 600);
    $("#scrollBtn i")
      .removeClass("fa-angle-double-up")
      .addClass("fa-angle-double-down");
  } else {
    // Estás arriba → bajar
    $("html, body").animate({ scrollTop: $(document).height() }, 600);
    $("#scrollBtn i")
      .removeClass("fa-angle-double-down")
      .addClass("fa-angle-double-up");
  }
});

$("#btnAgregarPresupuesto").on("click", function () {
  Swal.fire({
    title: 'Agregar Presupuesto',
    input: 'text',
    inputLabel: 'Descripción del presupuesto',
    inputPlaceholder: 'Ingrese la descripción (máx. 250 caracteres)',
    inputAttributes: {
      maxlength: 250
    },
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    inputValidator: (value) => {
      if (!value) {
        return 'La descripción es obligatoria';
      }
      if (value.length > 250) {
        return 'La descripción no puede exceder 250 caracteres';
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      let descripcion = result.value;
      let token = $('input[name="_token"]').val();
      $.ajax({
        url: rutaAjax + "app/Ajax/ObraAjax.php",
        method: "POST",
        data: {
          _token: token,
          accion: "crearPresupuesto",
          descripcion: descripcion,
          obraId: $("#filtroObraId").val()
        },
        dataType: "json",
        success: function (respuesta) {
          if (respuesta.error) {
            Swal.fire('Error', respuesta.errorMessage, 'error');
          } else {
            Swal.fire('Éxito', 'Presupuesto creado correctamente', 'success');
            $("#btnFiltrar").click();
          }
        },
        error: function () {
          Swal.fire('Error', 'Ocurrió un error al crear el presupuesto', 'error');
        }
      });
    }
  });
});

$("#btnCrearAnuncio").on("click", function () {
  const mensaje = $("#mensajeAnuncio").val();
  const fechaHora = $("#fechaHoraAnuncio").val();

  if (!mensaje || !fechaHora) {
    Swal.fire('Error', 'Por favor, complete todos los campos', 'error');
    return;
  }

  $.ajax({
    url: rutaAjax + "app/Ajax/ObraAjax.php",
    method: "POST",
    data: {
      _token: $('input[name="_token"]').val(),
      accion: "crearAnuncio",
      mensaje: mensaje,
      fechaHora: fechaHora,
      obraId: $("#filtroObraId").val()
    },
    dataType: "json"
  }).done(function (respuesta) {
    if (respuesta.error) {
      Swal.fire('Error', respuesta.errorMessage, 'error');
    } else {
      Swal.fire('Éxito', 'Anuncio creado correctamente', 'success');
      $("#tablaAnuncios tbody").append(`
        <tr>
          <td>${mensaje}</td>
          <td>${fechaHora}</td>
          <td>${respuesta.publicadoPor}</td>
        </tr>
      `);
      $("#modalAnuncio").modal("hide");
    }
  }).fail(function (error) {
    console.log(error);
  });
});

// Ejecutar solo una vez al iniciar la vista
(function checkarUsuarioRoalOnce() {
  if (document.getElementById("usuarioRoal").value) {
    // Aquí puedes poner la lógica que necesites ejecutar solo una vez
    // Por ejemplo, llamar a checkarUsuarioRoal si necesitas su valor
    console.log(checkarUsuarioRoal());
  }
})();

function checkarUsuarioRoal() {
  return document.getElementById("usuarioRoal").value;
}

