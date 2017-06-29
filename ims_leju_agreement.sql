/*
Navicat MySQL Data Transfer

Source Server         : 易触云
Source Server Version : 50505
Source Host           : 123.56.161.151:3306
Source Database       : wx_yichujilian

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-06-29 16:59:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_leju_agreement
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_agreement`;
CREATE TABLE `ims_leju_agreement` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户服务协议信息的id',
  `information` text COMMENT '用户服务协议的信息',
  `time` datetime DEFAULT NULL COMMENT '添加的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户协议信息表';

-- ----------------------------
-- Table structure for ims_leju_area
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_area`;
CREATE TABLE `ims_leju_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '房源归属地的id',
  `name` varchar(255) DEFAULT NULL COMMENT '房源归属地的名称',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='房源的归属地房源表';

-- ----------------------------
-- Table structure for ims_leju_assign
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_assign`;
CREATE TABLE `ims_leju_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '金额分配表的id',
  `rank_one` double(11,2) DEFAULT NULL COMMENT '一级经纪人自己推荐的客户购买返现金额',
  `rank_two` double(11,2) DEFAULT NULL COMMENT '二级经纪人自己推荐的客户购房返现金额',
  `rank_three` double(11,2) DEFAULT NULL COMMENT '三级经纪人自己推荐的客户购房返现金额',
  `assign_one` double(11,2) DEFAULT NULL COMMENT '一级经纪人下属推荐的客户购房返现金额',
  `assign_two` double(11,2) DEFAULT NULL COMMENT '二级经纪人下属推荐的客户购房返现金额',
  `assign_three` double(11,2) DEFAULT NULL COMMENT '三级经纪人下属推荐的客户购房返现金额',
  `information` text COMMENT '规则说明',
  `norm` double(11,2) DEFAULT NULL COMMENT '经济人推荐房源佣金标准：  元/㎡',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='经纪人推荐客户购买房源分配金额表';

-- ----------------------------
-- Table structure for ims_leju_carray
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_carray`;
CREATE TABLE `ims_leju_carray` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '提现的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '微信公众号的id',
  `uid` int(11) DEFAULT NULL COMMENT '用户的微信端的uid',
  `money` int(11) DEFAULT '0' COMMENT '提现数',
  `status` int(11) DEFAULT '0' COMMENT '佣金状态：0未体现 1：提现 ',
  `time` datetime DEFAULT NULL COMMENT '提现时间',
  `commission_id` int(11) DEFAULT NULL COMMENT '佣金的id',
  `name` varchar(255) DEFAULT NULL COMMENT '提款人姓名',
  `cell` varchar(255) DEFAULT NULL COMMENT '提款人电话',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COMMENT='佣金提现表';

-- ----------------------------
-- Table structure for ims_leju_client
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_client`;
CREATE TABLE `ims_leju_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推荐客户的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `name` varchar(255) DEFAULT NULL COMMENT '客户姓名',
  `phone` bigint(30) NOT NULL COMMENT '电话',
  `intention` text COMMENT '购买房源意向描述',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `recommon_id` int(11) DEFAULT NULL COMMENT '推荐人的id',
  `buy_status` int(11) unsigned DEFAULT '0' COMMENT '购房状态： 0 未购房  1已分配 2 已定房 3 已付首付 4  结款',
  `math` int(11) DEFAULT '0' COMMENT '购买的数量',
  `brokername` varchar(255) DEFAULT NULL COMMENT '客户分配给人员的姓名',
  `broker_status` int(11) DEFAULT '0' COMMENT '客户分配的状态： 0 未分配  1 已分配',
  `allocation_time` datetime DEFAULT NULL COMMENT '分配时间',
  `book_time` int(11) DEFAULT '0' COMMENT '客户订房时间',
  `station` int(11) DEFAULT NULL COMMENT '站点的id',
  `broker_id` int(255) DEFAULT NULL COMMENT '站点负责员工的id',
  PRIMARY KEY (`id`,`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=utf8 COMMENT='推荐的客户的信息';

-- ----------------------------
-- Table structure for ims_leju_commission
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_commission`;
CREATE TABLE `ims_leju_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '佣金的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `uid` int(11) DEFAULT NULL COMMENT '用户微信端的id',
  `money` int(11) DEFAULT '0' COMMENT '佣金数',
  `room_id` int(11) DEFAULT NULL COMMENT '房源的id',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=471 DEFAULT CHARSET=utf8 COMMENT='佣金表';

-- ----------------------------
-- Table structure for ims_leju_math
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_math`;
CREATE TABLE `ims_leju_math` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推荐',
  `client` int(11) DEFAULT NULL COMMENT '客户数量',
  `person` int(11) DEFAULT NULL COMMENT '联系人数量',
  `time` datetime DEFAULT NULL COMMENT '添加的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='推荐的人数限制';

-- ----------------------------
-- Table structure for ims_leju_pattern
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_pattern`;
CREATE TABLE `ims_leju_pattern` (
  `id` int(255) NOT NULL AUTO_INCREMENT COMMENT '房源格局的id',
  `name` varchar(255) DEFAULT NULL COMMENT '格局的名字',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='房源格局表';

-- ----------------------------
-- Table structure for ims_leju_person
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_person`;
CREATE TABLE `ims_leju_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推荐房源人信息的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `name` varchar(255) DEFAULT NULL COMMENT '房主姓名',
  `cell` bigint(30) NOT NULL COMMENT 'f房主电话',
  `time` int(11) DEFAULT NULL COMMENT '添加时间',
  `recommend_id` int(11) DEFAULT NULL COMMENT '推荐人id',
  `broker_id` int(11) DEFAULT NULL COMMENT '分配的站点员工的id',
  `station` int(11) DEFAULT NULL COMMENT '站点的id',
  `broker_status` int(11) DEFAULT '0' COMMENT '分配的状态',
  `applation_time` datetime DEFAULT NULL COMMENT '分配员工的时间',
  `broker_name` varchar(255) DEFAULT NULL COMMENT '分配的员工的名字',
  `math` int(11) DEFAULT '0' COMMENT '房主拥有的房源数量',
  PRIMARY KEY (`id`,`cell`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COMMENT='房源联系人信息表';

-- ----------------------------
-- Table structure for ims_leju_pictures
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_pictures`;
CREATE TABLE `ims_leju_pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '房源图片的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '微信公共号id',
  `imguqcl` varchar(255) DEFAULT NULL COMMENT '房源展示图',
  `type` varchar(255) DEFAULT NULL COMMENT '户型图',
  `living` varchar(255) DEFAULT NULL COMMENT '客厅图',
  `traffic` varchar(255) DEFAULT NULL COMMENT '交通图',
  `other` varchar(255) DEFAULT NULL COMMENT '其他房间图',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='房源图片表';

-- ----------------------------
-- Table structure for ims_leju_rank
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_rank`;
CREATE TABLE `ims_leju_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '等级表的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `type` int(11) DEFAULT '3' COMMENT '等级：1 2 3',
  `money` int(11) DEFAULT '0' COMMENT '等级的分配钱数',
  `math` int(11) DEFAULT NULL COMMENT '推广人数',
  `uid` int(11) DEFAULT NULL COMMENT '用户的微信端id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=471 DEFAULT CHARSET=utf8 COMMENT='等级表';

-- ----------------------------
-- Table structure for ims_leju_record
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_record`;
CREATE TABLE `ims_leju_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录分配的钱数记录',
  `user` int(11) DEFAULT NULL COMMENT '经纪人的id',
  `rank_money` int(11) DEFAULT '0' COMMENT '等级分配的钱数',
  `myself` int(11) DEFAULT '0' COMMENT '自己推荐的客户购买分配的钱数',
  `time` datetime DEFAULT NULL COMMENT '分配的时间',
  `userstatus` tinyint(1) DEFAULT '0',
  `room` int(11) DEFAULT '0' COMMENT '推荐房源奖励的金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='记录分配金额的详细信息';

-- ----------------------------
-- Table structure for ims_leju_room
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_room`;
CREATE TABLE `ims_leju_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '房源的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `name` varchar(255) DEFAULT NULL COMMENT '房子的名称',
  `area` double(11,2) DEFAULT NULL COMMENT '房子面积',
  `pattern` int(11) DEFAULT NULL COMMENT '房子格局',
  `position` varchar(255) DEFAULT NULL COMMENT '房子的位置',
  `city` int(11) DEFAULT NULL COMMENT '房子归属城市的id',
  `decorate` int(11) DEFAULT NULL COMMENT '装修等级 0:简装 1：精装 2：豪装 3:未装修',
  `type` int(11) DEFAULT NULL COMMENT '房源类型： 0：新房源 1：二手房 2:毛坯房',
  `photo_id` int(11) DEFAULT NULL COMMENT '房源展示效果id',
  `community` varchar(255) DEFAULT NULL COMMENT '小区名称',
  `status` int(11) DEFAULT '0' COMMENT '房源状态:0 未定房 1：订房',
  `money_id` int(11) DEFAULT NULL COMMENT '佣金的id',
  `recommend_id` int(11) DEFAULT NULL COMMENT '推荐人的id',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `person_id` int(11) DEFAULT NULL COMMENT '房东的id',
  `price` varchar(11) DEFAULT NULL COMMENT '房子价格',
  `average` double(11,2) DEFAULT NULL COMMENT '房源平均价格',
  `infomation` text COMMENT '房源简介',
  `pay_status` int(11) DEFAULT '0' COMMENT '房源付款状态： 0：未付款 1：已付定金 2：已首付 3：已付款',
  `pay_way` int(11) DEFAULT '0' COMMENT '付款方式： 0：未交易 1:现金 2：刷卡 3：微信支付 4：支付宝',
  `buy_id` int(11) DEFAULT '0' COMMENT '购买客户的id 默认为：0 无人购买',
  `money` double(11,2) DEFAULT '0.00' COMMENT '房源分配金额的总值',
  `rank_one` double(11,2) DEFAULT '0.00' COMMENT '一级代理分配的钱数',
  `rank_two` double(11,2) DEFAULT '0.00' COMMENT '二级代理分配的钱数',
  `rank_three` double(11,2) DEFAULT '0.00' COMMENT '三级代理分配的钱数',
  `residue_money` double(11,2) DEFAULT '0.00' COMMENT '分配金额剩余的钱数',
  `send_status` int(11) DEFAULT '0' COMMENT '分配金额的状态：  0：未分配过 1：已分配过',
  `mold` int(11) DEFAULT '0' COMMENT '房源的推荐类型 ： 0 普通房源  1：推荐房源',
  `alone_one` double(11,2) DEFAULT '0.00' COMMENT '自定分配给一级经纪人的佣金',
  `alone_two` double(11,2) DEFAULT '0.00' COMMENT '自定义分配给二级经纪人的佣金',
  `alone_three` double(11,2) DEFAULT '0.00' COMMENT '自定义分配给三级经纪人的佣金',
  `liation` int(11) DEFAULT NULL COMMENT '归属地的id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='房源表';

-- ----------------------------
-- Table structure for ims_leju_rule
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_rule`;
CREATE TABLE `ims_leju_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规则表的id',
  `content` text COMMENT '规则说明',
  `max` int(11) DEFAULT NULL COMMENT '最大提取金额',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='提现规则表';

-- ----------------------------
-- Table structure for ims_leju_scale_one
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_scale_one`;
CREATE TABLE `ims_leju_scale_one` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '等级规则表  二级升一级id',
  `math` int(11) DEFAULT NULL COMMENT '团队的经济人数量',
  `room` int(11) DEFAULT NULL COMMENT '客户购买的数量',
  `information` text COMMENT '规则说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='等级表二级升一级';

-- ----------------------------
-- Table structure for ims_leju_scale_two
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_scale_two`;
CREATE TABLE `ims_leju_scale_two` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '等级规则表 三级升二级规则',
  `person` int(11) DEFAULT NULL COMMENT '推荐的经纪人数',
  `room` int(11) DEFAULT NULL COMMENT '客户购买房间的数量',
  `information` text COMMENT '三级升二级会员的说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='等级设置三级升二级规则表';

-- ----------------------------
-- Table structure for ims_leju_staff
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_staff`;
CREATE TABLE `ims_leju_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '员工的id',
  `name` varchar(255) DEFAULT NULL COMMENT '员工的姓名',
  `phone` varchar(255) DEFAULT NULL COMMENT '员工的电话',
  `gender` int(11) DEFAULT NULL COMMENT '性别：0 女  1 男',
  `pictures` varchar(255) DEFAULT NULL COMMENT '员工的图片',
  `station` int(11) DEFAULT NULL COMMENT '站点的id',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `math` int(11) DEFAULT '0' COMMENT '已分配的客户数量',
  `month` int(11) DEFAULT '0' COMMENT '分配的房源联系人数量',
  `title` int(11) DEFAULT NULL COMMENT '员工的称谓 0：总经理 1：营销总监  2：财务总监   3：销售经理/店长  4:置业顾问',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='乐居员工表';

-- ----------------------------
-- Table structure for ims_leju_station
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_station`;
CREATE TABLE `ims_leju_station` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '站点的id',
  `name` varchar(255) DEFAULT NULL COMMENT '站点的名称',
  `cell` varchar(255) DEFAULT NULL COMMENT '站点的服务电话',
  `province` varchar(255) DEFAULT NULL COMMENT '站点的地址省',
  `username` varchar(255) DEFAULT NULL COMMENT '站点负责人姓名',
  `phone` varchar(255) DEFAULT NULL COMMENT '负责人电话',
  `pictures` varchar(255) DEFAULT NULL COMMENT '店面展示图',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `city` varchar(255) DEFAULT NULL COMMENT '市',
  `county` varchar(255) DEFAULT NULL COMMENT '县/区',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='站点管理表';

-- ----------------------------
-- Table structure for ims_leju_tisment
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_tisment`;
CREATE TABLE `ims_leju_tisment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告位的id',
  `info` text COMMENT '广告信息',
  `status` int(11) DEFAULT NULL COMMENT '显示的状态 ： 0 显示  1 不显示',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `cell` varchar(255) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='广告位表';

-- ----------------------------
-- Table structure for ims_leju_users
-- ----------------------------
DROP TABLE IF EXISTS `ims_leju_users`;
CREATE TABLE `ims_leju_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户表的id',
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号的id',
  `uid` varchar(255) DEFAULT NULL COMMENT '微信号的用户id',
  `openid` varchar(255) NOT NULL COMMENT '微信号的id',
  `mobile` varchar(255) NOT NULL COMMENT '手机号',
  `realname` varchar(255) DEFAULT NULL COMMENT '用户的真实姓名',
  `Idcard` varchar(255) DEFAULT NULL COMMENT '身份证号',
  `commission` int(11) DEFAULT NULL COMMENT '佣金的id',
  `grade_id` int(11) DEFAULT NULL COMMENT '等级的id',
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户的头像',
  `createtime` datetime DEFAULT NULL COMMENT '创建时间',
  `type` int(11) DEFAULT NULL COMMENT '类型： 0 普通用户 1 经济人',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信的昵称',
  `recommend_id` int(11) DEFAULT NULL COMMENT '推荐人的id 0为新升级则改变',
  `withdraw_id` int(11) DEFAULT NULL COMMENT '佣金提现表的id',
  `carray_status` int(11) DEFAULT '0' COMMENT '提取状态： 0 ：未提现  1：提现',
  `carray_time` datetime DEFAULT NULL COMMENT '提取时间',
  `record_recommon` int(11) DEFAULT NULL COMMENT '备份推荐人的id（当经纪人由三级升为二级时做备份用）',
  `code` varchar(255) DEFAULT NULL COMMENT '用户的二维码',
  `accredit` int(11) DEFAULT '0' COMMENT '二级升一级授权： 0代表可以升级 1：代表不可以升级',
  PRIMARY KEY (`id`,`mobile`)
) ENGINE=MyISAM AUTO_INCREMENT=471 DEFAULT CHARSET=utf8 COMMENT='乐居项目的用户表';
