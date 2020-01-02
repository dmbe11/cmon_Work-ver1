<?php
/**
 * Class: MySQL DataBase Access
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- This file uses imap functions from: http://xeoman.com/code/php/xeoport
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage DataBase
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */

# Code to handle file being loaded by URL
	IF (eregi('db_mysql.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit();
	}


/**************************************************************
 * Database MySQL Classes
 * Class:	db_base
**************************************************************/
class db_base {
	# Dim class vars
		var $dbms;
		var $dbhost;
		var $dbname;
		var $dbuname;
		var $dbpass;
		var $table_prefix;
		var $suppress_errors;
		var $connection;
		var $query_count;
		var $query_strings;

	# Call constructor
		function db_base() {
			# Dim globals
				global $_DBCFG;

			# Dim Vars
				$this->dbms			= $_DBCFG['dbms'];
				$this->dbhost			= $_DBCFG['dbhost'];
				$this->dbname			= $_DBCFG['dbname'];
				$this->dbuname			= $_DBCFG['dbuname'];
				$this->dbpass			= $_DBCFG['dbpass'];
				$this->table_prefix		= $_DBCFG['table_prefix'];
				$this->suppress_errors	= 0;
				$this->connection		= 0;
				$this->query_count		= 0;
				$this->query_strings	= '';

			# Call dbconnect
				$this->db_connect();
		}

		/**************************************************************
		 * Class Functions: base class
		 * Notes:
		 *	- See extended classes for overload code and notes.
		**************************************************************/
		function db_connect() { ; }
		function db_connect_check() { ; }
		function db_select_db() { ; }
		function db_query_execute($query) { ; }
		function db_query_return() { ; }
		function db_error_string() { ; }
		function db_error_number() { ; }
		function db_print_error($error_string) { ; }
		function db_sanitize_data($_inputdata) { ; }
		function db_free_result($result) { ; }
} //end class db_base


/**************************************************************
 * Database MySQL Classes
 * Class:		db_funcs
 * Extends:	db_base class
**************************************************************/
class db_funcs extends db_base {
	# Call constructor
		function db_funcs() {
			# Dim globals
				global $_DBCFG;

			# Dim Vars
				$this->dbms			= $_DBCFG['dbms'];
				$this->dbhost			= $_DBCFG['dbhost'];
				$this->dbname			= $_DBCFG['dbname'];
				$this->dbuname			= $_DBCFG['dbuname'];
				$this->dbpass			= $_DBCFG['dbpass'];
				$this->table_prefix		= $_DBCFG['table_prefix'];
				$this->suppress_errors	= 0;
				$this->connection		= 0;
				$this->query_count		= 0;
				$this->query_strings	= '';
		}

		/**************************************************************
		 * Class Function:	db_connect
		 * Arguments:		none
		 * Returns:			connection result
		 * Notes:
		 *	- Performs connect and select
		 *	- Calls print error on error
		**************************************************************/
		function db_connect() {
			$this->connection = @mysql_connect($this->dbhost, $this->dbuname, $this->dbpass);

			IF ($this->connection == 0) {
				$_error_string  = 'Unable to establish connection with the database (' .$this->dbname.').<br />';
				$_error_string .= 'Error returned is: (' .$this->db_error_number().' : '.$this->db_error_string().')<br />';
				$_error_string .= 'Check the hostname, username, password, and the server connection and try again.';
				$this->db_print_error($_error_string);
			}

			return $this->connection;
		}


		/**************************************************************
		 * Class Function:	db_connect_check()
		 * Arguments:		none
		 * Returns:		connection result
		 * Notes:
		 *	- Does connect test by select of db.
		 *	- Attempts (1) reconnect if failed.
		**************************************************************/
		function db_connect_check() {
			$this->connection = $this->db_select_db();
			IF (!$this->connection) {$this->db_connect();}
			return $this->connection;
		}


		/**************************************************************
		 * Class Function:	db_query_execute($query)
		 * Arguments:		$query	- Executable SQL statement.
		 * Returns:		$result
		 * Notes:
		 *	- Does connect test first and error.
		 *	- Does error check on rows returned.
		**************************************************************/
		function db_query_execute($query) {
			global $_CCFG;

			$this->db_connect_check();
			IF ($this->connection == 0) {
				$_error_string  = 'Unable to complete query due to connection errors with the database ('.$this->dbname.')<br />';
				$_error_string .= 'Error returned is: (' .$this->db_error_number().' : '.$this->db_error_string().')<br />';
				$_error_string .= 'Check the database name and the server connection and try again.';
				$this->db_print_error($_error_string);
			} ELSE {
				$result = mysql_query($query);
				$this->query_count++;
				$this->query_strings .= '<br>'.$this->query_count.'-'.$query.'<br>';

				IF ($result == 0) {
					IF ($_CCFG['_debug_queries']) {
						$_error_string  = 'Unable to execute query: '.$query.'<br />';
						$_error_string .= 'Error returned is: (' .$this->db_error_number().' : '.$this->db_error_string().').<br />';
						$_error_string .= 'Check the syntax / server connection and and try again.';
						$_error_string .= '<br>Called by: '.getTrace();
					} ELSE {
						$_error_string  = 'Unable to complete operation due to an invalid MySQL query. Admin should edit config.php and set -->> $_CCFG[_debug_queries] = 1 <<-- then try this operation again.<br />';
					}
					$this->db_print_error($_error_string);
				}
				return($result);
			}
		}


		/**************************************************************
		 * Class Function:	db_query_count()
		 * Arguments:		none
		 * Returns:		integer	- query count
		 * Notes:
		 *	- none
		**************************************************************/
		function db_query_count() {
			return $this->query_count;
		}


		/**************************************************************
		 * Class Function:	db_query_strings()
		 * Arguments:		none
		 * Returns:		string	- query string
		 * Notes:
		 *	- none
		**************************************************************/
		function db_query_strings() {
			return $this->query_strings;
		}


		/**************************************************************
		 * Class Function:	db_fetch_array($result)
		 * Arguments:		$result	- Valid query result set
		 * Returns:		$row	- Record as array ($row)
		 * Notes:
		 *	- Does connect test first.
		**************************************************************/
		function db_fetch_array($result) {
			$this->db_connect_check();
			IF ($result) {
				return @mysql_fetch_array($result);
			} ELSE {
				return 0;
			}
		}


		/**************************************************************
		 * Class Function:	db_fetch_row($result)
		 * Arguments:		$result	- Valid query result set
		 * Returns:		row		- Record as row variables
		 * Notes:
		 *	- Does connect test first.
		**************************************************************/
		function db_fetch_row($result) {
			$this->db_connect_check();
			IF ($result) {
				return @mysql_fetch_row($result);
			} ELSE {
				return 0;
			}
		}


		/**************************************************************
		 * Class Function:	db_query_numrows($result)
		 * Arguments:		$result	- Valid query result set
		 * Returns:		integer	- Numbers of rows in set
		 * Notes:
		 *	- Does connect test first.
		**************************************************************/
		function db_query_numrows($result) {
			$this->db_connect_check();
			IF ($result) {
				return @mysql_num_rows($result);
			} ELSE {
				return 0;
			}
		}


		/**************************************************************
		 * Class Function:	db_query_insertid()
		 * Arguments:		none
		 * Returns:		integer	- Inserted record id
		 * Notes:
		 *	- Does connect test first.
		**************************************************************/
		function db_query_insertid() {
			return @mysql_insert_id();
		}


		/**************************************************************
		 * Class Function:	db_query_affected_rows()
		 * Arguments:		none
		 * Returns:		integer	- number of rows affected
		 * Notes:
		 *	- Do not do connect check or will always return false.
		**************************************************************/
		function db_query_affected_rows() {
			return @mysql_affected_rows();
		}


		/**************************************************************
		 * Class Function:	db_query_return()
		 * Arguments:		none
		 * Returns:		none
		 * Notes:
		 *	- Future return query results ??
		**************************************************************/
		function db_query_return() { ; }


		/**************************************************************
		 * Class Function:	db_error_string()
		 * Arguments:		none
		 * Returns:		string	- returns mysql error string
		 * Notes:
		 *	- none
		**************************************************************/
		function db_error_string() {
			return mysql_error();
		}


		/**************************************************************
		 * Class Function:	db_error_number()
		 * Arguments:		none
		 * Returns:		long	- returns mysql error number
		 * Notes:
		 *	- none
		**************************************************************/
		function db_error_number() {
			return mysql_errno();
		}


		/**************************************************************
		 * Class Function:	db_print_error($error_string)
		 * Arguments:		$error_string	- desired error string.
		 * Returns:		none
		 * Notes:
		 *	- Checks error suppression flag prior to call of print.
		**************************************************************/
		function db_print_error($error_string) {
			IF (!$this->suppress_errors) {
				$block_title	= 'Database Error';
				$block_content	= $error_string;
				$this->db_error_block($block_title, $block_content);
			}
		}


		/**************************************************************
		 * Class Function:	db_error_block($block_title, $block_content)
		 * Arguments:		$block_title	- Error Table Title text
		 *				$block_content	- Error Table Content text
		 * Returns:		none
		 * Notes:
		 *	- Called for display of database error.
		 *	- Should probably just redirect (may / may not need header)
		 *	- Currently- tries to load nice table for message.
		**************************************************************/
		function db_error_block($block_title, $block_content) {
			global $_nl;

			IF (!$_CCFG['_DB_PKG_THEME']) {

			# Build Table Start and title
				$_out  = '<html>'.$_nl;
				$_out .= '<head>'.$_nl;
				$_out .= '<meta http-equiv="content-type" content="text/html;charset='.$_CCFG['ISO_CHARSET'].'">'.$_nl;
				$_out .= '<meta name="generator" content="phpcoin">'.$_nl;
				$_out .= '<title>Database Error</title>'.$_nl;
				$_out .= '<style media="screen" type="text/css">'.$_nl;
				$_out .= '<!--'.$_nl;
				$_out .= 'body			{background-color: #FFFFFF; margin: 5px}'.$_nl;
				$_out .= 'p			{color: #001; font-family: Verdana, Arial, Helvetica, Geneva}'.$_nl;
				$_out .= '.BLK_DEF_TITLE	{font-family: Verdana, Arial, Helvetica, Geneva; background-color: #EBEBEB}'.$_nl;
				$_out .= '.BLK_DEF_ENTRY	{font-family: Verdana, Arial, Helvetica, Geneva; background-color: #F5F5F5}'.$_nl;
				$_out .= '.BLK_IT_TITLE	{color: #001; font-style: normal; font-weight: bold; text-align: left; font-size: 12px; padding: 5px; height: 25px}'.$_nl;
				$_out .= '.BLK_IT_ENTRY	{color: #001; font-style: normal; font-weight: normal; text-align: justify; font-size: 11px; padding: 5px}'.$_nl;
				$_out .= '--></style>'.$_nl;
				$_out .= '</head>'.$_nl;
				$_out .= '<body bgcolor="#FFFFFF" link="blue" vlink="red">'.$_nl;
				$_out .= '<div align="center" width="100%">'.$_nl;
				$_out .= '<br>';
				$_out .= '<div align="center" width="100%">';
				$_out .= '<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#000000">';
				$_out .= '<tr bgcolor="#000000"><td bgcolor="#000000">';
				$_out .= '<table border="0" cellpadding="0" cellspacing="1" width="100%">';
				$_out .= '<tr class="BLK_DEF_TITLE" height="30" valign="middle"><td class="BLK_IT_TITLE" align="left">';
				$_out .= $block_title;
				$_out .= '</td></tr>';
				$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" align="left">';
				$_out .= $block_content;
				$_out .= '</td></tr>';
				$_out .= '</table>';
				$_out .= '</td></tr>';
				$_out .= '</table>';
				$_out .= '</div>';
				$_out .= '</div>'.$_nl;
				$_out .= '</body>'.$_nl;
				$_out .= '</html>'.$_nl;

			} ELSE {
				$_out .= '<div align="center" width="100%">'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr 		= $block_title;
				$_cstr 		= $block_content;
				$_cstr		.= '<br>Called by: '.getTrace();
				$_mstr_flag	= 0;
				$_mstr 		= '';

			# Call block it function
				$_out .= '<br>'.$_nl;
				$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
				$_out .= '<br>'.$_nl;

				$_out .= '</div>'.$_nl;
			}

		# Echo final output
			echo $_out;

		}


		/**************************************************************
		 * Class Function:	db_select_db()
		 * Arguments:		none
		 * Returns:		connection result flag
		 * Notes:
		 *	- Does connect test first.
		 *	- Will gen error on no select of db (ie no connect)
		**************************************************************/
		function db_select_db() {
			$this->connection = @mysql_select_db("$this->dbname");
			IF (!$this->connection) {
				$_error_string  = 'Unable to select the database (' .$this->dbname.')<br />';
				$_error_string .= 'Error returned is: ('.$this->db_error_number().' : '.$this->db_error_string().')<br />';
				$_error_string .= 'Check the database name and the server connection and try again.';
				$this->db_print_error($_error_string);
			}
			return $this->connection;
		}


		/**************************************************************
		 * Class Function:	db_return_prefix()
		 * Arguments:		none
		 * Returns:		string	- DB Config. table prefix value
		 * Notes:
		 *	- none
		**************************************************************/
		function db_return_prefix() {
			return $this->table_prefix;
		}


		/**************************************************************
		 * Class Function:	db_set_suppress_errors($suppress_flag)
		 * Arguments:		$suppress_flag	- indicate desired setting.
		 * Returns:		integer			- final setting
		 * Notes:
		 *	- Sets the current instance error supression message.
		 *	- Operation: 1=suppress error printing; 0=print errors
		**************************************************************/
		function db_set_suppress_errors($suppress_flag) {
			# echo "<br>Old Error Suppression: $this->suppress_errors<br>";
			$this->suppress_errors = $suppress_flag;
			# echo "<br>New Error Suppression: $this->suppress_errors<br>";
			return $this->suppress_errors;
		}


		/**
		 * Sanitize data for insertion into MySQL database
		 * @uses get_magic_quotes_gpc() to determine if phpCOIN should run stripslashes on the input first
		 * @uses mysql_real_escape_string() to "sanitize" the input
		 * @param string $_inputdata String to be "sanitized"
		 * @return string "sanitized" string
		 */
		function db_sanitize_data($_inputdata) {
			IF (function_exists('get_magic_quotes_gpc')) {
				$value = (get_magic_quotes_gpc()) ? (stripslashes($_inputdata)) : ($_inputdata);
			} ELSE {
				$value = $_inputdata;
			}
			return @mysql_real_escape_string($value);
		}


		function db_free_result($result) {
			return @mysql_free_result($result);
		}

		function db_close() {
			@mysql_close();
		}

} //end class db_funcs
?>