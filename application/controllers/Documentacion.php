<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Documentacion extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('Documentacion_model', 'General_model', 'Registrolote_modelo'));
        $this->load->library(array('session', 'form_validation', 'get_menu'));
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
        $idLote = $this->input->post('idLote');
        $idDocumento = $this->input->post('idDocumento');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $documentName = $this->Documentacion_model->generateFilename($idLote, $idDocumento)->row();
        $documentName = $documentName->fileName . '.' . substr(strrchr($_FILES["uploadedDocument"]["name"], '.'), 1);
        $folder = $this->getFolderFile($tipoDocumento);
        if ($tipoDocumento == 7) { // SE VA A SUBIR / REEMPLAZAR LA CORRIDA
            $fileExt = strtolower(substr($documentName, strrpos($documentName, '.') + 1));
            if ($fileExt == 'xlsx')
                $this->actualizarRamaDeDocumento($file, $folder, $documentName, $idDocumento);
            else
                echo json_encode(3); // SE INTENTÓ SUBIR UN ARCHIVO DIFERENTE A UN .XLSX (CORRIDA)
        } else // SE VA A SUBIR EL EXPEDIENTE O EL CONTRATO
            $this->actualizarRamaDeDocumento($file, $folder, $documentName, $idDocumento);
    }

    function getCarpetaDeArchivo($tipoDocumento){
        if ($tipoDocumento == 7)  // CORRIDA FINANCIERA: CONTRALORÍA
            $folder = "static/documentos/cliente/corrida/";
        else if ($tipoDocumento == 8) // CONTRATO: JURÍDICO
            $folder = "static/documentos/cliente/contrato/";
        else if ($tipoDocumento == 30) // CONTRATO FIRMADO: CONTRALORÍA
            $folder = "static/documentos/cliente/contratoFirmado/";
        else // EL RESTO DE DOCUMENTOS SE GUARDAN EN LA CARPETA DE EXPEDIENTES
            $folder = "static/documentos/cliente/expediente/";
        return $folder;
    }

    function actualizarRamaDeDocumento($file, $folder, $documentName, $idDocumento) {
        $movement = move_uploaded_file($file["tmp_name"], $folder . $documentName);
        $validateMovement = $movement == FALSE ? 0 : 1;
        if ($validateMovement == 1) {
            $updateDocumentData = array(
                "expediente" => $documentName,
                "modificado" => date('Y-m-d H:i:s'),
                "idUser" => $this->session->userdata('id_usuario')
            );
            $response = $this->General_model->updateRecord("historial_documento", $updateDocumentData, "idDocumento", $idDocumento);
            echo json_encode($response);
        } else if ($validateMovement == 0)
            echo json_encode(2); // EL ARCHIVO NO SE PUDO MOVER
        else
            echo json_encode(2); // EL ARCHIVO NO SE PUDO MOVER
    }

    public function eliminarArchivo() {
        $idDocumento = $this->input->post('idDocumento');
        $tipoDocumento = $this->input->post('tipoDocumento');
        $updateDocumentData = array(
            "expediente" => NULL,
            "modificado" => date('Y-m-d H:i:s'),
            "idUser" => $this->input->post('typeTransaction') == 2 ? $this->input->post('clientName') : $this->session->userdata('id_usuario')
        );
        $filename = $this->Documentacion_model->getFilename($idDocumento)->row()->expediente;
        $folder = $this->getFolderFile($tipoDocumento);
        $file = $folder . $filename;
        if (file_exists($file))
            unlink($file);
        $response = $this->General_model->updateRecord("historial_documento", $updateDocumentData, "idDocumento", $idDocumento);
        echo json_encode($response);
        // FALTA ENVIAR EL CORREO CUANDO ES LA CORRIDA QUE SE ELIMINA
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
