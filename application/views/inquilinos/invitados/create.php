<script src="<?=base_url();?>access/js/function.js"></script>
<style type="text/css">
    .art{
        display: none;
    }
    .margin{
        margin-right: 8px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-10">
            <h2 class="mc-page-header">Agregar invitado <small><?=$espacio->nombre?> - <?=date("d/m/Y",strtotime($espacio->dia))?> <?=date("H:i",strtotime($espacio->desde))?> <?=date("H:i",strtotime($espacio->hasta))?></small> </h2>
        </div>
        <div class="col-xs-12 col-sm-2">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>inquilinos/mis_reservas"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">

    <div class="row">    
        <div class="col-xs-12">
            <form method="POST" name="edificios_form" id="infitados_form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="nombre_id"><?=ucfirst('Nombre y apellido');?></label>
                            <input type="text" class="form-control" id="nombre" placeholder="Juan perez" name="nombre">
                        </div>
                    </div>          

                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="dni"><?=ucfirst('Documento');?></label>
                            <input type="text" class="form-control" id="dni" placeholder="245846484" name="dni">
                        </div>
                    </div>      

                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="email"><?=ucfirst('email');?></label>
                            <input type="email" class="form-control" id="email" placeholder="juan@perez.com" name="email" >
                        </div>
                    </div>                         

                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <label for="patente"><?=ucfirst('vehículo, color y patente');?></label>
                            <input type="text" class="form-control" id="patente" placeholder="Ford fiesta rojo AGV 345" name="patente">
                            <input type="hidden" name="reserva_id" id="reserva_id" value="<?=$espacio->reserva_id?>">
                            <input type="hidden" name="invitado_id" value="0" id="invitado_id">
                        </div>
                    </div>
                    <div class="art">
                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">
                                <label for="Empresa"><?=ucfirst('Empresa');?></label>
                                <input type="text" class="form-control" id="company" placeholder="Empresa" name="company">
                            </div>
                        </div>  

                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">
                                <label for="phone"><?=ucfirst('Telefono');?></label>
                                <input type="text" class="form-control" id="phone" placeholder="45555-5555" name="phone">
                            </div>
                        </div>                          
   
                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">
                                <label for="trabajo"><?=ucfirst('trabajo a realizar');?></label>
                                <input type="text" class="form-control" id="trabajo" 
                                name="trabajo">
                            </div>                        
                        </div>        

                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">
                                <label for="art"><?=ucfirst('Seguro');?></label>
                                <input type="file" class="form-control" id="art" 
                                name="art">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-onset-9 col-md-3">
                       
                       
                            <div class="form-group">
                                <label for="patente"><?=ucfirst('Seleccione Ingreso');?></label>
                                <select class="form-control" name="tipo" id="tipo" >
                                    <option value="1">Invitado</option>
                                    <option value="2">Trabajador</option>
                                </select>
                            </div>
                       

                    </div>



                </div>
                <button type="submit" class="btn btn-mk-primary"><i class="fa fa-plus"></i> Guardar</button>
            </form>
        </div>
        <div class="col-xs-12">
            <div class="table-responsive">
                <h4>Trabajadores</h4>
                <table class="table mc-read-table">
                    <thead id="table_trabajador">
                        <tr>

                            <th rel="nombre">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Nombre');?></a>
                            </th>     
                            <th rel="company">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Empresa');?></a>
                            </th>  
                            <th rel="dni">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('DNI');?></a>
                            </th>                         
                            <th rel="email">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('email');?></a>
                            </th>       
                            <th rel="phone">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Telefono');?></a>
                            </th>                                                    
                            <th rel="patente">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('patente');?></a>
                            </th>                             
                            <th rel="trabajo">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('trabajo');?></a>
                            </th>                                                          
                            <th rel="acction">Acciones</th>                                              
                        </tr>
                    </thead>
                    <tbody id="content_trabajador">
                    </tbody>
                </table> 
                <h4>Invitados</h4>
                <table class="table mc-read-table">
                    <thead  id="table_events">
                        <tr>

                            <th rel="nombre">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Nombre');?></a>
                            </th>                               
                            <th rel="dni">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('DNI');?></a>
                            </th>                         
                            <th rel="email">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('email');?></a>
                            </th>  
                            <th rel="phone">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Telefono');?></a>
                            </th>                                 
                            <th rel="patente">
                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('patente');?></a>
                            </th>                              
                            <th rel="acction">Acciones</th>                                                             
                        </tr>
                    </thead>
                        <tbody id="content_events">
                        </tbody>
                </table>
            </div>   
        </div>
    </div>
</div>
<script type="text/javascript">
    
    $(document).ready(function(){
        $("#tipo").change(function(){
            var tipe = $(this).val();
            if(tipe == 2){
                $(".art").show();
            }else{
                $(".art").hide();
            }
        });

        $(document).on('click','.delete',function(e){
           
            e.preventDefault();
            var id = $(this).data('primary-key');
            var url = $(this).attr('href');
            var text = 'Desea eliminar un Invitado?';
            var text_success = 'Eliminado!';
            
            Swal.fire({
                title: text,
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3498DB',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar'

            }).then((result) => {
                if (result.value) {
                    Swal.fire(
                        text_success,
                        '',
                        'success'
                    )
                    $.get(url,function(){
                       invitados();
                       trabajadores();
                    });
                }
            })

        });

        $(document).on('click',".edit",function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $.post(base_url+'/inquilinos/get_invitado',{id:id},function(data){
                invitado = JSON.parse(data);
                $(".art").hide();
               
                if(invitado.tipo_invitado_id == 2){
                    $(".art").show();
                    $("#trabajo").val(invitado.trabajo);
                    $("#company").val(invitado.company);
                    $("#phone").val(invitado.phone);
                    $('#tipo option[value="'+ invitado.tipo_invitado_id +'"]').attr("selected", "selected");
                }

                $("#nombre").val(invitado.nombre);
                $("#dni").val(invitado.dni);
                $("#email").val(invitado.email);
                $("#patente").val(invitado.patente);
                $("#invitado_id").val(invitado.id);

            });
        });

        $("body").on("submit","#infitados_form",function(e){
            e.preventDefault();
            var formData = new FormData(document.getElementById("infitados_form"));
            $.ajax({
                url:  base_url+"inquilinos/add_invitados/",
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(data){
                document.getElementById("infitados_form").reset();
                $('#infitados_form')[0].reset();
                invitados();
                trabajadores();
            });
        });      

        invitados();
        trabajadores();

    });


    function invitados(){
        result =20;
        var invitados = {};
        var tipo = 1;
        var reserva_id = <?=$espacio->reserva_id?>;

        $.post(base_url+"inquilinos/list_invitados",
            {reserva_id:reserva_id,tipo:tipo},
            function(data){
                invitados = JSON.parse(data);
                acction = [{ 
                    "button":' Editar',
                    "dataid":'id',
                    "target":'_self',
                    "title":'Editar Reservas',
                    "class":'margin fa fa-pencil btn btn-success btn-xs edit',
                    "parameter":''
                },{
                    "link":base_url+'inquilinos/delete_invitados/',  
                    "button":' Eliminar',
                    "dataid":'id',
                    "target":'_self',
                    "title":'Eliminar Invitado',
                    "class":'fa fa-times btn btn-danger btn-xs delete',
                    "parameter":'id'
                }];

                contenedor = $("#content_events");
                table = $("#table_events");

                json = invitados;
                json_active = invitados;
                load_content(json);
                selectedRow(json);
        });
    }      

    function trabajadores(){
        result =20;
        var invitados = {};
        var tipo = 2;
        var reserva_id = <?=$espacio->reserva_id?>;

        $.post(base_url+"inquilinos/list_invitados",
            {reserva_id:reserva_id,tipo:tipo},
            function(data){
                invitados = JSON.parse(data);

                acction = [{  
                    "button":' Editar',
                    "dataid":'id',
                    "target":'_self',
                    "title":'Editar Reservas',
                    "class":'margin fa fa-pencil btn btn-success btn-xs edit',
                    "parameter":''
                },{
                    "button":' Descargar',
                    "link":base_url+'upload/art/',
                    "dataid":'id',
                    "target":'_blank',
                    "title":'Descargar Archivo ART',
                    "class":'margin fa fa-download btn btn-info btn-xs',
                    "parameter":'art'
                },{
                    "button":' Eliminar',
                    "link":base_url+'inquilinos/delete_invitados/',  
                    "dataid":'id',
                    "target":'_self',
                    "title":'Eliminar Invitado',
                    "class":'fa fa-times btn btn-danger btn-xs delete',
                    "parameter":'id'
                }];

                contenedor = $("#content_trabajador");
                table = $("#table_trabajador");
                
                json = invitados;
                json_active = invitados;
                load_content(json);
                selectedRow(json);
        });
    }    
</script>