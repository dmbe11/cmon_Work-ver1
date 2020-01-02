<?php
/**
 * Admin: WHOIS
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage whois
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright � 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_admin.php
 */


# Code to handle file being loaded by URL
	IF (!eregi('admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=admin.php?cp=whois');
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
	#	IF (!$_GPV['whois_id'])		{$err_entry['flag'] = 1; $err_entry['whois_id'] = 1;}
	#	IF (!$_GPV['whois_include'])	{$err_entry['flag'] = 1; $err_entry['whois_include'] = 1;}
		IF (!$_GPV['whois_display'])	{$err_entry['flag'] = 1; $err_entry['whois_display'] = 1;}
		IF (!$_GPV['whois_value'])	{$err_entry['flag'] = 1; $err_entry['whois_value'] = 1;}
		IF (!$_GPV['whois_server'])	{$err_entry['flag'] = 1; $err_entry['whois_server'] = 1;}
		IF (!$_GPV['whois_nomatch'])	{$err_entry['flag'] = 1; $err_entry['whois_nomatch'] = 1;}
	#	IF (!$_GPV['whois_prod_id'])	{$err_entry['flag'] = 1; $err_entry['whois_prod_id'] = 1;}
	#	IF (!$_GPV['whois_notes'])	{$err_entry['flag'] = 1; $err_entry['whois_notes'] = 1;}

	return $err_entry;
}


# Do list field form for: WHOIS Servers
function cp_do_select_form_whois($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT whois_id, whois_display FROM '.$_DBCFG['whois'].' ORDER BY whois_display ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l16_WHOIS_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($whois_id, $whois_name) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$whois_id.'">'.$whois_name.'</option>'.$_nl;
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
function cp_do_form_add_edit_whois($adata, $aerr_entry, $aret_flag=0) {
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
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="35%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="65%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['WHOIS_Entry'].$_sp.'('.$_LANG['_ADMIN']['some_fields_required'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>';
	 		IF ($aerr_entry['whois_id']) 		{$err_str .= $_LANG['_ADMIN']['AD16_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['whois_display']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['whois_value']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_05']; $err_prv = 1;}
			IF ($aerr_entry['whois_server']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_03']; $err_prv = 1;}
			IF ($aerr_entry['whois_notes']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_04']; $err_prv = 1;}
			IF ($aerr_entry['whois_nomatch']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_06']; $err_prv = 1;}
			IF ($aerr_entry['whois_include']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_07']; $err_prv = 1;}
			IF ($aerr_entry['whois_prod_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD16_ERR_08']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="whois">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['whois_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['whois_include'] == '') {$adata['whois_include'] = 0;}
		$_cstr .= do_select_list_no_yes('whois_include', $adata['whois_include'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="whois_display" SIZE=25 value="'.htmlspecialchars($adata['whois_display']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_Domain'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="whois_value" SIZE=20 value="'.htmlspecialchars($adata['whois_value']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_URL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="whois_server" SIZE=40 value="'.htmlspecialchars($adata['whois_server']).'" maxlength="100">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_NoMatch_Return_Param'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="whois_nomatch" SIZE=40 value="'.htmlspecialchars($adata['whois_nomatch']).'" maxlength="40">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<hr></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<hr></td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_Override_Product'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= cp_do_select_list_prods('whois_prod_id', $adata['whois_prod_id'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l16_Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="whois_notes" COLS="60" ROWS="10">'.$adata['whois_notes'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="whois_id" value="'.$adata['whois_id'].'">'.$_nl;
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
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_whois($adata, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="35%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="35%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="65%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$_LANG['_ADMIN']['WHOIS_Entry'] . ': ' . $adata['whois_id'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr .= '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['whois_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['whois_include']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['whois_display'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_WHOIS_Domain'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['whois_value'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_URL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['whois_server'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_NoMatch_Return_Param'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['whois_nomatch'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l16_Override_Product'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_prod_id($adata['whois_prod_id'], 1).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l16_Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.nl2br($adata['whois_notes']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=edit&whois_id='.$adata['whois_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

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
	# Check for valid $_GPV[whois_id] no
		IF ($_GPV['whois_id'] ) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['whois'];
			$query	.= ' WHERE whois_id='.$_GPV['whois_id'];

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
			$_out .= cp_do_display_entry_whois($data, '1').$_nl;
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
		$_tstr = $_LANG['_ADMIN']['WHOIS_Editor'];

	# Call function for create select form: Vendors
		$_cstr = cp_do_select_form_whois($_SERVER["PHP_SELF"].'?cp=whois&op=edit', 'whois_id', $_GPV['whois_id'], '1').$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
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
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_whois($data, $err_entry,'1').$_nl;

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
IF ($_GPV[op]=='add' && $_GPV[stage]==1 && !$err_entry['flag'])	{
	# Do select
		$query	 = 'INSERT INTO '.$_DBCFG['whois'].' (';
		$query	.= 'whois_server, whois_nomatch, whois_value, ';
		$query	.= 'whois_display, whois_include, whois_prod_id, ';
		$query	.= 'whois_notes';
		$query	.= ') VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_server'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_nomatch'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_value'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_display'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_include'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_prod_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['whois_notes'])."'";
		$query	.= ")";
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_WHOIS_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['whois_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_whois($data, '1').$_nl;
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
		IF ($_GPV['stage'] == 1 && $err_entry['flag'] ) {
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_whois($data, $err_entry, '1').$_nl;
			echo $_out;

	} ELSE {
	# Check for valid $_GPV[whois_id] no
		IF ($_GPV['whois_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['whois'];
			$query	.= ' WHERE whois_id='.$_GPV['whois_id'];

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
		$_out .= cp_do_form_add_edit_whois($data, $err_entry, '1').$_nl;

		} ELSE {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['WHOIS_Editor'];

		# Call function for create select form: Vendors
			$_cstr  = cp_do_select_form_whois($_SERVER["PHP_SELF"].'?cp=whois&op=edit', 'whois_id', $_GPV['whois_id'], '1');

			$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1').$_nl;
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
		$query  = 'UPDATE '.$_DBCFG['whois'].' SET ';
		$query .= "whois_server='".$db_coin->db_sanitize_data($_GPV['whois_server'])."', ";
		$query .= "whois_nomatch='".$db_coin->db_sanitize_data($_GPV['whois_nomatch'])."', ";
		$query .= "whois_value='".$db_coin->db_sanitize_data($_GPV['whois_value'])."', ";
		$query .= "whois_display='".$db_coin->db_sanitize_data($_GPV['whois_display'])."', ";
		$query .= "whois_include='".$db_coin->db_sanitize_data($_GPV['whois_include'])."', ";
		$query .= "whois_prod_id='".$db_coin->db_sanitize_data($_GPV['whois_prod_id'])."', ";
		$query .= "whois_notes='".$db_coin->db_sanitize_data($_GPV['whois_notes'])."' ";
		$query .= 'WHERE whois_id='.$_GPV['whois_id'];
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_WHOIS_Entry_Results'], '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_whois($data, '1' ).$_nl;
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
		$_tstr = $_LANG['_ADMIN']['Delete_WHOIS_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="whois">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_WHOIS_Entry_Message'].$_sp.'='.$_sp.$_GPV['whois_id'].'?</b>'.$_nl;

		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_sp.$_GPV['whois_name'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="whois_id" value="'.$_GPV['whois_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=edit&whois_id='.$_GPC['whois_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['whois'].' WHERE whois_id='.$_GPV['whois_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_WHOIS_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=whois', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>