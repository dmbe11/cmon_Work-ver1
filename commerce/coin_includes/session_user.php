<?php
/**
 * Session: Client
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

# Load the database class file, based on setting in config.php
	require_once(PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php');

# Create db Instance and Connect (check for not install
	$db_coin = new db_funcs();
	$db_coin->db_connect();
	IF (!$db_coin->connection) {exit();}

# Connect to db
	$db_coin->db_select_db();

# Read the database in order to grab the default page to show client
# This is clunky, but the only way the default page could be stored in the database
# and therefore editable via Admin
	$query  = 'SELECT parm_value FROM '.$_DBCFG['table_prefix'].'parameters';
	$query .= " WHERE parm_name='CLIENT_VIEW_PAGE_UPON_LOGIN'";
	$result = $db_coin->db_query_execute($query) or die ("Database Error On Query");
	IF ($db_coin->db_query_numrows($result) == 1) {
		while ($row = $db_coin->db_fetch_array($result)) {
			$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN'] = $row['parm_value'];
		}
	}

# Read desired language, if database language is set
	IF ($_CCFG['_DB_PKG_LANG_ENABLE'] == 1) {
		$query  = 'SELECT parm_value FROM '.$_DBCFG['table_prefix'].'parameters';
		$query .= " WHERE parm_name='_DB_PKG_LANG'";
		$result = $db_coin->db_query_execute($query) or die ("Database Error On Query");
		IF ($db_coin->db_query_numrows($result) == 1) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_CCFG['_DB_PKG_LANG'] = $row['parm_value'];
			}
		# Re-Calc language related vars
			$_CCFG['_PKG_PATH_LANG']	= PKG_PATH_BASE.'coin_lang/'.$_CCFG['_DB_PKG_LANG'].'/';
		}
	}


# Load config arrays
	require_once ($_CCFG['_PKG_PATH_LANG'].'lang_config.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_config_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_config_override.php');
	}

# Set 'cc' as default page if not set in database
	IF (!$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN']) {$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN'] = 1;}


# Prepare URL for redirect after a succesful login
	$_url = '';
	IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
	$_url .= PKG_REDIRECT_ROOT.'mod.php?';
	IF (!$_GPV['mod']) {
		$_url .= 'mod=cc';
	} ELSE {
		while(list($key, $var) = each($_GPV)) {
			IF ($key != 'password' && $key != 'NULL') {$_url .= $key.'='.urlencode($var).'&';}
		}
	}

# Get the client data
	$query  = 'SELECT cl_id, cl_user_name, cl_user_pword, cl_status, cl_name_first, cl_name_last, cl_groups';
	$query .= ' FROM '.$_DBCFG['clients'];
	$query .= " WHERE cl_user_name='".$db_coin->db_sanitize_data($_GPV['username'])."' AND (cl_status = 'active' OR cl_status = '".$db_coin->db_sanitize_data($_CCFG['CL_STATUS'][1])."')";
	$result = $db_coin->db_query_execute($query) or die ("Database Error On Query");

	IF ($db_coin->db_query_numrows($result) == 1) {
		while ($row = $db_coin->db_fetch_array($result)) {
			$cl_id		= $row['cl_id'];
			$cl_user_name	= $row['cl_user_name'];
			$cl_user_pword	= $row['cl_user_pword'];
			$cl_status	= $row['cl_status'];
			$cl_name_first	= $row['cl_name_first'];
			$cl_name_last	= $row['cl_name_last'];
			$cl_groups	= $row['cl_groups'];
		}

	# Process passwords to check for match
	# Get salt parameter from encrypted password
		$_salt = substr($cl_user_pword, 0, CRYPT_SALT_LENGTH);

	# Generate encrypted password of input
		$password_encrypt = crypt($_GPV['password'], $_salt);

	# Compare entered vs encrypted
		IF ($password_encrypt == $cl_user_pword) {
			$_SESSION['_suser_flg'] 			= 1;
			$_SESSION['_suser_id'] 			= $cl_id;
			$_SESSION['_suser_name'] 		= $cl_user_name;
			$_SESSION['_suser_name_first']	= $cl_name_first;
			$_SESSION['_suser_name_last'] 	= $cl_name_last;
			$_SESSION['_suser_groups'] 		= $cl_groups;

			$db_coin->db_close($db_coin);

		} ELSE {
		# Passwords no-match, login failed
			$_url = '';
			IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
			$_url .= PKG_REDIRECT_ROOT.'login.php?w=user&o=login&e=p';
		}

	# Call redirect if good login OR bad password
		IF (!$_url) {
			$_url = '';
			IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
			$_url .= PKG_REDIRECT_ROOT;
		}
		header("Location: $_url");
		exit;

	} ELSE {
	# user no-match, login failed
		$_url = '';
		IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
		$_url .= PKG_REDIRECT_ROOT.'login.php?w=user&o=login&e=u';
		header("Location: $_url");
		exit;
	}

# For php < 4.3 compatability
# replaces html_entity_decode
function unhtmlentities($string) {
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}
?>