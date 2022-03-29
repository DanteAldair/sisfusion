<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contabilidad extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Contabilidad_model', 'asesor/Asesor_model'));
        $this->load->library(array('session', 'form_validation', 'get_menu'));
        $this->load->helper(array('url', 'form'));
        $this->load->database('default');
        $this->programacion = $this->load->database('programacion', TRUE);
        //$this->validateSession();
    }

    public function index()
    {
    }

    public function validateSession()
    {
        if ($this->session->userdata('id_usuario') == "" || $this->session->userdata('id_rol') == "")
            redirect(base_url() . "index.php/login");
    }

    public function crmInformation()
    {
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $this->load->view('template/header');
        $this->load->view("contabilidad/crmInformation", $datos);
    }

    public function neodataInformation()
    {
        $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
        $this->load->view('template/header');
        $this->load->view("contabilidad/neodataInformation", $datos);
    }

    public function getInformation()
    {
        if (isset($_POST) && !empty($_POST)) {
            $idLote = implode(", ", $this->input->post("lotes"));
            $data['data'] = $this->Contabilidad_model->getInformation($idLote)->result_array();
            echo json_encode($data);
        } else
            json_encode(array());
    }

    public function setData()
    {
        if (!isset($_POST))
            echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado."));
        else {
            // MJ: ACÁ TENGO QUE VALIDAR BIEN QUE TODAS LAS POSICIONES DEL ARAYY TENGAN UN VALOR
            if ($this->input->post("lotes") == "")
                echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado o no viene informado..."));
            else {
                $idLote = implode(", ", $this->input->post("lotes")); // MJ: SE CAMBIA ARRAY A UN STRING SEPARADO POR COMAS
                $json = json_decode($this->input->post("jsonInfo"));
                $statusByLote = $this->Contabilidad_model->getTypeTransaction($idLote)->result_array(); // MJ: SE OBTIENE EL TIPO DE TRANSACCIÓN QUE SE VA A CORRER POR REGISTRO
                $updateAuditoriaData = array("fecha_modificacion" => date("Y-m-d H:i:s"), "modificado_por" => $this->session->userdata('id_usuario'));
                $insertAuditoriaData = array("fecha_creacion" => date("Y-m-d H:i:s"), "creado_por" => $this->session->userdata('id_usuario'));
                $insertArrayData = array();
                $updateArrayData = array();
                $insertResponse = array();
                for ($i = 0; $i < count($statusByLote); $i++) { // MJ: SE ARMAN ARRAYS PARA INSERTAR | ACTUALIZAR SEGÚN SEA EL CASO
                    $commonData = array();
                    if ($statusByLote[$i]['typeTransaction'] == 0) { // MJ: INSERT

                        $commonData += (isset($json[$i]->fecha_firma) && !empty($json[$i]->fecha_firma)) ? array("fecha_firma" => date("Y/m/d", strtotime($json[$i]->fecha_firma))) : array("fecha_firma" => NULL);
                        $commonData += (isset($json[$i]->adendum) && !empty($json[$i]->adendum)) ? array("adendum" => $json[$i]->adendum) : array("adendum" => NULL);
                        $commonData += (isset($json[$i]->superficie_postventa) && !empty($json[$i]->superficie_postventa)) ? array("superficie_postventa" => $json[$i]->superficie_postventa) : array("superficie_postventa" => NULL);
                        $commonData += (isset($json[$i]->costo_m2) && !empty($json[$i]->costo_m2)) ? array("costo_m2" => $json[$i]->costo_m2) : array("costo_m2" => NULL);
                        $commonData += (isset($json[$i]->parcela) && !empty($json[$i]->parcela)) ? array("parcela" => $json[$i]->parcela) : array("parcela" => NULL);
                        $commonData += (isset($json[$i]->superficie_proyectos) && !empty($json[$i]->superficie_proyectos)) ? array("superficie_proyectos" => $json[$i]->superficie_proyectos) : array("superficie_proyectos" => NULL);
                        $commonData += (isset($json[$i]->presupuesto_m2) && !empty($json[$i]->presupuesto_m2)) ? array("presupuesto_m2" => $json[$i]->presupuesto_m2) : array("presupuesto_m2" => NULL);
                        $commonData += (isset($json[$i]->deduccion) && !empty($json[$i]->deduccion)) ? array("deduccion" => $json[$i]->deduccion) : array("deduccion" => NULL);
                        $commonData += (isset($json[$i]->m2_terreno) && !empty($json[$i]->m2_terreno)) ? array("m2_terreno" => $json[$i]->m2_terreno) : array("m2_terreno" => NULL);
                        $commonData += (isset($json[$i]->costo_terreno) && !empty($json[$i]->costo_terreno)) ? array("costo_terreno" => $json[$i]->costo_terreno) : array("costo_terreno" => NULL);
                        $commonData += (isset($json[$i]->comentario) && !empty($json[$i]->comentario)) ? array("comentario" => $json[$i]->comentario) : array("comentario" => NULL);

                        $commonData += array("id_lote" => $statusByLote[$i]['idLote']);
                        $commonData += array("id_cliente" => $json[$i]->id_cliente);
                        $commonData += $insertAuditoriaData;
                        $commonData += $updateAuditoriaData;
                        array_push($insertArrayData, $commonData);
                    } else { // MJ: UPDATE
                        if (isset($json[$i]->fecha_firma) && !empty($json[$i]->fecha_firma))
                            $commonData += array("fecha_firma" => $json[$i]->fecha_firma);
                        if (isset($json[$i]->adendum) && !empty($json[$i]->adendum))
                            $commonData += array("adendum" => $json[$i]->adendum);
                        if (isset($json[$i]->superficie_postventa) && !empty($json[$i]->superficie_postventa))
                            $commonData += array("superficie_postventa" => $json[$i]->superficie_postventa);
                        if (isset($json[$i]->costo_m2) && !empty($json[$i]->costo_m2))
                            $commonData += array("costo_m2" => $json[$i]->costo_m2);
                        if (isset($json[$i]->parcela) && !empty($json[$i]->parcela))
                            $commonData += array("parcela" => $json[$i]->parcela);
                        if (isset($json[$i]->superficie_proyectos) && !empty($json[$i]->superficie_proyectos))
                            $commonData += array("superficie_proyectos" => $json[$i]->superficie_proyectos);
                        if (isset($json[$i]->presupuesto_m2) && !empty($json[$i]->presupuesto_m2))
                            $commonData += array("presupuesto_m2" => $json[$i]->presupuesto_m2);
                        if (isset($json[$i]->deduccion) && !empty($json[$i]->deduccion))
                            $commonData += array("deduccion" => $json[$i]->deduccion);
                        if (isset($json[$i]->m2_terreno) && !empty($json[$i]->m2_terreno))
                            $commonData += array("m2_terreno" => $json[$i]->m2_terreno);
                        if (isset($json[$i]->costo_terreno) && !empty($json[$i]->costo_terreno))
                            $commonData += array("costo_terreno" => $json[$i]->costo_terreno);
                        if (isset($json[$i]->comentario) && !empty($json[$i]->comentario))
                            $commonData += array("comentario" => $json[$i]->comentario);

                        $commonData += array("id_dxl" => $statusByLote[$i]['id_dxl']);
                        $commonData += $updateAuditoriaData;
                        array_push($updateArrayData, $commonData);
                    }
                }
                if (count($insertArrayData) > 0)
                    $insertResponse = $this->Contabilidad_model->insertData($insertArrayData); // MJ: SE MANDA CORRER EL INSERT BATCH

                if (count($updateArrayData) > 0)
                    $updateResponse = $this->Contabilidad_model->updateData($updateArrayData); // MJ: SE MANDA CORRER EL UPDATE BATCH

                if (count($insertArrayData) > 0 && count($updateArrayData) > 0) { // MJ: AMBAS TRANSACCIONES
                    if ($insertResponse == true && ($updateResponse == true)) // MJ: INSERTS Y UPDATES OK
                        echo json_encode(array("status" => 200, "message" => "Los registros se ha insertado/actualizado de manera exitosa."));
                    else if ($insertResponse == true && ($updateResponse == false)) //MJ: INSERTS OK | UPDATE ERROR
                        echo json_encode(array("status" => 200, "message" => "Los nuevos registros se han insertado de manera exitosa. Error al actualizar los registros existentes."));
                    else if ($insertResponse == false && ($updateResponse == true)) //MJ: INSERTS ERROR | UPDATE OK
                        echo json_encode(array("status" => 200, "message" => "Los registros existentes se han actualizado de manera exitosa. Error al insertar los registros"));
                    else if ($insertResponse == false && ($updateResponse == false)) //MJ: INSERTS ERROR | UPDATE ERROR
                        echo json_encode(array("status" => 503, "message" => "Oops, algo salió mal. Inténtalo más tarde 001."));
                    else //MJ: ALGO MÁS PASÓ
                        echo json_encode(array("status" => 503, "message" => "Oops, algo salió mal. Inténtalo más tarde 002."));
                } else if (count($insertArrayData) > 0 && count($updateArrayData) <= 0) { // MJ: SÓLO INSERT
                    if ($insertResponse == true) // MJ: INSERTS Y UPDATES OK
                        echo json_encode(array("status" => 200, "message" => "Los nuevos registros se han insertado de manera exitosa."));
                    else //MJ: ALGO MÁS PASÓ
                        echo json_encode(array("status" => 503, "message" => "Oops, algo salió mal. Inténtalo más tarde 003."));
                } else if (count($insertResponse) <= 0 && count($updateArrayData) > 0) { // MJ: SÓLO UPDATE
                    if ($updateResponse == true) // MJ: INSERTS Y UPDATES OK
                        echo json_encode(array("status" => 200, "message" => "Los registros existentes se han actualizado de manera exitosa."));
                    else //MJ: ALGO MÁS PASÓ
                        echo json_encode(array("status" => 503, "message" => "Oops, algo salió mal. Inténtalo más tarde 004."));
                }
            }
        }
    }

    public function getClientLote()
    {
        $a = 0;
        if (isset($_POST) && !empty($_POST)) {
            $idLote = implode(", ", $this->input->post("lotes"));
            $data = $this->Contabilidad_model->getClientLote($idLote)->result_array();
            echo json_encode($data);
        } else
            json_encode(array());
    }

    public function getInformationFromNeoData()
    {
        $idProyecto = $this->input->post("idProyecto");
        $idCliente = $this->input->post("idCliente");
        $dates = $this->input->post("dates");
        $fechaIni = date("Y-m-d", strtotime($this->input->post("fechaIni")));
        $fechaFin = date("Y-m-d", strtotime($this->input->post("fechaFin")));
        $data['data'] = $this->Contabilidad_model->getInformationFromNeoData($this->input->post("empresa"), $idProyecto, $idCliente, $fechaIni, $fechaFin, $dates)->result_array();
        echo json_encode($data);
    }

    public function getEmpresasList()
    {
        $data = $this->Contabilidad_model->getEmpresasList();
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    public function getProyectosList()
    {
        $data = $this->Contabilidad_model->getProyectosList($this->input->post("empresa"));
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    public function getClientesList()
    {
        $data = $this->Contabilidad_model->getClientesList($this->input->post("empresa"), $this->input->post("proyecto"));
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

    function getColumns()
    {
        $data = $this->Contabilidad_model->getColumns();
        if ($data != null)
            echo json_encode($data);
        else
            echo json_encode(array());
    }

}

