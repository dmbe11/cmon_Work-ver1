<?php
/**
 * Module: Bills (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Bills
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_bills.php
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=bills');
		exit;
	}

# Get security vars
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_bills.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_bills_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_bills_override.php');
	}

# Include required and admin functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_common.php');
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php');


/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV[mode] and set default
	switch($_GPV['mode']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "autocopy":
			break;
		case "autoupdate":
			break;
		case "copy":
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "paid":
			break;
		case "trans":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = 'view';
			break;
	}

# Set default object
	IF (!$_GPV['obj']) {$_GPV['obj'] = 'bill';}

# Build time_stamp values when edit or add
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit' || $_GPV['mode'] == 'paid' ) {
		IF ($_GPV['bill_ts_year'] == '' || $_GPV['bill_ts_month'] == '' || $_GPV['bill_ts_day'] == '') {
			$_GPV['bill_ts'] = '';
		} ELSE {
			$_GPV['bill_ts'] = mktime(0,0,0, $_GPV['bill_ts_month'], $_GPV['bill_ts_day'], $_GPV['bill_ts_year']);
		}
		IF ($_GPV['bill_ts_due_year'] == '' || $_GPV['bill_ts_due_month'] == '' || $_GPV['bill_ts_due_day'] == '') {
			$_GPV['bill_ts_due'] = '';
		} ELSE {
			$_GPV['bill_ts_due'] = mktime(0,0,0, $_GPV['bill_ts_due_month'], $_GPV['bill_ts_due_day'], $_GPV['bill_ts_due_year']);
		}
		IF ($_GPV['bill_ts_paid_year'] == '' || $_GPV['bill_ts_paid_month'] == '' || $_GPV['bill_ts_paid_day'] == '') {
			$_GPV['bill_ts_paid'] = '';
		} ELSE {
			$_GPV['bill_ts_paid'] = mktime(0,0,0, $_GPV['bill_ts_paid_month'], $_GPV['bill_ts_paid_day'], $_GPV['bill_ts_paid_year']);
		}
		IF ($_GPV['bt_ts_year'] == '' || $_GPV['bt_ts_month'] == '' || $_GPV['bt_ts_day'] == '') {
			$_GPV['bt_ts'] = '';
		} ELSE {
			$_GPV['bt_ts'] = mktime(0,0,0, $_GPV['bt_ts_month'], $_GPV['bt_ts_day'], $_GPV['bt_ts_year']);
		}
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
	# Call validate input function
		$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP08'] != 1) {
	$_PFLAG = ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'delete' || $_GPV['mode'] == 'edit');
	IF ($_PERMS['AP10'] != 1 || ($_PERMS['AP10'] == 1 && $_PFLAG)) {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit;
	}
}


##############################
# Mode Call: Login
# Summary:
#	- Session not Registered
##############################
IF (!$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for articles listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'admin', 1).$_nl;

	# Echo final output
		echo $_out;
}




##############################
# Mode Call: View
# Summary:
#	- View Bill
##############################
IF (!$_login_flag && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'view') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;

	# Check for $_GPV[bill_id]
		IF (!$_GPV['bill_id']) {

	# Set only selected status bills for printing
		$_ps = '';
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
		IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

		# Build Title String, Content String, and Footer Menu String
			IF ($_GPV['bill_s_id']) {
				$_title = $_LANG['_BILLS']['View_Supplier_Bills'].$_sp.$_LANG['_BILLS']['l_Supplier_ID'].$_sp.$_GPV['bill_s_id'];
			} ELSE {
				$_title = $_LANG['_BILLS']['View_Supplier_Bills'];
			}

		# Add parameters "Edit" button
			IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
				$_title .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=bills">'.$_TCFG['_S_IMG_PM_S'].'</a>';
			}
			$_tstr = do_tstr_bill_action_list($_title);

			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = 0;} ELSE {$_mstr_flag = 1;}
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans&bill_s_id='.$_GPV['bill_s_id'], $_TCFG['_IMG_BILL_TRANS_M'],$_TCFG['_IMG_BILL_TRANS_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_url = '&sb='.$_GPV['sb'].'&so='.$_GPV['so'].'&fb='.$_GPV['fb'].'&fs='.$_GPV['fs'].'&rec_next='.$_GPV['rec_next'];
			$_mstr .= do_nav_link('mod_print.php?mod=bills'.$_url.$_ps, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');

			$_cstr .= '<br>'.$_nl;
			$_cstr .= do_view_bills($data).$_nl;
			$_cstr .= '<br>'.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, 1);
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;

		} ELSE {
			$_out .= do_display_entry($data).$_nl;

		# Echo final output
			echo $_out;
		}
}


##############################
# Mode Call: trans
# Summary:
#	- View Transactions
##############################
IF (!$_login_flag && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'trans') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;

	# Check for $_GPV[bill_id]
		IF (!$_GPV['bill_id']) {

		# Build Title String, Content String, and Footer Menu String
			IF ($_GPV['bill_s_id']) {
				$_tstr = $_LANG['_BILLS']['View_Supplier_Bill_Transactions'].$_sp.$_LANG['_BILLS']['l_Supplier_ID'].$_sp.$_GPV['bill_s_id'];
			} ELSE {
				$_tstr = $_LANG['_BILLS']['View_Supplier_Bill_Transactions'];
			}
			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = 0;} ELSE {$_mstr_flag = 1;}
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			$_mstr .= do_nav_link('mod_print.php?mod=bills&mode=trans&bill_s_id='.$_GPV['bill_s_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');

			$_cstr .= '<br>'.$_nl;
			$_cstr .= do_view_transactions($data).$_nl;
			$_cstr .= '<br>'.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, 1);
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;

		} ELSE {
			$_out .= do_display_entry($data).$_nl;

		# Echo final output
			echo $_out;
		}
}


##############################
# Mode Call: Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for Add / Edit form.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_form_add_edit($data, $err_entry).$_nl;

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
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Call timestamp function
		$_uts = dt_get_uts();

	# Build INSERT query
		$query  = 'INSERT INTO '.$_DBCFG['bills'].' (';
		$query .= 'bill_id, bill_status, bill_s_id, bill_invoice_number';
		$query .= ', bill_total_cost, bill_total_paid, bill_subtotal_cost';
		$query .= ', bill_tax_01_percent, bill_tax_01_amount, bill_tax_02_percent, bill_tax_02_amount';
		$query .= ', bill_tax_autocalc, bill_ts, bill_ts_due, bill_ts_paid, bill_cycle';
		$query .= ', bill_recurring, bill_recurr_proc';
		$query .= ')';

	# Get max / create new bill_id
		$_max_bill_id = do_get_max_bill_id();

		$query .= " VALUES ($_max_bill_id+1, ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_s_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_invoice_number'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_total_cost'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_total_paid'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_subtotal_cost'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_tax_01_percent'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_tax_01_amount'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_tax_02_percent'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_tax_02_amount'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_tax_autocalc'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_ts'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_ts_due'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_ts_paid'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_cycle'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_recurring'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['bill_recurr_proc'])."'";
		$query .= ')';

		$result = $db_coin->db_query_execute ($query) OR DIE("Unable to complete request");
		$_ins_bill_id		= $_max_bill_id+1;
		$_GPV['bill_id']	= $_ins_bill_id;

	# Insert Bill Debit Transaction
		$_bt_def = 0;
		$_bt_desc	= $_LANG['_BILLS']['l_Bill_ID'].$_sp.$_ins_bill_id;
		$q_it  = 'INSERT INTO '.$_DBCFG['bills_trans'].' (';
		$q_it .= 'bt_ts, bt_bill_id, bt_type';
		$q_it .= ', bt_origin, bt_desc, bt_amount';
		$q_it .= ') VALUES ( ';
		$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bill_ts'])."', ";
		$q_it .= "'".$db_coin->db_sanitize_data($_ins_bill_id)."', ";
		$q_it .= "'".$db_coin->db_sanitize_data($_bt_def)."', ";
		$q_it .= "'".$db_coin->db_sanitize_data($_bt_def)."', ";
		$q_it .= "'".$db_coin->db_sanitize_data($_bt_desc)."', ";
		$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bill_total_cost'])."'";
		$q_it .= ')';
		$r_it = $db_coin->db_query_execute($q_it);

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Rebuild Data Array with returned record
		$data['stage']		= $_GPV['stage'];
		$data['bill_id']	= $_ins_bill_id;

	# Call block it function
		$_out .= do_display_entry($data).$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for $_GPV[bill_id]- will determine select string (one for edit, all for list)
		IF (!$_GPV['bill_id'] || $_GPV['bill_id'] == 0) {
		# Set for list.
			$show_list_flag = 1;
		} ELSE {
		# Set Query for select and execute
			$query  = 'SELECT * FROM '.$_DBCFG['bills'];
			$query .= ' WHERE bill_id='.$_GPV['bill_id'];

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Set for no list.
			$show_list_flag = 0;
		}

	# Check flag- condition is show list
		IF ($show_list_flag) {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_BILLS']['View_Bills'];

			$_cstr  = '<br>'.$_nl;
			$_cstr .= do_view_bills($data).$_nl;
			$_cstr .= '<br>'.$_nl;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		# Call block it function
			$_out  = do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;

		} #if flag_list set

	# Check flag- condition is not show list
		IF (!$show_list_flag) {
		# If Stage and Error Entry, pass field vars to form,
		# Otherwise, pass looked up record to form
			IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
			# Call function for Add / Edit form.
				$_out  = '<!-- Start content -->'.$_nl;
				$_out .= do_form_add_edit($data, $err_entry).$_nl;

			# Echo final output
				echo $_out;

			} ELSE {
			# Process query results (assumes one returned row above)
				IF ($numrows) {
				# Process query results
					while ($row = $db_coin->db_fetch_array($result)) {
					# Merge Data Array with returned row
						$data_new	= array_merge($data, $row);
						$data	= $data_new;
					}
				}

			# Call function for Add / Edit form.
				$_out  = '<!-- Start content -->'.$_nl;
				$_out .= do_form_add_edit($data, $err_entry).$_nl;

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
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Calculate taxes
		$idata = do_calc_bill_values($data);

	# Get rid of commas
		$data['bill_total_cost']		= $idata['bill_total_cost'];		str_replace(',', '', $data['bill_total_cost']);
		$data['bill_subtotal_cost']	= $idata['bill_subtotal_cost'];	str_replace(',', '', $data['bill_subtotal_cost']);
		$data['bill_tax_01_percent']	= $idata['bill_tax_01_percent'];
		$data['bill_tax_01_amount']	= $idata['bill_tax_01_amount'];	str_replace(',', '', $data['bill_tax_01_amount']);
		$data['bill_tax_02_percent']	= $idata['bill_tax_02_percent'];
		$data['bill_tax_02_amount']	= $idata['bill_tax_02_amount'];	str_replace(',', '', $data['bill_tax_02_amount']);

	# Update Trans Entry for bill
		$_GPV['bt_type']		= 0;
		$_GPV['bt_bill_id']		= $_GPV['bill_id'];
		$_GPV['bt_ts']			= $_GPV['bill_ts'];
		$_tret				= do_set_bill_trans_values($_GPV);
		$data['bill_total_paid']	= do_get_bill_PTD($_GPV['bill_id']);

	# Do update
		$query  = 'UPDATE '.$_DBCFG['bills'].' SET ';
		$query .= "bill_status='".$db_coin->db_sanitize_data($_GPV['bill_status'])."', ";
		$query .= "bill_s_id='".$db_coin->db_sanitize_data($_GPV['bill_s_id'])."', ";
		$query .= "bill_invoice_number='".$db_coin->db_sanitize_data($_GPV['bill_invoice_number'])."', ";
		$query .= "bill_total_cost='".$db_coin->db_sanitize_data($data['bill_total_cost'])."', ";
		$query .= "bill_total_paid='".$db_coin->db_sanitize_data($data['bill_total_paid'])."', ";
		$query .= "bill_subtotal_cost='".$db_coin->db_sanitize_data($data['bill_subtotal_cost'])."', ";
		$query .= "bill_tax_autocalc='".$db_coin->db_sanitize_data($_GPV['bill_tax_autocalc'])."', ";
		$query .= "bill_tax_01_percent='".$db_coin->db_sanitize_data($data['bill_tax_01_percent'])."', ";
		$query .= "bill_tax_01_amount='".$db_coin->db_sanitize_data($data['bill_tax_01_amount'])."', ";
		$query .= "bill_tax_02_percent='".$db_coin->db_sanitize_data($data['bill_tax_02_percent'])."', ";
		$query .= "bill_tax_02_amount='".$db_coin->db_sanitize_data($data['bill_tax_02_amount'])."', ";
		$query .= "bill_ts='".$db_coin->db_sanitize_data($_GPV['bill_ts'])."', ";
		$query .= "bill_ts_due='".$db_coin->db_sanitize_data($_GPV['bill_ts_due'])."', ";
		$query .= "bill_ts_paid='".$db_coin->db_sanitize_data($_GPV['bill_ts_paid'])."', ";
		$query .= "bill_cycle='".$db_coin->db_sanitize_data($_GPV['bill_cycle'])."', ";
		$query .= "bill_recurring='".$db_coin->db_sanitize_data($_GPV['bill_recurring'])."', ";
		$query .= "bill_recurr_proc='".$db_coin->db_sanitize_data($_GPV['bill_recurr_proc'])."'";
		$query .= " WHERE bill_id='".$db_coin->db_sanitize_data($_GPV['bill_id'])."'";

		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Rebuild Data Array with returned record
		$data['stage']		= $_GPV['stage'];
		$data['bill_id']	= $_GPV['bill_id'];

	# Call block it function
		$_out .= do_display_entry($data).$_nl;
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
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BILLS']['Delete_Bill_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
		$_cstr .= '<input type=hidden name="mod" id="mod" value="bills">'.$_nl;
		$_cstr .= '<input type=hidden name="mode" id="mode" value="delete">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="bill_id" value="'.$_GPV['bill_id'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_BILLS']['Delete_Bill_Entry_Message'].'='.$_sp.$_GPV['bill_id'].'<br>'.$_nl;
		$_cstr .= $_LANG['_BILLS']['Delete_Bill_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_GPV['bill_id'].$_sp.'-'.$_sp.dt_make_datetime($_GPV['bill_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_sp.'-'.$_sp.$_GPV['bill_status'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_BACK_TO_bill_M'],$_TCFG['_IMG_BACK_TO_bill_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Do query for bill delete
		$query 		= 'DELETE FROM '.$_DBCFG['bills'].' WHERE bill_id='.$_GPV['bill_id'];
		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows		= $db_coin->db_query_affected_rows();
		$_del_results	= '<br>'.$_LANG['_BILLS']['Delete_Bill_Entry_Results_02'].':'.$_sp.$eff_rows;

	# Do query for bill items delete
		$query_ii 	= 'DELETE FROM '.$_DBCFG['bills_items'].' WHERE bi_bill_id='.$_GPV['bill_id'];
		$result_ii 	= $db_coin->db_query_execute($query_ii) OR DIE("Unable to complete request");
		$eff_rows_ii	= $db_coin->db_query_affected_rows();
		$_del_results	.= '<br>'.$_LANG['_BILLS']['Delete_Bill_Entry_Results_03'].':'.$_sp.$eff_rows_ii;

	# Do query for bill transactions delete
		$query_it 	= 'DELETE FROM '.$_DBCFG['bills_trans'].' WHERE bt_bill_id='.$_GPV['bill_id'];
		$result_it 	= $db_coin->db_query_execute($query_it) OR DIE("Unable to complete request");
		$eff_rows_it	= $db_coin->db_query_affected_rows();
		$_del_results	.= '<br>'.$_LANG['_BILLS']['Delete_Bill_Entry_Results_04'].':'.$_sp.$eff_rows_it;

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BILLS']['Delete_Bill_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_BILLS']['An_error_occurred'].'<br>'.$_del_results.'<br></center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_BILLS']['Delete_Bill_Entry_Results_01'].':<br>'.$_del_results.'<br></center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit', $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Copy Entry
# Summary Stage 1:
#	- Confirm copy entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'copy' && $_GPV['stage'] != 2) {
	# Content start flag
		$_out  = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BILLS']['Copy_Bill_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="bills">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="copy">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_BILLS']['Copy_Bill_Entry_Message'].'='.$_sp.$_GPV['bill_id'].'<br>'.$_nl;
		$_cstr .= $_LANG['_BILLS']['Copy_Bill_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="bill_id" value="'.$_GPV['bill_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_copy', 'SUBMIT', $_LANG['_BILLS']['B_Copy_Bill'], 'button_form_h', 'button_form', 1).$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_BACK_TO_bill_M'],$_TCFG['_IMG_BACK_TO_bill_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'copy' && $_GPV['stage'] == 2) {
	# Call Bill Copy function
		$_new_bill = do_bill_copy($data);

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BILLS']['Copy_Bill_Entry_Results'];

		IF ($_new_bill == 0) {
			$_cstr = '<center><br>'.$_LANG['_BILLS']['Copy_Bill_Entry_Results_01'].':'.$_sp.$_GPV['bill_id'].'<br></center>';
		} ELSE {
			$_cstr  = '<center><br>';
			$_cstr .= $_sp.$_LANG['_BILLS']['Copy_Bill_Entry_Results_02'].':';
			$_cstr .= $_sp.'<a href="mod.php?mod=bills&mode=edit&bill_id='.$_GPV['bill_id'].'">'.$_GPV['bill_id'].'</a>';
			$_cstr .= $_sp.$_LANG['_BILLS']['Copy_Bill_Entry_Results_03'].':';
			$_cstr .= $_sp.'<a href="mod.php?mod=bills&mode=edit&bill_id='.$_new_bill.'">'.$_new_bill.'</a>';
			$_cstr .= '<br></center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
	#	$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
	#	$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bill_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: 	ALL Modes
# Object:		Transaction
# Summary Stage 1:
#	- Display Add/Edit Entry
# Summary Stage 2:
#	- Do table update / action
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'trans') {

	# View Mode
	/*
	IF ($_GPV['mode'] == 'view') {
		IF ((!$_GPV['stage'] || $err_entry['flag'])) {
		# Call function for Add / Edit form.
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_items_editor($data, $err_entry).$_nl;

		# Echo final output
			echo $_out;
		}
	}
	*/
	# End View Mode

	# Start Add Mode
		IF ($_GPV['mode'] == 'add') {
			IF ($_GPV['stage'] != 2) {
			# Content start flag
				$_out  = '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_BILLS']['Set_Payment_Entry_Confirmation'];
				$_cstr = do_form_add_edit_trans($data, $err_entry);
				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			#	$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bt_bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_BACK_TO_bill_M'],$_TCFG['_IMG_BACK_TO_bill_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=add&obj=trans&bt_bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			# Call block it function
				$_out .= do_mod_block_it ($_tstr, $_cstr, 1, $_mstr, 1);
				$_out .= '<br>'.$_nl;

			# Echo final output
				echo $_out;

			} ELSE {
			# Set field values for update.
				IF (!$_GPV['bt_ts']) {$_GPV['bt_ts'] = dt_get_uts();}

			# Insert Bill Debit Transaction
				$_bt_def = 0;
				$_bt_desc	= $_LANG['_BILLS']['l_Bill_ID'].$_sp.$_ins_bill_id;
				$q_it  = 'INSERT INTO '.$_DBCFG['bills_trans'].' (';
				$q_it .= 'bt_ts, bt_bill_id, bt_type';
				$q_it .= ', bt_origin, bt_desc, bt_amount';
				$q_it .= ') VALUES ( ';
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_ts'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_bill_id'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_type'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_origin'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_desc'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_GPV['bt_amount'])."'";
				$q_it .= ")";
				$r_it = $db_coin->db_query_execute($q_it);
				$i_id = $db_coin->db_query_insertid();
				$_GPV['bt_id'] = $i_id;

			# Do status calc
				$ptd = do_get_bill_PTD($_GPV['bt_bill_id']);
				IF ($_GPV['bt_set_paid'] == 1) {
					$_us = 1; $_GPV['bill_status'] = $_CCFG['BILL_STATUS'][3];
				} ELSE {
				# Get bill amount
					$idata = do_get_bill_values($_GPV['bt_bill_id']);

				# Check against PTD
					IF ($idata['bill_total_cost'] <= $ptd) {
						$_us = 1; $_GPV['bill_status'] = $_CCFG['BILL_STATUS'][3];
					}
				}

			# Do update bill record
				$query	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
				$query	.= "bill_ts_paid='".$db_coin->db_sanitize_data($_GPV['bt_ts'])."', ";
				$query	.= "bill_total_paid='".$db_coin->db_sanitize_data($ptd)."'";
				IF ($_us == 1 && $_GPV['bill_status'] != '') {
					$query .= ", bill_status='".$db_coin->db_sanitize_data($_GPV['bill_status'])."'";
				}
				$query .= ' WHERE bill_id='.$_GPV['bt_bill_id'];

				$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

			# Content start flag
				$_out = '<!-- Start content -->'.$_nl;

			# Rebuild Data Array with returned record
				$data['stage']		= $_GPV['stage'];
				$data['bill_id']	= $_GPV['bt_bill_id'];

			# Call block it function
				$_out .= do_display_entry($data).$_nl;
				$_out .= '<br>'.$_nl;

			# Append API results
				$_out .= $_APIO_ret;

			# Echo final output
				echo $_out;
			}
		}
		# End Add Mode

	# Start Edit Mode
		IF ($_GPV['mode'] == 'edit') {
			IF ($_GPV['stage'] != 2) {
			# Set Query for select and execute
				$query =  'SELECT * FROM '.$_DBCFG['bills_trans'].' WHERE bt_id='.$_GPV['bt_id'];
				IF ($_GPV['bt_type'] == 0) {$query .= ' AND bt_type=0';}

			# Do select
				$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
				$numrows	= $db_coin->db_query_numrows($result);

			# Process query results (assumes one returned row above)
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
					# Merge Data Array with returned row
						$data_new			= array_merge($data, $row);
						$data			= $data_new;
						$data['bt_bill_id']	= $row['bt_bill_id'];
						$_GPV['bt_bill_id']	= $row['bt_bill_id'];
					}
				}

			# Content start flag
				$_out  = '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_BILLS']['Set_Payment_Entry_Confirmation'];
				$_cstr = do_form_add_edit_trans($data, $err_entry);

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			#	$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_BACK_TO_bill_M'],$_TCFG['_IMG_BACK_TO_bill_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=add&obj=trans&bt_bill_id='.$_GPV['bt_bill_id'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
				$_out .= '<br>'.$_nl;

			# Echo final output
				echo $_out;

			} ELSE {
			# Set field values for update.
				IF (!$_GPV['bt_ts']) {$_GPV['bt_ts'] = dt_get_uts();}

			# do_set_trans_values ( $atdata )
				$_tret = do_set_bill_trans_values($_GPV);

			# Do status calc
				$ptd = do_get_bill_PTD($_GPV['bt_bill_id']);
				IF ($_GPV['bt_set_paid'] == 1) {
					$_us = 1; $_GPV['bill_status'] = $_CCFG['BILL_STATUS'][3];
				} ELSE {
				# Get bill amount
					$idata = do_get_bill_values($_GPV['bt_bill_id']);

				# Check against PTD
					IF ($idata['bill_total_cost'] <= $ptd) {
						$_us = 1; $_GPV['bill_status'] = $_CCFG['BILL_STATUS'][3];
					}
				}

			# Do update bill record
				$query	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
				$query	.= "bill_ts_paid='".$db_coin->db_sanitize_data($_GPV['bt_ts'])."', ";
				$query	.= "bill_total_paid='".$db_coin->db_sanitize_data($ptd)."'";
				IF ($_us == 1 && $_GPV['bill_status'] != '') {
					$query .= ", bill_status='".$db_coin->db_sanitize_data($_GPV['bill_status'])."'";
				}
				$query .= ' WHERE bill_id='.$_GPV['bt_bill_id'];

				$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

			# Content start flag
				$_out = '<!-- Start content -->'.$_nl;

			# Rebuild Data Array with returned record
				$data['stage']		= $_GPV['stage'];
				$data['bill_id']	= $_GPV['bt_bill_id'];

			# Call block it function
				$_out .= do_display_entry($data).$_nl;
				$_out .= '<br>'.$_nl;

			# Echo final output
				echo $_out;
			}
		}
		# End Edit Mode

		# Delete Mode
		IF ($_GPV['mode'] == 'delete') {
			IF ($_GPV['stage'] != 2) {
			# Content start flag
				$_out  = '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_BILLS']['Delete_Trans_Entry_Confirmation'];

			# Do confirmation form to content string
				$_cstr  = '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
				$_cstr .= '<input type="hidden" name="mod" value="bills">'.$_nl;
				$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
				$_cstr .= '<input type="hidden" name="obj" value="trans">'.$_nl;
				$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_BILLS']['Delete_Trans_Entry_Message'].'='.$_sp.$_GPV['bt_id'].'?</b>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			#	$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			#	$_cstr .= $_GPV['bi_bill_id'].'-'.$_GPV['bi_item_no'].$_nl;
			#	$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
				$_cstr .= '<INPUT TYPE=hidden name="bt_id" value="'.$_GPV['bt_id'].'">'.$_nl;
				$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</FORM>'.$_nl;

				$_mstr  = do_nav_link('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$_GPV['bt_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&obj=trans&bt_id='.$_GPV['bt_id'], $_TCFG['_IMG_BACK_TO_BILL_M'],$_TCFG['_IMG_BACK_TO_BILL_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&obj=trans&bt_id='.$_GPV['bt_id'], $_TCFG['_IMG_BITEMS_EDITOR_M'],$_TCFG['_IMG_BITEMS_EDITOR_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
				$_out .= '<br>'.$_nl;

			# Echo final output
				echo $_out;
			}

			IF ($_GPV['stage'] == 2) {
			# Do select
				$query 	 = 'DELETE FROM '.$_DBCFG['bills_trans'];
				$query	.= ' WHERE bt_id='.$_GPV['bt_id'];
				$result 	 = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
				$eff_rows	 = $db_coin->db_query_affected_rows();

			# Do update bill record
				$ptd		 = do_get_bill_PTD($_GPV['bt_bill_id']);
				$query	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
				$query	.= 'bill_total_paid='.$ptd.' ';
				$query	.= 'WHERE bill_id='.$_GPV['bt_bill_id'];
				$result	 = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

			# Content start flag
				$_out  = '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_BILLS']['Delete_Trans_Entry_Results'];

				IF (!$eff_rows) {
					$_cstr = '<center>'.$_LANG['_BILLS']['An_error_occurred'].'</center>';
				} ELSE {
					$_cstr = '<center>'.$_LANG['_BILLS']['Delete_Trans_Entry_Results_01'].'</center>';
				}

			# Append API results
				$_cstr .= $_APIO_ret;

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=trans', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
				$_out .= '<br>'.$_nl;

			# Echo final output
				echo $_out;
			}
		}
		# End Delete Mode
}


##############################
# Mode Call: 	ALL Modes
# Object:		Item
# Summary:
#	- For all items
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bitem') {

	# View Mode
	IF ($_GPV['mode'] == 'view') {
		IF ((!$_GPV['stage'] || $err_entry['flag'])) {
		# Call function for Add / Edit form.
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_items_editor($data, $err_entry).$_nl;

		# Echo final output
			echo $_out;
		}
	}
	# End View Mode

	# Add Mode
	IF ($_GPV['mode'] == 'add') {
		IF ((!$_GPV['stage'] || $err_entry['flag'])) {
		# Call function for Add / Edit form.
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_items_editor($data, $err_entry).$_nl;

		# Echo final output
			echo $_out;

		} ELSEIF (($_GPV['stage'] && !$err_entry['flag'])) {
		# Check and if item, retrieve and set vars.
			IF ($_GPV['bi_prod_add'] && $_GPV['bi_prod_id']) {
			# Get current product price
				$query_prod	= 'SELECT * FROM '.$_DBCFG['products'].' WHERE prod_id='.$_GPV['bi_prod_id'];
				$result_prod 	= $db_coin->db_query_execute($query_prod) OR DIE("Unable to complete request");
				$numrows_prod	= $db_coin->db_query_numrows($result_prod);

			# Process query results
				IF ($numrows_prod) {
					while ($row = $db_coin->db_fetch_array($result_prod)) {
						$_GPV['bi_item_name']		= $row['prod_name'];
						$_GPV['bi_item_desc']		= $row['prod_desc'];
						$_GPV['bi_item_cost']		= $row['prod_unit_cost'];
						$_GPV['bi_apply_tax_01']		= $row['prod_apply_tax_01'];
						$_GPV['bi_apply_tax_02']		= $row['prod_apply_tax_02'];
						$_GPV['bi_calc_tax_02_pb']	= $row['prod_calc_tax_02_pb'];
					}
				}
			}

		# Get max / create new bi_item_no
			$_max_bill_item_no = do_get_max_bill_item_no($_GPV['bill_id']);

		# Build SQL and execute.
			$query	 = 'INSERT INTO '.$_DBCFG['bills_items'].' (';
			$query	.= 'bi_bill_id, bi_item_no, bi_item_name';
			$query	.= ', bi_item_desc, bi_item_cost';
			$query	.= ', bi_apply_tax_01, bi_apply_tax_02, bi_calc_tax_02_pb';
			$query	.= ') VALUES (';
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_bill_id'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_max_bill_item_no+1)."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_item_name'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_item_desc'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_item_cost'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_apply_tax_01'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_apply_tax_02'])."', ";
			$query	.= "'".$db_coin->db_sanitize_data($_GPV['bi_calc_tax_02_pb'])."'";
			$query	.= ')';

			$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$_ret	= do_set_bill_values($_GPV['bi_bill_id'], 1);

		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Rebuild Data Array with returned record
			$data['stage']				= $_GPV['stage'];
			$data['mode']				= 'view';
			$data['bill_id']			= $_GPV['bill_id'];
			$data['bi_bill_id']			= '';
			$data['bi_item_no']			= '';
			$data['bi_item_name']		= '';
			$data['bi_item_desc']		= '';
			$data['bi_item_cost']		= '';
		#	$data['bi_apply_tax_01']		= 1;
		#	$data['bi_apply_tax_02']		= 1;
		#	$data['bi_calc_tax_02_pb']	= 1;

		# Call block it function
			$_out .= do_display_items_editor($data, $err_entry).$_nl;
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}
	}
	# End Add Mode

	# Start Edit Mode
	IF ($_GPV['mode'] == 'edit') {
		IF ((!$_GPV['stage'] || $err_entry['flag'])) {
		# Check for id's- will determine select string (one for edit, all for list)
			IF (!$_GPV['bi_bill_id'] || $_GPV['bi_bill_id'] == 0 || !$_GPV['bi_item_no'] || $_GPV['bi_item_no'] == 0) {
			# Set for list.
				$show_list_flag = 1;
			} ELSE {
			# Set Query for select and execute
				$query  = 'SELECT * FROM '.$_DBCFG['bills_items'];
				$query .= ' WHERE bi_bill_id='.$_GPV['bi_bill_id'];
				$query .= ' AND bi_item_no='.$_GPV['bi_item_no'];

			# Do select
				$result	= $db_coin->db_query_execute($query);
				$numrows	= $db_coin->db_query_numrows($result);

			# Set for no list.
				$show_list_flag = 0;
			}

		# Check flag- condition is show list
			IF ($show_list_flag) {
			# Rebuild Data Array with returned record
				$data['bill_id'] = $_GPV['bi_bill_id'];

			# Call function for Add / Edit form.
				$_out = '<!-- Start content -->'.$_nl;
				$_out .= do_display_items_editor($data, $err_entry).$_nl;

			# Echo final output
				echo $_out;

			} #if flag_list set

		# Check flag- condition is not show list
			IF (!$show_list_flag) {
			# If Stage and Error Entry, pass field vars to form,
			# Otherwise, pass looked up record to form
				IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
				# Rebuild Data Array with returned record
					$data['bill_id'] = $_GPV['bi_bill_id'];

				# Call function for Add / Edit form.
					$_out  = '<!-- Start content -->'.$_nl;
					$_out .= do_display_items_editor($data, $err_entry).$_nl;

				# Echo final output
					echo $_out;

				} ELSE {
				# Process query results (assumes one returned row above)
					IF ($numrows) {
					# Process query results
						while ($row = $db_coin->db_fetch_array($result)) {
						# Merge Data Array with returned row
							$data_new			= array_merge($data, $row);
							$data			= $data_new;
							$data['bill_id']	= $row['bi_bill_id'];
						}
					}

				# Call function for Add / Edit form.
					$_out  = '<!-- Start content -->'.$_nl;
					$_out .= do_display_items_editor($data, $err_entry).$_nl;

				# Echo final output
					echo $_out;
				}
			}

		} ELSEIF (($_GPV['stage'] && !$err_entry['flag'])) {
		# Do update
			$query  = 'UPDATE '.$_DBCFG['bills_items'].' SET ';
			$query .= "bi_item_no='".$db_coin->db_sanitize_data($_GPV['bi_item_no'])."', ";
			$query .= "bi_item_name='".$db_coin->db_sanitize_data($_GPV['bi_item_name'])."', ";
			$query .= "bi_item_desc='".$db_coin->db_sanitize_data($_GPV['bi_item_desc'])."', ";
			$query .= "bi_item_cost='".$db_coin->db_sanitize_data($_GPV['bi_item_cost'])."', ";
			$query .= "bi_apply_tax_01='".$db_coin->db_sanitize_data($_GPV['bi_apply_tax_01'])."', ";
			$query .= "bi_apply_tax_02='".$db_coin->db_sanitize_data($_GPV['bi_apply_tax_02'])."', ";
			$query .= "bi_calc_tax_02_pb='".$db_coin->db_sanitize_data($_GPV['bi_calc_tax_02_pb'])."'";
			$query .= ' WHERE bi_bill_id='.$_GPV['bi_bill_id'];
			$query .= " AND bi_item_no='".$db_coin->db_sanitize_data($_GPV['bi_item_no_orig'])."'";

			$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$numrows	= $db_coin->db_query_affected_rows();

			$_ret	= do_set_bill_values($_GPV['bi_bill_id'], 1);
		# Rebuild Data Array with returned record
			$data['stage']				= $_GPV['stage'];
			$data['mode']				= 'view';
			$data['bill_id']			= $_GPV['bi_bill_id'];
			$data['bi_bill_id']			= $_GPV['bi_bill_id'];
			$data['bi_item_no']			= $_GPV['bi_item_no'];
			$data['bi_item_no_orig']		= $_GPV['bi_item_no_orig'];
			$data['bi_item_name']		= $_GPV['bi_item_name'];
			$data['bi_item_desc']		= $_GPV['bi_item_desc'];
			$data['bi_item_cost']		= $_GPV['bi_item_cost'];
			$data['bi_apply_tax_01']		= $_GPV['bi_apply_tax_01'];
			$data['bi_apply_tax_02']		= $_GPV['bi_apply_tax_02'];
			$data['bi_calc_tax_02_pb']	= $_GPV['bi_calc_tax_02_pb'];

		# Call block it function
			$_out .= do_display_items_editor($data, $err_entry).$_nl;
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}
	}
	# End Edit Mode

	# Delete Mode
	IF ($_GPV['mode'] == 'delete') {
		IF ($_GPV['stage'] != 2) {
		# Content start flag
			$_out  = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_BILLS']['Delete_BItem_Entry_Confirmation'];

		# Do confirmation form to content string
			$_cstr  = '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="bills">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="bitem">'.$_nl;
			$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
			$_cstr .= '<input type="hidden" name="bi_bill_id" value="'.$_GPV['bi_bill_id'].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="bi_item_no" value="'.$_GPV['bi_item_no'].'">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_BILLS']['Delete_BItem_Entry_Message'].'='.$_sp.$_GPV['bi_bill_id'].'-'.$_GPV['bi_item_no'].'?</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= $_GPV['bi_bill_id'].'-'.$_GPV['bi_item_no'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;

			$_mstr  = do_nav_link('mod.php?mod=bills&mode=edit&obj=bitem&bill_id='.$_GPV['bi_bill_id'].'&bi_item_no='.$_GPV['bi_item_no'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$_GPV['bi_bill_id'], $_TCFG['_IMG_BACK_TO_BILL_M'],$_TCFG['_IMG_BACK_TO_BILL_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&obj=bitem&bill_id='.$_GPV['bi_bill_id'], $_TCFG['_IMG_BITEMS_EDITOR_M'],$_TCFG['_IMG_BITEMS_EDITOR_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}

		IF ($_GPV['stage'] == 2) {
		# Do select
			$query 	 = 'DELETE FROM '.$_DBCFG['bills_items'];
			$query	.= ' WHERE bi_bill_id='.$_GPV['bi_bill_id'];
			$query	.= ' AND bi_item_no='.$_GPV['bi_item_no'];
			$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$eff_rows	= $db_coin->db_query_affected_rows();

		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_BILLS']['Delete_BItem_Entry_Results'];

			IF (!$eff_rows) {
				$_cstr = '<center>'.$_LANG['_BILLS']['An_error_occurred'].'</center>';
			} ELSE {
				$_ret  = do_set_bill_values($_GPV['bi_bill_id'], 1);
				$_cstr = '<center>'.$_LANG['_BILLS']['Delete_BItem_Entry_Results_01'].'</center>';
			}

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&obj=bitem&bill_id='.$_GPV['bi_bill_id'], $_TCFG['_IMG_BITEMS_EDITOR_M'],$_TCFG['_IMG_BITEMS_EDITOR_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'].'','');

		# Call block it function
			$_out .= do_mod_block_it ($_tstr, $_cstr, 1, $_mstr, 1);
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}
		}
		# End Delete Mode

} #last one for items




##############################
# Mode Call: Auto-Update
# Summary:
#	- Auto-Set Bill Status
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'autoupdate') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		IF ($_SEC['_sadmin_flg']) {
			$_tstr = $_LANG['_BILLS']['Auto_Bill_Update_Results'].':';
			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = 0;} ELSE {$_mstr_flag = 1;}
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		}
		$_cstr = '<div align="center">'.$_nl;
		IF ($_ACFG['BILL_AUTO_UPDATE_ENABLE'] == 1) {
			$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Update_Status'].$_sp.do_auto_bill_set_status().$_nl;
		} ELSE {
			$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Update_Status'].$_sp.$_LANG['_BILLS']['Function_Disabled'].$_nl;
		}
		$_cstr .= '<br><br>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}




##############################
# Mode Call: Auto-Copy
# Summary:
#	- Auto-Copy Recurring
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['obj'] == 'bill' && $_GPV['mode'] == 'autocopy') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		IF ($_SEC['_sadmin_flg']) {
			$_tstr = $_LANG['_BILLS']['Auto_Bill_Copy_Results'].':';
			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = 0;} ELSE {$_mstr_flag = 1;}
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		}
		$_cstr = '<div align="center">'.$_nl;
		IF ($_ACFG['BILL_AUTO_COPY_ENABLE'] == 1) {
			$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Copy_Recurring'].$_sp.do_auto_bill_copy().$_nl;
		} ELSE {
			$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Copy_Recurring'].$_sp.$_LANG['_BILLS']['Function_Disabled'].$_nl;
		}
		$_cstr .= '<br><br>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}



/**************************************************************
 * End Module Code
**************************************************************/
?>