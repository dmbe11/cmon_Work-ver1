<?php
/**
 * Module: Pages (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Pages
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_pages.php
 */


# Code to handle file being loaded by URL
	IF (eregi('pages_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=pages');
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
		IF (!$_GPV['subject']) 		{$err_entry['flag'] = 1; $err_entry['subject'] = 1;}
		IF (!$_GPV['topic_id']) 		{$err_entry['flag'] = 1; $err_entry['topic_id'] = 1;}
		IF (!$_GPV['cat_id']) 		{$err_entry['flag'] = 1; $err_entry['cat_id'] = 1;}
		IF (!$_GPV['pages_code'])	{$err_entry['flag'] = 1; $err_entry['pages_code'] = 1;}

		return $err_entry;
}


# Do display entry (individual pages entry)
function do_display_entry($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP0MED_BL">'.$adata['subject'].'</td>'.$_nl;
		$_tstr .= '<td class="TP0MED_NR">'.$adata['topic_name'].'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP0MED_NL">'.dt_make_datetime($adata['time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
		$_tstr .= '<td class="TP0MED_NR">'.$adata['cat_name'].'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		# Check for google adsense tag vertical
			IF (strpos($adata['pages_code'], '%GOOGLE_V%') !== FALSE) {
				$adata['pages_code'] = str_replace('%GOOGLE_V%', display_google_adsense('v'), $adata['pages_code']);
			}

		# Check for google adsense tag horizontal
			IF (strpos($adata['pages_code'], '%GOOGLE_H%') !== FALSE) {
				$adata['pages_code'] = str_replace('%GOOGLE_H%', display_google_adsense('h'), $adata['pages_code']);
			}

		$_cstr .= nl2br($adata['pages_code']).'<br><br>'.$_nl;

		IF ($_SEC['_sadmin_flg']) {
		# Build function argument text
			$_mstr_flg = 1;
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit&id='.$adata['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit', $_TCFG['_IMG_SELECT_LIST_M'],$_TCFG['_IMG_SELECT_LIST_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		} ELSE {
		# Build function argument text
			$_mstr_flg = 1;
			$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flg, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display pages listing
function do_display_listing($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build where string for topic_id and cat_id selection
		$_where  = ' WHERE '.$_DBCFG['pages'].'.topic_id='.$_DBCFG['topics'].'.topic_id';
		$_where .= ' AND '.$_DBCFG['pages'].'.cat_id='.$_DBCFG['categories'].'.cat_id';

		IF ($adata['dtopic_id']) {
			$_where .= ' AND '.$_DBCFG['pages'].'.topic_id='.$adata['dtopic_id'];
		}
		IF ($adata['dcat_id']) {
			$_where .= ' AND '.$_DBCFG['pages'].'.cat_id='.$adata['dcat_id'];
		}

	# Build where string for admin check
		IF (!$_SEC['_sadmin_flg']) {
			$_where .= ' AND '.$_DBCFG['pages'].'.pages_admin=0';
		}

	# Build where string for status check- if not admin- show only "on"
		IF (!$_SEC['_sadmin_flg']) {
			$_where .= ' AND '.$_DBCFG['pages'].'.pages_status=1';
		}

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['pages'].", ".$_DBCFG['topics'].", ".$_DBCFG['categories'];
		$query_ttl .= $_where;

		$result_ttl= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

		# Page Loading first rec number
		# $_rec_next	- is page loading first record number
		# $_rec_start	- is a given page start record (which will be rec_next)
			$_rec_page	= $_CCFG['IPP_PAGES'];
			$_rec_next	= $adata['rec_next'];
			IF (!$_rec_next) {$_rec_next = 0;}

		# Range of records on current page
			$_rec_next_lo = $_rec_next+1;
			$_rec_next_hi = $_rec_next+$_rec_page;
			IF ($_rec_next_hi > $numrows_ttl) {$_rec_next_hi = $numrows_ttl;}

		# Calc no pages,
			$_num_pages = round(($numrows_ttl/$_rec_page), 0);
			IF ($_num_pages < ($numrows_ttl/$_rec_page)) {$_num_pages = $_num_pages+1;}

		# Loop Array and Print Out Page Menu HTML
			$_page_menu = $_LANG['_PAGES']['l_Pages'].$_sp;
			for ($i = 1; $i <= $_num_pages; $i++) {
				$_rec_start = ( ($i*$_rec_page)-$_rec_page);
				IF ($_rec_start == $_rec_next) {
				# Loading Page start record so no link for this page.
					$_page_menu .= $i;
				} ELSE {
					IF ($adata['dtopic_id'])	{$_argt= '&dtopic_id='.$adata['dtopic_id'];}
					IF ($adata['dcat_id'])	{$_argc= '&dcat_id='.$adata['dcat_id'];}
					$_page_menu .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=list'.$_argt.$_argc.'&rec_next='.$_rec_start.'">'.$i.'</a>';
				}

				IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
			} # End page menu

		# Do select
			$query = 'SELECT '.$_DBCFG['pages'].'.id, '.$_DBCFG['pages'].'.subject, '.$_DBCFG['pages'].'.topic_id';
			$query .= ', '.$_DBCFG['pages'].'.cat_id, '.$_DBCFG['pages'].'.time_stamp';
			$query .= ', '.$_DBCFG['pages'].'.pages_title, '.$_DBCFG['pages'].'.pages_code, '.$_DBCFG['pages'].'.pages_block_it';
			$query .= ', '.$_DBCFG['pages'].'.pages_status, '.$_DBCFG['pages'].'.pages_admin';
			$query .= ', '.$_DBCFG['topics'].'.topic_name, '.$_DBCFG['categories'].'.cat_name';
			$query .= ' FROM '.$_DBCFG['pages'].', '.$_DBCFG['topics'].', '.$_DBCFG['categories'];
			$query .= $_where;
			$query .= " ORDER BY time_stamp DESC LIMIT $_rec_next, $_rec_page";

			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Check Return for numrows
			IF (!$numrows) {
				$numrows 	= 0;
				$err_text	= '<div align="center"><hr><br>'.$_LANG['_PAGES']['PG_ERR_NONE_FOUND'].$_nl;
				$err_text	.= '<br>[ <a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=list">'.$_LANG['_PAGES']['View_All'].'</a> ]</div>'.$_nl;
			}

		# Build Title String, Content String, and Footer Menu String
 		 	$_tstr .= $_LANG['_PAGES']['Pages'].$_sp.$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_PAGES']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_PAGES']['total_entries'].')'.$_nl;

		# Do admin login test
			IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
				# $_tstr .= '(<a href="mod.php?mod=pages&mode=add">'.$_LANG['_PAGES']['Add_To_Pages'].'</a>)'.$_nl;
			}

			$_cstr .= '<table width="100%">'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= '<td class="TP0MED_NL">'.$_page_menu.'</td>'.$_nl;
			$_cstr .= '<td class="TP0MED_NR">'.$_nl;
			$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="pages">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="list">'.$_nl;
			$_cstr .= do_select_list_topic('dtopic_id', $adata['dtopic_id'], '1').$_nl;
			$_cstr .= do_select_list_cat('dcat_id', $adata['dcat_id'], '1').$_nl;

			$_cstr .= do_input_button_class_sw('b_doit', 'SUBMIT', $_LANG['_PAGES']['B_Do_It'], 'button_form_s_h', 'button_form_s', '1').$_nl;
			$_cstr .= '</FORM>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '<hr>'.$_nl;

		# Error for no records found
			IF (!$numrows) {$_cstr .= '<p>'.$err_text.'<br>'.$_nl;}

		# Print out results
			while(list($id, $subject, $topic_id, $cat_id, $time_stamp, $pages_title, $pages_code, $pages_block_it, $pages_status, $pages_admin, $topic_name, $cat_name) = $db_coin->db_fetch_row($result)) {
			# Build Link For View:
				IF ($_link_str) {$_link_str .= '<br>';}
				$_link_str .= $_sp.$_sp.dt_make_datetime($time_stamp, $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=view&id='.$id.'"><b>'.$subject.'</b></a>'.$_nl;
			}

		# Check numrows for listing
			IF ($numrows) {
			# Build Title String, Content String, and Footer Menu String
				$_cstr .= $_link_str.$_nl;
				$_cstr .= '<br><br>'.$_nl;

				IF ($_SEC['_sadmin_flg']) {
				# Build function argument text
					$_mstr_flg = 1;
					$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
						$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
						$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit', $_TCFG['_IMG_SELECT_LIST_M'],$_TCFG['_IMG_SELECT_LIST_M_MO'],'','');
					}
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

				} ELSE {
				# Build function argument text
					$_mstr_flg = 1;
					$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
					$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
				}
			}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flg, $_mstr, '1');
			$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display page
function do_page_display($aid, $aret_flag=0, $ass='') {
	# Dim globals
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Build query string
		$query  = 'SELECT '.$_DBCFG['pages'].'.id, '.$_DBCFG['pages'].'.subject';
		$query .= ', '.$_DBCFG['pages'].'.topic_id, '.$_DBCFG['pages'].'.cat_id';
		$query .= ', '.$_DBCFG['pages'].'.time_stamp';
		$query .= ', '.$_DBCFG['pages'].'.pages_title, '.$_DBCFG['pages'].'.pages_code';
		$query .= ', '.$_DBCFG['pages'].'.pages_block_it, '.$_DBCFG['pages'].'.pages_status';
		$query .= ', '.$_DBCFG['pages'].'.pages_admin';
		$query .= ', '.$_DBCFG['pages'].'.pages_link_menu, '.$_DBCFG['pages'].'.pages_link_prev';
		$query .= ', '.$_DBCFG['pages'].'.pages_link_home, '.$_DBCFG['pages'].'.pages_link_next';
		$query .= ' FROM '.$_DBCFG['pages'];
		$query .= ' WHERE '.$_DBCFG['pages'].'.id='.$aid;

	# Build where string for admin check
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['pages'].'.pages_admin=0';}

	# Build where string for status check- if not admin- show only "on"
		IF (!$_SEC['_sadmin_flg']) {$query .= ' AND '.$_DBCFG['pages'].'.pages_status=1';}
		$query .= ' ORDER BY id asc';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results
		while(list($id, $subject, $topic_id, $cat_id, $time_stamp, $pages_title, $pages_code, $pages_block_it, $pages_status, $pages_admin, $pages_link_menu, $pages_link_prev, $pages_link_home, $pages_link_next) = $db_coin->db_fetch_row($result)) {

		# Check for search string to emphasize:
			IF ($ass != '') {
				$_str_search	= $ass;
				$subject 		= do_highlight_text($subject, $_str_search );
				$pages_code 	= do_highlight_text($pages_code, $_str_search );
			}

		# Check for blockit call, or just dump text
			IF ($pages_block_it) {

			# Add "Edit" button if admin
				IF ($_SEC['_sadmin_flg'] && $_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1)) {
					$pages_title .= ' <a href="mod.php?mod=pages&mode=edit&id='.$id.'">'.$_TCFG['_S_IMG_EDIT_S'].'</a>';
				}

			# Vars work
				$string = addslashes($pages_code);
				eval("\$string = \"$string\";");
				$string = stripslashes($string);

			# Build function argument text
				$menu_text = "";
				IF ($pages_link_prev) {$menu_text .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=view&id='.$pages_link_prev.'">'.$_TCFG['_IMG_PREV_M'].'</a>';}
				IF ($pages_link_home) {$menu_text .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=view&id='.$pages_link_home.'">'.$_TCFG['_IMG_MAIN_M'].'</a>';}
				IF ($pages_link_next) {$menu_text .= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=view&id='.$pages_link_next.'">'.$_TCFG['_IMG_NEXT_M'].'</a>';}

			# Call block function
				$_out .= do_block_it ($pages_title, $string, $pages_link_menu, $menu_text, '1');

			} ELSE {
			# Vars work
				$string = addslashes($pages_code);
				eval("\$string = \"$string\";");
				$string = stripslashes($string);
				$_out .= $string;
			}
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display pages summary
function do_display_summary($amode, $adata, $aret_flag=0) {
	# Get security vars
		$_SEC = get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get Topics Summary
	# Build where string for topic_id and cat_id selection
		$_where_t = ' WHERE '.$_DBCFG['pages'].'.topic_id='.$_DBCFG['topics'].'.topic_id';

	# Do select
		$query_t  = 'SELECT '.$_DBCFG['topics'].'.topic_name, '.$_DBCFG['topics'].'.topic_id';
		$query_t .= ', '.$_DBCFG['topics'].'.topic_desc';
		$query_t .= ', '.$_DBCFG['pages'].'.id, '.$_DBCFG['pages'].'.subject';
		$query_t .= ', '.$_DBCFG['pages'].'.topic_id';
		$query_t .= ' FROM '.$_DBCFG['topics'].', '.$_DBCFG['pages'];
		$query_t .= $_where_t;
		$query_t .= ' ORDER BY '.$_DBCFG['topics'].'.topic_name ASC, '.$_DBCFG['pages'].'.subject ASC';

		$result_t		= $db_coin->db_query_execute($query_t);
		$numrows_t	= $db_coin->db_query_numrows($result_t);

	# Loop Topic query results
		$_topic_name_last	= '';
		$_topic_link_count	= 0;
		$_pages_link_count	= 0;

		while(list($t_topic_name, $t_topic_id, $t_topic_desc, $p_id, $p_subject, $p_topic_id) = $db_coin->db_fetch_row($result_t)) {

		# Flag first of topic and do link- else- just topic links.
			IF ($_topic_name_last != $t_topic_name) {
				$_topic_link_count				= $_topic_link_count++;
				$_pages_link_count				= 1;
				$topic_count[$_topic_link_count] 	= $_pages_link_count;
				$topic_link[$_topic_link_count] 	= '';
				$topic_link[$_topic_link_count] 	.= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=list&dtopic_id='.$t_topic_id.'">';
				$topic_link[$_topic_link_count] 	.= '<b>'.$t_topic_name.'</b></a>'.$_nl;

			} ELSE {
				$_pages_link_count				= $_pages_link_count++;
				$topic_count[$_topic_link_count] 	= $_pages_link_count;
			}

		# Set last to current
			$_topic_name_last = $t_topic_name;
		}

	# Get Categories Summary
	# Build where string for topic_id and cat_id selection
		$_where_c = ' WHERE '.$_DBCFG['pages'].'.cat_id='.$_DBCFG['categories'].'.cat_id';

	# Do select
		$query_c  = 'SELECT '.$_DBCFG['categories'].'.cat_name, '.$_DBCFG['categories'].'.cat_id';
		$query_c .= ', '.$_DBCFG['categories'].'.cat_desc';
		$query_c .= ', '.$_DBCFG['pages'].'.id, '.$_DBCFG['pages'].'.subject';
		$query_c .= ', '.$_DBCFG['pages'].'.cat_id';
		$query_c .= ' FROM '.$_DBCFG['categories'].', '.$_DBCFG['pages'];
		$query_c .= $_where_c;
		$query_c .= ' ORDER BY '.$_DBCFG['categories'].'.cat_name ASC';

		$result_c		= $db_coin->db_query_execute($query_c);
		$numrows_c	= $db_coin->db_query_numrows($result_c);

	# Loop Category query results
		$_cat_name_last	= '';
		$_cat_link_count	= 0;
		$_pages_link_count	= 0;

		while(list($c_cat_name, $c_cat_id, $c_cat_desc, $p_id, $p_subject, $p_cat_id) = $db_coin->db_fetch_row($result_c)) {

		# Flag first of topic and do link- else- just topic links.
			IF ($_cat_name_last != $c_cat_name) {
				$_cat_link_count			= $_cat_link_count++;
				$_pages_link_count			= 1;
				$cat_count[$_cat_link_count] 	= $_pages_link_count;
				$cat_link[$_cat_link_count] 	= '';
				$cat_link[$_cat_link_count] 	.= '<a href="'.$_SERVER["PHP_SELF"].'?mod=pages&mode=list&dcat_id='.$c_cat_id.'">';
				$cat_link[$_cat_link_count] 	.= '<b>'.$c_cat_name.'</b></a>'.$_nl;

			} ELSE {
				$_pages_link_count			= $_pages_link_count++;
				$cat_count[$_cat_link_count] 	= $_pages_link_count;
			}

		# Set last to current
			$_cat_name_last 	= $c_cat_name;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_PAGES']['Pages_Summary'];

		$_cstr  = '<br>'.$_nl;
		$_cstr .= '<table width="75%" align="center">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0MED_NL" valign="top"><b>'.$_LANG['_PAGES']['l_Entries_By_Topic'].'</b><br>'.$_nl;

	# Loop Topic Array and Print Out HTML
		for ($i = 1; $i <= $_topic_link_count; $i++) {
		# Check for line break after first block
			IF ($i > 0) {$_cstr .= $_sp.$_sp.'-'.$_sp.$topic_link[$i].$_sp.'('.$topic_count[$i].')<br>'.$_nl;}
		}
		$_cstr .= '<br></td><td class="TP0MED_NL" valign="top"><b>'.$_LANG['_PAGES']['l_Entries_By_Category'].'</b><br>'.$_nl;

	# Loop Category Array and Print Out  HTML
		for ($i = 1; $i <= $_cat_link_count; $i++) {
		# Check for line break after first block
			IF ($i > 0) {$_cstr .= $_sp.$_sp.'-'.$_sp.$cat_link[$i].$_sp.'('.$cat_count[$i].')<br>'.$_nl;}
		}
		$_cstr .= '<br></td></tr></table>'.$_nl;

		IF ($_SEC['_sadmin_flg']) {
		# Build function argument text
			$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP14'] == 1) {
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
			}
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit', $_TCFG['_IMG_SELECT_LIST_M'],$_TCFG['_IMG_SELECT_LIST_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

		} ELSE {
		# Build function argument text
			$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

/**************************************************************
 * End Module Functions
**************************************************************/
?>