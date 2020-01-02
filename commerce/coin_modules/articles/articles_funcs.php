<?php
/**
 * Module: Articles (Common Funcions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Articles
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_articles.php
 */


# Code to handle file being loaded by URL
	IF (eregi(articles_funcs.php, $_SERVER[PHP_SELF])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=articles');
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
	#	IF (!$_GPV['id']) 		{$err_entry['flag'] = 1; $err_entry['id'] = 1;}
		IF (!$_GPV['subject']) 	{$err_entry['flag'] = 1; $err_entry['subject'] = 1;}
		IF (!$_GPV['topic_id']) 	{$err_entry['flag'] = 1; $err_entry['topic_id'] = 1;}
		IF (!$_GPV['cat_id']) 	{$err_entry['flag'] = 1; $err_entry['cat_id'] = 1;}
		IF (!$_GPV['entry']) 	{$err_entry['flag'] = 1; $err_entry['entry'] = 1;}

		return $err_entry;
}


# Do display entry (individual articles entry)
function do_display_entry($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP0MED_BL">'.$adata['subject'].'</td>'.$_nl;
		$_tstr .= '<td class="TP0MED_NR">'.$adata['topic_name'].'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP0MED_NL">'.dt_display_datetime($adata['time_stamp'], $_CCFG['ARTICLES_DATE_DISPLAY_FORMAT']).'</td>'.$_nl;
		$_tstr .= '<td class="TP0MED_NR">'.$adata['cat_name'].'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

	# Format entry
		IF ($adata['auto_nl2br'] == 1) {
			$mod_entry = nl2br($adata['entry']);
		} ELSE {
			$mod_entry = $adata['entry'];
		}
		$_cstr  = $mod_entry.'<br><br>'.$_nl;

		IF ($_SEC['_sadmin_flg']) {
		# Build footer menu argument text
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=edit&id='.$adata['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		} ELSE {
		# Build footer menu argument text
			$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do display articles listing
function do_display_listing($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Call timestamp function
		$_uts = dt_get_uts();

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build where string for topic_id and cat_id selection
		$_where  = ' WHERE '.$_DBCFG['articles'].'.topic_id='.$_DBCFG['topics'].'.topic_id';
		$_where .= ' AND '.$_DBCFG['articles'].'.cat_id='.$_DBCFG['categories'].'.cat_id';
		$_where .= ' AND '.$_DBCFG['articles'].".time_stamp <= '".$_uts."'";

		IF ($adata['dtopic_id']) {
			$_where .= ' AND '.$_DBCFG['articles'].'.topic_id='.$adata['dtopic_id'];
		}
		IF ($adata['dcat_id']) {
			$_where .= ' AND '.$_DBCFG['articles'].'.cat_id='.$adata['dcat_id'];
		}

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['articles'].', '.$_DBCFG['topics'].', '.$_DBCFG['categories'];
		$query_ttl .= $_where;

		$result_ttl = $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next	- is page loading first record number
	# $_rec_start	- is a given page start record (which will be rec_next)
		$_rec_page	= $_CCFG['IPP_ARTICLES'];
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
			$_page_menu = $_LANG['_ARTICLES']['l_Pages'].' ';
			FOR ($i = 1; $i <= $_num_pages; $i++) {
				$_rec_start = (($i*$_rec_page)-$_rec_page);
				IF ($_rec_start == $_rec_next) {
				# Loading Page start record so no link for this page.
					$_page_menu .= $i;
				} ELSE {
					IF ($adata['dtopic_id'])	{$_argt = '&dtopic_id='.$adata['dtopic_id'];}
					IF ($adata['dcat_id'])	{$_argc = '&dcat_id='.$adata['dcat_id'];}
					$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=articles&mode=list'.$_argt.$_argc.'&rec_next='.$_rec_start.'">'.$i.'</a>';
				}

				IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
			}
		# End page menu

		# Do select
			$query  = 'SELECT '.$_DBCFG['articles'].'.id, '.$_DBCFG['articles'].'.subject';
			$query .= ', '.$_DBCFG['articles'].'.topic_id, '.$_DBCFG['articles'].'.cat_id';
			$query .= ', '.$_DBCFG['articles'].'.time_stamp, '.$_DBCFG['articles'].'.auto_nl2br';
			$query .= ', '.$_DBCFG['articles'].'.entry, '.$_DBCFG['topics'].'.topic_name';
			$query .= ', '.$_DBCFG['categories'].'.cat_name';
			$query .= ' FROM '.$_DBCFG['articles'].', '.$_DBCFG['topics'].', '.$_DBCFG['categories'];
			$query .= $_where;
			$query .= " ORDER BY time_stamp DESC LIMIT $_rec_next, $_rec_page";

			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Check Return for numrows
			IF (!$numrows) {
				$numrows 	 = 0;
				$err_text	 = '<div align="center"><hr><br>'.$_LANG['_ARTICLES']['ART_ERR_NONE_FOUND'].$_nl;
				$err_text	.= '<br>[ <a href="'.$_SERVER["PHP_SELF"].'?mod=articles&mode=list">'.$_LANG['_ARTICLES']['View_All'].'</a> ]</div>'.$_nl;
			}

		# Build Title String, Content String, and Footer Menu String
 		 	$_tstr .= $_LANG['_ARTICLES']['Articles'].$_sp.$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_ARTICLES']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_ARTICLES']['total_entries'].')';

		# # Add "edit parameters" button
			IF ($_SEC['_sadmin_flg'] && $_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
				$_tstr .= ' <a href="admin.php?cp=parms&op=edit&fpg=&fpgs=articles">'.$_TCFG['_S_IMG_PM_S'].'</a>';
			}

			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="articles">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="list">'.$_nl;
			$_cstr .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0MED_NL" valign="top">'.$_page_menu.'</td>'.$_nl;
			$_cstr .= '<td class="TP0MED_NR" valign="top">'.$_nl;
			$_cstr .= do_select_list_topic('dtopic_id', $adata['dtopic_id'], '1');
			$_cstr .= do_select_list_cat('dcat_id', $adata['dcat_id'], '1');
			$_cstr .= do_input_button_class_sw('b_doit', 'SUBMIT', $_LANG['_ARTICLES']['B_Do_It'], 'button_form_s_h', 'button_form_s', '1').$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;

		# Error for no records found
			IF (!$numrows) {$_cstr .= '<p>'.$err_text.'<br>'.$_nl;}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '0', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Print out results
			while(list($id, $subject, $topic_id, $cat_id, $time_stamp, $auto_nl2br, $entry, $topic_name, $cat_name) = $db_coin->db_fetch_row($result)) {
			# Rebuild Data Array with returned record
				$data['id']			= $id;
				$data['subject']		= $subject;
				$data['topic_id']		= $topic_id;
				$data['cat_id']		= $cat_id;
				$data['time_stamp'] 	= $time_stamp;
				$data['auto_nl2br']		= $auto_nl2br;
				$data['entry']			= $entry;
				$data['topic_name']		= $topic_name;
				$data['cat_name']		= $cat_name;

			# Call function for Display Entry
				$_out .= do_display_entry($amode, $data, '1').$_nl;

			}  # End while loop

			$_out .= '<br>'.$_nl;

			IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display articles summary
function do_display_summary($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

		# Dim some Vars:
			global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;


	# Get Topics Summary
	# Build where string for topic_id and cat_id selection
		$_where_t = ' WHERE '.$_DBCFG['articles'].'.topic_id='.$_DBCFG['topics'].'.topic_id';

	# Do select
		$query_t  = 'SELECT '.$_DBCFG['topics'].'.topic_name, '.$_DBCFG['topics'].'.topic_id';
		$query_t .= ', '.$_DBCFG['topics'].'.topic_desc';
		$query_t .= ', '.$_DBCFG['articles'].'.id, '.$_DBCFG['articles'].'.subject';
		$query_t .= ', '.$_DBCFG['articles'].'.topic_id';
		$query_t .= ' FROM '.$_DBCFG['topics'].', '.$_DBCFG['articles'];
		$query_t .= $_where_t;
		$query_t .= ' ORDER BY '.$_DBCFG['topics'].'.topic_name ASC, '.$_DBCFG['articles'].'.subject ASC';

		$result_t		= $db_coin->db_query_execute($query_t);
		$numrows_t	= $db_coin->db_query_numrows($result_t);

	# Loop Topic query results
		$_topic_name_last		= '';
		$_topic_link_count		= 0;
		$_articles_link_count	= 0;

		while(list($t_topic_name, $t_topic_id, $t_topic_desc, $j_id, $j_subject, $j_topic_id) = $db_coin->db_fetch_row($result_t)) {
		# Flag first of topic and do link- else- just topic links.
			IF ($_topic_name_last != $t_topic_name) {
				$_topic_link_count				= $_topic_link_count + 1;
				$_articles_link_count			= 1;
				$topic_count[$_topic_link_count] 	= $_articles_link_count;
				$topic_link[$_topic_link_count] 	= '';
				$topic_link[$_topic_link_count] 	.= '<a href="'.$_SERVER["PHP_SELF"].'?mod=articles&mode=list&dtopic_id='.$t_topic_id.'">';
				$topic_link[$_topic_link_count] 	.= '<b>'.$t_topic_name.'</b></a>'.$_nl;
			} ELSE {
				$_articles_link_count			= $_articles_link_count + 1;
				$topic_count[$_topic_link_count] 	= $_articles_link_count;
			}

		# Set last to current
			$_topic_name_last 	= $t_topic_name;
		}

	# Get Categories Summary
	# Build where string for topic_id and cat_id selection
		$_where_c = ' WHERE '.$_DBCFG['articles'].'.cat_id='.$_DBCFG['categories'].'.cat_id';

	# Do select
		$query_c  = 'SELECT '.$_DBCFG['categories'].'.cat_name, '.$_DBCFG['categories'].'.cat_id';
		$query_c .= ', '.$_DBCFG['categories'].'.cat_desc';
		$query_c .= ', '.$_DBCFG['articles'].'.id, '.$_DBCFG['articles'].'.subject';
		$query_c .= ', '.$_DBCFG['articles'].'.cat_id';
		$query_c .= ' FROM '.$_DBCFG['categories'].', '.$_DBCFG['articles'];
		$query_c .= $_where_c;
		$query_c .= ' ORDER BY '.$_DBCFG['categories'].'.cat_name ASC';

		$result_c		= $db_coin->db_query_execute($query_c);
		$numrows_c	= $db_coin->db_query_numrows($result_c);

	# Loop Topic query results
		$_cat_name_last		= '';
		$_cat_link_count		= 0;
		$_articles_link_count	= 0;

		while(list($c_cat_name, $c_cat_id, $c_cat_desc, $j_id, $j_subject, $j_cat_id) = $db_coin->db_fetch_row($result_c)) {
		# Flag first of topic and do link- else- just topic links.
			IF ($_cat_name_last != $c_cat_name) {
				$_cat_link_count			= $_cat_link_count + 1;
				$_articles_link_count		= 1;
				$cat_count[$_cat_link_count] 	= $_articles_link_count;
				$cat_link[$_cat_link_count] 	= '';
				$cat_link[$_cat_link_count] 	.= '<a href="'.$_SERVER["PHP_SELF"].'?mod=articles&mode=list&dcat_id='.$c_cat_id.'">';
				$cat_link[$_cat_link_count] 	.= '<b>'.$c_cat_name.'</b></a>'.$_nl;
			} ELSE {
				$_articles_link_count		= $_articles_link_count + 1;
				$cat_count[$_cat_link_count] 	= $_articles_link_count;
			}

		# Set last to current
			$_cat_name_last = $c_cat_name;
		}

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ARTICLES']['Articles_Summary'];

			$_cstr  = '<br>'.$_nl;
			$_cstr .= '<table width="75%" align="center">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0MED_NL" valign="top"><b>'.$_LANG['_ARTICLES']['l_Entries_By_Topic'].'</b><br>'.$_nl;

		# Loop Topic Array and Print Out  HTML
			FOR ($i = 1; $i <= $_topic_link_count; $i++) {
				# Check for line break after first block
				  IF ($i > 0) { $_cstr .= $_sp.$_sp.'- '.$topic_link[$i].$_sp.'('.$topic_count[$i].')<br>'.$_nl; }
			}

			$_cstr .= '<br></td><td class="TP0MED_NL" valign="top"><b>'.$_LANG['_ARTICLES']['l_Entries_By_Category'].'</b><br>'.$_nl;

		# Loop Category Array and Print Out  HTML
			FOR ($i = 1; $i <= $_cat_link_count; $i++) {
			# Check for line break after first block
				  IF ($i > 0) {$_cstr .= $_sp.$_sp.'- '.$cat_link[$i].$_sp.'('.$cat_count[$i].')<br>'.$_nl;}
			}

			$_cstr .= '<br></td></tr></table>'.$_nl;

			IF ($_SEC['_sadmin_flg']) {
			# Build function argument text
				$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=edit&id='.$adata['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				}
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			} ELSE {
			# Build function argument text
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display article view
function do_display_article($amode, $adata, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build where string for topic_id and cat_id selection
		$_where  = ' WHERE '.$_DBCFG['articles'].'.topic_id='.$_DBCFG['topics'].'.topic_id';
		$_where .= ' AND '.$_DBCFG['articles'].'.cat_id='.$_DBCFG['categories'].'.cat_id';
		$_where .= ' AND '.$_DBCFG['articles'].'.id='.$adata['id'];

	# Do select
		$query  = 'SELECT '.$_DBCFG['articles'].'.id, '.$_DBCFG['articles'].'.subject';
		$query .= ', '.$_DBCFG['articles'].'.topic_id, '.$_DBCFG['articles'].'.cat_id';
		$query .= ', '.$_DBCFG['articles'].'.time_stamp, '.$_DBCFG['articles'].'.auto_nl2br';
		$query .= ', '.$_DBCFG['articles'].'.entry, '.$_DBCFG['topics'].'.topic_name';
		$query .= ', '.$_DBCFG['categories'].'.cat_name';
		$query .= ' FROM '.$_DBCFG['articles'].', '.$_DBCFG['topics'].', '.$_DBCFG['categories'];
		$query .= $_where;
		$query .= ' ORDER BY time_stamp DESC';

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check numrows and go accrdingly
		IF (!$numrows) {
			$err_text	 = '<br>'.$_LANG['_ARTICLES']['ART_ERR_NONE_FOUND'].$_nl;
			$err_text	.= '<br>[ <a href="'.$_SERVER["PHP_SELF"].'?mod=articles&mode=list">'.$_LANG['_ARTICLES']['View_All'].'</a> ]'.$_nl;
			$_cstr .= '<p align="center">'.$err_text.'<br><br>'.$_nl;
			$_out .= do_mod_block_it ($_LANG['_ARTICLES']['View_Article'], $_cstr, '0', '', '1').$_nl;

		} ELSE {
		# Print out results
			while(list($id, $subject, $topic_id, $cat_id, $time_stamp, $auto_nl2br, $entry, $topic_name, $cat_name) = $db_coin->db_fetch_row($result)) {
			# Check for search string to emphasize:
				IF ($adata['ss'] != '') {
					$_str_search	= $adata['ss'];
					$subject 		= do_highlight_text($subject, $_str_search);
					$entry 		= do_highlight_text($entry, $_str_search);
				}

			# Rebuild Data Array with returned record
				$data['id']			= $id;
				$data['subject']		= $subject;
				$data['topic_id']		= $topic_id;
				$data['cat_id']		= $cat_id;
				$data['time_stamp'] 	= $time_stamp;
				$data['auto_nl2br']		= $auto_nl2br;
				$data['entry']			= $entry;
				$data['topic_name']		= $topic_name;
				$data['cat_name']		= $cat_name;

			# Call function for Display Entry
				$_out .= do_display_entry($amode, $data, '1').$_nl;

			}  # End while loop
		}

		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Functions
**************************************************************/
?>