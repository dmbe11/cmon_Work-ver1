<?php
/**
 * Admin: Components
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Components
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
		html_header_location('error.php?err=01&url=admin.php?cp=components');
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
	#	IF (!$_GPV['comp_id'])		{$err_entry['flag'] = 1; $err_entry['comp_id'] = 1;}
		IF (!$_GPV['comp_type'])		{$err_entry['flag'] = 1; $err_entry['comp_type'] = 1;}
		IF (!$_GPV['comp_name'])		{$err_entry['flag'] = 1; $err_entry['comp_name'] = 1;}
	#	IF (!$_GPV['comp_mod'])		{$err_entry['flag'] = 1; $err_entry['comp_mod'] = 1;}
		IF (!$_GPV['comp_desc'])		{$err_entry['flag'] = 1; $err_entry['comp_desc'] = 1;}
		IF (!$_GPV['comp_ptitle'])	{$err_entry['flag'] = 1; $err_entry['comp_isadmin'] = 1;}
	#	IF (!$_GPV['comp_col_num'])	{$err_entry['flag'] = 1; $err_entry['comp_col_num'] = 1;}
	#	IF (!$_GPV['comp_isadmin'])	{$err_entry['flag'] = 1; $err_entry['comp_isadmin'] = 1;}
	#	IF (!$_GPV['comp_isuser'])	{$err_entry['flag'] = 1; $err_entry['comp_isuser'] = 1;}
	#	IF (!$_GPV['comp_status'])	{$err_entry['flag'] = 1; $err_entry['comp_status'] = 1;}

		return $err_entry;
}


# Do list field form for: Components
function cp_do_select_form_comp($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT comp_id, comp_name, comp_mod, comp_desc FROM '.$_DBCFG['components'].' ORDER BY comp_name ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l04_Components_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($comp_id, $comp_name, $comp_mod, $comp_desc) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$comp_id.'">'.$comp_name.'-'.$comp_desc.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do Form for Add / Edit
function cp_do_form_add_edit_comp($adata, $aerr_entry) {
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
		$_td_str_left	= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $op_proper.$_sp.$_LANG['_ADMIN']['Components_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>';

	 		IF ($aerr_entry['comp_id'])		{$err_str .= $_LANG['_ADMIN']['AD04_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['comp_type'])		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD04_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['comp_name']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD04_ERR_03']; $err_prv = 1;}
			IF ($aerr_entry['comp_desc']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD04_ERR_04']; $err_prv = 1;}
			IF ($aerr_entry['comp_ptitle'])	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD04_ERR_05']; $err_prv = 1;}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="components">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Component_Id'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['comp_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="comp_type" SIZE=20 value="'.htmlspecialchars($adata['comp_type']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="comp_name" SIZE=20 value="'.htmlspecialchars($adata['comp_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Mod'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="comp_mod" SIZE=20 value="'.htmlspecialchars($adata['comp_mod']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="comp_desc" SIZE=50 value="'.htmlspecialchars($adata['comp_desc']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Page_Title'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="comp_ptitle" SIZE=50 value="'.htmlspecialchars($adata['comp_ptitle']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_No_Columns'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="comp_col_num" size="1" value="'.$adata['comp_col_num'].'">'.$_nl;
		$_cstr .= '<option value="2"';
		IF ($adata['comp_col_num'] == 2) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_ADMIN']['Column_Two'].'</option>'.$_nl;
		$_cstr .= '<option value="3"';
		IF ($adata['comp_col_num'] == 3) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_ADMIN']['Column_Three'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Admin'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('comp_isadmin', $adata['comp_isadmin'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;

		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_User'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('comp_isuser', $adata['comp_isuser'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_off_on('comp_status', $adata['comp_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="comp_id" value="'.$adata['comp_id'].'">'.$_nl;
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

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_comp($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['comp_name'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Component_Id'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_type'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata[comp_name].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Mod'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_mod'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_desc'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Page_Title'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_ptitle'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_No_Columns'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['comp_col_num'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Admin'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['comp_isadmin']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_User'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['comp_isuser']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l04_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_off_on($adata['comp_status']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 || $_PERMS['AP14'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=edit&comp_id='.$adata['comp_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

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
IF ($_PERMS['AP16'] != 1 && $_PERMS['AP15'] != 1 && $_PERMS['AP14'] != 1) {
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
	# Check for valid $_GPV[comp_id] no
		IF ($_GPV['comp_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['components'];
			$query	.= ' WHERE comp_id='.$_GPV['comp_id'];

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
			$_out .= cp_do_display_entry_comp($data, '1').$_nl;

		# Echo final output
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
		$_tstr = $_LANG['_ADMIN']['Components_Editor'];

	# Call function for create select form: Components
		$_cstr = cp_do_select_form_comp($_SERVER['PHP_SELF'].'?cp=components&op=edit', 'comp_id', $_GPV['comp_id'], '1');

		$_mstr = do_nav_link($_SERVER['PHP_SELF'], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 || $_PERMS['AP14'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
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
		$_out .= cp_do_form_add_edit_comp($data, $err_entry,'1').$_nl;

	# Echo final output
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
	# Do insert
		$query 	 = 'INSERT INTO '.$_DBCFG['components'].' (';
		$query 	.= 'comp_type, comp_name, comp_mod, comp_desc';
		$query 	.= ', comp_ptitle, comp_col_num, comp_isadmin, comp_isuser';
		$query 	.= ', comp_status';
		$query	.= ') VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_type'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_name'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_mod'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_desc'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_ptitle'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_col_num'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_isadmin'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_isuser'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['comp_status'])."'";
		$query	.= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_Components_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['comp_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_comp($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

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
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_comp($data, $err_entry,'1').$_nl;
		echo $_out;

	} ELSE {
	# Check for valid $_GPV[comp_id] no
		IF ($_GPV['comp_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['components'];
			$query	.= ' WHERE comp_id='.$_GPV['comp_id'];

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
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_comp($data, $err_entry,'1').$_nl;

		} ELSE {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Components_Editor'];

		# Call function for create select form: Components
			$_cstr = cp_do_select_form_comp($_SERVER["PHP_SELF"].'?cp=components&op=edit', 'comp_id', $_GPV['comp_id'], '1');

			$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

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
	# Prevent login page from being accessible only to logged in people
		IF ($_GPV['comp_name'] == 'login') {
			$_GPV['comp_isuser'] = 0;
			$_GPV['comp_isadmin'] = 0;
			$_GPV['comp_status'] = 1;
		}

	# Prevent index page from being turned off
		IF ($_GPV['comp_name'] == 'index') {
			$_GPV['comp_status'] = 1;
		}

	# Do select
		$query	 = 'UPDATE '.$_DBCFG['components'].' SET ';
		$query	.= "comp_type='".$db_coin->db_sanitize_data($_GPV['comp_type'])."', ";
		$query	.= "comp_name='".$db_coin->db_sanitize_data($_GPV['comp_name'])."', ";
		$query	.= "comp_mod='".$db_coin->db_sanitize_data($_GPV['comp_mod'])."', ";
		$query	.= "comp_desc='".$db_coin->db_sanitize_data($_GPV['comp_desc'])."', ";
		$query	.= "comp_ptitle='".$db_coin->db_sanitize_data($_GPV['comp_ptitle'])."', ";
		$query	.= "comp_col_num='".$db_coin->db_sanitize_data($_GPV['comp_col_num'])."', ";
		$query	.= "comp_isadmin='".$db_coin->db_sanitize_data($_GPV['comp_isadmin'])."', ";
		$query	.= "comp_isuser='".$db_coin->db_sanitize_data($_GPV['comp_isuser'])."', ";
		$query	.= "comp_status='".$db_coin->db_sanitize_data($_GPV['comp_status'])."' ";
		$query	.= 'WHERE comp_id='.$_GPV['comp_id'];
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_Components_Entry_Results'], '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_comp($data, '1').$_nl;
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
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Components_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="components">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Components_Entry_Message'].$_sp.'='.$_sp.$_GPV['comp_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['comp_name'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="comp_id" value="'.$_GPV['comp_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=edit&comp_id='.$_GPV['comp_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['components'].' WHERE comp_id='.$_GPV['comp_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Components_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=components', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>