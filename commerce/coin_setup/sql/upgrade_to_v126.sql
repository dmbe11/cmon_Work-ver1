ALTER table %PREFIX%clients MODIFY cl_user_pword varchar(100);
INSERT INTO %PREFIX%components (`comp_id`, `comp_type`, `comp_name`, `comp_mod`, `comp_desc`, `comp_ptitle`, `comp_col_num`, `comp_isadmin`, `comp_isuser`, `comp_status`) VALUES ('', 'module', 'ipn', 'ipn', 'Instant Payment Notification', 'Instant Payment Notification', 2, 1, 0, 1);
CREATE TABLE IF NOT EXISTS %PREFIX%ipn_log (`ipn_id` int(10) unsigned NOT NULL auto_increment, `ipn_ts` varchar(10) default NULL, `ipn_var_details` text, `ipn_txn` varchar(25) default NULL, `ipn_txn_type` varchar(100) default NULL, `ipn_cl_id` int(11) default NULL, `ipn_pay_amt` double default '0', `ipn_amt_applied` double default '0', `ipn_pay_stat` int(11) default NULL, `ipn_name_last` varchar(100) default NULL, `ipn_vendor` varchar(50) default NULL, PRIMARY KEY  (`ipn_id`)) TYPE=MyISAM;
CREATE TABLE IF NOT EXISTS %PREFIX%ipn_text (`id` int(6) unsigned NOT NULL auto_increment, `ipn_txn_id` varchar(25) default NULL, `ipn_text_ts` varchar(10) default NULL, `ipn_log_text` text, PRIMARY KEY  (`id`)) TYPE=MyISAM;
ALTER table %PREFIX%invoices ADD column invc_last_nag_id int(11) NOT NULL default 0 after invc_recurr_proc;
INSERT INTO %PREFIX%parameters (`parm_id`, `parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('', 'common', 'invoices', 'B', 'INVC_SHOW_LAST_NAG', 'Invoice- Show Last Nag', '1', 'Determines if an additional info-box containing the last nag email sent will be displayed as part of the invoice.');
INSERT INTO %PREFIX%parameters (`parm_id`, `parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('', 'common', 'downloads', 'S', 'DLOAD_URL', 'Downloads Root URL', 'http://%DOMAIN%/coin_downloads/', 'Root URL where downloads are located. The http:// prefix and the trailing slash can be eliminated ~ phpCOIN will auto-append them if necessary.');
DELETE FROM %PREFIX%parameters WHERE parm_name='_debug_queries';
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common','ipn','I','IPN_NUM_DISPLAY','IPN: Default number of items to display','25','Default number of items to display in IPN Log');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','B','IPN_SEND_TRANS_ACK','IPN: Send transaction ack email','1','Should the mod send a trans ack email when payment is recieved?');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','B','IPN_PROCESS_INCOMING','IPN: Process Incoming Data','1','If we accept data, do we want to process it also?');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common','ipn','S','PAYPAL_RECEIVER_EMAIL','Paypal: Paypal EMail Address','payment@mividdesigns.com','This email needs to match your Primary PayPal email listed.  You can have multiple email addresses here sperated by spaces.\r\nEx:\r\n\r\njon@paypal.com john@paypal.com');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','B','IPN_SEARCH_NAME','Select invoice by name','1','This determines if the first and last name from the sender can also be used to determine the invoice if the email address cannot be used.');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','B','IPN_ACCEPT_INCOMING','IPN: Accept IPN data','1','Should phpCoin accept data from IPN?  Or ignore it?  (Essentially the on/off switch).');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','I','IPN_INVOICE_FIND_METHOD','IPN: Invoice Find Method','4','If no invoice number is sent with a Paypal transaciton, it searches by email address.  This is the method in which it applies the payment.\r\n0 - Highest invoice number, regardless of status\r\n1 - Highest invoice number that does not have status PAID\r\n2 - Highest invoice number that does not have status PAID, if none found, then return highest invoice num\r\n3 - Lowest invoice number that does not have status PAID\r\n4 - Lowest invoice number that does not have status PAID, IF none found, then return highest invoice number\r\n');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common','ipn','B','IPN_ALLOW_RESUBMIT','Allow ipn transactions to be resubmitted','1','This will allow you to simulate the transaction being run again.  Doing this will NOT reverse any payments posted, status changes, etc.');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common','ipn','B','IPN_ALLOW_DELETE','Allow ipn transactions to be deleted','1','Allow IPN transactions to be deleted from the LOG');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('automation','ipn','B','IPN_REQUIRE_AMOUNT_MATCH','IPN: Require receiving amount to match invoice','0','If this is yes, the amount recieved must match the invoice amount for the invoice match to be successful');
INSERT INTO %PREFIX%parameters (`parm_id`, `parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('', 'automation', 'helpdesk', 'B', 'HELPDESK_AUTO_VERBOSE', 'Helpdesk: Auto-import verbose', '1', 'If "Yes", phpCOIN will display the results of processing of each email message. If "No", phpCOIN will only display aggregate results.');
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.2.6', v_type='Upgrade';