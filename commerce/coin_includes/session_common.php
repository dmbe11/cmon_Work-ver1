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

# Code to handle file being loaded by URL
	IF (eregi('session_common.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit;
	}

# Common paths
	define('PKG_PATH_BASE', $_PACKAGE['DIR']);
	define('PKG_PATH_INCL', PKG_PATH_BASE.'coin_includes/');
	define('PKG_PATH_MDLS', PKG_PATH_BASE.'coin_modules/');
	define('PKG_PATH_ADDONS', PKG_PATH_BASE.'coin_addons/');
	define('PKG_PATH_OVERRIDES', PKG_PATH_BASE.'coin_overrides/');
	define('PKG_PATH_ADMN', PKG_PATH_BASE.'coin_admin/');
	define('PKG_PATH_AUXP', PKG_PATH_BASE.'coin_auxpages/');
	define('PKG_PATH_DBSE', PKG_PATH_BASE.'coin_database/');
	define('PKG_PATH_IMGS', PKG_PATH_BASE.'coin_images/');

# Include security file
	require(PKG_PATH_INCL.'security.php');

# Include config file
	require(PKG_PATH_BASE.'config.php');

# Include config-override file, if present
	IF (file_exists(PKG_PATH_OVERRIDES.'config_override.php')) {
		require(PKG_PATH_OVERRIDES.'config_override.php');
	}

# Build the web path
	$Location			= str_replace("\\", '/', $_SERVER['PHP_SELF']);
	$PathWeb			= explode("/", $Location);
	$FileName			= array_pop($PathWeb);
	$_PACKAGE['PATH']	= implode("/", $PathWeb);
	$data_array		= explode("$separat", $_PACKAGE['PATH']);
	$_PACKAGE['PATH']	= $data_array[0].'/';

# Use full urls if setup/upgrade
	IF (eregi('setup.php', $_SERVER['PHP_SELF'])) {$_CCFG['USE_FULL_URL'] = 1;}

# Build the URL
	IF ($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443') {unset($_SERVER['SERVER_PORT']);}
	define('BASE_HREF', (($_SERVER['HTTPS'] == 'on')?"https":"http").'://'.$_SERVER['SERVER_NAME'].((!empty($_SERVER['SERVER_PORT']))?":".$_SERVER['SERVER_PORT']:'').$_PACKAGE['PATH']);
	IF ($_CCFG['USE_FULL_URL'] == 1) {
		$_PACKAGE['URL'] = (($_SERVER['HTTPS'] == 'on')?"https":"http").'://'.$_SERVER['SERVER_NAME'].((!empty($_SERVER['SERVER_PORT']))?":".$_SERVER['SERVER_PORT']:'').$_PACKAGE['PATH'];
	} ELSE {
		$_PACKAGE['URL'] = '';
	}

# Common URLs
	define('PKG_REDIRECT_ROOT', $_PACKAGE['URL']);
	define('PKG_URL_BASE', $_PACKAGE['URL']);
	define('PKG_URL_INCL', PKG_URL_BASE.'coin_includes/');
	define('PKG_URL_MDLS', PKG_URL_BASE.'coin_modules/');
	define('PKG_URL_ADDONS', PKG_URL_BASE.'coin_addons/');
	define('PKG_URL_IMGS', PKG_URL_BASE.'coin_images/');

# These five are left as variables because they can be over-ridden by the database
	$_CCFG['_PKG_URL_THEME']			= PKG_URL_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/';
	$_CCFG['_PKG_PATH_THEME']		= PKG_PATH_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/';
	$_CCFG['_PKG_URL_THEME_IMGS']		= PKG_URL_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/images/';
	$_CCFG['_PKG_PATH_LANG']			= PKG_PATH_BASE.'coin_lang/'.$_CCFG['_HC_PKG_LANG'].'/';
	$_CCFG['_PKG_PATH_LANG_OVERRIDE']	= PKG_PATH_OVERRIDES.$_CCFG['_HC_PKG_LANG'].'/';


# Include constants file (requires db_config for prefix)
	require_once(PKG_PATH_INCL.'constants.php');

# Include constants-override file, if present
	IF (file_exists(PKG_PATH_OVERRIDES.'constants_override.php')) {
		require(PKG_PATH_OVERRIDES.'constants_override.php');
	}

?>