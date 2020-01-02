<?php
/**
 * Auxpage: Accounts PAyable Listing
 * 	- This auxpage will loop through all BILLS to calculate the balance due
 *	  for each SUPPLIER, then list those with a non-zero balance.
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Bills
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (!eregi('auxpage.php', $_SERVER['PHP_SELF']) && !eregi('auxpage_print.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=auxpage.php?page=ap_report');
		exit;
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_admin.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_admin_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_admin_override.php');
	}

# Initialize some output variables
	$_tstr = $_LANG['_ADMIN']['Accounts_Payable_Listing'];

# Get security vars
	$_SEC = get_security_flags();


# If user is not a logged-in admin, display error message
	IF (!$_SEC['_sadmin_flg']) {
		$_cstr = $_LANG['_BASE']['Permission_Msg'];
		echo do_mod_block_it($_tstr, $_cstr, 0, '', 1);


# Otherwise, display the content
	} ELSE {

	# Set Query for select.
		$query  = 'SELECT '.$_DBCFG['suppliers'].'.s_id, ';
		$query .= $_DBCFG['suppliers'].'.s_name_first, ';
		$query .= $_DBCFG['suppliers'].'.s_name_last, ';
		$query .= $_DBCFG['suppliers'].'.s_company, ';
		$query .= $_DBCFG['suppliers'].'.s_phone, ';
		$query .= $_DBCFG['suppliers'].'.s_email, ';
		$query .= 'sum('.$_DBCFG['bills'].'.bill_total_cost - '.$_DBCFG['bills'].'.bill_total_paid) AS balance ';
		$query .= 'FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'].' ';
		$query .= 'WHERE '.$_DBCFG['bills'].'.bill_s_id = '.$_DBCFG['suppliers'].'.s_id';

	# Block out draft [1] and void [5] and pending [4]
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][1])."'";
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][5])."'";
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][4])."'";

	# Group by clients
		$query .= 'GROUP BY '.$_DBCFG['suppliers'].'.s_id';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Start Output
		$_cstr  = '<div align="center">'.$_nl;
		IF ($_CCFG['_IS_PRINT']) {
			$_cstr .= '<h2>'.$_UVAR['CO_INFO_01_NAME'].'<br>'.$_tstr.'</h2>'.$_nl;
		}

	# Start table
		$_cstr .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;

		IF (!$_CCFG['_IS_PRINT']) {
			$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;
			$_cstr .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
			$_cstr .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl;
			$_cstr .= '<td class="TP0MED_NL">'.$_nl;
			$_cstr .= $_LANG['_ADMIN']['Accounts_Payable_Listing'].$_nl;
			$_cstr .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
		}

		$_cstr .= '<tr class="BLK_DEF_ENTRY">';
		$_cstr .= '<td class="TP3SML_BR">'.str_replace(':', '', $_LANG['_ADMIN']['l_Supplier_ID']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BL">'.str_replace(':', '', $_LANG['_ADMIN']['l_Company']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BL">'.str_replace(':', '', $_LANG['_ADMIN']['l_Full_Name']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BL">'.str_replace(':', '', $_LANG['_ADMIN']['l_Phone']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BL">'.str_replace(':', '', $_LANG['_ADMIN']['l06_Email']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BR">'.str_replace(':', '', $_LANG['_ADMIN']['l_Balance']).'</td>'.$_nl;
		IF (!$_CCFG['_IS_PRINT']) {
			$_cstr .= '<td class="TP3SML_BL">'.$_LANG['_ADMIN']['l_Action'].'</td>'.$_nl;
		}
		$_cstr .= '</tr>'.$_nl;

	# Only create rows table if there is any data
		$_rt = 0;
		$_rc = 0;
		IF ($db_coin->db_query_numrows($result)) {
			while($row = $db_coin->db_fetch_array($result)) {
				IF ($row['balance'] != 0) {
					$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
					$_cstr .= '<td class="TP3SML_NR">'.$row['s_id'].'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.($row['s_company']?$row['s_company']:$_sp).'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$row['s_name_first'].' '.$row['s_name_last'].'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.($row['s_phone']?$row['s_phone']:$_sp).'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NL">'.$row['s_email'].'</td>'.$_nl;
					$_cstr .= '<td class="TP3SML_NR">'.do_currency_format($row['balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.'</td>'.$_nl;
					IF (!$_CCFG['_IS_PRINT']) {
						$_cstr .= '<td class="TP3SML_NL">'.$_nl;
						$_cstr .= do_nav_link('admin.php?cp=suppliers&op=view&s_id='.$row['s_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
						$_cstr .= '</td>'.$_nl;
					}
					$_cstr .= '</tr>'.$_nl;
					$_rt += $row['balance'];
					$_rc++;
				}
			}
		}

	# Display Totals
		$_cstr .= '<tr class="BLK_DEF_ENTRY">';
		$_cstr .= '<td class="TP3SML_BR">'.$_rc.'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BL" colspan="2">'.$_LANG['_ADMIN']['Suppliers'].'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BR" colspan="2">'.str_replace(':', '', $_LANG['_ADMIN']['l_Balance']).'</td>'.$_nl;
		$_cstr .= '<td class="TP3SML_BR">'.do_currency_format($_rt,1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.'</td>'.$_nl;
		IF (!$_CCFG['_IS_PRINT']) {$_cstr .= '<td class="TP3SML_BR">'.$_sp.'</td>'.$_nl;}
		$_cstr .= '</tr>'.$_nl;

	# Close table
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Show "print"button
		$_mstr_flag	= 1;
		$_mstr		= do_nav_link('auxpage_print.php?page=ap_report', $_TCFG['_IMG_PRINT_M'],$_TCFG['_IMG_PRINT_M_MO'],'_new','');
		IF ($_CCFG['_IS_PRINT']) {
			$_mstr		= '';
			$_mstr_flag	= 0;
			$_tstr		= '';
		}

	# Call block it function
		echo do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
	}
?>