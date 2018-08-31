<!-- <script src="<?=base_url('assets/plugins/sweetalert/sweetalert.min.js')?>"></script> -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<script type="text/javascript">
<?php
$propStack = array();
$viewStack = array();
if ($props) {
	foreach ($props as $prop) {
		$propStack[$prop['profile_id']][$prop['property_id']] = $prop['property_name'];
	}
}
if ($views) {
	foreach ($views as $view) {
		$viewStack[$view['property_id']][$view['view_id']] = $view['view_name'];
	}
}
echo 'var propStack = ' . json_encode($propStack) . ';';
echo 'var viewStack = ' . json_encode($viewStack) . ';';
?>
    $( document ).ready( function(){
        $("#webmaster_sites").select2();
        $("#profile").on( 'change', function( event ){
            let props = propStack[ this.value ];
            $("#lastMonthsData").html("");
            $("#currentMonthData").html("");
            $('#prop').html("")
                    .append($("<option></option>").attr("value","").text("Select"));
            if( props ){
                $.each(props, function(key, value) {
                     $('#prop').append($("<option></option>")
                        .attr("value",key).text(value));
                });
            }
            event.preventDefault();
        });

        $("#prop").on( 'change', function( event ){
            let views = viewStack[ this.value ];
            $('#view').html("")
                    .append($("<option></option>").attr("value","").text("Select"));
            if( views ){
                $.each(views, function(key, value) {
                     $('#view').append($("<option></option>")
                        .attr("value",key).text(value));
                });
            }
            event.preventDefault();
        });

        $("#getGoogleData").on( 'submit', function( event ){
            let profile = $( "#profile" ).val();
            let prop = $( "#prop" ).val();
            let view = $( "#view" ).val();            
            $("#lastMonthsData").html("");
            $("#currentMonthData").html("");            
            // $("#lastMonthsData").html('<img src="<?=base_url('img/spinner.gif');?>"> loading...');            
            // $.post("<?= base_url( 'report/getViewData' ) ?>",{
            //     profile: profile,
            //     prop: prop,
            //     view: view,
            //     prof_id: "<?= $profDet[ 'id' ]; ?>"
            // },function(data){
            //     $("#lastMonthsData").html( data.lastMonthHtml );
            //     $("#currentMonthData").html( data.currMonthHtml );
            // }, 'json');
let pData = {
    profile: profile,
    prop: prop,
    view: view,
    prof_id: "<?= $profDet[ 'id' ]; ?>",
    webmaster_site: webmaster_site
};
$('#progress-bar').removeClass('hide');
$.ajax({
    xhr: function () {        
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", function (evt) {            
            if (evt.lengthComputable) {                
                var percentComplete = evt.loaded / evt.total;                
                $('#progress-bar')
                .css( { width: percentComplete + '%' } )
                .html( percentComplete + '%' );
                // $('.progress').css({
                //     width: percentComplete * 100 + '%'
                // });
                if (percentComplete === 1) {
                    $('#progress-bar')
                    .css( { width: '50%' } )
                    .html( '50%' );
                    // $('#progress-bar').addClass('hide');
                    // $('#progress-bar')
                    // .css( { width: percentComplete * 0 + '%' } )
                    // .html( percentComplete * 0 + '%' );                    
                }
            }
        }, false);
        xhr.addEventListener("progress", function (evt) {            
            if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;                
                // $('.progress').css({
                //     width: percentComplete * 100 + '%'
                // });
                $('#progress-bar')
                .css( { width: 100 + '100%' } )
                .html( '100%' );
                $('#progress-bar').addClass('hide')
                .css( { width: '0%' } )
                .html( '0%' );
            } else {                
                $('#progress-bar')
                .css( { width: 70 + '%' } )
                .html( 70 + '%' );
                $('#progress-bar').addClass('hide')
                .css( { width: '0%' } )
                .html( '0%' );
            }
        }, false);
        return xhr;
    },
    type: 'POST',
    url: "<?= base_url( 'report/getViewData' ) ?>",
    data: pData,
    success: function (data) {        
        $('#progress-bar').addClass('hide')
        .css( { width: '0%' } )
        .html( '0%' );
        data = JSON.parse( data );
        $("#lastMonthsData").html( data.lastMonthHtml );
        $("#currentMonthData").html( data.currMonthHtml );        
    }
},'json');
            event.preventDefault();
            return false;
        });
    });
</script>