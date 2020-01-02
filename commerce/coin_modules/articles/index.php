<?php
/**
 * Module: Articles (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=articles');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_articles.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_articles_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_articles_override.php');
	}

# Include module functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include module admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php'); }

/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV[mode] and set default to list
	switch($_GPV['mode']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "list":
			break;
		case "summary":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = 'list';
			break;
	}

# Build time_stamp values when edit or add
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'edit') {
		IF ($_GPV['time_stamp_hour'] == '')	{$_GPV['time_stamp_hour'] = 0;}
		IF ($_GPV['time_stamp_minute'] == '')	{$_GPV['time_stamp_minute'] = 0;}
		IF ($_GPV['time_stamp_second'] == '') 	{$_GPV['time_stamp_second'] = 0;}
		IF ($_GPV['time_stamp_year'] == '' || $_GPV['time_stamp_month'] == '' || $_GPV['time_stamp_day'] == '') {
			$_GPV['time_stamp'] = '';
		} ELSE {
			$_GPV['time_stamp'] = mktime($_GPV['time_stamp_hour'],$_GPV['time_stamp_minute'],$_GPV['time_stamp_second'],$_GPV['time_stamp_month'],$_GPV['time_stamp_day'],$_GPV['time_stamp_year']);
		}
		IF ($_GPV['time_stamp'] == '') {$_GPV['time_stamp'] = dt_get_uts();}
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_SEC['_sadmin_flg'] && $_PERMS['AP16'] != 1 && $_PERMS['AP14'] != 1) {
	IF ($_GPV['mode'] == 'add' || $_GPV['mode'] == 'delete' || $_GPV['mode'] == 'edit') {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;

	# Echo final output and exit
		echo $_out;
		exit;
		}
}


##############################
# Mode Call: Summary
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'summary') {
	# Call function for articles summary
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_summary($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: List
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'list') {
	# Call function for articles listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_listing($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: View
# Summary:
#	- View Single Entry
##############################
IF ($_GPV['mode'] == 'view') {
	# Call function for article entry
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_article($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for Add / Edit form.
	# Do admin login test
		IF ($_SEC['_sadmin_flg']) {
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1');

		# Echo final output
			echo $_out;
		}
}


##############################
# Mode Call: Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Call timestamp function
		$_uts = dt_get_uts();

	# Do select
		$query  = 'INSERT INTO '.$_DBCFG['articles'].' (';
		$query .= 'subject, topic_id, cat_id, time_stamp, entry, auto_nl2br';
		$query .= ') VALUES (';
		$query .= "'".$db_coin->db_sanitize_data($_GPV['subject'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['topic_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cat_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['time_stamp'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['entry'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['auto_nl2br'])."'";
		$query .= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ARTICLES']['Add_Articles_Entry_Results'].'- '.$insert_id.')';
		$_out .= do_subj_block_it($title_text, '1');

	# Adjust Data Array with returned record
		$data['insert_id']		= $insert_id;
		$data['id']			= $insert_id;
	#	$data['time_stamp'] 	= $_uts;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= do_display_entry($_GPV['mode'], $data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for $_GPV[id] no- will determine select string (one for edit, all for list)
		IF (!$_GPV['id'] || $_GPV['id'] == 0) {
		# Set Query for select.
			$query  = 'SELECT * FROM '.$_DBCFG['articles'];
			$query .= ' ORDER BY time_stamp DESC';
			$show_list_flag = 1;

		} ELSE {
		# Set Query for select of passed id record.
			$query  = 'SELECT * FROM '.$_DBCFG['articles'];
			$query .= ' WHERE id='.$_GPV['id'];
			$show_list_flag = 0;
		}

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check flag- condition is show list
		IF ($show_list_flag) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ARTICLES']['Articles_Editor'];

		# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
			# Do select form
				$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
				$_cstr .= '<input type="hidden" name="mod" value="articles">'.$_nl;
				$_cstr .= '<input type="hidden" name="mode" value="edit">'.$_nl;
				$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<select class="select_form" name="id" size="1" value="$_GPV[id]" onchange="submit();">'.$_nl;
				$_cstr .= '<option value="0">'.$_LANG['_ARTICLES']['Please_Select'].'</option>'.$_nl;

			# Process query results (assumes one returned row above)
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
						$_cstr .= '<option value="'.$row["id"].'">'.dt_make_datetime($row["time_stamp"], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).' - '.$row["subject"].'</option>'.$_nl;
					}
				}

				$_cstr .= '</select>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_ARTICLES']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</FORM>'.$_nl;

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			} ELSE {
				$_cstr = '';
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
			}

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;

		} #if flag_list set

	# Check flag- condition is not show list
		IF (!$show_list_flag) {
		# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
			# If Stage and Error Entry, pass field vars to form,
			# Otherwise, pass looked up record to form
				IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
					$_out = '<!-- Start content -->'.$_nl;
					$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;
					echo $_out;
				} ELSE {
					IF ($numrows) {
						while ($row = $db_coin->db_fetch_array($result)) {
						# Merge Data Array with returned row
							$data_new	= array_merge($data, $row);
							$data	= $data_new;
						}
					}

				# Call function for Add / Edit form.
					$_out = '<!-- Start content -->'.$_nl;
					$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;

				# Echo final output
					echo $_out;
				}
			}
		}
}


##############################
# Mode Call: Edit Entry Results
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query 	 = 'UPDATE '.$_DBCFG['articles'].' SET ';
		$query 	.= "subject='".$db_coin->db_sanitize_data($_GPV['subject'])."', ";
		$query 	.= "topic_id='".$db_coin->db_sanitize_data($_GPV['topic_id'])."', ";
		$query 	.= "cat_id='".$db_coin->db_sanitize_data($_GPV['cat_id'])."', ";
		$query 	.= "time_stamp='".$db_coin->db_sanitize_data($_GPV['time_stamp'])."', ";
		$query 	.= "entry='".$db_coin->db_sanitize_data($_GPV['entry'])."', ";
		$query 	.= "auto_nl2br='".$db_coin->db_sanitize_data($_GPV['auto_nl2br'])."'";
		$query 	.= ' WHERE id='.$_GPV['id'];

		$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ARTICLES']['Edit_Articles_Entry_Results'];
		$_out .= do_subj_block_it($title_text, '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= do_display_entry($_GPV['mode'], $data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 1) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ARTICLES']['Delete_Articles_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="articles">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ARTICLES']['Delete_Articles_Entry_Message'].'= '.$_GPV[id].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;

		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= dt_make_datetime($_GPV['time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'  - '.$_GPV['subject'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="id" value="'.$_GPV['id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ARTICLES']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=edit&id='.$_GPV['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['articles'].' WHERE id='.$_GPV['id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ARTICLES']['Delete_Articles_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr .= '<center>'.$_LANG['_ARTICLES']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_ARTICLES']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=articles', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

/**************************************************************
 * End Module Code
**************************************************************/
?>