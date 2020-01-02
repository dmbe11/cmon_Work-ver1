<?php
/**
 * Module: Orders (Administrative Functions)
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
	IF (eregi('orders_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=orders');
		exit;
	}


# Do Form for Add / Edit
function do_form_add_edit($amode, $adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_ACFG, $_TCFG, $_LANG, $_nl, $_sp;

	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_ORD']);
		$_BR = do_decode_DB16($_CCFG['ORDERS_FIELD_REQUIRE_ORD']);

	# Build mode dependent strings
		switch ($amode) {
			case "add":
				$mode_proper	= $_LANG['_ORDERS']['B_Add'];
				$mode_button	= $_LANG['_ORDERS']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_ORDERS']['B_Edit'];
				$mode_button	= $_LANG['_ORDERS']['B_Save'];
				break;
			default:
				$amode			= "add";
				$mode_proper	= $_LANG['_ORDERS']['B_Add'];
				$mode_button	= $_LANG['_ORDERS']['B_Add'];
				break;
		}

	# Build Temp Error Red Font Flag
		$_err_red_flag = '<font color="red"><b>-->></b></font>';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.' '.$_LANG['_ORDERS']['Orders_Entry'];

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
			$_cstr .= '<br><b>'.$_LANG['_ORDERS']['ORD_ERR_ERR_HDR1'].' '.$_err_red_flag.$_nl;
			$_cstr .= '<font color="red"><br>'.$_LANG['_ORDERS']['ORD_ERR_ERR_HDR2'].'</font><br><br>'.$_nl;
 			IF ($aerr_entry['ord_id']) 	{$err_str .= $_LANG['_ORDERS']['ORD_ERR_ERR01'];}
 			$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="35%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="65%" valign="top">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2" valign="top">';

	# Misc mode check for display values
		IF ($amode == 'add') {
			$adata['ord_id'] = '('.$_LANG['_ORDERS']['auto-assigned'].')'; $adata['ord_ts'] = '('.$_LANG['_ORDERS']['auto-assigned'].')';
		}

	# Do Main Form
		$_cstr .= '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="orders">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$amode.'">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="ord_id" value="'.$adata['ord_id'].'">'.$_nl;
		IF ($amode == 'edit') {
			IF (!$adata['ord_user_name_orig'])		{$adata['ord_user_name_orig'] = $adata['ord_user_name'];}
			$_cstr .= '<input type="hidden" name="ord_user_name_orig" value="'.htmlspecialchars($adata['ord_user_name_orig']).'">'.$_nl;
			IF (!$adata['ord_domain_orig'])		{$adata['ord_domain_orig'] = $adata['ord_domain'];}
			$_cstr .= '<input type="hidden" name="ord_domain_orig" value="'.htmlspecialchars($adata['ord_domain_orig']).'">'.$_nl;
			IF (!$_CCFG['DOMINS_ENABLE']) {
				$_cstr .= '<input type="hidden" name="ord_domain_action" value="5">'.$_nl;
				$_cstr .= '<input type="hidden" name="ord_domain" value="NONE">'.$_nl;
			}
		}
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

	# If adding, add note on current operation
		IF ($amode == 'add') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center_span.$_nl;
			$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_ADD_NOTE_H1'].'</b>'.$_sp.$_LANG['_ORDERS']['ORD_ADD_NOTE_L1'].$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_td_str_center_span.$_sp.'</td></tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Order_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['ord_id'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['ord_ts']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Order_DateTime'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['ord_ts'] <= 0 || $adata['ord_ts'] == '') {$adata['ord_ts'] = dt_get_uts().$_nl;}
		$_cstr .= do_datetime_edit_list('ord_ts', $adata['ord_ts'], 1).$_nl;
		IF ($aerr_entry['ord_ts']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['ord_status']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['ord_status'] == '') {$adata['ord_status'] = $_CCFG['ORD_STATUS'][4];}
		$_cstr .= do_select_list_status_order('ord_status', $adata['ord_status']).$_nl;
		IF ($aerr_entry['ord_status']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['ord_cl_id']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Client_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_clients('ord_cl_id', $adata['ord_cl_id'], 0, 0).$_nl;
		IF ($aerr_entry['ord_cl_id']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Sorry, but part of the next line is so that I do not have to maintain several versions of the single code-base
		IF ($aerr_entry['ord_domain'] || $_CCFG['DOMAINS_ENABLE'] || eregi('coinsofttechnologies.', $_SERVER['SERVER_NAME'])) {
			IF ($aerr_entry['ord_domain']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Domain_Name'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
		# Display instructions if domain name not wanted
			$_cstr .= $_LANG['_ORDERS']['ORD_P02_NO_DOMAIN'].'<br>'.$_nl;
		# Set default 'NONE';
			IF (!$adata['ord_domain']) {$adata['ord_domain'] = 'NONE';}
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_domain" SIZE=30 value="'.htmlspecialchars($adata['ord_domain']).'" maxlength="50">'.$_nl;
			$_cstr .= '<font color="red">';
			IF ($aerr_entry['err_domain_invalid']) {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR02'].'- mydom.'.do_domain_ext_valid_list(none, none, 1).$_nl;
			} ELSEIF ($aerr_entry['err_domain_exist']) {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR03'].$_nl;
			} ELSEIF ($aerr_entry['ord_domain']) {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_ERR_ERR38'];
			}
			$_cstr .= '</font>';
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
		# Set DOMAIN_IS_NEW if a whois search was done
			IF (strtolower($adata['ord_domain']) == 'none')	{$adata['ord_domain_action']	= 5;}
			IF (strtolower($adata['ord_domain']) != 'none')	{$adata['ord_domain_action']	= 1;}
			IF ($aerr_entry['ord_domain_action']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Domain_Action'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_dom_action('ord_domain_action', $adata['ord_domain_action'], 1);
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF (!$_CCFG['DEFAULT_PAYMENT_METHOD']) {
			IF ($aerr_entry['ord_vendor_id']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Vendor'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_vendors('ord_vendor_id', $adata['ord_vendor_id']).$_nl;
			IF ($aerr_entry['ord_vendor_id']) {
				$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
			} ELSEIF ($_BR['B15'] == 1) {
				$_cstr .= $_LANG['_ORDERS']['Required'];
			}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		} ELSE {
			$_cstr .= '<input type="hidden" name="ord_vendor_id" value="'.$adata['ord_vendor_id'].'">';
		}

		IF ($aerr_entry['ord_prod_id']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Product'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_products('ord_prod_id', $adata['ord_prod_id']).$_nl;
		IF ($aerr_entry['ord_prod_id']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSEIF ($_BR['B15'] == 1) {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($amode == 'edit' || $aerr_entry['ord_unit_cost']) {
			IF ($aerr_entry['ord_unit_cost']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Unit_Cost'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_unit_cost" SIZE=12 value="'.htmlspecialchars($adata['ord_unit_cost']).'">'.' (no commas)'.$_nl;
			IF ($aerr_entry['ord_unit_cost']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($adata['ord_accept_tos'] == '') {$adata['ord_accept_tos'] = 1;}
		IF ($aerr_entry['ord_accept_tos']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Accepted_TOS'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('ord_accept_tos', $adata['ord_accept_tos'], 1);
		IF ($aerr_entry['ord_accept_tos']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSEIF ($_BR['B15'] == 1) {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($adata['ord_accept_aup'] == '') {$adata['ord_accept_aup'] = 1;}
		IF ($aerr_entry['ord_accept_aup']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Accepted_AUP'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('ord_accept_aup', $adata['ord_accept_aup'], 1);
		IF ($aerr_entry['ord_accept_aup']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';
		} ELSEIF ($_BR['B15'] == 1) {
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B07'] == 1 || $_BR['B07'] == 1) {
			IF ($aerr_entry['ord_referred_by']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Referred_By'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_referred_by" SIZE=30 value="'.htmlspecialchars($adata['ord_referred_by']).'" maxlength="50">'.$_nl;
			IF ($_BR['B07'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

	# Set Optional Fields 01 thru 05
		IF ($_BV['B01'] == 1 || $_BR['B01'] == 1) {
			IF ($aerr_entry['ord_optfld_01']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_01'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= $_CCFG['ORD_TEXT_OPTFLD_01'].'<br>';
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_optfld_01" size="32" maxlength="50" value="'.htmlspecialchars($adata['ord_optfld_01']).'">'.$_nl;
			IF ($_BR['B01'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_optfld_01']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B02'] == 1 || $_BR['B02'] == 1) {
			IF ($aerr_entry['ord_optfld_02']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_02'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_optfld_02" size="32" maxlength="50" value="'.htmlspecialchars($adata['ord_optfld_02']).'">'.$_nl;
			IF ($_BR['B02'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_optfld_02']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B03'] == 1 || $_BR['B03'] == 1) {
			IF ($aerr_entry['ord_optfld_03']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_03'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_optfld_03" size="32" maxlength="50" value="'.htmlspecialchars($adata['ord_optfld_03']).'">'.$_nl;
			IF ($_BR['B03'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_optfld_03']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B04'] == 1 || $_BR['B04'] == 1) {
			IF ($aerr_entry['ord_optfld_04']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_04'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_optfld_04" size="32" maxlength="50" value="'.htmlspecialchars($adata['ord_optfld_04']).'">'.$_nl;
			IF ($_BR['B04'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_optfld_04']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B05'] == 1 || $_BR['B05'] == 1) {
			IF ($aerr_entry['ord_optfld_05']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_05'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_optfld_05" size="32" maxlength="50" value="'.htmlspecialchars($adata['ord_optfld_05']).'">'.$_nl;
			IF ($_BR['B05'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_optfld_06']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}


   # Add invoice number entry box unless invoice auto-added
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_ORDERS']['l_Invoice_ID'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($amode == 'add' && $_ACFG['ORDER_AUTO_CREATE_INVOICE']) {
			$adata['ord_invc_id'] = '('.$_LANG['_ORDERS']['auto-assigned'].')';
			$_cstr .= $adata['ord_invc_id'];
		} ELSE {
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="ord_invc_id" size="5" maxlength="11" value="'.$adata['ord_invc_id'].'">'.$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B06'] == 1 || $_BR['B06'] == 1) {
			IF ($aerr_entry['ord_comments']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Additional_Comments'].$_sp.'</b>'.$_nl;
			IF ($_BR['B06'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_comments']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<TEXTAREA class="PSML_NL" NAME="ord_comments" COLS="60" ROWS="10">'.htmlspecialchars($adata['ord_comments']).'</TEXTAREA>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ORDERS']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($amode=="edit") {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ORDERS']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

	# Build function argument text
		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=orders&mode=view', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
}


/**************************************************************
 * End Module Admin Functions
**************************************************************/

?>