<?php
/**
 * Admin: Parameters
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Parameters
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
		html_header_location('error.php?err=01&url=admin.php?cp=params');
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
	#	IF (!$_GPV['parm_id'])			{$err_entry['flag'] = 1; $err_entry['parm_id'] = 1;}
		IF (!$_GPV['parm_group'])		{$err_entry['flag'] = 1; $err_entry['parm_group'] = 1;}
		IF (!$_GPV['parm_group_sub'])		{$err_entry['flag'] = 1; $err_entry['parm_group_sub'] = 1;}
		IF (!$_GPV['parm_type'])			{$err_entry['flag'] = 1; $err_entry['parm_type'] = 1;}
		IF (!$_GPV['parm_name'])			{$err_entry['flag'] = 1; $err_entry['parm_name'] = 1;}
	#	IF (!$_GPV['parm_desc'])			{$err_entry['flag'] = 1; $err_entry['parm_desc'] = 1;}
	#	IF (!$_GPV['parm_value'])		{$err_entry['flag'] = 1; $err_entry['parm_value'] = 1;}
	#	IF (!$_GPV['parm_notes'])		{$err_entry['flag'] = 1; $err_entry['parm_notes'] = 1;}

	return $err_entry;
}


# Do list field form for: Parameters
function cp_do_select_form_parm($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT parm_id, parm_name, parm_desc';
		$query	.= ' FROM '.$_DBCFG['parameters'];
		$query	.= ' ORDER BY parm_name ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l09_Parameters_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($parm_id, $parm_name, $parm_desc) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$parm_id.'">'.$parm_name.' - '.$parm_desc.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do list field form for: Parameters
function cp_do_select_listing_parm($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query
		$query	 = 'SELECT parm_id, parm_group, parm_group_sub, parm_type';
		$query	.= ', parm_name, parm_desc, parm_value, parm_notes';
		$query	.= ' FROM '.$_DBCFG['parameters'];

	# Check for filter- group
		IF ($adata['fpg'] != '') {
			$where	.= " WHERE parm_group='".$db_coin->db_sanitize_data($adata['fpg'])."'";
		}

	# Check for filter- sub group
		IF ($adata['fpgs'] != '') {
			IF ($where != '') {
				$where .= " AND parm_group_sub='".$db_coin->db_sanitize_data($adata['fpgs'])."'";
			} ELSE {
				$where .= " WHERE parm_group_sub='".$db_coin->db_sanitize_data($adata['fpgs'])."'";
			}
		}

	# Check for undefined filter
		IF ($adata['cb_hide_undef'] == '' && $adata['b_doit'] == '') {$adata['cb_hide_undef'] = 1;}
		IF ($adata['cb_hide_undef'] == 1) {
			IF ($where != '') {
				$where .= " AND parm_group <> 'undefined'";
			} ELSE {
				$where .= " WHERE parm_group <> 'undefined'";
			}
		}

		$query	.= $where;
		$query	.= ' ORDER BY parm_group ASC, parm_group_sub ASC, parm_desc ASC';

	# Do select and return check only if filtered (to not return ALL records- list getting long)
		IF ($adata['fpg'] || $adata['fpgs']) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);
		}

	# Build output
		$_out  = '<div align="center">'.$_nl;
		$_out .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_out .= '<input type="hidden" name="cp" value="parms">'.$_nl;
		$_out .= '<table>'.$_nl;
		$_out .= '<tr>'.$_nl;
		$_out .= '<td class="TP0MED_NR"><b>'.$_LANG['_ADMIN']['l09_Group'].$_sp.'</b></td>'.$_nl;
		$_out .= '<td class="TP0MED_NL">'.do_select_exist_parm_group('fpg', $adata['fpg'], '1').'</td>'.$_nl;
		$_out .= '<td>'.$_sp.$_sp.'</td><td class="TP0MED_NR"><b>'.$_LANG['_ADMIN']['l09_SubGroup'].$_sp.'</b></td>'.$_nl;
		$_out .= '<td class="TP0MED_NL">'.do_select_exist_parm_group_sub('fpgs', $adata['fpgs'], '1').'</td>'.$_nl;
		$_out .= '<td>'.$_sp.$_sp.'</td><td class="TP0MED_NR">'.$_nl;
		$_out .= do_input_button_class_sw('b_doit', 'SUBMIT', $_LANG['_ADMIN']['B_Do_It'], 'button_form_s_h', 'button_form_s', '1');
		$_out .= '<INPUT TYPE=hidden name="s" value="'.$adata['s'].'">'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr><td class="TP5SML_NC" colspan="7">'.$_nl;
		IF ($adata['cb_hide_undef'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_out .= '<INPUT TYPE=CHECKBOX NAME="cb_hide_undef" value="1"'.$_set.' border="0">'.$_nl;
		$_out .= $_sp.'<b>'.$_LANG['_ADMIN']['Hide_Undefined_Group'].$_sp.'</b></td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;
		$_out .= '</div>'.$_nl;

	# Process query results only if filtered (to not return ALL records- list getting long)
		$_pg_prev	= 'null';
		$_pgs_prev	= 'null';
		IF ($adata['fpg'] || $adata['fpgs']) {
			while(list($parm_id, $parm_group, $parm_group_sub, $parm_type, $parm_name, $parm_desc, $parm_value, $parm_notes) = $db_coin->db_fetch_row($result)) {
				IF ($_pg_prev != $parm_group) {
					IF ($_pg_prev != 'null' ) {
						$_out .= '</td></tr>'.$_nl;
						$_out .= '</table>'.$_nl;
						$_out .= '</div>'.$_nl;
						$_out .= '<br><br>'.$_nl;
					} ELSE {
						$_out .= '<br>'.$_nl;
					}

					$_pgs_prev = 'null';
					$_out .= '<div align="center">'.$_nl;
					$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
					$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="3">'.$_nl;
					$_out .= '<b>'.$_LANG['_ADMIN']['l09_Group'].$_sp.$_sp.$parm_group.'</b>'.$_nl;
					$_out .= '</td></tr>'.$_nl;
				}
				$_pg_prev = $parm_group;

				IF ($_pgs_prev != $parm_group_sub) {
					IF ($_pgs_prev != 'null') {
						$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3SML_NL" colspan="3">'.$_nl;
						$_out .= '<b>'.$_sp.'</b>'.$_nl;
						$_out .= '</td></tr>'.$_nl;
					}

					$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3SML_NL" colspan="3">'.$_nl;
					$_out .= '<b>'.$_LANG['_ADMIN']['l09_SubGroup'].$_sp.$_sp.$parm_group_sub.'</b><br>'.$_nl;
					$_out .= '</td></tr>'.$_nl;
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP3SML_NL" valign="top"><b>'.$_LANG['_ADMIN']['l09_Description'].'</b></td>'.$_nl;
					$_out .= '<td class="TP3SML_NL" valign="top"><b>'.$_LANG['_ADMIN']['l09_Value'].'</b></td>'.$_nl;
					$_out .= '<td class="TP3SML_NC" valign="top"><b>'.$_sp.'</b></td>'.$_nl;
					$_out .= '</tr>'.$_nl;
				}
				$_pgs_prev = $parm_group_sub;

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NL" valign="top">'.$parm_desc.$_nl;
				IF ($_CCFG['_PARM_EDITOR_SHOW_NOTES'] && $parm_notes !='') {
					$_out .= '<br><div class="notes">'.nl2br(htmlspecialchars($parm_notes)).'</div>'.$_nl;
				}
				$_out .= '</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL" valign="top">';
				IF ($parm_type == 'B') {
					$_out .= do_valtostr_no_yes($parm_value);
				} ELSE {
					$_out .= do_parm_value_display_field($parm_name, $parm_value, 1);
				}
				$_out .= $_sp.'</td>'.$_nl;
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
					$_out .= '<td class="TP3SML_NC" valign="top">[<a href="'.$_SERVER["PHP_SELF"].'?cp=parms&op=edit&parm_id='.$parm_id.'&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'].'">'.$_LANG['_ADMIN']['B_Edit'].'</a>]</td>'.$_nl;
				} ELSE {
					$_out .= '<td class="TP3SML_NC" valign="top">[<a href="'.$_SERVER["PHP_SELF"].'?cp=parms&op=view&parm_id='.$parm_id.'&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'].'">'.$_LANG['_ADMIN']['B_View'].'</a>]</td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

		IF ($numrows) {
			$_out .= '</td></tr>'.$_nl;
			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;
			$_out .= '<br>'.$_nl;
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do Form for Add / Edit
function cp_do_form_add_edit_parm($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_cstr = '';

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
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['Parameters_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>'.$_nl;

	 		IF ($aerr_entry['parm_id']) 		{$err_str .= $_LANG['_ADMIN']['AD09_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['parm_group']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['parm_group_sub']) {IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_03']; $err_prv = 1;}
			IF ($aerr_entry['parm_type']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_04']; $err_prv = 1;}
			IF ($aerr_entry['parm_name']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_05']; $err_prv = 1;}
			IF ($aerr_entry['parm_desc']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_06']; $err_prv = 1;}
			IF ($aerr_entry['parm_value']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_07']; $err_prv = 1;}
			IF ($aerr_entry['parm_notes']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD09_ERR_08']; $err_prv = 1;}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="parms">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Parameter_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['parm_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Group'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_parm_group('parm_group', $adata['parm_group'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_SubGroup'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_parm_group_sub('parm_group_sub', $adata['parm_group_sub'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_parm_type('parm_type', $adata['parm_type'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="parm_name" SIZE=50 value="'.htmlspecialchars($adata['parm_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="parm_desc" SIZE=50 value="'.htmlspecialchars($adata['parm_desc']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l09_Value'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right;
		IF ($adata['parm_type'] == 'B') {
			$_cstr .= do_select_list_no_yes('parm_value', $adata['parm_value'], 1).$_nl;
		} ELSE {
			$_cstr .= do_parm_value_edit_field($adata['parm_name'], 'parm_value', $adata['parm_value'], 1 ).$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l09_Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="parm_notes" COLS="60" ROWS="10">'.$adata['parm_notes'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="parm_id" value="'.$adata['parm_id'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="fpg" value="'.$adata['fpg'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="fpgs" value="'.$adata['fpgs'].'">'.$_nl;
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
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'], $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_parm($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['parm_name'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr .= '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Parameter_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Group'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_group'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_SubGroup'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_group_sub'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_type'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_name'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l09_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['parm_desc'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l09_Value'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right;
		IF ($adata['parm_type'] == 'B') {
			$_cstr .= do_valtostr_no_yes($adata['parm_value']);
		} ELSE {
			$_cstr .= do_parm_value_display_field($adata['parm_name'], $adata['parm_value'], 1);
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l09_Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.nl2br(htmlspecialchars($adata['parm_notes'])).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=edit&parm_id='.$adata['parm_id'].'&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&fpg='.$adata['fpg'].'&fpgs='.$adata['fpgs'], $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

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

# Check $_GPV[op] (operation switch)
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
	# Encode binary fields
		$_dec = cp_do_encode_BD16($_GPV);
		IF ($_GPV['parm_name'] == 'ORDERS_FIELD_ENABLE_COR')	{$_GPV['parm_value'] = $_dec;}
		IF ($_GPV['parm_name'] == 'ORDERS_FIELD_ENABLE_ORD')	{$_GPV['parm_value'] = $_dec;}
		IF ($_GPV['parm_name'] == 'ORDERS_FIELD_REQUIRE_COR')	{$_GPV['parm_value'] = $_dec;}
		IF ($_GPV['parm_name'] == 'ORDERS_FIELD_REQUIRE_ORD')	{$_GPV['parm_value'] = $_dec;}

	# Call validate input function
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
	# Check for valid $_GPV[parm_id] no
		IF ($_GPV['parm_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['parameters'];
			$query	.= ' WHERE parm_id='.$_GPV['parm_id'];

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
			$_out .= cp_do_display_entry_parm ( $data, '1').$_nl;

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
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Parameters_Editor'];

	# Call function for create select form: Parameters
	#	$aaction = $_SERVER["PHP_SELF"].'?cp=parms&op=edit';
	#	$aname	= "parm_id";
	#	$avalue	= $_GPV[parm_id];
	#	$_cstr .= cp_do_select_form_parm($aaction, $aname, $avalue, '1');
		$_cstr = cp_do_select_listing_parm($data, '1');

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
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
IF ($_GPV[op]=='add' && (!$_GPV[stage] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_parm($data, $err_entry,'1').$_nl;

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
	# Do select
		$query	 = 'INSERT INTO '.$_DBCFG['parameters'];
		$query	.= ' (parm_group, parm_group_sub, parm_type, parm_name, parm_desc, parm_value, parm_notes)';
		$query	.= ' VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_group'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_group_sub'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_type'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_name'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_desc'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_value'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['parm_notes'])."')";
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ADMIN']['Add_Parameters_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')';
		$_out .= do_subj_block_it ($title_text, '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['parm_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_parm($data, '1').$_nl;
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
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_parm ( $data, $err_entry,'1').$_nl;
			echo $_out;

		} ELSE {
		# Check for valid $_GPV['parm_id'] no
			IF ($_GPV['parm_id']) {
			# Set Query for select.
				$query	 = 'SELECT * FROM '.$_DBCFG['parameters'];
				$query	.= ' WHERE parm_id='.$_GPV['parm_id'];

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
				$_out .= cp_do_form_add_edit_parm($data, $err_entry,'1').$_nl;

			} ELSE {
			# Content start flag
				$_out .= '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_ADMIN']['Parameters_Editor'];

			# Call function for create select form: Parameters
				$_cstr .= cp_do_select_listing_parm($data, '1');

				$_mstr .= do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

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
		$query	 = 'UPDATE '.$_DBCFG['parameters'].' SET ';
		$query	.= "parm_group='".$db_coin->db_sanitize_data($_GPV['parm_group'])."', ";
		$query	.= "parm_group_sub='".$db_coin->db_sanitize_data($_GPV['parm_group_sub'])."', ";
		$query	.= "parm_type='".$db_coin->db_sanitize_data($_GPV['parm_type'])."', ";
		$query	.= "parm_name='".$db_coin->db_sanitize_data($_GPV['parm_name'])."', ";
		$query	.= "parm_desc='".$db_coin->db_sanitize_data($_GPV['parm_desc'])."', ";
		$query	.= "parm_value='".$db_coin->db_sanitize_data($_GPV['parm_value'])."', ";
		$query	.= "parm_notes='".$db_coin->db_sanitize_data($_GPV['parm_notes'])."' ";
		$query	.= "WHERE parm_id='".$db_coin->db_sanitize_data($_GPV['parm_id'])."'";
		$result	 = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ADMIN']['Edit_Parameters_Entry_Results'];
		$_out .= do_subj_block_it ($title_text, '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_parm ( $data, '1' ).$_nl;
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
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Parameters_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="parms">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Parameters_Entry_Message'].$_sp.'='.$_sp.$_GPV['parm_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['parm_name'].' - '.$_GPV['parm_desc'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="parm_id" value="'.$_GPV['parm_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw ('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=edit&parm_id='.$_GPV['parm_id'].'&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['parameters'].' WHERE parm_id='.$_GPV['parm_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Parameters_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&op=add&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=parms&fpg='.$_GPV['fpg'].'&fpgs='.$_GPV['fpgs'], $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>