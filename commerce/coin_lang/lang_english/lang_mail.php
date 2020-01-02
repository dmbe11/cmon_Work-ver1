<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Mail
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_mail.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_MAIL']['AND']								= 'AND';
		$_LANG['_MAIL']['OR']								= 'OR';
		$_LANG['_MAIL']['Administrator']						= 'Administrator';
		$_LANG['_MAIL']['all_fields_required']					= 'all fields required';
		$_LANG['_MAIL']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_MAIL']['Client']							= 'Client';
		$_LANG['_MAIL']['Clients_On']							= 'Clients on';
		$_LANG['_MAIL']['Contact_Form']						= 'Contact Form';
		$_LANG['_MAIL']['Contact_Client_Form']					= 'Contact Client Form';
		$_LANG['_MAIL']['Delete_Archive_Entry_Confirmation']		= 'Delete Mail Archive Entry Confirmation';
		$_LANG['_MAIL']['Delete_Archive_Entry_Message_01']		= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_MAIL']['Delete_Archive_Entry_Message_02']		= 'Number of mail archive entries deleted';
		$_LANG['_MAIL']['Delete_Archive_Entry_Results']			= 'Delete Mail Archive Entry Results';
		$_LANG['_MAIL']['Found_Items']						= 'Found Items';
		$_LANG['_MAIL']['No_Items_Found']						= 'No items found for criteria entered.';
		$_LANG['_MAIL']['output_below']						= 'output below';
		$_LANG['_MAIL']['Password_Reset_Request']				= 'Password Reset Request:';
		$_LANG['_MAIL']['Password_Reset_Message_01']				= 'Please enter your valid user name and email address, a new password will be sent to your email address on file.';
		$_LANG['_MAIL']['Purge_Archive_Message_01']				= 'Are You Sure You Want to Purge the Found Result Set';
		$_LANG['_MAIL']['Purge_Archive_Message_02']				= 'Number of mail archive entries purged';
		$_LANG['_MAIL']['Purge_Archive_Results']				= 'Delete Mail Archive Entry Results';
		$_LANG['_MAIL']['total']								= 'total';
		$_LANG['_MAIL']['Resend_Archive_Entry_Confirmation']		= 'Resend Mail Archive Entry Confirmation';
		$_LANG['_MAIL']['Resend_Archive_Entry_Message_01']		= 'Are You Sure You Want to Resend Entry ID';
		$_LANG['_MAIL']['Resend_Archive_Entry_Message_02']		= 'Message has been resent to recipient(s).';
		$_LANG['_MAIL']['Resend_Archive_Entry_Message_03_L1']		= 'An error has occurred, Please try again.';
		$_LANG['_MAIL']['Resend_Archive_Entry_Message_03_L2']		= 'If problem continues, contact support via contact form.';
		$_LANG['_MAIL']['Resend_Archive_Entry_Results']			= 'Resend Mail Archive Entry Results';
		$_LANG['_MAIL']['Search_Mail']						= 'Search Mail';
		$_LANG['_MAIL']['Search_Mail_Archive']					= 'Search Mail Archive';
		$_LANG['_MAIL']['sent']								= 'sent';
		$_LANG['_MAIL']['Sent_And_After']						= 'And After';
		$_LANG['_MAIL']['Sent_And_Before']						= 'And Before';
		$_LANG['_MAIL']['Sorry_Administrative_Function_Only']		= 'Sorry- Administrative Function Only';
		$_LANG['_MAIL']['Welcome']							= 'Welcome';
		$_LANG['_MAIL']['SC_Instruct']						= 'Type the letters and numbers exactly as they appear on the image into the "Security Code" box';

$_LANG['_MAIL']['Contact_Supplier_Form']					= 'Contact Supplier Form';
$_LANG['_MAIL']['CS_FORM_ERR_ERR01']						= 'Supplier';
$_LANG['_MAIL']['l_To_Supplier']							= 'To Supplier:';
$_LANG['_MAIL']['All_Active_Suppliers']						= 'All Active Suppliers';

# Language Variables: Some Buttons
		$_LANG['_MAIL']['B_Delete_Entry']						= 'Delete';
		$_LANG['_MAIL']['B_Purge_Result_Set']					= 'Purge Result Set';
		$_LANG['_MAIL']['B_Resend_Entry']						= 'Resend Entry';
		$_LANG['_MAIL']['B_Reset']							= 'Reset';
		$_LANG['_MAIL']['B_Search']							= 'Search';
		$_LANG['_MAIL']['B_Send_Email']						= 'Send Email';
		$_LANG['_MAIL']['B_Submit_Request']					= 'Submit Request';

# Language Variables: Common Labels (note : on end)
		$_LANG['_MAIL']['l_View_Message']						= 'View Email Message:';
		$_LANG['_MAIL']['l_CC']								= 'CC:';
		$_LANG['_MAIL']['l_BCC']								= 'BCC:';
		$_LANG['_MAIL']['l_Date_Sent']						= 'Date Sent:';
		$_LANG['_MAIL']['l_Email']							= 'Email:';
		$_LANG['_MAIL']['l_From']							= 'From:';
		$_LANG['_MAIL']['l_Message']							= 'Message:';
		$_LANG['_MAIL']['l_Name']							= 'Name:';
		$_LANG['_MAIL']['l_Security_Code']						= 'Security Code:';
		$_LANG['_MAIL']['l_Subject']							= 'Subject:';
		$_LANG['_MAIL']['l_To']								= 'To:';
		$_LANG['_MAIL']['l_To_Client']						= 'To Client:';
		$_LANG['_MAIL']['l_User_Name']						= 'User Name:';
		$_LANG['_MAIL']['l_Search_Type']						= 'Search Type:';

# Language Variables: index.php
		$_LANG['_MAIL']['Mail']								= 'Mail';
		$_LANG['_MAIL']['Mail_Program_Index']					= 'Mail Program Index';

# Language Variables: Email Password Reset (mail_funcs.php:function do_mail_pword_reset())
		$_LANG['_MAIL']['PWORD_RESET_SUBJECT_PRE']				= '- Support Request';
		$_LANG['_MAIL']['PWORD_RESET_SUBJECT_SUF']				= '';
		$_LANG['_MAIL']['PWORD_RESET_RESULT_TITLE']				= 'Password Reset Request Results';
		$_LANG['_MAIL']['PWORD_RESET_MSG_01A']					= 'The User Name entered was not found in our database.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_01B']					= 'The Email address entered does not match the one on record.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_02_L1']				= 'An error has occurred updating the database, Please try again.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_02_L2']				= 'If problem continues, contact support via contact form.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_03_L1']				= 'An error has occurred sending the email, Please try again.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_03_L2']				= 'If problem continues, contact support via contact form.';
		$_LANG['_MAIL']['PWORD_RESET_MSG_04_L1']				= 'Our records have been updated, and an Email with instructions has been sent';
		$_LANG['_MAIL']['PWORD_RESET_MSG_04_L2']				= 'to the email address you have on file.';

# Language Variables: Contact Form Email (mail_funcs.php:function do_contact_email())
		$_LANG['_MAIL']['CS_FORM_SUBJECT_PRE']					= '- Contact Message';
		$_LANG['_MAIL']['CS_FORM_SUBJECT_SUF']					= '';
		$_LANG['_MAIL']['CS_FORM_RESULT_TITLE']					= 'Contact Form Submit Results';
		$_LANG['_MAIL']['CS_FORM_MSG_01']						= '';
		$_LANG['_MAIL']['CS_FORM_MSG_02_L1']					= 'An error has occurred, Please try again.';
		$_LANG['_MAIL']['CS_FORM_MSG_02_L2']					= 'If problem continues, contact support via alternative methods.';
		$_LANG['_MAIL']['CS_FORM_MSG_03_L1']					= 'The contact message has been sent. In addition, a copy has been sent';
		$_LANG['_MAIL']['CS_FORM_MSG_03_L2']					= 'to the address you entered.';

# Language Variables: Contact Client Form Email (mail_funcs.php:function do_contact_email())
		$_LANG['_MAIL']['CC_FORM_SUBJECT_PRE']					= '- Contact Client Message';
		$_LANG['_MAIL']['CC_FORM_SUBJECT_SUF']					= '';
		$_LANG['_MAIL']['CC_FORM_RESULT_TITLE']					= 'Contact Client Form Submit Results';
		$_LANG['_MAIL']['CC_FORM_MSG_01']						= '';
		$_LANG['_MAIL']['CC_FORM_MSG_02_L1']					= 'An error has occurred, Please try again.';
		$_LANG['_MAIL']['CC_FORM_MSG_02_L2']					= 'If problem continues, contact support via contact form.';
		$_LANG['_MAIL']['CC_FORM_MSG_03_L1']					= 'The contact client message has been sent.';
		$_LANG['_MAIL']['CC_FORM_MSG_04_L1']					= 'The contact client messages have been sent';

# Page: Data Entry and errors- Contact (Site) Form
		$_LANG['_MAIL']['CS_FORM_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_MAIL']['CS_FORM_ERR_HDR2']					= 'Please check the following:';
		$_LANG['_MAIL']['CS_FORM_ERR_MSG_01']					= '- Email appears to be invalid.';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR01']					= 'Contact';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR02']					= 'Name';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR03']					= 'Email';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR04']					= 'Subject';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR05']					= 'Message';
		$_LANG['_MAIL']['CS_FORM_ERR_ERR06']					= 'Security Code';

# Page: Data Entry and errors- Contact Client Form
		$_LANG['_MAIL']['CC_FORM_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_MAIL']['CC_FORM_ERR_HDR2']					= 'Please check the following:';
		$_LANG['_MAIL']['CC_FORM_ERR_MSG_01']					= '';
		$_LANG['_MAIL']['CC_FORM_ERR_ERR01']					= 'Client';
		$_LANG['_MAIL']['CC_FORM_ERR_ERR02']					= 'From';
		$_LANG['_MAIL']['CC_FORM_ERR_ERR03']					= 'Subject';
		$_LANG['_MAIL']['CC_FORM_ERR_ERR04']					= 'Message';
		$_LANG['_MAIL']['CC_FORM_ERR_ERR05']					= 'xxx';


# Page Contact Form section headers and other info
		$_LANG['_MAIL']['CC_FORM_TITLE_MAIL']					= 'Send Postal Mail to:';
		$_LANG['_MAIL']['CC_FORM_TITLE_TELECOM']				= 'Or, use telecommunications:';
		$_LANG['_MAIL']['CC_FORM_TITLE_EMAIL']					= 'Or, use this form to send us an email:';
		$_LANG['_MAIL']['CC_FORM_TITLE_OTHER']					= 'Or click on the icon(s) below:';

		$_LANG['_MAIL']['CC_FORM_DATA_TELECOM']					= 'Enter info for ICQ, etc. here, or make string blank to auto-hide this section (coin_lang/lang_english/lang_mail.php)';
		$_LANG['_MAIL']['CC_FORM_DATA_OTHER']					= 'Enter html for livehelp stuff here, or make string blank to auto-hide this section (coin_lang/lang_english/lang_mail.php)';
?>