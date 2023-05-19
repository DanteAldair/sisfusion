<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Documentacion extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('Documentacion_model', 'General_model', 'Registrolote_modelo'));
        $this->load->library(array('session', 'form_validation', 'get_menu', 'phpmailer_lib'));
        $this->load->helper(array('url', 'form'));
        $this->load->database('default');
        date_default_timezone_set('America/Mexico_City');
    }
    
    public function documentacion() {
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $datos["residencial"] = $this->Registrolote_modelo->getResidencialQro();
        $this->load->view('template/header');
        $this->load->view("documentacion/documentacion_view", $datos);
    }

    public function subirArchivo() {
        $file = $_FILES["uploadedDocument"];
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $idLote = $this->input->post('idLote');
        $idDocumento = $this->input->post('idDocumento');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $documentName = "{$this->input->post('tituloDocumento')}.$fileExt";

        $folder = $this->getCarpetaDeArchivo($tipoDocumento);
        
        if ($tipoDocumento == 7) { // SE VA A SUBIR / REEMPLAZAR LA CORRIDA
            if ($fileExt == 'xlsx') {
                $this->actualizarRamaDeDocumento($file, $folder, $documentName, $idDocumento);
            } else {
                echo json_encode(3); // SE INTENTÓ SUBIR UN ARCHIVO DIFERENTE A UN .XLSX (CORRIDA)
            }
        } else { // SE VA A SUBIR EL EXPEDIENTE O EL CONTRATO
            $this->actualizarRamaDeDocumento($file, $folder, $documentName, $idDocumento);
        }
    }

    function actualizarRamaDeDocumento($file, string $folder, string $documentName, $idDocumento) {
        $movement = move_uploaded_file($file["tmp_name"], $folder . $documentName);
        $validateMovement = $movement == FALSE ? 0 : 1;

        if ($validateMovement == 1) {
            $updateDocumentData = array(
                "expediente" => $documentName,
                "modificado" => date('Y-m-d H:i:s'),
                "idUser" => $this->session->userdata('id_usuario')
            );

            $result = $this->General_model->updateRecord("historial_documento", $updateDocumentData, "idDocumento", $idDocumento);

            $response = ($result) ? 1 : 4;
            echo json_encode($response);
        } else {
            echo json_encode(2); // EL ARCHIVO NO SE PUDO MOVER
        }
    }

    public function eliminarArchivo() {
        $idDocumento = $this->input->post('idDocumento');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $updateDocumentData = array(
            "expediente" => NULL,
            "modificado" => date('Y-m-d H:i:s'),
            "idUser" => $this->session->userdata('id_usuario')
        );

        $nombreExp = $this->Registrolote_modelo->getNomExp($idDocumento);
        $filename = $this->Documentacion_model
            ->getFilename($idDocumento)
            ->row()
            ->expediente;
        $folder = $this->getCarpetaDeArchivo($tipoDocumento);
        $file = $folder . $filename;

        if (file_exists($file)) {
            unlink($file);
        }

        $result = $this->General_model->updateRecord("historial_documento", $updateDocumentData, "idDocumento", $idDocumento);
        $response = ($result) ? 1 : 2;

        if (intval($tipoDocumento) !== 7) { // El tipo de documento es CORRIDA
            echo json_encode($response);
            return;
        }

        $validaMail = $this->Registrolote_modelo->sendMailAdmin($nombreExp->idLote);

        if (is_null($validaMail->idHistorialLote)) {
            echo json_encode($response);
            return;
        }

        $infoLote = $this->Registrolote_modelo->getNameLote($nombreExp->idLote);
        $mail = $this->phpmailer_lib->load();

        $mail->setFrom('no-reply@ciudadmaderas.com', 'Ciudad Maderas');
        // TODO: Reemplazar el correo por los de producción
        $mail->addAddress('programador.analista24@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativoslp@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo1@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo2@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo3@ciudadmaderas.com');
//        $mail->AddAddress('karen.pina@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo4@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo5@ciudadmaderas.com');
//        $mail->AddAddress('coord.administrativo7@ciudadmaderas.com');
//        $mail->AddAddress('asistente.admon@ciudadmaderas.com');
        $mail->Subject = utf8_decode('MODIFICACIÓN DE CORRIDA FINANCIERA');
        $mail->isHTML(true);
        $mailContent = utf8_decode(`
            <html>
                <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'>
                    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css'
                        integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
                    <style media='all' type='text/css'>
                        .encabezados {
                            text-align: center;
                            padding-top:  1.5%;
                            padding-bottom: 1.5%;
                        }

                        .encabezados a {
                            color: #234e7f;
                            font-weight: bold;
                        }

                        .fondo {
                            background-color: #234e7f;
                            color: #fff;
                        }

                        h4 {
                            text-align: center;
                        }

                        p {
                            text-align: right;
                        }

                        strong {
                            color: #234e7f;
                        }
                    </style>
                </head>
                <body>
                    <table align='center' cellspacing='0' cellpadding='0' border='0' width='100%'>
                        <tr colspan='3'><td class='navbar navbar-inverse' align='center'>
                            <table width='750px' cellspacing='0' cellpadding='3' class='container'>
                                <tr class='navbar navbar-inverse encabezados'>
                                    <td>
                                        <img src='https://www.ciudadmaderas.com/assets/img/logo.png'
                                             width='100%'
                                             class='img-fluid'/>
                                        <p><a href='#'>SISTEMA DE CONTRATACIÓN</a></p>
                                    </td>
                                </tr>
                            </table>
                        </tr>
                        <tr>
                            <td border=1 bgcolor='#FFFFFF' align='center'>
                                <center>
                                    <table id='reporyt' cellpadding='0' cellspacing='0' border='1' width ='50%' style class='darkheader'>
                                        <tr class='active'>
                                            <th>Proyecto</th>
                                            <th>Condominio</th>
                                            <th>Lote</th>
                                            <th>Observación</th>
                                            <th>Fecha/Hora</th>
                                        </tr>
                                        <tr>
                                            <td><center>$infoLote->nombreResidencial</center></td>
                                            <td><center>$infoLote->nombre</center></td>
                                            <td><center>$infoLote->nombreLote</center></td>
                                            <td><center>SE MODIFICÓ CORRIDA FINANCIERA</center></td>
                                            <td><center>`. date("Y-m-d H:i:s") .`</center></td>
                                        </tr>
                                    </table>
                                </center>
                            </td>
                        </tr>
                    </table>
                </body>
            </html>
        `);
        $mail->Body = $mailContent;
        $mail->send();

        echo json_encode($response);
    }

    private function getCarpetaDeArchivo($tipoDocumento): string {
        if ($tipoDocumento == 7) { // CORRIDA FINANCIERA: CONTRALORÍA
            return 'static/documentos/cliente/corrida/';
        }

        if ($tipoDocumento == 8) { // CONTRATO: JURÍDICO
            return 'static/documentos/cliente/contrato/';
        }

        if ($tipoDocumento == 30) { // CONTRATO FIRMADO: CONTRALORÍA
            return 'static/documentos/cliente/contratoFirmado/';
        }

        // EL RESTO DE DOCUMENTOS SE GUARDAN EN LA CARPETA DE EXPEDIENTES
        return 'static/documentos/cliente/expediente/';
    }

    function reasonsForRejectionByDocument() {
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $this->load->view('template/header');
        $this->load->view("documentacion/reasonsForRejectionByDocument", $datos);
    }

    function getReasonsForRejectionByDocument() {
        if ($this->input->post("id_documento") == '' || $this->input->post("tipo_proceso") == '')
            echo json_encode(array());
        else {
            $data['data'] = $this->Documentacion_model->getReasonsForRejectionByDocument($this->input->post("id_documento"), $this->input->post('tipo_proceso'))->result_array();
            if ($data != null)
                echo json_encode($data);
            else
                echo json_encode(array());
        }
    }

    function saveRejectReason() {
        if($this->input->post("action") == '' || $this->input->post("reject_reason") == '')
            echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado."));
        else {
            if ($this->input->post("action") == 0) {
                if ($this->input->post("id_documento") == '')
                    echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado."));
                else {
                    $insertData = array(
                        "tipo_documento" => $this->input->post("id_documento"),
                        "motivo" => $this->input->post("reject_reason"),
                        "estatus" => 1,
                        "tipo_proceso" =>$this->input->post("id_documento") == 0 ? 3 :  2,
                        "creado_por" => $this->session->userdata('id_usuario'),
                        "fecha_creacion" => date('Y-m-d H:i:s'),
                        "modificado_por" => $this->session->userdata('id_usuario'),
                        "fecha_modificacion" => date('Y-m-d H:i:s')
                    );
                    $response = $this->General_model->addRecord("motivos_rechazo", $insertData);
                    if ($response)
                        echo json_encode(array("status" => 200, "message" => "El registro se ha ingresado de manera exitosa."));
                    else
                        echo json_encode(array("status" => 500, "message" => "Oops, algo salió mal. Inténtalo más tarde."));
                }
            } else {
                if ($this->input->post("id_motivo") == '')
                    echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado."));
                else {
                    $updateData = array(
                        "motivo" => $this->input->post("reject_reason"),
                        "modificado_por" => $this->session->userdata('id_usuario'),
                        "fecha_modificacion" => date('Y-m-d H:i:s')
                    );
                    $response = $this->General_model->updateRecord("motivos_rechazo", $updateData, "id_motivo", $this->input->post("id_motivo"));
                    if ($response)
                        echo json_encode(array("status" => 200, "message" => "El registro se ha actualizado de manera exitosa."));
                    else
                        echo json_encode(array("status" => 500, "message" => "Oops, algo salió mal. Inténtalo más tarde."));
                }
            }
        }
    }

    function changeStatus() {
        if ($this->input->post("action") == '' || $this->input->post("id_motivo") == '')
            echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado."));
        else {
            $updateData = array(
                "estatus" => $this->input->post("action") == 2 ? 0 : 1,
                "modificado_por" => $this->session->userdata('id_usuario'),
                "fecha_modificacion" => date('Y-m-d H:i:s')
            );
            $response = $this->General_model->updateRecord("motivos_rechazo", $updateData, "id_motivo", $this->input->post("id_motivo"));
            if ($response)
                echo json_encode(array("status" => 200, "message" => "El registro se ha actualizado de manera exitosa."));
            else
                echo json_encode(array("status" => 500, "message" => "Oops, algo salió mal. Inténtalo más tarde."));
        }
    }

    function getDocumentsInformation_Escrituracion() {
        $idLote = $this->input->post("idLote");
        $data = $this->Documentacion_model->getDocumentsInformation_Escrituracion($idLote)->result_array();
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    function getLotesList_escrituracion() {
        $idCondominio = $this->input->post("idCondominio");
        $data = $this->Documentacion_model->getLotesList_escrituracion($idCondominio)->result_array();
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    public function getCatalogOptions() {
        
            echo json_encode($this->Documentacion_model->getCatalogOptions()->result_array());
    }

    function getRejectionReasons() {
        $tipo_proceso = $this->input->post('tipo_proceso');
        $data = $this->Documentacion_model->getRejectionReasons($tipo_proceso);
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }
}
