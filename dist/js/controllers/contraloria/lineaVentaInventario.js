
var estatus,usuariosVentas;
sp = {
    initFormExtendedDatetimepickers: function () {
      var today = new Date();
      var date =
        today.getFullYear() +
        "-" +
        (today.getMonth() + 1) +
        "-" +
        today.getDate();
      var time = today.getHours() + ":" + today.getMinutes();
  
      $(".datepicker").datetimepicker({
        format: "DD/MM/YYYY",
        icons: {
          time: "fa fa-clock-o",
          date: "fa fa-calendar",
          up: "fa fa-chevron-up",
          down: "fa fa-chevron-down",
          previous: "fa fa-chevron-left",
          next: "fa fa-chevron-right",
          today: "fa fa-screenshot",
          clear: "fa fa-trash",
          close: "fa fa-remove",
          inline: true,
        },
      });
    },
  };
  
  sp2 = {
    initFormExtendedDatetimepickers: function () {
      $(".datepicker2").datetimepicker({
        format: "DD/MM/YYYY",
        icons: {
          time: "fa fa-clock-o",
          date: "fa fa-calendar",
          up: "fa fa-chevron-up",
          down: "fa fa-chevron-down",
          previous: "fa fa-chevron-left",
          next: "fa fa-chevron-right",
          today: "fa fa-screenshot",
          clear: "fa fa-trash",
          close: "fa fa-remove",
          inline: true,
        },
        minDate: new Date(),
      });
    },
  };
$(document).ready(function(){ /**FUNCIÓN PARA LLENAR EL SELECT DE PROYECTOS(RESIDENCIALES)*/
sp.initFormExtendedDatetimepickers();
sp2.initFormExtendedDatetimepickers();
$(".datepicker").datetimepicker({ locale: "es" });

    // BEGIN DATE
    const fechaInicio = new Date();
    // Iniciar en este año, este mes, en el día 1
    const beginDate = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), 1);
    // END DATE
    const fechaFin = new Date();
    // Iniciar en este año, el siguiente mes, en el día 0 (así que así nos regresamos un día)
    const endDate = new Date(fechaFin.getFullYear(), fechaFin.getMonth() + 1, 0);
    finalBeginDate2 = [('0' + beginDate.getDate()).slice(-2), ('0' + (beginDate.getMonth() + 1)).slice(-2), beginDate.getFullYear()].join('/');
    finalEndDate2 = [('0' + endDate.getDate()).slice(-2), ('0' + (endDate.getMonth() + 1)).slice(-2), endDate.getFullYear()].join('/');
    
    $('#beginDate').val(finalBeginDate2);
    $('#endDate').val(finalEndDate2);

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

            $.post(`${general_base_url}Contratacion/lista_estatus`, function (data) {
                var len = data.length;
                for (var i = 0; i < len; i++) {
                    var id = data[i]['idStatusLote'];
                    var name = data[i]['nombre'];
                    $("#estatus").append($('<option>').val(id).text(name.toUpperCase()));
                }
                $("#estatus").selectpicker('refresh');
            }, 'json');


            $.post(`${general_base_url}contraloria/allUserVentas`, function (data) {
                    usuariosVentas = data;
            }, 'json');

            $('#anio').html("");
            var d = new Date();
            var n = d.getFullYear();
            for (var i = n; i >= 2020; i--){
              var id = i;
              $("#anio").append($('<option>').val(id).text(id));
            }
            $("#anio").selectpicker('refresh');

});




$('#residencial').change(function(){
    var residencial = $(this).val();
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


//VARIABLES DECLARADAS PARA LA OPTIMIZACION DE COLUMNAS AL MOMENTO DE GENERAR ARCHIVOS DESCARGABLES (XLSX Y PDF)
let titulos_encabezado = [];
let num_colum_encabezado = [];
$('#tabla_lineaVenta thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_encabezado.push(title);
    num_colum_encabezado.push(i);
    $(this).html(`<input type="text"
                         class="textoshead w-100"
                         data-toggle="tooltip" 
                         data-placement="top"
                         title="${title}"
                         placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#tabla_lineaVenta').DataTable().column(i).search() !== this.value) {
            $('#tabla_lineaVenta').DataTable().column(i).search(this.value).draw();
        }
    });
});
//Eliminamos la ultima columna que es "Acciones"
num_colum_encabezado.pop();
function formatDate(date) {
    var dateParts = date.split("/");
    var d = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]),
      month = "" + (d.getMonth() + 1),
      day = "" + d.getDate(),
      year = d.getFullYear();
    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;
  
    return [year, month, day].join("-");
  }
$(document).on('click','#searchByDateRange', function () {
    fechaInicio = $("#beginDate").val();
    fechaFin =  $("#endDate").val();
    fechaInicio = formatDate(fechaInicio);
    fechaFin = formatDate(fechaFin);
    tabla_inventario = $("#tabla_lineaVenta").DataTable({
        dom: 'Brt' + "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        destroy: true,
        initComplete: function () {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: "hover"
            });
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Inventario Lotes',
                title: "Inventario Lotes",
                exportOptions: {
                    columns: num_colum_encabezado,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezado[columnIdx] +' ';
                        }
                    }
                }
            },
        ],
        language: {
            url: general_base_url+'static/spanishLoader_v2.json',
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        pageLength: 10,
        bAutoWidth: false,
        bLengthChange: false,
        scrollX: true,
        bInfo: true,
        searching: true,
        paging: true,
        ordering: false,
        fixedColumns: true,
        columnDefs: [{
            defaultContent: "",
            targets: "_all",
            searchable: true,
            orderable: false
        }],
        columns:
            [{
        
                data: 'nombreResidencial'
            },
            {
                "data": function (d) {
                    
                    return '<p>' + (d.nombreCondominio).toUpperCase() + '</p>';
                }
            },
            {
                data: 'nombreLote'
            },
            {
                data: 'referencia'
            },
            {
                data: function (d) {
                    return d.asesor;
                }
            },
            {
                data: function (d) {
                    return d.coordinador;
                }
            },
            {
                data: function (d) {
                    return d.gerente;
                }
            },
            {
                data: function (d) {
                    return d.subdirector;
                }
            },
            {
                data: function (d) {
                    return d.regional;
                }
            },
            {
                data: function (d) {
                      return  d.regional2;
                }
            },
            {
                data: function (d) {
                      return  d.id_asesor == 12205 ? `<center><span class="label lbl-azure">ASESOR COMODÍN</span> <center>` : 'NORMAL';
                }
            },
            {
                data: function (d) {
                    let libContraloria = (d.observacionContratoUrgente == '1') ? '<center><span class="label lbl-pink">Lib. Contraloría</span> <center><p><p>' : '';
                    let compartida =  d.banderaVC != null || d.banderaVC != undefined ? '<center><span class="label lbl-violetBoots">Compartida</span><center><p><p>' : '';
                    let registro = `<center><span class="label lbl-violetBoots">${d.registro}</span><center><p><p>`; 
                    return d.tipo_venta == null ?
                        `<center><span class="label" style="background:#${d.background_sl}; color:#${d.color};">${d.descripcion_estatus}</span> ${libContraloria} <center>${compartida} ${registro}` :
                        `<center><span class="label" style="background:#${d.background_sl}; color:#${d.color};">${d.descripcion_estatus}</span> <p><p> <span class="label lbl-green">${d.tipo_venta}</span> ${libContraloria} <center>${compartida} ${registro}`;
                }
            },
            {
                "data": function (d) {
                    if (d.idStatusLote == 8 || d.idStatusLote == 9 || d.idStatusLote == 10) {
                        if (d.fecha_modst == 'null' || d.fecha_modst == 'NULL' || d.fecha_modst == null || d.fecha_modst == '') {
                            return '-';
                        } else {
                            return '<p>' + d.fecha_modst + '</p>';
                        }
                    } else {
                        if (d.fechaApartado == 'null' || d.fechaApartado == 'NULL' || d.fechaApartado == null || d.fechaApartado == '') {
                            return '-';
                        } else {
                            return '<p>' + d.fechaApartado + '</p>';
                        }
                    }
                }
            },
            {
                data: function(d) {
                    if(d.ubicacion != null)
                        return `<center><span class="label lbl-oceanGreen">${d.ubicacion}</span> <center>`;
                    else
                        return `<center><span class="label lbl-gray">NO APLICA</span> <center>`;                   
                }         
            },
            {
                "data": function (d) {
                    $('[data-toggle="tooltip"]').tooltip({
                        trigger: "hover"
                    });
                    return d.comision == null ? `<center><button class="editButton btn-data btn-yellow" data-accion="1" data-banderaVC="${d.banderaVC}" data-idCliente="${d.idCliente}" title="Ver inventario"><i class="fas fa-eye"></i></button></button><button data-accion="2" class="editButton btn-data btn-sky" data-banderaVC="${d.banderaVC}" data-idCliente="${d.idCliente}" title= "Editar línea de venta"><i class="fas fa-edit"></i></button></center>` : `<center><button class="editButton btn-data btn-yellow" data-accion="1" data-banderaVC="${d.banderaVC}" data-idCliente="${d.idCliente}" title="Ver inventario"><i class="fas fa-eye"></i></center>`;
                }
            }],
            ajax: {
                url: `${general_base_url}index.php/contraloria/get_inventario`,
                type: "POST",
                cache: false,
                data: {fechaInicio:fechaInicio,fechaFin:fechaFin}
            }      
    });

    $(window).resize(function () {
        tabla_inventario.columns.adjust();
    });
    
});

$('#tabla_lineaVenta').on('draw.dt', function() {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "hover"
    })
});



/*
const numeros  = [1,2,3,1];
let duplicados = [];
 
for (let i = 0; i < tempArray.length; i++) {
  if (tempArray[i + 1] === tempArray[i]) {
    duplicados.push(tempArray[i]);
    contador++;
  }
}
console.log(contador);
console.log(duplicados);
*/
function validarAsesor(id_asesor,bandera,len,origen,index){
    let  id_asesores = [];
    let duplicados = [];

    let  contador = 0;
    let asesorCliente = $(`#id_asesor`).val();
    console.log(asesorCliente)

    id_asesores.push(asesorCliente);
    if(bandera != 0 && bandera != null){
        setTimeout(() => {
        for (let m = 0; m < len; m++) {
            let idAsesorCompartida = $(`#id_asesor_${m}`).val();
            console.log(idAsesorCompartida)
                id_asesores.push(idAsesorCompartida);
        }
        const arrTemporal = [...id_asesores].sort();
       // console.log(idAsesorCompartida)

        for (let i = 0; i < arrTemporal.length; i++) {
            if (arrTemporal[i + 1] === arrTemporal[i]) {
              duplicados.push(arrTemporal[i]);
              contador++;
            }
          }
          console.log(id_asesores)

console.log(arrTemporal)
          if(contador == 0){
            $('#btnInventario').prop('disabled', false)
            $('#btnInventario').removeClass('hide');
          }else{
            $('#btnInventario').addClass('hide');
            $('#btnInventario').prop('disabled', true); 
            alerts.showNotification("top", "right", "Los asesores no pueden estan repetidos, favor de verificarlo", "warning");
          }

        }, 1000);
       /* if(origen == 1){ //SELECT CLIENTES
            let c = 0;
            for (let m = 0; m < len; m++) {
                    let idAsesorCompartida = $(`#id_asesor_${m}`).val();
                    if(asesorCliente == idAsesorCompartida){
                        c++;
                        $('#btnInventario').prop('disabled', true); 
                        alerts.showNotification("top", "right", "El asesor seleccionado ya se seleccionó anteriormente.", "warning");   
                    }
            }
              c == 0 ? $('#btnInventario').prop('disabled', false) : '';
        }else{  //SELECT VENTAS COMPARTIDAS
            let c = 0;
            asesorActualComp = $(`#id_asesor_${index}`).val();
            if(asesorActualComp == asesorCliente){
                $('#btnInventario').prop('disabled', true); 
                alerts.showNotification("top", "right", "El asesor seleccionado ya se seleccionó anteriormente.", "warning");  
            }else{
                for (let j = 0; j < len; j++) {     
                    let idAsesorCompartida = $(`#id_asesor_${j}`).val();
                    if(j != index){
                        if(asesorActualComp == idAsesorCompartida){
                            c++;
                            $('#btnInventario').prop('disabled', true); 
                            alerts.showNotification("top", "right", "El asesor seleccionado ya se seleccionó anteriormente.", "warning");   
                        }  
                    }      
                }
                c == 0 ? $('#btnInventario').prop('disabled', false) : '';
            }
        }*/
        
    }
}

$(document).on('click', '.editButton', function(){
    $('#spiner-loader').removeClass('hide');
    var $itself = $(this);
    let idCliente = $itself.attr('data-idCliente');
    let accion = $itself.attr('data-accion');
    let banderaVC = $itself.attr('data-banderaVC') != null && $itself.attr('data-banderaVC') != undefined ? 1 : 0;
    document.getElementById('modalI').innerHTML = '';
    $.post(`${general_base_url}contraloria/getLineaVenta`,{idCliente:idCliente,banderaVC:banderaVC}, function (data) {        
        console.log(data);
        let len = data.compartidas.length;

        if(accion == 1){ //mostrar inventario 

            $('#btnInventario').addClass('hide');
            $('#modalI').append(`
            <h5>Cliente inventario</h5>
                <div class="row">
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Asesor</label>
                        <p>${data.clientes[0].asesor}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Coordinador</label>
                        <p>${data.clientes[0].coordinador}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Gerente</label>
                        <p>${data.clientes[0].gerente}</p>
                    </div>
                </div>
                <div class="row">
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Subdirector</label>
                            <p>${data.clientes[0].subdirector}</p>
                        </div>
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Regional</label>
                            <p>${data.clientes[0].regional}</p>
                        </div>
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Regional 2</label>
                            <p>${data.clientes[0].regional2}</p>
                        </div>
                </div>      
            `);

            if (len <= 0) { //NO HAY VENTAS COMPARTIDAS
            }else{ //SI HAY VENTAS COMPARTIDAS
                $('#modalI').append(`<h4>Venta compartida</h4`);
                for (let m = 0; m < len; m++) {
                $('#modalI').append(`
                <h5>Línea ${m +1}</h5>
                <div class="row">
                    <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Asesor</label>
                            <p>${data.compartidas[m].asesor}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Coordinador</label>
                            <p>${data.compartidas[m].coordinador}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Gerente</label>
                            <p>${data.compartidas[m].gerente}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Subdirector</label>
                        <p>${data.compartidas[m].subdirector}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Regional</label>
                        <p>${data.compartidas[m].regional}</p>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Regional 2</label>
                        <p>${data.compartidas[m].regional2}</p>
                    </div>
                </div> 
                `);
                }
            }

        }else{
            $('#btnInventario').removeClass('hide');
        $('#modalI').append(`
        <h5>Cliente inventario</h5>
                <div class="row">
                    <div class="col-lg-4  overflow-hidden">
                    <input type="hidden" value="${idCliente}" name="id_cliente" id="id_cliente">
                    <input type="hidden" value="${banderaVC}" name="banderaVC" id="banderaVC">
                    <input type="hidden" value="${len}" name="indexVC" id="indexVC">
                        <label class="control-label">Asesor</label>
                        <select class="selectpicker select-gral m-0 asesor" onchange="validarAsesor(${data.clientes[0].id_asesor},${banderaVC},${len},1,0)" name="id_asesor" id="id_asesor" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        required ></select>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Coordinador</label>
                        <select class="selectpicker select-gral m-0 coordinador" name="id_coordinador" id="id_coordinador" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        ></select>
                    </div>
                    <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Gerente</label>
                        <select class="selectpicker select-gral m-0 gerente" name="id_gerente" id="id_gerente" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        required></select>
                    </div>
                </div>
                <div class="row">
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Subdirector</label>
                            <select class="selectpicker select-gral m-0 subdirector" name="id_subdirector" id="id_subdirector" data-style="btn"
                            data-show-subtext="true"
                            title="Selecciona una opción"
                            data-size="7"
                            data-live-search="true" data-container="body"
                            required></select>
                        </div>
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Regional</label>
                            <select class="selectpicker select-gral m-0 subdirector" name="id_regional" id="id_regional" data-style="btn"
                            data-show-subtext="true"
                            title="Selecciona una opción"
                            data-size="7"
                            data-live-search="true" data-container="body"
                            ></select>
                        </div>
                        <div class="col-lg-4  overflow-hidden">
                            <label class="control-label">Regional 2</label>
                            <select class="selectpicker select-gral m-0 subdirector" name="id_regional_2" id="id_regional_2" data-style="btn"
                            data-show-subtext="true"
                            title="Selecciona una opción"
                            data-size="7"
                            data-live-search="true" data-container="body"
                            ></select>
                        </div>
                </div>
        `);


        let asesores = usuariosVentas.filter(asesor => asesor.id_rol == 7);
        let coordinadores = usuariosVentas.filter(asesor => asesor.id_rol == 9);
        let gerentes = usuariosVentas.filter(asesor => asesor.id_rol == 3);
        let subdirectores = usuariosVentas.filter(asesor => asesor.id_rol == 2);

        if (len <= 0) { //NO HAY VENTAS COMPARTIDAS
        }else{ //SI HAY VENTAS COMPARTIDAS
            $('#modalI').append(`<h4>Venta compartida</h4`);
            for (let m = 0; m < len; m++) {
            $('#modalI').append(`
            <h5>Línea ${m +1}</h5>
            <div class="row">
                <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Asesor</label>
                        <select class="selectpicker select-gral m-0 asesor" onchange="validarAsesor(${data.compartidas[0].id_asesor},${banderaVC},${len},2,${m})" name="id_asesor_${m}" id="id_asesor_${m}" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        required ></select>
                </div>
                <input type="hidden" value="${data.compartidas[m].id_vcompartida}" name="id_vcompartida_${m}" id="id_vcompartida_${m}">
                <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Coordinador</label>
                        <select class="selectpicker select-gral m-0 coordinador" name="id_coordinador_${m}" id="id_coordinador_${m}" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        ></select>
                </div>
                <div class="col-lg-4  overflow-hidden">
                        <label class="control-label">Gerente</label>
                        <select class="selectpicker select-gral m-0 gerente" name="id_gerente_${m}" id="id_gerente_${m}" data-style="btn"
                        data-show-subtext="true"
                        title="Selecciona una opción"
                        data-size="7"
                        data-live-search="true" data-container="body"
                        required></select>
                </div>
                <div class="col-lg-4  overflow-hidden">
                    <label class="control-label">Subdirector</label>
                    <select class="selectpicker select-gral m-0 subdirector" name="id_subdirector_${m}" id="id_subdirector_${m}" data-style="btn"
                    data-show-subtext="true"
                    title="Selecciona una opción"
                    data-size="7"
                    data-live-search="true" data-container="body"
                    required></select>
                </div>
                <div class="col-lg-4  overflow-hidden">
                    <label class="control-label">Regional</label>
                    <select class="selectpicker select-gral m-0 subdirector" name="id_regional_${m}" id="id_regional_${m}" data-style="btn"
                    data-show-subtext="true"
                    title="Selecciona una opción"
                    data-size="7"
                    data-live-search="true" data-container="body"
                    ></select>
                </div>
                <div class="col-lg-4  overflow-hidden">
                    <label class="control-label">Regional 2</label>
                    <select class="selectpicker select-gral m-0 subdirector" name="id_regional_2_${m}" id="id_regional_2_${m}" data-style="btn"
                    data-show-subtext="true"
                    title="Selecciona una opción"
                    data-size="7"
                    data-live-search="true" data-container="body"
                    ></select>
                </div>
            </div> 
            `);
            }
        }
        for (var i = 0; i < asesores.length; i++) {
            // console.log()
             var id = asesores[i].id_usuario;
             var name = asesores[i].nombre;
             $(".asesor").append($('<option>').val(id).text(name.toUpperCase()));
         }
         $(".coordinador").append($('<option>').val(0).text('NO APLICA'));
         for (var i = 0; i < coordinadores.length; i++) {
             // console.log()
              var id = coordinadores[i].id_usuario;
              var name = coordinadores[i].nombre;
              $(".coordinador").append($('<option>').val(id).text(name.toUpperCase()));
          }
          for (var i = 0; i < gerentes.length; i++) {
             // console.log()
              var id = gerentes[i].id_usuario;
              var name = gerentes[i].nombre;
              $(".gerente").append($('<option>').val(id).text(name.toUpperCase()));
          }
          $(".subdirector").append($('<option>').val(0).text('NO APLICA'));
          for (var i = 0; i < subdirectores.length; i++) {
             // console.log()
              var id = subdirectores[i].id_usuario;
              var name = subdirectores[i].nombre;
              $(".subdirector").append($('<option>').val(id).text(name.toUpperCase()));
          }
          $("#id_asesor").selectpicker();
          $('#id_asesor').val(parseInt(data.clientes[0].id_asesor)).trigger('change');
          $("#id_coordinador").selectpicker();
          $('#id_coordinador').val(parseInt(data.clientes[0].id_coordinador)).trigger('change');
          $("#id_gerente").selectpicker();
          $('#id_gerente').val(parseInt(data.clientes[0].id_gerente)).trigger('change');
          $("#id_subdirector").selectpicker();
          $('#id_subdirector').val(parseInt(data.clientes[0].id_subdirector)).trigger('change');
          $("#id_regional").selectpicker();
          $('#id_regional').val(parseInt(data.clientes[0].id_regional == null ? 0 : data.clientes[0].id_regional)).trigger('change');
          $("#id_regional_2").selectpicker();
          $('#id_regional_2').val(parseInt(data.clientes[0].id_regional_2 == null ? 0 : data.clientes[0].id_regional_2)).trigger('change');

          if(banderaVC != 0 && banderaVC != 0){
            for (let o = 0; o < data.compartidas.length; o++) {
                $(`#id_asesor_${o}`).selectpicker();
                $(`#id_asesor_${o}`).val(parseInt(data.compartidas[o].id_asesor)).trigger('change');
                $(`#id_coordinador_${o}`).selectpicker();
                $(`#id_coordinador_${o}`).val(parseInt(data.compartidas[o].id_coordinador)).trigger('change');
                $(`#id_gerente_${o}`).selectpicker();
                $(`#id_gerente_${o}`).val(parseInt(data.compartidas[o].id_gerente)).trigger('change');
                $(`#id_subdirector_${o}`).selectpicker();
                $(`#id_subdirector_${o}`).val(parseInt(data.compartidas[o].id_subdirector)).trigger('change');
                $(`#id_regional_${o}`).selectpicker();
                $(`#id_regional_${o}`).val(parseInt(data.compartidas[o].id_regional == null ? 0 : data.clientes[0].id_regional)).trigger('change');
                $(`#id_regional_2_${o}`).selectpicker();
                $(`#id_regional_2_${o}`).val(parseInt(data.compartidas[o].id_regional_2 == null ? 0 : data.clientes[0].id_regional_2)).trigger('change');             
                $(`#id_asesor_${o}`).selectpicker('refresh'); 
            }
          }
        $(".asesor").selectpicker('refresh');
        $(".coordinador").selectpicker('refresh');
        $(".gerente").selectpicker('refresh');
        $(".subdirector").selectpicker('refresh');
        }
        $('#spiner-loader').addClass('hide');
        $('#modalLineaVenta').modal('show');
    }, 'json'); 

});

$(document).on("submit", "#formLineaVentas", function (e) {
    e.preventDefault();
    let datos = new FormData($(this)[0]);
    $('#spiner-loader').removeClass('hide');
    $.ajax({
        type: "POST",
        url:  `${general_base_url}Contraloria/EditarInventario`,
        data: datos,
        processData: false,
        contentType: false, 
        success: function(data){
            //data = JSON.parse(data);
            $('#spiner-loader').addClass('hide');
            if(data==true){
                alerts.showNotification("top", "right", "El inventario se actualizó correctamente.", "success");
                tabla_inventario.ajax.reload(null,false);    
            }else{
                alerts.showNotification("top", "right", "Ha ocurrido un error intentalo nuevamente.", "danger");
            }
            $('#modalLineaVenta').modal('hide');
        },
        async:   false,
        error: function() {
            $('#modalLineaVenta').modal('hide');
            $('#spiner-loader').addClass('hide');
            alerts.showNotification("top", "right", "Oops, algo salió mal.", "danger");
        }
    });

});

$(document).on("click", ".ver_historial", function () {
    var tr = $(this).closest('tr');
    var row = tabla_inventario.row(tr);
    idLote = $(this).val();
    var $itself = $(this);

    var element = document.getElementById("li_individual_sales");

    if ($itself.attr('data-tipo-venta') == 'Venta de particulares') {
        $.getJSON(`${general_base_url}Contratacion/getClauses/` + idLote).done(function (data) {
            $('#clauses_content').html(data[0]['nombre']);
        });
        element.classList.remove("hide");
    } else {
        element.classList.add("hide");
        $('#clauses_content').html('');
    }

    $("#seeInformationModal").on("hidden.bs.modal", function () {
        $("#changeproces").html("");
        $("#changelog").html("");
        $('#nomLoteHistorial').html("");
    });
    $("#seeInformationModal").modal();

    var urlTableFred = '';
    $.getJSON(`${general_base_url}Contratacion/obtener_liberacion/` + idLote).done(function (data) {
        urlTableFred = `${general_base_url}Contratacion/obtener_liberacion/` + idLote;
        fillFreedom(urlTableFred);
    });


    var urlTableHist = '';
    $.getJSON(`${general_base_url}Contratacion/historialProcesoLoteOp/` + idLote).done(function (data) {
        $('#nomLoteHistorial').html($itself.attr('data-nomLote'));
        urlTableHist = `${general_base_url}Contratacion/historialProcesoLoteOp/` + idLote;
        fillHistory(urlTableHist);
    });

    var urlTableCSA = '';
    $.getJSON(`${general_base_url}Contratacion/getCoSallingAdvisers/` + idLote).done(function (data) {
        urlTableCSA = `${general_base_url}Contratacion/getCoSallingAdvisers/` + idLote;
        fillCoSellingAdvisers(urlTableCSA);
    });
});

function fillLiberacion(v) {
    $("#changelog").append('<li class="timeline-inverted">\n' +
        '<div class="timeline-badge success"></div>\n' +
        '<div class="timeline-panel">\n' +
        '<label><h5><b>LIBERACIÓN - </b>' + v.nombreLote + '</h5></label><br>\n' +
        '<b>ID:</b> ' + v.idLiberacion + '\n' +
        '<br>\n' +
        '<b>Estatus:</b> ' + v.estatus_actual + '\n' +
        '<br>\n' +
        '<b>Comentario:</b> ' + v.observacionLiberacion + '\n' +
        '<br>\n' +
        '<span class="small text-gray"><i class="fa fa-clock-o mr-1"></i> ' + v.nombre + ' ' + v.apellido_paterno + ' ' + v.apellido_materno + ' - ' + v.modificado + '</span>\n' +
        '</h6>\n' +
        '</div>\n' +
        '</li>');
}

function fillProceso(i, v) {
    $("#changeproces").append('<li class="timeline-inverted">\n' +
        '<div class="timeline-badge info">' + (i + 1) + '</div>\n' +
        '<div class="timeline-panel">\n' +
        '<b>' + v.nombreStatus + '</b><br><br>\n' +
        '<b>Comentario:</b> \n<p><i>' + v.comentario + '</i></p>\n' +
        '<br>\n' +
        '<b>Detalle:</b> ' + v.descripcion + '\n' +
        '<br>\n' +
        '<b>Perfil:</b> ' + v.perfil + '\n' +
        '<br>\n' +
        '<b>Usuario:</b> ' + v.usuario + '\n' +
        '<br>\n' +
        '<span class="small text-gray"><i class="fa fa-clock-o mr-1"></i> ' + v.modificado + '</span>\n' +
        '</h6>\n' +
        '</div>\n' +
        '</li>');

    // comentario, perfil, modificado,
}

let titulos_encabezadoh = [];
let num_colum_encabezadoh = [];
$('#verDet thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_encabezadoh.push(title);
    num_colum_encabezadoh .push(i);
    $(this).html(`<input type="text"
                         class="textoshead w-100"
                         data-toggle="tooltip" 
                         data-placement="top"
                         title="${title}"
                         placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#verDet').DataTable().column(i).search() !== this.value) {
            $('#verDet').DataTable().column(i).search(this.value).draw();
        }
    });
});

function fillHistory(urlTableHist) {
    tableHistorial = $('#verDet').DataTable({
        dom: "<'container-fluid pb-1 p-0'<'row'<'col-xs-12 col-sm-6 col-md-6 col-lg-6'B><'col-xs-12 col-sm-6 col-md-6 col-lg-6'f>>>" + "rt" + "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width:"100%",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                className: 'btn buttons-excel',
                titleAttr: 'Excel',
                exportOptions: {
                    columns: num_colum_encabezadoh,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadoh[columnIdx] +' ';
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                className: 'btn buttons-pdf',
                titleAttr: 'PDF',
                exportOptions: {
                    columns: num_colum_encabezadoh,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadoh[columnIdx] +' ';
                        }
                    }
                }
            }
        ],
        columnDefs: [{
            defaultContent: "",
            targets: "_all",
            searchable: true,
            orderable: false
        }],
        "scrollX": true,
        "pageLength": 10,
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        "destroy": true,
        "ordering": false,
        columns: [
            { "data": "nombreLote" },
            { "data": "nombreStatus" },
            { "data": "descripcion" },
            { "data": "comentario" },
            { "data": "modificado" },
            { "data": "usuario" }

        ],
        "ajax":
        {
            "url": urlTableHist,
            "dataSrc": ""
        },
        initComplete: function () {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: "hover"
            });
        }
    });
}

let titulos_encabezadob = [];
let num_colum_encabezadob = [];
$('#verDetBloqueo thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_encabezadob.push(title);
    num_colum_encabezadob .push(i);
    $(this).html(`<input type="text"
                         class="textoshead w-100"
                         data-toggle="tooltip" 
                         data-placement="top"
                         title="${title}"
                         placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#verDet').DataTable().column(i).search() !== this.value) {
            $('#verDet').DataTable().column(i).search(this.value).draw();
        }
    });
});

function fillFreedom(urlTableFred) {
    tableHistorialBloqueo = $('#verDetBloqueo').DataTable({
        responsive: true,

        dom: "<'container-fluid pb-1 p-0'<'row'<'col-xs-12 col-sm-6 col-md-6 col-lg-6'B><'col-xs-12 col-sm-6 col-md-6 col-lg-6'f>>>" + "rt" + "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width:"100%",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                className: 'btn buttons-excel',
                exportOptions: {
                    columns: num_colum_encabezadob,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadob[columnIdx] +' ';
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF',
                className: 'btn buttons-pdf',
                exportOptions: {
                    columns: num_colum_encabezadob,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadob[columnIdx] +' ';
                        }
                    }
                }
            }
        ],
        columnDefs: [{
            defaultContent: "",
            targets: "_all",
            searchable: true,
            orderable: false
        }],
        "scrollX": true,
        "pageLength": 10,
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        "destroy": true,
        "ordering": false,
        columns: [
            { "data": "nombreLote" },
            { "data": "precio" },
            { "data": "modificado" },
            { "data": "observacionLiberacion" },
            { "data": "userLiberacion" }

        ],
        "ajax":
        {
            "url": urlTableFred,
            "dataSrc": ""
        },
    });
}

let titulos_encabezadoc = [];
let num_colum_encabezadoc = [];
$('#seeCoSellingAdvisers thead tr:eq(0) th').each(function (i) {
    var title = $(this).text();
    titulos_encabezadoc.push(title);
    num_colum_encabezadoc .push(i);
    $(this).html(`<input type="text"
                         class="textoshead w-100"
                         data-toggle="tooltip" 
                         data-placement="top"
                         title="${title}"
                         placeholder="${title}"/>`);
    $('input', this).on('keyup change', function () {
        if ($('#verDet').DataTable().column(i).search() !== this.value) {
            $('#verDet').DataTable().column(i).search(this.value).draw();
        }
    });
});

function fillCoSellingAdvisers(urlTableCSA) {
    tableCoSellingAdvisers = $('#seeCoSellingAdvisers').DataTable({
        responsive: true,
        dom: "<'container-fluid pb-1 p-0'<'row'<'col-xs-12 col-sm-6 col-md-6 col-lg-6'B><'col-xs-12 col-sm-6 col-md-6 col-lg-6'f>>>" + "rt" + "<'container-fluid pt-1 pb-1'<'row'<'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'i><'col-xs-12 col-sm-12 col-md-12 col-lg-12 d-flex justify-center'p>>>",
        width:"100%",
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i>',
                titleAttr: 'Excel',
                className: 'btn buttons-excel',
                exportOptions: {
                    columns: num_colum_encabezadoc,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadoc[columnIdx] +' ';
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i>',
                titleAttr: 'PDF',
                className: 'btn buttons-pdf',
                exportOptions: {
                    columns: num_colum_encabezadoc,
                    format: {
                        header: function (d, columnIdx) {
                            return ' '+titulos_encabezadoc[columnIdx] +' ';
                        }
                    }
                }
            }
        ],
        columnDefs: [{
            defaultContent: "",
            targets: "_all",
            searchable: true,
            orderable: false
        }],
        "scrollX": true,
        "pageLength": 10,
        language: {
            url: `${general_base_url}static/spanishLoader_v2.json`,
            paginate: {
                previous: "<i class='fa fa-angle-left'>",
                next: "<i class='fa fa-angle-right'>"
            }
        },
        "destroy": true,
        "ordering": false,
        columns: [
            { "data": "asesor" },
            { "data": "coordinador" },
            { "data": "gerente" },
            { "data": "fecha_creacion" },
            { "data": "creado_por" }

        ],
        "ajax":
        {
            "url": urlTableCSA,
            "dataSrc": ""
        },
    });
}