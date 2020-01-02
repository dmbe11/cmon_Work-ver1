<?php
/**
 * Module: Invoices (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Invoices
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_invoices.php
 */


# Code to handle file being loaded by URL
	IF (eregi('invoices_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=invoices');
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
		IF ($_GPV['obj'] == 'invc' && ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV[invc_id] )			{ $err_entry['flag'] = 1; $err_entry['invc_id'] = 1; }
		#	IF (!$_GPV[invc_status] )		{ $err_entry['flag'] = 1; $err_entry['invc_status'] = 1; }
		#	IF (!$_GPV[invc_deliv_method] )	{ $err_entry['flag'] = 1; $err_entry['invc_deliv_method'] = 1; }
		#	IF (!$_GPV[invc_delivered] )		{ $err_entry['flag'] = 1; $err_entry['invc_delivered'] = 1; }
			IF (!$_GPV['invc_cl_id'] )		{ $err_entry['flag'] = 1; $err_entry['invc_cl_id'] = 1; }
		#	IF (!$_GPV[invc_total_cost] )		{ $err_entry['flag'] = 1; $err_entry['invc_total_cost'] = 1; }
		#	IF (!$_GPV[invc_total_paid] )		{ $err_entry['flag'] = 1; $err_entry['invc_total_paid'] = 1; }
		#	IF (!$_GPV[invc_subtotal_cost] )	{ $err_entry['flag'] = 1; $err_entry['invc_subtotal_cost'] = 1; }
		#	IF (!$_GPV[invc_tax_01_percent] )	{ $err_entry['flag'] = 1; $err_entry['invc_tax_01_percent'] = 1; }
		#	IF (!$_GPV[invc_tax_01_amount] )	{ $err_entry['flag'] = 1; $err_entry['invc_tax_01_amount'] = 1; }
		#	IF (!$_GPV[invc_tax_02_percent] )	{ $err_entry['flag'] = 1; $err_entry['invc_tax_02_percent'] = 1; }
		#	IF (!$_GPV[invc_tax_02_amount] )	{ $err_entry['flag'] = 1; $err_entry['invc_tax_02_amount'] = 1; }
			IF (!$_GPV['invc_ts'] )			{ $err_entry['flag'] = 1; $err_entry['invc_ts'] = 1; }
			IF (!$_GPV['invc_ts_due'] )		{ $err_entry['flag'] = 1; $err_entry['invc_ts_due'] = 1; }
		#	IF (!$_GPV[invc_ts_paid] )		{ $err_entry['flag'] = 1; $err_entry['invc_ts_paid'] = 1; }
		#	IF (!$_GPV[invc_bill_cycle] )		{ $err_entry['flag'] = 1; $err_entry['invc_bill_cycle'] = 1; }
		#	IF (!$_GPV[invc_recurring] )		{ $err_entry['flag'] = 1; $err_entry['invc_recurring'] = 1; }
		#	IF (!$_GPV[invc_recurr_proc] )	{ $err_entry['flag'] = 1; $err_entry['invc_recurr_proc'] = 1; }
		#	IF (!$_GPV['invc_last_nag_id'] )	{ $err_entry['flag'] = 1; $err_entry['invc_last_nag_id'] = 1; }
		#	IF (!$_GPV[invc_pay_link] )		{ $err_entry['flag'] = 1; $err_entry['invc_pay_link'] = 1; }
		#	IF (!$_GPV[invc_terms] )			{ $err_entry['flag'] = 1; $err_entry['invc_terms'] = 1; }
		}

		IF ($_GPV['obj'] == 'iitem' && ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV[ii_invc_id] )			{ $err_entry['flag'] = 1; $err_entry['ii_invc_id'] = 1; }
		#	IF (!$_GPV[ii_item_no] )			{ $err_entry['flag'] = 1; $err_entry['ii_item_no'] = 1; }
		#	IF (!$_GPV[ii_item_name] )		{ $err_entry['flag'] = 1; $err_entry['ii_item_name'] = 1; }
		#	IF (!$_GPV[ii_item_desc] )		{ $err_entry['flag'] = 1; $err_entry['ii_item_desc'] = 1; }
		#	IF (!$_GPV[ii_item_cost] )		{ $err_entry['flag'] = 1; $err_entry['ii_item_cost'] = 1; }
		#	IF (!$_GPV[ii_prod_id] )			{ $err_entry['flag'] = 1; $err_entry['ii_prod_id'] = 1; }
		#	IF (!$_GPV[ii_apply_tax_01] )		{ $err_entry['flag'] = 1; $err_entry['ii_apply_tax_01'] = 1; }
		#	IF (!$_GPV[ii_apply_tax_02] )		{ $err_entry['flag'] = 1; $err_entry['ii_apply_tax_02'] = 1; }
		}

		IF ($_GPV['obj'] == 'trans' && ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV[it_id] )				{ $err_entry['flag'] = 1; $err_entry['it_id'] = 1; }
		#	IF (!$_GPV[it_ts] )				{ $err_entry['flag'] = 1; $err_entry['it_ts'] = 1; }
		#	IF (!$_GPV[it_invc_id] )			{ $err_entry['flag'] = 1; $err_entry['it_invc_id'] = 1; }
		#	IF (!$_GPV[it_type] )			{ $err_entry['flag'] = 1; $err_entry['it_type'] = 1; }
		#	IF (!$_GPV[it_origin] )			{ $err_entry['flag'] = 1; $err_entry['it_origin'] = 1; }
		#	IF (!$_GPV[it_desc] )			{ $err_entry['flag'] = 1; $err_entry['it_desc'] = 1; }
		}

		return $err_entry;
}


# Do display entry (individual invoice entry)
function do_display_entry($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_tstr = '';

		IF ($_CCFG['_IS_PRINT']) {
			$imageURL = check_if_image_exists('invoice_logo', $_CCFG['_PKG_PATH_THEME'].'images/', $_CCFG['_PKG_URL_THEME_IMGS']);
			IF ($imageURL) {$_tstr = '<img src="' . $imageURL . '" border="0"><br>';}
		}

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query	.= ' WHERE '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_id='.$adata['invc_id'];

	# Set to logged in Client ID if not admin to avoid seeing other client invoice id's
		IF (!$_SEC['_sadmin_flg']) {
			$query	.= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_SEC['_suser_id'];
			$query	.= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][1])."'";

		# Check show pending enable flag
			IF (!$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
				$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
			}
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build common td start tag / col strings (reduce text)
		$_td_str_left		= '<td class="TP3SML_NR" width="40%">';
		$_td_str_right		= '<td class="TP3SML_NL" width="60%">';
		$_td_str_center	= '<td class="TP3SML_NC">';
		$_td_str_just		= '<td class="TP3SML_NJ">';
		$_td_str_span_2	= '<td class="TP3SML_NC" colspan="2" valign="top">';

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Build Title String, Content String, and Footer Menu String
				IF (!$_CCFG['_IS_PRINT']) {
					$_tstr .= $_LANG['_INVCS']['View_Client_Invoice_ID'].$_sp.$row['invc_id'];
					IF ($_SEC['_sadmin_flg']) {
						$_tstr .= ' <a href="mod.php?mod=clients&mode=view&cl_id='.$row['invc_cl_id'].'">'.$_TCFG['_IMG_BACK_TO_CLIENT_M'].'</a>'.$_nl;
					}
				}

			# Open
				$_cstr  = '<br>'.$_nl.'<center>'.$_nl;
				$_cstr .= '<table cellpadding="0" width="95%">'.$_nl;

			# Add Form Title
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP1SML_NC" colspan="2" valign="top">'.$_nl;
				IF ($row['invc_status'] == 'credit memo') {
					$_cstr .= '<h1>'.$_LANG['_INVCS']['CM_Title'].'</h1>'.$_nl;
				} ELSE {
					$_cstr .= '<h1>'.$_LANG['_INVCS']['Form_Title'].'</h1>'.$_nl;
				}
				$_cstr .= '</td></tr>'.$_nl;

			# Primary Seller / Buyer Info Row
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP1SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= '<table width="100%"><tr><td class="TP0SML_NC" valign="top" width="50%">'.$_nl;

			# Order Info Cell
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
				$_cstr .= $_LANG['_INVCS']['Bill_To'].$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_center.$_nl;
				$_cstr .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Client_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_name_first'].$_sp.$row['cl_name_last'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($row['cl_company']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Company'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['cl_company'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Address'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_addr_01'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($row['cl_addr_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['cl_addr_02'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_City'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_city'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_State_Prov'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_state_prov'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_zip_code'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Country'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_country'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['invc_cl_id'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_User_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_user_name'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Email'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['cl_email'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Delivery_Method'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['invc_deliv_method'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Delivered'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.do_valtostr_no_yes($row['invc_delivered']).'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Recurring'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_valtostr_no_yes($row['invc_recurring']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($_SEC['_sadmin_flg']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b><nobr>'.$_LANG['_INVCS']['l_Recurring_Processed'].$_sp.'</nobr></b></td>'.$_nl;
					$_cstr .= $_td_str_right.do_valtostr_no_yes($row['invc_recurr_proc']).'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Insert blank lines to make up for missing rows
				IF (!$row['cl_company']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$row['cl_addr_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF ($_CCFG['_IS_PRINT'] == 1) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '<td class="TP0SML_NC" valign="top" width="50%">'.$_nl;


			# Over-ride the current seller address info, if necessary
				IF (file_exists(PKG_PATH_OVERRIDES.'invoice_address_override.php')) {
					require(PKG_PATH_OVERRIDES.'invoice_address_override.php');
				}

			# Company Info Cell
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
				$_cstr .= $_LANG['_INVCS']['Remit_To'].$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_center.$_nl;

				$_cstr .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Company'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_01_NAME'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($_UVAR['CO_INFO_12_TAGLINE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.''.$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_12_TAGLINE'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Address'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_02_ADDR_01'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($_UVAR['CO_INFO_03_ADDR_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.''.$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_03_ADDR_02'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_City'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_04_CITY'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_State_Prov'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_05_STATE_PROV'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_06_POSTAL_CODE'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Country'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_07_COUNTRY'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				IF ($_UVAR['CO_INFO_08_PHONE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Phone'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_08_PHONE'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				IF ($_UVAR['CO_INFO_09_FAX']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Fax'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_09_FAX'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				IF ($_UVAR['CO_INFO_11_TOLL_FREE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_BASE']['LABEL_TOLL_FREE'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_11_TOLL_FREE'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				IF ($_UVAR['CO_INFO_10_TAXNO']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Tax_Number'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_10_TAXNO'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Insert blank lines to make up for missing rows
				IF (!$_UVAR['CO_INFO_12_TAGLINE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$_UVAR['CO_INFO_03_ADDR_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$_UVAR['CO_INFO_08_PHONE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$_UVAR['CO_INFO_09_FAX']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$_UVAR['CO_INFO_11_TOLL_FREE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF (!$_UVAR['CO_INFO_10_TAXNO']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				IF ($_SEC['_sadmin_flg']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Invoice Info Row
				$_td_TP3SML_BC		= '<td class="TP3SML_BC">';
				$_td_TP3SML_NC		= '<td class="TP3SML_NC">';

				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Invoice_ID'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Invoice_Date'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Status'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Date_Due'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Total_Cost'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Date_Paid'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Total_Paid'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Billing_Cycle'].$_nl.'</td>'.$_nl;
				IF ($_CCFG['INVC_SHOW_LAST_NAG']) {
					$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_INVCS']['l_Last_Nag'].$_nl.'</td>'.$_nl;
				}
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$row['invc_id'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['invc_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$row['invc_status'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['invc_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.do_currency_format($row['invc_total_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['invc_ts_paid'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.do_currency_format($row['invc_total_paid'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$_CCFG['INVC_BILL_CYCLE'][$row['invc_bill_cycle']].$_nl.'</td>'.$_nl;
				IF ($_CCFG['INVC_SHOW_LAST_NAG']) {
					$_cstr .= $_td_TP3SML_NC.$_nl.nag_val_to_str($row['invc_last_nag_id']).$_nl.'</td>'.$_nl;
				}
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Display Invoices Items Row
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= do_view_invoices_items($adata, '1');
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Terms Link Row
				IF ($_CCFG['INVC_TERMS_ENABLE'] && $row['invc_terms'] != '') {
					$_cstr .= '<tr>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
					$_cstr .= $_LANG['_INVCS']['Terms'].$_nl;
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_just.$_nl;
					$_cstr .= nl2br($row['invc_terms']).$_nl;
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '</table>'.$_nl;
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Payment Link Row
				IF ($row['invc_pay_link'] != '' && $_CCFG['_IS_PRINT'] != 1) {
					$_cstr .= '<tr>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
					$_cstr .= $_LANG['_INVCS']['Payment_Link'].$_nl;
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '<tr class="BLK_DEF_ENTRY"><td class="TP3SML_NL">'.$_nl;
					IF ($row['invc_total_cost'] - $row['invc_total_paid'] > 0) {
						$_cstr .= str_replace('<invoice_id>', $row['invc_id'], $row['invc_pay_link']).$_nl;
					} ELSE {
						$_cstr .= $_LANG['_INVCS']['Payment_Link_Paid'].$_nl;
					}
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '</table>'.$_nl;
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Transactions Link Row
				IF (
					($_CCFG['INVC_VIEW_SHOW_TRANS'] == 1 && !$_CCFG['_IS_PRINT']) ||
					($_CCFG['INVC_VIEW_SHOW_TRANS'] == 2 && $_CCFG['_IS_PRINT']) ||
					($_CCFG['INVC_VIEW_SHOW_TRANS'] == 3)
				) {
					$_cstr .= '<tr>'.$_nl.'<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= '<hr>'.$_nl;
					$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
					$_cstr .= '<tr>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= do_view_transactions($row, '1').$_nl;
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Closeout
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</center>'.$_nl;

			# Add print footer line
				IF ($_CCFG['_IS_PRINT'] && $_LANG['_INVCS']['INV_PRINT_FOOTER']) {
					$_cstr .= $_LANG['_INVCS']['INV_PRINT_FOOTER'];
				}

			}

		} ELSE {
		# Build Title String, Content String, and Footer Menu String
			$_tstr .= $_LANG['_INVCS']['View_Client_Invoice_ID'];

			$_cstr  = '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3MED_NC"><b>'.$_LANG['_INVCS']['Error_Invoice_Not_Found'].'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
		}

		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_sadmin_flg']) {

			# Build function argument text
				$_mstr_flag = '1';
				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link('mod_print.php?mod=invoices&mode=view&invc_id='.$adata['invc_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
				IF (strtolower($row['cl_email']) != 'none') {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=mail&invc_id='.$adata['invc_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
				}
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=copy&invc_id='.$adata['invc_id'], $_TCFG['_IMG_COPY_M'],$_TCFG['_IMG_COPY_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=add&obj=trans&it_invc_id='.$adata['invc_id'], $_TCFG['_IMG_PAYMENT_M'],$_TCFG['_IMG_PAYMENT_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&invc_id='.$adata['invc_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				}
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			} ELSE {
			# Build function argument text
				$_mstr_flag = '1';
				$_mstr  = do_nav_link('mod_print.php?mod=invoices&mode=view&invc_id='.$adata['invc_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
				IF (strtolower($row['cl_email']) != 'none') {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=mail&invc_id='.$adata['invc_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
				}
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			}

		} ELSE {
			$_mstr_flag = '0';
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Client Invoices
function do_view_invoices($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_GPV, $_CCFG, $_TCFG, $_DBCFG, $db_coin, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_out	= '';
		$_ps		= '';
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
		IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}

	# Set Query for select.
		$query	.= 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$_where	.= ' WHERE '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';

	# Show only selected status invoices
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_where .= ' AND '.$_DBCFG['invoices'].".invc_status='".$db_coin->db_sanitize_data($_GPV['status'])."'";}
		IF ($_GPV['notstatus']) {$_where .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_GPV['notstatus'])."'";}

	# Set to logged in Client ID if not admin to avoid seeing other client invoice id's
		IF (!$_SEC['_sadmin_flg']) {
			$_where .= " AND ".$_DBCFG['invoices'].".invc_cl_id = ".$_SEC['_suser_id'];
			$_where .= " AND ".$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][1])."'";

		# Check show pending enable flag
			IF (!$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
				$_where .= " AND ".$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
			}
			$_invc_cl_id = $_SEC['_suser_id'];
		} ELSE {
			IF ( $adata['invc_cl_id'] > 0 ) {
				$_where .= " AND ".$_DBCFG['invoices'].".invc_cl_id = ".$adata['invc_cl_id'];
			}
			$_invc_cl_id = $adata['invc_cl_id'];
		}

	# Set Filters
		IF (!$adata['fb'])		{$adata['fb'] = '';}
		IF ($adata['fb'] == '1')	{$_where .= ' AND '.$_DBCFG['invoices'].".invc_status='".$db_coin->db_sanitize_data($adata['fs'])."'";}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'D';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])			{$adata['sb'] = '4';	}
		IF ($adata['sb'] == '1')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_id '.$order_AD;}
		IF ($adata['sb'] == '2')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_status '.$order_AD;}
		IF ($adata['sb'] == '3')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_cycle '.$order_AD;}
		IF ($adata['sb'] == '4')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_ts '.$order_AD;}
		IF ($adata['sb'] == '5')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_ts_due '.$order_AD;}
		IF ($adata['sb'] == '6')		{$_order = ' ORDER BY '.$_DBCFG['invoices'].'.invc_total_cost '.$order_AD;}
		IF ($adata['sb'] == '7')		{$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_name_last '.$order_AD.', '.$_DBCFG['clients'].'.cl_name_first '.$order_AD;}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb= '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso= '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb= '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs= '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query_ttl .= $_where;

		$result_ttl = $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
		# $_rec_next	- is page loading first record number
		# $_rec_start	- is a given page start record (which will be rec_next)
		$_rec_page	= $_CCFG['IPP_INVOICES'];
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
		$_page_menu = $_LANG['_INVCS']['l_Pages'].$_sp;
		for ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = (($i*$_rec_page)-$_rec_page);
			IF ($_rec_start == $_rec_next) {

			# Loading Page start record so no link for this page.
				$_page_menu .= $i;
			} ELSE {
				$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=invoices&mode=view'.$_link_xtra.$_ps.'&rec_next='.$_rec_start;
				IF (!$_SEC['_sadmin_flg']) {
					$_page_menu .= "&invc_cl_id=".$_SEC['_suser_id'];
				} ELSE {
					IF ($adata['invc_cl_id'] > 0) {
						$_page_menu .= "&invc_cl_id=".$adata['invc_cl_id'];
					}
				}
				$_page_menu .= '">'.$i.'</a>';
			}

			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="'.$_SERVER["PHP_SELF"].'?mod=invoices&sb=';
		$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&fc='.$adata['fc'].'&rec_next='.$_rec_next.$_ps.'">';

		$_hdr_link_1 .= $_LANG['_INVCS']['l_ID'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_2 .= $_LANG['_INVCS']['l_Status'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_3 .= $_LANG['_INVCS']['l_Billing_Cycle'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_4 .= $_LANG['_INVCS']['l_Date'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_5 .= $_LANG['_INVCS']['l_Date_Due'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_6 .= $_LANG['_INVCS']['l_Amount'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_7 .= $_LANG['_INVCS']['l_Full_Name'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_8 = $_LANG['_INVCS']['l_Balance'].$_sp;


	# Build form output

	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
			$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=invoices&status=all'.$_link_xtra;
			IF (!$_SEC['_sadmin_flg']) {
				$_out .= '&invc_cl_id='.$_SEC['_suser_id'];
			} ELSE {
				IF ($adata['invc_cl_id'] > 0) {$_out .= '&invc_cl_id='.$adata['invc_cl_id'];}
			}
			$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</nobr></td>';
			for ($i=0; $i< sizeof($_CCFG['INV_STATUS']); $i++) {
				$_out .= '<td align="right"><nobr>&nbsp;[<a href="mod.php?mod=invoices&status=' . $_CCFG['INV_STATUS'][$i].$_link_xtra;
				IF (!$_SEC['_sadmin_flg']) {
					$_out .= "&invc_cl_id=".$_SEC['_suser_id'];
				} ELSE {
					IF ($adata['invc_cl_id'] > 0) {
						$_out .= "&invc_cl_id=".$adata['invc_cl_id'];
					}
				}
				$_out .= '">'.$_CCFG['INV_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
			$_out .= '<td>&nbsp;</td>';
			for ($i=0; $i< sizeof($_CCFG['INV_STATUS']); $i++) {
				$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=invoices&notstatus='.$_CCFG['INV_STATUS'][$i].$_link_xtra;
				IF (!$_SEC['_sadmin_flg']) {
					$_out .= '&invc_cl_id='.$_SEC['_suser_id'];
				} ELSE {
					IF ($adata['invc_cl_id'] > 0) {$_out .= '&invc_cl_id='.$adata['invc_cl_id'];}
				}
				$_out .= '">'.$_CCFG['INV_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr></table>';
			$_out .= '<br><br>';
		}

	# Build the table
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.(9-$_CCFG['_IS_PRINT']).'">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_INVCS']['Clients_Invoices'].$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_INVCS']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_INVCS']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_sadmin_flg']) {
				$_out .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=cc&mode=search&sw=invoices', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
			}
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_1.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_2.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_3.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_4.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_5.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_6.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_8.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_7.'</td>'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		$p_idata = array('total_cost' => 0, 'net_balance' => 0);
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Color code recurring invoices
				IF ($row['invc_recurring'] && !$row['invc_recurr_proc']) {
					$_out .= '<tr class="GRN_DEF_ENTRY">'.$_nl;
				} ELSE {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				}
				$_out .= '<td class="TP3SML_NC">'.$row['invc_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['invc_status'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$_CCFG['INVC_BILL_CYCLE'][$row['invc_bill_cycle']].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['invc_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['invc_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['invc_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;

			# Calculate total billed, total and current balance due, and display current balance
				$idata = do_get_invc_cl_balance($_invc_cl_id, $row['invc_id']);
				$p_idata['net_balance'] += $idata['net_balance'];
				IF ($row['invc_status'] != $_CCFG['INV_STATUS'][1] && $row['invc_status'] != $_CCFG['INV_STATUS'][4] && $row['invc_status'] != $_CCFG['INV_STATUS'][5] && $row['invc_status'] != $_CCFG['INV_STATUS'][6]) {
					$p_idata['total_cost'] += $row['invc_total_cost'];		// Exclude draft, void, etc. for total billed
				}
				$_out .= '<td class="TP3SML_NR">';
				IF ($idata['net_balance']) {
					$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				} ELSE {$_out .= $_sp;}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NL">'.$row['cl_name_last'].','.$_sp.$row['cl_name_first'].'</td>'.$_nl;

				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
					$_out .= do_nav_link('mod.php?mod=invoices&mode=view&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_out .= do_nav_link('mod_print.php?mod=invoices&mode=view&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					IF (strtolower($row['cl_email']) != 'none') {
						$_out .= do_nav_link('mod.php?mod=invoices&mode=mail&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					} ELSE {
						$_out .= $_TCFG['_IMG_BLANK_S'];
					}
					IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');

// Uncomment the next line to get another icon which will take you
// directly to the items editor instead of the invoice editor.
//						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=iitem&ii_invc_id='.$row['invc_id'].'&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_HELP_S'],$_TCFG['_S_IMG_HELP_S_MO'],'','');
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=delete&stage=1&invc_id='.$row['invc_id'].'&invc_ts='.$row['invc_ts'].'&invc_status='.$row['invc_status'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
					$_out .= '</nobr></td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

	# Show display only totals footer row
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_INVCS']['l_Billed_Amount'].$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($p_idata['total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($p_idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BL" colspan="'.(2-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Show totals footer rows
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_BASE']['All'].':'.$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$idata = do_get_invc_cl_balance($_invc_cl_id,0);
		$_out .= do_currency_format($idata['total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BL" colspan="'.(2-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Closeout
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.(9-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Client Transactions
function do_view_transactions($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do we show the admin column
		$_showcol = 0;
		IF (!$_CCFG['_IS_PRINT'] && ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1))) {$_showcol = 1;}

	# Set Query for select.
		$query	.= 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices_trans'].', '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$_where	.= ' WHERE '.$_DBCFG['invoices_trans'].'.it_invc_id='.$_DBCFG['invoices'].'.invc_id';
		$_where	.= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';

	# Check for invc_id for limited
		IF ($adata['invc_id'] > 0) {
			$_where .= ' AND '.$_DBCFG['invoices'].'.invc_id='.$adata['invc_id'];
		}

	# Set to logged in Client ID if not admin to avoid seeing other client invoice id's
		IF (!$_SEC['_sadmin_flg']) {
			$_where .= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_SEC['_suser_id'];
			$_where .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][1])."'";

		# Check show pending enable flag
			IF (!$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
				$_where .= " AND ".$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
			}
		} ELSE {
			IF ($adata['invc_cl_id'] > 0) {
				$_where .= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$adata['invc_cl_id'];
			}
		}

	# Set Sort orders
		$_order = ' ORDER BY '.$_DBCFG['invoices_trans'].'.it_invc_id ASC, '.$_DBCFG['invoices_trans'].'.it_id ASC';

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate Header Row strings
		$_hdr_link_1 .= $_LANG['_INVCS']['l_ID'].$_sp.$_sp;
		$_hdr_link_2 .= $_LANG['_INVCS']['l_Trans_Date'].$_sp.$_sp;
		$_hdr_link_3 .= $_LANG['_INVCS']['l_Trans_Type'].$_sp.$_sp;
		$_hdr_link_4 .= $_LANG['_INVCS']['l_Trans_Origin'].$_sp.$_sp;
		$_hdr_link_5 .= $_LANG['_INVCS']['l_Trans_Description'].$_sp.$_sp;
		$_hdr_link_6 .= $_LANG['_INVCS']['l_Trans_Amount'].$_sp.$_sp;

	# Table open
		$_topen .= '<div align="center">'.$_nl;
		$_topen .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_topen .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_topen .= '<td class="TP3MED_NC" colspan="'.(8 - $_showcol).'">'.$_nl;

		$_topen .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_topen .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl;
		$_topen .= '<td class="TP0MED_NL"><b>'.$_LANG['_INVCS']['Clients_Invoice_Transactions'].$_sp.'</b></td>'.$_nl;
		IF ($_showcol) {
			$_topen .= '<td class="TP0MED_NR">'.$_nl;
			$_topen .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search&sw=trans', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
			$_topen .= '</td>'.$_nl;
		}
		$_topen .= '</tr></table>'.$_nl;
		$_topen .= '</td></tr>'.$_nl;

	# Title Row
		$_trow .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_trow .= '<td class="TP3SML_NC"><b>'.$_hdr_link_1.'</b></td>'.$_nl;
		$_trow .= '<td class="TP3SML_NC"><b>'.$_hdr_link_2.'</b></td>'.$_nl;
		$_trow .= '<td class="TP3SML_NC"><b>'.$_hdr_link_3.'</b></td>'.$_nl;
		$_trow .= '<td class="TP3SML_NC"><b>'.$_hdr_link_4.'</b></td>'.$_nl;
		$_trow .= '<td class="TP3SML_NL"><b>'.$_hdr_link_5.'</b></td>'.$_nl;
		$_trow .= '<td class="TP3SML_NR"><b>'.$_hdr_link_6.'</b></td>'.$_nl;
		IF ($_showcol) {
			$_trow .= '<td class="TP3SML_NC"><b>'.$_LANG['_INVCS']['Actions'].'</b></td>'.$_nl;
		}
		$_trow .= '</tr>'.$_nl;

	# Check param for which type listing
		IF ($_CCFG['INVC_SPLIT_TRANS_LIST_ENABLE'] == 1) {
		# Process query results
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Build Charge Rows
					IF ($row['it_type'] == 0) {
						$_chrg_ttl = $_chrg_ttl + $row['it_amount'];
						$_chrg .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$row['it_invc_id'].'-'.str_pad($row['it_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_TYPE'][$row['it_type']].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NL">'.$row['it_desc'].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NR">'.do_currency_format(($row['it_amount'] * 1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF ($_showcol) {
							$_chrg .= '<td class="TP3SML_NL"><nobr>'.$_nl;
							IF (!$adata['invc_id']) {
								$_chrg .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$row['it_invc_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
								$_chrg .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=trans&it_id='.$row['it_id'].'&it_type='.$row['it_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_chrg .= '</nobr></td>'.$_nl;
						}
						$_chrg .= '</tr>'.$_nl;
					}

				# Build Credit Rows
					IF ($row['it_type'] != 0) {
						$_cred_ttl = $_cred_ttl + ($row['it_amount'] * -1);
						$_cred .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$row['it_invc_id'].'-'.str_pad($row['it_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_TYPE'][$row['it_type']].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NL">'.$row['it_desc'].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NR">'.do_currency_format(($row['it_amount'] * -1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF ($_showcol) {
							$_cred .= '<td class="TP3SML_NC">'.$_nl;
							$_cred .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=trans&it_id='.$row['it_id'].'&it_type='.$row['it_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							$_cred .= '</td>'.$_nl;
						}
						$_cred .= '</tr>'.$_nl;
					}
				}
			}

		# Build output
			$_out .= $_topen.$_nl;

		# Charges
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BL" colspan="'.(8 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_INVCS']['l_Charges_To_Account'].$_nl;
			$_out .= '</td>'.$_nl.'</tr>'.$_nl;

			$_out .= $_trow.$_nl;
			$_out .= $_chrg.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BR" colspan="'.(6 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_INVCS']['l_Total_Charges'].$_nl;
			$_out .= '</td><td class="TP3SML_BR">'.$_nl;
			$_out .= do_currency_format($_chrg_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
			$_out .= '</td><td class="TP3SML_BL" colspan="1">'.$_nl;
			$_out .= $_sp.$_nl;
			$_out .= '</td></tr>'.$_nl;

		# Credits
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BL" colspan="'.(8 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_INVCS']['l_Credits_To_Account'].$_nl;
			$_out .= '</td>'.$_nl.'</tr>'.$_nl;

			$_out .= $_trow.$_nl;
			$_out .= $_cred.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BR" colspan="'.(6 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_INVCS']['l_Total_Credits'].$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3SML_BR">'.$_nl;
			$_out .= do_currency_format($_cred_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
			$_out .= '</td>'.$_nl;
			IF ($_showcol) {
				$_out .= '<td class="TP3SML_BL">'.$_sp.'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		} ELSE {
		# Build output
			$_out .= $_topen.$_nl;
			$_out .= $_trow.$_nl;

		# Process query results
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Build Charge Rows
					IF ($row['it_type'] == 0) {
						$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$row['it_invc_id'].'-'.str_pad($row['it_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_TYPE'][$row['it_type']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NL">'.$row['it_desc'].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NR">'.do_currency_format(($row['it_amount'] * 1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF (!$_CCFG['_IS_PRINT']) {
							$_out .= '<td class="TP3SML_NL">'.$_nl;
							IF (!$adata['invc_id']) {
								$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$row['invc_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
								$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=trans&it_id='.$row['it_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_out .= '</td>'.$_nl;
						}
						$_out .= '</tr>'.$_nl;
					}

				# Build Credit Row
					IF ($row['it_type'] != 0) {
						$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$row['it_invc_id'].'-'.str_pad($row['it_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_TYPE'][$row['it_type']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NL">'.$row['it_desc'].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NR">'.do_currency_format(($row['it_amount'] * -1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF (!$_CCFG['_IS_PRINT']) {
							$_out .= '<td class="TP3SML_NL">'.$_nl;
							IF (!$adata['invc_id']) {
								$_out .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$row['it_invc_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
								$_out .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=trans&it_id='.$row['it_id'].'&it_type='.$row['it_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_out .= '</td>'.$_nl;
						}
						$_out .= '</tr>'.$_nl;
					}
				}
			}
		}

	# Finish out with balance row, and table
		IF (!$_SEC['_sadmin_flg']) {$_invc_cl_id = $_SEC['_suser_id'];} ELSE {$_invc_cl_id = $adata['invc_cl_id'];}
		$idata = do_get_invc_cl_balance($_invc_cl_id, $adata['invc_id']);

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_INVCS']['l_Balance'].$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td>';
		IF ($_showcol) {
			$_out .= '<td class="TP3SML_BL">'.$_sp.'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Client Invoices Items
function do_view_invoices_items($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do we show the admin column
		$_showcol = 0;
		IF (!$_CCFG['_IS_PRINT'] && ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1))) {$_showcol = 1;}

	# Set Query for select.
		$query	 = 'SELECT '.$_DBCFG['invoices_items'].'.ii_invc_id';
		$query	.= ', '.$_DBCFG['invoices_items'].'.ii_item_no';
		$query	.= ', '.$_DBCFG['invoices_items'].'.ii_item_name';
		$query	.= ', '.$_DBCFG['invoices_items'].'.ii_item_desc';
		$query	.= ', '.$_DBCFG['invoices_items'].'.ii_item_cost';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['invoices_items'];
		$query	.= ' WHERE '.$_DBCFG['invoices'].'.invc_id='.$_DBCFG['invoices_items'].'.ii_invc_id';
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_id='.$adata['invc_id'];
		$query	.= ' ORDER BY '.$_DBCFG['invoices_items'].'.ii_item_no ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_out .= '<td class="TP3MED_NC" colspan="'.(4 + $_showcol).'">'.$_nl;
		$_out .= '<b>'.$_LANG['_INVCS']['Invoice_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NC"><b>'.$_LANG['_INVCS']['l_Item_No'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_INVCS']['l_Name'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_INVCS']['l_Description'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NR"><b>'.$_LANG['_INVCS']['l_Cost'].'</b>'.$_sp.$_sp.'</td>'.$_nl;
		IF ($_showcol) {
			$_out .= '<td class="TP3SML_NC"><b>'.$_LANG['_INVCS']['Actions'].'</b></td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		while(list($ii_invc_id, $ii_item_no, $ii_item_name, $ii_item_desc, $ii_item_cost) = $db_coin->db_fetch_row($result)) {
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			IF ($ii_item_name == 'NOTE') {
				$_out .= '<td class="TP3MED_NC" colspan="4">'.$_nl;
				$_out .= $ii_item_desc.$_nl;
				$_out .= '</td>'.$_nl;
			} ELSE {
				$_out .= '<td class="TP3SML_NC">'.$ii_item_no.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$ii_item_name.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$ii_item_desc.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($ii_item_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			IF ($_showcol) {
				$_out .= '<td class="TP3SML_NC">'.$_nl;
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&obj=iitem&ii_invc_id='.$ii_invc_id.'&ii_item_no='.$ii_item_no, $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=delete&obj=iitem&ii_invc_id='.$ii_invc_id.'&ii_item_no='.$ii_item_no, $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;
		}

		$idata = do_get_invc_values($adata['invc_id']);

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NR" colspan="3">'.$_nl;
		IF ($_CCFG['INVC_TAX_01_ENABLE'] || $_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<b>'.$_LANG['_INVCS']['l_SubTotal_Cost'].'</b>'.$_sp.$_nl;
			$_out .= '<br>'.$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_01_ENABLE']) {
			$_out .= '<br>'.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'('.do_currency_format($idata['invc_tax_01_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']).'%)'.$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<br>'.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'('.do_currency_format($idata['invc_tax_02_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']).'%)'.$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_01_ENABLE'] || $_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<br>'.$_sp.'<br>'.$_nl;
		}

		$_out .= '<b>'.$_LANG['_INVCS']['l_Total_Cost'].'</b>'.$_sp.$_nl;
		$_out .= '<br>'.$_sp.$_nl;
		$_out .= '</td>'.$_nl;

		$_out .= '<td class="TP3SML_NR">'.$_nl;
		IF ($_CCFG['INVC_TAX_01_ENABLE'] || $_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<b>'.do_currency_format($idata['invc_subtotal_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b>'.$_sp.$_nl;
			$_out .= '<br>'.'-------------------'.$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_01_ENABLE']) {
			$_out .= '<br>'.do_currency_format($idata['invc_tax_01_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<br>'.do_currency_format($idata['invc_tax_02_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		}
		IF ($_CCFG['INVC_TAX_01_ENABLE'] || $_CCFG['INVC_TAX_02_ENABLE']) {
			$_out .= '<br>'.'-------------------'.$_sp.'<br>'.$_nl;
		}

		$_out .= '<b>'.do_currency_format($idata['invc_total_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b>'.$_sp.$_nl;
		$_out .= '<br>'.$_sp.$_nl;
		$_out .= '</td>'.$_nl;

		IF ($_showcol) {
			$_out .= '<td class="TP3SML_BC"><b>'.$_sp.'</b></td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

		IF ($_SEC['_sadmin_flg'] && $_CCFG['_IS_PRINT'] != 1) {
			IF ($adata['obj'] == 'iitem') {
				$_out .= '<tr class="BLK_DEF_FMENU"><td class="TP3SML_BC" colspan="'.(4 + $_showcol).'">'.$_nl;
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$adata['invc_id'], $_TCFG['_IMG_BACK_TO_INVC_M'],$_TCFG['_IMG_BACK_TO_INVC_M_MO'],'','');
				$_out .= '</td></tr>'.$_nl;
			} ELSE {
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
					$_out .= '<tr class="BLK_DEF_FMENU"><td class="TP3SML_BC" colspan="'.(4 + $_showcol).'">'.$_nl;
					$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&obj=iitem&invc_id='.$adata['invc_id'], $_TCFG['_IMG_IITEMS_EDITOR_M'],$_TCFG['_IMG_IITEMS_EDITOR_M_MO'],'','');
					$_out .= '</td></tr>'.$_nl;
				}
			}
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do email Client Invoice
function do_mail_invoice($adata, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_MTP = array();

	# Call common.php function for invoice mtp data (see function for array values) / merge with current.
		$_in_info = get_mtp_invoice_info($adata['invc_id']);
		IF ($_in_info['numrows'] > 0) {
			$_in_info['invc_pay_link'] = str_replace('<invoice_id>', $row_inv['invc_id'], $_in_info['invc_pay_link']);
			$data_new	= array_merge($_MTP, $_in_info);
			$_MTP	= $data_new;
		} ELSE {
			$_mail_error_flg = 1;
			$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_01_PRE'].$_sp.$adata['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_01_SUF'];
		}

	# Call common.php function for invoice items mtp data (see function for array values) / merge with current.
		$_ii_info = get_mtp_invcitem_info($adata['invc_id']);

		IF ( $_ii_info['numrows'] > 0 ) {
			$data_new	= array_merge($_MTP, $_ii_info);
			$_MTP	= $data_new;
		} ELSE {
			$_mail_error_flg = 1;
			$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_02_PRE'].$_sp.$adata['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_02_SUF'];
		}

	# Call common.php function for client mtp data (see function for array values) / merge with current.
		$_cl_info 	= get_mtp_client_info($_in_info['invc_cl_id']);

		IF ($_cl_info['numrows'] > 0) {
			$data_new	= array_merge($_MTP, $_cl_info);
			$_MTP	= $data_new;
		}

	# Get client invoice balance and set $_MTP var
		$idata = do_get_invc_cl_balance($_MTP['cl_id']);
		IF ($idata['net_balance'] < 0) {
			$_MTP['cl_balance'] = '-'.do_currency_format(($idata['net_balance'] * -1),1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		} ELSE {
			$_MTP['cl_balance'] = ' '.do_currency_format($idata['net_balance'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		}

	# Get mail contact information array
		$_cinfo	= get_contact_info($_CCFG['MC_ID_BILLING']);

	# Set eMail Parameters (pre-eval template, some used in template)
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   			$mail['recip']		= $_MTP['cl_email'];
			$mail['from']		= $_cinfo['c_email'];
		} ELSE {
			$mail['recip']		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'].' <'.$_MTP['cl_email'].'>';
			$mail['from']		= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
		}
		IF ( $_CCFG['INVC_EMAIL_CC_ENABLE'] ) {
		    IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   				$mail['cc']	= $_cinfo['c_email'];
			} ELSE {
				$mail['cc']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
			}
		} ELSE {
			$mail['cc']		= '';
		}

	# Build custom email subject line
		IF ( $adata['template'] == 'email_trans_ack' ) {
			IF ($_LANG['_INVCS']['PYT_EMAIL_SUBJECT']) {
				$mail['subject'] = $_LANG['_INVCS']['PYT_EMAIL_SUBJECT'];
			} ELSE {
				$mail['subject'] = $_CCFG['_PKG_NAME_SHORT'].$_LANG['_INVCS']['PYT_EMAIL_SUBJECT_PRE'].$_LANG['_INVCS']['PYT_EMAIL_SUBJECT_SUF'];
			}
		} ELSE {
			IF ($_LANG['_INVCS']['INV_EMAIL_SUBJECT']) {
				$mail['subject'] = $_LANG['_INVCS']['INV_EMAIL_SUBJECT'];
			} ELSE {
				$mail['subject'] = $_CCFG['_PKG_NAME_SHORT'].$_LANG['_INVCS']['INV_EMAIL_SUBJECT_PRE'].$_LANG['_INVCS']['INV_EMAIL_SUBJECT_SUF'];
			}
		}

	# Replace parameters (if any)
		$balance			= $_in_info['invc_total_cost'] - $_in_info['invc_total_paid'];
		$clientname		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
		$mail['subject']	= str_replace("%SITENAME%", $_CCFG['_PKG_NAME_SHORT'], $mail['subject']);
		$mail['subject']	= str_replace("%INV_NO%", $_in_info['invc_id'], $mail['subject']);
		$mail['subject']	= str_replace("%INV_AMT_TTL%", do_currency_format($_in_info['invc_total_cost'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
		$mail['subject']	= str_replace("%INV_AMT_PAID%", do_currency_format($_in_info['invc_total_paid'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
		$mail['subject']	= str_replace("%AMT_BAL_DUE%", do_currency_format($balance, 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
		$mail['subject']	= str_replace("%DATE_DUE%", $_in_info['invc_ts_due'], $mail['subject']);
		$mail['subject']	= str_replace("%DATE_ISSUED%", $_in_info['invc_ts'], $mail['subject']);
		$mail['subject']	= str_replace("%INV_TERMS%", $_in_info['invc_terms'], $mail['subject']);
		$mail['subject']	= str_replace("%INV_STATUS%", $_in_info['invc_status'], $mail['subject']);
		$mail['subject']	= str_replace("%INV_CYCLE%", $_in_info['invc_bill_cycle'], $mail['subject']);
		$mail['subject']	= str_replace("%CLIENT_NAME%", $clientname, $mail['subject']);

	# Set MTP (Mail Template Parameters) array
		$_MTP['to_name']		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
		$_MTP['to_email']		= $_MTP['cl_email'];
		$_MTP['from_name']		= $_cinfo['c_name'];
		$_MTP['from_email']		= $_cinfo['c_email'];
		$_MTP['subject']		= $mail['subject'];
		$_MTP['site']			= $_CCFG['_PKG_NAME_SHORT'];
		$_MTP['invc_url']		= BASE_HREF.'mod.php?mod=invoices&mode=view&invc_id='.$adata['invc_id'];
		$_MTP['Company_TaxNo']	= $_UVAR['CO_INFO_10_TAXNO'];
		$_MTP['Company_Name']	= $_UVAR['CO_INFO_01_NAME'];

	# Format currency amounts
		$_MTP['invc_total_cost']		= do_currency_format($_in_info['invc_total_cost'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_MTP['invc_total_paid']		= do_currency_format($_in_info['invc_total_paid'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_MTP['invc_subtotal_cost']	= do_currency_format($_in_info['invc_subtotal_cost'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_MTP['invc_balance']		= do_currency_format($balance, 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_MTP['invc_tax_01_percent']	= do_currency_format($_in_info['invc_tax_01_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);
		$_MTP['invc_tax_02_percent']	= do_currency_format($_in_info['invc_tax_02_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);
		$_MTP['invc_tax_01_amount']	= do_currency_format($_in_info['invc_tax_01_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
		$_MTP['invc_tax_02_amount']	= do_currency_format($_in_info['invc_tax_02_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);

	# Check returned records, do not send if not 1
		$_ret = 1;
		IF ($_in_info['numrows'] > 0) {

		# Load mail template (processed)
			IF ($adata['template'] == '' || $adata['template'] == 'email_invoice_copy' || $adata['template'] == 'email_invoice_copy_singleline') {
				IF ($_CCFG['SINGLE_LINE_EMAIL_INVOICE_ITEMS']) {
					$mail['message'] = get_mail_template('email_invoice_copy_singleline', $_MTP);
				} ELSE {
					$mail['message'] = get_mail_template('email_invoice_copy', $_MTP);
				}
			} ELSEIF ($adata['template'] == 'email_trans_ack') {

			# Get / massage trans data for MTP, merge into current
				$tdata			= get_mtp_trans_info($adata['it_id']);
				$data_new			= array_merge($_MTP, $tdata);
				$_MTP			= $data_new;
				$mail['message']	= get_mail_template('email_trans_ack', $_MTP);
			}

		# Call basic email function (ret=1 on error)
			$_ret = do_mail_basic($mail);
		}

	# Check return
		IF ($_ret) {
			$_ret_msg  = $_LANG['_INVCS']['INV_EMAIL_MSG_03_L1'];
			$_ret_msg .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_03_L2'];
		} ELSE {
			$_ret_msg  = $_LANG['_INVCS']['INV_EMAIL_MSG_04_PRE'].$_sp.$adata['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_04_SUF'];
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_INVCS']['INV_EMAIL_RESULT_TITLE'];

		$_cstr  = '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_ret_msg.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag = 1;
		$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=view&cl_id='.$_in_info['invc_cl_id'], $_TCFG['_IMG_BACK_TO_CLIENT_M'],$_TCFG['_IMG_BACK_TO_CLIENT_M_MO'],'','');


	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1').'<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**
 * Convert record ID to name of last nag email sent
 * @param int $anid Record ID of last nag email template sent
 * @return string Title of email template
 */
function nag_val_to_str($anid) {
	global $_DBCFG, $db_coin, $_LANG;
	$_title = '';
	IF ($anid == '-1') {
		$_title = $_LANG['_INVCS']['Do_Not_Nag'];
	} ELSEIF ($anid == 0) {
		$_title = $_LANG['_INVCS']['none'];
	} ELSE {
		$query  = 'SELECT overdue_title ';
		$query .= ' FROM '.$_DBCFG['reminders'];
		$query .= " WHERE overdue_id='".$anid."'";
		$result = $db_coin->db_query_execute($query);
		while(list($overdue_title) = $db_coin->db_fetch_row($result)) {$_title = $overdue_title;}
	}
	return $_title;
}


/**************************************************************
 * End Module Functions
**************************************************************/
?>