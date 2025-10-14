/*
 Source Server         : 坤典物联网iot
 Source Server Type    : MySQL
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for kd_admin
-- ----------------------------
DROP TABLE IF EXISTS `kd_admin`;
CREATE TABLE `kd_admin`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `status` bigint(20) NULL DEFAULT 1 COMMENT '状态1使用0禁用',
  `is_del` bigint(20) NOT NULL DEFAULT 0 COMMENT '删除',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-账号' ROW_FORMAT = Dynamic;
-- ----------------------------
-- Records of kd_admin
-- ----------------------------
INSERT INTO `kd_admin` VALUES (1, 'admin', '18723237733', '$2y$10$WI6HZ6o1AbC0EQ41UBlUDulhqVaqxDqe8q06MQ5QF8UWrsT6mrE86', 1, 0, '2025-05-27 16:01:20');

-- ----------------------------
-- Table structure for kd_alarm
-- ----------------------------
DROP TABLE IF EXISTS `kd_alarm`;
CREATE TABLE `kd_alarm`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '报警配置名称',
  `push_type` int(1) NULL DEFAULT NULL COMMENT '推送类型【1设备，2模版触发器，3独立触发器】',
  `related_id` int(1) NULL DEFAULT NULL COMMENT '关联设备，触发器',
  `push_mechanism` int(1) NULL DEFAULT NULL COMMENT '推送机制【1；一次推送，2报警沉默时间】',
  `space` int(1) NULL DEFAULT NULL COMMENT '间隔分钟数',
  `push_way` int(1) NULL DEFAULT NULL COMMENT '推送方式【1：短信，2：邮件，3企业微信】',
  `push_human` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '推送人',
  `describe` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-报警' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_alarm_independence_trigger
-- ----------------------------
DROP TABLE IF EXISTS `kd_alarm_independence_trigger`;
CREATE TABLE `kd_alarm_independence_trigger`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发器名称',
  `trigger_type` int(1) NULL DEFAULT NULL COMMENT '触发器类型【1设备】',
  `device_id` int(11) NULL DEFAULT 1 COMMENT '设备ID',
  `subordinate_id` int(11) NULL DEFAULT NULL COMMENT '从机ID',
  `subordinate_variable_id` int(11) NULL DEFAULT NULL COMMENT '变量ID',
  `condition` int(1) NULL DEFAULT NULL COMMENT '触发条件【0：开关off，1：开关on，2：数值小于A，3：数值大于B，4：数值大于A且小于B，5数值小于A或大于B，6数值等于A】',
  `condition_parameter` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '条件参数',
  `dead_zone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '报警死区',
  `is_alarm` int(1) NULL DEFAULT NULL COMMENT '是否报警【0不推送，1推送】',
  `alarm_push` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '报警推送',
  `resume_push` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复推送',
  `is_linkage` int(1) NULL DEFAULT 0 COMMENT '是否联动【0不联动，1联动】',
  `linkage_type` int(1) NULL DEFAULT NULL COMMENT '联动类型【1采集，2控制】',
  `linkage_device_id` int(11) NULL DEFAULT NULL COMMENT '联动设备',
  `linkage_subordinate_id` int(1) NULL DEFAULT NULL COMMENT '联动从机',
  `linkage_subordinate_variable_id` int(1) NULL DEFAULT NULL COMMENT '联动变量',
  `control_type` int(1) NULL DEFAULT NULL COMMENT '控制类型【1手动下发，2报警值】',
  `number` int(11) NULL DEFAULT NULL COMMENT '手动下发数值',
  `status` int(1) NULL DEFAULT 0 COMMENT '设备状态【0关闭，1开启】',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-独立触发器' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_alarm_linkage_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_alarm_linkage_log`;
CREATE TABLE `kd_alarm_linkage_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `trigger_id` int(11) NULL DEFAULT NULL,
  `device_id` int(11) NULL DEFAULT NULL,
  `variable_id` int(11) NULL DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `trigger_type` int(11) NULL DEFAULT NULL COMMENT '触发器类型【1模版触发器，2独立触发器】',
  `trigger_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发器名称',
  `subordinate_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '从机名称',
  `variable_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量名称',
  `trigger_condition` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发条件',
  `trigger_device` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发设备',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备报警记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_alarm_template_trigger
-- ----------------------------
DROP TABLE IF EXISTS `kd_alarm_template_trigger`;
CREATE TABLE `kd_alarm_template_trigger`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发器名称',
  `template_id` int(11) NULL DEFAULT 1 COMMENT '模版ID',
  `template_subordinate_id` int(11) NULL DEFAULT NULL COMMENT '模版从机ID',
  `template_subordinate_variable_id` int(11) NULL DEFAULT NULL COMMENT '模版变量ID',
  `condition` int(1) NULL DEFAULT NULL COMMENT '触发条件【0：开关off，1：开关on，2：数值小于A，3：数值大于B，4：数值大于A且小于B，5数值小于A或大于B，6数值等于A】',
  `condition_parameter` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '条件参数',
  `dead_zone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '报警死区',
  `is_alarm` int(1) NULL DEFAULT NULL COMMENT '是否报警【0不推送，1推送】',
  `alarm_push` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '报警推送',
  `resume_push` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复推送',
  `is_linkage` int(1) NULL DEFAULT 0 COMMENT '是否联动【0不联动，1联动】',
  `linkage_type` int(1) NULL DEFAULT NULL COMMENT '联动类型【1采集，2控制】',
  `linkage_subordinate_id` int(1) NULL DEFAULT NULL COMMENT '联动从机',
  `linkage_subordinate_variable_id` int(1) NULL DEFAULT NULL COMMENT '联动变量',
  `control_type` int(1) NULL DEFAULT NULL COMMENT '控制类型【1手动下发，2报警值】',
  `number` int(11) NULL DEFAULT NULL COMMENT '手动下发数值',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-模版触发器' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_alarm_warning_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_alarm_warning_log`;
CREATE TABLE `kd_alarm_warning_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `trigger_id` int(11) NULL DEFAULT NULL,
  `device_id` int(11) NULL DEFAULT NULL,
  `variable_id` int(11) NULL DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `trigger_type` int(11) NULL DEFAULT NULL COMMENT '触发器类型【1模版触发器，2独立触发器】',
  `trigger_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发器名称',
  `subordinate_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '从机名称',
  `variable_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量名称',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量值',
  `trigger_condition` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '触发条件',
  `is_warning` int(1) NULL DEFAULT 0 COMMENT '是否预警0未预警1预警',
  `status` int(1) NULL DEFAULT 0 COMMENT '0未处理，1已处理',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备联动记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device
-- ----------------------------
DROP TABLE IF EXISTS `kd_device`;
CREATE TABLE `kd_device`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `gateway_id` int(11) NULL DEFAULT NULL COMMENT '网关ID',
  `template_id` int(11) NULL DEFAULT NULL COMMENT '设备模版ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备编号',
  `describe` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备描述',
  `label_ids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标签ID',
  `location` int(1) NULL DEFAULT 1 COMMENT '设备位置1手动定位，2网关定位',
  `longitude` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '经度',
  `latitude` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纬度',
  `is_warning` int(1) NULL DEFAULT 0 COMMENT '是否预警0未预警1预警',
  `last_time` datetime NULL DEFAULT NULL COMMENT '最近上报时间',
  `status` int(1) NULL DEFAULT 0 COMMENT '状态0未在线，1在线',
  `is_del` int(1) NULL DEFAULT 0 COMMENT '删除0未删除，1删除',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_logs
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_logs`;
CREATE TABLE `kd_device_logs`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `device_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '设备id',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '设备类型',
  `topic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '主题',
  `vtype` int(11) NOT NULL DEFAULT 0 COMMENT '0:ASCII类型1:HEX类型',
  `val` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值',
  `year` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '年',
  `month` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '月',
  `day` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `del` int(11) NOT NULL DEFAULT 0 COMMENT '0未删除1已删除',
  `del_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '删除人id',
  `del_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `member_id_device_id_del`(`device_id`, `del`) USING BTREE,
  INDEX `idx_device_time`(`device_id`, `year`, `month`, `day`, `del`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 202507141246419797 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备数据日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_online_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_online_log`;
CREATE TABLE `kd_device_online_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `device_id` int(11) NULL DEFAULT NULL COMMENT '设备ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` int(1) NULL DEFAULT 0 COMMENT '状态0未在线，1在线',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 573 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_product
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_product`;
CREATE TABLE `kd_device_product`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `protocol` int(1) NULL DEFAULT NULL COMMENT '从机协议1modbusRTU2自定义协议',
  `status` int(1) NULL DEFAULT 0 COMMENT '状态0启用，1停用',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-产品库' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_product_variable
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_product_variable`;
CREATE TABLE `kd_device_product_variable`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号（0默认模版）',
  `product_id` int(11) NULL DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量名称',
  `unit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '单位',
  `type` int(1) NULL DEFAULT 1 COMMENT '变量类型1直采变量，2运算变量，3录入变量',
  `register_mark` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '寄存器标志ModbusRTUS【0,1,3,4】自定义协议【do，di】',
  `register_address` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '寄存器地址',
  `data_format` int(1) NULL DEFAULT 1 COMMENT '数据格式【9:32位无符号整数(ABCD),10:32位无符号整数(DCBA),11:32位无符号整数(BADC),12:32位无符号整数(CDAB)13:32位有符号整数(ABCD),14:32位有符号整数(DCBA),15:32位有符号整数(BADC),16:32位有符号整数(CDAB)17:32位浮点数(ABCD),18:32位浮点数(DCBA),19:32位浮点数(BADC),20:32位浮点数(CDAB)\r\n21:64位无符号整数(ABCD),22:64位无符号整数(DCBA),23:64位无符号整数(BADC),24:64位无符号整数(CDAB) 25:64位有符号整数(ABCD),26:64位有符号整数(DCBA),27:64位有符号整数(BADC),28:64位有符号整数(CDAB) 29:64位浮点数(ABCD),30:64位浮点数(DCBA),31:64位浮点数(BADC),32:64位浮点数(CDAB)\r\n33:位】',
  `collect_frequency` int(1) NULL DEFAULT 0 COMMENT '采集频率【0：不采集，1:1分钟，2:3分钟，3:5分钟，4:10分钟，5:15分钟，6:20分钟，7:30分钟，8:1小时，9:5小时，10:1天，\r\n11:5天，12:15天】',
  `fraction` int(1) NULL DEFAULT NULL COMMENT '数字格式【0：整数，1:一位,2:二位,3:三位,4:四位,】',
  `storage_mode` int(1) NULL DEFAULT 1 COMMENT '储存方式【1变化储存，2全部储存】',
  `read_write_mode` int(1) NULL DEFAULT NULL COMMENT '读写模式【1只读，2读写】',
  `collect_formula` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '采集公式',
  `contro_formula` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '控制公式',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 58 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备从机变量' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_subordinate
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_subordinate`;
CREATE TABLE `kd_device_subordinate`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `device_id` int(11) NULL DEFAULT NULL COMMENT '设备ID',
  `template_subordinate_id` int(11) NULL DEFAULT NULL COMMENT '模版从机ID',
  `serial_port` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '串口序号',
  `subordinate_address` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '从机地址',
  `is_del` int(1) NULL DEFAULT 0,
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备变量' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_subordinate_variable
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_subordinate_variable`;
CREATE TABLE `kd_device_subordinate_variable`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `device_id` int(11) NULL DEFAULT NULL COMMENT '设备id',
  `subordinate_id` int(11) NULL DEFAULT NULL COMMENT '从机ID',
  `template_subordinate_variable_id` int(11) NULL DEFAULT NULL COMMENT '变量模版ID',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '当前值',
  `is_warning` int(1) NULL DEFAULT 0 COMMENT '是否预警',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量名称',
  `type` int(1) NULL DEFAULT 1 COMMENT '变量类型1直采变量，2运算变量，3录入变量',
  `last_time` datetime NULL DEFAULT NULL COMMENT '最近更新当前值时间',
  `is_del` int(1) NULL DEFAULT 0,
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 176 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备从机变量' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_subordinate_variable_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_subordinate_variable_log`;
CREATE TABLE `kd_device_subordinate_variable_log`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `device_id` bigint(20) NULL DEFAULT 0 COMMENT '设备id',
  `gateway_id` int(11) NULL DEFAULT NULL COMMENT '网关ID',
  `subordinate_id` int(11) NULL DEFAULT NULL COMMENT '从机ID',
  `variable_id` int(11) NULL DEFAULT NULL COMMENT '变量ID',
  `template_subordinate_variable_id` int(11) NULL DEFAULT NULL COMMENT '变量模版id',
  `gateway_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网关编号',
  `subordinate_address` int(11) NULL DEFAULT NULL COMMENT '从机地址',
  `register_address` int(11) NULL DEFAULT NULL COMMENT '寄存器地址',
  `function_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作 3读取',
  `val` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '值',
  `create_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `member_id_device_id_del`(`device_id`) USING BTREE,
  INDEX `idx_device_time`(`device_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 79967 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备数据日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_template
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_template`;
CREATE TABLE `kd_device_template`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号（0默认模版）',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模版名称',
  `collect` int(1) NULL DEFAULT 1 COMMENT '采集方式1云端轮询',
  `status_config` int(1) NULL DEFAULT NULL COMMENT '状态配置1网关2设备数据',
  `space_time` int(11) NULL DEFAULT NULL COMMENT '时长分钟数（status_config=2 通过数据判断设备是否在线）',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备模版' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_template_subordinate
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_template_subordinate`;
CREATE TABLE `kd_device_template_subordinate`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号（0默认模版）',
  `product_id` int(11) NULL DEFAULT NULL COMMENT '产品ID',
  `template_id` int(11) NULL DEFAULT NULL COMMENT '模版ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '从机名称',
  `protocol` int(1) NULL DEFAULT NULL COMMENT '从机协议1modbusRTU2自定义协议',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备从机' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_device_template_subordinate_variable
-- ----------------------------
DROP TABLE IF EXISTS `kd_device_template_subordinate_variable`;
CREATE TABLE `kd_device_template_subordinate_variable`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号（0默认模版）',
  `template_id` int(11) NULL DEFAULT NULL COMMENT '模版ID',
  `template_subordinate_id` int(11) NULL DEFAULT NULL COMMENT '模版从机ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '变量名称',
  `unit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '单位',
  `type` int(1) NULL DEFAULT 1 COMMENT '变量类型1直采变量，2运算变量，3录入变量',
  `register_mark` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '寄存器标志ModbusRTUS【0,1,3,4】自定义协议【do，di】',
  `register_address` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '寄存器地址',
  `data_format` int(1) NULL DEFAULT 1 COMMENT '数据格式【9:32位无符号整数(ABCD),10:32位无符号整数(DCBA),11:32位无符号整数(BADC),12:32位无符号整数(CDAB)13:32位有符号整数(ABCD),14:32位有符号整数(DCBA),15:32位有符号整数(BADC),16:32位有符号整数(CDAB)17:32位浮点数(ABCD),18:32位浮点数(DCBA),19:32位浮点数(BADC),20:32位浮点数(CDAB)\r\n21:64位无符号整数(ABCD),22:64位无符号整数(DCBA),23:64位无符号整数(BADC),24:64位无符号整数(CDAB) 25:64位有符号整数(ABCD),26:64位有符号整数(DCBA),27:64位有符号整数(BADC),28:64位有符号整数(CDAB) 29:64位浮点数(ABCD),30:64位浮点数(DCBA),31:64位浮点数(BADC),32:64位浮点数(CDAB)\r\n33:位】',
  `collect_frequency` int(1) NULL DEFAULT 0 COMMENT '采集频率【0：不采集，1:1分钟，2:3分钟，3:5分钟，4:10分钟，5:15分钟，6:20分钟，7:30分钟，8:1小时，9:5小时，10:1天，\r\n11:5天，12:15天】',
  `fraction` int(1) NULL DEFAULT NULL COMMENT '数字格式【0：整数，1:一位,2:二位,3:三位,4:四位,】',
  `storage_mode` int(1) NULL DEFAULT 1 COMMENT '储存方式【1变化储存，2全部储存】',
  `read_write_mode` int(1) NULL DEFAULT NULL COMMENT '读写模式【1只读，2读写】',
  `collect_formula` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '采集公式',
  `contro_formula` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '控制公式',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 61 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-设备从机变量' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway`;
CREATE TABLE `kd_gateway`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `marque_id` int(11) NULL DEFAULT NULL COMMENT '网关型号ID',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '0TCP协议,1websocket,2MQTT',
  `login` int(11) NULL DEFAULT 0 COMMENT '0单点登录,1多点登录',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mac/IMEI',
  `gateway_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网关密码（at指令使用）',
  `network` int(1) NULL DEFAULT NULL COMMENT '网络类型【1:2g/3g/4g/5g,2:wifi,3:NB-loT,4:以太网,5:其他】',
  `locate` int(1) NULL DEFAULT NULL COMMENT '定位类型【1:手动,2:自动】',
  `longitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '经度',
  `latitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '纬度',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '地址',
  `label_ids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标签多个逗号分隔',
  `remark` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `code` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义注册包',
  `recode` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义回复包',
  `rtype` int(11) NULL DEFAULT 0 COMMENT '0ASCII,1HEX',
  `val` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '数据key（无用）',
  `vtype` int(11) NULL DEFAULT 0 COMMENT '0ASCII,1HEX（无用）',
  `forward` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据转发',
  `http` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'http协议',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `password_md5` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码加密后的md5',
  `topic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt主题',
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt的动作,subscribe publish all',
  `permission` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '是否允许 allow deny',
  `del` int(11) NULL DEFAULT 0 COMMENT '0未删除,1已删除',
  `del_id` int(11) NULL DEFAULT 0,
  `del_time` datetime NULL DEFAULT NULL,
  `filter` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '过滤',
  `online` int(11) NULL DEFAULT -1 COMMENT '-1等待初始上线 1 在线 0 离线',
  `crontab` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '定时下发',
  `directive` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '被动回复',
  `log` int(1) NULL DEFAULT 1 COMMENT '是否启用设备数据日志0不启用1启用',
  `is_warning` int(1) NULL DEFAULT 0 COMMENT '是否预警0未预警1预警',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `mac`(`mac`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway_instruct
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway_instruct`;
CREATE TABLE `kd_gateway_instruct`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `type` int(1) NULL DEFAULT NULL COMMENT '命令类型1直接发送，2带参数命令',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关命令列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway_log`;
CREATE TABLE `kd_gateway_log`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `gateway_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '网关id',
  `admin_id` int(11) NULL DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '网关类型',
  `val` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '值',
  `year` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '年',
  `month` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '月',
  `day` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日',
  `date` date NOT NULL COMMENT '日期',
  `create_time` datetime NOT NULL COMMENT '时间',
  `del` int(11) NOT NULL DEFAULT 0 COMMENT '0未删除1已删除',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `member_id_device_id_del`(`gateway_id`, `del`) USING BTREE,
  INDEX `idx_device_time`(`gateway_id`, `year`, `month`, `day`, `del`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 145723 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关全日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway_marque
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway_marque`;
CREATE TABLE `kd_gateway_marque`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号（0默认模版）',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '型号名称',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关型号' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway_marque_instruct
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway_marque_instruct`;
CREATE TABLE `kd_gateway_marque_instruct`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `marque_id` int(11) NULL DEFAULT NULL COMMENT '关联型号ID',
  `instruct_id` int(11) NULL DEFAULT NULL COMMENT '关联命令ID',
  `command` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '命令',
  `parameter` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '默认参数',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关型号对应命令' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_gateway_online_log
-- ----------------------------
DROP TABLE IF EXISTS `kd_gateway_online_log`;
CREATE TABLE `kd_gateway_online_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `gateway_id` int(11) NULL DEFAULT NULL COMMENT '网关ID',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `code` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义注册包',
  `online` int(1) NULL DEFAULT NULL COMMENT '1 在线 0 离线',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8067 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-网关上下线记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_iot
-- ----------------------------
DROP TABLE IF EXISTS `kd_iot`;
CREATE TABLE `kd_iot`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '0TCP协议,1websocket,2MQTT',
  `login` int(11) NOT NULL DEFAULT 0 COMMENT '0单点登录,1多点登录',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `remark` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `code` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '自定义注册包',
  `recode` varchar(320) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '自定义回复包',
  `rtype` int(11) NOT NULL DEFAULT 0 COMMENT '0ASCII,1HEX',
  `val` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '数据key',
  `vtype` int(11) NOT NULL DEFAULT 0 COMMENT '0ASCII,1HEX',
  `forward` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据转发',
  `http` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'http协议',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `password_md5` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码加密后的md5',
  `topic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt主题',
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'mqtt的动作,subscribe publish all',
  `permission` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '是否允许 allow deny',
  `year` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `month` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `day` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `date` date NOT NULL,
  `addtime` datetime NOT NULL,
  `person` int(11) NOT NULL DEFAULT 0 COMMENT '录入人',
  `del` int(11) NOT NULL DEFAULT 0 COMMENT '0未删除,1已删除',
  `del_id` int(11) NOT NULL DEFAULT 0,
  `del_time` datetime NULL DEFAULT NULL,
  `filter` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '过滤',
  `online` int(11) NOT NULL DEFAULT 0 COMMENT '1 在线 0 离线',
  `crontab` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '定时下发',
  `directive` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '被动回复',
  `log` int(1) NOT NULL DEFAULT 1 COMMENT '是否启用设备数据日志0不启用1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网云平台' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_keys
-- ----------------------------
DROP TABLE IF EXISTS `kd_keys`;
CREATE TABLE `kd_keys`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `api_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'api_key',
  `api_secret` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'api_secret',
  `year` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '年份',
  `month` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '月份',
  `day` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `expire` int(1) NOT NULL DEFAULT 0 COMMENT '0永不过期,1过期',
  `expire_time` date NULL DEFAULT NULL COMMENT '过期时间',
  `status` int(1) NOT NULL DEFAULT 0 COMMENT '0未启用,1已启用',
  `remark` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `del` int(10) NOT NULL DEFAULT 0 COMMENT '0未删除1已删除',
  `del_time` datetime NULL DEFAULT NULL COMMENT '删除人id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'api_key' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_label
-- ----------------------------
DROP TABLE IF EXISTS `kd_label`;
CREATE TABLE `kd_label`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `category_id` int(11) NULL DEFAULT NULL COMMENT '分类id',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-标签分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_label_category
-- ----------------------------
DROP TABLE IF EXISTS `kd_label_category`;
CREATE TABLE `kd_label_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-标签分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_monitor
-- ----------------------------
DROP TABLE IF EXISTS `kd_monitor`;
CREATE TABLE `kd_monitor`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NULL DEFAULT NULL,
  `related_id` int(11) NULL DEFAULT 0 COMMENT '关联ID',
  `related_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联类型kun_dian坤典云，local本机，share分销',
  `stream_status` int(1) NULL DEFAULT 0 COMMENT '流状态',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '设备名称',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'offline: 离线, online: 在线, notReg: 未注册, locked: 锁定',
  `rtmp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `hls` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'm3u8播放格式',
  `flv` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `webrtc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `longitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '经度',
  `latitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '纬度',
  `is_del` int(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 193 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_monitor_auto
-- ----------------------------
DROP TABLE IF EXISTS `kd_monitor_auto`;
CREATE TABLE `kd_monitor_auto`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) NULL DEFAULT 1 COMMENT '1截图，2录制视频',
  `monitor_id` int(11) NOT NULL COMMENT '设备ID',
  `op_type` tinyint(1) NOT NULL COMMENT '操作类型\r\n每天 1\r\n每周 2\r\n时间点 3',
  `op_value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '操作类型值\r\n每几周\r\n每几天\r\n什么时间点 ',
  `channels` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '通道',
  `recording_duration` int(11) NULL DEFAULT NULL COMMENT '录制时长',
  `last_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '是否启用\r\n1 启用\r\n2 未启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备 - 监控自动截图' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_system_config
-- ----------------------------
DROP TABLE IF EXISTS `kd_system_config`;
CREATE TABLE `kd_system_config`  (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '所属账号',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '配置名称',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '值',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '物联网-系统配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kd_webhook_logs
-- ----------------------------
DROP TABLE IF EXISTS `kd_webhook_logs`;
CREATE TABLE `kd_webhook_logs`  (
  `id` bigint(20) NOT NULL COMMENT 'id',
  `device_id` bigint(20) NOT NULL COMMENT '设备id',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'url',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备数据',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0执行成功,1执行失败',
  `msg` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '执行结果',
  `exception` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '异常结果',
  `year` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '年份',
  `month` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '月份',
  `day` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '日期',
  `date` date NOT NULL COMMENT '日期',
  `addtime` datetime NOT NULL COMMENT '时间',
  `del` int(11) NOT NULL DEFAULT 0 COMMENT '0未删除1已删除',
  `del_id` bigint(20) NOT NULL DEFAULT 0 COMMENT '删除人id',
  `del_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `member_id_device_id_del`(`device_id`, `del`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备webhook日志' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
