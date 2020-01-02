<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Pages
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_pages.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_PAGES']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_PAGES']['Add_Pages_Entry_Results']				= 'Add Pages Entry Results (Inserted ID';
		$_LANG['_PAGES']['Add_To_Pages']						= 'Add To Pages';
		$_LANG['_PAGES']['all_fields_required']					= 'all fields required';
		$_LANG['_PAGES']['auto-assigned']						= 'auto-assigned';
		$_LANG['_PAGES']['Delete_Pages_Entry_Confirmation']		= 'Delete Pages Entry Confirmation';
		$_LANG['_PAGES']['Delete_Pages_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_PAGES']['Delete_Pages_Entry_Results']			= 'Delete Pages Entry Results';
		$_LANG['_PAGES']['Edit_Pages_Entry_Results']				= 'Edit Pages Entry Results';
		$_LANG['_PAGES']['Entry_Deleted']						= 'Entry Deleted.';
		$_LANG['_PAGES']['of']								= 'of';
		$_LANG['_PAGES']['Pages']							= 'Pages';
		$_LANG['_PAGES']['Pages_Editor']						= 'Pages Editor';
		$_LANG['_PAGES']['Pages_Entry']						= 'Pages Entry';
		$_LANG['_PAGES']['Pages_Summary']						= 'Pages Summary';
		$_LANG['_PAGES']['Please_Select']						= 'Please Select';
		$_LANG['_PAGES']['total_entries']						= 'total entries';
		$_LANG['_PAGES']['View_All']							= 'View All';
		$_LANG['_PAGES']['View_Page']							= 'View Page';

# Language Variables: Some Buttons
		$_LANG['_PAGES']['B_Add']							= 'Add';
		$_LANG['_PAGES']['B_Continue']						= 'Continue';
		$_LANG['_PAGES']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_PAGES']['B_Do_It']							= 'Do It';
		$_LANG['_PAGES']['B_Edit']							= 'Edit';
		$_LANG['_PAGES']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_PAGES']['B_Reset']							= 'Reset';
		$_LANG['_PAGES']['B_Save']							= 'Save';
		$_LANG['_PAGES']['B_Send_Email']						= 'Send Email';

# Language Variables: Common Labels (note : on end)
		$_LANG['_PAGES']['l_Admin']							= 'Admin:';
		$_LANG['_PAGES']['l_Block_It']						= 'Block-It:';
		$_LANG['_PAGES']['l_Block_It_Title']					= 'Block-It Title:';
		$_LANG['_PAGES']['l_Code']							= 'Code:';
		$_LANG['_PAGES']['l_Category']						= 'Category:';
		$_LANG['_PAGES']['l_Entries_By_Category']				= 'Entries By Category:';
		$_LANG['_PAGES']['l_Entries_By_Topic']					= 'Entries By Topic:';
		$_LANG['_PAGES']['l_Link_Home']						= 'Link Home:';
		$_LANG['_PAGES']['l_Link_Menu']						= 'Link Menu:';
		$_LANG['_PAGES']['l_Link_Next']						= 'Link Next:';
		$_LANG['_PAGES']['l_Link_Previous']					= 'Link Previous:';
		$_LANG['_PAGES']['l_Page_Id']							= 'Page Id:';
		$_LANG['_PAGES']['l_Pages']							= 'Page(s):';
		$_LANG['_PAGES']['l_Status']							= 'Status:';
		$_LANG['_PAGES']['l_Subject']							= 'Subject:';
		$_LANG['_PAGES']['l_Topic']							= 'Topic:';

# Language Variables:
	# Misc Errors:
		$_LANG['_PAGES']['PG_ERR_NONE_FOUND']					= 'Sorry, no records were found for that selection.';

	# Page: Admin Data Entry error
		$_LANG['_PAGES']['PG_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_PAGES']['PG_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_PAGES']['PG_ERR_ERR01']						= 'Subject';
		$_LANG['_PAGES']['PG_ERR_ERR02']						= 'Topic';
		$_LANG['_PAGES']['PG_ERR_ERR03']						= 'Category';
		$_LANG['_PAGES']['PG_ERR_ERR04']						= 'Code';


?>