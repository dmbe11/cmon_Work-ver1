<?php
/**
 * Module: Clients (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Clients
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_clients.php
 */


# Code to handle file being loaded by URL
	IF (eregi('clients_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=clients');
		exit;
	}


/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do list field form for: Clients
function do_select_form_clients($aaction, $aname, $avalue, $aret_flag=0) {
	# Example
	# Call function for create select form: Vendors
	#	$aaction = $_SERVER["PHP_SELF"].'?mod=clients&op=edit';
	#	$aname	= "cl_id";
	#	$avalue	= $cl_id;
	#	$_cstr .= do_select_form_clients($aaction, $aname, $avalue, '1');

	# Dim some Vars:
		global $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT ';
		$query	.= 'cl_id, cl_join_ts, cl_status, cl_company';
		$query	.= ', cl_name_first, cl_name_last, cl_user_name, cl_notes';
		$query	.= ' FROM '.$_DBCFG['clients'].' ORDER BY cl_id ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3MED_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_CLIENTS']['Client_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3MED_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">Please Select</option>'.$_nl;

	# Process query results
		while(list($cl_id, $cl_join_ts, $cl_status, $cl_company, $cl_name_first, $cl_name_last, $cl_user_name, $cl_notes) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$cl_id.'">'.$cl_id.' - '.$cl_name_last.', '.$cl_name_first.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3MED_NC">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_CLIENTS']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do list field form for: Clients
function do_select_listing_clients($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_GPV, $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_ptot	= 0;
		$_where	= '';
		$_out	= '';
		$_ps		= '';
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {$_ps .= '&status='.$_GPV['status'];}
		IF ($_GPV['notstatus']) {$_ps .= '&notstatus='.$_GPV['notstatus'];}


	# Set Query for select.
		$query = 'SELECT * FROM '.$_DBCFG['clients'];

	# Set Filters
		IF (!$adata['fb'])		{$adata['fb'] = '';}
		IF ($adata['fb'] == '1')	{$_where .= ' WHERE '.$_DBCFG['clients'].".cl_status='".$adata['fs']."'";}

	# Show only selected status clients
		IF ($_GPV['status'] && $_GPV['status'] != 'all') {
			IF ($_where) {$_where .= ' AND ';} ELSE {$_where .= ' WHERE ';}
			$_where .= $_DBCFG['clients'].".cl_status='".$db_coin->db_sanitize_data($_GPV['status'])."'";
		}
		IF ($_GPV['notstatus']) {
			IF ($_where) {$_where .= ' AND ';} ELSE {$_where .= ' WHERE ';}
			$_where .= $_DBCFG['clients'].".cl_status != '".$db_coin->db_sanitize_data($_GPV['notstatus'])."'";
		}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'A';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])		{$adata['sb'] = '4';}
		IF ($adata['sb'] == '1')	{$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_id'.$order_AD;}
		IF ($adata['sb'] == '2')	{$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_status'.$order_AD;}
		IF ($adata['sb'] == '3')	{$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_join_ts'.$order_AD;}
		IF ($adata['sb'] == '4')	{
			IF ($_CCFG['CLIENT_LIST_DISPLAY'] < 3) {
				$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_name_last'.$order_AD.', '.$_DBCFG['clients'].".cl_name_first".$order_AD;
			} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 3) {
				$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_user_name'.$order_AD;
			} ELSE {
				$_order  = ' ORDER BY '.$_DBCFG['clients'].'.cl_company'.$order_AD;
				$_order .= ', '.$_DBCFG['clients'].'.cl_name_last'.$order_AD.', '.$_DBCFG['clients'].".cl_name_first".$order_AD;
			}
		}
		IF ($adata['sb'] == '5')	{
			IF ($_CCFG['CLIENT_LIST_DISPLAY'] == 1 || $_CCFG['CLIENT_LIST_DISPLAY'] == 4) {
				$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_user_name'.$order_AD;
			} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 5) {
				$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_name_last'.$order_AD.', '.$_DBCFG['clients'].".cl_name_first".$order_AD;
			} ELSE {
				$_order = ' ORDER BY '.$_DBCFG['clients'].'.cl_email'.$order_AD;
			}
		}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb= '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso= '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb= '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs= '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['clients'];
		$query_ttl .= $_where;

		$result_ttl= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next	- is page loading first record number
	# $_rec_start	- is a given page start record (which will be rec_next)
		$_rec_page	= $_CCFG['IPP_CLIENTS'];
		$_rec_next	= $adata['rec_next'];
		IF (!$_rec_next) {$_rec_next=0;}

	# Range of records on current page
		$_rec_next_lo = $_rec_next+1;
		$_rec_next_hi = $_rec_next+$_rec_page;
		IF ($_rec_next_hi > $numrows_ttl) {$_rec_next_hi = $numrows_ttl;}

	# Calc no pages,
		$_num_pages = round(($numrows_ttl/$_rec_page), 0);
		IF ($_num_pages < ($numrows_ttl/$_rec_page)) {$_num_pages = $_num_pages+1;}

	# Loop Array and Print Out Page Menu HTML
		$_page_menu = $_LANG['_CLIENTS']['l_Pages'].$_sp;
		for ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = (($i*$_rec_page)-$_rec_page);
			IF ($_rec_start == $_rec_next) {
			# Loading Page start record so no link for this page.
				$_page_menu .= "$i";
			} ELSE {
				$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=clients'.$_link_xtra.$_ps.'&rec_next='.$_rec_start.'">'.$i.'</a>';
			}
			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="'.$_SERVER["PHP_SELF"].'?mod=clients&sb=';
		$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&fc='.$adata['fc'].'&rec_next='.$_rec_next.$_ps.'">';

		$_hdr_link_1 = $_LANG['_CLIENTS']['l_Id'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_2 = $_LANG['_CLIENTS']['l_Status'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_3 = $_LANG['_CLIENTS']['l_Join_Date'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		IF ($_CCFG['CLIENT_LIST_DISPLAY'] < 3) {
			$_hdr_link_4 = $_LANG['_CLIENTS']['l_Full_Name'].$_sp.'<br>';
		} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 3) {
			$_hdr_link_4 = $_LANG['_CLIENTS']['l_User_Name'].$_sp.'<br>';
		} ELSE {
			$_hdr_link_4 = $_LANG['_CLIENTS']['l_Company'].$_sp.'<br>';
		}
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		IF ($_CCFG['CLIENT_LIST_DISPLAY'] == 1 || $_CCFG['CLIENT_LIST_DISPLAY'] == 4) {
			$_hdr_link_5 = $_LANG['_CLIENTS']['l_User_Name'].$_sp.'<br>';
		} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 5) {
			$_hdr_link_5 = $_LANG['_CLIENTS']['l_Full_Name'].$_sp.'<br>';
		} ELSE {
			$_hdr_link_5 = $_LANG['_CLIENTS']['l_Email_Address'].$_sp.'<br>';
		}
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_6 .= $_LANG['_CLIENTS']['l_Balance'].$_sp.'<br>';

	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
			$_out .= '<td>&nbsp;[<a href="mod.php?mod=clients&mode=none&status=all'.$_link_xtra;
			$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</td>';
			for ($i=1; $i< sizeof($_CCFG['CL_STATUS']); $i++) {
				$_out .= '<td align="right"><nobr>&nbsp;[<a href="mod.php?mod=clients&mode=none&status='.$_CCFG['CL_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['CL_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
			$_out .= '<td>&nbsp;</td>';
			for ($i=1; $i< sizeof($_CCFG['CL_STATUS']); $i++) {
				$_out .= '<td><nobr>&nbsp;[<a href="mod.php?mod=clients&mode=none&notstatus='.$_CCFG['CL_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['CL_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr></table>';
			$_out .= '<br><br>';
		}

	# Build form output
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_CLIENTS']['Clients'].':'.$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_CLIENTS']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_CLIENTS']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($_SEC['_sadmin_flg']) {
				$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=cc&mode=search&sw=clients', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
			}
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_1.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_2.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC" valign="top">'.$_hdr_link_3.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_4.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_5.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR" valign="top">'.$_hdr_link_6.'</td>'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '<td class="TP3SML_BL" valign="top">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['cl_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['cl_status'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['cl_join_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;

				$_out .= '<td class="TP3SML_NL">';
				IF ($_CCFG['CLIENT_LIST_DISPLAY'] < 3) {
					$_out .= $row['cl_name_last'].', '.$row['cl_name_first'];
				} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 3) {
					$_out .= $row['cl_user_name'];
				} ELSE {
					$_out .= $row['cl_company'];
				}
				$_out .= '</td>'.$_nl;

				$_out .= '<td class="TP3SML_NL">';
				IF ($_CCFG['CLIENT_LIST_DISPLAY'] == 1 || $_CCFG['CLIENT_LIST_DISPLAY'] == 4) {
					$_out .= $row['cl_user_name'];
				} ELSEIF ($_CCFG['CLIENT_LIST_DISPLAY'] == 5) {
					$_out .= $row['cl_name_last'].', '.$row['cl_name_first'];
				} ELSE {
					$_out .= $row['cl_email'];
				}
				$_out .= '</td>'.$_nl;

				$idata = do_get_invc_cl_balance($row['cl_id']);
				$_ptot += $idata['net_balance'];
				$_out .= '<td class="TP3SML_NR">';
				IF ($idata['net_balance']) {$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);}
				$_out .= $_sp.'</td>'.$_nl;

				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
					$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=view&cl_id='.$row['cl_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					IF (strtolower($row['cl_email']) != 'none') {
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=client&cc_cl_id='.$row['cl_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					} ELSE {
						$_out .= $_TCFG['_IMG_BLANK_S'];
					}
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP07'] == 1) {
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=edit&cl_id='.$row['cl_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
						$_out .= do_nav_link($_SERVER["PHP_SELF"].'?mod=clients&mode=delete&stage=1&cl_id='.$row['cl_id'].'&cl_name_first='.$row['cl_name_first'].'&cl_name_last='.$row['cl_name_last'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
					$_out .= '</nobr></td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

	# Show totals footer row(s)
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
		$_out .= str_replace(':', '', $_LANG['_CLIENTS']['Page_Balance']).$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($_ptot, 1, 0, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		$_out .= '</td><td class="TP3SML_BL" colspan="2">'.$_nl;
		$_out .= $_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

		IF ($_num_pages > 1) {
			$idata = do_get_invc_cl_balance(0);
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= '<td class="TP3SML_BR" colspan="5">'.$_nl;
			$_out .= $_LANG['_CLIENTS']['Overall_Balance'].$_nl;
			$_out .= '</td><td class="TP3SML_BR">'.$_nl;
			$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
			$_out .= '</td><td class="TP3SML_BL" colspan="2">'.$_nl;
			$_out .= $_sp.$_nl;
			$_out .= '</td></tr>'.$_nl;
		}

		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>