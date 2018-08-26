<?php
$currMonthRef = date('Y-m', strtotime("-1 months"));
$lastMonthRef = date('Y-m', strtotime("-2 months"));
$lastMonthData = com_arrIndex($ga_data, $lastMonthRef);
$currMonthData = com_arrIndex($ga_data, $currMonthRef);
if( $currMonthData &&  $lastMonthData){
$statClasses = array();
foreach( $currMonthData as $key => $val ){
    if( $key <> 'month_ref' ){
        $statClasses[ $key ][ 'class' ] = 'ti-arrow-up text-success';
        $lMdata = com_arrIndex($lastMonthData, $key, 0);
        $val = str_replace("%", "", $val);
        $val = str_replace("$", "", $val);
        $lMdata = str_replace("%", "", $lMdata);
        $lMdata = str_replace("$", "", $lMdata);
        $statClasses[ $key ][ 'inc' ] = 0;
        if(  $val == $lMdata ){
            $statClasses[ $key ][ 'class' ] = 'ti-arrow-up text-info';
        } elseif( $val < $lMdata && $val && $lMdata ) {
            $statClasses[ $key ][ 'inc' ] = '-'.number_format(($lMdata - $val)/$val * 100, 2).' %';
            $statClasses[ $key ][ 'class' ] = 'ti-arrow-down text-danger';
        } elseif( ($val > $lMdata) && intval( $val ) && intval( $lMdata )  ) {
            $statClasses[ $key ][ 'inc' ] = number_format(($val-$lMdata)/$lMdata * 100, 2).' %';;
        }
    }
}
// $currMonthData[ 'bounce_rate' ] = number_format($currMonthData[ 'bounce_rate' ], 2).'%';
// $currMonthData[ 'goal_conversion_rate' ] = number_format($currMonthData[ 'goal_conversion_rate' ], 2).'%';
// $currMonthData[ 'avg_session_duration' ] = gmdate("H:i:s", $currMonthData[ 'avg_session_duration' ]);
// $currMonthData[ 'page_view_per_sessions' ] = number_format($currMonthData[ 'page_view_per_sessions' ], 2);
// $currMonthData[ 'avg_page_download_time' ] = number_format($currMonthData[ 'avg_page_download_time' ], 2);
if( $currMonthData[ 'cost' ] ){
    $currMonthData[ 'cost' ] = '$'.number_format( $currMonthData[ 'cost' ] / 1000000, 2);
}
if( $currMonthData[ 'avg_cpc' ] ){
    $currMonthData[ 'avg_cpc' ] = '$'.number_format( $currMonthData[ 'avg_cpc' ] / 1000000, 2);
}
if( $currMonthData[ 'cost_per_conversion' ] ){
    $currMonthData[ 'cost_per_conversion' ] = '$'.number_format( $currMonthData[ 'cost_per_conversion' ] / 1000000, 2);
}
?>
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
<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Clicks</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'clicks' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'clicks' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'clicks' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">IMPRESSIONS</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'impressions' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'impressions' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'impressions' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">CTR</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'ctr' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'ctr' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'ctr' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">AVG. CPC</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'avg_cpc' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'avg_cpc' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'avg_cpc' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">COST</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'cost' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'cost' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'cost' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">CONV.</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'conversion' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'conversion' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'conversion' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">COST / CONV.</h4>                
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'cost_per_conversion' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'cost_per_conversion' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'cost_per_conversion' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">AVG. POSITION</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'avg_position' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'avg_position' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'avg_position' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">PHONE CALLS</h4>                
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'phone_calls' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'phone_calls' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'phone_calls' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<!-- Row -->    
<?php
}
?>