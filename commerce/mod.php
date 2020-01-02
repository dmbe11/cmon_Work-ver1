<?php
/**
 * Loader: Modules
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Output
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_MODNAME.php
 * @arguments $mod Module to load
 * @arguments $mode Mode to start module in
 */


# Include session file (loads core)
	require_once('coin_includes/session_set.php');

# Set Global Print Flag
	$_CCFG['_IS_PRINT'] = 0;

# Check for hack attempts to include external files. Set default to siteinfo "index"
	IF (!eregi("^([a-zA-Z0-9_-]{1,255})$", $_GPV['mod'])) {
		$_GPV['mod']	= 'siteinfo';
		$_GPV['name']	= 'index';
		$_GPV['group']	= 'site';
	}

# Validate requested module
	$_fr = is_readable(PKG_PATH_MDLS.$_GPV['mod'].'/index.php');
	IF (!$_fr) {html_header_location('error.php?err=04'); exit;}

# Call Load Component parms
	$_comp_name = $_GPV['mod'];
	IF ($_GPV['id'] != '') {$_comp_oper = $_GPV['id'];} ELSE {$_comp_oper = '';}
	$compdata = do_load_comp_data($_comp_name, $_comp_oper);

# Call page open (start to content)
	$_popen = do_page_open($compdata, '1');

# Check for "generator" line
	IF (!eregi('<meta name="generator" content="phpcoin">', $_popen) && !eregi('<meta name="generator" content="phpcoin" />', $_popen)) {
		html_header_location('error.php?err=98');
		exit();
	}


##############################
# Mode Call: Apply PayPal eMail Payment
# Summary:
#	- Redirect to /coin_cron/paypal.php
##############################
$_SEC	= get_security_flags();
$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);
IF ($_SEC['_sadmin_flg'] && $_GPV['mod'] == 'invoices' && $_GPV['mode'] == 'autopay' && $_GPV['obj'] == 'invc' && ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1)) {
	$_url = BASE_HREF.'coin_cron/paypal.php';
	Header("Location: $_url");
	exit();
}


# Output page open
	echo $_popen;

/*************************************************************/
# Module Load / Include files
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/index.php');
/*************************************************************/

# Call page open (content to finish)
	do_page_close($compdata, '0');

?>