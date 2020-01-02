<?php
/**
 * Loader: php Configuration Information
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Configuration
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Include session file (loads core)
	require_once('coin_includes/session_set.php');

# Call Load Component parms
	$_comp_name = $_GPV['mod'];
	IF ($_GPV['id'] != '') {$_comp_oper = $_GPV['id'];} ELSE {$_comp_oper = '';}
	$compdata	= do_load_comp_data($_comp_name, $_comp_oper);

# Get security vars
	$_SEC = get_security_flags ();

# Do User Logged in check
	IF (!$_SEC['_sadmin_flg']) {
		do_page_open($compdata, '0');
		echo do_login('', 'admin', '1').$_nl;
		do_page_close($compdata, '0');
	} ELSE {
		# Call Standard php function
			echo phpinfo();
	}

?>