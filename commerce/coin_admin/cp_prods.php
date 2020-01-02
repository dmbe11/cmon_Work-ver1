<?php
/**
 * Admin: Products
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Products
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_admin.php
 */


# Code to handle file being loaded by URL
	IF (!eregi('admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=admin.php?cp=prods');
		exit;
	}


/**************************************************************
 * CP Functions Code
**************************************************************/
# Do Data Input Validate
function cp_do_input_validation($_GPV) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
	#	IF (!$_GPV['prod_id'])				{$err_entry['flag'] = 1; $err_entry['prod_id'] = 1;}
		IF (!$_GPV['prod_name'])				{$err_entry['flag'] = 1; $err_entry['prod_name'] = 1;}
		IF (!$_GPV['prod_desc'])				{$err_entry['flag'] = 1; $err_entry['prod_desc'] = 1;}
	#	IF (!$_GPV['prod_unit_cost'])			{$err_entry['flag'] = 1; $err_entry['prod_unit_cost'] = 1;}
	#	IF (!$_GPV['prod_client_scope'])		{$err_entry['flag'] = 1; $err_entry['prod_client_scope'] = 1;}
	#	IF (!$_GPV['prod_apply_tax_01'])		{$err_entry['flag'] = 1; $err_entry['prod_apply_tax_01'] = 1;}
	#	IF (!$_GPV['prod_apply_tax_02'])		{$err_entry['flag'] = 1; $err_entry['prod_apply_tax_02'] = 1;}
	#	IF (!$_GPV['prod_calc_tax_02_pb'])		{$err_entry['flag'] = 1; $err_entry['prod_calc_tax_02_pb'] = 1;}
	#	IF (!$_GPV['prod_dom_type'])			{$err_entry['flag'] = 1; $err_entry['prod_dom_type'] = 1;}
	#	IF (!$_GPV['prod_allow_domains'])		{$err_entry['flag'] = 1; $err_entry['prod_allow_domains'] = 1;}
	#	IF (!$_GPV['prod_allow_subdomains'])	{$err_entry['flag'] = 1; $err_entry['prod_allow_subdomains'] = 1;}
	#	IF (!$_GPV['prod_allow_disk_space_mb'])	{$err_entry['flag'] = 1; $err_entry['prod_allow_disk_space_mb'] = 1;}
	#	IF (!$_GPV['prod_allow_traffic_mb'])	{$err_entry['flag'] = 1; $err_entry['prod_allow_traffic_mb'] = 1;}
	#	IF (!$_GPV['prod_allow_mailboxes'])	{$err_entry['flag'] = 1; $err_entry['prod_allow_mailboxes'] = 1;}
	#	IF (!$_GPV['prod_allow_databases'])	{$err_entry['flag'] = 1; $err_entry['prod_allow_databases'] = 1;}

	# Unique product name- does not exist
		IF (do_prod_name_exist_check($_GPV['prod_name']) > 1) {
			$err_entry['flag'] = 1; $err_entry['prod_name_exist'] = 1;
		}

		return $err_entry;
}


function do_prod_name_exist_check($aprodname) {
	global $_DBCFG, $db_coin;
	$query = 'SELECT prod_id FROM '.$_DBCFG['products']." WHERE prod_name='".$db_coin->db_sanitize_data($aprodname)."'";
	$result = $db_coin->db_query_execute($query);
	return $db_coin->db_query_numrows($result);
}


# Do select list for: Client Scope
function cp_do_select_list_client_scope($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT cl_id, cl_name_first, cl_name_last, cl_user_name FROM '.$_DBCFG['clients'].' ORDER BY cl_name_last ASC, cl_name_first ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="-2"';
		IF ($avalue == -2) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['Group_Defined'].'</option>'.$_nl;
		$_out .= '<option value="-1"';
		IF ($avalue == -1) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['All_Active_Clients'].'</option>'.$_nl;
		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['All_Visitors'].'</option>'.$_nl;

	# Process query results
		while(list($cl_id, $cl_name_first, $cl_name_last, $cl_user_name) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$cl_id.'"';
			IF ($cl_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$cl_name_last.', '.$cl_name_first.' - '.$cl_user_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do list field form for: Vendors Products
function cp_do_select_listing_prod($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['products'];
		$query	.= ' LEFT JOIN '.$_DBCFG['clients'];
		$query	.= ' ON '.$_DBCFG['products'].'.prod_client_scope='.$_DBCFG['clients'].'.cl_id';

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'A';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Build select and perform
		IF (!$adata['sb'])		{$adata['sb'] = '1';}
		IF ($adata['sb'] == '1')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_id'.$order_AD;}
		IF ($adata['sb'] == '2')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_status'.$order_AD;}
		IF ($adata['sb'] == '3')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_name'.$order_AD;}
		IF ($adata['sb'] == '4')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_desc'.$order_AD;}
		IF ($adata['sb'] == '5')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_unit_cost'.$order_AD;}
		IF ($adata['sb'] == '6')	{$order = ' ORDER BY '.$_DBCFG['products'].'.prod_client_scope'.$order_AD;}

		$query .= $order;

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="'.$_SERVER["PHP_SELF"].'?cp=prods&sb=';

		$_hdr_link_1  = $_LANG['_ADMIN']['l10_Prod_Id'].$_sp.'<br>';
		$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

		$_hdr_link_2  = $_LANG['_ADMIN']['l10_Status'].$_sp.'<br>';
		$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

		$_hdr_link_3  = $_LANG['_ADMIN']['l10_Prod_Name'].$_sp.'<br>';
		$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

		$_hdr_link_4  = $_LANG['_ADMIN']['l10_Description'].$_sp.'<br>';
		$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

		$_hdr_link_5  = $_LANG['_ADMIN']['l10_Unit_Cost'].$_sp.'<br>';
		$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

		$_hdr_link_6  = $_LANG['_ADMIN']['l10_Client_Scope'].$_sp.'<br>';
		$_hdr_link_6 .= $_hdr_link_prefix.'6&so=A">'.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
		$_hdr_link_6 .= $_hdr_link_prefix.'6&so=D">'.$_TCFG['_IMG_SORT_DSC_S'].'</a>';

	# Build form output
		$_out  = '<br>'.$_nl;
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="7">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l10_Products_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_NC"><b>'.$_hdr_link_1.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NC"><b>'.$_hdr_link_2.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_hdr_link_3.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_hdr_link_4.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NR"><b>'.$_hdr_link_5.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL"><b>'.$_hdr_link_6.'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NC"><b>'.$_sp.'</b></td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['prod_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.do_valtostr_off_on($row['prod_status']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['prod_name'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['prod_desc'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR"><nobr>'.do_currency_format($row['prod_unit_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</nobr></td>'.$_nl;

				IF ($row['prod_client_scope'] == '') {$row['prod_client_scope'] = 0;}

			# Check Client Scope / Groups
				IF ($row['prod_cg_08'] == 1 || $row['prod_cg_07'] == 1 || $row['prod_cg_06'] == 1 || $row['prod_cg_05'] == 1	||
				$row['prod_cg_04'] == 1 || $row['prod_cg_03'] == 1 || $row['prod_cg_02'] == 1 || $row['prod_cg_01'] == 1) {
					$_grps = ' (+)';
				} ELSE {
					$_grps = '';
				}
				switch($row['prod_client_scope']) {
					case "-2":
						$_client_scope = $_LANG['_BASE']['Group_Defined'];
						break;
					case "-1":
						$_client_scope = $_LANG['_BASE']['All_Active_Clients'].$_grps;
						break;
					case "0":
						$_client_scope = $_LANG['_BASE']['All_Visitors'].$_grps;
						break;
					default:
						$_client_scope = $row['cl_name_last'].', '.$row['cl_name_first'].$_grps;
						break;
				}

				$_out .= '<td class="TP3SML_NL">'.$_client_scope.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC"><nobr>';
				$_out .= '<a href="'.$_SERVER["PHP_SELF"].'?cp=prods&op=view&prod_id='.$row['prod_id'].'">'.$_TCFG['_S_IMG_VIEW_S'].'</a>'.$_nl;
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 ) {
					$_out .= '&nbsp;&nbsp;<a href="'.$_SERVER["PHP_SELF"].'?cp=prods&op=edit&prod_id='.$row['prod_id'].'">'.$_TCFG['_S_IMG_EDIT_S'].'</a>'.$_nl;
				}
				$_out .= '<nobr></td></tr>'.$_nl;
			}
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function cp_do_form_add_edit_prod($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build op dependent strings
		switch ($adata['op']) {
			case "add":
				$op_proper	= $_LANG['_ADMIN']['B_Add'];
				$op_button	= $_LANG['_ADMIN']['B_Add'];
				break;
			case "edit":
				$op_proper	= $_LANG['_ADMIN']['B_Edit'];
				$op_button	= $_LANG['_ADMIN']['B_Save'];
				break;
			default:
				$adata['op']	= 'add';
				$op_proper	= $_LANG['_ADMIN']['B_Add'];
				$op_button	= $_LANG['_ADMIN']['B_Add'];
				break;
		}

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="30%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['Products_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>';
	 		IF ($aerr_entry['prod_id']) 		{$err_str .= $_LANG['_ADMIN']['AD10_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['prod_name'])		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD10_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['prod_name_exist']){IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD10_ERR_04']; $err_prv = 1;}
			IF ($aerr_entry['prod_desc'])		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD10_ERR_03']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="prods">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Product_Id'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['prod_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['prod_status'] == '') {$adata['prod_status'] = 0;}
		$_cstr .= do_select_list_off_on('prod_status', $adata['prod_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_name" SIZE=20 value="'.htmlspecialchars($adata['prod_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="prod_desc" COLS="'.$_cols.'" ROWS="5">'.$adata['prod_desc'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Unit_Cost'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_unit_cost" SIZE=10 value="'.$adata['prod_unit_cost'].'">'.$_sp.'('.$_LANG['_ADMIN']['no_commas'].')'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_CCFG['INVC_TAX_01_ENABLE']) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['prod_apply_tax_01'] == '')	{$adata['prod_apply_tax_01'] = 1;}
			IF ($adata['prod_apply_tax_01'] == 1)	{$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_apply_tax_01'] = 1;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_apply_tax_01" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_ADMIN']['l10_Apply_Tax_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($_CCFG['INVC_TAX_02_ENABLE']) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['prod_apply_tax_02'] == '')	{$adata['prod_apply_tax_02'] = 1;}
			IF ($adata['prod_apply_tax_02'] == 1)	{$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_apply_tax_02'] = 1;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_apply_tax_02" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_ADMIN']['l10_Apply_Tax_02'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			IF ($adata['prod_calc_tax_02_pb'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_calc_tax_02_pb'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_calc_tax_02_pb" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_ADMIN']['l10_Calc_Tax_02_On_01'].'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		IF ($adata['prod_client_scope'] == '' ) { $adata['prod_client_scope'] = 0;}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Client_Scope'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= cp_do_select_list_client_scope('prod_client_scope', $adata['prod_client_scope']);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l10_Groups'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<table width="100%"><tr><td class="TP0SML_NL">';
		IF ($adata['prod_cg_08'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_08'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_08" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_08'].'</b>'.$_nl;

		$_cstr .= '</td><td class="TP0SML_NL">';
		IF ($adata['prod_cg_04'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_04'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_04" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_04'].'</b>'.$_nl;

		$_cstr .= '</td></tr><tr><td class="TP0SML_NL">';
		IF ($adata['prod_cg_07'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_07'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_07" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_07'].'</b>'.$_nl;

		$_cstr .= '</td><td class="TP0SML_NL">';
		IF ($adata['prod_cg_03'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_03'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_03" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_03'].'</b>'.$_nl;

		$_cstr .= '</td></tr><tr><td class="TP0SML_NL">';
		IF ($adata['prod_cg_06'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_06'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_06" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_06'].'</b>'.$_nl;

		$_cstr .= '</td><td class="TP0SML_NL">';
		IF ($adata['prod_cg_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_02'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_02" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_02'].'</b>'.$_nl;

		$_cstr .= '</td></tr><tr><td class="TP0SML_NL">';
		IF ($adata['prod_cg_05'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_05'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_05" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_05'].'</b>'.$_nl;

		$_cstr .= '</td><td class="TP0SML_NL">';
		IF ($adata['prod_cg_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['prod_cg_01'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="prod_cg_01" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['User_Groups_01'].'</b>'.$_nl;
		$_cstr .= '</td></tr></table>';
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Domain  options IF domains are enabled
		IF ($_CCFG['DOMAINS_ENABLE']) {

			IF ($adata['prod_dom_type'] == '') {$adata['prod_dom_type'] = 0;}
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Domain_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= do_select_list_domain_type('prod_dom_type', $adata['prod_dom_type']);
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Domains'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_domains" SIZE=4 value="'.$adata['prod_allow_domains'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_SubDomains'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_subdomains" SIZE=4 value="'.$adata['prod_allow_subdomains'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Disk_Space_Mb'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_disk_space_mb" SIZE=6 value="'.$adata['prod_allow_disk_space_mb'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Traffic_BW_Mb'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_traffic_mb" SIZE=6 value="'.$adata['prod_allow_traffic_mb'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Databases'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_databases" SIZE=4 value="'.$adata['prod_allow_databases'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_MailBoxes_POP'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="prod_allow_mailboxes" SIZE=4 value="'.$adata['prod_allow_mailboxes'].'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

	# "hidden" domain options if domains are NOT enabled
		} ELSE {
			$_cstr .= '<input type="hidden" name="prod_dom_type" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_domains" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_subdomains" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_disk_space_mb" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_traffic_mb" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_databases" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="prod_allow_mailboxes" value="0">'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="prod_id" value="'.$adata['prod_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $op_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr .= do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_prod($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="35%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="65%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['prod_name'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Product_Id'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['prod_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_off_on($adata['prod_status']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['prod_name'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['prod_desc'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Unit_Cost'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['prod_unit_cost'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Apply_Tax_01'].'-'.$_CCFG['INVC_TAX_01_LABEL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['prod_apply_tax_01']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Apply_Tax_02'].'-'.$_CCFG['INVC_TAX_02_LABEL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['prod_apply_tax_02']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Calc_Tax_02_On_01'].':'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['prod_calc_tax_02_pb']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Client_Scope'].$_sp.'</b></td>'.$_nl;
		IF ($adata['prod_client_scope'] == '') {$adata['prod_client_scope'] = 0;}
		switch($adata['prod_client_scope']) {
			case "-2":
				$_client_scope = $_LANG['_BASE']['Group_Defined'];
				break;
			case "-1":
				$_client_scope = $_LANG['_BASE']['All_Active_Clients'].$_grps;
				break;
			case "0":
				$_client_scope = $_LANG['_BASE']['All_Visitors'].$_grps;
				break;
			default:
				$_cinfo 		= get_contact_client_info($adata['prod_client_scope']);
				$_client_scope	= $_cinfo['cl_name_last'].', '.$_cinfo['cl_name_first'].' - '.$_cinfo['cl_user_name'];
				break;
		}
		$_cstr .= $_td_str_right.$_client_scope.'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l10_Groups'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['prod_cg_08'] == 1 && $_LANG['_BASE']['User_Groups_08'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_08'];
		}
		IF ($adata['prod_cg_07'] == 1 && $_LANG['_BASE']['User_Groups_07'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_07'];
		}
		IF ($adata['prod_cg_06'] == 1 && $_LANG['_BASE']['User_Groups_06'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_06'];
		}
		IF ($adata['prod_cg_05'] == 1 && $_LANG['_BASE']['User_Groups_05'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_05'];
		}
		IF ($adata['prod_cg_04'] == 1 && $_LANG['_BASE']['User_Groups_04'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_04'];
		}
		IF ($adata['prod_cg_03'] == 1 && $_LANG['_BASE']['User_Groups_03'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_03'];
		}
		IF ($adata['prod_cg_02'] == 1 && $_LANG['_BASE']['User_Groups_02'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_02'];
		}
		IF ($adata['prod_cg_01'] == 1 && $_LANG['_BASE']['User_Groups_01'] != '') {
			IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['User_Groups_01'];
		}
		IF ($_p != '') {$_cstr .= $_p.$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Domain options IF domains are enabled
		IF ($_CCFG['DOMAINS_ENABLE']) {

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Domain_Type'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_CCFG['DOM_TYPE'][$adata['prod_dom_type']].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Domains'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_domains'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_SubDomains'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_subdomains'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Disk_Space_Mb'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_disk_space_mb'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Traffic_BW_Mb'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_traffic_mb'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_Databases'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_databases'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;

			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l10_MailBoxes_POP'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$adata['prod_allow_mailboxes'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '</table>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=edit&prod_id='.$adata['prod_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * CP Base Code
**************************************************************/
# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Check $_GPV['op'] (operation switch)
	switch($_GPV['op']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "view":
			break;
		default:
			$_GPV['op'] = 'none';
			break;
	} #end cp switch

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = cp_do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_PERMS['AP16'] != 1 && $_PERMS['AP15'] != 1) {
	IF ($_PERMS['AP10'] == 1) {
		$_GPV['op'] = 'view';
	} ELSE {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit;
	}
}


##############################
# Operation:	View Entry
# Summary:
#	- For viewing entry
#	- Must preceed "none"
##############################
IF ($_GPV['op'] == 'view') {
	# Check for valid $_GPV[prod_id] no
		IF ($_GPV['prod_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['products'];
			$query	.= ' WHERE prod_id='.$_GPV['prod_id'];

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results (assumes one returned row above)
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Merge Data Array with returned row
					$data_new	= array_merge($data, $row);
					$data	= $data_new;
				}
			}

		# Call function for displaying item
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_display_entry_prod ( $data, '1').$_nl;
			echo $_out;

		} ELSE {
			$_GPV['op'] = 'none';
		}
}


##############################
# Operation:	None
# Summary:
#	- For loading select menu.
#	- For no actions specified.
##############################
IF ($_GPV['op'] == 'none') {
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Products_Editor'];

	# Call function for create select form: Products
		$_cstr = cp_do_select_listing_prod($data, '1');

		$_mstr = '<a href="'.$_SERVER["PHP_SELF"].'">'.$_TCFG['_IMG_ADMIN_M'].'</a>';
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= '<a href="'.$_SERVER["PHP_SELF"].'?cp=prods&op=add">'.$_TCFG['_IMG_ADD_NEW_M'].'</a>';
		}

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_GPV['op'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_prod($data, $err_entry,'1').$_nl;
		echo $_out;
}


##############################
# Operation:	Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_GPV['op'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query	 = 'INSERT INTO '.$_DBCFG['products'].' (';
		$query	.= 'prod_status, prod_name, prod_desc, prod_unit_cost, prod_client_scope';
		$query	.= ', prod_apply_tax_01, prod_apply_tax_02, prod_calc_tax_02_pb';
		$query	.= ', prod_dom_type, prod_allow_domains, prod_allow_subdomains, prod_allow_disk_space_mb, prod_allow_traffic_mb';
		$query	.= ', prod_allow_mailboxes, prod_allow_databases';
		$query	.= ', prod_cg_08, prod_cg_07, prod_cg_06, prod_cg_05';
		$query	.= ', prod_cg_04, prod_cg_03, prod_cg_02, prod_cg_01';
		$query	.= ') VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_status'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_name'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_desc'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_unit_cost'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_client_scope'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_apply_tax_01'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_apply_tax_02'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_calc_tax_02_pb'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_dom_type'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_domains'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_subdomains'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_disk_space_mb'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_traffic_mb'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_mailboxes'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_allow_databases'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_08'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_07'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_06'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_05'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_04'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_03'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_02'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['prod_cg_01'])."' ";
		$query	.= ')';
		$result			= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id		= $db_coin->db_query_insertid();
		$_GPV['prod_id']	= $insert_id;

	#########################################################################################################
	# API Output Hook:
	# APIO_product_new: Product Created hook
		$_isfunc = 'APIO_product_new';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_PRODUCT_NEW_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
			} ELSE {
				$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
			}
		}
	#########################################################################################################

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_Products_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['prod_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_prod($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Append API results
		$_out .= $_APIO_ret;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_GPV['op'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# If Stage and Error Entry, pass field vars to form,
	# Otherwise, pass looked up record to form
	IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_prod($data, $err_entry, '1').$_nl;
		echo $_out;

	} ELSE {
	# Check for valid $_GPV[prod_id] no
		IF ($_GPV['prod_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['products'];
			$query	.= ' WHERE prod_id='.$_GPV['prod_id'];

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results (assumes one returned row above)
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Merge Data Array with returned row
					$data_new	= array_merge($data, $row);
					$data	= $data_new;
				}
			}

		# Call function for add/edit form
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_prod($data, $err_entry, '1').$_nl;

		} ELSE {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Products_Editor'];

		# Call function for create select form: Products
			$_cstr = cp_do_select_listing_prod($data, '1');

			$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}

	# Echo final output
		echo $_out;
	}
}


##############################
# Operation: 	Edit Entry Results
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query	 = 'UPDATE '.$_DBCFG['products'].' SET ';
		$query	.= "prod_status='".$db_coin->db_sanitize_data($_GPV['prod_status'])."', ";
		$query	.= "prod_name='".$db_coin->db_sanitize_data($_GPV['prod_name'])."', ";
		$query	.= "prod_desc='".$db_coin->db_sanitize_data($_GPV['prod_desc'])."', ";
		$query	.= "prod_unit_cost='".$db_coin->db_sanitize_data($_GPV['prod_unit_cost'])."', ";
		$query	.= "prod_client_scope='".$db_coin->db_sanitize_data($_GPV['prod_client_scope'])."', ";
		$query	.= "prod_apply_tax_01='".$db_coin->db_sanitize_data($_GPV['prod_apply_tax_01'])."', ";
		$query	.= "prod_apply_tax_02='".$db_coin->db_sanitize_data($_GPV['prod_apply_tax_02'])."', ";
		$query	.= "prod_calc_tax_02_pb='".$db_coin->db_sanitize_data($_GPV['prod_calc_tax_02_pb'])."', ";
		$query	.= "prod_dom_type='".$db_coin->db_sanitize_data($_GPV['prod_dom_type'])."', ";
		$query	.= "prod_allow_domains='".$db_coin->db_sanitize_data($_GPV['prod_allow_domains'])."', ";
		$query	.= "prod_allow_subdomains='".$db_coin->db_sanitize_data($_GPV['prod_allow_subdomains'])."', ";
		$query	.= "prod_allow_disk_space_mb='".$db_coin->db_sanitize_data($_GPV['prod_allow_disk_space_mb'])."', ";
		$query	.= "prod_allow_traffic_mb='".$db_coin->db_sanitize_data($_GPV['prod_allow_traffic_mb'])."', ";
		$query	.= "prod_allow_mailboxes='".$db_coin->db_sanitize_data($_GPV['prod_allow_mailboxes'])."', ";
		$query	.= "prod_allow_databases='".$db_coin->db_sanitize_data($_GPV['prod_allow_databases'])."', ";
		$query	.= "prod_cg_08='".$db_coin->db_sanitize_data($_GPV['prod_cg_08'])."', ";
		$query	.= "prod_cg_07='".$db_coin->db_sanitize_data($_GPV['prod_cg_07'])."', ";
		$query	.= "prod_cg_06='".$db_coin->db_sanitize_data($_GPV['prod_cg_06'])."', ";
		$query	.= "prod_cg_05='".$db_coin->db_sanitize_data($_GPV['prod_cg_05'])."', ";
		$query	.= "prod_cg_04='".$db_coin->db_sanitize_data($_GPV['prod_cg_04'])."', ";
		$query	.= "prod_cg_03='".$db_coin->db_sanitize_data($_GPV['prod_cg_03'])."', ";
		$query	.= "prod_cg_02='".$db_coin->db_sanitize_data($_GPV['prod_cg_02'])."', ";
		$query	.= "prod_cg_01='".$db_coin->db_sanitize_data($_GPV['prod_cg_01'])."' ";
		$query	.= ' WHERE prod_id='.$_GPV['prod_id'];
		$result	 = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_Products_Entry_Results'], '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_prod($data, '1' ).$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 1) {
	# Check product id usage
		$_inuse = do_inuse_prod_id($_GPV['prod_id']);

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Products_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="prods">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		IF (!$_inuse) {
			$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Products_Entry_Message'].$_sp.'='.$_sp.$_GPV['prod_id'].'?</b>'.$_nl;
		} ELSE {
			$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Entry_InUse_Error_Message'].$_sp.'='.$_sp.$_GPV['prod_id'].'.</b>'.$_nl;
		}
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['prod_name'].' - '.$_GPV['prod_desc'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="prod_id" value="'.$_GPV['prod_id'].'">'.$_nl;
		IF (!$_inuse) {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		}
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=edit&prod_id='.$_GPV['prod_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['products'].' WHERE prod_id='.$_GPV['prod_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	#########################################################################################################
	# API Output Hook:
	# APIO_product_del: Product Deleted hook
		$_isfunc = 'APIO_product_del';
		IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_PRODUCT_DEL_ENABLE'] == 1) {
			IF (function_exists($_isfunc)) {
				$_APIO = $_isfunc($_GPV); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
			} ELSE {
				$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
			}
		}
	#########################################################################################################

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Products_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

	# Append API results
		$_cstr .= $_APIO_ret;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=prods', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>