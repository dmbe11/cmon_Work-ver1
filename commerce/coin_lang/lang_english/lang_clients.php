<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Clients
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_clients.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_CLIENTS']['Required']						= ' (Required)';
		$_LANG['_CLIENTS']['ERR_ERR38']						= 'Required Field';
		$_LANG['_CLIENTS']['PWORD_LEN']						= 'Password must be %MIN% to %MAX% characters long';	// %FLAG% will be replaced at run-time with $_CCFG['CLIENT_MIN_LEN_PWORD'] and $_CCFG['CLIENT_MAX_LEN_PWORD'] respectively

		$_LANG['_CLIENTS']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_CLIENTS']['auto-assigned']					= 'auto-assigned';
		$_LANG['_CLIENTS']['Clients']							= 'Clients';
		$_LANG['_CLIENTS']['Client_Domains']					= 'Client Domains';
		$_LANG['_CLIENTS']['Client_Info_Entry']					= 'Client Info. Entry';
		$_LANG['_CLIENTS']['Client_Information']				= 'Client Information';
		$_LANG['_CLIENTS']['Client_Invoices']					= 'Client Invoices';
		$_LANG['_CLIENTS']['Client_Orders']					= 'Client Orders';
		$_LANG['_CLIENTS']['Client_Select']					= 'Client Select';
		$_LANG['_CLIENTS']['Client_Support_Tickets']				= 'Client Support Tickets';
		$_LANG['_CLIENTS']['denotes_optional_items']				= 'denotes optional items';
		$_LANG['_CLIENTS']['Error_Client_Not_Found']				= 'Error- Client ID not found !';
		$_LANG['_CLIENTS']['of']								= 'of';
		$_LANG['_CLIENTS']['Password_Note']					= 'Input password and confirm only if you wish to change current.';
		$_LANG['_CLIENTS']['total_entries']					= 'total entries';
		$_LANG['_CLIENTS']['Welcome']							= 'Welcome';
		$_LANG['_CLIENTS']['UNAME_CHARS']						= ' - (letters and numbers only)';

# Language Variables: Some Buttons
		$_LANG['_CLIENTS']['B_Add']							= 'Add';
		$_LANG['_CLIENTS']['B_Continue']						= 'Continue';
		$_LANG['_CLIENTS']['B_Delete_Entry']					= 'Delete Entry';
		$_LANG['_CLIENTS']['B_Edit']							= 'Edit';
		$_LANG['_CLIENTS']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_CLIENTS']['B_Reset']							= 'Reset';
		$_LANG['_CLIENTS']['B_Save']							= 'Save';
		$_LANG['_CLIENTS']['B_Send_Email']						= 'Send Email';

# Language Variables: Common Labels (note : on end)
		$_LANG['_CLIENTS']['l_Action']						= 'Action';
		$_LANG['_CLIENTS']['l_Address']						= 'Address:';
		$_LANG['_CLIENTS']['l_Address_Street_1']				= 'Address Street 1:';
		$_LANG['_CLIENTS']['l_Address_Street_2']				= 'Address Street 2:';
		$_LANG['_CLIENTS']['l_Address_Street_2_NReq']			= 'Address Street 2 (*):';
		$_LANG['_CLIENTS']['l_Amount']						= 'Amount:';
		$_LANG['_CLIENTS']['l_Billed_Amount']					= 'Billed Amount:';
		$_LANG['_CLIENTS']['l_Balance']						= 'Balance:';
		$_LANG['_CLIENTS']['l_City']							= 'City:';
		$_LANG['_CLIENTS']['l_Client']						= 'Client:';
		$_LANG['_CLIENTS']['l_Client_ID']						= 'Client ID:';
		$_LANG['_CLIENTS']['l_Client_Information']				= 'Client Information:';
		$_LANG['_CLIENTS']['l_Closed']						= 'Closed:';
		$_LANG['_CLIENTS']['l_Company']						= 'Company:';
		$_LANG['_CLIENTS']['l_Company_NReq']					= 'Company (*):';
		$_LANG['_CLIENTS']['l_Country']						= 'Country:';
		$_LANG['_CLIENTS']['l_Date']							= 'Date:';
		$_LANG['_CLIENTS']['l_Date_Due']						= 'Date Due:';
		$_LANG['_CLIENTS']['l_Domain']						= 'Domain:';
		$_LANG['_CLIENTS']['l_Domain_Expires']					= 'Domain Expires:';
		$_LANG['_CLIENTS']['l_Domains']						= 'Domains:';
		$_LANG['_CLIENTS']['l_Email']							= 'Email:';
		$_LANG['_CLIENTS']['l_Email_Address']					= 'Email Address:';
		$_LANG['_CLIENTS']['l_Email_Address_Additional']			= 'Additional Emails:';
		$_LANG['_CLIENTS']['l_Email_Address_Additional_later']		= 'After the client has been added, you may return to this page and enter additional email contacts for this client:';
		$_LANG['_CLIENTS']['l_Expired']						= 'Expired';
		$_LANG['_CLIENTS']['l_First_Name']						= 'First Name:';
		$_LANG['_CLIENTS']['l_Full_Name']						= 'Full Name:';
		$_LANG['_CLIENTS']['l_Groups']						= 'Groups:';
		$_LANG['_CLIENTS']['l_Id']							= 'Id:';
		$_LANG['_CLIENTS']['l_Invoice_Id']						= 'Invoice Id:';
		$_LANG['_CLIENTS']['l_Join_Date']						= 'Join Date:';
		$_LANG['_CLIENTS']['l_Join_DateTime']					= 'Join DateTime:';
		$_LANG['_CLIENTS']['l_Last_Name']						= 'Last Name:';
		$_LANG['_CLIENTS']['l_None']							= 'None';
		$_LANG['_CLIENTS']['l_Notes']							= 'Notes:';
		$_LANG['_CLIENTS']['l_Order_Id']						= 'Order Id:';
		$_LANG['_CLIENTS']['l_Pages']							= 'Page(s):';
		$_LANG['_CLIENTS']['l_Password']						= 'Password:';
		$_LANG['_CLIENTS']['l_Password_Confirm']				= 'Password Confirm:';
		$_LANG['_CLIENTS']['l_Password_Confirm_NReq']			= 'Password Confirm (*):';
		$_LANG['_CLIENTS']['l_Password_NReq']					= 'Password (*):';
		$_LANG['_CLIENTS']['l_Phone']							= 'Phone:';
		$_LANG['_CLIENTS']['l_Phone_NReq']						= 'Phone (*):';
		$_LANG['_CLIENTS']['l_Priority']						= 'Priority:';
		$_LANG['_CLIENTS']['l_Product']						= 'Product:';
		$_LANG['_CLIENTS']['l_Product_Description']				= 'Product Description:';
		$_LANG['_CLIENTS']['l_Registrar']						= 'Registrar:';
		$_LANG['_CLIENTS']['l_SACC']							= 'Hosting:';
		$_LANG['_CLIENTS']['l_SACC_Expires']					= 'Hosting Expires:';
		$_LANG['_CLIENTS']['l_Server']						= 'Server:';
		$_LANG['_CLIENTS']['l_Status']						= 'Status:';
		$_LANG['_CLIENTS']['l_Subject']						= 'Subject:';
		$_LANG['_CLIENTS']['l_State_Province']					= 'State / Province:';
		$_LANG['_CLIENTS']['l_User_Name']						= 'User Name:';
		$_LANG['_CLIENTS']['l_Vendor']						= 'Vendor:';
		$_LANG['_CLIENTS']['l_Zip_Postal_Code']					= 'Zip / Postal Code:';

# Language Variables: index.php
		$_LANG['_CLIENTS']['Add_Client_Info_Results']			= 'Add Client Info. Results';
		$_LANG['_CLIENTS']['Admin_Client_View']					= 'Admin Client View';
		$_LANG['_CLIENTS']['Inserted_ID']						= 'Inserted ID';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Confirmation']		= 'Delete Client Entry Confirmation';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Message_Cont']		= 'and all the associated orders, invoices, domains, etc.?';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results']			= 'Delete Client Entry Results';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_01']		= 'The following client items deleted';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_02']		= 'Deleted client records';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_03']		= 'Deleted client orders';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_04']		= 'Deleted client invoice items';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_05']		= 'Deleted client invoice transactions';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_06']		= 'Deleted client invoices';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_07']		= 'Deleted client domains';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_08']		= 'Deleted client helpdesk ticket messages';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_09']		= 'Deleted client helpdesk tickets';
		$_LANG['_CLIENTS']['Delete_Client_Entry_Results_10']		= 'Deleted client additional emails';

		$_LANG['_CLIENTS']['Edit_Client_Info_Results']			= 'Edit Client Info. Results';
		$_LANG['_CLIENTS']['Entry_Deleted']					= 'Entry Deleted.';
		$_LANG['_CLIENTS']['View_Clients']						= 'View Clients';

		$_LANG['_CLIENTS']['eMail_Client_Confirmation']			= 'eMail Client Profile Confirmation';
		$_LANG['_CLIENTS']['eMail_Client_Message_prefix']			= 'Are You Sure You Want to Email Client ID';
		$_LANG['_CLIENTS']['eMail_Client_Message_suffix']			= 'Profile to client?';

# Language Variables: Email Client (clients_funcs.php:function do_mail_order())
	# Caution- padded spaces are needed for email items to line up
		$_LANG['_CLIENTS']['CL_EMAIL_01']						= 'Client ID:         ';
		$_LANG['_CLIENTS']['CL_EMAIL_02']						= 'Join Date:         ';
		$_LANG['_CLIENTS']['CL_EMAIL_03']						= 'User Name:         ';
		$_LANG['_CLIENTS']['CL_EMAIL_04']						= 'Email:             ';
		$_LANG['_CLIENTS']['CL_EMAIL_05']						= 'Company:           ';
		$_LANG['_CLIENTS']['CL_EMAIL_06']						= 'Full Name:         ';
		$_LANG['_CLIENTS']['CL_EMAIL_07']						= 'Address Line 1:    ';
		$_LANG['_CLIENTS']['CL_EMAIL_08']						= 'Address Line 2:    ';
		$_LANG['_CLIENTS']['CL_EMAIL_09']						= 'City:              ';
		$_LANG['_CLIENTS']['CL_EMAIL_10']						= 'State / Province:  ';
		$_LANG['_CLIENTS']['CL_EMAIL_11']						= 'Country:           ';
		$_LANG['_CLIENTS']['CL_EMAIL_12']						= 'Zip / Postal Code: ';
		$_LANG['_CLIENTS']['CL_EMAIL_13']						= 'Phone:             ';

		$_LANG['_CLIENTS']['CL_EMAIL_SUBJECT']					= '- Profile';

		$_LANG['_CLIENTS']['CL_EMAIL_MSG_01_PRE']				= 'Client ID:';
		$_LANG['_CLIENTS']['CL_EMAIL_MSG_01_SUF']				= 'not located.';
		$_LANG['_CLIENTS']['CL_EMAIL_MSG_02_L1']				= 'An error has occurred, Please try again.';
		$_LANG['_CLIENTS']['CL_EMAIL_MSG_02_L2']				= 'If problem continues, contact support via contact form.';
		$_LANG['_CLIENTS']['CL_EMAIL_MSG_03_PRE']				= 'Client ID:';
		$_LANG['_CLIENTS']['CL_EMAIL_MSG_03_SUF']				= 'profile email has been sent.';
		$_LANG['_CLIENTS']['CL_EMAIL_RESULT_TITLE']				= 'eMail Results: Client Profile';

# Page: Admin Data Entry error
		$_LANG['_CLIENTS']['CL_ERR_ERR_HDR1']					= 'Data Entry Error- See flags';
		$_LANG['_CLIENTS']['CL_ERR_ERR_HDR2']					= '- Some required fields may be blank.';

		$_LANG['_CLIENTS']['CL_ERR_ERR01']						= 'Client ID';
		$_LANG['_CLIENTS']['CL_ERR_ERR02']						= 'Join Date';
		$_LANG['_CLIENTS']['CL_ERR_ERR03']						= 'Status';
		$_LANG['_CLIENTS']['CL_ERR_ERR04']						= 'Company';
		$_LANG['_CLIENTS']['CL_ERR_ERR05']						= 'First Name';
		$_LANG['_CLIENTS']['CL_ERR_ERR06']						= 'Last Name';
		$_LANG['_CLIENTS']['CL_ERR_ERR07']						= 'Address Street 1';
		$_LANG['_CLIENTS']['CL_ERR_ERR08']						= 'Address 2';
		$_LANG['_CLIENTS']['CL_ERR_ERR09']						= 'City';
		$_LANG['_CLIENTS']['CL_ERR_ERR10']						= 'State / Province';
		$_LANG['_CLIENTS']['CL_ERR_ERR11']						= 'Country';
		$_LANG['_CLIENTS']['CL_ERR_ERR12']						= 'Zip / Postal Code';
		$_LANG['_CLIENTS']['CL_ERR_ERR13']						= 'Phone';
		$_LANG['_CLIENTS']['CL_ERR_ERR14']						= 'Email Address';
		$_LANG['_CLIENTS']['CL_ERR_ERR15']						= 'User Name';
		$_LANG['_CLIENTS']['CL_ERR_ERR16']						= 'Password is missing';
		$_LANG['_CLIENTS']['CL_ERR_ERR17']						= 'Confirm password is missing';
		$_LANG['_CLIENTS']['CL_ERR_ERR18']						= 'password is too short. Min %NUM% characters';	// %NUM% will be replaced at run-time with $_CCFG['CLIENT_MIN_LEN_PWORD']
		$_LANG['_CLIENTS']['CL_ERR_ERR19']						= 'password is too long. Max %NUM% characters';	// %NUM% will be replaced at run-time with $_CCFG['CLIENT_MAX_LEN_PWORD']
		$_LANG['_CLIENTS']['CL_ERR_ERR20']						= 'password and confirm password does not match';
		$_LANG['_CLIENTS']['CL_ERR_ERR21']						= 'xxx';
		$_LANG['_CLIENTS']['CL_ERR_ERR22']						= 'xxx';
		$_LANG['_CLIENTS']['CL_ERR_ERR23']						= 'xxx';
		$_LANG['_CLIENTS']['CL_ERR_ERR24']						= 'xxx';
		$_LANG['_CLIENTS']['CL_ERR_ERR25']						= '- Additional Email appears to be invalid.';

		$_LANG['_CLIENTS']['CL_ERR_ERR30']						= '- Email appears to be invalid.';
		$_LANG['_CLIENTS']['CL_ERR_ERR31']						= '- User Name already exists, must enter another.';
		$_LANG['_CLIENTS']['CL_ERR_ERR32']						= '- Password and Password Confirm must match.';

		$_LANG['_CLIENTS']['CL_ERR_ERR33']						= '- Email address in use ~ you must enter another.';
		$_LANG['_CLIENTS']['CL_ERR_ERR34']						= '- Additional email address in use ~ you must enter another.';
		$_LANG['_CLIENTS']['CL_ERR_ERR39']						= 'The UserName must contain letters and numbers only';

		$_LANG['_CLIENTS']['Accounts_Receivable_Listing']			= 'Accounts Receivable Listing';
		$_LANG['_CLIENTS']['Page_Balance']						= 'Page Balance';
		$_LANG['_CLIENTS']['Overall_Balance']					= 'Total Balance';

?>