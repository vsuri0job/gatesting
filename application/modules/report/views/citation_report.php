<div class="row">
    <!-- column -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">                
                <div class="table-responsive">
                    <h4 class="card-title">Citation Report For Month ( <?= $month ?> )</h4>
                    <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>
                    <table id="citation-tbl" class="display nowrap table table-hover table-striped table-bordered" 
                    cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Acc. Name</th>
                                <th>Directory</th>
                                <th>Login Url</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Live Link</th>
                                <th>Domain Authority</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Acc. Name</th>
                                <th>Directory</th>
                                <th>Login Url</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Live Link</th>
                                <th>Domain Authority</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        	<?php
                        		$ccHtml = '';
                        		foreach ($citations as $cc) {
                                    $cstatus = 'N/A';
                                    if( $cc[ 'citation_status' ] == 1 ){
                                        $cstatus = 'Submitted';
                                    } else if( $cc[ 'citation_status' ] == 2 ){
                                        $cstatus = 'Live';
                                    }

                    				$ccHtml .= '<tr>
                                                    <td>'.$cc[ 'account_name' ].'</td>
                    								<td>'.$cc[ 'directory' ].'</td>
                    								<td>'.$cc[ 'login_url' ].'</td>
                    								<td>'.$cc[ 'username' ].'</td>
                                                    <td>'.$cc[ 'password' ].'</td>
                                                    <td>'.$cc[ 'live_link' ].'</td>
                                                    <td>'.$cc[ 'domain_authority' ].'</td>
                                                    <td>'.$cstatus.'</td>
                                                    <td>'.$cc[ 'notes' ].'</td>
                    							</tr>';
      	                  		}
                        		echo $ccHtml;
                        	?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>