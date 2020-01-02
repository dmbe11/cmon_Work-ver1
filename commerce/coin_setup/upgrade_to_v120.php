<?php
/**
 * Installation: Upgrade to phpCOIN v1.2.0 from v1.1.1
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
		IF ( !isset($_SERVER)	)	{ $_SERVER	= $HTTP_SERVER_VARS;	}
		IF ( !isset($_POST)		)	{ $_POST		= $HTTP_POST_VARS;		}
		IF ( !isset($_GET)		)	{ $_GET		= $HTTP_GET_VARS;		}
		IF ( !isset($_COOKIE)	)	{ $_COOKIE	= $HTTP_COOKIE_VARS;	}
		IF ( !isset($_FILES)	)	{ $_FILES		= $HTTP_POST_FILES;		}
		IF ( !isset($_ENV)		)	{ $_ENV		= $HTTP_ENV_VARS;		}
		IF ( !isset($_SESSION)	)	{ $_SESSION	= $HTTP_SESSION_VARS;	}

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
		require_once ('setup_lib.php');

	# Validate required files and paths
		IF (!isset($_SERVER))	{ $_SERVER = $HTTP_SERVER_VARS; }
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

	# Include new lang_config file
		require_once($_CCFG['_PKG_PATH_LANG']."lang_config.php");

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
		$db_coin = new db_funcs();

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
	$_options .= '<div align="center">'.$_nl;
	$_options .= '<table width="80%">'.$_nl;

	$_options .= '<tr><td class="TP1SML_NC" colspan="2">'.$_nl;
		IF ( $_GPV[up_all]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_all" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= '<b>'.$_sp.'Check for complete upgrade (recommended).</b>'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NC" colspan="2">'.$_nl;
		$_options .= '<b>(Or Uncheck ALL and Select Below)</b>'.$_nl;
		$_options .= '<br>'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NC" colspan="2">'.$_nl;
		$_options .= '<hr>'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_admin]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_admin" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Admins Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_mail]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_mail" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Mail Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_articles]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_articles" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Articles Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_menu_blocks]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_menu_blocks" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Menu Blocks Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_banned]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_banned" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Banned Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_orders]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_orders" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Orders Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_clients]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_clients" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Clients Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_parameters]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_parameters" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Parameters Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_components]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_components" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Components Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_products]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_products" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Products Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_faq]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_faq" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade FAQ Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_sessions]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_sessions" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Sessions Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_faq_qa]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_faq_qa" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade FAQ QA Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_server_info]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_server_info" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Server Info Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_helpdesk]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_helpdesk" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Helpdesk Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_vendors]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_vendors" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Vendors Table(s).'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_invoices]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_invoices" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Upgrade Invoices Table(s).'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		$_options .= '<br>'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_invc_trans_new]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_invc_trans_new" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Create Invoice Transaction Data.'.$_nl;
		$_options .= '</td><td class="TP1SML_NL">'.$_nl;
		IF ( $_GPV[up_adv_set_fields]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
		$_options .= '<INPUT TYPE=CHECKBOX NAME="up_adv_set_fields" value="1"'.$_set.' border="0">'.$_nl;
		$_options .= 'Update Misc Field Sizes.'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	$_options .= '<tr><td class="TP1SML_NC" colspan="2">'.$_nl;
		$_options .= '<hr>'.$_nl;
	$_options .= '</td></tr>'.$_nl;

	IF ( $_GPV[adv]=='yeah')
		{
			$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
				IF ( $_GPV[up_adv_01]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
				$_options .= '<INPUT TYPE=CHECKBOX NAME="up_adv_01" value="1"'.$_set.' border="0">'.$_nl;
				$_options .= 'Drop old Server Accounts.'.$_nl;
				$_options .= '</td><td class="TP1SML_NL">'.$_nl;
				IF ( $_GPV[up_adv_02]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
				$_options .= '<INPUT TYPE=CHECKBOX NAME="up_adv_02" value="1"'.$_set.' border="0">'.$_nl;
				$_options .= 'Flush Sessions Tables'.$_nl;
			$_options .= '</td></tr>'.$_nl;

			$_options .= '<tr><td class="TP1SML_NL">'.$_nl;
				IF ( $_GPV[up_adv_03]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
				$_options .= '<INPUT TYPE=CHECKBOX NAME="up_adv_03" value="1"'.$_set.' border="0">'.$_nl;
				$_options .= 'Copy clients_domains to _copy'.$_nl;
				$_options .= '</td><td class="TP1SML_NL">'.$_nl;
				IF ( $_GPV[up_adv_04]==1 ) { $_set = ' CHECKED'; } ELSE { $_set = ''; }
				$_options .= '<INPUT TYPE=CHECKBOX NAME="up_adv_04" value="1"'.$_set.' border="0">'.$_nl;
				$_options .= 'future'.$_nl;
			$_options .= '</td></tr>'.$_nl;

			$_options .= '<tr><td class="TP1SML_NC" colspan="2">'.$_nl;
				$_options .= '<hr>'.$_nl;
			$_options .= '</td></tr>'.$_nl;

		}

	$_options .= '</table>'.$_nl;
	$_options .= '<br>'.$_nl;
	$_options .= '</div>'.$_nl;


# Do cheesy login to check against db password (non-encrypt)
	IF ( !$_GPV[stage] && $_db_check==1 )
	{
		# Create Login Form
			$_cstr .= '<table width="100%"><tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>Select Complete or Perform Partial Upgrades:</b><br><br>'.$_nl;
			$_cstr .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="login">'.$_nl;
			$_cstr .= $_options;
			$_cstr .= '<b>Enter Your Database Password to Complete Upgrade:</b><br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;(upgrade will begin on clicking upgrade- no additional prompts)<br><br>'.$_nl;
			$_cstr .= '<b>Password:&nbsp;&nbsp;<INPUT class="PMED_NL" type="password" name="password" size="20" maxlength="20">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="adv" value="'.$_GPV[adv].'">'.$_nl;
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

					$_cstr .= '<b>Select Complete or Perform Partial Upgrades:</b><br><br>'.$_nl;
					$_cstr .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="login">'.$_nl;
					$_cstr .= $_options;
					$_cstr .= '<b>Enter Your Database Password to Complete Upgrade:</b><br>'.$_nl;
					$_cstr .= '&nbsp;&nbsp;(upgrade will begin on clicking upgrade- no additional prompts)<br><br>'.$_nl;
					$_cstr .= '<b>Password:&nbsp;&nbsp;<INPUT class="PMED_NL" type="password" name="password" size="20" maxlength="20">'.$_nl;
					$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
					$_cstr .= '<INPUT TYPE=hidden name="adv" value="'.$_GPV[adv].'">'.$_nl;
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
				IF (  $_TBL == $_DBCFG['banned'] )				{ $_TBLEXIST[$_DBCFG['banned']] = 1; }
				IF (  $_TBL == $_DBCFG['categories'] )			{ $_TBLEXIST[$_DBCFG['categories']] = 1; }
				IF (  $_TBL == $_DBCFG['clients'] )				{ $_TBLEXIST[$_DBCFG['clients']] = 1; }
				IF (  $_TBL == $_DBCFG['clients_domains'] )		{ $_TBLEXIST[$_DBCFG['clients_domains']] = 1; }
				IF (  $_TBL == $_DBCFG['clients_domains']."_copy" ){ $_TBLEXIST[$_DBCFG['clients_domains_copy']] = 1; }
				IF (  $_TBL == $_DBCFG['clients_status'] )		{ $_TBLEXIST[$_DBCFG['clients_status']] = 1; }
				IF (  $_TBL == $_DBCFG['components'] )			{ $_TBLEXIST[$_DBCFG['components']] = 1; }
				IF (  $_TBL == $_DBCFG['domains'] )				{ $_TBLEXIST[$_DBCFG['domains']] = 1; }
				IF (  $_TBL == $_DBCFG['faq'] )					{ $_TBLEXIST[$_DBCFG['faq']] = 1; }
				IF (  $_TBL == $_DBCFG['faq_qa'] )				{ $_TBLEXIST[$_DBCFG['faq_qa']] = 1; }
				IF (  $_TBL == $_DBCFG['icons'] )				{ $_TBLEXIST[$_DBCFG['icons']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices'] )			{ $_TBLEXIST[$_DBCFG['invoices']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices_items'] )		{ $_TBLEXIST[$_DBCFG['invoices_items']] = 1; }
				IF (  $_TBL == $_DBCFG['invoices_trans'] )		{ $_TBLEXIST[$_DBCFG['invoices_trans']] = 1; }
				IF (  $_TBL == $_DBCFG['helpdesk'] )			{ $_TBLEXIST[$_DBCFG['helpdesk']] = 1; }
				IF (  $_TBL == $_DBCFG['helpdesk_msgs'] )		{ $_TBLEXIST[$_DBCFG['helpdesk_msgs']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_archive'] )		{ $_TBLEXIST[$_DBCFG['mail_archive']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_contacts'] )		{ $_TBLEXIST[$_DBCFG['mail_contacts']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_queue'] )			{ $_TBLEXIST[$_DBCFG['mail_queue']] = 1; }
				IF (  $_TBL == $_DBCFG['mail_templates'] )		{ $_TBLEXIST[$_DBCFG['mail_templates']] = 1; }
				IF (  $_TBL == $_DBCFG['menu_blocks'] )			{ $_TBLEXIST[$_DBCFG['menu_blocks']] = 1; }
				IF (  $_TBL == $_DBCFG['menu_blocks_items'] )	{ $_TBLEXIST[$_DBCFG['menu_blocks_items']] = 1; }
				IF (  $_TBL == $_DBCFG['orders'] )				{ $_TBLEXIST[$_DBCFG['orders']] = 1; }
				IF (  $_TBL == $_DBCFG['orders_sessions'] )		{ $_TBLEXIST[$_DBCFG['orders_sessions']] = 1; }
				IF (  $_TBL == $_DBCFG['pages'] )				{ $_TBLEXIST[$_DBCFG['pages']] = 1; }
				IF (  $_TBL == $_DBCFG['parameters'] )			{ $_TBLEXIST[$_DBCFG['parameters']] = 1; }
				IF (  $_TBL == $_DBCFG['products'] )			{ $_TBLEXIST[$_DBCFG['products']] = 1; }
				IF (  $_TBL == $_DBCFG['sessions'] )			{ $_TBLEXIST[$_DBCFG['sessions']] = 1; }
				IF (  $_TBL == $_DBCFG['server_accounts'] )		{ $_TBLEXIST[$_DBCFG['server_accounts']] = 1; }
				IF (  $_TBL == $_DBCFG['server_info'] )			{ $_TBLEXIST[$_DBCFG['server_info']] = 1; }
				IF (  $_TBL == $_DBCFG['site_info'] )			{ $_TBLEXIST[$_DBCFG['site_info']] = 1; }
				IF (  $_TBL == $_DBCFG['topics'] )				{ $_TBLEXIST[$_DBCFG['topics']] = 1; }
				IF (  $_TBL == $_DBCFG['vendors'] )				{ $_TBLEXIST[$_DBCFG['vendors']] = 1; }
				IF (  $_TBL == $_DBCFG['vendors_prods'] )		{ $_TBLEXIST[$_DBCFG['vendors_prods']] = 1; }
				IF (  $_TBL == $_DBCFG['versions'] )			{ $_TBLEXIST[$_DBCFG['versions']] = 1; }
			}
			IF ( $result) { $result = mysql_free_result($result); }

		#######################################################
		# Enable Table Updates
		#######################################################
		$_myflag = 0;
		IF ( $_GPV[up_all] == 1 ) {
			$_TBLUPDATE[$_DBCFG['admins']]				= 1;
			$_TBLUPDATE[$_DBCFG['articles']]			= 1;
			$_TBLUPDATE[$_DBCFG['banned']]				= 1;
			$_TBLUPDATE[$_DBCFG['clients']]				= 1;
			$_TBLUPDATE[$_DBCFG['clients_domains']]		= 1;
			$_TBLUPDATE[$_DBCFG['components']]			= 1;
			$_TBLUPDATE[$_DBCFG['faq']]					= 1;
			$_TBLUPDATE[$_DBCFG['faq_qa']]				= 1;
			$_TBLUPDATE[$_DBCFG['helpdesk']]			= 1;
			$_TBLUPDATE[$_DBCFG['invoices']]			= 1;
			$_TBLUPDATE[$_DBCFG['invoices_items']]		= 1;
			$_TBLUPDATE[$_DBCFG['invoices_trans']]		= 1;
			$_TBLUPDATE[$_DBCFG['invc_trans_new']]		= 1;
			$_TBLUPDATE[$_DBCFG['mail_archive']]		= 1;
			$_TBLUPDATE[$_DBCFG['mail_contacts']]		= 1;
			$_TBLUPDATE[$_DBCFG['mail_queue']]			= 1;
			$_TBLUPDATE[$_DBCFG['mail_templates']]		= 1;
			$_TBLUPDATE[$_DBCFG['menu_blocks_items']]	= 1;
			$_TBLUPDATE[$_DBCFG['orders']]				= 1;
			$_TBLUPDATE[$_DBCFG['orders_sessions']]		= 1;
			$_TBLUPDATE[$_DBCFG['parameters']]			= 1;
			$_TBLUPDATE[$_DBCFG['products']]			= 1;
			$_TBLUPDATE[$_DBCFG['server_info']]			= 1;
			$_TBLUPDATE[$_DBCFG['site_info']]			= 1;
			$_TBLUPDATE[$_DBCFG['sessions']]			= 1;
			$_TBLUPDATE[$_DBCFG['vendors']]				= 1;
			$_TBLUPDATE[$_DBCFG['versions']]			= 1;
			$_GPV[up_adv_set_fields] 					= 1;
		} ELSE {
			IF ( $_GPV[up_admins] == 1 ) 		{ $_TBLUPDATE[$_DBCFG['admins']] = 1; }			ELSE { $_TBLUPDATE[$_DBCFG['admins']] = 0; }
			IF ( $_GPV[up_articles] == 1 )		{ $_TBLUPDATE[$_DBCFG['articles']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['articles']] = 0; }
			IF ( $_GPV[up_banned] == 1 )		{ $_TBLUPDATE[$_DBCFG['banned']] = 1; }			ELSE { $_TBLUPDATE[$_DBCFG['banned']] = 0; }
			IF ( $_GPV[up_clients] == 1 )
					{ $_TBLUPDATE[$_DBCFG['clients']] = 1; $_TBLUPDATE[$_DBCFG['clients_domains']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['clients']] = 0; $_TBLUPDATE[$_DBCFG['clients_domains']] = 0; }
			IF ( $_GPV[up_components] == 1 )	{ $_TBLUPDATE[$_DBCFG['components']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['components']] = 0; }
			IF ( $_GPV[up_faq] == 1 )			{ $_TBLUPDATE[$_DBCFG['faq']] = 1; }			ELSE { $_TBLUPDATE[$_DBCFG['faq']] = 0; }
			IF ( $_GPV[up_faq_qa] == 1 )		{ $_TBLUPDATE[$_DBCFG['faq_qa']] = 1; }			ELSE { $_TBLUPDATE[$_DBCFG['faq_qa']] = 0; }
			IF ( $_GPV[up_helpdesk] == 1 )		{ $_TBLUPDATE[$_DBCFG['helpdesk']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['helpdesk']] = 0; }
			IF ( $_GPV[invoices] == 1 )
					{ $_TBLUPDATE[$_DBCFG['invoices']] = 1; $_TBLUPDATE[$_DBCFG['invoices_items']] = 1; $_TBLUPDATE[$_DBCFG['invoices_trans']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['invoices']] = 0; $_TBLUPDATE[$_DBCFG['invoices_items']] = 0; $_TBLUPDATE[$_DBCFG['invoices_trans']] = 0; }
			IF ( $_GPV[invoices] == 1 || $_GPV[up_invc_trans_new] == 1 )
					{ $_TBLUPDATE[$_DBCFG['invc_trans_new']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['invc_trans_new']] = 0; }
			IF ( $_GPV[up_mail] == 1 )
					{ $_TBLUPDATE[$_DBCFG['mail_archive']] = 1; $_TBLUPDATE[$_DBCFG['mail_contacts']] = 1; $_TBLUPDATE[$_DBCFG['mail_queue']] = 1; $_TBLUPDATE[$_DBCFG['mail_templates']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['mail_archive']] = 0; $_TBLUPDATE[$_DBCFG['mail_contacts']] = 0; $_TBLUPDATE[$_DBCFG['mail_queue']] = 0; $_TBLUPDATE[$_DBCFG['mail_templates']] = 0;}
			IF ( $_GPV[up_menu_blocks] == 1 )
					{ $_TBLUPDATE[$_DBCFG['menu_blocks']] = 1; $_TBLUPDATE[$_DBCFG['menu_blocks_items']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['menu_blocks']] = 0; $_TBLUPDATE[$_DBCFG['menu_blocks_items']] = 0; }
			IF ( $_GPV[up_orders] == 1 )
					{ $_TBLUPDATE[$_DBCFG['orders']] = 1; $_TBLUPDATE[$_DBCFG['orders_sessions']] = 1; }
			ELSE	{ $_TBLUPDATE[$_DBCFG['orders']] = 0; $_TBLUPDATE[$_DBCFG['orders_sessions']] = 0; }
			IF ( $_GPV[up_parameters] == 1 )	{ $_TBLUPDATE[$_DBCFG['parameters']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['parameters']] = 0; }
			IF ( $_GPV[up_products] == 1 )		{ $_TBLUPDATE[$_DBCFG['products']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['products']] = 0; }
			IF ( $_GPV[up_server_info] == 1 )	{ $_TBLUPDATE[$_DBCFG['server_info']] = 1; }	ELSE { $_TBLUPDATE[$_DBCFG['server_info']] = 0; }
			IF ( $_GPV[up_sessions] == 1 )		{ $_TBLUPDATE[$_DBCFG['sessions']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['sessions']] = 0; }
			IF ( $_GPV[up_vendors] == 1 )		{ $_TBLUPDATE[$_DBCFG['vendors']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['vendors']] = 0; }
			IF ( $_GPV[up_versions] == 1 )		{ $_TBLUPDATE[$_DBCFG['versions']] = 1; }		ELSE { $_TBLUPDATE[$_DBCFG['versions']] = 1; }
		}

		#######################################################
		# Version V1.2.0 script stuff
		#######################################################
		#######################################################
		# Do a bunch of main SQL
		#######################################################

		# Table modifications: Admins
		IF ( $_TBLEXIST[$_DBCFG['admins']] && $_TBLUPDATE[$_DBCFG['admins']] == 1 )
		{
			$_cstr .= '<br><b>Admins Table(s) Upgrade started</b><br>';

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['admins']." ADD `admin_perms` INT DEFAULT '65535' NOT NULL";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['admins']." CHANGE `admin_perms` `admin_perms` INT(11) DEFAULT '0' NOT NULL";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table modified- field added';

			$_cstr .= '<br><b>Admins Table(s) Upgrade completed</b><br>';

		} # Table modifications completed: Admins

		#######################################################
		# Table modifications: Articles
		IF ( $_TBLEXIST[$_DBCFG['articles']] && $_TBLUPDATE[$_DBCFG['articles']] == 1 )
		{
			$_cstr .= '<br><b>Articles Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query 	= "ALTER TABLE ".$_DBCFG['articles']." ADD `auto_nl2br` TINYINT( 1 ) DEFAULT '1' NOT NULL";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table modified- field added';

			$_cstr .= '<br><b>Articles Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Articles

		#######################################################
		# Table Creation: Banned
		IF ( $_TBLUPDATE[$_DBCFG['banned']] == 1 )
		{
			$_cstr .= '<br><b>Banned Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['banned'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table.<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Banned
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['banned'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`banned_ip` varchar(15) NOT NULL default ''
						) TYPE=MyISAM COMMENT='Site Banned IP Address Table'
						";

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('000.000.000.000')
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				# Query for inserting data into new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.$eff_rows.' data rows into the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created';

			$_cstr .= '<br><b>Banned Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Banned

		#######################################################
		# Table modifications: Clients
		IF ( $_TBLEXIST[$_DBCFG['clients']] && $_TBLUPDATE[$_DBCFG['clients']] == 1 )
		{
			$_cstr .= '<br><b>Clients Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['clients']." ADD `cl_groups` INT(11) DEFAULT '0' NOT NULL";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table modified- field added';

			$_cstr .= '<br><b>Clients Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Clients

		#######################################################
		# Table modifications: Domains (formerly clients_domains)
		IF ( $_TBLEXIST[$_DBCFG['clients_domains']] && $_TBLUPDATE[$_DBCFG['clients_domains']] == 1 )
		{
			$_cstr .= '<br><b>Clients Domains Table(s) to Domains Conversion started</b><br>'.$_nl;

			# Some Vars
				$_DBCFG['domains_new'] = $_DBCFG['domains'];
				$_TBL_NAME_NEW = $_DBCFG['domains_new'];
				$_TBL_NAME_PRV = $_DBCFG['clients_domains'];

			# Rename Table
				$query = ""; $result= ""; $numrows = 0;
				$query 	= "ALTER TABLE ".$_TBL_NAME_PRV." RENAME ".$_TBL_NAME_NEW;
				$result = $db_coin->db_query_execute($query);
				$_cstr 	.= '<br>Table rename- from '.$_TBL_NAME_PRV.' to '.$_TBL_NAME_NEW;

			# Rename table comment
				$query = ""; $result= ""; $numrows = 0;
				$query 	= "ALTER TABLE ".$_TBL_NAME_NEW." COMMENT = 'Site Domains Module Table'";
				$result = $db_coin->db_query_execute($query);
				$_cstr 	.= '<br>Table comments corrected'.$_nl;

			# Alter field names
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_TBL_NAME_NEW."
						CHANGE `cd_id` `dom_id` INT(11) NOT NULL AUTO_INCREMENT ,
						CHANGE `cd_cl_id` `dom_cl_id` INT(11) DEFAULT '0' NOT NULL ,
						CHANGE `cd_cl_domain` `dom_domain` VARCHAR(50) NOT NULL ,
						CHANGE `cd_registrar` `dom_registrar` VARCHAR(32) NOT NULL ,
						CHANGE `cd_ts_expiration` `dom_ts_expiration` VARCHAR(10) NOT NULL ,
						CHANGE `cd_sa_id` `cd_sa_id` INT(11) DEFAULT '0' NOT NULL ,
						CHANGE `cd_sa_expiration` `dom_sa_expiration` VARCHAR(10) NOT NULL
						";
				$result = $db_coin->db_query_execute($query);
				$_cstr .= '<br>Table fields renamed as required'.$_nl;

			# Add fields
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_TBL_NAME_NEW."
						ADD `dom_status` TINYINT(2) DEFAULT '0' NOT NULL AFTER `dom_domain` ,
						ADD `dom_type` TINYINT(2) DEFAULT '0' NOT NULL AFTER `dom_status`
						";
				$result = $db_coin->db_query_execute($query);
				$_cstr .= '<br>Table fields added within other fields'.$_nl;

			# Add fields
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_TBL_NAME_NEW."
						ADD `dom_si_id` TINYINT(4) NOT NULL DEFAULT '0',
						ADD `dom_ip` VARCHAR(16) NOT NULL DEFAULT '',
						ADD `dom_path` VARCHAR(255) NOT NULL DEFAULT '',
						ADD `dom_path_temp` VARCHAR(255) NOT NULL DEFAULT '',
						ADD `dom_url_cp` VARCHAR(100) NOT NULL DEFAULT '',
						ADD `dom_user_name_cp` VARCHAR(30) NOT NULL DEFAULT '',
						ADD `dom_user_pword_cp` VARCHAR(30) NOT NULL DEFAULT '',
						ADD `dom_user_name_ftp` VARCHAR(30) NOT NULL DEFAULT '',
						ADD `dom_user_pword_ftp` VARCHAR(30) NOT NULL DEFAULT '',
						ADD `dom_allow_domains` TINYINT(4) NOT NULL DEFAULT '0',
						ADD `dom_allow_subdomains` TINYINT(4) NOT NULL DEFAULT '0',
						ADD `dom_allow_disk_space_mb` SMALLINT(6) NOT NULL DEFAULT '0',
						ADD `dom_allow_traffic_mb` SMALLINT(6) NOT NULL DEFAULT '0',
						ADD `dom_allow_mailboxes` TINYINT(4) NOT NULL DEFAULT '0',
						ADD `dom_allow_databases` TINYINT(4) NOT NULL DEFAULT '0',
						ADD `dom_enable_www_prefix` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_wu_scripting` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_webmail` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_frontpage` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_fromtpage_ssl` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_ssi` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_php` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_cgi` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_mod_perl` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_asp` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_ssl` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_stats` TINYINT(1) NOT NULL DEFAULT '0',
						ADD `dom_enable_err_docs` TINYINT(1) NOT NULL DEFAULT '0'
						";
				$result = $db_coin->db_query_execute($query);
				$_cstr .= '<br>Table fields added to end of table'.$_nl;

				# Load and update domains record with matching sacc record values
				# Do cross-table select for key fields
				# Set Query for select.
					$query = ""; $result= ""; $numrows = 0;
					$query		.= "SELECT *";
					$query		.= " FROM ".$_TBL_NAME_NEW.", ".$_DBCFG['server_accounts'];
					$query		.= " WHERE ".$_TBL_NAME_NEW.".cd_sa_id = ".$_DBCFG['server_accounts'].".sa_id";
					$result		= $db_coin->db_query_execute($query);
					$numrows	= $db_coin->db_query_numrows($result);
					# $_test	.= "<br>NumRows=".$numrows.$_nl;

				# Check Return and process results
					IF ( $numrows ) {
						while ($row = $db_coin->db_fetch_array($result))
						{
							# Rebuild Data Array with returned record: Server Account Fields
								$_TP['dom_id']						= $row[dom_id];
								$_TP['sa_id']						= $row[sa_id];
								$_TP['dom_si_id']					= $row[sa_si_id];
								$_TP['dom_ip']						= $row[sa_ip];
								$_TP['dom_path']					= $row[sa_path];
								$_TP['dom_path_temp']				= $row[sa_path_temp];
								$_TP['dom_user_name_cp']			= $row[sa_user_name_cp];
								$_TP['dom_user_pword_cp']			= $row[sa_user_pword_cp];
								$_TP['dom_user_name_ftp']			= $row[sa_user_name_ftp];
								$_TP['dom_user_pword_ftp']			= $row[sa_user_pword_ftp];
								$_TP['dom_allow_domains']			= $row[sa_allow_domains];
								$_TP['dom_allow_subdomains']		= $row[sa_allow_subdomains];
								$_TP['dom_allow_disk_space_mb']		= $row[sa_allow_disk_space_mb];
								$_TP['dom_allow_traffic_mb']		= $row[sa_allow_traffic_mb];
								$_TP['dom_allow_mailboxes']			= $row[sa_allow_mailboxes];
								$_TP['dom_allow_databases']			= $row[sa_allow_databases];
								$_TP['dom_enable_www_prefix']		= $row[sa_enable_www_prefix];
								$_TP['dom_enable_wu_scripting']		= $row[sa_enable_wu_scripting];
								$_TP['dom_enable_webmail']			= $row[sa_enable_webmail];
								$_TP['dom_enable_frontpage']		= $row[sa_enable_frontpage];
								$_TP['dom_enable_fromtpage_ssl']	= $row[sa_enable_fromtpage_ssl];
								$_TP['dom_enable_ssi']				= $row[sa_enable_ssi];
								$_TP['dom_enable_php']				= $row[sa_enable_php];
								$_TP['dom_enable_cgi']				= $row[sa_enable_cgi];
								$_TP['dom_enable_mod_perl']			= $row[sa_enable_mod_perl];
								$_TP['dom_enable_asp']				= $row[sa_enable_asp];
								$_TP['dom_enable_ssl']				= $row[sa_enable_ssl];
								$_TP['dom_enable_stats']			= $row[sa_enable_stats];
								$_TP['dom_enable_err_docs']			= $row[sa_enable_err_docs];

							# Locate and update current record
								$_qd = ""; $_rd = ""; $_nd = 0;
								$_qd	= "UPDATE ".$_TBL_NAME_NEW." SET ";
								$_qd	.= " dom_si_id = '$_TP[dom_si_id]', dom_ip = '$_TP[dom_ip]'";
								$_qd	.= ", dom_path = '$_TP[dom_path]', dom_path_temp = '$_TP[dom_path_temp]', dom_url_cp = '$_TP[dom_url_cp]'";
								$_qd	.= ", dom_user_name_cp = '$_TP[dom_user_name_cp]', dom_user_pword_cp = '$_TP[dom_user_pword_cp]'";
								$_qd	.= ", dom_user_name_ftp = '$_TP[dom_user_name_ftp]', dom_user_pword_ftp = '$_TP[dom_user_pword_ftp]'";
								$_qd	.= ", dom_allow_domains = '$_TP[dom_allow_domains]', dom_allow_subdomains = '$_TP[dom_allow_subdomains]'";
								$_qd	.= ", dom_allow_disk_space_mb = '$_TP[dom_allow_disk_space_mb]', dom_allow_traffic_mb = '$_TP[dom_allow_traffic_mb]'";
								$_qd	.= ", dom_allow_mailboxes = '$_TP[dom_allow_mailboxes]', dom_allow_databases = '$_TP[dom_allow_databases]'";
								$_qd	.= ", dom_enable_www_prefix = '$_TP[dom_enable_www_prefix]', dom_enable_wu_scripting = '$_TP[dom_enable_wu_scripting]'";
								$_qd	.= ", dom_enable_webmail = '$_TP[dom_enable_webmail]', dom_enable_frontpage = '$_TP[dom_enable_frontpage]'";
								$_qd	.= ", dom_enable_fromtpage_ssl = '$_TP[dom_enable_fromtpage_ssl]', dom_enable_ssi = '$_TP[dom_enable_ssi]'";
								$_qd	.= ", dom_enable_php = '$_TP[dom_enable_php]', dom_enable_cgi = '$_TP[dom_enable_cgi]'";
								$_qd	.= ", dom_enable_mod_perl = '$_TP[dom_enable_mod_perl]', dom_enable_asp = '$_TP[dom_enable_asp]'";
								$_qd	.= ", dom_enable_ssl = '$_TP[dom_enable_ssl]', dom_enable_stats = '$_TP[dom_enable_stats]'";
								$_qd	.= ", dom_enable_err_docs = '$_TP[dom_enable_err_docs]'";
								$_qd	.= " WHERE dom_id = ".$_TP[dom_id];
								$_rd 	= $db_coin->db_query_execute($_qd) OR DIE("Unable to complete request");
								$_nd	= $db_coin->db_query_affected_rows ();

							# Counter
								$_merge_count++;
						}
					} # End Get Server Account Record
				$_cstr .= '<br>Merged records: '.$_merge_count.$_nl;

			# Redo primary key
				$query = ""; $result= ""; $numrows = 0;
				$query 	= "ALTER TABLE ".$_TBL_NAME_NEW." DROP PRIMARY KEY, ADD PRIMARY KEY ( `dom_id` )";
				$result = $db_coin->db_query_execute($query);
				$_cstr 	.= '<br>Table primary key corrected'.$_nl;

			# Drop final old field
				$query = ""; $result= ""; $numrows = 0;
				$query = "ALTER TABLE ".$_TBL_NAME_NEW." DROP `cd_sa_id`";
				$result = $db_coin->db_query_execute($query);
				$_cstr .= '<br>Table old field dropped'.$_nl;

			$_cstr .= '<br><b>Clients Domains Table(s) to Domains Conversion completed.</b><br>'.$_nl;

		} # Table modifications completed: Clients Domains to Domains

		#######################################################
		# Table modifications: Components
		IF ( $_TBLEXIST[$_DBCFG['components']] && $_TBLUPDATE[$_DBCFG['components']] == 1 )
		{
			$_cstr .= '<br><b>Components Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['components']." ADD `comp_isuser` TINYINT( 1 ) DEFAULT '0' NOT NULL
					, ADD `comp_status` TINYINT( 1 ) DEFAULT '1' NOT NULL
					";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table modified- field added'.$_nl;

			$_cstr .= '<br><b>Components Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Components

		#######################################################
		# Table modifications: FAQ
		IF ( $_TBLEXIST[$_DBCFG['faq']] && $_TBLUPDATE[$_DBCFG['faq']] == 1 )
		{
			$_cstr .= '<br><b>FAQ Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query 	= "ALTER TABLE ".$_DBCFG['faq']."
					ADD `faq_admin` TINYINT( 1 ) DEFAULT '0' NOT NULL AFTER `faq_status`
					, ADD `faq_user` TINYINT( 1 ) DEFAULT '0' NOT NULL AFTER `faq_admin`
					";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table fields added'.$_nl;

			$_cstr .= '<br><b>FAQ Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: FAQ

		#######################################################
		# Table modifications: FAQ QA
		IF ( $_TBLEXIST[$_DBCFG['faq_qa']] && $_TBLUPDATE[$_DBCFG['faq_qa']] == 1 )
		{
			$_cstr .= '<br><b>FAQ QA Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['faq_qa']." ADD `faqqa_auto_nl2br` TINYINT( 1 ) DEFAULT '1' NOT NULL";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			$_cstr .= '<br><b>FAQ QA Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: FAQ QA

		#######################################################
		# Table modifications: Helpdesk
		IF ( $_TBLEXIST[$_DBCFG['helpdesk']] && $_TBLUPDATE[$_DBCFG['helpdesk']] == 1 )
		{
			$_cstr .= '<br><b>Helpdesk Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['helpdesk']." ADD `hd_tt_cl_email` VARCHAR(50) NOT NULL AFTER `hd_tt_cl_id`";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			$_cstr .= '<br><b>Helpdesk Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Helpdesk

		#######################################################
		# Table modifications: Invoices
		IF ( $_TBLEXIST[$_DBCFG['invoices']] && $_TBLUPDATE[$_DBCFG['invoices']] == 1 )
		{
			$_cstr .= '<br><b>Invoices Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['invoices']."
					ADD `invc_total_paid` DECIMAL(10,2) DEFAULT '0.00' NOT NULL AFTER `invc_total_cost`,
					CHANGE `invc_bill_cycle` `invc_bill_cycle` TINYINT(1) DEFAULT '1' NOT NULL,
					ADD `invc_recurring` TINYINT(1) DEFAULT '0' NOT NULL AFTER `invc_bill_cycle`,
					ADD `invc_recurr_proc` TINYINT(0) DEFAULT '0' NOT NULL AFTER `invc_recurring`,
					ADD `invc_tax_autocalc` TINYINT(1) DEFAULT '1' NOT NULL AFTER `invc_tax_02_amount`
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field(s) modified / added'.$_nl;

			$_cstr .= '<br><b>Invoices Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Invoices

		#######################################################
		# Table modifications: Invoices Items
		IF ( $_TBLEXIST[$_DBCFG['invoices_items']] && $_TBLUPDATE[$_DBCFG['invoices_items']] == 1 )
		{
			$_cstr .= '<br><b>Invoices Items Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['invoices_items']."
					ADD `ii_apply_tax_01` TINYINT(1) DEFAULT '1' NOT NULL
					, ADD `ii_apply_tax_02` TINYINT(1) DEFAULT '1' NOT NULL
					, ADD `ii_calc_tax_02_pb` TINYINT(1) DEFAULT '0' NOT NULL
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field(s) modified / added'.$_nl;

			$_cstr .= '<br><b>Invoices Items Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Invoices Items

		#######################################################
		# Table Creation: Invoices Trans
		IF ( $_TBLUPDATE[$_DBCFG['invoices_trans']] == 1 || $_TBLUPDATE[$_DBCFG['invc_trans_new']] == 1)
		{
			$_cstr .= '<br><b>Invoices Trans Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['invoices_trans'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Invoices Trans
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['invoices_trans'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`it_id` bigint(11) NOT NULL auto_increment,
							`it_ts` varchar(10) default NULL,
							`it_invc_id` int(11) NOT NULL default '0',
							`it_type` tinyint(2) NOT NULL default '0',
							`it_origin` tinyint(2) NOT NULL default '0',
							`it_desc` varchar(50) NOT NULL default '',
							`it_amount` decimal(10,2) NOT NULL default '0.00',
							PRIMARY KEY  (`it_id`)
						) TYPE=MyISAM COMMENT='Site Invoices Module Transactions Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			# Code for inserting debit for each invoice, and credit for each paid
				# Load and update domains record with matching sacc record values
				# Do cross-table select for key fields
				# Set Query for select.
					$query = ""; $result= ""; $numrows = 0;
					$query		.= "SELECT *";
					$query		.= " FROM ".$_DBCFG['invoices'];
					$query		.= " ORDER BY invc_id ASC";
					$result		= $db_coin->db_query_execute($query);
					$numrows	= $db_coin->db_query_numrows($result);
					# $_test	.= "<br>NumRows=".$numrows.$_nl;

				# Check Return and process results
					IF ( $numrows ) {
						while ($row = $db_coin->db_fetch_array($result))
						{
							# Rebuild Data Array with returned record: Server Account Fields
								$_TP['invc_id']					= $row[invc_id];
								$_TP['invc_status']				= $row[invc_status];
								$_TP['invc_total_cost']			= $row[invc_total_cost];
								$_TP['invc_ts']					= $row[invc_ts];
								$_TP['invc_ts_paid']			= $row[invc_ts_paid];

							# Do one insert as debit for initial invoice amount.
								$_TP['it_ts']					= $_TP[invc_ts];
								$_TP['it_invc_id']				= $_TP[invc_id];
								$_TP['it_type']					= 0;
								$_TP['it_origin']				= 0;
								$_TP['it_desc']					= 'Invoice ID:'.$sp.$_TP[invc_id];
								$_TP['it_amount']				= $_TP[invc_total_cost];

								# Insert Invoice Debit Transaction
									$q_it = ""; $r_it = ""; $n_it = 0;
									$q_it = "INSERT INTO ".$_DBCFG['invoices_trans']." (";
									$q_it .= "it_ts, it_invc_id, it_type";
									$q_it .= ", it_origin, it_desc, it_amount";
									$q_it .= ") VALUES ( ";
									$q_it .= "'$_TP[it_ts]','$_TP[it_invc_id]','$_TP[it_type]'";
									$q_it .= ",'$_TP[it_origin]','$_TP[it_desc]','$_TP[it_amount]'";
									$q_it .= ")";
									$r_it = $db_coin->db_query_execute($q_it);
									$n_it = $db_coin->db_query_numrows($r_it);

							# Do one insert as payment if status paid and set amount paid to invoice amount.
								IF ( $_TP['invc_status'] == $_CCFG['INV_STATUS'][3] )
									{
										$_TP['it_ts']					= $_TP[invc_ts_paid];
										$_TP['it_invc_id']				= $_TP[invc_id];
										$_TP['it_type']					= 2;
										$_TP['it_origin']				= 1;
										$_TP['it_desc']					= 'Payment:';
										$_TP['it_amount']				= $_TP[invc_total_cost];

										# Insert Invoice Debit Transaction
											$q_it = ""; $r_it = ""; $n_it = 0;
											$q_it = "INSERT INTO ".$_DBCFG['invoices_trans']." (";
											$q_it .= "it_ts, it_invc_id, it_type";
											$q_it .= ", it_origin, it_desc, it_amount";
											$q_it .= ") VALUES ( ";
											$q_it .= "'$_TP[it_ts]','$_TP[it_invc_id]','$_TP[it_type]'";
											$q_it .= ",'$_TP[it_origin]','$_TP[it_desc]','$_TP[it_amount]'";
											$q_it .= ")";
											$r_it = $db_coin->db_query_execute($q_it);
											$n_it = $db_coin->db_query_numrows($r_it);
									}

							# Counter
								$_merge_count++;
						}
					} # End Get Server Account Record
				$_cstr .= '<br>Merged records: '.$_merge_count.$_nl;

			$_cstr .= '<br><b>Invoices Trans Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Invoices Trans

		#######################################################
		# Table Creation: Mail Archive
		IF ( $_TBLUPDATE[$_DBCFG['mail_archive']] == 1 )
		{
			$_cstr .= '<br><b>Mail Archive Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['mail_archive'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Mail Archive
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['mail_archive'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`ma_id` bigint(11) NOT NULL auto_increment,
							`ma_time_stamp` varchar(10) default NULL,
							`ma_fld_from` varchar(100) NOT NULL default '',
							`ma_fld_recip` varchar(100) NOT NULL default '',
							`ma_fld_cc` varchar(100) NOT NULL default '',
							`ma_fld_bcc` varchar(100) NOT NULL default '',
							`ma_fld_subject` varchar(100) NOT NULL default '',
							`ma_fld_message` text NOT NULL,
							PRIMARY KEY  (`ma_id`)
						) TYPE=MyISAM COMMENT='Site eMail Archive Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			$_cstr .= '<br><b>Mail Archive Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Mail Archive

		#######################################################
		# Table modifications: Mail Contacts
		IF ( $_TBLEXIST[$_DBCFG['mail_contacts']] && $_TBLUPDATE[$_DBCFG['mail_contacts']] == 1 )
		{
			$_cstr .= '<br><b>Mail Contacts Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query 	= "ALTER TABLE ".$_DBCFG['mail_contacts']."
					 ADD `mc_status` TINYINT( 1 ) DEFAULT '1' NOT NULL
					";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table fields added'.$_nl;

			$_cstr .= '<br><b>Mail Contacts Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Mail Contacts

		#######################################################
		# Table Creation: Mail Queue
		IF ( $_TBLUPDATE[$_DBCFG['mail_queue']] == 1 )
		{
			$_cstr .= '<br><b>Mail Queue Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['mail_queue'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Mail Queue
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['mail_queue'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`mq_id` int(11) NOT NULL default '0',
							`mq_time_stamp` varchar(10) default NULL,
							`mq_fld_from` varchar(100) NOT NULL default '',
							`mq_fld_recip` varchar(100) NOT NULL default '',
							`mq_fld_cc` varchar(100) NOT NULL default '',
							`mq_fld_bcc` varchar(100) NOT NULL default '',
							`mq_fld_subject` varchar(100) NOT NULL default '',
							`mq_fld_message` text NOT NULL,
							`mq_sent_flag` tinyint(1) NOT NULL default '0',
							PRIMARY KEY  (`mq_id`)
						) TYPE=MyISAM COMMENT='Site eMail Queue Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			$_cstr .= '<br><b>Mail Queue Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Mail Queue

		#######################################################
		# Table modifications: Menu Blocks Items
		IF ( $_TBLEXIST[$_DBCFG['menu_blocks_items']] && $_TBLUPDATE[$_DBCFG['menu_blocks_items']] == 1 )
		{
			$_cstr .= '<br><b>Menu Blocks Items Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query 	= "ALTER TABLE ".$_DBCFG['menu_blocks_items']."
					CHANGE `item_target_new` `item_target` TINYINT(1) DEFAULT '0' NOT NULL,
					ADD `item_type` TINYINT(1) DEFAULT '0' NOT NULL AFTER `item_target`
					";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table fields added'.$_nl;

			$_cstr .= '<br><b>Menu Blocks Items Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Menu Blocks Items

		#######################################################
		# Table modifications: Orders
		IF ( $_TBLEXIST[$_DBCFG['orders']] && $_TBLUPDATE[$_DBCFG['orders']] == 1 )
		{
			$_cstr .= '<br><b>Orders Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query 	= "ALTER TABLE ".$_DBCFG['orders']."
					ADD `ord_ip` VARCHAR(16) DEFAULT '000.000.000.000' NOT NULL AFTER `ord_ts` ,
					ADD `ord_comments` TEXT NOT NULL ,
					ADD `ord_optfld_01` VARCHAR(50) NOT NULL ,
					ADD `ord_optfld_02` VARCHAR(50) NOT NULL ,
					ADD `ord_optfld_03` VARCHAR(50) NOT NULL ,
					ADD `ord_optfld_04` VARCHAR(50) NOT NULL ,
					ADD `ord_optfld_05` VARCHAR(50) NOT NULL ,
					CHANGE `ord_domain_new` `ord_domain_action` TINYINT( 1 ) DEFAULT '0' NOT NULL
					";
			$result	= $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table fields added'.$_nl;

			$_cstr .= '<br><b>Orders Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Orders

		#######################################################
		# Table Creation: Orders Sessions
		IF ( $_TBLEXIST[$_DBCFG['orders_sessions']] && $_TBLUPDATE[$_DBCFG['orders_sessions']] == 1 )
		{
			$_cstr .= '<br><b>Orders Sessions Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['orders_sessions'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Orders Sessions
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['orders_sessions'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`os_s_id` varchar(36) NOT NULL default '',
							`os_s_time_init` int(11) NOT NULL default '0',
							`os_s_time_last` int(11) NOT NULL default '0',
							`os_s_ip` varchar(16) NOT NULL default '000.000.000.000',
							`os_ord_processed` tinyint(1) NOT NULL default '0',
							`os_ord_ret_processed` tinyint(1) NOT NULL default '0',
							`os_ord_id` int(11) NOT NULL default '0',
							`os_ord_ts` varchar(10) default NULL,
							`os_ord_status` varchar(20) NOT NULL default 'pending',
							`os_ord_cl_id` int(11) NOT NULL default '0',
							`os_ord_company` varchar(50) NOT NULL default '',
							`os_ord_name_first` varchar(20) NOT NULL default '',
							`os_ord_name_last` varchar(20) NOT NULL default '',
							`os_ord_addr_01` varchar(50) NOT NULL default '',
							`os_ord_addr_02` varchar(50) NOT NULL default '',
							`os_ord_city` varchar(50) NOT NULL default '',
							`os_ord_state_prov` varchar(50) NOT NULL default '',
							`os_ord_country` varchar(50) NOT NULL default '',
							`os_ord_zip_code` varchar(12) NOT NULL default '',
							`os_ord_phone` varchar(20) NOT NULL default '',
							`os_ord_email` varchar(50) NOT NULL default '',
							`os_ord_domain` varchar(50) NOT NULL default '',
							`os_ord_domain_action` tinyint(1) NOT NULL default '0',
							`os_ord_user_name` varchar(20) NOT NULL default '',
							`os_ord_user_pword` varchar(50) NOT NULL default '',
							`os_ord_vendor_id` smallint(6) NOT NULL default '1',
							`os_ord_prod_id` smallint(6) NOT NULL default '0',
							`os_ord_unit_cost` decimal(10,2) NOT NULL default '0.00',
							`os_ord_accept_tos` tinyint(1) NOT NULL default '0',
							`os_ord_accept_aup` tinyint(1) NOT NULL default '0',
							`os_ord_referred_by` varchar(50) NOT NULL default '',
							`os_ord_comments` text NOT NULL,
							`os_ord_optfld_01` varchar(50) NOT NULL default '',
							`os_ord_optfld_02` varchar(50) NOT NULL default '',
							`os_ord_optfld_03` varchar(50) NOT NULL default '',
							`os_ord_optfld_04` varchar(50) NOT NULL default '',
							`os_ord_optfld_05` varchar(50) NOT NULL default '',
							`os_cor_flag` tinyint(1) NOT NULL default '0',
							`os_cor_type` varchar(32) NOT NULL default '',
							`os_cor_opt_bill_cycle` varchar(32) NOT NULL default '',
							`os_cor_opt_payment` varchar(32) NOT NULL default '',
							`os_cor_disk` smallint(6) NOT NULL default '0',
							`os_cor_disk_units` char(3) NOT NULL default 'Mb',
							`os_cor_traffic` smallint(6) NOT NULL default '0',
							`os_cor_traffic_units` char(3) NOT NULL default 'Gb',
							`os_cor_dbs` smallint(6) NOT NULL default '0',
							`os_cor_mailboxes` smallint(6) NOT NULL default '0',
							`os_cor_unique_ip` tinyint(1) NOT NULL default '0',
							`os_cor_shop_cart` tinyint(1) NOT NULL default '0',
							`os_cor_sec_cert` tinyint(1) NOT NULL default '0',
							`os_cor_site_pages` tinyint(4) NOT NULL default '0',
							`os_cor_comments` text NOT NULL,
							`os_cor_optfld_01` varchar(50) NOT NULL default '',
							`os_cor_optfld_02` varchar(50) NOT NULL default '',
							`os_cor_optfld_03` varchar(50) NOT NULL default '',
							`os_cor_optfld_04` varchar(50) NOT NULL default '',
							`os_cor_optfld_05` varchar(50) NOT NULL default '',
							PRIMARY KEY  (`os_s_id`)
						) TYPE=MyISAM COMMENT='Site Orders Module Sessions Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			$_cstr .= '<br><b>Orders Sessions Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Orders Sessions

		#######################################################
		# Table modifications: Parameters
		IF ( $_TBLEXIST[$_DBCFG['parameters']] && $_TBLUPDATE[$_DBCFG['parameters']] == 1 )
		{
			$_cstr .= '<br><b>Parameters Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['parameters']." CHANGE `parm_id` `parm_id` SMALLINT(6) NOT NULL AUTO_INCREMENT";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			/**************************************************************
				 * Table Create and Data Arrays For: parameters
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['parameters'];

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('', 'common', 'API', 'B', 'APIO_MASTER_ENABLE', 'API Output Master Enable', '0', 'Determines if the API Output function triggers (all) are enabled.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_COR_PROC_ENABLE', 'API Output Enable: Order COR Proc', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_NEW_CLIENT_ENABLE', 'API Output Enable: Order New Client', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_NEW_DOMAIN_ENABLE', 'API Output Enable: Order New Domain', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_OUT_PROC_ENABLE', 'API Output Enable: Order Out Proc', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_RET_PROC_ENABLE', 'API Output Enable: Order Return Proc', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_CLIENT_NEW_ENABLE', 'API Output Enable: Client Created', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_CLIENT_DEL_ENABLE', 'API Output Enable: Client Deleted', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_DOMAIN_NEW_ENABLE', 'API Output Enable: Domain Created', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_DOMAIN_DEL_ENABLE', 'API Output Enable: Domain Deleted', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_NEW_ENABLE', 'API Output Enable: Order Created', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_ORDER_DEL_ENABLE', 'API Output Enable: Order Deleted', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_PRODUCT_NEW_ENABLE', 'API Output Enable: Product Created', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_PRODUCT_DEL_ENABLE', 'API Output Enable: Product Deleted', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_TRANS_NEW_ENABLE', 'API Output Enable: Trans Created', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'API', 'B', 'APIO_TRANS_DEL_ENABLE', 'API Output Enable: Trans Deleted', '0', 'Determines if the API Output function trigger is enabled for the indicated item.'),
							('', 'common', 'clients', 'S', 'CLIENT_DEF_STATUS_NEW', 'Client Default Status on Create', 'pending', 'Determines the default client status used when order placed, new auto-created.'),
							('', 'common', 'clients', 'I', 'CLIENT_MAX_LEN_UNAME', 'Client Max. Characters for User Name', '16', 'Determines the maximum number of characters permitted in username form fields.'),
							('', 'common', 'clients', 'I', 'CLIENT_MAX_LEN_PWORD', 'Client Max. Characters for Password', '16', 'Determines the maximum number of characters permitted in password form fields.'),
							('', 'common', 'domains', 'I', 'IPP_DOMAINS', 'Listing Items Per Page for: Domains', '10', 'Determines the number of domains that are displayed in listing form on a page.'),
							('', 'common', 'domains', 'I', 'DOM_DEFAULT_SERVER', 'Domain Account Default Server ID', '1', 'Determines the default server id that is inserted when a domain is created.'),
							('', 'common', 'domains', 'S', 'DOM_DEFAULT_PATH', 'Domain Account Default Path', '/home/httpd/vhosts/domainname/httpdocs', 'Note: place \"domainname\" in path to have it replaced by domain name, or \"username\" in path to have it replaced by username on entry create.'),
							('', 'common', 'domains', 'B', 'DOM_EMAIL_CC_ENABLE', 'Domain Email CC Admin Enable', '1', 'Determines if the site admin will receive a copy each time a request is made to email a domain activation email.'),
							('', 'common', 'domains', 'B', 'DOM_FORCE_CHECK_TRUE', 'Domain Name Validation Force True', '0', 'Determines if the domain validation logic always returns true. Only for those who not want to validate input domain names.'),
							('', 'common', 'domains', 'S', 'DOM_DEFAULT_IP', 'Domain Account Default IP Address', '000.000.000.000', 'Determines the default domain IP address that is inserted when a domain account is created.'),
							('', 'common', 'domains', 'S', 'DOM_DEFAULT_USERNAME', 'Domain CP/FTP Default User Name', 'username', 'Determines the default CP/FTP User Name inserted when a domain account is created. Choose \"username\" to insert client user name, or \"domain\" to insert the domain name.'),
							('', 'common', 'domains', 'S', 'DOM_DEFAULT_CP_URL', 'Domain Default Control Panel URL', '', 'Determines the URL (fully qualified) that will be used for the control panel link shown in domain listings.'),
							('', 'common', 'domains', 'B', 'DOM_CP_URL_LINK_ENABLE', 'Domain Listing CP Link Enable', '1', 'Determines if the control panel link will be shown in domain listings.'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_ADMIN_REVEAL_ENABLE', 'Helpdesk Admin Reveal Identity Enable', '0', 'The setting is to enable the Admin responding to HelpDesk items to be identified by Admin information in listing and emails.'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_MSG_CC_CLIENT_ENABLE', 'Helpdesk Message Email CC Client Enable', '0', 'The setting is to email CC to the client when they submit helpdesk messages. Set to no, only the support receives the message.'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_REPLY_EMAIL_SET_LIMIT', 'Helpdesk Reply Email Limit Messages Sent', '0', 'The setting will limit the number of helpdesk messages included in the reply notice email, to the _LIMIT parameter most recent.'),
							('', 'common', 'helpdesk', 'I', 'HELPDESK_REPLY_EMAIL_LIMIT', 'Helpdesk Reply Email Messages Limit Setting', '2', 'The setting is the number of most recent messages to include in the reply notice email.'),
							('', 'common', 'helpdesk', 'B', 'HELPDESK_SHOW_CLIENT_NAME', 'Helpdesk Admin List Show Client Name', '0', 'The setting is to enable the Client column in the Admin Helpdesk listing.'),
							('', 'common', 'invoices', 'B', 'INVC_ACOPY_DELAY_ENABLE', 'Invoice Auto-Copy Delay Enable', '1', 'Determines if the auto-copy of recurring paid invoices is delayed until new invoice date is within days out setting.'),
							('', 'common', 'invoices', 'I', 'INVC_ACOPY_DAYS_OUT', 'Invoice Auto-Copy Days Out to Copy', '14', 'Determines number of days from current date to auto-copy a recurring paid invoice. Will auto-copy if the new invoice date falls within this date range.'),
							('', 'common', 'invoices', 'B', 'INVC_AUTO_COPY_ENABLE', 'Invoice Auto-Copy Recurring Enable', '1', 'Determines if the auto-copy of paid recurring invoices function is enabled when triggered by cron job or manually by command summary actions menu.'),
							('', 'common', 'invoices', 'B', 'INVC_SHOW_CLIENT_PENDING', 'Invoice Show Client Pending Invoices', '1', 'Determines if the client will see invoices that are set to pending status.'),
							('', 'common', 'invoices', 'B', 'INVC_SPLIT_TRANS_LIST_ENABLE', 'Invoice Transaction Listing Split Enable', '1', 'Determines if the invoice transaction listing is split to list charges on top, and then credits below (in seperate list).'),
							('', 'common', 'invoices', 'B', 'INVC_TAX_BY_ITEM', 'Invoice Tax By Item Enable', '1', 'Determines if the taxes are applied at the items level (each item), or just on the total of items.'),
							('', 'common', 'invoices', 'S', 'INVC_TAX_01_LABEL', 'Invoice Tax 01 Text Label', 'Tax 01:', 'Determines the text label displayed throughout the package in place of \"Tax 01\".'),
							('', 'common', 'invoices', 'R', 'INVC_TAX_01_DEF_VAL', 'Invoice Tax 01 Rate Default Value', '0.00', 'Determines the default tax rate percentage value inserted for this tax. Example 1.00 equates to 1 percent.'),
							('', 'common', 'invoices', 'S', 'INVC_TAX_02_LABEL', 'Invoice Tax 02 Text Label', 'Tax 02:', 'Determines the text label displayed throughout the package in place of \"Tax 01\".'),
							('', 'common', 'invoices', 'R', 'INVC_TAX_02_DEF_VAL', 'Invoice Tax 02 Rate Default Value', '0.00', 'Determines the default tax rate percentage value inserted for this tax. Example 1.00 equates to 1 percent.'),
							('', 'common', 'invoices', 'B', 'INVC_VIEW_SHOW_TRANS', 'Invoice View Show Transactions Enable', '1', 'Determines if the invoice transactions listing is displayed on lower portion of invoice view.'),
							('', 'common', 'orders', 'B', 'ORDERS_ACK_EMAIL_ENABLE', 'Orders Ack Email To Client Enable', '0', 'Determines if an acknowledgment email will be sent to client on new order.'),
							('', 'common', 'orders', 'B', 'ORDERS_ACK_EMAIL_ONRET', 'Orders Ack Email To Client On Return', '0', 'Determines if an acknowledgment email will be sent on return from vendor, instead of upon paylink display.'),
							('', 'common', 'orders', 'S', 'ORDERS_DEF_STATUS_NEW', 'Orders Default Status on Create', 'confirmed', 'Determines the default order status used when order placed, new auto-created.'),
							('', 'common', 'orders', 'I', 'ORDERS_FIELD_ENABLE_COR', 'Orders Form Field Enable Settings- COR', '65504', 'Determines which fields are enabled (visible) on the orders COR form.'),
							('', 'common', 'orders', 'I', 'ORDERS_FIELD_ENABLE_ORD', 'Orders Form Field Enable Settings- Order', '65504', 'Determines which fields are enabled (visible) on the orders order form. This also effects client editing forms.'),
							('', 'common', 'orders', 'B', 'ORDERS_LIST_SHOW_PROD_DESC', 'Orders Listing Show Prod. Description', '1', 'Determines if the Product description will be displayed on Orders Listings instead of the default Vendor / Product Name columns.'),
							('', 'common', 'orders', 'B', 'ORDERS_PROD_LIST_NAME_SHOW', 'Orders Product List Show Product Name', '1', 'Determines if the product name field is included in the order screen products list.'),
							('', 'common', 'orders', 'I', 'ORDERS_PROD_LIST_SORT_ORDER', 'Orders Product List Primary Sort Field', '1', 'Determines which field will be the ascending sort order for the order screen products list.'),
							('', 'common', 'orders', 'B', 'ORDERS_TOS_ENABLE', 'Orders Req. Terms Of Service (TOS) Enable', '1', 'Determines if the agree to the Terms Of Service button and requirement is visible during order placement.'),
							('', 'common', 'operation', 'B', '_PKG_EMAIL_OUT_DISABLE', 'Package Email Out Disable', '0', 'Determines if the main email functions are disabled from sending.'),
							('', 'common', 'operation', 'B', '_PKG_MODE_DEMO', 'Package Mode Enable- Demo', '0', 'Determines if the package is placed in demo mode.'),
							('', 'common', 'package', 'B', '_PKG_ENABLE_IP_BAN', 'Package Banned IP Feature Enable', '1', 'Package Banned IP Feature enables admin to create a list of banned IP addresses that can not view the site. Banned IPs are redirected to error message.'),
							('', 'common', 'package', 'B', '_PKG_ENABLE_EMAIL_ARCHIVE', 'Package Email Archive Enable', '0', 'Package Email Archive Enable set to YES will archive ALL emails sent out of the package.'),
							('', 'common', 'summary', 'I', 'CC_DOMAIN_EXP_IN_DAYS', 'Summary- Domains Expiring In Days', '30', 'The setting determines the number of days from current to show as expiring domains in summary listing.'),
							('', 'common', 'summary', 'B', 'CC_DOMAIN_EXP_LIST_ENABLE', 'Summary- Domains Expiring List Enable', '1', 'The setting enables the display of the domains expiring list in summary listing.'),
							('', 'common', 'summary', 'B', 'CC_DOMAIN_EXP_LIST_INCL_EXPRD', 'Summary- Domains Expiring List Include Expired', '1', 'The setting enables the display of the domains expiring list in summary listing to also include expired domains.'),
							('', 'common', 'summary', 'I', 'CC_SACC_EXP_IN_DAYS', 'Summary- Server Accounts Expiring In Days', '30', 'The setting determines the number of days from current to show as expiring server accounts in summary listing.'),
							('', 'common', 'summary', 'B', 'CC_SACC_EXP_LIST_ENABLE', 'Summary- Server Accounts Expiring List Enable', '1', 'The setting enables the display of the server account expiring list in summary listing.'),
							('', 'common', 'summary', 'B', 'CC_SACC_EXP_LIST_INCL_EXPRD', 'Summary- Server Accounts Expiring List Include Expired', '1', 'The setting enables the display of the server account expiring list in summary listing to also include expired server accounts.'),
							('', 'common', 'summary', 'B', 'CC_SERVER_LIST_ENABLE', 'Summary- Servers List Enable', '1', 'The setting enables the display of the servers list in summary listing.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_01', 'Header Menu Button 01: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_02', 'Header Menu Button 02: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_03', 'Header Menu Button 03: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_04', 'Header Menu Button 04: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_05', 'Header Menu Button 05: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_06', 'Header Menu Button 06: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_07', 'Header Menu Button 07: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_08', 'Header Menu Button 08: Enable', '0', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'B', '_HDR_MENU_BTTN_09', 'Header Menu Button 09: Enable', '1', 'Determines whether the button appears in the top menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_01', 'Header Menu Button 01: Link', 'index.php', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_02', 'Header Menu Button 02: Link', 'mod.php?mod=siteinfo&id=4', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_03', 'Header Menu Button 03: Link', 'mod.php?mod=articles', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_04', 'Header Menu Button 04: Link', 'mod.php?mod=mail&mode=contact', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_05', 'Header Menu Button 05: Link', 'mod.php?mod=faq', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_06', 'Header Menu Button 06: Link', 'mod.php?mod=helpdesk', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_07', 'Header Menu Button 07: Link', 'mod.php?mod=search', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_08', 'Header Menu Button 08: Link', 'index.php', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_LINK_09', 'Header Menu Button 09: Link', 'mod.php?mod=orders', 'Determines link (URL) for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_01', 'Header Menu Button 01: Image', '_IMG_MT_HOME_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_02', 'Header Menu Button 02: Image', '_IMG_MT_ABOUT_US_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_03', 'Header Menu Button 03: Image', '_IMG_MT_ARTICLES_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_04', 'Header Menu Button 04: Image', '_IMG_MT_CONTACT_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_05', 'Header Menu Button 05: Image', '_IMG_MT_FAQ_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_06', 'Header Menu Button 06: Image', '_IMG_MT_HELPDESK_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_07', 'Header Menu Button 07: Image', '_IMG_MT_SEARCH_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_08', 'Header Menu Button 08: Image', '_IMG_MT_SERVICES_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'S', '_HDR_MENU_BTTN_IMG_09', 'Header Menu Button 09: Image', '_IMG_MT_PLACE_ORDER_B', 'Determines image for the corresponding button in main menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_02', 'User Menu Button 02: Enable', '1', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_03', 'User Menu Button 03: Enable', '1', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_04', 'User Menu Button 04: Enable', '1', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_05', 'User Menu Button 05: Enable', '1', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_06', 'User Menu Button 06: Enable', '1', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_07', 'User Menu Button 07: Enable', '0', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'B', '_USR_MENU_BTTN_08', 'User Menu Button 08: Enable', '0', 'Determines whether the button appears in the user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_02', 'User Menu Button 02: Link', 'mod.php?mod=domains', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_03', 'User Menu Button 03: Link', 'mod.php?mod=orders&mode=view', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_04', 'User Menu Button 04: Link', 'mod.php?mod=invoices', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_05', 'User Menu Button 05: Link', 'mod.php?mod=helpdesk', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_06', 'User Menu Button 06: Link', 'mod.php?mod=cc', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_07', 'User Menu Button 07: Link', 'index.php', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_LINK_08', 'User Menu Button 08: Link', 'index.php', 'Determines link (URL) for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_02', 'User Menu Button 02: Image', '_IMG_MU_DOMAINS_B', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_03', 'User Menu Button 03: Image', '_IMG_MU_ORDERS_B', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_04', 'User Menu Button 04: Image', '_IMG_MU_INVOICES_B', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_05', 'User Menu Button 05: Image', '_IMG_MU_HELPDESK_B', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_06', 'User Menu Button 06: Image', '_IMG_MU_SUMMARY_B', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_07', 'User Menu Button 07: Image', 'undefined', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'buttons', 'S', '_USR_MENU_BTTN_IMG_08', 'User Menu Button 08: Image', 'undefined', 'Determines image for the corresponding button in user menu.'),
							('', 'theme', 'layout', 'B', '_ENABLE_ADMIN_LOGIN_LINK', 'Admin Login Link Button Enable', '1', 'Determines if the \"Admin\" login button link is displayed on the client login screen.'),
							('', 'theme', 'layout', 'B', '_ENABLE_BTN_MOUSEOVER', 'Enable- Form Button Mouseover', '1', 'Enables the mouseover action on all form buttons throughout the package.'),
							('', 'theme', 'layout', 'B', '_PAGE_HEADER_DATE', 'Site- Page Header Show Date', '1', 'Determines whether the date is displayed in the page header block.'),
							('', 'theme', 'layout', 'B', '_PKG_WRAPPER_ENABLE', 'Enable- Package Wrapper', '0', 'Package Wrapper Feature enables \"wrapper\" code around the phpCOIN package.')
						";

				# Query for inserting data into new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}

				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.'a bunch of'.' data rows into the new table<br>'.$_nl; }

			# Set Old Menu Button Params to undefined
				$query = ""; $result= ""; $eff_rows = 0;
				$query	= "UPDATE ".$_DBCFG['parameters']." SET parm_group = 'undefined' WHERE ( ";
				$query	.= "(parm_name = '_HDR_MENU_BTTN_HOME') OR (parm_name = '_HDR_MENU_BTTN_ABOUT')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_ARTICLES') OR (parm_name = '_HDR_MENU_BTTN_CONTACT')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_FAQ') OR (parm_name = '_HDR_MENU_BTTN_SEARCH')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_ORDER') OR (parm_name = '_HDR_MENU_BTTN_HELPDESK')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_SERVICES') OR (parm_name = '_HDR_MENU_BTTN_LINK_ABOUT')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_LINK_ARTICLES') OR (parm_name = '_HDR_MENU_BTTN_LINK_CONTACT')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_LINK_FAQ') OR (parm_name = '_HDR_MENU_BTTN_LINK_HELPDESK')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_LINK_HOME') OR (parm_name = '_HDR_MENU_BTTN_LINK_ORDER')";
				$query	.= " OR (parm_name = '_HDR_MENU_BTTN_LINK_SEARCH') OR (parm_name = '_HDR_MENU_BTTN_LINK_SERVICES')";
				$query	.= " )";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Old menu button parameters set to undefined.'.$_nl;

			# Set Old SACC Params to undefined
				$query = ""; $result= ""; $eff_rows = 0;
				$query	= "UPDATE ".$_DBCFG['parameters']." SET parm_group = 'undefined' WHERE ( ";
				$query	.= "(parm_group_sub = 'saccs')";
				$query	.= " )";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Old sacc parameters set to undefined.'.$_nl;

			# Set Domain Force True Params to undefined
				$query = ""; $result= ""; $eff_rows = 0;
				$query	= "UPDATE ".$_DBCFG['parameters']." SET parm_group = 'undefined' WHERE ( ";
				$query	.= "(parm_name = '_PKG_DOMAIN_CHECK_TRUE')";
				$query	.= " )";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Old force domain true parameter set to undefined.'.$_nl;

			# Set Orders AUP Enable Params to new text.
				$_desc = 'Orders Req. Acceptable Use Policy (AUP) Enable';
				$_notes = 'Determines if the agree to the Acceptable Use Policy button and requirement is visible during order placement.';
				$query = ""; $result= ""; $eff_rows = 0;
				$query	= "UPDATE ".$_DBCFG['parameters']." SET parm_desc = '$_desc', parm_notes = '$_notes' WHERE ( ";
				$query	.= "(parm_name = 'ORDERS_AUP_ENABLE')";
				$query	.= " )";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Old parameter text updated.'.$_nl;

			# Set Invc Due Days Offset to renamed
				$query = ""; $result= ""; $eff_rows = 0;
				$query	= "UPDATE ".$_DBCFG['parameters']." SET parm_name = 'INVC_DUE_DAYS_OFFSET' WHERE ( ";
				$query	.= "(parm_name = 'INVOICE_DUE_DAYS_OFFSET')";
				$query	.= " )";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Renamed Invc Due Days Offset.'.$_nl;

			$_cstr .= '<br><b>Parameters Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Parameters

		#######################################################
		# Table modifications: Products
		IF ( $_TBLEXIST[$_DBCFG['products']] && $_TBLUPDATE[$_DBCFG['products']] == 1 )
		{
			$_cstr .= '<br><b>Products Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['products']."
					ADD `prod_status` TINYINT(1) DEFAULT '1' NOT NULL AFTER `prod_id`
					, ADD `prod_dom_type` TINYINT(2) DEFAULT '0' NOT NULL
					, ADD `prod_allow_domains` TINYINT(4) NOT NULL
					, ADD `prod_allow_subdomains` TINYINT(4) NOT NULL
					, ADD `prod_allow_disk_space_mb` SMALLINT(6) NOT NULL
					, ADD `prod_allow_traffic_mb` SMALLINT(6) NOT NULL
					, ADD `prod_allow_mailboxes` TINYINT(4) NOT NULL
					, ADD `prod_allow_databases` TINYINT(4) NOT NULL
					, ADD `prod_cg_01` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_02` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_03` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_04` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_05` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_06` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_07` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_cg_08` TINYINT(1) DEFAULT '0' NOT NULL
					, ADD `prod_apply_tax_01` TINYINT(1) DEFAULT '1' NOT NULL AFTER `prod_client_scope`
					, ADD `prod_apply_tax_02` TINYINT(1) DEFAULT '1' NOT NULL AFTER `prod_apply_tax_01`
					, ADD `prod_calc_tax_02_pb` TINYINT(1) DEFAULT '0' NOT NULL AFTER `prod_apply_tax_02`
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table fields added.'.$_nl;

			# Set old -1 (all) to new 0 (all)
				$query = ""; $result= ""; $numrows = 0;
				$query	= "UPDATE ".$_DBCFG['products']." SET prod_client_scope = '0' WHERE prod_client_scope = '-1'";
				$result = $db_coin->db_query_execute($query);
				$eff_rows = $db_coin->db_affected_rows ();
				$_cstr .= '<br>Table field values modified.'.$_nl;

			$_cstr .= '<br><b>Products Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Products

		#######################################################
		# Table Creation: Sessions
		IF ( $_TBLUPDATE[$_DBCFG['sessions']] == 1 )
		{
			$_cstr .= '<br><b>Sessions Table(s) creation started</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['sessions'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Sessions
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['sessions'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`s_id` varchar(36) NOT NULL default '',
							`s_time_init` int(11) NOT NULL default '0',
							`s_time_last` int(11) NOT NULL default '0',
							`s_ip` varchar(16) NOT NULL default '000.000.000.000',
							`s_is_admin` tinyint(1) NOT NULL default '0',
							`s_is_user` tinyint(1) NOT NULL default '0',
							`s_time_last_contact` int(11) NOT NULL default '0',
							`s_time_last_order` int(11) NOT NULL default '0',
							PRIMARY KEY  (`s_id`)
						) TYPE=MyISAM COMMENT='Site Sessions Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			$_cstr .= '<br><b>Sessions Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Sessions

		#######################################################
		# Table modifications: Server Info
		IF ( $_TBLEXIST[$_DBCFG['server_info']] && $_TBLUPDATE[$_DBCFG['server_info']] == 1 )
		{
			$_cstr .= '<br><b>Server Info Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['server_info']."
					ADD `si_ns_01_ip` VARCHAR(15) DEFAULT '000.000.000.000' NOT NULL AFTER `si_ns_01`
					, ADD `si_ns_02_ip` VARCHAR(15) DEFAULT '000.000.000.000' NOT NULL AFTER `si_ns_02`
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			$_cstr .= '<br><b>Server Info Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Server Info

		#######################################################
		# Table modifications: SiteInfo
		IF ( $_TBLEXIST[$_DBCFG['site_info']] && $_TBLUPDATE[$_DBCFG['site_info']] == 1 )
		{
			$_cstr .= '<br><b>SiteInfo Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['site_info']."
					ADD `si_footer_menu` TINYINT(1) DEFAULT '1' NOT NULL
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			$_cstr .= '<br><b>SiteInfo Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Vendors

		#######################################################
		# Table modifications: Vendors
		IF ( $_TBLEXIST[$_DBCFG['vendors']] && $_TBLUPDATE[$_DBCFG['vendors']] == 1 )
		{
			$_cstr .= '<br><b>Vendors Table(s) Upgrade started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['vendors']."
					ADD `vendor_status` TINYINT(1) DEFAULT '1' NOT NULL AFTER `vendor_id`
					, ADD `vendor_buy_parm` VARCHAR(30) DEFAULT 'buy' NOT NULL AFTER `vendor_name`
					, ADD `vendor_buy_parm_val` VARCHAR(10) DEFAULT '1' NOT NULL AFTER `vendor_buy_parm`
					";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table field added.'.$_nl;

			$_cstr .= '<br><b>Vendors Table(s) Upgrade completed</b><br>'.$_nl;

		} # Table modifications completed: Vendors

		#######################################################
		# Table Creation: Versions
		IF ( $_TBLUPDATE[$_DBCFG['versions']] == 1 && $_GPV[up_all] == 1 )
		{
			$_cstr .= '<br><b>Versions Table(s) creation started.</b><br>'.$_nl;

			# Query for renaming existing table
				$_TBL_NAME = $_DBCFG['versions'];
				$result = ''; $eff_rows = '';
				$query 	= "DROP TABLE IF EXISTS ".$_TBL_NAME."";
				$result	= $db_coin->db_query_execute($query);
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Dropped the existing '.$_TBL_NAME.' table<br>'.$_nl; }

			/**************************************************************
				 * Table Create and Data Arrays For: Versions
			**************************************************************/
				# Set initial vars
					$_TBL_NAME = $_DBCFG['versions'];

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE IF NOT EXISTS ".$_TBL_NAME." (
							`v_id` smallint(6) NOT NULL auto_increment,
							`v_ts` varchar(10) NOT NULL default '',
							`v_ver` varchar(8) NOT NULL default '',
							`v_type` varchar(10) NOT NULL default 'install',
							PRIMARY KEY  (`v_id`)
						) TYPE=MyISAM COMMENT='Package Versions Table'
						";

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('', '$_time_stamp', 'V1.2.0', 'Upgrade')
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Created the new table<br>'.$_nl; }

				# Query for inserting data into new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.$eff_rows.' data rows into the new table<br>'.$_nl; }

				$_cstr .= '<br>Table Created'.$_nl;

			$_cstr .= '<br><b>Versions Table(s) Creation completed</b><br>'.$_nl;

		} # Table Creation completed: Versions


		#######################################################
		# Modify misc. field structure.
		#######################################################

		#######################################################
		# Table modifications: Misc data
		IF ( $_GPV[up_adv_set_fields] == 1 )
		{
			$_cstr .= '<br><b>Adjusting Misc Field size and properties started</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['domains']."
					CHANGE `dom_si_id` `dom_si_id` INT(11) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_domains` `dom_allow_domains` SMALLINT(6) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_subdomains` `dom_allow_subdomains` SMALLINT(6) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_disk_space_mb` `dom_allow_disk_space_mb` INT(11) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_traffic_mb` `dom_allow_traffic_mb` INT(11) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_mailboxes` `dom_allow_mailboxes` SMALLINT(6) DEFAULT '0' NOT NULL,
					CHANGE `dom_allow_databases` `dom_allow_databases` SMALLINT(6) DEFAULT '0' NOT NULL
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['mail_archive']."
					CHANGE `ma_id` `ma_id` BIGINT(11) NOT NULL auto_increment
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['mail_queue']."
					CHANGE `mq_id` `mq_id` BIGINT(11) NOT NULL auto_increment
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['menu_blocks']."
					CHANGE `block_id` `block_id` SMALLINT(6) NOT NULL auto_increment,
					CHANGE `block_pos` `block_pos` SMALLINT(6) NOT NULL default '0'
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['menu_blocks_items']."
					CHANGE `block_id` `block_id` SMALLINT(6) NOT NULL default '0',
					CHANGE `item_id` `item_id` SMALLINT(6) NOT NULL default '0'
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['orders']."
					CHANGE `ord_vendor_id` `ord_vendor_id` SMALLINT(6) NOT NULL default '1',
					CHANGE `ord_prod_id` `ord_prod_id` SMALLINT(6) NOT NULL default '0'
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['products']."
					CHANGE `prod_id` `prod_id` SMALLINT(6) NOT NULL auto_increment,
					CHANGE `prod_allow_domains` `prod_allow_domains` SMALLINT(6) NOT NULL default '0',
					CHANGE `prod_allow_subdomains` `prod_allow_subdomains` SMALLINT(6) NOT NULL default '0',
					CHANGE `prod_allow_disk_space_mb` `prod_allow_disk_space_mb` INT(11) NOT NULL default '0',
					CHANGE `prod_allow_traffic_mb` `prod_allow_traffic_mb` INT(11) NOT NULL default '0',
					CHANGE `prod_allow_mailboxes` `prod_allow_mailboxes` SMALLINT(6) NOT NULL default '0',
					CHANGE `prod_allow_databases` `prod_allow_databases` SMALLINT(6) NOT NULL default '0'
					";
			$result = $db_coin->db_query_execute($query);


			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['server_info']."
					CHANGE `si_id` `si_id` SMALLINT(6) NOT NULL auto_increment
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['vendors']."
					CHANGE `vendor_id` `vendor_id` SMALLINT(6) NOT NULL auto_increment
					";
			$result = $db_coin->db_query_execute($query);

			$query = ""; $result= ""; $numrows = 0;
			$query = "ALTER TABLE ".$_DBCFG['vendors_prods']."
					CHANGE `vprod_id` `vprod_id` SMALLINT(6) NOT NULL auto_increment,
					CHANGE `vprod_vendor_id` `vprod_vendor_id` SMALLINT(6) NOT NULL default '0',
					CHANGE `vprod_prod_id` `vprod_prod_id` SMALLINT(6) NOT NULL default '0'
					";
			$result = $db_coin->db_query_execute($query);

			$_cstr .= '<br><b>Misc Field size and properties adjusting completed</b><br>'.$_nl;

		} # Table modifications completed: Misc.

		#######################################################
		# Advanced Option One: Drop old Server Accounts
		IF ( $_TBLEXIST[$_DBCFG['server_accounts']] && $_GPV[up_adv_01] == 1 )
		{
			$_cstr .= '<br><b>Old Server Accounts Table(s) Dropping old table(s)</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "IF EXISTS DROP TABLE ".$_DBCFG['server_accounts'];
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br>Table dropped.'.$_nl;

			$_cstr .= '<br><b>Old Server Accounts Table(s) Dropped</b><br>'.$_nl;

		} # Table modifications completed: Server Accounts

		#######################################################
		# Advanced Option Two: Flush Sessions tables
		IF ( $_GPV[up_adv_02] == 1 )
		{
			$_cstr .= '<br><b>Flushing Sessions Table(s)</b><br>'.$_nl;
			IF ( $_TBLEXIST[$_DBCFG['orders_sessions']] )
				{
					$query = ""; $result= ""; $numrows = 0;
					$query = "DELETE FROM ".$_DBCFG['orders_sessions'];
					$result = $db_coin->db_query_execute($query);
					$_cstr .= '<br>Orders Sessions flushed'.$_nl;
				}
			IF ( $_TBLEXIST[$_DBCFG['sessions']] )
				{
					$query = ""; $result= ""; $numrows = 0;
					$query = "DELETE FROM ".$_DBCFG['sessions'];
					$result = $db_coin->db_query_execute($query);
					$_cstr .= '<br>Orders Sessions flushed'.$_nl;
				}

			$_cstr .= '<br><b>Sessions Table(s) Flushed</b><br>'.$_nl;

		} # Table modifications completed: Flush Sessions

		#######################################################
		# Advanced Option Three: Copy Client Domains to _copy
		IF ( $_GPV[up_adv_03] == 1 )
		{
			$_cstr .= '<br><b>Copying clients_domains table to _copy</b><br>'.$_nl;

			IF ( $_TBLEXIST[$_DBCFG['clients_domains_copy']] )
			{
				$query = ""; $result= ""; $numrows = 0;
				$query = "DROP TABLE ".$_DBCFG['clients_domains']."_copy";
				$result = $db_coin->db_query_execute($query);
				$_cstr .= '<br>Dropped existing _copy table.<br>'.$_nl;
			}

			# Set initial vars
				$_TBL_NAME = $_DBCFG['clients_domains']."_copy";

				# Table Create SQL for create table query
					$_TBL_SQL_CRT[$_TBL_NAME] = "
						CREATE TABLE ".$_TBL_NAME." (
							`cd_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
							`cd_cl_id` int( 11 ) NOT NULL default '0',
							`cd_cl_domain` varchar( 50 ) NOT NULL default '',
							`cd_registrar` varchar( 32 ) NOT NULL default '',
							`cd_ts_expiration` varchar( 10 ) NOT NULL default '',
							`cd_sa_id` int( 11 ) NOT NULL default '0',
							`cd_sa_expiration` varchar( 10 ) NOT NULL default '',
							PRIMARY KEY ( `cd_id` )
						) TYPE = MYISAM COMMENT = 'Client Domains Module Table'
						";

				# Query for creating new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_CRT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_CRT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Copied Table<br>'.$_nl; }

				# Query for creating new table
					$result = ''; $eff_rows = '';
					$query 		= "INSERT INTO ".$_TBL_NAME." SELECT * FROM ".$_DBCFG['clients_domains'];
					$result		= $db_coin->db_query_execute($query);
					$eff_rows	= $db_coin->db_affected_rows ();

				IF ( $result ) { $_cstr .= '&nbsp;&nbsp;- Inserted copied data<br>'.$_nl; }

			$_cstr .= '<br><b>Copied Table</b><br>'.$_nl;

		} # Table modifications completed: Copy Client Domains to _copy

		#######################################################
		# Advanced Option Four: Drop All
		IF ( $_GPV[up_adv_04] == 1  && $_GPV[up_adv_04] == 0)
		{
			$_cstr .= '<br><b>Dropped all tables</b><br>'.$_nl;

			$query = ""; $result= ""; $numrows = 0;
			$query = "DROP TABLE `$_DBCFG[admins]`, `$_DBCFG[articles]`, `$_DBCFG[banned]`, `$_DBCFG[categories]`, `$_DBCFG[clients]`, `$_DBCFG[clients_domains]`
				, `$_DBCFG[components]`, `$_DBCFG[domains]`, `$_DBCFG[faq]`, `$_DBCFG[faq_qa]`, `$_DBCFG[helpdesk]`
				, `$_DBCFG[helpdesk_msgs]`, `$_DBCFG[icons]`, `$_DBCFG[invoices]`, `$_DBCFG[invoices_items]`, `$_DBCFG[invoices_trans]`
				, `$_DBCFG[mail_archive]`, `$_DBCFG[mail_contacts]`, `$_DBCFG[mail_queue]`, `$_DBCFG[mail_templates]`, `$_DBCFG[menu_blocks]`
				, `$_DBCFG[menu_blocks_items]`, `$_DBCFG[orders]`, `$_DBCFG[orders_sessions]`, `$_DBCFG[pages]`, `$_DBCFG[parameters]`
				, `$_DBCFG[products]`, `$_DBCFG[server_accounts]`, `$_DBCFG[server_info]`, `$_DBCFG[sessions]`, `$_DBCFG[site_info]`
				, `$_DBCFG[topics]`, `$_DBCFG[vendors]`, `$_DBCFG[vendors_prods]`, `$_DBCFG[versions]`
				";
			$result = $db_coin->db_query_execute($query);
			$_cstr .= '<br><b>Dropped all tables</b><br>'.$_nl;

		} # Table modifications completed: Drop All



		#######################################################
		# Insert some modified / new misc data.
		#######################################################

		#######################################################
		#######################################################
		# Table modifications: Insert mail templates
		IF ( $_TBLEXIST[$_DBCFG['mail_templates']] && $_TBLUPDATE[$_DBCFG['mail_templates']] == 1 )
		{
			$_cstr .= '<br><b>Insert mail templates started</b><br>'.$_nl;

				# Set initial vars
					$_TBL_NAME = $_DBCFG['mail_templates'];

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('', 'email_order_ack', 'Hello \$_MTP[to_name],\r\nThe following order acknowledgement is from \$_MTP[site] \$_MTP[from_name] department.\r\n\r\n-------------------\r\n\$_MTP[order]\r\n-------------------\r\n\r\nThis order can be monitored online at the following link (requires login):\r\n \$_MTP[ord_url]\r\n\r\nThank you for continuing to choose \$_MTP[site] for your web service needs.\r\n\r\n \$_MTP[from_name]\r\n \$_MTP[from_email]\r\n'),
							('', 'email_trans_ack', 'Hello \$_MTP[to_name],\r\nThe following email transaction acknowledgement is from \$_MTP[site] \$_MTP[from_name] department.\r\n\r\n Invoice ID:    \$_MTP[invc_id]\r\n Invoice Date:  \$_MTP[invc_ts]\r\n Date Due:      \$_MTP[invc_ts_due]\r\n Date Paid:     \$_MTP[invc_ts_paid]\r\n Status:        \$_MTP[invc_status]\r\n                ----------------------\r\n Trans ID:      \$_MTP[it_id]\r\n Date:          \$_MTP[it_ts]\r\n Type:          \$_MTP[it_type]\r\n Origin:        \$_MTP[it_origin]\r\n Description:   \$_MTP[it_desc]\r\n Amount:        \$_MTP[it_amount]\r\n                ----------------------\r\n\r\nThe following is overall account balance with \$_MTP[site] and includes all account activity:\r\n\r\n Acc Balance:  \$_MTP[cl_balance]\r\n\r\nThis transaction can be viewed when visiting following link (requires login):\r\n \$_MTP[invc_url]\r\n\r\nThank you for continuing to choose \$_MTP[site] for your web service needs.\r\n\r\n \$_MTP[from_name]\r\n \$_MTP[from_email]\r\n'),
							('', 'email_domain_acc_activate', 'Hello \$_MTP[to_name],\r\n\r\nWelcome!  Thank you for choosing \$_MTP[site] for your hosting needs.\r\n\r\nThis email contains vital information about your server account. Please read it thoroughly and print these pages or copy to a safe place.\r\n\r\n-------------------------------------------------------------\r\n1.	General Client Profile Information\r\n-------------------------------------------------------------\r\n\r\n\$_MTP[cl_info]\r\n\r\n-------------------------------------------------------------\r\n2.	General Account Information\r\n-------------------------------------------------------------\r\n\r\nTo log into your account, please access:\r\n\r\n\$_MTP[si_cp_url]\r\n or:\r\nhttps://\$_MTP[si_ip]:\$_MTP[si_cp_url_port]/login.php3\r\n\r\nServer Name:	\$_MTP[si_name]\r\nIP Address:		\$_MTP[si_ip]  (shared)\r\nDomain Name:	\$_MTP[dom_domain]\r\nPOP Server:		mail.\$_MTP[dom_domain]\r\nSMTP Server:	mail.\$_MTP[dom_domain]\r\n\r\nNameservers:	\$_MTP[si_ns_01]  (IP: \$_MTP[si_ns_01_ip])\r\n				\$_MTP[si_ns_02]  (IP: \$_MTP[si_ns_02_ip])\r\n\r\nPlesk Username:	\$_MTP[dom_user_name_cp]\r\nPlesk Password:	\$_MTP[dom_user_pword_cp]\r\n\r\nFTP Username:	\$_MTP[dom_user_name_ftp]\r\nFTP Password:	\$_MTP[dom_user_pword_ftp]\r\nFTP URL:		ftp.\$_MTP[dom_domain]  (once resolved)\r\n\r\nPlease remember that it will take a short time for your Registrar to process the delegation request and for this new DNS to propagate around the internet. During that time, you will not be able to access this site as normal through a web browser. However, you can still access your site during this period by pointing your browser to:\r\n\r\n		http://\$_MTP[si_ip]/~\$_MTP[dom_user_name_ftp]\r\n\r\nPlease note that CGI and PHP will not function properly while view your site through this special IP address. It is just a temporary measure to assist you in preparing your site while your new DNS transfer around the net.\r\n\r\nYou can upload your site by pointing your FTP software to your shared IP address (\$_MTP[si_ip]). This means that you do not have to wait for your domain name to be transferred to our servers before you can upload your site. Once your domain has fully propagated around the net, you will be able to access your FTP account via your own domain name.\r\n\r\n\r\n-------------------------------------------------------------\r\n3.	Once your domain resolves:\r\n-------------------------------------------------------------\r\n\r\n	Your webmail URL is:	http://webmail.\$_MTP[dom_domain]\r\n\r\n	Your Webstats URL is:	http://\$_MTP[dom_domain]/webstat/\r\n	(stats can also be viewed from control panel)\r\n\r\n\r\n-------------------------------------------------------------\r\n4.	Setting Up An Email Account\r\n-------------------------------------------------------------\r\n\r\n	Before you can start sending or receiving email, you must\r\n	set up at least one email account.  To do this, please\r\n	follow the instructions below:\r\n\r\n	1. Log in to the control panel\r\n		(\$_MTP[si_cp_url])\r\n		using your control panel username and password.\r\n\r\n	2. Click on the MAIL button.\r\n\r\n	3. Enter the mail name that you want to create (i.e. \"bob\",\r\n		\"editor\", \"webmaster\" etc., not the full address).\r\n\r\n	4. Click on the ADD button\r\n\r\n	5. Check/Tick the \"Mailbox\" box\r\n\r\n	6. Enter and confirm a password that you would like to use\r\n\r\n	7. Scroll down to the bottom and click UPDATE\r\n\r\n\r\n-------------------------------------------------------------\r\n5.	Billing Information\r\n-------------------------------------------------------------\r\n\r\nFor increased security reasons we do not process credit cards ourselves on our servers. Depending on your selection of payment method when ordering, one of two third party processors will be used:\r\n\r\n	Paypal:			- Charge will appear as \"Paypal\"\r\n	Credit Card:	- Charge will appear as \"2CheckOut.com Inc.\"\r\n\r\nRegardless of the method you selected, you will receive an order confirmation email detailing your order information.\r\n\r\nIf your email addresses changes, please notify us of your new email address and also update your profile in the control panel via the EDIT button.\r\n\r\n\r\n-------------------------------------------------------------\r\n6.	Support Methods\r\n-------------------------------------------------------------\r\n\r\nWhen looking for information or support. Please go through the motions:\r\n(see \$_MTP[site] on the Client Tools menu for all these links)\r\n\r\na. FAQ Knowledgebase:\r\nFor general and other information, browse and search our FAQ:\r\n\r\n	http://www.yoursite.com/phpcoin/mod.php?mod=faq\r\n\r\n\r\nb. The \$_MTP[site] Community:\r\nWe can’t stress enough, the importance of joining and participating in our community forums.  Often, you can find the answers to your questions just by searching for keywords or posting a question.  There are other members who can help you out with certain things that are not covered by our normal support options (Programming etc). You can also interact with \$_MTP[site] staff, give us feedback and generally discuss our service at a community level.\r\n\r\n	http://forums.yoursite.com\r\n\r\nImportant note: The community is installed on a different server than the primary website. The reason for this is in the event you cannot reach the main site due to server issues, you should be able to reach the community for information and updates.\r\n\r\n\r\nc. HelpDesk:\r\nIf you require assistance please contact our Technical Support Team by submitting a support form. We will be happy to assist you. All support requests MUST be done using our ticket support system, which can be accessed at:\r\n\r\n	http://www.yoursite.com/phpcoin/mod.php?mod=helpdesk\r\n\r\n\r\nd. Quick Chat:\r\nFor immediate assistance, check the Quick Chat feature setup on the main site. Simply click on the \"Quick Chat\" link under the client Tools menu. Login and and quite possibly someone will be there who can assist you.\r\n\r\n\r\ne. Email Support:\r\nThis form of support is mostly used for sales, orders, or basic questions. Please view the Contacts Info link on the main site for up-to-date links for email options.\r\n\r\n\r\nf. Phone Support:\r\nCurrently, \$_MTP[site] does not offer phone support.\r\n\r\n\r\nh. Regular Mail:\r\n\r\n	\$_UVAR[CO_INFO_01_NAME]\r\n	c/o Customer Service\r\n	\$_UVAR[CO_INFO_02_ADDR_01]\r\n	\$_UVAR[CO_INFO_03_ADDR_02]\r\n	\$_UVAR[CO_INFO_04_CITY], \$_UVAR[CO_INFO_05_STATE_PROV]	\$_UVAR[CO_INFO_06_POSTAL_CODE]\r\n	\$_UVAR[CO_INFO_07_COUNTRY]\r\n\r\n	Phone:	\$_UVAR[CO_INFO_08_PHONE]\r\n	Fax:	\$_UVAR[CO_INFO_09_FAX]\r\n\r\nOnce again, this email contains vital information about your account. \r\nPlease read it thoroughly and print these pages or copy to a safe place.\r\n\r\nThank you again for choosing \$_MTP[site] as your service provider.\r\n\r\n \$_MTP[from_name]\r\n \$_MTP[from_email]\r\n')
							";

				# Query for inserting data into new table
					$result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.$eff_rows.' data rows into the new table<br>'.$_nl; }

			$_cstr .= '<br><b>Insert mail templates completed</b><br>'.$_nl;

		} # Table modifications completed: Insert mail templates

		#######################################################
		#######################################################
		# Table modifications: Insert / Modify components
		IF ( $_TBLEXIST[$_DBCFG['domains']] && $_TBLUPDATE[$_DBCFG['domains']] == 1 )
		{
			$_cstr .= '<br><b>Insert new component entries</b><br>'.$_nl;

				# Set initial vars
					$_TBL_NAME = $_DBCFG['components'];

				# Table Data SQL for insert data query
					$_TBL_SQL_DAT[$_TBL_NAME] = "
						INSERT INTO ".$_TBL_NAME." VALUES
							('', 'admin', 'cp_banip', '', 'Control Panel: Banned IP', 'Admin Banned IP', '2', '1', '0', '1'),
							('', 'module', 'domains', '', 'Domains', 'Domains', '2', '0', '0', '1')
							";

				# Query for inserting data into new table
					$query = ''; $result = ''; $eff_rows = '';
					IF ( $_TBL_SQL_DAT[$_TBL_NAME] )
					{
						$query 		= $_TBL_SQL_DAT[$_TBL_NAME];
						$result		= $db_coin->db_query_execute($query);
						$eff_rows	= $db_coin->db_affected_rows ();
					}
				IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Inserted '.$eff_rows.' data rows into the new table<br>'.$_nl; }

				# Query for deleting data
					$query = ''; $result = ''; $eff_rows = '';
					$query 		= "DELETE FROM ".$_DBCFG['components']." WHERE comp_name = 'cp_clients_domains'";
					$result		= $db_coin->db_query_execute($query);
					$eff_rows	= $db_coin->db_affected_rows ();
					IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Deleted old cp_clients_domains component entry<br>'.$_nl; }

				# Query for deleting data
					$query = ''; $result = ''; $eff_rows = '';
					$query 		= "DELETE FROM ".$_DBCFG['components']." WHERE comp_name = 'saccs'";
					$result		= $db_coin->db_query_execute($query);
					$eff_rows	= $db_coin->db_affected_rows ();
					IF ( $result) { $_cstr .= '&nbsp;&nbsp;- Deleted old cp_clients_domains component entry<br>'.$_nl; }

			$_cstr .= '<br><b>Insert / modify components completed</b><br>'.$_nl;

		} # Table modifications completed: Insert / Modify components


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