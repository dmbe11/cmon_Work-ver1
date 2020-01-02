<?php
/**
 * Loader: Main
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Output
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Include session file (loads core)
	require_once('coin_includes/session_set.php');

	$_CCFG['_IS_PRINT'] = 0;

# Set index global, Call Load Component parms
	$_comp_name	= 'index';
	$_comp_oper	= '';
	$compdata	= do_load_comp_data($_comp_name, $_comp_oper);

# Call page open (start to content)
	$_popen = do_page_open($compdata, '1');

# Check for "generator" line
	IF (!eregi('<meta name="generator" content="phpcoin">', $_popen)) {
		html_header_location('error.php?err=98');
		exit();
	}

# Output page open
	echo $_popen;

# Call display site info function for: site - announcement
	$si_code = '';
	$si_code .= do_site_info_display('', 'site', 'announce', '1');
	IF ($si_code) {echo $si_code.'<br>';}

# Call display site info function for: site - greeting
	$si_code = '';
	$si_code .= do_site_info_display('', 'site', 'greeting', '1');
	IF ($si_code) {echo $si_code.'<br>';}

# Call display site info function for: site - index
	$si_code = '';
	$si_code .= do_site_info_display('', 'site', 'index', '1');
	IF ($si_code) {echo $si_code;}

# Call page close (content to finish)
	do_page_close($compdata, '0');
?>