<?php
/**
 * Module: Domains (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=domains');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_domains.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_domains_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_domains_override.php');
	}

# Include functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php'); }

/**************************************************************
 * Module code
**************************************************************/
# Check $_GPV['mode'] (operation switch)
	switch($_GPV['mode']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "mail":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = 'view';
			break;
	} #end mode switch

# Build time_stamp values when edit or add
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit') {
		IF ($_GPV['dom_ts_expiration_year'] == '' || $_GPV['dom_ts_expiration_month'] == '' || $_GPV['dom_ts_expiration_day'] == '') {
			$_GPV['dom_ts_expiration'] = '';
		} ELSE {
			$_GPV['dom_ts_expiration'] = mktime(0,0,0,$_GPV['dom_ts_expiration_month'],$_GPV['dom_ts_expiration_day'],$_GPV['dom_ts_expiration_year']);
		}
		IF ($_GPV['dom_sa_expiration_year'] == '' || $_GPV['dom_sa_expiration_month'] == '' || $_GPV['dom_sa_expiration_day'] == '') {
			$_GPV['dom_sa_expiration'] = '';
		} ELSE {
			$_GPV['dom_sa_expiration'] = mktime(0,0,0,$_GPV['dom_sa_expiration_month'],$_GPV['dom_sa_expiration_day'],$_GPV['dom_sa_expiration_year']);
		}
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP06'] != 1) {
	$_PFLAG = ($_GPV['mode']=='add' || $_GPV['mode']=='delete' || $_GPV['mode']=='edit');
	IF ($_PERMS['AP10'] != 1 || ($_PERMS['AP10'] == 1 && $_PFLAG)) {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit;
	}
}


##############################
# Mode Call: 	All modes
# Summary:
#	- Check if login required
##############################
IF (!$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for articles listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'admin', '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: View
# Summary:
#	- View Domain
##############################
IF (!$_login_flag && $_GPV['mode'] == 'view') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;
		$_tstr = '';
		$_mstr = '';

	# Check for dom_id
		IF (!$_GPV['dom_id']) {
			$data['_suser_id']	= $_SEC['_suser_id'];

		# Build Title String, Content String, and Footer Menu String
			IF ($_SEC['_sadmin_flg']) {
				IF ($_GPV['dom_cl_id'] > 0) {
					$_tstr .= $_LANG['_DOMS']['View_Client_Domains'].$_sp.$_LANG['_DOMS']['l_Client_ID'].$_sp.$_GPV['dom_cl_id'];

				# Add "edit parameters" button
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
						$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=domains">'.$_TCFG['_S_IMG_PM_S'].'</a>';
					}
				} ELSE {
					$_tstr.= $_LANG['_DOMS']['View_Client_Domains'];

				# Add "edit parameters" button
					IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
						$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=domains">'.$_TCFG['_S_IMG_PM_S'].'</a>';
					}
				}

				IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
				$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP07'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				}
			} ELSE {
				$_tstr .= $_LANG['_DOMS']['View_Client_Domains_For'].' '.$_SEC['_suser_name'];
				IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
			}

		# Set only selected status domains for printing
			$_ps = '';
			IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
			IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

			$_url = '&sb='.$_GPV['sb'].'&so='.$_GPV['so'].'&fb='.$_GPV['fb'].'&fs='.$_GPV['fs'].'&rec_next='.$_GPV['rec_next'];
			$_mstr .= do_nav_link('mod_print.php?mod=domains&mode=view'.$_url.$_ps, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');

			$_cstr  = '<br>'.$_nl;
			$_cstr .= do_view_domains($data, '1').$_nl;
			$_cstr .= '<br>'.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;

		} ELSE {
			$_out .= do_display_entry($data, '1').$_nl;

		# Echo final output
			echo $_out;
		}
}


##############################
# Operation: 	Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_form_add_edit_domains($data, $err_entry,'1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do insert
		$query	 = 'INSERT INTO '.$_DBCFG['domains']." (";
		$query	.= 'dom_cl_id, dom_domain, dom_status';
		$query	.= ',dom_type, dom_registrar, dom_ts_expiration, dom_sa_expiration';
		$query	.= ',dom_si_id, dom_ip, dom_path, dom_path_temp, dom_url_cp';
		$query	.= ',dom_user_name_cp, dom_user_pword_cp, dom_user_name_ftp, dom_user_pword_ftp';
		$query	.= ',dom_allow_domains, dom_allow_subdomains, dom_allow_disk_space_mb, dom_allow_traffic_mb';
		$query	.= ',dom_allow_mailboxes, dom_allow_databases, dom_enable_www_prefix, dom_enable_wu_scripting';
		$query	.= ',dom_enable_webmail, dom_enable_frontpage, dom_enable_fromtpage_ssl, dom_enable_ssi';
		$query	.= ',dom_enable_php, dom_enable_cgi, dom_enable_mod_perl, dom_enable_asp';
		$query	.= ',dom_enable_ssl, dom_enable_stats, dom_enable_err_docs, dom_notes';
		$query	.= ') VALUES (';
		$query	.= "'".$_GPV['dom_cl_id']."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_domain'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_status'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_type'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_registrar'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_ts_expiration'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_sa_expiration'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_si_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_ip'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_path'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_path_temp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_url_cp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_user_name_cp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_user_pword_cp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_user_name_ftp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_user_pword_ftp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_domains'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_subdomains'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_disk_space_mb'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_traffic_mb'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_mailboxes'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_allow_databases'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_www_prefix'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_wu_scripting'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_webmail'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_frontpage'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_fromtpage_ssl'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_ssi'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_php'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_cgi'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_mod_perl'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_asp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_ssl'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_stats'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_enable_err_docs'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['dom_notes'])."'";
		$query	.= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['dom_id']	= $insert_id;

	#########################################################################################################
	# API Output Hook:
	# APIO_domain_new: Domain Created hook
		$_isfunc = 'APIO_domain_new';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_DOMAIN_NEW_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
			} ELSE {
				$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
			}
		}
	#########################################################################################################

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_DOMS']['Add_Domains_Entry_Results'].'-'.$_sp.$insert_id.')';
		$_out .= do_subj_block_it($title_text, '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= do_display_entry($data, '1');
		$_out .= '<br>'.$_nl;

	# Append API results
		$_out .= $_APIO_ret;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for valid $_GPV[dom_id]
		IF ($_GPV['dom_id']) {
		# Set Query for select.
			$query	 = 'SELECT *';
			$query	.= ' FROM '.$_DBCFG['domains'];
			$query	.= ' WHERE dom_id='.$_GPV['dom_id'];
			$query	.= ' ORDER BY dom_id ASC';

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);
		}

	# Process query results (assumes one returned row above)
		IF ($numrows) {
		# Process query results
			while ($row = $db_coin->db_fetch_array($result)) {
			# Merge Data Array with returned record
				$data_new	= array_merge($data, $row);
				$data	= $data_new;

			# Call function for add/edit form
				$_out = '<!-- Start content -->'.$_nl;
				$_out .= do_form_add_edit_domains($data, $err_entry,'1').$_nl;

			# Echo final output
				echo $_out;
			}
		} ELSE {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_DOMS']['View_Domain_Error'];

		# Do confirmation form to content string
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_DOMS']['Domain_ID'].'='.$_sp.$_GPV['dom_id'].$_sp.$_LANG['_DOMS']['could_not_be_located'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;

			$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}
}


##############################
# Operation: 	Edit Entry Results
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query	 = 'UPDATE '.$_DBCFG['domains'].' SET ';
		$query	.= "dom_cl_id='".$db_coin->db_sanitize_data($_GPV['dom_cl_id'])."', ";
		$query	.= "dom_domain='".$db_coin->db_sanitize_data($_GPV['dom_domain'])."', ";
		$query	.= "dom_status='".$db_coin->db_sanitize_data($_GPV['dom_status'])."', ";
		$query	.= "dom_type='".$db_coin->db_sanitize_data($_GPV['dom_type'])."', ";
		$query	.= "dom_registrar='".$db_coin->db_sanitize_data($_GPV['dom_registrar'])."', ";
		$query	.= "dom_ts_expiration='".$db_coin->db_sanitize_data($_GPV['dom_ts_expiration'])."', ";
		$query	.= "dom_sa_expiration='".$db_coin->db_sanitize_data($_GPV['dom_sa_expiration'])."', ";
		$query	.= "dom_si_id='".$db_coin->db_sanitize_data($_GPV['dom_si_id'])."', ";
		$query	.= "dom_ip='".$db_coin->db_sanitize_data($_GPV['dom_ip'])."', ";
		$query	.= "dom_path='".$db_coin->db_sanitize_data($_GPV['dom_path'])."', ";
		$query	.= "dom_path_temp='".$db_coin->db_sanitize_data($_GPV['dom_path_temp'])."', ";
		$query	.= "dom_url_cp='".$db_coin->db_sanitize_data($_GPV['dom_url_cp'])."', ";
		$query	.= "dom_user_name_cp='".$db_coin->db_sanitize_data($_GPV['dom_user_name_cp'])."', ";
		$query	.= "dom_user_pword_cp='".$db_coin->db_sanitize_data($_GPV['dom_user_pword_cp'])."', ";
		$query	.= "dom_user_name_ftp='".$db_coin->db_sanitize_data($_GPV['dom_user_name_ftp'])."', ";
		$query	.= "dom_user_pword_ftp='".$db_coin->db_sanitize_data($_GPV['dom_user_pword_ftp'])."', ";
		$query	.= "dom_allow_domains='".$db_coin->db_sanitize_data($_GPV['dom_allow_domains'])."', ";
		$query	.= "dom_allow_subdomains='".$db_coin->db_sanitize_data($_GPV['dom_allow_subdomains'])."', ";
		$query	.= "dom_allow_disk_space_mb='".$db_coin->db_sanitize_data($_GPV['dom_allow_disk_space_mb'])."', ";
		$query	.= "dom_allow_traffic_mb='".$db_coin->db_sanitize_data($_GPV['dom_allow_traffic_mb'])."', ";
		$query	.= "dom_allow_mailboxes='".$db_coin->db_sanitize_data($_GPV['dom_allow_mailboxes'])."', ";
		$query	.= "dom_allow_databases='".$db_coin->db_sanitize_data($_GPV['dom_allow_databases'])."', ";
		$query	.= "dom_enable_www_prefix='".$db_coin->db_sanitize_data($_GPV['dom_enable_www_prefix'])."', ";
		$query	.= "dom_enable_wu_scripting='".$db_coin->db_sanitize_data($_GPV['dom_enable_wu_scripting'])."', ";
		$query	.= "dom_enable_webmail='".$db_coin->db_sanitize_data($_GPV['dom_enable_webmail'])."', ";
		$query	.= "dom_enable_frontpage='".$db_coin->db_sanitize_data($_GPV['dom_enable_frontpage'])."', ";
		$query	.= "dom_enable_fromtpage_ssl='".$db_coin->db_sanitize_data($_GPV['dom_enable_fromtpage_ssl'])."', ";
		$query	.= "dom_enable_ssi='".$db_coin->db_sanitize_data($_GPV['dom_enable_ssi'])."', ";
		$query	.= "dom_enable_php='".$db_coin->db_sanitize_data($_GPV['dom_enable_php'])."', ";
		$query	.= "dom_enable_cgi='".$db_coin->db_sanitize_data($_GPV['dom_enable_cgi'])."', ";
		$query	.= "dom_enable_mod_perl='".$db_coin->db_sanitize_data($_GPV['dom_enable_mod_perl'])."', ";
		$query	.= "dom_enable_asp='".$db_coin->db_sanitize_data($_GPV['dom_enable_asp'])."', ";
		$query	.= "dom_enable_ssl='".$db_coin->db_sanitize_data($_GPV['dom_enable_ssl'])."', ";
		$query	.= "dom_enable_stats='".$db_coin->db_sanitize_data($_GPV['dom_enable_stats'])."', ";
		$query	.= "dom_enable_err_docs='".$db_coin->db_sanitize_data($_GPV['dom_enable_err_docs'])."', ";
		$query	.= "dom_notes='".$db_coin->db_sanitize_data($_GPV['dom_notes'])."' ";
		$query	.= "WHERE dom_id='".$db_coin->db_sanitize_data($_GPV['dom_id'])."'";
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_DOMS']['Edit_Domains_Entry_Results'];
		$_out .= do_subj_block_it($title_text, '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= do_display_entry($data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_DOMS']['Delete_Domains_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="domains">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_DOMS']['Delete_Domains_Entry_Message'].$_sp.'='.$_sp.$_GPV['dom_id'].' - '.$_GPV['dom_domain'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="dom_id" value="'.$_GPV['dom_id'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="dom_domain" value="'.$_GPV['dom_domain'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_DOMS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=edit&dom_id='.$_GPV['dom_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Grab domain record for passing into our API call BEFORE we delete the record
		$d_query 	= 'SELECT * FROM '.$_DBCFG['domains'].' WHERE dom_id='.$_GPV['dom_id'];
		$d_result = $db_coin->db_query_execute($d_query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_numrows($d_result);

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($d_result)) {$DDomain = $row;}
		}

	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['domains'].' WHERE dom_id='.$_GPV['dom_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

		#########################################################################################################
		# API Output Hook:
		# APIO_domain_del: Domain Deleted hook
			$_isfunc = 'APIO_domain_del';
			IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_DOMAIN_DEL_ENABLE'] == 1) {
				IF (function_exists($_isfunc)) {
					$_APIO = $_isfunc($DDomain); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
				} ELSE {
					$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
				}
			}
		#########################################################################################################

		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_DOMS']['Delete_Domains_Entry_Results'];

			IF (!$eff_rows) {
				$_cstr .= '<center>'.$_LANG['_DOMS']['An_error_occurred'].'</center>';
			} ELSE {
				$_cstr .= '<center>'.$_LANG['_DOMS']['Entry_Deleted'].'</center>';
			}

		# Append API results
			$_cstr .= $_APIO_ret;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
	}


##############################
# Mode Call: Mail
# Summary:
#	- eMail Domain (Server Acc)
##############################
IF (!$_login_flag && $_GPV['mode'] == 'mail') {
	IF ($_GPV['stage'] != 2) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_DOMS']['eMail_Domain_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="domains">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="mail">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_DOMS']['eMail_Domain_Message_prefix'].$_sp.$_GPV['dom_id'].$_sp.$_LANG['_DOMS']['eMail_Domain_Message_suffix'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="dom_id" value="'.$_GPV['dom_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_DOMS']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag = '1';
		$_mstr = '';
		IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=view&dom_id='.$_GPV['dom_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP06'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=edit&dom_id='.$_GPV['dom_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=domains', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
	}

	IF ($_GPV['stage'] == 2) {
	# Call function for doing it.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_mail_domain($data,'1').$_nl;

	# Echo final output
		echo $_out;
	}
}

/**************************************************************
 * End Module Code
**************************************************************/
?>