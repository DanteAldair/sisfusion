var url = "<?=base_url()?>";
var url2 = "<?=base_url()?>index.php/";

var getInfo1 = new Array(6);

$("#tabla_ingresar_13").ready(function () {

    $('#tabla_ingresar_13 thead tr:eq(0) th').each(function (i) {

        if (i != 0 && i != 8 && i != 1) {
            var title = $(this).text();
            $(this).html('<input type="text" class="textoshead"  placeholder="' + title + '"/>');
            $('input', this).on('keyup change', function () {
                if (tabla_13.column(i).search() !== this.value) {
                    tabla_13
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        }
    });

    tabla_13 = $("#tabla_ingresar_13").DataTable({
        dom: 'Brt'+ "<'row'<'col-12 col-sm-12 col-md-6 col-lg-6'i><'col-12 col-sm-12 col-md-6 col-lg-6'p>>",
        width: 'auto',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Registro estatus 13',
                title:"Registro estatus 13",
                exportOptions: {
                    columns: [1,2,3,4,5,6,7],
                    format: {
                        header: function (d, columnIdx) {
                            switch (columnIdx) {
                                case 1:
                                    return 'TIPO VENTA';
                                case 2:
                                    return 'PROYECTO';
                                case 3:
                                    return 'CONDOMINIO';
                                    break;
                                case 4:
                                    return 'LOTE';
                                    break;
                                case 5:
                                    return 'GERENTE';
                                    break;
                                case 6:
                                    return 'CLIENTE';
                                    break;
                                case 7:
                                    return 'RL';
                                break;
                            }
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf" aria-hidden="true"></i>',
                className: 'btn buttons-pdf',
                titleAttr: 'Registro estatus 13',
                title: "Registro estatus 13",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7],
                    format: {
                        header: function (d, columnIdx) {
                            switch (columnIdx) {
                                case 1:
                                    return 'TIPO VENTA';
                                case 2:
                                    return 'PROYECTO';
                                case 3:
                                    return 'CONDOMINIO';
                                    break;
                                case 4:
                                    return 'LOTE';
                                    break;
                                case 5:
                                    return 'GERENTE';
                                    break;
                                case 6:
                                    return 'CLIENTE';
                                    break;
                                case 7:
                                    return 'RL';
                                break;
                            }
                        }
                    }
                }
            }
        ],
        language: {
            url: general_base_url + "static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
        ],
        "bAutoWidth": false,
        "fixedColumns": true,
        "ordering": false,
        "columns": [
            {//0
                "width": "3%",
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '<div class="toggle-subTable"><i class="animacion fas fa-chevron-down fa-lg"></i>'
            },
            {
//1
                "data": function (d) {
                    var lblStats;

                    if (d.tipo_venta == 1) {
                        lblStats = '<span class="label label-danger">Venta Particular</span>';
                    } else if (d.tipo_venta == 2) {
                        lblStats = '<span class="label label-success">Venta normal</span>';
                    } else if (d.tipo_venta == 3) {
                        lblStats = '<span class="label label-warning">Bono</span>';
                    } else if (d.tipo_venta == 4) {
                        lblStats = '<span class="label label-primary">Donación</span>';
                    } else if (d.tipo_venta == 5) {
                        lblStats = '<span class="label label-info">Intercambio</span>';
                    } else if(d.tipo_venta==6) {
                        lblStats ='<span class="label label-info">Reubicación</span>';
                    } else if(d.tipo_venta==7) {
                        lblStats ='<span class="label label-info">Venta especial</span>';
                    } else if (d.tipo_venta == null) {
                        lblStats = '<span class="label label-info"></span>';
                    }


                    return lblStats;
                }
            },
            {
//2
                "width": "10%",
                "data": function (d) {
                    return '<p class="m-0">' + d.nombreResidencial + '</p>';
                }
            },
            {
  //3
                "width": "10%",
                "data": function (d) {
                    return '<p class="m-0">' + (d.nombreCondominio).toUpperCase();
                    +'</p>';
                }
            },
            {
     //4
                "width": "15%",
                "data": function (d) {
                    return '<p class="m-0">' + d.nombreLote + '</p>';

                }
            },
            {
        //5
                "width": "20%",
                "data": function (d) {
                    return '<p class="m-0">' + d.gerente + '</p>';
                }
            },
            {
           //6
                "width": "20%",
                "data": function (d) {
                    return '<p class="m-0">' + d.nombre + " " + d.apellido_paterno + " " + d.apellido_materno + '</p>';
                }
            },
            {
              //7
                "width": "15%",
                "data": function (d) {
                    if (d.RL == null || d.RL == ''  ){
                        return '<p class="m-0"> No definido  </p>';
                    }else{
                        return '<p class="m-0">' + d.RL + '</p>';
                    }
                
                }
            },
            {
                //8
                "width": "40%",
                "orderable": false,
                "data": function (data) {
                    var cntActions;

                    if (data.vl == '1') {
                        cntActions = 'En proceso de Liberación';

                    } else {

                        if (data.idStatusContratacion == 12 && data.idMovimiento == 42 && data.firmaRL == 'FIRMADO' && data.validacionEnganche == 'VALIDADO') {
                            cntActions = '<button href="#" data-idLote="' + data.idLote + '" data-nomLote="' + data.nombreLote + '" data-idCond="' + data.idCondominio + '"' +
                                'data-idCliente="' + data.id_cliente + '" data-fecVen="' + data.fechaVenc + '" data-ubic="' + data.ubicacion + '" data-code="' + data.cbbtton + '" ' +
                                'class="btn-data btn-green editReg" title="Registrar estatus">' +
                                '<i class="fas fa-thumbs-up"></i></button>';

                        } else if (data.idStatusContratacion == 11 && data.idMovimiento == 41 && data.validacionEnganche == 'VALIDADO') {
                            cntActions = '<button href="#" data-idLote="' + data.idLote + '" data-nomLote="' + data.nombreLote + '" data-idCond="' + data.idCondominio + '"' +
                                'data-idCliente="' + data.id_cliente + '" data-fecVen="' + data.fechaVenc + '" data-ubic="' + data.ubicacion + '" data-code="' + data.cbbtton + '" ' +
                                'class="btn-data btn-green editReg" title="Registrar estatus">' +
                                '<i class="fas fa-thumbs-up"></i></button>';

                        } /*else if (data.idStatusContratacion == 11 && data.idMovimiento == 41 && data.validacionEnganche == 'VALIDADO' && (data.firmaRL == 'NULL' || data.firmaRL == null)) {
                            cntActions = '<button href="#" data-idLote="' + data.idLote + '" data-nomLote="' + data.nombreLote + '" data-idCond="' + data.idCondominio + '"' +
                                'data-idCliente="' + data.id_cliente + '" data-fecVen="' + data.fechaVenc + '" data-ubic="' + data.ubicacion + '" ' +
                                'class="boton btn-data btn-orangeYellow" title="Sin tiempo" id="limit">' +
                                '<i class="fas fa-exclamation"></i></button>';


                        } */else if (data.idStatusContratacion == 12 && data.idMovimiento == 42 && data.firmaRL == 'FIRMADO' && (data.validacionEnganche == 'NULL' || data.validacionEnganche == null)) {
                            cntActions = '<button href="#" data-idLote="' + data.idLote + '" data-nomLote="' + data.nombreLote + '" data-idCond="' + data.idCondominio + '"' +
                                'data-idCliente="' + data.id_cliente + '" data-fecVen="' + data.fechaVenc + '" data-ubic="' + data.ubicacion + '" ' +
                                'class="boton_1 btn-data btn-orangeYellow" title="Sin tiempo" id="limi1">' +
                                '<i class="fas fa-exclamation"></i></button>';
                        } else if (data.idStatusContratacion == 10 && data.idMovimiento == 40) {
                            cntActions = '<button href="#" data-idLote="' + data.idLote + '" data-nomLote="' + data.nombreLote + '" data-idCond="' + data.idCondominio + '"' +
                                'data-idCliente="' + data.id_cliente + '" data-fecVen="' + data.fechaVenc + '" data-ubic="' + data.ubicacion + '" data-code="' + data.cbbtton + '" ' +
                                'class="btn-data btn-green editReg" title="Registrar estatus">' +
                                '<i class="fas fa-thumbs-up"></i></button>';

                        } else {
                            cntActions = 'N/A';
                        }

                    }

                    return '<div class="d-flex justify-center">'+cntActions+'</div>';

                }
            }

        ],

        columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "targets": 0
            },

        ],

        "ajax": {
            "url": general_base_url + "Contraloria/getregistroStatus13ContratacionContraloria",
            "dataSrc": "",
            "type": "POST",
            cache: false,
            "data": function (d) {
            }
        },
        "order": [[1, 'asc']]

    });

    $('#tabla_ingresar_13 tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tabla_13.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $(this).parent().find('.animacion').removeClass("fas fa-chevron-up").addClass("fas fa-chevron-down");
        } else {
            var status;
            var fechaVenc;
            if (row.data().idStatusContratacion == 12 && row.data().idMovimiento == 42) {
                status = 'Status 12 listo (Representante Legal)';
            } else if (row.data().idStatusContratacion == 11 && row.data().idMovimiento == 41) {
                status = 'Status 11 listo (Administración)';
            } else {
                status = 'N/A';
            }

            if (row.data().idStatusContratacion == 12 && row.data().idMovimiento == 42 &&
                row.data().validacionEnganche == "VALIDADO" && row.data().firmaRL == "FIRMADO") {
                fechaVenc = row.data().fechaVenc;
            } else if (row.data().idStatusContratacion == 11 && row.data().idMovimiento == 41 &&
                row.data().validacionEnganche == "VALIDADO" && row.data().firmaRL == "FIRMADO") {
                fechaVenc = row.data().fechaVenc;
            } else if (row.data().idStatusContratacion == 11 && row.data().idMovimiento == 41 &&
                row.data().validacionEnganche == "VALIDADO" && row.data().firmaRL == "NULL" || row.data().firmaRL == null) {
                fechaVenc = 'SIN FECHA FALTA FIRMA RL';
            } else if (row.data().idStatusContratacion == 12 && row.data().idMovimiento == 42 &&
                row.data().validacionEnganche == "NULL" && row.data().firmaRL == "FIRMADO" || row.data().validacionEnganche == null) {
                fechaVenc = 'SIN FECHA FALTA VALIDACION DE ENGANCHE';
            } else {
                fechaVenc = 'N/A';
            }


            var informacion_adicional2 = '<table class="table text-justify">' +
                '<tr><b>INFORMACIÓN ADICIONAL</b>:' +
                '<td style="font-size: .8em"><strong>ESTATUS: </strong>' + status + '</td>' +
                '<td style="font-size: .8em"><strong>COMENTARIO: </strong>' + row.data().comentario + '</td>' +
                '<td style="font-size: .8em"><strong>FECHA VENCIMIENTO: </strong>' + fechaVenc + '</td>' +
                '<td style="font-size: .8em"><strong>FECHA REALIZADO: </strong>' + row.data().modificado + '</td>' +
                '<td style="font-size: .8em"><strong>COORDINADOR: </strong>' + row.data().coordinador + '</td>' +
                '<td style="font-size: .8em"><strong>ASESOR: </strong>' + row.data().asesor + '</td>' +
                '</tr>' +
                '</table>';

            var informacion_adicional = '<div class="container subBoxDetail">';
            informacion_adicional += '  <div class="row">';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-sm-12 col-lg-12" style="border-bottom: 2px solid #fff; color: #4b4b4b; margin-bottom: 7px">';
            informacion_adicional += '          <label><b>Información adicional</b></label>';
            informacion_adicional += '      </div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>ESTATUS: </b>'+ status +'</label></div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>COMENTARIO: </b> ' + row.data().comentario + '</label></div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>FECHA VENCIMIENTO: </b> ' + fechaVenc + '</label></div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>FECHA REALIZADO: </b> ' + row.data().modificado + '</label></div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>COORDINADOR: </b> ' + row.data().coordinador + '</label></div>';
            informacion_adicional += '      <div class="col-12 col-sm-12 col-md-12 col-lg-12"><label><b>ASESOR: </b> ' + row.data().asesor + '</label></div>';
            informacion_adicional += '  </div>';
            informacion_adicional += '</div>';


            row.child(informacion_adicional).show();
            tr.addClass('shown');
            $(this).parent().find('.animacion').removeClass("fas fa-chevron-down").addClass("fas fa-chevron-up");
        }


    });


    $("#tabla_ingresar_13 tbody").on("click", ".editReg", function (e) {
        e.preventDefault();

        getInfo1[0] = $(this).attr("data-idCliente");
        getInfo1[1] = $(this).attr("data-nombreResidencial");
        getInfo1[2] = $(this).attr("data-nombreCondominio");
        getInfo1[3] = $(this).attr("data-idcond");
        getInfo1[4] = $(this).attr("data-nomlote");
        getInfo1[5] = $(this).attr("data-idLote");
        getInfo1[6] = $(this).attr("data-fecven");
        getInfo1[7] = $(this).attr("data-code");

        nombreLote = $(this).data("nomlote");
        $(".lote").html(nombreLote);

        $('#editReg').modal('show');

    });

});


$(document).on('click', '#save1', function (e) {
    e.preventDefault();

    var comentario = $("#comentario").val();

    var validaComent = ($("#comentario").val().length == 0) ? 0 : 1;

    var dataExp1 = new FormData();

    dataExp1.append("idCliente", getInfo1[0]);
    dataExp1.append("nombreResidencial", getInfo1[1]);
    dataExp1.append("nombreCondominio", getInfo1[2]);
    dataExp1.append("idCondominio", getInfo1[3]);
    dataExp1.append("nombreLote", getInfo1[4]);
    dataExp1.append("idLote", getInfo1[5]);
    dataExp1.append("comentario", comentario);
    dataExp1.append("fechaVenc", getInfo1[6]);


    if (validaComent == 0) {
        alerts.showNotification('top', 'right', 'Ingresa un comentario.', 'danger')
    }

    if (validaComent == 1) {

        $('#save1').prop('disabled', true);
        $.ajax({
            url: general_base_url + "Contraloria/editar_registro_lote_contraloria_proceceso13/",
            data: dataExp1,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                response = JSON.parse(data);

                if (response.message == 'OK') {
                    $('#save1').prop('disabled', false);
                    $('#editReg').modal('hide');
                    $('#tabla_ingresar_13').DataTable().ajax.reload();
                    alerts.showNotification("top", "right", "Estatus enviado.", "success");
                } else if (response.message == 'FALSE') {
                    $('#save1').prop('disabled', false);
                    $('#editReg').modal('hide');
                    $('#tabla_ingresar_13').DataTable().ajax.reload();
                    alerts.showNotification("top", "right", "El status ya fue registrado.", "danger");
                } else if (response.message == 'ERROR') {
                    $('#save1').prop('disabled', false);
                    $('#editReg').modal('hide');
                    $('#tabla_ingresar_13').DataTable().ajax.reload();
                    alerts.showNotification("top", "right", "Error al enviar la solicitud.", "danger");
                }
            },
            error: function (data) {
                $('#save1').prop('disabled', false);
                $('#editReg').modal('hide');
                $('#tabla_ingresar_13').DataTable().ajax.reload();
                alerts.showNotification("top", "right", "Error al enviar la solicitud.", "danger");
            }
        });

    }

});


$(document).on('click', '.boton', function (e) {
    var nomLote = $(this).attr("data-nomLote");
    e.preventDefault();
    var cntMessage = "<center> <h5> El lote: <b>" + nomLote + "</b> aun no se encuentra firmado por Representante Legal. </h5> </center>";
    $("#showMessageStats .modal-body").html("");
    $("#showMessageStats .modal-body").append(cntMessage);
    $('#showMessageStats').modal();
});

$(document).on('click', '.boton_1', function (e) {
    var nomLote = $(this).attr("data-nomLote");
    e.preventDefault();
    var cntMessage = "<center><h5> El lote: <b>" + nomLote + "</b> se encuentra en Validación de Enganche. </5> </center>";
    $("#showMessageStats .modal-body").html("");
    $("#showMessageStats .modal-body").append(cntMessage);
    $('#showMessageStats').modal();
});


jQuery(document).ready(function () {

    jQuery('#editReg').on('hidden.bs.modal', function (e) {
        jQuery(this).removeData('bs.modal');
        jQuery(this).find('#comentario').val('');
    })

})