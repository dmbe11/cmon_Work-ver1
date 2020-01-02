<?php
/**
 * Site Login/Logout
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Authentication
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_base.php
 * @arguments $w Who ('admin' or 'user')
 * @arguments $o Operation ('login' or 'logout')
 * @argumenst $e Error code (if any)
 */


# Include session file (loads core)
	require_once('coin_includes/session_set.php');


# Call Load Component parms
	$_comp_name	= 'login';
	$_comp_oper	= '';
	$compdata		= do_load_comp_data($_comp_name, $_comp_oper);


# Get security vars
	$_SEC = get_security_flags();

/*************************************************************/
# Check $w (who) & $o (operation) and set if empty
	switch($_GPV['w']) {
		case "user":
			break;
		case "admin":
			break;
		default:
			$_GPV['w'] = 'user';
			break;
	}
	switch($_GPV['o']) {
		case "login":
			break;
		case "logout":
			break;
		default:
			$_GPV['o'] = 'login';
			break;
	}

# Standard user login code
IF ($_GPV['w'] == 'user') {
	IF ($_GPV['o'] == 'login') {

	# Set data array
		$data['mod']	= $_GPV['mod'];
		$data['e']		= $_GPV['e'];

	# Check user logged in status
		IF (!$_SEC['_suser_flg']) {

	# Call function for login form
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= do_login($data, $_GPV['w'], '1').$_nl;

		} ELSE {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_BASE']['Welcome'].$_sp.$_suser_name.$_nl;

			$_cstr  = '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= $_LANG['_BASE']['Client_Login_Successful'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;

			$_mstr_flag = 1;
			$_mstr .= do_nav_link('login.php?w=user&o=logout', $_TCFG['_IMG_LOGOUT_M'],$_TCFG['_IMG_LOGOUT_M_MO'],'',$_LANG['_BASE']['B_Log_Out']);

		# Call block it function
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}
	}


	IF ($_GPV['o'] == 'logout' && !$_SEC['_suser_flg']) {
	# Set data array
		$data['mod']	= $_GPV['mod'];
		$data['e']		= $_GPV['e'];

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BASE']['Logout_Status'].$_nl;

		$_cstr  = '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_LANG['_BASE']['Logout_Successful'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 1;
		$_mstr .= do_nav_link('login.php?w=user&o=login', $_TCFG['_IMG_LOGIN_M'],$_TCFG['_IMG_LOGIN_M_MO'],'',$_LANG['_BASE']['B_Log_In']);

	# Call block it function
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;
	}
}

IF ($_GPV['w'] == 'admin') {
	IF ( $_GPV['o'] == 'login') {
	# Set data array
		$data['mod']	= $_GPV['mod'];
		$data['e']		= $_GPV['e'];

	# Check user logged in status
		IF (!$_SEC['_sadmin_flg']) {

		# Call function for login form
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= do_login($data, $_GPV['w'], '1').$_nl;

		} ELSE {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_BASE']['Welcome'].$_sp.$_sadmin_name.$_nl;

			$_cstr  = '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= $_LANG['_BASE']['Administrative_Login_Successful'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;

			$_mstr_flag	= 1;
			$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
			$_mstr .= do_nav_link('login.php?w=admin&o=logout', $_TCFG['_IMG_LOGOUT_M'],$_TCFG['_IMG_LOGOUT_M_MO'],'',$_LANG['_BASE']['B_Log_Out']);

		# Call block it function
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}
	}

	IF ($_GPV['o'] == 'logout' && !$_SEC['_sadmin_flg']) {
	# Set data array
		$data['mod']	= $_GPV['mod'];
		$data['e']		= $_GPV['e'];

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_BASE']['Logout_Status'].$_nl;

		$_cstr  = '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_LANG['_BASE']['Logout_Successful'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 1;
		$_mstr .= do_nav_link('login.php?w=admin&o=login', $_TCFG['_IMG_LOGIN_M'],$_TCFG['_IMG_LOGIN_M_MO'],'',$_LANG['_BASE']['B_Log_In']);

	# Call block it function
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;
	}
}

/*************************************************************/

# Call page open (start to content)
	do_page_open($compdata, '0');

# display content
	echo $_out;

# Call page open (content to finish)
	do_page_close($compdata, '0');

?>