<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-8 align-self-center">
                        <h4 class="card-title">Users accounts</h4>
                    </div>
                    <div class="col-md-6 col-4 align-self-center">
                        <a href="<?= base_url( 'accounts/addProfileUrl' ) ?>" class="btn pull-right btn-info">Add Account</a>
                    </div>
                </div>
                <div class="table-responsive m-t-40">
                    <table id="accounts" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Full Report</th>
                                <th>SEO/Rankinity</th>
                                <th>PPC</th>
                                <th>Local</th>
                                <th>Content/Citations</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Account</th>
                                <th>Full Report</th>
                                <th>SEO/Rankinity</th>
                                <th>PPC</th>
                                <th>Local</th>
                                <th>Content/Citations</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach( $accounts as $account){
                            $hasReports = false;
                            if( $account[ 'linked_account_id' ] ){
                                $services = explode(',', $account[ 'services' ]);
                                $services = array_unique($services);
                                $seoHtml = '<a href="#" aria-disabled="true" 
                                            class="btn btn-primary disabled m-t-10">Link GA & Rankings</a>';
                                if( in_array('SEO', $services) ){
                                    $hasReports = true;
                                    $seoHtml = '<a href='. base_url( 'report/fetchedAnalytic/'.$account[ 'id' ] )
                                            . ' class="btn btn-info m-t-10">View</a> |
                                             <a href='. base_url( 'report/viewFetchedRankinity/'.$account[ 'rankinity_ref' ] )
                                            . ' class="btn btn-info m-t-10">View</a>';
                                    if( !$account[ 'rankinity_ref' ] ){
                                        $seoHtml = '<a href='. base_url( 'report/fetchedAnalytic/'.$account[ 'id' ] )
                                            . ' class="btn btn-info m-t-10">View</a> | 
                                            <a href="'.base_url( 'social/link_rankinity/'.$account[ 'id' ] )
                                            .'" class="btn btn-primary m-t-10">Link</a>';
                                    }
                                }
                                $rHtml = '';
                                if( $account[ 'rankinity_name' ] ){
                                    $rHtml = '<h5 class="m-t-10">Rankinity Project Name</h5>';
                                    $rHtml .= $account[ 'rankinity_name' ];
                                }
                                $ppcHtml = '<a href="'.base_url( 'social/link_adwords/'.$account[ 'id' ] ).'"
                                            class="btn btn-primary m-t-10">Link</a>';
                                if( in_array('PPC', $services) && $account[ 'linked_adwords_acc_id' ] ){
                                    $hasReports = true;                                    
                                    $ppcHtml = '<a href="'. base_url( 'report/fetchedAdwords/'.$account[ 'id' ] ).'" 
                                                class="btn btn-info m-t-10">View</a>';
                                }
                                $localHtml = '<a href="#" aria-disabled="true" 
                                            class="btn btn-primary disabled m-t-10">Link</a>';
                                if( in_array('Local SEO', $services) && $account[ 'linked_google_page_location' ] ){
                                $localHtml = '<a href="#" aria-disabled="true" 
                                            class="btn btn-primary disabled m-t-10">View</a>';
                                }
                                $viewAll = '<a href='. base_url( 'report/fetchedPPC/'.$account[ 'id' ] ).
                                'aria-disabled="true" class="btn btn-primary disabled m-t-10">View</a>';
                                $ccHtml = '<a href="'.base_url( 'report/citation_and_content/'.$account[ 'linked_account_id' ] ).'
                                    " class="btn btn-info m-t-10" role="button" >View</a> ';
                                if( !$hasReports ){                                    
                                    $ccHtml = $seoHtml = $ppcHtml = $localHtml = $viewAll = 'N/A';
                                }
                        ?>
                            <tr>
                                <td>
                                    <?= $account[ 'account_name' ]; ?><br/>
                                    <?= $account[ 'property_website_url' ]; ?><br/>                                    
                                    <?= $rHtml; ?><br/>
                                </td>
                                <td><?= $viewAll; ?></td>
                                <td><?= $seoHtml; ?></td>
                                <td><?= $ppcHtml; ?></td>
                                <td><?= $localHtml; ?></td>
                                <td><?= $ccHtml; ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6">
                                    Profile: <?= $account[ 'profile_name' ]; ?><br/>
                                    Property: <?= $account[ 'property_name' ]; ?><br/>
                                    View: <?= $account[ 'view_name' ]; ?><br/>
                                    <span data-aid="<?= $account[ 'id' ]; ?>" 
                                        class="label label-info linkAccounts">Link Profile Manually </span>
                                </td>
                            </tr>
                        <?php   }
                        } 
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>
</div>
<div    id="link-account-modal" class="modal fade" 
        tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form name="link-account" id="link-analytic-profile">
            <div class="modal-header">                
                <h4 class="modal-title">Link Account</h4>
            </div>
            <div class="modal-body" id="link-account-modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger waves-effect waves-light">Link Account</button>
            </div>
            </form>
        </div>
    </div>
</div>