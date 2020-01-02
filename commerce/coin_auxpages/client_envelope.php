<?php
/**
 * Auxpage: Prepare Client-Addressed Envelope For Printing
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Clients
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @usage Call this file directly. When the address is displayed onscreen,
 *        print using the browser's "print" function.
 */


# Include session file (loads core)
	require_once('../coin_includes/session_set.php');

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_client_envelope.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_client_envelope_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_client_envelope_override.php');
	}

# Initialize some output variables
	$_tstr	 = $_LANG['Envelopes']['Form_Title'];
	$_mstr	 = do_nav_link($_SERVER['HTTP_REFERER'], $_TCFG['_IMG_RETURN_M'], $_TCFG['_IMG_RETURN_M_MO'], '', '');
	$_mstr	.= do_nav_link($_SERVER['PHP_SELF'], $_TCFG['_IMG_ADMIN_M'], $_TCFG['_IMG_ADMIN_M_MO'], '', '');

# Call Load Component parms
	$compdata = do_load_comp_data('envelope', '');

# Get security vars
	$_SEC = get_security_flags();




# If user is not a logged-in admin, display error message
	IF (!$_SEC['_sadmin_flg']) {
		$_cstr = $_LANG['_BASE']['Permission_Msg'];
		echo do_page_open($compdata, 1).$_nl;
		echo do_mod_block_it($_tstr, $_cstr, 0, $_mstr, 1);
		echo do_page_close($compdata, 1).$_nl;




# Else if user IS logged-in but this is the first pass, show the "select client" form
	} ELSEIF (!$_GPV['cl_id']) {
		$_cstr  = '<form method="post">'.$_nl;
		$_cstr .= '<fieldset>'.$_nl;
		$_cstr .= '<legend>'.$_LANG['Envelopes']['Form_Legend'].'</legend><br>'.$_nl;
		$_cstr .= '<p align="left">'.$_LANG['Envelopes']['Form_Instructions'].'</p>';
		$_cstr .= '<label for "cl_id">'.$_LANG['Envelopes']['Form_CLID'].'</label>'.$_nl;
		$_cstr .= '<input type="text" name="cl_id" value="'.$_GPV['cl_id'].'" length="8"><br>'.$_nl;
		$_cstr .= '<input type="submit" value="'.$_LANG['Envelopes']['Form_Submit'].'">'.$_nl;
		$_cstr .= '</fieldset>'.$_nl;
		$_cstr .= '</form>'.$_nl;
		echo do_page_open($compdata, 1).$_nl;
		echo do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
		echo do_page_close($compdata, 1).$_nl;




# Else if user IS logged-in but this is NOT the first pass, build the envelope
	} ELSEIF ($_GPV['cl_id']) {

	# Do select and return check
		$query	 = 'SELECT * FROM '.$_DBCFG['clients'];
		$query	.= ' WHERE cl_id='.$db_coin->db_sanitize_data($_GPV['cl_id']);
		$result	 = $db_coin->db_query_execute($query);

	# IF a record was found, build the output
		IF ($db_coin->db_query_numrows($result)) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$cl_id		= $row['cl_id'];
				$cl_join_ts	= $row['cl_join_ts'];
				$cl_status	= $row['cl_status'];
				$cl_company	= $row['cl_company'];
				$cl_name_first	= $row['cl_name_first'];
				$cl_name_last	= $row['cl_name_last'];
				$cl_addr_01	= $row['cl_addr_01'];
				$cl_addr_02	= $row['cl_addr_02'];
				$cl_city		= $row['cl_city'];
				$cl_state_prov	= $row['cl_state_prov'];
				$cl_country	= $row['cl_country'];
				$cl_zip_code	= $row['cl_zip_code'];
				$cl_phone		= $row['cl_phone'];
				$cl_email		= $row['cl_email'];
				$cl_user_name	= $row['cl_user_name'];
				$cl_notes		= $row['cl_notes'];
				$cl_groups	= $row['cl_groups'];
			}

		# Grab the template
			$_out = $_ENVELOPE_TEMPLATE;

		# Replace Sender info
			$_out = str_replace('%SENDER_COMPANY%', $_UVAR['CO_INFO_01_NAME'], $_out);
			$_out = str_replace('%SENDER_STREET_1%', $_UVAR['CO_INFO_02_ADDR_01'], $_out);
			$_out = str_replace('%SENDER_STREET_2%', $_UVAR['CO_INFO_03_ADDR_02'], $_out);
			$_out = str_replace('%SENDER_CITY%', $_UVAR['CO_INFO_04_CITY'], $_out);
			$_out = str_replace('%SENDER_STATE%', $_UVAR['CO_INFO_05_STATE_PROV'], $_out);
			$_out = str_replace('%SENDER_PCODE%', $_UVAR['CO_INFO_06_POSTAL_CODE'], $_out);
			$_out = str_replace('%SENDER_COUNTRY%', $_UVAR['CO_INFO_07_COUNTRY'], $_out);
			$_out = str_replace('%SENDER_PHONE%', $_UVAR['CO_INFO_08_PHONE'], $_out);
			$_out = str_replace('%SENDER_FAX%', $_UVAR['CO_INFO_08_FAX'], $_out);
			$_out = str_replace('%SENDER_TOLL_FREE%', $_UVAR['CO_INFO_11_TOLL_FREE'], $_out);
			$_out = str_replace('%SENDER_TAXNO%', $_UVAR['CO_INFO_10_TAXNO'], $_out);
			$_out = str_replace('%SENDER_TAGLINE%', $_UVAR['CO_INFO_12_TAGLINE'], $_out);

		# Replace client info
			$_out = str_replace('%CLIENT_COMPANY%', $cl_company, $_out);
			$_out = str_replace('%CLIENT_NAME_FIRST%', $cl_name_first, $_out);
			$_out = str_replace('%CLIENT_NAME_LAST%', $cl_name_last, $_out);
			$_out = str_replace('%CLIENT_STREET_1%', $cl_addr_01, $_out);
			$_out = str_replace('%CLIENT_STREET_2%', $cl_addr_02, $_out);
			$_out = str_replace('%CLIENT_CITY%', $cl_city, $_out);
			$_out = str_replace('%CLIENT_STATE%', $cl_state_prov, $_out);
			$_out = str_replace('%CLIENT_PCODE%', $cl_zip_code, $_out);
			$_out = str_replace('%CLIENT_COUNTRY%', $cl_country, $_out);
			$_out = str_replace('%CLIENT_PHONE%', $cl_phone, $_out);

		# Remove blank lines on sender address
			$_out = str_replace("\r\n\r\n", "\r\n", $_out);
			$_out = str_replace("\n\n", "\n", $_out);
			$_out = str_replace("\r\r", "\r", $_out);

		# Remove blank lines on client address.  You WILL need to adjust the
		# number of tabs checked for IF you change the number of tabs in the
		# template. This is checking for five tabs, the default in the template
			$_out = str_replace("\r\n\t\t\t\t\t\r\n", "\r\n", $_out);
			$_out = str_replace("\n\t\t\t\t\t\n", "\n", $_out);
			$_out = str_replace("\r\t\t\t\t\t\r", "\r", $_out);

		# Display the output, ready for printing. Set top/left marginss to
		# zero for best results, with NO headers/footer
			echo '<html>'.$_nl;
			echo '<head>'.$_nl;
			echo '<title>'.$_LANG['Envelopes']['Browser_Title'].'</title>'.$_nl;
			echo '</head>'.$_nl;
			echo '<body>'.$_nl;
			echo '<pre>'.$_out.'</pre>';
			echo '</body>'.$_nl;
			echo '</html>'.$_nl;


	# And if NO record was found, display an error
		} ELSE {
			$_cstr = $_LANG['Envelopes']['Error_No_Such_Client'];
			echo do_page_open($compdata, 1);
			echo do_mod_block_it($_tstr, $_cstr, 1, $_mstr, 1);
			echo do_page_close($compdata, 1);
		}
	}

?>