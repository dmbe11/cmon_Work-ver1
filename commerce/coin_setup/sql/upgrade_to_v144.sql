ALTER table %PREFIX%clients MODIFY cl_user_name varchar(100);
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'clients', 'B', 'Username_AlphaNum', 'Allow Only AlphaNumeric UserName', '0', 'Determines if the username must contain only letters and numbers, or if special characters such as pound and underscore are allowed');
INSERT INTO %PREFIX%parameters (parm_group, parm_group_sub, parm_type, parm_name, parm_desc, parm_value, parm_notes) VALUES ('common', 'invoices', 'I', 'TAX_DISPLAY_DIGITS_PERCENT', 'Display Digits For Tax Percent', '3', 'How many digits after the decimal place should we display for the tax percentages?');
INSERT INTO %PREFIX%parameters (parm_group, parm_group_sub, parm_type, parm_name, parm_desc, parm_value, parm_notes) VALUES ('common', 'invoices', 'I', 'TAX_DISPLAY_DIGITS_AMOUNT', 'Display Digits For Tax Amount', '2', 'How many digits after the decimal place should we display for the tax amounts?');
INSERT INTO %PREFIX%parameters (parm_group, parm_group_sub, parm_type, parm_name, parm_desc, parm_value, parm_notes) VALUES ('common', 'invoices', 'I', 'CURRENCY_DISPLAY_DIGITS_AMOUNT', 'Display Digits For Currency Amounts', '2', 'How many digits after the decimal place should we display for currency (non-tax) amounts?');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'orders', 'B', 'ORDERS_TOS_IN_IFRAME', 'Display TOS in Iframe', '1', 'Whether or not to show the TOS in an iframe on the orders page, underneath the "Accept TOS" checkbox');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'orders', 'B', 'ORDERS_AUP_IN_IFRAME', 'Display AUP in Iframe', '1', 'Whether or not to show the AUP in an iframe on the orders page, underneath the "Accept AUP" checkbox');
ALTER table %PREFIX%invoices MODIFY invc_total_cost double;
ALTER table %PREFIX%invoices MODIFY invc_total_paid double;
ALTER table %PREFIX%invoices MODIFY invc_subtotal_cost double;
ALTER table %PREFIX%invoices MODIFY invc_tax_01_amount double;
ALTER table %PREFIX%invoices MODIFY invc_tax_01_percent double;
ALTER table %PREFIX%invoices MODIFY invc_tax_02_amount double;
ALTER table %PREFIX%invoices MODIFY invc_tax_02_percent double;
ALTER table %PREFIX%invoices_items MODIFY ii_item_cost double;
ALTER table %PREFIX%invoices_trans MODIFY it_amount double;
ALTER table %PREFIX%orders MODIFY ord_unit_cost double;
ALTER table %PREFIX%products MODIFY prod_unit_cost double;
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.4.4', v_type='Upgrade';