<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Config
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_base.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Base Common Set
	$_LANG['_BASE']['All_Visitors']						= 'All Visitors';
	$_LANG['_BASE']['All_Active_Clients']					= 'All Active Clients';
	$_LANG['_BASE']['All_Groups']							= 'All Groups';
	$_LANG['_BASE']['Flood_Contact_Title']					= 'Flood Control Message';
	$_LANG['_BASE']['Flood_Contact_Message']				= 'Sorry the site admin does not permit more than (1) contact message in a '.$_CCFG['FC_IN_SECONDS_CONTACTS'].' second time period.<br><br>Please wait and try again.';
	$_LANG['_BASE']['Group_Defined']						= 'Group Defined';
	$_LANG['_BASE']['Permission_Msg']						= 'Sorry, you do not have sufficient permissions to continue.';
	$_LANG['_BASE']['Permission_Title']					= 'Permission Denied';
	$_LANG['_BASE']['Please_Select']						= 'Please Select';
	$_LANG['_BASE']['Welcome']							= 'Welcome';
	$_LANG['_BASE']['Welcome_Back']						= 'Welcome Back';

# Language Variables: Some Common Buttons Text.
	$_LANG['_BASE']['B_Log_In']							= 'Log In';
	$_LANG['_BASE']['B_Log_Out']							= 'Log Out';
	$_LANG['_BASE']['B_Reset']							= 'Reset';

# Language Variables: For helpdesk
	$_LANG['_HDESK']['Select_Status']						= 'Select Status';
	$_LANG['_HDESK']['Select_Category']					= 'Select Category';
	$_LANG['_HDESK']['Select_Priority']					= 'Select Priority';
	$_LANG['_HDESK']['Status_Closed']						= 'Closed';
	$_LANG['_HDESK']['Status_Open']						= 'Open';

# Laguage Variables: core.php file
	# do_login() function:
		$_LANG['_BASE']['Administrative_Login_Required']		= 'Administrative Login Required';
		$_LANG['_BASE']['Client_Login_Required']			= 'Client Login Required';
		$_LANG['_BASE']['Login_Form']						= 'Login Status';
		$_LANG['_BASE']['Failed_Msg_User_Name']				= 'Login Failed due to incorrect or inactive username.';
		$_LANG['_BASE']['Failed_Msg_Password']				= 'Login Failed due to incorrect password.';

		$_LANG['_BASE']['l_User_Name']					= 'User Name:';
		$_LANG['_BASE']['l_Password']						= 'Password:';

	#	Context of next four strings:
	#	'(Forgot your password? Click <a href="mod.php?mod=mail&mode=reset&w='.$aw.'">here</a> for reset.)'.$_nl;
		$_LANG['_BASE']['Click']							= 'Click';
		$_LANG['_BASE']['for reset']						= 'for reset.';
		$_LANG['_BASE']['here']							= 'here';
		$_LANG['_BASE']['Forgot_your_password']				= 'Forgot your password?';

	# Other misc.
		$_LANG['_BASE']['All']							= 'All';
		$_LANG['_BASE']['All_Contacts']					= 'All Contacts';
		$_LANG['_BASE']['Email_Additional']				= 'Additional Email';
		$_LANG['_BASE']['Except']						= 'Except';
		$_LANG['_BASE']['Only']							= 'Only';
		$_LANG['_BASE']['Select_Category']					= 'Select Category';
		$_LANG['_BASE']['Select_Group']					= 'Select Group';
		$_LANG['_BASE']['Select_SubGroup']					= 'Select Sub-Group';
		$_LANG['_BASE']['Select_Topic']					= 'Select Topic';
		$_LANG['_BASE']['Transactions']					= 'Invoice Transactions';
		$_LANG['_BASE']['Bill_Transactions']				= 'Bill Transactions';

	# Parameter type listing: function do_select_list_parm_type()
		$_LANG['_BASE']['PTL_Boolean']					= 'Boolean';
		$_LANG['_BASE']['PTL_Date']						= 'Date';
		$_LANG['_BASE']['PTL_Integer']					= 'Integer';
		$_LANG['_BASE']['PTL_Real']						= 'Real';
		$_LANG['_BASE']['PTL_String']						= 'String';
		$_LANG['_BASE']['PTL_Timestamp']					= 'Timestamp';

# Laguage Variables: login.php file
	# Misc.
		$_LANG['_BASE']['Administrative_Login_Successful']	= 'Administrative Login Successful.';
		$_LANG['_BASE']['Client_Login_Successful']			= 'Client Login Successful.';
		$_LANG['_BASE']['Logout_Status']					= 'Logout Status';
		$_LANG['_BASE']['Logout_Successful']				= 'Logout Successful.';
		$_LANG['_BASE']['Logout_When_Done']				= 'Please click the [Logout] button when you are finished';

# Laguage Variables: common.php file
	# Date Select Lists:
		$_LANG['_BASE']['DS_Jan']						= 'January';
		$_LANG['_BASE']['DS_Feb']						= 'February';
		$_LANG['_BASE']['DS_Mar']						= 'March';
		$_LANG['_BASE']['DS_Apr']						= 'April';
		$_LANG['_BASE']['DS_May']						= 'May';
		$_LANG['_BASE']['DS_Jun']						= 'June';
		$_LANG['_BASE']['DS_Jul']						= 'July';
		$_LANG['_BASE']['DS_Aug']						= 'August';
		$_LANG['_BASE']['DS_Sep']						= 'September';
		$_LANG['_BASE']['DS_Oct']						= 'October';
		$_LANG['_BASE']['DS_Nov']						= 'November';
		$_LANG['_BASE']['DS_Dec']						= 'December';
		$_LANG['_BASE']['DS_Format_Date']					= 'year-month-day';
		$_LANG['_BASE']['DS_Format_Time']					= '24-hour:minute:second';

	# Drop Down List and Value-To-String Values
		$_LANG['_BASE']['Yes']							= 'Yes';
		$_LANG['_BASE']['No']							= 'No';

		$_LANG['_BASE']['On']							= 'On';
		$_LANG['_BASE']['Off']							= 'Off';

		$_LANG['_BASE']['Search']						= 'Search...';
		$_LANG['_BASE']['Clients']						= 'Clients';
		$_LANG['_BASE']['Domains']						= 'Domains';
		$_LANG['_BASE']['HelpDesk']						= 'HelpDesk';
		$_LANG['_BASE']['Invoices']						= 'Invoices';
		$_LANG['_BASE']['Orders']						= 'Orders';
		$_LANG['_BASE']['Bills']							= 'Bills';

# Laguage Variables: Admin Perms and User Groups
	# Admin Perms
		$_LANG['_BASE']['Permissions_16']					= 'Super Admin';		# Super Admin
		$_LANG['_BASE']['Permissions_15']					= 'Configuration';		# Configuration
		$_LANG['_BASE']['Permissions_14']					= 'Site Content';		# Site Content
		$_LANG['_BASE']['Permissions_13']					= 'MySQL Backup';		# Backup Database
		$_LANG['_BASE']['Permissions_12']					= 'ToDo';				# ToDo
		$_LANG['_BASE']['Permissions_11']					= 'Downloads';
		$_LANG['_BASE']['Permissions_10']					= 'ALL Read Only';		# ALL Read Only
		$_LANG['_BASE']['Permissions_09']					= 'Support';			# Support
		$_LANG['_BASE']['Permissions_08']					= 'Accounting';			# Accounting
		$_LANG['_BASE']['Permissions_07']					= 'Client Management';	# Client Management
		$_LANG['_BASE']['Permissions_06']					= 'Domains Management';	# Domains Management
		$_LANG['_BASE']['Permissions_05']					= 'eMail Management';	# eMail Management
		$_LANG['_BASE']['Permissions_04']					= 'Suppliers';			# Suppliers Management
		$_LANG['_BASE']['Permissions_03']					= '';		# Future
		$_LANG['_BASE']['Permissions_02']					= '';		# Future
		$_LANG['_BASE']['Permissions_01']					= '';		# Future
		$_LANG['_BASE']['l_Permissions']					= 'Permissions:';

	# User Groups:
		$_LANG['_BASE']['User_Groups_08']					= 'Group 8';		# Future
		$_LANG['_BASE']['User_Groups_07']					= 'Group 7';		# Future
		$_LANG['_BASE']['User_Groups_06']					= 'Group 6';		# Future
		$_LANG['_BASE']['User_Groups_05']					= 'Group 5';		# Future
		$_LANG['_BASE']['User_Groups_04']					= 'Group 4';		# Future
		$_LANG['_BASE']['User_Groups_03']					= 'Group 3';		# Future
		$_LANG['_BASE']['User_Groups_02']					= 'Group 2';		# Future
		$_LANG['_BASE']['User_Groups_01']					= 'Group 1';		# Future
		$_LANG['_BASE']['l_User_Groups']					= 'Groups:';

# For backing up MySQL database
        $_LANG['_BASE']['l_backup_download']					= 'Download MySQL dump to my computer';
        $_LANG['_BASE']['l_backup_save']                   		= 'Save MySQL dump on web-server';
        $_LANG['_BASE']['l_backup_email']					= 'Email MySQL dump to me';

#Text for auto-update
	$_LANG['_ADMIN']['UPDATE_TITLE']						= 'phpCOIN Updates';
	$_LANG['_ADMIN']['UPDATE_VERSION']						= 'Your phpCOIN installation is';
	$_LANG['_ADMIN']['UPDATE_FIX']						= 'with fix-file';
	$_LANG['_ADMIN']['UPDATE_NONE']						= 'Your installation is up-to-date';
	$_LANG['_ADMIN']['UPDATE_UNAVAILABLE']					= 'Update site presently unavailable';
	$_LANG['_ADMIN']['UPDATE_MANY']						= 'There is more than one fix file available, but you <i>only</i> need to download the <i>newest</i> fix-file for';
	$_LANG['_ADMIN']['UPDATE_NEW']						= 'You may wish to download the new release <i>instead</i> of the newest fix-file for';


# For page footer
	$_LANG['_BASE']['LABEL_PHONE']						= 'Phone:';
	$_LANG['_BASE']['LABEL_FAX']							= 'Fax:';
	$_LANG['_BASE']['LABEL_TOLL_FREE']						= 'Toll Free:';


# META TAGS
		$_LANG['META_DESCRIPTION']['Home_Page']				= 'Welcome to phpCOIN';
		$_LANG['META_KEYWORDS']['Home_Page']				= 'phpcoin';

		$_LANG['META_DESCRIPTION']['Site_Info']				= 'About Us';
		$_LANG['META_KEYWORDS']['Site_Info']				= 'phpcoin';

		$_LANG['META_DESCRIPTION']['Articles']				= 'View news and other helpful articles';
		$_LANG['META_KEYWORDS']['Articles']				= 'news,articles,phpcoin';

		$_LANG['META_DESCRIPTION']['Mail']					= 'Contact Us';
		$_LANG['META_KEYWORDS']['Mail']					= 'mail,phpcoin';

		$_LANG['META_DESCRIPTION']['Search_Site']			= 'Search our site';
		$_LANG['META_KEYWORDS']['Search_Site']				= 'search,phpcoin';

		$_LANG['META_DESCRIPTION']['Orders']				= 'Order Form for products and services';
		$_LANG['META_KEYWORDS']['Orders']					= 'order,phpcoin';

		$_LANG['META_DESCRIPTION']['FAQ']					= 'View answers to Frequently Asked Questions';
		$_LANG['META_KEYWORDS']['FAQ']					= 'FAQ,help,support,phpcoin';

		$_LANG['META_DESCRIPTION']['HelpDesk_Support_Tickets']	= 'Enter and view support requests and resolutions.';
		$_LANG['META_KEYWORDS']['HelpDesk_Support_Tickets']	= 'help,support,trouble,ticket,phpcoin';

		$_LANG['META_DESCRIPTION']['Site_Login']			= 'Login to our site';
		$_LANG['META_KEYWORDS']['Site_Login']				= 'login,phpcoin';

		$_LANG['META_DESCRIPTION']['WHOIS']				= 'Domain Name Lookup';
		$_LANG['META_KEYWORDS']['WHOIS']					= 'whois,phpcoin';

		$_LANG['META_DESCRIPTION']['Comand_Center']			= 'Summary';
		$_LANG['META_KEYWORDS']['Comand_Center']			= 'cc,command center,summary,phpcoin';


		$_LANG['_BASE']['AUTOPASSWORD_BUTTON_TEXT']			= 'Auto-Password';
		$_LANG['_BASE']['AUTOPASSWORD_BUTTON_REMEMBER']		= 'Write This Password Down';
?>