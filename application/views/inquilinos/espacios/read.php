<style type="text/css">
  .thumbnail>img {
      height: 150px !important;
  }  
  .thumbnail{
      height: 300px !important;
  }
</style>
<div class="row">
    <?php foreach($registers->result() as $register){ ?>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail">
              <?php if(is_file(BASEPATH.'../upload/espacios/'.$register->foto_espacio)){?>
                    <img src="<?=base_url('upload/espacios/'.$register->foto_espacio)?>" 
                    alt="fotos">
                <?php }else{ ?>                              
                    <img src="<?=base_url('access/images/espacio_defoult.jpg')?>" alt="fotos">
                <?php } ?>
              <div class="caption">
                  <h4><?=$register->nombre_espacio;?></h4>
                  <p><?=$register->init_hora;?> - <?=$register->fin_hora;?></p>
                  <p>
                      <a href="<?=base_url();?>inquilinos/espacios_load/<?=$register->id;?>" data-primary-key="<?=$register->id;?>" class="btn btn-primary btn-xs">Reservar 
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                      </a>
                  </p>
              </div>
            </div>
        </div>
    <?php } ?>
</div>