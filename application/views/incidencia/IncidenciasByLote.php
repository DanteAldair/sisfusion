<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<body>
    <div class="wrapper">
        <?php
        if($this->session->userdata('id_rol') == 13 || $this->session->userdata('id_rol') == 8 || $this->session->userdata('id_rol') == 32 || $this->session->userdata('id_rol') == 17){
            switch ($this->session->userdata('id_usuario')) {
                case 1: // corporativa
                case 2815: // admin
                    $datos = array();
                    $datos = $datos4;
                    $datos = $datos2;
                    $datos = $datos3;
                    $this->load->view('template/sidebar', $datos);
                    break;
                default:
                    if ($this->session->userdata('id_rol') == 17) {
                        $datos = array();
                        $datos = $datos4;
                        $datos = $datos2;
                        $datos = $datos3;
                        $this->load->view('template/sidebar', $datos);
                    } else {
                        echo '<script>alert("ACCESSO DENEGADO"); window.location.href="' . base_url() . '";</script>';
                    }
                    break; 
            }
        } else{
            echo '<script>alert("ACCESSO DENEGADO"); window.location.href="' . base_url() . '";</script>';
        }
        ?>

       
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
                        <div class="modal-footer">  
            				</div>
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

        <div class="modal fade modal-alertas"  id="addEmpresa" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="material-icons">clear</i>
                        </button>   
                    </div>
                    <form method="post" id="form_empresa">
                        <div class="modal-body">
                            <input type="hidden" name="idLoteE" readonly="true" id="idLoteE" >
                            <input type="hidden" name="idClienteE" readonly="true" id="idClienteE" >
                            <input type="hidden" name="PrecioLoteE" readonly="true" id="PrecioLoteE" >
                            <h4>¿Esta seguro que desea agregar empresa?</h4>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" disabled id="btn-save" class="btn btn-gral-data" value="GUARDAR">GUARDAR</button>
                            <button type="button" class="btn btn-danger btn-simple"  data-dismiss="modal" value="CANCELAR"> CANCELAR</button>
                        <!-- <button type="submit" id="btn_add" class="btn btn-primary">GUARDAR</button>
                            <button class="btn btn-danger" type="button" data-dismiss="modal" >CANCELAR</button> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="seeInformationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Consulta de historial <b id="nomLoteHistorial"></b></h4>
                    </div>
                    <div class="modal-body">
                        <div role="tabpanel">
                            <ul class="nav nav-tabs" role="tablist" style="background: #003d82;">
                                <li role="presentation" class="active">
                                    <a href="#changeprocesTab" aria-controls="changeprocesTab" role="tab" onclick="javascript:$('#verDet').DataTable().ajax.reload();" data-toggle="tab">Proceso de contratación</a>
                                </li>
                                <li role="presentation">
                                    <a href="#changelogTab" aria-controls="changelogTab" role="tab" data-toggle="tab" onclick="javascript:$('#verDetBloqueo').DataTable().ajax.reload();">Liberación</a>
                                </li>
                                <li role="presentation">
                                    <a href="#coSellingAdvisers" aria-controls="coSellingAdvisers" role="tab" data-toggle="tab" onclick="javascript:$('#seeCoSellingAdvisers').DataTable().ajax.reload();">Asesores venta compartida</a>
                                </li>
                                <?php 
                                $id_rol = $this->session->userdata('id_rol');
                                if($id_rol == 11){
                                    echo '<li role="presentation"><a href="#tab_asignacion" aria-controls="tab_asignacion" role="tab" data-toggle="tab"
                                        onclick="fill_data_asignacion();">Asignación</a>
                                    </li>';
                                }
                                ?>
                                <li role="presentation" class="hide" id="li_individual_sales">
                                    <a href="#salesOfIndividuals" aria-controls="salesOfIndividuals" role="tab" 
                                    data-toggle="tab">Clausulas</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="changeprocesTab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content">
                                                    <table id="verDet" class="table table-bordered table-hover" width="100%" style="text-align:center;">
                                                        <thead>
                                                            <tr>
                                                                <th><center>Lote</center></th>
                                                                <th><center>Status</center></th>
                                                                <th><center>Detalles</center></th>
                                                                <th><center>Comentario</center></th>
                                                                <th><center>Fecha</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th><center>Lote</center></th>
                                                                <th><center>Status</center></th>
                                                                <th><center>Detalles</center></th>
                                                                <th><center>Comentario</center></th>
                                                                <th><center>Fecha</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
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
                                                    <table id="verDetBloqueo" class="table table-bordered table-hover" width="100%" style="text-align:center;">
                                                        <thead>
                                                            <tr>
                                                                <th><center>Lote</center></th>
                                                                <th><center>Precio</center></th>
                                                                <th><center>Fecha Liberación</center></th>
                                                                <th><center>Comentario Liberación</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th><center>Lote</center></th>
                                                                <th><center>Precio</center></th>
                                                                <th><center>Fecha Liberación</center></th>
                                                                <th><center>Comentario Liberación</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="coSellingAdvisers">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content">
                                                    <table id="seeCoSellingAdvisers" class="table table-bordered table-hover" width="100%" style="text-align:center;">
                                                        <thead>
                                                            <tr>
                                                                <th><center>Asesor</center></th>
                                                                <th><center>Coordinador</center></th>
                                                                <th><center>Gerente</center></th>
                                                                <th><center>Fecha alta</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th><center>Asesor</center></th>
                                                                <th><center>Coordinador</center></th>
                                                                <th><center>Gerente</center></th>
                                                                <th><center>Fecha alta</center></th>
                                                                <th><center>Usuario</center></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_asignacion">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content">
                                                    <div class="form-group">
                                                        <label for="des">Desarrollo</label>
                                                        <select name="sel_desarrollo" id="sel_desarrollo" class="selectpicker" 
                                                        data-style="btn btn-second" data-show-subtext="true" 
                                                        data-live-search="true"  title="" data-size="7" required>
                                                            <option disabled selected>Selecciona un desarrollo</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group"></div>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="check_edo">
                                                        <label class="form-check-label" for="check_edo">Intercambio</label>
                                                    </div>
                                                    <div class="form-group text-right">
                                                        <button type="button" id="save_asignacion" class="btn btn-primary">ENVIAR</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="salesOfIndividuals">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-plain">
                                                <div class="card-content">
                                                    <h4 id="clauses_content"></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="prospecto_lbl" id="prospecto_lbl">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal"> CERRAR </button>
                    </div>
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

        <div class="modal fade" id="modal_avisitos" style="overflow-y: scroll;" 
        style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                                           
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="material-icons">clear</i>
                    </button>    
                    <h4 class="card-title"><b>Cambiar usuario</b></h4>
                        <!-- <button type="button" style="font-size: 20px;top:20px;" class="close" data-dismiss="modal">  <i class="large material-icons">close</i></button> -->
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">

                    </div>
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
                        <h4 class="card-title"><b>Ceder comisiones</b></h4>
                    </div>
                    <form method="post" id="form_ceder">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="label">Asesor dado de baja</label>
                                <select name="asesorold" id="asesorold" class="selectpicker" data-style="btn " data-show-subtext="true" data-live-search="true" title="Selecciona un usuario" data-size="7" required>
                                    <option value="0">Seleccione todo</option>
                                </select>
                            </div>
                            <div id="info" ></div>
                            <div class="form-group" id="users"></div>
                            <div class="form-group">
                                <label class="label">Puesto del usuario a ceder la comisiones</label>
                                <select class="selectpicker roles2" name="roles2" id="roles2" required>
                                    <option value="">----Seleccionar-----</option>
                                    <option value="7">Asesor</option>
                                    <option value="9">Coordinador</option>
                                    <option value="3">Gerente</option>
                                </select> 
                            </div>
                            <div class="form-group" id="users">
                                <label class="label">Usuario a ceder comisiones</label>
                                <select id="usuarioid2" name="usuarioid2" class="form-control directorSelect ng-invalid ng-invalid-required" required data-live-search="true"></select>
                            </div>
                            <div class="form-group">
                                <label class="label">Descripción</label>
                                <textarea id="comentario" name="comentario" class="form-control" rows="3" placeholder="Descripción" required="required"></textarea>
                            </div>
                            <div class="form-group">
                             
                            </div>
                        </div>
                        
                        <div class="modal-footer">     
                        
                                    <button type="submit" id="btn_ceder" class="btn btn-gral-data">GUARDAR</button>
                                    <button class="btn btn-danger" type="button" data-dismiss="modal" >CANCELAR</button>
                                
                                <!-- <button type="button"   class="btn btn-danger btn-simple " 
                                        data-dismiss="modal" >Cerrar</button>	

                                <button  type="button"type="submit" id="btn_inv" 
                                 class="btn btn-gral-data updatePrestamo">Aceptar</button> -->
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalInventario" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title"><b>Actualizar inventario</b></h4>
                    </div>
                    <form method="post" id="form_inventario" >
                        <div class="modal-body">
                            <div class="invent"></div>
                            <div class="form-group" id="users"></div>
                            <div class="form-group">
                                <label class="label">Puesto del usuario a modificar</label>
                                <select class="selectpicker roles3" name="roles3" id="roles3" required>
                                    <option value="">----Seleccionar-----</option>
                                    <option value="7">Asesor</option>
                                    <option value="9">Coordinador</option>
                                    <option value="3">Gerente</option>
                                </select>
                                <p id="UserSelect"></p>
                            </div>
                            <div class="form-group" id="users">
                                <label class="label">Seleccionar usuario</label>
                                <select id="usuarioid3" name="usuarioid3" class="form-control directorSelect ng-invalid ng-invalid-required" required data-live-search="true"></select>
                            </div>
                            <div class="form-group">
                                <label class="label">Descripción</label>
                                <textarea id="comentario3" name="comentario3" class="form-control input-gral" rows="3" placeholder="Descripción" required="required"></textarea>
                            </div>
                            <!-- <div class="form-group">
                                <center>
                                    <button class="btn btn-primary">GUARDAR</button>
                                    <button class="btn btn-danger" type="button" data-dismiss="modal" >CANCELAR</button>
                                </center>
                            </div> -->
                        </div>
                        <div class="modal-footer">    
                                <!-- <button  type="button"type="submit" id="btn_inv" 
                                 class="btn btn-gral-data updatePrestamo">Aceptar</button>
                                 <button type="button" class="btn btn-danger btn-simple " 
                                        data-dismiss="modal" >Cerrar</button>	 -->
                                <center>
                                    <button type="submit" id="btn_inv" class="btn btn-gral-data">GUARDAR</button>
                                    <button class="btn btn-danger" type="button" data-dismiss="modal" >CANCELAR</button>
                                </center> 

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalVc" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title"><b>Actualizar venta compartida</b></h4>
                    </div>
                    <form method="post" id="form_vc" >
                        <div class="modal-body">
                            <div class="vc"></div>
                            <div class="form-group" id="users"></div>
                            <div class="form-group">
                                <label class="label">Puesto del usuario a modificar</label>
                                <select class="selectpicker rolesvc" name="rolesvc" id="rolesvc" required>
                                    <option value="">----Seleccionar-----</option>
                                    <option value="7">Asesor</option>
                                    <option value="9">Coordinador</option>
                                    <option value="3">Gerente</option>
                                </select>
                                <p id="UserSelectvc"></p>
                            </div>
                            <div class="form-group" id="users">
                                <label class="label">Seleccionar usuario</label>
                                <select id="usuarioid4" name="usuarioid4" class="form-control directorSelect ng-invalid ng-invalid-required" required data-live-search="true"></select>
                            </div>
                            <div class="form-group">
                                <label class="label">Descripción</label>
                                <textarea id="comentario4" name="comentario4" class="form-control" rows="3" placeholder="Descripción" required="required"></textarea>
                            </div>
                            <div class="form-group">
                              
                            </div>
                        </div>
                        <div class="modal-footer">     
                                <center>
                                    <button class="btn btn-danger btn-simple" type="button" data-dismiss="modal" >CANCELAR</button>
                                    <button type="submit" id="btn_vc" class="btn btn-gral-data">GUARDAR</button>
                                </center>
                           
                       </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade modal-alertas" id="miModalVcNew" style="overflow:auto !important;" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <h4 class="card-title"><b>Agregar venta compartida</b></h4>
                    </div>
                    <form method="post" id="form_vcNew" >
                        <div class="modal-body">
                            <div class="vcnew"></div>
                            <div class="form-group" id="users5">
                                <label class="label">Asesor</label>
                                <select id="usuarioid5" name="usuarioid5" class="form-control asesor ng-invalid ng-invalid-required" required data-live-search="true" required></select>
                            </div>
                            <div class="form-group" id="users6">
                                <label class="label">Coordinador</label>
                                <select id="usuarioid6" name="usuarioid6" class="form-control coor ng-invalid ng-invalid-required"  data-live-search="true" required></select>
                            </div>
                            <div class="form-group" id="users7">
                                <label class="label">Gerente</label>
                                <select id="usuarioid7" name="usuarioid7" class="form-control ger ng-invalid ng-invalid-required" required data-live-search="true" required></select>
                            </div>
                            <div class="form-group" id="users7">
                                <label class="label">Subdirector</label>
                                <select id="usuarioid8" name="usuarioid8" class="form-control ger ng-invalid ng-invalid-required" required data-live-search="true" required></select>
                            </div>
                        </div>
                        <div class="modal-footer">     
                               
                                <button class="btn btn-danger btn-simple" type="button" data-dismiss="modal" >CANCELAR</button>
                                <button type="submit" id="btn_vcnew" class="btn btn-gral-data">GUARDAR</button>
                        </div>
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
                                <i class="fas fa-wallet fa-2x"></i>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title center-align">Panel de incidencias</h3>
                                <div class="toolbar">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <button class="btn-gral-data" onclick="open_Modal();">Ceder comisiones</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                                <div class="form-group  label-floating is-empty">
                                                    <label class="control-label label-gral">Lote</label>
                                                    <input id="inp_lote" onkeyup="onKeyUp(event)" name="inp_lote" class="form-control input-gral" type="number" maxlength="6">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn-gral-data find_doc">Buscar <i class="fas fa-search pl-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="material-datatables">
                                    <div class="form-group">
                                        <div class="table-responsive">
                                            <table class="table-striped table-hover" id="tabla_inventario_contraloria" name="tabla_inventario_contraloria">
                                                <thead>
                                                    <tr>   
                                                        <th>PROYECTO</th>
                                                        <th>CONDOMINIO</th>
                                                        <th>LOTE</th>
                                                        <th>ID LOTE</th>
                                                        <th>CLIENTE</th>
                                                        <th>TIPO VENTA</th>
                                                        <th>MODALIDAD</th>
                                                        <th>CONTRATACIÓN</th>
                                                        
                                                        <th>PLAN VENTA</th>
                                                        <th>FEC. SISTEMA</th> 
                                                        <th>FEC. NEODATA</th>

                                                        <th>ENT. VENTA</th>
                                                        <th>ESTATUS COM.</th>
                                                        <th>MÁS</th>
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
        <?php $this->load->view('template/footer_legend');?>
    </div>
    </div>
    </div><!--main-panel close-->
    <?php $this->load->view('template/footer');?>
        
    <!--DATATABLE BUTTONS DATA EXPORT-->
    <script src="<?= base_url() ?>dist/js/controllers/incidencias/incidencia_by_lote.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
</body>