$(document).ready(function () {
    console.log("We are here");
    $('.zoom').hover(function () {
        $(this).addClass('transition');
    }, function () {
        $(this).removeClass('transition');
    });

    $(".select-is-empty").removeClass("is-empty");
    $(document).on('fileselect', '.btn-file :file', function (event, numFiles, label) {
        var input = $(this).closest('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }
    });

    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
        console.log('triggered');
    });
});

function validateEmptyFields() {
    var miArray = [];
    $("#btnSubmit").attr("onclick", "").unbind("click");
    for (i = 0; i < $("#tamanocer").val(); i++) {
        if ($('#expediente1')[0].files.length === 0) {
            alerts.showNotification('top', 'right', 'Debes seleccionar un archivo', 'danger');
            $("#btnSubmit").attr("onclick", "return validateEmptyFields()");
            return false;
        } else {
            $('#btnSubmitEnviar').click();
        }
    }
}

function validateEmptyFieldsE() {
    var miArray = [];
    $("#btnSubmitE").attr("onclick", "").unbind("click");
    for (i = 0; i < $("#tamanocerE").val(); i++) {
        miArray.push(1);
        if ($('#evidenciaE')[0].files.length === 0) {
            alerts.showNotification('top', 'right', 'Debes seleccionar un archivo', 'danger');
            $("#btnSubmitE").attr("onclick", "return validateEmptyFieldsE()");
            return false;
        } else {
            $('#btnSubmitEnviarE').click();
        }
    }
}

$(document).on('click', '.revisarSolicitud', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    let nombreCliente = $itself.attr("data-nombrecliente");
    var idCliente = $itself.attr("data-idCliente");
    var nombreLote = $itself.attr("data-nombrelote");
    var idLote = $itself.attr('data-idLote');
    var evidencia = $itself.attr('data-evidencia');
    $('#idCliente').val(idCliente);
    $('#idLote').val(idLote);
    $('#id_evidencia').val(id_evidencia);
    $('#nombreLote').val(nombreLote);
    $('#evidencia_file').val(evidencia);
    var cnt;
    var extension_file = evidencia.split('.').pop();
    var path_evidences = general_base_url + '/documentos/evidencia_mktd/';
    $.post(general_base_url + "index.php/Asesor/getSolicitudEvidencia/" + id_evidencia, function (data) {
        $('#loadAuts').empty();

        cnt = '<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding: 20px 0px;">';
        cnt += '    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12"><p style="text-align: right">Fecha: ' + data[0]['fecha_creacion'] + '</p></div>';
        cnt += '    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: center">';
        if (extension_file == 'png' || extension_file == 'jpg' || extension_file == 'jpeg' || extension_file == 'gif') {
            cnt += '<center><img src="' + path_evidences + data[0]['evidencia'] + '" class="img-responsive zoom"></center>';
        } else {
            cnt += '<iframe class="responsive-iframe" src="' + path_evidences + data[0]['evidencia'] + '" style="width: 100%;height: 400px;"></iframe>';
        }
        cnt += '    </div>';
        cnt += '    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: center">';
        cnt += '        <p style="text-align: justify;padding: 10px">' + data[0]['comentario_autorizacion'] + '</p>';
        cnt += '    </div>';
        cnt += '    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-bottom: 10px">';
        cnt += '        <div class="col-md-6">';
        cnt += '            <label><input type="radio" name="accion" id="rechazoGer" value="0" required> Rechazar a gerente</label>';
        cnt += '        </div>';
        cnt += '        <div class="col-md-6">';
        cnt += '            <label><input type="radio" name="accion" id="avanzaContra" value="1" required> Aceptar y pasar a contraloría</label>';
        cnt += '        </div>';
        cnt += '        <div class="col-md-12">';
        cnt += '            <label>Escribe un comentario: </label>';
        cnt += '            <textarea style="width: 100%" class="form-control" name="comentario_cobranza" id="comentario_cobranza"></textarea>';
        cnt += '        </div>';
        cnt += '    </div>';
        cnt += '</div>';


        $('#loadAuts').append(cnt);
    }, 'json');
    document.getElementById("modal-mktd-title").innerHTML = "<b>" + nombreCliente + "<b>";
    $('#evidenciaModalRev').modal();

});

// $(document).ready(function () {
//     if ($this->session->userdata('success') == 1) {
//         alerts.showNotification("top","right","Se enviaron las autorizaciones correctamente", "success");
//         console.log("logrado correctamente");
//     } elseif ($this->session->userdata('error') == 99) {
//         alerts.showNotification("top","right","Ocurrio un error al enviar la autorización ", "danger");
//         console.log("OCURRIO UN ERROR");
//     }
// });

$(document).on('click', '.rechazar_a_gte', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    var evidencia = $itself.attr('data-evidencia');
    var nombreLote = $itself.attr('data-nombreLote');

    $('#btn_confirma_borrado').attr("data-id_evidencia", id_evidencia);
    $('#btn_confirma_borrado').attr("data-evidencia", evidencia);
    $('#btn_confirma_borrado').attr("data-nombreLote", nombreLote);


    $('#preguntaRechazar').modal();
});

$(document).on('click', '.enviar_contraloria', function (e) {
    e.preventDefault();
    $('#img_actual').html('');
    $('#comentario_contraloria').html('');
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    var evidencia = $itself.attr('data-evidencia');
    var nombreLote = $itself.attr('data-nombreLote');
    var comments = $itself.attr('data-comentario');
    $('#btn_confirma_envio_contraloria').attr("data-id_evidencia", id_evidencia);
    $('#btn_confirma_envio_contraloria').attr("data-evidencia", evidencia);
    $('#btn_confirma_envio_contraloria').attr("data-nombreLote", nombreLote);
    var img_cnt;
    var directory = general_base_url + 'static/documentos/evidencia_mktd/';
    var extension_file = evidencia.split('.').pop();
    img_cnt = '<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: center">';
    if (extension_file == 'png' || extension_file == 'jpg' || extension_file == 'jpeg' || extension_file == 'gif' || extension_file == 'webp') {
        img_cnt += '<img src="' + directory + evidencia + '" class="img-responsive zoom">';
    } else {
        img_cnt += '<iframe class="responsive-iframe" src="' + directory + evidencia + '" style="width: 100%;height: 400px;"></iframe>';
    }
    img_cnt += '</div>';
    comentario = '<label><b>Comentario contraloría: </b>' + comments + '</label>'
    $('#textoLote').text(nombreLote);
    $('#img_actual').append(img_cnt);
    $('#comentario_contraloria').append(comentario);
    $('#preguntaEnviarContraloria').modal();
});

$(document).on('click', '#btn_confirma_envio_contraloria', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    var evidencia = $itself.attr('data-evidencia');
    var nombreLote = $itself.attr('data-nombreLote');
    var comentarioEnvioContraloria = $("#comentario_envio_contraloria").val();

    var data = new FormData();

    data.append("id_evidencia", id_evidencia);
    data.append("evidencia", evidencia);
    data.append("nombreLote", nombreLote);
    data.append("comentario", comentarioEnvioContraloria);

    $.ajax({
        type: 'POST',
        url: general_base_url + 'index.php/asesor/envioContraloria',
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        beforeSend: function () {

        },
        success: function (data) {
            console.log(data);
            if (data['exe'] == 1) {
                $('#preguntaEnviarContraloria').modal("hide");
                $('#autorizarEvidencias').DataTable().ajax.reload();
                $('#checkEvidencia').DataTable().ajax.reload();
                alerts.showNotification('top', 'right', 'La evidencia ha sido enviada correctamente.', 'success');
                $("#comentario_envio_contraloria").val("");
            } else {
                alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
            }
        },
        error: function () {
            alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
        }
    });
});

$(document).on('click', '.delete_mktd', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var idLote = $itself.attr('data-idLote');
    var id_cliente = $itself.attr('data-idcliente');
    $('#id_cliente_delete').val(id_cliente);
    $('#btn_delete_mktd').attr("data-idLote", idLote);
    $('#preguntaDeleteMktd').modal();
});

$(document).on('click', '#btn_delete_mktd', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var idLote = $itself.attr('data-idLote');
    var comentario_delete_mktd = $("#comentario_delete_mktd").val();
    var idCliente_delete_mktd = $("#id_cliente_delete").val();
    var data = new FormData();
    data.append("id_lote", idLote);
    data.append("comments", comentario_delete_mktd);
    data.append("type_transaction", 2);
    data.append("id_cliente", idCliente_delete_mktd);
    $.ajax({
        type: 'POST',
        url: general_base_url + 'index.php/Comisiones/addRemoveMktd',
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        beforeSend: function () {

        },
        success: function (data) {
            if (data == 1) {
                $('#preguntaDeleteMktd').modal("hide");
                $("#comentario_delete_mktd").val("");
                $('#autorizarEvidencias').DataTable().ajax.reload();
                $('#checkEvidencia').DataTable().ajax.reload();
                alerts.showNotification('top', 'right', 'Se ha eliminado MKTD de esta venta de manera exitosa.', 'success');
            } else {
                alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
            }
        },
        error: function () {
            alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
        }
    });
});

$(document).on('click', '#btn_confirma_borrado', function (e) {
    e.preventDefault();
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    var evidencia = $itself.attr('data-evidencia');
    var nombreLote = $itself.attr('data-nombreLote');
    var comentarioEnvioGerente = $("#comentario_envio_gerente").val();
    var data = new FormData();

    data.append("id_evidencia", id_evidencia);
    data.append("evidencia", evidencia);
    data.append("nombreLote", nombreLote);
    data.append("comentario", comentarioEnvioGerente);
    $.ajax({
        type: 'POST',
        url: general_base_url + 'index.php/asesor/rechazaAGte',
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        beforeSend: function () {

        },
        success: function (data) {
            console.log(data);
            if (data['exe'] == 1) {
                $('#preguntaRechazar').modal("hide");
                $('#autorizarEvidencias').DataTable().ajax.reload();
                $('#checkEvidencia').DataTable().ajax.reload();
                alerts.showNotification('top', 'right', 'La evidencia ha sido enviada correctamente.', 'success');
            } else {
                alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
            }
        },
        error: function () {
            alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
        }
    });
});

$(document).on('click', '.reempEvCobranza', function (e) {
    e.preventDefault();
    $('#img_actual_input').empty();
    // $('#img_actual').html('');
    var $itself = $(this);
    var id_evidencia = $itself.attr('data-id_evidencia');
    var evidencia = $itself.attr('data-evidencia');
    var nombreLote = $itself.attr('data-nombreLote');
    var img_cnt;
    var directory = general_base_url + 'static/documentos/evidencia_mktd/';
    var extension_file = evidencia.split('.').pop();

    img_cnt = '<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align: center">';
    img_cnt += '    <h5>Evidencia actual: </h5>';
    if (extension_file == 'png' || extension_file == 'jpg' || extension_file == 'jpeg' || extension_file == 'gif' || extension_file == 'webp') {
        img_cnt += '<img src="' + directory + evidencia + '" class="img-responsive zoom">';
    } else {
        img_cnt += '<iframe class="responsive-iframe" src="' + directory + evidencia + '" style="width: 100%;height: 400px;"></iframe>';
    }
    img_cnt += '    <input value="' + evidencia + '" name="previousImg" type="hidden">';
    img_cnt += '</div>';
    $('#textoLote').text(nombreLote);

    $('#img_actual_input').append(img_cnt);
    $('#id_evidenciaE').val(id_evidencia);
    $("#btnSubmitE").attr("onclick", "return validateEmptyFieldsE()");
    $('#editarAutorizacionEvidencia').modal();


});

//subir_evidencia_formE
$('#subir_evidencia_formE').on('submit', function (e) {
    /**modificar está parte**/
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: general_base_url + 'index.php/asesor/updateEvidenceChatCB',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        dataType: "json",
        beforeSend: function () {
            $('#btnSubmitE').attr("disabled", true);
            $('#btnSubmitE').css("opacity", ".5");
        },
        success: function (data) {
            console.log(data);
            if (data['exe'] == 1) {
                $('#btnSubmitE').removeAttr("disabled");
                $('#btnSubmitE').css("opacity", "1");
                $('#editarAutorizacionEvidencia').modal("hide");
                //toastr.success("Se enviaron las autorizaciones correctamente");
                $('#subir_evidencia_formE').trigger("reset");
                $('#autorizarEvidencias').DataTable().ajax.reload();
                $('#checkEvidencia').DataTable().ajax.reload();
                alerts.showNotification('top', 'right', 'Se enviaron las autorizaciones correctamente', 'success');
            } else {
                $('#btnSubmitE').removeAttr("disabled");
                $('#btnSubmitE').css("opacity", "1");
                $("#btnSubmitE").attr("onclick", "return validateEmptyFieldsE()");
                //toastr.error("Asegúrate de haber llenado todos los campos mínimos requeridos");
                alerts.showNotification('top', 'right', 'Asegúrate de haber llenado todos los campos mínimos requeridos', 'danger');
            }
        },
        error: function () {
            $('#btnSubmitE').removeAttr("disabled");
            $('#btnSubmitE').css("opacity", "1");
            $("#btnSubmitE").attr("onclick", "return validateEmptyFieldsE()");
            //toastr.error("ops, algo salió mal.");
            alerts.showNotification('top', 'right', 'ops, algo salió mal, intentalo de nuevo', 'danger');
        }
    });
});

/*agregar input de buscar al header de la tabla*/
let titulos_intxt2 = [];
$('#checkEvidencia thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_intxt2.push(title);
    if (i != 14) {
        $(this).html('<input type="text" class="textoshead"  placeholder="' + title + '"/>');
        $('input', this).on('keyup change', function () {
            if ($('#checkEvidencia').DataTable().column(i).search() !== this.value) {
                $('#checkEvidencia').DataTable()
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    }
});

$(document).ready(function () {
    tablaEvidencias = $('#checkEvidencia').DataTable({
        dom: 'Brt' + "<'row'<'col-xs-12 col-sm-12 col-md-6 col-lg-6'i><'col-xs-12 col-sm-12 col-md-6 col-lg-6'p>>",
        width: "auto",
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });
                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>')
                });
            });
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Descargar archivo de Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                    format: {
                        header: function (d, columnIdx) {
                            switch (columnIdx) {
                                case 0:
                                    return 'ID LOTE';
                                    break;
                                case 1:
                                    return 'FECHA APARTADO';
                                    break;
                                case 2:
                                    return 'PLAZA';
                                    break;
                                case 3:
                                    return 'LOTE';
                                    break;
                                case 4:
                                    return 'CLIENTE';
                                    break;
                                case 5:
                                    return 'SOLICITANTE';
                                    break;
                                case 6:
                                    return 'ESTATUS';
                                    break;
                                case 7:
                                    return 'TIPO';
                                    break;
                                case 8:
                                    return 'VALIDACIÓN GERENTE';
                                    break;
                                case 9:
                                    return 'VALIDACIÓN COBRANZA';
                                    break;
                                case 10:
                                    return 'VALIDACIÓN CONTRALORÍA';
                                    break;
                                case 11:
                                    return 'RECHAZO COBRANZA';
                                    break;
                                case 12:
                                    return 'RECHAZO CONTRALORÍA';
                                    break;
                                case 13:
                                    return 'LUGAR PROSPECCIÓN';
                                    break;
                            }
                        }
                    }
                }

            },
        ],
        pagingType: "full_numbers",
        fixedHeader: true,
        language: {
            url: general_base_url + "/static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        destroy: true,
        ordering: false,
        columns: [
            {"data": "idLote"},
            {"data": "fechaApartado"},
            {"data": "plaza"},
            {"data": "nombreLote"},
            {"data": "cliente"},
            {"data": "solicitante"},
            {
                "data": function (d) {
                    var labelStatus;
                    switch (d.estatus) {
                        case '1':
                            labelStatus = '<span class="label" style="background:#3498DB;">ENVIADA A COBRANZA</span>';
                            break;
                        case '10':
                            labelStatus = '<span class="label" style="background:#CD6155;">COBRANZA RECHAZÓ LA EVIDENCIA AL GERENTE</span>';
                            break;
                        case '2':
                            labelStatus = '<span class="label" style="background:#27AE60;">ENVIADA A CONTRALORÍA</span>';
                            break;
                        case '20':
                            labelStatus = '<span class="label" style="background:#E67E22;">CONTRALORÍA RECHAZÓ LA EVIDENCIA</span>';
                            break;
                        case '3':
                            labelStatus = '<span class="label" style="background:#9B59B6;">EVIDENCIA ACEPTADA</span>';
                            break;
                        default:
                            labelStatus = '<span class="label" style="background:#808B96;">SIN ESTATUS REGISTRADO</span>';
                            break;
                    }
                    return labelStatus;
                }

            },
            {
                "data": function (d) {
                    if (d.rowType == 1) // MJ: EVIDENCA NATURAL: SIEMPRE HA SIDO MKTD
                        return "<small class='label bg-green' style='background-color: #99A3A4'>MKTD</small>";
                    if (d.rowType == 11) // MJ: CONTROVERSIA NORMAL
                        return "<small class='label bg-green' style='background-color: #45B39D'>Normal</small>";
                    else if (d.rowType == 22)// MJ: CONTROVERSIA PARA DESCUENTO
                        return "<small class='label bg-green' style='background-color: #F4D03F'>Para descuento</small>";
                    else if (d.rowType == 33)//Implementación Venta nueva
                        return "<small class='label' style='background-color: #566573'>Venta nueva</small>";
                    else if (d.rowType == 44)// MJ: CONTROVERSIA MKTD 2022
                        return "<small class='label bg-green' style='background-color: #A569BD'>MKTD 2022</small>";
                    else if (d.rowType == 55)// MJ: CONTROVERSIA CARGA MASIVA
                        return "<small class='label bg-green' style='background-color: #5DADE2'>Carga masiva</small>";
                }
            },
            {"data": "fechaValidacionGerente"},
            {"data": "fechaValidacionCobranza"},
            {"data": "fechaValidacionContraloria"},
            {"data": "fechaRechazoCobranza"},
            {"data": "fechaRechazoContraloria"},
            {"data": "lugarProspeccion"},
            {
                // "data": "fecha_creacion"
                "data": function (d) {
                    var cntActions = '';

                    cntActions += '<br><li><button href="#" title= "Ver comentarios" data-id_autorizacion="' + d.id_evidencia + '" data-idLote="' + d.idLote + '" class="bkcolor-trans border-none seeAuts"><i class="far fa-comments"></i></button></li><br>';

                    if (d.estatus == 10 || d.estatus == 20 || d.estatus == 1) { //COBRANZA RECHAZÓ LA EVIDENCIA AL GERENTE | CONTRALORÍA RECHAZÓ LA EVIDENCIA | EVIDENCIAS ENVIADAS A COBRANZA

                        // cntActions += '<li><button href="#" title= "Rechazar a gerente" data-evidencia="'+d.evidencia+'" data-nombreLote="'+d.nombreLote+'" data-id_evidencia="'+d.id_evidencia+'" class="bkcolor-trans border-none rechazar_a_gte"><i class="far fa-thumbs-down"></i></button></li><br>';

                        cntActions += '<li><button href="#" title= "Reemplazar evidencia" data-evidencia="' + d.evidencia + '" data-nombreLote="' + d.nombreLote + '" data-id_evidencia="' + d.id_evidencia + '" class="bkcolor-trans border-none reempEvCobranza"><i class="fas fa-redo-alt"></i></button></li><br>';

                        cntActions += '<li><button href="#" title= "Enviar a contraloría" data-evidencia="' + d.evidencia + '" data-nombreLote="' + d.nombreLote + '" data-id_evidencia="' + d.id_evidencia + '" data-comentario="' + d.comentario_autorizacion + '" class="bkcolor-trans border-none enviar_contraloria"><i class="far fa-paper-plane"></i></button></li><br>';

                        cntActions += '<li><button href="#" title= "Eliminar MKTD de esta venta" data-idLote="' + d.idLote + '" class="bkcolor-trans border-none delete_mktd"><i class="far fa-trash-alt"></i></button></li><br>';
                    }

                    var btns = '<div class="btn-group">' +
                        '<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Acciones" style="background: #1eb25c;">' +
                        'Acciones <span class="caret"></span>' +
                        '</button>' +
                        '<ul class="dropdown-menu min-w100" style="background-color: #008130; color: white; text-align: center; font-size: 20px">' +
                        cntActions +
                        '</ul>' +
                        '</div>';
                    return btns;
                }
            }
        ],
        columnDefs: [{
            visible: false,
            searchable: false
        }],
        ajax: {
            "url": general_base_url + "index.php/asesor/getEvidenciaGte/",
            "type": "POST",
            cache: false
        },
    });

    let titulos_intxt3 = [];
    $('#controversyTable thead tr:eq(0) th').each(function (i) {
        var title = $(this).text();
        titulos_intxt3.push(title);
        $(this).html('<input type="text" class="textoshead"  placeholder="' + title + '"/>');
        $('input', this).on('keyup change', function () {
            if ($('#controversyTable').DataTable().column(i).search() !== this.value) {
                $('#controversyTable').DataTable()
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });

    tablaControversia = $('#controversyTable').DataTable({
        dom: 'Brt' + "<'row'<'col-xs-12 col-sm-12 col-md-6 col-lg-6'i><'col-xs-12 col-sm-12 col-md-6 col-lg-6'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Descargar archivo de Excel',
            }
        ],
        pagingType: "full_numbers",
        fixedHeader: true,
        language: {
            url: general_base_url + "/static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        destroy: true,
        ordering: false,
        ajax: general_base_url + "index.php/Asesor/getControversy/",
        columns: [
            {"data": "idLote"},
            {
                // "data": "nombreLote"
                "data": function (d) {
                    if (d.nombreLote != '' || d.nombreLote != undefined || d.nombreLote != null) { // MJ: CONTROVERSIA NORMAL
                        return d.nombreLote;
                    } else {
                        return "NA";
                    }
                }
            },
            {
                "data": function (d) {
                    if (d.tipo == 1) // MJ: CONTROVERSIA NORMAL
                        return "<small class='label bg-green' style='background-color: #45B39D'>Normal</small>";
                    else if (d.tipo == 2)// MJ: CONTROVERSIA PARA DESCUENTO
                        return "<small class='label bg-green' style='background-color: #F4D03F'>Para descuento</small>";
                    else if (d.tipo == 3)//Implementación Venta nueva
                        return "<small class='label' style='background-color: #566573'>Venta nueva</small>";
                    else if (d.tipo == 4)// MJ: CONTROVERSIA MKTD 2022
                        return "<small class='label bg-green' style='background-color: #A569BD'>MKTD 2022</small>";
                    else if (d.tipo == 5)// MJ: CONTROVERSIA CARGA MASIVA
                        return "<small class='label bg-green' style='background-color: #5DADE2'>Carga masiva</small>"
                }
            },
            {"data": "comentario"},
            {"data": "fechaApartado"},
            {"data": "plaza"},
            {"data": "nombreCliente"},
            {"data": "fecha_creacion"},
            {"data": "creado_por"}
        ],
        columnDefs: [{
            visible: false,
            searchable: false
        }],
    });
    tablaEvidencias.columns.adjust().draw();
    tablaControversia.columns.adjust().draw();

    function agregarControversia() {
        let idLote = $("#inp_lote").val();

    }
});

$("#sendRespFromCB").validate({
    rules: {
        'accion': {
            required: true
        }
    },
    errorPlacement: function (error, element) {
        error.insertBefore(element);
    },
    errorElement: 'div',
    messages: {
        'accion': {
            required: "Seleccione al menos una opción"
        }
    },
    submitHandler: function (form) {
        var data = new FormData($(form)[0]);
        $.ajax({
            url: general_base_url + 'index.php/Asesor/actualizaSolEvi',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function (data) {
                if (data == 1) {
                    // $('#loader').addClass('hidden');
                    $("#evidenciaModalRev").modal('toggle');
                    $('#autorizarEvidencias').DataTable().ajax.reload();
                    alerts.showNotification('top', 'right', 'Se ha realizado el registro exitosamente', 'success');
                } else {
                    $("#evidenciaModalRev").modal('toggle');
                    alerts.showNotification('top', 'right', 'Ha ocurrido un error', 'danger');
                }
            }, error: function () {
                $("#evidenciaModalRev").modal('toggle');
                alerts.showNotification('top', 'right', 'Ha ocurrido un error', 'danger');
            }
        });
    }
});

$("#formControversias").on('submit', function (e) {
    e.preventDefault();

    var getNameForm = new FormData();
    let idLoteInput = $('#inp_lote').val();
    getNameForm.append("id_lote", idLoteInput);
    if (idLoteInput.length > 0) {
        $.ajax({
            url: general_base_url + 'index.php/Clientes/getNameLoteById/',
            data: getNameForm,
            cache: false,
            contentType: false,
            processData: false,
            type: 'POST',
            success: function (data) {
                response = JSON.parse(data);
                console.log(response);
                if (response.data.length > 0) {
                    $('#handlerIdCliente').val(response.data[0]['idCliente']);
                    $('#loteName').html("ID: <b>" + idLoteInput + "</b><br> LOTE:<b> " + response.data[0]['nombreLote'] + "</b>");
                    $('#modalConfirmRequest').modal();
                } else {
                    alerts.showNotification('top', 'right', 'El lote <b>' + idLoteInput + '</b> no existe.', 'danger');
                    $('#inp_lote').val('');
                }
            },
            error: function (data) {
                alerts.showNotification('top', 'right', 'Ocurrió un error inesperado intentalo nuevamente. ' + data, 'danger');

            }
        });
    }

}).validate({
    rules: {
        'inp_lote': {
            required: true
        }
    },
    messages: {
        'inp_lote': {
            required: 'Ingresa un ID de lote'
        }
    }
});

$(document).on('click', '.cancel_operation_addc', function () {
    console.log('se canceló la operación...');
    return false;
});


$('.acept_operation_addc').on('click', function () {
    $('#modalConfirmRequest').modal('hide');
    console.log('[OK] se realizará la operación...');
    var data = new FormData($("#formControversias")[0]);
    $.ajax({
        url: general_base_url + 'index.php/Asesor/setControversias',
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        method: 'POST',
        type: 'POST', // For jQuery < 1.9
        success: function (data) {
            if (data['resultado']) {
                alerts.showNotification('top', 'right', 'Se ha realizado el registro exitosamente.', 'success');
                $('#controversyTable').DataTable().ajax.reload();
                $("#inp_lote").val('');
                $("#controversy_type").val('');
                $("#controversy_comment").val('');
                $("#controversy_type").selectpicker('refresh');
            } else {
                alerts.showNotification('top', 'right', data['error'], 'warning');
            }
        }, error: function () {
            alerts.showNotification('top', 'right', 'Oops, algo salió mal, inténtalo de nuevo.', 'danger');
        }
    });
});

function cleanElement() { // MJ: LIMPIA EL CONTENIDO DE UN ELEMENTO
    var myElement = document.getElementById('img_actual');
    myElement.innerHTML = '';
    var myElement2 = document.getElementById('comentario_contraloria');
    myElement2.innerHTML = '';
}

function cleanComments() {
    $("#comentario_delete_mktd").val("");
}

/*agregar input de buscar al header de la tabla*/
let titulos_intxt_nuevas = [];
$('#sol_aut thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_intxt_nuevas.push(title);
    $(this).html('<input type="text" class="textoshead"  placeholder="' + title + '"/>');
    $('input', this).on('keyup change', function () {
        if ($('#sol_aut').DataTable().column(i).search() !== this.value) {
            $('#sol_aut').DataTable()
                .column(i)
                .search(this.value)
                .draw();
        }
    });
});

/*Tabla donde se dan de alta las evidencias*/
$(document).ready(function () {
    var table2;
    // let titulo_2 = [];
    // $('#sol_aut thead tr:eq(0) th').each(function (i) {
    //     if (i != 0 && i != 13) {
    //         var title = $(this).text();
    //         titulo_2.push(title);
    //     }
    // });
    table2 = $('#sol_aut').DataTable({
        dom: 'Brt' + "<'row'<'col-12 col-sm-12 col-md-6 col-lg-6'i><'col-12 col-sm-12 col-md-6 col-lg-6'p>>",
        "ordering": true,
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Evidencias a solicitar ',
                title: 'Evidencias a solicitar ',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    format: {
                        header: function (d, columnIdx) {
                            switch (columnIdx) {
                                case 0:
                                    return 'ID LOTE';
                                    break;
                                case 1:
                                    return 'CLIENTE';
                                    break;
                                case 2:
                                    return 'TELÉFONO';
                                case 3:
                                    return 'LOTE';
                                    break;
                                case 4:
                                    return 'PLAZA';
                                    break;
                                case 5:
                                    return 'ASESOR';
                                    break;
                                case 6:
                                    return 'FECHA APARTADO';
                                    break;
                                case 7:
                                    return 'TIPO';
                                    break;
                                case 8:
                                    return 'LUGAR PROSPECCIÓN';
                                    break;
                            }
                        }
                    }
                }
            }
        ],
        pagingType: "full_numbers",
        language: {
            url: general_base_url + "/static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        columnDefs: [{
            visible: false,
            searchable: false
        }],
        columns: [
            {"data": "idLote"},
            {"data": "nombreCliente"},
            {"data": "telefono1"},
            {"data": "nombreLote"},
            {"data": "sedeAsesor"},
            {"data": "nombreAsesor"},
            {"data": "fechaApartado"},
            {
                "data": function (d) {
                    if (d.rowType == 1) // MJ: EVIDENCA NATURAL: SIEMPRE HA SIDO MKTD
                        return "<small class='label bg-green' style='background-color: #99A3A4'>MKTD</small>";
                    if (d.rowType == 11) // MJ: CONTROVERSIA NORMAL
                        return "<small class='label bg-green' style='background-color: #45B39D'>Normal</small>";
                    else if (d.rowType == 22)// MJ: CONTROVERSIA PARA DESCUENTO
                        return "<small class='label bg-green' style='background-color: #F4D03F'>Para descuento</small>";
                    else if (d.rowType == 33)//Implementación Venta nueva
                        return "<small class='label' style='background-color: #566573'>Venta nueva</small>";
                    else if (d.rowType == 44)// MJ: CONTROVERSIA MKTD 2022
                        return "<small class='label bg-green' style='background-color: #A569BD'>MKTD 2022</small>";
                    else if (d.rowType == 55)// MJ: CONTROVERSIA CARGA MASIVA
                        return "<small class='label bg-green' style='background-color: #5DADE2'>Carga masiva</small>";
                }
            },
            {"data": "lugarProspeccion"},
            {
                "data": function (d) {
                    var cntActions = '';

                    cntActions += '<center><button href="#" title= "Subir evidencia" data-nombreLote="' + d.nombreLote + '" data-idCliente="' + d.id_cliente + '"  data-idLote="' + d.idLote + '" class="btn-data btn-blueMaderas addEvidenciaClient"><span class="fas fa-cloud-upload-alt"></span></button></center>';

                    cntActions += '<button href="#" title= "Eliminar MKTD de esta venta" data-idLote="' + d.idLote + '" data-idCliente="' + d.id_cliente + '" class="btn-data btn-gray delete_mktd"><span class="fas fa-trash"></span></button>';
                    return '<div class="d-flex justify-center">' + cntActions + '</div>';
                }
            }
        ],
        ajax: {
            url: general_base_url + "index.php/asesor/getClientsByMKTDG/",
            type: "POST",
            cache: false
        },
    });
    console.log(random(200));
});

function random(list) {
    var position = 0;

    for (var i = 0, l = list.length; i < l; i++) {
        const random = Math.floor((Math.random() * 1));
        const aux = list[i];
        list[i] = list[random];
        list[random] = aux;

    }
    return list[position++ % list.length];
}


$(document).on('click', '.addEvidenciaClient', function () {
    var $itself = $(this);
    var id_cliente = $itself.attr('data-idcliente');
    var id_lote = $itself.attr('data-idlote');
    var nombreLote = $itself.attr('data-nombrelote');
    console.log("id_cliente: " + id_cliente);
    console.log("id_lote: " + id_lote);

    $('#idCliente').val(id_cliente);
    $('#idLote').val(id_lote);
    $('#nombreLote').val(nombreLote);
    $('#id_sol').val(id_usuario_general);
    $("#btnSubmit").attr("onclick", "return validateEmptyFields()");
    $('#solicitarAutorizacion').modal();
});

$("#subir_evidencia_form").on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: general_base_url + '/Asesor/addEvidenceToCobranza',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('#btnSubmit').attr("disabled", "disabled");
            $('#btnSubmit').css("opacity", ".5");
            $("#btnSubmit").attr("onclick", "").unbind("click");

        },
        success: function (data) {
            if (data == 1) {
                $('#btnSubmit').removeAttr("disabled");
                $('#btnSubmit').css("opacity", "1");
                $('#solicitarAutorizacion').modal("hide");
                //toastr.success("Se enviaron las autorizaciones correctamente");
                $('#subir_evidencia_form').trigger("reset");
                $('#checkEvidencia').DataTable().ajax.reload();
                $('#sol_aut').DataTable().ajax.reload();
                alerts.showNotification('top', 'right', 'Se enviaron las autorizaciones correctamente', 'success');
            } else {
                $('#btnSubmit').removeAttr("disabled");
                $('#btnSubmit').css("opacity", "1");
                //toastr.error("Asegúrate de haber llenado todos los campos mínimos requeridos");
                alerts.showNotification('top', 'right', 'Asegúrate de haber llenado todos los campos mínimos requeridos', 'danger');
                $("#btnSubmit").attr("onclick", "return validateEmptyFields()");
            }
        },
        error: function () {
            $('#btnSubmit').removeAttr("disabled");
            $('#btnSubmit').css("opacity", "1");
            $("#btnSubmit").attr("onclick", "return validateEmptyFields()");
            //toastr.error("ops, algo salió mal.");
            alerts.showNotification('top', 'right', 'ops, algo salió mal, intentalo de nuevo', 'danger');
        }
    });
});