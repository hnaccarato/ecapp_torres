<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Empresas</h3>
            	<div class="box-tools">
                    <a href="<?php echo site_url('admin/empresa__create'); ?>" class="btn btn-success btn-sm">Add</a> 
                </div>
            </div>
            <div class="box-body">
                           
                 <table id="secciones" class="table table-striped table-bordered" style="width:100%" >
                    <thead>
                        <tr>
                           <th>N°</th>
                           <th>Nombre</th>
                           <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>    
                 </table>      
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

var table;

$(document).ready(function() {
    table = $('#secciones').DataTable({ 
 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url('admin/empresas_read/')?>",
            "type": "POST"
        },
 
        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
           {
               extend: 'pdfHtml5',
               orientation: 'landscape',
               pageSize: 'LEGAL'
           }
        ]
 
    });
});


</script>
