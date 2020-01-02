<?php
/**
 * Admin: Side Menus
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Side_Menus
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
		html_header_location('error.php?err=01&url=admin.php?cp=menu');
		exit;
	}

/**************************************************************
 * CP Functions Code: Menu Block
**************************************************************/
# Do Data Input Validate
function cp_do_input_validation($_GPV) {
	# Initialize error array
		$_err_entry = array("flag" => 0);

	# Check modes and data as required
		IF ($_GPV['obj'] == 'block') {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV['block_id'])		{$err_entry['flag'] = 1; $err_entry['block_id'] = 1;}
			IF (!$_GPV['block_pos'])		{$err_entry['flag'] = 1; $err_entry['block_pos'] = 1;}
			IF (!$_GPV['block_title'])	{$err_entry['flag'] = 1; $err_entry['block_title'] = 1;}
		#	IF (!$_GPV['block_status'])	{$err_entry['flag'] = 1; $err_entry['block_status'] = 1;}
		#	IF (!$_GPV['block_admin'])	{$err_entry['flag'] = 1; $err_entry['block_admin'] = 1;}
		#	IF (!$_GPV['block_user'])	{$err_entry['flag'] = 1; $err_entry['block_user'] = 1;}
		#	IF (!$_GPV['block_col'])		{$err_entry['flag'] = 1; $err_entry['block_col'] = 1;}
		}

		IF ($_GPV['obj'] == 'item') {
		# Check required fields (err / action generated later in cade as required)
			IF ($_GPV['item_id'] == '')	{$err_entry['flag'] = 1; $err_entry['item_id'] = 1;}
			IF (!$_GPV['item_text'])		{$err_entry['flag'] = 1; $err_entry['item_text'] = 1;}
			IF (!$_GPV['item_url'])		{$err_entry['flag'] = 1; $err_entry['item_url'] = 1;}
		#	IF (!$_GPV['item_target'])	{$err_entry['flag'] = 1; $err_entry['item_target'] = 1;}
		#	IF (!$_GPV['item_type'])		{$err_entry['flag'] = 1; $err_entry['item_type'] = 1;}
		#	IF (!$_GPV['item_status'])	{$err_entry['flag'] = 1; $err_entry['item_status'] = 1;}
		#	IF (!$_GPV['item_admin'])	{$err_entry['flag'] = 1; $err_entry['item_admin'] = 1;}
		#	IF (!$_GPV['item_user'])		{$err_entry['flag'] = 1; $err_entry['item_user'] = 1;}
		}

		return $err_entry;
}


# Do listing of items, by block_id with edit url
function cp_do_list_menu_links($ablock_col='A', $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Validate ablock_col
		$_ablock_col_upper = strtoupper($ablock_col);
		switch ($_ablock_col_upper) {
			case "A":
				$_str_where = '';
				break;
			case "L":
				$_str_where = ' AND '.$_DBCFG['menu_blocks'].".block_col = '$_ablock_col_upper'";
				break;
			case "R":
				$_str_where = ' AND '.$_DBCFG['menu_blocks'].".block_col = '$_ablock_col_upper'";
				break;
			default:
				$ablock_col = 'A';
				$_str_where = '';
				break;
		}

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['menu_blocks'].', '.$_DBCFG['menu_blocks_items'];
		$query	.= ' WHERE '.$_DBCFG['menu_blocks_items'].'.block_id='.$_DBCFG['menu_blocks'].'.block_id';
		$query	.= $_str_where;
		$query	.= ' ORDER BY '.$_DBCFG['menu_blocks'].'.block_pos ASC, '.$_DBCFG['menu_blocks_items'].'.item_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above)
		IF ($numrows) {
			$block_id_last = '';
			while ($row = $db_coin->db_fetch_array($result)) {
			# Build link tags
				$block_link	 = '';
				$block_link	.= '<a href="admin.php?cp=menu&op=edit&obj=block&block_id='.$row['block_id'].'">';
				$block_link	.= '<b>'.$row['block_title'].'</b></a>'.$_nl;
				$item_link	 = '';
				$item_link	.= $_nl.$_sp.$_sp.$_sp.$_sp.'-'.$_sp;
				$item_link	.= '<a href="admin.php?cp=menu&op=edit&obj=item&block_id='.$row['block_id'].'&item_id='.$row['item_id'].'">';
				$item_link	.= '<b>'.$row['item_text'].'</b></a>'.$_nl;

			# Flag first of menu block group and do link- else- just block item links.
				IF ($block_id_last != $row['block_id']) {
					$_out .= $block_link.'<br>'.$item_link.'<br>'.$_nl;
				} ELSE {
					$_out .= $item_link.'<br>'.$_nl;
				}

			# Set last to current
				$block_id_last = $row['block_id'];
			}
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Menu blocks
function cp_do_select_form_menu_block($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT block_id, block_pos, block_title FROM '.$_DBCFG['menu_blocks'].' ORDER BY block_pos ASC, block_id ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<form method="POST" action="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l08_Menu_Blocks_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($block_id, $block_pos, $block_title) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$block_id.'">'.str_pad($block_pos,2,'0',STR_PAD_LEFT).' - '.$block_title.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="obj" value="block">'.$_nl;
		$_out .= do_input_button_class_sw('b_load_mb', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</form>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Menu blocks
function cp_do_select_list_menu_block($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT block_id, block_title';
		$query	.= ' FROM '.$_DBCFG['menu_blocks'].' ORDER BY block_title ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($block_id, $block_title) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$block_id.'"';
			IF ($block_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$block_title.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Menu blocks
function cp_do_select_list_block_pos($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT block_pos, block_col';
		$query	.= ' FROM '.$_DBCFG['menu_blocks'].' ORDER BY block_pos ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
	#	$_out .= '<option value="0">0</option>'.$_nl;

	# Build default list
		$_max = $_CCFG['_MAX_MENU_BLK_POS'];
		for ($i = 0; $i <= $_max; $i++) {$_list[$i] = $i;}

		# Loop array to set used items in default list
			while(list($block_pos, $block_col) = $db_coin->db_fetch_row($result)) {
				IF ($block_col == 'L') {
					$_col_str = $_LANG['_ADMIN']['MBlock_Col_Abbrev_Left'];
				} ELSE IF ($block_col == 'R') {
					$_col_str = $_LANG['_ADMIN']['MBlock_Col_Abbrev_Right'];
				}
				IF ($block_pos == $avalue) {
					$_used[$block_pos] = ' ('.$_LANG['_ADMIN']['MBlock_Current'].'-'.$_col_str.')';
				} ELSE {
					$_used[$block_pos] = ' ('.$_LANG['_ADMIN']['MBlock_Used'].'-'.$_col_str.')';
				}
			}

		# Build options in list
			for ($k = 0; $k <= count($_list); $k++) {
				$_out .= '<option value="'.$_list[$k].'"';
				IF ($_list[$k] == $avalue) {$_out .= ' selected';}
				$_out .= '>'.str_pad($_list[$k], 2, '0', STR_PAD_LEFT);
				IF ($_used[$k] != '') {$_out .= $_used[$k];}
				$_out .= '</option>'.$_nl;
			}

			$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function cp_do_form_add_edit_menu_block($adata, $aerr_entry, $aret_flag=0) {
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
		$_td_str_left	= '<td class="TP1SML_NR" width="30%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['Menu_Blocks_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>';
	 		IF ($aerr_entry['block_id']) 		{$err_str .= $_LANG['_ADMIN']['AD08_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['block_title']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD08_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['block_status']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD08_ERR_03']; $err_prv = 1;}
			IF ($aerr_entry['block_admin']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD08_ERR_04']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<table width="100%"><tr><td align="center" valign="top">'.$_nl;
		$_cstr .= '<form method="POST" action="admin.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="block">'.$_nl;

		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Block_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['block_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		IF (!$adata['block_pos']) {$adata['block_pos'] = 0;}
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Position'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= cp_do_select_list_block_pos('block_pos', $adata['block_pos'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Title'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="block_title" SIZE=30 value="'.htmlspecialchars($adata['block_title']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_off_on('block_status', $adata['block_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Admin_Block'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('block_admin', $adata['block_admin'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_User_Block'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('block_user', $adata['block_user'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Column'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="block_col" size="1" value="'.$adata['block_col'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF (!$adata['block_col'] == 'L' && !$adata['block_col'] == 'R') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_ADMIN']['Column_None'].'</option>'.$_nl;
		$_cstr .= '<option value="L"';
		IF ($adata['block_col'] == 'L') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_ADMIN']['Column_Left'].'</option>'.$_nl;
		$_cstr .= '<option value="R"';
		IF ($adata['block_col'] == 'R') {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_ADMIN']['Column_Right'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="obj" value="block">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="block_id" value="'.$adata['block_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit_mb', 'SUBMIT', $op_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset_mb', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete_mb', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;

		$_cstr .= '</td><td align="center" valign="top">'.$_nl;

		IF ($adata['block_id'] > 0) {
			$_cstr .= cp_do_select_form_menu_item('admin.php?cp=menu&op=edit&obj=item', 'item_id', $adata['item_id'], $adata['block_id'], '1').$_nl;
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<form method="POST" action="admin.php">'.$_nl;
			$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
			$_cstr .= '<input type="hidden" name="op" value="add">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
			$_cstr .= '<table cellpadding="0" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0SML_NC" width="100%">'.$_nl;
			$_cstr .= '<input type="hidden" name="stage" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
			$_cstr .= '<input type="hidden" name="item_id" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="block_id" value="'.$adata['block_id'].'">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_add_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Add_Menu_Item'], 'button_form_h', 'button_form', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</form>'.$_nl;
		} ELSE {
			$_cstr .= $_sp.$_sp.$_nl;
		}
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '</td></tr></table>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual menu entry)
function cp_do_display_entry_menu_block($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['block_title'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Block_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['block_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Position'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['block_pos'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Title'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['block_title'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_off_on($adata['block_status']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Admin_Block'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['block_admin']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_User_Block'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['block_user']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Column'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['block_col'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 || $_PERMS['AP14'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=menu&op=edit&obj=block&block_id='.$adata['block_id'], $_TCFG['_IMG_EDIT_BLOCK_M'],$_TCFG['_IMG_EDIT_BLOCK_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * CP Functions Code: Menu Block Item
**************************************************************/
# Do list field form for: Menu Block Items (for passed block_id)
function cp_do_select_form_menu_item($aaction, $aname, $avalue, $ablock_id, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		IF (!$ablock_id || $ablock_id == 0 ) {
		# Set Query for select.
			$query	 = 'SELECT block_id, item_id, item_text';
			$query	.= ' FROM '.$_DBCFG['menu_blocks_items'].' WHERE block_id=0 ORDER BY block_id ASC, item_id ASC';
			$show_all_list_flag	= 1;

		} ELSE {
		# Set Query for select of past id record.
			$query	 = 'SELECT block_id, item_id, item_text';
			$query	.= ' FROM '.$_DBCFG['menu_blocks_items'].' WHERE block_id='.$ablock_id.' ORDER BY block_id ASC, item_id ASC';
			$show_all_list_flag	= 0;
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

 	# Build form output
		$_out  = '<form method="POST" action="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l08_Menu_Block_Items_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($block_id_item, $item_id, $item_text) = $db_coin->db_fetch_row($result)) {
		# Calc label for either all or by block ids
			IF ($show_all_list_flag) {
				$_out .= '<option value="'.$item_id.'">'.str_pad($block_id_item,3,'0',STR_PAD_LEFT).' - '.str_pad($item_id,3,'0',STR_PAD_LEFT).' - '.$item_text.'</option>'.$_nl;
			} ELSE {
				$_out .= '<option value="'.$item_id.'">'.str_pad($item_id,3,'0',STR_PAD_LEFT).' - '.$item_text.'</option>'.$_nl;
			}
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="block_id" value="'.$ablock_id.'">'.$_nl;
		$_out .= '<INPUT TYPE=hidden name="obj" value="item">'.$_nl;
		$_out .= do_input_button_class_sw('b_load_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</form>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Menu Items
function cp_do_select_list_item_id($aname, $avalue, $ablock_id, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build default list
		$_max = $_CCFG['_MAX_MENU_ITM_NO'];
		for ($i = 0; $i <= $_max; $i++) {$_default[$i] = $i;}

	# Set Query for select if existing block id passed records for list.
		IF ($ablock_id >= 0) {
		# Set Query for select.
			$query	 = 'SELECT block_id, item_id';
			$query	.= ' FROM '.$_DBCFG['menu_blocks_items'];
			$query	.= ' WHERE block_id='.$ablock_id.' ORDER BY block_id ASC, item_id ASC';

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Loop Array- set flags for existing
			while(list($block_id, $item_id) = $db_coin->db_fetch_row($result)) {
				IF ($item_id != $avalue) {$_default[$item_id] = -1;}
			}
		}

	# Build Form row
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
	#	$_out .= '<option value="0">0</option>'.$_nl;


	# Loop Array again to set used/current flags
		$y=0;
		for ($i = 0; $i <= $_max; $i++) {
			IF ($_default[$i] != -1) {
				$_list[$y] = $i;
				IF ($_list[$y] == $avalue) {$_used[$y] = ' ('.$_LANG['_ADMIN']['MBlock_Current'].')';} ELSE {$_used[$y] = '';}
				$y = $y+1;
			}
		}

	# Build options in list
		for ($k = 0; $k <= count($_list); $k++) {
			$_out .= '<option value="'.$_list[$k].'"';
			IF ($_list[$k] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.str_pad($_list[$k],3,'0',STR_PAD_LEFT);
			IF ($_used[$k] != '') {$_out .= $_used[$k];}
			$_out .= '</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function cp_do_form_add_edit_menu_item($adata, $aerr_entry, $aret_flag=0) {
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
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['Menu_Block_Items_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>';
	 		IF ($aerr_entry['item_id']) 	{$err_str .= $_LANG['_ADMIN']['AD08_ERR_11']; $err_prv = 1;}
			IF ($aerr_entry['item_text']) {IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD08_ERR_12']; $err_prv = 1;}
			IF ($aerr_entry['item_url']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD08_ERR_13']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<form method="POST" action="admin.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Block_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= cp_do_select_list_menu_block('block_id', $adata['block_id'], '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Item_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= cp_do_select_list_item_id('item_id', $adata['item_id'], $adata['block_id'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Text'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="item_text" SIZE=30 value="'.htmlspecialchars($adata['item_text']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_URL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="item_url" SIZE=50 value="'.htmlspecialchars($adata['item_url']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Target'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_mbi_target('item_target', $adata['item_target'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_mbi_type('item_type', $adata['item_type'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_off_on('item_status', $adata['item_status'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Admin_Item'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('item_admin', $adata['item_admin'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_User_Item'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('item_user', $adata['item_user'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="obj" value="item">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="block_id_orig" value="'.$adata['block_id'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="item_id_orig" value="'.$adata['item_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit_mbi', 'SUBMIT', $op_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset_mbi', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;

		$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($adata['op'] == 'edit') {
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=item&block_id='.$adata['block_id'], $_TCFG['_IMG_ADD_BLOCK_ITEM_M'],$_TCFG['_IMG_ADD_BLOCK_ITEM_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual menu item entry)
function cp_do_display_entry_menu_item($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['item_text'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Block_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['block_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Item_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['item_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Text'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['item_text'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_URL'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['item_url'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Target'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_CCFG['MBI_LINK_TARGET'][$adata['item_target']].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_CCFG['MBI_TEXT_TYPE'][$adata['item_type']].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_off_on($adata['item_status']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_Admin_Item'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['item_admin']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l08_User_Item'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.do_valtostr_no_yes($adata['item_user']).'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 || $_PERMS['AP14'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=menu&op=edit&obj=item&block_id='.$adata['block_id'].'&item_id='.$adata[item_id], $_TCFG['_IMG_EDIT_BLOCK_ITEM_M'],$_TCFG['_IMG_EDIT_BLOCK_ITEM_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=item&block_id='.$adata['block_id'], $_TCFG['_IMG_ADD_BLOCK_ITEM_M'],$_TCFG['_IMG_ADD_BLOCK_ITEM_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * CP Base Code
**************************************************************/
# Get security vars
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Check $_GPV['op'] (operation switch)
	switch($_GPV['op']) {
		case "add":
			IF ($_GPV['b_delete_mb'] != '' || $_GPV['b_delete_mbi'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete_mb'] != '' || $_GPV['b_delete_mbi'] != '') {$_GPV['op'] = 'delete';}
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
	# Check for valid $_GPV[block_id] no or $_GPV[block_id]
		IF ($_GPV['block_id'] && $_GPV['obj'] == 'block') {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['menu_blocks'];
			$query	.= ' WHERE block_id='.$_GPV['block_id'];

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
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_display_entry_menu_block($data, '1').$_nl;
			echo $_out;

		} ELSEIF ($_GPV['block_id'] >= 0 && $_GPV['item_id'] && $_GPV['obj'] == 'item') {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['menu_blocks_items'];
			$query	.= ' WHERE block_id='.$_GPV['block_id'].' AND item_id='.$_GPV['item_id'];

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
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_display_entry_menu_item($data, '1').$_nl;
			echo $_out;

		} ELSE {
			$_GPV['op']	= 'none';
			$_GPV['obj']	= '';
		}
}


##############################
# Operation:	None
# Object:		None
# Summary:
#	- For loading select menu.
#	- For no actions specified.
##############################
IF (!$_GPV['op'] == 'none' || !$_GPV['obj']) {
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Menu_Blocks_Editor'];

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP3SML_NC" width="30%"><b>'.$_LANG['_ADMIN']['l08_Menu_Blocks_Edit_List'].'</b></td>'.$_nl;
		$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Left_Col_Quick_Select'].'</b></td>'.$_nl;
		$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Right_Col_Quick_Select'].'</b></td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;

		$_cstr .= '<td class="TP3SML_NC" valign="top" width="30%">'.$_nl;
		$_cstr .= cp_do_select_form_menu_block('admin.php?cp=menu&op=edit&obj=block', 'block_id', $_GPV['block_id'], '1');
		$_cstr .= cp_do_select_form_menu_item('admin.php?cp=menu&op=edit&obj=item', 'item_id', 0, 0, 1);

	# Add button to add item
		$_cstr .= '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="add">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
		$_cstr .= '<table cellpadding="0" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="0">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="obj" value="item">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="item_id" value="0">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="block_id" value="0">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_add_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Add_Menu_Item'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
		$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
		$_cstr .= cp_do_list_menu_links('L', '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
		$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
		$_cstr .= cp_do_list_menu_links('R', '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr><td colspan="5">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1 || $_PERMS['AP14'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


/**************************************************************
 * CP Base Code: Menu Block
**************************************************************/
##############################
# Operation: 	Add Entry
# Object:		Menu Block
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_GPV['op'] == 'add' && $_GPV['obj'] == 'block' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_menu_block($data, $err_entry, '1').$_nl;
		echo $_out;
}

##############################
# Operation:	Add Entry Results
# Object:		Menu Block
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_GPV['op'] == 'add' && $_GPV['obj'] == 'block' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do insert
		$query	 = 'INSERT INTO '.$_DBCFG['menu_blocks'].' ';
		$query	.= '(block_pos, block_title, block_status, block_admin, block_user, block_col)';
		$query	.= ' VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_pos'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_title'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_status'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_admin'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_user'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_col'])."')";
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_Menu_Blocks_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['block_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_menu_block($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Edit Entry
# Object:		Menu Block
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['obj'] == 'block' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# If Stage and Error Entry, pass field vars to form,
	# Otherwise, pass looked up record to form
	IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_menu_block($data, $err_entry, '1').$_nl;
		echo $_out;

	} ELSE {
	# Check for valid $_GPV[block_id] no
		IF ($_GPV['block_id']) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['menu_blocks'];
			$query	.= ' WHERE block_id='.$_GPV['block_id'];

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
			$_out .= cp_do_form_add_edit_menu_block($data, $err_entry, 1).$_nl;

		} ELSE {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Menu_Blocks_Editor'];

			$_cstr  = '<table width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" width="30%"><b>'.$_LANG['_ADMIN']['l08_Menu_Blocks_Edit_List'].'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Left_Col_Quick_Select'].'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Right_Col_Quick_Select'].'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_select_form_menu_block('admin.php?cp=menu&op=edit&obj=block', 'block_id', $_GPV['block_id'], '1');
			$_cstr .= cp_do_select_form_menu_item('admin.php?cp=menu&op=edit&obj=item', 'item_id', 0, 0, 1);

		# Add button to add item
			$_cstr .= '<form mehtod="POST" action="admin.php">'.$_nl;
			$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
			$_cstr .= '<input type="hidden" name="op" value="edit">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
			$_cstr .= '<table cellpadding="0" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0SML_NC" width="100%">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="0">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="obj" value="item">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="item_id" value="0">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="block_id" value="0">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_add_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Add_Menu_Item'], 'button_form_h', 'button_form', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</form>'.$_nl;
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '</td>'.$_nl;

			$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_list_menu_links('L', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_list_menu_links('R', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr><td colspan="5">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

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
# Object:		Menu Block
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['obj'] == 'block' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query	 = 'UPDATE '.$_DBCFG['menu_blocks'].' SET ';
		$query	.= "block_pos='".$db_coin->db_sanitize_data($_GPV['block_pos'])."', ";
		$query	.= "block_title='".$db_coin->db_sanitize_data($_GPV['block_title'])."', ";
		$query	.= "block_status='".$db_coin->db_sanitize_data($_GPV['block_status'])."', ";
		$query	.= "block_admin='".$db_coin->db_sanitize_data($_GPV['block_admin'])."', ";
		$query	.= "block_user='".$db_coin->db_sanitize_data($_GPV['block_user'])."', ";
		$query	.= "block_col='".$db_coin->db_sanitize_data($_GPV['block_col'])."' ";
		$query	.= 'WHERE block_id='.$_GPV['block_id'];
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_Menu_Blocks_Entry_Results'], '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_menu_block($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Delete Entry
# Object:		Menu Block
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'delete' && $_GPV['obj'] == 'block' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="admin.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="block">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Message'].$_sp.'='.$_sp.$_GPV['block_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['block_title'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="block_id" value="'.$_GPV['block_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete_mb', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=edit&obj=block&block_id='.$_GPV['block_id'], $_TCFG['_IMG_EDIT_BLOCK_M'],$_TCFG['_IMG_EDIT_BLOCK_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['obj'] == 'block' && $_GPV['stage'] == 2) {
	# Do select
		$query 	= 'DELETE FROM '.$_DBCFG['menu_blocks'].' WHERE block_id='.$_GPV['block_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows ();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


/**************************************************************
 * CP Base Code: Menu Block Item
**************************************************************/
##############################
# Operation: 	Add Entry
# Object: 	Menu Item
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_GPV['op'] == 'add' && $_GPV['obj'] == 'item' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_menu_item($data, $err_entry, '1').$_nl;
		echo $_out;
}


##############################
# Operation:	Add Entry Results
# Object:		Menu Item
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_GPV['op'] == 'add' && $_GPV['obj'] == 'item' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do insert
		$query 	 = 'INSERT INTO '.$_DBCFG['menu_blocks_items'].' (';
		$query 	.= 'block_id, item_id, item_text, item_url';
		$query 	.= ', item_target, item_type, item_status, item_admin, item_user';
		$query	.= ') VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['block_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_id'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_text'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_url'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_target'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_type'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_status'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_admin'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['item_user'])."'";
		$query	.= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_Menu_Block_Items_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_menu_item($data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Edit Entry
# Object:		Menu Item
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['obj'] == 'item' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# If Stage and Error Entry, pass field vars to form,
	# Otherwise, pass looked up record to form
	IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_menu_item($data, $err_entry, '1').$_nl;
		echo $_out;

	} ELSE {
	# Check for valid $_GPV[block_id] and $_GPV[item_id] no's
		IF ($_GPV['block_id'] >= 0 && $_GPV['item_id'] >= 0) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['menu_blocks_items'];
			$query	.= ' WHERE block_id='.$_GPV['block_id'].' AND item_id='.$_GPV['item_id'];

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
			$_out .= cp_do_form_add_edit_menu_item($data, $err_entry, '1').$_nl;

		} ELSE {
		# Content start flag
			$_out = '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Menu_Blocks_Editor'];

			$_cstr  = '<table width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" width="30%"><b>'.$_LANG['_ADMIN']['l08_Menu_Blocks_Edit_List'].'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Left_Col_Quick_Select'].'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" width="30%"><b>'.$_LANG['_ADMIN']['l08_Right_Col_Quick_Select'].'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_select_form_menu_block('admin.php?cp=menu&op=edit&obj=block', 'block_id', $_GPV['block_id'], '1');
			$_cstr .= cp_do_select_form_menu_item('admin.php?cp=menu&op=edit&obj=item', 'item_id', 0, '0', '1');

		# Add button to add item
			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
			$_cstr .= '<input type="hidden" name="op" value="edit">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
			$_cstr .= '<table cellpadding="0" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0SML_NC" width="100%">'.$_nl;
			$_cstr .= '<input type="hidden" name="stage" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
			$_cstr .= '<input type="hidden" name="item_id" value="0">'.$_nl;
			$_cstr .= '<input type="hidden" name="block_id" value="0">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_add_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Add_Menu_Item'], 'button_form_h', 'button_form', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '</td>'.$_nl;

			$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_list_menu_links('L', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="5%">'.$_sp.'</td>'.$_nl;
			$_cstr .= '<td class="TP5SML_NL" valign="top" width="30%">'.$_nl;
			$_cstr .= cp_do_list_menu_links('R', '1');
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr><td colspan="5">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=block', $_TCFG['_IMG_ADD_BLOCK_M'],$_TCFG['_IMG_ADD_BLOCK_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

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
# Object:		Menu Item
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['obj'] == 'item' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query 	 = 'UPDATE '.$_DBCFG['menu_blocks_items'].' SET ';
		$query 	.= "block_id='".$db_coin->db_sanitize_data($_GPV['block_id'])."', ";
		$query 	.= "item_id='".$db_coin->db_sanitize_data($_GPV['item_id'])."', ";
		$query 	.= "item_text='".$db_coin->db_sanitize_data($_GPV['item_text'])."', ";
		$query 	.= "item_url='".$db_coin->db_sanitize_data($_GPV['item_url'])."', ";
		$query 	.= "item_target='".$db_coin->db_sanitize_data($_GPV['item_target'])."', ";
		$query 	.= "item_type='".$db_coin->db_sanitize_data($_GPV['item_type'])."', ";
		$query 	.= "item_status='".$db_coin->db_sanitize_data($_GPV['item_status'])."', ";
		$query 	.= "item_admin='".$db_coin->db_sanitize_data($_GPV['item_admin'])."', ";
		$query 	.= "item_user='".$db_coin->db_sanitize_data($_GPV['item_user'])."' ";
		$query 	.= 'WHERE block_id='.$_GPV['block_id_orig'].' AND item_id='.$_GPV['item_id_orig'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_Menu_Block_Items_Entry_Results'], '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_menu_item($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Delete Entry
# Object:		Menu Item
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'delete' && $_GPV['obj'] == 'item' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Menu_Block_Items_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="menu">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<input type="hidden" name="obj" value="item">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Menu_Block_Items_Message'].$_sp.'='.$_sp.$_GPV['item_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['item_text'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="block_id" value="'.$_GPV['block_id'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="item_id" value="'.$_GPV['item_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete_mbi', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=edit&obj=item&block_id='.$_GPV['block_id'].'&item_id='.$_GPV['item_id'], $_TCFG['_IMG_EDIT_BLOCK_ITEM_M'],$_TCFG['_IMG_EDIT_BLOCK_ITEM_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu&op=add&obj=item&block_id='.$_GPV['block_id'], $_TCFG['_IMG_ADD_BLOCK_ITEM_M'],$_TCFG['_IMG_ADD_BLOCK_ITEM_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['obj'] == 'item' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['menu_blocks_items'].' WHERE block_id='.$_GPV['block_id'].' AND item_id='.$_GPV['item_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Menu_Block_Items_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=menu', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>