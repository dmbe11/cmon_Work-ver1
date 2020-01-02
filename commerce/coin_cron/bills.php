<?php
/**
 * CronJobs: Bills Process
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- This file uses imap functions from: http://xeoman.com/code/php/xeoport
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Bills
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_bills.php
 */

# Turn off pointless "warning" messages, and do NOT display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);

# Set cron filename
	$cronfile	= 'bills.php';

# Chaneg directory
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
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_bills.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_bills_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_bills_override.php');
	}

# Warn about non-configured URL, if necessary
	IF ($_COINURL == 'http://my.phpcoin.com') {$_cstr .= $_LANG['_BILLS']['BILLS_CRON_CONFIG'];}

# Include required functions file
	require_once(PKG_PATH_MDLS.'bills/bills_common.php');
	require_once(PKG_PATH_MDLS.'bills/bills_funcs.php');
	require_once(PKG_PATH_MDLS.'bills/bills_admin.php');


# Bill Status Auto-Update
	IF ($_ACFG['BILL_AUTO_UPDATE_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Update_Status'].$_sp.do_auto_bill_set_status();
	}

# Bill Status Auto-Copy
	IF ($_ACFG['BILL_AUTO_COPY_ENABLE']) {
		$_cstr .= '<br>'.$_LANG['_BILLS']['l_Auto_Copy_Recurring'].$_sp.do_auto_bill_copy();
	}

# Display results
	echo $_cstr;
?>