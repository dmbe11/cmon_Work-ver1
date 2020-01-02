<?php
/**
 * Module: Bills (Administrative Functions)
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
	IF (eregi('bills_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=bills');
		exit;
	}



# Do Form for Add / Edit
function do_form_add_edit($adata, $aerr_entry) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;

	# Build mode dependent strings
		switch($adata['mode']) {
			case "add":
				$mode_proper	= $_LANG['_BILLS']['B_Add'];
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_BILLS']['B_Edit'];
				$mode_button	= $_LANG['_BILLS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_proper	= $_LANG['_BILLS']['B_Add'];
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_BILLS']['Bills_Entry'].$_sp.'( <b>(*)</b>'.$_sp.$_LANG['_BILLS']['denotes_optional_items'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_BILLS']['BILL_ERR_ERR_HDR1'].'<br>'.$_LANG['_BILLS']['BILL_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['bill_id']) 			{$err_str .= $_LANG['_BILLS']['BILL_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['bill_status']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR02']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_s_id']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR03']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_total_cost'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR04']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_total_paid'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR04a']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_ts']) 			{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR05']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_ts_due']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR06']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_ts_paid']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR07']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_cycle']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR08']; $err_prv = 1;}
		 	IF ($aerr_entry['bill_total_paid'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR12']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';

	# Misc mode check for display values
		IF ($adata['mode'] == 'add') {$adata['bill_id'] = '('.$_LANG['_BILLS']['auto-assigned'].')';}
		IF ($adata['mode'] == 'add' && $adata['bill_ts'] == '') {$adata['bill_ts'] = dt_get_uts();}
		IF ($adata['mode'] == 'add' && $adata['bill_ts_due'] == '') {$adata['bill_ts_due'] = dt_get_uts()+($_CCFG['BILL_DUE_DAYS_OFFSET']*(24*60*60));}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="bills">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Bill_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['bill_id'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['bill_status'] == '') {$adata['bill_status'] = $_CCFG['BILL_STATUS'][1];}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_status_bill('bill_status', $adata['bill_status'], 0).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Supplier'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_suppliers_edit('bill_s_id', $adata['bill_s_id']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Bill_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('bill_ts', $adata['bill_ts'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Date_Due'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('bill_ts_due', $adata['bill_ts_due'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Date_Paid'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('bill_ts_paid', $adata['bill_ts_paid'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['bill_cycle'] == '') {$adata['bill_cycle'] = 1;}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Billing_Cycle'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_bill_cycle('bill_cycle', $adata['bill_cycle']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Recurring'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('bill_recurring', $adata['bill_recurring'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Recurring_Processed'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('bill_recurr_proc', $adata['bill_recurr_proc'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['BILL_TAX_01_ENABLE'] == 1 || $_CCFG['BILL_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['AutoCalc_Tax'].'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['bill_tax_autocalc'] == '') {$adata['bill_tax_autocalc'] = 1;}
			IF ($adata['bill_tax_autocalc'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['bill_tax_autocalc'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="bill_tax_autocalc" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_01_ENABLE']) {
			IF ($adata['bill_tax_01_percent'] == '') {$adata['bill_tax_01_percent'] = do_currency_format($_CCFG['BILL_TAX_01_DEF_VAL'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Tax_Rate'].'- '.$_CCFG['BILL_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="bill_tax_01_percent" SIZE="6" value="'.$adata['bill_tax_01_percent'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_02_ENABLE']) {
			IF ($adata['bill_tax_02_percent'] == '') {$adata['bill_tax_02_percent'] = do_currency_format($_CCFG['BILL_TAX_02_DEF_VAL'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Tax_Rate'].'- '.$_CCFG['BILL_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="bill_tax_02_percent" SIZE="6" value="'.$adata['bill_tax_02_percent'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_01_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Tax_Amount'].'- '.$_CCFG['BILL_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="bill_tax_01_amount" SIZE="6" value="'.$adata['bill_tax_01_amount'].'">'.$_nl;
			$_cstr .= $_sp.$_LANG['_BILLS']['Tax_Amount_Manual_Calc'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Tax_Amount'].'- '.$_CCFG['BILL_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="bill_tax_02_amount" SIZE="6" value="'.$adata['bill_tax_02_amount'].'">'.$_nl;
			$_cstr .= $_sp.$_LANG['_BILLS']['Tax_Amount_Manual_Calc'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['Invoice_Number'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="bill_invoice_number" SIZE="15" value="'.$adata['bill_invoice_number'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Make read-only, to avoid confusion on part of some users.
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Total_Paid'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_currency_format($adata['bill_total_paid'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_cstr .= '<INPUT TYPE=hidden NAME="bill_total_paid" SIZE="10" value="'.$adata['bill_total_paid'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="bill_id" value="'.$adata['bill_id'].'">'.$_nl;
		IF (!$_CCFG['BILL_TAX_01_ENABLE']) {
			$_cstr .= '<input type="hidden" name="bill_tax_01_percent" value="'.$adata['bill_tax_01_percent'].'">'.$_nl;
		}
		IF (!$_CCFG['BILL_TAX_02_ENABLE']) {
			$_cstr .= '<input type="hidden" name="bill_tax_02_percent" value="'.$adata['bill_tax_02_percent'].'">'.$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', 1).$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_BILLS']['B_Reset'], 'button_form_h', 'button_form', 1).$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_SEC['_sadmin_flg'] && $adata['mode'] == 'edit') {
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$adata['bill_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	return $_out;
}

# Do Form for Add / Edit Items Manually
function do_form_add_edit_items($adata, $aerr_entry) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;

	# Build mode dependent strings
		switch($adata['mode']) {
			case "add":
				$mode_proper	= $_LANG['_BILLS']['B_Add'];
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_BILLS']['B_Edit'];
				$mode_button	= $_LANG['_BILLS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_proper	= $_LANG['_BILLS']['B_Add'];
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_BILLS']['Bill_Items_Entry'];

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_BILLS']['BILL_ERR_ERR_HDR1'].'<br>'.$_LANG['_BILLS']['BILL_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['bi_bill_id']) 	{$err_str .= $_LANG['_BILLS']['BILL_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['bi_item_no']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR16']; $err_prv = 1;}
		 	IF ($aerr_entry['bi_item_name']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR17']; $err_prv = 1;}
		 	IF ($aerr_entry['bi_item_desc'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR18']; $err_prv = 1;}
		 	IF ($aerr_entry['bi_item_cost']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR19']; $err_prv = 1;}
		 	IF ($aerr_entry['bi_prod_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERR20']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2">';

	# Set items id to bill_id
		$adata['bi_bill_id'] = $adata['bill_id'];

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="bills">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="bitem">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center_span.$_nl;
		$_cstr .= '<b>'.$_LANG['_BILLS']['BILL_ADD_ITEM_MSG_TXT01'].'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Bill_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['bi_bill_id'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'edit') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Item_No'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="bi_item_no" SIZE=12 value="'.$adata['bi_item_no'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="bi_item_name" SIZE=30 value="'.htmlspecialchars($adata['bi_item_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="bi_item_desc" SIZE=50 value="'.htmlspecialchars($adata['bi_item_desc']).'" maxlength="75">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Item_Cost'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="bi_item_cost" SIZE=12 value="'.$adata['bi_item_cost'].'">'.$_sp.'('.$_LANG['_BILLS']['no_commas'].')'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['BILL_TAX_01_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['BILL_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['bi_apply_tax_01'] == '') {$adata['bi_apply_tax_01'] = 1;}
			IF ($adata['bi_apply_tax_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['bi_apply_tax_01'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="bi_apply_tax_01" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_BILLS']['Apply_Tax_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['BILL_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['bi_apply_tax_02'] == '') {$adata['bi_apply_tax_02'] = 1;}
			IF ($adata['bi_apply_tax_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['bi_apply_tax_02'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="bi_apply_tax_02" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_BILLS']['Apply_Tax_02'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['BILL_TAX_01_ENABLE'] == 1 && $_CCFG['BILL_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['bi_calc_tax_02_pb'] == 1) {$_set = ' CHECKED'; } ELSE { $_set = ''; $adata['bi_calc_tax_02_pb'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="bi_calc_tax_02_pb" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_BILLS']['Calc_Tax_02_On_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($adata['mode'] == 'add') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center_span.$_nl;
			 IF ($adata['bi_prod_add'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="bi_prod_add" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.$_LANG['_BILLS']['BILL_ADD_ITEM_MSG_TXT02'].$_nl;
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Product'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_products('bi_prod_id', $adata['bi_prod_id']).$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="bill_id" value="'.$adata['bill_id'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="bi_bill_id" value="'.$adata['bi_bill_id'].'">'.$_nl;

		IF ($adata['mode'] == 'edit') {
			IF (!$adata['bi_item_no_orig'] || $adata['bi_item_no_orig'] == 0) {$adata['bi_item_no_orig'] = $adata['bi_item_no'];}
			$_cstr .= '<input type="hidden" name="bi_item_no_orig" value="'.$adata['bi_item_no_orig'].'">'.$_nl;
		}
		IF (!$_CCFG['BILL_TAX_01_ENABLE']) {$_cstr .= '<INPUT TYPE=hidden name="bi_apply_tax_01" value="'.$adata['bi_apply_tax_01'].'">'.$_nl;}
		IF (!$_CCFG['BILL_TAX_02_ENABLE']) {$_cstr .= '<INPUT TYPE=hidden name="bi_apply_tax_02" value="'.$adata['bi_apply_tax_02'].'">'.$_nl;}
		IF (!$_CCFG['BILL_TAX_02_ENABLE'] && !$_CCFG['BILL_TAX_02_ENABLE']) {
			$_cstr .= '<INPUT TYPE=hidden name="bi_calc_tax_02_pb" value="'.$adata['bi_calc_tax_02_pb'].'">'.$_nl;
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', 1).$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_BILLS']['B_Reset'], 'button_form_h', 'button_form', 1).$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr = do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$adata['bill_id'], $_TCFG['_IMG_BACK_TO_bill_M'],$_TCFG['_IMG_BACK_TO_bill_M_MO'],'','');

	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1).'<br>'.$_nl;

		return $_out;
}

# Do display items editor (for editing items)
function do_display_items_editor($adata, $aerr_entry) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / col strings (reduce text)
		$_td_str_center	= '<td class="TP1SML_NC">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_BILLS']['l_Bill_ID'].$_sp.$adata['bill_id'].$_sp.$_LANG['_BILLS']['Items_Editor'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="95%">'.$_nl;
		$_cstr .= '<tr>'.$_nl.$_td_str_center.$_nl;
		$_cstr .= do_view_bills_items($adata);
		$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;
		$_cstr .= '<br>'.$_nl;

		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="95%">'.$_nl;
			$_cstr .= '<tr>'.$_nl.$_td_str_center.$_nl;
			$_cstr .= do_form_add_edit_items($adata, $aerr_entry);
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
			$_cstr .= '<br>'.$_nl;
		}

		$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$adata['bill_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link('mod.php?mod=bills&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('mod.php?mod=bills&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1).'<br>'.$_nl;

	return $_out;
}

# Do Form for Add / Edit Transactions
function do_form_add_edit_trans($adata, $aerr_entry) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;
		$_out = '';

	# Get Bill Total for insert to amount paid.
		$idata = do_get_bill_values($adata['bt_bill_id']);

	# Calc paid to date, amount remaining values
		$ptd		= do_get_bill_PTD($adata['bt_bill_id']);
		$_bal_due	= $idata['bill_total_cost'] - $ptd;
		IF ($adata['mode'] == 'add') {$adata['bt_amount'] = $_bal_due;}
		$_ptd_str	 = do_currency_format($idata['bill_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_ptd_str	.= ' - '.do_currency_format($ptd,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_ptd_str	.= ' = '.do_currency_format($_bal_due,1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);

	# Build mode dependent strings
		switch ($adata['mode']) {
			case "add":
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
			case "edit":
				$mode_button	= $_LANG['_BILLS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_button	= $_LANG['_BILLS']['B_Add'];
				break;
		}

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_BILLS']['BILL_ERR_ERR_HDR1'].'<br>'.$_LANG['_BILLS']['BILL_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['bt_id']) 	{$err_str .= $_LANG['_BILLS']['BILL_ERR_ERRxx']; $err_prv = 1;}
			IF ($aerr_entry['bt_ts']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_BILLS']['BILL_ERR_ERRxx']; $err_prv = 1;}
	 		$_out .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_spacer				= '<tr><td class="TP1SML_NC" colspan="2">'.$_sp.'</td></tr>';
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';
		$_td_str_center_span	= '<td class="TP3SML_NC" width="100%" colspan="2">';

	# Do form
		$_out .= '<center>'.$_nl;
		$_out .= '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="mod" value="bills">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="obj" value="trans">'.$_nl;

		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr>'.$_td_str_center_span.$_nl;
		$_out .= '<b>'.$_LANG['_BILLS']['Set_Payment_Entry_Message'].'='.$_sp.$adata['bt_bill_id'].'<br>'.$_nl;
		$_out .= $_LANG['_BILLS']['Set_Payment_Entry_Message_Cont'].'</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Date'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF (!$adata['bt_ts']) {$adata['bt_ts'] = dt_get_uts();} # ELSE {$adata['bt_ts'] = $idata['bt_ts'];}
		$_out .= do_date_edit_list('bt_ts', $adata['bt_ts'], 1).$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		IF ($adata['bt_type'] == '') {$adata['bt_type'] = 2;}
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Type'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'edit' && $adata['bt_type'] == '0') {
			$_out .= '<INPUT TYPE=hidden name="bt_type" value="'.$adata['bt_type'].'">'.$_nl;
			$_out .= $_CCFG['BILL_TRANS_TYPE'][$adata['bt_type']].$_nl;
		} ELSE {
			$_out .= do_select_list_trans_type('bt_type', $adata['bt_type']).$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		IF ($adata['bt_origin'] == '') {$adata['bt_origin'] = 1;}
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Origin'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'edit' && $adata['bt_type'] == '0') {
			$_out .= '<INPUT TYPE=hidden name="bt_origin" value="'.htmlspecialchars($adata['bt_origin']).'">'.$_nl;
			$_out .= $_CCFG['BILL_TRANS_ORIGIN'][$adata['bt_origin']].$_nl;
		} ELSE {
			$_out .= do_select_list_trans_origin('bt_origin', $adata['bt_origin']).$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Description'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		$_out .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="bt_desc" SIZE="30" value="'.htmlspecialchars($adata['bt_desc']).'" maxlength="50">'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$adata['bt_amount'] = number_format($adata['bt_amount'],$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT'],'.','');
		IF ($adata['mode'] == 'edit' && $adata['bt_type'] == '0') {
			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Amount'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= '<input type="hidden" name="bt_amount" value="'.$adata['bt_amount'].'">'.$_nl;
			$_out .= $adata['bt_amount'];
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		} ELSE {
			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Amount_Due'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= $_ptd_str.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_BILLS']['l_Trans_Amount'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= '<input class="PSML_NR" type="text" name="bt_amount" SIZE="10" value="'.$adata['bt_amount'].'">'.$_sp.'('.$_LANG['_BILLS']['no_commas'].')'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		}

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_sp.$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['bt_set_paid'] == '') {$adata['bt_set_paid'] = 0;}
		IF ($adata['bt_set_paid'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['bt_set_paid'] = 0;}
		$_out .= '<INPUT TYPE=CHECKBOX NAME="bt_set_paid" value="1"'.$_set.' border="0">'.$_nl;
		$_out .= $_sp.'<b>'.$_LANG['_BILLS']['Set_Bill_To_Paid'].'</b>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= $_spacer.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		$_out .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="bt_id" value="'.$adata['bt_id'].'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="bt_bill_id" value="'.$adata['bt_bill_id'].'">'.$_nl;
		$_out .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', 1).$_nl;
		$_out .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_BILLS']['B_Reset'], 'button_form_h', 'button_form', 1).$_nl;
		IF ($adata['mode'] == 'edit') {
			$_out .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_BILLS']['B_Delete_Entry'], 'button_form_h', 'button_form', 1).$_nl;
		}

		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;
		$_out .= '</center>'.$_nl;
		$_out .= '<br>'.$_nl;

		return $_out;
}

# Do suppliers select list
function do_select_list_suppliers_edit($aname, $avalue) {
	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl;

	# Set Query for select.
		$query	= 'SELECT s_id, s_name_first, s_name_last, s_company FROM '.$_DBCFG['suppliers'].' ORDER BY s_company ASC, s_name_last ASC, s_name_first ASC';
		$result	=  $db_coin->db_query_execute($query);
		$numrows	=  $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BILLS']['Please_Select'].'</option>'.$_nl;

	# Process query results
		IF ($numrows) {
			while(list($s_id, $s_name_first, $s_name_last, $s_company) =  $db_coin->db_fetch_row($result)) {
				$_out .= '<option value="'.$s_id.'"';
				IF ($s_id == $avalue) {$_out .= ' selected';}
				$_out .= '>'.$s_company.' - '.$s_name_first.' '.$s_name_last.'</option>'.$_nl;
			}
		}

	# Close SELECT and return
		$_out .= '</select>'.$_nl;
		return $_out;
}

# Do transaction type select list
function do_select_list_trans_type($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i < count($_CCFG['BILL_TRANS_TYPE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_TRANS_TYPE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}

# Do transaction origin select list
function do_select_list_trans_origin($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i < count($_CCFG['BILL_TRANS_ORIGIN']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_TRANS_ORIGIN'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}

# Do return string from value for: Title Row with bill action dropdown
function do_tstr_bill_action_list($atitle) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Search form
		$_sform  = '<FORM METHOD="POST" ACTION="mod.php">'.$_nl;
		$_sform .= '<input type="hidden" name="mod" value="bills" />'.$_nl;
		$_sform .= '<input type="hidden" name="obj" value="bill" />'.$_nl;
	#	$_sform .= 'Please Select:'.$_sp.$_nl;
		$_sform .= '<select class="select_form" name="mode" size="1" value="Action" onchange="submit();">'.$_nl;
		$_sform .= '<option value="" selected>'.$_LANG['_BILLS']['Actions'].'</option>'.$_nl;
		$_sform .= '<option value="autoupdate">'.$_LANG['_BILLS']['Auto_Update_Status'].'</option>'.$_nl;
		$_sform .= '<option value="autocopy">'.$_LANG['_BILLS']['Auto_Copy_Recurring'].'</option>'.$_nl;
		$_sform .= '</select>'.$_nl;
		$_sform .= '</FORM>'.$_nl;

		$_tstr 	.= '<table width="100%" cellpadding="0" cellspacing="0"><tr class="BLK_IT_TITLE_TXT">';
		$_tstr 	.= '<td class="TP0MED_BL" valign="top">'.$_nl.$atitle.$_nl.'</td>'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_tstr	.= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sform.$_nl.'</td>'.$_nl;
		} ELSE {
			$_tstr	.= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sp.$_sp.$_nl.'</td>'.$_nl;
		}
		$_tstr 	.= '</tr></table>';

	# Build form output
		return $_tstr;
}

# Do auto set bill status
function do_auto_bill_set_status() {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query	.= 'SELECT * FROM '.$_DBCFG['bills'];
		$query	.= " WHERE bill_status != '".$_CCFG['BILL_STATUS'][1]."'";
		$query	.= " AND bill_status != '".$_CCFG['BILL_STATUS'][3]."'";
		$query	.= " AND bill_status != '".$_CCFG['BIL_STATUS'][5]."'";
		$query	.= " AND bill_status != '".$_CCFG['BILL_STATUS'][6]."'";
		$query	.= ' ORDER BY '.$_DBCFG['bills'].".bill_id";

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Call timestamp function
				$_uts = dt_get_uts();

			# Auto-Generate Status
				# Status Levels:
				#	void			- ignore it
				#	draft		- being created, not done yet, do not change it.
				#	pending		- pending, set by admin after draft, done but not due / sent
				#	due			- between Bill date and due date +1
				#	overdue		- due date +1
				#	paid			- paid
				#
				#	$_CCFG['BILL_STATUS'][0]		= 'due';				# For Due bills
				#	$_CCFG['BILL_STATUS'][1]		= 'draft';			# For Draft Version of Bill
				#	$_CCFG['BILL_STATUS'][2]		= 'overdue';			# For Overdue bills
				#	$_CCFG['BILL_STATUS'][3]		= 'paid';				# For Paid bills
				#	$_CCFG['BILL_STATUS'][4]		= 'pending';			# For Pending (To Be Sent) bills
				#	$_CCFG['BILL_STATUS'][5]		= 'void';				# For Void bills

				$_bill_status_auto 	= $row['bill_status'];
				$_due_plus_one		= $row['bill_ts_due']+(60*60*24);

				IF (($_uts < $row['bill_ts']))							{$_bill_status_auto = $_CCFG['BILL_STATUS'][4];}
				IF (($_uts >= $row['bill_ts']) && ($_uts <= $_due_plus_one))	{$_bill_status_auto = $_CCFG['BILL_STATUS'][0];}
				IF (($_uts > $_due_plus_one))								{$_bill_status_auto = $_CCFG['BILL_STATUS'][2];}
				IF ($row['bill_total_cost'] - $row['bill_total_paid'] == 0)		{$_invc_status_auto = $_CCFG['BILL_STATUS'][3];}

			# Call function to auto-set status
				$_ret = do_set_bill_status($row['bill_id'], $_bill_status_auto);
			}
		}

		return $numrows;
	}

# Do Copy bill
function do_bill_copy($adata) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Check for $adata[bill_id]- will determine select string (one for edit, all for list)
		IF ($adata['bill_id'] && $adata['bill_id'] != 0) {

		# Set Query for select and execute
			$query_pi = 'SELECT * FROM '.$_DBCFG['bills'];
			$query_pi .= " WHERE bill_id='".$adata['bill_id']."'";

		# Do select
			$result_pi	= $db_coin->db_query_execute($query_pi);
			$numrows_pi	= $db_coin->db_query_numrows($result_pi);

		# Process query results (assumes one returned row above- need to verify)
			while ($row = $db_coin->db_fetch_array($result_pi)) {

			# Calc new dates
				switch($row['bill_cycle']) {
					case 0:
						$row['bill_ts']		= $row['bill_ts'] + (3600*24*$_CCFG['BILL_CYCLE_VAL'][0]);
						$row['bill_ts_due']		= $row['bill_ts_due'] + (3600*24*$_CCFG['BILL_CYCLE_VAL'][0]);
						break;
					default:
						IF (is_int($_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']])) {
							$_dt_bill_dt			= dt_make_datetime_array($row['bill_ts']);
							$_dt_bill_dt['month'] 	= $_dt_bill_dt['month'] + $_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']];
							$row['bill_ts'] 		= dt_make_uts($_dt_bill_dt);
							$_dt_bill_due			= dt_make_datetime_array($row['bill_ts_due']);
							$_dt_bill_due['month'] 	= $_dt_bill_due['month'] + $_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']];
							$row['bill_ts_due']		= dt_make_uts($_dt_bill_due);
						} ELSE {
							$row['bill_ts']		= intval($row['bill_ts'] + (3600*24*(30.41667*$_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']])));
							$row['bill_ts_due']		= intval($row['bill_ts_due'] + (3600*24*(30.41667*$_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']])));
						}
						break;
				}

			# Insert copied Bill data
				$query_ni  = 'INSERT INTO '.$_DBCFG['bills'].' (';
				$query_ni .= 'bill_id, bill_status, bill_s_id';
				$query_ni .= ', bill_total_cost, bill_total_paid, bill_subtotal_cost';
				$query_ni .= ', bill_tax_01_percent, bill_tax_01_amount, bill_tax_02_percent, bill_tax_02_amount';
				$query_ni .= ', bill_tax_autocalc, bill_ts, bill_ts_due, bill_ts_paid, bill_cycle';
				$query_ni .= ', bill_recurring, bill_recurr_proc';
				$query_ni .= ')';

			#Get max / create new bill_id and set defaults
				$_max_bill_id			= do_get_max_bill_id();
				$row['bill_status']		= $_CCFG['BILL_STATUS'][4];
				$row['bill_ts_paid']	= '';
				$row['bill_total_paid']	= 0;
				$row['bill_recurr_proc']	= 0;

				$query_ni .= " VALUES ( $_max_bill_id+1".', ';
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_status'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_s_id'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_total_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_total_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_subtotal_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_tax_01_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_tax_01_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_tax_02_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_tax_02_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_tax_autocalc'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_ts'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_ts_due'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_ts_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_cycle'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_recurring'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['bill_recurr_proc'])."'";
				$query_ni .= ')';

				$result_ni 	= $db_coin->db_query_execute($query_ni) OR DIE("Unable to complete request");
				$eff_rows_ni	= $db_coin->db_query_affected_rows();
				$_ins_bill_id	= $_max_bill_id+1;
			}

		# Check for inserted $_GPV[bill_id]
			IF ($_ins_bill_id && $_ins_bill_id != 0 && $eff_rows_ni) {

			# Set Query for select and execute
				$query_pii 	 = 'SELECT * FROM '.$_DBCFG['bills_items'];
				$query_pii 	.= ' WHERE bi_bill_id='.$adata['bill_id'];
				$query_pii	.= ' ORDER BY bi_item_no ASC';

			# Do select
				$result_pii	= $db_coin->db_query_execute($query_pii);
				$numrows_pii	= $db_coin->db_query_numrows($result_pii);

			# Process query results (assumes one returned row above- need to verify)
				while ($row = $db_coin->db_fetch_array($result_pii)) {

				# Build SQL and execute.
					$query_nii	= 'INSERT INTO '.$_DBCFG['bills_items'].' (';
					$query_nii	.= 'bi_bill_id, bi_item_no, bi_item_name';
					$query_nii	.= ', bi_item_desc, bi_item_cost';
					$query_nii	.= ', bi_apply_tax_01, bi_apply_tax_02, bi_calc_tax_02_pb';
					$query_nii	.= ') VALUES (';

					$query_nii	.= "'".$db_coin->db_sanitize_data($_ins_bill_id)."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_item_no'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_item_name'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_item_desc'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_item_cost'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_apply_tax_01'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_apply_tax_02'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['bi_calc_tax_02_pb'])."'";
					$query_nii	.= ')';

					$result_nii	= $db_coin->db_query_execute($query_nii) OR DIE("Unable to complete request");
					$eff_rows_ni	= $db_coin->db_query_affected_rows();
				}
			}

		# Update Bill total cost for new Bill
			IF ($_ins_bill_id != 0) {$_ret = do_set_bill_values($_ins_bill_id, 0);}

		# Check for inserted $_GPV[bill_id]- Insert Bill Debit Transaction
			IF ($_ins_bill_id && $_ins_bill_id != 0 && $eff_rows_ni) {

			# Get Bill Total for insert to amount paid.
				$idata = do_get_bill_values($_ins_bill_id);

			# Insert Bill Debit it
				$_bt_def = 0;
				$_bt_desc	= $_LANG['_BILLS']['l_Bill_ID'].$_sp.$_ins_bill_id;
				$q_it = 'INSERT INTO '.$_DBCFG['bills_trans'].' (';
				$q_it .= 'bt_ts, bt_bill_id, bt_type';
				$q_it .= ', bt_origin, bt_desc, bt_amount';
				$q_it .= ') VALUES ( ';
				$q_it .= "'".$db_coin->db_sanitize_data($idata['bill_ts'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($idata['bill_id'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_bt_def)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_bt_def)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_bt_desc)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($idata['bill_total_cost'])."'";
				$q_it .= ')';
				$r_it = $db_coin->db_query_execute($q_it);
				$n_it = $db_coin->db_query_numrows($r_it);

			}
		}
		return $_ins_bill_id;
	}

# Do auto copy bill
function do_auto_bill_copy() {
	# Dim some Vars:
		global $_CCFG, $_ACFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'].' WHERE';

	# Uncommenting the next line will select only PAID bills for autocopying
	# To select all recurring bills regardless of status, leave the line commented out.
	# 	$query	.= ' '.$_DBCFG['bills'].".bill_status = '".$_CCFG['BILL_STATUS'][3]."' AND";
		$query	.= ' '.$_DBCFG['bills'].".bill_recurring='1'";
		$query	.= ' AND '.$_DBCFG['bills'].".bill_recurr_proc='0'";
		$query	.= ' AND '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';
		$query	.= ' AND ('.$_DBCFG['suppliers'].".s_status='active' OR ".$_DBCFG['suppliers'].".s_status = '".$_CCFG['S_STATUS'][1]."')";
		$query	.= ' ORDER BY '.$_DBCFG['bills'].'.bill_id';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		$_cnt_copied = 0;
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_new_bill = 0;
				IF (!isset($_ACFG['BILL_ACOPY_DELAY_ENABLE']))	{$_ACFG['BILL_ACOPY_DELAY_ENABLE'] = 1;}
				IF (!isset($_ACFG['BILL_ACOPY_DAYS_OUT'])) 		{$_ACFG['BILL_ACOPY_DAYS_OUT'] = 30;}

			# Delay auto copy until new bill_ts < now+days
				IF ($_ACFG['BILL_ACOPY_DELAY_ENABLE'] == 1) {

				# Calc new Bill date if copied
					switch($row['bill_cycle']) {
						case 0:
							$_bill_ts_new			= $row['bill_ts'] + (3600*24*$_CCFG['BILL_CYCLE_VAL'][0]);
							break;
						default:
							IF (is_int($_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']])) {
								$_dt_bill_dt			= dt_make_datetime_array($row['bill_ts']);
								$_dt_bill_dt['month'] 	= $_dt_bill_dt['month'] + $_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']];
								$_bill_ts_new 			= dt_make_uts($_dt_bill_dt);
							} ELSE {
								$_bill_ts_new = intval($row['bill_ts'] + (3600*24*(30.41667*$_CCFG['BILL_CYCLE_VAL'][$row['bill_cycle']])));
							}
							break;
					}

				# Calc now+days_out uts
					$_uts = dt_get_uts();
					$_uts_days_out = $_uts + ((3600*24)*$_ACFG['BILL_ACOPY_DAYS_OUT']);

				# Check and fire copy if required
					IF ($_bill_ts_new < $_uts_days_out) {

					# Call Bill Copy function
						$_cnt_copied++;
						$_new_bill = do_bill_copy($row);
					}
				} ELSE {
				# Call Bill Copy function
					$_cnt_copied++;
					$_new_bill = do_bill_copy($row);
				}

			# Call function to auto-set recurring was processed (copied)
				IF ($_new_bill > 0) {
					$_recurr_proc = 1;
					$_ret = do_set_bill_recurr_proc($row['bill_id'], $_recurr_proc);
				}
			}
		}

		return $_cnt_copied;
	}

/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>