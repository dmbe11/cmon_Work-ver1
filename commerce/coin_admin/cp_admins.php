<?php
/**
 * Admin: Administrators
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Admins
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
		html_header_location('error.php?err=01&url=admin.php?cp=admins');
		exit;
	}

/**************************************************************
 * CP Functions Code
**************************************************************/
# Do decode perms: Admins (16-bit 0-65535)
function cp_do_decode_perms_admin($_AP) {
	# Decode into array
		$_bin = str_pad(decbin($_AP), 16, "0", STR_PAD_LEFT);
		$_PERMS['AP00']	= $_bin;
		$_PERMS['AP16']	= $_bin{0};
		$_PERMS['AP15']	= $_bin{1};
		$_PERMS['AP14']	= $_bin{2};
		$_PERMS['AP13']	= $_bin{3};
		$_PERMS['AP12']	= $_bin{4};
		$_PERMS['AP11']	= $_bin{5};
		$_PERMS['AP10']	= $_bin{6};
		$_PERMS['AP09']	= $_bin{7};
		$_PERMS['AP08']	= $_bin{8};
		$_PERMS['AP07']	= $_bin{9};
		$_PERMS['AP06']	= $_bin{10};
		$_PERMS['AP05']	= $_bin{11};
		$_PERMS['AP04']	= $_bin{12};
		$_PERMS['AP03']	= $_bin{13};
		$_PERMS['AP02']	= $_bin{14};
		$_PERMS['AP01']	= $_bin{15};

	# Return decoded array
		return $_PERMS;
}


# Do encode perms: Admins (16-bit 0-65535)
function cp_do_encode_perms_admin($_AP) {
	# Encode into 16-bit binary string
		IF ($_AP['AP16'] == 1) {
			$_AP['AP15'] = 1;
			$_AP['AP14'] = 1;
			$_AP['AP13'] = 1;
			$_AP['AP12'] = 1;
			$_AP['AP11'] = 1;
			$_AP['AP10'] = 1;
			$_AP['AP09'] = 1;
			$_AP['AP08'] = 1;
			$_AP['AP07'] = 1;
			$_AP['AP06'] = 1;
			$_AP['AP05'] = 1;
			$_AP['AP04'] = 1;
			$_AP['AP03'] = 1;
			$_AP['AP02'] = 1;
			$_AP['AP01'] = 1;
		}
		IF ($_AP['AP16'] != 1) {$_AP['AP16'] = 0;}
		IF ($_AP['AP15'] != 1) {$_AP['AP15'] = 0;}
		IF ($_AP['AP14'] != 1) {$_AP['AP14'] = 0;}
		IF ($_AP['AP13'] != 1) {$_AP['AP13'] = 0;}
		IF ($_AP['AP12'] != 1) {$_AP['AP12'] = 0;}
		IF ($_AP['AP11'] != 1) {$_AP['AP11'] = 0;}
		IF ($_AP['AP10'] != 1) {$_AP['AP10'] = 0;}
		IF ($_AP['AP09'] != 1) {$_AP['AP09'] = 0;}
		IF ($_AP['AP08'] != 1) {$_AP['AP08'] = 0;}
		IF ($_AP['AP07'] != 1) {$_AP['AP07'] = 0;}
		IF ($_AP['AP06'] != 1) {$_AP['AP06'] = 0;}
		IF ($_AP['AP05'] != 1) {$_AP['AP05'] = 0;}
		IF ($_AP['AP04'] != 1) {$_AP['AP04'] = 0;}
		IF ($_AP['AP03'] != 1) {$_AP['AP03'] = 0;}
		IF ($_AP['AP02'] != 1) {$_AP['AP02'] = 0;}
		IF ($_AP['AP01'] != 1) {$_AP['AP01'] = 0;}
		$_bin	= $_AP['AP16'].$_AP['AP15'].$_AP['AP14'].$_AP['AP13'].$_AP['AP12'].$_AP['AP11'].$_AP['AP10'].$_AP['AP09'];
		$_bin	.= $_AP['AP08'].$_AP['AP07'].$_AP['AP06'].$_AP['AP05'].$_AP['AP04'].$_AP['AP03'].$_AP['AP02'].$_AP['AP01'];
		$_dec	= bindec($_bin);

	# Return decoded array
		return $_dec;
}


# Do Data Input Validate
function cp_do_input_validation($_GPV) {
	# Initialize array
		$err_entry = array("flag" => 0);

	# Check modes and data as required
	#	IF (!$_GPV['admin_id']) 				{$err_entry['flag'] = 1;	$err_entry['admin_id'] = 1;}
		IF (!$_GPV['admin_user_name']) 		{$err_entry['flag'] = 1;	$err_entry['admin_user_name'] = 1;}
		IF (!$_GPV['admin_name_first']) 		{$err_entry['flag'] = 1;	$err_entry['admin_name_first'] = 1;}
		IF (!$_GPV['admin_name_last']) 		{$err_entry['flag'] = 1;	$err_entry['admin_name_last'] = 1;}
		IF (!$_GPV['admin_email']) 			{$err_entry['flag'] = 1;	$err_entry['admin_email'] = 1;}
	#	IF (!$_GPV['admin_perms']) 			{$err_entry['flag'] = 1;	$err_entry['admin_perms'] = 1;}

		IF ($_GPV['op']=='add') {
		# Check existing admin existing can leave passwords blank- match check below
			IF (!$_GPV['admin_user_pword'])	{$err_entry['flag'] = 1; $err_entry['admin_user_pword'] = 1;}
			IF (!$_GPV['admin_user_pword_re'])	{$err_entry['flag'] = 1; $err_entry['admin_user_pword_re'] = 1;}
		}

	# Email
		IF (do_validate_email($_GPV['admin_email'], 0)) {
			$err_entry['flag'] = 1;
			$err_entry['err_email_invalid'] = 1;
		}

	# Email does not match existing email
		$_ce = array(0,1,1,1,1);	// Element 0 = Nothing, 1 = clients, 2 = suppliers, 3 = admins, 4 = site addressses
		IF (do_email_exist_check($_GPV['admin_email'], $_GPV['admin_id'], $_ce)) {
			$err_entry['flag'] = 1; $err_entry['err_email_matches_another'] = 1;
		}

	# Passwords equal
		IF ($_GPV['admin_user_pword'] && $_GPV['admin_user_pword_re'] && ($_GPV['admin_user_pword'] != $_GPV['admin_user_pword_re'])) {
			$err_entry['flag'] = 1;
			$err_entry['err_pword_match'] = 1;
			$err_entry['admin_user_pword'] = 1;
			$err_entry['admin_user_pword_re'] = 1;
		}

		return $err_entry;
}


# Do list field form for: Admins
function cp_do_select_form_admin($aaction, $aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Set Query for select.
		$query	= 'SELECT admin_id, admin_user_name, admin_name_first, admin_name_last';
		$query .= ' FROM '.$_DBCFG['admins'];
		IF ($_PERMS['AP16'] != 1) {$query .= ' WHERE admin_id='.$_SEC['_sadmin_id'];}
		$query .= ' ORDER BY admin_name_last ASC, admin_name_first ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<FORM METHOD="POST" ACTION="'.$aaction.'">'.$_nl;
		$_out .= '<table cellpadding="5" width="100%">'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<b>'.$_LANG['_ADMIN']['l01_Administrator_Select'].$_sp.'('.$numrows.')</b><br>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'" onchange="submit();">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_ADMIN']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($admin_id, $admin_user_name, $admin_name_first, $admin_name_last) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$admin_id.'">'.$admin_name_last.', '.$admin_name_first.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '<tr><td class="TP3SML_NC">'.$_nl;
		$_out .= do_input_button_class_sw('b_load', 'SUBMIT', $_LANG['_ADMIN']['B_Load_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_out .= '</td></tr>'.$_nl;
		$_out .= '</table>'.$_nl;
		$_out .= '</FORM>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do Form for Add / Edit
function cp_do_form_add_edit_admin($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_cstr = '';

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
		$_td_str_left			= '<td class="TP1SML_NR" width="25%">';
		$_td_str_left_vtop		= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_right			= '<td class="TP1SML_NL" width="75%">';
		$_td_str_center_span	= '<td class="TP1SML_NC" width="100%" colspan="2">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $op_proper.$_sp.$_LANG['_ADMIN']['Admins_Entry'];

	# Build Temp Error Red Font Flag
		$_err_red_flag = '<font color="red"><b>-->> </b></font>';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
			$_cstr .= '<br><b>'.$_LANG['_ADMIN']['AD_ERR00__HDR1'].' '.$_err_red_flag.$_nl;
			$_cstr .= '<font color="red"><br>'.$_LANG['_ADMIN']['AD_ERR00__HDR2'].'</font><br><br>'.$_nl;
		}

	# Do Main Form
		$_cstr .= '<form name="ainfo" id="ainfo" method="POST" action="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="admins">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="'.$adata['op'].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="stage" value="1">'.$_nl;
		$_cstr .= '<input type="hidden" name="admin_id" value="'.$adata['admin_id'].'">'.$_nl;
		IF ($_PERMS['AP16'] != 1) {
			$_cstr .= '<input type="hidden" name="admin_perms" value="'.$adata['admin_perms'].'">'.$_nl;
		}

		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Admin_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($adata['op'] == 'add') {
			$_cstr .= '('.$_LANG['_ADMIN']['auto-assigned'].')'.$_nl;
		} ELSE {
			$_cstr .= $adata['admin_id'].$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['admin_name_first']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="admin_name_first" SIZE=20 value="'.htmlspecialchars($adata['admin_name_first']).'">'.$_nl;
		$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		IF ($aerr_entry['admin_name_first']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>'.$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['admin_name_last']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="admin_name_last" SIZE=20 value="'.htmlspecialchars($adata['admin_name_last']).'">'.$_nl;
		$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		IF ($aerr_entry['admin_name_last']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>'.$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['admin_user_name']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_Admin_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="admin_user_name" SIZE="30" value="'.htmlspecialchars($adata['admin_user_name']).'">'.$_nl;
		$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;
		IF ($aerr_entry['admin_user_name']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>'.$_nl;}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['admin_email'] || $aerr_entry['err_email_matches_another'] || $aerr_entry['err_email_invalid']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_Email_Address'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT NAME="admin_email" SIZE=50 value="'.htmlspecialchars($adata['admin_email']).'">'.$_nl;
		$_cstr .= $_LANG['_ADMIN']['Required'];
		IF ($aerr_entry['admin_email'])				{$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		IF ($aerr_entry['err_email_matches_another'])	{$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR33'].'</font>';}
		IF ($aerr_entry['err_email_invalid'])			{$_cstr .= '<font color="red">'.$_sp.$_LANG['_ADMIN']['ERR_ERR30'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# If existing user, add note password for change only
		IF ($adata['op'] == 'edit') {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_LANG['_ADMIN']['Password_Note'].'</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_autopass = do_password_create();
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.'</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<script type="text/javascript">document.write("<input type=\"button\" name=\"gp\" value=\"'.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_TEXT'].'\" onclick=\"document.ainfo.admin_user_pword.type=\'text\'; document.ainfo.admin_user_pword_re.type=\'text\'; document.ainfo.admin_user_pword.value=\''.$_autopass.'\'; document.ainfo.admin_user_pword_re.value=\''.$_autopass.'\'; document.ainfo.gp.disabled=\'disabled\'; document.ainfo.gp.value=\''.$_LANG['_BASE']['AUTOPASSWORD_BUTTON_REMEMBER'].'\';\">")</script>';
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;

		IF ($aerr_entry['admin_user_pword']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_Password'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=PASSWORD NAME="admin_user_pword" SIZE="30" value="'.htmlspecialchars($adata['admin_user_pword']).'">'.$_nl;
		IF ($adata['op'] == 'add') {$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;}
		IF ($aerr_entry['admin_user_pword']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($aerr_entry['admin_user_pword_re'] || $aerr_entry['admin_user_pword_match']) {$_tmp = $_err_red_flag;} ELSE {$_tmp = '';}
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_tmp.'<b>'.$_LANG['_ADMIN']['l01_Password_Confirm'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=PASSWORD NAME="admin_user_pword_re" SIZE="30" value="'.htmlspecialchars($adata['admin_user_pword_re']).'">'.$_nl;
		IF ($adata['op'] == 'add') {$_cstr .= $_LANG['_ADMIN']['Required'].$_nl;}
		IF ($aerr_entry['err_pword_match']) {
			$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['AD01_ERR_12'].'</font>';
			$adata['cl_user_pword'] = '';
			$adata['cl_user_pword_re'] = '';
		}
		IF ($aerr_entry['err_user_pword_re']) {$_cstr .= $_sp.'<font color="red">'.$_LANG['_ADMIN']['ERR_ERR38'].'</font>';}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		IF ($_PERMS['AP16'] == 1) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l01_Permissions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_APERMS = cp_do_decode_perms_admin($adata['admin_perms']);
			IF ($_APERMS['AP16']==1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP16'] = 0;}
			$_cstr .= '<INPUT TYPE=CHECKBOX NAME="AP16" value="1"'.$_set.' border="0">'.$_nl;
			$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_16'].'</b>'.$_nl;
			IF ($_LANG['_BASE']['Permissions_15'] != '') {
				IF ($_APERMS['AP15']==1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP15'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP15" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_15'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_14'] != '') {
				IF ($_APERMS['AP14']==1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP14'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP14" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_14'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_13'] != '') {
				IF ($_APERMS['AP13'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP13'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP13" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_13'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_12'] != '') {
				IF ($_APERMS['AP12'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP12'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP12" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_12'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_11'] != '') {
				IF ($_APERMS['AP11'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP11'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP11" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_11'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_10'] != '') {
				IF ($_APERMS['AP10'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP10'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP10" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_10'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_09'] != '') {
				IF ($_APERMS['AP09'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP09'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP09" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_09'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_08'] != '') {
				IF ($_APERMS['AP08'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP08'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP08" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_08'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_07'] != '') {
				IF ($_APERMS['AP07'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP07'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP07" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_07'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_06'] != '') {
				IF ($_APERMS['AP06'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP06'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP06" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_06'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_05'] != '') {
				IF ($_APERMS['AP05'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP05'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP05" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_05'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_04'] != '') {
				IF ($_APERMS['AP04'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP04'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP04" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_04'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_03'] != '') {
				IF ($_APERMS['AP03'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP03'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP03" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_03'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_02'] != '') {
				IF ($_APERMS['AP02'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP02'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP02" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_02'].'</b>'.$_nl;
			}
			IF ($_LANG['_BASE']['Permissions_01'] != '') {
				IF ($_APERMS['AP01'] == 1) {$_set = ' CHECKED';} ELSE {$_set = ''; $adata['AP01'] = 0;}
				$_cstr .= '<br><INPUT TYPE=CHECKBOX NAME="AP01" value="1"'.$_set.' border="0">'.$_nl;
				$_cstr .= $_sp.'<b>'.$_LANG['_BASE']['Permissions_01'].'</b>'.$_nl;
			}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= '<td class="TP0SML_NR" width="25%">'.$_nl;
		$_cstr .= $_sp.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '<td class="TP0SML_NL" width="75%">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_edit', 'SUBMIT', $op_button, 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_ADMIN']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		IF ($_PERMS['AP16'] == 1 && $adata['op'] == 'edit') {
			$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		}
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;
		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function cp_do_display_entry_admin ($adata, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left		= '<td class="TP1SML_NR" width="25%">';
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_right		= '<td class="TP1SML_NL" width="75%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr  = '<table width="100%">'.$_nl;
		$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
		$_tstr .= '<td class="TP3MED_BL">'.$adata['admin_user_name'].'</td>'.$_nl;
		$_tstr .= '<td class="TP3MED_BR">'.$_sp.'</td>'.$_nl;
		$_tstr .= '</tr>'.$_nl;
		$_tstr .= '</table>'.$_nl;

		$_cstr  = '<table width="100%">'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Admin_ID'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_id'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Admin_User_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_user_name'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Password'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_user_pword'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_First_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_name_first'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Last_Name'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_name_last'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr valign="bottom">'.$_nl;
		$_cstr .= $_td_str_left.'<b>'.$_LANG['_ADMIN']['l01_Email_Address'].$_sp.'</b></td>'.$_nl;
		$_cstr .= $_td_str_right.$adata['admin_email'].'</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		IF ($_PERMS['AP16'] == 1) {
			$_VPERMS = cp_do_decode_perms_admin($adata['admin_perms']);
			$_cstr .= '<tr valign="bottom">'.$_nl;
			$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_ADMIN']['l01_Permissions'].$_sp.'</b></td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= $adata['admin_perms'].$_nl;
			IF ($_VPERMS['AP16'] == 1 && $_LANG['_BASE']['Permissions_16'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_16'];
			}
			IF ($_VPERMS['AP15'] == 1 && $_LANG['_BASE']['Permissions_15'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_15'];
			}
			IF ($_VPERMS['AP14'] == 1 && $_LANG['_BASE']['Permissions_14'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_14'];
			}
			IF ($_VPERMS['AP13'] == 1 && $_LANG['_BASE']['Permissions_13'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_13'];
			}
			IF ($_VPERMS['AP12'] == 1 && $_LANG['_BASE']['Permissions_12'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_12'];
			}
			IF ($_VPERMS['AP11'] == 1 && $_LANG['_BASE']['Permissions_11'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_11'];
			}
			IF ($_VPERMS['AP10'] == 1 && $_LANG['_BASE']['Permissions_10'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_10'];
			}
			IF ($_VPERMS['AP09'] == 1 && $_LANG['_BASE']['Permissions_09'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_09'];
			}
			IF ($_VPERMS['AP08'] == 1 && $_LANG['_BASE']['Permissions_08'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_08'];
			}
			IF ($_VPERMS['AP07'] == 1 && $_LANG['_BASE']['Permissions_07'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_07'];
			}
			IF ($_VPERMS['AP06'] == 1 && $_LANG['_BASE']['Permissions_06'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_06'];
			}
			IF ($_VPERMS['AP05'] == 1 && $_LANG['_BASE']['Permissions_05'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_05'];
			}
			IF ($_VPERMS['AP04'] == 1 && $_LANG['_BASE']['Permissions_04'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_04'];
			}
			IF ($_VPERMS['AP03'] == 1 && $_LANG['_BASE']['Permissions_03'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_03'];
			}
			IF ($_VPERMS['AP02'] == 1 && $_LANG['_BASE']['Permissions_02'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_02'];
			}
			IF ($_VPERMS['AP01'] == 1 && $_LANG['_BASE']['Permissions_01'] != '') {
				IF ($_p != '') {$_p .= ', ';} $_p .= $_LANG['_BASE']['Permissions_01'];
			}
			IF ($_p != '') {$_cstr .= '<br>'.$_p.$_nl;}
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}
		$_cstr .= '</table>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=edit&admin_id='.$adata['admin_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		IF ($_PERMS['AP16'] == 1) {
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		}
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 * CP Base Code
**************************************************************/
# Get security vars
	$_SEC 	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

# Check $op (operation switch)
	switch($_GPV['op']) {
		case "add":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "edit":
			IF ($_GPV['b_delete'] != '') {$_GPV['op'] = 'delete';}
			break;
		case "delete":
			break;
		default:
			$_GPV['op'] = 'none';
			break;
	} #end op switch

# Check required fields (err / action generated later in cade as required)
	IF ($_GPV['stage'] == 1) {
	# Encode perms fields
		$_GPV['admin_perms'] = cp_do_encode_perms_admin($_GPV);

	# Call validate input function
		$err_entry = cp_do_input_validation($_GPV);
	}

# Build Data Array (may also be over-ridden later in code)
	$data = $_GPV;


##############################
# Operation:	Any Perm Check
# Summary:
#	- Exit out on perm error.
##############################
IF ($_PERMS['AP16'] != 1) {
	$_GPV['admin_id'] = $_SEC['_sadmin_id'];
	IF ($_GPV['op'] == 'add' || $_GPV['op'] == 'delete') {
		$_out .= '<!-- Start content -->'.$_nl;
		$_out .= do_no_permission_message();
		$_out .= '<br>'.$_nl;

	# Echo final output and exit
		echo $_out;
		exit;
	}
}


##############################
# Operation:	None
# Summary:
#	- Go To Control Panel Menu
##############################
IF ($_GPV['op'] == 'none') {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Admins_Editor'];

	# Call function for create select form: Admins
		$_cstr = cp_do_select_form_admin($_SERVER["PHP_SELF"].'?cp=admins&op=edit', 'admin_id', $_GPV['admin_id'],'1');

		$_mstr = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],'');
		IF ($_PERMS['AP16'] == 1) {
			$_mstr = do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
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
IF ($_GPV['op']=='add' && (!$_GPV['stage'] || $err_entry['flag'])) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_admin($data, $err_entry,'1').$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation:	Add Results
# Summary:
#	- For processing added entry
#	- Do table insert
#	- Display results
##############################
IF ($_GPV['op']=='add' && $_GPV['stage']==1 && !$err_entry['flag']) {
	# Generate encrypted password
		$admin_user_pword_crypt = do_password_crypt($_GPV['admin_user_pword']);

	# Do select
		$query	 = 'INSERT INTO '.$_DBCFG['admins'].' (';
		$query	.= 'admin_user_name, admin_user_pword';
		$query	.= ', admin_name_first, admin_name_last';
		$query	.= ', admin_email, admin_perms';
		$query 	.= ') VALUES (';
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['admin_user_name'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($admin_user_pword_crypt)."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['admin_name_first'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['admin_name_last'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['admin_email'])."', ";
		$query	.= "'".$db_coin->db_sanitize_data($_GPV['admin_perms'])."'";
		$query 	.= ')';
		$result		= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$insert_id	= $db_coin->db_query_insertid();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ADMIN']['Add_Admins_Entry_Results'].$_sp.'('.$_LANG['_ADMIN']['Inserted_ID'].$_sp.$insert_id.')';
		$_out .= do_subj_block_it($title_text, '1');

	# Adjust Data Array with returned record
		$data['insert_id']	= $insert_id;
		$data['admin_id']	= $insert_id;

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_admin($data, '1');
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
IF ($_GPV['op']=='edit' && (!$_GPV['stage'] || $err_entry['flag'])) {
# If Stage and Error Entry, pass field vars to form,
# Otherwise, pass looked up record to form
	IF ($_GPV['stage'] == 1 && $err_entry['flag']) {
	# Call function for add/edit form
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= cp_do_form_add_edit_admin($data, $err_entry,'1').$_nl;
	# Echo final output
		echo $_out;

	} ELSE {
	# Check for valid $_GPV[admin_id] no
		IF ($_GPV['admin_id']) {
		# Do select of admin record
			$query 	 = 'SELECT * FROM '.$_DBCFG['admins'];
			$query 	.= ' WHERE admin_id='.$_GPV['admin_id'];
			$query 	.= ' ORDER BY admin_id ASC';

		# Do select
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Process query results (assumes one returned row above)
			IF ($numrows) {
				while ($row = $db_coin->db_fetch_array($result)) {
				# Merge Data Array with returned row
					$data_new					= array_merge($data, $row);
					$data					= $data_new;
					$data['admin_user_pword']	= ''; # Do not load password as it is encrypted.
					$data['admin_user_pword_re']	= ''; # Do not load password as it is encrypted.
				}
			}

		# Call function for add/edit form
			$_out = '<!-- Start content -->'.$_nl;
			$_out .= cp_do_form_add_edit_admin($data, $err_entry,'1').$_nl;

		} ELSE {
		# Content start flag
			$_out .= '<!-- Start content -->'.$_nl;

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_ADMIN']['Admins_Editor'];

		# Call function for create select form: Admins
			$_cstr  = cp_do_select_form_admin($_SERVER["PHP_SELF"].'?cp=admins&op=edit', 'admin_id', $_GPV['admin_id'],'1');
			$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
			$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
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
	# Generate encrypted password
		$admin_user_pword_crypt = do_password_crypt($_GPV['admin_user_pword']);


		$query  = 'UPDATE '.$_DBCFG['admins'].' SET ';
		$query .= "admin_user_name='".$db_coin->db_sanitize_data($_GPV['admin_user_name'])."', ";
		IF ($_GPV['admin_user_pword']) {$query .= "admin_user_pword='".$db_coin->db_sanitize_data($admin_user_pword_crypt)."', ";}
		$query .= "admin_name_first='".$db_coin->db_sanitize_data($_GPV['admin_name_first'])."', ";
		$query .= "admin_name_last='".$db_coin->db_sanitize_data($_GPV['admin_name_last'])."', ";
		$query .= "admin_email='".$db_coin->db_sanitize_data($_GPV['admin_email'])."'";
		IF ($_PERMS['AP16'] == 1) {$query .= ", admin_perms='".$db_coin->db_sanitize_data($_GPV['admin_perms'])."'";}
		$query .= " WHERE admin_id='".$db_coin->db_sanitize_data($_GPV['admin_id'])."'";

		$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Call function to open block
		$title_text = $_LANG['_ADMIN']['Edit_Admins_Entry_Results'];
		$_out .= do_subj_block_it($title_text, '1');

	# Call function for Display Entry
		$_out .= '<br>'.$_nl;
		$_out .= cp_do_display_entry_admin($data, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


##############################
# Operation: Delete Entry
# Summary Stage 1:
#	- Confirm delete entry
# Summary Stage 2:
#	- Do table update
#	- Display results
##############################
IF ($_GPV['op']=='delete' && $_GPV['stage']==1) {
	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Admins_Entry_Confirmation'];

	# Do confirmation form to content string
		$_cstr = '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_cstr .= '<input type="hidden" name="cp" value="admins">'.$_nl;
		$_cstr .= '<input type="hidden" name="op" value="delete">'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<b>'.$_LANG['_ADMIN']['Delete_Admins_Entry_Message'].$_sp.'='.$_sp.$_GPV['admin_id'].'?</b>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_GPV['admin_id'].$_sp.'-'.$_sp.$_GPV['admin_user_name'].$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP0MED_NC">'.$_sp.'</td></tr>'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="2">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="admin_id" value="'.$_GPV['admin_id'].'">'.$_nl;
		$_cstr .= do_input_button_class_sw('b_delete', 'SUBMIT', $_LANG['_ADMIN']['B_Delete_Entry'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</FORM>'.$_nl;

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=edit&admin_id='.$_GPV['admin_id'], $_TCFG['_IMG_EDIT_M'],$_TCFG['_IMG_EDIT_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


IF ($_GPV['op'] == 'delete' && $_GPV['stage'] == 2) {
	# Do select
		$query	= 'DELETE FROM '.$_DBCFG['admins'].' WHERE admin_id='.$_GPV['admin_id'];
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$eff_rows	= $db_coin->db_query_affected_rows();

	# Content start flag
		$_out .= '<!-- Start content -->'.$_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_ADMIN']['Delete_Admins_Entry_Results'];

		IF (!$eff_rows) {
			$_cstr .= '<center>'.$_LANG['_ADMIN']['An_error_occurred'].'</center>';
		} ELSE {
			$_cstr .= '<center>'.$_LANG['_ADMIN']['Entry_Deleted'].'</center>';
		}

		$_mstr  = do_nav_link($_SERVER["PHP_SELF"], $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins&op=add', $_TCFG['_IMG_ADD_NEW_M'],$_TCFG['_IMG_ADD_NEW_M_MO'],'','');
		$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?cp=admins', $_TCFG['_IMG_LISTING_M'],$_TCFG['_IMG_LISTING_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}
?>