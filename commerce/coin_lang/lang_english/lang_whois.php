<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- whois lookups based loosely on mrwhois Copyright (C) 2001 Marek Rozanski, marek@mrscripts.co.uk
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage WHOIS
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_whois.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_WHOIS']['Text_Title']			= 'Domain Name Search';
		$_LANG['_WHOIS']['Option_All_Domains']		= 'All Domains';
		$_LANG['_WHOIS']['Text_Instructions_Short']	= '<b>Please enter a suggested domain name</b><br>(Selecting "all domains" may take several minutes before the results are displayed)<br>When you find an available name, click the relevant [Order] link to return to the order form.';
		$_LANG['_WHOIS']['Text_Instructions_Long']	= 'A domain name is one word, with no periods or spaces<br>(use hyphens instead), and <i>without</i> the leading www.<br>Enter the desired domain name in the space provided,<br>then select the desired extension to check.<br>Selecting "all domains" may take several minutes before the results are displayed.<br>When you find an available name, click the relevant [Order] link to return to the order form.';

		$_LANG['_WHOIS']['Link_Register']			= 'Register';
		$_LANG['_WHOIS']['Link_Order']			= 'Order';
		$_LANG['_WHOIS']['Link_Details']			= 'Details';
		$_LANG['_WHOIS']['Link_Goto']				= 'Goto';

		$_LANG['_WHOIS']['Title_Available']		= 'Available';
		$_LANG['_WHOIS']['Title_Taken']			= 'Taken';

		$_LANG['_WHOIS']['Title_Domain']			= 'Domain Name';
		$_LANG['_WHOIS']['Title_Extension']		= 'Extension';

# Language Variables: Some Buttons
		$_LANG['_WHOIS']['B_Check']				= 'Check';

# Language Variables:
	# Misc Errors:
		$_LANG['_WHOIS']['Error_Too_Short']		= 'The domain name you typed is too short - it must contain minimum 3 characters.';
		$_LANG['_WHOIS']['Error_Too_Long']			= 'The domain name you typed is to long - it may contain maximum 63 characters.';
		$_LANG['_WHOIS']['Error_Hyphens']			= 'Domain names cannot begin or end with a hyphen or contain double hyphens.';
		$_LANG['_WHOIS']['Error_AlphaNum']			= 'Domain names can only contain alphanumerical characters and hyphens.';
?>