<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

<body class="">
    <div class="wrapper ">
        <?php
            if (in_array($this->session->userdata('id_rol'), array(17, 70, 71, 73)))
                $this->load->view('template/sidebar');
            else
                echo '<script>alert("ACCESSO DENEGADO"); window.location.href="' . base_url() . '";</script>';
        ?>
        <div class="modal fade modal-alertas" id="modal_NEODATA" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <div class="row" style="text-align: center">
                            <h3>Consulta en NEODATA</h3>
                        </div>
                    </div>
                    <form method="post" id="form_NEODATA">
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                <i class="fas fa-expand fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <div class="encabezadoBox">
                                    <h3 class="card-title center-align">Reporte estatus 9</h3>
                                    <p class="card-title pl-1"></p>
                                </div>
                                <div class="toolbar">
                                    <div class="row">
                                        <div
                                            class="col-12 col-sm-6 col-md-6 col-lg-6 col-lg-offset-6 col-md-offset-6 col-sm-offset-6">
                                            <div class="container-fluid p-0">
                                                <div class="row">
                                                    <div class="col-md-12 p-r">
                                                        <div class="form-group d-flex">
                                                            <input type="text" class="form-control datepicker" id="beginDate"/>
                                                            <input type="text" class="form-control datepicker" id="endDate"/>
                                                            <button class="btn btn-success btn-round btn-fab btn-fab-mini" id="searchByDateRange"><span class="material-icons update-dataTable">search</span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="material-datatables">
                                    <table id="estatusNueveTable" name="estatusNueveTable" class="table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>PROYECTO</th>
                                                <th>CONDOMINIO</th>
                                                <th>LOTE</th>
                                                <th>REFERENCIA</th>
                                                <th>GERENTE</th>
                                                <th>ENGANCHE</th>
                                                <th>TOTAL</th>
                                                <th>FECHA ESTATUS 9</th>
                                                <th>USUARIO</th>
                                                <th>FECHA APARTADO</th>
                                                <th>REUBICACIÓN</th>
                                                <th>FECHA REUBICACIÓN</th>
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
    </div>
</body>
<?php $this->load->view('template/footer');?>
<script src="<?=base_url()?>dist/js/controllers/contraloria/status9Report.js"></script>
<script src="<?= base_url() ?>dist/js/dataTables.select.js"></script>
<script src="<?= base_url() ?>dist/js/dataTables.select.min.js"></script>
<script src="<?= base_url() ?>dist/js/moment.min.js"></script>
<script src="<?= base_url() ?>dist/js/es.js"></script>
<script src="<?= base_url() ?>dist/js/bootstrap-datetimepicker.js"></script>
<script src="<?= base_url() ?>dist/js/nouislider.min.js"></script>
<script src="<?= base_url() ?>dist/js/fullcalendar.min.js"></script>