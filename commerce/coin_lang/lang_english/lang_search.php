<?php
/**
 * Language: English
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Search
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translater Stephen M. Kitching <support@phpCOIN.com>
 */


# Code to handle file being loaded by URL
	IF (eregi('lang_search.php', $_SERVER['PHP_SELF'])) {
		require_once('../../coin_includes/session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}

/**************************************************************
 * Language Variables
**************************************************************/
# Language Variables: Common
		$_LANG['_SEARCH']['Search_Results']					= 'Search Results';
		$_LANG['_SEARCH']['Search_Site']						= 'Search Site';

		$_LANG['_SEARCH']['sl_Entire_Site']					= 'Entire Site';
		$_LANG['_SEARCH']['sl_Articles']						= 'Articles';
		$_LANG['_SEARCH']['sl_FAQ']							= 'FAQ';
		$_LANG['_SEARCH']['sl_Guest_Book']						= 'Guest Book';
		$_LANG['_SEARCH']['sl_Journal']						= 'Journal';
		$_LANG['_SEARCH']['sl_Links']							= 'Links';
		$_LANG['_SEARCH']['sl_Pages']							= 'Pages';
		$_LANG['_SEARCH']['sl_SiteInfo']						= 'SiteInfo';

		$_LANG['_SEARCH']['sl_All_Possible']					= 'All Possible';
		$_LANG['_SEARCH']['sl_Content_Entry']					= 'Content / Entry';
		$_LANG['_SEARCH']['sl_Subject_Title']					= 'Subject / Title';

		$_LANG['_SEARCH']['items']							= 'items';
		$_LANG['_SEARCH']['New_Win_Message']					= 'Clicking Result Link Opens New Window';
		$_LANG['_SEARCH']['No_Items_Found']					= 'No Items Found';

# Language Variables: Some Buttons
		$_LANG['_SEARCH']['B_Reset']							= 'Reset';
		$_LANG['_SEARCH']['B_Search']							= 'Search';

# Language Variables: Common Labels (note : on end)
		$_LANG['_SEARCH']['l_Search_For']						= 'Search For:';
		$_LANG['_SEARCH']['l_Where']							= 'Where:';
		$_LANG['_SEARCH']['l_In']							= 'In:';

?>