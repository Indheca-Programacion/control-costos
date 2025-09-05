  let parametrosTableList = { responsive: true };

$(function () {
  let tablaCargas = document.getElementById("tablaCargas");
  let tablaMovimietoCarga = document.getElementById("tablaMovimietoCarga");

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tablaCargas != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/CargaAjax.php", "#tablaCargas");

  let idCarga = $("#idCarga").val();

  if (tablaMovimietoCarga != null)
    fActualizarListado(
      rutaAjax + `app/Ajax/CargaAjax.php?idCarga=${idCarga}`,
      "#tablaMovimietoCarga",
      parametrosTableList
    );

  function fActualizarListado(rutaAjax, idTabla, parametros = {}) {
    fetch(rutaAjax, {
      method: "GET",
      cache: "no-cache",
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
            paging: false, // Deshabilita la paginación
            searching: false, // Deshabilita la barra de búsqueda
            info: false,
            createdRow: function (row, data, index) {
              if (data.colorTexto != "")
                $("td", row).eq(3).css("color", data.colorTexto);
              if (data.colorFondo != "")
                $("td", row).eq(3).css("background-color", data.colorFondo);
            },
            language: LENGUAJE_DT,
            aaSorting: [],
          })
          .buttons()
          .container()
          .appendTo(idTabla + "_wrapper .row:eq(0)");
      });
  }

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }

  $(tablaCargas).on("click", "button.eliminar", function (e) {
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
});
