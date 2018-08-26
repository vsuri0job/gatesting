<?php
$hasReports = false;
$attr = array();
$attr['width'] = '25px';
$attr['height'] = '25px';
$attr['class'] = 'm-l-2';
$gmbIcon = img(base_url('img/social/gmb-on.png'), false, $attr);
$ppcIcon = img(base_url('img/social/adwords-on.png'), false, $attr);
$seoIcon = img(base_url('img/social/analytic-on.png'), false, $attr);
$trelloIcon = img(base_url('img/social/trello-on.png'), false, $attr);
$rankIcon = img(base_url('img/social/rankinity-on.png'), false, $attr);
if (!$account['anlytic_refresh_token']) {
	$seoIcon = anchor(base_url('social/google/analytic/' . $account['id']),
		img(base_url('img/social/analytic-off.png'), false, $attr));
}
if (!$account['adword_refresh_token']) {
	$aattr = array();
	$aattr['class'] = "google-ad-link";
	$ppcIcon = anchor(base_url('social/google/adwords/' . $account['id']),
		img(base_url('img/social/adwords-off.png'), false, $attr), $aattr);
}
if (!$account['gmb_refresh_token']) {
	$gmbIcon = anchor(base_url('social/google/mbusiness/' . $account['id']),
		img(base_url('img/social/gmb-off.png'), false, $attr));
}
if (!$account['trello_access_token']) {
	$attr['class'] = 'm-l-2 trelloAuth';
	$attr['data-id'] = $account['id'];
	$attr['style'] = " cursor:pointer; ";
	$trelloIcon = img(base_url('img/social/trello-off.png'), false, $attr);
	$attr['class'] = 'm-l-2';
	unset($attr['data-id']);
	unset($attr['style']);
}
if (!$account['rankinity_access_token']) {
	// $attr[ 'class' ] = 'm-l-2 rankAuth';
	// $attr[ 'data-id' ] = $account[ 'id' ];
	// $attr[ 'style' ] = " cursor:pointer; ";
	// $rankIcon = img( base_url( 'img/social/rankinity-off.png' ), false, $attr);
	// $attr[ 'class' ] = 'm-l-2';
	// unset( $attr[ 'data-id' ] );
	// unset( $attr[ 'style' ] );

	$aattr = array();
	$aattr['class'] = "rankAuth";
	$rankIcon = anchor(base_url('social/link_rankinity/' . $account['id']),
		img(base_url('img/social/rankinity-off.png'), false, $attr), $aattr);
}
$iconsHtml = $seoIcon . $ppcIcon . $gmbIcon . $trelloIcon . $rankIcon;
$rHtml = $viewAll = $seoHtml = $ppcHtml = $localHtml = $ccHtml = $trHtml = 'N/A';
// $services = explode(',', $account[ 'services' ]);
// $services = array_unique($services);
$seoHtml = '<a href="' . base_url('report/link_analytic/' . $account['id']) . '"
                class="btn btn-primary  m-t-10">Link</a>';
if ($account['view_id']) {
	$hasReports = true;
	$seoHtml = '<a href=' . base_url('report/fetchedAnalytic/' . $account['id'])
		. ' class="btn btn-info m-t-10">View</a>';
}

if ($account['adword_customer_id']) {
	$ppcHtml = '<a href="' . base_url('social/link_adwords/' . $account['id']) . '"
                    class="btn btn-primary m-t-10">Link</a>';
	if ($account['linked_adwords_acc_id']) {
		$hasReports = true;
		$ppcHtml = '<a href="' . base_url('report/fetchedAdwords/' . $account['id']) . '"
                        class="btn btn-info m-t-10">View</a>';
	}
}

if ($account['trello_access_token']) {
	$hasReports = true;
	$trHtml = '<a href="' . base_url('report/tboardreport/' . $account['id']) . '"
                    class="btn btn-info m-t-10">View</a>';
}

if ($account['rankinity_access_token']) {
	$rHtml = '<a href="' . base_url('social/link_rankinity_project/' . $account['id']) . '"
                    class="btn btn-primary  m-t-10">Link</a>';
	if ($account['linked_rankinity_id']) {
		$hasReports = true;
		$rHtml = '<a href="' . base_url('report/rankinityProf/' . $account['id']) . '"
                        class="btn btn-info m-t-10">View</a>';
	}
}

// |
//                 <a href='. base_url( 'report/viewFetchedRankinity/'.$account[ 'rankinity_ref' ] )
//                . ' class="btn btn-info m-t-10">View</a>
// $rHtml = '';
// if( $account[ 'rankinity_name' ] ){
//     $rHtml = '<h5 class="m-t-10">Rankinity Project Name</h5>';
//     $rHtml .= $account[ 'rankinity_name' ];
// }
// $localHtml = '<a href="#" aria-disabled="true"
//             class="btn btn-primary disabled m-t-10">Link</a>';
// if( in_array('Local SEO', $services) && $account[ 'linked_google_page_location' ] ){
// $localHtml = '<a href="#" aria-disabled="true"
//             class="btn btn-primary disabled m-t-10">View</a>';
// }
// $viewAll = '<a href='. base_url( 'report/fetchedPPC/'.$account[ 'id' ] ).
// 'aria-disabled="true" class="btn btn-primary disabled m-t-10">View</a>';
// $ccHtml = '<a href="'.base_url( 'report/citation_and_content/'.$account[ 'linked_account_id' ] ).'
//     " class="btn btn-info m-t-10" role="button" >View</a> ';
// if( !$hasReports ){
//     $ccHtml = $seoHtml = $ppcHtml = $localHtml = $viewAll = 'N/A';
// }
?>
<tr>
    <td>
        <?=$account['account_url'];?><br/>
        <?=$iconsHtml;?>
    </td>
    <td><?=$viewAll;?></td>
    <td><?=$seoHtml;?></td>
    <td><?=$ppcHtml;?></td>
    <td><?=$localHtml;?></td>
    <td><?=$trHtml;?></td>
    <td><?=$rHtml;?></td>
    <td><?=$ccHtml;?></td>
</tr>