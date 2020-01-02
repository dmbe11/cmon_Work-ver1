<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Bills
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_bills.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_BILLS']['Form_Title']						= 'Bill';
		$_LANG['_BILLS']['Invoice_Number']						= 'Bill/Invoice Number:';
		$_LANG['_BILLS']['Actions']							= 'Actions';
		$_LANG['_BILLS']['Apply_Tax_01']						= 'Apply Tax 01:';
		$_LANG['_BILLS']['Apply_Tax_02']						= 'Apply Tax 02:';
		$_LANG['_BILLS']['AutoCalc_Tax']						= 'AutoCalc Tax Amounts:';
		$_LANG['_BILLS']['Auto_Copy_Recurring']					= 'Auto-Copy Recurring';
		$_LANG['_BILLS']['Auto_Bill_Copy_Results']				= 'Auto Bill Copy Results';
		$_LANG['_BILLS']['Auto_Update_Status']					= 'Auto-Update Status';
		$_LANG['_BILLS']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_BILLS']['auto-assigned']						= 'auto-assigned';
		$_LANG['_BILLS']['autocalcs_on_save']					= 'autocalcs on save';
		$_LANG['_BILLS']['Bill_To']							= 'Bill To';
		$_LANG['_BILLS']['Calc_Tax_02_On_01']					= 'Calc Tax 02 On Tax 01';
		$_LANG['_BILLS']['charge']							= 'charge';
		$_LANG['_BILLS']['Supplier_Information']				= 'Supplier Information';
		$_LANG['_BILLS']['Supplier_Bill_Status_Auto_Update']		= 'Supplier Bill Status Auto-Update';
		$_LANG['_BILLS']['Supplier_Bills']						= 'Suppliers Bills';
		$_LANG['_BILLS']['Supplier_Bill_Transactions']			= 'Suppliers Bill Transactions';
		$_LANG['_BILLS']['credit']							= 'credit';
		$_LANG['_BILLS']['debit']							= 'debit';
		$_LANG['_BILLS']['denotes_optional_items']				= 'denotes optional items';
		$_LANG['_BILLS']['due']								= 'due';
		$_LANG['_BILLS']['Error_Bill_Not_Found']				= 'Error- Bill ID not found !';
		$_LANG['_BILLS']['Function_Disabled']					= 'Function Disabled';
		$_LANG['_BILLS']['Bill_Information']					= 'Bill Information';
		$_LANG['_BILLS']['Bill_Items']						= 'Bill Items';
		$_LANG['_BILLS']['Bill_Items_Entry']					= 'Bill Items Entry';
		$_LANG['_BILLS']['Bills_Entry']						= 'Bills Entry';
		$_LANG['_BILLS']['Items_Editor']						= 'Items Editor';
		$_LANG['_BILLS']['no_commas']							= 'no commas';
		$_LANG['_BILLS']['none']								= 'none';
		$_LANG['_BILLS']['of']								= 'of';
		$_LANG['_BILLS']['percent_of_total']					= 'percent of total';
		$_LANG['_BILLS']['Please_Select']						= 'Please Select';
		$_LANG['_BILLS']['Remit_To']							= 'Remit To';
		$_LANG['_BILLS']['Select_Cycle']						= 'Select Cycle';
		$_LANG['_BILLS']['Set_Bill_To_Paid']					= 'Set Bill to paid';
		$_LANG['_BILLS']['Tax_Amount']						= 'Tax Amount';
		$_LANG['_BILLS']['Tax_Rate']							= 'Tax Rate';
		$_LANG['_BILLS']['Terms']							= 'Terms';
		$_LANG['_BILLS']['total_entries']						= 'total entries';
		$_LANG['_BILLS']['View_Supplier_Bill_ID']				= 'View Supplier Bill ID:';
		$_LANG['_BILLS']['Welcome']							= 'Welcome';
		$_LANG['_BILLS']['Tax_Amount_Manual_Calc']				= 'Enter tax amount if "AutoCalc Tax Amounts" is NOT checked';

# Language Variables: Some Buttons
		$_LANG['_BILLS']['B_Add']							= 'Add';
		$_LANG['_BILLS']['B_Continue']						= 'Continue';
		$_LANG['_BILLS']['B_Copy_Bill']						= 'Copy Bill';
		$_LANG['_BILLS']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_BILLS']['B_Edit']							= 'Edit';
		$_LANG['_BILLS']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_BILLS']['B_Reset']							= 'Reset';
		$_LANG['_BILLS']['B_Save']							= 'Save';
		$_LANG['_BILLS']['B_Set_Paid']						= 'Set Paid';
		$_LANG['_BILLS']['B_Submit']							= 'Submit';

# Language Variables: Common Labels (note : on end)
		$_LANG['_BILLS']['l_Address']							= 'Address:';
		$_LANG['_BILLS']['l_Amount']							= 'Amount:';
		$_LANG['_BILLS']['l_Auto_Update_Status']				= 'Auto-Update Status:';
		$_LANG['_BILLS']['l_Auto_Copy_Recurring']				= 'Auto-Copy Recurring:';
		$_LANG['_BILLS']['l_Balance']							= 'Balance:';
		$_LANG['_BILLS']['l_Billing_Cycle']					= 'Billing Cycle:';
		$_LANG['_BILLS']['l_Charges_To_Account']				= 'Charges To Account:';
		$_LANG['_BILLS']['l_Credits_To_Account']				= 'Credits To Account:';
		$_LANG['_BILLS']['l_City']							= 'City:';
		$_LANG['_BILLS']['l_Supplier']						= 'Supplier:';
		$_LANG['_BILLS']['l_Supplier_ID']						= 'Supplier ID:';
		$_LANG['_BILLS']['l_Supplier_Name']					= 'Contact Name:';
		$_LANG['_BILLS']['l_Company']							= 'Company:';
		$_LANG['_BILLS']['l_Cost']							= 'Cost:';
		$_LANG['_BILLS']['l_Country']							= 'Country:';
		$_LANG['_BILLS']['l_Date']							= 'Date:';
		$_LANG['_BILLS']['l_Date_Due']						= 'Date Due:';
		$_LANG['_BILLS']['l_Date_Paid']						= 'Date Paid:';
		$_LANG['_BILLS']['l_Date_Paid_NReq']					= 'Date Paid (*):';
		$_LANG['_BILLS']['l_Description']						= 'Description:';
		$_LANG['_BILLS']['l_Email']							= 'Email:';
		$_LANG['_BILLS']['l_Fax']							= 'Fax No.:';
		$_LANG['_BILLS']['l_Full_Name']						= 'Full Name:';
		$_LANG['_BILLS']['l_ID']								= 'ID:';
		$_LANG['_BILLS']['l_Bill_ID']							= 'Bill ID:';
		$_LANG['_BILLS']['l_Bill_Date']						= 'Bill Date:';
		$_LANG['_BILLS']['l_Bill_Status']						= 'Bill Status:';
		$_LANG['_BILLS']['l_Item_Cost']						= 'Item Cost:';
		$_LANG['_BILLS']['l_Item_No']							= 'Item No.:';
		$_LANG['_BILLS']['l_Name']							= 'Name:';
		$_LANG['_BILLS']['l_Pages']							= 'Page(s):';
		$_LANG['_BILLS']['l_Phone']							= 'Phone No.:';
		$_LANG['_BILLS']['l_Product']							= 'Product:';
		$_LANG['_BILLS']['l_Recurring']						= 'Recurring:';
		$_LANG['_BILLS']['l_Recurring_Processed']				= 'Recurr. Processed:';
		$_LANG['_BILLS']['l_State_Prov']						= 'State / Prov.:';
		$_LANG['_BILLS']['l_Status']							= 'Status:';
		$_LANG['_BILLS']['l_Status_Auto']						= 'Status-Auto:';
		$_LANG['_BILLS']['l_SubTotal_Cost']					= 'Sub-Total:';
		$_LANG['_BILLS']['l_Tax_Number']                        	= 'Tax Registration:';
		$_LANG['_BILLS']['l_Total_Charges']					= 'Total Charges:';
		$_LANG['_BILLS']['l_Total_Cost']						= 'Total Cost:';
		$_LANG['_BILLS']['l_Total_Cost_NReq']					= 'Total Cost (*):';
		$_LANG['_BILLS']['l_Total_Credits']					= 'Total Credits:';
		$_LANG['_BILLS']['l_Total_Paid']						= 'Total Paid:';
		$_LANG['_BILLS']['l_Trans_Amount']						= 'Amount:';
		$_LANG['_BILLS']['l_Trans_Amount_Due']					= 'Amount Due:';
		$_LANG['_BILLS']['l_Trans_Date']						= 'Date:';
		$_LANG['_BILLS']['l_Trans_Description']					= 'Description:';
		$_LANG['_BILLS']['l_Trans_Origin']						= 'Origin:';
		$_LANG['_BILLS']['l_Trans_Type']						= 'Type:';
		$_LANG['_BILLS']['l_Zip_Postal_Code']					= 'Zip / Postal Code:';

# Language Variables: index.php
		$_LANG['_BILLS']['Auto_Bill_Update_Results']				= 'Auto Bill Update Results';
		$_LANG['_BILLS']['Copy_Bill_Entry_Confirmation']			= 'Copy Bill Entry Confirmation';
		$_LANG['_BILLS']['Copy_Bill_Entry_Message']				= 'Are You Sure You Want to Copy Entry ID';
		$_LANG['_BILLS']['Copy_Bill_Entry_Message_Cont']			= 'and all the associated Bill items?';
		$_LANG['_BILLS']['Copy_Bill_Entry_Results']				= 'Copy Bill Entry Results';
		$_LANG['_BILLS']['Copy_Bill_Entry_Results_01']			= 'An error occurred trying to copy Bill ID';
		$_LANG['_BILLS']['Copy_Bill_Entry_Results_02']			= 'The Bill ID';
		$_LANG['_BILLS']['Copy_Bill_Entry_Results_03']			= 'has been copied to Bill ID';
		$_LANG['_BILLS']['Delete_Bill_Entry_Confirmation']		= 'Delete Bill Entry Confirmation';
		$_LANG['_BILLS']['Delete_Bill_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_BILLS']['Delete_Bill_Entry_Message_Cont']		= 'and all the associated Bill items?';
		$_LANG['_BILLS']['Delete_Bill_Entry_Results']			= 'Delete Bill Entry Results';
		$_LANG['_BILLS']['Delete_Bill_Entry_Results_01']			= 'The following Bills items deleted';
		$_LANG['_BILLS']['Delete_Bill_Entry_Results_02']			= 'Deleted Supplier Bills';
		$_LANG['_BILLS']['Delete_Bill_Entry_Results_03']			= 'Deleted Supplier Bill items';
		$_LANG['_BILLS']['Delete_Bill_Entry_Results_04']			= 'Deleted Supplier Bill transactions';
		$_LANG['_BILLS']['Delete_IItem_Entry_Confirmation']		= 'Delete Bill Item Entry Confirmation';
		$_LANG['_BILLS']['Delete_IItem_Entry_Message']			= 'Are You Sure You Want to Delete Item Entry ID';
		$_LANG['_BILLS']['Delete_IItem_Entry_Results']			= 'Delete Bill Item Entry Results';
		$_LANG['_BILLS']['Delete_IItem_Entry_Results_01']			= 'Entry deleted.';
		$_LANG['_BILLS']['Delete_Trans_Entry_Confirmation']		= 'Delete Bill Transaction Item Entry Confirmation';
		$_LANG['_BILLS']['Delete_Trans_Entry_Message']			= 'Are You Sure You Want to Delete Item Entry ID';
		$_LANG['_BILLS']['Delete_Trans_Entry_Results']			= 'Delete Bill Transaction Item Entry Results';
		$_LANG['_BILLS']['Delete_Trans_Entry_Results_01']			= 'Entry deleted.';
		$_LANG['_BILLS']['Set_Payment_Entry_Confirmation']		= 'Set Payment Confirmation';
		$_LANG['_BILLS']['Set_Payment_Entry_Message']			= 'Setup Payment Transaction for Bill ID';
		$_LANG['_BILLS']['Set_Payment_Entry_Message_Cont']		= '';

		$_LANG['_BILLS']['View_Supplier_Invc_Transactions']		= 'View Supplier Bill Transactions';
		$_LANG['_BILLS']['View_Supplier_Invc_Transactions_For']	= 'View Supplier Bill Transactions For';
		$_LANG['_BILLS']['View_Supplier_Bills']					= 'View Supplier Bills';
		$_LANG['_BILLS']['View_Supplier_Bills_For']				= 'View Supplier Bills For';
		$_LANG['_BILLS']['View_Bills']						= 'View Bills';


# Page: Data Entry and errors
		$_LANG['_BILLS']['BILL_ADD_ITEM_MSG_TXT01']				= 'All fields required, unless selecting from products listing.';
		$_LANG['_BILLS']['BILL_ADD_ITEM_MSG_TXT02']				= 'Check to add product from list below.';

		$_LANG['_BILLS']['BILL_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_BILLS']['BILL_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_BILLS']['BILL_ERR_ERR01']						= 'Bill ID';
		$_LANG['_BILLS']['BILL_ERR_ERR02']						= 'Status';
		$_LANG['_BILLS']['BILL_ERR_ERR03']						= 'Supplier';
		$_LANG['_BILLS']['BILL_ERR_ERR04']						= 'Total Cost';
		$_LANG['_BILLS']['BILL_ERR_ERR04a']					= 'Total Paid';
		$_LANG['_BILLS']['BILL_ERR_ERR05']						= 'Bill Date';
		$_LANG['_BILLS']['BILL_ERR_ERR06']						= 'Date Due';
		$_LANG['_BILLS']['BILL_ERR_ERR07']						= 'Date Paid';
		$_LANG['_BILLS']['BILL_ERR_ERR08']						= 'Billing Cycle';
		$_LANG['_BILLS']['BILL_ERR_ERR12']						= 'Total Paid';
		$_LANG['_BILLS']['BILL_ERR_ERR14']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR15']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR16']						= 'Item No.';
		$_LANG['_BILLS']['BILL_ERR_ERR17']						= 'Name';
		$_LANG['_BILLS']['BILL_ERR_ERR18']						= 'Description';
		$_LANG['_BILLS']['BILL_ERR_ERR19']						= 'Item Cost';
		$_LANG['_BILLS']['BILL_ERR_ERR20']						= 'Product';
		$_LANG['_BILLS']['BILL_ERR_ERR21']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR22']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR23']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR24']						= 'xxx';
		$_LANG['_BILLS']['BILL_ERR_ERR25']						= 'xxx';

		$_LANG['_BILLS']['BILLS_CRON_CONFIG']					= 'If the /coin_cron/invoices.php file is <i>not</i> called via wget or curl or a browser simulator, you <b>must</b> configure the URL to your website in /coin_cron/cron_config.php';

?>