<?php
/**
 * Module: IPN (Vendor: ChronoPay)
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

$_CCFG['CHRONOPAY_WRITEDEBUG']	= 1;
$_CCFG['CHRONOPAY_DEBUGFILE']		= 'c:/php/temp/chronopay.log';
$_CCFG['CHRONOPAY_SHARED_SECRET']	= 'jfjskdfyehfuegwf';
$_CCFG['CHRONOPAY_CLIENT_ID']		= '004378-0001';

# Initialize variables. Make sure these are ALL zero, or else!
	$_CONFIRMED				= 0;		// Not a valid payment
	$_PROCESS					= 0;		// Do NOT try to apply payment
	$fsocket					= 0;		// No valid socket
	$_CCFG['CHRONOPAY_DEBUGFILE']	= str_replace('%TIMESTAMP%', time(), $_CCFG['CHRONOPAY_DEBUGFILE']);

# Exit if we should NOT accept IPN data
	IF (!$_CCFG['IPN_ACCEPT_INCOMING']) {exit();}

# Get referer (if any) else get remote IP and convert to a name
	IF (isset($_SERVER['HTTP_REFERER'])) {
		$_ref = $_SERVER['HTTP_REFERER'];
	} ELSE {
		$_ref = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	}

# If incoming connection is from ourselves (testing):
#	- set "good data" flag
#	- Do NOT set "proceed" flag
	IF (strpos($_ref, BASE_HREF) !== FALSE) {
		$_CONFIRMED++;

# If incoming connection is from ChronoPay:
#	- Do NOT set "good data" flag
#	- Set "proceed" flag
	} ELSEIF (strpos($_ref, '.chronopay.com') !== FALSE) {
		$_PROCESS++;
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

# Include class file
	require_once(PKG_PATH_MDLS.'/ipn/ipn.class.php');

# create ipn object
	$ipn = new ipn;

# set variables
	$ipn->set_txn_payer_email($_GPV['email']);
	$ipn->set_txn_type($_GPV['transaction_type']);
	$ipn->set_txn_payment_type($_GPV['transaction_type']);
	$ipn->set_txn_timestamp(dt_get_uts());
	$ipn->set_txn_vendor('chronopay');
	$ipn->set_txn_line($ipn->do_format_gpv($_POST));
	$ipn->set_txn_id($_GPV['transactionn_id']);
	$ipn->set_txn_gross($_GPV['total']);
	$ipn->set_txn_currency($_GPV['currency']);
	$ipn->set_txn_invc_id($_GPV['cs1']);
	$ipn->set_txn_firstname($_GPV['cs2']);
	$ipn->set_txn_lastname($_GPV['cs3']);
	$ipn->set_txn_cl_id($ipn->do_get_client_id());
	$ipn->set_debug_on($_GPV['debug']);

	$ipn->set_txn_subscr_id('');
	$ipn->set_txn_pending_reason('');
	$ipn->set_txn_parent_id('');
	$ipn->set_txn_receiver_email($_GPV['customer_id']);
	$ipn->set_txn_status('');

# If connection is from paypal process incoming data according to paypal instructions
	IF ($_PROCESS) {

	# Create debug log and add call-back URL
		IF ($_CCFG['CHRONOPAY_WRITEDEBUG']) {
			$open_file = fopen($_CCFG['CHRONOPAY_DEBUGFILE'], "wb");
			fputs($open_file, 'Incoming Connection Time: '.date("Y-m-d @ hh:mm:ss", time())."\r\n");
			fputs($open_file, "Incoming Connection Source: $_ref (from IP lookup)\r\n");
		}

	# Write incoming POST data to debug log
		IF ($_CCFG['CHRONOPAY_WRITEDEBUG']) {
			fputs($open_file, 'Data Name/Value Pairs Received:'."\r\n");
			foreach ($_POST as $key => $var) {
				fputs($open_file, '   '.$key.': '.$var."\r\n");
			}
			fputs($open_file, "\r\n");
		}

	# Check if data is as expected
		IF (
			isset($_GPV['email']) && !empty($_GPV['email']) &&
			isset($_GPV['cs1']) && isset($_GPV['cs2']) && isset($_GPV['cs3']) &&
			isset($_GPV['total']) && !empty($_GPV['total']) &&
			isset($_GPV['transaction_type']) && !empty($_GPV['transaction_type']) &&
			$_GPV['sign'] == md5($_CCFG['CHRONOPAY_SHARED_SECRET'].$_GPV['customer_id'].$_GPV['transactionn_id'].$_GPV['transaction_type'].$_GPV['total'])
		) {
		# Set "confirmed" flag
			$_CONFIRMED++;

		# Write data status to logfile
			fputs($open_file, "Data Status: Valid\r\n\r\n");

		} ELSE {
		# Write data status to logfile
			fputs($open_file, "Data Status: In-Valid\r\n\r\n");
		}

	# Close debug log
		IF ($_CCFG['CHRONOPAY_WRITEDEBUG']) {fclose($open_file);}

	}

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from PayPal), process it
	IF ($_CONFIRMED) {

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

# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from ChronoPay), process it
	IF ($_CONFIRMED) {

	# Check to make sure recipient is correct
		IF (strpos($_CCFG['CHRONOPAY_CLIENT_ID'], $ipn->txn_receiver_email) === FALSE) {
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
		$url = BASE_HREF;
		header("Location: $url");
	}
?>