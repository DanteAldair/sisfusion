<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<body>
    <style>
        .box_cash h6{
            line-height: 19px;
            font-size: 10px;
            font-weight: 100;
            color: #999;
        }
        .box_cash span{
            font-size: 25px;
            font-weight: 600;
            color: #4e4e4e;
        }
        .estatus{
            background-color: #4caf501f;
            color: #4caf50;
            border-radius: 10px;
            padding: 1px 10px;
        }

        .timelineR {
            position: relative;
            border-color: rgba(160, 175, 185, .15);
            padding: 0;
            margin: 0
        }

        .tl-item {
            border-radius: 3px;
            position: relative;
            display: -ms-flexbox;
            display: flex
        }

        .tl-item>* {
            padding: 10px
        }

        .tl-dot {
            position: relative;
            border-color: rgba(160, 175, 185, .15)
        }

        .tl-dot:after,
        .tl-dot:before {
            content: '';
            position: absolute;
            border-color: inherit;
            border-width: 2px;
            border-style: solid;
            border-radius: 50%;
            width: 10px;
            height: 10px;
            top: 15px;
            left: 50%;
            transform: translateX(-50%)
        }

        .tl-dot:after {
            width: 0;
            height: auto;
            top: 25px;
            bottom: -15px;
            border-right-width: 0;
            border-top-width: 0;
            border-bottom-width: 0;
            border-radius: 0
        }

        .tl-date {
            font-size: .85em;
            margin-top: 2px;
            min-width: 100px;
            max-width: 100%
        }

        .b-warning {
            border-color: #243D7C!important;
        }

    </style>
    <div class="wrapper ">
        <?php
            if (in_array($this->session->userdata('id_rol'), array(18, 63, 8)))
                $this->load->view('template/sidebar', '');
            else
                echo '<script>alert("ACCESSO DENEGADO"); window.location.href="' . base_url() . '";</script>';
        ?>

        <div class="modal fade" id="detailComisionistaModal" tabindex="-1" role="dialog" aria-labelledby="detailComisionistaLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-between">
                        <h5 class="modal-title" id="exampleModalLabel">Detalle de comisiones</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="timelineR p-4 block mb-4">
                            <div class="tl-item">
                                <div class="tl-dot b-warning"></div>
                                <div class="tl-content">
                                    <div><b>2019</b></div>
                                    <div class="tl-date mt-1">Asesor comisiones <b>2</b></div>
                                    <div class="tl-date mt-1"></div>
                                </div>
                            </div>
                            <!-- <div class="tl-item">
                                <div class="tl-dot b-warning"></div>
                                <div class="tl-content">
                                    <div><b>2020</b></div>
                                    <div class="tl-date mt-1">Asesor comisiones 3</div>
                                    <div class="tl-date mt-1">Coordinador comisiones 5</div>
                                </div>
                            </div> -->
                            <div class="tl-item">
                                <div class="b-warning"></div>
                                <div class="tl-content">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title center-align">Reporte de lotes por comisionista</h3>
                                <div class="toolbar">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12 col-sm-4 col-md-4 col-lg-4 overflow-hidden">
                                                <label class="label-gral"><span class="isRequired">*</span>Comisionista</label><label class="lblEstatus ml-2"></label>
                                                <select class="selectpicker select-gral m-0" id="comisionista" name="comisionista" data-style="btn" data-show-subtext="true" data-live-search="true" title="Selecciona una opción" data-size="7" data-container="body"></select>
                                            </div>
                                            <div class="col-12 col-sm-4 col-md-4 col-lg-4 overflow-hidden">
                                                <label class="label-gral"><span class="isRequired">*</span>Tipo de usuario</label>
                                                <select class="selectpicker select-gral m-0" id="tipoUsuario" name="tipoUsuario" data-style="btn" data-show-subtext="true" data-live-search="true" title="Selecciona una opción" data-size="7" data-container="body"></select>
                                            </div>
                                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                                <div class="container-fluid p-0">
                                                    <div class="row">
                                                        <div class="col-md-12 p-r">
                                                            <div class="form-group d-flex">
                                                                <input type="text" class="form-control datepicker"
                                                                    id="beginDate" value="01/01/2022"/>
                                                                <input type="text" class="form-control datepicker" id="endDate"
                                                                    value="01/01/2022"/>
                                                                <button class="btn btn-success btn-round btn-fab btn-fab-mini"
                                                                        id="searchByDateRange">
                                                                    <span class="material-icons update-dataTable">search</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row aligned-row mt-1">
                                            <div class="col-12 col-sm-2 col-md-2 col-lg-2 d-flex align-center">
                                                <button style="border: none; background: transparent; width: fit-content; padding: 0;" data-toggle="modal" data-target="#detailComisionistaModal" id="detailComisionista">
                                                    <i class="fas fa-user mr-2" style="color: #eaeaea; font-size: 40px"></i>
                                                </button>
                                                <div>
                                                    <label style="font-size:12px; margin:0">Estatus <span class="estatus">Activo</span></label>
                                                    <label style="font-size:12px">Rol <span class="estatus">Subdirector</span></label>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-10 col-md-10 col-lg-10">
                                                <div class="row">
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 text-center">
                                                        <div class="box_cash">
                                                            <h6>TOTAL COMISIÓN<br><span id="txt_totalComision" class="cash">$0.00</span></h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 text-center">
                                                        <div class="box_cash">
                                                            <h6>TOTAL ABONADO<br><span id="txt_totalAbonado" class="cash">$0.00</span></h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 text-center">
                                                        <div class="box_cash">
                                                            <h6>TOTAL PAGADO<br><span id="txt_totalPagado" class="cash">$0.00</span></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="material-datatables" id="box-reporteLotesPorComisionista">
                                    <div class="form-group">
                                        <div class="table-responsive">
                                            <table class="table-striped table-hover"
                                                id="reporteLotesPorComisionista" name="reporteLotesPorComisionista">
                                                <thead>
                                                    <tr>
                                                        <th>PROYECTO</th>
                                                        <th>CONDOMINIO</th>
                                                        <th>LOTE</th>
                                                        <th>ID LOTE</th>
                                                        <th>CLIENTE</th>
                                                        <th>FECHA APARTADO</th>
                                                        <th>SEDE</th>
                                                        <th>ASESOR</th>
                                                        <th>COORDINADOR</th>
                                                        <th>GERENTE</th>
                                                        <th>SUBDIRECTOR</th>
                                                        <th>DIRECTOR REGIONAL</th>
                                                        <th>ESTATUS CONTRATACIÓN</th>
                                                        <th>ESTATUS VENTA</th>
                                                        <th>ESTATUS COMISIÓN</th>
                                                        <th>PRECIO FINAL</th>
                                                        <th>PORCENTAJE COMISIÓN</th>
                                                        <th>TOTAL COMISIÓN</th>
                                                        <th>TOTAL ABONADO</th>
                                                        <th>TOTAL PAGADO</th>
                                                        <th>LUGAR PROSPECCIÓN</th>
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
        <?php $this->load->view('template/footer_legend'); ?>
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
    <!--  Plugin for Date Time Picker and Full Calendar Plugin-->
    <script src="<?= base_url() ?>dist/js/moment.min.js"></script>
    <script src="<?= base_url() ?>dist/js/es.js"></script>
    <!-- DateTimePicker Plugin -->
    <script src="<?= base_url() ?>dist/js/bootstrap-datetimepicker.js"></script>
    <!-- Sliders Plugin -->
    <script src="<?= base_url() ?>dist/js/nouislider.min.js"></script>
    <!--  Full Calendar Plugin    -->
    <script src="<?= base_url() ?>dist/js/fullcalendar.min.js"></script>
    <script src="<?= base_url() ?>dist/js/controllers/comisiones/reporteLotesPorComisionista.js"></script>
</body>