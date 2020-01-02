<?php
/**
 * Theme: Classic (Configuration)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Theme
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @designer Mike Lansberry
 */


# Code to handle file being loaded by URL
	IF ( eregi('config.php', $_SERVER['PHP_SELF']) ) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Misc Tag Parameters
**************************************************************/
# Use Rollover Images
	$_TCFG['_USE_ROLLOVER_IMAGES']      = 0;          // 1 = use "mouseover" images
											// 0 = do NOT use "mouseover" images
											// Whether used or not, images must still be defined
											// for ALL buttons

# Body Tag Parameters
	$_TCFG['_TAG_BODY_BACK_COLOR']	= "#F0F8FF";	// Tag- Body Background Color (purple="#9933cc")
	$_TCFG['_TAG_BODY_LINK_COLOR']	= "#0000FF";	// Tag- Body Link Color
	$_TCFG['_TAG_BODY_HOVER_COLOR']	= "#0000FF";	// Tag- Body Hover Color
	$_TCFG['_TAG_BODY_VLINK_COLOR']	= "#FF0000";	// Tag- Body Visited Link Color
	$_TCFG['_TAG_BODY_ALINK_COLOR']	= "#DC143C";	// Tag- Body Active Link Color

# Table Related Parameters
	$_TCFG['_TAG_TABLE_BRDR_COLOR']	= "#000000";	// Tag- Table Border Color
	$_TCFG['_TAG_TRTD_BKGRND_COLOR']	= "#000000";	// Tag- Table Row (TR) / Cell (TD) Background Color (border)

# Misc package images (in /images folder)
	$_PIMG_PREFIX					= '<img src="'.PKG_URL_IMGS;
	$_PIMG_SUFFIX					= '" border="0" align="middle">';

	$_TCFG['_IMG_SORT_ASC_S']		= $_PIMG_PREFIX.'arrow_up.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SORT_ASC_S'].'"'.$_PIMG_SUFFIX;			// Image URL for: Sort ASC
	$_TCFG['_IMG_SORT_DSC_S']		= $_PIMG_PREFIX.'arrow_dn.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SORT_DSC_S'].'"'.$_PIMG_SUFFIX;			// Image URL for: Sort DESC

# Some text savers
	$_PARM_PREFIX					= '<img src="'.$_CCFG['_PKG_URL_THEME_IMGS'].'nav/';
	$_PARM_SUFFIX					= '" border="0" align="middle">';


# Buttons / Img File URLS- Top Menu buttons- 30 px high
# Variable names ending in _MO are the "mouse-over" images
	$_TCFG['_IMG_MT_ABOUT_US_B']		= $_PARM_PREFIX.'cl_menu_top_about_us.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_ABOUT_US_B'].$_PARM_SUFFIX;				// Image URL for: About Us Button (big)
	$_TCFG['_IMG_MT_ABOUT_US_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_about_us.gif';
	$_TCFG['_IMG_MT_ARTICLES_B']		= $_PARM_PREFIX.'cl_menu_top_articles.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_ARTICLES_B'].$_PARM_SUFFIX;				// Image URL for: Articles Button (big)
	$_TCFG['_IMG_MT_ARTICLES_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_articles.gif';
	$_TCFG['_IMG_MT_CONTACT_B']		= $_PARM_PREFIX.'cl_menu_top_contact.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_CONTACT_B'].$_PARM_SUFFIX;					// Image URL for: Contact Button (big)
	$_TCFG['_IMG_MT_CONTACT_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_contact.gif';
	$_TCFG['_IMG_MT_FAQ_B']			= $_PARM_PREFIX.'cl_menu_top_faq.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_FAQ_B'].$_PARM_SUFFIX;							// Image URL for: FAQ Button (big)
	$_TCFG['_IMG_MT_FAQ_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_faq.gif';
	$_TCFG['_IMG_MT_HELPDESK_B']		= $_PARM_PREFIX.'cl_menu_top_helpdesk.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_HELPDESK_B'].$_PARM_SUFFIX;				// Image URL for: HelpDesk Button (big)
	$_TCFG['_IMG_MT_HELPDESK_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_helpdesk.gif';
	$_TCFG['_IMG_MT_HOME_B']			= $_PARM_PREFIX.'cl_menu_top_home.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_HOME_B'].$_PARM_SUFFIX;						// Image URL for: Home Button (big)
	$_TCFG['_IMG_MT_HOME_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_home.gif';
	$_TCFG['_IMG_MT_HOSTING_B']		= $_PARM_PREFIX.'cl_menu_top_hosting.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_HOSTING_B'].$_PARM_SUFFIX;					// Image URL for: Home Button (big)
	$_TCFG['_IMG_MT_HOSTING_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_hosting.gif';
	$_TCFG['_IMG_MT_LOGIN_B']		= $_PARM_PREFIX.'cl_menu_top_login.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_LOGIN_B'].$_PARM_SUFFIX;						// Image URL for: Login Button (big)
	$_TCFG['_IMG_MT_LOGIN_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_login.gif';
	$_TCFG['_IMG_MT_LOGOUT_B']		= $_PARM_PREFIX.'cl_menu_top_logout.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_LOGOUT_B'].$_PARM_SUFFIX;					// Image URL for: Logout Button (big)
	$_TCFG['_IMG_MT_LOGOUT_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_logout.gif';
	$_TCFG['_IMG_MT_ORDER_B']		= $_PARM_PREFIX.'cl_menu_top_place_order.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_ORDER_B'].$_PARM_SUFFIX;				// Image URL for: Place Order Button (big)
	$_TCFG['_IMG_MT_ORDER_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_order.gif';
	$_TCFG['_IMG_MT_PLACE_ORDER_B']	= $_PARM_PREFIX.'cl_menu_top_place_order.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_PLACE_ORDER_B'].$_PARM_SUFFIX;			// Image URL for: Place Order Button (big)
	$_TCFG['_IMG_MT_PLACE_ORDER_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_place_order.gif';
	$_TCFG['_IMG_MT_PLANS_B']		= $_PARM_PREFIX.'cl_menu_top_plans.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_PLANS_B'].$_PARM_SUFFIX;						// Image URL for: Place Order Button (big)
	$_TCFG['_IMG_MT_PLANS_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_plans.gif';
	$_TCFG['_IMG_MT_SEARCH_B']		= $_PARM_PREFIX.'cl_menu_top_search.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_SEARCH_B'].$_PARM_SUFFIX;					// Image URL for: Search Button (big)
	$_TCFG['_IMG_MT_SEARCH_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_search.gif';
	$_TCFG['_IMG_MT_SERVICES_B']		= $_PARM_PREFIX.'cl_menu_top_services.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_SERVICES_B'].$_PARM_SUFFIX;				// Image URL for: Services Button (big)
	$_TCFG['_IMG_MT_SERVICES_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_services.gif';
	$_TCFG['_IMG_MT_SUMMARY_B']		= $_PARM_PREFIX.'cl_menu_top_summary.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MT_SUMMARY_B'].$_PARM_SUFFIX;					// Image URL for: Services Button (big)
	$_TCFG['_IMG_MT_SUMMARY_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_top_summary.gif';

# Buttons / Img File URLS- User Menu buttons- 30 px high
	$_TCFG['_IMG_MU_ADMIN_B']		= $_PARM_PREFIX.'cl_menu_usr_admin.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_ADMIN_B'].$_PARM_SUFFIX;						// Image URL for: Admin Button
	$_TCFG['_IMG_MU_ADMIN_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_admin.gif';
	$_TCFG['_IMG_MU_CLIENTS_B']		= $_PARM_PREFIX.'cl_menu_usr_clients.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_CLIENTS_B'].$_PARM_SUFFIX;					// Image URL for: Clients Button
	$_TCFG['_IMG_MU_CLIENTS_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_clients.gif';
	$_TCFG['_IMG_MU_DOMAINS_B']		= $_PARM_PREFIX.'cl_menu_usr_domains.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_DOMAINS_B'].$_PARM_SUFFIX;					// Image URL for: Domains Button
	$_TCFG['_IMG_MU_DOMAINS_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_domains.gif';
	$_TCFG['_IMG_MU_EMAIL_CLIENT_B']	= $_PARM_PREFIX.'cl_menu_usr_email_client.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_EMAIL_CLIENT_B'].$_PARM_SUFFIX;		// Image URL for: Email Clients Button
	$_TCFG['_IMG_MU_EMAIL_CLIENT_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_email_client.gif';
	$_TCFG['_IMG_MU_HELPDESK_B']		= $_PARM_PREFIX.'cl_menu_usr_helpdesk.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_HELPDESK_B'].$_PARM_SUFFIX;				// Image URL for: HelpDesk Button
	$_TCFG['_IMG_MU_HELPDESK_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_helpdesk.gif';
	$_TCFG['_IMG_MU_INVOICES_B']		= $_PARM_PREFIX.'cl_menu_usr_invoices.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_INVOICES_B'].$_PARM_SUFFIX;				// Image URL for: Invoices Button
	$_TCFG['_IMG_MU_INVOICES_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_invoices.gif';
	$_TCFG['_IMG_MU_MY_ACCOUNT_B']	= $_PARM_PREFIX.'cl_menu_usr_my_account.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_MY_ACCOUNT_B'].$_PARM_SUFFIX;			// Image URL for: Orders Button
 	$_TCFG['_IMG_MU_MY_ACCOUNT_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_my_account.gif';
	$_TCFG['_IMG_MU_MY_PROFILE_B']	= $_PARM_PREFIX.'cl_menu_usr_my_profile.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_MY_PROFILE_B'].$_PARM_SUFFIX;			// Image URL for: Orders Button
 	$_TCFG['_IMG_MU_MY_PROFILE_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_my_profile.gif';
	$_TCFG['_IMG_MU_ORDERS_B']		= $_PARM_PREFIX.'cl_menu_usr_orders.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_ORDERS_B'].$_PARM_SUFFIX;					// Image URL for: Orders Button
	$_TCFG['_IMG_MU_ORDERS_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_orders.gif';
	$_TCFG['_IMG_MU_SUMMARY_B']		= $_PARM_PREFIX.'cl_menu_usr_summary.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MU_SUMMARY_B'].$_PARM_SUFFIX;					// Image URL for: Services Button
 	$_TCFG['_IMG_MU_SUMMARY_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_menu_usr_summary.gif';

# Buttons / Img File URLS (big ones- 30 px high)
	$_TCFG['_IMG_BLANK_B']			= $_PARM_PREFIX.'cl_big_blank.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BLANK_B'].$_PARM_SUFFIX;								// Image URL for: Blank Button (big)
	$_TCFG['_IMG_BLANK_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_blank.gif';								// Image URL for: Blank Button (big)
	$_TCFG['_IMG_DEMO_B']			= $_PARM_PREFIX.'cl_big_demo.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_DEMO_B'].$_PARM_SUFFIX;								// Image URL for: Demo Button (big)
	$_TCFG['_IMG_DEMO_B_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_demo.gif';								// Image URL for: Demo Button (big)
	$_TCFG['_IMG_FORUMS_B']			= $_PARM_PREFIX.'cl_big_forums.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FORUMS_B'].$_PARM_SUFFIX;							// Image URL for: Forums Button (big)
	$_TCFG['_IMG_FORUMS_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_forums.gif';							// Image URL for: Forums Button (big)
	$_TCFG['_IMG_GALLERY_B']			= $_PARM_PREFIX.'cl_big_gallery.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_GALLERY_B'].$_PARM_SUFFIX;							// Image URL for: Gallery Button (big)
	$_TCFG['_IMG_GALLERY_B_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_gallery.gif';							// Image URL for: Gallery Button (big)
	$_TCFG['_IMG_HD_ADD_MSG_B']		= $_PARM_PREFIX.'cl_big_hd_add_msg.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_HD_ADD_MSG_B'].$_PARM_SUFFIX;					// Image URL for: HelpDesk Add Msg Button (big)
	$_TCFG['_IMG_HD_ADD_MSG_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_hd_add_msg.gif';					// Image URL for: HelpDesk Add Msg Button (big)
	$_TCFG['_IMG_RATE_PHPCOIN_B']		= $_PARM_PREFIX.'cl_big_rate_phpcoin.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_RATE_PHPCOIN_B'].$_PARM_SUFFIX;				// Image URL for: Services Button (big)
	$_TCFG['_IMG_RATE_PHPCOIN_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_rate_phpcoin.gif';				// Image URL for: Services Button (big)
	$_TCFG['_IMG_SUPPORT_PHPCOIN_B']	= $_PARM_PREFIX.'cl_big_support_phpcoin.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SUPPORT_PHPCOIN_B'].$_PARM_SUFFIX;			// Image URL for: Services Button (big)
	$_TCFG['_IMG_SUPPORT_PHPCOIN_B_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_big_support_phpcoin.gif';			// Image URL for: Services Button (big)

# Buttons / Img File URLS (medium ones- 27 px high)
	$_TCFG['_IMG_BLANK_M']			= $_PARM_PREFIX.'cl_med_blank.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BLANK_M'].$_PARM_SUFFIX;								// Image URL for: Blank Button (medium)
	$_TCFG['_IMG_BLANK_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_blank.gif';								// Image URL for: Blank Button (medium)
	$_TCFG['_IMG_ADD_NEW_M']			= $_PARM_PREFIX.'cl_med_add_new.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_ADD_NEW_M'].$_PARM_SUFFIX;							// Image URL for: Add New Button (medium)
	$_TCFG['_IMG_ADD_NEW_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_add_new.gif';							// Image URL for: Add New Button (medium)
	$_TCFG['_IMG_ADD_BLOCK_M']		= $_PARM_PREFIX.'cl_med_add_block.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_ADD_BLOCK_M'].$_PARM_SUFFIX;						// Image URL for: Add Block Button (medium)
	$_TCFG['_IMG_ADD_BLOCK_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_add_block.gif';						// Image URL for: Add Block Button (medium)
	$_TCFG['_IMG_ADD_BLOCK_ITEM_M']	= $_PARM_PREFIX.'cl_med_add_block_item.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_ADD_BLOCK_ITEM_M'].$_PARM_SUFFIX;			// Image URL for: Add Block Item Button (medium)
	$_TCFG['_IMG_ADD_BLOCK_ITEM_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_add_block_item.gif';			// Image URL for: Add Block Item Button (medium)
	$_TCFG['_IMG_ADMIN_M']			= $_PARM_PREFIX.'cl_med_admin.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_ADMIN_M'].$_PARM_SUFFIX;								// Image URL for: Admin Button (medium)
	$_TCFG['_IMG_ADMIN_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_admin.gif';								// Image URL for: Admin Button (medium)
	$_TCFG['_IMG_BACK_TO_TOP_M']		= $_PARM_PREFIX.'cl_med_top.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_TOP_M'].$_PARM_SUFFIX;							// Image URL for: Back To Top Button (medium)
	$_TCFG['_IMG_BACK_TO_TOP_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_top.gif';							// Image URL for: Back To Top Button (medium)
	$_TCFG['_IMG_BACK_TO_CLIENT_M']	= $_PARM_PREFIX.'cl_med_back_to_client.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_CLIENT_M'].$_PARM_SUFFIX;			// Image URL for: Back To Client Button (medium)
	$_TCFG['_IMG_BACK_TO_CLIENT_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_client.gif';			// Image URL for: Back To Client Button (medium)
	$_TCFG['_IMG_BACK_TO_INVC_M']		= $_PARM_PREFIX.'cl_med_back_to_invc.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_INVC_M'].$_PARM_SUFFIX;				// Image URL for: Back To Invoice Button (medium)
	$_TCFG['_IMG_BACK_TO_INVC_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_invc.gif';				// Image URL for: Back To Invoice Button (medium)
	$_TCFG['_IMG_BACK_TO_ORDER_M']	= $_PARM_PREFIX.'cl_med_back_to_order.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_ORDER_M'].$_PARM_SUFFIX;				// Image URL for: Back To Order Button (medium)
	$_TCFG['_IMG_BACK_TO_ORDER_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_order.gif';				// Image URL for: Back To Order Button (medium)
	$_TCFG['_IMG_BACK_TO_TT_M']		= $_PARM_PREFIX.'cl_med_back_to_tt.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_TT_M'].$_PARM_SUFFIX;					// Image URL for: Back To Trouble Ticket Button (medium)
	$_TCFG['_IMG_BACK_TO_TT_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_tt.gif';					// Image URL for: Back To Trouble Ticket Button (medium)
	$_TCFG['_IMG_CLIENTS_M']			= $_PARM_PREFIX.'cl_med_clients.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_CLIENTS_M'].$_PARM_SUFFIX;							// Image URL for: Clients Button (medium)
	$_TCFG['_IMG_CLIENTS_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_clients.gif';							// Image URL for: Clients Button (medium)
	$_TCFG['_IMG_COPY_M']			= $_PARM_PREFIX.'cl_med_copy.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_COPY_M'].$_PARM_SUFFIX;								// Image URL for: Copy Button (medium)
	$_TCFG['_IMG_COPY_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_copy.gif';								// Image URL for: Copy Button (medium)
	$_TCFG['_IMG_DELETE_M']			= $_PARM_PREFIX.'cl_med_delete.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_DELETE_M'].$_PARM_SUFFIX;							// Image URL for: Delete Button (medium)
	$_TCFG['_IMG_DELETE_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_delete.gif';							// Image URL for: Delete Button (medium)
	$_TCFG['_IMG_EDIT_M']			= $_PARM_PREFIX.'cl_med_edit.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EDIT_M'].$_PARM_SUFFIX;								// Image URL for: Edit Button (medium)
	$_TCFG['_IMG_EDIT_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_edit.gif';								// Image URL for: Edit Button (medium)
	$_TCFG['_IMG_EDIT_BLOCK_M']		= $_PARM_PREFIX.'cl_med_edit_block.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EDIT_BLOCK_M'].$_PARM_SUFFIX;					// Image URL for: Edit Block Button (medium)
	$_TCFG['_IMG_EDIT_BLOCK_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_edit_block.gif';					// Image URL for: Edit Block Button (medium)
	$_TCFG['_IMG_EDIT_BLOCK_ITEM_M']	= $_PARM_PREFIX.'cl_med_edit_block_item.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EDIT_BLOCK_ITEM_M'].$_PARM_SUFFIX;			// Image URL for: Edit Block Item Button (medium)
	$_TCFG['_IMG_EDIT_BLOCK_ITEM_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_edit_block_item.gif';			// Image URL for: Edit Block Item Button (medium)
	$_TCFG['_IMG_EMAIL_M']			= $_PARM_PREFIX.'cl_med_email.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EMAIL_M'].$_PARM_SUFFIX;								// Image URL for: eMail Button (medium)
	$_TCFG['_IMG_EMAIL_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_email.gif';								// Image URL for: eMail Button (medium)
	$_TCFG['_IMG_FAQ_M']			= $_PARM_PREFIX.'cl_med_faq.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FAQ_M'].$_PARM_SUFFIX;									// Image URL for: FAQ Button (medium)
	$_TCFG['_IMG_FAQ_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_faq.gif';									// Image URL for: FAQ Button (medium)
	$_TCFG['_IMG_FAQ_ADD_FAQ_M']		= $_PARM_PREFIX.'cl_med_add_faq.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FAQ_ADD_FAQ_M'].$_PARM_SUFFIX;						// Image URL for: FAQ ADD Button (medium)
	$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_add_faq.gif';						// Image URL for: FAQ ADD Button (medium)
	$_TCFG['_IMG_FAQ_ADD_QA_M']		= $_PARM_PREFIX.'cl_med_add_faq_qa.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FAQ_ADD_QA_M'].$_PARM_SUFFIX;					// Image URL for: FAQ QA ADD Button (medium)
	$_TCFG['_IMG_FAQ_ADD_QA_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_add_faq_qa.gif';					// Image URL for: FAQ QA ADD Button (medium)
	$_TCFG['_IMG_FAQ_EDIT_FAQ_M']		= $_PARM_PREFIX.'cl_med_edit_faq.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FAQ_EDIT_FAQ_M'].$_PARM_SUFFIX;					// Image URL for: FAQ EDIT Button (medium)
	$_TCFG['_IMG_FAQ_EDIT_FAQ_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_edit_faq.gif';					// Image URL for: FAQ EDIT Button (medium)
	$_TCFG['_IMG_FAQ_EDIT_QA_M']		= $_PARM_PREFIX.'cl_med_edit_faq_qa.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_FAQ_EDIT_QA_M'].$_PARM_SUFFIX;					// Image URL for: FAQ QA EDIT Button (medium)
	$_TCFG['_IMG_FAQ_EDIT_QA_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_edit_faq_qa.gif';					// Image URL for: FAQ QA EDIT Button (medium)
	$_TCFG['_IMG_HOME_M']			= $_PARM_PREFIX.'cl_med_home.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_HOME_M'].$_PARM_SUFFIX;								// Image URL for: Home Button (medium)
	$_TCFG['_IMG_HOME_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_home.gif';								// Image URL for: Home Button (medium)
	$_TCFG['_IMG_IITEMS_EDITOR_M']	= $_PARM_PREFIX.'cl_med_iitems_editor.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_IITEMS_EDITOR_M'].$_PARM_SUFFIX;				// Image URL for: Invoice Items Editor Button (medium)
	$_TCFG['_IMG_IITEMS_EDITOR_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_iitems_editor.gif';				// Image URL for: Invoice Items Editor Button (medium)
	$_TCFG['_IMG_INVC_TRANS_M']		= $_PARM_PREFIX.'cl_med_invc_trans.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_INVC_TRANS_M'].$_PARM_SUFFIX;					// Image URL for: Invoice Transactions Button (medium)
	$_TCFG['_IMG_INVC_TRANS_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_invc_trans.gif';					// Image URL for: Invoice Transactions Button (medium)
	$_TCFG['_IMG_LISTING_M']			= $_PARM_PREFIX.'cl_med_listing.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_LISTING_M'].$_PARM_SUFFIX;							// Image URL for: Listing Button (medium)
	$_TCFG['_IMG_LISTING_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_listing.gif';							// Image URL for: Listing Button (medium)
	$_TCFG['_IMG_LOGIN_M']			= $_PARM_PREFIX.'cl_med_login.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_LOGIN_M'].$_PARM_SUFFIX;								// Image URL for: Login Button (medium)
	$_TCFG['_IMG_LOGIN_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_login.gif';								// Image URL for: Login Button (medium)
	$_TCFG['_IMG_LOGOUT_M']			= $_PARM_PREFIX.'cl_med_logout.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_LOGOUT_M'].$_PARM_SUFFIX;							// Image URL for: Logout Button (medium)
	$_TCFG['_IMG_LOGOUT_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_logout.gif';							// Image URL for: Logout Button (medium)
	$_TCFG['_IMG_MAIN_M']			= $_PARM_PREFIX.'cl_med_main.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_MAIN_M'].$_PARM_SUFFIX;								// Image URL for: Main Button (medium)
	$_TCFG['_IMG_MAIN_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_main.gif';								// Image URL for: Main Button (medium)
	$_TCFG['_IMG_NEXT_M']			= $_PARM_PREFIX.'cl_med_next.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_NEXT_M'].$_PARM_SUFFIX;								// Image URL for: Next Button (medium)
	$_TCFG['_IMG_NEXT_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_next.gif';								// Image URL for: Next Button (medium)
	$_TCFG['_IMG_PAYMENT_M']			= $_PARM_PREFIX.'cl_med_payment.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PAYMENT_M'].$_PARM_SUFFIX;							// Image URL for: Payment Button (medium)
	$_TCFG['_IMG_PAYMENT_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_payment.gif';							// Image URL for: Payment Button (medium)
	$_TCFG['_IMG_PREV_M']			= $_PARM_PREFIX.'cl_med_prev.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PREV_M'].$_PARM_SUFFIX;								// Image URL for: Prev Button (medium)
	$_TCFG['_IMG_PREV_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_prev.gif';								// Image URL for: Prev Button (medium)
	$_TCFG['_IMG_PRINT_M']			= $_PARM_PREFIX.'cl_med_print.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PRINT_M'].$_PARM_SUFFIX;								// Image URL for: Print Button (medium)
	$_TCFG['_IMG_PRINT_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_print.gif';								// Image URL for: Print Button (medium)
	$_TCFG['_IMG_REFRESH_M']			= $_PARM_PREFIX.'cl_med_refresh.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_REFRESH_M'].$_PARM_SUFFIX;							// Image URL for: Refresh Button (medium)
	$_TCFG['_IMG_REFRESH_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_refresh.gif';							// Image URL for: Refresh Button (medium)
	$_TCFG['_IMG_RETURN_M']			= $_PARM_PREFIX.'cl_med_return.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_RETURN_M'].$_PARM_SUFFIX;							// Image URL for: Return Button (medium)
	$_TCFG['_IMG_RETURN_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_return.gif';							// Image URL for: Return Button (medium)
	$_TCFG['_IMG_SEARCH_M']			= $_PARM_PREFIX.'cl_med_search.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SEARCH_M'].$_PARM_SUFFIX;							// Image URL for: Search Button (medium)
	$_TCFG['_IMG_SEARCH_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_search.gif';							// Image URL for: Search Button (medium)
	$_TCFG['_IMG_SELECT_LIST_M']		= $_PARM_PREFIX.'cl_med_select.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SELECT_LIST_M'].$_PARM_SUFFIX;						// Image URL for: Select List Button (medium)
	$_TCFG['_IMG_SELECT_LIST_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_select.gif';						// Image URL for: Select List Button (medium)
	$_TCFG['_IMG_SET_PAID_M']		= $_PARM_PREFIX.'cl_med_set_paid.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SET_PAID_M'].$_PARM_SUFFIX;						// Image URL for: Set Paid Button (medium)
	$_TCFG['_IMG_SET_PAID_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_set_paid.gif';						// Image URL for: Set Paid Button (medium)
	$_TCFG['_IMG_START_OVER_M']		= $_PARM_PREFIX.'cl_med_start_over.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_START_OVER_M'].$_PARM_SUFFIX;					// Image URL for: Start Over Button (medium)
	$_TCFG['_IMG_START_OVER_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_start_over.gif';					// Image URL for: Start Over Button (medium)
	$_TCFG['_IMG_SUMMARY_M']			= $_PARM_PREFIX.'cl_med_summary.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SUMMARY_M'].$_PARM_SUFFIX;							// Image URL for: Summary Button (medium)
	$_TCFG['_IMG_SUMMARY_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_summary.gif';							// Image URL for: Summary Button (medium)
	$_TCFG['_IMG_TRY_AGAIN_M']		= $_PARM_PREFIX.'cl_med_try_again.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_TRY_AGAIN_M'].$_PARM_SUFFIX;						// Image URL for: Try Again Button (medium)
	$_TCFG['_IMG_TRY_AGAIN_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_try_again.gif';						// Image URL for: Try Again Button (medium)
	$_TCFG['_IMG_VIEW_M']			= $_PARM_PREFIX.'cl_med_view.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_VIEW_M'].$_PARM_SUFFIX;								// Image URL for: View Button (medium)
	$_TCFG['_IMG_VIEW_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_view.gif';								// Image URL for: View Button (medium)
	$_TCFG['_IMG_VIEW_ALL_M']		= $_PARM_PREFIX.'cl_med_view_all.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_VIEW_ALL_M'].$_PARM_SUFFIX;						// Image URL for: View All Button (medium)
	$_TCFG['_IMG_VIEW_ALL_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_view_all.gif';						// Image URL for: View All Button (medium)

$_TCFG['_IMG_BACK_TO_BILL_M']		= $_PARM_PREFIX.'cl_med_back_to_bill.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_BILL_M'].$_PARM_SUFFIX;				// Image URL for: Back To Bill Button (medium)
$_TCFG['_IMG_BACK_TO_BILL_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_bill.gif';
$_TCFG['_IMG_BITEMS_EDITOR_M']	= $_PARM_PREFIX.'cl_med_bitems_editor.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BITEMS_EDITOR_M'].$_PARM_SUFFIX;				// Image URL for: Bill Items Editor Button (medium)
$_TCFG['_IMG_BITEMS_EDITOR_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_bitems_editor.gif';				// Image URL for: Bill Items Editor Button (medium)
$_TCFG['_IMG_BILL_TRANS_M']		= $_PARM_PREFIX.'cl_med_bill_trans.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BILL_TRANS_M'].$_PARM_SUFFIX;					// Image URL for: Bill Transactions Button (medium)
$_TCFG['_IMG_BILL_TRANS_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_bill_trans.gif';					// Image URL for: Bill Transactions Button (medium)
$_TCFG['_IMG_BACK_TO_SUPPLIER_M']	= $_PARM_PREFIX.'cl_med_back_to_supplier.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACK_TO_SUPPLIER_M'].$_PARM_SUFFIX;			// Image URL for: Back To Supplier Button (medium)
$_TCFG['_IMG_BACK_TO_SUPPLIER_M_MO']	= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_back_to_supplier.gif';			// Image URL for: Back To Supplier Button (medium)

	$_TCFG['_IMG_AUP_M']			= $_PARM_PREFIX.'cl_med_pol_aup.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_AUP_M'].$_PARM_SUFFIX;								// Image URL for: AUP Button (medium)
	$_TCFG['_IMG_AUP_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_pol_aup.gif';								// Image URL for: AUP Button (medium)
	$_TCFG['_IMG_BAN_CODE_M']		= $_PARM_PREFIX.'cl_med_pol_bc.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BAN_CODE_M'].$_PARM_SUFFIX;							// Image URL for: Banned Code Button (medium)
	$_TCFG['_IMG_BAN_CODE_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_pol_bc.gif';							// Image URL for: Banned Code Button (medium)
	$_TCFG['_IMG_PRIV_POL_M']		= $_PARM_PREFIX.'cl_med_pol_pp.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PRIV_POL_M'].$_PARM_SUFFIX;							// Image URL for: Pivacy Policy Button (medium)
	$_TCFG['_IMG_PRIV_POL_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_pol_pp.gif';							// Image URL for: Pivacy Policy Button (medium)
	$_TCFG['_IMG_TOS_M']			= $_PARM_PREFIX.'cl_med_pol_tos.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_TOS_M'].$_PARM_SUFFIX;								// Image URL for: TOS Button (medium)
	$_TCFG['_IMG_TOS_M_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_med_pol_tos.gif';								// Image URL for: TOS Button (medium)

# Buttons / Img File URLS (smallest ones- 25 px high)
	$_TCFG['_IMG_BLANK_S']			= $_PARM_PREFIX.'cl_small_blank.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BLANK_S'].$_PARM_SUFFIX;								// Image URL for: Blank Button (small)
	$_TCFG['_IMG_BLANK_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_blank.gif';								// Image URL for: Blank Button (small)
	$_TCFG['_IMG_ADD_S']			= $_PARM_PREFIX.'cl_small_add.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_ADD_S'].$_PARM_SUFFIX;								// Image URL for: Add Button (small)
	$_TCFG['_IMG_ADD_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_add.gif';								// Image URL for: Add Button (small)
	$_TCFG['_IMG_DEL_S']			= $_PARM_PREFIX.'cl_small_delete.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_DEL_S'].$_PARM_SUFFIX;								// Image URL for: Delete Button (small)
	$_TCFG['_IMG_DEL_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_delete.gif';								// Image URL for: Delete Button (small)
	$_TCFG['_IMG_EDIT_S']			= $_PARM_PREFIX.'cl_small_edit.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EDIT_S'].$_PARM_SUFFIX;								// Image URL for: Edit Button (small)
	$_TCFG['_IMG_EDIT_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_edit.gif';								// Image URL for: Edit Button (small)
	$_TCFG['_IMG_EMAIL_S']			= $_PARM_PREFIX.'cl_small_email.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EMAIL_S'].$_PARM_SUFFIX;							// Image URL for: eMail Button (small)
	$_TCFG['_IMG_EMAIL_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_email.gif';							// Image URL for: eMail Button (small)
	$_TCFG['_IMG_PRINT_S']			= $_PARM_PREFIX.'cl_small_print.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PRINT_S'].$_PARM_SUFFIX;							// Image URL for: View Button (small)
	$_TCFG['_IMG_PRINT_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_print.gif';							// Image URL for: View Button (small)
	$_TCFG['_IMG_SAVE_S']			= $_PARM_PREFIX.'cl_small_save.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SAVE_S'].$_PARM_SUFFIX;								// Image URL for: View Button (small)
	$_TCFG['_IMG_SAVE_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_save.gif';								// Image URL for: View Button (small)
	$_TCFG['_IMG_TOP_S']			= $_PARM_PREFIX.'cl_small_top.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_TOP_S'].$_PARM_SUFFIX;								// Image URL for: View Button (small)
	$_TCFG['_IMG_TOP_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_top.gif';								// Image URL for: View Button (small)
	$_TCFG['_IMG_VIEW_S']			= $_PARM_PREFIX.'cl_small_view.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_VIEW_S'].$_PARM_SUFFIX;								// Image URL for: View Button (small)
	$_TCFG['_IMG_VIEW_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'cl_small_view.gif';								// Image URL for: View Button (small)

# Buttons / Img File URLS (smallest ones- 25 px high) for CC Search output
	$_TCFG['_S_IMG_CP_S']			= $_PARM_PREFIX.'ni_cp.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_CP_S'].$_PARM_SUFFIX;										// Image URL for: Control Panel Button (small)
	$_TCFG['_S_IMG_CP_S_MO']			= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_cp.gif';										// Image URL for: Control Panel Button (small)
	$_TCFG['_S_IMG_DEL_S']			= $_PARM_PREFIX.'ni_delete.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_DEL_S'].$_PARM_SUFFIX;									// Image URL for: Delete Button (small)
	$_TCFG['_S_IMG_DEL_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_delete.gif';									// Image URL for: Delete Button (small)
	$_TCFG['_S_IMG_EDIT_S']			= $_PARM_PREFIX.'ni_edit.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EDIT_S'].$_PARM_SUFFIX;									// Image URL for: Edit Button (small)
	$_TCFG['_S_IMG_EDIT_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_edit.gif';									// Image URL for: Edit Button (small)
	$_TCFG['_S_IMG_EMAIL_S']			= $_PARM_PREFIX.'ni_email.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_EMAIL_S'].$_PARM_SUFFIX;									// Image URL for: eMail Button (small)
	$_TCFG['_S_IMG_EMAIL_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_email.gif';									// Image URL for: eMail Button (small)
	$_TCFG['_S_IMG_HELP_S']			= $_PARM_PREFIX.'ni_help.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_HELP_S'].$_PARM_SUFFIX;									// Image URL for: Help Button (small)
	$_TCFG['_S_IMG_HELP_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_help.gif';									// Image URL for: Help Button (small)
	$_TCFG['_S_IMG_PRINT_S']			= $_PARM_PREFIX.'ni_print.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PRINT_S'].$_PARM_SUFFIX;									// Image URL for: View Button (small)
	$_TCFG['_S_IMG_PRINT_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_print.gif';									// Image URL for: View Button (small)
	$_TCFG['_S_IMG_SEARCH_S']		= $_PARM_PREFIX.'ni_search.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_SEARCH_S'].$_PARM_SUFFIX;								// Image URL for: Search Button (small)
	$_TCFG['_S_IMG_SEARCH_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_search.gif';								// Image URL for: Search Button (small)
	$_TCFG['_S_IMG_VIEW_S']			= $_PARM_PREFIX.'ni_view.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_VIEW_S'].$_PARM_SUFFIX;									// Image URL for: View Button (small)
	$_TCFG['_S_IMG_VIEW_S_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_view.gif';									// Image URL for: View Button (small)
	$_TCFG['_S_IMG_PM_S']    		= $_PARM_PREFIX.'ni_param.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_PM_S'].$_PARM_SUFFIX;     							    // Image URL for: Parameters View Button (small)
	$_TCFG['_S_IMG_PM_S_MO']    		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'ni_param.gif';     							    // Image URL for: Parameters View Button (small)
	$_TCFG['_IMG_BACKUP_M']			= $_PARM_PREFIX.'n_med_backup.gif'.'" alt="'.$_LANG['_THEME']['ALT_IMG_BACKUP_M'].$_PARM_SUFFIX;								// Image URL for: Copy Button (medium)
	$_TCFG['_IMG_BACKUP_M_MO']		= $_CCFG['_PKG_URL_THEME_IMGS'].'nav/'.'n_med_backup.gif';								// Image URL for: Copy Button (medium)
?>