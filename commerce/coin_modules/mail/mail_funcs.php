<?php
/**
 * Module: Mail (Common Functions)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Mail
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright � 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_mail.php
 */

# Code to handle file being loaded by URL
	IF (eregi('mail_funcs.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=mail');
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
		IF ($_GPV['mode'] == 'contact') {
		# Check required fields (err / action generated later in cade as required)
			IF (!$_GPV['mc_id']) 	{$err_entry['flag'] = 1; $err_entry['mc_id'] = 1;}
			IF (!$_GPV['mc_name']) 	{$err_entry['flag'] = 1; $err_entry['mc_name'] = 1;}
			IF (!$_GPV['mc_email'])	{$err_entry['flag'] = 1; $err_entry['mc_email'] = 1;}
			IF (!$_GPV['mc_subj']) 	{$err_entry['flag'] = 1; $err_entry['mc_subj'] = 1;}
			IF (!$_GPV['mc_msg']) 	{$err_entry['flag'] = 1; $err_entry['mc_msg'] = 1;}

		# Validate Email Address
			IF (do_validate_email($_GPV['mc_email'], 0)) {
				$err_entry['flag'] = 1;
				$err_entry['mc_email'] = 1;
				$err_entry['err_email_invalid'] = 1;
			}

		# Validate the captcha if "GD" is loaded
			IF (extension_loaded('gd') && file_exists(PKG_PATH_ADDONS.'captcha')) {
				IF ($_SESSION['security_code'] != $_GPV['security_code'] || empty($_SESSION['security_code'])) {
     				$err_entry['flag'] = 1; $err_entry['sec_code'] = 1;
	    				unset($_SESSION['security_code']);
				}
			}
		}

		IF ($_GPV['mode'] == 'client') {
		# Check required fields (err / action generated later in cade as required)
			IF (!$_GPV['cc_cl_id']) 	{$err_entry['flag'] = 1; $err_entry['cc_cl_id'] = 1;}
			IF (!$_GPV['cc_mc_id']) 	{$err_entry['flag'] = 1; $err_entry['cc_mc_id'] = 1;}
			IF (!$_GPV['cc_subj']) 	{$err_entry['flag'] = 1; $err_entry['cc_subj'] = 1;}
			IF (!$_GPV['cc_msg']) 	{$err_entry['flag'] = 1; $err_entry['cc_msg'] = 1;}
		}

	# Return results
		return $err_entry;
}


# Do password reset form
function do_pword_reset_form($adata, $aret_flag=0) {
	# Dim some Vars:
		global $_TCFG, $_LANG, $_nl, $_sp;

	# Some HTML Strings (reduce text)
		$_td_str_left			= '<td class="TP3SML_NR" width="30%">';
		$_td_str_right			= '<td class="TP3SML_NL" width="70%">';
		$_td_str_center_span	= '<td class="TP3SML_NC" width="100%" colspan="2">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_LANG['_MAIL']['Password_Reset_Request'].$_sp;
		IF ($adata['w'] == 'admin') {$_tstr .= $_LANG['_MAIL']['Administrator'];} ELSE {$_tstr .= $_LANG['_MAIL']['Client'];}

		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<form action="'.$_SERVER["PHP_SELF"].'" method="post" name="reset">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_center_span.$_nl;
		$_cstr .= $_LANG['_MAIL']['Password_Reset_Message_01'].$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_User_Name'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="username" size="30" maxlength="20">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Email'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="email" size="30" maxlength="100">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="mod" value="'.$adata['mod'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="mode" value="'.$adata['mode'].'">'.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="w" value="'.$adata['w'].'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_subreq', 'SUBMIT', $_LANG['_MAIL']['B_Submit_Request'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_MAIL']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '<tr><td align="right">'.$_nl;
		IF ($adata['w'] == 'admin') {
			$_cstr .= '<a href="mod.php?mod=mail&mode=reset&w=user">'.$_TCFG['_IMG_CLIENTS_M'].'</a>';
		} ELSE {
			IF ($_CCFG['_ENABLE_ADMIN_LOGIN_LINK']) {$_cstr .= do_nav_link('mod.php?mod=mail&mode=reset&w=admin', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
		}
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr 		= ''.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return / Echo Final Output
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do password reset code
function do_mail_pword_reset($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Prevent email address hacking
		IF (do_validate_email($adata['email'],0)) {$adata['email'] = '';}

	# Check username exist
		IF ($adata['w'] == 'admin' || $adata['w'] == 'user') {
			$_user_check = do_user_name_exist_check($adata['username'], $adata['w']);
		}

	# IF existing, get username email for comparison and compare
		IF ($adata['w'] == 'admin' || $adata['w'] == 'user') {
			$_user_email	= get_user_name_email($adata['username'], $adata['w']);
			$_email_check	= 1;
			IF ($_user_email != $adata['email']) {$_email_check = 0;}
		}

	# Check return on user / admin exist check
		IF (!$_user_check || !$_email_check) {

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_MAIL']['PWORD_RESET_RESULT_TITLE'];

			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			IF (!$_user_check) {
				$_cstr .= $_LANG['_MAIL']['PWORD_RESET_MSG_01A'].$_nl;
				IF (!$_email_check) {$_cstr .= '<br>'.$_LANG['_MAIL']['PWORD_RESET_MSG_01B'].$_nl;}
			} ELSEIF (!$_email_check) {
				$_cstr .= $_LANG['_MAIL']['PWORD_RESET_MSG_01B'].$_nl;
			}
			$_cstr .= '<br><br><a href="mod.php?mod=mail&mode=reset&w='.$adata['w'].'">'.$_TCFG['_IMG_TRY_AGAIN_M'].'</a>';
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '<tr><td align="right">'.$_nl;
			IF ($adata['w'] == 'admin') {
				$_cstr .= '<a href="mod.php?mod=mail&mode=reset&w=user">'.$_TCFG['_IMG_CLIENTS_M'].'</a>';
			} ELSE {
				$_cstr .= '<a href="mod.php?mod=mail&mode=reset&w=admin">'.$_TCFG['_IMG_ADMIN_M'].'</a>';
			}
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;

			$_mstr_flag	= 0;
			$_mstr		= $_sp.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;

		} ELSE {
		# Generate New Password String and encrypt for db
			$_nps 		= do_password_create();
			$_nps_encrypt	= do_password_crypt($_nps);

		# Update proper database with new password
			IF ($adata['w'] == 'admin') {
				$query  = 'UPDATE '.$_DBCFG['admins'].' SET ';
				$query .= "admin_user_pword='".$db_coin->db_sanitize_data($_nps_encrypt)."' ";
				$query .= "WHERE admin_user_name='".$db_coin->db_sanitize_data($adata['username'])."'";
			}
			IF ($adata['w'] == 'user') {
				$query = 'UPDATE '.$_DBCFG['clients'].' SET ';
				$query .= "cl_user_pword='".$db_coin->db_sanitize_data($_nps_encrypt)."' ";
				$query .= "WHERE cl_user_name='".$db_coin->db_sanitize_data($adata['username'])."'";
			}
			$result 	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
			$eff_rows	= $db_coin->db_query_affected_rows();

		# Check update and continue
			IF (!$eff_rows) {
				$_ret_msg = $_LANG['_MAIL']['PWORD_RESET_MSG_02_L1'];
				$_ret_msg .= '<br>'.$_LANG['_MAIL']['PWORD_RESET_MSG_02_L2'];
			} ELSE {
			# Set eMail Parameters (pre-eval template, some used in template)
				$mail['recip']		= get_user_name_email($adata['username'], $adata['w']);
				$mail['from']		= $_CCFG['_PKG_EMAIL_MAIL'];
				$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_MAIL']['PWORD_RESET_SUBJECT_PRE'];

			# Grab client record for passing into email template
				$cl_query 	= 'SELECT * FROM '.$_DBCFG['clients']." WHERE cl_user_name='".$db_coin->db_sanitize_data($adata['username'])."'";
				$cl_result 	= $db_coin->db_query_execute($cl_query) OR DIE("Unable to complete request");
				IF ($db_coin->db_query_numrows($cl_result)) {
					while ($row = $db_coin->db_fetch_array($cl_result)) {
						$_MTP['cl_id']			= $row['cl_id'];
						$_MTP['cl_join_ts']		= $row['cl_join_ts'];
						$_MTP['cl_status']		= $row['cl_status'];
						$_MTP['cl_company']		= $row['cl_company'];
						$_MTP['cl_name_first']	= $row['cl_name_first'];
						$_MTP['cl_name_last']	= $row['cl_name_last'];
						$_MTP['cl_addr_01']		= $row['cl_addr_01'];
						$_MTP['cl_addr_02']		= $row['cl_addr_02'];
						$_MTP['cl_city']		= $row['cl_city'];
						$_MTP['cl_state_prov']	= $row['cl_state_prov'];
						$_MTP['cl_country']		= $row['cl_country'];
						$_MTP['cl_zip_code']	= $row['cl_zip_code'];
						$_MTP['cl_phone']		= $row['cl_phone'];
						$_MTP['cl_email']		= $row['cl_email'];
						$_MTP['cl_user_name']	= $row['cl_user_name'];
						$_MTP['cl_notes']		= $row['cl_notes'];
						$_MTP['cl_groups']		= $row['cl_groups'];
					}
				}

			# Set MTP (Mail Template Parameters) array
				$_MTP['from_email']	= $mail['from'];
				$_MTP['username']	= $adata['username'];
				$_MTP['password']	= $_nps;
				$_MTP['url']		= BASE_HREF.'login.php?w='.$adata['w'].'&o=login';
				$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];

			# Load message template (processed)
				$mail['message']	.= get_mail_template('email_password_reset', $_MTP);

			# Call basic email function
				$_ret = do_mail_basic($mail);

			# Check return
				IF ($_ret) {
					$_ret_msg  = $_LANG['_MAIL']['PWORD_RESET_MSG_03_L1'];
					$_ret_msg .= '<br>'.$_LANG['_MAIL']['PWORD_RESET_MSG_03_L2'];
				} ELSE {
					$_ret_msg  = $_LANG['_MAIL']['PWORD_RESET_MSG_04_L1'];
					$_ret_msg .= $_sp.$_LANG['_MAIL']['PWORD_RESET_MSG_04_L2'];
				}
			}

		# Build Title String, Content String, and Footer Menu String
			$_tstr = $_LANG['_MAIL']['PWORD_RESET_RESULT_TITLE'];

			$_cstr .= '<center>'.$_nl;
			$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
			$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
			$_cstr .= $_ret_msg.$_nl;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</center>'.$_nl;

			$_mstr_flag	= 0;
			$_mstr		= '&nbsp;'.$_nl;

		# Call block it function
			$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
			$_out .= '<br>'.$_nl;
		}

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
	}


# Do contact form (contact user to site)
function do_contact_form($adata, $aerr_entry, $aret_flag=0) {
	# Get security vars
		$_SEC 	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Check for admin / user and get email address accordingly
		IF ($_SEC['_sadmin_flg'] && $_SEC['_sadmin_name'] && !$adata['mc_email']) {
			$adata['mc_email'] = get_user_name_email($_SEC['_sadmin_name'], 'admin');
		}
		IF ($_SEC['_suser_flg'] && $_SEC['_suser_name'] && !$adata['mc_email']) {
			$adata['mc_email'] = get_user_name_email($_SEC['_suser_name'], 'user');
		}

	# Some HTML Strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="30%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_CCFG['_PKG_NAME_SHORT'].$_sp.$_LANG['_MAIL']['Contact_Form'].$_sp.'('.$_LANG['_MAIL']['all_fields_required'].')';

	# Add "Edit" button for editing contact info parameters if admin and has permission
		IF ($_SEC['_sadmin_flg'] && $_CCFG['ENABLE_QUICK_EDIT'] && ($_PERMS['AP16'] == 1 || $_PERMS['AP15'] == 1)) {
			$_tstr .= ' <a href="admin.php?cp=parms&fpg=user">'.$_TCFG['_S_IMG_PM_S'].'</a>';
		}

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_MAIL']['CS_FORM_ERR_HDR1'].'<br>'.$_LANG['_MAIL']['CS_FORM_ERR_HDR2'].'<br>'.$_nl;
	 		IF ($aerr_entry['mc_id']) 	{$err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['mc_name']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR02']; $err_prv = 1;}
			IF ($aerr_entry['mc_email']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR03']; $err_prv = 1;}
			IF ($aerr_entry['mc_subj']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR04']; $err_prv = 1;}
			IF ($aerr_entry['mc_msg']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR05']; $err_prv = 1;}
			IF ($aerr_entry['sec_code']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR06']; $err_prv = 1;}
	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Check Stage for extra data validation
		IF ($adata['stage'] == 1) {
		# Email
			IF ($aerr_entry['err_email_invalid']) {$_err_more .= '<br>'.$_LANG['_MAIL']['CS_FORM_ERR_MSG_01'].$_nl;}

		# Print out more errors
			IF ($_err_more) {$_cstr .= '<b>'.$_err_more.'</b>'.$_nl;}
		}

	# Formatting tweak for spacing
		IF ($aerr_entry['flag'] || $_err_more) {$_cstr .= '<br><br>'.$_nl;}

		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;

	# Display othr contact info, if enabled
		IF ($_UVAR['DISPLAY_ON_CONTACT_FORM']) {
			$_cstr .= '<tr><td>'.$_nl;
			$_cstr .= '<b>'.$_LANG['_MAIL']['CC_FORM_TITLE_MAIL'].'</b><br>';
			IF ($_UVAR['CO_INFO_01_NAME'])				{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_01_NAME'].'<br>';}
			IF ($_UVAR['CO_INFO_12_TAGLINE'])				{$_cstr .= $_sp.$_sp.$_sp.'<i>"'.$_UVAR['CO_INFO_12_TAGLINE'].'"</i><br>';}
			IF ($_UVAR['CO_INFO_02_ADDR_01'])				{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_02_ADDR_01'].'<br>';}
			IF ($_UVAR['CO_INFO_03_ADDR_02'])				{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_03_ADDR_02'].'<br>';}
			IF ($_UVAR['CO_INFO_04_CITY'])				{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_04_CITY'].', ';}
			IF ($_UVAR['CO_INFO_05_STATE_PROV'])			{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_05_STATE_PROV'].', ';}
			IF ($_UVAR['CO_INFO_06_POSTAL_CODE'])			{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_06_POSTAL_CODE'].'<br>';}
			IF ($_UVAR['CO_INFO_07_COUNTRY'])				{$_cstr .= $_sp.$_sp.$_sp.$_UVAR['CO_INFO_07_COUNTRY'].'<br>';}
			IF ($_UVAR['CO_INFO_08_PHONE'] || $_UVAR['CO_INFO_09_FAX'] || $_UVAR['CO_INFO_11_TOLL_FREE'] || $_LANG['_MAIL']['CC_FORM_DATA_TELECOM']) {
				$_cstr .= '<br><b>'.$_LANG['_MAIL']['CC_FORM_TITLE_TELECOM'].'</b><br>';
				IF ($_UVAR['CO_INFO_08_PHONE'])			{$_cstr .= $_sp.$_sp.$_sp.$_LANG['_BASE']['LABEL_PHONE'].' '.$_UVAR['CO_INFO_08_PHONE'].'<br>';}
				IF ($_UVAR['CO_INFO_09_FAX'])				{$_cstr .= $_sp.$_sp.$_sp.$_LANG['_BASE']['LABEL_FAX'].' '.$_UVAR['CO_INFO_09_FAX'].'<br>';}
				IF ($_UVAR['CO_INFO_11_TOLL_FREE'])		{$_cstr .= $_sp.$_sp.$_sp.$_LANG['_BASE']['LABEL_TOLL_FREE'].' '.$_UVAR['CO_INFO_11_TOLL_FREE'].'<br>';}
				IF ($_LANG['_MAIL']['CC_FORM_DATA_TELECOM']) {$_cstr .= '&nbsp;&nbsp;&nbsp;'.$_LANG['_MAIL']['CC_FORM_DATA_TELECOM'].'</b><br>';}
			}
			IF ($_LANG['_MAIL']['CC_FORM_DATA_OTHER']) {
				$_cstr .= '<br><b>'.$_LANG['_MAIL']['CC_FORM_TITLE_OTHER'].'</b><br>';
				$_cstr .= '&nbsp;&nbsp;&nbsp;'.$_LANG['_MAIL']['CC_FORM_DATA_OTHER'].'</b><br>';
			}
			$_cstr .= '<br><b>'.$_LANG['_MAIL']['CC_FORM_TITLE_EMAIL'].'</b><br>';
			$_cstr .= '<br></td></tr>';
		}

		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<form action="mod.php" method="post" name="contact">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="mail">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="contact">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="5" border="0">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_To'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_mail_contacts('mc_id', $adata['mc_id'], 1);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Name'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="mc_name" size="30" maxlength="50" value="'.htmlspecialchars($adata['mc_name']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Email'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="mc_email" size="30" maxlength="50" value="'.htmlspecialchars($adata['mc_email']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="mc_subj" size="30" maxlength="50" value="'.htmlspecialchars($adata['mc_subj']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="mc_msg" COLS="'.$_cols.'" ROWS="15">'.$adata['mc_msg'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

	# Captcha if "GD" is loaded
		IF (extension_loaded('gd') && file_exists(PKG_PATH_ADDONS.'captcha')) {
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= $_LANG['_MAIL']['SC_Instruct'].$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.$_sp.'</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<img src="'.PKG_URL_ADDONS.'captcha/CaptchaSecurityImages.php">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
			$_cstr .= '<tr>'.$_nl;
			$_cstr .= $_td_str_left_vtop.$_nl;
			$_cstr .= '<b>'.$_LANG['_MAIL']['l_Security_Code'].$_sp.'</b>'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= $_td_str_right.$_nl;
			$_cstr .= '<input class="PSML_NL" id="security_code" name="security_code" type="text" value="'.htmlspecialchars($adata['security_code']).'">'.$_nl;
			$_cstr .= '</td>'.$_nl;
			$_cstr .= '</tr>'.$_nl;
		}

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_MAIL']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_MAIL']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr 		= ''.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return / Echo Final Output
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do process contact user-to-site email form (build, set email))
function do_contact_email($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get contact information array
		$_cinfo = get_contact_info($adata['mc_id']);

	# Set eMail Parameters (pre-eval template, some used in template)
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   			$mail['recip']	= $_cinfo['c_email'];
			$mail['from']	= $adata['mc_email'];
			IF ($_CCFG['CONTACT_FORM_CC']) {$mail['cc']	= $adata['mc_email'];}
		} ELSE {
			$mail['recip']	= $_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
			$mail['from']	= $adata['mc_name'].' <'.$adata['mc_email'].'>';
			IF ($_CCFG['CONTACT_FORM_CC']) {$mail['cc']	= $adata['mc_name'].' <'.$adata['mc_email'].'>';}
		}
		$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].'- Contact Message';

	# Grab ip_address of sender
		IF (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
			$pos = strpos(strtolower($_SERVER['HTTP_X_FORWARDED_FOR']), '192.168.');
			IF ($pos === FALSE) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} ELSE {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
		} ELSE {
			$ip = $_SERVER["REMOTE_ADDR"];
		}

	# Set MTP (Mail Template Parameters) array
		$_MTP['to_name']	= $_cinfo['c_name'];
		$_MTP['to_email']	= $_cinfo['c_email'];
		$_MTP['from_name']	= $adata['mc_name'];
		$_MTP['from_email']	= $adata['mc_email'];
		$_MTP['subject']	= $adata['mc_subj'];
		$_MTP['message']	= $adata['mc_msg'];
		$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];
		$_MTP['sender_ip']	= $ip;
	
	# Load message template (processed)
		$mail['message']	= get_mail_template('email_contact_form', $_MTP);

	# Call basic email function (ret=0 on error)
		$_ret = do_mail_basic($mail);

	# Set flood control values in session
		$sdata['set_last_contact'] = 1;
		$_sret = do_session_update($sdata);

	# Check return
		IF ($_ret) {
			$_ret_msg  = $_LANG['_MAIL']['CS_FORM_MSG_02_L1'];
			$_ret_msg .= '<br>'.$_LANG['_MAIL']['CS_FORM_MSG_02_L2'];
		} ELSE {
			$_ret_msg  = $_LANG['_MAIL']['CS_FORM_MSG_03_L1'];
			$_ret_msg .= $_sp.$_LANG['_MAIL']['CS_FORM_MSG_03_L2'];
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_MAIL']['CS_FORM_RESULT_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_ret_msg.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr		= '&nbsp;'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do contact client form (contact site to user / client)
function do_contact_client_form($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Some HTML Strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="30%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_CCFG['_PKG_NAME_SHORT'].$_sp.$_LANG['_MAIL']['Contact_Client_Form'].$_sp.'('.$_LANG['_MAIL']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_MAIL']['CC_FORM_ERR_HDR1'].'<br>'.$_LANG['_MAIL']['CC_FORM_ERR_HDR2'].'<br>'.$_nl;

	 		IF ($aerr_entry['cc_cl_id']) 	{$err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['cc_mc_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR02']; $err_prv = 1;}
			IF ($aerr_entry['cc_subj']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR03']; $err_prv = 1;}
			IF ($aerr_entry['cc_msg']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR04']; $err_prv = 1;}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Formatting tweak for spacing
		IF ($aerr_entry['flag']) {$_cstr .= '<br><br>'.$_nl;}

		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<form action="mod.php" method="post" name="client">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="mail">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="client">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="5">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_To_Client'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_clients('cc_cl_id', $adata['cc_cl_id'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_From'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_mail_contacts('cc_mc_id', $adata['cc_mc_id']);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cc_subj" size="30" maxlength="50" value="'.htmlspecialchars($adata['cc_subj']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 120;} ELSE {$_cols = 80;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="cc_msg" COLS="'.$_cols.'" ROWS="15">'.$adata['cc_msg'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_MAIL']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_MAIL']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr 		= ''.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return / Echo Final Output
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do process contact site-to-user email form (build, set email))
function do_contact_client_email($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Check if we are sending to an additional email instead of the clients regular address
		$pos = strpos(strtolower($adata['cc_cl_id']), "alias");
		if ($pos !== false) {
			$pieces	= explode('|', $adata['cc_cl_id']);
			$_ccinfot	= get_contact_client_info_alias($pieces[1], 0);
			$_ccinfo	= $_ccinfot[1];
	    } ELSE {
		# Get client contact information array
			$_ccinfo	= get_contact_client_info($adata['cc_cl_id']);
		}

	# Get site contact information array
		$_mcinfo = get_contact_info($adata['cc_mc_id']);

	# Set eMail Parameters (pre-eval template, some used in template)
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   			$mail['recip']		= $_ccinfo['cl_email'];
			$mail['from']		= $_mcinfo['c_email'];
			$mail['cc']		= $_mcinfo['c_email'];
		} ELSE {
			$mail['recip']		= $_ccinfo['cl_name_first'].' '.$_ccinfo['cl_name_last'].' <'.$_ccinfo['cl_email'].'>';
			$mail['from']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
			$mail['cc']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
		}

		IF ($_CCFG['MAIL_USE_CUSTOM_SUBJECT']) {
			$mail['subject']	= $adata['cc_subj'];
		} ELSE {
			$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_MAIL']['CC_FORM_SUBJECT_PRE'];
		}

	# Set MTP (Mail Template Parameters) array
		$_MTP['to_name']	= $_ccinfo['cl_name_first'].' '.$_ccinfo['cl_name_last'];
		$_MTP['to_email']	= $_ccinfo['cl_email'];
		$_MTP['from_name']	= $_mcinfo['c_name'];
		$_MTP['from_email']	= $_mcinfo['c_email'];
		$_MTP['subject']	= $adata['cc_subj'];
		$_MTP['message']	= $adata['cc_msg'];
		$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];

	# Load message template (processed)
		$mail['message']	= get_mail_template('email_contact_client_form', $_MTP);

	# Call basic email function (ret=0 on error)
		$_ret = do_mail_basic($mail);

	# Check return
		IF ($_ret) {
			$_ret_msg = $_LANG['_MAIL']['CC_FORM_MSG_02_L1'];
			$_ret_msg .= '<br>'.$_LANG['_MAIL']['CC_FORM_MSG_02_L2'];
		} ELSE {
			$_ret_msg = $_LANG['_MAIL']['CC_FORM_MSG_03_L1'];
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_MAIL']['CC_FORM_RESULT_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_ret_msg.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr		= '&nbsp;'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do process contact site-to-all-users email form (build, set email))
function do_contact_client_email_all($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$DesiredGroup	= 0;
		$DesiredServer	= 0;
		$DesiredAlias	= 0;
		$DesiredClient	= 0;
		$_ret_msg		= '';

	# Check if we are sending to a group instead of all clients
		$pos = strpos(strtolower($adata['cc_cl_id']), 'group');
		IF ($pos !== false) {
			$pieces = explode('|', $adata['cc_cl_id']);
			$DesiredGroup = $pieces[1];
		}

	# Check if we are sending to a server instead of all clients
		$pos2 = strpos(strtolower($adata['cc_cl_id']), 'server');
		IF ($pos2 !== false) {
			$pieces = explode('|', $adata['cc_cl_id']);
			$DesiredServer = $pieces[1];
		}

	# Check if we are sending to an alias for a client
		$pos3 = strpos(strtolower($adata['cc_cl_id']), 'alias');
		IF ($pos3 !== false) {
			$pieces = explode('|', $adata['cc_cl_id']);
			$DesiredAlias = $pieces[1];
		}

	# Check if we are sending to all contacts for a client
		$pos3 = strpos(strtolower($adata['cc_cl_id']), 'contacts');
		IF ($pos3 !== false) {
			$pieces = explode('|', $adata['cc_cl_id']);
			$DesiredClient = $pieces[1];
		}

	# Get site contact information array
		$_mcinfo	= get_contact_info($adata['cc_mc_id']);

	# Set Query for select
		$query	= 'SELECT ';

		IF ($DesiredAlias) {
			$query .= $_DBCFG['clients_contacts'].'.contacts_email, ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_name_first, ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_name_last';
		} ELSEIF ($DesiredClient) {
			$query .= $_DBCFG['clients'].'.cl_email, ';
			$query .= $_DBCFG['clients'].'.cl_name_first, ';
			$query .= $_DBCFG['clients'].'.cl_name_last, ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_email, ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_name_first, ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_name_last';
		} ELSE {
			$query .= $_DBCFG['clients'].'.cl_email, ';
			$query .= $_DBCFG['clients'].'.cl_name_first, ';
			$query .= $_DBCFG['clients'].'.cl_name_last';
		}
		IF ($DesiredGroup) {$query .= ', '.$_DBCFG['clients'].'.cl_groups';}
		$query .= ' FROM ';

		IF ($DesiredServer) {
			$query .= $_DBCFG['clients'].', '.$_DBCFG['domains'];
			$query .= ' WHERE (('.$_DBCFG['domains'].'.dom_si_id='.$DesiredServer.' AND ';
			$query .= $_DBCFG['domains'].'.dom_cl_id='.$_DBCFG['clients'].'.cl_id)';

		} ELSEIF ($DesiredGroup) {
			$query .= $_DBCFG['clients'];
			$query .= ' WHERE (('.$_DBCFG['clients'].'.cl_groups <> 0)';

		} ELSEIF ($DesiredAlias) {
			$query .= $_DBCFG['clients_contacts'];
			$query .= ' WHERE ('.$_DBCFG['clients_contacts'].'.contacts_id='.$DesiredAlias.')';

		} ELSEIF ($DesiredClient) {
			$query .= $_DBCFG['clients'].', '.$_DBCFG['clients_contacts'];
			$query .= ' WHERE (';
			$query .= $_DBCFG['clients'].'.cl_id='.$DesiredClient.' OR (';
			$query .= $_DBCFG['clients'].'.cl_id='.$_DBCFG['clients_contacts'].'.contacts_cl_id AND ';
			$query .= $_DBCFG['clients_contacts'].'.contacts_cl_id='.$DesiredClient.')';
			$query .= ')';

		} ELSEIF ($adata['cc_cl_id'] == '-1') {
			$query .= $_DBCFG['clients'];
			$query .= " WHERE cl_status='active' OR cl_status='".$db_coin->db_sanitize_data($_CCFG['CL_STATUS'][1])."'";

		} ELSE {
			$query .= $_DBCFG['clients'];
			$query .= ' WHERE ('.$_DBCFG['clients'].'.cl_id='.$adata['cc_cl_id'].')';
		}

		IF ($DesiredServer || $DesiredGroup) {
			$query	.= ' AND ('.$_DBCFG['clients'].".cl_status='active'";
			$query	.= ' OR '.$_DBCFG['clients'].".cl_status='".$db_coin->db_sanitize_data($_CCFG['CL_STATUS'][1])."'))";
		}

	# Do select
		$result		= $db_coin->db_query_execute($query);
		$numrows		= $db_coin->db_query_numrows($result);
		$_emails_sent	= 0;
		$_emails_error	= 0;
		$_sento		= '';

	# Process query results
		while($row = $db_coin->db_fetch_array($result)) {

		# ONLY clients in specified group, OR All clients if group not specified
			IF (!$DesiredGroup || ($DesiredGroup && Check_User_Group($DesiredGroup, $row['cl_groups']))) {
			# Loop all clients and send email
			# Set eMail Parameters (pre-eval template, some used in template)
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   					$mail['recip']		= $row['contacts_email'] ? $row['contacts_email'] : $row['cl_email'];
					$mail['from']		= $_mcinfo['c_email'];
				} ELSE {
					$mail['recip']		= $row['contacts_name_first'] ? $row['contacts_name_first'] : $row['cl_name_first'];
					$mail['recip']		.= ' ';
					$mail['recip']		.= $row['contacts_name_last'] ? $row['contacts_name_last'] : $row['cl_name_last'];
					$mail['recip']		.= ' <';
					$mail['recip']		.= $row['contacts_email'] ? $row['contacts_email'] : $row['cl_email'];
					$mail['recip']		.= '>';
					$mail['from']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
				}
				#	$mail['cc']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';

				IF ($_CCFG['MAIL_USE_CUSTOM_SUBJECT']) {
					$mail['subject']	= $adata['cc_subj'];
				} ELSE {
					$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_MAIL']['CC_FORM_SUBJECT_PRE'];
				}

			# Set MTP (Mail Template Parameters) array
				$_MTP['to_name']	 = $row['contacts_name_first'] ? $row['contacts_name_first'] : $row['cl_name_first'];
				$_MTP['to_name']	.= ' ';
				$_MTP['to_name']	.= $row['contacts_name_last'] ? $row['contacts_name_last'] : $row['cl_name_last'];
				$_MTP['to_email']	 = $row['contacts_email'] ? $row['contacts_email'] : $row['cl_email'];
				$_MTP['from_name']	 = $_mcinfo['c_name'];
				$_MTP['from_email']	 = $_mcinfo['c_email'];
				$_MTP['subject']	 = $adata['cc_subj'];
				$_MTP['message']	 = $adata['cc_msg'];
				$_MTP['site']		 = $_CCFG['_PKG_NAME_SHORT'];

			# Load message template (processed)
				$mail['message']	= get_mail_template('email_contact_client_form', $_MTP);

			# Call basic email function (ret=1 on error)
				$_ret = do_mail_basic($mail);

			# Show what was sent
				$_sento .= htmlspecialchars($mail['recip']).'<br>';

			# Check return
				IF ($_ret) {$_emails_error++;} ELSE {$_emails_sent++;}
			}
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_MAIL']['CC_FORM_RESULT_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NL">'.$_nl;
		IF ($_emails_error) {
			$_cstr .= $_LANG['_MAIL']['CC_FORM_MSG_02_L1'];
			$_cstr .= '<br>'.$_LANG['_MAIL']['CC_FORM_MSG_02_L2'];
		} ELSE {
			$_cstr .= $_LANG['_MAIL']['CC_FORM_MSG_04_L1'];
		}
		$_cstr .= '<br>'.$_LANG['_MAIL']['total'].':'.$_sp.$_emails_sent.$_sp.$_LANG['_MAIL']['sent'];
		$_cstr .= '<br><br>'.$_sento;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr		= '&nbsp;'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do display entry (individual entry)
function do_display_entry_mail_archive($adata, $aret_flag=0) {
	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build common td start tag / strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="25%" valign="top">';
		$_td_str_colsp2	= '<td class="TP1SML_NJ" width="25%" colspan="2">';
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
				IF ($_SEC['_suser_id']) {
				# Dim some vars
					$good = 0;

				# Is it the client main address
					$clinfo = get_contact_client_info($_SEC['_suser_id']);
					$pos = strpos(strtolower($row['ma_fld_from']), strtolower($clinfo['cl_email']));
					IF ($pos !== false) {$good++;}
					$pos1 = strpos(strtolower($row['ma_fld_recip']), strtolower($clinfo['cl_email']));
					IF ($pos1 !== false) {$good++;}

				# Check all additional email addresses for a client
					IF (!$good) {
						$cl_emails = get_contact_client_info_alias($_SEC['_suser_id'],1);
						$x = sizeof($cl_emails);
						IF ($x) {
							FOR ($i=1; $i<=$x; $i++) {
								$pos = strpos(strtolower($row['ma_fld_from']), strtolower($cl_emails[$x]['cl_email']));
								IF ($pos !== false) {$good++; break;}
								$pos1 = strpos(strtolower($row['ma_fld_recip']), strtolower($cl_emails[$x]['cl_email']));
								IF ($pos1 !== false) {$good++; break;}
							}
						}
					}
				}

				IF ($_SEC['_sadmin_flg'] || $good) {

				# Build Title String, Content String, and Footer Menu String
					$_tstr  = '<table width="100%">'.$_nl;
					$_tstr .= '<tr class="BLK_IT_TITLE_TXT" valign="bottom">'.$_nl;
					$_tstr .= '<td class="TP3MED_BL">'.$_LANG['_MAIL']['l_View_Message'].$_sp.htmlspecialchars($row['ma_fld_recip'], ENT_QUOTES);
					IF ($_SEC['_sadmin_flg'] && $adata['_suser_id']) {
						$_tstr .= ' <a href="mod.php?mod=clients&mode=view&cl_id='.$adata['_suser_id'].'">'.$_TCFG['_IMG_BACK_TO_CLIENT_M'].'</a>'.$_nl;
					}
					$_tstr .= '</td>'.$_nl;
					$_tstr .= '</tr>'.$_nl;
					$_tstr .= '</table>'.$_nl;

					$_cstr  = '<table width="100%">'.$_nl;
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
					$_cstr .= $_td_str_right.htmlspecialchars($row['ma_fld_subject']).'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;

					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left.'<b>'.$_LANG['_MAIL']['l_Date_Sent'].$_sp.'</b></td>'.$_nl;
					$_cstr .= $_td_str_right.dt_make_datetime($row['ma_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;

					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_left_vtop.'<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'('.$_LANG['_MAIL']['output_below'].')</b></td>'.$_nl;
					$_cstr .= $_td_str_right.$_sp.'</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_colsp2.'<hr></td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '<tr valign="bottom">'.$_nl;
					$_cstr .= $_td_str_colsp2;
					IF ($_CCFG['EMAIL_AS_HTML'] || $_CCFG['INCOMING_EMAIL_AS_HTML']) {
						$_cstr .= nl2br(htmlspecialchars($row['ma_fld_message']));
					} ELSE {
						$_cstr .= '<pre>'.$row['ma_fld_message'].'</pre>';
					}
					$_cstr .= '</td>'.$_nl;
					$_cstr .= '</tr>'.$_nl;
					$_cstr .= '</table>'.$_nl;
					IF ($_CCFG['_IS_PRINT'] != 1) {
						IF ($_SEC['_sadmin_flg']) {$_mstr .= do_nav_link('admin.php', $_TCFG['_IMG_ADMIN_M'],$_TCFG['_IMG_ADMIN_M_MO'],'','');}
						$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=resend&obj=arch&ma_id='.$adata['ma_id'], $_TCFG['_IMG_EMAIL_M'],$_TCFG['_IMG_EMAIL_M_MO'],'','');
						IF ($_PERMS['AP16'] == 1 || $_PERMS['AP05'] == 1) {
							$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=delete&obj=arch&ma_id='.$adata['ma_id'], $_TCFG['_IMG_DELETE_M'],$_TCFG['_IMG_DELETE_M_MO'],'','');
						}
						$_mstr .= do_nav_link($_SERVER["PHP_SELF"].'?mod=mail&mode=search', $_TCFG['_IMG_SEARCH_M'],$_TCFG['_IMG_SEARCH_M_MO'],'','');
					} ELSE {
						$_mstr = '';
					}
				}

			# Call block it function
				$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
				$_out .= '<br>'.$_nl;
			}
		}

	# Return results
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do select list for: Suppliers Additional Emails
function do_select_list_suppliers_additional_emails($avalue, $aname) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_out = '';

	# Set Query for select.
		$query	= 'SELECT contacts_id, contacts_s_id, contacts_name_first, contacts_name_last, contacts_email FROM '.$_DBCFG['suppliers_contacts'];
		IF ($avalue) {$query .= ' WHERE contacts_s_id='.$avalue;}
		$query .= ' ORDER BY contacts_name_last ASC, contacts_name_first ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		IF ($numrows) {
		# Process query results to list individual clients
			while(list($contacts_id, $contacts_cl_id, $contacts_name_first, $contacts_name_last, $contacts_email) = $db_coin->db_fetch_row($result)) {
		    	$i++;
				$_out .= '<option value="'.'alias|'.$contacts_id.'">';
				$_out .= $_sp.$_sp.$_sp.$aname.' - '.$contacts_name_last.', '.$contacts_name_first.' ('.$_LANG['_BASE']['Email_Additional'].')</option>'.$_nl;
			}
			return $_out;
		} ELSE {
		    return '';
		}
}

# Do select list for: Suppliers
function do_select_list_suppliers($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT s_id, s_company, s_name_first, s_name_last FROM '.$_DBCFG['suppliers'];
		$query	.= " WHERE s_email <> ''";
		$query	.= ' ORDER BY s_company ASC, s_name_last ASC, s_name_first ASC';
		$result	 = $db_coin->db_query_execute($query);
		$numrows	 = $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BASE']['Please_Select'].'</option>'.$_nl;
		$_out .= '<option value="-1"';
		IF ($avalue == -1) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_MAIL']['All_Active_Suppliers'].'</option>'.$_nl;

	# Process query results to list individual suppliers
		while(list($s_id, $s_company, $s_name_first, $s_name_last) = $db_coin->db_fetch_row($result)) {
			$_more = '';

		# Add supplier info, indenting if additional emails present
			$_out .= '<option value="'.$s_id.'"';
			IF ($s_id == $avalue) {$_out .= ' selected';}
			$_out .= '>';
			$_out .= $s_company.' - '.$s_name_last.', '.$s_name_first.'</option>'.$_nl;

		# Grab any additional emails for this client, so they are all together in the list
			$_more = do_select_list_suppliers_additional_emails($s_id, $s_company);

		# Add "All" option, if necessary
			IF ($_more) {
				IF (substr_count($_more, '<option') > 1) {
					$_out .= '<option value="contacts|'.$cl_id.'">'.$_sp.$_sp.$_sp.$s_company.' - '.$s_name_last.', '.$s_name_first.' ('.$_LANG['_BASE']['All_Contacts'].')</option>'.$_nl;
				}
				$_out .= $_more;
			}
		}
		$_out .= '</select>'.$_nl;
		return $_out;
}

# Do contact supplier form (contact site to user / supplier)
function do_contact_supplier_form($adata, $aerr_entry, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Some HTML Strings (reduce text)
		$_td_str_left_vtop	= '<td class="TP1SML_NR" width="30%" valign="top">';
		$_td_str_left		= '<td class="TP1SML_NR" width="30%">';
		$_td_str_right		= '<td class="TP1SML_NL" width="70%">';

	# Build Title String, Content String, and Footer Menu String
		$_tstr .= $_CCFG['_PKG_NAME_SHORT'].$_sp.$_LANG['_MAIL']['Contact_Supplier_Form'].$_sp.'('.$_LANG['_MAIL']['all_fields_required'].')';

	# Do data entry error string check and build
		IF ($aerr_entry['flag']) {
		 	$err_str = $_LANG['_MAIL']['CC_FORM_ERR_HDR1'].'<br>'.$_LANG['_MAIL']['CC_FORM_ERR_HDR2'].'<br>'.$_nl;

	 		IF ($aerr_entry['cc_s_id']) 	{$err_str .= $_LANG['_MAIL']['CS_FORM_ERR_ERR01']; $err_prv = 1;}
			IF ($aerr_entry['cc_mc_id']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR02']; $err_prv = 1;}
			IF ($aerr_entry['cc_subj']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR03']; $err_prv = 1;}
			IF ($aerr_entry['cc_msg']) 	{IF ($err_prv) {$err_str .= ', ';} $err_str .= $_LANG['_MAIL']['CC_FORM_ERR_ERR04']; $err_prv = 1;}

	 		$_cstr .= '<p align="center"><b>'.$err_str.'</b>'.$_nl;
		}

	# Formatting tweak for spacing
		IF ($aerr_entry['flag']) {$_cstr .= '<br><br>'.$_nl;}

		$_cstr .= '<table width="100%" border="0" cellspacing="0" cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td align="center">'.$_nl;
		$_cstr .= '<form action="mod.php" method="post" name="supplier">'.$_nl;
		$_cstr .= '<input type="hidden" name="mod" value="mail">'.$_nl;
		$_cstr .= '<input type="hidden" name="mode" value="supplier">'.$_nl;
		$_cstr .= '<table width="100%" cellspacing="0" cellpadding="5">'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_To_Supplier'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_suppliers('cc_s_id', $adata['cc_s_id'], '1').$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_From'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_select_list_mail_contacts('cc_mc_id', $adata['cc_mc_id']);
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Subject'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= '<INPUT class="PSML_NL" TYPE=TEXT name="cc_subj" size="30" maxlength="50" value="'.htmlspecialchars($adata['cc_subj']).'">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left_vtop.$_nl;
		$_cstr .= '<b>'.$_LANG['_MAIL']['l_Message'].$_sp.'</b>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		IF ($_CCFG['WYSIWYG_OPEN']) {$_cols = 100;} ELSE {$_cols = 75;}
		$_cstr .= '<TEXTAREA class="PSML_NL" NAME="cc_msg" COLS="'.$_cols.'" ROWS="15">'.$adata['cc_msg'].'</TEXTAREA>'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= '</tr>'.$_nl;

		$_cstr .= '<tr>'.$_nl;
		$_cstr .= $_td_str_left.$_nl;
		$_cstr .= '<INPUT TYPE=hidden name="stage" value="1">'.$_nl;
		$_cstr .= '</td>'.$_nl;
		$_cstr .= $_td_str_right.$_nl;
		$_cstr .= do_input_button_class_sw('b_email', 'SUBMIT', $_LANG['_MAIL']['B_Send_Email'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= do_input_button_class_sw('b_reset', 'RESET', $_LANG['_MAIL']['B_Reset'], 'button_form_h', 'button_form', '1').$_nl;
		$_cstr .= '</td>'.$_nl;

		$_cstr .= '</tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr 		= ''.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Return / Echo Final Output
		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}

# Do process contact site-to-client email form (build, set email))
function do_contact_supplier_email($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Check if we are sending to an additional email instead of the clients regular address
		$pos = strpos(strtolower($adata['cc_s_id']), "alias");
		if ($pos !== false) {
			$pieces	= explode('|', $adata['cc_cl_id']);
			$_ccinfot	= get_contact_supplier_info_alias($pieces[1], 0);
			$_ccinfo	= $_ccinfot[1];
	    } ELSE {
		# Get supplier contact information array
			$_ccinfo	= get_contact_supplier_info($adata['cc_s_id']);
		}

	# Get site contact information array
		$_mcinfo = get_contact_info($adata['cc_mc_id']);

	# Set eMail Parameters (pre-eval template, some used in template)
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
   			$mail['recip']		= $_ccinfo['s_email'];
			$mail['from']		= $_mcinfo['c_email'];
			$mail['cc']		= $_mcinfo['c_email'];
		} ELSE {
			IF ($_ccinfo['s_name_first'] && $_ccinfo['s_name_last']) {
				$mail['recip'] = $_ccinfo['s_name_first'].' '.$_ccinfo['s_name_last'].' <'.$_ccinfo['s_email'].'>';
			} ELSE {
				$mail['recip'] = $_ccinfo['s_company'].' <'.$_ccinfo['s_email'].'>';
			}
			$mail['from']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
			$mail['cc']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
		}

		IF ($_CCFG['MAIL_USE_CUSTOM_SUBJECT']) {
			$mail['subject']	= $adata['cc_subj'];
		} ELSE {
			$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_MAIL']['CC_FORM_SUBJECT_PRE'];
		}

	# Set MTP (Mail Template Parameters) array
		IF ($_ccinfo['s_name_first'] && $_ccinfo['s_name_last']) {
			$_MTP['to_name']	= $_ccinfo['s_name_first'].' '.$_ccinfo['s_name_last'];
		} ELSE {
			$_MTP['to_name']	= $_ccinfo['s_company'];
		}
		$_MTP['to_email']	= $_ccinfo['s_email'];
		$_MTP['from_name']	= $_mcinfo['c_name'];
		$_MTP['from_email']	= $_mcinfo['c_email'];
		$_MTP['subject']	= $adata['cc_subj'];
		$_MTP['message']	= $adata['cc_msg'];
		$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];

	# Load message template (processed)
		$mail['message']	= get_mail_template('email_contact_supplier_form', $_MTP);

	# Call basic email function (ret=0 on error)
		$_ret = do_mail_basic($mail);

	# Check return
		IF ($_ret) {
			$_ret_msg = $_LANG['_MAIL']['CC_FORM_MSG_02_L1'];
			$_ret_msg .= '<br>'.$_LANG['_MAIL']['CC_FORM_MSG_02_L2'];
		} ELSE {
			$_ret_msg = $_LANG['_MAIL']['CC_FORM_MSG_03_L1'];
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_MAIL']['CC_FORM_RESULT_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5" width="100%">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NC">'.$_nl;
		$_cstr .= $_ret_msg.$_nl;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr		= '&nbsp;'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do process contact site-to-all-supplier email form (build, set email))
function do_contact_supplier_email_all($adata, $aret_flag=0) {
	# Dim Some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$DesiredGroup	= 0;
		$DesiredServer	= 0;
		$DesiredAlias	= 0;
		$DesiredClient	= 0;
		$_ret_msg		= '';

	# Check if we are sending to an alias for a supplier
		$pos1 = strpos(strtolower($adata['cc_s_id']), 'alias');
		IF ($pos1 !== false) {
			$pieces = explode('|', $adata['cc_s_id']);
			$DesiredAlias = $pieces[1];
		}

	# Check if we are sending to all contacts for a supplier
		$pos2 = strpos(strtolower($adata['cc_s_id']), 'contacts');
		IF ($pos2 !== false) {
			$pieces = explode('|', $adata['cc_s_id']);
			$DesiredSupplier = $pieces[1];
		}

	# Get site contact information array
		$_mcinfo	= get_contact_info($adata['cc_mc_id']);

	# Set Query for select
		$query	= 'SELECT ';

		IF ($DesiredAlias) {
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_email, ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_name_first, ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_name_last';
		} ELSEIF ($DesiredSupplier) {
			$query .= $_DBCFG['suppliers'].'.s_email, ';
			$query .= $_DBCFG['suppliers'].'.s_name_first, ';
			$query .= $_DBCFG['suppliers'].'.s_name_last, ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_email, ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_name_first, ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_name_last';
		} ELSE {
			$query .= $_DBCFG['suppliers'].'.s_email, ';
			$query .= $_DBCFG['suppliers'].'.s_name_first, ';
			$query .= $_DBCFG['suppliers'].'.s_name_last';
		}

		$query .= ' FROM ';

		IF ($DesiredAlias) {
			$query .= $_DBCFG['suppliers_contacts'];
			$query .= ' WHERE ('.$_DBCFG['suppliers_contacts'].'.contacts_id='.$DesiredAlias.')';

		} ELSEIF ($DesiredSupplier) {
			$query .= $_DBCFG['suppliers'].', '.$_DBCFG['suppliers_contacts'];
			$query .= ' WHERE (';
			$query .= $_DBCFG['suppliers'].'.s_id='.$DesiredSupplier.' OR (';
			$query .= $_DBCFG['suppliers'].'.s_id='.$_DBCFG['suppliers_contacts'].'.contacts_s_id AND ';
			$query .= $_DBCFG['suppliers_contacts'].'.contacts_s_id='.$DesiredSupplier.')';
			$query .= ')';

		} ELSEIF ($adata['cc_s_id'] == '-1') {
			$query .= $_DBCFG['suppliers'];
			$query .= " WHERE s_status='active' OR s_status='".$db_coin->db_sanitize_data($_CCFG['S_STATUS'][1])."'";

		} ELSE {
			$query .= $_DBCFG['suppliers'];
			$query .= ' WHERE ('.$_DBCFG['suppliers'].'.s_id='.$adata['cc_s_id'].')';
		}

	# Do select
		$result		= $db_coin->db_query_execute($query);
		$numrows		= $db_coin->db_query_numrows($result);
		$_emails_sent	= 0;
		$_emails_error	= 0;
		$_sento		= '';

	# Process query results
		while($row = $db_coin->db_fetch_array($result)) {

		# Only send email if an address exists
			IF ($row['contacts_email'] || $row['s_email']) {

			# Loop all suppliers and send email
			# Set eMail Parameters (pre-eval template, some used in template)
				IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
					$mail['recip']		= $row['contacts_email'] ? $row['contacts_email'] : $row['s_email'];
					$mail['from']		= $_mcinfo['c_email'];
				} ELSE {
					$mail['recip']		= $row['contacts_name_first'] ? $row['contacts_name_first'] : $row['s_name_first'];
					$mail['recip']		.= ' ';
					$mail['recip']		.= $row['contacts_name_last'] ? $row['contacts_name_last'] : $row['s_name_last'];
					$mail['recip']		.= ' <';
					$mail['recip']		.= $row['contacts_email'] ? $row['contacts_email'] : $row['s_email'];
					$mail['recip']		.= '>';
					$mail['from']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';
				}
				#	$mail['cc']		= $_mcinfo['c_name'].' <'.$_mcinfo['c_email'].'>';

				IF ($_CCFG['MAIL_USE_CUSTOM_SUBJECT']) {
					$mail['subject']	= $adata['cc_subj'];
				} ELSE {
					$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_MAIL']['CC_FORM_SUBJECT_PRE'];
				}

			# Set MTP (Mail Template Parameters) array
				$_MTP['to_name']	 = $row['contacts_name_first'] ? $row['contacts_name_first'] : $row['s_name_first'];
				$_MTP['to_name']	.= ' ';
				$_MTP['to_name']	.= $row['contacts_name_last'] ? $row['contacts_name_last'] : $row['s_name_last'];
				$_MTP['to_email']	 = $row['contacts_email'] ? $row['contacts_email'] : $row['s_email'];
				$_MTP['from_name']	 = $_mcinfo['c_name'];
				$_MTP['from_email']	 = $_mcinfo['c_email'];
				$_MTP['subject']	 = $adata['cc_subj'];
				$_MTP['message']	 = $adata['cc_msg'];
				$_MTP['site']		 = $_CCFG['_PKG_NAME_SHORT'];

			# Load message template (processed)
				$mail['message']	= get_mail_template('email_contact_supplier_form', $_MTP);

			# Call basic email function (ret=1 on error)
				$_ret = do_mail_basic($mail);

			# Show what was sent
				$_sento .= htmlspecialchars($mail['recip']).'<br>';

			# Check return
				IF ($_ret) {$_emails_error++;} ELSE {$_emails_sent++;}
			}
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_MAIL']['CC_FORM_RESULT_TITLE'];

		$_cstr .= '<center>'.$_nl;
		$_cstr .= '<table cellpadding="5">'.$_nl;
		$_cstr .= '<tr><td class="TP5MED_NL">'.$_nl;
		IF ($_emails_error) {
			$_cstr .= $_LANG['_MAIL']['CC_FORM_MSG_02_L1'];
			$_cstr .= '<br>'.$_LANG['_MAIL']['CC_FORM_MSG_02_L2'];
		} ELSE {
			$_cstr .= $_LANG['_MAIL']['CC_FORM_MSG_04_L1'];
		}
		$_cstr .= '<br>'.$_LANG['_MAIL']['total'].':'.$_sp.$_emails_sent.$_sp.$_LANG['_MAIL']['sent'];
		$_cstr .= '<br><br>'.$_sento;
		$_cstr .= '</td></tr>'.$_nl;
		$_cstr .= '</table>'.$_nl;
		$_cstr .= '</center>'.$_nl;

		$_mstr_flag	= 0;
		$_mstr		= '&nbsp;'.$_nl;

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1');
		$_out .= '<br>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}
/**************************************************************
 * End Module Functions
**************************************************************/
?>