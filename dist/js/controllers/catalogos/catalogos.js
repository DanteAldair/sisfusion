$(document).ready(function () {

  fillCatalogosTable();

  $.ajax({
    url: `${general_base_url}Catalogos/getOnlyCatalogos`,
    type: 'GET',
    dataType: 'json',
    success: function (data) {
        for (let i = 0; i < data.length; i++) {
            const id_catalogo = data[i].id_catalogo;
            const nombre = data[i].nombre;
            $('#id_catalogo').append($('<option>').val(id_catalogo).text(nombre));
        }
        $('#id_catalogo').selectpicker('refresh');
        $('#spiner-loader').addClass('hide');
    }
  });

  $('#estatus_n').selectpicker('refresh');
  
});

let titulos_intxt = [];

$("#catalogo_datatable thead tr:eq(0) th").each(function (i) {
  var title = $(this).text();
  titulos_intxt.push(title);
  $(this).html(
    `<input class="textoshead" data-toggle="tooltip" data-placement="top" title="${title}" placeholder="${title}"/>`
  );
  $("input", this).on("keyup change", function () {
    if ($("#catalogo_datatable").DataTable().column(i).search() !== this.value) {
      $("#catalogo_datatable").DataTable().column(i).search(this.value).draw();
    }
  });
});

//guardar opcion

$(document).on('click', '#guardarCatalogo', function(){
  var nombreInfo = $("#nombre").val();
  var catalogoInfo = $("#id_catalogo").val();
  console.log("catalogo", catalogoInfo);
  
  if(catalogoInfo == ''){
    $("#spiner-loader").addClass('hide');
    alerts.showNotification("top", "right", "Selecciona una opción", "warning");
    return;
  }

  var datos = new FormData();
  $("#spiner-loader").removeClass('hide');

  datos.append("nombre", nombreInfo)
  datos.append("id_catalogo", catalogoInfo)

  $.ajax({
      method: 'POST',
      url: general_base_url + 'Catalogos/insertNombre',
      data: datos,
      processData: false,
      contentType: false,
      success: function(data) {
          if (data == 1) {
          $('#catalogo_datatable').DataTable().ajax.reload(null, false);
          $('#OpenModal').modal('hide');
          $("#spiner-loader").addClass('hide');
          alerts.showNotification("top", "right", "Opción insertada correctamente.", "success");
          $('#nombre').val('');
          $('#id_catalogo').val('');
          }
      },
      error: function(){
          $('#OpenModal').modal('hide');
          $("#spiner-loader").addClass('hide');
          alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
      }
  });
  return;
});

//Edit CAMBIAR DE ESTATUS
$(document).on("click", ".editarCatalogos", function () {
  //tomar id's
  $("#id_catalogo_e").val($(this).attr("data-id_catalogo"));
  $("#idOpcion_e").val($(this).attr("data-id_opcion"));
  $("#estatus_n_e").val($(this).attr("data-id_estatus"));

  //mostrar el edit dentro del modal
  $("#editCatalogoModal").modal();
});

$(document).on("click", "#btn_aceptar", function () {

  //id's
  var idCatalogosEdit = $("#id_catalogo_e").val();
  var id_opcion = $("#idOpcion_e").val();
  
  //Dato del select
  var estatus = $("#estatus_n_e").val();
  //console.log(idCatalogosEdit,id_opcion, estatus);

  var datos = new FormData();
  $("#spiner-loader").removeClass("hide");

  if (estatus === 1) {
    estatus = 0;
  } else if (estatus === 0) {
    estatus = 1;
  }

  datos.append("idCatalogosEdit", idCatalogosEdit);
  datos.append("id_opcion", id_opcion);
  datos.append("estatus_n", estatus);

  $.ajax({
    method: "POST",
    url: general_base_url + "Catalogos/editarCatalogos",
    data: datos,
    processData: false,
    contentType: false,
    success: function (data) {
      if (data == 1) {
        $("#catalogo_datatable").DataTable().ajax.reload(null, false);
        $("#spiner-loader").addClass("hide"); 
        alerts.showNotification("top", "right", "Opcion editada correctamente.", "success");
        $("#id_opcion").val("");
        $("#estatus_n").val("");
        $("#idCatalogosEdit").val("");
        $("id_catalogo").val("");
        $("#editCatalogoModal").modal("hide");
      }
    },
    error: function () {
      $("#editarModel").modal("hide");
      $("#spiner-loader").addClass("hide");
      alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
    },
  });
});

//Editar Nombre

$(document).on("click", "#editar-catalogo-information", function () {
  $("#idOpcion").val($(this).attr("data-id_opcion"));
  $("#id_catalogo").val($(this).attr("data-id_catalogo"));
  $("#editarCatalogo").val($(this).attr("data-nombre"));
  $("#editCatalogModal").modal();
});

$(document).on("click", "#editOp", function () {
  var idOpcion = $("#idOpcion").val();
  var id_catalogo = $("#id_catalogo").val();
  var editarCatalogo = $("#editarCatalogo").val();
 
  var datos = new FormData();
  $("#spiner-loader").removeClass("hide");

  datos.append("idOpcion", idOpcion);
  datos.append("id_catalogo", id_catalogo);
  datos.append("editarCatalogo", editarCatalogo);

  $.ajax({
    method: "POST",
    url: general_base_url + "Catalogos/editarNombre",
    data: datos,
    processData: false,
    contentType: false,
    success: function (data) {
      console.log(data);
      
      if (data == 1) {
        $("#catalogo_datatable").DataTable().ajax.reload(null, false);
        $("#spiner-loader").addClass("hide");
        $("#editCatalogModal").modal("hide");
        alerts.showNotification("top", "right", "Opción Editada Correctamente.", "success");
        $('#idOpcion').val('');
        $('#id_catalogo').val('');
        $('#editarCatalogo').val('');
      }
    },
    error: function () {
      $("#spiner-loader").addClass("hide");
      $("#editCatalogModal").modal("hide");
      alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
    },
  });
});

//Abrir modal con botón

function openModal(){
  $("#OpenModal").modal();
}

function fillCatalogosTable() {
  //console.log("hola");
  CatalogoTable = $("#catalogo_datatable").DataTable({
    width: "100%",
    dom:
      "Brt" +
      "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
      buttons: [
        {
          extend: "excelHtml5",
          text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
          className: "btn buttons-excel",
          titleAttr: "Catálogos",
          title: "Catálogos",
          exportOptions: {
            columns: [0, 1, 2, 3, 4],
            format: {
              header: function (d, columnIdx) {
                return " " + titulos_intxt[columnIdx] + " ";
              },
            },
          },
        }, {
          text: '<i class="fas fa-plus"></i>    ',
          action: function() {
            openModal();
          }, 
          attr: {
              class: 'btn btn-azure',
              style: 'position: relative;',
          },
      
        }
      
        
      ],
      
    pagingType: "full_numbers",
    language: {
      url: general_base_url + "static/spanishLoader_v2.json",
      paginate: {
        previous: "<i class='fa fa-angle-left'>",
        next: "<i class='fa fa-angle-right'>",
      },
    },
    processing: true,
    pageLength: 10,
    bAutoWidth: true,
    bLengthChange: false,
    scrollX: true,
    bInfo: true,
    searching: true,
    ordering: false,
    fixedColumns: true,
    destroy: true,
    columns: [
      {
        data: function (d) {
          return '<p class="m-0">' + d.id_opcion + "</p>";
        },
      },
      {
        data: function (d) {
          return '<p class="m-0">' + d.id_catalogo + "</p>";
        },
      },

      {
        data: function (d) {
          return '<p class="m-0">' + d.catalogo + "</p>";
        },
      },
      {
        data: function (d) {
          return '<p class="m-0">' + d.nombre + "</p>";
        },
      },
      {
        data: function (d) {
          if (d.estatus == 1) {
            return '<center><span class="label lbl-green">ACTIVO</span><center>';
          } else {
            return '<center><span class="label lbl-warning">INACTIVO</span><center>';
          }
        },
      },
      {
        data: function (d) {
          if (d.estatus == 1) {
            //var actions = '';

            return (
            
              '<div class="d-flex justify-center"> <button class="btn-data btn-blueMaderas editarCatalogos" id="edit-catalogo-information" data-id_estatus="' + d.estatus +'" data-id_catalogo="' + d.id_catalogo +'" data-id_opcion="' + d.id_opcion + '" data-nombre="' + d.nombre + '" style="margin-right: 3px" data-toggle="tooltip" data-placement="top" title="Cambiar"><i class="fas fa-exchange-alt"></i></button>' + '<div class="d-flex justify-center"> <button class="btn-data btn-orangeYellow change-user-status editar-catalogo-information" id="editar-catalogo-information" name="editar-catalogo-information" data-id_catalogo="' + d.id_catalogo + '" data-id_opcion="' + d.id_opcion +'" data-nombre="' + d.nombre + '" style="margin-right: 3px" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-edit"></i></button>');


          } else {
            // IS NOT ACTIVE
            //var actions = '';
            return (
              '<div class="d-flex justify-center"> <button class="btn-data btn-blueMaderas editarCatalogos" id="edit-catalogo-information" data-id_estatus="' + d.estatus +'" data-id_catalogo="' + d.id_catalogo +'" data-id_opcion="' + d.id_opcion + '" data-nombre="' + d.nombre + '" style="margin-right: 3px" data-toggle="tooltip" data-placement="top" title="Cambiar"><i class="fas fa-exchange-alt"></i></button>' + '<div class="d-flex justify-center"> <button class="btn-data btn-orangeYellow change-user-status editar-catalogo-information" id="editar-catalogo-information" name="editar-catalogo-information" data-id_catalogo="' + d.id_catalogo + '" data-id_opcion="' + d.id_opcion +'" data-nombre="' + d.nombre + '" style="margin-right: 3px" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-edit"></i></button>');
          }
        },
      },
    ],
    columnDefs: [
      {
        defaultContent: "",
        targets: "_all",
        searchable: true,
        orderable: false,
      },
    ],
    ajax: {
      url: general_base_url + "Catalogos/getCatalogos",
      dataSrc: "",
      type: "GET",
      cache: false,
    },
    initComplete: function () {
      $("#spiner-loader").addClass("hide");
    },
  });

  $("#catalogo_datatable").on("draw.dt", function () {
    $('[data-toggle="tooltip"]').tooltip({
      trigger: "hover",
    });
  });
}
