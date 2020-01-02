<?php
/**
 * Installation: Library
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
	IF (eregi('setup_lib.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Function:	error_block($block_title, $block_content)
 * Arguments:	$block_title	- Error block title
 *				$block_content	- Error block message
 * Returns:		none
 * Description:	Error block on file validation
 * Notes:
 *	-
**************************************************************/
function error_block($block_title, $block_content)
	{
		global $_CCFG, $_GPV, $F_INSTALL, $F_UPGRADE, $_nl, $_sp;

		# Build Table Start and title
			$_out .= '<html>'.$_nl;
			$_out .= '<head>'.$_nl;
			$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
			$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
			IF ( $F_INSTALL == 1 ) { $_out .= '<title>phpCOIN Installation Error</title>'.$_nl; }
			IF ( $F_UPGRADE == 1 ) { $_out .= '<title>phpCOIN Upgrade Error</title>'.$_nl; }

			$_out .= '<style media="screen" type="text/css">'.$_nl;
			$_out .= '<!--'.$_nl;
			$_out .= 'body				{ background-color: #FFFFFF; margin: 5px }'.$_nl;
			$_out .= 'p					{ color: #001; font-family: Verdana, Arial, Helvetica, Geneva }'.$_nl;
			$_out .= '.BLK_DEF_TITLE	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #EBEBEB }'.$_nl;
			$_out .= '.BLK_DEF_ENTRY	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #F5F5F5 }'.$_nl;
			$_out .= '.BLK_IT_TITLE		{ color: #001; font-style: normal; font-weight: bold; text-align: left; font-size: 12px; padding: 5px; height: 25px }'.$_nl;
			$_out .= '.BLK_IT_ENTRY		{ color: #001; font-style: normal; font-weight: normal; text-align: justify; font-size: 11px; padding: 5px }'.$_nl;
			$_out .= '.BLK_IT_FMENU		{ color: #001; font-style: normal; font-weight: normal; text-align: center; font-size: 11px; padding: 5px }'.$_nl;
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
			$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_FMENU">';
			$_out .= '<a href="setup.php">Try Again</a>';
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

/**************************************************************
 * Function:	do_password_crypt ($apwrd_input)
 * Arguments:	$apwrd_input	- password string to encrypt
 * Returns:		encrypted password string
 * Description:	Function for encrypt passed string
 * Notes:
 *	-
**************************************************************/
function do_password_crypt ( $apwrd_input )
	{
		# Generate encrypted password
			return crypt($apwrd_input);
	}

/**************************************************************
 * Function:	do_mod_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
 * Function:	do_install_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
 * Arguments:	$atitle_text		- Block Title test
 *				$acontent_text		- Block Content
 *				$ado_menu_flag		- Bottom Row Menu Flag
 *				$abot_row_menu_text	- Bottom row text
 *				$aret_flag			- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build content html block for passed data
 * Notes:
 *	- Uses _WIDTH_CONTENT_AREA var for setting width
**************************************************************/
# Do html for mod content block (needed for db_api errors)
	function do_mod_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
		$_out .= '<tr bgcolor="black"><td bgcolor="black">'.$_nl;
		$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
 		$_out .= $atitle_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" colspan="2">'.$_nl;
 		$_out .= $acontent_text.$_nl;
		$_out .= '</td></tr>'.$_nl;

		IF ( $ado_menu_flag )
		{
			$_out .= '<tr class="BLK_DEF_FMENU"><td class="BLK_IT_FMENU" align="center" valign="top" colspan="2">'.$_nl;
			$_out .= $abot_row_menu_text.$_nl;
			$_out .= '</td></tr>'.$_nl;
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}

# Do html for standard content block
	function do_install_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
		$_out .= '<tr bgcolor="black"><td bgcolor="black">'.$_nl;
		$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
 		$_out .= $atitle_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" colspan="2">'.$_nl;
 		$_out .= $acontent_text.$_nl;
		$_out .= '</td></tr>'.$_nl;

		IF ( $ado_menu_flag )
		{
			$_out .= '<tr class="BLK_DEF_FMENU"><td class="BLK_IT_FMENU" align="center" valign="top" colspan="2">'.$_nl;
			$_out .= $abot_row_menu_text.$_nl;
			$_out .= '</td></tr>'.$_nl;
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_title_block_it ($atitle_text, $aret_flag=0)
 * Arguments:	$atitle_text	- Block Text
 *				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build module subject block for passed data
 * Notes:
 *	- Uses _WIDTH_CONTENT_AREA var for setting width
**************************************************************/
# Do html for title content block
	function do_install_title_block_it ($atitle_text, $aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
		$_out .= '<tr bgcolor="black"><td bgcolor="black">'.$_nl;
		$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
 		$_out .= $atitle_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_header($acomp_ptitle='phpCOIN', $aret_flag=0)
 * Arguments:	$acomp_ptitle	- Page title
 *				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "header"
 * Notes:
 *	- Opens initial system table and ready for first row (top_row)
**************************************************************/
	function do_install_page_header($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;
		global $F_INSTALL, $F_UPGRADE;

		$_out .= '<html>'.$_nl;
		$_out .= '<head>'.$_nl;
		$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
		$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
			IF ( $F_INSTALL == 1 ) { $_out .= '<title>phpCOIN Installation</title>'.$_nl; }
			IF ( $F_UPGRADE == 1 ) { $_out .= '<title>phpCOIN Upgrade</title>'.$_nl; }
		$_out .= '<link href="'.$_CCFG[_PKG_URL_THEME].'styles.css" rel="styleSheet" type="text/css">'.$_nl;
		$_out .= '</head>'.$_nl;
		$_out .= '<body bgcolor="#00AFAF" link="#0000FF" vlink="#FF0000">'.$_nl;
		$_out .= '<div align="center" width="100%">'.$_nl;
		$_out .= '<!-- Outer Table- 1 Col- span 2-3 -->'.$_nl;
		$_out .= '<table border="0" bordercolor="black" cellpadding="0" cellspacing="0" width="600px">'.$_nl;
		$_out .= '<tr><td valign="top">'.$_nl;
		$_out .= '<!-- Inner Table- 2/3 Col add rules=none here -->'.$_nl;
		$_out .= '<table border="0" bordercolor="black" cellpadding="0" cellspacing="5" width="100%" rules="none">'.$_nl;
		$_out .= '<!-- End page_header -->'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_top_row($aret_flag=0)
 * Arguments:	$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "top row"
 * Notes:
 *	-
**************************************************************/
	function do_install_page_top_row ($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '<tr height="40"><td colspan="2">'.$_nl;
		$_out .= do_install_page_top_block('1');
		$_out .= '</td></tr>'.$_nl;

		$_out .= '<!-- Start Content Column -->'.$_nl;
		$_out .= '<tr>'.$_nl;
		$_out .= '<td valign="top" align="center" width="100%">'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_top_block($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "top block"
 * Notes:
 *	-
**************************************************************/
	function do_install_page_top_block($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;
		global $F_INSTALL, $F_UPGRADE;

		# Build Top Of Page Title Block
			$_out .= '<!-- Start topblock -->'.$_nl;
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="black">'.$_nl;
			$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">'.$_nl;
			$_out .= '<tr class="BLK_HDR_TITLE" height="40px"><td class="TP3LRG_BL">'.$_nl;
				IF ( $F_INSTALL == 1 ) { $_out .= 'phpCOIN Installation Program'.$_nl; }
				IF ( $F_UPGRADE == 1 ) { $_out .= 'phpCOIN Upgrade Program'.$_nl; }
			$_out .= '</td></tr>'.$_nl;
			$_out .= '</table>'.$_nl;
			$_out .= '</td></tr></table>'.$_nl;
			$_out .= '<!-- End topblock -->'.$_nl;

			IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}

/**************************************************************
 * Function:	do_install_page_footer_block($aret_flag=0)
 * Arguments:	$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "footer"
 * Notes:
 *	-
**************************************************************/
	function do_install_page_footer_block($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '</td>'.$_nl;
		$_out .= '</tr>'.$_nl;
		$_out .= '<!-- End Content Area : End Row 2 -->'.$_nl;

		$_out .= '<!-- Start Footer Row -->'.$_nl;
		$_out .= '<tr height="20"><td valign="middle" colspan="2">'.$_nl;
		$_out .= '<div align="center" valign="middle">'.$_nl;

		$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>'.$_nl;
		$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_FTR_CLEAR_C" valign="middle">'.$_nl;

		$_out .= $_CCFG[_PKG_FOOTER_LINE_01].$_nl;
			IF ( $_CCFG[_PKG_FOOTER_LINE_01] && $_CCFG[_PKG_FOOTER_LINE_02] ) {	$_out .= '<br>'; }
		$_out .= $_CCFG[_PKG_FOOTER_LINE_02].$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;

		$_out .= '</div>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<!-- End Footer Row -->'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_closeout($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for final page closeout
 * Notes:
 *	-
**************************************************************/
	function do_install_page_closeout($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		$_out .= '<!-- Close Out Inner/Outer Table and Page Tags -->'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '</body>'.$_nl;
		$_out .= '</html>'.$_nl;

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_open($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build page html from starting tag
 *				to opening column for start of content.
 * Notes:
 *	-
**************************************************************/
	function do_install_page_open($aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		# Call page header function-
			$_out .= do_install_page_header('1');

		# Call page top row function-
			$_out .= do_install_page_top_row('1');

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Function:	do_install_page_close($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build page html from closeout of
 *				column for content to final page tag.
 * Notes:
 *	-
**************************************************************/
	function do_install_page_close($acompdata, $aret_flag=0)
	{
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_sp;

		# Call footer block function- does copyright and tag close out
			$_out .= do_install_page_footer_block('1');

		# Call page closeout function- does page tag close outs
			$_out .= do_install_page_closeout('1');

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
	}


/**************************************************************
 * Create some additional required functions
**************************************************************/
# Return current unix timestamp with offset:
function dt_get_uts ( )
	{
		global $_CCFG;
		return time()+($_CCFG[_PKG_DATE_SERVER_OFFSET]*3600);
	}

# Make unix timestamp from passed date array:
function dt_make_uts ( $_dt )
	{
		global $_CCFG;
		return  mktime( $_dt[hour],$_dt[minute],$_dt[second],$_dt[month],$_dt[day],$_dt[year]);
	}

# Make unix timestamp from passed date string (mySQL stored yyyy-mm-dd hh:mm:ss):
function dt_make_uts_from_string ( $_dt )
	{
		global $_CCFG;
		$dt[year]	= substr($_dt,0,4);
		$dt[month]	= substr($_dt,5,2);
		$dt[day]	= substr($_dt,8,2);
		$dt[hour]	= substr($_dt,11,2);
		$dt[minute]	= substr($_dt,14,2);
		$dt[second]	= substr($_dt,17,2);
		return  mktime( $dt[hour],$dt[minute],$dt[second],$dt[month],$dt[day],$dt[year]);
	}

# Return current formatted datetime string based on unix timestamp and format passed (uses date() function):
function dt_get_datetime ( $_format='Y-m-d H:i:s' )
	{
		# Format examples:
		#	long	- $_format='l- F d, Y @ h:i:s a T'
		#	short	- $_format='Y-m-d H:i:s'

		global $_CCFG;
		$_uts = time()+($_CCFG[_PKG_DATE_SERVER_OFFSET]*3600);
		return date($_format, $_uts);
	}

# Make formatted datetime string based on unix timestamp and format passed (uses date() function):
function dt_make_datetime ( $_uts=0, $_format='Y-m-d H:i:s' )
	{
		# Format examples:
		#	long	- $_format='l- F d, Y @ h:i:s a T'
		#	short	- $_format='Y-m-d H:i:s'

		return date($_format, $_uts);
	}

# Make datetime array from passed unix timestamp :
function dt_make_datetime_array ( $_uts )
	{
		$_dt = dt_make_datetime ( $_uts, 'Y-m-d H:i:s' );
		$dt[year]	= substr($_dt,0,4);
		$dt[month]	= substr($_dt,5,2);
		$dt[day]	= substr($_dt,8,2);
		$dt[hour]	= substr($_dt,11,2);
		$dt[minute]	= substr($_dt,14,2);
		$dt[second]	= substr($_dt,17,2);
		return  $dt;
	}

?>