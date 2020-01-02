<?php
/**
 * Module: Pages (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=pages');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_pages.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_pages_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_pages_override.php');
	}

# Include pages functions file
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
		case "submit":
			break;
		case "summary":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = "summary";
			break;
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
	# Call validate input function
		$err_entry = do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_PERMS['AP16'] != 1 && $_PERMS['AP14'] != 1) {
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
	# Call function for pages summary
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_summary($_GPV['mode'], $data, '1' ).$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: List
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'list') {
	# Call function for pages listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_listing($_GPV['mode'], $data, '1' ).$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: View
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'view') {
	# Call function for page view
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_page_display($_GPV['id'], '1', $_GPV['ss']).$_nl;

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
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1' );

	# Echo final output
		echo $_out;
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

	# Do select and insert
		$query  = 'INSERT INTO '.$_DBCFG['pages'].' (subject, topic_id, cat_id, time_stamp';
		$query .= ', pages_title, pages_code, pages_block_it, pages_status, pages_admin';
		$query .= ', pages_link_menu, pages_link_prev, pages_link_home, pages_link_next)';
		$query .= ' VALUES (';
		$query .= "'".$db_coin->db_sanitize_data($_GPV['subject'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['topic_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['cat_id'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_uts)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_title'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_code'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_block_it'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_admin'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_link_menu'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_link_prev'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_link_home'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['pages_link_next'])."')";

		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_PAGES']['Add_Pages_Entry_Results'].'-'.$_sp.$insert_id.')';
		$_out .= do_subj_block_it($title_text, '1');

	# Adjust Data Array with returned record
		$data['insert_id']		= $insert_id;
		$data['id']			= $insert_id;
		$data['time_stamp'] 	= $_uts;

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
			$query  = 'SELECT * FROM '.$_DBCFG['pages'];
			$query .= ' ORDER BY pages_title DESC';
			$show_list_flag = 1;
		} ELSE {
		# Set Query for select of past id record.
			$query  = 'SELECT * FROM '.$_DBCFG['pages'];
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
			$_tstr = $_LANG['_PAGES']['Pages_Editor'];

		# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
			# Do select form
				$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
				$_cstr .= '<input type="hidden" name="mod" value="pages">'.$_nl;
				$_cstr .= '<input type="hidden" name="mode" value="edit">'.$_nl;
				$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<select class="select_form" name="id" size="1" value="$_GPV[id]" onchange="submit();">'.$_nl;
				$_cstr .= '<option value="0">'.$_LANG['_PAGES']['Please_Select'].'</option>'.$_nl;

			# Process query results (assumes one returned row above)
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
						$_cstr .= '<option value="'.$row['id'].'">'.$row['subject'].' - '.$row['pages_title'].'</option>'.$_nl;
					}
				}

				$_cstr .= '</select>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_PAGES']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</FORM>'.$_nl;

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

			} ELSE {
				$_cstr = '';
				$_mstr  = do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=summary', $_TCFG['_IMG_SUMMARY_M'],$_TCFG['_IMG_SUMMARY_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');
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
				IF ( $_GPV[stage] == 1 && $err_entry['flag'] ) {
				# Call function for Add / Edit form.
					$_out = '<!-- Start content -->'.$_nl;
					$_out .= do_form_add_edit($_GPV['mode'], $data, $err_entry, '1').$_nl;

				# Echo final output
					echo $_out;
				} ELSE {
				# Process query results (assumes one returned row above)
					IF ($numrows) {
					# Process query results
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
	# Call Core DateTime Function (vars, by ref)
		get_udatetime($mydatetime, $mydatetime_display);

	# Do select
		$query = 'UPDATE '.$_DBCFG['pages'].' SET ';
		$query .= "subject='".$db_coin->db_sanitize_data($_GPV['subject'])."', ";
		$query .= "topic_id='".$db_coin->db_sanitize_data($_GPV['topic_id'])."', ";
		$query .= "cat_id='".$db_coin->db_sanitize_data($_GPV['cat_id'])."', ";
		$query .= "pages_title='".$db_coin->db_sanitize_data($_GPV['pages_title'])."', ";
		$query .= "pages_code='".$db_coin->db_sanitize_data($_GPV['pages_code'])."', ";
		$query .= "pages_block_it='".$db_coin->db_sanitize_data($_GPV['pages_block_it'])."', ";
		$query .= "pages_status='".$db_coin->db_sanitize_data($_GPV['pages_status'])."', ";
		$query .= "pages_admin='".$db_coin->db_sanitize_data($_GPV['pages_admin'])."', ";
		$query .= "pages_link_menu='".$db_coin->db_sanitize_data($_GPV['pages_link_menu'])."', ";
		$query .= "pages_link_prev='".$db_coin->db_sanitize_data($_GPV['pages_link_prev'])."', ";
		$query .= "pages_link_home='".$db_coin->db_sanitize_data($_GPV['pages_link_home'])."', ";
		$query .= "pages_link_next='".$db_coin->db_sanitize_data($_GPV['pages_link_next'])."' ";
		$query .= 'WHERE id='.$_GPV['id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_PAGES']['Edit_Pages_Entry_Results'];
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
		$_tstr = $_LANG['_PAGES']['Delete_Pages_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="pages">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_PAGES']['Delete_Pages_Entry_Message'].'='.$_sp.$_GPV['id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= dt_make_datetime($_GPV['time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'-'.$_sp.$_GPV['subject'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="id" value="'.$_GPV['id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_PAGES']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit&id='.$_GPV['id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit', $_TCFG['_IMG_SELECT_LIST_M'],$_TCFG['_IMG_SELECT_LIST_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['pages'].' WHERE id='.$_GPV['id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows = $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_PAGES']['Delete_Pages_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr .= '<center>'.$_LANG['_PAGES']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_PAGES']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'], '');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=edit', $_TCFG['_IMG_SELECT_LIST_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=pages&mode=list', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>