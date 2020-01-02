<?php
/**
 * Module: Downloads (Popup File Description)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Downloads
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_downloads.php
 * @arguments dload_id Record ID to lookup and display info for
 */


# Include session file (loads core)
	require_once('../../coin_includes/session_set.php');


# Select data from downloads table
	IF (!isset($_GPV['dload_id'])) {$_GPV['dload_id'] = 0;}
	$query = 'SELECT dload_name, dload_desc FROM '.$_DBCFG['downloads'];
	IF ($_CCFG['HIDE_NOAVAILS']) {$query .= ' WHERE dload_avail=1';}
	$query .= ' AND dload_id='.$_GPV['dload_id'];
	$result = $db_coin->db_query_execute($query);

# Process returned record
	$_title	= '';
	$_content	= '';
	while(list($dload_name, $dload_desc) = $db_coin->db_fetch_row($result)) {
		$_title	= $dload_name;
		$_content	= $dload_desc;
	}

# Display in new window
	echo '<html>'.$_nl;
	echo '<head>'.$_nl;
	echo '<title>phpCOIN Download Description</title>'.$_nl;
	echo '<link href="'.$_CCFG['_PKG_URL_THEME'].'styles_print.css" rel="styleSheet" type="text/css">'.$_nl;
	echo '<script language="javascript 1.2" type="text/javascript">'.$_nl;
	echo '<!--'.$_nl;
	echo 'function RSto() {'.$_nl;
	echo '	self.resizeTo(620,320);'.$_nl;
	echo '	self.location.visible=false;'.$_nl;
	echo '	self.menubar.visible=false;'.$_nl;
	echo '	self.personalbar.visible=false;'.$_nl;
	echo '	self.scrollbars.visible=true;'.$_nl;
	echo '	self.statusbar.visible=false;'.$_nl;
	echo '	self.toolbar.visible=false;'.$_nl;
	echo '}'.$_nl;
	echo 'function printpage() {'.$_nl;
	echo '    window.print();'.$_nl;
	echo '}'.$_nl;
	echo '//-->'.$_nl;
	echo '</script>'.$_nl;
	echo '</head>'.$_nl;
	echo '<body onLoad="RSto()" onBlur="self.focus()">'.$_nl;
	echo '<script language="JavaScript" type="text/javascript">'.$_nl;
	echo '<!--'.$_nl;
//	echo '   self.resizeTo(620,320);'.$_nl;
	echo '-->'.$_nl;
	echo '</script>'.$_nl;
	echo '<div class="TP5MED_NL" style="border: 1px; margin-left: auto; margin-right: auto; width: 95%;">'.$_nl;
	echo '<fieldset class="helptext">'.$_nl;
	$_title = ucwords(ereg_replace('_', ' ', strtolower($_title)));
	IF (!$_title) {$_name = 'Nothing Selected';}
	echo '<legend>'.$_title.'</legend>';
	echo $_content;
	echo '</fieldset>'.$_nl;
	echo '<form>'.$_nl;
	echo '    <input type="button" value="Print Page" onClick="printpage()" class="formbutton">'.$_nl;
	echo '	<input type="button" value="Close Window" onClick="window.close()" class="formbutton">'.$_nl;
	echo '</form>'.$_nl;
	echo '</div>'.$_nl;
	echo '</body>'.$_nl;
	echo '</html>'.$_nl;
?>