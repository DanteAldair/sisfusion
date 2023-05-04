<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 */
class PaquetesCorrida_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getTipoDescuento()
    {
        return $this->db->query("select * from tipos_condiciones where id_tcondicion in(1,2,4,12,13)")->result_array();
    }
    public  function get_lista_sedes(){
    return $this->db->query("SELECT * FROM sedes where id_sede in(1,2,3,4,5,6,9) ORDER BY nombre");
    }
    
    public function getResidencialesList($id_sede)
    {
        return $this->db->query("SELECT idResidencial, nombreResidencial, UPPER(CAST(descripcion AS VARCHAR(75))) descripcion, empresa FROM residenciales WHERE status = 1 and sede_residencial=$id_sede ORDER BY nombreResidencial ASC")->result_array();
    }
    public function getDescuentosPorTotal($tdescuento,$id_condicion,$eng_top,$apply)
    {
        return $this->db->query("SELECT id_tdescuento,inicio,fin,id_condicion,eng_top,apply,max(id_descuento) AS id_descuento,porcentaje 
        FROM descuentos WHERE id_tdescuento = $tdescuento AND id_condicion = $id_condicion AND eng_top = $eng_top AND apply = $apply and inicio is null 
        group by id_tdescuento,inicio,fin,id_condicion,eng_top,apply,porcentaje 
        order by porcentaje");
    }
 
    public function UpdateLotes($desarrollos,$cadena_lotes,$query_superdicie,$query_tipo_lote,$usuario,$inicio,$fin){
        $this->db->query("UPDATE  l 
        set l.id_descuento = '$cadena_lotes',usuario='$usuario'
        from lotes l
        inner join condominios c on c.idCondominio=l.idCondominio 
        inner join residenciales r on r.idResidencial=c.idResidencial
        where r.idResidencial in($desarrollos)  and l.idStatusLote = 1 
        $query_superdicie
        $query_tipo_lote");

        $this->db->query("UPDATE  l 
        SET l.id_descuento = '$cadena_lotes',usuario='$usuario'
        FROM lotes l
        INNER JOIN condominios c ON c.idCondominio=l.idCondominio 
        INNER JOIN residenciales r ON r.idResidencial=c.idResidencial
        INNER JOIN clientes cl ON cl.id_cliente=l.idCliente
        WHERE r.idResidencial IN($desarrollos) AND l.idStatusLote IN (3, 2)
        AND cl.fechaApartado BETWEEN '$inicio 00:00:00.000' AND '$fin 23:59:59.999'
        $query_superdicie
        $query_tipo_lote"); 
    }

    public function insertBatch($table, $data)
    {
      $row = $this->db->insert_batch($table, $data);
        if ($row === FALSE) { 
            return false;
        } else { 
            return true;
        }
    }

    public function getDescuentos($objDescuentos, $primeraCarga)
    {
        list($desTotal, $desEnganche, $desPrecioM2, $desBono, $desMSI) = $this->getDescuentosQueries();

        if($primeraCarga){
            $data = $this->db->query("$desTotal UNION ALL $desEnganche UNION ALL $desPrecioM2 UNION ALL $desBono UNION ALL $desMSI ORDER BY id_condicion, d.porcentaje");
        }
        else if( $type == 1 ) // total
            $data = $this->db->query("$desTotal ORDER BY d.porcentaje");
        else if( $type == 2) // Enganche
            $data = $this->db->query("$desEnganche ORDER BY d.porcentaje");
        else if( $type == 4) // Precio m2
            $data = $this->db->query("$desPrecioM2 ORDER BY d.porcentaje");
        else if( $type == 12) // Bono
            $data = $this->db->query("$desBono ORDER BY d.porcentaje");
        else if( $type == 13) // MSI
            $data = $this->db->query("$desMSI ORDER BY d.porcentaje");

        return $data;
    }

    public function getDescuentosQueries(){
        $desTotal = "SELECT c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply,
        MAX(d.id_descuento) AS id_descuento, d.porcentaje 
        FROM descuentos d
        INNER JOIN condiciones c ON c.id_condicion = d.id_condicion
        WHERE d.id_tdescuento = 1 
        AND d.id_condicion = 1 
        AND d.eng_top = 0 
        AND d.apply = 1
        AND d.inicio IS NULL
        GROUP BY c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, d.porcentaje ";

        $desEnganche = "SELECT c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply,
        MAX(d.id_descuento) AS id_descuento, d.porcentaje 
        FROM descuentos d
        INNER JOIN condiciones c ON c.id_condicion = d.id_condicion
        WHERE d.id_tdescuento = 1 
        AND d.id_condicion = 1 
        AND d.eng_top = 0 
        AND d.apply = 1
        AND d.inicio IS NULL
        GROUP BY c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, d.porcentaje ";

        $desPrecioM2 = "SELECT c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, MAX(d.id_descuento) AS id_descuento, d.porcentaje 
        FROM descuentos d
        INNER JOIN condiciones c on c.id_condicion = d.id_condicion
        WHERE d.id_tdescuento = 1 
        AND d.id_condicion = 4 
        AND d.eng_top = 0 
        AND d.apply = 1
        AND d.inicio IS NULL 
        GROUP BY c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, d.porcentaje ";

        $desBono = "SELECT c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, MAX(d.id_descuento) AS id_descuento, d.porcentaje 
        FROM descuentos d
        INNER JOIN condiciones c on c.id_condicion = d.id_condicion
        WHERE d.id_tdescuento = 1 
        AND d.id_condicion = 12 
        AND d.eng_top = 1 
        AND d.apply = 1
        AND d.inicio IS NULL 
        GROUP BY c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, d.porcentaje";

        $desMSI = "SELECT c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, MAX(d.id_descuento) AS id_descuento, d.porcentaje 
        FROM descuentos d
        INNER JOIN condiciones c on c.id_condicion=d.id_condicion
        WHERE d.id_tdescuento = 1 
        AND d.id_condicion = 13 
        AND d.eng_top = 1 
        AND d.apply = 1
        AND d.inicio IS NULL 
        GROUP BY c.descripcion, d.id_tdescuento, d.inicio, d.fin, d.id_condicion, d.eng_top, d.apply, d.porcentaje";

        return [$desTotal, $desEnganche, $desPrecioM2, $desBono, $desMSI];
    }

    public function SaveNewDescuento($tdescuento,$id_condicion,$eng_top,$apply,$descuento){
      $response =  $this->db->query("INSERT INTO descuentos VALUES($tdescuento,NULL,NULL,$id_condicion,$descuento,$eng_top,$apply,NULL)"); 
        if (! $response ) {
            return $finalAnswer = 0;
        } else {
            return $finalAnswer = 1;
        }
    }

    public function ValidarDescuento($tdescuento,$id_condicion,$eng_top,$apply,$descuento)
    {
        return $this->db->query("SELECT c.descripcion,d.id_tdescuento,d.inicio,d.fin,d.id_condicion,d.eng_top,d.apply,max(d.id_descuento) AS id_descuento,d.porcentaje 
        FROM descuentos d
		INNER JOIN condiciones c on c.id_condicion=d.id_condicion
		WHERE d.id_tdescuento = $tdescuento 
		AND d.id_condicion = $id_condicion 
		AND d.eng_top = $eng_top 
		AND d.apply = $apply
        AND d.porcentaje=$descuento
		and d.inicio is null 
        group by c.descripcion,d.id_tdescuento,d.inicio,d.fin,d.id_condicion,d.eng_top,d.apply,d.porcentaje 
        order by d.porcentaje");
    }
 

public function getPaquetesByLotes($desarrollos,$query_superdicie,$query_tipo_lote,$superficie,$inicio,$fin){
    date_default_timezone_set('America/Mexico_City');
    $hoy2 = date('Y-m-d H:i:s');
    
    $cuari1 =  $this->db->query("SELECT DISTINCT(l.idCondominio) FROM lotes l
        INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
        INNER JOIN residenciales r ON r.idResidencial = c.idResidencial
        WHERE r.idResidencial IN ($desarrollos)
        $query_superdicie
        $query_tipo_lote 
        GROUP BY l.idCondominio")->result_array();
        
    $imploded = array();
    foreach($cuari1 as $array) {
        $imploded[] = implode(',', $array);
    }
    
    $stack= array();
  
    for ($i=0; $i < sizeof($cuari1); $i++) {
        $arrCondominio= implode(",", $cuari1[$i]);
        $queryRes =  $this->db->query("DECLARE @condominio varchar(200), @tags VARCHAR(MAX); 
        SET @condominio = ($arrCondominio) 
      
        /*INICIO DEL PROCESO*/ 
        SET @tags = (SELECT STRING_AGG(CONVERT(VARCHAR(MAX),(id_descuento) ), ',') 
        FROM lotes l 
        INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
        INNER JOIN residenciales r ON r.idResidencial = c.idResidencial 
        WHERE c.idCondominio IN (@condominio)) 
      
        (SELECT 
        @condominio condominio, STRING_AGG(id_paquete, ',') paquetes, fecha_inicio, fecha_fin, 
        UPPER(CONCAT('PAQUETE ', DATENAME(MONTH, fecha_inicio), ' ', YEAR(fecha_inicio))) descripcion 
        FROM paquetes 
        WHERE id_paquete in (SELECT DISTINCT(value) FROM STRING_SPLIT(@tags, ',') WHERE RTRIM(value) <> '') 
        GROUP BY fecha_inicio, fecha_fin)");
        
        foreach ($queryRes->result() as  $valor) {
  
        array_push($stack, array('condominio' => $valor->condominio, 'paquetes' => $valor->paquetes, 'fecha_inicio' => $valor->fecha_inicio, 'fecha_fin' => $valor->fecha_fin, 'descripcion' => $valor->descripcion));
  
    }
  }
  $getPaquetesByName = $stack;
  
//   print_r( $getPaquetesByName);
  $datosInsertar_x_condominio = array();
  for ($o=0; $o <count($getPaquetesByName) ; $o++) {
    $json = array();
    if(!empty($getPaquetesByName[$o]['paquetes'])){
        array_push($json,array( 
            "paquetes" => $getPaquetesByName[$o]['paquetes'],
            "tipo_superficie" => array("tipo" => $superficie,
            "sup1" => $inicio,
            "sup2" => $fin) ));
            
            $json = json_encode($json);
            $json = ltrim($json,'[');
            $json = rtrim($json,']');
            
            $array_x_condominio =array(
                'id_condominio' => $getPaquetesByName[$o]['condominio'],
                'id_paquete' => $json,
                'nombre' => $getPaquetesByName[$o]['descripcion'],
                'fecha_inicio' =>  $getPaquetesByName[$o]['fecha_inicio'],
                'fecha_fin' =>  $getPaquetesByName[$o]['fecha_fin'],
                'estatus' => 1,
                'creado_por' => $this->session->userdata('id_usuario'),
                'fecha_modificacion' =>  $hoy2,
                'modificado_por' => $this->session->userdata('id_usuario'),
                'list_paquete' => $getPaquetesByName[$o]['paquetes']);
                
                array_push($datosInsertar_x_condominio,$array_x_condominio);
            }
        }
        if(count($datosInsertar_x_condominio) > 0){
            $this->PaquetesCorrida_model->insertBatch('paquetes_x_condominios',$datosInsertar_x_condominio);
        }
    }
     
    public function getPaquetes($query_tipo_lote,$query_superdicie,$desarrollos, $fechaInicio, $fechaFin){
        return  $this->db->query("SELECT STRING_AGG(t.descuentos, ',') id_descuento FROM (
        SELECT DISTINCT(id_descuento) descuentos
        FROM lotes l
        INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
        INNER JOIN residenciales r ON r.idResidencial = c.idResidencial
        where l.idStatusLote = 1 AND r.idResidencial IN ($desarrollos) AND id_descuento IS NOT NULL
        $query_superdicie
        $query_tipo_lote
        UNION 
        SELECT DISTINCT(id_descuento) descuentos
        FROM lotes l
        INNER JOIN clientes cl ON cl.id_cliente = l.idCliente AND cl.status = 1 AND cl.fechaApartado BETWEEN '$fechaInicio 00:00:00.000' AND '$fechaFin 23:59:59.999'
        INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
        INNER JOIN residenciales r ON r.idResidencial = c.idResidencial
        where l.idStatusLote = 3 AND r.idResidencial IN ($desarrollos) AND id_descuento IS NOT NULL
        $query_superdicie
        $query_tipo_lote
        ) t")->result_array();
    }

    public function getPaquetesById($id_paquete){
        return  $this->db->query("SELECT * FROM paquetes WHERE id_paquete in($id_paquete)")->result_array();
    }

    public function getDescuentosByPlan($id_paquete,$id_tcondicion){
        return  $this->db->query("select r.*,d.*,c.descripcion from relaciones r 
        inner join descuentos d on d.id_descuento=r.id_descuento
        inner join condiciones c on c.id_condicion = d.id_condicion
        inner join tipos_condiciones tc on tc.id_tcondicion=c.id_tcondicion
        where r.id_paquete in ($id_paquete) and c.id_condicion=$id_tcondicion  order by r.prioridad asc")->result_array();
    }
    

    public function getPaquetesDisponiblesyApart($query_tipo_lote,$query_superdicie,$desarrollos, $fechaInicio, $fechaFin){
            $paquetes =  $this->db->query("SELECT STRING_AGG(t.descuentos, ',') id_descuento FROM (
                SELECT DISTINCT(id_descuento) descuentos
                FROM lotes l
                INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
                INNER JOIN residenciales r ON r.idResidencial = c.idResidencial
                where l.idStatusLote = 1 AND r.idResidencial IN ($desarrollos) AND id_descuento IS NOT NULL
                $query_superdicie
                $query_tipo_lote
                ) t")->result_array();
                if(count($paquetes) == 0){
                    $paquetes =  $this->db->query("SELECT STRING_AGG(t.descuentos, ',') id_descuento FROM (
                        SELECT DISTINCT(id_descuento) descuentos
                        FROM lotes l
                        INNER JOIN clientes cl ON cl.id_cliente = l.idCliente AND cl.status = 1 AND cl.fechaApartado BETWEEN '$fechaInicio 00:00:00.000' AND '$fechaFin 23:59:59.999'
                        INNER JOIN condominios c ON c.idCondominio = l.idCondominio 
                        INNER JOIN residenciales r ON r.idResidencial = c.idResidencial
                        where l.idStatusLote = 3 AND r.idResidencial IN ($desarrollos) AND id_descuento IS NOT NULL
                        $query_superdicie
                        $query_tipo_lote
                        ) t")->result_array();
                }
            return $paquetes;
        

        
    }
    



}
