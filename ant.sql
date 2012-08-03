/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : ant

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2012-08-03 13:51:19
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `ant_user`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user`;
CREATE TABLE `ant_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(255) DEFAULT NULL,
  `user_first_name` varchar(255) DEFAULT NULL,
  `user_last_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_login_ut` int(11) DEFAULT NULL,
  `user_register_ut` int(11) DEFAULT NULL,
  `user_status` int(1) DEFAULT NULL,
  `user_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user
-- ----------------------------
INSERT INTO `ant_user` VALUES ('7', '7new', null, null, null, null, null, null, '1');
INSERT INTO `ant_user` VALUES ('8', '8new', null, null, null, null, null, null, '2');

-- ----------------------------
-- Table structure for `ant_user_account_facebook_user`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_account_facebook_user`;
CREATE TABLE `ant_user_account_facebook_user` (
  `_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `field_id` varchar(255) DEFAULT NULL,
  `field_email` varchar(255) DEFAULT NULL,
  `field_first_name` varchar(255) DEFAULT NULL,
  `field_last_name` varchar(255) DEFAULT NULL,
  `field_full_name` varchar(255) DEFAULT NULL,
  `last_fetch_ut` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_account_facebook_user
-- ----------------------------

-- ----------------------------
-- Table structure for `ant_user_account_google_user`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_account_google_user`;
CREATE TABLE `ant_user_account_google_user` (
  `_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `field_id` varchar(255) DEFAULT NULL,
  `field_email` varchar(255) DEFAULT NULL,
  `field_first_name` varchar(255) DEFAULT NULL,
  `field_last_name` varchar(255) DEFAULT NULL,
  `field_full_name` varchar(255) DEFAULT NULL,
  `last_fetch_ut` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_account_google_user
-- ----------------------------

-- ----------------------------
-- Table structure for `ant_user_account_type`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_account_type`;
CREATE TABLE `ant_user_account_type` (
  `user_account_type_id` int(1) NOT NULL DEFAULT '0',
  `user_account_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_account_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_account_type
-- ----------------------------
INSERT INTO `ant_user_account_type` VALUES ('1', 'Google');
INSERT INTO `ant_user_account_type` VALUES ('2', 'Facebook');

-- ----------------------------
-- Table structure for `ant_user_account_user`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_account_user`;
CREATE TABLE `ant_user_account_user` (
  `_id` int(1) NOT NULL AUTO_INCREMENT,
  `user_account_type_id` int(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_account_user
-- ----------------------------

-- ----------------------------
-- Table structure for `ant_user_group`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_group`;
CREATE TABLE `ant_user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_group
-- ----------------------------
INSERT INTO `ant_user_group` VALUES ('1', 'Superadmin');
INSERT INTO `ant_user_group` VALUES ('2', 'User');
INSERT INTO `ant_user_group` VALUES ('3', 'Guest');

-- ----------------------------
-- Table structure for `ant_user_group_permission`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_group_permission`;
CREATE TABLE `ant_user_group_permission` (
  `user_group_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_permission_level` int(2) DEFAULT NULL,
  `user_group_permission_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_group_permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_group_permission
-- ----------------------------
INSERT INTO `ant_user_group_permission` VALUES ('1', '1', 'All access');
INSERT INTO `ant_user_group_permission` VALUES ('2', '2', 'Front end access');

-- ----------------------------
-- Table structure for `ant_user_group_permission_group`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_group_permission_group`;
CREATE TABLE `ant_user_group_permission_group` (
  `_id` int(11) NOT NULL DEFAULT '0',
  `user_group_permission_id` int(11) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `_status` int(2) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_group_permission_group
-- ----------------------------

-- ----------------------------
-- Table structure for `ant_user_group_user`
-- ----------------------------
DROP TABLE IF EXISTS `ant_user_group_user`;
CREATE TABLE `ant_user_group_user` (
  `_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of ant_user_group_user
-- ----------------------------
