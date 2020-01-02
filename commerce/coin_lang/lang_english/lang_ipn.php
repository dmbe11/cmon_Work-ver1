<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- ipn is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage IPN
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */

# Code to handle file being loaded by URL
	IF (eregi('lang_ipn.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit();
	}

/**************************************************************
* Language Variables
**************************************************************/
# Language Variables: IPN
$_LANG['_IPN']['IPN_PENDING_invoice']			= 'No invoice match found.';
$_LANG['_IPN']['IPN_PENDING_address']			= 'Customer did not include a confirmed shipping address.';
$_LANG['_IPN']['IPN_PENDING_echeck']			= 'Payment was made by eCheck, which has not yet cleared.';
$_LANG['_IPN']['IPN_PENDING_intl']				= 'You hold a non-U.S. account and do not have a withdrawal mechanism.';
$_LANG['_IPN']['IPN_PENDING_upgrade']			= 'You must upgrade your account to Business or Premier to recieve funds.';
$_LANG['_IPN']['IPN_PENDING_verify']			= 'You are not yet verified.  You must verify your account first.';
$_LANG['_IPN']['IPN_PENDING_other']			= 'Payment is pending for an unknown reason.  Please contact PayPal Support.';
$_LANG['_IPN']['IPN_LOG_TITLE']				= 'IPN Log';
$_LANG['_IPN']['IPN_LOG_TS']					= 'Date/Time';
$_LANG['_IPN']['IPN_LOG_TXN']					= 'Transaction Number';
$_LANG['_IPN']['IPN_LOG_ACTION']				= 'Actions';
$_LANG['_IPN']['IPN_EMAIL_MISMATCH']			= 'Receiver email does not match.';
$_LANG['_IPN']['IPN_TXN_DUPLICATE']			= 'Transaction Number has already been processed once.';
$_LANG['_IPN']['IPN_WRONG_CUR']				= 'Invalid currency used.';
$_LANG['_IPN']['IPN_PENDING_ERR']				= 'Pending: Error';
$_LANG['_IPN']['Vendor']						= 'Vendor';


$_LANG['_IPN']['IPN_TXN_REFUNDED']				= 'Transaction refunded in the amount of <refund_amount>.';

$_LANG['_IPN']['IPN_DIRECT']					= 'Direct';
$_LANG['_IPN']['IPN_TXN_PENDING']				= 'Transaction Pending Reason: ';
$_LANG['_IPN']['IPN_TXN_DESC']				= 'Payment: Thank You';
$_LANG['_IPN']['IPN_CREDIT_APPLIED']			= 'Credit applied to invoice <invc_number> for the amount of <payment_amount>';
$_LANG['_IPN']['IPN_STATUS_CHANGE']			= 'Transaction caused invoice <invc_number>s status to change from <old_status> to <new_status>';
$_LANG['_IPN']['IPN_ACK_EML_SNT']				= 'Transaction acknowledgement email sent';
$_LANG['_THEME']['ALT_IMG_MU_IPN']				= 'IPN Mod';

$_LANG['_IPN']['Show']						= 'Show...';
$_LANG['_IPN']['Show_Only_Direct']				= 'Only Direct';
$_LANG['_IPN']['Show_All']					= 'All';
$_LANG['_IPN']['ShowSub']					= 'Subscriptions';

$_LANG['_IPN']['l_Pages']					= 'Page(s):';
$_LANG['_IPN']['b_submit']					= 'Submit';
$_LANG['_IPN']['b_reset']					= 'Reset';

# Testing Vars
$_LANG['_IPN']['Enter_Test_Info']				= 'Enter Information:';
$_LANG['_IPN']['TEST']['vendor']				= 'paypal';	// Who do we want to test?
															// Should be filename in /ipn/vendors without .php part
$_LANG['_IPN']['TEST']['txn_type']				= 'subscr_payment';
$_LANG['_IPN']['TEST']['payment_date']			=  date("H:i:s M j, Y T");
$_LANG['_IPN']['TEST']['last_name']				= 'Smith';
$_LANG['_IPN']['TEST']['payment_gross']			= '19.95';
$_LANG['_IPN']['TEST']['mc_currency']			= 'USD';
$_LANG['_IPN']['TEST']['payment_type']			= 'instant';
$_LANG['_IPN']['TEST']['payer_status']			= 'verified';
$_LANG['_IPN']['TEST']['payer_email']			= 'john@doe.com';
$_LANG['_IPN']['TEST']['txn_id']				= 'tst'.mktime( date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
$_LANG['_IPN']['TEST']['first_name']			= 'John';
$_LANG['_IPN']['TEST']['invoice']				= '1001';
$_LANG['_IPN']['TEST']['receiver_email']		= $_CCFG['PAYPAL_RECEIVER_EMAIL'];
$_LANG['_IPN']['TEST']['payer_id']				= 'WA69BHXZS9KQW';
$_LANG['_IPN']['TEST']['payment_status']		= 'Completed';
$_LANG['_IPN']['TEST']['mc_gross']				= '19.95';
//$_LANG['_IPN']['TEST']['pending_reason']		= 'upgrade';

$_LANG['_IPN']['TYPE']['subscr_payment']		= 'Subscription Payment';
$_LANG['_IPN']['TYPE']['send_money']			= 'Payment';
$_LANG['_IPN']['TYPE']['subscr_eot']			= 'Subscription End Of Term';
$_LANG['_IPN']['TYPE']['subscr_cancel']			= 'Subscription Cancellation';
$_LANG['_IPN']['TYPE']['subscr_signup']			= 'Subscription Creation';
$_LANG['_IPN']['TYPE']['subscr_failed']			= 'Subscription Failed';
$_LANG['_IPN']['TYPE']['refund']				= 'Refund';


$_LANG['_IPN']['STAT'][1]					= 'Completed';
$_LANG['_IPN']['STAT'][2]					= 'Active';
$_LANG['_IPN']['STAT'][3]					= 'Cancelled';
$_LANG['_IPN']['STAT'][4]					= 'Failed';
$_LANG['_IPN']['STAT'][5]					= 'Error';
$_LANG['_IPN']['STAT'][6]					= 'Paypal Pending';
$_LANG['_IPN']['STAT'][7]					= 'Denied';
$_LANG['_IPN']['STAT'][8]					= 'Failed';
$_LANG['_IPN']['STAT'][9]					= 'Reversed';
$_LANG['_IPN']['STAT'][10]					= 'Refunded';

$_LANG['_IPN']['Status']						= 'Status';
$_LANG['_IPN']['Type']						= 'Type';
$_LANG['_IPN']['AmtSent']					= 'Amt Sent';
$_LANG['_IPN']['AmtApplied']					= 'Amt Applied';
$_LANG['_IPN']['Name']						= 'Name';
$_LANG['_IPN']['PageTotals']					= 'Page Totals:';

$_LANG['_IPN']['TESTCOMPLETE']				= 'Thank you for testing.  Your information has been submitted';
$_LANG['_IPN']['refunded']					= 'Refunded Transaction';

$_LANG['_IPN']['subscr_created']				= 'Subscription Created';
$_LANG['_IPN']['subscr_cancelled']				= 'Subscription Cancelled';
$_LANG['_IPN']['subscr_failed']				= 'Subscription Failed';
$_LANG['_IPN']['subscr_eot']					= 'End of Subscription Terms';
$_LANG['_IPN']['trans_deleted_title']			= 'Deletion Status';

$_LANG['_IPN']['Delete_ipn_Entry_Message']		= 'Are You Sure You Want to Delete Entry ID';
$_LANG['_IPN']['Delete_ipn_Entry_Message_Cont']	= 'and all associated transactions?';
$_LANG['_IPN']['Delete_ipn_Confirmation']		= 'Delete IPN entry?';
$_LANG['_IPN']['B_Delete_Entry']				= 'Delete Entry';
$_LANG['_IPN']['Delete_ipn_Entry_Results_02']	= 'Total number of transactions deleted:';
$_LANG['_IPN']['trans_deleted']				= 'Transaction Deleted';
$_LANG['_IPN']['resubmit_warning']				= '<b><font color=red>This will Resubmit this information like it was being submitted for the first time.  The old txn id will be overwritten</font></b>';

# Emails Import CronJob
	$_LANG['_IPN']['EMAIL_IMPORT_CONFIG']		= 'If the /coin_cron/%VENDOR%.php file is <i>not</i> called via wget or curl or a browser simulator, you <b>must</b> configure the URL to your website in /coin_cron/cron_config.php';
	$_LANG['_IPN']['EMAIL_IMPORT_CONNECTING']	= 'Connecting to mail-server';
	$_LANG['_IPN']['EMAIL_IMPORT_NO_CONNECT']	= 'Unable to connect to mail-server';
	$_LANG['_IPN']['EMAIL_IMPORT_TO_PROCESS']	= 'Messages to process';
	$_LANG['_IPN']['EMAIL_IMPORT_PROCESSING']	= 'Processing message';
	$_LANG['_IPN']['EMAIL_IMPORT_TO_DELETE']	= 'Marking for deletion';
	$_LANG['_IPN']['EMAIL_IMPORT_FROM']		= 'From';
	$_LANG['_IPN']['EMAIL_IMPORT_NOT_VENDOR']	= 'Not From %VENDOR%';
	$_LANG['_IPN']['EMAIL_IMPORT_CREATING']		= 'Applying payment';
	$_LANG['_IPN']['EMAIL_IMPORT_DELETING']		= 'Deleting Messages';
	$_LANG['_IPN']['EMAIL_IMPORT_DISCONNETING']	= 'Closing connection to mail-server';
	$_LANG['_IPN']['EMAIL_IMPORT_NUM_PROCESSED']	= 'Message(s) processed';
	$_LANG['_IPN']['EMAIL_IMPORT_NUM_PYTS']		= 'Payments(s) created or applied';
	$_LANG['_IPN']['EMAIL_IMPORT_DISABLED']		= 'Auto-Import of %VENDOR% payment messages is disabled';
?>