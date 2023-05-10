<?php

use application\helpers\email\asesor\Elementos_Correos_Asesor;

 if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asesor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_queryinventario');
        $this->load->model('asesor/Asesor_model');
        $this->load->model('Contraloria_model');
        $this->load->model('registrolote_modelo');
        $this->load->model('caja_model_outside');
        $this->load->model('General_model');
        $this->load->library(array('session', 'form_validation'));
        //LIBRERIA PARA LLAMAR OBTENER LAS CONSULTAS DE LAS  DEL MENÚ
        $this->load->library(array('session', 'form_validation', 'get_menu'));
        $this->load->helper(array('url', 'form', 'email/asesor/elementos_correo', 'email/plantilla_dinamica_correo'));
        $this->load->database('default');
        $this->load->library('Pdf');
        $this->load->library('phpmailer_lib');
        date_default_timezone_set('America/Mexico_City');
        $this->validateSession();
    }

    public function index()
    {
        if ($this->session->userdata('id_rol') == FALSE || $this->session->userdata('id_rol') != '7') {
            redirect(base_url() . 'login');
        }
        $this->load->view('template/header');
        $this->load->view('asesor/inicio_asesor_view');
        $this->load->view('template/footer');
    }

    public function homeView()
    {
        if ($this->session->userdata('id_rol') == FALSE || $this->session->userdata('id_rol') != '61')
            redirect(base_url() . 'login');
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $this->load->view('template/header');
        $this->load->view('template/home', $datos);
        $this->load->view('template/footer');
    }


    public function dataPrueba($idCliente, $onlyView)
    {
        $datos["cliente"] = $this->registrolote_modelo->selectDS_ds($idCliente);

        // print_r($datos);
    }

    public function deposito_seriedad_ds($idCliente, $onlyView)
    {
        $this->validateSession();
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $datos["cliente"] = $this->registrolote_modelo->selectDS_ds($idCliente);

        if ($this->session->userdata('id_rol') == 7 || $this->session->userdata('id_rol') == 9 || $this->session->userdata('id_rol') == 3) {
            if ($onlyView == 1) { // CONSULTA
                $this->load->view('contraloria/dpform_c', $datos);
            } else if ($onlyView == 0) { // ESCRITURA
                $this->load->view('template/header');
                $this->load->view('contraloria/dpform_ea', $datos);
            }
        } else {
            $this->load->view('contraloria/dpform_c', $datos);
        }
    }

    public function lista_gerentes()
    {
        echo json_encode($this->Asesor_model->get_gerentes_lista()->result_array());
    }

    public function lista_asesores($gerente)
    {
        echo json_encode($this->Asesor_model->get_asesores_lista($gerente)->result_array());
    }

    public function lista_proyecto()
    {
        echo json_encode($this->Asesor_model->get_proyecto_lista()->result_array());
    }

    public function lista_condominio($proyecto)
    {
        echo json_encode($this->Asesor_model->get_condominio_lista($proyecto)->result_array());
    }

    public function lista_lote($condominio)
    {
        echo json_encode($this->Asesor_model->get_lote_lista($condominio)->result_array());
    }

    public function datos_dinamicos($lote, $asesor)
    {
        echo json_encode($this->Asesor_model->get_datos_dinamicos($lote, $asesor)->result_array());
    }

    public function forma_venta()
    {
        echo json_encode($this->Asesor_model->get_datos_forma()->result_array());
    }

    public function tipo_venta()
    {
        echo json_encode($this->Asesor_model->get_datos_tipo()->result_array());
    }

    public function verificar_solicitud($lote)
    {
        echo json_encode($this->Asesor_model->get_validar_solicitud($lote)->result_array());
    }

    public function getinfoLoteDisponible() {
        $objDatos = json_decode(file_get_contents("php://input"));
        $data = $this->Asesor_model->getLotesInfoCorrida($objDatos->lote);
        $data_casa = ($objDatos->tipo_casa==null) ? null : $objDatos->tipo_casa;
        $cd = json_decode(str_replace("'", '"', $data[0]['casasDetail']));
        $total_construccion = 0; // MJ: AQUÍ VAMOS A GUARDAR EL TOTAL DE LA CONSTRUCCIÓN + LOS EXRTAS
        /*print_r($data[0]['casasDetail']);
        exit;*/

        if($data[0]['casasDetail']!=null){
            if(count($cd->tipo_casa) >= 1){
                foreach($cd->tipo_casa as $value) {
//                    print_r($value);
//                    echo '<br><br>';

                    if($data_casa->id === $value->id){
                        $total_construccion = $value->total_const; // MJ: SE EXTRAE EL TOTAL DE LA CONSTRUCCIÓN POR TIPO DE CASA
                        foreach($value->extras as $v) {
                            $total_construccion += $v->techado;
                        }
                    }


//                     if($value->nombre === 'Aura') {
//                        print_r($value);
//                        $total_construccion = $value->total_const; // MJ: SE EXTRAE EL TOTAL DE LA CONSTRUCCIÓN POR TIPO DE CASA
//                        foreach($value->extras as $v) {
//                            $total_construccion += $v->techado;
//                        }
//                     }else if($value->nombre === 'Stella'){
//                         echo '<br><br>STELLA';
//                     }
                }
            }
        }


        $total_nuevo = $total_construccion + $data[0]['total'];
        #prueba
        $data[0]['precio_lote'] = $data[0]['total'];
        $data[0]['precio_construccion'] = $total_construccion;
        #end prueba
        $data[0]['total'] += $total_construccion;
        $data[0]['enganche'] += $total_construccion*(.10);
        $preciom2 = $total_nuevo/$data[0]['sup'];
        $data[0]['precio'] = $preciom2;
        if($data != null) {
            echo json_encode($data, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array());
        }
    }

    public function getinfoLoteDisponibleE() {
        $objDatos = json_decode(file_get_contents("php://input"));
        $data = $this->Asesor_model->getLotesInfoCorridaE($objDatos->lote);
        $getDataDB = $this->Asesor_model->getInfoCasasByLote($objDatos->lote);
//        print_r(count($getDataDB));
//        exit;
        if(count($getDataDB)>0){
            $casas = str_replace("'tipo_casa':", '', $getDataDB[0]['tipo_casa']);
            $casas = str_replace('"', '', $casas );
            $casas = str_replace("'", '"', $casas );
            $data_casa = ($objDatos->tipo_casa==null) ? null : json_decode($casas);
            $data_casa = $data_casa[0];

            $cd = json_decode(str_replace("'", '"', $data[0]['casasDetail']));
            $total_construccion = 0; // MJ: AQUÍ VAMOS A GUARDAR EL TOTAL DE LA CONSTRUCCIÓN + LOS EXRTAS

            if($data[0]['casasDetail']!=null){
                if(count($cd->tipo_casa) >= 1){
                    foreach($cd->tipo_casa as $value) {
                        if($data_casa->id === $value->id){
                            $total_construccion = $value->total_const;
                            foreach($value->extras as $v) {
                                $total_construccion += $v->techado;
                            }
                        }
                    }
                }
            }
            $total_nuevo = $total_construccion + $data[0]['total'];
            #prueba
            $data[0]['precio_lote'] = $data[0]['total'];
            $data[0]['precio_construccion'] = $total_construccion;
            #end prueba
            $data[0]['total'] += $total_construccion;
            $data[0]['enganche'] += $total_construccion*(.10);
            $preciom2 = $total_nuevo/$data[0]['sup'];
            $data[0]['precio'] = $preciom2;
        }



        if($data != null) {
            echo json_encode($data, JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(array());
        }
    }

    public function inventario()/*this is the function*/
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $datos["registrosLoteContratacion"] = $this->registrolote_modelo->registroLote();
        $datos["residencial"] = $this->Asesor_model->get_proyecto_lista();
        $this->load->view('template/header');
        $this->load->view("contratacion/datos_lote_contratacion_view", $datos);
    }

    //public function cf()
    //{
        //$this->load->view("corrida/cf_view");
    //}

    public function cf2()
    {
        $this->load->view("corrida/cf_view2");
    }

    public function cf3()
    {
        $this->load->view("corrida/cf_view_PAC");
    }


    public function eliminar_propietario()
    {
        $json['resultado'] = FALSE;
        if ($this->input->post("id_copropietario")) {
            $this->load->model("Asesor_model");
            $id_copropietario = $this->input->post("id_copropietario");
            $this->db->query('UPDATE copropietarios SET estatus = 0 WHERE id_copropietario = ' . $id_copropietario . '');
            $json['resultado'] = TRUE;
        }

        echo json_encode($json);
    }


    public function agregar_propietario()
    {

        $json['resultado'] = FALSE;

        if ($this->input->post("nombre_nuevo")) {

            $nuevo0 = $this->input->post("idd");
            $nuevo1 = $this->input->post("nombre_nuevo");
            $nuevo2 = $this->input->post("apellidop_nuevo");
            $nuevo3 = $this->input->post("apellidom_nuevo");
            $nuevo4 = $this->input->post("correo_nuevo");
            $nuevo5 = $this->input->post("telefono1_nuevo");
            $nuevo6 = $this->input->post("telefono2_nuevo");
            $nuevo7 = $this->input->post("fnacimiento_nuevo");

            $nuevo8 = $this->input->post("nacionalidad_nuevo");
            $nuevo9 = $this->input->post("originario_nuevo");
            $nuevo10 = $this->input->post("domicilio_particular_nuevo");

            $nuevo11 = $this->input->post("estadocivil_nuevo");
            $nuevo12 = $this->input->post("conyuge_nuevo");
            $nuevo13 = $this->input->post("regimen_nuevo");

            $nuevo14 = $this->input->post("ocupacion_nuevo");
            $nuevo15 = $this->input->post("puesto_nuevo");
            $nuevo16 = $this->input->post("empresa_nuevo");

            $nuevo17 = $this->input->post("antiguedad_nuevo");
            $nuevo18 = $this->input->post("edad_firma_nuevo");
            $nuevo19 = $this->input->post("domempresa_nuevo");
            $nuevo20 = 1;
            $nuevo21 = $this->session->userdata('id_usuario');

            $arreglo_nuevo = array();
            $arreglo_nuevo["id_cliente"] = $nuevo0;
            $arreglo_nuevo["nombre"] = $nuevo1;
            $arreglo_nuevo["apellido_paterno"] = $nuevo2;
            $arreglo_nuevo["apellido_materno"] = $nuevo3;

            $arreglo_nuevo["correo"] = $nuevo4;
            $arreglo_nuevo["telefono"] = $nuevo5;
            $arreglo_nuevo["telefono_2"] = $nuevo6;
            $arreglo_nuevo["fecha_nacimiento"] = $nuevo7;

            $arreglo_nuevo["nacionalidad"] = $nuevo8;
            $arreglo_nuevo["originario_de"] = $nuevo9;
            $arreglo_nuevo["domicilio_particular"] = $nuevo10;

            $arreglo_nuevo["estado_civil"] = $nuevo11;
            $arreglo_nuevo["regimen_matrimonial"] = $nuevo13;
            $arreglo_nuevo["conyuge"] = $nuevo12;

            $arreglo_nuevo["ocupacion"] = $nuevo14;
            $arreglo_nuevo["posicion"] = $nuevo15;
            $arreglo_nuevo["empresa"] = $nuevo16;

            $arreglo_nuevo["antiguedad"] = $nuevo17;
            $arreglo_nuevo["edadFirma"] = $nuevo18;
            $arreglo_nuevo["direccion"] = $nuevo19;
            $arreglo_nuevo["estatus"] = $nuevo20;
            $arreglo_nuevo["creado_por"] = $nuevo21;

            $this->load->model("Asesor_model");
            $this->db->insert('copropietarios', $arreglo_nuevo);

            $json['resultado'] = TRUE;
        }

        echo json_encode($json);
    }


    public function getGerente()
    {
        $data = $this->registrolote_modelo->getGerente();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }


    public function newProspect()
    {
        $datos = array();
        $this->load->view('template/header');
        $this->load->view("asesor/prospectos", $datos);
    }

    public function consultProspects()
    {
        $datos = array();
        $this->load->view('template/header');
        $this->load->view("asesor/consulta_prospectos", $datos);
    }
    // Este es el query padre de todos los select
    // public function allSelectCatalogo()
    // {
    //     $data['data'] = $this->Asesor_model->getCatalogoSelect()->result_array();
    //     echo json_encode($data);
    //     // echo json_encode($this->Asesor_model->getCatalogoSelect()->result_array());
    // }

    public function consultStatistics()
    {
        $datos = array();
        $this->load->view('template/header');
        $this->load->view("asesor/consult_statistics", $datos);
    }

    public function getProspectingPlaces()
    {
        echo json_encode($this->Asesor_model->getProspectingPlaces()->result_array());
    }

    public function getNationality()
    {
        echo json_encode($this->Asesor_model->getNationality()->result_array());
    }

    public function getLegalPersonality()
    {
        echo json_encode($this->Asesor_model->getLegalPersonality()->result_array());
    }

    public function getAdvertising()
    {
        echo json_encode($this->Asesor_model->getAdvertising()->result_array());
    }

    public function getSalesPlaza()
    {
        echo json_encode($this->Asesor_model->getSalesPlaza()->result_array());
    }

    public function getCivilStatus()
    {
        echo json_encode($this->Asesor_model->getCivilStatus()->result_array());
    }

    public function getMatrimonialRegime()
    {
        echo json_encode($this->Asesor_model->getMatrimonialRegime()->result_array());
    }

    public function getState()
    {
        echo json_encode($this->Asesor_model->getState()->result_array());
    }

    public function getParentesco()
    {
        echo json_encode($this->Asesor_model->getParentesco()->result_array());
    }

    public function getModalidad()
    {
        echo json_encode($this->Asesor_model->getModalidad()->result_array());
    }

    public function getMediosVenta()
    {
        echo json_encode($this->Asesor_model->getMediosVenta()->result_array());
    }
    // public function getTipoVenta(){
    //     echo json_encode($this->Asesor_model->getTipoVenta()->result_array());
    // }
    public function getPlan()
    {
        echo json_encode($this->Asesor_model->getPlan()->result_array());
    }

    public function getProspectsList()
    {
        $data['data'] = $this->Asesor_model->getProspectsList()->result_array();
        echo json_encode($data);
    }

    public function getProspectInformation($id_prospecto)
    {
        echo json_encode($this->Asesor_model->getProspectInformation($id_prospecto)->result_array());
    }

    public function getInformationToPrint($id_prospecto)
    {
        echo json_encode($this->Asesor_model->getInformationToPrint($id_prospecto)->result_array());
    }

    public function getComments($id_prospecto)
    {
        echo json_encode($this->Asesor_model->getComments($id_prospecto)->result_array());
    }

    public function getChangelog($id_prospecto)
    {
        echo json_encode($this->Asesor_model->getChangelog($id_prospecto)->result_array());
    }

    public function saveComment()
    {
        if (isset($_POST) && !empty($_POST)) {
            $response = $this->Asesor_model->saveComment($this->session->userdata('id_usuario'), $this->input->post("id_prospecto"), $this->input->post("observations"));
            echo json_encode($response);
        }
    }

    public function updateProspect()
    {
        $specify = $_POST['specify'];
        if ($specify == '' || $specify == null) {
            $final_specification = 0;
        } else {
            $final_specification = $specify;
        }
        $data = array(
            "nacionalidad" => $_POST['nationality'],
            "personalidad_juridica" => $_POST['legal_personality'],
            "curp" => $_POST['curp'],
            "rfc" => $_POST['rfc'],
            "apellido_paterno" => $_POST['last_name'],
            "apellido_materno" => $_POST['mothers_last_name'],
            "correo" => $_POST['email'],
            "telefono" => $_POST['phone_number'],
            "telefono_2" => $_POST['phone_number2'],
            "lugar_prospeccion" => $_POST['prospecting_place'],
            "otro_lugar" => $final_specification,
            "medio_publicitario" => $_POST['advertising'],
            "plaza_venta" => $_POST['sales_plaza'],
            "observaciones" => $_POST['observation'],
            "fecha_nacimiento" => $_POST['date_birth'],
            "estado_civil" => $_POST['civil_status'],
            "regimen_matrimonial" => $_POST['matrimonial_regime'],
            "regimenFiscal" => $_POST['regimen_fac'],
            "conyuge" => $_POST['spouce'],
            "calle" => $_POST['street_name'],
            "numero" => $_POST['ext_number'],
            "colonia" => $_POST['suburb'],
            "municipio" => $_POST['town'],
            "estado" => $_POST['state'],
            "codigo_fac" => $_POST['cp_fac'],
            "tipo_vivienda" => $_POST['lives_at_home'],
            "ocupacion" => $_POST['occupation'],
            "empresa" => $_POST['company'],
            "posicion" => $_POST['position'],
            "antiguedad" => $_POST['antiquity'],
            "direccion" => $_POST['company_residence'],
            "edadFirma" => $_POST['company_antiquity'],
            "fecha_modificacion" => date("Y-m-d H:i:s"),
            "modificado_por" => $this->session->userdata('id_usuario')
        );
        $response = $this->Asesor_model->updateProspect($data, $this->input->post("id_prospecto_ed"));
        echo json_encode($response);
    }

    public function saveProspect()
    {
        $specify = $_POST['specify'];
        if ($specify == '' || $specify == null) {
            $final_specification = 0;
        } else {
            $final_specification = $specify;
        }
        $data = array(
            "nacionalidad" => $_POST['nationality'],
            "personalidad_juridica" => $_POST['legal_personality'],
            "curp" => $_POST['curp'],
            "rfc" => $_POST['rfc'],
            "tipo" => 0,
            "estatus" => 1,
            "nombre" => $_POST['name'],
            "apellido_paterno" => $_POST['last_name'],
            "apellido_materno" => $_POST['mothers_last_name'],
            "correo" => $_POST['email'],
            "telefono" => $_POST['phone_number'],
            "telefono_2" => $_POST['phone_number2'],
            "lugar_prospeccion" => $_POST['prospecting_place'],
            "otro_lugar" => $final_specification,
            "medio_publicitario" => $_POST['advertising'],
            "plaza_venta" => $_POST['sales_plaza'],
            "observaciones" => $_POST['observations'],
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "creado_por" => $this->session->userdata('id_usuario'),
            "fecha_modificacion" => date("Y-m-d H:i:s"),
            "modificado_por" => $this->session->userdata('id_usuario'),
            "fecha_nacimiento" => $_POST['date_birth'],
            "estado_civil" => $_POST['civil_status'],
            "regimen_matrimonial" => $_POST['matrimonial_regime'],
            "conyuge" => $_POST['spouce'],
            "calle" => $_POST['street_name'],
            "numero" => $_POST['ext_number'],
            "colonia" => $_POST['suburb'],
            "municipio" => $_POST['town'],
            "estado" => $_POST['state'],
            "codigo_postal" => $_POST['postal_code'],
            "tipo_vivienda" => $_POST['lives_at_home'],
            "ocupacion" => $_POST['occupation'],
            "empresa" => $_POST['company'],
            "posicion" => $_POST['position'],
            "antiguedad" => $_POST['antiquity'],
            "direccion" => $_POST['company_residence'],
            "edadFirma" => $_POST['company_antiquity'],
            "id_sede" => $this->session->userdata('id_sede'),
            "id_asesor" => $this->session->userdata('id_usuario'),
            "id_coordinador" => $this->session->userdata('id_coordinador'),
            "id_gerente" => $this->session->userdata('id_gerente')
        );
        $response = $this->Asesor_model->saveProspect($data);
        echo json_encode($response);
    }

    public function saveCoOwner()
    {
        $data = array(
            "nacionalidad" => $_POST['nationality_co'],
            "personalidad_juridica" => $_POST['legal_personality_co'],
            "rfc" => $_POST['rfc_co'],
            "estatus" => 1,
            "nombre" => $_POST['name_co'],
            "apellido_paterno" => $_POST['last_name_co'],
            "apellido_materno" => $_POST['mothers_last_name_co'],
            "correo" => $_POST['email_co'],
            "telefono" => $_POST['phone_number_co'],
            "telefono_2" => $_POST['phone_number2_co'],
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "creado_por" => $this->session->userdata('id_usuario'),
            "fecha_modificacion" => date("Y-m-d H:i:s"),
            "modificado_por" => $this->session->userdata('id_usuario'),
            "fecha_nacimiento" => $_POST['date_birth_co'],
            "estado_civil" => $_POST['civil_status_co'],
            "regimen_matrimonial" => $_POST['matrimonial_regime_co'],
            "conyuge" => $_POST['spouce_co'],
            "calle" => $_POST['street_name_co'],
            "numero" => $_POST['ext_number_co'],
            "colonia" => $_POST['suburb_co'],
            "municipio" => $_POST['town_co'],
            "estado" => $_POST['state_co'],
            "codigo_postal" => $_POST['postal_code_co'],
            "tipo_vivienda" => $_POST['lives_at_home_co'],
            "ocupacion" => $_POST['occupation_co'],
            "empresa" => $_POST['company_co'],
            "posicion" => $_POST['position_co'],
            "antiguedad" => $_POST['antiquity_co'],
            "direccion" => $_POST['company_residence_co'],
            "edadFirma" => $_POST['company_antiquity_co'],
            "id_cliente" => $_POST['id_prospecto_ed_co']
        );
        $response = $this->Asesor_model->saveCoOwner($data);
        echo json_encode($response);
    }

    public function toPrintProspectInfo($id_prospecto)
    {
        $this->load->library('Pdf');
        $pdf = new TCPDF('P', 'mm', 'LETTER', 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        // $pdf->SetAuthor('Sistemas Victor Manuel Sanchez Ramirez');
        $pdf->SetTitle('INFORMACIÓN GENERAL DE PROSPECCIÓN');
        $pdf->SetSubject('Información personal de prospecto / cliente (CRM)');
        $pdf->SetKeywords('CRM, INFROMACION, PERSONAL, PROSPECTO');
        // se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetAutoPageBreak(TRUE, 0);
        //relación utilizada para ajustar la conversión de los píxeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        // $pdf->setPrintFooter();
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 9, '', true);
        $pdf->SetMargins(7, 10, 10, true);
        $pdf->AddPage('P', 'LETTER');
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->Image('dist/img/ar4c.png', 120, 0, 300, 0, 'PNG', '', '', false, 150, '', false, false, 0, false, false, false);
        $pdf->setPageMark();
        $informacion = $this->Asesor_model->getPrintableInformation($id_prospecto)->row();
        $informacion_lugar = $this->Asesor_model->getProspectSpecification($id_prospecto)->row();
        if ($informacion) {
            $html = '
            <!DOCTYPE html>
            <html lang="es_mx"  ng-app="CRM">
        <head>
            <style>
            legend {
                background-color: #296D5D;
                color: #fff;
            }           
            </style>
        </head>
        <body>
              <section class="content">
                    <div class="row">
                        <div class="col-xs-10 col-md-offset-1">
                        <div class="box">
                            <div class="box-body">
                                  <table width="100%" style="height: 100px; border: 1px solid #ddd;" width="690">
                                    <tr>
                                        <td colspan="2" align="left"><img src="https://www.ciudadmaderas.com/assets/img/logo.png" style=" max-width: 70%; height: auto;"></td>
                                        <td colspan="2" align="right"><b style="font-size: 2em; "> Información<BR></b><small style="font-size: 2em; color: #777;"> Prospecto</small> 
                                        </td>
                                    </tr>
                                </table>
                                
                                <br><br>
                                  <table width="100%" style="padding:10px 0px; text-align: center;height: 45px; border: 1px solid #ddd;" width="690">
                                    <tr>
                                        <td colspan="2" style="background-color: #15578B;color: #fff;padding: 3px 6px; "><b style="font-size: 2em; ">Datos generales</b>
                                        </td>
                                    </tr>
                                </table>                            
                                <br>                       
                                    <div class="row">                
                                  <table width="100%" style="padding:10px 3px;height: 45px; border: 1px solid #ddd; text-align: center;" width="690">
                                        <tr>
                                            <td style="font-size: 1em;">
                                             <b>Nombre:</b><br>
                                             ' . $informacion->cliente . '
                                            </td>
                                            <td style="font-size: 1em;">
                                            <b>CURP:</b><br>
                                            ' . $informacion->curp . '
                                            </td>
                                            <td style="font-size: 1em;">
                                            <b>RFC:</b><br>
                                            ' . $informacion->rfc . '
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td style="font-size: 1em;">
                                             <b>Correo electrónico:</b><br>
                                             ' . $informacion->correo . '
                                            </td>
                                            <td style="font-size: 1em;">
                                            <b>Teléfono:</b><br>
                                            ' . $informacion->telefono . '
                                            </td>
                                            <td style="font-size: 1em;">
                                            <b>Teléfono 2 (opcional):</b><br>
                                            ' . $informacion->telefono_2 . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 1em;">
                                             <b>Personalidad jurídica:</b><br>
                                             ' . $informacion->personalidad . '
                                            </td>
                                            <td style="font-size: 1em;">
                                            <b>Nacionalidad:</b><br>
                                            ' . $informacion->nacionalidad . '
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <br>
                                    <br>                       
                                  <table width="100%" style="text-align: center;padding:10px;height: 45px; border-top: 1px solid #ddd;border-left: 1px solid #ddd;border-right: 1px solid #ddd;" width="690">
                                    <tr>
                                        <td colspan="2" style="background-color: #15578B;color: #fff;padding: 3px 6px; "><b style="font-size: 2em; ">Datos de prospección</b>
                                        </td>
                                    </tr>
                                </table>                            
                                <br><br>                                  
                                <table width="100%" style="padding:10px 3px;height: 45px; border: 1px solid #ddd; text-align: center;" width="690">
                                <tr>
                                    <td style="font-size: 1em;">
                                     <b>Asesor:</b><br>
                                     ' . $informacion->asesor . '
                                    </td>
                                    <td style="font-size: 1em;">
                                    <b>Coordinador:</b><br>
                                    ' . $informacion->coordinador . '
                                    </td> 
                                    <td style="font-size: 1em;">
                                    <b>Gerente:</b><br>
                                    ' . $informacion->gerente . '
                                    </td>
                                </tr>
                                <tr>
                                <td style="font-size: 1em;">
                                 <b>Teléfono asesor:</b><br>
                                 ' . $informacion->telefono_asesor . '
                                </td>
                                <td style="font-size: 1em;">
                                <b>Teléfono coordinador:</b><br>
                                ' . $informacion->telefono_coordinador . '
                                </td> 
                                <td style="font-size: 1em;">
                                <b>Teléfono gerente:</b><br>
                                ' . $informacion->telefono_gerente . '
                                </td>
                            </tr>
                            </table>
                            <table width="100%" style="padding:10px 3px;height: 45px; border: 1px solid #ddd; text-align: center;" width="690">
                            <tr>
                                <td style="font-size: 1em;">
                                 <b>Lugar:</b><br>
                                 ' . $informacion->lugar . '<br>
                                 ' . $informacion_lugar->especificar . '
                                </td>
                                <td style="font-size: 1em;">
                                <b>Método:</b><br>
                                ' . $informacion->metodo . '
                                </td> 
                                <td style="font-size: 1em;">
                                <b>Plaza de venta:</b><br>
                                ' . $informacion->plaza . '
                                </td>
                                <td style="font-size: 1em;">
                                <b>Creado por:</b><br>
                                ' . $informacion->creacion . '
                                </td>
                            </tr>
                        </table>
                            <br>
                            <br>
                            <br>
                                  <body>
            </html>
                                  ';

            $pdf->writeHTMLCell(0, 0, $x = '', $y = '10', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
            ob_end_clean();
            $pdf->Output(utf8_decode("Informacion_" . $informacion->cliente . ".pdf"), 'I');
        }
    }


    public function depositoSeriedad()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        // $this->validateSession();
        $this->load->view('template/header');
        $this->load->view("asesor/depositoSeriedad", $datos);
    }



    public function depositoSeriedadConsulta()
    {
        // $this->validateSession();
        $this->load->view('template/header');
        $this->load->view("asesor/DSConsult");
    }

    public function documentacion()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $datos["residencial"] = $this->registrolote_modelo->getResidencialQro();
        $this->load->view('template/header');
        $this->load->view("contratacion/datos_cliente_documentos_contratacion_view", $datos);
    }

    public function registrosLoteVentasAsesor()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $datos["residencial"] = $this->Asesor_model->get_proyecto_lista();
        $this->load->view('template/header');
        $this->load->view("contratacion/datos_lote_contratacion_view", $datos);
    }


    public function invDispAsesor()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/

        $datos["residencial"] = $this->registrolote_modelo->getResidencialQro();
        $this->load->view('template/header');
        $this->load->view("asesor/inventario_disponible", $datos);
    }

    public function manual()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $this->load->view('template/header');
        // $this->load->view("asesor/manual_view",$datos);
        $this->load->view("asesor/manuales_view", $datos);
    }

    public function validateSession()
    {
        if ($this->session->userdata('id_rol') == "") {
            //echo "<script>console.log('No hay sesión iniciada');</script>";
            redirect(base_url() . "index.php/login");
        }
    }

    public function getLotesInventarioGralTodosc()
    {

        $data = $this->Asesor_model->getInventarioTodosc();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }


    public function getCondominioDesc($residenciales)
    {

        $data = $this->Asesor_model->getCondominioDesc($residenciales);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getCondominioDescTodos()
    {

        $data = $this->Asesor_model->getCondominioDescTodos();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getSupOne($residencial)
    {
        $data = $this->Asesor_model->getSupOne($residencial);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getSupOneTodos()
    {
        $data = $this->Asesor_model->getSupOneTodos();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getPrecio($residencial)
    {
        $data = $this->Asesor_model->getPrecio($residencial);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getPrecioTodos()
    {
        $data = $this->Asesor_model->getPrecioTodos();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getTotal($residencial)
    {
        $data = $this->Asesor_model->getTotal($residencial);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getTotalTodos()
    {
        $data = $this->Asesor_model->getTotalTodos();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getMeses($id, $type)
    {
        $data = $this->Asesor_model->getMeses($id, $type);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getMesesTodos()
    {
        $data = $this->Asesor_model->getMesesTodos();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getLotesInventarioXproyectoc($residencial)
    {
        $data = $this->Asesor_model->getInventarioXproyectoc($residencial);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }


    function getLotesInventarioGralc($residencial, $condominio)
    {
        $data = $this->registrolote_modelo->getInventarioc($residencial, $condominio);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }


    public function getMesesResidencial($residencial, $meses)
    {
        $data = $this->Asesor_model->getMesesResidencial($residencial, $meses);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getMesesCluster($residencial, $condominio, $meses)
    {
        $data = $this->Asesor_model->getMesesCluster($residencial, $condominio, $meses);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function getEmpy()
    {
        $data = [];
        echo json_encode($data);
    }

    function getTwoGroup($residencial, $grupo)
    {
        $data = $this->Asesor_model->getTwoGroup($residencial, $grupo);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    function getOneGroup($condominio, $grupo)
    {
        $data = $this->Asesor_model->getOneGroup($condominio, $grupo);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
        exit;
    }

    public function tableClienteDS()
    {
        $objDatos = json_decode(file_get_contents("php://input"));
        $dato = $this->Asesor_model->registroClienteDS($this->input->post('id_condominio'));
        $data = array();

        for ($i = 0; $i < COUNT($dato); $i++) {
            $query = $this->Asesor_model->getDataDs1($dato[$i]['id_cliente']);
            if (count($query) <= 0) {
                $query = $this->Asesor_model->getDataDs2($dato[$i]['id_cliente']);
                if (count($query) <= 0) {
                    $query = $this->Asesor_model->getDataDs3($dato[$i]['id_cliente']);
                }
            }
            $data[$i]['qry'] = $query[0]->qry;
            $data[$i]['dsType'] = $query[0]->dsType;
            $data[$i]['id_cliente'] = $query[0]->id_cliente;
            $data[$i]['id_asesor'] = $query[0]->id_asesor;
            $data[$i]['id_coordinador'] = $query[0]->id_coordinador;
            $data[$i]['id_gerente'] = $query[0]->id_gerente;
            $data[$i]['id_sede'] = $query[0]->id_sede;
            $data[$i]['nombre'] = $query[0]->nombre;
            $data[$i]['apellido_paterno'] = $query[0]->apellido_paterno;
            $data[$i]['apellido_materno'] = $query[0]->apellido_materno;
            $data[$i]['idLote'] = $query[0]->idLote;
            $data[$i]['fechaApartado'] = $query[0]->fechaApartado;
            $data[$i]['fechaVencimiento'] = $query[0]->fechaVencimiento;
            $data[$i]['usuario'] = $query[0]->usuario;
            $data[$i]['idCondominio'] = $query[0]->idCondominio;
            $data[$i]['fecha_creacion'] = $query[0]->fecha_creacion;
            $data[$i]['creado_por'] = $query[0]->creado_por;
            $data[$i]['fecha_modificacion'] = $query[0]->fecha_modificacion;
            $data[$i]['modificado_por'] = $query[0]->modificado_por;
            $data[$i]['nombreCondominio'] = $query[0]->nombreCondominio;
            $data[$i]['nombreResidencial'] = $query[0]->nombreResidencial;
            $data[$i]['status'] = $query[0]->status;
            $data[$i]['nombreLote'] = $query[0]->nombreLote;
            $data[$i]['comentario'] = $query[0]->comentario;
            $data[$i]['idMovimiento'] = $query[0]->idMovimiento;
            $data[$i]['idStatusContratacion'] = $query[0]->idStatusContratacion;
            $data[$i]['id_prospecto'] = $query[0]->id_prospecto;
            $data[$i]['concepto'] = $query[0]->concepto;
            $data[$i]['fechaVenc'] = $query[0]->fechaVenc;
            $data[$i]['modificado'] = $query[0]->modificado;
            $data[$i]['vl'] = $query[0]->vl;
            $data[$i]['flag_compartida'] = $query[0]->flag_compartida;
            $data[$i]['coordinador'] = $query[0]->coordinador;
            $data[$i]['gerente'] = $query[0]->gerente;
            $data[$i]['subdirector'] = $query[0]->subdirector;
            $data[$i]['regional'] = $query[0]->regional;
            $data[$i]['estatus'] = $query[0]->estatus;
            $data[$i]['tipo_comprobanteD'] = ($query[0]->tipo_comprobanteD == '' || $query[0]->tipo_comprobanteD==NULL) ? 0 : $query[0]->tipo_comprobanteD;
        }


        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    /****nuevo en modelo 28-10-20*****/
    public function get_info_prospectos()
    {
        $id_asesor = $this->session->userdata('id_usuario');
        $data = $this->Asesor_model->get_info_prospectos($id_asesor);


        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }

    }

    public function prospecto_a_cliente()
    {
        $id_prospecto = $this->input->post('id_prospecto');
        $id_cliente = $this->input->post('id_cliente');

        $data_prospecto = $this->Asesor_model->getProspectInfoById($id_prospecto);
        $data_update_client = array(
            'id_sede' => $data_prospecto[0]->id_sede,
            'rfc' => $data_prospecto[0]->rfc,
            'curp' => $data_prospecto[0]->curp,
            'correo' => $data_prospecto[0]->correo,
            'telefono1' => $data_prospecto[0]->telefono,
            'telefono2' => $data_prospecto[0]->telefono_2,
            'lugar_prospeccion' => $data_prospecto[0]->lugar_prospeccion,
            'otro_lugar' => $data_prospecto[0]->otro_lugar,
            'plaza_venta' => $data_prospecto[0]->plaza_venta,
            'medio_publicitario' => $data_prospecto[0]->medio_publicitario,
            'nacionalidad' => $data_prospecto[0]->nacionalidad,
            'fecha_nacimiento' => $data_prospecto[0]->fecha_nacimiento,
            'estado_civil' => $data_prospecto[0]->estado_civil,
            'regimen_matrimonial' => $data_prospecto[0]->regimen_matrimonial,
            'nombre_conyuge' => $data_prospecto[0]->conyuge,
            'domicilio_particular' => $data_prospecto[0]->domicilio_particular,
            'originario_de' => $data_prospecto[0]->originario_de,
            'tipo_vivienda' => $data_prospecto[0]->tipo_vivienda,
            'ocupacion' => $data_prospecto[0]->ocupacion,
            'empresa' => $data_prospecto[0]->empresa,
            'antiguedad' => $data_prospecto[0]->antiguedad,
            'edadFirma' => $data_prospecto[0]->edadFirma,
            'puesto' => $data_prospecto[0]->posicion,
            'id_prospecto' => $id_prospecto
        );
        $update_cliente = $this->Asesor_model->update_client_from_prospect($id_cliente, $data_update_client);

        if ($update_cliente > 0) {
            $data_response['cliente_update'] = 'OK';

            /*cuando el cliente se haya actualizado correctamente*/
            $dataActualizaProspecto = array(
                'tipo' => 1,
                'becameClient' => date('Y-m-d H:i:s'),
                'estatus_particular' => 7

            );
            if ($this->caja_model_outside->updateProspecto($id_prospecto, $dataActualizaProspecto) > 0) {
                //echo "acciones realizadas correctamente";
                $data_response['prospecto_update'] = 'OK';
            } else {
                $data_response['prospecto_update'] = 'FAIL';
            }
        } else {
            $data_response['cliente_update'] = 'FAIL';
        }
        echo json_encode($data_response);

    }

    /*********************************/


    public function getInfoTest($id_cliente)
    {
        $datos["cliente"] = $this->Asesor_model->selectDS($id_cliente);
        // print_r(json_encode($datos));
    }

    public function deposito_seriedad($id_cliente, $onlyView)
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $datos["cliente"] = $this->Asesor_model->selectDS($id_cliente);

        $datos["cliente"][0]->tipo_nc = ( $datos["cliente"][0]->tipo_nc === null || $datos["cliente"][0]->tipo_nc === '' ) ? 3 : $datos["cliente"][0]->tipo_nc;

        $datos["referencias"] = $this->Asesor_model->selectDSR($id_cliente);
        if (count($datos["referencias"]) < 1) {
            $aray1 = array(
                'id_referencia' => '',
                'nombre' => '',
                'parentesco' => '',
                'telefono' => '');
            /**/
            $datos["referencias"][0] = (object)$aray1;
            $datos["referencias"][1] = (object)$aray1;
        }


        $datos["asesor"] = $this->Asesor_model->selectDSAsesor($id_cliente);
        if (count($datos["asesor"]) < 1) {
            $aray1 = array(
                'id_usuario' => '',
                'nombreAsesor' => '',
                'id_lider' => '',
                'nombreGerente' => '',
                'nombreCoordinador' => '',
                'correo' => '');
            /**/
            $datos["asesor"][0] = (object)$aray1;
        }
        $datos["asesor2"] = $this->Asesor_model->selectDSAsesorCompartido($id_cliente);
        $datos["copropiedad"] = $this->Asesor_model->selectDSCopropiedad($id_cliente);
        $datos["copropiedadTotal"] = $this->Asesor_model->selectDSCopropiedadCount($id_cliente);

        $catalogs = $this->Asesor_model->getCatalogs()->result_array();

        $arrayobj1 = new ArrayObject();
        $arrayobj2 = new ArrayObject();
        $arrayobj3 = new ArrayObject();
        $arrayobj4 = new ArrayObject();
        $arrayobj5 = new ArrayObject();

        for ($i = 0; $i < count($catalogs); $i++) {
            if ($catalogs[$i]["id_catalogo"] == 11)  // MJ: NACIONALIDAD
                $arrayobj1->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 18) // MJ: ESTADO CIVIL
                $arrayobj2->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 19) // MJ: REGIMEN MATRIMONIAL
                $arrayobj3->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 26) // MJ: PARENTESCO
                $arrayobj4->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 88) // UR: REGIMEN FISCAL
                $arrayobj5->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
        }

        $datos["nacionalidades"] = $arrayobj1;
        $datos["edoCivil"] = $arrayobj2;
        $datos["regMat"] = $arrayobj3;
        $datos["parentescos"] = $arrayobj4;
        $datos["regFis"] = $arrayobj5;


        $datos['onlyView'] = $onlyView;
        $datos['corrida_financiera'] = $this->Asesor_model->getInfoCFByCl($id_cliente);
        if(isset($datos['corrida_financiera']->id_corrida)){
            $datos['descuentos_aplicados'] = $this->Asesor_model->getDescsByCF($datos['corrida_financiera']->id_corrida);
        }else{
            $datos['descuentos_aplicados'] = array();
        }

        $this->load->view('template/header');
        $this->load->view('asesor/deposito_formato', $datos);

    }

    public function getHistorialDS($idCliente)
    {
        $columnas = $this->Contraloria_model->getCamposHistorialDS($idCliente);
        foreach ($columnas as &$columna) {
            $columna['detalle'] = $this->Contraloria_model->getDetalleCamposHistorialDS($idCliente, $columna['columna']);
        }
        echo json_encode($columnas);
    }

    public function imprimir_ds($id_cliente)
    {
        setlocale(LC_MONETARY, 'en_US.UTF-8');
        $informacion_cliente = $this->Asesor_model->getinfoCliente($id_cliente);
        $informacion_referencias = $this->Asesor_model->getinfoReferencias($id_cliente);
        $informacion_copropietarios = $this->Asesor_model->getinfoCopropietario($id_cliente);

        $informacion_asesor = $this->Asesor_model->selectDSAsesor1($id_cliente);
        $informacion_asesor2 = $this->Asesor_model->selectDSAsesorCompartido1($id_cliente);

        $catalogs = $this->Asesor_model->getCatalogs()->result_array();
        $arrayobj1 = new ArrayObject();
        $arrayobj2 = new ArrayObject();
        $arrayobj3 = new ArrayObject();
        //$arrayobj4 = new ArrayObject();
        for ($i = 0; $i < count($catalogs); $i++) {
            if ($catalogs[$i]["id_catalogo"] == 11)  // MJ: NACIONALIDAD
                $arrayobj1->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 18) // MJ: ESTADO CIVIL
                $arrayobj2->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 19) // MJ: REGIMEN MATRIMONIAL
                $arrayobj3->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 92) // UR: REGIMEN FISCAL
                $arrayobj5->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
        }
        $nacionalidades = $arrayobj1;
        $edoCivil = $arrayobj2;
        $regMat = $arrayobj3;
        $regFiscal = $arrayobj4;

        $asesor = $this->Asesor_model->selectDSAsesor($id_cliente);

        $asesor2 = $this->Asesor_model->selectDSAsesorCompartido($id_cliente);

        $costoM2 = ($informacion_cliente->row()->desarrollo == 17) ? $informacion_cliente->row()->costoM2_casas : $informacion_cliente->row()->costoM2;

        if ($informacion_cliente->row()->tipoLote != '' || $informacion_cliente->row()->tipoLote != null) {
            if ($informacion_cliente->row()->tipoLote == 0) {
                $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" checked="checked" readonly> Lote';
                $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="2" readonly> Lote Comercial';
            } elseif ($informacion_cliente->row()->tipoLote == 1) {
                $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" readonly> Lote';
                $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="2" checked="checked" readonly> Lote Comercial';
            } else {
                $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" readonly> Lote';
                $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="2" readonly> Lote Comercial';
            }
        } else {
            $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" readonly> Lote';
            $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="2" readonly> Lote Comercial';
        }
        /////////////////////////////////////////////////////////////////////////////////////////

        if ($informacion_cliente->row()->desarrollo) {
            // $arreglo_ds["desarrollo"]= $desarrollo;
            if ($informacion_cliente->row()->desarrollo == 1 || $informacion_cliente->row()->desarrollo == 2 ||
                $informacion_cliente->row()->desarrollo == 5 || $informacion_cliente->row()->desarrollo == 6 ||
                $informacion_cliente->row()->desarrollo == 7 || $informacion_cliente->row()->desarrollo == 8 ||
                $informacion_cliente->row()->desarrollo == 11) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" checked="checked" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            } elseif ($informacion_cliente->row()->desarrollo == 13 || $informacion_cliente->row()->desarrollo == 3) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" checked="checked" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            } elseif ($informacion_cliente->row()->desarrollo == 9 || $informacion_cliente->row()->desarrollo == 10) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" checked="checked" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            } elseif ($informacion_cliente->row()->desarrollo == 4 || $informacion_cliente->row()->desarrollo == 14) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" checked="checked" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            } elseif ($informacion_cliente->row()->desarrollo == 12 || $informacion_cliente->row()->desarrollo == 17) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" checked="checked" readonly> Mérida';
            } else {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            }
        } else if (!$informacion_cliente->row()->desarrollo) {
            // $arreglo_ds["desarrollo"]= '0';
        }


        /////////////////////////////////////////////////////////////////////////////////////////


        if ($informacion_cliente->row()->idOficial_pf) {
            $id_identificacion = '<input type="checkbox" readonly name="idOficial_pf" id="idOficial_pf" value="1" checked="checked"> Identificación&nbsp;Oficial';
        } else if (!$informacion_cliente->row()->idOficial_pf) {
            $id_identificacion = '<input type="checkbox" readonly name="idOficial_pf" id="idOficial_pf" value="1"> Identificación&nbsp;Oficial';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($informacion_cliente->row()->idDomicilio_pf) {
            $id_domicilio = '<input type="checkbox" readonly name="idDomicilio_pf" id="idDomicilio_pf" value="1" checked="checked"> Comprobante&nbsp;de&nbsp;Domicilio';
        } else if (!$informacion_cliente->row()->idDomicilio_pf) {
            $id_domicilio = '<input type="checkbox" readonly name="idDomicilio_pf" id="idDomicilio_pf" value="1"> Comprobante&nbsp;de&nbsp;Domicilio';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($informacion_cliente->row()->actaMatrimonio_pf) {
            $id_acta_m = '<input type="checkbox" readonly name="actaMatrimonio_pf" id="actaMatrimonio_pf" value="1" checked="checked"> Acta&nbsp;de&nbsp;Matrimonio';
        } else if (!$informacion_cliente->row()->actaMatrimonio_pf) {
            $id_acta_m = '<input type="checkbox" readonly name="actaMatrimonio_pf" id="actaMatrimonio_pf" value="1"> Acta&nbsp;de&nbsp;Matrimonio';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($informacion_cliente->row()->actaConstitutiva_pm) {
            $id_acta_c = '<input type="checkbox" readonly name="actaConstitutiva_pm" id="actaConstitutiva_pm" value="1" checked="checked"> Acta&nbsp;Constitutiva';
        } else if (!$informacion_cliente->row()->actaConstitutiva_pm) {
            $id_acta_c = '<input type="checkbox" readonly name="actaConstitutiva_pm" id="actaConstitutiva_pm" value="1"> Acta&nbsp;Constitutiva';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($informacion_cliente->row()->poder_pm) {
            $id_poder = '<input type="checkbox" readonly name="poder_pm" id="poder_pm" value="1" checked="checked"> Poder';
        } else if (!$informacion_cliente->row()->poder_pm) {
            $id_poder = '<input type="checkbox" readonly name="poder_pm" id="poder_pm" value="1"> Poder';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($informacion_cliente->row()->idOficialApoderado_pm) {
            $id_apoderado = '<input type="checkbox" readonly name="idOficialApoderado_pm" id="idOficialApoderado_pm" value="1" checked="checked"> Identificación&nbsp;Oficial&nbsp;Apoderado';
        } else if (!$informacion_cliente->row()->idOficialApoderado_pm) {
            $id_apoderado = '<input type="checkbox" readonly name="idOficialApoderado_pm" id="idOficialApoderado_pm" value="1"> Identificación&nbsp;Oficial&nbsp;Apoderado';
        }

        // //----------------------------------------------------------------------------------------------------------
        // /////////////////////////////////////////////////////////////////////////////////////////////////////////////


        if ($informacion_cliente->row()->tipo_vivienda != '' || $informacion_cliente->row()->tipo_vivienda != null) {
            // $arreglo_cliente["tipo_vivienda"]= $tipo_vivienda;

            if ($informacion_cliente->row()->tipo_vivienda == 1) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" checked="checked" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
            if ($informacion_cliente->row()->tipo_vivienda == 2) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" checked="checked" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
            if ($informacion_cliente->row()->tipo_vivienda == 3) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" checked="checked" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
            if ($informacion_cliente->row()->tipo_vivienda == 4) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" checked="checked" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
            if ($informacion_cliente->row()->tipo_vivienda == 5) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" checked="checked" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
            if ($informacion_cliente->row()->tipo_vivienda == 6) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" checked="checked" readonly> SIN ESPECIFICAR';
            } else {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5"  readonly> OTRO';
                $tv6 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="6" readonly> SIN ESPECIFICAR';
            }
        } else if (!$informacion_cliente->row()->tipo_vivienda) {
            // $arreglo_cliente["tipo_vivienda"]= '0';
        }


        //CONVERTIMOS A ARREGLO TANTO LOS DESCUENTOS ACTUALES COMO EL NUEVO A AGREGAR
        $arrayCorreo = explode(",", 'kelyn.rodriguez23@gmail.com');

        // CHECAMOS SI EN EL ARREGLO NO HAY POSICIONES VACIAS Y LAS ELIMINAMOS
        $listCheckVacio = array_filter($arrayCorreo, "strlen");

        //VERIFICAMOS QUE NUESTRO ARREGLO NO TENGA DATOS REPETIDOS
        $arrayCorreoNotRepeat = array_unique($listCheckVacio);

        //EL ARREGLO FINAL LO CONVERTIMOS A STRING
        // $resCorreo = implode( ",", $arrayCorreoNotRepeat);

        $this->load->library('Pdf');
        $pdf = new TCPDF('P', 'mm', 'LETTER', 'UTF-8', false);
        // $pdf = new TCPDF('P', 'mm', 'LETTER', 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistemas María José Martínez Martínez');
        $pdf->SetTitle('DEPÓSITO DE SERIEDAD');
        $pdf->SetSubject('CONSTANCIA DE RELACION EMPRESA TRABAJADOR');
        $pdf->SetKeywords('CONSTANCIA, CIUDAD MADERAS, RELACION, EMPRESA, TRABAJADOR');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetAutoPageBreak(TRUE, 0);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('Helvetica', '', 9, '', true);
        $pdf->SetMargins(15, 15, 15, true);
        $pdf->AddPage('P', 'LEGAL');
        $pdf->SetFont('Helvetica', '', 5, '', true);
        $pdf->SetFooterMargin(0);
        $bMargin = $pdf->getBreakMargin();
        $auto_page_break = $pdf->getAutoPageBreak();
        $pdf->Image('static/images/ar4c.png', 120, 15, 300, 0, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
        $pdf->setPageMark();


        $html = '<!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="shortcut icon" href="' . base_url() . 'static/images/arbol_cm.png" />
            <link "<?=base_url()?>dist/css/bootstrap.min.css" rel="stylesheet" />
            <!--  Material Dashboard CSS    -->
            <link href="<?=base_url()?>dist/css/material-dashboard.css" rel="stylesheet" />
            <!--  CSS for Demo Purpose, don\'t include it in your project     -->
            <link href="<?=base_url()?>dist/css/demo.css" rel="stylesheet" />
            <!--     Fonts and icons     -->
            <link href="<?=base_url()?>dist/css/font-awesome.css" rel="stylesheet" />
            <link href="<?=base_url()?>dist/css/google-roboto-300-700.css" rel="stylesheet" />
            <style>
            body{color: #084c94;}
            .espacio{padding: 5%;}
            .espaciodos{padding: 10%;} 
            h2{font-weight: bold;color: #084c94;}
            .save {display:scroll;position:fixed;bottom:225px;right:17px;z-index: 3;}
            p{color: #084c94;}
            .col-xs-16 {width: 3px;float: left;}
            .col-xs-17 {width: 16%;float: left;}
            #imagenbg {position: relative;top:1500px;z-index: -1;}
            #fichadeposito {position: absolute;z-index: 2;}
            .mySectionPdf
            {
                padding: 20px;
            }
            .form-group.is-focused .form-control 
            {
                outline: none;
                background-image: linear-gradient(#0c63c5, #177beb), linear-gradient(#D2D2D2, #D2D2D2);
                background-size: 100% 2px, 100% 1px;
                box-shadow: none;
                transition-duration: 0.3s;
            }
            b
            {
                font-size: 8px;
            }
            </style>
            </head>

            <body>
            <div id="fichadeposito" name="fichadeposito" class="fichadeposito">
            <div id="muestra">
            <table border="0" width="100%" id="tabla" align="center">
            <tr>
            
            <td width="70%" align="left">
             <label>
            <h1 style="margin-right: 50px;"> DEPÓSITO DE SERIEDAD</h1>
            </label>
            </td>

            <td align="right" width="15%">
            <br><br><br>
            <p style="margin-right: 2px;">FOLIO</p>
            </td>

            <td width="15%" style="border-bottom:1px solid #CCCCCC">
            <p style="color: red;font-size:14px;">' . $informacion_cliente->row()->clave . '</p>
            </td>

            </tr>
            </table>

            <table border="0" width="100%" align="" align="">
            <tr>
            <th rowspan="4" width="283" align="left">
            <img src="' . base_url() . '/static/images/CMOF.png" alt="Servicios Condominales" title="Servicios Condominales" style="width: 250px"/>
            </th>

            <td width="367">
            <h5><p style="font-size:9px;"><strong>DESARROLLO:</strong></p></h5>
            </td>
            </tr>

            <tr>
            <td width="367">
            <table border="0" width="100%">
            <tr>
            <td width="20%">' . $d1 . '</td>
            <td width="20%">' . $d2 . '</td>
            <td width="20%">' . $d3 . '</td>
            <td width="20%">' . $d4 . '</td>
            <td width="20%">' . $d5 . '</td>
            </tr>

            <tr>
            <td width="20%">' . $tpl1 . '</td>
            <td width="20%">' . $tpl2 . '</td>
            <td width="20%"></td>
            <td width="20%"></td>
            <td width="20%"></td>
            </tr>

            </table>
            </td>
            </tr>

            <tr>
            <td>
            <h5><p style="font-size:9px;"><strong>DOCUMENTACIÓN ENTREGADA:</strong></p></h5>
            </td>
            </tr>

            <tr>
            <td>
            <table border="0" width="100%">
            <tr>
            <td width="19 %"><p><strong>Personas&nbsp;Físicas</strong></p></td>
            <td width="23%">' . $id_identificacion . '</td>
            <td width="27%">' . $id_domicilio . '</td>
            <td width="29%" colspan="2">' . $id_acta_m . '</td>
            </tr>

            <tr>
            <td width="19%"><p><strong>Personas&nbsp;Morales</strong></p></td>
            <td width="23%">' . $id_acta_c . '</td>
            <td width="27%">' . $id_poder . '</td>
            <td width="29%" colspan="2">' . $id_apoderado . '</td>
            </tr>
            ';
            
            if ($informacion_cliente->row()->rfc != '' && $informacion_cliente->row()->rfc != null){
            $html .= '<tr>
            <th colspan="3">
            <h5><p style="font-size:9px;"><strong>DATOS FACTURACIÓN:</strong></p></h5>
            </th>
            </tr>
            <tr>
                <td width="20%"><b>RFC:</b> ' . $informacion_cliente->row()->rfc . '</td>
                <td width="50%"><b>RÉGIMEN FISCAL:</b> ' . $informacion_cliente->row()->reg_nom . '</td>
                <td width="30%"><b>CODIGO POSTAL:</b> ' . $informacion_cliente->row()->cp_fac . '</td>
                <td width="29%" colspan="2"></td>
            </tr>';
             }
            
            $html .= '</table>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2">
            <br>
            </td>
            </tr>
            
            <tr>
            <td width="40%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>NOMBRE(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $informacion_cliente->row()->nombre . ' <br></b>
            </td>
            <td width="30%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>APELLIDO PATERNO(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $informacion_cliente->row()->apellido_paterno . ' <br></b>
            </td>
            <td width="30%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>APELLIDO MATERNO(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $informacion_cliente->row()->apellido_materno . ' <br></b>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>

            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>TELÉFONO CASA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->telefono1 . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>CELULAR (<b><span style="color: red;">*</span></b>) </label><br><br><b>&nbsp;' . $informacion_cliente->row()->telefono2 . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label> EMAIL (<b><span style="color: red;">*</span></b>)
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->correo . '</b><br>
            </td>
            </tr>
            
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA DE NACIMIENTO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->fecha_nacimiento . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NACIONALIDAD:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->nacionalidad_valor . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ORIGINARIO DE:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->originario . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ESTADO CIVIL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->estado_valor . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NOMBRE CONYUGE:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->nombre_conyuge . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>RÉGIMEN:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->regimen_valor . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DOMICILIO PARTICULAR:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->domicilio_particular . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>OCUPACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->ocupacion . '</b><br>
            </td>
            <td width="35%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EMPRESA EN LA QUE TRABAJA:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->empresa . '</b><br>
            </td>
            <td width="35%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>PUESTO:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->puesto . '</b><br>
            </td>
            <td width="10%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ANTIGÜEDAD:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->antiguedad . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EDAD:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->edadFirma . '</b><br>
            </td>
            <td width="70%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DOMICILIO EMPRESA:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->domicilio_empresa . '</b><br>
            </td>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>TELÉFONO EMPRESA:
                </label><br><br><b>&nbsp;' . $informacion_cliente->row()->telefono_empresa . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
 
            <tr>
            <td width="15%" colspan="2"><b>VIVE EN CASA:</b></td>
            <td width="10%">' . $tv1 . '</td>
            <td width="10%">' . $tv2 . '</td>
            <td width="10%">' . $tv3 . '</td>
            <td width="10%">' . $tv4 . '</td>
            <td width="10%">' . $tv5 . '</td>
            <td width="10%">' . $tv6 . '</td>
            </tr>
            <tr><td><br></td></tr>
            ';


        if ($informacion_copropietarios->num_rows() > 0) {
            $html .= '<tr><td width="100%" colspan="2" style="background-color:#BECFDC;"><b style="font-size:1.7em;">DATOS COOPROPIETARIOS:</b><br></td></tr>';
        } else {
            $html .= '';
        }


        if ($informacion_copropietarios->num_rows() > 0) {
            foreach ($informacion_copropietarios->result() as $row) {
                $html .= '<tr style="background-color:#BECFDC;">
                    <td width="22%"><b>NOMBRE: </b>' . $row->nombre_cop . ' ' . $row->apellido_paterno . ' ' . $row->apellido_materno . '</td>
                    <td width="26%"><b>PERSONALIDAD JURÍDICA: </b>' . $row->personalidad_juridica . '</td>
                    <td width="12%"><b>RFC: </b>' . $row->rfc . '</td>
                    <td width="16%"><b>CORREO: </b>' . $row->correo . '</td>
                    <td width="12%"><b>TEL: </b>' . $row->telefono . '</td>
                    <td width="12%"><b>TEL 2: </b>' . $row->telefono_2 . '</td>
                    </tr>

                    <tr style="background-color:#BECFDC;">
                    <td width="22%"><b>FECHA NACIMIENTO: </b>' . $row->fecha_nacimiento . '</td>
                    <td width="26%"><b>NACIONALIDAD: </b>';

                for ($n = 0; $n < count($nacionalidades); $n++) {
                    if ($nacionalidades[$n]['id_opcion'] == $row->nacionalidad_valor) {
                        $html .= $nacionalidades[$n]['nombre'];
                    }
                }
                $html .= '</td>
                    <td width="12%"><b>EDAD FIRMA: </b>' . $row->edadFirma . '</td>
                    <td width="16%"><b>ESTADO CIVIL: </b>';
                for ($n = 0; $n < count($edoCivil); $n++) {
                    if ($edoCivil[$n]['id_opcion'] == $row->estado_valor) {
                        $html .= $edoCivil[$n]['nombre'];
                    }
                }

                $html .= '</td>
                    <td width="24%"><b>CONYUGE: </b>' . $row->conyuge . '</td>
                    </tr>

                    <tr style="background-color:#BECFDC;">
                    <td width="22%"><b>REGIMEN: </b>';

                #$row->regimen_matrimonial
                for ($n = 0; $n < count($regMat); $n++) {
                    if ($regMat[$n]['id_opcion'] == $row->regimen_valor) {
                        $html .= $regMat[$n]['nombre'];
                    }
                }
                $html .= '</td>
                    <td width="26%"><b>DOMICILIO: </b>' . $row->domicilio_particular . '</td>
                    <td width="28%"><b>ORIGINARIO DE: </b>' . $row->originario_de . '</td>
                    <td width="24%"><b>TIPO VIVIENDA: </b>';
                if ($row->tipo_vivienda == 1) {
                    $html .= 'PROPIA';
                }
                if ($row->tipo_vivienda == 2) {
                    $html .= 'RENTADA';
                }
                if ($row->tipo_vivienda == 3) {
                    $html .= 'PAGÁNDOSE';
                }
                if ($row->tipo_vivienda == 4) {
                    $html .= 'FAMILIAR';
                }
                if ($row->tipo_vivienda == 5) {
                    $html .= 'OTRO';
                }
                if ($row->tipo_vivienda == '' || $row->tipo_vivienda == null) {
                    $html .= 'SIN ESPECIFICAR';
                }
                $html .= '</td>
                    </tr>
                    
                    <tr style="background-color:#BECFDC;">
                    <td width="22%"><b>OCUPACIÓN: </b>' . $row->ocupacion . '</td>
                    <td width="26%"><b>EMPRESA: </b>' . $row->empresa . '</td>
                    <td width="28%"><b>PUESTO: </b>' . $row->posicion . '</td>
                    <td width="24%"><b>ANTIGÜEDAD: </b>' . $row->antiguedad . '</td>
                    </tr>

                    <tr style="background-color:#BECFDC;">
                    <td width="100%"><b>DIRECCIÓN EMPRESA: </b>' . $row->direccion . '</td>
                    </tr>

                    <tr style="background-color:#BECFDC;">
                    <td width="100%"><br></td>
                    </tr>';
            }
        } else {
            $html .= '<tr><center><td>No hay co - propietarios</center></td></tr>';
        }

        $html .= '<tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>

            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>El Sr.(a):';
        $nomCopops = '';
        if ($informacion_copropietarios->num_rows() > 0) {
            foreach ($informacion_copropietarios->result() as $row) {
                $nomCopops .= '/ ' . $row->nombre_cop . ' ' . $row->apellido_paterno . ' ' . $row->apellido_materno;
            }
        }
        $html .= '</label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->nombre . ' ' . $informacion_cliente->row()->apellido_paterno . ' ' . $informacion_cliente->row()->apellido_materno . ' ' . $nomCopops . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>SE COMPROMETE A ADQUIRIR:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->nombreLote . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EN EL CLÚSTER:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->nombreCondominio . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DE SUP APROX:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->sup . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NO. REFERENCIA PAGO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->referencia . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>COSTO POR M<sup>2</sup> LISTA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $costoM2 . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>COSTO POR M<sup>2</sup> FINAL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->costom2f . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>UNA VEZ QUE SEA AUTORIZADO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->proyecto . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EN EL MUNICIPIO DE:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->municipio2 . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>LA UBICACIÓN DE LOTE PUEDE VARIAR DEBIDO A AJUSTES DEL PROYECTO
                </label><br><br><br>
            </td>
            </tr>


            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>IMPORTE DE LA OFERTA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->importOferta . '</b><br>
            </td>
            <td width="75%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>IMPORTE EN LETRA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->letraImport . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label><label>El ofertante como garantía de seriedad de la operación, entrega en este momento la cantidad de $: </label><b>' . $informacion_cliente->row()->cantidad . '</b> <b> ( ' . $informacion_cliente->row()->letraCantidad . ' ), </b> misma que se aplicará a cuenta del precio al momento de celebrar el contrato definitivo.El ofertante manifiesta que es su voluntad seguir aportando cantidades a cuenta de la siguiente forma:</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>SALDO DE DEPÓSITO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->saldoDeposito . '</b><br>
            </td>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>APORTACIÓN MENSUAL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->aportMensualOfer . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA 1° APORTACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->fecha1erAport . '</b><br>
            </td>
            <td width="10%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>PLAZO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->plazo . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA LIQUIDACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->fechaLiquidaDepo . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA 2° APORTACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->fecha2daAport . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label align="justify">Esta oferta tendrá una vigencia de 180 (ciento ochenta) días naturales. Dicho lapso de tiempo será para la firma del contrato privado el cual contendrá entre otras cláusulas, los términos y condiciones suspensivas que regulan esta oferta. En caso de no llevarse a cabo la firma del contrato, todo compromiso u obligación quedará sin efectos. En caso de que el ofertante realizara alguna aportación con cheque, éste será recibido salvo buen cobro y en el supuesto de que no fuera cobrable el título, esta operación también quedará sin efectos. En caso de cancelarse la presente operación o de no firmarse el contrato en el lapso arriba mencionado, la empresa cobrará al ofertante únicamente $10,000.00 (Diez mil pesos 00/100 m.n.) que cubren parcialmente los gastos generados por la operación. Que el ofertante sabe que como consecuencia de la modificación del proyecto por parte del desarrollador o de las autorizaciones definitivas emitidas por el Municipio correspondiente, la ubicación, la superficie, medidas y colindancias del lote señalado en el presente documento, así como la nomenclatura o el número definitivo de lotes del Desarrollo Inmobiliario, en el que se encuentra, puede variar, así mismo con motivo de ello, el lote puede sufrir afectaciones y/o servidumbres libres de construcción.Durante el periodo de contingencia derivado de la prevención contra el virus denominado COVID-19, la suscripción de éste Depósito de Seriedad, será documento suficiente para la formalización de la compraventa con la empresa titular del inmueble que por este medio adquiere el cliente. Una vez que se decrete el término del periodo de contingencia a que se hace referencia en el párrafo anterior, el comprador se compromete a suscribir el contrato de compraventa respectivo, mismo que le será entregado impreso en un periodo máximo de 60 (sesenta) días naturales, contados a partir del término del periodo de contingencia. De acuerdo a lo estipulado en el contrato de compraventa que habrá de suscribirse entre el comprador y el vendedor, la pena convencional en caso de que el comprador incumpla con cualquiera de sus obligaciones es del 25% (veinticinco por ciento) del precio total pactado. Una vez formalizada la compraventa y en caso de que el comprador solicite el envío del contrato de compraventa en forma digital, éste podrá ser solicitado a través de su asesor de ventas.</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>En el Municipio de <b>' . $informacion_cliente->row()->municipio2 . '</b> a <b>' . $informacion_cliente->row()->dia . '</b> del mes <b>' . $informacion_cliente->row()->mes . '</b> del año <b>' . $informacion_cliente->row()->anio . '</b>.</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <br><br><br><br><br> ';
        $nomCopops = '';
        if ($informacion_copropietarios->num_rows() > 0) {
            foreach ($informacion_copropietarios->result() as $row) {
                $nomCopops .= '/ ' . $row->nombre_cop . ' ' . $row->apellido_paterno . ' ' . $row->apellido_materno;
            }
        }
        $html .= '<td width="70%" align="center">' . $informacion_cliente->row()->nombre . ' ' . $informacion_cliente->row()->apellido_paterno . ' ' . $informacion_cliente->row()->apellido_materno . ' ' . $nomCopops . '
            <BR> ______________________________________________________________________________<p>Nombre y Firma / Ofertante</p><p>Acepto que se realice una verificación de mis datos, en los teléfonos<br> y correos que proporciono para el otorgamiento del crédito.</p>
            </td>

            <td width="30%" align="center"><label><b>REFERENCIAS PERSONALES</b>.</label>

            ';

        if ($informacion_referencias->num_rows() > 0) {
            foreach ($informacion_referencias->result() as $row) {
                $html .= '<br><p align="left">' . $row->nombre . ' - ' . $row->parentezco . ' - ' . $row->telefono . '</p>';
            }
        } else {
            $html .= '<br><p align="left">SIN REFERENCIAS PERSONALES</p>';
        }

        $html .= '</td></tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>OBSERVACIONES:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $informacion_cliente->row()->observacion . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>';

        /*print_r($asesor2[0]) ;
                						 exit;*/

        /**/
        if (count($asesor2) > 0) {
            $asesoresVC = '';
            $coordGerVC = '';
            for ($vc = 0; $vc < count($asesor2); $vc++) {
                if ($asesor2[0]->id_usuario == '' || $asesor[0]->id_usuario == null) {
                    $asesoresVC = '';
                    $coordGerVC = '';
                } else {
                    $coordFinal = ($asesor[0]->nombreCoordinador == $asesor2[0]->nombreCoordinador) ? '' : $asesor2[0]->nombreCoordinador;
                    $gerenteFinal = ($asesor[0]->nombreGerente == $asesor2[0]->nombreGerente) ? '' : $asesor2[0]->nombreGerente;

                    $coordinador = ($asesor2[0]->nombreCoordinador == '') ? '' : ' - ' . $coordFinal . ', ';
                    $gerente = ($asesor2[0]->nombreGerente == '') ? '' : $gerenteFinal;

                    ($asesor2[0]->nombreAsesor == '') ? $asesoresVC .= '' : $asesoresVC .= ' - ' . $asesor2[$vc]->nombreAsesor;

                    ($asesor2[0]->nombreCoordinador == '' AND $asesor2[0]->nombreGerente == '') ? $coordGerVC .= '' : $coordGerVC .= $coordinador . $gerente;
                }
            }

        } else {
            $asesoresVC = '';
            $coordGerVC = '';
        }


        $coordGerenteVN = '';
        if ($asesor[0]->nombreCoordinador == ' ') {

            $coordinadorVN = '';
        } else {
            $coordinadorVN = '- ' . $asesor[0]->nombreCoordinador . ', ';
        }
        if ($asesor[0]->nombreGerente == '') {
            $gerenteVN = '';
        } else {
            $gerenteVN = $asesor[0]->nombreGerente;
        }
        $coordGerenteVN = $coordinadorVN . $gerenteVN;


        if ($informacion_asesor->num_rows() > 0) {

            if ($informacion_asesor2->num_rows() > 0) {
                foreach ($informacion_asesor2->result() as $row) {
                    $valor .= $informacion_asesor2->row()->nombreAsesor . " - ";
                    $valo2 .= $informacion_asesor2->row()->nombreGerente . " - ";
                }


                $html .= '<tr><br><br><br><br><br> <td width="50%" align="center">' . $valor . $informacion_asesor->row()->nombreAsesor . $asesor2[0] . '<BR> ______________________________________________________________________________<p> <b>Nombre y Firma / Asesor</b></p></td>
                    <td width="50%" align="center">' . $valo2 . $informacion_asesor->row()->nombreGerente . $asesor[0] . '<BR> ______________________________________________________________________________<p> 
                    <b>Nombre y Firma / Autorización de operación</b></p>
                    </td></tr>';
            } else {
                $html .= '<tr><br><br><br><br><br> <td width="50%" align="center">' . $informacion_asesor->row()->nombreAsesor . '<BR> ______________________________________________________________________________<p> <b>Nombre y Firma / Asesor</b></p></td>
                    <td width="50%" align="center">' . $informacion_asesor->row()->nombreGerente . '<BR> ______________________________________________________________________________<p> 
                    <b>Nombre y Firma / Autorización de operación</b></p>
                    </td></tr>';
            }
        } else {
            $html .= '<br><p align="left">SIN REFERENCIAS PERSONALES</p>';
        }


        $html .= '<tr>
            <td width="100%" align="center">
            <table border="0" width="91%" style="background-color:#ffffff;">
            <tr>
            <td></td>
            </tr>
            </table>
            </td>
            </tr>

            </table>
            </td>


            </div>
            </div>
            </body>
            </html>';

        $pdf->writeHTMLCell(0, 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        ob_end_clean();
        $namePDF = $pdf->Output(utf8_decode('DEPÓSITO_DE_SERIEDAD.pdf'), 'I');
        $attachment = $pdf->Output(utf8_decode($namePDF), 'S');
    }


    public function editar_ds()
    {
        setlocale(LC_MONETARY, 'en_US');

        $array1 = $this->input->post("email_cop[]");
        $array2 = $this->input->post("telefono1_cop[]");
        $array3 = $this->input->post("telefono2_cop[]");
        $array4 = $this->input->post("fnacimiento_cop[]");
        $array5 = $this->input->post("nacionalidad_cop[]");
        $array6 = $this->input->post("originario_cop[]");

        $array7 = $this->input->post("id_particular_cop[]");
        $array8 = $this->input->post("ecivil_cop[]");
        $array9 = $this->input->post("conyuge_cop[]");
        $array10 = $this->input->post("r_matrimonial_cop[]");
        $array11 = $this->input->post("ocupacion_cop[]");
        $array12 = $this->input->post("puesto_cop[]");

        $array13 = $this->input->post("empresa_cop[]");
        $array14 = $this->input->post("antiguedad_cop[]");
        $array15 = $this->input->post("edadFirma_cop[]");
        $array16 = $this->input->post("dom_emp_cop[]");

        $array17 = $this->input->post("id_cop[]");
        $array19 = $this->input->post("rfc_cop[]");
        $array20 = $this->input->post("regimen_fac[]");
        $numOfCoprops = $this->input->post('numOfCoprops');

        $cm = (empty($this->input->post('especificar'))) ? '11' : $this->input->post('especificar');

        if ($numOfCoprops > 0) {
            for ($i = 0; $i < $numOfCoprops; $i++) {
                if ($this->input->post("tipo_vivienda_cop" . $i) == null ||
                    $this->input->post("tipo_vivienda_cop" . $i) == '' ||
                    empty($this->input->post("tipo_vivienda_cop" . $i))
                ) {
                    $array18[$i] = 5;
                } else {
                    $array18[$i] = $this->input->post("tipo_vivienda_cop" . $i . "[]");
                }
            }
        }


        if ($this->input->post("id_cop[]")) {
            for ($i = 0; $i <= 5; $i++) {
                $valor_coprop = '<input type="radio" name="tipoLote" id="tipoLote" value="1" checked="checked" readonly> yeii';
            }
        } else {
            $valor_coprop = '<input type="radio" name="tipoLote" id="tipoLote" value="1" checked="checked" readonly> Lote';
        }


        $clave_folio = $this->input->post('clavevalor');
        $id_cliente = $this->input->post('id_cliente');

        $desarrollo = $this->input->post('desarrollo');
        $tipoLote = $this->input->post('tipoLote_valor');


        $asesor_datos = $this->input->post('asesor_datos');
        $gerente_datos = $this->input->post('gerente_datos');

        //VALORES SELECT
        $nac_select = $this->input->post('nacionalidad');
        $ecivil_select = $this->input->post('estado_civil');
        $regimen_select = $this->input->post('regimen_matrimonial');
        $regifis_select = $this->input->post('regimenFiscal');

        $parentezco_select1 = $this->input->post('parentezco_select1');
        $parentezco_select2 = $this->input->post('parentezco_select2');

        $catalogs = $this->Asesor_model->getCatalogs()->result_array();
        $arrayobj1 = new ArrayObject();
        $arrayobj2 = new ArrayObject();
        $arrayobj3 = new ArrayObject();
        $arrayobj4 = new ArrayObject();
        //$arrayobj4 = new ArrayObject();
        for ($i = 0; $i < count($catalogs); $i++) {
            if ($catalogs[$i]["id_catalogo"] == 11)  // MJ: NACIONALIDAD
                $arrayobj1->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 18) // MJ: ESTADO CIVIL
                $arrayobj2->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 19) // MJ: REGIMEN MATRIMONIAL
                $arrayobj3->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            else if ($catalogs[$i]["id_catalogo"] == 88) // UR: REGIMEN FISCAL
                $arrayobj4->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));
            /*else if ($catalogs[$i]["id_catalogo"] == 26) // MJ: PARENTESCO
                    $arrayobj4->append(array('id_opcion' => $catalogs[$i]["id_opcion"], 'nombre' => $catalogs[$i]["nombre"]));*/
        }
        $nacionalidades2 = $arrayobj1;
        $edoCivil = $arrayobj2;
        $regMat = $arrayobj3;
        $regFiscal2 = $arrayobj4;
        for ($n = 0; $n < count($nacionalidades2); $n++) {
            if ($nacionalidades2[$n]['id_opcion'] == $nac_select) {
                $nac_select_II = $nacionalidades2[$n]['nombre'];
            }
        }
        for ($n = 0; $n < count($edoCivil); $n++) {
            if ($edoCivil[$n]['id_opcion'] == $ecivil_select) {
                $est_vic = $edoCivil[$n]['nombre'];
            }
        }
        // for ($n = 0; $n < count($regFiscal); $n++) {
        //     if ($regFiscal[$n]['id_opcion'] == $ecivil_select) {
        //         $est_vic = $regFiscal[$n]['nombre'];
        //     }
        // }
        for ($c = 0; $c < count($regMat); $c++) {
            if ($regMat[$c]['id_opcion'] == $regimen_select) {
                $reg_ses = $regMat[$c]['nombre'];
            }
        }
        for ($c = 0; $c < count($regFiscal2); $c++){
            if ($regFiscal2[$c]['id_opcion'] == $regifis_select) {
                $reg_fis = $regFiscal2[$c]['nombre'];
            }
        }


        //DOCUMENTACIÓN
        //PERSONA FISICA
        $idOficial_pf = $this->input->post('idOficial_pf');
        $idDomicilio_pf = $this->input->post('idDomicilio_pf');
        $actaMatrimonio_pf = $this->input->post('actaMatrimonio_pf');

        //PERSONA MORAL
        $poder_pm = $this->input->post('poder_pm');
        $actaConstitutiva_pm = $this->input->post('actaConstitutiva_pm');
        $idOficialApoderado_pm = $this->input->post('idOficialApoderado_pm');
        $rfc = $this->input->post('rfc');

        $nombre = $this->input->post('nombre');
        $apellido_paterno = $this->input->post('apellido_paterno');
        $apellido_materno = $this->input->post('apellido_materno');

        $telefono1 = $this->input->post('telefono1');//telefono casa
        $telefono2 = $this->input->post('telefono2');//telefono celular
        $correo = $this->input->post('correo');

        $fecha_nacimiento = $this->input->post('fecha_nacimiento');
        $nacionalidad = $this->input->post('nacionalidad');
        $originario = $this->input->post('originario');
        $regimen_fac = $this->input->post('regimenFiscal');
        $cp_fac = $this->input->post('cp_fac');
        
        $estado_civil = $this->input->post('estado_civil');
        $nombre_conyuge = $this->input->post('nombre_conyuge');
        $regimen_matrimonial = $this->input->post('regimen_matrimonial');

        $domicilio_particular = $this->input->post('domicilio_particular');

        $ocupacion = $this->input->post('ocupacion');
        $empresa = $this->input->post('empresa');
        $puesto = $this->input->post('puesto');
        $antiguedad = $this->input->post('antiguedad');

        $edadFirma = $this->input->post('edadFirma');
        $domicilio_empresa = $this->input->post('domicilio_empresa');
        $telefono_empresa = $this->input->post('telefono_empresa');

        $tipo_vivienda = $this->input->post('tipo_vivienda');

        $costoM2 = str_replace(',','',$this->input->post('costoM2'));
        $costoM2 = str_replace('$','', $costoM2);
        $costom2f = str_replace(',','',$this->input->post('costom2f'));
        $costom2f = str_replace('$','', $costom2f);
        $proyecto = $this->input->post('proyecto');
        $municipioDS = $this->input->post('municipioDS');

        $importOferta = str_replace(',','',$this->input->post('importOferta'));
        $importOferta = str_replace('$','', $importOferta);
        $letraImport = $this->input->post('letraImport');

        $cantidad = str_replace(',','',$this->input->post('cantidad'));
        $cantidad = str_replace('$','', $cantidad);
        $letraCantidad = $this->input->post('letraCantidad');

        $saldoDeposito = str_replace(',','',$this->input->post('saldoDeposito'));
        $saldoDeposito = str_replace('$','', $saldoDeposito);
        $aportMensualOfer = $this->input->post('aportMensualOfer');
        $aportMensualOfer = str_replace('$','', $aportMensualOfer);
        $fecha1erAport = $this->input->post('fecha1erAport');
        $plazo = $this->input->post('plazo');
        $fechaLiquidaDepo = $this->input->post('fechaLiquidaDepo');
        $fecha2daAport = $this->input->post('fecha2daAport');

        $municipio2 = $this->input->post('municipio2');
        $dia = $this->input->post('dia');
        $mes = $this->input->post('mes');
        $anio = $this->input->post('anio');

        $nombre1 = $this->input->post('nombre1');
        $nombre2 = $this->input->post('nombre2');
        $parentesco1 = $this->input->post('parentesco1');
        $parentesco2 = $this->input->post('parentesco2');
        $telefono_referencia1 = $this->input->post('telefono_referencia1');
        $telefono_referencia2 = $this->input->post('telefono_referencia2');

        $observacion = $this->input->post('observacion');

        $nombreLote = $this->input->post('nombreLote');
        $nombreCondominio = $this->input->post('nombreCondominio');
        $sup = str_replace(',','',$this->input->post('sup'));
        $sup = str_replace('$','', $sup);
        $referencia = $this->input->post('referencia');

        $id_referencia1 = $this->input->post('id_referencia1');
        $id_referencia2 = $this->input->post('id_referencia2');
        $tipo_nc = $this->input->post('tipoNc_valor');
        $printPagare = $this->input->post('imprimePagare');
        $tipo_comprobante = $this->input->post('tipo_comprobante');

        //revisar si coloco que no quiere la carta, revisar si hay un registro en el arbol
        //si es así hayq ue borrar la rama ya que no la estará utilizando
        if($tipo_comprobante == 2){ //ha eligido que no, hay que borrar la rama y el archivo el archivo
            $dcv = $this->Asesor_model->informacionVerificarCliente($id_cliente);
            $revisar_registro = $this->Asesor_model->revisarCartaVerif($id_cliente,  29);

            if(count($revisar_registro)>0){
                $ubicacion = getFolderFile($revisar_registro[0]['tipo_doc']);
                $filename = $revisar_registro[0]['expediente'];
                //revisar si hay algun documento
                $array_key = array("idDocumento" => $revisar_registro[0]['idDocumento']);
                $tabla = 'historial_documento';
                if($filename=='' || $filename==NULL){
                    $borrar_archivo = $this->General_model->deleteRecord($tabla, $array_key);
                }else{
                    $eliminar_archivo = delete_img($ubicacion, $filename);
                    $borrar_archivo = $this->General_model->deleteRecord($tabla, $array_key);
                }
            }
        }
        /*****MARTHA DEBALE OPTION*******/
        //$descuento_mdb = $this->input->post('descuento_mdb');
        /*************/
        $des_casa = $this->input->post('des_hide');


        //ARRAY DEPOSITO DE SERIEDAD
        $arreglo_ds = array();
        $arreglo_ds["clave"] = $clave_folio;
        $arreglo_ds["desarrollo"] = $desarrollo;
        $arreglo_ds["tipoLote"] = $tipoLote;
        $arreglo_ds["idOficial_pf"] = $idOficial_pf;
        $arreglo_ds["idDomicilio_pf"] = $idDomicilio_pf;
        $arreglo_ds["actaMatrimonio_pf"] = $actaMatrimonio_pf;
        $arreglo_ds["actaConstitutiva_pm"] = $actaConstitutiva_pm;
        $arreglo_ds["idOficialApoderado_pm"] = $idOficialApoderado_pm;

        if ($des_casa == 1) {
            $arreglo_ds["costoM2_casas"] = $costoM2;
        } else {
            $arreglo_ds["costoM2"] = $costoM2;
        }


        $arreglo_ds["costom2f"] = $costom2f;
        $arreglo_ds["proyecto"] = $proyecto;
        $arreglo_ds["municipio"] = $municipioDS;
        $arreglo_ds["importOferta"] = $importOferta;
        $arreglo_ds["letraImport"] = $letraImport;
        $arreglo_ds["cantidad"] = $cantidad;
        $arreglo_ds["letraCantidad"] = $letraCantidad;
        $arreglo_ds["saldoDeposito"] = $saldoDeposito;
        $arreglo_ds["aportMensualOfer"] = $aportMensualOfer;
        $arreglo_ds["fecha1erAport"] = $fecha1erAport;
        $arreglo_ds["plazo"] = $plazo;
        $arreglo_ds["fechaLiquidaDepo"] = $fechaLiquidaDepo;
        $arreglo_ds["fecha2daAport"] = $fecha2daAport;
        $arreglo_ds["municipio2"] = $municipio2;
        $arreglo_ds["dia"] = $dia;
        $arreglo_ds["mes"] = $mes;
        $arreglo_ds["anio"] = $anio;
        $arreglo_ds["observacion"] = $observacion;
        $arreglo_ds['modificado_por'] = $this->session->userdata('id_usuario');


        //ARRAY DATOS CLIENTE
        $arreglo_cliente = array();
        $arreglo_cliente["nombre"] = $nombre;
        $arreglo_cliente["apellido_paterno"] = $apellido_paterno;
        $arreglo_cliente["apellido_materno"] = $apellido_materno;
        $arreglo_cliente["telefono1"] = $telefono1;
        $arreglo_cliente["telefono2"] = $telefono2;
        $arreglo_cliente["correo"] = $correo;
        $arreglo_cliente["rfc"] = $rfc;
        $arreglo_cliente["fecha_nacimiento"] = $fecha_nacimiento;
        $arreglo_cliente["nacionalidad"] = $nacionalidad;
        $arreglo_cliente["regimen_fac"] = $regimen_fac;
        $arreglo_cliente["cp_fac"] = $cp_fac;
        $arreglo_cliente["originario_de"] = $originario;
        $arreglo_cliente["estado_civil"] = $estado_civil;
        $arreglo_cliente["nombre_conyuge"] = $nombre_conyuge;
        $arreglo_cliente["regimen_matrimonial"] = $regimen_matrimonial;
        $arreglo_cliente["domicilio_particular"] = $domicilio_particular;
        $arreglo_cliente["ocupacion"] = $ocupacion;
        $arreglo_cliente["nombre_conyuge"] = $nombre_conyuge;
        $arreglo_cliente["empresa"] = $empresa;
        $arreglo_cliente["puesto"] = $puesto;
        $arreglo_cliente["antiguedad"] = $antiguedad;
        $arreglo_cliente["edadFirma"] = $edadFirma;
        $arreglo_cliente["domicilio_empresa"] = $domicilio_empresa;
        $arreglo_cliente["telefono_empresa"] = $telefono_empresa;
        $arreglo_cliente["tipo_vivienda"] = $tipo_vivienda;
        $arreglo_cliente["regimen_matrimonial"] = $regimen_matrimonial;
        $arreglo_cliente["modificado_por"] = $this->session->userdata('id_usuario');
        $arreglo_cliente["tipo_nc"] = $tipo_nc;
        $arreglo_cliente["printPagare"] = $printPagare;
        $arreglo_cliente["tipo_comprobanteD"] = $tipo_comprobante;
        //$arreglo_cliente['lugar_prospeccion'] = $cm;
//        $arreglo_cliente["descuento_mdb"] = $descuento_mdb;

        //ARRAY REFERENCIAS
        $arreglo_referencia1 = array();
        $arreglo_referencia1["nombre"] = $nombre1;
        $arreglo_referencia1["telefono"] = $telefono_referencia1;
        $arreglo_referencia1["parentesco"] = $parentesco1;
        $arreglo_referencia2 = array();
        $arreglo_referencia2["nombre"] = $nombre2;
        $arreglo_referencia2["telefono"] = $telefono_referencia2;
        $arreglo_referencia2["parentesco"] = $parentesco2;
        /////////////////////////////////////////////////////////////////////////////////////////

        $tpl1 = '';
        $tpl2 = '';
        if ($this->input->post('tipoLote_valor')) {
            $arreglo_ds["tipoLote"] = $tipoLote;

            if ($this->input->post('tipoLote_valor') == 0 || $this->input->post('tipoLote_valor') == '0') {
                $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="0" checked="checked" readonly> Lote';
                $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" readonly> Lote Comercial';
            }
            if ($this->input->post('tipoLote_valor') == 1 || $this->input->post('tipoLote_valor') == '1') {
                $tpl1 = '<input type="radio" name="tipoLote" id="tipoLote" value="0" readonly> Lote';
                $tpl2 = '<input type="radio" name="tipoLote" id="tipoLote" value="1" checked="checked" readonly> Lote Comercial';
            }
        } else if (!$this->input->post('tipoLote_valor')) {
            $arreglo_ds["tipoLote"] = '0';
        }


        /////////////////////////////////////////////////////////////////////////////////////////


        if ($this->input->post('desarrollo')) {
            $arreglo_ds["desarrollo"] = $desarrollo;

            if ($this->input->post('desarrollo') == 1) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" checked="checked" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            }
            if ($this->input->post('desarrollo') == 2) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" checked="checked" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            }
            if ($this->input->post('desarrollo') == 3) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" checked="checked" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            }
            if ($this->input->post('desarrollo') == 4) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" checked="checked" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" readonly> Mérida';
            }
            if ($this->input->post('desarrollo') == 5) {
                $d1 = '<input type="radio" name="desarrollo" id="desarrollo" value="1" readonly> Queretaro';
                $d2 = '<input type="radio" name="desarrollo" id="desarrollo" value="2" readonly> Leon';
                $d3 = '<input type="radio" name="desarrollo" id="desarrollo" value="3" readonly> Celaya';
                $d4 = '<input type="radio" name="desarrollo" id="desarrollo" value="4" readonly> San Luis Potosí';
                $d5 = '<input type="radio" name="desarrollo" id="desarrollo" value="5" checked="checked" readonly> Mérida';
            }
        } else if (!$this->input->post('desarrollo')) {
            $arreglo_ds["desarrollo"] = '0';
        }

        /////////////////////////////////////////////////////////////////////////////////////////

        if ($this->input->post('idOficial_pf')) {
            $arreglo_ds["idOficial_pf"] = $idOficial_pf;
            $id_identificacion = '<input type="checkbox" readonly name="idOficial_pf" id="idOficial_pf" value="1" checked="checked"> Identificación&nbsp;Oficial';
        } else if (!$this->input->post('idOficial_pf')) {
            $id_identificacion = '<input type="checkbox" readonly name="idOficial_pf" id="idOficial_pf" value="1"> Identificación&nbsp;Oficial';
            $arreglo_ds["idOficial_pf"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($this->input->post('idDomicilio_pf')) {
            $arreglo_ds["idDomicilio_pf"] = $idDomicilio_pf;
            $id_domicilio = '<input type="checkbox" readonly name="idDomicilio_pf" id="idDomicilio_pf" value="1" checked="checked"> Comprobante&nbsp;de&nbsp;Domicilio';
        } else if (!$this->input->post('idDomicilio_pf')) {
            $id_domicilio = '<input type="checkbox" readonly name="idDomicilio_pf" id="idDomicilio_pf" value="1"> Comprobante&nbsp;de&nbsp;Domicilio';
            $arreglo_ds["idDomicilio_pf"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($this->input->post('actaMatrimonio_pf')) {
            $arreglo_ds["actaMatrimonio_pf"] = $actaMatrimonio_pf;
            $id_acta_m = '<input type="checkbox" readonly name="actaMatrimonio_pf" id="actaMatrimonio_pf" value="1" checked="checked"> Acta&nbsp;de&nbsp;Matrimonio';
        } else if (!$this->input->post('actaMatrimonio_pf')) {
            $id_acta_m = '<input type="checkbox" readonly name="actaMatrimonio_pf" id="actaMatrimonio_pf" value="1"> Acta&nbsp;de&nbsp;Matrimonio';
            $arreglo_ds["actaMatrimonio_pf"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($this->input->post('actaConstitutiva_pm')) {
            $arreglo_ds["actaConstitutiva_pm"] = $actaConstitutiva_pm;
            $id_acta_c = '<input type="checkbox" readonly name="actaConstitutiva_pm" id="actaConstitutiva_pm" value="1" checked="checked"> Acta&nbsp;Constitutiva';
        } else if (!$this->input->post('actaConstitutiva_pm')) {
            $id_acta_c = '<input type="checkbox" readonly name="actaConstitutiva_pm" id="actaConstitutiva_pm" value="1"> Acta&nbsp;Constitutiva';
            $arreglo_ds["actaConstitutiva_pm"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($this->input->post('poder_pm')) {
            $arreglo_ds["poder_pm"] = $poder_pm;
            $id_poder = '<input type="checkbox" readonly name="poder_pm" id="poder_pm" value="1" checked="checked"> Poder';
        } else if (!$this->input->post('poder_pm')) {
            $id_poder = '<input type="checkbox" readonly name="poder_pm" id="poder_pm" value="1"> Poder';
            $arreglo_ds["poder_pm"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------
        if ($this->input->post('idOficialApoderado_pm')) {
            $arreglo_ds["idOficialApoderado_pm"] = $idOficialApoderado_pm;
            $id_apoderado = '<input type="checkbox" readonly name="idOficialApoderado_pm" id="idOficialApoderado_pm" value="1" checked="checked"> Identificación&nbsp;Oficial&nbsp;Apoderado';
        } else if (!$this->input->post('idOficialApoderado_pm')) {
            $id_apoderado = '<input type="checkbox" readonly name="idOficialApoderado_pm" id="idOficialApoderado_pm" value="1"> Identificación&nbsp;Oficial&nbsp;Apoderado';
            $arreglo_ds["idOficialApoderado_pm"] = '0';
        }
        //----------------------------------------------------------------------------------------------------------

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////

        if ($this->input->post('tipo_vivienda')) {
            $arreglo_cliente["tipo_vivienda"] = $tipo_vivienda;

            if ($this->input->post('tipo_vivienda') == 1) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" checked="checked" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
            }
            if ($this->input->post('tipo_vivienda') == 2) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" checked="checked" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
            }
            if ($this->input->post('tipo_vivienda') == 3) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" checked="checked" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
            }
            if ($this->input->post('tipo_vivienda') == 4) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" checked="checked" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO';
            }
            if ($this->input->post('tipo_vivienda') == 5) {
                $tv1 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly> PROPIA';
                $tv2 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA';
                $tv3 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE';
                $tv4 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR';
                $tv5 = '<input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" checked="checked" readonly> OTRO';
            }
        } else if (!$this->input->post('tipo_vivienda')) {
            $arreglo_cliente["tipo_vivienda"] = '0';
        }


        if ($this->input->post('pdfOK') != null || $this->input->post('pdfOK') == '0') {


            //CONVERTIMOS A ARREGLO TANTO LOS DESCUENTOS ACTUALES COMO EL NUEVO A AGREGAR
            $arrayCorreo = explode(",", $correo);/*$correo)*/

            // CHECAMOS SI EN EL ARREGLO NO HAY POSICIONES VACIAS Y LAS ELIMINAMOS
            $listCheckVacio = array_filter($arrayCorreo, "strlen");

            //VERIFICAMOS QUE NUESTRO ARREGLO NO TENGA DATOS REPETIDOS
            $arrayCorreoNotRepeat = array_unique($listCheckVacio);

            //EL ARREGLO FINAL LO CONVERTIMOS A STRING
            // $resCorreo = implode( ",", $arrayCorreoNotRepeat);

            $this->load->library('Pdf');
            $pdf = new TCPDF('P', 'mm', 'LETTER', 'UTF-8', false);
            // $pdf = new TCPDF('P', 'mm', 'LETTER', 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Sistemas María José Martínez Martínez');
            $pdf->SetTitle('DEPÓSITO DE SERIEDAD');
            $pdf->SetSubject('CONSTANCIA DE RELACION EMPRESA TRABAJADOR');
            $pdf->SetKeywords('CONSTANCIA, CIUDAD MADERAS, RELACION, EMPRESA, TRABAJADOR');
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->setFontSubsetting(true);
            $pdf->SetFont('Helvetica', '', 9, '', true);
            $pdf->SetMargins(15, 15, 15, true);

            $pdf->AddPage('P', 'LEGAL');

            $pdf->SetFont('Helvetica', '', 5, '', true);

            $pdf->SetFooterMargin(0);
            $bMargin = $pdf->getBreakMargin();
            $auto_page_break = $pdf->getAutoPageBreak();
            $pdf->Image('static/images/ar4c.png', 120, 15, 300, 0, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);
            $pdf->setPageMark();

        $html = '<!DOCTYPE html>
            <html lang="en">
            <head>
            <link rel="shortcut icon" href="' . base_url() . 'static/images/arbol_cm.png" />
            <link href="<?=base_url()?>dist/css/bootstrap.min.css" rel="stylesheet" />
            <!--  Material Dashboard CSS    -->
            <link href="<?=base_url()?>dist/css/material-dashboard.css" rel="stylesheet" />
            <!--  CSS for Demo Purpose, don\'t include it in your project     -->
            <link href="<?=base_url()?>dist/css/demo.css" rel="stylesheet" />
            <!--     Fonts and icons     -->
            <link href="<?=base_url()?>dist/css/font-awesome.css" rel="stylesheet" />
            <link href="<?=base_url()?>dist/css/google-roboto-300-700.css" rel="stylesheet" />
            <style>
            body{color: #084c94;}
            .espacio{padding: 5%;}
            .espaciodos{padding: 10%;} 
            h2{font-weight: bold;color: #084c94;}
            .save {display:scroll;position:fixed;bottom:225px;right:17px;z-index: 3;}
            p{color: #084c94;}
            .col-xs-16 {width: 3px;float: left;}
            .col-xs-17 {width: 16%;float: left;}
            #imagenbg {position: relative;top:1500px;z-index: -1;}
            #fichadeposito {position: absolute;z-index: 2;}
            .mySectionPdf
            {
                padding: 20px;
            }
            .form-group.is-focused .form-control 
            {
                outline: none;
                background-image: linear-gradient(#0c63c5, #177beb), linear-gradient(#D2D2D2, #D2D2D2);
                background-size: 100% 2px, 100% 1px;
                box-shadow: none;
                transition-duration: 0.3s;
            }
            b
            {
                font-size: 8px;
            }
            </style>
            </head>

            <body>
            <div id="fichadeposito" name="fichadeposito" class="fichadeposito">
            <div id="muestra">
            <table border="0" width="100%" id="tabla" align="center">
            <tr>
            
            <td width="70%" align="left">
             <label>
            <h1 style="margin-right: 50px;"> DEPÓSITO DE SERIEDAD</h1>
            </label>
            </td>

            <td align="right" width="15%">
            <br><br><br>
            <p style="margin-right: 2px;">FOLIO</p>
            </td>

            <td width="15%" style="border-bottom:1px solid #CCCCCC">
            <p style="color: red;font-size:14px;">' . $clave_folio . '</p>
            </td>

            </tr>
            </table>

            <table border="0" width="100%" align="" align="">
            <tr>
            <th rowspan="4" width="283" align="left">
            <img src="' . base_url() . '/static/images/CMOF.png" alt="Servicios Condominales" title="Servicios Condominales" style="width: 250px"/>
            </th>

            <td width="367">
            <h5><p style="font-size:9px;"><strong>DESARROLLO:</strong></p></h5>
            </td>
            </tr>

            <tr>
            <td width="367">
            <table border="0" width="100%">
            <tr>
            <td width="20%">' . $d1 . '</td>
            <td width="20%">' . $d2 . '</td>
            <td width="20%">' . $d3 . '</td>
            <td width="20%">' . $d4 . '</td>
            <td width="20%">' . $d5 . '</td>
            </tr>

            <tr>
            <td width="20%">' . $tpl1 . '</td>
            <td width="20%">' . $tpl2 . '</td>
            <td width="20%"></td>
            <td width="20%"></td>
            <td width="20%"></td>
            </tr>

            </table>
            </td>
            </tr>

            <tr>
            <td>
            <h5><p style="font-size:9px;"><strong>DOCUMENTACIÓN ENTREGADA:</strong></p></h5>
            </td>
            </tr>

            <tr>
            <td>
            <table border="0" width="100%">
            <tr>
            <td width="19 %"><p><strong>Personas&nbsp;Físicas</strong></p></td>
            <td width="23%">' . $id_identificacion . '</td>
            <td width="27%">' . $id_domicilio . '</td>
            <td width="29%" colspan="2">' . $id_acta_m . '</td>
            </tr>

            <tr>
            <td width="19%"><p><strong>Personas&nbsp;Morales</strong></p></td>
            <td width="23%">' . $id_acta_c . '</td>
            <td width="27%">' . $id_poder . '</td>
            <td width="29%" colspan="2">' . $id_apoderado . '</td>
            </tr>';
             
            if ($informacion_cliente->row()->rfc != '' && $informacion_cliente->row()->rfc != null){
            $html .= '<tr>    
            <tr>
            <td></td>
            <td width="15%"><b>RFC:</b> ' . $rfc . '</td>
            <td width="50%"><b>REF:</b> ' . $reg_fis . '</td>
            <td width="23%"><b>CP:</b> ' . $cp_fac . '</td>
            <td width="27%"></td>
            <td width="29%" colspan="2"></td>
            </tr>';
            }

            $html .= '</table>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2">
            <br>
            </td>
            </tr>
            
            <tr>
            <td width="40%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>NOMBRE(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $nombre . ' <br></b>
            </td>
            <td width="30%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>APELLIDO PATERNO(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $apellido_paterno . ' <br></b>
            </td>
            <td width="30%" colspan="2" style="border-bottom: 1px solid #CCCCCC; margin: 0px 0px 150px 0px">
            <label>APELLIDO MATERNO(<b><span style="color: red;">*</span></b>):</label><br><br><b>&nbsp;' . $apellido_materno . ' <br></b>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>

            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>TELÉFONO CASA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $telefono1 . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>CELULAR (<b><span style="color: red;">*</span></b>) </label><br><br><b>&nbsp;' . $telefono2 . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label> EMAIL (<b><span style="color: red;">*</span></b>)
                </label><br><br><b>&nbsp;' . $correo . '</b><br>
            </td>
            </tr>
            
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA DE NACIMIENTO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $fecha_nacimiento . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NACIONALIDAD:
                </label><br><br><b>&nbsp;' . $nac_select_II . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ORIGINARIO DE:
                </label><br><br><b>&nbsp;' . $originario . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ESTADO CIVIL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $est_vic . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NOMBRE CONYUGE:
                </label><br><br><b>&nbsp;' . $nombre_conyuge . '</b><br>
            </td>
            <td width="33.3%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>RÉGIMEN:
                </label><br><br><b>&nbsp;' . $reg_ses . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DOMICILIO PARTICULAR:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $domicilio_particular . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>OCUPACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $ocupacion . '</b><br>
            </td>
            <td width="35%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EMPRESA EN LA QUE TRABAJA:
                </label><br><br><b>&nbsp;' . $empresa . '</b><br>
            </td>
            <td width="35%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>PUESTO:
                </label><br><br><b>&nbsp;' . $puesto . '</b><br>
            </td>
            <td width="10%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>ANTIGÜEDAD:
                </label><br><br><b>&nbsp;' . $antiguedad . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EDAD:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $edadFirma . '</b><br>
            </td>
            <td width="70%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DOMICILIO EMPRESA:
                </label><br><br><b>&nbsp;' . $domicilio_empresa . '</b><br>
            </td>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>TELÉFONO EMPRESA:
                </label><br><br><b>&nbsp;' . $telefono_empresa . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
 
            <tr>
            <td width="15%" colspan="2"><b>VIVE EN CASA:</b></td>
            <td width="10%">' . $tv1 . '</td>
            <td width="10%">' . $tv2 . '</td>
            <td width="10%">' . $tv3 . '</td>
            <td width="10%">' . $tv4 . '</td>
            <td width="10%">' . $tv5 . '<br><br></td>
            </tr>

            <tr >
                <td width="100%" style="border-top:1px solid #CCC">
                <br><br>
                    <b style="font-size:1.5em;">CO - PROPIETARIOS:</b><br>
                    ';
            $copropiedad = $this->Asesor_model->selectDSCopropiedad($id_cliente);
            $datos["copropiedadTotal"] = $this->Asesor_model->selectDSCopropiedadCount($id_cliente);
            $limite = $datos["copropiedadTotal"][0]->valor_propietarios;
            $nacionalidades = $this->Asesor_model->getNationality()->result_array();
            $edoCivil = $this->Asesor_model->getCivilStatus()->result_array();
            $regMat = $this->Asesor_model->getMatrimonialRegime()->result_array();
            $regFiscal = $this->Asesor_model->getFiscalRegime()->result_array();
            $parentescos = $this->Asesor_model->getParentesco()->result_array();
            if ($limite > 0) {
                $coprops_ds = '';
                for ($i = 0; $i < $limite; $i++) {
                    $coprops_ds .= '/ ' . $copropiedad[$i]->nombre_cop . ' ' . $copropiedad[$i]->apellido_paterno . ' ' . $copropiedad[$i]->apellido_materno;
                }
            }

            if ($limite > 0) {


                for ($i = 0; $i < $limite; $i++) {
                    $html .= '<center><br><br><label style="font-size: 1.5em; color:#0A548B;"><b>PROPIETARIO: ' . ($i + 1) . '</b></label></center><br><br>
                    <table style="font-size: 1.2em; border-bottom: 1px solid #CCC">
                    <tr>
                        <td ><label>Nombre: </label><br><b>' . $copropiedad[$i]->nombre_cop . ' ' . $copropiedad[$i]->apellido_paterno . ' ' . $copropiedad[$i]->apellido_materno . '</b></td>
                        <td ><label>Email: </label><br><b>' . $array1[$i] . '</b></td>
                        <td ><label>Tel. Casa: </label><br><b>' . $array2[$i] . '</b></td>
                        <td ><label>Celular: </label><br><b>' . $array3[$i] . '</b></td>
                        <td ><label>Fecha Nac:</label><br><b>' . $array4[$i] . '</b></td>
                        <td ><label>Nacionalidad: </label><br><b>';
                    for ($n = 0; $n < count($nacionalidades); $n++) {
                        if ($nacionalidades[$n]['id_opcion'] == $array5[$i]) {
                            $html .= $nacionalidades[$n]['nombre'];
                        }
                    }

                    $html .= '</b><br><br></td>
                    </tr>
                    <tr>
                        <td colspan="2"><label>Originario de: </label><br><b>' . $array6[$i] . '</b></td>
                        <td><label>Domicilio particular: </label><br><b>' . $array7[$i] . '</b></td>
                        <td><label>Estado Civil: </label><br><b>';
                    for ($n = 0; $n < count($edoCivil); $n++) {
                        if ($edoCivil[$n]['id_opcion'] == $array8[$i]) {
                            $html .= $edoCivil[$n]['nombre'];
                        }
                    }
                    $html .= '</b></td>
                        <td><label>Nombre Conyugue: </label><br><b>' . $array9[$i] . '</b></td>
                        <td><label>Régimen: </label><br><b>';
                    for ($n = 0; $n < count($regMat); $n++) {
                        if ($regMat[$n]['id_opcion'] == $array10[$i]) {
                            $html .= $regMat[$n]['nombre'];
                        }
                    }
                    $html .= '</b><br><br></td>
                    </tr>
                    <tr>
                        <td><label>Ocupación: </label><br><b>' . $array11[$i] . '</b></td>
                        <td><label>Puesto: </label><br><b>' . $array12[$i] . '</b></td>
                        <td><label>Empresa Laboral: </label><br><b>' . $array13[$i] . '</b></td>
                        <td><label>Antigüedad: </label><br><b>' . $array14[$i] . '</b></td>
                        <td><label>Edad Firma: </label><br><b>' . $array15[$i] . '</b></td>
                        <td><label>Domicilio Empresa: </label><br><b>' . $array16[$i] . '<br></b></td>
                    </tr>
                    <tr>
                        <td width="80%"><label>Tipo vivienda: </label><br><b>';

                    if ($array18[$i] == 1) {

                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly checked="checked" readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO
                            ';
                    } elseif ($array18[$i] == 2) {
                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly checked="checked"> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO
                            ';
                    } elseif ($array18[$i] == 3) {
                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly checked="checked"> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO
                            ';
                    } elseif ($array18[$i] == 4) {
                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly checked="checked"> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO
                            ';
                    } elseif ($array18[$i] == 5) {
                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly checked="checked"> OTRO
                            ';
                    } else {
                        $html .= '
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="1" readonly readonly> PROPIA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="2" readonly> RENTADA
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="3" readonly> PAGÁNDOSE
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="4" readonly> FAMILIAR
                            <input type="radio" name="tipo_vivienda" id="tipo_vivienda" value="5" readonly> OTRO
                            ';
                    }


                    $html .= '</b></td>

                        <td><label>RFC: </label><br><b>' . $array19[$i] . '</b></td>
                        <td><label>Régimen Fiscal: </label><br><b>';
                        for ($n = 0; $n < count($regFiscal); $n++) {
                        if ($regFiscal[$n]['id_opcion'] == $array20[$i]) {
                            $html .= $regFiscal[$n]['nombre'];
                        }
                    }
                    $html .= '</b></td>
                    </tr>
                    </table>';
                }


            } else {

                echo '<table><tr><td><center>NO HAY CO - PROPIETARIOS</ceneter></td></tr></table>';


            }
            $html .= '
                    
                    
                </td>
            </tr>


 

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>El Sr.(a):
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno . ' ' . $coprops_ds . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>SE COMPROMETE A ADQUIRIR:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $nombreLote . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EN EL CLÚSTER:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $nombreCondominio . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>DE SUP APROX:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $sup . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>NO. REFERENCIA PAGO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $referencia . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>COSTO POR M<sup>2</sup> LISTA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $costoM2 . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>COSTO POR M<sup>2</sup> FINAL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $costom2f . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>UNA VEZ QUE SEA AUTORIZADO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $proyecto . '</b><br>
            </td>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>EN EL MUNICIPIO DE:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $municipio2 . '</b><br>
            </td>
            </tr>


          

            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>LA UBICACIÓN DE LOTE PUEDE VARIAR DEBIDO A AJUSTES DEL PROYECTO
                </label><br><br><br>
            </td>
            </tr>


           

            <tr>
            <td width="25%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>IMPORTE DE LA OFERTA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $importOferta . '</b><br>
            </td>
            <td width="75%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>IMPORTE EN LETRA:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $letraImport . '</b><br>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label><label>El ofertante como garantía de seriedad de la operación, entrega en este momento la cantidad de $: </label><b>' . $cantidad . '</b> <b> ( ' . $letraCantidad . ' ), </b> misma que se aplicará a cuenta del precio al momento de celebrar el contrato definitivo.El ofertante manifiesta que es su voluntad seguir aportando cantidades a cuenta de la siguiente forma:</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>SALDO DE DEPÓSITO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $saldoDeposito . '</b><br>
            </td>
            <td width="15%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>APORTACIÓN MENSUAL:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $aportMensualOfer . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA 1° APORTACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $fecha1erAport . '</b><br>
            </td>
            <td width="10%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>PLAZO:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $plazo . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA LIQUIDACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $fechaLiquidaDepo . '</b><br>
            </td>
            <td width="20%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>FECHA 2° APORTACIÓN:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $fecha2daAport . '</b><br>
            </td>
            </tr>



            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label align="justify">Esta oferta tendrá una vigencia de 180 (ciento ochenta) días naturales. Dicho lapso de tiempo será para la firma del contrato privado el cual contendrá entre otras cláusulas, los términos y condiciones suspensivas que regulan esta oferta. En caso de no llevarse a cabo la firma del contrato, todo compromiso u obligación quedará sin efectos. En caso de que el ofertante realizara alguna aportación con cheque, éste será recibido salvo buen cobro y en el supuesto de que no fuera cobrable el título, esta operación también quedará sin efectos. En caso de cancelarse la presente operación o de no firmarse el contrato en el lapso arriba mencionado, la empresa cobrará al ofertante únicamente $10,000.00 (Diez mil pesos 00/100 m.n.) que cubren parcialmente los gastos generados por la operación. Que el ofertante sabe que como consecuencia de la modificación del proyecto por parte del desarrollador o de las autorizaciones definitivas emitidas por el Municipio correspondiente, la ubicación, la superficie, medidas y colindancias del lote señalado en el presente documento, así como la nomenclatura o el número definitivo de lotes del Desarrollo Inmobiliario, en el que se encuentra, puede variar, así mismo con motivo de ello, el lote puede sufrir afectaciones y/o servidumbres libres de construcción.Durante el periodo de contingencia derivado de la prevención contra el virus denominado COVID-19, la suscripción de éste Depósito de Seriedad, será documento suficiente para la formalización de la compraventa con la empresa titular del inmueble que por este medio adquiere el cliente. Una vez que se decrete el término del periodo de contingencia a que se hace referencia en el párrafo anterior, el comprador se compromete a suscribir el contrato de compraventa respectivo, mismo que le será entregado impreso en un periodo máximo de 60 (sesenta) días naturales, contados a partir del término del periodo de contingencia. De acuerdo a lo estipulado en el contrato de compraventa que habrá de suscribirse entre el comprador y el vendedor, la pena convencional en caso de que el comprador incumpla con cualquiera de sus obligaciones es del 25% (veinticinco por ciento) del precio total pactado. Una vez formalizada la compraventa y en caso de que el comprador solicite el envío del contrato de compraventa en forma digital, éste podrá ser solicitado a través de su asesor de ventas.</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>En el Municipio de <b>' . $municipio2 . '</b> a <b>' . $dia . '</b> del mes <b>' . $mes . '</b> del año <b>' . $anio . '</b>.</label> 
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>



            <tr>
            <br><br><br><br><br> 
            <td width="70%" align="center">' . $nombre . ' ' . $apellido_paterno . ' ' . $apellido_materno . ' ' . $coprops_ds . '
            <BR> ______________________________________________________________________________<p>Nombre y Firma / Ofertante</p><p>Acepto que se realice una verificación de mis datos, en los teléfonos<br> y correos que proporciono para el otorgamiento del crédito.</p>
            </td>

            <td width="30%" align="center"><label><b>REFERENCIAS PERSONALES</b>.</label><br>
            <p align="left">
            <label>' . $nombre1 . ' - ' . $parentezco_select1 . ' - ' . $telefono1 . '</label><br>
            <label>' . $nombre2 . ' - ' . $parentezco_select2 . ' - ' . $telefono2 . '</label>
            </p>
            </td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2"></td>
            </tr>


            <tr>
            <td width="100%" colspan="2" style="border-bottom: 1px solid #CCCCCC"><label>OBSERVACIONES:
                </label><br><br><b style="padding-left: 250px">&nbsp;' . $observacion . '</b><br>
            </td>
            </tr>

            <tr>
            <td width="100%" colspan="2"></td>
            </tr>
            <tr>
            <br><br><br><br><br> 
            <td width="50%" align="center">' . $asesor_datos . '<BR> ______________________________________________________________________________<p> 
            <b>Nombre y Firma / Asesor</b></p>
            </td>

            <td width="50%" align="center">' . $gerente_datos . '<BR> ______________________________________________________________________________<p> 
            <b>Nombre y Firma / Autorización de operación</b></p>
            </td>
            </tr>
 

            <tr>
            <td width="100%" align="center">
            <table border="0" width="91%" style="background-color:#ffffff;">
            <tr>
            <td></td>
            </tr>
            </table>
            </td>
            </tr>


            </table>
            </td>

 
            </div>
            </div>
            </body>
            </html>';

            $pdf->writeHTMLCell(0, 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);


            $namePDF = utf8_decode('DEPÓSITO_DE_SERIEDAD_' . $id_cliente . '.pdf');
            ob_end_clean();
            $pdf->Output(utf8_decode($namePDF), 'I');


            $attachment = $pdf->Output(utf8_decode($namePDF), 'S');
            // PHPMailer object

            /************************************************************************************
            * Armado de parámetros a mandar a plantilla para creación de correo electrónico     *
            ************************************************************************************/
            $correos_entregar = array();
            // if(count($arrayCorreoNotRepeat)>0){
            //     foreach ($arrayCorreoNotRepeat as $arrCorreo){
            //         if ($arrCorreo) {
            //             array_push($correos_entregar, $arrCorreo);
            //         }
            //     }
            // }

            $datos_etiquetas = null;

            $archivo_adjunto = array('adjunto'      =>  $attachment,
                                     'nombre_pdf'   =>  $namePDF);

            $datos_correo[0] = array();

            #PROVICIONAL TESTING
            array_push($correos_entregar, 'programador.analista18@ciudadmaderas.com');
            //$correos_entregar[0] = 'programador.analista18@ciudadmaderas.com';
            //$correos_entregar[1] = 'mariadejesus.garduno@ciudadmaderas.com';

            $elementos_correo = array(  "setFrom" => Elementos_Correos_Asesor::SET_FROM_EMAIL,
                                        "Subject" => Elementos_Correos_Asesor::ASUNTO_CORREO_TABLA_DEPOSITOS_SERIEDAD_ASESOR);

            $comentario_general = Elementos_Correos_Asesor::EMAIL_DEPOSITO_SERIEDAD_ASESOR;
            $datos_encabezados_tabla = Elementos_Correos_Asesor::ETIQUETAS_ENCABEZADO_TABLA_DEPOSITOS_SERIEDAD_ASESOR;

            $plantilla_correo = new plantilla_dinamica_correo;
            /********************************************************************************************************************************************/

            $checkIfRefExist = $this->Asesor_model->checkExistRefrencias($id_cliente);
            if (count($checkIfRefExist) >= 1) {
                /**********/
                if (count($array17) > 0) {
                    for ($i = 0; $i < sizeof($array17); $i++) {
                        $updCoprop = $this->db->query(" UPDATE copropietarios SET correo = '" . $array1[$i] . "', telefono = '" . $array2[$i] . "', 
                                                            telefono_2 = '" . $array3[$i] . "', fecha_nacimiento = '" . $array4[$i] . "',
                                                            nacionalidad = '" . $array5[$i] . "', originario_de = '" . $array6[$i] . "',
                                                            domicilio_particular = '" . $array7[$i] . "', estado_civil = '" . $array8[$i] . "',
                                                            conyuge = '" . $array9[$i] . "', regimen_matrimonial = '" . $array10[$i] . "',
                                                            ocupacion = '" . $array11[$i] . "', posicion = '" . $array12[$i] . "', empresa = '" . $array13[$i] . "',
                                                            antiguedad = '" . $array14[$i] . "', edadFirma = '" . $array15[$i] . "', direccion = '" . $array16[$i] . "',
                                                            rfc='" . $array19[$i] . "',  tipo_vivienda=" . $array18[$i] . "
                                                        WHERE id_copropietario = " . $array17[$i]);
                    }
                }
                /**********/


                if ($this->Asesor_model->editaRegistroClienteDS($id_cliente, $arreglo_cliente, $arreglo_ds, $id_referencia1, $arreglo_referencia1, $id_referencia2, $arreglo_referencia2)) {

                    if($arreglo_cliente['tipo_comprobanteD'] == 1){
                        //checar si ya se ha añadido el registro anteriormente
                        $dcv = $this->Asesor_model->informacionVerificarCliente($id_cliente);
                        $revisar_registro = $this->Asesor_model->revisarCartaVerif($id_cliente, 29);

                        if(count($revisar_registro) == 0){
                            //validar la opcion de domicilio empresa

                            //se crea rama
                            $data_insert = array(
                                'movimiento'        =>'CARTA DOMICILIO CM',
                                'expediente'        => NULL,
                                'modificado'        => date('Y-m-d H:i:s'),
                                'status'            => 1,
                                'idCliente'         => $id_cliente,
                                'idCondominio'      => $dcv->idCondominio,
                                'idLote'            => $dcv->idLote,
                                'idUser'            => NULL,
                                'tipo_documento'    => 0,
                                'id_autorizacion'   => 0,
                                'tipo_doc'          => 29,
                                'estatus_validacion'=> 0
                            );

                            $dbTransaction = $this->General_model->addRecord("historial_documento", $data_insert); // MJ: LLEVA 2 PARÁMETROS $table, $data
                            //if ($dbTransaction) // SUCCESS TRANSACTION
                            //echo json_encode(array("status" => 200, "message" => "Registro guardado con éxito.", "resultado" => $result), JSON_UNESCAPED_UNICODE);
                            //else // ERROR TRANSACTION
                            //echo json_encode(array("status" => 503, "message" => "Servicio no disponible. El servidor no está listo para manejar la solicitud. Por favor, inténtelo de nuevo más tarde."), JSON_UNESCAPED_UNICODE);
                        }
                    }
                    $datos_correo_enviar = $plantilla_correo->crearPlantillaCorreo($correos_entregar, $elementos_correo, $datos_correo,
                                                                                $datos_encabezados_tabla, $datos_etiquetas, $comentario_general, $archivo_adjunto);
                    if ($datos_correo_enviar > 0) {
                        ECHO "SUCCESS";
                    } else {
                        ECHO 'Correo no enviado '.$datos_correo_enviar;
                    }

                } else {

                    die("ERROR");

                }

            } else {
                /**********/
                if (count($array17) > 0) {
                    for ($i = 0; $i < sizeof($array17); $i++) {
                        $updCoprop = $this->db->query("UPDATE copropietarios SET correo = '" . $array1[$i] . "', telefono = '" . $array2[$i] . "', telefono_2 = '" . $array3[$i] . "', fecha_nacimiento = '" . $array4[$i] . "', nacionalidad = '" . $array5[$i] . "', originario_de = '" . $array6[$i] . "', domicilio_particular = '" . $array7[$i] . "', estado_civil = '" . $array8[$i] . "', conyuge = '" . $array9[$i] . "', regimen_matrimonial = '" . $array10[$i] . "', ocupacion = '" . $array11[$i] . "', posicion = '" . $array12[$i] . "', empresa = '" . $array13[$i] . "', antiguedad = '" . $array14[$i] . "', edadFirma = '" . $array15[$i] . "', direccion = '" . $array16[$i] . "',
                                rfc='" . $array19[$i] . "',  tipo_vivienda=" . $array18[$i] . "
                            WHERE id_copropietario = " . $array17[$i]);
                    }
                }
                /**********/

                if ($this->Asesor_model->editaRegistroClienteDS_2($id_cliente, $arreglo_cliente, $arreglo_ds)) {
                    if($arreglo_cliente['tipo_comprobanteD'] == 1){
                        //checar si ya se ha añadido el registro anteriormente
                        $dcv = $this->Asesor_model->informacionVerificarCliente($id_cliente);
                        $revisar_registro = $this->Asesor_model->revisarCartaVerif($id_cliente, 29);

                        if(count($revisar_registro) == 0){
                            //validar la opcion de domicilio empresa

                            //se crea rama
                            $data_insert = array(
                                'movimiento'        =>'CARTA DOMICILIO CM',
                                'expediente'        => NULL,
                                'modificado'        => date('Y-m-d H:i:s'),
                                'status'            => 1,
                                'idCliente'         => $id_cliente,
                                'idCondominio'      => $dcv->idCondominio,
                                'idLote'            => $dcv->idLote,
                                'idUser'            => NULL,
                                'tipo_documento'    => 0,
                                'id_autorizacion'   => 0,
                                'tipo_doc'          => 29,
                                'estatus_validacion'=> 0
                            );

                            $dbTransaction = $this->General_model->addRecord("historial_documento", $data_insert); // MJ: LLEVA 2 PARÁMETROS $table, $data
                            //if ($dbTransaction) // SUCCESS TRANSACTION
                            //echo json_encode(array("status" => 200, "message" => "Registro guardado con éxito.", "resultado" => $result), JSON_UNESCAPED_UNICODE);
                            //else // ERROR TRANSACTION
                            //echo json_encode(array("status" => 503, "message" => "Servicio no disponible. El servidor no está listo para manejar la solicitud. Por favor, inténtelo de nuevo más tarde."), JSON_UNESCAPED_UNICODE);
                        }
                    }
                    $arreglo_referencia2["id_cliente"] = $id_cliente;
                    $arreglo_referencia1["id_cliente"] = $id_cliente;
                    $this->Asesor_model->insertnewRef($arreglo_referencia1);
                    $this->Asesor_model->insertnewRef($arreglo_referencia2);
                    $datos_correo_enviar = $plantilla_correo->crearPlantillaCorreo($correos_entregar, $elementos_correo, $datos_correo,
                                                                                    $datos_encabezados_tabla, $datos_etiquetas, $comentario_general, $archivo_adjunto);
                    if ($datos_correo_enviar > 0) {
                        ECHO "SUCCESS";
                    } else {
                        ECHO 'Correo no enviado '.$datos_correo_enviar;
                    }

                } else {

                    die("ERROR");

                }


            }

        }
        else if ($this->input->post('pdfOK') == null || $this->input->post('pdfOK') != '1') {

            $checkIfRefExist = $this->Asesor_model->checkExistRefrencias($id_cliente);

            if (count($checkIfRefExist) > 0) {
                $updateDs = $this->Asesor_model->editaRegistroClienteDS($id_cliente, $arreglo_cliente, $arreglo_ds, $id_referencia1, $arreglo_referencia1, $id_referencia2, $arreglo_referencia2);

                if ($updateDs) {

                    if($arreglo_cliente['tipo_comprobanteD'] == 1){
                        //checar si ya se ha añadido el registro anteriormente
                        $dcv = $this->Asesor_model->informacionVerificarCliente($id_cliente);
                        $revisar_registro = $this->Asesor_model->revisarCartaVerif($id_cliente, 29);

                        if(count($revisar_registro) == 0){
                        //validar la opcion de domicilio empresa

                            //se crea rama
                            $data_insert = array(
                                'movimiento'        =>'CARTA DOMICILIO CM',
                                'expediente'        => NULL,
                                'modificado'        => date('Y-m-d H:i:s'),
                                'status'            => 1,
                                'idCliente'         => $id_cliente,
                                'idCondominio'      => $dcv->idCondominio,
                                'idLote'            => $dcv->idLote,
                                'idUser'            => NULL,
                                'tipo_documento'    => 0,
                                'id_autorizacion'   => 0,
                                'tipo_doc'          => 29,
                                'estatus_validacion'=> 0
                            );
                            $dbTransaction = $this->General_model->addRecord("historial_documento", $data_insert); // MJ: LLEVA 2 PARÁMETROS $table, $data
                            //if ($dbTransaction) // SUCCESS TRANSACTION
                            //echo json_encode(array("status" => 200, "message" => "Registro guardado con éxito.", "resultado" => $result), JSON_UNESCAPED_UNICODE);
                            //else // ERROR TRANSACTION
                            //echo json_encode(array("status" => 503, "message" => "Servicio no disponible. El servidor no está listo para manejar la solicitud. Por favor, inténtelo de nuevo más tarde."), JSON_UNESCAPED_UNICODE);
                        }
                    }




                    if (count($array17) > 0) {
                        for ($i = 0; $i < sizeof($array17); $i++) {
                            $updCoprop = $this->db->query("UPDATE copropietarios SET correo = '" . $array1[$i] . "', telefono = '" . $array2[$i] . "', telefono_2 = '" . $array3[$i] . "', fecha_nacimiento = '" . $array4[$i] . "', nacionalidad = '" . $array5[$i] . "', originario_de = '" . $array6[$i] . "', domicilio_particular = '" . $array7[$i] . "', estado_civil = '" . $array8[$i] . "', conyuge = '" . $array9[$i] . "', regimen_matrimonial = '" . $array10[$i] . "', ocupacion = '" . $array11[$i] . "', posicion = '" . $array12[$i] . "', empresa = '" . $array13[$i] . "', antiguedad = '" . $array14[$i] . "', edadFirma = '" . $array15[$i] . "', direccion = '" . $array16[$i] . "',
                                rfc='" . $array19[$i] . "',  tipo_vivienda=" . $array18[$i] . "
                            WHERE id_copropietario = " . $array17[$i]);
                            //$array18=$this->input->post("tipo_vivienda_cop[]");
                            //$array19=$this->input->post("rfc_cop[]");
                        }
                        if ($updCoprop) {
                            //echo 'Se actualizaron correctamente';
                            $this->session->set_userdata('success_coprop', 777);
                            redirect(base_url() . 'index.php/Asesor/deposito_seriedad/' . $id_cliente . '/0');
                            // redirect(base_url()."index.php/asesor/depositoSeriedad");
                        }
                    }
                    $this->session->set_userdata('success_coprop', 777);
                    redirect(base_url() . 'index.php/Asesor/deposito_seriedad/' . $id_cliente . '/0');
                    // redirect(base_url()."index.php/asesor/depositoSeriedad");

                } else {

                    $this->session->set_userdata('success_coprop', -1);
                    redirect(base_url() . 'index.php/Asesor/deposito_seriedad/' . $id_cliente . '/0');
                    // redirect(base_url()."index.php/asesor/depositoSeriedad");
                }
            } else {
                if ($this->Asesor_model->editaRegistroClienteDS_2($id_cliente, $arreglo_cliente, $arreglo_ds)) {

                    if (count($checkIfRefExist) <= 0) {
                        $arreglo_referencia2["id_cliente"] = $id_cliente;
                        $arreglo_referencia2["creado_por"] = $this->session->userdata('id_usuario');
                        $arreglo_referencia1["id_cliente"] = $id_cliente;
                        $arreglo_referencia1["creado_por"] = $this->session->userdata('id_usuario');
                        $this->Asesor_model->insertnewRef($arreglo_referencia1);
                        $this->Asesor_model->insertnewRef($arreglo_referencia2);

                    }
                    $this->session->set_userdata('success_coprop', 777);
                    redirect(base_url() . 'index.php/Asesor/deposito_seriedad/' . $id_cliente . '/0');


                } else {
                    die("ERROR");
                }
            }
        }
    }

    /*autorizaciones*/
    public function autorizaciones()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $this->load->view('template/header');
        $this->load->view("asesor/autorizaciones", $datos);
    }

    function getAutorizacionAs()
    {
        $data['data'] = $this->Asesor_model->get_autorizaciones();
        echo json_encode($data);
    }

    public function get_auts_by_lote($idLote)
    {
        $data = $this->Asesor_model->get_auts_by_lote($idLote);
        //print_r($data);
        if ($data != null) {

            echo json_encode($data);

        } else {

            echo json_encode(array());

        }
    }

    public function get_sol_aut()
    {
        $data['data'] = $this->Asesor_model->get_sol_aut();
        echo json_encode($data);
    }

    public function addAutorizacionSbmt()
    {
        $data = array();
        $tamanoArreglo = $_POST['tamanocer'];
        $idCliente = $_POST['idCliente'];
        $idLote = $_POST['idLote'];
        $id_sol = $_POST['id_sol'];
        $id_aut = $_POST['id_aut'];

        /*nuevo*/
        $nombreResidencial = $_POST['nombreResidencial'];
        $nombreCondominio = $_POST['nombreCondominio'];
        $nombreLote = $_POST['nombreLote'];
        $idCondominio = $_POST['idCondominio'];
        $autorizacionComent = "";
        /*termina nuevo*/
        $comentario = '';
        for ($n = 0; $n < $tamanoArreglo; $n++) {
            $data = array(
                'idCliente' => $idCliente,
                'idLote' => $idLote,
                'id_sol' => $id_sol,
                'id_aut' => $id_aut,
                'estatus' => 1,
                'autorizacion' => $_POST['comentario_' . $n]
            );
            $dataInsert = $this->Asesor_model->insertAutorizacion($data);
            $n > 0 ? $comentario .= "<br>-".$_POST['comentario_' . $n] : $comentario .= '-'.$_POST['comentario_' . $n];
            $autorizacionComent .= $_POST['comentario_' . $n] . ". ";
        }

        if ($dataInsert == 1) {
            $correos_entregar = array();
            //funcion aterior -> notifyUsers 
            /************************************************************************************
		    * Armado de parámetros a mandar a plantilla para creación de correo electrónico     *
		    ************************************************************************************/
            $dataUser = $this->Asesor_model->getInfoUserById($id_aut);

            $datos_correo[0] = array('nombreResidencial'   =>  $nombreResidencial,
                                  'nombreCondominio'    =>  $nombreCondominio,
                                  'nombreLote'          =>  $nombreLote,
                                  'motivoAut'           =>  $autorizacionComent,
                                  'fecgaHora'           =>  date("Y-m-d H:i:s"));

            $datos_etiquetas = null; 
            $elementos_correo = array("setFrom" => Elementos_Correos_Asesor::SET_FROM_EMAIL,
							          "Subject" => Elementos_Correos_Asesor::ASUNTO_CORREO_TABLA_NUEVA_AUTORIZACION_SBMT);

            $comentario_general = Elementos_Correos_Asesor::EMAIL_NUEVA_AUTORIZACION_SBMT.'<br><br>'. $comentario;
            $datos_encabezados_tabla = Elementos_Correos_Asesor::ETIQUETAS_ENCABEZADO_TABLA_NUEVA_AUTORIZACION_SBMT;

            $plantilla_correo = new plantilla_dinamica_correo;

            // if(count($dataUser)>0){
            //     $index_correo = 0;
            //     foreach ($dataUser as $item => $value){
            //         $index_correo ++;
            //         $correos_entregar += ["correo".$index_correo  =>  $value->correo];
            //     }
            // }
            // if (!empty(array_search('gustavo.mancilla@ciudadmaderas.com', $correos_entregar))){
            //     unset($correos_entregar[array_search('gustavo.mancilla@ciudadmaderas.com', $correos_entregar)]);
            // }

            /***************
            *CORREO TESTING* 
            ****************/
            array_push($correos_entregar, 'programador.analista18@ciudadmaderas.com');

            /**************************************************************************************/
            if(count($correos_entregar) > 0){
                /*envia un correo cuando se solicita una nueva autorizacion*/
                $plantilla_correo
                    ->crearPlantillaCorreo($correos_entregar, $elementos_correo, $datos_correo, 
                                            $datos_encabezados_tabla, $datos_etiquetas, $comentario_general);
            }

            echo json_encode($dataInsert);
        } else {
            echo json_encode($dataInsert);
        }
    }

    public function intExpAsesor()
    {


        $idLote = $this->input->post('idLote');
        $nombreLote = $this->input->post('nombreLote');
        $id_cliente = $this->input->post('idCliente');
        $tipo_comprobante = $this->input->post('tipo_comprobante');

        $valida_tventa = $this->Asesor_model->getTipoVenta($idLote);//se valida el tipo de venta para ver si se va al nuevo status 3 (POSTVENTA)
        if($valida_tventa[0]['tipo_venta'] == 1 ){
            if($valida_tventa[0]['idStatusContratacion'] == 1 && $valida_tventa[0]['idMovimiento'] == 104 || $valida_tventa[0]['idStatusContratacion'] == 2 && $valida_tventa[0]['idMovimiento'] == 108){
                $statusContratacion = 2;
                $idMovimiento = 105;
            }elseif($valida_tventa[0]['idStatusContratacion'] == 1 && $valida_tventa[0]['idMovimiento'] == 109 || $valida_tventa[0]['idStatusContratacion'] == 1 && $valida_tventa[0]['idMovimiento'] == 111 ){
                $statusContratacion = 2;
                $idMovimiento = 110;
            }
            elseif($valida_tventa[0]['idStatusContratacion'] == 1 && $valida_tventa[0]['idMovimiento'] == 102){ #rechazo del status 5
                $statusContratacion = 2;
                $idMovimiento = 113;
            }
            elseif($valida_tventa[0]['idStatusContratacion'] == 1 && $valida_tventa[0]['idMovimiento'] == 107){ #rechazo del status 6
                $statusContratacion = 2;
                $idMovimiento = 114;
            }
            else{
                $statusContratacion = 3;
                $idMovimiento = 98;
            }

        }else{
            $statusContratacion = 2;
            $idMovimiento = 84;
        }

        $arreglo = array();
        $arreglo["idStatusContratacion"] = $statusContratacion;
        $arreglo["idMovimiento"] = $idMovimiento;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");
        $arreglo["comentario"] = $this->input->post('comentario');
        $data = $this->Asesor_model->revisaOU($idLote);
        if(count($data)>=1){
            $data['message'] = 'OBSERVACION_CONTRATO';
            echo json_encode($data);
            exit;
        }else{
        if ($this->session->userdata('id_rol') == 17 || $this->session->userdata('id_rol') == 70)
            $documentsNumber = 3;
        else
            $documentsNumber = $tipo_comprobante == 1 ? 3 : 4; //se valida si quiere la carta de domicilio para que  no valide el comp de domicilio


        $dataClient = $this->Asesor_model->getLegalPersonalityByLote($idLote);
        $documentsValidation = $this->Asesor_model->validateDocumentation($idLote, $dataClient[0]['personalidad_juridica'], $tipo_comprobante);
        $validacion = $this->Asesor_model->getAutorizaciones($idLote, $id_cliente);
        $validacionIM = $this->Asesor_model->getInicioMensualidadAut($idLote, $id_cliente); //validacion para verificar si tiene inicio de autorizacion de mensualidad pendiente



        if(COUNT($documentsValidation) != $documentsNumber && COUNT($documentsValidation) < $documentsNumber) {
            $data['message'] = 'MISSING_DOCUMENTS';
            echo json_encode($data);
        }
        else if($validacion) {
            $data['message'] = 'MISSING_AUTORIZATION';
            echo json_encode($data);
        }
        else if(count($validacionIM)>0){
            if($validacionIM[0]['tipoPM']==3 AND $validacionIM[0]['expediente'] == ''){
                $data['message'] = 'MISSING_AUTFI';
                echo json_encode($data);
            }
        }
        else {
            date_default_timezone_set('America/Mexico_City');
            $horaActual = date('H:i:s');
            $horaInicio = date("08:00:00");
            $horaFin = date("16:00:00");


            if ($horaActual > $horaInicio and $horaActual < $horaFin) {

                $fechaAccion = date("Y-m-d H:i:s");
                $hoy_strtotime2 = strtotime($fechaAccion);
                $sig_fecha_dia2 = date('D', $hoy_strtotime2);
                $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);


                if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                    $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                    $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                    $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                    $sig_fecha_feriado2 == "25-12") {

                    $fecha = $fechaAccion;
                    $i = 0;

                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);


                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;

                    }
                    $arreglo["fechaVenc"] = $fecha;

                } else {

                    $fecha = $fechaAccion;
                    $i = 0;
                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }

                    $arreglo["fechaVenc"] = $fecha;

                }

            }
            elseif ($horaActual < $horaInicio || $horaActual > $horaFin) {

                $fechaAccion = date("Y-m-d H:i:s");
                $hoy_strtotime2 = strtotime($fechaAccion);
                $sig_fecha_dia2 = date('D', $hoy_strtotime2);
                $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

                if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                    $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                    $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                    $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                    $sig_fecha_feriado2 == "25-12") {

                    $fecha = $fechaAccion;
                    $i = 0;

                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }

                    $arreglo["fechaVenc"] = $fecha;

                } else {
                    $fecha = $fechaAccion;
                    $i = 0;

                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }

                    $arreglo["fechaVenc"] = $fecha;

                }
            }



            $arreglo2 = array();
            $arreglo2["idStatusContratacion"] = $statusContratacion;
            $arreglo2["idMovimiento"] = $idMovimiento;
            $arreglo2["nombreLote"] = $nombreLote;
            $arreglo2["usuario"] = $this->session->userdata('id_usuario');
            $arreglo2["perfil"] = $this->session->userdata('id_rol');
            $arreglo2["modificado"] = date("Y-m-d H:i:s");
            $arreglo2["fechaVenc"] = $this->input->post('fechaVenc');
            $arreglo2["idLote"] = $idLote;
            $arreglo2["idCondominio"] = $this->input->post('idCondominio');
            $arreglo2["idCliente"] = $this->input->post('idCliente');
            $arreglo2["comentario"] = $this->input->post('comentario');



            $validate = $this->Asesor_model->validateSt2($idLote);


            if ($validate == 1) {

                if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                    $data['message'] = 'OK';
                    echo json_encode($data);
                } else {
                    $data['message'] = 'ERROR';
                    echo json_encode($data);
                }

            } else {
                $data['message'] = 'FALSE';
                echo json_encode($data);
            }

        }
        }
    }


    public function editar_registro_loteRevision_asistentesAContraloria6_proceceso2()
    {

        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $nombreLote = $this->input->post('nombreLote');
        $idCliente = $this->input->post('idCliente');
        $comentario = $this->input->post('comentario');
        $modificado = date("Y-m-d H:i:s");
        $fechaVenc = $this->input->post('fechaVenc');


        $valida_tl = $this->Contraloria_model->checkTipoVenta($idLote);

        if($valida_tl[0]['tipo_venta'] == 1){
            $idStaC = 3;
            $idMov = 98;
        }else{
            $idStaC = 2;
            $idMov = 62;
        }

        $arreglo = array();
        $arreglo["idStatusContratacion"] = $idStaC;
        $arreglo["idMovimiento"] = $idMov;
        $arreglo["comentario"] = $comentario;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");


        date_default_timezone_set('America/Mexico_City');
        $horaActual = date('H:i:s');
        $horaInicio = date("08:00:00");
        $horaFin = date("16:00:00");

        if ($horaActual > $horaInicio and $horaActual < $horaFin) {
            $fechaAccion = date("Y-m-d H:i:s");
            $hoy_strtotime2 = strtotime($fechaAccion);
            $sig_fecha_dia2 = date('D', $hoy_strtotime2);
            $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

            if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                $sig_fecha_feriado2 == "25-12") {


                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);


                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }
                $arreglo["fechaVenc"] = $fecha;
            } else {
                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 1) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }
                $arreglo["fechaVenc"] = $fecha;
            }

        } elseif ($horaActual < $horaInicio || $horaActual > $horaFin) {
            $fechaAccion = date("Y-m-d H:i:s");
            $hoy_strtotime2 = strtotime($fechaAccion);
            $sig_fecha_dia2 = date('D', $hoy_strtotime2);
            $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

            if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                $sig_fecha_feriado2 == "25-12") {

                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }
                $arreglo["fechaVenc"] = $fecha;
            } else {
                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }
                $arreglo["fechaVenc"] = $fecha;
            }
        }


        $arreglo2 = array();
        $arreglo2["idStatusContratacion"] = $idStaC;
        $arreglo2["idMovimiento"] = $idMov;
        $arreglo2["nombreLote"] = $nombreLote;
        $arreglo2["comentario"] = $comentario;
        $arreglo2["usuario"] = $this->session->userdata('id_usuario');
        $arreglo2["perfil"] = $this->session->userdata('id_rol');
        $arreglo2["modificado"] = date("Y-m-d H:i:s");
        $arreglo2["fechaVenc"] = $fechaVenc;
        $arreglo2["idLote"] = $idLote;
        $arreglo2["idCondominio"] = $idCondominio;
        $arreglo2["idCliente"] = $idCliente;




        $validate = $this->Asesor_model->validateSt2($idLote);

        if ($validate == 1) {

            if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                $data['message'] = 'OK';
                echo json_encode($data);

            } else {
                $data['message'] = 'ERROR';
                echo json_encode($data);
            }
            
        } else {
            $data['message'] = 'FALSE';
            echo json_encode($data);
        }

    }

    public function editar_registro_loteRevision_asistentesAContraloria_proceceso2()
    {

        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $nombreLote = $this->input->post('nombreLote');
        $idCliente = $this->input->post('idCliente');
        $comentario = $this->input->post('comentario');
        $fechaVenc = $this->input->post('fechaVenc');

        $dataClient = $this->Asesor_model->getLegalPersonalityByLote($idLote);

        if ($this->session->userdata('id_rol') == 32) {
            $documentsNumber = 3;
        } else {
            $documentsNumber = 4;
        }

        $documentsValidation = $this->Asesor_model->validateDocumentation($idLote, $dataClient[0]['personalidad_juridica']);
        $dataBackTest = $this->Asesor_model->getWstatus1($idLote);

        if (COUNT($documentsValidation) < $documentsNumber) {
            $data['message'] = 'MISSING_DOCUMENTS';
            echo json_encode($data);
        } elseif(count($dataBackTest)<=0){
            $data['message'] = 'PENDIENT_AUTHORIZATION';
            echo json_encode($data);
        }
        else {
            $arreglo = array();
            $arreglo["idStatusContratacion"] = 2;
            $arreglo["idMovimiento"] = 4;
            $arreglo["comentario"] = $comentario;
            $arreglo["usuario"] = $this->session->userdata('id_usuario');
            $arreglo["perfil"] = $this->session->userdata('id_rol');
            $arreglo["modificado"] = date("Y-m-d H:i:s");

            date_default_timezone_set('America/Mexico_City');
            $horaActual = date('H:i:s');
            $horaInicio = date("08:00:00");
            $horaFin = date("16:00:00");

            if ($horaActual > $horaInicio and $horaActual < $horaFin) {

                $fechaAccion = date("Y-m-d H:i:s");
                $hoy_strtotime2 = strtotime($fechaAccion);
                $sig_fecha_dia2 = date('D', $hoy_strtotime2);
                $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

                if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                    $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                    $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                    $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                    $sig_fecha_feriado2 == "25-12") {

                    $fecha = $fechaAccion;

                    $i = 0;
                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;

                    }
                    $arreglo["fechaVenc"] = $fecha;
                } else {

                    $fecha = $fechaAccion;
                    $i = 0;
                    while ($i <= -1) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }
                    $arreglo["fechaVenc"] = $fecha;

                }

            }
            elseif ($horaActual < $horaInicio || $horaActual > $horaFin) {

                $fechaAccion = date("Y-m-d H:i:s");
                $hoy_strtotime2 = strtotime($fechaAccion);
                $sig_fecha_dia2 = date('D', $hoy_strtotime2);
                $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

                if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                    $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                    $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                    $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                    $sig_fecha_feriado2 == "25-12") {

                    $fecha = $fechaAccion;
                    $i = 0;

                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);


                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }
                    $arreglo["fechaVenc"] = $fecha;
                } else {

                    $fecha = $fechaAccion;

                    $i = 0;
                    while ($i <= 0) {
                        $hoy_strtotime = strtotime($fecha);
                        $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                        $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                        $sig_fecha_dia = date('D', $sig_strtotime);
                        $sig_fecha_feriado = date('d-m', $sig_strtotime);

                        if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                            $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                            $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                            $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                            $sig_fecha_feriado == "25-12") {
                        } else {
                            $fecha = $sig_fecha;
                            $i++;
                        }
                        $fecha = $sig_fecha;
                    }
                    $arreglo["fechaVenc"] = $fecha;
                }
            }


            $arreglo2 = array();
            $arreglo2["idStatusContratacion"] = 2;
            $arreglo2["idMovimiento"] = 4;
            $arreglo2["nombreLote"] = $nombreLote;
            $arreglo2["comentario"] = $comentario;
            $arreglo2["usuario"] = $this->session->userdata('id_usuario');
            $arreglo2["perfil"] = $this->session->userdata('id_rol');
            $arreglo2["modificado"] = date("Y-m-d H:i:s");
            $arreglo2["fechaVenc"] = $fechaVenc;
            $arreglo2["idLote"] = $idLote;
            $arreglo2["idCondominio"] = $idCondominio;
            $arreglo2["idCliente"] = $idCliente;

            $validate = $this->Asesor_model->validateSt2($idLote);

            if ($validate == 1) {
                if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                    $data['message'] = 'OK';
                    echo json_encode($data);
                } else {
                    $data['message'] = 'ERROR';
                    echo json_encode($data);
                }
            } else {
                $data['message'] = 'FALSE';
                echo json_encode($data);
            }
        }
    }


    public function envioRevisionAsesor2aJuridico7()
    {

        $idCliente = $this->input->post('idCliente');
        $nombreLote = $this->input->post('nombreLote');
        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $comentario = $this->input->post('comentario');
        $fechaVenc = $this->input->post('fechaVenc');

        $valida_tventa = $this->Asesor_model->getTipoVenta($idLote);//se valida el tipo de venta para ver si se va al nuevo status 3 (POSTVENTA)
        if($valida_tventa[0]['tipo_venta'] == 1 ){
            $statusContratacion = 3;
            $idMovimiento = 98;
        }else{
            $statusContratacion = 7;
            $idMovimiento = 83;
        }

        $arreglo = array();
        $arreglo["idStatusContratacion"] = $statusContratacion;
        $arreglo["idMovimiento"] = $idMovimiento;
        $arreglo["comentario"] = $comentario;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");


        $horaActual = date('H:i:s');
        $horaInicio = date("08:00:00");
        $horaFin = date("16:00:00");


        if ($horaActual > $horaInicio and $horaActual < $horaFin) {

            $fechaAccion = date("Y-m-d H:i:s");
            $hoy_strtotime2 = strtotime($fechaAccion);
            $sig_fecha_dia2 = date('D', $hoy_strtotime2);
            $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);


            if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                $sig_fecha_feriado2 == "25-12") {

                $fecha = $fechaAccion;
                $i = 0;

                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);


                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;

                }
                $arreglo["fechaVenc"] = $fecha;

            } else {

                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 1) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }

                $arreglo["fechaVenc"] = $fecha;

            }

        } elseif ($horaActual < $horaInicio || $horaActual > $horaFin) {

            $fechaAccion = date("Y-m-d H:i:s");
            $hoy_strtotime2 = strtotime($fechaAccion);
            $sig_fecha_dia2 = date('D', $hoy_strtotime2);
            $sig_fecha_feriado2 = date('d-m', $hoy_strtotime2);

            if ($sig_fecha_dia2 == "Sat" || $sig_fecha_dia2 == "Sun" ||
                $sig_fecha_feriado2 == "01-01" || $sig_fecha_feriado2 == "06-02" ||
                $sig_fecha_feriado2 == "20-03" || $sig_fecha_feriado2 == "01-05" ||
                $sig_fecha_feriado2 == "16-09" || $sig_fecha_feriado2 == "20-11" || $sig_fecha_feriado2 == "19-11" ||
                $sig_fecha_feriado2 == "25-12") {

                $fecha = $fechaAccion;
                $i = 0;

                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }

                $arreglo["fechaVenc"] = $fecha;

            } else {
                $fecha = $fechaAccion;
                $i = 0;

                while ($i <= 2) {
                    $hoy_strtotime = strtotime($fecha);
                    $sig_strtotime = strtotime('+1 days', $hoy_strtotime);
                    $sig_fecha = date("Y-m-d H:i:s", $sig_strtotime);
                    $sig_fecha_dia = date('D', $sig_strtotime);
                    $sig_fecha_feriado = date('d-m', $sig_strtotime);

                    if ($sig_fecha_dia == "Sat" || $sig_fecha_dia == "Sun" ||
                        $sig_fecha_feriado == "01-01" || $sig_fecha_feriado == "06-02" ||
                        $sig_fecha_feriado == "20-03" || $sig_fecha_feriado == "01-05" ||
                        $sig_fecha_feriado == "16-09" || $sig_fecha_feriado == "20-11" || $sig_fecha_feriado == "19-11" ||
                        $sig_fecha_feriado == "25-12") {
                    } else {
                        $fecha = $sig_fecha;
                        $i++;
                    }
                    $fecha = $sig_fecha;
                }

                $arreglo["fechaVenc"] = $fecha;

            }
        }


        $arreglo2 = array();
        $arreglo2["idStatusContratacion"] = $statusContratacion;
        $arreglo2["idMovimiento"] = $idMovimiento;
        $arreglo2["nombreLote"] = $nombreLote;
        $arreglo2["comentario"] = $comentario;
        $arreglo2["usuario"] = $this->session->userdata('id_usuario');
        $arreglo2["perfil"] = $this->session->userdata('id_rol');
        $arreglo2["modificado"] = date("Y-m-d H:i:s");
        $arreglo2["fechaVenc"] = $fechaVenc;
        $arreglo2["idLote"] = $idLote;
        $arreglo2["idCondominio"] = $idCondominio;
        $arreglo2["idCliente"] = $idCliente;


        $validate = $this->Asesor_model->validateSt2($idLote);

        if ($validate == 1) {

            if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                $data['message'] = 'OK';
                echo json_encode($data);

            } else {
                $data['message'] = 'ERROR';
                echo json_encode($data);
            }

        } else {
            $data['message'] = 'FALSE';
            echo json_encode($data);
        }


    }

    public function editar_registro_loteRevision_eliteAcontraloria5_proceceso2()
    {
        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $nombreLote = $this->input->post('nombreLote');
        $idCliente = $this->input->post('idCliente');
        $comentario = $this->input->post('comentario');
        $modificado = date('Y-m-d H:i:s');
        $fechaVenc = $this->input->post('fechaVenc');


        $valida_tventa = $this->Asesor_model->getTipoVenta($idLote);//se valida el tipo de venta para ver si se va al nuevo status 3 (POSTVENTA)
        if($valida_tventa[0]['tipo_venta'] == 1 ){
            $statusContratacion = 3;
            $idMovimiento = 98;
        }else{
            $statusContratacion = 2;
            $idMovimiento = 74;
        }


        $arreglo = array();
        $arreglo["idStatusContratacion"] = $statusContratacion;
        $arreglo["idMovimiento"] = $idMovimiento;
        $arreglo["comentario"] = $comentario;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");


        $arreglo2 = array();
        $arreglo2["idStatusContratacion"] = $statusContratacion;
        $arreglo2["idMovimiento"] = $idMovimiento;
        $arreglo2["nombreLote"] = $nombreLote;
        $arreglo2["comentario"] = $comentario;
        $arreglo2["usuario"] = $this->session->userdata('id_usuario');
        $arreglo2["perfil"] = $this->session->userdata('id_rol');
        $arreglo2["modificado"] = date("Y-m-d H:i:s");
        $arreglo2["fechaVenc"] = $fechaVenc;
        $arreglo2["idLote"] = $idLote;
        $arreglo2["idCondominio"] = $idCondominio;
        $arreglo2["idCliente"] = $idCliente;


        $validate = $this->Asesor_model->validateSt2($idLote);
        if ($validate == 1) {

            if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                $data['message'] = 'OK';
                echo json_encode($data);

            } else {
                $data['message'] = 'ERROR';
                echo json_encode($data);
            }

        } else {
            $data['message'] = 'FALSE';
            echo json_encode($data);
        }

    }

    public function editar_registro_loteRevision_eliteAcontraloria5_proceceso2_2()
    {
        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $nombreLote = $this->input->post('nombreLote');
        $idCliente = $this->input->post('idCliente');
        $comentario = $this->input->post('comentario');
        $modificado = date('Y-m-d H:i:s');
        $fechaVenc = $this->input->post('fechaVenc');

        $arreglo = array();
        $arreglo["idStatusContratacion"] = 2;
        $arreglo["idMovimiento"] = 93;
        $arreglo["comentario"] = $comentario;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");


        $arreglo2 = array();
        $arreglo2["idStatusContratacion"] = 2;
        $arreglo2["idMovimiento"] = 93;
        $arreglo2["nombreLote"] = $nombreLote;
        $arreglo2["comentario"] = $comentario;
        $arreglo2["usuario"] = $this->session->userdata('id_usuario');
        $arreglo2["perfil"] = $this->session->userdata('id_rol');
        $arreglo2["modificado"] = date("Y-m-d H:i:s");
        $arreglo2["fechaVenc"] = $fechaVenc;
        $arreglo2["idLote"] = $idLote;
        $arreglo2["idCondominio"] = $idCondominio;
        $arreglo2["idCliente"] = $idCliente;


        $validate = $this->Asesor_model->validateSt2($idLote);

        if ($validate == 1) {

            if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                $data['message'] = 'OK';
                echo json_encode($data);

            } else {
                $data['message'] = 'ERROR';
                echo json_encode($data);
            }

        } else {
            $data['message'] = 'FALSE';
            echo json_encode($data);
        }


    }

    function getregistrosClientes()
    {
        $objDatos = json_decode(file_get_contents("php://input"));
        $dato = $this->registrolote_modelo->registroCliente();
        //$data2= $this->registrolote_modelo->getReferenciasCliente();

        for ($i = 0; $i < count($dato); $i++) {
            $data[$i]['id_cliente'] = $dato[$i]->id_cliente;
            $data[$i]['id_asesor'] = $dato[$i]->id_asesor;
            $data[$i]['id_coordinador'] = $dato[$i]->id_coordinador;
            $data[$i]['id_gerente'] = $dato[$i]->id_gerente;
            $data[$i]['id_sede'] = $dato[$i]->id_sede;
            $data[$i]['nombre'] = $dato[$i]->nombre;
            $data[$i]['apellido_paterno'] = $dato[$i]->apellido_paterno;
            $data[$i]['apellido_materno'] = $dato[$i]->apellido_materno;
            $data[$i]['personalidad_juridica'] = ($dato[$i]->personalidad_juridica == "") ? "N/A" : $dato[$i]->personalidad_juridica;
            $data[$i]['nacionalidad'] = ($dato[$i]->nacionalidad == "") ? "N/A" : $dato[$i]->nacionalidad;
            $data[$i]['rfc'] = ($dato[$i]->rfc == "") ? "N/A" : $dato[$i]->rfc;
            $data[$i]['curp'] = ($dato[$i]->curp == "") ? "N/A" : $dato[$i]->curp;
            $data[$i]['correo'] = ($dato[$i]->correo == "") ? "N/A" : $dato[$i]->correo;
            $data[$i]['telefono1'] = ($dato[$i]->telefono1 == "") ? "N/A" : $dato[$i]->telefono1;
            $data[$i]['telefono2'] = ($dato[$i]->telefono2 == "") ? "N/A" : $dato[$i]->telefono2;
            $data[$i]['telefono3'] = ($dato[$i]->telefono3 == "") ? "N/A" : $dato[$i]->telefono3;
            $data[$i]['fecha_nacimiento'] = ($dato[$i]->fecha_nacimiento == "") ? "N/A" : $dato[$i]->fecha_nacimiento;
            $data[$i]['lugar_prospeccion'] = ($dato[$i]->lugar_prospeccion == "") ? "N/A" : $dato[$i]->lugar_prospeccion;
            $data[$i]['medio_publicitario'] = ($dato[$i]->medio_publicitario == "") ? "N/A" : $dato[$i]->medio_publicitario;
            $data[$i]['otro_lugar'] = ($dato[$i]->otro_lugar == "") ? "N/A" : $dato[$i]->otro_lugar;
            $data[$i]['plaza_venta'] = ($dato[$i]->plaza_venta == "") ? "N/A" : $dato[$i]->plaza_venta;
            $data[$i]['tipo'] = ($dato[$i]->tipo == "") ? "N/A" : $dato[$i]->tipo;
            $data[$i]['estado_civil'] = ($dato[$i]->estado_civil == "") ? "N/A" : $dato[$i]->estado_civil;
            $data[$i]['regimen_matrimonial'] = ($dato[$i]->regimen_matrimonial == "") ? "N/A" : $dato[$i]->regimen_matrimonial;
            $data[$i]['nombre_conyuge'] = ($dato[$i]->nombre_conyuge == "") ? "N/A" : $dato[$i]->nombre_conyuge;
            $data[$i]['domicilio_particular'] = ($dato[$i]->domicilio_particular == "") ? "N/A" : $dato[$i]->domicilio_particular;
            $data[$i]['tipo_vivienda'] = ($dato[$i]->tipo_vivienda == "") ? "N/A" : $dato[$i]->tipo_vivienda;
            $data[$i]['ocupacion'] = ($dato[$i]->ocupacion == "") ? "N/A" : $dato[$i]->ocupacion;
            $data[$i]['empresa'] = ($dato[$i]->empresa == "") ? "N/A" : $dato[$i]->empresa;
            $data[$i]['puesto'] = ($dato[$i]->puesto == "") ? "N/A" : $dato[$i]->puesto;
            $data[$i]['edadFirma'] = ($dato[$i]->edadFirma == "") ? "N/A" : $dato[$i]->edadFirma;
            $data[$i]['antiguedad'] = ($dato[$i]->antiguedad == "") ? "N/A" : $dato[$i]->antiguedad;
            $data[$i]['domicilio_empresa'] = ($dato[$i]->domicilio_empresa == "") ? "N/A" : $dato[$i]->domicilio_empresa;
            $data[$i]['telefono_empresa'] = ($dato[$i]->telefono_empresa == "") ? "N/A" : $dato[$i]->telefono_empresa;
            $data[$i]['noRecibo'] = ($dato[$i]->noRecibo == "") ? "N/A" : $dato[$i]->noRecibo;
            $data[$i]['engancheCliente'] = ($dato[$i]->engancheCliente == "") ? "N/A" : $dato[$i]->engancheCliente;
            $data[$i]['concepto'] = ($dato[$i]->concepto == "") ? "N/A" : $dato[$i]->concepto;
            $data[$i]['fechaEnganche'] = ($dato[$i]->fechaEnganche == "") ? "N/A" : $dato[$i]->fechaEnganche;
            $data[$i]['idTipoPago'] = ($dato[$i]->idTipoPago == "") ? "N/A" : $dato[$i]->idTipoPago;
            $data[$i]['expediente'] = ($dato[$i]->expediente == "") ? "N/A" : $dato[$i]->expediente;
            $data[$i]['status'] = ($dato[$i]->status == "") ? "N/A" : $dato[$i]->status;
            $data[$i]['idLote'] = ($dato[$i]->idLote == "") ? "N/A" : $dato[$i]->idLote;
            $data[$i]['fechaApartado'] = ($dato[$i]->fechaApartado == "") ? "N/A" : $dato[$i]->fechaApartado;
            $data[$i]['fechaVencimiento'] = ($dato[$i]->fechaVencimiento == "") ? "N/A" : $dato[$i]->fechaVencimiento;
            $data[$i]['usuario'] = ($dato[$i]->usuario == "") ? "N/A" : $dato[$i]->usuario;
            $data[$i]['idCondominio'] = ($dato[$i]->idCondominio == "") ? "N/A" : $dato[$i]->idCondominio;
            $data[$i]['fecha_creacion'] = ($dato[$i]->fecha_creacion == "") ? "N/A" : $dato[$i]->fecha_creacion;
            $data[$i]['creado_por'] = ($dato[$i]->creado_por == "") ? "N/A" : $dato[$i]->creado_por;
            $data[$i]['fecha_modificacion'] = ($dato[$i]->fecha_modificacion == "") ? "N/A" : $dato[$i]->fecha_modificacion;
            $data[$i]['modificado_por'] = ($dato[$i]->modificado_por == "") ? "N/A" : $dato[$i]->modificado_por;
            $data[$i]['nombreCondominio'] = ($dato[$i]->nombreCondominio == "") ? "N/A" : $dato[$i]->nombreCondominio;
            $data[$i]['nombreResidencial'] = ($dato[$i]->nombreResidencial == "") ? "N/A" : $dato[$i]->nombreResidencial;
            $data[$i]['nombreLote'] = ($dato[$i]->nombreLote == "") ? "N/A" : $dato[$i]->nombreLote;
            $data[$i]['asesor'] = ($dato[$i]->asesor == "") ? "N/A" : $dato[$i]->asesor;
            $data[$i]['gerente'] = ($dato[$i]->gerente == "") ? "N/A" : $dato[$i]->gerente;
            $data[$i]['coordinador'] = ($dato[$i]->coordinador == "") ? "N/A" : $dato[$i]->coordinador;

            $dataRef = $this->registrolote_modelo->getReferenciasCliente($dato[$i]->id_cliente);
            $dataPrCon = $this->registrolote_modelo->getPrimerContactoCliente($dato[$i]->lugar_prospeccion);
            $dataVenComp = $this->registrolote_modelo->getVentasCompartidas($dato[$i]->id_cliente);
            $data[$i]['primerContacto'] = $dataPrCon[0]->nombre;

            for ($n = 0; $n < count($dataRef); $n++) {
                $data[$i]['idreferencia' . ($n + 1)] = $dataRef[$n]->id_referencia;
                $data[$i]['referencia' . ($n + 1)] = $dataRef[$n]->nombre;
                $data[$i]['telreferencia' . ($n + 1)] = $dataRef[$n]->telefono;
            }
            if (count($dataVenComp) <= 0) {
                $data[$i]['asesor2'] = "N/A";
                $data[$i]['asesor3'] = "N/A";
            } else {
                for ($a = 0; $a < count($dataVenComp); $a++) {
                    if (count($dataVenComp) > 0) {
                        $data[$i]['asesor' . ($a + 1 + 1)] = $dataVenComp[$a]->nombre;
                    } else {
                        $data[$i]['asesor' . ($a + 1 + 1)] = "";
                    }

                }
            }
        }
        if ($data != null) {

            echo json_encode($data);

        } else {

            echo json_encode(array());
        }
    }

    /*----------------------------CARPETAS--------------------------------------*/
    public function saveCarpetas()
    {
        $fileTmpPath = $_FILES['file-upload']['tmp_name'];
        $fileName = $_FILES['file-upload']['name'];
        $fileSize = $_FILES['file-upload']['size'];
        $fileType = $_FILES['file-upload']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // echo $fileTmpPath = $_FILES['file-upload']['name'];
        $data = [
            'nombre' => $this->input->post("nombre"),
            'descripcion' => $this->input->post("desc"),
            'archivo' => $newFileName,
            'estatus' => 1,
            'usuario' => 666,
            'fecha_creacion' => date("Y-m-d H:i:s"),
            'fecha_modificacion' => date("Y-m-d H:i:s")
        ];


        $uploadFileDir = './static/documentos/carpetas/';
        $dest_path = $uploadFileDir . $newFileName;


        $dest_path = $uploadFileDir . $newFileName;
        move_uploaded_file($fileTmpPath, $dest_path);

        $response = $this->Asesor_model->saveCarpeta($data);


        echo json_encode($response);

    }

    public function updateCarpetas($val)
    {
        if ($val == 2) {

            unlink("./static/documentos/carpetas/" . $this->input->post("filename"));
            $fileTmpPath = $_FILES['file-uploadE']['tmp_name'];
            $fileName = $_FILES['file-uploadE']['name'];
            $fileSize = $_FILES['file-uploadE']['size'];
            $fileType = $_FILES['file-uploadE']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './static/documentos/carpetas/';
            $dest_path = $uploadFileDir . $newFileName;


            $dest_path = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $dest_path);

            $data = [
                'nombre' => $this->input->post("nombreE"),
                'descripcion' => $this->input->post("descripcionE"),
                'archivo' => $newFileName,
                'estatus' => $this->input->post("estatus")
            ];

            $response = $this->Asesor_model->updateCarpeta($data, $this->input->post("idCarpeta"));
            echo json_encode($response);

        } else {

            $data = [
                'nombre' => $this->input->post("nombreE"),
                'descripcion' => $this->input->post("descripcionE"),
                'estatus' => $this->input->post("estatus")
            ];

            $response = $this->Asesor_model->updateCarpeta($data, $this->input->post("idCarpeta"));
            echo json_encode($response);
        }


    }

    public function getInfoCarpeta($id_carpeta)
    {
        $data = $this->Asesor_model->getInfoCarpeta($id_carpeta);
        echo json_encode($data);
    }

    public function getCarpetas()
    {

        $data['data'] = $this->Asesor_model->getCarpetas()->result_array();

        echo json_encode($data);

    }

    /*---------------------------------FIN CARPETAS------------------------------------------------*/


    public function presentacionesCarpetas()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $this->load->view('template/header');
        $this->load->view("asesor/carpetas_view", $datos);
    }

    public function AdminCarpetas()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $this->load->view('template/header');
        $this->load->view("asesor/carpetas_admin", $datos);
    }

    public function getAllFoldersPDF()
    {
        $data = $this->Asesor_model->getAllFoldersPDF();
        if ($data != null) {

            echo json_encode($data);

        } else {

            echo json_encode(array());
        }
    }


    public function getAllFoldersManual()
    {
        $data = $this->Asesor_model->getAllFoldersManual();
        if ($data != null) {

            echo json_encode($data);

        } else {

            echo json_encode(array());
        }
    }


    public function return1aaj()
    {

        $idCliente = $this->input->post('idCliente');
        $nombreLote = $this->input->post('nombreLote');
        $idLote = $this->input->post('idLote');
        $idCondominio = $this->input->post('idCondominio');
        $comentario = $this->input->post('comentario');
        $fechaVenc = $this->input->post('fechaVenc');


        $arreglo = array();
        $arreglo["idStatusContratacion"] = 6;
        $arreglo["idMovimiento"] = 97;
        $arreglo["comentario"] = $comentario;
        $arreglo["usuario"] = $this->session->userdata('id_usuario');
        $arreglo["perfil"] = $this->session->userdata('id_rol');
        $arreglo["modificado"] = date("Y-m-d H:i:s");


        $arreglo2 = array();
        $arreglo2["idStatusContratacion"] = 6;
        $arreglo2["idMovimiento"] = 97;
        $arreglo2["nombreLote"] = $nombreLote;
        $arreglo2["comentario"] = $comentario;
        $arreglo2["usuario"] = $this->session->userdata('id_usuario');
        $arreglo2["perfil"] = $this->session->userdata('id_rol');
        $arreglo2["modificado"] = date("Y-m-d H:i:s");
        $arreglo2["fechaVenc"] = $fechaVenc;
        $arreglo2["idLote"] = $idLote;
        $arreglo2["idCondominio"] = $idCondominio;
        $arreglo2["idCliente"] = $idCliente;


        $validate = $this->Asesor_model->validateSt2($idLote);

        if ($validate == 1) {

            if ($this->Asesor_model->updateSt($idLote, $arreglo, $arreglo2) == TRUE) {
                $data['message'] = 'OK';
                echo json_encode($data);

            } else {
                $data['message'] = 'ERROR';
                echo json_encode($data);
            }

        } else {
            $data['message'] = 'FALSE';
            echo json_encode($data);
        }


    }

    public function get_info_tabla()
    {
        $datos = file_get_contents('php://input');
        $filtros = array();
        foreach ($_POST as $key => $filtro) {
            array_push($filtros, array($key => $filtro));
        }
        $data = $this->Asesor_model->get_info_tabla($filtros);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }


    /**nuevas funciones al 090421**/
    public function getClientsByMKTDG()
    {
        $data['data'] = $this->Asesor_model->getClientsByMKTDG();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function getEvidenciaGte()
    {
        $data['data'] = $this->Asesor_model->getEvidenciaGte();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    function addEvidenceToCobranza()
    {
        $comentario = ($this->input->post('comentario_0') != '' ? $this->input->post('comentario_0') : '');
        $id_cliente = $this->input->post('idCliente');
        $id_lote = $this->input->post('idLote');
        $id_sol = $this->input->post('id_sol');
        $id_rolAut = 32;

        $fileTmpPath = $_FILES['docArchivo1']['tmp_name'];
        $fileName = $_FILES['docArchivo1']['name'];
        $fileSize = $_FILES['docArchivo1']['size'];
        $fileType = $_FILES['docArchivo1']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        /*$data = [
            'idCliente' => $id_cliente,
            'idLote' => $id_lote,
            'id_sol' => $id_sol,
            'id_rolAut' => $id_rolAut,
            'estatus' => 1,
            'evidencia' => $newFileName,
            'comentario_autorizacion' => $comentario,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "fecha_modificado" => date("Y-m-d H:i:s"),
            "estatus_particular" => 1
        ];*/
        $data = array(
            'idCliente' => $id_cliente,
            'idLote' => $id_lote,
            'id_sol' => $id_sol,
            'id_rolAut' => $id_rolAut,
            'estatus' => 2,
            'evidencia' => $newFileName,
            'comentario_autorizacion' => $comentario,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "fecha_modificado" => date("Y-m-d H:i:s"),
            "estatus_particular" => 1
        );


        $data_insert = $this->Asesor_model->insertEvidencia($data);
        $last_id = $this->db->insert_id();
        $data_historial = array(
            'id_evidencia' => $last_id,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'estatus' => 2,
            'creado_por' => $this->session->userdata('id_usuario'),
            'evidencia' => $newFileName,
            'comentario_autorizacion' => $comentario
        );
        /*$data_historial = [
            'id_evidencia' => $last_id,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'estatus' => 1,
            'creado_por' => $this->session->userdata('id_usuario'),
            'evidencia' => $newFileName,
            'comentario_autorizacion' => $comentario
        ];*/
        $this->Asesor_model->insertHistorialEvidencia($data_historial);
        if ($data_insert) {
            $uploadFileDir = './static/documentos/evidencia_mktd/';
            $dest_path = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $dest_path);
            echo json_encode($data_insert);
        }
    }

    public function getAutsEvidencia($id_evidencia)
    {
        $data = $this->Asesor_model->getAutsEvidencia($id_evidencia);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function getAutsForCobranza()
    {
        $data['data'] = $this->Asesor_model->getAutsForCobranza();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function getSolicitudEvidencia($id_evidencia)
    {
        $data = $this->Asesor_model->getSolicitudEvidencia($id_evidencia);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function getControversy()
    {
        $data['data'] = $this->Asesor_model->getControversy();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function actualizaSolEvi()
    {
        $accion = $this->input->post('accion');
        $comentario_cobranza = ($this->input->post('comentario_cobranza') != '') ? $this->input->post('comentario_cobranza') : '';
        $nombreLote = $this->input->post('nombreLote');
        $idLote = $this->input->post('idLote');
        $id_evidencia = $this->input->post('id_evidencia');
        $evidencia_file = $this->input->post('evidencia_file');

        if ($accion == 0)//rechazo a gerente
        {
            $rol = 28;
            $estatus = 10;
        } elseif ($accion == 1)//avanza a contraloria
        {
            $rol = 32;
            $estatus = 2;
        }
        $data_update = array(
            'id_rolAut' => $rol,
            'estatus' => $estatus,
            'comentario_autorizacion' => $comentario_cobranza,
            'fecha_modificado' => date("Y-m-d H:i:s"),
        );

        $data_insert_historial = array(
            'id_evidencia' => $id_evidencia,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'estatus' => $estatus,
            'creado_por' => $this->session->userdata('id_usuario'),
            'evidencia' => $evidencia_file,
            'comentario_autorizacion' => $comentario_cobranza
        );


        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_insert_historial);

        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            if ($_POST['accion'] == 3) {
                $type = 1;
            } else {
                $type = 2;
            }
            echo json_encode(1);
        } else {
            $type = 3;
            echo json_encode(0);
        }

    }

    function getAutsForContraloria()
    {
        $data['data'] = $this->Asesor_model->getAutsForContraloria();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    function setControversias()
    {
        $json['resultado'] = FALSE;
        $json['error'] = '';

        $idLote = $this->input->post("inp_lote");
        $idCliente = $this->input->post("handlerIdCliente");
        $data = $this->Asesor_model->verificarMarketing($idLote);
        if ($data != null) {
            $data2 = $this->Asesor_model->verificarControversia($idLote);
            if ($data2 == null) {
                $data_insert = array('id_lote' => $idLote, 'tipo' => $this->input->post("controversy_type"), 'creado_por' => $this->session->userdata('id_usuario'), 'id_cliente' => $idCliente);
                $this->Asesor_model->insertControversia($data_insert);
                $json['resultado'] = TRUE;
            } else
                $json['error'] = 'El lote ya ha sido registrado o no existe.';
        } else
            $json['error'] = 'El lote pertenece a Marketing Digital o no existe.';
        echo json_encode($json);
    }

    function actualizaSolEviCN()
    {
        $accion = $this->input->post('accion');
        $comentario_contra = ($this->input->post('comentario_contra') != '') ? $this->input->post('comentario_contra') : '';
        $nombreLote = $this->input->post('nombreLote');
        $idLote = $this->input->post('idLote');
        $id_evidencia = $this->input->post('id_evidencia');
        $evidencia_file = $this->input->post('evidencia_file');

        if ($accion == 0)//rechazo a cobranza
        {
            $rol = 28;
            $estatus = 20;
        } elseif ($accion == 1)//avanza a contraloria
        {
            $rol = 32;
            $estatus = 3;
        }
        $data_update = array(
            'id_rolAut' => $rol,
            'estatus' => $estatus,
            'comentario_autorizacion' => $comentario_contra,
            'fecha_modificado' => date("Y-m-d H:i:s"),
        );

        $data_insert_historial = array(
            'id_evidencia' => $id_evidencia,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'estatus' => $estatus,
            'creado_por' => $this->session->userdata('id_usuario'),
            'evidencia' => $evidencia_file,
            'comentario_autorizacion' => $comentario_contra
        );


        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_insert_historial);
        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            if ($_POST['accion'] == 3) {
                $type = 1;
            } else {
                $type = 2;
            }
            echo json_encode(1);
        } else {
            $type = 3;
            echo json_encode(0);
        }

    }

    function updateEvidenceChat()
    {
        $comentario_E = ($this->input->post('comentario_E') != '' ? $this->input->post('comentario_E') : '');
        $id_evidencia = $this->input->post('id_evidenciaE');
        $evidencia_file = $this->input->post('evidenciaE');
        $previousImg = $this->input->post('previousImg');


        $fileTmpPath = $_FILES['evidenciaE']['tmp_name'];
        $fileName = $_FILES['evidenciaE']['name'];
        $fileSize = $_FILES['evidenciaE']['size'];
        $fileType = $_FILES['evidenciaE']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;


        /*continuar aquí*/
        /*borrar la imagen anterior*/
        $pathImg = FCPATH . 'static/documentos/evidencia_mktd/';
        $resp['img_status'] = delete_img($pathImg, $previousImg);


        $data_update = array(
            "evidencia" => $newFileName,
            "comentario_autorizacion" => $comentario_E,
            "fecha_modificado" => date('Y-m-d H:i:s'),
            "estatus" => 1
        );

        $data_historial = array(
            "id_evidencia" => $id_evidencia,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "estatus" => 1,
            "creado_por" => $this->session->userdata('id_usuario'),
            "evidencia" => $newFileName,
            "comentario_autorizacion" => $comentario_E
        );

        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_historial);

        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            $resp['exe'] = 1;
            $uploadFileDir = './static/documentos/evidencia_mktd/';
            $dest_path = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $dest_path);
        } else {
            $resp['exe'] = 0;
        }
        if ($resp != null) {
            echo json_encode($resp);
        } else {
            echo json_encode(array());
        }
    }

    function rechazaAGte()
    {
        $id_evidencia = $this->input->post('id_evidencia');
        $evidencia = $this->input->post('evidencia');
        $nombreLote = $this->input->post('nombreLote');
        $comentario = $this->input->post('comentario');


        $data_update = array(
            "evidencia" => $evidencia,
            "fecha_modificado" => date("Y-m-d H:i:s"),
            "estatus" => 10,
            "comentario_autorizacion" => $comentario
        );

        $data_historial = array(
            "id_evidencia" => $id_evidencia,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "estatus" => 10,
            "creado_por" => $this->session->userdata('id_usuario'),
            "evidencia" => $evidencia,
            "comentario_autorizacion" => $comentario
        );

        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_historial);

        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            $resp['exe'] = 1;
        } else {
            $resp['exe'] = 0;
        }
        if ($resp != null) {
            echo json_encode($resp);
        } else {
            echo json_encode(array());
        }
    }

    function updateEvidenceChatCB()
    {
        $comentario_E = ($this->input->post('comentario_E') != '' ? $this->input->post('comentario_E') : '');
        $id_evidencia = $this->input->post('id_evidenciaE');
        $evidencia_file = $this->input->post('evidenciaE');
        $previousImg = $this->input->post('previousImg');


        $fileTmpPath = $_FILES['evidenciaE']['tmp_name'];
        $fileName = $_FILES['evidenciaE']['name'];
        $fileSize = $_FILES['evidenciaE']['size'];
        $fileType = $_FILES['evidenciaE']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        $pathImg = FCPATH . 'static/documentos/evidencia_mktd/';
        $resp['img_status'] = delete_img($pathImg, $previousImg);

        $data_update = array(
            "evidencia" => $newFileName,
            "comentario_autorizacion" => $comentario_E,
            "fecha_modificado" => date("Y-m-d H:i:s"),
            "estatus" => 2
        );

        $data_historial = array(
            "id_evidencia" => $id_evidencia,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "estatus" => 2,
            "creado_por" => $this->session->userdata('id_usuario'),
            "evidencia" => $newFileName,
            "comentario_autorizacion" => $comentario_E
        );
        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_historial);

        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            $resp['exe'] = 1;
            $uploadFileDir = './static/documentos/evidencia_mktd/';
            $dest_path = $uploadFileDir . $newFileName;
            move_uploaded_file($fileTmpPath, $dest_path);
        } else {
            $resp['exe'] = 0;
        }
        if ($resp != null) {
            echo json_encode($resp);
        } else {
            echo json_encode(array());
        }
    }

    function sendMailReportER()
    {
        $sedes_array = $this->getSedes();
        $correo = '';

        for ($i = 0; $i < count($sedes_array); $i++) {
            
            $correos_entregar = array();
            $elementos_correo = array();
            $datos_correo[] = array();
            $datos_encabezados_tabla = '';
            $datos_etiquetas = null;
            $comentario_general = '';

            $data_eviRec = $this->Asesor_model->getEviRecBySede($sedes_array[$i]['id_sede']);
            switch ($sedes_array[$i]['id_sede']) {
                case 1:
                    //SLP
                   // $correo = 'bertha.magos@ciudadmaderas.com';
                    break;
                case 2:
                    //QRO
                    //$correo = 'estefania.oceguera@ciudadmaderas.com';
                    break;
                case 3:
                    //PEN
                    //$correo = 'maricela.rico@ciudadmaderas.com';
                    break;
                case 4:
                    //CDMX
                    //$correo = 'sergio.colina@ciudadmaderas.com';
                    break;
                case 5:
                    //LEO
                    //$correo = 'maria.licea@ciudadmaderas.com';
                    break;
                case 6:
                    //CAN
                    //$correo = 'villanueva@ciudadmaderas.com';
                    break;
                case 7:
                    //US
                    //$correo = 'programador.analista8@ciudadmaderas.com';
                    break;
                default:
                    //$correo = 'programador.analista8@ciudadmaderas.com';
                    break;
            }

            if (count($data_eviRec) > 0) {
                /***********************************************************************************
                *   Armado de parámetros a mandar a plantilla para creación de correo electrónico  *
                ***********************************************************************************/
                $correo = 'programador.analista18@ciudadmaderas.com';
                array_push($correos_entregar, $correo);

                $elementos_correo = array('setFrom'  =>  Elementos_Correos_Asesor::SET_FROM_EMAIL,
                                         'Subject'  =>  '['.strtoupper($sedes_array[$i]['abreviacion']).']'.
                                                        Elementos_Correos_Asesor::ASUNTO_CORREO_TABLA_EVIDENCIAS_RECHAZADAS_ASESOR . $correo);

                $datos_correo = $data_eviRec;

                $datos_encabezados_tabla = Elementos_Correos_Asesor::ETIQUETAS_ENCABEZADO_TABLA_EVIDENCIAS_RECHAZADAS_ASESOR;
                
                //$data_enviar_mail = $this->notifyRejEv($correo, $data_eviRec, $sedes_array[$i]['abreviacion']);

                $comentario_general = Elementos_Correos_Asesor::EMAIL_EVIDENCIAS_RECHAZADAS_ASESOR . '<br>' . (!isset($comentario) ? '' : '<br>'. $comentario);

                $plantilla_correo = new plantilla_dinamica_correo;
                /************************************************************************************************************************/
                $envio_correo = $plantilla_correo->crearPlantillaCorreo($correos_entregar, $elementos_correo, $datos_correo, 
                                                                        $datos_encabezados_tabla, $datos_etiquetas, $comentario_general);
                if ($envio_correo > 0) {
                    $data_request['msg'] = 'Correo enviado correctamente [' . $sedes_array[$i]['abreviacion'] . ']';
                } else {
                    $data_request['msg'] = 'Correo no enviado [' . $sedes_array[$i]['abreviacion'] . '] : [' . $envio_correo . ']';
                }
            }else {
                $data_request['msg'] = 'No hay registros para enviar un correo en [' . $sedes_array[$i]['abreviacion'] . ']';
            }
            if ($data_request != null) {
                echo json_encode($data_request);
            }else {
                echo json_encode(array());
            }
        }
    }

    public function notifyRejEv($correo, $data_eviRec, $sede)
    {
        // $correo_new = 'programador.analista8@ciudadmaderas.com';/*se coloca el correo de testeo para desarrollo*/
        $correoDir = $dataUser[0]->correo;


        $mail = $this->phpmailer_lib->load();
     
        $mail->setFrom('no-reply@ciudadmaderas.com', 'Ciudad Maderas');
        $mail->addAddress($correo_new);
        // $mail->addCC('erick_eternal@live.com.mx');
        //$mail->addBCC('copia_oculta@outlook.com');

        $mail->Subject = utf8_decode('[' . strtoupper($sede) . '][REPORTE] EVIDENCIAS RECHAZADAS PARA:' . $correo);
        $mail->isHTML(true);

        $mailContent = "<html><head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
          <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'> 
          <style media='all' type='text/css'>
              .encabezados{
                  text-align: center;
                  padding-top:  1.5%;
                  padding-bottom: 1.5%;
              }
              .encabezados a{
                  color: #234e7f;
                  font-weight: bold;
              }
              
              .fondo{
                  background-color: #234e7f;
                  color: #fff;
              }
              
              h4{
                  text-align: center;
              }
              p{
                  text-align: right;
              }
              strong{
                  color: #234e7f;
              }
          </style>
        </head>
        <body>
          <img src='" . base_url() . "static/images/mailER/header9@4x.png' width='100%'>
          <table align='center' cellspacing='0' cellpadding='0' border='0' width='100%'>
              <tr colspan='3'>
                <td class='navbar navbar-inverse' align='center'>
                  <table width='750px' cellspacing='0' cellpadding='3' class='container'>
                      <tr class='navbar navbar-inverse encabezados'><td>
                          <p><a href='#'>SISTEMA DE CONTRATACIÓN</a></p>
                      </td></tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td border=1 bgcolor='#FFFFFF' align='center'> 
                    <h3>¡ Buenos días estimad@ !</h3><br> <br>
                    
                    <p style='padding: 10px 90px;text-align: justify;'>¿Cómo estás?, espero que bien, te adjunto el reporte semanal de las evidencias rechazadas por
                        <b>cobranza/contraloria</b>, te invito a leer las observaciones. Recuerda que deben ser corregidas a más
                        tardar los jueves a las 12:00 PM, con esto ayudas a que el proceso en cobranza sea en tiempo y forma,
                        dando como resultado el cobro a tiempo de las comisiones.
                    </p><br><br>
                    
                    
                </td>
              </tr>
              <tr>
                <td border=1 bgcolor='#FFFFFF' align='center'>  
                  <center><table id='reporyt' cellpadding='0' cellspacing='0' border='1' width ='100%' style class='darkheader'>
                    <tr class='active' style='text-align: center'>
                      <th>Solicitante</th>   
                      <th>Lote</th>   
                      <th>comentario</th>   
                      <th>Fecha/Hora</th>   
                    </tr>";
        for ($p = 0; $p < count($data_eviRec); $p++) {
            $mailContent .= '<tr>';
            $mailContent .= '    <td><center>' . $data_eviRec[$p]['nombreSolicitante'] . '</center></td>';
            $mailContent .= '    <td><center>' . $data_eviRec[$p]['nombreLote'] . '</center></td>';
            $mailContent .= '    <td><center>' . $data_eviRec[$p]['comentario_autorizacion'] . '</center></td>';
            $mailContent .= '    <td><center>' . $data_eviRec[$p]['fecha_creacion'] . '</center></td>';
            $mailContent .= '</tr>';
        }
        $mailContent .= "</table></center>
                    <br><br>
                </td>
              </tr>
          </table>
          <img src='" . base_url() . "static/images/mailER/footer@4x.png' width='100%'>
          </body></html>";

        $mail->Body = utf8_decode($mailContent);
        if ($mail->send()) {
            return 1;
        } else {
            return $mail->ErrorInfo;
        }
    }

    function getSedes()
    {
        $data = $this->Asesor_model->getSedes();
        if ($data != null) {
            return $data;
        } else {
            return array();
        }
    }


    /***************************/

    function deleteFromListMKTD()
    {
        $idLoteDMKTD = $this->input->post('idLoteDMKTD');
        $comentario_delete = $this->input->post('comentario_delete');
        $data_insert = array(
            'idLote' => $idLoteDMKTD,
            'observacion' => $comentario_delete,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'creado_por' => $this->session->userdata('id_usuario'));

        $data_update_cl = array(
            'lugar_prospeccion' => 11,
            'otro_lugar' => '');


        $data_cliente = $this->Asesor_model->getClientByLote($idLoteDMKTD);
        $id_cliente = $data_cliente[0]['id_cliente'];
        $data_prospecto = $this->Asesor_model->getIdProspectByCl($id_cliente);
        $id_prospecto = ($data_prospecto[0]['id_prospecto'] != '' || $data_prospecto[0]['id_prospecto'] != null) ? $data_prospecto[0]['id_prospecto'] : '';

        if (count($data_cliente) >= 1) {
            $update_cliente = $this->Asesor_model->updateClienteLP($data_update_cl, $id_cliente);
            if ($update_cliente >= 1) {
                $data_return_insert = $this->Asesor_model->insertRegDelMKTDFList($data_insert);
                if ($data_return_insert >= 1) {
                    $data['exe'] = 1;
                    $data['msg'] = 'Se ha eliminado correctamente';

                    /************/
                    if ($id_prospecto != '' || $id_prospecto != null) {
                        $this->Asesor_model->updateProspectLP($data_update_cl, $id_prospecto);
                    }
                    /***********/
                } else {
                    $data['exe'] = 1;
                    $data['msg'] = 'Se actualizó el cliente pero no se insertó el historial';
                }
            } else {
                $data['exe'] = 0;
                $data['msg'] = 'Ocurrió un error al ejecutar la operación';
            }

        } else {
            $data['exe'] = 0;
            $data['msg'] = 'No se encontró el cliente relacionado a este lote [' . $idLoteDMKTD . ']';
        }

        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }

    }

    function getDeletedLotesEV()
    {
        $data['data'] = $this->Asesor_model->getDeletedLotesEV();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
    }

    public function inventoryByLote()
    {
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        //$datos["registrosLoteContratacion"] = $this->registrolote_modelo->registroLote();
        $datos["residencial"] = $this->Asesor_model->get_proyecto_lista();
        $this->load->view('template/header');
        $this->load->view("contratacion/inventoryByLote", $datos);
    }

    function envioContraloria()
    {
        $id_evidencia = $this->input->post('id_evidencia');
        $evidencia = $this->input->post('evidencia');
        $nombreLote = $this->input->post('nombreLote');
        $comentario = $this->input->post('comentario');
        $data_update = array(
            "evidencia" => $evidencia,
            "fecha_modificado" => date("Y-m-d H:i:s"),
            "estatus" => 2,
            "comentario_autorizacion" => $comentario
        );

        $data_historial = array(
            "id_evidencia" => $id_evidencia,
            "fecha_creacion" => date("Y-m-d H:i:s"),
            "estatus" => 2,
            "creado_por" => $this->session->userdata('id_usuario'),
            "evidencia" => $evidencia,
            "comentario_autorizacion" => $comentario
        );
        $dataUpdAut = $this->Asesor_model->updateSolEvidencia($id_evidencia, $data_update);
        $dataInsertHA = $this->Asesor_model->insertHistSolEv($data_historial);

        if ($dataUpdAut >= 1 || $dataInsertHA >= 1) {
            $resp['exe'] = 1;
        } else {
            $resp['exe'] = 0;
        }
        if ($resp != null) {
            echo json_encode($resp);
        } else {
            echo json_encode(array());
        }
    }

    function getAsesores()
    {
        $data = $this->Asesor_model->getAsesores($this->session->userdata('id_usuario'));
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    function getAsesores2()
    {
        $data = $this->Asesor_model->getAsesores2($this->session->userdata('id_usuario'), $_POST['value']);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    function saveVentaCompartida()
    {
        $asesor1 = $_POST['asesor1'];
        $asesor2 = $_POST['asesor2'];
        $id_cliente = $_POST['id_cliente'];

        $count = 0;
        $arrAsesor = array($asesor1, $asesor2);

        if($_POST['ventaC'] == 'uno'){
            if($asesor2 != ''){
                $count = 2;
            }else{
                $count = 1;
            }
            for($x=0;$x<$count;$x++){
                $dataAsesor = $this->Asesor_model->getAsesorData($arrAsesor[$x]);
                $update = array(
                    "id_cliente" => $id_cliente,  
                    "id_asesor" =>  $dataAsesor->asesor,  
                    "id_coordinador" => $dataAsesor->coord,  
                    "id_gerente" => $dataAsesor->ger,  
                    "estatus" => 1,
                    "fecha_creacion" => date("Y-m-d H:i:s"),  
                    "creado_por" => $this->session->userdata('id_usuario'),  
                    "id_regional" => $dataAsesor->regional,  
                    "id_subdirector" => $dataAsesor->subdir 
                );
                $data = $this->Asesor_model->saveVentaCompartida($update);
                if($data == true){
                    $this->Asesor_model->updateFlagCompartida($id_cliente);
                }
            }
        }else{
            $data = $this->Asesor_model->updateFlagCompartida($id_cliente);
        }
        
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    public function viewGrafica()
    {
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $this->load->view('template/header');
        $this->load->view("asesor/grafica_comisiones", $datos);
    }

    public function expedientesRechazados(){
        /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        // $this->validateSession();
        $this->load->view('template/header');
        $this->load->view("asesor/contratosCancelados", $datos);
    }

    function getlotesRechazados(){
        $data = $this->Asesor_model->getlotesRechazados();
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    function getLineOfACG(){
        $objDatos = json_decode(file_get_contents("php://input"));
        $id_lote = $objDatos->lote;
        $data = $this->Asesor_model->getLineOfACG($id_lote);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }
    function getGerenteById(){
        $objDatos = json_decode(file_get_contents("php://input"));
        $id_gerente = $objDatos->gerente;
        $data = $this->Asesor_model->getGerenteById($id_gerente);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }
    function getCoordinadorById(){
        $objDatos = json_decode(file_get_contents("php://input"));
        $id_coordinador = $objDatos->coordinador;
        $data = $this->Asesor_model->getCoordinadorById($id_coordinador);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }
    function getAsesorById(){
        $objDatos = json_decode(file_get_contents("php://input"));
        $id_asesor = $objDatos->asesor;
        $data = $this->Asesor_model->getAsesorById($id_asesor);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    public function reporteAsesores(){
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        /*-------------------------------------------------------------------------------*/
        $this->load->view('template/header');
        $this->load->view("asesor/reporte_asesores_view", $datos);

    }

    public function getReporteAsesores(){
        $data['data'] = $this->Asesor_model->reporteAsesor()->result_array();
        echo json_encode($data);
    }

    function getFolderFile($documentType)
    {
        if ($documentType == 7) $folder = "static/documentos/cliente/corrida/";
        else if ($documentType == 8) $folder = "static/documentos/cliente/contrato/";
        else $folder = "static/documentos/cliente/expediente/";
        return $folder;
    }
}
?>