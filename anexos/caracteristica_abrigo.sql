/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100310
 Source Host           : localhost:3306
 Source Schema         : poprua

 Target Server Type    : MySQL
 Target Server Version : 100310
 File Encoding         : 65001

 Date: 19/12/2018 23:44:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for caracteristica_abrigo
-- ----------------------------
DROP TABLE IF EXISTS `caracteristica_abrigo`;
CREATE TABLE `caracteristica_abrigo`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `caracteristica_abrigo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of caracteristica_abrigo
-- ----------------------------
INSERT INTO `caracteristica_abrigo` VALUES (1, 'Baixio');
INSERT INTO `caracteristica_abrigo` VALUES (2, 'Canteiro central');
INSERT INTO `caracteristica_abrigo` VALUES (3, 'Marquise de propriedade comercial ativa');
INSERT INTO `caracteristica_abrigo` VALUES (4, 'Marquise de propriedade comercial ativa - bancos');
INSERT INTO `caracteristica_abrigo` VALUES (5, 'Marquise de propriedade comercial inativa');
INSERT INTO `caracteristica_abrigo` VALUES (6, 'Na propriedade privada');
INSERT INTO `caracteristica_abrigo` VALUES (7, 'Na propriedade pública');
INSERT INTO `caracteristica_abrigo` VALUES (8, 'Passeio em frente a propriedade privada');
INSERT INTO `caracteristica_abrigo` VALUES (9, 'Passeio em frente a propriedade pública');
INSERT INTO `caracteristica_abrigo` VALUES (10, 'Pista de rolamento');
INSERT INTO `caracteristica_abrigo` VALUES (11, 'Praça Pública');

SET FOREIGN_KEY_CHECKS = 1;
