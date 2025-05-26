-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-04-17 08:09:00
-- 服务器版本： 5.6.50-log
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `app_iot`
--

-- --------------------------------------------------------

--
-- 表的结构 `qf_device_logs`
--

CREATE TABLE `qf_device_logs` (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `device_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '设备id',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '设备类型',
  `topic` varchar(255) DEFAULT NULL COMMENT '主题',
  `vtype` int(11) NOT NULL DEFAULT '0' COMMENT '0:ASCII类型1:HEX类型',
  `val` longtext NOT NULL COMMENT '值',
  `year` varchar(10) NOT NULL COMMENT '年',
  `month` varchar(10) NOT NULL COMMENT '月',
  `day` varchar(10) NOT NULL COMMENT '日',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `del` int(11) NOT NULL DEFAULT '0' COMMENT '0未删除1已删除',
  `del_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '删除人id',
  `del_time` datetime DEFAULT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备数据日志表';

-- --------------------------------------------------------

--
-- 表的结构 `qf_iot`
--

CREATE TABLE `qf_iot` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0TCP协议,1websocket,2MQTT',
  `login` int(11) NOT NULL DEFAULT '0' COMMENT '0单点登录,1多点登录',
  `name` varchar(32) NOT NULL COMMENT '名称',
  `remark` varchar(320) DEFAULT NULL COMMENT '备注',
  `code` varchar(320) NOT NULL COMMENT '自定义注册包',
  `recode` varchar(320) NOT NULL COMMENT '自定义回复包',
  `rtype` int(11) NOT NULL DEFAULT '0' COMMENT '0ASCII,1HEX',
  `val` varchar(32) NOT NULL COMMENT '数据key',
  `vtype` int(11) NOT NULL DEFAULT '0' COMMENT '0ASCII,1HEX',
  `forward` text COMMENT '数据转发',
  `http` text COMMENT 'http协议',
  `username` varchar(255) DEFAULT NULL COMMENT 'mqtt用户名',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `password_md5` varchar(255) DEFAULT NULL COMMENT '密码加密后的md5',
  `topic` varchar(255) DEFAULT NULL COMMENT 'mqtt主题',
  `action` varchar(255) DEFAULT NULL COMMENT 'mqtt的动作,subscribe publish all',
  `permission` varchar(255) DEFAULT NULL COMMENT '是否允许 allow deny',
  `year` varchar(32) NOT NULL,
  `month` varchar(32) NOT NULL,
  `day` varchar(32) NOT NULL,
  `date` date NOT NULL,
  `addtime` datetime NOT NULL,
  `person` int(11) NOT NULL DEFAULT '0' COMMENT '录入人',
  `del` int(11) NOT NULL DEFAULT '0' COMMENT '0未删除,1已删除',
  `del_id` int(11) NOT NULL DEFAULT '0',
  `del_time` datetime DEFAULT NULL,
  `filter` text COMMENT '过滤',
  `online` int(11) NOT NULL DEFAULT '0' COMMENT '1 在线 0 离线',
  `crontab` text COMMENT '定时下发',
  `directive` text COMMENT '被动回复',
  `log` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用设备数据日志0不启用1启用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='物联网云平台';

-- --------------------------------------------------------

--
-- 表的结构 `qf_keys`
--

CREATE TABLE `qf_keys` (
  `id` int(20) NOT NULL COMMENT 'id',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `api_key` varchar(50) NOT NULL COMMENT 'api_key',
  `api_secret` varchar(50) NOT NULL COMMENT 'api_secret',
  `year` varchar(20) NOT NULL COMMENT '年份',
  `month` varchar(20) NOT NULL COMMENT '月份',
  `day` varchar(20) NOT NULL COMMENT '日',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `expire` int(1) NOT NULL DEFAULT '0' COMMENT '0永不过期,1过期',
  `expire_time` date DEFAULT NULL COMMENT '过期时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0未启用,1已启用',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `del` int(10) NOT NULL DEFAULT '0' COMMENT '0未删除1已删除',
  `del_time` datetime DEFAULT NULL COMMENT '删除人id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='api_key';

-- --------------------------------------------------------

--
-- 表的结构 `qf_webhook_logs`
--

CREATE TABLE `qf_webhook_logs` (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `device_id` bigint(20) NOT NULL COMMENT '设备id',
  `url` text NOT NULL COMMENT 'url',
  `param` text NOT NULL COMMENT '设备数据',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0执行成功,1执行失败',
  `msg` text NOT NULL COMMENT '执行结果',
  `exception` text COMMENT '异常结果',
  `year` varchar(20) NOT NULL COMMENT '年份',
  `month` varchar(20) NOT NULL COMMENT '月份',
  `day` varchar(20) NOT NULL COMMENT '日期',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `del` int(11) NOT NULL DEFAULT '0' COMMENT '0未删除1已删除',
  `del_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '删除人id',
  `del_time` datetime DEFAULT NULL COMMENT '删除时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备webhook日志';

--
-- 转储表的索引
--

--
-- 表的索引 `qf_device_logs`
--
ALTER TABLE `qf_device_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id_device_id_del` (`device_id`,`del`);

--
-- 表的索引 `qf_iot`
--
ALTER TABLE `qf_iot`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `qf_keys`
--
ALTER TABLE `qf_keys`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `qf_webhook_logs`
--
ALTER TABLE `qf_webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id_device_id_del` (`device_id`,`del`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `qf_iot`
--
ALTER TABLE `qf_iot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `qf_keys`
--
ALTER TABLE `qf_keys`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
