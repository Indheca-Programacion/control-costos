$(function () {
  let tableList = document.getElementById("tablaOrdenes");
  let parametrosTableList = { responsive: true };

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

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fActualizarListado(
      rutaAjax + "app/Ajax/OrdenCompraProveedorAjax.php",
      "#tablaOrdenes",
      parametrosTableList
    );

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

  $("#modalVerPagos").on("show.bs.modal", function (event) {
    const modal = $(this);
    const button = $(event.relatedTarget);
    const ordenId = button.data().id;
    const archivosContainer = modal.find("#archivosPagos");

    archivosContainer.html("<p>Cargando archivos...</p>");

    fetch(
      `${rutaAjax}app/Ajax/OrdenCompraProveedorAjax.php?accion=buscarArchivos&ordenId=${ordenId}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (
          !data.error &&
          Array.isArray(data.archivos) &&
          data.archivos.length > 0
        ) {
          const grupos = {
            1: [],
            3: [],
          };
          data.archivos.forEach((archivo) => {
            if (archivo.tipo == 1) grupos["1"].push(archivo);
            else if (archivo.tipo == 3) grupos["3"].push(archivo);
          });

          let html = `
						<div id="archivosAccordion" role="tablist" aria-multiselectable="true">
							<div class="card">
								<div class="card-header" role="tab" id="headingComprobantes">
									<h5 class="mb-0">
										<a data-toggle="collapse" href="#collapseComprobantes" aria-expanded="true" aria-controls="collapseComprobantes">
											Comprobantes de Pago
										</a>
									</h5>
								</div>
								<div id="collapseComprobantes" class="collapse show" role="tabpanel" aria-labelledby="headingComprobantes" data-parent="#archivosAccordion">
									<div class="card-body">
										${
                      grupos["1"].length > 0
                        ? `
											<ul class="list-group">
												${grupos["1"]
                          .map(
                            (archivo) => `
													<li class="list-group-item d-flex justify-content-between align-items-center">
														<a href="${archivo.ruta}" target="_blank">${archivo.titulo}</a>
														<a href="${archivo.ruta}" download class="btn btn-sm btn-primary ms-2" title="Descargar">
															<i class="fa fa-download"></i> Descargar
														</a>
													</li>
												`
                          )
                          .join("")}
											</ul>
										`
                        : "<p>No se encontraron comprobantes de pago.</p>"
                    }
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="headingFacturas">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" href="#collapseFacturas" aria-expanded="false" aria-controls="collapseFacturas">
											Facturas
										</a>
									</h5>
								</div>
								<div id="collapseFacturas" class="collapse" role="tabpanel" aria-labelledby="headingFacturas" data-parent="#archivosAccordion">
									<div class="card-body">
										${
                      grupos["3"].length > 0
                        ? `
											<ul class="list-group">
												${grupos["3"]
                          .map(
                            (archivo) => `
													<li class="list-group-item d-flex justify-content-between align-items-center">
														<a href="${archivo.ruta}" target="_blank">${archivo.titulo}</a>
														<a href="${archivo.ruta}" download class="btn btn-sm btn-primary ms-2" title="Descargar">
															<i class="fa fa-download"></i> Descargar
														</a>
													</li>
												`
                          )
                          .join("")}
											</ul>
										`
                        : "<p>No se encontraron facturas.</p>"
                    }
									</div>
								</div>
							</div>
						</div>
					`;
          archivosContainer.html(html);
        } else {
          archivosContainer.html("<p>No se encontraron archivos.</p>");
        }
      })
      .catch(() => {
        archivosContainer.html("<p>Error al cargar archivos.</p>");
      });
  });

  let ordenSelected = null;
  $("#modalAgregarFactura").on("show.bs.modal", function (event) {
    const button = $(event.relatedTarget);
    ordenSelected = button.data().id;
  });

  $("#btnSubirFacturas").on("click", function () {
    const modal = $("#modalSubirFacturas");
    const form = $("#formAgregarFactura")[0];
    const formData = new FormData(form);
    formData.append("accion", "subirFacturas");
    formData.append("requisicionId", ordenSelected);

    $.ajax({
      url: `${rutaAjax}app/Ajax/OrdenCompraProveedorAjax.php`,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        Swal.fire({
          title: "Subiendo archivos",
          text: "Por favor espere...",
          icon: "info",
          allowOutsideClick: false,
          showConfirmButton: false,
          willOpen: () => {
            Swal.showLoading();
          },
        });
      },
      success: function (response) {
        Swal.close();
        let data;
        try {
          data = typeof response === "string" ? JSON.parse(response) : response;
        } catch (e) {
          data = { error: true, mensaje: "Respuesta inválida del servidor." };
        }
        if (!data.error) {
          Swal.fire({
            icon: "success",
            title: "Éxito",
            text: "Archivos subidos correctamente.",
            showConfirmButton: true,
          }).then(() => {
            location.reload(); // Recargar la página para actualizar el listado
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.mensaje || "Error al subir archivos.",
          });
        }
      },
      error: function () {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error en la petición AJAX.",
        });
      },
    });
  });

  // 	  // *************************************************
  //   // * REFERENTE A LOS ARCHIVOS DE LOS PERMISOS
  //   // *************************************************
  //   // Subir Archivos
  //   $("#btnSubirFacturasPDF").click(function () {
  //     document.getElementById("facturaArchivosPDF").click();
  //   });

  //   $("#btnSubirFacturasXML").click(function () {
  //     document.getElementById("facturaArchivosXML").click();
  //   });

  //   $("#facturaArchivosPDF").change(function () {
  //     let archivos = this.files;
  //     if (archivos.length == 0) return;

  //     let error = false;

  //     for (let i = 0; i < archivos.length; i++) {
  //       let archivo = archivos[i];

  //       /*==========================================
  //         VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
  //         ==========================================*/

  //       if (archivo["type"] != "application/pdf") {
  //         error = true;

  //         // $("#facturaArchivos").val("");
  //         // $("div.subir-facturas span.lista-archivos").html('');

  //         Swal.fire({
  //           title: "Error en el tipo de archivo",
  //           text: '¡El archivo "' + archivo["name"] + '" debe ser PDF o XML!',
  //           icon: "error",
  //           confirmButtonText: "¡Cerrar!",
  //         });
  //       } else if (archivo["size"] > 4000000) {
  //         error = true;

  //         // $("#facturaArchivos").val("");
  //         // $("div.subir-facturas span.lista-archivos").html('');

  //         Swal.fire({
  //           title: "Error en el tamaño del archivo",
  //           text:
  //             '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
  //           icon: "error",
  //           confirmButtonText: "¡Cerrar!",
  //         });
  //       }
  //     }

  //     if (error) {
  //       $("#facturaArchivosPDF").val("");

  //       return;
  //     }

  //     for (let i = 0; i < archivos.length; i++) {
  //       let archivo = archivos[i];

  //       $("div.subir-facturas-pdf span.lista-archivos").append(
  //         '<p class="font-italic text-info mb-0"><i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" archivoId="' +
  //           i +
  //           '" style="cursor: pointer;" ></i> ' +
  //           archivo["name"] +
  //           "</p>"
  //       );
  //     }

  //     let cloneElementArchivos = this.cloneNode(true);
  //     cloneElementArchivos.removeAttribute("id");
  //     cloneElementArchivos.name = "facturaArchivosPDF[]";
  //     $("div.subir-facturas-pdf").append(cloneElementArchivos);
  //   }); // $("#facturaArchivos").change(function(){

  //   $("#facturaArchivosXML").change(function () {
  //     let archivos = this.files;
  //     if (archivos.length == 0) return;

  //     let error = false;

  //     for (let i = 0; i < archivos.length; i++) {
  //       let archivo = archivos[i];

  //       /*==========================================
  //         VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
  //         ==========================================*/
  //       if (archivo["type"] != "text/xml") {
  //         error = true;

  //         // $("#facturaArchivos").val("");
  //         // $("div.subir-facturas span.lista-archivos").html('');

  //         Swal.fire({
  //           title: "Error en el tipo de archivo",
  //           text: '¡El archivo "' + archivo["name"] + '" debe ser XML!',
  //           icon: "error",
  //           confirmButtonText: "¡Cerrar!",
  //         });
  //       } else if (archivo["size"] > 4000000) {
  //         error = true;

  //         // $("#facturaArchivos").val("");
  //         // $("div.subir-facturas span.lista-archivos").html('');

  //         Swal.fire({
  //           title: "Error en el tamaño del archivo",
  //           text:
  //             '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
  //           icon: "error",
  //           confirmButtonText: "¡Cerrar!",
  //         });
  //       }
  //     }

  //     if (error) {
  //       $("#facturaArchivosXML").val("");
  //       return;
  //     }

  //     for (let i = 0; i < archivos.length; i++) {
  //       let archivo = archivos[i];

  //       $("div.subir-facturas-xml span.lista-archivos").append(
  //         '<p class="font-italic text-info mb-0"><i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" archivoId="' +
  //           i +
  //           '" style="cursor: pointer;" ></i> ' +
  //           archivo["name"] +
  //           "</p>"
  //       );
  //     }

  //     let cloneElementArchivos = this.cloneNode(true);
  //     cloneElementArchivos.removeAttribute("id");
  //     cloneElementArchivos.name = "facturaArchivosXML[]";
  //     $("div.subir-facturas-xml").append(cloneElementArchivos);
  //   }); // $("#facturaArchivos").change(function(){

  //   // Eliminar Archivos
  //   $(document).on("click", "i.eliminarArchivo", function () {
  //     let archivoId = $(this).attr("archivoId");

  //     $("input[archivoId='" + archivoId + "']").remove();
  //     $(this).parent().remove();
  //   });

  //   // Subir Archivos
  //   $("#btnSubirFacturas").click(function () {
  //     let formulario = document.getElementById("formSend");
  //     let formData = new FormData(formulario);
  //     formData.append("accion", "subirFacturas");

  //     let archivosPDF = document.getElementById("facturaArchivosPDF");
  //     let archivosXML = document.getElementById("facturaArchivosXML");
  //     let archivosPDFLength = archivosPDF.files.length;
  //     let archivosXMLLength = archivosXML.files.length;
  //     let archivosLength = archivosPDFLength + archivosXMLLength;
  //     let error = false;

  //     if (archivosLength == 0) {
  //       Swal.fire({
  //         title: "Error en la carga de archivos",
  //         text: "¡No se han seleccionado archivos!",
  //         icon: "error",
  //         confirmButtonText: "¡Cerrar!",
  //       });
  //       return;
  //     }

  //     if (archivosPDFLength == 0) {
  //       Swal.fire({
  //         title: "Error en la carga de archivos",
  //         text: "¡No se han seleccionado archivos PDF!",
  //         icon: "error",
  //         confirmButtonText: "¡Cerrar!",
  //       });
  //       return;
  //     }

  //     if (archivosXMLLength == 0) {
  //       Swal.fire({
  //         title: "Error en la carga de archivos",
  //         text: "¡No se han seleccionado archivos XML!",
  //         icon: "error",
  //         confirmButtonText: "¡Cerrar!",
  //       });
  //       return;
  //     }

  //     if (error) return;

  //     $.ajax({
  //       url: rutaAjax + "app/Ajax/RequisicionAjax.php",
  //       type: "POST",
  //       data: formData,
  //       contentType: false,
  //       processData: false,
  //       success: function (respuesta) {
  //         if (!respuesta.error) {
  //           Swal.fire({
  //             title: "¡Los archivos se han subido correctamente!",
  //             icon: "success",
  //             confirmButtonText: "¡Cerrar!",
  //           }).then((result) => {
  //             if (result.isConfirmed) {
  //               location.reload();
  //             }
  //           });
  //         } else {
  //           Swal.fire({
  //             title: "Error en la carga de archivos",
  //             text: "¡No se han podido subir los archivos!",
  //             icon: "error",
  //             confirmButtonText: "¡Cerrar!",
  //           });
  //         }
  //       },
  //     });
  //   });
});
