$('#Jtabla thead tr:eq(0) th').each(function (i) {  
    var title = $(this).text();
    $(this).html('<input type="text" style="width:100%; background:#003D82; color:white; border: 0; font-weight: 500;" class="textoshead"  placeholder="' + title + '"/>');
    $('input', this).on('keyup change', function () {
        if ($('#Jtabla').DataTable().column(i).search() !== this.value) {
            $('#Jtabla').DataTable().column(i).search(this.value).draw();
        }
    });
});

$(document).ready(function()
{
    $.ajax(
        {
            post: "POST",
            url: general_base_url + "registroLote/getDateToday/"
        }).done(function(data)
            {
                $('#showDate').text('Lotes contratados al: '+data);
            }).fail(function()
            {
                // $('#showDate').append('<center><h3>Lotes contratados al: '+new Date().getDay()+new Date().getMonth()+new Date().getFullYear()'</h3></center>');
            });
    sp.initFormExtendedDatetimepickers();
    $('.datepicker').datetimepicker({locale: 'es'});
    setIniDatesXMonth('#beginDate','#endDate');
    fillTable(1, finalBeginDate, finalEndDate, 0);
});

sp = { //  SELECT PICKER
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

$(document).on("click", "#searchByDateRange", function () {
    let finalBeginDate = $("#beginDate").val();
    let finalEndDate = $("#endDate").val();
    fillTable(3, finalBeginDate, finalEndDate, 0);
});

var table
function fillTable(typeTransaction, beginDate, endDate, where) {
    var today = new Date();
    var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var dateTime = date+' '+time;
    table = $('#Jtabla').dataTable( {
        "ajax":
            {
                "url": general_base_url + 'registroLote/getReportData',
                "type": "POST",
                cache: false,
                data: {
                    "typeTransaction": typeTransaction,
                    "beginDate": beginDate,
                    "endDate": endDate,
                    "where": where
                }
            },
        dom: 'Brt'+ "<'row'<'col-12 col-sm-12 col-md-6 col-lg-6'i><'col-12 col-sm-12 col-md-6 col-lg-6'p>>",
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            className: 'btn buttons-excel',
            titleAttr: 'Lotes contratados al ' + dateTime ,
            title: 'Lotes contratados al ' + dateTime ,
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6],
                format: {
                    header: function (d, columnIdx) {
                        switch (columnIdx) {
                            case 0:
                                return 'LOTE';
                                break;
                            case 1:
                                return 'GERENTE';
                                break;
                            case 2:
                                return 'ASESOR';
                            case 3:
                                return 'STATUS';
                                break;
                            case 4:
                                return 'DETALLES';
                                break;
                            case 5:
                                return 'COMENTARIO';
                                break;
                            case 6:
                                return 'FECHA';
                                break;
                        }
                    }
                }
            }
        }],
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
        ],
        language: {
            url: general_base_url + "static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        columnDefs: [{
            defaultContent: "Sin especificar",
            targets: "_all",
            searchable: true,
            orderable: false
        }],
        destroy: true,
        ordering: false,
        "columns":
            [
                {data: 'nombreLote'},
                {
                    data: function (data)
                    {
                        var ge1, ge2, ge3, ge4, ge5;
                        if(data.gerente == undefined){ge1="";}else{ge1=data.gerente;};
                        if(data.gerente2 == undefined){ge2="";}else{ge2=data.gerente2;};
                        if(data.gerente3 == undefined){ge3="";}else{ge3=data.gerente3;};
                        if(data.gerente4 == undefined){ge4="";}else{ge4=data.gerente4;};
                        if(data.gerente5 == undefined){ ge5=""; }else{ge5=data.gerente5;};
                        return ge1 ;
                    }
                },
                {
                    data: function (data)
                    {
                        var as1, as2, as3, as4, as5;
                        if(data.asesor == undefined){as1="";}else{as1=data.asesor};
                        if(data.asesor2 == undefined){as2="";}else{as2=data.asesor2;};
                        if(data.asesor3 == undefined){as3="";}else{as3=data.asesor3};
                        if(data.asesor4 == undefined){as4="";}else{ as4=data.asesor4;};
                        if(data.asesor5 == undefined){as5="";}else{ as5=data.asesor5;};
                        return as1 ;
                    }
                },
                {
                    data: function (data)
                    {
                        var status;
                        if(data.idStatusContratacion==15){status="Lote Contratado"}else{status="Status no definido [303]"}
                        return status;
                    }
                },
                {
                    data: function (data)
                    {
                        var details;
                        if(data.idStatusContratacion==15 && data.idMovimiento==45){details="15. Acuse entregado (Contraloría)"}
                        return details;
                    }
                },
                {data: 'comentario'},
                {data: 'fechaVenc'},
            ]
    } );
}