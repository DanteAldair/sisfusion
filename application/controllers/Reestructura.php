<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reestructura extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
        $this->load->model(array('Reestructura_model','General_model', 'caja_model_outside'));
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

	public function lista_catalogo_opciones(){
		echo json_encode($this->Reestructura_model->get_catalogo_resstructura()->result_array());
	}

	public function insertarOpcionN (){
		$idOpcion = $this->Reestructura_model->insertOpcion();
		$idOpcion = $idOpcion->lastId;
		$dataPost = $_POST;
		$datos["id"] = $idOpcion;
		$datos["nombre"] = $dataPost['nombre'];
		$datos["fecha_creacion"] = date('Y-m-d H:i:s');

		$insert = $this->Reestructura_model->nuevaOpcion($datos);

		if ($insert == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR';
			echo json_encode(0);
		}
	}

	public function borrarOpcion(){
		$dataPost = $_POST;
		$datos["idOpcion"] = $dataPost['idOpcion'];

		$update = $this->Reestructura_model->borrarOpcionModel($datos);
		if ($update == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR';
			echo json_encode(0);
		}
	}

	public function validarLote(){

		$dataPost = $_POST;
		$datos["idLote"] = $dataPost['idLote'];
		$datos["opcionReestructura"] = $dataPost['opcionReestructura'];
		$datos["comentario"] = $dataPost['comentario'];
		$datos["userLiberacion"] = $this->session->userdata('id_usuario');
		$update = $this->Reestructura_model->actualizarValidacion($datos);

		if ($update == TRUE) {
			$response['message'] = 'SUCCESS';
			echo json_encode(1);
		} else {
			$response['message'] = 'ERROR';
			echo json_encode(0);
		} 
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
        $this->db->trans_begin();
        $idCliente = $this->input->post('idCliente');
        $idAsesor = $this->session->userdata('id_usuario');
        $idLider = $this->session->userdata('id_lider');
		$clienteAnterior = $this->General_model->getCliente($idCliente)->row();
		$lineaVenta = $this->General_model->getLider($idLider)->row();
        $proceso = 3;

        if (!$this->copiarClienteANuevo($clienteAnterior, $idAsesor, $idLider, $lineaVenta, $proceso)) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        $idClienteInsert = $this->db->insert_id();

        if ($insert == TRUE) {
            echo json_encode(1);
        } else {
            echo json_encode(0);
        }
    }

    public function setReubicacion(){
        $this->db->trans_begin();

        $idClienteAnterior = $this->input->post('idCliente');
        $loteAOcupar = $this->input->post('loteAOcupar');
        $idCondominio = $this->input->post('condominioAOcupar');
        $idAsesor = $this->session->userdata('id_usuario');
		$nombreAsesor = $this->session->userdata('nombre') . ' ' . $this->session->userdata('apellido_paterno') . ' ' . $this->session->userdata('apellido_materno');
        $idLider = $this->session->userdata('id_lider');
		$clienteAnterior = $this->General_model->getCliente($idClienteAnterior)->result();
		$lineaVenta = $this->General_model->getLider($idLider)->result();
		$loteSelected = $this->Reestructura_model->getSelectedSup($loteAOcupar)->result();
		$nuevaSup = floatval($loteSelected[0]->sup);
		$anteriorSup = floatval($clienteAnterior[0]->sup);
		$proceso = ( $anteriorSup == $nuevaSup || (($nuevaSup - $anteriorSup) <= 2)) ? 2 : 4;

		$validateLote = $this->caja_model_outside->validate($loteAOcupar);
        if ($validateLote == 0) {
            echo json_encode([
                'titulo' => 'FALSE',
                'resultado' => FALSE,
                'message' => 'Error, el lote no esta disponible',
                'color' => 'danger'
            ]);
            return;
        }

        if (!$this->copiarClienteANuevo($loteSelected, $idCondominio, $clienteAnterior, $idAsesor, $idLider, $lineaVenta, $proceso)) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        $idClienteInsert = $this->db->insert_id();

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
                while ($i <= 6) {
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
                $fechaFull = $fecha;
            } else {
                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 5) {
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
                $fechaFull = $fecha;
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
                while ($i <= 6) {
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
                $fechaFull = $fecha;
            } else {
                $fecha = $fechaAccion;
                $i = 0;
                while ($i <= 6) {
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
                $fechaFull = $fecha;
            }
        }

		$dataUpdateLote = array(
            'idCliente' => $idClienteInsert,
            'idStatusContratacion' => 1,
            'idMovimiento' => 31,
            'comentario' => 'OK',
            'usuario' => $nombreAsesor,
            'perfil' => 'ooam',
            'modificado' => date('Y-m-d h:i:s'),
            'fechaVenc' => $fechaFull,
            'IdStatusLote' => 3
        );

        if (!$this->General_model->updateRecord("lotes", $dataUpdateLote, "idLote", $loteAOcupar, )) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        $dataUpdateCliente = array(
            'proceso' => $proceso
        );

        if (!$this->General_model->updateRecord("clientes", $dataUpdateCliente, "id_cliente", $idClienteAnterior)){
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        $dataInsertHistorialLote = array(
			'nombreLote' => $loteSelected[0]->nombreLote,
			'idStatusContratacion' => 1,
			'idMovimiento' => 31,
			'modificado' => date('Y-m-d h:i:s'),
			'fechaVenc' => date('Y-m-d h:i:s'),
			'idLote' => $loteAOcupar,
			'idCondominio' => $idCondominio,
			'idCliente' => $idClienteInsert,
			'usuario' => $idAsesor,
			'perfil' => 'ooam',
			'comentario' => 'OK',
			'status' => 1
        );

        if (!$this->caja_model_outside->insertLotToHist($dataInsertHistorialLote)) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        if (!$this->copiarDSAnteriorAlNuevo($idClienteAnterior, $idClienteInsert)) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        if (!$this->moverExpediente($clienteAnterior[0]->idLote, $loteAOcupar, $idClienteAnterior, $idClienteInsert)) {
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();

            echo json_encode([
                'titulo' => 'ERROR',
                'resultado' => FALSE,
                'message' => 'Error al dar de alta el cliente, por favor verificar la transacción.',
                'color' => 'danger'
            ]);
            return;
        }

        $this->db->trans_commit();
		echo json_encode([
            'titulo' => 'OK',
            'resultado' => TRUE,
            'message' => 'Proceso realizado correctamente.',
            'color' => 'success'
        ]);
	}

    function moverExpediente($idLoteAnterior, $idLoteNuevo, $idClienteAnterior, $idClienteNuevo): bool
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
                return false;
            }
        }

        return $this->General_model->insertBatch('historial_documento', $documentacion);
    }

    public function copiarClienteANuevo($loteSelected = null, $idCondominio = null, $clienteAnterior, $idAsesor, $idLider, $lineaVenta, $proceso){
        $dataCliente = [];
        $arrayReestructura = ['idCliente', 'idLote', 'idCondominio'];
        $arrayReubicacion = ['idCliente'];
        foreach ($clienteAnterior as $clave => $valor) {
            if(in_array($clave, $proceso == 2 ? $arrayReestructura : $arrayReestructura)) {
                continue;
            }

            if ($clave == 'id_asesor') {
                $dataCliente = array_merge([$clave => $idAsesor], $dataCliente);
                continue;
            }
            else if ($clave == 'id_coordinador') {
                $dataCliente = array_merge([$clave => 0], $dataCliente);
                continue;
            }
            else if ($clave == 'id_gerente') {
                $dataCliente = array_merge([$clave => $idLider], $dataCliente);
                continue;
            }
            else if ($clave == 'idLote' && $proceso != 2) {
                $dataCliente = array_merge([$clave =>$loteSelected], $dataCliente);
                continue;
            }
            else if ($clave == 'idCondominio' && $proceso != 2) {
                $dataCliente = array_merge([$clave =>  $idCondominio], $dataCliente);
                continue;
            }
            else if ($clave == 'id_subdirector') {
                $dataCliente = array_merge([$clave => $lineaVenta->id_subdirector], $dataCliente);
                continue;
            }
            else if ($clave == 'id_regional') {
                $dataCliente = array_merge([$clave =>  $lineaVenta->id_regional], $dataCliente);
                continue;
            }
            else if ($clave == 'plan_comision') {
                $dataCliente = array_merge([$clave =>  $proceso == 3 ? 64 : $proceso == 2 ? 65 : 66 ], $dataCliente);
                continue;
            }
            else if ($clave == 'proceso') {
                $dataCliente = array_merge([$clave =>  $proceso], $dataCliente);
                continue;
            }
            else if ($clave == 'totalNeto2Cl') {
                $dataCliente = array_merge([$clave =>  0], $dataCliente);
                continue;
            }

            $dataCliente = array_merge([$clave => $valor], $dataCliente);
        }

        $resultCliente = $this->General_model->addRecord('clientes', $dataCliente);
        return $resultCliente;
    }

    public function copiarDSAnteriorAlNuevo($idClienteAnterior, $idClienteNuevo): bool
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

        $resultDs = $this->General_model->updateRecord('deposito_seriedad', $dsData, 'id_cliente', $idClienteNuevo);
        $resultCop = (empty($coopropietariosData)) ? true : $this->General_model->insertBatch('copropietarios', $coopropietariosData);

        return $resultDs && $resultCop;
    }
} 
