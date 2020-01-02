<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Orders
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_orders.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_ORDERS']['Required']							= ' (Required)';
		$_LANG['_ORDERS']['An_error_occurred']					= 'An error occurred.';
		$_LANG['_ORDERS']['auto-assigned']						= 'auto-assigned';
		$_LANG['_ORDERS']['denotes_optional_items']				= 'denotes optional items';
		$_LANG['_ORDERS']['denotes_required_items']				= 'denotes required items';
		$_LANG['_ORDERS']['Entry_Deleted']						= 'Entry Deleted.';
		$_LANG['_ORDERS']['Error_Order_Not_Found']				= 'Error- Order ID not found !';
		$_LANG['_ORDERS']['of']								= 'of';
		$_LANG['_ORDERS']['Orders_Entry']						= 'Orders Entry';
		$_LANG['_ORDERS']['Password_Note']						= 'Input password and confirm only if you wish to change current.';
		$_LANG['_ORDERS']['Select_Billing_Cycle']				= 'Select Billing Cycle';
		$_LANG['_ORDERS']['Select_Payment_Type']				= 'Select Payment Type';
		$_LANG['_ORDERS']['Select_Request_Type']				= 'Select Request Type';
		$_LANG['_ORDERS']['total_entries']						= 'total entries';

# Language Variables: Some Buttons
		$_LANG['_ORDERS']['B_Add']							= 'Add';
		$_LANG['_ORDERS']['B_Continue']						= 'Continue';
		$_LANG['_ORDERS']['B_Delete_Entry']					= 'Delete Entry';
		$_LANG['_ORDERS']['B_Edit']							= 'Edit';
		$_LANG['_ORDERS']['B_Reset']							= 'Reset';
		$_LANG['_ORDERS']['B_Save']							= 'Save';
		$_LANG['_ORDERS']['B_Send_Email']						= 'Send Email';

# Language Variables: Common Labels (note : on end)
		$_LANG['_ORDERS']['l_Accepted_TOS']					= 'Accepted TOS:';
		$_LANG['_ORDERS']['l_Accepted_AUP']					= 'Accepted AUP:';
		$_LANG['_ORDERS']['l_Additional_Comments']				= 'Additional Comments:';

		$_LANG['_ORDERS']['l_Address_Street_1']					= 'Address Street 1:';
		$_LANG['_ORDERS']['l_Address_Street_2']					= 'Address Street 2:';

		$_LANG['_ORDERS']['l_City']							= 'City:';
		$_LANG['_ORDERS']['l_Client_ID']						= 'Client ID:';
		$_LANG['_ORDERS']['l_Client_IP']						= 'Client IP:';
		$_LANG['_ORDERS']['l_Client_Information']				= 'Client Information:';
		$_LANG['_ORDERS']['l_Client_Name:']					= 'Client Name:';
		$_LANG['_ORDERS']['l_Clients_Orders']					= 'Clients Orders:';
		$_LANG['_ORDERS']['l_Company']						= 'Company:';

		$_LANG['_ORDERS']['l_Country']						= 'Country:';
		$_LANG['_ORDERS']['l_Databases_mysql']					= 'Databases (mysql):';
		$_LANG['_ORDERS']['l_Date']							= 'Date:';
		$_LANG['_ORDERS']['l_Description']						= 'Description:';
		$_LANG['_ORDERS']['l_Domain']							= 'Domain:';
		$_LANG['_ORDERS']['l_Domain_Name']						= 'Domain Name:';
		$_LANG['_ORDERS']['l_Domain_Action']					= 'Domain Action:';
		$_LANG['_ORDERS']['l_Email']							= 'Email:';
		$_LANG['_ORDERS']['l_Email_Address']					= 'Email Address:';
		$_LANG['_ORDERS']['l_First_Name']						= 'First Name:';
		$_LANG['_ORDERS']['l_Hard_Disk_Space']					= 'Hard Disk Space:';
		$_LANG['_ORDERS']['l_Id']							= 'Id:';
		$_LANG['_ORDERS']['l_Item_No']						= 'Item No:';
		$_LANG['_ORDERS']['l_Invoice_ID']						= 'Invoice ID:';
		$_LANG['_ORDERS']['l_Last_Name']						= 'Last Name:';
		$_LANG['_ORDERS']['l_Mailboxes_POP']					= 'Mailboxes (POP):';
		$_LANG['_ORDERS']['l_Monthly_Traffic_bandwidth']			= 'Monthly Traffic (bandwidth):';
		$_LANG['_ORDERS']['l_Optional_Bill_Cycle']				= 'Optional Bill Cycle:';
		$_LANG['_ORDERS']['l_Optional_Payment']					= 'Optional Payment:';
		$_LANG['_ORDERS']['l_Order_Cost']						= 'Order Cost:';
		$_LANG['_ORDERS']['l_Order_Date']						= 'Order Date:';
		$_LANG['_ORDERS']['l_Order_DateTime']					= 'Order DateTime:';
		$_LANG['_ORDERS']['l_Order_ID']						= 'Order ID:';
		$_LANG['_ORDERS']['l_Order_Information']				= 'Order Information:';
		$_LANG['_ORDERS']['l_Order_Status']					= 'Order Status:';
		$_LANG['_ORDERS']['l_Pages']							= 'Page(s):';
		$_LANG['_ORDERS']['l_Password']						= 'Password:';
		$_LANG['_ORDERS']['l_Password_Confirm']					= 'Password Confirm:';
		$_LANG['_ORDERS']['l_Payment_Method']					= 'Payment Method:';
		$_LANG['_ORDERS']['l_Phone']							= 'Phone:';
		$_LANG['_ORDERS']['l_Product']						= 'Product:';
		$_LANG['_ORDERS']['l_Product_Name']					= 'Product Name:';
		$_LANG['_ORDERS']['l_Product_Description']				= 'Product Description:';
		$_LANG['_ORDERS']['l_Products_Ordered']					= 'Product(s) Ordered:';
		$_LANG['_ORDERS']['l_Referred_By']						= 'Referred By:';
		$_LANG['_ORDERS']['l_Referred_By_domain']				= 'Referred By (domain):';
		$_LANG['_ORDERS']['l_Request_Type']					= 'Request Type:';
		$_LANG['_ORDERS']['l_Security_Certificate']				= 'Security Certificate:';
		$_LANG['_ORDERS']['l_Shopping_Cart']					= 'Shopping Cart:';
		$_LANG['_ORDERS']['l_Status']							= 'Status:';
		$_LANG['_ORDERS']['l_State_Province']					= 'State / Province:';
		$_LANG['_ORDERS']['l_Unique_IP_Address']				= 'Unique IP Address:';
		$_LANG['_ORDERS']['l_Unit_Cost']						= 'Unit Cost:';
		$_LANG['_ORDERS']['l_User_Name']						= 'User Name:';
		$_LANG['_ORDERS']['l_User_Name_preferred']				= 'User Name (preferred):';
		$_LANG['_ORDERS']['l_Username']						= 'Username:';
		$_LANG['_ORDERS']['l_Vendor']							= 'Vendor:';
		$_LANG['_ORDERS']['l_Website_Authoring_pages']			= 'Website Authoring (pages):';
		$_LANG['_ORDERS']['l_Zip_Postal_Code']					= 'Zip / Postal Code:';

# Language Variables: index.php
		$_LANG['_ORDERS']['View_Client_Orders']					= 'View Client Orders';
		$_LANG['_ORDERS']['View_Client_Orders_For']				= 'View Client Orders For:';
		$_LANG['_ORDERS']['View_Client_Order_ID']				= 'View Client Order ID:';
		$_LANG['_ORDERS']['Delete_Order_Entry_Confirmation']		= 'Delete Order Entry Confirmation';
		$_LANG['_ORDERS']['Delete_Order_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ORDERS']['Delete_Order_Entry_Results']			= 'Delete Order Entry Results';

		$_LANG['_ORDERS']['eMail_Order_Confirmation']			= 'eMail Order Confirmation';
		$_LANG['_ORDERS']['eMail_Order_Message_prefix']			= 'Are You Sure You Want to Email Order ID';
		$_LANG['_ORDERS']['eMail_Order_Message_suffix']			= 'to client?';

# Language Variables: Place Orders Screens (orders_funcs_orders.php)
	# Pages Titles
		$_LANG['_ORDERS']['ORD_P01_TITLE']						= 'Place Order- Product/Payment Selection and Policy Acceptance';
		$_LANG['_ORDERS']['ORD_COR_TITLE']						= 'Quotation Request';
		$_LANG['_ORDERS']['ORD_P03_TITLE']						= 'Place Order- Client Information';
		$_LANG['_ORDERS']['ORD_P04_TITLE']						= 'Place Order- Final Confirmation';
		$_LANG['_ORDERS']['ORD_P05_TITLE_NORMAL']				= 'Order Information Saved- Proceed To Billing';
		$_LANG['_ORDERS']['ORD_P05_TITLE_COR']					= 'Quotation Request Processed';
		$_LANG['_ORDERS']['ORD_P06_TITLE_RETURN']				= 'Order Return From Billing';

	# All Order pages: Text and Objects
		// "Fine Print" for order form. Leave blank for NO fineprint
		$_LANG['_ORDERS']['ORD_FINEPRINT_MAIN']					= 'PLACE YOUR FINE PRINT FOR FIRST ORDER SCREEN HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';
		$_LANG['_ORDERS']['ORD_FINEPRINT_COR']					= 'PLACE YOUR FINE PRINT FOR QUOTE FORM HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';
		$_LANG['_ORDERS']['ORD_FINEPRINT_CUSTOMER_DATA']			= 'PLACE YOUR FINE PRINT FOR CUSTOMER ADDRESS ENTRY PAGE HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';
		$_LANG['_ORDERS']['ORD_FINEPRINT_CONFIRM']				= 'PLACE YOUR FINE PRINT FOR DATA CONFIRMATION PAGE HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';
		$_LANG['_ORDERS']['ORD_FINEPRINT_PAYLINK']				= 'PLACE YOUR FINE PRINT FOR DISPLAY PAYLINK PAGE HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';
		$_LANG['_ORDERS']['ORD_FINEPRINT_RETURN']				= 'PLACE YOUR FINE PRINT FOR ORDER RETURN PAGE HERE (edit coin_lang/lang_english/lang_orders.php OR coin_overrides/lang_XXXXX/lang_orders_override.php)';


	# Page 01: Text and Objects
		$_LANG['_ORDERS']['ORD_P01_TEXT01']					= 'Please confirm the desired product and select payment method.';
		$_LANG['_ORDERS']['ORD_P01_TEXT02']					= 'To proceed with the order, you must confirm you have read and agree to the following:'.$_nl;
		$_LANG['_ORDERS']['ORD_P01_IP_NOTE']					= 'IP Logged for fraud detection:'.$_sp.$_nl;
		$_LANG['_ORDERS']['ORD_P01_TOS']						= 'Terms Of Service';
		$_LANG['_ORDERS']['ORD_P01_AUP']						= 'Acceptable Use Policy';
		$_LANG['_ORDERS']['ORD_P01_CONTINUE']					= 'Continue';
		$_LANG['_ORDERS']['ORD_P01_TEXT03']					= 'Please choose the Quotation Request option for products / services not listed.';
		$_LANG['_ORDERS']['ORD_P01_COR']						= 'Quotation Request';

$_LANG['_ORDERS']['ORD_P01_SHOW_TOS']						= 'Display Terms Of Service';
$_LANG['_ORDERS']['ORD_P01_SHOW_AUP']						= 'Display Acceptable Use Policy';

	# Page COR: Text and Objects
		$_LANG['_ORDERS']['ORD_PCOR_TEXT01']					= 'Quotation Request:';
		$_LANG['_ORDERS']['ORD_PCOR_TEXT02']					= 'The following form is for submitting a price quote request for a custom order';
		$_LANG['_ORDERS']['ORD_PCOR_TEXT02']					.= ' (not included in product listing). Upon receiving your request,';
		$_LANG['_ORDERS']['ORD_PCOR_TEXT02']					.= ' we will process immediately and contact you with a price';
		$_LANG['_ORDERS']['ORD_PCOR_TEXT02']					.= ' for the service(s) requested, and instructions on how place your order.';
		$_LANG['_ORDERS']['ORD_COR_CONTINUE']					= 'Continue';
		$_LANG['_ORDERS']['ORD_COR_RESET']						= 'Reset';
		$_LANG['_ORDERS']['ORD_COR_CCOR']						= 'Cancel Quotation Request';

	# Page 02: Text and Objects
		$_LANG['_ORDERS']['ORD_P02_NO_DOMAIN']					= 'Enter <b>NONE</b> for no domain name';
		$_LANG['_ORDERS']['ORD_P02_PWORD_LEN']					= 'Password must be %MIN% to %MAX% characters long';	// %FLAG% will be replaced at run-time with $_CCFG['CLIENT_MIN_LEN_PWORD'] and $_CCFG['CLIENT_MAX_LEN_PWORD'] respectively
		$_LANG['_ORDERS']['ORD_P02_UNAME_CHARS']				= ' - (letters and numbers only)';

	# Page 03: Text and Objects
		$_LANG['_ORDERS']['ORD_P03_ERR01']						= '- Email appears to be invalid.';
		$_LANG['_ORDERS']['ORD_P03_ERR02']						= '- Domain name invalid ( example format';
		$_LANG['_ORDERS']['ORD_P03_ERR03']						= '- Domain name taken, please try again';
		$_LANG['_ORDERS']['ORD_P03_ERR04']						= '- Username is taken, please try again.';
		$_LANG['_ORDERS']['ORD_P03_ERR05']						= '- Password and Password Confirm must match.';
		$_LANG['_ORDERS']['ORD_P03_TEXT01']					= 'Please input the following information required for us to setup your account.';
		$_LANG['_ORDERS']['ORD_P03_TEXT02']					= 'Billing will be entered on secure third party billing site following account information.';
		$_LANG['_ORDERS']['ORD_P03_ERR_HDR']					= 'Data Entry Error- See flags';
		$_LANG['_ORDERS']['ORD_P03_ERR06']						= '- Some required fields may be blank.';
		$_LANG['_ORDERS']['ORD_P03_CONTINUE']					= 'Continue';
		$_LANG['_ORDERS']['ORD_P03_RESET']						= 'Reset';
		$_LANG['_ORDERS']['ORD_P03_START_OVER']					= 'Start Over';
		$_LANG['_ORDERS']['ORD_P03_ERR07']						= '- Email is taken, please try again.';

	# Page 04: Text and Objects
		$_LANG['_ORDERS']['ORD_P04_TEXT01']					= 'Please double check all the entered information.';
		$_LANG['_ORDERS']['ORD_P04_TEXT02']					= 'To continue from this point will insert the information into our system.';
		$_LANG['_ORDERS']['ORD_P04_EDIT_INFO']					= 'Edit Information';
		$_LANG['_ORDERS']['ORD_P04_CONTINUE']					= 'Continue';
		$_LANG['_ORDERS']['ORD_P04_START_OVER']					= 'Start Over';

	# Page 05: Text and Objects
		$_LANG['_ORDERS']['ORD_P05_TEXT01']					= 'Please read the notes, and click to order button';
		$_LANG['_ORDERS']['ORD_P05_TEXT01']					.= ' to complete the billing portion under secure conditions.';

		$_LANG['_ORDERS']['ORD_P05_LI_HDR']					= 'Please note the following:';

		$_LANG['_ORDERS']['ORD_P05_LI_01']						= 'Clicking the order button will transfer you to a secure server to handle billing information to complete the order. The selected third party billing service provides the billing service only, all purchased products and services are provided by '.$_CCFG['_PKG_NAME_SHORT'].'.';
		$_LANG['_ORDERS']['ORD_P05_LI_02']						= 'Once you complete the order, you should be returned to our site. Please be sure to either Complete or Cancel the order to permit proper record updates.';
		$_LANG['_ORDERS']['ORD_P05_LI_03']						= 'If you completed the order, you will receive a confirming email from the billing vendor. '.$_CCFG['_PKG_NAME_SHORT'].' will also receive a copy of that email.';
		$_LANG['_ORDERS']['ORD_P05_LI_04']						= 'We will process your order ASAP and send you an activation confirmation email to the address you provided. This will contain all your necessary information.';
		$_LANG['_ORDERS']['ORD_P05_LI_05']						= 'Thank you for choosing '.$_CCFG['_PKG_NAME_SHORT'].' for your internet service needs.';

		$_LANG['_ORDERS']['ORD_P05_LI_01_COR']					= 'We have recorded your Quotation Request and have emailed a copy of this request to you, at the email address you entered, for your records.';
		$_LANG['_ORDERS']['ORD_P05_LI_02_COR']					= 'We have also created a pending '.$_CCFG['_PKG_NAME_SHORT'].' client profile that can be used to login and complete your order (if you wish).';
		$_LANG['_ORDERS']['ORD_P05_LI_03_COR']					= 'We will process and respond to your request via email ASAP. Included in the reply will be directions for completing the order.';
		$_LANG['_ORDERS']['ORD_P05_LI_04_COR']					= 'Thank you for choosing '.$_CCFG['_PKG_NAME_SHORT'].' for your internet service needs.';

		$_LANG['_ORDERS']['ORD_P05_EMAIL_01']					= '- Order Processed Outgoing';
		$_LANG['_ORDERS']['ORD_P05_EMAIL_02']					= 'Order ID: ';
		$_LANG['_ORDERS']['ORD_P05_EMAIL_03']					= ' was just input into the system and client showed paylink.';

	# Page 06: Text and Objects
		$_LANG['_ORDERS']['ORD_P06_TEXT01']					= 'Welcome back to '.$_CCFG['_PKG_NAME_SHORT'];

		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					 = 'Congratulations and Thank You for choosing '.$_CCFG['_PKG_NAME_SHORT'];
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= ' for your hosting service.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= 'We will be processing your order as soon as possible and you will be receiving';
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= ' a confirmation email on your purchase from the billing vendor.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= 'In addition, we will be sending you an Activation email that will contain';
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= ' your account information.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_BUY']					.= 'Once again, we thank you for choosing '.$_CCFG['_PKG_NAME_SHORT'].'.<br><br>';

		$_LANG['_ORDERS']['ORD_P06_TEXT_CANCEL']				 = 'Thank You for reviewing our services but it appears you have changed';
		$_LANG['_ORDERS']['ORD_P06_TEXT_CANCEL']				.= ' your mind about selecting '.$_CCFG['_PKG_NAME_SHORT'].' for your hosting service.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_CANCEL']				.= 'If this was an error in the return function, or you had problems with your order,';
		$_LANG['_ORDERS']['ORD_P06_TEXT_CANCEL']				.= ' please contact us to assist you in correcting the issues.<br><br>';

		$_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN']				 = 'Thank You for reviewing our services but we must apologize as we were unable to determine your final actions.<br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN']				.= 'If you placed an order for our services, we will be contacting you as soon as we review the transactions.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN']				.= 'If you changed your mind about ordering our services, we thank you for considering us and wish you success in your search.<br><br>';
		$_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN']				.= 'If this was an error in the return function, or you had problems with your order,';
		$_LANG['_ORDERS']['ORD_P06_TEXT_UNKNOWN']				.= ' please contact us to assist you in correcting the issues.<br><br>';

		$_LANG['_ORDERS']['ORD_P06_EMAIL_YES']					= 'Yes';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_NO']					= 'No';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_UNKNOWN']				= 'Unknown';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_01']					= '- Order Processed Return';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_02']					= 'The following order has been processed returning from billing vendor.';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_03']					= 'Order ID:    ';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_04']					= 'Vendor:      ';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_05']					= 'Prod. Name:  ';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_06']					= 'Prod. Desc:  ';
		$_LANG['_ORDERS']['ORD_P06_EMAIL_07']					= 'Buy (Y/N):   ';

# Language Variables: Email Order (orders_funcs.php:function do_mail_order())
	# Caution- padded spaces are needed for email items to line up
		$_LANG['_ORDERS']['ORD_EMAIL_01']						= 'Order No:      ';
		$_LANG['_ORDERS']['ORD_EMAIL_02']						= 'Order Date:    ';
		$_LANG['_ORDERS']['ORD_EMAIL_03']						= 'Status:        ';
		$_LANG['_ORDERS']['ORD_EMAIL_04']						= 'Product:       ';
		$_LANG['_ORDERS']['ORD_EMAIL_05']						= 'Description:   ';
		$_LANG['_ORDERS']['ORD_EMAIL_06']						= 'Order Cost:    ';
		$_LANG['_ORDERS']['ORD_EMAIL_SUBJECT']					= '- Order';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_01_PRE']				= 'Order ID:';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_01_SUF']				= 'not located.';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_02_L1']				= 'An error has occurred, Please try again.';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_02_L2']				= 'If problem continues, contact support via contact form.';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_03_PRE']				= 'Order ID:';
		$_LANG['_ORDERS']['ORD_EMAIL_MSG_03_SUF']				= 'email has been sent.';
		$_LANG['_ORDERS']['ORD_EMAIL_RESULT_TITLE']				= 'eMail Results: Order';

# Language Variables: Email COR Order (orders_funcs.php:function do_cor_email())
	# Caution- padded spaces are needed for email items to line up
		$_LANG['_ORDERS']['COR_EMAIL_01']						= 'Client ID:         ';
		$_LANG['_ORDERS']['COR_EMAIL_02']						= 'Join Date:         ';
		$_LANG['_ORDERS']['COR_EMAIL_03']						= 'User Name:         ';
		$_LANG['_ORDERS']['COR_EMAIL_04']						= 'Email:             ';
		$_LANG['_ORDERS']['COR_EMAIL_05']						= 'Company:           ';
		$_LANG['_ORDERS']['COR_EMAIL_06']						= 'Full Name:         ';
		$_LANG['_ORDERS']['COR_EMAIL_07']						= 'Address Line 1:    ';
		$_LANG['_ORDERS']['COR_EMAIL_08']						= 'Address Line 2:    ';
		$_LANG['_ORDERS']['COR_EMAIL_09']						= 'City:              ';
		$_LANG['_ORDERS']['COR_EMAIL_10']						= 'State / Province:  ';
		$_LANG['_ORDERS']['COR_EMAIL_11']						= 'Country:           ';
		$_LANG['_ORDERS']['COR_EMAIL_12']						= 'Zip / Postal Code: ';
		$_LANG['_ORDERS']['COR_EMAIL_13']						= 'Phone:             ';
		$_LANG['_ORDERS']['COR_EMAIL_14']						= 'Request Type:         ';
		$_LANG['_ORDERS']['COR_EMAIL_15']						= 'Optional Bill Cycle:  ';
		$_LANG['_ORDERS']['COR_EMAIL_16']						= 'Optional Payment:     ';
		$_LANG['_ORDERS']['COR_EMAIL_17']						= 'Disk Space:           ';
		$_LANG['_ORDERS']['COR_EMAIL_18']						= 'Traffic / Bandwidth:  ';
		$_LANG['_ORDERS']['COR_EMAIL_19']						= 'Databases:            ';
		$_LANG['_ORDERS']['COR_EMAIL_20']						= 'Mailboxes (POP):      ';
		$_LANG['_ORDERS']['COR_EMAIL_21']						= 'Unique IP Address:    ';
		$_LANG['_ORDERS']['COR_EMAIL_22']						= 'Shopping Cart:        ';
		$_LANG['_ORDERS']['COR_EMAIL_23']						= 'Security Certificate: ';
		$_LANG['_ORDERS']['COR_EMAIL_24']						= 'Webmaster Authoring:  ';
		$_LANG['_ORDERS']['COR_EMAIL_25']						= 'Additional Comments (below):';
		$_LANG['_ORDERS']['COR_EMAIL_26']						= str_pad($_CCFG['COR_LABEL_OPTFLD_01'], 22, " ", STR_PAD_RIGHT);
		$_LANG['_ORDERS']['COR_EMAIL_27']						= str_pad($_CCFG['COR_LABEL_OPTFLD_02'], 22, " ", STR_PAD_RIGHT);
		$_LANG['_ORDERS']['COR_EMAIL_28']						= str_pad($_CCFG['COR_LABEL_OPTFLD_03'], 22, " ", STR_PAD_RIGHT);
		$_LANG['_ORDERS']['COR_EMAIL_29']						= str_pad($_CCFG['COR_LABEL_OPTFLD_04'], 22, " ", STR_PAD_RIGHT);
		$_LANG['_ORDERS']['COR_EMAIL_30']						= str_pad($_CCFG['COR_LABEL_OPTFLD_05'], 22, " ", STR_PAD_RIGHT);
		$_LANG['_ORDERS']['COR_EMAIL_SUBJECT']					= '- Quotation Request';

# Page: Admin Data Entry error
		$_LANG['_ORDERS']['ORD_ERR_ERR_HDR1']					= 'Entry error- required fields may not have been completed.';
		$_LANG['_ORDERS']['ORD_ERR_ERR_HDR2']					= 'Please check the following:';

		$_LANG['_ORDERS']['ORD_ERR_ERR01']						= 'Order ID';
		$_LANG['_ORDERS']['ORD_ERR_ERR02']						= 'Order Date';
		$_LANG['_ORDERS']['ORD_ERR_ERR03']						= 'Status';
		$_LANG['_ORDERS']['ORD_ERR_ERR04']						= 'Client ID';
		$_LANG['_ORDERS']['ORD_ERR_ERR05']						= 'Company';
		$_LANG['_ORDERS']['ORD_ERR_ERR06']						= 'First Name';
		$_LANG['_ORDERS']['ORD_ERR_ERR07']						= 'Last Name';
		$_LANG['_ORDERS']['ORD_ERR_ERR08']						= 'Address 1';
		$_LANG['_ORDERS']['ORD_ERR_ERR09']						= 'Address 2';
		$_LANG['_ORDERS']['ORD_ERR_ERR10']						= 'City';
		$_LANG['_ORDERS']['ORD_ERR_ERR11']						= 'State / Province';
		$_LANG['_ORDERS']['ORD_ERR_ERR12']						= 'Country';
		$_LANG['_ORDERS']['ORD_ERR_ERR13']						= 'Zip / Postal Code';
		$_LANG['_ORDERS']['ORD_ERR_ERR14']						= 'Phone';
		$_LANG['_ORDERS']['ORD_ERR_ERR15']						= 'Email Address';
		$_LANG['_ORDERS']['ORD_ERR_ERR16']						= 'Domain';
		$_LANG['_ORDERS']['ORD_ERR_ERR17']						= 'Domain Action';
		$_LANG['_ORDERS']['ORD_ERR_ERR18']						= 'User Name';
		$_LANG['_ORDERS']['ORD_ERR_ERR19']						= 'Password is missing';
		$_LANG['_ORDERS']['ORD_ERR_ERR20']						= 'Vendor';
		$_LANG['_ORDERS']['ORD_ERR_ERR21']						= 'Product';
		$_LANG['_ORDERS']['ORD_ERR_ERR22']						= 'Unit Cost';
		$_LANG['_ORDERS']['ORD_ERR_ERR23']						= 'Accepted TOS';
		$_LANG['_ORDERS']['ORD_ERR_ERR24']						= 'Accepted AUP';
		$_LANG['_ORDERS']['ORD_ERR_ERR25']						= 'Referred By';

		$_LANG['_ORDERS']['ORD_ERR_ERR26']						= 'Confirm password is missing';
		$_LANG['_ORDERS']['ORD_ERR_ERR27']						= 'password is too short. Min %NUM% characters';	// %NUM% will be replaced at run-time with $_CCFG['CLIENT_MIN_LEN_PWORD']
		$_LANG['_ORDERS']['ORD_ERR_ERR28']						= 'password is too long. Max %NUM% characters';	// %NUM% will be replaced at run-time with $_CCFG['CLIENT_MAX_LEN_PWORD']

		$_LANG['_ORDERS']['ORD_ERR_ERR30']						= '- Email appears to be invalid.';
		$_LANG['_ORDERS']['ORD_ERR_ERR31']						= '- Domain name invalid ~ example format';
		$_LANG['_ORDERS']['ORD_ERR_ERR32']						= '- Domain already exists, please try another.';

		$_LANG['_ORDERS']['ORD_ERR_ERR34']						= '- Password and Password Confirm must match.';
		$_LANG['_ORDERS']['ORD_ERR_ERR36']						= '- Email already exists, please try another.';
		$_LANG['_ORDERS']['ORD_ERR_ERR38']						= 'Required Field';
		$_LANG['_ORDERS']['ORD_ERR_ERR39']						= 'The UserName must contain letters and numbers only';

# Language Variables: Admin Edit form add order note at bottom
		$_LANG['_ORDERS']['ORD_ADD_NOTE_H1']					= 'Important Note:';
		$_LANG['_ORDERS']['ORD_ADD_NOTE_L1']					= 'This form requires the client exists within the system.';

# For whois integration
	$_LANG['_ORDERS']['WHOIS_DOMAIN_NEW']						= 'Ordering Domain';
	$_LANG['_ORDERS']['WHOIS_DOMAIN_CHECK']						= 'Click Here To Check Domain Name Availability';
	$_LANG['_ORDERS']['WHOIS_DOMAIN_CHECK_AVAILABLE']				= 'Check If Available';
	$_LANG['_ORDERS']['ADD_SETUP_FEE']							= 'plus setup fee';

?>