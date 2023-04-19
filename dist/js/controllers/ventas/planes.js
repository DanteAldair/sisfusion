let descuentosYCondiciones;
let allCondiciones = [];
var primeraCarga = 1;

const arr = [];

function formatNumber(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

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
        let tipo_d = $('#tipo_d').val();
            if(tipo_d == 12 || tipo_d == 4){
                formatCurrency($(this));
            }
    },
    blur: function() { 
        let tipo_d = $('#tipo_d').val();
        if(tipo_d == 12 || tipo_d == 4){
            formatCurrency($(this), "blur");
        }
    }
});


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

async function construirTablas(){
    if(primeraCarga == 1){
        descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
        descuentosYCondiciones = JSON.parse(descuentosYCondiciones);
        primeraCarga = 0;
    }
    
    descuentosYCondiciones.forEach(element => {
        //Llenamos array de SOLO condiciones
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

var tr;

$(document).ready(function() {
    $.post(general_base_url+"PaquetesCorrida/lista_sedes", function (data) {
        $('[data-toggle="tooltip"]').tooltip()
        var len = data.length;
        $("#sede").append($('<option>').val("").text("SELECCIONA UNA OPCIÓN"));
        for (var i = 0; i < len; i++) {
            var id = data[i]['id_sede']+','+data[i]['abreviacion'];
            var name = data[i]['nombre'];
            $("#sede").append($('<option>').val(id).text(name.toUpperCase()));
        }
        $("#sede").selectpicker('refresh');
    }, 'json');
    setIniDatesXMonth("#fechainicio", "#fechafin");
    sinPlanesDiv();
});

$("#sede").change(function() {
    $('#spiner-loader').removeClass('hide');
    $('#residencial option').remove();
    var parent = $(this).val();
    var	datos = parent.split(',')
    var	id_sede = datos[0];

    $.post('getResidencialesList/'+id_sede, function(data) {
        $('#spiner-loader').addClass('hide');
        $("#residencial").append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
        var len = data.length;
        for( var i = 0; i<len; i++){
            var name = data[i]['nombreResidencial']+' '+data[i]['descripcion'];
            var id = data[i]['idResidencial'];
            var descripcion = data[i]['descripcion'];
            $("#residencial").append(`<option value='${id}'>${name}</option>`);
        }   
        if(len<=0){
            $("#residencial").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }
        $("#residencial").selectpicker('refresh');
    }, 'json'); 
});

$("#residencial").select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown"});
var id_paquete=0;
var descripcion='';
var id_descuento=0;

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

/**--------------------------FN PARA MEJORA DE CARGA DE PLANES, COSULTAR PLANES---------------------- */
function ConsultarPlanes(){
$('#spiner-loader').removeClass('hide');

if($('#sede').val() != '' && $('#residencial').val() != '' && $('input[name="tipoLote"]').is(':checked') && $('#fechainicio').val() != '' && $('#fechafin').val() != '' && $('input[name="superficie"]').is(':checked') ){
    let params = {'sede':$('#sede').val(),'residencial':$('#residencial').val(),'superficie':$('#super').val(),'fin':$('#fin').val(),'tipolote':$('#tipo_l').val(),'fechaInicio':$('#fechainicio').val(),'fechaFin':$('#fechafin').val()};
    ClearAll2();
    $.post('getPaquetes',params, function(data) {
        let countPlanes = data.length;
        if(countPlanes >= 1){
            //MOSTRAR TODOS LOS PLANES EXISTENTES
            data[0].paquetes.unshift({});
            let dataPaquetes = data[0].paquetes;
            for (let index = 1; index < dataPaquetes.length; index++){
                var indexActual = document.getElementById('index');
                var indexNext = (document.getElementById('index').value - 1) + 2;
                indexActual.value = indexNext;
                $('#showPackage').append(`
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" id="card_${indexNext}">
                    <div class="cardPlan dataTables_scrollBody">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="title d-flex justify-center align-center">
                                        <h3 class="card-title">Plan</h3>
                                        <button type="button" class="btn-trash" data-toggle="tooltip" data-placement="left" title="Eliminar plan" id="btn_delete_${indexNext}" onclick="removeElementCard('card_${indexNext}')"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <input type="text" class="inputPlan" required name="descripcion_${indexNext}" id="descripcion_${indexNext}" value="${dataPaquetes[index].descripcion}">
                                    <div class="mt-1" id="checks_${indexNext}">
                                        <div class="loadCard w-100">
                                            <img src= '`+general_base_url+`dist/img/loadingMini.gif' alt="Icono gráfica" class="w-30">
                                        </div>
                                    </div>						
                                    <div class="form-group col-md-12" id="tipo_descuento_select_${indexNext}" hidden>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`);

                llenarDiv(indexNext,dataPaquetes[index].id_paquete,dataPaquetes.length,index)
                validateNonePlans();

                $('[data-toggle="tooltip"]').tooltip();
                $('.popover-dismiss').popover({
                    trigger: 'focus'
                });
        
            
            }
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


async function llenarDiv(indexNext,id_paquete,leng,ifor){
    $.post('getTipoDescuento', function(data2) {
        $("#checks_"+indexNext).html('');
        $("#tipo_descuento_"+indexNext).append($('<option>').val("default").text("SELECCIONA UNA OPCIÓN"));
        var len = data2.length;
        
        for( var i = 0; i<len; i++){
            var id = data2[i]['id_tcondicion'];
            var descripcion = data2[i]['descripcion'];
            $("#tipo_descuento_"+indexNext).append(`<option value='${id}'>${descripcion}</option>`);
            $("#checks_"+indexNext).append(`
            <div class="row boxAllDiscounts">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="check__item" for="inlineCheckbox1">
                        <label>
                            <input type="checkbox" class="default__check d-none" id="inlineCheckbox1_${indexNext}_${i}" value="${id}" onclick="PrintSelectDesc(this, ${id},${i},${indexNext})">
                            ${descripcion}
                            <span class="custom__check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="boxDetailDiscount hidden">
                        <div class="w-100 mb-1" id="selectDescuentos_${indexNext}_${i}"></div>
                        <div class="container-fluid rowDetailDiscount hidden">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0"><p class="m-0 txtMSI">msi</p></div>
                            </div>
                            <div class="container-flluid" id="listamsi_${indexNext}_${i}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>`);
            llenarSelects(indexNext,id_paquete,i,id,leng,ifor);
        }

        if(len<=0){
            $("#tipo_descuento_"+indexNext).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }
        $("#tipo_descuento_"+indexNext).selectpicker('refresh');
    }, 'json');               
}

async function llenarSelects(indexNext,id_paquete,i,id,len,ifor){
    let params = {'id_paquete':id_paquete,'id_tcondicion':id}
    $.ajax({
        async: true,
        url: 'getDescuentosByPlan',
        type: 'POST',
        data: params,
        success: function (data2) {
            //Las locas respuestas de las peticiones anidadas van aquí
            data2 = JSON.parse(data2);
            if(data2.length > 0){
                const check =  document.getElementById(`inlineCheckbox1_${indexNext}_${i}`);
                check.checked = true;
                const a = PrintSelectDesc(check, id,i,indexNext,1,data2,len,ifor);
            }
        },
    })	
}

// Función para construir plantilla del plan
function buildTemplateCard(index){
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
                        <input type="text" class="inputPlan" required name="descripcion_${index}" id="descripcion_${index}" placeholder="Descripción del plan (*)">
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



async function GenerarCard(){
    if($('#sede').val() != '' && $('#residencial').val() != '' && $('input[name="tipoLote"]').is(':checked') && $('#fechainicio').val() != '' && $('#fechafin').val() != '' && $('input[name="superficie"]').is(':checked') ){
        
        var indexActual = document.getElementById('index');
        var indexNext = (document.getElementById('index').value - 1) + 2;
        indexActual.value = indexNext;
        
        buildTemplateCard(indexNext);
        if(descuentosYCondiciones == ''){
            descuentosYCondiciones = await getDescuentosYCondiciones(primeraCarga, 0);
        }

        debugger;
        descuentosYCondiciones.forEach(element => {
            a = console.log(element);
            var id = data[i]['id_tcondicion'];
            var descripcion = data[i]['descripcion'];
            $("#tipo_descuento_"+indexNext).append(`<option value='${id}'>${descripcion}</option>`);
            $("#checks_"+indexNext).append(`
            <div class="row boxAllDiscounts">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="check__item" for="inlineCheckbox1">
                        <label>
                            <input type="checkbox" class="default__check d-none" id="inlineCheckbox1_${indexNext}_${i}" value="${id}" onclick="PrintSelectDesc(this, ${id},${i},${indexNext})">
                            ${descripcion}
                            <span class="custom__check"></span>
                        </label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="boxDetailDiscount hidden">
                        <div class="w-100 mb-1" id="selectDescuentos_${indexNext}_${i}"></div>
                        <div class="container-fluid rowDetailDiscount hidden">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
                                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0"><p class="m-0 txtMSI">msi</p></div>
                            </div>
                            <div class="container-flluid" id="listamsi_${indexNext}_${i}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>`);
        });
        
        /**-----------TIPO DESCUENTO------------------ */
        $.post('getTipoDescuento', function(data) {
            $("#checks_"+indexNext).html('');
            $("#tipo_descuento_"+indexNext).append($('<option>').val("default").text("SELECCIONA UNA OPCIÓN"));
            var len = data.length;

            
            for( var i = 0; i<len; i++){
                var id = data[i]['id_tcondicion'];
                var descripcion = data[i]['descripcion'];
                $("#tipo_descuento_"+indexNext).append(`<option value='${id}'>${descripcion}</option>`);
                $("#checks_"+indexNext).append(`
                <div class="row boxAllDiscounts">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="check__item" for="inlineCheckbox1">
                            <label>
                                <input type="checkbox" class="default__check d-none" id="inlineCheckbox1_${indexNext}_${i}" value="${id}" onclick="PrintSelectDesc(this, ${id},${i},${indexNext})">
                                ${descripcion}
                                <span class="custom__check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="boxDetailDiscount hidden">
                            <div class="w-100 mb-1" id="selectDescuentos_${indexNext}_${i}"></div>
                            <div class="container-fluid rowDetailDiscount hidden">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0"><p class="m-0 txtMSI">msi</p></div>
                                </div>
                                <div class="container-flluid" id="listamsi_${indexNext}_${i}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`);
            }

            if(len<=0){
                $("#tipo_descuento_"+indexNext).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
            }
            $("#tipo_descuento_"+indexNext).selectpicker('refresh');

        }, 'json');
        
        validateNonePlans();

        $('.popover-dismiss').popover({
            trigger: 'focus'
        });
        
    }
    else{
        alerts.showNotification("top", "right", "Debe llenar todos los campos requeridos.", "warning");
    }

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

function validarMsi(indexN,i){
    let valorIngresado = $(`#input_msi_${indexN}_${i}`).val();
    if(valorIngresado < 1){
        $(`#btn_save_${indexN}_${i}`).prop( "disabled", true );
    }
    else{
        $(`#btn_save_${indexN}_${i}`).prop( "disabled", false );
    }
}

function ModalMsi(indexN,i,select,id,text,pesos = 0){
    const Modalbody = $('#ModalMsi .modal-body');
    const Modalfooter = $('#ModalMsi .modal-footer');
    Modalbody.html('');
    Modalfooter.html('');
    Modalbody.append(`
    <h4>¿Este descuento tiene meses sin intereses?</h4>
    <div class="row text-center">
        <div class="col-md-12 text-center"></div>
        <div class="col-md-10 text-center">
            <div class="form-group text-center">
                <input type="number" placeholder="Cantidad" onkeyup="validarMsi(${indexN},${i})" class="input-descuento" id="input_msi_${indexN}_${i}">
            </div>
        </div>
    </div>`);

    Modalbody.append(`
    <div class="row text-center">
        <div class="col-md-6">
            <button class="btn btn-success btn-circle btn-lg" data-toggle="tooltip" data-placement="left" title="Agregar MSI"  disabled onclick="AddMsi(${indexN},${i},'${select}',${id},${text},${pesos});" name="disper_btn"  id="btn_save_${indexN}_${i}"><i class="fas fa-check"></i></button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-danger btn-circle btn-lg" data-toggle="tooltip" data-placement="right" title="No tiene MSI" data-dismiss="modal"><i class="fas fa-times"></i></button>
        </div>
    </div>`);

    $("#ModalMsi").modal();
    $('[data-toggle="tooltip"]').tooltip()
}

function llenar(e,indexGral,index,datos,id_select,id,leng,ifor){
    var boxDetail = $(e).closest('.boxAllDiscounts' ).find('.boxDetailDiscount');
    boxDetail.removeClass('hidden');
    let rowDetail = boxDetail.find( '.rowDetailDiscount');
    let tipo = 0;
    if(id == 4 || id == 12){
        tipo=1;
    }

    if(id != 13){
        rowDetail.removeClass('hidden');
    }
    let descuentosSelected = [];
    for (let m = 0; m < datos.length; m++) {
        
        if(id != 13){
            crearBoxDetailDescuentos(indexGral,index,id_select,datos[m].id_descuento,datos[m].porcentaje,tipo);
            descuentosSelected.push(datos[m].id_descuento);
                if(datos[m].msi_descuento != 0){
                    var miCheckbox = document.getElementById(`${indexGral}_${datos[m].id_descuento}_msiC`);
                    miCheckbox.checked = true;
                    document.getElementById(`${indexGral}_${datos[m].id_descuento}_msi`).removeAttribute("readonly");
                    $(`#${indexGral}_${datos[m].id_descuento}_msi`).val(datos[m].msi_descuento);
                }
            }else{
                descuentosSelected.push(datos[m].id_descuento+','+parseInt(datos[m].porcentaje));

            }
        }
    
    $(`#${id_select}${indexGral}_${index}`).select2().val(descuentosSelected).trigger('change');
                if(ifor == leng -1){
                    $('#spiner-loader').addClass('hide');
                }
}

async function PrintSelectDesc(e, id,index,indexGral, j = 0,datos = [],leng = 0,ifor = 0){
    let id_condicion=0;
    let id_con = id;
    var boxDetail = $(e).closest('.boxAllDiscounts' ).find('.boxDetailDiscount');
    boxDetail.removeClass('hidden');
    let rowDetail = boxDetail.find( '.rowDetailDiscount');
    if(id == 1){
        if($(`#inlineCheckbox1_${indexGral}_${index}`).is(':checked')){
            $(`#orden_${indexGral}_${index}`).prop( "disabled", false );
            
            id_condicion=1;
            	
            ///TOTAL DE LOTE
            $(`#selectDescuentos_${indexGral}_${index}`).append(`
            <div class="w-100 d-flex justify-center align-center">
                <div id="divmsi_${indexGral}_${index}"></div>
                <select id="ListaDescuentosTotal_${indexGral}_${index}" required name="${indexGral}_${index}_ListaDescuentosTotal_[]" multiple class="form-control" data-live-search="true">
            </div>`);

            $.post('getDescuentosPorTotal',{id_condicion: id_condicion}, function(data) {
                $(`#ListaDescuentosTotal_${indexGral}_${index}`).append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
                var len = data.length;
                for( var i = 0; i<len; i++){
                    var name = data[i]['porcentaje'];
                    var id = data[i]['id_descuento'];
                    $(`#ListaDescuentosTotal_${indexGral}_${index}`).append(`<option value='${id}' label="${name}">${name}%</option>`);
                }
                if(len<=0){
                    $(`#ListaDescuentosTotal_${indexGral}_${index}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }
                if(j == 1){
                    llenar(e,indexGral,index,datos,`ListaDescuentosTotal_`,id_con,leng,ifor);
                }
                
                $(`#ListaDescuentosTotal_${indexGral}_${index}`).selectpicker('refresh');
                
            }, 'json');

            $(`#ListaDescuentosTotal_${indexGral}_${index}`).select2({allow_single_deselect: false,containerCssClass: "select-gral", dropdownCssClass: "custom-dropdown", tags: false, tokenSeparators: [',', ' '], closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true});

            $(`#ListaDescuentosTotal_${indexGral}_${index}`).on("select2:select", function (evt){
                var element = evt.params.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                crearBoxDetailDescuentos(indexGral,index,'ListaDescuentosTotal_',$element[0].value,$element[0].label);
                
                rowDetail.removeClass('hidden');
            });
            $(`#ListaDescuentosTotal_${indexGral}_${index}`).on("select2:unselecting", function (evt){
                var element = evt.params.args.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                var classnameExists = !!document.getElementById(`${indexGral}_${$element[0].value}_msi`);
                if(classnameExists == true){
                    document.getElementById(`${indexGral}_${$element[0].value}_msi`).outerHTML = "";
                    document.getElementById(`${indexGral}_${$element[0].value}_span`).outerHTML = "";
                }
            });
        }
        else{
            boxDetail.addClass('hidden');
            rowDetail.addClass('hidden');

            $(`#orden_${indexGral}_${index}`).val("");
            $(`#orden_${indexGral}_${index}`).prop( "disabled", true );
            document.getElementById(`selectDescuentos_${indexGral}_${index}`).innerHTML = "";
            document.getElementById(`listamsi_${indexGral}_${index}`).innerHTML = "";
        }
    }
    else if(id == 2){
        if( $(`#inlineCheckbox1_${indexGral}_${index}`).is(':checked') ) {	
            $(`#orden_${indexGral}_${index}`).prop( "disabled", false );
            id_condicion=2;		
        
            ///TOTAL DE ENGANCHE
            $(`#selectDescuentos_${indexGral}_${index}`).append(`
            <div class="w-100 d-flex justify-center align-center">
                <select id="ListaDescuentosEnganche_${indexGral}_${index}" required name="${indexGral}_${index}_ListaDescuentosEnganche_[]" multiple="multiple" class="form-control" required data-live-search="true"></select>
            </div>`);

            $(`#ListaDescuentosEnganche_${indexGral}_${index}`).select2({containerCssClass: "select-gral", dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false});
            $.post('getDescuentosPorTotal',{ id_condicion: id_condicion }, function(data) {
                $(`#ListaDescuentosEnganche_${indexGral}_${index}`).append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
                var len = data.length;
                for( var i = 0; i<len; i++){
                    var name = data[i]['porcentaje'];
                    var id = data[i]['id_descuento'];
                    $(`#ListaDescuentosEnganche_${indexGral}_${index}`).append(`<option value='${id}' label="${name}">${name}%</option>`);
                }
                if(len<=0){
                    $(`#ListaDescuentosEnganche_${indexGral}_${index}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }
                if(j == 1){
                    llenar(e,indexGral,index,datos,`ListaDescuentosEnganche_`,id_con,leng,ifor);
                }
            
                $(`#ListaDescuentosEnganche_${indexGral}_${index}`).selectpicker('refresh');
            }, 'json');
        
            $(`#ListaDescuentosEnganche_${indexGral}_${index}`).select2({containerCssClass: "select-gral", dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false});
            $(`#ListaDescuentosEnganche_${indexGral}_${index}`).on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                crearBoxDetailDescuentos(indexGral,index,'ListaDescuentosEnganche_',$element[0].value,$element[0].label);
                rowDetail.removeClass('hidden');
            });

            $(`#ListaDescuentosEnganche_${indexGral}_${index}`).on("select2:unselecting", function (evt){
                var element = evt.params.args.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                var classnameExists = !!document.getElementById(`${indexGral}_${$element[0].value}_msi`);
                if(classnameExists == true){
                    document.getElementById(`${indexGral}_${$element[0].value}_msi`).outerHTML = "";
                    document.getElementById(`${indexGral}_${$element[0].value}_span`).outerHTML = "";
                }
            });

        
        }
        else{
            boxDetail.addClass('hidden');
            rowDetail.addClass('hidden');

            $(`#orden_${indexGral}_${index}`).val("");
            $(`#orden_${indexGral}_${index}`).prop( "disabled", true );
            document.getElementById(`selectDescuentos_${indexGral}_${index}`).innerHTML = "";
            document.getElementById(`listamsi_${indexGral}_${index}`).innerHTML = "";
        }
    }else if(id == 4){
        //Descuentos m2
        if( $(`#inlineCheckbox1_${indexGral}_${index}`).is(':checked') ) {
            $(`#orden_${indexGral}_${index}`).prop( "disabled", false );
            id_condicion=4;
        
            $(`#selectDescuentos_${indexGral}_${index}`).append(`
            <div class="w-100 d-flex justify-center align-center">
                <select id="ListaDescuentosM2_${indexGral}_${index}" required name="${indexGral}_${index}_ListaDescuentosM2_[]" multiple="multiple" class="form-control"  required data-live-search="true"></select>
            </div>`);

            $.post('getDescuentosPorTotal',{ id_condicion: id_condicion }, function(data) {
                $(`#ListaDescuentosM2_${indexGral}_${index}`).append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
                var len = data.length;
                for( var i = 0; i<len; i++){
                    var name = data[i]['porcentaje'];
                    var id = data[i]['id_descuento'];
                    $(`#ListaDescuentosM2_${indexGral}_${index}`).append(`<option value='${id}' label="${name}">$${ name }</option>`);
                }
                if(len<=0){
                    $(`#ListaDescuentosM2_${indexGral}_${index}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }
                if(j == 1){
                    llenar(e,indexGral,index,datos,`ListaDescuentosM2_`,id_con,leng,ifor);
                }
                $(`#ListaDescuentosM2_${indexGral}_${index}`).selectpicker('refresh');
                
            }, 'json');

            $(`#ListaDescuentosM2_${indexGral}_${index}`).select2({containerCssClass: "select-gral", dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false});
            $(`#ListaDescuentosM2_${indexGral}_${index}`).on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                crearBoxDetailDescuentos(indexGral,index,'ListaDescuentosM2_',$element[0].value,$element[0].label,1);
                rowDetail.removeClass('hidden');
            });

            $(`#ListaDescuentosM2_${indexGral}_${index}`).on("select2:unselecting", function (evt){
                var element = evt.params.args.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                var classnameExists = !!document.getElementById(`${indexGral}_${$element[0].value}_msi`);
                if(classnameExists == true){
                    document.getElementById(`${indexGral}_${$element[0].value}_msi`).outerHTML = "";
                    document.getElementById(`${indexGral}_${$element[0].value}_span`).outerHTML = "";
                }
            });
        }else{
            boxDetail.addClass('hidden');
            rowDetail.addClass('hidden');

            $(`#orden_${indexGral}_${index}`).val("");
            $(`#orden_${indexGral}_${index}`).prop( "disabled", true );
            document.getElementById(`selectDescuentos_${indexGral}_${index}`).innerHTML = "";
            document.getElementById(`listamsi_${indexGral}_${index}`).innerHTML = "";
        }
    }
    else if(id == 12){
        //Bono
        if( $(`#inlineCheckbox1_${indexGral}_${index}`).is(':checked') ) {	
            $(`#orden_${indexGral}_${index}`).prop( "disabled", false );
            id_condicion=12;		
            
            $(`#selectDescuentos_${indexGral}_${index}`).append(`
            <div class="w-100 d-flex justify-center align-center">
                <select id="ListaDescuentosBono_${indexGral}_${index}" required name="${indexGral}_${index}_ListaDescuentosBono_[]" multiple="multiple" class="form-control"  required data-live-search="true"></select>
            </div>`);

            $(`#ListaDescuentosBono_${indexGral}_${index}`).select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false});
            $.post('getDescuentosPorTotal',{ id_condicion: id_condicion }, function(data) {
                $(`#ListaDescuentosBono_${indexGral}_${index}`).append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
                var len = data.length;
                for( var i = 0; i<len; i++){
                    var name = data[i]['porcentaje'];
                    var id = data[i]['id_descuento'];
                    $(`#ListaDescuentosBono_${indexGral}_${index}`).append(`<option value='${id}' label="${name}">$${ name }</option>`);
                }
                if(len<=0){
                    $(`#ListaDescuentosBono_${indexGral}_${index}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }
                if(j == 1){
                    llenar(e,indexGral,index,datos,`ListaDescuentosBono_`,id_con,leng,ifor);
                }
                $(`#ListaDescuentosBono_${indexGral}_${index}`).selectpicker('refresh');
            }, 'json');

            $(`#ListaDescuentosBono_${indexGral}_${index}`).select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false });
            $(`#ListaDescuentosBono_${indexGral}_${index}`).on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                crearBoxDetailDescuentos(indexGral,index,'ListaDescuentosBono_',$element[0].value,$element[0].label,1);
                rowDetail.removeClass('hidden');
            });

            $(`#ListaDescuentosBono_${indexGral}_${index}`).on("select2:unselecting", function (evt){
                var element = evt.params.args.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
                var classnameExists = !!document.getElementById(`${indexGral}_${$element[0].value}_msi`);
                if(classnameExists == true){
                    document.getElementById(`${indexGral}_${$element[0].value}_msi`).outerHTML = "";
                    document.getElementById(`${indexGral}_${$element[0].value}_span`).outerHTML = "";
                }
            });
        }else{
            boxDetail.addClass('hidden');
            rowDetail.addClass('hidden');

            $(`#orden_${indexGral}_${index}`).val("");
            $(`#orden_${indexGral}_${index}`).prop( "disabled", true );
            document.getElementById(`selectDescuentos_${indexGral}_${index}`).innerHTML = "";
            document.getElementById(`listamsi_${indexGral}_${index}`).innerHTML = "";
        }
    }
    else if(id == 13){
        //MSI
        if( $(`#inlineCheckbox1_${indexGral}_${index}`).is(':checked') ) {	
            $(`#orden_${indexGral}_${index}`).prop( "disabled", false );
            id_condicion=13;			
            
            $(`#selectDescuentos_${indexGral}_${index}`).append(`
            <div class="w-100 d-flex justify-center align-center">
                <select id="ListaDescuentosMSI_${indexGral}_${index}" required name="${indexGral}_${index}_ListaDescuentosMSI_[]" multiple="multiple" class="form-control"  required data-live-search="true"></select>
            </div>`);
            
            $(`#ListaDescuentosMSI_${indexGral}_${index}`).select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false});
            $.post('getDescuentosPorTotal',{ id_condicion: id_condicion }, function(data) {
                $(`#ListaDescuentosMSI_${indexGral}_${index}`).append($('<option disabled>').val("default").text("SELECCIONA UNA OPCIÓN"));
                var len = data.length;
                for( var i = 0; i<len; i++){
                    var name = data[i]['porcentaje'];
                    var id = data[i]['id_descuento']+','+data[i]['porcentaje'];
                    //console.log(id);
                    $(`#ListaDescuentosMSI_${indexGral}_${index}`).append(`<option value='${id}' label="${name}">${name}</option>`);
                }
                if(len<=0){
                    $(`#ListaDescuentosMSI_${indexGral}_${index}`).append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
                }
                if(j == 1){
                    llenar(e,indexGral,index,datos,`ListaDescuentosMSI_`,id_con,leng,ifor);
                }
                $(`#ListaDescuentosMSI_${indexGral}_${index}`).selectpicker('refresh');
            }, 'json');
            $(`#ListaDescuentosMSI_${indexGral}_${index}`).select2({containerCssClass: "select-gral",dropdownCssClass: "custom-dropdown", closeOnSelect : false, placeholder : "SELECCIONA UNA OPCIÓN", allowHtml: true, allowClear: true, tags: false	});
            $(`#ListaDescuentosMSI_${indexGral}_${index}`).on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);
                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });
        }else{
            boxDetail.addClass('hidden');
            rowDetail.addClass('hidden');

            $(`#orden_${indexGral}_${index}`).val("");
            $(`#orden_${indexGral}_${index}`).prop( "disabled", true );
            document.getElementById(`selectDescuentos_${indexGral}_${index}`).innerHTML = "";
            document.getElementById(`listamsi_${indexGral}_${index}`).innerHTML = "";
        }
    }
}

function selectSuperficie(tipoSup){
    $('#super').val(tipoSup);
    document.getElementById("printSuperficie").innerHTML ='';
    if(tipoSup == 1){
        $('#printSuperficie').append(`
            <input type="number" class="form-control input-gral p-0 text-center h-100" name="fin" id="fin" placeholder="Mayor a" data-toggle="tooltip" data-placement="top" title="Mayor que 200">
            <input type="hidden" class="form-control" value="0" name="inicio">`);
    }
    else if(tipoSup == 2){
        $('#printSuperficie').append(`
            <input type="number" class="form-control input-gral p-0 text-center h-100" name="fin" id="fin" placeholder="Menor a" data-toggle="tooltip" data-placement="top" title="Menor que 199.99">
            <input type="hidden" class="form-control" value="0" name="inicio">`);
    }
    else if(tipoSup == 3){
        $('#printSuperficie').append(`
            <input type="hidden" class="form-control" name="inicio" value="0">
            <input type="hidden" class="form-control" name="fin" id="fin" value="0">`);
    }
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
function crearBoxDetailDescuentos(indexN,i,select,id,text,pesos = 0){
    let texto = pesos == 2 ? text : (pesos == 1 ? '$'+ text : text + '%');
    $(`#listamsi_${indexN}_${i}`).append(`
        <div class="row d-flex align-center mb-1" id="${indexN}_${id}_span">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 d-flex align-center">
                <i class="fas fa-tag mr-1"></i><p class="m-0">${texto}</p>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 pr-0">
                <div class="boxOnOff">
                    <input type="checkbox" id="${indexN}_${id}_msiC" name="${indexN}_${id}_msiC" class="switch-input d-none" onclick="turnOnOff(this)">
                    <label for="${indexN}_${id}_msiC" class="switch-label"></label>
                    <input value="0" id="${indexN}_${id}_msi" name="${indexN}_${id}_msi" class="inputMSI" onkeyup="numberMask(this);" required readonly>
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