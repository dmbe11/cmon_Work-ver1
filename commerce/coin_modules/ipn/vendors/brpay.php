<?php
/**
 * Module: IPN (Vendor: BRPay)
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
	$_CCFG['BRPAY_DEBUGFILE']	= str_replace('%TIMESTAMP%', time(), $_CCFG['BRPAY_DEBUGFILE']);

# Exit if we should NOT accept IPN data
	IF (!$_CCFG['IPN_ACCEPT_INCOMING']) {exit();}

# Get referer (if any) else get remote IP and convert to a name
	IF (isset($_SERVER['HTTP_REFERER'])) {
		$_ref = $_SERVER['HTTP_REFERER'];
	} ELSE {
		$_ref = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	}

# If incoming connection is from ourselves (testing):
#	- set "good data" flag
#	- Do NOT set "proceed" flag
	IF (strpos($_ref, BASE_HREF) !== FALSE) {
		$_CONFIRMED++;

# If incoming connection is from PayPal:
#	- Do NOT set "good data" flag
#	- Set "proceed" flag
	} ELSEIF (strpos($_ref, 'brpay.com.br') !== FALSE) {
		$_PROCESS++;
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

# Include class file
	require_once(PKG_PATH_MDLS.'/ipn/ipn.class.php');

# create ipn object
	$ipn = new ipn;

	# set variables
		$ipn->set_txn_payer_email($_GPV['cliente_email']);
		$ipn->set_txn_timestamp(dt_get_uts());
		$ipn->set_txn_vendor('brpay');
		$ipn->set_txn_line($ipn->do_format_gpv($_POST));

	# not translated yet
		$ipn->set_txn_payment_type($_GPV['payment_type']);
		$ipn->set_txn_subscr_id($_GPV['subscr_id']);
		$ipn->set_txn_id($_GPV['txn_id']);
		$ipn->set_txn_lastname($_GPV['last_name']);
		$ipn->set_txn_firstname($_GPV['first_name']);
		$ipn->set_txn_status($_GPV['payment_status']);
		$ipn->set_txn_type($_GPV['txn_type']);
		$ipn->set_txn_pending_reason($_GPV['pending_reason']);
		$ipn->set_txn_parent_id($_GPV['parent_txn_id']);

	# Finish variable assignments
		$ipn->set_txn_cl_id($ipn->do_get_client_id());
		$ipn->set_txn_gross($_GPV['item_valor']);
		$ipn->set_txn_currency($_GPV['moeda']);
		$ipn->set_txn_reciever_email($_GPV['email_cobranca']);
		$ipn->set_txn_invc_id($_GPV['ref_transacao']);
		$ipn->set_debug_on($_GPV['debug']);


# If connection is from paypal process incoming data according to paypal instructions
	IF ($_PROCESS) {

	# Determine if incoming call is live site or sandbox site and set call-back URL accordingly
		IF (strpos($_ref, 'sandbox.brpay.com.br') !== FALSE) {
			$_cburl = 'sandbox.brpay.com.br';
		} ELSE {
			$_cburl = 'www.brpay.com.br';
		}

	# Create debug log and add call-back URL
		IF ($_CCFG['BRPAY_WRITEDEBUG']) {
			$open_file = fopen($_CCFG['BRPAY_DEBUGFILE'], "wb");
			fputs($open_file, "BRPay Call-Back URL: $_cburl\r\n\r\n");
		}

	# Open a connection to paypal
		IF (!$fp = fsockopen($_cburl, 80, $errno, $errstr, 30)) {

		# If unable to open connection, write debug log
			IF ($_CCFG['BRPAY_WRITEDEBUG']) {fputs($open_file, "Unable To Open Socket To BRPay\r\n\r\n");}

		} ELSE {
		# Write debug log
			IF ($_CCFG['BRPAY_WRITEDEBUG']) {fputs($open_file, "Socket To BRPay Opened\r\n\r\n");}

		# Start building callback info
			$req	= 'Comando=validar&Token='.$_CCFG['BRPPAY_TOKEN'];

		# Clean inputs and append to return string
			foreach ($_POST as $key => $value) {$req .= "&$key=".urlencode(stripslashes($value));}

		# Post the data back to BRPay
			fputs($fp, "POST /Security/NPI/Default.aspx HTTP/1.0\r\n");
			fputs($fp, "Host: ".$_cburl.":80\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: " . strlen($req) . "\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $req . "\r\n\r\n");

		# Write debug log
			IF ($_CCFG['BRPAP_WRITEDEBUG']) {fputs($open_file, 'Sending Data To BRPay: '.$req."\r\n\r\n");}

		# BRPay then sends confirmation back to your server with a single
		# word, "VERIFICADO" or "?????", so grab whatever BRPay responds
		# with and add it to a string until we have the entire reply
			$_res = '';
			while (!feof($fp)) {$_res .= fgets($fp, 1024);}

		# Close the connection
			fclose($fp);

		# Write debug log
			IF ($_CCFG['BRPAY_WRITEDEBUG']) {fputs($open_file, 'Received From BRPay: '.$_res."\r\n\r\n");}

		# Now look for verification code in the final result, and if found
		# set "good data" flag
			IF (strpos($_res, 'VERIFICADO') !== FALSE) {$_CONFIRMED++;}

		}

	# Close debug log
		IF ($_CCFG['BRPAY_WRITEDEBUG']) {fclose($open_file);}

	}


# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from BRPay), process it
	IF ($_CONFIRMED) {

	# Include language file (must be after parameter load to use them)
		require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

	# Include class file
		require_once(PKG_PATH_MDLS.'ipn/ipn.class.php');

	# Set a "callback vendor" flag and a "paid/not paid" flag in the "orders_sessions" table
		IF ($_CONFIRMED) {$_Status = 1;} ELSE {$_Status = 0;}
		$_ord_id   = $_GPV['Referencia'];
		$query_cl  = 'UPDATE '.$_DBCFG['orders_sessions'];
		$query_cl .= " SET os_ord_cbflag=1, os_ord_cbpaid='".$_Status."'";
		$query_cl .= " WHERE os_ord_id='".$_ord_id."'";
		$result_cl = $db_coin->db_query_execute($query_cl) OR DIE('Unable to complete request');

	# Update old db information
		IF ($_GPV['resubmit']) {$ipn->do_save_old_txn($ipn->txn_id);}

	# Set credit strings
		IF ($ipn->txn_status == 'Refunded' || $ipn->txn_status == 'Reversed') {
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

	# check make sure user is correct
		IF (($_cburl == 'www.sandbox.brpay.com.br' && strpos($_CCFG['BRPAY_SBEMAIL'], $ipn->txn_receiver_email) === FALSE)
		|| (strpos($_CCFG['BRPAY_RECEIVER_EMAIL'], $ipn->txn_receiver_email) === FALSE)) {
			$ipn->set_throw_user_mismatch(1);
		}

	# Determine if TXN number has been used before
		IF ($ipn->do_get_txn_count() > 1 && strpos($ipn->txn_id, 'S-') === FALSE && $ipn->txn_payment_type != 'echeck' && !$_CCFG['IPN_ALLOW_RESUBMIT']) {
			$ipn->set_throw_dup_mismatch(1);
		}

	# Process transaction
		$ipn->process_ipn();


# Redirect to orders return page if NOT the vendor or test mode
	} ELSE {
		$url = BASE_HREF.'mod.php?mod=orders&mode=return';
		header("Location: $url");
	}
?>