<script src="<?= base_url( 'assets/plugins/datatables/jquery.dataTables.min.js' ); ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
<script type="text/javascript">
	var gmb_data = <?= json_encode($gmb_data); ?>;
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

        var locTable = $('#locations-data').DataTable({
                            "order": []
                        });    	
        $( '#locations' ).on( 'change', function( event ){
            let locId = this.value;
            let locData = gmb_data[ locId ];
            let locHtml = '';            
            locTable.clear().draw();            
            $.each( locData, function( index, value ){
                locHtml = "<tr> \
                					<td class=\"ucfirst\">"+value[ '0' ]+"</td>\
                					<td >"+value[ '1' ]+"</td>\
                					<td >"+value[ '2' ]+"</td>\
                					<td >"+value[ '3' ]+"</td>\
                				</tr>";
                locTable.row.add( $( locHtml ) ).draw( );                
            });
            event.preventDefault();
        });
    });
</script>