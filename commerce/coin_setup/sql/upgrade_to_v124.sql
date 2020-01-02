DELETE FROM %PREFIX%parameters WHERE parm_name='_PKG_MODE_DEMO';
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.2.4', v_type='Upgrade';