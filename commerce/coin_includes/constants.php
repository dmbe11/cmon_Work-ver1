<?php
/**
 * Configuration: Constants
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage Configuration
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 */


# Code to handle file being loaded by URL
	IF (eregi('constants.php', $_SERVER['PHP_SELF'])) {
		Header("Location: ../error.php?err=01");
		exit;
	}


/**************************************************************
 * Misc System Constants (do not translate or edit)
**************************************************************/
# Some formatting strings
	$_nl							= "\n";
	$_sp							= '&nbsp;';

# Orders Session Table record age in seconds
	$_CCFG['OS_AGE_IN_SECONDS']		= 3600;

# Session Table record age in seconds
	$_CCFG['S_AGE_IN_SECONDS']		= 900;

# Flood Control Parameters: Once per numbers seconds set.
	$_CCFG['FC_IN_SECONDS_CONTACTS']	= 30;
	$_CCFG['FC_IN_SECONDS_ORDERS']	= 30;

# Menu Block Item Target Select List Params (must be valid target)
	$_CCFG['MBI_LINK_TARGET'][0]		= '_self';		# For Link Target to _self
	$_CCFG['MBI_LINK_TARGET'][1]		= '_blank';		# For Link Target to _blank
	$_CCFG['MBI_LINK_TARGET'][2]		= '_top';			# For Link Target to _top

# Menu Block Item Text Contents Type (text, image, function)
	$_CCFG['MBI_TEXT_TYPE'][0]		= 'Text';			# For Text Contents- Text
	$_CCFG['MBI_TEXT_TYPE'][1]		= 'Image';		# For Text Contents- Image
	$_CCFG['MBI_TEXT_TYPE'][2]		= 'Function';		# For Text Contents- Function

# Order Product List Sort Order Options Select List Params ()
	$_CCFG['ORD_PROD_LIST_SORT'][0]	= 'Product ID';
	$_CCFG['ORD_PROD_LIST_SORT'][1]	= 'Product Name';
	$_CCFG['ORD_PROD_LIST_SORT'][2]	= 'Product Description';
	$_CCFG['ORD_PROD_LIST_SORT'][3]	= 'Product Price';

/**************************************************************
 * Table Name with Prefix Array (must be after DB load)
**************************************************************/

# Build Array for database tables
	$_DBCFG['admins'] 				= $_DBCFG['table_prefix'].'admins';
	$_DBCFG['articles'] 			= $_DBCFG['table_prefix'].'articles';
	$_DBCFG['banned'] 				= $_DBCFG['table_prefix'].'banned';
	$_DBCFG['categories'] 			= $_DBCFG['table_prefix'].'categories';
	$_DBCFG['clients'] 				= $_DBCFG['table_prefix'].'clients';
	$_DBCFG['clients_contacts'] 		= $_DBCFG['table_prefix'].'clients_contacts';
	$_DBCFG['components'] 			= $_DBCFG['table_prefix'].'components';
	$_DBCFG['domains']				= $_DBCFG['table_prefix'].'domains';
	$_DBCFG['downloads'] 			= $_DBCFG['table_prefix'].'downloads';
	$_DBCFG['faq'] 				= $_DBCFG['table_prefix'].'faq';
	$_DBCFG['faq_qa'] 				= $_DBCFG['table_prefix'].'faq_qa';
	$_DBCFG['helpdesk'] 			= $_DBCFG['table_prefix'].'helpdesk';
	$_DBCFG['helpdesk_msgs'] 		= $_DBCFG['table_prefix'].'helpdesk_msgs';
	$_DBCFG['icons'] 				= $_DBCFG['table_prefix'].'icons';
	$_DBCFG['invoices'] 			= $_DBCFG['table_prefix'].'invoices';
	$_DBCFG['invoices_items']		= $_DBCFG['table_prefix'].'invoices_items';
	$_DBCFG['invoices_trans']		= $_DBCFG['table_prefix'].'invoices_trans';
	$_DBCFG['ipn_log']				= $_DBCFG['table_prefix'].'ipn_log';
	$_DBCFG['ipn_text']				= $_DBCFG['table_prefix'].'ipn_text';
	$_DBCFG['mail_archive'] 			= $_DBCFG['table_prefix'].'mail_archive';
	$_DBCFG['mail_contacts'] 		= $_DBCFG['table_prefix'].'mail_contacts';
	$_DBCFG['mail_queue'] 			= $_DBCFG['table_prefix'].'mail_queue';
	$_DBCFG['mail_templates'] 		= $_DBCFG['table_prefix'].'mail_templates';
	$_DBCFG['menu_blocks'] 			= $_DBCFG['table_prefix'].'menu_blocks';
	$_DBCFG['menu_blocks_items']		= $_DBCFG['table_prefix'].'menu_blocks_items';
	$_DBCFG['orders'] 				= $_DBCFG['table_prefix'].'orders';
	$_DBCFG['orders_sessions']		= $_DBCFG['table_prefix'].'orders_sessions';
	$_DBCFG['pages'] 				= $_DBCFG['table_prefix'].'pages';
	$_DBCFG['parameters'] 			= $_DBCFG['table_prefix'].'parameters';
	$_DBCFG['products'] 			= $_DBCFG['table_prefix'].'products';
	$_DBCFG['reminders'] 			= $_DBCFG['table_prefix'].'reminders';
	$_DBCFG['server_info'] 			= $_DBCFG['table_prefix'].'server_info';
	$_DBCFG['sessions'] 			= $_DBCFG['table_prefix'].'sessions';
	$_DBCFG['site_info'] 			= $_DBCFG['table_prefix'].'site_info';
	$_DBCFG['todo'] 				= $_DBCFG['table_prefix'].'todo';
	$_DBCFG['topics'] 				= $_DBCFG['table_prefix'].'topics';
	$_DBCFG['vendors'] 				= $_DBCFG['table_prefix'].'vendors';
	$_DBCFG['vendors_prods'] 		= $_DBCFG['table_prefix'].'vendors_prods';
	$_DBCFG['versions'] 			= $_DBCFG['table_prefix'].'versions';
	$_DBCFG['whois'] 				= $_DBCFG['table_prefix'].'whois';

	$_DBCFG['suppliers'] 			= $_DBCFG['table_prefix'].'suppliers';
	$_DBCFG['suppliers_contacts'] 	= $_DBCFG['table_prefix'].'suppliers_contacts';
	$_DBCFG['bills'] 				= $_DBCFG['table_prefix'].'bills';
	$_DBCFG['bills_items']			= $_DBCFG['table_prefix'].'bills_items';
	$_DBCFG['bills_trans']			= $_DBCFG['table_prefix'].'bills_trans';

# Countries
	$_Countries					= array('Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica', 'Antigua & Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia & Herzegowina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Cook Islands', 'Costa Rica', 'Cote D\'Ivoire', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'France, Metropolitan', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-bissau', 'Guyana', 'Haiti', 'Heard & McDonald Islands', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran, Islamic Republic of', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, Democratic People\'s Republic of', 'Korea, Republic of', 'Kuwait', 'Kyrgyzstan', 'Lao People\'s Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia, (former) Yugoslav Republic of', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States of', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent & the Grenadines', 'Samoa', 'San Marino', 'Sao Tome & Principe', 'Saudi Arabia', 'Senegal', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia & South Sandwich Islands', 'Spain', 'Sri Lanka', 'St. Helena', 'St. Pierre & Miquelon', 'Sudan', 'Suriname', 'Svalbard & Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan, Province of China', 'Tajikistan', 'Tanzania, United Republic of', 'Thailand', 'Togo', 'Tokelau', 'Tonga', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks & Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'US, Minor Outlying Islands', 'Uzbekistan', 'Vanuatu', 'Vatican City State (Holy See)', 'Venezuela', 'Viet Nam', 'Virgin Islands (British)', 'Virgin Islands (U.S.)', 'Wallis & Futuna Islands', 'Western Sahara', 'Yemen', 'Yugoslavia', 'Zaire', 'Zambia', 'Zimbabwe');

# "action" of default page shown to clients upon login.
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][1]	= 'cc';
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][2]	= 'clients';
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][3]	= 'domains';
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][4]	= 'invoices';
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][5]	= 'helpdesk';
	$_CCFG['CLIENT_VIEW_PAGE_UPON_LOGIN_ACTION'][6]	= 'orders&mode=view';

# Config loaded flag, to prevent double-loading of non-numeric-element arrays
	IF (!defined('CONSTANTED')) {

	# Parameter Group Select List Params (do not change)
		$_CCFG['_PARM_GROUP'][]			= 'add-ons';
		$_CCFG['_PARM_GROUP'][]			= 'cronjobs';
		$_CCFG['_PARM_GROUP'][]			= 'enable';
		$_CCFG['_PARM_GROUP'][]			= 'layout';
		$_CCFG['_PARM_GROUP'][]			= 'ordering';
		$_CCFG['_PARM_GROUP'][]			= 'operation';
		$_CCFG['_PARM_GROUP'][]			= 'theme';
		$_CCFG['_PARM_GROUP'][]			= 'user';

	# Parameter Sub-Group Select List Params (Do NOT delete or edit)
		$_CCFG['_PARM_GROUP_SUB'][]		= 'admin';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'API';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'articles';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'backup';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'bills';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'buttons';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'clients';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'domains';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'downloads';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'email';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'helpdesk';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'invoices';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'ipn';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'orders';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'package';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'pages';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'summary';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'suppliers';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'todo';
		$_CCFG['_PARM_GROUP_SUB'][]		= 'whois';

	# Config loaded flag, to prevent double-loading of non-numeric-element arrays
		define('CONSTANTED', 1);
	}
?>