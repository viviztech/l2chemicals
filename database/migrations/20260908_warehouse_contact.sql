INSERT INTO settings (setting_group, setting_key, setting_value, field_type, is_public)
VALUES ('contact','warehouse_address','','textarea',1)
ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);
