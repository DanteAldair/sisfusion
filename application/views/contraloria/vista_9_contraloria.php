<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<body class="">
<div class="wrapper ">
    <?php
        $this->load->view('template/sidebar', '');
    ?>
    <!--Contenido de la página-->
    <div class="content boxContent ">
        <div class="container-fluid">
            <div class="row">
                <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header card-header-icon" data-background-color="goldMaderas">
                            <i class="fas fa-expand fa-2x"></i>
                        </div>
                        <div class="card-content">
                            <div class="encabezadoBox">
                                <h3 class="card-title center-align">Registro estatus 9 </h3>
                                <p class="card-title pl-1">(contrato recibido con firma del cliente)</p>
                            </div>
                            <div  class="toolbar">
                                <div class="row">
                                </div>
                            </div>
                            <div class="material-datatables">
                                <div class="table-responsive">
                                    <table id="tabla_ingresar_9" name="tabla_ingresar_9"
                                            class="table-striped table-hover">
                                        <thead>
                                            
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>PROYECTO</th>
                                                <th>CONDOMINIO</th>
                                                <th>LOTE</th>
                                                <th>GERENTE</th>
                                                <th>CLIENTE</th>
                                                <th>RESIDENCIA</th>
                                                <th>UBICACIÓN</th>
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


<!-- modal  ENVIA A CONTRALORIA 7-->
<div class="modal fade" id="editReg" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <center><h4 class="modal-title"><label>Registro estatus 9 - <b><span class="lote"></span></b></label></h4></center>
            </div>
            <div class="modal-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">
                        <label>Comentario</label>
                        <textarea class="form-control" id="comentario" rows="3"></textarea>
                         <br>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <label id="tvLbl">Total neto</label>
                        <input class="form-control" name="totalNeto2" id="totalNeto2"
                               oncopy="return false" onpaste="return false" onkeypress="return SoloNumeros(event)"
                               type="tel" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency">
                               
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="m-0" for="proyecto">Representante Legal</label>
                            <select name="rl" id="rl"  class="selectpicker select-gral m-0 rl" data-default-value="opciones" data-style="btn" data-show-subtext="true" data-live-search="true" title="SELECCIONA UN RL" data-size="7" required></select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="m-0" for="proyecto">Residencia cliente</label>
                            <select name="residencia" id="residencia"  class="selectpicker select-gral m-0 rl" data-default-value="opciones" data-style="btn" data-show-subtext="true" data-live-search="true" title="SELECCIONA UNA RESIDENCIA" data-size="7" required></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="save1" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->

<!-- modal  rechazar A CONTRALORIA 7-->
<div class="modal fade" id="rechReg" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <center><h4 class="modal-title"><label>Rechazo estatus 9 - <b><span class="lote"></span></b></label></h4></center>
            </div>
            <div class="modal-body">
                <label>Comentario:</label>
                  <textarea class="form-control" id="comentario3" rows="3"></textarea>
                  <br>              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="save3" class="btn btn-primary">Registrar</button>
            </div>
        </div>
    </div>
</div>
<!-- modal -->

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
<script src="<?= base_url() ?>dist/js/controllers/contraloria/vista_9_contraloria.js"></script>
