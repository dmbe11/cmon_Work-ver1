<?php
/**
 * Session: Common
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Session
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */

# Disable session_auto_start if enabled ~ it screws up logins
	ini_set('session.auto_start', 0);

# Start session
	session_name(md5($_SERVER['SERVER_NAME']));
	session_start();

# Prevent cross-server session stealing
	IF (!isset($_SESSION['hash']) || ($_SESSION['hash'] != md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']))) {
		$_SESSION = array();
		IF (isset($_COOKIE[session_name(md5($_SERVER['SERVER_NAME']))])) {setcookie(session_name(md5($_SERVER['SERVER_NAME'])), '', time()-42000, '/');}
		session_destroy();
		session_start();
		$_SESSION['hash'] = md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']);
	}

# Turn off pointless "warning" messages, and display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);

# If we validating URLs, make sure we do not timeout
	IF (isset($_GET['validate']) && strtolower($_GET['validate']) == 'urls') {
		ini_set('max_execution_time', 0);
		set_time_limit(0);
	}

# Exit with error if php < 4.1
	$_pv = phpversion();
	IF (!version_compare($_pv, '4.1', ">=")) {
		Header("Location: error.php?err=80&required=4.1");
		exit();
	}

# Set our desired "magic_quotes_runtime" if php < v6
	$_pv1 = explode('.', $_pv);
	IF ($_pv1[0] < 6) {set_magic_quotes_runtime(0);}


# Process PHP_SELF variable for XSS before we use it for path building
	while($_SERVER['PHP_SELF'] != urldecode($_SERVER['PHP_SELF'])) {$_SERVER['PHP_SELF'] = urldecode($_SERVER['PHP_SELF']);}
	$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);
	IF (function_exists('html_entity_decode')) {
		$_SERVER['PHP_SELF'] = html_entity_decode($_SERVER['PHP_SELF']);
	} ELSE {
		$_SERVER['PHP_SELF'] = unhtmlentities($_SERVER['PHP_SELF']);
	}
	while($_SERVER['PHP_SELF'] != strip_tags($_SERVER['PHP_SELF'])) {$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);}
	$pieces = explode("\"",$_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("'", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode(" ", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("\n", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("\r", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$_tx = substr($_SERVER['PHP_SELF'], -1, 1);
	IF ($_tx == '/') {$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, -1);}


# Figure out our location
	$separat			= '/coin_';

# build the file path
	$tempdocroot		= (substr(PHP_OS, 0, 3)=='WIN')?strtolower(getcwd()):getcwd();
	$_PACKAGE['DIR']	= str_replace("\\", '/', $tempdocroot);
	$data_array		= explode("$separat", $_PACKAGE['DIR']);
	$_PACKAGE['DIR']	= $data_array[0].'/';

# Include common session/paths setting file
	IF (is_readable($_PACKAGE['DIR'].'coin_includes/session_common.php')) {
		require($_PACKAGE['DIR'].'coin_includes/session_common.php');
	} ELSE {
		echo 'The required file <b>coin_includes/session_common.php</b> could not be located where it was expected at '.$_PACKAGE['DIR'].'coin_includes/session_common.php';
	}


# Code to handle file being loaded by URL
    IF (eregi('session_set.php', $_SERVER['PHP_SELF'])) {
        require_once('redirect.php');
        html_header_location('error.php?err=01');
        exit();
    }

# Set Time Start
	global $_OTS;
	$_OTS = explode(' ', microtime());
	$_OTS = number_format(($_OTS[1] + $_OTS[0]), 4, '.', '');


# Read Admin and User Session Vars (prevent passing in URL)
	IF (!isset($_SESSION["_sadmin_flg"]))         {$_SESSION["_sadmin_flg"] = 0;}
	IF (!isset($_SESSION["_sadmin_id"]))          {$_SESSION["_sadmin_id"] = 0;}
	IF (!isset($_SESSION["_sadmin_name"]))        {$_SESSION["_sadmin_name"] = 'none';}
	IF (!isset($_SESSION["_sadmin_name_first"]))  {$_SESSION["_sadmin_name_first"] = 'none';}
	IF (!isset($_SESSION["_sadmin_name_last"]))   {$_SESSION["_sadmin_name_last"] = 'none';}

	IF (!isset($_SESSION["_suser_flg"]))          {$_SESSION["_suser_flg"] = 0;}
	IF (!isset($_SESSION["_suser_id"]))           {$_SESSION["_suser_id"] = 0;}
	IF (!isset($_SESSION["_suser_name"]))         {$_SESSION["_suser_name"] = 'none';}
	IF (!isset($_SESSION["_suser_name_first"]))   {$_SESSION["_suser_name_first"] = 'none';}
	IF (!isset($_SESSION["_suser_name_last"]))    {$_SESSION["_suser_name_last"] = 'none';}
	IF (!isset($_SESSION["_suser_groups"]))       {$_SESSION["_suser_groups"] = 0;}


# Finish up the session vars.
	IF (!isset($_GPV['op']))	{$_GPV['op'] = '';}
	IF (!isset($_GPV['o']))	{$_GPV['o'] = '';}
	IF (!isset($_GPV['id']))	{$_GPV['id'] = 0;}
	IF (!isset($_GPV['cp']))	{$_GPV['cp'] = '';}

	IF (($_GPV['op'] == 'logout' || $_GPV['o'] == 'logout')) {
		session_unset();
		session_destroy();

		session_name(md5($_SERVER['SERVER_NAME']));
		session_start();
		IF (!isset($_SESSION['hash']) || ($_SESSION['hash'] != md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']))) {
			$_SESSION = array();
			IF (isset($_COOKIE[session_name(md5($_SERVER['SERVER_NAME']))])) {setcookie(session_name(md5($_SERVER['SERVER_NAME'])), '', time()-42000, '/');}
			session_destroy();
			session_start();
			$_SESSION['hash'] = md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']);
		}

//	} ELSE {
//		header("Cache-Control: must-revalidate");
//		header("Expires: " . gmdate("D, d M Y H:i:s", time()+24*60*60) . " GMT");
//		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	}


# Include core file
	require_once(PKG_PATH_INCL.'core.php');


# For php < 4.3 compatability
# replaces html_entity_decode
function unhtmlentities($string) {
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}
?>