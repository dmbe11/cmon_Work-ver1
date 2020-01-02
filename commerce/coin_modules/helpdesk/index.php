<?php
/**
 * Module: HelpDesk (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=helpdesk');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_helpdesk.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_helpdesk_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_helpdesk_override.php');
	}

# Include functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php'); }

/**************************************************************
 * Module code
**************************************************************/
# Check $_GPV[mode] and set default to list
	switch($_GPV['mode']) {
		case "add":
			break;
		case "delete":
			break;
		case "mail":
			break;
		case "new":
			break;
		case "update":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = "none";
			break;
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {

		# Get the users id from session unless it is an admin
			IF (!$_SEC['_sadmin_flg']) {
				$_GPV['cl_id']			= $_SEC['_suser_id'];
				$_GPV['hd_tt_cl_id']	= $_SEC['_suser_id'];
			}

			# Call validate input function
				$err_entry = do_input_validation($_GPV);
		}

# Build Data Array (may also be over-ridden later in code)
	$data	= $_GPV;

	$data['hd_tt_message']	= do_parse_input_data($data['hd_tt_message']);
	$data['hdi_tt_message']	= do_parse_input_data($data['hdi_tt_message']);

##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP09'] != 1) {
	$_PFLAG = ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'delete' || $_GPV['mode'] == 'new' || $_GPV['mode'] == 'update');
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

	# Call function for listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'user', '1').$_nl;

	# Echo final output
		echo $_out;
}



# Check last submit for flood control
IF ($_GPV['stage'] && !$err_entry['flag']) {
	$_tm		= dt_get_uts();
	$_sdata		= array();
	$_sdata		= do_session_select();
	IF (($_tm - $_sdata['s_time_last_contact']) <= $_CCFG['FC_IN_SECONDS_CONTACTS']) {
		$_GPV['stage'] = 0;
		$_out .= do_no_contact_flood_message();
	}
}



##############################
# Operation:	None
# Summary:
#	- For listing of tickets
#	- For no actions specified.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'none') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String
		IF ($_SEC['_sadmin_flg']) {
			IF ($_GPV['hd_tt_cl_id'] > 0) {
				$_tstr = $_LANG['_HDESK']['HelpDesk_Support_Ticket_Summary'].$_sp.$_LANG['_HDESK']['l_Client_ID'].$_sp.$_GPV['hd_tt_cl_id'];
			} ELSE {
				$_tstr = $_LANG['_HDESK']['HelpDesk_Support_Ticket_Summary'].':'.$_sp.$_LANG['_HDESK']['Administration'];
			}
		# Add "edit parameters" button
			IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) && $_CCFG['_IS_PRINT'] != 1) {
				$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=helpdesk">'.$_TCFG['_S_IMG_PM_S'].'</a>';
			}

		} ELSE IF ($_SEC['_suser_flg']) {
			$_tstr	= $_LANG['_HDESK']['HelpDesk_Support_Ticket_Summary'].':'.$_sp.$_SEC['_suser_name'];
		}

	# Build Content String
		$_cstr	 = '<br>'.$_nl;
		$_cstr	.= do_select_listing_tickets($data, 1);
		$_cstr	.= '<br>'.$_nl;

	# Build Footer Menu String
		IF ($_CCFG['_IS_PRINT'] != 1) {

		# Set only selected status tickets for printing
			$_ps = '';
			IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
			IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

			$_mstr_flag = '1';
			IF ($_SEC['_sadmin_flg'] ) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
			$_url = '&sb='.$_GPV['sb'].'&so='.$_GPV['so'].'&fb='.$_GPV['fb'].'&fs='.$_GPV['fs'].'&rec_next='.$_GPV['rec_next'];
			$_mstr .= do_nav_link('mod_print.php?mod=helpdesk'.$_url.$_ps, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			IF ($_SEC['_suser_flg']) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=new', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	View
# Summary:
#	- For viewing ticket.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'view') {
	$_SESSION['hd_tt_id'] = $_GPV['hd_tt_id'];		// Session var to prevent user from changing ticket_id if "update" is chosen later
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call block it function
		$_tstr = $_LANG['_HDESK']['HelpDesk_View_Support_Ticket'].':'.$_sp.$_GPV['hd_tt_id'];

	# Add "return to client" button if admin
		IF ($_SEC['_sadmin_flg']  && !$_CCFG['_IS_PRINT']) {

		# Read ticket so we can get the client_id, then output the button
			$ttinfo = get_mtp_hdtt_info($_GPV['hd_tt_id']);
			$_tstr .= ' <a href="mod.php?mod=clients&mode=view&cl_id='.$ttinfo['hd_tt_cl_id'].'">'.$_TCFG['_IMG_BACK_TO_CLIENT_M'].'</a>';
		}

		$_cstr .= '<br>'.$_nl;
		$_cstr .= do_display_ticket($data, 1);

		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1))) {
				$_cstr .= '<br><div align="center">'.$_nl;
				$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=add&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_HD_ADD_MSG_B'],$_TCFG['_IMG_HD_ADD_MSG_B_MO'],'','');
				$_cstr .= '</div>'.$_nl;
			}

			$_mstr_flag = '1';
			IF ($_SEC['_sadmin_flg']) {
				$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			}
			IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1)) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=delete&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_DELETE_M'],$_TCFG['_IMG_DELETE_M_MO'],'','');
			}
			$_mstr .= do_nav_link('mod_print.php?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=mail&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
			IF ($_SEC['_suser_flg']) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=new', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			} ELSE { $_cstr .= '<br>'.$_nl; }

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	New
# Summary:
#	- For add new ticket.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'new' && (!$_GPV['stage'] || $err_entry['flag'])) {
	if ($_SEC['_sadmin_flg'] && !$_CCFG['HELPDESK_ADMIN_CAN_ADD']) {
	# Build Title String, Content String, and Footer Menu String
		$_tstr 		= $_LANG['_HDESK']['Helpdesk_Support_Ticket'].':'.$_sp.$_LANG['_HDESK']['Open_New'].$_sp;
		$_cstr 		= '<div align="center">'.$_LANG['_HDESK']['Admins_Not_Permitted'].'</div>'.$_nl;
		$_mstr_flag	= 0;
		$_mstr 		= '';

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;

	} ELSE {
	# Call function for New form.
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_form_new_ticket($data, $err_entry, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
	}
}


##############################
# Mode Call: New Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF (!$_login_flag && $_GPV['mode'] == 'new' && $_GPV['stage'] == 1 && !$err_entry['flag']) {

	# Check if we are sending to an additional email instead of the clients regular address
		$pos = strpos(strtolower($_GPV['hd_tt_cl_email']), 'alias');
		IF ($pos !== false) {

       	# It's an additional email address, so drop the 'alias|' bit and we have the client_contact_id
			$_GPV['hd_tt_cl_email'] = str_replace('alias|', '', $_GPV['hd_tt_cl_email']);

		# Now we need to lookup the the contact email address
			$query  = 'SELECT contacts_cl_id, contacts_email FROM '.$_DBCFG['clients_contacts'].' WHERE ';
			IF ($_GPV['hd_tt_cl_id']) {
				$query .= 'contacts_cl_id='.$_GPV['hd_tt_cl_id']. ' AND ';
			}
			$query .= 'contacts_id='.$_GPV['hd_tt_cl_email'];
			$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

			while(list($contacts_cl_id, $contacts_email) = $db_coin->db_fetch_row($result)) {
				$_GPV['hd_tt_cl_email']	= $contacts_email;

			# If admin entered ticket, we need to determine client_id
				IF ($_SEC['_sadmin_flg']) {$_GPV['hd_tt_cl_id'] = $contacts_cl_id;}
			}
		}

	# If admin entered ticket, we need to determine client_id if not already done
		IF ($_SEC['_sadmin_flg'] && !$_GPV['hd_tt_cl_id']) {
			$query	= 'SELECT cl_id FROM '.$_DBCFG['clients']." WHERE cl_email='".$db_coin->db_sanitize_data($_GPV['hd_tt_cl_email'])."'";
			$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$numrows	= $db_coin->db_query_numrows($result);
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
					$_GPV['hd_tt_cl_id'] = $row['cl_id'];
				}
			}
		}

	# Setup automatically filled in vars.
		$_GPV['hd_tt_time_stamp']	= dt_get_uts();
		$_GPV['hd_tt_status']		= $_CCFG['HD_TT_STATUS'][3];
		$_GPV['hd_tt_closed']		= 0;
		$_GPV['hd_tt_rating']		= 0;

	# Build SQL and execute
		$query  = 'INSERT INTO '.$_DBCFG['helpdesk'];
		$query .= ' (hd_tt_id, hd_tt_cl_id, hd_tt_cl_email';
		$query .= ', hd_tt_time_stamp, hd_tt_priority, hd_tt_category';
		$query .= ', hd_tt_subject, hd_tt_message, hd_tt_cd_id';
		$query .= ', hd_tt_url, hd_tt_status, hd_tt_closed';
		$query .= ', hd_tt_rating';
		$query .= ')';

	#Get max / create new tt_id
		$_max_hd_tt_id	= do_get_max_hd_tt_id();

		$query .= " VALUES ($_max_hd_tt_id+1".', ';

	# Get the users ID from session unless they are an admin - Based on Elantrix patch
		IF ($_SEC['_sadmin_flg']) {
			$query .= $_GPV['hd_tt_cl_id'].', ';
		} ELSE {
			$query .= $_SEC['_suser_id'].', ';
		}

		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_cl_email'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_time_stamp'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_priority'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_category'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_subject'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_message'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_cd_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_url'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_closed'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_rating'])."'";
		$query .= ')';

		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$_ins_hd_tt_id	= $_max_hd_tt_id+1;

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Rebuild Data Array
		$data['hd_tt_id'] = $_ins_hd_tt_id;

	# Send eMail notice
		$_ret = do_mail_helpdesk_tt($data, '1').$_nl;

	# Set flood control values in session
		$sdata['set_last_contact'] = 1;
		$_sret = do_session_update($sdata);

	# Call block it function
		$_tstr 	 = $_LANG['_HDESK']['HelpDesk_View_Support_Ticket'].':'.$_sp.$_ins_hd_tt_id;
		$_cstr 	 = '<br>'.$_nl;
		$_cstr 	.= do_display_ticket($data, 1);

	# Additional Items for add msg and footer menu
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1))) {
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=helpdesk&mode=add&hd_tt_id='.$_ins_hd_tt_id.'">'.$_TCFG['_IMG_HD_ADD_MSG_B'].'</a>';
				$_cstr .= '</div>'.$_nl;
			}
			$_mstr_flag	= '1';
			$_mstr = '';
			IF ($_SEC['_sadmin_flg'] ) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
			$_mstr .= do_nav_link('mod_print.php?mod=helpdesk&mode=view&hd_tt_id='.$_ins_hd_tt_id, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=mail&hd_tt_id='.$_ins_hd_tt_id, $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
			IF ($_SEC['_suser_flg']) {$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=new', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');}
		} ELSE {
			$_cstr .= '<br>'.$_nl;
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Update
# Summary:
#	- For processing updated entry
#	- Do table update
#	- Display results
##############################
IF (!$_login_flag && $_GPV['mode'] == 'update' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Prevent user from changing ticket_id
		IF (!$_SEC['_sadmin_flg']) {$_GPV['hd_tt_id'] = $_SESSION['hd_tt_id'];}

	# Do update
		$query  = 'UPDATE '.$_DBCFG['helpdesk'].' SET ';
		$query .= "hd_tt_category='".$db_coin->db_sanitize_data($_GPV['hd_tt_category'])."', ";
		$query .= "hd_tt_cd_id='".$db_coin->db_sanitize_data($_GPV['hd_tt_cd_id'])."', ";
		$query .= "hd_tt_status='".$db_coin->db_sanitize_data($_GPV['hd_tt_status'])."', ";
		$query .= "hd_tt_closed='".$db_coin->db_sanitize_data($_GPV['hd_tt_closed'])."', ";
		$query .= "hd_tt_rating='".$db_coin->db_sanitize_data($_GPV['hd_tt_rating'])."' ";
		$query .= "WHERE hd_tt_id='".$db_coin->db_sanitize_data($_GPV['hd_tt_id'])."'";

		$result 	= $db_coin->db_query_execute ($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call block it function
		$_tstr	= $_LANG['_HDESK']['HelpDesk_View_Support_Ticket'].':'.$_sp.$_GPV['hd_tt_id'];
		$_cstr	 = '<br>'.$_nl;
		$_cstr	.= do_display_ticket($data, 1);

	# Additional Items for add msg and footer menu
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1))) {
				$_cstr	.= '<div align="center">'.$_nl;
				$_cstr	.= '<a href="'.$_SERVER["PHP_SELF"].'?mod=helpdesk&mode=add&hd_tt_id='.$_GPV['hd_tt_id'].'">'.$_TCFG['_IMG_HD_ADD_MSG_B'].'</a>';
				$_cstr	.= '</div>'.$_nl;
			}

			$_mstr_flag = '1';
			$_mstr = '';
			IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
			$_mstr .= do_nav_link('mod_print.php?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=mail&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
			IF ($_SEC['_suser_flg']) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=new', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
		} ELSE {
			$_cstr .= '<br>'.$_nl;
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Add
# Summary:
#	- For add message to ticket.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	#Session var to prevent user from changing ticket_id if "update" is chosen later
		$_SESSION['hd_tt_id'] = $_GPV['hd_tt_id'];

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build output
		$_tstr 	 = $_LANG['_HDESK']['HelpDesk_View_Support_Ticket'].':'.$_sp.$_GPV['hd_tt_id'];
		$_cstr 	 = '<br>'.$_nl;
		$_cstr 	.= do_display_ticket($data, 1);
		$_cstr 	.= '<br>'.$_nl;

	# Dim global scope to get return set in display ticket, for use on form.
		global $_return_tt_status, $_return_tt_closed;
		$data['hd_tt_status'] = $_return_tt_status;
		$data['hd_tt_closed'] = $_return_tt_closed;

		$_cstr 		.= do_form_new_message($data, $err_entry, 1);
		$_cstr 		.= '<br>'.$_nl;
		$_mstr_flag	 = 1;
		$_mstr 		 = do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_BACK_TO_TT_M'],$_TCFG['_IMG_BACK_TO_TT_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Add Msg Results
# Summary:
#	- For processing added msg
#	- Do table insert
#	- Display results
##############################
IF (!$_login_flag && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Prevent user from changing ticket_id
		IF (!$_SEC['_sadmin_flg']) {$_GPV['hd_tt_id'] = $_SESSION['hd_tt_id'];}

	# Setup automatically filled in vars.
		IF ($_SEC['_sadmin_flg'] == 1)	{$_GPV['hdi_tt_ad_id'] = $_SEC['_sadmin_id'];}	ELSE {$_GPV['hdi_tt_ad_id'] = 0;}
		IF ($_SEC['_suser_flg'] == 1)		{$_GPV['hdi_tt_cl_id'] = $_SEC['_suser_id'];}	ELSE {$_GPV['hdi_tt_cl_id'] = 0;}
		$_GPV['hdi_tt_time_stamp'] = dt_get_uts();

	# Build SQL and execute
		$query	 = 'INSERT INTO '.$_DBCFG['helpdesk_msgs'];
		$query	.= ' (hdi_tt_id, hdi_tt_time_stamp';
		$query	.= ', hdi_tt_cl_id, hdi_tt_ad_id, hdi_tt_message';
		$query	.= ')';
		$query	.= ' VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['hd_tt_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['hdi_tt_time_stamp'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['hdi_tt_cl_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['hdi_tt_ad_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['hdi_tt_message'])."'";
		$query	.= ')';

		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Update ticket status
		IF ($_SEC['_sadmin_flg'] == 1 && !$_GPV['hd_tt_status']) {
			$_GPV['hd_tt_status'] = $_CCFG['HD_TT_STATUS'][1];
		}
		IF ($_SEC['_suser_flg'] == 1) {
			$_GPV['hd_tt_status'] = $_CCFG['HD_TT_STATUS'][3];
			$_GPV['hd_tt_closed'] = 0;
		}

	# Build SQL and execute
		$query	 = 'UPDATE '.$_DBCFG['helpdesk'].' SET ';
		$query	.= "hd_tt_status='".$db_coin->db_sanitize_data($_GPV['hd_tt_status'])."', ";
		$query 	.= "hd_tt_closed='".$db_coin->db_sanitize_data($_GPV['hd_tt_closed'])."' ";
		$query	.= "WHERE hd_tt_id='".$db_coin->db_sanitize_data($_GPV['hd_tt_id'])."'";

		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Send eMail notice
		$_ret = do_mail_helpdesk_tt($data, '1').$_nl;

	# Set flood control values in session
		$sdata['set_last_contact'] = 1;
		$_sret = do_session_update($sdata);

	# Call block it function
		$_tstr	 = $_LANG['_HDESK']['HelpDesk_View_Support_Ticket'].':'.$_sp.$_GPV['hd_tt_id'];
		$_cstr	 = '<br>'.$_nl;
		$_cstr	.= do_display_ticket($data, 1);

	# Additional Items for add msg and footer menu
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP09'] == 1))) {
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=helpdesk&mode=add&hd_tt_id='.$_GPV['hd_tt_id'].'">'.$_TCFG['_IMG_HD_ADD_MSG_B'].'</a>';
				$_cstr .= '</div>'.$_nl;
			}

			$_mstr_flag = 1;
			$_mstr = '';
			IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
			$_mstr .= do_nav_link('mod_print.php?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=mail&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
			IF ($_SEC['_suser_flg']) {$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=new', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');}
		} ELSE {
			$_cstr .= '<br>'.$_nl;
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && !$_GPV['stage'] == 2) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_HDESK']['Delete_HelpDesk_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=helpdesk&mode=delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['Delete_HelpDesk_Entry_Message'].'='.$_GPV[hd_tt_id].'<br>'.$_nl;
		$_cstr .= $_LANG['_HDESK']['Delete_HelpDesk_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
	#	$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
	#	$_cstr .= $_GPV[hd_tt_id].$_sp.'-'.$_sp.$_GPV[hd_tt_subject].$_nl;
	#	$_cstr .= '</td></tr>'.$_nl;
	#	$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="hd_tt_id" value="'.$_GPV['hd_tt_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_HDESK']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag = 1;
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Do purge client helpdesk tickets and and ticket msgs
		$query_hd 	= 'SELECT hd_tt_id FROM '.$_DBCFG['helpdesk'].' WHERE hd_tt_id='.$_GPV['hd_tt_id'];
		$result_hd 	= $db_coin->db_query_execute($query_hd) OR DIE("Unable to complete request");
		$eff_rows_hd	= $db_coin->db_query_numrows($result_hd);

	# Loop helpdesk msgs and delete messages
		while(list($_hd_tt_id) = $db_coin->db_fetch_row($result_hd)) {
		# Do query for ticket messages delete
			$query_hdi 	= 'DELETE FROM '.$_DBCFG['helpdesk_msgs'].' WHERE hdi_tt_id='.$_hd_tt_id;
			$result_hdi 	= $db_coin->db_query_execute($query_hdi) OR DIE("Unable to complete request");
			$eff_rows_hdi	= $db_coin->db_query_affected_rows();
			$_del_results	.= '<br>'.$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_02'].':'.$_sp.$eff_rows_hdi;
		}

	# Delete the helpdesk tickets
		$query_hd 	= 'DELETE FROM '.$_DBCFG['helpdesk'].' WHERE hd_tt_id='.$_GPV['hd_tt_id'];
		$result_hd 	= $db_coin->db_query_execute($query_hd) OR DIE("Unable to complete request");
		$eff_rows_hd	= $db_coin->db_query_affected_rows();
		$_del_results	.= '<br>'.$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_03'].':'.$_sp.$eff_rows_hd;

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_HDESK']['Delete_HelpDesk_Entry_Results'];

		IF (!$eff_rows_hd) {
			$_cstr .= '<center>'.$_LANG['_HDESK']['An_error_occurred'].'<br>'.$_del_results.'<br></center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_01'].':<br>'.$_del_results.'<br></center>';
		}

		$_mstr_flag = 1;
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Mail
# Summary:
#	- eMail HelpDesk Ticket
##############################
IF (!$_login_flag && $_GPV['mode'] == 'mail') {
	IF ($_GPV['stage'] != 2) {

	# Session var to prevent user from changing ticket_id if "update" is chosen later
		$_SESSION['hd_tt_id'] = $_GPV['hd_tt_id'];

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_HDESK']['eMail_Ticket_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="helpdesk">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="mail">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="hd_tt_id" value="'.$_GPV['hd_tt_id'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_HDESK']['eMail_Ticket_Message_prefix'].$_sp.$_GPV['hd_tt_id'].$_sp.$_LANG['_HDESK']['eMail_Ticket_Message_suffix'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_HDESK']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag = 1;
		$_mstr = '';
		IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk&mode=view&hd_tt_id='.$_GPV['hd_tt_id'], $_TCFG['_IMG_BACK_TO_TT_M'],$_TCFG['_IMG_BACK_TO_TT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=helpdesk', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
	}

	IF ($_GPV['stage'] == 2) {
	# Prevent user from changing ticket_id
		IF (!$_SEC['_sadmin_flg']) {$_GPV['hd_tt_id'] = $_SESSION['hd_tt_id'];}

	# Call function for doing it.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_mail_helpdesk_tt($data, '1').$_nl;

	# Set flood control values in session
		$sdata['set_last_contact'] = 1;
		$_sret = do_session_update($sdata);

	# Echo final output
		echo $_out;
	}
}


/**************************************************************
 * End Module Code
**************************************************************/
?>