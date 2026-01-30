<style type="text/css">
    .error{
        color: red;
    }
    .ms-container{
        width: 100% !important;
    }
</style>
<div class="col-xs-12">
    <div class="row">
        <div class="col-xs-12"><h3 for="imagen_id" ><?=ucfirst('Nuevo Pago');?></h3></div>
    </div>
    <hr class="hr_subrayado">
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?=base_url('inquilinos/pagar')?>" method="post" id="my_form" >
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-7">
                    <div class="form-group">
                        <label for="exampleInputEmail1">
                            <small>Expensas Pendientes: </small>
                        </label>
                         <span class="error"><?php echo form_error('recibos[]'); ?></span>
                        <select multiple="multiple"  id="recibos" name="recibos[]" widht="100%">
                            <?php foreach($recibos->result() as $value){?>
                            <option value="<?=$value->id?>" <?=($value->id == set_value('recibos[]'))? "selected":" "?>><?=$value->titulo?></option>
                            <?php } ?>
                        </select>
                    </div> 
                    <ul class="media-list" style="margin-top: 50px;">
                      <li class="media">
                        <div class="media-left">
                          <a href="#">
                            <img class="media-object" src="<?=base_url('access/img/MercadoPago.png')?>" alt="Mercado pago" width="180" >
                          </a>
                        </div>
                        <div class="media-body">
                          <p class="media-heading">
                              El pago online se encuentra sujeto a gastos administrativos.
                          </p>
                        </div>
                      </li>
                    </ul>
                </div>

                <div class="col-sm-12 col-md-5" >
                    <div class="form-group" id="input_importe">
                    <span class="error"><?php echo form_error('recibos[]'); ?></span>
                    </div> 
                
                    <input type="hidden" id="porcentaje" value="<?=$porcentaje?>"> 
                    <hr class="suma" style="border:solid;">
                    <h3>Total a Pagar : $<strong id="total"></strong></h3> 
                </div>  
            </div>
            <hr>
            <button type="submit" class="btn btn-primary pull-right">Aceptar</button>
        </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#recibos').multiSelect(
            {
              afterSelect: function(values){
                 $.post(base_url+'inquilinos/load_liquidacion',{expensa_id:values},function(data){
                     var liquidaciones = JSON.parse(data);
                     $.each(liquidaciones, function(i, value) {
                         var imput = "<div id=\""+value.id+"\"><label for=\"exampleInputEmail1\">"+
                         "<a  href=\""+base_url+"upload/expensas/"+value.file+"\" target=\"_blank\" id=\"expensa_file\">"+
                         "<i class=\"glyphicon glyphicon-save-file\"></i> "+value.titulo+"</a></label>"+
                         "<input type=\"number\" name=\"importe[]\" step= \"any\""+ 
                         "class=\"form-control importe\" required=\"required\" placeholder=\"Importe\"></div>";
                         
                         $("#input_importe").append(imput);        
                     });
                }) 
              },
              afterDeselect: function(values){
              //  load_liquidacion();  
                $("#"+values).remove();
              }
            }
        );
       // $("#input_importe").hide();
        $(document).on('change', '.importe', function(){
            calcular();
        });


    });

    function load_liquidacion(){
         var expensa_id = [];
        $('#recibos option:selected').each(function(){ 
               expensa_id.push($(this).val());
            }
        ); 

       $.post(base_url+'inquilinos/load_liquidacion',{expensa_id:expensa_id},function(data){
            var liquidaciones = JSON.parse(data);
            $("#load_liquidacion").html(' ');
            $.each(liquidaciones, function(i, value) {
                var list_liq = "<p><a  href=\""+base_url+"upload/expensas/"+value.file+"\" target=\"_blank\" id=\"expensa_file\"><i class=\"glyphicon glyphicon-save-file\"></i>"+value.titulo+"</a></p>";
                $("#load_liquidacion").append(list_liq);
            });
       }) 
    }

    function calcular(){
        var total = 0;
        
        $(".importe").each(function() {
       
            if (isNaN(parseFloat($(this).val()))) {
                total += 0;
            } else {
                total += parseFloat($(this).val());
            }

        });
        var porcentaje =  $("#porcentaje").val();
        $.post(base_url+'inquilinos/porcentaje/'+total+'/'+porcentaje , function(data){
            $("#total").html(data);  
        });
        
    }

</script>
