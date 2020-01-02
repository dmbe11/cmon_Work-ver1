<?php
/**
 * Module: Invoices (Administrative Functions)
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
	IF (eregi('invoices_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=invoices');
		exit;
	}

/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do Form for Add / Edit
function do_form_add_edit($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;

	# Build mode dependent strings
		switch($adata['mode']) {
			case "add":
				$mode_proper	= $_LANG['_INVCS']['B_Add'];
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_INVCS']['B_Edit'];
				$mode_button	= $_LANG['_INVCS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_proper	= $_LANG['_INVCS']['B_Add'];
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_INVCS']['Invoices_Entry'].$_sp.'( <b>(*)</b>'.$_sp.$_LANG['_INVCS']['denotes_optional_items'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_INVCS']['INV_ERR_ERR_HDR1'].'<br>'.$_LANG['_INVCS']['INV_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['invc_id']) 			{$err_str .= $_LANG['_INVCS']['INV_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['invc_status']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR02']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_cl_id']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR03']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_total_cost'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR04']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_total_paid'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR04a']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_ts']) 			{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR05']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_ts_due']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR06']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_ts_paid']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR07']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_bill_cycle']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR08']; $err_prv = 1;}
		  	IF ($aerr_entry['invc_last_nag_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR13']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_pay_link']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR09']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_terms']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR10']; $err_prv = 1;}
			IF ($aerr_entry['invc_deliv_method']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR11']; $err_prv = 1;}
		 	IF ($aerr_entry['invc_total_paid'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR12']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_left_valign	= '<td class="TP1SML_NR" width="35%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';

	# Misc mode check for display values
		IF ($adata['mode'] == 'add') {$adata['invc_id'] = '('.$_LANG['_INVCS']['auto-assigned'].')';}
		IF ($adata['mode'] == 'add' && $adata['invc_ts'] == '') {$adata['invc_ts'] = dt_get_uts();}
		IF ($adata['mode'] == 'add' && $adata['invc_ts_due'] == '') {$adata['invc_ts_due'] = dt_get_uts()+($_CCFG['INVC_DUE_DAYS_OFFSET']*(24*60*60));}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="invoices">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Invoice_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['invc_id'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['invc_status'] == '') {$adata['invc_status'] = $_CCFG['INV_STATUS'][4];}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_status_invoice('invc_status', $adata['invc_status'], 0).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Delivery_Method'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['invc_deliv_method'] == '') {$adata['invc_deliv_method'] = $_CCFG['INVC_DEL_MTHD_DEFAULT'];}
		$_cstr .= do_select_list_delivery_invoice('invc_deliv_method', $adata['invc_deliv_method']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Delivered'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('invc_delivered', $adata['invc_delivered'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Last nag email
		IF ($adata['invc_last_nag_id'] == '') {$adata['invc_last_nag_id'] = 0;}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Last_Nag'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$aname	= 'invc_last_nag_id';
		$avalue	= $adata['invc_last_nag_id'];
		$_cstr .= do_select_list_last_nag($aname, $avalue, 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Client'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_clients_edit('invc_cl_id', $adata['invc_cl_id']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Invoice_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('invc_ts', $adata['invc_ts'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Date_Due'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('invc_ts_due', $adata['invc_ts_due'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Date_Paid_NReq'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_date_edit_list('invc_ts_paid', $adata['invc_ts_paid'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['invc_bill_cycle'] == '') {$adata['invc_bill_cycle'] = 1;}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Billing_Cycle'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_billing_cycle('invc_bill_cycle', $adata['invc_bill_cycle'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Recurring'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('invc_recurring', $adata['invc_recurring'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Recurring_Processed'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('invc_recurr_proc', $adata['invc_recurr_proc'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['invc_tax_autocalc'] == '') {$adata['invc_tax_autocalc'] = 1;}

		IF ($_CCFG['INVC_TAX_01_ENABLE'] == 1 || $_CCFG['INVC_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['AutoCalc_Tax'].'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['invc_tax_autocalc'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['invc_tax_autocalc'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="invc_tax_autocalc" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_01_ENABLE']) {
			IF ($adata['invc_tax_01_percent'] == '') {$adata['invc_tax_01_percent'] = do_currency_format($_CCFG['INVC_TAX_01_DEF_VAL'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['Tax_Rate'].'- '.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="invc_tax_01_percent" SIZE="6" value="'.$adata['invc_tax_01_percent'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_02_ENABLE']) {
			IF ($adata['invc_tax_02_percent'] == '') {$adata['invc_tax_02_percent'] = do_currency_format($_CCFG['INVC_TAX_02_DEF_VAL'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['Tax_Rate'].'- '.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="invc_tax_02_percent" SIZE="6" value="'.$adata['invc_tax_02_percent'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_01_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['Tax_Amount'].'- '.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="invc_tax_01_amount" SIZE="6" value="'.$adata['invc_tax_01_amount'].'">'.$_nl;
			$_cstr .= $_sp.$_LANG['_INVCS']['Tax_Amount_Manual_Calc'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['Tax_Amount'].'- '.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NR" TYPE=TEXT NAME="invc_tax_02_amount" SIZE="6" value="'.$adata['invc_tax_02_amount'].'">'.$_nl;
			$_cstr .= $_sp.$_LANG['_INVCS']['Tax_Amount_Manual_Calc'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

	# Make read-only, to avoid confusion on part of some users.
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Total_Paid'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_currency_format($adata['invc_total_paid'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_cstr .= '<INPUT TYPE=hidden NAME="invc_total_paid" SIZE="10" value="'.$adata['invc_total_paid'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_valign.'<b>'.$_LANG['_INVCS']['l_Pay_Link'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="invc_pay_link" COLS="60" ROWS="10">'.$adata['invc_pay_link'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_valign.'<b>'.$_LANG['_INVCS']['l_Terms'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="invc_terms" COLS="60" ROWS="10">'.$adata['invc_terms'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="invc_id" value="'.$adata['invc_id'].'">'.$_nl;
		IF (!$_CCFG['INVC_TAX_01_ENABLE']) {
			$_cstr .= '<INPUT TYPE=hidden name="invc_tax_01_percent" value="'.$adata['invc_tax_01_percent'].'">'.$_nl;
		}
		IF ($_CCFG['INVC_TAX_02_ENABLE'] != 1) {
			$_cstr .= '<INPUT TYPE=hidden name="invc_tax_02_percent" value="'.$adata['invc_tax_02_percent'].'">'.$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_INVCS']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_INVCS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_SEC['_sadmin_flg'] && $adata['mode'] == 'edit') {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$adata['invc_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit Items Manually
function do_form_add_edit_items($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_SERVER, $_nl, $_sp;

	# Build mode dependent strings
		switch($adata['mode']) {
			case "add":
				$mode_proper	= $_LANG['_INVCS']['B_Add'];
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_INVCS']['B_Edit'];
				$mode_button	= $_LANG['_INVCS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_proper	= $_LANG['_INVCS']['B_Add'];
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_INVCS']['Invoice_Items_Entry'];

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_INVCS']['INV_ERR_ERR_HDR1'].'<br>'.$_LANG['_INVCS']['INV_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['ii_invc_id']) 	{$err_str .= $_LANG['_INVCS']['INV_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['ii_item_no']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR16']; $err_prv = 1;}
		 	IF ($aerr_entry['ii_item_name']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR17']; $err_prv = 1;}
		 	IF ($aerr_entry['ii_item_desc'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR18']; $err_prv = 1;}
		 	IF ($aerr_entry['ii_item_cost']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR19']; $err_prv = 1;}
		 	IF ($aerr_entry['ii_prod_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERR20']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2">';

	# Set items id to invc_id
		$adata['ii_invc_id'] = $adata['invc_id'];

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="invoices">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="iitem">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center_span.$_nl;
		$_cstr .= '<b>'.$_LANG['_INVCS']['INV_ADD_ITEM_MSG_TXT01'].'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Invoice_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['ii_invc_id'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['mode'] == 'edit') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Item_No'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ii_item_no" SIZE=12 value="'.$adata['ii_item_no'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ii_item_name" SIZE=30 value="'.htmlspecialchars($adata['ii_item_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ii_item_desc" SIZE=50 value="'.htmlspecialchars($adata['ii_item_desc']).'" maxlength="75">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Item_Cost'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ii_item_cost" SIZE=12 value="'.$adata['ii_item_cost'].'">'.$_sp.'('.$_LANG['_INVCS']['no_commas'].')'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['INVC_TAX_01_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['ii_apply_tax_01'] == '') {$adata['ii_apply_tax_01'] = 1;}
			IF ($adata['ii_apply_tax_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['ii_apply_tax_01'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="ii_apply_tax_01" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_INVCS']['Apply_Tax_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['ii_apply_tax_02'] == '') {$adata['ii_apply_tax_02'] = 1;}
			IF ($adata['ii_apply_tax_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['ii_apply_tax_02'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="ii_apply_tax_02" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_INVCS']['Apply_Tax_02'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_01_ENABLE'] == 1 || $_CCFG['INVC_TAX_02_ENABLE'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['ii_calc_tax_02_pb'] == 1) {$_set = ' CHECKED'; } ELSE { $_set = ''; $adata['ii_calc_tax_02_pb'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="ii_calc_tax_02_pb" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_INVCS']['Calc_Tax_02_On_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($adata['mode'] == 'add') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center_span.$_nl;
			 IF ($adata['ii_prod_add'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="ii_prod_add" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.$_LANG['_INVCS']['INV_ADD_ITEM_MSG_TXT02'].$_nl;
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Product'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_products('ii_prod_id', $adata['ii_prod_id']).$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="invc_id" value="'.$adata['invc_id'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="ii_invc_id" value="'.$adata['ii_invc_id'].'">'.$_nl;

		IF ($adata['mode'] == 'edit') {
			IF (!$adata['ii_item_no_orig'] || $adata['ii_item_no_orig'] == 0) {$adata['ii_item_no_orig'] = $adata['ii_item_no'];}
			$_cstr .= '<input type="hidden" name="ii_item_no_orig" value="'.$adata['ii_item_no_orig'].'">'.$_nl;
		}
		IF (!$_CCFG['INVC_TAX_01_ENABLE']) {$_cstr .= '<INPUT TYPE=hidden name="ii_apply_tax_01" value="'.$adata['ii_apply_tax_01'].'">'.$_nl;}
		IF (!$_CCFG['INVC_TAX_02_ENABLE']) {$_cstr .= '<INPUT TYPE=hidden name="ii_apply_tax_02" value="'.$adata['ii_apply_tax_02'].'">'.$_nl;}
		IF (!$_CCFG['INVC_TAX_01_ENABLE'] && !$_CCFG['INVC_TAX_02_ENABLE']) {
			$_cstr .= '<INPUT TYPE=hidden name="ii_calc_tax_02_pb" value="'.$adata['ii_calc_tax_02_pb'].'">'.$_nl;
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_INVCS']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_INVCS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view&invc_id='.$adata['invc_id'], $_TCFG['_IMG_BACK_TO_INVC_M'],$_TCFG['_IMG_BACK_TO_INVC_M_MO'],'','');

	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1').'<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display items editor (for editing items)
function do_display_items_editor($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars
		global $_TCFG, $_LANG, $_nl, $_sp;

	# Build common td start tag / col strings (reduce text)
		$_td_str_center	= '<td class="TP1SML_NC">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_INVCS']['l_Invoice_ID'].$_sp.$adata['invc_id'].$_sp.$_LANG['_INVCS']['Items_Editor'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="95%">'.$_nl;
		$_cstr .= '<tr>'.$_nl.$_td_str_center.$_nl;
		$_cstr .= do_view_invoices_items($adata, '1');
		$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;
		$_cstr .= '<br>'.$_nl;

		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="95%">'.$_nl;
			$_cstr .= '<tr>'.$_nl.$_td_str_center.$_nl;
			$_cstr .= do_form_add_edit_items($adata, $aerr_entry, '1');
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
			$_cstr .= '<br>'.$_nl;
		}

		$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=edit&invc_id='.$adata['invc_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=invoices&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1').'<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit Transactions
function do_form_add_edit_trans($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_out = '';

	# Get Invoice Total for insert to amount paid.
		$idata = do_get_invc_values($adata['it_invc_id']);

	# Calc paid to date, amount remaining values
		$ptd		= do_get_invc_PTD($adata['it_invc_id']);
		$_bal_due	= $idata['invc_total_cost'] - $ptd;
		IF ($adata['mode'] == 'add') {$adata['it_amount'] = $_bal_due;}
		$_ptd_str	 = do_currency_format($idata['invc_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_ptd_str	.= ' - '.do_currency_format($ptd,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_ptd_str	.= ' = '.do_currency_format($_bal_due,1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);

	# Build mode dependent strings
		switch ($adata['mode']) {
			case "add":
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
			case "edit":
				$mode_button	= $_LANG['_INVCS']['B_Save'];
				break;
			default:
				$adata['mode']	= "add";
				$mode_button	= $_LANG['_INVCS']['B_Add'];
				break;
		}

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_INVCS']['INV_ERR_ERR_HDR1'].'<br>'.$_LANG['_INVCS']['INV_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['it_id']) 	{$err_str .= $_LANG['_INVCS']['INV_ERR_ERRxx']; $err_prv = 1;}
			IF ($aerr_entry['it_ts']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_INVCS']['INV_ERR_ERRxx']; $err_prv = 1;}
	 		$_out .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_spacer				= '<tr><td class="TP1SML_NC" colspan="2">'.$_sp.'</td></tr>';
		$_td_str_left			= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%">';
		$_td_str_center_span	= '<td class="TP3SML_NC" width="100%" colspan="2">';

/*
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="95%">'.$_nl;
			$_cstr .= '<tr>'.$_nl.$_td_str_center.$_nl;
			$_cstr .= do_form_add_edit_items($adata, $aerr_entry, '1');
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;
			$_cstr .= '<br>'.$_nl;
		}
*/
	# Do form
		$_out .= '<center>'.$_nl;
		$_out .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="mod" value="invoices">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="obj" value="trans">'.$_nl;

		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr>'.$_td_str_center_span.$_nl;
		$_out .= '<b>'.$_LANG['_INVCS']['Set_Payment_Entry_Message'].'='.$_sp.$adata['it_invc_id'].'<br>'.$_nl;
		$_out .= $_LANG['_INVCS']['Set_Payment_Entry_Message_Cont'].'</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Date'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF (!$adata['it_ts']) {$adata['it_ts'] = dt_get_uts();} # ELSE {$adata['it_ts'] = $idata['it_ts'];}
		$_out .= do_date_edit_list('it_ts', $adata['it_ts'], 1).$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		IF ($adata['it_type'] == '') {$adata['it_type'] = 2;}
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Type'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'edit' && $adata['it_type'] == '0') {
			$_out .= '<INPUT TYPE=hidden name="it_type" value="'.$adata['it_type'].'">'.$_nl;
			$_out .= $_CCFG['INV_TRANS_TYPE'][$adata['it_type']].$_nl;
		} ELSE {
			$_out .= do_select_list_trans_type('it_type', $adata['it_type']).$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		IF ($adata['it_origin'] == '') {$adata['it_origin'] = 1;}
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Origin'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'edit' && $adata['it_type'] == '0') {
			$_out .= '<INPUT TYPE=hidden name="it_origin" value="'.htmlspecialchars($adata['it_origin']).'">'.$_nl;
			$_out .= $_CCFG['INV_TRANS_ORIGIN'][$adata['it_origin']].$_nl;
		} ELSE {
			$_out .= do_select_list_trans_origin('it_origin', $adata['it_origin']).$_nl;
		}
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Description'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		$_out .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="it_desc" SIZE="30" value="'.htmlspecialchars($adata['it_desc']).'" maxlength="50">'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$adata['it_amount'] = number_format($adata['it_amount'],$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT'],'.','');
		IF ($adata['mode'] == 'edit' && $adata['it_type'] == '0') {
			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Amount'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= '<input type="hidden" name="it_amount" value="'.$adata['it_amount'].'">'.$_nl;
			$_out .= $adata['it_amount'];
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		} ELSE {
			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Amount_Due'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= $_ptd_str.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_INVCS']['l_Trans_Amount'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			$_out .= '<input class="PSML_NR" type="text" name="it_amount" SIZE="10" value="'.$adata['it_amount'].'">'.$_sp.'('.$_LANG['_INVCS']['no_commas'].')'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		}

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_sp.$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		IF ($adata['it_set_paid'] == '') {$adata['it_set_paid'] = 0;}
		IF ($adata['it_set_paid'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['it_set_paid'] = 0;}
		$_out .= '<INPUT TYPE=CHECKBOX NAME="it_set_paid" value="1"'.$_set.' border="0">'.$_nl;
		$_out .= $_sp.'<b>'.$_LANG['_INVCS']['Set_Invoice_To_Paid'].'</b>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Determine if client has an email address
		$_he = 0;
		$query	 = 'SELECT cl_email';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query	.= ' WHERE '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_id='.$adata['it_invc_id'];
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while ($row = $db_coin->db_fetch_array($result)) {IF (strtolower($row['cl_email']) != 'none') {$_he++;}}
		}

	# Show "Send Receipt" checkbox If the clietn has an email address
		IF ($_he) {
			$_out .= '<tr>'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_sp.$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$_nl;
			IF ($adata['it_email_ack'] == '') {$adata['it_email_ack'] = 0;}
			IF ($adata['it_email_ack'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['it_email_ack'] = 0;}
			$_out .= '<INPUT TYPE=CHECKBOX NAME="it_email_ack" value="1"'.$_set.' border="0">'.$_nl;
			$_out .= $_sp.'<b>'.$_LANG['_INVCS']['Send_Trans_Ack_Email'].'</b>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		}

		$_out .= $_spacer.$_nl;

		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;
		$_out .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="it_id" value="'.$adata['it_id'].'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="it_invc_id" value="'.$adata['it_invc_id'].'">'.$_nl;
		$_out .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_out .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_INVCS']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_out .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_INVCS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}

		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;
		$_out .= '</center>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do invoice delivery method select list
function do_select_list_delivery_invoice($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['INV_DELIVERY']); $i++) {
			$_out .= '<option value="'.$_CCFG['INV_DELIVERY'][$i].'"';
			IF ($_CCFG['INV_DELIVERY'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_DELIVERY'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do clients select list
function do_select_list_clients_edit($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl;

	# Set Query for select
		IF ($_CCFG['Company_In_Select']) {
			$query	= 'SELECT cl_id, cl_name_first, cl_name_last, cl_company FROM '.$_DBCFG['clients'].' ORDER BY cl_company ASC, cl_name_last ASC, cl_name_first ASC';
		} ELSE {
			$query	= 'SELECT cl_id, cl_name_first, cl_name_last, cl_user_name FROM '.$_DBCFG['clients'].' ORDER BY cl_name_last ASC, cl_name_first ASC';
		}
		$result	=  $db_coin->db_query_execute($query);
		$numrows	=  $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_INVCS']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($cl_id, $cl_name_first, $cl_name_last, $cl_user_name) =  $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$cl_id.'"';
			IF ($cl_id == $avalue) {$_out .= ' selected';}
			IF ($_CCFG['Company_In_Select']) {
				IF ($cl_user_name) {
					$_out .= '>'.$cl_user_name.' - '.$cl_name_last.', '.$cl_name_first.'</option>'.$_nl;
				} ELSE {
					$_out .= '>'.$cl_name_last.', '.$cl_name_first.'</option>'.$_nl;
				}
			} ELSE {
				$_out .= '>'.$cl_name_last.', '.$cl_name_first.' - '.$cl_user_name.'</option>'.$_nl;
			}
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do billing cycle select list
function do_select_list_billing_cycle_old($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_INVCS']['Select_Cycle'].'</option>'.$_nl;

	# Load config array and sort
		$_tmp_array = $_CCFG['INVC_BILL_CYCLE'];
		sort($_tmp_array);

	# Process query results
		FOR ($i = 0; $i < count($_tmp_array); $i++) {
			$_out .= '<option value="'.$_tmp_array[$i].'"';
			IF ($_tmp_array[$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_tmp_array[$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do transaction type select list
function do_select_list_trans_type($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i < count($_CCFG['INV_TRANS_TYPE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_TRANS_TYPE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do transaction origin select list
function do_select_list_trans_origin($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i < count($_CCFG['INV_TRANS_ORIGIN']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_TRANS_ORIGIN'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do return string from value for: Title Row with invoice action dropdown
function do_tstr_invc_action_list($atitle) {
	# Dim some Vars
		global $_CCFG, $_LANG, $_nl, $_sp;

	# Search form
		$_sform  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_sform .= '<input type="hidden" name="mod" value="invoices" />'.$_nl;
		$_sform .= '<input type="hidden" name="obj" value="invc" />'.$_nl;
	#	$_sform .= 'Please Select:'.$_sp.$_nl;
		$_sform .= '<select class="select_form" name="mode" size="1" value="Action" onchange="submit();">'.$_nl;
		$_sform .= '<option value="" selected>'.$_LANG['_INVCS']['Actions'].'</option>'.$_nl;
		$_sform .= '<option value="autoupdate">'.$_LANG['_INVCS']['Auto_Update_Status'].'</option>'.$_nl;
		$_sform .= '<option value="autoemail">'.$_LANG['_INVCS']['Auto_Email_Due'].'</option>'.$_nl;
		$_sform .= '<option value="autocopy">'.$_LANG['_INVCS']['Auto_Copy_Recurring'].'</option>'.$_nl;
		$_sform .= '<option value="autonag">'.$_LANG['_INVCS']['l_Auto_Email_OverDue'].'</option>'.$_nl;
		$_sform .= '<option value="autosoon">'.$_LANG['_INVCS']['l_Auto_Email_SoonDue'].'</option>'.$_nl;
		$_sform .= '<option value="autopay">'.$_LANG['_INVCS']['l_Auto_Parse_PayPal_Email'].'</option>'.$_nl;
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


# Do auto set invoice status
function do_auto_invoice_set_status() {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin;

	# Initialize "update" counter
		$_todo	= 0;

	# Call timestamp function
		$_uts	= dt_get_uts();

	# Set Query for select.
		$query	.= 'SELECT * FROM '.$_DBCFG['invoices'];
		$query	.= " WHERE invc_status != '".$_CCFG['INV_STATUS'][1]."'";
		$query	.= " AND invc_status != '".$_CCFG['INV_STATUS'][3]."'";
		$query	.= " AND invc_status != '".$_CCFG['INV_STATUS'][5]."'";
		$query	.= " AND invc_status != '".$_CCFG['INV_STATUS'][6]."'";
		$query	.= ' ORDER BY '.$_DBCFG['invoices'].".invc_id";

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Initialize update call flag
				$_uf = 0;

			# Auto-Generate Status
				# Status Levels:
				#	void			- ignore it
				#	draft		- being created, not done yet, do not change it.
				#	pending		- pending, set by admin after draft, done but not due / sent
				#	due			- between invoice date and due date +1
				#	overdue		- due date +1
				#	paid			- paid
				#
				#	$_CCFG['INV_STATUS'][0]		= 'due';				# For Due Invoices
				#	$_CCFG['INV_STATUS'][1]		= 'draft';			# For Draft Version of Invoice
				#	$_CCFG['INV_STATUS'][2]		= 'overdue';			# For Overdue Invoices
				#	$_CCFG['INV_STATUS'][3]		= 'paid';				# For Paid Invoices
				#	$_CCFG['INV_STATUS'][4]		= 'pending';			# For Pending (To Be Sent) Invoices
				#	$_CCFG['INV_STATUS'][5]		= 'void';				# For Void Invoices
				#	$_CCFG['INV_STATUS'][6]		= 'credit memo';		# For Credit Memos

				$_invc_status_auto 	= $row['invc_status'];
				$_due_plus_one		= $row['invc_ts_due']+(60*60*24);

				IF (($_uts < $row['invc_ts']) && ($row['invc_status'] != $_CCFG['INV_STATUS'][4]))							{$_invc_status_auto = $_CCFG['INV_STATUS'][4]; $_uf++;}
				IF (($_uts >= $row['invc_ts']) && ($_uts <= $_due_plus_one)&& ($row['invc_status'] != $_CCFG['INV_STATUS'][0]))	{$_invc_status_auto = $_CCFG['INV_STATUS'][0]; $_uf++;}
				IF (($_uts > $_due_plus_one) && ($row['invc_status'] != $_CCFG['INV_STATUS'][2]))							{$_invc_status_auto = $_CCFG['INV_STATUS'][2]; $_uf++;}
				IF (($row['invc_total_cost'] - $row['invc_total_paid'] == 0) && ($row['invc_status'] != $_CCFG['INV_STATUS'][3]))	{$_invc_status_auto = $_CCFG['INV_STATUS'][3]; $_uf++;}

			# Call function to auto-set status
				IF ($_uf) {
					$_ret = do_set_invc_status($row['invc_id'], $_invc_status_auto);
					$_todo += $_uf;
				}
			}
		}

		return $_todo;
	}


# Do auto email invoices
function do_auto_invoice_emails() {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_sp;

	# Set Query for select.
		$query_inv	.= 'SELECT * FROM '.$_DBCFG['invoices'];
		$query_inv	.= " WHERE invc_status='".$_CCFG['INV_STATUS'][0]."'";
		$query_inv	.= " AND invc_deliv_method='".$_CCFG['INV_DELIVERY'][0]."'";
		$query_inv	.= ' AND invc_delivered=0';
		$query_inv	.= ' ORDER BY invc_id';

	# Do select and return check
		$result_inv	= $db_coin->db_query_execute($query_inv);
		$numrows_inv	= $db_coin->db_query_numrows($result_inv);
		$_MTP		= array();

	# Check Return and process results
		IF ($numrows_inv) {

		# Process query results
			while ($row_inv = $db_coin->db_fetch_array($result_inv)) {

			# Call common.php function for invoice mtp data (see function for array values) / merge with current.
				$_in_info = get_mtp_invoice_info($row_inv['invc_id']);
				IF ($_in_info['numrows'] > 0) {
					$_in_info['invc_pay_link'] = str_replace('<invoice_id>', $row_inv['invc_id'], $_in_info['invc_pay_link']);
					$data_new	= array_merge($_MTP, $_in_info);
					$_MTP	= $data_new;
				} ELSE {
					$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_01_PRE'].$_sp.$row_inv['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_01_SUF'];
				}

			# Call common.php function for invoice items mtp data (see function for array values) / merge with current.
				$_ii_info = get_mtp_invcitem_info($row_inv['invc_id']);
				IF ($_ii_info['numrows'] > 0) {
					$data_new	= array_merge($_MTP, $_ii_info);
					$_MTP	= $data_new;
				} ELSE {
					$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_02_PRE'].$_sp.$row_inv['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_02_SUF'];
				}

			# Call common.php function for client mtp data (see function for array values) / merge with current.
				$_cl_info 	= get_mtp_client_info($row_inv['invc_cl_id']);
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
				IF ($_CCFG['INVC_AUTO_EMAIL_CC_ENABLE']) {
  						IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
						$mail['cc']	= $_cinfo['c_email'];
					} ELSE {
						$mail['cc']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
					}
				} ELSE {
					$mail['cc']	= '';
				}

			# Build custom email subject line
				IF ($_LANG['_INVCS']['INV_EMAIL_SUBJECT']) {
					$clientname	= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
					$balance	= $row_inv['invc_total_cost'] - $row_inv['invc_total_paid'];
					$mail['subject'] = $_LANG['_INVCS']['INV_EMAIL_SUBJECT'];
					$mail['subject'] = str_replace('%SITENAME%', $_CCFG['_PKG_NAME_SHORT'], $mail['subject']);
					$mail['subject'] = str_replace('%INV_NO%', $row_inv['invc_id'], $mail['subject']);
					$mail['subject'] = str_replace('%INV_AMT_TTL%', do_currency_format($row_inv['invc_total_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
					$mail['subject'] = str_replace('%INV_AMT_PAID%', do_currency_format($row_inv['invc_total_paid'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
					$mail['subject'] = str_replace('%AMT_BAL_DUE%', do_currency_format($balance,1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']), $mail['subject']);
					$mail['subject'] = str_replace('%DATE_DUE%', $_in_info['invc_ts_due'], $mail['subject']);
					$mail['subject'] = str_replace('%DATE_ISSUED%', $_in_info['invc_ts'], $mail['subject']);
					$mail['subject'] = str_replace('%INV_TERMS%', $row_inv['invc_terms'], $mail['subject']);
					$mail['subject'] = str_replace('%INV_STATUS%', $row_inv['invc_status'], $mail['subject']);
					$mail['subject'] = str_replace('%INV_CYCLE%', $row_inv['invc_bill_cycle'], $mail['subject']);
					$mail['subject'] = str_replace('%CLIENT_NAME%', $clientname, $mail['subject']);
				} ELSE {
					$mail['subject'] = $_CCFG['_PKG_NAME_SHORT'].$_LANG['_INVCS']['INV_EMAIL_SUBJECT_PRE'];
				}

			# Set MTP (Mail Template Parameters) array
				$_MTP['to_name']		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
				$_MTP['to_email']		= $_MTP['cl_email'];
				$_MTP['from_name']		= $_cinfo['c_name'];
				$_MTP['from_email']		= $_cinfo['c_email'];
				$_MTP['subject']		= $mail['subject'];
				$_MTP['site']			= $_CCFG['_PKG_NAME_SHORT'];
				$_MTP['invc_url']		= BASE_HREF.'mod.php?mod=invoices&mode=view&invc_id='.$row_inv['invc_id'];
				$_MTP['Company_TaxNo']	= $_UVAR['CO_INFO_10_TAXNO'];
				$_MTP['Company_Name']	= $_UVAR['CO_INFO_01_NAME'];

			# Format currency amounts
				$_MTP['invc_total_cost']		= do_currency_format($row_inv['invc_total_cost'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$_MTP['invc_total_paid']		= do_currency_format($row_inv['invc_total_paid'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$_MTP['invc_subtotal_cost']	= do_currency_format($row_inv['invc_subtotal_cost'], 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$_MTP['invc_balance']		= do_currency_format($balance, 1, 1, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$_MTP['invc_tax_01_percent']	= do_currency_format($row_inv['invc_tax_01_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);
				$_MTP['invc_tax_02_percent']	= do_currency_format($row_inv['invc_tax_02_percent'],0,0,$_CCFG['TAX_DISPLAY_DIGITS_PERCENT']);
				$_MTP['invc_tax_01_amount']	= do_currency_format($row_inv['invc_tax_01_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$_MTP['invc_tax_02_amount']	= do_currency_format($row_inv['invc_tax_02_amount'],1,1,$_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);

			# Check returned records, do not send if not 1
				$_ret = 1;
				IF (($_in_info['numrows'] > 0) && ($_ii_info['numrows'] > 0)) {

				# Load message template (processed)
					IF ($_CCFG['SINGLE_LINE_EMAIL_INVOICE_ITEMS']) {
						$mail['message'] = get_mail_template('email_invoice_copy_singleline', $_MTP);
					} ELSE {
						$mail['message'] = get_mail_template('email_invoice_copy', $_MTP);
					}

				# Call basic email function (ret=1 on error)
					$_ret = do_mail_basic($mail);

				# Set delivered
					IF (!$_ret) {do_set_invc_delivered($row_inv['invc_id'], 1);}
				}

			} # Invoice Loop
		} # Numrows on Invoice Loop

		return $numrows_inv;
	}


# Do Copy Invoice
function do_invoice_copy($adata) {
	# Dim some Vars
		global $_ACFG, $_CCFG, $_DBCFG, $db_coin, $_LANG, $_sp;

	# Check for $adata[invc_id]- will determine select string (one for edit, all for list)
		IF ($adata['invc_id'] && $adata['invc_id'] != 0) {

		# Set Query for select and execute
			$query_pi = 'SELECT * FROM '.$_DBCFG['invoices'];
			$query_pi .= " WHERE invc_id='".$adata['invc_id']."'";

		# Do select
			$result_pi	= $db_coin->db_query_execute($query_pi);
			$numrows_pi	= $db_coin->db_query_numrows($result_pi);

		# Process query results (assumes one returned row above- need to verify)
			while ($row = $db_coin->db_fetch_array($result_pi)) {

			# Calc new dates
				switch($row['invc_bill_cycle']) {
					case 0:
						$row['invc_ts']		= $row['invc_ts'] + (3600*24*$_CCFG['INVC_BILL_CYCLE_VAL'][0]);
						$row['invc_ts_due']		= $row['invc_ts_due'] + (3600*24*$_CCFG['INVC_BILL_CYCLE_VAL'][0]);
						break;
					default:
						IF (is_int($_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']])) {
								$_dt_invc_dt			= dt_make_datetime_array($row['invc_ts']);
								$_dt_invc_dt['month'] 	= $_dt_invc_dt['month'] + $_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']];
								$row['invc_ts'] 		= dt_make_uts($_dt_invc_dt);
								$_dt_invc_due			= dt_make_datetime_array($row['invc_ts_due']);
								$_dt_invc_due['month'] 	= $_dt_invc_due['month'] + $_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']];
								$row['invc_ts_due']		= dt_make_uts($_dt_invc_due);
							} ELSE {
								$row['invc_ts']		= intval($row['invc_ts'] + (3600*24*(30.41667*$_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']])));
								$row['invc_ts_due']		= intval($row['invc_ts_due'] + (3600*24*(30.41667*$_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']])));
							}
						break;
				}

			# Use default terms, or copy existing terms
				IF ($_ACFG['INVC_ACOPY_NEW_TERMS']) {
					$row['invc_terms'] = $_CCFG['INV_TERMS_DEF_LINE_01'].$_CCFG['INV_TERMS_DEF_LINE_02'].$_CCFG['INV_TERMS_DEF_LINE_03'];
				}

			# Insert copied invoice data
				$query_ni  = 'INSERT INTO '.$_DBCFG['invoices'].' (';
				$query_ni .= 'invc_id, invc_status, invc_cl_id, invc_deliv_method, invc_delivered';
				$query_ni .= ', invc_total_cost, invc_total_paid, invc_subtotal_cost';
				$query_ni .= ', invc_tax_01_percent, invc_tax_01_amount, invc_tax_02_percent, invc_tax_02_amount';
				$query_ni .= ', invc_tax_autocalc, invc_ts, invc_ts_due, invc_ts_paid, invc_bill_cycle';
				$query_ni .= ', invc_recurring, invc_recurr_proc, invc_pay_link, invc_terms';
				$query_ni .= ')';

			#Get max / create new invc_id and set defaults
				$_max_invc_id			= do_get_max_invc_id();
				$row['invc_status']		= $_CCFG['INV_STATUS'][4];
				$row['invc_ts_paid']	= '';
				$row['invc_total_paid']	= 0;
				$row['invc_delivered']	= 0;
				$row['invc_recurr_proc']	= 0;

				$query_ni .= " VALUES ( $_max_invc_id+1".', ';
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_status'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_cl_id'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_deliv_method'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_delivered'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_total_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_total_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_subtotal_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_01_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_01_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_02_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_02_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_autocalc'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts_due'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_bill_cycle'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_recurring'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_recurr_proc'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_pay_link'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_terms'])."'";
				$query_ni .= ')';

				$result_ni 	= $db_coin->db_query_execute($query_ni) OR DIE("Unable to complete request");
				$eff_rows_ni	= $db_coin->db_query_affected_rows();
				$_ins_invc_id	= $_max_invc_id+1;
			}

		# Check for inserted $_GPV[invc_id]
			IF ($_ins_invc_id && $_ins_invc_id != 0 && $eff_rows_ni) {

			# Set Query for select and execute
				$query_pii 	 = 'SELECT * FROM '.$_DBCFG['invoices_items'];
				$query_pii 	.= ' WHERE ii_invc_id='.$adata['invc_id'];
				$query_pii	.= ' ORDER BY ii_item_no ASC';

			# Do select
				$result_pii	= $db_coin->db_query_execute($query_pii);
				$numrows_pii	= $db_coin->db_query_numrows($result_pii);

			# Process query results (assumes one returned row above- need to verify)
				while ($row = $db_coin->db_fetch_array($result_pii)) {

				# Build SQL and execute.
					$query_nii	= 'INSERT INTO '.$_DBCFG['invoices_items'].' (';
					$query_nii	.= 'ii_invc_id, ii_item_no, ii_item_name';
					$query_nii	.= ', ii_item_desc, ii_item_cost';
					$query_nii	.= ', ii_apply_tax_01, ii_apply_tax_02, ii_calc_tax_02_pb';
					$query_nii	.= ') VALUES (';

					$query_nii	.= "'".$db_coin->db_sanitize_data($_ins_invc_id)."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_no'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_name'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_desc'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_cost'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_apply_tax_01'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_apply_tax_02'])."', ";
					$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_calc_tax_02_pb'])."'";
					$query_nii	.= ')';

					$result_nii	= $db_coin->db_query_execute($query_nii) OR DIE("Unable to complete request");
					$eff_rows_ni	= $db_coin->db_query_affected_rows();
				}
			}

		# Update invoice total cost for new invoice
			IF ($_ins_invc_id != 0) {$_ret = do_set_invc_values($_ins_invc_id, 0);}

		# Check for inserted $_GPV[invc_id]- Insert Invoice Debit Transaction
			IF ($_ins_invc_id && $_ins_invc_id != 0 && $eff_rows_ni) {

			# Get Invoice Total for insert to amount paid.
				$idata = do_get_invc_values($_ins_invc_id);

			# Insert Invoice Debit Transaction
				$_it_def = 0;
				$_it_desc	= $_LANG['_INVCS']['l_Invoice_ID'].$_sp.$_ins_invc_id;
				$q_it = 'INSERT INTO '.$_DBCFG['invoices_trans'].' (';
				$q_it .= 'it_ts, it_invc_id, it_type';
				$q_it .= ', it_origin, it_desc, it_amount';
				$q_it .= ') VALUES ( ';
				$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_ts'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_id'])."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($_it_desc)."', ";
				$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_total_cost'])."'";
				$q_it .= ')';
				$r_it = $db_coin->db_query_execute($q_it);
				$n_it = $db_coin->db_query_numrows($r_it);

			#########################################################################################################
			# API Output Hook:
			# APIO_trans_new: Trasaction Created hook
				$_isfunc = 'APIO_trans_new';
				IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_TRANS_NEW_ENABLE'] == 1) {
					IF (function_exists($_isfunc)) {
						$_APIO = $_isfunc($idata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
					} ELSE {
						$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
					}
				}
			#########################################################################################################
			}
		}
		return $_ins_invc_id;
	}


# Do auto copy invoice
function do_auto_invoice_copy() {
	# Dim some Vars:
		global $_CCFG, $_ACFG, $_DBCFG, $db_coin;

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'].' WHERE';
		IF (!$_ACFG['AUTOCOPY_UNPAID']) {
			$query .= ' ('.$_DBCFG['invoices'].".invc_status='".$_CCFG['INV_STATUS'][3]."'";
			$query .= ' OR '.$_DBCFG['invoices'].".invc_status='paid') AND";
		}
		$query	.= ' '.$_DBCFG['invoices'].".invc_recurring = '1'";
		$query	.= ' AND '.$_DBCFG['invoices'].".invc_recurr_proc='0'";
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND ('.$_DBCFG['clients'].".cl_status='active' OR ".$_DBCFG['clients'].".cl_status = '".$_CCFG['CL_STATUS'][1]."')";
		$query	.= ' ORDER BY '.$_DBCFG['invoices'].'.invc_id';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		$_cnt_copied = 0;
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_new_invc = 0;
				IF (!isset($_ACFG['INVC_ACOPY_DELAY_ENABLE']))	{$_ACFG['INVC_ACOPY_DELAY_ENABLE'] = 1;}
				IF (!isset($_ACFG['INVC_ACOPY_DAYS_OUT'])) 		{$_ACFG['INVC_ACOPY_DAYS_OUT'] = 30;}

			# Delay auto copy until new invc_ts < now+days
				IF ($_ACFG['INVC_ACOPY_DELAY_ENABLE'] == 1) {

				# Calc new invoice date if copied
					switch($row['invc_bill_cycle']) {
						case 0:
							$_invc_ts_new			= $row['invc_ts'] + (3600*24*$_CCFG['INVC_BILL_CYCLE_VAL'][0]);
							break;
						default:
							IF (is_int($_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']])) {
								$_dt_invc_dt			= dt_make_datetime_array($row['invc_ts']);
								$_dt_invc_dt['month'] 	= $_dt_invc_dt['month'] + $_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']];
								$_invc_ts_new 			= dt_make_uts($_dt_invc_dt);
							} ELSE {
								$_invc_ts_new = intval($row['invc_ts'] + (3600*24*(30.41667*$_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']])));
							}
							break;
					}

				# Calc now+days_out uts
					$_uts		= dt_get_uts();
					$_uts_days_out	= $_uts + (3600*24*$_ACFG['INVC_ACOPY_DAYS_OUT']);

				# Check and fire copy if required
					IF ($_invc_ts_new < $_uts_days_out) {

					# Call Invoice Copy function
						$_cnt_copied++;
						$_new_invc = do_invoice_copy($row);
					}

				} ELSE {
				# Call Invoice Copy function
					$_cnt_copied++;
					$_new_invc = do_invoice_copy($row);
				}

			# Call function to auto-set recurring was processed (copied)
				IF ($_new_invc > 0) {
					$_recurr_proc = 1;
					$_ret = do_set_invc_recurr_proc($row['invc_id'], $_recurr_proc);
				}
			}
		}

		return $_cnt_copied;
	}


# Do auto email overdue reminders
function do_auto_overdue_invoice_emails() {
	global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_UVAR, $_sp;
	$todayis		= dt_get_uts();
	$nags_sent	= 0;

// Select all invoices with status "Overdue" whose days overdue matches an active reminder, UNLESS "do not nag" is set
	$query  = 'SELECT invc_id, overdue_id, overdue_title, overdue_subject, overdue_template, overdue_cc_support FROM '.$_DBCFG['invoices'];
	$query .= ' JOIN '.$_DBCFG['reminders'];
	$query .= ' ON FLOOR(('.$todayis.'-'.$_DBCFG['invoices'].'.invc_ts_due)/86400) = '.$_DBCFG['reminders'].'.overdue_days';
	$query .= ' WHERE ('.$_DBCFG['invoices'].".invc_status='".$_CCFG['INV_STATUS'][2]."'";
	$query .= ' OR '.$_DBCFG['invoices'].".invc_status='overdue')";
	$query .= ' AND '.$_DBCFG['invoices'].".invc_deliv_method='".$_CCFG['INV_DELIVERY'][0]."'";
	$query .= ' AND '.$_DBCFG['reminders'].".overdue_active=1";
	$query .= ' AND '.$_DBCFG['invoices'].'.invc_last_nag_id != '.$_DBCFG['reminders'].'.overdue_id';
	$query .= ' AND '.$_DBCFG['invoices'].".invc_last_nag_id != '-1'";

	$result	= $db_coin->db_query_execute($query);
	$numrows	= $db_coin->db_query_numrows($result);

# If any record matched
	IF ($numrows) {

	#Loop through each overdue invoice
		while ($row = $db_coin->db_fetch_array($result)) {

		# Grab the invoice information
			$_in_info = get_mtp_invoice_info($row['invc_id']);

		# Determine client balance for ALL invoices
			$idata = do_get_invc_cl_balance($_in_info['invc_cl_id'], 0);

		# If a balance due, or if nag on credit allowed, prepare a nag
			IF ($idata['net_balance'] > 0 || !$_CCFG['NO_NAG_ON_CREDIT']) {

				$_MTP = array();
				IF ($_in_info['numrows'] > 0) {
					$data_new = array_merge($_MTP, $_in_info);
					$_MTP = $data_new;
				} ELSE {
					$_mail_error_flg = 1;
					$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_01_PRE'].$_sp.$row['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_01_SUF'];
				}

				$_cl_info = get_mtp_client_info($_MTP['invc_cl_id']);
				IF ($_cl_info['numrows'] > 0) {
					$data_new = array_merge($_MTP, $_cl_info);
					$_MTP = $data_new;
				}

				IF ($idata['net_balance'] < 0) {
					$_MTP['cl_balance'] = '-'.do_currency_format(($idata['net_balance'] * -1),1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				} ELSE {
					$_MTP['cl_balance'] = ' '.do_currency_format($idata['net_balance'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				}

				$_cinfo = get_contact_info($_CCFG['MC_ID_BILLING']);
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
	   				$mail['recip'] = $_MTP['cl_email'];
					$mail['from'] = $_cinfo['c_email'];
				} ELSE {
					$mail['recip'] = $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'].' <'.$_MTP['cl_email'].'>';
					$mail['from'] = $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
				}
				IF ($row['overdue_cc_support']) {
					$_ccinfo = get_contact_info($_CCFG['MC_ID_SUPPORT']);
     				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   						$mail['cc'] = $_ccinfo['c_email'];
					} ELSE {
						$mail['cc'] = $_CCFG['_PKG_NAME_SHORT'].'-'.$_ccinfo['c_name'].' <'.$_ccinfo['c_email'].'>';
					}
				} ELSE {
					$mail['cc'] = '';
				}
				$mail['message']		= $row['overdue_template'];
				$mail['subject']		= $_CCFG['_PKG_NAME_SHORT'].': '.$row['overdue_subject'];
				$_MTP['to_name']		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
				$_MTP['to_email']		= $_MTP['cl_email'];
				$_MTP['from_name']		= $_cinfo['c_name'];
				$_MTP['from_email']		= $_cinfo['c_email'];
				$_MTP['subject']		= $mail['subject'];
				$_MTP['site']			= $_CCFG['_PKG_NAME_SHORT'];
				$_MTP['invc_url']		= BASE_HREF.'mod.php?mod=invoices&mode=view&invc_id='.$row['invc_id'];
				$_MTP['Company_TaxNo']	= $_UVAR['CO_INFO_10_TAXNO'];
				$_MTP['Company_Name']	= $_UVAR['CO_INFO_01_NAME'];
				$_MTP['_PKG_URL_BASE']	= BASE_HREF;
				$_ret = 1;
				IF ($_in_info['numrows'] > 0) {
					$_mt = addslashes($mail['message']);
					eval("\$_mt = \"$_mt\";");
					$_mt = stripslashes($_mt);
					$mail['message'] = $_mt;
					$_ret = do_mail_basic($mail);
					IF (!$_ret) {
						$nags_sent++;
					// APPEND INVOICE TERMS FIELD TO REFLECT THIS REMINDER EMAIL
						$updatemessage  = $_in_info['invc_terms'] . "\n\n" . date("Y-m-d") . ': ' . $row['overdue_title'] . ' ' . $_LANG['_INVCS']['INV_OVERDUE_APPEND'];
						$query_inv_upd  = 'UPDATE ' . $_DBCFG['invoices'].' SET ';
						$query_inv_upd .= "invc_terms='".$db_coin->db_sanitize_data($updatemessage)."', ";
						$query_inv_upd .= "invc_last_nag_id='".$row['overdue_id']."'";
						$query_inv_upd .= " WHERE invc_id='".$row['invc_id']."'";
						$result_inv_upd = $db_coin->db_query_execute($query_inv_upd);
					}
				}
				unset($_MTP);
			}
		}
	}
	return $nags_sent;
}


# Do auto email "soon due" reminders
function do_auto_soondue_invoice_emails() {
	# Initialize some variables
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_UVAR, $_sp;
		$todayis		= dt_get_uts();
		$nags_sent	= 0;

	# Retrieve all "due" invoices that are not nag disabled and that are halfway between the issued and due dates.
		$query  = 'SELECT invc_id';
		$query .= ' FROM '.$_DBCFG['invoices'];
		$query .= " WHERE invc_status='".$_CCFG['INV_STATUS'][0]."'";
		$query .= " AND invc_deliv_method='".$_CCFG['INV_DELIVERY'][0]."'";
		$query .= " AND invc_last_nag_id != '-1'";
		$query .= ' AND FLOOR(('.$todayis.' - invc_ts) / 86400) = FLOOR(((invc_ts_due - invc_ts) / 86400) / 2)';
		$result	= $db_coin->db_query_execute($query);

	# If any invoices/nag templates matched
		IF ($db_coin->db_query_numrows($result)) {

		# Retrieve the "nag" template
			$query2  = 'SELECT overdue_subject, overdue_template, overdue_cc_support';
			$query2 .= ' FROM '.$_DBCFG['reminders'];
			$query2 .= " WHERE overdue_title='Soon Due'";
			$result2	= $db_coin->db_query_execute($query2);
			IF ($db_coin->db_query_numrows($result2)) {
				$_template = $db_coin->db_fetch_array($result2);
			}

		# Loop through each "soon due" invoice
			while ($row = $db_coin->db_fetch_array($result)) {

			# Grab the invoice information
				$_in_info = get_mtp_invoice_info($row['invc_id']);

			# Determine client balance for ALL invoices
				$idata = do_get_invc_cl_balance($_in_info['invc_cl_id'], 0);

			# If a balance due, or if nag on crdit allowed, prepare a nag
				IF ($idata['net_balance'] > 0 || !$_CCFG['NO_NAG_ON_CREDIT']) {

					$_MTP = array();
					IF ($_in_info['numrows'] > 0) {
						$data_new = array_merge($_MTP, $_in_info);
						$_MTP = $data_new;
					} ELSE {
						$_mail_error_flg = 1;
						$_mail_error_str .= '<br>'.$_LANG['_INVCS']['INV_EMAIL_MSG_01_PRE'].$_sp.$row['invc_id'].$_sp.$_LANG['_INVCS']['INV_EMAIL_MSG_01_SUF'];
					}

					$_cl_info = get_mtp_client_info($_MTP['invc_cl_id']);
					IF ($_cl_info['numrows'] > 0) {
						$data_new = array_merge($_MTP, $_cl_info);
						$_MTP = $data_new;
					}

					IF ($idata['net_balance'] < 0) {
						$_MTP['cl_balance'] = '-'.do_currency_format(($idata['net_balance'] * -1),1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
					} ELSE {
						$_MTP['cl_balance'] = ' '.do_currency_format($idata['net_balance'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
					}

					$_cinfo = get_contact_info($_CCFG['MC_ID_BILLING']);
					IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
	   					$mail['recip'] = $_MTP['cl_email'];
						$mail['from'] = $_cinfo['c_email'];
					} ELSE {
						$mail['recip'] = $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'].' <'.$_MTP['cl_email'].'>';
						$mail['from'] = $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
					}
					IF ($_template['overdue_cc_support']) {
						$_ccinfo = get_contact_info($_CCFG['MC_ID_SUPPORT']);
     					IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   							$mail['cc'] = $_ccinfo['c_email'];
						} ELSE {
							$mail['cc'] = $_CCFG['_PKG_NAME_SHORT'].'-'.$_ccinfo['c_name'].' <'.$_ccinfo['c_email'].'>';
						}
					} ELSE {
						$mail['cc'] = '';
					}
					$mail['message']		= $_template['overdue_template'];
					$mail['subject']		= $_CCFG['_PKG_NAME_SHORT'].': '.$_template['overdue_subject'];
					$_MTP['to_name']		= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
					$_MTP['to_email']		= $_MTP['cl_email'];
					$_MTP['from_name']		= $_cinfo['c_name'];
					$_MTP['from_email']		= $_cinfo['c_email'];
					$_MTP['subject']		= $mail['subject'];
					$_MTP['site']			= $_CCFG['_PKG_NAME_SHORT'];
					$_MTP['invc_url']		= BASE_HREF.'mod.php?mod=invoices&mode=view&invc_id='.$row['invc_id'];
					$_MTP['Company_TaxNo']	= $_UVAR['CO_INFO_10_TAXNO'];
					$_MTP['Company_Name']	= $_UVAR['CO_INFO_01_NAME'];
					$_MTP['_PKG_URL_BASE']	= BASE_HREF;
					$_ret = 1;
					IF ($_in_info['numrows'] > 0) {
						$_mt = addslashes($mail['message']);
						eval("\$_mt = \"$_mt\";");
						$_mt = stripslashes($_mt);
						$mail['message'] = $_mt;
						$_ret = do_mail_basic($mail);
						IF (!$_ret) {
							$nags_sent++;
						// APPEND INVOICE TERMS FIELD TO REFLECT THIS REMINDER EMAIL
							$updatemessage  = $_in_info['invc_terms'] . "\n\n" . date("Y-m-d") . ': ' . $_template['overdue_title'] . ' ' . $_LANG['_INVCS']['INV_OVERDUE_APPEND'];
							$query_inv_upd  = 'UPDATE ' . $_DBCFG['invoices'].' SET ';
							$query_inv_upd .= "invc_terms='".$db_coin->db_sanitize_data($updatemessage)."', ";
							$query_inv_upd .= "invc_last_nag_id='".$_template['overdue_id']."'";
							$query_inv_upd .= " WHERE invc_id='".$row['invc_id']."'";
							$result_inv_upd = $db_coin->db_query_execute($query_inv_upd);
						}
					}
					unset($_MTP);
				}
			}
		}

	# Return results
		return $nags_sent;
}


# Do last nag email select list
function do_select_list_last_nag($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl;

	# Start SELECT
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Do Not Nag
		$_out .= '<option value="-1"';
		IF ($avalue == '-1') {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_INVCS']['Do_Not_Nag'].'</option>'.$_nl;

	# None
		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_INVCS']['none'].'</option>'.$_nl;

	# Set Query for select.
		$query	= 'SELECT overdue_id, overdue_title FROM '.$_DBCFG['reminders'].' ORDER BY overdue_days ASC';
		$result	= $db_coin->db_query_execute($query);

	# Process query results
		while(list($overdue_id, $overdue_title) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$overdue_id.'"';
			IF ($overdue_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$overdue_title.'</option>'.$_nl;
		}

	# Close SELECT
		$_out .= '</select>'.$_nl;

	# Output results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}
/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>