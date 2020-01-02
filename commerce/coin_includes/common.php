<?php
/**
 * Loader: Common Functions
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Common
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('common.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit;
	}



/**
 * Break a long string into "bite-sized" pieces :)
 * @param string $astring string to be checked/broken up
 * @param int $alimit How long should the maximum word-length be before we chop it up? We made it long so we do not chop URLs
 * @param int $achop How many characters long should each new "word" be?
 * @return string Original string or "chopped up" string
 */
function truncate_string($astring, $alimit=100, $achop=75) {
	$astring	= str_replace("\r\n", '<br>', $astring);
	$astring	= str_replace("\n", '<br>', $astring);
	$text	= explode(' ', $astring);
	$post	= '';

	while(list($key, $value) = each($text)) {
		$new		= '';
		$length	= strlen($value);
		IF ($length >= $alimit) {
			for($i=0; $i<=$length; $i+=$achop) {$new .= substr($value, $i, $achop).' ';}
			$post .= $new;
		} ELSEIF ($length <= $alimit) {
			$post .= $value;
		}
		$post .= ' ';
	}
	return($post);
}



/**************************************************************
 *              Start Common Module Functions
**************************************************************/
# Common function to return permissions insufficient message
function do_no_permission_message() {
	# Dim some Vars
		global $_TCFG, $_LANG, $_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr	= $_LANG['_BASE']['Permission_Title'];
		$_cstr	= '<center><br>'.$_LANG['_BASE']['Permission_Msg'].'<br><br></center>';
		$_mstr	= do_nav_link(getenv("HTTP_REFERER"), $_TCFG['_IMG_RETURN_M'],$_TCFG['_IMG_RETURN_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}

# Common function for selecting billing cycle
function do_select_list_billing_cycle($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_LANG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['INVC_BILL_CYCLE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INVC_BILL_CYCLE'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Common function to return contact flood control message
function do_no_contact_flood_message() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_nl;

	# Build Title String, Content String, and Footer Menu String
		$_tstr	= $_LANG['_BASE']['Flood_Contact_Title'];
		$_cstr	= '<center><br>'.$_LANG['_BASE']['Flood_Contact_Message'].'<br><br></center>';
		$_mstr	= do_nav_link (getenv("HTTP_REFERER"), $_TCFG['_IMG_RETURN_M'],$_TCFG['_IMG_RETURN_M_MO'],'','');

	# Call block it function
		$_out .= do_mod_block_it ($_tstr, $_cstr, '0', $_mstr, '1');
		$_out .= '<br>'.$_nl;

	# Echo final output
		echo $_out;
}


/**
 * Format a number
 * @param real $avalue Numeric value to be formatted
 * @param int $acp Whether or not to prepend the currency prefix
 * @param int $acs Whether or not to append the currency suffix
 * @param int $acd Number of digits to display after the period
 * @return string Formatted number
 */
function do_currency_format($avalue, $acp=0, $acs=0, $acd=2) {
	# Dim some Vars
		global $_CCFG;

	# Create prefix and suffix
		IF ($acp) {$_prefix = $_CCFG['_CURRENCY_PREFIX'];} ELSE {$_prefix = '';}
		IF ($acs) {$_suffix = ' '.$_CCFG['_CURRENCY_SUFFIX'];} ELSE {$_suffix = '';}

	# Example number 1234.56
	# 	Value = 1		Output: 1234
	# 	Value = 2		Output: 1234.56
	# 	Value = 3		Output: 1,234.56
	# 	Value = 4		Output: 1 234,56
	# 	Value = 5		Output: 1.234,56

	switch($_CCFG['_NUMBER_FORMAT_ID']) {
		case 1:
			return $_prefix.number_format($avalue, 0, '', '').$_suffix;
			break;
		case 2:
			return $_prefix.number_format($avalue, $acd, '.', '').$_suffix;
			break;
		case 3:
			return $_prefix.number_format($avalue, $acd, '.', ',').$_suffix;
			break;
		case 4:
			return $_prefix.number_format($avalue, $acd, ',', ' ').$_suffix;
			break;
		case 5:
			return $_prefix.number_format($avalue, $acd, ',', '.').$_suffix;
			break;
		default:
			return $_prefix.number_format($avalue, $acd, '.', ',').$_suffix;
			break;
	}
}


# Common function to format decimal numbers (percent) using php
# number_format function
# @ http://www.php.net/manual/en/function.number-format.php
function do_decimal_format($avalue) {
	# Example number 1234.56
	# 	Value = 1		Output: 1234
	# 	Value = 2		Output: 1234.56
	# 	Value = 3		Output: 1,234.56
	# 	Value = 4		Output: 1 234,56
	# 	Value = 5		Output: 1.234,56

	$_format = 2;
	switch($_format) {
		case 1:
			return number_format($avalue, 0, '', '');
			break;
		case 2:
			return number_format($avalue, 2, '.', '');
			break;
		case 3:
			return number_format($avalue, 2, '.', ',');
			break;
		case 4:
			return number_format($avalue, 2, ',', ' ');
			break;
		case 5:
			return number_format($avalue, 2, ',', '.');
			break;
		default:
			return number_format($avalue, 2, '.', ',');
			break;
	}
}


# Do return string from value for: Title Row with search dropdown
function do_tstr_search_list($atitle) {
	# Dim some Vars
		global $_CCFG, $_LANG, $_nl;

	# Search form
		$_sform .= '<FORM METHOD="POST" ACTION="'.$_SERVER["PHP_SELF"].'">'.$_nl;
		$_sform .= '<input type="hidden" name="mod" value="cc">'.$_nl;
		$_sform .= '<input type="hidden" name="mode" value="search">'.$_nl;
		$_sform .= '<select class="select_form" name="sw" size="1" value="Search" onchange="submit();">'.$_nl;
		$_sform .= '<option value="" selected>'.$_LANG['_BASE']['Search'].'</option>'.$_nl;
		$_sform .= '<option value="clients">'.$_LANG['_BASE']['Clients'].'</option>'.$_nl;
		IF ($_CCFG['DOMAINS_ENABLE'])		{$_sform .= '<option value="domains">'.$_LANG['_BASE']['Domains'].'</option>'.$_nl;}
		IF ($_CCFG['HELPDESK_ENABLE'])	{$_sform .= '<option value="helpdesk">'.$_LANG['_BASE']['HelpDesk'].'</option>'.$_nl;}
		IF ($_CCFG['INVOICES_ENABLE'])	{$_sform .= '<option value="invoices">'.$_LANG['_BASE']['Invoices'].'</option>'.$_nl;}
		IF ($_CCFG['ORDERS_ENABLE'])		{$_sform .= '<option value="orders">'.$_LANG['_BASE']['Orders'].'</option>'.$_nl;}
		IF ($_CCFG['INVOICES_ENABLE'])	{$_sform .= '<option value="trans">'.$_LANG['_BASE']['Transactions'].'</option>'.$_nl;}
		IF ($_CCFG['BILLS_ENABLE'])		{
			$_sform .= '<option value="bills">'.$_LANG['_BASE']['Bills'].'</option>'.$_nl;
			$_sform .= '<option value="billtrans">'.$_LANG['_BASE']['Bill_Transactions'].'</option>'.$_nl;
		}
		$_sform .= '</select>'.$_nl;
		$_sform .= '</FORM>'.$_nl;

		$_tstr 	 = '<table width="100%" cellpadding="0" cellspacing="0"><tr class="BLK_IT_TITLE_TXT">';
		$_tstr 	.= '<td class="TP0MED_BL" valign="top">'.$_nl.$atitle.$_nl.'</td>'.$_nl;
		IF (!$_CCFG['_IS_PRINT']) {
			$_tstr	.= '<td class="TP0MED_BR" valign="top">'.$_nl.$_sform.$_nl.'</td>'.$_nl;
		}
		$_tstr 	.= '</tr></table>';

	# Return form output
		return $_tstr;
}


# Create date edit list (year, month, day)
function do_date_edit_list($aname, $avalue, $aret_flag=0) {
	# Requires $avalue to be unix timestamp format datetime
	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_nl, $_sp;

	# Get datetime array from passed timestamp
		$_dt = dt_make_datetime_array($avalue);

	# Build list array for year, month, and day
	# Year (list now minus 10 and plus 10)
		$_dt_now	= getdate(dt_get_uts());
		$_ymin	= $_dt_now['year']-10; $_ymax = $_dt_now['year']+10;
		for ($y = $_ymin; $y <= $_ymax; $y++) {$i++; $_list_year[$i] = $y;}

	# Build list for year:
		$_out .= '<select class="select_form" name="'.$aname.'_year'.'" size="1" value="'.$_dt['year'].'">'.$_nl;
		for ($i = 0; $i <= count($_list_year); $i++) {
			$_out .= '<option value="'.$_list_year[$i].'"';
			IF ($_list_year[$i] == $_dt['year']) {$_out .= ' selected';}
			$_out .= '>'.$_list_year[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		#	$_out .= $_sp.$_nl;

	# Month
		$_list_month['num'][1] = '01'; $_list_month['text'][1] = $_LANG['_BASE']['DS_Jan'];
		$_list_month['num'][2] = '02'; $_list_month['text'][2] = $_LANG['_BASE']['DS_Feb'];
		$_list_month['num'][3] = '03'; $_list_month['text'][3] = $_LANG['_BASE']['DS_Mar'];
		$_list_month['num'][4] = '04'; $_list_month['text'][4] = $_LANG['_BASE']['DS_Apr'];
		$_list_month['num'][5] = '05'; $_list_month['text'][5] = $_LANG['_BASE']['DS_May'];
		$_list_month['num'][6] = '06'; $_list_month['text'][6] = $_LANG['_BASE']['DS_Jun'];
		$_list_month['num'][7] = '07'; $_list_month['text'][7] = $_LANG['_BASE']['DS_Jul'];
		$_list_month['num'][8] = '08'; $_list_month['text'][8] = $_LANG['_BASE']['DS_Aug'];
		$_list_month['num'][9] = '09'; $_list_month['text'][9] = $_LANG['_BASE']['DS_Sep'];
		$_list_month['num'][10] = '10'; $_list_month['text'][10] = $_LANG['_BASE']['DS_Oct'];
		$_list_month['num'][11] = '11'; $_list_month['text'][11] = $_LANG['_BASE']['DS_Nov'];
		$_list_month['num'][12] = '12'; $_list_month['text'][12] = $_LANG['_BASE']['DS_Dec'];

	# Build list for month:
		$_out .= '<select class="select_form" name="'.$aname.'_month'.'" size="1" value="'.$_dt['month'].'">'.$_nl;
		for ($i = 0; $i <= count($_list_month['num']); $i++) {
			$_out .= '<option value="'.$_list_month['num'][$i].'"';
			IF ($_list_month['num'][$i] == $_dt['month']) {$_out .= ' selected';}
			$_out .= '>'.$_list_month['text'][$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		#	$_out .= $_sp.$_nl;

	# Day
		for ($i = 1; $i <= 31; $i++) {
			IF ($i < 10) {$_list_day[$i] = '0'.$i;} ELSE {$_list_day[$i] = $i;}
		}

	# Build list for day:
		$_out .= '<select class="select_form" name="'.$aname.'_day'.'" size="1" value="'.$_dt['day'].'">'.$_nl;
		for ($i = 0; $i <= count($_list_day); $i++) {
			$_out .= '<option value="'.$_list_day[$i].'"';
			IF ($_list_day[$i] == $_dt['day']) {$_out .= ' selected';}
			$_out .= '>'.$_list_day[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		$_out .= $_sp.'(year-month-day)'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Create datetime edit list (year, month, day, 24hr, min, sec)
function do_datetime_edit_list($aname, $avalue, $aret_flag=0) {
	# Requires $avalue to be unix timestamp format datetime
	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_nl, $_sp;

	# Get datetime array from passed timestamp
		$_dt = dt_make_datetime_array($avalue);

	# Build list array for year, month, and day
	# Year (list now minus 10 and plus 10)
		$_dt_now	= getdate(dt_get_uts());
		$_ymin	= $_dt_now['year']-10; $_ymax = $_dt_now['year']+10;
		for ($y = $_ymin; $y <= $_ymax; $y++) {$i++; $_list_year[$i] = $y;}

	# Build list for year:
		$_out .= '<select class="select_form" name="'.$aname.'_year'.'" size="1" value="'.$_dt['year'].'">'.$_nl;
		for ($i = 0; $i <= count($_list_year); $i++) {
			$_out .= '<option value="'.$_list_year[$i].'"';
			IF ($_list_year[$i] == $_dt['year']) {$_out .= ' selected';}
			$_out .= '>'.$_list_year[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		#	$_out .= $_sp.$_nl;

	# Month
		$_list_month['num'][1] = '01'; $_list_month['text'][1] = $_LANG['_BASE']['DS_Jan'];
		$_list_month['num'][2] = '02'; $_list_month['text'][2] = $_LANG['_BASE']['DS_Feb'];
		$_list_month['num'][3] = '03'; $_list_month['text'][3] = $_LANG['_BASE']['DS_Mar'];
		$_list_month['num'][4] = '04'; $_list_month['text'][4] = $_LANG['_BASE']['DS_Apr'];
		$_list_month['num'][5] = '05'; $_list_month['text'][5] = $_LANG['_BASE']['DS_May'];
		$_list_month['num'][6] = '06'; $_list_month['text'][6] = $_LANG['_BASE']['DS_Jun'];
		$_list_month['num'][7] = '07'; $_list_month['text'][7] = $_LANG['_BASE']['DS_Jul'];
		$_list_month['num'][8] = '08'; $_list_month['text'][8] = $_LANG['_BASE']['DS_Aug'];
		$_list_month['num'][9] = '09'; $_list_month['text'][9] = $_LANG['_BASE']['DS_Sep'];
		$_list_month['num'][10] = '10'; $_list_month['text'][10] = $_LANG['_BASE']['DS_Oct'];
		$_list_month['num'][11] = '11'; $_list_month['text'][11] = $_LANG['_BASE']['DS_Nov'];
		$_list_month['num'][12] = '12'; $_list_month['text'][12] = $_LANG['_BASE']['DS_Dec'];

	# Build list for month:
		$_out .= '<select class="select_form" name="'.$aname.'_month'.'" size="1" value="'.$_dt['month'].'">'.$_nl;
		FOR ($i = 0; $i <= count($_list_month['num']); $i++) {
			$_out .= '<option value="'.$_list_month['num'][$i].'"';
			IF ( $_list_month['num'][$i] == $_dt['month'] ) { $_out .= ' selected'; }
			$_out .= '>'.$_list_month[text][$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		#	$_out .= $_sp.$_nl;

	# Day
		FOR ($i = 1; $i <= 31; $i++) {
			IF ($i < 10) {$_list_day[$i] = '0'.$i;} ELSE {$_list_day[$i] = $i;}
		}

	# Build list for day:
		$_out .= '<select class="select_form" name="'.$aname.'_day'.'" size="1" value="'.$_dt['day'].'">'.$_nl;
		FOR ($i = 0; $i <= count($_list_day); $i++) {
			$_out .= '<option value="'.$_list_day[$i].'"';
			IF ( $_list_day[$i] == $_dt['day'] ) { $_out .= ' selected'; }
			$_out .= '>'.$_list_day[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		$_out .= $_sp.'('.$_LANG['_BASE']['DS_Format_Date'].')<p>'.$_nl;

	# 24-Hour
		FOR ($i = 0; $i <= 23; $i++) {
			IF ($i < 10) {$_list_hour[$i] = '0'.$i;} ELSE {$_list_hour[$i] = $i;}
		}

	# Build list for hour:
		$_out .= '<select class="select_form" name="'.$aname.'_hour'.'" size="1" value="'.$_dt['hour'].'">'.$_nl;
		FOR ($i = 0; $i < count($_list_hour); $i++) {
			$_out .= '<option value="'.$_list_hour[$i].'"';
			IF ( $_list_hour[$i] == $_dt['hour'] ) { $_out .= ' selected'; }
			$_out .= '>'.$_list_hour[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		$_out .= '<b>:</b>'.$_nl;

	# Minute
		FOR ($i = 0; $i <= 59; $i++) {
			IF ($i < 10) {$_list_minute[$i] = '0'.$i;} ELSE {$_list_minute[$i] = $i;}
		}

	# Build list for minute:
		$_out .= '<select class="select_form" name="'.$aname.'_minute'.'" size="1" value="'.$_dt['minute'].'">'.$_nl;
		FOR ($i = 0; $i < count($_list_minute); $i++) {
			$_out .= '<option value="'.$_list_minute[$i].'"';
			IF ($_list_minute[$i] == $_dt['minute']) {$_out .= ' selected';}
			$_out .= '>'.$_list_minute[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		$_out .= '<b>:</b>'.$_nl;

	# Second
		FOR ($i = 0; $i <= 59; $i++) {
			IF ($i < 10) {$_list_second[$i] = '0'.$i;} ELSE {$_list_second[$i] = $i;}
		}

	# Build list for second:
		$_out .= '<select class="select_form" name="'.$aname.'_second'.'" size="1" value="'.$_dt['second'].'">'.$_nl;
		FOR ($i = 0; $i < count($_list_second); $i++) {
			$_out .= '<option value="'.$_list_second[$i].'"';
			IF ($_list_second[$i] == $_dt['second']) {$_out .= ' selected';}
			$_out .= '>'.$_list_second[$i];
			$_out .= '</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;
		$_out .= $_sp.'('.$_LANG['_BASE']['DS_Format_Time'].')'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do return string from value for: No or Yes Options
function do_valtostr_no_yes($avalue) {
	# Dim some Vars
		global $_LANG;

	# Build form output
		IF ($avalue == 1) {
			return $_LANG['_BASE']['Yes'];
		} ELSE {
			return $_LANG['_BASE']['No'];
		}
}


# Do list select field for: No or Yes Options
function do_select_list_no_yes($aname, $avalue, $aret_flag=0) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_nl, $_sp;

	# Build form output
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['No'].'</option>'.$_nl;
		$_out .= '<option value="1"';
		IF ($avalue == 1) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['Yes'].'</option>'.$_nl;
		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do return string from value for: Off or On Options
function do_valtostr_off_on($avalue) {
	# Dim some Vars
		global $_LANG;

	# Build form output
		IF ($avalue == 1) {
			return $_LANG['_BASE']['On'];
		} ELSE {
			return $_LANG['_BASE']['Off'];
		}
}


# Do list select field for: Off or On Options
function do_select_list_off_on($aname, $avalue, $aret_flag=0) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_LANG, $_nl, $_sp;

	# Build form output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['Off'].'</option>'.$_nl;
		$_out .= '<option value="1"';
		IF ($avalue == 1) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_BASE']['On'].'</option>'.$_nl;
		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Get site mail contacts info (core??)
function get_contact_info($amc_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select and execute
		$query	 = 'SELECT mc_id, mc_name, mc_email FROM '.$_DBCFG['mail_contacts'];
		$query	.= ' WHERE mc_id='.$amc_id.' ORDER BY mc_id ASC';
		$result	 = $db_coin->db_query_execute($query);

	# Get value and set return
		while(list($mc_id, $mc_name, $mc_email) = $db_coin->db_fetch_row($result)) {
			$_cinfo['c_id']	= $mc_id;
			$_cinfo['c_name']	= $mc_name;
			$_cinfo['c_email']	= $mc_email;
		}
		return $_cinfo;
}


function get_contact_email_by_name($amc_name) {
	# Dim some Vars
		global $_DBCFG, $db_coin;

	# Set Query for select and execute
		$query	 = 'SELECT mc_email FROM '.$_DBCFG['mail_contacts'];
		$query	.= " WHERE mc_name LIKE '%".$amc_name."%' ORDER BY mc_id ASC";
		$result	 = $db_coin->db_query_execute($query);

	# Get value and return
		while(list($mc_email) = $db_coin->db_fetch_row($result)) {$_c_email = $mc_email;}
		return $_c_email;
}

# Get admin contact info
function get_contact_admin_info($aca_admin_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select and execute
		$query	 = 'SELECT admin_id, admin_name_first, admin_name_last, admin_user_name, admin_email FROM '.$_DBCFG['admins'];
		$query	.= ' WHERE admin_id='.$aca_admin_id;
		$result	 = $db_coin->db_query_execute($query);

	# Get value and set return
		while(list($admin_id, $admin_name_first, $admin_name_last, $admin_user_name, $admin_email) = $db_coin->db_fetch_row($result)) {
			$_cinfo['admin_id']			= $admin_id;
			$_cinfo['admin_name_first']	= $admin_name_first;
			$_cinfo['admin_name_last']	= $admin_name_last;
			$_cinfo['admin_user_name']	= $admin_user_name;
			$_cinfo['admin_email']		= $admin_email;
			$_cinfo['c_id']			= $admin_id;
			$_cinfo['c_name']			= $admin_name_first.' '.$admin_name_last;
			$_cinfo['c_email']			= $admin_email;
		}

		return $_cinfo;
}

############## MTP Info Calls start ################
# Get MTP array for: Client Info
function get_mtp_client_info($acl_id) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Include language file (must be after parameter load to use them)
		require_once($_CCFG['_PKG_PATH_LANG'].'lang_clients.php');
		IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_clients_override.php')) {
			require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_clients_override.php');
		}

	# Set Query for select.
		$query = 'SELECT * FROM '.$_DBCFG['clients'];

	# Set to logged in Client ID if user to avoid seeing other client id's
		IF (!$_SEC['_sadmin_flg'] && $_SEC['_suser_flg']) {
			$query .= ' WHERE '.$_DBCFG['clients'].'.cl_id='.$_SEC['_suser_id'];
		} ELSE {
			$query .= ' WHERE '.$_DBCFG['clients'].'.cl_id = '.$acl_id;
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set data array
				$_clinfo					= $row;
				$_clinfo['numrows']			= $numrows;
				$_clinfo['cl_found']		= 1;
				$_clinfo['cl_join_ts']		= dt_make_datetime($row['cl_join_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']);

				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_01'].$row['cl_id'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_02'].dt_make_datetime($row['cl_join_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_03'].$row['cl_user_name'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_04'].$row['cl_email'].$_nl;
				$_clinfo['cl_info'] .= '-------------------'.$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_05'].$row['cl_company'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_06'].$row['cl_name_first'].' '.$row['cl_name_last'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_07'].$row['cl_addr_01'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_08'].$row['cl_addr_02'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_09'].$row['cl_city'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_10'].$row['cl_state_prov'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_11'].$row['cl_country'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_12'].$row['cl_zip_code'].$_nl;
				$_clinfo['cl_info'] .= $_LANG['_CLIENTS']['CL_EMAIL_13'].$row['cl_phone'];
			}
		}

		return $_clinfo;
}


# Get MTP array for: Helpdesk Trouble Ticket Info
function get_mtp_hdtt_info($ahd_tt_id) {
	# Get security vars
		$_SEC 	= get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Get helpdesk ticket information
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['helpdesk'];
		$query	.= ' LEFT JOIN '.$_DBCFG['clients'].' ON '.$_DBCFG['clients'].'.cl_id='.$_DBCFG['helpdesk'].'.hd_tt_cl_id';
		$query	.= ' LEFT JOIN '.$_DBCFG['clients_contacts'].' ON '.$_DBCFG['clients_contacts'].'.contacts_email='.$_DBCFG['helpdesk'].'.hd_tt_cl_email';
		$query	.= ' WHERE '.$_DBCFG['helpdesk'].'.hd_tt_id='.$ahd_tt_id;

	# Set to logged in Client ID if not admin to avoid seeing other client ticket id's
		IF (!$_SEC['_sadmin_flg'] && $_SEC['_suser_flg']) {
			$query .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_SEC['_suser_id'];
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set data array
				$_ttinfo						= $row;
				$_ttinfo['numrows']				= $numrows;
				$_ttinfo['hd_tt_id']			= $row['hd_tt_id'];
				$_ttinfo['hd_tt_cl_id']			= $row['hd_tt_cl_id'];
				$_ttinfo['hd_tt_cl_email']		= $row['hd_tt_cl_email'];
				$_ttinfo['hd_tt_time_stamp']		= dt_make_datetime($row['hd_tt_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']);
				$_ttinfo['hd_tt_priority']		= $row['hd_tt_priority'];
				$_ttinfo['hd_tt_category']		= $row['hd_tt_category'];
				$_ttinfo['hd_tt_subject']		= $row['hd_tt_subject'];

				$_ttinfo['hd_tt_message'] 		= $row['hd_tt_message'].$_nl;

				$_ttinfo['hd_tt_cd_id']			= $row['hd_tt_cd_id'];
				$_ttinfo['hd_tt_url']			= $row['hd_tt_url'];
				$_ttinfo['hd_tt_status']			= $row['hd_tt_status'];
				$_ttinfo['hd_tt_closed']			= do_valtostr_open_closed($row['hd_tt_closed']);
				$_ttinfo['hd_tt_rating']			= do_valtostr_rate_ticket($row['hd_tt_rating']);

				$_ttinfo['cl_company']			= $row['cl_company'];
				$_ttinfo['cl_name_first']		= $row['cl_name_first'];
				$_ttinfo['cl_name_last']			= $row['cl_name_last'];
				$_ttinfo['cl_email']			= $row['cl_email'];
				$_ttinfo['cl_user_name']			= $row['cl_user_name'];

				$_ttinfo['contacts_name_first']	= $row['contacts_name_first'];
				$_ttinfo['contacts_name_last']	= $row['contacts_name_last'];
				$_ttinfo['contacts_email']		= $row['contacts_email'];

			# Get domain name if id exists.
				IF ($row['hd_tt_cd_id'] > 0 && $_CCFG['DOMAINS_ENABLE']) {
					$query_cd	 = 'SELECT dom_domain';
					$query_cd	.= ' FROM '.$_DBCFG['domains'];
					$query_cd	.= ' WHERE '.$_DBCFG['domains'].'.dom_id='.$row['hd_tt_cd_id'];

				# Do select
					$result_cd = $db_coin->db_query_execute($query_cd);

				# Get value and set return
					while(list($dom_domain) = $db_coin->db_fetch_row($result_cd)) {$_ttinfo['cd_cl_domain'] = $dom_domain;}
				}
			}
		}

		return $_ttinfo;
}


# Get MTP array for: Helpdesk Trouble Ticket Items / Messages Info
function get_mtp_hdti_info( $ahd_tt_id ) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_tiinfo = array("numrows" => 0, "tt_msgs" => '');

	# Include language file (must be after parameter load to use them)
		require_once($_CCFG['_PKG_PATH_LANG'].'lang_helpdesk.php');
		IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_helpdesk_override.php')) {
			require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_helpdesk_override.php');
		}

	# Get helpdesk ticket information
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['helpdesk'].', '.$_DBCFG['helpdesk_msgs'];
		$query	.= ' WHERE '.$_DBCFG['helpdesk'].'.hd_tt_id='.$_DBCFG['helpdesk_msgs'].'.hdi_tt_id';
		$query	.= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_id='.$ahd_tt_id;

	# Set to logged in Client ID if not admin to avoid seeing other client ticket id's
		IF (!$_SEC['_sadmin_flg'] && $_SEC['_suser_flg']) {
			$query .= ' AND '.$_DBCFG['helpdesk'].'.hd_tt_cl_id='.$_SEC['_suser_id'];
		}

	# Check config for limit of messages.
		IF (!$_CCFG['HELPDESK_REPLY_EMAIL_SET_LIMIT']) {
			$query .= ' ORDER BY '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_time_stamp ASC';
			$_MTP['messages_included'] = $_LANG['_HDESK']['HD_EMAIL_MSGS_NO_LIMIT_STRING'];
		} ELSE {
			$query .= ' ORDER BY '.$_DBCFG['helpdesk_msgs'].'.hdi_tt_time_stamp DESC';
			$query .= ' LIMIT '.$_CCFG['HELPDESK_REPLY_EMAIL_LIMIT'];
			$_MTP['messages_included'] = $_LANG['_HDESK']['HD_EMAIL_MSGS_LIMIT_STRING'];
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			$_tt_msgs_cnt = 0;
			while ($row = $db_coin->db_fetch_array($result)) {
				$_tt_msgs_cnt = $_tt_msgs_cnt + 1;

			# Set data array
				$_tiinfo['numrows']	= $numrows;

			# Get name of user or admin who replied
				IF ($row['hdi_tt_cl_id'] != 0) {
					$_name = get_user_name($row['hdi_tt_cl_id'], 'user');
				} ELSE IF ($row['hdi_tt_ad_id'] != 0) {
					IF ($_CCFG['HELPDESK_ADMIN_REVEAL_ENABLE'] == 1) {
						$_name = get_user_name($row['hdi_tt_ad_id'], 'admin');
					} ELSE {
						$_sinfo = get_contact_info($_CCFG['MC_ID_SUPPORT']);
						$_name = $_sinfo['c_name'];
					}
				}

			# Parse out space in name
				$_str_search	= '&nbsp;';
				$_str_replace	= ' ';
				$_name		= str_replace($_str_search, $_str_replace, $_name);

				IF ($_tt_msgs_cnt > 1) {$_tiinfo['tt_msgs'] .= $_nl;}

				$_tiinfo['tt_msgs'] .= '------------------------------'.$_nl;
				$_tiinfo['tt_msgs'] .= $_LANG['_HDESK']['HD_EMAIL_01'].$_name.$_nl;
				$_tiinfo['tt_msgs'] .= $_LANG['_HDESK']['HD_EMAIL_02'].dt_make_datetime($row['hdi_tt_time_stamp'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
				$_tiinfo['tt_msgs'] .= $_LANG['_HDESK']['HD_EMAIL_03'].$_nl;
				$_tiinfo['tt_msgs'] .= '----------------'.$_nl;
				$_tiinfo['tt_msgs'] .= $row['hdi_tt_message'].$_nl;
			}
		}

		return $_tiinfo;
}

# Get MTP array for: Invoice Info
function get_mtp_invoice_info($ainvc_id) {
	# Get security vars
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Get invoice information (invoice header, client information)
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query	.= ' WHERE '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_id='.$ainvc_id;

	# Set to logged in Client ID if not admin to avoid seeing other client invoice id's
		IF (!$_SEC['_sadmin_flg'] && $_SEC['_suser_flg']) {
			$query	.= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_SEC['_suser_id'];
			$query	.= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][1])."'";

		# Check show pending enable flag
			IF (!$_CCFG['INVC_SHOW_CLIENT_PENDING']) {
				$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
			}
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set data array
				$_ininfo						= $row;
				$_ininfo['numrows']				= $numrows;
				$_ininfo['invc_id']				= $row['invc_id'];
				$_ininfo['invc_status']			= $row['invc_status'];
				$_ininfo['invc_deliv_method']		= $row['invc_deliv_method'];
				$_ininfo['invc_delivered']		= do_valtostr_no_yes($row['invc_delivered']);
				$_ininfo['invc_subtotal_cost']	= $row['invc_subtotal_cost'];
				$_ininfo['invc_tax_01_percent']	= $row['invc_tax_01_percent'];
				$_ininfo['invc_tax_01_amount']	= $row['invc_tax_01_amount'];
				$_ininfo['invc_tax_02_percent']	= $row['invc_tax_02_percent'];
				$_ininfo['invc_tax_02_amount']	= $row['invc_tax_02_amount'];
				$_ininfo['invc_total_cost']		= $row['invc_total_cost'];
				$_ininfo['invc_total_paid']		= $row['invc_total_paid'];
				$_ininfo['invc_ts']				= dt_make_datetime($row['invc_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				$_ininfo['invc_ts_due']			= dt_make_datetime($row['invc_ts_due'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				IF ($row['invc_status'] == $_CCFG['INV_STATUS'][3] ) {
					$_ininfo['invc_ts_paid']	= dt_make_datetime($row['invc_ts_paid'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				} ELSE {
					$_ininfo['invc_ts_paid']	= '';
				}
				$_ininfo['invc_bill_cycle']		= $_CCFG['INVC_BILL_CYCLE'][$row['invc_bill_cycle']];
				$_ininfo['invc_terms']			= $row['invc_terms'];
				$_ininfo['invc_pay_link']		= $row['invc_pay_link'];
			}
		}

		return $_ininfo;
}


# Get MTP array for: Invoice Items Info
function get_mtp_invcitem_info( $ainvc_id ) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;
		$_invc_items_cnt = 0;

	# Include language file (must be after parameter load to use them)
		require_once($_CCFG['_PKG_PATH_LANG'].'lang_invoices.php');
		IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_invoices_override.php')) {
			require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_invoices_override.php');
		}

	# Get invoice information (invoice header, client information)
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices'].', '.$_DBCFG['invoices_items'];
		$query	.= ' WHERE '.$_DBCFG['invoices'].'.invc_id='.$_DBCFG['invoices_items'].'.ii_invc_id';
		$query	.= ' AND '.$_DBCFG['invoices'].'.invc_id='.$ainvc_id;
		$query	.= ' ORDER BY '.$_DBCFG['invoices_items'].'.ii_item_no ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
				$_invc_items_cnt++;

			# Set data array
				$_iiinfo['numrows']				= $numrows;
				$_ii_item_no[$_invc_items_cnt]	= $row['ii_item_no'];
				$_ii_item_name[$_invc_items_cnt]	= $row['ii_item_name'];
				$_ii_item_desc[$_invc_items_cnt]	= $row['ii_item_desc'];
				$_ii_item_cost[$_invc_items_cnt]	= $row['ii_item_cost'];

				IF ($_CCFG['SINGLE_LINE_EMAIL_INVOICE_ITEMS']) {
				// Do single-line invoice items
					$itemno = sprintf("%-6s", $row['ii_item_no']);
					$itemname = sprintf("%-12s", $row['ii_item_name']);
					$itemdesc = sprintf("%-40s", $row['ii_item_desc']);
					$itemcost = $_CCFG['_CURRENCY_PREFIX'].sprintf("%8.".$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']."f", $row['ii_item_cost']).' '.$_CCFG['_CURRENCY_SUFFIX'];
					$_iiinfo['iitems'] .= $itemno . ' ' . $itemname . ' ' . $itemdesc . ' ' . $itemcost . $_nl;

				} ELSE {
				// Do multi-line invoice items (default)
					IF ($_invc_items_cnt > 1) {$_MTP['iitems'] .= $_nl;}
					$_iiinfo['iitems'] .= $_LANG['_INVCS']['INV_EMAIL_01'].$row['ii_item_no'].$_nl;
					$_iiinfo['iitems'] .= $_LANG['_INVCS']['INV_EMAIL_02'].$row['ii_item_name'].$_nl;
					$_iiinfo['iitems'] .= $_LANG['_INVCS']['INV_EMAIL_03'].$row['ii_item_desc'].$_nl;
					$_iiinfo['iitems'] .= $_LANG['_INVCS']['INV_EMAIL_04'].do_currency_format($row['ii_item_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
					IF ( $_invc_items_cnt < $numrows ) { $_iiinfo['iitems'] .= $_nl; }
				}
			}
		}

		return $_iiinfo;
}


# Get MTP array for: Invoice Transaction Info
function get_mtp_trans_info( $ait_id ) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['invoices_trans'];
		$query	.= ' WHERE '.$_DBCFG['invoices_trans'].'.it_id='.$ait_id;

		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set data array
				$_itinfo				= $row;
				$_itinfo['numrows']		= $numrows;
				$_itinfo['it_id']		= str_pad($row['it_id'],5,'0',STR_PAD_LEFT);
				$_itinfo['it_ts']		= dt_make_datetime($row['it_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				$_itinfo['it_invc_id']	= $row['it_invc_id'];
				$_itinfo['it_type']		= $_CCFG['INV_TRANS_TYPE'][$row['it_type']];
				$_itinfo['it_origin']	= $_CCFG['INV_TRANS_ORIGIN'][$row['it_origin']];
				$_itinfo['it_desc']		= $row['it_desc'];
				$_itinfo['it_amount']	= do_currency_format($row['it_amount'],0,0,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
			}
		}

		return $_itinfo;
}


# Get MTP array for: Order Info
function get_mtp_order_info( $aord_id ) {
	# Get security vars
		$_SEC = get_security_flags ();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Include language file (must be after parameter load to use them)
		require_once($_CCFG['_PKG_PATH_LANG'].'lang_orders.php');
		IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_orders_override.php')) {
			require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_orders_override.php');
		}

	# Get order information
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['orders'].', '.$_DBCFG['clients'].', '.$_DBCFG['vendors'];
		$query	.= ', '.$_DBCFG['products'];
		$query	.= ' WHERE '.$_DBCFG['orders'].'.ord_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['orders'].'.ord_vendor_id='.$_DBCFG['vendors'].'.vendor_id';
		$query	.= ' AND '.$_DBCFG['orders'].'.ord_prod_id='.$_DBCFG['products'].'.prod_id';
		$query	.= ' AND '.$_DBCFG['orders'].'.ord_id='.$aord_id;

	# Set to logged in Client ID if not admin to avoid seeing other client order id's
		IF (!$_SEC['_sadmin_flg'] && $_SEC['_suser_flg']) {
			$query .= ' AND '.$_DBCFG['orders'].'.ord_cl_id='.$_SEC['_suser_id'];
		}

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Set data array
				$_orinfo				= $row;
				$_orinfo['numrows']		= $numrows;
				$_orinfo['ord_id']		= $row['ord_id'];
				$_orinfo['cl_id']       = $row['ord_cl_id'];
				$_orinfo['ord_ts']		= dt_make_datetime($row['ord_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']);
				$_orinfo['ord_status']	= $row['ord_status'];
				$_orinfo['ord_domain']	= $row['ord_domain'];
				$_orinfo['vendor_name']	= $row['vendor_name'];
				$_orinfo['prod_name']	= $row['prod_name'];
				$_orinfo['prod_desc']	= $row['prod_desc'];

				$_orinfo['order']  = $_LANG['_ORDERS']['ORD_EMAIL_01'].$row['ord_id'].$_nl;
				$_orinfo['order'] .= $_LANG['_ORDERS']['ORD_EMAIL_02'].dt_make_datetime($row['ord_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
				$_orinfo['order'] .= $_LANG['_ORDERS']['ORD_EMAIL_03'].$row['ord_status'].$_nl;
				$_orinfo['order'] .= '---------------'.$_nl;
				$_orinfo['order'] .= $_LANG['_ORDERS']['ORD_EMAIL_04'].$row['prod_name'].$_nl;
				$_orinfo['order'] .= $_LANG['_ORDERS']['ORD_EMAIL_05'].$row['prod_desc'].$_nl;
				$_orinfo['order'] .= $_LANG['_ORDERS']['ORD_EMAIL_06'].do_currency_format($row['ord_unit_cost'],1,1,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
			}
		}

		return $_orinfo;
}
############## MTP Info Calls end ################


# Get client contact info
function get_contact_client_info($acc_cl_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select and execute
		$query	 = 'SELECT cl_id, cl_name_first, cl_name_last, cl_user_name, cl_email FROM '.$_DBCFG['clients'];
		$query	.= ' WHERE cl_id='.$acc_cl_id.' ORDER BY cl_name_last ASC, cl_name_first ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get value and set return
		while(list($cl_id, $cl_name_first, $cl_name_last, $cl_user_name, $cl_email) = $db_coin->db_fetch_row($result)) {
			$_cinfo['cl_id']		= $cl_id;
			$_cinfo['cl_name_first']	= $cl_name_first;
			$_cinfo['cl_name_last']	= $cl_name_last;
			$_cinfo['cl_user_name']	= $cl_user_name;
			$_cinfo['cl_email']		= $cl_email;
			$_cinfo['c_id']		= $cl_id;
			$_cinfo['c_name']		= $cl_name_first.' '.$cl_name_last;
			$_cinfo['c_email']		= $cl_email;
		}

		return $_cinfo;
}


# Get client contact info for additional email addresses
function get_contact_client_info_alias($alias_id, $idtype) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select and execute
		$query = 'SELECT contacts_id, contacts_cl_id, contacts_name_first, contacts_name_last, contacts_email FROM '.$_DBCFG['clients_contacts'];
		IF ($idtype) {
			$query .= ' WHERE contacts_cl_id='.$alias_id;
		} ELSE {
			$query .= ' WHERE contacts_id='.$alias_id;
		}
		$query	.= ' ORDER BY contacts_name_last, contacts_name_first ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get value and set return
		$x=0;
		while(list($contact_id, $cl_id, $cl_name_first, $cl_name_last, $cl_email) = $db_coin->db_fetch_row($result)) {
			$x++;
			$_cinfo[$x]['cl_id']		= $acc_cl_id;
			$_cinfo[$x]['cl_name_first']	= $cl_name_first;
			$_cinfo[$x]['cl_name_last']	= $cl_name_last;
			$_cinfo[$x]['cl_user_name']	= '';
			$_cinfo[$x]['cl_email']		= $cl_email;
			$_cinfo[$x]['c_id']			= $cl_id;
			$_cinfo[$x]['c_name']		= $cl_name_first.' '.$cl_name_last;
			$_cinfo[$x]['c_email']		= $cl_email;
		}

		return $_cinfo;
}


# Do select list for: Icons
function do_select_list_icon($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT icon_id, icon_name, icon_desc, icon_filename FROM '.$_DBCFG['icons'].' ORDER BY icon_name ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BASE']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($icon_id, $icon_name, $icon_desc, $icon_filename) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$icon_id.'"';
			IF ($icon_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$icon_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


/**************************************************************
 *           Start Common Module Functions phpCOIN
**************************************************************/
# Note- this return is just boolean checked (0 or >0) so this could be
# swapped out with code in do_get_domain_id to serve both functions.
# Was written before cd_id field added.
function do_domain_exist_check($adomain, $acl_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# return 'OK' if Domains Disabled
		IF (!$_CCFG['DOMAINS_ENABLE']) {return 0;}

	# Return 0 if domain name "NONE"
		IF (strtolower($adomain) == 'none') {return 0;}

	# Set Query for select
		$query	= 'SELECT dom_cl_id FROM '.$_DBCFG['domains']." WHERE dom_domain='".$db_coin->db_sanitize_data($adomain)."'";
		IF ($acl_id) {$query .= ' AND dom_cl_id <> '.$acl_id;}
		$query .= ' ORDER BY dom_cl_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		return $numrows;
}


function do_get_domain_id($adomain) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select
		$query	= 'SELECT dom_id FROM '.$_DBCFG['domains']." WHERE dom_domain='".$db_coin->db_sanitize_data($adomain)."' ORDER BY dom_cl_id ASC";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Value
		$_ret = 0;
		while(list($dom_id) = $db_coin->db_fetch_row($result)) {$_ret = $dom_id;}

		return $_ret;
}


function do_get_client_domain_id($adomain, $acl_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select
		$query	 = 'SELECT dom_id FROM '.$_DBCFG['domains'];
		$query	.= " WHERE dom_domain='".$db_coin->db_sanitize_data($adomain)."' AND dom_cl_id=".$acl_id;
		$query	.= ' ORDER BY dom_cl_id ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Value
		$_ret = 0;
		while(list($dom_id) = $db_coin->db_fetch_row($result)) {$_ret = $dom_id;}

		return $_ret;
}


# Do get server name for passed si_id
function do_get_server_name($asi_id) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Check for vlaid argument:
		IF ($asi_id > 0 ) {

		# Set Query and do select.
			$query	= 'SELECT si_name FROM '.$_DBCFG['server_info'].' WHERE si_id='.$asi_id;
			$result	= $db_coin->db_query_execute($query);
			$numrows	= $db_coin->db_query_numrows($result);

		# Check return and Process query results
			IF (!$numrows) {
				$_ret = 'error';
			} ELSE {
				while(list($si_name) = $db_coin->db_fetch_row($result)) {$_ret = $si_name;}
			}
		} ELSE {
			$_ret = 'error';
		}

	# Set return
		return $_ret;
}


function do_get_max_cl_id() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(cl_id) FROM '.$_DBCFG['clients'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_cl_id) = $db_coin->db_fetch_row($result)) {$max_cl_id = $_max_cl_id;}

	# Check / Set Value for return
		IF (!$max_cl_id) {
			return $_CCFG['BASE_CLIENT_ID'];
		} ELSE {
			return $max_cl_id;
		}
}


function do_get_max_ord_id() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(ord_id) FROM '.$_DBCFG['orders'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_ord_id) = $db_coin->db_fetch_row($result)) {$max_ord_id = $_max_ord_id;}

	# Check / Set Value for return
		IF (!$max_ord_id) {
			return $_CCFG['BASE_ORDER_ID'];
		} ELSE {
			return $max_ord_id;
		}
}

function do_get_max_domain_id() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(dom_id) FROM '.$_DBCFG['domains'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_dom_id) = $db_coin->db_fetch_row($result)) {$max_dom_id = $_max_dom_id;}

	# Check / Set Value for return
		IF (!$max_dom_id) {
			return 1;
		} ELSE {
			return $max_dom_id;
		}
}

function do_get_max_invc_id() {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	= 'SELECT max(invc_id) FROM '.$_DBCFG['invoices'];
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Get Max Value
		while(list($_max_invc_id) = $db_coin->db_fetch_row($result)) {$max_invc_id = $_max_invc_id;}

	# Check / Set Value for return
		IF (!$max_invc_id) {
			return $_CCFG['BASE_INVOICE_ID'];
		} ELSE {
			return $max_invc_id;
		}
}


function do_calc_invc_values($adata) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Array passed / and returned (?=a passed, ?=i return)
		# $adata['invc_id']
		# $?data['invc_total_cost']		- To be calcd
		# $?data['invc_subtotal_cost']	- To be calcd
		# $?data['invc_tax_01_percent']
		# $?data['invc_tax_01_amount']	- To be calcd
		# $?data['invc_tax_02_percent']
		# $?data['invc_tax_02_amount']	- To be calcd
		# $?data['invc_tax_autocalc']

	# Check / Set Incoming Data
		IF (!$adata['invc_tax_01_percent'])	{$adata['invc_tax_01_percent'] = 0;}
		IF (!$adata['invc_tax_02_percent'])	{$adata['invc_tax_02_percent'] = 0;}

	# Here we allow phpCOIN to override the tax rates.
	# This could be because one of the taxes has different rates for different
	# jurisdictions, or because one of the rates has changed over time
		IF (file_exists(PKG_PATH_OVERRIDES.'invoice_tax_override.php')) {
			require(PKG_PATH_OVERRIDES.'invoice_tax_override.php');
		}

		$idata['invc_tax_autocalc']	= $adata['invc_tax_autocalc'];
		$idata['invc_tax_01_percent']	= $adata['invc_tax_01_percent'];
		$idata['invc_tax_01_amount']	= $adata['invc_tax_01_amount'];
		$idata['invc_tax_02_percent']	= $adata['invc_tax_02_percent'];
		$idata['invc_tax_02_amount']	= $adata['invc_tax_02_amount'];

	# Build query and select by invoice id
		$query	 = 'SELECT * FROM '.$_DBCFG['invoices_items'];
		$query	.= ' WHERE '.$_DBCFG['invoices_items'].'.ii_invc_id='.$adata['invc_id'];
		$query	.= ' ORDER BY ii_item_no ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results and get values
		$_cost_subtotal_all	= 0; $_cost_subtotal_01	= 0; $_cost_subtotal_02	= 0;
		$_tax_subtotal_all	= 0; $_tax_subtotal_01	= 0; $_tax_subtotal_02	= 0;

		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Here we allow phpCOIN to override the items info.
			# This could be because one of the taxes may or may not be piggybacked
			# for different jurisdictions
				IF (file_exists(PKG_PATH_OVERRIDES.'invoice_items_override.php')) {
					require(PKG_PATH_OVERRIDES.'invoice_items_override.php');
				}

			# If prices include tax, then remove applicable taxes BEFORE we add taxes.
				IF ($_CCFG['PRICES_INCLUDE_TAXES']) {
					$done=0;

				# Start with tax2 because it's the last one applied
				# If Tax 2 is applied, remove it
					IF ($row['ii_apply_tax_02'] == 1) {
						IF ($row['ii_calc_tax_02_pb']) {

						# If Tax2 piggybacked, remove it
							$row['ii_item_cost'] = $row['ii_item_cost'] / (1+($idata['invc_tax_02_percent'] / 100));
						} ELSE {

						# If NOT piggybacked, remove both taxes together and then set "done" flag
							$row['ii_item_cost'] = $row['ii_item_cost'] / (1+(($idata['invc_tax_02_percent'] + $idata['invc_tax_01_percent']) / 100));
							$done++;
						}
					}

				# If Tax1 is applied and we are NOT done, remove Tax1
					IF (($row['ii_apply_tax_01'] == 1) && (!$done)) {
						$row['ii_item_cost'] = $row['ii_item_cost'] / (1+($idata['invc_tax_01_percent'] / 100));
					}
				}

			# Now calculate our taxes as normal
				$_cost_subtotal_all = $_cost_subtotal_all + $row['ii_item_cost'];
				IF ($row['ii_apply_tax_01'] == 1) {
					$_cost_subtotal_01	= $_cost_subtotal_01 + $row['ii_item_cost'];
					$_tax_subtotal_01	= $_tax_subtotal_01 + ($row['ii_item_cost'] * ($idata['invc_tax_01_percent'] / 100));
				}

				IF ($row['ii_apply_tax_02'] == 1) {
					IF ($row['ii_calc_tax_02_pb'] != 1) {
						$_cost_subtotal_02	= $_cost_subtotal_02 + $row['ii_item_cost'];
						$_tax_subtotal_02	= $_tax_subtotal_02 + ($row['ii_item_cost'] * ($idata['invc_tax_02_percent'] / 100));
					} ELSE {
						$_tax_01			= ($row['ii_item_cost'] * ($idata['invc_tax_01_percent'] / 100));
						$_tax_02_amount	= $row['ii_item_cost'] + $_tax_01;
						$_cost_subtotal_02	= $_cost_subtotal_02 + $_tax_02_amount;
						$_tax_subtotal_02	= $_tax_subtotal_02 + ($_tax_02_amount * ($idata['invc_tax_02_percent'] / 100));
					}
				}
			}
		}

	# Calc tax amounts on total cost
		$_tax_subtotal_01_all = round(($_cost_subtotal_all * ($idata['invc_tax_01_percent'] / 100)), $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
		$_tax_subtotal_02_all = round(($_cost_subtotal_all * ($idata['invc_tax_02_percent'] / 100)), $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);

	# Check for tax enabled and set zero if not.
		IF ($_CCFG['INVC_TAX_01_ENABLE'] != 1) {
			$_tax_subtotal_01_all = 0; $_tax_subtotal_01 = 0; $idata['invc_tax_01_amount'] = 0;
		}
		IF ($_CCFG['INVC_TAX_02_ENABLE'] != 1) {
			$_tax_subtotal_02_all = 0; $_tax_subtotal_02 = 0; $idata['invc_tax_02_amount'] = 0;
		}

	# Set return values based on various config items
		IF ($idata['invc_tax_autocalc'] == 1) {
			IF ($_CCFG['INVC_TAX_BY_ITEM'] == 1) {
				$idata['invc_tax_01_amount'] 	= round($_tax_subtotal_01, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_tax_02_amount'] 	= round($_tax_subtotal_02, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_subtotal_cost'] 	= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_total_cost']	= $idata['invc_subtotal_cost'] + $idata['invc_tax_01_amount'] + $idata['invc_tax_02_amount'];
			} ELSE {
				$idata['invc_tax_01_amount'] 	= round($_tax_subtotal_01_all, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_tax_02_amount'] 	= round($_tax_subtotal_02_all, $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_subtotal_cost'] 	= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
				$idata['invc_total_cost']	= $idata['invc_subtotal_cost'] + $idata['invc_tax_01_amount'] + $idata['invc_tax_02_amount'];
			}
		} ELSE {
			$idata['invc_tax_01_amount']		= round($adata['invc_tax_01_amount'], $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
			$idata['invc_tax_02_amount'] 		= round($adata['invc_tax_02_amount'], $_CCFG['TAX_DISPLAY_DIGITS_AMOUNT']);
			$idata['invc_subtotal_cost'] 		= round($_cost_subtotal_all, $_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
			$idata['invc_total_cost']		= $idata['invc_subtotal_cost'] + $idata['invc_tax_01_amount'] + $idata['invc_tax_02_amount'];
		}

	# Check / Outgoing Data
		IF (!$idata['invc_total_cost'])		{$idata['invc_total_cost'] = '0.00';}
		IF (!$idata['invc_subtotal_cost'])		{$idata['invc_subtotal_cost'] = '0.00';}
		IF (!$idata['invc_tax_01_percent'])	{$idata['invc_tax_01_percent'] = '0.00';}
		IF (!$idata['invc_tax_01_amount'])		{$idata['invc_tax_01_amount'] = '0.00';}
		IF (!$idata['invc_tax_02_percent'])	{$idata['invc_tax_02_percent'] = '0.00';}
		IF (!$idata['invc_tax_02_amount'])		{$idata['invc_tax_02_amount'] = '0.00';}

	# Check / Set Value for return
		return $idata;
}


function do_get_invc_values($ainvc_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select and execute
		$query  = 'SELECT * FROM '.$_DBCFG['invoices'];
		$query .= ' WHERE invc_id='.$ainvc_id;

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above- need to verify)
		while ($row = $db_coin->db_fetch_array($result)) {

		# Rebuild Data Array with returned record
			$idata['invc_id']			= $row['invc_id'];
			$idata['invc_ts']			= $row['invc_ts'];
			$idata['invc_ts_due']		= $row['invc_ts_due'];
			$idata['invc_ts_paid']		= $row['invc_ts_paid'];
			$idata['invc_total_cost']	= $row['invc_total_cost'];
			$idata['invc_total_paid']	= $row['invc_total_paid'];
			$idata['invc_subtotal_cost']	= $row['invc_subtotal_cost'];
			$idata['invc_tax_01_percent']	= $row['invc_tax_01_percent'];
			$idata['invc_tax_01_amount']	= $row['invc_tax_01_amount'];
			$idata['invc_tax_02_percent']	= $row['invc_tax_02_percent'];
			$idata['invc_tax_02_amount']	= $row['invc_tax_02_amount'];
			$idata['invc_tax_autocalc']	= $row['invc_tax_autocalc'];
		}

	# Check / Outgoing Data
		IF (!$idata['invc_total_cost'])		{$idata['invc_total_cost'] = '0.00';}
		IF (!$idata['invc_total_paid'])		{$idata['invc_total_paid'] = '0.00';}
		IF (!$idata['invc_subtotal_cost'])		{$idata['invc_subtotal_cost'] = '0.00';}
		IF (!$idata['invc_tax_01_percent'])	{$idata['invc_tax_01_percent'] = '0.00';}
		IF (!$idata['invc_tax_01_amount'])		{$idata['invc_tax_01_amount'] = '0.00';}
		IF (!$idata['invc_tax_02_percent'])	{$idata['invc_tax_02_percent'] = '0.00';}
		IF (!$idata['invc_tax_02_amount'])		{$idata['invc_tax_02_amount'] = '0.00';}
		IF ($idata['invc_tax_autocalc'] == '')	{$idata['invc_tax_autocalc'] = '1';}

	# Check / Set Value for return
		return $idata;
}


function do_set_invc_values($ainvc_id, $aadd) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_LANG, $_nl, $_sp;

	# Get invoice values now (need tax percent for recalc
		$idata_now = do_get_invc_values($ainvc_id);

	# Get invoice calc new values
		$idata_now['invc_id']	= $ainvc_id;
		$idata_new			= do_calc_invc_values($idata_now);

	# Do update
		$query 	 = 'UPDATE '.$_DBCFG['invoices'].' SET ';
		$query 	.= "invc_total_cost='".$idata_new['invc_total_cost']."', ";
		$query 	.= "invc_subtotal_cost='".$idata_new['invc_subtotal_cost']."', ";
		$query 	.= "invc_tax_01_amount='".$idata_new['invc_tax_01_amount']."', ";
		$query 	.= "invc_tax_02_amount='".$idata_new['invc_tax_02_amount']."' ";
		$query 	.= 'WHERE invc_id='.$ainvc_id;
		$result	= $db_coin->db_query_execute($query) OR DIE("Unable to complete request");
		$numrows	= $db_coin->db_query_affected_rows();

	# Update Invoice Debit Transaction
		$q_it 	 = 'UPDATE '.$_DBCFG['invoices_trans'].' SET ';
		$q_it 	.= "it_amount='".$idata_new['invc_total_cost']."' ";
		$q_it 	.= 'WHERE it_invc_id='.$ainvc_id.' AND it_type=0';
		$r_it	 = $db_coin->db_query_execute($q_it) OR DIE("Unable to complete request");

	# If no invoice transaction was updated, then create one
		IF ($aadd && $r_it == FALSE) {
			IF (!$idata_new['invc_ts']) {$idata_new['invc_ts'] = dt_get_uts();}
			$_it_def = 0;
			$_it_desc	= $_LANG['_INVCS']['l_Invoice_ID'].' - '.$ainvc_id;
			$q2_it  = 'INSERT INTO '.$_DBCFG['invoices_trans'].' (';
			$q2_it .= 'it_ts, it_invc_id, it_type';
			$q2_it .= ', it_origin, it_desc, it_amount';
			$q2_it .= ') VALUES ( ';
			$q2_it .= "'".$db_coin->db_sanitize_data($idata_new['invc_ts'])."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($ainvc_id)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_def)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($_it_desc)."', ";
			$q2_it .= "'".$db_coin->db_sanitize_data($idata_new['invc_total_cost'])."'";
			$q2_it .= ")";
			$r2_it = $db_coin->db_query_execute($q2_it);
		}

	# Set return
		return $numrows;
}


function do_get_invc_cl_balance($ainvc_cl_id, $ainvc_id=0) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_SEC = get_security_flags();

	# Array returned
		# $idata['total_cost']	- Sum Invoice Cost Column
		# $idata['total_paid']	- Sum Invoice Paid Column
		# $idata['net_balance']	- (Cost*-1)+Paid

	# Set Query for select and execute
		$query  = 'SELECT *';
		$query .= ' FROM '.$_DBCFG['invoices_trans'].', '.$_DBCFG['invoices'].', '.$_DBCFG['clients'];
		$query .= ' WHERE '.$_DBCFG['invoices_trans'].'.it_invc_id='.$_DBCFG['invoices'].'.invc_id';
		$query .= ' AND '.$_DBCFG['invoices'].'.invc_cl_id='.$_DBCFG['clients'].'.cl_id';

	# Block out draft [1] and void [5] and pending [4]
		$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][1])."'";
		$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][4])."'";
		$query .= ' AND '.$_DBCFG['invoices'].".invc_status != '".$db_coin->db_sanitize_data($_CCFG['INV_STATUS'][5])."'";

	# If specific client requested
		IF ($ainvc_cl_id) {$query .= ' AND invc_cl_id='.$ainvc_cl_id;}

	# If specific invoice requested
		IF ($ainvc_id) {$query .= ' AND invc_id='.$ainvc_id;}

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above)
		$_total_cost = 0; $_total_paid = 0;

		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {

			# Sum debits and credits
				IF ($row['it_type'] == 0) {
					$_total_cost = $_total_cost + $row['it_amount'];
				} ELSE {
					$_total_paid = $_total_paid + $row['it_amount'];
				}
			}
		}

	# Set return array
		$idata['total_cost']	= $_total_cost;	// round($_total_cost,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$idata['total_paid']	= $_total_paid;	// round($_total_paid,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$idata['net_balance']	= $idata['total_cost']-$idata['total_paid'];	// round((($idata['total_cost'])+$idata['total_paid']*-1),$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);

	# Check / Set Value for return
		return $idata;
}


function do_get_invc_PTD($ainvc_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select and execute
		$query 	 = 'SELECT sum(it_amount) as PTD FROM '.$_DBCFG['invoices_trans'];
		$query 	.= " WHERE (it_invc_id='".$ainvc_id."' AND it_type <> 0)";
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);
		IF ($numrows)	{while(list($PTD) = $db_coin->db_fetch_row($result)) {$_PTD = $PTD;}}
		IF (!$_PTD)	{$_PTD = 0;}	// 2008-01-31: Deleted payments cause this to not be set, so we will force it

	# Check / Set Value for return
		return $_PTD;
}


function do_get_bill_supplier_balance($as_id, $abill_id) {
	# Dim some Vars
		global $_CCFG, $_DBCFG, $db_coin;
		$_SEC = get_security_flags();

	# Array returned
		# $idata['total_cost']	- Sum Bill Cost Column
		# $idata['total_paid']	- Sum Bill Paid Column
		# $idata['net_balance']	- (Cost*-1)+Paid

	# Set Query for select and execute
		$query  = 'SELECT * ';
		$query .= ' FROM '.$_DBCFG['bills_trans'].', '.$_DBCFG['bills'].', '.$_DBCFG['suppliers'];
		$query .= ' WHERE '.$_DBCFG['bills_trans'].'.bt_bill_id='.$_DBCFG['bills'].'.bill_id';
		$query .= ' AND '.$_DBCFG['bills'].'.bill_s_id='.$_DBCFG['suppliers'].'.s_id';

	# Block out draft [1] and void [5] and pending [4]
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][1])."'";
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][5])."'";
		$query .= ' AND '.$_DBCFG['bills'].".bill_status != '".$db_coin->db_sanitize_data($_CCFG['BILL_STATUS'][4])."'";

	# If specific supplier requested
		IF ($as_id) {$query .= ' AND bill_s_id='.$as_id;}

	# If specific bill requested
		IF ($abill_id) {$query .= ' AND bill_id='.$abill_id;}

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above)
		$_total_cost = 0;
		$_total_paid = 0;

		IF ($numrows) {
			while ($row = $db_coin->db_fetch_array($result)) {
			# Sum the debits and credits
				IF ($row['bt_type'] == 0) {
					$_total_cost += $row['bt_amount'];
				} ELSE {
					$_total_paid += $row['bt_amount'];
				}
			}
		}

	# Set return array
		$idata['total_cost']	= $_total_cost;	// round($_total_cost,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$idata['total_paid']	= $_total_paid;	// round($_total_paid,$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);
		$idata['net_balance']	= $_total_cost - $_total_paid;	// round((($itotal_cost+$total_paid*-1),$_CCFG['CURRENCY_DISPLAY_DIGITS_AMOUNT']);

	# Check / Set Value for return
		return $idata;
}


function do_set_trans_values($atdata) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_SERVER, $_nl, $_sp;
		$c = '';

	# Update Invoice Debit Transaction
		$q_it = 'UPDATE '.$_DBCFG['invoices_trans'].' SET ';
		IF ($atdata['it_ts'] != '')		{$q_it .= "it_ts='".$db_coin->db_sanitize_data($atdata['it_ts'])."'";				$c = ',';}
		IF ($atdata['it_invc_id'] != '')	{$q_it .= $c."it_invc_id='".$db_coin->db_sanitize_data($atdata['it_invc_id'])."'";	$c = ',';}
		IF ($atdata['it_type'] != '')		{$q_it .= $c."it_type='".$db_coin->db_sanitize_data($atdata['it_type'])."'";		$c = ',';}
		IF ($atdata['it_origin'] != '')	{$q_it .= $c."it_origin='".$db_coin->db_sanitize_data($atdata['it_origin'])."'";	$c = ',';}
		IF ($atdata['it_desc'] != '')		{$q_it .= $c."it_desc='".$db_coin->db_sanitize_data($atdata['it_desc'])."'";		$c = ',';}
		IF ($atdata['it_amount'] != '')	{$q_it .= $c."it_amount='".$db_coin->db_sanitize_data($atdata['it_amount'])."'";	$c = ',';}
		IF ($atdata['it_type'] == 0) {
			$q_it .= " WHERE it_invc_id = $atdata[it_invc_id] AND it_type = 0";
		} ELSE {
			$q_it .= " WHERE it_id = $atdata[it_id]";
		}
		$r_it = $db_coin->db_query_execute($q_it) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}


function do_get_trans_values($ait_id) {
	# Set Query for select and execute
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_SERVER, $_nl, $_sp;

		$query  = 'SELECT * FROM '.$_DBCFG['invoices_trans'];
		$query .= ' WHERE it_id='.$ait_id;

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Process query results (assumes one returned row above- need to verify)
		while ($row = $db_coin->db_fetch_array($result)) {

		# Rebuild Data Array with returned record
			$tdata['numrows']		= $numrows;
			$tdata['it_id']		= $row['it_id'];
			$tdata['it_ts']		= $row['it_ts'];
			$tdata['it_invc_id']	= $row['it_invc_id'];
			$tdata['it_type']		= $row['it_type'];
			$tdata['it_origin']		= $row['it_origin'];
			$tdata['it_desc']		= $row['it_desc'];
			$tdata['it_amount']		= $row['it_amount'];
		}

	# Check / Set Value for return
		return $tdata;
}


function do_set_invc_status($ainvc_id, $astatus) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do update
		$query_is 	 = 'UPDATE '.$_DBCFG['invoices'].' SET ';
		$query_is 	.= "invc_status='".$db_coin->db_sanitize_data($astatus)."'";
		$query_is 	.= ' WHERE invc_id='.$ainvc_id;
		$result_is	 = $db_coin->db_query_execute($query_is) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows();
}


function do_set_invc_delivered($ainvc_id, $avalue) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do update
		$query_id 	 = 'UPDATE '.$_DBCFG['invoices'].' SET ';
		$query_id 	.= "invc_delivered='".$db_coin->db_sanitize_data($avalue)."'";
		$query_id 	.= ' WHERE invc_id='.$ainvc_id;
		$result_id	= $db_coin->db_query_execute($query_id) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows ();
}


function do_set_invc_recurr_proc($ainvc_id, $avalue) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do update
		$query_id 	 = 'UPDATE '.$_DBCFG['invoices'].' SET ';
		$query_id		.= "invc_recurr_proc='".$db_coin->db_sanitize_data($avalue)."'";
		$query_id 	.= ' WHERE invc_id='.$ainvc_id;
		$result_id	= $db_coin->db_query_execute($query_id) OR DIE("Unable to complete request");
		return $db_coin->db_query_affected_rows ();
}


function do_get_max_invc_item_no($ainvc_id) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query and select for max field value.
		$query	 = 'SELECT max(ii_item_no) FROM '.$_DBCFG['invoices_items'];
		$query	.= ' WHERE '.$_DBCFG['invoices_items'].'.ii_invc_id='.$ainvc_id;
		$result	= $db_coin->db_query_execute($query);

	# Get Max Value
		while(list($_max_item_no) = $db_coin->db_fetch_row($result)) {$max_item_no = $_max_item_no;}

	# Check / Set Value for return
		IF (!$max_item_no) {return 0;} ELSE {return $max_item_no;}
}


# Do client status select list
function do_select_list_status_client($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i < count($_CCFG['CL_STATUS']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['CL_STATUS'][$i]).'"';
			IF ($_CCFG['CL_STATUS'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['CL_STATUS'][$i].'</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do order status select list
function do_select_list_status_order($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['ORD_STATUS']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['ORD_STATUS'][$i]).'"';
			IF ($_CCFG['ORD_STATUS'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['ORD_STATUS'][$i].'</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;

		return $_out;
}


function do_select_list_status_invoice($aname, $avalue, $aall) {
	# Dim some Vars:
		global $_CCFG, $_LANG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Add status "all statuses"
		If ($aall) {
			$_out .= '<option value="ALL"';
			IF ($avalue == 'ALL') {$_out .= ' selected';}
			$_out .= '>'.$_LANG['_BASE']['All'].'</option>'.$_nl;
		}

	# Loop array and load list
		FOR ($i = 0; $i < count($_CCFG['INV_STATUS']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['INV_STATUS'][$i]).'"';
			IF ($_CCFG['INV_STATUS'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['INV_STATUS'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do domain status select list
function do_select_list_domain_status($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i <= count($_CCFG['DOM_STATUS']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['DOM_STATUS'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do domain type select list
function do_select_list_domain_type($aname, $avalue) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i <= count($_CCFG['DOM_TYPE']); $i++) {
			$_out .= '<option value="'.$i.'"';
			IF ($i == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['DOM_TYPE'][$i].'</option>'.$_nl;
		}
		$_out .= '</select>'.$_nl;

		return $_out;
}


# Do select list for: Mail Contacts
function do_select_list_mail_contacts($aname, $avalue, $astatus=0) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT mc_id, mc_name, mc_email, mc_status';
		$query	.= ' FROM '.$_DBCFG['mail_contacts'];
		IF ($astatus == 1) {$query .= ' WHERE mc_status = 1';}
		$query	.= ' ORDER BY mc_name ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);

	# Build Form row
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Process query results
		while(list($mc_id, $mc_name, $mc_email, $mc_status) = $db_coin->db_fetch_row($result)) {
			IF ($mc_id == $_CCFG['MC_ID_SUPPORT'] && !$_CCFG['ENABLE_SUPPORT_ON_CONTACT']) {
			# twiddle our thumbs :)
			} ELSE {
				$_out .= '<option value="'.$mc_id.'"';
				IF ($mc_id == $avalue) {$_out .= ' selected';}
				$_out .= '>'.$mc_name.'</option>'.$_nl;
			}
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do return string from value for: Mail Contacts
function do_valtostr_mail_contacts($avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT mc_id, mc_name';
		$query	.= ' FROM '.$_DBCFG['mail_contacts'];
		$query	.= ' WHERE mc_id='.$avalue;

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Process query results
		while(list($mc_id, $mc_name) = $db_coin->db_fetch_row($result)) {$_out = $mc_id.'- '.$mc_name;}

		return $_out;
}


# Do select list for: Clients Additional Emails
function do_select_list_clients_additional_emails($avalue, $aname) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_out = '';

	# Set Query for select.
		$query	= 'SELECT contacts_id, contacts_cl_id, contacts_name_first, contacts_name_last, contacts_email FROM '.$_DBCFG['clients_contacts'];
		IF ($avalue) {$query .= ' WHERE contacts_cl_id='.$avalue;}
		$query .= ' ORDER BY contacts_name_last ASC, contacts_name_first ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

		IF ($numrows) {
		# Process query results to list individual clients
			while(list($contacts_id, $contacts_cl_id, $contacts_name_first, $contacts_name_last, $contacts_email) = $db_coin->db_fetch_row($result)) {
		    	$i++;
				$_out .= '<option value="'.'alias|'.$contacts_id.'">';
				$_out .= $_sp.$_sp.$_sp.$contacts_name_last.', '.$contacts_name_first.' - '.$aname.' ('.$_LANG['_BASE']['Email_Additional'].')</option>'.$_nl;
			}
			return $_out;
		} ELSE {
		    return '';
		}
}

# Do select list for: Clients
function do_select_list_clients($aname, $avalue, $ashowalloption=0, $mailonly=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT cl_id, cl_name_first, cl_name_last, cl_user_name ';
		$query	.= 'FROM '.$_DBCFG['clients'].' ';
		IF ($mailonly) {$query .= "WHERE LOWER(cl_email) <> 'none' ";}
		$query	.= 'ORDER BY cl_name_last ASC, cl_name_first ASC';
		$result	 = $db_coin->db_query_execute($query);
		$numrows	 = $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BASE']['Please_Select'].'</option>'.$_nl;

		IF ($ashowalloption) {

		# Show All Servers
			$srv_query	= 'SELECT si_id, si_name FROM '.$_DBCFG['server_info'].' ORDER BY si_name ASC';
			$srv_result	= $db_coin->db_query_execute($srv_query);
			$srv_numrows	= $db_coin->db_query_numrows($srv_result);
			while(list($si_id, $si_name) = $db_coin->db_fetch_row($srv_result)) {
				$_out .= '<option value="server|'.$si_id.'">'.$_LANG['_MAIL']['Clients_On'].' '.$si_name.'</option>'.$_nl;
			}

		# Show All Groups
			$_out .= '<option value="group|1">'.$_LANG['_BASE']['User_Groups_01'].'</option>'.$_nl;
			$_out .= '<option value="group|2">'.$_LANG['_BASE']['User_Groups_02'].'</option>'.$_nl;
			$_out .= '<option value="group|3">'.$_LANG['_BASE']['User_Groups_03'].'</option>'.$_nl;
			$_out .= '<option value="group|4">'.$_LANG['_BASE']['User_Groups_04'].'</option>'.$_nl;
			$_out .= '<option value="group|5">'.$_LANG['_BASE']['User_Groups_05'].'</option>'.$_nl;
			$_out .= '<option value="group|6">'.$_LANG['_BASE']['User_Groups_06'].'</option>'.$_nl;
			$_out .= '<option value="group|7">'.$_LANG['_BASE']['User_Groups_07'].'</option>'.$_nl;
			$_out .= '<option value="group|8">'.$_LANG['_BASE']['User_Groups_08'].'</option>'.$_nl;

		# Show "All Clients"
			$_out .= '<option value="-1"';
			IF ($avalue == -1) {$_out .= ' selected';} {
				$_out .= '>'.$_LANG['_BASE']['All_Active_Clients'].'</option>'.$_nl;
			}
		}

	# Process query results to list individual clients
		while(list($cl_id, $cl_name_first, $cl_name_last, $cl_user_name) = $db_coin->db_fetch_row($result)) {
			$_more = '';

		# Add client info, indenting if additional emails present
			$_out .= '<option value="'.$cl_id.'"';
			IF ($cl_id == $avalue) {$_out .= ' selected';}
			$_out .= '>';
			$_out .= $cl_name_last.', '.$cl_name_first.' - '.$cl_user_name.'</option>'.$_nl;

		# Grab any additional emails for this client, so they are all together in the list
			IF ($ashowalloption) {$_more = do_select_list_clients_additional_emails($cl_id, $cl_user_name);}

		# Add "All" option, if necessary
			IF ($_more) {
				IF (substr_count($_more, '<option') > 1) {
					$_out .= '<option value="contacts|'.$cl_id.'">'.$_sp.$_sp.$_sp.$cl_name_last.', '.$cl_name_first.' - '.$cl_user_name.' ('.$_LANG['_BASE']['All_Contacts'].')</option>'.$_nl;
				}
				$_out .= $_more;
			}
		}
		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select list for: Clients
# To allow Admin to enter trouble ticket for client
function do_select_list_clients_emails($aname) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query = 'SELECT cl_id, cl_name_first, cl_name_last, cl_user_name, cl_email FROM '.$_DBCFG['clients'];
		IF ($aname) {$query .= " WHERE cl_email='".$db_coin->db_sanitize_data($aname)."'";}
		$query .= ' ORDER BY cl_name_last ASC, cl_name_first ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form field output
		$_out .= '<select class="select_form" name="hd_tt_cl_email" size="1" value="'.$avalue.'">'.$_nl;

	# Process query results
		$itsselected =0;
		while(list($cl_id, $cl_name_first, $cl_name_last, $cl_user_name, $cl_email) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.htmlspecialchars($cl_email).'"';

		# Grab first one as "selected"
			IF (!$itsselected) {$_out .= ' selected'; $itsselected++;}

		# Build the line
			$_out .= '>'.$cl_name_last.', '.$cl_name_first.' - '.$cl_user_name.'</option>'.$_nl;

		# Grab any additional emails for this client, so they are all together in the list
			$_out .= do_select_list_clients_additional_emails($cl_id, $cl_user_name);
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select list for: Vendors
function do_select_list_vendors($aname, $avalue) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT vendor_id, vendor_name FROM '.$_DBCFG['vendors'].' ORDER BY vendor_name ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Process query results
		while(list($vendor_id, $vendor_name) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$vendor_id.'"';
			IF ($vendor_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$vendor_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select list for: Countries
function do_select_list_countries($aname, $avalue) {
	# Dim some Vars
		global $_Countries, $_UVAR, $_nl, $_sp;
		$ListSize = sizeof($_Countries);

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Loop through countries list array
		FOR ($i=0; $i< $ListSize; $i++) {
			$_out .= '<option value="'.htmlspecialchars($_Countries[$i]).'"';
			IF ($_Countries[$i] == $avalue) {
				$_out .= ' selected';
			} ELSEIF (!$avalue && $_Countries[$i] == $_UVAR['CO_INFO_07_COUNTRY']) {
				$_out .= ' selected';
			}
			$_out .= '>'.$_Countries[$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

	# return results
		return $_out;
}


# Do select list for: Products (for Invoices and Orders Editors- Admin only so show all)
function do_select_list_products($aname, $avalue) {
	# Get security vars
		$_SEC = get_security_flags ();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT prod_id, prod_name, prod_desc';
		$query	.= ' FROM '.$_DBCFG['products'];
		$query	.= ' ORDER BY prod_name ASC';

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build Form row
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;

	# Process query results
		while(list($prod_id, $prod_name, $prod_desc) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$prod_id.'"';
			IF ($prod_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$prod_name.' - '.$prod_desc.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;
		return $_out;
}


# Do select list for: Server Info
function do_select_list_server_info($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	= 'SELECT si_id, si_name, si_ip, si_cp_url, si_cp_url_port FROM '.$_DBCFG['server_info'].' ORDER BY si_name ASC';
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BASE']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($si_id, $si_name, $si_ip, $si_cp_url, $si_cp_url_port) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$si_id.'"';
			IF ($si_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$si_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do return string from value for: Server Info
function do_valtostr_server_info($avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT si_id, si_name';
		$query	.= ' FROM '.$_DBCFG['server_info'];
		$query	.= ' WHERE si_id='.$avalue;

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Process query results
		while(list($si_id, $si_name) = $db_coin->db_fetch_row($result)) {$_out = $si_id.'- '.$si_name;}
		return $_out;
}


# Do select list for: SiteInfo
function do_select_list_siteinfo($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT si_id, si_group, si_name, si_desc, si_title';
		$query	.= ' FROM '.$_DBCFG['site_info'].' ORDER BY si_group ASC, si_name ASC';

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Build form output
		$_out .= '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_BASE']['Please_Select'].'</option>'.$_nl;

	# Process query results
		while(list($si_id, $si_group, $si_name, $si_desc, $si_title) = $db_coin->db_fetch_row($result)) {
			$_out .= '<option value="'.$si_id.'"';
			IF ($si_id == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$si_group.' - '.$si_name.'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do return string from value for: SiteInfo Page
function do_valtostr_siteinfo($avalue) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Set Query for select.
		$query	 = 'SELECT si_id, si_name';
		$query	.= ' FROM '.$_DBCFG['site_info'];
		$query	.= ' WHERE si_id='.$avalue;

	# Do select and return check
		$result	= $db_coin->db_query_execute($query);

	# Process query results
		while(list($si_id, $si_name) = $db_coin->db_fetch_row($result)) {$_out = $si_id.'- '.$si_name;}
		return $_out;
}

# Do item in use check for: Vendor ID
function do_inuse_vendor_id($avendorid) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_in_use_count = 0;

	# Set Query for select and select in orders
		$query	= 'SELECT ord_id FROM '.$_DBCFG['orders'].' WHERE ord_vendor_id='.$avendorid;
		$result	= $db_coin->db_query_execute($query);
		$_in_use_count	+= $db_coin->db_query_numrows($result);

	# Set Query for select and select in vendors products table
		$query	= 'SELECT vprod_id FROM '.$_DBCFG['vendors_prods'].' WHERE vprod_vendor_id='.$avendorid;
		$result	= $db_coin->db_query_execute($query);
		$_in_use_count	+= $db_coin->db_query_numrows($result);

		return $_in_use_count;
}


# Do item in use check for: Product ID
function do_inuse_prod_id($aprodid) {
	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;
		$_in_use_count = 0;

	# Set Query for select and select in orders
		$query	= 'SELECT ord_id FROM '.$_DBCFG['orders'].' WHERE ord_prod_id='.$aprodid;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);
		$_in_use_count	= $_in_use_count + $numrows;

	# Set Query for select and select in vendors products table
		$query	= 'SELECT vprod_id FROM '.$_DBCFG['vendors_prods'].' WHERE vprod_prod_id='.$aprodid;
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);
		$_in_use_count	= $_in_use_count + $numrows;

		return $_in_use_count;
}

// Displays a login form as a "menu box" item
function do_Display_Login_Menu_Form() {
	# Dim some Vars
		global $_TCFG, $_CCFG, $_LANG;
		$_SEC = get_security_flags();

	# If not logged in, draw form
		IF (!$_SEC['_sadmin_flg'] && !$_SEC['_suser_flg']) {
			$loginbutton = eregi_replace('<img ','',$_TCFG['_IMG_MT_LOGIN_B']);
			$loginbutton = eregi_replace(' align="middle"','',$loginbutton);
			$loginform  = '<form action="'.PKG_URL_BASE.'coin_includes/session_user.php" method="post" name="login">';
			$loginform .= $_LANG['_BASE']['l_User_Name'] . '<br>';
			$loginform .= '<input class="PMED_NL" type="text" name="username" size="20" maxlength="'.$_CCFG['CLIENT_MAX_LEN_UNAME'].'">';
			$loginform .= '<br>' . $_LANG['_BASE']['l_Password'] . '<br>';
			$loginform .= '<input class="PMED_NL" type="password" name="password" size="20" maxlength="'.$_CCFG['CLIENT_MAX_LEN_PWORD'].'">';
			$loginform .= '<br>' . $_LANG['_BASE']['Forgot_your_password'] . '<br>' . $_LANG['_BASE']['Click'] . ' ';
			$loginform .= '<a href="'.PKG_URL_BASE.'mod.php?mod=mail&mode=reset&w=user">' . $_LANG['_BASE']['here'] . '</a> ' . $_LANG['_BASE']['for reset'] . '<br><br>';
			$loginform .= '<input type="hidden" name="mod" value="">';
			$loginform .= '<input type="hidden" name="mode" value="">';
			$loginform .= '&nbsp;&nbsp;&nbsp;<input type="image" ' . $loginbutton;
			$loginform .= '</form>';
		} ELSE {

		# display "logged in " message
			$loginform  = $_LANG['_BASE']['Welcome_Back'];
			$loginform .= '<br><br>' . $_LANG['_BASE']['Logout_When_Done'] . '.<br><br>';
			$loginform .= '&nbsp;&nbsp;&nbsp;<a href="'.PKG_URL_BASE.'login.php?w=';
			IF ($_SEC['_sadmin_flg']) {$loginform .= 'admin';} ELSE {$loginform .= 'user';}
			$loginform .= '&amp;o=logout">'.$_TCFG['_IMG_MT_LOGOUT_B'].'</a><br><br>';
		}
		return $loginform;
}


# Do helpdesk support ticket priority select list
function do_select_list_priority($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_nl;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_HDESK']['Select_Priority'].'</option>'.$_nl;

	# Loop array and load list
		FOR ($i = 1; $i <= count($_CCFG['HD_TT_PRIORITY']); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_CCFG['HD_TT_PRIORITY'][$i]).'"';
			IF ($_CCFG['HD_TT_PRIORITY'][$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_CCFG['HD_TT_PRIORITY'][$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do helpdesk support ticket category select list
function do_select_list_category($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build Form row
		$_out = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_HDESK']['Select_Category'].'</option>'.$_nl;

	# Load config array and sort
		$_tmp_array = $_CCFG['HD_TT_CATEGORY'];
		sort($_tmp_array);

	# Loop array and load list
		FOR ($i = 0; $i < count($_tmp_array); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_tmp_array[$i]).'"';
			IF ($_tmp_array[$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_tmp_array[$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

	IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do helpdesk support ticket status select list
function do_select_list_status($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Build Form row
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0">'.$_LANG['_HDESK']['Select_Status'].'</option>'.$_nl;

	# Load config array and sort
		$_tmp_array = $_CCFG['HD_TT_STATUS'];
		sort($_tmp_array);

	# Loop array and load list
		FOR ($i = 0; $i < count($_tmp_array); $i++) {
			$_out .= '<option value="'.htmlspecialchars($_tmp_array[$i]).'"';
			IF ($_tmp_array[$i] == $avalue) {$_out .= ' selected';}
			$_out .= '>'.$_tmp_array[$i].'</option>'.$_nl;
		}

		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do return string from value for: Open or Closed Options
function do_valtostr_open_closed($avalue) {
	# Dim some Vars:
		global $_LANG;

	# Build form output
		IF ($avalue == 1) {
			return $_LANG['_HDESK']['Status_Closed'];
		} ELSE {
			return $_LANG['_HDESK']['Status_Open'];
		}
}


# Do list select field for: Open or Closed Options
function do_select_list_open_closed($aname, $avalue, $aret_flag=0) {
	# Dim some Vars:
		global $_LANG;

	# Build form output
		$_out  = '<select class="select_form" name="'.$aname.'" size="1" value="'.$avalue.'">'.$_nl;
		$_out .= '<option value="0"';
		IF ($avalue == 0) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_HDESK']['Select_Open'].'</option>'.$_nl;
		$_out .= '<option value="1"';
		IF ($avalue == 1) {$_out .= ' selected';}
		$_out .= '>'.$_LANG['_HDESK']['Select_Closed'].'</option>'.$_nl;
		$_out .= '</select>'.$_nl;

		IF ($aret_flag) {return $_out;} ELSE {echo $_out;}
}


# Do domain activation email (build, set email))
function do_mail_domain($adata, $aret_flag=0) {
	# Dim some vars
		global $_CCFG, $_TCFG, $_DBCFG, $db_coin, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Get security vars
		$_SEC	= get_security_flags();
		$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);

	# Set MTP array equal to data array
		$_MTP = $adata;

	# Do cross-table select for key fields
	# Set Query for select.
		$query	 = 'SELECT *';
		$query	.= ' FROM '.$_DBCFG['domains'].', '.$_DBCFG['server_info'].', '.$_DBCFG['clients'];
		$query	.= ' WHERE '.$_DBCFG['domains'].'.dom_si_id='.$_DBCFG['server_info'].'.si_id';
		$query	.= ' AND '.$_DBCFG['domains'].'.dom_cl_id='.$_DBCFG['clients'].'.cl_id';
		$query	.= ' AND '.$_DBCFG['domains'].'.dom_id='.$adata['dom_id'];

	# Do select
		$result	= $db_coin->db_query_execute($query);
		$numrows	= $db_coin->db_query_numrows($result);

	# Check Return and process results
		IF ($numrows) {
		# Process query results
			while ($row = $db_coin->db_fetch_array($result)) {
			# Rebuild Data Array with returned record: Server Account Fields
				$_MTP						= $row;
				$_MTP['dom_id']				= $row['dom_id'];
				$_MTP['dom_domain']				= $row['dom_domain'];
				$_MTP['dom_notes']				= $row['dom_notes'];
				$_MTP['dom_status']				= $row['dom_status'];
				$_MTP['dom_type']				= $row['dom_type'];
				$_MTP['dom_cl_id']				= $row['dom_cl_id'];
				$_MTP['dom_registar']			= $row['dom_registar'];
				$_MTP['dom_ts_expiration']		= dt_make_datetime($row['dom_ts_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				$_MTP['dom_sa_expiration']		= dt_make_datetime($row['dom_sa_expiration'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DT']);
				$_MTP['dom_si_id']				= $row['dom_si_id'];
				$_MTP['dom_ip']				= $row['dom_ip'];
				$_MTP['dom_path']				= $row['dom_path'];
				$_MTP['dom_path_temp']			= $row['dom_path_temp'];
				$_MTP['dom_url_cp']				= $row['dom_url_cp'];
				$_MTP['dom_user_name_cp']		= $row['dom_user_name_cp'];
				$_MTP['dom_user_pword_cp']		= $row['dom_user_pword_cp'];
				$_MTP['dom_user_name_ftp']		= $row['dom_user_name_ftp'];
				$_MTP['dom_user_pword_ftp']		= $row['dom_user_pword_ftp'];
				$_MTP['dom_allow_domains']		= $row['dom_allow_domains'];
				$_MTP['dom_allow_subdomains']		= $row['dom_allow_subdomains'];
				$_MTP['dom_allow_disk_space_mb']	= $row['dom_allow_disk_space_mb'];
				$_MTP['dom_allow_traffic_mb']		= $row['dom_allow_traffic_mb'];
				$_MTP['dom_allow_mailboxes']		= $row['dom_allow_mailboxes'];
				$_MTP['dom_allow_databases']		= $row['dom_allow_databases'];
				$_MTP['dom_enable_www_prefix']	= do_valtostr_no_yes($row['dom_enable_www_prefix']);
				$_MTP['dom_enable_wu_scripting']	= do_valtostr_no_yes($row['dom_enable_wu_scripting']);
				$_MTP['dom_enable_webmail']		= do_valtostr_no_yes($row['dom_enable_webmail']);
				$_MTP['dom_enable_frontpage']		= do_valtostr_no_yes($row['dom_enable_frontpage']);
				$_MTP['dom_enable_fromtpage_ssl']	= do_valtostr_no_yes($row['dom_enable_fromtpage_ssl']);
				$_MTP['dom_enable_ssi']			= do_valtostr_no_yes($row['dom_enable_ssi']);
				$_MTP['dom_enable_php']			= do_valtostr_no_yes($row['dom_enable_php']);
				$_MTP['dom_enable_cgi']			= do_valtostr_no_yes($row['dom_enable_cgi']);
				$_MTP['dom_enable_mod_perl']		= do_valtostr_no_yes($row['dom_enable_mod_perl']);
				$_MTP['dom_enable_asp']			= do_valtostr_no_yes($row['dom_enable_asp']);
				$_MTP['dom_enable_ssl']			= do_valtostr_no_yes($row['dom_enable_ssl']);
				$_MTP['dom_enable_stats']		= do_valtostr_no_yes($row['dom_enable_stats']);
				$_MTP['dom_enable_err_docs']		= do_valtostr_no_yes($row['dom_enable_err_docs']);
			# Rebuild Data Array with returned record: Server Info Fields
				$_MTP['si_id']					= $row['si_id'];
				$_MTP['si_name']				= $row['si_name'];
				$_MTP['si_ip']					= $row['si_ip'];
				$_MTP['si_ns_01']				= $row['si_ns_01'];
				$_MTP['si_ns_01_ip']			= $row['si_ns_01_ip'];
				$_MTP['si_ns_02']				= $row['si_ns_02'];
				$_MTP['si_ns_02_ip']			= $row['si_ns_02_ip'];
				$_MTP['si_cp_url']				= $row['si_cp_url'];
				$_MTP['si_cp_url_port']			= $row['si_cp_url_port'];
			# Rebuild Data Array with returned record: Clients fields
				$_MTP['cl_company']				= $row['cl_company'];
				$_MTP['cl_name_first']			= $row['cl_name_first'];
				$_MTP['cl_name_last']			= $row['cl_name_last'];
				$_MTP['cl_email']				= $row['cl_email'];
				$_MTP['cl_user_name']			= $row['cl_user_name'];
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_01'].$row['cl_id'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_02'].dt_make_datetime($row['cl_join_ts'], $_CCFG['_PKG_DATE_FORMAT_SHORT_DTTM']).$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_03'].$row['cl_user_name'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_04'].$row['cl_email'].$_nl;
				$_MTP['cl_info'] .= '-------------------'.$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_05'].$row['cl_company'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_06'].$row['cl_name_first'].' '.$row['cl_name_last'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_07'].$row['cl_addr_01'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_08'].$row['cl_addr_02'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_09'].$row['cl_city'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_10'].$row['cl_state_prov'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_11'].$row['cl_country'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_12'].$row['cl_zip_code'].$_nl;
				$_MTP['cl_info'] .= $_LANG['_DOMS']['DOM_EMAIL_13'].$row['cl_phone'];
			}
		} # End Get Domain Record

	# Get contact information array
		$_cinfo = get_contact_info($_CCFG['MC_ID_SUPPORT']);

	# Set eMail Parameters (pre-eval template, some used in template)
		IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
    			$mail['recip']	= $_MTP['cl_email'];
			$mail['from']	= $_cinfo['c_email'];
		} ELSE {
			$mail['recip']	= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'].' <'.$_MTP['cl_email'].'>';
			$mail['from']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
		}
		IF ( $_CCFG['DOM_EMAIL_CC_ENABLE'] ) {
			IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
				$mail['cc']	= $_cinfo['c_email'];
			} ELSE {
				$mail['cc']	= $_CCFG['_PKG_NAME_SHORT'].'-'.$_cinfo['c_name'].' <'.$_cinfo['c_email'].'>';
			}
		} ELSE {
			$mail['cc']	= '';
		}
		$mail['subject']	= $_CCFG['_PKG_NAME_SHORT'].$_LANG['_DOMS']['DOM_EMAIL_SUBJECT'];

	# Set MTP (Mail Template Parameters) array
		$_MTP['to_name']	= $_MTP['cl_name_first'].' '.$_MTP['cl_name_last'];
		$_MTP['to_email']	= $_MTP['cl_email'];
		$_MTP['from_name']	= $_cinfo['c_name'];
		$_MTP['from_email']	= $_cinfo['c_email'];
		$_MTP['subject']	= $mail['subject'];
		$_MTP['site']		= $_CCFG['_PKG_NAME_SHORT'];
		$_MTP['cl_url']	= BASE_HREF.'mod.php?mod=clients&mode=view&cl_id='.$row['cl_id'];

	# Check returned records, don't send if not 1
		$_ret = 1;
		IF ($numrows == 1) {
		# Load message template (processed)
			$mail['message']	.= get_mail_template('email_domain_acc_activate', $_MTP);

		# Call basic email function (ret=1 on error)
			$_ret = do_mail_basic($mail);

		# Check return
			IF ($_ret) {
				$_ret_msg  = $_LANG['_DOMS']['DOM_EMAIL_MSG_02_L1'];
				$_ret_msg .= '<br>'.$_LANG['_DOMS']['DOM_EMAIL_MSG_02_L2'];
			} ELSE {
				$_ret_msg = $_LANG['_DOMS']['DOM_EMAIL_MSG_03_PRE'].$_sp.$adata['dom_id'].$_sp.$_LANG['_DOMS']['DOM_EMAIL_MSG_03_SUF'];
			}
		} ELSE {
			$_ret_msg = $_LANG['_DOMS']['DOM_EMAIL_MSG_01_PRE'].$_sp.$adata['dom_id'].$_sp.$_LANG['_DOMS']['DOM_EMAIL_MSG_01_SUF'];
		}

	# Build Title String, Content String, and Footer Menu String
		$_tstr = $_LANG['_DOMS']['DOM_EMAIL_RESULT_TITLE'];
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



/**
 * Determine if an image exists on the hard-drive
 *	- Do NOT pass image extension. For instance, passing 'phpcoin' means phpCOIN will check for the following, <i>in the order listed</i>:
 *	- phpcoin.png
 *	- phpcoin.jpg
 *	- phpcoin.jpeg
 *	- phpcoin.gif
 *	- phpcoin.tif
 *	- phpcoin.psd
 *	- phpcoin.psp
 *	- phpcoin.ico
 *	- phpcoin.bmp
 * @uses file_exists() to determine if particular filename exists
 * @param string $imagename Basename of image to look for
 * @param string $_basepath Where on hard-drive to look for image
 * @param string URL to image directory
 * @return string URL to image with found extension appended, or null (empty) string is nothing found
 */
function check_if_image_exists($imagename, $_basepath, $_baseurl) {
	$imageURL = '';
	IF (file_exists($_basepath.$imagename . '.png')) {
		$imageURL = $_baseurl.$imagename . '.png';
	} ELSEIF (file_exists($_basepath.$imagename . '.jpg')) {
		$imageURL = $_baseurl.$imagename . '.jpg';
	} ELSEIF (file_exists($_basepath.$imagename . '.jpeg')) {
		$imageURL = $_baseurl.$imagename . '.jpeg';
	} ELSEIF (file_exists($_basepath.$imagename . '.gif')) {
		$imageURL = $_baseurl.$imagename . '.gif';
	} ELSEIF (file_exists($_basepath.$imagename . '.tif')) {
		$imageURL = $_baseurl.$imagename . '.tif';
	} ELSEIF (file_exists($_basepath.$imagename . '.psd')) {
		$imageURL = $_baseurl.$imagename . '.psd';
	} ELSEIF (file_exists($_basepath.$imagename . '.psp')) {
		$imageURL = $_baseurl.$imagename . '.psp';
	} ELSEIF (file_exists($_basepath.$imagename . '.ico')) {
		$imageURL = $_baseurl.$imagename . '.ico';
	} ELSEIF (file_exists($_basepath.$imagename . '.bmp')) {
		$imageURL = $_baseurl.$imagename . '.bmp';
	}
	return $imageURL;
}


function display_phpcoin_updates() {
		global $_CCFG, $_PACKAGE, $_LANG, $ThisVersion, $ThisFix, $_sp;

	# Initialize some vars
		$_cstr	= '';
		$_mstr	= '';
		$_out	= '';

	# Check for updates to phpCOIN
		IF ($_CCFG['AUTOCHECK_UPDATES']) {
			$arItems	= array();
			$_content	= '';
			$Release	= 0;
			$Fixes	= 0;
			$uFile	= 'http://www.phpcoin.com/current.rss';

		# Grab and parse the .rss file
		# Revised rss code based on rss2array from http://www.rssmix.com/rss2array-parser/
			global $rss2array_globals;

			function rss2array($url) {
				global $rss2array_globals;
				$rss2array_globals = array();

				if (preg_match("/^http:\/\/([^\/]+)(.*)$/", $url, $matches)) {
					$host = $matches[1];
					$uri = $matches[2];
					$request = "GET $uri HTTP/1.0\r\n";
					$request .= "Host: $host\r\n";
					$request .= "User-Agent: phpCOIN\r\n";
					$request .= "Connection: close\r\n\r\n";
					if ($http = fsockopen($host, 80, $errno, $errstr, 5)) {
						fwrite($http, $request);
						$timeout = time() + 5;
						while(time() < $timeout && !feof($http)) {
							$response .= fgets($http, 4096);
						}
						list($header, $xml) = preg_split("/\r?\n\r?\n/", $response, 2);
						if (preg_match("/^HTTP\/[0-9\.]+\s+(\d+)\s+/", $header, $matches)) {
							$status = $matches[1];
							if ($status == 200) {
								$xml_parser = xml_parser_create();
								xml_set_element_handler($xml_parser, "startElement", "endElement");
								xml_set_character_data_handler($xml_parser, "characterData");
								xml_parse($xml_parser, trim($xml), true) or $rss2array_globals[errors][] = xml_error_string(xml_get_error_code($xml_parser)) . " at line " . xml_get_current_line_number($xml_parser);
								xml_parser_free($xml_parser);
							} else {
								$rss2array_globals['errors'][] = "Can't get feed: HTTP status code $status";
							}
						} else {
							$rss2array_globals['errors'][] = "Can't get status from header";
						}
					} else {
						$rss2array_globals['errors'][] = "Can't connect to $host";
					}
				} else {
					$rss2array_globals['errors'][] = "Invalid url: $url";
				}
				unset($rss2array_globals['channel_title']);
				unset($rss2array_globals['inside_rdf']);
				unset($rss2array_globals['inside_rss']);
				unset($rss2array_globals['inside_channel']);
				unset($rss2array_globals['inside_item']);
				unset($rss2array_globals['current_date']);
				unset($rss2array_globals['current_version']);
				unset($rss2array_globals['current_title']);
				unset($rss2array_globals['current_link']);
				unset($rss2array_globals['current_description']);
				return $rss2array_globals;
			}

			function startElement($parser, $name, $attrs){
				global $rss2array_globals;
				$rss2array_globals['current_tag'] = $name;
				if ($name == 'RSS') {
					$rss2array_globals['inside_rss'] = true;
				} elseif ($name == 'RDF:RDF') {
					$rss2array_globals['inside_rdf'] = true;
				} elseif ($name == 'CHANNEL') {
					$rss2array_globals['inside_channel'] = true;
					$rss2array_globals['channel_title'] = '';
				} elseif(($rss2array_globals['inside_rss'] and $rss2array_globals['inside_channel']) or $rss2array_globals['inside_rdf']) {
					if ($name == 'ITEM') {
						$rss2array_globals['inside_item'] = true;
					} elseif ($name == 'IMAGE') {
						$rss2array_globals['inside_image'] = true;
					}
				}
			}

			function characterData($parser, $data){
				global $rss2array_globals;
				if ($rss2array_globals['inside_item']) {
					switch($rss2array_globals[current_tag]){
						case "TITLE":
							$rss2array_globals['current_title'] .= $data;
							break;
						case "DESCRIPTION":
							$rss2array_globals['current_description'] .= $data;
							break;
						case "LINK":
							$rss2array_globals['current_link'] .= $data;
							break;
						case "DATE":
							$rss2array_globals['current_date'] .= $data;
							break;
						case "VERSION":
							$rss2array_globals['current_version'] .= $data;
							break;
					}
				} elseif($rss2array_globals['inside_image']) {
				} elseif($rss2array_globals['inside_channel']) {
					switch($rss2array_globals['current_tag']){
						case "TITLE":
							$rss2array_globals['channel_title'] .= $data;
							break;
					}
				}
			}

			function endElement($parser, $name){
				global $rss2array_globals;
				if ($name == 'ITEM') {
					$rss2array_globals['items'][] = array(title => trim($rss2array_globals['current_title']), link => trim($rss2array_globals['current_link']), date => trim($rss2array_globals['current_date']), version => trim($rss2array_globals['current_version']), description => trim($rss2array_globals['current_description']));
					$rss2array_globals['current_title'] = '';
					$rss2array_globals['current_description'] = '';
					$rss2array_globals['current_link'] = '';
					$rss2array_globals['current_date'] = '';
					$rss2array_globals['current_version'] = '';
					$rss2array_globals['inside_item'] = false;
				} elseif ($name == 'RSS') {
					$rss2array_globals['inside_rss'] = false;
				} elseif ($name == 'RDF:RDF') {
					$rss2array_globals[inside_rdf] = false;
				} elseif ($name == 'CHANNEL') {
					$rss2array_globals['channel']['title'] = trim($rss2array_globals['channel_title']);
					$rss2array_globals['inside_channel'] = false;
				} elseif ($name == 'IMAGE') {
					$rss2array_globals['inside_image'] = false;
				}
			}

			$arItems = rss2array($uFile);
			IF (!$arItems['errors']) {
				$_todo = count($arItems['items']);

				FOR ($i=0; $i<$_todo; $i++) {
					$txItem = $arItems['items'][$i];

					IF ($txItem['title'] == "Release") {
						IF ($ThisVersion < $txItem['version']) {
							$_content .= 'phpCOIN ';
							$_content .= '<a href = "'.$txItem['link'].'">v'.$txItem['version'].'</a> was released '.$txItem['date'].'<br>';
							$txItem['description'] = str_replace("|",'</li><li>',$txItem['description']);
							$_content .= '<ul><li>'. $txItem['description'].'</li></ul>';
							$_content .='<br><br>';
							$Release++;
						}
					}

					IF ($txItem['title'] == "Fix-File") {
						IF (($ThisFix < $txItem['date']) && ($ThisVersion == $txItem['version'])) {
							$_content .= $txItem['title'].' ';
							$_content .= '<a href = "'.$txItem['link'].'">'.$txItem['date'].'</a> for version '.$txItem['version'].'<br>';
							$txItem['description'] = str_replace("|",'</li><li>',$txItem['description']);
							$_content .= '<ul><li>'. $txItem['description'].'</li></ul>';
							$Fixes++;
						}
					}
				}
			} ELSE {
				$_content = $_LANG['_ADMIN']['UPDATE_UNAVAILABLE'].':<br>';
				$_todo = count($arItems['errors']);
				FOR ($i=0; $i<$_todo; $i++) {
					$_content .= $_sp.$_sp.$arItems['errors'][$i].'<br>';
				}
			}
			IF (!$_content) {$_content = $_LANG['_ADMIN']['UPDATE_NONE'];}


		# Display output
			IF ($Fixes > 1)		{$_content .= $_LANG['_ADMIN']['UPDATE_MANY'].' v'.$ThisVersion;}
			IF ($Release && $Fixes)	{$_content .= '<br>'.$_LANG['_ADMIN']['UPDATE_NEW'].' v'.$ThisVersion;}

		# Build Title String, Content String, and Footer Menu String
			$_tstr  = $_LANG['_ADMIN']['UPDATE_TITLE'];

			$_cstr  = '<div align="center" valign="top" height="100%">'.$_nl;
			$_cstr .= '<table width="90%" cellspacing="5">'.$_nl;
			$_cstr .= '<tr><td align="left" valign="top">'.$_nl;
			$_cstr .= $_LANG['_ADMIN']['UPDATE_VERSION'].' v'.$ThisVersion.' ';
			IF ($Fixes) {$_cstr .= $_LANG['_ADMIN']['UPDATE_FIX'].' '.$ThisFix.'<br>';}
			$_cstr .= '<br>'.$_content;
			$_cstr .= '</td></tr>'.$_nl;
			$_cstr .= '</table>'.$_nl;
			$_cstr .= '</div>'.$_nl;
			$_mstr_flag	= 0;
			$_mstr  = '';

		# Call block it function
			$_out  = do_mod_block_it($_tstr, $_cstr, $_mstr_flag, $_mstr, '1').'<br>'.$_nl;
		}

	# Return final output
		return $_out;
}


/**
 * Checks an URL and prepends "http://" if it does not exist and appends "/" if it does not exist
 * @param string $target URL to be checked
 * @return string checked (and corrected if necessary) URL
 */
function make_valid_link($target) {
	$rest = substr($target, 0, 4);
	IF ($rest != 'http') {$dest = 'http://' . $target;} ELSE {$dest = $target;}
	$_tx = substr($dest, -1, 1);
	IF ($_tx != '/') {$dest .= '/';}
	return $dest;
}


function do_autopassword_button($apw, $acpw, $_td_str_left, $_td_str_right) {
	global $_LANG, $_nl;
	$_autopass = do_password_create();
	$_out  = '<tr>'.$_nl;
	$_out .= $_td_str_left.'</td>'.$_nl;
	$_out .= $_td_str_right.$_nl;
	$_out .= '<script type="text/javascript">'.$_nl;
	$_out .= 'function set_values() {'.$_nl;
	$_out .= "	document.getElementById('b1').style.display = 'none';".$_nl;
	$_out .= "	document.getElementById('b2').style.display = 'block';".$_nl;
	$_out .= "	document.cl_info.".$apw.".value='".$_autopass."';".$_nl;
	$_out .= "	document.cl_info.".$acpw.".value='".$_autopass."';".$_nl;
	$_out .= '}'.$_nl;
	$_out .= "document.write('<div id=\"b1\" style=\"display: block;\"><input type=\"button\" name=\"gp\" value=\"".$_LANG['_BASE']['AUTOPASSWORD_BUTTON_TEXT']."\" onclick=\"set_values()\"></div>');".$_nl;
	$_out .= "document.write('<div id=\"b2\" style=\"display: none;\">".$_LANG['_BASE']['AUTOPASSWORD_BUTTON_REMEMBER'].": ".htmlentities($_autopass)."</div>');".$_nl;
	$_out .= '</script>';
	$_out .= '</tr>'.$_nl;
	$_out .= '<tr>'.$_nl;
	return $_out;
}

?>