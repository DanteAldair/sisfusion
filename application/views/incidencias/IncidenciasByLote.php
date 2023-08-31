<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<style>
hr {
    display: block;
    height: 1px;
    border: 0;
    border-top: 1px solid #ccc;
    margin: 1em 0;
    padding: 0;
}
</style>
<body>
    <div class="wrapper">
        <?php $this->load->view('template/sidebar'); ?>
        <!--MODALS-->
        <div class="modal fade modal-alertas" id="modal_sedes" name="modal_sedes" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button> 
                    <h4 class="card-title text-center"><b>Cambio de sedes</b></h4>
                    </div>
                    <form method="post" id="form_sede">
                        <div class="modal-body pb-0">
                            <div class="tituloLote pl-1" id="tituloLote" ></div>
                            <div class="sedeOld pl-1" id="sedeOld" ></div>
                            <div class="form-group mt-0" >
                                <label class="control-label">Selecciona una sede (<span class="isRequired">*</span>)</label>
                                <select id="sedesCambio" name="sedesCambio" class="selectpicker select-gral m-0 sedesNuevo" title="SELECCIONA UNA OPCIÓN"required data-live-search="true">
                                <?php foreach($sedes as $sede){ ?>>
                                    <option value="<?= $sede->id_sede ?>"> <?= $sede->nombre  ?> </option>
                                <?php  }  ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">  
                            <button type="button" class="btn btn-danger btn-simple " data-dismiss="modal">CANCELAR</button>
                            <button type="submit" class="btn btn-primary" value="ACEPTAR">ACEPTAR</button>
            			</div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="modal_pagadas" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>   
                    </div>
                    <form method="post" id="form_pagadas">
                        <div class="modal-body"></div>
                        <div class="modal-footer">  </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas"  id="modal_NEODATA" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" id="form_NEODATA">
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas"  id="modal_inventario" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" id="form_i">
                        <div class="seleccionar"></div>
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_avisos" style="overflow-y: scroll;" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <button type="button" style="font-size: 20px;top:20px;" class="close" data-dismiss="modal">
                            <i class="large material-icons">close</i>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_avisitos" style="overflow-y: scroll;" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">           
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="material-icons">clear</i>
                        </button>      
                    </div>
                    <div class="modal-body"> 
                        <h4 class="modal-title text-center" >Cambiar usuario</h4>
                        <div class="form-group">        
                            <div class="col-md-12" >
                                <select class="selectpicker select-gral m-0" data-style="btn" data-cliente="" data-lote="" title="SELECCIONA UNA OPCIÓN" required data-live-search="true"name="opcion" onchange="selectOpcion()" id="opcion" >
                                    <option value="1">CLIENTE</option>
                                    <option value="2">VENTA COMPARTIDA</option>
                                </select>
                                <input type="hidden" class="form-control"id="lotes1" name="lotes1">
                                <input type="hidden" class="form-control"id="clientes2" name="clientes2">
                            </div> 
                        </div> 
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="myUpdateBanderaModal" data-backdrop="static" data-keyboard="false" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header"></div>
                    <form method="post" id="my_updatebandera_form">
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="modal_add" style="overflow-y: scroll;" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header"></div>
                    <form method="post" id="form_add">
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="modal_quitar" style="overflow-y: scroll;" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header"></div>
                    <form method="post" id="form_quitar">
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalCeder" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title text-center"><b>Ceder comisiones</b></h4>
                    </div>
                    <form method="post" id="form_ceder">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">Asesor dado de baja (<span class="isRequired">*</span>)</label>
                                <select name="asesorold" id="asesorold" class="selectpicker select-gral m-0" title="SELECCIONA UNA OPCIÓN" data-style="btn" data-container="body" data-show-subtext="true" data-live-search="true" data-size="7" required>
                                </select>
                            </div>
                            <div id="info" class="mb-2"></div>
                            <div class="form-group mt-0" id="users"></div>
                            <div class="form-group mt-0">
                                <label class="control-label">Puesto del usuario a ceder la comisiones (<span class="isRequired">*</span>)</label>
                                <select class="selectpicker select-gral roles2 m-0"  name="roles2" id="roles2" title="SELECCIONA UNA OPCIÓN" required data-live-search="true">
                                    <option value="7">ASESOR</option>
                                    <option value="9">COORDINADOR</option>
                                    <option value="3">GERENTE</option>
                                </select> 
                            </div>
                            <div class="form-group mt-0" id="users">
                                <label class="control-label">Usuario a ceder comisiones (<span class="isRequired">*</span>)</label>
                                <select id="usuarioid2" name="usuarioid2" class="selectpicker directorSelect select-gral m-0" data-container="body" title="SELECCIONA UNA OPCIÓN" data-size="7" required data-live-search="true"></select>
                            </div>
                            <div class="form-group mt-0">
                                <label class="control-label">Descripción (<span class="isRequired">*</span>)</label>
                                <textarea id="comentario" name="comentario" class="text-modal" rows="3" placeholder="Descripción" required="required"></textarea>
                            </div>
                            <div class="form-group"></div>
                        </div>
                        <div class="modal-footer">     
                            <button class="btn btn-danger btn-simple" type="button" data-dismiss="modal" >CANCELAR</button>
                            <button type="submit" id="btn_ceder" class="btn btn-primary ">GUARDAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalInventario" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title text-center"><b>Actualizar inventario</b></h4>
                    </div>
                    <form method="post" id="form_inventario" >
                        <div class="modal-body">
                            <div class="invent"></div>
                            <div class="form-group mt-0 pt-0" id="users"></div>
                            <div class="form-group">
                                <label class="control-label">Puesto del usuario a modificar (<span class="isRequired">*</span>)</label>
                                <select class="selectpicker select-gral roles3 m-0"  name="roles3"  id="roles3" required title="SELECCIONA UNA OPCIÓN" required data-live-search="true">
                                    <option value="7">ASESOR</option>
                                    <option value="9">COORDINADOR</option>
                                    <option value="3">GERENTE</option>
                                    <option value="2">SUBDIRECTOR</option>
                                    <option value="59">DIRECTOR REGIONAL</option>
                                </select>
                                <p id="UserSelect"></p>
                            </div>
                            <div class="form-group mt-0 pt-0" id="users">
                                <div class="mt-0">
                                    <label class="control-label">Seleccionar usuario (<span class="isRequired">*</span>)</label>
                                    <select id="usuarioid3" name="usuarioid3"  class="selectpicker select-gral directorSelect m-0" data-size="7" title="SELECCIONA UNA OPCIÓN"  required data-live-search="true"></select>
                                </div>
                            </div>      
                            <p id="UserSelectDirec"></p>                
                            <div class="form-group">
                                <label class="control-label">Descripción</label>
                                <textarea id="comentario3" name="comentario3" class="text-modal" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">    
                            <button class="btn btn-danger btn-simple" type="button" data-dismiss="modal" >CANCELAR</button>
                            <button type="submit" id="btn_inv" class="btn btn-primary">GUARDAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalVc" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title text-center"><b>Actualizar venta compartida</b></h4>
                    </div>
                    <form method="post" id="form_vc" >
                        <div class="modal-body">
                            <div class="vc"></div>
                            <div class="form-group m-0 p-0" id="users"></div>
                            <div class="form-group">
                                <label class="control-label mt-0">Puesto del usuario a modificar (<span class="isRequired">*</span>)</label>
                                    <select class="selectpicker select-gral rolesvc mt-0"  name="rolesvc" id="rolesvc" required title="SELECCIONA UNA OPCIÓN" required data-live-search="true">
                                    <option value="7">ASESOR</option>
                                    <option value="9">COORDINADOR</option>
                                    <option value="3">GERENTE</option>
                                </select>
                                <p id="UserSelectvc"></p>
                            </div>
                            <div class="form-group m-0 p-0" id="users">
                                <label class="control-label mt-0">Seleccionar usuario (<span class="isRequired">*</span>)</label>
                                <select id="usuarioid4" name="usuarioid4"  class="selectpicker select-gral directorSelect mt-0"  data-size="7" title="SELECCIONA UNA OPCIÓN" required data-live-search="true"></select>
                            </div>
                            <div class="form-group">
                                <label class="control-label mt-0">Descripción (<span class="isRequired">*</span>)</label>
                                <textarea id="comentario4" name="comentario4" class="text-modal" rows="3" required="required"></textarea>
                            </div>
                            <div class="form-group m-0 p-0"></div>
                        </div>
                        <div class="modal-footer">     
                            <button class="btn btn-danger btn-simple " type="button" data-dismiss="modal" >CANCELAR</button>
                            <button type="submit" id="btn_vc" class="btn btn-primary" >GUARDAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalVcNew" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title text-center"><b>Agregar venta compartida</b></h4>
                    </div>
                    <form method="post" id="form_vcNew" >
                        <div class="modal-body">
                            <div class="vcnew"></div>
                            <div class="form-group mt-0" id="users5">
                                <label class="control-label">Asesor</label>
                                <select id="usuarioid5" name="usuarioid5" class="selectpicker select-gral  asesor m-0" required data-live-search="true"  data-size="7"  title="SELECCIONA UNA OPCIÓN" ></select>
                            </div>
                            <div class="form-group mt-0" id="users6">
                                <label class="control-label">Coordinador</label>
                                <select id="usuarioid6" name="usuarioid6" class="selectpicker select-gral  coor m-0"data-live-search="true" required   data-size="7" title="SELECCIONA UNA OPCIÓN"></select>
                            </div>
                            <div class="form-group mt-0" id="users7">
                                <label class="control-label">Gerente</label>
                                <select id="usuarioid7" name="usuarioid7" class="selectpicker select-gral  ger m-0" required data-live-search="true"   data-size="7" title="SELECCIONA UNA OPCIÓN" ></select>
                            </div>
                            <div class="form-group mt-0" id="users7">
                                <label class="control-label">Subdirector</label>
                                <select id="usuarioid8" name="usuarioid8" class="selectpicker select-gral ger m-0" required data-live-search="true"   data-size="7" title="SELECCIONA UNA OPCIÓN"></select>
                            </div>
                        </div>
                        <div class="modal-footer">     
                            <button class="btn btn-danger btn-simple" type="button" data-dismiss="modal" >CANCELAR</button>
                            <button type="submit" id="btn_vcnew" class="btn btn-primary">GUARDAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--END MODALS-->

        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header card-header-icon" data-background-color="goldMaderas">
                                <i class="fas fa-chart-pie fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <div class="encabezadoBox">
                                    <h3 class="card-title center-align" >Panel de incidencias</h3>
                                </div>
                                <div class="toolbar">
                                    <div class="container-fluid">
                                        <div class="row aligned-row">
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                                <label class="control-label">Lote (<span class="isRequired">*</span>)</label>
                                                <div class="form-group m-0">
                                                    <input id="inp_lote" onkeyup="onKeyUp(event)" name="inp_lote"  onkeydown="return event.keyCode !== 69" class="form-control input-gral m-0" type="number" min="1"  maxlength="6">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 d-flex align-end">
                                                <div class="form-group w-100">
                                                    <button type="button" class="btn-gral-data buscarLote ">Buscar <i class="fas fa-search pl-1"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"></div>
                                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 d-flex align-end">
                                                <div class="form-group w-100">
                                                    <button class="btn-gral-data" onclick="open_Modal();">Ceder comisiones</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="material-datatables">
                                    <div class="form-group">
                                        <table class="table-striped table-hover hide" id="tabla_incidencias_contraloria" name="tabla_incidencias_contraloria">
                                            <thead>
                                                <tr>
                                                    <th>PROYECTO</th>
                                                    <th>CONDOMINIO</th>
                                                    <th>LOTE</th>
                                                    <th>ID LOTE</th>
                                                    <th>CLIENTE</th>
                                                    <th>TIPO DE VENTA</th>
                                                    <th>MODALIDAD</th>
                                                    <th>CONTRATACIÓN</th>
                                                    <th>PLAN DE VENTA</th>
                                                    <th>FECHA EN SISTEMA</th> 
                                                    <th>FECHA DE NEODATA</th>
                                                    <th>ESTATUS DE LA COMISIÓN</th>
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

        <?php $this->load->view('template/footer_legend');?>
    </div>
    <?php $this->load->view('template/footer');?>
    <script src="<?= base_url() ?>dist/js/controllers/incidencias/incidencia_by_lote.js"></script>
</body>