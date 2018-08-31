<!-- <script src="<?=base_url('assets/plugins/sweetalert/sweetalert.min.js')?>"></script> -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<link href="<?= base_url( 'assets/plugins/select2/dist/css/select2.min.css' ) ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url( 'assets/plugins/select2/dist/js/select2.full.min.js' ) ?>" type="text/javascript"></script>
<script type="text/javascript">
    $( document ).ready( function(){
        $("#webmaster_sites").select2();
    });
</script>