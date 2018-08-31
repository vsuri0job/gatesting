<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ) ?>"></script>
<!-- start - This is for export functionality only -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script type="text/javascript">
	$( document ).ready( function(){		
		$('#total-traffic').DataTable({
            "order": []
        }); 
		$('#organic-traffic').DataTable({
            "order": []
        });
		$('#medium-traffic').DataTable({
            "order": []
        });
		$('#source-traffic').DataTable({
            "order": []
        });
		$('#landing-traffic').DataTable({
            "order": []
        });
	});
</script>