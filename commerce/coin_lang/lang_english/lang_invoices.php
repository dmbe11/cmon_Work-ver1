<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Invoices
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_invoices.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_INVCS']['Form_Title']						= 'INVOICE';
$_LANG['_INVCS']['CM_Title']						= 'CREDIT MEMO';
		$_LANG['_INVCS']['Actions']							= 'Actions...';
		$_LANG['_INVCS']['Apply_Tax_01']						= 'Apply Tax 01';
		$_LANG['_INVCS']['Apply_Tax_02']						= 'Apply Tax 02';
		$_LANG['_INVCS']['AutoCalc_Tax']						= 'AutoCalc Tax Amounts';
		$_LANG['_INVCS']['Auto_Copy_Recurring']					= 'Auto-Copy Recurring';
		$_LANG['_INVCS']['Auto_Email_Due']						= 'Auto-Email Due';
		$_LANG['_INVCS']['Auto_Email_OverDue']					= 'Auto-Email OverDue';
		$_LANG['_INVCS']['Auto_Email_OverDue_Results']			= 'Auto Email OverDue Results';
		$_LANG['_INVCS']['Auto_Invoice_Copy_Results']			= 'Auto Invoice Copy Results';
		$_LANG['_INVCS']['Auto_Update_Status']					= 'Auto-Update Status';
		$_LANG['_INVCS']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_INVCS']['auto-assigned']						= 'auto-assigned';
		$_LANG['_INVCS']['autocalcs_on_save']					= 'autocalcs on save';
		$_LANG['_INVCS']['Bill_To']							= 'Bill To';
		$_LANG['_INVCS']['Calc_Tax_02_On_01']					= 'Calc Tax 02 On Tax 01';
		$_LANG['_INVCS']['charge']							= 'charge';
		$_LANG['_INVCS']['Client_Information']					= 'Client Information';
		$_LANG['_INVCS']['Client_Invoice_Status_Auto_Update']		= 'Client Invoice Status Auto-Update';
		$_LANG['_INVCS']['Clients_Invoices']					= 'Clients Invoices';
		$_LANG['_INVCS']['Clients_Invoice_Transactions']			= 'Clients Invoice Transactions';
		$_LANG['_INVCS']['credit']							= 'credit';
		$_LANG['_INVCS']['debit']							= 'debit';
		$_LANG['_INVCS']['denotes_optional_items']				= 'denotes optional items';
		$_LANG['_INVCS']['Do_Not_Nag']						= 'Do NOT Nag';
		$_LANG['_INVCS']['due']								= 'due';
		$_LANG['_INVCS']['Error_Invoice_Not_Found']				= 'Error- Invoice ID not found !';
		$_LANG['_INVCS']['Function_Disabled']					= 'Function Disabled';
		$_LANG['_INVCS']['Invoice_Information']					= 'Invoice Information';
		$_LANG['_INVCS']['Invoice_Items']						= 'Invoice Items';
		$_LANG['_INVCS']['Invoice_Items_Entry']					= 'Invoice Items Entry';
		$_LANG['_INVCS']['Invoices_Entry']						= 'Invoices Entry';
		$_LANG['_INVCS']['Items_Editor']						= 'Items Editor';
		$_LANG['_INVCS']['no_commas']							= 'no commas';
		$_LANG['_INVCS']['none']								= 'none';
		$_LANG['_INVCS']['of']								= 'of';
		$_LANG['_INVCS']['Payment_Link']						= 'Payment Link';
		$_LANG['_INVCS']['Payment_Link_Paid']					= 'This invoice was paid, so the paymant link has been removed';
		$_LANG['_INVCS']['percent_of_total']					= 'percent of total';
		$_LANG['_INVCS']['Please_Select']						= 'Please Select';
		$_LANG['_INVCS']['Remit_To']							= 'Remit To';
		$_LANG['_INVCS']['Select_Cycle']						= 'Select Cycle';
		$_LANG['_INVCS']['Send_Trans_Ack_Email']				= 'Send Transaction Acknowledgement Email';
		$_LANG['_INVCS']['Set_Invoice_To_Paid']					= 'Set invoice to paid';
		$_LANG['_INVCS']['Tax_Amount']						= 'Tax Amount';
		$_LANG['_INVCS']['Tax_Rate']							= 'Tax Rate';
		$_LANG['_INVCS']['Terms']							= 'Terms';
		$_LANG['_INVCS']['total_entries']						= 'total entries';
		$_LANG['_INVCS']['View_Client_Invoice_ID']				= 'View Client Invoice ID:';
		$_LANG['_INVCS']['Welcome']							= 'Welcome';
		$_LANG['_INVCS']['Tax_Amount_Manual_Calc']				= 'Enter tax amount if "AutoCalc Tax Amounts" is NOT checked';

# Language Variables: Some Buttons
		$_LANG['_INVCS']['B_Add']							= 'Add';
		$_LANG['_INVCS']['B_Continue']						= 'Continue';
		$_LANG['_INVCS']['B_Copy_Invoice']						= 'Copy Invoice';
		$_LANG['_INVCS']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_INVCS']['B_Edit']							= 'Edit';
		$_LANG['_INVCS']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_INVCS']['B_Reset']							= 'Reset';
		$_LANG['_INVCS']['B_Save']							= 'Save';
		$_LANG['_INVCS']['B_Send_Email']						= 'Send Email';
		$_LANG['_INVCS']['B_Set_Paid']						= 'Set Paid';
		$_LANG['_INVCS']['B_Submit']							= 'Submit';

# Language Variables: Common Labels (note : on end)
		$_LANG['_INVCS']['l_Address']							= 'Address:';
		$_LANG['_INVCS']['l_Amount']							= 'Amount:';
		$_LANG['_INVCS']['l_Billed_Amount']					= 'Billed Amount:';
		$_LANG['_INVCS']['l_Auto_Update_Status']				= 'Auto-Update Status:';
		$_LANG['_INVCS']['l_Auto_Copy_Recurring']				= 'Auto-Copy Recurring:';
		$_LANG['_INVCS']['l_Auto_Email_Due']					= 'Auto-Email Due:';
		$_LANG['_INVCS']['l_Auto_Email_OverDue']				= 'Auto-Email OverDue:';
		$_LANG['_INVCS']['l_Auto_Parse_PayPal_Email']			= 'Apply PayPal Email Payments';
		$_LANG['_INVCS']['l_Balance']							= 'Balance:';
		$_LANG['_INVCS']['l_Billing_Cycle']					= 'Billing Cycle:';
		$_LANG['_INVCS']['l_Charges_To_Account']				= 'Charges To Account:';
		$_LANG['_INVCS']['l_Credits_To_Account']				= 'Credits To Account:';
		$_LANG['_INVCS']['l_City']							= 'City:';
		$_LANG['_INVCS']['l_Client']							= 'Client:';
		$_LANG['_INVCS']['l_Client_ID']						= 'Client ID:';
		$_LANG['_INVCS']['l_Client_Name']						= 'Client Name:';
		$_LANG['_INVCS']['l_Company']							= 'Company:';
		$_LANG['_INVCS']['l_Cost']							= 'Cost:';
		$_LANG['_INVCS']['l_Country']							= 'Country:';
		$_LANG['_INVCS']['l_Date']							= 'Date:';
		$_LANG['_INVCS']['l_Date_Due']						= 'Date Due:';
		$_LANG['_INVCS']['l_Date_Paid']						= 'Date Paid:';
		$_LANG['_INVCS']['l_Date_Paid_NReq']					= 'Date Paid (*):';
		$_LANG['_INVCS']['l_Delivered']						= 'Delivered:';
		$_LANG['_INVCS']['l_Delivery_Method']					= 'Delivery Method:';
		$_LANG['_INVCS']['l_Description']						= 'Description:';
		$_LANG['_INVCS']['l_Email']							= 'Email:';
		$_LANG['_INVCS']['l_Fax']							= 'Fax No.:';
		$_LANG['_INVCS']['l_Full_Name']						= 'Full Name:';
		$_LANG['_INVCS']['l_ID']								= 'ID:';
		$_LANG['_INVCS']['l_Invoice_ID']						= 'Invoice ID:';
		$_LANG['_INVCS']['l_Invoice_Date']						= 'Invoice Date:';
		$_LANG['_INVCS']['l_Invoice_Status']					= 'Invoice Status:';
		$_LANG['_INVCS']['l_Item_Cost']						= 'Item Cost:';
		$_LANG['_INVCS']['l_Item_No']							= 'Item No.:';
		$_LANG['_INVCS']['l_Last_Nag']						= 'Last Reminder';
		$_LANG['_INVCS']['l_Name']							= 'Name:';
		$_LANG['_INVCS']['l_Pages']							= 'Page(s):';
		$_LANG['_INVCS']['l_Pay_Link']						= 'Pay Link:';
		$_LANG['_INVCS']['l_Phone']							= 'Phone No.:';
		$_LANG['_INVCS']['l_Product']							= 'Product:';
		$_LANG['_INVCS']['l_Recurring']						= 'Recurring:';
		$_LANG['_INVCS']['l_Recurring_Processed']				= 'Recurr. Processed:';
		$_LANG['_INVCS']['l_State_Prov']						= 'State / Prov.:';
		$_LANG['_INVCS']['l_Status']							= 'Status:';
		$_LANG['_INVCS']['l_Status_Auto']						= 'Status-Auto:';
		$_LANG['_INVCS']['l_SubTotal_Cost']					= 'Sub-Total:';
		$_LANG['_INVCS']['l_Tax_Number']                        	= 'Tax Registration:';
		$_LANG['_INVCS']['l_Terms']							= 'Terms:';
		$_LANG['_INVCS']['l_Total_Charges']					= 'Total Charges:';
		$_LANG['_INVCS']['l_Total_Cost']						= 'Total Cost:';
		$_LANG['_INVCS']['l_Total_Cost_NReq']					= 'Total Cost (*):';
		$_LANG['_INVCS']['l_Total_Credits']					= 'Total Credits:';
		$_LANG['_INVCS']['l_Total_Paid']						= 'Total Paid:';
		$_LANG['_INVCS']['l_Trans_Amount']						= 'Amount:';
		$_LANG['_INVCS']['l_Trans_Amount_Due']					= 'Amount Due:';
		$_LANG['_INVCS']['l_Trans_Date']						= 'Date:';
		$_LANG['_INVCS']['l_Trans_Description']					= 'Description:';
		$_LANG['_INVCS']['l_Trans_Origin']						= 'Origin:';
		$_LANG['_INVCS']['l_Trans_Type']						= 'Type:';
		$_LANG['_INVCS']['l_User_Name']						= 'User Name:';
		$_LANG['_INVCS']['l_Zip_Postal_Code']					= 'Zip / Postal Code:';

# Language Variables: index.php
		$_LANG['_INVCS']['Auto_Invoice_Update_Results']			= 'Auto Invoice Update Results';
		$_LANG['_INVCS']['Auto_Invoice_Email_Results']			= 'Auto Invoice Email Results';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Confirmation']		= 'Copy Invoice Entry Confirmation';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Message']			= 'Are You Sure You Want to Copy Entry ID';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Message_Cont']		= 'and all the associated invoice items?';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Results']			= 'Copy Invoice Entry Results';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Results_01']			= 'An error occurred trying to copy Invoice ID';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Results_02']			= 'The Invoice ID';
		$_LANG['_INVCS']['Copy_Invoice_Entry_Results_03']			= 'has been copied to Invoice ID';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Confirmation']		= 'Delete Invoice Entry Confirmation';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Message_Cont']		= 'and all the associated invoice items?';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Results']			= 'Delete Invoice Entry Results';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Results_01']		= 'The following invoices items deleted';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Results_02']		= 'Deleted client invoices';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Results_03']		= 'Deleted client invoice items';
		$_LANG['_INVCS']['Delete_Invoice_Entry_Results_04']		= 'Deleted client invoice transactions';
		$_LANG['_INVCS']['Delete_IItem_Entry_Confirmation']		= 'Delete Invoice Item Entry Confirmation';
		$_LANG['_INVCS']['Delete_IItem_Entry_Message']			= 'Are You Sure You Want to Delete Item Entry ID';
		$_LANG['_INVCS']['Delete_IItem_Entry_Results']			= 'Delete Invoice Item Entry Results';
		$_LANG['_INVCS']['Delete_IItem_Entry_Results_01']			= 'Entry deleted.';
		$_LANG['_INVCS']['Delete_Trans_Entry_Confirmation']		= 'Delete Invoice Transaction Item Entry Confirmation';
		$_LANG['_INVCS']['Delete_Trans_Entry_Message']			= 'Are You Sure You Want to Delete Item Entry ID';
		$_LANG['_INVCS']['Delete_Trans_Entry_Results']			= 'Delete Invoice Transaction Item Entry Results';
		$_LANG['_INVCS']['Delete_Trans_Entry_Results_01']			= 'Entry deleted.';
		$_LANG['_INVCS']['Set_Payment_Entry_Confirmation']		= 'Set Payment Confirmation';
		$_LANG['_INVCS']['Set_Payment_Entry_Message']			= 'Setup Payment Transaction for Invoice ID';
		$_LANG['_INVCS']['Set_Payment_Entry_Message_Cont']		= '';

		$_LANG['_INVCS']['View_Client_Invc_Transactions']			= 'View Client Invoice Transactions';
		$_LANG['_INVCS']['View_Client_Invc_Transactions_For']		= 'View Client Invoice Transactions For';
		$_LANG['_INVCS']['View_Client_Invoices']				= 'View Client Invoices';
		$_LANG['_INVCS']['View_Client_Invoices_For']				= 'View Client Invoices For';
		$_LANG['_INVCS']['View_Invoices']						= 'View Invoices';

		$_LANG['_INVCS']['eMail_Invoice_Confirmation']			= 'eMail Invoice Confirmation';
		$_LANG['_INVCS']['eMail_Invoice_Message_prefix']			= 'Are You Sure You Want to Email Invoice ID';
		$_LANG['_INVCS']['eMail_Invoice_Message_suffix']			= 'to the client?';

# Language Variables: Email Invoice (invoices_funcs.php:function do_mail_invoice())
	# Caution- padded spaces are needed for email items to line up
		$_LANG['_INVCS']['INV_EMAIL_01']						= 'Item No:          ';
		$_LANG['_INVCS']['INV_EMAIL_02']						= 'Item Name:        ';
		$_LANG['_INVCS']['INV_EMAIL_03']						= 'Item Description: ';
		$_LANG['_INVCS']['INV_EMAIL_04']						= 'Item Cost:        ';
		$_LANG['_INVCS']['INV_EMAIL_05']						= '';

		$_LANG['_INVCS']['INV_EMAIL_SUBJECT_PRE']				= '- Invoice';
		$_LANG['_INVCS']['INV_EMAIL_SUBJECT_SUF']				= '';

		$_LANG['_INVCS']['PYT_EMAIL_SUBJECT_PRE']				= '- Payment';
		$_LANG['_INVCS']['PYT_EMAIL_SUBJECT_SUF']				= '';

		$_LANG['_INVCS']['INV_EMAIL_MSG_01_PRE']				= 'Invoice ID:';
		$_LANG['_INVCS']['INV_EMAIL_MSG_01_SUF']				= 'not located.';
		$_LANG['_INVCS']['INV_EMAIL_MSG_02_PRE']				= 'Invoice ID:';
		$_LANG['_INVCS']['INV_EMAIL_MSG_02_SUF']				= 'items not located.';

		$_LANG['_INVCS']['INV_EMAIL_MSG_03_L1']					= 'An error has occurred, Please try again.';
		$_LANG['_INVCS']['INV_EMAIL_MSG_03_L2']					= 'If problem continues, contact support via contact form.';
		$_LANG['_INVCS']['INV_EMAIL_MSG_04_PRE']				= 'The Invoice ID';
		$_LANG['_INVCS']['INV_EMAIL_MSG_04_SUF']				= 'email has been sent.';
		$_LANG['_INVCS']['INV_EMAIL_RESULT_TITLE']				= 'eMail Results: Invoice';

# Page: Data Entry and errors
		$_LANG['_INVCS']['INV_ADD_ITEM_MSG_TXT01']				= 'All fields required, unless selecting from products listing.';
		$_LANG['_INVCS']['INV_ADD_ITEM_MSG_TXT02']				= 'Check to add product from list below.';

		$_LANG['_INVCS']['INV_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_INVCS']['INV_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_INVCS']['INV_ERR_ERR01']						= 'Invoice ID';
		$_LANG['_INVCS']['INV_ERR_ERR02']						= 'Status';
		$_LANG['_INVCS']['INV_ERR_ERR03']						= 'Client';
		$_LANG['_INVCS']['INV_ERR_ERR04']						= 'Total Cost';
		$_LANG['_INVCS']['INV_ERR_ERR04a']						= 'Total Paid';
		$_LANG['_INVCS']['INV_ERR_ERR05']						= 'Invoice Date';
		$_LANG['_INVCS']['INV_ERR_ERR06']						= 'Date Due';
		$_LANG['_INVCS']['INV_ERR_ERR07']						= 'Date Paid';
		$_LANG['_INVCS']['INV_ERR_ERR08']						= 'Billing Cycle';
		$_LANG['_INVCS']['INV_ERR_ERR09']						= 'Pay Link';
		$_LANG['_INVCS']['INV_ERR_ERR10']						= 'Terms';
		$_LANG['_INVCS']['INV_ERR_ERR11']						= 'Delivery Method';
		$_LANG['_INVCS']['INV_ERR_ERR12']						= 'Total Paid';
		$_LANG['_INVCS']['INV_ERR_ERR13']						= 'Last Nag Email';
		$_LANG['_INVCS']['INV_ERR_ERR14']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR15']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR16']						= 'Item No.';
		$_LANG['_INVCS']['INV_ERR_ERR17']						= 'Name';
		$_LANG['_INVCS']['INV_ERR_ERR18']						= 'Description';
		$_LANG['_INVCS']['INV_ERR_ERR19']						= 'Item Cost';
		$_LANG['_INVCS']['INV_ERR_ERR20']						= 'Product';
		$_LANG['_INVCS']['INV_ERR_ERR21']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR22']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR23']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR24']						= 'xxx';
		$_LANG['_INVCS']['INV_ERR_ERR25']						= 'xxx';

		$_LANG['_INVCS']['INVCS_CRON_CONFIG']					= 'If the /coin_cron/invoices.php file is <i>not</i> called via wget or curl or a browser simulator, you <b>must</b> configure the URL to your website in /coin_cron/cron_config.php';
		$_LANG['_INVCS']['l_Auto_Email_SoonDue']				= 'Soon-Due Reminders';
		$_LANG['_INVCS']['Auto_Email_SoonDue_Results']			= 'Auto Email SoonDue Results';

# You may customize the invoice and payment receipt email subjects with replaceable parameters.
# If you leave the variable blank, the default subject will be used.
#	IF you wish to use parameters:
#		%SITENAME%		= Ssite short name
#		%INV_NO%		= The current invoice number
#		%INV_AMT_TTL%	= Invoice Total Amount
#		%INV_AMT_PAID%	= Amount Paid to date on invoice
#		%AMT_BAL_DUE%	= Balance still due on invoice
#		%DATE_DUE%		= Date invoice is due to be paid by
#		%DATE_ISSUED%	= Date invoice was issued
#		%INV_TERMS%		= Payment terms
#		%INV_STATUS%	= Status (due, overdue, etc.)
#		%INV_CYCLE%		= Invoice cycle (monthly, annual, etc)
#		%CLIENT_NAME%	= Client's name, in "FirstName LastName" format

	$_LANG['_INVCS']['INV_EMAIL_SUBJECT'] = '%SITENAME% - Invoice: %INV_NO%, Due: %DATE_DUE%, Amount: %INV_AMT_TTL%';

	$_LANG['_INVCS']['PYT_EMAIL_SUBJECT'] = '%SITENAME% - Payment Receipt, Invoice: %INV_NO%';


# Message added to invoice specifying that notice was sent.
# The date, plus the template name, plus a space, will be pre-pended.
	$_LANG['_INVCS']['INV_OVERDUE_APPEND'] 						= 'notice auto-sent.';


# Message added to bottom of "print" invoices
	$_LANG['_INVCS']['INV_PRINT_FOOTER'] 						= '';
?>