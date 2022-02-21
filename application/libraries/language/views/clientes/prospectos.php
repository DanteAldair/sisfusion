<div>
<div class="wrapper">
    <?php
        switch ($this->session->userdata('id_rol')) {
            case "1": // DIRECTOR
            case "2": // SUBDIRECTOR
            case "3": // GERENTE
            case "4": // ASISTENTE DIRECTOR
            case "5": // ASISTENTE SUBDIRECTOR
            case "9": // COORDINADOR
        
            case "19": // SUBDIRECTOR MKTD
            case "20": // GERENTE MKTD
                $dato= array(
                    'home' => 0,
                    'usuarios' => 0,
                    'statistics' => 0,
                    'manual' => 0,
                    'aparta' => 0,
                    'prospectos' => 0,
                    'prospectosAlta' => 1,
                    'sharedSales' => 0,
                    'coOwners' => 0,
                    'prospectosMktd' => 0,
                    'bulkload' => 0,
                    'listaAsesores' => 0,
                    'altaUsuarios' => 0,
                    'listaUsuarios' => 0
                );
                $this->load->view('template/sidebar', $dato);
            break;


             case "18": // DIRECTOR MKTD
          
                $dato= array(
                'home' => 0,
                'usuarios' => 0,
                'statistics' => 0,
                'manual' => 0,
                'aparta' => 0,
                'prospectos' => 1,
                'prospectosMktd' => 0,
                'prospectosAlta' => 0,
                'sharedSales' => 0,
                'coOwners' => 0,
                'references' => 0,
                'plazasComisiones'     => 0,
                'nuevasComisiones' => 0,
                'histComisiones' => 0,
                'bulkload' => 0,
                'listaAsesores' => 0,
                    'altaUsuarios' => 0,
                    'listaUsuarios' => 0
            );


 

            //$this->load->view('template/ventas_pr/sidebar', $dato);
            $this->load->view('template/sidebar', $dato);
            break;
            case "7": // ASESOR
               $dato= array(
                    'home' => 0,
                    'listaCliente' => 0,
                    'corridaF' => 0,
                    'inventario' => 0,
                    'prospectos' => 0,
                    'prospectosAlta' => 1,
                    'statistic' => 0,
                    'comisiones' => 0,
                    'DS'    => 0,
                    'DSConsult' => 0,
                    'documentacion' => 0,
                    'inventarioDisponible'  =>  0,
                    'manual'    =>  0,
                    'nuevasComisiones'     => 0,
                    'histComisiones'       => 0,
                    'sharedSales' => 0,
                    'coOwners' => 0,
                    'references' => 0,
				   'autoriza' => 0,
                   'altaUsuarios' => 0,
                   'listaUsuarios' => 0,
				   'clientsList' => 0
                );
                //$this->load->view('template/asesor/sidebar', $dato);
				$this->load->view('template/sidebar', $dato);
            break;
            case "6": // ASISTENTE GERENCIA
                $dato= array(
                    'home' => 0,
                    'listaCliente' => 0,
                    'corridaF' => 0,
                    'documentacion' => 0,
                    'autorizacion' => 0,
                    'contrato' => 0,
                    'inventario' => 0,
                    'estatus8' => 0,
                    'estatus14' => 0,
                    'estatus7' => 0,
                    'reportes' => 0,
                    'estatus9' => 0,
                    'disponibles' => 0,
                    'asesores' => 0,
                    'nuevasComisiones' => 0,
                    'histComisiones' => 0,
                    'prospectos' => 0,
                    'prospectosAlta' => 1,
                    'sharedSales' => 0,
                    'coOwners' => 0,
                    'prospectosMktd' => 0,
                    'bulkload' => 0,
                    'listaAsesores' => 0,
                    'altaUsuarios' => 0,
                    'listaUsuarios' => 0
                );
                $this->load->view('template/sidebar', $dato);//template/ventas/sidebar
                break;
            default:
                $dato= array(
                    'prospectos' => 0,
                    'prospectosAlta' => 1,
                    'sharedSales' => 0,
                    'coOwners' => 0,
                    'prospectosMktd' => 0,
                    'bulkload' => 0,
                    'listaAsesores' => 0,
                    'altaUsuarios' => 0,
                    'listaUsuarios' => 0
                );
                $this->load->view('template/sidebar', $dato);
            break;
        }
    ?>

    <div class="content">
        <div class="container-fluid">


            <div class="col col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!--      Wizard container        -->
                <div class="wizard-container">
                    <div class="card wizard-card" data-color="green" id="wizardProfile">
                        <form id="my-form" name="my-form" method="post">
                            <!--        You can switch " data-color="purple" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
                            <div class="wizard-header">
                                <h3 class="wizard-title">
                                    Construye su perfil
                                </h3>
                                <h5>
                                    Esta información nos permitirá saber más sobre él.</h5>
                            </div>
                            <div class="wizard-navigation">
                                <ul>
                                    <li>
                                        <a href="#about" data-toggle="tab">Acerca de</a>
                                    </li>
                                    <li>
                                        <a href="#job" data-toggle="tab">Empleo</a>
                                    </li>
                                    <li>
                                        <a href="#prospecting" data-toggle="tab">Prospección</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane" id="about">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4 class="info-text"> Comencemos con la información básica </h4>
                                        </div>
                                        <div class="col-sm-2 col-sm-offset-1">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Nacionalidad<small> (requerido)</small></label>
                                                <select id="nationality" name="nationality" class="form-control nationality"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Personalidad jurídica<small> (requerido)</small></label>
                                                <select id="legal_personality" name="legal_personality" class="form-control legal_personality" onchange="validatePersonality()"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group label-floating">
                                                <label class="control-label">CURP</label>
                                                <input id="curp" name="curp" type="text" class="form-control" minlength="18" maxlength="18" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">RFC</label>
                                                <input id="rfc" name="rfc" type="text" class="form-control" minlength="12" maxlength="13">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-sm-offset-1">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Nombre / Razón social<small> (requerido)</small></label>
                                                <input id="name" name="name" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Apellido paterno</label>
                                                <input id="last_name" name="last_name" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Apellido materno</label>
                                                <input id="mothers_last_name" name="mothers_last_name" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Fecha de nacimiento</label>
                                                <input id="date_birth" name="date_birth" type="date" class="form-control" onchange="getAge(1)" />
<!--                                                <input id="date_birth" name="date_birth" type="text" class="form-control datepicker">-->
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-sm-offset-1">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Edad</label>
                                                <input id="company_antiquity" name="company_antiquity" type="text" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Correo electrónico<small> (requerido)</small></label>
                                                <input id="email" name="email" type="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Teléfono celular<small> (requerido)</small></label>
                                                <input id="phone_number" name="phone_number" type="number" class="form-control" maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Teléfono casa</label>
                                                <input id="phone_number2" name="phone_number2" type="number" class="form-control" maxlength="10" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-sm-offset-1">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Estado civil</label>
                                                <select id="civil_status" name="civil_status" class="form-control civil_status"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Régimen matrimonial</label>
                                                <select id="matrimonial_regime" name="matrimonial_regime" class="form-control matrimonial_regime" onchange="validateMatrimonialRegime(1)"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Cónyugue</label>
                                                <input id="spouce" name="spouce" type="text" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-3 col-sm-offset-1">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Originiario de</label>
                                                <input id="from" name="from" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Domicilio particular</label>
                                                <input id="home_address" name="home_address" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col-lg-10 col-lg-offset-1">La casa donde vive es</div>
                                        <div class="col-lg-10 col-lg-offset-2">
                                            <div class="col-sm-2">
                                                <div class="choice" data-toggle="wizard-radio">
                                                    <input id="own" name="lives_at_home" type="radio" value="1">
                                                    <div class="icon"><i class="fa fa-home"></i></div>
                                                    <h6>Propia</h6>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="choice" data-toggle="wizard-radio">
                                                    <input id="rented" name="lives_at_home" type="radio" value="2">
                                                    <div class="icon"><i class="fa fa-file"></i></div>
                                                    <h6>Rentada</h6>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="choice" data-toggle="wizard-radio">
                                                    <input id="paying" name="lives_at_home" type="radio" value="3">
                                                    <div class="icon"><i class="fa fa-money"></i></div>
                                                    <h6>Pagándose</h6>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="choice" data-toggle="wizard-radio">
                                                    <input id="family" name="lives_at_home" type="radio" value="4">
                                                    <div class="icon"><i class="fa fa-group"></i></div>
                                                    <h6>Familiar</h6>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="choice" data-toggle="wizard-radio">
                                                    <input id="other" name="lives_at_home" type="radio" value="5">
                                                    <input id="hidden" name="lives_at_home" type="hidden">
                                                    <div class="icon"><i class="fa fa-circle"></i></div>
                                                    <h6>Otro</h6>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane" id="job">
                                    <h4 class="info-text"> ¿Cuál es su empleo? </h4>
                                    <div class="row">
                                        <div class="col-sm-4 col-sm-offset-1">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Ocupación</label>
                                                <input id="occupation" name="occupation" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Empresa</label>
                                                <input id="company" name="company" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-sm-offset-1">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Puesto</label>
                                                <input id="position" name="position" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Antigüedad (años)</label>
                                                <input id="antiquity" name="antiquity" type="text" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Domicilio</label>
                                                <input id="residence" name="company_residence" type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="prospecting">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h4 class="info-text"> Es importante saber cómo nos conoció </h4>
                                        </div>
                                        <div class="col-sm-3 col-sm-offset-1">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">¿Cómo se enteró de nosotros?<small> (requerido)</small></label>
                                                <select id="advertising" name="advertising" class="form-control advertising"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">¿Cómo nos contactaste?<small> (requerido)</small></label>
                                                <select id="prospecting_place" name="prospecting_place" class="form-control prospecting_place" onchange="validateProspectingPlace()"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Específique cuál</label>
                                                <input id="specify" name="specify" type="text" class="form-control" readonly>
                                                <select id="specify_mkt" name="specify" class="form-control" style="display: none">
                                                    <option value="default" id="sm" disabled selected>Seleccione una opción</option>
                                                    <option value="01 800">01 800</option>
                                                    <option value="Chat">Chat</option>
                                                    <option value="Contacto web">Contacto web</option>
                                                    <option value="Facebook">Facebook</option>
                                                    <option value="WhatsApp">WhatsApp</option>
                                                    <option value="Recomendado">Recomendado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group label-floating select-is-empty">
                                                <label class="control-label">Plaza de venta<small> (requerido)</small></label>
                                                <select id="sales_plaza" name="sales_plaza" class="form-control sales_plaza"></select>
                                            </div>
                                        </div>

                                        <div class="col-sm-10 col-sm-offset-1">
                                            <div class="form-group label-floating">
                                                <label class="control-label">Observaciones</label>
                                                <textarea type="password" id="observations" name="observations" class="form-control"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="wizard-footer">
                                <div class="pull-right">
                                    <input type='button' class='btn btn-next btn-fill btn-wd' name='next' value='Siguiente' style="background-color: #4caf50" />
                                    <input type='submit' class='btn btn-finish btn-fill btn-green btn-wd' name='finish' value='Finalizar' style="background-color: #4caf50"/>
<!--                                    <button type="submit" class="btn btn-green" style="background-color: #4caf50;">Finalizar</button>-->
                                </div>
                                <div class="pull-left">
                                    <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='Anterior' />
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- wizard container -->
            </div>

        </div>
    </div>

<?php $this->load->view('template/footer_legend');?>



</div>
</div>
</body>
<?php $this->load->view('template/footer');?>

<script src="<?=base_url()?>dist/js/controllers/general-1.1.0.js"></script>

<script>
    document.write(new Date().getFullYear())
</script>

<script type="text/javascript">
    $(document).ready(function() {
        demo.initMaterialWizard();
        md.initSliders()
        demo.initFormExtendedDatetimepickers();
    });
</script>
</html>
