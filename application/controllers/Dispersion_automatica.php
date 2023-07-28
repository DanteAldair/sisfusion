<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Dispersion_automatica extends CI_Controller
{
  private $gph;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Comisiones_model');
    $this->load->model('asesor/Asesor_model');
    $this->load->model('Usuarios_modelo');
    $this->load->model('PagoInvoice_model');
    $this->load->model('General_model');
    $this->load->model('Dispersion_automatica_model');
    $this->load->library(array('session', 'form_validation', 'get_menu', 'Jwt_actions'));
    $this->load->helper(array('url', 'form'));
    $this->load->database('default');
    $this->jwt_actions->authorize('780', $_SERVER['HTTP_HOST']);
 
    $this->load->model('ComisionesNeo_model');
   }

  public function index(){
    redirect(base_url());
  }
  
  public function prueba  (){
    $QUERY_V = $this->db->query("SELECT MAX(idResidencial) DATA_V FROM residenciales ");
    $DAT = $QUERY_V->row()->DATA_V;
    $lotePruebas = 66018;
    //  echo json_encode($this->ComisionesNeo_model->getStatusNeodata($lotePruebas)->result_array(),JSON_NUMERIC_CHECK);
    for($j = 1; $j < $DAT+1; $j++){
      $datos = $this->ComisionesNeo_model->getLotesPagados($j)->result_array();
    if(count($datos) > 0){
        $data = array();
         $datos = $this->ComisionesNeo_model->getLotesPagadosAutomatica($j);
        $final_data = array();                     
         echo($j);
        for($i = 0; $i < COUNT($datos); $i++){
            $data[$i] = $this->ComisionesNeo_model->getGeneralStatusFromNeodata($datos[$i]['referencia'], $datos[$i]['idResidencial']);
              // echo('<pre>');
              echo json_encode($datos[$i] );
              echo('datos');
              // echo('</pre>');
              echo('<pre>');
                echo json_encode($data[$i] );
              echo('</pre>');
              echo('data');
            if(!empty($data)){
                echo('pre!!!!!!!!!!!!!!!!!!!!!!!!!!');
              // echo json_encode($datos[$i] );
              // echo('</pre>');
                if($data[$i]->Marca == 1){
                  echo('lollll!!!!!!!!!!!!!!!!!!!!!!l.!!!!');
                  if($data[$i]->Aplicado > ($datos[$i]['ultimo_pago']+100)){
                      // $d2 = $this->ComisionesNeo_model->getStatusNeodata($datos[$i]['id_lote']);   
                      echo('<pre>');
                      echo('</pre>');             
                        //   $this->ComisionesNeo_model->UpdateBanderaPagoComision($datos[$i]['id_lote'], $data[$i]->Bonificado, $data[$i]->FechaAplicado, $data[$i]->fpoliza, $data[$i]->Aplicado);
                      // $contador ++;
                    }else{
               //         $this->ComisionesNeo_model->UpdateBanderaPagoComisionNO($datos[$i]['id_lote']);
                    }
                }else{
                    // $this->ComisionesNeo_model->UpdateBanderaPagoComisionNO($datos[$i]['id_lote']);
                }
                // $this->ComisionesNeo_model->UpdateBanderaPagoComisionNO($datos[$i]['id_lote']);
              }else{
            }
        }
        //  for($i = 0; $i < COUNT($datos); $i++){
        //     $data[$i] = $this->ComisionesNeo_model->getGeneralStatusFromNeodata($datos[$i]['referencia'], $datos[$i]['idResidencial']);
        //     if(!empty($data)){
        //         if($data[$i]->Marca == 1){
        //                  $this->ComisionesNeo_model->UpdateBanderaPagoComision2($datos[$i]['id_lote'], $data[$i]->Bonificado, $data[$i]->fpoliza, $data[$i]->Aplicado);
        //                 $contador ++;
        //         }else{
        //              echo NULL;
        //         }
        //     }else{
        //          echo NULL;
        //     }
        // }
    }else{
        echo NULL;
    }
       }
    $informacion_de_dispersion = array();
    // if(count($informacion_de_dispersion) > 0){
    //   // switch (){
    //   // }
    // }
    echo ('Dispersión automatica');
  }


  





  public function InsertNeo(){
    $lote_1 =  $this->input->post("idLote");
    $bonificacion =  $this->input->post("bonificacion");
    $responses = $this->Comisiones_model->validateDispersionCommissions($lote_1)->result_array();
    if(sizeof($responses) > 0 && $responses[0]['bandera'] != 0) {
      $respuesta[0] = 2;
  } else {
  
          $disparador =  $this->input->post("id_disparador");
          if($disparador == '1' || $disparador == 1){
              $lote_1 =  $this->input->post("idLote");
              $pending_1 =  $this->input->post("pending");
              $abono_nuevo = $this->input->post("abono_nuevo[]");
              $rol = $this->input->post("rol[]");
              $id_comision = $this->input->post("id_comision[]");
              $pago = $this->input->post("pago_neo");
              $suma = 0;
              $replace = [",","$"];
              for($i=0;$i<sizeof($id_comision);$i++){
                $var_n = str_replace($replace,"",$abono_nuevo[$i]);
                $respuesta = $this->Comisiones_model->insert_dispersion_individual($id_comision[$i], $rol[$i], $var_n, $pago);
                }
              for($i=0;$i<sizeof($abono_nuevo);$i++){
                $var_n = str_replace($replace,"",$abono_nuevo[$i]);
                $suma = $suma + $var_n ;
              }
              $resta = $pending_1 - $pago;
                $this->Comisiones_model->UpdateLoteDisponible($lote_1);
              $respuesta = $this->Comisiones_model->update_pago_dispersion($suma, $lote_1, $pago);
            }else if($disparador == '0' || $disparador == 0){
              $replace = [",","$"];
              $id_usuario = $this->input->post("id_usuario[]");
              $comision_total = $this->input->post("comision_total[]");
              $porcentaje = $this->input->post("porcentaje[]");
              $id_rol = $this->input->post("id_rol[]");
              $comision_abonada = $this->input->post("comision_abonada[]");
              $comision_pendiente = $this->input->post("comision_pendiente[]");
              $comision_dar = $this->input->post("comision_dar[]");
  
              $pago_neo = $this->input->post("pago_neo");
              $porcentaje_abono = $this->input->post("porcentaje_abono");
              $abonado = $this->input->post("abonado");
              $total_comision = $this->input->post("total_comision");
              $pendiente = $this->input->post("pendiente");
              $idCliente = $this->input->post("idCliente");
  
              $tipo_venta_insert = $this->input->post('tipo_venta_insert'); 
              $lugar_p = $this->input->post('lugar_p');
              $totalNeto2 = $this->input->post('totalNeto2');

              $banderita = 0;
              $PorcentajeAsumar=0;
              // 1.- validar tipo venta
              if($tipo_venta_insert <= 6 || $tipo_venta_insert == 11 || $tipo_venta_insert == 13){
                if($porcentaje_abono < 8){
                  $PorcentajeAsumar = 8 - $porcentaje_abono;
                  $banderita=1;
                  $porcentaje_abono =8;
                }
              }
              
              $pivote=0;
  
              for ($i=0; $i <count($id_usuario) ; $i++) { 

                if($banderita == 1 && $id_rol[$i] == 45){
                  $banderita=0;

                  
                  $comision_total[$i] = $totalNeto2 * (($porcentaje[$i] + $PorcentajeAsumar) / 100 );  
                  $porcentaje[$i] = $porcentaje[$i] + $PorcentajeAsumar;
                 
                }

                if($id_rol[$i] == 1){
                  $pivote=str_replace($replace,"",$comision_total[$i]);
                }

                $respuesta =  $this->Comisiones_model->InsertNeo($lote_1,$id_usuario[$i],str_replace($replace,"",$comision_total[$i]),$this->session->userdata('id_usuario'),$porcentaje[$i],str_replace($replace,"",$comision_dar[$i]),str_replace($replace,"",$pago_neo),$id_rol[$i],$idCliente,$tipo_venta_insert);
              
              }
              $this->Comisiones_model->UpdateLoteDisponible($lote_1);
              $respuesta =  $this->Comisiones_model->InsertPagoComision($lote_1,str_replace($replace,"",$total_comision),str_replace($replace,"",$abonado),$porcentaje_abono,str_replace($replace,"",$pendiente),$this->session->userdata('id_usuario'),str_replace($replace,"",$pago_neo),str_replace($replace,"",$bonificacion)); 
  
                    if($banderita == 1){
                      $total_com = $totalNeto2 * (($PorcentajeAsumar) / 100 );
                       $this->Comisiones_model->InsertNeo($lote_1,4824,$total_com,$this->session->userdata('id_usuario'),$PorcentajeAsumar,($pivote*$PorcentajeAsumar),str_replace($replace,"",$pago_neo),45,$idCliente,$tipo_venta_insert);

                    }

               
            }


            $validatePenalization = $this->Comisiones_model->validatePenalization($lote_1)->result_array();

            if(sizeof($validatePenalization) > 0 ) {

              $respuesta =  $this->InsertPena($lote_1);

            }else{
              $respuesta = true;
            }

            // $respuesta =  $this->InsertPena($lote_1);
           
            // validar si aplica penalización

  
  
  }
  echo json_encode( $respuesta );
  }

}

