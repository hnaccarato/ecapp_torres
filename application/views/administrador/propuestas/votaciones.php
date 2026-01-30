<script src="<?=base_url();?>js/Chart.bundle.min.js"></script>
<style type="text/css">
    .data_result{
        margin-top: 3%;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-7">
            <h2 class="mc-page-header">Votaciones</h2>
        </div>
        <div class="col-xs-12 col-sm-5">
            <ul class="mc-page-actions">
                <li>
                    <a type="button" href="<?=base_url();?>administrador/propuestas_list"><i class="fa fa-arrow-circle-o-left"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <hr class="hr_subrayado">

    <div class="row">
        <div class="col-xs-12">

            <div class="row">
                <div class="col-xs-6">    
                    <label for="fecha_fin_id"><?=ucfirst('fecha_fin');?></label>
                    <p id="fecha_fin_id" ><?=$propuesta->fecha_fin;?></p>
                </div>
                <div class="col-xs-6">
                    <label for="fecha_fin_id"><?=ucfirst('titulo');?></label>
                    <p id="fecha_fin_id" ><?=$propuesta->titulo;?></p>
                </div>
                <div class="col-xs-12 col-sm-12">
                    <label for="fecha_fin_id"><?=ucfirst('descripcion');?></label>
                    <p id="fecha_fin_id" ><?=$propuesta->descripcion;?></p>
                </div>
            </div>

            <div class="row">
                <?php foreach ($files->result() as  $value) { ?>
                <div class="col-xs-12 col-md-3"> 

                    <p>Descargar archivo
                        <span class="opcion_file">
                            <a href="<?=base_url('/upload/propuesta/'.$value->file)?>" target="_blank" class="glyphicon glyphicon-save"></a>
                        </span>   
                    </p> 

                </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="panel panel-default">
                  <div class="panel-body">
                      <div class="row">
                          <div class="col-sm-12 col-md-6">Total de unidades : <strong><?=$unidades->num_rows()?> </strong>
                          </div>
                          <div class="col-sm-12 col-md-6">Total de votos : <strong><?=$votantes->votos?></strong></div>
                      </div>
                  </div>
                  <div class="col-md-8">    
                      <canvas id="pie_chart"></canvas>
                  </div>
                  <div class="col-md-4 data_result">
                      <?php foreach ($opciones->result() as $opcion) { ?>
                       <div class="col-sm-12">
                           <p>Votos Por <?=$opcion->titulo?>: <strong><?=$opcion->voto?></strong></p>
                       </div>
                      <?php } ?>
                  </div>
                </div>


            </div>
        </div>
    </div>
</div>  
<script type="text/javascript">
    $(document).ready(function(){

        
        var data_pie_chart = {
            labels: [],
            datasets: [
                {
                    data: [],
                    backgroundColor: [
                        "#FF6384",
                        "#36A2EB",
                        "#FFCE56",
                        "#C261FF",
                        "#4724FF",
                        "#BF4CB2",
                        "#FFC2B8",
                        "#AD1499",
                        "#59C9BA",
                        "#DEC978",
                        "#FFD926"
                    ]
                }]
        };
        
        /*data_pie_chart.labels.push("Total de unidades");
        data_pie_chart.datasets[0].data.push(<?=$unidades->num_rows()?>);*/

        <?php foreach ($opciones->result() as $opcion) { ?>
            data_pie_chart.labels.push("<?=$opcion->titulo?>");
            data_pie_chart.datasets[0].data.push(<?=$opcion->voto?>);
        <?php } ?>
        
        
        
        var pie_chart = new Chart($("#pie_chart"), {
            type: 'pie',
            data: data_pie_chart,
            options:{
                responsive: true,
                title: {
                    display: true,
                    text: 'Grafico de torta'
                },
                legend: {
                    onClick: function (e) {
                        e.stopPropagation();
                    }
                }
            }
        });
        
    });
</script>