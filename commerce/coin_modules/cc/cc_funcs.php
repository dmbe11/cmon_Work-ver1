<?php
/**
 * Module: Command Center (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Summary
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_cc.php
 */


# Code to handle file being loaded by URL
	IF (eregi('cc_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=cc');
		exit;
	}

/**************************************************************
 * Module Functions
**************************************************************/

/**
 * Determine join-date of oldest client from database
 * @return int unix timetamp
 */
function determine_oldest_client_date($join_ts) {
	# Grab some globals
		global $_DBCFG, $db_coin;

	# Set and do the query
		$query	= 'SELECT cl_join_ts FROM '.$_DBCFG['clients'].' ORDER BY cl_join_ts ASC LIMIT 1';
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while($row = $db_coin->db_fetch_array($result)) {
				IF ($row['cl_join_ts'] < $join_ts) {$join_ts = $row['cl_join_ts'];}
			}
		}

	# return results
		return $join_ts;
}

/**
 * Build start date and end date inputs for a report
 * @uses datetime_format_uts_to_string() to display time if print/pdf
 * @uses build_datetime_input() to build inputs of <i>not</i> print/pdf
 * @param int $start_date Desired Report Start Date in Unix timestamp format
 * @param int $end_date Desired Report End Date in Unix timestamp format
 * @return string Complete html for select dates form
 */
function build_input_report_dates($start_date, $end_date) {
	global $_CCFG, $_LANG, $_nl;

	IF (!$start_date)	{$start_date	= time()-1;}
	IF (!$end_date)	{$end_date	= time()+1;}

	$_cstr  = '<form method="post" action="mod.php" accept-charset="'.$_CCFG['ISO_CHARSET'].'">'.$_nl;
	$_cstr .= '<input type="hidden" id="mod" name="mod" value="cc">'.$_nl;
	$_cstr .= '<input type="hidden" id="stage" name="stage" value="1">'.$_nl;
	$_cstr .= '<fieldset>'.$_nl;
	$_cstr .= '<legend>'.$_LANG['_CC']['Summary_Dates'].'</legend>'.$_nl;
	$_cstr .= '<label for="report_start" acceskey="S"';
	$_cstr .= '>'.$_LANG['_CC']['Start_Date'].'</label>'.$_nl;
	IF ($_CCFG['_IS_PRINT']) {
		$_cstr .= datetime_format_uts_to_string($start_date, $_CCFG['SHORT_DATE_FORMAT']);
	} ELSE {
		$_cstr .= do_date_edit_list('report_start', $start_date, 1);
	}
	$_cstr .= '<br>';
	$_cstr .= '<label for="report_end" acceskey="E"';
	$_cstr .= '>'.$_LANG['_CC']['End_Date'].'</label>'.$_nl;
	IF ($_CCFG['_IS_PRINT']) {
		$_cstr .= datetime_format_uts_to_string($end_date, $_CCFG['SHORT_DATE_FORMAT']);
	} ELSE {
		$_cstr .= do_date_edit_list('report_end', $end_date, 1);
	}
	$_cstr .= '<br>';
	IF (!$_CCFG['_IS_PRINT']) {
		$_cstr .= '<input type="submit" name="b_submit" value="'.$_LANG['_CC']['B_Submit'].'" class="formbutton" onmouseover="this.className=\'formbuttonhover\'" onmouseout="this.className=\'formbutton\'">'.$_nl;
		$_cstr .= '<input type="submit" name="b_reset" value="'.$_LANG['_CC']['B_Reset'].'" class="formbutton" onmouseover="this.className=\'formbuttonhover\'" onmouseout="this.className=\'formbutton\'">'.$_nl;
		$_cstr .= '<br>';
	}
	$_cstr .= '</fieldset>'.$_nl;
	$_cstr .= '</form>'.$_nl;

	return $_cstr;
}

# Do search list select field for: Vendors
function do_search_select_list_vendors($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_DBCFG, $db_coin, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT vendor_id, vendor_name FROM '.$_DBCFG['vendors'].' ORDER BY vendor_id ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_sp.'</option>'.$_nl;

	# Process query results
		IF ($numrows) {
			while(list($vendor_id, $vendor_name) = $db_coin->db_fetch_row($result)) {
				$_out .= '<option value="'.$vendor_id.'"';
				IF ($vendor_id == $avalue) {$_out .= ' selected';}
				$_out .= '>'.$vendor_id.' - '.$vendor_name.'</option>'.$_nl;
			}
		}
		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Products
function do_search_select_list_prods($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_DBCFG, $db_coin, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT prod_id, prod_name, prod_desc FROM '.$_DBCFG['products'].' ORDER BY prod_id ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_sp.'</option>'.$_nl;

	# Process query results
		IF ($numrows) {
			while(list($prod_id, $prod_name, $prod_desc) = $db_coin->db_fetch_row($result)) {
				$_out .= '<option value="'.$prod_id.'"';
				IF ($prod_id == $avalue) {$_out .= ' selected';}
				$_out .= '>'.$prod_id.' - '.$prod_name.' - '.$prod_desc.'</option>'.$_nl;
			}
		}
		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list select field for: Trans Type
function do_search_select_list_trans_type($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="'.''.'"';
		IF ($avalue == '') {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_CC']['Please_Select'].'</option>'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['INV_TRANS_TYPE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_TRANS_TYPE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select field for: Trans Origin
function do_search_select_list_trans_origin($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="'.''.'"';
		IF ($avalue == '') {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_CC']['Please_Select'].'</option>'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['INV_TRANS_ORIGIN']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_TRANS_ORIGIN'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do summary: Clients
function do_summary_clients($adata) {
	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT cl_status, count(cl_id) as cl_count';
		$query .= ' FROM '.$_DBCFG['clients'];
		$query .= ' WHERE cl_join_ts >= '.$adata['report_start'].' AND cl_join_ts <= '.$adata['report_end'];
		$query .= ' GROUP BY cl_status';
		$query .= ' ORDER BY cl_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
		$_out .= '<b><a href="mod.php?mod=clients">'.$_LANG['_CC']['Clients'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$cl_count_ttl	= 0;
		IF ($numrows) {
			while(list($cl_status, $cl_count) = $db_coin->db_fetch_row($result)) {
				IF ($cl_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_client'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_clients'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp.$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">('.$cl_count.')</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top"><a href="mod.php?mod=clients&status='.urlencode($cl_status).'">'.$cl_status.'</a></td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'.</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$cl_count_ttl = $cl_count_ttl + $cl_count;
			}
		}

		$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$cl_count_ttl.$_sp.$_LANG['_CC']['lc_client_s'].'</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}


# Do summary: Domains  Expired / Expiring
function do_summary_domains_exp() {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Call timestamp function
		$_uts_now		= dt_get_uts();
		$_uts_setpoint	= $_uts_now+(86400*$_CCFG['CC_DOMAIN_EXP_IN_DAYS']);

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['domains'];
		$query	.= ' WHERE dom_ts_expiration <= '.$_uts_setpoint;
		IF ($_CCFG['CC_DOMAIN_EXP_LIST_INCL_EXPRD']) {
			$query .= ' AND dom_ts_expiration > 0';
			$_str_line_02 = $_LANG['_CC']['Expired'].$_sp.$_LANG['_CC']['or'].$_sp.$_LANG['_CC']['Expiring_In'];
		} ELSE {
			$query .= ' AND dom_ts_expiration > '.$_uts_now;
			$_str_line_02 = $_LANG['_CC']['Expiring_In'];
		}
		$query .= ' AND dom_status <> 0';

	# Set to logged in Client ID if not admin to avoid seeing other client domains
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_SEC['_suser_id'];}

		$query .= ' ORDER BY dom_ts_expiration ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Domains'].$_sp.'</b>'.$_nl;
		$_out .= '<br><b>'.$_str_line_02.$_sp.'('.$_CCFG['CC_DOMAIN_EXP_IN_DAYS'].')'.$_sp.$_LANG['_CC']['days'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL">'.$_sp.$_sp.'</td>'.$_nl;
				IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP06'] == 1)) {$_pmode = 'edit';} ELSE {$_pmode = 'view';}
				$_out .= '<td class="TP1SML_NL"><a href="mod.php?mod=domains&mode='.$_pmode.'&dom_id='.$row['dom_id'].'">'.$row['dom_domain'].'</a></td>'.$_nl;
				$_out .= '<td class="TP1SML_NL">'.dt_make_datetime($row['dom_ts_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
				$_days = (($row['dom_ts_expiration'] - $_uts_now) / 86400);
				$_out .= '<td class="TP1SML_NL">'.'('.number_format($_days, 2, '.', '').$_sp.$_LANG['_CC']['x_days'].')'.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}


# Do summary: Domain Server Accounts (SACCS) Expired / Expiring
function do_summary_saccs_exp() {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Call timestamp function
		$_uts_now		= dt_get_uts();
		$_uts_setpoint	= $_uts_now+(86400*$_CCFG['CC_SACC_EXP_IN_DAYS']);

	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['domains'];
		$query	.= ' WHERE dom_sa_expiration <= '.$_uts_setpoint;
		IF ($_CCFG['CC_SACC_EXP_LIST_INCL_EXPRD']) {
			$query .= ' AND dom_sa_expiration > 0';
			$_str_line_02 = $_LANG['_CC']['Expired'].$_sp.$_LANG['_CC']['or'].$_sp.$_LANG['_CC']['Expiring_In'];
		} ELSE {
			$query .= ' AND dom_sa_expiration > '.$_uts_now;
			$_str_line_02 = $_LANG['_CC']['Expiring_In'];
		}

	# Set to logged in Client ID if not admin to avoid seeing other client domains
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_SEC['_suser_id'];}

		$query .= ' ORDER BY dom_sa_expiration ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Server_Accounts'].$_sp.'</b>'.$_nl;
		$_out .= '<br><b>'.$_str_line_02.$_sp.'('.$_CCFG['CC_SACC_EXP_IN_DAYS'].')'.$_sp.$_LANG['_CC']['days'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL">'.$_sp.$_sp.'</td>'.$_nl;
				IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP06'] == 1)) {$_pmode = 'edit';} ELSE {$_pmode = 'view';}
				$_out .= '<td class="TP1SML_NL">'.'<a href="mod.php?mod=domains&mode='.$_pmode.'&dom_id='.$row['dom_id'].'">'.$row['dom_domain'].'</a>'.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL">'.dt_make_datetime($row['dom_sa_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_days = (($row['dom_sa_expiration'] - $_uts_now) / 86400);
				$_out .= '<td class="TP1SML_NL">'.'('.number_format($_days, 2, '.', '').$_sp.$_LANG['_CC']['x_days'].')'.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
	}


# Do summary: Orders
function do_summary_orders($adata) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT ord_status, count(ord_id) as ord_count, sum(ord_unit_cost) as ord_sum_cost';
		$query .= ' FROM '.$_DBCFG['orders'];
		$query .= ' WHERE ord_ts >= '.$adata['report_start'].' AND ord_ts <= '.$adata['report_end'];

	# Set to logged in Client ID if not admin to avoid seeing other client order id's
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['orders'].'.ord_cl_id='.$_SEC['_suser_id'];}

		$query .= ' GROUP BY ord_status';
		$query .= ' ORDER BY ord_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="6">'.$_nl;
		$_out .= '<b><a href="mod.php?mod=orders&mode=view">'.$_LANG['_CC']['Orders'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$ord_count_ttl	= 0;
		$ord_cost_ttl	= 0;
		IF ($numrows) {
			while(list($ord_status, $ord_count, $ord_sum_cost) = $db_coin->db_fetch_row($result)) {
				IF ($ord_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_order'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_orders'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp.$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">('.$ord_count.')</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top"><a href="mod.php?mod=orders&mode=view&status='.urlencode($ord_status).'">'.$ord_status.'</a></td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_LANG['_CC']['totalling'].$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">'.do_currency_format($ord_sum_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$ord_count_ttl = $ord_count_ttl + $ord_count;
				$ord_cost_ttl = $ord_cost_ttl + $ord_sum_cost;
			}
		}
		$_out .= '<tr><td class="TP1SML_BL" colspan="5">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$ord_count_ttl.'</b>'.$_sp.$_LANG['_CC']['lc_order_s'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="1">'.$_nl;
		$_out .= do_currency_format($ord_cost_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}


# Do summary: Products Ordered
function do_summary_product_orders($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query = 'SELECT ord_status, count(ord_prod_id) as prod_count, sum(ord_unit_cost) as ord_sum_cost, ';
		IF ($_CCFG['ORDERS_LIST_SHOW_PROD_DESC']) {$q2 .= 'prod_desc';} ELSE {$q2 .= 'prod_name';}
		$query .= $q2;
		$query .= ' FROM '.$_DBCFG['orders'].', '.$_DBCFG['products'];
		$query .= ' WHERE '.$_DBCFG['orders'].'.ord_ts >= '.$adata['report_start'].' AND '.$_DBCFG['orders'].'.ord_ts <= '.$adata['report_end'];
		$query .= ' AND '.$_DBCFG['orders'].'.ord_prod_id='.$_DBCFG['products'].'.prod_id';

	# Show only "active"
		$query .= ' AND '.$_DBCFG['orders'].".ord_status='".$db_coin->db_sanitize_data($_CCFG['ORD_STATUS'][0])."'";

	# Set to logged in Client ID if not admin to avoid seeing other client order id's
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['orders'].'.ord_cl_id='.$_SEC['_suser_id'];}

		$query .= ' GROUP BY '.$_DBCFG['products'].'.'.$q2;

	# Set the ORDER BY clause
		switch($_CCFG['SUMMARY_PRODUCTS_ORDER_BY']) {
			case 1:
				$query .= ' ORDER BY '.$_DBCFG['products'].'.'.$q2.' ASC';
				break;
			case 2:
				$query .= ' ORDER BY '.$_DBCFG['products'].'.'.$q2.' DESC';
				break;
			case 3:
				$query .= ' ORDER BY prod_count ASC';
				break;
			case 4:
				$query .= ' ORDER BY prod_count DESC';
				break;
			case 5:
				$query .= ' ORDER BY ord_sum_cost ASC';
				break;
			case 6:
				$query .= ' ORDER BY ord_sum_cost DESC';
				break;
			default:
				$query .= ' ORDER BY '.$_DBCFG['products'].'.'.$q2.' ASC';
				break;

		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="6">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Active_Products'].$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$ord_cost_ttl	= 0;
		IF ($numrows) {
			while(list($ord_status, $prod_count, $ord_sum_cost, $prod_name) = $db_coin->db_fetch_row($result)) {
				IF ($prod_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_order'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_orders'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp.$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">('.$prod_count.')</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$prod_name.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_LANG['_CC']['totalling'].$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">'.do_currency_format($ord_sum_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$ord_cost_ttl = $ord_cost_ttl + $ord_sum_cost;
			}
		}
		$_out .= '<tr><td class="TP1SML_BL" colspan="5">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$numrows.'</b>'.$_sp.$_LANG['_CC']['lc_products'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="1">'.$_nl;
		$_out .= do_currency_format($ord_cost_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		return $_out;
}


# Do summary: Invoices (Single Column)
function do_summary_invoices($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select
		$query  = 'SELECT invc_status, count(invc_id) as invc_count, sum(invc_total_cost) as invc_sum_cost, sum(invc_total_paid) as invc_sum_paid';
		$query .= ' FROM '.$_DBCFG['invoices'];

	# Set the dates
		$query .= ' WHERE invc_ts >= '.$adata['report_start'].' AND invc_ts <= '.$adata['report_end'];

	# Show pending to client yes/no
		IF (!$_SEC['_sadmin_flg'] && !$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
			$query .= " AND invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
		}

	# Make sure client can see only their own invoices
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND invc_cl_id='.$_SEC['_suser_id'];}

		$query .= ' GROUP BY invc_status';
		$query .= ' ORDER BY invc_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="7">'.$_nl;
		$_out .= '<b><a href="mod.php?mod=invoices">'.$_LANG['_CC']['Invoices'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$invc_count_ttl	= 0;
		$invc_cost_ttl	= 0;
		IF ($numrows) {
			while(list($invc_status, $invc_count, $invc_sum_cost, $invc_sum_paid) = $db_coin->db_fetch_row($result)) {
				IF ($invc_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_invoice'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_invoices'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp.$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">('.$invc_count.')</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top"><a href="mod.php?mod=invoices&status='.urlencode($invc_status).'">'.$invc_status.'</a></td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_LANG['_CC']['totalling'].$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">'.do_currency_format($invc_sum_cost, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">';
				IF ($invc_sum_cost != $invc_sum_paid) {
					$_out .= do_currency_format($invc_sum_cost - $invc_sum_paid, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$invc_count_ttl = $invc_count_ttl + $invc_count;
				$invc_cost_ttl = $invc_cost_ttl + $invc_sum_cost;
			}
		}
		$_out .= '<tr><td class="TP1SML_BR" colspan="5">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$invc_count_ttl.'</b>'.$_sp.$_LANG['_CC']['lc_invoice_s'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="1">'.$_nl;
		$_out .= do_currency_format($invc_cost_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td></td></tr>'.$_nl;

	# Show the amount of money actually receivable, as opposed to the different invoice status
		$idata = do_get_invc_cl_balance($_SEC['_suser_id'],0);
		$_out .= '<tr><td class="TP1SML_BR" colspan="5">'.$_nl;
		$_out .= '<b>' . $_LANG['_CC']['Balance_Due'] . ':</b>'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_NR" colspan="2">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="4">'.$_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Close the table
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

	return $_out;
}


# Do summary: Invoices (Column By Billing Type)
function do_summary_invoices_columnar($adata) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp, $_br;
		$invc_numbers = array();

	# initialize totals for each status
		foreach($_CCFG['INV_STATUS'] as $key => $var) {
			$invc_numbers[$var]['scount']			= 0;
			$invc_numbers[$var]['stotal']			= 0;
			$invc_numbers['ctotal']['stotal']		= 0;
			$invc_numbers['ctotal']['scount']		= 0;
			foreach($_CCFG['INVC_BILL_CYCLE'] as $key2 => $var2) {
				$invc_numbers[$var][$key2]		= 0;
				$invc_numbers['ctotal'][$key2]	= 0;
			}
		}

	# Set Query for select.
		$query  = 'SELECT invc_status, count(invc_id) as invc_count, invc_bill_cycle, sum(invc_total_cost) as invc_sum_cost';
		$query .= ' FROM '.$_DBCFG['invoices'];
		$query .= ' WHERE invc_ts >= '.$adata['report_start'].' AND invc_ts <= '.$adata['report_end'];

	# Set to logged in Client ID if not admin to avoid seeing other client invoice id's
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND invc_cl_id='.$_SEC['_suser_id'];}

	# Check show pending enable flag
		IF (!$_SEC['_sadmin_flg'] && !$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
			$query .= " AND invc_status != '".$_CCFG['INV_STATUS'][4]."'";
		}

		$query .= ' GROUP BY invc_bill_cycle, invc_status';
		$query .= ' ORDER BY invc_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Only update array if there is any data
		IF ($numrows) {
			while(list($invc_status, $invc_count, $invc_bill_cycle, $invc_sum_cost) = $db_coin->db_fetch_row($result)) {
				$invc_numbers['ctotal']['stotal'] += $invc_sum_cost;
				$invc_numbers['ctotal'][$invc_bill_cycle] += $invc_sum_cost;
				$invc_numbers['ctotal']['scount'] += $invc_count;
				$invc_numbers[$invc_status][$invc_bill_cycle] += $invc_sum_cost;
				$invc_numbers[$invc_status]['stotal'] += $invc_sum_cost;
				$invc_numbers[$invc_status]['scount'] += $invc_count;
			}
		}

	# Build output table by starting with header, then appending the row for any
	# status that either has invoices or we want to see empty row, then finally appending footer

	# Start table header: add a column for each bill cycle, plus a total and a balance due column
		$_table  = '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_table .= '<tr class="BLK_DEF_TITLE">';
		$_table .= '<td class="TP3SML_BL">'.$_LANG['_CC']['Invoices'].'</td>'.$_nl;
		foreach($_CCFG['INVC_BILL_CYCLE'] as $key => $var) {
			IF ($invc_numbers['ctotal'][$key] || $_CCFG['SHOW_EMPTY_INVC_CYCLE']) {$_table .= '<td class="TP3SML_BR"><b>'.$var.'</b></td>'.$_nl;}
		}
		$_table .= '<td class="TP3SML_BR"><b>'.$_LANG['_CC']['Total'].'</b></td>'.$_nl;
		$_table .= '<td class="TP3SML_BR"><b>'.$_LANG['_CC']['Balance_Due'].'</b></td>'.$_nl;

	# Make one row for each status, with a column for each bill cycle plus a total and a balance due column
		foreach($_CCFG['INV_STATUS'] as $key => $var) {
			IF ($invc_numbers[$var]['scount'] != 1) {$_str_02 = $_LANG['_CC']['lc_invoices'];} ELSE {$_str_02 = $_LANG['_CC']['lc_invoice'];}
			$_tRows  = '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_tRows .= '<td class="TP3SML_NL">'.$_sp.'<a href="mod.php?mod=invoices&status='.$var.'">'.$invc_numbers[$var]['scount'].'</a> '.$var.$_sp.$_str_02.'</td>'.$_nl;
			foreach($_CCFG['INVC_BILL_CYCLE'] as $key2 => $var2) {
				IF ($invc_numbers['ctotal'][$key2] || $_CCFG['SHOW_EMPTY_INVC_CYCLE']) {
					$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($invc_numbers[$var][$key2],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
			}
			$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($invc_numbers[$var]['stotal'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			$AmountDue = get_invoice_balance_by_status($var, $_SEC['_suser_id']);
			$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($AmountDue,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			$_tRows .= '</tr>'.$_nl;

		# Add row to table
			IF ($invc_numbers[$var]['scount'] || $_CCFG['SHOW_EMPTY_INVC_STATUS']) {$_table .= $_tRows;}
		}

	# Create the Totals row and close table
		$_table .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_table .= '<td class="TP3SML_BL">'.$_sp.$invc_numbers['ctotal']['scount'].$_sp.$_LANG['_CC']['lc_invoices'].$_sp.$_LANG['_CC']['totalling'].'</td>'.$_nl;
		foreach($_CCFG['INVC_BILL_CYCLE'] as $key => $var) {
			IF ($invc_numbers['ctotal'][$key] || $_CCFG['SHOW_EMPTY_INVC_CYCLE']) {
				$_table .= '<td class="TP1SML_BR">';
				IF ($invc_numbers['ctotal'][$key] || $_CCFG['SHOW_INVC_ZERO_TOTAL']) {
					$_table .= do_currency_format($invc_numbers['ctotal'][$key],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				} ELSE {
					$_table .= $_sp;
				}
				$_table .= '</td>'.$_nl;
			}
		}
		$_table .= '<td class="TP3SML_BR">'.do_currency_format($invc_numbers['ctotal']['stotal'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$idata   = do_get_invc_cl_balance($_SEC['_suser_id'], 0);
		$_table .= '<td class="TP3SML_BR">'.do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_table .= '</tr>'.$_nl;
		$_table .= '</table>'.$_nl;

	# Build form output
		$_out  = '<div align="left">';
		$_out .= '<p><b>';
		$_out .= '<a href="mod.php?mod=invoices">'.$_LANG['_CC']['Invoices'].'</a>';
		$_out .= $_sp.$_LANG['_CC']['By_Cycle'].$_sp.$_LANG['_CC']['Summary'];
		$_out .= ':</b></p>'.$_br.$_nl;
		$_out .= $_table.$_nl;
		$_out .= '</div>'.$_nl;

	# Return the results
		return $_out;
}


function get_invoice_balance_by_status($status, $cl_id) {
	# Dim some vars
		global $_CCFG, $_DBCFG, $db_coin;
		$due = 0;

	# Ignore "Pending", "draft", and "void"
		IF ($status == $_CCFG['INV_STATUS'][4]) {return 0;}
		IF ($status == $_CCFG['INV_STATUS'][1]) {return 0;}
		IF ($status == $_CCFG['INV_STATUS'][5]) {return 0;}

	# Set Query for select.
		$query	= 'SELECT sum(invc_total_cost), sum(invc_total_paid)';
		$query .= ' FROM '.$_DBCFG['invoices'];
		$query .= " WHERE invc_status='".$db_coin->db_sanitize_data($status)."'";
		IF ($cl_id) {$query .= ' AND invc_cl_id='.$cl_id;}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {

		# Process query results
			while(list($invc_total_cost, $invc_total_paid) = $db_coin->db_fetch_row($result)) {
				$due = $invc_total_cost - $invc_total_paid;
			}
		}

	# return result;
		return $due;
}

# Do summary: HelpDesk Support Tickets
function do_summary_support_tickets($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for Closed Tickets records for list.
		$query_c  = 'SELECT hd_tt_closed, count(hd_tt_id) as tt_count_c';
		$query_c .= ' FROM '.$_DBCFG['helpdesk'];
		$query_c .= ' WHERE hd_tt_time_stamp >= '.$adata['report_start'].' AND hd_tt_time_stamp <= '.$adata['report_end'];
		$query_c .= ' AND hd_tt_closed=1';

	# Set to logged in Client ID if not admin to avoid seeing other client order id's
		IF (!$_SEC['_sadmin_flg']) {
			$query_c .= ' AND hd_tt_cl_id='.$_SEC['_suser_id'];}
			$query_c .= ' GROUP BY hd_tt_closed';
			$query_c .= ' ORDER BY hd_tt_closed ASC';

		# Do select and return check
			$result_c		= $db_coin->db_query_execute($query_c);
			$numrows_c	= $db_coin->db_query_numrows($result_c);

		# Set Query for Open Tickets records for list.
			$query_o  = 'SELECT hd_tt_status, count(hd_tt_id) as tt_count_o';
			$query_o .= ' FROM '.$_DBCFG['helpdesk'];
			$query_o .= ' WHERE hd_tt_time_stamp >= '.$adata['report_start'].' AND hd_tt_time_stamp <= '.$adata['report_end'];
			$query_o .= ' AND hd_tt_closed=0';

		# Set to logged in Client ID if not admin to avoid seeing other client order id's
			IF (!$_SEC['_sadmin_flg']) {
				$query_o .= ' AND hd_tt_cl_id='.$_SEC['_suser_id'];
			}
			$query_o .= ' GROUP BY hd_tt_status';
			$query_o .= ' ORDER BY hd_tt_status ASC';

		# Do select and return check
			$result_o		= $db_coin->db_query_execute($query_o);
			$numrows_o	= $db_coin->db_query_numrows($result_o);

		# Build form output
			$_out .= '<div align="left">'.$_nl;
			$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
			$_out .= '<b><a href="mod.php?mod=helpdesk">'.$_LANG['_CC']['HelpDesk'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
			$_out .= '<b><a href="mod.php?mod=helpdesk&fb=1&fs=0">'.$_LANG['_CC']['Open'].'</a>'.$_sp.$_LANG['_CC']['Ticket'].$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
			$_out .= '</td></tr>'.$_nl;

		# Print out open ticket query results
			$tt_count_ttl_o = 0;
			IF ($numrows_o) {
				while(list($hd_tt_status, $tt_count_o) = $db_coin->db_fetch_row($result_o)) {
					IF ($tt_count_o == 1) {
						$_str_02 = $_LANG['_CC']['lc_support_ticket'];
					} ELSE {
						$_str_02 = $_LANG['_CC']['lc_support_tickets'];
					}
					$_out .= '<tr>'.$_nl;
					$_out .= '<td class="TP1SML_NL">'.$_sp.$_sp.'</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL" valign="top">('.$tt_count_o.')</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL" valign="top"><a href="mod.php?mod=helpdesk&fb=3&fs='.$hd_tt_status.'">'.$hd_tt_status.'</a></td>'.$_nl;
					$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'.</td>'.$_nl;
					$_out .= '</tr>'.$_nl;
					$tt_count_ttl_o = $tt_count_ttl_o + $tt_count_o;
				}
			}
			$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
			$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$tt_count_ttl_o.$_sp.$_LANG['_CC']['Open'].$_sp.$_LANG['_CC']['lc_support_ticket_s'].'</b>'.$_nl;
			$_out .= '</td></tr>'.$_nl;

			$_out .= '<tr><td class="TP1MED_BC" colspan="4">'.$_sp.$_nl.'</td></tr>'.$_nl;

		# Print out closed ticket query results
			$tt_count_ttl_c	= 0;
			IF ($numrows_c) {
				while(list($hd_tt_closed, $tt_count_c) = $db_coin->db_fetch_row($result_c)) {
					$tt_count_ttl_c = $tt_count_ttl_c + $tt_count_c;
				}
			}
			$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
			$_out .= '<b><a href="mod.php?mod=helpdesk&fb=1&fs=1">'.$_LANG['_CC']['Closed'].'</a>'.$_sp.$_LANG['_CC']['Ticket'].$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
			$_out .= '</td></tr>'.$_nl;
			$_out .= '<tr><td class="TP1SML_BL" colspan="4">'.$_nl;
			$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$tt_count_ttl_c.$_sp.$_LANG['_CC']['Closed'].$_sp.$_LANG['_CC']['lc_support_ticket_s'].'</b>'.$_nl;
			$_out .= '</td></tr>'.$_nl;

			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;

		return $_out;
}


# Do summary: Servers
function do_summary_servers() {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT '.$_DBCFG['server_info'].'.si_id, '.$_DBCFG['server_info'].'.si_name';
		$query	.= ', count('.$_DBCFG['domains'].'.dom_id) as si_count';
		$query	.= ' FROM '.$_DBCFG['server_info'].', '.$_DBCFG['domains'];
		$query	.= ' WHERE '.$_DBCFG['server_info'].'.si_id='.$_DBCFG['domains'].'.dom_si_id';

	# Set to logged in Client ID if not admin to avoid seeing other client's stuff
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_SEC['_suser_id'];}

		$query .= ' GROUP BY si_name';
		$query .= ' ORDER BY si_name ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="2">'.$_nl;
		$_out .= $_LANG['_CC']['Servers'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$domain_count_ttl	= 0;
		$server_count_ttl	= $numrows;
		IF ($numrows) {
			while(list($si_id, $si_name, $si_count) = $db_coin->db_fetch_row($result)) {
				IF ($si_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_domain'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_domains'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL">'.$_sp.$_sp.'</td>'.$_nl;
				$_link = '<a href="mod.php?mod=domains&mode=view&fb=1&fs='.$si_id.'">'.$si_name.'</a>'.$_nl;
				$_out .= '<td class="TP1SML_NL">('.$si_count.')'.$_sp.$_link.$_sp.$_str_02.'</td>'.$_nl;
				$_out .= '</tr>'.$_nl;
				$domain_count_ttl = $domain_count_ttl + $si_count;
			}
		}
		$_out .= '<tr><td class="TP1SML_BL" colspan="2">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.'('.$domain_count_ttl.')'.'</b>'.$_sp;
		$_out .= $_LANG['_CC']['lc_domain_s'].$_sp.$_LANG['_CC']['on'].$_sp.'('.$server_count_ttl.')'.$_sp.$_LANG['_CC']['lc_server_s'].$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}


function do_summary_invoice_products($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query = 'SELECT count(ii_item_name) as prod_count, sum(ii_item_cost) as prod_cost, ii_item_name, ii_item_desc, '.$_DBCFG['products'].'.prod_desc';
		$query .= ' FROM '.$_DBCFG['invoices_items'];

	# Grab the invoice info
		$query	.= ' LEFT JOIN '.$_DBCFG['invoices'];
		$query	.= ' ON '.$_DBCFG['invoices_items'].'.ii_invc_id='.$_DBCFG['invoices'].'.invc_id';

	# Grab the product description
		$query	.= ' LEFT JOIN '.$_DBCFG['products'];
		$query	.= ' ON '.$_DBCFG['invoices_items'].'.ii_item_name='.$_DBCFG['products'].'.prod_name';

	# Set the dates
		$query .= ' WHERE '.$_DBCFG['invoices'].'.invc_ts >= '.$adata['report_start'].' AND '.$_DBCFG['invoices'].'.invc_ts <= '.$adata['report_end'];

	# Do NOT retrieve invoices with status in "Do not count status" array
		IF ($_CCFG['SUMMARY_INVOICES_BY_PRODUCT_STATUS_IGNORE']) {
			$_NOSTAT = explode('|', $_CCFG['SUMMARY_INVOICES_BY_PRODUCT_STATUS_IGNORE']);
			foreach ($_NOSTAT as $key => $value) {
				$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][$value])."'";
			}
		}

	# Show pending yes/no
		IF (!$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
			$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
		}

	# Make sure client can see only their own invoices
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_SEC['_suser_id'];}

		$query .= ' GROUP BY ii_item_name';

	# Set the ORDER BY clause
		switch($_CCFG['SUMMARY_PRODUCTS_ORDER_BY']) {
			case 1:
				$query .= ' ORDER BY prod_desc ASC';
				break;
			case 2:
				$query .= ' ORDER BY prod_desc DESC';
				break;
			case 3:
				$query .= ' ORDER BY prod_count ASC';
				break;
			case 4:
				$query .= ' ORDER BY prod_count DESC';
				break;
			case 5:
				$query .= ' ORDER BY prod_cost ASC';
				break;
			case 6:
				$query .= ' ORDER BY prod_cost DESC';
				break;
			case 7:
				$query .= ' ORDER BY prod_name ASC';
				break;
			case 8:
				$query .= ' ORDER BY prod_name DESC';
				break;
			default:
				$query .= ' ORDER BY prod_desc ASC';
				break;
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<p><b>'.$_LANG['_CC']['Invoiced_Products'].$_sp.$_LANG['_CC']['Summary'].':</b></p>'.$_nl;

		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE">';
		$_out .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Quantity'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Product'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Description'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Total'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Prepare "Do not count items" array
		IF ($_CCFG['SUMMARY_INVOICES_BY_PRODUCT_IGNORE']) {$_NOSUM = explode('|', $_CCFG['SUMMARY_INVOICES_BY_PRODUCT_IGNORE']);}

	# Process query results
		$prod_count_ttl	= 0;
		$prod_cost_ttl		= 0;
		IF ($numrows) {
			while(list($prod_count, $prod_cost, $prod_name, $prod_desc, $pd2) = $db_coin->db_fetch_row($result)) {
				IF (!$_CCFG['SUMMARY_INVOICES_BY_PRODUCT_IGNORE'] || !in_array($prod_name, $_NOSUM)) {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP1SML_NR">'.$prod_count.$_sp.'</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL">'.$_sp;
					IF ($_SEC['_sadmin_flg']) {
						$_out .= '<a href="mod.php?mod=cc&mode=iitems&ii_item_name='.$prod_name.'&report_start='.$adata['report_start'].'&report_end='.$adata['report_end'].'">'.$prod_name.'</a>';
					} ELSE {
						$_out .= $prod_name;
					}
					$_out .= '</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL">'.$_sp;
					IF ($pd2) {$_out .= $pd2;} ELSE {$_out .= $prod_desc;}
					$_out .= '</td>'.$_nl;
					$_out .= '<td class="TP1SML_NR">'.do_currency_format($prod_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.'</td>'.$_nl;
					$_out .= '</tr>'.$_nl;
					$prod_cost_ttl += $prod_cost;
					$prod_count_ttl++;
				}
			}
		}
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_BR" colspan="3">';
		$_out .= $prod_count_ttl.$_sp.$_LANG['_CC']['lc_products'].$_sp.$_LANG['_CC']['totalling'].$_sp;
		$_out .= '</td>'.$_nl;
		$_out .= '<td class="TP1SML_BR">';
		$_out .= do_currency_format($prod_cost_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_out .= $_sp.'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

	# Return results
		$_cres = array();
		$_cres['text']		= $_out;
		$_cres['amount']	= $prod_cost_ttl;
		return $_cres;
}


function do_summary_bills_products($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query  = 'SELECT count(bi_item_name) as prod_count, sum(bi_item_cost) as prod_cost, bi_item_name, bi_item_desc';
		$query .= ' FROM '.$_DBCFG['bills_items'];

	# Grab the bill info
		$query	.= ' LEFT JOIN '.$_DBCFG['bills'];
		$query	.= ' ON '.$_DBCFG['bills_items'].'.bi_bill_id='.$_DBCFG['bills'].'.bill_id';

		$query .= ' WHERE bill_ts >= '.$adata['report_start'].' AND bill_ts <= '.$adata['report_end'];
		$query .= ' GROUP BY bi_item_name';

	# Set the ORDER BY clause
		switch($_CCFG['SUMMARY_PRODUCTS_ORDER_BY']) {
			case 1:
				$query .= ' ORDER BY bi_item_desc ASC';
				break;
			case 2:
				$query .= ' ORDER BY bi_item_desc DESC';
				break;
			case 3:
				$query .= ' ORDER BY prod_count ASC';
				break;
			case 4:
				$query .= ' ORDER BY prod_count DESC';
				break;
			case 5:
				$query .= ' ORDER BY prod_cost ASC';
				break;
			case 6:
				$query .= ' ORDER BY prod_cost DESC';
				break;
			case 7:
				$query .= ' ORDER BY bi_item_name ASC';
				break;
			case 8:
				$query .= ' ORDER BY bi_item_name DESC';
				break;
			default:
				$query .= ' ORDER BY bi_item_desc ASC';
				break;

		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<p><b>'.$_LANG['_CC']['Expense_Items'].$_sp.$_LANG['_CC']['Summary'].':</b></p>'.$_nl;

		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE">';
		$_out .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Quantity'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Product'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Description'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Total'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Prepare "Do not count" array
		IF ($_CCFG['SUMMARY_BILLS_BY_PRODUCT_IGNORE']) {$_NOSUM = explode('|', $_CCFG['SUMMARY_BILLS_BY_PRODUCT_IGNORE']);}

	# Process query results
		$prod_count_ttl	= 0;
		$prod_cost_ttl		= 0;
		IF ($numrows) {
			while(list($prod_count, $prod_cost, $prod_name, $prod_desc, $pd2) = $db_coin->db_fetch_row($result)) {
				IF (!$_CCFG['SUMMARY_BILLS_BY_PRODUCT_IGNORE'] || !in_array($prod_name, $_NOSUM)) {
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP1SML_NR" valign="top">'.$prod_count.$_sp.'</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp;
					IF ($_SEC['_sadmin_flg']) {
						$_out .= '<a href="mod.php?mod=cc&mode=bitems&bi_item_name='.$prod_name.'&report_start='.$adata['report_start'].'&report_end='.$adata['report_end'].'">'.$prod_name.'</a>';
					} ELSE {
						$_out .= $prod_name;
					}
					$_out .= '</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp;
					IF ($pd2) {$_out .= $pd2;} ELSE {$_out .= $prod_desc;}
					$_out .= '</td>'.$_nl;
					$_out .= '<td class="TP1SML_NR" valign="top">'.do_currency_format($prod_cost,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.'</td>'.$_nl;
					$_out .= '</tr>'.$_nl;
					$prod_cost_ttl += $prod_cost;
					$prod_count_ttl++;
				}
			}
		}
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_BR" colspan="3">';
		$_out .= $prod_count_ttl.$_sp.$_LANG['_CC']['lc_expenses'].$_sp.$_LANG['_CC']['totalling'].$_sp;
		$_out .= '</td>'.$_nl;
		$_out .= '<td class="TP1SML_BR">'.$_sp;
		$_out .= do_currency_format($prod_cost_ttl,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$_out .= $_sp.'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

	# Return results
		$_cres = array();
		$_cres['text']		= $_out;
		$_cres['amount']	= $prod_cost_ttl;
		return $_cres;
}

# Do list select field for: Bill Trans Type
function do_search_select_list_bill_trans_type($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="'.''.'"';
		IF ($avalue == '') {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_CC']['Please_Select'].'</option>'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['BILL_TRANS_TYPE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_TRANS_TYPE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select field for: Bill Trans Origin
function do_search_select_list_bill_trans_origin($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="'.''.'"';
		IF ($avalue == '') {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_CC']['Please_Select'].'</option>'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['BILL_TRANS_ORIGIN']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['BILL_TRANS_ORIGIN'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


function do_summary_taxes($adata) {
	# Dim some Vars:
		global $_TCFG, $_CCFG, $_LANG, $_DBCFG, $db_coin, $_sp, $_nl;

	# Start output
		$_out = '<div>'.$_nl;

	# Do invoice taxes
		IF ($_CCFG['INVC_TAX_01_ENABLE'] || $_CCFG['INVC_TAX_01_ENABLE']) {

		# Start block
			$_out .= '<div style="float: left;">'.$_nl;
			$_out .= '<p><b>'.$_LANG['_CC']['Invoice_Taxes'].$_sp.$_LANG['_CC']['Summary'].':</b></p>'.$_nl;

			$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE">';
			$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Quantity'].'</td>'.$_nl;
			$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Status'].'</td>'.$_nl;
			IF ($_CCFG['INVC_TAX_01_ENABLE']) {
				$_out .= '<td class="TP3MED_BL">'.$_CCFG['INVC_TAX_01_LABEL'].'</td>'.$_nl;
			}
			IF ($_CCFG['INVC_TAX_02_ENABLE']) {
				$_out .= '<td class="TP3MED_BR">'.$_CCFG['INVC_TAX_02_LABEL'].'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		# Set Query for select
			$query  = 'SELECT invc_status, count(invc_id) as invc_count, sum(invc_tax_01_amount) as invc_sum_tax1, sum(invc_tax_02_amount) as invc_sum_tax2';
			$query .= ' FROM '.$_DBCFG['invoices'];
			$query .= ' WHERE invc_ts >= '.$adata['report_start'].' AND invc_ts <= '.$adata['report_end'];
			$query .= ' GROUP BY invc_status';
			$query .= ' ORDER BY invc_status ASC';

		# Do select and return check
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results
			$invc_count_ttl	= 0;
			$invc_t1_ttl		= 0;
			$invc_t2_ttl		= 0;
			IF ($numrows) {
				while(list($invc_status, $invc_count, $invc_sum_tax1, $invc_sum_tax2) = $db_coin->db_fetch_row($result)) {
					IF ($invc_count == 1) {
						$_str_02 = $_LANG['_CC']['lc_invoice'];
					} ELSE {
						$_str_02 = $_LANG['_CC']['lc_invoices'];
					}
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP1SML_NL">'.$invc_count.'</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL"><a href="mod.php?mod=invoices&status='.urlencode($invc_status).'">'.$invc_status.'</a>'.$_sp.$_str_02.$_sp.$_LANG['_CC']['totalling'].'</td>'.$_nl;
					IF ($_CCFG['INVC_TAX_01_ENABLE']) {
						$_out .= '<td class="TP1SML_NR">'.do_currency_format($invc_sum_tax1, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
					}
					IF ($_CCFG['INVC_TAX_02_ENABLE']) {
						$_out .= '<td class="TP1SML_NR">'.do_currency_format($invc_sum_tax2, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
					}
					$_out .= '</tr>'.$_nl;

					$invc_count_ttl += $invc_count;
					$invc_t1_ttl += $invc_sum_tax1;
					$invc_t2_ttl += $invc_sum_tax2;
				}
			}
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP1SML_BR" colspan="2">'.$_nl;
			$_out .= '<b>'.$invc_count_ttl.'</b>'.$_sp.$_LANG['_CC']['lc_invoice_s'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
			$_out .= '</td>';
			IF ($_CCFG['INVC_TAX_01_ENABLE']) {
				$_out .= '<td class="TP1SML_BR">'.do_currency_format($invc_t1_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			IF ($_CCFG['INVC_TAX_02_ENABLE']) {
				$_out .= '<td class="TP1SML_BR">'.do_currency_format($invc_t2_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		# Close the table
			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;
		}


	# Do bill taxes
		IF ($_CCFG['BILL_TAX_01_ENABLE'] || $_CCFG['BILL_TAX_01_ENABLE']) {

		# Start block
			$_out .= '<div style="float: right;">'.$_nl;
			$_out .= '<p><b>'.$_LANG['_CC']['Bill_Taxes'].$_sp.$_LANG['_CC']['Summary'].':</b></p>'.$_nl;

			$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE">';
			$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Quantity'].'</td>'.$_nl;
			$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['Status'].'</td>'.$_nl;
			IF ($_CCFG['BILL_TAX_01_ENABLE']) {
				$_out .= '<td class="TP3MED_BL">'.$_CCFG['BILL_TAX_01_LABEL'].'</td>'.$_nl;
			}
			IF ($_CCFG['BILL_TAX_02_ENABLE']) {
				$_out .= '<td class="TP3MED_BR">'.$_CCFG['BILL_TAX_02_LABEL'].'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		# Set Query for select
			$query  = 'SELECT bill_status, count(bill_id) as invc_count, sum(bill_tax_01_amount) as bill_sum_tax1, sum(bill_tax_02_amount) as bill_sum_tax2';
			$query .= ' FROM '.$_DBCFG['bills'];
			$query .= ' WHERE bill_ts >= '.$adata['report_start'].' AND bill_ts <= '.$adata['report_end'];
			$query .= ' GROUP BY bill_status';
			$query .= ' ORDER BY bill_status ASC';

		# Do select and return check
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results
			$invc_count_ttl	= 0;
			$invc_t1_ttl		= 0;
			$invc_t2_ttl		= 0;
			IF ($numrows) {
				while(list($invc_status, $invc_count, $invc_sum_tax1, $invc_sum_tax2) = $db_coin->db_fetch_row($result)) {
					IF ($invc_count == 1) {
						$_str_02 = $_LANG['_CC']['lc_bill'];
					} ELSE {
						$_str_02 = $_LANG['_CC']['lc_bills'];
					}
					$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_out .= '<td class="TP1SML_NL">'.$invc_count.'</td>'.$_nl;
					$_out .= '<td class="TP1SML_NL"><a href="mod.php?mod=bills&status='.urlencode($invc_status).'">'.$invc_status.'</a>'.$_sp.$_str_02.$_sp.$_LANG['_CC']['totalling'].'</td>'.$_nl;
					IF ($_CCFG['BILL_TAX_01_ENABLE']) {
						$_out .= '<td class="TP1SML_NR">'.do_currency_format($invc_sum_tax1, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
					}
					IF ($_CCFG['BILL_TAX_02_ENABLE']) {
						$_out .= '<td class="TP1SML_NR">'.do_currency_format($invc_sum_tax2, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
					}
					$_out .= '</tr>'.$_nl;

					$invc_count_ttl += $invc_count;
					$invc_t1_ttl += $invc_sum_tax1;
					$invc_t2_ttl += $invc_sum_tax2;
				}
			}
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP1SML_BR" colspan="2">'.$_nl;
			$_out .= '<b>'.$invc_count_ttl.'</b>'.$_sp.$_LANG['_CC']['lc_bill_s'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
			$_out .= '</td>';
			IF ($_CCFG['BILL_TAX_01_ENABLE']) {
				$_out .= '<td class="TP1SML_BR">'.do_currency_format($invc_t1_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			IF ($_CCFG['BILL_TAX_02_ENABLE']) {
				$_out .= '<td class="TP1SML_BR">'.do_currency_format($invc_t2_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		# Close the table
			$_out .= '</table>'.$_nl;
			$_out .= '</div>'.$_nl;
		}

		$_out .= '</div>'.$_nl;
		return $_out;
}


function show_profitability($start_date, $end_date, $bills_ttl, $invc_ttl) {
	# grab some variables
		global $_CCFG, $_LANG, $_TCFG, $_sp, $_nl;

	# Calculations
		$_days_in_period			= ($end_date - $start_date) / (60*60*24);
		$_gross_profit_for_period	= $invc_ttl - $bills_ttl;
		$_gross_margin_for_period	= ($_gross_profit_for_period/$invc_ttl)*100;

	# Build output
		$_out  = '<div align="left">'.$_nl;
		$_out .= '<p><b>'.$_LANG['_CC']['Profitability'].$_sp.$_LANG['_CC']['Summary'].':</b></p>'.$_nl;
		$_out .= '<p>'.$_LANG['_CC']['Profitability_Note'].'</p>'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE">';
		$_out .= '<td class="TP3MED_BL">'.$_LANG['_CC']['l_Description'].'</td>'.$_nl;
		$_out .= '<td class="TP3MED_BR">'.$_LANG['_CC']['Total'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Days In Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Days_In_Period'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.round($_days_in_period, 0).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Income Dollars For Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Invoiced_Products'].$_sp.$_LANG['_CC']['Summary'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.do_currency_format($invc_ttl, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Expense Dollars For Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Operating_Expenses'].$_sp.$_LANG['_CC']['Summary'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.do_currency_format($bills_ttl, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Operating Profit Dollars For Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Gross_Profit_For_Period'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.do_currency_format($_gross_profit_for_period, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Operating Profit Dollars Per Day For Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Gross_Profit_Per_Day'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.do_currency_format($_gross_profit_for_period / $_days_in_period, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Gross Profit Margin For Period
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP1SML_NL">'.$_LANG['_CC']['Gross_Margin_For_Period'].'</td>'.$_nl;
		$_out .= '<td class="TP1SML_NR">'.round($_gross_margin_for_period, 3).'%'.'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Close table and return results
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		return $_out;

}


# Do summary: Bills (Column By Billing Type)
function do_summary_bills_columnar($adata) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp, $_br;
		$bill_numbers = array();

	# initialize totals for each status
		foreach($_CCFG['BILL_STATUS'] as $key => $var) {
			$bill_numbers[$var]['scount']			= 0;
			$bill_numbers[$var]['stotal']			= 0;
			$bill_numbers['ctotal']['stotal']		= 0;
			$bill_numbers['ctotal']['scount']		= 0;
			foreach($_CCFG['BILL_CYCLE'] as $key2 => $var2) {
				$bill_numbers[$var][$key2]		= 0;
				$bill_numbers['ctotal'][$key2]	= 0;
			}
		}


	# Set Query for select.
		$query  = 'SELECT bill_status, count(bill_id) as bill_count, bill_cycle, sum(bill_total_cost) as bill_sum_cost';
		$query .= ' FROM '.$_DBCFG['bills'];
		$query .= ' WHERE bill_ts >= '.$adata['report_start'].' AND bill_ts <= '.$adata['report_end'];

		$query .= ' GROUP BY bill_cycle, bill_status';
		$query .= ' ORDER BY bill_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Only update array if there is any data
		IF ($numrows) {
			while(list($bill_status, $bill_count, $bill_cycle, $bill_sum_cost) = $db_coin->db_fetch_row($result)) {
				$bill_numbers['ctotal']['stotal'] += $bill_sum_cost;
				$bill_numbers['ctotal'][$bill_cycle] += $bill_sum_cost;
				$bill_numbers['ctotal']['scount'] += $bill_count;
				$bill_numbers[$bill_status][$bill_cycle] += $bill_sum_cost;
				$bill_numbers[$bill_status]['stotal'] += $bill_sum_cost;
				$bill_numbers[$bill_status]['scount'] += $bill_count;
			}
		}

	# Build output table by starting with header, then appending the row for any
	# status that either has bills or we want to see empty row, then finally appending footer

	# Start table header: add a column for each bill cycle, plus a total and a balance due column
		$_table  = '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_table .= '<tr class="BLK_DEF_TITLE">';
		$_table .= '<td class="TP3SML_BL">'.$_LANG['_CC']['Bills'].'</td>'.$_nl;
		foreach($_CCFG['BILL_CYCLE'] as $key => $var) {
			IF ($bill_numbers['ctotal'][$key] || $_CCFG['SHOW_EMPTY_BILL_CYCLE']) {$_table .= '<td class="TP3SML_BR"><b>'.$var.'</b></td>'.$_nl;}
		}
		$_table .= '<td class="TP3SML_BR"><b>'.$_LANG['_CC']['Total'].'</b></td>'.$_nl;
		$_table .= '<td class="TP3SML_BR"><b>'.$_LANG['_CC']['Balance_Due'].'</b></td>'.$_nl;

	# Make one row for each status, with a column for each bill cycle plus a total and a balance due column
		foreach($_CCFG['BILL_STATUS'] as $key => $var) {
			IF ($bill_numbers[$var]['scount'] != 1) {$_str_02 = $_LANG['_CC']['lc_bills'];} ELSE {$_str_02 = $_LANG['_CC']['lc_bill'];}
			$_tRows  = '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_tRows .= '<td class="TP3SML_NL">'.$_sp.'<a href="mod.php?mod=bills&status='.$var.'">'.$bill_numbers[$var]['scount'].'</a> '.$var.$_sp.$_str_02.'</td>'.$_nl;
			foreach($_CCFG['BILL_CYCLE'] as $key2 => $var2) {
				IF ($bill_numbers['ctotal'][$key2] || $_CCFG['SHOW_EMPTY_BILL_CYCLE']) {
					$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($bill_numbers[$var][$key2],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				}
			}
			$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($bill_numbers[$var]['stotal'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			$AmountDue = get_bill_balance_by_status($var, 0);
			$_tRows .= '<td class="TP3SML_NR">'.do_currency_format($AmountDue,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			$_tRows .= '</tr>'.$_nl;

		# Add row to table
			IF ($bill_numbers[$var]['scount'] || $_CCFG['SHOW_EMPTY_BILL_STATUS']) {$_table .= $_tRows;}
		}

	# Create the Totals row and close table
		$_table .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_table .= '<td class="TP3SML_BL">'.$_sp.$bill_numbers['ctotal']['scount'].$_sp.$_LANG['_CC']['lc_bills'].$_sp.$_LANG['_CC']['totalling'].'</td>'.$_nl;
		foreach($_CCFG['BILL_CYCLE'] as $key => $var) {
			IF ($bill_numbers['ctotal'][$key] || $_CCFG['SHOW_EMPTY_BILL_CYCLE']) {
				$_table .= '<td class="TP1SML_BR">';
				IF ($bill_numbers['ctotal'][$key] || $_CCFG['SHOW_BILL_ZERO_TOTAL']) {
					$_table .= do_currency_format($bill_numbers['ctotal'][$key],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				} ELSE {
					$_table .= $_sp;
				}
				$_table .= '</td>'.$_nl;
			}
		}
		$_table .= '<td class="TP3SML_BR">'.do_currency_format($bill_numbers['ctotal']['stotal'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$idata   = do_get_bill_supplier_balance(0,0);
		$_table .= '<td class="TP3SML_BR">'.do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
		$_table .= '</tr>'.$_nl;
		$_table .= '</table>'.$_nl;

	# Build form output
		$_out  = '<div align="left">';
		$_out .= '<p><b>';
		$_out .= '<a href="mod.php?mod=bills">'.$_LANG['_CC']['Bills'].'</a>';
		$_out .= $_sp.$_LANG['_CC']['By_Cycle'].$_sp.$_LANG['_CC']['Summary'];
		$_out .= ':</b></p>'.$_br.$_nl;
		$_out .= $_table.$_nl;
		$_out .= '</div>'.$_nl;

	# Return the results
		return $_out;
}


# Do summary: Bills (Single Column)
function do_summary_bills($adata) {
	# Dim some Vars:
		global $_CCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select
		$query  = 'SELECT bill_status, count(bill_id) as bill_count, sum(bill_total_cost) as bill_sum_cost, sum(bill_total_paid) as bill_sum_paid';
		$query .= ' FROM '.$_DBCFG['bills'];

	# Set the dates
		$query .= ' WHERE bill_ts >= '.$adata['report_start'].' AND bill_ts <= '.$adata['report_end'];

		$query .= ' GROUP BY bill_status';
		$query .= ' ORDER BY bill_status ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="left">'.$_nl;
		$_out .= '<table border="0" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr><td class="TP1SML_BL" colspan="7">'.$_nl;
		$_out .= '<b><a href="mod.php?mod=invoices">'.$_LANG['_CC']['Bills'].'</a>'.$_sp.$_LANG['_CC']['Summary'].':</b>'.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Process query results
		$bill_count_ttl	= 0;
		$bill_cost_ttl	= 0;
		IF ($numrows) {
			while(list($bill_status, $bill_count, $bill_sum_cost, $bill_sum_paid) = $db_coin->db_fetch_row($result)) {
				IF ($bill_count == 1) {
					$_str_02 = $_LANG['_CC']['lc_bill'];
				} ELSE {
					$_str_02 = $_LANG['_CC']['lc_bills'];
				}
				$_out .= '<tr>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_sp.$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">('.$bill_count.')</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top"><a href="mod.php?mod=bills&status='.urlencode($bill_status).'">'.$bill_status.'</a></td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_str_02.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NL" valign="top">'.$_LANG['_CC']['totalling'].$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">'.do_currency_format($bill_sum_cost, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '<td class="TP1SML_NR" valign="top">';
				IF ($bill_sum_cost != $bill_sum_paid) {
					$_out .= $_sp.do_currency_format($bill_sum_cost - $bill_sum_paid, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				}
				$_out .= '</td>'.$_nl;
				$_out .= '</tr>'.$_nl;

				$bill_count_ttl = $bill_count_ttl + $bill_count;
				$bill_cost_ttl = $bill_cost_ttl + $bill_sum_cost;
			}
		}
		$_out .= '<tr><td class="TP1SML_BR" colspan="5">'.$_nl;
		$_out .= '<b>'.$_LANG['_CC']['Total_of'].$_sp.$bill_count_ttl.'</b>'.$_sp.$_LANG['_CC']['lc_bill_s'].$_sp.$_LANG['_CC']['totalling'].':'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="1">'.$_nl;
		$_out .= do_currency_format($bill_cost_ttl, 1, 0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td></td></tr>'.$_nl;

	# Show the amount of money actually receivable, as opposed to the different invoice status
		$idata = do_get_bill_supplier_balance(0,0);
		$_out .= '<tr><td class="TP1SML_BR" colspan="5">'.$_nl;
		$_out .= '<b>' . $_LANG['_CC']['Balance_Due'] . ':</b>'.$_sp.$_nl;
		$_out .= '</td><td class="TP1SML_NR" colspan="2">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_nl;
		$_out .= '</td><td class="TP1SML_BR" colspan="4">'.$_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

	# Close the table
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

	return $_out;
}


function get_bill_balance_by_status($status, $supplier_id) {
	# Dim some vars
		global $_CCFG, $_DBCFG, $db_coin;
		$due = 0;

	# Ignore "Pending", "draft", and "void"
		IF ($status == $_CCFG['BILL_STATUS'][4]) {return 0;}
		IF ($status == $_CCFG['BILL_STATUS'][1]) {return 0;}
		IF ($status == $_CCFG['BILL_STATUS'][5]) {return 0;}

	# Set Query for select.
		$query	= 'SELECT sum(bill_total_cost), sum(bill_total_paid)';
		$query .= ' FROM '.$_DBCFG['bills'];
		$query .= " WHERE bill_status='".$db_coin->db_sanitize_data($status)."'";
		IF ($supplier_id) {$query .= ' AND bill_supplier_id='.$supplier_id;}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {

		# Process query results
			while(list($bill_total_cost, $bill_total_paid) = $db_coin->db_fetch_row($result)) {
				$due = $bill_total_cost - $bill_total_paid;
			}
		}

	# return result;
		return $due;
}

/**************************************************************
 * End Module Functions
**************************************************************/
?>