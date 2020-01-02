<?php
/**
 * Module: SiteInfo Pages (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage SiteInfo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('siteinfo_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=siteinfo');
	}


/**
 * Build MySQL query for products comparison table
 * @return string Complete database query for plans info
 */
function do_build_products_comparison_query() {
	# Initialize soem variables
		global $_CCFG, $_DBCFG;

	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);
		$_GROUPS	= do_decode_groups_user($_SEC['_suser_groups']);

	# Set Query for select.
		$query  = 'SELECT * FROM ' . $_DBCFG['products'];
		$query .= ' WHERE prod_status=1';
		$query .= ' AND (prod_dom_type=2 OR prod_dom_type=5)';		// lang_config.php $_CCFG['DOM_TYPE'] 1 and 4 are "hosted"
		IF (!$_SEC['_sadmin_flg'] && !$group)	{$query .= ' AND prod_client_scope=0';}
		IF ($group)						{$query .= ' AND prod_cg_0'.$group.'=1';}

	# Seperate free vs paid products
		IF ($_CCFG['_FREETRIAL']) {
			$query .= ' AND prod_unit_cost=0';
		} ELSE {
			$query .= ' AND prod_unit_cost>0';
		}

	# Allow for groups
		IF ($_SEC['_suser_flg']) {
			$query .= " OR prod_client_scope = -1";
			$query .= ' OR prod_client_scope = '.$_SEC['_suser_id'];
			IF ($_GROUPS['UG08'] == 1) {$query .= " OR prod_cg_08 = 1";}
			IF ($_GROUPS['UG07'] == 1) {$query .= " OR prod_cg_07 = 1";}
			IF ($_GROUPS['UG06'] == 1) {$query .= " OR prod_cg_06 = 1";}
			IF ($_GROUPS['UG05'] == 1) {$query .= " OR prod_cg_05 = 1";}
			IF ($_GROUPS['UG04'] == 1) {$query .= " OR prod_cg_04 = 1";}
			IF ($_GROUPS['UG03'] == 1) {$query .= " OR prod_cg_03 = 1";}
			IF ($_GROUPS['UG02'] == 1) {$query .= " OR prod_cg_02 = 1";}
			IF ($_GROUPS['UG01'] == 1) {$query .= " OR prod_cg_01 = 1";}
		}

	# Set sort order based on config
		switch($_CCFG['ORDERS_PROD_LIST_SORT_ORDER']) {
			case "0":
				$query .= " ORDER BY prod_id ASC";
				break;
			case "1":
				$query .= " ORDER BY prod_name ASC";
				break;
			case "2":
				$query .= " ORDER BY prod_desc ASC";
				break;
			case "3":
				$query .= " ORDER BY prod_unit_cost ASC";
				break;
			default:
				$query .= " ORDER BY prod_name ASC";
				break;
		}

	# Return results;
		return $query;
}


/**
 * Display a vertical listing of hosting plans
 * @return string Complete html for vertical table of plans info
 */
function show_hosting_plans_vertical() {
	# grab some globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_out = '';

	# Grab the products from the database
		$query	= do_build_products_comparison_query();
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_affected_rows();

	# If there are any "hosting" products,
		IF ($numrows)  {

		# Do our table headers
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" align="left" valign="top">'.$_nl;
			$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE">'.$_nl;
			$_out .= '<td class="TP3MED_BC" align="center" colspan="9"><b>'.$_LANG['_SITEINFO']['Compare_Plans'].'</b></td>'.$_nl;
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Plan'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Domains'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Sub-Domains'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Disk_MB'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Bandwidth'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Emails'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Databases'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BR">'.$_LANG['_SITEINFO']['Cost'].'</td>'.$_nl;
			$_out .= '<td class="TP3SML_BC">'.$_LANG['_SITEINFO']['Action'].'</td>'.$_nl;
			$_out .= '</tr>';

		# Loop through the data and print out the table row
			while ($row = $db_coin->db_fetch_array($result)) {

			# Remove first part of description IF sub-grouping
				$pieces			= explode(' ', $row['prod_desc']);
				$v_new_product		= str_replace(':', '', $pieces[0]);
				$row['prod_desc']	= str_replace($v_new_product.' - ', '', $row['prod_desc']);
				$row['prod_desc']	= str_replace($v_new_product.': ', '', $row['prod_desc']);

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">' . $row['prod_desc'] . '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_domains'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_domains'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_domains'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_subdomains'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_subdomains'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_subdomains'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_disk_space_mb'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_disk_space_mb'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_disk_space_mb'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_traffic_mb'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_traffic_mb'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_traffic_mb'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_mailboxes'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_mailboxes'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_mailboxes'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NC">';
				IF ($row['prod_allow_databases'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($row['prod_allow_databases'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $row['prod_allow_databases'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['prod_unit_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC"><a href="mod.php?mod=orders&ord_prod_id='.$row['prod_id'].'"><img src="coin_images/sign_up_title.gif" width="57" height="15" border="0" alt="'.$_LANG['_SITEINFO']['Order'].'" title="'.$_LANG['_SITEINFO']['Order'].'"></a></td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}

		# Close the tables
			IF ($_CCFG['ORDERS_COR_ENABLE']) {
				$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_BC" align="center" colspan="9">'.$_LANG['_SITEINFO']['Use_COR'].'</td></tr>'.$_nl;
			}
			$_out .= '</table></td></tr></table>';
		}

	# Return results
		return $_out;
}


/**
 * Display a horizontal listing of hosting plans
 *	- This function is based on the ideas and code of Jay Cooper (http://www.phatpixel.com)
 * @return string Complete html for horizontal table of plans info
 */
function show_hosting_plans_horizontal() {
	# Grab some globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_out = '';

	# Grab the products from the database
		$query	= do_build_products_comparison_query();
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_affected_rows();

	# If there are any "hosting" products,
		IF ($numrows) {

		# Loop through and store the data
			$hosting = array();
			$t1=0;
			while($row = $db_coin->db_fetch_array($result)) {
				foreach ($row as $key=>$value) {$hosting[$t1][$key] = $value;}
				$t1++;
			}
			$span = count($hosting);

		# Do our table headers
			$_out .= '<table border="0" cellpadding="5" cellspacing="1" width="100%">'.$_nl;
			$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY" align="left" valign="top">'.$_nl;
			$_out .= '<table width="100%" border="0" bordercolor="' . $_TCFG['_TAG_TABLE_BRDR_COLOR'] . '" bgcolor="' . $_TCFG['_TAG_TRTD_BKGRND_COLOR'] . '" cellpadding="0" cellspacing="1">'.$_nl;
			$_out .= '<tr class="BLK_DEF_TITLE">'.$_nl;
			$_out .= '<td class="TP3MED_BC" align="center" colspan="'.($span+1).'"><b>'.$_LANG['_SITEINFO']['Compare_Plans'].'</b></td>'.$_nl;
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td width="'.round((100/($span+2)*2),1).'%">&nbsp;</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {

			# Remove first part of description IF sub-grouping
				$pieces					= explode(' ', $hosting[$c]['prod_desc']);
				$v_new_product				= str_replace(':', '', $pieces[0]);
				$hosting[$c]['prod_desc']	= str_replace($v_new_product.' - ', '', $hosting[$c]['prod_desc']);
				$hosting[$c]['prod_desc']	= str_replace($v_new_product.': ', '', $hosting[$c]['prod_desc']);

				$_out .= '<td class="TP3SML_BC" width="'.round(100/($span+2),1).'%">'.$hosting[$c]['prod_desc'].'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Domains'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_domains'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_domains'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_domains'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Sub-Domains'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_subdomains'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_subdomains'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_subdomains'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Disk_MB'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				IF (is_numeric($hosting[$c]['prod_allow_disk_space_mb'])) $hosting[$c]['prod_allow_disk_space_mb'] = number_format($hosting[$c]['prod_allow_disk_space_mb']);
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_disk_space_mb'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_disk_space_mb'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_disk_space_mb'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Bandwidth'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				IF (is_numeric($hosting[$c]['prod_allow_traffic_mb'])) $hosting[$c]['prod_allow_traffic_mb'] = number_format($hosting[$c]['prod_allow_traffic_mb']);
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_traffic_mb'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_traffic_mb'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_traffic_mb'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Emails'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				IF (is_numeric($hosting[$c]['prod_allow_mailboxes'])) $hosting[$c]['prod_allow_mailboxes'] = number_format($hosting[$c]['prod_allow_mailboxes']);
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_mailboxes'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_mailboxes'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_mailboxes'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Databases'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				IF (is_numeric($hosting[$c]['prod_allow_databases'])) $hosting[$c]['prod_allow_databases'] = number_format($hosting[$c]['prod_allow_databases']);
				$_out .= '<td class="TP3SML_NC">';
				IF ($hosting[$c]['prod_allow_databases'] == 0) {
					$_out .= $_LANG['_SITEINFO']['Not_Applicable'];
				} ELSEIF ($hosting[$c]['prod_allow_databases'] == '-1') {
					$_out .= $_LANG['_SITEINFO']['Unlimited'];
				} ELSE {
					$_out .= $hosting[$c]['prod_allow_databases'];
				}
				$_out .= '</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Cost'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++) {
				$_out .= '<td class="TP3SML_NC">'.do_currency_format($hosting[$c]['prod_unit_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).'</td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

			$_out .= '<tr class="BLK_DEF_ENTRY" valign="top">'.$_nl;
			$_out .= '<td class="TP3SML_BL">'.$_LANG['_SITEINFO']['Action'].'</td>'.$_nl;
			for ($c=0; $c<$span; $c++){
				$_out .= '<td class="TP3SML_NC"><A HREF="mod.php?mod=orders&amp;ord_prod_id='.$hosting[$c]['prod_id'].'"><img src="coin_images/sign_up_title.gif" width="57" height="15" border="0" alt="'.$_LANG['_SITEINFO']['Order'].'" title="'.$_LANG['_SITEINFO']['Order'].'"></a></td>'.$_nl;
			}
			$_out .= '</tr>'.$_nl;

		# Close the tables
			IF ($_CCFG['ORDERS_COR_ENABLE']) {
				$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_BC" colspan="'.($span+1).'">'.$_LANG['_SITEINFO']['Use_COR'].'</td></tr>'.$_nl;
			}
			$_out .= '</table></td></tr></table>';
		}

	# Return results
		return $_out;
}


function show_form_whois() {
	# Grab some globals
		global $_DBCFG, $db_coin, $_LANG, $_CCFG, $_TCFG, $_nl, $_sp;
		$stdcolor		= 'black';	// header and footer font color
		$Lookup_Domain	= array();
		$_out		= '';
		$atype		= 'com';
		$xx			= 0;

	# Buold array of TLDs
		$query	= 'SELECT * FROM '.$_DBCFG['whois'].' WHERE whois_include=1 ORDER BY whois_display ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		while(list($whois_id, $whois_server, $whois_nomatch, $whois_value, $whois_display, $whois_include, $whois_prod_id, $whois_notes) = $db_coin->db_fetch_row($result)) {
			$Lookup_Domain['server'][$xx]		= $whois_server;	// server to lookup for domain name
			$Lookup_Domain['nomatch'][$xx]	= $whois_nomatch;	// string returned by server if the domain is not found
			$Lookup_Domain['value'][$xx]		= $whois_value;	// string value for this domain extension (do not change)
			$Lookup_Domain['display'][$xx]	= $whois_display;	// string value for this domain to display on form
			$Lookup_Domain['include'][$xx]	= $whois_include;	// include this domain in lookup
			$Lookup_Domain['prod_id'][$xx]	= $whois_prod_id;	// over-ride product ordered with this id
			$xx++;                                              	// Increment counter
		}
		$Lookups = $xx;

	# Start form if any TLDs configured
		IF ($Lookups) {
			$_out .= '<form method="post" action="mod.php">'.$_nl;
			IF ($_CCFG['_FREETRIAL']) {$_out .= '<input type="hidden" id="free" name="free" value="1">'.$_nl;}
			$_out .= '<input type="hidden" name="mod" value="whois">'.$_nl;
			$_out .= '<input type="hidden" name="action" value="checkdom">'.$_nl;
			$_out  .= $_LANG['_SITEINFO']['Title_Domain'].':'.$_sp.$_nl;
			$_out .= '<input class="PSML_NL" type="text" name="ord_domain" size="25" value="" maxlength="58">'.$_nl;

		# Build extensions as select list
			$_out .= $_sp.$_sp.'<select class="PMED_NL" name="type" size="1" value="'.$atype.'">'.$_nl;
			# Loop through for domains list
			FOR ($i=0; $i <= $Lookups; $i++) {
				IF ($Lookup_Domain['include'][$i] == true) {
					$_cnt++;
					$_out .= '<option value="'.$Lookup_Domain['value'][$i].'"';
					IF ($atype == $Lookup_Domain['value'][$i]) {$_out .= ' selected';}
					$_out .= '>'.$Lookup_Domain['display'][$i].'</option>'. $_nl;
				}
			}
			IF ($_cnt > 0) {
				$_cnt = 0;
				$_out .= '<option value="all"';
				IF ($atype == 'all') {$_out .= ' selected';}
				$_out .= '>'.$_LANG['_SITEINFO']['Option_All_Domains'].'</option>'. $_nl;
			}
			$_out .= '</select>'.$_nl;

		# Close form
			$_out .= '<input class="PSML_NC" type="submit" name="button" value="'.$_LANG['_SITEINFO']['B_Check'].'">';
			$_out .= '</form>'.$_nl;
		}

	# Return results
		return $_out;
}
?>