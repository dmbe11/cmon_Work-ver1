<?php
/**
 * Module: FAQ (Main)
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
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=faq');
		exit;
	}

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_faq.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_faq_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_faq_override.php');
	}

# Include module functions file
	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');

# Include module admin functions file if admin
	IF ($_SEC['_sadmin_flg']) {require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_admin.php');}

/**************************************************************
 * Module Code
**************************************************************/
# Check $_GPV[mode] and set default to list
	switch($_GPV['mode']) {
		case "list":
			break;
		case "summary":
			break;
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "submit":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['mode'] = 'delete';}
			break;
		case "delete":
			break;
		case "show":
			break;
		case "view":
			break;
		default:
			$_GPV['mode'] = "summary";
			break;
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
# Mode Call: 	All modes
# Summary:
#	- Check if login required
##############################
IF (!$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;
}

##############################
# Mode Call:	Summary
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'summary') {
	# Call function for FAQ listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_summary($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	Show FAQ
# Summary:
#	- List entries
##############################
IF ($_GPV['mode'] == 'show') {
	# Call function for show a FAQ
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_faq($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	View
# Summary:
#	- View Single Entry
##############################
IF ($_GPV['mode'] == 'view') {
	# Call function for show a FAQ QA
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_display_faqqa($_GPV['mode'], $data, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call:	Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for Add / Edit form.
	# Do admin login test
		IF ($_SEC['_sadmin_flg']) {
		# Check object for edit
			IF ($_GPV['obj'] == 'faq') {
				$_out  = '<!-- Start content -->'.$_nl;
				$_out .= do_form_add_edit_faq($_GPV['mode'], $data, $err_entry, '1');
			}
			IF ($_GPV['obj'] == 'faqqa') {
				$_out  = '<!-- Start content -->'.$_nl;
				$_out .= do_form_add_edit_faqqa($_GPV['mode'], $data, $err_entry, '1');
			}

		# Echo final output
			echo $_out;
		}
}


##############################
# Mode Call: 	Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Call timestamp function
		$_uts = dt_get_uts();

	# Check object for edit
		IF ($_GPV['obj'] == 'faq') {
		# Do Object=FAQ Items
			$title_text = $_LANG['_FAQ']['Add_FAQ'];

			$query  = 'INSERT INTO '.$_DBCFG['faq'].' (';
			$query .= 'faq_position, faq_time_stamp_mod, faq_status';
			$query .= ', faq_admin, faq_user';
			$query .= ', faq_title, faq_descrip';
			$query .= ') VALUES (';
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_position'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_uts)."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_status'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_admin'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_user'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_title'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faq_descrip'])."'";
			$query .= ')';
		}

		IF ($_GPV['obj'] == 'faqqa') {
		# Do Object=FAQ QA Items
			$title_text = $_LANG['_FAQ']['Add_FAQ_QA'];

			$query  = 'INSERT INTO '.$_DBCFG['faq_qa'].' (';
			$query .= 'faqqa_faq_id, faqqa_position, faqqa_time_stamp_mod';
			$query .= ', faqqa_status, faqqa_question, faqqa_answer, faqqa_auto_nl2br';
			$query .= ') VALUES (';
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_faq_id'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_position'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_uts)."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_status'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_question'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_answer'])."', ";
			$query .= "'".$db_coin->db_sanitize_data($_GPV['faqqa_auto_nl2br'])."'";
			$query .= ')';
		}

	# Do select
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text .= $_sp.$_LANG['_FAQ']['Entry_Results'].'('.$_LANG['_FAQ']['Inserted_ID'].'-'.$_sp.$insert_id.')';
		$_out .= do_subj_block_it($title_text, '1');

	# Adjust Data Array with returned record
		$data['insert_id']			= $insert_id;
		$data['faq_id']			= $insert_id;
		$data['faq_time_stamp_mod']	= $_uts;
		$data['faqqa_id']			= $insert_id;
		$data['faqqa_time_stamp_mod']	= $_uts;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= do_display_entry($_GPV['mode'], $data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Mode Call: 	Edit Entry
# Object:		FAQ
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && $_GPV['obj'] == 'faq' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for $id no- will determine select string (one for edit, all for list)
		IF (!$_GPV['faq_id'] || $_GPV['faq_id'] == 0) {
		# Set Query for select.
			$_where_t = '';
			$show_list_flag = 1;

		} ELSE {
		# Set Query for select of past id record.
			$_where_t = ' WHERE faq_id='.$_GPV['faq_id'];
			$show_list_flag = 0;
		}

	# Set Query for select.
		$query  = 'SELECT * FROM '.$_DBCFG['faq'];
		$query .= $_where_t;
		$query .= ' ORDER BY faq_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check flag- condition is show list
		IF ($show_list_flag) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_FAQ']['FAQ_Editor'];

		# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
			# Do select form
				$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
				$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
				$_cstr .= '<input type="hidden" name="mode" value="edit">'.$_nl;
				$_cstr .= '<input type="hidden" name="obj" value="faq">'.$_nl;
				$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<select class="select_form" name="faq_id" size="1" value="$_GPV[faq_id]" onchange="submit();">'.$_nl;
				$_cstr .= '<option value="0">Please Select</option>'.$_nl;

			# Process query results (assumes one returned row above)
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
						$_cstr .= '<option value="'.$row["faq_id"].'">'.dt_make_datetime($row["faq_time_stamp_mod"], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'-'.$_sp.htmlspecialchars($row['faq_title']).'</option>'.$_nl;
					}
				}

				$_cstr .= '</select>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_FAQ']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</FORM>'.$_nl;

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faq', $_TCFG['_IMG_FAQ_ADD_FAQ_M'],$_TCFG['_IMG_FAQ_ADD_FAQ_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

			} ELSE {
				$_cstr = '';
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
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
					$_out .= do_form_add_edit_faq($_GPV['mode'], $data, $err_entry, '1').$_nl;
					echo $_out;

				} ELSE {
				# Process query results (assumes one returned row above)
					IF ($numrows) {
						while ($row = $db_coin->db_fetch_array($result)) {
						# Merge Data Array with returned row
							$data_new	= array_merge( $data, $row );
							$data	= $data_new;
						}
					}

				# Call function for Add / Edit form.
					$_out = '<!-- Start content -->'.$_nl;
					$_out .= do_form_add_edit_faq($_GPV['mode'], $data, $err_entry, '1').$_nl;

				# Echo final output
					echo $_out;
				}
			}
		}
}


##############################
# Mode Call:	Edit Entry
# Object:		FAQ QA
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'edit' && $_GPV['obj'] == 'faqqa' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Check for $id no- will determine select string (one for edit, all for list)
		IF (!$_GPV['faqqa_id'] || $_GPV['faqqa_id'] == 0) {
		# Set Query for select.
			$_where_t = '';
			$show_list_flag = 1;

		} ELSE {
		# Set Query for select of past id record.
			$_where_t = ' WHERE faqqa_id='.$_GPV['faqqa_id'];
			$show_list_flag = 0;
		}

	# Set Query for select.
		$query  = 'SELECT * FROM '.$_DBCFG['faq_qa'];
		$query .= $_where_t;
		$query .= ' ORDER BY faqqa_faq_id ASC, faqqa_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check flag- condition is show list
		IF ($show_list_flag) {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_FAQ']['FAQ_QA_Editor'];

		# Do admin login test
			IF ($_SEC['_sadmin_flg']) {
			# Do select form
				$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
				$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
				$_cstr .= '<input type="hidden" name="mode" value="edit">'.$_nl;
				$_cstr .= '<input type="hidden" name="obj" value="faqqa">'.$_nl;
				$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= '<select class="select_form" name="faqqa_id" size="1" value="'.$_GPV['faqqa_id'].'" onchange="submit();">'.$_nl;
				$_cstr .= '<option value="0">Please Select</option>'.$_nl;

			# Process query results (assumes one returned row above)
				IF ($numrows) {
					while ($row = $db_coin->db_fetch_array($result)) {
						$_cstr .= '<option value="'.$row["faqqa_id"].'">'.dt_make_datetime($row["faqqa_time_stamp_mod"], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'-'.$_sp.htmlspecialchars($row["faqqa_question"]).'</option>'.$_nl;
					}
				}

				$_cstr .= '</select>'.$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
				$_cstr .= do_input_button_class_sw ('b_load', 'SUBMIT', $_LANG['_FAQ']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
				$_cstr .= '</td></tr>'.$_nl;
				$_cstr .= '</table>'.$_nl;
				$_cstr .= '</FORM>'.$_nl;

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=add&obj=faqqa', $_TCFG['_IMG_FAQ_ADD_QA_M'],$_TCFG['_IMG_FAQ_ADD_QA_M_MO'],'','');
				$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

			} ELSE {
				$_cstr = '';
				$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');
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
					$_out .= do_form_add_edit_faqqa($_GPV['mode'], $data, $err_entry, '1').$_nl;
					echo $_out;

				} ELSE {
				# Process query results (assumes one returned row above)
					IF ($numrows) {
						while ($row = $db_coin->db_fetch_array($result)) {
						# Merge Data Array with returned row
							$data_new	= array_merge($data, $row);
							$data	= $data_new;
						}
					}

				# Call function for Add / Edit form.
					$_out = '<!-- Start content -->'.$_nl;
					$_out .= do_form_add_edit_faqqa($_GPV['mode'], $data, $err_entry, '1').$_nl;

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
	# Call timestamp function
		$_uts = dt_get_uts();

	# Check object for edit
		IF ($_GPV['obj'] == 'faq') {
		# Do Object=FAQ Items
			$title_text = $_LANG['_FAQ']['Edit_FAQ'];

			$query  = 'UPDATE '.$_DBCFG['faq'].' SET ';
			$query .= "faq_position='".$db_coin->db_sanitize_data($_GPV['faq_position'])."', ";
			$query .= "faq_time_stamp_mod='".$db_coin->db_sanitize_data($_uts)."', ";
			$query .= "faq_status='".$db_coin->db_sanitize_data($_GPV['faq_status'])."', ";
			$query .= "faq_admin='".$db_coin->db_sanitize_data($_GPV['faq_admin'])."', ";
			$query .= "faq_user='".$db_coin->db_sanitize_data($_GPV['faq_user'])."', ";
			$query .= "faq_title='".$db_coin->db_sanitize_data($_GPV['faq_title'])."', ";
			$query .= "faq_descrip='".$db_coin->db_sanitize_data($_GPV['faq_descrip'])."' ";
			$query .= 'WHERE faq_id='.$_GPV['faq_id'];
		}

		IF ($_GPV['obj'] == 'faqqa') {
		# Do Object=FAQ QA Items
			$title_text = $_LANG['_FAQ']['Edit_FAQ_QA'];

			$query  = 'UPDATE '.$_DBCFG['faq_qa'].' SET ';
			$query .= "faqqa_faq_id='".$db_coin->db_sanitize_data($_GPV['faqqa_faq_id'])."', ";
			$query .= "faqqa_position='".$db_coin->db_sanitize_data($_GPV['faqqa_position'])."', ";
			$query .= "faqqa_time_stamp_mod='".$db_coin->db_sanitize_data($_uts)."', ";
			$query .= "faqqa_status='".$db_coin->db_sanitize_data($_GPV['faqqa_status'])."', ";
			$query .= "faqqa_question='".$db_coin->db_sanitize_data($_GPV['faqqa_question'])."', ";
			$query .= "faqqa_answer='".$db_coin->db_sanitize_data($_GPV['faqqa_answer'])."', ";
			$query .= "faqqa_auto_nl2br='".$db_coin->db_sanitize_data($_GPV['faqqa_auto_nl2br'])."'";
			$query .= 'WHERE faqqa_id='.$_GPV['faqqa_id'];
		}

	# Do select
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

		$_out = '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $title_text.$_sp.$_LANG['_FAQ']['Entry_Results'];
		$_out .= do_subj_block_it($title_text, '1');

	# Adjust Data Array with returned record
		$data['faq_time_stamp_mod']	= $_uts;
		$data['faqqa_time_stamp_mod']	= $_uts;

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

	# Check object for edit
		IF ($_GPV['obj'] == 'faq') {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_FAQ']['Delete_FAQ_Entry_Confirmation'];

		# Do confirmation form to content string
			$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="faq">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_FAQ']['Delete_FAQ_Entry_Message'].'='.$_sp.$_GPV['faq_id'].'?</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= dt_make_datetime($_GPV['faq_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'-'.$_sp.$_GPV['faq_title'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="faq_id" value="'.$_GPV['faq_id'].'">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_FAQ']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faq&faq_id='.$_GPV['faq_id'], $_TCFG['_IMG_FAQ_EDIT_FAQ_M'],$_TCFG['_IMG_FAQ_EDIT_FAQ_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}

		IF ($_GPV['obj'] == 'faqqa') {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_FAQ']['Delete_FAQ_QA_Entry_Confirmation'];

		# Do confirmation form to content string
			$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
			$_cstr .= '<input type="hidden" name="mod" value="faq">'.$_nl;
			$_cstr .= '<input type="hidden" name="mode" value="delete">'.$_nl;
			$_cstr .= '<input type="hidden" name="obj" value="faqqa">'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<b>'.$_LANG['_FAQ']['Delete_FAQ_QA_Entry_Message'].'='.$_sp.$_GPV['faqqa_id'].'?</b>'.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= dt_make_datetime($_GPV['faqqa_time_stamp_mod'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_sp.$_sp.'-'.$_sp.$_GPV['faqqa_question'].$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
			$_cstr .= '<INPUT TYPE=hidden name="faqqa_id" value="'.$_GPV['faqqa_id'].'">'.$_nl;
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_FAQ']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</FORM>'.$_nl;

			$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faqqa&faqqa_id='.$_GPV['faqqa_id'], $_TCFG['_IMG_FAQ_EDIT_QA_M'],$_TCFG['_IMG_FAQ_EDIT_QA_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
		}
}

IF ($_SEC['_sadmin_flg'] && $_GPV['mode'] == 'delete' && $_GPV['stage'] == 2) {
	# Check object for edit
		IF ($_GPV['obj'] == 'faq') {
		# Do Object=FAQ Items
			$title_text = $_LANG['_FAQ']['Delete_FAQ'];
			$query = 'DELETE FROM '.$_DBCFG['faq'].' WHERE faq_id='.$_GPV['faq_id'];
		}

		IF ($_GPV['obj'] == 'faqqa') {
		# Do Object=FAQ QA Items
			$title_text = $_LANG['_FAQ']['Delete_FAQ_QA'];
			$query = 'DELETE FROM '.$_DBCFG['faq_qa'].' WHERE faqqa_id='.$_GPV['faqqa_id'];
		}

	# Do select
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $title_text.$_sp.$_LANG['_FAQ']['Entry_Results'];

		IF (!$eff_rows) {
			$_cstr .= '<center>'.$_LANG['_FAQ']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_FAQ']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faq', $_TCFG['_IMG_FAQ_EDIT_FAQ_M'],$_TCFG['_IMG_FAQ_EDIT_FAQ_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq&mode=edit&obj=faqqa', $_TCFG['_IMG_FAQ_EDIT_QA_M'],$_TCFG['_IMG_FAQ_EDIT_QA_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=faq', $_TCFG['_IMG_FAQ_M'],$_TCFG['_IMG_FAQ_M_MO'],'','');

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