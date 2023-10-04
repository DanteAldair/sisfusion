<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet" />

<div class="wrapper">
    <?php $this->load->view('template/sidebar'); ?>

    <div class="modal fade" id=OpenModal data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="d-flex justify-center">
                    <label class="pt-3">INGRESAR NUEVO CATALOGO</label>
                </div>
                <div class="modal-body d-flex justify-center">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" >
                                <label class="d-flex justify-center pb-2" for="id_catalogo">Catálogos(<span class="text-danger">*</span>)</label>
                                <select class="selectpicker select-gral" name="id_catalogo" id="id_catalogo" data-style="btn" data-show-subtext="true" title="SELECCIONA UNA OPCIÓN" data-size="7" data-container="body" required></select>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                                <label class="d-flex justify-center">Nombre(<span class="text-danger">*</span>)</label>
                                <input type="text" class="form-control input-gral mt-3" id="nombre" name="nombre" required>
                        </div>
                        <div class="modal-footer d-flex justify-center mt-1">
                                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                                <button type="button" id="guardarCatalogo" name="guardarCatalogo" class="btn btn-primary">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editCatalogModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-1 text-center">
                        <h4>EDITAR NOMBRE</h4>
                    </div>
                        <div class="form-group pb-2">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <input type="text" class="form-control input-gral mb-2" id="editarCatalogo" name="editarCatalogo" required>
                        </div>
					</div>
                    <input type="hidden" name="idOpcion" id="idOpcion">
                    <input type="hidden" name="id_catalogo" id="id_catalogo">
                </div>
                <div class="modal-footer d-flex justify-center pb-2">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="editOp" name="editOp" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCatalogoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header pb-0 ">
                    <h4 class="text-center" class="modal-title">¿ESTAS SEGURO DE CAMBIAR DE ESTATUS?</h4>
                </div>
                <div class="modal-body pt-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                                    <label class="d-flex justify-center pt-2" for="estatus"></label>
                                    <input type="text" class="hide" id="id_catalogo_e">
                                    <input type="text" class="hide" id="idOpcion_e">
                                    <input type="text" class="hide" id="estatus_n_e">

                                   <!-- <select name="estatus_n" id="estatus_n" class="selectpicker select-gral m-0" data-style="btn" data-show-subtext="true" title="SELECCIONA UNA OPCIÓN" data-size="7" data-container="body" required></select>-->
                                </div>
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-center mt-0">
                                        <button type="button" class="btn btn-danger btn-simple mt-1" data-dismiss="modal">Cancelar</button>
                                        <button type="button" id="btn_aceptar"class="btn btn-primary mt-1">Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                  </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header card-header-icon" data-background-color="goldMaderas">
                            <i class="fas fa-user-friends fa-2x"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title center-align">Catálogos</h3>
                            <div class="material-datatables">
                                <table id="catalogo_datatable" class="table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ID CATALOGO</th>
                                            <th>CATALOGO</th>
                                            <th>NOMBRE</th>
                                            <th>ESTATUS</th>
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
        <?php $this->load->view('template/footer_legend'); ?>
    </div>
</div>
</body>
<?php $this->load->view('template/footer'); ?>
<script src="<?= base_url() ?>dist/js/controllers/catalogos/catalogos.js"></script>