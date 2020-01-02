<?php
/**
 * Module: ToDo (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=todo');
		exit();
	}


# Get security vars
	$_SEC = get_security_flags();

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_todo.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_todo_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_todo_override.php');
	}

# Include journal functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV['mode'] and set default
	switch($_GPV['mode']) {
		case "list":
			break;
		case "view":
			break;
		case "add":
			break;
		case "delete":
			break;
		case "edit":
			break;
		default:
 			$_GPV['mode'] = "list";
			break;
}

##############################
# Mode Call:         All modes
# Summary:
#        - Check if login required
##############################
IF (!$_SEC['_sadmin_flg']) {
# Set login flag
	$_login_flag = 1;

# Call function for articles listings
	$_out  = '<!-- Start content -->'.$_nl;
	$_out .= do_login($data, 'admin', '1').$_nl;

# Echo final output
	echo $_out;
}



# Call display list
IF (!$_login_flag && $_GPV['mode'] == 'list') {
	$_t1 = $_LANG['_TODO']['TODO_TITLE'].': ';
	IF (!isset($_GPV['todo_completed']) && strpos($_SERVER['HTTP_REFERER'], 'mod=todo') !== FALSE) {
		$_t1 .= $_LANG['_TODO']['NO_STATUS'];
	} ELSE {
		IF (!isset($_GPV['todo_completed'])) {$_GPV['todo_completed'] = 0;}
		$_t1 .= $_LANG['_TODO']['STATUS'][$_GPV['todo_completed']];
	}
	$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=add', $_TCFG['_IMG_ADD_NEW_M'],'');
	$_tstr  = do_tstr_todo_action_list($_t1, $_GPV['todo_admin']);
	$return = do_list_todo_table($_GPV,1);
	$_out .= do_mod_block_it($_tstr, $return, '1', $_mstr, '1');
	echo $_out;
}



IF (!$_login_flag && $_GPV['mode'] == 'view') {
	$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=add', $_TCFG['_IMG_ADD_NEW_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=edit&todo_id='.$_GPV['todo_id'], $_TCFG['_IMG_EDIT_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo', $_TCFG['_IMG_LISTING_M'],'');
	$_tstr  = $_LANG['_TODO']['view_todo'];

	$query	= 'SELECT * FROM '.$_DBCFG['todo'].' WHERE todo_id='.$_GPV['todo_id'];
	$result	= $db_coin->db_query_execute($query);
	$data	= $db_coin->db_fetch_array($result);
	$return	= do_view_todo_entry($data, 1);
	$_out .= do_mod_block_it($_tstr, $return, '1', $_mstr, '1');
	echo $_out;
}




IF (!$_login_flag && $_GPV['mode'] == 'add') {
	$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo', $_TCFG['_IMG_LISTING_M'],'');
	IF ($_GPV['step'] != 2) {
		$_tstr = $_LANG['_TODO']['Add_Todo'];
		$data['mode'] = 'add';
		$_ret = do_add_todo_form($_GPV, 1);
	} ELSE {
		$_uts = dt_get_uts();
		$_due_date = mktime($_GPV['todo_duedate_hour'], $_GPV['todo_duedate_minute'], $_GPV['todo_duedate_second'],
		$_GPV['todo_duedate_month'], $_GPV['todo_duedate_day'], $_GPV['todo_duedate_year']);
		$_tstr = $_LANG['_TODO']['Add_Todo'];
		$query  = 'INSERT INTO ' . $_DBCFG['todo'] . ' (';
		$query .= 'todo_admin_id, todo_title, todo_completed, todo_priority, todo_text, todo_duedate, todo_entered';
		$query .= ') VALUES (';
		$query .= $_GPV['todo_admin_id'].", ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['todo_title'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['todo_completed'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['todo_priority'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['todo_text'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_due_date)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_uts)."')";
		$result = $db_coin->db_query_execute($query);
		$_recnum = $db_coin->db_query_insertid();

		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['todo'].' WHERE todo_id='.$_recnum;
		$result  = $db_coin->db_query_execute($query);
		$data = $db_coin->db_fetch_array($result);
		$_ret = do_view_todo_entry($data, 1);
		$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=todo', $_TCFG['_IMG_LISTING_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=edit&todo_id='.$_recnum, $_TCFG['_IMG_EDIT_M'],'');
	}
	$_out .= do_mod_block_it($_tstr, $_ret, '1', $_mstr, '1');
	echo $_out;
}


IF (!$_login_flag && $_GPV['mode'] == 'edit') {
	$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=add', $_TCFG['_IMG_ADD_NEW_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo', $_TCFG['_IMG_LISTING_M'],'');
	IF ($_GPV['step'] != 2) {
		$query = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['todo'].' WHERE todo_id='.$_GPV['todo_id'];
		$result  = $db_coin->db_query_execute($query);
		$data = $db_coin->db_fetch_array($result);
		$data['mode'] = 'edit';
		$_tstr = $_LANG['_TODO']['Edit_Todo'];
		$_ret = do_add_todo_form($data, 1);
	} ELSE {
		$_due_date = mktime($_GPV['todo_duedate_hour'], $_GPV['todo_duedate_minute'], $_GPV['todo_duedate_second'],
		$_GPV['todo_duedate_month'], $_GPV['todo_duedate_day'], $_GPV['todo_duedate_year']);
		$query  = 'UPDATE ' . $_DBCFG['todo'].' SET ';
		$query .= 'todo_admin_id='.$_GPV['todo_admin_id'].', ';
		$query .= 'todo_completed='.$_GPV['todo_completed'].', ';
		$query .= 'todo_priority='.$_GPV['todo_priority'].', ';
		$query .= "todo_title='".$db_coin->db_sanitize_data($_GPV['todo_title'])."', ";
		$query .= "todo_text='".$db_coin->db_sanitize_data($_GPV['todo_text'])."', ";
		$query .= "todo_duedate='".$db_coin->db_sanitize_data($_due_date)."' ";
		$query .= 'WHERE todo_id='.$_GPV['todo_id'];
		$result = $db_coin->db_query_execute($query);
		$_tstr = $_LANG['_TODO']['view_todo'];

		$query = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['todo'].' WHERE todo_id='.$_GPV['todo_id'];
		$result  = $db_coin->db_query_execute($query);
		$data = $db_coin->db_fetch_array($result);
		$_ret = do_view_todo_entry($data, 1);
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=edit&todo_id='.$_GPV['todo_id'], $_TCFG['_IMG_EDIT_M'],'');
	}
	$_out .= do_mod_block_it($_tstr, $_ret, '1', $_mstr, '1');
	echo $_out;
}



##############################
# Mode Call: Delete Entry
# Summary Stage 1:
#        - Confirm delete entry
# Summary Stage 2:
#        - Do table update
#        - Display results
##############################
IF (!$_login_flag && $_GPV['mode'] == 'delete' && $_GPV['stage'] != 2) {
# Content start flag
	$_out .= '<!-- Start content -->'.$_nl;

# Build Title String, Content String, and Footer Menu String
	$_tstr = $_LANG['_TODO']['delete_confirm'];

# Do confirmation form to content string
	$_cstr = '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
	$_cstr .= '<input type="hidden" name="mod" value="todo">'.$_nl;
	$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
	$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
	$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
	$_cstr .= '<b>'.$_LANG['_TODO']['delete_msg'].'='.$_sp.$_GPV['todo_id'].'</b>'.$_nl;
	$_cstr .= '</td></tr>'.$_nl;
	$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
	$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
	$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
	$_cstr .= '<INPUT TYPE=hidden name="todo_id" value="'.$_GPV['todo_id'].'">'.$_nl;
	$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_TODO']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
	$_cstr .= '</td></tr>'.$_nl;
	$_cstr .= '</table>'.$_nl;
	$_cstr .= '</FORM>'.$_nl;
	$_mstr_flag        = '1';
	$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=list', $_TCFG['_IMG_LISTING_M'],'');

# Call block it function
	$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
	$_out .= '<br>'.$_nl;

# Echo final output
	echo $_out;
}


IF (!$_login_flag && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
# Do query for transactions
	$query	= 'DELETE FROM '.$_DBCFG['todo'].' WHERE todo_id='.$_GPV['todo_id'];
	$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

# Content start flag
	$_out .= '<!-- Start content -->'.$_nl;

# Build Title String, Content String, and Footer Menu String
	$_tstr = $_LANG['_TODO']['todo_deleted_title'];
	$_cstr .= '<center>'.$_LANG['_TODO']['todo_deleted'].'<br></center>';
	$_mstr_flag       = '1';
	$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
	$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=todo&mode=list', $_TCFG['_IMG_LISTING_M'],'');

# Call block it function
	$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
	$_out .= '<br>'.$_nl;

# Echo final output
	echo $_out;
}

?>