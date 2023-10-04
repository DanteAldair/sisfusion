<?php class Catalogos_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    function getCatalogosInformation(){

        return $this->db->query(" SELECT oxc.id_opcion, oxc.id_catalogo, ca.nombre catalogo, oxc.nombre, oxc.estatus FROM opcs_x_cats oxc
        INNER JOIN catalogos ca ON ca.id_catalogo = oxc.id_catalogo
        WHERE oxc.id_catalogo IN (11, 77) AND (oxc.estatus = 0 OR oxc.estatus = 1)
        ORDER BY oxc.id_catalogo, oxc.nombre");
    }

    public function editarModelCatalogos($datos){
        
        return $this->db->query("UPDATE opcs_x_cats SET estatus = ".$datos['estatus_n']." WHERE id_opcion = ".$datos['id_opcion']." AND id_catalogo = ".$datos['idCatalogosEdit']."");
    
    }

    public function editarNombreCatalogo($datos){
        return $this->db->query("UPDATE opcs_x_cats SET nombre = '".$datos['editarCatalogo']."' WHERE id_opcion = ".$datos['idOpcion']." AND id_catalogo = ".$datos['id_catalogo']."");
    }

    public function getCatalogosInfo(){
        return $this->db->query("SELECT id_catalogo, nombre, estatus FROM catalogos where id_catalogo in (11,77)");
    }

    function  insertOpcion(){
        return $this->db->query("SELECT TOP (1) id_opcion + 1 AS lastId FROM opcs_x_cats WHERE id_catalogo IN(11,77) ORDER BY id_opcion DESC")->row();
    }

    public function insertarCampo($datos){
        return $this->db->query("INSERT INTO opcs_x_cats VALUES (".$datos['id'].",'".$datos['id_catalogo']."','".$datos['nombre']."',1,'".$datos['fecha_creacion']."',1,NULL)");
    }

}