<?php
/**
 * Module: Downloads (Main)
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Downloads
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_downloads.php
 */


# Code to handle file being loaded by URL
	IF (eregi('index.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01&url=mod.php?mod=downloads');
		exit();
	}


# Get security vars
	$_login_flag	= 0;
	$_SEC		= get_security_flags();

##############################
# Mode Call: 	All modes
# Summary:
#	- Check if login required
##############################
IF ($_CCFG['LIMIT_DOWNLOADS_TO_LOGGED_IN'] && !$_SEC['_suser_flg'] && !$_SEC['_sadmin_flg']) {
	# Set login flag
		$_login_flag = 1;

	# Call function for clients listings
		$_out = '<!-- Start content -->'.$_nl;
		$_out .= do_login($data, 'user', '1').$_nl;

	# Echo final output
		echo $_out;
}


# Process file if logged in or login not required
IF (!$_login_flag) {


# Include language file (must be after parameter load to use them)
	require_once($_CCFG['_PKG_PATH_LANG'].'lang_downloads.php');
	IF (file_exists($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_downloads_override.php')) {
		require_once($_CCFG['_PKG_PATH_LANG_OVERRIDE'].'lang_downloads_override.php');
	}

# Include functions file
//	require_once(PKG_PATH_MDLS.$_GPV['mod'].'/'.$_GPV['mod'].'_funcs.php');


# Select data from downloads table
	$numrows	 = 0;
	$query 	 = "SELECT * FROM ".$_DBCFG['downloads'];
	IF ($_CCFG['HIDE_NOAVAILS']) {$query .= ' WHERE dload_avail=1';}
	$query 	.= " ORDER BY dload_group ASC, dload_name ASC";
	$result 	 = $db_coin->db_query_execute($query);
	$numrows	 = $db_coin->db_query_numrows($result);

# Process returned record
	$_dload_group_pntr	= '';
	while(list( $dload_id, $dload_group, $dload_name, $dload_desc, $dload_count, $dload_date_str, $dload_avail, $dload_filename, $dload_filesize, $dload_contributor ) = $db_coin->db_fetch_row($result)) {
	# Check $dload_group and set misc text
		$_tbls_title = $dload_group;

	# Open up table
		IF ($_dload_group_pntr != $dload_group) {
			IF ($_tbls != '') {
				$_tbls .= '</table>'.$_nl;
				$_tbls .= '</div>'.$_nl;
				$_tbls .= '<br>'.$_nl;
			}

		# Initialize show descritoions flag and build link to display
			IF (!isset($_GPV['sd'])) {$_GPV['sd'] = 0;}
			IF ($_GPV['sd'] == 1) {
				$_tbls .= '[<a href="mod.php?mod=downloads&sd=0">'.$_LANG['Downloads']['Hide_Descriptions'].'</a>]<br>';
			} ELSE {
				$_tbls .= '[<a href="mod.php?mod=downloads&sd=1">'.$_LANG['Downloads']['Show_Descriptions'].'</a>]<br>';
			}

			$_tbls .= '<div align="center">'.$_nl;
			$_tbls .= '<table width="100%" border="0" bordercolor="'.$_TCFG['_TAG_TABLE_BRDR_COLOR'].'" bgcolor="'.$_TCFG['_TAG_TRTD_BKGRND_COLOR'].'" cellpadding="0" cellspacing="1">'.$_nl;
			$_tbls .= '<tr class="BLK_DEF_TITLE"><td class="TP3MED_BC" colspan="7">'.$_nl;

			$_tbls .= '<table width="100%" cellpadding="0" cellspacing="0">'.$_nl;
			$_tbls .= '<tr class="BLK_IT_TITLE_TXT">'.$_nl.'<td class="TP0MED_NL" colspan="7">'.$_nl;
			$_tbls .= '<b>'.$_tbls_title.'</b><br>'.$_nl;
			$_tbls .= '</td>'.$_nl.'</tr>'.$_nl.'</table>'.$_nl;

			$_tbls .= '</td></tr>'.$_nl;
			$_tbls .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_tbls .= '<td class="TP3SML_NL"><b>'.$_LANG['Downloads']['Name'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="60"><b>FileName</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="70"><b>'.$_LANG['Downloads']['Released'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="70"><b>'.$_LANG['Downloads']['Contributor'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="60"><b>'.$_LANG['Downloads']['FileSize'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="68"><b>'.$_LANG['Downloads']['Count'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="35"><b>'.$_LANG['Downloads']['Get_It'].'</b></td>'.$_nl;
			$_tbls .= '</tr>'.$_nl;
		}

	# Rows
		$_tbls .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
		$_tbls .= '<td class="TP3SML_NL">';

		IF (!$_GPV['sd']) {
			IF (strlen($dload_desc) > 255) {
				$theURL	 = PKG_URL_BASE.'coin_modules/downloads/desc.php?dload_id='.$dload_id;
				$_tbls .= "<a href=\"$theURL\" target=\"_new\" onclick=\"window.open('$theURL','HelpText','toolbar=no,directories=no,location=no,status=no,menubar=no,resizable=yes,scrollbars=yes,width=620,height=320');return false;\"".$_et.'>';
				$_tbls .= $_PARM_PREFIX.'ni_view.gif" border="0" alt="Icon: File Description" title="'.$_LANG['Downloads']['Too_Long'].'"></a>&nbsp;&nbsp;';
			} ELSE {
				$_tbls .= $_PARM_PREFIX.'ni_view.gif" alt="Icon: File Description" title="'.htmlspecialchars($dload_desc).'">&nbsp;&nbsp;';
			}
		}
		$_tbls .= $dload_name;
		$_tbls .= '</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NC">'.$dload_filename.'</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NC">'.$dload_date_str.'</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NC">'.$dload_contributor.'</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NR">'.$dload_filesize.'</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NR">'.number_format($dload_count).'&nbsp;</td>'.$_nl;
		$_tbls .= '<td class="TP3SML_NC">'.$_nl;
		$fileparts	= explode('.',$dload_filename);
		$fileext		= $fileparts[1];
		IF ($fileparts[2]) {$fileext .= '.'.$fileparts[2];}
		IF ($dload_avail == 1) {
			$_tbls .= '[<a href="'.PKG_URL_MDLS.'downloads/dload.php?id='.$dload_id.'" target="_blank">'.$fileext.'</a>]';
		} ELSE {
			$_tbls .= 'n/a';
		}
		$_tbls .= '</td>'.$_nl;
		$_tbls .= '</tr>'.$_nl;

	# Show descriptios, if enabled
		IF ($_GPV['sd'] == 1) {
			$_tbls .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_tbls .= '<td colspan="7" class="TP3SML_NL"><b>'.$_LANG['Downloads']['Description'].':</b>'.$_sp.$dload_desc.'</td>'.$_nl;
			$_tbls .= '</tr>'.$_nl;

			$_tbls .= '<tr class="BLK_DEF_ENTRY"><td colspan="7" class="TP3SML_NL">&nbsp;</td></tr>';
			$_tbls .= '<tr class="BLK_DEF_ENTRY">'.$_nl;
			$_tbls .= '<td class="TP3SML_NL"><b>'.$_LANG['Downloads']['Name'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="60"><b>FileName</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="70"><b>'.$_LANG['Downloads']['Released'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="70"><b>'.$_LANG['Downloads']['Contributor'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="60"><b>'.$_LANG['Downloads']['FileSize'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NR" width="68"><b>'.$_LANG['Downloads']['Count'].'</b></td>'.$_nl;
			$_tbls .= '<td class="TP3SML_NC" width="35"><b>'.$_LANG['Downloads']['Get_It'].'</b></td>';

		}

		$_dload_group_pntr = $dload_group;
	}

# Closeout- assumes a table being finished
	$_tbls .= '</table>'.$_nl;
	$_tbls .= '</div>'.$_nl;
	$_tbls .= '<br>'.$_nl;


# Build Final output block
	$_tstr  = $_LANG['Downloads']['Title'];

	$_cstr = '<p>'.$_LANG['Downloads']['Pre-amble'].'</p><p>';
	IF ( $_GPV['v'] == 'group' )
		{ $_cstr .= '[<a href="mod.php?mod=downloads&v=name">'.$_LANG['Downloads']['Group_Name'].'</a>]'.$_nl; }
	IF ( $_GPV['v'] == 'name' )
		{ $_cstr .= '[<a href="mod.php?mod=download&v=group">'.$_LANG['Downloads']['Group_Category'].'</a>]'.$_nl; }
	$_cstr .= '</p>'.$_tbls.$_nl;

	$_mstr = ''.$_nl;

	echo do_mod_block_it($_tstr, $_cstr, 0, $_mstr, 1);

}
?>