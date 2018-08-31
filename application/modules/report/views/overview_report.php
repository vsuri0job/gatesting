<?php	
	$prices = explode(',', $service_url_data[ 'price' ]);
	$services = explode(',', $service_url_data[ 'services' ]);
	$servicePrices = array_combine($services, $prices);	
	$seo_price = com_arrIndex($servicePrices, 'SEO', 0);
	$ppc_price = com_arrIndex($servicePrices, 'PPC', 0);
	$gmb_price = com_arrIndex($servicePrices, 'Local SEO', 0);
	list( $firMonthDate) = com_lastMonths( 1, "", 1, 1 );
	$firMonthData = date('Y-m', strtotime( $firMonthDate ));		
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?= $prodDet[ 'account_url' ] ?></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Row -->
<div class="row">
	<?php
		foreach ($gmb_locs as $locKey => $locName) {

			$gadwords_data = $gad_data[ $firMonthData ];
			$ganalytic_data = $ga_data[ $firMonthData ];
			$gmb_loc_data = $gmb_data[ $locKey ][ $firMonthData ];
			$cost = $seo_price + $ppc_price + $gmb_price;
			$clicks = $gmb_loc_data[ "clicks" ];
			$leads = $gmb_loc_data[ "calls" ] + $ganalytic_data[ "conversion" ] + $gadwords_data[ "goal_completion_all" ];
			$closing_rate = $prodDet[ 'close_rate' ];
			$closed_leads = ( $leads * $prodDet[ 'close_rate' ] ) / 100;
			$avg_sale = $prodDet[ 'avg_sale_amount' ];
			$avg_sale_revenue = $closed_leads * $prodDet[ 'avg_sale_amount' ];
			$ltv_amount = $prodDet[ 'ltv_amount' ];
			$ltv = $avg_sale_revenue * $ltv_amount;
			$roas = $ltv/$cost;
			$seo_closed_leads = ( $ganalytic_data[ "conversion" ] * $prodDet[ 'close_rate' ] ) / 100;
			$ppc_closed_leads = ( $gadwords_data[ "goal_completion_all" ] * $prodDet[ 'close_rate' ] ) / 100;
			$gmb_closed_leads = ( $gmb_loc_data[ "calls" ] * $prodDet[ 'close_rate' ] ) / 100;
			$seo_avg_sale_revenue = $seo_closed_leads * $prodDet[ 'avg_sale_amount' ];
			$ppc_avg_sale_revenue = $ppc_closed_leads * $prodDet[ 'avg_sale_amount' ];
			$ppc_avg_sale_revenue = $gmb_closed_leads * $prodDet[ 'avg_sale_amount' ];
			$ltv_seo = $seo_avg_sale_revenue * $ltv_amount;
			$ltv_ppc = $ppc_avg_sale_revenue * $ltv_amount;
			$ltv_gmb = $gmb_avg_sale_revenue * $ltv_amount;
			$roas_seo = $ltv_seo/$seo_price;
			$roas_ppc = $ltv_ppc/$seo_price;
			$roas_gmb = $ltv_gmb/$seo_price;
	?>
	<div class="table-responsive">
		<h3>Total</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Cost</th>
                    <th>Clicks</th>
                    <th># of Leads Generated</th>
                    <th>Closing Rate</th>
                    <th># of Closed Leads (Sales)</th>
                    <th>Average Sale Amount</th>
                    <th>Amount Sales Revenue</th>
                    <th>LTV Amount</th>
                    <th>LTV</th>
                    <th>ROAS</th>
                </tr>
            </thead>
            <tbody>
            	<tr>
            		<td><?= $cost; ?></td>
            		<td><?= $clicks; ?></td>
            		<td><?= $leads; ?></td>
            		<td><?= $closing_rate; ?></td>
            		<td><?= $closed_leads; ?></td>
            		<td><?= $avg_sale; ?></td>
            		<td><?= $avg_sale_revenue; ?></td>
            		<td><?= $ltv_amount; ?></td>
            		<td><?= $ltv; ?></td>
            		<td><?= $roas; ?></td>
            	</tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Cost</th>
                    <th>Clicks</th>
                    <th># of Leads Generated</th>
                    <th>Closing Rate</th>
                    <th># of Closed Leads (Sales)</th>
                    <th>Average Sale Amount</th>
                    <th>Amount Sales Revenue</th>
                    <th>LTV Amount</th>
                    <th>LTV</th>
                    <th>ROAS</th>
                </tr>
            </tfoot>
        </table>
	</div>

	<div class="table-responsive">
		<h3>Last Month</h3>
        <table class="table">
            <thead>
                <tr>
                	<th>Campaign</th>
                    <th>Cost</th>
                    <th>Clicks</th>
                    <th># of Leads Generated</th>
                    <th>Closing Rate</th>
                    <th># of Closed Leads (Sales)</th>
                    <th>Average Sale Amount</th>
                    <th>Amount Sales Revenue</th>
                    <th>LTV Amount</th>
                    <th>LTV</th>
                    <th>ROAS</th>
                </tr>
            </thead>
            <tbody>
            	<tr>
            		<td>SEO</td>
            		<td><?= $seo_price; ?></td>
            		<td><?= $gmb_loc_data[ "clicks" ]; ?></td>
            		<td><?= $ganalytic_data[ "conversion" ]; ?></td>
            		<td><?= $prodDet[ 'close_rate' ]; ?></td>
            		<td><?= $seo_closed_leads; ?></td>
            		<td><?= $avg_sale; ?></td>
            		<td><?= $seo_avg_sale_revenue; ?></td>
            		<td><?= $ltv_amount; ?></td>
            		<td><?= $ltv_seo; ?></td>
            		<td><?= $roas_seo; ?></td>
            	</tr>
            	<tr>
            		<td>PPC</td>
            		<td><?= $ppc_price; ?></td>
            		<td><?= $gmb_loc_data[ "clicks" ]; ?></td>
            		<td><?= $gadwords_data[ "goal_completion_all" ]; ?></td>
            		<td><?= $prodDet[ 'close_rate' ]; ?></td>
            		<td><?= $ppc_closed_leads; ?></td>
            		<td><?= $avg_sale; ?></td>
            		<td><?= $ppc_avg_sale_revenue; ?></td>
            		<td><?= $ltv_amount; ?></td>
            		<td><?= $ltv_ppc; ?></td>
            		<td><?= $roas_ppc; ?></td>
            	</tr>
            	<tr>
            		<td>GMB</td>
            		<td><?= $seo_price; ?></td>
            		<td><?= $gmb_loc_data[ "clicks" ]; ?></td>
            		<td><?= $gmb_loc_data[ "calls" ]; ?></td>
            		<td><?= $prodDet[ 'close_rate' ]; ?></td>
            		<td><?= $gmb_closed_leads; ?></td>
            		<td><?= $avg_sale; ?></td>
            		<td><?= $gmb_avg_sale_revenue; ?></td>
            		<td><?= $ltv_amount; ?></td>
            		<td><?= $ltv_gmb; ?></td>
            		<td><?= $roas_gmb; ?></td>
            	</tr>
            </tbody>
            <tfoot>
                <tr>
                	<th>Campaign</th>
                    <th>Cost</th>
                    <th>Clicks</th>
                    <th># of Leads Generated</th>
                    <th>Closing Rate</th>
                    <th># of Closed Leads (Sales)</th>
                    <th>Average Sale Amount</th>
                    <th>Amount Sales Revenue</th>
                    <th>LTV Amount</th>
                    <th>LTV</th>
                    <th>ROAS</th>
                </tr>
            </tfoot>
        </table>
	</div>

	<?php } ?>
</div>
<div class="row">
    <div class="table-responsive">
    	<h3>Over all Total</h3>
    </div>
</div>

<div id="lastMonthsData">   
</div>