<?php
/**
 * Configuration: phpCOIN Version
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Output
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */

# Code to handle file being loaded by URL
	IF (eregi('version.php', $_SERVER['PHP_SELF'])) {
		Header("Location: error.php?err=01");
		exit();
	}

$ThisVersion	= '1.6.5';		// Current Release Version
$ThisFix		= '2009-09-26';	// Current fix-file
?>