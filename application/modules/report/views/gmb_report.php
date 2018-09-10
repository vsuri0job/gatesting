<style type="text/css">
.align-bottom small {
    position: absolute;
    right: 0;
    top: 11px;
}
.kpiText{
    padding: 0; 
}
</style>
<?php if (!isset($full_report_show) || !$full_report_show) {
	?>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10 col-10 align-self-center">
                        <h4 class="m-b-0 text-white"><?=$prodDet['account_url']?> Locations:
                        <?php
echo form_dropdown('locations', $gmb_locs, "",
		' id="locations" class="form-control" style="width:250px"');
	?>
                        </h4>
                    </div>
                    <div class="col-md-2 col-2 align-self-center">
                        <?php if ($show_public_url) {
		echo anchor('publicReport/' . $prodDet['share_gmb_link'],
			'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
	}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else {
	?>
<div class="row">
                    <div class="col-md-12 col-12 align-self-center">
                        <h4 class="m-b-0"> Locations:
                        <?php
echo form_dropdown('locations', $gmb_locs, "",
		' id="locations" class="form-control" style="width:250px"');
	?>
                        </h4>
                    </div>
                </div>
<?php }?>
<!-- Row -->
<div class="row">
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> 
                    <img src="<?= base_url( 'img/social/gmb-on.png' ) ?>" width="20px" height="20px"> Clicks</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 id="clicks-info" 
                                class="font-light m-b-0"><?= $gmb_loc_kpis[$gmb_loc_id][ 'clicks' ][ 'infotxt' ] ?></h2>
                            <small>
                                <i id="clicks-icon" 
                                class="<?= $gmb_loc_kpis[$gmb_loc_id][ 'clicks' ][ 'class' ]; ?>"></i> 
                                <?= $gmb_loc_kpis[$gmb_loc_id][ 'clicks' ][ 'difftxt' ]; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> 
                    <img src="<?= base_url( 'img/social/gmb-on.png' ) ?>" width="20px" height="20px"> Directions</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 id="direc-info" 
                                class="font-light m-b-0"><?= $gmb_loc_kpis[$gmb_loc_id][ 'direc' ][ 'infotxt' ] ?></h2>
                            <small>
                                <i id="direc-icon" 
                                    class="<?= $gmb_loc_kpis[$gmb_loc_id][ 'direc' ][ 'class' ]; ?>"></i> <?= $gmb_loc_kpis[$gmb_loc_id][ 'direc' ][ 'difftxt' ]; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> 
                    <img src="<?= base_url( 'img/social/gmb-on.png' ) ?>" width="20px" height="20px"> Calls</h4>
                <div class="text-left m-l-10">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 kpiText">
                            <h2 id="calls-info" 
                            class="font-light m-b-0"><?= $gmb_loc_kpis[$gmb_loc_id][ 'calls' ][ 'infotxt' ] ?></h2>
                            <small>
                                <i  id="calls-icon" 
                                class="<?= $gmb_loc_kpis[$gmb_loc_id][ 'calls' ][ 'class' ]; ?>"></i> <?= $gmb_loc_kpis[$gmb_loc_id][ 'calls' ][ 'difftxt' ]; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="locations-data" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Clicks to Site</th>
                        <th>Driving Direction Requests</th>
                        <th>Calls</th>
                    </tr>
                </thead>
                <tbody id="locationsData">
                    <?php
    if ($gmb_data) {
    	$loc_data = $gmb_data[$gmb_loc_id];
    	foreach ($loc_data as $lData) {
    		$lData[1] = number_format($lData[1]);
    		$lData[2] = number_format($lData[2]);
    		$lData[3] = number_format($lData[3]);
    		?>
                            <tr>
                                <td class="ucfirst"><?=$lData[0];?></td>
                                <td ><?=$lData[1];?></td>
                                <td ><?=$lData[2];?></td>
                                <td ><?=$lData[3];?></td>
                            </tr>
                    <?php }
    }
    ?>
                </tbody>                
            </table>
        </div>
    </div>
</div>