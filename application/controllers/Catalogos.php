<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Catalogos extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Catalogos_model'));
        $this->load->library(array('session','form_validation', 'get_menu','permisos_sidebar'));
        $this->load->helper(array('url', 'form'));
        $this->load->database('default');
        date_default_timezone_set('America/Mexico_City');
        $this->validateSession();

        $val = $this->session->userdata('certificado'). $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $_SESSION['rutaController'] = str_replace('' . base_url() . '', '', $val);
        $rutaUrl = explode($_SESSION['rutaActual'], $_SERVER["REQUEST_URI"]);
        $this->permisos_sidebar->validarPermiso($this->session->userdata('datos'),$rutaUrl[1],$this->session->userdata('opcionesMenu'));
    }

    public function validateSession() {
        if($this->session->userdata('id_usuario') == "" || $this->session->userdata('id_rol') == "")
            redirect(base_url() . "index.php/login");
    }

    public function index(){
        $this->load->view('template/header');
        $this->load->view("template/home");
        $this->load->view('template/footer');
    }

    public function CatalogoInfo(){
        $this->load->view('template/header');
        $this->load->view("Catalogos/Catalogos_view");

    }    

    public function getCatalogos()
    {
        
        $data = $this->Catalogos_model->getCatalogosInformation()->result_array();

        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }      
    }

    public function editarNombre(){

		$dataPost = $_POST;
		$datos["idOpcion"] = $dataPost['idOpcion'];
        $datos["id_catalogo"] = $dataPost['id_catalogo'];
        $datos["editarCatalogo"] = $dataPost["editarCatalogo"];
		$update = $this->Catalogos_model->editarNombreCatalogo($datos);

		if ($update == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR';
			echo json_encode(0);
		}
	}

    //nuevos Correciones

    public function getOnlyCatalogos()
    {
        $data = $this->Catalogos_model->getCatalogosInfo()->result_array();
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }      
    }

    public function editarCatalogos(){
		$dataPost = $_POST;
		$datos["id_opcion"] = $dataPost['id_opcion'];
        $datos["idCatalogosEdit"] = $dataPost['idCatalogosEdit'];
        $datos["estatus_n"] = $dataPost["estatus_n"];
		$update = $this->Catalogos_model->editarModelCatalogos($datos);

		if ($update == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR'; 
			echo json_encode(0);
		}
	}

    public function insertNombre(){
		
        $idOpcion = $this->Catalogos_model->insertOpcion();
        $idOpcion = $idOpcion->lastId;
        $dataPost = $_POST;
        $datos["id"] = $idOpcion;
        $datos["id_catalogo"] = $dataPost["id_catalogo"];
		$datos["nombre"] = $dataPost['nombre'];
        $datos["fecha_creacion"] = date('Y-m-d H:i:s');

		$insert = $this->Catalogos_model->insertarCampo($datos);

		if ($insert == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR';
			echo json_encode(0);
		}
	}


}

