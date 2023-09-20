<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

<body class="">
    <div class="wrapper ">
        <?php $this->load->view('template/sidebar'); ?>

        <div class="modal fade" id="liberarReestructura" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content" > 
					<div class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-1 text-center">
                            <h4>¿Está usted seguro de liberar el lote?</h4>
                        </div>
                        <br>
                        <input type="hidden" name="idLote" id="idLoteenvARevCE" >
                        <input type="hidden" name="nombreLote" id="nombreLoteAv" >
                        <input type="hidden" name="precio" id="precioAv" >        
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
						<button type="button" id="saveLi" name="saveLi" class="btn btn-primary">Aceptar</button>
					</div>
				</div>
			</div>
		</div>

        <div class="modal fade" id="catalogoRee" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content" > 
					<div class="modal-header">
                        <div class="row d-flex justify-center align-center">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
						        <h4 class="modal-title text-center">CATALOGO</h4>
                            </div>
                        </div>
					</div>
					<div class="modal-body">
                        <div class="material-datatables">
                            <div class="form-group">
                                <table class="table-striped table-hover" id="tableCatalogo" name="tableCatalogo">
                                    <thead>
                                        <tr>
                                            <th>OPCIÓN CATALOGO</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
					</div>
				</div>
			</div>
		</div>

        <div class="modal fade" id="modalBorrar" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-sm">
				<div class="modal-content"> 
					<div class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-1 text-center">
                            <h4>¿Está seguro de borrar la opción?</h4>
                        </div>
                        <br>
                        <input type="hidden" name="idOpcion" id="idOpcion">       
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
						<button type="button" id="borrarOp" name="borrarOp" class="btn btn-primary">Aceptar</button>
					</div>
				</div>
			</div>
		</div>

        <div class="modal fade modal-alertas" id="catalogoNuevo" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-center">Cargar nueva opción</h5>
					</div>
					<form id="addNewDesc">
						<input type="hidden" value="0" name="id_opcion" id="id_opcion">
						<div class="form-group d-flex justify-center">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<input type="text" class="form-control input-gral" id="inputCatalogo" name="inputCatalogo" required>
							</div>
						</div>
						<div class="container-fluid">
							<div class="row mt-1 mb-1 d-flex align-center">
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<input type="button" class="btn btn-danger btn-simple m-0" data-dismiss="modal" value="CANCELAR">
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<input type="button" class="btn btn-primary" name="guardarCatalogo"  id="guardarCatalogo" value="GUARDAR">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

        <div class="modal fade modal-alertas" id="editarModel" role="dialog">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-center">Editar opción</h5>
					</div>
					<form id="addNewDesc">
						<input type="hidden" value="0" name="id_opcionEdit" id="id_opcionEdit">
						<div class="form-group d-flex justify-center">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<input type="text" class="form-control input-gral" id="editarCatalogo" name="editarCatalogo" required>
							</div>
						</div>
						<div class="container-fluid">
							<div class="row mt-1 mb-1 d-flex align-center">
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<input type="button" class="btn btn-danger btn-simple m-0 align-center" data-dismiss="modal" value="CANCELAR">
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
									<input type="button" class="btn btn-primary" name="guardarEdit"  id="guardarEdit" value="GUARDAR">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

        <div class="modal fade" id="aceptarReestructura" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content" > 
					<div class="modal-header">
						<h4 class="modal-title text-center">Validar lote para reestructura</h4>
					</div>
					<div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <label>OPCIONES</label>
                                <select name="grabado" id="grabado" class="selectpicker select-gral m-0 grabado" data-style="btn" data-show-subtext="true" title="SELECCIONA UNA OPCIÓN" data-size="7" data-container="body" required></select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p-1">
                            <label>COMENTARIO</label>
                            <textarea class="text-modal" id="comentario2" rows="3"></textarea>
                        </div>
                        <br>
                        <input type="hidden" name="idLoteCatalogo" id="idLoteCatalogo" >
					</div>
					<div class="modal-footer">
						<button type="button" id="cancelarValidacion" class="btn btn-danger btn-simple cancelarValidacion" data-dismiss="modal">Cancelar</button>
						<button type="button" id="guardarValidacion" name="guardarValidacion" class="btn btn-primary guardarValidacion">Registrar</button>
					</div>
				</div>
			</div>
		</div>

        <div class="modal fade" id="modal_historial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
						<h4 class="modal-title text-center">HISTORIAL LOTE</h4>
					</div>
                    <div class="modal-body">
                        <div role="tabpanel">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="historialTap">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content scroll-styles" style="height: 350px; overflow: auto">
                                                    <ul class="timeline-3" id="historialLine"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal" ><b>Cerrar</b></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title center-align">Reestructuración</h3>
                                <div class="toolbar">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label class="control-label overflow-hidden" for="proyecto">Proyecto</label>
                                                <select name="proyecto" id="proyecto" class="selectpicker select-gral m-0" data-style="btn" data-show-subtext="true" data-live-search="true" title="SELECCIONA UNA OPCIÓN" data-size="7" data-container="body" required></select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"></div>
                                    </div>
                                </div>
                                <div class="material-datatables">
                                    <div class="form-group">
                                        <table class="table-striped table-hover" id="tabla_clientes" name="tabla_clientes">
                                            <thead>
                                                <tr>
                                                    <th>PROYECTO</th>
                                                    <th>CONDOMINIO</th>
                                                    <th>LOTE</th>
                                                    <th>ID LOTE </th>
                                                    <th>SUPERFICIE</th>
                                                    <th>PRECIO</th>
                                                    <th>NOMBRE</th>
                                                    <th>OBSERVACIÓN EN LIBERACIÓN</th>
                                                    <th>ACCIONES</th>
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
            <?php $this->load->view('template/footer_legend'); ?>
        </div>
    </div>
</body>
<?php $this->load->view('template/footer'); ?>
<script src="<?= base_url() ?>dist/js/controllers/reestructura/reestructura.js"></script>