<?php
/**
 * Admin: Backup
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Backup
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_admin.php
 */


# Include session file (loads core)
	require_once('../coin_includes/session_set.php');

# Include redirect for after we are done
	require_once(PKG_PATH_INCL.'redirect.php');


# Do the backup IF it is an admin.
	$_SEC	= get_security_flags();
	$_PERMS	= do_decode_perms_admin($_SEC['_sadmin_perms']);
	IF ($_SEC['_sadmin_flg'] && ($_PERMS['AP13'] == 1 || $_PERMS['AP16'] == 1)) {

	# Set filename
		IF ($_SEC['_sadmin_name']) {$_doneby = $_SEC['_sadmin_name'];} ELSE {$_doneby = 'cron';}
		$_dumpname = 'phpcoin_v'.$ThisVersion.'_'.date("Y-m-d_His").'_'.$_doneby.'.sql';

	# Setup for download to desktop
		IF ($_POST['btype'] == 'download') {
			IF ((is_integer(strpos($user_agent, 'msie'))) && (is_integer(strpos($user_agent, 'win')))) {
				header("Content-disposition: filename=$_dumpname");
			} ELSE {
				header("Content-Disposition: attachment; filename=$_dumpname");
			}
			Header("Content-type: application/octetstream");

	# Setup for backup to local directory
		} ELSEIF ($_POST['btype'] == 'save') {
			$today		= date("F_j_Y");
    			$thearchive	= $_CCFG['MYSQL_BACKUP_SAVE_DIR'].'/'.$_dumpname;
			$handle		= fopen($thearchive, "w");
		}

	# Create the MySQL dump.
		$TheFile	= '';
		$tables	= $db_coin->db_query_execute('show tables');
		WHILE ($table = $db_coin->db_fetch_array($tables)) {
			$table = $table[0];

		# Optimize tables
			$sql = 'OPTIMIZE TABLE '.$table;
			$res = $db_coin->db_query_execute($sql);

			$schema  = "drop table if exists $table;\n";
			$schema .= "create table $table (\n";
			$table_list = '(';
			$fields = $db_coin->db_query_execute("show fields from $table");
			WHILE ($field = $db_coin->db_fetch_array($fields)) {
				$schema .= '  ' . $field['Field'] . ' ' . $field['Type'];
				IF (isset($field['Default']))	{$schema .= ' default \'' . $field['Default'] . '\'';}
				IF ($field['Null'] != 'YES')	{$schema .= ' not null';}
				IF (isset($field['Extra']))	{$schema .= ' ' . $field['Extra'];}
				$schema .= ",\n";
				$table_list .= $field['Field'] . ', ';
			}
			$schema		= ereg_replace(",\n$", "", $schema);
			$table_list	= ereg_replace(", $", "", $table_list) . ')';
			$index		= array();
			$keys		= $db_coin->db_query_execute("show keys from $table");
			WHILE ($key = $db_coin->db_fetch_array($keys)) {
				$kname = $key['Key_name'];
				IF (($kname != "PRIMARY") && ($key['Non_unique'] == 0)) {$kname = "UNIQUE|$kname";}
				IF (!isset($index[$kname])) {$index[$kname] = array();}
				$index[$kname][] = $key['Column_name'];
			}
			WHILE (list($x, $columns) = @each($index)) {
				$schema .= ",\n";
				IF ($x == 'PRIMARY') {
					$schema .= '  PRIMARY KEY (' . implode($columns, ', ') . ')';
				} ELSEIF (substr($x, 0, 6) == 'UNIQUE') {
					$schema .= '  UNIQUE '.substr($x,7).' (' . implode($columns, ', ') . ')';
				} ELSE {
					$schema .= "  KEY $x (" . implode($columns, ", ") . ")";
				}
			}
			$schema .= "\n);";
			IF ($_POST['btype'] == 'download') {echo "$schema\n";} ELSE {$TheFile .= $schema;}
			$rows = $db_coin->db_query_execute('select * from '.$table);
			WHILE ($row = $db_coin->db_fetch_array($rows)) {
				$schema_insert = "INSERT INTO $table $table_list VALUES (";
				WHILE (list($field) = each($row)) {
					list($field) = each($row);
					IF (!isset($row[$field])) {
						$schema_insert .= ' NULL,';
					} ELSEIF ($row[$field] != "") {
						$schema_insert .= " '".$db_coin->db_sanitize_data($row[$field])."',";
					} ELSE {
						$schema_insert .= " '',";
					}
				}
				$schema_insert = ereg_replace(',$', '', $schema_insert);
				$schema_insert .= ')';
				IF ($_POST['btype'] == 'download') {
					echo trim($schema_insert).";\n";
				} ELSE {
					$TheFile .= trim($schema_insert).";\n";
				}
			}
			IF ($_POST['btype'] == 'download') {echo "\n";} ELSE {$TheFile .= "\n";}
		}

	# Process the MySQL dump
		IF ($_POST['btype'] == 'save') {
			fwrite($handle, $TheFile);
			fclose($handle);
			$handle2 = fopen($thearchive, "r");
			IF ($handle2) {$_saved++; fclose($handle2);}
		}

	# Email The File
		IF ($_POST['btype'] == 'email') {
			$amail = array();
			IF ($_CCFG['_PKG_SAFE_EMAIL_ADDRESS']) {
				$amail['recip']	= $_CCFG['MYSQL_BACKUP_EMAIL_TO_ADDRESS'];
				$amail['from']		= $_CCFG['MYSQL_BACKUP_EMAIL_FROM_ADDRESS'];
			} ELSE {
				$amail['recip']	= '"'.$_CCFG['MYSQL_BACKUP_EMAIL_TO_NAME'].'" <'.$_CCFG['MYSQL_BACKUP_EMAIL_TO_ADDRESS'].'>';
				$amail['from']		= '"'.$_CCFG['MYSQL_BACKUP_EMAIL_FROM_NAME'].'" <'.$_CCFG['MYSQL_BACKUP_EMAIL_FROM_ADDRESS'].'>';
			}
			$amail['subject']		= $_CCFG['MYSQL_BACKUP_EMAIL_SUBJECT'];
			$amail['message']		= $_CCFG['MYSQL_BACKUP_EMAIL_BODY']."\n";
			$amail['dump']			= $TheFile;
			$amail['dumpname']		= $_dumpname;
			$_ret				= do_mail_basic($amail);
		}

	# redirect to "backed up" page
		IF ($_POST['btype'] != 'download') {
			IF ($_POST['btype'] == 'email') {
				html_header_location('mod.php?mod=pages&mode=view&id=3');
			} ELSEIF ($_POST['btype'] == 'save') {
				IF ($_saved) {
					html_header_location('mod.php?mod=pages&mode=view&id=3');
				} ELSE {
					html_header_location('mod.php?mod=pages&mode=view&id=4');
				}
			}
		}

} ELSE {
	// disallowed, so redirect to error page
	html_header_location('mod.php?mod=pages&mode=view&id=2');
}
?>