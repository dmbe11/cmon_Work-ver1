<?php
/**
 * Installation: Main
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Output
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
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


# Set a session var to prevent re-processing on refresh
	IF (!isset($_SESSION['setup_ran'])) {$_SESSION['setup_ran'] = 0;}

# Turn off pointless "warning" messages, and display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);

# Prevent timeout if MySQL server is on a different machine and network latency is causing installation issues
	ini_set('max_execution_time', 10000);
	set_time_limit(10000);


# Exit with error if php < 4.1
	$_pv = phpversion();
	IF (!version_compare($_pv, '4.1', ">=")) {
		Header("Location: error.php?err=80&required=4.1");
		exit();
	}

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


# Initialize variables
	$Resuming		= 0;
	$FullInstall	= 0;
	$FatalError	= 0;
	$block_title	= 'Fatal Error: <i>Required</i> File Not Found';

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
		error_block($block_title, 'The required file <b>coin_includes/session_common.php</b> could not be located where it was expected at '.$_PACKAGE['DIR'].'coin_includes/session_common.php');
	}

# Check for setup_config.php file
	IF (is_readable(PKG_PATH_BASE.'coin_setup/setup_config.php')) {
		require_once(PKG_PATH_BASE.'coin_setup/setup_config.php');
	} ELSE {
		error_block($block_title, 'The required file <b>setup_config.php</b> could not be located where it was expected at '.PKG_PATH_BASE.'coin_setup/setup_config.php');
	}


# Check for theme config.php file
	IF (is_readable($_CCFG['_PKG_PATH_THEME'].'config.php')) {
		require_once($_CCFG['_PKG_PATH_THEME'].'config.php');
	} ELSE {
		error_block($block_title, 'The required theme file <b>config.php</b> could not be located where it was expected at '.$_CCFG['_PKG_PATH_THEME'].'config.php');
	}

# Unset some form fields if browser-refresh was pressed
	IF ($_SESSION['setup_ran'] > 0) {unset($_GPV['password']);}

# Check for db_xxxxx.php file
	require_once(PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php');
	IF (is_readable(PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php')) {
		require_once(PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php');
	} ELSE {
		error_block($block_title, 'The required file <b>db_'.$_DBCFG['dbms'].'.php</b> could not be located where it was expected at '.PKG_PATH_DBSE.'db_'.$_DBCFG['dbms'].'.php');
	}


# Ensure we have a numeric version number
	IF (!isset($ThisVersion)) {$ThisVersion = 0;}
	$TV			= str_replace('b', '', $ThisVersion);
	$TV			= str_replace('v', '', $TV);
	$ThisVersionIs	= $TV;
	$TV			= str_replace('.', '', $TV);
	$FileVersion	= abs($TV);

# Create db Instance
	$db_coin		= new db_funcs();

# Open Page
	echo do_install_page_open();

# Is surfer an "Always Online" IP?
	IF (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
		$pos = strpos(strtolower($_SERVER['HTTP_X_FORWARDED_FOR']), '192.168.');
		IF ($pos === FALSE) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} ELSE {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
	} ELSE {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	$_online_ips	= explode(',', $_CCFG['_PKG_MODE_ONLINE_IP']);
	$_offoverride	= 0;
	IF (in_array($ip, $_online_ips)) {$_offoverride = 1;}


# Check Offline flag
	IF ($_CCFG['_PKG_MODE_OFFLINE'] == 1) {

	# Output warning text if admin allowed to continue
		IF ($_offoverride) {
			echo '<div style="background-color: #FFFFFF; color: #FF0000; text-align: center; padding: 15px;">WARNING: This site is marked as offline in config.php</div>';

	# Otherwise redirect to "site offline " page
		} ELSE {
			html_header_location('index_offline.html');
			exit();
		}
	}

# Attempt database connect:
	$db_coin->db_connect();
	IF ($db_coin->connection)  {
		$_db_check = 1;
		$_cstr  = '<b>Database Connection completed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Hostname, Username, and Password are OK.<br>'.$_nl;
	} ELSE {
		$_db_check = 0;
		$_cstr  = '<b>Database Connection failed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Check Hostname, Username, and Password in config.php file and try again.<br>'.$_nl;
	}

# Attempt database select:
	$db_coin->db_select_db();
	IF ($db_coin->connection)  {
		$_db_check = 1;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<b>Database Selection Completed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Database Name is OK.<br>'.$_nl;
	} ELSE {
		$_db_check = 0;
		$_cstr .= '<br>'.$_nl;
		$_cstr .= '<b>Database Selection failed:</b><br>'.$_nl;
		$_cstr .= '&nbsp;&nbsp;- Check Database Name in config.php file and try again<br>'.$_nl;
	}

	$db_coin->db_set_suppress_errors(1);

# Upon succesful database connect.....
	IF ($db_coin->connection) {

	# Determine database version, and if MySQL v5 reset SAFE MODE settings for this session
		$query	= "SHOW VARIABLES LIKE 'version'";
		$result	= $db_coin->db_query_execute($query);
		IF ($result) {
			while ($row = $db_coin->db_fetch_array($result)) {
				IF (strpos($row['Value'], '5.') !== FALSE) {
					IF ($db_coin->db_query_execute('SELECT @@session.sql_mode')) {
						$result = $db_coin->db_query_execute("SET @@session.sql_mode=''");
					}
				}
			}
		}

	# What tables do we have installed? This will help determine our upgrade/install path.
		$Table_Installed['clients']			= 0;
		$Table_Installed['versions']			= 0;
		$Table_Installed['clients_status']		= 0;
		$Table_Installed['server_accounts']	= 0;
		$Table_Installed['install_status']		= 0;
		$_DBCFG['clients_status']			= $_DBCFG['table_prefix'].'clients_status';
		$_DBCFG['server_accounts']			= $_DBCFG['table_prefix'].'server_acounts';

		$result = $db_coin->db_query_execute('SHOW TABLES');
		$num_results = $db_coin->db_query_numrows($result);
		for ($i = 0; $i < $num_results; $i++) {
			$row = $db_coin->db_fetch_array($result);
 			IF ($row[0] == $_DBCFG['clients'])			{$Table_Installed['clients']++;}
			IF ($row[0] == $_DBCFG['versions'])		{$Table_Installed['versions']++;}
			IF ($row[0] == $_DBCFG['clients_status'])	{$Table_Installed['clients_status']++;}
			IF ($row[0] == $_DBCFG['server_accounts'])	{$Table_Installed['server_acounts']++;}
			IF ($row[0] == 'install_status')			{$Table_Installed['install_status']++;}
		}

	# Are we resuming an aborted install?
		IF ($Table_Installed['install_status'])			{$Resuming = 1;}

	# OK, so what version is installed?
		$DBVersion		= 0;
		$InstalledVersion	= '';
		IF ($Table_Installed['clients_status']) {
			$InstalledVersion	= '1.1.0';
			$DBVersion		= 110;
		} ELSEIF ($Table_Installed['server_accounts']) {
			$InstalledVersion	= '1.1.1';
			$DBVersion		= 111;
		} ELSEIF ($Table_Installed['versions']) {
			$query	= 'SELECT v_ver FROM '.$_DBCFG['versions'];
			$result	= $db_coin->db_query_execute($query);
			IF ($result) {
				$numrows = $db_coin->db_query_numrows($result);
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
						$row['v_ver']		= strtolower($row['v_ver']);
						IF ($row['v_ver'] == 'v1.3.2') {$row['v_ver'] = 'v1.3.1';}
						$InstalledVersion	= str_replace('v', '', $row['v_ver']);
						$DBVersion		= str_replace('.', '', $InstalledVersion);
					}
				}
			}
		}
		$DBVersion = abs($DBVersion);

	# Build installation action to display
		$_cstr .= '<br><b>Installation Action:</b><br>&nbsp;&nbsp;- '.$_nl;
		IF ($Resuming) {$_cstr .= 'Resuming ';}

		IF ($DBVersion == $FileVersion) {
			$FullInstall++;
			$Warn = 1;
			$_cstr .= '<font color="red">This version of phpCOIN is allready installed. Re-install (and possible backup) will be performed.</font>'.$_nl;

		} ELSEIF ($Table_Installed['clients_status']) {
			$_cstr .= 'Upgrade v'.$InstalledVersion.' To v'.$ThisVersionIs.'<br>'.$_nl;
			$_cstr .= '<br>Sorry, but v1.1.0 or lower cannot be <i>directly</i> upgraded to v'.$ThisVersionIs.$_nl;
			$_cstr .= '<br>To complete the upgrade process, please exit this installation program, then:<ol>'.$_nl;
			$_cstr .= '<li>Run upgrade_to_v11x.php then</li>'.$_nl;
			$_cstr .= '<li>Run upgrade_to_v120.php then</li>'.$_nl;
			$_cstr .= '<li>Rerun setup.php</li></ol><br>'.$_nl;
			$FatalError++;

		} ELSEIF ($Table_Installed['server_accounts']) {
			$_cstr .= 'Upgrade v'.$InstalledVersion.' To v'.$ThisVersionIs.'<br>'.$_nl;
			$_cstr .= '<br>Sorry, but v1.1.1 cannot be <i>directly</i> upgraded to v'.$ThisVersionIs.$_nl;
			$_cstr .= '<br>To complete the upgrade process, please exit this installation program, then:<ol>'.$_nl;
			$_cstr .= '<li>run upgrade_to_v120.php then</li>'.$_nl;
			$_cstr .= '<li> rerun setup.php</li></ol><br>'.$_nl;
			$FatalError++;

		} ELSEIF ($DBVersion > $FileVersion) {
			$_cstr .= 'Downgrade v'.$InstalledVersion.' To v'.$ThisVersionIs.'<br>'.$_nl;
			$_cstr .= '<br>Sorry, but v'.$InstalledVersion.' cannot be downgraded to v'.$ThisVersionIs.$_nl;
			$_cstr .= '<br>'.$_nl;
			$FatalError++;

		} ELSEIF ($InstalledVersion) {
			$_cstr .= 'Upgrade v'.$InstalledVersion.' To v'.$ThisVersionIs.'<br>'.$_nl;

		} ELSE {
			$_cstr .= 'New Installation of v'.$ThisVersionIs.'<br>'.$_nl;
		}

	# Output  data
		$_out	= '<br />'.$_nl;
		$_out	.= do_install_block_it('Initial Database Check', $_cstr, '');
		$_cstr	= '';
		$_mstr	= '';
		echo $_out;


	# Display notice about compatibility mode IF upgrading to v1.4
		IF ($FileVersion > 131 && ($DBVersion > 0 && $DBVersion < 140)) {
			$_cstr = '<p style="color: red;">This version of phpCOIN introduces some code changes that <i>will</i> break third-party themes or add-ons (such as WHM) written for phpCOIN v1.3.1 and lower.</p><p>IF you use any third-party themes or add-ons, ensure that you either:<ol><li>Enable $_CCFG[ENABLE_COMPATIBILITY_MODE] in config.php <i>or</i></li><li>Edit the theme/add-on according to the instructions in /coin_docs/readme.txt</li></ol></p><p>Changing the theme or add-on is preferred, because the compatibility mode file creates variables and functions identical to the older versions of phpCOIN so you lose the speed enhancements of <i>this</i> version</p>';
			$_out  = '<br />'.$_nl;
			$_out .= do_install_block_it('<font color="red">NOTICE: COMPATIBILITY MODE</font>', $_cstr, '');
			$_cstr = '';
			echo $_out;
		}

	# Do cheesy login to check against db password (non-encrypt)
	# unless your password is easy to guess, this SHOULD prevent people from
	# re-installing the database and wiping out your data IF you do NOT
	# delete the setup directory and contents
		IF (!$FatalError && $_GPV['stage'] == 1 && $_db_check == 1 && $_GPV['password'] == $_DBCFG['dbpass'] && $_GPV['read_license']) {

		# Build result string
			$_proceed = 1;
			IF ($DBVersion == $FileVersion) {$_action = ' re-installation';} ELSEIF ($DBVersion) {$_action = ' upgrade';} ELSE {$_action = ' installation';}
			$_cstr .= '<b>Login Results:</b><br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;- Login: Passed, proceeding with '.$_action.' .............<br>'.$_nl;

		# Output  data
			$_out	= '<br>'.$_nl;
			$_out	.= do_install_block_it('Log In', $_cstr, '');
			$_cstr	= '';
			$_mstr	= '';
			echo $_out;

		} ELSE {

		# Create Login Form
			$_cstr .= '<table width="100%"><tr><td class="TP5MED_NL">'.$_nl;
			$_cstr .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="login">'.$_nl;
			$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;

		# Build result string
			IF (!$_GPV['read_license'] && $_GPV['stage'] == 1) {
				$_cstr .= '<b>License Acceptance:</b><br>'.$_nl;
				$_cstr .= '&nbsp;&nbsp;- License Not Accepted: Aborting .............<br>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
			IF ($_GPV['password'] != $_DBCFG['dbpass'] && $_GPV['stage'] == 1) {
				$_cstr .= '<b>Login Results:</b><br>'.$_nl;
				$_cstr .= '&nbsp;&nbsp;- Login Failed: Aborting .............<br>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}

		# Show read and agree to license
			IF ($DBVersion == $FileVersion) {$_button = 're-install';} ELSEIF ($DBVersion) {$_button = ' upgrade';} ELSE {$_button = ' install';}
			$_cstr .= '<p><b>License Agreement:</b><br>'.$_nl;
			$_cstr .= '<iframe src="../coin_docs/license.txt" width="98%" height="150" scrolling="auto" style="border: 1px solid; margin: 5px;">[Your user agent does not support frames or is currently configured not to display frames. However, you may view the <a href="../coin_docs/license.txt">license terms</a> by clicking the link]</iframe><br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;- Check this box to indicate that you have read and agree with the license terms.<br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;- phpCOIN will not '.$_button.' if not checked.<br>'.$_nl;
			$_cstr .= '<input type="checkbox" id="read_license" name="read_license">'.$_nl;
			$_cstr .= '&nbsp;I Agree With The License'.$_nl;
			$_cstr .= '</p>'.$_nl;

		# Set db backup options if upgrading
			IF ($InstalledVersion || $Warn == 1) {
				$_checked = '';
				$_cstr .= '<p><b>Backup Existing Data:</b><br>'.$_nl;
				IF ($Warn == 1) {
					$_cstr .= '&nbsp;&nbsp;- This version of phpCOIN is allready installed.<br>';
					$_cstr .= '<font color="red">&nbsp;&nbsp;- ALL EXISTING DATA WILL BE DELETED AND NEW DATA WILL BE INSTALLED</font><br>';
					$_checked = ' checked';
				}
				$_cstr .= '&nbsp;&nbsp;- Check this box to backup existing data first by copying the tables into identical _bak tables<br>';
				$_cstr .= '<input type="checkbox" name="tbl_bak" value="1"'.$_checked.'>'.$_nl;
				$_cstr .= '&nbsp;Perform Backup</p>';
			}

			$_cstr .= '<p><b>Database Password:</b><br>'.$_nl;
			$_cstr .= '&nbsp;&nbsp;- Enter your database password to ';
			IF ($Resuming) {$_cstr .= 'resume';} ELSE {$_cstr .= 'complete';}
			IF ($DBVersion == $FileVersion) {$_cstr .= ' re-install';} ELSEIF ($DBVersion) {$_cstr .= ' upgrade';} ELSE {$_cstr .= ' installation';}
			$_cstr .= '<br>&nbsp;&nbsp;- ';
			IF ($DBVersion == $FileVersion) {$_cstr .= 'Re-Install';} ELSEIF ($DBVersion) {$_cstr .= 'Upgrade';} ELSE {$_cstr .= 'Installation';}
			$_cstr .= ' will begin when you click ';
			IF ($DBVersion == $FileVersion) {$_cstr .= '[Re-Install]';} ELSEIF ($DBVersion) {$_cstr .= '[Upgrade]';} ELSE {$_cstr .= '[Install]';}
			$_cstr .= ' ~ there will be NO additional prompts<br>'.$_nl;
			$_cstr .= 'Password:&nbsp;<input class="PMED_NL" type="password" name="password" size="20" maxlength="20" value=""></p>'.$_nl;

			$_cstr .= '<input class="PMED_NC" type="submit" value="';
			IF ($DBVersion == $FileVersion) {$_cstr .= 'Re-Install';} ELSEIF ($DBVersion) {$_cstr .= 'Upgrade';} ELSE {$_cstr .= 'Install';}
			$_cstr .= '">'.$_nl;

			$_cstr .= '</form>'.$_nl;

			$_cstr .= '</td></tr>'.$_nl;

		# Output  data
			$_out	= '<br>'.$_nl;
			$_out	.= do_install_block_it('Log In', $_cstr, '');
			$_cstr	= '';
			$_mstr	= '';
			echo $_out;
		}

	# Check proceed flag and go
		IF (!isset($_proceed)) {$_proceed = '';}
		IF ($_proceed) {

			IF (!$Resuming) {

			# Get list of existing tables, and set "exist" flags
				$_cstr .= '<b>Checking for existing tables: Begin</b><br>'.$_nl;
				$result = $db_coin->db_query_execute('SHOW TABLES');
				while(list($_TBL) = $db_coin->db_fetch_row($result)) {
					$_cstr .= '&nbsp;&nbsp;"'.$_TBL.'"<br>'.$_nl;
					$_TBLEXIST[$_TBL] = 1;
				}
				IF ($result) {$result = $db_coin->db_free_result($result);}
				$_cstr .= '<b>Checking for existing tables: Completed</b><br><br>'.$_nl;

			# Backup and/or delete existing tables
				IF ($_GPV['tbl_bak'] == 1) {
					$_cstr .= '<b>Backup and/or delete existing tables: Begin</b><br>'.$_nl;
				}

			# How many tables are we working with for the upgrade/install?
				$ToDo = count($_TBL_NAMES);

			# Loop TBL_NAMES array and backup or delete each table
				FOR ($i=0; $i<$ToDo; $i++) {

				# Process Table (without prefix): $_TBL_NAMES[$i]
					$_TBL_NAME = $_TBL_NAMES[$i];

				# Check exist flag and process
					IF (!isset($_TBLEXIST)) {$_TBLEXIST = array();}
					IF ($_TBLEXIST[$_TBL_NAME] == 1) {

					# Check _bak option
						IF ($_GPV['tbl_bak'] == 1) {

						# Drop existing _bak table
							$query 	= 'DROP TABLE IF EXISTS '.$_TBL_NAME.'_bak';
							$result	= $db_coin->db_query_execute($query);
							$_cstr	.= '&nbsp;&nbsp;- Dropped existing '.$_TBL_NAME.'_bak table (if any)<br>'.$_nl;

						# Copy existing tables into new tables with _bak appended to name
							$query = 'CREATE TABLE '.$_TBL_NAME.'_bak LIKE '.$_TBL_NAME;
							$result	= $db_coin->db_query_execute($query);
							$query = 'INSERT '.$_TBL_NAME.'_bak SELECT * FROM '.$_TBL_NAME;
							$result	= $db_coin->db_query_execute($query);
							$_cstr	.= '&nbsp;&nbsp;- Copied existing '.$_TBL_NAME.' table and data to '.$_TBL_NAME.'_bak<br>'.$_nl;
						}

					# Drop existing table if re-installing
						IF ($FullInstall) {
							$query 	= 'DROP TABLE IF EXISTS '.$_TBL_NAME;
							$result	= $db_coin->db_query_execute($query);
							$_cstr	.= '&nbsp;&nbsp;- Dropped existing table '.$_TBL_NAME.' (if any)<br>'.$_nl;
						}

					# Space before next table name
						IF ($_GPV['tbl_bak'] == 1 || $FullInstall) {$_cstr .= '<br>';}
					}
				}
				IF ($_GPV['tbl_bak'] == 1) {
					$_cstr .= '<b>Backup and/or delete existing tables: Completed.</b><br><br>'.$_nl;
				}

			# create our placeholder table
				$result	= $db_coin->db_query_execute('DROP TABLE IF EXISTS '.$_DBCFG['table_prefix'].'install_status');
				$result	= $db_coin->db_query_execute('CREATE TABLE '.$_DBCFG['table_prefix']."install_status (id int(11) NOT NULL auto_increment, datafile varchar(75) NOT NULL default '', theline text NOT NULL, PRIMARY KEY (id)) TYPE=MyISAM COMMENT='Temporary table For tracking installation'");
				$result	= $db_coin->db_query_execute('INSERT INTO '.$_DBCFG['table_prefix']."install_status (id, datafile, theline) VALUES ('','','blank line');");
			} # End Not resuming

		# Now let us read the MySQL command file(s) and process it/them
			$NoGood	= 0;					# No errors yet
			$LineNo	= 0;
			$Loops	= sizeof($SQL_Files);	# Number of command files available, if upgrading
			$_cstr	.= '<b>Create or upgrade tables & populate: Begin</b><br>'.$_nl;

		# If new install, or overwriting the database,
			IF ($FullInstall || !$InstalledVersion) {
				$datafile	= $_PACKAGE['DIR'] . 'coin_setup/sql/setup.sql';
				$fd		= fopen("$datafile", 'r');
				IF (!$fd) {
					$_cstr .= 'Cannot find SQL commands file<br> - '.$datafile.'<br>'.$_nl;
					$NoGood = 1;
				} ELSE {

			    # Let the user see what we are doing
    				$_cstr .= "&nbsp;&nbsp;&nbsp;Processing MySQL commands: setup.sql<br>";

				# Loop through the sql file until done
					while (!feof ($fd)) {
						$LineNo++;
						$buffer = fgets($fd, 8192);
						IF ($buffer) {$error = Do_The_SQL(rtrim($buffer),$datafile,$LineNo);}

					# Terminate on sql error
						IF ($error) {
							$FatalError = $error;
							break;
						}
					}
					fclose($fd);
				}
			} ELSE {

			# Upgrading, so loop through each sql command file in turn
				FOR ($i=1; $i<=$Loops; $i++) {

				# get our command file version/name and break it apart
				    $field = explode('|', $SQL_Files[$i]);

				# create our "datafile" name
					$datafile = $_PACKAGE['DIR'].'coin_setup/sql/'.$field[1];

				# If it is for a higher version of phpCOIN than what is already installed, process it
					IF ($field[0] > $DBVersion) {

					# Let the user see what we are doing
						$_cstr .= "&nbsp;&nbsp;&nbsp;Processing MySQL commands: $field[1]<br>";

					# Open the datafile for reading
						$fd = fopen("$datafile", 'r');

					# If an error opening then bug out
						IF (!$fd) {
							$_cstr .= 'Cannot find SQL commands file<br> - '.$datafile.'<br>'.$_nl;
							$NoGood = 1;
						} ELSE {

						# Else read a line at a time and send to routine to write to database
							$LineNo = 0;
							while (!feof ($fd)) {
								$LineNo++;
								$buffer = fgets($fd, 8192);
								IF ($buffer) {
									$error = Do_The_SQL(rtrim($buffer), $datafile, $LineNo);
								}
							# Terminate on sql error
								IF ($error) {
									$FatalError = $error;
									break;
								}
							}
							fclose($fd);
						}
					}
				}
			}


		# We are done, so build results strings
			IF ($NoGood || $FatalError) {
    				$_cstr .= '<b>Create or upgrade tables and populate: Aborted</b><br><br>'.$_nl;
			} ELSE {
			# Else everything was OK, so drop our status table
				$result = $db_coin->db_query_execute('DROP TABLE IF EXISTS '.$_DBCFG['table_prefix'].'install_status');
	    			$_cstr .= '<b>Create or upgrade tables and populate: Completed</b><br><br>'.$_nl;

			# Increment counter so we know setup was already run
				$_SESSION['setup_ran']++;

			}

		# If full install or overwrite
			IF ($FullInstall || !$InstalledVersion) {
				$_tstr = 'Database Schema Create and Populate';
				If (!$NoGood && !$FatalError) {
        		# If no errors, and new install or overwrite, show link to admin -> parameters -> user
					$_mstr  = '<a href="'.PKG_URL_BASE.'admin.php?cp=parms&fpg=user&w=admin&o=login&username=webmaster&password='.$_GPV['password'].'">'.$_TCFG['_IMG_ADMIN_M'].'</a>';
					IF ($DBVersion == $FileVersion) {$_cstr .= 'Re-';}
					$_cstr .= 'Installation Completed. Click the button below.';
				}

			} ELSE {
			# else it is an upgrade
				$_tstr = 'Database Schema Upgrade and Populate';
				IF (!$NoGood && !$FatalError) {
				# Else if no errors give link to home and admin
					$_mstr  = '<a href="'.PKG_URL_BASE.'">'.$_TCFG['_IMG_HOME_M'].'</a>';
					$_mstr .= '<a href="'.PKG_URL_BASE.'admin.php">'.$_TCFG['_IMG_ADMIN_M'].'</a>';
					$_cstr .= 'Installation Completed. Click one of the buttons below.';
				}
			}

		# If any errors
			IF ($NoGood || $FatalError) {
				IF ($NoGood)		{$_cstr .= 'Please check the datafile location and try again.<br>'.$_nl;}
				IF ($FatalError)	{$_cstr .= $FatalError;}
				$_cstr .= 'Fatal errors encountered. Installation Aborted.';
			}
			$_out	= '<br>'.$_nl;

		# Now show the results of all our hard work
			$_out	.= do_install_block_it($_tstr, $_cstr, $_mstr);
			$_tstr	= '';
			$_cstr	= '';
			$_mstr	= '';
			echo $_out;

		} ELSE {
			IF ($FatalError) {
				IF ($DBVersion < $FileVersion) {
	    				$_cstr	= '<b>Upgrade to v'.$ThisVersionIs.': Aborted</b><br><br>'.$_nl;
	    			} ELSE {
	    				$_cstr	= '<b>Downgrade to v'.$ThisVersionIs.': Aborted</b><br><br>'.$_nl;
	    			}
    				$_out	= do_install_block_it($_tstr, $_cstr, '');
    				echo $_out;
    			}
    		}
	}


/**************************************************************
 * Close Page
**************************************************************/
	echo do_install_page_close();


#######################################################
#	FUNCTIONS LIBRARY                                #
#######################################################

# For php < 4.3 compatability
# replaces html_entity_decode
function unhtmlentities($string) {
	$trans_tbl	= get_html_translation_table(HTML_ENTITIES);
	$trans_tbl	= array_flip($trans_tbl);
	return strtr($string, $trans_tbl);
}


function error_block($block_title, $block_content) {
	global $_CCFG, $_nl;

	# Build Table Start and title
		$_out  = '<html>'.$_nl;
		$_out .= '<head>'.$_nl;
		$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
		$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
		$_out .= '<title>phpCOIN Installation/Upgrade Fatal Error</title>'.$_nl;
		$_out .= '<style media="screen" type="text/css">'.$_nl;
		$_out .= '<!--'.$_nl;
		$_out .= 'body	{ background-color: #FFFFFF; margin: 5px }'.$_nl;
		$_out .= 'p { color: #001; font-family: Verdana, Arial, Helvetica, Geneva }'.$_nl;
		$_out .= '.BLK_DEF_TITLE	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #EBEBEB }'.$_nl;
		$_out .= '.BLK_DEF_ENTRY	{ font-family: Verdana, Arial, Helvetica, Geneva; background-color: #F5F5F5 }'.$_nl;
		$_out .= '.BLK_IT_TITLE	{ color: #001; font-style: normal; font-weight: bold; text-align: left; font-size: 12px; padding: 5px; height: 25px }'.$_nl;
		$_out .= '.BLK_IT_ENTRY	{ color: #001; font-style: normal; font-weight: normal; text-align: left; font-size: 11px; padding: 5px }'.$_nl;
		$_out .= '.BLK_IT_FMENU	{ color: #001; font-style: normal; font-weight: normal; text-align: center; font-size: 11px; padding: 5px }'.$_nl;
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
		$_out .= '<p>'.$block_content.'</p>';
		$_out .= 'Please check that <i>all</i> phpCOIN files have been correctly uploaded to your web-server, then try again.</p>';
		$_out .= '</td></tr>';
		$_out .= '</table>';
		$_out .= '</td></tr>';
		$_out .= '</table>';
		$_out .= '</div>';
		$_out .= '</div>'.$_nl;
		$_out .= '</body>'.$_nl;
		$_out .= '</html>'.$_nl;

	# Echo final output and terminate script
		echo $_out;
		exit();
}



# Do html for standard content block
function do_install_block_it($atitle_text, $acontent_text, $abot_row_menu_text='') {
	global $_nl;

	$_out  = '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
	$_out .= '<tr bgcolor="black"><td bgcolor="black">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
	$_out .= $atitle_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" colspan="2">'.$_nl;
	$_out .= $acontent_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	IF ($abot_row_menu_text) {
		$_out .= '<tr class="BLK_DEF_FMENU"><td class="BLK_IT_FMENU" align="center" valign="top" colspan="2">'.$_nl;
		$_out .= $abot_row_menu_text.$_nl;
		$_out .= '</td></tr>'.$_nl;
	}
	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	return $_out;
}


/**************************************************************
 * Function:	do_install_title_block_it ($atitle_text)
 * Arguments:	$atitle_text	- Block Text
 * Returns:		output return switchable
 * Description:	Function to build module subject block for passed data
 * Notes:
 *	- Uses _WIDTH_CONTENT_AREA var for setting width
**************************************************************/
# Do html for title content block
function do_install_title_block_it($atitle_text) {
	global $_nl;

	$_out  = '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;
	$_out .= '<tr bgcolor="black"><td bgcolor="black">'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
	$_out .= '<tr class="BLK_DEF_TITLE" valign="middle"><td class="BLK_IT_TITLE" colspan="2">'.$_nl;
	$_out .= $atitle_text.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	return $_out;
}


/**************************************************************
 * Function:	do_install_page_header()
 * Returns:		output return switchable
 * Description:	Function to build html for page "header"
 * Notes:
 *	- Opens initial system table and ready for first row (top_row)
**************************************************************/
function do_install_page_header() {
	global $_CCFG, $_nl;

	$_out  = '<html>'.$_nl;
	$_out .= '<head>'.$_nl;
	$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
	$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
	$_out .= '<title>phpCOIN Installation/Upgrade</title>'.$_nl;
	$_out .= '<link href="'.$_CCFG['_PKG_URL_THEME'].'styles.css" rel="styleSheet" type="text/css">'.$_nl;
	$_out .= '</head>'.$_nl;
	$_out .= '<body bgcolor="#00AFAF" link="#0000FF" vlink="#FF0000">'.$_nl;
	$_out .= '<div align="center" width="100%">'.$_nl;
	$_out .= '<!-- Outer Table- 1 Col- span 2-3 -->'.$_nl;
	$_out .= '<table border="0" bordercolor="black" cellpadding="0" cellspacing="0" width="600px">'.$_nl;
	$_out .= '<tr><td valign="top">'.$_nl;
	$_out .= '<!-- Inner Table- 2/3 Col add rules=none here -->'.$_nl;
	$_out .= '<table border="0" bordercolor="black" cellpadding="0" cellspacing="5" width="100%" rules="none">'.$_nl;
	$_out .= '<!-- End page_header -->'.$_nl;

	return $_out;
}


/**************************************************************
 * Function:	do_install_page_top_row()
 * Returns:		output return switchable
 * Description:	Function to build html for page "top row"
**************************************************************/
function do_install_page_top_row() {
	global $_nl;
	$_out  = '<tr height="40"><td colspan="2">'.$_nl;
	$_out .= do_install_page_top_block();
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<!-- Start Content Column -->'.$_nl;
	$_out .= '<tr>'.$_nl;
	$_out .= '<td valign="top" align="center" width="100%">'.$_nl;
	return $_out;
}


/**************************************************************
 * Function:	do_install_page_top_block($aret_flag=0)
 * Returns:		output return switchable
 * Description:	Function to build html for page "top block"
**************************************************************/
function do_install_page_top_block() {
	global $_nl;

	# Build Top Of Page Title Block
		$_out  = '<!-- Start topblock -->'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="black">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">'.$_nl;
		$_out .= '<tr class="BLK_HDR_TITLE" height="40px"><td class="TP3LRG_BL">'.$_nl;
		$_out .= 'phpCOIN Installation/Upgrade'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</td></tr></table>'.$_nl;
		$_out .= '<!-- End topblock -->'.$_nl;

	# Return results
		return $_out;
}

/**************************************************************
 * Function:	do_install_page_footer_block()
 * Returns:		output return switchable
 * Description:	Function to build html for page "footer"
**************************************************************/
function do_install_page_footer_block() {
	global $_nl, $ThisVersion;

	$_out  = '</td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	$_out .= '<!-- End Content Area : End Row 2 -->'.$_nl;

	$_out .= '<!-- Start Footer Row -->'.$_nl;
	$_out .= '<tr height="20"><td valign="middle" colspan="2">'.$_nl;
	$_out .= '<div align="center" valign="middle">'.$_nl;

	$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>'.$_nl;
	$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%"><tr><td class="BLK_FTR_CLEAR_C" valign="middle">'.$_nl;

	$_out .= 'Powered By <a href="http://www.phpcoin.com" target="_blank">phpCOIN</a> v'.$ThisVersion.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;

	$_out .= '</div>'.$_nl;
	$_out .= '</td></tr>'.$_nl;
	$_out .= '<!-- End Footer Row -->'.$_nl;

	return $_out;
}


/**************************************************************
 * Function:	do_install_page_closeout()
 * Returns:		output return switchable
 * Description:	Function to build html for final page closeout
**************************************************************/
function do_install_page_closeout() {
	global $_nl;

	$_out  = '<!-- Close Out Inner/Outer Table and Page Tags -->'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	$_out .= '</div>'.$_nl;
	$_out .= '</body>'.$_nl;
	$_out .= '</html>'.$_nl;

	return $_out;
}


/**************************************************************
 * Function:	do_install_page_open()
 * Arguments:	$aret_flag	- How To Handle Output- 1=return, 0=echo
 * Returns:		output return switchable
 * Description:	Function to build page html from starting tag
 *				to opening column for start of content.
 * Notes:
 *	-
**************************************************************/
function do_install_page_open() {
	# Call page header function
		$_out = do_install_page_header();

	# Call page top row function
		$_out .= do_install_page_top_row();

	# Return results
		return $_out;
}


/**************************************************************
 * Function:	do_install_page_close()
 * Returns:		output return switchable
 * Description:	Function to build page html from closeout of
 *				column for content to final page tag.
**************************************************************/
function do_install_page_close() {
	# Call footer block function- does copyright and tag close out
		$_out = do_install_page_footer_block();

	# Call page closeout function- does page tag close outs
		$_out .= do_install_page_closeout();

	# Return results
		return $_out;
}


/**************************************************************
 * Create some additional required functions
**************************************************************/
# Return current unix timestamp with offset:
function dt_get_uts() {
	global $_CCFG;
	If (!isset($_CCFG['_PKG_DATE_SERVER_OFFSET'])) {$_CCFG['_PKG_DATE_SERVER_OFFSET'] = 0;}
	return time()+($_CCFG['_PKG_DATE_SERVER_OFFSET']*3600);
}


/**************************************************************
 * Function:	do_password_crypt ($apwrd_input)
 * Arguments:	$apwrd_input	- password string to encrypt
 * Returns:		encrypted password string
 * Description:	Function for encrypt passed string
 * Notes:
 *	-
**************************************************************/
function do_password_crypt($apwrd_input) {
	return crypt($apwrd_input);
}


# A single SQL statement is passed in.
# string replacement happens,
# the query *may* be executed,
# the installation progress database is updated,
# and an error string or "0" is returned.
function Do_The_SQL($sql, $thefile, $theline) {
	# Grab necessary global vars
		global $_DBCFG, $db_coin;

	# Create some variables for auto-insert data
		$_time_stamp	= dt_get_uts();

		$pieces		= explode('.', $_SERVER['SERVER_NAME']);
		$precedent	= $pieces[0].'.';
		$TLDomain		= str_replace($precedent, '', $_SERVER['SERVER_NAME']);

		$sql			= str_replace('%DOMAINNAME%', $TLDomain, $sql);
		$sql			= str_replace('%PASSWORD%', do_password_crypt($_DBCFG['dbpass']), $sql);
		$sql			= str_replace('%PREFIX%', $_DBCFG['table_prefix'], $sql);
		$sql			= str_replace('%TIMESTAMP%', $_time_stamp, $sql);

	# See if we have already completed this command.
		$query		= 'SELECT * FROM '.$_DBCFG['table_prefix']."install_status WHERE datafile='".$thefile."' AND theline='".$db_coin->db_sanitize_data($sql)."'";
		$result		= $db_coin->db_query_execute($query);
		$numrows		= $db_coin->db_query_numrows($result);

	# If this line exists it is already completed, so bug-out
		IF ($numrows) {return 0;}

	# Execute the query
		$sh			= $db_coin->db_query_execute($sql);

	# Build result string
		$ErrorCode	= $db_coin->db_error_number();
		IF ($ErrorCode == 1060) {
			# Duplicating column add
			$errorstring = 0;
		} ELSEIF ($ErrorCode == 1050) {
			# Duplicating create table
			$errorstring = 0;
		} ELSEIF ($ErrorCode == 1062) {
			# Duplicating Insert record
			$errorstring = 0;
		} ELSEIF ($ErrorCode == 1091) {
			# No such key/index to be dropped
			$errorstring = 0;
		} ELSEIF ($sh) {
			# No errors
			$errorstring = 0;
		} ELSE {
			# A fatal error that we are not trapping
			$errorstring  = 'Error '.$ErrorCode.' running SQL command: '.$db_coin->db_error_string().'<br>';;
			$errorstring .= '&nbsp;&nbsp;&nbsp;In File: '.$thefile.'<br>';
			$errorstring .= '&nbsp;&nbsp;&nbsp;At Line: '.$theline.'<br>';
			$errorstring .= 'Please check the datafile mentioned above, <i>or</i> check the ';
			$errorstring .= '<a href="http://forums.phpCcoin.com">phpCOIN support forums</a>, ';
			$errorstring .= 'making sure to note the file and line number that caused the problem.<br><br>';
		}

	# Update our installation status
		IF ($errorstring == 0) {
    			$query = 'INSERT INTO '.$_DBCFG['table_prefix']."install_status (id, datafile, theline) VALUES('','".$thefile."', '".$db_coin->db_sanitize_data($sql)."')";
			$result = $db_coin->db_query_execute($query);
		}

	# Return result string
		return $errorstring;
}


function getTrace() {
	global $_CCFG;
	$vDebug = debug_backtrace();
	$vFiles = array();
	$vToDo  = count($vDebug);
	FOR ($i=0; $i<$vToDo; $i++) {
		IF ($i==0) {continue;}		// skip the first one, since it's always this func
		$aFile	= $vDebug[$i];
		$vFiles[] = basename($aFile['file']).' at line '.$aFile['line'].'';
	}
	return $vFiles[0];
}
?>