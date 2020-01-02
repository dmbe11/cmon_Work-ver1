<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Summary
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_cc.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
$_LANG['_CC']['Gross_Profit_For_Period']					= 'Gross Profit For Period';
$_LANG['_CC']['Gross_Margin_For_Period']					= 'Margin For Period';
$_LANG['_CC']['Profitability']							= 'Financial';
$_LANG['_CC']['Days_In_Period']							= 'Days In Period';
$_LANG['_CC']['Profitability_Note']						= 'Because phpCOIN has no control over the type of expenses that you entered, you are cautioned that the following table may <i>not</i> be suitable for submission to your tax authorities.';
$_LANG['_CC']['Gross_Profit_Per_Day']						= 'Gross Profit Per Day';

		$_LANG['_CC']['Administrator_Command_Center']			= 'Administrator Command Center';
		$_LANG['_CC']['l_Amount']							= 'Amount:';
		$_LANG['_CC']['AND']								= 'AND';
		$_LANG['_CC']['Balance_Due']							= 'Balance Due';
		$_LANG['_CC']['Clients']								= 'Clients';
		$_LANG['_CC']['lc_client']							= 'client';
		$_LANG['_CC']['lc_clients']							= 'clients';
		$_LANG['_CC']['lc_client_s']							= 'client(s)';
		$_LANG['_CC']['Closed']								= 'Closed';
		$_LANG['_CC']['lc_closed']							= 'closed';
		$_LANG['_CC']['days']								= 'days';
		$_LANG['_CC']['l_Date']								= 'Date:';
		$_LANG['_CC']['l_Description']						= 'Description:';
		$_LANG['_CC']['Domains']								= 'Domains';
		$_LANG['_CC']['lc_domain']							= 'domain';
		$_LANG['_CC']['lc_domains']							= 'domains';
		$_LANG['_CC']['lc_domain_s']							= 'domain(s)';
		$_LANG['_CC']['Expired']								= 'Expired';
		$_LANG['_CC']['Expired']								= 'Expired';
		$_LANG['_CC']['Expiring_In']							= 'Expiring In';
		$_LANG['_CC']['Found_Items']							= 'Found Items';
		$_LANG['_CC']['HelpDesk']							= 'HelpDesk';
		$_LANG['_CC']['Invoices']							= 'Invoices';
		$_LANG['_CC']['lc_invoice']							= 'invoice';
		$_LANG['_CC']['lc_invoices']							= 'invoices';
		$_LANG['_CC']['lc_invoice_s']							= 'invoice(s)';
		$_LANG['_CC']['By_Cycle']							= 'By Cycle';
		$_LANG['_CC']['No_Items_Found']						= 'No items found for criteria entered.';
		$_LANG['_CC']['None']								= 'None';
		$_LANG['_CC']['Open']								= 'Open';
		$_LANG['_CC']['lc_open']								= 'open';
		$_LANG['_CC']['on']									= 'on';
		$_LANG['_CC']['or']									= 'or';
		$_LANG['_CC']['OR']									= 'OR';
		$_LANG['_CC']['Orders']								= 'Orders';
		$_LANG['_CC']['lc_order']							= 'order';
		$_LANG['_CC']['lc_orders']							= 'orders';
		$_LANG['_CC']['lc_order_s']							= 'order(s)';
		$_LANG['_CC']['Please_Select']						= 'Please Select';
		$_LANG['_CC']['Active_Products']						= 'Active Product Orders';
		$_LANG['_CC']['lc_products']							= 'products';
		$_LANG['_CC']['Search_Clients']						= 'Search Clients';
		$_LANG['_CC']['Search_Domains']						= 'Search Domains';
		$_LANG['_CC']['Search_Helpdesk']						= 'Search Helpdesk';
		$_LANG['_CC']['Search_Invoices']						= 'Search Invoices';
		$_LANG['_CC']['Search_Options']						= 'Search Options';
		$_LANG['_CC']['Search_Orders']						= 'Search Orders';
		$_LANG['_CC']['Search_Transactions']					= 'Search Invoice Transactions';
		$_LANG['_CC']['lc_server_s']							= 'server(s)';
		$_LANG['_CC']['Sent_And_After']						= 'And After';
		$_LANG['_CC']['Sent_And_Before']						= 'And Before';
		$_LANG['_CC']['Servers']								= 'Servers';
		$_LANG['_CC']['Server_Accounts']						= 'Server Accounts (SACC)';
		$_LANG['_CC']['Sorry_Administrative_Function_Only']		= 'Sorry- Administrative Function Only';
		$_LANG['_CC']['Summary']								= 'Summary';
		$_LANG['_CC']['lc_support_ticket']						= 'support ticket';
		$_LANG['_CC']['lc_support_tickets']					= 'support tickets';
		$_LANG['_CC']['lc_support_ticket_s']					= 'support ticket(s)';
		$_LANG['_CC']['Ticket']								= 'Ticket';
		$_LANG['_CC']['Total']								= 'Total';
		$_LANG['_CC']['Total_of']							= 'Total of:';
		$_LANG['_CC']['totalling']							= 'totalling';
		$_LANG['_CC']['Welcome']								= 'Welcome';
		$_LANG['_CC']['Within']								= 'Within';

$_LANG['_CC']['Status']									= 'Status';
$_LANG['_CC']['Invoiced_Products']							= 'Invoiced Products';
$_LANG['_CC']['Quantity']								= 'QTY';
$_LANG['_CC']['Operating_Expenses']						= 'Expenses';
$_LANG['_CC']['lc_expenses']								= 'expenses';
$_LANG['_CC']['Search_Bills']								= 'Search Bills';
$_LANG['_CC']['l_Bill_ID']								= 'Bill ID:';
$_LANG['_CC']['l_Supplier_ID']							= 'Supplier ID:';
$_LANG['_CC']['l_Company']								= 'Company:';
$_LANG['_CC']['Search_Bill_Transactions']					= 'Search Bill Transactions';
$_LANG['_CC']['Invoice_Taxes']							= 'Invoice Taxes';
$_LANG['_CC']['Bill_Taxes']								= 'Bill Taxes';
$_LANG['_CC']['lc_bill']									= 'bill';
$_LANG['_CC']['lc_bills']								= 'bills';
$_LANG['_CC']['lc_bill_s']								= 'bill(s)';
$_LANG['_CC']['Summary_Dates']							= 'Summary Dates';
$_LANG['_CC']['Start_Date']								= 'Start Date:';
$_LANG['_CC']['End_Date']								= 'End Date:';
$_LANG['_CC']['B_Submit']								= 'Submit';

$_LANG['_CC']['Search_Invoiced_Products']					= 'Search Invoiced Products';
$_LANG['_CC']['Search_Billed_Items']						= 'Search Billed Items';
$_LANG['_CC']['Date_Issued']								= 'Date Issued';
$_LANG['_CC']['Date_Due']								= 'Date Due';
$_LANG['_CC']['Invoices_With_Product']						= 'Invoices With Product:';
$_LANG['_CC']['Bills_With_Item']							= 'Bills With Item:';

$_LANG['_CC']['Bills']									= 'Bills';
$_LANG['_CC']['Expense_Items']							= 'Expense Items';

# Language Variables: Some Buttons
		$_LANG['_CC']['B_Reset']								= 'Reset';
		$_LANG['_CC']['B_Search']							= 'Search';

# Language Variables: Common Labels (note : on end)
		$_LANG['_CC']['l_Actions']							= 'Actions:';
		$_LANG['_CC']['l_Client_ID']							= 'Client ID:';
		$_LANG['_CC']['l_Company']							= 'Company:';
		$_LANG['_CC']['l_Domain_Name']						= 'Domain Name:';
		$_LANG['_CC']['l_Domain_Expiration']					= 'Domain Expiration:';
		$_LANG['_CC']['l_Email']								= 'Email:';
		$_LANG['_CC']['l_First_Name']							= 'First Name:';
		$_LANG['_CC']['l_Id']								= 'Id:';
		$_LANG['_CC']['l_Invoice_ID']							= 'Invoice ID:';
		$_LANG['_CC']['l_Last_Name']							= 'Last Name:';
		$_LANG['_CC']['l_Name']								= 'Name:';
		$_LANG['_CC']['l_Order_ID']							= 'Order ID:';
		$_LANG['_CC']['l_Origin']							= 'Origin:';
		$_LANG['_CC']['l_Pages']								= 'Page(s):';
		$_LANG['_CC']['l_Product']							= 'Product:';
		$_LANG['_CC']['l_Referred_By']						= 'Referred By:';
		$_LANG['_CC']['l_SACC_Expiration']						= 'SACC Expiration:';
		$_LANG['_CC']['l_Search_Type']						= 'Search Type:';
		$_LANG['_CC']['l_Subject']							= 'Subject:';
		$_LANG['_CC']['l_Ticket_ID']							= 'Ticket ID:';
		$_LANG['_CC']['l_Type']								= 'Type:';
		$_LANG['_CC']['l_User_Name']							= 'User Name:';
		$_LANG['_CC']['l_Vendor']							= 'Vendor:';

?>