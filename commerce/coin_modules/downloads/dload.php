<?php
/**
 * Module: Downloads (Process Download)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Downloads
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_downloads.php
 */


# Include Root File for package url/path and required files
	require_once('../../coin_includes/session_set.php');

# Get security vars
	$_login_flag	= 0;
	$_SEC		= get_security_flags();

##############################
# Mode Call: 	All modes
# Summary:
#	- Check if login required
##############################
IF ($_CCFG['LIMIT_DOWNLOADS_TO_LOGGED_IN'] && !$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for clients listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'user', '1').$_nl;

	# Echo final output
		echo $_out;
}


# Process download if logged in or login not required
IF (!$_login_flag) {


# File to load
	IF ($_GPV['id'] == '' OR $_GPV['id'] <= 0 ) {$id = 1;} ELSE {$id = $_GPV['id'];}

# Get file name of passed
	$result = '';
	$query = 'SELECT dload_filename FROM '.$_DBCFG['downloads'].' WHERE dload_id='.$id;
	$result = $db_coin->db_query_execute($query) or die ("Database Error On Query to get file.");

	IF ($db_coin->db_query_numrows($result) == 1) {list($dload_filename) = $db_coin->db_fetch_row($result);}

# Build meta refresh url for file to load
	IF ($dload_filename != '') {$_url = make_valid_link($_CCFG['DLOAD_URL']).$dload_filename;}

# Update counter
	$query	= 'UPDATE '.$_DBCFG['downloads'].' SET dload_count=dload_count + 1 WHERE dload_id='.$id;
	$result	= $db_coin->db_query_execute($query) or die ('Database Error On Query to update file.');

# Call redirect
	IF (!$_url) {$_url = PKG_REDIRECT_ROOT;}
	header("Location: $_url");
	exit();

}
?>