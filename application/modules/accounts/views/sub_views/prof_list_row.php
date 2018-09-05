<?php
$hasReports = false;
$attr = array();
$attr['width'] = '19px';
$attr['height'] = '19px';
$attr['class'] = 'm-l-5';
$attr['title'] = $attr['alt'] = 'Google My Business';
$gmbIcon = img(base_url('img/social/gmb-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Google Adwords';
$ppcIcon = img(base_url('img/social/adwords-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Google Analytics';
$seoIcon = img(base_url('img/social/analytic-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Trello';
$trelloIcon = img(base_url('img/social/trello-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Rankings';
$rankIcon = img(base_url('img/social/rankinity-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Admin';
$adminIcon = img(base_url('img/social/admin-on.png'), false, $attr);
$attr['title'] = $attr['alt'] = 'Google Search Console';
$webMIcon = img(base_url('img/social/search-console-on.png'), false, $attr);
$icon = array();
$icon['width'] = '19px';
$icon['height'] = '19px';
$icon['class'] = 'm-l-5';
$profIcon = anchor( base_url('accounts/editProfileUrl/' . $account['id']), 
	'<i class="mdi mdi-settings"></i>', $icon );

if (!$account['analytic_refresh_token'] || $account['analytic_reset_token']) {
	$aattr = array();
	$attr['title'] = $attr['alt'] = 'Google Analytics';
	$seoIcon = anchor(base_url('social/google/analytic/' . $account['id']),
		img(base_url('img/social/analytic-off.png'), false, $attr), $aattr);
}
if (!$account['gsc_refresh_token'] || $account['gsc_reset_token']) {
	$aattr = array();
	$attr['title'] = $attr['alt'] = 'Google Search Console';
	$webMIcon = anchor(base_url('social/google/webmaster/' . $account['id']),
		img(base_url('img/social/search-console-off.png'), false, $attr), $aattr);
}
if (!$account['adword_refresh_token'] || $account['adword_reset_token']) {
	$aattr = array();
	$aattr['class'] = "google-ad-link";
	if ($account['adword_customer_id']) {
		$aattr["data-cid"] = $account['adword_customer_id'];
	}
	$attr['title'] = $attr['alt'] = 'Google Adwords';
	$ppcIcon = anchor(base_url('social/google/adwords/' . $account['id']),
		img(base_url('img/social/adwords-off.png'), false, $attr), $aattr);
}
if (!$account['gmb_refresh_token'] || $account['gmb_reset_token']) {
	$attr['title'] = $attr['alt'] = 'Google My Business';
	$gmbIcon = anchor(base_url('social/google/mbusiness/' . $account['id']),
		img(base_url('img/social/gmb-off.png'), false, $attr));
}
if (!$account['trello_access_token']) {
	$attr['title'] = $attr['alt'] = 'Trello';
	$attr['class'] = 'm-l-5 trelloAuth';
	$attr['data-id'] = $account['id'];
	$attr['style'] = " cursor:pointer; ";
	$trelloIcon = img(base_url('img/social/trello-off.png'), false, $attr);
	$attr['class'] = 'm-l-5';
	unset($attr['data-id']);
	unset($attr['style']);
}
if (!$account['rankinity_access_token']) {
	$attr['title'] = $attr['alt'] = 'Rankings';
	$aattr = array();
	$aattr['class'] = "rankAuth";
	$rankIcon = anchor(base_url('social/link_rankinity/' . $account['id']),
		img(base_url('img/social/rankinity-off.png'), false, $attr), $aattr);
}
if (!$account['linked_account_id']) {
	$aattr = array();
	$attr['title'] = $attr['alt'] = 'Admin';
	$adminIcon = anchor(base_url('social/link_account_admin/' . $account['id']),
		img(base_url('img/social/admin-off.png'), false, $attr), $aattr);
}

$profHtml = '';
$iconsHtml = $seoIcon . $ppcIcon . $gmbIcon . $webMIcon . $trelloIcon . $rankIcon . $adminIcon. $profIcon;
$rHtml = $viewAll = $seoHtml = $ppcHtml = $localHtml = $ccHtml = $trHtml = $webMHtml = 'N/A';
// $services = explode(',', $account[ 'services' ]);
// $services = array_unique($services);
$seoHtml = 'N/A';
if ($account['analytic_refresh_token']) {
	$seoHtml = '<a href="' . base_url('report/link_analytic/' . $account['id']) . '"
	                class="btn btn-primary m-t-10">Link</a>';
	if ($account['analytic_reset_token']) {
		$seoHtml = '<a href="' . base_url('report/link_analytic/' . $account['id']) . '"
		                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >Link</a>';
	}
}
if ($account['view_id']) {
	$hasReports = true;
	$seoHtml = '<a href=' . base_url('report/fetchedAnalytic/' . $account['id'])
		. ' class="btn btn-info m-t-10">View</a>';
	if ($account['analytic_reset_token']) {
		$seoHtml = '<a href=' . base_url('report/fetchedAnalytic/' . $account['id'])
			. ' class="btn btn-primary disabled m-t-10" aria-disabled="true"  >View</a>';
	}
}

if ($account['adword_customer_id']) {
	$ppcHtml = '<a href="' . base_url('social/link_adwords/' . $account['id']) . '"
                    class="btn btn-primary m-t-10">Link</a>';
	if ($account['adword_reset_token']) {
		$ppcHtml = '<a href="' . base_url('social/link_adwords/' . $account['id']) . '"
	                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >Link</a>';
	}
	if ($account['linked_adwords_acc_id']) {
		$hasReports = true;
		$ppcHtml = '<a href="' . base_url('report/fetchedAdwords/' . $account['id']) . '"
                        class="btn btn-info m-t-10">View</a>';
		if ($account['adword_reset_token']) {
			$ppcHtml = '<a href="' . base_url('report/fetchedAdwords/' . $account['id']) . '"
		                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >Link</a>';
		}
	}
}

if ($account['trello_access_token']) {	
	$trHtml = '<a href="' . base_url('social/link_trello/' . $account['id']) . '"
                    class="btn btn-primary m-t-10">Link</a>';	
	if ($account['linked_trello_board_id']) {
		$hasReports = true;
		$trHtml = '<a href="' . base_url('report/tboardreport/' . $account['id']) . '"
	                    class="btn btn-info m-t-10">View</a>';
	}
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

if ($account['linked_account_id']) {
	$hasReports = true;
	$ccHtml = '<a href="' . base_url('report/citation_and_content/' . $account['id']) . '"
                    class="btn btn-info m-t-10">View</a>';
}

if ($account['gmb_refresh_token']) {
	$localHtml = '<a href="' . base_url('social/link_gbusiness/' . $account['id']) . '"
                    class="btn btn-primary  m-t-10">Link</a>';
	if ($account['gmb_reset_token']) {
		$localHtml = '<a href="' . base_url('social/link_gbusiness/' . $account['id']) . '"
	                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >Link</a>';
	}
	if ($account['linked_google_page_location']) {
		$hasReports = true;
		$localHtml = '<a href="' . base_url('report/fetchedGMB/' . $account['id']) . '"
                        class="btn btn-info m-t-10" >View</a>';
		if ($account['gmb_reset_token']) {
			$localHtml = '<a href="' . base_url('report/fetchedGMB/' . $account['id']) . '"
		                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >View</a>';
		}
	}
}
$reloginGoogle = "";
if ($account['gmb_reset_token'] || $account['analytic_reset_token'] || $account['adword_reset_token']) {
	$reloginGoogle = "Google token expired, please relogin";
}

if ($account['gsc_refresh_token']) {
	$webMHtml = '<a href="' . base_url('social/link_webmaster/' . $account['id']) . '"
	                class="btn btn-primary m-t-10">Link</a>';
	if ($account['gsc_reset_token']) {
		$webMHtml = '<a href="' . base_url('social/link_webmaster/' . $account['id']) . '"
		                class="btn btn-primary disabled m-t-10" aria-disabled="true"  >Link</a>';
	}
}
if ($account['linked_webmaster_site']) {
	$hasReports = true;
	$webMHtml = '<a href=' . base_url('report/fetchedWebmaster/' . $account['id'])
		. ' class="btn btn-info m-t-10">View</a>';
	if ($account['gsc_reset_token']) {
		$webMHtml = '<a href=' . base_url('report/fetchedWebmaster/' . $account['id'])
			. ' class="btn btn-primary disabled m-t-10" aria-disabled="true"  >View</a>';
	}
}
$viewAll = '<a href=' . base_url('report/complete_full/' . $account['id'])
	. ' class="btn btn-info m-t-10">View</a>';
if ($viewAll) {

}
?>
<tr>
    <td>
        <?=$account['account_url'];?><br/>
        <?=$iconsHtml . '<br/>';?>
        <?php
if ($reloginGoogle) {
	echo '<small>' . $reloginGoogle . '</small>';
}
?>
    </td>
    <td><?=$seoHtml;?></td>
    <td><?=$ppcHtml;?></td>
    <td><?=$localHtml;?></td>
    <td><?=$trHtml;?></td>
    <td><?=$rHtml;?></td>
    <td><?=$webMHtml;?></td>
    <td><?=$ccHtml;?></td>
    <td> <a href="<?=base_url('report/overview/' . $account['id'])?>"
    	class="btn btn-info m-t-10"
    	>View</a></td>
    <td><?=$viewAll;?></td>
</tr>