<?php
/**
 * Module: IPN (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- ipn is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Payments
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_ipn.php
 */


# Code to handle file being loaded by URL
	IF (eregi('ipn_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=ipn');
		exit();
	}

# Include language file (must be after parameter load to use them)
	include_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');


function do_log_display($adata, $aret_flag=0) {
	# Dim some Vars:
	global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
	$where = '';

	# Get account array
	$query = 'SELECT * FROM ' . $_DBCFG['table_prefix'].'ipn_log';
	IF ($adata['ipn_view'] == 'subscr') {$where .= " WHERE ipn_txn LIKE 'S-%'";}
	$order = ' ORDER BY ipn_ts DESC';

	# Build Page menu
	# Get count of rows total for pages menu:
	$result_ttl = '';
	$query_ttl = 'SELECT COUNT(*) FROM '.$_DBCFG['table_prefix'].'ipn_log'.$where;

	$result_ttl = $db_coin->db_query_execute($query_ttl);
	while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next	- is page loading first record number
	# $_rec_start	- is a given page start record (which will be rec_next)
	$_rec_page = $_CCFG['IPN_NUM_DISPLAY'];
	if ($_rec_page <= 0) {$_rec_page == 10;}
	$_rec_next = $adata['rec_next'];
	IF (!$_rec_next) {$_rec_next = 0;}

	# Range of records on current page
	$_rec_next_lo = $_rec_next+1;
	$_rec_next_hi = $_rec_next+$_rec_page;
	IF ($_rec_next_hi > $numrows_ttl) {$_rec_next_hi = $numrows_ttl;}

	# Calc no pages,
	$_num_pages = round(($numrows_ttl/$_rec_page), 0);
	IF ($_num_pages < ($numrows_ttl/$_rec_page)) {$_num_pages = $_num_pages+1;}

	# Loop Array and Print Out Page Menu HTML
	$_page_menu = $_LANG['_IPN']['l_Pages'].$_sp;
	for ($i = 1; $i <= $_num_pages; $i++) {
		$_rec_start = (($i*$_rec_page)-$_rec_page);
		IF ($_rec_start == $_rec_next) {
			# Loading Page start record so no link for this page.
			$_page_menu .= $i;
		} ELSE {
			$_page_menu .= '<a href="'.$_SERVER['PHP_SELF'].'?mod=ipn&mode=view&rec_next='.$_rec_start.'">'.$i.'</a>';
		}
		IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
	}
	# End page menu

	$query .= $where.$order." LIMIT $_rec_next, $_rec_page";
	$result	= $db_coin->db_query_execute($query);
	$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
	$_out .= '<div align="center">'.$_nl;
	$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="9">'.$_nl;

	$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
	$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl;
	$_out .= '<td class="TP0MED_NL">'.$_nl."<b>".$_LANG['_IPN']['IPN_LOG_TITLE']."</b>".$_nl;
	$_out .= '</tr>'.$_nl.'</table>'.$_nl;

	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
	$_out .= '<td class="TP3SML_NL">'.$_sp.'</td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['IPN_LOG_TS'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['IPN_LOG_TXN'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['Status'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['Type'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NR"><b>'.$_LANG['_IPN']['AmtSent'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NR"><b>'.$_LANG['_IPN']['AmtApplied'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['Name'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['IPN_LOG_ACTION'].'</b></td>'.$_nl;
	$_out .= '</tr>'.$_nl;

	# Process query results
	$total = 0;
	while ($row = $db_coin->db_fetch_array($result)) {
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NL"><img src="coin_modules/ipn/vendors/'.$row['ipn_vendor'].'.jpg" alt="'.$row['ipn_vendor'].'"></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL">'.dt_make_datetime($row['ipn_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><a href="mod.php?mod=ipn&mode=detailed&ipn_txn='.$row['ipn_txn'].'">'.$row['ipn_txn'].'</a></td>'.$_nl;
		IF (empty($row['ipn_pay_stat'])) {$row['ipn_pay_stat'] = '-----';}
		$_out .= '<td class="TP3SML_NL">'.$_LANG['_IPN']['STAT'][$row['ipn_pay_stat']].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_NL">'.$_LANG['_IPN']['TYPE'][$row['ipn_txn_type']].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['ipn_pay_amt'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['ipn_amt_applied'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$total += $row['ipn_pay_amt'];
		$apply_total += $row['ipn_amt_applied'];
		$_out .= '<td class="TP3SML_NL">';
		IF ($row['ipn_cl_id'] == 0) {
			$_out .= $row['ipn_name_last'];
		} ELSE {
			$_out .= do_get_client_name($row['ipn_cl_id']);
		}
		$_out .= '</td>'.$_nl;
		$_out .= '<td class=TP3SML_NL><nobr>';
		IF ($_CCFG['IPN_ALLOW_DELETE']) {
			$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=ipn&mode=delete&stage=1&ipn_txn='.$row['ipn_txn'], $_TCFG['_S_IMG_DEL_S'],'');
		}
		IF ($_CCFG['IPN_ALLOW_RESUBMIT']) {
			$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=ipn&mode=resubmit&ipn_txn='.$row['ipn_txn'], $_TCFG['_S_IMG_EDIT_S'],'');
		}
		$_out .= '</nobr></td>'.$_nl;
		$_out .= '</tr>'.$_nl;
	}
	# Totals
	$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
	$_out .= '<td class="TP3SML_NR" colspan=5><b>'.$_LANG['_IPN']['PageTotals'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NR"><b>'.do_currency_format($total,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'<b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NR"><b>'.do_currency_format($apply_total,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'<b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL" colspan=2></td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	# closeout
	$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="9">'.$_nl;
	$_out .= $_page_menu.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</div>'.$_nl;
	$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

function do_test_ipn_form($adata, $aret_flag=0) {
	# Dim some Vars:
	global $_CCFG, $_TCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Some HTML Strings (reduce text)
	$_td_str_left	= '<td class="TP3SML_NR" width="30%">';
	$_td_str_right	= '<td class="TP3SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
	$_cstr  = '<form action="'.PKG_URL_MDLS.'ipn/vendors/'.$_LANG['_IPN']['TEST']['vendor'].'.php" method="post" name="ipn">'.$_nl;
	$_cstr .= '<input type="hidden" name="debug" id="debug" value="1">'.$_nl;
	$_cstr .= '<table width="100%" cellspacing="0" cellpadding="1">'.$_nl;

	FOREACH($adata as $key => $value) {
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$key.$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata[$key] == $_LANG['_IPN']['TEST']['vendor']) {
			$_cstr .= '<INPUT class="PSML_NL" type="text" name="'.$key.'" value="'.htmlspecialchars($adata[$key]).'" size="40" maxlength="999" readonly="readonly">';
		} ELSE {
			$_cstr .= '<INPUT class="PSML_NL" type="text" name="'.$key.'" value="'.htmlspecialchars($adata[$key]).'" size="40" maxlength="999">';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
	}

	$_cstr .= '<tr>'.$_td_str_left.'</td>'.$_td_str_right.$_nl;
	IF ($adata['resubmit']) {$_cstr .= $_LANG['_IPN']['resubmit_warning'].'<br /><br />';}
	$_cstr .= do_input_button_class_sw('b_submit', 'SUBMIT', $_LANG['_IPN']['b_submit'], 'button_form_h', 'button_form', '1').$_nl;
	$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_IPN']['b_reset'], 'button_form_h', 'button_form', '1');
	$_cstr .= '</td>'.$_nl;
	$_cstr .= '</tr>'.$_nl;
	$_cstr .= '</table>'.$_nl;
	$_cstr .= '</form>'.$_nl;

	# Return / Echo Final Output
	IF ($aret_flag) {return $_cstr;} ELSE {echo $_cstr;}
}

function do_detailed_display($ipn_txn, $aret_flag=0) {
	# Dim some Vars:
	global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Get account array
	$query	= 'SELECT * FROM ' . $_DBCFG['table_prefix'] . "ipn_log WHERE ipn_txn='$ipn_txn' ORDER BY ipn_ts ASC";
	$result	= $db_coin->db_query_execute($query);

	# Get Log Lines
	$query		= 'SELECT * FROM ' . $_DBCFG['table_prefix'] . "ipn_text WHERE ipn_txn_id='$ipn_txn' ORDER BY ipn_text_ts ASC";
	$result_l		= $db_coin->db_query_execute($query);
	$numrows_l	= $db_coin->db_query_numrows($result_l);

	# Build form output
	$_out  = '<div align="center">'.$_nl;
	$_out .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="4">'.$_nl;

	$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
	$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl;
	$_out .= '<td class="TP0MED_NL">'.$_nl."<b>".$_LANG['_IPN']['IPN_LOG_TITLE']."</b>".$_nl;
	$_out .= '</tr>'.$_nl.'</table>'.$_nl;

	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
	$_out .= '<td class="TP3SML_NL" colspan="4"><b>'.$_LANG['_IPN']['IPN_LOG_ACTION'].' for '.$ipn_txn.':</b></td>'.$_nl;
	$_out .= '</tr>'.$_nl;

	# Process query results
	while ($row = $db_coin->db_fetch_array($result)) {
		$_str = $row['ipn_var_details'];
		$_str = str_replace("-:","<tr><td>",$_str);
		$_str = str_replace("=","</td><td>",$_str);
		$_str = str_replace(":-","</td></tr>",$_str);

		$_out_1 .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out_1 .= '<td class="TP3SML_NL" colspan="4"><table width=100%>'.$_str.'</table></td>'.$_nl;
		$_out_1 .= '</tr>'.$_nl;
	}
	while ($row = $db_coin->db_fetch_array($result_l)) {
		$_out_2 .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out_2 .= '<td class="TP3SML_NL">'.dt_make_datetime ( $row['ipn_text_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM'] ).'</td>'.$_nl;
		$_out_2 .= '<td class="TP3SML_NL" colspan="2">'.str_replace("\n", '<br>', $row['ipn_log_text']).'</td>'.$_nl;
		$_out_2 .= '</tr>'.$_nl;
	}

	$_out .= $_out_1.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
	$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_IPN']['IPN_LOG_TS'].'</b></td>'.$_nl;
	$_out .= '<td class="TP3SML_NL" colspan="2"><b>'.$_LANG['_IPN']['IPN_LOG_ACTION'].'</b></td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	$_out .= $_out_2.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</div>'.$_nl;
	$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

function do_tstr_ipn_action_list($atitle) {
	# Dim some Vars
	global $_CCFG, $_TCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Search form
	$_sform  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
	$_sform .= '<input type="hidden" name="mod" id="mod" value="ipn">'.$_nl;
	$_sform .= '<select class="select_form" name="ipn_view" size="1" value="Action" onchange="submit();">'.$_nl;
	$_sform .= '<option value="" selected>'.$_LANG['_IPN']['Show'].'</option>'.$_nl;
	$_sform .= '<option value="direct">'.$_LANG['_IPN']['Show_Only_Direct'].'</option>'.$_nl;
	$_sform .= '<option value="all">'.$_LANG['_IPN']['Show_All'].'</option>'.$_nl;
	$_sform .= '<option value="subscr">'.$_LANG['_IPN']['ShowSub'].'</option>'.$_nl;
	$_sform .= '</select>'.$_nl;
	$_sform .= '</FORM>'.$_nl;

	$_tstr  = '<table width="100%" cellpadding="0" cellspacing="0"><tr class="BLK_IT_TITLE_TXT">';
	$_tstr .= '<td class="TP0MED_BL" valign="top">'.$_nl.$atitle.$_nl.'</td>'.$_nl;

	IF ($_CCFG['_IS_PRINT'] != 1) {
		$_tstr .= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sform.$_nl.'</td>'.$_nl;
	} ELSE {
		$_tstr .= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sp.$_sp.$_nl.'</td>'.$_nl;
	}

	$_tstr .= '</tr></table>';

	# Build form output
	return $_tstr;
}

function do_resubmit_data($ipn_txn, $aret_flag) {
	global $_DBCFG, $db_coin;
	$query = 'SELECT ipn_var_details FROM '.$_DBCFG['table_prefix']."ipn_log WHERE ipn_txn='".$db_coin->db_sanitize_data($ipn_txn)."' ORDER BY ipn_ts ASC";
	$result = $db_coin->db_query_execute($query);
	$ipn = $db_coin->db_fetch_array($result);
	$str = str_replace('-:', '$_LOG[', $ipn['ipn_var_details']);
	$str = str_replace(' = ', ']=\'', $str);
	$str = str_replace(':-', '\';', $str);
	eval($str);
	$_LOG['resubmit'] = 1;
	unset($_LOG['b_submit']);
	$_out = do_test_ipn_form($_LOG, 1);
	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

function do_get_client_name($cl_id) {
	global $_DBCFG, $db_coin;
	# Find client for entry
	$query = 'SELECT cl_name_first, cl_name_last FROM ' . $_DBCFG['clients'] . ' WHERE cl_id='.$cl_id;
	# Do select and return check
	$result	= $db_coin->db_query_execute($query);
	$numrows	= $db_coin->db_query_numrows($result);
	$client	= $db_coin->db_fetch_array($result);
	IF ($numrows == 0) {
		return 'n/a';
	} ELSE {
		return '<a href=mod.php?mod=clients&mode=view&cl_id='.$cl_id.'>'.$client['cl_name_last'].', '.$client['cl_name_first'].'</a>';
	}
}
?>