-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2019-08-06 16:40:22
-- 服务器版本: 5.5.58
-- PHP 版本: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `testdwdb`
--

-- --------------------------------------------------------

--
-- 表的结构 `yl_admin`
--

CREATE TABLE IF NOT EXISTS `yl_admin` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `encrypt` char(20) NOT NULL COMMENT '验证加密',
  `realname` char(20) NOT NULL COMMENT '用户昵称',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_date` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` varchar(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_date` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_date` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '1=正常，0=禁止',
  `group_id` int(2) NOT NULL,
  `avatar` varchar(200) DEFAULT NULL,
  `app_access_token` varchar(32) DEFAULT NULL COMMENT 'app token',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `mobile` (`mobile`) USING BTREE,
  KEY `app_access_token` (`app_access_token`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员表' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_admin_log`
--

CREATE TABLE IF NOT EXISTS `yl_admin_log` (
  `uid` int(11) NOT NULL COMMENT '操作人id',
  `log_info` varchar(255) NOT NULL DEFAULT '' COMMENT '用户操作行为描述',
  `log_time` int(11) NOT NULL COMMENT '添加时间',
  `log_ip` char(15) DEFAULT NULL,
  `log_url` varchar(255) NOT NULL COMMENT '操作url',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=正常，1=删除',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='管理员操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `yl_admin_menu`
--

CREATE TABLE IF NOT EXISTS `yl_admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` char(100) NOT NULL DEFAULT '' COMMENT '标题',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `m` char(100) NOT NULL DEFAULT '' COMMENT '链接地址',
  `c` char(100) NOT NULL,
  `a` char(100) NOT NULL,
  `data` char(200) NOT NULL COMMENT '附加参数',
  `is_show` tinyint(1) unsigned NOT NULL COMMENT '是否显示1显示，0隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `icon_class` char(50) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=删除 0=未删除',
  PRIMARY KEY (`id`),
  KEY `pid` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员后台菜单' AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_area`
--

CREATE TABLE IF NOT EXISTS `yl_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int(11) DEFAULT NULL,
  `name` varchar(30) NOT NULL,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='地区表' AUTO_INCREMENT=3457 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_area_map`
--

CREATE TABLE IF NOT EXISTS `yl_area_map` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '区域划分ID',
  `area_name` varchar(255) NOT NULL DEFAULT '' COMMENT '区域名称',
  `employee_num` varchar(50) NOT NULL DEFAULT '' COMMENT '区域员工数',
  `coordinate` text NOT NULL COMMENT '区域坐标',
  `add_time` bigint(20) NOT NULL COMMENT '添加时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1=显示 0=隐藏',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=是 0=否',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='区域表' AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_attachment`
--

CREATE TABLE IF NOT EXISTS `yl_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned DEFAULT '0' COMMENT '用户ID',
  `admin_uid` int(10) DEFAULT NULL,
  `name` char(100) DEFAULT '' COMMENT '附件显示名',
  `savename` char(100) DEFAULT NULL COMMENT '文件名',
  `type` varchar(255) DEFAULT '0' COMMENT '附件类型',
  `ext` char(50) DEFAULT NULL,
  `record_id` int(10) unsigned DEFAULT '0' COMMENT '关联记录ID',
  `size` bigint(20) unsigned DEFAULT '0' COMMENT '附件大小',
  `path` varchar(200) DEFAULT NULL COMMENT '目录',
  `url` varchar(200) DEFAULT '0' COMMENT 'url完整路径',
  `addtime` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `album_id` int(10) DEFAULT NULL COMMENT '相册id',
  `thumb` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='附件表' AUTO_INCREMENT=183 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_attendance`
--

CREATE TABLE IF NOT EXISTS `yl_attendance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` int(11) NOT NULL COMMENT '设备id',
  `leave_id` bigint(20) NOT NULL COMMENT '请假id',
  `userid` bigint(20) NOT NULL COMMENT '员工id',
  `add_time` int(11) NOT NULL COMMENT '考勤创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `first_time` int(11) DEFAULT '0' COMMENT '第一次打卡时间',
  `first_result` tinyint(1) DEFAULT '0' COMMENT '第一次打卡结果1=上班打卡，2=下班打卡，3=迟到，4=早退，5= 旷工',
  `second_time` int(11) DEFAULT '0' COMMENT '第二次打卡时间',
  `second_result` tinyint(1) DEFAULT '0' COMMENT '第二次打卡结果1=上班打卡，2=下班打卡，3=迟到，4=早退，5= 旷工',
  `third_time` int(11) NOT NULL DEFAULT '0',
  `third_result` tinyint(1) NOT NULL DEFAULT '0',
  `fourth_time` int(11) NOT NULL DEFAULT '0',
  `fourth_result` tinyint(1) NOT NULL DEFAULT '0',
  `num` int(11) NOT NULL DEFAULT '0',
  `remark` text COMMENT '本次考勤备注',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除1=是，0=否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='考勤表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_attendance_setting`
--

CREATE TABLE IF NOT EXISTS `yl_attendance_setting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `late_time` int(11) NOT NULL COMMENT '迟到时间',
  `leave_time` int(11) NOT NULL COMMENT '早退时间',
  `absenteeism_time` int(11) NOT NULL COMMENT '旷工时间',
  `distance_time` int(11) NOT NULL COMMENT '远离区域告警延迟时间',
  `still_time_interval` int(11) NOT NULL DEFAULT '600' COMMENT '静止报警时间间隔',
  `still_time` int(11) NOT NULL DEFAULT '1800' COMMENT '静止告警延迟时间',
  `site_time` int(11) NOT NULL COMMENT '上传时间间隔',
  `electric_quantity` int(11) NOT NULL COMMENT '电量',
  `electric_time` int(11) NOT NULL COMMENT '电量报警上传时间间隔',
  `clock_time` int(10) NOT NULL COMMENT '允许最早的打卡时间',
  `uid` bigint(20) NOT NULL COMMENT '创建人',
  `add_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `operation_season` tinyint(1) NOT NULL DEFAULT '1' COMMENT '运行季节1=冬季，2-夏季',
  `error_time` int(11) NOT NULL COMMENT '误差时间',
  `shake_frequency` tinyint(2) NOT NULL COMMENT '0=关，1=高，2=标准，3=低',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_auth_group`
--

CREATE TABLE IF NOT EXISTS `yl_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  `menu_ids` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理组表' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_auth_group_access`
--

CREATE TABLE IF NOT EXISTS `yl_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '管理员id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '管理组id',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `adminid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `yl_auth_rule`
--

CREATE TABLE IF NOT EXISTS `yl_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-url;2-主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  `parent_id` int(10) NOT NULL,
  `sort` int(10) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=删除 0=未删除',
  PRIMARY KEY (`id`),
  KEY `module` (`status`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_lbs`
--

CREATE TABLE IF NOT EXISTS `yl_lbs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `req_mac` varchar(20) NOT NULL COMMENT '请求mac地址',
  `lat` decimal(10,7) NOT NULL COMMENT '纬度',
  `lon` decimal(10,7) NOT NULL COMMENT '经度',
  `add_time` int(11) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_leave`
--

CREATE TABLE IF NOT EXISTS `yl_leave` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '请假表主键',
  `leave_start_time` int(11) NOT NULL COMMENT '请假开始时间',
  `leave_end_time` int(11) NOT NULL COMMENT '请假结束时间',
  `add_time` int(11) NOT NULL COMMENT '创建时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除1=是，0=否',
  `userid` int(11) NOT NULL COMMENT '请假人id',
  `uid` int(11) NOT NULL DEFAULT '1' COMMENT '创建人id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='请假表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_loc_log`
--

CREATE TABLE IF NOT EXISTS `yl_loc_log` (
  `machine_imei` char(32) NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `add_time` int(11) NOT NULL,
  `electricity` int(11) NOT NULL,
  `machine_status` varchar(20) DEFAULT NULL,
  `server_utc_date` varchar(40) DEFAULT NULL,
  `device_utc_date` varchar(40) DEFAULT NULL,
  `baidu_lat` decimal(10,5) DEFAULT NULL,
  `baidu_lng` decimal(10,5) DEFAULT NULL,
  `data_context` varchar(10) DEFAULT NULL,
  `is_stop` tinyint(1) DEFAULT NULL,
  `stop_time_minute` int(11) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `data_type` tinyint(1) NOT NULL,
  `distance` decimal(10,4) NOT NULL,
  `speed` decimal(10,2) NOT NULL,
  KEY `machine_imei` (`machine_imei`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='位置记录表';

-- --------------------------------------------------------

--
-- 表的结构 `yl_machine`
--

CREATE TABLE IF NOT EXISTS `yl_machine` (
  `machine_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `uid` int(10) NOT NULL DEFAULT '1' COMMENT '创建人id',
  `machine_name` varchar(40) NOT NULL COMMENT '设备名称',
  `machine_imei` char(32) NOT NULL COMMENT '设备IMEI',
  `center_tel` varchar(20) DEFAULT '' COMMENT '设备中心号码',
  `mid` varchar(20) DEFAULT '' COMMENT '设备id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=是，0=否',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '绑定员工id',
  `area_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '区域ID',
  `version_number` varchar(20) NOT NULL COMMENT '版本号',
  `plmn` varchar(80) NOT NULL COMMENT '运营商编号',
  `item` varchar(20) NOT NULL COMMENT '型号',
  `owner` varchar(20) NOT NULL COMMENT 'sim卡号',
  `schedules_id` bigint(20) NOT NULL DEFAULT '3' COMMENT '班组id',
  `electricity` int(11) NOT NULL DEFAULT '0' COMMENT '电量',
  `machine_status` varchar(20) NOT NULL COMMENT '设备状态stop=静止；move=移动,Offline=离线',
  `server_utc_time` varchar(40) NOT NULL COMMENT '设备服务器时间',
  `device_utc_time` varchar(40) NOT NULL COMMENT '设备信息上传时间',
  `b_lat` decimal(10,5) NOT NULL COMMENT '纬度',
  `b_lon` decimal(10,5) NOT NULL COMMENT '纬度',
  PRIMARY KEY (`machine_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='设备表' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_machine_bind`
--

CREATE TABLE IF NOT EXISTS `yl_machine_bind` (
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `machine_id` int(11) DEFAULT NULL COMMENT '设备表主键id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1=绑定 ，0=解绑'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='设备用户绑定表';

-- --------------------------------------------------------

--
-- 表的结构 `yl_member`
--

CREATE TABLE IF NOT EXISTS `yl_member` (
  `userid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `update_time` int(11) DEFAULT NULL,
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `username` char(20) DEFAULT NULL COMMENT '用户名',
  `password` char(32) DEFAULT NULL,
  `encrypt` char(6) DEFAULT NULL,
  `realname` varchar(40) NOT NULL COMMENT '真实姓名',
  `job_number` varchar(20) NOT NULL COMMENT '员工号',
  `machine_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备表主键id',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别，1=男，2=女',
  `position` tinyint(1) DEFAULT '1' COMMENT '职位1=养护工人，2=养护经理',
  `group_id` varchar(255) DEFAULT NULL COMMENT '主营业务',
  `reg_date` bigint(20) DEFAULT NULL COMMENT '注册时间',
  `last_date` bigint(20) DEFAULT NULL COMMENT '最后登录时间',
  `reg_ip` varchar(20) DEFAULT '' COMMENT '注册ip',
  `last_ip` varchar(20) DEFAULT '' COMMENT '最后登录ip',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定，1=锁定，0=正常',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1:删除,0:正常',
  `job_status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '在岗状态1=在岗，0=离岗，2=未上岗',
  `parent_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '养护经理id',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_member_group`
--

CREATE TABLE IF NOT EXISTS `yl_member_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '状态1=开启，2关闭',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户组' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_member_position`
--

CREATE TABLE IF NOT EXISTS `yl_member_position` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL COMMENT '职位名称',
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除1-是，0=否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_module_extend`
--

CREATE TABLE IF NOT EXISTS `yl_module_extend` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `pingxx_secret_key` varchar(200) DEFAULT NULL,
  `pingxx_app_id` varchar(200) DEFAULT NULL,
  `jpush_app_key` varchar(200) DEFAULT NULL,
  `jpush_master_secret` varchar(200) DEFAULT NULL,
  `ucpaas_app_id` varchar(200) DEFAULT NULL,
  `ucpaas_account_sid` varchar(200) DEFAULT NULL,
  `ucpaas_auth_token` varchar(200) DEFAULT NULL,
  `qcloud_app_id` varchar(200) DEFAULT NULL COMMENT '腾讯云appid',
  `qcloud_app_key` varchar(200) DEFAULT NULL COMMENT '腾讯云appkey',
  `faceverify_app_key` varchar(200) DEFAULT NULL,
  `faceverify_app_secret` varchar(200) DEFAULT NULL,
  `faceverify_level` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='第三方模块配置' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_schedules_setting`
--

CREATE TABLE IF NOT EXISTS `yl_schedules_setting` (
  `schedules_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '排班ID',
  `schedules_name` varchar(255) NOT NULL COMMENT '排班名称',
  `work_day` varchar(50) NOT NULL COMMENT '工作日  1=周一 2=周二 。。。 0=周日',
  `time_id` varchar(255) DEFAULT NULL COMMENT '时间段',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1=显示 0=不显示',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=删除 2=未删除',
  PRIMARY KEY (`schedules_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_schedules_time`
--

CREATE TABLE IF NOT EXISTS `yl_schedules_time` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '排班时间设置表',
  `title` varchar(255) NOT NULL COMMENT '时间段名称',
  `start_time` varchar(20) NOT NULL COMMENT '上班时间',
  `end_time` varchar(20) NOT NULL COMMENT '下班时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1=显示 0=不显示',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=删除 0=不删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_site_log`
--

CREATE TABLE IF NOT EXISTS `yl_site_log` (
  `machine_id` int(11) NOT NULL COMMENT '设备id',
  `device_utc_date` varchar(40) NOT NULL,
  `machine_imei` char(32) NOT NULL,
  `lat` decimal(10,5) NOT NULL COMMENT '纬度',
  `lon` decimal(10,5) NOT NULL COMMENT '经度',
  `add_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=在围栏，0=不在围栏',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=上午，2=下午',
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `server_utc_date` varchar(40) NOT NULL,
  KEY `machine_imei` (`machine_imei`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='位置记录表';

-- --------------------------------------------------------

--
-- 表的结构 `yl_site_setting`
--

CREATE TABLE IF NOT EXISTS `yl_site_setting` (
  `id` int(10) NOT NULL,
  `site_name` char(100) NOT NULL,
  `site_title` char(100) NOT NULL,
  `site_logo` char(200) NOT NULL,
  `site_keywords` varchar(200) NOT NULL,
  `site_description` varchar(200) NOT NULL,
  `site_url` char(100) NOT NULL,
  `icp_number` char(100) NOT NULL,
  `site_status` tinyint(1) NOT NULL COMMENT '站点状态，1=开启，2=关闭',
  `closed_reason` varchar(200) NOT NULL COMMENT '关闭原因',
  `hot_search` varchar(200) NOT NULL COMMENT '默认热门搜索',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点设置';

-- --------------------------------------------------------

--
-- 表的结构 `yl_sms`
--

CREATE TABLE IF NOT EXISTS `yl_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` char(15) CHARACTER SET latin1 NOT NULL,
  `code` char(8) CHARACTER SET latin1 NOT NULL,
  `send_time` int(10) NOT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1=正常，0=超时失效，99=使用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='短信验证码' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `yl_warning_message`
--

CREATE TABLE IF NOT EXISTS `yl_warning_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `add_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '警告时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '警告类型 1: 不在工作区域，2=远离区域报警，3=低电量报警，4=迟到，5=旷工，6=早退',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '报警结束时间',
  `machine_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='报警表' AUTO_INCREMENT=1003 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
