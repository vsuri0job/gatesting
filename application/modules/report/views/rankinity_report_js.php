<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ); ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
    var profVisibilities = <?= json_encode($profileEnginesVisibility) ?>;
    var profRanks  = <?= json_encode($profileEnginesRanks) ?>;
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
            console.log( this.value );
            let profDet = profVisibilities[ this.value ];
            $( "#profVisibility" ).html( profDet[ 'position' ] );
            $( "#pos_unchanged" ).html( profDet[ 'position_unchanged' ] );
            $( "#pos_up" ).html( profDet[ 'position_up' ] );
            $( "#pos_down" ).html( profDet[ 'position_down' ] );
            let profRank = profRanks[ this.value ];
            console.log( profRank );
            let profRanksHtml = '';
            rankTable.clear().draw();            
            $.each( profRank, function( index, value ){
                profRanksHtml = tableRow( value );
                console.log( profRanksHtml );
                rankTable.row.add( $( profRanksHtml ) ).draw( );                
            });
            // $("keywordData").html(profRanksHtml);
            //$('#keyword-ranks').DataTable({"bDestroy": true, "order": []});
            event.preventDefault();
        });
    });
</script>