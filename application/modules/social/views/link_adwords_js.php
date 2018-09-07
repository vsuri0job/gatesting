<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<link href="<?= base_url( 'assets/plugins/bootstrap-select/bootstrap-select.min.css' ) ?>" rel="stylesheet" />
<script type="text/javascript">
    var processCounter = "";
    function updateProcess() {
        let pro_txt = $('#progress-bar').html();
        pro_txt = parseInt( pro_txt.trim() );
        if( pro_txt < 92 ){
            pro_txt = pro_txt + 1;
            pro_txt = pro_txt+"%";
            $('#progress-bar')
                .css( { width: pro_txt } )
                .html( pro_txt );
        }
    }
    $("#adwordProject").select2();    
    $("#getGoogleData").on( 'submit', function( event ){
        
        $("#adwordProject").val();
    	// $("#adwordProject").attr('disabled', true);
        $("#progressBar").removeClass('hide');
    	processCounter = setInterval(updateProcess, 2000);
    	$("#update_adwords").attr('disabled', true);
    });
</script>