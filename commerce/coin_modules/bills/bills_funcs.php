<?php
/**
 * Module: Bills (Common Functions)
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
	IF (eregi('bills_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=bills');
		exit;
	}


/**************************************************************
 * Module Functions
**************************************************************/
# Do Data Input Validate
function do_input_validation($_adata) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
		IF ($_adata['obj'] == 'bill' && ($_adata['mode'] == 'add' || $_adata['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_adata['nill_id'] )		{ $err_entry['flag'] = 1; $err_entry['bill_id'] = 1; }
		#	IF (!$_adata['bill_status'] )		{ $err_entry['flag'] = 1; $err_entry['bill_status'] = 1; }
			IF (!$_adata['bill_s_id'] )		{ $err_entry['flag'] = 1; $err_entry['bill_cl_id'] = 1; }
		#	IF (!$_adata['bill_total_cost] )	{ $err_entry['flag'] = 1; $err_entry['bill_total_cost'] = 1; }
		#	IF (!$_adata['bill_total_paid] )	{ $err_entry['flag'] = 1; $err_entry['bill_total_paid'] = 1; }
		#	IF (!$_adata['bill_subtotal_cost] ){ $err_entry['flag'] = 1; $err_entry['bill_subtotal_cost'] = 1; }
		#	IF (!$_adata['bill_tax_01_percent] ){ $err_entry['flag'] = 1; $err_entry['bill_tax_01_percent'] = 1; }
		#	IF (!$_adata['bill_tax_01_amount] ){ $err_entry['flag'] = 1; $err_entry['bill_tax_01_amount'] = 1; }
		#	IF (!$_adata['bill_tax_02_percent] ){ $err_entry['flag'] = 1; $err_entry['bill_tax_02_percent'] = 1; }
		#	IF (!$_adata['bill_tax_02_amount] ){ $err_entry['flag'] = 1; $err_entry['bill_tax_02_amount'] = 1; }
			IF (!$_adata['bill_ts'] )		{ $err_entry['flag'] = 1; $err_entry['bill_ts'] = 1; }
			IF (!$_adata['bill_ts_due'] )		{ $err_entry['flag'] = 1; $err_entry['bill_ts_due'] = 1; }
		#	IF (!$_adata['bill_ts_paid] )		{ $err_entry['flag'] = 1; $err_entry['bill_ts_paid'] = 1; }
		#	IF (!$_adata['bill_cycle] )		{ $err_entry['flag'] = 1; $err_entry['bill_cycle'] = 1; }
		#	IF (!$_adata['bill_recurring] )	{ $err_entry['flag'] = 1; $err_entry['bill_recurring'] = 1; }
		#	IF (!$_adata['bill_recurr_proc] )	{ $err_entry['flag'] = 1; $err_entry['bill_recurr_proc'] = 1; }
		}

//		IF ($_adata['obj'] == 'bitem' && ($_adata['mode'] == 'add' || $_adata['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_adata[bi_bill_id] )		{ $err_entry['flag'] = 1; $err_entry['bi_bill_id'] = 1; }
		#	IF (!$_adata[bi_item_no] )		{ $err_entry['flag'] = 1; $err_entry['bi_item_no'] = 1; }
		#	IF (!$_adata[bi_item_name] )		{ $err_entry['flag'] = 1; $err_entry['bi_item_name'] = 1; }
		#	IF (!$_adata[bi_item_desc] )		{ $err_entry['flag'] = 1; $err_entry['bi_item_desc'] = 1; }
		#	IF (!$_adata[bi_item_cost] )		{ $err_entry['flag'] = 1; $err_entry['bi_item_cost'] = 1; }
		#	IF (!$_adata[bi_prod_id] )		{ $err_entry['flag'] = 1; $err_entry['bi_prod_id'] = 1; }
		#	IF (!$_adata[bi_apply_tax_01] )	{ $err_entry['flag'] = 1; $err_entry['bi_apply_tax_01'] = 1; }
		#	IF (!$_adata[bi_apply_tax_02] )	{ $err_entry['flag'] = 1; $err_entry['bi_apply_tax_02'] = 1; }
//		}

//		IF ($_adata['obj'] == 'trans' && ($_adata['mode'] == 'add' || $_adata['mode'] == 'edit')) {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_adata[bt_id] )			{ $err_entry['flag'] = 1; $err_entry['bt_id'] = 1; }
		#	IF (!$_adata[bt_ts] )			{ $err_entry['flag'] = 1; $err_entry['bt_ts'] = 1; }
		#	IF (!$_adata[bt_bill_id] )		{ $err_entry['flag'] = 1; $err_entry['bt_bill_id'] = 1; }
		#	IF (!$_adata[bt_type] )			{ $err_entry['flag'] = 1; $err_entry['bt_type'] = 1; }
		#	IF (!$_adata[bt_origin] )		{ $err_entry['flag'] = 1; $err_entry['bt_origin'] = 1; }
		#	IF (!$_adata[bt_desc] )			{ $err_entry['flag'] = 1; $err_entry['bt_desc'] = 1; }
//		}

		return $err_entry;
}


# Do display entry (individual bill entry)
function do_display_entry($adata) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_tstr = '';

	# Build common td start tag / col strings (reduce text)
		$_td_str_left		= '<td class="TP3SML_NR" width="40%">';
		$_td_str_right		= '<td class="TP3SML_NL" width="60%">';
		$_td_str_center	= '<td class="TP3SML_NC">';
		$_td_str_span_2	= '<td class="TP3SML_NC" colspan="2" valign="top">';

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$query	.= ' WHERE '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';
		$query	.= ' AND '.$_DBCFG['bills'].'.bill_id='.$adata['bill_id'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Build Title String, Content String, and Footer Menu String
				IF (!$_CCFG['_IS_PRINT']) {
					$_tstr .= $_LANG['_BILLS']['View_Supplier_Bill_ID'].$_sp.$row['bill_id'];
					$_tstr .= ' <a href="admin.php?cp=suppliers&op=view&s_id='.$row['bill_s_id'].'">'.$_TCFG['_IMG_BACK_TO_SUPPLIER_M'].'</a>'.$_nl;
				}

			# Open
				$_cstr  = '<br>'.$_nl.'<center>'.$_nl;
				$_cstr .= '<table cellpadding="0" width="95%">'.$_nl;

			# Add Form Title
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP1SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= '<h1>'.$_LANG['_BILLS']['Form_Title'].'</h1>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;

			# Primary Seller / Buyer Info Row
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP1SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= '<table width="100%"><tr><td class="TP0SML_NC" valign="top" width="50%">'.$_nl;

			# Supplier Info Cell
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
				$_cstr .= $_LANG['_BILLS']['Remit_To'].$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_center.$_nl;
				$_cstr .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$_nl;

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Company'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_company'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Supplier_Name'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_name_first'].$_sp.$row['s_name_last'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Address'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_addr_01'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($row['s_addr_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$row['s_addr_02'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_City'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_city'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_State_Prov'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_state_prov'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_zip_code'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Country'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_country'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['bill_s_id'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Email'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['s_email'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Recurring'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_valtostr_no_yes($row['bill_recurring']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($_SEC['_sadmin_flg']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b><nobr>'.$_LANG['_BILLS']['l_Recurring_Processed'].$_sp.'</nobr></b></td>'.$_nl;
					$_cstr .= $_td_str_right.do_valtostr_no_yes($row['bill_recurr_proc']).'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Invoice_Number'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['bill_invoice_number'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Insert blank lines to make up for missing rows
				IF (!$row['s_addr_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '<td class="TP0SML_NC" valign="top" width="50%">'.$_nl;


			# Over-ride the current buyer address info, if necessary
				IF (file_exists(PKG_PATH_OVERRIDES.'bills_address_override.php')) {
					require(PKG_PATH_OVERRIDES.'bills_address_override.php');
				}

			# Company Info Cell
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BL">'.$_nl;
				$_cstr .= $_LANG['_BILLS']['Bill_To'].$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_td_str_center.$_nl;

				$_cstr .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Company'].$_sp.'</b></td>'.$_nl;
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
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Address'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_02_ADDR_01'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				IF ($_UVAR['CO_INFO_03_ADDR_02']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.''.$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_03_ADDR_02'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_City'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_04_CITY'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_State_Prov'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_05_STATE_PROV'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_06_POSTAL_CODE'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Country'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_UVAR['CO_INFO_07_COUNTRY'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl.$_td_str_span_2.'<b>'.$_sp.'</b></td>'.$_nl.'</tr>'.$_nl;
				IF ($_UVAR['CO_INFO_08_PHONE']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Phone'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_UVAR['CO_INFO_08_PHONE'].'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}
				IF ($_UVAR['CO_INFO_09_FAX']) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Fax'].$_sp.'</b></td>'.$_nl;
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
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Tax_Number'].$_sp.'</b></td>'.$_nl;
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

				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Bill Info Row
				$_td_TP3SML_BC		= '<td class="TP3SML_BC">';
				$_td_TP3SML_NC		= '<td class="TP3SML_NC">';

				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Bill_ID'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Bill_Date'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Status'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Date_Due'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Total_Cost'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Date_Paid'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Total_Paid'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_BC.$_nl.$_LANG['_BILLS']['l_Billing_Cycle'].$_nl.'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$row['bill_id'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['bill_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$row['bill_status'].$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['bill_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.do_currency_format($row['bill_total_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.dt_make_datetime($row['bill_ts_paid'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.do_currency_format($row['bill_total_paid'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl.'</td>'.$_nl;
				$_cstr .= $_td_TP3SML_NC.$_nl.$_CCFG['BILL_CYCLE'][$row['bill_cycle']].$_nl.'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

			# Display Bills Items Row
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
				$_cstr .= do_view_bills_items($adata);
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;


			# Transactions Link Row
				IF (
					($_CCFG['BILL_VIEW_SHOW_TRANS'] == 1 && !$_CCFG['_IS_PRINT']) ||
					($_CCFG['BILL_VIEW_SHOW_TRANS'] == 2 && $_CCFG['_IS_PRINT']) ||
					($_CCFG['BILL_VIEW_SHOW_TRANS'] == 3)
				) {
					$_cstr .= '<tr>'.$_nl.'<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= '<hr>'.$_nl;
					$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
					$_cstr .= '<tr>'.$_nl;
					$_cstr .= '<td class="TP3SML_NC" colspan="2" valign="top">'.$_nl;
					$_cstr .= do_view_transactions($row).$_nl;
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				}

			# Closeout
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</center>'.$_nl;

			}

		} ELSE {
		# Build Title String, Content String, and Footer Menu String
			$_tstr .= $_LANG['_BILLS']['View_Supplier_Bill_ID'];

			$_cstr  = '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3MED_NC"><b>'.$_LANG['_BILLS']['Error_Bill_Not_Found'].'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
		}

		IF ($_CCFG['_IS_PRINT'] != 1) {
		# Build function argument text
			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('mod_print.php?mod=bills&mode=view&bill_id='.$adata['bill_id'], $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=copy&bill_id='.$adata['bill_id'], $_TCFG['_IMG_COPY_M'],$_TCFG['_IMG_COPY_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=add&obj=trans&bt_bill_id='.$adata['bill_id'], $_TCFG['_IMG_PAYMENT_M'],$_TCFG['_IMG_PAYMENT_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$adata['bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			$_mstr_flag = 1;
		} ELSE {
			$_mstr_flag = 0;
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, 1);
		$_out .= '<br>'.$_nl;

		return $_out;
}


# Do list field form for: Supplier Bills
function do_view_bills($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_out	= '';
		$_ps		= '';
		IF ($adata['status'] && $adata['status'] != 'all') {$_ps .= '&status='.$adata['status'];}
		IF ($adata['notstatus']) {$_ps .= '&notstatus='.$adata['notstatus'];}

	# Set Query for select.
		$query	.= 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$_where	.= ' WHERE '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';

	# Show only selected status bills
		IF ($adata['status'] && $adata['status'] != 'all') {$_where .= ' AND '.$_DBCFG['bills'].".bill_status='".$db_coin->db_sanitize_data($adata['status'])."'";}
		IF ($adata['notstatus']) {$_where .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($adata['notstatus'])."'";}

	# Set to selected supplier id's
		IF ($adata['bill_s_id']) {$_where .= ' AND '.$_DBCFG['bills'].".bill_s_id = ".$adata['bill_s_id'];}
		$_bill_s_id = $adata['bill_s_id'];

	# Set Filters
		IF (!$adata['fb'])		{$adata['fb'] = '';}
		IF ($adata['fb'] == 1)	{$_where .= ' AND '.$_DBCFG['bills'].".bill_status='".$db_coin->db_sanitize_data($adata['fs'])."'";}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'D';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])			{$adata['sb'] = 5;}
		IF ($adata['sb'] == 1)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_id '.$order_AD;}
		IF ($adata['sb'] == 2)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_status '.$order_AD;}
		IF ($adata['sb'] == 3)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_cycle '.$order_AD;}
		IF ($adata['sb'] == 4)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_ts '.$order_AD;}
		IF ($adata['sb'] == 5)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_ts_due '.$order_AD;}
		IF ($adata['sb'] == 6)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_total_cost '.$order_AD;}
		IF ($adata['sb'] == 7)		{$_order = ' ORDER BY '.$_DBCFG['bills'].'.bill_invoice_number '.$order_AD;}
		IF ($adata['sb'] == 8)		{$_order = ' ORDER BY '.$_DBCFG['suppliers'].'.s_company '.$order_AD;}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb= '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso= '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb= '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs= '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$query_ttl .= $_where;

		$result_ttl = $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
		# $_rec_next	- is page loading first record number
		# $_rec_start	- is a given page start record (which will be rec_next)
		$_rec_page	= $_CCFG['IPP_BILLS'];
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
		$_page_menu = $_LANG['_BILLS']['l_Pages'].$_sp;
		for ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = (($i*$_rec_page)-$_rec_page);
			IF ($_rec_start == $_rec_next) {

			# Loading Page start record so no link for this page.
				$_page_menu .= $i;
			} ELSE {
				$_page_menu .= '<a href="mod.php?mod=bills&mode=view'.$_link_xtra.$_ps.'&rec_next='.$_rec_start;
				IF ($adata['bill_s_id']) {$_page_menu .= "&bill_s_id=".$adata['bill_s_id'];}
				$_page_menu .= '">'.$i.'</a>';
			}

			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="mod.php?mod=bills&sb=';
		$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&fc='.$adata['fc'].'&rec_next='.$_rec_next.$_ps.'">';

		$_hdr_link_1 = $_LANG['_BILLS']['l_ID'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_2 = $_LANG['_BILLS']['l_Status'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_3 = $_LANG['_BILLS']['l_Billing_Cycle'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_4 = $_LANG['_BILLS']['l_Date'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_5 = $_LANG['_BILLS']['l_Date_Due'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_6 = $_LANG['_BILLS']['l_Amount'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_6 .= $_hdr_link_prefix.'6&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_7 = $_LANG['_BILLS']['Invoice_Number'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_7 .= $_hdr_link_prefix.'7&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_8 = $_LANG['_BILLS']['l_Company'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_8 .= $_hdr_link_prefix.'8&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_8 .= $_hdr_link_prefix.'8&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_9 = $_LANG['_BILLS']['l_Balance'].$_sp;

	# Build form output

	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
			$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=bills&status=all'.$_link_xtra;
			IF ($adata['bill_s_id']) {$_out .= '&bill_s_id='.$adata['bill_s_id'];}
			$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</nobr></td>';
			for ($i=0; $i< sizeof($_CCFG['BILL_STATUS']); $i++) {
				$_out .= '<td align="right"><nobr>&nbsp;[<a href="mod.php?mod=bills&status=' . $_CCFG['BILL_STATUS'][$i].$_link_xtra;
				IF ($adata['bill_s_id']) {$_out .= "&bill_s_id=".$adata['bill_s_id'];}
				$_out .= '">'.$_CCFG['BILL_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
			$_out .= '<td>&nbsp;</td>';
			for ($i=0; $i< sizeof($_CCFG['BILL_STATUS']); $i++) {
				$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=bills&notstatus='.$_CCFG['BILL_STATUS'][$i].$_link_xtra;
				IF ($adata['bill_s_id']) {$_out .= '&bill_s_id='.$adata['bill_s_id'];}
				$_out .= '">'.$_CCFG['BILL_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr></table>';
			$_out .= '<br><br>';
		}

	# Build the table
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.(10-$_CCFG['_IS_PRINT']).'">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_BILLS']['Suppliers_Bills'].$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_BILLS']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_BILLS']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= do_nav_link('mod.php?mod=cc&mode=search&sw=bills', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
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
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_6.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_9.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_8.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_7.'</td>'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Determine current bill balance
				$idata = do_get_bill_supplier_balance($_bill_s_id, $row['bill_id']);
				$p_idata['net_balance']+=$idata['net_balance'];
				$p_idata['total_cost']+=$row['bill_total_cost'];

			# Color code recurring but not yet recurred bills row
				IF ($row['bill_recurring'] && !$row['bill_recurr_proc']) {
					$_out .= '<tr class="GRN_DEF_ENTRY">'.$_nl;
				} ELSE {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				}
				$_out .= '<td class="TP3SML_NC">'.$row['bill_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['bill_status'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$_CCFG['BILL_CYCLE'][$row['bill_cycle']].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bill_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bill_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">';
				IF ($idata['net_balance']) {
					$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				} ELSE {$_out .= $_sp;}
				$_out .= '</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['s_company'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['bill_invoice_number'].'</td>'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
					$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_out .= do_nav_link('mod_print.php?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
						$_out .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');

// Uncomment the next line to get another icon which will take you
// directly to the items editor instead of the bill editor.
//						$_out .= do_nav_link('mod.php?mod=bills&mode=edit&obj=bitem&bi_bill_id='.$row['bill_id'].'&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_HELP_S'],$_TCFG['_S_IMG_HELP_S_MO'],'','');
						$_out .= do_nav_link('mod.php?mod=bills&mode=delete&stage=1&bill_id='.$row['bill_id'].'&bill_ts='.$row['bill_ts'].'&bill_status='.$row['bill_status'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
					$_out .= '</nobr></td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

	# Show display only totals footer row
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_BILLS']['l_Amount'].$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($p_idata['total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($p_idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="2">'.$_sp.'</td>'.$_nl;
		IF (!$_CCFG['_IS_PRINT']) {$_out .= '<td class="TP3SML_BL">'.$_sp.'</td>'.$_nl;}
		$_out .= '</tr>'.$_nl;

	# Show totals footer rows
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_BASE']['All'].':'.$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$idata = do_get_bill_supplier_balance($_bill_s_id,0);
		$_out .= do_currency_format($idata['total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="2">'.$_sp.'</td>'.$_nl;
		IF (!$_CCFG['_IS_PRINT']) {$_out .= '<td class="TP3SML_BL">'.$_sp.'</td>'.$_nl;}
		$_out .= '</td></tr>'.$_nl;

	# Closeout
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.(10-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

	# Return results
		return $_out;
}


# Do list field form for: Supplier Transactions
function do_view_transactions($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do we show the admin column
		$_showcol = 0;
		IF (!$_CCFG['_IS_PRINT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {$_showcol = 1;}

	# Set Query for select.
		$query	.= 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['bills_trans'].', '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$_where	.= ' WHERE '.$_DBCFG['bills_trans'].'.bt_bill_id='.$_DBCFG['bills'].'.bill_id';
		$_where	.= ' AND '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';

	# Check for bill_id for limited
		IF ($adata['bill_id'] > 0) {
			$_where .= ' AND '.$_DBCFG['bills'].'.bill_id='.$adata['bill_id'];
		}

		IF ($adata['bill_s_id'] > 0) {
			$_where .= ' AND '.$_DBCFG['bills'].'.bill_s_id='.$adata['bill_s_id'];
		}

	# Set Sort orders
		$_order = ' ORDER BY '.$_DBCFG['bills_trans'].'.bt_bill_id ASC, '.$_DBCFG['bills_trans'].'.bt_id ASC';

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate Header Row strings
		$_hdr_link_1 .= $_LANG['_BILLS']['l_ID'].$_sp.$_sp;
		$_hdr_link_2 .= $_LANG['_BILLS']['l_Trans_Date'].$_sp.$_sp;
		$_hdr_link_3 .= $_LANG['_BILLS']['l_Trans_Type'].$_sp.$_sp;
		$_hdr_link_4 .= $_LANG['_BILLS']['l_Trans_Origin'].$_sp.$_sp;
		$_hdr_link_5 .= $_LANG['_BILLS']['l_Trans_Description'].$_sp.$_sp;
		$_hdr_link_6 .= $_LANG['_BILLS']['l_Trans_Amount'].$_sp.$_sp;

	# Table open
		$_topen .= '<div align="center">'.$_nl;
		$_topen .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_topen .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_topen .= '<td class="TP3MED_NC" colspan="'.(8 - $_showcol).'">'.$_nl;

		$_topen .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_topen .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl;
		$_topen .= '<td class="TP0MED_NL"><b>'.$_LANG['_BILLS']['Supplier_Bill_Transactions'].$_sp.'</b></td>'.$_nl;
		IF ($_showcol) {
			$_topen .= '<td class="TP0MED_NR">'.$_nl;
			$_topen .= do_nav_link('mod.php?mod=cc&mode=search&sw=trans', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
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
			$_trow .= '<td class="TP3SML_NC"><b>'.$_LANG['_BILLS']['Actions'].'</b></td>'.$_nl;
		}
		$_trow .= '</tr>'.$_nl;

	# Initialize debite/credit totals
		$_chrg_ttl = 0;
		$_cred_ttl = 0;

	# Check param for which type listing
		IF ($_CCFG['BILL_SPLIT_TRANS_LIST_ENABLE'] == 1) {
		# Process query results
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Build Charge Rows
					IF ($row['bt_type'] == 0) {
						$_chrg_ttl += $row['bt_amount'];
						$_chrg .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$row['bt_bill_id'].'-'.str_pad($row['bt_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bt_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_TYPE'][$row['bt_type']].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_ORIGIN'][$row['bt_origin']].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NL">'.$row['bt_desc'].'</td>'.$_nl;
						$_chrg .= '<td class="TP3SML_NR">'.do_currency_format(($row['bt_amount'] * 1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF ($_showcol) {
							$_chrg .= '<td class="TP3SML_NL"><nobr>'.$_nl;
							IF (!$adata['bill_id']) {
								$_chrg .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$row['bt_bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
								$_chrg .= do_nav_link('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$row['bt_id'].'&bt_type='.$row['bt_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_chrg .= '</nobr></td>'.$_nl;
						}
						$_chrg .= '</tr>'.$_nl;
					}

				# Build Credit Rows
					IF ($row['bt_type'] != 0) {
						$_cred_ttl += ($row['bt_amount'] * -1);
						$_cred .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$row['bt_bill_id'].'-'.str_pad($row['bt_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bt_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_TYPE'][$row['bt_type']].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_ORIGIN'][$row['bt_origin']].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NL">'.$row['bt_desc'].'</td>'.$_nl;
						$_cred .= '<td class="TP3SML_NR">'.do_currency_format(($row['bt_amount'] * -1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF ($_showcol) {
							$_cred .= '<td class="TP3SML_NC">'.$_nl;
							$_cred .= do_nav_link ('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$row['bt_id'].'&bt_type='.$row['bt_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
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
			$_out .= $_LANG['_BILLS']['l_Charges_To_Account'].$_nl;
			$_out .= '</td>'.$_nl.'</tr>'.$_nl;

			$_out .= $_trow.$_nl;
			$_out .= $_chrg.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BR" colspan="'.(6 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_BILLS']['l_Total_Charges'].$_nl;
			$_out .= '</td><td class="TP3SML_BR">'.$_nl;
			$_out .= do_currency_format($_chrg_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
			$_out .= '</td><td class="TP3SML_BL" colspan="1">'.$_nl;
			$_out .= $_sp.$_nl;
			$_out .= '</td></tr>'.$_nl;

		# Credits
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BL" colspan="'.(8 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_BILLS']['l_Credits_To_Account'].$_nl;
			$_out .= '</td>'.$_nl.'</tr>'.$_nl;

			$_out .= $_trow.$_nl;
			$_out .= $_cred.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BR" colspan="'.(6 - $_showcol).'">'.$_nl;
			$_out .= $_LANG['_BILLS']['l_Total_Credits'].$_nl;
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
					IF ($row['bt_type'] == 0) {
						$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$row['bt_bill_id'].'-'.str_pad($row['bt_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bt_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_TYPE'][$row['bt_type']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_ORIGIN'][$row['bt_origin']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NL">'.$row['bt_desc'].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NR">'.do_currency_format(($row['bt_amount'] * 1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF (!$_CCFG['_IS_PRINT']) {
							$_out .= '<td class="TP3SML_NL">'.$_nl;
							IF (!$adata['bill_id']) {
								$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
								$_out .= do_nav_link('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$row['bt_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_out .= '</td>'.$_nl;
						}
						$_out .= '</tr>'.$_nl;
					}

				# Build Credit Row
					IF ($row['bt_type'] != 0) {
						$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$row['bt_bill_id'].'-'.str_pad($row['bt_id'],5,'0',STR_PAD_LEFT).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bt_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_TYPE'][$row['bt_type']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NC">'.$_CCFG['BILL_TRANS_ORIGIN'][$row['bt_origin']].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NL">'.$row['bt_desc'].'</td>'.$_nl;
						$_out .= '<td class="TP3SML_NR">'.do_currency_format(($row['bt_amount'] * -1),1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
						IF (!$_CCFG['_IS_PRINT']) {
							$_out .= '<td class="TP3SML_NL">'.$_nl;
							IF (!$adata['bill_id']) {
								$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$row['bt_bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
							}
							IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
								$_out .= do_nav_link('mod.php?mod=bills&mode=edit&obj=trans&bt_id='.$row['bt_id'].'&bt_type='.$row['bt_type'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
							}
							$_out .= '</td>'.$_nl;
						}
						$_out .= '</tr>'.$_nl;
					}
				}
			}
		}

	# Finish out with balance row, and table
		$idata = do_get_bill_supplier_balance($adata['bill_s_id'], $adata['bill_id']);
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= $_LANG['_BILLS']['l_Balance'].$_nl;
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

		return $_out;
}


# Do list field form for: Supplier Bill Items
function do_view_bills_items($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do we show the admin column
		$_showcol = 0;
		IF (!$_CCFG['_IS_PRINT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {$_showcol = 1;}

	# Set Query for select.
		$query	 = 'SELECT '.$_DBCFG['bills_items'].'.bi_bill_id';
		$query	.= ', '.$_DBCFG['bills_items'].'.bi_item_no';
		$query	.= ', '.$_DBCFG['bills_items'].'.bi_item_name';
		$query	.= ', '.$_DBCFG['bills_items'].'.bi_item_desc';
		$query	.= ', '.$_DBCFG['bills_items'].'.bi_item_cost';
		$query	.= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['bills_items'];
		$query	.= ' WHERE '.$_DBCFG['bills'].'.bill_id='.$_DBCFG['bills_items'].'.bi_bill_id';
		$query	.= ' AND '.$_DBCFG['bills'].'.bill_id='.$adata['bill_id'];
		$query	.= ' ORDER BY '.$_DBCFG['bills_items'].'.bi_item_no ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_out .= '<td class="TP3MED_NC" colspan="'.(5 + $_showcol).'">'.$_nl;
		$_out .= '<b>'.$_LANG['_BILLS']['Bill_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NC"><b>'.$_LANG['_BILLS']['l_Item_No'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_BILLS']['l_Name'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_LANG['_BILLS']['l_Description'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NR"><b>'.$_LANG['_BILLS']['l_Cost'].'</b>'.$_sp.$_sp.'</td>'.$_nl;
		IF ($_showcol) {
			$_out .= '<td class="TP3SML_NC"><b>'.$_LANG['_BILLS']['Actions'].'</b></td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		while(list($bi_bill_id, $bi_item_no, $bi_item_name, $bi_item_desc, $bi_item_cost) = $db_coin->db_fetch_row($result)) {
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			IF ($bi_item_name == 'NOTE') {
				$_out .= '<td class="TP3MED_NC" colspan="4">'.$_nl;
				$_out .= $bi_item_desc.$_nl;
				$_out .= '</td>'.$_nl;
			} ELSE {
				$_out .= '<td class="TP3SML_NC">'.$bi_item_no.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$bi_item_name.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$bi_item_desc.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($bi_item_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			IF ($_showcol) {
				$_out .= '<td class="TP3SML_NC">'.$_nl;
				$_out .= do_nav_link('mod.php?mod=bills&mode=edit&obj=bitem&bi_bill_id='.$bi_bill_id.'&bi_item_no='.$bi_item_no, $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
				$_out .= do_nav_link('mod.php?mod=bills&mode=delete&obj=bitem&bi_bill_id='.$bi_bill_id.'&bi_item_no='.$bi_item_no, $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;
		}

		$idata = do_get_bill_values($adata['bill_id']);

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NR" colspan="3">'.$_nl;
		IF ($_CCFG['BILL_TAX_01_ENABLE'] || $_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<b>'.$_LANG['_BILLS']['l_SubTotal_Cost'].'</b>'.$_sp.$_nl;
			$_out .= '<br>'.$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_01_ENABLE']) {
			$_out .= '<br>'.$_CCFG['BILL_TAX_01_LABEL'].$_sp.'('.do_currency_format($idata['bill_tax_01_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']).'%)'.$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<br>'.$_CCFG['BILL_TAX_02_LABEL'].$_sp.'('.do_currency_format($idata['bill_tax_02_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']).'%)'.$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_01_ENABLE'] || $_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<br>'.$_sp.'<br>'.$_nl;
		}

		$_out .= '<b>'.$_LANG['_BILLS']['l_Total_Cost'].'</b>'.$_sp.$_nl;
		$_out .= '<br>'.$_sp.$_nl;
		$_out .= '</td>'.$_nl;

		$_out .= '<td class="TP3SML_NR">'.$_nl;
		IF ($_CCFG['BILL_TAX_01_ENABLE'] || $_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<b>'.do_currency_format($idata['bill_subtotal_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b>'.$_sp.$_nl;
			$_out .= '<br>'.'-------------------'.$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_01_ENABLE']) {
			$_out .= '<br>'.do_currency_format($idata['bill_tax_01_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<br>'.do_currency_format($idata['bill_tax_02_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		}
		IF ($_CCFG['BILL_TAX_01_ENABLE'] || $_CCFG['BILL_TAX_02_ENABLE']) {
			$_out .= '<br>'.'-------------------'.$_sp.'<br>'.$_nl;
		}

		$_out .= '<b>'.do_currency_format($idata['bill_total_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</b>'.$_sp.$_nl;
		$_out .= '<br>'.$_sp.$_nl;
		$_out .= '</td>'.$_nl;

		IF ($_showcol) {
			$_out .= '<td class="TP3SML_BC"><b>'.$_sp.'</b></td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($adata['obj'] == 'bitem') {
				$_out .= '<tr class="BLK_DEF_FMENU"><td class="TP3SML_BC" colspan="'.(5 + $_showcol).'">'.$_nl;
				$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$adata['bill_id'], $_TCFG['_IMG_BACK_TO_BILL_M'],$_TCFG['_IMG_BACK_TO_BILL_M_MO'],'','');
				$_out .= '</td></tr>'.$_nl;
			} ELSE {
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
					$_out .= '<tr class="BLK_DEF_FMENU"><td class="TP3SML_BC" colspan="'.(5 + $_showcol).'">'.$_nl;
					$_out .= do_nav_link('mod.php?mod=bills&mode=view&obj=bitem&bill_id='.$adata['bill_id'], $_TCFG['_IMG_BITEMS_EDITOR_M'],$_TCFG['_IMG_BITEMS_EDITOR_M_MO'],'','');
					$_out .= '</td></tr>'.$_nl;
				}
			}
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}


/**************************************************************
 * End Module Functions
**************************************************************/
?>