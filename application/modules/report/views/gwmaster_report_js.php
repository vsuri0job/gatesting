<!-- <script src="<?=base_url('assets/plugins/sweetalert/sweetalert.min.js')?>"></script> -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ) ?>"></script>
<!-- start - This is for export functionality only -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript">
	$( document ).ready( function(){	    
        $("#webmaster_sites").select2();
		$('#month-tbl').DataTable({
            "order": [],
            "paging": false
        }); 
		$('#queries-tbl').DataTable({
            "order": []
        });
		$('#pages-tbl').DataTable({
            "order": [],
			"columns": [
			    { "width": "40%" },
			    null,
			    null,
			    null
		  	]
        });
	});
</script>