<!--   Core JS Files   -->
<script src="<?=base_url()?>dist/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>dist/js/popper.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>dist/js/material.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>dist/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<!--<script src="--><?//=base_url()?><!--dist/bower_components/select2/dist/js/select2.full.min.js"></script>-->
<!-- Forms Validations Plugin -->
<script src="<?=base_url()?>dist/js/jquery.validate.min.js"></script>
<!--  Plugin for Date Time Picker and Full Calendar Plugin-->
<script src="<?=base_url()?>dist/js/moment.min.js"></script>
<!--  Charts Plugin -->
<script src="<?=base_url()?>dist/js/chartist.min.js"></script>
<!--  Plugin for the Wizard -->
<script src="<?=base_url()?>dist/js/jquery.bootstrap-wizard.js"></script>
<!--  Notifications Plugin    -->
<script src="<?=base_url()?>dist/js/bootstrap-notify.js"></script>
<!--   Sharrre Library    -->
<script src="<?=base_url()?>dist/js/jquery.sharrre.js"></script>
<!-- DateTimePicker Plugin -->
<script src="<?=base_url()?>dist/js/bootstrap-datetimepicker.js"></script>
<!-- Vector Map plugin -->
<script src="<?=base_url()?>dist/js/jquery-jvectormap.js"></script>
<!-- Sliders Plugin -->
<script src="<?=base_url()?>dist/js/nouislider.min.js"></script>
<!-- Select Plugin -->
<script src="<?=base_url()?>dist/js/jquery.select-bootstrap.js"></script>
<!--  DataTables.net Plugin    -->
<script src="<?=base_url()?>dist/js/jquery.datatables.js"></script>
<!-- Sweet Alert 2 plugin -->
<script src="<?=base_url()?>dist/js/sweetalert2.js"></script>

<script src="<?=base_url()?>dist/js/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin    -->
<script src="<?=base_url()?>dist/js/fullcalendar.min.js"></script>
<!-- TagsInput Plugin -->
<script src="<?=base_url()?>dist/js/jquery.tagsinput.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="<?=base_url()?>dist/js/material-dashboard2.js"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="<?=base_url()?>dist/js/demo.js"></script>

<script src="<?=base_url()?>dist/js/alerts.js"></script>

<script src="<?=base_url()?>dist/js/funciones-generales.js"></script>

<script src="<?=base_url()?>dist/js/controllers/select2/select2.full.min.js"></script>

<script type="text/javascript">
    var url2 = "<?=base_url()?>index.php/";
	$(document).ready(function() {
        

		// Javascript method's body can be found in assets/js/demos.js
		demo.initDashboardPageCharts();

		demo.initVectorMap();
		<?php
        /*switch ($this->session->userdata('id_rol')) {
            case 1:
                if ($this->session->userdata('no_show_modal_info')==0) {
                    echo '$("#avisoNovedades").modal("toggle");';
                }
            break;
            case 2:
                if ($this->session->userdata('no_show_modal_info')==0) {
                    echo '$("#avisoNovedades").modal("toggle");';
                }
            break;
            case 3:
                if ($this->session->userdata('no_show_modal_info')==0) {
                    echo '$("#avisoNovedades").modal("toggle");';
                }
            case 4:
                if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
                }
            case 5:
                if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
                }
            case 6:
                if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
                }
            case 7:
                if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
                }
            case 9:
                if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
                }
            break;
            default:
            echo "i no es igual a 0, 1 ni 2";
        }*/
        /**/if($this->session->userdata('id_rol') == 7 || $this->session->userdata('id_rol') == 1
            || $this->session->userdata('id_rol') == 2 || $this->session->userdata('id_rol') == 3
            || $this->session->userdata('id_rol') == 4 || $this->session->userdata('id_rol') == 5
            || $this->session->userdata('id_rol') == 6 || $this->session->userdata('id_rol') == 9)
        {
            if ($this->session->userdata('no_show_modal_info')==0) {
                echo '$("#avisoNovedades").modal("toggle");';
            }
        }
         ?>
	});


	/**/function validaCheckSession()
    {
        if($('#no_mostrar_session:checkbox:checked').length > 0)
        {
            $.post('<?=base_url()?>index.php/Login/noShowModalSession',  function(data) {
                console.log(data);
            });
            <?php echo "console.log(".$this->session->userdata('no_show_modal_info').");";?>
            console.log('No mostrar');
        }
    }

   
    // $.post('<?=base_url()?>index.php/Chat/checkIfIsGuarrior/<?php echo $this->session->userdata("id_usuario")?>',  function(data) {
    //             console.log(data);
    // });
    var id_rol_global = <?=$this->session->userdata('id_rol')?>; 

</script>

<?php if($this->session->userdata('id_rol') == 7 && $this->session->userdata('asesor_guardia')==1){?>


<script src="<?=base_url()?>dist/js/socket.io.js"></script>
<script>
    //consultas al servidor con ajax
    // let urlimg = "<?=base_url()?>";
    let perfil;
    let id;
    $.ajax({
        type: 'POST',
        url: '<?=base_url()?>index.php/Chat/getInfoPerfil',
        dataType: 'json',
        beforeSend: function(){
            // Actions before send post
        },
        success: function(data) {
            perfil = data;
            $('#paraimg').val('<?=base_url()?>static/images/perfil/'+perfil[0].id_usuario+'/'+perfil[0].foto);
        },
        async: false
    })
    var mySound = new Audio('../static/tono-mensaje.mp3');
    // mySound.load();

    let im = '<?=base_url()?>static/images/perfil/'+perfil[0].id_usuario+'/'+perfil[0].foto;
    console.log(im);
    if ($(window).width() < 996){
        console.log("chico");
    }
    else {
        var socket = io('https://chatcomercial.gphsis.com/', {query:{
            "user":1,
            "maxChats": perfil[0].num_chat,
            "status": perfil[0].estatus,
            "sede" : perfil[0].id_sede,
            "customID": perfil[0].id_usuario,
            "nombre": perfil[0].nombre,
            "msgb":perfil[0].mensaje,
            "img":im,
            "emisor":1
        }
        });
        // console.log(socket);
        var i=0;
        function sendNotify(data, numeroNot)
        {
            // console.log(data);
            $('#cpoNtallSys').empty();

            if(data.length >0)
            {
                if(data[data.length-1]['emisor'] == "0")
                {
                    $('#numberMsgAllSys').append('<span class="notification">'+numeroNot+'</span>');
                    window.document.title = "MaderasCRM | Ciudad Maderas Nuevo mensaje("+numeroNot+")";
                    var  w=0;
                    for(var t=(data.length-1); t>=0; t--)
                    {
                        if(data[t]['emisor'] == "0")
                        {
                            if(w <= 10 )
                            {
                                $('#cpoNtallSys').append('<li style="border-bottom: 1px solid #ddd;"><a href="<?=base_url()?>Chat/Chat#'+data[t]['id']+'" style="white-space: pre-wrap;"><span style="font-size:0.8em">lead: '+data[t]['id']+' dice</span>: <i>'+data[t]['msg']+'</i><br><small>'+data[t]['time']+'</small></a></li>');
                            }
                            w++;
                        }
                    }
                    $('#cpoNtallSys').append('<div style="background:black;color:white;text-align:center;position:sticky;height:40px;width:100%;bottom:2%; padding: 10px 0px;"><a style="color:white" href="<?=base_url()?>Chat/Chat">IR A MENSAJES</i></a></div>');
                }
                else if(data[data.length-1]['emisor'] == "1")
                {
                    alerts.showNotification("top", "right", "¡Nuevo cliente en chat!", "success");
                    mySound.play();
                    $('#cpoNtallSys').append('<li style="background:black;color:white;text-align:center"><a style="color:white" href="<?=base_url()?>Chat/Chat">NUEVO CHAT '+data[(data.length-1)]["id"]+'</i></a></li>');
                    console.log(data);
                    var  w=0;
                    for(var t=(data.length-1); t>=0; t--)
                    {
                        if(data[t]['emisor'] == "0")
                        {
                            if(w <= 10 )
                            {
                                $('#cpoNtallSys').append('<li style="border-bottom: 1px solid #ddd;"><a style="white-space: pre-wrap;" href="<?=base_url()?>Chat/Chat#'+data[t]['id']+'"><span style="font-size:0.8em">lead: '+data[t]['id']+' dice</span>: <i>'+data[t]['msg']+'</i><br><small>'+data[t]['time']+'</small></a></li>');
                            }
                            w++;
                        }
                    }
                    $('#cpoNtallSys').append('<div style="background:black;color:white;text-align:center;position:sticky;height:40px;width:100%;bottom:2%; padding: 10px 0px;"><a style="color:white" href="<?=base_url()?>Chat/Chat">IR A MENSAJES</i></a></div>');
                }
            }

            
            
        }

        socket.on('chat message', function(data) {
            // console.log(data);
            var sessionNotificacion;
            if (localStorage.getItem("dataNotificaciones") === null) {
                console.log('no, no existe');
                localStorage.setItem("dataNotificaciones", "[]");
                localStorage.setItem("contadorNotificaciones", 1);
                sessionNotificacion = localStorage.getItem("dataNotificaciones");
                sessionNotificacion =  JSON.parse(sessionNotificacion);
                sessionNotificacion.push(JSON.stringify(data));
                localStorage.setItem("dataNotificaciones", "[" + sessionNotificacion + "]");
            }
            else
            {
                var arrayManejo=[];
                var numeroNotificaciones;
                // console.log('si, ya existia y se le agrega el nuevo valor xd');
                arrayManejo = JSON.parse(localStorage.getItem('dataNotificaciones')) || [];
                numeroNotificaciones = JSON.parse(localStorage.getItem('contadorNotificaciones'));
                if(data.emisor==0)
                {
                    numeroNotificaciones = (numeroNotificaciones+1);
                }
                arrayManejo.push(data);

                localStorage.setItem("dataNotificaciones", JSON.stringify(arrayManejo));
                localStorage.setItem("contadorNotificaciones", numeroNotificaciones);
            }




            setTimeout(function () {
               var sessionNotify = localStorage.getItem("dataNotificaciones");
               var sessionContador = localStorage.getItem("contadorNotificaciones");
                sessionNotify = JSON.parse(sessionNotify);
                // console.log(sessionNotify);
                // console.log(sessionContador);

                sendNotify(sessionNotify, sessionContador);
            }, 500);
        });

    }




    function urlify(text) {
        var urlRegex = /(((https?:\/\/)|(www\.))[^\s]+)/g;
        //var urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, function(url,b,c) {
            var url2 = (c == 'www.') ?  'http://' +url : url;
            return '<a class="link_chat" href="' +url2+ '" target="_blank">' + url + '</a>';
        })
    }
    $(document).ready(function () {
        //cleanRepMsg();

        let currentNot = (localStorage.getItem("contadorNotificaciones") === null) ? 0 : localStorage.getItem("contadorNotificaciones");<?php #echo #$this->session->userdata('mensajes_count');?>;
        var totalData = JSON.parse((localStorage.getItem("dataNotificaciones") === null) ? 0 : localStorage.getItem("dataNotificaciones")); <?php #print_r(json_encode($this->session->userdata('msg_data')))?>;

            // console.log(totalData);

            if(totalData.length >0)
            {
                // console.log('llegue hasta qui alv');
                // console.log(totalData[0]);
                // if(totalData[totalData.length-1]['emisor'] == "0")
                // {
                    if(currentNot > 0)
                    {
                        $('#numberMsgAllSys').append('<span class="notification">'+currentNot+'</span>');
                        window.document.title += " ("+currentNot+" mensaje(s) sin leer)";
                    }

                    var  w=1;
                    for(var t=(totalData.length-1); t>=0; t--)
                    {

                        if(totalData[t]['emisor'] == "0")
                        {
                            if(w <= 10 )
                            {
                                $('#cpoNtallSys').append('<li style="border-bottom: 1px solid #ddd;"><a style="white-space: pre-wrap;" href="<?=base_url()?>Chat/Chat#'+totalData[t]['id']+'"><span style="font-size:0.8em">lead: '+totalData[t]['id']+' dice</span>: <i>'+totalData[t]['msg']+'</i><br><small>'+totalData[t]['time']+'</small></a></li>');
                            }
                            w++;
                        }

                    }
                    $('#cpoNtallSys').append('<div style="background:black;color:white;text-align:center;position:sticky;height:40px;width:100%;bottom:2%; padding: 10px 0px;"><a style="color:white" href="<?=base_url()?>Chat/Chat">IR A MENSAJES</i></a></div>');
                // }
            }
            else
            {
                $('#cpoNtallSys').append('<li style="background:black;color:white;text-align:center"><a style="color:white" href="#">NO HAY MENSAJES</i></a></li>');
            }

    });

    $(document).on('click', '#numberMsgAllSys', function () {
      /*
        let currentNot2 = <?php echo $this->session->userdata('mensajes_count');?>;
        var totalData2 = <?php print_r(json_encode($this->session->userdata('msg_data')))?>;
    */
        /*******************************/
        $('#numberMsgAllSys').html('<i class="material-icons">chat</i>');
        // $('#cpoNtallSys').html('');
        window.document.title = 'MaderasCRM | Ciudad Maderas';

        cleanVarNt();
    });

    function cleanVarNt()
    {
        localStorage.removeItem('contadorNotificaciones');
    }

    function cleanRepMsg()
    {
        $.post( "<?=base_url()?>index.php/Chat/cleanRepMsg/", function( data ) {
            console.log(data);
        }, 'json');
    }

</script>
<script>
    $(document).on('click', '#cerrar_sesionsb21', function () {
        localStorage.clear();
    });

    $(document).on('click', '.session_close_btn_clean', function(){
        localStorage.clear();
    });

</script>
<?php }?>
