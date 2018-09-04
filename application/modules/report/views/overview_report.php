<?php if (!isset($full_report_show) || !$full_report_show) {?>
<div class="row">
    <div class="col-lg-12">
        <div class="card card-outline-info">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8 col-8 align-self-center">
                        <h4 class="m-b-0 text-white"><?=$prodDet['account_url']?></h4>
                    </div>
                    <div class="col-md-4 col-4 align-self-center">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
$monthStamp = com_lastMonths(13);
$prices = explode(',', $service_url_data['price']);
$services = explode(',', $service_url_data['services']);
$servicePrices = array_combine($services, $prices);
$seo_price = com_arrIndex($servicePrices, 'SEO', 0);
$ppc_price = com_arrIndex($servicePrices, 'PPC', 0);
$gmb_price = com_arrIndex($servicePrices, 'Local SEO', 0);
list($firMonthDate) = com_lastMonths(1, "", 1, 1);
$firMonthData = date('Y-m', strtotime($firMonthDate));
$cost = $seo_price + $ppc_price + $gmb_price;
$lm_ga_data = $ga_data[$firMonthData];
$lm_gmb_data = $gmb_data[$firMonthData];
$lm_gad_data = $gad_data[$firMonthData];

$ltv_amount = $prodDet['ltv_amount'];
$closing_rate = $prodDet['close_rate'];
$avg_sale = $prodDet['avg_sale_amount'];
$lm_ttl_clicks = $lm_gmb_data["clicks"];
$lm_ttl_leads = $lm_gmb_data["calls"] + $lm_ga_data["conversion"] + $lm_gad_data["goal_completion_all"];
$lm_ttl_closed_leads = ($lm_ttl_leads * $prodDet['close_rate']) / 100;
$lm_avg_sale_revenue = $lm_ttl_closed_leads * $prodDet['avg_sale_amount'];
$lm_ttl_ltv = $lm_avg_sale_revenue * $ltv_amount;
$lm_ttl_roas = $seo_roas = $ppc_roas = $gmb_roas = 0;
if ($lm_ttl_ltv && $cost) {
	$lm_ttl_roas = $lm_ttl_ltv / $cost;
}

$seo_closed_leads = ($lm_ga_data["conversion"] * $prodDet['close_rate']) / 100;
$ppc_closed_leads = ($lm_gad_data["goal_completion_all"] * $prodDet['close_rate']) / 100;
$gmb_closed_leads = ($lm_gmb_data["calls"] * $prodDet['close_rate']) / 100;

$seo_avg_sale_revenue = $seo_closed_leads * $prodDet['avg_sale_amount'];
$ppc_avg_sale_revenue = $ppc_closed_leads * $prodDet['avg_sale_amount'];
$ppc_avg_sale_revenue = $gmb_closed_leads * $prodDet['avg_sale_amount'];

$seo_ltv = $seo_avg_sale_revenue * $ltv_amount;
$ppc_ltv = $ppc_avg_sale_revenue * $ltv_amount;
$gmb_ltv = $gmb_avg_sale_revenue * $ltv_amount;

$seo_price = floatval($seo_price);
if ($seo_price) {
	$roas_seo = $ltv_seo / $seo_price;
}
$ppc_price = floatval($ppc_price);
if ($ppc_price) {
	$roas_seo = $ltv_seo / $ppc_price;
}
$gmb_price = floatval($gmb_price);
if ($gmb_price) {
	$roas_gmb = $ltv_gmb / $gmb_price;
}
?>
<!-- Row -->
<div class="row">
    <div class="table-responsive">
        <h3>Total</h3>
        <table class="table" id="ttl-tbl">
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
                    <td>$<?=number_format($cost);?></td>
                    <td><?=number_format($lm_ttl_clicks);?></td>
                    <td><?=number_format($lm_ttl_leads);?></td>
                    <td><?=number_format($closing_rate);?>%</td>
                    <td><?=number_format($lm_ttl_closed_leads);?></td>
                    <td>$<?=number_format($avg_sale);?></td>
                    <td>$<?=number_format($lm_avg_sale_revenue);?></td>
                    <td>$<?=number_format($ltv_amount);?></td>
                    <td>$<?=number_format($lm_ttl_ltv);?></td>
                    <td>$<?=number_format($lm_ttl_roas);?></td>
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
</div>

<?php if (!isset($full_report_show) || !$full_report_show) {
	?>
<div class="row">
    <div class="table-responsive">
        <h3>Total Descriptive</h3>
        <table class="table" id="ttl-tbl">
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
                    <td>$<?=number_format($seo_cost);?></td>
                    <td><?=number_format($lm_ttl_clicks);?></td>
                    <td><?=number_format($lm_ga_data["conversion"]);?></td>
                    <td><?=number_format($closing_rate);?>%</td>
                    <td><?=number_format($seo_closed_leads);?></td>
                    <td>$<?=number_format($avg_sale);?></td>
                    <td>$<?=number_format($seo_avg_sale_revenue);?></td>
                    <td>$<?=number_format($ltv_amount);?></td>
                    <td>$<?=number_format($seo_ltv);?></td>
                    <td>$<?=number_format($seo_roas);?></td>
                </tr>
                <tr>
                    <td>PPC</td>
                    <td>$<?=number_format($ppc_cost);?></td>
                    <td><?=number_format($lm_ttl_clicks);?></td>
                    <td><?=number_format($lm_gad_data["goal_completion_all"]);?></td>
                    <td><?=number_format($closing_rate);?>%</td>
                    <td><?=number_format($ppc_closed_leads);?></td>
                    <td>$<?=number_format($avg_sale);?></td>
                    <td>$<?=number_format($ppc_avg_sale_revenue);?></td>
                    <td>$<?=number_format($ltv_amount);?></td>
                    <td>$<?=number_format($ppc_ltv);?></td>
                    <td>$<?=number_format($ppc_roas);?></td>
                </tr>
                <tr>
                    <td>GMB</td>
                    <td>$<?=number_format($gmb_cost);?></td>
                    <td><?=number_format($lm_ttl_clicks);?></td>
                    <td><?=number_format($lm_gmb_data["calls"]);?></td>
                    <td><?=number_format($closing_rate);?>%</td>
                    <td><?=number_format($gmb_closed_leads);?></td>
                    <td>$<?=number_format($avg_sale);?></td>
                    <td>$<?=number_format($gmb_avg_sale_revenue);?></td>
                    <td>$<?=number_format($ltv_amount);?></td>
                    <td>$<?=number_format($gmb_ltv);?></td>
                    <td>$<?=number_format($gmb_roas);?></td>
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
</div>

<div class="row">
<div id="lastMonthsData">
    <div class="table-responsive">
        <h3>Month Over Month</h3>
        <table class="table" id="ttl-tbl">
            <thead>
                <tr>
                    <th>Month</th>
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
<?php
foreach ($monthStamp as $timeStamp => $monthDate) {
		$month_txt = date("F Y", $timeStamp);
		$month_ref = date("Y-m", $timeStamp);
		$tm_ga_data = $ga_data[$month_ref];
		$tm_gmb_data = $gmb_data[$month_ref];
		$tm_gad_data = $gad_data[$month_ref];
		$tm_ttl_clicks = com_arrIndex($tm_gmb_data, "clicks", 0);
		$tm_ttl_leads = com_arrIndex($tm_gmb_data, "calls", 0) + com_arrIndex($tm_ga_data, "conversion", 0) + com_arrIndex($tm_gad_data, "goal_completion_all", 0);
		$tm_ttl_closed_leads = ($tm_ttl_leads * $prodDet['close_rate']) / 100;
		$tm_avg_sale_revenue = $tm_ttl_closed_leads * $prodDet['avg_sale_amount'];
		$tm_ttl_ltv = $tm_avg_sale_revenue * $ltv_amount;
		$tm_ttl_roas = 0;
		if ($tm_ttl_ltv && $cost) {
			$tm_ttl_roas = $tm_ttl_ltv / $cost;
		}
		?>
                <tr>
                    <td><?=$month_txt;?></td>
                    <td>$<?=number_format($cost);?></td>
                    <td><?=number_format($tm_ttl_clicks);?></td>
                    <td><?=number_format($tm_ttl_leads);?></td>
                    <td><?=number_format($closing_rate);?>%</td>
                    <td><?=number_format($tm_ttl_closed_leads);?></td>
                    <td>$<?=number_format($avg_sale);?></td>
                    <td>$<?=number_format($tm_avg_sale_revenue);?></td>
                    <td>$<?=number_format($ltv_amount);?></td>
                    <td>$<?=number_format($tm_ttl_ltv);?></td>
                    <td>$<?=number_format($tm_ttl_roas);?></td>
                </tr>
<?php }?>
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
</div>
</div>
<?php }?>