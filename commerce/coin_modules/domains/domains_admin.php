<?php
/**
 * Module: Domains (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Domains
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_domains.php
 */


# Code to handle file being loaded by URL
	IF (eregi('domains_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=domains');
		exit;
	}

/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do list field form for: Domains
function do_select_form_domains($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT dom_id, dom_domain';
		$query	.= ' FROM '.$_DBCFG['domains'];
		$query	.= ' ORDER BY dom_domain ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_DOMS']['Domains_Select'].':'.$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_DOMS']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($dom_id, $dom_domain) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$dom_id.'">'.$dom_domain.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="dom_domain" value="'.$dom_domain.'">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_DOMS']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
}


# Do Form for Add / Edit
function do_form_add_edit_domains($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build op dependent strings
		switch ($adata['mode']) {
			case "add":
				$mode_proper	= $_LANG['_DOMS']['B_Add'];
				$mode_button	= $_LANG['_DOMS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_DOMS']['B_Edit'];
				$mode_button	= $_LANG['_DOMS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_proper	= $_LANG['_DOMS']['B_Add'];
				$mode_button	= $_LANG['_DOMS']['B_Add'];
				break;
		}

	# Build common td start tag / strings (reduce text)
		$_td_str_left		= '<td class="TP1SML_NR" width="40%">';
		$_td_str_left_vtop	= '<td class="TP1SML_NR" valign="top" width="40%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="60%">';
		$_td_str_left_2	= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right_2	= '<td class="TP1SML_NL" width="15%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_DOMS']['Domains_Entry'].$_sp.'('.$_LANG['_DOMS']['some_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_DOMS']['DOM_ERR_ERR_HDR1'].'<br>'.$_LANG['_DOMS']['DOM_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['dom_id']) 			{$err_str .= $_LANG['_DOMS']['DOM_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['dom_cl_id']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR02']; $err_prv = 1;}
			IF ($aerr_entry['dom_domain']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR03']; $err_prv = 1;}
			IF ($aerr_entry['dom_user_name_cp']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR04']; $err_prv = 1;}
			IF ($aerr_entry['dom_user_pword_cp']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR05']; $err_prv = 1;}
			IF ($aerr_entry['dom_user_name_ftp']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR06']; $err_prv = 1;}
			IF ($aerr_entry['dom_user_pword_ftp']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_DOMS']['DOM_ERR_ERR07']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<div align="center">'.$_nl;
		$_cstr .= '<form name="dom_info" id="dom_info" method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="domains">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%" border="0">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'add') {
			$_cstr .= '('.$_LANG['_DOMS']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['dom_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dom_domain" SIZE=32 value="'.htmlspecialchars($adata['dom_domain']).'" maxlength="50">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		IF ($adata['dom_status'] == '') {$adata['dom_status'] = 0;}
		$_cstr .= '<tr>'.$_nl;

		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_domain_status('dom_status', $adata['dom_status']);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['dom_type'] == '') {$adata['dom_type'] = 0;}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_domain_type('dom_type', $adata['dom_type']);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_clients('dom_cl_id', $adata['dom_cl_id'], '0');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Registrar'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dom_registrar" SIZE=32 value="'.htmlspecialchars($adata['dom_registrar']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain_Expiration'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('dom_ts_expiration', $adata['dom_ts_expiration'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_SACC_Expiration'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('dom_sa_expiration', $adata['dom_sa_expiration'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'add' && $adata['dom_si_id'] == '') {$adata['dom_si_id'] = $_CCFG['DOM_DEFAULT_SERVER'];}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_server_info('dom_si_id', $adata['dom_si_id'], '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'add' && $adata['dom_ip'] == '') {$adata['dom_ip'] = $_CCFG['DOM_DEFAULT_IP'];}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Account_IP'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_ip" SIZE=20 value="'.$adata['dom_ip'].'" maxlength="16">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'add' && $adata['dom_path'] == '') {$adata['dom_path'] = $_CCFG['DOM_DEFAULT_PATH'];}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Account_Path'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_path" SIZE=40 value="'.htmlspecialchars($adata['dom_path']).'" maxlength="255">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Path_Temp'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_path_temp" SIZE=40 value="'.htmlspecialchars($adata['dom_path_temp']).'" maxlength="255">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'add' && $adata['dom_url_cp'] == '') {
			$adata['dom_url_cp'] = $_CCFG['DOM_DEFAULT_CP_URL'];
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_URL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_url_cp" SIZE=40 value="'.htmlspecialchars($adata['dom_url_cp']).'" maxlength="100">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_user_name_cp" SIZE=30 value="'.htmlspecialchars($adata['dom_user_name_cp']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['ENABLE_AUTOPASS_DOMAINS']) {$_autopass = do_password_create();}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_User_Password'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<input class="PMED_NL" type="text" name="dom_user_pword_cp" SIZE=30 value="'.htmlspecialchars($adata['dom_user_pword_cp']).'">'.$_nl;
		IF ($_CCFG['ENABLE_AUTOPASS_DOMAINS']) {
			$_cstr .= ' <script type="text/javascript">document.write("<input type=\"button\" name=\"gp\" value=\"'.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_TEXT'].'\" onclick=\"document.dom_info.dom_user_pword_cp.value=\''.$_autopass.'\'; document.dom_info.gp.disabled=\'disabled\'; document.dom_info.gp.value=\''.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_REMEMBER'].'\';\">")</script>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_FTP_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<input class="PMED_NL" type="text" name="dom_user_name_ftp" SIZE=30 value="'.htmlspecialchars($adata['dom_user_name_ftp']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['ENABLE_AUTOPASS_DOMAINS']) {$_autopass2 = do_password_create();}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_FTP_User_Password'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<input class="PMED_NL" type="text" name="dom_user_pword_ftp" SIZE=30 value="'.htmlspecialchars($adata['dom_user_pword_ftp']).'">'.$_nl;
		IF ($_CCFG['ENABLE_AUTOPASS_DOMAINS']) {
			$_cstr .= ' <script type="text/javascript">document.write("<input type=\"button\" name=\"gp2\" value=\"'.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_TEXT'].'\" onclick=\"document.dom_info.dom_user_pword_ftp.value=\''.$_autopass2.'\'; document.dom_info.gp2.disabled=\'disabled\'; document.dom_info.gp2.value=\''.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_REMEMBER'].'\';\">")</script>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_DOMS']['Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="dom_notes" COLS="60" ROWS="10">'.$adata['dom_notes'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="75%">'.$_nl;
	# Show "unlimited" instructions
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= '<td class="TP1SML_NC" colspan="4"><b>';
		$_cstr .= $_LANG['_DOMS']['Unlimited_Instructions'];
		$_cstr .= '</b></td></tr>';

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Domains'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_domains" SIZE=4 value="'.$adata['dom_allow_domains'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Disk_Space_Mb'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_disk_space_mb" SIZE=6 value="'.$adata['dom_allow_disk_space_mb'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SubDomains'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_subdomains" SIZE=4 value="'.$adata['dom_allow_subdomains'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Traffic_BW_Mb'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_traffic_mb" SIZE=6 value="'.$adata['dom_allow_traffic_mb'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Databases'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_databases" SIZE=4 value="'.$adata['dom_allow_databases'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_MailBoxes_POP'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= '<INPUT class="PMED_NL" TYPE=TEXT NAME="dom_allow_mailboxes" SIZE=4 value="'.$adata['dom_allow_mailboxes'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_WWW_Prefix'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_www_prefix', $adata['dom_enable_www_prefix'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SSI_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_ssi', $adata['dom_enable_ssi'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Web_User_Scripting'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_wu_scripting', $adata['dom_enable_wu_scripting'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_PHP_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_php', $adata['dom_enable_php'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_WebMail'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_webmail', $adata['dom_enable_webmail'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Enable_CGI_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_cgi', $adata['dom_enable_cgi'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_FrontPage_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_frontpage', $adata['dom_enable_frontpage'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_mod_perl_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_mod_perl', $adata['dom_enable_mod_perl'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Frontpage_SSL_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_fromtpage_ssl', $adata['dom_enable_fromtpage_ssl'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_ASP_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_asp', $adata['dom_enable_asp'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Webstats'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_stats', $adata['dom_enable_stats'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SSL_Support'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_ssl', $adata['dom_enable_ssl'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Error_Docs_Logs'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.$_nl;
		$_cstr .= do_select_list_no_yes('dom_enable_err_docs', $adata['dom_enable_err_docs'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_left_2.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_2.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="dom_id" value="'.$adata['dom_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>&nbsp;</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_DOMS']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_DOMS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=mail&dom_id='.$adata['dom_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>