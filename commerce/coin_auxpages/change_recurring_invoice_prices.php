<?php
/**
 * Auxpage: Invoice Item Price Change
 * 	- This auxpage will loop through all not-yet-recurred invoices
 *	  and increase or decrease the price of each invoice item by X%, then reset
 *     the totals in the invoices table.  You MUST be logged-in as an admin when
 *     calling this file, otherwise NO price changes will occur.
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Invoices
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @usage Call this file directly, with the variable named "change" having the
 *	decimal percentage value that you want to change each item price by.
 *	Increase prices by 10%: change_recurring_invoice_prices.php?change=0.10
 *	Increase prices by 100: change_recurring_invoice_prices.php?change=1
 *   Decrease prices by 15%: change_recurring_invoice_prices.php?change=-0.15
 */


# Code to handle file being loaded by URL
	IF (!eregi('auxpage.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=index.php');
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

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['invoices'].' WHERE ';
		$query	.= "invc_recurring='1' AND invc_recurr_proc='0' ";
		$query	.= 'ORDER BY invc_id';

	# Do select and Process query results
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Calc new dates
				switch($row['invc_bill_cycle']) {
					case 0:
						$row['invc_ts']		= $row['invc_ts'] + (3600*24*$_CCFG['INVC_BILL_CYCLE_VAL'][0]);
						$row['invc_ts_due']		= $row['invc_ts_due'] + (3600*24*$_CCFG['INVC_BILL_CYCLE_VAL'][0]);
						break;
					default:
						$_dt_invc_dt			= dt_make_datetime_array($row['invc_ts']);
						$_dt_invc_dt['month'] 	= $_dt_invc_dt['month'] + $_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']];
						$row['invc_ts'] 		= dt_make_uts($_dt_invc_dt);
						$_dt_invc_due			= dt_make_datetime_array($row['invc_ts_due']);
						$_dt_invc_due['month'] 	= $_dt_invc_due['month'] + $_CCFG['INVC_BILL_CYCLE_VAL'][$row['invc_bill_cycle']];
						$row['invc_ts_due']		= dt_make_uts($_dt_invc_due);
						break;
				}

			# Use default terms, or copy existing terms
				IF ($_ACFG['INVC_ACOPY_NEW_TERMS']) {
					$row['invc_terms'] = $_CCFG['INV_TERMS_DEF_LINE_01'].$_CCFG['INV_TERMS_DEF_LINE_02'].$_CCFG['INV_TERMS_DEF_LINE_03'];
				}

			#Get max / create new invc_id and set defaults
				$_max_invc_id			= do_get_max_invc_id();
				$row['invc_status']		= $_CCFG['INV_STATUS'][4];
				$row['invc_ts_paid']	= '';
				$row['invc_total_paid']	= 0;
				$row['invc_delivered']	= 0;
				$row['invc_recurr_proc']	= 0;

			# Insert copied invoice data
				$query_ni  = 'INSERT INTO '.$_DBCFG['invoices'].' (';
				$query_ni .= 'invc_id, invc_status, invc_cl_id, invc_deliv_method, invc_delivered';
				$query_ni .= ', invc_total_cost, invc_total_paid, invc_subtotal_cost';
				$query_ni .= ', invc_tax_01_percent, invc_tax_01_amount, invc_tax_02_percent, invc_tax_02_amount';
				$query_ni .= ', invc_tax_autocalc, invc_ts, invc_ts_due, invc_ts_paid, invc_bill_cycle';
				$query_ni .= ', invc_recurring, invc_recurr_proc, invc_pay_link, invc_terms';
				$query_ni .= ") VALUES ($_max_invc_id+1".', ';
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_status'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_cl_id'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_deliv_method'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_delivered'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_total_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_total_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_subtotal_cost'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_01_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_01_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_02_percent'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_02_amount'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_tax_autocalc'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts_due'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_ts_paid'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_bill_cycle'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_recurring'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_recurr_proc'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_pay_link'])."', ";
				$query_ni .= "'".$db_coin->db_sanitize_data($row['invc_terms'])."'";
				$query_ni .= ')';

				$result_ni 	= $db_coin->db_query_execute($query_ni) OR DIE("Unable to complete request");
				$eff_rows_ni	= $db_coin->db_query_affected_rows();
				$_ins_invc_id	= $_max_invc_id+1;


			# Check for inserted $_GPV[invc_id]
				IF ($_ins_invc_id && $_ins_invc_id != 0 && $eff_rows_ni) {

				# Set Query for select and execute
					$query_pii 	 = 'SELECT * FROM '.$_DBCFG['invoices_items'];
					$query_pii 	.= ' WHERE ii_invc_id='.$adata['invc_id'];
					$query_pii	.= ' ORDER BY ii_item_no ASC';

				# Do select
					$result_pii	= $db_coin->db_query_execute($query_pii);
					$numrows_pii	= $db_coin->db_query_numrows($result_pii);

				# Process query results (assumes one returned row above- need to verify)
					while ($row = $db_coin->db_fetch_array($result_pii)) {

					# Change item price
						$row['ii_item_cost'] += ($row['ii_item_cost'] * $_GPV['change']);

					# Build SQL and execute.
						$query_nii	= 'INSERT INTO '.$_DBCFG['invoices_items'].' (';
						$query_nii	.= 'ii_invc_id, ii_item_no, ii_item_name';
						$query_nii	.= ', ii_item_desc, ii_item_cost';
						$query_nii	.= ', ii_apply_tax_01, ii_apply_tax_02, ii_calc_tax_02_pb';
						$query_nii	.= ') VALUES (';
						$query_nii	.= "'".$db_coin->db_sanitize_data($_ins_invc_id)."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_no'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_name'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_desc'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_item_cost'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_apply_tax_01'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_apply_tax_02'])."', ";
						$query_nii	.= "'".$db_coin->db_sanitize_data($row['ii_calc_tax_02_pb'])."'";
						$query_nii	.= ')';

						$result_nii	= $db_coin->db_query_execute($query_nii) OR DIE("Unable to complete request");
						$eff_rows_ni	= $db_coin->db_query_affected_rows();
					}
				}

			# Update invoice total cost for new invoice
				IF ($_ins_invc_id != 0) {$_ret = do_set_invc_values($_ins_invc_id, 0);}

			# Check for inserted $_GPV[invc_id] - Insert Invoice Debit Transaction
				IF ($_ins_invc_id && $_ins_invc_id != 0 && $eff_rows_ni) {

				# Get Invoice Total for insert to amount paid.
					$idata = do_get_invc_values($_ins_invc_id);

				# Insert Invoice Debit Transaction
					$_it_def = 0;
					$_it_desc	= $_LANG['_INVCS']['l_Invoice_ID'].$_sp.$_ins_invc_id;
					$q_it = 'INSERT INTO '.$_DBCFG['invoices_trans'].' (';
					$q_it .= 'it_ts, it_invc_id, it_type';
					$q_it .= ', it_origin, it_desc, it_amount';
					$q_it .= ') VALUES ( ';
					$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_ts'])."', ";
					$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_id'])."', ";
					$q_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
					$q_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
					$q_it .= "'".$db_coin->db_sanitize_data($_it_desc)."', ";
					$q_it .= "'".$db_coin->db_sanitize_data($idata['invc_total_cost'])."'";
					$q_it .= ')';
					$r_it = $db_coin->db_query_execute($q_it);
					$n_it = $db_coin->db_query_numrows($r_it);

				#########################################################################################################
				# API Output Hook:
				# APIO_trans_new: Trasaction Created hook
					$_isfunc = 'APIO_trans_new';
					IF ($_CCFG['APIO_MASTER_ENABLE'] == 1 && $_CCFG['APIO_TRANS_NEW_ENABLE'] == 1) {
						IF (function_exists($_isfunc)) {
							$_APIO = $_isfunc($idata); $_APIO_ret .= '<br>'.$_APIO['msg'].'<br>';
						} ELSE {
							$_APIO_ret .= '<br>'.'Error- no function'.'<br>';
						}
					}
				#########################################################################################################
				}

			# Call function to auto-set recurring was processed (copied) on old invoice
				IF ($_ins_invc_id != 0) {
					$_recurr_proc = 1;
					$_ret = do_set_invc_recurr_proc($row['invc_id'], $_recurr_proc);
				}
			}
		}
	}

?>