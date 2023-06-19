$(document).ready(function () {
    sp.initFormExtendedDatetimepickers();
    $('.datepicker').datetimepicker({ locale: 'es' });
    setIniDatesXMonth("#beginDate", "#endDate");
    let finalBeginDate = $("#beginDate").val();
    let finalEndDate = $("#endDate").val();
    fillTable(1, finalBeginDate, finalEndDate, 0);
});

sp = { // MJ: SELECT PICKER
    initFormExtendedDatetimepickers: function () {
        $('.datepicker').datetimepicker({
            format: 'DD/MM/YYYY',
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

let titulos = [];
$('#estatusNueveTable thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos.push(title);
    $(this).html(`<input type="text" class="textoshead" placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#estatusNueveTable').DataTable().column(i).search() !== this.value)
            $('#estatusNueveTable').DataTable().column(i).search(this.value).draw();
    });
});

function fillTable(typeTransaction, beginDate, endDate) {
    generalDataTable = $('#estatusNueveTable').dataTable({
        dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width: '100%',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn btn-success buttons-excel',
                titleAttr: 'Reporte estatus 9',
                title: 'Reporte estatus 9',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    format: {
                        header: function (d, columnIdx) {
                            return ' ' + titulos[columnIdx] + ' ';
                        }
                    }
                }
            },
            {
                text: "<i class='fas fa-sync' aria-hidden='true'></i>",
                titleAttr: 'Cargar vista inicial',
                className: 'btn btn-success buttons-excel reset-initial-values',
            }
        ],
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
        ],
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        destroy: true,
        ordering: false,
        scrollX: true,
        columns: [
            { data: 'nombreResidencial' },
            { data: 'nombreCondominio' },
            { data: 'nombreLote' },
            { data: 'referencia' },
            { data: 'nombreGerente' },
            { data: 'enganche' },
            { data: 'total' },
            { data: 'modificado' },
            { data: 'nombreUsuario' },
            { data: 'fechaApartado' },
            {
                data: function (d) {
                    if (d.id_cliente_reubicacion != 0 && d.id_cliente_reubicacion != null)
                        return `<span class="label" style="background: #A3E4D7; color: #0E6251">REUBICADO</span>`;
                    else
                        return `<span class="label" style="background: #ABB2B9; color: #17202A">NO APLICA</span>`;
                }
            },
            {
                data: function (d) {
                    if (d.id_cliente_reubicacion != 0 && d.id_cliente_reubicacion != null)
                        return d.fechaAlta;
                    else
                        return 'NO APLICA';
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
                "endDate": endDate
            }
        }
    });

}

$(document).on("click", "#searchByDateRange", function () {
    let finalBeginDate = $("#beginDate").val();
    let finalEndDate = $("#endDate").val();
    fillTable(2, finalBeginDate, finalEndDate);
    $('#estatusNueveTable').removeClass('hide');
});

$(document).on("click", ".reset-initial-values", function () {
    setInitialValues();
    const fechaInicio = new Date();
    // Iniciar en este año, este mes, en el día 1
    const beginDate = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), 1);
    // END DATE
    const fechaFin = new Date();
    // Iniciar en este año, el siguiente mes, en el día 0 (así que así nos regresamos un día)
    const endDate = new Date(fechaFin.getFullYear(), fechaFin.getMonth() + 1, 0);
    $(".idLote").val('');
    $(".textoshead").val('');
    $("#beginDate").val(convertDate(beginDate));
    $("#endDate").val(convertDate(endDate));
    fillTable(1, finalBeginDate, finalEndDate);
});