<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ) ?>"></script>
<!-- start - This is for export functionality only -->
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
	$( document ).ready( function(){
		$( '.linkAccounts' ).on( 'click', function(){
			let aId = $( this ).data( 'aid' );
			$.get( "accounts/getAccounts", { aid: aId }, function( data ) {
				$( '#link-account-modal-title').html( data.analyticProfileTitle );
				$( '#link-account-modal-body' ).html( data.account_html );
				$( '#link-account-modal').modal();
			}, 'json');
		});

		$("#link-analytic-profile").on( 'submit', function(){
			let analytic_id = $('input[name="analytic_id"]').val();
			let account_id = $('input[name="account"]:checked').val();
			if( account_id && analytic_id){
				$.post( 'accounts/linkAccount', { account_id: account_id, analytic_id: analytic_id }, function( data ){
					if( data.success ){
						location.reload();
					}
				}, 'json');
			}			
			return false;
		});		
		$('#accounts').DataTable({
  			"columns": [
    			{ "width": "20%" },
			    { "width": "15%" },
			    { "width": "25%" },
			    { "width": "10%" },
			    { "width": "10%" },
			    { "width": "15%" },
		]});
	} );
</script>