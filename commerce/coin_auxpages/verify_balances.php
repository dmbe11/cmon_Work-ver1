<?php
/**
 * Auxpage: Invoice Balances Verify
 * 	- This auxpage will loop through all TRANSACTIONS in the transactiosn table
 *	  to calculate the balance due for each INVOICE, then reset the balance in
 *	  the invoices table. Optionally, it can reset the status from PAID to DUE
 *	  on any adjusted balances.
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Invoices
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (!eregi('auxpage.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=auxpage.php?page=verify_balances');
		exit;
	}



##############################
# Content Start
# Notes:
#	- required includes
#	- db connected
#	- html is fine
#	- php requires tags set
##############################

# Get security vars
	$_SEC = get_security_flags();

# IF user is not logged-in, show the login form
	IF (!$_SEC['_sadmin_flg']) {
		do_login($data, 'admin', '0').$_nl;

# Otherwise, display the content
	} ELSE {

	# Initialize data array
		$_invoices = array();

	# Set Query for select.
		$query  = 'SELECT it_invc_id, it_type, it_amount';
		$query .= ' FROM '.$_DBCFG['invoices_trans'];
		$query .= ' ORDER BY it_invc_id ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Only create rows table if there is any data
		IF ($numrows) {
			while(list($invc_id, $it_type, $it_amount) = $db_coin->db_fetch_row($result)) {
				IF ($it_type == 0) {
					$_invoices[$invc_id] += $it_amount;
				} ELSE {
					$_invoices[$invc_id] -= $it_amount;
				}
			}
		}
	# Loop through results array
		$_todo = count($_invoices);
		FOR ($i=0; $i<$_todo; $i++) {
			IF (isset($_invoices[$i])) {
				echo 'Invoice: '.$i.'     Balance: '.$_invoices[$i].'<br>';

			# Do update
				$query 	 = 'UPDATE '.$_DBCFG['invoices'].' SET ';
				$query 	.= "invc_total_paid=(invc_total_cost-".$_invoices[$i].")";

			# UNCOMMENT if you want to reset invoice status to DUE if not paid in full
//				IF ($_invoices[$i] > '0.001') {$query .= ", invc_status='".$_CCFG['INV_STATUS'][0]."'";}

				$query 	.= 'WHERE invc_id='.$i;
				$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
				$numrows	= $db_coin->db_query_affected_rows();
			} ELSE {
				$_todo++;
			}
		}

	}
?>