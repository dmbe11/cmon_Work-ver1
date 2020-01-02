<?php
/**
 * Loader: API System
 *	- phpCOIN is based on concept and code of Mike Lansberry <mg@mgwebhosting.com>
 *	- Do NOT alter or remove this text block
 * @package phpCOIN
 * @subpackage API
 * @version 1.6.5
 * @author Stephen M. Kitching <support@phpcoin.com>
 * @copyright Copyright © 2003-2009 COINSoftTechnologies Inc. All rights reserved.
 * @license coin_docs/license.txt phpCOIN License Terms
 * @translation lang_api.php
 */


# Code to handle file being loaded by URL
	IF (eregi('api.php', $_SERVER['PHP_SELF'])) {
		require_once('session_set.php');
		require_once(PKG_PATH_INCL.'redirect.php');
		html_header_location('error.php?err=01');
		exit;
	}


/**************************************************************
 *	API Output (APIO) Triggers
 *	All are flagged in code (for search) by following:
 *		# API Output Hook:
 *		# APIO_order_new_client: Order new client hook
 *			(for example- each has correct name)
 *	Notes-
 *		- Data array in scope at time of fcall passed in.
 *		- Return array for "dn"- success, and "msg" string
 *		- All globals / parameters in scope.
 *		- Database connected and available.
**************************************************************/

/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_cor_proc ($_APIO_AData)
 *	Notes-
 *		- Trigger during the place order process.
 *		- Fires on custom order request emails sent
 *		  Scope is during "process_order" script.
**************************************************************/
function APIO_order_cor_proc($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_out_proc ($_APIO_AData)
 *	Notes-
 *		- Trigger during the place order process.
 *		- Fires on order inserted into database during order
 *		  process. Scope is during "process_order" script.
**************************************************************/
function APIO_order_out_proc($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_ret_proc ($_APIO_AData)
 *	Notes-
 *		- Trigger during the place order process.
 *		- Fires on return from billing vendor and order return
 *		  processing. Buy / Cancel should be known at this time.
**************************************************************/
function APIO_order_ret_proc($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_new_client ($_APIO_AData)
 *	Notes-
 *		- Trigger during the place order process.
 *		- Fires on new client inserted into database during order
 *		  process. Scope is during "process_order" script.
**************************************************************/
function APIO_order_new_client($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_new_domain ($_APIO_AData)
 *	Notes-
 *		- Trigger during the place order process.
 *		- Fires on new domain inserted into database during order
 *		  process. Scope is during "process_order" script.
**************************************************************/
function APIO_order_new_domain($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_client_new ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing clients.
 *		- Fires on new client inserted into database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_client_new($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_client_del ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing clients.
 *		- Fires on deleting a client from the database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_client_del($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags();

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_domain_new ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing domains.
 *		- Fires on new domain inserted into database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_domain_new($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_domain_del ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing domains.
 *		- Fires on deleting a domain from the database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_domain_del($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_new ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing orders.
 *		- Fires on new order inserted into database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_order_new($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_order_del ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing orders.
 *		- Fires on deleting a order from the database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_order_del($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_product_new ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing products.
 *		- Fires on new product inserted into database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_product_new($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_product_del ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing products.
 *		- Fires on deleting a product from the database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_product_del($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_trans_new ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing transactions / payments.
 *		- Fires on new transaction inserted into database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_trans_new($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}


/**************************************************************
 *	API Output (APIO) Trigger: APIO_trans_del ($_APIO_AData)
 *	Notes-
 *		- Trigger admin editing transactions / payments.
 *		- Fires on deleting a transaction from the database.
 *		  Scope is during admin editing.
**************************************************************/
function APIO_trans_del($_APIO_AData) {
	# Get security flags
		$_SEC = get_security_flags ( );

	# Dim some Vars
		global $_CCFG, $_TCFG, $_DBCFG, $_UVAR, $_LANG, $_SERVER, $_nl, $_sp;

	# Do whatever, set returns
		$_APIO_Ret['dn']	= 1;
		$_APIO_Ret['msg']	= 'none';

	# Return output
		return $_APIO_Ret;
}
?>