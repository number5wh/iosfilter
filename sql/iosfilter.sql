/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50725
Source Host           : localhost:3306
Source Database       : iosfilter

Target Server Type    : MYSQL
Target Server Version : 50725
File Encoding         : 65001

Date: 2019-08-28 15:47:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for account
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `salt` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT '0.00',
  `addtime` datetime DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mobilestore
-- ----------------------------
DROP TABLE IF EXISTS `mobilestore`;
CREATE TABLE `mobilestore` (
  `id` int(11) DEFAULT NULL,
  `mobile` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `taskname` varchar(200) DEFAULT NULL,
  `orderno` varchar(200) DEFAULT NULL,
  `ordermoney` decimal(18,2) DEFAULT '0.00' COMMENT '订单金额',
  `num` int(11) DEFAULT '0' COMMENT '号码数量',
  `checked` int(11) DEFAULT '0' COMMENT '已检测数量',
  `opennum` int(11) DEFAULT '0' COMMENT '开通数量',
  `filename` varchar(200) DEFAULT NULL COMMENT '文件地址',
  `addtime` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0未处理  1已处理  2文件未找到 3处理中',
  `ispay` tinyint(1) DEFAULT '0' COMMENT '是否已支付 0=否 1=是',
  `isrun` tinyint(1) DEFAULT '0' COMMENT '0未执行计划任务  1已执行',
  `updatetime` datetime DEFAULT NULL,
  `completetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderno` (`orderno`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usermobile
-- ----------------------------
DROP TABLE IF EXISTS `usermobile`;
CREATE TABLE `usermobile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `orderno` varchar(100) DEFAULT NULL,
  `mobile` varchar(200) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '0  未处理，1未开通2开通，3检测失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
