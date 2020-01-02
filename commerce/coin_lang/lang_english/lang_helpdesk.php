<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage HelpDesk
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_helpdesk.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_HDESK']['adding_a_message_to_ticket']			= 'adding a message to ticket';
		$_LANG['_HDESK']['Admins_Not_Permitted']				= 'Administrators are not permitted to create tickets.';
		$_LANG['_HDESK']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_HDESK']['auto-assigned']						= 'auto-assigned';
		$_LANG['_HDESK']['Client']							= 'Client';
		$_LANG['_HDESK']['Client_Support_Tickets']				= 'Client Support Tickets';
		$_LANG['_HDESK']['denotes_optional_items']				= 'denotes optional items';
		$_LANG['_HDESK']['Helpdesk_Support_Ticket']				= 'Helpdesk Support Ticket';
		$_LANG['_HDESK']['manually_requesting_a_copy']			= 'manually requesting a copy';
		$_LANG['_HDESK']['of']								= 'of';
		$_LANG['_HDESK']['Open_New']							= 'Open New';
		$_LANG['_HDESK']['Please_Select']						= 'Please Select';
		$_LANG['_HDESK']['Rate_Ticket']						= 'Rate Ticket';
		$_LANG['_HDESK']['Support']							= 'Support';
		$_LANG['_HDESK']['Support_Ticket_Messages']				= 'Support Ticket Messages';
		$_LANG['_HDESK']['total_entries']						= 'total entries';
		$_LANG['_HDESK']['unrated']							= 'unrated';
		$_LANG['_HDESK']['Welcome']							= 'Welcome';

# Language Variables: For Entry Select List of Closed Field Status
		$_LANG['_HDESK']['Select_Closed']						= 'Closed';
		$_LANG['_HDESK']['Select_Open']						= 'Open';

# Language Variables: Some Buttons
		$_LANG['_HDESK']['B_Add']							= 'Add';
		$_LANG['_HDESK']['B_Continue']						= 'Continue';
		$_LANG['_HDESK']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_HDESK']['B_Edit']							= 'Edit';
		$_LANG['_HDESK']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_HDESK']['B_Reset']							= 'Reset';
		$_LANG['_HDESK']['B_Save']							= 'Save';
		$_LANG['_HDESK']['B_Send_Email']						= 'Send Email';
		$_LANG['_HDESK']['B_Submit']							= 'Submit';

# Language Variables: Common Labels (note : on end)
		$_LANG['_HDESK']['l_Category']						= 'Category:';
		$_LANG['_HDESK']['l_Client']							= 'Client:';
		$_LANG['_HDESK']['l_Client_ID']						= 'Client ID:';
		$_LANG['_HDESK']['l_Client_TT_Email']					= 'Client TT Email:';
		$_LANG['_HDESK']['l_Closed']							= 'Closed:';
		$_LANG['_HDESK']['l_Closed_Flag']						= 'Closed Flag:';
		$_LANG['_HDESK']['l_Date']							= 'Date:';
		$_LANG['_HDESK']['l_Date_Created']						= 'Date Created:';
		$_LANG['_HDESK']['l_Domain']							= 'Domain:';
		$_LANG['_HDESK']['l_Domain_NReq']						= 'Domain (*):';
		$_LANG['_HDESK']['l_Example_URL']						= 'Example URL:';
		$_LANG['_HDESK']['l_Example_URL_NReq']					= 'Example URL (*):';
		$_LANG['_HDESK']['l_Id']								= 'Id:';
		$_LANG['_HDESK']['l_Message']							= 'Message:';
		$_LANG['_HDESK']['l_Pages']							= 'Page(s):';
		$_LANG['_HDESK']['l_Primary_Information']				= 'Primary Information:';
		$_LANG['_HDESK']['l_Priority']						= 'Priority:';
		$_LANG['_HDESK']['l_Rate_Ticket']						= 'Rate Ticket:';
		$_LANG['_HDESK']['l_Status']							= 'Status:';
		$_LANG['_HDESK']['l_Server']							= 'Server:';
		$_LANG['_HDESK']['l_Subject']							= 'Subject:';
		$_LANG['_HDESK']['l_Ticket_Id']						= 'Ticket Id:';
		$_LANG['_HDESK']['l_User_Name']						= 'User Name:';

# Language Variables: index.php
		$_LANG['_HDESK']['Administration']						= 'Administration';
		$_LANG['_HDESK']['HelpDesk_View_Support_Ticket']			= 'Helpdesk View Support Ticket';
		$_LANG['_HDESK']['HelpDesk_Support_Ticket_Summary']		= 'HelpDesk Support Ticket Summary';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Confirmation']		= 'Delete HelpDesk Entry Confirmation';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Message_Cont']		= 'and all the associated helpdesk messages?';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results']			= 'Delete HelpDesk Entry Results';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_01']		= 'The following helpdesk items deleted';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_02']		= 'Deleted helpdesk ticket messages';
		$_LANG['_HDESK']['Delete_HelpDesk_Entry_Results_03']		= 'Deleted helpdesk tickets';

		$_LANG['_HDESK']['eMail_Ticket_Confirmation']			= 'eMail HelpDesk Ticket Confirmation';
		$_LANG['_HDESK']['eMail_Ticket_Message_prefix']			= 'Are You Sure You Want to eMail HelpDesk Ticket ID';
		$_LANG['_HDESK']['eMail_Ticket_Message_suffix']			= 'to the client?';

# Language Variables: Email Ticket (helpdesk_funcs.php:function do_mail_helpdesk_tt())
	# Caution- padded spaces are needed for email items to line up
		$_LANG['_HDESK']['HelpDesk_Alert']						= 'HelpDesk Alert';

		$_LANG['_HDESK']['HD_EMAIL_01']						= 'Message By:  ';
		$_LANG['_HDESK']['HD_EMAIL_02']						= 'When:        ';
		$_LANG['_HDESK']['HD_EMAIL_03']						= 'Message (below):';
		$_LANG['_HDESK']['HD_EMAIL_04']						= '';
		$_LANG['_HDESK']['HD_EMAIL_05']						= '';

		$_LANG['_HDESK']['HD_EMAIL_SUBJECT_PRE']				= '- Ticket';
		$_LANG['_HDESK']['HD_EMAIL_SUBJECT_SUF']				= 'Updated';

		$_LANG['_HDESK']['HD_EMAIL_MSG_01_PRE']					= 'HelpDesk Ticket ID:';
		$_LANG['_HDESK']['HD_EMAIL_MSG_01_SUF']					= 'not located.';
		$_LANG['_HDESK']['HD_EMAIL_MSG_02_PRE']					= 'HelpDesk Ticket ID:';
		$_LANG['_HDESK']['HD_EMAIL_MSG_02_SUF']					= 'messages not located.';

		$_LANG['_HDESK']['HD_EMAIL_MSG_03_L1']					= 'An error has occurred, Please try again.';
		$_LANG['_HDESK']['HD_EMAIL_MSG_03_L2']					= 'If problem continues, contact support via contact form.';
		$_LANG['_HDESK']['HD_EMAIL_MSG_04_PRE']					= 'The HelpDesk Ticket ID';
		$_LANG['_HDESK']['HD_EMAIL_MSG_04_SUF']					= 'email has been sent.';
		$_LANG['_HDESK']['HD_EMAIL_RESULT_TITLE']				= 'eMail Results: Helpdesk Support Ticket';

		$_LANG['_HDESK']['HD_EMAIL_MSGS_LIMIT_STRING']			= 'The following is a listing of the most recent HelpDesk Support Ticket messages:';
		$_LANG['_HDESK']['HD_EMAIL_MSGS_NO_LIMIT_STRING']			= 'The following is a listing of HelpDesk Support Ticket messages to date:';

# Page: Data Entry and errors
		$_LANG['_HDESK']['HD_ADD_NEW_TXT_HDR']					= 'Support Ticket: Add New';
		$_LANG['_HDESK']['HD_ADD_NEW_TXT01']					= 'The following form is for submitting a helpdesk support ticket.';
		$_LANG['_HDESK']['HD_ADD_NEW_TXT02']					= 'You will receive an email when support has responded.';
		$_LANG['_HDESK']['HD_ADD_NEW_TXT02A']					= 'A ticket will be created on behalf of the client, which we must then respond to as we normally do.';

		$_LANG['_HDESK']['HD_ADD_MSG_TXT_HDR']					= 'Support Ticket: Add Message';
		$_LANG['_HDESK']['HD_ADD_MSG_TXT01']					= 'The following form is for submitting additional messages / comments on your existing support ticket.';
		$_LANG['_HDESK']['HD_ADD_MSG_TXT02']					= 'You will receive an email when support has responded.';

		$_LANG['_HDESK']['HD_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_HDESK']['HD_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_HDESK']['HD_ERR_ERR01']						= 'Priority';
		$_LANG['_HDESK']['HD_ERR_ERR02']						= 'Category';
		$_LANG['_HDESK']['HD_ERR_ERR03']						= 'Subject';
		$_LANG['_HDESK']['HD_ERR_ERR04']						= 'Message';
		$_LANG['_HDESK']['HD_ERR_ERR05']						= 'TT Email Address';

# HelpDesk Import CronJob
	$_LANG['_HDESK']['HD_IMPORT_CONFIG']						= 'If the /coin_cron/helpdesk.php file is <i>not</i> called via wget or curl or a browser simulator, you <b>must</b> configure the URL to your website in /coin_cron/cron_config.php';
	$_LANG['_HDESK']['HD_IMPORT_CONNECTING']					= 'Connecting to mail-server';
	$_LANG['_HDESK']['HD_IMPORT_NO_CONNECT']					= 'Unable to connect to mail-server';
	$_LANG['_HDESK']['HD_IMPORT_TO_PROCESS']					= 'messages to process';
	$_LANG['_HDESK']['HD_IMPORT_PROCESSING']					= 'Processing message';
	$_LANG['_HDESK']['HD_IMPORT_TO_DELETE']						= 'Marking for deletion';
	$_LANG['_HDESK']['HD_IMPORT_FROM']							= 'From';
	$_LANG['_HDESK']['HD_IMPORT_APPENDING']						= 'Appending to ticket';
	$_LANG['_HDESK']['HD_IMPORT_STATUSING']						= 'Changing ticket status and marking "open"';
	$_LANG['_HDESK']['HD_IMPORT_CREATING']						= 'Creating ticket';
	$_LANG['_HDESK']['HD_IMPORT_EMAILING']						= 'Emailing to admin';
	$_LANG['_HDESK']['HD_IMPORT_DELETING']						= 'Deleting Messages';
	$_LANG['_HDESK']['HD_IMPORT_DISCONNETING']					= 'Closing connection to mail-server';
	$_LANG['_HDESK']['HD_IMPORT_NUM_PROCESSED']					= 'Message(s) processed';
	$_LANG['_HDESK']['HD_IMPORT_NUM_TICKETS']					= 'Tickets(s) created or appended';
	$_LANG['_HDESK']['HD_IMPORT_DISABLED']						= 'Auto-Import of HelpDesk messages is disabled';
?>