<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Custom
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_custom.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Custom Common Set
	$_LANG['_CUSTOM']['Please_Select']			= 'Please Select';
	$_LANG['_CUSTOM']['Welcome']				= 'Welcome';

# Language Variables: Some Common Buttons Text.
	$_LANG['_CUSTOM']['B_Submit']				= 'Submit';
	$_LANG['_CUSTOM']['B_Reset']				= 'Reset';

	$_LANG['_CUSTOM']['CP_Supporters_Edit']		= 'Edit Supporters';
	$_LANG['_CUSTOM']['CP_Downloads_Edit']		= 'Edit Downloads';
	$_LANG['_CUSTOM']['LICENSE']				= 'Edit Licenses';
?>