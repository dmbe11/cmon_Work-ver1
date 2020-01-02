<?php
/**
 * Module: Command Center (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Summary
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_cc.php
 */


# Code to handle file being loaded by URL
	IF (eregi('cc_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=cc');
		exit;
	}


/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do Form for Client Search
function do_form_search_clients($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Clients']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr  = '<div align="center" width="75%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == '0') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == '1') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<input type="checkbox" name="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<input type="checkbox" name="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<input type="checkbox" name="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Company'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_company" SIZE=30 value="'.htmlspecialchars($adata['s_company']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_user_name" SIZE=30 value="'.htmlspecialchars($adata['s_user_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Email'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_email" SIZE=30 value="'.htmlspecialchars($adata['s_email']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where = ' WHERE (';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['clients'].".cl_join_ts >= '".$adata['s_ts_01']."'";
				$where .= ' AND '.$_DBCFG['clients'].".cl_join_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['clients'].".cl_join_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}
		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['clients'].".cl_join_ts >= '".$adata['s_ts_02']."'";
				$where .= ' AND '.$_DBCFG['clients'].".cl_join_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['clients'].".cl_join_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_id like '%".$adata['s_id']."%')";
		}
		IF ($adata['s_company']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_company like '%".$db_coin->db_sanitize_data($adata['s_company'])."%')";
		}
		IF ($adata['s_name_first']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_name_first like '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
		}
		IF ($adata['s_name_last']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_name_last like '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
		}

		IF ($adata['s_user_name']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_user_name like '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
		}

		IF ($adata['s_email']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(cl_email like '%".$db_coin->db_sanitize_data($adata['s_email'])."%')";

		# Addition for checking additional email addresses
		# Check if any additional emails exist.
			$ae_query	= 'SELECT * FROM '.$_DBCFG['clients_contacts'];

		# Do select and return check
			$ae_result	= $db_coin->db_query_execute($ae_query);
			$ae_numrows	= $db_coin->db_query_numrows($ae_result);

		# Process query results
			IF ($ae_numrows) {
				$where .= ' OR (('.$_DBCFG['clients_contacts'].".contacts_email LIKE '%".$db_coin->db_sanitize_data($adata['s_email'])."%')";
				$where .= ' AND ('.$_DBCFG['clients'].'.cl_id='.$_DBCFG['clients_contacts'].'.contacts_cl_id))';
			}
		}

		$where .= ')';

		$query  = 'SELECT ';
		IF ($ae_numrows) {$query .= 'DISTINCT';}
		$query .= ' cl_id, cl_company, cl_name_first, cl_name_last, cl_user_name, cl_email';
		$query .= ' FROM '.$_DBCFG['clients'];

	# Addition for checking additional email addresses
		IF ($adata['s_email'] && $ae_numrows) {$query .= ', '.$_DBCFG['clients_contacts'];}

		$query .= $where;
		$query .= ' ORDER BY cl_name_last ASC, cl_name_first ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="5">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Id'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Email'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			while(list($cl_id, $cl_company, $cl_name_first, $cl_name_last, $cl_user_name, $cl_email) = $db_coin->db_fetch_row($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$cl_id.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$cl_name_first.$_sp.$cl_name_last.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$cl_user_name.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$cl_email.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				IF ($_PERMS['AP10'] != 1) {
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=edit&cl_id='.$cl_id, $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
				}
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=client&cc_cl_id='.$cl_id, $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=view&cl_id='.$cl_id, $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;
		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="5"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		return $_out;
}


# Do Form for Domains Search
function do_form_search_domains($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;


	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="40%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="60%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Domains']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr  = '<div align="center" width="75%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == 0) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == 1) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Domain_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_domain" SIZE=30 value="'.htmlspecialchars($adata['s_domain']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Domain_Expiration'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_dom_expired'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_dom_expired" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Expired'].'</b>'.$_nl;
		IF ($adata['cb_dom_expires_in'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_dom_expires_in" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Within'].'</b>'.$_sp.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dom_expires_in_days" SIZE=5 value="'.$adata['dom_expires_in_days'].'">'.$_sp.'<b>'.$_LANG['_CC']['days'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_SACC_Expiration'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_sacc_expired'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_sacc_expired" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Expired'].'</b>'.$_nl;
		IF ($adata['cb_sacc_expires_in'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_sacc_expires_in" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Within'].'</b>'.$_sp.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="sacc_expires_in_days" SIZE=5 value="'.$adata['sacc_expires_in_days'].'">'.$_sp.'<b>'.$_LANG['_CC']['days'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_user_name" SIZE=30 value="'.htmlspecialchars($adata['s_user_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="40%">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="60%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= '('.$_DBCFG['clients'].'.cl_id='.$_DBCFG['domains'].'.dom_cl_id)';
		IF ($adata['s_domain']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['domains'].".dom_domain LIKE '%".$db_coin->db_sanitize_data($adata['s_domain'])."%')";
				$_search_cnt++;
			}

			IF ($adata['cb_dom_expired']) {
				$_uts = dt_get_uts();
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['domains'].".dom_ts_expiration <> ''";
				$where_2 .= ' AND '.$_DBCFG['domains'].".dom_ts_expiration < '".$_uts."')";
				$_search_cnt++;
			}

			IF ($adata['cb_dom_expires_in'] && $adata['dom_expires_in_days'] > 0) {
				$_uts = dt_get_uts();
				$_uts_plus = $_uts + (3600*24*$adata['dom_expires_in_days']);
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['domains'].".dom_ts_expiration > '".$_uts."'";
				$where_2 .= ' AND '.$_DBCFG['domains'].".dom_ts_expiration < '".$_uts_plus."')";
				$_search_cnt++;
			}

			IF ($adata['cb_sacc_expired']) {
				$_uts = dt_get_uts();
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['domains'].".dom_sa_expiration <> ''";
				$where_2 .= ' AND '.$_DBCFG['domains'].".dom_sa_expiration < '".$_uts."')";
				$_search_cnt++;
			}

			IF ($adata['cb_sacc_expires_in'] && $adata['sacc_expires_in_days'] > 0) {
				$_uts = dt_get_uts();
				$_uts_plus = $_uts + (3600*24*$adata['sacc_expires_in_days']);
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['domains'].".dom_sa_expiration > '".$_uts."'";
				$where_2 .= ' AND '.$_DBCFG['domains'].".dom_sa_expiration < '".$_uts_plus."')";
				$_search_cnt++;
			}

			IF ($adata['s_id']) {
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['clients'].".cl_id like '%".$db_coin->db_sanitize_data($adata['s_id'])."%')";
				$_search_cnt++;
			}

			IF ($adata['s_name_first']) {
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['clients'].".cl_name_first LIKE '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
				$_search_cnt++;
			}

			IF ($adata['s_name_last']) {
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['clients'].".cl_name_last LIKE '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
				$_search_cnt++;
			}

			IF ($adata['s_user_name']) {
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['clients'].".cl_user_name LIKE '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
				$_search_cnt++;
			}

			IF ($_search_cnt > 0) {$where .= ' AND ( '.$where_2.' )';}
			$where .= ')';

			$query  = 'SELECT *';
			$query .= ' FROM '.$_DBCFG['clients'].', '.$_DBCFG['domains'];
			$query .= $where;
			$query .= ' ORDER BY dom_domain ASC';

		# Do select / form if criteria entered
			IF ($_search_cnt > 0) {
				$result	= $db_coin->db_query_execute($query);
				$numrows	= $db_coin->db_query_numrows($result);

			# Build form output
				$_cstr .= '<br>'.$_nl;
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="7">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
			}

		# Process query results
			IF ($numrows && $_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Domain_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Domain_Expiration'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_SACC_Expiration'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				while ($row = $db_coin->db_fetch_array($result)) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.$_nl;
					IF ($_PERMS['AP10'] == 1) {$_pmode = 'view';} ELSE {$_pmode = 'edit';}
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode='.$_pmode.'&cl_id='.$row['cl_id'], $row['cl_id'],$row['cl_id'],'','');
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$row['cl_name_first'].$_sp.$row['cl_name_last'].'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$row[cl_user_name].'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$_nl;
					IF ($_PERMS['AP10'] == 1) {$_pmode = 'view';} ELSE {$_pmode = 'edit';}
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode='.$_pmode.'&dom_id='.$row['dom_id'], $row['dom_domain'],$row['dom_domain'],'','');
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($row['dom_ts_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($row['dom_sa_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.$_nl;
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=client&cc_cl_id='.$row['cl_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=view&cl_id='.$row['cl_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;

			} ELSE {
				IF ($_search_cnt > 0) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="7"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '</table>'.$_nl;
					$_cstr .= '</div>'.$_nl;
					$_cstr .= '<br>'.$_nl;
				}
			}

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		return $_out;
	}


# Do Form for Orders Search
function do_form_search_orders($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Orders']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr  = '<div align="center" width="75%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == '0') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == '1') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Order_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Domain_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_domain" SIZE=30 value="'.htmlspecialchars($adata['s_domain']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_cl" SIZE=10 value="'.$adata['s_id_cl'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Company'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_company" SIZE=30 value="'.htmlspecialchars($adata['s_company']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_user_name" SIZE=30 value="'.htmlspecialchars($adata['s_user_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Referred_By'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_referrer" SIZE=30 value="'.htmlspecialchars($adata['s_referrer']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Vendor'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_vendors('s_vendor_id', $adata['s_vendor_id'], '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Product'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_prods('s_prod_id', $adata['s_prod_id'], '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="sw" value="'.$adata[sw].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where = ' WHERE (';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['orders'].".ord_ts >= '".$adata['s_ts_01']."'";
				$where .= ' AND '.$_DBCFG['orders'].".ord_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['orders'].".ord_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['orders'].".ord_ts >= '".$adata['s_ts_02']."'";
				$where .= ' AND '.$_DBCFG['orders'].".ord_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
				$where .= '('.$_DBCFG['orders'].".ord_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_id LIKE '%".$adata['s_id']."%')";
		}

		IF ($adata['s_domain']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_domain LIKE '%".$db_coin->db_sanitize_data($adata['s_domain'])."%')";
		}

		IF ($adata['s_id_cl']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_cl_id LIKE '%".$adata['s_id_cl']."%')";
		}

		IF ($adata['s_company']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_company LIKE '%".$db_coin->db_sanitize_data($adata['s_company'])."%')";
		}

		IF ($adata['s_name_first']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_name_first LIKE '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
		}

		IF ($adata['s_name_last']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_name_last LIKE '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
		}

		IF ($adata['s_user_name']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_user_name like '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
		}

		IF ($adata['s_referrer']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_referred_by LIKE '%".$db_coin->db_sanitize_data($adata['s_referrer'])."%')";
		}

		IF ($adata['s_vendor_id']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_vendor_id LIKE '%".$adata['s_vendor_id']."%')";
		}

		IF ($adata['s_prod_id']) {
			IF ($_search_cnt > 0) {$where .= ' '.$_search_type.' ';}
			$_search_cnt++;
			$where .= "(ord_prod_id LIKE '%".$adata['s_prod_id']."%')";
		}

		$where .= ')';

		$query  = 'SELECT ord_id, ord_ts, ord_company, ord_name_first, ord_name_last, ord_domain, ord_user_name, ord_referred_by';
		$query .= ' FROM '.$_DBCFG['orders'];
		$query .= $where;
		$query .= ' ORDER BY ord_id ASC';

		# Do select / form if criteria entered
			IF ($_search_cnt > 0) {
				$result	= $db_coin->db_query_execute($query);
				$numrows	= $db_coin->db_query_numrows($result);

			# Build form output
				$_cstr .= '<br>'.$_nl;
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="5">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
			}

		# Process query results
			IF ($numrows && $_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Id'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Domain_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				while(list($ord_id, $ord_ts, $ord_company, $ord_name_first, $ord_name_last, $ord_domain, $ord_user_name, $ord_referred_by) = $db_coin->db_fetch_row($result)) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.$ord_id.'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$ord_name_first.$_sp.$ord_name_last.'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$ord_user_name.'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.$ord_domain.'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC">'.$_nl;
					IF ($_PERMS['AP10'] != 1) {
						$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=edit&ord_id='.$ord_id, $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
					}
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=mail&ord_id='.$ord_id, $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view&ord_id='.$ord_id, $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S'],'','');
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;

			} ELSE {
				IF ($_search_cnt > 0) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="5"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '</table>'.$_nl;
					$_cstr .= '</div>'.$_nl;
					$_cstr .= '<br>'.$_nl;
				}
			}

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		return $_out;
	}


# Do Form for Invoices Search
function do_form_search_invoices($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Invoices']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		IF (!$_CCFG['_IS_PRINT']) {
			$_cstr .= '<div align="center" width="75%">'.$_nl;
			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
			$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
			$_cstr .= '<option value="0"';
			IF ($adata['search_type'] == '0') {$_cstr .= ' selected';}
			$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
			$_cstr .= '<option value="1"';
			IF ($adata['search_type'] == '1') {$_cstr .= ' selected';}
			$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
			$_cstr .= '</select>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
			IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
			$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
			IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= $_sp.$_sp.$_nl;
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
			IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
			$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
			IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= $_sp.$_sp.$_nl;
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Invoice_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['Status'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_status_invoice('s_invc_status', $adata['s_invc_status'], 1).$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_cl" SIZE=10 value="'.$adata['s_id_cl'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_user_name" SIZE=30 value="'.htmlspecialchars($adata['s_user_name']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_cstr .= '</div>'.$_nl;
		}

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= ' ('.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id) ';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices'].".invc_ts >= '".$adata['s_ts_01']."'";
				$where_2 .= ' AND '.$_DBCFG['invoices'].".invc_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices'].".invc_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices'].".invc_ts >= '".$adata['s_ts_02']."'";
				$where_2 .= ' AND '.$_DBCFG['invoices'].".invc_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices'].".invc_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices'].".invc_id LIKE '%".$adata['s_id']."%')";
			$_search_cnt++;
		}

		IF ($adata['s_invc_status'] != 'ALL') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices'].".invc_status='".$db_coin->db_sanitize_data($adata['s_invc_status'])."')";
			$_search_cnt++;
		}

		IF ($adata['s_id_cl']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices'].".invc_cl_id LIKE '%".$adata['s_id_cl']."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_first']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_name_first LIKE '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_last']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_name_last LIKE '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_user_name']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_user_name LIKE '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ( '.$where_2.' )';}
		$where .= ')';

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query .= $where;
		$query .= ' ORDER BY invc_ts ASC, invc_id ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			IF ($_CCFG['_IS_PRINT']) {
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL" colspan="'.(11-$_CCFG['_IS_PRINT']-($adata['s_invc_status'] == 'ALL')).'">'.$_nl;
				IF ($adata['cb_and_after'])	{$_cstr .= 'From: ';}
				IF ($adata['s_ts_01'])		{$_cstr .= dt_make_datetime($adata['s_ts_01'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);}
				IF ($adata['cb_and_before'])	{$_cstr .= $_sp.$_sp.$_sp.$_sp.$_sp.'To: ';}
				IF ($adata['s_ts_02'])		{$_cstr .= dt_make_datetime($adata['s_ts_02'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);}
				$_cstr .= '<br />';
				IF ($adata['s_invc_status'] != 'ALL')	{$_cstr .= 'Status: '.$adata['s_invc_status'].'<br />';}
			} ELSE {
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="'.(11-$_CCFG['_IS_PRINT']-($adata['s_invc_status'] == 'ALL')).'">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b>'.$_nl;
			}
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		$_TOTALS = array("amt"=>0, "tax1"=>0, "tax2"=>0, "total"=>0, "due"=>0);
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Id'].$_sp.'</b></td>'.$_nl;
			IF ($adata['s_invc_status'] == 'ALL') {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['Status'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Amount'].$_sp.'</b></td>'.$_nl;
			IF ($_CCFG['INVC_TAX_01_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			}
			IF ($_CCFG['INVC_TAX_02_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['Total'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['Balance_Due'].$_sp.'</b></td>'.$_nl;
			IF (!$_CCFG['_IS_PRINT']) {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '</tr>'.$_nl;
			while ($row = $db_coin->db_fetch_array($result)) {
				$_TOTALS['amt']	+= $row['invc_subtotal_cost'];
				$_TOTALS['tax1']	+= $row['invc_tax_01_amount'];
				$_TOTALS['tax2']	+= $row['invc_tax_02_amount'];
				$_TOTALS['total']	+= $row['invc_total_cost'];
				$_TOTALS['due']	+= ($row['invc_total_cost']-$row['invc_total_paid']);

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.$row['invc_id'].'</td>'.$_nl;
				IF ($adata['s_invc_status'] == 'ALL') {
					$_cstr .= '<td class="TP3SML_NL">'.$row['invc_status'].'</td>'.$_nl;
				}
				$_cstr .= '<td class="TP3SML_NL">'.dt_make_datetime($row['invc_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['cl_name_first'].$_sp.$row['cl_name_last'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_subtotal_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				IF ($_CCFG['INVC_TAX_01_ENABLE']) {
					$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_tax_01_amount'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
				IF ($_CCFG['INVC_TAX_02_ENABLE']) {
					$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_tax_02_amount'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_total_cost']-$row['invc_total_paid'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				If (!$_CCFG['_IS_PRINT']) {
					$_cstr .= '<td class="TP3SML_NL">'.$_nl;
					IF ($_PERMS['AP10'] != 1) {$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');}
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=mail&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_cstr .= '</td>'.$_nl;
				}
				$_cstr .= '</tr>'.$_nl;
			}

		# Totals row
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NR" colspan="'.(3+($adata['s_invc_status'] == 'ALL')).'"><b>'.$_LANG['_CC']['Total'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['amt'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			IF ($_CCFG['INVC_TAX_01_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['tax1'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			}
			IF ($_CCFG['INVC_TAX_02_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['tax2'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['total'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['due'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			IF (!$_CCFG['_IS_PRINT']) {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="'.(10-$_CCFG['_IS_PRINT']).'"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

	# Build PRINT GET URL
		$_url  = '&mode=search&sw=invoices';
		$_url .= '&search_type='.$adata['search_type'];
		$_url .= '&cb_on_01='.$adata['cb_on_01'];
		$_url .= '&cb_and_after='.$adata['cb_and_after'];
		$_url .= '&cb_on_02='.$adata['cb_on_02'];
		$_url .= '&cb_and_before='.$adata['cb_and_before'];
		$_url .= '&s_id='.$adata['s_id'];
		IF ($adata['s_ts_01'])		{$_url .= '&s_ts_01='.$adata['s_ts_01'];}
		IF ($adata['s_ts_01_year'])	{$_url .= '&s_ts_01_year='.$adata['s_ts_01_year'];}
		IF ($adata['s_ts_01_month'])	{$_url .= '&s_ts_01_month='.$adata['s_ts_01_month'];}
		IF ($adata['s_ts_01_day'])	{$_url .= '&s_ts_01_day='.$adata['s_ts_01_day'];}
		IF ($adata['s_ts_02'])		{$_url .= '&s_ts_02='.$adata['s_ts_02'];}
		IF ($adata['s_ts_02_year'])	{$_url .= '&s_ts_02_year='.$adata['s_ts_02_year'];}
		IF ($adata['s_ts_02_month'])	{$_url .= '&s_ts_02_month='.$adata['s_ts_02_month'];}
		IF ($adata['s_ts_02_day'])	{$_url .= '&s_ts_02_day='.$adata['s_ts_02_day'];}
		IF ($adata['s_invc_status'])	{$_url .= '&s_invc_status='.$adata['s_invc_status'];}
		IF ($adata['s_id_cl'])		{$_url .= '&s_id_cl='.$adata['s_id_cl'];}
		IF ($adata['s_name_first'])	{$_url .= '&s_name_first='.$adata['s_name_first'];}
		IF ($adata['s_name_last'])	{$_url .= '&s_name_last='.$adata['s_name_last'];}
		IF ($adata['s_user_name'])	{$_url .= '&s_user_name='.$adata['s_user_name'];}

	# Footer buttons
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('mod_print.php?mod=cc'.$_url, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		IF ($_CCFG['_IS_PRINT']) {$_p = 0;} ELSE {$_p = 1;}
		$_out .= do_mod_block_it($_tstr, $_cstr, $_p, $_mstr, 1);
		$_out .= '<br>'.$_nl;

		return $_out;
	}


# Do Form for Invoice Transaction Search
function do_form_search_trans($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Transactions']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr .= '<div align="center" width="90%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == 0) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == 1) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_trans_type('s_it_type', $adata['s_it_type']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Origin'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_trans_origin('s_it_origin', $adata['s_it_origin']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Invoice_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_cl" SIZE=10 value="'.$adata['s_id_cl'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= '('.$_DBCFG['invoices_trans'].'.it_invc_id='.$_DBCFG['invoices'].'.invc_id)';
		$where .= ' AND ';
		$where .= '('.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id)';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices_trans'].".it_ts >= '".$adata['s_ts_01']."'";
				$where_2 .= ' AND '.$_DBCFG['invoices_trans'].".it_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices_trans'].".it_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['invoices_trans'].".it_ts >= '".$adata['s_ts_02']."'";
				$where_2 .= ' AND '.$_DBCFG['invoices_trans'].".it_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata[s_ts_02]+86399;
				IF ( $_search_cnt > 0 ) { $where_2 .= " ".$_search_type." "; }
				$where_2 .= "(".$_DBCFG['invoices_trans'].".it_ts <= '$_ts_02_end')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_it_type'] != '') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices_trans'].".it_type LIKE '%".$db_coin->db_sanitize_data($adata['s_it_type'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_it_origin'] != '') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices_trans'].".it_origin LIKE '%".$db_coin->db_sanitize_data($adata['s_it_origin'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['invoices'].".invc_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_id_cl']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id_cl'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_user_name']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_user_name LIKE '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ('.$where_2.')';}
		$where .= ')';

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['invoices_trans'].', '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query .= $where;
		$query .= ' ORDER BY it_id ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="7">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Origin'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Description'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Amount'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			while ($row = $db_coin->db_fetch_array($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				IF ($_PERMS['AP10'] == 1) {$_pmode = 'view';} ELSE {$_pmode = 'edit';}
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode='.$_pmode.'&cl_id='.$row['cl_id'], $row['cl_id'],$row['cl_id'],'','');
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$_CCFG['INV_TRANS_TYPE'][$row['it_type']].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['it_desc'].'</td>'.$_nl;
				IF ($row['it_type'] != 0) {$row['it_amount'] = $row['it_amount'] * -1;}
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['it_amount'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
						$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=trans&it_id='.$row['it_id'].'&it_type='.$row['it_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
					}
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="7"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		return $_out;
	}


# Do Form for Helpdesk Search
function do_form_search_helpdesk($adata) {
	# Dim some Vars:
		global $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Helpdesk']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr .= '<div align="center" width="75%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == '0') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == '1') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Ticket_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_cl" SIZE=10 value="'.$adata['s_id_cl'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_user_name" SIZE=30 value="'.htmlspecialchars($adata['s_user_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= '('.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_DBCFG['clients'].'.cl_id)';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_time_stamp >= '".$adata['s_ts_01']."'";
				$where_2 .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_time_stamp <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_time_stamp >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_time_stamp >= '".$adata['s_ts_02']."'";
				$where_2 .= ' AND '.$_DBCFG['helpdesk'].".hd_tt_time_stamp <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_time_stamp <= '".$_ts_02_end."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_id_cl']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['helpdesk'].".hd_tt_cl_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id_cl'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_first']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_name_first LIKE '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_last']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_name_last LIKE '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_user_name']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['clients'].".cl_user_name LIKE '%".$db_coin->db_sanitize_data($adata['s_user_name'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ('.$where_2.')';}
		$where .= ')';

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['helpdesk'].', '.$_DBCFG['clients'];
		$query .= $where;
		$query .= ' ORDER BY hd_tt_id ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="5">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Id'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_User_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Subject'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			while ($row = $db_coin->db_fetch_array($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$row['hd_tt_id'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['cl_name_first'].$_sp.$row['cl_name_last'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['cl_user_name'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['hd_tt_subject'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=mail&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=view&hd_tt_id='.$row['hd_tt_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="5"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		return $_out;
}


# Do Form for Bills Search
function do_form_search_bills($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Include bills common functions file
		require_once(PKG_PATH_MDLS.'bills/bills_common.php');

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Bills']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		IF (!$_CCFG['_IS_PRINT']) {
			$_cstr .= '<div align="center" width="75%">'.$_nl;
			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
			$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
			$_cstr .= '<option value="0"';
			IF ($adata['search_type'] == '0') {$_cstr .= ' selected';}
			$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
			$_cstr .= '<option value="1"';
			IF ($adata['search_type'] == '1') {$_cstr .= ' selected';}
			$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
			$_cstr .= '</select>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
			IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
			$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
			IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= $_sp.$_sp.$_nl;
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
			IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
			$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
			IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= $_sp.$_sp.$_nl;
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Bill_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['Status'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_status_bill('s_bill_status', $adata['s_bill_status'], 1).$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_s" SIZE=10 value="'.$adata['s_id_s'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Company'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_company" SIZE=30 value="'.htmlspecialchars($adata['s_company']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_First_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=30 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=30 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_cstr .= '</div>'.$_nl;
		}

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= ' ('.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id) ';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills'].".bill_ts >= '".$adata['s_ts_01']."'";
				$where_2 .= ' AND '.$_DBCFG['bills'].".bill_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills'].".bill_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills'].".bill_ts >= '".$adata['s_ts_02']."'";
				$where_2 .= ' AND '.$_DBCFG['bills'].".bill_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills'].".bill_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills'].".bill_id LIKE '%".$adata['s_id']."%')";
			$_search_cnt++;
		}

		IF ($adata['s_bill_status'] != 'ALL') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills'].".bill_status='".$db_coin->db_sanitize_data($adata['s_bill_status'])."')";
			$_search_cnt++;
		}

		IF ($adata['s_id_s']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills'].".bill_s_id LIKE '%".$adata['s_id_s']."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_first']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['suppliers'].".s_name_first LIKE '%".$db_coin->db_sanitize_data($adata['s_name_first'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_name_last']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['suppliers'].".s_name_last LIKE '%".$db_coin->db_sanitize_data($adata['s_name_last'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_company']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['suppliers'].".s_company LIKE '%".$db_coin->db_sanitize_data($adata['s_company'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ( '.$where_2.' )';}
		$where .= ')';

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$query .= $where;
		$query .= ' ORDER BY bill_id ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			IF ($_CCFG['_IS_PRINT']) {
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL" colspan="'.(11-$_CCFG['_IS_PRINT']-($adata['s_bill_status'] == 'ALL')).'">'.$_nl;
				IF ($adata['cb_and_after'])	{$_cstr .= 'From: ';}
				IF ($adata['s_ts_01'])		{$_cstr .= dt_make_datetime($adata['s_ts_01'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);}
				IF ($adata['cb_and_before'])	{$_cstr .= $_sp.$_sp.$_sp.$_sp.$_sp.'To: ';}
				IF ($adata['s_ts_02'])		{$_cstr .= dt_make_datetime($adata['s_ts_02'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);}
				$_cstr .= '<br />';
				IF ($adata['s_bill_status'] != 'ALL')	{$_cstr .= 'Status: '.$adata['s_bill_status'].'<br />';}
			} ELSE {
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="'.(11-$_CCFG['_IS_PRINT']-($adata['s_bill_status'] == 'ALL')).'">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b>'.$_nl;
			}
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		$_TOTALS = array("amt"=>0, "tax1"=>0, "tax2"=>0, "total"=>0, "due"=>0);
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Id'].$_sp.'</b></td>'.$_nl;
			IF ($adata['s_bill_status'] == 'ALL') {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['Status'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Company'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Amount'].$_sp.'</b></td>'.$_nl;
			IF ($_CCFG['BILL_TAX_01_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.$_CCFG['BILL_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			}
			IF ($_CCFG['BILL_TAX_02_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.$_CCFG['BILL_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['Total'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['Balance_Due'].$_sp.'</b></td>'.$_nl;
			IF (!$_CCFG['_IS_PRINT']) {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '</tr>'.$_nl;
			while ($row = $db_coin->db_fetch_array($result)) {
				$_TOTALS['amt']	+= $row['bill_subtotal_cost'];
				$_TOTALS['tax1']	+= $row['bill_tax_01_amount'];
				$_TOTALS['tax2']	+= $row['bill_tax_02_amount'];
				$_TOTALS['total']	+= $row['bill_total_cost'];
				$_TOTALS['due']	+= ($row['bill_total_cost']-$row['bill_total_paid']);

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.$row['bill_id'].'</td>'.$_nl;
				IF ($adata['s_bill_status'] == 'ALL') {
					$_cstr .= '<td class="TP3SML_NL">'.$row['bill_status'].'</td>'.$_nl;
				}
				$_cstr .= '<td class="TP3SML_NL">'.dt_make_datetime($row['bill_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['s_company'].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_subtotal_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				IF ($_CCFG['BILL_TAX_01_ENABLE']) {
					$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_tax_01_amount'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
				IF ($_CCFG['BILL_TAX_02_ENABLE']) {
					$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_tax_02_amount'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_total_cost']-$row['bill_total_paid'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				If (!$_CCFG['_IS_PRINT']) {
					$_cstr .= '<td class="TP3SML_NL">'.$_nl;
					IF ($_PERMS['AP10'] != 1) {$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=bills&mode=edit&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');}
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_cstr .= '</td>'.$_nl;
				}
				$_cstr .= '</tr>'.$_nl;
			}

		# Totals row
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NR" colspan="'.(3+($adata['s_bill_status'] == 'ALL')).'"><b>'.$_LANG['_CC']['Total'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['amt'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			IF ($_CCFG['BILL_TAX_01_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['tax1'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			}
			IF ($_CCFG['BILL_TAX_02_ENABLE']) {
				$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['tax2'],1,0,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			}
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['total'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.do_currency_format($_TOTALS['due'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b></td>'.$_nl;
			IF (!$_CCFG['_IS_PRINT']) {
				$_cstr .= '<td class="TP3SML_NL"><b>'.$_sp.'</b></td>'.$_nl;
			}
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="'.(10-$_CCFG['_IS_PRINT']).'"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

	# Build PRINT GET URL
		$_url  = '&mode=search&sw=bills';
		$_url .= '&search_type='.$adata['search_type'];
		$_url .= '&cb_on_01='.$adata['cb_on_01'];
		$_url .= '&cb_and_after='.$adata['cb_and_after'];
		$_url .= '&cb_on_02='.$adata['cb_on_02'];
		$_url .= '&cb_and_before='.$adata['cb_and_before'];
		$_url .= '&s_id='.$adata['s_id'];
		IF ($adata['s_ts_01'])		{$_url .= '&s_ts_01='.$adata['s_ts_01'];}
		IF ($adata['s_ts_01_year'])	{$_url .= '&s_ts_01_year='.$adata['s_ts_01_year'];}
		IF ($adata['s_ts_01_month'])	{$_url .= '&s_ts_01_month='.$adata['s_ts_01_month'];}
		IF ($adata['s_ts_01_day'])	{$_url .= '&s_ts_01_day='.$adata['s_ts_01_day'];}
		IF ($adata['s_ts_02'])		{$_url .= '&s_ts_02='.$adata['s_ts_02'];}
		IF ($adata['s_ts_02_year'])	{$_url .= '&s_ts_02_year='.$adata['s_ts_02_year'];}
		IF ($adata['s_ts_02_month'])	{$_url .= '&s_ts_02_month='.$adata['s_ts_02_month'];}
		IF ($adata['s_ts_02_day'])	{$_url .= '&s_ts_02_day='.$adata['s_ts_02_day'];}
		IF ($adata['s_bill_status'])	{$_url .= '&s_bill_status='.$adata['s_bill_status'];}
		IF ($adata['s_id_s'])		{$_url .= '&s_id_s='.$adata['s_id_s'];}
		IF ($adata['s_name_first'])	{$_url .= '&s_name_first='.$adata['s_name_first'];}
		IF ($adata['s_name_last'])	{$_url .= '&s_name_last='.$adata['s_name_last'];}
		IF ($adata['s_company'])	{$_url .= '&s_company='.$adata['s_company'];}

	# Footer buttons
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('mod_print.php?mod=cc'.$_url, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		IF ($_CCFG['_IS_PRINT']) {$_p = 0;} ELSE {$_p = 1;}
		$_out .= do_mod_block_it($_tstr, $_cstr, $_p, $_mstr, 1);
		$_out .= '<br>'.$_nl;

		return $_out;
	}

# Do Form for Bill Transaction Search
function do_form_search_bill_trans($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = do_tstr_search_list($_LANG['_CC']['Search_Bill_Transactions']);

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr .= '<div align="center" width="90%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="billtrans">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == 0) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == 1) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_CC']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_01'] <= 0 || $adata['s_ts_01'] == '') {$adata['s_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_01', $adata['s_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_ts_02'] <= 0 || $adata['s_ts_02'] == '') {$adata['s_ts_02'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_ts_02', $adata['s_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_CC']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_bill_trans_type('s_bt_type', $adata['s_bt_type']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Origin'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_search_select_list_bill_trans_origin('s_bt_origin', $adata['s_bt_origin']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Bill_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id" SIZE=10 value="'.$adata['s_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_id_s" SIZE=10 value="'.$adata['s_id_s'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_CC']['l_Company'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_company" SIZE=30 value="'.htmlspecialchars($adata['s_company']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_CC']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_CC']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= '('.$_DBCFG['bills_trans'].'.bt_bill_id='.$_DBCFG['bills'].'.bill_id)';
		$where .= ' AND ';
		$where .= '('.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id)';

		IF ($adata['s_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills_trans'].".bt_ts >= '".$adata['s_ts_01']."'";
				$where_2 .= ' AND '.$_DBCFG['bills_trans'].".bt_ts <= '".$_ts_01_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills_trans'].".bt_ts >= '".$adata['s_ts_01']."')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['bills_trans'].".it_ts >= '".$adata['s_ts_02']."'";
				$where_2 .= ' AND '.$_DBCFG['bills_trans'].".bt_ts <= '".$_ts_02_end."')";
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata[s_ts_02]+86399;
				IF ( $_search_cnt > 0 ) { $where_2 .= " ".$_search_type." "; }
				$where_2 .= "(".$_DBCFG['bills_trans'].".bt_ts <= '$_ts_02_end')";
				$_search_cnt++;
			}
		}

		IF ($adata['s_bt_type'] != '') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills_trans'].".bt_type LIKE '%".$db_coin->db_sanitize_data($adata['s_bt_type'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_bt_origin'] != '') {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills_trans'].".bt_origin LIKE '%".$db_coin->db_sanitize_data($adata['s_bt_origin'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_id']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['bills'].".bill_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_id_s']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['suppliers'].".s_id LIKE '%".$db_coin->db_sanitize_data($adata['s_id_s'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_company']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['suppliers'].".s_company LIKE '%".$db_coin->db_sanitize_data($adata['s_company'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ('.$where_2.')';}
		$where .= ')';

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['bills_trans'].', '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$query .= $where;
		$query .= ' ORDER BY bt_id ASC';

	# Do select / form if criteria entered
		IF ($_search_cnt > 0) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Build form output
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center">'.$_nl;
			$_cstr .= '<table width="90%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="7">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_CC']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
		}

	# Process query results
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Date'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Origin'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL"><b>'.$_LANG['_CC']['l_Description'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR"><b>'.$_LANG['_CC']['l_Amount'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC"><b>'.$_LANG['_CC']['l_Actions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			while ($row = $db_coin->db_fetch_array($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				IF ($_PERMS['AP10'] == 1) {$_pmode = 'view';} ELSE {$_pmode = 'edit';}
				$_cstr .= do_nav_link('admin.php?cp=suppliers&op='.$_pmode.'&s_id='.$row['s_id'], $row['s_id'],$row['s_id'],'','');
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bt_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$_CCFG['BILL_TRANS_TYPE'][$row['bt_type']].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$_CCFG['BILL_TRANS_ORIGIN'][$row['bt_origin']].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$row['bt_desc'].'</td>'.$_nl;
				IF ($row['bt_type'] != 0) {$row['bt_amount'] = $row['bt_amount'] * -1;}
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['bt_amount'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
						$_cstr .= do_nav_link('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$row['bt_id'].'&bt_type='.$row['bt_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
					}
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="7"><p><p><b>'.$_LANG['_CC']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		return $_out;
	}


function do_search_invoiced_products($adata) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT invc_id, invc_status, invc_ts, invc_ts_due, invc_total_cost FROM '.$_DBCFG['invoices'];
		$query .= ' LEFT JOIN '.$_DBCFG['invoices_items'];
		$query .= ' ON '.$_DBCFG['invoices_items'].'.ii_invc_id='.$_DBCFG['invoices'].'.invc_id';
		$query .= ' WHERE invc_ts >= '.$adata['report_start'].' AND invc_ts <= '.$adata['report_end'];
		$query .= " AND ii_item_name='".$db_coin->db_sanitize_data($adata['ii_item_name'])."'";
		$query .= ' ORDER BY invc_id DESC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_CC']['Search_Invoiced_Products'];

	# Process query results
		IF ($numrows) {

		# Build form output
			$_cstr  = '<div align="left">'.$_nl;
			$_cstr .= '<p><b>'.$_LANG['_CC']['Invoices_With_Product'].$_sp.$adata['ii_item_name'].'</b></p>'.$_nl;
			$_cstr .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE">';
			$_cstr .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Invoices'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Status'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Date_Issued'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Date_Due'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BR">'.$_LANG['_CC']['l_Amount'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_invc_ttl = 0;
			while(list($invc_id, $invc_status, $invc_ts, $invc_ts_due, $invc_total_cost) = $db_coin->db_fetch_row($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.$invc_id.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.$invc_status.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.dt_make_datetime($invc_ts, $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL">'.dt_make_datetime($invc_ts_due, $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($invc_total_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP1SML_NL">';
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_cstr .= do_nav_link('mod.php?mod=invoices&mode=view&invc_id='.$invc_id, $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
				} ELSE {
					$_cstr .= $_sp;
				}
				$_cstr .= '</a></td>'.$_nl;
				$_cstr .= '</tr>';
				$_invc_ttl++;
			}
			$_cstr .= '<tr class="BLK_DEF_ENTRY">';
			$_cstr .= '<td class="TP1SML_BR" colspan="5">'.$_invc_ttl.$_sp.$_LANG['_CC']['lc_products'].'</td>'.$_nl;
			$_cstr .= '<td class="TP1SML_BL">'.$_sp.'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;

		} ELSE {
			$_cstr  = $_LANG['_CC']['No_Items_Found'];
		}

	# Do Footer
		$_mstr = '';

	# Call block it function
		return do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
}

function do_search_billed_items($adata) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT DISTINCT bill_id, bill_status, bill_ts, bill_ts_due, bill_total_cost FROM '.$_DBCFG['bills'];
		$query .= ' LEFT JOIN '.$_DBCFG['bills_items'];
		$query .= ' ON '.$_DBCFG['bills_items'].'.bi_bill_id='.$_DBCFG['bills'].'.bill_id';
		$query .= ' WHERE bill_ts >= '.$adata['report_start'].' AND bill_ts <= '.$adata['report_end'];
		$query .= " AND bi_item_name='".$db_coin->db_sanitize_data($adata['bi_item_name'])."'";
		$query .= ' ORDER BY bill_id DESC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_CC']['Search_Billed_Items'];

	# Process query results
		IF ($numrows) {

		# Build form output
			$_cstr  = '<div align="left">'.$_nl;
			$_cstr .= '<p><b>'.$_LANG['_CC']['Bills_With_Item'].$_sp.$adata['bi_item_name'].'</b></p>'.$_nl;
			$_cstr .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_cstr .= '<tr class="BLK_DEF_TITLE">';
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Bill_ID'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Status'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Date_Issued'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Date_Due'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Amount'].'</td>'.$_nl;
			$_cstr .= '<td class="TP3MED_BL">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_bill_ttl = 0;
			while(list($bill_id, $bill_status, $bill_ts, $bill_ts_due, $bill_total_cost) = $db_coin->db_fetch_row($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$bill_id.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.$bill_status.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($bill_ts, $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC">'.dt_make_datetime($bill_ts_due, $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($bill_total_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_cstr .= '<td class="TP1SML_NL">';
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_cstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$bill_id, $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
				} ELSE {
					$_cstr .= $_sp;
				}
				$_cstr .= '</a></td>'.$_nl;
				$_cstr .= '</tr>';
				$_bill_ttl++;
			}
			$_cstr .= '<tr class="BLK_DEF_ENTRY">';
			$_cstr .= '<td class="TP1SML_BR" colspan="5">'.$_bill_ttl.$_sp.$_LANG['_CC']['lc_bill_s'].'</td>'.$_nl;
			$_cstr .= '<td class="TP1SML_BR">'.$_sp.'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;

		} ELSE {
			$_cstr  = $_LANG['_CC']['No_Items_Found'];
		}

	# Do Footer
		$_mstr = '';

	# Call block it function
		return do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
}

/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>