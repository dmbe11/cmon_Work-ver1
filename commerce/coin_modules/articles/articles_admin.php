<?php
/**
 * Module: Articles (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Articles
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_articles.php
 */


# Code to handle file being loaded by URL
	IF (eregi('articles_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=articles');
		exit;
	}


/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do Form for Add / Edit
function do_form_add_edit($amode, $adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags ();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_cstr = '';

	# Build mode dependent strings
		switch ($amode) {
			case "add":
				$mode_proper	= $_LANG['_ARTICLES']['B_Add'];
				$mode_button	= $_LANG['_ARTICLES']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_ARTICLES']['B_Edit'];
				$mode_button	= $_LANG['_ARTICLES']['B_Save'];
				break;
			default:
				$amode			= "add";
				$mode_proper	= $_LANG['_ARTICLES']['B_Add'];
				$mode_button	= $_LANG['_ARTICLES']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $mode_proper.$_sp.$_LANG['_ARTICLES']['Articles_Entry'].$_sp.'('.$_LANG['_ARTICLES']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ARTICLES']['ART_ERR_ERR_HDR1'].'<br>'.$_LANG['_ARTICLES']['ART_ERR_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['subject']) 	{$err_str .= $_LANG['_ARTICLES']['ART_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['topic_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ARTICLES']['ART_ERR_ERR02']; $err_prv = 1;}
			IF ($aerr_entry['cat_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ARTICLES']['ART_ERR_ERR03']; $err_prv = 1;}
			IF ($aerr_entry['entry']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ARTICLES']['ART_ERR_ERR04']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" width="25%">';
		$_td_str_left_valign	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="75%">';
		$_td_str_right_just		= '<td class="TP1SML_NJ" width="75%">';

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="articles">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$amode.'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ARTICLES']['l_Article_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['mode'] == 'add') {
			$_cstr .= '('.$_LANG['_ARTICLES']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_valign.'<b>'.$_LANG['_ARTICLES']['l_DateTime'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['time_stamp'] <= 0 || $adata['time_stamp'] == '') {$adata['time_stamp'] = dt_get_uts().$_nl;}
		$_cstr .= do_datetime_edit_list('time_stamp', $adata['time_stamp'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ARTICLES']['l_Subject'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="subject" SIZE=50 value="'.htmlspecialchars($adata['subject']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ARTICLES']['l_Topic'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_topic('topic_id', $adata['topic_id'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ARTICLES']['l_Category'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_cat('cat_id', $adata['cat_id'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['auto_nl2br'] == '')	{$adata['auto_nl2br'] = 1;}
		IF ($adata['auto_nl2br'] == 1)	{$_set = ' CHECKED';} ELSE {$_set = ''; $adata['auto_nl2br'] = 0;}
		$_cstr .= '<INPUT TYPE=CHECKBOX NAME="auto_nl2br" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_ARTICLES']['Convert_New_Line_2_Break'].'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_valign.'<b>'.$_LANG['_ARTICLES']['l_Entry'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right_just.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="entry" COLS="'.$_cols.'" ROWS="25">'.$adata['entry'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="id" value="'.$adata['id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ARTICLES']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['mode'] == 'edit') {$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ARTICLES']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=edit&id='.$adata['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>