<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage FAQ
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_faq.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_FAQ']['all_fields_required']				= 'all fields required';
		$_LANG['_FAQ']['An_error_occurred']				= 'An error occurred.';
		$_LANG['_FAQ']['Convert_New_Line_2_Break']			= 'Convert New Line To HTML Break';
		$_LANG['_FAQ']['Error_Not_Found']					= 'Sorry, no records were found for that selection.';
		$_LANG['_FAQ']['FAQ']							= 'FAQ';
		$_LANG['_FAQ']['FAQ_Answers']						= 'FAQ Answers';
		$_LANG['_FAQ']['FAQ_Entry']						= 'FAQ Entry';
		$_LANG['_FAQ']['FAQ_QA_Entry']					= 'FAQ QA Entry';
		$_LANG['_FAQ']['FAQ_Summary']						= 'FAQ Summary';
		$_LANG['_FAQ']['questions']						= 'questions';
		$_LANG['_FAQ']['Select_FAQ']						= 'Select FAQ';
		$_LANG['_FAQ']['Select_FAQ_QA']					= 'Select FAQ QA';
		$_LANG['_FAQ']['View_All']						= 'View All';
		$_LANG['_FAQ']['View_FAQ_QA']						= 'View FAQ QA';

# Language Variables: Some Buttons
		$_LANG['_FAQ']['B_Add']							= 'Add';
		$_LANG['_FAQ']['B_Continue']						= 'Continue';
		$_LANG['_FAQ']['B_Delete_Entry']					= 'Delete Entry';
		$_LANG['_FAQ']['B_Edit']							= 'Edit';
		$_LANG['_FAQ']['B_Load_Entry']					= 'Load Entry';
		$_LANG['_FAQ']['B_Reset']						= 'Reset';
		$_LANG['_FAQ']['B_Save']							= 'Save';
		$_LANG['_FAQ']['B_Send_Email']					= 'Send Email';

# Language Variables: Common Labels (note : on end)

		$_LANG['_FAQ']['l_Admin_FAQ']						= 'Admin FAQ:';
		$_LANG['_FAQ']['l_Answer']						= 'Answer:';
		$_LANG['_FAQ']['l_Conver_NL2BR']					= 'Convert NL2BR:';
		$_LANG['_FAQ']['l_Description']					= 'Description:';
		$_LANG['_FAQ']['l_FAQ_ID']						= 'FAQ ID:';
		$_LANG['_FAQ']['l_FAQ_Title']						= 'FAQ Title:';
		$_LANG['_FAQ']['l_Last_Modified:']					= 'Last Modified:';
		$_LANG['_FAQ']['l_Position']						= 'Position:';
		$_LANG['_FAQ']['l_FAQ_Question']					= 'FAQ Question:';
		$_LANG['_FAQ']['l_Status']						= 'Status:';
		$_LANG['_FAQ']['l_Title']						= 'Title';
		$_LANG['_FAQ']['l_User_FAQ']						= 'User FAQ:';

# Language Variables: index.php
		$_LANG['_FAQ']['Add_FAQ']						= 'Add FAQ';
		$_LANG['_FAQ']['Add_FAQ_QA']						= 'Add FAQ QA';
		$_LANG['_FAQ']['Delete_FAQ_Entry_Confirmation']		= 'Delete FAQ Entry Confirmation';
		$_LANG['_FAQ']['Delete_FAQ_QA_Entry_Confirmation']	= 'Delete FAQ QA Entry Confirmation';
		$_LANG['_FAQ']['Delete_FAQ_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_FAQ']['Delete_FAQ_QA_Entry_Message']		= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_FAQ']['Delete_FAQ']						= 'Delete FAQ';
		$_LANG['_FAQ']['Delete_FAQ_QA']					= 'Delete FAQ QA';
		$_LANG['_FAQ']['Edit_FAQ']						= 'Edit FAQ';
		$_LANG['_FAQ']['Edit_FAQ_QA']						= 'Edit FAQ QA';
		$_LANG['_FAQ']['Entry_Deleted']					= 'Entry Deleted.';
		$_LANG['_FAQ']['Entry_Results']					= 'Entry Results';
		$_LANG['_FAQ']['FAQ_Editor']						= 'FAQ Editor';
		$_LANG['_FAQ']['FAQ_QA_Editor']					= 'FAQ QA Editor';
		$_LANG['_FAQ']['Inserted_ID']						= 'Inserted ID';

# Page: Admin Data Entry error
		$_LANG['_FAQ']['FAQ_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_FAQ']['FAQ_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_FAQ']['FAQ_ERR_ERR01']					= 'Position';
		$_LANG['_FAQ']['FAQ_ERR_ERR02']					= 'Title';
		$_LANG['_FAQ']['FAQ_ERR_ERR03']					= 'Description';
		$_LANG['_FAQ']['FAQ_ERR_ERR04']					= 'future';
		$_LANG['_FAQ']['FAQ_ERR_ERR05']					= 'future';
		$_LANG['_FAQ']['FAQ_ERR_ERR06']					= 'FAQ ID';
		$_LANG['_FAQ']['FAQ_ERR_ERR07']					= 'Position';
		$_LANG['_FAQ']['FAQ_ERR_ERR08']					= 'Question';
		$_LANG['_FAQ']['FAQ_ERR_ERR09']					= 'Answer';
		$_LANG['_FAQ']['FAQ_ERR_ERR10']					= 'future';
		$_LANG['_FAQ']['FAQ_ERR_ERR11']					= 'xxx';
		$_LANG['_FAQ']['FAQ_ERR_ERR12']					= 'xxx';
		$_LANG['_FAQ']['FAQ_ERR_ERR13']					= 'xxx';
		$_LANG['_FAQ']['FAQ_ERR_ERR14']					= 'xxx';
		$_LANG['_FAQ']['FAQ_ERR_ERR15']					= 'xxx';
?>