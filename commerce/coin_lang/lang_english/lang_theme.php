<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Theme
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_theme.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}


/**************************************************************/
# Language Variables: Misc /coin_themes/xxxxxx/core.php
	$_LANG['_THEME']['Printed']						= 'Printed:';

# ALT Text for: Language Variables: Navigation button Alt Text (/coin_themes/xxxxxx/config.php)
	$_LANG['_THEME']['ALT_IMG_SORT_ASC_S']				= 'Sort Ascending';
	$_LANG['_THEME']['ALT_IMG_SORT_DSC_S']				= 'Sort Descending';

# ALT Text for: Buttons / Img File URLS- Top Menu buttons
	$_LANG['_THEME']['ALT_IMG_MT_ABOUT_US_B']			= 'About Us';
	$_LANG['_THEME']['ALT_IMG_MT_ARTICLES_B']			= 'Articles';
	$_LANG['_THEME']['ALT_IMG_MT_CONTACT_B']			= 'Contact';
	$_LANG['_THEME']['ALT_IMG_MT_FAQ_B']				= 'FAQ';
	$_LANG['_THEME']['ALT_IMG_MT_HELPDESK_B']			= 'HelpDesk';
	$_LANG['_THEME']['ALT_IMG_MT_HOME_B']				= 'Home';
	$_LANG['_THEME']['ALT_IMG_MT_HOSTING_B']			= 'Hosting';
	$_LANG['_THEME']['ALT_IMG_MT_LOGIN_B']				= 'Login';
	$_LANG['_THEME']['ALT_IMG_MT_LOGOUT_B']				= 'Logout';
	$_LANG['_THEME']['ALT_IMG_MT_ORDER_B']				= 'Place Order';
	$_LANG['_THEME']['ALT_IMG_MT_PLACE_ORDER_B']			= 'Place Order';
	$_LANG['_THEME']['ALT_IMG_MT_PLANS_B']				= 'Plans';
	$_LANG['_THEME']['ALT_IMG_MT_SEARCH_B']				= 'Search';
	$_LANG['_THEME']['ALT_IMG_MT_SERVICES_B']			= 'Services';
	$_LANG['_THEME']['ALT_IMG_MT_SUMMARY_B']			= 'Summary';

# ALT Text for: Buttons / Img File URLS- User Menu buttons
	$_LANG['_THEME']['ALT_IMG_MU_ADMIN_B']				= 'Admin Menu';
	$_LANG['_THEME']['ALT_IMG_MU_CLIENTS_B']			= 'Clients Listing';
	$_LANG['_THEME']['ALT_IMG_MU_DOMAINS_B']			= 'Cleint Domains';
	$_LANG['_THEME']['ALT_IMG_MU_EMAIL_CLIENT_B']		= 'eMail Clients From';
	$_LANG['_THEME']['ALT_IMG_MU_HELPDESK_B']			= 'Support Helpdesk';
	$_LANG['_THEME']['ALT_IMG_MU_INVOICES_B']			= 'Client Invoices';
	$_LANG['_THEME']['ALT_IMG_MU_MY_ACCOUNT_B']			= 'My Account Summary';
	$_LANG['_THEME']['ALT_IMG_MU_MY_PROFILE_B']			= 'My Profile Summary';
	$_LANG['_THEME']['ALT_IMG_MU_ORDERS_B']				= 'Client Orders';
	$_LANG['_THEME']['ALT_IMG_MU_LICENSES_B']			= 'Software Licenses';
	$_LANG['_THEME']['ALT_IMG_MU_SUMMARY_B']			= 'Summary Command Center';

# ALT Text for: Buttons / Img File URLS (big ones)
	$_LANG['_THEME']['ALT_IMG_BLANK_B']				= 'Blank';
	$_LANG['_THEME']['ALT_IMG_DEMO_B']					= 'Demo';
	$_LANG['_THEME']['ALT_IMG_FORUMS_B']				= 'Forums';
	$_LANG['_THEME']['ALT_IMG_GALLERY_B']				= 'Gallery';
	$_LANG['_THEME']['ALT_IMG_HD_ADD_MSG_B']			= 'HelpDesk Add Message';
	$_LANG['_THEME']['ALT_IMG_RATE_PHPCOIN_B']			= 'Rate phpCOIN at Hotscripts.com';
	$_LANG['_THEME']['ALT_IMG_SUPPORT_PHPCOIN_B']		= 'Support phpCOIN by Donation';

# ALT Text for: Buttons / Img File URLS (medium ones)
	$_LANG['_THEME']['ALT_IMG_BLANK_M']				= 'Blank';
	$_LANG['_THEME']['ALT_IMG_ADD_NEW_M']				= 'Add New';
	$_LANG['_THEME']['ALT_IMG_ADD_BLOCK_M']				= 'Add Block';
	$_LANG['_THEME']['ALT_IMG_ADD_BLOCK_ITEM_M']			= 'Add Block Item';
	$_LANG['_THEME']['ALT_IMG_ADMIN_M']				= 'Admin';
	$_LANG['_THEME']['ALT_IMG_BACKUP_M']				= 'Backup MySQL Database';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_TOP_M']			= 'Back To Top';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_CLIENT_M']			= 'Back To Client';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_INVC_M']			= 'Back To Invoice';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_ORDER_M']			= 'Back To Order';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_TT_M']			= 'Back To Trouble Ticket';
	$_LANG['_THEME']['ALT_IMG_BLANK_S']				= '';	// Leave blank
	$_LANG['_THEME']['ALT_IMG_CLIENTS_M']				= 'Clients';
	$_LANG['_THEME']['ALT_IMG_COPY_M']					= 'Copy';
	$_LANG['_THEME']['ALT_IMG_DELETE_M']				= 'Delete';
	$_LANG['_THEME']['ALT_IMG_EDIT_M']					= 'Edit';
	$_LANG['_THEME']['ALT_IMG_EDIT_BLOCK_M']			= 'Edit Block';
	$_LANG['_THEME']['ALT_IMG_EDIT_BLOCK_ITEM_M']		= 'Edit Block Item';
	$_LANG['_THEME']['ALT_IMG_EMAIL_M']				= 'eMail';
	$_LANG['_THEME']['ALT_IMG_EMAIL_CLIENTS_M']			= 'eMail Clients';
	$_LANG['_THEME']['ALT_IMG_FAQ_M']					= 'FAQ';
	$_LANG['_THEME']['ALT_IMG_FAQ_ADD_FAQ_M']			= 'FAQ Add';
	$_LANG['_THEME']['ALT_IMG_FAQ_ADD_QA_M']			= 'FAQ QA Add';
	$_LANG['_THEME']['ALT_IMG_FAQ_EDIT_FAQ_M']			= 'FAQ Edit';
	$_LANG['_THEME']['ALT_IMG_FAQ_EDIT_QA_M']			= 'FAQ QA Edit';
	$_LANG['_THEME']['ALT_IMG_HOME_M']					= 'Home';
	$_LANG['_THEME']['ALT_IMG_IITEMS_EDITOR_M']			= 'Invoice Items Editor';
	$_LANG['_THEME']['ALT_IMG_INVC_TRANS_M']			= 'Invoice Transactions';
	$_LANG['_THEME']['ALT_IMG_LISTING_M']				= 'Listing';
	$_LANG['_THEME']['ALT_IMG_LOGIN_M']				= 'Login';
	$_LANG['_THEME']['ALT_IMG_LOGOUT_M']				= 'Logout';
	$_LANG['_THEME']['ALT_IMG_MAIN_M']					= 'Main';
	$_LANG['_THEME']['ALT_IMG_NEXT_M']					= 'Next';
	$_LANG['_THEME']['ALT_IMG_PAYMENT_M']				= 'Payment';
	$_LANG['_THEME']['ALT_IMG_PREV_M']					= 'Previous';
	$_LANG['_THEME']['ALT_IMG_PRINT_M']				= 'Print';
	$_LANG['_THEME']['ALT_IMG_REFRESH_M']				= 'Refresh';
	$_LANG['_THEME']['ALT_IMG_RETURN_M']				= 'Return';
	$_LANG['_THEME']['ALT_IMG_SEARCH_M']				= 'Search';
	$_LANG['_THEME']['ALT_IMG_SELECT_LIST_M']			= 'Select List';
	$_LANG['_THEME']['ALT_IMG_SET_PAID_M']				= 'Set To Paid';
	$_LANG['_THEME']['ALT_IMG_START_OVER_M']			= 'Start Over';
	$_LANG['_THEME']['ALT_IMG_SUMMARY_M']				= 'Summary';
	$_LANG['_THEME']['ALT_IMG_TRY_AGAIN_M']				= 'Try Again';
	$_LANG['_THEME']['ALT_IMG_VIEW_M']					= 'View';
	$_LANG['_THEME']['ALT_IMG_VIEW_ALL_M']				= 'View All';

	$_LANG['_THEME']['ALT_IMG_AUP_M']					= 'Acceptable Use';
	$_LANG['_THEME']['ALT_IMG_BAN_CODE_M']				= 'Banned Code';
	$_LANG['_THEME']['ALT_IMG_PRIV_POL_M']				= 'Privacy Policy';
	$_LANG['_THEME']['ALT_IMG_TOS_M']					= 'Terms Of Service';

# ALT Text for: Buttons / Img File URLS (smallest ones)
	$_LANG['_THEME']['ALT_IMG_ADD_S']					= 'Add';
	$_LANG['_THEME']['ALT_IMG_CP_S']					= 'Control Panel';
	$_LANG['_THEME']['ALT_IMG_DEL_S']					= 'Delete';
	$_LANG['_THEME']['ALT_IMG_EDIT_S']					= 'Edit';
	$_LANG['_THEME']['ALT_IMG_EMAIL_S']				= 'eMail';
	$_LANG['_THEME']['ALT_IMG_HELP_S']					= 'Help';
	$_LANG['_THEME']['ALT_IMG_PRINT_S']				= 'Print';
	$_LANG['_THEME']['ALT_IMG_SAVE_S']					= 'Save';
	$_LANG['_THEME']['ALT_IMG_SEARCH_S']				= 'Search';
	$_LANG['_THEME']['ALT_IMG_TOP_S']					= 'Top';
	$_LANG['_THEME']['ALT_IMG_VIEW_S']					= 'View';

	$_LANG['_THEME']['ALT_IMG_CANCEL_S']				= 'Cancel';
	$_LANG['_THEME']['ALT_IMG_PM_S']					= 'Edit Parameters';

	$_LANG['_THEME']['ALT_IMG_BACK_TO_BILL_M']			= 'Back to Bill';
	$_LANG['_THEME']['ALT_IMG_BITEMS_EDITOR_M']			= 'Bill Items Editor';
	$_LANG['_THEME']['ALT_IMG_BILL_TRANS_M']			= 'Bill Transactions';
	$_LANG['_THEME']['ALT_IMG_BACK_TO_SUPPLIER_M']		= 'Back To Supplier';
?>