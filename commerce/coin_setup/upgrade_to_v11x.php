<?php
/**
 * Installation: Upgrade to phpCOIN v1.1.1
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Output
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


require_once('../coin_includes/session_set.php');

/**************************************************************
 * Process "global" variables
**************************************************************/
	# Following should provide php4.06 and register_globals off functionality
	# Process Variables
		IF ( !isset($_SERVER)	)	{ $_SERVER	= $HTTP_SERVER_VARS;}
		IF ( !isset($_POST)		)	{ $_POST	= $HTTP_POST_VARS;		}
		IF ( !isset($_GET)		)	{ $_GET		= $HTTP_GET_VARS;	}
		IF ( !isset($_COOKIE)	)	{ $_COOKIE	= $HTTP_COOKIE_VARS;}
		IF ( !isset($_FILES)	)	{ $_FILES	= $HTTP_POST_FILES;		}
		IF ( !isset($_ENV)		)	{ $_ENV		= $HTTP_ENV_VARS;	}
		IF ( !isset($_SESSION)	)	{ $_SESSION	= $HTTP_SESSION_VARS;}

	# Load GET/POST in global array (POST overwrites GET)
		global $_GPV;

		while(list($key, $var) = each($_GET)) {
//			while($var != utf8_decode($var))	{$var = utf8_decode($var);}
			while($var != urldecode($var))	{$var = urldecode($var);}
			$var = htmlentities($var);
			IF (function_exists('html_entity_decode')) {
				$var = html_entity_decode($var);
			} ELSE {
				$var = unhtmlentities($var);
			}
			while($var != strip_tags($var))	{$var = strip_tags($var);}
			$pieces = explode("\"",$var);		$var = $pieces[0];
			$pieces = explode("'",$var);		$var = $pieces[0];
			$pieces = explode(" ",$var);		$var = $pieces[0];
			IF (eregi('_id', $key) || $key == 'id') {IF (!is_numeric($var)) {$var=0;}}
			$_GPV[$key] = $var;
		}

		while(list($key, $var) = each($_POST))	{
//			while($var != utf8_decode($var))	{$var = utf8_decode($var);}
			while($var != urldecode($var))	{$var = urldecode($var);}
			$var = htmlentities($var);
			IF (function_exists('html_entity_decode')) {
				$var = html_entity_decode($var);
			} ELSE {
				$var = unhtmlentities($var);
			}
			while($var != strip_tags($var))	{$var = strip_tags($var);}
			IF (eregi('_id', $key) || $key == 'id') {IF (!is_numeric($var)) {$var=0;}}
			$_GPV[$key] = $var;
		}


/**************************************************************
 * Load required file includes
**************************************************************/
	# Include Install Lib File
		require_once('setup_lib.php');

	# Validate required files and paths
		clearstatcache();


# Check for <phpcoindir><theme>config.php file
	$_fr_tconfig 	= is_readable( $_CCFG['_PKG_PATH_THEME'].'config.php' );
	$block_title	= 'Error: Required File Not Found';
	$block_content	= 'The required <b>phpcoindir/coin_themes/config.php</b> file could not be located at:';
	$block_content	.= '<br><br><b>'.$_CCFG['_PKG_PATH_THEME'].'config.php</b>';
	$block_content	.= '<br><br>'.'Please double check to make sure the theme configured in <phpcoindir>config.php exists';
	IF ( !$_fr_tconfig ) { error_block($block_title, $block_content) ; exit; }

# Include theme config file
	require_once ($_CCFG['_PKG_PATH_THEME'].'config.php');

# Include constants file
	require_once(PKG_PATH_INCL.'common.php');

# Include redirect file
	require_once(PKG_PATH_INCL.'redirect.php');


/**************************************************************
 * Open Page
**************************************************************/

	$_out = do_install_page_open('1');
	echo $_out;


/**************************************************************
 * Database Instance and load remaining (require config.php)
**************************************************************/

	# Include db.php file and connect
		require_once(PKG_PATH_INCL.'db.php');

	# Create db Instance
		$db = new db_funcs();

	# Include constants file (requires config for prefix)
		require_once(PKG_PATH_INCL.'constants.php');

	# Include constants-override file, if present
		IF (file_exists(PKG_PATH_OVERRIDES.'constants_override.php')) {
			require(PKG_PATH_OVERRIDES.'constants_override.php');
		}


/**************************************************************
 * Database Call and load remaining (require config.php)
**************************************************************/
# Attempt database connect:
	$db_coin->db_set_suppress_errors(1);
	$db_coin->db_connect();

	IF ( $db_coin->connection )
	{
		$_db_check = 1;
		$_cstr .= '<b>Database Connection completed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Hostname, Username, and Password are OK.<br>'.$_nl;
	}
	ELSE
	{
		$_db_check = 0;
		$_cstr .= '<b>Database Connection failed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Check Hostname, Username, and Password and try again. Items located in config.php file.<br>'.$_nl;
	}

# Attempt database select:
	$db_coin->db_select_db();

	IF ( $db_coin->connection )
	{
		$_db_check = 1;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<b>Database Selection Completed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Database Name is OK.<br>'.$_nl;
	}
	ELSE
	{
		$_db_check = 0;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<b>Database Selection failed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Check Database Name and try again. Items located in config.php file.<br>'.$_nl;
	}

	# Output  data
		$_tstr	= 'Initial Database Check';
		$_out	= '<br>'.$_nl;
		$_out	.= do_install_block_it ($_tstr, $_cstr, 0, '', '1');
		$_tstr	= ''; $_cstr	= ''; $_mstr	= '';
		echo $_out;

# Create string for option checkboxes (used twice below).
	$_options .= '<table width="65%"><tr><td class="TP5MED_NL">'.$_nl;

	IF ( $_GPV[up_all]==1 || !$_GPV[stage]) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
	$_options .= '<INPUT TYPE=CHECKBOX NAME="up_all" value="1"'.$_set.' border="0">'.$_nl;
	$_options .= '<b>&nbsp;Check for complete upgrade (recommended).</b>'.$_nl;
	$_options .= '<br>'.$_nl;

	$_options .= '</td></tr></table>'.$_nl;
	$_options .= '<br>'.$_nl;


# Do cheesy login to check against db password (non-encrypt)
	IF ( !$_GPV[stage] && $_db_check==1 )
	{
		# Create Login Form
			$_cstr .= '<table width="100%"><tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>Enter Your Database Password to Complete Upgrade:</b><br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;(upgrade will begin on clicking upgrade- no additional prompts)<br>'.$_nl;
			$_cstr .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="login">'.$_nl;

			$_cstr .= $_options;

			$_cstr .= '<b>Password:&nbsp;&nbsp;<INPUT class="PMED_NL" type="password" name="password" size="20" maxlength="20">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
			$_cstr .= '<INPUT class="PMED_NC" TYPE=SUBMIT value="Upgrade">'.$_nl;
			$_cstr .= '</form>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;

			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>Upgrading this package indicates acceptance of accompanying '.$_nl;
			$_cstr .= '<a href="'.PKG_URL_BASE.'coin_docs/license.txt" target="_blank">license</a></b>'.$_nl;
			$_cstr .= '</td></tr></table>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		# Output  data
			$_tstr	= 'Log In';
			$_out	= '<br>'.$_nl;
			$_out	.= do_install_block_it ($_tstr, $_cstr, 0, '', '1');
			$_tstr	= ''; $_cstr	= ''; $_mstr	= '';
			echo $_out;
	}
	ELSE
	{
		IF ( $_GPV[stage]==1 && $_db_check==1 )
		{
			IF ( $_GPV[password] == $_DBCFG[dbpass] )
			{
				# Build result string
					$_proceed = 1;
					$_cstr .= '<b>Login Results:</b><br>'.$_nl;
					$_cstr .= '&nbsp;&nbsp;- Login: Passed- proceeding with upgrade.............<br>'.$_nl;

				# Output  data
					$_tstr	= 'Log In';
					$_out	= '<br>'.$_nl;
					$_out	.= do_install_block_it ($_tstr, $_cstr, 0, '', '1');
					$_tstr	= ''; $_cstr	= ''; $_mstr	= '';
					echo $_out;
			}
			ELSE
			{
				# Build result string
					$_cstr .= '<b>Login Results:</b><br>'.$_nl;
					$_cstr .= '&nbsp;&nbsp;- Login: Failed- aborting upgrade.............<br>'.$_nl;
					$_cstr .= '<br>'.$_nl;

				# Create Login Form
					$_cstr .= '<table width="100%"><tr><td class="TP5MED_NC">'.$_nl;
					$_cstr .= '<b>Enter Your Database Password to Complete Upgrade:</b><br>'.$_nl;
					$_cstr .= '&nbsp;&nbsp;(upgrade will begin on clicking upgrade- no additional prompts)<br>'.$_nl;
					$_cstr .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="login">'.$_nl;

					$_cstr .= $_options;

					$_cstr .= '<b>Password:&nbsp;&nbsp;<INPUT type="password" name="password" size="20" maxlength="20">'.$_nl;
					$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
					$_cstr .= '<INPUT TYPE=SUBMIT value="Upgrade">'.$_nl;
					$_cstr .= '</form>'.$_nl;
					$_cstr .= '</td></tr>'.$_nl;

					$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
					$_cstr .= '<b>Upgrading this package indicates acceptance of accompanying '.$_nl;
					$_cstr .= '<a href="'.PKG_URL_BASE.'coin_docs/license.txt" target="_blank">license</a></b>'.$_nl;
					$_cstr .= '</td></tr></table>'.$_nl;
					$_cstr .= '<br>'.$_nl;

				# Output  data
					$_tstr	= 'Log In';
					$_out	= '<br>'.$_nl;
					$_out	.= do_install_block_it ($_tstr, $_cstr, 0, '', '1');
					$_tstr	= ''; $_cstr	= ''; $_mstr	= '';
					echo $_out;
			}
		}
	}

# Check proceed flag and go
	IF ( $_proceed )
	{
		$db_coin->db_set_suppress_errors(0);

		# Create some variables for auto-insert data
			$_domain = $_SERVER["SERVER_NAME"];
			$_str_search	= 'www.';
			$_str_replace	= '';
			$_domain		= str_replace( $_str_search, $_str_replace, $_domain );

		# Get list of tables, and set exist flags
			$result = mysql_list_tables($_DBCFG['dbname']);
			while(list($_TBL)=mysql_fetch_row($result))
			{
				$_tbl_list1 .= $_dbtable.'<br>'.$_nl;
				IF (  $_TBL == $_DBCFG['admins'] ) 				{ $_TBLEXIST[$_DBCFG['admins']] = 1; }
				IF (  $_TBL == $_DBCFG['articles'] )			{ $_TBLEXIST[$_DBCFG['articles']] = 1; }
				IF (  $_TBL == $_DBCFG['categories'] )			{ $_TBLEXIST[$_DBCFG['categories']] = 1; }
				IF (  $_TBL == $_DBCFG['clients'] )				{ $_TBLEXIST[$_DBCFG['clients']] = 1; }
				IF (  $_TBL == $_DBCFG['clients_domains'] )		{ $_TBLEXIST[$_DBCFG['clients_domains']] = 1; }
				IF (  $_TBL == $_DBCFG['clients_status'] )		{ $_TBLEXIST[$_DBCFG['clients_status']] = 1; }
				IF (  $_TBL == $_DBCFG['components'] )			{ $_TBLEXIST[$_DBCFG['components']] = 1; }
				IF (  $_TBL == $_DBCFG['faq'] )					{ $_TBLEXIST[$_DBCFG['faq']] = 1; }
				IF (  $_TBL == $_DBCFG['faq_qa'] )				{ $_TBLEXIST[$_DBCFG['faq_qa']] = 1; }
				IF (  $_TBL == $_DBCFG['icons'] )				{ $_TBLEXIST[$_DBCFG['icons']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices'] )			{ $_TBLEXIST[$_DBCFG['invoices']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices_items'] )		{ $_TBLEXIST[$_DBCFG['invoices_items']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices_status'] )		{ $_TBLEXIST[$_DBCFG['invoices_status']] = 1; }
				IF (  $_TBL == $_DBCFG['helpdesk'] )			{ $_TBLEXIST[$_DBCFG['helpdesk']] = 1; }
				IF (  $_TBL == $_DBCFG['helpdesk_msgs'] )		{ $_TBLEXIST[$_DBCFG['helpdesk_msgs']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_archive'] )		{ $_TBLEXIST[$_DBCFG['mail_archive']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_contacts'] )		{ $_TBLEXIST[$_DBCFG['mail_contacts']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_queue'] )			{ $_TBLEXIST[$_DBCFG['mail_queue']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_templates'] )		{ $_TBLEXIST[$_DBCFG['mail_templates']] = 1; }
				IF (  $_TBL == $_DBCFG['menu_blocks'] )			{ $_TBLEXIST[$_DBCFG['menu_blocks']] = 1; }
				IF (  $_TBL == $_DBCFG['menu_blocks_items'] )	{ $_TBLEXIST[$_DBCFG['menu_blocks_items']] = 1; }
				IF (  $_TBL == $_DBCFG['orders'] )				{ $_TBLEXIST[$_DBCFG['orders']] = 1; }
				IF (  $_TBL == $_DBCFG['orders_status'] )		{ $_TBLEXIST[$_DBCFG['orders_status']] = 1; }
				IF (  $_TBL == $_DBCFG['orders_sessions'] )		{ $_TBLEXIST[$_DBCFG['orders_sessions']] = 1; }
				IF (  $_TBL == $_DBCFG['pages'] )				{ $_TBLEXIST[$_DBCFG['pages']] = 1; }
				IF (  $_TBL == $_DBCFG['parameters'] )			{ $_TBLEXIST[$_DBCFG['parameters']] = 1; }
				IF (  $_TBL == $_DBCFG['products'] )			{ $_TBLEXIST[$_DBCFG['products']] = 1; }
				IF (  $_TBL == $_DBCFG['server_accounts'] )		{ $_TBLEXIST[$_DBCFG['server_accounts']] = 1; }
				IF (  $_TBL == $_DBCFG['server_info'] )			{ $_TBLEXIST[$_DBCFG['server_info']] = 1; }
				IF (  $_TBL == $_DBCFG['site_info'] )			{ $_TBLEXIST[$_DBCFG['site_info']] = 1; }
				IF (  $_TBL == $_DBCFG['topics'] )				{ $_TBLEXIST[$_DBCFG['topics']] = 1; }
				IF (  $_TBL == $_DBCFG['vendors'] )				{ $_TBLEXIST[$_DBCFG['vendors']] = 1; }
				IF (  $_TBL == $_DBCFG['vendors_prods'] )		{ $_TBLEXIST[$_DBCFG['vendors_prods']] = 1; }
			}
			IF ( $result) { $result = mysql_free_result($result); }

		#######################################################
		# Clients Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['clients']] )
		{
			$_cstr .= '<br><b>Clients Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "ALTER TABLE ".$_DBCFG['clients']." ADD `cl_notes` TEXT NOT NULL";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- field added.<br><br>';

			$_cstr .= '<br><b>Clients Table(s) Upgrade completed.</b><br>';

		} # Clients Table modifications completed

		#######################################################
		# Clients Domains Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['clients_domains']] )
		{
			$_cstr .= '<br><b>Clients Domains Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "ALTER TABLE ".$_DBCFG['clients_domains']." ADD `cd_sa_expiration` VARCHAR(10) NOT NULL";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- field added.<br><br>';

			$_cstr .= '<br><b>Clients Domains Table(s) Upgrade completed.</b><br>';

		} # Clients Domains Table modifications completed

		#######################################################
		# Clients Status Table modifications: Dropped
		IF ( $_TBLEXIST[$_DBCFG['clients_status']] )
		{
			$_cstr .= '<br><b>Clients Status Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "DROP TABLE ".$_DBCFG['clients_status'];
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table dropped<br><br>';

			$_cstr .= '<br><b>Clients Status Table(s) Upgrade completed.</b><br>';

		} # Clients Status Table modifications completed

		#######################################################
		# FAQ QA Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['faq_qa']] )
		{
			$_cstr .= '<br><b>FAQ QA Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "ALTER TABLE ".$_DBCFG['faq_qa']." CHANGE `faqqa_question` `faqqa_question` VARCHAR(255) NOT NULL default ''";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- field modified.<br><br>';

			$_cstr .= '<br><b>FAQ QA Table(s) Upgrade completed.</b><br>';

		} # FAQ QA Table modifications completed

		#######################################################
		# Invoices Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['invoices']] )
		{
			$_cstr .= '<br><b>Invoices Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_DBCFG['invoices']." ADD `invc_subtotal_cost` DECIMAL(10,2) DEFAULT '0.00' NOT NULL AFTER `invc_total_cost`
						, ADD `invc_tax_01_percent` DECIMAL(3,2) DEFAULT '0.00' NOT NULL AFTER `invc_subtotal_cost`
						, ADD `invc_tax_01_amount` DECIMAL(10,2) DEFAULT '0.00' NOT NULL AFTER `invc_tax_01_percent`
						, ADD `invc_tax_02_percent` DECIMAL(3,2) DEFAULT '0.00' NOT NULL AFTER `invc_tax_01_amount`
						, ADD `invc_tax_02_amount` DECIMAL(10,2) DEFAULT '0.00' NOT NULL AFTER `invc_tax_02_percent`
						, ADD `invc_deliv_method` VARCHAR(10) NOT NULL AFTER `invc_status`
						, ADD `invc_delivered` TINYINT(1) DEFAULT '0' NOT NULL AFTER `invc_deliv_method`
						, ADD `invc_terms` TEXT NOT NULL
						;";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- fields added.<br><br>';

			$_cstr .= '<br><b>Invoices Table(s) Upgrade completed.</b><br>';

		} # Invoices Table modifications completed

		#######################################################
		# Invoices Status Table modifications: Dropped
		IF ( $_TBLEXIST[$_DBCFG['invoices_status']] )
		{
			$_cstr .= '<br><b>Invoices Status Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "DROP TABLE ".$_DBCFG['invoices_status'];
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table dropped<br><br>';

			$_cstr .= '<br><b>Invoices Status Table(s) Upgrade completed.</b><br>';

		} # Invoices Status Table modifications completed

		#######################################################
		# Mail Templates Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['mail_templates']] )
		{
			$_cstr .= '<br><b>Mail Templates Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
			# Query to check existing entry entry:
				$query 			= "SELECT mt_id FROM ".$_DBCFG['mail_templates']." WHERE mt_name = 'future-13'";
				$result 		= $db_coin->db_query_execute($query);
				$numrows_ttl	= $db_coin->db_query_numrows($result);
				while(list($mt_id) = $db_coin->db_fetch_row($result)) { $_mt_id = $mt_id; }

				# If exists, do update.
				IF ( $numrows_ttl && $_mt_id )
					{
						$_value		= ' Ticket ID:    \$_MTP[hd_tt_id]\r\n Priority:     \$_MTP[hd_tt_priority]\r\n Subject:      \$_MTP[hd_tt_subject]\r\n';
						$query		= "UPDATE ".$_DBCFG['mail_templates'];
						$query		.= " SET mt_name = 'email_helpdesk_tt_alert'";
						$query		.= ", mt_text = '$_value'";
						$query		.= " WHERE mt_id = '$_mt_id'";

						$result 	= $db_coin->db_query_execute($query);
						$numrows	= $db_coin->db_affected_rows();
					}

				$_cstr .= '<br>Table modified- template update.<br><br>';

			$_cstr .= '<br><b>Mail Templates Table(s) Upgrade completed.</b><br>';

		} # Mail Templates Table modifications completed

		#######################################################
		# Orders Status Table modifications: Dropped
		IF ( $_TBLEXIST[$_DBCFG['orders_status']] )
		{
			$_cstr .= '<br><b>Orders Status Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query 		= "DROP TABLE ".$_DBCFG['orders_status'];
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table dropped<br><br>';

			$_cstr .= '<br><b>Orders Status Table(s) Upgrade completed.</b><br>';

		} # Orders Status Table modifications completed

		#######################################################
		# Parameters Table modifications: (rename existing and create new)
		IF ( $_TBLEXIST[$_DBCFG['parameters']] )
		{
			$_cstr .= '<br><b>Parameters Table(s) Upgrade started.</b><br>';

			# Dim some Vars:
				global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
				$query = ""; $result= ""; $numrows = 0;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['parameters'];
				$result 	= ''; $eff_rows = '';
				$query 		= "DROP TABLE IF EXISTS ".$_TBL_NAME."_bak";
				$result		= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.'_bak table.<br>'.$_nl; }
				$result = ''; $eff_rows = '';
				$query 		= "ALTER TABLE ".$_TBL_NAME." RENAME ".$_TBL_NAME."_bak";
				$result		= $db_coin->db_query_execute($query);
				IF ( $result) {  $_cstr .= '&nbsp;&nbsp;- Renamed the existing table to '.$_TBL_NAME.'_bak.<br>'.$_nl; }


			/**************************************************************
				 * Table Create and Data Arrays For: parameters
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['parameters'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
						  `parm_id` tinyint(4) NOT NULL auto_increment,
						  `parm_group` varchar(10) NOT NULL default 'undefined',
						  `parm_group_sub` varchar(10) NOT NULL default 'undefined',
						  `parm_type` char(1) NOT NULL default 'S',
						  `parm_name` varchar(50) NOT NULL default '',
						  `parm_desc` varchar(50) NOT NULL default '',
						  `parm_value` varchar(100) NOT NULL default '',
						  `parm_notes` text NOT NULL,
						  PRIMARY KEY  (`parm_id`)
						) TYPE=MyISAM COMMENT='Parameters Table'
						";

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('', 'common', 'admin', 'I', '_MAX_MENU_BLK_POS', 'Menu Block Position List Max Select', '25', 'Determines the maximum number to appear in the menu block position field dropdown list.'),
							('', 'common', 'admin', 'I', '_MAX_MENU_ITM_NO', 'Menu Block Item Position List Max Select', '25', 'Determines the maximum number to appear in the menu block item position field dropdown list.'),
							('', 'common', 'admin', 'B', '_PARM_EDITOR_SHOW_NOTES', 'Parameter Editor Show Notes in Listing', '1', 'To enable the display of the parameter notes field in the main listing.'),
							('', 'common', 'articles', 'S', 'ARTICLES_DATE_DISPLAY_FORMAT', 'Articles Date/Time Display Format', '%A- %B %d, %Y @ %H:%M:%S %Z', 'Determines the display format for the Articles module date display. See http://www.php.net/manual/en/function.strftime.php for formatting options.'),
							('', 'common', 'articles', 'I', 'IPP_ARTICLES', 'Listing Items Per Page for: Articles', '10', 'Determines the number of articles that are displayed  in listing form on a page.'),
							('', 'common', 'clients', 'I', 'BASE_CLIENT_ID', 'Client ID Base-Initial Value Minus 1', '1000', 'Determines the client id initial value for the first client entered in the system. Client ID will be this value plus one.'),
							('', 'common', 'clients', 'I', 'IPL_CLIENTS_ACCOUNT', 'Listing Items Per List for: Client Account', '5', 'Determines the number of most recent items (orders, invoices, tickets) that are displayed on a clients account page.'),
							('', 'common', 'clients', 'I', 'IPP_CLIENTS', 'Listing Items Per Page for: Clients', '10', 'Determines the number of clients that are displayed on a page.'),
							('', 'common', 'clients', 'B', 'CLIENT_EMAIL_CC_ENABLE', 'Client Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a request is made to email a client profile.'),
							('', 'common', 'contacts', 'I', 'MC_ID_BILLING', 'Mail Contact ID For Billing', '1', 'Determines the mail contact id to be used when code requires an email address for: Billing'),
							('', 'common', 'contacts', 'I', 'MC_ID_ORDERS', 'Mail Contact ID For Orders', '2', 'Determines the mail contact id to be used when code requires an email address for: Orders'),
							('', 'common', 'contacts', 'I', 'MC_ID_SUPPORT', 'Mail Contact ID For Support', '3', 'Determines the mail contact id to be used when code requires an email address for: Support'),
							('', 'common', 'contacts', 'I', 'MC_ID_WEBMASTER', 'Mail Contact ID For Webmaster', '4', 'Determines the mail contact id to be used when code requires an email address for: Webmaster'),
							('', 'common', 'helpdesk', 'I', 'BASE_HELPDESK_ID', 'HelpDesk TT ID Base-Initial Value Minus 1', '1000', 'Determines the helpdesk ticket id initial value for the first helpdesk ticket entered in the system. Helpdesk Ticket ID will be this value plus one.'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_ENABLE', 'Helpdesk Enable (Visible)', '1', 'Determines if the HelpDesk module is visible to the clients.'),
							('', 'common', 'helpdesk', 'I', 'IPP_HELPDESK', 'Listing Items Per Page for: HelpDesk', '10', 'Determines the number of helpdesk tickets that are displayed  in listing form on a page.\r\n'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_ALERT_EMAIL_ENABLE', 'Helpdesk TT Alert Email Enable', '1', 'Determines if an Alert Email is also sent when a new ticket is created. In addition to normal email to support.'),
							('', 'common', 'helpdesk', 'S', 'HELPDESK_ALERT_EMAIL_ADDRESS', 'HelpDesk TT Alert Email Address', 'webmaster@$_domain', 'Determines the email (pager email) address the Alert Email is sent to. Does not effect normal support email received.'),
							('', 'common', 'invoices', 'I', 'BASE_INVOICE_ID', 'Invoice ID Base-Initial Value Minus 1', '1000', 'Determines the invoice id initial value for the first invoice entered in the system. Invoice ID will be this value plus one.'),
							('', 'common', 'invoices', 'B', 'INVC_TAX_01_ENABLE', 'Invoice Tax 01 Enable', '1', 'Determines if invoice tax 01 is enabled for display and calculation.'),
							('', 'common', 'invoices', 'B', 'INVC_TAX_02_ENABLE', 'Invoice Tax 02 Enable', '1', 'Determines if invoice tax 02 is enabled for display and calculation.'),
							('', 'common', 'invoices', 'B', 'INVC_TERMS_INSERT_DEF', 'Invoice Terms- Insert Default on New', '1', 'Determines if, when terms enabled, the default terms value is inserted on new invoice create (if left empty on save).'),
							('', 'common', 'invoices', 'B', 'INVC_TERMS_ENABLE', 'Invoice Terms- Enable Display', '1', 'Determines if the invoice terms field is displayed on invoice views.'),
							('', 'common', 'invoices', 'B', 'INVC_AUTO_UPDATE_ENABLE', 'Invoice Auto-Update of Status Enable', '1', 'Determines if the auto-update of invoice status function is enabled when triggered by cron job or manually by command summary actions menu.'),
							('', 'common', 'invoices', 'B', 'INVC_AUTO_EMAIL_ENABLE', 'Invoice Auto-Email to Clients Enable', '1', 'Determines if the auto-email of due invoices function is enabled when triggered by cron job or manually by command summary actions menu.'),
							('', 'common', 'invoices', 'B', 'INVC_AUTO_EMAIL_CC_ENABLE', 'Invoice Auto-Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a the auto-email code sends an invoice.\r\n'),
							('', 'common', 'invoices', 'B', 'INVC_EMAIL_CC_ENABLE', 'Invoice Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a request is made to email an invoice.\r\n'),
							('', 'common', 'invoices', 'S', 'INVC_DEL_MTHD_DEFAULT', 'Invoice Delivery Method Default Value', 'email', 'Determines the default invoice delivery method presented on invoice create.'),
							('', 'common', 'invoices', 'I', 'INVOICE_DUE_DAYS_OFFSET', 'Invoice Due Date Offset from Create in Days', '14', 'Determines the number of days to add to current date and set the due date to when creating a new invoice.'),
							('', 'common', 'invoices', 'I', 'IPP_INVOICES', 'Listing Items Per Page for: Invoices', '10', 'Determines the number of invoices that are displayed  in listing form on a page.\r\n'),
							('', 'common', 'layout', 'S', '_PKG_FOOTER_LINE_01', 'Site Footer Block Line 01 Text', 'Copyright © <a href=http://www.$_domain>$_domain</a> 2003', 'Determines the line 01 text that will be displayed in footer of the page output.'),
							('', 'common', 'layout', 'S', '_PKG_FOOTER_LINE_02', 'Site Footer Block Line 02 Text', '', 'Determines the line 02 text that will be displayed in footer of the page output.'),
							('', 'common', 'module', 'I', 'IPP_MNEWS', 'Listing Items Per Page for: M-News', '30', 'Determines the number of news items that are displayed  in listing form on a page. Future item.\r\n'),
							('', 'common', 'operation', 'B', '_PKG_MODE_TEST', 'Package Test Mode Enable.', '0', 'Determines if the package is placed in test mode. This mode is for admin setup and testing, and may display various items for convenience.'),
							('', 'common', 'operation', 'B', 'DOM_FORCE_CHECK_TRUE', 'Domain Name Validation Force True', '0', 'Determines if the domain validation logic always returns true. Only for those who not want to validate input domain names.'),
							('', 'common', 'orders', 'I', 'BASE_ORDER_ID', 'Order ID Base-Initial Value Minus 1', '1000', 'Determines the order id initial value for the first order entered in the system. Order ID will be this value plus one.'),
							('', 'common', 'orders', 'B', 'ENABLE_EMAIL_ORDER_OUT', 'Email to Admin on Order Out Enable', '1', 'Determines if an email is sent to admin when client is showed the order paylinks (order inserted into system).'),
							('', 'common', 'orders', 'B', 'ENABLE_EMAIL_ORDER_RET', 'Email to Admin on Order Return Enable', '1', 'Determines if an email is sent to admin when client is returned to site from billing vendor.'),
							('', 'common', 'orders', 'B', 'ORDER_POLICY_BTTN_AUP', 'Order Policy Button Show: AUP', '1', 'Determines if order policy button for Acceptable Use Policy is shown on place order screens.'),
							('', 'common', 'orders', 'B', 'ORDER_POLICY_BTTN_BC', 'Order Policy Button Show: BC', '1', 'Determines if order policy button for Banned Code is shown on place order screens.'),
							('', 'common', 'orders', 'B', 'ORDER_POLICY_BTTN_PP', 'Order Policy Button Show: PP', '1', 'Determines if order policy button for Privacy Policy is shown on place order screens.'),
							('', 'common', 'orders', 'B', 'ORDER_POLICY_BTTN_TOS', 'Order Policy Button Show: TOS', '1', 'Determines if order policy button for Terms Of Service is shown on place order screens.'),
							('', 'common', 'orders', 'I', 'ORDER_POLICY_SI_ID_AUP', 'Order Policy SiteInfo ID for: AUP', '6', 'Determines the siteinfo page id for the Acceptable Use Policy button link.'),
							('', 'common', 'orders', 'I', 'ORDER_POLICY_SI_ID_BC', 'Order Policy SiteInfo ID for: BC', '8', 'Determines the siteinfo page id for the Banned Code Policy button link.'),
							('', 'common', 'orders', 'I', 'ORDER_POLICY_SI_ID_PP', 'Order Policy SiteInfo ID for: PP', '5', 'Determines the siteinfo page id for the Privacy Policy button link.'),
							('', 'common', 'orders', 'I', 'ORDER_POLICY_SI_ID_TOS', 'Order Policy SiteInfo ID for: TOS', '7', 'Determines the siteinfo page id for the Terms Of Service button link.'),
							('', 'common', 'orders', 'I', 'IPP_ORDERS', 'Listing Items Per Page for: Orders', '10', 'Determines the number of orders that are displayed in listing form on a page.'),
							('', 'common', 'orders', 'B', 'ORDERS_ENABLE', 'Orders Module Enable', '1', 'Determines if the Orders module links / output is visible to clients.'),
							('', 'common', 'orders', 'B', 'ORDERS_AUP_ENABLE', 'Orders Acceptable Use Policy (AUP) Enable', '1', 'Determines if the agree to the Acceptable Use Policy button and requirement is visible during order placement. '),
							('', 'common', 'orders', 'B', 'ORDERS_COR_ENABLE', 'Orders Custom Order Request (COR) Enable', '1', 'Determines if the Custom Order Request (COR) button / link is visible during the order placement.'),
							('', 'common', 'orders', 'B', 'ORDER_EMAIL_CC_ENABLE', 'Order Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a request is made to email a order.'),
							('', 'common', 'package', 'S', '_CURRENCY_PREFIX', 'Currency Prefix String (ex. \$)', '\$', 'Determines the currency notation prefix, placed in front of currency amounts (where formatted to display).'),
							('', 'common', 'package', 'S', '_CURRENCY_SUFFIX', 'Currency Suffix String (ex. USD)', '(USD)', 'Determines the currency notation suffix, placed following currency amounts (where formatted to display).'),
							('', 'common', 'package', 'S', '_DB_PKG_LANG', 'Package Language (database setting)', 'lang_english', 'Determines which language files to load. The language file directory and files MUST exist prior to selection. Maybe be overridden by config.php setting.'),
							('', 'common', 'package', 'S', '_DB_PKG_LOCALE', 'Package Locale Setting (datetime display)', 'en_EN', 'Some examples:\r\nBulgaria - bg_BG\r\nEnglish  - en_EN (or English)\r\nFrench   - fr_FR\r\nGerman  - German\r\nSwedish  - sv_SE\r\nSpanish  - Spanish\r\n'),
							('', 'common', 'package', 'S', '_DB_PKG_THEME', 'Package Theme (database setting)', 'earthtone', 'Determines which theme files to load. The theme file directory and files MUST exist prior to selection. Maybe be overridden by config.php setting.'),
							('', 'common', 'package', 'I', '_NUMBER_FORMAT_ID', 'Number Format (see notes)', '3', 'Example number 1234.56\r\nValue = 1	Output: 1234\r\nValue = 2	Output: 1234.56\r\nValue = 3	Output: 1,234.56\r\nValue = 4	Output: 1 234,56\r\nValue = 5	Output: 1.234,56\r\n'),
							('', 'common', 'package', 'S', '_PKG_NAME_SHORT', 'Site Name (short)', '$_domain', 'For display of site name (short version). This variable is used extensively in emails, and various pages.'),
							('', 'common', 'package', 'S', '_PKG_NAME_LONG', 'Site Name (long)', '$_domain WebServices', 'Site long name for display, emails, etc. Basically longer version of the site short name.'),
							('', 'common', 'package', 'S', '_PKG_EMAIL_MAIL', 'Package Email (default)', 'webmaster@$_domain', 'Package last resort email address when it cannot locate a mail contacts entry.'),
							('', 'common', 'package', 'S', '_PKG_TOP_GREETING', 'Site Top Block Greeting Text', 'Welcome To $_domain WebServices', 'The Site Top Block (header) greeting text displayed if logo is not enabled.'),
							('', 'common', 'package', 'B', '_PKG_DATE_SERVER_OFFSET', 'Date display server offset in hours (global)', '0', 'Determines any hour offset from server time for the timestamps recorded / displayed. Can be negative.'),
							('', 'common', 'package', 'S', '_PKG_DATE_FORMAT_SHORT_DT', 'Date Display Format for: Short Date (numerical)', 'Y-m-d', 'Determines the display format for the short datetime, not locale setting dependent. See http://www.php.net/manual/en/function.date.php for formatting options.'),
							('', 'common', 'package', 'S', '_PKG_DATE_FORMAT_SHORT_DTTM', 'Date Display Format for: Short DateTime (numerical)', 'Y-m-d H:i:s', 'Determines the display format for the short datetime, not locale setting dependent. See http://www.php.net/manual/en/function.date.php for formatting options.'),
							('', 'common', 'package', 'S', '_PKG_DATE_FORMAT_HEADER', 'Date Display Format for: Header Row', '%A- %B %d, %Y', 'Determines the display format for the header block date display. See http://www.php.net/manual/en/function.strftime.php for formatting options.'),
							('', 'common', 'package', 'S', '_PKG_DATE_FORMAT_PRINT', 'Date Display Format for: Printing (includes time)', '%A- %B %d, %Y @ %H:%M:%S %Z', 'Determines the display format for the footer printed items  date display. See http://www.php.net/manual/en/function.strftime.php for formatting options.'),
							('', 'common', 'pages', 'I', 'IPP_PAGES', 'Listing Items Per Page for: Pages', '15', 'Determines the number of pages that are displayed  in listing form on a page.\r\n'),
							('', 'common', 'saccs', 'I', 'DOM_DEFAULT_SERVER', 'Server Account Default Server ID', '1', 'Determines the default server id that is inserted when a server account is created.'),
							('', 'common', 'saccs', 'S', 'DOM_DEFAULT_PATH', 'Server Account Default Path', '/home/httpd/vhosts/domainname/httpdocs', 'Note: place \"domainname\" in path to have it replaced by domain name on entry create.'),
							('', 'common', 'saccs', 'B', 'DOM_EMAIL_CC_ENABLE', 'SACC Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a request is made to email a server account activation email.'),
							('', 'theme', 'layout', 'B', '_PAGE_HEADER_LOGO', 'Site- Page Header Show Logo', '1', 'Determines whether the site logo, or greeting text is displayed in the page header block.'),
							('', 'theme', 'layout', 'S', '_PAGE_HEADER_LOGO_FILE', 'Site- Page Logo Filename', 'logo_phpcoin.gif', 'Logo filename that is located in the site /images directory.'),
							('', 'theme', 'layout', 'B', '_PAGE_HEADER_CLEAR', 'Site- Page Header Make Clear', '0', 'Determines if the site header row is colored (styles) or clear (underlying color).'),
							('', 'theme', 'layout', 'B', '_PAGE_FOOTER_CLEAR', 'Site- Page Footer Make Clear', '1', 'Determines if the site footer row is colored (styles) or clear (underlying color).'),
							('', 'theme', 'layout', 'B', '_DISABLE_MENU_COLS', 'Disable Menu Columns (Left / Right)', '1', 'Determines if the page left / right menu columns are hidden.'),
							('', 'theme', 'layout', 'B', '_DISABLE_HEADER_BLK', 'Disable Header (top) Block', '0', 'Determines if the page header row is hidden.'),
							('', 'theme', 'layout', 'B', '_DISABLE_FOOTER_BLK', 'Disable Footer (bottom) Block', '0', 'Determines if the page footer row is hidden.'),
							('', 'theme', 'layout', 'B', '_ENABLE_MENU_USER_HDR', 'Enable- Menu User in Header Block', '0', 'Determines if the logged in user / admin menu is visible as a line directly below the main header menu.'),
							('', 'theme', 'layout', 'B', '_ENABLE_MENU_USER_HROW', 'Enable- Menu User in Header Row', '1', 'Determines if the logged in user / admin menu is visible as an additional row below the header.'),
							('', 'theme', 'layout', 'B', '_ENABLE_MENU_USER_FROW', 'Enable- Menu User in Footer Row', '0', 'Determines if the logged in user / admin menu is visible as an additional row above the footer.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_HOME', 'Header Menu Button Show: Home', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_ABOUT', 'Header Menu Button Show: About Us', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_ARTICLES', 'Header Menu Button Show: Articles', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_CONTACT', 'Header Menu Button Show: Contact', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_FAQ', 'Header Menu Button Show: FAQ', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_SEARCH', 'Header Menu Button Show: Search', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_ORDER', 'Header Menu Button Show: Place Order', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_HELPDESK', 'Header Menu Button Show: HelpDesk', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'B', '_HDR_MENU_BTTN_SERVICES', 'Header Menu Button Show: Services', '0', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_ABOUT', 'Header Menu Button Link: About Us', 'mod.php?mod=siteinfo&id=4', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_ARTICLES', 'Header Menu Button Link: Articles', 'mod.php?mod=articles', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_CONTACT', 'Header Menu Button Link: Contact', 'mod.php?mod=mail&mode=contact', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_FAQ', 'Header Menu Button Link: FAQ', 'mod.php?mod=faq', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_HELPDESK', 'Header Menu Button Link: HelpDesk', 'mod.php?mod=helpdesk', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_HOME', 'Header Menu Button Link: Home', 'index.php', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_ORDER', 'Header Menu Button Link: Place Order', 'mod.php?mod=orders', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_SEARCH', 'Header Menu Button Link: Search', 'mod.php?mod=search', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'layout', 'S', '_HDR_MENU_BTTN_LINK_SERVICES', 'Header Menu Button Link: Services', 'index.php', 'Determines link (URL) for the corresponding button in main menu. To be defined by user.'),
							('', 'theme', 'layout', 'S', '_WIDTH_OUTER_TABLE', 'Percent / Pixel (include % or px) of outmost table', '750px', 'Determines the pixel width of the package outermost table. Must be include either percent or pixel.'),
							('', 'theme', 'layout', 'I', '_WIDTH_COL_BLOCK', 'Percentage Width Of Menu left / right columns', '15', 'Determines the percentage of total width for each left / right menu column.'),
							('', 'theme', 'layout', 'I', '_WIDTH_MENU_BLOCK', 'Pixel Width Of Menu Block', '150', 'Determines the pixel width setting for a right / left column menu block.'),
							('', 'theme', 'layout', 'S', '_WIDTH_CONTENT_AREA', 'Percentage width of Content Area Tables', '100%', 'Determines the percentage width of the center content area that non modules output will consume.'),
							('', 'theme', 'layout', 'S', '_WIDTH_MODULE_AREA', 'Percentage Width Of Module Content', '100%', 'Determines the percentage width of the center content area that modules output will consume.'),
							('', 'theme', 'layout', 'I', '_WIDTH_PRINT_AREA', 'Pixel width of Print Tables', '700', 'Determines the pixel width of the package outer most table when being printed.'),
							('', 'user', 'package', 'S', 'CO_INFO_02_ADDR_01', 'Company Info 02: Address Line 01', '2112 Syrinx Ave.', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_01_NAME', 'Company Info 01: Name', 'The phpCOIN Thing', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_03_ADDR_02', 'Company Info 03: Address Line 02', 'Suite OU812', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_04_CITY', 'Company Info 04: City', 'Rushville', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_05_STATE_PROV', 'Company Info 05: State/Prov', 'Pa.', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_06_POSTAL_CODE', 'Company Info 06: Postal Code', '12112', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_07_COUNTRY', 'Company Info 07: Country', 'USA', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_08_PHONE', 'Company Info 08: Phone No.', '(777)-555-2112', 'Global scope variable, currently used in invoices.'),
							('', 'user', 'package', 'S', 'CO_INFO_09_FAX', 'Company Info 09: FAX', '(777)-555-2112', 'Global scope variable, currently used in invoices.')
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
					IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table.<br>'.$_nl; }

				# Query for inserting data into new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}

				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.$eff_rows.' data rows into the new table<br>'.$_nl; }

				$_cstr .= '<br>Table modified- fields added.<br><br>';

			$_cstr .= '<br><b>Parameters Table(s) Upgrade completed.</b><br>';

		} # Parameters Table modifications completed

		#######################################################
		# Server Accounts Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['server_accounts']] )
		{
			$_cstr .= '<br><b>Server Accounts Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_DBCFG['server_accounts']." ADD `sa_ip` VARCHAR(16) NOT NULL AFTER `sa_si_id`
						, ADD `sa_path` VARCHAR(255) NOT NULL AFTER `sa_ip`
						, ADD `sa_path_temp` VARCHAR(255) NOT NULL AFTER `sa_path`
						;";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- fields added.<br><br>';

			$_cstr .= '<br><b>Server Accounts Table(s) Upgrade completed.</b><br>';

		} # Server Accounts Table modifications completed

		#######################################################
		# Server Info Table modifications:
		IF ( $_TBLEXIST[$_DBCFG['server_info']] )
		{
			$_cstr .= '<br><b>Server Info Table(s) Upgrade started.</b><br>';

			# Modify Table for new format of data
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_DBCFG['server_info']." ADD `si_ns_01` VARCHAR(50) NOT NULL AFTER `si_ip`
						, ADD `si_ns_02` VARCHAR(50) NOT NULL AFTER `si_ns_01`
						;";
				$result 	= $db_coin->db_query_execute($query);

				$_cstr .= '<br>Table modified- fields added.<br><br>';

			$_cstr .= '<br><b>Server Info Table(s) Upgrade completed.</b><br>';

		} # Server Info Table modifications completed

		# Final Output data
			$_tstr	= 'Database Schema Upgrade';
			$_mstr	= '<a href="'.PKG_URL_BASE.'">'.$_TCFG['_IMG_HOME_M'].'</a>';
			$_mstr	.= '<a href="'.PKG_URL_BASE.'admin.php">'.$_TCFG['_IMG_ADMIN_M'].'</a>';

			$_out	= '<br>'.$_nl;
			$_out	.= do_install_block_it ($_tstr, $_cstr, 1, $_mstr, '1');
			$_tstr	= ''; $_cstr	= ''; $_mstr	= '';
			echo $_out;

	} # proceed flag


/**************************************************************
 * Close Page
**************************************************************/

	$_out	= do_install_page_close('1');
	echo $_out;


# For php < 4.3 compatability
# replaces html_entity_decode
function unhtmlentities($string) {
	 $trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}
?>