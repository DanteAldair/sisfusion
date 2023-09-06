<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 */
class Documentacion_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }

    public function generateFilename($idLote, $idDocumento) {
        return $this->db->query("SELECT CONCAT(r.nombreResidencial, '_', SUBSTRING(cn.nombre, 1, 4), '_', l.idLote, 
        '_', c.id_cliente,'_TDOC', hd.tipo_doc, SUBSTRING(hd.movimiento, 1, 4),
        '_', UPPER(REPLACE(REPLACE(CONVERT(varchar, GETDATE(),109), ' ', ''), ':', ''))) fileName FROM lotes l 
        INNER JOIN clientes c ON c.idLote = l.idLote
        INNER JOIN condominios cn ON cn.idCondominio = l.idCondominio
        INNER JOIN residenciales r ON r.idResidencial = cn.idResidencial
        INNER JOIN historial_documento hd ON hd.idLote = l.idLote AND hd. idDocumento = $idDocumento
        WHERE l.idLote = $idLote");
    }

    public function getFilename($idDocumento) {
        return $this->db->query("SELECT * FROM historial_documento WHERE idDocumento = $idDocumento");
    }

    function getRejectionReasons($tipo_proceso) {
        return $this->db->query("SELECT id_motivo, tipo_documento, motivo, tipo_proceso FROM motivos_rechazo WHERE estatus = 1 AND tipo_proceso = $tipo_proceso")->result_array();
    }

    function saveRejectionReasons($data) {
        $response = $this->db->insert_batch("motivos_rechazo_x_documento", $data);
        if (!$response)
            return 0;
        else
            return 1;
    }

    function getRejectReasons($idDocumento, $idLote, $documentType) {
        $and = "AND mrxd.tipo = $documentType";
        if ($documentType == 30) $clause = "INNER JOIN deposito_seriedad main ON main.id_cliente = cl.id_cliente";
        else if ($documentType == 32) $clause = "INNER JOIN autorizaciones main ON main.idCliente = cl.id_cliente";
        else if ($documentType == 33) $clause = "INNER JOIN prospectos main ON main.id_prospecto = cl.id_prospecto";
        else if ($documentType == 34) $clause = "INNER JOIN evidencia_cliente main ON main.idCliente = cl.id_cliente";
        else {
            $clause = "INNER JOIN historial_documento main ON main.idLote = l.idLote AND main.status = 1 AND main.idDocumento = $idDocumento";
            $and = "";
        }
        return $this->db->query("SELECT mrxd.id_mrxdoc, mr.motivo, UPPER(CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno)) userValidacion FROM lotes l 
        INNER JOIN clientes cl ON cl.idLote = l.idLote AND cl.status = 1
        $clause
        INNER JOIN usuarios u ON u.id_usuario = main.validado_por
        INNER JOIN motivos_rechazo_x_documento mrxd ON mrxd.id_documento = $idDocumento AND mrxd.estatus = 1 $and
        INNER JOIN motivos_rechazo mr ON mr.id_motivo = mrxd.id_motivo
        WHERE l.status = 1 AND l.idLote = $idLote");
    }

    function getRejectReasonsTwo($idDocumento, $idSolicitud, $documentType) {
        return $this->db->query("SELECT mrxd.id_mrxdoc, mr.motivo, UPPER(CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno)) userValidacion 
        FROM solicitud_escrituracion se      
        INNER JOIN documentos_escrituracion de ON de.idSolicitud = se.idSolicitud
        INNER JOIN usuarios u ON u.id_usuario = de.validado_por
        INNER JOIN motivos_rechazo_x_documento mrxd ON mrxd.id_documento = $idDocumento AND mrxd.estatus = 1 AND mrxd.tipo = $documentType
        INNER JOIN motivos_rechazo mr ON mr.id_motivo = mrxd.id_motivo
        WHERE se.idSolicitud = $idSolicitud");
    }

    function getReasonsForRejectionByDocument($id_documento, $tipo_proceso) {
        return $this->db->query("SELECT id_motivo, 
        CASE WHEN oxc.descripcion IS NULL THEN 'POR SOLICITUD' ELSE oxc.descripcion END nombre_documento, 
        UPPER(mr.motivo) AS motivo, mr.estatus,
        CASE mr.estatus WHEN 1 THEN '<span class=\"label lbl-green\">ACTIVO</span>'
        ELSE '<span class=\"label lbl-warning\">INACTIVO</span>' END estatus_motivo
        FROM motivos_rechazo mr 
        LEFT JOIN documentacion_escrituracion oxc ON oxc.id_documento = mr.tipo_documento 
        WHERE mr.tipo_documento = $id_documento AND mr.tipo_proceso = $tipo_proceso");
    }


    function getDocumentsInformation_Escrituracion($idLote) {
        return $this->db->query("SELECT se.idSolicitud, de.* FROM solicitud_escrituracion se
        INNER JOIN documentos_escrituracion de ON de.idSolicitud = se.idSolicitud
        WHERE se.idLote = $idLote");
    }

    function getLotesList_escrituracion($idCondominio) {
        return $this->db->query("SELECT * FROM lotes lo
        INNER JOIN solicitud_escrituracion se ON se.idLote = lo.idLote
        WHERE lo.idCondominio = $idCondominio");
    }
    function getCatalogOptions() {
        return $this->db->query("SELECT id_documento as id_opcion,descripcion as nombre,id_documento as id_catalago FROM documentacion_escrituracion");
    }

    public function getClientesPorLote($idLote) {
		$result = $this->db->query("SELECT id_cliente, UPPER(CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno)) nombreCliente, status FROM clientes WHERE idLote = $idLote AND isNULL(noRecibo, '') != 'CANCELADO' ORDER BY status DESC")->result_array();
		return count($result) > 0 ? $result: array();
	}

    public function obtenerDocumentacionActiva($idLote, $idCliente)
    {
        $query = $this->db->query("SELECT * FROM historial_documento WHERE idLote = $idLote AND idCliente = $idCliente AND status = 1");
        return $query->result_array();
    }

    public function obtenerLotePorId($idLote)
    {
        $query = $this->db->query("SELECT * FROM lotes WHERE idLote = $idLote");
        return $query->row();
    }

    public function darBajaDocumentacion($idLote, $idCliente)
    {
        $this->db->query("UPDATE historial_documento SET status = 0 WHERE status = 1 and idLote = $idLote AND idCliente = $idCliente");
    }

    public function obtenerDocumentacionPorReubicacion()
    {
        $query = $this->db->query('SELECT * FROM opcs_x_cats WHERE id_catalogo = 98 AND estatus = 1');
        return $query->result_array();
    }
}
