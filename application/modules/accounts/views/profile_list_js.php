<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ) ?>"></script>
<!-- start - This is for export functionality only -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script src="https://api.trello.com/1/client.js?key=<?=TRELLO_DEV_KEY?>"></script>
<script type="text/javascript">
	$( document ).ready( function(){

		$('.google-ad-link').on( 'click', function( event ){
			let cus_id = $( this ).data( 'cid' );
			$( "#customer_id" ).val( "" ).attr( 'readonly', false );
			if( cus_id ){
				$( "#customer_id" ).val( cus_id ).attr( 'readonly', true );
			}
			$("#adword-cus-id").attr( 'action', $( this ).attr( 'href' ) );
			$("#link-account-adword").modal();
			event.preventDefault();
		});

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

		$( '.trelloAuth' ).on( 'click', function( event ){
			let profileId = $( this ).data( 'id' );
			let verifyUrl = "<?= base_url('social/verify_trello?1=1')?>" + "&pid="+profileId			
			Trello.authorize({
				type: 'popup',
				name: 'Trello Card Updates',
				expiration: 'never',
				success: function(){
					let trelloToken = localStorage.getItem('trello_token');
			      	localStorage.removeItem('trello_token');
					window.location.href=verifyUrl+"&token="+trelloToken;
				}
			});
			event.preventDefault();
			return false;
		} );

		$('.rankAuth').on( 'click', function( event ){			
			$("#rank-cus-id").attr( 'action', $( this ).attr( 'href' ) );
			$("#link-account-rankinity").modal();
			event.preventDefault();
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
	});
</script>
<script type="text/javascript">
	<?php if( $emsg ){ ?>
		swal("Issue", "<?= $emsg; ?>", 'warning');
	<?php } ?>
</script>