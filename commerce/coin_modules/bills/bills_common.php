<?php
/**
 * Module: Bills (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Bills
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_bills.php
 */


# Code to handle file being loaded by URL
	IF (eregi('bills_common.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=bills');
		exit;
	}


/**************************************************************
 * Module Admin Functions
**************************************************************/
function do_set_bill_trans_values($atdata) {
	# Dim some Vars
		global $_DBCFG, $db_coin;
		$c = '';

	# Update Bill Debit Transaction
		$q_it = 'UPDATE '.$_DBCFG['bills_trans'].' SET ';
		IF ($atdata['bt_ts'] != '')		{$q_it .= "bt_ts='".$db_coin->db_sanitize_data($atdata['bt_ts'])."'";				$c = ',';}
		IF ($atdata['bt_bill_id'] != '')	{$q_it .= $c."bt_bill_id='".$db_coin->db_sanitize_data($atdata['bt_bill_id'])."'";	$c = ',';}
		IF ($atdata['bt_type'] != '')		{$q_it .= $c."bt_type='".$db_coin->db_sanitize_data($atdata['bt_type'])."'";		$c = ',';}
		IF ($atdata['bt_origin'] != '')	{$q_it .= $c."bt_origin='".$db_coin->db_sanitize_data($atdata['bt_origin'])."'";	$c = ',';}
		IF ($atdata['bt_desc'] != '')		{$q_it .= $c."bt_desc='".$db_coin->db_sanitize_data($atdata['bt_desc'])."'";		$c = ',';}
		IF ($atdata['bt_amount'] != '')	{$q_it .= $c."bt_amount='".$db_coin->db_sanitize_data($atdata['bt_amount'])."'";	$c = ',';}
		IF ($atdata['bt_type'] == 0) {
			$q_it .= " WHERE bt_bill_id = $atdata[bt_bill_id] AND bt_type = 0";
		} ELSE {
			$q_it .= " WHERE bt_id = $atdata[bt_id]";
		}
		$r_it = $db_coin->db_query_execute($q_it) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

function do_get_bill_PTD($abill_id) {
	# Dim some Vars
		global $_DBCFG, $db_coin;

	# Set Query for select and execute
		$query 	 = 'SELECT sum(bt_amount) as PTD FROM '.$_DBCFG['bills_trans'];
		$query 	.= " WHERE (bt_bill_id='".$abill_id."' AND bt_type <> 0)";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);
		IF ($numrows)	{while(list($PTD) = $db_coin->db_fetch_row($result)) {$_PTD = $PTD;}}
		IF (!$_PTD)	{$_PTD = 0;}	// 2008-01-31: Deleted payments cause this to not be set, so we will force it

	# Check / Set Value for return
		return $_PTD;
}

function do_get_max_bill_id() {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;
		$max_id = $_CCFG['BASE_BILL_ID'];

	# Set Query and select for max field value.
		$query	= 'SELECT max(bill_id) FROM '.$_DBCFG['bills'];
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while(list($_max_bill_id) = $db_coin->db_fetch_row($result)) {$max_id = $_max_bill_id;}
		}

	# Return value
		return $max_id;
}

function do_get_max_bill_item_no($abill_id) {
	# Dim some Vars
		global $_DBCFG, $db_coin;
		$max_item_no = 0;

	# Set Query and select for max field value.
		$query	 = 'SELECT max(bi_item_no) FROM '.$_DBCFG['bills_items'];
		$query	.= ' WHERE '.$_DBCFG['bills_items'].'.bi_bill_id='.$abill_id;
		$result	= $db_coin->db_query_execute($query);

	# Get Max Value
		while(list($_max_no) = $db_coin->db_fetch_row($result)) {$max_item_no = $_max_no;}

	# Check / Set Value for return
		return $max_item_no;
}


function do_calc_bill_values($adata) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;

	# Check / Set Incoming Data
		IF (!$adata['bill_tax_01_percent'])	{$adata['bill_tax_01_percent'] = 0;}
		IF (!$adata['bill_tax_02_percent'])	{$adata['bill_tax_02_percent'] = 0;}

	# Here we allow phpCOIN to override the tax rates.
	# This could be because one of the taxes has different rates for different
	# jurisdictions, or because one of the rates has changed over time
		IF (file_exists(PKG_PATH_OVERRIDES.'bill_tax_override.php')) {
			require(PKG_PATH_OVERRIDES.'bill_tax_override.php');
		}

		$idata['bill_tax_autocalc']	= $adata['bill_tax_autocalc'];
		$idata['bill_tax_01_percent']	= $adata['bill_tax_01_percent'];
		$idata['bill_tax_01_amount']	= $adata['bill_tax_01_amount'];
		$idata['bill_tax_02_percent']	= $adata['bill_tax_02_percent'];
		$idata['bill_tax_02_amount']	= $adata['bill_tax_02_amount'];

	# Build query and select by bill id
		$query	 = 'SELECT * FROM '.$_DBCFG['bills_items'];
		$query	.= ' WHERE '.$_DBCFG['bills_items'].'.bi_bill_id='.$adata['bill_id'];
		$query	.= ' ORDER BY bi_item_no ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results and get values
		$_cost_subtotal_all	= 0;
		$_cost_subtotal_01	= 0;
		$_cost_subtotal_02	= 0;
		$_tax_subtotal_all	= 0;
		$_tax_subtotal_01	= 0;
		$_tax_subtotal_02	= 0;

		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# If prices include tax, then remove applicable taxes BEFORE we add taxes.
				IF ($_CCFG['PRICES_INCLUDE_TAXES']) {
					$done=0;

				# Start with tax2 because it's the last one applied
				# If Tax 2 is applied, remove it
					IF ($row['bi_apply_tax_02'] == 1) {
						IF ($row['bi_calc_tax_02_pb']) {

						# If Tax2 piggybacked, remove it
							$row['bi_item_cost'] = ($row['bi_item_cost'] / (1+($idata['bill_tax_02_percent'] / 100)));
						} ELSE {

						# If NOT piggybacked, remove both taxes together and then set "done" flag
							$row['bi_item_cost'] = ($row['bi_item_cost'] / (1+(($idata['bill_tax_02_percent'] + $idata['bill_tax_01_percent']) / 100)));
							$done++;
						}
					}

				# If Tax1 is applied and we are NOT done, remove Tax1
					IF (($row['bi_apply_tax_01'] == 1) && (!$done)) {
						$row['bi_item_cost'] = ($row['bi_item_cost'] / (1+($idata['bill_tax_01_percent'] / 100)));
					}
				}

			# Now calculate our taxes as normal
				$_cost_subtotal_all += $row['bi_item_cost'];
				IF ($row['bi_apply_tax_01'] == 1) {
					$_cost_subtotal_01	+= $row['bi_item_cost'];
					$_tax_subtotal_01	+= ($row['bi_item_cost'] * ($idata['bill_tax_01_percent'] / 100));
					$_tax_subtotal_all  += $_tax_subtotal_01;
				}

				IF ($row['bi_apply_tax_02'] == 1) {
					IF ($row['bi_calc_tax_02_pb'] != 1) {
						$_cost_subtotal_02	+= $row['bi_item_cost'];
						$_tax_subtotal_02	+= ($row['bi_item_cost'] * ($idata['bill_tax_02_percent'] / 100));
					} ELSE {
						$_tax_01			= ($row['bi_item_cost'] * ($idata['bill_tax_01_percent'] / 100));
						$_tax_02_amount	= $row['bi_item_cost'] + $_tax_01;
						$_cost_subtotal_02	+= $_tax_02_amount;
						$_tax_subtotal_02	+= ($_tax_02_amount * ($idata['bill_tax_02_percent'] / 100));
					}
					$_tax_subtotal_all += $_tax_subtotal_02;
				}
			}
		}

	# Calc tax amounts on total cost
		$_tax_subtotal_01_all = round(($_cost_subtotal_all * ($idata['bill_tax_01_percent'] / 100)), $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
		$_tax_subtotal_02_all = round(($_cost_subtotal_all * ($idata['bill_tax_02_percent'] / 100)), $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);

	# Check for tax enabled and set zero if not.
		IF ($_CCFG['BILL_TAX_01_ENABLE'] != 1) {
			$_tax_subtotal_01_all		= 0;
			$_tax_subtotal_01			= 0;
			$idata['bill_tax_01_amount']	= 0;
		}
		IF ($_CCFG['BILL_TAX_02_ENABLE'] != 1) {
			$_tax_subtotal_02_all		= 0;
			$_tax_subtotal_02			= 0;
			$idata['bill_tax_02_amount']	= 0;
		}

	# Set return values based on various config items
		IF ($idata['bill_tax_autocalc'] == 1) {
			IF ($_CCFG['BILL_TAX_BY_ITEM'] == 1) {
				$idata['bill_tax_01_amount'] 	= round($_tax_subtotal_01, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_tax_02_amount'] 	= round($_tax_subtotal_02, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_subtotal_cost'] 	= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_total_cost']	= $idata['bill_subtotal_cost'] + $idata['bill_tax_01_amount'] + $idata['bill_tax_02_amount'];
			} ELSE {
				$idata['bill_tax_01_amount'] 	= round($_tax_subtotal_01_all, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_tax_02_amount'] 	= round($_tax_subtotal_02_all, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_subtotal_cost'] 	= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$idata['bill_total_cost']	= $idata['bill_subtotal_cost'] + $idata['bill_tax_01_amount'] + $idata['bill_tax_02_amount'];
			}
		} ELSE {
			$idata['bill_tax_01_amount']		= round($adata['bill_tax_01_amount'], $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
			$idata['bill_tax_02_amount'] 		= round($adata['bill_tax_02_amount'], $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
			$idata['bill_subtotal_cost'] 		= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
			$idata['bill_total_cost']		= $idata['bill_subtotal_cost'] + $idata['bill_tax_01_amount'] + $idata['bill_tax_02_amount'];
		}

	# Check / Outgoing Data
		IF (!$idata['bill_total_cost'])		{$idata['bill_total_cost']		= 0;}
		IF (!$idata['bill_subtotal_cost'])		{$idata['bill_subtotal_cost']		= 0;}
		IF (!$idata['bill_tax_01_percent'])	{$idata['bill_tax_01_percent']	= 0;}
		IF (!$idata['bill_tax_01_amount'])		{$idata['bill_tax_01_amount']		= 0;}
		IF (!$idata['bill_tax_02_percent'])	{$idata['bill_tax_02_percent']	= 0;}
		IF (!$idata['bill_tax_02_amount'])		{$idata['bill_tax_02_amount']		= 0;}

	# Check / Set Value for return
		return $idata;
}

function do_set_bill_values($abill_id, $aadd) {
	# Dim some Vars
		global $_CCFG, $_LANG, $_DBCFG, $_sp, $db_coin;

	# Get bill values now (need tax percent for recalc
		$idata_now = do_get_bill_values($abill_id);

	# Get bill calc new values
		$idata_now['bill_id']	= $abill_id;
		$idata_new			= do_calc_bill_values($idata_now);

	# Do update
		$query 	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
		$query 	.= "bill_total_cost='".$idata_new['bill_total_cost']."', ";
		$query 	.= "bill_subtotal_cost='".$idata_new['bill_subtotal_cost']."', ";
		$query 	.= "bill_tax_01_amount='".$idata_new['bill_tax_01_amount']."', ";
		$query 	.= "bill_tax_02_amount='".$idata_new['bill_tax_02_amount']."' ";
		$query 	.= 'WHERE bill_id='.$abill_id;
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Update Bill Debit Transaction
		$q_it 	 = 'UPDATE '.$_DBCFG['bills_trans'].' SET ';
		$q_it 	.= "bt_amount='".$idata_new['bill_total_cost']."' ";
		$q_it 	.= 'WHERE bt_bill_id='.$abill_id.' AND bt_type=0';
		$r_it	 = $db_coin->db_query_execute($q_it) OR DIE("Unable to complete request");

	# If no bill transaction was updated, then create one
		IF ($aadd && $r_it == FALSE) {
			IF (!$idata_new['bill_ts']) {$idata_new['bill_ts'] = dt_get_uts();}
			$_it_def = 0;
			$_it_desc	= $_LANG['_BILLS']['l_Bill_ID'].$_sp.$abill_id;
			$q2_it  = 'INSERT INTO '.$_DBCFG['bills_trans'].' (';
			$q2_it .= 'bt_ts, bt_bill_id, bt_type';
			$q2_it .= ', bt_origin, bt_desc, bt_amount';
			$q2_it .= ') VALUES ( ';
			$q2_it .= "'".$db_coin->db_sanitize_data($idata_new['bill_ts'])."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($abill_id)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_desc)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($idata_new['bill_total_cost'])."'";
			$q2_it .= ")";
			$r2_it = $db_coin->db_query_execute($q2_it);
		}

	# Set return
		return $numrows;
}

function do_get_bill_values($abill_id) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;

	# Set Query for select and execute
		$query  = 'SELECT * FROM '.$_DBCFG['bills'];
		$query .= ' WHERE bill_id='.$abill_id;

	# Do select and process query results (assumes one returned row)
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$idata['bill_id']			= $row['bill_id'];
				$idata['bill_ts']			= $row['bill_ts'];
				$idata['bill_ts_due']		= $row['bill_ts_due'];
				$idata['bill_ts_paid']		= $row['bill_ts_paid'];
				$idata['bill_total_cost']	= $row['bill_total_cost'];
				$idata['bill_total_paid']	= $row['bill_total_paid'];
				$idata['bill_subtotal_cost']	= $row['bill_subtotal_cost'];
				$idata['bill_tax_01_percent']	= $row['bill_tax_01_percent'];
				$idata['bill_tax_01_amount']	= $row['bill_tax_01_amount'];
				$idata['bill_tax_02_percent']	= $row['bill_tax_02_percent'];
				$idata['bill_tax_02_amount']	= $row['bill_tax_02_amount'];
				$idata['bill_tax_autocalc']	= $row['bill_tax_autocalc'];
			}
		}

	# Check / Outgoing Data
		IF (!$idata['bill_total_cost'])		{$idata['bill_total_cost'] = 0;}
		IF (!$idata['bill_total_paid'])		{$idata['bill_total_paid'] = 0;}
		IF (!$idata['bill_subtotal_cost'])		{$idata['bill_subtotal_cost'] = 0;}
		IF (!$idata['bill_tax_01_percent'])	{$idata['bill_tax_01_percent'] = 0;}
		IF (!$idata['bill_tax_01_amount'])		{$idata['bill_tax_01_amount'] = 0;}
		IF (!$idata['bill_tax_02_percent'])	{$idata['bill_tax_02_percent'] = 0;}
		IF (!$idata['bill_tax_02_amount'])		{$idata['bill_tax_02_amount'] = 0;}
		IF ($idata['bill_tax_autocalc'] == '')	{$idata['bill_tax_autocalc'] = '1';}

	# Check / Set Value for return
		return $idata;
}

function do_set_bill_status($abill_id, $astatus) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;

	# Do update
		$query_is 	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
		$query_is 	.= "bill_status='".$db_coin->db_sanitize_data($astatus)."'";
		$query_is 	.= ' WHERE bill_id='.$abill_id;
		$result_is	 = $db_coin->db_query_execute($query_is) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

function do_set_bill_recurr_proc($abill_id, $avalue) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;

	# Do update
		$query_id 	 = 'UPDATE '.$_DBCFG['bills'].' SET ';
		$query_id		.= "bill_recurr_proc='".$db_coin->db_sanitize_data($avalue)."'";
		$query_id 	.= ' WHERE bill_id='.$abill_id;
		$result_id	= $db_coin->db_query_execute($query_id) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

function do_select_list_status_bill($aname, $avalue, $aall) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Add status "all statuses"
		If ($aall) {
			$_out .= '<option value="ALL"';
			IF ($avalue == 'ALL') {$_out .= ' selected';}
			$_out .= '>'.$_LANG['_BASE']['All'].'</option>'.$_nl;
		}

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['BILL_STATUS']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['BILL_STATUS'][$i]).'"';
			IF ($_CCFG['BILL_STATUS'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_STATUS'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}

function do_select_list_bill_cycle($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['BILL_CYCLE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_CYCLE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}
?>