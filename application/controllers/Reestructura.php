<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reestructura extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
        $this->load->model(array('Reestructura_model','General_model'));
        $this->load->library(array('session','form_validation', 'get_menu','permisos_sidebar'));
		$this->load->helper(array('url', 'form'));
		$this->load->database('default');
        date_default_timezone_set('America/Mexico_City');
		$this->validateSession();

        $val =  $this->session->userdata('certificado'). $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
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

	public function reubicarCliente(){
		$this->load->view('template/header');
        $this->load->view("reestructura/reubicarCliente_view");
	}	

	public function getListaClientesReubicar(){
        $data = $this->Reestructura_model->getListaClientesReubicar();
        echo json_encode($data);
	}

	public function getProyectosDisponibles(){
		$idProyecto = $this->input->post('idProyecto');
		$superficie = $this->input->post('superficie');
		$tipoLote = $this->input->post('tipoLote');

		$data = $this->Reestructura_model->getProyectosDisponibles($idProyecto, $superficie, $tipoLote);
        echo json_encode($data);
	}

	public function getCondominiosDisponibles(){
		$idProyecto = $this->input->post('idProyecto');
		$superficie = $this->input->post('superficie');
		$tipoLote = $this->input->post('tipoLote');

		$data = $this->Reestructura_model->getCondominiosDisponibles($idProyecto, $superficie, $tipoLote);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
	}

	public function getLotesDisponibles(){
		$idCondominio = $this->input->post('idCondominio');
        $superficie = $this->input->post('superficie');

		$data = $this->Reestructura_model->getLotesDisponibles($idCondominio, $superficie);
        if ($data != null) {
            echo json_encode($data);
        } else {
            echo json_encode(array());
        }
	}
	
	public function reestructura(){
		$this->load->view('template/header');
        $this->load->view("reestructura/reestructura_view");
	}

	public function lista_proyecto(){
        echo json_encode($this->Reestructura_model->get_proyecto_lista()->result_array());
    }

	public function getregistros(){
        $index_proyecto = $this->input->post('index_proyecto');
        $dato = $this->Reestructura_model->get_valor_lote($index_proyecto);
        if ($dato != null) {
            echo json_encode($dato);
        }else{
            echo json_encode(array());
        }
    }

	public function aplicarLiberacion(){
				$dataPost = $_POST;
				$comentarioLiberacion = $dataPost['tipoLiberacion'] == 7 ? 'LIBERADO POR REUBICACIÓN' : ( $dataPost['tipoLiberacion'] == 9 ? 'LIBERACIÓN JURÍDICA' : ($dataPost['tipoLiberacion'] == 8 ? 'LIBERADO POR REESTRUCTURA' : '') );
				$observacionLiberacion = $dataPost['tipoLiberacion'] == 7 ? 'LIBERADO POR REUBICACIÓN' : ( $dataPost['tipoLiberacion'] == 9 ? 'LIBERACIÓN JURÍDICA' : ($dataPost['tipoLiberacion'] == 8 ? 'LIBERADO POR REESTRUCTURA' : '') );
                $datos["nombreLote"] = $dataPost['nombreLote'];
                $datos["precio"] = $dataPost['precio'];
                $datos["comentarioLiberacion"] = $comentarioLiberacion;
                $datos["observacionLiberacion"] = $observacionLiberacion;
                $datos["fechaLiberacion"] = date('Y-m-d H:i:s');
                $datos["modificado"] = date('Y-m-d H:i:s');
                $datos["status"] = 1;
                $datos["userLiberacion"] = $this->session->userdata('id_usuario');
				$dataPost['tipoLiberacion'] == 7 ? $datos['idLoteNuevo'] = $dataPost['idLoteNuevo'] : '' ;
                $datos["tipo"] = $dataPost['tipoLiberacion'];
				$datos["idLote"] = $dataPost['idLote'];
                $update = $this->Reestructura_model->aplicaLiberacion($datos);

                if ($update == TRUE) {
                    $response['message'] = 'SUCCESS';
                    echo json_encode(1);
                } else {
                    $response['message'] = 'ERROR';
                    echo json_encode(0);
                }
        
	}
}
 