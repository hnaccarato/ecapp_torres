
<form action="<?=base_url('propietarios/nuevos_pagos')?>" method="post" 
enctype="multipart/form-data">    
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Realizar Pagos</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">
                       <small>Expensas Pendientes: </small> 
                    </label>
                    <select multiple="multiple"  id="recibos" name="recibos[]" required="required">
                        <?php foreach($recibos->result() as $value){?>
                            <option value="<?=$value->id?>"><?=$value->titulo?></option>
                        <?php } ?>
                    </select>
                </div> 
                <div class="form-group">
                    <label for="exampleInputFile">
                        Comprobante<br/> 
                        <small>Archivos Permitidos: jpg, jpeg, gif, png,pdf, doc, docx.</small>
                    </label>
                    <input type="file" id="exampleInputFile" name="comprobante" 
                    accept="image/*,.pdf,.docx,.doc">
                    <p class="help-block">Adjuntar comprobante</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="exampleInputEmail1">Importe</label>
                    <input type="number" name="importe" class="form-control" required="required">
                </div>    
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>

<script type="text/javascript">
    
    $(document).ready(function(){
        $('#recibos').multiSelect();
    })

</script>