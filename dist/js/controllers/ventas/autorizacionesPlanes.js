let descuentosYCondiciones;
var primeraCarga = 1;
let titulos = [];

$(document).ready(function(){ /**FUNCIÓN PARA LLENAR EL SELECT DE LOS FILTROS DE ESTATUS */
    $.post('getCatalogo', {
        id_catalogo: 90
    }, function (data) {        
        var len = data.length;
        for (var i = 0; i < len; i++) {
            var id = data[i]['id_opcion'];
            var name = data[i]['nombre'];
            $("#estatusAut").append($('<option>').val(id).text(name));
                if(i == data.length -1) { 
                    //DESPUES DE LA ULTIMA OPCIÓN DE LOS ESTATUS, AGREGAR LA OPCIÓN "TODOS" PARA TRAER TODOS LOS ESTATUS
                    $("#estatusAut").append($('<option>').val(0).text('Todos'));
                }
        } 
        if (len <= 0) {
            $("#estatusAut").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }
        $("#estatusAut").selectpicker('refresh');
        $('#spiner-loader').addClass('hide');
    }, 'json'); 
});

$('#autorizacionesPVentas thead tr:eq(0) th').each( function (i) {
    var title = $(this).text();
    titulos.push(title);
    $(this).html('<input type="text"  class="textoshead" placeholder="' + title + '"/>');
    $('input', this).on('keyup change', function() {
        if (tablaAutorizacion.column(i).search() !== this.value) {
            tablaAutorizacion
                .column(i)
                .search(this.value)
                .draw();
            var index = tablaAutorizacion.rows({
                selected: true,
                search: 'applied'
            }).indexes();
        }
    });
});

ConsultarTabla();
function ConsultarTabla(opcion = 1,anio = '',estatus = ''){
    tablaAutorizacion = $("#autorizacionesPVentas").DataTable({
        dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width: '100%',
        scrollX: true,
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            className: 'btn buttons-excel',
            titleAttr: 'Descargar archivo de Excel',
            title: 'AUTORIZACIONES PLANES DE VENTAS',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                format: {
                    header:  function (d, columnIdx) {
                        return titulos[columnIdx];
                    }
                }
            },
        } ],
        pagingType: "full_numbers",
        fixedHeader: true,
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        destroy: true,
        ordering: false,
        columns: [{  
            "width": "2%",
            "data": function( d ){
                return '<p class="m-0">'+d.id_autorizacion+'</p>';
            }
        },
        {  
            "width": "10%",
            "data": function( d ){
                return `<p class="m-0">${d.sede}</p>`;
            }
        },
        {  
            "width": "10%",
            "data": function( d ){
                return `<p class="m-0">${d.idResidencial}</p>`;
            }
        },
        {  
            "width": "10%",
            "data": function( d ){
                let fecha_inicio = moment(d.fecha_inicio,'YYYY/MM/DD').format('DD/MM/YYYY');
                return `<p class="m-0"><b>${fecha_inicio}</b></p>`;
            }
        },
        {
            "width": "15%",
            "data": function( d ){
                let fecha_fin = moment(d.fecha_fin,'YYYY/MM/DD').format('DD/MM/YYYY');
                return `<p class="m-0"><b>${fecha_fin}</b></p>`;
            }
        },
        {
            "width": "10%",
            "data": function( d ){
                return `<p class="m-0">${d.tipoLote}</p>`;
            }
        },
        {  
            "width": "5%",
            "data": function( d ){
                    return `<p class="m-0">${d.tipoSuperficie}</p>`;  
            }
        },
        {
            "width": "5%",
            "data": function( d ){
                    return `<p class="m-0"><span class="label ${d.colorAutorizacion}">${d.estatusAutorizacion}</span></p>`;  
            }
        },
        {  
            "width": "5%",
            "data": function( d ){
                return `<p class="m-0"><span class="label ${d.colorEstatus}">${d.estatusA}</span></p>`;
        }
        },
        {
            "width": "5%",
            "data": function( d ){
               let fecha_creacion = moment(d.fecha_creacion.split('.')[0],'YYYY/MM/DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')
                return `<p class="m-0">${fecha_creacion}</p>`;
        }
        },
        {  
            "width": "5%",
            "data": function( d ){
                return `<p class="m-0">${d.creadoPor}</p>`;
             }
        },
        {  
            "width": "5%",
            "data": function( d ){
                $('[data-toggle="tooltip"]').tooltip();
                let botones = '';
                switch(id_rol_general){
                    case 5:
                        if(d.estatus == 1){
                            botones += botonesPermiso(1,1,1,0,d.id_autorizacion,d.estatus);
                        }
                        if(d.estatus == 3){
                            botones += botonesPermiso(1,0,0,0,d.id_autorizacion,d.estatus);
                        }
                        if(d.estatus == 4){
                            botones += botonesPermiso(1,1,1,0,d.id_autorizacion,d.estatus);
                        }
                    break;
                    case 17:
                        if(d.estatus == 2){
                            botones += botonesPermiso(1,0,1,1,d.id_autorizacion,d.estatus);
                        }
                        if(d.estatus == 3){
                            botones += botonesPermiso(1,0,0,0,d.id_autorizacion,d.estatus);
                        }
                        if(d.estatus == 4){
                            botones += botonesPermiso(1,0,0,0,d.id_autorizacion,d.estatus);
                        }
                    break;
                }
                botones += `<button data-idAutorizacion="${d.id_autorizacion}" id="btnHistorial" class="btn-data btn-gray" data-toggle="tooltip" data-placement="top" title="Historial"><i class="fas fa-info"></i></button>`; ;
                return '<div class="d-flex justify-center">' + botones + '<div>';
             }
        }],
        columnDefs: [{}],
        ajax: {
            "url": general_base_url + "PaquetesCorrida/getAutorizaciones",
            "type": "POST",
            cache: false,
            data: {
                "opcion": opcion,
                "anio": anio,
                "estatus":estatus
            }
        },
        order: [
            [1, 'asc']
        ]
    });
}

function botonesPermiso(permisoVista,permisoEditar,permisoAvanzar,permisoRechazar,idAutorizacion,estatus){
    let botones = '';
    /**Permisos
     * 1.- vista
     * 2.- Editar
     * 3.- Avanzar
     * 4.- Rechazar
     * */
        if(permisoVista == 1){ botones += `<button data-idAutorizacion="${idAutorizacion}" id="btnVer" class="btn-data btn-sky" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fas fa-eye"></i></button>`;   }
        if(permisoEditar == 1){ botones += `<button data-idAutorizacion="${idAutorizacion}" id="btnEditar" class="btn-data btn-yellow" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-edit"></i></button>`; }
        if(permisoAvanzar == 1){ botones += `<button data-idAutorizacion="${idAutorizacion}" data-tipo="1" data-estatus="${estatus}" id="btnAvanzar" class="btn-data btn-green" data-toggle="tooltip" data-placement="top" title="Avanzar"><i class="fas fa-thumbs-up"></i></button>`;  }
        if(permisoRechazar == 1){ botones += `<button data-idAutorizacion="${idAutorizacion}" data-tipo="2" data-estatus="${estatus}" id="btnAvanzar" class="btn-data btn-warning" data-toggle="tooltip" data-placement="top" title="Rechazar"><i class="fas fa-trash"></i></button>`;  }
    return  botones;
}

$(document).on('click', '#btnEditar', function (e) {
    var data = tablaAutorizacion.row($(this).parents('tr')).data();
    document.getElementById('fechainicio').value = data.fecha_inicio;
    document.getElementById('fechafin').value = data.fecha_fin;
    document.getElementById('accion').value = 2;
    document.getElementById('idSolicitudAut').value = data.id_autorizacion;
    document.getElementById('paquetes').value = data.paquetes;
    $('#li-plan').addClass('active');
    $('#li-aut').removeClass('active');
    $('#nuevas-2').addClass('active');
    $('#nuevas-1').removeClass('active');
    $("#sede").selectpicker();
    $('#sede').val(parseInt(data.id_sede)).trigger('change');
    $("#sede").selectpicker('refresh');
    let residencialesSelect = [];
    $("#residencial").selectpicker();
    let residenciales = data.idResidencial.split(',');
    for (let m = 0; m < residenciales.length; m++) {
        residencialesSelect.push(residenciales[m]);
    }
    setTimeout(() => {
        $(`#residencial`).val(residencialesSelect).trigger('change');
    }, 1000);

    $("#residencial").selectpicker('refresh');
    
    var radios = document.getElementsByName('tipoLote');
    for (var j = 0; j < radios.length; j++) {
        if (radios[j].value == data.tipo_lote) {
            radios[j].checked = true;
            break;
        }
    }
    validateAllInForm(data.tipo_lote,1);
    var radios = document.getElementsByName('superficie');
    for (var j = 0; j < radios.length; j++) {
        if (radios[j].value == data.superficie) {
            radios[j].checked = true;
            break;
        }
    }

    selectSuperficie(data.superficie);
    const scroll = document.querySelector(".ps-scrollbar-y-rail");
    scroll.scrollTop = 0;
    $('#btn_consultar').prop('disabled', true);
    setTimeout(() => {
        ConsultarPlanes();
    }, 1000);
    $('#spiner-loader').addClass('hide');
});

//Fn para visualizar de manera rápida los planes de venta
$(document).on('click', '#btnVer', function (e) {
    let residencialesSelect = [];
    let data = tablaAutorizacion.row($(this).parents('tr')).data();
    let residenciales = data.idResidencial.split(',');
    for (let m = 0; m < residenciales.length; m++) {
        residencialesSelect.push(residenciales[m]);
    };
    
    let params = {
        'sede': data.id_sede,
        'residencial': residencialesSelect,
        'superficie': data.superficie,
        'fin':$('#fin').val(),
        'tipolote': data.tipo_lote,
        'fechaInicio': data.fecha_inicio,
        'fechaFin': data.fecha_fin,
        'paquetes': data.paquetes,
        'accion': 2
    };
    
    consultarPlanesPreview(params);
    
});

async function consultarPlanesPreview(paramsPlan){
    if(primeraCarga == 1){
        descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
        descuentosYCondiciones = JSON.parse(descuentosYCondiciones);
        primeraCarga = 0;
    }

    $.post('getPaquetes', paramsPlan, function(data) {
        if( data.length >= 1){
            let dataPaquetes = data[0].paquetes;
            let dataDescuentosByPlan = data[0].descuentos;            
            
            dataPaquetes.forEach(function (element, indexPaquetes) {
                console.log(element);
                let idPaquete = element.id_paquete;
                var indexActual = document.getElementById('index');
                var indexNext = (document.getElementById('index').value - 1) + 2;
                indexActual.value = indexNext;

                previewPlan(indexPaquetes, element);
                $("#viewPlansModal").modal();
                let lenDesCon = descuentosYCondiciones.length;

                descuentosYCondiciones.forEach(function (subelement, indexCondicion) {                        
                //     let idCondicion = subelement['condicion']['id_condicion'];
                //     let nombreCondicion = subelement['condicion']['descripcion'];
                    
                previewDetallePlan(indexNext, indexCondicion, idCondicion, nombreCondicion);
                //     let existe = dataDescuentosByPlan.find(elementD => elementD.id_paquete == idPaquete &&  elementD.id_condicion == idCondicion);

                //     let descuentosByPlan = dataDescuentosByPlan.filter(desc => desc.id_paquete == idPaquete);
                //     if(existe != undefined){
                //         const check =  document.getElementById(`inlineCheckbox1_${indexNext}_${indexCondicion}`);
                //         check.checked = true; 
                //         PrintSelectDesc(check, nombreCondicion, idCondicion, indexCondicion, indexNext, descuentosByPlan, lenDesCon, indexPaquetes);
                //     }                
                });
            
                if( lenDesCon <= 0 ){
                    $("#tipo_descuento_"+indexNext).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }

                $("#tipo_descuento_"+indexNext).selectpicker('refresh');    
                validateNonePlans();
            });
        }
        else{
            alerts.showNotification("top", "right", "No se encontraron planes con los datos proporcionados", "warning");
        }
    }, 'json');
};

$(document).on('click', '#btnAvanzar', function () {
    let idAutorizacion = $(this).attr('data-idAutorizacion');
    let estatus = $(this).attr('data-estatus');
    let tipo = $(this).attr('data-tipo');
    tipo == 1  ? $('#modalAutorizacion').addClass("modal-sm") : $('#modalAutorizacion').addClass("modal-md") ;
    document.getElementById('titleAvance').innerHTML = tipo == 1 ? 'Avanzar autorización' : 'Rechazar autorización';
    $('#id_autorizacion').val(idAutorizacion);
    $('#estatus').val(estatus);
    $('#tipo').val(tipo);
    document.getElementById('modal-body').innerHTML = tipo == 2 ? `<textarea class="text-modal scroll-styles" max="255" type="text" name="comentario" id="comentario" autofocus="true" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Escriba aquí su comentario"></textarea>
    <b id="text-observations" class="text-danger"></b>` : ''; 
    $("#avanzarAut").modal();
});
    
$(document).on('submit', '#avanceAutorizacion', function (e) {
    e.preventDefault();
    let tipo = $('#tipo').val();
    let data = new FormData($(this)[0]);
    $('#spiner-loader').removeClass('hide');
    $.ajax({
        url: "avanceAutorizacion",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function (response) {
            if (response == 1) {
                $("#avanzarAut").modal("hide");
                tipo == 1  ? $('#modalAutorizacion').removeClass("modal-sm") : $('#modalAutorizacion').removeClass("modal-md") ;
                $('#spiner-loader').addClass('hide');
                alerts.showNotification("top", "right", "Estatus actualizado", "success");
                tablaAutorizacion.ajax.reload(null,false);    
            }
        }, error: function () {
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
            $('#spiner-loader').addClass('hide');
        }
    });
});

$(document).on('click', '#searchByEstatus', function () { 
    if($('#estatusAut').val() == '' || $('#anio').val() == ''){
        alerts.showNotification("top", "right", "Debe seleccionar ambas opciones.", "warning");
    }else{
        let estatus = $('#estatusAut').val();
        let anio = $('#anio').val();
        ConsultarTabla(2,anio,estatus);
    }
});

$(document).on('click', '#btnHistorial', function () {
    let idAutorizacion = $(this).attr('data-idAutorizacion');
    document.getElementById('historialAut').innerHTML = '';
        $.post('getHistorialAutorizacion', {
            id_autorizacion: idAutorizacion
        }, function (data) {      
            var len = data.length;
            for (var i = 0; i < len; i++) {
                let estatus=data[i]['estatus'];
                let comentario = data[i]['comentario'];
                    $('#historialAut').append(`
                    <div class="d-flex mb-2">
                        <div class="w-10 d-flex justify-center align-center">
                            <span style="width:40px; height:40px; display:flex; justify-content:center; align-items:center; border-radius:27px; background-color: ${estatus == 1 ? '#28B46318' : '#c0131318' }">
                                <i class="fas ${estatus == 1 ? 'fa-check' : 'fa-close' } fs-2" style="color: ${estatus == 1 ? '#28B463' : '#c01313'} "></i>
                            </span>
                        </div>
                        <div class="w-90">
                            <b>${data[i]['creadoPor']}</b>
                            <p class="m-0" style="font-size:12px">${comentario}</p> 
                            <p class="m-0" style="font-size:10px; line-height:12px; color:#999">${moment(data[i]['fecha_movimiento'].split('.')[0],'YYYY/MM/DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')}</p>
                        </div>
                    </div>`)
            }
        }, 'json');
    $("#modalHistorial").modal();
});


/* - SCRIPTS TAB CARGAR PLAN- */
$(document).ready(function() {
    $.post(general_base_url+"PaquetesCorrida/lista_sedes", function (data) {
        $('[data-toggle="tooltip"]').tooltip()
        var len = data.length;
        for (var i = 0; i < len; i++) {
            var id = data[i]['id_sede'];
            var name = data[i]['nombre'];
            $("#sede").append($('<option>').val(id).text(name.toUpperCase()));
        }
        $("#sede").selectpicker('refresh');
    }, 'json');

    //Función para mandar parametros por defecto en filtros de fecha
    setIniDatesXMonth("#fechainicio", "#fechafin");
    //Función para mandar estatus vacio por defecto 
    sinPlanesDiv();
});

$("#sede").change(function() {
    $('#spiner-loader').removeClass('hide');
    $('#residencial option').remove();
    var	id_sede = $(this).val();

    $.post('getResidencialesList/'+id_sede,{async: true}, function(data) {
        $('#spiner-loader').addClass('hide');
        $("#residencial").append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
        var len = data.length;
        for( var i = 0; i<len; i++){
            var name = data[i]['nombreResidencial']+' '+data[i]['descripcion'];
            var id = data[i]['idResidencial'];
            $("#residencial").append(`<option value='${id}'>${name}</option>`);
        }   
        if(len<=0){
            $("#residencial").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }
        $("#residencial").selectpicker('refresh');
    }, 'json'); 
});

$("#residencial").select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown"});

function addDescuento(id_condicion, descripcion){
    $('#descuento').val('');
    $('#label_descuento').html();
    $('#id_condicion').val(id_condicion);
    $('#nombreCondicion').val(descripcion);
    $('#label_descuento').html('Agregar descuento a "' + descripcion +'"');
    $('#ModalFormAddDescuentos').modal();
};

$("input[data-type='currency']").on({
    keyup: function() {
        let id_condicion = $('#id_condicion').val();
            if(id_condicion == 12 || id_condicion == 4){
                formatCurrency($(this));
            }
    },
    blur: function() { 
        let id_condicion = $('#id_condicion').val();
        if(id_condicion == 12 || id_condicion == 4){
            formatCurrency($(this), "blur");
        }
    }
});

//Agregar formato tipo moneda y decimales para ciertas condiciones 
function formatCurrency(input, blur) {
    var input_val = input.val();
    if (input_val === "") { return; }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionStart");
    if (input_val.indexOf(".") >= 0) {
        var decimal_pos = input_val.indexOf(".");
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);
        left_side = formatNumber(left_side);
        right_side = formatNumber(right_side);
        if (blur === "blur") {
            right_side += "00";
        }
        right_side = right_side.substring(0, 2);
        input_val = "$" + left_side + "." + right_side;
    } else {
        input_val = formatNumber(input_val);
        input_val = "$" + input_val;
        if (blur === "blur") {
            input_val += ".00";
        }
    }

    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

//Fn para obtener las condiciones y descuentos que pertenecen a ellas (tablas condiciones en BD)
function getDescuentosYCondiciones(primeraCarga, tipoCondicion){
    $('#spiner-loader').removeClass('hide');

    return new Promise ((resolve, reject) => {   
        $.ajax({
            type: "POST",
            url: `getDescuentosYCondiciones`,
            data: { "primeraCarga": primeraCarga, "tipoCondicion": tipoCondicion },
            cache: false,
            success: function(data){
                primeraCarga = 0;
                resolve(data);
                $('#spiner-loader').addClass('hide');
            },
            error: function() {
                $('#spiner-loader').addClass('hide');
                alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
            }
        });
    });
}

//Fn para construir las tablas según el número de condiciones existente, esto en la modal para ver condiciones
async function construirTablas(){
    if(primeraCarga == 1){
        descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
        descuentosYCondiciones = JSON.parse(descuentosYCondiciones);
        primeraCarga = 0;
    }
    
    descuentosYCondiciones.forEach(element => {
        let descripcion = element['condicion']['descripcion'];
        let id_condicion = element['condicion']['id_condicion'];
        let dataCondicion = element['data'];
        let title = (descripcion.replace(/ /g,'')).replace(/[^a-zA-Z ]/g, "");
        
        $('#table'+title+' thead tr:eq(0) th').each( function (i) {
            var subtitle = $(this).text();
            $(this).html('<input type="text" class="textoshead" placeholder="'+subtitle+'"/>' );
            $( 'input', this ).on('keyup change', function () {
                if ($('#table' + title).column(i).search() !== this.value ) {
                    $('#table' + title).column(i).search(this.value).draw();
                }
            });
        });
        
        $("#table"+title).DataTable({
            dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
            width: "auto",
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Descargar archivo de Excel',
                title: 'DESCUENTOS AL '+ descripcion.toUpperCase()
            },
            {
                text: `<a href="#" onclick="addDescuento(${id_condicion}, '${descripcion}');">Agregar descuento</a>`,
                className: 'btn-azure',
            }],
            pagingType: "full_numbers",
            language: {
                url: general_base_url + "static/spanishLoader_v2.json",
                paginate: {
                    previous: "<i class='fa fa-angle-left'>",
                    next: "<i class='fa fa-angle-right'>"
                }
            },
            destroy: true,
            ordering: false,
            columns: [{
                data: 'id_descuento'
            },
            {
                data: function (d) {
                    return d.porcentaje + '%';
                }
            }
            ],
            data: dataCondicion,
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                searchable:false,
                className: 'dt-body-center'
            }],
            order: [
                [1, 'desc']
            ]
        });
    });

    $('[data-toggle="tooltip"]').tooltip();
}

//Fn para agregar nuevo descuento
$("#addNewDesc").on('submit', function(e){
    e.preventDefault();
    $('#spiner-loader').removeClass('hide');

    let formData = new FormData(document.getElementById("addNewDesc"));
    let nombreCondicion = (($("#nombreCondicion").val()).replace(/ /g,'')).replace(/[^a-zA-Z ]/g, "");
    $.ajax({
        url: 'SaveNewDescuento',
        data: formData,
        method: 'POST',
        contentType: false,
        cache: false,
        processData:false,
        success: function(data) {
            data =  JSON.parse(data);
            if ( data['status'] = 402 ){
                descuentosYCondiciones.forEach(element => {
                    if ( element['condicion']['id_condicion'] == data['detalle'][0]['condicion']['id_condicion'] ){
                        element['data'] = [];
                        element['data'] = data['detalle'][0]['data'];

                        $("#table"+nombreCondicion).DataTable().clear().rows.add(element['data']).draw();
                    }
                });
            }

            alerts.showNotification("top", "right", ""+data["mensaje"]+"", ""+data["color"]+"");

            //Se cierra el modal
            $('#ModalFormAddDescuentos').modal('toggle');
            document.getElementById('addNewDesc').reset();
            $('#spiner-loader').addClass('hide');
        },
        error: function(){
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
            $('#spiner-loader').addClass('hide');
        },
        async: false
    });
});


function previewPlan(index, objPlan){
    let value = objPlan['descripcion'];

    $(".previewBody").append(`
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="cardPlan dataTables_scrollBody">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <label>Descripción del plan (*)" `+value+`</label>
                    </div>
                    <div class="col-sm-12">
                        <div class="mt-1" id="checks_${index}">
                            <div class="loadCard w-100">
                                <img src= '`+general_base_url+`dist/img/loadingMini.gif' alt="Icono gráfica" class="w-30">
                            </div>
                        </div>	
                    </div>
                </div>
            </div>
        </div>
    </div>`);
}

function previewDetallePlan(){

}

//Plantilla para crear tarjeta de los planes de ventas (cascarón principal)
function templateCard(index, objPlan = ''){
    let value = `${objPlan != '' ? 'value="' + objPlan['descripcion'] + '"' : ''}`;
    $('#showPackage').append(`
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" id="card_${index}">
        <div class="cardPlan dataTables_scrollBody">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="title d-flex justify-center align-center">
                            <h3 class="card-title">Plan</h3>
                            <button type="button" class="btn-trash" data-toggle="tooltip" data-placement="left" title="Eliminar plan" id="btn_delete_${index}" onclick="removeElementCard('card_${index}')"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <input type="text" class="inputPlan" required name="descripcion_${index}" id="descripcion_${index}" placeholder="Descripción del plan (*)" `+value+`>
                        <div class="mt-1" id="checks_${index}">
                            <div class="loadCard w-100">
                                <img src= '`+general_base_url+`dist/img/loadingMini.gif' alt="Icono gráfica" class="w-30">
                            </div>
                        </div>						
                        <div class="form-group col-md-12" id="tipo_descuento_select_${index}" hidden>
                    </div>
                </div>
            </div>
        </div>
    </div>`);
    $('[data-toggle="tooltip"]').tooltip();
}

//Plantilla para crear selects según número de condiciones e insertar en la plantilla del plan
function templateSelectsByCard(indexNext, indexCondiciones, idCondicion, nombreCondicion){
    $("#tipo_descuento_"+indexNext).append(`<option value='${idCondicion}'>${nombreCondicion}</option>`);
    $("#checks_"+indexNext).append(`
    <div class="row boxAllDiscounts">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="check__item" for="inlineCheckbox1">
                <label>
                    <input type="checkbox" class="default__check d-none" id="inlineCheckbox1_${indexNext}_${indexCondiciones}" value="${idCondicion}" onclick="PrintSelectDesc(this, '${nombreCondicion}', ${idCondicion}, ${indexCondiciones}, ${indexNext})">
                    ${nombreCondicion}
                    <span class="custom__check"></span>
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="boxDetailDiscount hidden">
                <div class="w-100 mb-1" id="selectDescuentos_${indexNext}_${indexCondiciones}"></div>
                <div class="container-fluid rowDetailDiscount hidden">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0"><p class="m-0 txtMSI">msi</p></div>
                    </div>
                    <div class="container-flluid" id="listamsi_${indexNext}_${indexCondiciones}">
                    </div>
                </div>
            </div>
        </div>
    </div>`);
}

async function GenerarCard(){
    if($('#sede').val() != '' && $('#residencial').val() != '' && $('input[name="tipoLote"]').is(':checked') && $('#fechainicio').val() != '' && $('#fechafin').val() != '' && $('input[name="superficie"]').is(':checked') ){
        var indexActual = document.getElementById('index');
        var indexNext = (document.getElementById('index').value - 1) + 2;
        indexActual.value = indexNext;
        
        templateCard(indexNext);
        if(primeraCarga == 1){
            descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
            descuentosYCondiciones = JSON.parse(descuentosYCondiciones);
            primeraCarga = 0;
        }
        
        $("#checks_"+indexNext).html('');
        $("#tipo_descuento_"+indexNext).append($('<option>').val("default").text("SELECCIONA UNA OPCIÓN"));
        var len = descuentosYCondiciones.length;

        descuentosYCondiciones.forEach(function (element, indexCondiciones) {
            let idCondicion = element['condicion']['id_condicion'];
            let nombreCondicion = element['condicion']['descripcion'];
            templateSelectsByCard(indexNext, indexCondiciones, idCondicion, nombreCondicion);
        });

        if(len<=0){
            $("#tipo_descuento_"+indexNext).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }

        $("#tipo_descuento_"+indexNext).selectpicker('refresh');
        validateNonePlans();
    }
    else{
        alerts.showNotification("top", "right", "Debe llenar todos los campos requeridos.", "warning");
    }
}

//Guardar nuevo paquetes de planes de venta según attr seleccionados
function SavePaquete(){
    let formData = new FormData(document.getElementById("form-paquetes"));
    $.ajax({
        url: 'SavePaquete',
        data: formData,
        method: 'POST',
        contentType: false,
        cache: false,
        processData:false,
        beforeSend: function(){
            $('#ModalAlert .btnSave').attr("disabled","disabled");
            $('#ModalAlert .btnSave').css("opacity",".5");
        },
        success: function(data) {
            $('#ModalAlert .btnSave').prop('disabled', false);
            $('#ModalAlert .btnSave').css("opacity","1");
            if(data == 1){
                tablaAutorizacion.ajax.reload();
                ClearAll();
                alerts.showNotification("top", "right", "Planes almacenados correctamente.", "success");	
            }else{
                alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
            }
        
        },
        error: function(){
            $('#ModalAlert .btnSave').prop('disabled', false);
            $('#ModalAlert .btnSave').css("opacity","1");
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        },
        async: false
    });
}

//Fn para consultar los planes de ventas existente según parametros seleccionados
async function ConsultarPlanes(){
    $('#spiner-loader').removeClass('hide');
    if($('#sede').val() != '' && $('#residencial').val() != '' && $('input[name="tipoLote"]').is(':checked') && $('#fechainicio').val() != '' && $('#fechafin').val() != '' && $('input[name="superficie"]').is(':checked') ){
        let params = {
            'sede':$('#sede').val(),
            'residencial':$('#residencial').val(),
            'superficie':$('#super').val(),
            'tipolote':$('#tipo_l').val(),
            'fechaInicio':$('#fechainicio').val(),
            'fechaFin':$('#fechafin').val(),
            'paquetes':$('#paquetes').val(),
            'accion':$('#accion').val()};
        ClearAll2();

        if(primeraCarga == 1){
            descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
            descuentosYCondiciones = JSON.parse(descuentosYCondiciones);
            primeraCarga = 0;
        }

        $.post('getPaquetes',params, function(data) {
            if( data.length >= 1){
                let dataPaquetes = data[0].paquetes;
                let dataDescuentosByPlan = data[0].descuentos;            
                
                dataPaquetes.forEach(function (element, indexPaquetes) {
                    let idPaquete = element.id_paquete;
                    var indexActual = document.getElementById('index');
                    var indexNext = (document.getElementById('index').value - 1) + 2;
                    indexActual.value = indexNext;

                    templateCard(indexNext, element);

                    $("#checks_"+indexNext).html('');
                    $("#tipo_descuento_"+indexNext).append($('<option>').val("default").text("SELECCIONA UNA OPCIÓN"));
                    let lenDesCon = descuentosYCondiciones.length;

                    descuentosYCondiciones.forEach(function (subelement, indexCondicion) {                        
                        let idCondicion = subelement['condicion']['id_condicion'];
                        let nombreCondicion = subelement['condicion']['descripcion'];
                        
                        templateSelectsByCard(indexNext, indexCondicion, idCondicion, nombreCondicion);
                        let existe = dataDescuentosByPlan.find(elementD => elementD.id_paquete == idPaquete &&  elementD.id_condicion == idCondicion);

                        let descuentosByPlan = dataDescuentosByPlan.filter(desc => desc.id_paquete == idPaquete);
                        if(existe != undefined){
                            const check =  document.getElementById(`inlineCheckbox1_${indexNext}_${indexCondicion}`);
                            check.checked = true; 
                            PrintSelectDesc(check, nombreCondicion, idCondicion, indexCondicion, indexNext, descuentosByPlan, lenDesCon, indexPaquetes);
                        }                
                    });
                
                    if( lenDesCon <= 0 ){
                        $("#tipo_descuento_"+indexNext).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                    }

                    $("#tipo_descuento_"+indexNext).selectpicker('refresh');    
                    validateNonePlans();
                });
            }
            else{
                alerts.showNotification("top", "right", "No se encontraron planes con los datos proporcionados", "warning");
            }
        }, 'json');
    }
    else{
        alerts.showNotification("top", "right", "Debe llenar todos los campos requeridos.", "warning");
    }        
}

$("#form-paquetes").on('submit', function(e){ 
    e.preventDefault();
    $("#ModalAlert").modal();
});

function ClearAll2(){
    document.getElementById('showPackage').innerHTML = '';
    $('#index').val(0);	
    $(".leyendItems").addClass('d-none');
    $("#btn_save").addClass('d-none');
}

function ClearAll(){
    $("#ModalAlert").modal('toggle');
    document.getElementById('form-paquetes').reset();
    $("#sede").selectpicker("refresh");
    $('#residencial option').remove();
    document.getElementById('showPackage').innerHTML = '';
    $('#index').val(0);	
    setIniDatesXMonth("fechainicio", "fechafin");
    sinPlanesDiv();
    $(".leyendItems").addClass('d-none');
    $("#btn_save").addClass('d-none');
}

function ValidarOrden(indexN,i){
    let seleccionado = $(`#orden_${indexN}_${i}`).val();	
    for (let m = 0; m < 4; m++) {
        if(m != i){
            if( $(`#orden_${indexN}_${m}`).val() == seleccionado && seleccionado != ""){
                $(`#orden_${indexN}_${i}`).val("");
                alerts.showNotification("top", "right", "Este número ya se ha seleccionado.", "warning");
            }	
        }
    }
}

function llenar(e, indexGral, indexCondiciones, dataDescuentosByPlan, id_select, idCondicion, lenDesCon, indexPaquetes){
    var boxDetail = $(e).closest('.boxAllDiscounts' ).find('.boxDetailDiscount');
    boxDetail.removeClass('hidden');
    let rowDetail = boxDetail.find( '.rowDetailDiscount');
    let tipo = 0;
    if(idCondicion == 4 || idCondicion == 12){
        tipo = 1;
    }

    if(idCondicion != 13){
        rowDetail.removeClass('hidden');
    }
    let descuentosSelected = [];
    dataDescuentosByPlan = dataDescuentosByPlan.filter(desc => desc.id_condicion == idCondicion);
    dataDescuentosByPlan = dataDescuentosByPlan.sort();

    for (let m = 0; m < dataDescuentosByPlan.length; m++) {
        if(idCondicion != 13){
            crearBoxDetailDescuentos(indexGral, indexCondiciones, id_select, dataDescuentosByPlan[m].id_descuento, dataDescuentosByPlan[m].porcentaje, tipo);

            descuentosSelected.push(dataDescuentosByPlan[m].id_descuento);
                if(dataDescuentosByPlan[m].msi_descuento != 0){
                    var miCheckbox = document.getElementById(`${indexGral}_${dataDescuentosByPlan[m].id_descuento}_msiC`);
                    miCheckbox.checked = true;
                    document.getElementById(`${indexGral}_${dataDescuentosByPlan[m].id_descuento}_msi`).removeAttribute("readonly");
                    $(`#${indexGral}_${dataDescuentosByPlan[m].id_descuento}_msi`).val(dataDescuentosByPlan[m].msi_descuento);
                }
        }
        else{
            descuentosSelected.push(dataDescuentosByPlan[m].id_descuento+','+parseInt(dataDescuentosByPlan[m].porcentaje));
        }
    }
    
    $(`#${id_select}${indexGral}_${indexCondiciones}`).select2().val(descuentosSelected).trigger('change');

    if( indexPaquetes == lenDesCon -1 ){
        $('#spiner-loader').addClass('hide');
    }
}

//Se introducen todas las opcines para cada uno de los select que pertenecen a un plan
function PrintSelectDesc(e, nombreCondicion, idCondicion, indexCondiciones, indexGral, dataDescuentosByPlan=[], lenDesCon = 0, indexPaquetes = 0){
    nombreCondicion = (nombreCondicion.replace(/ /g,'')).replace(/[^a-zA-Z ]/g, "");
    var boxDetail = $(e).closest('.boxAllDiscounts' ).find('.boxDetailDiscount');
    boxDetail.removeClass('hidden');
    let rowDetail = boxDetail.find( '.rowDetailDiscount');
    let descuentosArray = descuentosYCondiciones[indexCondiciones]['data'];

    //Si la condición en el plan ES checkeada
    if($(`#inlineCheckbox1_${indexGral}_${indexCondiciones}`).is(':checked')){
        $(`#orden_${indexGral}_${indexCondiciones}`).prop( "disabled", false );
        
        $(`#selectDescuentos_${indexGral}_${indexCondiciones}`).append(`
        <div class="w-100 d-flex justify-center align-center">
            <select id="ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}" required name="${indexGral}_${indexCondiciones}_ListaDescuentos${nombreCondicion}_[]" multiple class="form-control" data-live-search="true">
        </div>`);

                //Propiedades que asignaremos a los select
                $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).select2({
                    allow_single_deselect: false,
                    containerCssClass: "select-gral",
                    dropdownCssClass: "custom-dropdown",
                    tags: false, 
                    tokenSeparators: [',', ' '], 
                    closeOnSelect : false,
                    placeholder : "SELECCIONA UNA OPCIÓN",
                    allowHtml: true, 
                    allowClear: true});
        
        descuentosArray.forEach(element => {
            let porcentaje = element['porcentaje'];
            let id_descuento = `${idCondicion == 13 ? element['id_descuento'] +','+ element['porcentaje'] : element['id_descuento'] }`;
            
            $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).append(`<option value='${id_descuento}' label="${porcentaje}">${idCondicion == 4 || idCondicion == 12 ? '$'+formatMoney(porcentaje) : (idCondicion == 13 ? porcentaje : porcentaje + '%'  ) }</option>`);
        });
        if( descuentosArray.length <= 0){
            $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }

        if( dataDescuentosByPlan.length > 0 ){
            llenar(e, indexGral, indexCondiciones, dataDescuentosByPlan, `ListaDescuentos${nombreCondicion}_`, idCondicion, lenDesCon, indexPaquetes);
        }

        //Propiedades que asignaremos a los select
        $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).select2({
            allow_single_deselect: false,
            containerCssClass: "select-gral",
            dropdownCssClass: "custom-dropdown",
            tags: false, 
            tokenSeparators: [',', ' '], 
            closeOnSelect : false,
            placeholder : "SELECCIONA UNA OPCIÓN",
            allowHtml: true, 
            allowClear: true});

        //Acciones que se ejecutaran cuando SE selecciona un descuento de una condición
        $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).on("select2:select", function (evt){            
            let element = evt.params.data.element;
            let $element = $(element);
            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
            if(idCondicion != 13){
                let lblListaDescuentos = 'ListaDescuentos' + nombreCondicion + '_';
                crearBoxDetailDescuentos(indexGral, indexCondiciones, `${lblListaDescuentos}`, $element[0].value, $element[0].label);
            } 
            rowDetail.removeClass('hidden');
        });
        $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).selectpicker('refresh');

        //Acciones que se ejecutaran cuando DESselecciona un descuento de una condición
        $(`#ListaDescuentos${nombreCondicion}_${indexGral}_${indexCondiciones}`).on("select2:unselecting", function (evt){
            let element = evt.params.args.data.element;
            let $element = $(element);
            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
            let classnameExists = !!document.getElementById(`${indexGral}_${$element[0].value}_msi`);
            if(classnameExists == true){
                document.getElementById(`${indexGral}_${$element[0].value}_msi`).outerHTML = "";
                document.getElementById(`${indexGral}_${$element[0].value}_span`).outerHTML = "";
            }
        });

    }
    else{
        boxDetail.addClass('hidden');
        rowDetail.addClass('hidden');

        $(`#orden_${indexGral}_${indexCondiciones}`).val("");
        $(`#orden_${indexGral}_${indexCondiciones}`).prop( "disabled", true );
        document.getElementById(`selectDescuentos_${indexGral}_${indexCondiciones}`).innerHTML = "";
        document.getElementById(`listamsi_${indexGral}_${indexCondiciones}`).innerHTML = "";
    }
    
}

function selectSuperficie(tipoSup){
    $('#super').val(tipoSup);
    document.getElementById("printSuperficie").innerHTML ='';
    validateAllInForm();
    $('[data-toggle="tooltip"]').tooltip();
}

function RemovePackage(){
    let divNum = $('#iddiv').val();
    $('#ModalRemove').modal('toggle');
    $("#" + divNum + "").remove();
    $('#iddiv').val(0);
    validateNonePlans();
    return false;
}

function removeElementCard(divNum) {
    $('#iddiv').val(divNum);
    $('#ModalRemove').modal('show');
}

function crearBoxDetailDescuentos(indexNext, indexCondiciones, select, id, text, pesos = 0){
    let texto = pesos == 2 ? text : (pesos == 1 ? '$'+ text : text + '%');

    $(`#listamsi_${indexNext}_${indexCondiciones}`).append(`
        <div class="row d-flex align-center mb-1" id="${indexNext}_${id}_span">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 d-flex align-center">
                <i class="fas fa-tag mr-1"></i><p class="m-0">${texto}</p>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0">
                <div class="boxOnOff">
                    <input type="checkbox" id="${indexNext}_${id}_msiC" class="switch-input d-none" onclick="turnOnOff(this)">
                    <label for="${indexNext}_${id}_msiC" class="switch-label"></label>
                    <input value="0" id="${indexNext}_${id}_msi" name="${indexNext}_${id}_msi" class="inputMSI" onkeyup="numberMask(this);" required readonly>
                </div>
            </div>
        </div>`);
}

function turnOnOff(e){
    let inputMSI = $(e).closest( '.boxOnOff' ).find( '.inputMSI');
    if (e.checked == true) {
        inputMSI.attr("readonly", false); 
        inputMSI.val('');
        inputMSI.focus();
    }
    else{
        inputMSI.attr("readonly", true); 
        inputMSI.val(0);
    }
}

function numberMask(e) {
    let arr = e.value.replace(/[^\dA-Z]/g, '').replace(/[\s-)(]/g, '').split('');
    e.value = arr.toString().replace(/[,]/g, '');
    if ( e.value > 12 ){
        e.value = '';
        alerts.showNotification("top", "right", "La cantidad ingresada es mayor.", "danger");
    }
}

function validateAllInForm( tipo_l = 0,origen = 0){
    if(origen == 1){
        $('#tipo_l').val(tipo_l);
    }
    var dinicio = $('#fechainicio').val();
    var dfin = $('#fechafin').val();
    var sede = $('#sede').val();
    var proyecto = $('#residencial').val();
    var containerTipoLote = document.querySelector('.boxTipoLote');
    var containerSup = document.querySelector('.boxSuperficie');
    var checkedTipoLote = containerTipoLote.querySelectorAll('input[type="radio"]:checked').length;
    var checkedSuper = containerSup.querySelectorAll('input[type="radio"]:checked').length;

    if(dinicio != '' && dfin != '' && sede != '' && proyecto != '' && checkedTipoLote != 0 && checkedSuper != 0){
        $("#btn_generate").removeClass('d-none');
        $("#btn_consultar").removeClass('d-none');
    }
    else{
        $("#btn_generate").addClass('d-none');
        $("#btn_consultar").addClass('d-none');
        $("#btn_save").addClass('d-none');
    }
}

function validateNonePlans(){
    var plans = document.getElementsByClassName("cardPlan");
    if (plans.length > 0 ){
        $("#btn_save").removeClass('d-none');
        $(".emptyCards").addClass('d-none');
        $(".emptyCards").removeClass('d-flex');
        $(".leyendItems").removeClass('d-none');
        $(".items").text(plans.length);
    }
    else{
        $("#btn_save").addClass('d-none');
        $(".emptyCards").removeClass('d-none');	
        $(".leyendItems").addClass('d-none');
    }
}

function sinPlanesDiv(){
    $('#showPackage').append(`
        <div class="emptyCards h-100 d-flex justify-center align-center pt-4">
            <div class="h-100 text-center pt-4">
                <img src= '`+general_base_url+`dist/img/emptyFile.png' alt="Icono gráfica" class="h-50 w-auto">
                <h3 class="titleEmpty">Aún no ha agregado ningún plan</h3>
                <div class="subtitleEmpty">Puede comenzar llenando el formulario de la izquierda <br>para después crear un nuevo plan</div>
            </div>
        </div>`);
}