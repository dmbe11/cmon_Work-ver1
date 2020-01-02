<?php
/**
 * Loader: Input Parsing
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Security
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('security.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit();
	}


# Process "global" variables for hack attempts
	$_GPV					= array();

	IF (isset($_GET))	{$_GPV	= array_merge($_GPV, clean_input_array($_GET, 1));}
	IF (isset($_POST))	{$_GPV	= array_merge($_GPV, clean_input_array($_POST, 0));}

	$_COOKIE					= clean_input_array($_COOKIE, 0);
	$_SESSION					= clean_input_array($_SESSION, 0);



# Check each element of an input array for hacking attempts, and clean if necessary
function clean_input_array($_array, $isget) {
	# Initlaiize variables
		$res = array();

	# Process each element of input array
		foreach ($_array as $key => $var) {

		# If element is itself an array, recurse to this function
			IF (is_array($var)) {
				$res[$key] = clean_input_array($var, $isget);

		# Otherwise, start processing it
			} ELSE {

			# First, urldecode it
				while($var != rawurldecode($var)) {$var = rawurldecode($var);}

			# Then, change htmlentities into regular characters so we can deal with them
				IF (function_exists('html_entity_decode')) {
					while($var != html_entity_decode($var))	{$var = html_entity_decode($var);}
				} ELSE {
					while($var != unhtmlentities($var))	{$var = unhtmlentities($var);}
				}

			# Eliminate leading & trailing spaces, tabs, etc.
				while($var != trim($var)) {$var = trim($var);}

			# Remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
				$var = preg_replace('/([\xC0][\xBC][\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $var);

			# Eliminate CDATA
				$var	= strip_cdata($var);

//			# Eliminate potential XSS characters
//				IF (
//					$key != 'mc_msg'
//				) {
//				# If variable is not in allowed list above, strip the character
//					while($var != ereg_replace("/[\'\")(;|`,<>]/", '', $var)) {
//						$var = ereg_replace("/[\'\")(;|`,<>]/", '', $var);
//					}
//				}

			# Some variables are allowed to contain html
				IF (
					$key != 'cc_msg' &&
					$key != 'comments' &&
					$key != 'dload_contributor' &&
					$key != 'dload_desc' &&
					$key != 'entry' &&
					$key != 'faq_descrip' &&
					$key != 'faqqa_answer' &&
					$key != 'hd_tt_message' &&
					$key != 'hdi_tt_message' &&
					$key != 'invc_pay_link' &&
					$key != 'invc_terms' &&
					$key != 'mc_msg' &&
					$key != 'mt_text' &&
					$key != 'overdue_template' &&
					$key != 'pages_code' &&
					$key != 'parm_notes' &&
					$key != 'parm_value' &&
					$key != 'prod_desc' &&
					$key != 'si_code' &&
					$key != 'todo_text' &&
					$key != 'vprod_order_link'
				) {
				# If variable is not in allowed list above, strip the html
					while($var != strip_tags($var)) {$var = strip_tags($var);}
				}

			# Some variables are allowed to contain a linefeed
				IF (
					$key != 'cc_msg' &&
					$key != 'cl_notes' &&
					$key != 'comments' &&
					$key != 'dom_notes' &&
					$key != 'dload_desc' &&
					$key != 'entry' &&
					$key != 'faq_descrip' &&
					$key != 'faqqa_answer' &&
					$key != 'hd_tt_message' &&
					$key != 'hdi_tt_message' &&
					$key != 'invc_pay_link' &&
					$key != 'invc_terms' &&
					$key != 'mc_msg' &&
					$key != 'mt_text' &&
					$key != 'ord_comments' &&
					$key != 'overdue_template' &&
					$key != 'parm_notes' &&
					$key != 'pages_code' &&
					$key != 'prod_desc' &&
					$key != 'si_code' &&
					$key != 'todo_text' &&
					$key != 'vendor_notes' &&
					$key != 'vprod_order_link' &&
					$key != 'whois_notes'
				) {
				# If variable is not in allowed list above, remove
				# everything after the linefeed because it is probably
				# something we do not want
					$pieces = explode("\n", $var);	$var = $pieces[0];
					$pieces = explode("\r", $var);	$var = $pieces[0];
				}

			# If this variables is from a GET, eliminate anything after a quote mark
				IF ($isget) {
					$pieces = explode('"', $var);	$var = $pieces[0];
					$pieces = explode("'", $var);	$var = $pieces[0];
				}

			# Make sure that a variable named id or ending with _id is actually a number
				IF ((eregi('_id', $key) && ($key != 'cc_cl_id' && $key != 'txn_id' && $key != 'parent_txn_id')) || $key == 'id') {
					IF (!is_numeric($var)) {$var=0;}
				}

			# Make sure that some other variables are actually a number
				IF ($key == 'ord_accept_tos' || $key == 'ord_accept_aup' || $key == 'ord_prod_id' || $key == 'ord_vendor_id') {
					IF (!is_numeric($var)) {$var=0;}
				}


			# Rename variable if it contains a non-alphanumeric (probably hack attempt) character
				IF (ereg("[^A-Za-z0-9_-]", $key)) {$key = 'NULL';}

			# All done
				$res[$key] = $var;
			}
		}

	# Return the variable
		return $res;
}


function strip_cdata($string) {
	preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
	return str_replace($matches[0], $matches[1], $string);
}

?>