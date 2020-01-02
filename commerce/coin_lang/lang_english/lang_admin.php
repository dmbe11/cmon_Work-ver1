<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Admin
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */

# Code to handle file being loaded by URL
	IF (eregi('lang_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Control Panel Specific Variables (by l## or AD##)
 *		- 00	- cp_core.php / index.php
 *		- 01	- cp_admins.php
 *		- 03	- cp_banned.php
 *		- 02	- cp_categories.php
 *		- 04	- cp_components.php
 *		- 05	- cp_icons.php
 *		- 06	- cp_mail_contacts.php
 *		- 07	- cp_mail_templates.php
 *		- 08	- cp_menu.php
 *		- 09	- cp_parms.php
 *		- 10	- cp_prods.php
 *		- 11	- cp_server_info.php
 *		- 12	- cp_siteinfo.php
 *		- 13	- cp_topics.php
 *		- 14	- cp_vendors.php
 *		- 15	- cp_vprods.php
 *      - 16    - cp_whois.php
 *      - 17    - cp_reminders.php
 *		- 20	 - cp_downloads
 **************************************************************/


/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_ADMIN']['Add_Admins_Entry_Results']					= 'Add Admins Entry Results';
		$_LANG['_ADMIN']['Add_Banned_IP_Entry_Results']				= 'Add Banned IP Entry Results';
		$_LANG['_ADMIN']['Add_Categories_Entry_Results']				= 'Add Categories Entry Results';
		$_LANG['_ADMIN']['Add_ADMIN_Domains_Entry_Results']			= 'Add Clients Domains Entry Results';
		$_LANG['_ADMIN']['Add_Components_Entry_Results']				= 'Add Components Entry Results';
		$_LANG['_ADMIN']['Add_Icons_Entry_Results']					= 'Add Icons Entry Results';
		$_LANG['_ADMIN']['Add_Mail_Contacts_Entry_Results']			= 'Add Mail Contacts Entry Results';
		$_LANG['_ADMIN']['Add_Mail_Templates_Entry_Results']			= 'Add Mail Templates Entry Results';
		$_LANG['_ADMIN']['Add_Menu_Block_Items_Entry_Results']			= 'Add Menu Block Items Entry Results';
		$_LANG['_ADMIN']['Add_Menu_Blocks_Entry_Results']				= 'Add Menu Blocks Entry Results';
		$_LANG['_ADMIN']['Add_Parameters_Entry_Results']				= 'Add Parameters Entry Results';
		$_LANG['_ADMIN']['Add_Products_Entry_Results']				= 'Add Products Entry Results';
		$_LANG['_ADMIN']['Add_Reminders_Entry_Results']				= 'Add Reminders Entry Results';
		$_LANG['_ADMIN']['Add_Server_Info_Entry_Results']				= 'Add Server Info Entry Results';
		$_LANG['_ADMIN']['Add_SiteInfo_Entry_Results']				= 'Add SiteInfo Entry Results';
		$_LANG['_ADMIN']['Add_Topics_Entry_Results']					= 'Add Topics Entry Results';
		$_LANG['_ADMIN']['Add_Vendors_Entry_Results']				= 'Add Vendors Entry Results';
		$_LANG['_ADMIN']['Add_Vendors_Products_Entry_Results']			= 'Add Vendors Products Entry Results';
		$_LANG['_ADMIN']['Add_WHOIS_Entry_Results']					= 'Add WHOIS Entry Results';
		$_LANG['_ADMIN']['Admins_Entry']							= 'Admins Entry';
		$_LANG['_ADMIN']['Admins_Editor']							= 'Admins Editor';
		$_LANG['_ADMIN']['all_fields_required']						= 'all fields required';
		$_LANG['_ADMIN']['some_fields_required']					= 'all fields <i>above</i> the line are <i>required</i>';
		$_LANG['_ADMIN']['An_error_occurred']						= 'An error occurred.';
		$_LANG['_ADMIN']['auto-assigned']							= 'auto-assigned';
		$_LANG['_ADMIN']['Banned_IP_Entry']						= 'Banned IP Entry';
		$_LANG['_ADMIN']['Banned_IP_Editor']						= 'Banned IP Editor';
		$_LANG['_ADMIN']['Categories_Entry']						= 'Categories Entry';
		$_LANG['_ADMIN']['Categories_Editor']						= 'Categories Editor';
		$_LANG['_ADMIN']['Client_Can_Select']						= 'Client Can Select';
		$_LANG['_ADMIN']['Clients_Domains_Entry']					= 'Clients Domains Entry';
		$_LANG['_ADMIN']['Clients_Domains_Editor']					= 'Clients Domains Editor';
		$_LANG['_ADMIN']['Components_Entry']						= 'Components Entry';
		$_LANG['_ADMIN']['Components_Editor']						= 'Components Editor';
		$_LANG['_ADMIN']['Column_Left']							= 'Left Column';
		$_LANG['_ADMIN']['Column_None']							= 'None';
		$_LANG['_ADMIN']['Column_Right']							= 'Right Column';
		$_LANG['_ADMIN']['Column_Three']							= 'Three Column';
		$_LANG['_ADMIN']['Column_Two']							= 'Two Column';
		$_LANG['_ADMIN']['Delete_Admins_Entry_Confirmation']			= 'Delete Admins Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Admins_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Admins_Entry_Results']				= 'Delete Admins Entry Results';
		$_LANG['_ADMIN']['Delete_Banned_IP_Entry_Confirmation']		= 'Delete Banned IP Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Banned_IP_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Banned_IP_Entry_Results']			= 'Delete Banned IP Entry Results';
		$_LANG['_ADMIN']['Delete_Categories_Entry_Confirmation']		= 'Delete Categories Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Categories_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Categories_Entry_Results']			= 'Delete Categories Entry Results';

		$_LANG['_ADMIN']['Delete_ADMIN_Domains_Entry_Confirmation']	= 'Delete Clients Domains Entry Confirmation';
		$_LANG['_ADMIN']['Delete_ADMIN_Domains_Entry_Message']		= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_ADMIN_Domains_Entry_Results']		= 'Delete Clients Domains Entry Results';

		$_LANG['_ADMIN']['Delete_Components_Entry_Confirmation']		= 'Delete Components Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Components_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Components_Entry_Results']			= 'Delete Components Entry Results';
		$_LANG['_ADMIN']['Delete_Entry_InUse_Error_Message']			= 'Sorry, because it is in use, you cannot delete Entry ID';
		$_LANG['_ADMIN']['Delete_Icons_Entry_Confirmation']			= 'Delete Icons Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Icons_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Icons_Entry_Results']				= 'Delete Icons Entry Results';
		$_LANG['_ADMIN']['Delete_Mail_Contacts_Entry_Confirmation']		= 'Delete Mail Contacts Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Mail_Contacts_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Mail_Contacts_Entry_Results']			= 'Delete Mail Contacts Entry Results';
		$_LANG['_ADMIN']['Delete_Mail_Templates_Entry_Confirmation']	= 'Delete Mail Templates Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Mail_Templates_Entry_Message']		= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Mail_Templates_Entry_Results']		= 'Delete Mail Templates Entry Results';
		$_LANG['_ADMIN']['Delete_Menu_Block_Items_Confirmation']		= 'Delete Menu Block Items Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Menu_Block_Items_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Menu_Block_Items_Entry_Results']		= 'Delete Menu Block Items Entry Results';
		$_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Confirmation']		= 'Delete Menu Blocks Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Menu_Blocks_Entry_Results']			= 'Delete Menu Blocks Entry Results';
		$_LANG['_ADMIN']['Delete_Parameters_Entry_Confirmation']		= 'Delete Parameters Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Parameters_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Parameters_Entry_Results']			= 'Delete Parameters Entry Results';
		$_LANG['_ADMIN']['Delete_Products_Entry_Confirmation']			= 'Delete Products Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Products_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Products_Entry_Results']				= 'Delete Products Entry Results';
		$_LANG['_ADMIN']['Delete_Reminders_Entry_Confirmation']		= 'Delete REminders Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Reminders_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Reminders_Entry_Results']			= 'Delete Overdue Reminders Entry Results';
		$_LANG['_ADMIN']['Delete_Server_Info_Entry_Confirmation']		= 'Delete Server Info Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Server_Info_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Server_Info_Entry_Results']			= 'Delete Server Info Entry Results';
		$_LANG['_ADMIN']['Delete_SiteInfo_Entry_Confirmation']			= 'Delete SiteInfo Entry Confirmation';
		$_LANG['_ADMIN']['Delete_SiteInfo_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_SiteInfo_Entry_Results']				= 'Delete SiteInfo Entry Results';
		$_LANG['_ADMIN']['Delete_Topics_Entry_Confirmation']			= 'Delete Topics Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Topics_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Topics_Entry_Results']				= 'Delete Topics Entry Results';
		$_LANG['_ADMIN']['Delete_Vendors_Entry_Confirmation']			= 'Delete Vendors Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Vendors_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Vendors_Entry_Results']				= 'Delete Vendors Entry Results';
		$_LANG['_ADMIN']['Delete_Vendors_Products_Entry_Confirmation']	= 'Delete Vendors Products Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Vendors_Products_Entry_Message']		= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Vendors_Products_Entry_Results']		= 'Delete Vendors Products Entry Results';
		$_LANG['_ADMIN']['Delete_WHOIS_Entry_Confirmation']			= 'Delete WHOIS Lookup Entry Confirmation';
		$_LANG['_ADMIN']['Delete_WHOIS_Entry_Message']				= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_WHOIS_Entry_Results']				= 'Delete WHOIS Lookup Entry Results';
		$_LANG['_ADMIN']['denotes_optional_items']					= 'denotes optional items';
		$_LANG['_ADMIN']['Edit_Admins_Entry_Results']				= 'Edit Admins Entry Results';
		$_LANG['_ADMIN']['Edit_Banned_IP_Entry_Results']				= 'Edit Banned IP Entry Results';
		$_LANG['_ADMIN']['Edit_Categories_Entry_Results']				= 'Edit Categories Entry Results';
		$_LANG['_ADMIN']['Edit_ADMIN_Domains_Entry_Results']			= 'Edit Clients Domains Entry Results';
		$_LANG['_ADMIN']['Edit_Components_Entry_Results']				= 'Edit Components Entry Results';
		$_LANG['_ADMIN']['Edit_Icons_Entry_Results']					= 'Edit Icons Entry Results';
		$_LANG['_ADMIN']['Edit_Mail_Contacts_Entry_Results']			= 'Edit Mail Contacts Entry Results';
		$_LANG['_ADMIN']['Edit_Mail_Templates_Entry_Results']			= 'Edit Mail Templates Entry Results';
		$_LANG['_ADMIN']['Edit_Menu_Block_Items_Entry_Results']		= 'Edit Menu Block Items Entry Results';
		$_LANG['_ADMIN']['Edit_Menu_Blocks_Entry_Results']			= 'Edit Menu Blocks Entry Results';
		$_LANG['_ADMIN']['Edit_Parameters_Entry_Results']				= 'Edit Parameters Entry Results';
		$_LANG['_ADMIN']['Edit_Products_Entry_Results']				= 'Edit Products Entry Results';
		$_LANG['_ADMIN']['Edit_Reminders_Entry_Results']				= 'Edit Reminders Entry Results';
		$_LANG['_ADMIN']['Edit_Server_Info_Entry_Results']			= 'Edit Server Info Entry Results';
		$_LANG['_ADMIN']['Edit_SiteInfo_Entry_Results']				= 'Edit SiteInfo Entry Results';
		$_LANG['_ADMIN']['Edit_Topics_Entry_Results']				= 'Edit Topics Entry Results';
		$_LANG['_ADMIN']['Edit_Vendors_Entry_Results']				= 'Edit Vendors Entry Results';
		$_LANG['_ADMIN']['Edit_Vendors_Products_Entry_Results']		= 'Edit Vendors Products Entry Results';
		$_LANG['_ADMIN']['Edit_WHOIS_Entry_Results']					= 'Edit WHOIS Lookups Entry Results';
		$_LANG['_ADMIN']['Entry_Deleted']							= 'Entry Deleted.';
		$_LANG['_ADMIN']['for_all_ADMIN']						= 'for all clients';
		$_LANG['_ADMIN']['Hide_Undefined_Group']					= 'Hide Undefined Group';
		$_LANG['_ADMIN']['Icons_Entry']							= 'Icons Entry';
		$_LANG['_ADMIN']['Icons_Editor']							= 'Icons Editor';
		$_LANG['_ADMIN']['Inserted_ID']							= 'Inserted ID-';
		$_LANG['_ADMIN']['Mail_Contacts_Entry']						= 'Mail Contacts Entry';
		$_LANG['_ADMIN']['Mail_Contacts_Editor']					= 'Mail Contacts Editor';
		$_LANG['_ADMIN']['Mail_Templates_Entry']					= 'Mail Templates Entry';
		$_LANG['_ADMIN']['Mail_Templates_Editor']					= 'Mail Templates Editor';
		$_LANG['_ADMIN']['MBlock_Col_Abbrev_Left']					= 'left col.';
		$_LANG['_ADMIN']['MBlock_Col_Abbrev_Right']					= 'right col.';
		$_LANG['_ADMIN']['MBlock_Current']							= 'current';
		$_LANG['_ADMIN']['MBlock_Used']							= 'used';
		$_LANG['_ADMIN']['Menu_Block_Items_Entry']					= 'Menu Block Items Entry';
		$_LANG['_ADMIN']['Menu_Block_Items_Editor']					= 'Menu Block Items Editor';
		$_LANG['_ADMIN']['Menu_Blocks_Entry']						= 'Menu Blocks Entry';
		$_LANG['_ADMIN']['Menu_Blocks_Editor']						= 'Menu Blocks Editor';
		$_LANG['_ADMIN']['no_commas']								= 'no commas';
		$_LANG['_ADMIN']['of']									= 'of';
		$_LANG['_ADMIN']['output_below']							= 'output below';
		$_LANG['_ADMIN']['Parameters_Entry']						= 'Parameters Entry';
		$_LANG['_ADMIN']['Parameters_Editor']						= 'Parameters Editor';
		$_LANG['_ADMIN']['Password_Note']							= 'Input password and confirm only if you wish to change current.';
		$_LANG['_ADMIN']['Please_Select']							= 'Please Select';
		$_LANG['_ADMIN']['Products_Entry']							= 'Products Entry';
		$_LANG['_ADMIN']['Products_Editor']						= 'Products Editor';
		$_LANG['_ADMIN']['Reminders_Editor']						= 'Reminders Editor';
		$_LANG['_ADMIN']['Reminders_Entry']						= 'Reminders Entry';
		$_LANG['_ADMIN']['Server_Info_Entry']						= 'Server Info Entry';
		$_LANG['_ADMIN']['Server_Info_Editor']						= 'Server Info Editor';
		$_LANG['_ADMIN']['SiteInfo_Entry']							= 'SiteInfo Entry';
		$_LANG['_ADMIN']['SiteInfo_Editor']						= 'SiteInfo Editor';
		$_LANG['_ADMIN']['Topics_Entry']							= 'Topics Entry';
		$_LANG['_ADMIN']['Topics_Editor']							= 'Topics Editor';
		$_LANG['_ADMIN']['total_entries']							= 'total entries';
		$_LANG['_ADMIN']['Vendors_Entry']							= 'Vendors Entry';
		$_LANG['_ADMIN']['Vendors_Editor']							= 'Vendors Editor';
		$_LANG['_ADMIN']['Vendors_Products_Entry']					= 'Vendors Products Entry';
		$_LANG['_ADMIN']['Vendors_Products_Editor']					= 'Vendors Products Editor';
		$_LANG['_ADMIN']['WHOIS_Entry']							= 'WHOIS Entry';
		$_LANG['_ADMIN']['WHOIS_Editor']							= 'WHOIS Editor';
		$_LANG['_ADMIN']['HelpDesk_Import']						= 'Import Email To HelpDesk';

# Language Variables: Admin Control Panel Link Text
		$_LANG['_ADMIN']['CP_Administrator_Menu']				= 'Administrator Menu';
		$_LANG['_ADMIN']['CP_Admins']							= 'Admins';
		$_LANG['_ADMIN']['CP_Articles_Edit']					= 'Articles Edit';
		$_LANG['_ADMIN']['CP_Backup']							= 'Backup Database';
		$_LANG['_ADMIN']['CP_Banned_IP']						= 'Banned IP';
		$_LANG['_ADMIN']['CP_Categories']						= 'Categories';
		$_LANG['_ADMIN']['CP_Components']						= 'Components';
		$_LANG['_ADMIN']['CP_eMail_Contacts']					= 'eMail Contacts';
		$_LANG['_ADMIN']['CP_eMail_Templates']					= 'eMail Templates';
		$_LANG['_ADMIN']['CP_FAQ_Edit']						= 'FAQ Edit';
		$_LANG['_ADMIN']['CP_FAQ_QA_Edit']						= 'FAQ QA Edit';
		$_LANG['_ADMIN']['CP_Icons']							= 'Icons';
		$_LANG['_ADMIN']['CP_IPN_Log']						= 'IPN Log';
		$_LANG['_ADMIN']['CP_IPN_Test']						= 'IPN Test';
		$_LANG['_ADMIN']['CP_Mail']							= 'Mail Archive';
		$_LANG['_ADMIN']['CP_Menu_Blocks']						= 'Menu Blocks';
		$_LANG['_ADMIN']['CP_Pages_Edit']						= 'Pages Edit';
		$_LANG['_ADMIN']['CP_Parameters']						= 'Parameters';
		$_LANG['_ADMIN']['CP_Products']						= 'Products';
		$_LANG['_ADMIN']['CP_Reminders']						= 'Reminder Templates';
		$_LANG['_ADMIN']['CP_Server_Info']						= 'Server Info';
		$_LANG['_ADMIN']['CP_SiteInfo_Edit']					= 'SiteInfo Edit';
		$_LANG['_ADMIN']['CP_Topics']							= 'Topics';
		$_LANG['_ADMIN']['CP_Vendors']						= 'Vendors';
		$_LANG['_ADMIN']['CP_Vendors_Products']					= 'Paylinks';
		$_LANG['_ADMIN']['CP_WHOIS_Lookups']					= 'WHOIS Lookups';
		$_LANG['_ADMIN']['CP_Support_Import']					= 'Import HelpDesk';

# Language Variables: Some Buttons
		$_LANG['_ADMIN']['B_Add']							= 'Add';
		$_LANG['_ADMIN']['B_Add_Menu_Item']					= 'Add Menu Item';
		$_LANG['_ADMIN']['B_Continue']						= 'Continue';
		$_LANG['_ADMIN']['B_Delete_Entry']						= 'Delete Entry';
		$_LANG['_ADMIN']['B_Do_It']							= 'Do It';
		$_LANG['_ADMIN']['B_Edit']							= 'Edit';
		$_LANG['_ADMIN']['B_Load_Entry']						= 'Load Entry';
		$_LANG['_ADMIN']['B_Reset']							= 'Reset';
		$_LANG['_ADMIN']['B_Save']							= 'Save';
		$_LANG['_ADMIN']['B_View']							= 'View';

# Language Variables: Common Labels (note : on end)
		$_LANG['_ADMIN']['l01_Administrator_Select']				= 'Administrator Select:';
		$_LANG['_ADMIN']['l01_Admin_ID']						= 'Admin ID:';
		$_LANG['_ADMIN']['l01_Admin_User_Name']					= 'Admin User Name:';
		$_LANG['_ADMIN']['l01_Email_Address']					= 'Email Address:';
		$_LANG['_ADMIN']['l01_First_Name']						= 'First Name:';
		$_LANG['_ADMIN']['l01_Last_Name']						= 'Last Name:';
		$_LANG['_ADMIN']['l01_Password']						= 'Password:';
		$_LANG['_ADMIN']['l01_Password_Confirm']				= 'Password Confirm:';
		$_LANG['_ADMIN']['l01_Permissions']					= 'Permissions:';

		$_LANG['_ADMIN']['l02_Categories_Select']				= 'Categories Select:';
		$_LANG['_ADMIN']['l02_Category_ID']					= 'Category ID:';
		$_LANG['_ADMIN']['l02_Description']					= 'Description:';
		$_LANG['_ADMIN']['l02_Icon']							= 'Icon:';
		$_LANG['_ADMIN']['l02_Name']							= 'Name:';
		$_LANG['_ADMIN']['l02_Position']						= 'Position:';

		$_LANG['_ADMIN']['l03_Banned_IP']						= 'Banned IP:';
		$_LANG['_ADMIN']['l03_Banned_IP_Select']				= 'Banned IP Select:';

		$_LANG['_ADMIN']['l04_Components_Select']				= 'Components Select:';
		$_LANG['_ADMIN']['l04_Admin']							= 'Admin Only:';
		$_LANG['_ADMIN']['l04_Component_Id']					= 'Component Id:';
		$_LANG['_ADMIN']['l04_Description']					= 'Description:';
		$_LANG['_ADMIN']['l04_Mod']							= 'Mod:';
		$_LANG['_ADMIN']['l04_Name']							= 'Name:';
		$_LANG['_ADMIN']['l04_No_Columns']						= 'No. Columns:';
		$_LANG['_ADMIN']['l04_Page_Title']						= 'Page Title:';
		$_LANG['_ADMIN']['l04_Type']							= 'Type:';
		$_LANG['_ADMIN']['l04_Status']						= 'Status:';
		$_LANG['_ADMIN']['l04_User']							= 'User or Admin Only:';

		$_LANG['_ADMIN']['l05_Icons_Select']					= 'Icons Select:';
		$_LANG['_ADMIN']['l05_Description']					= 'Description:';
		$_LANG['_ADMIN']['l05_Filename']						= 'Filename:';
		$_LANG['_ADMIN']['l05_Icon_ID']						= 'Icon ID:';
		$_LANG['_ADMIN']['l05_Name']							= 'Name:';

		$_LANG['_ADMIN']['l06_Mail_Contacts_Select']				= 'Mail Contacts Select:';
		$_LANG['_ADMIN']['l06_Contact_ID']						= 'Contact ID:';
		$_LANG['_ADMIN']['l06_Email']							= 'Email:';
		$_LANG['_ADMIN']['l06_Name']							= 'Name:';
		$_LANG['_ADMIN']['l06_Status']						= 'Status:';

		$_LANG['_ADMIN']['l07_Mail_Templates_Select']			= 'Mail Templates Select:';
		$_LANG['_ADMIN']['l07_Name']							= 'Name:';
		$_LANG['_ADMIN']['l07_Template_ID']					= 'Template ID:';
		$_LANG['_ADMIN']['l07_Text']							= 'Text:';
		$_LANG['_ADMIN']['l07_Text_output_below']				= 'Text (output below):';

		$_LANG['_ADMIN']['l08_Menu_Blocks_Select']				= 'Menu Blocks Select:';
		$_LANG['_ADMIN']['l08_Admin_Block']					= 'Admin Only Block:';
		$_LANG['_ADMIN']['l08_Block_ID']						= 'Block ID:';
		$_LANG['_ADMIN']['l08_Column']						= 'Column:';
		$_LANG['_ADMIN']['l08_Position']						= 'Position:';
		$_LANG['_ADMIN']['l08_Status']						= 'Status:';
		$_LANG['_ADMIN']['l08_Title']							= 'Title:';
		$_LANG['_ADMIN']['l08_User_Block']						= 'User or Admin Only Block:';

		$_LANG['_ADMIN']['l08_Menu_Block_Items_Select']			= 'Menu Block Items Select:';
		$_LANG['_ADMIN']['l08_Admin_Item']						= 'Admin Only Item:';
		$_LANG['_ADMIN']['l08_Item_ID']						= 'Item ID:';
		$_LANG['_ADMIN']['l08_Target']						= 'Target:';
		$_LANG['_ADMIN']['l08_Text']							= 'Text:';
		$_LANG['_ADMIN']['l08_URL']							= 'URL:';
		$_LANG['_ADMIN']['l08_User_Item']						= 'User or Admin Only Item:';

		$_LANG['_ADMIN']['l08_Menu_Blocks_Edit_List']			= 'Menu Blocks Edit List:';
		$_LANG['_ADMIN']['l08_Left_Col_Quick_Select']			= 'Left Col. Quick Select:';
		$_LANG['_ADMIN']['l08_Right_Col_Quick_Select']			= 'Right Col. Quick Select:';
		$_LANG['_ADMIN']['l08_Target']						= 'Target:';
		$_LANG['_ADMIN']['l08_Type']							= 'Type:';

		$_LANG['_ADMIN']['l09_Parameters_Select']				= 'Parameters Select:';
		$_LANG['_ADMIN']['l09_Description']					= 'Description:';
		$_LANG['_ADMIN']['l09_Group']							= 'Group:';
		$_LANG['_ADMIN']['l09_Id']							= 'Id:';
		$_LANG['_ADMIN']['l09_Name']							= 'Name:';
		$_LANG['_ADMIN']['l09_Notes']							= 'Notes:';
		$_LANG['_ADMIN']['l09_Parameter_ID']					= 'Parameter Id:';
		$_LANG['_ADMIN']['l09_SubGroup']						= 'SubGroup:';
		$_LANG['_ADMIN']['l09_Type']							= 'Type:';
		$_LANG['_ADMIN']['l09_Value']							= 'Value:';

		$_LANG['_ADMIN']['l09_ORD_Field_16']					= 'Company';
		$_LANG['_ADMIN']['l09_ORD_Field_15']					= 'Address Street 1';
		$_LANG['_ADMIN']['l09_ORD_Field_14']					= 'Address Street 2';
		$_LANG['_ADMIN']['l09_ORD_Field_13']					= 'City';
		$_LANG['_ADMIN']['l09_ORD_Field_12']					= 'State / Province';
		$_LANG['_ADMIN']['l09_ORD_Field_11']					= 'Zip / Postal Code';
		$_LANG['_ADMIN']['l09_ORD_Field_10']					= 'Country';
		$_LANG['_ADMIN']['l09_ORD_Field_09']					= 'Phone #';
		$_LANG['_ADMIN']['l09_ORD_Field_08']					= 'Domain Action';
		$_LANG['_ADMIN']['l09_ORD_Field_07']					= 'Referred By';
		$_LANG['_ADMIN']['l09_ORD_Field_06']					= 'Comments';
		$_LANG['_ADMIN']['l09_ORD_Field_05']					= str_replace( ':', '', $_CCFG['ORD_LABEL_OPTFLD_05'] );
		$_LANG['_ADMIN']['l09_ORD_Field_04']					= str_replace( ':', '', $_CCFG['ORD_LABEL_OPTFLD_04'] );
		$_LANG['_ADMIN']['l09_ORD_Field_03']					= str_replace( ':', '', $_CCFG['ORD_LABEL_OPTFLD_03'] );
		$_LANG['_ADMIN']['l09_ORD_Field_02']					= str_replace( ':', '', $_CCFG['ORD_LABEL_OPTFLD_02'] );
		$_LANG['_ADMIN']['l09_ORD_Field_01']					= str_replace( ':', '', $_CCFG['ORD_LABEL_OPTFLD_01'] );

		$_LANG['_ADMIN']['l09_COR_Field_16']					= 'Optional Bill Cycle';
		$_LANG['_ADMIN']['l09_COR_Field_15']					= 'Optional Payments';
		$_LANG['_ADMIN']['l09_COR_Field_14']					= 'Hard Disk Space';
		$_LANG['_ADMIN']['l09_COR_Field_13']					= 'Monthly Traffic';
		$_LANG['_ADMIN']['l09_COR_Field_12']					= 'Databases';
		$_LANG['_ADMIN']['l09_COR_Field_11']					= 'MailBoxes';
		$_LANG['_ADMIN']['l09_COR_Field_10']					= 'Unique IP';
		$_LANG['_ADMIN']['l09_COR_Field_09']					= 'Shopping Cart';
		$_LANG['_ADMIN']['l09_COR_Field_08']					= 'Security Certificate';
		$_LANG['_ADMIN']['l09_COR_Field_07']					= 'WebSite Authoring';
		$_LANG['_ADMIN']['l09_COR_Field_06']					= 'Comments';
		$_LANG['_ADMIN']['l09_COR_Field_05']					= str_replace( ':', '', $_CCFG['COR_LABEL_OPTFLD_05'] );
		$_LANG['_ADMIN']['l09_COR_Field_04']					= str_replace( ':', '', $_CCFG['COR_LABEL_OPTFLD_04'] );
		$_LANG['_ADMIN']['l09_COR_Field_03']					= str_replace( ':', '', $_CCFG['COR_LABEL_OPTFLD_03'] );
		$_LANG['_ADMIN']['l09_COR_Field_02']					= str_replace( ':', '', $_CCFG['COR_LABEL_OPTFLD_02'] );
		$_LANG['_ADMIN']['l09_COR_Field_01']					= str_replace( ':', '', $_CCFG['COR_LABEL_OPTFLD_01'] );

		$_LANG['_ADMIN']['l10_Products_Select']					= 'Products Select:';
		$_LANG['_ADMIN']['l10_Apply_Tax_01']					= 'Apply Tax 01';
		$_LANG['_ADMIN']['l10_Apply_Tax_02']					= 'Apply Tax 02';
		$_LANG['_ADMIN']['l10_Calc_Tax_02_On_01']				= 'Calc Tax 02 On Tax 01';
		$_LANG['_ADMIN']['l10_Client_Scope']					= 'Client Scope:';
		$_LANG['_ADMIN']['l10_Databases']						= 'Databases:';
		$_LANG['_ADMIN']['l10_Description']					= 'Description:';
		$_LANG['_ADMIN']['l10_Disk_Space_Mb']					= 'Disk Space (Mb):';
		$_LANG['_ADMIN']['l10_Domains']						= 'Domains:';
		$_LANG['_ADMIN']['l10_Domain_Type']					= 'Domain Type:';
		$_LANG['_ADMIN']['l10_Groups']						= 'Groups:';
		$_LANG['_ADMIN']['l10_MailBoxes_POP']					= 'MailBoxes (POP):';
		$_LANG['_ADMIN']['l10_Name']							= 'Name:';
		$_LANG['_ADMIN']['l10_Prod_Id']						= 'Prod Id:';
		$_LANG['_ADMIN']['l10_Product_Id']						= 'Product Id:';
		$_LANG['_ADMIN']['l10_Prod_Name']						= 'Prod Name:';
		$_LANG['_ADMIN']['l10_Status']						= 'Status:';
		$_LANG['_ADMIN']['l10_SubDomains']						= 'SubDomains:';
		$_LANG['_ADMIN']['l10_Traffic_BW_Mb']					= 'Traffic / BW (Mb):';
		$_LANG['_ADMIN']['l10_Unit_Cost']						= 'Unit Cost:';

		$_LANG['_ADMIN']['l11_Server_Info_Select']				= 'Server Info Select:';
		$_LANG['_ADMIN']['l11_Control_Panel_Port']				= 'Control Panel Port:';
		$_LANG['_ADMIN']['l11_Control_Panel_URL']				= 'Control Panel URL:';
		$_LANG['_ADMIN']['l11_IP_Address']						= 'IP Address:';
		$_LANG['_ADMIN']['l11_Nameserver_01']					= 'Nameserver 01:';
		$_LANG['_ADMIN']['l11_Nameserver_01_IP']				= 'NS01 IP Address:';
		$_LANG['_ADMIN']['l11_Nameserver_02']					= 'Nameserver 02:';
		$_LANG['_ADMIN']['l11_Nameserver_02_IP']				= 'NS02 IP Address:';
		$_LANG['_ADMIN']['l11_Server_ID']						= 'Server ID:';
		$_LANG['_ADMIN']['l11_Server_Name']					= 'Server Name:';

		$_LANG['_ADMIN']['l12_SiteInfo_Select']					= 'SiteInfo Select:';
		$_LANG['_ADMIN']['l12_Code']							= 'Code:';
		$_LANG['_ADMIN']['l12_Footer_Menu']					= 'Footer Menu:';
		$_LANG['_ADMIN']['l12_SI_Block_It']					= 'SI Block-It:';
		$_LANG['_ADMIN']['l12_SI_Description']					= 'SI Description:';
		$_LANG['_ADMIN']['l12_SI_Group']						= 'SI Group:';
		$_LANG['_ADMIN']['l12_SI_Name']						= 'SI Name:';
		$_LANG['_ADMIN']['l12_SI_Seq_No']						= 'SI Seq No:';
		$_LANG['_ADMIN']['l12_SiteInfo_ID']					= 'SiteInfo ID:';
		$_LANG['_ADMIN']['l12_Status']						= 'Status:';
		$_LANG['_ADMIN']['l12_Title']							= 'Title:';

		$_LANG['_ADMIN']['l13_Topics_Select']					= 'Topics Select:';
		$_LANG['_ADMIN']['l13_Description']					= 'Description:';
		$_LANG['_ADMIN']['l13_Icon']							= 'Icon:';
		$_LANG['_ADMIN']['l13_Name']							= 'Name:';
		$_LANG['_ADMIN']['l13_Position']						= 'Position:';
		$_LANG['_ADMIN']['l13_Topic_ID']						= 'Topic ID:';

		$_LANG['_ADMIN']['l14_Vendors_Select']					= 'Vendors Select:';
		$_LANG['_ADMIN']['l14_Buy_Return_Param_Name']			= 'Buy Return Param Name:';
		$_LANG['_ADMIN']['l14_Buy_Return_Param_Value']			= 'Buy Return Param Value:';
		$_LANG['_ADMIN']['l14_Notes']							= 'Notes:';
		$_LANG['_ADMIN']['l14_Status']						= 'Status:';
		$_LANG['_ADMIN']['l14_URL_Website']					= 'URL / Website:';
		$_LANG['_ADMIN']['l14_Vendor_ID']						= 'Vendor ID:';
		$_LANG['_ADMIN']['l14_Vendor_Name']					= 'Vendor Name:';
		$_LANG['_ADMIN']['l14_Use_IPN']						= 'Use IPN:';

		$_LANG['_ADMIN']['l15_Vendors_Products_Select']			= 'Vendors Products Select:';
		$_LANG['_ADMIN']['l15_Description']					= 'Description:';
		$_LANG['_ADMIN']['l15_Order_Link']						= 'Order Link:';
		$_LANG['_ADMIN']['l15_Product']						= 'Product:';
		$_LANG['_ADMIN']['l15_Product_ID']						= 'Product ID:';
		$_LANG['_ADMIN']['l15_Vendor']						= 'Vendor:';
		$_LANG['_ADMIN']['l15_Vendor_ID']						= 'Vendor ID:';
		$_LANG['_ADMIN']['l15_Vendor_Product_Id']				= 'Vendor Product Id:';
		$_LANG['_ADMIN']['l15_VProd_Id']						= 'V-Prod Id:';

		$_LANG['_ADMIN']['l16_WHOIS_Select']					= 'WHOIS Server Select:';
		$_LANG['_ADMIN']['l16_Notes']							= 'Notes:';
		$_LANG['_ADMIN']['l16_Status']						= 'Use It:';
		$_LANG['_ADMIN']['l16_URL']							= 'WHOIS URL:';
		$_LANG['_ADMIN']['l16_WHOIS_ID']						= 'WHOIS ID:';
		$_LANG['_ADMIN']['l16_WHOIS_Name']						= 'TLD Display (with DOT):';
		$_LANG['_ADMIN']['l16_WHOIS_Domain']					= 'TLD Extension(s) (without DOT):';
		$_LANG['_ADMIN']['l16_NoMatch_Return_Param']            	= '"Available" Return String';
		$_LANG['_ADMIN']['l16_Override_Product']                	= 'Override Ordered product_id With:';

		$_LANG['_ADMIN']['l17_Reminders_Select']				= 'Reminder Template Select:';
		$_LANG['_ADMIN']['l17_Reminder_ID']					= 'Reminder ID:';
		$_LANG['_ADMIN']['l17_Status']						= 'Use It:';
		$_LANG['_ADMIN']['l17_Reminder_Name']					= 'Template Title:';
		$_LANG['_ADMIN']['l17_Reminder_Days']					= 'Overdue Days:';
		$_LANG['_ADMIN']['l17_Reminder_Subject']				= 'Email Subject:';
		$_LANG['_ADMIN']['l17_Reminder_Contents']				= 'Message Contents:';
		$_LANG['_ADMIN']['l17_Reminder_CC_Support']				= 'cc To Support:';


		$_LANG['_ADMIN']['l20_Download_Select']					= 'Download Select:';
		$_LANG['_ADMIN']['l20_Download_ID']					= 'Download ID:';
		$_LANG['_ADMIN']['l20_Download_Name']					= 'Name:';
		$_LANG['_ADMIN']['l20_Download_FileName']				= 'FileName:';
		$_LANG['_ADMIN']['l20_Download_FileSize']				= 'FileSize:';
		$_LANG['_ADMIN']['l20_Download_Contributor']				= 'Contributor:';
		$_LANG['_ADMIN']['l20_Download_Count']					= 'Num. Downloads:';
		$_LANG['_ADMIN']['l20_Download_Date']					= 'Release Date:';
		$_LANG['_ADMIN']['l20_Download_Description']				= 'Description:';
		$_LANG['_ADMIN']['l20_Download_Group']					= 'Group:';
		$_LANG['_ADMIN']['l20_Download_Status']					= 'Available:';
		$_LANG['_ADMIN']['Download_Entry']						= 'Downloads Entry';
		$_LANG['_ADMIN']['CP_Downloads']						= 'Downloads Edit';
		$_LANG['_ADMIN']['Add_Downloads_Entry_Results']			= 'Add Download Results';
		$_LANG['_ADMIN']['Edit_Downloads_Entry_Results']			= 'Edit Download Results';
		$_LANG['_ADMIN']['Delete_Downloads_Entry_Confirmation']	= 'Delete Downloads Confirmation';
		$_LANG['_ADMIN']['Delete_Downloads_Entry_Message']		= 'Are you sure you want to delete Download ID:';
		$_LANG['_ADMIN']['Delete_Downloads_Entry_Results']		= 'Delete Downloads Entry Results';
		$_LANG['_ADMIN']['Downloads_Editor']					= 'Downloads Editor';

		$_LANG['_ADMIN']['AD20_ERR_01']						= 'Download ID';
		$_LANG['_ADMIN']['AD20_ERR_02']						= 'Group';
		$_LANG['_ADMIN']['AD20_ERR_03']						= 'Name';
		$_LANG['_ADMIN']['AD20_ERR_04']						= 'Description';
		$_LANG['_ADMIN']['AD20_ERR_05']						= 'Num. Downloads';
		$_LANG['_ADMIN']['AD20_ERR_06']						= 'Release Date';
		$_LANG['_ADMIN']['AD20_ERR_07']						= 'Available';
		$_LANG['_ADMIN']['AD20_ERR_08']						= 'FileName';
		$_LANG['_ADMIN']['AD20_ERR_09']						= 'FileSize';
		$_LANG['_ADMIN']['AD20_ERR_10']						= 'Contributor';


	# Page: Admin Data Entry error
		$_LANG['_ADMIN']['AD_ERR00__HDR1']						= 'Entry error- required fields may not have been completed.';
		$_LANG['_ADMIN']['AD_ERR00__HDR2']						= 'Please check the following:';

		$_LANG['_ADMIN']['AD01_ERR_01']						= 'Admin ID';
		$_LANG['_ADMIN']['AD01_ERR_02']						= 'Admin User Name';
		$_LANG['_ADMIN']['AD01_ERR_03']						= 'Password';
		$_LANG['_ADMIN']['AD01_ERR_04']						= 'First Name';
		$_LANG['_ADMIN']['AD01_ERR_05']						= 'Last Name';
		$_LANG['_ADMIN']['AD01_ERR_06']						= 'Email';
		$_LANG['_ADMIN']['AD01_ERR_07']						= 'Permissions';
		$_LANG['_ADMIN']['AD01_ERR_10']						= 'Email appears to be invalid.';
		$_LANG['_ADMIN']['AD01_ERR_11']						= 'User Name already exists, must enter another.';
		$_LANG['_ADMIN']['AD01_ERR_12']						= 'Password and Password Confirm must match.';

		$_LANG['_ADMIN']['AD02_ERR_01']						= 'Category ID';
		$_LANG['_ADMIN']['AD02_ERR_02']						= 'Position';
		$_LANG['_ADMIN']['AD02_ERR_03']						= 'Name';
		$_LANG['_ADMIN']['AD02_ERR_04']						= 'Description';

		$_LANG['_ADMIN']['AD03_ERR_01']						= 'Domain ID';
		$_LANG['_ADMIN']['AD03_ERR_02']						= 'Client Id';
		$_LANG['_ADMIN']['AD03_ERR_03']						= 'Domain';
		$_LANG['_ADMIN']['AD03_ERR_04']						= 'Server Acc.';

		$_LANG['_ADMIN']['AD04_ERR_01']						= 'Component ID';
		$_LANG['_ADMIN']['AD04_ERR_02']						= 'Type';
		$_LANG['_ADMIN']['AD04_ERR_03']						= 'Name';
		$_LANG['_ADMIN']['AD04_ERR_04']						= 'Description';
		$_LANG['_ADMIN']['AD04_ERR_05']						= 'Page Title';

		$_LANG['_ADMIN']['AD05_ERR_01']						= 'Icon ID';
		$_LANG['_ADMIN']['AD05_ERR_02']						= 'Name';
		$_LANG['_ADMIN']['AD05_ERR_03']						= 'Description';
		$_LANG['_ADMIN']['AD05_ERR_04']						= 'Filename';

		$_LANG['_ADMIN']['AD06_ERR_01']						= 'Mail Contact ID';
		$_LANG['_ADMIN']['AD06_ERR_02']						= 'Name';
		$_LANG['_ADMIN']['AD06_ERR_03']						= 'eMail';

		$_LANG['_ADMIN']['AD07_ERR_01']						= 'Mail Template ID';
		$_LANG['_ADMIN']['AD07_ERR_02']						= 'Name';
		$_LANG['_ADMIN']['AD07_ERR_03']						= 'Text';

		$_LANG['_ADMIN']['AD08_ERR_01']						= 'Block ID';
		$_LANG['_ADMIN']['AD08_ERR_02']						= 'Block Title';
		$_LANG['_ADMIN']['AD08_ERR_03']						= 'Status';
		$_LANG['_ADMIN']['AD08_ERR_04']						= 'Admin Flag';
		$_LANG['_ADMIN']['AD08_ERR_11']						= 'Item ID';
		$_LANG['_ADMIN']['AD08_ERR_12']						= 'Item Text';
		$_LANG['_ADMIN']['AD08_ERR_13']						= 'Item URL';

		$_LANG['_ADMIN']['AD09_ERR_01']						= 'Parameter ID';
		$_LANG['_ADMIN']['AD09_ERR_02']						= 'Group';
		$_LANG['_ADMIN']['AD09_ERR_03']						= 'Sub-Group';
		$_LANG['_ADMIN']['AD09_ERR_04']						= 'Type';
		$_LANG['_ADMIN']['AD09_ERR_05']						= 'Name';
		$_LANG['_ADMIN']['AD09_ERR_06']						= 'Description';
		$_LANG['_ADMIN']['AD09_ERR_07']						= 'Value';
		$_LANG['_ADMIN']['AD09_ERR_08']						= 'Notes';

		$_LANG['_ADMIN']['AD10_ERR_01']						= 'Product ID';
		$_LANG['_ADMIN']['AD10_ERR_02']						= 'Name';
		$_LANG['_ADMIN']['AD10_ERR_03']						= 'Description';
		$_LANG['_ADMIN']['AD10_ERR_04']						= 'Name must be unique';

		$_LANG['_ADMIN']['AD11_ERR_01']						= 'Server ID';
		$_LANG['_ADMIN']['AD11_ERR_02']						= 'Server Name';
		$_LANG['_ADMIN']['AD11_ERR_03']						= 'Server IP Address';
		$_LANG['_ADMIN']['AD11_ERR_04']						= 'CP Url';
		$_LANG['_ADMIN']['AD11_ERR_05']						= 'CP Url Port';

		$_LANG['_ADMIN']['AD12_ERR_01']						= 'SiteInfo ID';
		$_LANG['_ADMIN']['AD12_ERR_02']						= 'Group';
		$_LANG['_ADMIN']['AD12_ERR_03']						= 'Name';
		$_LANG['_ADMIN']['AD12_ERR_04']						= 'Description';
		$_LANG['_ADMIN']['AD12_ERR_05']						= 'Title';
		$_LANG['_ADMIN']['AD12_ERR_06']						= 'Code';

		$_LANG['_ADMIN']['AD13_ERR_01']						= 'Topic ID';
		$_LANG['_ADMIN']['AD13_ERR_02']						= 'Topic ID';
		$_LANG['_ADMIN']['AD13_ERR_03']						= 'Name';
		$_LANG['_ADMIN']['AD13_ERR_04']						= 'Description';

		$_LANG['_ADMIN']['AD14_ERR_01']						= 'Vendor ID';
		$_LANG['_ADMIN']['AD14_ERR_02']						= 'Name';
		$_LANG['_ADMIN']['AD14_ERR_03']						= 'URL';
		$_LANG['_ADMIN']['AD14_ERR_04']						= 'Notes';
		$_LANG['_ADMIN']['AD14_ERR_05']						= 'Use IPN';

		$_LANG['_ADMIN']['AD15_ERR_01']						= 'Product ID';
		$_LANG['_ADMIN']['AD15_ERR_02']						= 'Vendor ID';
		$_LANG['_ADMIN']['AD15_ERR_03']						= 'Product ID';
		$_LANG['_ADMIN']['AD15_ERR_04']						= 'Description';
		$_LANG['_ADMIN']['AD15_ERR_05']						= 'Order Link';

		$_LANG['_ADMIN']['AD16_ERR_01']						= 'WHOIS ID';
		$_LANG['_ADMIN']['AD16_ERR_02']						= 'TLD Display';
		$_LANG['_ADMIN']['AD16_ERR_03']						= 'WHOIS URL';
		$_LANG['_ADMIN']['AD16_ERR_04']						= 'Notes';
		$_LANG['_ADMIN']['AD16_ERR_05']						= 'TLd Extension';
		$_LANG['_ADMIN']['AD16_ERR_06']						= '"Available" String';
		$_LANG['_ADMIN']['AD16_ERR_07']						= 'Use It';
		$_LANG['_ADMIN']['AD16_ERR_08']						= 'Override prod_id';

		$_LANG['_ADMIN']['AD17_ERR_01']						= 'Reminder ID';
		$_LANG['_ADMIN']['AD17_ERR_02']						= 'Template Title';
		$_LANG['_ADMIN']['AD17_ERR_03']						= 'Use It';
		$_LANG['_ADMIN']['AD17_ERR_04']						= 'Days Overdue';
		$_LANG['_ADMIN']['AD17_ERR_05']						= 'Email Subject';
		$_LANG['_ADMIN']['AD17_ERR_06']						= 'Message Contents';
		$_LANG['_ADMIN']['AD17_ERR_07']						= 'CC Support';

	# Section Labels for admin page
		$_LANG['_ADMIN']['l_CONFIGURATION']                    	= 'CONFIGURATION';
		$_LANG['_ADMIN']['l_CONTENT']							= 'CONTENT';
		$_LANG['_ADMIN']['l_OPERATION']                        	= 'OPERATION';
		$_LANG['_ADMIN']['l_PRODUCTS']						= 'PRODUCTS';
		$_LANG['_ADMIN']['l_EMAIL']							= 'EMAIL';

		$_LANG['_ADMIN']['TODO_List']							= 'To-Do List';

	# Expenses Section
		$_LANG['_ADMIN']['l_Pages']							= 'Pages';
		$_LANG['_ADMIN']['l_COSTS']							= 'SUPPLIERS';
		$_LANG['_ADMIN']['Required']							= 'Required';
		$_LANG['_ADMIN']['ERR_ERR38']							= 'Required Field';
		$_LANG['_ADMIN']['ERR_ERR33']							= 'Email address matches an existing address ~ please use a different address';
		$_LANG['_ADMIN']['ERR_ERR30']							= 'Email address is invalid ~ please use a different address';

		$_LANG['_ADMIN']['Email_Suppliers']					= 'Email Suppliers';
		$_LANG['_ADMIN']['Suppliers']							= 'Suppliers';
		$_LANG['_ADMIN']['Suppliers_Listing']					= 'Suppliers Listing';
		$_LANG['_ADMIN']['Suppliers_Editor']					= 'Suppliers Editor';
		$_LANG['_ADMIN']['Supplier_Info_Entry']					= 'Supplier Information';
		$_LANG['_ADMIN']['l_Supplier_ID']						= 'Supplier ID:';
		$_LANG['_ADMIN']['l_AccountID']						= 'Account Number:';
		$_LANG['_ADMIN']['l_Terms']							= 'Terms:';
		$_LANG['_ADMIN']['l_Company']							= 'Company:';
		$_LANG['_ADMIN']['l_First_Name']						= 'First Name:';
		$_LANG['_ADMIN']['l_Last_Name']						= 'Last Name:';
		$_LANG['_ADMIN']['l_Address_Street_1']					= 'Street Address:';
		$_LANG['_ADMIN']['l_Address_Street_2']					= 'Street Address 2:';
		$_LANG['_ADMIN']['l_City']							= 'City:';
		$_LANG['_ADMIN']['l_State_Province']					= 'State/Province:';
		$_LANG['_ADMIN']['l_Country']							= 'Country:';
		$_LANG['_ADMIN']['l_Zip_Postal_Code']					= 'Zip/Postal Code:';
		$_LANG['_ADMIN']['l_Phone']							= 'Phone:';
		$_LANG['_ADMIN']['l_Fax']							= 'Fax:';
		$_LANG['_ADMIN']['l_TollFree']						= 'TollFree:';
		$_LANG['_ADMIN']['l_Email_Address']					= 'Email Address:';
		$_LANG['_ADMIN']['l_TaxID']							= 'Fiscal ID:';
		$_LANG['_ADMIN']['l_Notes']							= 'Notes:';
		$_LANG['_ADMIN']['l_Email_Address_Additional']			= 'Additional Emails:';
		$_LANG['_ADMIN']['l_Email_Address_Additional_later']		= 'After the supplier has been added, you may return to this page and enter additional email contacts for this supplier:';
		$_LANG['_ADMIN']['l_Status']							= 'Status';
		$_LANG['_ADMIN']['l_Full_Name']						= 'Contact Name';

		$_LANG['_ADMIN']['Add_Suppliers_Entry_Results']			= 'Add Supplier Results';
		$_LANG['_ADMIN']['Edit_Suppliers_Entry_Results']			= 'Edit Supplier Results';

		$_LANG['_ADMIN']['l_Amount']							= 'Amount';
		$_LANG['_ADMIN']['l_Balance']							= 'Balance';
		$_LANG['_ADMIN']['Bills']							= 'Bills';
		$_LANG['_ADMIN']['l_Email_Archive']					= 'Email Archive';
		$_LANG['_ADMIN']['l_Id']								= 'Record ID';
		$_LANG['_ADMIN']['l_Date_Issued']						= 'Date Issued';
		$_LANG['_ADMIN']['l_Date_Due']						= 'Date Due';
		$_LANG['_ADMIN']['l_Date_Sent']						= 'Date Sent';
		$_LANG['_ADMIN']['l_Subject']							= 'Subject';

		$_LANG['_ADMIN']['Delete_Supplier_Entry_Confirmation']		= 'Delete Supplier Entry Confirmation';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Message']			= 'Are You Sure You Want to Delete Entry ID';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Message_Cont']		= 'and all the associated bills, messages, etc.?';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results']			= 'Delete Cupplier Entry Results';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_1']		= 'The following supplier items deleted';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_2']		= 'Deleted supplier records';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_3']		= 'Deleted supplier bill items';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_4']		= 'Deleted supplier bill transactions';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_5']		= 'Deleted supplier bills';
		$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_6']		= 'Deleted supplier additional emails';

		$_LANG['_ADMIN']['Entry_Deleted']						= 'Entry Deleted.';
		$_LANG['_ADMIN']['Accounts_Payable_Listing']				= 'Accounts Payable Listing';
?>