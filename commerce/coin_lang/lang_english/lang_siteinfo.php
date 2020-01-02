<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage SiteInfo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_siteinfo.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
	}

# Language Variables: WHOIS Form
	$_LANG['_SITEINFO']['Title_Domain']		= 'Domain Name';
	$_LANG['_SITEINFO']['Option_All_Domains']	= 'All Domains';
	$_LANG['_SITEINFO']['B_Check']			= 'Check';

# Language Variables: Plans Listing
	$_LANG['_SITEINFO']['Compare_Plans']		= 'Compare Plans';
	$_LANG['_SITEINFO']['Plan']				= 'Plan';
	$_LANG['_SITEINFO']['Domains']			= 'Domains';
	$_LANG['_SITEINFO']['Sub-Domains']			= 'Sub-Domains';
	$_LANG['_SITEINFO']['Disk_MB']			= 'Disk Space (mb)';
	$_LANG['_SITEINFO']['Bandwidth']			= 'Bandwidth (mb)';
	$_LANG['_SITEINFO']['Emails']				= 'Emails';
	$_LANG['_SITEINFO']['Databases']			= 'Databases';
	$_LANG['_SITEINFO']['Cost']				= 'Cost ('.$_CCFG['_CURRENCY_SUFFIX'].')';
	$_LANG['_SITEINFO']['Action']				= 'Action';
	$_LANG['_SITEINFO']['Unlimited']			= 'Unlimited';
	$_LANG['_SITEINFO']['Order']				= 'Order';
	$_LANG['_SITEINFO']['Not_Applicable']		= 'n/a';
	$_LANG['_SITEINFO']['Use_COR']			= 'Don\'t see a package that fits your needs? Complete our <a href="mod.php?mod=orders&cor_flag=1&ord_accept_tos=1&ord_accept_aup=1&stage=1&b_cor=1">Custom Quote Request';
?>