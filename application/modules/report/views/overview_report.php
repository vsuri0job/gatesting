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
<?php if ($show_public_url) {
        echo anchor('publicReport/' . $prodDet['share_overview_link'],
            'Public Link', ' class="btn pull-right btn-outline-primary" target="_blank" ');
    }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
$monthStamp = com_lastMonths(13);
// $prices = explode(',', $service_url_data['price']);
// $services = explode(',', $service_url_data['services']);
// $servicePrices = array_combine($services, $prices);
$seo_price = com_arrIndex($service_url_data, 'SEO', 0);
$ppc_price = com_arrIndex($service_url_data, 'PPC', 0);
$gmb_price = com_arrIndex($service_url_data, 'Local SEO', 0);
list($firMonthDate) = com_lastMonths(1, "", 1, 1);
$firMonthData = date('Y-m', strtotime($firMonthDate));
$lm_gan_data = $ga_data[$firMonthData];
$lm_gmb_data = $gmb_data[$firMonthData];
$lm_gad_data = $gad_data[$firMonthData];
$lmAdwordCost = com_arrIndex($lm_gad_data, 'cost', 0);
if( $lmAdwordCost ){
    $lmAdwordCost = $lmAdwordCost / 1000000;
}
$cost = $seo_price + $ppc_price + $gmb_price + $lmAdwordCost;
$avg_sale = $prodDet['avg_sale_amount'];
$ltv_amount = $prodDet['ltv_amount'];
$closing_rate = $prodDet['close_rate'];
$lm_ttl_clicks = com_arrIndex( $lm_gmb_data, "clicks", 0) + com_arrIndex( $lm_gad_data, "clicks", 0) + com_arrIndex( $lm_gan_data, "users", 0);
$lm_gmb_leads = com_arrIndex( $lm_gmb_data, "calls", 0);
$lm_ga_leads = com_arrIndex( $lm_gad_data, "conversion", 0);
$lm_gad_leads = com_arrIndex( $lm_gan_data, "goal_completion_all", 0);
$lm_ttl_leads = $lm_gmb_leads + $lm_ga_leads + $lm_gad_leads;


$lm_ttl_closed_leads = ($lm_ttl_leads * $prodDet['close_rate']) / 100;
$lm_avg_sale_revenue = $lm_ttl_closed_leads * $prodDet['avg_sale_amount'];
$lm_ttl_ltv = $lm_avg_sale_revenue * $ltv_amount;
$lm_ttl_roas = $seo_roas = $ppc_roas = $gmb_roas = 0;
if ($lm_ttl_ltv && $cost) {
	$lm_ttl_roas = $lm_ttl_ltv / $cost;
}

$ppc_closed_leads = ($lm_gad_data["conversion"] * $prodDet['close_rate']) / 100;
$seo_closed_leads = ($lm_gan_data["goal_completion_all"] * $prodDet['close_rate']) / 100;
$gmb_closed_leads = ($lm_gmb_data["calls"] * $prodDet['close_rate']) / 100;

$seo_avg_sale_revenue = $seo_closed_leads * $prodDet['avg_sale_amount'];
$ppc_avg_sale_revenue = $ppc_closed_leads * $prodDet['avg_sale_amount'];
$gmb_avg_sale_revenue = $gmb_closed_leads * $prodDet['avg_sale_amount'];


$seo_ltv = $seo_avg_sale_revenue * $ltv_amount;
$ppc_ltv = $ppc_avg_sale_revenue * $ltv_amount;
$gmb_ltv = $gmb_avg_sale_revenue * $ltv_amount;

$seo_price = floatval($seo_price);
if ($seo_price) {
	$seo_roas = $seo_ltv / $seo_price;
}

$ppc_price = floatval($ppc_price);
if ($ppc_price) {
	$ppc_roas = $ppc_ltv / $ppc_price;
}
$gmb_price = floatval($gmb_price);
if ($gmb_price) {
	$gmb_roas = $gmb_ltv / $gmb_price;
}
$ttl_cost_per_lead = 0;
if( $cost && $lm_ttl_leads ){
    $ttl_cost_per_lead = $cost / $lm_ttl_leads;
}
?>
<!-- Row -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <h3>Total</h3>
            <table id="ttl-tbl" 
                class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Cost</th>
                        <th>Clicks</th>
                        <th># of Leads</th>
                        <th>Cost per lead</th>
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
                        <td>$<?=number_format($ttl_cost_per_lead, 2);?></td>
                        <td><?=number_format($closing_rate);?>%</td>
                        <td><?=number_format($lm_ttl_closed_leads, 2);?></td>
                        <td>$<?=number_format($avg_sale);?></td>
                        <td>$<?=number_format($lm_avg_sale_revenue);?></td>
                        <td><?=number_format($ltv_amount, 2);?></td>
                        <td>$<?=number_format($lm_ttl_ltv);?></td>
                        <td>$<?=number_format($lm_ttl_roas, 2);?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!isset($full_report_show) || !$full_report_show) {
    $gmb_cost_per_lead = $seo_cost_per_lead = $ppc_cost_per_lead = 0;
    if( $seo_price && $lm_gan_data["goal_completion_all"]){
        $seo_cost_per_lead = $seo_price / $lm_gan_data["goal_completion_all"];
    }
    $ppc_ad_price = (float)($ppc_price + $lmAdwordCost);
    if( $ppc_ad_price && $lm_gad_data["conversion"]){
        $ppc_cost_per_lead = $ppc_ad_price / $lm_gad_data["conversion"];
    }
    if( $gmb_price && $lm_gmb_data["calls"]){
        $gmb_cost_per_lead = $gmb_price / $lm_gmb_data["calls"];
    }
?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <h3>Total Descriptive</h3>
            <table id="desc-tbl" 
                class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Cost</th>
                        <th>Clicks</th>
                        <th># of Leads</th>
                        <th>Cost per lead</th>
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
                        <td>$<?=number_format($seo_price);?></td>
                        <td><?=number_format(com_arrIndex( $lm_gan_data, "users", 0));?></td>
                        <td><?=number_format($lm_gan_data["goal_completion_all"]);?></td>
                        <td>$<?=number_format($seo_cost_per_lead, 2);?></td>
                        <td><?=number_format($closing_rate);?>%</td>
                        <td><?=number_format($seo_closed_leads);?></td>
                        <td>$<?=number_format($avg_sale);?></td>
                        <td>$<?=number_format($seo_avg_sale_revenue);?></td>
                        <td><?=number_format($ltv_amount, 2);?></td>
                        <td>$<?=number_format($seo_ltv);?></td>
                        <td>$<?=number_format($seo_roas, 2);?></td>
                    </tr>
                    <tr>
                        <td>PPC</td>
                        <td>$<?=number_format($ppc_ad_price );?></td>                    
                        <td><?=number_format(com_arrIndex( $lm_gad_data, "clicks", 0));?></td>
                        <td><?=number_format($lm_gad_data["conversion"]);?></td>
                        <td>$<?=number_format($ppc_cost_per_lead, 2);?></td>
                        <td><?=number_format($closing_rate);?>%</td>
                        <td><?=number_format($ppc_closed_leads);?></td>
                        <td>$<?=number_format($avg_sale);?></td>
                        <td>$<?=number_format($ppc_avg_sale_revenue);?></td>
                        <td><?=number_format($ltv_amount, 2);?></td>
                        <td>$<?=number_format($ppc_ltv);?></td>
                        <td>$<?=number_format($ppc_roas, 2);?></td>
                    </tr>
                    <tr>
                        <td>GMB</td>
                        <td>$<?=number_format($gmb_price);?></td>                    
                        <td><?=number_format(com_arrIndex( $lm_gmb_data, "clicks", 0));?></td>
                        <td><?=number_format($lm_gmb_data["calls"]);?></td>
                        <td>$<?=number_format($gmb_cost_per_lead, 2);?></td>
                        <td><?=number_format($closing_rate);?>%</td>
                        <td><?=number_format($gmb_closed_leads);?></td>
                        <td>$<?=number_format($avg_sale);?></td>
                        <td>$<?=number_format($gmb_avg_sale_revenue);?></td>
                        <td><?=number_format($ltv_amount, 2);?></td>
                        <td>$<?=number_format($gmb_ltv);?></td>
                        <td>$<?=number_format($gmb_roas, 2);?></td>
                    </tr>
                </tbody>                
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <h3>Month Over Month</h3>        
            <table id="month-tbl" 
                class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">            
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Cost</th>
                        <th>Clicks</th>
                        <th># of Leads</th>
                        <th>Cost per lead</th>
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
    $sum_ltv_amt = number_format($ltv_amount, 2);
    $sum_avg_sale_amt = number_format($avg_sale);
    $sum_closing_rate = number_format($closing_rate).'%';
    $sum_ltv = 0;
    $sum_cost = 0;
    $sum_roas = 0;
    $sum_leads = 0;
    $sum_sales = 0;
    $sum_clicks = 0;
    $sum_cost_lead = 0;
    $sum_revenue_amt = 0;
    $ttlRows = 0;
    $currMonthRef = date("Y-m", time());
    foreach ($monthStamp as $timeStamp => $monthDate) {
            $ttlRows++;
            $month_txt = date("F Y", $timeStamp);
            $month_ref = date("Y-m", $timeStamp);
            
            $tm_ga_data = $ga_data[$month_ref];
            $tm_gmb_data = $gmb_data[$month_ref];
            $tm_gad_data = $gad_data[$month_ref];
            
            $tm_ttl_clicks = com_arrIndex($tm_gmb_data, "clicks", 0) 
                            + com_arrIndex($tm_gad_data, "clicks", 0)
                            + com_arrIndex($tm_ga_data, 'users', 0);
            $tm_ttl_leads = com_arrIndex($tm_gmb_data, "calls", 0) + 
                            com_arrIndex($tm_gad_data, "conversion", 0) + 
                            com_arrIndex($tm_ga_data, "goal_completion_all", 0);

            $lmAdwordCost = com_arrIndex($tm_gad_data, 'cost', 0);
            if( $lmAdwordCost ){
                $lmAdwordCost = $lmAdwordCost / 1000000;
            }
            $cost = $seo_price + $ppc_price + $gmb_price + $lmAdwordCost;
    		$tm_ttl_closed_leads = ($tm_ttl_leads * $prodDet['close_rate']) / 100;        
    		$tm_avg_sale_revenue = $tm_ttl_closed_leads * $prodDet['avg_sale_amount'];
    		$tm_ttl_ltv = $tm_avg_sale_revenue * $ltv_amount;
    		$tm_ttl_roas = 0;
    		if ($tm_ttl_ltv && $cost) {
    			$tm_ttl_roas = $tm_ttl_ltv / $cost;
    		}
            $tm_cost_lead = 0;
            if ($tm_ttl_leads && $cost) {                
                $tm_cost_lead = $cost/$tm_ttl_leads;
            }
            if( $currMonthRef <> $month_ref){
                $sum_ltv += $tm_ttl_ltv;
                $sum_cost += $cost;
                $sum_roas += $tm_ttl_roas;
                $sum_leads += $tm_ttl_leads;
                $sum_sales += number_format($tm_ttl_closed_leads, 2);
                $sum_clicks += $tm_ttl_clicks;
                $sum_revenue_amt += $tm_avg_sale_revenue;
                $sum_cost_lead += $tm_cost_lead;
            }
    		?>
                    <tr>
                        <td><?=$month_txt;?></td>
                        <td>$<?=number_format($cost);?></td>
                        <td><?=number_format($tm_ttl_clicks);?></td>
                        <td><?=number_format($tm_ttl_leads);?></td>
                        <td>$<?=number_format($tm_cost_lead, 2);?></td>
                        <td><?=number_format($closing_rate);?>%</td>
                        <td><?=number_format($tm_ttl_closed_leads, 2);?></td>
                        <td>$<?=number_format($avg_sale);?></td>
                        <td>$<?=number_format($tm_avg_sale_revenue, 2);?></td>
                        <td><?=number_format($ltv_amount, 2);?></td>
                        <td>$<?=number_format($tm_ttl_ltv, 2);?></td>
                        <td>$<?=number_format($tm_ttl_roas, 2);?></td>
                    </tr>
    <?php }
    $cost_per_lead = 0;
    if($sum_cost && $sum_leads){
        $cost_per_lead = $sum_cost/$sum_leads;
    }
    ?>
                    <tr>
                        <td class="font-weight-bold">Total</td>
                        <td class="font-weight-bold">$<?=number_format($sum_cost);?></td>
                        <td class="font-weight-bold"><?=number_format($sum_clicks);?></td>
                        <td class="font-weight-bold"><?=number_format($sum_leads);?></td>
                        <td class="font-weight-bold">$<?=number_format($cost_per_lead, 2);?></td>
                        <td class="font-weight-bold"><?=number_format($closing_rate);?>%</td>
                        <td class="font-weight-bold"><?=number_format($sum_sales, 2);?></td>
                        <td class="font-weight-bold">$<?=number_format($avg_sale);?></td>
                        <td class="font-weight-bold">$<?=number_format($sum_revenue_amt/$ttlRows, 2);?></td>
                        <td class="font-weight-bold"><?=number_format($ltv_amount, 2);?></td>
                        <td class="font-weight-bold">$<?=number_format($sum_ltv, 2);?></td>
                        <td class="font-weight-bold">$<?=number_format($sum_roas, 2);?></td>
                    </tr>
                </tbody>                
            </table>
        </div>
</div>
</div>
<?php }?>