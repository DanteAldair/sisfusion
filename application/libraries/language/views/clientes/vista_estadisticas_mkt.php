<body class="">
<div class="wrapper ">
	<?php #include 'sidebarParams_statistics.php'
	/**/
	$dato= array(
		'home'	=> 0,
		'prospectos' => 0,
		'prospectosMktd' => 0,
		'prospectosAlta' => 0,
		'statistics' => 0,
		'sharedSales' => 0,
		'coOwners' => 0,
		'references' => 0,
		'bulkload' => 0,
		'listaAsesores' => 0,
		'manual'	=>	0,
		'aparta' => 0,
		'mkt_digital' => 1,
		'prospectPlace' => 0,
		'documentacionMKT' => 0,
		'inventarioMKT' => 0
	);
	$this->load->view('template/sidebar', $dato);
	?>

	<div class="content" ng-controller="datos">
		<div class="container-fluid">
			<div class="container-fluid">
				<div class="row">
					<div class="container-fluid">
						<div class="card" >
							<div class="card-header card-header-icon" data-background-color="purple">
								<i class="material-icons">bubble_chart</i>
							</div>
							<div class="card-content">
								<h4 class="card-title"> Estadísticas Marketing Digital</h4>
								<div class="row">
									<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="box">
											<div class="box-header with-border">
												<form id="formUsuario" name = "formUsuario" ng-submit="function()" novalidate>
												<dir-custom-loader class="ng-hide"></dir-custom-loader>
												<div class="row">
													<div class="col-md-3 form-group">
														<label>Lugar de prospección</label>
														<select name="lugarSelect"  id="lugarSelect"
																class="selectpicker"
																data-style="select-with-transition"
																title="Elegir Lugar prospección"
																ng-model="datos.lugar"
																ng-options="item3.id_lugares as item3.nombre_lugares for item3 in lugares">

														</select>
													</div>

													<div class="col-md-3 form-group">
														<label>Sede</label>
														<select name="sedeSelect" id="sedeSelect"
																class="selectpicker"
																data-style="select-with-transition"
																title="Elegir sede"
																ng-change="changesede(datos)"
																ng-model="datos.sede"
																ng-options="item3.id_sedes as item3.nombre_sedes for item3 in sedes">
																<!-- <option value="">Seleccione una sede</option>-->
														</select>
														<span ng-show="etapaInvalido" style="color:white" ng-style="myStyle" >Por favor, seleccione una sede</span>
													</div>

													<div class="col-md-3 form-group" style="font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;">
														<label>Asesor</label>
														<select name="asesoresSelect" id="asesoresSelect"
																class="selectpicker searchAsesoresMkt"
																data-style="select-with-transition"
																title="selecciona asesor"
																ng-model="datos.asesor"
																ng-options="item4.id_asesores as item4.nombre_asesores for item4 in asesores">
																<!--<option value="">Seleccione una asesor</option>-->
														</select>
													</div>

													<div class="col-md-3 form-group">
														<br/>
														<button type="button" ng-click="ObtenerReporte(datos)" title="Generar Gráfico Nuevo"
																style="    background-color: #884EA0;color: #FFFFFF;" class="btn btn-block" >
															<span ng-show="searchButtonText == 'Searching'">
																<i class="glyphicon glyphicon-refresh spinning"></i>
															</span>{{ searchButtonText }}
														</button>
													</div>

												</div>
												<div class="row">
													<div class="col-md-3 form-group">
													</div>
													<div class="col-md-3 form-group">
														<label>Fecha inicio</label>
														<input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" ng-change="changefecha1()" ng-model="datos.fecha1" required>
													</div>

													<div class="col-md-3 form-group">
														<label>Fecha final</label>
														<input type="date" class="form-control" name="fecha_final" id="fecha_final"  ng-change="changefecha2()" ng-model="datos.fecha2" required>
													</div>
												</div>

												<div class="row">

													<div class="col-md-10">
														<div ng-app="myChart" style="width:100%;" >
															<canvas id="bar"
																	class="chart chart-line"
																	chart-data="data"
																	chart-labels="labels"
																	chart-options="options"
																	chart-dataset-override="datasetOverride"
																	chart-series="series"
																	chart-colors="colours"
																	ng-model="datos.grafica">
															</canvas>
														</div>
													</div>

													<div id = "tabla" class="col-md-2" ng-model="datos.tabla">
														<table>
															<tr>
																<th style="border:1px solid black; text-align:center; width:70px;">Mes</th>
																<th style="border:1px solid black; text-align:center;">{{ tipoDecliente }}</th>
															</tr>
															<tr ng-repeat="user in users">
																<td style="border:1px solid black; text-align:center;">{{user.mes}}</td>
																<td style="border:1px solid black; text-align:center; width:70px;">{{user.clientes}}</td>
															</tr>
														</table>

													</div>

												</div>
												<div class="row">

													<div class="col-md-3">
														<button type="button" title="Descargar Gráfica" class="btn btn-block"
																style="background-color: #0E6655;" ng-click="Download(datos)">Descargar Gráfica</button>
													</div>

													<div class="col-md-3">
														<button type="button" title="Exportar Listado a Excel" class="btn btn-success btn-block" ng-click="exportData(datos)">Exportar Listado</button>
													</div>

												</div>
											</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="Modal_export" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content ">
						<div class="modal-header header-fail">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Advertencia</h4>
						</div>
						<div class="modal-body">
							<h5 class="text-center text-body">Asegúrese de haber llenado los campos <b>Lugar de prospección</b>, <b>Sede,</b> <b>Asesor</b> y <b>Fechas (inicio y fin)</b> antes de intentar exportar. De lo contrario, su resultado se verá afectado.<h5>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-eliminar" data-dismiss="modal">Aceptar</button>
						</div>
					</div>
				</div>
			</div>
			<?php $this->load->view('template/footer_legend');?>
		</div>
	</div><!--main-panel close-->
</body>
<?php $this->load->view('template/footer');?>
<script src="<?=base_url()?>dist/js/controllers/clientes/statistics-1.1.0.js"></script>
<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
<script src="<?=base_url()?>dist/js/moment.min.js"></script>
<!-- DateTimePicker Plugin -->
<script src="<?=base_url()?>dist/js/bootstrap-datetimepicker.js"></script>
<!-- Sliders Plugin -->
<script src="<?=base_url()?>dist/js/nouislider.min.js"></script>
<!--  Full Calendar Plugin    -->
<script src="<?=base_url()?>dist/js/fullcalendar.min.js"></script>

<script src="<?=base_url()?>dist/js/controllers/clientes/alasql.min.js"></script>
<script src="<?=base_url()?>dist/js/controllers/clientes/xlsx.core.min.js"></script>
<script src="<?=base_url()?>dist/js/controllers/clientes/angular.min.js"></script>

<script src="<?=base_url()?>dist/js/Chart.js"></script>
<script src="<?=base_url()?>dist/js/angular/angular-chart.min.js"></script>
<script src="<?=base_url()?>dist/js/controllers/clientes/charts-1.1.0.js"></script>



<script>
	$(document).ready(function(){
		function setCurrency (currency) {
			if (!currency.id) { return currency.text; }
			var $currency = $('<span class="glyphicon glyphicon-' + currency.element.value + '">' + currency.text + '</span>');
			return $currency;
		};
		$(".searchAsesoresMkt").select2({
			placeholder: "Seleccione un asesor", //placeholder
			templateResult: setCurrency,
			templateSelection: setCurrency
		});
	})
</script>

<script>



	var myApp = angular.module('CRM', ["chart.js"]);
	var grafica;
	var prueba;
	var ttipo = 'PROSPECTOS '; var ta = new Date().getFullYear();
	var texto = ttipo + ta;
	var maxnumber;
	var tipo_grafica;
	var url = "<?php echo base_url().'index.php/'?>";

	myApp.controller('datos',
		function ($scope, $http) {
			// REQUEST OPTIONS USING GET METHOD.
			//angular.element.blockUI()
			$scope.myStyle = {'color':'white'}
			$scope.etapaInvalido = false;
			$scope.HideColumn = false;
			$scope.searchButtonText = "Aplicar Filtros";
			$scope.test = "false";
			$scope.tipoDecliente2 = "Clientes";
			$scope.tipoDecliente = "Prospectos";
			$scope.series = ['Prospectos'];

			var request = {
				method: 'get',
				url: url + 'MKT/get_total_mkt',
				dataType: 'json',
				contentType: "application/json"
			};

			var opts = {sheetid : 'Listado',
				headers:true,
				column: {style:{Font:{Bold:"1",Color: "#3C3741"}}},
				rows: {1:{style:{Font:{Color:"#FF0077"}}}},
				cells: {1:{1:{
							style: {Font:{Color:"#00FFFF"}}
						}}}


			};
			$scope.colours = ['#263eab', '#3498DB', '#717984', '#F1C40F'];

			$http({
				method: 'get',
				url: url + 'MKT/get_lugares'
			}).then(function successCallback(response) {
				// Store response data
				var todos = [{
					id_lugares: 'Todos',
					nombre_lugares: 'Todos'
				}];
				$scope.lugares = response.data.concat(todos);

				//console.log("data del servicio de angular");
				// console.log(response.data);
				//console.log("Longitud de la data: " + response.data.length);

				var len = response.data.length;

				for( var i = 0; i<len; i++)
				{
					var id = response.data[i]['id_lugares'];
					var name = response.data[i]['nombre_lugares'];
					$("#lugarSelect").append($('<option>').val(id).attr('label', name).text(name));
					//console.log(response.data[i]['id_lugares']);

				}

				if (len <= 0) {
					// $("#managerSelect").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
					$("#lugarSelect").append('<option selected="selected" disabled>SIN OPCIONES</option>');
				}
				else
				{
					$("#lugarSelect").append($('<option>').val('Todos').attr('label', 'Todos').text('Todos'));
				}
				$("#lugarSelect").selectpicker('refresh');
				//$scope.lugares = angular.extend(response.data, todos);


			});

			$http({
				method: 'get',
				url: url + 'MKT/get_sedes'
			}).then(function successCallback(response) {
				// Store response
				var todos1 = [{
					id_sedes: 'Todas',
					nombre_sedes: 'Todas'
				}];
				$scope.sedes = response.data.concat(todos1)
				/************/
				var len = response.data.length;

				for( var i = 0; i<len; i++)
				{
					var id = response.data[i]['id_sedes'];
					var name = response.data[i]['nombre_sedes'];
					$("#sedeSelect").append($('<option>').val(id).attr('label', name).text(name));
					//console.log(response.data[i]['id_lugares']);
				}
				if (len <= 0) {
					// $("#managerSelect").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
					$("#sedeSelect").append('<option selected="selected" disabled>SIN OPCIONES</option>');
				}
				else
				{
					$("#sedeSelect").append($('<option>').val('Todas').attr('label', 'Todas').text('Todas'));
				}
				$("#sedeSelect").selectpicker('refresh');


				/************/

			});

			// MAKE REQUEST USING $http SERVICE.
			$http(request)
				.then(function (jsonData) {
					$scope.CrearGrafica(jsonData.data);
					$scope.options = {
						legend: {
							display: true ,
							position: 'bottom'
						},
						scales: {
							yAxes: [{id: 'y-axis-1', type: 'linear', position: 'left', ticks: {min: 0, max: maxnumber}}]
						},

						animation : {
							onComplete : function(datooos){
								grafica = datooos.chartInstance.toBase64Image();
								//console.log(grafica);
							}
						},

						title: {
							display: true,
							text: 'MARKETING DIGITAL',
							fontSize: 20
						}
					};
					//angular.element.unblockUI()
				})
				.catch(function (Object) {
					alert(Object.data);
				});


			$scope.CrearGrafica = function(data){
				$scope.users = data;
				$scope.arrData = new Array;
				$scope.arrLabels = new Array;
				// LOOP THROUGH DATA IN THE JSON FILE.
				angular.forEach(data, function (item) {
					$scope.arrData.push(item.clientes);
					$scope.arrLabels.push(item.mes);
				});
				maxnumber = Math.max.apply(Math,$scope.arrData) + 10;
				$scope.data = new Array;
				$scope.labels = new Array;

				// UPDATE SCOPE PROPERTIES “data” and “label” FOR DATA.
				$scope.data.push($scope.arrData.slice(0));

				for (var i = 0; i < $scope.arrLabels.length; i++) {
					$scope.labels.push($scope.arrLabels[i]);
				}

			}

			$scope.changesede = function(datos){
				var request2 = { };
				if(datos.sede){
					//angular.element.blockUI()
					request2 = {
						method: 'POST',
						url: url + 'MKT/get_asesores',
						data: JSON.stringify({sede: datos.sede})
					};

					$http(request2)
						.then(function successCallback(response) {
							$scope.asesores = response.data;
							// console.log(response.data);
							// asesoresSelect
							/************/
							$("#asesoresSelect").empty().selectpicker('refresh');
							var len = response.data.length;

							for( var i = 0; i<len; i++)
							{
								var id = response.data[i]['id_asesores'];
								var name = response.data[i]['nombre_asesores'];
								$("#asesoresSelect").append($('<option>').val(id).attr('label', name).text(name));
								//console.log(response.data[i]['id_lugares']);
							}
							if (len <= 0) {
								// $("#managerSelect").append('<option selected="selected" disabled>No se han encontrado registros que mostrar</option>');
								$("#asesoresSelect").append('<option selected="selected" disabled>SIN OPCIONES</option>');
							}
							$("#asesoresSelect").selectpicker('refresh');
							/************/

						})
						.catch(function (Object) {
							alert(Object.data);
						});
					//angular.element.unblockUI()

				}

			};


			// $http.get(url + 'MKT/get_asesores').then(
			//     function(data){
			//         $scope.prospectos = data.data.data;
			//     },
			//     function(data){
			// });

			// $http.get(method: 'POST', url: url + 'MKT/get_asesore', data: JSON.stringify({sede: datos.sede})).then(
			//     function(data){
			//         $scope.prospectos = data.data.data;
			//     },
			//     function(data){
			// });

			$scope.ObtenerReporte = function(data){
				$scope.myStyle = {'color':'red'}
				var a = $scope.formUsuario.lugarSelect.$viewValue;
				var b = $scope.formUsuario.sedeSelect.$viewValue;
				var c = $scope.formUsuario.asesoresSelect.$viewValue;

				if(a == undefined || b == undefined){

				}else{
					if(a != undefined && b != undefined && c != undefined){
						request = {
							method: 'POST',
							url: url + 'MKT/get_chart_complete',
							data: JSON.stringify({lugar : data.lugar, sede: data.sede, asesor: data.asesor,  fecha_ini : data.fecha1, fecha_fin : data.fecha2})
						};
						$http(request)
							.then(function (jsonData) {

								$scope.CrearGrafica(jsonData.data);
								$scope.options = {
									legend: {
										display: true ,
										position: 'bottom'
									},
									scales: {
										yAxes: [{id: 'y-axis-1', type: 'linear', position: 'left', ticks: {min: 0, max: maxnumber}}]
									},

									animation : {
										onComplete : function(datooos){
											grafica = datooos.chartInstance.toBase64Image();
											//console.log(grafica);
										}
									},

									title: {
										display: true,
										text: 'MARKETING DIGITAL',
										fontSize: 20
									}
								};

							})

					}
					if(a != undefined && b != undefined && (c == undefined || c == null)){
						request = {
							method: 'POST',
							url: url + 'MKT/get_chart_mkt',
							data: JSON.stringify({lugar : data.lugar, sede: data.sede, fecha_ini : data.fecha1, fecha_fin : data.fecha2})
						};
						$http(request)
							.then(function (jsonData) {

								$scope.CrearGrafica(jsonData.data);
								$scope.options = {
									legend: {
										display: true ,
										position: 'bottom'
									},
									scales: {
										yAxes: [{id: 'y-axis-1', type: 'linear', position: 'left', ticks: {min: 0, max: maxnumber}}]
									},

									animation : {
										onComplete : function(datooos){
											grafica = datooos.chartInstance.toBase64Image();
											//console.log(grafica);
										}
									},

									title: {
										display: true,
										text: 'MARKETING DIGITAL',
										fontSize: 20
									}
								};

							})

					}


				}

			}

			$scope.exportData = function (data) {
				var a = $scope.formUsuario.lugarSelect.$viewValue;
				var b = $scope.formUsuario.sedeSelect.$viewValue;
				var c = $scope.formUsuario.asesoresSelect.$viewValue;

				if(a == undefined || b == undefined){
					$("#Modal_export").modal("show");
				}
				else{
					//angular.element.blockUI()
					if(a != undefined && b != undefined && c != undefined){
						request = {
							method: 'POST',
							url: url + 'MKT/get_report_complete',
							data: JSON.stringify({lugar : data.lugar, sede: data.sede, asesor: data.asesor,  fecha_ini : data.fecha1, fecha_fin : data.fecha2})
						};
					}
					if(a != undefined && b != undefined && (c == undefined || c == null)){
						request = {
							method: 'POST',
							url: url + 'MKT/get_report_mkt',
							data: JSON.stringify({lugar : data.lugar, sede: data.sede, fecha_ini : data.fecha1, fecha_fin : data.fecha2})
						};
					}

					$http(request)
						.then(function (jsonData) {
							alasql('SELECT * INTO XLSX("Listado.xlsx",?) FROM ?',[opts,jsonData.data]);
							//angular.element.unblockUI()

						})
						.catch(function (Object) {
							alert(Object.data);
						});


				}
			};

			$scope.function = function(data){

			}

			$scope.changefecha1 = function(){
				$scope.datos.fecha2 = null;
				$scope.fecha2Invalido = $scope.formUsuario.fecha_inicio.$invalid;
			};

			$scope.changefecha2 = function(){
				$scope.fechaInvalido = $scope.formUsuario.fecha_final.$invalid;
			};

			$scope.Download = function() {

				$http.get(grafica, {
					responseType: "arraybuffer"
				})
					.then(function(data) {
						//angular.element.blockUI()
						var anchor = angular.element('<a/>');
						//var blob = new Blob([data]);
						var blob = new Blob( [ data ]);
						anchor.attr({
							href: grafica,
							target: '_blank',
							download: 'Grafica.jpg'
						})[0].click();
						//angular.element.unblockUI()
					})
			};

		});

</script>

</html>
