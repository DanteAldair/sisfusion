<link href="<?= base_url() ?>dist/css/reportDasboard.css" rel="stylesheet"/>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <div class="card p-0 cardMiniChart">
                <div class="container-fluid">
                    <div class="row pl-2 pt-2 pr-2">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 p-0 info">
                            <p class="m-0">ventas<br> <span class="str">contratadas</span></p>
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 p-0 d-flex align-center justify-end">
                            <label class="selectMini m-0">4 meses <span class="material-icons">show_chart</span></label>
                        </div>
                    </div>
                    <div class="row pl-2 pr-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <p class="mt-1 money" id="totventasContratadas">$0.00</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="p-0 col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="boxMiniCharts" id="ventasContratadas"></div>
                        </div>
                        <button type="btn" class="moreMiniChart d-flex justify-center align-center" data-toggle="tooltip" data-placement="bottom" title="Más detalle" onclick="chartDetail(this, 'vc')" data-name ="ventas_contratadas">  
                            <i class="fas fa-expand-alt"></i>
                        </button>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <div class="card p-0 cardMiniChart">
                <div class="container-fluid">
                    <div class="row pl-2 pt-2 pr-2">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 p-0 info">
                            <p class="m-0">ventas<br><span class="str">apartadas</span></p>
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 p-0 d-flex align-center justify-end">
                            <label class="selectMini m-0">4 meses <span class="material-icons">show_chart</span></label> 
                        </div>
                    </div>
                    <div class="row pl-2 pr-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <p class="mt-1 money" id="totventasApartadas">$0.00</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="p-0 col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="boxMiniCharts" id="ventasApartadas"></div>
                        </div>
                        <button type="btn" class="moreMiniChart d-flex justify-center align-center" data-toggle="tooltip" data-placement="bottom" title="Más detalle" onclick="chartDetail(this, 'va')" data-name ="ventas_apartadas">  
                            <i class="fas fa-expand-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <div class="card p-0 cardMiniChart">
                <div class="container-fluid">
                    <div class="row pl-2 pt-2 pr-2">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 p-0 info">
                            <p class="m-0">canceladas<br> <span class="str">contratadas</span></p>
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 p-0 d-flex align-center justify-end">
                            <label class="selectMini m-0">4 meses <span class="material-icons">show_chart</span></label> 
                        </div>
                    </div>
                    <div class="row pl-2 pr-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <p class="mt-1 money" id="totcanceladasContratadas">$0.00</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="p-0 col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="boxMiniCharts" id="canceladasContratadas"></div>
                        </div>
                        <button type="btn" class="moreMiniChart d-flex justify-center align-center" data-toggle="tooltip" data-placement="bottom" title="Más detalle" onclick="chartDetail(this, 'cc')" data-name ="canceladas_contratadas">  
                            <i class="fas fa-expand-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <div class="card p-0 cardMiniChart">
                <div class="container-fluid">
                    <div class="row pl-2 pt-2 pr-2">
                        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 p-0 info">
                            <p class="m-0">canceladas<br> <span class="str">apartadas</span></p>
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 p-0 d-flex align-center justify-end">
                            <label class="selectMini m-0">4 meses <span class="material-icons">show_chart</span></label> 
                        </div>
                    </div>
                    <div class="row pl-2 pr-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 p-0">
                            <p class="mt-1 money" id="totcanceladasApartadas">$0.00</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="p-0 col-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="boxMiniCharts" id="canceladasApartadas"></div>
                        </div>
                        <button type="btn" class="moreMiniChart d-flex justify-center align-center" data-toggle="tooltip" data-placement="bottom" title="Más detalle" onclick="chartDetail(this, 'ca')" data-name ="canceladas_apartadas">  
                            <i class="fas fa-expand-alt"></i>
                        </button> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- mini charts -->

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="boxAccordions">
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url()?>dist/js/controllers/dashboard/reporte/dashboardReport.js"></script>