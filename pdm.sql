/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50620
 Source Host           : 127.0.0.1
 Source Database       : pdm

 Target Server Type    : MySQL
 Target Server Version : 50620
 File Encoding         : utf-8

 Date: 01/07/2015 21:32:52 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pdm_categories`
-- ----------------------------
DROP TABLE IF EXISTS `pdm_categories`;
CREATE TABLE `pdm_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `parent` int(10) unsigned NOT NULL DEFAULT '0',
  `listorder` smallint(6) NOT NULL DEFAULT '8',
  `add_time` int(10) NOT NULL DEFAULT '0',
  `sum` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pdm_history`
-- ----------------------------
DROP TABLE IF EXISTS `pdm_history`;
CREATE TABLE `pdm_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uname` blob NOT NULL,
  `pwd` blob NOT NULL,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `create_ip` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pdm_password`
-- ----------------------------
DROP TABLE IF EXISTS `pdm_password`;
CREATE TABLE `pdm_password` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(140) NOT NULL DEFAULT '',
  `uname` blob NOT NULL,
  `pwd` blob NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `add_ip` bigint(10) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(10) NOT NULL DEFAULT '0',
  `hits` int(10) NOT NULL DEFAULT '0',
  `note` varchar(140) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `pdm_users`
-- ----------------------------
DROP TABLE IF EXISTS `pdm_users`;
CREATE TABLE `pdm_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email_auth` tinyint(1) NOT NULL DEFAULT '0',
  `email` varchar(30) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `password` blob NOT NULL,
  `register_time` int(10) NOT NULL DEFAULT '0',
  `register_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `login_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL DEFAULT '0',
  `login_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `auth_code` blob,
  `timeout` tinyint(1) unsigned DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
