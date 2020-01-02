ALTER table %PREFIX%products MODIFY prod_desc text NOT NULL;
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.6.1', v_type='Upgrade';