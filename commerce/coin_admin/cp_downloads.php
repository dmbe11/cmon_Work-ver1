<?php
/**
 * Admin: Downloads
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Downloads
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_admin.php
 */


# Code to handle file being loaded by URL
	IF (!eregi('admin.php', $_SERVER['PHP_SELF'])) {
		require_once('../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=admin.php?cp=downloads');
		exit();
	}


/**************************************************************
 * CP Base Code
**************************************************************/
	$err_entry = Array("flag" => 0);

# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Check $_GPV['op'] (operation switch)
	switch($_GPV['op']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "view":
			break;
		default:
			$_GPV['op'] = 'none';
			break;
	} #end op switch


# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = cp_do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;

##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_PERMS['AP16'] != 1 && $_PERMS['AP11'] != 1) {
	IF ($_PERMS['AP10'] == 1) {
		$_GPV['op'] = 'view';
	} ELSE {
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit();
	}
}


##############################
# Operation:	View Entry
# Summary:
#	- For viewing entry
#	- Must preceed "none"
##############################
IF ($_GPV['op'] == 'view') {
	# Check for valid $_GPV['dload_id'] no
	IF ($_GPV['dload_id']) {
	# Set Query for select.
		$query	 = 'SELECT * FROM '.$_DBCFG['downloads'];
		$query	.= ' WHERE dload_id='.$_GPV['dload_id'];

	# Do select
		$result	 = $db_coin->db_query_execute($query);
		$numrows	 = $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above)
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
			# Merge Data Array with returned row
				$data_new	= array_merge($data, $row);
				$data	= $data_new;
			}
		}

	# Call function for displaying item
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_display_entry_downloads($data, '1').$_nl;

	# Echo final output
		echo $_out;

	} ELSE {
		$_GPV['op'] = 'none';
	}
}


##############################
# Operation:	None
# Summary:
#	- For loading select menu.
#	- For no actions specified.
##############################
IF ($_GPV['op'] == 'none') {
	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Downloads_Editor'];

	# Call function for create select form: Downloads
		$_cstr = cp_do_select_form_downloads($_SERVER["PHP_SELF"].'?cp=downloads&op=edit', 'dload_id', $_GPV['dload_id'], '1');

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP11'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		}

	# Call block it function
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Add Entry
# Summary:
#	- For intial entry
#	- For re-entry on error
##############################
IF ($_GPV['op'] == 'add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_downloads($data, $err_entry, '1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Add Entry Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_GPV['op'] == 'add' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query  = 'INSERT INTO '.$_DBCFG['downloads'].' (';
		$query .= 'dload_group, dload_name, dload_desc, dload_count, dload_date_str, dload_avail, dload_filename, dload_filesize, dload_contributor';
		$query .= ') VALUES (';
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_group'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_name'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_desc'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_count'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_date_str'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_avail'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_filename'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_filesize'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['dload_contributor'])."'";
		$query .= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['dload_id']	= $insert_id;

	# Call function for Display Entry
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= '<br>'.$_nl;
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Add_Downloads_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')', '1');
		$_out .= cp_do_display_entry_downloads( $data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Edit Entry
# Summary:
#	- For editing entry
#	- For re-editing on error
##############################
IF ($_GPV['op'] == 'edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# If Stage and Error Entry, pass field vars to form,
	# Otherwise, pass looked up record to form
	IF ( $_GPV['stage'] == 1 && $err_entry['flag'] ) {
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_downloads($data, $err_entry, '1').$_nl;

	} ELSE {
	# Check for valid $_GPV['dload_id'] no
		IF ( $_GPV['dload_id'] ) {
		# Set Query for select.
			$query	 = 'SELECT * FROM '.$_DBCFG['downloads'];
			$query	.= ' WHERE dload_id='.$_GPV['dload_id'];

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results (assumes one returned row above)
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Merge Data Array with returned row
					$data_new	= array_merge($data, $row);
					$data	= $data_new;
				}
			}

		# Call function for add/edit form
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_downloads($data, $err_entry, '1').$_nl;

		} ELSE {
		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Downloads_Editor'];

		# Call function for create select form: Downloads
			$_cstr	 = cp_do_form_add_edit_downloads($_SERVER["PHP_SELF"].'?cp=downloads&op=edit', 'dload_id', $_GPV['dload_id'], '1');

			$_mstr	 = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
			$_mstr	.= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');

		# Call block it function
			$_out  = '<!-- Start content -->'.$_nl;
			$_out  = do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}
	}

	echo $_out;

}


##############################
# Operation: 	Edit Entry Results
# Summary:
#	- For processing edited entry
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'edit' && $_GPV['stage'] == 1 && !$err_entry['flag']) {
	# Do select
		$query  = 'UPDATE '.$_DBCFG['downloads'].' SET ';
		$query .= "dload_name='".$db_coin->db_sanitize_data($_GPV['dload_name'])."', ";
		$query .= "dload_desc='".$db_coin->db_sanitize_data($_GPV['dload_desc'])."', ";
		$query .= "dload_group='".$db_coin->db_sanitize_data($_GPV['dload_group'])."', ";
		$query .= "dload_count='".$db_coin->db_sanitize_data($_GPV['dload_count'])."', ";
		$query .= "dload_date_str='".$db_coin->db_sanitize_data($_GPV['dload_date_str'])."', ";
		$query .= "dload_avail='".$db_coin->db_sanitize_data($_GPV['dload_avail'])."', ";
		$query .= "dload_filename='".$db_coin->db_sanitize_data($_GPV['dload_filename'])."', ";
		$query .= "dload_filesize='".$db_coin->db_sanitize_data($_GPV['dload_filesize'])."', ";
		$query .= "dload_contributor='".$db_coin->db_sanitize_data($_GPV['dload_contributor'])."' ";
		$query .= 'WHERE dload_id='.$_GPV['dload_id'];
		$result = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Call function for Display Entry
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_subj_block_it($_LANG['_ADMIN']['Edit_Downloads_Entry_Results'], '1');
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_downloads($data, '1').$_nl;
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: 	Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 1) {

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Downloads_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="downloads">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="dload_id" value="'.$_GPV['dload_id'].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Downloads_Entry_Message'].$_sp.'='.$_sp.$_GPV['dload_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '-'.$_sp.$_GPV['dload_name'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1');
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=edit&dload_id='.$_GPV['dload_id'], $_TCFG['_IMG_EDIT_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {

	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['downloads'].' WHERE dload_id='.$_GPV['dload_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Downloads_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr = '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr = '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}



/**************************************************************
 * CP Functions Code
**************************************************************/
# Do Data Input Validate
function cp_do_input_validation($adata) {
	# Dim some Vars:
		$err_entry = Array("flag" => 0);

	# Check modes and data as required
	#	IF (!$adata['dload_id'])			{$err_entry['flag'] = 1; $err_entry['dload_id'] = 1;}
		IF (!$adata['dload_group'])		{$err_entry['flag'] = 1; $err_entry['dload_group'] = 1;}
		IF (!$adata['dload_name'])		{$err_entry['flag'] = 1; $err_entry['dload_name'] = 1;}
		IF (!$adata['dload_desc'])		{$err_entry['flag'] = 1; $err_entry['dload_desc'] = 1;}
	#	IF (!$adata['dload_count'])		{$err_entry['flag'] = 1; $err_entry['dload_count'] = 1;}
		IF (!$adata['dload_date_str'])	{$err_entry['flag'] = 1; $err_entry['dload_date_str'] = 1;}
	#	IF (!$adata['dload_avail'])		{$err_entry['flag'] = 1; $err_entry['dload_avail'] = 1;}
		IF (!$adata['dload_filename'])	{$err_entry['flag'] = 1; $err_entry['dload_filename'] = 1;}
		IF (!$adata['dload_filesize'])	{$err_entry['flag'] = 1; $err_entry['dload_filesize'] = 1;}
	#	IF (!$adata['dload_contributor'])	{$err_entry['flag'] = 1; $err_entry['dload_contributor'] = 1;}

	# Return results
		return $err_entry;
}


# Do list field form for: Mail Template
function cp_do_select_form_downloads($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT dload_id, dload_group, dload_name FROM '.$_DBCFG['downloads'].' ORDER BY dload_group ASC, dload_name ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_out .= '<input type="hidden" name="cp" value="downloads">'.$_nl;
		$_out .= '<input type="hidden" name="op" value="edit">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l20_Download_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($dload_id, $dload_group, $dload_name) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$dload_id.'">'.$dload_group.' - '.$dload_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function cp_do_form_add_edit_downloads($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build op dependent strings
		switch ($adata['op']) {
			case "add":
				$op_proper	= $_LANG['_ADMIN']['B_Add'];
				$op_button	= $_LANG['_ADMIN']['B_Add'];
				break;
			case "edit":
				$op_proper	= $_LANG['_ADMIN']['B_Edit'];
				$op_button	= $_LANG['_ADMIN']['B_Save'];
				break;
			default:
				$adata['op']	= 'add';
				$op_proper	= $_LANG['_ADMIN']['B_Add'];
				$op_button	= $_LANG['_ADMIN']['B_Add'];
				break;
		}

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $op_proper.$_sp.$_LANG['_ADMIN']['Download_Entry'].$_sp.'('.$_LANG['_ADMIN']['all_fields_required'].')';
		$_cstr = '';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_ADMIN']['AD_ERR00__HDR1'].'<br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'<br>';

	 		IF ($aerr_entry['dload_id']) 			{$err_str .= $_LANG['_ADMIN']['AD20_ERR_01']; $err_prv = 1;}
			IF ($aerr_entry['dload_group']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_02']; $err_prv = 1;}
			IF ($aerr_entry['dload_name']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_03']; $err_prv = 1;}
			IF ($aerr_entry['dload_desc']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_04']; $err_prv = 1;}
			IF ($aerr_entry['dload_count']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_05']; $err_prv = 1;}
			IF ($aerr_entry['dload_date_str'])		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_06']; $err_prv = 1;}
			IF ($aerr_entry['dload_avail']) 		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_07']; $err_prv = 1;}
			IF ($aerr_entry['dload_filename'])		{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_08']; $err_prv = 1;}
			IF ($aerr_entry['dload_filesize']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_09']; $err_prv = 1;}
			IF ($aerr_entry['dload_contributor']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_ADMIN']['AD20_ERR_10']; $err_prv = 1;}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ( $adata['op'] == 'add' ) {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['dload_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['dload_avail'] == '') {$adata['dload_avail'] = 0;}
		$_cstr .= do_select_list_no_yes('dload_avail', $adata['dload_avail'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Group'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_group" SIZE=50 value="'.htmlspecialchars($adata['dload_group']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_name" SIZE=50 value="'.htmlspecialchars($adata['dload_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l20_Download_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="dload_desc" COLS="'.$_cols.'" ROWS="15">'.$adata['dload_desc'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_date_str" SIZE=50 value="'.htmlspecialchars($adata['dload_date_str']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_FileName'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_filename" SIZE=50 value="'.htmlspecialchars($adata['dload_filename']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_FileSize'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_filesize" SIZE=10 value="'.htmlspecialchars($adata['dload_filesize']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Contributor'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_contributor" SIZE=50 value="'.htmlspecialchars($adata['dload_contributor']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Count'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="dload_count" SIZE=10 value="'.htmlspecialchars($adata['dload_count']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NC" width="100%" colspan="2">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="downloads">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="dload_id" value="'.$adata['dload_id'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $op_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out  = do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_downloads($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_colsp2	= '<td class="TP1SML_NJ" width="25%" colspan="2">';
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['dload_name'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_avail'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Group'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_group'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_name'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l20_Download_Description'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_desc'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Date'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_date_str'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_FileName'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_filename'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_FileSize'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_filesize'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Contributor'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_contributor'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l20_Download_Count'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['dload_count'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP11'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=edit&dload_id='.$adata['dload_id'], $_TCFG['_IMG_EDIT_M'],'');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads&op=add', $_TCFG['_IMG_ADD_NEW_M'],'');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=downloads', $_TCFG['_IMG_LISTING_M'],'');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}
?>