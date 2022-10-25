<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<body>
    <div class="wrapper ">
     
    <?php
        $datos = array();
        $datos = $datos4;
        $datos = $datos2;
        $datos = $datos3;  
        $this->load->view('template/sidebar', $datos);
        ?>
  

        <div class="modal fade " id="modalConfirmRequest" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content"> 
                    <div class="modal-body">
                        <div class="modal-body text-center">
                            <h5>¿Estás segura de hacer este movimiento? </h5>
                            <p style="font-size: 0.8em">Marcarás este lote para solicitar que se disperese la comisión.</p>
                        </div>
                        <input id="idLote" class="hide">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="sendRequestCommissionPayment">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="seeInformationModalAsimilados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="material-icons" onclick="cleanCommentsAsimilados()">clear</i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist" style="background: #949494;">
                                <div id="nameLote"></div>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="changelogTab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content">
                                                    <ul class="timeline timeline-simple" id="comments-list-asimilados"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal" onclick="cleanCommentsAsimilados()"><b>Cerrar</b></button>
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
                                <h3 class="card-title center-align">Cobranza master</h3>
                                <div class="toolbar">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group d-flex">
                                                <input type="number" class="form-control idLote" id="idLote"
                                                    placeholder="ID lote"/>
                                                <button class="btn btn-success btn-round btn-fab btn-fab-mini"
                                                        id="searchByLote">
                                                    <span class="material-icons">search</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                            <div class="container-fluid p-0">
                                                <div class="row">
                                                    <div class="col-md-12 p-r">
                                                        <div class="form-group d-flex">
                                                            <input type="text" class="form-control datepicker"
                                                                id="beginDate" value="01/10/2022"/>
                                                            <input type="text" class="form-control datepicker" id="endDate"
                                                                value="31/10/2022"/>
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
                                </div>
                                <br>
                                <div class="material-datatables" id="box-cobranzaHistorial">
                                    <div class="form-group">
                                        <div class="table-responsive">
                                            <table class="table-striped table-hover"
                                                id="cobranzaHistorial" name="cobranzaHistorial">
                                                <thead>
                                                <tr>
                                                     <th title="ID PAGO" class="encabezado">ID PAGO</th>
                                                    <th title="ID LOTE">ID LOTE</th>
                                                    <th title="LOTE">LOTE</th>
                                                    <th title="REFERENCIA LOTE">REFERENCIA LOTE</th>
                                                    <th title="PRECIO LOTE">PRECIO LOTE</th>
                                                    <th title="TOTAL COMISIÓN">TOTAL COMISIÓN</th>
                                            
                                                    <th title="FECHA DE APARTADO">FECHA DE APARTADO</th>
                                                    <th title="ESTATUS CONTRATACIÓN">ESTATUS CONTRATACIÓN</th>
                                                    <th title="ESTATUS COMISIÓN">ESTATUS COMISIÓN</th>
                                                    <th title="ESTATUS VENTA/LOTE">ESTATUS VENTA/LOTE</th>
                                                   
                                                    <th title="DISPERSADO POR MES">PAGO DEL MES</th>
                                                    <th title="DISPERSADO">DISPERSADO</th>
                                                    <th title="PAGO HISTÓRICO">PAGO HISTÓRICO </th>
                                                    
                                                    <th title="PENDIENTE">PENDIENTE</th>
                                                    <th title="USUARIO">USUARIO</th>
                                                    <th title="PUESTO">PUESTO</th>
                                                    <th title="PLAZA">PLAZA</th>
                                                    <th title="LUGAR DE PROSPECCION">LUGAR DE PROSPECCION</th>
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
    <script src="<?= base_url() ?>dist/js/controllers/cobranza/cobranza.js"></script> 

</body>