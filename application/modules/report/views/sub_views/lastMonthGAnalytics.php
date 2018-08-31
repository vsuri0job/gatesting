<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Total Traffic - Month over Month</h4>            
            <div class="table-responsive">
                <table class="table" id="total-traffic">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>User</th>
                            <th>Session</th>
                            <th>Bounce Rate</th>
                            <th>Avg Session Duration</th>
                            <th>Page/Session</th>
                            <th>Goal Completions</th>
                            <th>Goal Conversion Rate</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                                $dataHtml = '';
                                foreach( $ga_data as $data ){
                                    $month = date('F Y', strtotime( $data[ 'month_ref' ].'-01' ));
                                    $dataHtml .= '<tr>
                                                    <td>'.$month.'</td>
                                                    <td>'.$data[ 'users' ].'</td>
                                                    <td>'.$data[ 'sessions' ].'</td>
                                                    <td>'.number_format( $data[ 'bounce_rate' ], 2, '.', '' ).'% </td>
                                                    <td>'.gmdate("H:i:s", $data[ 'avg_session_duration' ] ).' </td>
                                                    <td>'.number_format( $data[ 'page_view_per_sessions' ], 2, '.', '' ).'</td>
                                                    <td>'.$data[ 'goal_completion_all' ].'</td>
                                                    <td>'.number_format( $data[ 'goal_conversion_rate' ], 2, '.', '' ).'% </td>
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
    
<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Organic Traffic - Month over Month</h4>
            <div class="table-responsive">                
                <table class="table" id="organic-traffic">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>User</th>
                            <th>Session</th>
                            <th>Bounce Rate</th>
                            <th>Avg Session Duration</th>
                            <th>Pages / session</th>
                            <th>Goal Completions</th>
                            <th>Goal Conversion Rate</th>
                        </tr>                        
                    </thead>
                    <tbody>
                            <?php
                                $dataHtml = '';                                
                                foreach( $ga_data_organic as $data ){
                                    $month = date('F Y', strtotime( $data[ 'month_ref' ].'-01' ));
                                    $avSessDur = gmdate("H:i:s", $data[ 'avg_session_duration' ] );
                                    $dataHtml .= '<tr>
                                                    <td>'.$month.'</td>
                                                    <td>'.$data[ 'users' ].'</td>
                                                    <td>'.$data[ 'sessions' ].'</td>
                                                    <td>'.number_format( $data[ 'bounce_rate' ], 2, '.', '').'% </td>
                                                    <td>'.$avSessDur.' </td>
                                                    <td>'.number_format( $data[ 'page_view_per_sessions' ], 2, '.', '').'</td>
                                                    <td>'.$data[ 'goal_completion_all' ].'</td>
                                                    <td>'.number_format( $data[ 'goal_conversion_rate' ], 2, '.', '' ).'% </td>
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

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">MEDIUM PERFORMANCE</h4>
            <div class="table-responsive">                
                <table class="table" id="medium-traffic">
                    <thead>
                        <tr>
                            <th>Medium</th>
                            <th>User</th>
                            <th>sessions</th>
                            <th>Bounce Rate</th>
                            <th>Avg Session Duration</th>
                            <th>Pages / session</th>
                            <th>Goal Compeltions</th>
                            <th>Goal Conversion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                $dataHtml = '';                                
                                foreach( $ga_data_medium as $data ){
                                    $avSessDur = gmdate("H:i:s", $data[ 'avg_session_duration' ] );
                                    $data[ 'bounce_rate' ] = number_format( $data[ 'bounce_rate' ], 2, '.', '');
                                    $data[ 'per_new_sessions' ] = number_format( $data[ 'per_new_sessions' ], 2, '.', '');
                                    // $data[ 'avg_session_duration' ] = number_format( $data[ 'avg_session_duration' ], 2, '.', '');
                                    $data[ 'page_view_per_sessions' ] = number_format( $data[ 'page_view_per_sessions' ], 2, '.', '');
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'medium' ].'</td>
                                                    <td>'.$data[ 'users' ].'</td>
                                                    <td>'.$data[ 'sessions' ].'</td>
                                                    <td>'.$data[ 'bounce_rate' ].'</td>
                                                    <td>'.$avSessDur.'</td>
                                                    <td>'.$data[ 'page_view_per_sessions' ].'</td>
                                                    <td>'.$data[ 'goal_completion_all' ].'</td>
                                                    <td>'.number_format( $data[ 'goal_conversion_rate' ], 2, '.', '' ).'% </td>
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

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">SOURCE / MEDIUM PERFORMANCE</h4>
            <div class="table-responsive">                
                <table class="table" id="source-traffic">
                    <thead>
                        <tr>
                            <th>Source / Medium</th>
                            <th>User</th>
                            <th>sessions</th>
                            <th>Bounce Rate</th>
                            <th>Avg Session Duration</th>
                            <th>Pages / session</th>
                            <th>Goal Compeltions</th>
                            <th>Goal Conversion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                $dataHtml = '';                                
                                foreach( $ga_data_source_medium as $data ){
                                    $avSessDur = gmdate("H:i:s", $data[ 'avg_session_duration' ] );
                                    $data[ 'bounce_rate' ] = number_format( $data[ 'bounce_rate' ], 2, '.', '');
                                    $data[ 'per_new_sessions' ] = number_format( $data[ 'per_new_sessions' ], 2, '.', '');
                                    // $data[ 'avg_session_duration' ] = number_format( $data[ 'avg_session_duration' ], 2, '.', '');
                                    $data[ 'page_view_per_sessions' ] = number_format( $data[ 'page_view_per_sessions' ], 2, '.', '');
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'source_medium' ].'</td>
                                                    <td>'.$data[ 'users' ].'</td>
                                                    <td>'.$data[ 'sessions' ].'</td>
                                                    <td>'.$data[ 'bounce_rate' ].'</td>
                                                    <td>'.$avSessDur.'</td>
                                                    <td>'.$data[ 'page_view_per_sessions' ].'</td>
                                                    <td>'.$data[ 'goal_completion_all' ].'</td>
                                                    <td>'.number_format( $data[ 'goal_conversion_rate' ], 2, '.', '' ).'% </td>
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

<div class="">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">LANDING PAGE PERFORMANCE</h4>
            <div class="table-responsive">                
                <table class="table" id="landing-traffic">                    
                    <thead>
                        <tr>
                            <th>landing page path</th>
                            <th>New User</th>
                            <th>Users</th>
                            <th>% New Sessions</th>
                            <th>Sessions</th>
                            <th>Bounce Rate</th>
                            <th>Avg. Session Duration</th>
                            <th>Page / Session</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                                $dataHtml = '';                                
                                foreach( $ga_data_landing_page as $data ){
                                    $avSessDur = gmdate("H:i:s", $data[ 'avg_session_duration' ] );
                                    $data[ 'bounce_rate' ] = number_format( $data[ 'bounce_rate' ], 2, '.', '');
                                    $data[ 'per_new_sessions' ] = number_format( $data[ 'per_new_sessions' ], 2, '.', '');
                                    // $data[ 'avg_session_duration' ] = number_format( $data[ 'avg_session_duration' ], 2, '.', '');
                                    $data[ 'page_view_per_sessions' ] = number_format( $data[ 'page_view_per_sessions' ], 2, '.', '');
                                    $data[ 'landing_page' ] = substr($data[ 'landing_page' ], 0, 100);
                                    $dataHtml .= '<tr>
                                                    <td>'.$data[ 'landing_page' ].'</td>
                                                    <td>'.$data[ 'new_users' ].'</td>
                                                    <td>'.$data[ 'users' ].'</td>
                                                    <td>'.$data[ 'per_new_sessions' ].'</td>
                                                    <td>'.$data[ 'sessions' ].'</td>
                                                    <td>'.$data[ 'bounce_rate' ].'</td>
                                                    <td>'.$avSessDur.'</td>
                                                    <td>'.$data[ 'page_view_per_sessions' ].' </td>
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