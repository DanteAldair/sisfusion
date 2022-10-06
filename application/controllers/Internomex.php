  <?php
  defined('BASEPATH') or exit('No direct script access allowed');

  use PhpOffice\PhpSpreadsheet\Spreadsheet;


  class Internomex extends CI_Controller
  {
    private $gph;
    public function __construct()
    {
      parent::__construct();
      $this->load->model(array('Internomex_model', 'asesor/Asesor_model', 'General_model'));
      $this->load->library(array('session','form_validation', 'get_menu', 'Jwt_actions', 'Formatter'));
      $this->load->helper(array('url', 'form'));
      $this->load->database('default');
    }


    public function index()
    {
      if ($this->session->userdata('id_rol') == false || $this->session->userdata('id_rol') != '31') {
        redirect(base_url() . 'login');
      }
      /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/           
      $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
      /*-------------------------------------------------------------------------------*/
      $this->load->view('template/header');
      // $this->load->view('internomex/inicio_internomex_view',$datos);
      $this->load->view('template/home',$datos);
      $this->load->view('template/footer');
    }

    
    public function nuevos()
    {
     /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/           
     $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
     /*-------------------------------------------------------------------------------*/

     $this->load->view('template/header');
     $this->load->view("internomex/nuevos", $datos);
   }

   public function getDatosNuevasInternomex($proyecto,$condominio){
    $dat =  $this->Internomex_model->getDatosNuevasInternomex($proyecto,$condominio)->result_array();
    for( $i = 0; $i < count($dat); $i++ ){
     $dat[$i]['pa'] = 0;
   }
   echo json_encode( array( "data" => $dat));
  }



  public function aplicados()
  {
    /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/           
    $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
    /*-------------------------------------------------------------------------------*/
    $this->load->view('template/header');
    $this->load->view("internomex/aplicados", $datos);
  }

  public function getDatosAplicadosInternomex($proyecto,$condominio){
    $dat =  $this->Internomex_model->getDatosAplicadosInternomex($proyecto,$condominio)->result_array();
    for( $i = 0; $i < count($dat); $i++ ){
     $dat[$i]['pa'] = 0;
   }
   echo json_encode( array( "data" => $dat));
  }



  public function historial()
  {
    /*--------------------NUEVA FUNCIÓN PARA EL MENÚ--------------------------------*/           
    $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
    /*-------------------------------------------------------------------------------*/
    $this->load->view('template/header');
    $this->load->view("internomex/historial", $datos);
  }

  public function getDatosHistorialInternomex($proyecto,$condominio){
    $dat =  $this->Internomex_model->getDatosHistorialInternomex($proyecto,$condominio)->result_array();
    for( $i = 0; $i < count($dat); $i++ ){
     $dat[$i]['pa'] = 0;
   }
   echo json_encode( array( "data" => $dat));
  }


  public function aplico_internomex_pago($sol){
    $this->load->model("Internomex_model");   
    $consulta_comisiones = $this->db->query("SELECT id_pago_i FROM pago_comision_ind where id_pago_i IN (".$sol.")");
    
    if( $consulta_comisiones->num_rows() > 0 ){
     $consulta_comisiones = $consulta_comisiones->result_array();
     for( $i = 0; $i < count($consulta_comisiones ); $i++){
      $this->Internomex_model->update_aplica_intemex($consulta_comisiones[$i]['id_pago_i']);
    }
  }
  else{
   $consulta_comisiones = array();
  }
  }

  public function loadFinalPayment()
  {
    $datos = $this->get_menu->get_menu_data($this->session->userdata('id_rol'));
    $this->load->view('template/header');
    $this->load->view("internomex/load_final_payment", $datos); 
  }

  public function getPaymentsListByCommissionAgent()
  {
    $data = $this->Internomex_model->getCommissions()->result_array();
    echo json_encode($data);
  }
  public function getPagosFinal(){
    $year   = date("Y");
    $mes    = date("m");
    $Udia   = date("t");
    //var_dump($fecha);
    $fechaInicio = $this->input->post('fechaInicio');
    $fechaFin = $this->input->post('fechaFin');
   // var_dump($fechaFin);
   // var_dump($fechaInicio);
    if(!isset($fechaFin) and !isset($fechaInicio))
    {
      $mes = date("m");
      $year = date("Y");
      //var_dump($mes);
      //var_dump($year);
      //var_dump('mensaje de nulos');
      $fechaInicio = $year.'-'.$mes.'-'.'1';
      $fechaFin = date("Y-m-t");   
      //var_dump($fechaFin);
      // var_dump('mensaje de nulos');

    }
    $fechaInicio = $fechaInicio ." 0:00:00"; 
    $fechaFin = $fechaFin ." 23:59:59"; 

    //var_dump($fechaFin);
    //var_dump($fechaInicio);
    $data['data'] = $this->Internomex_model->getMFPagos($fechaInicio ,$fechaFin)->result_array();
    echo json_encode($data);
  }


  public function insertInformation() {
    if (!isset($_POST))
      echo json_encode(array("status" => 400, "message" => "Algún parámetro no viene informado."));
    else {
      if ($this->input->post("data") == "")
        echo json_encode(array("status" => 400, "message" => "Algún parámetro no tiene un valor especificado."), JSON_UNESCAPED_UNICODE);
      else {
        $data = $this->input->post("data");
        $decodedData = $this->jwt_actions->decodeData('4582', $data);
        if (in_array($decodedData, array('ALR001', 'ALR003', 'ALR004', 'ALR005', 'ALR006', 'ALR007', 'ALR008', 'ALR009', 'ALR010', 'ALR012', 'ALR013', 'ALR002', 'ALR011', 'ALR014')))
          echo json_encode(array("status" => 500, "message" => "No se logró decodificar la data."), JSON_UNESCAPED_UNICODE);
        else {
          $insertArrayData = array();
          $decodedData = json_decode($decodedData); // SE CONVIERTE A UN ARRAY
          $insertAuditoriaData = array("fecha_creacion" => date("Y-m-d H:i:s"), "creado_por" => (int)$this->session->userdata('id_usuario')); // SE CREA ARREGLO CON DATOS BASE (QUE LLEVAN TODOS LOS REGISTROS)
          if (count($decodedData) > 0) { // SE VALIDA QUE EL ARRAY AL MENOS TENGA DATOS
            $id_usuario = array();
            for ($i = 0; $i < count($decodedData); $i++) { // CICLO PARA VALIDACIÓN DE DATOS (CHECAR QUE LOS REGISTROS NOS SE HAYAN INSERTADO YA)
              // SE VERIFICA QUE LA FILA DE DATOS CONTEGA LA INFORMACIÓN QUE SE VA A INSERTAR Y QUE NO VENGA VACÍA
              if (isset($decodedData[$i]->id_usuario) && !empty($decodedData[$i]->id_usuario) && isset($decodedData[$i]->formaPago) && !empty($decodedData[$i]->formaPago) && isset($decodedData[$i]->montoSinDescuentos) && !empty($decodedData[$i]->montoSinDescuentos) &&
              isset($decodedData[$i]->montoConDescuentosSede) && !empty($decodedData[$i]->montoConDescuentosSede) && isset($decodedData[$i]->montoFinal) && !empty($decodedData[$i]->montoFinal))
                $id_usuario[$i] = (int)$decodedData[$i]->id_usuario;
              else {
                unset($decodedData[$i]); // SE ELIMINA LA POSICIÓN QUE YA SE INSERTÓ ANTERIORMENTE
                $decodedData = array_values($decodedData); // SE REORDENA EL ARRAY
              }
            }
            $verifiedData = array();
            if (count($id_usuario) > 0) {
              $id_usuario = implode(", ", $id_usuario); // SE CONVIERTE ARRAY DE ID DE USARIO A UN STRING SEPARADO POR COMMA PARA LA CONSULTA
              $verifiedData = $this->Internomex_model->verifyData($id_usuario);
            }
            for ($i = 0; $i < count($decodedData); $i++) { // CICLO PARA RECORRER ARRAY DE DATOS Y ARMAR ARRAY PARA EL BATCH INSERT
              $commonData = array();
              if (count($verifiedData) > 0) { // SE ENCONTRARON REGISTROS YA INSERTADOS EN EL MES
                for($e = 0; $e < count($verifiedData); $e++){
                  if((int)$decodedData[$i]->id_usuario === (int)$verifiedData[$e]->id_usuario)
                    unset($decodedData[$i]); // SE ELIMINA LA POSICIÓN QUE YA SE INSERTÓ ANTERIORMENTE
                    $decodedData = array_values($decodedData); // SE REORDENA EL ARRAY
                }
              }
              if (count($decodedData) > 0) {
                $commonData += array("id_usuario" => (int)$decodedData[$i]->id_usuario, 
                  "forma_pago" => $this->formatter->convertPaymentMethod($decodedData[$i]->formaPago), 
                  "monto_sin_descuento" => (float)$this->formatter->removeNumberFormat($decodedData[$i]->montoSinDescuentos), 
                  "monto_con_descuento" => (float)$this->formatter->removeNumberFormat($decodedData[$i]->montoConDescuentosSede), 
                  "monto_internomex" => (float)$this->formatter->removeNumberFormat($decodedData[$i]->montoFinal));
                $commonData += $insertAuditoriaData; // SE CONCATENA LA DATA BASE + LA DATA DEL ARRAY PRINCIPAL
                array_push($insertArrayData, $commonData);
              }
              else
                echo json_encode(array("status" => 500, "message" => "No hay información para procesar (vacío)."), JSON_UNESCAPED_UNICODE);
            }
            if (count($insertArrayData) > 0) { // AL TERMINAR EL CICLO SE EVALÚA SI EL ARRAY DE DATOS PARA EL BATCH INSERT TIENE DATA VA Y TIRA EL BATCH
              $insertResponse = $this->General_model->insertBatch("pagos_internomex", $insertArrayData);
              if ($insertResponse) // SE EVALÚA LA RESPUSTA DE LA TRANSACCIÓN OK
                echo json_encode(array("status" => 200, "message" => "Todos los registros se han insertado con éxito."), JSON_UNESCAPED_UNICODE);
              else // FALLÓ EL BATCH
                echo json_encode(array("status" => 500, "message" => "No se logró procesar la petición."), JSON_UNESCAPED_UNICODE);
            }
            else
              echo json_encode(array("status" => 500, "message" => "No hay información para procesar (intermedio)."), JSON_UNESCAPED_UNICODE);
          }
          else // ARRAY VACÍO
            echo json_encode(array("status" => 500, "message" => "No hay información para procesar (inicio)."), JSON_UNESCAPED_UNICODE);
        }
      }
    }
 }






}
