INSERT INTO `%PREFIX%mail_contacts` (`mc_id`, `mc_name`, `mc_email`, `mc_status`) VALUES('', 'Abuse', 'abuse@%DOMAINNAME%', 1);
UPDATE %PREFIX%versions SET v_ts=%TIMESTAMP%, v_ver='v1.6.4', v_type='Upgrade';