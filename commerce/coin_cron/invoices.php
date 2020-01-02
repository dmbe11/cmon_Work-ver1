<?php
/**
 * CronJobs: Invoices Process
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- This file uses imap functions from: http://xeoman.com/code/php/xeoport
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Invoices
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_invoices.php
 */

# Turn off pointless "warning" messages, and do NOT display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);

# Set cron filename
	$cronfile	= 'invoices.php';

# Change directory
	$_pth	= str_replace("\\", '/', realpath($argv[0]));
	$_pth	= str_replace($cronfile, '', $_pth);
	$_pth	= str_replace('/coin_cron', '', $_pth);
	chdir($_pth);

# include the "where are we" code
	require_once('cron_config.php');
	$_cstr	= '';

# Include core file
	require_once($_PACKAGE['DIR'].'coin_includes/core.php');

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_invoices.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_invoices_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_invoices_override.php');
	}

# Warn about non-configured URL, if necessary
	IF ($_COINURL == 'http://my.phpcoin.com') {$_cstr .= $_LANG['_INVCS']['INVCS_CRON_CONFIG'];}

# Include required functions file
	require_once(PKG_PATH_MDLS.'invoices/invoices_funcs.php');
	require_once(PKG_PATH_MDLS.'invoices/invoices_admin.php');


# Invoice Status Auto-Update
	IF ($_ACFG['INVC_AUTO_UPDATE_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_INVCS']['l_Auto_Update_Status'].$_sp.do_auto_invoice_set_status();
	}

# Invoice Status Auto-Copy
	IF ($_ACFG['INVC_AUTO_COPY_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_INVCS']['l_Auto_Copy_Recurring'].$_sp.do_auto_invoice_copy();
	}

# Invoice Status Auto-Email
	IF ($_CCFG['INVC_AUTO_EMAIL_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_INVCS']['l_Auto_Email_Due'].$_sp.do_auto_invoice_emails();
	}

# Invoice Overdue Auto-reminder
	IF ($_CCFG['INVC_AUTO_EMAIL_ENABLE'] && $_ACFG['INVC_AUTO_REMINDERS_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_INVCS']['l_Auto_Email_OverDue'].$_sp.do_auto_overdue_invoice_emails();
	}

# Invoice "Soon Due" Auto-reminder
	IF ($_CCFG['INVC_AUTO_EMAIL_ENABLE'] && $_ACFG['INVC_SOON_REMINDERS_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_INVCS']['l_Auto_Email_SoonDue'].$_sp.do_auto_soondue_invoice_emails();
	}

# Display results
	echo $_cstr;
?>