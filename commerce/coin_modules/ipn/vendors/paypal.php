<?php
/**
 * Module: IPN (Vendor: PayPal)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- ipn is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Payments
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Include session file (auto-loads core)
	require_once('../../../coin_includes/session_set.php');

# Initialize variables. Make sure these are ALL zero, or else!
	$_CONFIRMED				= 0;		// Not a valid payment
	$_PROCESS					= 0;		// Do NOT try to apply payment
	$fsocket					= 0;		// No valid socket
	$_CCFG['PAYPAL_DEBUGFILE']	= str_replace('%TIMESTAMP%', time(), $_CCFG['PAYPAL_DEBUGFILE']);

# Get referer (if any) else get remote IP and convert to a name
	IF (isset($_SERVER['HTTP_REFERER'])) {
		$_ref = $_SERVER['HTTP_REFERER'];
	} ELSE {
		$_ref = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	}

# Start debug file
	IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
		$open_file = fopen($_CCFG['PAYPAL_DEBUGFILE'], "wb");
		fputs($open_file, 'Incoming Connection From: '.$_SERVER['REMOTE_ADDR']."\r\n");
	}

# Exit if we should NOT accept IPN data
	IF (!$_CCFG['IPN_ACCEPT_INCOMING']) {
		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "Not Accepting IPN - Exiting IPN\r\n\r\n");
			fclose($open_file);
		}
		exit();
	}


# If incoming connection is from ourselves (testing):
#	- set "good data" flag
#	- Do NOT set "proceed" flag
	IF (strpos($_ref, BASE_HREF) !== FALSE) {
		$_CONFIRMED++;

		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "  Connection Is From Self-Testing\r\n");
		}

# If incoming connection is from PayPal:
#	- Do NOT set "good data" flag
#	- Set "proceed" flag
	} ELSEIF (strpos($_ref, '.paypal.com') !== FALSE) {
		$_PROCESS++;

		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "  Connection Is From PayPal\r\n");
		}
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

# Include class file
	require_once(PKG_PATH_MDLS.'/ipn/ipn.class.php');

# create ipn object
	$ipn = new ipn;

# set variables
	$ipn->set_txn_vendor('PayPal');
	$ipn->payment_completed	= 'Completed';
	$ipn->payment_denied	= 'Denied';
	$ipn->payment_failed	= 'Failed';
	$ipn->payment_pending	= 'Pending';
	$ipn->payment_refunded	= 'Refunded';
	$ipn->payment_reversed	= 'Reversed';

	$ipn->set_debug_on($_GPV['debug']);

	$ipn->set_txn_firstname($_GPV['first_name']);
	$ipn->set_txn_lastname($_GPV['last_name']);
	$ipn->set_txn_payer_email($_GPV['payer_email']);
	$ipn->set_txn_cl_id($ipn->do_get_client_id());
	$ipn->set_txn_timestamp(strtotime($_GPV['payment_date']));
	$ipn->set_txn_line($ipn->do_format_gpv($_POST));

	$ipn->set_txn_id($_GPV['txn_id']);
	$ipn->set_txn_parent_id($_GPV['parent_txn_id']);

	$ipn->set_txn_type($_GPV['txn_type']);
	$ipn->set_txn_status($_GPV['payment_status']);

	$ipn->set_txn_payment_type($_GPV['payment_type']);
	$ipn->set_txn_gross($_GPV['mc_gross']);
	$ipn->set_txn_currency($_GPV['mc_currency']);

	$ipn->set_txn_pending_reason($_GPV['pending_reason']);
	$ipn->set_txn_invc_id($_GPV['invoice']);
	$ipn->set_txn_subscr_id($_GPV['subscr_id']);
	$ipn->set_txn_receiver_email($_GPV['receiver_email']);

# If connection is from paypal process incoming data according to paypal instructions
	IF ($_PROCESS) {

	# Determine if incoming call is live site or sandbox site and set call-back URL accordingly
		IF (strpos($_ref, 'sandbox.paypal.com') !== FALSE) {
			$_cburl = 'www.sandbox.paypal.com';
		} ELSE {
			$_cburl = 'www.paypal.com';
		}

	# Add call-back URL
		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "  PayPal Call-Back URL: $_cburl\r\n");
		}

	# Open a connection to paypal
		IF (!$fp = fsockopen($_cburl, 80, $errno, $errstr, 30)) {

		# If unable to open connection, write debug log
			IF ($_CCFG['PAYPAL_WRITEDEBUG']) {fputs($open_file, "  Unable To Open Socket To PayPal\r\n");}

		} ELSE {
		# Write debug log
			IF ($_CCFG['PAYPAL_WRITEDEBUG']) {fputs($open_file, "  Socket To PayPal Opened\r\n");}

		# Add 'cmd' to the return string
			$req = 'cmd=_notify-validate';

		# Clean inputs and append to return string
			foreach ($_POST as $key => $value) {$req .= "&$key=".urlencode(stripslashes($value));}

		# Post the data back to paypal
			fputs($fp, "POST /cgi-bin/webscr HTTP/1.1\r\n");
			fputs($fp, "Host: ".$_cburl.":80\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: " . strlen($req) . "\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $req . "\r\n\r\n");

		# Write debug log
			IF ($_CCFG['PAYPAL_WRITEDEBUG']) {fputs($open_file, '  Sending Data To PayPal: '.$req."\r\n");}

		# PayPal then sends confirmation back to your server with a single
		# word, "VERIFIED" or "INVALID", so grab whatever paypal responds
		# with and add it to a string until we have the entire reply
			$_res = '';
			while (!feof($fp)) {$_res .= fgets($fp, 1024);}

		# Send "200 OK" response so PayPal will not keep trying to post data
			fputs($fp, "HTTP/1.1 200 OK\r\n\r\n");

		# Close the connection
			fclose($fp);

		# Write debug log
			IF ($_CCFG['PAYPAL_WRITEDEBUG']) {fputs($open_file, '  Received From Paypal: '.$_res."\r\n");}


		# Now look for verification code in the final result, and if found
		# set "good data" flag
			IF (strpos($_res, 'VERIFIED') !== FALSE) {$_CONFIRMED++;}

		}

	# Close debug log
		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "Closing IPN Connection Log\r\n\r\n");
			fclose($open_file);
		}

	}

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from PayPal), process it
	IF ($_CONFIRMED) {

	# Update old db information
		IF ($_GPV['resubmit']) {$ipn->do_save_old_txn($ipn->txn_id);}

	# Set credit strings
		IF ($ipn->txn_status == $ipn->payment_refunded || $ipn->txn_status == $ipn->payment_reversed) {
			$ipn->set_txn_type($_GPV['reason_code']);
		}

	# Subcriptions do not have txn_id, but rather subscr_id
		IF (isset($ipn->txn_subscr_id) && !isset($ipn->txn_id)) {
			$ipn->set_txn_id($ipn->txn_subscr_id);
		}
	}

# Log transaction data, regardlesss of valid or not.
# This is so we can re-submit it later
	$ipn->do_log_ipn();

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from PayPal), process it
	IF ($_CONFIRMED) {

	# Check to make sure recipient is correct
		IF (($_cburl == 'www.sandbox.paypal.com' && strpos($_CCFG['PAYPAL_SBEMAIL'], $ipn->txn_receiver_email) === FALSE)
		|| (strpos($_CCFG['PAYPAL_RECEIVER_EMAIL'], $ipn->txn_receiver_email) === FALSE)) {
			$ipn->set_throw_user_mismatch(1);
		}

	# Determine if TXN number has been used before
		IF ($ipn->do_get_txn_count() > 1 && strpos($ipn->txn_id, 'S-') === FALSE && $ipn->txn_payment_type != 'echeck' && !$_CCFG['IPN_ALLOW_RESUBMIT']) {
			$ipn->set_throw_dup_mismatch(1);
		}

	# Process transaction
		$ipn->process_ipn();

# Redirect to main page if NOT paypal or test mode (prevent hackers from POSTing a payment)
	} ELSE {
		IF ($_CCFG['PAYPAL_WRITEDEBUG']) {
			fputs($open_file, "Invalid Connection Attempt - Exiting IPN\r\n\r\n");
			fclose($open_file);
		}

		$url = BASE_HREF;
		header("Location: $url");
	}
?>