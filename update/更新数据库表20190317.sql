-- 小程序增加应用ID，以前用全局小程序配置会有问题
ALTER TABLE diygw_wechat_config ADD COLUMN dashboard_id int(11) NULL DEFAULT NULL;