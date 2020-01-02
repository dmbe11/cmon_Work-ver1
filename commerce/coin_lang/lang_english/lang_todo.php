<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- todo is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage ToDo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_todo.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit();
	}



/**************************************************************
 * Language Variables
**************************************************************/

# Permissions
	$_LANG['_TODO']['perms_no_view']		= 'You do not have permission to view to do items';
	$_LANG['_TODO']['perms_no_add']		= 'You do not have permission to add to do items';
	$_LANG['_TODO']['perms_no_edit']		= 'You do not have permission to edit to do items';
	$_LANG['_TODO']['perms_no_delete']		= 'You do not have permission to delete to do items';

# Language Variables: todo
	$_LANG['_TODO']['l_Pages']			= 'Page(s)';
	$_LANG['_TODO']['TODO_TITLE']			= 'To Do';
	$_LANG['_TODO']['priority']			= 'Priority';
	$_LANG['_TODO']['due_date']			= 'Due Date';
	$_LANG['_TODO']['due_date_b']			= 'Due Date:<br><br><br>Time:';
	$_LANG['_TODO']['due']				= 'Due:';
	$_LANG['_TODO']['completed']			= 'Completed:';
	$_LANG['_TODO']['title']				= 'Title:';
	$_LANG['_TODO']['assigned_to']		= 'Assigned To:';
	$_LANG['_TODO']['entered_on']			= 'Entered On';
	$_LANG['_TODO']['actions']			= 'Actions';
	$_LANG['_TODO']['desc']				= 'Description:';
	$_LANG['_TODO']['Add_Todo']			= 'Add To Do';
	$_LANG['_TODO']['Edit_Todo']			= 'Edit To Do Entry';
	$_LANG['_TODO']['view_todo']			= 'View To Do Entry';
	$_LANG['_TODO']['delete_confirm']		= 'Delete To Do Entry?';
	$_LANG['_TODO']['delete_msg']			= 'Are You Sure You Want to Delete Entry ID ';
	$_LANG['_TODO']['Please_Select']		= 'Please Select';

	$_LANG['_TODO']['b_submit']			= 'Submit';
	$_LANG['_TODO']['b_reset']			= 'Reset';
	$_LANG['_TODO']['B_Delete_Entry']		= 'Delete Entry';

	$_LANG['_TODO']['todo_deleted_title']	= 'To Do Delete Results';
	$_LANG['_TODO']['todo_deleted']		= 'To Do entry deleted';

	$_LANG['_TODO']['PRIO'][1]			= 'High';
	$_LANG['_TODO']['PRIO'][2]			= 'Medium';
	$_LANG['_TODO']['PRIO'][3]			= 'Low';

	$_LANG['_TODO']['STATUS'][0]			= 'Open';
	$_LANG['_TODO']['STATUS'][1]			= 'Completed';
	$_LANG['_TODO']['NO_STATUS']			= 'All';
	$_LANG['_TODO']['Status']			= 'Status';

?>