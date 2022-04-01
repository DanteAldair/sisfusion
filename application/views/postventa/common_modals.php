<div class="modal   ade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">clear</i>
                </button>
                <h4 class="modal-title card-title">Ingrese su comentario</h4>
            </div>
            <form id="approveForm" name="approveForm" method="post">
                <div class="modal-body">
                    <textarea class="text-modal scroll-styles" type="text" name="observations" id="observations" autofocus="true" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Escriba aqui su comentario"></textarea>
                    <input type="hidden" name="id_solicitud" id="id_solicitud">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">clear</i>
                </button>
                <h4 class="modal-title">Ingresa tus comentarios</h4>
            </div>
            <form id="rejectForm" name="rejectForm" method="post">
                <div class="modal-body">
                    <div class="col-lg-12 form-group">
                        <label>Seleccione el motivo de rechazo.</label>
                        <select class="selectpicker" name="motivos_rechazo" id="motivos_rechazo" data-style="select-with-transition" title="Seleccione una opción" data-size="7"></select>
                        <input type="hidden" name="id_solicitud2" id="id_solicitud2">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Aceptar</button>
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body text-center">
                <h4 class="card-title" id="mainLabelText"></h4>
                <p id="secondaryLabelDetail"></p>
                <!-- <div class="input-group hide" id="selectFileSection">
                    <label class="input-group-btn">
                        <span class="btn btn-primary btn-file">
                            Seleccionar archivo&hellip;
                            <input type="file" name="uploadedDocument" id="uploadedDocument" style="display: none;">
                        </span>
                    </label>
                    <input type="text" class="form-control" id="txtexp" readonly>
                </div> -->

                <div class="file-gph" id="selectFileSection">
                    <input class="d-none" type="file" name="uploadedDocument" id="uploadedDocument">
                    <input class="file-name" id="file-name" type="text" placeholder="No has seleccionada nada aún" readonly="">
                    <label class="upload-btn m-0" for="uploadedDocument">
                        <span>Seleccionar</span>
                        <i class="fas fa-folder-open"></i>
                    </label>
                </div>

                <div class="input-group hide" id="rejectReasonsSection">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 pr-0">
                        <select class="selectpicker" data-style="btn btn-primary btn-round" title="Selecciona un motivo de rechazo" data-size="7" id="rejectionReasons" data-live-search="true" multiple></select>
                    </div>
                </div>
                <input type="text" class="hide" id="idSolicitud">
                <input type="text" class="hide" id="idDocumento">
                <input type="text" class="hide" id="documentType">
                <input type="text" class="hide" id="docName">
                <input type="text" class="hide" id="action">
                <input type="text" class="hide" id="details">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="sendRequestButton" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="presupuestoModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog boxContent">
        <div class="modal-content card">
            <form class="card-content" id="formPresupuesto" name="formPresupuesto" method="post">
            <input type="hidden" name="id_solicitud3" id="id_solicitud3">
                <div class="modal-body text-center toolbar m-0">
                    <h3 id="mainLabelText"></h3>
                    <h4 id="encabezado"></h4>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 pr-0 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral">Nombre Completo</label>
                                    <input id="nombrePresupuesto" name="nombrePresupuesto" class="form-control input-gral" type="text" disabled>
                                </div>
                            </div>
                            <div class="col-md-12 pr-0 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Nombre a quien escritura</label>
                                    <input id="nombrePresupuesto2" name="nombrePresupuesto2" class="form-control input-gral" type="text" required>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Estatus de pago</label>
                                    <select class="selectpicker m-0" data-style="btn btn-primary btn-round"
                                            title="Estatus de pago" data-size="7" id="estatusPago" name="estatusPago"
                                            data-live-search="true" required>
                                            <option value ="default" selected disabled>Selecciona una opción</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Superficie</label>
                                    <input id="superficie" name="superficie" class="form-control input-gral" value="" type="number" required>
                                </div>
                            </div>    
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Fecha de contrato</label>
                                    <input type="text" class="form-control datepicker input-gral"
                                    id="fContrato" name="fContrato" disabled/>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Clave catastral</label>
                                    <input id="catastral" name="catastral" value="" class="form-control input-gral" type="number" required>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">Estatus construcción</label>
                                    <select class="selectpicker m-0" data-style="btn btn-primary btn-round"
                                            title="Estatus construcción" data-size="7" id="construccion" name="construccion"
                                            data-live-search="true" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group text-left m-0">
                                    <label class="control-label label-gral m-0">¿Tenemos cliente anterior?</label>
                                    <select class="selectpicker m-0" data-style="btn btn-primary btn-round"
                                            title="¿Tenemos cliente anterior?" data-size="7" id="cliente" name="cliente"
                                            data-live-search="true" required>
                                            <option value ="default" selected disabled>Selecciona una opción</option>
                                            <option value="uno">Si</option>
                                            <option value="dos">No</option>
                                    </select>
                                </div>
                            </div>
                            <!-- estos input solo se muestran si es si el select anterior -->
                            <div id="ifClient" style="display:none">
                                <div class="col-md-12 pr-0 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">Nombre del titular anterior</label>
                                        <input id="nombreT" name="nombreT" class="form-control input-gral" type="text" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">Fecha del contrato anterior</label>
                                        <input type="text" class="form-control datepicker"
                                        id="fechaCA" name="fechaCA" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">RFC / Datos personales</label>
                                        <input id="rfcDatos" name="rfcDatos" value="N/A" class="form-control input-gral" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-end p-0">
                                <button type="button" class="btn btn-danger btn-simple mt-2" data-dismiss="modal">Cancelar</button>
                                <button type="submit" id="RequestPresupuesto" class="btn btn-primary mt-2">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="checkPresupuestoModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog boxContent">
        <div class="modal-content card">
            <form class="card-content" id="formPresupuesto" name="formPresupuesto" method="post">
            <input type="hidden" name="id_solicitud3" id="id_solicitud3">
                <div class="modal-body text-center toolbar m-0 p-0">
                    <h5 id="mainLabelText"></h5>
                    <h4 id="secondaryLabelDetail">Desarrollo / Condominio / Lote</h4>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 pr-0 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Nombre Completo</label>
                                    <input id="nombrePresupuesto3" name="nombrePresupuesto3" class="form-control input-gral" type="text" disabled>
                                </div>
                            </div>
                            <div class="col-md-12 pr-0 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Nombre a quien escritura</label>
                                    <input id="nombrePresupuesto4" name="nombrePresupuesto4" class="form-control input-gral" type="text" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Estatus de pago</label>
                                    <input id="estatusPago2" name="estatusPago2" class="form-control input-gral" type="text" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Superficie</label>
                                    <input id="superficie2" name="superficie2" class="form-control input-gral" value="" type="number" disabled>
                                </div>
                            </div>    
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Fecha de contrato</label>
                                    <input type="text" class="form-control datepicker input-gral"
                                    id="fContrato2" name="fContrato2" disabled/>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Clave catastral</label>
                                    <input id="catastral2" name="catastral2" value="" class="form-control input-gral" type="number" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">Estatus construcción</label>
                                    <input id="construccion2" name="construccion2" class="form-control input-gral" type="text" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 pr-0">
                                <div class="form-group label-floating is-focused">
                                    <label class="control-label label-gral">¿Tenemos cliente anterior?</label>
                                    <select class="selectpicker" data-style="btn btn-primary btn-round"
                                            title="¿Tenemos cliente anterior?" data-size="7" id="cliente2" name="cliente2"
                                            data-live-search="true" disabled>
                                            <option value ="default" selected disabled>Selecciona una opción</option>
                                            <option value="uno">Si</option>
                                            <option value="dos">No</option>
                                    </select>
                                </div>
                            </div>
                            <!-- estos input solo se muestran si es si el select anterior -->
                            <div id="ifClient2" style="display:none">
                                <div class="col-md-12 pr-0 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">Nombre del titular anterior</label>
                                        <input id="nombreT2" name="nombreT2" class="form-control input-gral" type="text" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">Fecha del contrato anterior</label>
                                        <input type="text" class="form-control datepicker"
                                        id="fechaCA2" name="fechaCA2" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 pr-0">
                                    <div class="form-group label-floating is-focused">
                                        <label class="control-label label-gral">RFC / Datos personales</label>
                                        <input id="rfcDatos2" name="rfcDatos2" value="N/A" class="form-control input-gral" type="text" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-end p-0">
                                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="documentTree" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body text-center">
                <h5 id="mainLabelText"></h5>
                <p style="font-size: 0.8em"></p>
                
                <div class="input-group" >
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 pr-0">
                        <select class="selectpicker" data-style="btn btn-primary btn-round"
                            title="Selecciona un documento a subir" data-size="7" id="documents"
                            data-live-search="true"></select>
                    </div>
                </div>
                <div class="input-group hide" id="documentsSection">
                    <label class="input-group-btn">
                        <span class="btn btn-primary btn-file">
                            Seleccionar archivo&hellip;<input type="file" name="uploadedDocument2" id="uploadedDocument2" style="display: none;">
                        </span>
                    </label>
                    <input type="text" class="form-control" id="txtexp" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="sendRequestButton2" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="notarias" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <div class="modal-header">
            <h5 class="text-center m-0">Selecciona Notaria/Valuador</h5>
            </div>
            <div class="modal-body ">
                
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6" >
                            <select class="selectpicker" data-style="btn btn-primary btn-round"
                                title="Selecciona una notaria" data-size="7" id="notaria"
                                data-live-search="true"></select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6" >
                            <select class="selectpicker" data-style="btn btn-primary btn-round"
                                title="Selecciona un valuador" data-size="7" id="valuador"
                                data-live-search="true"></select>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6"><div id="information"></div></div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6"><div id="information2"></div></div>
                </div>
                <input type="text" class="hide" id="idSolicitud">
                <input type="text" class="hide" id="action">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="notariaSubmit" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dateModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog boxContent">
        <div class="modal-content card">
            <div class="modal-header"></div>
            <div class="modal-body text-center card-content">
                <h5 id="mainLabelText">Fecha para firma de escrituras</h5>
                <div class="toolbar" >
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 pr-0">
                                <input type="text" class="form-control datepicker2 input-gral" id="signDate" value="" />
                                <p>*(fecha sugerida para firma de escrituras)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="text" class="hide" id="idSolicitud">
                <input type="text" class="hide" id="type">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                <button type="button" id="dateSubmit" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="altaNotario" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 id="mainLabelText">Nueva Notaria</h5></div>
            <div class="modal-body text-center">
               <form id="newNotario" name="newNotario" method="post">
                    <input type="text" class="hide" id="idSolicitud" name="idSolicitud">
                    <div class="modal-body">
                        <div class="col-md-4">
                            <div class="form-group label-floating is-focused">
                                <label class="control-label label-gral">Nombre de la notaria</label>
                                <input type="text" id="nombre_notaria" name="nombre_notaria" class="form-control input-gral">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group label-floating is-focused">
                                <label class="control-label label-gral">Nombre del notario</label>
                                <input type="text" id="nombre_notario" name="nombre_notario" class="form-control input-gral">
                            </div>
                        </div>    
                        <div class="col-md-4">
                            <div class="form-group label-floating is-focused">
                                <label class="control-label label-gral">Direccion</label>
                                <input type="text" id="direccion" name="direccion" class="form-control input-gral">
                            </div>
                        </div>    
                        <div class="col-md-4">
                            <div class="form-group label-floating is-focused">
                                <label class="control-label label-gral">Correo</label>
                                <input type="text" id="correo" name="correo" class="form-control input-gral">
                            </div>
                        </div>    
                        <div class="col-md-4">
                            <div class="form-group label-floating is-focused">
                                <label class="control-label label-gral">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form-control input-gral">
                            </div>
                        </div>    
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gestionNotaria" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 id="mainLabelText" aling="center"><b>Información Notaria</b></h5></div>
            <div class="modal-body text-center">
               <form method="post" id="rechazar" name="rechazar">
                    <input type="text" class="hide" id="idSolicitud" name="idSolicitud">
                    <div class="modal-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label label-gral">Nombre de la notaria</label>
                                <input type="text" id="nombreNotaria" name="nombreNotaria" class="form-control input-gral" value="" style="text-align:center" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label label-gral">Nombre del notario</label>
                                <input type="text" id="nombreNotario" name="nombre_notario" class="form-control input-gral" value="" style="text-align:center" disabled>
                            </div>
                        </div>    
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label label-gral">Direccion</label>
                                <input type="text" id="direccionN" name="direccion" class="form-control input-gral" value="" style="text-align:center" disabled>
                            </div>
                        </div> 
                        <div class="col-md-2"></div>  
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label label-gral">Correo</label>
                                <input type="text" id="correoN" name="correo" class="form-control input-gral" value="" style="text-align:center" disabled>
                            </div>
                        </div>    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label label-gral">Teléfono</label>
                                <input type="text" id="telefonoN" name="telefono" class="form-control input-gral" value="" style="text-align:center" disabled>
                            </div>
                        </div>    
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-danger">Rechazar</button>
                                <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>    
                        <div class="col-md-4"></div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewObservaciones" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-center m-0">Envío de Observaciones</h5>
            </div>
            <form method="post" name="observacionesForm" id="observacionesForm">
                <input type="text" class="hide" id="idSolicitud" name="idSolicitud">
                <input type="text" class="hide" id="action" name="action">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <select id="pertenece" class="selectpicker" data-style="btn btn-primary btn-round" title="¿Para quién es la observación?" data-size="7" data-live-search="true">
                                <option value="Postventa">Postventa</option>
                                <option value="Proyectos">Proyectos</option>
                            </select>
                        </div>      
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <select id="observaciones" class="selectpicker" data-style="btn btn-primary btn-round" title="Observaciones" data-size="7" data-live-search="true">
                                <option value="Correccioón Documentos">Corrección Documentos</option>
                                <option value="Documentación Correcta">Documentación Correcta</option>  
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="postventa" style="display:none">
                        <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <div id="proyectos" style="display:none">
                    <input type="text" class="hide" id="idSolicitud" name="idSolicitud">
                    <input type="text" class="hide" id="action" name="action">
                    <button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="observacionesSubmit" class="btn btn-primary">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>              