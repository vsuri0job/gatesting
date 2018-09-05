<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<link href="<?= base_url( 'assets/plugins/bootstrap-select/bootstrap-select.min.css' ) ?>" rel="stylesheet" />
<script type="text/javascript">
	$( document ).ready( function(){
		$("#board").select2();
		// $( "#searchBoard" ).on( 'submit', function( event ){			
		// 	$.post( "<?= base_url( 'report/fetchBoardCards' ) ?>", { formData: $( this ).serializeArray() }, function( data ) {
		// 		$( '#cardResult' ).html( data.cardsHtml );
		// 	}, 'json');
		// 	event.preventDefault();
		// 	return false;
		// });
	});
</script>