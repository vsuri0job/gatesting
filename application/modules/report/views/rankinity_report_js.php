<?php
$engineId = current( $profileEngines );
$engineId = com_arrIndex( $engineId, 'engine_id', '' );
?>
<script src="<?= base_url( 'assets/plugins/raphael/raphael-min.js' ); ?>"></script>
<script src="<?= base_url( 'assets/plugins/morrisjs/morris.js' ); ?>"></script>
<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ); ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var profVisibilities = <?= json_encode($profileEnginesVisibility) ?>;
    var profRanks  = <?= json_encode($profileEnginesRanks) ?>;
    var profVisHistory = <?= json_encode($profileEnginesVisibilityHistory) ?>;    
    function tableRow( rowData ){
        let icon = 'mdi-arrow-down-bold text-danger';
        rowData[ 'position_boost' ] = parseInt( rowData[ 'position_boost' ] );
        if( rowData[ 'position_boost' ] >= 0 ){
            icon = 'mdi-arrow-up-bold text-success';
        }
        let trow = "<tr> \
                        <td class=\"ucfirst\">"+rowData[ 'keyword_name' ]+"</td>\
                        <td>"+rowData[ 'position' ]+"\
                        <i class=\"mdi "+icon+"\" aria-hidden=\"true\"></i>\
                        <small>"+Math.abs(rowData[ 'position_boost' ])+"</small>\
                        </td>\
                    </tr>";
        return trow;
    }
    $( document ).ready( function(){

        var rankTable = $('#keyword-ranks').DataTable({
                            "order": []
                        });

        $( '#engines' ).on( 'change', function( event ){
            let engId = this.value;
            let profDet = profVisibilities[ engId ];
            $( "#profVisibility" ).html( profDet[ 'position' ] );
            $( "#pos_unchanged" ).html( profDet[ 'position_unchanged' ] );
            $( "#pos_up" ).html( profDet[ 'position_up' ] );
            $( "#pos_down" ).html( profDet[ 'position_down' ] );
            let profRank = profRanks[ engId ];
            let profRanksHtml = '';
            getMorrisOffline( engId );
            rankTable.clear().draw();            
            $.each( profRank, function( index, value ){
                profRanksHtml = tableRow( value );                
                rankTable.row.add( $( profRanksHtml ) ).draw( );                
            });
            // $("keywordData").html(profRanksHtml);
            //$('#keyword-ranks').DataTable({"bDestroy": true, "order": []});
            event.preventDefault();
        });
    });
</script>
<script type="text/javascript">
var morrisLine;
initMorris();
//getMorris(); 
getMorrisOffline( "<?= $engineId; ?>" );

function initMorris() {
   morrisLine = Morris.Line({
    element: 'visibility-chart',
    xkey: 'date',
    ykeys: ['vdata'],
    xLabelFormat: function(date) {        
        let label = new Date( date.label.split(" ") );
        console.log( label.getMonth() );
        console.log( monthNames );
        label = label.getDate()+'/'+( monthNames[ label.getMonth() ] );
        return label;
    },
    parseTime: false,
    resize: true,
    smooth: true,
    axes: 'x',
    lineColors: ['#32c5d2']    
  });
}

function setMorris(data) {
  morrisLine.setData(data);
}

function getMorrisOffline( engineId ) {    
 var lineData = profVisHistory[ engineId ];
  setMorris(lineData);
}
</script>