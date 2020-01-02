<?php
/**
 * Module: ToDo (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- todo is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage ToDo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_todo.php
 */


# Code to handle file being loaded by URL
	IF (eregi('todo_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=todo');
		exit();
	}


/**************************************************************
* Module Admin Functions
**************************************************************/
function do_list_todo_table($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_SERVER, $_GPV, $_nl, $_sp;

	# Set Query for select
		$_where = '';
		$query .= 'SELECT *';
		$query .= ' FROM '.$_DBCFG['todo'];

		IF (!isset($_GPV['todo_completed']) && strpos($_SERVER['HTTP_REFERER'], 'mod=todo') === FALSE) {$_GPV['todo_completed'] = 0;}
		IF (isset($_GPV['todo_completed'])) {$_where .= 'todo_completed='.$_GPV['todo_completed'];}
			IF ($_GPV['todo_admin'] > 0) {
			IF ($_where) {$_where .= ' AND ';}
			$_where .= 'todo_admin_id='.$_GPV['todo_admin'];
		}
		IF ($_where) {$_where = ' WHERE '.$_where;}

		$_sort  = ' ORDER BY todo_duedate ASC, todo_priority ASC';

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['todo'];
		$query_ttl .= $_where;
		$result_ttl		= $db_coin->db_query_execute($query_ttl);
		while(list($cnt)	= $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next - is page loading first record number
	# $_rec_start - is a given page start record (which will be rec_next)
	# $_rec_page = $_CCFG['TODO_NUM_PAGES'];
		IF (!isset($_CCFG['IPP_TODO'])) {$_CCFG['IPP_TODO'] = 10;}
		$_rec_page = $_CCFG['IPP_TODO'];
		$_rec_next = $adata['rec_next'];
		IF (!$_rec_next) { $_rec_next=0; }

	# Range of records on current page
		$_rec_next_lo = $_rec_next+1;
		$_rec_next_hi = $_rec_next+$_rec_page;
		IF ($_rec_next_hi > $numrows_ttl) {$_rec_next_hi = $numrows_ttl;}

	# Calc no pages,
		$_num_pages = round(($numrows_ttl/$_rec_page), 0);
		IF ($_num_pages < ($numrows_ttl/$_rec_page)) {$_num_pages = $_num_pages+1;}

	# Loop Array and Print Out Page Menu HTML
		$_page_menu = $_LANG['_TODO']['l_Pages'].$_sp;
		FOR ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = ( ($i*$_rec_page)-$_rec_page);
			IF ( $_rec_start == $_rec_next ) {
			# Loading Page start record so no link for this page.
				$_page_menu .= "$i";
			} ELSE {
				$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=todo'.$_link_xtra.'&rec_next='.$_rec_start.'">'.$i.'</a>';
			}
			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query  .= $_where.$_sort." LIMIT $_rec_next, $_rec_page";
		$result  = $db_coin->db_query_execute($query);
		$numrows = $db_coin->db_query_numrows($result);

	# Build form output
		IF ($_GPV['todo_admin']) {$_admin = '&todo_admin='.$_GPV['todo_admin'];} ELSE {$_admin = '';}

	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<div align="left">'.$_nl;
			$_out .= '&nbsp;&nbsp;&nbsp;'.$_nl;
			$_out .= '[<a href=mod.php?mod=todo'.$_admin.'>All</a>]'.$_sp.$_nl;
			$_out .= '[<a href=mod.php?mod=todo&todo_completed=0'.$_admin.'>Open</a>]'.$_sp.$_nl;
			$_out .= '[<a href=mod.php?mod=todo&todo_completed=1'.$_admin.'>Completed</a>]'.$_sp.$_nl;
			$_out .= '</div>';
		}

	# Build the table
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<br><br><table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="7">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_TODO']['TITLE'].$_sp.'</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;

		IF (!isset($_GPV['todo_completed'])) {
			$_out .= '<td class="TP3SML_NL" valign="top" width="25%"><b>'.$_LANG['_TODO']['Status'].'</b></td>'.$_nl;
		}
		$_out .= '<td class="TP3SML_NL" valign="top" width="25%"><b>'.$_LANG['_TODO']['due_date'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NC" valign="top" width="5%"><b>'.$_LANG['_TODO']['priority'].'</b></td>'.$_nl;
		$_out .= '<td class="TP3SML_NL" valign="top" width="30%"><b>'.$_LANG['_TODO']['title'].'</b></td>'.$_nl;
		IF (!$_GPV['todo_admin']) {
			$_out .= '<td class="TP3SML_NL" valign="top" width="25%"><b>'.$_LANG['_TODO']['assigned_to'].'</b></td>'.$_nl;
		} ELSE {
			$_out .= '<td class="TP3SML_NL" valign="top" width="25%"><b>'.$_LANG['_TODO']['entered_on'].'</b></td>'.$_nl;
		}
		$_out .= '<td class="TP3SML_NL" valign="top" width="15%"><b>'.$_LANG['_TODO']['actions'].'</b></td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				IF (!isset($_GPV['todo_completed'])) {
					$_out .= '<td class="TP3SML_NC">'.$_LANG['_TODO']['STATUS'][$row['todo_completed']].'</td>'.$_nl;
				}
				$_out .= '<td class="TP3SML_NL">'.dt_make_datetime($row['todo_duedate'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM'] ).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$_LANG['_TODO']['PRIO'][$row['todo_priority']].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.htmlspecialchars($row['todo_title']).'</td>'.$_nl;
				IF (!$_GPV['todo_admin']) {
					$_out .= '<td class="TP3SML_NL">'.get_user_name($row['todo_admin_id'], 'admin').'</td>'.$_nl;
				} ELSE {
					$_out .= '<td class="TP3SML_NL">'.dt_make_datetime($row['todo_entered'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
				}
				$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=view&todo_id='.$row['todo_id'], $_TCFG['_S_IMG_VIEW_S'], $_TCFG['_S_IMG_VIEW_S_MO'], '', '');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP12'] == 1) {
					$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=edit&todo_id='.$row['todo_id'], $_TCFG['_S_IMG_EDIT_S'], $_TCFG['_S_IMG_EDIT_S_MO'], '', '');
					$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=delete&stage=1&todo_id='.$row['todo_id'], $_TCFG['_S_IMG_DEL_S'], $_TCFG['_S_IMG_DEL_S_MO'], '', '');
				}
				$_out .= '</nobr></td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

	# Closeout
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="7">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}

}



function do_add_todo_form($adata, $aret_flag = 0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_SEC, $_nl, $_sp;

	# Some HTML Strings (reduce text)
		$_td_str_span = '<td class="TP3SML_BC" colspan="2">';
		$_td_str_left = '<td class="TP3SML_NR" width="30%">';
		$_td_str_right = '<td class="TP3SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		# IF (empty($adata['todo_admin_id'])) {$adata['todo_admin_id'] = $_SESSION['_sadmin_id'];}

		$_tstr .= $_blk_title;
		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<form action="mod.php" method="post" name="todo">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="todo">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="step" value="2">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="1">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['due_date_b'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF (!$adata['todo_duedate']) {$adata['todo_duedate'] = time();}
		$_cstr .= do_datetime_edit_list('todo_duedate', $adata['todo_duedate'], 1).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['assigned_to'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		If (!$adata['todo_admin_id']) {$adata['todo_admin_id'] = $_SEC['_sadmin_id'];}
		$_cstr .= $_out .= do_select_list_admin('todo_admin_id', $adata['todo_admin_id'], 1 ).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['completed'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_no_yes('todo_completed', $adata['todo_completed'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['priority'].$_sp.':</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .=  do_select_priority('todo_priority', $adata['todo_priority'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['title'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" type="text" name="todo_title" size="50" maxlength="255" value="'.htmlspecialchars($adata['todo_title']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['desc'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" name="todo_text" cols="60" rows="10">'.htmlspecialchars($adata['todo_text']).'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_submit', 'SUBMIT', $_LANG['_TODO']['b_submit'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_TODO']['b_reset'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '<input type="hidden" name="todo_id" value="'.$adata['todo_id'].'">'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td align="right">'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

	# Return / Echo Final Output
		IF ( $aret_flag ) { return $_cstr; } ELSE { echo $_out; }

}


function do_view_todo_entry($adata, $aret_flag = 0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_SEC, $_nl, $_sp;

	# Some HTML Strings (reduce text)
		$_td_str_span = '<td class="TP3SML_BC" colspan="2">';
		$_td_str_left = '<td class="TP3SML_NR" width="30%">';
		$_td_str_right = '<td class="TP3SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_blk_title;

		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="1">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['due'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= dt_make_datetime($adata['todo_duedate'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['priority'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $_LANG['_TODO']['PRIO'][$adata['todo_priority']].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['title'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['todo_title'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['assigned_to'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= get_user_name($adata['todo_admin_id'],'admin').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_TODO']['desc'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= nl2br(htmlspecialchars($adata['todo_text'])).$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td align="right">'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

	# Return / Echo Final Output
		IF ($aret_flag) {return $_cstr;} ELSE {echo $_out;}

}


# Do list select field for: Admin
function do_select_list_admin($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select records for list.
		$query  = 'SELECT admin_id, admin_name_first, admin_name_last FROM '.$_DBCFG['admins'];
		$query .= ' ORDER BY admin_name_last ASC, admin_name_first ASC';

	# Do select and return check
		$result  = $db_coin->db_query_execute($query);
		$numrows = $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<select class="select_form" name="'.$aname.'">'.$_nl;
		$_out .= '<option value="'.$_SESSION['_sadmin_id'].'">'.$_LANG['_TODO']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($admin_id, $admin_name_first, $admin_name_last) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$admin_id.'"';
			IF ($admin_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$admin_name_last.', '.$admin_name_first.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Get user info
function get_user_name($auser_id, $aw) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select
		IF ($aw == 'admin') {
			$query = 'SELECT admin_name_first, admin_name_last FROM '.$_DBCFG['admins'].' WHERE admin_id='.$auser_id.' ORDER BY admin_id ASC';
		}
		IF ($aw == 'user') {
			$query = 'SELECT cl_name_first, cl_name_last FROM '.$_DBCFG['clients'].' WHERE cl_id='.$auser_id.' ORDER BY cl_id ASC';
		}

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get value and set return
		while(list($_name_first, $_name_last) = $db_coin->db_fetch_row($result)) {
			$_name = $_name_first.$_sp.$_name_last;
		}

		return $_name;
}


function do_select_priority($aname, $adata, $aret_flag=0) {
	global $_LANG;
	$_out  = '<select class="select_form" name="'.$aname.'" size="1">'.$_nl;
	FOREACH($_LANG['_TODO']['PRIO'] as $k => $v) {
		$_out .= '<option value="'.$k.'"';
		IF ($k == $adata) {$_out .= ' selected';}
		$_out .= '>'.$v;
		$_out .= '</option>'.$_nl;
	}
	$_out .= '</select>'.$_nl;
	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


function do_tstr_todo_action_list($atitle, $avalue) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select records for list.
		$query  = "SELECT admin_id,admin_name_first,admin_name_last FROM ".$_DBCFG['admins'];
		$query .= " ORDER BY admin_name_first ASC";

	# Do select and return check
		$result  = $db_coin->db_query_execute($query);
		$numrows = $db_coin->db_query_numrows($result);

	# Search form
		$_sform  .= '<form method="GET" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_sform .= 'Please Select:'.$_sp.$_nl;
		$_sform .= '<input type="hidden" name="mod" value="todo">'.$_nl;
		$_sform .= '<select class="select_form" name="todo_admin" size="1" value="Action" onchange="submit();">'.$_nl;
		$_sform .= '<option value="'.$_SESSION['_sadmin_id'].'">'.$_LANG['_TODO']['Please_Select'].'</option>'.$_nl;
		$_sform .= '<option value="0">'.$_LANG['_BASE']['All'].'</option>'.$_nl;

	# Process query results
		while(list($admin_id, $admin_name_first, $admin_name_last) = $db_coin->db_fetch_row($result)) {
			$_sform .= '<option value="'.$admin_id.'"';
			IF ($admin_id == $avalue) {$_sform .= ' selected';}
			$_sform .= '>'.$admin_name_first.' '.$admin_name_last.'</option>'.$_nl;
		}

		$_sform .= '</select>'.$_nl;
		$_sform .= '</FORM>'.$_nl;

		$_tstr .= '<table width="100%" cellpadding="0" cellspacing="0"><tr class="BLK_IT_TITLE_TXT">';
		$_tstr .= '<td class="TP0MED_BL" valign="top">'.$_nl.$atitle.$_nl.'</td>'.$_nl;

		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_tstr .= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sform.$_nl.'</td>'.$_nl;
		} ELSE {
			$_tstr  .= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sp.$_sp.$_nl.'</td>'.$_nl;
		}

		$_tstr .= '</tr></table>';

	# Build form output
		return $_tstr;

}

/**************************************************************
* End Module Admin Functions
**************************************************************/
?>