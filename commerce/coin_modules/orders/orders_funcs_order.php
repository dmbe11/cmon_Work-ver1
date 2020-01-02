<?php
/**
 * Module: Orders (Order Processing Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Orders
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_orders.php
 */


# Code to handle file being loaded by URL
	IF (eregi('orders_funcs_order.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=orders');
		exit;
	}

/**************************************************************
 * Module Functions
**************************************************************/
# Do display Order Step: 01 (Stage==0)
# Order Step Screen: Product / Payment Selection and TOS/AUP Acceptance
function do_display_order_00($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_PERMS, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp, $_GPV;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ORDERS']['ORD_P01_TITLE'];

	# Add parameters "Edit" button
		IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
			$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=ordering">'.$_TCFG['_S_IMG_PM_S'].'</a>';
		}


	# Build Temp Error Red Font Flag
		$_err_prefix = '<font color="red"><b>-->> </b></font>';
		$_err_suffix = $_sp.'<font color="red"><b><<--'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</b></font>';

	# Do Main Form
		$_cstr  = '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="orders">';
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="cor_flag" value="0">'.$_nl;
		$_cstr .= '<input type="hidden" name="ord_domain" value="'.htmlspecialchars($adata['ord_domain']).'">'.$_nl;
		$_cstr .= '<input type="hidden" name="ord_referred_by" value="'.htmlspecialchars($adata['ord_referred_by']).'">'.$_nl;

		IF ($_CCFG['ORDERS_TOS_ENABLE'] != 1) {
			$_cstr .= '<input type="hidden" name="ord_accept_tos" value="0">'.$_nl;
		}
		IF ($_CCFG['ORDERS_AUP_ENABLE'] != 1) {
			$_cstr .= '<input type="hidden" name="ord_accept_aup" value="0">'.$_nl;
		}
		IF ($_CCFG['DEFAULT_PAYMENT_METHOD']) {
			$_cstr .= '<input type="hidden" name="ord_vendor_id" value="'.$_CCFG['DEFAULT_PAYMENT_METHOD'].'">';
		}

	# If WHOIS is enabled AND Domains are enabled,
	# Display "Do Whois Lookup" link OR "Ordering Domain: DOMAINNAME" text
	# depending on whether results passed in from whois or not.
		IF ($_CCFG['WHOIS_ENABLED'] && $_CCFG['DOMAINS_ENABLE']) {
			IF ($adata['ord_domain']) {
				$_cstr .= '<p><center>'.$_LANG['_ORDERS']['WHOIS_DOMAIN_NEW'].': '.htmlspecialchars($adata['ord_domain']).'</center></p>';
			} ELSE {
				$_cstr .= '<p><center><a href="mod.php?mod=whois';
				IF ($_CCFG['_FREETRIAL'])		{$_cstr .= '&free=1';}	// Support free vs paid products
				IF ($adata['ord_prod_id'])		{$_cstr .= '&ord_prod_id='.$adata['ord_prod_id'];}
				IF ($adata['ord_accept_tos'])		{$_cstr .= '&ord_accept_tos=1';}
				IF ($adata['ord_accept_aup'])		{$_cstr .= '&ord_accept_aup=1';}
				IF ($adata['ord_referred_by'])	{$_cstr .= '&ord_referred_by='.htmlspecialchars($adata['ord_referred_by']);}
				IF ($adata['ord_vendor_id'])		{$_cstr .= '&ord_vendor_id='.$adata['ord_vendor_id'];}
				IF ($adata['stage'])			{$_cstr .= '&stage=1';}
				IF ($adata['b_continue'])		{$_cstr .= '&b_continue='.htmlspecialchars($_LANG['_ORDERS']['ORD_P00_CONTINUE']);}
				$_cstr .= '">'.$_LANG['_ORDERS']['WHOIS_DOMAIN_CHECK'] . '</a></center></p>';
			}
		}
		$_cstr .= '<p>'.$_LANG['_ORDERS']['ORD_P01_TEXT01'].'</p>'.$_nl;

		$_cstr .= '<table cellpadding="5" width="100%" border="0">'.$_nl;

		IF ($adata['ord_prod_id'] == '' || $adata['ord_prod_id'] < 1) {$adata['ord_prod_id'] = 1;}
		$_cstr .= '<tr><td valign="top" width="185">'.$_nl;
		IF ($aerr_entry['ord_prod_id']) {$_cstr .= $_err_prefix;}
		$_cstr .= '<b>'.$_LANG['_ORDERS']['l_Product'].'</b>'.$_nl;
		$_cstr .= '</td><td valign="top">'.$_nl;
		$_cstr .= do_select_list_order_products('ord_prod_id', $adata['ord_prod_id'], $adata['group']).$_nl;
		IF ($aerr_entry['ord_prod_id']) {$_cstr .= $_err_suffix;}
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td colspan="2">&nbsp;</td></tr>'.$_nl;

	# Show/Hide "Payment Method" dropdown
		IF ($adata['ord_vendor_id'] == '' || $adata['ord_vendor_id'] < 1) {$adata['ord_vendor_id'] = $_CCFG['DEFAULT_PAYMENT_METHOD'];}
		IF (!$_CCFG['DEFAULT_PAYMENT_METHOD']) {
			$_cstr .= '<tr><td valign="top">'.$_nl;
			IF ($aerr_entry['ord_vendor_id']) {$_cstr .= $_err_prefix;}
			$_cstr .= '<b>'.$_LANG['_ORDERS']['l_Payment_Method'].'</b>'.$_nl;
			$_cstr .= '</td><td valign="top">'.$_nl;
			$_cstr .= do_select_list_order_vendors('ord_vendor_id', $adata['ord_vendor_id']).$_nl;
			IF ($aerr_entry['ord_vendor_id']) {$_cstr .= $_err_suffix;}
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td colspan="2">&nbsp;</td></tr>'.$_nl;
		}

		IF ($_CCFG['ORDERS_TOS_ENABLE'] == 1 || $_CCFG['ORDERS_AUP_ENABLE'] == 1) {
			$_cstr .= '<tr><td colspan="2" valign="top">'.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_P01_TEXT02'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			IF ($_CCFG['ORDERS_TOS_ENABLE'] == 1) {
				$_cstr .= '<tr><td>'.$_nl;
				IF ($aerr_entry['ord_accept_tos']) {$_cstr .= $_err_prefix;}
				$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_P01_TOS'].'</b>'.$_nl;
				$_cstr .= '</td><td>'.$_nl;
				IF ($adata['ord_accept_tos'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['ord_accept_tos'] = 0;}
				$_cstr .= '<input type="checkbox" name="ord_accept_tos" value="1"'.$_set.' border="0">'.$_nl;
				IF ($aerr_entry['ord_accept_tos']) {$_cstr .= $_err_suffix;}

			# Display TOS in iframe or just hyperlink
				IF ($_CCFG['ORDERS_TOS_IN_IFRAME']) {
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '<tr><td>&nbsp;</td><td>'.$_nl;
					$_cstr .= '<iframe src="coin_includes/show_terms.php?id='.$_CCFG['ORDER_POLICY_SI_ID_TOS'].'" width="98%" height="75" scrolling="auto" style="border: 1px solid; margin: 5px;">[Your user agent does not support frames or is currently configured not to display frames. However, you may view the <a href="coin_includes/show_terms.php?wp=XXX">information</a> by clicking the link]</iframe><br>'.$_nl;
				} ELSE {
					$_cstr .= $_sp.'<a href="coin_includes/show_terms.php?id='.$_CCFG['ORDER_POLICY_SI_ID_TOS'].'" target="_new">'.$_LANG['_ORDERS']['ORD_P01_SHOW_TOS'].'</a>';
				}
				$_cstr .= '</td></tr>'.$_nl;
			}

			IF ($_CCFG['ORDERS_AUP_ENABLE'] == 1) {
				$_cstr .= '<tr><td>'.$_nl;
				IF ($aerr_entry['ord_accept_aup']) {$_cstr .= $_err_prefix;}
				$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_P01_AUP'].'</b>'.$_nl;
				$_cstr .= '</td><td>'.$_nl;
				IF ($adata['ord_accept_aup'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['ord_accept_aup'] = 0;}
				$_cstr .= '<input type="checkbox" name="ord_accept_aup" value="1"'.$_set.' border="0">'.$_nl;
				IF ($aerr_entry['ord_accept_aup']) {$_cstr .= $_err_suffix;}


			# Display AUP in iframe or just hyperlink
				IF ($_CCFG['ORDERS_AUP_IN_IFRAME']) {
					$_cstr .= '</td></tr>'.$_nl;
					$_cstr .= '<tr><td>&nbsp;</td><td>'.$_nl;
					$_cstr .= '<iframe src="coin_includes/show_terms.php?id='.$_CCFG['ORDER_POLICY_SI_ID_AUP'].'" width="98%" height="75" scrolling="auto" style="border: 1px solid; margin: 5px;">[Your user agent does not support frames or is currently configured not to display frames. However, you may view the <a href="coin_includes/show_terms.php?wp=XXX">information</a> by clicking the link]</iframe><br>'.$_nl;
				} ELSE {
					$_cstr .= $_sp.'<a href="coin_includes/show_terms.php?id='.$_CCFG['ORDER_POLICY_SI_ID_AUP'].'" target="_new">'.$_LANG['_ORDERS']['ORD_P01_SHOW_AUP'].'</a>';
				}
				$_cstr .= '</td></tr>'.$_nl;
			}
		}
		$_cstr .= '<tr><td colspan="2">&nbsp;</td></tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;

		$_cstr .= do_input_button_class_sw('b_continue', 'SUBMIT', $_LANG['_ORDERS']['ORD_P01_CONTINUE'], 'button_form_h', 'button_form', '1').$_nl;


		IF ($_CCFG['ORDER_SHOW_IP']) {
			$_cstr .= '<p align="center"><font color="red"><b>'.$_LANG['_ORDERS']['ORD_P01_IP_NOTE'];
			IF (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
				$pos = strpos(strtolower($_SERVER['HTTP_X_FORWARDED_FOR']), '192.168.');
				IF ($pos === FALSE) {
					$_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} ELSE {
					$_ip = $_SERVER["REMOTE_ADDR"];
				}
			} ELSE {
				$_ip = $_SERVER["REMOTE_ADDR"];
			}
			$_cstr .= $_ip.'</b></font></p>'.$_nl;
		}

		IF ($_CCFG['ORDERS_COR_ENABLE'] == 1) {
			$_cstr .= '<br><hr width="50%">'.$_nl;
			$_cstr .= '<p>'.$_LANG['_ORDERS']['ORD_P01_TEXT03'].'</p>'.$_nl;
			$_cstr .= do_input_button_class_sw('b_cor', 'SUBMIT', $_LANG['_ORDERS']['ORD_P01_COR'], 'button_form_h', 'button_form', '1').$_nl;
		}

		$_cstr .= '</form>'.$_nl;


	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_MAIN']) {
			$_cstr .= '<p>'.$_LANG['_ORDERS']['ORD_FINEPRINT_MAIN'].'</p>'.$_nl;
		}

		IF ($_CCFG['ORDER_POLICY_BTTN_AUP'] == 1 && $_CCFG['ORDERS_AUP_ENABLE'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_AUP'], $_TCFG['_IMG_AUP_M'],$_TCFG['_IMG_AUP_M_MO'],'','');
		}
		IF ($_CCFG['ORDER_POLICY_BTTN_BC'] == 1) {
			$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_BC'], $_TCFG['_IMG_BAN_CODE_M'],$_TCFG['_IMG_BAN_CODE_M_MO'],'','');
		}
		IF ($_CCFG['ORDER_POLICY_BTTN_PP'] == 1) {
			$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_PP'], $_TCFG['_IMG_PRIV_POL_M'],$_TCFG['_IMG_PRIV_POL_M_MO'],'','');
		}
		IF ($_CCFG['ORDER_POLICY_BTTN_TOS'] == 1) {
			$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_TOS'], $_TCFG['_IMG_TOS_M'],$_TCFG['_IMG_TOS_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display Order Step: 02 (Stage==1)
# Order Step Screen: Custom Order Request (may be skipped)
function do_display_order_01($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_COR']);
		$_BR = do_decode_DB16($_CCFG['ORDERS_FIELD_REQUIRE_COR']);


	# Build common td start tag / col strings (reduce text)
		$_tr_spacer 		= '<tr><td class="TP1MED_NC" height="10px" width="100%">'.$_sp.'</td></tr>';
		$_td_str_center	= '<td class="TP1SML_NC" width="100%" colspan="2">';
		$_td_str_just		= '<td class="TP1SML_NJ" width="100%" colspan="2">';
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="40%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="40%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="60%">';

	# Build Temp Error Red Font Flag
		$_err_red_flag = '<font color="red"><b>-->></b></font>';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_ORDERS']['ORD_COR_TITLE'];

	# Do Main Form
		$_cstr .= '<table width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<table width="100%"  cellpadding="5" cellspacing="0">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_just.$_nl;
		$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_PCOR_TEXT01'].'</b>'.$_nl;
		$_cstr .= '<br>'.$_LANG['_ORDERS']['ORD_PCOR_TEXT02'].$_nl;
		$_cstr .= '<br><hr>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['cor_type']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Request_Type'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_cor_req_type('cor_type', $adata['cor_type'], 1);
		$_cstr .= $_LANG['_ORDERS']['Required'];
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B16'] == 1 || $_BR['B16'] == 1) {
			IF ($aerr_entry['cor_opt_bill_cycle']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Optional_Bill_Cycle'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_cor_opt_bill_cycle('cor_opt_bill_cycle', $adata['cor_opt_bill_cycle'], 1);
			IF ($_BR['B16'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF (($_BV['B15'] == 1  || $_BR['B15'] == 1) && !$_CCFG['DEFAULT_PAYMENT_METHOD']) {
			IF ($aerr_entry['cor_opt_payment']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Optional_Payment'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_cor_opt_payment('cor_opt_payment', $adata['cor_opt_payment'], 1);
			IF ($_BR['B15'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		} ELSE {
			IF ($adata['cor_opt_payment'] == '' || $adata['cor_opt_payment'] < 1) {$adata['cor_opt_payment'] = $_CCFG['DEFAULT_PAYMENT_METHOD'];}
			$_cstr .= '<input type ="hidden" name="cor_opt_payment" value="'.htmlspecialchars($adata['cor_opt_payment']).'">';
		}

		IF ($_BV['B14'] == 1 || $_BR['B14'] == 1) {
		# Set some default values
			IF (!$adata['cor_disk'] ) 		{$adata['cor_disk'] = 0;}
			IF (!$adata['cor_disk_units'] )	{$adata['cor_disk_units'] = 'Mb';}
			IF ($aerr_entry['cor_disk'])		{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Hard_Disk_Space'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_disk" size="6" maxlength="6" value="'.htmlspecialchars($adata['cor_disk']).'">'.$_nl;
			$_cstr .= $_sp.'<select class="select_form" name="cor_disk_units" size="1" value="'.htmlspecialchars($adata['cor_disk_units']).'">'.$_nl;
			$_cstr .= '<option value="Mb"';
			IF ($adata['cor_disk_units'] == 'Mb') {$_cstr .= ' selected';}
			$_cstr .= '>Mb (megabytes)</option>'.$_nl;
			$_cstr .= '<option value="Gb"';
			IF ($adata['cor_disk_units'] == 'Gb') {$_cstr .= ' selected';}
			$_cstr .= '>Gb (gigabytes)</option>'.$_nl;
			$_cstr .= '</select>'.$_nl;
			IF ($_BR['B14'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B13'] == 1 || $_BR['B13'] == 1) {
		# Set some default values
			IF (!$adata['cor_traffic']) 		{$adata['cor_traffic'] = 0;}
			IF (!$adata['cor_traffic_units']) 	{$adata['cor_traffic_units'] = 'Gb';}
			IF ($aerr_entry['cor_traffic'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Monthly_Traffic_bandwidth'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_traffic" size="6" maxlength="6" value="'.htmlspecialchars($adata['cor_traffic']).'">'.$_nl;
			$_cstr .= $_sp.'<select class="select_form" name="cor_traffic_units" size="1" value="'.htmlspecialchars($adata['cor_traffic_units']).'">'.$_nl;
			$_cstr .= '<option value="Mb"';
			IF ($adata['cor_traffic_units'] == 'Mb') {$_cstr .= ' selected';}
			$_cstr .= '>Mb (megabytes)</option>'.$_nl;
			$_cstr .= '<option value="Gb"';
			IF ($adata['cor_traffic_units'] == 'Gb') {$_cstr .= ' selected';}
			$_cstr .= '>Gb (gigabytes)</option>'.$_nl;
			$_cstr .= '</select>'.$_nl;
			IF ($_BR['B13'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B12'] == 1 || $_BR['B12'] == 1) {
		# Set some default values
			IF (!$adata['cor_dbs'] ) 		{$adata['cor_dbs'] = 0;}
			IF ($aerr_entry['cor_dbs'])		{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Databases_mysql'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_dbs" size="6" maxlength="6" value="'.htmlspecialchars($adata['cor_dbs']).'">'.$_nl;
			IF ($_BR['B12'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B11'] == 1 || $_BR['B11'] == 1) {
		# Set some default values
			IF (!$adata['cor_mailboxes']) 	{$adata['cor_mailboxes'] = 0;}
			IF ($aerr_entry['cor_mailboxes'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Mailboxes_POP'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_mailboxes" size="6" maxlength="6" value="'.htmlspecialchars($adata['cor_mailboxes']).'">'.$_nl;
			IF ($_BR['B11'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B10'] == 1 || $_BR['B10'] == 1) {
		# Set some default values
			IF (!$adata['cor_unique_ip']) 	{$adata['cor_unique_ip'] = 0;}
			IF ($aerr_entry['cor_unique_ip'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Unique_IP_Address'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_no_yes('cor_unique_ip', $adata['cor_unique_ip'], 1);
			IF ($_BR['B10'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B09'] == 1 || $_BR['B09'] == 1) {
		# Set some default values
			IF (!$adata['cor_shop_cart']) 	{$adata['cor_shop_cart'] = 0;}
			IF ($aerr_entry['cor_shop_cart'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Shopping_Cart'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_no_yes('cor_shop_cart', $adata['cor_shop_cart'], 1);
			IF ($_BR['B09'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B08'] == 1 || $_BR['B08'] == 1) {
		# Set some default values
			IF (!$adata['cor_sec_cert'])		{$adata['cor_sec_cert'] = 0;}
			IF ($aerr_entry['cor_sec_cert'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Security_Certificate'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_no_yes('cor_sec_cert', $adata['cor_sec_cert'], 1);
			IF ($_BR['B08'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B07'] == 1 || $_BR['B07'] == 1) {
		# Set some default values
			IF (!$adata['cor_site_pages']) 	{$adata['cor_site_pages'] = 0;}
			IF ($aerr_entry['cor_site_pages'])	{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Website_Authoring_pages'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_site_pages" size="6" maxlength="6" value="'.htmlspecialchars($adata['cor_site_pages']).'">'.$_nl;
			IF ($_BR['B07'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		# Set Optional Fields 01 thru 05
		IF ($_BV['B01'] == 1 || $_BR['B01'] == 1) {
			IF ($aerr_entry['cor_optfld_01'])			{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['COR_LABEL_OPTFLD_01'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_optfld_01" size="32" maxlength="50" value="'.htmlspecialchars($adata['cor_optfld_01']).'">'.$_nl;
			IF ($_BR['B01'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B02'] == 1 || $_BR['B02'] == 1) {
			IF ($aerr_entry['cor_optfld_02'])			{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['COR_LABEL_OPTFLD_02'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_optfld_02" size="32" maxlength="50" value="'.htmlspecialchars($adata['cor_optfld_02']).'">'.$_nl;
			IF ($_BR['B02'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B03'] == 1 || $_BR['B03'] == 1) {
			IF ($aerr_entry['cor_optfld_03'])			{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['COR_LABEL_OPTFLD_03'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_optfld_03" size="32" maxlength="50" value="'.htmlspecialchars($adata['cor_optfld_03']).'">'.$_nl;
			IF ($_BR['B03'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B04'] == 1 || $_BR['B04'] == 1) {
			IF ($aerr_entry['cor_optfld_04'])			{$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['COR_LABEL_OPTFLD_04'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_optfld_04" size="32" maxlength="50" value="'.htmlspecialchars($adata['cor_optfld_04']).'">'.$_nl;
			IF ($_BR['B04'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B05'] == 1 || $_BR['B05'] == 1) {
			IF ($aerr_entry['cor_optfld_05']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_CCFG['COR_LABEL_OPTFLD_05'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cor_optfld_05" size="32" maxlength="50" value="'.htmlspecialchars($adata['cor_optfld_05']).'">'.$_nl;
			IF ($_BR['B05'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		IF ($_BV['B06'] == 1 || $_BR['B06'] == 1) {
			IF ($aerr_entry['cor_comments']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Additional_Comments'].$_sp.'</b>'.$_nl;
			IF ($_BR['B06'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<TEXTAREA class="PSML_NL" NAME="cor_comments" COLS="60" ROWS="10">'.htmlspecialchars($adata['cor_comments']).'</TEXTAREA>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="mod" value="orders">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="cor_form" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="cor_flag" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_accept_tos" value="'.$adata['ord_accept_tos'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_accept_aup" value="'.$adata['ord_accept_aup'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_domain" value="'.htmlspecialchars($adata['ord_domain']).'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_referred_by" value="'.htmlspecialchars($adata['ord_referred_by']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_ccontinue', 'SUBMIT', $_LANG['_ORDERS']['ORD_COR_CONTINUE'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ORDERS']['ORD_COR_RESET'], 'button_form_h', 'button_form', '1').$_nl;

	# Uncomment the next line to "cancel" to the main order page
		// $_cstr .= do_input_button_class_sw('b_ccor', 'SUBMIT', $_LANG['_ORDERS']['ORD_COR_CCOR'], 'button_form_h', 'button_form', '1').$_nl;

	# This button will "cancel" by going one page back in the browser history
		$_cstr .= '<input name="cancel" class="button_form" type="button" value="'.$_LANG['_ORDERS']['ORD_COR_CCOR'].'" onClick="history.back();">';

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_COR']) {
			$_cstr .= $_tr_spacer.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_FINEPRINT_COR'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

	#	$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=orders&cor_flag='.$adata[cor_flag], $_TCFG['_IMG_START_OVER_M'],$_TCFG['_IMG_START_OVER_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display Order Step: 03 (Stage==2)
# Order Step Screen: Order Information- Client / Domain Info.
function do_display_order_02($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_nl, $_sp;

	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_ORD']);
		$_BR = do_decode_DB16($_CCFG['ORDERS_FIELD_REQUIRE_ORD']);

	# Check existing client login attempt
		IF ($_SEC['_suser_flg'] && !$_SEC['_suser_id'] == 0 && $adata['b_edit'] == '') {

		# Pre-Load From Database, set flag for verified existing client, disable fields, and no- dup username check

		# Set Query for select and execute
			$query	 = 'SELECT * FROM '.$_DBCFG['clients'];
			$query	.= ' WHERE cl_id='.$_SEC['_suser_id'];
			$result	 = $db_coin->db_query_execute($query);

		# Process returned record
			while ($row = $db_coin->db_fetch_array($result)) {
				$adata['ord_company']			= $row['cl_company'];
				$adata['ord_name_first']			= $row['cl_name_first'];
				$adata['ord_name_last']			= $row['cl_name_last'];
				$adata['ord_addr_01']			= $row['cl_addr_01'];
				$adata['ord_addr_02']			= $row['cl_addr_02'];
				$adata['ord_city']				= $row['cl_city'];
				$adata['ord_state_prov']			= $row['cl_state_prov'];
				$adata['ord_country']			= $row['cl_country'];
				$adata['ord_zip_code']			= $row['cl_zip_code'];
				$adata['ord_phone']				= $row['cl_phone'];
				$adata['ord_email']				= $row['cl_email'];
				$adata['ord_user_name']			= $row['cl_user_name'];
				#	$adata['ord_user_pword']		= $row['cl_user_pword']; # Do not load- encrypted
				#	$adata['ord_user_pword_re']	= $row['cl_user_pword']; # Do not load- encrypted
			}
		}

	# Build Temp Error Red Font Flag
		$_err_red_flag = '<font color="red"><b>-->> </b></font>';

	# Build common td start tag / col strings (reduce text)
		$_tr_spacer 			= '<tr><td class="TP1MED_NC" height="10px" width="100%" colspan="2">'.$_sp.'</td></tr>';
		$_td_str_left			= '<td class="TP1SML_NR" width="40%">';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="40%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="60%">';
		$_td_str_center		= '<td class="TP1SML_NC" width="100%">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_ORDERS']['ORD_P03_TITLE'];

	# If WHOIS is enabled AND Domains are enabled,
	# Display "Do Whois Lookup" link OR "Ordering Domain: DOMAINNAME" text
	# depending on whether results passed in from whois or not.
		IF ($_CCFG['WHOIS_ENABLED'] && $_CCFG['DOMAINS_ENABLE']) {
			IF ($adata['ord_domain']) {
				$_cstr .= '<br><br><center>'.$_LANG['_ORDERS']['WHOIS_DOMAIN_NEW'].': '.htmlspecialchars($adata['ord_domain']).'</center><br><br>';
			} ELSE {
				$_cstr .= '<br><br><center><a href="mod.php?mod=whois';
				IF ($_CCFG['_FREETRIAL'])		{$_cstr .= '&free=1';}	// Support free vs paid products
				IF ($adata['ord_prod_id'])		{$_cstr .= '&ord_prod_id='.$adata['ord_prod_id'];}
				IF ($adata['ord_accept_tos'])		{$_cstr .= '&ord_accept_tos=1';}
				IF ($adata['ord_accept_aup'])		{$_cstr .= '&ord_accept_aup=1';}
				IF ($adata['ord_referred_by'])	{$_cstr .= '&ord_referred_by='.htmlspecialchars($adata['ord_referred_by']);}
				IF ($adata['ord_vendor_id'])		{$_cstr .= '&ord_vendor_id='.$adata['ord_vendor_id'];}
				IF ($adata['stage'])			{$_cstr .= '&stage=2';}
				IF ($adata['b_continue'])		{$_cstr .= '&b_continue='.htmlspecialchars($_LANG['_ORDERS']['ORD_P02_CONTINUE']);}
				$_cstr .= '">'.$_LANG['_ORDERS']['WHOIS_DOMAIN_CHECK'] . '</a></center><br><br>';
			}
		}

	# Do Main Form
		$_cstr .= '<table width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center.$_nl;
		$_cstr .= $_LANG['_ORDERS']['ORD_P03_TEXT01'].'<br>'.$_nl;
		$_cstr .= $_LANG['_ORDERS']['ORD_P03_TEXT02'].'<br>'.$_nl;
		IF ($aerr_entry['flag']) {
			$_CCFG['ORDER_EDIT_CLIENT'] = 1;	// Allow editing if info is missing
			$_cstr .= '<br><b>'.$_LANG['_ORDERS']['ORD_P03_ERR_HDR'].' '.$_err_red_flag.$_nl;
			$_cstr .= '<font color="red"><br>'.$_LANG['_ORDERS']['ORD_P03_ERR06'].'</font><br><br>'.$_nl;
		}
		$_cstr .= '</td>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= '<center>'.$_nl;

		$_cstr .= '<form name="cl_info" id="cl_info" method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="orders">'.$_nl;
		$_cstr .= '<input type="hidden" name="ord_prod_id" value="'.$adata['ord_prod_id'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="ord_vendor_id" value="'.$adata['ord_vendor_id'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" cellspacing="0" width="90%">'.$_nl;

		IF ($_BV['B16'] == 1 || $_BR['B16'] == 1) {
			IF ($aerr_entry['ord_company']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Company'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_company" value="'.htmlspecialchars($adata['ord_company']).'">'.$_nl;
				$_cstr .= $adata['ord_company'].$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_company" SIZE="20" value="'.htmlspecialchars($adata['ord_company']).'" maxlength="50">'.$_nl;
				IF ($_BR['B16'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_company']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($aerr_entry['ord_name_first']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_First_Name'].'</b>'.$_sp.'</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
			$_cstr .= $adata['ord_name_first'].$_nl;
			$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_name_first" value="'.htmlspecialchars($adata['ord_name_first']).'">'.$_nl;
		} ELSE {
			$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_name_first" SIZE="20" value="'.htmlspecialchars($adata['ord_name_first']).'">'.$_nl;
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		IF ($aerr_entry['ord_name_first']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['ord_name_last']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Last_Name'].'</b>'.$_sp.'</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
			$_cstr .= $adata['ord_name_last'].$_nl;
			$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_name_last" value="'.htmlspecialchars($adata['ord_name_last']).'">'.$_nl;
		} ELSE {
			$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_name_last" SIZE="20" value="'.htmlspecialchars($adata['ord_name_last']).'">'.$_nl;
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		IF ($aerr_entry['ord_name_last']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B15'] == 1 || $_BR['B15'] == 1) {
			IF ($aerr_entry['ord_addr_01']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Address_Street_1'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_addr_01'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_addr_01" value="'.htmlspecialchars($adata['ord_addr_01']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_addr_01" SIZE=30 value="'.htmlspecialchars($adata['ord_addr_01']).'" maxlength="50">'.$_nl;
				IF ($_BR['B15'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_addr_01']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B14'] == 1 || $_BR['B14'] == 1) {
			IF ($aerr_entry['ord_addr_02']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Address_Street_2'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_addr_02'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_addr_02" value="'.htmlspecialchars($adata['ord_addr_02']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_addr_02" SIZE=30 value="'.htmlspecialchars($adata['ord_addr_02']).'" maxlength="50">'.$_nl;
				IF ($_BR['B14'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_addr_02']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B13'] == 1 || $_BR['B13'] == 1) {
			IF ($aerr_entry['ord_city']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_City'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_city'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_city" value="'.htmlspecialchars($adata['ord_city']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_city" SIZE=30 value="'.htmlspecialchars($adata['ord_city']).'" maxlength="50">'.$_nl;
				IF ($_BR['B13'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_city']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B12'] == 1 || $_BR['B12'] == 1) {
			IF ($aerr_entry['ord_state_prov']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_State_Province'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_state_prov'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_state_prov" value="'.htmlspecialchars($adata['ord_state_prov']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_state_prov" SIZE=30 value="'.htmlspecialchars($adata['ord_state_prov']).'" maxlength="50">'.$_nl;
				IF ($_BR['B12'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_state_prov']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B10'] == 1 || $_BR['B10'] == 1) {
			IF ($aerr_entry['ord_country']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Country'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_country'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_country" value="'.htmlspecialchars($adata['ord_country']).'">'.$_nl;
			} ELSE {
				$_cstr .= do_select_list_countries('ord_country', $adata['ord_country']).$_nl;
				IF ($_BR['B10'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_country']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B11'] == 1 || $_BR['B11'] == 1)	{
			IF ($aerr_entry['ord_zip_code']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Zip_Postal_Code'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_zip_code'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_zip_code" value="'.htmlspecialchars($adata['ord_zip_code']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_zip_code" SIZE=12 value="'.htmlspecialchars($adata['ord_zip_code']).'" maxlength="12">'.$_nl;
				IF ($_BR['B11'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_zip_code']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B09'] == 1 || $_BR['B09'] == 1) {
			IF ($aerr_entry['ord_phone']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Phone'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= $adata['ord_phone'].$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_phone" value="'.htmlspecialchars($adata['ord_phone']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_phone" SIZE=20 value="'.htmlspecialchars($adata['ord_phone']).'">'.$_nl;
				IF ($_BR['B09'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
			IF ($aerr_entry['ord_phone']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($aerr_entry['ord_email']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Email'].'</b>'.$_sp.'</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
			$_cstr .= $adata['ord_email'].$_nl;
			$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_email" value="'.htmlspecialchars($adata['ord_email']).'">'.$_nl;
		} ELSE {
			$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_email" SIZE=30 value="'.htmlspecialchars($adata['ord_email']).'" maxlength="50">'.$_nl;
			$_cstr .= $_LANG['_ORDERS']['Required'];
		}
		IF ($aerr_entry['ord_email']) {
			$_cstr .= '<font color="red">';
			IF ($aerr_entry['err_email_matches_another']) {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR07'];
			} ELSEIF ($aerr_entry['err_email_invalid']) {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR01'];
			} ELSE {
				$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_ERR_ERR38'];
			}
			$_cstr .= '</font>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['ord_domain']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		# Sorry, but part of the next line is so that I do not have to maintain several versions of the single code-base
			IF ($_CCFG['DOMAINS_ENABLE'] || eregi('coinsofttechnologies.', $_SERVER['SERVER_NAME'])) {
    				$_cstr .= $_tr_spacer.$_nl;
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Domain_Name'].'</b>'.$_sp.'</td>'.$_nl;
				$_cstr .= $_td_str_right.$_nl;
			# Display instructions if domain name not wanted
				$_cstr .= $_LANG['_ORDERS']['ORD_P02_NO_DOMAIN'].'<br>'.$_nl;
			# Set default 'NONE';
				IF (!$adata['ord_domain']) {$adata['ord_domain'] = 'NONE';}
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_domain" SIZE="30" value="'.htmlspecialchars($adata['ord_domain']).'" maxlength="50">'.$_nl;
				$_cstr .= $_LANG['_ORDERS']['Required'];
				IF ($aerr_entry['ord_domain']) {
					$_cstr .= '<font color="red">';
					IF ($aerr_entry['err_domain_invalid']) {
						$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR02'].'- mydom.'.do_domain_ext_valid_list(none, none, 1).$_nl;
					} ELSEIF ($aerr_entry['err_domain_exist']) {
						$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR03'].$_nl;
					} ELSE {
						$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_ERR_ERR38'];
					}
					$_cstr .= '</font>';
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			} ELSE {
	            $ord_domain = 'NONE';
			}

			$_cstr .= '<tr>'.$_nl;
			IF ($_BV['B08'] == 1 || $_BR['B08'] == 1) {
	            IF ($_CCFG['DOMAINS_ENABLE']) {

				# Set DOMAIN_IS_NEW if a whois search was done
					IF ($adata['ord_domain']) {$adata['ord_domain_action']	= 1;}

					IF ($aerr_entry['ord_domain_action']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
					$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Domain_Action'].'</b>'.$_sp.'</td>'.$_nl;
					$_cstr .= $_td_str_right.$_nl;
					$_cstr .= do_select_list_dom_action('ord_domain_action', $adata['ord_domain_action'], 1);
					IF ($_BR['B08'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
				} ELSE {
					$ord_domain_action = 1;
				}
			}
			$_cstr .= $_tr_spacer.$_nl;

			IF ($aerr_entry['ord_user_name']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_User_Name_preferred'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
		# If existing user, disable username- can't change from here
			IF ($_SEC['_suser_flg'] && !$_SEC['_suser_id'] == 0) {
				$_cstr .= $adata['ord_user_name'].'<INPUT class="PSML_NL" TYPE="hidden" NAME="ord_user_name" value="'.htmlspecialchars($adata['ord_user_name']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE="TEXT" NAME="ord_user_name" SIZE="20" value="'.htmlspecialchars($adata['ord_user_name']).'" maxlength="'.$_CCFG['CLIENT_MAX_LEN_UNAME'].'">'.$_nl;
				$_cstr .= $_LANG['_ORDERS']['Required'];
				IF ($_CCFG['Username_AlphaNum']) {$_cstr .= $_LANG['_ORDERS']['ORD_P02_UNAME_CHARS'].$_nl;}
			}
			IF ($aerr_entry['ord_user_name']) {
				$_cstr .= '<font color="red">';
				IF ($aerr_entry['err_user_name_exist']) {
					$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_P03_ERR04'].$_nl;
				} ELSEIF ($aerr_entry['err_user_name_badchars']) {
					$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_ERR_ERR39'].$_nl;
				} ELSE {
					$_cstr .= $_sp.$_LANG['_ORDERS']['ORD_ERR_ERR38'].$_nl;
				}
				$_cstr .= '</font>';
			}

			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

		# Show minimum/maximum password length notes
			$_len_prompt = str_replace('%MIN%', $_CCFG['CLIENT_MIN_LEN_PWORD'], $_LANG['_ORDERS']['ORD_P02_PWORD_LEN']);
			$_len_prompt = str_replace('%MAX%', $_CCFG['CLIENT_MAX_LEN_PWORD'], $_len_prompt);
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_len_prompt.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

		# If existing user, add note password for change only
			IF ($_SEC['_suser_flg'] && !$_SEC['_suser_id'] == 0 && $_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_center_span.$_LANG['_ORDERS']['Password_Note'].$_nl;
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

		IF ($_CCFG['ORDER_EDIT_CLIENT'] || !$_SEC['_suser_id']) {

			IF ($_CCFG['ENABLE_AUTOPASS_ORDERS']) {
				$_cstr .= do_autopassword_button('ord_user_pword', 'ord_user_pword_re', $_td_str_left, $_td_str_right);
			}

		# If existing user, add option flag- not required
			IF ($aerr_entry['err_user_pword']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Password'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<input class="PMED_NL" type="password" name="ord_user_pword" SIZE="20" value="'.htmlspecialchars($adata['ord_user_pword']).'" maxlength="'.$_CCFG['CLIENT_MAX_LEN_PWORD'].'">'.$_nl;
			IF (!$_SEC['_suser_flg'] || $_SEC['_suser_id'] == 0 || $_CCFG['ORDER_EDIT_CLIENT']) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['err_user_pword']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR19'].'</font>';}
			IF ($aerr_entry['err_pword_short']) {$_cstr .= $_sp.'<font color="red">'.str_replace('%NUM%', $_CCFG['CLIENT_MIN_LEN_PWORD'], $_LANG['_ORDERS']['ORD_ERR_ERR27']).'</font>';}
			IF ($aerr_entry['err_pword_long']) {$_cstr .= $_sp.'<font color="red">'.str_replace('%NUM%', $_CCFG['CLIENT_MAX_LEN_PWORD'], $_LANG['_ORDERS']['ORD_ERR_ERR28']).'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			IF ($adata['ord_user_pword'] && !$adata['ord_user_pword_re']) {$adata['ord_user_pword_re'] = $adata['ord_user_pword'];}
			IF ($aerr_entry['err_user_pword_re']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
		# If existing user, add option flag- not required
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Password_Confirm'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<input class="PMED_NL" type="password" name="ord_user_pword_re" SIZE="20" value="'.htmlspecialchars($adata['ord_user_pword_re']).'" maxlength="'.$_CCFG['CLIENT_MAX_LEN_PWORD'].'">'.$_nl;
			IF (!$_SEC['_suser_flg'] || $_SEC['_suser_id'] == 0 || $_CCFG['ORDER_EDIT_CLIENT']) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['err_pword_match']) {
				$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_P03_ERR05'].'</font>';
				$adata['ord_user_pword'] = '';
				$adata['ord_user_pword_re'] = '';
			}
			IF ($aerr_entry['err_user_pword_re']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR26'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B07'] == 1 || $_BR['B07'] == 1) {
			IF ($aerr_entry['ord_referred_by']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Referred_By_domain'].'</b>'.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($_SEC['_suser_id'] && !$_CCFG['ORDER_EDIT_CLIENT']) {
				$_cstr .= htmlspecialchars($adata['ord_referred_by']).$_nl;
				$_cstr .= '<INPUT TYPE="HIDDEN" NAME="ord_referred_by" value="'.htmlspecialchars($adata['ord_referred_by']).'">'.$_nl;
			} ELSE {
				$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="ord_referred_by" SIZE=30 value="'.htmlspecialchars($adata['ord_referred_by']).'" maxlength="50">'.$_nl;
				IF ($_BR['B07'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			}
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

		IF ($_BV['B06'] == 1 || $_BR['B06'] == 1) {
			IF ($aerr_entry['ord_comments']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.$_tmp.'<b>'.$_LANG['_ORDERS']['l_Additional_Comments'].$_sp.'</b>'.$_nl;
			IF ($_BR['B06'] == 1) {$_cstr .= $_LANG['_ORDERS']['Required'];}
			IF ($aerr_entry['ord_comments']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ORDERS']['ORD_ERR_ERR38'].'</font>';}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<TEXTAREA class="PSML_NL" NAME="ord_comments" COLS="60" ROWS="10">'.htmlspecialchars($adata['ord_comments']).'</TEXTAREA>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_td_str_center_span.$_sp.'</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="3">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="cor_flag" value="'.$adata['cor_flag'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_accept_tos" value="'.$adata['ord_accept_tos'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="ord_accept_aup" value="'.$adata['ord_accept_aup'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_continue', 'SUBMIT', $_LANG['_ORDERS']['ORD_P03_CONTINUE'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ORDERS']['ORD_P03_RESET'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_restart', 'SUBMIT', $_LANG['_ORDERS']['ORD_P03_START_OVER'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</center>'.$_nl;

	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_CUSTOMER_DATA']) {
			$_cstr .= $_tr_spacer.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_FINEPRINT_CUSTOMER_DATA'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display Order Step: 04 (Stage==3)
# Order Step Screen: Final Confirmation Of Data
function do_display_order_03($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags ();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get field enabled vars
		$_BV = do_decode_DB16($_CCFG['ORDERS_FIELD_ENABLE_ORD']);

	# Check if Custom Order / Regular Order- set product info as required.
		IF ($adata['cor_flag'] != 1) {
		# Get current product information
			$query	= 'SELECT prod_name, prod_desc, prod_unit_cost FROM '.$_DBCFG['products'].' WHERE prod_id='.$adata['ord_prod_id'];
			$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

			while(list($prod_name, $prod_desc, $prod_unit_cost) = $db_coin->db_fetch_row($result)) {
				$_prod_name 		= $prod_name;
				$_prod_desc 		= $prod_desc;
				$_prod_unit_cost	= $prod_unit_cost;
			}
		} ELSE {
		# Get custom order data array from orders session table
			$_cor_data = do_orders_session_select();
		}

	# Build common td start tag / col strings (reduce text)
		$_tr_spacer_span		= '<tr><td class="TP1MED_NC" height="10px" width="100%" colspan="2">'.$_sp.'</td></tr>';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="50%" valign="top">';
		$_td_str_left			= '<td class="TP1SML_NR" width="50%">';
		$_td_str_right			= '<td class="TP1SML_NL" width="50%">';
		$_td_str_center		= '<td class="TP1SML_NC" width="100%">';
		$_td_str_center_50		= '<td class="TP1SML_NC" width="50%">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_ORDERS']['ORD_P04_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="2" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= $_LANG['_ORDERS']['ORD_P04_TEXT01'].'<br>'.$_nl;
		$_cstr .= $_LANG['_ORDERS']['ORD_P04_TEXT02'].'<br><br>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="90%">'.$_nl;

	# Check product Custom / Regular
		IF ($adata['cor_flag'] != 1) {
			IF (!$_CCFG['_FREETRIAL']) {
			# Add domain setup fee (and text) to order
				$zx=0;
				IF ($adata['ord_domain_action'] == 1 && $_CCFG['DOMAIN_SETUP_FEE']) {
					$_prod_unit_cost += $_CCFG['DOMAIN_SETUP_FEE'];
					$_prod_desc .= ' (' . $_LANG['_ORDERS']['ADD_SETUP_FEE'] . ')';
					$zx++;  // We added the setup text once
				}
			# Add regular setup fee (and text) to order
				IF ($_CCFG['ORDER_SETUP_FEE']) {
					$_prod_unit_cost += $_CCFG['ORDER_SETUP_FEE'];

				// Do NOT add setup text twice)
					if (!$zx) {$_prod_desc .= ' (' . $_LANG['_ORDERS']['ADD_SETUP_FEE'] . ')';}
				}
			}

		# Display returned product information
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Product_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($_prod_name).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Description'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($_prod_desc).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Order_Cost'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.do_currency_format($_prod_unit_cost,1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

		} ELSE {
		# Display returned custom order information
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Request_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_type']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Optional_Bill_Cycle'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_opt_bill_cycle']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Optional_Payment'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_opt_payment']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			IF ($_cor_data['cor_disk'] > 0) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Hard_Disk_Space'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_disk']).$_sp.$_sp.htmlspecialchars($_cor_data['cor_disk_units']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_traffic'] > 0) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Monthly_Traffic_bandwidth'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_traffic']).$_sp.$_sp.htmlspecialchars($_cor_data['cor_traffic_units']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_dbs'] > 0) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Databases_mysql'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_dbs']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_mailboxes'] > 0) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Mailboxes_POP'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_mailboxes']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_unique_ip'] == 1) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Unique_IP_Address'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_valtostr_no_yes($_cor_data['cor_unique_ip']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_shop_cart'] == 1) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Shopping_Cart'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_valtostr_no_yes($_cor_data['cor_shop_cart']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_sec_cert'] == 1) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Security_Certificate'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.do_valtostr_no_yes($_cor_data['cor_sec_cert']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_site_pages'] > 0) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Website_Authoring_pages'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_site_pages']).$_sp.'pages</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_optfld_01']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_CCFG['COR_LABEL_OPTFLD_01'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_optfld_01']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_optfld_02']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_CCFG['COR_LABEL_OPTFLD_02'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_optfld_02']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_optfld_03']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_CCFG['COR_LABEL_OPTFLD_03'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_optfld_03']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_optfld_04']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_CCFG['COR_LABEL_OPTFLD_04'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_optfld_04']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_optfld_05']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_CCFG['COR_LABEL_OPTFLD_05'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_optfld_05']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
			IF ($_cor_data['cor_comments']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ORDERS']['l_Additional_Comments'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_cor_data['cor_comments']).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}
		}

		IF ($_BV['B16'] == 1) {
			$_cstr .= $_tr_spacer_span.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Company'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_company']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata[ord_name_first].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Last_Name'].$_sp.'</td>'.$_nl;
		$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_name_last']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B15'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Address_Street_1'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_addr_01']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B14'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Address_Street_2'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_addr_02']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B13'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_City'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_city']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B12'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_State_Province'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_state_prov']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B10'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Country'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_country']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B11'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_zip_code']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B09'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Phone'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_phone']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Email'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_email']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Support "disabled" domains
		IF ($_CCFG['DOMAINS_ENABLE']) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Domain_Name'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_domain']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		} ELSE {
			$adata['ord_domain'] = 'NONE';
		}

		IF ($_BV['B08'] == 1) {
			IF ($_CCFG['DOMAINS_ENABLE']) {
				$_cstr .= '<tr>'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Domain_Action'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($_CCFG['ORD_DOM_ACT'][$adata['ord_domain_action']]).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			} ELSE {
				$adata['ord_domain_action'] = 1;
			}
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_User_Name_preferred'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_user_name']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Password'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_user_pword']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Password_Confirm'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_user_pword_re']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_BV['B07'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ORDERS']['l_Referred_By_domain'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_referred_by']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B01'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_01'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_optfld_01']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B02'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_02'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_optfld_02']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B03'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_03'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_optfld_03']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B04'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_04'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_optfld_04']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B05'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['ORD_LABEL_OPTFLD_05'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.htmlspecialchars($adata['ord_optfld_05']).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_BV['B06'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ORDERS']['l_Additional_Comments'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.nl2br(htmlspecialchars($adata['ord_comments'])).'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= $_tr_spacer_span.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center_span.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=orders">'.$_nl;
		$_cstr .= '<INPUT TYPE="hidden" name="stage" value="4">'.$_nl;
		$_cstr .= '<INPUT TYPE="hidden" name="cor_flag" value="'.$adata['cor_flag'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE="hidden" name="ord_accept_tos" value="'.$adata['ord_accept_tos'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE="hidden" name="ord_accept_aup" value="'.$adata['ord_accept_aup'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_continue', 'SUBMIT', $_LANG['_ORDERS']['ORD_P04_CONTINUE'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $_LANG['_ORDERS']['ORD_P04_EDIT_INFO'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_restart', 'SUBMIT', $_LANG['_ORDERS']['ORD_P04_START_OVER'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_CONFIRM']) {
			$_cstr .= $_tr_spacer.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_FINEPRINT_CONFIRM'];
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display Order Step: 05 (Stage==4)
# Order Step: Record Order and Display Pay Link
function do_display_order_04($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do Process Order
		$_order_button = do_process_order($adata);

	# Build common td start tag / col strings (reduce text)
		$_td_str_left		= '<td class="TP1SML_NR" width="100%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="100%">';
		$_td_str_center	= '<td class="TP1SML_NC" width="100%">';

	# Check product Custom / Regular
		IF ($adata['cor_flag'] != 1) {

		# Build Title String, Content String, and Footer Menu String for normal order
			$_tstr .= $_LANG['_ORDERS']['ORD_P05_TITLE_NORMAL'];

			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="90%">'.$_nl;
			$_cstr .= '<tr>'.$_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_P05_TEXT01'].'<br>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr>'.$_td_str_right.$_nl;
			$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_P05_LI_HDR'].'</b><br>'.$_nl;
			$_cstr .= '<ul>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_01'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_02'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_03'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_04'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_05'].'<br><br>'.$_nl;
			$_cstr .= '</ul><br><br>'.$_nl;
		} ELSE {

		# Build Title String, Content String, and Footer Menu String for COR
			$_tstr .= $_LANG['_ORDERS']['ORD_P05_TITLE_COR'];

			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="90%">'.$_nl;
			$_cstr .= '<tr>'.$_td_str_right.$_nl;
			$_cstr .= '<b>'.$_LANG['_ORDERS']['ORD_P05_LI_HDR'].'</b><br>'.$_nl;
			$_cstr .= '<ul>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_01_COR'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_02_COR'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_03_COR'].'<br><br>'.$_nl;
			$_cstr .= '<li>'.$_LANG['_ORDERS']['ORD_P05_LI_04_COR'].'<br><br>'.$_nl;
			$_cstr .= '</ul><br><br>'.$_nl;
		}

		$_cstr .= $_order_button.'<br>'.$_nl;

	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_PAYLINK']) {
            $_cstr .= '</td></tr>';
			$_cstr .= $_tr_spacer.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_FINEPRINT_PAYLINK'];
		}

		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr .= $_sp.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display Order Return
function do_display_order_return($adata, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_ACFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_ret_flag = 'Unknown';

	# Set a session var to prevent re-processing on refresh
		IF (!isset($_SESSION['pyt_paid'])) {$_SESSION['pyt_paid'] = 0;}	// set session to 0 if it doesn't exist
		$_SESSION['pyt_paid']++;									// add one to the session

	# Process the payment IF page was not refreshed
		IF ($_SESSION['pyt_paid'] < 2) {

		# Merge Data Array with session data
			$data_sess	= do_orders_session_select();
			$adata_new	= array_merge( $adata, $data_sess );
			$adata		= $adata_new;

		# Get vendor info
		# Set Query for select.
			IF ($adata['ord_vendor_id'] > 0) {
				$query_v	 = 'SELECT * FROM '.$_DBCFG['vendors'];
				$query_v	.= ' WHERE vendor_id='.$adata['ord_vendor_id'];
				$query_v	.= ' ORDER BY vendor_id ASC';

			# Do select
				$result_v		= $db_coin->db_query_execute($query_v);
				$numrows_v	= $db_coin->db_query_numrows($result_v);

			# Process query results (assumes one returned row above)
				IF ($numrows_v) {while($row = $db_coin->db_fetch_array($result_v)) {$vdata = $row;}}
			}

		# Get product info
		# Set Query for select.
			IF ($adata['ord_prod_id'] > 0) {
				$query_p	 = 'SELECT * FROM '.$_DBCFG['products'];
				$query_p	.= ' WHERE prod_id='.$adata['ord_prod_id'];
				$query_p	.= ' ORDER BY prod_id ASC';

			# Do select
				$result_p		= $db_coin->db_query_execute($query_p);
				$numrows_p	= $db_coin->db_query_numrows($result_p);

			# Process query results (assumes one returned row above)
				IF ($numrows_p) {while($row = $db_coin->db_fetch_array($result_p)) {$pdata = $row;}}
			}

		# Append "payment flag" info if this vendor is a CallBack vendor
			IF ($adata['os_ord_cbflag'] && $adata['ord_prod_id'] > 0) {
				IF ($adata['os_ord_cbpaid']) {
					$adata[$vdata['vendor_buy_parm']] = $vdata['vendor_buy_parm_val'];
				} ELSE {
					$adata[$vdata['vendor_buy_parm']] = '';
				}
			}

		# Check return parameters for "buy" indicator.
			IF (($adata[$vdata['vendor_buy_parm']] == $vdata['vendor_buy_parm_val']) && $adata['ord_prod_id'] > 0) {
				$_ret_result = $_LANG['_ORDERS']['ORD_P06_EMAIL_YES'];
				$_ret_flag = 'Yes';
				IF (!$adata['ord_id']) {$adata['ord_id'] = $adata['os_ord_id'];}
				IF ($_CCFG['ORDER_AUTO_CREATE_INVOICE'] && $adata['ord_id'] && !$vdata['vendor_use_ipn']) {
					$tempvar = do_auto_create_payment($adata['ord_id']);
				}
			} ELSE {
				$_ret_result	= $_LANG['_ORDERS']['ORD_P06_EMAIL_NO'];
				$_ret_flag	= 'No';
			}

		# Send email that client returned from billing vendor
			IF ($_CCFG['ENABLE_EMAIL_ORDER_RET'] == 1 && $adata['ord_ret_processed'] != 1 && $adata['ord_prod_id'] > 0) {

			# Get contact information array
				$_cinfo = get_contact_info($_CCFG['MC_ID_ORDERS']);

			# Set eMail Parameters (no template used)
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   					$mail['recip']	= $_cinfo['c_email'];
					$mail['from']	= $_cinfo['c_email'];
				} ELSE {
					$mail['recip']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
					$mail['from']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
				}
				$mail['subject']	 = $_CCFG['_PKG_NAME_SHORT'].$_LANG['_ORDERS']['ORD_P06_EMAIL_01'];
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_02'].$_nl;
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_03'].$adata['ord_id'].$_nl;
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_04'].$vdata['vendor_name'].$_nl;
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_05'].$pdata['prod_name'].$_nl;
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_06'].$pdata['prod_desc'].$_nl;
				$mail['message']	.= $_LANG['_ORDERS']['ORD_P06_EMAIL_07'].$_ret_result.$_nl;

			# Call basic email function (ret=0 on error)
				$_ret = do_mail_basic($mail);
			}

		# Send ord ack email
			IF ($_CCFG['ORDERS_ACK_EMAIL_ENABLE'] == 1 && $_CCFG['ORDERS_ACK_EMAIL_ONRET'] == 1 && $adata['ord_ret_processed'] != 1 && $_ret_flag == 'Yes' && $adata['ord_id'] > 0) {

			# Call email order ack function (ret=0 on error)
				$adata['template'] = 'email_order_ack';
				$_ret = do_mail_order($adata, '1').$_nl;
			}

		# Set Result String
			IF ($_ret_flag == 'Yes') {
				$_ret_result_str = $_LANG['_ORDERS']['ORD_P06_TEXT_BUY'].$_nl;
			} ELSEIF ($_ret_flag == 'No') {
				$_ret_result_str = $_LANG['_ORDERS']['ORD_P06_TEXT_CANCEL'].$_nl;
			} ELSEIF ($_ret_flag == 'Unknown') {
				$_ret_result_str = $_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN'].$_nl;
			}

		# Search and replace parameters in result string ~ all paylink variables except invoice_id are available
			IF ($adata['ord_prod_id'] > 0) {$_ret_result_str = do_parse_paylink($adata, $_ret_result_str, 1);}

		# Set return processed if not
			IF ($adata['ord_ret_processed'] != 1 && $adata['ord_prod_id'] > 0) {
				$adata['os_ord_ret_processed'] = 1;
				do_orders_session_set_ret_proc($adata);
			}

		#########################################################################################################
		# API Output Hook:
		# APIO_order_ret_proc: Order return first pass hook
			$_isfunc = 'APIO_order_ret_proc';
			IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_RET_PROC_ENABLE'] == 1 && $adata['ord_prod_id'] > 0) {
				IF (function_exists($_isfunc)) {
					$_APIO = $_isfunc($adata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
				} ELSE {
					$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
				}
			}
		#########################################################################################################

	# End of processing payment only ONCE
		}

	# Build common td start tag / col strings (reduce text)
		$_tr_spacer 		= '<tr><td class="TP1SML_NC" height="10px" width="100%">'.$_sp.'</td></tr>';
		$_td_str_center	= '<td class="TP1SML_NC" width="100%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ORDERS']['ORD_P06_TITLE_RETURN'];

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center.$_nl;

		$_cstr .= $_LANG['_ORDERS']['ORD_P06_TEXT01'].'<br>'.$_nl;

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= $_tr_spacer.$_nl;
		$_cstr .= '<tr>'.$_td_str_center.$_nl;

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="80%">'.$_nl;
		$_cstr .= '<tr>'.$_td_str_center.$_nl;
		$_cstr .= $_ret_result_str;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

	# Display "Fine Print", if any
		IF ($_LANG['_ORDERS']['ORD_FINEPRINT_RETURN']) {
            $_cstr .= '</td></tr>';
			$_cstr .= $_tr_spacer.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_center.$_nl;
			$_cstr .= $_LANG['_ORDERS']['ORD_FINEPRINT_RETURN'];
		}

		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

	# Append API results
		$_cstr .= $_APIO_ret;
		$_mstr = '';

		IF ($_CCFG['ORDER_POLICY_BTTN_AUP'] == 1 && $_CCFG['ORDERS_AUP_ENABLE'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_AUP'], $_TCFG['_IMG_AUP_M'],$_TCFG['_IMG_AUP_M_MO'],'','');
		}

		IF ($_CCFG['ORDER_POLICY_BTTN_BC'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_BC'], $_TCFG['_IMG_BAN_CODE_M'],$_TCFG['_IMG_BAN_CODE_M_MO'],'','');
		}

		IF ($_CCFG['ORDER_POLICY_BTTN_PP'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_PP'], $_TCFG['_IMG_PRIV_POL_M'],$_TCFG['_IMG_PRIV_POL_M_MO'],'','');
		}

		IF ($_CCFG['ORDER_POLICY_BTTN_TOS'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=siteinfo&id='.$_CCFG['ORDER_POLICY_SI_ID_TOS'], $_TCFG['_IMG_TOS_M'],$_TCFG['_IMG_TOS_M_MO'],'','');
		}

	# Call block it function
		$_out = do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Process Order into database
function do_process_order($adata) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim globals
		global $_CCFG, $_ACFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Call timestamp function
		$_uts = dt_get_uts();

	# Do Process Order (fields shown for clarity)
		$sdata = do_orders_session_qet_proc();
		#	$sdata['numrows']			= $numrows;
		#	$sdata['ord_processed']		= $row['os_ord_processed'];
		#	$sdata['ord__ret_processed']	= $row['os_ord_ret_processed'];
		#	$sdata['ord_id']			= $row['os_ord_id'];
		#	$sdata['ord_vendor_id']		= $row['os_ord_vendor_id'];
		#	$sdata['ord_prod_id']		= $row['os_ord_prod_id'];

	# Do insert / update on Clients Table if order not processed
		IF ($sdata['ord_processed'] != 1) {

		# Generate encrypted password
			$ord_user_pword_crypt = do_password_crypt($adata['ord_user_pword']);

			IF ($_SEC['_suser_flg'] && !$_SEC['_suser_id'] == 0) {

			# Do update
				$query_cl  = 'UPDATE '.$_DBCFG['clients'].' SET ';
				$query_cl .= "cl_company='".$db_coin->db_sanitize_data($adata['ord_company'])."', ";
				$query_cl .= "cl_name_first='".$db_coin->db_sanitize_data($adata['ord_name_first'])."', ";
				$query_cl .= "cl_name_last='".$db_coin->db_sanitize_data($adata['ord_name_last'])."', ";
				$query_cl .= "cl_addr_01='".$db_coin->db_sanitize_data($adata['ord_addr_01'])."', ";
				$query_cl .= "cl_addr_02='".$db_coin->db_sanitize_data($adata['ord_addr_02'])."', ";
				$query_cl .= "cl_city='".$db_coin->db_sanitize_data($adata['ord_city'])."', ";
				$query_cl .= "cl_state_prov='".$db_coin->db_sanitize_data($adata['ord_state_prov'])."', ";
				$query_cl .= "cl_country='".$db_coin->db_sanitize_data($adata['ord_country'])."', ";
				$query_cl .= "cl_zip_code='".$db_coin->db_sanitize_data($adata['ord_zip_code'])."', ";
				$query_cl .= "cl_phone='".$db_coin->db_sanitize_data($adata['ord_phone'])."', ";
				$query_cl .= "cl_email='".$db_coin->db_sanitize_data($adata['ord_email'])."', ";
				$query_cl .= "cl_user_name='".$db_coin->db_sanitize_data($adata['ord_user_name'])."'";
				IF ($adata['ord_user_pword']) {
					$query_cl .= ", cl_user_pword='".$db_coin->db_sanitize_data($ord_user_pword_crypt)."'";
				}
				$query_cl .= " WHERE cl_id='".$_SEC['_suser_id']."'";

				$result_cl		= $db_coin->db_query_execute($query_cl) OR DIE("Unable to complete request");
				$_ord_cl_id		= $_SEC['_suser_id'];
				$adata['cl_id']	= $_ord_cl_id;
			} ELSE {

			# Do insert of new client
				$query_cl  = 'INSERT INTO '.$_DBCFG['clients'].' (cl_id';
				$query_cl .= ', cl_join_ts, cl_status, cl_company, cl_name_first, cl_name_last';
				$query_cl .= ', cl_addr_01, cl_addr_02, cl_city, cl_state_prov';
				$query_cl .= ', cl_country, cl_zip_code, cl_phone, cl_email';
				$query_cl .= ', cl_user_name, cl_user_pword';
				$query_cl .= ')';

			# Get max / create new cl_id
				$_max_cl_id = do_get_max_cl_id();

				$query_cl .= " VALUES ($_max_cl_id+1, ";
				$query_cl .= "'".$db_coin->db_sanitize_data($_uts)."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($_CCFG['CLIENT_DEF_STATUS_NEW'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_company'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_name_first'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_name_last'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_addr_01'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_addr_02'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_city'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_state_prov'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_country'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_zip_code'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_phone'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_email'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($adata['ord_user_name'])."', ";
				$query_cl .= "'".$db_coin->db_sanitize_data($ord_user_pword_crypt)."'";
				$query_cl .= ')';

				$result_cl 		= $db_coin->db_query_execute($query_cl) OR DIE("Unable to complete request");
				$_ord_cl_id		= $_max_cl_id+1;
				$adata['cl_id']	= $_ord_cl_id;

			#########################################################################################################
			# API Output Hook:
			# APIO_order_new_client: Order new client hook
				$_isfunc = 'APIO_order_new_client';
				IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_NEW_CLIENT_ENABLE'] == 1) {
					IF (function_exists($_isfunc)) {
						$_APIO = $_isfunc($adata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
					} ELSE {
						$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
					}
				}
			#########################################################################################################
			}

		} ELSE { # End not processed

		# Merge Data Array with session data
			$data_sess	= do_orders_session_select();
			$adata_new	= array_merge($adata, $data_sess);
			$adata		= $adata_new;

		# Set ord client id for code below
			$_ord_cl_id	= $adata['ord_cl_id'];
		} # End processed

		/*************************************************************************************************************
		*
		* Notes: Custom Order Request (COR) vs. Regular Product
		* The concept of custom order request is not to actually order the product, but more of a quote at this point.
		* Therefore- when doing COR the following occur:
		*	- A client ID is created, if not logged in a client, as we need information about the client.
		*	- The COR information is sent via email to the site admin and to client (??)
		*	- NO other data is inserted- domain, order records, etc.
		*
		************************************************************************************************************/

		# Check product Custom / Regular
			IF ($adata['cor_flag'] != 1) {

			# Get vendor info
			# Set Query for select.
				$query_v	 = 'SELECT * FROM '.$_DBCFG['vendors'];
				$query_v	.= ' WHERE vendor_id='.$adata['ord_vendor_id'];
				$query_v	.= ' ORDER BY vendor_id ASC';

			# Do select
				$result_v		= $db_coin->db_query_execute($query_v);
				$numrows_v	= $db_coin->db_query_numrows($result_v);

			# Process query results (assumes one returned row above)
				IF ($numrows_v) {while($row = $db_coin->db_fetch_array($result_v)) {$vdata = $row;}}

			# Get product info (and default allowances if added-)
			# Set Query for select.
				$query_p	= 'SELECT * FROM '.$_DBCFG['products'];
				$query_p	.= ' WHERE prod_id='.$adata['ord_prod_id'];
				$query_p	.= ' ORDER BY prod_id ASC';

			# Do select
				$result_p		= $db_coin->db_query_execute($query_p);
				$numrows_p	= $db_coin->db_query_numrows($result_p);

			# Process query results (assumes one returned row above)
				IF ($numrows_p) {
					while ($row = $db_coin->db_fetch_array($result_p)) {
						$pdata						= $row;
						$adata['prod_id'] 				= $row['prod_id'];
						$adata['prod_name'] 			= $row['prod_name'];
						$adata['prod_desc'] 			= $row['prod_desc'];
						$adata['prod_unit_cost'] 		= $row['prod_unit_cost'];

					# For auto-invoices
						$adata['prod_cost']	 			= $row['prod_unit_cost'];
						$adata['prod_apply_tax_01'] 		= $row['prod_apply_tax_01'];
						$adata['prod_apply_tax_02'] 		= $row['prod_apply_tax_02'];
						$adata['prod_calc_tax_02_pb']		= $row['prod_calc_tax_02_pb'];

						$adata['ord_unit_cost'] 			= $row['prod_unit_cost'];
						$adata['prod_dom_type'] 			= $row['prod_dom_type'];
						$adata['prod_allow_domains'] 		= $row['prod_allow_domains'];
						$adata['prod_allow_subdomains'] 	= $row['prod_allow_subdomains'];
						$adata['prod_allow_disk_space_mb']	= $row['prod_allow_disk_space_mb'];
						$adata['prod_allow_traffic_mb']	= $row['prod_allow_traffic_mb'];
						$adata['prod_allow_mailboxes']	= $row['prod_allow_mailboxes'];
						$adata['prod_allow_databases']	= $row['prod_allow_databases'];

						IF (!$_CCFG['_FREETRIAL']) {
						# Add domain setup fee (and text) to order
							$zx=0;
							IF ($adata['ord_domain_action'] == 1 && $_CCFG['DOMAIN_SETUP_FEE']) {
								$adata['ord_unit_cost']	+= $_CCFG['DOMAIN_SETUP_FEE'];
								$adata['prod_unit_cost']	+= $_CCFG['DOMAIN_SETUP_FEE'];
								$adata['prod_desc']		.= ' ('.$_LANG['_ORDERS']['ADD_SETUP_FEE'].')';
								$zx++;  // We added the setup text once
							}
						# Add regular setup fee (and text) to order
							IF ($_CCFG['ORDER_SETUP_FEE']) {
								$adata['ord_unit_cost']	+= $_CCFG['ORDER_SETUP_FEE'];
								$adata['prod_unit_cost']	+= $_CCFG['ORDER_SETUP_FEE'];

		  					// Do NOT add setup text twice)
		  						if (!$zx) {$adata['prod_desc'] .= ' ('.$_LANG['_ORDERS']['ADD_SETUP_FEE'].')';}
							}
						}

					}
				}

			# Check if not exist domain, do insert of new domain
				IF (strtolower($adata['ord_domain']) == 'none') {$NoDomain = 1;} ELSE {$NoDomain = 0;}
				IF ($_CCFG['DOMAINS_ENABLE'] && !do_domain_exist_check($adata['ord_domain'], 0) && !$NoDomain) {

				# Determine cp/ftp username/password
					IF ($_CCFG['MATCH_CP_LOGIN_TO_ACCOUNT']) {
						# match password to cp login
						$_new_password = $adata['ord_user_pword'];
					} ELSE {
						# Get a random password
						$_new_password = do_password_create();
					}

				# Calc default account path and misc default vars.
					$_sa_path	= $_CCFG['DOM_DEFAULT_PATH'];

					$_str_search	= 'domain';
					$_str_replace	= $adata['ord_domain'];
					$_sa_path		= eregi_replace($_str_search, $_str_replace, $_sa_path);

					$_str_search	= 'username';
					$_str_replace	= $adata['ord_user_name'];
					$_sa_path		= eregi_replace($_str_search, $_str_replace, $_sa_path);

					IF ($_CCFG['DOM_DEFAULT_USERNAME'] == 'username')			{$_sa_uname = $adata['ord_user_name'];}
					IF ($_CCFG['DOM_DEFAULT_USERNAME'] == 'domain')			{$_sa_uname = $adata['ord_domain'];}
					IF ($_CCFG['DOM_DEFAULT_USERNAME'] == 'username@domain')	{$_sa_uname = $adata['ord_user_name'].'@'.$adata['ord_domain'];}
					IF ($_sa_uname == '')								{$_sa_uname = $adata['ord_domain'];}

				# Grab next domain_id
					$next_dom = do_get_max_domain_id();
					$next_dom = $next_dom + 1;

				# Setup default cp_url value
					$dom_cp_url = $_CCFG['DOM_DEFAULT_CP_URL'];

				# Replace domain info
					$dom_cp_url = str_replace("%DOM_ID%", $next_dom, $dom_cp_url);
					$dom_cp_url = str_replace("%DOM_NAME%", $adata['ord_domain'], $dom_cp_url);

				# Replace client info
					$dom_cp_url = str_replace("%CL_ID%", $_ord_cl_id, $dom_cp_url);

				# Replace FTP info
					$dom_cp_url = str_replace("%FTP_NAME%", $_sa_uname, $dom_cp_url);
					$dom_cp_url = str_replace("%FTP_PWORD%", $_new_password, $dom_cp_url);

				# Insert Domains
					$query_d = 'INSERT INTO '.$_DBCFG['domains'].' (';
					$query_d .= ' dom_cl_id, dom_domain, dom_si_id, dom_ip, dom_path';
					$query_d .= ', dom_url_cp, dom_user_name_cp, dom_user_pword_cp, dom_user_name_ftp, dom_user_pword_ftp';
					$query_d .= ', dom_type, dom_allow_domains, dom_allow_subdomains, dom_allow_disk_space_mb';
					$query_d .= ', dom_allow_traffic_mb, dom_allow_mailboxes, dom_allow_databases';
					$query_d .= ' ) VALUES ( ';
					$query_d .= $_ord_cl_id.', ';
					$query_d .= "'".$db_coin->db_sanitize_data($adata['ord_domain'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_CCFG['DOM_DEFAULT_SERVER'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_CCFG['DOM_DEFAULT_IP'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_sa_path)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($dom_cp_url)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_sa_uname)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_new_password)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_sa_uname)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($_new_password)."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_dom_type'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_domains'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_subdomains'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_disk_space_mb'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_traffic_mb'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_mailboxes'])."', ";
					$query_d .= "'".$db_coin->db_sanitize_data($adata['prod_allow_databases'])."'";
					$query_d .= ')';

					$result_d 		= $db_coin->db_query_execute($query_d) OR DIE("Unable to complete request");
					$insert_id_d		= $db_coin->db_query_insertid();
					$adata['dom_id']	= $insert_id_d;

				#########################################################################################################
				# API Output Hook:
				# APIO_order_new_domain: Order new domain hook
					$_isfunc = 'APIO_order_new_domain';
					IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_NEW_DOMAIN_ENABLE'] == 1) {
						IF (function_exists($_isfunc)) {
							$_APIO = $_isfunc($adata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
						} ELSE {
							$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
						}
					}
				#########################################################################################################
				}

			# Do insert into Orders Table if not processed yet
				IF ($sdata['ord_processed'] != 1) {

				# Do insert into Orders Table
					$query_ord = 'INSERT INTO '.$_DBCFG['orders'].' (ord_id';
					$query_ord .= ', ord_ts, ord_ip, ord_status, ord_cl_id, ord_company, ord_name_first, ord_name_last';
					$query_ord .= ', ord_addr_01, ord_addr_02, ord_city, ord_state_prov, ord_country, ord_zip_code';
					$query_ord .= ', ord_phone, ord_email, ord_domain, ord_domain_action, ord_user_name';
					$query_ord .= ', ord_user_pword, ord_vendor_id, ord_prod_id, ord_unit_cost';
					$query_ord .= ', ord_accept_tos, ord_accept_aup, ord_referred_by, ord_comments';
					$query_ord .= ', ord_optfld_01, ord_optfld_02, ord_optfld_03, ord_optfld_04, ord_optfld_05';
					$query_ord .= ')';

				#Get max / create new ord_id, prep comments
					$_max_ord_id = do_get_max_ord_id();

					$query_ord .= ' VALUES ('.($_max_ord_id+1).', ';
					$query_ord .= "'".$db_coin->db_sanitize_data($_uts)."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_ip'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($_CCFG['ORDERS_DEF_STATUS_NEW'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($_ord_cl_id)."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_company'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_name_first'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_name_last'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_addr_01'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_addr_02'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_city'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_state_prov'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_country'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_zip_code'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_phone'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_email'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_domain'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_domain_action'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_user_name'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($ord_user_pword_crypt)."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_vendor_id'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_prod_id'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_unit_cost'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_accept_tos'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_accept_aup'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_referred_by'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_comments'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_optfld_01'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_optfld_02'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_optfld_03'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_optfld_04'])."', ";
					$query_ord .= "'".$db_coin->db_sanitize_data($adata['ord_optfld_05'])."'";
					$query_ord .= ')';

					$result_ord 		= $db_coin->db_query_execute($query_ord) OR DIE("Unable to complete request");
					$insert_id_ord		= $_max_ord_id+1;
					$adata['ord_id']	= $insert_id_ord;
				} ELSE {

				# Set ord id for code below
					$insert_id_ord	= $adata['ord_id'];
				} # End processed

			# Set Query for select to get vendor / product link from database
				$query_vpl  = 'SELECT vprod_order_link FROM '.$_DBCFG['vendors_prods'];
				$query_vpl .= ' WHERE vprod_vendor_id='.$adata['ord_vendor_id'].' AND vprod_prod_id='.$adata['ord_prod_id'];
				$query_vpl .= ' ORDER BY vprod_id ASC';

			# Do select
				$result_vpl	= $db_coin->db_query_execute($query_vpl);

			# Fetch raw paylink
				while(list($vprod_order_link) = $db_coin->db_fetch_row($result_vpl)) {$_paylink = $vprod_order_link;}

			# Auto-create invoice
				IF ($_CCFG['ORDER_AUTO_CREATE_INVOICE'] && $sdata['ord_processed'] != 1) {
					$adata['invc_id'] = do_auto_create_invoice($adata);

				# Retrieve the invoice info and make available for paylink
					$q2 = 'SELECT * FROM '.$_DBCFG['invoices']." WHERE invc_id='$adata[invc_id]'";
					$r2 = $db_coin->db_query_execute($q2);
					$n2 = $db_coin->db_query_numrows($r2);
					IF ($n2) {
						while ($row = $db_coin->db_fetch_array($r2)) {
							$adata['tax1_amt']  = $row['invc_tax_01_amount'];
							$adata['tax2_amt']  = $row['invc_tax_02_amount'];
							$adata['total_amt'] = $row['invc_total_cost'];
						}
					}
				}

			# Build return and IPN links
				$adata['return_order_buy']	= BASE_HREF.'mod.php?mod=orders&mode=return&order_id='.$insert_id_ord.'&'.$vdata['vendor_buy_parm'].'='.$vdata['vendor_buy_parm_val'];
				$adata['return_order_cancel']	= BASE_HREF.'mod.php?mod=orders&mode=return&order_id='.$insert_id_ord.'&'.$vdata['vendor_buy_parm'].'=0';
				$adata['return_order_ipn']	= BASE_HREF.'coin_modules/ipn/vendors/';

			# Search and replace parameters in string (v1.2.1 plus)
				$_mod_paylink = do_parse_paylink($adata, $_paylink, 1);

			# Finalize and encrypt (if necessary) the paylink
				IF (function_exists('do_encrypt_paylink')) {
					$_order_button = do_encrypt_paylink($_mod_paylink);
				} ELSE {
					$_order_button = $_mod_paylink;
				}
				$adata['ord_pay_link'] = $_order_button;

			# Update invoice info with new paylink
				IF ($_CCFG['ORDER_AUTO_CREATE_INVOICE'] && $sdata['ord_processed'] != 1) {
					$pquery	= 'UPDATE '.$_DBCFG['invoices']." SET invc_pay_link='".$db_coin->db_sanitize_data($_order_button)."' WHERE invc_id=".$adata['invc_id'];
					$result_p	= $db_coin->db_query_execute($pquery);

				# Update order info with invoice id
					$oquery	 = 'UPDATE '.$_DBCFG['orders'].' SET ord_invc_id='.$adata['invc_id'].' WHERE ord_id='.$adata['ord_id'];
					$result_o	 = $db_coin->db_query_execute($oquery);
				}

			# Set current invoice_id
				$_order_button = str_replace('<invoice_id>', $adata['invc_id'], $_order_button).$_nl;

			# Send email that client shown paylink- potential order if not processed.
				IF ($_CCFG['ENABLE_EMAIL_ORDER_OUT'] == 1 && $sdata['ord_processed'] != 1) {

				# Get contact information array
					$_cinfo = get_contact_info($_CCFG['MC_ID_ORDERS']);

				# Set eMail Parameters (no template used)
					IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   						$mail['recip']	= $_cinfo['c_email'];
						$mail['from']	= $_cinfo['c_email'];
					} ELSE {
						$mail['recip']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
						$mail['from']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
					}
					$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_ORDERS']['ORD_P05_EMAIL_01'];
					$mail['message']	.= $_LANG['_ORDERS']['ORD_P05_EMAIL_02'].$insert_id_ord.$_LANG['_ORDERS']['ORD_P05_EMAIL_03'];

				# Call basic email function (ret=0 on error)
					$_ret = do_mail_basic($mail);
				}

			# Send ord ack email
				IF ($_CCFG['ORDERS_ACK_EMAIL_ENABLE'] == 1 && $_CCFG['ORDERS_ACK_EMAIL_ONRET'] != 1 && $sdata['ord_processed'] != 1 ) {

				# Call email order ack function (ret=0 on error)
					$adata['template'] = 'email_order_ack';
					$_ret = do_mail_order($adata, '1').$_nl;
				}

			# Handle processed triggers
				IF ($sdata['ord_processed'] != 1) {

				#########################################################################################################
				# API Output Hook:
				# APIO_order_out_proc: Order outgoing, order inserted
					$_isfunc = 'APIO_order_out_proc';
					IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_OUT_PROC_ENABLE'] == 1) {
						IF (function_exists($_isfunc)) {
							$_APIO = $_isfunc($adata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
						} ELSE {
							$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
						}
					}
				#########################################################################################################
				}

			} ELSE {

			# Process COR routine- basically send email to site admin w/ cc to client.
			# Get custom order data array from orders session table
				$_cor_data = do_orders_session_select();

			# Rebuild some Data Array items with returned record
				$_cor_data['numrows']	= $numrows;
				$_cor_data['ord_cl_id']	= $_ord_cl_id;

			# Get custom order data array from orders session table and do email if not processed
				IF ($_cor_data['ord_processed'] != 1) {

				# Send email to admin and cc client with COR form data.
					do_cor_email($_cor_data);

				#########################################################################################################
				# API Output Hook:
				# APIO_order_cor_proc: COR outgoing
					$_isfunc = 'APIO_order_cor_proc';
					IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_ORDER_COR_PROC_ENABLE'] == 1) {
						IF (function_exists($_isfunc)) {
							$_APIO = $_isfunc($_cor_data); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
						} ELSE {
							$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
						}
					}
				#########################################################################################################
				}
			}

		# Update session with "processed" flag
			IF ($sdata['ord_processed'] != 1) {
				$adata['ord_id'] 			= $insert_id_ord;
				$adata['os_ord_id'] 		= $insert_id_ord;
				$adata['os_ord_cl_id'] 		= $_ord_cl_id;
				$adata['os_ord_processed']	= '1';
				do_orders_session_set_proc($adata);
			}


		# Return order_button string
			return $_order_button;
}
/**************************************************************
 * Module Module Functions
**************************************************************/
?>