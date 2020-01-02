<?php
/**
 * Module: IPN (class)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- ipn is based on concept and code of Jeremi Bergman (http://www.mividdesigns.com)
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Payments
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_ipn.php
 */


# Code to handle file being loaded by URL
	IF (eregi('ipn.class.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=ipn');
		exit();
	}

# Include invoice functions file
	require_once(PKG_PATH_MDLS.'invoices/invoices_funcs.php');

class ipn {
	var $txn_vendor;
	var $txn_timestamp;
	var $txn_line;
	var $txn_id;
	var $txn_type;
	var $txn_payment_type;
	var $txn_cl_id;
	var $txn_gross;
	var $txn_status;
	var $txn_lastname;
	var $txn_firstname;
	var $txn_pending_reason;
	var $txn_parent_id;
	var $txn_invc_id;
	var $txn_subscr_id;
	var $txn_payer_email;
	var $txn_receiver_email;
	var $txn_currency;

	var $debug_on = 0;
	var $throw_user_mismatch;
	var $throw_dup_mismatch;
	var $throw_cur_mismatch;
	var $payment_completed;
	var $payment_pending;
	var $payment_refunded;

	function set_debug_on($debug_on){
		$this->debug_on = $debug_on;
	}
	function set_txn_currency($txn_currency){
		$this->txn_currency = $txn_currency;
	}
	function set_txn_receiver_email($txn_receiver_email){
		$this->txn_receiver_email = $txn_receiver_email;
	}
	function set_txn_payer_email($txn_payer_email){
		$this->txn_payer_email = $txn_payer_email;
	}
	function set_txn_vendor($txn_vendor){
		$this->txn_vendor = $txn_vendor;
	}
	function set_txn_timestamp($txn_timestamp){
		$this->txn_timestamp = $txn_timestamp;
	}
	function set_txn_line($txn_line){
		$this->txn_line = $txn_line;
	}
	function set_txn_id($txn_id){
		$this->txn_id = $txn_id;
	}
	function set_txn_type($txn_type){
		$this->txn_type = $txn_type;
	}
	function set_txn_payment_type($txn_payment_type){
		$this->txn_payment_type = $txn_payment_type;
	}
	function set_txn_cl_id($txn_cl_id){
		$this->txn_cl_id = $txn_cl_id;
	}
	function set_txn_gross($txn_gross){
		$this->txn_gross = $txn_gross;
	}
	function set_txn_status($txn_status){
		$this->txn_status = $txn_status;
	}
	function set_txn_lastname($txn_lastname){
		$this->txn_lastname = $txn_lastname;
	}
	function set_txn_firstname($txn_firstname){
		$this->txn_firstname = $txn_firstname;
	}
	function set_txn_pending_reason($txn_pending_reason){
		$this->txn_pending_reason = $txn_pending_reason;
	}
	function set_txn_parent_id($txn_parent_id){
		$this->txn_parent_id = $txn_parent_id;
	}
	function set_throw_user_mismatch($throw_user_mismatch){
		$this->throw_user_mismatch = $throw_user_mismatch;
	}
	function set_throw_dup_mismatch($throw_dup_mismatch){
		$this->throw_dup_mismatch = $throw_dup_mismatch;
	}
	function set_throw_cur_mismatch($throw_cur_mismatch){
		$this->throw_cur_mismatch = $throw_cur_mismatch;
	}
	function set_txn_invc_id($txn_invc_id){
		$this->txn_invc_id = $txn_invc_id;
	}
	function set_txn_subscr_id($txn_subscr_id){
		$this->txn_subscr_id = $txn_subscr_id;
	}

	function process_ipn(){
		global $_CCFG, $_LANG, $_sp;
		# check to make sure we want to processes ipn
		IF ($_CCFG['IPN_PROCESS_INCOMING'] == 0) {return 0;}

		# Valid Reciever Mismatch
		if ($this->throw_user_mismatch) {
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_EMAIL_MISMATCH']);
			$this->do_update_ipn_status($this->txn_id, 5);
			$this->debug_message('receiver email error', $this->txn_receiver_email);
			return 0;
		}

		# Duplicate Transaction Mismatch
		if ($this->throw_dup_mismatch){
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_TXN_DUPLICATE']);
			$this->do_update_ipn_status($this->txn_id, 5);
			$this->debug_message('txn duplicate', $_LANG['_IPN']['IPN_TXN_DUPLICATE']);
			return 0;
		}

		# Invalid Currency Mismatch
		if ($this->throw_cur_mismatch){
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_WRONG_CUR']);
			$this->do_update_ipn_status($this->txn_id, 5);
			$this->debug_message('invalid currency', $$this->txn_currency);
			return 0;
		}

		# Handle Payments
		$this->debug_message('payment_status', $this->txn_status);
		IF ($this->txn_status == $this->payment_completed) {
			$it_ts = mktime(0,0,0,date("m"),date("d"),date("Y"));

		# find invoice
			IF ($this->txn_invc_id == '' || $this->txn_invc_id == 0) {
				$this->txn_invc_id = $this->find_latest_invoice($this->txn_cl_id, $_CCFG['IPN_INVOICE_FIND_METHOD'], $this->txn_gross);
			}
			IF ($this->txn_invc_id == 0) {
			# no invoice found, log error
				$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_TXN_PENDING'] . $_LANG['_IPN']['IPN_PENDING_invoice']);
				$this->debug_message('no invoice', $this->txn_id);
				$this->do_update_ipn_status($this->txn_id, 5);
				return 0;
			}

		# Get Status before applying payment
			$pre = get_mtp_invoice_info($this->txn_invc_id);
			$this->debug_message('pre', $pre,1);

		# Post Payment and log
			$it_id = $this->do_post_payment($this->txn_invc_id, $this->txn_gross, 1, $_LANG['_IPN']['IPN_TXN_DESC']." - ".$this->txn_id, $it_ts);
			$_LANG['_IPN']['IPN_CREDIT_APPLIED'] = str_replace('<payment_amount>',$this->txn_gross,$_LANG['_IPN']['IPN_CREDIT_APPLIED']);
			$_LANG['_IPN']['IPN_CREDIT_APPLIED'] = str_replace('<invc_number>','<a href=mod.php?mod=invoices&mode=view&invc_id='.$this->txn_invc_id.'>'.$this->txn_invc_id.'</a>',$_LANG['_IPN']['IPN_CREDIT_APPLIED']);
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_CREDIT_APPLIED']);
			$this->do_update_amt_applied($this->txn_id, $this->txn_gross);

		# Get Status after applying payment
			$post = get_mtp_invoice_info($this->txn_invc_id);
			$this->debug_message('post', $post, 1);

			IF ($pre['invc_status'] != $post['invc_status']) {
				$this->debug_message('invoice difference', $pre['invc_status'].' '.$post['invc_status']);
				$_LANG['_IPN']['IPN_STATUS_CHANGE'] = str_replace('<new_status>', $post['invc_status'], $_LANG['_IPN']['IPN_STATUS_CHANGE']);
				$_LANG['_IPN']['IPN_STATUS_CHANGE'] = str_replace('<invc_number>', '<a href=mod.php?mod=invoices&mode=view&invc_id='.$this->txn_invc_id.'>'.$this->txn_invc_id.'</a>', $_LANG['_IPN']['IPN_STATUS_CHANGE']);
				$_LANG['_IPN']['IPN_STATUS_CHANGE'] = str_replace('<old_status>', $pre['invc_status'], $_LANG['_IPN']['IPN_STATUS_CHANGE']);
				$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_STATUS_CHANGE']);
			}

		# Sent trans ack email
			IF ($_CCFG['IPN_SEND_TRANS_ACK']) {
				$this->do_send_email_trans_ack($this->txn_invc_id, 'email_trans_ack', $it_id);
				$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_ACK_EML_SNT']);
			}

		# update status
			$this->do_update_ipn_status($this->txn_id, 1);

		} ELSEIF ($this->txn_status == $this->payment_pending) {
			# Payment is pending
			$lang_error = 'IPN_PENDING_'.$this->txn_pending_reason;
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['IPN_TXN_PENDING'] . $_LANG['_IPN'][$lang_error]);
			$this->do_update_ipn_status($this->txn_id, 6);
			$this->debug_message('pending reason', $this->txn_pending_reason);

		} ELSEIF ($this->txn_status == $this->payment_refunded) {
			# You sent a refund
			$_LANG['_IPN']['IPN_TXN_REFUNDED'] = str_replace('<refund_amount>', $this->txn_gross, $_LANG['_IPN']['IPN_TXN_REFUNDED']);
			$this->log_ipn_detail($this->txn_parent_id,$_LANG['_IPN']['IPN_TXN_REFUNDED']);
			$line = '<a href=mod.php?mod=ipn&mode=detailed&ipn_txn='.$this->txn_parent_id.'>'.$this->txn_parent_id.'</a>';
			$this->log_ipn_detail($this->txn_id, $_LANG['_IPN']['refunded'].$_sp.$line);
			$this->do_update_ipn_status($this->txn_parent_id, 10);
			$this->do_update_ipn_status($this->txn_id, 1);
			$this->debug_message('refund','');

		} ELSEIF (!empty($this->txn_status)) {
			IF ($this->txn_status == $this->payment_denied)	{$stat = 7;}
			IF ($this->txn_status == $this->payment_failed)	{$stat = 8;}
			IF ($this->txn_status == $this->payment_reversed)	{$stat = 9;}
			$this->do_update_ipn_status($this->txn_id, $stat);
		}

		# Handle Subscriptions Here
		$this->debug_message('txn_type',$this->txn_type);
		IF ($this->txn_type == 'subscr_signup') {
			$this->do_update_ipn_status($this->txn_subscr_id, 2);
			$this->log_ipn_detail($this->txn_subscr_id, $_LANG['_IPN']['subscr_created']);
		} ELSEIF ($this->txn_type == 'subscr_cancel') {
			$this->do_update_ipn_status($this->txn_subscr_id, 3);
			$this->log_ipn_detail($this->txn_subscr_id, $_LANG['_IPN']['subscr_cancelled']);
		} ELSEIF ($this->txn_type == 'subscr_eot') {
			$this->do_update_ipn_status($this->txn_subscr_id, 3);
			$this->log_ipn_detail($this->txn_subscr_id, $_LANG['_IPN']['subscr_eot']);
		} ELSEIF ($this->txn_type == 'subscr_failed') {
			$this->do_update_ipn_status($this->txn_subscr_id, 8);
			$this->log_ipn_detail($this->txn_subscr_id, $_LANG['_IPN']['subscr_failed']);
		}

		$this->debug_message($_LANG['_IPN']['TESTCOMPLETE'], '');

		return 1;
	}

	function find_latest_invoice($cl_id, $applyto = 1, $invc_amount = 0) {
		global $_DBCFG, $db_coin, $_CCFG;
		/***************************************************************************************
		$applyto:
		0 - Highest invoice number, regardless of status
		1 - Highest invoice number that does not have status PAID
		2 - Highest invoice number that does not have status PAID, if none found, then return highest invoice nunmber
		3 - Lowest invoice number that does not have status PAID
		4 - Lowest invoice number that does not have status PAID, if none found, then return highest invoice nunmber
		$invc_amount	 - will check the invoice and make sure the amounts match.  If zero, then
		it is ignored.
		*****************************************************************************************/
		$this->debug_message('find_latest_invoice', 'find_latest_invoice('.$cl_id.','.$applyto.');');

		IF ($cl_id == 0) return 0; # no user found with email

		$sql = 'SELECT invc_id FROM '.$_DBCFG['invoices'] . ' WHERE invc_cl_id='.$cl_id.' ';
		IF ($applyto == 1 || $applyto == 2 || $applyto == 3 || $applyto == 4) {
			$sql .= "AND invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][3])."' ";
		}
		IF ($invc_amount != 0 && $_CCFG['IPN_REQUIRE_AMOUNT_MATCH']) {
			$sql .= 'AND invc_total_cost='.$invc_amount.' ';
		}
		$sql .= 'ORDER BY invc_id ';
		if ($applyto == 3 || $applyto == 4) {$sql .= 'ASC';} else {$sql .= 'DESC';}

		$result	= $db_coin->db_query_execute($sql);
		$numrows	= $db_coin->db_query_numrows($result);

		$this->debug_message('find_latest_invoice', $sql);

		IF (($applyto == 2 || $applyto == 4) && $numrows <= 0) {
			return $this->find_latest_invoice($cl_id, 0, $invc_amount);
		}

		$inv = $db_coin->db_fetch_array($result);

		$this->debug_message('invoice number ', $inv['invc_id']);
		return ($numrows > 0 ? $inv['invc_id'] : 0);

	}

	function do_update_ipn_status($ipn_txn, $status) {
		global $_DBCFG, $db_coin;
		# Update status of parent txn
		$query  = 'UPDATE '.$_DBCFG['ipn_log'].' SET ';
		$query .= "ipn_pay_stat='".$db_coin->db_sanitize_data($status)."'";
		$query .= ' WHERE ';
		$query .= "ipn_txn='".$db_coin->db_sanitize_data($ipn_txn)."'";
		$result = $db_coin->db_query_execute($query);
		$this->debug_message('do_update_ipn_status', $query);
	}

	function do_update_amt_applied($ipn_txn, $amt) {
		global $_DBCFG, $db_coin;
		# Update status of parent txn
		$query  = "UPDATE ".$_DBCFG['ipn_log']." SET ";
		$query .= "ipn_amt_applied = '$amt' WHERE ";
		$query .= "ipn_txn='".$db_coin->db_sanitize_data($ipn_txn)."'";
		$result = $db_coin->db_query_execute($query);
		$this->debug_message('do_update_amt_applied', $query);
	}

	function do_send_email_trans_ack($invc_id, $template, $trans_id) {
		# Needs to be set:
		# $adata['invc_id']		- invoice id
		# $adata['template']	- email template name
		# $adata['it_id']		- trans id
		$adata['invc_id']		= $invc_id;
		$adata['template']		= $template;
		$adata['it_id']		= $trans_id;
		$_temp = do_mail_invoice($adata, 1);
		IF (strpos($_temp, '/ipn/') !== FALSE) {
			$_temp = str_replace('coin_modules/ipn/vendors/'.$this->txn_vendor.'.php', 'mod.php', $_temp);
		}
		echo $_temp;
		$this->debug_message('do_send_email_trans_ack', $adata, 1);

	}

	function do_format_gpv($data) {
		foreach($data as $key => $value){
			$line .= "-:$key = $value:-\n"; // -: starts line  :- stops line
			$ipn[$key] = $value;
			$this->debug_message('', $key.'='.$value);
		}
		return $line;
	}

	function do_post_payment($invc_id, $it_amount, $it_origin=1, $it_desc='', $it_ts='') {
		# Dim some variables
			global $_CCFG, $_DBCFG, $db_coin, $_GPV, $_sp;

		# Date/Time Stamp
			IF (empty($it_ts)) {$it_ts = dt_get_uts();}

		# Create invoice "payment" transaction
			$query  = 'INSERT INTO '.$_DBCFG['invoices_trans'].' (';
			$query .= 'it_ts, it_invc_id, it_type';
			$query .= ', it_origin, it_desc, it_amount';
			$query .= ') VALUES (';
			$query .= "'".$db_coin->db_sanitize_data($it_ts)."', ";
			$query .= "'".$db_coin->db_sanitize_data($invc_id)."', ";
			$query .= '2, ';
			$query .= "'".$db_coin->db_sanitize_data($it_origin)."', ";
			$query .= "'".$db_coin->db_sanitize_data($it_desc)."', ";
			$query .= "'".$db_coin->db_sanitize_data($it_amount)."'";
			$query .= ')';
			$result = $db_coin->db_query_execute($query);
			$it_id = $db_coin->db_query_insertid();

		# Do status calc
			$ptd = do_get_invc_PTD($invc_id);

		# Get invoice amount
			$idata = do_get_invc_values($invc_id);

		# Check against PTD
			IF ($idata['invc_total_cost'] <= $ptd) {
				$_us = 1;
				$invc_status = $_CCFG['INV_STATUS'][3];
			}

		# Do update invoice record
			$query   = 'UPDATE '.$_DBCFG['invoices'].' SET ';
			$query  .= "invc_ts_paid='".$db_coin->db_sanitize_data($it_ts)."', ";
			$query  .= "invc_total_paid='".$db_coin->db_sanitize_data($ptd)."'";
			IF ($_us == 1) {$query .= ", invc_status='".$db_coin->db_sanitize_data($invc_status)."'";}
			$query  .= ' WHERE invc_id='.$invc_id;
			$result  = $db_coin->db_query_execute($query) OR DIE('Unable to complete request');

		#####################################################################################
		# API Output Hook:
		# APIO_trans_new: Trasaction Created hook
			$_isfunc = 'APIO_trans_new';
			if ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_TRANS_NEW_ENABLE'] == 1 ) {
				if (function_exists($_isfunc)) {
					$_APIO = $_isfunc($_GPV);
					$_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
				} else {
					$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
				}
			}
		#####################################################################################
			return $it_id;
	}

	function do_save_old_txn($txn_id) {
		global $_DBCFG, $db_coin;
		$query = 'UPDATE '.$_DBCFG['ipn_log'].' SET ';
		$query .= "ipn_txn='".$db_coin->db_sanitize_data($txn_id)."-old' ";
		$query .= "WHERE ipn_txn='".$db_coin->db_sanitize_data($txn_id)."'";
		$result = $db_coin->db_query_execute($query);
		$query = 'UPDATE '.$_DBCFG['ipn_text'].' SET ';
		$query .= "ipn_txn_id='".$db_coin->db_sanitize_data($txn_id)."-old' WHERE ";
		$query .= "ipn_txn_id='".$db_coin->db_sanitize_data($txn_id)."'";
		$result = $db_coin->db_query_execute($query);
	}

	function do_get_client_id() {
		global $_DBCFG, $db_coin, $_CCFG;
		# Build query string
		$client['cl_id'] = 0;
		$query  = 'SELECT DISTINCT cl_id from '.$_DBCFG['clients'].' ';
		$query .= 'LEFT JOIN '.$_DBCFG['clients_contacts'].' ';
		$query .= 'ON '.$_DBCFG['clients'].'.cl_id='.$_DBCFG['clients_contacts'].'.contacts_cl_id ';
		$query .= 'WHERE '.$_DBCFG['clients'].".cl_email='".$db_coin->db_sanitize_data($this->txn_payer_email)."'";
		$query .= 'OR '.$_DBCFG['clients_contacts'].".contacts_email='".$db_coin->db_sanitize_data($this->txn_payer_email)."'";

		$this->debug_message('find client by email',$query);
		# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		IF ($_CCFG['IPN_SEARCH_NAME'] && $numrows == 0) {
			$query  = 'SELECT cl_id FROM '.$_DBCFG['clients'];
			$query .= " WHERE (cl_name_last='".$db_coin->db_sanitize_data($this->txn_lastname)."' AND cl_name_first='".$db_coin->db_sanitize_data($this->txn_firstname)."')";
		# Do select and return check
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);
			$this->debug_message('find client by name',$query);
		}

		$client = $db_coin->db_fetch_array($result);
		$this->debug_message('find client by email result: ',$client['cl_id']);
		return $client['cl_id'];
	}

	function do_log_ipn() {
		global $_DBCFG, $db_coin;
		$query  = 'INSERT INTO '.$_DBCFG['ipn_log'].' (';
		$query .= "ipn_ts, ipn_var_details, ipn_txn, ipn_txn_type, ";
		$query .= "ipn_cl_id, ipn_pay_amt, ipn_pay_stat, ipn_name_last, ipn_vendor";
		$query .= ") VALUES ( ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_timestamp)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_line)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_id)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_type)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_cl_id)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_gross)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_status)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_lastname)."', ";
		$query .= "'".$db_coin->db_sanitize_data($this->txn_vendor)."'";
		$query .= ")";
		$result = $db_coin->db_query_execute($query);
		$this->debug_message('log_ipn'.$query);
	}

	function log_ipn_detail($ipn_txn, $line) {
		global $_DBCFG, $db_coin;
		$it_ts = mktime( date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		$query  = 'INSERT INTO '.$_DBCFG['ipn_text'].' (';
		$query .= "ipn_txn_id, ipn_text_ts, ipn_log_text";
		$query .= ") VALUES ( ";
		$query .= "'".$db_coin->db_sanitize_data($ipn_txn)."', ";
		$query .= "'".$db_coin->db_sanitize_data($it_ts)."', ";
		$query .= "'".$db_coin->db_sanitize_data($line)."')";
		$result = $db_coin->db_query_execute($query);
		$this->debug_message('log_ipn_detail', $query);
	}

	function do_get_txn_count() {
		global $_DBCFG, $db_coin;
		$query	= 'SELECT ipn_txn FROM '.$_DBCFG['ipn_log']." WHERE ipn_txn = '".$this->txn_id."'";
		$result	= $db_coin->db_query_execute($query);
		return $db_coin->db_query_numrows($result);
	}

	function debug_message($title='', $txt='', $print_r=0) {
		IF (!$this->debug_on)			{return 0;}
		IF (!empty($title))				{echo '<br><b>'.$title.':</b><br>';}
		IF (!$print_r && !empty($txt))	{echo '&nbsp;'.$txt.'<br>';}
		IF ($print_r && is_array($txt))	{print_r($txt);}
	}
}
?>