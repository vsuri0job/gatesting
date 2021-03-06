<script src="<?=base_url('assets/plugins/datatables/jquery.dataTables.min.js')?>"></script>
<!-- start - This is for export functionality only -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
<script src="<?=base_url('assets/plugins/raphael/raphael-min.js');?>"></script>
<script src="<?=base_url('assets/plugins/morrisjs/morris.js');?>"></script>
<script type="text/javascript">
	$( document ).ready( function(){
		$('#total-traffic').DataTable({
            "paging":   false,
            "order": []
        });
		$('#organic-traffic').DataTable({
            "paging":   false,
            "order": []
        });
		$('#medium-traffic').DataTable({
            "paging":   false,
            "order": []
        });
		$('#source-traffic').DataTable({
            "paging":   false,
            "order": []
        });
		$('#landing-traffic').DataTable({
            "paging":   false,
            "order": []
        });
	});

    $(function () {
        "use strict";
        var line = new Morris.Line({
          element: 'session-day-chart',
          resize: true,
          data: <?=json_encode($chart_graph)?>,
          xkey: 'date',
          ykeys: ['sess'],
          labels: ['Sessions'],
          gridLineColor: '#eef0f2',
          lineColors: ['#009efb', '#2e893a'],
          lineWidth: 1,
          hideHover: 'auto'
        });
    });
</script>