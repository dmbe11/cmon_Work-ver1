<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Domains
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_domains.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_DOMS']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_DOMS']['Add_Domains_Entry_Results']				= 'Add Domains Entry Results (Inserted ID';
		$_LANG['_DOMS']['all_fields_required']					= 'all fields required';
		$_LANG['_DOMS']['some_fields_required']					= 'some fields required';
		$_LANG['_DOMS']['auto-assigned']						= 'auto-assigned';
		$_LANG['_DOMS']['Client_Domains']						= 'Client Domains';
		$_LANG['_DOMS']['could_not_be_located']					= 'could not be located.';
		$_LANG['_DOMS']['Domains_ID']							= 'Domain ID';
		$_LANG['_DOMS']['Domains_Editor']						= 'Domains Editor';
		$_LANG['_DOMS']['Domains_Entry']						= 'Domains Entry';
		$_LANG['_DOMS']['Domains_Select']						= 'Domains Select';
		$_LANG['_DOMS']['Edit_Domains_Entry_Results']			= 'Edit Domains Entry Results';
		$_LANG['_DOMS']['Entry_Deleted']						= 'Entry Deleted.';
		$_LANG['_DOMS']['Error_Domain_Not_Found']				= 'Error- Domain ID not found !';
		$_LANG['_DOMS']['Expired']                              	= 'Expired';
		$_LANG['_DOMS']['Notes']								= 'Notes';
		$_LANG['_DOMS']['Expired']							= 'Expired';
		$_LANG['_DOMS']['of']								= 'of';
		$_LANG['_DOMS']['Please_Select']						= 'Please Select';
		$_LANG['_DOMS']['total_entries']						= 'total entries';
		$_LANG['_DOMS']['Unlimited']							= 'Unlimited';
		$_LANG['_DOMS']['Unlimited_Instructions']				= 'Enter "-1" for "unlimited"';
		$_LANG['_DOMS']['View_Domain_Error']					= 'View Domain Error';

# Language Variables: index.php (goes below l_)
		$_LANG['_DOMS']['Delete_Domains_Entry_Confirmation']		= 'Delete Domains Entry Confirmation';
		$_LANG['_DOMS']['Delete_Domains_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_DOMS']['Delete_Domains_Entry_Results']			= 'Delete Domains Entry Results';
		$_LANG['_DOMS']['View_Client_Domains']					= 'View Client Domains';
		$_LANG['_DOMS']['View_Client_Domains_For']				= 'View Client Domains For';
		$_LANG['_DOMS']['View_Client_Domain_ID']				= 'View Client Domain ID';
		$_LANG['_DOMS']['View_Domains']						= 'View Domains';

		$_LANG['_DOMS']['eMail_Domain_Confirmation']				= 'eMail Domain Activation Information';
		$_LANG['_DOMS']['eMail_Domain_Message_prefix']			= 'Are You Sure You Want to Email Domain ID';
		$_LANG['_DOMS']['eMail_Domain_Message_suffix']			= 'activation email to client?';

# Language Variables: Some Buttons
		$_LANG['_DOMS']['B_Add']								= 'Add';
		$_LANG['_DOMS']['B_Continue']							= 'Continue';
		$_LANG['_DOMS']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_DOMS']['B_Edit']							= 'Edit';
		$_LANG['_DOMS']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_DOMS']['B_Reset']							= 'Reset';
		$_LANG['_DOMS']['B_Save']							= 'Save';
		$_LANG['_DOMS']['B_Send_Email']						= 'Send Email';

# Language Variables: Common Labels (note : on end)
		$_LANG['_DOMS']['l_ASP_Support']						= 'ASP Support:';
		$_LANG['_DOMS']['l_CGI_Support']						= 'CGI Support:';
		$_LANG['_DOMS']['l_Client_ID']						= 'Client ID:';
		$_LANG['_DOMS']['l_Control_Panel_URL']					= 'Control Panel URL:';
		$_LANG['_DOMS']['l_Control_Panel_User_Name']				= 'Control Panel User Name:';
		$_LANG['_DOMS']['l_Control_Panel_User_Password']			= 'Control Panel User Password:';
		$_LANG['_DOMS']['l_Databases']						= 'Databases:';
		$_LANG['_DOMS']['l_Disk_Space_Mb']						= 'Disk Space (Mb):';
		$_LANG['_DOMS']['l_Domains']							= 'Domains:';
		$_LANG['_DOMS']['l_Domain']							= 'Domain:';
		$_LANG['_DOMS']['l_Domain_ID']						= 'Domain ID:';
		$_LANG['_DOMS']['l_Domain_Expires']					= 'Domain Expires:';
		$_LANG['_DOMS']['l_Domain_Expiration']					= 'Domain Expiration:';
		$_LANG['_DOMS']['l_Enable_CGI_Support']					= 'Enable CGI Support:';
		$_LANG['_DOMS']['l_Error_Docs_Logs']					= 'Error Docs / Logs:';
		$_LANG['_DOMS']['l_Frontpage_SSL_Support']				= 'Frontpage SSL Support:';
		$_LANG['_DOMS']['l_FrontPage_Support']					= 'FrontPage Support:';
		$_LANG['_DOMS']['l_FTP_User_Name']						= 'FTP User Name:';
		$_LANG['_DOMS']['l_FTP_User_Password']					= 'FTP User Password:';
		$_LANG['_DOMS']['l_ID']								= 'ID:';
		$_LANG['_DOMS']['l_MailBoxes_POP']						= 'MailBoxes (POP):';
		$_LANG['_DOMS']['l_mod_perl_Support']					= 'mod_perl Support:';
		$_LANG['_DOMS']['l_Pages']							= 'Page(s):';
		$_LANG['_DOMS']['l_PHP_Support']						= 'PHP Support:';
		$_LANG['_DOMS']['l_Registrar']						= 'Registrar:';
		$_LANG['_DOMS']['l_SACC']							= 'Hosting:';
		$_LANG['_DOMS']['l_SACC_Expires']						= 'Hosting Expires:';
		$_LANG['_DOMS']['l_SACC_Expiration']					= 'Hosting Expiration:';
		$_LANG['_DOMS']['l_Server']							= 'Server:';
		$_LANG['_DOMS']['l_Server_ID']						= 'Server ID:';
		$_LANG['_DOMS']['l_Server_Name']						= 'Server Name:';
		$_LANG['_DOMS']['l_Server_Account_IP']					= 'Server Account IP:';
		$_LANG['_DOMS']['l_Server_Account_Path']				= 'Server Account Path:';
		$_LANG['_DOMS']['l_Server_Path_Temp']					= 'Server Path Temp:';
		$_LANG['_DOMS']['l_SSI_Support']						= 'SSI Support:';
		$_LANG['_DOMS']['l_SSL_Support']						= 'SSL Support:';
		$_LANG['_DOMS']['l_Status']							= 'Status:';
		$_LANG['_DOMS']['l_SubDomains']						= 'SubDomains:';
		$_LANG['_DOMS']['l_Traffic_BW_Mb']						= 'Traffic / BW (Mb):';
		$_LANG['_DOMS']['l_Type']							= 'Type:';
		$_LANG['_DOMS']['l_Web_User_Scripting']					= 'Web User Scripting:';
		$_LANG['_DOMS']['l_WebMail']							= 'WebMail:';
		$_LANG['_DOMS']['l_Webstats']							= 'Webstats:';
		$_LANG['_DOMS']['l_WWW_Prefix']						= 'WWW Prefix:';

# Language Variables:
	# Misc Errors:
		$_LANG['_DOMS']['DOM_ERR_NONE_FOUND']					= 'Sorry, no records were found for that selection.';

# Language Variables: Email Server Account Activation (saccs_funcs.php:function do_mail_sacc())
	# Caution- padded spaces are needed for email items to line up

		$_LANG['_DOMS']['DOM_EMAIL_01']						= 'Client ID:         ';
		$_LANG['_DOMS']['DOM_EMAIL_02']						= 'Join Date:         ';
		$_LANG['_DOMS']['DOM_EMAIL_03']						= 'User Name:         ';
		$_LANG['_DOMS']['DOM_EMAIL_04']						= 'Email:             ';
		$_LANG['_DOMS']['DOM_EMAIL_05']						= 'Company:           ';
		$_LANG['_DOMS']['DOM_EMAIL_06']						= 'Full Name:         ';
		$_LANG['_DOMS']['DOM_EMAIL_07']						= 'Address Line 1:    ';
		$_LANG['_DOMS']['DOM_EMAIL_08']						= 'Address Line 2:    ';
		$_LANG['_DOMS']['DOM_EMAIL_09']						= 'City:              ';
		$_LANG['_DOMS']['DOM_EMAIL_10']						= 'State / Province:  ';
		$_LANG['_DOMS']['DOM_EMAIL_11']						= 'Country:           ';
		$_LANG['_DOMS']['DOM_EMAIL_12']						= 'Zip / Postal Code: ';
		$_LANG['_DOMS']['DOM_EMAIL_13']						= 'Phone:             ';
		$_LANG['_DOMS']['DOM_EMAIL_14']						= '';
		$_LANG['_DOMS']['DOM_EMAIL_15']						= '';

		$_LANG['_DOMS']['DOM_EMAIL_SUBJECT']					= '- Domain Account Activation';

		$_LANG['_DOMS']['DOM_EMAIL_MSG_01_PRE']					= 'Domain ID:';
		$_LANG['_DOMS']['DOM_EMAIL_MSG_01_SUF']					= 'not located.';
		$_LANG['_DOMS']['DOM_EMAIL_MSG_02_L1']					= 'An error has occurred, Please try again.';
		$_LANG['_DOMS']['DOM_EMAIL_MSG_02_L2']					= 'If problem continues, contact support via contact form.';
		$_LANG['_DOMS']['DOM_EMAIL_MSG_03_PRE']					= 'The Domain ID:';
		$_LANG['_DOMS']['DOM_EMAIL_MSG_03_SUF']					= 'Activation email has been sent.';
		$_LANG['_DOMS']['DOM_EMAIL_RESULT_TITLE']				= 'eMail Results: Domain Account Activation';

	# Page: Admin Data Entry error
		$_LANG['_DOMS']['DOM_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_DOMS']['DOM_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_DOMS']['DOM_ERR_ERR01']						= 'Domain ID';
		$_LANG['_DOMS']['DOM_ERR_ERR02']						= 'Client ID';
		$_LANG['_DOMS']['DOM_ERR_ERR03']						= 'Domain';
		$_LANG['_DOMS']['DOM_ERR_ERR04']						= 'CP User Name';
		$_LANG['_DOMS']['DOM_ERR_ERR05']						= 'CP User Password';
		$_LANG['_DOMS']['DOM_ERR_ERR06']						= 'FTP User Name';
		$_LANG['_DOMS']['DOM_ERR_ERR07']						= 'FTP User Password';
		$_LANG['_DOMS']['DOM_ERR_ERR08']						= 'xxx';

?>