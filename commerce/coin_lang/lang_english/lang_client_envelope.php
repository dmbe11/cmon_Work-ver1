<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Clients
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_client_envelopes.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit();
	}



/**************************************************************
 * Envelope Template
**************************************************************/
# Enter the envelope info EXACTLY as you want it to appear, including tabs for
# indents.  If you change the default indent from 5 tabs to anything else, you
# WILL need to adjust the str_replace lines (starting at line 135 in the auxpage)
# so this script can automatically remove any blank lines within the client
# address.  You must also enter TWO blank lines for each desired line between
# the sender address and the client address blocks, because the code that
# removes a blank line within the sender address simply looks for consecutive
# linefeeds.  In addition to the tags shown below, you may also use
# %SENDER_PHONE% %SENDER_FAX% %SENDER_TOLL_FREE% %SENDER_TAXNO% and
# %CLIENT_PHONE% DOUBLE the number of blank lines desired between the sender and
# client address so that when the script removes the "blank" lines the line
# spacing will be as desired.
	$_ENVELOPE_TEMPLATE = '
%SENDER_COMPANY%
%SENDER_TAGLINE%
%SENDER_STREET_1%
%SENDER_STREET_2%
%SENDER_CITY%, %SENDER_STATE%  %SENDER_PCODE%
%SENDER_COUNTRY%












					%CLIENT_NAME_FIRST% %CLIENT_NAME_LAST%
					%CLIENT_COMPANY%
					%CLIENT_STREET_1%
					%CLIENT_STREET_2%
					%CLIENT_CITY%, %CLIENT_STATE%  %CLIENT_PCODE%
					%CLIENT_COUNTRY%
';	// END OF EMVELOPE TEMPLATE




/**************************************************************
 * Language Variables
**************************************************************/

	$_LANG['Envelopes']['Browser_Title']		= 'phpCOIN Envelope Printer';

	$_LANG['Envelopes']['Form_Title']			= 'Print Client Envelope';
	$_LANG['Envelopes']['Form_Legend']			= 'Select Client For Envelope';
	$_LANG['Envelopes']['Form_Instructions']	= 'Enter the record id of the client that the envelope should be addressed to.';
	$_LANG['Envelopes']['Form_CLID']			= 'Client ID:';
	$_LANG['Envelopes']['Form_Submit']			= 'Prepare Envelope';

	$_LANG['Envelopes']['Error_No_Such_Client']	= 'No client record with ID '.$_GPV['cl_id'];


?>