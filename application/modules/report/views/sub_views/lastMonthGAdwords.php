<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">YEAR-TO-DATE TABLE</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Clicks</th>
                            <th>Impressions</th>
                            <th>CTR</th>
                            <th>Avg CPC</th>
                            <th>Cost</th>
                            <th>Avg Position</th>
                            <th>Conversion</th>
                            <th>Cost / Conv.</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                $dataHtml = '';
                                foreach( $ga_data as $data ){
                                    $data[ 'clicks' ] = number_format( $data[ 'clicks' ] );
                                    $data[ 'impressions' ] = number_format( $data[ 'impressions' ] );
                                    $data[ 'cost' ] =  number_format( $data[ 'cost' ] ? ( $data[ 'cost' ] / 1000000) : 0, 2 ); 
                                    $data[ 'avg_cpc' ] = number_format( $data[ 'avg_cpc' ] ? ( $data[ 'avg_cpc' ] / 1000000) : 0, 2 ); 
                                    $data[ 'cost_per_conversion' ] = number_format( $data[ 'cost_per_conversion' ] 
                                    ? ( $data[ 'cost_per_conversion' ] / 1000000) : 0, 2 );
                                    $data[ 'month_ref' ] = date("F Y", strtotime( $data[ 'month_ref' ].'-01' ));
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'month_ref' ].'</td>
                                                    <td>'.$data[ 'clicks' ].'</td>
                                                    <td>'.$data[ 'impressions' ].'</td>
                                                    <td>'.$data[ 'ctr' ].'</td>
                                                    <td>$'.$data[ 'avg_cpc' ].'</td>
                                                    <td>$'.$data[ 'cost' ].'</td>
                                                    <td>'.$data[ 'avg_position' ].' </td>
                                                    <td>'.$data[ 'conversion' ].'% </td>
                                                    <td>$'.$data[ 'cost_per_conversion' ].'</td>
                                                </tr>';
                                }
                                echo $dataHtml;
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div>