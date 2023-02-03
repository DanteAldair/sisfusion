<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>dist/css/shadowbox.css">

<body>
    <div class="wrapper">
        <?php $this->load->view('template/sidebar', ""); ?>
        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <ul class="nav nav-tabs nav-tabs-cm">
                            <li class="active">
                                <a href="#solicitudes" role="tab" id="solicitudes_tabla" data-toggle="tab">Solicitudes</a>
                            </li>

                            <li>
                                <a href="#carga_test" role="tab" id="testimonio_tabla" data-toggle="tab">Carga testimonio</a>
                            </li>
                        </ul>
                        <div class="card no-shadow m-0">
                            <div class="card-content">
                                <div class="nav-tabs-custom">
                                    <div class="tab-content p-2">
                                        <div class="tab-pane active" id="solicitudes">
                                            <div class="encabezadoBox">
                                                <h3 class="card-title center-align">Solicitudes escrituración</h3>
                                            </div>
                                            <div class="toolbar">
                                                <div class="row">
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                        <div class="form-group label-floating select-is-empty">
                                                            <select id="estatusE" name="estatusE" class="selectpicker select-gral m-0" data-style="btn" data-show-subtext="true" data-live-search="true" title="Selecciona un estatus" data-size="7" required>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                        <div class="container-fluid p-0">
                                                            <div class="row">
                                                                <div class="col-md-12 p-r">
                                                                    <div class="form-group d-flex">
                                                                        <input type="text" class="form-control datepicker" id="beginDate" value="" autocomplete='off' />
                                                                        <input type="text" class="form-control datepicker" id="endDate" value="" autocomplete='off' />
                                                                        <button class="btn btn-success btn-round btn-fab btn-fab-mini" id="searchByDateRange">
                                                                            <span class="material-icons update-dataTable">search</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="material-datatables">
                                                <div class="form-group">
                                                    <table class="table-striped table-hover" id="escrituracion-datatable" name="escrituracion-datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>ID SOLICITUD</th>
                                                                <th>PROYECTO</th>
                                                                <!-- <th>CONDOMINIO</th> -->
                                                                <th>LOTE</th>
                                                                <th>CLIENTE</th>
                                                                <th>FECHA DE CREACIÓN</th>
                                                                <th>ESTATUS</th>
                                                                <th>ÁREA</th>
                                                                <th>COMENTARIOS</th>
                                                                <th>OBSERVACIONES</th>
                                                                <th>ACCIONES</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="carga_test">
                                            <div class="encabezadoBox">
                                                <h3 class="card-title center-align">Carga testimonio</h3>
                                            </div>
                                            <div class="toolbar">
                                                <div class="row">
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                        <div class="form-group label-floating select-is-empty"></div>
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                        <div class="container-fluid p-0">
                                                            <div class="row">
                                                                <div class="col-md-12 p-r">
                                                                    <div class="form-group d-flex">
                                                                        <input type="text" class="form-control datepicker" id="startDate" value="" autocomplete='off' />
                                                                        <input type="text" class="form-control datepicker" id="finalDate" value="" autocomplete='off' />
                                                                        <button class="btn btn-success btn-round btn-fab btn-fab-mini" id="searchByDateTest">
                                                                            <span class="material-icons update-dataTable">search</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="material-datatables">
                                                <div class="form-group">
                                                    <table class="table-striped table-hover" id="carga-datatable" name="carga-datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>ID SOLICITUD</th>
                                                                <th>PROYECTO</th>
                                                                <!-- <th>CONDOMINIO</th> -->
                                                                <th>LOTE</th>
                                                                <th>CLIENTE</th>
                                                                <th>FECHA DE CREACIÓN</th>
                                                                <th>ESTATUS</th>
                                                                <th>ÁREA</th>
                                                                <th>COMENTARIOS</th>
                                                                <th>OBSERVACIONES</th>
                                                                <th>ACCIONES</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php include 'common_modals.php' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('template/footer_legend'); ?>
    </div>
    <!--main-panel close-->
</body>
<?php $this->load->view('template/footer'); ?>
<!--DATATABLE BUTTONS DATA EXPORT-->
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>dist/js/shadowbox.js"></script>
<script>
    userType = <?= $this->session->userdata('id_rol') ?>;
    idUser = <?= $this->session->userdata('id_usuario') ?>;
    typeTransaction = 1;
    Shadowbox.init();
</script>

<!-- MODAL WIZARD -->
<script src="<?= base_url() ?>dist/js/modal-steps.min.js"></script>
<script src="<?= base_url() ?>dist/js/moment.min.js"></script>
<script src="<?= base_url() ?>dist/js/es.js"></script>
<script src="<?= base_url() ?>dist/js/bootstrap-datetimepicker.js"></script>
<script src="<?= base_url() ?>static/yadcf/jquery.dataTables.yadcf.js"></script>
<script src="<?= base_url() ?>dist/js/controllers/general/main_services.js"></script>
<script src="<?= base_url() ?>dist/js/controllers/postventa/solicitudes_escrituracion.js"></script>

</html>