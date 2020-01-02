<?php
/**
 * Module: Mail (Administrative Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Mail
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_mail.php
 */


# Code to handle file being loaded by URL
	IF (eregi('mail_admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=mail');
		exit;
	}

/**************************************************************
 * Module Admin Functions
**************************************************************/
# Do Form for Mail Archive Search
function do_form_search_mail_archive($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NR" width="20%">';
		$_td_str_right	= '<td class="TP1SML_NL" width="80%">';
		$_td_str_span	= '<td class="TP1SML_BC" colspan="2">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_MAIL']['Search_Mail_Archive'];

	# Set some defaults
		IF ($adata['search_type'] == '') {$adata['search_type'] = 0;}

	# Do Main Form
		$_cstr .= '<div align="center" width="95%">'.$_nl;
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="mail">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_cstr .= '<input type="hidden" name="sw" value="'.$adata['sw'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Search_Type'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<select class="select_form" name="search_type" size="1" value="'.$adata['search_type'].'">'.$_nl;
		$_cstr .= '<option value="0"';
		IF ($adata['search_type'] == 0) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_MAIL']['AND'].'</option>'.$_nl;
		$_cstr .= '<option value="1"';
		IF ($adata['search_type'] == 1) {$_cstr .= ' selected';}
		$_cstr .= '>'.$_LANG['_MAIL']['OR'].'</option>'.$_nl;
		$_cstr .= '</select>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Date_Sent'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<input type="checkbox" NAME="cb_on_01" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_sent_ts_01'] <= 0 || $adata['s_sent_ts_01'] == '') {$adata['s_sent_ts_01'] = dt_get_uts().$_nl;}
		$_cstr .= do_date_edit_list('s_sent_ts_01', $adata['s_sent_ts_01'], 1).$_nl;
		IF ($adata['cb_and_after'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<input type="checkbox" NAME="cb_and_after" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_MAIL']['Sent_And_After'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Date_Sent'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['cb_on_02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= '<input type="checkbox" NAME="cb_on_02" value="1"'.$_set.' border="0">'.$_nl;
		IF ($adata['s_sent_ts_02'] <= 0 || $adata['s_sent_ts_02'] == '') {$adata['s_sent_ts_02'] = dt_get_uts().$_nl; }
		$_cstr .= do_date_edit_list('s_sent_ts_02', $adata['s_sent_ts_02'], 1).$_nl;
		IF ($adata['cb_and_before'] == 1) {$_set = ' CHECKED';} ELSE {$_set = '';}
		$_cstr .= $_sp.$_sp.$_nl;
		$_cstr .= '<input type="checkbox" NAME="cb_and_before" value="1"'.$_set.' border="0">'.$_nl;
		$_cstr .= $_sp.'<b>'.$_LANG['_MAIL']['Sent_And_Before'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_To'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" type="text" NAME="s_to" SIZE="30" value="'.htmlspecialchars($adata['s_to']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_From'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_from" SIZE="30" value="'.htmlspecialchars($adata['s_from']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_CC'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_cc" SIZE="30" value="'.htmlspecialchars($adata['s_cc']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_BCC'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_bcc" SIZE="30" value="'.htmlspecialchars($adata['s_bcc']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_subject" SIZE="30" value="'.htmlspecialchars($adata['s_subject']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_message" SIZE="30" value="'.htmlspecialchars($adata['s_message']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="20%">&nbsp;</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="80%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_search', 'SUBMIT', $_LANG['_MAIL']['B_Search'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_MAIL']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP05'] == 1) {
			$_cstr .= do_input_button_class_sw('b_purge', 'SUBMIT', $_LANG['_MAIL']['B_Purge_Result_Set'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_cstr .= '</div>'.$_nl;

	# Purge Result Set
		IF ($adata['mode'] == 'purge' && $adata['stage'] != 2 && ($_PERMS['AP16'] == 1 || $_PERMS['AP05'] == 1)) {
			$_cstr .= '<br>'.$_nl;
			$_cstr .= '<div align="center" width="95%">'.$_nl;
			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'?mod=mail&mode=search&sw='.$adata[sw].'">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl.$_td_str_span.$_nl.'<hr>'.$_nl.'</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_span.'<b>'.$_LANG['_MAIL']['Purge_Archive_Message_01'].'?'.$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_span.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="sw" value="'.htmlspecialchars($adata['sw']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="search_type" value="'.$adata['search_type'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="cb_on_01" value="'.$adata['cb_on_01'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_sent_ts_01" value="'.$adata['s_sent_ts_01'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="cb_and_after" value="'.$adata['cb_and_after'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="cb_on_02" value="'.$adata['cb_on_02'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_sent_ts_02" value="'.$adata['s_sent_ts_02'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="cb_and_before" value="'.$adata['cb_and_before'].'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_to" value="'.htmlspecialchars($adata['s_to']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_from" value="'.htmlspecialchars($adata['s_from']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_cc" value="'.htmlspecialchars($adata['s_cc']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_bcc" value="'.htmlspecialchars($adata['s_bcc']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_subject" value="'.htmlspecialchars($adata['s_subject']).'">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="s_message" value="'.htmlspecialchars($adata['s_message']).'">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_purge_do', 'SUBMIT', $_LANG['_MAIL']['B_Purge_Result_Set'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl.$_td_str_span.$_nl.'<hr>'.$_nl.'</td>'.$_nl.'</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_cstr .= '</div>'.$_nl;
		}

	# Search stuff and return
		$_search_cnt = 0;
		IF ($adata['search_type'] == 0) {$_search_type = 'AND';} ELSE {$_search_type = 'OR';}
		$where  = ' WHERE (';
		$where .= ' ('.$_DBCFG['mail_archive'].'.ma_id > 0) ';

		IF ($adata['s_sent_ts_01'] && $adata['cb_on_01']) {
			IF (!$adata['cb_and_after']) {
				$_ts_01_end = $adata['s_sent_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['mail_archive'].'.ma_time_stamp >= '.$adata['s_sent_ts_01'];
				$where_2 .= ' AND '.$_DBCFG['mail_archive'].'.ma_time_stamp <= '.$_ts_01_end.')';
				$_search_cnt++;
			} ELSE {
				$_ts_01_end = $adata['s_sent_ts_01']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['mail_archive'].'.ma_time_stamp >= '.$adata['s_sent_ts_01'].')';
				$_search_cnt++;
			}
		}

		IF ($adata['s_sent_ts_02'] && $adata['cb_on_02']) {
			IF (!$adata['cb_and_before']) {
				$_ts_02_end = $adata['s_sent_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['mail_archive'].'.ma_time_stamp >= '.$adata['s_sent_ts_02'];
				$where_2 .= ' AND '.$_DBCFG['mail_archive'].'.ma_time_stamp <= '.$_ts_02_end.')';
				$_search_cnt++;
			} ELSE {
				$_ts_02_end = $adata['s_sent_ts_02']+86399;
				IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
				$where_2 .= '('.$_DBCFG['mail_archive'].'.ma_time_stamp <= '.$_ts_02_end.')';
				$_search_cnt++;
			}
		}

		IF ($adata['s_to']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_recip like '%".$db_coin->db_sanitize_data($adata['s_to'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_from']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_from like '%".$db_coin->db_sanitize_data($adata['s_from'])."%')";
			$_search_cnt++;
		}

 		IF ($adata['s_cc']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_cc like '%".$db_coin->db_sanitize_data($adata['s_cc'])."%')";
			$_search_cnt++;
		}

 		IF ($adata['s_bcc']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_bcc like '%".$db_coin->db_sanitize_data($adata['s_bcc'])."%')";
			$_search_cnt++;
		}

 		IF ($adata['s_subject']) {
			IF ($_search_cnt > 0) {$where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_subject like '%".$db_coin->db_sanitize_data($adata['s_subject'])."%')";
			$_search_cnt++;
		}

		IF ($adata['s_message']) {
			IF ($_search_cnt > 0) { $where_2 .= ' '.$_search_type.' ';}
			$where_2 .= '('.$_DBCFG['mail_archive'].".ma_fld_message like '%".$db_coin->db_sanitize_data($adata['s_message'])."%')";
			$_search_cnt++;
		}

		IF ($_search_cnt > 0) {$where .= ' AND ( '.$where_2.' )';}
		$where .= ')';

	# Purge Result Set
		IF ($adata['mode'] == 'purge' && $adata['stage'] == 2) {
			$query  = 'DELETE FROM '.$_DBCFG['mail_archive'];
			$query .= $where;

		# Do select / form if criteria entered
			IF ($_search_cnt > 0) {
				$result	= $db_coin->db_query_execute($query);
				$eff_rows	= $db_coin->db_query_affected_rows();

			# Build form output
				$_cstr .= '<br>'.$_nl;
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE">'.'<td class="TP3MED_BC">'.$_nl;
				$_cstr .= $_LANG['_MAIL']['Purge_Archive_Results'].$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC"><p><p>'.$_LANG['_MAIL']['Purge_Archive_Message_02'].' :'.$eff_rows.'<p><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;

			# Clear search count to bypass below
				$_search_cnt = 0;
			}

		} ELSE {
			$query  = 'SELECT *';
			$query .= ' FROM '.$_DBCFG['mail_archive'];
			$query .= $where;
			$query .= ' ORDER BY ma_time_stamp DESC';

		# Do select / form if criteria entered
			IF ($_search_cnt > 0) {
				$result	= $db_coin->db_query_execute($query);
				$numrows	= $db_coin->db_query_numrows($result);

			# Build form output
				$_cstr .= '<br>'.$_nl;
				$_cstr .= '<div align="center">'.$_nl;
				$_cstr .= '<table width="95%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
				$_cstr .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="4">'.$_nl;
				$_cstr .= '<b>'.$_LANG['_MAIL']['Found_Items'].$_sp.'('.$numrows.')</b><br>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
			}
		}

	# Process query results
		IF ($numrows && $_search_cnt > 0) {
			$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		#	$_cstr .= '<td class="TP3SML_NC" valign="top"><b>'.$_LANG['_MAIL']['l_Id'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" valign="top"><b>'.$_LANG['_MAIL']['l_Date_Sent'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NR" valign="top"><b>'.''.$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NL" valign="top"><b>'.''.$_sp.'</b></td>'.$_nl;
			$_cstr .= '<td class="TP3SML_NC" valign="top"><b>'.$_LANG['_MAIL']['l_Actions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			while ($row = $db_coin->db_fetch_array($result)) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			#	$_cstr .= '<td class="TP3SML_NC" valign="top">'.$row[ma_id].'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" valign="top">'.dt_make_datetime($row['ma_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NR" valign="top">';
				$_cstr .= '<b>'.$_LANG['_MAIL']['l_To'].$_sp.'</b>'.$_nl;
				$_cstr .= '<br>'.'<b>'.$_LANG['_MAIL']['l_From'].$_sp.'</b>'.$_nl;
				$_cstr .= '<br>'.'<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b>'.$_nl;
				$_cstr .= '<br>'.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NL" valign="top">';
				$_cstr .= htmlspecialchars($row['ma_fld_recip'], ENT_QUOTES).$_nl;
				$_cstr .= '<br>'.htmlspecialchars($row['ma_fld_from'], ENT_QUOTES).$_nl;
				$_cstr .= '<br>'.$row['ma_fld_subject'].$_nl;
				$_cstr .= '<br>'.'</td>'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" valign="top">'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=resend&obj=arch&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=view&obj=arch&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_cstr .= do_nav_link('mod_print.php?mod=mail&mode=view&obj=arch&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP05'] == 1)) {
						$_cstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=delete&obj=arch&stage=2&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
				}
				$_cstr .= '</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
			}

			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_cstr .= '<br>'.$_nl;

		} ELSE {
			IF ($_search_cnt > 0) {
				$_cstr .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_cstr .= '<td class="TP3SML_NC" colspan="4"><p><p><b>'.$_LANG['_MAIL']['No_Items_Found'].'</b><p></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</div>'.$_nl;
				$_cstr .= '<br>'.$_nl;
			}
		}

		$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function do_resend_entry_mail_archive($adata, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_colsp2J	= '<td class="TP1SML_NJ" colspan="2">';
		$_td_str_colsp2C	= '<td class="TP1SML_NC" colspan="2">';
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build query and execute
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['mail_archive'];
		$query .= ' WHERE '.$_DBCFG['mail_archive'].'.ma_id='.$adata['ma_id'];

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set eMail Parameters (pre-eval template, some used in template)
				$mail['recip']		= $row['ma_fld_recip'];
				$mail['from']		= $row['ma_fld_from'];
				$mail['cc']		= $row['ma_fld_cc'];
				$mail['bcc']		= $row['ma_fld_bcc'];
				$mail['subject']	= $row['ma_fld_subject'];
				$mail['message']	= $row['ma_fld_message'];

			# Clear cc and bcc is specified in admin parameters
				IF ($_CCFG['no_cc_on_resend']) {
					$mail['cc']	= $row['ma_fld_cc'];
					$mail['bcc']	= $row['ma_fld_bcc'];
				}

			# Call basic email function (ret=0 on error)
				$_ret = do_mail_basic($mail);

			# Check return
				IF ($_ret) {
					$_ret_msg  = $_LANG['_MAIL']['Resend_Archive_Entry_Message_03_L1'];
					$_ret_msg .= '<br>'.$_LANG['_MAIL']['Resend_Archive_Entry_Message_03_L2'];
				} ELSE {
					$_ret_msg = $_LANG['_MAIL']['Resend_Archive_Entry_Message_02'];
				}

			# Build Title String, Content String, and Footer Menu String
				$_tstr .= '<table width="100%">'.$_nl;
				$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
				$_tstr .= '<td class="TP3MED_BL">'.htmlspecialchars($row['ma_fld_recip'], ENT_QUOTES).'</td>'.$_nl;
				$_tstr .= '<td class="TP3MED_BR">'.dt_make_datetime($row['ma_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
				$_tstr .= '</tr>'.$_nl;
				$_tstr .= '</table>'.$_nl;

				$_cstr .= '<table width="100%">'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl.$_td_str_colsp2C.$_nl;
				$_cstr .= '<hr>'.$_nl;
				$_cstr .= '<b>'.$_LANG['_MAIL']['Resend_Archive_Entry_Results'].'</b>'.$_nl;
				$_cstr .= '<br>'.$_ret_msg.$_nl;
				$_cstr .= '<hr>'.$_nl;
				$_cstr .= '</td>'.$_nl.'</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_To'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($row['ma_fld_recip'], ENT_QUOTES).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_CC'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($row['ma_fld_cc'], ENT_QUOTES).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_BCC'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($row['ma_fld_bcc'], ENT_QUOTES).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_From'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.htmlspecialchars($row['ma_fld_from'], ENT_QUOTES).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$row['ma_fld_subject'].'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;

				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'('.$_LANG['_MAIL']['output_below'].')</b></td>'.$_nl;
				$_cstr .= $_td_str_right.$_sp.'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_colsp2J.'<hr></td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '<tr valign="bottom">'.$_nl;
				$_cstr .= $_td_str_colsp2J.nl2br(htmlspecialchars($row['ma_fld_message'])).'</td>'.$_nl;
				$_cstr .= '</tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;

				$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
				$_out .= '<br>'.$_nl;
			}
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Admin Functions
**************************************************************/
?>