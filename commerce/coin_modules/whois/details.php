<?php
/**
 * Module: WHOIS (Domain Details)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- whois lookups based loosely on mrwhois Copyright (C) 2001 Marek Rozanski, marek@mrscripts.co.uk
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage whois
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_whois.php
 * @arguments $ord_domain Domain name to lookup registration details for
 * @arguments $server WHOIS server to ask for the details
 */

# Include Root File for package url/path and required files
	require_once('../../coin_includes/session_set.php');

# Ensure server isa  numebr, nto astring
	IF (!is_numeric($_GPV['server'])) {$_GPV['server'] = 0;}

# Exit if parameters not passed in
	IF (!$_GPV['ord_domain'] || !$_GPV['server']) {exit();}

# Initialize some variables
	$fontface	= 'verdana, ariel, helvetica';
	$fontsize	= 2;
	$stdcolor	= 'black';
	$_out	= '<pre>';

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

# Setup whois url
	$_whois = $Lookup_Domain['server'][$_GPV['server']];

# Support .co.za and .mt
	IF (strpos($_whois, '.co.za') != 0 || strpos($_whois, '.mt') != 0) {
		$_whois	= str_replace('%ORD_DOMAIN%', $_GPV['ord_domain'], $_whois);
		$rtn		= ffl_HttpGet($_whois, 1);
		$_out	.= $rtn['body'];

# Normal whois lookup
	} ELSE {
		$fp = fsockopen($_whois, 43, $errno, $errstr, 10);
		fputs($fp, "$_GPV[ord_domain]\r\n");
		while(!feof($fp)) {$_out .= fgets($fp,128);}
		fclose($fp);
	}

# Close display tag
	$_out .= '</pre>';

# Display output
	echo '<html><head><title>Domain Details</title></head><body bgcolor="white">';
	echo $_out;
	echo '<p align=center><a href=javascript:window.close()><font face='.$fontface.' size='.$fontsize.' color='.$stdcolor.'><b>Close</b></font></a>';
	echo '</body></html>';



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
function ffl_HttpGet($url, $followRedirects=true) {
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
?>