<?php
/**
 * Module: Domains (Common Functions)
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
	IF (eregi('domains_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=domains');
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
	#	IF (!$_GPV['dom_id'])			{$err_entry['flag'] = 1; $err_entry['dom_id'] = 1;}
		IF (!$_GPV['dom_cl_id'])			{$err_entry['flag'] = 1; $err_entry['dom_cl_id'] = 1;}
		IF (!$_GPV['dom_domain'])		{$err_entry['flag'] = 1; $err_entry['dom_domain'] = 1;}
	#	IF (!$_GPV['dom_registrar'])		{$err_entry['flag'] = 1; $err_entry['dom_registrar'] = 1;}
	#	IF (!$_GPV['dom_ts_expiration'])	{$err_entry['flag'] = 1; $err_entry['dom_ts_expiration'] = 1;}
	#	IF (!$_GPV['dom_sa_expiration'])	{$err_entry['flag'] = 1; $err_entry['dom_sa_expiration'] = 1;}
	#	IF (!$_GPV['dom_user_name_cp'])	{$err_entry['flag'] = 1; $err_entry['dom_user_name_cp'] = 1;}
	#	IF (!$_GPV['dom_user_pword_cp'])	{$err_entry['flag'] = 1; $err_entry['dom_user_pword_cp'] = 1;}
	#	IF (!$_GPV['dom_user_name_ftp'])	{$err_entry['flag'] = 1; $err_entry['dom_user_name_ftp'] = 1;}
	#	IF (!$_GPV['dom_user_pword_ftp'])	{$err_entry['flag'] = 1; $err_entry['dom_user_pword_ftp'] = 1;}
	#	IF (!$_GPV['dom_notes'])			{$err_entry['flag'] = 1; $err_entry['dom_notes'] = 1;}

		return $err_entry;
}


# Do display entry (individual entry)
function do_display_entry($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['domains'].', '.$_DBCFG['server_info'].', '.$_DBCFG['clients'];
		$query	.= ' WHERE '.$_DBCFG['domains'].'.dom_si_id='.$_DBCFG['server_info'].'.si_id';
		$query	.= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['domains'].'.dom_id='.$adata['dom_id'];

	# Set to logged in Client ID if not admin to avoid seeing other client domain id's
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_SEC['_suser_id'];}

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build common td start tag / strings (reduce text)
		$_td_str_left		= '<td class="TP1SML_NR" width="40%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="60%">';
		$_td_str_left_2	= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right_2	= '<td class="TP1SML_NL" width="15%">';

	# Get current date
        $todayis = dt_get_uts();

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Build Title String, Content String, and Footer Menu String
				$_tstr .= $_LANG['_DOMS']['View_Client_Domain_ID'].$_sp.$row['dom_id'];

            # Add "return to client" button if admin
				IF ($_SEC['_sadmin_flg'] && !$_CCFG['_IS_PRINT']) {
                    $_tstr .= ' <a href="mod.php?mod=clients&mode=view&cl_id='.$row['dom_cl_id'].'">'.$_TCFG['_IMG_BACK_TO_CLIENT_M'].'</a>';
				}

				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<table width="90%">'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain_ID'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_id'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_domain'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Status'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_CCFG['DOM_STATUS'][$row['dom_status']].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Type'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_CCFG['DOM_TYPE'][$row['dom_type']].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Client_ID'].$_sp.'</b></td>'.$_nl;

				$_cinfo = get_contact_client_info($row['dom_cl_id'] );
				$_cstr .= $_td_str_right.$row['dom_cl_id'].' - '.$_cinfo['cl_name_last'].', '.$_cinfo['cl_name_first'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Registrar'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_registrar'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Domain_Expiration'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right;
			# Display text "Expired" rather than a date in the past
				IF (($todayis > $row['dom_ts_expiration']) && ($row['dom_ts_expiration'])) {
				    $_cstr .= $_LANG['_DOMS']['Expired'];
				} ELSE {
				    $_cstr .= dt_make_datetime($row['dom_ts_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_SACC_Expiration'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right;
			# Display text "Expired" rather than a date in the past
				IF (($todayis > $row['dom_sa_expiration']) && ($row['dom_sa_expiration'])) {
				    $_cstr .= $_LANG['_DOMS']['Expired'];
				} ELSE {
				    $_cstr .= dt_make_datetime($row['dom_sa_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				}
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_get_server_name($row['dom_si_id']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Account_IP'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_ip'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Account_Path'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_path'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Server_Path_Temp'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_path_temp'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_URL'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['dom_url_cp'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				IF ($_SEC['_sadmin_flg'] || $_CCFG['SHOW_PASSWORDS_TO_CLIENTS']) {
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_User_Name'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['dom_user_name_cp'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_Control_Panel_User_Password'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['dom_user_pword_cp'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_FTP_User_Name'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['dom_user_name_ftp'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['l_FTP_User_Password'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['dom_user_pword_ftp'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

				$_cstr .= '<tr valign="top">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_DOMS']['Notes'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.nl2br($row['dom_notes']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '<br>'.$_nl;
				$_cstr .= '<table cellpadding="5" width="75%">'.$_nl;

			# Show account allowances
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Domains'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_domains'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_domains'];
				}
				$_cstr.'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Disk_Space_Mb'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_disk_space_mb'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_disk_space_mb'];
				}
				$_cstr.'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SubDomains'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_subdomains'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_subdomains'];
				}
				$_cstr.'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Traffic_BW_Mb'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_traffic_mb'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_traffic_mb'];
				}
				$_cstr.'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Databases'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_databases'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_databases'];
				}
				$_cstr.'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_MailBoxes_POP'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2;
			# Display text "unlimited, or number allowed
				IF ($row['dom_allow_mailboxes'] == "-1") {
					$_cstr .= $_LANG['_DOMS']['Unlimited'];
				} ELSE {
					$_cstr .= $row['dom_allow_mailboxes'];
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_WWW_Prefix'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_www_prefix']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SSI_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_ssi']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Web_User_Scripting'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_wu_scripting']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_PHP_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_php']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_WebMail'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_webmail']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Enable_CGI_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_cgi']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_FrontPage_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_frontpage']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_mod_perl_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_mod_perl']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Frontpage_SSL_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_fromtpage_ssl']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_ASP_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_asp']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Webstats'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_stats']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_SSL_Support'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_ssl']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_LANG['_DOMS']['l_Error_Docs_Logs'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.do_valtostr_no_yes($row['dom_enable_err_docs']).'</td>'.$_nl;
				$_cstr .= $_td_str_left_2.'<b>'.$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right_2.'<b>'.$_sp.'</b></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		} ELSE {

		# Build Title String, Content String, and Footer Menu String
			$_tstr .= $_LANG['_DOMS']['View_Client_Domain_ID'];
			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3MED_NC"><b>'.$_LANG['_DOMS']['Error_Domain_Not_Found'].'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
		}

		IF ( $_CCFG['_IS_PRINT'] != 1 ) {

		# Build function argument text
			$_mstr_flag = '1';
			$_mstr = '';
			IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod_print.php?mod=domains&mode=view&dom_id='.$adata['dom_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new',''); }
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=mail&dom_id='.$adata['dom_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP06'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=edit&dom_id='.$adata['dom_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		} ELSE {
			$_mstr_flag = '0';
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Domains
function do_view_domains($adata, $aret_flag=0) {
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
		$query	.= ' FROM '.$_DBCFG['domains'].', '.$_DBCFG['server_info'];
		$_where	 = ' WHERE '.$_DBCFG['domains'].'.dom_si_id='.$_DBCFG['server_info'].'.si_id';

	# Show only selected status domains
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_where .= ' AND '.$_DBCFG['domains'].".dom_status='".$db_coin->db_sanitize_data($_GPV['status'])."'";}
		IF ($_GPV['notstatus']) {$_where .= ' AND '.$_DBCFG['domains'].".dom_status != '".$db_coin->db_sanitize_data($_GPV['notstatus'])."'";}

	# Set to logged in Client ID if not admin to avoid seeing other client domain id's
		IF (!$_SEC['_sadmin_flg']) {
			$_where .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_SEC['_suser_id'];
		} ELSE {
			IF ($adata['dom_cl_id'] > 0) {
				$_where .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$adata['dom_cl_id'];
			}
		}

	# Set Filters
		IF (!$adata['fb'])			{$adata['fb'] = '';}
		IF ($adata['fb'] == '1')		{$_where .= ' AND '.$_DBCFG['server_info'].'.si_id='.$adata['fs'];}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])			{$adata['so'] = 'A';}
		IF ($adata['so'] == 'A')		{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')		{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])			{$adata['sb']='2';	}
		IF ($adata['sb'] == '1')		{$_order = ' ORDER BY '.$_DBCFG['domains'].'.dom_id '.$order_AD;}
		IF ($adata['sb'] == '2')		{$_order = ' ORDER BY '.$_DBCFG['domains'].'.dom_domain '.$order_AD;}
		IF ($adata['sb'] == '3')		{$_order = ' ORDER BY '.$_DBCFG['server_info'].'.si_name '.$order_AD;}
		IF ($adata['sb'] == '4')		{$_order = ' ORDER BY '.$_DBCFG['domains'].'.dom_registrar '.$order_AD;}
		IF ($adata['sb'] == '5')		{$_order = ' ORDER BY '.$_DBCFG['domains'].'.dom_ts_expiration '.$order_AD;}
		IF ($adata['sb'] == '6')		{$_order = ' ORDER BY '.$_DBCFG['domains'].'.dom_sa_expiration '.$order_AD;}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb= '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso= '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb= '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs= '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;
		IF ($adata['dom_cl_id']) {$_link_xtra .= '&dom_cl_id='.$adata['dom_cl_id'];}

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['domains'].', '.$_DBCFG['server_info'];
		$query_ttl .= $_where;

		$result_ttl= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

		# Page Loading first rec number
		# $_rec_next	- is page loading first record number
		# $_rec_start	- is a given page start record (which will be rec_next)
			$_rec_page	= $_CCFG['IPP_DOMAINS'];
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
			$_page_menu = $_LANG['_DOMS']['l_Pages'].$_sp;
			for ($i = 1; $i <= $_num_pages; $i++) {
				$_rec_start = (($i*$_rec_page)-$_rec_page);
				IF ($_rec_start == $_rec_next) {
				# Loading Page start record so no link for this page.
					$_page_menu .= "$i";
				} ELSE {
					$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=domains'.$_link_xtra.$_ps.'&rec_next='.$_rec_start.'">'.$i.'</a>';
				}
				IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
			}
		# End page menu

		# Finish out query with record limits and do data select for display and return check
			$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Generate links for sorting
			$_hdr_link_prefix = '<a href="'.$_SERVER["PHP_SELF"].'?mod=domains&sb=';
			$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&fc='.$adata['fc'].'&rec_next='.$_rec_next.$_ps.'">';

			$_hdr_link_1  = $_LANG['_DOMS']['l_ID'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

			$_hdr_link_2  = $_LANG['_DOMS']['l_Domain'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

			$_hdr_link_3  = $_LANG['_DOMS']['l_Server'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

			$_hdr_link_4  = $_LANG['_DOMS']['l_Registrar'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

			$_hdr_link_5  = $_LANG['_DOMS']['l_Domain_Expires'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

			$_hdr_link_6  = $_LANG['_DOMS']['l_SACC_Expires'].$_sp.'<br>';
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_hdr_link_6 .= $_hdr_link_prefix.'6&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
				$_hdr_link_6 .= $_hdr_link_prefix.'6&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
			}

		# Build Status header bar for viewing only certain types
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
				$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
				$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=domains&mode=view&status=all'.$_link_xtra;
				$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</nobr></td>';
				for ($i=1; $i<= sizeof($_CCFG['DOM_STATUS']); $i++) {
					$_out .= '<td align="right"><nobr>&nbsp;[<a href="mod.php?mod=domains&mode=view&status='.$i.$_link_xtra;
					$_out .= '">'.$_CCFG['DOM_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
				}
				$_out .= '</tr><tr>';
				$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
				$_out .= '<td>&nbsp;</td>';
				for ($i=1; $i<= sizeof($_CCFG['DOM_STATUS']); $i++) {
					$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=domains&mode=view&notstatus='.$i.$_link_xtra;
					$_out .= '">'.$_CCFG['DOM_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
				}
				$_out .= '</tr></table>';
				$_out .= '<br><br>';
			}

		# Build form output
			$_out .= '<div align="center">'.$_nl;
			$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;

			$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
			$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
			$_out .= '<b>'.$_LANG['_DOMS']['Client_Domains'].$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_DOMS']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_DOMS']['total_entries'].')</b><br>'.$_nl;
			$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
			IF ($_CCFG['_IS_PRINT'] != 1) {
				IF ($_SEC['_sadmin_flg']) {
					$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search&sw=domains', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
				}
			} ELSE {
				$_out .= $_sp;
			}
			$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_1.'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_2.'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_3.'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_4.'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_5.'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_6.'</td>'.$_nl;
			IF ($_CCFG['_IS_PRINT'] != 1) {
				$_out .= '<td class="TP3SML_BL" valign="top">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			# Process query results
			$todayis = dt_get_uts();
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP3SML_NC">'.$row['dom_id'].'</td>'.$_nl;
					$_out .= '<td class="TP3SML_NL"><a href="'.make_valid_link($row['dom_domain']).'">'.$row['dom_domain'].'</a></td>'.$_nl;
					$_out .= '<td class="TP3SML_NL">'.$row['si_name'].'</td>'.$_nl;
					$_out .= '<td class="TP3SML_NL">'.$row['dom_registrar'].'</td>'.$_nl;

					$_out .= '<td class="TP3SML_NC">';
				# Display text "expired" if in past, else display date
					IF (($todayis > $row['dom_ts_expiration']) && ($row['dom_ts_expiration'])) {
    						$_out .= $_LANG['_DOMS']['Expired'];
					} ELSE {
					    $_out .= dt_make_datetime($row['dom_ts_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
					}
					$_out .= '</td>'.$_nl;

					$_out .= '<td class="TP3SML_NC">';
				# Display text "expired" if in past, else display date
					IF (($todayis > $row['dom_sa_expiration']) && ($row['dom_sa_expiration'])) {
					    $_out .= $_LANG['_DOMS']['Expired'];
					} ELSE {
					    $_out .= dt_make_datetime($row['dom_sa_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
					}
					$_out .= '</td>'.$_nl;

					IF ($_CCFG['_IS_PRINT'] != 1) {
						$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
   						$_out .= do_nav_link('mod.php?mod=domains&mode=view&dom_id='.$row['dom_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
  						$_out .= do_nav_link('mod_print.php?mod=domains&mode=view&dom_id='.$row['dom_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
						$_out .= do_nav_link('mod.php?mod=domains&mode=mail&dom_id='.$row['dom_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
						IF ($row['dom_url_cp'] != '' && $_CCFG['DOM_CP_URL_LINK_ENABLE'] == 1) {
							$_out .= do_nav_link($row['dom_url_cp'], $_TCFG['_S_IMG_CP_S'],$_TCFG['_S_IMG_CP_S_MO'],'_new','');
						} ELSE {
							$_out .= $_TCFG['_IMG_BLANK_S'];
						}
						IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP06'] == 1)) {
							$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=edit&dom_id='.$row['dom_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=delete&stage=1&dom_id='.$row['dom_id'].'&dom_domain='.$row['dom_domain'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
						}
						$_out .= '</nobr></td>'.$_nl;
					}
					$_out .= '</tr>'.$_nl;
				}
			}

		# Closeout
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;
			$_out .= $_page_menu.$_nl;
			$_out .= '</td></tr>'.$_nl;

			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;
			$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * End Module Functions
**************************************************************/
?>