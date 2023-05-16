$(document).ready(function () {
    $.post(general_base_url + "Asistente_gerente/lista_proyecto", function (data) {
        var len = data.length;
        for (var i = 0; i < len; i++) {
            var id = data[i]['idResidencial'];
            var name = data[i]['descripcion'];
            $("#proyecto").append($('<option>').val(id).text(name.toUpperCase()));
        }
        $("#proyecto").selectpicker('refresh');
    }, 'json');
});

$('#proyecto').change(function () {
    index_proyecto = $(this).val();
    $("#condominio").html("");
    $(document).ready(function () {
        $.post(general_base_url + "Asistente_gerente/lista_condominio/" + index_proyecto, function (data) {
            var len = data.length;            
            for (var i = 0; i < len; i++) {
                var id = data[i]['idCondominio'];
                var name = data[i]['nombre'];
                $("#condominio").append($('<option>').val(id).text(name.toUpperCase()));
            }
            $("#condominio").selectpicker('refresh');
        }, 'json');
    });

});


$('#condominio').change(function () {
    index_condominio = $(this).val();
    $("#lote").html("");
    $(document).ready(function () {
        $.post(general_base_url + "Asistente_gerente/lista_lote/" + index_condominio, function (data) {
            var len = data.length;
            for (var i = 0; i < len; i++) {
                var id = data[i]['idLote'];
                var name = data[i]['nombreLote'];
                $("#lote").append($('<option>').val(id).text(name.toUpperCase()));
            }
            $("#lote").selectpicker('refresh');
        }, 'json');

    });

});

var titulos_encabezado = [];
var num_colum_encabezado = [];
$('#tabla_contrato_ventas thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    if (title !== 'CONTRATO' || title !== '') {
        titulos_encabezado.push(title);
        num_colum_encabezado.push(i);
    }
    let readOnly = (title == 'CONTRATO' || title == '') ? 'readOnly': '';
    let width = title=='CONTRATO' ? 'style="width: 65px;"': '';
        $(this).html(`<input    type="text"
                                ${width}
                                class="textoshead"
                                data-toggle="tooltip" 
                                data-placement="top"
                                title="${title}"
                                placeholder="${title}"
                                ${readOnly}/>`);
    $('input', this).on('keyup change', function () {
        if (tabla_contrato.column(i).search() !== this.value) {
            tabla_contrato
                .column(i)
                .search(this.value)
                .draw();
        }
    });
});

var tabla_contrato;
$('#lote').change(function () {
    index_lote = $(this).val();

    // $('#tabla_contrato_ventas').DataTable({
    tabla_contrato = $("#tabla_contrato_ventas").DataTable({
        width: 'auto',
        dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        "ajax":
        {
            "url": `${general_base_url}index.php/Asistente_gerente/get_lote_contrato/${index_lote}`,
            "dataSrc": ""
        },
        destroy: true,
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Contrato',
                title: 'Contrato',
                exportOptions: {
                    columns: num_colum_encabezado,
                    format: {
                        header: function (d, columnIdx) {
                            return ' ' + titulos_encabezado[columnIdx] + ' ';
                        }
                    }
                },

            }
        ],
        pagingType: "full_numbers",
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        processing: true,
        pageLength: 10,
        bAutoWidth: false,
        bLengthChange: false,
        scrollX: true,
        bInfo: true,
        searching: true,
        ordering: false,
        fixedColumns: true,

        "columns":
            [
                { data: 'nombreResidencial'},
                { data: 'condominio' },
                { data: 'nombreLote' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return data.nombre + ' ' + data.apellido_paterno + ' ' + data.apellido_materno;
                    },
                },
                {
                    data: function (data) {
                        return myFunctions.validateEmptyField(data.contratoArchivo);
                    }
                },
                {
                    "orderable": false,
                    "data": function (data) {
                        $('#cnt-file')
                            .html(`<h3 style="font-weight:100">
                                        Visualizando
                                        <b>
                                            ${myFunctions.validateEmptyField(data.contratoArchivo)}
                                        </b>
                                    </h3>
                                    <embed  src="${general_base_url}static/documentos/cliente/contrato/${data.contratoArchivo}"
                                            frameborder="0" 
                                            width="100%"
                                            height="500"
                                            style="height: 60vh;">
                                    </embed >`);
                        var myLinkConst = ` <center>
                                                <a type="button" data-toggle="modal" data-target="#fileViewer" class="btn-data btn-blueMaderas">
                                                    <center>
                                                        <i class="fas fa-eye" style="cursor: pointer"></i>
                                                    </center>
                                                </a>
                                            </center>`;
                        return myLinkConst;
                    }
                }
            ]

    });
    
    $("#tabla_contrato_ventas tbody").on("click", ".ver_contrato", function () {

        var tr = $(this).closest('tr');
        var row = tabla_contrato.row(tr);

        idautopago = $(this).val();

        $("#modal_contrato .modal-body").html("");
        $("#modal_contrato .modal-body").append('<div class="row"><div class="col-lg-12"><input type="file" name="autorizacion" id="autorizacion"></div></div>');
        $("#modal_contrato .modal-body")
            .append(`<div class="row">
                        <div class="col-lg-12">
                            <br>
                        </div>
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <button class="btn btn-social btn-fill btn-info">
                                <i class="fa fa-google-square"></i>
                                    SUBIR
                            </button>
                        </div>
                    </div>`);
        $("#modal_contrato").modal();
    });

});

$(window).resize(function () {
    tabla_contrato.columns.adjust();
});