<?php
list( $firMonthDate, $secMonthDate) = com_lastMonths( 2, "", 1, 1 );
$firMonthData = date('Y-m', strtotime( $firMonthDate ));
$secMonthData = date('Y-m', strtotime( $secMonthDate ));
$secMonthData = com_arrIndex($ga_data, $secMonthData);
$firMonthData = com_arrIndex($ga_data, $firMonthData);
if( $firMonthData &&  $secMonthData){
$statClasses = array();
foreach( $firMonthData as $key => $val ){
    $val = floatval($val);
    if( $key <> 'month_ref' ){
        $statClasses[ $key ][ 'class' ] = 'ti-arrow-up text-success';
        $lMdata = com_arrIndex($secMonthData, $key, 0);
        $val = str_replace("%", "", $val);
        $val = str_replace("$", "", $val);
        $lMdata = str_replace("%", "", $lMdata);
        $lMdata = str_replace("$", "", $lMdata);
        $statClasses[ $key ][ 'inc' ] = 0;
        if(  $val == $lMdata ){
            $statClasses[ $key ][ 'class' ] = 'ti-arrow-up text-info';
        } elseif( $val < $lMdata && $val && $lMdata ) {
            $statClasses[ $key ][ 'inc' ] = '-'.number_format((($lMdata - $val)/$val) * 100, 2).' %';
            $statClasses[ $key ][ 'class' ] = 'ti-arrow-down text-danger';
        } elseif( ($val > $lMdata) && intval( $val ) && intval( $lMdata )  ) {
            $statClasses[ $key ][ 'inc' ] = number_format((($val-$lMdata)/$lMdata) * 100, 2).' %';;
        }
    }
}
if( $firMonthData[ 'cost' ] ){
    $firMonthData[ 'cost' ] = '$'.number_format( $firMonthData[ 'cost' ] / 1000000, 2);
}
if( $firMonthData[ 'avg_cpc' ] ){
    $firMonthData[ 'avg_cpc' ] = '$'.number_format( $firMonthData[ 'avg_cpc' ] / 1000000, 2);
}
if( $firMonthData[ 'cost_per_conversion' ] ){
    $firMonthData[ 'cost_per_conversion' ] = '$'.number_format( $firMonthData[ 'cost_per_conversion' ] / 1000000, 2);
}
$kpis = array();
$kpis[ 'clicks' ] = array('text' => 'Clicks', 'skip_icon' => false );
$kpis[ 'impressions' ] = array('text' => 'Impressions', 'skip_icon' => false );
$kpis[ 'ctr' ] = array('text' => 'CTR', 'skip_icon' => false );
$kpis[ 'avg_cpc' ] =  array('text' => 'AVG. CPC', 'skip_icon' => true );
$kpis[ 'cost' ] =  array('text' => 'COST', 'skip_icon' => true );
$kpis[ 'conversion' ] = array('text' => 'CONV.', 'skip_icon' => false );
$kpis[ 'cost_per_conversion' ] = array('text' => 'COST / CONV.', 'skip_icon' => false );
$kpis[ 'avg_position' ] = array('text' => 'AVG. POSITION', 'skip_icon' => false );
// $kpis[ 'phone_calls' ] = array('text' => 'PHONE CALLS', 'skip_icon' => false );
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
                        <img src="<?= base_url( 'img/social/adwords-on.png' ) ?>" width="20px" height="20px"> <?= $kpVal; ?></h4>
                    <div class="text-left m-l-10">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 kpiText">
                                <h2 class="font-light m-b-0"><?= $firMonthData[ $kpKey ] ?></h2>
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
<?php
}
?>