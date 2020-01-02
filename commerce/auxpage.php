<?php
/**
 * Loader: Auxilliary Pages
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Auxiliarry_Pages
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @arguments $page (without .php extension) Desired auxpage
 */


# Include session file (loads core)
	require_once("coin_includes/session_set.php");

# Set Global Print Flag
	$_CCFG['_IS_PRINT'] = 0;

# Check for hack attempts to include external files
	IF (!eregi("^([a-zA-Z0-9_]{1,255})$", $_GPV['page'])) {$_GPV['page'] = 'h';}

# Validate requested page
	$_fr = is_readable(PKG_PATH_AUXP.$_GPV['page'].'.php');
	IF (!$_fr) {html_header_location('error.php?err=03'); exit;}

# Call Load Component parms
	$_comp_name	= $_GPV['page'];
	$_comp_oper = '';
	$compdata	= do_load_comp_data($_comp_name, $_comp_oper);

# Call page open (start to content)
	do_page_open($compdata, '0');

/*************************************************************/
# Auxpage Load / Include files
	IF ($_GPV['page'] == 'h') {
		echo '<!-- Start content -->'.$_nl;
		echo '	<table width="'.$_TCFG['_WIDTH_CONTENT_AREA'].'" cellpadding="0" cellspacing="0" border="0">'.$_nl;
		echo '		<tr bgcolor="#000000">'.$_nl;
		echo '			<td bgcolor="#000000"><table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
		echo '				<tr class="BLK_DEF_TITLE" height="30" valign="middle">'.$_nl;
		echo '					<td class="BLK_IT_TITLE" align="center">Why Are You Doing This?</td>'.$_nl;
		echo '				</tr><tr class="BLK_DEF_ENTRY">'.$_nl;
		echo '					<td class="BLK_IT_ENTRY" align="left" valign="top">You are attempting to hack my installation of phpCOIN. That is not nice. Why are you trying to destroy my livelihood? Aren\'t you ashamed of yourself?</td>'.$_nl;
		echo '				</tr>'.$_nl;
		echo '			</table></td>'.$_nl;
		echo '		</tr>'.$_nl;
		echo '	</table>'.$_nl;
		echo '<!-- Finish content -->'.$_nl;
	} ELSE {
		require_once(PKG_PATH_AUXP.$_GPV['page'].'.php' );
	}
/*************************************************************/

# Call page close (content to finish)
	do_page_close($compdata, '0');

?>