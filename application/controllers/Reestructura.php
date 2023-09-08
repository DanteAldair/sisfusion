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
        $update = $this->Reestructura_model->aplicaLiberacion($dataPost);
        if ($update == TRUE) {
            echo json_encode(1);
        } else {
            echo json_encode(0);
        }
        
	}

    public function setReestructura(){
        $dataPost = $_POST;
        $insert = $this->Reestructura_model->setReestructura($dataPost);
        if ($insert == TRUE) {
            echo json_encode(1);
        } else {
            echo json_encode(0);
        }
    }

    function moverArchivos($idLoteAnterior, $idLoteNuevo, $idClienteAnterior, $idClienteNuevo)
    {
        $loteNuevoInfo = $this->Reestructura_model->obtenerLotePorId($idLoteNuevo);
        $docAnterior = $this->Reestructura_model->obtenerDocumentacionActiva($idLoteAnterior, $idClienteAnterior);
        $docPorReubicacion = $this->Reestructura_model->obtenerDocumentacionPorReubicacion($loteNuevoInfo->personalidad_juridica);
        $documentacion = [];
        $modificado = date('Y-m-d H:i:s');

        foreach ($docAnterior as $doc) {
            $documentacion[] = [
                'movimiento' => $doc['movimiento'],
                'expediente' => $doc['expediente'],
                'modificado' => $modificado,
                'status' => 1,
                'idCliente' => $idClienteNuevo,
                'idCondominio' => $loteNuevoInfo->idCondominio,
                'idLote' => $idLoteNuevo,
                'idUser' => NULL,
                'tipo_documento' => 0,
                'id_autorizacion' => 0,
                'tipo_doc' => $doc['tipo_doc'],
                'estatus_validacion' => 0
            ];
        }

        foreach ($docPorReubicacion as $doc) {
            $documentacion[] = [
                'movimiento' => $doc['nombre'],
                'expediente' => NULL,
                'modificado' => $modificado,
                'status' => 1,
                'idCliente' => $idClienteNuevo,
                'idCondominio' => $loteNuevoInfo->idCondominio,
                'idLote' => $idLoteNuevo,
                'idUser' => NULL,
                'tipo_documento' => 0,
                'id_autorizacion' => 0,
                'tipo_doc' => $doc['id_opcion'],
                'estatus_validacion' => 0
            ];
        }

        if (!file_exists("static/documentos/contratacion-reubicacion/$loteNuevoInfo->nombreLote/")) {
            $result = mkdir("static/documentos/contratacion-reubicacion/$loteNuevoInfo->nombreLote", 0777, TRUE);
            if (!$result) {
                echo 'No se pudo crear el folder';
                return;
            }
        }

        $this->General_model->insertBatch('historial_documento', $documentacion);

        echo 'Salió todo fine';
    }

    public function copiarDSAnteriorAlNuevo($idClienteAnterior, $idClienteNuevo)
    {
        $dsAnterior = $this->Reestructura_model->obtenerDSPorIdCliente($idClienteAnterior);
        $residencial = $this->Reestructura_model->obtenerResidencialPorIdCliente($idClienteNuevo);
        $coopropietarios = $this->Reestructura_model->obtenerCopropietariosPorIdCliente($idClienteAnterior);
        $dsData = [];
        $coopropietariosData = [];

        foreach ($dsAnterior as $clave => $valor) {
            if(in_array($clave, ['id', 'id_cliente', 'clave'])) {
                continue;
            }

            if ($clave == 'proyecto') {
                $dsData = array_merge([$clave => $residencial->nombreResidencial], $dsData);
                continue;
            }

            $dsData = array_merge([$clave => $valor], $dsData);
        }

        foreach ($coopropietarios as $coopropietario) {
            $dataTmp = [];

            foreach ($coopropietario as $clave => $valor) {
                if (in_array($clave, ['id_copropietario'])) {
                    continue;
                }

                if ($clave == 'id_cliente') {
                    $dataTmp = array_merge([$clave => $idClienteNuevo], $dataTmp);
                    continue;
                }

                $dataTmp = array_merge([$clave => $valor], $dataTmp);
            }

            $coopropietariosData[] = $dataTmp;
        }

        $this->General_model->updateRecord('deposito_seriedad', $dsData, 'id_cliente', $idClienteNuevo);
        $this->General_model->insertBatch('copropietarios', $coopropietariosData);

        echo 'Salió todo bien';
    }

    public function setReubicacion(){
        $idClienteAnterior = $this->input->post('idCliente');
        $loteAOcupar = $this->input->post('loteAOcupar');
        $idCondominio = $this->input->post('condominioAOcupar');
        $idAsesor = $this->session->userdata('id_usuario');
        $idLider = $this->session->userdata('id_lider');

        $clienteAnterior = $this->General_model->getCliente($idClienteAnterior)->result();
        $lineaVenta = $this->General_model->getLider($idLider)->result();
        $nuevaSup = $this->Reestructura_model->getSelectedSup($loteAOcupar)->result();
        $nuevaSup = intval($nuevaSup[0]->sup);
        $anteriorSup = intval($clienteAnterior[0]->sup);

        $proceso = ( $anteriorSup == $nuevaSup || (($anteriorSup + 2) == $nuevaSup)) ? 2 : 5;

        $data = array(
            'id_asesor' => $idAsesor,
            'id_coordinador' => 0,
            'id_gerente' => $idLider,
            'id_sede' => $clienteAnterior[0]->id_sede,
            'nombre' => $clienteAnterior[0]->nombre,
            'apellido_paterno' => $clienteAnterior[0]->apellido_paterno,
            'apellido_materno' => $clienteAnterior[0]->apellido_materno,
            'personalidad_juridica' => $clienteAnterior[0]->personalidad_juridica,
            'nacionalidad' => $clienteAnterior[0]->nacionalidad,
            'rfc' => $clienteAnterior[0]->rfc,
            'curp' => $clienteAnterior[0]->curp,
            'correo' => $clienteAnterior[0]->correo,
            'telefono1' => $clienteAnterior[0]->telefono1,
            'telefono2' => $clienteAnterior[0]->telefono2,
            'telefono3' => $clienteAnterior[0]->telefono3,
            'fecha_nacimiento' => $clienteAnterior[0]->fecha_nacimiento,
            'lugar_prospeccion' => $clienteAnterior[0]->lugar_prospeccion,
            'medio_publicitario' => $clienteAnterior[0]->medio_publicitario,
            'otro_lugar' => $clienteAnterior[0]->otro_lugar,
            'plaza_venta' => $clienteAnterior[0]->plaza_venta,
            'tipo' => $clienteAnterior[0]->tipo,
            'estado_civil' => $clienteAnterior[0]->estado_civil,
            'regimen_matrimonial' => $clienteAnterior[0]->regimen_matrimonial,
            'nombre_conyuge' => $clienteAnterior[0]->nombre_conyuge,
            'domicilio_particular' => $clienteAnterior[0]->domicilio_particular,
            'originario_de' => $clienteAnterior[0]->originario_de,
            'tipo_vivienda' => $clienteAnterior[0]->tipo_vivienda,
            'ocupacion' => $clienteAnterior[0]->ocupacion,
            'empresa' => $clienteAnterior[0]->empresa,
            'puesto' => $clienteAnterior[0]->puesto,
            'edadFirma' => $clienteAnterior[0]->edadFirma,
            'antiguedad' => $clienteAnterior[0]->antiguedad,
            'domicilio_empresa' => $clienteAnterior[0]->domicilio_empresa,
            'telefono_empresa' => $clienteAnterior[0]->telefono_empresa,
            'noRecibo' => $clienteAnterior[0]->noRecibo,
            'engancheCliente' => $clienteAnterior[0]->engancheCliente,
            'concepto' => $clienteAnterior[0]->concepto,
            'fechaEnganche' => $clienteAnterior[0]->fechaEnganche,
            'idTipoPago' => $clienteAnterior[0]->idTipoPago,
            'expediente' => $clienteAnterior[0]->expediente,
            'status' => $clienteAnterior[0]->status,
            'idLote' => $loteAOcupar,
            'fechaApartado' => $clienteAnterior[0]->fechaApartado,
            'fechaVencimiento' => $clienteAnterior[0]->fechaVencimiento,
            'usuario' => $clienteAnterior[0]->usuario,
            'idCondominio' => $idCondominio,
            'fecha_creacion' => $clienteAnterior[0]->fecha_creacion,
            'creado_por' => $clienteAnterior[0]->creado_por,
            'fecha_modificacion' => $clienteAnterior[0]->fecha_modificacion,
            'modificado_por' => $clienteAnterior[0]->modificado_por,
            'autorizacion' => $clienteAnterior[0]->autorizacion,
            'idAut' => $clienteAnterior[0]->idAut,
            'motivoAut' => $clienteAnterior[0]->motivoAut,
            'id_prospecto' => $clienteAnterior[0]->id_prospecto,
            'idReferido' => $clienteAnterior[0]->idReferido,
            'descuento_mdb' => $clienteAnterior[0]->descuento_mdb,
            'id_subdirector' => $lineaVenta[0]->id_subdirector,
            'id_regional' => $lineaVenta[0]->id_regional,
            'plan_comision' => 0,
            'flag_compartida' => $clienteAnterior[0]->flag_compartida,
            'precio_cl' => $clienteAnterior[0]->precio_cl,
            'total_cl' => $clienteAnterior[0]->total_cl,
            'ubicacion_cl' => $clienteAnterior[0]->ubicacion_cl,
            'totalNeto_cl' => $clienteAnterior[0]->totalNeto_cl,
            'totalNeto2_cl' => $clienteAnterior[0]->totalNeto2_cl,
            'registro_comision_cl' => $clienteAnterior[0]->registro_comision_cl,
            'totalValidado_cl' => $clienteAnterior[0]->totalValidado_cl,
            'tipo_venta_cl' => $clienteAnterior[0]->tipo_venta_cl,
            'id_regional_2' => $clienteAnterior[0]->id_regional_2,
            'rl' => $clienteAnterior[0]->rl,
            'tipo_nc' => $clienteAnterior[0]->tipo_nc,
            'printPagare' => $clienteAnterior[0]->printPagare,
            'estructura' => $clienteAnterior[0]->estructura,
            'tipo_casa' => $clienteAnterior[0]->tipo_casa,
            'apartadoXReubicacion' => $clienteAnterior[0]->apartadoXReubicacion,
            'id_cliente_reubicacion' => $clienteAnterior[0]->id_cliente,
            'fechaAlta' => $clienteAnterior[0]->fechaAlta,
            'tipo_comprobanteD' => $clienteAnterior[0]->tipo_comprobanteD,
            'regimen_fac' => $clienteAnterior[0]->regimen_fac,
            'cp_fac' => $clienteAnterior[0]->cp_fac,
            'cancelacion_proceso' => $clienteAnterior[0]->cancelacion_proceso,
            'banderaComisionCl' => $clienteAnterior[0]->banderaComisionCl,
            'proceso' => $proceso,
            'totalNeto2Cl' => 0
        );

        $response =  $this->db->insert('clientes', $data);
        $insertedId = $this->db->insert_id();

        if ($response) {
            print_r("exito");
        } else {
            print_r("nah");
        }
    }
}
