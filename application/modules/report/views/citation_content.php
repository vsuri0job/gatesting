<div class="row">
    <!-- column -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Month Ref</th>
                                <th>Content Count</th>
                                <th>Citation Count</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Month Ref</th>
                                <th>Content Count</th>
                                <th>Citation Count</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        	<?php
                        		$ccHtml = '';
                        		foreach ($cc_counts as $cc) {
                        			$contAttr = ' class="btn btn-block btn-info" style="width:25%" ';
                        			$citAttr = ' class="btn btn-block btn-info" style="width:25%" ';
                        			if( !$cc[ 'contents' ] ){
                        				$contAttr = ' class="btn btn-block btn-info disabled" style="width:25%" aria-disabled="true" ';
                        			}
                        			if( !$cc[ 'citation' ] ){
                        				$citAttr = ' class="btn btn-block btn-info disabled" style="width:25%" aria-disabled="true" ';	
                        			}
                                    $contUrl = base_url('report/contentView/'.$cc[ 'stamp' ]);
                                    if( $prof_id ){
                                        $contUrl = base_url('report/contentView/'.$cc[ 'stamp' ].'/'.$prof_id);
                                    }
                                    $citUrl = base_url('report/citationView/'.$cc[ 'stamp' ]);
                                    if( $prof_id ){
                                        $citUrl = base_url('report/citationView/'.$cc[ 'stamp' ].'/'.$prof_id);
                                    }
                    				$ccHtml .= '<tr>
                    								<td>'.$cc[ 'month' ].'</td>
                    								<td>'.anchor( $contUrl, 
                										$cc[ 'contents' ], 
                										$contAttr )
                    								.'</td>
                    								<td>'.anchor( $citUrl, 
                										$cc[ 'citation' ], 
                										$citAttr )
                    								.'</td>
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