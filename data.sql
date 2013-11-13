/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50527
Source Host           : 127.0.0.1:3306
Source Database       : helper

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2013-08-30 15:42:15
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `ga_privacy`
-- ----------------------------
DROP TABLE IF EXISTS `ga_privacy`;
CREATE TABLE `ga_privacy` (
  `tid` binary(6) NOT NULL DEFAULT '\0\0\0\0\0\0',
  `userid` int(11) NOT NULL DEFAULT '0',
  `typeid` int(11) NOT NULL DEFAULT '0',
  `mark` tinyint(4) NOT NULL DEFAULT '0' COMMENT '标记状态 0正常 1回收站 2标记',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `mark_time` int(11) NOT NULL DEFAULT '0' COMMENT '标记时间',
  `description` varchar(200) NOT NULL DEFAULT '',
  `optime` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL,
  `account` blob,
  `password` blob,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ga_privacy
-- ----------------------------

-- ----------------------------
-- Table structure for `ga_record`
-- ----------------------------
DROP TABLE IF EXISTS `ga_record`;
CREATE TABLE `ga_record` (
  `id` binary(6) NOT NULL,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `safeuname` blob,
  `password` blob,
  `gendate` int(11) NOT NULL,
  `genip` varchar(15) NOT NULL,
  `gentype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ga_record
-- ----------------------------

-- ----------------------------
-- Table structure for `ga_type`
-- ----------------------------
DROP TABLE IF EXISTS `ga_type`;
CREATE TABLE `ga_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `typename` varchar(15) NOT NULL DEFAULT '',
  `num` smallint(6) NOT NULL DEFAULT '0' COMMENT '密码数量',
  `description` varchar(200) NOT NULL DEFAULT '',
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ga_type
-- ----------------------------
INSERT INTO `ga_type` VALUES ('19', '10', '22022', '1', '', '0');

-- ----------------------------
-- Table structure for `ga_user`
-- ----------------------------
DROP TABLE IF EXISTS `ga_user`;
CREATE TABLE `ga_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(20) NOT NULL DEFAULT '',
  `pwd` char(6) NOT NULL DEFAULT '',
  `register_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `loginip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ga_user
-- ----------------------------