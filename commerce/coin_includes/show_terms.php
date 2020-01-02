<?php
/**
 * Module: SiteInfo (iFrame Dispaly Page)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage SiteInfo
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @arguments id Record ID to lookup and display info for
 */


require_once('session_set.php');
IF (!$_GPV['id']) {$_GPV['id'] = 1;}
$query					= 'SELECT si_code, si_title FROM '.$_DBCFG['site_info']." WHERE si_id='".$db_coin->db_sanitize_data($_GPV['id'])."'";
$result					= $db_coin->db_query_execute($query);
list($si_code, $si_title)	= $db_coin->db_fetch_row($result);

$_string	= addslashes($si_code);
eval("\$_string = \"$_string\";");
$_string	= stripslashes($_string);

$_out  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'.$_nl;
$_out .= '<html>'.$_nl;
$_out .= '<head>'.$_nl;
$_out .= '<meta name="generator" content="phpCOIN">'.$_nl;
$_out .= '<title>'.$si_title.'</title>'.$_nl;
$_out .= '<style type=text/css>'.$_nl;
$_out .= ' body {background-color: white; color: black; font-size: 10pt; font-familly: arial, helvetica, verdana;}'.$_nl;
$_out .= '</style>'.$_nl;
$_out .= '</head>'.$_nl;
$_out .= '<body>'.$_nl;
$_out .= $_string;
$_out .= '</body></html>';
echo $_out;
?>