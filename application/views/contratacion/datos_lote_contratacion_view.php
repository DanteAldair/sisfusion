<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

<body>
	<div class="wrapper">
		<?php

		switch ($this->session->userdata('id_rol')) {
			case "16": // CONTRATACIÓN
			case "6": // ASISTENTE GERENTE
			case "5": // ASISTENTE SUBIDIRECCIÓN
			case "13": // CONTRALORÍA
			case "17": // SUBDI<RECTOR CONTRALORÍA
			case "32": // CONTRALORÍA CORPORATIVA
			case "2": // SUBDIRECTOR
			case "3": // GERENTE
			case "4": // ASISTENTE SUBDIRECCIÓN
			case "9": // COORDINADOR
			case "7": // ASESOR
			case "33": // CONSULTA
			case "23": // SUBDIRECTOR CLUB MADERAS
			case "35": // ATENCIÓN A CLIENTES
			case "2": // DIRECTOR VENTAS
			case "11": // ADMINITRACIÓN
			case "12": // CAJA
			case "15": // JURÍDICO
			case "28": // EJECUTIVO ADMINISTRATIVO MKTD
			case "19": // SUBDIRECTOR MKTD
			case "20": // GERENTE MKTD
			case "50": // GENERALISTA MKTD
			case "40": // COBRANZA
			case "53": // Analista comisiones
            case "55": // POSTVENTA
			case "47": // DIRECCIÓN FINANZAS
			case "58": // ANALISTA DE DATOS
			case "61": // ASESOR CONSULTA
			case "54": // MKTD POPEA
            case '74': //  Ejecutivo Postventa(EXTERNO)
            case '75': //  Supervisor Postventa(EXTERNO)
            case '76': //  Asistente subdirección Postventa(EXTERNO)
            case '77': //  Auxiliar Postventa(EXTERNO)
            case '78': //  Base de Datos Postventa(EXTERNO)
            case '79': //  Coordinador de Postventa(EXTERNO)
            case '80': //  Coordinador de Call Center Postventa(EXTERNO)
            case '81': //  Subdirección Postventa(EXTERNO)
            case '82': //  Agente de asignación(EXTERNO)
            case '83': //  Agente de calidad(EXTERNO)
            $this->load->view('template/sidebar', "");
			break;
			default:
				echo '<script>alert("ACCESSO DENEGADO"); window.location.href="' . base_url() . '";</script>';
			break;
		}
		?>

		<!-- Modals -->
		<div class="modal fade" id="seeInformationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Consulta de historial <b id="nomLoteHistorial"></b></h4>
					</div>
					<div class="modal-body">
						<div role="tabpanel">
							<ul class="nav nav-tabs" role="tablist" style="background: #003d82;">
								<li role="presentation" class="active"><a href="#changeprocesTab" aria-controls="changeprocesTab" role="tab"
									onclick="javascript:$('#verDet').DataTable().ajax.reload();"	data-toggle="tab">Proceso de contratación</a>
								</li>
								<li role="presentation"><a href="#changelogTab" aria-controls="changelogTab" role="tab" data-toggle="tab"
								onclick="javascript:$('#verDetBloqueo').DataTable().ajax.reload();">Liberación</a>
								</li>
								<li role="presentation"><a href="#coSellingAdvisers" aria-controls="coSellingAdvisers" role="tab" data-toggle="tab"
									onclick="javascript:$('#seeCoSellingAdvisers').DataTable().ajax.reload();">Asesores venta compartida</a>
								</li>
								<?php
								$id_rol = $this->session->userdata('id_rol');
								if($id_rol == 11){
								echo '<li role="presentation"><a href="#tab_asignacion" aria-controls="tab_asignacion" role="tab" data-toggle="tab"
									onclick="fill_data_asignacion();">Asignación</a>
								</li>';
								}
								?>
								<li role="presentation" class="hide" id="li_individual_sales"><a href="#salesOfIndividuals" aria-controls="salesOfIndividuals" role="tab" data-toggle="tab">Clausulas</a></li>
							</ul>
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="changeprocesTab">
									<div class="row">
										<div class="col-md-12">
											<div class="card card-plain">
												<div class="card-content">
													<table id="verDet" class="table table-bordered table-hover" style="width:100%">
														<thead>
															<tr>
																<th>LOTE</th>
																<th>STATUS</th>
																<th>DETALLES</th>
																<th>COMENTARIO</th>
																<th>FECHA</th>
																<th>USUARIO</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
															<tr>
																<th>LOTE</th>
																<th>STATUS</th>
																<th>DETALLES</th>
																<th>COMENTARIO</th>
																<th>FECHA</th>
																<th>USUARIO</th>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div role="tabpanel" class="tab-pane" id="changelogTab">
									<div class="row">
										<div class="col-md-12">
											<div class="card card-plain">
												<div class="card-content">
													<table id="verDetBloqueo" class="table table-bordered table-hover" style="width:100%">
														<thead>
															<tr>
																<th>LOTE</th>
																<th>PRECIO</th>
																<th>FECHA LIBERACIÓN</th>
																<th>COMENTARIO LIBERACIÓN</th>
																<th>USUARIO</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
															<tr>
																<th>LOTE</th>
																<th>PRECIO</th>
																<th>FECHA LIBERACIÓN</th>
																<th>COMENTARIO LIBERACIÓN</th>
																<th>USUARIO</th>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="coSellingAdvisers">
									<div class="row">
										<div class="col-md-12">
											<div class="card card-plain">
												<div class="card-content">
													<table id="seeCoSellingAdvisers" class="table table-bordered table-hover" style="width:100%">
														<thead>
															<tr>
																<th>ASESOR</th>
																<th>COORDINADOR</th>
																<th>GERENTE</th>
																<th>FECHA ALTA</th>
																<th>USUARIO</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
															<tr>
																<th>ASESOR</th>
																<th>COORDINADOR</th>
																<th>GERENTE</th>
																<th>FECHA ALTA</th>
																<th>USUARIO</th>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="tab_asignacion">
									<div class="row">
										<div class="col-md-12">
											<div class="card card-plain">
												<div class="card-content">
													<div class="form-group">
														<label for="des">Desarrollo</label>
														<select name="sel_desarrollo" id="sel_desarrollo" class="selectpicker"
														data-style="btn btn-second" data-show-subtext="true"
														data-live-search="true"  title="" data-size="7" required>
														<option disabled selected>Selecciona un desarrollo</option></select>
													</div>
													<div class="form-group"></div>
													<div class="form-check">
														<input type="checkbox" class="form-check-input" id="check_edo">
														<label class="form-check-label" for="check_edo">Intercambio</label>
													</div>
													<div class="form-group text-right">
														<button type="button" id="save_asignacion" class="btn btn-primary">ENVIAR</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane" id="salesOfIndividuals">
									<div class="row">
										<div class="col-md-12">
											<div class="card card-plain">
												<div class="card-content">
													<h4 id="clauses_content"></h4>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="prospecto_lbl" id="prospecto_lbl">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger btn-simple" data-dismiss="modal"> CERRAR </button>
					</div>
				</div>
			</div>
		</div>
		<!-- END Modals -->

		<div class="content boxContent">
			<div class="container-fluid">
				<div class="row">
					<div class="col xol-xs-12 col-sm-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header card-header-icon fa-2x" data-background-color="goldMaderas">
								<i class="fas fa-box"></i>
							</div>
							<div class="card-content">
								<h3 class="card-title center-align">Inventario Lotes</h3>
								<div class="toolbar">
									<div class="row">
										<div class="col-md-4 form-group">
											<div class="form-group">
												<label class="m-0" for="proyecto">Proyecto*</label>
												<select id="proyecto" name="proyecto"
														class="selectpicker select-gral"
														data-style="btn" data-show-subtext="true"
														data-live-search="true"
														title="SELECCIONA UNA OPCIÓN" data-size="7" multiple size="5" required>
												</select>
											</div>
										</div>

										<div class="col-md-4 form-group">
											<div class="form-group">
												<label class="m-0" for="condominio">Condominio</label>
												<select name="condominio" id="condominio"
														class="selectpicker select-gral"
														data-style="btn" data-show-subtext="true"
														data-live-search="true"
														title="SELECCIONA UNA OPCIÓN" data-size="7" required>
													<option disabled selected>SELECCIONA UNA OPCIÓN</option>
												</select>
											</div>
										</div>

										<div class="col-md-4 form-group">
											<div class="form-group">
												<label class="m-0" for="estatus">Estatus</label>
												<select name="estatus" id="estatus" class="selectpicker select-gral"
														data-style="btn" data-show-subtext="true"
														data-live-search="true"
														title="SELECCIONA UNA OPCIÓN" data-size="7" required>
													<option disabled selected>SELECCIONA UNA OPCIÓN</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="">
								<table class="table-striped table-hover" id="tabla_inventario_contraloria" name="tabla_inventario_contraloria">
									<thead>
										<tr>
											<th>PROYECTO</th>
											<th>CONDOMINIO</th>
											<th>LOTE</th>
											<th>ID LOTE</th>
											<th>SUP.</th>
											<th>PRECIO DE LISTA</th>
											<th>TOTAL CON DESCUENTOS</th>
											<th>M2</th>
											<th>REFERENCIA</th>
											<th>MSI</th>
											<th>ASESOR</th>
											<th>COORDINADOR</th>
											<th>GERENTE</th>
											<th>ESTATUS</th>
											<th>APARTADO</th>
											<th>COMENTARIO</th>
											<th>LUGAR PROS.</th>
											<th>FECHA VAL. ENGANCHE</th>
											<th>CANTIDAD ENGANCHE PAGADO</th>
											<th>ESTATUS CONTRATACIÓN</th>
											<th>CLIENTE</th>
											<th>COPROPIETARIO (S)</th>
											<th></th>
										</tr>
									</thead>
								</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php $this->load->view('template/footer_legend');?>
	</div>
	</div><!--main-panel close-->
	<?php $this->load->view('template/footer');?>
	<!--DATATABLE BUTTONS DATA EXPORT-->
	<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script src="<?=base_url()?>dist/js/controllers/contratacion/datos_lote_contratacion.js"></script>
</body>