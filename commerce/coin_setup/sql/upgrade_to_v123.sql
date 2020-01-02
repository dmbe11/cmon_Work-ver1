ALTER table %PREFIX%invoices MODIFY invc_tax_01_percent decimal(5,3);
ALTER table %PREFIX%invoices MODIFY invc_tax_02_percent decimal(5,3);
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.2.3', v_type='Upgrade';