<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<body>
<div class="wrapper">
    <?php
        $datos = array();
        $datos = $datos4;
        $datos = $datos2;
        $datos = $datos3;
        $this->load->view('template/sidebar', $datos);
    ?>
    <style>
        .textoshead::placeholder { color: white; }    
    </style>
    <div class="modal fade" id="modal_pregunta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         data-backdrop="static" data-keyboard="false" style="z-index: 1600;top: 30%;" >
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" style="box-shadow: 0 27px 24px 0 rgb(0 0 0 / 57%), 0 40px 77px 0 rgb(0 0 0 / 90%);
            border-radius: 6px;border: 1px solid #ccc;">
                <div class="modal-header">
                    <h4 class="modal-title">¿Realmente desea asignar este prospecto al cliente?</h4>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-simple" data-dismiss="modal">CANCELAR
                            <div class="ripple-container"></div></button>
                        <button type="button" class="btn btn-primary" id="asignar_prospecto" data-dismiss="modal">ASIGNAR
                            <div class="ripple-container"></div></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_aut_ds" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>Solicitar</b> autorización.</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="my-edit-form" name="my-edit-form" method="post">
                    <div class="modal-body">
                    </div>

                    <div class="modal-footer"></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="asignar_prospecto_a_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>Asignar</b> prospecto al cliente
                        <b><span id="nom_cliente" style="text-transform: uppercase"></span></b>.</h4>
                        <a type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 2%;right: 5%;"><span class="material-icons">close</span></a>
                    <h5 class=""></h5>
                    <input type="hidden" id="id_cliente_asignar" name="id_cliente_asignar">
                    <div class="modal-body">
                        <div class="material-datatables">
                            <table class="table table-responsive table-bordered table-striped table-hover" id="table_prospectos" width="100%">
                                <thead>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Información prospecto</th>
                                <th>Asignar</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_loader_assign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Asignando prospecto al cliente</h4>
                    <div class="modal-body" style="text-align: center">
                            <img src="<?=base_url()?>static/images/asignando.gif" width="100%">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cerrar
                            <div class="ripple-container"></div></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal  ENVIA A CONTRALORIA 2-->
    <div class="modal fade" id="modal1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header">
                    <center><h4 class="modal-title"><label>Integración de Expediente - <b><span class="lote"></span></b></label></h4></center>
                </div>
                <div class="modal-body">
                    <label>Comentario:</label>
                    <textarea class="form-control" id="comentario" rows="3"></textarea>
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="save1" class="btn btn-primary">ACEPTAR</button>

                </div>
            </div>
        </div>
    </div>
    <!-- modal -->

    <!-- modal  ENVIA A postventa 3 despúes de un rechazo-->
    <div class="modal fade" id="enviarNuevamenteEstatus3PV" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content" >
                <div class="modal-header">
                    <center><h4 class="modal-title"><label>Enviar nuevamente a postventa (despúes de un rechazo de postventa) - <b><span class="lote"></span></b></label></h4></center>
                </div>
                <div class="modal-body">
                    <label>Comentario:</label>
                    <textarea class="form-control" id="comentarioST3PV2" rows="3"></textarea>
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="guardar_re3pv" class="btn btn-primary"> Registrar</button>

                </div>
            </div>
        </div>
    </div>
    <!-- modal -->

    <div class="content boxContent">
        <div class="container-fluid">
            <div class="row">
                <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header card-header-icon" data-background-color="goldMaderas">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div class="card-content">
                            <div class="encabezadoBox">
                                <h3 class="card-title center-align">Tus ventas</h3>
                                <p class="card-title pl-1"></p>
                            </div>
                            <?php
                            if($this->session->userdata('id_usuario') == 9651) {
                            ?>
                                <div class="toolbar">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="col-md-4 form-group">
                                                <div class="form-group label-floating select-is-empty">
                                                    <label class="control-label">Proyecto</label>
                                                    <select name="proyecto" id="proyecto" class="selectpicker select-gral m-0"
                                                            data-style="btn" data-show-subtext="true"  title="Selecciona un proyecto"
                                                            data-size="7" data-live-search="true" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <div class="form-group label-floating select-is-empty">
                                                    <label class="control-label">Condominio</label>
                                                    <select name="condominio" id="condominio" class="selectpicker select-gral m-0"
                                                            data-style="btn" data-show-subtext="true"  title="Selecciona condominio"
                                                            data-size="7" data-live-search="true" required>
                                                        <option disabled selected>Selecciona un condominio</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="material-datatables">
                                <table id="tabla_deposito_seriedad" name="tabla_deposito_seriedad" class="table-striped table-hover" style="text-align:center;">
                                    <thead>
                                        <tr>
                                            <th>PROYECTO</th>
                                            <th>CONDOMINIO</th>
                                            <th>LOTE</th>
                                            <th>CLIENTE</th>
                                            <th>COORDINADOR</th>
                                            <th>GERENTE</th>
                                            <th>SUBDIRECTOR</th>
                                            <th>DIRECTOR REGIONAL</th>
                                            <th>DIRECTOR REGIONAL 2</th>
                                            <th>FECHA DE APARTADO</th>
                                            <th>FECHA DE VENCIMIENTO</th>
                                            <th>COMENTARIO</th>
                                            <th>PROSPECTO</th>
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

    <div class="content hide">
        <!-- modal  ENVIA A CONTRALORIA 5 por rechazo 1-->
        <div class="modal fade" id="modal2" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 5 Contraloría) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario2" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save2" class="btn btn-success"><span class="material-icons" >send</span> </i> Registrar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A CONTRALORIA 5 por rechazo 1-->
        <div class="modal fade" id="modal3" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 5 Contraloría) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario3" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save3" class="btn btn-success"><span class="material-icons" >send</span> </i> Registrar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A CONTRALORIA 6 por rechazo 1-->
        <div class="modal fade" id="modal4" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 6 Contraloría) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario4" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="save4" class="btn btn-primary"> Registrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A VENTAS 8 por rechazo 1-->
        <div class="modal fade" id="modal5" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 8 Ventas) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario5" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="save5" class="btn btn-primary"> Registrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A JURIDICO por rechazo 1-->
        <div class="modal fade" id="modal6" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 7 Jurídico) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario6" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="save6" class="btn btn-primary">Registrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A JURIDICO por rechazo 1-->
        <div class="modal fade" id="modal7" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 5 Contraloría) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario7" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save7" class="btn btn-success"><span class="material-icons" >send</span> </i> Registrar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

        <!-- modal  ENVIA A JURIDICO por rechazo 2-->
        <div class="modal fade" id="modal_return1" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <center><h4 class="modal-title"><label>Integración de Expediente (Rechazo estatus 7 Jurídico) - <b><span class="lote"></span></b></label></h4></center>
                    </div>
                    <div class="modal-body">
                        <label>Comentario:</label>
                        <textarea class="form-control" id="comentario8" rows="3"></textarea>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="b_return1" class="btn btn-success"><span class="material-icons" >send</span> </i> Registrar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal -->

    </div>
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
<link rel="stylesheet" type="text/css" href="<?=base_url()?>dist/css/shadowbox.css">
<script type="text/javascript" src="<?=base_url()?>dist/js/shadowbox.js"></script>
<script src="<?=base_url()?>dist/js/controllers/asesores/depositoSeriedad.js"></script>
