<?php
/**
 * CronJobs: PayPal Payment Emails Import
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- This file uses imap functions from: http://xeoman.com/code/php/xeoport
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage PayPal
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_ipn.php
 */

# Turn off pointless "warning" messages, and do NOT display errors on-screen
	ini_set('error_reporting','E_ALL & ~E_NOTICE');
	ini_set('display_errors', 1);

# Set cron filename
	$cronfile	= 'paypal.php';

# Chaneg directory
	$_pth	= str_replace("\\", '/', realpath($argv[0]));
	$_pth	= str_replace($cronfile, '', $_pth);
	$_pth	= str_replace('/coin_cron', '', $_pth);
	chdir($_pth);

# include the "where are we" code
	require_once('cron_config.php');
	$_cstr	= '';
	$_vendor	= 'PayPal';

# Include core file
	require_once($_PACKAGE['DIR'].'coin_includes/core.php');

# Silent running, or show progress, and message count(s) on-screen when done
	$ShowProgress = $_ACFG['PAYPAL_AUTO_VERBOSE'];

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_ipn.php');

# Warn about non-configured URL, if necessary
	IF ($_COINURL == 'http://my.phpcoin.com') {$_cstr .= str_replace('%VENDOR%', $_vendor, $_LANG['_IPN']['EMAIL_IMPORT_CONFIG']);}

# Include class file
	require_once(PKG_PATH_MDLS.'/ipn/ipn.class.php');


# FROM HERE TO 'END XEOPORT' IS FROM http://xeoman.com/code/php/xeoport
	$line_break = "\r\n";  // How we want line breaks to appear in database.  Use "\n" for *nix
	$type = array("text", "multipart", "message", "application", "audio", "image", "video", "other");
	$encoding = array("7bit", "8bit", "binary", "base64", "quoted-printable", "other");
	$xp_type = 'email';
	$parts_type = array();
	$parts_encoding = array();
	$parts_attachments = array();
	$parts_filename = array();
	$parts_filesize = array();
	$parts_size = array();
	$counter_found = 0;
	$counter_inserted = 0;
	$counter_sqlerrors = 0;
	$time_counter = 0;
	$counter_empty = 0;
	$counter_size = 0;
	$conf_magicquotes = get_magic_quotes_gpc();

	function decode_text($input, $encoding) {
		switch ($encoding) {
			case '7bit':
				return $input;
				break;
			case 'quoted-printable':
				$input = preg_replace("/=\r?\n/", '', $input);
				return quoted_printable_decode($input);
				break;
			case 'base64':
				return base64_decode($input);
				break;
			default:
				return $input;
		}
	}

	function decode_header($string) {
		if (ereg("=\?.{0,}\?[Bb]\?", $string)) {
			$arrHead = split("=\?.{0,}\?[Bb]\?", $string);
			while(list($key,$value) = each($arrHead)){
				if (ereg("\?=", $value)){
					$arrTemp = split("\?=", $value);
					$arrTemp[0] = base64_decode($arrTemp[0]);
					$arrHead[$key] = join("",$arrTemp);
				}
			}
			$string = join("", $arrHead);
		} elseif (ereg("=\?.{0,}\?[Qq]\?", $string)){
			$string = quoted_printable_decode($string);
			$string = ereg_replace("=\?.{0,}\?[Qq]\?", '', $string);
			$string = ereg_replace("\?=", '', $string);
		}
		if (substr_count($string, '@') < 1) {$string = str_replace('_', ' ', $string);}
		return $string;
	}

	function get_name ($body) {
		if (substr_count($body, '<') < 1 || substr_count($body, '>') < 1) {
			return $body;
		}
		$endposition = strpos($body, '<');
		return trim(substr($body, 0, $endposition));
	}

	function get_substring($body, $start, $end) {
		$startposition = strpos($body, $start, 0) + strlen($start);
		if (substr_count($body, $start) < 1 || substr_count($body, $end) < 1) {
			return $body;
		}
		$endposition = strpos($body, $end, 0);
		return substr($body, $startposition, $endposition - $startposition);
	}
# END XEOPORT



# Call import email functions (if enabled)
	IF ($_ACFG['PAYPAL_AUTO_IMPORT_ENABLE']) {

	# Dim some vars
		$Processed	= 0;
		$total		= 0;

	# Connect To Mail Server
		IF ($ShowProgress) {$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_CONNECTING'].'<br>';}
		$inbox = @imap_open('{' . $_ACFG['PAYPAL_AUTO_IMPORT_SERVER'] . $_ACFG['PAYPAL_AUTO_IMPORT_TYPE'] . '}INBOX', $_ACFG['PAYPAL_AUTO_IMPORT_USERID'], $_ACFG['PAYPAL_AUTO_IMPORT_PASSWORD']);

		IF ($inbox) {

		# Grab The Message Headers
			$total = imap_num_msg($inbox);
			IF ($ShowProgress) {$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_TO_PROCESS'].': '.$total.'<br />';}

		# Process The Messages
			FOR ($x=1; $x <= $total; $x++) {
				If ($ShowProgress) {$_cstr .= '<br>&nbsp;&nbsp;&nbsp;'.$_LANG['_IPN']['EMAIL_IMPORT_PROCESSING'].': '.$x.'<br>';}
				$header		= imap_header($inbox, $x);
				$structure	= imap_fetchstructure($inbox, $x);

			# If $_CCFG['DELETE_IMPORTED_MESSAGES'] is ALL then delete message on server
				IF ($_ACFG['PAYPAL_AUTO_IMPORT_DELETE_MESSAGES'] == 2) {
					IF ($ShowProgress) {$_cstr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_LANG['_IPN']['EMAIL_IMPORT_TO_DELETE'].'<br />';}
					$DeleteIt = imap_delete($inbox, $x);
				}

			# FROM HERE TO 'END XEOPORT' IS FROM http://xeoman.com/code/php/xeoport
			# Get constituent parts
				$xp_id			= $header->message_id;
				$xp_time_unix		= strtotime('+ '.$_CCFG['_PKG_DATE_SERVER_OFFSET'].' hours');
				$xp_time_iso		= date("H:i:s", $xp_time_unix);
				$xp_date_iso		= date("Y-m-d", $xp_time_unix);
				$xp_date_full		= $header->Date;
				$xp_subject_text	= decode_header($header->subject);
				$xp_from_full		= decode_header($header->fromaddress);
				$xp_from_address	= get_substring($xp_from_full, '<', '>');
				$xp_to_full		= decode_header($header->toaddress);
				$xp_header_raw		= imap_fetchheader($inbox, $x);
				$parts			= $structure->parts;
				$parts_count		= count($parts);

			# Process if message is from PayPal
				IF (strpos($xp_subject_text, $_ACFG['PAYPAL_AUTO_PARTIAL_SUBJECT']) === FALSE) {
					IF ($ShowProgress) {$_cstr .= '&nbsp;&nbsp;&nbsp;'.str_replace('%VENDOR%', $_vendor, $_LANG['_IPN']['EMAIL_IMPORT_NOT_VENDOR']).'<br />';}

				} ELSE {
					IF ($ShowProgress) {$_cstr .= '&nbsp;&nbsp;&nbsp;'.$_LANG['_IPN']['EMAIL_IMPORT_FROM'].': '.htmlspecialchars($xp_from_address).'<br />';}
				# Increment "processed" counter
					$Processed++;

				# Retrieving the message body doesn't seem to work sometimes,
				# so cantex stuck this next line in to fool it
					IF (!$parts_count) {$xp_body_text = imap_body($inbox,$x);}
					for ($temp_z=0; $temp_z <= $parts_count; $temp_z++) {
						$temp_p = NULL;
						$temp_b = NULL;
						$parts_type_main = NULL;
						$parts_subtype_main = NULL;
						if ($parts[$temp_z]->type == "") {$parts[$temp_z]->type = 0;}
						$temp_y = $temp_z + 1;
						$parts_number = '_' . $temp_y;
						$parts_type_main = strtolower($type[$parts[$temp_z]->type]);
						$parts_type[$parts_number] = $parts_type_main . '/' . strtolower($parts[$temp_z]->subtype);
						$parts_encoding[$parts_number] = $encoding[$parts[$temp_z]->encoding];
						$parts_size[$parts_number] = $parts[$temp_z]->bytes;
						if (strtolower($parts[$temp_z]->disposition) == 'attachment') {
							$temp_b = $parts[$temp_z]->dparameters;
							if (is_array($temp_b) || is_object($temp_b)) {
								reset($temp_b);
								while (list(, $temp_p) = each ($temp_b)) {
									if ($temp_p->attribute == 'FILENAME') {
										$xp_attachments .= decode_header($temp_p->value) . ' [' . ceil($parts[$temp_z]->bytes / 1024) . ' KB]' . $line_break;
										$parts_filename[$parts_number] = decode_header($temp_p->value);
										$parts_filesize[$parts_number] = $parts[$temp_z]->bytes;
									}
								}
							}
						}
						if ($parts_type_main == 'multipart') {
							$parts_sub = $parts[$temp_z]->parts;
							$parts_sub_count = count($parts_sub);
							for ($temp_s = 0; $temp_s < $parts_sub_count; $temp_s++) {
								$temp_t = $temp_s + 1;
								$parts_sub_number = $parts_number . '.' . $temp_t;
								$parts_subtype_main = strtolower($type[$parts_sub[$temp_s]->type]);
								$parts_type[$parts_sub_number] = $parts_subtype_main . '/' . strtolower($parts_sub[$temp_s]->subtype);
								$parts_encoding[$parts_sub_number] = strtolower($encoding[$parts_sub[$temp_s]->encoding]);
								$parts_size[$parts_sub_number] = $parts_sub[$temp_s]->bytes;
								if ($parts_subtype_main == 'multipart') {
									$parts_subsub = $parts_sub[$temp_s]->parts;
									$parts_subsub_count = count($parts_subsub);
									for ($temp_m = 0; $temp_m < $parts_subsub_count; $temp_m++) {
										$temp_n = $temp_m + 1;
										$parts_subsub_number = $parts_sub_number . '.' . $temp_n;
										$parts_type[$parts_subsub_number] = strtolower($type[$parts_subsub[$temp_m]->type]) . '/' . strtolower($parts_subsub[$temp_m]->subtype);
										$parts_encoding[$parts_subsub_number] = strtolower($encoding[$parts_subsub[$temp_m]->encoding]);
										$parts_size[$parts_subsub_number] = $parts_subsub[$temp_m]->bytes;
									}
								}
							}
						}
					}

					IF (is_array($parts_type)) {
						while (list ($key, $val) = each ($parts_type)) {
							if (strlen($key) < 3) {
								$parts_structure .= '<strong>' . str_replace('_', '', $key) . '</strong>';
							} else {
								$parts_structure .= '&nbsp;&nbsp;&nbsp;<strong>' . str_replace('_', '', $key) . '</strong>';
							}
							$parts_structure .= ' _ ' . $val . ' <em>' . $parts_encoding[$key] . ' _ </em> [' . $parts_size[$key] . ']<br />';
							if ($val == 'text/plain' || $val == 'message/rfc822') {
								$xp_body_text = decode_text(imap_fetchbody($inbox, $x, str_replace("_", "", $key)), $parts_encoding[$key]);
							}
							if ($val == 'text/html') {$temp_html_key = $key;}
						}
					} ELSE {
						IF ($structure->encoding > 0) {
							$xp_body_text = decode_text(imap_body($inbox, $x), $encoding[$structure->encoding]);
							$parts_structure .= '<strong>0</strong> _ text/plain <em>' . $encoding[$structure->encoding] . '</em> _ [' . $structure->bytes . ']<br />';
						} ELSE {
							$xp_body_text = imap_body($inbox, $x);
							$parts_structure .= '<strong>0</strong> _ text/plain <em>7bit</em> _ [' . $structure->bytes . ']<br />';
						}
					}

					IF (($xp_body_text == '') && ($temp_html_key)) {
						$xp_body_text = strip_tags(decode_text(imap_fetchbody($inbox, $x, str_replace('_', '', $temp_html_key)), $parts_encoding[$temp_html_key]));
					}


					$xp_body_text	= preg_replace("/(\015\012)|(\015)|(\012)/", "$line_break", $xp_body_text);
					$xp_attachments = str_replace("$line_break$line_break", "$line_break", $xp_attachments);

				# calculating the message size
					if (is_array($parts_size)) {
						$xp_size = ceil(array_sum($parts_size) / 1024);
					} else {
						$xp_size = ceil($structure->bytes / 1024);
					}

					if ($conf_magicquotes == 0) {
						foreach($GLOBALS as $temp_k=>$temp_v) {
							if (substr_count($temp_k, "xp_") > 0) {
								$GLOBALS[$temp_k] = addslashes($temp_v);
							}
						}
					} # END XEOPORT


				# Create new IPN object
					$ipn = new ipn;

				# Set some variables
					$_CONFIRMED = 0;
					$ipn->set_txn_payer_email($xp_from_address);
					$ipn->set_txn_receiver_email($xp_to_full);
					$ipn->set_txn_timestamp($xp_time_unix);
					$ipn->set_txn_vendor('paypal');
					$ipn->set_txn_status('Completed');
					$ipn->set_debug_on(0);
					$ipn->set_txn_type('send_money');
					$ipn->set_txn_payment_type('email');

				# Replace encoding. On 2008-03-24 PayPal is using Windows-1252 "quoted-printable"
					$xp_body_text = quoted_printable_decode($xp_body_text);

				# Save the modified message
					$ipn->set_txn_line($xp_body_text);

				# Split entire body into "lines"
				# Determine payment vars/values
					$_xpb_parts = explode("\n", $xp_body_text);
					foreach($_xpb_parts as $key => $val) {
						IF (strpos($val, ':')) {
							$_p2 = explode(':', $val);
							$_p2[0] = trim($_p2[0]);
							$_p2[1] = trim($_p2[1]);

						# Set variables ~ make sure actual and expected are all lowercase so we do not miss a comparison
							IF (strtolower($_p2[0]) == strtolower($_ACFG['PAYPAL_AUTO_INVOICE_ID']))	{$ipn->set_txn_invc_id($_p2[1]);}
							IF (strtolower($_p2[0]) == strtolower($_ACFG['PAYPAL_AUTO_TRANS_ID']))	{$ipn->set_txn_id($_p2[1]);}
							IF (strtolower($_p2[0]) == strtolower($_ACFG['PAYPAL_AUTO_TOTAL_AMT']))	{
								$_p2[1] = str_replace($_CCFG['_CURRENCY_PREFIX'], '', $_p2[1]);		// Remove currency symbol prefix
								$_p3 = explode(' ', $_p2[1]); 								// Make line into two vars
								$ipn->set_txn_gross($_p3[0]);									// actual amount in numbers
								$ipn->set_txn_currency($_p3[1]);								// Currency Code
							}

							$ipn->set_txn_cl_id($ipn->do_get_client_id());
//	$ipn->set_txn_pending_reason($_GPV['pending_reason']);
//	$ipn->set_txn_parent_id($_GPV['parent_txn_id']);
//	$ipn->set_txn_subscr_id($_GPV['subscr_id']);

							$_CONFIRMED++;
						}
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

				# IF we have a confirmed payment (either test mode or resubmit by admin, or real data from PayPal), process it
					IF ($_CONFIRMED) {

					# Check to make sure recipient is correct
						IF (strpos($ipn->txn_receiver_email, $_CCFG['PAYPAL_RECEIVER_EMAIL']) === FALSE) {
							$ipn->set_throw_user_mismatch(1);
						}

					# Determine if TXN number has been used before
						IF ($ipn->do_get_txn_count() > 1 && strpos($ipn->txn_id, 'S-') === FALSE && $ipn->txn_payment_type != 'echeck' && !$_CCFG['IPN_ALLOW_RESUBMIT']) {
							$ipn->set_throw_dup_mismatch(1);
						}

					# Process transaction
						$ipn->process_ipn();
						IF ($ShowProgress) {$_cstr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_LANG['_IPN']['EMAIL_IMPORT_CREATING'].'<br />';}

					# If $_CCFG['DELETE_IMPORTED_MESSAGES'] is IMPORTED then delete message on server
						IF ($_ACFG['PAYPAL_AUTO_IMPORT_DELETE_MESSAGES'] == 1) {
							IF ($ShowProgress) {$_cstr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_LANG['_IPN']['EMAIL_IMPORT_TO_DELETE'].'<br />';}
							$DeleteIt = imap_delete($inbox, $x);
						}

					# Destroy the IPN object
						unset($ipn);

					}
				}
			}

		# Perform delete of any messages flagged for deletion
			IF ($ShowProgress) {$_cstr .= '<br />'.$_LANG['_IPN']['EMAIL_IMPORT_DELETING'].'<br />';}
			$DeletedEmail = imap_expunge($inbox);

		} ELSE {
			$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_NO_CONNECT'];
		}

	# Close The Connection
		IF ($ShowProgress) {$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_DISCONNETING'];}
		IF (imap_ping($inbox)) {imap_close($inbox);}

	# Done, with Number of messages processed
		IF ($ShowProgress) {
			$_cstr .= $_out.'<br><br>';
			$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_NUM_PROCESSED'].': '.$total.'<br>';
			$_cstr .= $_LANG['_IPN']['EMAIL_IMPORT_NUM_PYTS'].': '.$Processed;
		}
	} ELSE {
		$_cstr = str_replace('%VENDOR%', $_vendor, $_LANG['_IPN']['EMAIL_IMPORT_DISABLED']);
	}

# Display results
	echo $_cstr;
?>