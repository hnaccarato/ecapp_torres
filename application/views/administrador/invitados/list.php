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
                    <a type="button" href="<?=base_url();?>propietarios/mis_reservas"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">

    <div class="row">    
        
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

       
        invitados();
        trabajadores();

    });


    function invitados(){
        result =20;
        var invitados = {};
        var tipo = 1;
        var reserva_id = <?=$espacio->reserva_id?>;

        $.post(base_url+"administrador/list_invitados",
            {reserva_id:reserva_id,tipo:tipo},
            function(data){
                invitados = JSON.parse(data);
                acction = [{}];
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

        $.post(base_url+"administrador/list_invitados",
            {reserva_id:reserva_id,tipo:tipo},
            function(data){
                invitados = JSON.parse(data);
                acction = [{}];
                contenedor = $("#content_trabajador");
                table = $("#table_trabajador");
                
                json = invitados;
                json_active = invitados;
                load_content(json);
                selectedRow(json);
        });
    }    
</script>