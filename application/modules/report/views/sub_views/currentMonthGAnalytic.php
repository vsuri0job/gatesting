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
$kpis = array();
$kpis[ 'users' ] = array('text' => 'Users', 'skip_icon' => false );
$kpis[ 'sessions' ] = array('text' => 'Sessions', 'skip_icon' => false );
$kpis[ 'page_view_per_sessions' ] = array('text' => 'Page / Session', 'skip_icon' => false );
$kpis[ 'avg_session_duration' ] =  array('text' => 'Avg. Session Duration', 'skip_icon' => true );
$kpis[ 'bounce_rate' ] =  array('text' => 'Bounce Rate', 'skip_icon' => true );
$kpis[ 'goal_completion_all' ] = array('text' => 'Goal Completions', 'skip_icon' => false );
$kpis[ 'goal_conversion_rate' ] = array('text' => 'Goal Coversion Rate', 'skip_icon' => false );
$kpis[ 'avg_page_download_time' ] = array('text' => 'Page Download Time', 'skip_icon' => false );
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
    <?php 
        foreach ($kpis as $kpKey => $kpValUser) { 
            $kpVal = $kpValUser[ 'text' ];
            $kpIcon = $kpValUser[ 'skip_icon' ];
    ?>
        <!-- Column -->
        <div class="col-lg-3 col-md-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> 
                        <img src="<?= base_url( 'img/social/analytic-on.png' ) ?>" width="20px" height="20px"> <?= $kpVal; ?></h4>
                    <div class="text-left m-l-10">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 kpiText">
                                <h2 class="font-light m-b-0"><?= $currMonthData[ $kpKey ] ?></h2>
                                <?php if( !$kpIcon ){ ?>
                                    <small><i class="<?= $statClasses[ $kpKey ][ 'class' ]; ?>"></i> <?= $statClasses[ $kpKey ][ 'inc' ]; ?></small>
                                <?php } else {
                                    echo '<br/>';
                                }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Column -->
    <?php } ?>
</div>
<!-- Row -->    
<?php
}
?>