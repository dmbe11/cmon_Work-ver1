
DOCUMENTATION AND SUPPORT:
==========================
	Software License: 		/coin_docs/license.txt
	Free support forums:	http://forums.phpCOIN.com
	User manual:			http://docs.phpCOIN.com
	Bug Reports & Fixes:	http://bugs.phpOIN.com



HOW AND WHEN TO RUN SETUP.PHP:
==============================

	New Installation:
	-----------------
		If you are installing for the first time, run /coin_setup/setup.php

	Upgrade from v1.0.1:
	--------------------
		Run /coin_setup/upgrade_to_v11x.php to get to v1.1.0
		Run /coin_setup/upgrade_to_v120.php to get to v1.2.0
		Run /coin_setup/setup.php to get to this version of phpCOIN

	Upgrade from v1.1.0:
	--------------------
		Run /coin_setup/upgrade_to_v120.php to get to v1.2.0
		Run /coin_setup/setup.php to get to this version of phpCOIN

	Upgrade from v1.2.0 or higher:
	--------------------
		Run /coin_setup/setup.php to get to this version of phpCOIN

	Upgrade from this version
	-------------------
		If you have already upgraded the database to this version,
		DO NOT RUN SETUP.PHP OR YOUR DATABASE WILL BE WIPED OUT AND
		A FRESH INSTAL DONE



If you are upgrading from phpCOIN v1.3.1 or lower, some major changes may affect
any third-party themes or add-on modules you have.  See:
http://docs.phpcoin.com/index.php/How_to_Upgrade_phpCOIN
for details on a quick workaround, as well as detailed permanent-fix
instructions.


If you are upgrading from phpCOIN v1.4.2 or lower, all override files that you
have created MUST be moved into the new /coin_overrides folder or they will no
longer work.  Language override files MUST be moved into
/coin_overrides/lang_xxxxx (where xxxxx is the name of the language, such as
english, french, german, etc.) or they will no longer work.

This new folder structure ensures that all your customizations are in one place,
and also makes it a lot easier to delete all older phpCOIN files without
accidentally deleting your override files.