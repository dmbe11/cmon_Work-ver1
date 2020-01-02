<?php
/**
 * Module: FAQ (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage FAQ
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_faq.php
 */


# Code to handle file being loaded by URL
	IF (eregi('faq_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=faq');
		exit;
	}

/**************************************************************
 * Module Functions
**************************************************************/
# Do Data Input Validate
function do_input_validation($_GPV) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
		IF ($_GPV['obj'] == 'faq') {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV['faq_id'])			{$err_entry['flag'] = 1; $err_entry['faq_id'] = 1;}
			IF (!$_GPV['faq_position'])		{$err_entry['flag'] = 1; $err_entry['faq_position'] = 1;}
		#	IF (!$_GPV['faq_time_stamp_mod'])	{$err_entry['flag'] = 1; $err_entry['faq_time_stamp_mod'] = 1;}
		#	IF (!$_GPV['faq_status'])		{$err_entry['flag'] = 1; $err_entry['faq_status'] = 1;}
		#	IF (!$_GPV['faq_admin'])			{$err_entry['flag'] = 1; $err_entry['faq_admin'] = 1;}
		#	IF (!$_GPV['faq_user'])			{$err_entry['flag'] = 1; $err_entry['faq_user'] = 1;}
			IF (!$_GPV['faq_title'])			{$err_entry['flag'] = 1; $err_entry['faq_title'] = 1;}
			IF (!$_GPV['faq_descrip'])		{$err_entry['flag'] = 1; $err_entry['faq_descrip'] = 1;}
		}

		IF ($_GPV['obj'] == 'faqqa') {
		# Check required fields (err / action generated later in cade as required)
		#	IF (!$_GPV['faqqa_id'])				{$err_entry['flag'] = 1; $err_entry['faqqa_id'] = 1;}
			IF (!$_GPV['faqqa_faq_id'])			{$err_entry['flag'] = 1; $err_entry['faqqa_faq_id'] = 1;}
			IF (!$_GPV['faqqa_position'])			{$err_entry['flag'] = 1; $err_entry['faqqa_position'] = 1;}
		#	IF (!$_GPV['faqqa_time_stamp_mod'])	{$err_entry['flag'] = 1; $err_entry['faqqa_time_stamp_mod'] = 1;}
		#	IF (!$_GPV['faqqa_status'])			{$err_entry['flag'] = 1; $err_entry['faqqa_status'] = 1;}
			IF (!$_GPV['faqqa_question'])			{$err_entry['flag'] = 1; $err_entry['faqqa_question'] = 1;}
			IF (!$_GPV['faqqa_answer'])			{$err_entry['flag'] = 1; $err_entry['faqqa_answer'] = 1;}
		}

		return $err_entry;
}


function do_get_next_faq_pos() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(faq_position) FROM '.$_DBCFG['faq'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_faq_pos) = $db_coin->db_fetch_row($result)) {$max_faq_pos = $_max_faq_pos;}

	# Check / Set Value for return
		IF (!$max_faq_pos) {return 0;} ELSE {return $max_faq_pos+1;}
}


function do_get_next_faqqa_pos($afaq_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(faqqa_position) FROM '.$_DBCFG['faq_qa'].' WHERE faqqa_faq_id='.$afaq_id;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_faqqa_pos) = $db_coin->db_fetch_row($result)) {$max_faqqa_pos = $_max_faqqa_pos;}

	# Check / Set Value for return
		IF (!$max_faqqa_pos) {return 0;} ELSE {return $max_faqqa_pos+1;}
}


# Do display entry (individual faq / faqqa entry)
function do_display_entry($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Check object
		IF ($adata['obj'] == 'faq') {
		# Build Title String, Content String, and Footer Menu String
			$_tstr  = '<table width="100%">'.$_nl;
			$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
			$_tstr .= '<td class="TP0MED_BL">'.$adata['faq_title'].'</td>'.$_nl;
			$_tstr .= '<td class="TP0MED_NR">'.dt_make_datetime($adata['faq_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
			$_tstr .= '</tr>'.$_nl;
			$_tstr .= '<tr valign="bottom">'.$_nl;
			$_tstr .= '<td class="TP0MED_NL">'.$_sp.'</td>'.$_nl;
			$_tstr .= '<td class="TP0MED_NR">'.$_sp.'</td>'.$_nl;
			$_tstr .= '</tr>'.$_nl;
			$_tstr .= '</table>'.$_nl;

			$_cstr = $adata['faq_descrip'].'<br><br>'.$_nl;

			IF ($_SEC['_sadmin_flg']) {
				$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faq&faq_id='.$adata['faq_id'], $_TCFG['_IMG_FAQ_EDIT_FAQ_M'],$_TCFG['_IMG_FAQ_EDIT_FAQ_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
				}
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

			} ELSE {
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
			}
		}

		IF ($adata['obj'] == 'faqqa') {

		# Check for google adsense tag vertical
			IF (strpos($adata['faqqa_answer'], '%GOOGLE_V%') !== FALSE) {
				$adata['faqqa_answer'] = str_replace('%GOOGLE_V%', display_google_adsense('v'),$adata['faqqa_answer']);
			}

		# Check for google adsense tag horizontal
			IF (strpos($adata['faqqa_answer'], '%GOOGLE_H%') !== FALSE) {
				$adata['faqqa_answer'] = str_replace('%GOOGLE_H%', display_google_adsense('h'), $adata['faqqa_answer']);
			}

		# Format entry
			IF ($adata['faqqa_auto_nl2br'] == 1) {
				$mod_faqqa_answer = nl2br($adata['faqqa_answer']);
			} ELSE {
				$mod_faqqa_answer = $adata['faqqa_answer'];
			}

		# Build Title String, Content String, and Footer Menu String
			$_tstr  = '<table width="100%">'.$_nl;
			$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
			$_tstr .= '<td class="TP0MED_BL">'.$adata['faqqa_question'].'</td>'.$_nl;
			$_tstr .= '<td class="TP0MED_NR">'.dt_make_datetime($adata['faqqa_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
			$_tstr .= '</tr>'.$_nl;
			$_tstr .= '<tr valign="bottom">'.$_nl;
			$_tstr .= '<td class="TP0MED_NL">'.$_sp.'</td>'.$_nl;
			$_tstr .= '<td class="TP0MED_NR">'.$_sp.'</td>'.$_nl;
			$_tstr .= '</tr>'.$_nl;
			$_tstr .= '</table>'.$_nl;

			$_cstr  = $mod_faqqa_answer.'<br><br>'.$_nl;

			IF ($_SEC['_sadmin_flg']) {
				$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faqqa&faqqa_id='.$adata['faqqa_id'], $_TCFG['_IMG_FAQ_EDIT_QA_M'],$_TCFG['_IMG_FAQ_EDIT_QA_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faqqa', $_TCFG['_IMG_FAQ_ADD_QA_M'],$_TCFG['_IMG_FAQ_ADD_QA_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
				}
				$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

			} ELSE {
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
			}
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do display faq summary
function do_display_summary($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get FAQ Summary
	# Build where string selection
		$_where .= ' ON '.$_DBCFG['faq'].'.faq_id='.$_DBCFG['faq_qa'].'.faqqa_faq_id';
		$_where .= ' WHERE '.$_DBCFG['faq'].'.faq_status=1';
		$_where .= ' AND '.$_DBCFG['faq_qa'].'.faqqa_status=1';

	# Check Admin and User Flags
		IF ($_SEC['_sadmin_flg'] != 1) {
			$_where .= ' AND '.$_DBCFG['faq'].'.faq_admin=0';
			IF ($_SEC['_suser_flg'] != 1) {
				$_where .= ' AND '.$_DBCFG['faq'].'.faq_user=0';
			}
		}

	# Do select
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['faq'].' LEFT JOIN '.$_DBCFG['faq_qa'];
		$query .= $_where;
		$query .= ' ORDER BY '.$_DBCFG['faq'].'.faq_position ASC, '.$_DBCFG['faq'].'.faq_id ASC, '.$_DBCFG['faq_qa'].'.faqqa_position ASC';

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Loop Topic query results
		$_faq_title_last	= '';
		$_faq_title_count	= 0;
		$_faq_quest_count	= 0;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
			# Flag first of topic and do link- else- just topic links.
				IF ($_faq_title_last != $row['faq_title']) {
					$_faq_title_count				= $_faq_title_count + 1;
					$_faq_title[$_faq_title_count]	= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=show&faq_id='.$row['faq_id'].'"><b>'.$row['faq_title'].'</b></a>'.$_nl;
					$_faq_descr[$_faq_title_count]	= $row['faq_descrip'].$_nl;
					$_faq_moddt[$_faq_title_count]	= $_LANG['_FAQ']['l_Last_Modified:'].$_sp.dt_make_datetime($row['faq_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
					IF ($row['faqqa_id']) {
						$_faq_qcount[$_faq_title_count] = 1;
					} ELSE {
						$_faq_qcount[$_faq_title_count] = 0;
					}
				} ELSE {
					$_faq_qcount[$_faq_title_count]	= $_faq_qcount[$_faq_title_count] + 1;
				}

			# Build admin edit text
				IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
					$_faq_admin[$_faq_title_count]  = '<br><a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faqqa&faqqa_faq_id='.$row['faq_id'].'">'.$_TCFG['_IMG_FAQ_ADD_QA_M'].'</a>';
					$_faq_admin[$_faq_title_count] .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faq&faq_id='.$row['faq_id'].'">'.$_TCFG['_IMG_FAQ_EDIT_FAQ_M'].'</a>';
				} ELSE {
					$_faq_admin[$_faq_title_count] = '';
				}

			# Set last to current
				$_faq_title_last = $row['faq_title'];
			}
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_FAQ']['FAQ_Summary'].$_nl;
		$_cstr .= '<ol>'.$_nl;

	# Loop Topic Array and Print Out  HTML
		FOR ($i = 1; $i <= $_faq_title_count; $i++) {
		# Check for line break after first block
			IF ($i > 0) {
	 	 		$_cstr .= '<li>'.$_faq_title[$i].$_sp.'('.$_faq_qcount[$i].$_sp.$_LANG['_FAQ']['questions'].')'.$_nl;
	 	 		$_cstr .= '<br><i>'.$_faq_moddt[$i].'</i>'.$_nl;
	 	 		$_cstr .= '<br>'.nl2br($_faq_descr[$i]).$_nl;
	 	 		$_cstr .= $_faq_admin[$i].$_nl;
	 	 		$_cstr .= '<p>'.$_nl;
			}
		}

		$_cstr .= '</ol>'.$_nl;

		IF ($_SEC['_sadmin_flg']) {
			$_mstr_flag = '1';
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
		} ELSE {
			$_mstr_flag = '0';
			$_mstr = '';
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display faq summary
function do_display_faq($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get FAQ Summary
	# Build where string selection
		$_where  = ' WHERE '.$_DBCFG['faq'].'.faq_id='.$_DBCFG['faq_qa'].'.faqqa_faq_id';
		$_where .= ' AND '.$_DBCFG['faq'].'.faq_id='.$adata['faq_id'];
		$_where .= ' AND '.$_DBCFG['faq'].'.faq_status=1';
		$_where .= ' AND '.$_DBCFG['faq_qa'].'.faqqa_status=1';

	# Check Admin and User Flags
		IF ($_SEC['_sadmin_flg'] != 1) {
			$_where .= ' AND '.$_DBCFG['faq'].'.faq_admin=0';
			IF ($_SEC['_suser_flg'] != 1) {$_where .= ' AND '.$_DBCFG['faq'].'.faq_user=0';}
		}

	# Do select
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['faq'].', '.$_DBCFG['faq_qa'];
		$query .= $_where;
		$query .= ' ORDER BY '.$_DBCFG['faq'].'.faq_position ASC, '.$_DBCFG['faq'].'.faq_id ASC, '.$_DBCFG['faq_qa'].'.faqqa_position ASC';

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Loop Topic query results
		$_faq_title_last	= '';
		$_faq_title_count	= 0;
		$_faq_quest_count	= 0;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
			# Flag first of topic and do link- else- just topic links.
				IF ($_faq_title_last != $row['faq_title']) {
					$_faq_title_count					= $_faq_title_count + 1;
					$_faq_title[$_faq_title_count]		= $row['faq_title'].$_nl;
					$_faq_descr[$_faq_title_count]		= $row['faq_descrip'].$_nl;
					$_faq_moddt[$_faq_title_count]		= $_LANG['_FAQ']['l_Last_Modified:'].$_sp.dt_make_datetime($row['faq_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
					IF ($row['faqqa_id']) {$_faq_quest_count = 1;} ELSE {$_faq_quest_count = 0;}
					$_faq_qcount[$_faq_title_count] 		= $_faq_quest_count;
					$_faqqa_id[$_faq_quest_count]			= $row['faqqa_id'];
					$_faqqa_qa_link[$_faq_quest_count]		= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=show&faq_id='.$row['faq_id'].'#'.$row['faqqa_id'].'"><b>'.$row['faqqa_question'].'</b></a>'.$_nl;
					$_faqqa_question[$_faq_quest_count]	= '<a name="'.$row['faqqa_id'].'"><b>'.$row['faqqa_question'].'</b></a>'.$_nl;
					$_faqqa_answer[$_faq_quest_count]		= $row['faqqa_answer'].$_nl;
					$_faqqa_moddt[$_faq_quest_count]		= $_LANG['_FAQ']['l_Last_Modified:'].$_sp.dt_make_datetime($row['faqqa_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
					$_faqqa_auto_nl2br[$_faq_quest_count]	= $row['faqqa_auto_nl2br'];

				} ELSE {
					$_faq_quest_count					= $_faq_quest_count + 1;
					$_faq_qcount[$_faq_title_count] 		= $_faq_quest_count;
					$_faqqa_id[$_faq_quest_count]			= $row['faqqa_id'];
					$_faqqa_qa_link[$_faq_quest_count]		= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=show&faq_id='.$row['faq_id'].'#'.$row['faqqa_id'].'"><b>'.$row['faqqa_question'].'</b></a>'.$_nl;
					$_faqqa_question[$_faq_quest_count]	= '<a name="'.$row['faqqa_id'].'"><b>'.$row['faqqa_question'].'</b></a>'.$_nl;
					$_faqqa_answer[$_faq_quest_count]		= $row['faqqa_answer'].$_nl;
					$_faqqa_moddt[$_faq_quest_count]		= $_LANG['_FAQ']['l_Last_Modified:'].$_sp.dt_make_datetime($row['faqqa_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
					$_faqqa_auto_nl2br[$_faq_quest_count]	= $row['faqqa_auto_nl2br'];
				}

			# Build FAQ QA admin edit text
				IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
					$_faqqa_admin[$_faq_quest_count]  = '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=show&faq_id='.$row['faq_id'].'#top">'.$_TCFG['_IMG_TOP_S'].'</a>';
					$_faqqa_admin[$_faq_quest_count] .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faqqa&faqqa_id='.$row['faqqa_id'].'">'.$_TCFG['_IMG_EDIT_S'].'</a>';
				} ELSE {
					$_faqqa_admin[$_faq_quest_count] = '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=show&faq_id='.$row['faq_id'].'#top">'.$_TCFG['_IMG_TOP_S'].'</a>';
				}

			# Build FAQ admin edit text
				IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
					$_faq_admin[$_faq_title_count]  = '<br><a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faqqa&faqqa_faq_id='.$row['faq_id'].'">'.$_TCFG['_IMG_FAQ_ADD_QA_M'].'</a>';
					$_faq_admin[$_faq_title_count] .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faq&faq_id='.$row['faq_id'].'">'.$_TCFG['_IMG_FAQ_EDIT_FAQ_M'].'</a>';
				} ELSE {
					$_faq_admin[$_faq_title_count] = '';
				}

			# Set last to current
				$_faq_title_last 	= $row['faq_title'];
			}
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_FAQ']['FAQ'].':'.$_sp.$_faq_title[1].$_sp.'('.$_faq_qcount[1].$_sp.$_LANG['_FAQ']['questions'].')'.$_nl;

	# Loop Topic Array and Print Out  HTML
		for ($i = 1; $i <= $_faq_title_count; $i++) {
		# Check for line break after first block
			IF ($i > 0) {
				$mod_faq_descr = nl2br($_faq_descr[$i]);
		  		$_cstr .= $mod_faq_descr.$_nl;
		  		$_cstr .= '<ol>'.$_nl;

		  	# Loop Topic Array and Print Out  HTML
				for ($j = 1; $j <= $_faq_quest_count; $j++) {
				# Check for line break after first block
					IF ($j > 0) {$_cstr .= '<li>'.$_faqqa_qa_link[$j].'<br>'.$_nl;}
				}

				$_cstr .= '</ol>'.$_nl;
		  		$_cstr .= '<center>'.$_faq_admin[$i].'</center>'.$_nl;
		  	}
		}

		IF ($_SEC['_sadmin_flg']) {
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link ($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
		} ELSE {
			$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Flush block_it vars
		$_tstr = ''; $_cstr = ''; $_mstr = '';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_FAQ']['FAQ_Answers'].':'.$_sp.$_faq_title[1].$_nl;

	# Loop and layout questions / answers
  		$_cstr .= '<ol>'.$_nl;

	# Loop Topic Array and Print Out  HTML
		for ($j = 1; $j <= $_faq_quest_count; $j++) {
		# Check for line break after first block
			IF ($j > 0) {
				$_cstr .= '<li>'.$_faqqa_question[$j].$_nl;
			#	$_cstr .= '<br><i>'.$_faqqa_moddt[$j].'</i>'.$_nl;
				IF ($_faqqa_auto_nl2br[$j] == 1) {
					$mod_faqqa_answer = nl2br($_faqqa_answer[$j]);
				} ELSE {
					$mod_faqqa_answer = $_faqqa_answer[$j];
				}

				$_cstr .= '<br>'.$mod_faqqa_answer.$_nl;
				$_cstr .= '<p>'.$_faqqa_admin[$j];
				$_cstr .= '<p>'.$_nl;
		  	}
		}

  		$_cstr .= '</ol>'.$_nl;

		IF ($_SEC['_sadmin_flg']) {
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
		} ELSE {
			$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do display faq qa view
function do_display_faqqa($amode, $adata, $aret_flag=0 ) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build where string for selection
		$_where .= ' WHERE '.$_DBCFG['faq'].'.faq_id='.$_DBCFG['faq_qa'].'.faqqa_faq_id';
		$_where .= ' AND '.$_DBCFG['faq_qa'].'.faqqa_id='.$adata['faqqa_id'];
		$_where .= ' AND '.$_DBCFG['faq'].'.faq_status=1';
		$_where .= ' AND '.$_DBCFG['faq_qa'].'.faqqa_status=1';

	# Check Admin and User Flags
		IF ($_SEC['_sadmin_flg'] != 1) {
			$_where .= ' AND '.$_DBCFG['faq'].'.faq_admin=0';
			IF ($_SEC['_suser_flg'] != 1) {$_where .= ' AND '.$_DBCFG['faq'].'.faq_user=0';}
		}

	# Do select
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['faq'].', '.$_DBCFG['faq_qa'];
		$query .= $_where;
		$query .= ' ORDER BY '.$_DBCFG['faq_qa'].'.faqqa_question DESC';

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		IF (!$numrows) {
			$err_text	 = '<br>'.$_LANG['_FAQ']['Error_Not_Found'].$_nl;
			$err_text	.= '<br>[ <a href="mod.php?mod=faq">'.$_LANG['_FAQ']['View_All'].'</a> ]'.$_nl;
		}

	# Start content
		$_out .= '<!-- Start content -->'.$_nl;

	# Row for no records found
		IF (!$numrows) {
		# Build Title String, Content String, and Footer Menu String
			$_tstr  = $_LANG['_FAQ']['View_FAQ_QA'].$_nl;
	  		$_cstr  = '<p class="PSML_NC">'.$err_text.'<br><br>'.$_nl;
	  		$_mstr  = ''.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
			$_out .= '<br>'.$_nl;

			IF ($aret_flag) {return $_out;} ELSE {echo $_out;}

		} ELSE {
		# Process query results
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Check for search string to emphasize:
					IF ($adata['ss'] != '') {
						$_str_search		= $adata['ss'];
						$faqqa_question	= do_highlight_text($row['faqqa_question'], $_str_search);
						$faqqa_answer 		= do_highlight_text($row['faqqa_answer'], $_str_search);
					} ELSE {
						  $faqqa_question	= $row['faqqa_question'];
						  $faqqa_answer	= $row['faqqa_answer'];
					 }

				# Build Title String, Content String, and Footer Menu String
					$_tstr  = $_LANG['_FAQ']['View_FAQ_QA'].$_nl;

					$_cstr  = '<ol>'.$_nl;
					$_cstr .= '<li><b>'.$faqqa_question.'</b>'.$_nl;
					IF ($row['faqqa_auto_nl2br'] == 1) {
						$mod_faqqa_answer = nl2br($faqqa_answer);
					} ELSE {
						$mod_faqqa_answer = $faqqa_answer;
					}

					$_cstr .= '<br>'.$mod_faqqa_answer.$_nl;
					$_cstr .= '<p>'.$_nl;
					$_cstr .= '</ol>'.$_nl;

 	 				$_mstr  = ''.$_nl;

				}  # End while loop
			}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
			$_out .= '<br>'.$_nl;

			IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
		}
}

/**************************************************************
 * End Module Functions
**************************************************************/
?>