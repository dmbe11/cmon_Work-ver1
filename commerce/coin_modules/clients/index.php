<?php
/**
 * Module: Clients (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Clients
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_clients.php
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=clients');
		exit();
	}

# Get security vars
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_clients.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_clients_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_clients_override.php');
	}

# Include functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php');}



/**************************************************************
 * Module code
**************************************************************/
# Check $_GPV['mode'] and set default to list
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
		case "ae_mails":

		# Get the users ID from session unless it is an admin
			IF (!$_SEC['_sadmin_flg']) {$_GPV['cl_id'] = $_SEC['_suser_id'];}

			IF ($_GPV['op'] == "del") {$good = do_delete_additional_email($_GPV['cl_id'], $_GPV['contacts_id']);}
			IF ($_GPV['op'] == "add") {
				IF (do_validate_email($_GPV['new_ae_email'],0)) {
					$err_entry['flag'] = 1;
					$err_entry['cl_email'] = 1;
					$err_entry['err_additional_email_invalid'] = 1;
				} ELSE {
					$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
					IF (do_email_exist_check($_GPV['new_ae_email'], $_GPV['cl_id'], $_ce)) {
						$err_entry['flag'] = 1;
						$err_entry['cl_email'] = 1;
						$err_entry['err_additional_matches_another'] = 1;
					} ELSE {
						$good = do_insert_additional_email($_GPV['cl_id'],$_GPV['new_ae_fname'],$_GPV['new_ae_lname'],$_GPV['new_ae_email']);
					}
				}
			}
			IF ($_GPV['op'] == "update") {
				IF (do_validate_email($_GPV['ae_email'],0)) {
					$err_entry['flag'] = 1;
					$err_entry['cl_email'] = 1;
					$err_entry['err_additional_email_invalid'] = 1;
				} ELSE {
					$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
					IF (do_email_exist_check($_GPV['ae_email'], $_GPV['cl_id'], $_ce)) {
						$err_entry['flag'] = 1;
						$err_entry['cl_email'] = 1;
						$err_entry['err_additional_matches_another'] = 1;
					} ELSE {
						$good = do_update_additional_email($_GPV['cl_id'],$_GPV['ae_fname'],$_GPV['ae_lname'],$_GPV['ae_email'],$_GPV['contacts_id']);
					}
				}
			}
			$_GPV['mode'] = 'edit';
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = "none";
			break;
	}

# Build time_stamp values when edit or add
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit') {
		IF ($_GPV['cl_join_ts_hour'] == '')	{$_GPV['cl_join_ts_hour'] = 0;}
		IF ($_GPV['cl_join_ts_minute'] == '')	{$_GPV['cl_join_ts_minute'] = 0;}
		IF ($_GPV['cl_join_ts_second'] == '') 	{$_GPV['cl_join_ts_second'] = 0;}
		IF ($_GPV['cl_join_ts_year'] != '' && $_GPV['cl_join_ts_month'] != '' && $_GPV['cl_join_ts_day'] != '') {
			$_GPV['cl_join_ts'] = mktime($_GPV['cl_join_ts_hour'],$_GPV['cl_join_ts_minute'],$_GPV['cl_join_ts_second'],$_GPV['cl_join_ts_month'],$_GPV['cl_join_ts_day'],$_GPV['cl_join_ts_year']);
		}
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {

	# Get the users ID and original name from session/database read unless it is an admin
		IF (!$_SEC['_sadmin_flg'])	{
			$_GPV['cl_id']				= $_SEC['_suser_id'];
			$_cinfo					= get_contact_client_info($_GPV['cl_id']);
			$_GPV['cl_user_name_orig']	= $_cinfo['cl_user_name'];
		}

	# Encode groups fields
		IF (!isset($_GPV['cl_groups']) && $_SEC['_sadmin_flg']) {
		    $_GPV['cl_groups'] = do_encode_groups_user($_GPV);
		}

		# Call input validation code
			$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Mode Call: 	All modes
# Summary:
#	- Check if login required
##############################
IF (!$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for clients listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'user', '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP07'] != 1) {
	$_PFLAG = ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'delete' || $_GPV['mode'] == 'edit');
	IF ($_PERMS['AP10'] != 1 || ($_PERMS['AP10'] == 1 && $_PFLAG)) {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br />'.$_nl;
		echo $_out;
		exit();
	}
}


##############################
# Operation:	None
# Summary:
#	- For loading select menu.
#	- For no actions specified.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'none') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String for admin
		IF ($_SEC['_sadmin_flg']) {
			$data['_suser_id']	= $_GPV['cl_id'];
			$_tstr 	= $_LANG['_CLIENTS']['View_Clients'];

		# Add "edit parameters" button
			IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
				$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=clients">'.$_TCFG['_S_IMG_PM_S'].'</a>';
			}

			$_cstr 	 = '<br>'.$_nl;
			$_cstr 	.= do_select_listing_clients($data, '1');
			$_cstr 	.= '<br>'.$_nl;

		# Set only selected status clients for printing
			$_ps = '';
			IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
			IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP07'] == 1)) {
				$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_url = '&sb='.$_GPV['sb'].'&so='.$_GPV['so'].'&fb='.$_GPV['fb'].'&fs='.$_GPV['fs'].'&rec_next='.$_GPV['rec_next'];
			$_mstr .= do_nav_link('mod_print.php?mod=clients'.$_url.$_ps, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');


	# Build Title String, Content String, and Footer Menu String for user
		} ELSE IF ($_SEC['_suser_flg']) {
			$data['_suser_id']	= $_SEC['_suser_id'];
			$_tstr 	= $_LANG['_CLIENTS']['Welcome'].$_sp.$_SEC['_suser_name'];
			$_cstr 	.= '<center><table width="95%" cellspacing="5">'.$_nl;
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_info($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;

		# Sorry, but this section is so that I do not have to maintain
		# several versions of the single code-base
			IF (
				eregi('phpcoin.com', $_SERVER['SERVER_NAME']) ||
				eregi('phpcoin.ca', $_SERVER['SERVER_NAME']) ||
				eregi('phpcoin.eu', $_SERVER['SERVER_NAME']) ||
				eregi('coinsofttechnologies.com', $_SERVER['SERVER_NAME']) ||
				eregi('coinsofttechnologies.ca', $_SERVER['SERVER_NAME'])
			) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_licenses($_SEC['_suser_id'], '1' );
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['DOMAINS_ENABLE']) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_domains( $data, '1');
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['ORDERS_ENABLE'] && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP08'] == 1)))) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_orders($data, '1');
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['INVOICES_ENABLE'] && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP08'] == 1)))) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_invoices($data, '1');
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['HELPDESK_ENABLE'] && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP09'] == 1)))) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_tickets($data, '1');
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['_PKG_ENABLE_EMAIL_ARCHIVE'] && (($_SEC['_suser_flg'] && $_CCFG['show_archive_to_clients']) || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP05'] == 1)))) {
				$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
				$_cstr 	.= do_view_client_emails($data, '1');
				$_cstr 	.= '</td></tr>'.$_nl;
			}

			$_cstr 	.= '</table></center>'.$_nl;

			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
			$_mstr .= do_nav_link('mod_print.php?mod=clients', $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	View
# Summary:
#	- For viewing entry.
##############################
IF (!$_login_flag && $_GPV['mode'] == 'view') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String for admin
		IF ($_SEC['_sadmin_flg']) {
			$data['_suser_id']	= $_GPV['cl_id'];
			$_tstr 			= $_LANG['_CLIENTS']['Admin_Client_View'];

			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod_print.php?mod=clients&mode=view&cl_id='.$_GPV[cl_id], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP07'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				# $_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=delete&stage=1&cl_id='.$_GPV['cl_id'].'&cl_name_first='.$_GPV['cl_name_first'].'&cl_name_last='.$_GPV['cl_name_last'], $_TCFG['_IMG_DELETE_M'],$_TCFG['_IMG_DELETE_M_MO'],'',''); // no confirming name available yet
			}
			$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Build Title String, Content String, and Footer Menu String for client
		} ELSE IF ($_SEC['_suser_flg']) {
			$data['_suser_id']	= $_SEC['_suser_id'];
			$_tstr 			= $_LANG['_CLIENTS']['Welcome'].$_sp.$_SEC['_suser_name'];

			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}
			$_mstr .= do_nav_link('mod_print.php?mod=clients&mode=view&cl_id='.$_SEC['_suser_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
		}

		$_cstr 	.= '<center><table width="95%" cellspacing="5">'.$_nl;
		$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
		$_cstr 	.= do_view_client_info($data, '1');
		$_cstr 	.= '</td></tr>'.$_nl;

	# Sorry, but this section is so that I do not have to maintain
	# several versions of the single code-base
		IF (
			eregi('phpcoin.com', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.ca', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.eu', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.com', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.ca', $_SERVER['SERVER_NAME'])
		) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_licenses($data['_suser_id'], '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		IF ($_CCFG['DOMAINS_ENABLE'] == 1 && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP06'] == 1))) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_domains($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		IF ($_CCFG['ORDERS_ENABLE'] == 1 && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP08'] == 1))) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_orders($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		IF ($_CCFG['INVOICES_ENABLE'] && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP08'] == 1))) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_invoices($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		IF ( $_CCFG['HELPDESK_ENABLE'] == 1 && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP09'] == 1)) ) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_tickets($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		IF ($_CCFG['_PKG_ENABLE_EMAIL_ARCHIVE'] && ($_SEC['_suser_flg'] || ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP10'] == 1 || $_PERMS['AP05'] == 1)))) {
			$_cstr 	.= '<tr><td align="center" valign="top">'.$_nl;
			$_cstr 	.= do_view_client_emails($data, '1');
			$_cstr 	.= '</td></tr>'.$_nl;
		}

		$_cstr 	.= '</table></center>'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for Add / Edit form.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1');

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Call timestamp function if join ts empty
		IF (!$_GPV['cl_join_ts']) {$_GPV['cl_join_ts'] = dt_get_uts();}

	# Generate encrypted password
		$cl_user_pword_crypt = do_password_crypt($_GPV['cl_user_pword']);

	#Get max / create new cl_id
		$_max_cl_id = do_get_max_cl_id();

	# Do insert of new client
		$query = "INSERT INTO ".$_DBCFG['clients']." (cl_id";
		$query .= ", cl_join_ts, cl_status, cl_company, cl_name_first, cl_name_last";
		$query .= ", cl_addr_01, cl_addr_02, cl_city, cl_state_prov";
		$query .= ", cl_country, cl_zip_code, cl_phone, cl_email";
		$query .= ", cl_user_name, cl_user_pword, cl_notes, cl_groups";
		$query .= ")";
		$query .= " VALUES ('".($_max_cl_id+1)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_join_ts'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_company'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_name_first'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_name_last'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_addr_01'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_addr_02'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_city'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_state_prov'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_country'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_zip_code'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_phone'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_email'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_user_name'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($cl_user_pword_crypt)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_notes'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cl_groups'])."'";
		$query .= ")";

		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$_ins_cl_id	= $_max_cl_id+1;
		$_GPV['cl_id']	= $_max_cl_id+1;

	#########################################################################################################
	# API Output Hook:
	# APIO_order_new_client: Order new client hook
		$_isfunc = 'APIO_client_new';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_CLIENT_NEW_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret .= '<br />'.$_APIO['msg'].'<br />';
			} ELSE {
				$_APIO_ret .= '<br />'.'Error- no function'.'<br />';
			}
		}
	#########################################################################################################

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Adjust Data Array with returned record
		$data['_suser_id']	= $_ins_cl_id;

	# Build Title String, Content String, and Footer Menu String
		$_tstr 		 = $_LANG['_CLIENTS']['Add_Client_Info_Results'].$_sp.'('.$_LANG['_CLIENTS']['Inserted_ID'].'-'.$_sp.$_ins_cl_id.')';
		$_cstr		.= '<center><table width="85%" cellspacing="5"><tr><td align="center" valign="top">'.$_nl;
		$_cstr		.= do_view_client_info($data, '1');
		$_cstr		.= '</td></tr></table></center>'.$_nl;

	# Append API results
		$_cstr		.= $_APIO_ret;

		$_mstr_flag	 = '1';
		$_mstr 		.= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr 		.= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr 		.= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF (!$_login_flag && $_GPV['mode'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {

	# Check / Set cl_id to logged in if not admin
		IF (!$_SEC['_sadmin_flg']) {$_GPV['cl_id'] = $_SEC['_suser_id'];}

	# Check for $_GPV[cl_id] no- will determine select string (one for edit, all for list)
		IF (!$_GPV['cl_id'] || $_GPV['cl_id'] == 0) {
			$show_list_flag = 1;
		} ELSE {
		# Set Query for select and execute
			$query  = 'SELECT * FROM '.$_DBCFG['clients'];
			$query .= ' WHERE cl_id = '.$_GPV['cl_id'];

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Set for no list.
			$show_list_flag = 0;
		}

	# Check flag- condition is show list
		IF ($show_list_flag) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_CLIENTS']['View_Clients'];

			# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
				$_cstr 		.= do_select_listing_clients($data, '1');
				$_mstr_flag	 = '1';
				$_mstr		.= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr 		.= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				$_mstr		.= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			} ELSE {
				$_mstr_flag	 = '1';
				$_mstr		.= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br />'.$_nl;

		# Echo final output
			echo $_out;

	} #if flag_list set

	# Check flag- condition is not show list
	IF (!$show_list_flag) {

	# If Stage and Error Entry, pass field vars to form,
	# Otherwise, pass looked up record to form
		IF ($_GPV['stage'] == 1 && $err_entry['flag']) {

		# Call function for Add / Edit form.
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;

		# Echo final output
			echo $_out;

		} ELSE {
		# Process query results (assumes one returned row above)
			IF ($numrows) {

			# Process query results
				while ($row = $db_coin->db_fetch_array($result)) {
				# Merge Data Array with returned row
					$data_new					= array_merge($data, $row);
					$data					= $data_new;
					$data['cl_user_pword']		= ''; # Do not load- encrypted
					$data['cl_user_pword_re']	= ''; # Do not load- encrypted
				}
			}

		# Call function for Add / Edit form.
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;

		# Echo final output
			echo $_out;
		}
	}
}


##############################
# Mode Call: Edit Entry Results
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF (!$_login_flag && $_GPV['mode'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_ORD']);

	# Do update
		$query = 'UPDATE '.$_DBCFG['clients'].' SET ';
		$query .= "cl_join_ts='".$db_coin->db_sanitize_data($_GPV['cl_join_ts'])."', ";
		$query .= "cl_status='".$db_coin->db_sanitize_data($_GPV['cl_status'])."', ";
		$query .= "cl_name_first='".$db_coin->db_sanitize_data($_GPV['cl_name_first'])."', ";
		$query .= "cl_name_last='".$db_coin->db_sanitize_data($_GPV['cl_name_last'])."', ";
		$query .= "cl_email='".$db_coin->db_sanitize_data($_GPV['cl_email'])."', ";
		$query .= "cl_user_name='".$db_coin->db_sanitize_data($_GPV['cl_user_name'])."', ";
		$query .= "cl_notes='".$db_coin->db_sanitize_data($_GPV['cl_notes'])."'";
		IF ($_SEC['_sadmin_flg']) {$query .= ", cl_groups='".$db_coin->db_sanitize_data($_GPV['cl_groups'])."'";}

	# Generate encrypted password
		IF ($_GPV['cl_user_pword']) {
			$cl_user_pword_crypt = do_password_crypt($_GPV['cl_user_pword']);
			$query .= ", cl_user_pword='".$db_coin->db_sanitize_data($cl_user_pword_crypt)."'";
		}

		IF ($_BV['B16'] == 1) {$query .= ", cl_company='".$db_coin->db_sanitize_data($_GPV['cl_company'])."'";}
		IF ($_BV['B15'] == 1) {$query .= ", cl_addr_01='".$db_coin->db_sanitize_data($_GPV['cl_addr_01'])."'";}
		IF ($_BV['B14'] == 1) {$query .= ", cl_addr_02='".$db_coin->db_sanitize_data($_GPV['cl_addr_02'])."'";}
		IF ($_BV['B13'] == 1) {$query .= ", cl_city='".$db_coin->db_sanitize_data($_GPV['cl_city'])."'";}
		IF ($_BV['B12'] == 1) {$query .= ", cl_state_prov='".$db_coin->db_sanitize_data($_GPV['cl_state_prov'])."'";}
		IF ($_BV['B11'] == 1) {$query .= ", cl_country='".$db_coin->db_sanitize_data($_GPV['cl_country'])."'";}
		IF ($_BV['B10'] == 1) {$query .= ", cl_zip_code='".$db_coin->db_sanitize_data($_GPV['cl_zip_code'])."'";}
		IF ($_BV['B09'] == 1) {$query .= ", cl_phone='".$db_coin->db_sanitize_data($_GPV['cl_phone'])."'";}

		$query .= " WHERE cl_id='".$_GPV['cl_id']."'";
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Adjust Data Array with returned record
		$data['_suser_id']	= $_GPV['cl_id'];

	# Build Title String, Content String, and Footer Menu String
		$_tstr 		= $_LANG['_CLIENTS']['Edit_Client_Info_Results'];
		$_cstr		.= '<center><table width="85%" cellspacing="5"><tr><td align="center" valign="top">'.$_nl;
		$_cstr		.= do_view_client_info($data, '1');
		$_cstr		.= '</td></tr></table></center>'.$_nl;
		$_mstr_flag	= '1';

		IF ($_SEC['_sadmin_flg']) {
			$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=edit&cl_id='.$_GPV['cl_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=view&cl_id='.$_GPV['cl_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

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
#   - Despite having the ability to disable some modules,
#       We did NOT disable the corresponding "Delete Records"
#       function, just in case there are old records in the
#		database for that function.
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_CLIENTS']['Delete_Client_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="clients">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_CLIENTS']['Delete_Client_Entry_Message'].'='.$_GPV['cl_id'].'<br>'.$_nl;
		$_cstr .= $_LANG['_CLIENTS']['Delete_Client_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_GPV['cl_name_first'].$_sp.$_GPV['cl_name_last'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="cl_id" value="'.$_GPV['cl_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_CLIENTS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag	= '1';
		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=edit&cl_id='.$_GPV['cl_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Dim some Vars:
		$_del_results = '';

	# Grab client record for passing into our API call BEFORE we delete the record
		$cl_query 	= 'SELECT * FROM '.$_DBCFG['clients'].' WHERE cl_id='.$_GPV['cl_id'];
		$cl_result 	= $db_coin->db_query_execute($cl_query) OR DIE("Unable to complete request");
		$numrows		= $db_coin->db_query_numrows($cl_result);

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($cl_result)) {
				$DClient['cl_id']			= $row['cl_id'];
				$DClient['cl_join_ts']		= $row['cl_join_ts'];
				$DClient['cl_status']		= $row['cl_status'];
				$DClient['cl_company']		= $row['cl_company'];
				$DClient['cl_name_first']	= $row['cl_name_first'];
				$DClient['cl_name_last']		= $row['cl_name_last'];
				$DClient['cl_addr_01']		= $row['cl_addr_01'];
				$DClient['cl_addr_02']		= $row['cl_addr_02'];
				$DClient['cl_city']			= $row['cl_city'];
				$DClient['cl_state_prov']	= $row['cl_state_prov'];
				$DClient['cl_country']		= $row['cl_country'];
				$DClient['cl_zip_code']		= $row['cl_zip_code'];
				$DClient['cl_phone']		= $row['cl_phone'];
				$DClient['cl_email']		= $row['cl_email'];
				$DClient['cl_user_name']		= $row['cl_user_name'];
				$DClient['cl_notes']		= $row['cl_notes'];
				$DClient['cl_groups']		= $row['cl_groups'];
			}

		# Do purge client
			$query 		= 'DELETE FROM '.$_DBCFG['clients'].' WHERE cl_id='.$_GPV['cl_id'];
			$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_02'].':'.$_sp.$db_coin->db_query_affected_rows();

		# Do purge client orders
			$query_o 		= 'DELETE FROM '.$_DBCFG['orders'].' WHERE ord_cl_id='.$_GPV['cl_id'];
			$result_o 	= $db_coin->db_query_execute($query_o) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_03'].':'.$_sp.$db_coin->db_query_affected_rows();

		# Do purge client invoices and invoices items
			$query_i 		= 'SELECT invc_id FROM '.$_DBCFG['invoices'].' WHERE invc_cl_id='.$_GPV['cl_id'];
			$result_i 	= $db_coin->db_query_execute($query_i) OR DIE("Unable to complete request");

		# Loop invoice id's and delete items
			IF ($db_coin->db_query_numrows($result_i)) {
				while(list($invc_id) = $db_coin->db_fetch_array($result_i)) {
				# Do query for invoice items delete
					$query_ii 	= 'DELETE FROM '.$_DBCFG['invoices_items'].' WHERE ii_invc_id='.$invc_id;
					$result_ii 	= $db_coin->db_query_execute($query_ii) OR DIE("Unable to complete request");
					$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_04'].':'.$_sp.$db_coin->db_query_affected_rows();

				# Do query for invoice trans delete
					$query_it 	= 'DELETE FROM '.$_DBCFG['invoices_trans'].' WHERE it_invc_id='.$invc_id;
					$result_it 	= $db_coin->db_query_execute($query_it) OR DIE("Unable to complete request");
					$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_05'].':'.$_sp.$db_coin->db_query_affected_rows();
				}
			}

		# Delete the invoices
			$query_i 		= 'DELETE FROM '.$_DBCFG['invoices'].' WHERE invc_cl_id='.$_GPV['cl_id'];
			$result_i 	= $db_coin->db_query_execute($query_i) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_06'].':'.$_sp.$db_coin->db_query_affected_rows();

		# Do purge domains
			$query_d 		= 'DELETE FROM '.$_DBCFG['domains'].' WHERE dom_cl_id='.$_GPV['cl_id'];
			$result_d 	= $db_coin->db_query_execute($query_d) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_07'].':'.$_sp.$db_coin->db_query_affected_rows();

		# Do purge client helpdesk tickets and and ticket msgs
			$query_hd 	= 'SELECT hd_tt_id FROM '.$_DBCFG['helpdesk'].' WHERE hd_tt_cl_id='.$_GPV['cl_id'];
			$result_hd 	= $db_coin->db_query_execute($query_hd) OR DIE("Unable to complete request");

		# Loop helpdesk msgs and delete messages
			IF ($db_coin->db_query_numrows($result_hd)) {
				while(list($hd_tt_id) = $db_coin->db_fetch_row($result_hd)) {
				# Do query for invoice items delete
					$query_hdi 	= 'DELETE FROM '.$_DBCFG['helpdesk_msgs'].' WHERE hdi_tt_id='.$hd_tt_id;
					$result_hdi 	= $db_coin->db_query_execute($query_hdi) OR DIE("Unable to complete request");
					$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_08'].':'.$_sp.$db_coin->db_query_affected_rows();
				}
			}

		# Delete the helpdesk tickets
			$query_hd 	= 'DELETE FROM '.$_DBCFG['helpdesk'].' WHERE hd_tt_cl_id='.$_GPV['cl_id'];
			$result_hd 	= $db_coin->db_query_execute($query_hd) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_09'].':'.$_sp.$db_coin->db_query_affected_rows();

		# Delete additional email addresses
			$query 		= 'DELETE FROM '.$_DBCFG['clients_contacts'].' WHERE contacts_cl_id='.$_GPV['cl_id'];
			$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$_del_results	.= '<br />'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_10'].':'.$_sp.$db_coin->db_query_affected_rows();

		#########################################################################################################
		# API Output Hook:
		# APIO_client_del: Client Deleted hook
			$_isfunc = 'APIO_client_del';
			IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_CLIENT_DEL_ENABLE'] == 1) {
				IF (function_exists($_isfunc)) {
					$_APIO = $_isfunc($DClient); $_APIO_ret .= '<br />'.$_APIO['msg'].'<br />';
				} ELSE {
					$_APIO_ret .= '<br />'.'Error- no function'.'<br />';
				}
			}
		#########################################################################################################

		} // END of delete records section

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_CLIENTS']['Delete_Client_Entry_Results'];

		IF (!$numrows) {
			$_cstr .= '<center>'.$_LANG['_CLIENTS']['An_error_occurred'].'<br />'.$_del_results.'<br /></center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_CLIENTS']['Delete_Client_Entry_Results_01'].':<br />'.$_del_results.'<br /></center>';
		}

	# Append API results
		$_cstr .= $_APIO_ret;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Mail
# Summary:
#	- eMail Client Profile
##############################
IF (!$_login_flag && $_GPV['mode'] == 'mail') {
	IF ($_GPV['stage'] != 2) {

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_CLIENTS']['eMail_Client_Confirmation'];

	# Do confirmation form to content string
		$_cstr = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=clients&mode=mail">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_CLIENTS']['eMail_Client_Message_prefix'].$_sp.$_GPV['cl_id'].$_sp.$_LANG['_CLIENTS']['eMail_Client_Message_suffix'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="cl_id" value="'.$_GPV['cl_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_CLIENTS']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag	= '1';
		$_mstr .= do_nav_link($_SERVER['PHP_SELF'].'?mod=clients&mode=view&cl_id='.$_GPV['cl_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br />'.$_nl;

	# Echo final output
		echo $_out;
	}

	IF ($_GPV['stage'] == 2) {
	# Call function for doing it.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_mail_profile($data, '1').$_nl;

	# Echo final output
		echo $_out;
	}
}


/**************************************************************
 * End Module Code
**************************************************************/
?>