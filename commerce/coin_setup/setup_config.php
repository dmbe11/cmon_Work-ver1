<?php
/**
 * Installation: Configuration
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
	IF (eregi('setup_config.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}


# Enable display of invalid queries during install
	$_CCFG['_debug_queries']			= 1;

# Define laguage file / decoding
	$_CCFG['_HC_PKG_LANG']			= 'lang_english';			// Use this lang if the next line is 0
	$_CCFG['_DB_PKG_LANG_ENABLE']		= 0;						// Use the lang in the database

# Theme settings
	$_CCFG['_HC_PKG_THEME']			= 'earthtone';			// Use this theme if the next line is 0
	$_CCFG['_DB_PKG_THEME_ENABLE']	= 0;					// Use the theme in the database

# Dim Some Vars;
	$ThisVersion					= '1.6.5';

# Initialize our various upgrade MySQL command files
# First element is current installed version, next is the upgrade commands filename
	$SQL_Files[1]	= '121|upgrade_to_v121.sql';
	$SQL_Files[2]	= '122|upgrade_to_v122.sql';
	$SQL_Files[3]	= '123|upgrade_to_v123.sql';
	$SQL_Files[4]	= '124|upgrade_to_v124.sql';
	$SQL_Files[5]	= '125|upgrade_to_v125.sql';
	$SQL_Files[6]	= '126|upgrade_to_v126.sql';
	$SQL_Files[7]	= '127|upgrade_to_v127.sql';
	$SQL_Files[8]	= '128|upgrade_to_v128.sql';
	$SQL_Files[9]	= '130|upgrade_to_v130.sql';
	$SQL_Files[10]	= '131|upgrade_to_v131.sql';
	$SQL_Files[11]	= '140|upgrade_to_v140.sql';
	$SQL_Files[12]	= '141|upgrade_to_v141.sql';
	$SQL_Files[13]	= '142|upgrade_to_v142.sql';
	$SQL_Files[14]	= '143|upgrade_to_v143.sql';
	$SQL_Files[15]	= '144|upgrade_to_v144.sql';
	$SQL_Files[16]	= '145|upgrade_to_v145.sql';
	$SQL_Files[17]	= '150|upgrade_to_v150.sql';
	$SQL_Files[18]	= '151|upgrade_to_v151.sql';
	$SQL_Files[19]	= '160|upgrade_to_v160.sql';
	$SQL_Files[20]	= '161|upgrade_to_v161.sql';
	$SQL_Files[21]	= '162|upgrade_to_v162.sql';
	$SQL_Files[22]	= '163|upgrade_to_v163.sql';
	$SQL_Files[23]	= '164|upgrade_to_v164.sql';
	$SQL_Files[24]	= '165|upgrade_to_v165.sql';

/*	Why did we do the array above?  Simple.
	When releasing a new phpCOIN 1.x we only need to:
		1) Create a MySQL dump of the database changes:
			a) Save the dump as upgrade_to_vXXXX.sql, AND
			b) Append it to the end of setup.sql
		2) Append another element in the array above.
	There is NO need to change the install/upgrade script.
	The install/upgrade script will process each *necessary*
	file in turn, making both install and upgrade super-simple
	to program.
*/


# Build Table Array
	$_TBL_NAMES[]	= $_DBCFG['admins'];
	$_TBL_NAMES[]	= $_DBCFG['articles'];
	$_TBL_NAMES[]	= $_DBCFG['banned'];
	$_TBL_NAMES[]	= $_DBCFG['bills'];
	$_TBL_NAMES[]	= $_DBCFG['bills_items'];
	$_TBL_NAMES[]	= $_DBCFG['bills_trans'];
	$_TBL_NAMES[]	= $_DBCFG['categories'];
	$_TBL_NAMES[]	= $_DBCFG['clients'];
	$_TBL_NAMES[]	= $_DBCFG['clients_contacts'];
	$_TBL_NAMES[]	= $_DBCFG['components'];
	$_TBL_NAMES[]	= $_DBCFG['domains'];
	$_TBL_NAMES[]	= $_DBCFG['downloads'];
	$_TBL_NAMES[]	= $_DBCFG['faq'];
	$_TBL_NAMES[]	= $_DBCFG['faq_qa'];
	$_TBL_NAMES[]	= $_DBCFG['helpdesk'];
	$_TBL_NAMES[]	= $_DBCFG['helpdesk_msgs'];
	$_TBL_NAMES[]	= $_DBCFG['icons'];
	$_TBL_NAMES[]	= $_DBCFG['invoices'];
	$_TBL_NAMES[]	= $_DBCFG['invoices_items'];
	$_TBL_NAMES[]	= $_DBCFG['invoices_trans'];
	$_TBL_NAMES[]	= $_DBCFG['ipn_log'];
	$_TBL_NAMES[]	= $_DBCFG['ipn_text'];
	$_TBL_NAMES[]	= $_DBCFG['mail_archive'];
	$_TBL_NAMES[]	= $_DBCFG['mail_contacts'];
	$_TBL_NAMES[]	= $_DBCFG['mail_queue'];
	$_TBL_NAMES[]	= $_DBCFG['mail_templates'];
	$_TBL_NAMES[]	= $_DBCFG['menu_blocks'];
	$_TBL_NAMES[]	= $_DBCFG['menu_blocks_items'];
	$_TBL_NAMES[]	= $_DBCFG['orders'];
	$_TBL_NAMES[]	= $_DBCFG['orders_sessions'];
	$_TBL_NAMES[]	= $_DBCFG['pages'];
	$_TBL_NAMES[]	= $_DBCFG['parameters'];
	$_TBL_NAMES[]	= $_DBCFG['products'];
	$_TBL_NAMES[]	= $_DBCFG['reminders'];
	$_TBL_NAMES[]	= $_DBCFG['server_info'];
	$_TBL_NAMES[]	= $_DBCFG['sessions'];
	$_TBL_NAMES[]	= $_DBCFG['site_info'];
	$_TBL_NAMES[]	= $_DBCFG['suppliers'];
	$_TBL_NAMES[]	= $_DBCFG['suppliers_contacts'];
	$_TBL_NAMES[]	= $_DBCFG['todo'];
	$_TBL_NAMES[]	= $_DBCFG['topics'];
	$_TBL_NAMES[]	= $_DBCFG['vendors'];
	$_TBL_NAMES[]	= $_DBCFG['vendors_prods'];
	$_TBL_NAMES[]	= $_DBCFG['versions'];
	$_TBL_NAMES[]	= $_DBCFG['whois'];
?>