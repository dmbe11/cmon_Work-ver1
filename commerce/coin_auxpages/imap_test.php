<?php
/**
 * Auxpage: IMAP Connection Tester
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage IMAP
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (!eregi('auxpage.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=auxpage.php?page=imap_test');
	}


# Get security vars
	$_SEC = get_security_flags();


# Do Admin Logged in check ~ goto login of not a logged-in admin
	IF (!$_SEC['_sadmin_flg']) {
		echo do_login($data, 'admin', 1).$_nl;


# Good to go, so start the html document
	} ELSE {
		echo '<style type="text/css">'.$_nl;
//		echo 'body		{margin: 15px; font-family: Verdana, Arial, Helvetica, Geneva, sans-serif; font-size: 11px;}'.$_nl;
		echo 'fieldset		{border: 1px solid #6597CD; margin-top: 0px; margin-bottom: 8px; padding: 5px; width: 550px;}'.$_nl;
		echo 'legend		{color: black; font-weight: bold; border: 1px solid #6597CD; padding-top: 2px; padding-bottom: 2px; padding-left: 5px; padding-right: 5px; margin-bottom: 5px;}'.$_nl;
		echo 'label		{width: 200px; padding: 0px; padding-right: 10px; font-weight: normal; text-align: right; margin: 0px; margin-bottom: 5px; float: left; display: block;}'.$_nl;
		echo 'resultlabel	{width: 200px; background-color: white; padding: 0px; padding-right: 10px; font-weight: bold; text-align: right; margin: 0px; margin-bottom: 5px; float: left; display: block;}'.$_nl;
		echo 'resultdata	{background-color: white; font-style: normal; text-align: left; font-size: 11px; float: left; border: none;}'.$_nl;
		echo 'br		{clear: both;}'.$_nl;
		echo '</style>'.$_nl;


	# Draw the test data-entry form
		echo '<form method="post">'.$_nl;
		echo '<input type="hidden" name="stage" value="1">'.$_nl;
		echo '<fieldset>'.$_nl;
		echo '<legend>IMAP Connection Test Settings</legend><br>'.$_nl;
		echo '<p align="left">:143/IMAP" seems to work better than "/POP:110".<br>You may also need to try /:143/IMAP/NOTLS or /:110/POP/NOTLS depending on your mailserver</p>';
		echo '<label for "HELPDESK_AUTO_IMPORT_SERVER">IMAP Server Name: </label>'.$_nl;
		echo '<input type="text" name="HELPDESK_AUTO_IMPORT_SERVER" value="'.$_GPV['HELPDESK_AUTO_IMPORT_SERVER'].'" length="50"><br>'.$_nl;
		echo '<label for "HELPDESK_AUTO_IMPORT_TYPE">Connection String: </label>'.$_nl;
		echo '<input class="bad_data" type="text" name="HELPDESK_AUTO_IMPORT_TYPE" value="'.$_GPV['HELPDESK_AUTO_IMPORT_TYPE'].'" length="50"><br>'.$_nl;
		echo '<label for "HELPDESK_AUTO_IMPORT_USERID">UserName: </label>'.$_nl;
		echo '<input type="text" name="HELPDESK_AUTO_IMPORT_USERID" value="'.$_GPV['HELPDESK_AUTO_IMPORT_USERID'].'" length="50"><br>'.$_nl;
		echo '<label for "HELPDESK_AUTO_IMPORT_PASSWORD">Password: </label>'.$_nl;
		echo '<input type="text" name="HELPDESK_AUTO_IMPORT_PASSWORD" value="'.$_GPV['HELPDESK_AUTO_IMPORT_PASSWORD'].'" length="50"><br>'.$_nl;
		echo '<input type="submit" value="Test Connection">'.$_nl;
		echo '</fieldset>'.$_nl;
		echo '</form>'.$_nl;


	# If FORM has been POSTed, then process it
		IF ($_GPV['stage'] == 1) {

		# Test IMAP connection by checking for new messages
			$mailbox = imap_open('{'.$_GPV['HELPDESK_AUTO_IMPORT_SERVER'].$_GPV['HELPDESK_AUTO_IMPORT_TYPE'].'}INBOX', $_GPV['HELPDESK_AUTO_IMPORT_USERID'], $_GPV['HELPDESK_AUTO_IMPORT_PASSWORD'], '', 1);

		# Did it connect?
			$buggy = imap_errors();

		# No, so show actual strings to use
			IF ($buggy) {
				echo '<fieldset>'.$_nl;
				echo '<legend>IMAP Connection Error</legend><br>'.$_nl;
				echo '<label class="resultlabel">IMAP Error:</label>'.$_nl;
				echo $buggy[0].'<br>'.$_nl;
				echo '<label class="resultlabel">To Correct:</label>'.$_nl;

			# Check for specific error messages so we can display diagnostics
				IF (strpos($buggy[0], 'Host not found') !== FALSE) {
					IF (substr($_GPV['HELPDESK_AUTO_IMPORT_TYPE'], 0, 1) != ':' && substr($_GPV['HELPDESK_AUTO_IMPORT_TYPE'], 0, 1) != '/') {
						echo '<span class="resultdata">Check The Connection String ~ it <i>should</i> start with : or /</span>'.$_nl;
					} ELSE {
						echo '<span class="resultdata">Check the IMAP Server Name</span>'.$_nl;
					}
				}
				IF (strpos($buggy[0], 'invalid remote specification') !== FALSE) {
					echo '<span class="resultdata">Use a different Connection String</span>'.$_nl;
				}
				IF (strpos($buggy[0], 'LOGIN failed') !== FALSE) {
					echo '<span class="resultdata">Check the UserName and/or Password. It is also possible that a bad password has now locked out the account on the server ~ you may have to wait to try again until the reset period is passed.</span>'.$_nl;
				}

			# Close the form
				echo '</fieldset>'.$_nl;


		# Yes, so show strings to use
			} ELSE {
				echo '<fieldset>';
				echo '<legend>IMAP Connection Success</legend><br />'.$_nl;
				echo '<p>Use the following settings in Admin -> Parameters:<br />'.$_nl;
				echo '<label>automation -> helpdesk -> Helpdesk -> Auto-import server: </label>'.$_nl;
				echo $_GPV['HELPDESK_AUTO_IMPORT_SERVER'].'<br />'.$_nl;
				echo '<label>automation -> helpdesk -> Helpdesk -> Auto-import protocol: </label>'.$_nl;
				echo $_GPV['HELPDESK_AUTO_IMPORT_TYPE'].'<br />'.$_nl;
				echo '<label>automation -> helpdesk -> Helpdesk -> Auto-import mailbox: </label>'.$_nl;
				echo $_GPV['HELPDESK_AUTO_IMPORT_USERID'].'<br />'.$_nl;
				echo '<label>automation -> helpdesk -> Helpdesk -> Auto-import password: </label>'.$_nl;
				echo $_GPV['HELPDESK_AUTO_IMPORT_PASSWORD'];
				echo '</fieldset>'.$_nl;
			}

		# Close the connection, and the page.
			imap_close($mailbox);
		}
	}
?>