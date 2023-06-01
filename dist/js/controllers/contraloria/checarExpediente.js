
var sedes,tipos_venta;
$(document).ready(function(){ /**FUNCIÓN PARA LLENAR EL SELECT DE PROYECTOS(RESIDENCIALES)*/
    $.post(`${general_base_url}General/getResidencialesList`, function (data) {        
        let len = data.length;
        for (let i = 0; i < len; i++) {
            let id = data[i]['idResidencial'];
            let name = data[i]['descripcion'];
            $("#residencial").append($('<option>').val(id).text(name));
        } 
        if (len <= 0) {
            $("#residencial").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
        }
        $("#residencial").selectpicker('refresh');
        $('#spiner-loader').addClass('hide');
    }, 'json'); 


    $.post("get_tventa", function (data) {
        tipos_venta = data;
    }, 'json');

    $.post("get_sede", function (data) {
        sedes = data;
    }, 'json');

});

$('#residencial').change(function(){
    var residencial = $(this).val();
    //$('#tableClient').DataTable().clear();
    $("#condominio").empty().selectpicker('refresh');
    $("#lotes").empty().selectpicker('refresh');
    $.post(`${general_base_url}General/getCondominiosList`,{idResidencial:residencial}, function (data) {  
            data = JSON.parse(data);
            let len = data.length;
            for( let i = 0; i<len; i++)
            {
                let id = data[i]['idCondominio'];
                let name = data[i]['nombre'];
                $("#condominio").append($('<option>').val(id).text(name));
            }
            $("#condominio").selectpicker('refresh');
            $('#spiner-loader').addClass('hide');
    });
});

$('#condominio').change(function(){
    var condominio = $(this).val();
    $("#lotes").selectpicker('refresh');
    $.post(`${general_base_url}General/getLotesList`,{idCondominio:condominio,typeTransaction:0}, function (data) {  
            data = JSON.parse(data);
            let len = data.length;
            for( let i = 0; i<len; i++)
            {
                let id = data[i]['idLote'];
                let name = data[i]['nombreLote'];
                $("#lotes").append($('<option>').val(id).text(name));
            }
            $("#lotes").selectpicker('refresh');
            $('#spiner-loader').addClass('hide');
    });
});
var contador=0;
var datosTable;
function llenarClientes(idLote){

    datosTable = [];
    var lote = idLote;
    $.ajax({
        url:`${general_base_url}RegistroCliente/getClientByID`,
        type: 'POST',
        data:{idLote:lote,idCliente:''},
        success: function(data) {
            contador == 0 ? $('#tableClient').removeClass('hide') : '';
            contador = contador == 0 ? 1 : 1;
                    data = JSON.parse(data);
                    datosTable = data;
                    let datosSelect = data.data;
                    console.log(data)
                    let len = datosSelect.length;

                    $('#spiner-loader').addClass('hide');  
                 },
        async:   false
   }); 
    construirTableClient('','',datosTable);
}
$('#lotes').change(function(){
    var lote = $(this).val();
    llenarClientes(lote);
});


let titulos = [];
$('#tableClient thead tr:eq(0) th').each(function (i) {
        var title = $(this).text();
        titulos.push(title);
        $(this).html(`<input data-toggle="tooltip" data-placement="top" placeholder="${title}" title="${title}"/>`);
        $('input', this).on('keyup change', function () {
            if ($('#tableClient').DataTable().column(i).search() !== this.value) {
                $('#tableClient').DataTable()
                    .column(i)
                    .search(this.value)
                    .draw();
            }
        });
    $('[data-toggle="tooltip"]').tooltip();
});

/*$('#clientes').change(function(){
    datosTable = [];
    var idCliente = $(this).val();
    $.ajax({
        url:`${general_base_url}RegistroCliente/getClientByID`,
        type: 'POST',
        data:{idLote:'',idCliente:idCliente},
        success: function(data) {
                    data = JSON.parse(data);
                    datosTable = data;
                 },
        async:   false
   });
    construirTableClient('',idCliente,datosTable);
});*/

function construirTableClient(idCliente = '',idLote = '',datos = ''){

    console.log(datos.data)
    let opcionConsulta = 'getClientsByLote'
    tableClient = $("#tableClient").DataTable({
        dom: 'Brt'+ "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width: '100%',
        data:datos.data,
        scrollX: true,
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
            className: 'btn buttons-excel',
            titleAttr: 'Descargar archivo de Excel',
            title: 'AUTORIZACIONES PLANES DE VENTAS',
            exportOptions: {
                columns: [0,1,2,3,4,5,7,8,9,10,11,12,13,14,15,16,17,18,19],
                format: {
                    header:  function (d, columnIdx) {
                        return titulos[columnIdx];
                    }
                }
            },
        },{
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf" aria-hidden="true"></i>',
            className: 'btn buttons-pdf',
            titleAttr: 'Regreso de expediente',
            title: "Regreso de expediente",
            orientation: 'landscape',
            pageSize: 'LEGAL',
            exportOptions: {
                columns: [0,1,2,3,4,5,7,8,9,10,11,12,13,14,15,16,17,18,19],
                format: {
                    header:  function (d, columnIdx) {
                        return titulos[columnIdx];
                    }
                }
            }
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
        columns: [
            {
                "width": "8%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.nombreResidencial+'</p>';
                }
            },

            {
                "width": "8%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.nombreCondominio+'</p>';
                }
            },
            {
                "width": "12%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.idLote+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.nombreLote+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.nombreStatus+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">$'+formatMoney(d.totalNeto)+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">$'+formatMoney(d.totalNeto2)+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">$'+formatMoney(d.totalValidado)+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.bandera8+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.validacionEng+'</p>';
                } 
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.tventa+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.registroComision+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.gerente+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.coordinador+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.asesor+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.nomCliente+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.fechaApartado+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    let lblStatus='';
                    if(d.estatus_cliente == 1){
                        lblStatus = '<span class="label lbl-green">ACTIVO</span>';
                    }else{
                        lblStatus = '<span class="label lbl-warning">INACTIVO</span>';
                    }
                    return '<p style="font-size: .8em">'+lblStatus+'</p>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<span class="label lbl-blueMaderas">'+d.estatus_lote+'</span>';
                }
            },
            {
                "width": "10%",
                "data": function( d ){
                    return '<p style="font-size: .8em">'+d.lp+'</p>';
                }
            },
            {
                "width": "10%",
                data: null,
                render: function ( data, type, row )
                {
                    let button_action='';
                    if(data.estatus_cliente==0){
                        
                        button_action = data.hlidStatus < 5 ? `<center><a class="backButton btn-data btn-warning" title= "Regresar expediente" style="cursor:pointer;" data-idLote="${data.idLote}" data-nomLote="${data.nombreLote}" data-nombreCliente="${data.nomCliente}" data-idCliente="${data.id_cliente}"><i class="fas fa-history"></i></a></center>` : 
                        `<center><a class="editButton btn-data btn-warning" title= "Regresar expediente" data-accion="1" style="cursor:pointer;" data-idStatusConstruccion="${data.hlidStatus}" data-idLote="${data.idLote}" data-nomLote="${data.nombreLote}" data-nombreCliente="${data.nomCliente}" data-idCliente="${data.id_cliente}"><i class="fas fa-history"></i></a></center>`;
                    }else{

                        if(data.hlidStatus >= 5){
                            button_action = `<center><a class="editButton btn-data btn-warning" data-accion="2" title= "Regresar expediente" style="cursor:pointer;" data-idStatusConstruccion="${data.hlidStatus}" data-idLote="${data.idLote}" data-nomLote="${data.nombreLote}" data-nombreCliente="${data.nomCliente}" data-idCliente="${data.id_cliente}"><i class="fas fa-history"></i></a></center>`;
                        }
                    }
                    return button_action;
                }
            }
        ],
        columnDefs: [{}],
        order: [
            [1, 'asc']
        ]
    });

}


const permisosEstatus = [
    {
        idStatusContratacion : [5,6,7], // idStatusContratacion in(5,6,7)
        campos: ['ubicacion','tipo_venta'],
        title: ['Ubicacion','Tipo venta']
    },
    {
        idStatusContratacion : [8,9,10],
        campos: ['totalNeto','totalNeto2','comentario','ubicacion','tipo_venta'],
        title: ['Total Neto','Precio final con descuento','Comentario','Ubicación' ,'Tipo venta']

    },
    {
        idStatusContratacion : [11,12,13,14,15], // idStatusContratacion >= 11
        campos:  ['totalValidado','totalNeto','totalNeto2','comentario','ubicacion','tipo_venta'],          
        title: ['Total validado','Total Neto','Precio final con descuento','Comentario','Ubicación' ,'Tipo venta']

    },
];

$(document).on('click', '.editButton', function(){
    var $itself = $(this);
    var datosPorTr = tableClient.row($(this).parents('tr')).data();
    console.log(datosPorTr);
    let cliente = $itself.attr('data-nombreCliente');
    let accion = $itself.attr('data-accion');
    $('#idCliente').val(datosPorTr.id_cliente);
    $('#accion').val(accion);
    let idStatusConstrataccion = $itself.attr('data-idStatusConstruccion');
    if(idStatusConstrataccion < 5){ //SI ES MENOR A 5 SE PASAN LOS DATOS A NULL
    
    }else{ //SI EL ESTATUS ES MAYOR IGUAL A 5, SE CONSULTAN LOS CAMPOS A EDITAR
        //OBTENERMOS LOS CAMPOS A EDITAR DEPENDIENDO DEL ULTIMO idStatusContruccion REGISTRADO
        document.getElementById('camposEditar').innerHTML = '';
        const permisos = permisosEstatus.filter(element => element.idStatusContratacion.find(element2 => element2 == idStatusConstrataccion));
        console.log(permisos[0].campos);
        for (let m = 0; m < permisos[0].campos.length; m++) {
            if(permisos[0].campos[m] == 'comentario'){
                $('#camposEditar').append(`
                    <div class="form-group m-0">
                        <label>${permisos[0].title[m]}</label>
                            <textarea class="text-modal" rows="1" required name="${permisos[0].campos[m]}" id="${permisos[0].campos[m]}"></textarea>
                    </div>`);
            }else if(permisos[0].campos[m] == 'ubicacion' || permisos[0].campos[m] == 'tipo_venta'){
                $('#camposEditar').append(`
                    <div class="col-lg-12">
                    <div class="form-group m-0 overflow-hidden">
                    <label>${permisos[0].title[m]}</label>
                    <select class="selectpicker select-gral" data-container="body" tabindex="-1" required="required" title="SELECCIONA UNA OPCIÓN" name="${permisos[0].campos[m]}" id="${permisos[0].campos[m]}">
                    </select>
                </div>
                    </div>    
                `);
            }else{
               let columna = permisos[0].campos[m] == 'totalNeto' ? datosPorTr.totalNeto :(permisos[0].campos[m] == 'totalNeto2' ? datosPorTr.totalNeto2 : datosPorTr.totalValidado) ;
                $('#camposEditar').append(`
                    <div class="form-group m-0">
                        <label>${permisos[0].title[m]}</label>
                        <input class="form-control input-gral" type="text" required value="$${formatMoney(columna)}" name="${permisos[0].campos[m]}" id="${permisos[0].campos[m]}">
                    </div>`);
            }                        
        }    
        
        var len = sedes.length;
        for (var i = 0; i < len; i++) {
            var id = sedes[i]['id_sede'];
            var name = sedes[i]['nombre'];
            $("#ubicacion").append($('<option>').val(id).text(name.toUpperCase()));
        }
        var len = tipos_venta.length;
        for (var i = 0; i < len; i++) {
            var id = tipos_venta[i]['id_tventa'];
            var name = tipos_venta[i]['tipo_venta'];
            $("#tipo_venta").append($('<option>').val(id).text(name.toUpperCase()));
        }
        $("#tipo_venta").selectpicker('refresh');
        $("#ubicacion").selectpicker('refresh');
    }
    $('#modalEditExp').modal();
});



function RegresarExpo(datos){

    let idLote = $('#lotes').val();
    let accion = $('#accion').val();
    let ruta = accion = 2 ? 'updateLote' : 'return_status_uno';
    $.ajax({
        type: "POST",
        url:  `${general_base_url}Restore/${ruta}/`,
        data: datos,
        processData: false,
        contentType: false, 
        success: function(data){
            $('#spiner-loader').addClass('hide');
            $('#tempIDC').val(0);
            $('#idLote').val(0);
            $('#accion').val(0);
            console.log(data.data);
            if(data.data==true){
                llenarClientes(idLote);
                alerts.showNotification("top", "right", "Se ha regresado el expediente correctamente.", "success");
            }else{
                alerts.showNotification("top", "right", "Ha ocurrido un error intentalo nuevamente.", "danger");
            }
            $('#modalConfirmRegExp').modal('hide');
        },
        async:   false,
        error: function() {
            $('#modalConfirmRegExp').modal('hide');
    
            $('#spiner-loader').addClass('hide');
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        }
    });
}

$(document).on("submit", "#formEdit", function (e) {
    e.preventDefault();
    let datos = new FormData($(this)[0]);
    RegresarExpo(datos);

});

$(document).on('click', '.backButton', function(){
var $itself = $(this);
let nombreLote = $itself.attr('data-nomLote');
let cliente = $itself.attr('data-nombreCliente');
let idCliente = $itself.attr('data-idCliente');
let idLote = $itself.attr('data-idLote');
console.log("nombreLote: ", nombreLote);
console.log("cliente: ", cliente);
console.log("idCliente: ", idCliente);
$('#tempIDC').val(idCliente);
$('#loteName').text(nombreLote);
$('#idLote').text(idLote);
$('#modalConfirmRegExp').modal();
});

$(document).on('click', '.acepta_regreso', function(e){
        let idCliente = $('#tempIDC').val();
        datos = new FormData();
        datos.append("totalValidado","N/A");
        datos.append("totalNeto","N/A");
        datos.append("totalNeto2","N/A");
        datos.append("comentario", "N/A")
        datos.append("tipo_venta","N/A");
        datos.append("ubicacion","N/A");
        datos.append("idCliente", idCliente);
    RegresarExpo(datos);
});

