<script src="<?=base_url();?>js/function.js"></script>
<style type="text/css">

    .siguiente, .previo{
        text-align: center;
    }
    .btn-group{
        margin-bottom:20px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Reservas</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>encargado/espacios_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?=$values->nombre_espacio?></div>
                <div class="panel-body">
                    <div class="col-xs-12">
                        <div class="media precentacion">
                            <div class="media-left media-middle">
                                <?php if(!empty($values->foto_espacio)){ ?>
                                    <a href="#">
                                        <img class="media-object" src="<?=base_url('/upload/espacios/'.$values->foto_espacio)?>" height="200px">
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="media-body">
                                <?php if($values->asoc_from > 0 || $values->asoc_to > 0   ){ ?>
                                    <h5 class="media-heading" style="color: red">
                                        Este espacio cuenta con los siguientes días de Inhabilitación previos a su reserva para mantenimiento "<?=$values->asoc_from?>" y con los siguientes dias de inhabilitacion posteriores a su reserva "<?=$values->asoc_to?>" para mantenimiento.
                                        Recuerde que el espacio también puede requerir autorización de la administración.
                                    </h5>
                                    <br/>
                                <?php } ?> 
                                <h4 class="media-heading"><?=$values->descripcion?></h4>
                                <?php if(!empty($values->reglamento)){ ?>
                                    <a href="<?=base_url('/upload/espacios/'.$values->reglamento)?>" target="_blank" class="btn btn-primary">Descargar Reglamento</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <hr>        
                    <div class="col-xs-12">
                        <div class="btn-group btn-group-justified" role="group" aria-label="...">
                            <?php foreach ($turnos->result() as $turno){?>
                                <?php
                                if ($turno_id == 0) {
                                    $active = 'active';
                                    $turno_id = $turno->id;
                                }else{
                                    $active = '';
                                }
                                ?>
                                <div class="btn-group" role="group">
                                    <type type="button" class="btn btn-default turnos <?=$active?>" data-id="<?=$turno->id?>">
                                        <?=$turno->identificacion?>    
                                    </div>
                                <?php } ?>
                            </div>
                        </div>      
                        <div class="col-xs-12 col-md-4"><?=$calendario?></div>     
                        <div class="col-xs-12 col-md-8">
                            <p>Puede Buscar por fecha si no encuentra la reserva</p>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <label for="result">Buscar</label>
                                    <input type="text" name="buscador" id="buscador" class="form-control">
                                </div>          
                                <div class="col-md-6">
                                    <label for="result">Mostrar:</label>
                                    <select  id="result" name="result" class="form-control">
                                        <option value="">Seleccione</option>
                                    </select>
                                    <span>Total de Reservas: </span><span id="numRows"></span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table mc-read-table">
                                    <thead id="table_events">
                                        <tr>
                                            <th rel="acction"><?=$this->lang->line('table_acction');?></th>
                                            <th rel="date">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('dia');?></a>
                                            </th>     
                                            <th rel="desde">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Desde');?></a>
                                            </th>                         
                                            <th rel="hasta">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('Hasta');?></a>
                                            </th>                          
                                            <th rel="unidad">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('unidad');?></a>
                                            </th>                           
                                            <th rel="departamento">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('departamento');?></a>
                                            </th>                           
                                        </tr>
                                    </thead>
                                    <tbody id="content_events">
                                    </tbody> 
                                </table>
                            </div>     
                        </div>     
                        <input type="hidden" value="<?=$year?>" class="year" />
                        <input type="hidden" value="<?=$month?>" class="month" />
                        <input type="hidden" value="<?=$turno_id?>" name="turno_id" class="turno_id" id="turno_id" />
                        <input type="hidden" value="<?=$values->id?>" name="espacio_id" class="espacio_id" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default hidden"  id="myModal" >
        <div class="panel-body" id="midiv">A Basic Panel</div>
    </div>

    <script type="text/javascript">

        $(document).ready(function(){

            $("body").on('click','.activation',function(e){ 

                e.preventDefault();
                var input = $(this);
                var url = "<?=base_url('encargado/espacios_active')?>"; 
                var reserva_id = $(this).data('id');
                var estado_id = $(this).attr('rel');

                if($(this).hasClass('aprobado')){
                    var text = "Desea confirmar la reserva ?";
                }    

                if($(this).hasClass('rechazado')){
                    var text = "Desea rechazar la reserva ?";
                }

                $.confirm({
                    title: text,
                    content: '',
                    buttons: {
                        Si: function(){
                            $.post(url,{estado_id:estado_id,reserva_id:reserva_id}, 
                                function(){
                                    load();
                                });
                        },
                        No: function(){}
                    }
                });
            });

            load();

        });


        function load(){
            result =20;
            var reserva = {};
            var year = '<?=$year?>';
            var month = '<?=$month?>';
            var espacio_id = '<?=$values->id?>';
            var turno = '<?=$turno_id?>';

            $.post(base_url+"encargado/get_reservas",
                {year:year,
                    month:month,
                    espacio_id:espacio_id,
                    turno:turno
                },function(data){
                    reserva = JSON.parse(data);
                    acction = [];
                    contenedor = $("#content_events");
                    table = $("#table_events");
                    $('#buscador').val('');
                    json = reserva;
                    json_active = reserva;

                    load_content(json);
                    selectedRow(json);
                });
        }

    </script>