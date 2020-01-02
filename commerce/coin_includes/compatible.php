<?php
/**
 * Loader: Compatibility
 *	- This file allows themes and modules written for phpCOIN v1.3.1 and lower to function without modification in phpCOIN v1.4.0
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Compatibility
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('compatible.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit;
	}


# Configure old-style path/url variables
	$_CCFG['_PKG_REDIRECT_ROOT']	= $_PACKAGE['URL'];	# Site- Root used for redirect URL (must have http://)
	$_CCFG['_PKG_URL_BASE']		= $_PACKAGE['URL'];
	$_CCFG['_PKG_URL_INCL']		= PKG_URL_BASE.'coin_includes/';
	$_CCFG['_PKG_URL_THEME']		= PKG_URL_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/';
	$_CCFG['_PKG_URL_THEME_IMGS']	= PKG_URL_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/images/';
	$_CCFG['_PKG_URL_IMGS']		= PKG_URL_BASE.'coin_images/';
	$_CCFG['_PKG_URL_ADDONS']	= PKG_URL_BASE.'coin_addons/';
	$_CCFG['_PKG_URL_MDLS']		= PKG_URL_BASE.'coin_modules/';

	$_CCFG['_PKG_PATH_BASE']		= $_PACKAGE['DIR'];
	$_CCFG['_PKG_PATH_ADMN']		= PKG_PATH_BASE.'coin_admin/';
	$_CCFG['_PKG_PATH_ADDONS']	= PKG_PATH_BASE.'coin_addons/';
	$_CCFG['_PKG_PATH_AUXP']		= PKG_PATH_BASE.'coin_auxpages/';
	$_CCFG['_PKG_PATH_DBSE']		= PKG_PATH_BASE.'coin_database/';
	$_CCFG['_PKG_PATH_IMGS']		= PKG_PATH_BASE.'coin_images/';
	$_CCFG['_PKG_PATH_INCL']		= PKG_PATH_BASE.'coin_includes/';
	$_CCFG['_PKG_PATH_LANG']		= PKG_PATH_BASE.'coin_lang/'.$_CCFG['_HC_PKG_LANG'].'/';
	$_CCFG['_PKG_PATH_MDLS']		= PKG_PATH_BASE.'coin_modules/';
	$_CCFG['_PKG_PATH_THEME']	= PKG_PATH_BASE.'coin_themes/'.$_CCFG['_HC_PKG_THEME'].'/';
	$_CCFG['_PKG_PATH_MDLS']		= PKG_PATH_BASE.'coin_modules/';



function do_addslashes($_input) {
	global $db_coin;
	return $db_coin->db_sanitize_data($_input);
}
function do_stripslashes($_input) {
	IF (function_exists('get_magic_quotes_gpc')) {
		$value = (get_magic_quotes_gpc()) ? (stripslashes($_input)) : ($_input);
	} ELSE {
		$value = $_input;
	}
	return $value;
}



/**************************************************************
 * Database API Functions
**************************************************************/
function db_read_prefix($suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	$db_coin->db_connect_check();
	return $db_coin->db_return_prefix();
}

function db_connect($suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	return $db_coin->db_connect();
}

function db_query_execute($query, $suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	$db_coin->db_connect_check();
	return $db_coin->db_query_execute($query);
}

function db_fetch_array($result, $suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	$db_coin->db_connect_check();
	return $db_coin->db_fetch_array($result);
}

function db_fetch_row($result, $suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	$db_coin->db_connect_check();
	return $db_coin->db_fetch_row($result);
}

function db_query_numrows($result, $suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	$db_coin->db_connect_check();
	return $db_coin->db_query_numrows($result);
}

function db_query_insertid($suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	return $db_coin->db_query_insertid();
}

function db_query_affected_rows($suppress_err_flag=0) {
	global $db_coin;
	$db_coin->db_set_suppress_errors($suppress_err_flag);
	return $db_coin->db_query_affected_rows();
}

function db_query_count() {
	global $db_coin;
	return $db_coin->db_query_count();
}

function db_query_strings() {
	global $db_coin;
	return $db_coin->db_query_strings();
}
?>