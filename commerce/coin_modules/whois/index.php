<?php
/**
 * Module: WHOIS (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- whois lookups based loosely on mrwhois Copyright (C) 2001 Marek Rozanski, marek@mrscripts.co.uk
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage whois
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_whois.php
 */


# Check file loaded through modules call
	IF (eregi('index.php', $_SERVER["PHP_SELF"])) {
		require('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=whois');
		exit();
	}

# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_whois.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_whois_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_whois_override.php');
	}


# Define some look and misc parameters
	$poweredby  = "WHOIS lookup code based on <a href=http://www.mrscripts.co.uk target=_blank><b>MRWhois</b></a>"; // DO NOT CHANGE
	$backgcol   = '#9AC0CD';						// general background color
	$fontacolor = 'green';							// color of an available domain
	$fontucolor = 'red';							// color when not available
	$infolinks  = 'black';							// color of additional links
	$sepcolor   = '#cccccc';						// separator color
	$stdcolor   = 'black';							// header and footer font color
	$errcolor   = 'red';							// color of error messages
# End of variables, you do not need to change anything below this line.

# Support free vs paid products
	IF ($_GPV['free'] == 1) {$_CCFG['_FREETRIAL'] = 1;} ELSE {$_CCFG['_FREETRIAL'] = 0;}

# Register / Order Link Configuration
	$_link 	= $_CCFG['WHOIS_LINK'];
	switch($_link) {
		case 0:
			# Do NOT add an [Order] or [Register] link
			$regurl	= '';
			$regtext	= '';
			break;
		case 1:
			/*
			Go to "orders" page, passing in domain name and setting "New Domain" to "Yes"
			If you want the customer to be able to click on an [Order] button
			for available domains. May need to edit $regurl.
			*/
			$regurl	= 'mod.php?mod=orders';
			IF ($_CCFG['_FREETRIAL'])	{$regurl .= '&free=1';}
			IF ($_GPV['ord_accept_tos'])	{$regurl .= '&ord_accept_tos='.$_GPV['ord_accept_tos'];}
			IF ($_GPV['ord_accept_aup'])	{$regurl .= '&ord_accept_aup='.$_GPV['ord_accept_aup'];}
			IF ($_GPV['ord_referred_by'])	{$regurl .= '&ord_referred_by='.$_GPV['ord_referred_by'];}
			IF ($_GPV['ord_vendor_id'])	{$regurl .= '&ord_vendor_id='.$_GPV['ord_vendor_id'];}
			IF ($_GPV['stage'])			{$regurl .= '&stage='.$_GPV['stage'];}
			IF ($_GPV['b_continue'])		{$regurl .= '&b_continue='.$_GPV['b_continue'];}
			$regtext	= $_LANG['_WHOIS']['Link_Order'];
			break;
		case 2:
			/*
			Go to your affiliiate link to register the domain.
			This option will open a new browser window to the registrar, and the
			original browser window will remain on this page. May need to edit $regurl.
			*/
			$regurl	= $_CCFG['WHOIS_AFFILIATE_LINK'];
			$regtext	= $_LANG['_WHOIS']['Link_Register'];
			break;
		default:
			# Do NOT add an [Order] or [Register] link
			$regurl	= '';
			$regtext	= '';
			break;
		}

# Do select and return check
	$query	= 'SELECT * FROM '.$_DBCFG['whois'].' WHERE whois_include=1 ORDER BY whois_display ASC';
	$result	= $db_coin->db_query_execute($query);
	$numrows	= $db_coin->db_query_numrows($result);

# Process query results
	$xx=0;
	while(list($whois_id, $whois_server, $whois_nomatch, $whois_value, $whois_display, $whois_include, $whois_prod_id, $whois_notes) = $db_coin->db_fetch_row($result)) {
		$Lookup_Domain['server'][$xx]		= $whois_server;	// server to lookup for domain name
		$Lookup_Domain['nomatch'][$xx]	= $whois_nomatch;	// string returned by server if the domain is not found
		$Lookup_Domain['value'][$xx]		= $whois_value;	// string value for this domain extension (do not change)
		$Lookup_Domain['display'][$xx]	= $whois_display;	// string value for this domain to display on form
		$Lookup_Domain['include'][$xx]	= $whois_include;	// include this domain in lookup
		$Lookup_Domain['prod_id'][$xx]	= $whois_prod_id;	// over-ride product ordered with this id
		$xx++;                                              	// Increment counter
	}
	$Lookups = $xx;



# This function displays an available domain ($what), adding [Order] or [Register] links if configured.
function dispav($what, $prodid) {
	global $fontacolor, $infolinks, $regurl, $regtext, $_nl;
	$where = str_replace('%DOMAIN_NAME%', $what, $regurl);
	$_out  = '<tr>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap>'.$_nl;
	IF ($regurl <> '') {
		# Check if regurl is linked to phpCOIN orders page (if no, open new window)
		$theURL = strpos($where, 'mod.php');
		# Set link
		IF ($theURL === false) {
			# Open a new browser window to the registrar.
			$_out .= '<a href="'.$where.'" target="_blank" onMouseOver="window.status=\'Register '.$what.'\';return true" onMouseOut="window.status=\'\';return true">';
		} ELSE {
			# Send current browser window to order form, passing in domain name.
			$_out .= '<a href="'.$where.'&ord_domain='.$what.'&ord_prod_id='.$prodid.'"'.'onMouseOver="window.status=\'Order '.$what.'\';return true"onMouseOut="window.status=\'\';return true">';
		}
		$_out .= '<font color='.$infolinks.'>'.$regtext.'</font></a>'.$_nl;
	} ELSE {
		$_out .= '&nbsp;';
	}
	$_out .= '</td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap><font color="'.$fontacolor.'"><b>'.$what.'</b></font></td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" colspan="3">&nbsp;</td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	return $_out;
}


# Function to display an unavailable domain ($what) via server ($where), with [details] and [goto] links
function dispun($what, $where) {
	global $fontucolor, $infolinks, $_nl, $_LANG, $_CCFG;
	$where = str_replace('%DOMAIN_NAME%', $what, $where);
	$_out  = '<tr><td class="TP3MED_BC" colspan="2">&nbsp;</td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap>'.$_nl;
	$_out .= '<font color="'.$fontucolor.'"><b>'.$what.'</b></font></td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap>'.$_nl;
	IF (!$_CCFG['WHOIS_DETAILS_NEW']) {
		# Set to open details in current window
		$_out .= '<a href="mod.php?mod=whois&action=details&ord_domain='.$what.'&server='.$where.'" onMouseOver="window.status=\'Details about '.$what.'\';return true" onMouseOut="window.status=\'\';return true">';
		$_out .= ' <font color="'.$infolinks.'">'.$_LANG['_WHOIS']['Link_Details'].'</font></a>'.$_nl;
	} ELSE {
		# Set to open details in new window
		$theURL	 = 'coin_modules/whois/details.php?ord_domain='.$what.'&server='.$where;
		$_out	.= '<a href="'.htmlentities($theURL)."\" target=\"_new\" onclick=\"window.open('$theURL','Domain Details','toolbar=no,directories=no,location=no,status=no,menubar=no,resizable=yes,scrollbars=yes,width=650,height=500');return false;\">";
		$_out	.= ' <font color="'.$infolinks.'">'.$_LANG['_WHOIS']['Link_Details'].'</font></a>'.$_nl;
	}
	$_out .= '</td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap><a href="http://www.'.$what.'" target="_blank"><font color="'.$infolinks.'">'.$_LANG['_WHOIS']['Link_Goto'].'</font></a></td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	return $_out;
}


# Function to display an error
function disperror($text) {
	global $errcolor, $_nl;
	$_out  = '<table width=80%><tr><td class="TP3MED_BC">'.$_nl;
	$_out .= '<font color="'.$errcolor.'"><b>'.$text.'</b></font>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	return $_out;
}


# Function to display main lookup form
function main() {
	global $stdcolor, $_nl, $_sp, $_LANG, $_CCFG, $_GPV;
	global $Lookups, $Lookup_Domain;

	IF ($_GPV['type'] == '') {$_GPV['type'] = 'com';}
	$_out  = '<table width="80%"><tr><td>'.$_nl;
	$_out .= '<form method="post" action="mod.php">'.$_nl;
	$_out .= '<input type="hidden" name="mod" value="whois">'.$_nl;
	IF ($_GPV['ord_accept_tos'])	{$_out .= '<input type="hidden" name="ord_accept_tos" value="'.$_GPV['ord_accept_tos'].'">'.$_nl;}
	IF ($_GPV['ord_accept_aup'])	{$_out .= '<input type="hidden" name="ord_accept_aup" value="'.$_GPV['ord_accept_aup'].'">'.$_nl;}
	IF ($_GPV['ord_referred_by'])	{$_out .= '<input type="hidden" name="ord_referred_by" value="'.$_GPV['ord_referred_by'].'">'.$_nl;}
	IF ($_GPV['ord_vendor_id'])	{$_out .= '<input type="hidden" name="ord_vendor_id" value="'.$_GPV['ord_vendor_id'].'">'.$_nl;}
	IF ($_GPV['ord_prod_id'])	{$_out .= '<input type="hidden" name="ord_prod_id" value="'.$_GPV['ord_prod_id'].'">'.$_nl;}
	IF ($_GPV['stage'])			{$_out .= '<input type="hidden" name="stage" value="'.$_GPV['stage'].'">'.$_nl;}
	IF ($_GPV['b_continue'])		{$_out .= '<input type="hidden" name="b_continue" value="'.$_GPV['b_continue'].'">'.$_nl;}
	$_out .= '<table width="100%" align="center" cellspacing="0" cellpadding="8" border="0">'.$_nl;

	$_out .= '<tr>'.$_nl;
	$_out .= '<td class="TP3MED_NC" colspan="2" align="center" width="100%">'.$_nl;
	IF ($_CCFG['WHOIS_INSTRUCTIONS_SHORT']) {
		$_out .= $_LANG['_WHOIS']['Text_Instructions_Short'].$_nl;
	} ELSE {
		$_out .= $_LANG['_WHOIS']['Text_Instructions_Long'].$_nl;
	}
	$_out .= '</td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	$_out .= '<tr>'.$_nl.'<td class="TP3MED_BC" colspan="2" width="100%">'.$_sp.'</td>'.$_nl.'</tr>'.$_nl;

	$_out .= '<tr>'.$_nl.'<td class="TP3MED_BR" >'.$_LANG['_WHOIS']['Title_Domain'].$_sp.'</td>'.$_nl;
	$_out .= '<td class="TP3MED_BL">'.$_sp.$_LANG['_WHOIS']['Title_Extension'].'</td></tr>'.$_nl;
	$_out .= '<tr>'.$_nl;
	$_out .= '<td class="TP3MED_NR" valign="middle">'.$_nl;
	IF ($_CCFG['_FREETRIAL']) {$_out .= '<input class="PSML_NL" type="hidden" id="free" name="free" value="1">'.$_nl;}
	$_out .= '<input class="PSML_NL" type="hidden" name="action" value="checkdom">'.$_nl;
	$_out .= '<input class="PSML_NL" type="hidden" name="ord_referred_by" value="'.$_GPV['ord_referred_by'].'">'.$_nl;
	$_out .= '<input class="PSML_NL" type="hidden" name="ord_prod_id" value="'.$_GPV['ord_prod_id'].'">'.$_nl;
	$_out .= '<input class="PSML_NL" type="text" name="ord_domain" size="25" value="'.$_GPV['ord_domain'].'" maxlength="58">&nbsp;'.$_nl;
	$_out .= '</td><td class="TP3MED_NL" valign="middle">'.$_nl;
	IF ($_CCFG['WHOIS_EXT_LIST']) {
		# Build extensions as select list
		$_out .= $_sp.$_sp.'<select class="PMED_NL" name="type" size="1" value="'.$_GPV['type'].'">'.$_nl;
		# Loop through for domains list
		FOR ($i=0; $i <= $Lookups; $i++) {
			IF ( $Lookup_Domain['include'][$i] == true ) {
				$_cnt++;
				$_out .= '<option value="'.$Lookup_Domain['value'][$i].'"';
				IF ($_GPV['type'] == $Lookup_Domain['value'][$i]) { $_out .= ' selected'; }
				$_out .= '>'.$_sp.$_sp.$Lookup_Domain['display'][$i] .'</option>'. $_nl;
			}
		}
		IF ( $_cnt > 0 ) {
			$_cnt = 0;
			$_out .= '<option value="all"';
			IF ($_GPV['type'] == 'all') {$_out .= ' selected';}
			$_out .= '>'.$_sp.$_sp.$_LANG['_WHOIS']['Option_All_Domains'].'</option>'. $_nl;
		}
		$_out .= '</select>'.$_nl;
	} ELSE {
		# Build extensions as radio buttons list
		$_out .= '<p class="PMED_NL_ID">'.$_nl;
		# Loop through for domains list
		FOR ($i=0; $i <= $Lookups; $i++) {
			IF ($Lookup_Domain['include'][$i] == true) {
				$_cnt++;
				$_out .= '<INPUT TYPE="radio" ';
				IF ($_GPV['type'] == $Lookup_Domain['value'][$i]) {$_out .= 'CHECKED ';}
				$_out .= 'NAME="type" VALUE="'.$Lookup_Domain['value'][$i].'">';
				$_out .= '<font color="'.$stdcolor.'"> '.$Lookup_Domain['display'][$i].'</font><br>'.$_nl;
			}
		}
		IF ($_cnt > 0) {
			$_cnt = 0;
			$_out .= '<INPUT TYPE="radio" ';
			IF ($_GPV['type'] == "all") {$_out .= 'CHECKED ';}
			$_out .= 'NAME="type" VALUE="all">';
			$_out .= '<font color="'.$stdcolor.'"> '.$_LANG['_WHOIS']['Option_All_Domains'].'</font><br>';
		}
		$_out .= '</p>'.$_nl;
	} // showlist
	$_out .= '</td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	$_out .= '<tr>'.$_nl.'<td class="TP3MED_BC" colspan="2" align="center" width="100%">'.$_sp.'</td>'.$_nl.'</tr>'.$_nl;
	$_out .= '<tr><td class="TP3MED_BC" colspan="2"><input class="PSML_NC" type="submit" name="button" value="'.$_LANG['_WHOIS']['B_Check'].'"></td></tr>'.$_nl;
	$_out .= '<tr>'.$_nl.'<td class="TP3MED_BC" colspan="2" align="center" width="100%">'.$_sp.'</td>'.$_nl.'</tr>'.$_nl;
	$_out .= '</table>'.$_nl;
	$_out .= '</form>'.$_nl;
	$_out .= '</td></tr></table>'.$_nl;
	return $_out;
}


function dispdi($what) {
	global $fontucolor, $_nl;
	$_out  = '<tr><td class="TP3MED_BC" colspan="2">&nbsp;</td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap>'.$_nl;
	$_out .= '<font color="'.$fontucolor.'"><b>'.$what.'</b></font></td>'.$_nl;
	$_out .= '<td class="TP3MED_BC" nowrap colspan="2">'.'Unable to check: allow_url_fopen is disabled'.'</td>'.$_nl;
	$_out .= '</tr>'.$_nl;
	return $_out;
}




/**
 * Perform a HTTP Get Request.
 * ffl_HttpGet uses fsockopen() to request a given URL via HTTP
 * 1.0 GET and returns a three element array.  On success, array
 * key 'body' contains the body of the request's reply and key
 * 'header' contains the reply's headers.  On error, the keys
 * returned are 'errornumber' and 'errorstring' from
 * fsockopen()'s third and fourth arguments.  In either case,
 * key 'url' contains an array such as returned from parse_url()
 * after the input url has been massaged a bit.
 * @param string $url URL to fetch.
 * @param boolean $followRedirects Optionally follow
 * 'location:' in header, default true.
 * @return array 'header', 'body', 'url' OR 'errorstring', 'errornumber', 'url'
 */
function ffl_HttpGet($url, $followRedirects=false) {
	$url_parsed						= parse_url($url);
	IF (empty($url_parsed['scheme'])) 		{$url_parsed = parse_url('http://'.$url);}
	$rtn['url']						= $url_parsed;

	$port							= $url_parsed['port'];
	IF (!$port)						{$port = 80;}
	IF ($url_parsed['scheme'] == 'https')	{$port = 443;}

	$rtn['url']['port']					= $port;

	$path							= $url_parsed["path"];
	if (empty($path))					{$path = '/';}
	if (!empty($url_parsed["query"]))		{$path .= '?'.$url_parsed['query'];}
	$rtn['url']['path']					= $path;
	$host							= $url_parsed['host'];
	$foundBody						= false;
	$out								= "GET $path HTTP/1.1\r\n";
	$out								.= "Host: $host\r\n";
	$out								.= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
	$out								.= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
	$out								.= "Connection: keep-alive\r\n";
	$out								.= "\r\n";
	if (!$fp = @fsockopen($host, $port, $errno, $errstr, 10)) {
		$rtn['errornumber'] = $errno;
		$rtn['errorstring'] = $errstr;
		return $rtn;
	}
	fwrite($fp, $out);
	while (!feof($fp)) {
		$s = fgets($fp, 128);
		if ($s == "\r\n") {
			$foundBody = true;
			continue;
		}
		if ($foundBody) {
			$body .= $s;
		} else {
			if (($followRedirects) && (stristr($s, 'location:') != false)) {
				$redirect = preg_replace("/location:/i", '', $s);
				return ffl_HttpGet(trim($redirect));
			}
			$header .= $s;
		}
	}
	fclose($fp);
	$rtn['header']	= trim($header);
	$rtn['body']	= trim($body);
	return $rtn;
}




#############################
#####   End Functions   #####
#############################
# Output main form except for details
	IF ($_GPV['action'] != 'details') {$_out .= main();}

# Continue if displaying results
	IF ($_GPV['action'] == 'details' && !$_CCFG['WHOIS_DETAILS_NEW']) {

	# Support .co.za and .mt
		IF (strpos('.co.za', $_GPV['server']) != 0 || strpos($_GPV['server'], '.mt') != 0) {
			$rtn		= ffl_HttpGet($_GPV['server'], 0);
			$_out	.= $rtn['body'];

	# Normal whois lookup
		} ELSE {
			$_out .= '<div align="center">'.$_nl;
			$_out .= '<table width="80%"><tr><td>'.$_nl;
			$_out .= '<p class="PMED_NL">'.$_nl;
			$fp = fsockopen($_GPV['server'],43);
			fputs($fp, "$_GPV[ord_domain]\r\n");
			while(!feof($fp)) {$_out .= fgets($fp,128).'<br>'.$_nl;}
			fclose($fp);
			$_out .= '</td></tr></table>'.$_nl;
			$_out .= '</div>'.$_nl;
		}
	}

# Continue if checking domain
	IF ($_GPV['action'] == 'checkdom') {
		// Check the name for bad characters
		IF (strlen($_GPV['ord_domain']) < 3) {
			$err	= 1;
			$msg	= $_LANG['_WHOIS']['Error_Too_Short'];
			$_out	.= disperror($msg);
		}
		IF (strlen($_GPV['ord_domain']) > 63) {
			$err	= 1;
			$msg	= $_LANG['_WHOIS']['Error_Too_Long'];
			$_out	.= disperror($msg);
		}
		IF (ereg("^-|-$",$_GPV['ord_domain'])) {
			$err	= 1;
			$msg	= $_LANG['_WHOIS']['Error_Hyphens'];
			$_out	.= disperror($msg);
		}

		IF (!$err) {
			IF (!ereg("([a-z]|[A-Z]|[0-9]|-){".strlen($_GPV['ord_domain'])."}",$_GPV['ord_domain'])) {
				$err	= 1;
				$msg	= $_LANG['_WHOIS']['Error_AlphaNum'];
				$_out .= disperror($msg);
			}
		}
		IF ($err) {$_out = '<br>'.$_out.$_nl;}

		IF (!$err) {
			#	$_out .= '<br>'.$_nl;
			$_out .= '<table width="80%"><tr><td>'.$_nl;
			$_out .= '<table width="100%" align="center" cellspacing="0" cellpadding="1">'.$_nl;
			$_out .= '<tr>'.$_nl;
			$_out .= '<td class="TP3MED_BC" nowrap bgcolor="'.$sepcolor.'">'.$_nl;
			$_out .= '<font color="'.$stdcolor.'"><b>'.$_sp.'</b></font>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BC" nowrap bgcolor="'.$sepcolor.'">'.$_nl;
			$_out .= '<font color="'.$stdcolor.'"><b>'.$_LANG['_WHOIS']['Title_Available'].'</b></font>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BC" nowrap bgcolor="'.$sepcolor.'">'.$_nl;
			$_out .= '<font color="'.$stdcolor.'"><b>'.$_LANG['_WHOIS']['Title_Taken'].'</b></font>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BC" nowrap bgcolor="'.$sepcolor.'">'.$_nl;
			$_out .= '<font color="'.$stdcolor.'"><b>'.$_sp.'</b></font>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '<td class="TP3MED_BC" nowrap bgcolor="'.$sepcolor.'">'.$_nl;
			$_out .= '<font color="'.$stdcolor.'"><b>'.$_sp.'</b></font>'.$_nl;
			$_out .= '</td>'.$_nl;
			$_out .= '</tr>'.$_nl;
			FOR ( $x=0; $x < $Lookups; $x++ ) {
				IF ((($_GPV['type'] == 'all') || ($_GPV['type'] == $Lookup_Domain['value'][$x])) && $Lookup_Domain['include'][$x]) {
					# Custom to get multiple lookups from one item
					IF ($Lookup_Domain['value'][$x] == 'com' && $Lookup_Domain['display'][$x] == '.com .net') {
						$dom_array = array($_GPV['ord_domain'].'.com', $_GPV['ord_domain'].'.net');
					} ELSEIF ($Lookup_Domain['value'][$x] == 'uk' && $Lookup_Domain['display'][$x] == '.co.uk .org.uk .me.uk') {
						$dom_array = array($_GPV['ord_domain'].'.co.uk', $_GPV['ord_domain'].'.org.uk', $_GPV['ord_domain'].'.me.uk');
					} ELSEIF ($Lookup_Domain['value'][$x] == 'pl' && $Lookup_Domain['display'][$x] == '.pl .com.pl') {
						$dom_array = array($_GPV['ord_domain'].'.pl', $_GPV['ord_domain'].'.com.pl');
					} ELSE {
						$dom_array = array($_GPV['ord_domain'].'.'.$Lookup_Domain['value'][$x]);
					}
    					$dom_count = count($dom_array);
					$i=0;
					FOR ($i=0; $i < $dom_count; $i++) {
						$domname	= $dom_array[$i];
						$result	= '';

					# Some Registrars now insert domain name into return
						$Lookup_Domain['nomatch'][$x]	= str_replace('%ORD_DOMAIN%', $domname, $Lookup_Domain['nomatch'][$x]);
						$Lookup_Domain['server'][$x]	= str_replace('%ORD_DOMAIN%', $domname, $Lookup_Domain['server'][$x]);

					# Support .co.za, and .mt
						IF ($Lookup_Domain['value'][$x] == 'co.za' || $Lookup_Domain['value'][$x] == 'com.mt' || $Lookup_Domain['value'][$x] == 'net.mt' || $Lookup_Domain['value'][$x] == 'org.mt' ) {
							$rtn		= ffl_HttpGet('http://'.$Lookup_Domain['server'][$x], 0);
							$result	= $rtn['body'];

					# Normal WHOIS lookup
						} ELSE {
							$ns = fsockopen($Lookup_Domain['server'][$x], 43, $errno, $errstr, 10);
							fputs($ns,"$domname\r\n");
							while(!feof($ns)) {$result .= fgets($ns,128);}
							fclose($ns);
						}
						$result = trim($result);

					# Some registrars now insert extra spaces and carriage returns into results
						$result = str_replace(' ', '', $result);
						$result = str_replace("\n", '', $result);
						$result = str_replace("\r", '', $result);

						$NoMatch = str_replace(' ', '', $Lookup_Domain['nomatch'][$x]);

						IF (eregi($NoMatch, $result) !== FALSE) {
							// Over-ride product_id ordered, if set in array above,
							// otherwise pass back ord_prod_id originally ordered.
							IF ($Lookup_Domain['prod_id'][$x]) {
								$prodid = $Lookup_Domain['prod_id'][$x];
							} ELSE {
								$prodid = $_REQUEST['ord_prod_id'];
							}
							$_out .= dispav($domname, $prodid);
						} ELSEIF ($result == 'disabled') {
							$_out .= dispdi($domname);
						} ELSE {
							$_out .= dispun($domname, $x);
						}
					}
					$_out .= '<tr><td colspan="5" bgcolor="'.$sepcolor.'">&nbsp;</td></tr>'.$_nl;
				}
			}
			$_out .= '</table>'.$_nl;
			$_out .= '</td></tr></table>'.$_nl;
		} // error check
	} // action checkdomain


# Call block it function
	$_tstr	.= $_LANG['_WHOIS']['Text_Title'];
	$_cstr	.= '<div align="center">'.$_out.'</div><br>'.$_nl;
	$_cstr	.= '<div class="PSML_NC">'.$poweredby.'</div><br>'.$_nl;
	$_mstr	 = do_nav_link('mod.php?mod=whois', $_TCFG['_IMG_TRY_AGAIN_M'], $_TCFG['_IMG_TRY_AGAIN_M_MO'], '', '');
	$_ret	.= do_mod_block_it($_tstr, $_cstr, '1', $_mstr, '1');
	$_ret	.= '<br>'.$_nl;
	echo $_ret;
?>