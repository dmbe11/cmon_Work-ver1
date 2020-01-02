<?php
/**
 * Loader: Error Display
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Error_Display
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @arguments $err Error number to display
 * @arguments $url URL to auto-redirect to (if any) after displaying error message
 * @arguments $required Minimum php version that phpCOIN requires to run
 */

# Disable session_auto_start if enabled ~ it screws up logins
	ini_set('session.auto_start', 0);

# Start session
	session_name(md5($_SERVER['SERVER_NAME']));
	session_start();

# Prevent cross-server session stealing
	IF (!isset($_SESSION['hash']) || ($_SESSION['hash'] != md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']))) {
		$_SESSION = array();
		IF (isset($_COOKIE[session_name(md5($_SERVER['SERVER_NAME']))])) {setcookie(session_name(md5($_SERVER['SERVER_NAME'])), '', time()-42000, '/');}
		session_destroy();
		session_start();
		$_SESSION['hash'] = md5($_SERVER['SERVER_NAME'].':'.$_SERVER['HTTP_HOST']);
	}

# Turn off pointless "warning" messages, and display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);


# Set our desired "magic_quotes_runtime" if php < v6
	$_pv1 = explode('.', $_pv);
	IF ($_pv1[0] < 6) {set_magic_quotes_runtime(0);}


# Process PHP_SELF variable for XSS before we use it for path building
	while($_SERVER['PHP_SELF'] != urldecode($_SERVER['PHP_SELF'])) {$_SERVER['PHP_SELF'] = urldecode($_SERVER['PHP_SELF']);}
	$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);
	IF (function_exists('html_entity_decode')) {
		$_SERVER['PHP_SELF'] = html_entity_decode($_SERVER['PHP_SELF']);
	} ELSE {
		$_SERVER['PHP_SELF'] = unhtmlentities($_SERVER['PHP_SELF']);
	}
	while($_SERVER['PHP_SELF'] != strip_tags($_SERVER['PHP_SELF'])) {$_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);}
	$pieces = explode("\"", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("'", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode(" ", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("\n", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$pieces = explode("\r", $_SERVER['PHP_SELF']);	$_SERVER['PHP_SELF'] = $pieces[0];
	$_tx = substr($_SERVER['PHP_SELF'], -1, 1);
	IF ($_tx == '/') {$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, -1);}


# Figure out our location
	$separat			= '/coin_';

# build the file path
	$tempdocroot		= (substr(PHP_OS, 0, 3)=='WIN')?strtolower(getcwd()):getcwd();
	$_PACKAGE['DIR']	= str_replace("\\", '/', $tempdocroot);
	$data_array		= explode("$separat", $_PACKAGE['DIR']);
	$_PACKAGE['DIR']	= $data_array[0].'/';

# Include common session/paths setting file
	IF (is_readable($_PACKAGE['DIR'].'coin_includes/session_common.php')) {
		require($_PACKAGE['DIR'].'coin_includes/session_common.php');
	} ELSE {
		echo 'The required file <b>coin_includes/session_common.php</b> could not be located where it was expected at '.$_PACKAGE['DIR'].'coin_includes/session_common.php';
		exit();
	}



/**************************************************************
 * Error File Main Code
**************************************************************/
# Check $_GPV[err] and set default to list
	IF (!$_GPV['err'])				{$_GPV['err'] = '00';}
	IF (!is_numeric($_GPV['err']))	{$_GPV['err'] = '00';}
	switch($_GPV['err']) {
		case "00":
			$block_title	= 'Error: Package';
			$block_content	= 'A package error has occurred, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "01":
			$block_title	= 'Error: File Load';
			$block_content	= 'The file requested cannot be loaded directly by the browser, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "02":
			$block_title	= 'Error: Admin Control Panel Request';
			$block_content	= 'That admin control panel requested does not exist or is not readable, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "03":
			$block_title	= 'Error: Auxpage Request';
			$block_content	= 'That auxpage requested does not exist or is not readable, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "04":
			$block_title	= 'Error: Module Request';
			$block_content	= 'That module requested does not exist or is not readable, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "05":
			$block_title	= 'Error: Component Request';
			$block_content	= 'That package component requested has been disabled, redirecting accordingly.';
			$block_delay	= 5;
			$block_redirect	= 1;
			break;
		case "50":
			$block_title	= 'Error: Banned IP';
			$block_content	= 'Sorry, but the your IP Address: '.$_SERVER['REMOTE_ADDR'].' has been banned from this site. Contact the site for additional details.';
			$block_delay	= 5;
			$block_redirect	= 0;
			break;
		case "80":
			$block_title		= 'Error: php Version Insufficient';
			$block_content		= 'phpCOIN <i>requires</i> php version '.$_GPV['required'].' or higher but this web-site only has version '.phpversion();
			$block_content		.= '<br />Sorry, but phpCOIN must terminate.';
			$block_content		.= '<br /><br />After the web-site is upgraded to php v'.$_GPV['required'].' or higher, you can try again.';
			$block_redirect	= 0;
			break;
		case "81":
			$block_title		= 'Error: mbstring Functions Required';
			$block_content		= 'phpCOIN <i>requires</i> the php mbstring extensions in order to support UTF8 and other multi-byte character sets, but this web-site does not have the extensions enabled.';
			$block_content		.= '<br />Sorry, but phpCOIN must terminate.';
			$block_content		.= '<br /><br />After the mbstring extensions are enabled, you can try again.';
			$block_redirect	= 0;
			break;
		case "97":
			$block_title	= 'Error: License Violation';
			$block_content	= 'The required '.PKG_PATH_BASE.'coin_docs/license.txt file could not be located on the server. You cannot run phpCOIN without it.';
			$block_delay	= 5;
			$block_redirect	= 0;
			break;
		case "98":
			$block_title	= 'Error: License Violation';
			$block_content	= 'The required metatag "generator" set to "phpcoin" could not be located in the output. You cannot run phpCOIN without it.';
			$block_delay	= 5;
			$block_redirect	= 0;
			break;
		case "99":
			$block_title	= 'Error: License Violation';
			$block_content	= 'The required "Powered By phpCOIN" statement could not be located in the output. You cannot run phpCOIN without it.';
			$block_delay	= 5;
			$block_redirect	= 0;
			break;
		default:
			$_GPV['err']	= '00';
			$block_title	= 'Error: Package';
			$block_content	= 'A package error has occurred, redirecting accordingly.';
			$block_delay	= 5;
			break;
	}

# Call output block function
	error_block($block_title, $block_content, $block_delay, $block_redirect);


/**************************************************************
 * Error File Functions Code
**************************************************************/
# For php < 4.3 compatability, replaces html_entity_decode
function unhtmlentities($string) {
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}

function error_block($block_title, $block_content, $block_delay=5, $block_redirect=1) {
	global $_CCFG, $_GPV, $_nl, $_sp;

	# Build Table Start and title
		$_out .= '<html>'.$_nl;
		$_out .= '<head>'.$_nl;
		$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
		$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
		IF ($block_redirect == 1) {
			IF (!isset($_GPV['url'])) {$_GPV['url'] = 'index.php';}
			$_out .= '<meta http-equiv="refresh" content="'.$block_delay.';URL='.BASE_HREF.$_GPV['url'].'">'.$_nl;
		}
		$_out .= '<title>'.'Package Error'.'</title>'.$_nl;

		$_out .= '<style media="screen" type="text/css">'.$_nl;
		$_out .= '<!--'.$_nl;
		$_out .= 'body				{ background-color: #FFFFFF; margin: 5px }'.$_nl;
		$_out .= 'p					{ color: #001; font-family: Verdana, Arial, Helvetica, Geneva }'.$_nl;
		$_out .= '.BLK_DEF_TITLE	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #EBEBEB }'.$_nl;
		$_out .= '.BLK_DEF_ENTRY	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #F5F5F5 }'.$_nl;
		$_out .= '.BLK_IT_TITLE		{ color: #001; font-style: normal; font-weight: bold; text-align: left; font-size: 12px; padding: 5px; height: 25px }'.$_nl;
		$_out .= '.BLK_IT_ENTRY		{ color: #001; font-style: normal; font-weight: normal; text-align: justify; font-size: 11px; padding: 5px }'.$_nl;
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