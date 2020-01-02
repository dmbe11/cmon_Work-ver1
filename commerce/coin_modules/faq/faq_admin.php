<?php
/**
 * Module: FAQ (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage FAQ
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_faq.php
 */


# Code to handle file being loaded by URL
	IF (eregi('faq_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=faq');
		exit;
	}

/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do FAQ Select List
function do_select_list_faq($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT faq_id, faq_position, faq_time_stamp_mod, faq_status, faq_title, faq_descrip';
		$query .= ' FROM '.$_DBCFG['faq'];
		$query .= ' ORDER BY faq_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_FAQ']['Select_FAQ'].'</option>'.$_nl;

	# Process query results
		while(list($faq_id, $faq_position, $faq_time_stamp_mod, $faq_status, $faq_title, $faq_descrip) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$faq_id.'"';
			IF ($faq_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$faq_title.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do FAQ QA Select List
function do_select_list_faqqa($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT faqqa_id, faqqa_faq_id, faqqa_position, faqqa_time_stamp_mod, faqqa_status, faqqa_question, faqqa_answer';
		$query .= ' FROM '.$_DBCFG['faq_qa'];
		$query .= ' ORDER BY faqqa_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_FAQ']['Select_FAQ_QA'].'</option>'.$_nl;

	# Process query results
		while(list($faqqa_id, $faqqa_faq_id, $faqqa_position, $faqqa_time_stamp_mod, $faqqa_status, $faqqa_question, $faqqa_answer) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$faqqa_id.'"';
			IF ($faqqa_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$faqqa_question.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function do_form_add_edit_faq($amode, $adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_cstr = '';

	# Build mode dependent strings
		switch ($amode) {
			case "add":
				$mode_proper	= $_LANG['_FAQ']['B_Add'];
				$mode_button	= $_LANG['_FAQ']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_FAQ']['B_Edit'];
				$mode_button	= $_LANG['_FAQ']['B_Save'];
				break;
			default:
				$amode			= "add";
				$mode_proper	= $_LANG['_FAQ']['B_Add'];
				$mode_button	= $_LANG['_FAQ']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $mode_proper.$_sp.$_LANG['_FAQ']['FAQ_Entry'].$_sp.'('.$_LANG['_FAQ']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_FAQ']['FAQ_ERR_ERR_HDR1'].'<br>'.$_LANG['_FAQ']['FAQ_ERR_ERR_HDR2'].'<br>'.$_nl;
		 	# Check for faq position entry flag
		 		IF ($aerr_entry['faq_position']) {
		 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR01'];
	 			}
		 	# Check for title entry flag
		 		IF ($aerr_entry['faq_title']) {
			 		IF ($aerr_entry['faq_position']) { $err_str .= ", "; }
		 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR02'];
	 			}
		 	# Check for description entry flag
		 		IF ($aerr_entry['faq_descrip']) {
			 		IF ($aerr_entry['faq_position'] || $aerr_entry['faq_title']) { $err_str .= ", "; }
		 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR03'];
	 			}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="25%">';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="75%">';
		$_td_str_right_just		= '<td class="TP1SML_NJ" width="75%">';

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$amode.'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="faq">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_Position'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'add') {
			IF ($adata['faq_position'] == '') {$adata['faq_position'] = do_get_next_faq_pos();}
		}
		$_cstr .= '<INPUT class="PSML_NC" TYPE=TEXT NAME="faq_position" SIZE=5 value="'.htmlspecialchars($adata['faq_position']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_off_on('faq_status', $adata['faq_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_Admin_FAQ'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('faq_admin', $adata['faq_admin'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_User_FAQ'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('faq_user', $adata['faq_user'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_FAQ_Title'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="faq_title" SIZE=50 value="'.htmlspecialchars($adata['faq_title']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_FAQ']['l_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="faq_descrip" COLS="'.$_cols.'" ROWS="25">'.$adata['faq_descrip'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP3MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="faq_id" value="'.$adata['faq_id'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="faq_time_stamp_mod" value="'.$adata['faq_time_stamp_mod'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_FAQ']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_FAQ']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function do_form_add_edit_faqqa($amode, $adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_cstr = '';

	# Build mode dependent strings
		switch ($amode) {
			case "add":
				$mode_proper	= $_LANG['_FAQ']['B_Add'];
				$mode_button	= $_LANG['_FAQ']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_FAQ']['B_Edit'];
				$mode_button	= $_LANG['_FAQ']['B_Save'];
				break;
			default:
				$amode			= "add";
				$mode_proper	= $_LANG['_FAQ']['B_Add'];
				$mode_button	= $_LANG['_FAQ']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $mode_proper.$_sp.$_LANG['_FAQ']['FAQ_QA_Entry'].$_sp.'('.$_LANG['_FAQ']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_FAQ']['FAQ_ERR_ERR_HDR1'].'<br>'.$_LANG['_FAQ']['FAQ_ERR_ERR_HDR2'].'<br>'.$_nl;

	 	# Check for faq position entry flag
	 		IF ($aerr_entry['faqqa_faq_id']) {
	 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR06'];
 			}
	 	# Check for title entry flag
	 		IF ($aerr_entry['faqqa_position']) {
		 		IF ($aerr_entry['faqqa_faq_id']) { $err_str .= ", "; }
	 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR07'];
 			}
	 	# Check for description entry flag
	 		IF ($aerr_entry['faqqa_question']) {
		 		IF ($aerr_entry['faqqa_faq_id'] || $aerr_entry['faqqa_position']) { $err_str .= ", "; }
	 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR08'];
 			}
	 	# Check for description entry flag
	 		IF ($aerr_entry['faqqa_answer']) {
		 		IF ($aerr_entry['faqqa_faq_id'] || $aerr_entry['faqqa_position'] || $aerr_entry['faqqa_question']) { $err_str .= ", "; }
	 			$err_str .= $_LANG['_FAQ']['FAQ_ERR_ERR09'];
 			}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="25%">';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="75%">';
		$_td_str_right_just		= '<td class="TP1SML_NJ" width="75%">';

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$amode.'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="faqqa">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_FAQ_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_faq('faqqa_faq_id', $adata['faqqa_faq_id'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_Position'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'add') {
			IF ($adata['faqqa_position'] == '') {
				IF (!$adata['faqqa_faq_id']) {$adata['faqqa_faq_id'] = 0;}
				$adata['faqqa_position'] = do_get_next_faqqa_pos($adata['faqqa_faq_id']);
			}
		}
		$_cstr .= '<INPUT class="PSML_NC" TYPE=TEXT NAME="faqqa_position" SIZE=5 value="'.htmlspecialchars($adata['faqqa_position']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_off_on('faqqa_status', $adata['faqqa_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_FAQ']['l_FAQ_Question'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="faqqa_question" SIZE=50 value="'.htmlspecialchars($adata['faqqa_question']).'" maxlength="255">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['faqqa_auto_nl2br'] == '')	{$adata['faqqa_auto_nl2br'] = 1;}
		IF ($adata['faqqa_auto_nl2br'] == 1)	{$_set = ' CHECKED';} ELSE {$_set = ''; $adata['faqqa_auto_nl2br'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="faqqa_auto_nl2br" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_FAQ']['Convert_New_Line_2_Break'].'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_FAQ']['l_Answer'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="faqqa_answer" COLS="'.$_cols.'" ROWS="25">'.$adata['faqqa_answer'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP3MED_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="faqqa_id" value="'.$adata['faqqa_id'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="faqqa_time_stamp_mod" value="'.$adata['faqqa_time_stamp_mod'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_FAQ']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_FAQ']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>