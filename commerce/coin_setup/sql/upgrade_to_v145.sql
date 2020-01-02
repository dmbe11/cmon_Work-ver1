INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'summary', 'I', 'SUMMARY_PRODUCTS_ORDER_BY', 'Summary- Product Orders Sort Order', '1', 'Determine the sort order for product orders on the Summary page:\r\n1: Alphabetical by product name or description\r\n2: Reverse alphabetical by product name or description\r\n3: Number of products sold, low to high\r\n4: Number of products sold, high to low\r\n5: Value of products sold, low to high\r\n6: Value of products sold, high to low');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'summary', 'B', 'SUMMARY_INVOICES_BY_PRODUCT', 'Summary: Display products that have been invoiced', '1', 'If "Yes", phpCOIN will display a table with the number of times each invoiced product appears on an invoice, as well as the total value.');
INSERT INTO %PREFIX%parameters (`parm_group`, `parm_group_sub`, `parm_type`, `parm_name`, `parm_desc`, `parm_value`, `parm_notes`) VALUES ('common', 'summary', 'S', 'SUMMARY_INVOICES_BY_PRODUCT_IGNORE', 'Summary: Ignore these invoiced poducts', '', 'A pipe-seperated list of item codes on invoices that should be ignored when building the invoiced products summary.');
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.4.5', v_type='Upgrade';