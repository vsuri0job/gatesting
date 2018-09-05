<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
	<?php if( $validation_errors ){ ?>
		swal("Account Url", "<?= $validation_errors; ?>", 'warning');
	<?php } ?>
</script>