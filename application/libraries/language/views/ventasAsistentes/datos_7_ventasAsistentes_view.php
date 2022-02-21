
<body class="">
<div class="wrapper ">
    <?php
    $dato= array(
        'home' => 0,
        'listaCliente' => 0,
        'corridaF' => 0,
        'documentacion' => 0,
        'autorizacion' => 0,
        'contrato' => 0,
        'inventario' => 0,
        'estatus8' => 0,
        'estatus14' => 0,
        'estatus7' => 1,
        'reportes' => 0,
        'estatus9' => 0,
        'disponibles' => 0,
        'asesores' => 0,
        'nuevasComisiones' => 0,
        'histComisiones' => 0,
        'prospectos' => 0,
        'prospectosAlta' => 0

    );
    //$this->load->view('template/ventas/sidebar', $dato);
	$this->load->view('template/sidebar', $dato);
    ?>
    <!--Contenido de la página-->

    <style type="text/css">
        ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
  color: white;
 opacity: 0.4;

  ::-moz-placeholder { /* Firefox 19+ */
  color: white;
  opacity: 0.4;
}
:-ms-input-placeholder { /* IE 10+ */
  color: white;
  opacity: 0.4;
}
:-moz-placeholder { /* Firefox 18- */
  color: white;
  opacity: 0.4;
}


}


    </style>
    <div class="content">
        <div class="container-fluid">
			<div class="row">
				<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<center>
						<h3>Registro Status 3 (Rechazos de status 7 Jurídico)</h3>
						<div id="loadingMsg"></div>
					</center>
					<hr>
					<br>
				</div>
			</div>
            <div class="row">
                <div class="col xol-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header card-header-icon" data-background-color="goldMaderas">
							<i class="material-icons">reorder</i>
                        </div>
                        <div class="card-content">
                            <h4 class="card-title"></h4>
                            <div class="toolbar">
                                <!--        Here you can write extra buttons/actions for the toolbar              -->
                            </div>
                            <div class="material-datatables"> 

                                <div class="form-group">
                                    <div class="table-responsive">
                                    <!--     Registro de todos los clientes con y sin expediente.  -->

                                        <table class="table table-bordered table-hover" id="tabla_clientes_7" name="tabla_clientes_7"
											   style="text-align:center;">
                                        <thead>
										<tr>
											<th>
												<center>Proyecto</center>
											</th>
											<th>
												<center>Condominio</center>
											</th>
											<th>
												<center>Lote</center>
											</th>
											<th>
												<center>Gerente</center>
											</th>
											<th>
												<center>Asesor(es)</center>
											</th>
											<th>
												<center>Cliente</center>
											</th>
											<th>
												<center>Status</center>
											</th>
											<th>
												<center>Comentario</center>
											</th>
											<th>
												<center>Fecha Vencimiento</center>
											</th>
											<th>
												<center>Fecha Realizado</center>
											</th>
											<th>
												<center>Accion</center>
											</th>
										</tr>
                                        </thead>
                                    </table>

 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="modal fade" id="edit" >
						<div class="modal-dialog modal-lg" style="width: 45%">
							<div class="modal-content" >
								<form name="revStat3" id="revStat3" enctype="multipart/form-data" >
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<center><h3 class="modal-title" id="myModalLabel">Revisión Estatus 3 (Ventas)</h3></center>
									</div>
									<div class="modal-body">
										<div class="container-fluid">
											<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<div class="form-group" style="padding-bottom: 20px;">
													<label>¿Cuáles son sus observaciones?</label>
													<textarea class="form-control" id="observaciones" name="observaciones" rows="3" width="100%" required></textarea>
												</div>
												<div class="form-group" style="text-align: center">
													<!--<label class="input-group-btn">
													  <span class="btn btn-primary">
														  Seleccionar archivo&hellip; <input type="file" name="expediente" id="expediente" style="display: none;">
													  </span>
													</label>
													<input type="text" class="form-control" readonly>-->
													<legend>Seleccionar archivo&hellip; </legend>
													<div class="fileinput fileinput-new text-center" data-provides="fileinput">
														<div class="fileinput-new ">
															<!--<img src="../../dist/img/image_placeholder.jpg" alt="...">-->
															<span class="material-icons">attach_file</span>
														</div>
														<div class="fileinput-preview fileinput-exists thumbnail"></div>
														<div>
														<span class="btn btn-primary  btn-file">
															<span class="fileinput-new">Selecciona tu archivo</span>
															<span class="fileinput-exists">Cambiar</span>
															<input type="file" name="expediente" id="expediente"/>
														</span>
															<a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Limpiar</a>
														</div>
														<input name="idLote" id="idLote" type="hidden">
														<input name="nombreLote" id="nombreLote" type="hidden">
														<input name="fechaVenc" id="fechaVenc" type="hidden">
														<input name="idCliente" id="idCliente" type="hidden">
														<input name="idCondominio" id="idCondominio" type="hidden">
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer" style="text-align: center">
										<button href="#" type="submit" id="guardar" class="btn btn-primary" >Enviar a revisión <span class="material-icons">send</span></i></button>
										<!--<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>-->
									</div>
								</form>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('template/footer_legend');?>
</div>
</div>

</div><!--main-panel close-->
</body>
<?php $this->load->view('template/footer');?>
<!--DATATABLE BUTTONS DATA EXPORT-->
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script>


    var url = "<?=base_url()?>";
    var url2 = "<?=base_url()?>index.php/";



     $("#tabla_clientes_7").ready( function(){


 

    tabla_valores_cliente_7 = $("#tabla_clientes_7").DataTable({
      dom: 'Brtip',
      width: 'auto',
  
		"language":{ "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json" },
		"buttons": [
			{
				extend: 'copyHtml5',
				text: '<i class="fa fa-files-o"></i>',
				titleAttr: 'Copy'
			},
			{
				extend: 'excelHtml5',
				text: '<i class="fa fa-file-excel-o"></i>',
				titleAttr: 'Excel'
			},
			{
				extend: 'csvHtml5',
				text: '<i class="fa fa-file-text-o"></i>',
				titleAttr: 'CSV'
			},
			{
				extend: 'pdfHtml5',
				text: '<i class="fa fa-file-pdf-o"></i>',
				titleAttr: 'PDF',
				orientation: 'landscape',
				pageSize: 'LEGAL'
			}
		],
		"columns":
				[
		{
			// data: 'nombreResidencial'
			data: function (data) {
				var mgLbl;
				if (data.tipo_venta == 1) {
					mgLbl = '<br><small class="label bg-red" style="background-color: #dd4b39 ">Venta Particular</small>';
				} else if (data.tipo_venta == 2) {
					mgLbl = '<br><small class="label bg-blue" style="background-color: #0073b7 ">Venta normal</small>';
				} else if (data.tipo_venta == 3) {
					mgLbl = '<br><small class="label bg-yellow" style="background-color: #f39c12 ">Bono</small>';
				} else if (data.tipo_venta == 4) {
					mgLbl = '<br><small class="label bg-green" style="background-color: #00a65a ">Donación</small>';
				} else if (data.tipo_venta == 5) {
					mgLbl = '<br><small class="label bg-gray" style="color: #000;background-color: #d2d6de ;">Intercambio</small>';
				}
				return data.nombreResidencial + "<br>" + mgLbl;
			}
		},
		{data: 'nombreCondominio'},
		{data: 'nombreLote'},
		{
			// data: 'gerente1'
			data: function (data) {
				var ge1, ge2, ge3, ge4, ge5;
				if (data.gerente != "" && data.gerente != null) {
					ge1 = "- " + data.gerente
				} else {
					ge1 = "";
				}
				return ge1;
			}
		},
		{
			// data: 'asesor'
			data: function (data) {
				var as1, as2, as3, as4, as5;
				if (data.asesor != "" && data.asesor != null) {
					as1 = "- " + data.asesor
				} else {
					as1 = "";
				}
				return as1;
			}
		},
		{
			data: function (data) {
				var nom1, nom2, app, apm;
				if (data.nombre != "" && data.nombre != null) {
					nom1 = data.nombre;
				} else {
					nom1 = "";
				}
				if (data.apellido_paterno != "" && data.apellido_paterno != null) {
					app = data.apellido_paterno;
				} else {
					app = "";
				}
				if (data.apellido_materno != "" && data.apellido_materno != null) {
					apm = data.apellido_materno;
				} else {
					apm = "";
				}
				return nom1 + " " + app + " " + apm;
			}
		},
		{
			data: function (data) {
				var status;
				if (data.idStatusContratacion == 3 && data.idMovimiento == 82) {
					status = "Status 7 Rechazado (Jurídico) ";
				}else {
					status = "N/A";
				}
				return status;
			}
		},
		{data: 'comentario'},
		{
			// data: 'fechaVenc'
			data: function (data) {
				if (data.idStatusContratacion == 3 && data.idMovimiento == 82) {
					return data.fechaVenc;
				} else {
					return "N/A";
				}
			}
		},
		{data: 'modificado'},
		{

			data: function (d) {

				if (d.idStatusContratacion == 3 && d.idMovimiento == 82 && d.perfil == "juridico") {
					/*index.php/registroLote/editarLoteAsistentesStatusContratacion14*/
					var mySel = '<button type="button" class="btn btn-success edit" data-idlote= "' + d.idLote + '" data-nomlote= "' + d.nombreLote + '" data-fechavenc= "' + d.fechaVenc + '" data-idcliente= "' + d.id_cliente + '" data-idcondominio= "' + d.idCondominio + '" title="Enviar a revisión a (Jurídico 7)"><span class="glyphicon glyphicon-thumbs-up"></span></button>';;

					return mySel;
				}else {
					var tituloAlert = "'Información'";
					var textoAlert = "'Este usuario puede modificar el status de este registro pero, el status actual no permite editar este registro'";
					var tipoAlert = "'info'";
					var mySel = '<a onclick="myFunctions.muestraAlerta(' + tituloAlert + ', ' + textoAlert + ', ' + tipoAlert + ')" style="cursor:pointer"><i class="material-icons">info</i>accion no disponible</a>';
					return mySel;
				}
			}
		},

 ],

columnDefs: [
{
 "searchable": false,
 "orderable": false,
 "targets": 0
},
 
],

"ajax": {
    "url": url2 + "registroLote/getJurStats7ToVentas3",
    "dataSrc": "",
    "type": "POST",
    cache: false,
    "data": function( d ){
    }
},
"order": [[ 1, 'asc' ]]

});



 

});
	var parametros;
	var idLote;
	var nombreLote;
	var fechaVenc;
	var idCliente;
	var idCondominio;


	$(document).on('click', '.edit', function(e) {

		idLote = $(this).data("idlote");
		nombreLote = $(this).data("nomlote");
		fechaVenc = $(this).data("fechavenc");
		idCliente = $(this).data("idcliente");
		idCondominio = $(this).data("idcondominio");
		$('#idLote').val($(this).data("idlote"));
		$('#nombreLote').val($(this).data("nomlote"));
		$('#fechaVenc').val($(this).data("fechavenc"));
		$('#idCliente').val($(this).data("idcliente"));
		$('#idCondominio').val($(this).data("idcondominio"));

		$('#edit').modal('show');
		e.preventDefault();
	});


	$('#revStat3').on('submit', function(e) {
		e.preventDefault();
		var $itself = $(this);
		var pass_data = $('#revStat3').serialize();
		var observaciones = $("#observaciones").val();
		var expediente = $("#expediente")[0].files[0];

		/*var data = new FormData();
		data.append("idLote", $("#idLote").val());
		data.append("nombreLote", $("#nombreLote").val());
		data.append("observaciones", observaciones);
		data.append("fechaVenc", $("#fechaVenc").val());
		data.append("expediente", expediente);
		data.append("idCliente", $("#idCliente").val());
		data.append("idCondominio", $("#idCondominio").val());*/
		var form = $('#revStat3')[0]; // You need to use standard javascript object here
		var formData = new FormData(form);


		$('#guardar').prop('disabled', true);

		console.log(formData);
		$.ajax({
			url : '<?=base_url()?>index.php/registroLote/envioRevisionVentas3aJuridico7/',/**/
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			type: 'POST',
			success: function (data, textStatus, jqXHR) {
				$('#revStat3')[0].reset();
				$("#edit").modal('hide');
				// toastr.success('Expediente enviado exitosamente');
				alerts.showNotification('top', 'right', 'Expediente enviado exitosamente', 'success');
				$('#guardar').prop('disabled', false);
				// location.reload();
				$("#tabla_clientes_7").DataTable().ajax.reload();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alerts.showNotification('top', 'right', 'Ha ocurrido un error,intentalo de nuevo', 'danger');
			}
		});

	});

	$(window).resize(function(){
        tabla_valores_cliente_7.columns.adjust();
    });


</script>

