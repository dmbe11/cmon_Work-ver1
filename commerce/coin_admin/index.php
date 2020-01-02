<?php
/**
 * Loader: Site Administration Menus
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Administration
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_admin.php
 * @arguments $cp Desired Control Panel
 */


###########################################################################
#	CHANGE YOUR ADMIN MENU OPTIONS HERE                                   #
###########################################################################


# Each menu item consists of three parts:
#	1: the text for the hyperlink
#	2: the actual hyperlink, and
#	3: the "group" that it belongs with
# Menu items will be displayed in the order entered in each array "group"
# To move a menu item to a different "group", change the first parameter of the array
# to match the desired group. For instance, to move "eMail Templates" from the MAIL group
# to the CONFIG group, change $AdminMenu['MAIL']..... to $AdminMenu['CONFIG'].....


	# 'Configuration' section
		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Admins'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=admins';

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Parameters'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=parms';

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Server_Info'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=server_info';

		IF ($_CCFG['WHOIS_ENABLED']) {
			$AdminMenu['CONFIG']['TEXT'][]	= $_LANG['_ADMIN']['CP_WHOIS_Lookups'];
			$AdminMenu['CONFIG']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=whois';
		}

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Downloads'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=downloads';

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Menu_Blocks'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=menu';

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Components'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=components';

		$AdminMenu['CONFIG']['TEXT'][]		= $_LANG['_ADMIN']['CP_Icons'];
		$AdminMenu['CONFIG']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=icons';



	# 'Products' section
		$AdminMenu['PRODUCTS']['TEXT'][]		= $_LANG['_ADMIN']['CP_Products'];
		$AdminMenu['PRODUCTS']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=prods';

		$AdminMenu['PRODUCTS']['TEXT'][]		= $_LANG['_ADMIN']['CP_Vendors'];
		$AdminMenu['PRODUCTS']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=vendors';

		$AdminMenu['PRODUCTS']['TEXT'][]		= $_LANG['_ADMIN']['CP_Vendors_Products'];
		$AdminMenu['PRODUCTS']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=vprods';

		$AdminMenu['PRODUCTS']['TEXT'][]		= $_LANG['_ADMIN']['CP_IPN_Log'];
		$AdminMenu['PRODUCTS']['URL'][]		= 'mod.php?mod=ipn';

		$AdminMenu['PRODUCTS']['TEXT'][]		= $_LANG['_ADMIN']['CP_IPN_Test'];
		$AdminMenu['PRODUCTS']['URL'][]		= 'mod.php?mod=ipn&mode=test';

	# 'Site Content' section
		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_FAQ_Edit'];
		$AdminMenu['CONTENT']['URL'][]		= 'mod.php?mod=faq&mode=edit&obj=faq';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_FAQ_QA_Edit'];
		$AdminMenu['CONTENT']['URL'][]		= 'mod.php?mod=faq&mode=edit&obj=faqqa';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_Topics'];
		$AdminMenu['CONTENT']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=topics';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_Categories'];
		$AdminMenu['CONTENT']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=categories';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_Pages_Edit'];
		$AdminMenu['CONTENT']['URL'][]		= 'mod.php?mod=pages&mode=edit';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_SiteInfo_Edit'];
		$AdminMenu['CONTENT']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=siteinfo';

		$AdminMenu['CONTENT']['TEXT'][]		= $_LANG['_ADMIN']['CP_Articles_Edit'];
		$AdminMenu['CONTENT']['URL'][]		= 'mod.php?mod=articles&mode=edit';

	# Sorry, but this section is so that I do not have to maintain several versions of the single code-base
		IF (
			eregi('phpcoin.com', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.ca', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.eu', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.com', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.ca', $_SERVER['SERVER_NAME'])
		) {
			$AdminMenu['CONTENT']['TEXT'][]	= $_LANG['_CUSTOM']['CP_Supporters_Edit'];
			$AdminMenu['CONTENT']['URL'][]	= $_SERVER['PHP_SELF'].'?cp=supporters';
		}


	# 'Mail' section
		$AdminMenu['MAIL']['TEXT'][]			= $_LANG['_ADMIN']['CP_eMail_Contacts'];
		$AdminMenu['MAIL']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=mail_contacts';

		$AdminMenu['MAIL']['TEXT'][]			= $_LANG['_ADMIN']['CP_eMail_Templates'];
		$AdminMenu['MAIL']['URL'][]			= $_SERVER['PHP_SELF'].'?cp=mail_templates';

		IF ($_ACFG['INVC_AUTO_REMINDERS_ENABLE']) {
			$AdminMenu['MAIL']['TEXT'][]		= $_LANG['_ADMIN']['CP_Reminders'];
			$AdminMenu['MAIL']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=reminders';
		}

		$AdminMenu['MAIL']['TEXT'][]			= $_LANG['_ADMIN']['CP_Mail'];
		$AdminMenu['MAIL']['URL'][]			= 'mod.php?mod=mail&mode=search&sw=archive';

		IF ($_ACFG['HELPDESK_AUTO_IMPORT_ENABLE']) {
			$AdminMenu['MAIL']['TEXT'][]		= $_LANG['_ADMIN']['CP_Support_Import'];
			$AdminMenu['MAIL']['URL'][]		= 'coin_cron/helpdesk.php';
		}


	# 'Operations' section
		IF ($_CCFG['_PKG_ENABLE_IP_BAN']) {
			$AdminMenu['OPS']['TEXT'][]		= $_LANG['_ADMIN']['CP_Banned_IP'];
			$AdminMenu['OPS']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=banip';
		}
	# Sorry, but this section is so that I do not have to maintain several versions of the single code-base
		IF (
			eregi('phpcoin.com', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.ca', $_SERVER['SERVER_NAME']) ||
			eregi('phpcoin.eu', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.com', $_SERVER['SERVER_NAME']) ||
			eregi('coinsofttechnologies.ca', $_SERVER['SERVER_NAME'])
		) {
			$AdminMenu['OPS']['TEXT'][]		= $_LANG['_CUSTOM']['LICENSE'];
			$AdminMenu['OPS']['URL'][]		= $_SERVER['PHP_SELF'].'?cp=licenses';
		}

		$AdminMenu['OPS']['TEXT'][]			= $_LANG['_ADMIN']['TODO_List'];
		$AdminMenu['OPS']['URL'][]			= 'mod.php?mod=todo';


	# "Expenses" Section
		IF ($_CCFG['SUPPLIERS_ENABLE']) {
			$AdminMenu['COSTS']['TEXT'][]		= $_LANG['_ADMIN']['Suppliers'];
			$AdminMenu['COSTS']['URL'][]		= 'admin.php?cp=suppliers';

			$AdminMenu['COSTS']['TEXT'][]		= $_LANG['_ADMIN']['Email_Suppliers'];
			$AdminMenu['COSTS']['URL'][]		= 'mod.php?mod=mail&mode=supplier';
		}
		IF ($_CCFG['SUPPLIERS_ENABLE'] && $_CCFG['BILLS_ENABLE']) {
			$AdminMenu['COSTS']['TEXT'][]		= $_LANG['_ADMIN']['Bills'];
			$AdminMenu['COSTS']['URL'][]		= 'mod.php?mod=bills';
		}


###########################################################################
#	DO NO CHANGE ANYTHING BELOW THIS LINE                                 #
###########################################################################

# Code to handle file being loaded by URL
	IF (!eregi('admin.php', $_SERVER['PHP_SELF']) ) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=admin.php');
		exit;
	}

# Get admin permissions
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);



# Content start flag
	$_out .= '<!-- Start content -->'.$_nl;

# Build Title String
	$_tstr = $_LANG['_ADMIN']['CP_Administrator_Menu'];

# Display as new "list" format
	IF ($_CCFG['DisplayType'] == 1) {
		$_cstr .= '<table width="100% border="0" cellpadding="5" cellspacing="0"><tr>';

	# Start left-hand column
		$_cstr .= '<td valign="top"><dl>';

	# Build "Config" section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_CONFIGURATION'].':</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['CONFIG']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['CONFIG']['URL'][$i].'">'.$AdminMenu['CONFIG']['TEXT'][$i].'</a></dd>'.$_nl;
		}
		$_cstr .= '<dd>&nbsp;</dd>';

	# Build "Products" Section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_PRODUCTS'].':</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['PRODUCTS']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['PRODUCTS']['URL'][$i].'">'.$AdminMenu['PRODUCTS']['TEXT'][$i].'</a></dd>'.$_nl;
		}
		$_cstr .= '<dd>&nbsp;</dd>';

	# End left-hand column and begin right-hand column
		$_cstr .= '</dl></td><td valign="top"><dl>';

	# Build "Content" section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_CONTENT'].':</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['CONTENT']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['CONTENT']['URL'][$i].'">'.$AdminMenu['CONTENT']['TEXT'][$i].'</a></dd>'.$_nl;
		}
		$_cstr .= '<dd>&nbsp;</dd>';

	# Build "Email" Section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_EMAIL'].'</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['MAIL']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['MAIL']['URL'][$i].'">'.$AdminMenu['MAIL']['TEXT'][$i].'</a></dd>'.$_nl;
		}
		$_cstr .= '<dd>&nbsp;</dd>';

	# Build "Operations" Section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_OPERATION'].':</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['OPS']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['OPS']['URL'][$i].'">'.$AdminMenu['OPS']['TEXT'][$i].'</a></dd>'.$_nl;
		}

	# Build "Expenses" Section
		$_cstr .= '<dt><b>'.$_LANG['_ADMIN']['l_COSTS'].':</b></dt>'.$_nl;
		for ($i=0; $i<= sizeof($AdminMenu['COSTS']['TEXT']); $i++) {
			$_cstr .= '<dd><a href="'.$AdminMenu['COSTS']['URL'][$i].'">'.$AdminMenu['COSTS']['TEXT'][$i].'</a></dd>'.$_nl;
		}

	# End right hand column
		$_cstr .= '</dl></td></tr></table>'.$_nl;


# Do original 'Buttons' layout
	} ELSE {
		$_td_start_str	= '<td class="TP1MED_BL" valign="top" width="25%">'.$_nl;
		$_td_hdr_str	= '<td class="BLK_HDR_MENU_C" valign="top" width="25%">'.$_nl;

		$_cstr .= '<center><br>'.$_nl;
		$_cstr .= '<table border="0" cellpadding="0" cellspacing="0" width="90%"><tr>'.$_nl;

	# Build "Config" section
		$_cstr .= '<td valign="top">';
		$_cstr .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_CONFIGURATION'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['CONFIG']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['CONFIG']['URL'][$i].'">'.$AdminMenu['CONFIG']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}
		$_cstr .= '</table></td>';

	# Build "Content" section
		$_cstr .= '<td valign="top">';
		$_cstr .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_CONTENT'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['CONTENT']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['CONTENT']['URL'][$i].'">'.$AdminMenu['CONTENT']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}
		$_cstr .= '</table></td>';


	# Build "Products" section
		$_cstr .= $_td_start_str;
		$_cstr .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_PRODUCTS'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['PRODUCTS']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['PRODUCTS']['URL'][$i].'">'.$AdminMenu['PRODUCTS']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}

	# Build 'Operations' Section
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_td_hdr_str.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_OPERATION'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['OPS']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['OPS']['URL'][$i].'">'.$AdminMenu['OPS']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}
		$_cstr .= '</table></td>';

	# Build "Email" section
		$_cstr .= '<td valign="top">';
		$_cstr .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_EMAIL'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['MAIL']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['MAIL']['URL'][$i].'">'.$AdminMenu['MAIL']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}

	# Build 'Expenses' Section
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_td_hdr_str.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr class="BLK_DEF_TITLE">'.$_nl;
		$_cstr .= $_td_hdr_str.'<b>'.$_LANG['_ADMIN']['l_COSTS'].'</b></td></tr>'.$_nl;
		for ($i=0; $i< sizeof($AdminMenu['COSTS']['TEXT']); $i++) {
			$_cstr .= '<tr>'.$_td_start_str.'<div class="button"><a href="'.$AdminMenu['COSTS']['URL'][$i].'">'.$AdminMenu['COSTS']['TEXT'][$i].'</a></div></td></tr>'.$_nl;
		}
		$_cstr .= '</table></td>';


	# Close off table
		$_cstr .= '</tr></table>';
		$_cstr .= '</center>';
	}



# Build "Backup Database" Section
	IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP13'] == 1 || $_PERMS['AP16'] == 1)) {
		$_cstr .= '<form action="coin_admin/cp_backup.php" method="post">';
		$_cstr .= '<dl><dt><b>'.$_LANG['_ADMIN']['CP_Backup'].':</b>';
		$_cstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=backup">'.$_TCFG['_S_IMG_PM_S'].'</a>';
		$_cstr .= '</dt>';
		$_cstr .= '<dd><INPUT type="radio" name="btype" value="download" checked> '.$_LANG['_BASE']['l_backup_download'].'<br>';
		$_cstr .= '<INPUT type="radio" name="btype" value="save"> '.$_LANG['_BASE']['l_backup_save'].'<br>';
		$_cstr .= '<INPUT type="radio" name="btype" value="email"> '.$_LANG['_BASE']['l_backup_email'].'<br>';
		$_cstr .= '<INPUT TYPE=hidden name="op" value="save">&nbsp;&nbsp;&nbsp;';
		$_cstr .= '<input type="image" src="'.$_CCFG['_PKG_URL_THEME_IMGS'].'nav/n_med_backup.gif" alt="Backup" border="0" align="middle">';
		$_cstr .= '</dd></dl></form>';
	}



# Block Footer Menu
	$_mstr .= do_nav_link('login.php?w=admin&o=logout', $_TCFG['_IMG_LOGOUT_M'],$_TCFG['_IMG_LOGOUT_M_MO'],'',$_LANG['_BASE']['B_Log_Out']);
	$_mstr .= do_nav_link($_SERVER['PHP_SELF'], $_TCFG['_IMG_REFRESH_M'],$_TCFG['_IMG_REFRESH_M_MO'],'','');

# Call block it function
	$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
	$_out .= '<br>'.$_nl;

# Echo final output
	echo $_out;


# Check for updates to phpCOIN
	IF ($_CCFG['AUTOCHECK_UPDATES']) {echo display_phpcoin_updates();}

?>