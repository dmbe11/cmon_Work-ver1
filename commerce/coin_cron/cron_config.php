<?php
/**
 * CronJobs: Configuration
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- This file uses imap functions from: http://xeoman.com/code/php/xeoport
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage CronJobs
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright 2003-2009 COINSoft Technologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * Notes:
 * If you wish to run cron jobs from the command line (without wget or similar),
 * you MUST set the variable below for *nix AND for Windows
 * If you are calling the cron jobs via web-browser or wget/curl or other browser
 * simulator, then ignore this file entirely ~ touch absolutely nothing.
 *
 * Here are some samples to get you started. Note that if you are running on a port
 * other than the standard port 80, the format IS protocol server port path, as shown below:
 * 	$_COINURL = 'http://my.phpcoin.com/phpcoin'			// installed in "phpcoin" subdirectory, no SSL
 * 	$_COINURL = 'https://my.phpcoin.com/phpcoin'		// installed in "phpcoin" subdirectory, with SSL
 * 	$_COINURL = 'http://my.phpcoin.com'					// installed in site root, no SSL
 * 	$_COINURL = 'https://my.phpcoin.com'				// installed in site root, with SSL
 * 	$_COINURL = 'http://my.phpcoin.com:8080'			// installed in site root, port 8080, no SSL
 * 	$_COINURL = 'http://my.phpcoin.com:8080/phpcoin'	// installed in "phpcoin" subdirectory, port 8080, no SSL
 */

# What is the URL to your phpCOIN installation?
# You MUST configure this IF you do not call the cronjob
# with wget or curl or some other web-browser simulator.
# If you DO use wget or curl, OR if you use a cron_config override file,
# then you can safely ignore this.
	$_COINURL = 'http://my.phpcoin.com';

#############################################
#	Do NOT touch anything below this line  #
#############################################

# Code to handle file being loaded by URL
	IF (eregi('cron_config.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

# Include config-override file, if present
	IF (file_exists($_pth.'/coin_overrides/cron_config_override.php')) {
		require($_pth.'/coin_overrides/cron_config_override.php');
	}

# If cronfile called via CLI
	IF (!$_SERVER['SERVER_NAME']) {

		# First we will fix any mistakes the user made in setting the URL above,
		# then we will build all the variables that would be set if the cronfile
		# was called via web-browser

		# Strip trailing slash in URL, if slash exists
			$_tx = substr($_COINURL, -1, 1);
			IF ($_tx == '/') {$_COINURL = substr($_COINURL, 0, -1);}

		# Strip leading slash in URL, if slash exists
		# this is in case the user forgot the protocol part
			$_tl = substr($_COINURL, 0,1);
			IF ($_tl == '/') {$_COINURL = substr($_COINURL, 1);}

		# Strip leading slash in cronfile, if slash exists
			$_tc = substr($cronfile, 0,1);
			IF ($_tc == '/') {$cronfile = substr($cronfile, 1);}

		# Append /coin_cron/ and our cronfilename, to create "browser URL"
			$_theURL = $_COINURL.'/coin_cron/'.$cronfile;

		# Check that protocol was supplied in URL, make it http if not
			$_http = substr($_theURL, 0, 4);
			$_https = substr($_theURL, 0, 5);
			IF (($_http != 'http') && ($_https != 'https')) {$_theURL = 'http://'.$_theURL;}

		# now break URL up into pieces so we can set the $_SERVER variables
			$_pieces = parse_url($_theURL);

		# Set server_name
			$_SERVER["SERVER_NAME"] = $_pieces['host'];

		# set server_port, making empty if standard port 80
			IF ($_pieces['port'] == 80) {$_pieces['port'] = '';}
			$_SERVER["SERVER_PORT"] = $_pieces['port'];

		# Set https flag
			$_scheme = $_pieces['scheme'];
			IF ($_scheme == 'https') {$_SERVER["HTTPS"] = "on";} ELSE {$_SERVER["HTTPS"] = "off";}

		# Make php-self
			$_SERVER['PHP_SELF'] = $_pieces['path'];
	}


# Figure out our location
	$separat			= '/coin_';

# build the file path
	$tempdocroot		= (substr(PHP_OS, 0, 3)=='WIN')?strtolower(getcwd()):getcwd();
	$_PACKAGE['DIR']	= str_replace("\\", '/', $tempdocroot);
	$data_array		= explode($separat, $_PACKAGE['DIR']);
	$_PACKAGE['DIR']	= $data_array[0].'/';

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
	require($_PACKAGE['DIR'].'coin_includes/security.php');

# Include config file
	require($_PACKAGE['DIR'].'config.php');

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

# Build the URL
	IF ($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443') {unset($_SERVER['SERVER_PORT']);}
	define('BASE_HREF', (($_SERVER['HTTPS'] == 'on')?"https":"http").'://'.$_SERVER["SERVER_NAME"].((!empty($_SERVER['SERVER_PORT']))?":".$_SERVER['SERVER_PORT']:'').$_PACKAGE['PATH']);
	IF ($_CCFG['USE_FULL_URL'] == 1) {
		$_PACKAGE['URL'] = (($_SERVER['HTTPS'] == 'on')?"https":"http").'://'.$_SERVER["SERVER_NAME"].((!empty($_SERVER['SERVER_PORT']))?":".$_SERVER['SERVER_PORT']:'').$_PACKAGE['PATH'];
	} ELSE {
		IF ($_PACKAGE['PATH'] == '/') {$_PACKAGE['PATH'] = '';}
		$_PACKAGE['URL'] = $_PACKAGE['PATH'];
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

?>