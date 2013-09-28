/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50159
 Source Host           : localhost
 Source Database       : tempdb

 Target Server Type    : MySQL
 Target Server Version : 50159
 File Encoding         : utf-8

 Date: 09/29/2013 02:23:06 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `hmacauth`
-- ----------------------------
DROP TABLE IF EXISTS `hmacauth`;
CREATE TABLE `hmacauth` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `user_nickname` varchar(64) NOT NULL,
  `user_passwd_hash` varchar(40) NOT NULL COMMENT 'password md5 hash',
  `user_passwd_key` varchar(40) NOT NULL COMMENT 'random key',
  `user_passwd_access_token` varchar(40) NOT NULL COMMENT 'access token',
  `user_auth_expire_time` datetime NOT NULL,
  `user_auth_last_time` datetime NOT NULL COMMENT 'last success time',
  `lock_auth` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `hmacauth`
-- ----------------------------
BEGIN;
INSERT INTO `hmacauth` VALUES ('1000', 'test', 'test', 'e10adc3949ba59abbe56e057f20f883e', '22JhBTuECR', '2EkrgEzlyBJsFPxRX4nEsw', '2013-10-02 01:40:32', '2013-09-29 01:40:32', '0');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
