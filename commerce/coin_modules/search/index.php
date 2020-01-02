<?php
/**
 * Module: Search Site (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Search
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_search.php
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=search');
		exit;
	}



# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_search.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_search_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_search_override.php');
	}

# Include search functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

/**************************************************************
 * Module Code
**************************************************************/


##############################
# Mode Call: Load Search Form
# Summary:
#	- Load Search Form
##############################
IF (!$_GPV['search_str']) {
	# Call function for search form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_search_form($_GPV, '1');

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Do Search
# Summary:
#	- And load output
##############################
IF ($_GPV['search_str'] && $_GPV['stage']) {
	# Call function for search and output
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_search($_GPV, '1');

	# Echo final output
		echo $_out;
}
?>