<?php
/**
 * Loader: Redirect Functions
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Redirect
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('redirect.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit();
	}

function html_header_location($url) {
	$url_full	= BASE_HREF.$url;
	Header("Location: $url_full");
	exit();
}
?>