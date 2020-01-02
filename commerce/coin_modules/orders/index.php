<?php
/**
 * Module: Orders (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Orders
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_orders.php
 */

# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=orders');
		exit;
	}

# Get security vars
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Remove illegal characters from domain name
	IF (isset($_GPV['ord_domain'])) {
		$_GPV['ord_domain'] = strtolower($_GPV['ord_domain']);
		$rest = substr($_GPV['ord_domain'], 0, 5);
		IF ($rest == "https") {$_GPV['ord_domain'] = eregi_replace('https://', '', $_GPV['ord_domain']);}
		$rest = substr($_GPV['ord_domain'], 0, 4);
		IF ($rest == "http") {$_GPV['ord_domain'] = eregi_replace('http://', '', $_GPV['ord_domain']);}
		IF ($rest == "www.") {$_GPV['ord_domain'] = eregi_replace('www.', '', $_GPV['ord_domain']);}
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_orders.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_orders_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_orders_override.php');
	}

# Include required functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php');}

/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV[mode] and set default
	$_login_flag = 0;
	switch($_GPV['mode']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "cor":
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "order":
		# Include required functions file
			require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs_order.php');
			break;
		case "mail":
			break;
		case "return":
		# Include required functions file
			require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs_order.php');
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = 'order';
		# Include required functions file
			require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs_order.php');
			break;
	}

# Build time_stamp values when edit or add
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit') {
		IF ($_GPV['ord_ts_hour'] == '')	{$_GPV['ord_ts_hour'] = 0;}
		IF ($_GPV['ord_ts_minute'] == '')	{$_GPV['ord_ts_minute'] = 0;}
		IF ($_GPV['ord_ts_second'] == '') 	{$_GPV['ord_ts_second'] = 0;}
		IF ($_GPV['ord_ts_year'] == '' || $_GPV['ord_ts_month'] == '' || $_GPV['ord_ts_day'] == '') {
		   $_GPV['ord_ts'] = '';
		} ELSE {
			$_GPV['ord_ts'] = mktime($_GPV['ord_ts_hour'],$_GPV['ord_ts_minute'],$_GPV['ord_ts_second'],$_GPV['ord_ts_month'],$_GPV['ord_ts_day'],$_GPV['ord_ts_year']);
		}
		#	ELSE	{$_GPV['ord_ts'] = mktime( 0,0,0,$_GPV['ord_ts_month'],$_GPV['ord_ts_day'],$_GPV['ord_ts_year']);}
	}

# Support free vs paid products
	IF (isset($_GPV['free']) && $_GPV['free'] == 1) {
		$_CCFG['_FREETRIAL'] = 1;
	} ELSE {
		$_CCFG['_FREETRIAL'] = 0;
	}

# Unwanted text parser call
IF ($_GPV['mode'] == 'order') {
	# Loop $_GPV array to parse out nasties from anonymous input data
		while(list($key, $var) = each($_GPV)) {
			IF ($key != 'NULL') {
				$_GPV[$key] = do_parse_input_data($var);
			}
		}
		reset($_GPV);
}


# Get the users ID and original name from session/database read
	IF (!$_SEC['_sadmin_flg']) {
		$_GPV['cl_id']				= $_SEC['_suser_id'];
		$_GPV['ord_cl_id']			= $_SEC['_suser_id'];
		$_c_info					= get_contact_client_info($_GPV['ord_cl_id']);
		$_GPV['ord_user_name_orig']	= $_c_info['cl_user_name'];
	}

	IF ($_SEC['_sadmin_flg'] && $_GPV['stage'] == 1 && $_GPV['mode'] == 'add') {
		$_c_info					= get_contact_client_info($_GPV['ord_cl_id']);
		$_GPV['ord_user_name']		= $_c_info['cl_user_name'];
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
# Mode Call: Order
# Summary:
#	- Placing Orders
##############################
IF ($_GPV['mode'] == 'order') {
	# Set orders Session Records (purge, update, insert)
		$_ret = do_orders_session_set();

	# Call input validation code
		IF ($_GPV['stage'] == 2 && $_GPV['cor_form'] == 1 && !$_GPV['b_ccor']){$cerr_entry	= do_cor_validation($_GPV);}
		IF ($_GPV['stage'] > 2)										{$err_entry	= do_input_validation($_GPV);}

	# Required TOS / AUP Flags
		IF ($_GPV['ord_accept_tos'] != 1 && $_CCFG['ORDERS_TOS_ENABLE'] == 1) {$_req_tos_err = 1;} ELSE {$_req_tos_err = 0;}
		IF ($_GPV['ord_accept_aup'] != 1 && $_CCFG['ORDERS_AUP_ENABLE'] == 1) {$_req_aup_err = 1;} ELSE {$_req_aup_err = 0;}
		IF (!$_GPV['ord_prod_id'])									{$_req_prod_err = 1;} ELSE {$_req_prod_err = 0;}
		IF (!$_GPV['ord_vendor_id'] && !$_CCFG['DEFAULT_PAYMENT_METHOD'])	{$_req_vend_err = 1;} ELSE {$_req_vend_err = 0;}

	# Determine Step / Check Referer
	#	echo 'Refer: '.getenv("HTTP_REFERER");
	#	$_exp = getenv("HTTP_REFERER");
	#	IF (!eregi("mod\.php\?mod=orders", getenv("HTTP_REFERER")) ) { $_GPV[stage] = 0; }
	#	IF (!eregi('^[a-zA-Z0-9_\.\-]+.'.'mod.php?mod=orders'.'$', getenv("HTTP_REFERER")) ) { $_GPV[stage] = 0; }

	# Check Stgae and conditions for setting ORD_STEP
	# 	Order Step Screen: Product Select/Payment Select/TOS/AUP Acceptance		$_ORD_STEP == 0
	# 	Order Step Screen: Custom Order Request (may be skipped)				$_ORD_STEP == 1
	# 	Order Step Screen: Order Information- Client / Domain Info.				$_ORD_STEP == 2
	# 	Order Step Screen: Final Confirmation Of Data- 						$_ORD_STEP == 3
	# 	Order Step: Pay Link- 											$_ORD_STEP == 4

		switch($_GPV['stage']) {
			case "":
				$_ORD_STEP = 0;
				break;
			case "0":
				$_ORD_STEP = 0;
				break;
			case "1":
				IF ($_GPV['b_cor'] != '') {
					$_GPV['cor_flag'] = 1;
					$data['uw'] = 'ORD';
					do_orders_session_update($data);
					$_ORD_STEP = 1;
					break;
				}
				IF (($_req_tos_err || $_req_aup_err || $_req_prod_err || $_req_vend_err) && !$_GPV['cor_flag']) {$_ORD_STEP = 0; break;}
				IF ($_GPV['b_continue'] != '') {
					$_GPV['cor_flag'] = 0;
					$data['uw'] = 'ORD';
					do_orders_session_update($data);
					$_ORD_STEP = 2;
					break;
				}
				break;
			case "2":
				IF ($_GPV['cor_flag'] && $cerr_entry['flag']) {$_ORD_STEP = 1; break;}
				IF ($_GPV['b_ccor'] != '') {
					$_GPV['cor_flag'] = 0;
					$data['uw'] = 'COR';
					do_orders_session_update($data);
					$_ORD_STEP = 0;
					break;
				}
				IF ($_GPV['b_ccontinue'] != '') {
					$_GPV['cor_flag'] = 1;
					$data['uw'] = 'COR';
					do_orders_session_update($data);
					$_ORD_STEP = 2;
					break;
				}
				IF (($_req_tos_err || $_req_aup_err || $_req_vend_err) && !$_GPV['cor_flag']) {$_ORD_STEP = 0; break;}
				IF ($_GPV['b_continue'] != '') {
					$_GPV['cor_flag'] = 0;
					$data['uw'] = 'ORD';
					do_orders_session_update($data);
					$_ORD_STEP = 2;
					break;
				}
				break;
			case "3":
				IF ($_GPV['b_restart'] != '' || (($_req_tos_err || $_req_aup_err || $_req_vend_err) && !$_GPV['cor_flag'])) {$_ORD_STEP = 0; break;}
				IF ($err_entry['flag'])			{$_ORD_STEP = 2; break;}
				IF ($_GPV['b_continue'] != '')	{
					$data['uw'] = 'ORD';
					do_orders_session_update($data);

				# Merge Data Array with session data
					$data_sess	= do_orders_session_select();
					$data_new		= array_merge($data, $data_sess);
					$data		= $data_new;
					$_ORD_STEP = 3; break;
				}
				break;
			case "4":
				IF (($_req_tos_err || $_req_aup_err) && !$_GPV['cor_flag']) {$_ORD_STEP = 0; break;}
				IF ($_GPV['b_edit'] != '' || $_GPV['b_restart'] != '') {

				# Merge Data Array with session data
					$data_sess	= do_orders_session_select();
					$data_new		= array_merge($data, $data_sess);
					$data		= $data_new;
					unset($err_entry);
					IF ($_GPV['cor_flag'] == 1) {
						$_ORD_STEP = 1; break;
					} ELSE {
						IF ($_GPV['b_edit'] != '') 	{$_ORD_STEP = 2; break;}
						IF ($_GPV['b_restart'] != '') {$_ORD_STEP = 0; break;}
					}
				}
				IF ($_GPV['b_continue'] != '') {

				# Merge Data Array with session data
					$data_sess	= do_orders_session_select();
					$data_new		= array_merge($data, $data_sess);
					$data		= $data_new;
					$_ORD_STEP = 4; break;
				}
				break;
			default:
				$_ORD_STEP = 0;
				break;
		}

	# Order Step Screen: Product Select/Payment Select/TOS/AUP Acceptance
		IF ($_ORD_STEP == 0) {

		# Set error flags for TOS/AUP/vendor_id
		IF ($data['stage'] == 1) {
			IF ($_req_tos_err == 1)	{$err_entry['ord_accept_tos'] = 1;}
			IF ($_req_aup_err == 1) 	{$err_entry['ord_accept_aup'] = 1;}
			IF ($_req_vend_err == 1) {$err_entry['ord_vendor_id'] = 1;}
		}

		# Clear processed for next order.
			$sdata['os_ord_id'] 			= '';
			$sdata['os_ord_processed']		= '0';
			$sdata['os_ord_ret_processed']	= '0';
			do_orders_session_set_proc($sdata);
			do_orders_session_set_ret_proc($sdata);

		# Call function next step
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_order_00($data, $err_entry, '1');
			echo $_out;
		}

	# Order Step Screen: Custom Order Request (may be skipped)
		IF ($_ORD_STEP == 1) {

		# Check cor_flag and cor_form for update of order sessions
			IF ($_GPV['stage']==1 && $_GPV['cor_flag'] == 1) {$data = do_orders_session_select();}

		# Call function next step
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_order_01($data, $cerr_entry, '1');
			echo $_out;
		}

	# Order Step Screen: Order Information- Client / Domain Info.
		IF ($_ORD_STEP == 2) {
			$data['stage'] = 2;
		# Call function next step
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_order_02($data, $err_entry, '1');
			echo $_out;
		}

	# Order Step Screen: Final Confirmation Of Data
		IF ($_ORD_STEP == 3) {

		# Call function next step
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_order_03($data, $err_entry, '1');
			echo $_out;
		}

	# Order Step: Pay Link
		IF ($_ORD_STEP == 4) {

		# Call function next step
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_display_order_04($data, $err_entry, '1');
			echo $_out;
		}
}


##############################
# Mode Call: Return
# Summary:
#	- Return From Billing
##############################
IF ($_GPV['mode'] == 'return') {
	# Call function for output
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_order_return($data, '1');

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Login
# Summary:
#	- Session not Registered
##############################
IF ($_GPV['mode']!='order' && $_GPV['mode'] != 'return' && $_GPV['mode'] != 'cor' && !$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for login
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'user', '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: View
# Summary:
#	- View Order
##############################
IF (!$_login_flag && $_GPV['mode'] == 'view') {
	# Set content flag
		$_out = '<!-- Start content -->'.$_nl;

	# Check for ord_id
		IF (!$_GPV['ord_id']) {
			$data['_suser_id'] = $_SEC['_suser_id'];

		# Build Title String, Content String, and Footer Menu String
			IF ($_SEC['_sadmin_flg']) {
				IF ($_GPV['ord_cl_id'] > 0) {
					$_tstr = $_LANG['_ORDERS']['View_Client_Orders'].$_sp.$_LANG['_ORDERS']['l_Client_ID'].$_sp.$_GPV['ord_cl_id'];

				# Add parameters "Edit" button
					IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
						$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=orders">'.$_TCFG['_S_IMG_PM_S'].'</a>';
					}
				} ELSE {
					$_tstr = $_LANG['_ORDERS']['View_Client_Orders'];

				# Add parameters "Edit" button
					IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
						$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=orders">'.$_TCFG['_S_IMG_PM_S'].'</a>';
					}
				}
				$_mstr .= do_nav_link ('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				}
			} ELSE {
				$_tstr = $_LANG['_ORDERS']['View_Client_Orders_For'].' '.$_SEC['_suser_name'];
			}

		# Set only selected status orders for printing
			$_ps = '';
			IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
			IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

			$_url = '&sb='.$_GPV['sb'].'&so='.$_GPV['so'].'&fb='.$_GPV['fb'].'&fs='.$_GPV['fs'].'&rec_next='.$_GPV['rec_next'];
			$_mstr .= do_nav_link('mod_print.php?mod=orders&mode=view'.$_url.$_ps, $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			IF ($_CCFG['_IS_PRINT'] == 1) {$_mstr_flag = '0';} ELSE {$_mstr_flag = '1';}

			$_cstr .= '<br>'.$_nl;
			$_cstr .= do_view_orders($data, '1').$_nl;
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
# Mode Call: 	Add / Edit
# Summary:
#	- Data input validation
##############################
IF ($_SEC['_sadmin_flg'] && ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit') && $_GPV['stage'] == 1) {
	$err_entry = do_input_validation($_GPV);
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
		$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;

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
	# Call timestamp function
		$_uts = dt_get_uts();

	# Set Query for select and execute for client info
		$query = 'SELECT * FROM '.$_DBCFG['clients'];
		$query .= " WHERE cl_id='".$_GPV['ord_cl_id']."'";

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above- need to verify)
		while ($row = $db_coin->db_fetch_array($result)) {

		# Rebuild Data Array with returned record
			$_GPV['ord_cl_id']			= $row['cl_id'];
			$_GPV['ord_company']		= $row['cl_company'];
			$_GPV['ord_name_first']		= $row['cl_name_first'];
			$_GPV['ord_name_last']		= $row['cl_name_last'];
			$_GPV['ord_addr_01']		= $row['cl_addr_01'];
			$_GPV['ord_addr_02']		= $row['cl_addr_02'];
			$_GPV['ord_city']			= $row['cl_city'];
			$_GPV['ord_state_prov']		= $row['cl_state_prov'];
			$_GPV['ord_country']		= $row['cl_country'];
			$_GPV['ord_zip_code']		= $row['cl_zip_code'];
			$_GPV['ord_phone']			= $row['cl_phone'];
			$_GPV['ord_email']			= $row['cl_email'];
			$_GPV['ord_user_name']		= $row['cl_user_name'];
			$_GPV['ord_user_pword']		= $row['cl_user_pword'];
		# For auto-generated invoice
			$_GPV['os_ord_cl_id']		= $row['cl_id'];
			$_GPV['cl_id']         		= $row['cl_id'];
		}

	# Get current product price
		$query_prod	= 'SELECT * FROM '.$_DBCFG['products']." WHERE prod_id='".$_GPV['ord_prod_id']."'";
		$result_prod 	= $db_coin->db_query_execute($query_prod) OR DIE("Unable to complete request");
		$numrows_prod	= $db_coin->db_query_numrows($result_prod);

		IF ($numrows_prod) {

		# Process query results
			while ($row = $db_coin->db_fetch_array($result_prod)) {
				$_GPV['ord_unit_cost'] 			= $row['prod_unit_cost'];
				$_GPV['prod_dom_type'] 			= $row['prod_dom_type'];
				$_GPV['prod_allow_domains'] 		= $row['prod_allow_domains'];
				$_GPV['prod_allow_subdomains'] 	= $row['prod_allow_subdomains'];
				$_GPV['prod_allow_disk_space_mb']	= $row['prod_allow_disk_space_mb'];
				$_GPV['prod_allow_traffic_mb']	= $row['prod_allow_traffic_mb'];
				$_GPV['prod_allow_mailboxes']		= $row['prod_allow_mailboxes'];
				$_GPV['prod_allow_databases']		= $row['prod_allow_databases'];

			# For auto-invoices
				$_GPV['prod_cost']	 			= $row['prod_unit_cost'];
				$_GPV['prod_apply_tax_01'] 		= $row['prod_apply_tax_01'];
				$_GPV['prod_apply_tax_02'] 		= $row['prod_apply_tax_02'];
				$_GPV['prod_calc_tax_02_pb']		= $row['prod_calc_tax_02_pb'];
				$_GPV['prod_id'] 				= $row['prod_id'];
				$_GPV['prod_name'] 				= $row['prod_name'];
				$_GPV['prod_desc'] 				= $row['prod_desc'];
				$_GPV['prod_unit_cost'] 			= $row['prod_unit_cost'];

			}
		}

	# Do domain check for insert of server account and domain
		IF (strtolower($_GPV['ord_domain']) == "none") {$NoDomain=1;} ELSE {$NoDomain=0;}
		IF ($_CCFG['DOMAINS_ENABLE'] && !do_domain_exist_check($data['ord_domain'], 0) && !$NoDomain) {

		# Get a random password
			$_new_password = do_password_create();

		# Calc default account path and misc default vars.
			$_str_search	= 'domainname';
			$_str_replace	= $_GPV['ord_domain'];
			$_sa_path		= eregi_replace($_str_search, $_str_replace, $_CCFG['DOM_DEFAULT_PATH']);

			IF ($_CCFG['DOM_DEFAULT_USERNAME'] == 'username')	{$_sa_uname = $data['ord_user_name'];}
			IF ($_CCFG['DOM_DEFAULT_USERNAME'] == 'domain')	{$_sa_uname = $data['ord_domain'];}
			IF ($_sa_uname == '')						{$_sa_uname = $data['ord_domain'];}

		# Insert Domains
			$query_d  = "INSERT INTO ".$_DBCFG['domains']." (";
			$query_d .= " dom_cl_id, dom_domain, dom_si_id, dom_ip, dom_path";
			$query_d .= ", dom_url_cp, dom_user_name_cp, dom_user_pword_cp, dom_user_name_ftp, dom_user_pword_ftp";
			$query_d .= ", dom_type, dom_allow_domains, dom_allow_subdomains, dom_allow_disk_space_mb";
			$query_d .= ", dom_allow_traffic_mb, dom_allow_mailboxes, dom_allow_databases";
			$query_d .= " ) VALUES ( ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['ord_cl_id'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['ord_domain'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_CCFG['DOM_DEFAULT_SERVER'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_CCFG['DOM_DEFAULT_IP'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_sa_path)."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_CCFG['DOM_DEFAULT_CP_URL'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_sa_uname)."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_new_password)."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_sa_uname)."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_new_password)."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_dom_type'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_domains'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_subdomains'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_disk_space_mb'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_traffic_mb'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_mailboxes'])."', ";
			$query_d .= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_databases'])."'";
			$query_d .= ')';

			$result_d 		= $db_coin->db_query_execute($query_d) OR DIE("Unable to complete request");
			$insert_id_d		= $db_coin->db_query_insertid();
			$_GPV['dom_id']	= $insert_id_d;

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

		}

	# Order Info- build query and do insert into database
		$query = 'INSERT INTO '.$_DBCFG['orders'].' (ord_id';
		$query .= ', ord_ts, ord_status, ord_cl_id, ord_company, ord_name_first, ord_name_last';
		$query .= ', ord_addr_01, ord_addr_02, ord_city, ord_state_prov, ord_country, ord_zip_code';
		$query .= ', ord_phone, ord_email, ord_domain, ord_domain_action, ord_user_name';
		$query .= ', ord_user_pword, ord_vendor_id, ord_prod_id, ord_unit_cost';
		$query .= ', ord_accept_tos, ord_accept_aup, ord_referred_by, ord_comments';
		$query .= ', ord_optfld_01, ord_optfld_02, ord_optfld_03, ord_optfld_04, ord_optfld_05';
		$query .= ', ord_invc_id';
		$query .= ')';

	# Get max / create new ord_id / prep comments
		$_max_ord_id = do_get_max_ord_id();
		IF (!$_GPV['ord_vendor_id']) {$_GPV['ord_vendor_id']=1;}
		$query .= ' VALUES ('.($_max_ord_id+1).', ';
		$query .= "'".$db_coin->db_sanitize_data($_uts)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_cl_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_company'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_name_first'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_name_last'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_addr_01'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_addr_02'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_city'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_state_prov'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_country'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_zip_code'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_phone'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_email'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_domain'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_domain_action'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_user_name'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_user_pword'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_vendor_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_prod_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_unit_cost'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_accept_tos'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_accept_aup'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_referred_by'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_comments'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_optfld_01'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_optfld_02'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_optfld_03'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_optfld_04'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_optfld_05'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['ord_invc_id'])."'";
		$query .= ')';

		$result_ord 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$_ins_ord_id		= $_max_ord_id+1;
		$_GPV['ord_id']	= $_ins_ord_id;

		IF ($_CCFG['ORDER_AUTO_CREATE_INVOICE']) {
			# Auto-create an invoice
				$_GPV['invc_id'] = do_auto_create_invoice($_GPV);
			# Update order info with invoice id
				$oquery		= 'UPDATE '.$_DBCFG[orders].' SET ord_invc_id='.$_GPV['invc_id'].' WHERE ord_id='.$_ins_ord_id;
				$result_o		= $db_coin->db_query_execute($oquery);
				$numrows_o	= $db_coin->db_query_numrows($result_o);
		}

	#########################################################################################################
	# API Output Hook:
	# APIO_order_new: Order Created hook
		$_isfunc = 'APIO_order_new';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_NEW_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret2 .= '<br>'.$_APIO['msg'].'<br>';
			} ELSE {
				$_APIO_ret2 .= '<br>'.'Error- no function'.'<br>';
			}
		}
	#########################################################################################################


	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Adjust Data Array with returned record
		$data['ord_id']		= $_ins_ord_id;
		$data['ord_unit_cost']	= $_GPV['ord_unit_cost'];

	# Call block it function
		$_out .= do_display_entry($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Append API results
		$_out .= $_APIO_ret;
		$_out .= '<br><br>';
		$_out .= $_APIO_ret2;

	# Echo final output
		echo $_out;
}

##############################
# Mode Call: Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for $id no- will determine select string (one for edit, all for list)
		IF (!$_GPV['ord_id'] || $_GPV['ord_id'] == 0) {

		# Set for list.
			$show_list_flag = 1;
		} ELSE {

		# Set Query for select and execute
			$query = 'SELECT * FROM '.$_DBCFG['orders'];
			$query .= " WHERE ord_id='$_GPV[ord_id]'";

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
			$_tstr = $_LANG['_ORDERS']['View_Client_Orders'];

			$_cstr .= '<br>'.$_nl;
			$_cstr .= do_view_orders($data, '1') .$_nl;
			$_cstr .= '<br>'.$_nl;
			$_mstr_flag = '1';
			$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
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
						$data['ord_user_pword']		= ''; # Do not load- encrypted
						$data['ord_user_pword_re']	= ''; # Do not load- encrypted
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
#	- Do table update,
#	- Display results
#	- Comments field not updatable
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_ORD']);

	# Generate encrypted password
		$ord_user_pword_crypt = do_password_crypt($_GPV['ord_user_pword']);

	# Do update
		$query  = 'UPDATE '.$_DBCFG['orders'].' SET ';
		$query .= "ord_ts='".$_GPV['ord_ts']."', ";
		$query .= "ord_status='".$db_coin->db_sanitize_data($_GPV['ord_status'])."', ";
		$query .= "ord_cl_id='".$_GPV['ord_cl_id']."', ";
		$query .= "ord_domain='".$db_coin->db_sanitize_data($_GPV['ord_domain'])."', ";
		$query .= "ord_vendor_id='".$db_coin->db_sanitize_data($_GPV['ord_vendor_id'])."', ";
		$query .= "ord_prod_id='".$db_coin->db_sanitize_data($_GPV['ord_prod_id'])."', ";
		$query .= "ord_unit_cost='".$db_coin->db_sanitize_data($_GPV['ord_unit_cost'])."', ";
		$query .= "ord_accept_tos='".$db_coin->db_sanitize_data($_GPV['ord_accept_tos'])."', ";
		$query .= "ord_accept_aup='".$db_coin->db_sanitize_data($_GPV['ord_accept_aup'])."', ";
		$query .= "ord_comments='".$db_coin->db_sanitize_data($_GPV['ord_comments'])."', ";
		IF ($_BV['B01'] == 1 || $_BR['B01'] == 1) {$query .= "ord_optfld_01='".$db_coin->db_sanitize_data($_GPV['ord_optfld_01'])."', ";}
		IF ($_BV['B02'] == 1 || $_BR['B02'] == 1) {$query .= "ord_optfld_02='".$db_coin->db_sanitize_data($_GPV['ord_optfld_02'])."', ";}
		IF ($_BV['B03'] == 1 || $_BR['B03'] == 1) {$query .= "ord_optfld_03='".$db_coin->db_sanitize_data($_GPV['ord_optfld_03'])."', ";}
		IF ($_BV['B04'] == 1 || $_BR['B04'] == 1) {$query .= "ord_optfld_04='".$db_coin->db_sanitize_data($_GPV['ord_optfld_04'])."', ";}
		IF ($_BV['B05'] == 1 || $_BR['B05'] == 1) {$query .= "ord_optfld_05='".$db_coin->db_sanitize_data($_GPV['ord_optfld_05'])."', ";}
		$query .= "ord_invc_id='".$_GPV['ord_invc_id']."' ";
		$query .= "WHERE ord_id=".$_GPV['ord_id'];

		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call block it function
		$_out .= do_display_entry($data, '1').$_nl;
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
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ORDERS']['Delete_Order_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=orders&mode=delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ORDERS']['Delete_Order_Entry_Message'].'= '.$_GPV['ord_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_GPV['ord_id'].' - '.$_GPV['ord_name_first'].$_sp.$_GPV['ord_name_last'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_id" value="'.$_GPV['ord_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ORDERS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag	= '1';
		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=edit&ord_id='.$_GPV['ord_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query 	= 'DELETE FROM '.$_DBCFG['orders'].' WHERE ord_id='.$_GPV['ord_id'];
		$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	#########################################################################################################
	# API Output Hook:
	# APIO_order_del: Order Deleted hook
		$_isfunc = 'APIO_order_del';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_DEL_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
			} ELSE {
				$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
			}
		}
	#########################################################################################################

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ORDERS']['Delete_Order_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr .= '<center>'.$_LANG['_ORDERS']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_ORDERS']['Entry_Deleted'].'</center>';
		}

	# Append API results
		$_cstr .= $_APIO_ret;

		$_mstr_flag = '1';
		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=edit', $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Mail
# Summary:
#	- eMail Order
##############################
IF (!$_login_flag && $_GPV['mode'] == 'mail') {
	IF ($_GPV['stage'] != 2) {

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ORDERS']['eMail_Order_Confirmation'];

	# Do confirmation form to content string
		$_cstr = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=orders&mode=mail">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ORDERS']['eMail_Order_Message_prefix'].' '.$_GPV['ord_id'].' '.$_LANG['_ORDERS']['eMail_Order_Message_suffix'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_id" value="'.$_GPV['ord_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_ORDERS']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr_flag	= '1';
		IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'',''); }
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view&ord_id='.$_GPV[ord_id], $_TCFG['_IMG_BACK_TO_ORDER_M'],$_TCFG['_IMG_BACK_TO_ORDER_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
	}

	IF ($_GPV['stage'] == 2) {

	# Call function for doing it.
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_mail_order($data, '1').$_nl;

	# Echo final output
		echo $_out;
	}
}

/**************************************************************
 * End Module Code
**************************************************************/

?>