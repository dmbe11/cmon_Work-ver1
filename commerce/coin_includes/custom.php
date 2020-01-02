<?php
/**
 * Loader: Custom Functions
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Custom_Functions
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('custom.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit;
	}


/**************************************************************
 *              Start Custom Module Functions
**************************************************************/
function do_yourcustom($adata, $aret_flag=0) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build your return / output
		$_out  = 'Generally set to the $_out variable'.$_nl;
		$_out .= ' to control the output on error handling.'.$_nl;

	# Either return output or echo here
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


function do_whois_online() {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Some text strings for language:
		$_Total_Visitors	= 'Total Visitors:';
		$_Guests			= 'Guests';
		$_Users			= 'Users';
		$_Admins			= 'Admins';

	# Set Query for select on total visitors
		$_count_visitors = 0; $_count_admins = 0; $_count_users = 0;
		$q  = 'SELECT s_is_admin, s_is_user';
		$q .= ' FROM '.$_DBCFG['sessions'];
		$r = $db_coin->db_query_execute($q);
		while(list($s_is_admin, $s_is_user) = $db_coin->db_fetch_row($r)) {
			$_count_visitors++;
			IF ($s_is_admin == 1)	{$_count_admins++;}
			IF ($s_is_user == 1)	{$_count_users++;}
		}

	# Build Output
		$_count_guests = ($_count_visitors - $_count_admins - $_count_users);
		$_out .= $_Total_Visitors.$_sp.$_count_visitors.$_nl;
		IF ($_count_guests > 0)	{$_out .= '<br>'.$_Guests.$_sp.'('.$_count_guests.')'.$_nl;}
		IF ($_count_users > 0)	{$_out .= '<br>'.$_Users.$_sp.'('.$_count_users.')'.$_nl;}
		IF ($_count_admins > 0)	{$_out .= '<br>'.$_Admins.$_sp.'('.$_count_admins.')'.$_nl;}

	# Return output
		return $_out;
}
?>