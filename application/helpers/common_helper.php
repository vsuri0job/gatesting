<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

if (!function_exists('com_e')) {
	function com_e($fld, $exit = true, $dump = false, $pprint = true) {
		if ($pprint) {
			echo '<pre>';
		}

		if (!$dump) {
			print_r($fld);
		} else {
			var_dump($fld);
		}
		if ($pprint) {
			echo '</pre>';
		}
		if ($exit) {
			die();
		}
	}
}

if (!function_exists('com_user_data')) {
	function com_user_data($fld = null) {
		$CI = &get_instance();
		if( is_null( $fld ) ){
			return $CI->session->userdata();
		} else {
			return $CI->session->userdata( $fld );
		}
	}
}

if (!function_exists('com_user_img')) {
	function com_user_img() {
		$CI = &get_instance();
		$img = $CI->session->userdata('user_image');
		if ($img) {
			return 'http://admin.51blocks.com/userimages/' . $img;
		}
		return base_url('assets/images/users/default.jpeg');
	}
}

if (!function_exists('com_update_session')) {
	function com_update_session() {
		$CI = &get_instance();
		$user_data = $CI->UserModel->getUserDetail(com_user_data('id'));
		$user_setting = $CI->UserModel->getUserSettingDetail(com_user_data('id'));
		$user_setting = com_makelist( $user_setting, 'setting_name', 'setting_det', false );		
		$user_data = array_merge($user_data, $user_setting);		
		$CI->session->set_userdata($user_data);
	}
}

if (!function_exists('com_get_user_data')) {
	function com_get_user_data( $user_id ) {
		$CI = &get_instance();
		$user_data = $CI->UserModel->getUserDetail($user_id);
		$user_setting = $CI->UserModel->getUserSettingDetail($user_id);
		$user_setting = com_makelist( $user_setting, 'setting_name', 'setting_det', false );		
		$user_data = array_merge($user_data, $user_setting);
		return $user_data;
	}
}

if (!function_exists('com_arrIndex')) {
	function com_arrIndex($stack, $ind, $def = '') {
		$ret = $def;
		if (isset($stack[$ind])) {
			$ret = $stack[$ind];
		}
		return $ret;
	}
}

if (!function_exists('com_lquery')) {
	function com_lquery($exit = 1) {
		$CI = &get_instance();
		com_e($CI->db->last_query(), $exit);
	}
}

if (!function_exists('com_formatSeconds')) {

	function com_formatSeconds($seconds) {
		return $seconds;
		$hours = 0;
		$milliseconds = str_replace("0.", '', $seconds - floor($seconds));

		if ($seconds > 3600) {
			$hours = floor($seconds / 3600);
		}
		$seconds = $seconds % 3600;

		return str_pad($hours, 2, '0', STR_PAD_LEFT)
		. gmdate(':i:s', $seconds)
			. ($milliseconds ? ".$milliseconds" : '')
		;
		// $str = '';
		// if( $hours ){
		// 	$str .= $hours.'h ';
		// }
		// $str .= gmdate( 'im s', $seconds ).'s ';
		// if( $milliseconds ){
		// 	$str .= $milliseconds.'ms ';
		// }
		// return $str;
	}
}

if (!function_exists('com_formatSeconds12')) {

	function com_formatSeconds12($seconds) {
		$dt1 = new DateTime("@0");
		$dt2 = new DateTime("@$seconds");
		return $dt1->diff($dt2)->format('%a days, %h hours, %i minutes and %s seconds');
	}
}

if (!function_exists('com_get_domain')) {
	function com_get_domain($url) {
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		return false;
	}
}

if (!function_exists('com_make2dArray')) {

	function com_make2dArray($rst, $key){
		$tmp = array();
		foreach( $rst as $data ){
			if( isset( $data[ $key ] ) ){
				$tmp[ $data[ $key ] ] = $data;
			}
		}
		return $tmp;
	}
}

if (!function_exists('com_makelist')) {
	function com_makelist($rstSet, $Key_index, $Key_text, $forSelect = true, 
		$defaultSel = 'Select', $selected = array(), $grpKey = '') {
		$result = array();
		if ($forSelect) {
			$result[] = $defaultSel;
		}
		$Key_text_st = explode(",", $Key_text);
		foreach ($rstSet as $rstSetRow) {
			$Key_text = array();
			if( is_array( $Key_text_st ) ){
				foreach ($Key_text_st as $kt_key) {
					if( isset( $rstSetRow[ $kt_key ] ) ){
						$Key_text[] = $rstSetRow[ $kt_key ];
					}
				}
			}			
			$new_stack_index = $rstSetRow[$Key_index];
			$new_stack_text = implode(" ", $Key_text);
			if( $grpKey && isset( $rstSetRow[$grpKey] ) ){
				if( !isset( $result[ $rstSetRow[$grpKey] ] ) ){
					$result[ $rstSetRow[$grpKey] ] = array();
				}
				if( $new_stack_text && $new_stack_index ){
					$result[ $rstSetRow[$grpKey] ][$new_stack_index] = ucfirst($new_stack_text);
				}
			} else {
				$result[$new_stack_index] = ucfirst($new_stack_text);
			}
		}
		return $result;
	}
}

/* return & build html from resultset */
if (!function_exists('com_makelistElem')) {
	function com_makelistElem($rstSet, $Key_index, $Key_text, $forSelect = true,
		$defaultSel = 'Select', $defaultSelVal = "", $optGrp = false, $optGrpK = '',
		$selected = array(), $nonAssociate = false, $disabledOpt = False) {
		$result = '';
		if ($forSelect) {
			$result .= '<option value="' . $defaultSelVal . '">' . $defaultSel . '</option>';
		}
		if ($nonAssociate) {
			foreach ($rstSet as $rstSetInd => $rstSetD) {
				$result .= '<option ' . ($disabledOpt ? 'disabled' : '') . ' value="'
				. $rstSetInd . '">' . ucfirst($rstSetD) . '</option>';
			}
		} else {
			$current_grp = '';
			$rst_count = count($rstSet) - 1;
			foreach ($rstSet as $ind => $rstSetRow) {
				if (!array_key_exists($Key_index, $rstSetRow)
					or !array_key_exists($Key_text, $rstSetRow)) {
					continue;
				}
				if ($optGrp && !empty($optGrpK) && $current_grp !== $rstSetRow[$optGrpK]) {
					$current_grp = $rstSetRow[$optGrpK];
					$result .= '<optgroup label="' . $rstSetRow[$optGrpK] . '">';
				}
				if ($rstSetRow[$Key_index]) {
					$result .= '<option ' . (in_array($rstSetRow[$Key_index], $selected) ? 'Selected' : '') . ($disabledOpt ? 'disabled' : '') . ' value="' . $rstSetRow[$Key_index] . '">' . ucfirst($rstSetRow[$Key_text]) . '</option>';
				}
				if ($optGrp && !empty($optGrpK) && ($rst_count == $ind || ($ind < $rst_count && $current_grp !== $rstSet[$ind][$optGrpK]))
				) {
					$result .= '</optgroup>';
				}
			}
		}
		return $result;
	}
}

if (!function_exists('com_makelistElemFromTable')) {
	function com_makelistElemFromTable($ci, $tbl, $key, $txt, $parent, $parentWhere = 0, $andWhere = array(), &$rstHtml) {
		if(  $andWhere ){
			$ci->db->where( $andWhere );
		}
		$rst = $ci->db->select(" @itmKey:=$tbl.$key as `$key`, $tbl.*,
			( SELECT count( $key ) FROM $tbl WHERE $parent = @itmKey) `childCount`", false)
			->from("$tbl")
			->where($parent, $parentWhere)
			->order_by($parent, 'desc')
			->order_by('childCount', 'desc')
			->order_by($txt, 'asc')
			->get()->result_array();
		// com_e($rst, 0);
		foreach ($rst as $rkey => $value) {
			if ($value['childCount']) {
				$value[$txt] = str_repeat(" ", $value['depth']) . $value[$txt];
				$value[$txt] .= str_repeat("&nbsp;", 10) . $value[$key];
				$rstHtml .= '<optgroup label="' . $value[$txt] . '">';
				com_makelistElemFromTable($ci, $tbl, $key, $txt, $parent, $value[$key], $andWhere, $rstHtml);
				$rstHtml .= '</optgroup>';
			} else {
				$value[$txt] .= str_repeat("&nbsp;", 10) . $value[$key];
				$rstHtml .= '<option value="' . $value[$key] . '">' . ucfirst($value[$txt]) . '</option>';
			}
		}
	}
}

if (!function_exists('com_compDate')) {
	function com_compDate($minDate, $maxDate) {
		$datetime1 = new DateTime( $minDate );
		$datetime2 = new DateTime( $maxDate );
		$interval = $datetime2->diff($datetime1);
		$out = false;
		if( $interval->y || $interval->m || $interval->d
			|| $interval->h || $interval->i || $interval->s){
			$out = true;
		}
		return $out;
	}
}

if (!function_exists('com_b64UrlEncode')) {
	function com_b64UrlEncode($data) {
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	}
}

if (!function_exists('com_b64UrlDecode')) {
	function com_b64UrlDecode($data) {
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	}
}

if (!function_exists('com_monthName')) {
	function com_monthName($month) {
		$months = array();
		$months[ '01' ] = 'January';
		$months[ '02' ] = 'February';
		$months[ '03' ] = 'March';
		$months[ '04' ] = 'April';
		$months[ '05' ] = 'May';
		$months[ '06' ] = 'June';
		$months[ '07' ] = 'July';
		$months[ '08' ] = 'August';
		$months[ '09' ] = 'September';
		$months[ '10' ] = 'October';
		$months[ '11' ] = 'November';
		$months[ '12' ] = 'December';
		$monthName = com_arrIndex( $months, $month, "");
		return $monthName;
	}
}

if (!function_exists('com_lastMonths')) {
	function com_lastMonths($limit, $stDate = '', $skipStamp = false, $skipStartDate = false) {
		/* $stDate = YYYY-MM-01 */
		if( !$stDate ){
			$stDate = date("Y-m-01", time());
		}
		$stExt = explode('-', $stDate);
		$stYear = $stExt[ 0 ];
		$stMonth = intval($stExt[ 1 ]);
		if( $skipStartDate ){
			$stMonth--;
		}
		$monthStamps = array();
		$msCount = $limit;
		while( $msCount ){
			for( ; $stMonth > 0 && $msCount; $stMonth--){
				$dateTime = $stYear.'-'.$stMonth.'-01';
				if( $stMonth < 10 ){
					$dateTime = $stYear.'-0'.$stMonth.'-01';
				}
				if( $skipStamp ){
					$monthStamps[] = $dateTime;
				} else {
					$monthStamps[ strtotime( $dateTime ) ] = $dateTime;
				}
				$msCount--;
			}
			$stYear--;
			$stMonth = 12;
		}
		return $monthStamps;
	}
}

if (!function_exists('com_initAnlData')) {
	function com_initAnlData() {
	    $data = array();
	    $data[ 'users' ] = 0;
	    $data[ 'sessions' ] = 0;
	    $data[ 'new_users' ] = 0;    
	    $data[ 'page_views' ] = 0;
	    $data[ 'bounce_rate' ] = 0;
	    $data[ 'per_new_sessions' ] = 0;
	    $data[ 'goal_completion_all' ] = 0;    
	    $data[ 'goal_conversion_rate' ] = 0;
	    $data[ 'avg_session_duration' ] = 0;
	    $data[ 'page_view_per_sessions' ] = 0;
	    $data[ 'avg_page_download_time' ] = 0;
	    return $data;
	}
}

if (!function_exists('com_initAdwData')) {
	function com_initAdwData() {
	    $data = array();
	    $data[ 'ctr' ] = 0;
	    $data[ 'cost' ] = 0;
	    $data[ 'clicks' ] = 0;
	    $data[ 'avg_cpc' ] = 0;    
	    $data[ 'avg_cost' ] = 0;
	    $data[ 'conversion' ] = 0;
	    $data[ 'impressions' ] = 0;
	    $data[ 'phone_calls' ] = 0;
	    $data[ 'avg_position' ] = 0;
	    $data[ 'cost_per_conversion' ] = 0;
	    return $data;
	}
}

if (!function_exists('com_initGMBData')) {
	function com_initGMBData() {
	    $data = array();
	    $data[ 'calls' ] = 0;
	    $data[ 'direc' ] = 0;
	    $data[ 'clicks' ] = 0;
	    return $data;
	}
}

if (!function_exists('com_compKPI')) {
	function com_compKPI($fVal, $sVal) {		
		$out = array();
		$out[ 'infotxt' ] = $fVal;
		$out[ 'difftxt' ] = '0';
		$out[ 'class' ] = 'ti-arrow-up text-info';
		$fVal = (float)$fVal;
		$sVal = (float)$sVal;
		if( $fVal && $sVal && ( $fVal < $sVal) ) {
			$out[ 'difftxt' ] = '-'.number_format((($sVal - $fVal)/$fVal) * 100, 2).' %';
			$out[ 'class' ] = 'ti-arrow-down text-danger';
        } elseif( $fVal && $sVal && ( $fVal > $sVal)   ) {
			$out[ 'difftxt' ] = number_format((($fVal - $sVal)/$sVal) * 100, 2).' %';			
        }
        return $out;
	}
}