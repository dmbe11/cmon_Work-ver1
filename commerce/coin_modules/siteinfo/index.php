<?php
/**
 * Module: SiteInfo Pages (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage SiteInfo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER["PHP_SELF"])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=siteinfo');
	}


# Include functions file
	require_once(PKG_PATH_MDLS.'siteinfo/siteinfo_funcs.php');



IF (!$_GPV['group'] && !$_GPV['name'] && $_GPV['id'] < 1) {$_GPV['id'] = 1;}

##############################
# Mode Call: Load page
# Summary:
#	- List entries
##############################
IF (($_GPV['group'] && $_GPV['name']) || $_GPV['id']) {
	# Check for index to also load announce if on.
		IF ($_GPV['id'] == 1 || ($_GPV['group'] == 'site' && $_GPV['name'] == 'index')) {
			$_out .= do_site_info_display(0, 'site', 'announce', '1', $_GPV['ss']);
			IF ($_out != '') {$_out .= '<br>'.$_nl;}
		}

	# Call display site info function for:
		$_out .= do_site_info_display($_GPV['id'], $_GPV['group'], $_GPV['name'], '1', $_GPV['ss']);

	# Echo final output
		echo $_out;
}
?>