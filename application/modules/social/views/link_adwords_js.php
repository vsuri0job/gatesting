<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<link href="<?= base_url( 'assets/plugins/bootstrap-select/bootstrap-select.min.css' ) ?>" rel="stylesheet" />
<script type="text/javascript">
    $("#adwordProject").select2();
    $("#getGoogleData").on( 'submit', function( event ){
    	// $("#adwordProject").attr('disabled', true);
    	$("#update_adwords").attr('disabled', true);
    });
</script>