// AA: Obtener fecha inicial y cuatro meses atrás para mini charts.
var endDate = moment().format("YYYY-MM-DD");
var beginDate = moment(endDate).subtract(4, 'months').format("YYYY-MM-DD");
var chart;
var initialOptions = {
    series: [],
    chart: {
        type: 'area',
        height: '100%',
        toolbar: { show: false },
        zoom: { enabled: false },
        sparkline: {
            enabled: true
        }
    },
    colors: [],
    grid: { show: false},
    dataLabels: { enabled: false },
    legend: { show: false },
    stroke: {
        curve: 'smooth',
        width: `1`,
    },
    xaxis: {
        categories: [],
        labels: {show: false},
        axisBorder: {show:false},
        axisTicks: {show:false},
    },
    yaxis: {
        labels: {
            show: false,
            formatter: function (value) {
                return value;
            }
        },
        axisBorder: {show:false},
        axisTicks: {show:false},
    },
    fill: {
        opacity: 1,
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: "vertical",
            shadeIntensity: 1,
            gradientToColors:  [],
            inverseColors: true,
            opacityFrom: 0.60,
            opacityTo: 0.2,
            stops: [0, 70, 100],
            colorStops: []
        }
    },
    tooltip: { enabled: true},
    markers: {
        size: `5`,
        colors: '#143860',
        strokeColors: [],
        strokeWidth: `3`,
        hover: {
            size: `3`
        }
    }
}

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

$(document).ready(function(){
    sp.initFormExtendedDatetimepickers();
    $('.datepicker').datetimepicker({locale: 'es'});
    init();
    setInitialValues();
    chart = new ApexCharts(document.querySelector("#boxModalChart"), initialOptions);
    chart.render();
});

$('[data-toggle="tooltip"]').tooltip();

async function init(){
    getLastSales(null, null);
    let rol = userType == 2 ? await getRolDR(idUser): userType;
    fillBoxAccordions(rol == '1' ? 'director_regional': rol == '2' ? 'gerente' : rol == '3' ? 'coordinador' : rol == '59' ? 'subdirector':'asesor', rol, idUser, 1, 1);
}

function createAccordions(option, render, rol){
    let tittle = getTitle(option);
    let html = '';
    html = `<div data-rol="${rol}" class="bk ${render == 1 ? 'parentTable': 'childTable'}">
                <div class="d-flex justify-between align-center">   
                    <div>
                        <i class="fas fa-angle-down"></i>
                    </div>
                    <div>
                        <h4 class="p-0 accordion-title js-accordion-title">`+tittle+`</h4>
                    </div>
                    <div>
                        ${render == 1 ? '': '<i class="fas fa-times deleteTable"></i>'}
                    </div>
                </div>
            <div class="accordion-content">
                <table class="table-striped table-hover" id="table`+option+`" name="table`+option+`">
                    <thead>
                        <tr>
                            <th class="detail">MÁS</th>
                            <th class="encabezado">`+option.toUpperCase()+`</th>
                            <th># LOTES APARTADOS</th>
                            <th>APARTADO</th>
                            <th>CANCELADOS</th>
                            <th>% CANCELADOS</th>
                            <th># LOTES CONTRATADOS</th>
                            <th>CONTRATADOS</th>
                            <th>CANCELADOS</th>
                            <th>% CANCELADOS</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>`;
    $(".boxAccordions").append(html);
}

function fillBoxAccordions(option, rol, id_usuario, render, transaction, dates=null){
    createAccordions(option, render, rol);
    $(".js-accordion-title").addClass('open');
    $(".accordion-content").css("display", "block");
    if(render == 1){
        $("#chartButton").data('option', option);
    }
    $('#table'+option+' thead tr:eq(0) th').each(function (i) {
        const title = $(this).text();
        $(this).html('<input type="text" center;" class="textoshead"  placeholder="' + title + '"/>');
        $('input', this).on('keyup change', function () {
            if ($("#table"+option+"").DataTable().column(i).search() !== this.value) {
                $("#table"+option+"").DataTable().column(i)
                    .search(this.value).draw();
            }
        });
    });

    generalDataTable = $("#table"+option).DataTable({
        dom: 'rt'+ "<'container-fluid pt-1 pb-1'<'row d-flex align-center'<'col-xs-12 col-sm-12 col-md-6 col-lg-6'i><'col-xs-12 col-sm-12 col-md-6 col-lg-6'p>>>",
        width: '100%',
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Todos"]
        ],
        destroy: true,
        ordering: false,
        scrollX: true,
        language: {
            url: "static/spanishLoader_v2.json",
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        columns: [
            {
                width: "2%",
                data: function(d){
                    return `<button type="btn" data-option="${option}" data-transaction="${transaction}" data-rol="${d.id_rol}" data-render="${render}" data-idUser="${d.userID}" id="details-${d.userID}" class="btnSub"><i class="fas fa-sitemap" data-toggle="tooltip" data-placement="bottom" title="Desglose a detalle"></i></button>`;
                }
            },
            {
                width: "26%",
                data: function (d) {
                    return d.nombreUsuario;
                }
            },
            // {
            //     width: "8%",
            //     data: function (d) {
            //         return "<b>" + d.sumaTotal+"</b>";
            //     }
            // },
            // {
            //     width: "8%",
            //     data: function (d) {
            //         return d.totalVentas;
            //     }
            // },
            {
                width: "8%",
                data: function (d) {
                    return d.totalAT; //# APARTADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return "<b>" + d.sumaAT+"</b>"; //SUMA APARTADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return d.totalCanA; //# CANCELADOS APARTADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return d.porcentajeTotalCanA + "%"; //PORCENTAJE CANCELADOS APARTADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return d.totalConT; //# CONTRATADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return "<b>" + d.sumaConT +"</b>"; //SUMA CONTRATADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return d.totalCanC; //# CANCELADOS CONTRATADOS
                }
            },
            {
                width: "8%",
                data: function (d) {
                    return d.porcentajeTotalCanC + "%"; //PORCENTAJE CANCELADOS CONTRATADOS
                }
            },
            // {
            //     width: "8%",
            //     data: function (d) {
            //         return "<b>" + d.sumaCT+"</b>";
            //     }
            // },
            // {
            //     width: "8%",
            //     data: function (d) {
            //         return d.totalCT;
            //     }
            // },
            {
                width: "8%",
                data: function (d) {
                    return  rol == 7 || (rol == 9 && render == 1) ? '':'<div class="d-flex justify-center"><button class="btn-data btn-blueMaderas update-dataTable" data-transaction="'+transaction+'" data-type="' + rol + '" data-render="' + render + '" value="' + d.userID + '"><i class="fas fa-sign-in-alt"></i></button></div>';
                }
            },
        ],
        columnDefs: [{
            visible: false,
            searchable: false
        }],
        ajax: {
            url: 'Reporte/getInformation',
            type: "POST",
            cache: false,
            data: {
                "typeTransaction": transaction,
                "beginDate": dates != null ? formatDate(dates.begin): '',
                "endDate":  dates != null ? formatDate(dates.end): '',
                "where": '1',
                "type": rol,
                "id_usuario": id_usuario,
                "render": render
            }
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
}

$(document).on('click', '.update-dataTable', function () {
    const type = $(this).attr("data-type");
    const render = $(this).data("render");
    const transaction = $(this).data("transaction");3
    let closestChild = $(this).closest('.childTable');
    console.log( closestChild.nextAll());
    closestChild.nextAll().remove();

    let dates = transaction == 2 ?  {begin: $('#tableBegin').val(), end: $('#tableEnd').val()}:null;


    // const beginDate = $("#beginDate").val();
    // const endDate = $("#endDate").val();

    // const saleType = $("#saleType").val();
    // const where = $(this).val();
    // let typeTransaction = 0;

    // if (beginDate == '01/01/2022' && endDate == '01/01/2022' && saleType == null) // APLICA FILTRO AÑO ACTUAL
    //     typeTransaction = 1;
    // else if (beginDate == '01/01/2022' && endDate == '01/01/2022' && saleType != null) // APLICA FILTRO AÑO ACTUAL Y TIPO DE VENTA
    //     typeTransaction = 2;
    // else if ((beginDate != '01/01/2022' || endDate != '01/01/2022') && saleType == null) // APLICA FILTRO POR FECHA
    //     typeTransaction = 3;
    // else if ((beginDate != '01/01/2022' || endDate != '01/01/2022') && saleType != null) // APLICA FILTRO POR FECHA Y TIPO DE VENTA
    //     typeTransaction = 4;

    if (type == 2) { // MJ: #sub->ger->coord
        if(render == 1){
            const table = "coordinador";
            fillBoxAccordions(table, 9, $(this).val(), 2, transaction, dates);
        }else{
            const table = "gerente";
            fillBoxAccordions(table, 3, $(this).val(), 2, transaction, dates);
        }
    } else if (type == 3) { // MJ: #gerente->coord->asesor
        if(render == 1){
            const table = "asesor";
            fillBoxAccordions(table, 7, $(this).val(), 2, transaction, dates);
        }else{
            const table = "coordinador";
            fillBoxAccordions(table, 9, $(this).val(), 2, transaction, dates);
        }
    } else if (type == 9) { // MJ: #coordinatorTable -> asesor
        if(render == 1){
        }else{
            const table = "asesor";
            fillBoxAccordions(table, 7, $(this).val(), 2, transaction, dates);
        }
    } else if (type == 59) { // MJ: #DirRegional->subdir->ger
        const table = "gerente";
        fillBoxAccordions(table, 3, $(this).val(), 2, transaction, dates);
    }
});

function setOptionsChart(series, categories, miniChart, type= null){
    (series.length > 1) ? colors = ["#2C93E7", "#d9c07b"] : colors = ["#2C93E7"];
    var optionsMiniChart = {
        series: series,
        chart: {
            type: 'area',
            height: '100%',
            toolbar: { show: false },
            zoom: { enabled: false },
            sparkline: {
                enabled: true
            }
        },
        colors: colors,
        grid: { show: false},
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: {
            curve: 'smooth',
            width: `${ ( miniChart == 0 ) ? 3 : 2 }`,
        },
        xaxis: {
            categories: categories,
            labels: {show: false},
            axisBorder: {show:false},
            axisTicks: {show:false},
        },
        yaxis: {
            labels: {
                show: false,
                formatter: function (value) {
                    let format = type != null ? value: "$" + formatMoney(value);
                    return format;
                }
            },
            axisBorder: {show:false},
            axisTicks: {show:false},
        },
        fill: {
            opacity: 1,
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 1,
                gradientToColors:  colors,
                inverseColors: true,
                opacityFrom: 0.60,
                opacityTo: 0.2,
                stops: [0, 70, 100],
                colorStops: []
            }
        },
        tooltip: { enabled: true},
        markers: {
            size: `${ ( miniChart == 0 ) ? 5 : 0 }`,
            colors: '#143860',
            strokeColors: colors,
            strokeWidth: `${ ( miniChart == 0 ) ? 3 : 0 }`,
            hover: {
                size: `${ ( miniChart == 0 ) ? 8 : 3 }`
            }
        }
    }
    return optionsMiniChart;
}
// $(document, '.js-accordion-title').unbind();
$(document).off('click', '.js-accordion-title').on('click', '.js-accordion-title', function () {
    $(this).parent().parent().next().slideToggle(200);
    $(this).toggleClass('open', 200);
});

$(document).on('click', '.deleteTable', function () {
    accordionToRemove($(this).parent().parent().parent().data( "rol" ));
});

$(document).on('click', '.btnSub', function () {
    let data = {
        transaction: $(this).data("transaction"),
        render: $(this).data("render"),
        user: $(this).data("iduser"),
        rol: $(this).data("rol"),
        table: $(this).closest('table'),
        thisVar: $(this),
        option: $(this).data("option"),
        begin: formatDate($('#tableBegin').val()), 
        end: formatDate($('#tableEnd').val())
    }

    initDetailRow(data);
});

$(document).on('click', '#searchByDateRangeTable', async function () {
    $(".boxAccordions").html('');
    let dates = {begin: $('#tableBegin').val(), end: $('#tableEnd').val()};
    let rol = userType == 2 ? await getRolDR(idUser): userType;

    fillBoxAccordions(rol == '1' ? 'director_regional': rol == '2' ? 'gerente' : rol == '3' ? 'coordinador' : rol == '59' ? 'subdirector':'asesor', rol, idUser, 1, 2, dates);

});

$(document).on('click', '.chartButton', function () {
    $(".datesModal").hide();
    $("#modalChart .boxModalTitle .title").html('');
    $("#modalChart .boxModalTitle .total").html('');
    $("#modalChart .boxModalTitle .title").append('Grafica general');
    $('#modalChart').modal();
    // $("#boxModalChart").html('');
    let option = $('#chartButton').data('option');
    let table = $(`#table${option}`);
    var tableData = table.DataTable().rows().data().toArray();
    generalChart(tableData);
});


function chartDetail(e, tipoChart){
    // $("#boxModalChart").html('');
    $(".datesModal").show();
    $("#modalChart").modal();
    $("#modalChart .boxModalTitle .title").html('');
    $("#modalChart .boxModalTitle .total").html('');
    $("#modalChart #type").val('');

    var nameChart = (titleCase($(e).data("name").replace(/_/g, " "))).split(" ");
    $(".boxModalTitle .title").append('<p class="mb-1">' + nameChart[0] + '<span class="enfatize"> '+ nameChart[1] +'</span></p>');

    $("#modalChart #beginDate").val(moment(beginDate).format('DD/MM/YYYY'));
    $("#modalChart #endDate").val(moment(endDate).format('DD/MM/YYYY'));
    $("#modalChart #type").val(tipoChart);
    getSpecificChart(tipoChart, beginDate, endDate);
}

function getSpecificChart(type, beginDate, endDate){
    $.ajax({
        type: "POST",
        url: "Reporte/getDataChart",
        data: {general: 0, tipoChart: type, beginDate: beginDate, endDate: endDate},
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            $('#spiner-loader').removeClass('hide');
        },
        success: function(data){
            var miniChart = 0;
            $('#spiner-loader').addClass('hide');
            var orderedArray = orderedDataChart(data);
            let { categories, series } = orderedArray[0];
            console.log('series',series);
            let total = 0;
            series.forEach(element => {
                total = total + element.data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
            });
            console.log(total);
            $("#modalChart .boxModalTitle .total").html('');
            $("#modalChart .boxModalTitle .total").append('<p>$'+formatMoney(total)+'</p>');
            
            if ( total != 0 ){
                $("#boxModalChart").removeClass('d-flex justify-center');
                // var miniChart = new ApexCharts(document.querySelector("#boxModalChart"), setOptionsChart(series, categories, miniChart));
                chart.updateOptions(setOptionsChart(series, categories, miniChart));
            }
            else{
                $("#boxModalChart").addClass('d-flex justify-center');
                $("#boxModalChart").append('<img src="./dist/img/emptyChart.png" alt="Icono gráfica" class="h-70 w-auto">');
                chart.updateOptions(setOptionsChart([], [], miniChart));
            }
            // chart.render();

        },
        error: function() {
            $('#spiner-loader').addClass('hide');
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        }
    });
}

function getLastSales(beginDate, endDate){
    $.ajax({
        type: "POST",
        url: "Reporte/getDataChart",
        data: {general: 1, tipoChart:'na', beginDate: beginDate, endDate: endDate},
        dataType: 'json',
        cache: false,
        beforeSend: function() {
          $('#spiner-loader').removeClass('hide');
        },
        success: function(data){
            let miniChart = 1, total = 0;
            $('#spiner-loader').addClass('hide');
            let orderedArray = orderedDataChart(data);
            for ( i=0; i<orderedArray.length; i++ ){
                let { chart, categories, series } = orderedArray[i];
                total = 0;
                for ( j=0; j < series.length; j++ ){
                    total += series[j].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                }

                $("#tot"+chart).text("$"+formatMoney(total));
                if ( total != 0 ){
                    $("#"+chart+"").html('');
                    $("#"+chart+"").removeClass('d-flex justify-center');
                    var miniChartApex = new ApexCharts(document.querySelector("#"+chart+""), setOptionsChart(series, categories, miniChart));
                    // chart.updateOptions(setOptionsChart(series, categories, miniChart));

                    miniChartApex.render();
                }
                else $("#"+chart+"").addClass('d-flex justify-center');
            }
        },
        error: function() {
          $('#spiner-loader').addClass('hide');
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        }
    });
}

$(document).on("click", "#searchByDateRange", function () {
    var beginDate = $("#modalChart #beginDate").val();
    var endDate = $("#modalChart #endDate").val();
    var type = $("#modalChart #type").val();
    $("#modalChart .boxModalTitle .total").html('');
    console.log('trigger');
    getSpecificChart(type, formatDate(beginDate), formatDate(endDate));
});

function orderedDataChart(data){
    let allData = [], totalMes = [], meses = [], series = [];
    for( i=0; i<data.length; i++){
        let { tipo, rol, total, mes, año } = data[i];

        nameTypeChart = `${ (tipo == 'vc') ? 'ventasContratadas' : (tipo == 'va') ? 'ventasApartadas' : (tipo == 'cc') ? 'canceladasContratadas' : 'canceladasApartadas' }`;

        nameSerie = `${ (rol == '9') ? 'Coordinador' : (rol == '7') ? 'Asesor' : (tipo == 'vc') ? 'ventasContratadas' : (tipo == 'va') ? 'ventasApartadas' : (tipo == 'cc') ? 'canceladasContratadas' : 'canceladasApartadas' }`;
        
        totalMes.push( (total != null) ? parseFloat(total.replace(/[^0-9.-]+/g,"")) : 0 );
        if( (i+1) < data.length ){
            if(tipo == data[i + 1].tipo){
                if(rol != data[i + 1].rol){
                    buildSeries(series, nameSerie, totalMes);
                    totalMes = [];
                    meses = [];
                }
                else{
                    meses.push(monthName(mes) + ' ' + año);
                }             
            }
            else{
                meses.push(monthName(mes) + ' ' + año);
                buildSeries(series, nameSerie, totalMes);
                buildAllDataChart(allData, nameTypeChart, series, meses);
                series = [];
                totalMes = [];
                meses = [];
            }
        }
        else{
            meses.push(monthName(mes) + ' ' + año);
            buildSeries(series, nameSerie, totalMes);
            buildAllDataChart(allData, nameTypeChart, series, meses)
            series = [];
            totalMes = [];
        }
    }
    return allData;
}

function buildSeries(series, nameSerie, totalMes){
    nameSerie = titleCase(nameSerie.split(/(?=[A-Z])/).join(" "));
    series.push({
        name: nameSerie,
        data: totalMes
    });
}

function buildAllDataChart(allData, nameTypeChart, series, meses){
    allData.push({
        chart : nameTypeChart,
        series : series,
        categories : meses
    });
}

function formatMoney(n) {
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function monthName(mon){
    var monthName = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'][mon - 1];
    return monthName;
}

function getRolDR(idUser){
    return new Promise(resolve => {      
        $.ajax({
            type: "POST",
            url: "Reporte/getRolDR",
            data: {idUser: idUser},
            dataType: 'json',
            cache: false,
            beforeSend: function() {
            $('#spiner-loader').removeClass('hide');
            },
            success: function(data){
                $('#spiner-loader').addClass('hide');
                resolve (data.length > 0 ? 59:2);
            },
            error: function() {
            $('#spiner-loader').addClass('hide');
                alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
            }
        });
    });
}

function formatDate(date) {
    var dateParts = date.split("/");
    var d = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function getTitle(option){
    var title;
    switch (option) {
        case 'director_regional':
          title = 'Reporte de ventas por dirección regional';
          break;
        case 'gerente':
            title = 'Reporte de ventas por gerencia';
            break;
        case 'coordinador':
            title = 'Reporte de ventas por coordinación';
            break;
        case 'subdirector':
            title = 'Reporte de ventas por subdirección';
            break;
        case 'asesor':
            title = 'Reporte de ventas por asesor';
            break;
        default:
            title = 'N/A';
        }
    return title;
};

function accordionToRemove(rol){
    $(".boxAccordions").find(`[data-rol='${rol}']`).remove();
    switch (rol) {
        case 7://asesor
            //solo se borra asesor
            break;
        case 9://coordinador
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        case 3://gerente
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        case 6://asistente gerente
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        case 2://subdir
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        case 5://asistente subdir
            $(".boxAccordions").find(`[data-rol='${2}']`).remove();
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break; 
        case 1://dir
            $(".boxAccordions").find(`[data-rol='${59}']`).remove();
            $(".boxAccordions").find(`[data-rol='${2}']`).remove();
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break; 
        case 4://asistente dir
            $(".boxAccordions").find(`[data-rol='${1}']`).remove();
            $(".boxAccordions").find(`[data-rol='${59}']`).remove();
            $(".boxAccordions").find(`[data-rol='${2}']`).remove();
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break; 
        case 59://dir regional
            $(".boxAccordions").find(`[data-rol='${2}']`).remove();
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        case 60://asistente dir regional
            $(".boxAccordions").find(`[data-rol='${59}']`).remove();
            $(".boxAccordions").find(`[data-rol='${2}']`).remove();
            $(".boxAccordions").find(`[data-rol='${3}']`).remove();
            $(".boxAccordions").find(`[data-rol='${9}']`).remove();
            $(".boxAccordions").find(`[data-rol='${7}']`).remove();
            break;
        default:
            break;
    }
}

function initDetailRow(dataObj){
    var detailRows = [];
    var tr = $(`#details-${dataObj.user}`).closest('tr');
    var table = $(`#details-${dataObj.user}`).closest('table');
    var row = $(`#table${dataObj.option}`).DataTable().row(tr);
    var idx = $.inArray(tr.attr('id'), detailRows);
    if (row.child.isShown()) {
        tr.removeClass('details');
        row.child.hide();

        // Remove from the 'open' array
        detailRows.splice(idx, 1);
    } else {
        $('#spiner-loader').removeClass('hide');
        tr.addClass('details');
        createDetailRow(row, tr, dataObj);
        // Add to the 'open' array
        if (idx === -1) {
            detailRows.push(tr.attr('id'));
        }
    }
}

function createDetailRow(row, tr, dataObj){
    $.post("Reporte/getDetails", {
        id_usuario: dataObj.user,
        rol: dataObj.rol,
        render:  dataObj.render,
        transaction: dataObj.transaction,
        beginDate: dataObj.begin,
        endDate: dataObj.end
    }).done(function (response) {
        row.data().sedesData = JSON.parse(response);
        
        $(`#table${dataObj.option}`).DataTable().row(tr).data(row.data());
        row = $(`#table${dataObj.option}`).DataTable().row(tr);
        row.child(buildTableDetail(row.data().sedesData)).show();
        tr.addClass('shown');
        dataObj.thisVar.parent().find('.animacion').removeClass("fa-caret-right").addClass("fa-caret-down");
        $('#spiner-loader').addClass('hide');
    }, 'json');
}

function buildTableDetail(data) {
    var sedes = '<table class="table subBoxDetail">';
    sedes += '<tr style="border-bottom: 1px solid #fff; color: #4b4b4b;">';
    sedes += '<td>' + '<b>' + '# ' + '</b></td>';
    sedes += '<td>' + '<b>' + 'SEDE ' + '</b></td>';
    sedes += '<td>' + '<b>' + '# DE LOTES APARTADOS ' + '</b></td>';
    sedes += '<td>' + '<b>' + 'APARTADO ' + '</b></td>';
    sedes += '<td>' + '<b>' + 'CANCELADO ' + '</b></td>';
    sedes += '<td>' + '<b>' + '% CANCELADOS ' + '</b></td>';
    sedes += '<td>' + '<b>' + '# DE LOTES CONTRATADOS ' + '</b></td>';
    sedes += '<td>' + '<b>' + 'CONTRATADOS ' + '</b></td>';
    sedes += '<td>' + '<b>' + 'CANCELADOS ' + '</b></td>';
    sedes += '<td>' + '<b>' + '% CANCELADOS ' + '</b></td>';
    sedes += '</tr>';
    $.each(data, function (i, v) {
        //i es el indice y v son los valores de cada fila
        sedes += '<tr>';
        sedes += '<td> ' + (i + 1) + ' </td>';
        sedes += '<td> ' + v.sede + ' </td>';
        sedes += '<td> ' + v.totalAT + ' </td>';
        sedes += '<td> ' + v.sumaAT + ' </td>';
        sedes += '<td> ' + v.totalCanA + ' </td>';
        sedes += '<td> ' + v.porcentajeTotalCanA + '% </td>';
        sedes += '<td> ' + v.totalConT + ' </td>';
        sedes += '<td> ' + v.sumaConT + ' </td>';
        sedes += '<td> ' + v.totalCanC + ' </td>';
        sedes += '<td> ' + v.porcentajeTotalCanC + ' </td>';
        sedes += '</tr>';
    });
    return sedes += '</table>';
}

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
    finalBeginDate2 = [('0' + beginDate.getDate()).slice(-2), ('0' + (beginDate.getMonth() + 1)).slice(-2), beginDate.getFullYear()].join('/');
    finalEndDate2 = [('0' + endDate.getDate()).slice(-2), ('0' + (endDate.getMonth() + 1)).slice(-2), endDate.getFullYear()].join('/');
    
    $('#tableBegin').val(finalBeginDate2);
    $('#tableEnd').val(finalEndDate2);
}
function titleCase(string){
    return string[0].toUpperCase() + string.slice(1).toLowerCase();
}

function generalChart(data){
    let x = [];
    let apartados = [];
    let apartadosC = [];
    let contratados = [];
    let contratadosC = [];

    data.forEach(element => {
        x.push(element.nombreUsuario);
        apartados.push(element.totalAT);
        apartadosC.push(element.totalCanA);
        contratados.push(element.totalConT);
        contratadosC.push(element.totalCanC);

    });
    let series = [
        {
            name: 'Apartados',
            data: apartados
        },
        {
            name: 'Cancelados apartados',
            data: apartadosC
        },
        {
            name: 'Contratados',
            data: contratados
        },
        {
            name: 'Cancelados contratados',
            data: contratadosC
        }
    ];
    chart.updateOptions(setOptionsChart(series, x, 0, 1));
    // chart.render();
}
