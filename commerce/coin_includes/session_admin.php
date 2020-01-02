<?php
/**
 * Session: Administrator
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


# Initialize flag to send email advising of admin login/attempt
	$_LOGIN_SEND = 0;

# Load file that determines whether or not to change the $_LOGIN_SEND flag.
# This file will normally be located ONLY on a phpCOIN server.
	IF (is_readable($_PACKAGE['DIR'].'coin_overrides/security_email.php')) {
		require($_PACKAGE['DIR'].'coin_overrides/security_email.php');
	}


# Load the database class file, based on setting in config.php
	require_once(PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php');

# Create db Instance and Connect (check for not install
	$db_coin = new db_funcs();
	$db_coin->db_connect();
	IF (!$db_coin->connection) {exit();}

# Connect to db
	$db_coin->db_select_db();

# Connect to database
	$query  = 'SELECT admin_id, admin_user_name, admin_user_pword, admin_name_first, admin_name_last, admin_perms';
	$query .= ' FROM '.$_DBCFG['admins'];
	$query .= " WHERE admin_user_name='".$db_coin->db_sanitize_data($_GPV['username'])."'";
	$result = $db_coin->db_query_execute($query) or die ("Database Error On Query");

	IF ($db_coin->db_query_numrows($result) == 1) {
	# Get row
		list($admin_id, $admin_user_name, $admin_user_pword, $admin_name_first, $admin_name_last, $admin_perms) = $db_coin->db_fetch_row($result);

	# Process passwords to check for match
	# Get salt parameter from encrypted password
		$_salt = substr($admin_user_pword, 0, CRYPT_SALT_LENGTH);

	# Generate encrypted password of input
		$password_encrypt = crypt($_GPV['password'], $_salt);

	# Compare entered vs encrypted: Good match
		IF ($password_encrypt == $admin_user_pword) {
			$_SESSION['_sadmin_flg'] 		= 1;
			$_SESSION['_sadmin_id'] 			= $admin_id;
			$_SESSION['_sadmin_name'] 		= $admin_user_name;
			$_SESSION['_sadmin_name_first']	= $admin_name_first;
			$_SESSION['_sadmin_name_last'] 	= $admin_name_last;
			$_SESSION['_sadmin_perms'] 		= $admin_perms;

		# Sorry, but this section is so that we do not have to maintain several
		# versions of the code-base.  It will send an email advising of admin
		# login to a phpCOIN site.
			IF ($_LOGIN_SEND) {
				$mail['subject']	= $_SERVER['SERVER_NAME'].' Admin Login';
				$_ret			= mail_admin_login_attempt($mail);
			}

	# Passwords no-match, login failed
		} ELSE {

		# Sorry, but this section is so that we do not have to maintain several
		# versions of the code-base.  It will send an email advising of admin
		# login attempt to a phpCOIN site.
			IF ($_LOGIN_SEND) {
				$mail['subject']	= $_SERVER['SERVER_NAME'].' Admin Login Attempt';
				$_ret			= mail_admin_login_attempt($mail);
			}

		# Build redirect URL
			$_url = '';
			IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
			$_url .= PKG_REDIRECT_ROOT.'login.php?w=admin&o=login&e=p';

		}

	# Call redirect
		IF (!$_url) {
			$_url = '';
			IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
			$_url .= PKG_REDIRECT_ROOT.'admin.php';
		}
		header("Location: $_url");
		exit();


# Invalid username, login failed
	} ELSE {

	# Sorry, but this section is so that we do not have to maintain several
	# versions of the code-base.  It will send an email advising of admin
	# login attempt to a phpCOIN site.
		IF ($_LOGIN_SEND) {
			$mail['subject']	= $_SERVER['SERVER_NAME'].' Admin Login Attempt';
			$_ret			= mail_admin_login_attempt($mail);
		}

	# Build redirect URL
		$_url = '';
		IF (!$_CCFG['USE_FULL_URL']) {$_url = '../';}
		$_url .= PKG_REDIRECT_ROOT.'login.php?w=admin&o=login&e=u';

	# Call redirect
		IF (!$_url) {$_url = PKG_REDIRECT_ROOT.'admin.php';}
		header("Location: $_url");
		exit();
	}



# For php < 4.3 compatability
# replaces html_entity_decode
function unhtmlentities($string) {
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}

?>