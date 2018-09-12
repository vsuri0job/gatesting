<!-- <script src="<?=base_url('assets/plugins/sweetalert/sweetalert.min.js')?>"></script> -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="<?=base_url('assets/plugins/select2/dist/css/select2.min.css')?>" rel="stylesheet" type="text/css" />
<script src="<?=base_url('assets/plugins/select2/dist/js/select2.full.min.js')?>" type="text/javascript"></script>
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
		$viewStack[$view['property_id']][$view['view_id']] = $view['view_name'] . '        ' . $view['property_id'];
	}
}
echo 'var propStack = ' . json_encode($propStack) . ';';
echo 'var viewStack = ' . json_encode($viewStack) . ';';
?>
    var processCounter = "";
    function updateProcess() {
        let pro_txt = $('#progress-bar').html();
        pro_txt = parseInt( pro_txt.trim() );
        if( pro_txt < 92 ){
            pro_txt = pro_txt + 2;
            pro_txt = pro_txt+"%";
            $('#progress-bar')
                .css( { width: pro_txt } )
                .html( pro_txt );
        }
    }
    $( document ).ready( function(){
        $("#prop").select2();
        $("#view").select2();
        $("#profile").select2();
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
            event.preventDefault();
            let profile = $( "#profile" ).val();
            let prop = $( "#prop" ).val();
            let view = $( "#view" ).val();
            $( "#prop" ).attr('disabled', true);
            $( "#view" ).attr('disabled', true);
            $( "#profile" ).attr('disabled', true);
            $( "#link_update").hide();
            $("#lastMonthsData").html("");
            $("#currentMonthData").html("");
            // $("#lastMonthsData").html('<img src="<?=base_url('img/spinner.gif');?>"> loading...');
            // $.post("<?=base_url('report/getViewData')?>",{
            //     profile: profile,
            //     prop: prop,
            //     view: view,
            //     prof_id: "<?=$profDet['id'];?>"
            // },function(data){
            //     $("#lastMonthsData").html( data.lastMonthHtml );
            //     $("#currentMonthData").html( data.currMonthHtml );
            // }, 'json');
            let pData = {
                profile: profile,
                prop: prop,
                view: view,
                prof_id: "<?=$profDet['id'];?>"
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
                                .css( { width: '15%' } )
                                .html( '15%' );
                                processCounter = setInterval(updateProcess, 3000);
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
                url: "<?=base_url('report/getViewData')?>",
                data: pData,
                success: function (data) {
                    $('#progress-bar').addClass('hide')
                    .css( { width: '0%' } )
                    .html( '0%' );
                    clearInterval( processCounter );
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