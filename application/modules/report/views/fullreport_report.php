<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?=$prodDet['account_url']?></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
<?php if ($show_public_url) {
	echo anchor('publicReport/' . $prodDet['share_analytic_link'],
		'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
    <div class="col-lg-12 m-l-15 m-b-10">
        <?php echo $overview_report; ?>
    </div>


    <div class="col-lg-12 m-l-15 m-b-10">
        <h4 class="m-b-10">SEO Report</h4>
        <?php echo $ganalytic_report; ?>
    </div>


    <div class="col-lg-12 m-l-15 m-b-10">
        <h4 class="m-b-10">Adwords Report</h4>
        <?php echo $gad_report; ?>
    </div>

    <div class="col-lg-12 m-l-15 m-b-10">
        <h4 class="m-b-10">Google My Business Report</h4>
        <?php echo $gmb_report; ?>
    </div>

    <div class="col-lg-12 m-l-15 m-b-10">
        <h4 class="m-b-10">Rankinity Report</h4>
        <?php echo $rankinity_report; ?>
    </div>

    <div class="col-lg-12 m-l-15 m-b-10">
        <h4 class="m-b-10">Citation Report</h4>
        <?php echo $citation_content; ?>
    </div>
