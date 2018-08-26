<script type="text/javascript">
	$( document ).ready( function(){
		$( "#searchBoard" ).on( 'submit', function( event ){			
			$.post( "<?= base_url( 'report/fetchBoardCards' ) ?>", { formData: $( this ).serializeArray() }, function( data ) {
				$( '#cardResult' ).html( data.cardsHtml );
			}, 'json');
			event.preventDefault();
			return false;
		});
	});
</script>