$(document).ready(function () {
    sp.initFormExtendedDatetimepickers();
    $('.datepicker').datetimepicker({locale: 'es'});
    setInitialValues();
    /*
    fillTable(typeTransaction, beginDate, endDate, where) PARAMS;
        typeTransaction:
            1 = ES LA PRIMERA VEZ QUE SE LLENA LA TABLA O NO SE SELECCIONÓ UN RANGO DE FECHA (MUESTRA LO DEL AÑO ACTUAL)
            2 = ES LA SEGUNDA VEZ QUE SE LLENA LA TABLA (MUESTRA INFORMACIÓN CON BASE EN EL ID DE LOTE INGRESADO)
            3 = ES LA SEGUNDA VEZ QUE SE LLENA LA TABLA (MUESTRA INFORMACIÓN CON BASE EN EL RANGO DE FECHA SELECCIONADO)
        beginDate
            FECHA INICIO
        endDate
            FECHA FIN
        where
            ID LOTE (WHEN typeTransaction VALUE IS 2 WE SEND ID LOTE VALUE)
    */
});

sp = { // MJ: SELECT PICKER
    initFormExtendedDatetimepickers: function () {
        $('.datepicker').datetimepicker({
            format: 'MM/DD/YYYY',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove',
                inline: true
            }
        });
    }
}

let num_colum_encabezado = [];
let titulos_encabezado = [];
$('#masterCobranzaTable thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    $(this).html(`<input type="text" class="textoshead" data-toggle="tooltip" data-placement="top" title="${title}" placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#masterCobranzaTable').DataTable().column(i).search() !== this.value) {
            $('#masterCobranzaTable').DataTable().column(i).search(this.value).draw();
        }
    });
    titulos_encabezado.push(title);
    num_colum_encabezado.push(i);
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "hover"
    });
});

function fillTable(typeTransaction, beginDate, endDate, where) {
    generalDataTable = $('#masterCobranzaTable').dataTable({
        dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width: "100%",
        scrollX:true,
        initComplete: function () {
            $('[data-toggle="tooltip"]').tooltip("destroy");
            $('[data-toggle="tooltip"]').tooltip({trigger: "hover"});
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                title: 'Master cobranza',
                titleAttr: 'Descargar archivo de Excel',
                exportOptions: {
                    columns: num_colum_encabezado,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezado[columnIdx] +' ';
                        }
                    }
                }
            },
            {
                text: "<i class='fa fa-refresh' aria-hidden='true'></i>",
                titleAttr: 'Cargar vista inicial',
                className: "btn btn-azure reset-initial-values",
            }
        ],
        pagingType: "full_numbers",
        fixedHeader: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
        ],
        language: {
            url: `${general_base_url}/static/spanishLoader_v2.json`,
                paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        destroy: true,
        ordering: false,
        columns: [
            {
                data: function (d) {
                    return d.idLote;
                }
            },
            {
                data: function (d) {
                    return d.nombreLote;
                }
            },
            {
                data: function (d) {
                    if(d.rec == 8){
                        return '-';
                    }else{
                        if(d.precioTotalLote == '$0.00')
                            return '<span style="color: #960034">' + d.total_sindesc + '</span>'
                        else
                            return '<span style="font-weight: 700">'+d.precioTotalLote+'</span>';
                    }
                }
            },
            {
                data: function (d) {
                    if(d.rec == 8){
                    return '-';
                    }else{
                    return d.fechaApartado;
                }
                }
            },
            {
                data: function (d) {
                    if(d.rec == 8){
                    return '-';
                    }else{
                    return d.plaza;
                }
                }
            },
            {
                data: function (d) {
                    var labelStatus;

                    if(d.rec == 8){
                            labelStatus = 'VENTA CANCELADA';
                    }else{

                    switch (d.registroComision) {
                        case 2:
                        case '2':
                            labelStatus = '<span class="label lbl-sky">SOLICITUD ENVIADA</span>';
                            break;
                        case 3:
                        default:
                            labelStatus = '<span class="label lbl-gray">SIN SOLICITAR</span>';
                            break;
                    }
                }
                    return labelStatus;
                }
            },
            {
                data: function (d) {
                    var labelStatus;
                    if(d.rec == 8){
                        labelStatus = '<span class="label lbl-warning">RECISIÓN DE CONTRATO</span>';
                    }else{
                    switch (d.estatusEvidencia) {
                        case 1:
                        case '1':
                            labelStatus = '<span class="label lbl-green">ENVIADA A COBRANZA</span>';
                            break;
                        case 10:
                        case '10':
                            labelStatus = '<span class="label lbl-warning">COBRANZA RECHAZÓ LA EVIDENCIA AL GERENTE</span>';
                            break;
                        case 2:
                        case '2':
                            labelStatus = '<span class="label lbl-sky">ENVIADA A CONTRALORÍA</span>';
                            break;
                        case 20:
                        case '20':
                            labelStatus = '<span class="label lbl-warning">CONTRALORÍA RECHAZÓ LA EVIDENCIA</span>';
                            break;
                        case 3:
                        case '3':
                            labelStatus = '<span class="label lbl-violetDeep">EVIDENCIA ACEPTADA</span>';
                            break;
                        default:
                            labelStatus = '<span class="label lbl-warning">NO SE HA INTEGRADO EVIDENCIA</span>';
                            break;
                    }
                    }
                    return labelStatus;
                }
            },
            {
                data: function (d) {
                    return d.idStatusContratacion;
                }
            },
            {
                data: function (d) {
                    var labelStatus;
                    if(d.rec == 8){
                            labelStatus = 'VENTA CANCELADA';
                    }else{
                    switch (d.idStatusLote) {
                        case 1:
                        case '1':
                            labelStatus = '<span class="label lbl-green">DISPONIBLE</span>';
                            break;
                        case 2:
                        case '2':
                            labelStatus = '<span class="label lbl-sky">CONTRATADO</span>';
                            break;
                        case 3:
                        case '3':
                            labelStatus = '<span class="label lbl-orangeYellow">APARTADO</span>';
                            break;
                        default:
                            labelStatus = '<span class="label lbl-warning">SIN ESTATUS REGISTRADO</span>';
                            break;
                    }
                }
                    return labelStatus;
                }
            },
            {
                data: function (d) {
                    var labelStatus;
                    if(d.rec == 8){
                            labelStatus = '<span class="label lbl-brown">RECISIÓN DE CONTRATO</span>';
                    }else{
                        switch (d.registroComision) {
                            case 0:
                            case '0':
                            case 2:
                            case '2':
                                labelStatus = '<span class="label" style="background:#27AE60;">SIN DISPERSAR</span>';
                                break;
                            case 7:
                            case '7':
                                labelStatus = '<span class="label lbl-warning">LIQUIDADA</span>';
                                break;
                            case 8:
                            case '8':
                            case 88:
                            case '88':
                                labelStatus = '<span class="label lbl-brown">RECISIÓN DE CONTRATO</span>';
                                break;
                            case 1:
                            case '1':
                            default:
                                labelStatus = '<span class="label lbl-violetBoots">ACTIVA</span>';
                                break;
                        }
                }
                    return labelStatus;
                }
            },
            {
                data: function (d) {
                    return d.comisionTotal;
                }
            },
            {
                data: function (d) {
                    return d.abonoDispersado;
                }
            },
            {
                data: function (d) {
                    return d.abonoPagado;
                }
            },
            {
                data: function (d) {
                    return d.lugar_prospeccion;
                }
            },
            {
                // Info
                data: function (d) {
                    let btns = '';
                    if(d.rec == 8){
                        btns = '';
                    }else{
                    btns = '<button class="btn-data btn-blueMaderas" data-idLote="' + d.idLote + '" data-registroComision="' + d.registroComision + '" id="verifyNeodataStatus" data-toggle="tooltip" data-placement="top" title="VER MÁS"></body><i class="fas fa-info"></i></button>';
                    if (d.estatusEvidencia == 3 && (d.registroComision == 0 ) && (d.idStatusContratacion == 11 || d.idStatusContratacion == 15))
                        btns += '<button class="btn-data btn-green" data-idLote="' + d.idLote + '" id="requestCommissionPayment" title="Solicitar pago"><i class="fas fa-money-bill-wave"></i></button>';
                    }
                    return '<div class="d-flex justify-center">'+btns+'</div>';
                }
                
            }
        ],
        columnDefs: [{
            visible: false,
            searchable: false
        }],
        ajax: {
            url: 'getInformation',
            type: "POST",
            cache: false,
            data: {
                "typeTransaction": typeTransaction,
                "beginDate": beginDate,
                "endDate": endDate,
                "where": where
            }
        }
    });

    $("#masterCobranzaTable tbody").on("click", "#verifyNeodataStatus", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        let tr = $(this).closest('tr');
        let row = $("#masterCobranzaTable").DataTable().row(tr);
        let lote = $(this).attr("data-idLote");
        let registro_status = $(this).attr("data-registroComision");
        let cadena = '';

        $("#modal_NEODATA .modal-body").html("");
        $("#modal_NEODATA .modal-footer").html("");

        $.getJSON(general_base_url + "ComisionesNeo/getStatusNeodata/" + lote).done(function (data) {
            if (data.length > 0) {
                switch (data[0].Marca) {
                    case 0:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>En espera de próximo abono en NEODATA de ' + row.data().nombreLote + '.</b></h4><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                    case 1:
                        if (registro_status == 0 || registro_status == 8) { //COMISION NUEVA
                            let total0 = parseFloat(data[0].Aplicado);
                            let total = 0;
                            if (total0 > 0) {
                                total = total0;
                            } else {
                                total = 0;
                            }
                            $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4>Monto registrado: <b>$' + formatMoney(data[0].Aplicado) + '</b></h4></div></div>');
                            if (parseFloat(data[0].Bonificado) > 0) {
                                cadena = '<h4>Bonificación: <b style="color:#D84B16;">$' + formatMoney(data[0].Bonificado) + '</b></h4></div></div>';
                                $("#modal_NEODATA .modal-body").append(`<input type="hidden" name="bonificacion" id="bonificacion" value="${parseFloat(data[0].Bonificado)}">`);
                            } else {
                                cadena = '<h4>Bonificación: <b>$' + formatMoney(0) + '</b></h4></div></div>';
                                $("#modal_NEODATA .modal-body").append(`<input type="hidden" name="bonificacion" id="bonificacion" value="0">`);
                            }
                        } else if (registro_status == 1) {
                            $.getJSON(general_base_url + "Comisiones/getDatosAbonadoSuma11/" + lote).done(function (data1) {
                                let total0 = parseFloat((data[0].Aplicado));
                                let total = 0;
                                if (total0 > 0) {
                                    total = total0;
                                } else {
                                    total = 0;
                                }
                                var counts = 0;
                                if (parseFloat(data[0].Bonificado) > 0) {
                                    cadena = '<h4>Bonificación: <b style="color:#D84B16;">$' + formatMoney(data[0].Bonificado) + '</b></h4>';
                                } else {
                                    cadena = '<h4>Bonificación: <b >$' + formatMoney(0) + '</b></h4>';
                                }
                                $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-6" style="text-align: center;"><h4>Monto registrado: <b>$' + formatMoney(data[0].Aplicado) + '</b></h4></div><div class="col-md-6">' + cadena + '</div></div>');
                                $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center"><h3><i class="fa fa-info-circle" style="color:gray;"></i> Saldo diponible para dispersar de <i>' + row.data().nombreLote + '</i>: <b>$' + formatMoney(total0 - (data1[0].abonado)) + '</b></h3></div></div><br>');
                            });
                        }
                        break;
                    case 2:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>No se encontró esta referencia de ' + row.data().nombreLote + '.</b></h4><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                    case 3:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>No tiene vivienda, sí hay referencia de ' + row.data().nombreLote + '.</b></h4><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                    case 4:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>No hay pagos aplicados a esta referencia de ' + row.data().nombreLote + '.</b></h4><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                    case 5:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>Referencia duplicada de ' + row.data().nombreLote + '.</b></h4><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                    default:
                        $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h4><b>Sin localizar.</b></h4><br><h5>Revisar con sistemas: ' + row.data().nombreLote + '.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
                        break;
                }
            } else {
                $("#modal_NEODATA .modal-body").append('<div class="row"><div class="col-md-12" style="text-align: center;"><h3><b>No se encontró esta referencia en NEODATA de ' + row.data().nombreLote + '.</b></h3><br><h5>Revisar con Administración.</h5></div> <div class="col-md-12"><center><img src="' + general_base_url + 'static/images/robot.gif" width="320" height="300"></center></div> </div>');
            }
        });
        $("#modal_NEODATA").modal();
    });
}

$(document).on("click", "#searchByLote", function () {
    let lote = $("#idLote").val();
    let finalBeginDate = $("#beginDate").val();
    let finalEndDate = $("#endDate").val();
    fillTable(2, finalBeginDate, finalEndDate, lote);
});

$(document).on("click", "#searchByDateRange", function () {
    let finalBeginDate = $("#beginDate").val();
    let finalEndDate = $("#endDate").val();
    fillTable(3, finalBeginDate, finalEndDate, 0);
});

function setInitialValues() {
    // BEGIN DATE
    const fechaInicio = new Date();
    // Iniciar en este año, este mes, en el día 1
    const beginDate = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), 1);
    // END DATE
    const fechaFin = new Date();
    // Iniciar en este año, el siguiente mes, en el día 0 (así que así nos regresamos un día)
    const endDate = new Date(fechaFin.getFullYear(), fechaFin.getMonth() + 1, 0);
    finalBeginDate = [beginDate.getFullYear(), ('0' + (beginDate.getMonth() + 1)).slice(-2), ('0' + beginDate.getDate()).slice(-2)].join('-');
    finalEndDate = [endDate.getFullYear(), ('0' + (endDate.getMonth() + 1)).slice(-2), ('0' + endDate.getDate()).slice(-2)].join('-');
    fillTable(1, finalBeginDate, finalEndDate, 0);
}

$(document).on("click", ".reset-initial-values", function () {
    setInitialValues();
    $(".idLote").val('');
    $(".textoshead").val('');
    $("#beginDate").val('01/01/2022');
    $("#endDate").val('01/01/2022');
});

$(document).on('click', '#requestCommissionPayment', function () {
    let lote = $(this).attr("data-idLote");
    $("#idLote").val(lote);
    $("#modalConfirmRequest").modal();
});

$(document).on('click', '#sendRequestCommissionPayment', function () {
    let lote = $("#idLote").val();
    $.ajax({
        type: 'POST',
        url: 'sendRequestPayment',
        data: {
            'idLote': lote
        },
        dataType: 'json',
        success: function (data) {
            if (data == 1) {
                $("#modalConfirmRequest").modal("hide");
                alerts.showNotification("top", "right", "El registro ha sido actualizado de manera éxitosa.", "success");
                $("#masterCobranzaTable").DataTable().ajax.reload();
            } else {
                alerts.showNotification("top", "right", "Oops, algo salió mal.", "warning");
            }
        }, error: function () {
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        }
    });
});