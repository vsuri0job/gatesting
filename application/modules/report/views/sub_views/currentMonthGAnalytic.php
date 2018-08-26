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
$currMonthData[ 'bounce_rate' ] = number_format($currMonthData[ 'bounce_rate' ], 2).'%';
$currMonthData[ 'goal_conversion_rate' ] = number_format($currMonthData[ 'goal_conversion_rate' ], 2).'%';
$currMonthData[ 'avg_session_duration' ] = gmdate("H:i:s", $currMonthData[ 'avg_session_duration' ]);
$currMonthData[ 'page_view_per_sessions' ] = number_format($currMonthData[ 'page_view_per_sessions' ], 2);
$currMonthData[ 'avg_page_download_time' ] = number_format($currMonthData[ 'avg_page_download_time' ], 2);
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
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Users</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'users' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'users' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'users' ] ?></h2>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Sessions</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'sessions' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'sessions' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'sessions' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Page / Session</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'page_view_per_sessions' ][ 'class' ]; ?>"></i>  <?= $statClasses[ 'page_view_per_sessions' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'page_view_per_sessions' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Avg. Session Duration</h4>
                <div class="text-right">                    
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">                            
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'avg_session_duration' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Bounce Rate</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">                            
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'bounce_rate' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Goal Completions</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'goal_completion_all' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'goal_completion_all' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'goal_completion_all' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Goal Coversion Rate</h4>
                <div class="text-right">                    
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'goal_conversion_rate' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'goal_conversion_rate' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'goal_conversion_rate' ] ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Page Download Time</h4>
                <div class="text-right">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 align-bottom">
                            <small><i class="<?= $statClasses[ 'avg_page_download_time' ][ 'class' ]; ?>"></i> <?= $statClasses[ 'avg_page_download_time' ][ 'inc' ]; ?></small>
                        </div>
                        <div class="col-lg-6 col-md-6 kpiText">
                            <h2 class="font-light m-b-0"><?= $currMonthData[ 'avg_page_download_time' ] ?></h2>
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