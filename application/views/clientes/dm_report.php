<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<body>
    <div class="wrapper">
        <?php
        $this->load->view('template/sidebar', '');
        ?>

        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                <i class="fas fa-bookmark fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title center-align">Reporte de estatus por prospecto</h3>
                                <div class="toolbar">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-lg-offset-6 col-md-offset-6 col-sm-offset-6">
                                            <div class="container-fluid p-0">
                                                <div class="row">
                                                    <div class="col-md-12 p-r">
                                                        <div class="form-group d-flex">
                                                            <input type="text" class="form-control datepicker" id="beginDate"/>
                                                            <input type="text" class="form-control datepicker" id="endDate"/>
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
                                <div class="material-datatables" id="box-masterCobranzaTable">
                                    <div class="form-group">
                                        <div class="table-responsive">
                                            <table id="mktdProspectsTable" class="table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ESTADO</th>
                                                        <th>ETAPA</th>
                                                        <th>PROSPECTO</th>
                                                        <th>MEDIO</th>
                                                        <th>ASESOR</th>
                                                        <th>GERENTE</th>
                                                        <th>CREACIÓN</th>
                                                        <th>VENCIMIENTO</th>
                                                        <th>ÚLTIMA MODIFICACIÓN</th>
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
            </div>
        </div>


        <div class="content hide">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="block full">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                            <i class="material-icons">list</i>
                                        </div>
                                        <div class="card-content">
                                            <div class="row">
                                                <h4 class="card-title">Reporte de estatus por prospecto</h4>
                                                <div class="table-responsive">
                                                    <div class="material-datatables">

                                                        <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                            <div id="external_filter_container18"><b> Búsqueda por fecha </b></div>
                                                            <br>
                                                            <div id="external_filter_container7"></div>
                                                            <br><br>
                                                        </div>

                                                        <table id="mktdProspectsTable" class="table table-striped table-no-bordered table-hover" style="text-align:center;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Estado</th>
                                                                    <th>Etapa</th>
                                                                    <th>Prospecto</th>
                                                                    <th>Medio</th>
                                                                    <th>Asesor</th>
                                                                    <th>Gerente</th>
                                                                    <th>Creación</th>
                                                                    <th>Vencimiento</th>
                                                                    <th>Última modificación</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                        </table>

                                                        <div class="modal fade" id="seeCommentsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                                                            <i class="material-icons" onclick="cleanComments()">clear</i>
                                                                        </button>
                                                                        <h4 class="modal-title">Consulta información</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div role="tabpanel">
                                                                            <ul class="nav nav-tabs" role="tablist" style="background: #003d82;">
                                                                                <li role="presentation" class="active"><a href="#commentsTab" aria-controls="commentsTab" role="tab" data-toggle="tab">Comentarios</a></li>
                                                                                <li role="presentation"><a href="#changelogTab" aria-controls="changelogTab" role="tab" data-toggle="tab">Bitácora de cambios</a></li>
                                                                            </ul>
                                                                            <div class="tab-content">

                                                                                <div role="tabpanel" class="tab-pane active" id="commentsTab">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="card card-plain">
                                                                                                <div class="card-content">
                                                                                                    <ul class="timeline timeline-simple" id="comments-list"></ul>
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
                                                                                                    <ul class="timeline timeline-simple" id="changelog"></ul>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal" onclick="cleanComments()">Cerrar</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
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
<script src="<?= base_url() ?>dist/js/controllers/mktd-1.1.0.js"></script>
<script src="<?= base_url() ?>dist/js/es.js"></script>
<!-- DateTimePicker Plugin -->
<script src="<?= base_url() ?>dist/js/bootstrap-datetimepicker.js"></script>
<!--  Full Calendar Plugin    -->
<script src="<?= base_url() ?>dist/js/fullcalendar.min.js"></script>
<script src="<?= base_url() ?>dist/js/controllers/reportes/dmReport.js"></script>
<script src="<?= base_url() ?>static/yadcf/jquery.dataTables.yadcf.js"></script>

</html>