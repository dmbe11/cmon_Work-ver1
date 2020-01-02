<?php
/**
 * Theme: EarthTone (Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Theme
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @designer Mike Lansberry
 */


# Code to handle file being loaded by URL
	IF ( eregi('core.php', $_SERVER['PHP_SELF']) ) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}


/**************************************************************
 * Function:	do_nav_link ($alink, $alink_obj)
 * Arguments:	$alink			- Hyperlink URL
 *				$alink_obj		- Hyperlink image
 *              $alink_obj_mo   - Hyperlink "mouseover" image
 *              $alink_target   - same window, new window, etc.
				$aid            - name of hyperlink, for "mouseover" effects
 * Returns:		returns link with object
 * Description:	Function to build nav link html block for passed data
 * Notes:
 *
**************************************************************/
# New- Do html for standard nav link
function do_nav_link($alink, $alink_obj, $alink_obj_mo, $alink_target='',$aid='') {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Return nothing if the module is disabled
		$FoundHidden = 0;
		IF (!$_CCFG['DOMAINS_ENABLE']) {
			$FoundHidden = strpos($alink_obj, strtolower("_domain"));
			IF ($FoundHidden != false) {return "";}
		}
		IF (!$_CCFG['ORDERS_ENABLE']) {
			$FoundHidden = strpos($alink_obj, strtolower("_order"));
			IF ($FoundHidden != false) {return "";}
		}
		IF (!$_CCFG['INVOICES_ENABLE']) {
			$FoundHidden = strpos($alink_obj, strtolower("_invoice"));
			IF ($FoundHidden != false) {return "";}
		}
		IF (!$_CCFG['HELPDESK_ENABLE']) {
			$FoundHidden = strpos($alink_obj, strtolower("_helpdesk"));
			IF ($FoundHidden != false) {return "";}
		}

	# Else search in object for known string to insert object name
		$_str_search	= ' border=';
		$_str_replace	= ' name="'.$aid.'" border=';
		$alink_obj	= str_replace( $_str_search, $_str_replace, $alink_obj );

	# And build hyperlink
		$_out  = '<a href="'.$alink.'"';
		IF ($alink_target == '_blank' || $alink_target == '_new' || $alink_target == '_self' || $alink_target == '_parent') {
			$_out .= ' target="'.$alink_target.'"';
		}
	# Do we use rollovers?
		IF ($_TCFG['_USE_ROLLOVER_IMAGES']) {
		    $_out .= ' onMouseOver="MM_swapImage(\''.$aid.'\',\'\',\''.$alink_obj_mo.'\',\'0\');"';
			$_out .= ' onMouseOut="MM_swapImgRestore();"';
		}

	# Complete hyperlink
		$_out .= '>';
		$_out .= $alink_obj;
		$_out .= '</a>'.$_nl;

	# And return it
		return $_out;
}

/**************************************************************
 * Function:	do_input_button_class_sw ($aid, $atype, $avalue, $amover_class, $amout_class)
 * Arguments:	$aid			- Input button id
 *				$atype			- Input button type
 *				$avalue			- Input button value
 *				$amover_class	- Input button mouseover class
 *				$amout_class	- Input button initial / mouseout class
 *				$amo_enable		- Input button initial / mouseout class
 * Returns:		returns javascript string for class switch.
 * Description:	Function to build javascript string for class switch.
 * Notes:
 *
**************************************************************/
# New- Do html for input button with jscript class switch
function do_input_button_class_sw ($aid, $atype, $avalue, $amover_class, $amout_class, $amo_enable='1') {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	IF ($_TCFG['_ENABLE_BTN_MOUSEOVER'] == '1') {
		$_out  = '<INPUT name="'.$aid.'" id="'.$aid.'" class="'.$amout_class.'" TYPE="'.$atype.'" value="'.$avalue.'"';
		$_out .= ' onMouseOver="setClassName(\''.$aid.'\',\''.$amover_class.'\');"';
		$_out .= ' onMouseOut="setClassName(\''.$aid.'\',\''.$amout_class.'\');"';
		$_out .= '>';
	} ELSE {
		$_out  = '<INPUT name="'.$aid.'" class="'.$amout_class.'" TYPE="'.$atype.'" value="'.$avalue.'">';
	}
	return $_out;
}


/**************************************************************
 * Function:	do_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
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
# Do html for standard content block
function do_block_it($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out  = '<table width="'.$_TCFG['_WIDTH_CONTENT_AREA'].'" cellpadding="0" cellspacing="0" border="0">'.$_nl;
	$_out .= '<tr bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'"><td bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
	$_out .= $atitle_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" colspan="2">'.$_nl;
	$_out .= $acontent_text.$_nl;
	$_out .= '</td></tr>'.$_nl;

	IF ($ado_menu_flag) {
		$_out .= '<tr class="BLK_DEF_FMENU"><td class="BLK_IT_FMENU" align="center" valign="top" colspan="2">'.$_nl;
		$_out .= $abot_row_menu_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
	}

	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_mod_block_it ($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0)
 * Arguments:	$atitle_text		- Block Title test
 *				$acontent_text		- Block Content
 *				$ado_menu_flag		- Bottom Row Menu Flag
 *				$abot_row_menu_text	- Bottom row text
 *				$aret_flag			- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build module html block for passed data
 * Notes:
 *	- Uses _WIDTH_MODULE_AREA var for setting width
**************************************************************/
# Do html for module content block
function do_mod_block_it($atitle_text, $acontent_text, $ado_menu_flag=0, $abot_row_menu_text='', $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out  = '<table width="'.$_TCFG['_WIDTH_MODULE_AREA'].'" cellpadding="0" cellspacing="0" border="0">'.$_nl;
	$_out .= '<tr bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'"><td bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
	$_out .= $atitle_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" colspan="2">'.$_nl;
	$_out .= $acontent_text.$_nl;
	$_out .= '</td></tr>'.$_nl;

	IF ($ado_menu_flag) {
		$_out .= '<tr class="BLK_DEF_FMENU"><td class="BLK_IT_FMENU" align="center" valign="top" colspan="2">'.$_nl;
		$_out .= $abot_row_menu_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
	}

	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_subj_block_it ($atitle_text, $aret_flag=0)
 * Arguments:	$atitle_text	- Block Text
 *				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build module subject block for passed data
 * Notes:
 *	- Uses _WIDTH_MODULE_AREA var for setting width
**************************************************************/
# Do html for module content block
function do_subj_block_it($atitle_text, $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out  = '<table width="'.$_TCFG['_WIDTH_MODULE_AREA'].'" cellpadding="0" cellspacing="0" border="0">'.$_nl;
	$_out .= '<tr bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'"><td bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
	$_out .= $atitle_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
}


/**************************************************************
 * Function:	do_page_header($acomp_ptitle='phpCOIN', $aret_flag=0)
 * Arguments:	$acomp_ptitle	- Page title
 *				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "header"
 * Notes:
 *	- Opens initial system table and ready for first row (top_row)
**************************************************************/
function do_page_header($acomp_ptitle='phpCOIN', $aret_flag=0) {
	global $_GPV, $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_PACKAGE, $_GPV, $_nl, $_sp;

	$newTitle = str_replace(' ', '_', $acomp_ptitle);
	$newTitle = str_replace(strtolower('phpcoin:_'), '', $newTitle);

	$_out  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.$_nl;
	$_out .= '<html>'.$_nl;
	$_out .= '<head>'.$_nl;
	$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
	IF (!$_CCFG['USE_FULL_URL']) {$_out .= '<base href="'.BASE_HREF.'">'.$_nl;}
	IF (isset($_LANG['META_DESCRIPTION'][$newTitle])) {
		$_out .= '<meta name="description" content="'.$_LANG['META_DESCRIPTION'][$newTitle].'">'.$_nl;
	}
	IF (isset($_LANG['META_KEYWORDS'][$newTitle])) {
		$_out .= '<meta name="keywords" content="'.$_LANG['META_KEYWORDS'][$newTitle].'">'.$_nl;
	}
	$_out .= '<meta name="MSSmartTagsPreventParsing" content="TRUE">'.$_nl;
	$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
	$_out .= '<title>'.$acomp_ptitle.'</title>'.$_nl;

	IF ($_CCFG['GOOGLE_WEBMASTER']) {
		$_out .= '<meta name="verify-v1" content="'.$_CCFG['GOOGLE_WEBMASTER'].'" >';
	}

	IF ($_CCFG['_IS_PRINT'] != 1) {
		$_out .= '<link href="'.$_CCFG['_PKG_URL_THEME'].'styles.css" rel="styleSheet" type="text/css">'.$_nl;
		$_out .= '<script type="text/javascript" src="'.$_CCFG['_PKG_URL_THEME'].'jscript.js"></script>'.$_nl;

	# Add WYSIWYG (if found) to output
		IF ($_CCFG['WYSIWYG_OPEN']) {$_out .= $_CCFG['WYSIWYG_OPEN'];}

		$_out .= '</head>'.$_nl;
		$_out .= '<body bgcolor="'.$_TCFG['_TAG_BODY_BACK_COLOR'].'" link="'.$_TCFG['_TAG_BODY_LINK_COLOR'].'" vlink="'.$_TCFG['_TAG_BODY_VLINK_COLOR'].'">'.$_nl;
	} ELSE {
		$_out .= '<link href="'.$_CCFG['_PKG_URL_THEME'].'styles_print.css" rel="styleSheet" type="text/css">'.$_nl;
		$_out .= '<script type="text/javascript" src="'.$_CCFG['_PKG_URL_THEME'].'jscript.js"></script>'.$_nl;
		$_out .= '</head>'.$_nl;
		$_out .= '<body bgcolor="#FFFFFF" link="'.$_TCFG['_TAG_BODY_LINK_COLOR'].'" vlink="'.$_TCFG['_TAG_BODY_VLINK_COLOR'].'">'.$_nl;
	}

	$_out .= '<a name="top"></a>'.$_nl;

	# Start: Mods for embedding into a table (phpcoin in second row, right column)
	IF ($_TCFG['_PKG_WRAPPER_ENABLE'] == 1 && $_CCFG['_IS_PRINT'] != 1) {
		$_fr = is_readable($_CCFG['_PKG_PATH_THEME'].'html/wrapper_top.inc' );
		IF ($_fr) {
			$_wrapper_top = file($_CCFG['_PKG_PATH_THEME'].'html/wrapper_top.inc');

		# Loop array and load
			FOR ($i = 0; $i < count($_wrapper_top); $i++) {$_wrapper_out_str .= $_wrapper_top[$i];}

		# Eval and output
			$string = addslashes($_wrapper_out_str);
			eval("\$string = \"$string\";");
			$string = stripslashes($string);
			$_out .= $string;
		} ELSE {

		# Default Hardcoded- you see this and file not readable or error.
			$_out .= '<div align="center" width="100%">'.$_nl;
			$_out .= '<table border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" cellpadding="0" cellspacing="0" width="100%">'.$_nl;
			$_out .= '<tr><td valign="top">'.$_nl;
			$_out .= '<table border="1" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
			$_out .= '<tr height="25px"><td valign="top" align="center" colspan="2">'.$_nl;
			$_out .= '<br>HardCoded Header Row for whatever<br><br>'.$_nl;
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr><td valign="top" align="center" width="15%">'.$_nl;
			$_out .= 'Left Column'.$_nl;
			$_out .= '</td><td valign="top">'.$_nl;
		}
	}
	# End: Mods for embedding into a table (phpcoin in second row, right column)

	$_out .= '<div align="center" width="100%">'.$_nl;
	$_out .= '<!-- Outer Table- 1 Col- span 2-3 -->'.$_nl;

	IF ($_CCFG['_IS_PRINT'] != 1) {
		$_out .= '<table border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" cellpadding="0" cellspacing="0" width="'.$_TCFG['_WIDTH_OUTER_TABLE'].'">'.$_nl;
	} ELSE {
		$_out .= '<table border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" cellpadding="0" cellspacing="0" width="'.$_TCFG['_WIDTH_PRINT_AREA'].'">'.$_nl;
	}

	$_out .= '<tr><td valign="top">'.$_nl;
	$_out .= '<!-- Inner Table- 2/3 Col do not add rules=none here or mozilla mac shows no cell spacing -->'.$_nl;
	$_out .= '<table border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" cellpadding="0" cellspacing="5" width="100%">'.$_nl;
	$_out .= '<!-- End page_header -->'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_top_row($acomp_col_num=2, $aret_flag=0)
 * Arguments:	$acomp_col_num	- Column number (2/3)
 *				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "top row"
 * Notes:
 *	-
**************************************************************/
function do_page_top_row($acomp_col_num=2, $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out = '';
	IF (!$_TCFG['_DISABLE_HEADER_BLK']) {
		$_out .= '<tr height="40"><td colspan="'.$acomp_col_num.'">'.$_nl;
		$_out .= do_page_top_block('1');
		$_out .= '</td></tr>'.$_nl;
	}
	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_top_block($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "top block"
 * Notes:
 *	-
**************************************************************/
function do_page_top_block($aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build Top Of Page Title Block
		$_out = '<!-- Start topblock -->'.$_nl;

	# Check option for clear table, or as top (fill w/ border)
		IF ($_TCFG['_PAGE_HEADER_CLEAR']) {
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>'.$_nl;
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_HDR_TCLEAR_C" valign="middle">'.$_nl;
		} ELSE {
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="black">'.$_nl;
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_HDR_TITLE" valign="middle">'.$_nl;
		}

		$_out .= '<table width="100%"><tr class="BLK_HDR_TITLE_C">'.$_nl;
		$_out .= '<td class="TP3LRG_BL" valign="top">'.$_nl;

	# Logo / Img File URLS (larger ones)
		$_LOGO_PREFIX			= '<img src="'.$_CCFG['_PKG_URL_THEME'].'images/';
		$_LOGO_SUFFIX			= '" border="0" align="middle">';
		$_TCFG['_IMG_LOGO_L']	= $_LOGO_PREFIX.$_TCFG['_PAGE_HEADER_LOGO_FILE'].$_LOGO_SUFFIX;

		IF ($_TCFG['_PAGE_HEADER_LOGO']) {
			$_out .= $_TCFG['_IMG_LOGO_L'].$_nl;
		} ELSE {
			$_out .= $_CCFG['_PKG_TOP_GREETING'].$_nl;
		}

	# Add "edit parameters" button
		IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
			$_out .= ' <a href="admin.php?cp=parms&op=edit&fpg=layout&fpgs=package">'.$_TCFG['_S_IMG_PM_S'].'</a>';
		}

		$_out .= '</td>'.$_nl;

		IF ($_TCFG['_PAGE_HEADER_DATE']) {
			$_out .= '<td class="TP3SML_BR" valign="bottom">'.$_nl;
			$_out .= '<b>'.dt_display_datetime(0, $_CCFG['_PKG_DATE_FORMAT_HEADER']).'</b>'.$_nl;
			$_out .= '</td>'.$_nl;
		}
		$_out .= '</tr></table>'.$_nl;

	# Check option for clear table, or as top (fill w/ border)
		IF ($_TCFG['_PAGE_HEADER_CLEAR']) {
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr><td class="BLK_HDR_MCLEAR">'.$_nl;
		} ELSE {
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr><td class="BLK_HDR_MENU">'.$_nl;
		}

	# Call menu
		$_out .= do_page_top_block_menu('1');

	# Close out block
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '<!-- End topblock -->'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_top_block_menu($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page top block
 *				Menu
 * Notes:
 *	-
**************************************************************/
function do_page_top_block_menu($aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out = '';
	# Check option for clear table, or as top (fill w/ border)
		IF ($_TCFG['_PAGE_HEADER_CLEAR']) {
			$_out .= '<table width="100%"><tr>'.$_nl;
			$_out .= '<td class="TP3MED_BL">'.$_nl;
		} ELSE {
			$_out .= '<table width="100%"><tr>'.$_nl;
			$_out .= '<td class="TP3MED_BL">'.$_nl;
		}

	# Build Menu for header block
		IF ($_TCFG['_HDR_MENU_BTTN_01'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_01'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_01']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_01'].'_MO'],'','MB01');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_02'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_02'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_02']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_02'].'_MO'],'','MB02');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_03'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_03'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_03']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_03'].'_MO'],'','MB03');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_04'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_04'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_04']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_04'].'_MO'],'','MB04');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_05'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_05'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_05']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_05'].'_MO'],'','MB05');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_06'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_06'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_06']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_06'].'_MO'],'','MB06');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_07'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_07'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_07']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_07'].'_MO'],'','MB07');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_08'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_08'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_08']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_08'].'_MO'],'','MB08');
		}
		IF ($_TCFG['_HDR_MENU_BTTN_09'] == 1) {
			$_out .= do_nav_link($_TCFG['_HDR_MENU_BTTN_LINK_09'], $_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_09']],$_TCFG[$_TCFG['_HDR_MENU_BTTN_IMG_09'].'_MO'],'','MB09');
		}

	# Add "edit parameters" button
		IF ($_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
			$_out .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=buttons">'.$_TCFG['_S_IMG_PM_S'].'</a>';
		}

	# Check option for clear table, or as top (fill w/ border)
		IF ($_TCFG['_PAGE_HEADER_CLEAR']) {
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BR">'.$_nl;
		} ELSE {
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BR">'.$_nl;
		}

	# Build Menu for header block
		// If menubox disabled OR columns disabled, show logon/logoff buttons
		IF ($_TCFG['_DISABLE_MENU_COLS'] || !$_CCFG['USE_LOGIN_MENUBOX']) {
			IF ($_SEC['_sadmin_flg']) {
				$_out .= do_nav_link('login.php?w=admin&o=logout', $_TCFG['_IMG_MT_LOGOUT_B'],$_TCFG['_IMG_MT_LOGOUT_B_MO'],'','L01');
			} ELSE IF ( $_SEC['_suser_flg']) {
				$_out .= do_nav_link('login.php?w=user&o=logout', $_TCFG['_IMG_MT_LOGOUT_B'],$_TCFG['_IMG_MT_LOGOUT_B_MO'],'','L01');
			} ELSE {
				$_out .= do_nav_link('login.php?w=user&o=login', $_TCFG['_IMG_MT_LOGIN_B'],$_TCFG['_IMG_MT_LOGIN_B_MO'],'','L01');
			}
		}
		$_out .= '</td>'.$_nl;

	# Call user / admin menu bar function-
		IF ($_TCFG['_ENABLE_MENU_USER_HDR'] == 1) {

		# If not user (client), or admin return empty
			IF ($_SEC['_suser_flg'] || $_SEC['_sadmin_flg']) {
				$_out .= '</tr>'.$_nl.'<tr>'.$_nl;
				$_out .= '<td class="TP0SML_BC" colspan="2">'.$_nl;
			# Call user / admin menu bar function-
				$_out .= do_user_menu('0','1');
				$_out .= '</td>'.$_nl;
			}
		}

		$_out .= '</tr></table>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_lcol_block($acomp_col_num=2, $aret_flag=0)
 * Arguments:	$acomp_col_num	- Column number (2/3)
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for "left" column
 * Notes:
 *	- Add first (left) column, start content column
 *	- Note- load comp data adjusts col_num also to stabilize.
**************************************************************/
function do_page_lcol_block($acomp_col_num=2, $aret_flag=0) {
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get menu count items from $_SYS array
		global $_SYS;
		$_do_left = $_SYS['_do_col_left'];
		$_do_right = $_SYS['_do_col_right'];

		$_out  = '<!-- Start Row 2 / Menu Left Column / Content Area -->'.$_nl;
		$_out .= '<tr>'.$_nl;

		IF ($_TCFG['_DISABLE_MENU_COLS'] == 1 || ($_do_left == 0 && $_do_right == 0)) {
			$temp_width 		= (100);
			$temp_colspan_str 	= ' colspan="'.$acomp_col_num.'"';

		} ELSEIF ($_do_left == 1 && $_do_right == 1) {
			IF ($acomp_col_num == 2) {
				$temp_width = (100-$_TCFG['_WIDTH_COL_BLOCK']);
			} ELSE {
				$temp_width = (100-(2*$_TCFG['_WIDTH_COL_BLOCK']));
			}

			$temp_colspan_str 	= '';
			$_out .= '<td width="'.$_TCFG['_WIDTH_COL_BLOCK'].'%" valign="top">'.$_nl;
			$_out .= '<!-- Start menu blocks -->'.$_nl;
			$_out .= do_menu_blocks('L', '1');
			$_out .= '<!-- Finish menu blocks -->'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<!-- End Menu Left Column / Start Content Area -->'.$_nl;

		} ELSEIF ($_do_left == 1 && $_do_right == 0) {
			$temp_width = (100-$_TCFG['_WIDTH_COL_BLOCK']);
			$_out .= '<td width="'.$_TCFG['_WIDTH_COL_BLOCK'].'%" valign="top">'.$_nl;
			$_out .= '<!-- Start menu blocks -->'.$_nl;
			$_out .= do_menu_blocks('L', '1');
			$_out .= '<!-- Finish menu blocks -->'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<!-- End Menu Left Column / Start Content Area -->'.$_nl;

		} ELSEIF ($_do_left == 0 && $_do_right == 1) {
			$temp_width = (100-$_TCFG['_WIDTH_COL_BLOCK']);
		}

	# Content cell start
		$_out .= '<td valign="top" align="center" width="'.$temp_width.'%"'.$temp_colspan_str.'>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_menu_block($ablock_title, $ablock_content, $aret_flag=0)
 * Arguments:	$ablock_title	- Menu block title
 *				$ablock_content	- Menu block content
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build a typical menu block
 * Notes:
 *	- Outputs passed menu block html
**************************************************************/
function do_menu_block ($ablock_title, $ablock_content, $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out  = '<!-- Start menublock -->'.$_nl;
	$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'.$_nl;
	$_out .= '<tr bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'"><td bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr><td class="BLK_MENU_TITLE" width="'.$_TCFG['_WIDTH_MENU_BLOCK'].'">'.$_nl;
	$_out .= $ablock_title.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr><td class="BLK_MENU_ENTRY" width="'.$_TCFG['_WIDTH_MENU_BLOCK'].'" valign="top">'.$_nl;
	$_out .= $ablock_content.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '<!-- Stop menublock -->'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_rcol_block($acomp_col_num=3, $aret_flag=0)
 * Arguments:	$acomp_col_num	- Current number of columns
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "right col"
 * Notes:
 *	- Add third column, after closing content column
 *	- Called from as final call in pages loaded (varies)
 *	- Note- load comp data adjusts col_num also to stabilize.
**************************************************************/
function do_page_rcol_block($acomp_col_num=3, $aret_flag=0) {
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get menu count items from $_SYS array
		global $_SYS;
		$_do_left = $_SYS['_do_col_left'];
		$_do_right = $_SYS['_do_col_right'];
		$_out = '';

		IF ($_TCFG['_DISABLE_MENU_COLS'] == 1 || $_do_right == 0) {
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
			$_out .= '<!-- End Content Area : End Row 2 -->'.$_nl;

		} ELSEIF ($_TCFG['_DISABLE_MENU_COLS'] == 0) {
			IF ($_do_right == 1 && ($acomp_col_num == 3 || $_do_left == 0)) {
				$_out .= '</td>'.$_nl;
				$_out .= '<!-- End Content Area : Start Right Column/Menu -->'.$_nl;
				$_out .= '<td width="'.$_TCFG['_WIDTH_COL_BLOCK'].'%" valign="top">'.$_nl;
				$_out .= '<!-- Start menu -->'.$_nl;
				$_out .= do_menu_blocks('R', '1');
				$_out .= '<!-- Finish menu -->'.$_nl;
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$_out .= '<!-- End Right Column/Menu : End Row 2 -->'.$_nl;

			} ELSE {
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$_out .= '<!-- End Content Area : End Row 2 -->'.$_nl;
			}
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_footer_block($acomp_col_num=2, $aret_flag=0)
 * Arguments:	$acomp_col_num	- Current number of columns
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for page "footer"
 * Notes:
 *	-
**************************************************************/
function do_page_footer_block($acomp_col_num=2, $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out = '';
	IF (!$_TCFG['_DISABLE_FOOTER_BLK']) {
		$_out .= '<!-- Start Footer Row -->'.$_nl;
		$_out .= '<tr><td valign="top" colspan="'.$acomp_col_num.'">'.$_nl;
		$_out .= '<div align="center" valign="middle">'.$_nl;

	# Check option for clear table, or as top (fill w/ border)
		IF ($_TCFG['_PAGE_FOOTER_CLEAR']) {
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>'.$_nl;
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_FTR_CLEAR_C" valign="middle">'.$_nl;
		} ELSE {
			$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="black">'.$_nl;
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_FTR_C" valign="middle">'.$_nl;
		}

		$_out .= $_CCFG['_PKG_FOOTER_LINE_01'].$_nl;
		IF ($_CCFG['_PKG_FOOTER_LINE_01'] && $_CCFG['_PKG_FOOTER_LINE_02']) {$_out .= '<br>';}
		$_out .= $_CCFG['_PKG_FOOTER_LINE_02'].$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;

		$_out .= '</div>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<!-- End Footer Row -->'.$_nl;
	}

	$_out .= '<tr><td colspan="'.$acomp_col_num.'" class="BLK_FTR_CLEAR_C" valign="middle">';
	IF ($_UVAR['DISPLAY_ON_FOOTER']) {
		$_out .= $_UVAR['CO_INFO_01_NAME'];
		IF ($_UVAR['CO_INFO_02_ADDR_01'])		{$_out .= ', '.$_UVAR['CO_INFO_02_ADDR_01'];}
		IF ($_UVAR['CO_INFO_03_ADDR_02'])		{$_out .= ', '.$_UVAR['CO_INFO_03_ADDR_02'];}
		IF ($_UVAR['CO_INFO_04_CITY'])		{$_out .= ', '.$_UVAR['CO_INFO_04_CITY'];}
		IF ($_UVAR['CO_INFO_05_STATE_PROV'])	{$_out .= ', '.$_UVAR['CO_INFO_05_STATE_PROV'];}
		IF ($_UVAR['CO_INFO_06_POSTAL_CODE'])	{$_out .= ', '.$_UVAR['CO_INFO_06_POSTAL_CODE'];}
		IF ($_UVAR['CO_INFO_07_COUNTRY'])		{$_out .= ', '.$_UVAR['CO_INFO_07_COUNTRY'];}
		$_out .= '<br>';
		IF ($_UVAR['CO_INFO_08_PHONE'])		{$_out .= $_LANG['_BASE']['LABEL_PHONE'].' '.$_UVAR['CO_INFO_08_PHONE'].'&nbsp;&nbsp;&nbsp;';}
		IF ($_UVAR['CO_INFO_09_FAX'])			{$_out .= $_LANG['_BASE']['LABEL_FAX'].' '.$_UVAR['CO_INFO_09_FAX'].'&nbsp;&nbsp;&nbsp;';}
		IF ($_UVAR['CO_INFO_11_TOLL_FREE'])	{$_out .= $_LANG['_BASE']['LABEL_TOLL_FREE'].' '.$_UVAR['CO_INFO_11_TOLL_FREE'];}
		$_out .= '<br>';
	}
	$_out .= str_replace('%WEBMASTER%', get_contact_email_by_name('webmaster'), $_CCFG['SITE_FOOTER_EMAIL_WEBMASTER']);
	$_out .= '</td></tr>';

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_powered_by($acomp_col_num=2, $aret_flag=0)
 * Arguments:	$acomp_col_num	- Current number of columns
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for Powered By statement
 * Notes:
 *	- License dictates this cannot be altered in any way.
**************************************************************/
function do_page_powered_by($acomp_col_num=2, $aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out = '';
	IF ($_CCFG['_IS_PRINT'] != 1) {
 		$_out .= '<!-- Start Powered By Row -->'.$_nl;
		$_out .= '<tr height="12px"><td class="TP0SML_NC" colspan="'.$acomp_col_num.'">'.$_nl;
		$_out .= 'Powered By <a href="http://www.phpcoin.com" target="_blank">phpCOIN</a>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<!-- End Powered By Row -->'.$_nl;
	}
	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_closeout($aret_flag=0)
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build html for final page closeout
 * Notes:
 *	-
**************************************************************/
function do_page_closeout($aret_flag=0) {
	global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	$_out  = '<!-- Close Out Inner/Outer Table and Page Tags -->'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	$_out .= '</div>'.$_nl;

	IF ($_CCFG['_IS_PRINT'] == 1) {
	# Print Date
		$_out .= '<div class="PSML_NC" align="center">'.$_LANG['_THEME']['Printed'].$_sp.dt_display_datetime ( 0, $_CCFG[_PKG_DATE_FORMAT_PRINT] ).'</div>'.$_nl;
	}

	# Start: Mods for embedding into a table (phpcoin in second row, right column)
	IF ($_TCFG['_PKG_WRAPPER_ENABLE'] == 1 && $_CCFG['_IS_PRINT'] != 1) {
		$_fr = is_readable($_CCFG['_PKG_PATH_THEME'].'html/wrapper_bot.inc');
		IF ($_fr) {
			$_wrapper_bot = file($_CCFG['_PKG_PATH_THEME'].'html/wrapper_bot.inc');

		# Loop array and load
		FOR ($i = 0; $i < count($_wrapper_bot); $i++) {$_wrapper_bot_str .= $_wrapper_bot[$i];}

		# Eval and output
			$string = addslashes($_wrapper_bot_str);
			eval("\$string = \"$string\";");
			$string = stripslashes($string);
			$_out .= $string;

		} ELSE {
		# Default Hardcoded- you see this and file not readable or error.
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr height="15px"><td valign="top" colspan="2" align="center">'.$_nl;
			$_out .= '<br>HardCoded Footer Row for whatever<br><br>'.$_nl;
			$_out .= '</td></tr>'.$_nl;
			$_out .= '</table>'.$_nl;
			$_out .= '</td></tr></table>'.$_nl;
			$_out .= '</div>'.$_nl;
		}
	}
	# End: Mods for embedding into a table (phpcoin in second row, right column)

	# Add WYSIWYG (if found) to output
	IF ($_CCFG['WYSIWYG_CLOSE']) {$_out .= $_CCFG['WYSIWYG_CLOSE'];}

	$_out .= '</body>'.$_nl;
	$_out .= '</html>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_open($acompdata, $aret_flag=0)
 * Arguments:	$acompdata	- Component Data Array
 * 				$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build page html from starting tag
 *				to opening column for start of content.
 * Notes:
 *	-
**************************************************************/
function do_page_open($acompdata, $aret_flag=0) {
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Call page header function-
		$_out  = do_page_header($acompdata['comp_ptitle'],1);

	# Call page top row function-
		$_out .= do_page_top_row($acompdata['comp_col_num'],1);

	# Call user / admin menu bar function-
		IF ($_TCFG['_ENABLE_MENU_USER_HROW'] == 1) {
			$_out .= do_user_menu($acompdata['comp_col_num'], 1);
		}

	# Call left col block function-
		$_out .= do_page_lcol_block($acompdata['comp_col_num'], 1);

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * Function:	do_page_close($acompdata, $aret_flag=0)
 * Arguments:	$acompdata	- Component Data Array
 * 				$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build page html from closeout of
 *				column for content to final page tag.
 * Notes:
 *	-
**************************************************************/
function do_page_close($acompdata, $aret_flag=0) {
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Call right col block function-
		$_out  = do_page_rcol_block($acompdata['comp_col_num'],1);

	# Call user / admin menu bar function-
		IF ($_TCFG['_ENABLE_MENU_USER_FROW'] == 1) {
			$_out .= do_user_menu($acompdata['comp_col_num'],1);
		}

	# Call footer block function- does copyright and tag close out
		$_out .= do_page_footer_block($acompdata['comp_col_num'],1);

	# Call powered by block function- does powered by statement
		$_out .= do_page_powered_by($acompdata['comp_col_num'],1);

	# Call debug info block function- dumps out num queries, etc.
		IF ($_CCFG['_debug_queries']) {
			$_out .= do_page_debug_info($acompdata['comp_col_num'],1);
		}

	# Call page closeout function- does page tag close outs
		$_out .= do_page_closeout('1');

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
}


/**************************************************************
 * Function:	do_user_menu($aret_flag=0)
 * Arguments:	$acomp_col_num	- Current number of columns
 * 				$aret_flag		- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build module subject block for passed data
 * Notes:
 *	-
**************************************************************/
# Do html for module content block
function do_user_menu($acomp_col_num=2, $aret_flag=0) {
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get security flags
		$_SEC = get_security_flags ( );

	$_out = '';
	# If not user (client), or admin return empty
		IF ( !$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
			$_out = '';
			IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
		} ELSE {
			$_out .= '<!-- Start Admin / User Menu -->'.$_nl;

		# Build page wide row / cell open
			IF ( $acomp_col_num != '0' ) {

			# Block open
				$_out .= '<tr><td colspan="'.$acomp_col_num.'">'.$_nl;
				$_out .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
				$_out .= '<tr bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'"><td bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'">'.$_nl;
				$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">'.$_nl;
				$_out .= '<tr class="BLK_HDR_MENU" valign="middle"><td class="TP3MED_NC">'.$_nl;
			} ELSE {

			# Block open
				$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">'.$_nl;
				$_out .= '<tr class="TP0MED_NC" valign="middle"><td class="TP1MED_NC">'.$_nl;
			}

		# Content ( 2-column table )
			$_out .= '<center>'.$_nl;
			$_out .= '<table width="100%"><tr>'.$_nl;
			$_out .= '<td class="TP0MED_NL">'.$_nl;

		# Build Menu for user block- left cell
			IF ( $_SEC['_sadmin_flg']) {
				$_out .= do_nav_link('mod.php?mod=clients', $_TCFG['_IMG_MU_CLIENTS_B'],$_TCFG['_IMG_MU_CLIENTS_B_MO'],'','UB01');
			} ELSE IF ( $_SEC['_suser_flg']) {
				$_out .= do_nav_link ('mod.php?mod=clients', $_TCFG['_IMG_MU_MY_ACCOUNT_B'],$_TCFG['_IMG_MU_MY_ACCOUNT_B_MO'],'','UB01');
			}

			IF ( $_TCFG['_USR_MENU_BTTN_02'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_02'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_02']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_02'].'_MO'],'','UB02'); }

			IF ( $_TCFG['_USR_MENU_BTTN_03'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_03'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_03']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_03'].'_MO'],'','UB03'); }

			IF ( $_TCFG['_USR_MENU_BTTN_04'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_04'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_04']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_04'].'_MO'],'','UB04'); }

			IF ( $_TCFG['_USR_MENU_BTTN_05'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_05'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_05']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_05'].'_MO'],'','UB05'); }

			IF ( $_TCFG['_USR_MENU_BTTN_06'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_06'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_06']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_06'].'_MO'],'','UB06'); }

			IF ( $_TCFG['_USR_MENU_BTTN_07'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_07'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_07']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_07'].'_MO'],'','UB07'); }

			IF ( $_TCFG['_USR_MENU_BTTN_08'] == 1 )
				{ $_out .= do_nav_link($_TCFG['_USR_MENU_BTTN_LINK_08'], $_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_08']],$_TCFG[$_TCFG['_USR_MENU_BTTN_IMG_08'].'_MO'],'','UB08'); }

		# End left cell, open right
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP0MED_NR">'.$_nl;

		# Build Menu for header block
			IF ( $_SEC['_sadmin_flg']) {
				$_out .= do_nav_link('mod.php?mod=mail&mode=client', $_TCFG['_IMG_MU_EMAIL_CLIENT_B'],$_TCFG['_IMG_MU_EMAIL_CLIENT_B_MO'],'','UB10');
				$_out .= do_nav_link('admin.php', $_TCFG['_IMG_MU_ADMIN_B'],$_TCFG['_IMG_MU_ADMIN_B_MO'],'','UB11');
			} ELSE IF ( $_SEC['_suser_flg']) { $_out .= $_sp; }

		# End right cell, row and table
			$_out .= '</td>'.$_nl;
			$_out .= '</tr></table>'.$_nl;
			$_out .= '</center>'.$_nl;
		# End content

		# End block and row
			IF ( $acomp_col_num != '0' ) {
				$_out .= '</td></tr>'.$_nl;
				$_out .= '</table>'.$_nl;
				$_out .= '</td></tr></table>'.$_nl;
				$_out .= '</td></tr>'.$_nl;
			} ELSE {
				$_out .= '</td></tr></table>'.$_nl;
			}

			$_out .= '<!-- End Admin / User Menu -->'.$_nl;
		}

		IF ( $aret_flag ) { return $_out; } ELSE { echo $_out; }
}
?>