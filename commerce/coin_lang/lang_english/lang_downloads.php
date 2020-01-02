<?php
/**
 * Language: Englishode of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Downloads
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */

# Code to handle file being loaded by URL
	IF (eregi('lang_downloads.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit();
	}

/**************************************************************
 * Language Variables
**************************************************************/

	$_LANG['Downloads']['Title']				= 'Free Downloads';

	$_LANG['Downloads']['Files']				= 'Files';
	$_LANG['Downloads']['Description']			= 'Description';
	$_LANG['Downloads']['Released']			= 'Released';
	$_LANG['Downloads']['Name']				= 'Download Name';
	$_LANG['Downloads']['FileSize']			= 'FileSize';
	$_LANG['Downloads']['Contributor']			= 'Contributor';
	$_LANG['Downloads']['Count']				= 'Downloaded';
	$_LANG['Downloads']['Get_It']				= 'Get It';

	$_LANG['Downloads']['Group_Category']		= 'Group By Category';
	$_LANG['Downloads']['Group_Name']			= 'Group By Name';

	$_LANG['Downloads']['Pre-amble']			= 'This is the text that goes above the downloads table. You can put anything you want here. This text is in lang_downloads.php';

	$_LANG['Downloads']['Show_Descriptions']	= 'Show Descriptions';
	$_LANG['Downloads']['Hide_Descriptions']	= 'Hide Descriptions';

	$_LANG['Downloads']['Too_Long']			= 'Description is too long: please click icon for popup window';
?>