<?php
/**
 * Module: IPN (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=ipn');
		exit();
	}


# Get security vars
	$_SEC = get_security_flags();


# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_ipn_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_ipn_override.php');
	}


# Include journal functions file
	include(PKG_PATH_MDLS.'ipn/ipn_admin.php');



/**************************************************************
* Module Code
**************************************************************/
# Check $_GPV['mode'] and set default
	switch($_GPV['mode']) {
		case 'detailed':
		case 'test':
		case 'delete':
		case 'resubmit':
			break;
		default:
			$_GPV['mode'] = 'view';
		break;
	}


# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;

##############################
# Mode Call:         All modes
# Summary:
#        - Check if login required
##############################
IF (!$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for ipn listings
		$_out  = $_nl;
		echo do_login($data, 'admin', '1').$_nl;
}


$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=ipn', $_TCFG['_IMG_LISTING_M'],'');

IF (!$_login_flag  && $_GPV['mode'] == 'view') {
	echo do_mod_block_it(do_tstr_ipn_action_list($_LANG['_IPN']['IPN_LOG_TITLE']), do_log_display($_GPV,1), '1', $_mstr, '1');
}

IF (!$_login_flag  && $_GPV['mode'] == 'detailed') {
	echo do_mod_block_it($_LANG['_IPN']['IPN_LOG_TITLE'], do_detailed_display($_GPV['ipn_txn'],1), '1', $_mstr, '1');
}

IF (!$_login_flag  && $_GPV['mode'] == 'test') {
	echo do_mod_block_it($_LANG['_IPN']['Enter_Test_Info'], do_test_ipn_form($_LANG['_IPN']['TEST'],1), '1', $_mstr, '1');
}

IF (!$_login_flag  && $_GPV['mode'] == 'resubmit') {
	echo do_mod_block_it($_LANG['_IPN']['Enter_Test_Info'], do_resubmit_data($_GPV['ipn_txn'],1), '1', $_mstr, '1');
}

##############################
# Mode Call: Delete Entry
# Summary Stage 1:
#        - Confirm delete entry
# Summary Stage 2:
#        - Do table update
#        - Display results
##############################
IF (!$_login_flag  && $_GPV['mode'] == 'delete') {

	IF ($_GPV['stage'] == 2) {
	# Do query for transactions
		$eff_rows	= 0;
		$query	= 'DELETE FROM '.$_DBCFG['table_prefix']."ipn_log WHERE ipn_txn = '".$_GPV['ipn_txn']."'";
		$result	= $db_coin->db_query_execute($query) OR DIE('Unable to complete request');
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Do query for details
		$eff_rows_ii	= 0;
		$query_ii		= 'DELETE FROM '.$_DBCFG['table_prefix']."ipn_text WHERE ipn_txn_id = '".$_GPV['ipn_txn']."'";
		$result_ii	= $db_coin->db_query_execute($query_ii) OR DIE('Unable to complete request');
		$eff_rows_ii	= $db_coin->db_query_affected_rows();
		$_del_results  .= '<br />'.$_LANG['_IPN']['Delete_ipn_Entry_Results_02'].':'.$_sp.($eff_rows+$eff_rows_ii);

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = $_LANG['_IPN']['trans_deleted_title'];
		$_cstr  = '<center>'.$_LANG['_IPN']['trans_deleted'].'<br>'.$_del_results.'<br></center>';
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=ipn&mode=view', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, '1').'<br />'.$_nl;

	# Echo final output
		echo $_out;


	} else {
	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_IPN']['Delete_ipn_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER['PHP_SELF'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="mod" id="mod" value="ipn">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="mode" id="mode" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_IPN']['Delete_ipn_Entry_Message'].'='.$_sp.$_GPV[ipn_txn].'<br>'.$_nl;
		$_cstr .= $_LANG['_IPN']['Delete_ipn_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_GPV['txn_id'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ipn_txn" value="'.$_GPV[ipn_txn].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_IPN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=ipn&mode=view', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it ($_tstr, $_cstr, 1, $_mstr, '1').'<br />'.$_nl;

	# Echo final output
		echo $_out;
	}
}
?>