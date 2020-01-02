<?php
/**
 * Admin: Suppliers
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Suppliers
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
		html_header_location('error.php?err=01&url=admin.php?cp=suppliers');
		exit;
	}



/**************************************************************
 * CP Functions Code
**************************************************************/
# Determine the current s_id
function do_get_max_s_id() {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;
		$max_s_id = $_CCFG['BASE_SUPPLIER_ID'];

	# Set Query and select for max field value.
		$query	= 'SELECT max(s_id) FROM '.$_DBCFG['suppliers'];
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result) ) {

		# Get Max Value
			while(list($_max_id) = $db_coin->db_fetch_row($result)) {$max_s_id = $_max_id;}
		}

	# Check / Set Value for return
		return $max_s_id;
}


# Get client contact info
function get_contact_supplier_info($acc_s_id) {
	# Dim some Vars
		global $_DBCFG, $db_coin;
		$_cinfo = array("s_id" => 0, "s_name_first" => '', "s_name_last" => '', "s_company" => '', "s_email" => '');

	# Set Query for select and execute
		$query	 = 'SELECT s_id, s_name_first, s_name_last, s_company, s_email FROM '.$_DBCFG['suppliers'];
		$query	.= ' WHERE s_id='.$acc_s_id.' ORDER BY s_company ASC, s_name_last ASC, s_name_first ASC';
		$result	= $db_coin->db_query_execute($query);
		IF ($db_coin->db_query_numrows($result)) {
			while(list($s_id, $s_name_first, $s_name_last, $s_company, $s_email) = $db_coin->db_fetch_row($result)) {
				$_cinfo['s_id']		= $s_id;
				$_cinfo['s_name_first']	= $s_name_first;
				$_cinfo['s_name_last']	= $s_name_last;
				$_cinfo['s_company']	= $s_company;
				$_cinfo['s_email']		= $s_email;
			}
		}
		return $_cinfo;
}

# Get client contact info for additional email addresses
function get_contact_supplier_info_alias($alias_id, $idtype) {
	# Dim some Vars
		global $_DBCFG, $db_coin;

	# Set Query for select and execute
		$query = 'SELECT contacts_id, contacts_s_id, contacts_name_first, contacts_name_last, contacts_email FROM '.$_DBCFG['suppliers_contacts'];
		IF ($idtype) {
			$query .= ' WHERE contacts_s_id='.$alias_id;
		} ELSE {
			$query .= ' WHERE contacts_id='.$alias_id;
		}
		$query	.= ' ORDER BY contacts_name_last, contacts_name_first ASC';
		$result	= $db_coin->db_query_execute($query);

	# Get value and set return
		$x=0;
		while(list($contact_id, $s_id, $s_name_first, $s_name_last, $s_email) = $db_coin->db_fetch_row($result)) {
			$x++;
			$_cinfo[$x]['contact_id']	= $contact_id;
			$_cinfo[$x]['s_id']			= $s_id;
			$_cinfo[$x]['s_name_first']	= $s_name_first;
			$_cinfo[$x]['s_name_last']	= $s_name_last;
			$_cinfo[$x]['s_company']		= '';
			$_cinfo[$x]['s_email']		= $s_email;
		}

		return $_cinfo;
}

# Delete an "additional email"
function do_delete_additional_email($s_id, $contacts_id) {
	# Dim some Vars:
		global $_DBCFG, $db_coin;

	# Do purge contact
		$query	= 'DELETE FROM '.$_DBCFG['suppliers_contacts'].' WHERE contacts_id='.$contacts_id.' AND contacts_s_id='.$s_id;
		$db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

# Insert an "additional email"
function do_insert_additional_email($s_id,$fname,$lname,$email) {
	# Dim some Vars:
		global $_DBCFG, $db_coin;

	# Do purge contact
		$query 	 = 'INSERT INTO '.$_DBCFG['suppliers_contacts'];
		$query	.= ' (contacts_id, contacts_s_id, contacts_name_first, contacts_name_last, contacts_email)';
		$query	.= "VALUES ('', ";
		$query	.= "'".$db_coin->db_sanitize_data($s_id)."', ";
		$query	.= "'".$db_coin->db_sanitize_data($fname)."', ";
		$query	.= "'".$db_coin->db_sanitize_data($lname)."', ";
		$query	.= "'".$db_coin->db_sanitize_data($email)."')";
		$db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

# Update an "additional email"
function do_update_additional_email($s_id,$fname,$lname,$email,$contacts_id) {
	# Dim some Vars:
		global $_DBCFG, $db_coin;

	# Do purge contact
		$query 	 = "UPDATE ".$_DBCFG['suppliers_contacts']." SET ";
		$query	.= "contacts_name_first='".$db_coin->db_sanitize_data($fname)."', ";
		$query	.= "contacts_name_last='".$db_coin->db_sanitize_data($lname)."', ";
		$query	.= "contacts_email='".$db_coin->db_sanitize_data($email)."' ";
		$query	.= 'WHERE contacts_id='.$contacts_id.' AND contacts_s_id='.$s_id;
		$db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}

# Build a form for add/edit/delete additional supplier emails
function do_form_additional_emails($s_id) {
	# Dim some Vars:
		global $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / col strings (reduce text)
		$_td_str_left	= '<td class="TP1SML_NL">'.$_nl;
		$_td_str_ctr	= '<td class="TP1SML_NC">'.$_nl;

	# Build table row beginning and ending
		$_cstart  = '<b>'.$_LANG['_ADMIN']['l_Email_Address_Additional'].$_sp.'</b><br>'.$_nl;
		$_cstart .= '<table border="0" cellpadding="3" cellspacing="0"><tr>'.$_nl;
		$_cstart .= $_td_str_left.$_LANG['_ADMIN']['l_First_Name'].'</td>'.$_nl;
		$_cstart .= $_td_str_left.$_LANG['_ADMIN']['l_Last_Name'].'</td>'.$_nl;
		$_cstart .= $_td_str_left.$_LANG['_ADMIN']['l_Email_Address'].'</td>'.$_nl;
		$_cstart .= $_td_str_ctr.$_LANG['_CCFG']['Actions'].'</td></tr>'.$_nl;

		$_cend    = '</table>'.$_nl;

	# Set Query for select (additional emails).
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['suppliers_contacts'];
		$query .= ' WHERE contacts_s_id='.$s_id;
		$query .= ' ORDER BY contacts_email ASC';

	# Do select and return check
		IF (is_numeric($s_id)) {
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);
		}
	# Process query results
		IF ($numrows) {
			$button = str_replace('<img src="', '', $_TCFG['_IMG_SAVE_S']);
			while ($row = $db_coin->db_fetch_array($result)) {

			# Display the "edit/delete data" form for this row
				$_out .= '<form method="POST" action="admin.php">'.$_nl;
				$_out .= '<input type="hidden" name="s_id" value="'.$s_id.'">'.$_nl;
				$_out .= '<input type="hidden" name="stage" value="">'.$_nl;
				$_out .= '<input type="hidden" name="op" value="ae_mail_update">'.$_nl;
				$_out .= '<input type="hidden" name="contacts_id" value="'.$row['contacts_id'].'">'.$_nl;
				$_out .= '<input type="hidden" name="cp" value="suppliers">'.$_nl;
				$_out .= '<tr>'.$_nl;
				$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="ae_fname" size="15" value="'.htmlspecialchars($row['contacts_name_first']).'"></td>'.$_nl;
				$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="ae_lname" size="15" value="'.htmlspecialchars($row['contacts_name_last']).'"></td>'.$_nl;
				$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="ae_email" size="35" value="'.htmlspecialchars($row['contacts_email']).'"></td>'.$_nl;
				$_out .= $_td_str_left.'&nbsp;&nbsp;&nbsp;'.$_nl;

			# Display "update" button
				$_out .= '<input type="image" src="'.$button.$_nl;

			# Display "Delete" button
				$_out .= '&nbsp;<a href="admin.php?cp=suppliers&op=ae_mail_delete&stage=0&s_id='.$s_id.'&contacts_id='.$row['contacts_id'].'">'.$_TCFG['_IMG_DEL_S'].'</a>'.$_nl;

			# End row
				$_out .= '</td></tr></form>'.$_nl;
			}
		}

	# Display form for adding a new entry
		$button = str_replace('<img src="', '', $_TCFG['_IMG_ADD_S']);
		$_out .= '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">'.$_nl;
		$_out .= '<input type="hidden" name="s_id" value="'.$s_id.'">'.$_nl;
		$_out .= '<input type="hidden" name="stage" value="">'.$_nl;
		$_out .= '<input type="hidden" name="cp" value="suppliers">'.$_nl;
		$_out .= '<input type="hidden" name="op" value="ae_mail_add">'.$_nl;
		$_out .= '<tr>'.$_nl;
		$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="new_ae_fname" size="15" value=""></td>'.$_nl;
		$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="new_ae_lname" size="15" value=""></td>'.$_nl;
		$_out .= $_td_str_left.'<input class="PSML_NL" type="text" name="new_ae_email" size="35" value=""></td>'.$_nl;
		$_out .= $_td_str_left.'&nbsp;&nbsp;&nbsp;'.$_nl;
		$_out .= '<input type="image" src="'.$button.'</td></tr></form>'.$_nl;

	# Build return string
		$returning = $_cstart.$_out.$_cend;

	# Return the form
		return $returning;
}

function do_select_list_status_supplier($aname, $avalue) {
		global $_CCFG, $_nl;
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		FOR ($i = 0; $i < count($_CCFG['S_STATUS']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['S_STATUS'][$i]).'"';
			IF ($_CCFG['S_STATUS'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['S_STATUS'][$i].'</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		return $_out;
}

# Do Data Input Validate
function cp_do_input_validation($_adata) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
		IF ($_adata['op'] == 'edit' || $_adata['op'] == 'add') {
		# Check required fields (err / action generated later in cade as required)
		//	IF (!$_adata['s_id'])		{$err_entry['flag'] = 1; $err_entry['s_id'] = 1;}
		//	IF (!$_adata['s_status'])	{$err_entry['flag'] = 1; $err_entry['s_status'] = 1;}
			IF (!$_adata['s_company'])	{$err_entry['flag'] = 1; $err_entry['s_company'] = 1;}
		//	IF (!$_adata['s_name_first'])	{$err_entry['flag'] = 1; $err_entry['s_name_first'] = 1;}
		//	IF (!$_adata['s_name_last'])	{$err_entry['flag'] = 1; $err_entry['s_name_last'] = 1;}
			IF (!$_adata['s_addr_01'])	{$err_entry['flag'] = 1; $err_entry['s_addr_01'] = 1;}
		//	IF (!$_adata['s_addr_02'])	{$err_entry['flag'] = 1; $err_entry['s_addr_02'] = 1;}
			IF (!$_adata['s_city'])		{$err_entry['flag'] = 1; $err_entry['s_city'] = 1;}
			IF (!$_adata['s_state_prov'])	{$err_entry['flag'] = 1; $err_entry['s_state_prov'] = 1;}
			IF (!$_adata['s_country'])	{$err_entry['flag'] = 1; $err_entry['s_country'] = 1;}
			IF (!$_adata['s_zip_code'])	{$err_entry['flag'] = 1; $err_entry['s_zip_code'] = 1;}
		//	IF (!$_adata['s_phone'])		{$err_entry['flag'] = 1; $err_entry['s_phone'] = 1;}
		//	IF (!$_adata['s_fax'])		{$err_entry['flag'] = 1; $err_entry['s_fax'] = 1;}
		//	IF (!$_adata['s_tollfree'])	{$err_entry['flag'] = 1; $err_entry['s_tollfree'] = 1;}
		//	IF (!$_adata['s_email'])		{$err_entry['flag'] = 1; $err_entry['s_email'] = 1;}
		//	IF (!$_adata['s_taxid'])		{$err_entry['flag'] = 1; $err_entry['s_taxid'] = 1;}
		//	IF (!$_adata['s_account'])	{$err_entry['flag'] = 1; $err_entry['s_account'] = 1;}
			IF (!$_adata['s_terms'])		{$err_entry['flag'] = 1; $err_entry['s_terms'] = 1;}
		//	IF (!$_adata['s_notes'])		{$err_entry['flag'] = 1; $err_entry['s_notes'] = 1;}

		}

	# Validate some data (submitting data entered)
	# Email
		IF ($_adata['s_email'] && do_validate_email($_adata['s_email'], 0)) {
			$err_entry['flag'] = 1; $err_entry['err_email_invalid'] = 1;
		}

	# Email does not match existing email
		$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
		IF ($_adata['s_email'] && do_email_exist_check($_adata['s_email'], $_adata['s_id'], $_ce)) {
			$err_entry['flag'] = 1; $err_entry['err_email_matches_another'] = 1;
		}

		return $err_entry;

	}

# Do Form for Add / Edit
function cp_do_form_add_edit_supplier($adata, $aerr_entry) {
	# Dim some Vars:
		global $_TCFG, $_DBCFG, $_LANG, $_nl, $_sp;

	# Build mode dependent strings
		switch ($adata['op']) {
			case "add":
				$mode_proper	= $_LANG['_ADMIN']['B_Add'];
				$mode_button	= $_LANG['_ADMIN']['B_Add'];
				break;
			case "edit":
				$mode_proper	= $_LANG['_ADMIN']['B_Edit'];
				$mode_button	= $_LANG['_ADMIN']['B_Save'];
				break;
			default:
				$adata['op']	= "add";
				$mode_proper	= $_LANG['_ADMIN']['B_Add'];
				$mode_button	= $_LANG['_ADMIN']['B_Add'];
				break;
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $mode_proper.$_sp.$_LANG['_ADMIN']['Supplier_Info_Entry'];

	# Build Temp Error Red Font Flag
		$_err_red_flag = '<font color="red"><b>-->> </b></font>';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
			$_cstr .= '<br><b>'.$_LANG['_SDMIN']['AD_ERR00__HDR1'].' '.$_err_red_flag.$_nl;
			$_cstr .= '<font color="red"><br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'</font><br><br>'.$_nl;
		}

	# Build common td start tag / col strings (reduce text)
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_left			= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="70%" valign="top">';

	# Do Main Form
		$_cstr .= '<form name="s_info" id="s_info" method="POST"action="admin.php">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="suppliers">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="s_id" value="'.htmlspecialchars($adata['s_id']).'">'.$_nl;

		$_cstr .= '<table cellpadding="5" cellspacing="0" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= $adata['s_id'].$_nl;
		IF ($adata['op'] == 'add') {$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

//		IF ($aerr_entry['s_status']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l14_Status'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_status_supplier('s_status', $adata['s_status']);
//		IF ($aerr_entry['s_status']) {
//			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
//		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
//		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_account']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_AccountID'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_account" SIZE=20 value="'.htmlspecialchars($adata['s_account']).'">'.$_nl;
		IF ($aerr_entry['s_account']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
//		} ELSE {
//			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_terms']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Terms'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_terms" SIZE=20 value="'.htmlspecialchars($adata['s_terms']).'">'.$_nl;
		IF ($aerr_entry['s_terms']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_company']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Company'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_company" SIZE=20 value="'.htmlspecialchars($adata['s_company']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_company']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_name_first']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_first" SIZE=20 value="'.htmlspecialchars($adata['s_name_first']).'">'.$_nl;
		IF ($aerr_entry['s_name_first']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_name_last']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_name_last" SIZE=20 value="'.htmlspecialchars($adata['s_name_last']).'">'.$_nl;
		IF ($aerr_entry['s_name_last']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_addr_01']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Address_Street_1'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_addr_01" SIZE=40 value="'.htmlspecialchars($adata['s_addr_01']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_addr_01']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_addr_02']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Address_Street_2'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_addr_02" SIZE=40 value="'.htmlspecialchars($adata['s_addr_02']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_addr_02']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_city']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_City'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_city" SIZE=40 value="'.htmlspecialchars($adata['s_city']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_city']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_state_prov']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_State_Province'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_state_prov" SIZE=40 value="'.htmlspecialchars($adata['s_state_prov']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_state_prov']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_country']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Country'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_countries('s_country', $adata['s_country']);
		IF ($aerr_entry['s_country']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_zip_code']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Zip_Postal_Code'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_zip_code" SIZE=12 value="'.htmlspecialchars($adata['s_zip_code']).'">'.$_nl;
		IF ($aerr_entry['s_zip_code']) {
			$_cstr .= '<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_phone']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Phone'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_phone" SIZE=20 value="'.htmlspecialchars($adata['s_phone']).'">'.$_nl;
		IF ($aerr_entry['s_phone']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_fax']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Fax'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_fax" SIZE=20 value="'.htmlspecialchars($adata['s_fax']).'">'.$_nl;
		IF ($aerr_entry['s_fax']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_tollfree']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_TollFree'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_tollfree" SIZE=20 value="'.htmlspecialchars($adata['s_tollfree']).'">'.$_nl;
		IF ($aerr_entry['s_tollfree']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_email'] || $aerr_entry['err_email_matches_another'] || $aerr_entry['err_email_invalid']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_Email_Address'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_email" SIZE=40 value="'.htmlspecialchars($adata['s_email']).'" maxlength="50">'.$_nl;
		IF ($aerr_entry['s_email']) {
			$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';
		} ELSEIF ($aerr_entry['err_email_matches_another']) {
			$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR33'].'</font>';
		} ELSEIF ($aerr_entry['err_email_invalid']) {
			$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR30'].'</font>';
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['s_taxid']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l_TaxID'].$_sp;
		$_cstr .= '</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="s_taxid" SIZE=20 value="'.htmlspecialchars($adata['s_taxid']).'">'.$_nl;
		IF ($aerr_entry['s_taxid']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l_Notes'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="s_notes" COLS="60" ROWS="10">'.$adata['s_notes'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $mode_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw ('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;

	# Build a form for showing/adding/editing/deleting additional emails
	# Supplier must exist before this form will show.
	# If supplier does not exist, a notice about form availability will appear instead.
		IF ($adata['op'] == 'edit') {
			$_cstr .= do_form_additional_emails($adata['s_id']).$_nl;
		} ELSE {
			$_cstr .= $_LANG['_ADMIN']['l_Email_Address_Additional_later'];
		}

		IF ($adata['op'] == 'edit') {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=view&s_id='.$adata['s_id'], $_TCFG['_IMG_VIEW_M'],$_TCFG['_IMG_VIEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link('admin.php?cp=suppliers', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out  = do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1).'<br>'.$_nl;

		return $_out;
}

# Do display entry (individual entry)
function cp_do_display_entry_supplier($adata) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left			= '<td class="TP1SML_NR" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" valign="top">';
		$_td_str_right_span3	= '<td class="TP1SML_NL" valign="top" colspan="3">';


	# Build output
		$_out  = '<br>'.$_nl;
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['Supplier_Info_Entry'].'</b>';
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
 			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
				$_out .= do_nav_link('admin.php?cp=suppliers&op=edit&s_id='.$adata['s_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
			}
			IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
				$_out .= do_nav_link('admin.php?cp=suppliers&op=delete&stage=1&s_id='.$adata['s_id'].'&s_name_first='.$adata['s_name_first'].'&s_name_last='.$adata['s_name_last'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
			}
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="BLK_IT_ENTRY">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0" border="0">'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Supplier_ID'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$adata['s_id'].'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Company'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_company']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Full_Name'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_name_first'].' '.$adata['s_name_last']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Status'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$adata['s_status'].'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Address_Street_1'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$adata['s_addr_01'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_AccountID'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$adata['s_account'].'</td>'.$_nl;
		IF ($adata['s_addr_02']) {
			$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Address_Street_2'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.$adata['s_addr_02'].'</td>'.$_nl;
		} ELSE {
			$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		}
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Terms'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$adata['s_terms'].'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_City'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_city']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
		$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_State_Province'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_state_prov']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		IF ($adata['s_phone']) {
			$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Phone'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.htmlspecialchars($adata['s_phone']).'</td>'.$_nl;
		} ELSE {
			$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		}
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Country'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_country']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		IF ($adata['s_fax']) {
			$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Fax'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.htmlspecialchars($adata['s_fax']).'</td>'.$_nl;
		} ELSE {
			$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		}
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Zip_Postal_Code'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_zip_code']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		IF ($adata['s_tollfree']) {
			$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_TollFree'].$_sp.'</b></td>'.$_nl;
			$_out .= $_td_str_right.htmlspecialchars($adata['s_tollfree']).'</td>'.$_nl;
			$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
		}

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Email_Address'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_email']).'</td>'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_TaxID'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.htmlspecialchars($adata['s_taxid']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Email_Address_Additional'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right.$_nl;

	# Set Query for select (additional emails).
		$ae_query	= 'SELECT *';
		$ae_query .= ' FROM '.$_DBCFG['suppliers_contacts'];
		$ae_query .= ' WHERE contacts_s_id='.$adata['s_id'];
		$ae_query .= ' ORDER BY contacts_email ASC';
		$ae_result	= $db_coin->db_query_execute($ae_query);
		IF ($db_coin->db_query_numrows($ae_result)) {
			while ($ae_row = $db_coin->db_fetch_array($ae_result)) {
				$_out .= htmlspecialchars($ae_row['contacts_email']).'<br>'.$_nl;
       	    }
		}
		$_out .= '</td>'.$_nl;

		$_out .= $_td_str_left.$_sp.'</td>'.$_nl;
		$_out .= $_td_str_right.$_sp.'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '<tr class="BLK_DEF_ENTRY"><td colspan="4"><hr></td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l_Notes'].$_sp.'</b></td>'.$_nl;
		$_out .= $_td_str_right_span3.nl2br($adata['s_notes']).'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

		$_out .= '</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;

		return $_out;
}

# Do list field form for: Supplier Bills
function cp_do_view_supplier_bills($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query parameters for select.
		$query	 = 'SELECT *';
		$_from	.= ' FROM '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$_where	.= ' WHERE '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';
		$_where	.= ' AND '.$_DBCFG['bills'].'.bill_s_id='.$adata['s_id'];
		$_order	.= ' ORDER BY '.$_DBCFG['bills'].'.bill_ts DESC';

		IF (!$_CCFG['IPL_SUPPLIERS'] > 0) {$_CCFG['IPL_SUPPLIERS'] = 5;}
		$_limit .= ' LIMIT 0, '.$_CCFG['IPL_SUPPLIERS'];

	# Get count of rows total:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= $_from;
		$query_ttl .= $_where;
		$result_ttl	= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Do select listing records and return check
		$query	.= $_from.$_where.$_order.$_limit;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
	#	$_out .= '<br>'.$_nl;
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="8">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['Bills'];
		$_out .= ' ('.$numrows.$_sp.$_LANG['_ADMIN']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_ADMIN']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;
		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($numrows_ttl > $_CCFG['IPL_SUPPLIERS']) {
				$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_s_id='.$adata['s_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
			}
			$_out .= do_nav_link('mod.php?mod=cc&mode=search&sw=bills', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Id'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Status'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Date_Issued'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Date_Due'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR">'.$_LANG['_ADMIN']['l_Amount'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BR">'.$_LANG['_ADMIN']['l_Balance'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['bill_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.htmlspecialchars($row['bill_status']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bill_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['bill_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($row['bill_total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.$_sp.'</td>'.$_nl;

			# Show current bill balance
				$idata = do_get_bill_supplier_balance($adata['s_id'], $row['bill_id']);
				$_out .= '<td class="TP3SML_NR">'.do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= do_nav_link('mod.php?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_out .= do_nav_link('mod_print.php?mod=bills&mode=view&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP08'] == 1) {
						$_out .= do_nav_link('mod.php?mod=bills&mode=edit&bill_id='.$row['bill_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
						$_out .= do_nav_link('mod.php?mod=bills&mode=delete&stage=1&bill_id='.$row['bill_id'].'&invc_ts='.$row['invc_ts'].'&invc_status='.$row['invc_status'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
				}
				$_out .= '</nobr></td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

	# Show totals footer row
		$idata = do_get_bill_supplier_balance($adata['s_id'], 0);
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BR" colspan="4">'.$_nl;
		$_out .= $_LANG['_SUPPLIERS']['l_Amount'].$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($idata['total_cost'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		$_out .= '</td><td class="TP3SML_BR">'.$_nl;
		$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']).$_sp.$_nl;
		$_out .= '</td><td class="TP3SML_BL">'.$_nl;
		$_out .= $_sp.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

	# Return results
		return $_out;
}

# Do list field form for: Emails
function cp_do_view_supplier_emails($adata) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_SERVER, $_nl, $_sp;

	# Get all email addresses for a supplier
		$sinfo	= get_contact_supplier_info($adata['s_id']);
		$s_emails	= get_contact_supplier_info_alias($adata['s_id'], 1);
		$x		= sizeof($s_emails);

	# Set Query parameters for select.
		$query	 = 'SELECT *';
		$_from	 = ' FROM '.$_DBCFG['mail_archive'];
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
			$_where	 = ' WHERE '.$_DBCFG['mail_archive'].".ma_fld_from='".$sinfo['s_email']."'";
			$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_recip='".$sinfo['s_email']."'";
		} ELSE {
			$_where	 = ' WHERE '.$_DBCFG['mail_archive'].".ma_fld_from LIKE '%<".$sinfo['s_email'].">%'";
			$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_recip LIKE '%<".$sinfo['s_email'].">%'";
		}
		IF ($x) {
			FOR ($i=0; $i<=$x; $i++) {
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
					$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_from='".$s_emails[$i]['c_email']."'";
					$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_recip='".$s_emails[$i]['c_email']."'";
				} ELSE {
		   			$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_from LIKE '%<".$s_emails[$i]['c_email'].">%'";
					$_where	.= ' OR '.$_DBCFG['mail_archive'].".ma_fld_recip LIKE '%<".$s_emails[$i]['c_email'].">%'";
				}
			}
		}

		$_order = ' ORDER BY '.$_DBCFG['mail_archive'].'.ma_time_stamp DESC';

		IF (!$_CCFG['IPL_SUPPLIERS'] > 0) {$_CCFG['IPL_SUPPLIERS'] = 5;}
		$_limit = ' LIMIT 0, '.$_CCFG['IPL_SUPPLIERS'];

	# Get count of rows total:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= $_from;
		$query_ttl .= $_where;
		$result_ttl	= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Do select listing records and return check
		$query	.= $_from.$_where.$_order.$_limit;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<div align="center">'.$_nl;
		$_out .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
		$_out .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_NC" colspan="7">'.$_nl;

		$_out .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
		$_out .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l_Email_Archive'];
		$_out .= ' ('.$numrows.$_sp.$_LANG['_ADMIN']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_ADMIN']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl.'<td class="TP0MED_NR">'.$_nl;

		IF ($_CCFG['_IS_PRINT'] != 1) {
			IF ($numrows_ttl > $_CCFG['IPL_SUPPLIERS']) {
	   			$_out .= do_nav_link('mod.php?mod=mail&mode=search&sw=archive&search_type=1&s_to='.$sinfo['s_email'].'&s_from='.$sinfo['s_email'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
	   		}
			$_out .= do_nav_link('mod.php?mod=mail&mode=search&sw=archive', $_TCFG['_S_IMG_SEARCH_S'],$_TCFG['_S_IMG_SEARCH_S_MO'],'','');
		} ELSE {
			$_out .= $_sp;
		}
		$_out .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Id'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BC">'.$_LANG['_ADMIN']['l_Date_Sent'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL">'.$_LANG['_ADMIN']['l_Subject'].'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL">'.$_LANG['_CCFG']['Actions'].'</td>'.$_nl;
		$_out .= '</tr>'.$_nl;

	# Process query results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.$row['ma_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NC">'.dt_make_datetime($row['ma_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT'] ).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.htmlspecialchars($row['ma_fld_subject']).'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= do_nav_link('mod.php?mod=mail&mode=resend&obj=arch&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_EMAIL_S'],$_TCFG['_S_IMG_EMAIL_S_MO'],'','');
					$_out .= do_nav_link('mod.php?mod=mail&mode=view&obj=arch&ma_id='.$row['ma_id'].'&_suser_id='.$adata['_suser_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					$_out .= do_nav_link('mod_print.php?mod=mail&mode=view&obj=arch&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_PRINT_S'],$_TCFG['_S_IMG_PRINT_S_MO'],'_new','');
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP05'] == 1) {
						$_out .= do_nav_link('mod.php?mod=mail&mode=delete&obj=arch&stage=2&ma_id='.$row['ma_id'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
				}
				$_out .= '</nobr></td>'.$_nl;
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

	# Return results
		return $_out;
}

# Do list field form for: Suppliers
function cp_do_select_listing_suppliers($adata) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_where	= '';
		$_out	= '';
		$_ps		= '';
		IF ($adata['status'] && $adata['status'] != 'all') {$_ps .= '&status='.$adata['status'];}
		IF ($adata['notstatus']) {$_ps .= '&notstatus='.$adata['notstatus'];}


	# Set Query for select.
		$query = 'SELECT * FROM '.$_DBCFG['suppliers'];

	# Set Filters
		IF (!$adata['fb'])		{$adata['fb'] = '';}
		IF ($adata['fb'] == '1')	{$_where .= " WHERE s_status='".$db_coin->db_sanitize_data($adata['fs'])."'";}

	# Show only selected status suppliers
		IF ($adata['status'] && $adata['status'] != 'all') {
			IF ($_where) {$_where .= ' AND ';} ELSE {$_where .= ' WHERE ';}
			$_where .= "s_status='".$db_coin->db_sanitize_data($adata['status'])."'";
		}
		IF ($adata['notstatus']) {
			IF ($_where) {$_where .= ' AND ';} ELSE {$_where .= ' WHERE ';}
			$_where .= "s_status != '".$db_coin->db_sanitize_data($adata['notstatus'])."'";
		}

	# Set Order ASC / DESC part of sort
		IF (!$adata['so'])		{$adata['so'] = 'A';}
		IF ($adata['so'] == 'A')	{$order_AD = ' ASC';}
		IF ($adata['so'] == 'D')	{$order_AD = ' DESC';}

	# Set Sort orders
		IF (!$adata['sb'])		{$adata['sb'] = '3';}
		IF ($adata['sb'] == '1')	{$_order = ' ORDER BY s_id'.$order_AD;}
		IF ($adata['sb'] == '2')	{$_order = ' ORDER BY s_status'.$order_AD;}
		IF ($adata['sb'] == '3')	{$_order = ' ORDER BY s_company'.$order_AD;}
		IF ($adata['sb'] == '4')	{$_order = ' ORDER BY s_name_last'.$order_AD.', s_name_first'.$order_AD;}
		IF ($adata['sb'] == '5')	{$_order = ' ORDER BY s_email'.$order_AD;}

	# Set / Calc additional paramters string
		IF ($adata['sb'])	{$_argsb= '&sb='.$adata['sb'];}
		IF ($adata['so'])	{$_argso= '&so='.$adata['so'];}
		IF ($adata['fb'])	{$_argfb= '&fb='.$adata['fb'];}
		IF ($adata['fs'])	{$_argfs= '&fs='.$adata['fs'];}
		$_link_xtra = $_argsb.$_argso.$_argfb.$_argfs;

	# Build Page menu
	# Get count of rows total for pages menu:
		$query_ttl  = 'SELECT COUNT(*)';
		$query_ttl .= ' FROM '.$_DBCFG['suppliers'];
		$query_ttl .= $_where;

		$result_ttl= $db_coin->db_query_execute($query_ttl);
		while(list($cnt) = $db_coin->db_fetch_row($result_ttl)) {$numrows_ttl = $cnt;}

	# Page Loading first rec number
	# $_rec_next	- is page loading first record number
	# $_rec_start	- is a given page start record (which will be rec_next)
		IF (!$_CCFG['IPP_SUPPLIERS'] > 0) {$_CCFG['IPP_SUPPLIERS'] = 15;}
		$_rec_page	= $_CCFG['IPP_SUPPLIERS'];
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
		$_page_menu = $_LANG['_ADMIN']['l_Pages'].$_sp;
		for ($i = 1; $i <= $_num_pages; $i++) {
			$_rec_start = (($i*$_rec_page)-$_rec_page);
			IF ($_rec_start == $_rec_next) {
			# Loading Page start record so no link for this page.
				$_page_menu .= $i;
			} ELSE {
				$_page_menu .= '<a href="admin.php?cp=suppliers'.$_link_xtra.$_ps.'&rec_next='.$_rec_start.'">'.$i.'</a>';
			}
			IF ($i < $_num_pages) {$_page_menu .= ','.$_sp;}
		} # End page menu

	# Finish out query with record limits and do data select for display and return check
		$query	.= $_where.$_order." LIMIT $_rec_next, $_rec_page";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Generate links for sorting
		$_hdr_link_prefix = '<a href="admin.php?cp=suppliers&sb=';
		$_hdr_link_suffix = '&fb='.$adata['fb'].'&fs='.$adata['fs'].'&fc='.$adata['fc'].'&rec_next='.$_rec_next.$_ps.'">';

		$_hdr_link_1 = $_LANG['_ADMIN']['l_Supplier_ID'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_1 .= $_hdr_link_prefix.'1&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_2 = $_LANG['_ADMIN']['l_Status'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_2 .= $_hdr_link_prefix.'2&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_3 = $_LANG['_ADMIN']['l_Company'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_3 .= $_hdr_link_prefix.'3&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_4 = $_LANG['_ADMIN']['l_Full_Name'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_4 .= $_hdr_link_prefix.'4&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_5 = $_LANG['_ADMIN']['l_Email_Address'].$_sp.'<br>';
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=A'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_ASC_S'].'</a>';
			$_hdr_link_5 .= $_hdr_link_prefix.'5&so=D'.$_hdr_link_suffix.$_TCFG['_IMG_SORT_DSC_S'].'</a>';
		}

		$_hdr_link_6 .= $_LANG['_ADMIN']['l_Balance'].$_sp.'<br>';

	# Build Status header bar for viewing only certain types
		IF ($_CCFG['_IS_PRINT'] != 1) {
			$_out .= '&nbsp;&nbsp;&nbsp;<table cellpadding="5" cellspacing="0" border="0"><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Only'].':</td>';
			$_out .= '<td>&nbsp;[<a href="admin.php?cp=suppliers&op=none&status=all'.$_link_xtra;
			$_out .= '">'.$_LANG['_BASE']['All'].'</a>]&nbsp;</td>';
			for ($i=0; $i< sizeof($_CCFG['S_STATUS']); $i++) {
				$_out .= '<td align="right"><nobr>&nbsp;[<a href="admin.php?cp=suppliers&op=none&status='.$_CCFG['S_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['S_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
			}
			$_out .= '</tr><tr>';
			$_out .= '<td>'.$_LANG['_BASE']['Except'].':</td>';
			$_out .= '<td>&nbsp;</td>';
			for ($i=0; $i< sizeof($_CCFG['S_STATUS']); $i++) {
				$_out .= '<td><nobr>&nbsp;[<a href="admin.php?cp=suppliers&op=none&notstatus='.$_CCFG['S_STATUS'][$i].$_link_xtra;
				$_out .= '">'.$_CCFG['S_STATUS'][$i].'</a>]&nbsp;</nobr></td>';
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
		$_out .= '<b>'.$_LANG['_ADMIN']['Suppliers'].':'.$_sp.'('.$_rec_next_lo.'-'.$_rec_next_hi.$_sp.$_LANG['_ADMIN']['of'].$_sp.$numrows_ttl.$_sp.$_LANG['_ADMIN']['total_entries'].')</b><br>'.$_nl;
		$_out .= '</td>'.$_nl;
		$_out .= '<td class="TP0MED_NR">'.$_sp.'</td>'.$_nl;
		$_out .= '</tr>'.$_nl.'</table>'.$_nl;

		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_1.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_2.'</td>'.$_nl;
		$_out .= '<td class="TP3SML_BL" valign="top">'.$_hdr_link_3.'</td>'.$_nl;
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
				$_out .= '<td class="TP3SML_NL">'.$row['s_id'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['s_status'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['s_company'].'</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['s_name_last'];
				IF ($row['s_name_last']) {$_out .= ', ';}
				$_out .= $row['s_name_first'];
				$_out .= '</td>'.$_nl;
				$_out .= '<td class="TP3SML_NL">'.$row['s_email'].'</td>'.$_nl;
				$idata = do_get_bill_supplier_balance($row['s_id']);
				$_out .= '<td class="TP3SML_NR">';
				IF ($idata['net_balance']) {$_out .= do_currency_format($idata['net_balance'],1,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);}
				$_out .= $_sp.'</td>'.$_nl;

				IF ($_CCFG['_IS_PRINT'] != 1) {
					$_out .= '<td class="TP3SML_NL"><nobr>'.$_nl;
					$_out .= do_nav_link('admin.php?cp=suppliers&op=view&s_id='.$row['s_id'], $_TCFG['_S_IMG_VIEW_S'],$_TCFG['_S_IMG_VIEW_S_MO'],'','');
					IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
						$_out .= do_nav_link('admin.php?cp=suppliers&op=edit&s_id='.$row['s_id'], $_TCFG['_S_IMG_EDIT_S'],$_TCFG['_S_IMG_EDIT_S_MO'],'','');
						$_out .= do_nav_link('admin.php?cp=suppliers&op=delete&stage=1&s_id='.$row['s_id'].'&s_name_first='.$row['s_name_first'].'&s_name_last='.$row['s_name_last'], $_TCFG['_S_IMG_DEL_S'],$_TCFG['_S_IMG_DEL_S_MO'],'','');
					}
					$_out .= '</nobr></td>'.$_nl;
				}
				$_out .= '</tr>'.$_nl;
			}
		}

		$_out .= '<tr class="BLK_DEF_ENTRY"><td class="TP3MED_NC" colspan="'.(7-$_CCFG['_IS_PRINT']).'">'.$_nl;
		$_out .= $_page_menu.$_nl;
		$_out .= '</td></tr>'.$_nl;

		$_out .= '</table>'.$_nl;
		$_out .= '</div>'.$_nl;
		$_out .= '<br>'.$_nl;

		return $_out;
}





/**************************************************************
 * CP Base Code
**************************************************************/
# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);



# Operation:	Any Perm Check
#	- Exit out on perm error.
IF ($_PERMS['AP16'] != 1 && $_PERMS['AP04'] != 1) {
	IF ($_PERMS['AP10'] == 1) {
		$_GPV['op'] = 'view';
	} ELSE {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;
		echo $_out;
		exit;
	}
}



# Check $_GPV[op] (operation switch)
	switch($_GPV['op']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "delete":
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "mail":
			break;
		case "ae_mail_delete":
			$good = do_delete_additional_email($_GPV['s_id'], $_GPV['contacts_id']);
			$_GPV['op'] = 'edit';
			break;
		case "ae_mail_add":
			IF (do_validate_email($_GPV['new_ae_email'],0)) {
				$err_entry['flag'] = 1;
				$err_entry['s_email'] = 1;
				$err_entry['err_additional_email_invalid'] = 1;
			} ELSE {
				$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
				IF (do_email_exist_check($_GPV['new_ae_email'], $_GPV['s_id'], $_ce)) {
					$err_entry['flag'] = 1;
					$err_entry['s_email'] = 1;
					$err_entry['err_additional_matches_another'] = 1;
				} ELSE {
					$good = do_insert_additional_email($_GPV['s_id'],$_GPV['new_ae_fname'],$_GPV['new_ae_lname'],$_GPV['new_ae_email']);
				}
			}
			$_GPV['op'] = 'edit';
			break;
		case "ae_mail_update":
			IF (do_validate_email($_GPV['ae_email'],0)) {
				$err_entry['flag'] = 1;
				$err_entry['s_email'] = 1;
				$err_entry['err_additional_email_invalid'] = 1;
			} ELSE {
				$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
				IF (do_email_exist_check($_GPV['ae_email'], $_GPV['s_id'], $_ce)) {
					$err_entry['flag'] = 1;
					$err_entry['s_email'] = 1;
					$err_entry['err_additional_matches_another'] = 1;
				} ELSE {
					$good = do_update_additional_email($_GPV['s_id'],$_GPV['ae_fname'],$_GPV['ae_lname'],$_GPV['ae_email'],$_GPV['contacts_id']);
				}
			}
			$_GPV['op'] = 'edit';
			break;
		case "view":
			break;
		default:
			$_GPV['op'] = 'none';
			break;
	}

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
		$err_entry = cp_do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;





##############################
# Operation:	View Entry
# Summary:
#	- For viewing entry
#	- Must preceed "none"
##############################
IF ($_GPV['op'] == 'view') {
	# Check for valid $_GPV[s_id] no
		IF ($_GPV['s_id']) {
		# Set Query for select.
			$query	= 'SELECT * FROM '.$_DBCFG['suppliers'].' WHERE s_id='.$_GPV['s_id'];
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

		# Call function for displaying item
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_display_entry_supplier($data).$_nl;
			$_out .= '<br><br>'.cp_do_view_supplier_bills($data).$_nl;
			$_out .= '<br><br>'.cp_do_view_supplier_emails($data).$_nl;
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
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Suppliers_Listing'];

	# Call function for create select form: Vendors
		$_cstr = cp_do_select_listing_suppliers($data).$_nl;

		$_mstr = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
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
		$_out .= cp_do_form_add_edit_supplier($data, $err_entry).$_nl;
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
	# Get max / create new s_id
		$_max_s_id = do_get_max_s_id();

	# Do insert of new supplier
		$query = "INSERT INTO ".$_DBCFG['suppliers']." (s_id";
		$query .= ", s_status, s_company, s_name_first, s_name_last";
		$query .= ", s_addr_01, s_addr_02, s_city, s_state_prov";
		$query .= ", s_country, s_zip_code, s_phone, s_fax, s_tollfree";
		$query .= ", s_email, s_taxid, s_account, s_terms, s_notes";
		$query .= ")";
		$query .= " VALUES ('".($_max_s_id+1)."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_status'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_company'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_name_first'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_name_last'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_addr_01'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_addr_02'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_city'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_state_prov'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_country'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_zip_code'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_phone'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_fax'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_tollfree'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_email'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_taxid'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_account'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_terms'])."', ";
		$query .= "'".$db_coin->db_sanitize_data($_GPV['s_notes'])."'";
		$query .= ")";

		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Adjust Data Array with returned record
		$_insert_id		= $_max_s_id+1;
		$data['insert_id']	= $_insert_id;
		$data['s_id']		= $_insert_id;

	# Call function to open block
		$_tstr = $_LANG['_ADMIN']['Add_Suppliers_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$_insert_id.')';

	# Call function for Display Entry
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=suppliers', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Display results
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, cp_do_display_entry_supplier($data), 1, $_mstr, 1).$_nl;
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
		IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
			$_out  = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_supplier($data, $err_entry, 1).$_nl;
			echo $_out;

		} ELSE {
		# Check for valid $_GPV[vendor_id] no
			IF ($_GPV['s_id']) {
			# Set Query for select.
				$query	 = 'SELECT * FROM '.$_DBCFG['suppliers'];
				$query	.= ' WHERE s_id='.$_GPV['s_id'];

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
				$_out = '<!-- Start content -->'.$_nl;
				$_out .= cp_do_form_add_edit_supplier($data, $err_entry).$_nl;

			} ELSE {
			# Content start flag
				$_out = '<!-- Start content -->'.$_nl;

			# Build Title String, Content String, and Footer Menu String
				$_tstr = $_LANG['_ADMIN']['Suppliers_Editor'];

			# Call function for create select form: Vendors
				$_cstr = cp_do_select_form_supplier('admin.php?cp=suppliers&op=edit', 's_id', $_GPV['s_id'], '1');

				$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
				IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
					$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
				}

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1).$_nl;
				$_out .= '<br>'.$_nl;
			}

		# Echo final output
			echo $_out;
		}
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
		$query = 'UPDATE '.$_DBCFG['suppliers'].' SET ';
		$query .= "s_status='".$db_coin->db_sanitize_data($_GPV['s_status'])."', ";
		$query .= "s_company='".$db_coin->db_sanitize_data($_GPV['s_company'])."', ";
		$query .= "s_name_first='".$db_coin->db_sanitize_data($_GPV['s_name_first'])."', ";
		$query .= "s_name_last='".$db_coin->db_sanitize_data($_GPV['s_name_last'])."', ";
		$query .= "s_addr_01='".$db_coin->db_sanitize_data($_GPV['s_addr_01'])."', ";
		$query .= "s_addr_02='".$db_coin->db_sanitize_data($_GPV['s_addr_02'])."', ";
		$query .= "s_city='".$db_coin->db_sanitize_data($_GPV['s_city'])."', ";
		$query .= "s_state_prov='".$db_coin->db_sanitize_data($_GPV['s_state_prov'])."', ";
		$query .= "s_country='".$db_coin->db_sanitize_data($_GPV['s_country'])."', ";
		$query .= "s_zip_code='".$db_coin->db_sanitize_data($_GPV['s_zip_code'])."', ";
		$query .= "s_phone='".$db_coin->db_sanitize_data($_GPV['s_phone'])."', ";
		$query .= "s_fax='".$db_coin->db_sanitize_data($_GPV['s_fax'])."', ";
		$query .= "s_tollfree='".$db_coin->db_sanitize_data($_GPV['s_tollfree'])."', ";
		$query .= "s_taxid='".$db_coin->db_sanitize_data($_GPV['s_tax_id'])."', ";
		$query .= "s_account='".$db_coin->db_sanitize_data($_GPV['s_account'])."', ";
		$query .= "s_email='".$db_coin->db_sanitize_data($_GPV['s_email'])."', ";
		$query .= "s_terms='".$db_coin->db_sanitize_data($_GPV['s_terms'])."', ";
		$query .= "s_notes='".$db_coin->db_sanitize_data($_GPV['s_notes'])."' ";
		$query .= 'WHERE s_id='.$_GPV['s_id'];
		$result	 = $db_coin->db_query_execute($query) OR DIE("Unable to complete request");

	# Call function to open block
		$_tstr = $_LANG['_ADMIN']['Edit_Suppliers_Entry_Results'];

	# Footer buttons
		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=suppliers', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Display results
		$_out  = '<!-- Start content -->'.$_nl;
		$_out .= do_mod_block_it($_tstr, cp_do_display_entry_supplier($data), 1, $_mstr, 1).$_nl;
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
	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Suppliers_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr  = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="suppliers">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Message'].'='.$_GPV['s_id'].'<br>'.$_nl;
		$_cstr .= $_LANG['_ADMIN']['Delete_Supplier_Entry_Message_Cont'].'</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_sp.$_GPV['s_company'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="s_id" value="'.$_GPV['s_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=edit&s_id='.$data['s_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=suppliers', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

		# Echo final output
			echo $_out;
}

IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do purge supplier
		$query 		= 'DELETE FROM '.$_DBCFG['suppliers'].' WHERE s_id='.$_GPV['s_id'];
		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$_del_results	= '<br />'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_2'].':'.$_sp.$db_coin->db_query_affected_rows();

	# Do purge supplier bills and bills items
		$query_i 		= 'SELECT bill_id FROM '.$_DBCFG['bills'].' WHERE bill_s_id='.$_GPV['s_id'];
		$result_i 	= $db_coin->db_query_execute($query_i) OR DIE("Unable to complete request");

	# Loop bill id's and delete items
		IF ($db_coin->db_query_numrows($result_i)) {
			while(list($bill_id) = $db_coin->db_fetch_array($result_i)) {
			# Do query for invoice items delete
				$query_ii 	= 'DELETE FROM '.$_DBCFG['bills_items'].' WHERE bi_bill_id='.$bill_id;
				$result_ii 	= $db_coin->db_query_execute($query_ii) OR DIE("Unable to complete request");
				$_del_results	.= '<br />'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_3'].':'.$_sp.$db_coin->db_query_affected_rows();

			# Do query for bills trans delete
				$query_it 	= 'DELETE FROM '.$_DBCFG['bills_trans'].' WHERE bt_bill_id='.$bill_id;
				$result_it 	= $db_coin->db_query_execute($query_it) OR DIE("Unable to complete request");
				$_del_results	.= '<br />'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_4'].':'.$_sp.$db_coin->db_query_affected_rows();
			}
		}

	# Delete the bill
		$query_i 		= 'DELETE FROM '.$_DBCFG['bills'].' WHERE bill_s_id='.$_GPV['s_id'];
		$result_i 	= $db_coin->db_query_execute($query_i) OR DIE("Unable to complete request");
		$_del_results	.= '<br />'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_5'].':'.$_sp.$db_coin->db_query_affected_rows();

	# Delete additional email addresses
		$query 		= 'DELETE FROM '.$_DBCFG['suppliers_contacts'].' WHERE contacts_s_id='.$_GPV['s_id'];
		$result 		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$_del_results	.= '<br />'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_6'].':'.$_sp.$db_coin->db_query_affected_rows();

	# Content start flag
		$_out = '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Supplier_Entry_Results'];

		IF (!$numrows) {
			$_cstr .= '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'<br />'.$_del_results.'<br /></center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_ADMIN']['Delete_Supplier_Entry_Results_1'].':<br />'.$_del_results.'<br /></center>';
		}

		$_mstr  = do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1 || $_PERMS['AP04'] == 1) {
			$_mstr .= do_nav_link('admin.php?cp=suppliers&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link('admin.php?cp=suppliers', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>