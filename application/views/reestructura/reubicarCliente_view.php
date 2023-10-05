<link href="<?= base_url() ?>dist/css/datatableNFilters.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
<body>
    <style>
        #checkDS .boxChecks {
  background-color: #eeeeee;
  width: 100%;
  border-radius: 27px;
  box-shadow: none;
  padding: 5px !important;
}
#checkDS .boxChecks .checkstyleDS {
  cursor: pointer;
  user-select: none;
  display: block;
}
#checkDS .boxChecks .checkstyleDS span {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 31px;
  border-radius: 9999px;
  overflow: hidden;
  transition: linear 0.3s;
  margin: 0;
  font-weight: 100;
}
#checkDS .boxChecks .checkstyleDS span:nth-child(2) {
  margin: 0 3px;
}
#checkDS .boxChecks .checkstyleDS span:hover {
  box-shadow: none;
}
#checkDS .boxChecks .checkstyleDS input {
  pointer-events: none;
  display: none;
}
#checkDS .boxChecks .checkstyleDS input:checked + span {
  transition: 0.3s;
  font-weight: 400;
  color: #0a548b;
}
#checkDS .boxChecks .checkstyleDS input:checked + span:before {
  font-family: FontAwesome !important;
  content: "\f00c";
  color: #0a548b;
  font-size: 18px;
  margin-right: 5px;
}
    </style>
    <div class="wrapper">
        <?php  $this->load->view('template/sidebar'); ?>
        <div class="content boxContent">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="encabezadoBox">
                                    <h3 class="card-title center-align">Reubicación de clientes existentes</h3>
                                    <p class="card-title pl-1"></p>
                                </div>
                                <div class="material-datatables">
                                    <table id="reubicacionClientes" class="table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>PROYECTO</th>
                                                <th>CONDOMINIO</th>
                                                <th>LOTE</th>
                                                <th>ID LOTE</th>
                                                <th>CLIENTE</th>
                                                <th>ASESOR</th>
                                                <th>COORDINADOR</th>
                                                <th>GERENTE</th>
                                                <th>SUBDIRECTOR</th>
                                                <th>DIRECTOR REGIONAL</th>
                                                <th>DIRECTOR REGIONAL 2</th>
                                                <th>FECHA DE APARTADO</th>
                                                <th>SUPERFICIE</th>
                                                <th>COSTO M2 FINAL</th>
                                                <th>TOTAL</th>
                                                <th>ESTATUS</th>
                                                <th>ASIGNADO A</th>
                                                <th>ACCIONES</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('template/footer_legend');?>
    </div>
</body>
<?php $this->load->view('template/footer');?>
<script src="<?=base_url()?>dist/js/core/modal-general.js"></script>
<script src="<?=base_url()?>dist/js/controllers/reestructura/reubicacionClientes.js"></script>