<script src="<?=base_url();?>access/js/function.js"></script>
<style type="text/css">

    .siguiente, .previo{
        text-align: center;
    }
    .btn-group{
        margin-bottom:20px;
    }
    .rechazado{
        color: red;
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
                    <a type="button" href="<?=base_url();?>propietarios/espacios_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<hr class="hr_subrayado">
<div class="row"> 
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?=$values->nombre_espacio?></div>
            <div class="panel-body">
                <div class="col-xs-12" style="margin: 6px -32px 6px 27px;">
                    <div class="media precentacion">
                        <div class="media-left media-middle">
                            <a href="#">
                                <?php if(!empty($values->foto_espacio)){ ?>
                                    <img class="media-object" src="<?=base_url('/upload/espacios/'.$values->foto_espacio)?>" height="200px">
                                <?php } ?>
                            </a>
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
                            <hr class="hr_subrayado">
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
                                <span>De un total de: </span><span id="numRows"></span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mc-read-table">
                                <thead id="table_events">
                                    <tr>
                                        <th rel="acction"><?=$this->lang->line('table_acction');?></th>
                                        <th rel="estado">
                                            <a href="javascript:void(0)" class="asc" ><?=ucfirst('estado');?></a>
                                        </th>
                                        <th rel="date">
                                            <a href="javascript:void(0)" class="asc" ><?=ucfirst('dia');?></a>
                                        </th>                 
                                        <?php if($values->view_reservation):?>
                                            <th rel="unidad">
                                                <a href="javascript:void(0)" class="asc" ><?=ucfirst('unidad');?></a>
                                            </th>
                                        <?php endif;?>
                                        <th rel="desde">
                                            <a href="javascript:void(0)" class="asc" ><?=ucfirst('Desde');?></a>
                                        </th>                         
                                        <th rel="hasta">
                                            <a href="javascript:void(0)" class="asc" ><?=ucfirst('Hasta');?></a>
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

            $("body").on('click','.rechazado',function(e){ 

                e.preventDefault();
                var input = $(this);
                var url = "<?=base_url('propietarios/espacios_active')?>"; 
                var reserva_id = $(this).data('id');
                var text = "Desea rechazar la reserva ?";
                var text_success = "Reserva rechazada";

                Swal.fire({
                    title: text,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3498DB',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                   if (result.value) {
                        $.post(url,{reserva_id:reserva_id}, 
                           function(data){
                                
                                var data = JSON.parse(data);
                                var alert_icono = 'success';
                               
                                if(data.error.length > 0){
                                   text_success =  data.error;
                                   alert_icono = 'warning';
                                }

                                Swal.fire(
                                   text_success,
                                   '',
                                   alert_icono
                                )
                                
                                load();
                           }
                       );
                   }
                })
            });

            load();

        });


        function load(){
            result = 10;
            var reserva = {};
            var year = '<?=$year?>';
            var month = '<?=$month?>';
            var espacio_id = '<?=$values->id?>';
            var turno = '<?=$turno_id?>';
            control = '<?=$this->user->id?>';

            $.post(base_url+"propietarios/get_reservas",
                {year:year,
                    month:month,
                    espacio_id:espacio_id,
                    turno:turno
                },function(data){
                    reserva = JSON.parse(data);

                    acction = [ 
                    {  "button":'',
                    "dataid":'id',
                    "control":'user_id',
                    "target":'_self',
                    "title":'Rechazar reserva',
                    "class":'glyphicon glyphicon-trash rechazado',
                    "parameter":''}];
                    contenedor = $("#content_events");
                    table = $("#table_events");
                    json = reserva;
                    json_active = reserva;
                    load_content(json);
                    selectedRow(json);
                });
        }

    </script>