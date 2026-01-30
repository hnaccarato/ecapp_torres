
<div class="row text-center">
    <?php foreach($registers->result() as $register){ ?>
    <div class="col-md-3 expensa">
        <div class="pricing-item">

            <!-- Price class -->
            <div class="pricing-price pb-1 text-primary color-primary-text ">
                <h1 style="font-weight: 1000; font-size: 1.5em;">
                    <?php setlocale(LC_ALL,"es_ES");?>
                        <?=strftime("%B %Y",strtotime($register->fecha));?>
                </h1>
            </div>
            <!-- Perks of said subscription -->
            <div class="pricing-description">
                <ul class="list-unstyled mt-3 mb-4">
                    <li class="pl-3 pr-3"><?=$register->titulo;?></li>
                </ul>
                <br>
                <br>
            </div>
            <!-- Button -->
            <div class="pricing-button pb-4">
            <a class="btn btn-primary" href="<?=base_url();?>propietarios/expensas_view/<?=$register->id;?>" role="button"><i class="fa fa-eye" aria-hidden="true" title="Ver Expensas"></i> Ver Expensas</a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

    
