<?php
/**
 * Module: Mail (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Mail
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_mail.php
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=mail');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_mail.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_mail_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_mail_override.php');
	}

# Include module functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include module admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php'); }


/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV[mode] and set default to list
	switch($_GPV['mode']) {
		case "client":
			break;
		case "contact":
			break;
		case "supplier":
			break;
		case "delete":
			break;
		case "resend":
			break;
		case "reset":
			break;
		case "search":
			IF ($_GPV['b_purge'] != '')		{$_GPV['mode'] = 'purge';}
			IF ($_GPV['b_purge_do'] != '')	{$_GPV['mode'] = 'purge';}
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = 'none';
			break;
	}

# Build time_stamp values when search
	IF ($_GPV['mode'] == 'search' || ($_GPV['mode'] == 'purge' && $_GPV['stage'] != '2')) {
		IF ($_GPV['s_sent_ts_01_hour'] == '')	{$_GPV['s_sent_ts_01_hour'] = 0;}
		IF ($_GPV['s_sent_ts_01_minute'] == '')	{$_GPV['s_sent_ts_01_minute'] = 0;}
		IF ($_GPV['s_sent_ts_01_second'] == '')	{$_GPV['s_sent_ts_01_second'] = 0;}
		IF ($_GPV['s_sent_ts_01_year'] == '' || $_GPV['s_sent_ts_01_month'] == '' || $_GPV['s_sent_ts_01_day'] == '') {
			$_GPV['s_sent_ts_01'] = '';
		} ELSE {
			$_GPV['s_sent_ts_01'] = mktime($_GPV['s_sent_ts_01_hour'],$_GPV['s_sent_ts_01_minute'],$_GPV['s_sent_ts_01_second'],$_GPV['s_sent_ts_01_month'],$_GPV['s_sent_ts_01_day'],$_GPV['s_sent_ts_01_year']);
		}

		IF ($_GPV['s_sent_ts_02_hour'] == '')	{$_GPV['s_sent_ts_02_hour'] = 0;}
		IF ($_GPV['s_sent_ts_02_minute'] == '')	{$_GPV['s_sent_ts_02_minute'] = 0;}
		IF ($_GPV['s_sent_ts_02_second'] == '') {$_GPV['s_sent_ts_02_second'] = 0;}
		IF ($_GPV['s_sent_ts_02_year'] == '' || $_GPV['s_sent_ts_02_month'] == '' || $_GPV['s_sent_ts_02_day'] == '') {
			$_GPV['s_sent_ts_02'] = '';
		} ELSE {
			$_GPV['s_sent_ts_02'] = mktime($_GPV['s_sent_ts_02_hour'],$_GPV['s_sent_ts_02_minute'],$_GPV['s_sent_ts_02_second'],$_GPV['s_sent_ts_02_month'],$_GPV['s_sent_ts_02_day'],$_GPV['s_sent_ts_02_year']);
		}
	}


# Check required fields (err / action generated later in code as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;

# From reset password form (for reference)
	$data['username']		= $_GPV['username'];
	$data['password']		= $_GPV['password'];

# From contact form (for reference)
	$data['mc_id']			= $_GPV['mc_id'];
	$data['mc_name']		= do_parse_input_data($_GPV['mc_name']);
	$data['mc_email']		= do_parse_input_data($_GPV['mc_email']);
	$data['mc_subj']		= do_parse_input_data($_GPV['mc_subj']);
	$data['mc_msg']		= do_parse_input_data($_GPV['mc_msg']);

# From contact client form (for reference)
	$data['cc_cl_id']		= $_GPV['cc_cl_id'];
	$data['cc_mc_id']		= $_GPV['cc_mc_id'];
	$data['cc_subj']		= $_GPV['cc_subj'];
	$data['cc_msg']		= $_GPV['cc_msg'];

# From contact supplier form (for reference)
	$data['cc_s_id']		= $_GPV['cc_s_id'];


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP05'] != 1) {
	$_PFLAG = ($_GPV['mode']=='delete' || $_GPV['mode']=='purge');
	IF ($_PERMS['AP10'] != 1 || ($_PERMS['AP10'] == 1 && $_PFLAG)) {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit;
	}
}


##############################
# Mode Call: all-
# Summary:
#	- Check login status
##############################
IF (!$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for login
	#	$_out = '<!-- Start content -->'.$_nl;
	#	$_out .= do_login($data, 'admin', '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: none
# Summary:
#	- To be determined
##############################
IF ($_GPV['mode'] == 'none') {
	IF (!$_SEC['_sadmin_flg']) {
		$_out 		.= '<!-- Start content -->'.$_nl;
		$_tstr 		= $_LANG['_MAIL']['Search_Mail'];
		$_cstr 		= '<center>'.$_LANG['_MAIL']['Sorry_Administrative_Function_Only'].'</center>'.$_nl;
		$_mstr_flag	= 0;
		$_mstr 		= '';

	} ELSE {
		$_out .= '<!-- Start content -->'.$_nl;
		$_tstr = $_LANG['_MAIL']['Mail'];
		$_cstr 	 = '<div align="center" valign="top" height="100%">'.$_nl;
		$_cstr 	.= '<table width="200px" cellspacing="5">'.$_nl;
		$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
		$_cstr 	.= '<div class="button"><a href="'.$_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw=archive">'.$_LANG['_MAIL']['Search_Mail_Archive'].'</a></div>';
		$_cstr 	.= '</td></tr>'.$_nl;
		$_cstr 	.= '</table>'.$_nl;
		$_cstr 	.= '</div>'.$_nl;
		$_mstr_flag = 1;
		$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
	}

# Call block it function
	$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
	$_out .= '<br>'.$_nl;

# Echo final output
	echo $_out;
}


##############################
# Mode Call: Reset (password)
# Summary:
#	- For resetting password
#	  via email to user / admin
##############################
IF ($_GPV[mode]=='reset') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Check user name, call function accordingly
		IF (!$_GPV['username']) {
			$_out .= do_pword_reset_form($data, '1');
		} ELSE {
			$_out .= do_mail_pword_reset($data, '1');
		}

		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Contact
# Summary:
#	- For doing contact form
#	  and submit (email)
##############################
IF ($_GPV['mode'] == 'contact') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Check last contact submit for flood control
		$_tm		= dt_get_uts();
		$_sdata	= array();
		$_sdata	= do_session_select();
		IF (($_tm - $_sdata['s_time_last_contact']) <= $_CCFG['FC_IN_SECONDS_CONTACTS']) {
			$_GPV['stage'] = 0;
			$_out .= do_no_contact_flood_message();
		}

	# Check user name, call function accordingly
		IF (!$_GPV['stage']) {
			$_out .= do_contact_form($data, $err_entry, '1');
		} ELSE {

		# Final Check for errors- redo form or process
			IF ($err_entry['flag'] == 1) {
				$_out .= do_contact_form($data, $err_entry, '1');
			} ELSE {
				$_out .= do_contact_email($data, '1');
			}
		}

		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Client
# Summary:
#	- For doing contact client
#	  form and submit (email)
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'client') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Check user name, call function accordingly
		IF (!$_GPV['stage']) {
			$_out .= do_contact_client_form($data, $err_entry, '1');
		} ELSE {
		# Final Check for errors- redo form or process
			IF ($err_entry['flag'] == 1 || !$_GPV['cc_cl_id']) {
				$_out .= do_contact_client_form($data, $err_entry, '1');
			} ELSE {
			# Check for group sending
				$pos		= strpos(strtolower($_GPV['cc_cl_id']), 'group');
				$pos1	= strpos(strtolower($_GPV['cc_cl_id']), 'contacts');
				$pos2	= strpos(strtolower($_GPV['cc_cl_id']), 'server');
				$pos3	= strpos(strtolower($_GPV['cc_cl_id']), 'alias');

				IF ($_GPV['cc_cl_id'] != -1 && $pos === false && $pos1 === false && $pos2 === false  && $pos3 === false) {
				# Specified client or additional email address
					$_out .= do_contact_client_email($data, '1');
				} ELSE {
	                # All or group
					$_out .= do_contact_client_email_all($data, '1');
				}
			}
		}

		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Supplier
# Summary:
#	- For doing contact supplier
#	  form and submit (email)
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'supplier') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Check user name, call function accordingly
		IF (!$_GPV['stage']) {
			$_out .= do_contact_supplier_form($data, $err_entry, '1');
		} ELSE {
		# Final Check for errors- redo form or process
			IF ($err_entry['flag'] == 1 || !$_GPV['cc_s_id']) {
				$_out .= do_contact_supplier_form($data, $err_entry, '1');
			} ELSE {
			# Check for group sending
				$pos1	= strpos(strtolower($_GPV['cc_s_id']), 'contacts');
				$pos2	= strpos(strtolower($_GPV['cc_s_id']), 'alias');

				IF ($_GPV['cc_s_id'] != -1 && $pos1 === false && $pos2 === false) {
				# Specified supplier or additional email address
					$_out .= do_contact_supplier_email($data, '1');
				} ELSE {
	                # All or group
					$_out .= do_contact_supplier_email_all($data, '1');
				}
			}
		}

		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

##############################
# Mode Call: Search
# Summary:
#	- For search mail
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'search') {
	# Check what to search, call code accordingly:
		IF ($_GPV['sw'] == '') {
		# Content start flag
			$_out 	.= '<!-- Start content -->'.$_nl;
			$_tstr 	 = $_LANG['_MAIL']['Search_Mail'];
			$_cstr 	 = '<div align="center" valign="top" height="100%">'.$_nl;
			$_cstr 	.= '<table width="200px" cellspacing="5">'.$_nl;
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= '<div class="button"><a href="'.$_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw=archive">'.$_LANG['_MAIL']['Search_Mail_Archive'].'</a></div>';
			$_cstr 	.= '</td></tr>'.$_nl;
			$_cstr 	.= '</table>'.$_nl;
			$_cstr 	.= '</div>'.$_nl;
			$_mstr_flag = 1;
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}

		IF ($_GPV['sw'] == 'archive') {
		# Content start flag
			$_out 		.= '<!-- Start content -->'.$_nl;
			$_out .= do_form_search_mail_archive( $data, 1);
		}

		# Echo final output
			echo $_out;
}


##############################
# Mode Call:	View
# Object: 		arch
# Summary:
#	- For viewing mail archive
#	  entry
##############################
IF ($_GPV['mode'] == 'view' && $_GPV['obj'] == 'arch') {
	# Check what to search, call code accordingly:
		IF ($_GPV['ma_id'] != '') {
			$_out 	.= '<!-- Start content -->'.$_nl;
			$_out .= do_display_entry_mail_archive($data, 1);
		}
	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	Resend
# Object: 		arch
# Summary:
#	- For resending mail archive
#	  entry
##############################
IF (($_SEC['_sadmin_flg'] || $_SEC['_suser_flg']) && $_GPV['mode'] == 'resend' && $_GPV['obj'] == 'arch') {
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php');
	# Do Confirmmation form
		IF ($_GPV['ma_id'] != '' && $_GPV['stage'] != 2) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_MAIL']['Resend_Archive_Entry_Confirmation'];

		# Do confirmation form to content string
			$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=mail&mode=resend&obj=arch">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_MAIL']['Resend_Archive_Entry_Message_01'].'='.$_sp.$_GPV['ma_id'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="ma_id" value="'.$_GPV['ma_id'].'">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_resend', 'SUBMIT', $_LANG['_MAIL']['B_Resend_Entry'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;

			$_mstr_flag = 1;
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=view&obj=arch&ma_id='.$_GPV['ma_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw=archive', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it ($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}

	# Check and call code accordingly:
		IF ($_GPV['ma_id'] != '' && $_GPV['stage'] == 2) {
			$_out 	.= '<!-- Start content -->'.$_nl;
			$_out 	.= do_resend_entry_mail_archive($data, 1);
		}

	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	Delete
# Object: 		arch
# Summary:
#	- For deleting mail archive
#	  entry
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['obj'] == 'arch') {
	# Do Confirmmation form
		IF ($_GPV['ma_id'] != '' && $_GPV['stage'] != 2) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_MAIL']['Delete_Archive_Entry_Confirmation'];

		# Do confirmation form to content string
			$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="mail">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="arch">'.$_nl;
			$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
			$_cstr .= '<input type="hidden" name="ma_id" value="'.$_GPV['ma_id'].'">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_MAIL']['Delete_Archive_Entry_Message_01'].'='.$_sp.$_GPV['ma_id'].'</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_MAIL']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_mstr_flag = '1';
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=view&obj=arch&ma_id='.$_GPV['ma_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw=archive', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
		}

	# Check and call code accordingly:
		IF ($_GPV['ma_id'] != '' && $_GPV['stage'] == 2) {
		# Do query for delete
			$query 	= 'DELETE FROM '.$_DBCFG['mail_archive'].' WHERE ma_id='.$_GPV['ma_id'];
			$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$eff_rows	= $db_coin->db_query_affected_rows();

			$_out .= '<!-- Start content -->'.$_nl;
			$_tstr = $_LANG['_MAIL']['Delete_Archive_Entry_Results'];
			$_cstr = '<center><p><p>'.$_LANG['_MAIL']['Delete_Archive_Entry_Message_02'].': '.$eff_rows.'<p><p></center>'.$_nl;
			$_mstr_flag = 1;
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw=archive', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');

// need to calculate client_id for the next line.
//			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=view&cl_id='.$adata['cl_id'], $_TCFG['_IMG_BACK_TO_CLIENT_M'],$_TCFG['_IMG_BACK_TO_CLIENT_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	Purge
# Object: 		arch
# Summary:
#	- For purging mail archive
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'purge') {
	IF ($_GPV['sw'] == 'archive') {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_form_search_mail_archive($data, 1);
	}
	echo $_out;
}


/**************************************************************
 * End Module Code
**************************************************************/
?>