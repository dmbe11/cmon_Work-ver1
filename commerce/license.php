<?php
/**
 * License Complaince Checker
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage License
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Include core file
	require_once('coin_includes/session_set.php');

# Get Current version
	$_vdata		= do_get_version();
	$_get_version	= $_vdata['version'];

# Check if "license.txt" is present
	clearstatcache();
	IF (!is_readable('coin_docs/license.txt')) {
		html_header_location('error.php?err=97');
	}

# Call page open (start to content)
	$_popen = do_page_open($compdata, '1');

# Check for META tag "generator=phpcoin"
	IF (!eregi('<meta name="generator" content="phpcoin">', $_popen) && !eregi('<meta name="generator" content="phpcoin" />', $_popen)) {
		html_header_location('error.php?err=98');
	}

# Call page close (content to finish)
	$_pclose = do_page_close($compdata, '1');

# Check for "powered by" line
	IF (!eregi('Powered By <a href="http://www.phpcoin.com" target="_blank">phpCOIN</a>', $_pclose)) {
		html_header_location('error.php?err=99');
	}

# All good, so display results:
	IF (eregi('license.php', $_SERVER["PHP_SELF"])) {
		$block_title	= 'phpCOIN License Compliance Check';
		$block_content	= 'Results indicate full compliance. Thank-you!';
		$block_content	.= '<br>Version: '.$_get_version;
		result_block($block_title, $block_content);
	}

/**************************************************************
 * License File Functions Code
**************************************************************/
function result_block($block_title, $block_content) {
		global $_CCFG, $_GPV, $_nl, $_sp;

		# Build Table Start and title
			$_out .= '<html>'.$_nl;
			$_out .= '<head>'.$_nl;
			$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
			$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
			$_out .= '<title>'.'License Compliance Check'.'</title>'.$_nl;

			$_out .= '<style media="screen" type="text/css">'.$_nl;
			$_out .= '<!--'.$_nl;
			$_out .= 'body				{ background-color: #FFFFFF; margin: 5px }'.$_nl;
			$_out .= 'p					{ color: #001; font-family: Verdana, Arial, Helvetica, Geneva }'.$_nl;
			$_out .= '.BLK_DEF_TITLE	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #EBEBEB }'.$_nl;
			$_out .= '.BLK_DEF_ENTRY	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #F5F5F5 }'.$_nl;
			$_out .= '.BLK_IT_TITLE		{ color: #001; font-style: normal; font-weight: bold; text-align: left; font-size: 12px; padding: 5px; height: 25px }'.$_nl;
			$_out .= '.BLK_IT_ENTRY		{ color: #001; font-style: normal; font-weight: normal; text-align: center; font-size: 11px; padding: 5px }'.$_nl;
			$_out .= '--></style>'.$_nl;

			$_out .= '</head>'.$_nl;

			$_out .= '<body link="blue" vlink="red">'.$_nl;
			$_out .= '<div align="center" width="100%">'.$_nl;

			$_out .= '<br>';
			$_out .= '<div align="center" width="100%">';
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#000000">';
			$_out .= '<tr bgcolor="#000000"><td bgcolor="#000000">';
			$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">';
			$_out .= '<tr class="BLK_DEF_TITLE" height="30" valign="middle"><td class="BLK_IT_TITLE">';
			$_out .= $block_title;
			$_out .= '</td></tr>';
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY">';
			$_out .= $block_content;
			$_out .= '</td></tr>';
			$_out .= '</table>';
			$_out .= '</td></tr>';
			$_out .= '</table>';
			$_out .= '</div>';

			$_out .= '</div>'.$_nl;
			$_out .= '</body>'.$_nl;
			$_out .= '</html>'.$_nl;

		# Echo final output
			echo $_out;
	}
?>