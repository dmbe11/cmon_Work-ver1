<?php
/**
 * Module: HelpDesk (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage HelpDesk
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_helpdesk.php
 */


# Code to handle file being loaded by URL
	IF (eregi('helpdesk_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=helpdesk');
		exit;
	}

/**************************************************************
 * Module Functions
**************************************************************/
# Do Data Input Validate
function do_input_validation($_GPV) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
		IF ($_GPV['mode'] == 'new') {
			IF (!$_GPV['hd_tt_cl_email'])		{$err_entry['flag'] = 1; $err_entry['hd_tt_cl_email'] = 1;}
			IF (!$_GPV['hd_tt_priority'])		{$err_entry['flag'] = 1; $err_entry['hd_tt_priority'] = 1;}
			IF (!$_GPV['hd_tt_category'])		{$err_entry['flag'] = 1; $err_entry['hd_tt_category'] = 1;}
			IF (!$_GPV['hd_tt_subject'])		{$err_entry['flag'] = 1; $err_entry['hd_tt_subject'] = 1;}
			IF (!$_GPV['hd_tt_message'])		{$err_entry['flag'] = 1; $err_entry['hd_tt_message'] = 1;}
		}

		IF ($_GPV['mode'] == 'add') {
			IF (!$_GPV['hdi_tt_message'])		{$err_entry['flag'] = 1; $err_entry['hdi_tt_message'] = 1;}
		}

		return $err_entry;
}


# Do return string from value for: Rate Ticket option
function do_valtostr_rate_ticket($avalue) {
	# Dim some Vars:
		global $_LANG;

		switch($avalue) {
			case "1":
				$_result = '*';
				break;
			case "2":
				$_result = '**';
				break;
			case "3":
				$_result = '***';
				break;
			case "4":
				$_result = '****';
				break;
			case "5":
				$_result = '*****';
				break;
			default:
				$_result = $_LANG['_HDESK']['unrated'];
				break;
		}

		return $_result;
}


# Do list select field for: Rate Ticket
function do_select_list_rate_ticket($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_LANG;

	# Build form output
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_HDESK']['Rate_Ticket'].'</option>'.$_nl;

		$_out .= '<option value="1"';
		IF ($avalue == 1) {$_out .= ' selected';}
		$_out .= '>*</option>'.$_nl;

		$_out .= '<option value="2"';
		IF ($avalue == 2) {$_out .= ' selected';}
		$_out .= '>**</option>'.$_nl;

		$_out .= '<option value="3"';
		IF ($avalue == 3) {$_out .= ' selected';}
		$_out .= '>***</option>'.$_nl;

		$_out .= '<option value="4"';
		IF ($avalue == 4) {$_out .= ' selected';}
		$_out .= '>****</option>'.$_nl;

		$_out .= '<option value="5"';
		IF ($avalue == 5) {$_out .= ' selected';}
		$_out .= '>*****</option>'.$_nl;

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Admin
function do_select_list_admin($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select records for list.
		$query	 = 'SELECT admin_id, admin_name_first, admin_name_last FROM '.$_DBCFG['admins'];
		$query	.= ' ORDER BY admin_name_last ASC, admin_name_first ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Build form output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_HDESK']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($admin_id, $admin_name_first, $admin_name_last) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$admin_id.'"';
			IF ($admin_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$admin_name_last.', '.$admin_name_first.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Client Domains
function do_select_list_client_domain($aname, $avalue, $acl_id, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select records for list.
		$query  = 'SELECT dom_id, dom_cl_id, dom_domain FROM '.$_DBCFG['domains'];
		IF ($acl_id > 0) {$query	.= ' WHERE dom_cl_id='.$acl_id;}
		$query .= ' ORDER BY dom_domain ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Build form output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_HDESK']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($dom_id, $dom_cl_id, $dom_domain) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$dom_id.'"';
			IF ($dom_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$dom_domain.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Get user info
function get_user_name($auser_id, $aw) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select
		IF ($aw == 'admin') {
			$query = 'SELECT admin_name_first, admin_name_last FROM '.$_DBCFG['admins'].' WHERE admin_id='.$auser_id;
		}
		IF ($aw == 'user') {
			$query = 'SELECT cl_name_first, cl_name_last FROM '.$_DBCFG['clients'].' WHERE cl_id='.$auser_id;
		}

	# Do select
		$result = $db_coin->db_query_execute($query);

	# Get value and set return
		while(list($_name_first, $_name_last) = $db_coin->db_fetch_row($result)) {
			$_name = $_name_first.$_sp.$_name_last;
		}

		return $_name;
}


# Get last admin to reply id
function do_get_last_reply_admin_id($ahd_tt_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	.= 'SELECT '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_ad_id';
		$query	.= ' FROM '.$_DBCFG['helpdesk'].', '.$_DBCFG['helpdesk_msgs'];
		$query	.= ' WHERE '.$_DBCFG['helpdesk'].'.hd_tt_id='.$_DBCFG['helpdesk_msgs'].'.hdi_tt_id';
		$query	.= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_id='.$ahd_tt_id;
		$query	.= ' AND '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_ad_id > 0';
		$query	.= ' ORDER BY '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_time_stamp ASC LIMIT 1';

		$result	= $db_coin->db_query_execute($query);

	# Get Max Value
		$_ret_tt_ad_id = 0;
		IF ($db_coin->db_query_numrows($result)) {
			while(list($_hdi_tt_ad_id) = $db_coin->db_fetch_row($result)) {
				$_ret_tt_ad_id = $_hdi_tt_ad_id;
			}
		}

	# Return
		return $_ret_tt_ad_id;
}


# Get max ticket id
function do_get_max_hd_tt_id() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(hd_tt_id) FROM '.$_DBCFG['helpdesk'];
		$result	= $db_coin->db_query_execute($query);

	# Get Max Value
		while(list($_max_hd_tt_id) = $db_coin->db_fetch_row($result)) {
			$max_hd_tt_id = $_max_hd_tt_id;
		}

	# Check / Set Value for return
		IF (!$max_hd_tt_id) {
			return $_CCFG['BASE_HELPDESK_ID'];
		} ELSE {
			return $max_hd_tt_id;
		}
}


# Do form New Ticket
function do_form_new_ticket($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_HDESK']['HD_ERR_ERR_HDR1'].'<br>'.$_LANG['_HDESK']['HD_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['hd_tt_priority']) {$err_str .= $_LANG['_HDESK']['HD_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['hd_tt_category']) {IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_HDESK']['HD_ERR_ERR02']; $err_prv = 1;}
		 	IF ($aerr_entry['hd_tt_subject']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_HDESK']['HD_ERR_ERR03']; $err_prv = 1;}
		 	IF ($aerr_entry['hd_tt_message'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_HDESK']['HD_ERR_ERR04']; $err_prv = 1;}
		 	IF ($aerr_entry['hd_tt_cl_email'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_HDESK']['HD_ERR_ERR05']; $err_prv = 1;}
		}

	# Get default client email for tt email
		IF ($adata['hd_tt_cl_email'] == '') {
			$_clinfo = get_contact_client_info($_SEC['_suser_id']);
			$adata['hd_tt_cl_email'] = $_clinfo['cl_email'];
		}

	# Build common td start tag / col strings (reduce text)
		$_tr_spacer 		= '<tr><td class="TP1MED_NC" height="10px" width="100%">'.$_sp.'</td></tr>';
		$_td_str_center	= '<td class="TP1MED_NC" width="100%" colspan="2">';
		$_td_str_just		= '<td class="TP1MED_NJ" width="100%" colspan="2">';
		$_td_str_left_vtop	= '<td class="TP1MED_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1MED_NR" width="30%">';
		$_td_str_right		= '<td class="TP1MED_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_HDESK']['Helpdesk_Support_Ticket'].':'.$_sp;
		$_tstr .= $_LANG['_HDESK']['Open_New'].$_sp.'('.$_sp.'(*)'.$_sp.$_LANG['_HDESK']['denotes_optional_items'].')';

	# Do Main Form
		$_cstr .= '<table width="95%" align="center">'.$_nl;

		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="helpdesk">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="new">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<table width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_just.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['HD_ADD_NEW_TXT_HDR'].'</b>'.$_nl;
		$_cstr .= '<br>'.$_LANG['_HDESK']['HD_ADD_NEW_TXT01'].$_nl;
		IF ($_CCFG['HELPDESK_ADMIN_CAN_ADD'] && $_SEC['_sadmin_flg']) {
			$_cstr .= $_sp.$_LANG['_HDESK']['HD_ADD_NEW_TXT02A'].$_nl;
		} ELSE {
			$_cstr .= $_sp.$_LANG['_HDESK']['HD_ADD_NEW_TXT02'].$_nl;
		}
		$_cstr .= '<br><hr>'.$_nl;
		IF ($err_str) {$_cstr .= '<p align="center"><b>'.$err_str.'</b><br><br>'.$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Client_TT_Email'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
	# Create dropdown of client emails. Avoids mis-spellings in TT email
		$_cstr .= do_select_list_clients_emails($adata['hd_tt_cl_email']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Priority'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_priority('hd_tt_priority', $adata['hd_tt_priority'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Category'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_category('hd_tt_category', $adata['hd_tt_category'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Subject'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" type="text" name="hd_tt_subject" size="40" maxlength="50" value="'.htmlspecialchars($adata['hd_tt_subject']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Message'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PMED_NL" NAME="hd_tt_message" COLS="'.$_cols.'" ROWS="15">'.$adata['hd_tt_message'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Domain_NReq'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;

		IF ($_CCFG['HELPDESK_ADMIN_CAN_ADD'] && $_SEC['_sadmin_flg']) {
		# If admin, create drop-down of ALL domains
			$_cstr .= do_select_list_client_domain('hd_tt_cd_id', $adata['hd_tt_cd_id'], 0, 1);
		} ELSE {
		# Else create dropdown of client's domains
			$_cstr .= do_select_list_client_domain('hd_tt_cd_id', $adata['hd_tt_cd_id'], $_SEC['_suser_id'], 1);
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['l_Example_URL_NReq'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" type="text" name="hd_tt_url" size="40" maxlength="50" value="'.htmlspecialchars($adata['hd_tt_url']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit_nt', 'SUBMIT', $_LANG['_HDESK']['B_Submit'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset_nt', 'RESET', $_LANG['_HDESK']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr_flag = 0;
		$_mstr = '';

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo out
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do form Add Ticket Message
function do_form_new_message($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_HDESK']['HD_ERR_ERR_HDR1'].'<br>'.$_LANG['_HDESK']['HD_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['hdi_tt_message']) {$err_str .= $_LANG['_HDESK']['HD_ERR_ERR04']; $err_prv = 1;}
		}

	# Build common td start tag / col strings (reduce text)
		$_tr_spacer 		= '<tr><td class="TP1MED_NC" height="10px" width="100%">'.$_sp.'</td></tr>';
		$_td_str_center	= '<td class="TP1MED_NC" width="100%" colspan="2">';
		$_td_str_just		= '<td class="TP1MED_NJ" width="100%" colspan="2">';
		$_td_str_left_vtop	= '<td class="TP1MED_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1MED_NR" width="30%">';
		$_td_str_right		= '<td class="TP1MED_NL" width="70%">';

	# Do Main Form
		$_out .= '<table width="75%" align="center">'.$_nl;

		$_out .= '<tr>'.$_td_str_center.$_nl;
		$_out .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_out .= '<input type="hidden" name="mod" value="helpdesk">'.$_nl;
		$_out .= '<input type="hidden" name="mode" value="add">'.$_nl;
		$_out .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_out .= '<input type="hidden" name="hd_tt_id" value="'.$adata['hd_tt_id'].'">'.$_nl;

		$_out .= '<table width="100%">'.$_nl;
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_just.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['HD_ADD_MSG_TXT_HDR'].'</b>'.$_nl;
		$_out .= '<br>'.$_LANG['_HDESK']['HD_ADD_MSG_TXT01'].$_nl;
		$_out .= $_sp.$_LANG['_HDESK']['HD_ADD_MSG_TXT02'].$_nl;
		$_out .= '<br><hr>'.$_nl;
		IF ($err_str) {$_out .= '<p align="center"><b>'.$err_str.'</b><br><br>'.$_nl;}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left_vtop.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['l_Message'].$_sp.'</b>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_out .= '<TEXTAREA class="PMED_NL" NAME="hdi_tt_message" COLS="'.$_cols.'" ROWS="15">'.$adata['hdi_tt_message'].'</TEXTAREA>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left_vtop.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['l_Status'].$_sp.'</b>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($_SEC['_sadmin_flg'] == 1 && $_CCFG['_IS_PRINT'] != 1) {
			$_out .= do_select_list_status('hd_tt_status', $adata['hd_tt_status'], 1);
		} ELSE {
			$_out .= $adata['hd_tt_status'];
			$_out .= $_sp.'<input type="hidden" name="hd_tt_status" value="'.htmlspecialchars($adata['hd_tt_status']).'">'.$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left_vtop.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['l_Closed_Flag'].$_sp.'</b>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= do_select_list_open_closed('hd_tt_closed', $adata['hd_tt_closed'], 1);
		} ELSE {
			$_out .= do_valtostr_open_closed($adata['hd_tt_closed']);
			$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_closed" value="'.htmlspecialchars($adata['hd_tt_closed']).'">'.$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		$_out .= do_input_button_class_sw('b_edit_nm', 'SUBMIT', $_LANG['_HDESK']['B_Submit'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= do_input_button_class_sw('b_reset_nm', 'RESET', $_LANG['_HDESK']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</form>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</td>'.$_nl.'</tr>'.$_nl;
		$_out .= '</table>'.$_nl;

		$_out .= '<br>'.$_nl;

	# Echo out
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Tickets
function do_select_listing_tickets($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_GPV, $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_out	= '';
		$_ps		= '';
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
		IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['helpdesk'];
		$query	.= ' LEFT JOIN '.$_DBCFG['clients'].' ON '.$_DBCFG['clients'].'.cl_id='.$_DBCFG['helpdesk'].'.hd_tt_cl_id';
		$query	.= ' LEFT JOIN '.$_DBCFG['clients_contacts'].' ON '.$_DBCFG['clients_contacts'].'.contacts_email='.$_DBCFG['helpdesk'].'.hd_tt_cl_email';
		$_where	 = ' WHERE '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_DBCFG['clients'].'.cl_id';

	# Show only selected status tickets
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_where .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_status='".$db_coin->db_sanitize_data($_GPV['status'])."'";}
		IF ($_GPV['notstatus']) {$_where .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_status != '".$db_coin->db_sanitize_data($_GPV['notstatus'])."'";}

	# Set to logged in Client ID if not admin to avoid seeing other client ticket id's
		IF ($_SEC['_suser_flg']) {
			$_where .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_SEC['_suser_id'];
		} ELSEIF ($_SEC['_sadmin_flg']) {
			IF ($adata['hd_tt_cl_id'] > 0) {
				$_where .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$adata['hd_tt_cl_id'];
			} ELSE {
				$_where .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id > 0';
			}
		}

	# Set Filters
		IF (!$adata['fb'])		{$adata['fb'] = '';}
		IF ($adata['fb'] == '1')	{$_where .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_closed='".$db_coin->db_sanitize_data($adata['fs'])."'";}
		IF ($adata['fb'] == '2')	{$_where .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_status='".$db_coin->db_sanitize_data($adata['fs'])."'";}
		IF ($adata['fb'] == '3') {
			$_where .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_status='".$db_coin->db_sanitize_data($adata['fs'])."'";
			$_where .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_closed=0';
		}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'D';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])		{$adata['sb'] = '1';}
		IF ($adata['sb'] == '1')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_id '.$order_AD;}
		IF ($adata['sb'] == '2')	{$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_name_last '.$order_AD.', '.$_DBCFG['clients'].'.cl_name_first '.$order_AD;}
		IF ($adata['sb'] == '3')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_time_stamp '.$order_AD;}
		IF ($adata['sb'] == '4')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_subject '.$order_AD;}
		IF ($adata['sb'] == '5')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_priority '.$order_AD;}
		IF ($adata['sb'] == '6')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_status '.$order_AD;}
		IF ($adata['sb'] == '7')	{$_order = ' ORDER BY '.$_DBCFG['helpdesk'].'.hd_tt_closed '.$order_AD;}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb = '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso = '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb = '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs = '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['helpdesk'].', '.$_DBCFG['clients'];
		$query_ttl .= $_where;

		$result_ttl = $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next	- is page loading first record number
	# $_rec_start	- is a given page start record (which will be rec_next)
		$_rec_page	= $_CCFG['IPP_HELPDESK'];
		$_rec_next	= $adata['rec_next'];
		IF (!$_rec_next) {$_rec_next=0;}

	# Range of records on current page
		$_rec_next_lo = $_rec_next+1;
		$_rec_next_hi = $_rec_next+$_rec_page;
		IF ($_rec_next_hi > $numrows_ttl) {$_rec_next_hi = $numrows_ttl;}

	# Calc no pages,
		$_num_pages = round(($numrows_ttl/$_rec_page), 0);
		IF ($_num_pages < ($numrows_ttl/$_rec_page)) {$_num_pages = $_num_pages+1;}

	# Loop Array and Print Out Page Menu HTML
		$_page_menu = $_LANG['_HDESK']['l_Pages'].$_sp;
		for ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = (($i*$_rec_page)-$_rec_page);
			IF ($_rec_start == $_rec_next) {
			# Loading Page start record so no link for this page.
				$_page_menu .= "$i";
			} ELSE {
				$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=helpdesk'.$_link_xtra.$_ps.'&rec_next='.$_rec_start.'">'.$i.'</a>';
			}
			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="'.$_SERVER["PHP_SELF"].'?mod=helpdesk&sb=';
		$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&rec_next='.$_rec_next.$_ps.'">';

		$_hdr_link_1 = $_LANG['_HDESK']['l_Id'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_2 = $_LANG['_HDESK']['l_Client'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_3 = $_LANG['_HDESK']['l_Date'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_4 = $_LANG['_HDESK']['l_Subject'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_5 = $_LANG['_HDESK']['l_Priority'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_6 = $_LANG['_HDESK']['l_Status'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_7 = $_LANG['_HDESK']['l_Closed'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}


	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
			$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=helpdesk&status=all'.$_link_xtra;
			$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</nobr></td>';
			for ($i=1; $i< sizeof($_CCFG['HD_TT_STATUS']); $i++) {
				$_out .= '<td align="right"><nobr>&nbsp;[<a href="mod.php?mod=helpdesk&status='.$_CCFG['HD_TT_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['HD_TT_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
			$_out .= '<td>&nbsp;</td>';
			for ($i=1; $i< sizeof($_CCFG['HD_TT_STATUS']); $i++) {
				$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=helpdesk&notstatus='.$_CCFG['HD_TT_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['HD_TT_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr></table>';
			$_out .= '<br><br>';
		}

	# Build form output
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		IF ($_SEC['_sadmin_flg'] && $_CCFG['HELPDESK_SHOW_CLIENT_NAME'] == 1) {$_cspan = 8;} ELSE {$_cspan = 7;}
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.($_cspan-$_CCFG['_IS_PRINT']).'">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['Client_Support_Tickets'].':'.$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_HDESK']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_HDESK']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_sadmin_flg']) {
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search&sw=helpdesk', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
			}
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NC" valign="top"><b>'.$_hdr_link_1.'</b></td>'.$_nl;
		IF ($_SEC['_sadmin_flg'] && $_CCFG['HELPDESK_SHOW_CLIENT_NAME'] == 1) {
			$_out .= '<td class="TP3SML_NL" valign="top"><b>'.$_hdr_link_2.'</b></td>'.$_nl;
		}
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_3.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_4.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_5.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_6.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_7.'</td>'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<td class="TP3SML_BC" valign="top">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['hd_tt_id'].'</td>'.$_nl;
				IF ($_SEC['_sadmin_flg'] && $_CCFG['HELPDESK_SHOW_CLIENT_NAME'] == 1) {
					$_out .= '<td class="TP3SML_NL">';
					IF ($row['hd_tt_cl_email'] != $row['cl_email']) {
						$_out .= $row['contacts_name_last'].', '.$row['contacts_name_first'].'<br>--> ';
					}
					$_out .= $row['cl_name_last'].', '.$row['cl_name_first'].'</td>'.$_nl;
				}
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['hd_tt_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['hd_tt_subject'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['hd_tt_priority'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['hd_tt_status'].' </td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.do_valtostr_no_yes($row['hd_tt_closed']).' </td>'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
					$_out .= do_nav_link('mod.php?mod=helpdesk&mode=view&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_out .= do_nav_link('mod_print.php?mod=helpdesk&mode=view&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					$_out .= do_nav_link('mod.php?mod=helpdesk&mode=mail&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1) ) {
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=delete&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
					$_out .= '</nobr></td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.($_cspan-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display: Ticket
function do_display_ticket($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Begin Primary Ticket Information
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['helpdesk'];
		$query	.= ' LEFT JOIN '.$_DBCFG['clients'].' ON '.$_DBCFG['clients'].'.cl_id='.$_DBCFG['helpdesk'].'.hd_tt_cl_id';
		$query	.= ' LEFT JOIN '.$_DBCFG['clients_contacts'].' ON '.$_DBCFG['clients_contacts'].'.contacts_email='.$_DBCFG['helpdesk'].'.hd_tt_cl_email';
		$query	.= ' WHERE ';
		$query	.= $_DBCFG['helpdesk'].'.hd_tt_id='.$adata['hd_tt_id'];

	# Set to logged in Client ID if not admin to avoid seeing other client ticket id's
		IF (!$_SEC['_sadmin_flg']) {
			$query .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_SEC['_suser_id'];
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build common td start tag / strings (reduce text)
		$_td_str_span			= '<td class="TP1SML_NC" colspan="4">';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" valign="top">';
		$_td_str_left			= '<td class="TP1SML_NR">';
		$_td_str_right			= '<td class="TP1SML_NL">';
		$_td_str_left_2		= '<td class="TP1SML_NR" width="35%" colspan="2">';
		$_td_str_right_2		= '<td class="TP1SML_NL" width="65%" colspan="2">';
		$_td_str_right_3		= '<td class="TP1SML_NL" colspan="3">';
		$_td_str_right_3_vtop	= '<td class="TP1SML_NL" valign="top" colspan="3">';

		$_td_str_msg_left	= '<td class="TP5SML_BR" align="right" width="30%" valign="top">';
		$_td_str_msg_right	= '<td class="TP5SML_NL" width="70%" valign="top">';

	# Determine if form to be embedded (either Admin (FA) or User (FU) form)
		IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1))	{$_FA = 1;} ELSE {$_FA = 0;}
		IF ($_SEC['_suser_flg'])												{$_FU = 1;} ELSE {$_FU = 0;}

	# Build form output
		$_out = '<div align="center">'.$_nl;
		IF (($_FA == 1 || $_FU == 1) && $_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_out .= '<input type="hidden" name="mod" value="helpdesk">'.$_nl;
			$_out .= '<input type="hidden" name="mode" value="update">'.$_nl;
		}

		$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NL" colspan="4">'.$_nl;
		$_out .= '<b>'.$_LANG['_HDESK']['l_Primary_Information'].'</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
			# Get status for if add message used back in index.
				global $_return_tt_status, $_return_tt_closed;
				$_return_tt_status = $row['hd_tt_status'];
				$_return_tt_closed = $row['hd_tt_closed'];

				IF ($row['hd_tt_cd_id'] >0) {
				# Set Query for select records for list.
					$query_cd	 = 'SELECT '.$_DBCFG['domains'].'.dom_domain, '.$_DBCFG['server_info'].'.si_name';
					$query_cd	.= ' FROM '.$_DBCFG['domains'].', '.$_DBCFG['server_info'];
					$query_cd	.= ' WHERE '.$_DBCFG['domains'].'.dom_si_id='.$_DBCFG['server_info'].'.si_id';
					$query_cd .= ' AND '.$_DBCFG['domains'].'.dom_id='.$row['hd_tt_cd_id'];

				# Do select and return check
					$result_cd	= $db_coin->db_query_execute($query_cd);

				# Process query results
					while(list($dom_domain, $si_name) = $db_coin->db_fetch_row($result_cd)) {
						$_cl_domain	= $dom_domain;
						$_cl_server	= $si_name;
					}
				}

			# Process Data
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Ticket_Id'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$row['hd_tt_id'].'</td>'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Date_Created'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.dt_make_datetime($row['hd_tt_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Client'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right;
				IF ($row['hd_tt_cl_email'] != $row['cl_email']) {
					$_out .= $row['contacts_name_last'].', '.$row['contacts_name_first'].' --> ';
				}
				$_out .= $row['cl_name_last'].', '.$row['cl_name_first'].$_nl.'</td>'.$_nl;

				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Priority'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$row['hd_tt_priority'].$_nl.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_User_Name'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$row['cl_user_name'].$_nl.'</td>'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Category'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_nl;
				IF ($_FA == 1 && $_CCFG['_IS_PRINT'] != 1) {
					$_out .= do_select_list_category('hd_tt_category', $row['hd_tt_category'], 1);
				} ELSE {
					$_out .= $row['hd_tt_category'];
					$_out .= $_sp.'<INPUT TYPE="hidden" name="hd_tt_category" value="'.htmlspecialchars($row['hd_tt_category']).'">'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Domain'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_nl;
				IF ($_FA == 1 && $_CCFG['_IS_PRINT'] != 1) {
					$_out .= do_select_list_client_domain('hd_tt_cd_id', $row['hd_tt_cd_id'], $row['hd_tt_cl_id'], 1);
				} ELSE {
					$_out .= $_cl_domain;
					$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_cd_id" value="'.$row['hd_tt_cd_id'].'">'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Status'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_nl;
				IF ($_FA == 1 && $_CCFG['_IS_PRINT'] != 1) {
					$_out .= do_select_list_status('hd_tt_status', $row['hd_tt_status'], 1);
				} ELSE {
					$_out .= $row['hd_tt_status'];
					$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_status" value="'.htmlspecialchars($row['hd_tt_status']).'">'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Server'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_cl_server.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Closed_Flag'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_nl;
				IF (($_FA == 1 || $_FU == 1) && $_CCFG['_IS_PRINT'] != 1) {
					$_show_btns = 1;
					$_out .= do_select_list_open_closed('hd_tt_closed', $row['hd_tt_closed'], 1);
				} ELSE {
					$_out .= do_valtostr_open_closed($row['hd_tt_closed']);
					$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_closed" value="'.$row['hd_tt_closed'].'">'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Example_URL'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$row['hd_tt_url'].$_nl.'</td>'.$_nl;
				$_out .= $_td_str_left.'<b>'.$_LANG['_HDESK']['l_Rate_Ticket'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					IF ($_FU == 1 && $row['hd_tt_closed'] == 1 && $row['hd_tt_rating'] == 0) {
						$_show_btns = 1;
						$_out .= do_select_list_rate_ticket('hd_tt_rating', $row['hd_tt_rating'], 1);
					} ELSE {
						$_out .= do_valtostr_rate_ticket($row['hd_tt_rating']);
						$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_rating" value="'.$row['hd_tt_rating'].'">'.$_nl;
					}
				} ELSE {
					$_out .= do_valtostr_rate_ticket($row['hd_tt_rating']);
					$_out .= $_sp.'<INPUT TYPE=hidden name="hd_tt_id" value="'.$row['hd_tt_id'].'">'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				IF (($_FA == 1 || ($_FU == 1 && $_show_btns == 1)) && $_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= $_td_str_left.$_sp.$_nl.'</td>'.$_nl;
					$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
					$_out .= $_td_str_left.$_sp.$_nl.'</td>'.$_nl;
					$_out .= $_td_str_right.$_nl;
					$_out .= do_input_button_class_sw('b_edit_t', 'SUBMIT', $_LANG['_HDESK']['B_Submit'], 'button_form_h', 'button_form', '1').$_nl;
					$_out .= do_input_button_class_sw('b_reset_t', 'RESET', $_LANG['_HDESK']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
					$_out .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
					$_out .= '<INPUT TYPE=hidden name="hd_tt_id" value="'.$row['hd_tt_id'].'">'.$_nl;
					$_out .= '</td>'.$_nl;
					$_out .= '</tr>'.$_nl;
				}

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_span.'<hr width="90%">'.$_nl.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left_vtop.'<b>'.$_LANG['_HDESK']['l_Subject'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right_3.htmlspecialchars($row['hd_tt_subject']).'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_left_vtop.'<b>'.$_LANG['_HDESK']['l_Message'].'</b>'.$_sp.$_nl.'</td>'.$_nl;
				$_out .= $_td_str_right_3_vtop.truncate_string(htmlspecialchars($row['hd_tt_message'])).'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= $_td_str_span.$_sp.$_nl.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;
	# End Primary Ticket Information

	# Begin Ticket Messages Information
	# Set Query for select records for list.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['helpdesk'].', '.$_DBCFG['helpdesk_msgs'];
		$query	.= ' WHERE '.$_DBCFG['helpdesk'].'.hd_tt_id='.$_DBCFG['helpdesk_msgs'].'.hdi_tt_id';
		$query	.= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_id='.$adata['hd_tt_id'];

	# Set to logged in Client ID if not admin to avoid seeing other client ticket id's
		IF (!$_SEC['_sadmin_flg']) {
			$query .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_SEC['_suser_id'];
		}
		$query .= ' ORDER BY '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_time_stamp ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
		# Build form output
			$_out .= '<br>'.$_nl;
			$_out .= '<div align="center">'.$_nl;
			$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NL" colspan="2">'.$_nl;
			$_out .= '<b>'.$_LANG['_HDESK']['Support_Ticket_Messages'].':</b><br>'.$_nl;
			$_out .= '</td></tr>'.$_nl;

		# Process query results
			while ($row = $db_coin->db_fetch_array($result)) {
			# Get name of user or admin who replied
				IF ($row['hdi_tt_cl_id'] != 0) {
					$_name		= get_user_name($row['hdi_tt_cl_id'], 'user');
				} ELSEIF ($row['hdi_tt_ad_id'] != 0) {
					IF ($_CCFG['HELPDESK_ADMIN_REVEAL_ENABLE'] == 1) {
						$_name	= get_user_name($row['hdi_tt_ad_id'], 'admin');
					} ELSE {
						$_sinfo	= get_contact_info($_CCFG['MC_ID_SUPPORT']);
						$_name	= $_sinfo['c_name'];
					}
				}

			# Process Data
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_msg_left.$_nl;
				$_out .= '<b>'.$_name.$_sp.'</b><br><i>'.dt_make_datetime($row['hdi_tt_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.'</i>'.$_nl;
				$_out .= '</td>'.$_td_str_msg_right.$_nl;
				$_out .= truncate_string(htmlspecialchars($row['hdi_tt_message'])).$_nl;
				$_out .= '</td></tr>'.$_nl;
			}

			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;
		}

		IF (($_FA == 1 || $_FU == 1) && $_CCFG['_IS_PRINT'] != 1) {
			$_out .= '</FORM>'.$_nl;
		} ELSE {
			$_out .= '<br><br>'.$_nl;
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do email Client HelpDesk Support Ticket
function do_mail_helpdesk_tt($adata, $aret_flag=0) {
	# Abort if no ticket_id
		IF (!$adata['hd_tt_id']) {return '';}

	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_MTP = array();

	# Get mail contact information array based in "admin_revealed" setting
		$_cinfo = get_contact_info($_CCFG['MC_ID_SUPPORT']);
		IF ($_CCFG['HELPDESK_ADMIN_REVEAL_ENABLE'] == 1) {
			$_last_admin_id = do_get_last_reply_admin_id($adata['hd_tt_id']);
			IF ($_last_admin_id > 0) {
				$_ainfo = get_contact_admin_info($_last_admin_id);
			} ELSE {
				$_ainfo = $_cinfo;
			}
		} ELSE {
			$_ainfo = $_cinfo;
		}

	# Call common.php function for helpdesk ticket mtp data (see function for array values) / merge with current.
		$_tt_info = get_mtp_hdtt_info($adata['hd_tt_id']);

		IF ($_tt_info['numrows'] > 0) {
			$data_new	= array_merge($_MTP, $_tt_info);
			$_MTP	= $data_new;
		} ELSE {
			$_mail_error_flg = 1;
			$_mail_error_str .= '<br>'.$_LANG['_HDESK']['HD_EMAIL_MSG_01_PRE'].$_sp.$adata['hd_tt_id'].$_sp.$_LANG['_HDESK']['HD_EMAIL_MSG_01_SUF'];
		}

	# Call common.php function for helpdesk ticket items mtp data (see function for array values) / merge with current.
		$_ti_info = get_mtp_hdti_info($adata['hd_tt_id']);

		IF ($_ti_info['numrows'] > 0) {
			$data_new	= array_merge($_MTP, $_ti_info);
			$_MTP	= $data_new;
		} ELSE {
			$_mail_error_flg = 1;
			$_mail_error_str .= '<br>'.$_LANG['_HDESK']['HD_EMAIL_MSG_02_PRE'].$_sp.$adata['hd_tt_id'].$_sp.$_LANG['_HDESK']['HD_EMAIL_MSG_02_SUF'];
		}

	# Call common.php function for client mtp data (see function for array values) / merge with current.
		$_cl_info	= get_mtp_client_info($_tt_info['hd_tt_cl_id']);

		IF ( $_cl_info['numrows'] > 0 ) {
			$data_new	= array_merge($_MTP, $_cl_info);
			$_MTP	= $data_new;
		}

	# Set string for how ticket triggered (request type) and determine who to send to.
	# Request by who and how
		IF ($_SEC['_sadmin_flg']) {
			$_req_by = $_LANG['_HDESK']['Support'];
			$_req_id = 'S';
		} ELSE {
			$_req_by = $_LANG['_HDESK']['Client'];
			$_req_id = 'C';
		}
		IF ($adata['stage'] == 2) {
			$_req_how = $_LANG['_HDESK']['manually_requesting_a_copy'];
			$_req_id .= 'M';
		} ELSE {
			$_req_how = $_LANG['_HDESK']['adding_a_message_to_ticket'];
			$_req_id .= 'A';
		}

		$_MTP['req_type'] = $_req_by.' '.$_req_how;

	# Send to who, from support in all cases
	# If Admin (Support) requested OR Client added message- sent to admin (CC client if CA and enabled)
		IF ($_req_id == 'SM' || $_req_id == 'CA') {
    			IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   				$_req_recip	= $_cinfo['c_email'];
			} ELSE {
				$_req_recip	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
			}
			$_req_to_name	= $_cinfo['c_name'];
			$_req_to_email	= $_cinfo['c_email'];
			IF ($_req_id == 'CA' && $_CCFG['HELPDESK_MSG_CC_CLIENT_ENABLE']) {
				IF ($_MTP['cl_email'] == $_MTP['hd_tt_cl_email'] || $_MTP['hd_tt_cl_email'] == '') {
					IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
						$mail['cc'] = $_tt_info['hd_tt_cl_email'];
					} ELSE {
						$mail['cc'] = $_tt_info['cl_name_first'].' '.$_tt_info['cl_name_last'].' <'.$_tt_info['hd_tt_cl_email'].'>';
					}
				} ELSE {
					IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
						$mail['cc'] = $_tt_info['hd_tt_cl_email'];
					} ELSE {
						$mail['cc'] = $_tt_info['contacts_name_first'].' '.$_tt_info['contacts_name_last'].' <'.$_tt_info['hd_tt_cl_email'].'>';
					}
				}
			}
		}

	# If Client requested OR Admin (Support) added message- sent to client
		IF ($_req_id == 'CM' || $_req_id == 'SA') {
			IF ($_tt_info['cl_email'] == $_tt_info['hd_tt_cl_email'] || $_tt_info['hd_tt_cl_email'] == '') {
    			IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   					$_req_recip	= $_tt_info['hd_tt_cl_email'];
				} ELSE {
					$_req_recip	= $_tt_info['cl_name_first'].' '.$_tt_info['cl_name_last'].' <'.$_tt_info['hd_tt_cl_email'].'>';
				}
				$_req_to_name	= $_tt_info['cl_name_first'].' '.$_tt_info['cl_name_last'];
				$_req_to_email	= $_tt_info['cl_email'];
			} ELSE {
        			IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   					$_req_recip	= $_tt_info['hd_tt_cl_email'];
				} ELSE {
					$_req_recip	= $_tt_info['contacts_name_first'].' '.$_tt_info['contacts_name_last'].' <'.$_tt_info['hd_tt_cl_email'].'>';
				}
				$_req_to_name	= $_tt_info['contacts_name_first'].' '.$_tt_info['contacts_name_last'];
				$_req_to_email	= $_tt_info['hd_tt_cl_email'];
			}
		}

	# Set eMail Parameters (pre-eval template, some used in template)
		$mail['recip']		= $_req_recip;
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
			$mail['from']		= $_ainfo['c_email'];
			$mail['replyto']	= $_cinfo['c_email'];
		} ELSE {
			$mail['from']		= $_CCFG['_PKG_NAME_SHORT'].'-'.$_ainfo['c_name'].' <'.$_ainfo['c_email'].'>';
			$mail['replyto']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
		}
		$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_HDESK']['HD_EMAIL_SUBJECT_PRE'].' '.$adata['hd_tt_id'].' '.$_LANG['_HDESK']['HD_EMAIL_SUBJECT_SUF'];

	# Set MTP (Mail Template Parameters) array
		$_MTP['to_name']	= $_req_to_name;
		$_MTP['to_email']	= $_req_to_email;
		$_MTP['from_name']	= $_ainfo['c_name'];
		$_MTP['from_email']	= $_ainfo['c_email'];
		$_MTP['subject']	= $mail['subject'];
		$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];
		$_MTP['tt_url']	= BASE_HREF.'mod.php?mod=helpdesk&mode=view&hd_tt_id='.$adata['hd_tt_id'];

	# Check returned records, don't send if not 1
		$_ret = 1;
		IF ($_tt_info['numrows'] == 1) {
		# Load message template (processed)
			$mail['message'] = get_mail_template('email_helpdesk_tt_update', $_MTP);

		# Call basic email function (ret=1 on error)
			$_ret = do_mail_basic($mail);

		# Check for alert email enable on client add (not message)
			IF ($_req_id == 'CA' && $numrows_msgs == 0 && $_CCFG['HELPDESK_ALERT_EMAIL_ENABLE'] && $_CCFG['HELPDESK_ALERT_EMAIL_ADDRESS']) {

			# Set recipient to alert address
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   					$mail['recip']	= $_CCFG['HELPDESK_ALERT_EMAIL_ADDRESS'];
				} ELSE {
					$mail['recip']	= $_LANG['_HDESK']['HelpDesk_Alert'].' <'.$_CCFG['HELPDESK_ALERT_EMAIL_ADDRESS'].'>';
				}

			# Load message template (processed)
				$mail['cc']		= '';
				$mail['message']	= get_mail_template('email_helpdesk_tt_alert', $_MTP);

			# Call basic email function (ret=2 on error)
				$_ret2 = 1;
				$_ret2 = do_mail_pager($mail);
			}
		}

	# Check return
		IF ($_ret) {
			$_ret_msg = $_LANG['_HDESK']['HD_EMAIL_MSG_03_L1'];
			$_ret_msg .= '<br>'.$_LANG['_HDESK']['HD_EMAIL_MSG_03_L2'];
		} ELSE {
			$_ret_msg = $_LANG['_HDESK']['HD_EMAIL_MSG_04_PRE'].$_sp.$adata['hd_tt_id'].$_sp.$_LANG['_HDESK']['HD_EMAIL_MSG_04_SUF'];
		}

	# Check mode- if none, called from script, so no return
		IF ($adata['stage']) {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_HDESK']['HD_EMAIL_RESULT_TITLE'];

			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= $_ret_msg.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;

			$_mstr_flag = 1;
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=view&hd_tt_id='.$adata['hd_tt_id'], $_TCFG['_IMG_BACK_TO_TT_M'],$_TCFG['_IMG_BACK_TO_TT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Functions
**************************************************************/
?>