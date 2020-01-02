<?php
/**
 * Module: IPN (Vendor: 2Checkout)
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
	$_CCFG['2CHECKOUT_DEBUGFILE']	= str_replace('%TIMESTAMP%', time(), $_CCFG['2CHECKOUT_DEBUGFILE']);

# Get referer (if any) else get remote IP and convert to a name
	IF (isset($_SERVER['HTTP_REFERER'])) {
		$_ref = $_SERVER['HTTP_REFERER'];
	} ELSE {
		$_ref = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	}

# Start debug file
	IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
		$open_file = fopen($_CCFG['2CHECKOUT_DEBUGFILE'], "wb");
		fputs($open_file, 'Incoming Connection From: '.$_SERVER['REMOTE_ADDR']."\r\n");
		fputs($open_file, '           Translates To: '.$_ref."\r\n");
	}

# Exit if we should NOT accept IPN data
	IF (!$_CCFG['IPN_ACCEPT_INCOMING']) {
		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
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

		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
			fputs($open_file, "  Connection Is From Self-Testing\r\n");
		}

# If incoming connection is from 2Checkout:
#	- Do NOT set "good data" flag
#	- Set "proceed" flag
	} ELSE {
		$_PROCESS++;

		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
			fputs($open_file, "  Connection ASSUMED To Be From 2Checkout\r\n");
		}
	}

# Include language file
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

# Include class file
	require_once(PKG_PATH_MDLS.'/ipn/ipn.class.php');

# create ipn object
	$ipn = new ipn;

# set variables
	$ipn->set_txn_vendor('2Checkout');
	$ipn->payment_completed	= 'approved';
	$ipn->payment_denied	= 'denied';
	$ipn->payment_failed	= 'failed';
	$ipn->payment_pending	= 'pending';
	$ipn->payment_refunded	= 'refunded';
	$ipn->payment_reversed	= 'reversed';

	$ipn->set_debug_on($_GPV['debug']);

	$ipn->set_txn_firstname($_GPV['customer_first_name']);
	$ipn->set_txn_lastname($_GPV['customer_last_name']);
	$ipn->set_txn_payer_email($_GPV['customer_email']);
	$ipn->set_txn_cl_id($ipn->do_get_client_id());
	$ipn->set_txn_timestamp(strtotime($_GPV['timestamp']));
	$ipn->set_txn_line($ipn->do_format_gpv($_POST));

	$ipn->set_txn_id($_GPV['sale_id']);
	$ipn->set_txn_parent_id('0');

	IF ($_GPV['message_type'] == 'RECURRING_STOPPED') {
		$ipn->set_txn_type('subscr_eot');
	} ELSEIF ($_GPV['message_type'] == 'RECURRING_INSTALLMENT_FAILED') {
		$ipn->set_txn_type('subscr_failed');
	} ELSEIF ($_GPV['message_type'] == 'RECURRING_STOPPED') {
		$ipn->set_txn_type('subscr_cancel');
	} ELSEIF ($_GPV['message_type'] == 'ORDER_CREATED' && $_GPV['recurr'] == 1) {
		$ipn->set_txn_type('subscr_signup');
	} ELSE {
		$ipn->set_txn_type($_GPV['message_type']);
	}
	IF ($_GPV['message_type'] == 'ORDER_CREATED') {
		IF ($_GPV['invoice_status'] == 'approved' && $_GPV['fraud_status'] != 'wait' && $_GPV['fraud_status'] != 'fail') {
			$ipn->set_txn_status($ipn->payment_completed);
		} ELSE {
			$ipn->set_txn_status($ipn->payment_pending);
		}
	} ELSEIF ($_GPV['message_type'] == 'FRAUD_STATUS_CHANGED') {
		IF ($_GPV['fraud_status'] == 'pass') {
			$ipn->set_txn_status($ipn->payment_completed);
		} ELSEIF ($_GPV['fraud_status'] == 'wait') {
			$ipn->set_txn_status($ipn->payment_pending);
		} ELSEIF ($_GPV['fraud_status'] == 'fail') {
			$ipn->set_txn_status($ipn->payment_failed);
		}
	} ELSEIF ($_GPV['message_type'] == 'REFUND_ISSUED') {
		$ipn->set_txn_status($ipn->payment_refunded);
	}

	$ipn->set_txn_payment_type($_GPV['payment_type']);
	$ipn->set_txn_gross($_GPV['invoice_list_amount']);
	$ipn->set_txn_currency($_GPV['list_currency']);

	$ipn->set_txn_invc_id(0);						// Let IPN determien invoice, since 2co does not tell us
	$ipn->set_txn_subscr_id($ipn->txn_cl_id);			// USe cl_id, since 2co does not tell us
	$ipn->set_txn_pending_reason($_GPV['fraud_status']);
	$ipn->set_txn_receiver_email($_GPV['vendor_id']);

# If connection is from 2Checkout process incoming data according to 2Checkout instructions
	IF ($_PROCESS) {

	# Write debug log
		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {fputs($open_file, "  Verifying Hash\r\n");}

	# Verify MD5 hash
		$md5hash = strtoupper(md5($_GPV['sale_id'].$_GPV['vendor_id'].$_GPV['invoice_id'].$_CCFG['2CHECKOUT_SECRET_WORD']));

	# Now look for verification code in the final result, and if found
	# set "good data" flag
		IF (strtoupper($_GPV['md5_hash']) == $md5hash) {$_CONFIRMED++;}

	# Close debug log
		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
			fputs($open_file, "\r\n");
			fclose($open_file);
		}

	}

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from 2Checkout), process it
	IF ($_CONFIRMED) {

	# Update old db information
		IF ($_GPV['resubmit']) {$ipn->do_save_old_txn($ipn->txn_id);}

	# Set credit strings
		IF ($ipn->txn_status == $ipn->payment_refunded || $ipn->txn_status == $ipn->payment_reversed) {
			$ipn->set_txn_type($_GPV['message_description']);
		}

	# Subcriptions do not have txn_id, but rather subscr_id
		IF (isset($ipn->txn_subscr_id) && !isset($ipn->txn_id)) {
			$ipn->set_txn_id($ipn->txn_subscr_id);
		}
	}

# Log transaction data, regardlesss of valid or not.
# This is so we can re-submit it later
	$ipn->do_log_ipn();

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from 2Checkout), process it
	IF ($_CONFIRMED) {

	# Check to make sure recipient is correct
		IF ($ipn->txn_receiver_email != $_CCFG['2CHECKOUT_VENDOR_ID']) {
			$ipn->set_throw_user_mismatch(1);
		}

	# Determine if TXN number has been used before
		IF ($ipn->do_get_txn_count() > 1 && strpos($ipn->txn_id, 'S-') === FALSE && !$_CCFG['IPN_ALLOW_RESUBMIT']) {
			$ipn->set_throw_dup_mismatch(1);
		}

	# Process transaction
		$ipn->process_ipn();

# Redirect to main page if NOT 2Checkout or test mode (prevent hackers from POSTing a payment)
	} ELSE {
		IF ($_CCFG['2CHECKOUT_WRITEDEBUG']) {
			fputs($open_file, "Invalid Connection Attempt - Exiting IPN\r\n\r\n");
			fclose($open_file);
		}

		$url = BASE_HREF;
		header("Location: $url");
	}
?>