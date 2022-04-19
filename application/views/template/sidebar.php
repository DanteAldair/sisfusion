<div class="sidebar" data-active-color="blue" data-background-color="white" data-image="<?=base_url()?>/dist/img/sidebar-1.jpg">
	<div class="logo"> 
		<a href="<?=base_url()?>#" class="simple-text">
            <img src="<?=base_url()?>static/images/cm_blue.png" width="70%">
		</a>
	</div>
	<div class="logo logo-mini">
		<a href="<?=base_url()?>#" class="simple-text" style="color: #0e4377;font-weight: 800;font-family: 'Times New Roman', Times, serif;">CM</a>
	</div>
	<div class="sidebar-wrapper">
		<div class="user hide">
			<div class="photo">
				<img src="../dist/img/faces/avatar.jpg" />
			</div>
			<div class="info">
				<a data-toggle="collapse" href="#collapseExample" class="collapsed">
					Tania Andrew
					<b class="caret"></b>
				</a>
				<div class="collapse" id="collapseExample">
					<ul class="nav">
						<li>
							<a href="#">My Profile</a>
						</li>
						<li>
							<a href="#">Edit Profile</a>
						</li>
						<li>
							<a href="#">Settings</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<br><br>
		<ul class="nav">
<?php
  $url = "https://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];	
  $url2 = "";
  $padreVal = 0;
$rol = $this->session->userdata('id_rol');
if($rol == 1 || $rol == 2 || $rol == 3 || $rol == 4 || $rol == 5 || $rol == 6 || $rol == 7 || $rol == 9)
{
    $url2 = base_url()."Ventas";
}
elseif ($rol == 11 || $rol == 23 || $rol == 26 || $rol == 34 || $rol == 35 || $rol == 38 || $rol == 41 || $rol == 49 || $rol == 50 || $rol == 40 || $rol == 54 || $rol == 58
|| $rol == 8 || $rol == 10 || $rol == 18 || $rol == 19 || $rol == 20 || $rol == 21 || $rol == 23 || $rol == 28 || $rol == 33 || $rol == 25 || $rol == 27 || $rol == 30
 || $rol == 36 || $rol == 22 || $rol == 53 || $rol == 8 || $rol == 12) {
    $url2 = base_url()."Administracion";
}
elseif ($rol == 12) {
    $url2 = base_url()."Caja";
}
elseif ($rol == 13 || $rol == 17 || $rol == 32 || $rol == 47) {
    $url2 = base_url()."Contraloria";
}
elseif ($rol == 14) {
    $url2 = base_url()."Direccion_administracion";
}
elseif ($rol == 15) {
    $url2 = base_url()."Juridico";
}
elseif ($rol == 16) {
    $url2 = base_url()."Contratacion";
}
elseif ($rol == 31) {
    $url2 = base_url()."Internomex";
}
if(isset($datos4))
{
            foreach($datos4 as $valor)
            {
                $padreVal = $valor->padre;
            }
}
    $c=0;
foreach($datos2 as $datos)
{ 			
        if($datos->padre == 0)
        {	
                                if($datos->hijos == 0)
                                { ?>
                                    <li class="nav-item hidden-xs  <?php if ($url == $url2 && $datos->nombre == "Inicio") { echo 'active'; }elseif($url == base_url().$datos->pagina  && $datos->nombre == "Asesores / Coordinadores"){echo 'active';}elseif($url == base_url().$datos->pagina && ($datos->nombre == "Revisión evidencia" || $datos->nombre == "Evidencias clientes" || $datos->nombre == "Eliminados de la lista")){echo 'active';}?>">
                                        <a class="nav-link" href="<?php if($datos->nombre == "Aparta en línea"){ echo $datos->pagina; } elseif($datos->nombre == "Asesores / Coordinadores"){echo base_url().$datos->pagina;}else {echo base_url().$datos->pagina;}?>" <?php if($datos->nombre == "Aparta en línea"){ echo ' target="_blank"';   } ?>>
                                            <i class="material-icons"><?=$datos->icono?></i>
                                            <p><?=$datos->nombre?></p>
                                        </a>
                                    </li>
                                <?php
                        }else{
              //  $valor = $datos->orden +1;

                            if($this->session->userdata('id_usuario') == 2826 || $this->session->userdata('id_usuario') == 2855 || $this->session->userdata('id_rol') == 17)
                            {
?>
 <li class="nav-item <?php if (isset($datos4)) {   if($padreVal == $datos->orden) {  echo 'active';}} ?>">
                                                <a data-toggle="collapse" href="#componentsExamples_<?=$c?>">
                                                    <i class="material-icons"><?=$datos->icono?></i>
                                                    <p><?php echo $datos->nombre; ?>
                                                        <b class="caret"></b>
                                                    </p>
                                                </a>
                                <div class="collapse" id="componentsExamples_<?=$c?>">
                                    <ul class="nav">
                                                    <?php
                                            foreach ($datos3 as $hijos) 
                                            {
                                            if($hijos->orden >= $datos->orden && $hijos->orden <= $datos->orden +1)	
                                                {		
                                                    ?>
                                                    <li class="<?php if ($url == base_url().$hijos->pagina) {echo 'active';} ?>">
                                                                                    <a href="<?= base_url().$hijos->pagina?>  "><?=$hijos->nombre?></a>
                                                                                </li>
                                                    <?php
                                                }
                                            }
                                    ?>
                                </ul>
                            </div>
                        </li>
<?php
                            }
                            else{
                                if($datos->nombre == "Expedientes <small>(Contraloría)</small>" || $datos->nombre == "Liberaciones")
                                {
?>
 <li class="nav-item <?php if (isset($datos4)) {   if($padreVal == $datos->orden) {  echo 'active';}} ?>">
                                                <a data-toggle="collapse" style="display: none;" href="#componentsExamples_<?=$c?>">
                                                    <i class="material-icons"><?=$datos->icono?></i>
                                                    <p><?php echo $datos->nombre; ?>
                                                        <b class="caret"></b>
                                                    </p>
                                                </a>
                                <div class="collapse" id="componentsExamples_<?=$c?>">
                                    <ul class="nav">
                                                    <?php
                                            foreach ($datos3 as $hijos) 
                                            {
                                            if($hijos->orden >= $datos->orden && $hijos->orden <= $datos->orden +1)	
                                                {		
                                                    ?>
                                                    <li class="<?php if ($url == base_url().$hijos->pagina) {echo 'active';} ?>">
                                                                                    <a href="<?= base_url().$hijos->pagina?>  "><?=$hijos->nombre?></a>
                                                                                </li>
                                                    <?php
                                                }
                                            }
                                    ?>
                                </ul>
                            </div>
                        </li>
<?php
                                }
                                else
                                {
 ?>
 <li class="nav-item <?php if (isset($datos4)) {   if($padreVal == $datos->orden) {  echo 'active';}} ?>">
                                                <a data-toggle="collapse" href="#componentsExamples_<?=$c?>">
                                                    <i class="material-icons"><?=$datos->icono?></i>
                                                    <p><?php echo $datos->nombre; ?>
                                                        <b class="caret"></b>
                                                    </p>
                                                </a>
                                <div class="collapse" id="componentsExamples_<?=$c?>">
                                    <ul class="nav">
                                                    <?php
                                            foreach ($datos3 as $hijos) 
                                            {
                                            if($hijos->orden >= $datos->orden && $hijos->orden <= $datos->orden +1)	
                                                {		
                                                    ?>
                                                    <li class="<?php if ($url == base_url().$hijos->pagina) {echo 'active';} ?>">
                                                                                    <a href="<?= base_url().$hijos->pagina?>  "><?=$hijos->nombre?></a>
                                                                                </li>
                                                    <?php
                                                }
                                            }
                                    ?>
                                </ul>
                            </div>
                        </li> 
 <?php
                                }

                            }



                    ?>
                       	
                    <?php } 
                    }		
                $c+=1; //contador para agregar a cada id de las opciones del menu
        }   
        
        if($this->session->userdata('estatus') == 1){

?>
<!---CÓDIGO PARA ABRIR EL SITEMA DE TICKETS------------->
<li class="nav-item ">
 <a data-toggle="collapse" href="#componentsExamples_T">
    <i class="material-icons">report</i>
        <p>TICKETS<b class="caret"></b></p>
</a>
    <div class="collapse" id="componentsExamples_T">
        <ul class="nav">
            <li class="">
              <a href="javascript: AddTicket()" >Agregar</a>
            </li>
        </ul>
    </div>
</li>
<?php } ?>

<!----------FIN DEL CÓDIGO------------------------------>

</ul>
	</div>
</div>

<div class="spiner-loader hide" id="spiner-loader">
    <div class="backgroundLS">
        <div class="contentLS">
            <div class="center-align">
                Este proceso puede demorar algunos segundos
            </div>
            <div class="inner">
                <div class="load-container load1">
                    <div class="loader">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('template/calendar_sidebar');?>
<div class="main-panel">
	<nav class="navbar navbar-transparent navbar-absolute">
		<div class="container-fluid">
			<div class="navbar-minimize">
				<button id="minimizeSidebar" class="btn btn-round btn-white btn-fill btn-just-icon">
					<i class="material-icons visible-on-sidebar-regular">more_vert</i>
					<i class="material-icons visible-on-sidebar-mini">view_list</i>
				</button>
			</div>
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="<?=base_url()?>#" class="navbar-brand hidden-md hidden-lg" style="color: #0e4377;font-weight: 800">
					<img src="<?=base_url()?>static/images/img.ico" class="img-responsive" width="15%">
				</a>
                <div class="divCalendar">
                    <a id="minimizeSidecalendar" class="navbar-brand openCalendar-mini" style="cursor:pointer;">
                        <i class="material-icons far fa-calendar-alt"></i>
                        <p class="hidden-lg hidden-md"></p>
                    </a>
                </div>
            </div>
			<div class="collapse navbar-collapse">
				<a class="navbar-brand" href="#"> Menú </a>
				<ul class="nav navbar-nav navbar-right">
                    <!----------------------------------------------------------------------->
                    <input type="hidden" id="uri2" value="<?=$url?>">
                    <input type="hidden" id="uri" value="<?=base_url()?>Usuarios/Chat">
                    <!------------------------------------------------------------------------->
                    <!-- Abrir side-calendar -->
					<li class="openCalendar">
						<a id="minimizeSidecalendar"  style="cursor:pointer;">
                            <i class="material-icons far fa-calendar-alt"></i>
						</a>
                    </li>
                    <?php
                    if($this->session->userdata('id_rol') == 7 && $this->session->userdata('asesor_guardia'))
                    {
                    ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="numberMsgAllSys">
                                <i class="material-icons">chat</i>
                                <p class="hidden-lg hidden-md">
                                    chat
                                    <b class="caret"></b>
                                </p>
                            </a>
                            <ul class="dropdown-menu" id="cpoNtallSys" style="height: 450px;overflow-y: auto;width: 340px;">
                            </ul>
                        </li>
                    <?php
                    }
                    ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span><?= $this->session->userdata('id_usuario') . " - " . $this->session->userdata('nombre')." ".$this->session->userdata('apellido_paterno')." ".$this->session->userdata('apellido_materno') ?></span>
							<i class="material-icons">person</i>
							<p class="hidden-lg hidden-md">Profile</p>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="<?=base_url()?>index.php/Usuarios/configureProfile">Configurar perfil</a>
							</li>
							<li>
								<a href="<?=base_url()?>index.php/login/logout_ci" class="session_close_btn_clean">Cerrar sesión</a>
							</li>
						</ul>
					</li>
					<li class="separator hidden-lg hidden-md"></li>
				</ul>
			</div>
		</div>
    </nav>
<script>
    function AddTicket(){
          $.post("<?=base_url()?>index.php/Api/ServicePostTicket", function (data) {
            console.log(data);
          //  window.open(data);

          var newtab =  window.open('','Sistema de tickets', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=1000,height=400,left = 390,top = 50');
          newtab.document.write(data);  

    }, 'json');
        }
</script>