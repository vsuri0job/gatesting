<script type="text/javascript">
<?php
    $propStack = array();
    $viewStack = array();
    if( $props ){
        foreach( $props as $prop ){
            $propStack[ $prop[ 'profile_id' ] ][ $prop[ 'property_id' ] ] = $prop[ 'property_name' ];
        }
    }
    if( $views ){
        foreach( $views as $view ){
            $viewStack[ $view[ 'property_id' ] ][ $view[ 'view_id' ] ] = $view[ 'view_name' ];
        }
    }
    echo 'var propStack = '.json_encode( $propStack ).';';
    echo 'var viewStack = '.json_encode( $viewStack ).';';
    ?>  
    $( document ).ready( function(){
        $("#profile").on( 'change', function( event ){
            let props = propStack[ this.value ];
            $("#lastMonthsData").html("");
            $("#currentMonthData").html("");
            $('#view').html("");
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

        $("#getGoogleAdwordData").on( 'submit', function( event ){
            let profile = $( "#profile" ).val();
            let prop = $( "#prop" ).val();
            let view = $( "#view" ).val();
            $("#lastMonthsData").html("");
            $("#currentMonthData").html("");
            $("#lastMonthsData").html('<img src="<?= base_url( 'img/spinner.gif' ); ?>"> loading...');
            $.post("report/getViewAdwordData",{
                profile: profile,
                prop: prop,
                view: view
            },function(data){
                $("#lastMonthsData").html( data.lastMonthHtml );
                $("#currentMonthData").html( data.currMonthHtml );                
            }, 'json');
            event.preventDefault();
        });
    });
</script>