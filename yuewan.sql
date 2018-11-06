/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : yuewan

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 06/11/2018 18:07:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cl_admins
-- ----------------------------
DROP TABLE IF EXISTS `cl_admins`;
CREATE TABLE `cl_admins`  (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `last_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '上次登陆ip',
  `last_login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '上次登陆时间',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `update_time` datetime NULL DEFAULT NULL COMMENT '上次修改资料的时间',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 32 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_admins
-- ----------------------------
INSERT INTO `cl_admins` VALUES (14, 'xiaobai', '', '42cd4eb8b1e58488ec65d8e7359bc711', '', NULL, 1, '0000-00-00 00:00:00', NULL);
INSERT INTO `cl_admins` VALUES (1, 'admin', '', 'f6c4ae773379f7bf7690935949afd77f', '', NULL, 1, '0000-00-00 00:00:00', NULL);
INSERT INTO `cl_admins` VALUES (31, '测试', '', '3f7be0b83c065bb6534b066a392f9564', NULL, NULL, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for cl_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `cl_auth_group`;
CREATE TABLE `cl_auth_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '规则id',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 30 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_auth_group
-- ----------------------------
INSERT INTO `cl_auth_group` VALUES (28, '超级管理员', 1, '568,569,589,642,644,572,573,574,575,576,742,743,744,745,764', NULL, 1538030630);
INSERT INTO `cl_auth_group` VALUES (29, '普通管理员', 1, '566,567,568,569,570,571,572,573,574,575,576,577,578,579', NULL, NULL);

-- ----------------------------
-- Table structure for cl_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `cl_auth_group_access`;
CREATE TABLE `cl_auth_group_access`  (
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `group_id` int(11) UNSIGNED NOT NULL COMMENT '用户组id',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户组明细表' ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of cl_auth_group_access
-- ----------------------------
INSERT INTO `cl_auth_group_access` VALUES (14, 28, NULL, NULL);
INSERT INTO `cl_auth_group_access` VALUES (1, 28, NULL, NULL);
INSERT INTO `cl_auth_group_access` VALUES (30, 28, NULL, NULL);
INSERT INTO `cl_auth_group_access` VALUES (31, 28, NULL, NULL);

-- ----------------------------
-- Table structure for cl_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `cl_auth_rule`;
CREATE TABLE `cl_auth_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级id',
  `name` char(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：为1正常，为0禁用',
  `update_time` int(11) NULL DEFAULT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `condition` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 768 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '规则表' ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of cl_auth_rule
-- ----------------------------
INSERT INTO `cl_auth_rule` VALUES (568, 0, 'user', '用户管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (569, 568, 'user/index', '用户列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (572, 0, 'rule', '系统管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (573, 572, 'rule/index', '权限管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (574, 572, 'rule/group', '用户组管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (575, 572, 'rule/admin_user_list', '管理员列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (576, 572, 'nav/index', '菜单管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (589, 568, 'user/log', '登录日志', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (625, 0, 'banner', '轮播图管理', 1, 1532596221, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (607, 572, 'rule/money', '系统设置', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (626, 625, 'banner/index', '轮播图列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (627, 625, 'banner/cate', '轮播图分类', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (628, 625, 'banner/banner_edit', '轮播图编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (629, 625, 'banner/cate_edit', '轮播图分类编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (630, 0, 'gift', '礼物管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (631, 630, 'admin/gift/index', '礼物配置列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (632, 630, 'admin/gift/gift_edit', '礼物配置编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (763, 630, 'gift/gift_record_list', '礼物记录', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (634, 0, 'red_package', '红包管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (635, 634, 'red_package/index', '红包列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (636, 635, 'red_package/edit', '红包编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (637, 0, 'play', '房间管理', 1, 1533190219, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (638, 637, 'play/cate', '房间分类', 1, 1533190228, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (639, 637, 'play/cate_edit', '房间分类编辑', 1, 1533190239, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (640, 637, 'play/cate_change', '房间分类禁用', 1, 1533190248, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (642, 568, 'user/user_edit', '用户编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (643, 568, 'user/change', '用户禁用', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (644, 568, 'user/user_pass', '用户删除', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (645, 568, 'user/del', '日志删除', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (650, 637, 'play/index', '房间列表', 1, 1533190262, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (651, 0, 'vod', '回放管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (652, 651, 'vod/index', '视频列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (765, 681, 'money/stream', '平台流水', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (654, 0, 'guess', '竞猜管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (764, 742, 'sale_success/index', '拍卖管理列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (656, 0, '', '认证管理', 1, 1534382765, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (674, 656, 'role_check/index', '官方认证管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (658, 656, 'user_check/user_check', '实名认证审核', 1, 1534382820, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (675, 656, 'role_check/check_edit', '官方认证审核', 1, 1534382946, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (660, 568, 'message/opinion', '用户反馈', 1, 1534751226, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (661, 568, 'message/op_edit', '用户反馈编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (662, 568, 'message/op_del', '用户反馈删除', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (663, 0, 'news', '咨讯评论管理', 1, 1538020059, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (664, 663, 'news/index', '咨讯列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (665, 663, 'news/edit', '咨讯编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (666, 663, 'news/change', '咨讯置顶', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (667, 663, 'news/del', '咨讯删除', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (668, 663, 'news/changestatus', '咨讯禁用', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (669, 0, 'newsreply', '咨讯管理', 1, 1538019935, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (670, 669, 'newsreply/index', '咨讯评论列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (671, 669, 'newsreply/edit', '咨讯评论编辑', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (672, 669, 'newsreply/changestatus', '咨讯评论禁用', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (673, 656, 'user_check/index', '实名认证管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (676, 654, 'guess/index', '房间竞猜', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (677, 0, 'message', '系统消息', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (678, 677, 'message/index', '系统消息列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (679, 677, 'message/add', '发布系统消息', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (680, 677, 'message/edit', '编辑系统消息', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (681, 0, 'Money', '资金管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (682, 681, 'money/cash', '提现申请', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (683, 681, 'money/edit', '提现审核', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (684, 681, 'money/detail', '提现详情查看', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (685, 681, 'money/map', '统计管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (686, 572, 'rule/explain', '参数配置', 1, 1536644950, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (687, 651, 'vod/expired_list', '回放空间过期列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (689, 0, 'report', '举报管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (690, 689, 'Admin/report/index', '举报列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (691, 689, 'Admin/report/delete', '删除举报', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (692, 689, 'Admin/report/report_edit', '修改举报状态', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (693, 568, 'user/cate', '用户类型', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (694, 568, 'user/cate_edit', '用户类型修改', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (695, 568, 'user/detail', '用户资金明细表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (696, 572, 'rule/ex_add', '说明添加', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (697, 572, 'rule/delete', '删除权限', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (698, 572, 'rule/edit', '修改权限', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (699, 572, 'rule/add', '添加权限', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (700, 572, 'rule/add_group', '添加用户组', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (701, 572, 'rule/edit_group', '修改用户组', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (702, 572, 'rule/delete_group', '删除用户组', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (703, 572, 'rule/rule_group', '分配权限', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (704, 572, 'rule/check_user', '添加成员', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (705, 572, 'rule/add_user_to_group', '添加用户到用户组', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (706, 572, 'rule/delete_user_from_group', '将用户移到用户组', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (707, 572, 'rule/add_admin', '添加管理员', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (708, 572, 'rule/edit_admin', '修改管理员', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (709, 572, 'rule/del_admin', '删除管理员', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (710, 572, 'rule/version', '版本控制', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (711, 572, 'rule/version_edit', '版本修改', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (712, 572, 'rule/top', '改变推荐状态', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (713, 572, 'rule/change', '权限改变', 1, 1537494239, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (714, 572, 'rule/del', '规则删除', 1, 1537494254, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (716, 625, 'banner/change', '轮播图改变推荐状态', 1, 1537494273, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (717, 625, 'banner/banner_del', '轮播图删除', 1, 1537494287, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (718, 625, 'banner/cate_change', '改变轮播图分类推荐状态', 1, 1537494305, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (719, 625, 'banner/cate_del', '分类删除', 1, 1537494221, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (720, 637, 'play/cate_top', '改变分类推荐状态', 1, 1538020150, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (721, 637, 'play/top', '改变推荐状态', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (722, 637, 'play/edit', '房间修改', 1, 1537494188, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (723, 637, 'play/change', '改变房间状态', 1, 1537494208, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (762, 654, 'guess/guess_index', '竞猜详情', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (761, 677, 'message/del', '系统消息删除', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (760, 677, 'message/change', '系统消息状态改变', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (735, 681, 'money/index', '充值列表', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (736, 681, 'money/detail2', '充值详情', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (737, 0, 'nav/add', '添加菜单', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (738, 0, 'nav/edit', '修改菜单', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (739, 0, 'nav/delete', '删除菜单', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (740, 0, 'nav/order', '菜单排序', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (741, 0, 'sale_success/sale_edit', '修改拍卖', 1, 1536819236, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (742, 0, 'sale_success', '拍卖管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (744, 742, 'sale_success/change', '拍卖品状态修改', 1, 1540864205, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (745, 742, 'sale_success/sale_del', '拍卖品删除', 1, 1540864222, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (746, 0, 'upload', '文件上传管理', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (747, 746, 'upload/index', '文件上传', 1, 1538019348, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (748, 746, 'upload/delete1', '文件删除', 1, 1538019359, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (767, 568, 'user/Push', '发布推送', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (752, 651, 'vod/top', '改变推荐状态', 1, NULL, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (753, 651, 'vod/edit', '回放修改', 1, 1537494320, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (754, 651, 'vod/change', '回放推荐状态', 1, 1537494342, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (755, 651, 'vod/delete', '回放删除', 1, 1537494356, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (756, 651, 'vod/cate_edit', '回放分类修改', 1, 1537494408, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (757, 651, 'vod/cate_change', '回放分类推荐状态', 1, 1537494391, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (758, 651, 'vod/cate_del', '回放分类删除', 1, 1537494375, 1, '', NULL);
INSERT INTO `cl_auth_rule` VALUES (759, 651, 'vod/del', '回放删除', 1, 1537494419, 1, '', NULL);

-- ----------------------------
-- Table structure for cl_bankroll
-- ----------------------------
DROP TABLE IF EXISTS `cl_bankroll`;
CREATE TABLE `cl_bankroll`  (
  `b_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_num` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `money` int(10) NOT NULL COMMENT '金额',
  `money_type` char(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '资金类型  BCDN eth',
  `status` tinyint(1) NOT NULL COMMENT '状态 1充值成功  2=>提现待处理 3=>提现处理中 4=>提现驳回  5=>提现成功',
  `type` tinyint(1) NOT NULL COMMENT '1充值  2=>提现',
  `TxHash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '提现或充值交易hash',
  `ETHAddr` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易地址',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`b_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值提现记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_bankroll
-- ----------------------------
INSERT INTO `cl_bankroll` VALUES (1, 'RE8xK1dyWj201809307864', 4, 100, 'BCDN', 2, 2, NULL, '0xb781d6ce5865726d954585fef312c1ed6d5898db', 1538242605, 1538242605);
INSERT INTO `cl_bankroll` VALUES (2, 'RELbyJGK5d201810081300', 6, 100, 'BCDN', 2, 2, NULL, 'vvvvvb', 1538998799, 1538998799);
INSERT INTO `cl_bankroll` VALUES (3, 'RELbyJGK5d201810086279', 6, 100, 'BCDN', 2, 2, NULL, 'vvvvvb', 1538999565, 1538999565);
INSERT INTO `cl_bankroll` VALUES (4, 'REPRBaZyZ2201810101925', 2, 20, 'BCDN', 2, 2, NULL, 'emmmm1', 1539134312, 1539134312);
INSERT INTO `cl_bankroll` VALUES (5, 'REX7yoXK1A201810137797', 1, 15, 'BCDN', 2, 2, NULL, '好利来', 1539415188, 1539415188);
INSERT INTO `cl_bankroll` VALUES (6, 'REjGBP6KXA201810296214', 3, 700, 'BCDN', 2, 2, NULL, 'mmmmmmm', 1540781390, 1540781390);

-- ----------------------------
-- Table structure for cl_banner
-- ----------------------------
DROP TABLE IF EXISTS `cl_banner`;
CREATE TABLE `cl_banner`  (
  `bid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '简介',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '广告轮播图链接地址',
  `cid` int(11) NOT NULL COMMENT '分类id',
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片地址',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT 99,
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cl_banner_cate
-- ----------------------------
DROP TABLE IF EXISTS `cl_banner_cate`;
CREATE TABLE `cl_banner_cate`  (
  `cid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'banner分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for cl_capital_flow
-- ----------------------------
DROP TABLE IF EXISTS `cl_capital_flow`;
CREATE TABLE `cl_capital_flow`  (
  `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `money` decimal(10, 2) NULL DEFAULT NULL COMMENT '金额',
  `money_type` tinyint(1) NOT NULL COMMENT '资金类型',
  `status` tinyint(1) NOT NULL COMMENT '1=>平台流水 2=>用户充值 3=>用户提现',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`c_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '平台资金流水记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cl_explain
-- ----------------------------
DROP TABLE IF EXISTS `cl_explain`;
CREATE TABLE `cl_explain`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '说明',
  `create_time` int(10) NULL DEFAULT NULL,
  `update_time` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '扩展说明' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_explain
-- ----------------------------
INSERT INTO `cl_explain` VALUES (1, '拍卖超过一天无更高竞价即完成拍卖。拍卖品自动发放至账户，拍卖加价每次不得低于当前竞价的10%。', '拍卖规则', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (2, '区块链是分布式数据存储、点对点运输、共识机制、加密算法等计算机技术的新型应用模式。所谓共识机制是区块链系统，算法等计算机技术的新型应用模式，所谓的', '兑换规则', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (4, '四川创客区块链科技有限公司 版权所有@www.sohatv.com 川CP备12345678-1', 'footer 公司', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (5, '公司地址：中国（四川）自由贸易试验区成都高新区世纪城南路599号6栋5层505号', 'footer 地址', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (6, '涉黄,涉政,口嗨,打广告,其他', '举报理由', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (7, '1000', 'VIP房间升级最低抵押积分', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (8, '区块链是分布式数据存储、点对点运输、共识机制、加密算法等计算机技术的新型应用模式。所谓共识机制是区块链系统，算法等计算机技术的新型应用模式，所谓的', '升级须知', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (9, 'http://wap.xingzhuosong.com/web/download', '下载链接', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (10, '此为收区块链是分布式数据存储、点对点运输、共识机制、加密算法等计算机技术的新型应用模式。所谓共识机制是区块链系统，算法等计算机技术的新型应用模式，所谓的费说明', '收费说明', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (11, '测试,小测试,大测1试', '热门推荐(请以英文逗号隔开)', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (12, 'http://wap.xingzhuosong.com/', 'PC请求地址', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (13, '区块链是分布式数据存储、点对点运输、共识机制、加密算法等计算机技术的新型应用模式。所谓共识机制是区块链系统，算法等计算机技术的新型应用模式，所谓的', '分享规则', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (14, 'http://www.baidu.com', '安卓下载地址', NULL, 1540432957);
INSERT INTO `cl_explain` VALUES (15, 'http://www.baidu.com', 'ios下载地址', NULL, 1540432957);

-- ----------------------------
-- Table structure for cl_extend
-- ----------------------------
DROP TABLE IF EXISTS `cl_extend`;
CREATE TABLE `cl_extend`  (
  `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nick_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '禁止使用的昵称',
  `role_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '禁止使用的角色名',
  `room_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '禁止使用的房间名',
  `guess_ratio` tinyint(2) NULL DEFAULT NULL COMMENT '竞猜抽成比例 整数  最大99',
  `guess_max` int(10) NOT NULL DEFAULT 9999 COMMENT '用户单次竞猜最高下注额',
  `gift_ratio` tinyint(2) NOT NULL DEFAULT 30 COMMENT '礼物抽成比例 整数  最大99',
  `inte_min_unit` double NULL DEFAULT NULL COMMENT '积分红包最小单位',
  `btc_min_unit` double NULL DEFAULT NULL COMMENT '比特币红包最小单位',
  `eth_min_unit` double NULL DEFAULT NULL COMMENT '以太币红包最小单位',
  `BCDN_min_unit` decimal(10, 2) NULL DEFAULT NULL COMMENT 'BCDN红包最小单位',
  `role_check` decimal(10, 2) NULL DEFAULT NULL COMMENT '角色认证抵押资产最低限制',
  `compare_num` int(3) NULL DEFAULT NULL COMMENT '人脸比对通过百分比',
  `eth` int(10) NOT NULL DEFAULT 1 COMMENT '以太币最低提现金额',
  `BCDN` int(10) NOT NULL COMMENT '10',
  `BCDN_cash` int(10) NOT NULL DEFAULT 20 COMMENT 'BCDN提现手续费',
  `BCDN_to_money` decimal(10, 2) NOT NULL COMMENT 'bcdn兑换积分比例',
  `charge` int(2) NOT NULL COMMENT '积分bcdn互换手续费 最高99',
  `create_time` int(11) NULL DEFAULT NULL,
  `sale_room` int(11) NOT NULL DEFAULT 100 COMMENT '竞拍房间名称底价',
  `eth_cash` int(4) NOT NULL COMMENT '以太币提现手续费',
  `update_time` int(11) NULL DEFAULT NULL,
  `sale_role` int(11) NOT NULL DEFAULT 100 COMMENT '竞拍角色名称底价',
  `price_limit` float(1, 1) NULL DEFAULT 0.0 COMMENT '拍卖加价最低限制(0-1之间)',
  `space_money` int(11) NOT NULL DEFAULT 1 COMMENT '1G存储空间1个月所需积分',
  `app_qrcode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'app下载二维码图片地址',
  `wx_qrcode` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信公众号二维码',
  `backGround` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分享背景图',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '扩展表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_extend
-- ----------------------------
INSERT INTO `cl_extend` VALUES (1, '小松,小包', '小松,小包,小兰,小丽', '小松,小包,小兰,小丽', 30, 9999, 30, 0.01, 0.001, 0.01, 1.00, 500.00, 85, 1, 10, 20, 10.00, 10, NULL, 100000, 1, NULL, 10000, 0.1, 1000, 'http://file.51soha.com//efb7c201809191346581162.jpg', 'http://file.51soha.com//0f075201810191746541936.jpg', 'http://file.51soha.com//30273201810271358002124.png');

-- ----------------------------
-- Table structure for cl_gift
-- ----------------------------
DROP TABLE IF EXISTS `cl_gift`;
CREATE TABLE `cl_gift`  (
  `gift_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `gift_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '礼物名称',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '礼物图片',
  `price` decimal(10, 2) NOT NULL COMMENT '礼物价格',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '礼物状态 1=>正常 0>禁用',
  PRIMARY KEY (`gift_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '礼物表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_gift
-- ----------------------------
INSERT INTO `cl_gift` VALUES (2, '无字天书', 'http://file.51soha.com//dd4d1201809181620547320.png', 49.99, 1534298459, 1537258855, 1);
INSERT INTO `cl_gift` VALUES (3, 'KO', 'http://file.51soha.com//ea576201809181620447649.png', 19.99, 1534298471, 1537258846, 1);
INSERT INTO `cl_gift` VALUES (4, '玫瑰', 'http://file.51soha.com//a5edf20180918162022459.png', 9.99, 1534298481, 1537258823, 1);
INSERT INTO `cl_gift` VALUES (5, '跑车', 'http://file.51soha.com//b92de201809181620044513.png', 99.99, 1534298491, 1537258805, 1);
INSERT INTO `cl_gift` VALUES (6, '火箭', 'http://file.51soha.com//91bd1201809181618544732.png', 199.99, 1534298500, 1537258736, 1);
INSERT INTO `cl_gift` VALUES (7, '金杯汽车', 'http://file.51soha.com//3f938201809181619517801.png', 999.99, 1534298525, 1537953378, 1);

-- ----------------------------
-- Table structure for cl_gift_record
-- ----------------------------
DROP TABLE IF EXISTS `cl_gift_record`;
CREATE TABLE `cl_gift_record`  (
  `record_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL COMMENT '关联直播间id',
  `gift_id` int(11) NOT NULL COMMENT '关联礼物id',
  `role_id` int(11) NOT NULL COMMENT '赠送人角色id',
  `num` int(11) NOT NULL COMMENT '赠送数量',
  `activity_id` int(11) NOT NULL DEFAULT 0 COMMENT '活动id（赠送礼物时有活动开启即记录）',
  `user_id` int(11) NOT NULL COMMENT '赠送人id',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>正常 0=>异常  参数0作为预设参数  暂无意义',
  `to_user` int(11) NOT NULL COMMENT '接受者ID',
  `to_role` int(11) NOT NULL DEFAULT 1 COMMENT '接收者角色id  role_id',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0已结算1未结算',
  PRIMARY KEY (`record_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 339 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '礼物赠送记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_gift_record
-- ----------------------------
INSERT INTO `cl_gift_record` VALUES (1, 5, 6, 6, 1, 0, 5, 1538117172, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (2, 5, 2, 6, 10, 0, 5, 1538117185, NULL, 1, 4, 1, 0);
INSERT INTO `cl_gift_record` VALUES (3, 5, 2, 7, 1, 0, 1, 1538118909, NULL, 1, 4, 1, 0);
INSERT INTO `cl_gift_record` VALUES (4, 5, 3, 7, 10, 0, 1, 1538118925, NULL, 1, 4, 1, 0);
INSERT INTO `cl_gift_record` VALUES (5, 5, 7, 9, 1, 0, 10, 1538118952, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (6, 5, 6, 9, 1, 0, 10, 1538118960, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (7, 5, 0, 2, 10, 0, 2, 1538118967, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (8, 5, 2, 9, 1, 0, 10, 1538118968, NULL, 1, 4, 1, 0);
INSERT INTO `cl_gift_record` VALUES (9, 5, 0, 2, 1, 0, 2, 1538118989, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (10, 5, 5, 2, 1, 0, 2, 1538119005, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (11, 5, 6, 2, 1, 0, 2, 1538119016, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (12, 5, 2, 2, 1, 0, 2, 1538119030, NULL, 1, 4, 1, 0);
INSERT INTO `cl_gift_record` VALUES (13, 5, 7, 2, 1, 0, 2, 1538119037, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (14, 5, 6, 9, 1, 0, 10, 1538119073, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (15, 5, 7, 9, 1, 0, 10, 1538119079, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (16, 5, 6, 7, 1, 0, 1, 1538119083, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (17, 5, 5, 7, 1, 0, 1, 1538119566, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (18, 5, 7, 5, 1, 0, 8, 1538119581, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (19, 1, 2, 6, 1, 0, 5, 1538122371, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (20, 1, 2, 6, 1, 0, 5, 1538122372, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (21, 1, 4, 6, 1, 0, 5, 1538122377, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (22, 1, 5, 6, 1, 0, 5, 1538122387, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (23, 1, 6, 6, 1, 0, 5, 1538122393, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (24, 1, 4, 6, 1, 0, 5, 1538122401, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (25, 1, 7, 8, 1, 0, 9, 1538184379, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (26, 1, 7, 8, 1, 0, 9, 1538187974, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (27, 1, 7, 8, 13, 0, 9, 1538193261, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (28, 1, 5, 17, 1, 0, 3, 1538193737, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (29, 1, 7, 17, 1, 0, 3, 1538193748, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (30, 7, 7, 17, 1, 0, 3, 1538202479, NULL, 1, 9, 1, 1);
INSERT INTO `cl_gift_record` VALUES (31, 7, 6, 3, 1, 0, 7, 1538205117, NULL, 1, 9, 1, 1);
INSERT INTO `cl_gift_record` VALUES (32, 1, 7, 12, 1, 0, 6, 1538208714, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (33, 14, 7, 9, 1, 0, 10, 1538217775, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (34, 14, 6, 9, 1, 0, 10, 1538217793, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (35, 14, 6, 9, 1, 0, 10, 1538217842, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (36, 14, 7, 9, 1, 0, 10, 1538217890, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (37, 14, 7, 9, 1, 0, 10, 1538217964, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (38, 14, 7, 9, 1, 0, 10, 1538218008, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (39, 14, 6, 9, 1, 0, 10, 1538218341, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (40, 14, 7, 9, 1, 0, 10, 1538218352, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (41, 14, 7, 9, 1, 0, 10, 1538218498, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (42, 14, 7, 9, 1, 0, 10, 1538218518, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (43, 14, 5, 9, 1, 0, 10, 1538218589, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (44, 14, 7, 24, 2, 0, 20, 1538218708, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (45, 14, 7, 24, 222, 0, 20, 1538218717, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (46, 1, 5, 10, 2, 0, 11, 1538218745, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (47, 14, 7, 24, 1, 0, 20, 1538218764, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (48, 14, 7, 9, 1, 0, 10, 1538219151, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (49, 14, 7, 9, 1, 0, 10, 1538219253, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (50, 14, 7, 9, 1, 0, 10, 1538219264, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (51, 14, 7, 9, 1, 0, 10, 1538219343, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (52, 14, 7, 9, 1, 0, 10, 1538219469, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (53, 14, 7, 9, 1, 0, 10, 1538219484, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (54, 14, 7, 9, 1, 0, 10, 1538219636, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (55, 14, 6, 9, 1, 0, 10, 1538219641, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (56, 1, 4, 10, 20, 0, 11, 1538220632, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (57, 1, 7, 8, 1, 0, 9, 1538220672, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (58, 1, 4, 10, 2, 0, 11, 1538220743, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (59, 1, 7, 3, 1, 0, 7, 1538220761, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (60, 1, 7, 8, 1, 0, 9, 1538220768, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (61, 1, 7, 3, 1, 0, 7, 1538220922, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (62, 1, 7, 8, 100, 0, 9, 1538221465, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (63, 1, 7, 17, 11, 0, 3, 1538235840, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (64, 5, 7, 17, 10, 1, 3, 1538236248, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (65, 5, 6, 6, 1, 1, 5, 1538236409, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (66, 5, 5, 6, 1, 1, 5, 1538236422, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (67, 14, 5, 24, 1, 0, 20, 1538236968, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (68, 1, 7, 3, 1, 0, 7, 1538238055, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (69, 1, 7, 3, 1, 0, 7, 1538238110, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (70, 13, 2, 7, 1, 0, 1, 1538267202, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (71, 1, 6, 25, 70, 0, 23, 1538274222, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (72, 1, 7, 25, 1, 0, 23, 1538274235, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (73, 1, 0, 25, 1, 0, 23, 1538274239, NULL, 1, 3, 1, 1);
INSERT INTO `cl_gift_record` VALUES (74, 13, 4, 12, 20, 0, 6, 1538280018, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (75, 5, 4, 12, 20, 0, 6, 1538294156, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (76, 5, 6, 26, 1, 0, 24, 1538294285, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (77, 5, 5, 26, 1, 0, 24, 1538294288, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (78, 5, 5, 12, 5, 0, 6, 1538294305, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (79, 6, 6, 27, 1, 0, 25, 1538442085, NULL, 1, 1, 1, 1);
INSERT INTO `cl_gift_record` VALUES (80, 10, 7, 8, 1, 6, 9, 1538978794, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (81, 7, 4, 12, 10, 10, 6, 1538979042, NULL, 1, 9, 1, 1);
INSERT INTO `cl_gift_record` VALUES (82, 10, 7, 8, 1, 6, 9, 1538979113, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (83, 10, 7, 8, 1, 6, 9, 1538979122, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (84, 10, 7, 8, 1, 6, 9, 1538979130, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (85, 7, 7, 12, 1, 0, 6, 1538980158, NULL, 1, 9, 1, 1);
INSERT INTO `cl_gift_record` VALUES (86, 7, 7, 12, 1, 0, 6, 1538980165, NULL, 1, 9, 1, 1);
INSERT INTO `cl_gift_record` VALUES (87, 8, 7, 23, 1, 11, 18, 1538982841, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (88, 8, 7, 23, 1, 11, 18, 1538982908, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (89, 8, 7, 23, 1, 11, 18, 1538982957, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (90, 8, 6, 23, 1, 11, 18, 1538982990, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (91, 8, 6, 23, 1, 11, 18, 1538983039, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (92, 8, 6, 23, 1, 11, 18, 1538983131, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (93, 10, 4, 12, 10, 6, 6, 1538983884, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (94, 8, 7, 12, 12, 0, 6, 1538983955, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (95, 8, 4, 12, 10, 0, 6, 1538983991, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (96, 8, 7, 23, 1, 0, 18, 1538984282, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (97, 8, 0, 23, 1, 0, 18, 1538984306, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (98, 8, 6, 23, 1, 0, 18, 1538984379, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (99, 8, 6, 23, 1, 0, 18, 1538984469, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (100, 10, 7, 8, 1, 6, 9, 1538984716, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (101, 10, 7, 23, 1, 6, 18, 1538984721, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (102, 10, 7, 8, 1, 6, 9, 1538984755, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (103, 14, 7, 8, 1, 0, 9, 1538984797, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (104, 14, 6, 9, 1, 0, 10, 1538984809, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (105, 14, 7, 9, 1, 0, 10, 1538984927, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (106, 14, 7, 9, 1, 0, 10, 1538984960, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (107, 14, 7, 9, 1, 0, 10, 1538985008, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (108, 14, 7, 9, 1, 0, 10, 1538985052, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (109, 14, 7, 9, 1, 0, 10, 1538985546, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (110, 14, 6, 9, 1, 0, 10, 1538985568, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (111, 14, 7, 9, 1, 0, 10, 1538986197, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (112, 13, 7, 9, 1, 0, 10, 1538986313, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (113, 14, 7, 9, 1, 0, 10, 1538986484, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (114, 14, 7, 8, 12, 0, 9, 1538986978, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (115, 8, 7, 23, 1, 0, 18, 1538988181, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (116, 13, 7, 23, 1, 0, 18, 1538989059, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (117, 16, 7, 23, 1, 0, 18, 1538989101, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (118, 16, 7, 9, 1, 0, 10, 1538989110, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (119, 8, 7, 8, 1, 0, 9, 1538989241, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (120, 8, 7, 23, 1, 0, 18, 1538989266, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (121, 13, 7, 23, 1, 0, 18, 1538993676, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (122, 8, 6, 23, 1, 0, 18, 1538995529, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (123, 8, 6, 23, 1, 0, 18, 1538995588, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (124, 8, 7, 23, 1, 0, 18, 1538995705, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (125, 8, 7, 23, 1, 0, 18, 1538995781, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (126, 8, 7, 23, 1, 0, 18, 1538995804, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (127, 8, 7, 23, 1, 0, 18, 1538995833, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (128, 8, 7, 12, 2, 0, 6, 1538995872, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (129, 8, 7, 12, 2, 0, 6, 1538995948, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (130, 14, 7, 9, 1, 0, 10, 1538996331, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (131, 14, 7, 9, 1, 0, 10, 1538996425, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (132, 14, 7, 9, 1, 0, 10, 1538996926, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (133, 1, 7, 12, 1, 0, 6, 1538997219, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (134, 14, 7, 9, 1, 0, 10, 1538997749, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (135, 14, 7, 9, 1, 0, 10, 1538997780, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (136, 14, 7, 9, 1, 0, 10, 1538997855, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (137, 14, 7, 9, 1, 0, 10, 1538998017, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (138, 14, 7, 9, 1, 0, 10, 1538998663, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (139, 13, 7, 8, 1, 0, 9, 1538999995, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (140, 13, 7, 8, 12, 0, 9, 1539000001, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (141, 14, 7, 9, 1, 0, 10, 1539000791, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (142, 14, 7, 9, 1, 0, 10, 1539001357, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (143, 8, 7, 23, 1, 0, 18, 1539001731, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (144, 8, 7, 23, 1, 0, 18, 1539003065, NULL, 1, 10, 1, 1);
INSERT INTO `cl_gift_record` VALUES (145, 14, 7, 9, 1, 0, 10, 1539004970, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (146, 16, 3, 10, 1, 25, 11, 1539053080, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (147, 16, 2, 10, 1, 25, 11, 1539053110, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (148, 16, 2, 10, 1, 25, 11, 1539053122, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (149, 16, 7, 10, 1, 25, 11, 1539053428, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (150, 16, 7, 10, 1, 25, 11, 1539053491, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (151, 16, 7, 10, 1, 0, 11, 1539053724, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (152, 16, 7, 10, 1, 0, 11, 1539053728, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (153, 16, 2, 10, 1, 0, 11, 1539053866, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (154, 14, 7, 9, 1, 0, 10, 1539054713, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (155, 14, 7, 9, 1, 0, 10, 1539056296, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (156, 14, 6, 9, 1, 0, 10, 1539056301, NULL, 1, 18, 1, 1);
INSERT INTO `cl_gift_record` VALUES (157, 13, 3, 2, 1, 0, 2, 1539070870, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (158, 13, 3, 2, 1, 0, 2, 1539070873, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (159, 13, 3, 2, 1, 0, 2, 1539070881, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (160, 13, 3, 2, 1, 0, 2, 1539075945, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (161, 13, 0, 9, 12, 0, 10, 1539080290, NULL, 1, 3, 1, 1);
INSERT INTO `cl_gift_record` VALUES (162, 13, 7, 9, 1, 0, 10, 1539080316, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (163, 13, 6, 9, 1, 0, 10, 1539080322, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (164, 13, 2, 9, 11, 0, 10, 1539080334, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (165, 13, 7, 9, 11, 0, 10, 1539080346, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (166, 19, 3, 2, 1, 0, 2, 1539080437, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (167, 5, 3, 10, 1, 0, 11, 1539150102, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (168, 5, 3, 10, 1, 0, 11, 1539150109, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (169, 5, 3, 10, 1, 0, 11, 1539150162, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (170, 9, 3, 2, 1, 0, 2, 1539150595, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (171, 9, 2, 2, 1, 0, 2, 1539150601, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (172, 5, 7, 2, 1, 0, 2, 1539151223, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (173, 22, 3, 2, 1, 0, 2, 1539160708, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (174, 22, 2, 2, 1, 0, 2, 1539160712, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (175, 5, 7, 2, 1, 0, 2, 1539226800, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (176, 5, 0, 2, 20, 0, 2, 1539226811, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (177, 23, 3, 10, 1, 0, 11, 1539237725, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (178, 23, 3, 10, 1, 0, 11, 1539237729, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (179, 23, 3, 10, 1, 0, 11, 1539237752, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (180, 23, 3, 10, 1, 0, 11, 1539237755, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (181, 23, 0, 10, 1, 0, 11, 1539237766, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (182, 23, 0, 10, 1, 0, 11, 1539237770, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (183, 23, 0, 10, 1, 0, 11, 1539237771, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (184, 23, 3, 10, 1, 0, 11, 1539237773, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (185, 23, 7, 2, 1, 0, 2, 1539237776, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (186, 23, 0, 2, 1, 0, 2, 1539237778, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (187, 23, 0, 2, 1, 0, 2, 1539237780, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (188, 23, 0, 2, 1, 0, 2, 1539237785, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (189, 23, 3, 10, 1, 0, 11, 1539237786, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (190, 23, 0, 2, 2, 0, 2, 1539237790, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (191, 2, 3, 10, 1, 0, 11, 1539237810, NULL, 1, 2, 1, 1);
INSERT INTO `cl_gift_record` VALUES (192, 2, 3, 10, 1, 0, 11, 1539237816, NULL, 1, 2, 1, 1);
INSERT INTO `cl_gift_record` VALUES (193, 5, 3, 15, 1, 0, 11, 1539239456, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (194, 5, 0, 15, 1, 0, 11, 1539239462, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (195, 5, 0, 15, 1, 0, 11, 1539239465, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (196, 5, 0, 15, 1, 0, 11, 1539239471, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (197, 5, 0, 15, 1, 0, 11, 1539239474, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (198, 5, 0, 15, 1, 0, 11, 1539239632, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (199, 5, 0, 15, 1, 0, 11, 1539239635, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (200, 5, 0, 15, 1, 0, 11, 1539239656, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (201, 3, 7, 31, 1, 0, 29, 1539244386, NULL, 1, 7, 1, 1);
INSERT INTO `cl_gift_record` VALUES (202, 3, 0, 31, 5, 0, 29, 1539244396, NULL, 1, 7, 1, 1);
INSERT INTO `cl_gift_record` VALUES (203, 3, 7, 31, 1, 0, 29, 1539244407, NULL, 1, 7, 1, 1);
INSERT INTO `cl_gift_record` VALUES (204, 28, 3, 10, 1, 0, 11, 1539246709, NULL, 1, 38, 1, 1);
INSERT INTO `cl_gift_record` VALUES (205, 28, 5, 10, 1, 0, 11, 1539246721, NULL, 1, 38, 1, 1);
INSERT INTO `cl_gift_record` VALUES (206, 28, 3, 10, 1, 0, 11, 1539246744, NULL, 1, 38, 1, 1);
INSERT INTO `cl_gift_record` VALUES (207, 25, 6, 40, 1, 0, 38, 1539251307, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (208, 23, 7, 9, 1, 0, 10, 1539311573, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (209, 23, 6, 9, 1, 0, 10, 1539311583, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (210, 10, 6, 3, 1, 0, 7, 1539326220, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (211, 10, 7, 3, 1, 0, 7, 1539326227, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (212, 10, 5, 3, 1, 0, 7, 1539326232, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (213, 10, 4, 3, 1, 0, 7, 1539326249, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (214, 13, 7, 8, 1, 0, 9, 1539329662, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (215, 13, 7, 8, 1, 0, 9, 1539329762, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (216, 13, 7, 8, 1, 0, 9, 1539329765, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (217, 13, 7, 8, 1, 0, 9, 1539330083, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (218, 13, 7, 8, 1, 0, 9, 1539330780, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (219, 13, 7, 8, 1, 0, 9, 1539330789, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (220, 13, 7, 23, 1, 0, 18, 1539331030, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (221, 13, 6, 23, 12, 0, 18, 1539331043, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (222, 10, 7, 23, 1, 0, 18, 1539331057, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (223, 10, 6, 23, 12, 0, 18, 1539331065, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (224, 13, 7, 8, 1, 0, 9, 1539331154, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (225, 13, 6, 8, 1, 0, 9, 1539331162, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (226, 5, 7, 23, 1, 0, 18, 1539331548, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (227, 5, 7, 23, 1, 0, 18, 1539331558, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (228, 5, 7, 23, 1, 0, 18, 1539331564, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (229, 13, 7, 8, 10, 0, 9, 1539331701, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (230, 13, 3, 8, 1, 0, 9, 1539331708, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (231, 13, 2, 8, 1, 0, 9, 1539331712, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (232, 13, 6, 8, 1, 0, 9, 1539331716, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (233, 13, 3, 8, 1, 0, 9, 1539331721, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (234, 13, 7, 8, 1, 0, 9, 1539331727, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (235, 13, 3, 8, 1, 0, 9, 1539331730, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (236, 13, 7, 8, 1, 0, 9, 1539331739, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (237, 13, 7, 8, 1, 0, 9, 1539331742, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (238, 13, 7, 8, 1, 0, 9, 1539331746, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (239, 13, 6, 8, 1, 0, 9, 1539331749, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (240, 13, 7, 8, 1, 0, 9, 1539331759, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (241, 13, 7, 8, 1, 0, 9, 1539332083, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (242, 5, 7, 8, 1, 0, 9, 1539332216, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (243, 5, 7, 8, 1, 0, 9, 1539332219, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (244, 5, 7, 8, 1, 0, 9, 1539332223, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (245, 5, 7, 23, 1, 0, 18, 1539332307, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (246, 5, 6, 23, 1, 0, 18, 1539332436, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (247, 5, 7, 23, 1, 0, 18, 1539332459, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (248, 13, 7, 8, 1, 0, 9, 1539332760, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (249, 10, 7, 8, 1, 0, 9, 1539333020, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (250, 5, 6, 23, 1, 0, 18, 1539333307, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (251, 13, 7, 8, 1, 0, 9, 1539333539, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (252, 13, 7, 8, 1, 0, 9, 1539333881, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (253, 5, 6, 23, 1, 0, 18, 1539334244, NULL, 1, 4, 1, 1);
INSERT INTO `cl_gift_record` VALUES (254, 13, 7, 8, 1, 0, 9, 1539334286, NULL, 1, 3, 1, 0);
INSERT INTO `cl_gift_record` VALUES (255, 23, 7, 8, 1, 0, 9, 1539334324, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (256, 23, 7, 8, 1, 0, 9, 1539334329, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (257, 23, 7, 8, 1, 0, 9, 1539334332, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (258, 23, 7, 8, 1, 0, 9, 1539334358, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (259, 18, 6, 29, 1, 47, 17, 1539416891, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (260, 18, 5, 29, 1, 47, 17, 1539416930, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (261, 18, 4, 29, 1, 47, 17, 1539416932, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (262, 18, 4, 29, 1, 47, 17, 1539416936, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (263, 27, 6, 35, 1, 0, 33, 1539419525, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (264, 27, 6, 35, 1, 0, 33, 1539419527, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (265, 27, 6, 35, 1, 0, 33, 1539419534, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (266, 27, 5, 35, 1, 0, 33, 1539419536, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (267, 27, 4, 35, 1, 0, 33, 1539419538, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (268, 27, 3, 35, 1, 0, 33, 1539419543, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (269, 27, 5, 35, 1, 0, 33, 1539420246, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (270, 27, 5, 35, 1, 0, 33, 1539420248, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (271, 27, 5, 35, 1, 0, 33, 1539420249, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (272, 27, 6, 35, 1, 0, 33, 1539420254, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (273, 27, 6, 35, 1, 0, 33, 1539420255, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (274, 27, 6, 35, 1, 0, 33, 1539420261, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (275, 27, 6, 35, 1, 0, 33, 1539420343, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (276, 27, 6, 35, 1, 0, 33, 1539420345, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (277, 27, 6, 35, 1, 0, 33, 1539420346, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (278, 27, 3, 35, 1, 0, 33, 1539420352, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (279, 27, 7, 35, 1, 0, 33, 1539420649, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (280, 27, 7, 35, 1, 0, 33, 1539420651, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (281, 27, 6, 35, 1, 0, 33, 1539420667, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (282, 27, 6, 35, 1, 0, 33, 1539420669, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (283, 27, 6, 35, 1, 0, 33, 1539420671, NULL, 1, 29, 1, 1);
INSERT INTO `cl_gift_record` VALUES (284, 31, 2, 17, 13, 41, 3, 1539422396, NULL, 1, 17, 1, 0);
INSERT INTO `cl_gift_record` VALUES (285, 31, 6, 27, 1, 41, 25, 1539428821, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (286, 16, 2, 10, 1, 0, 11, 1539571658, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (287, 16, 7, 10, 1, 0, 11, 1539571662, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (288, 31, 3, 10, 1, 41, 11, 1539596080, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (289, 31, 2, 10, 1, 41, 11, 1539596084, NULL, 1, 17, 1, 0);
INSERT INTO `cl_gift_record` VALUES (290, 31, 3, 10, 100, 41, 11, 1539596090, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (291, 31, 2, 10, 1, 41, 11, 1539596102, NULL, 1, 17, 1, 0);
INSERT INTO `cl_gift_record` VALUES (292, 1, 3, 10, 100, 27, 11, 1539596270, NULL, 1, 3, 1, 1);
INSERT INTO `cl_gift_record` VALUES (293, 1, 3, 10, 1, 27, 11, 1539596510, NULL, 1, 3, 1, 1);
INSERT INTO `cl_gift_record` VALUES (294, 20, 3, 10, 1, 0, 11, 1539596800, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (295, 20, 3, 10, 1, 0, 11, 1539597007, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (296, 20, 4, 12, 1, 0, 6, 1539597845, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (297, 31, 4, 31, 1, 0, 29, 1539762404, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (298, 31, 3, 31, 1, 0, 29, 1539762407, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (299, 31, 2, 31, 1, 0, 29, 1539762411, NULL, 1, 17, 1, 0);
INSERT INTO `cl_gift_record` VALUES (300, 31, 4, 31, 1, 0, 29, 1539762413, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (301, 31, 3, 31, 1, 0, 29, 1539764136, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (302, 31, 3, 31, 1, 0, 29, 1539764143, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (303, 31, 3, 31, 1, 0, 29, 1539764145, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (304, 31, 3, 31, 1, 0, 29, 1539764146, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (305, 31, 3, 31, 1, 0, 29, 1539764153, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (306, 29, 6, 31, 1, 51, 29, 1539832393, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (307, 29, 6, 31, 1, 51, 29, 1539832396, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (308, 29, 3, 10, 1, 51, 11, 1539842404, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (309, 29, 3, 10, 1, 51, 11, 1539842408, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (310, 29, 3, 10, 1, 51, 11, 1539842413, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (311, 34, 4, 29, 1, 0, 17, 1539847005, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (312, 34, 3, 29, 1, 0, 17, 1539847009, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (313, 34, 2, 29, 1, 0, 17, 1539847014, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (314, 34, 3, 31, 1, 0, 29, 1539848012, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (315, 34, 3, 31, 1, 0, 29, 1539848017, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (316, 34, 3, 29, 1, 0, 17, 1539849346, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (317, 34, 3, 29, 1, 0, 17, 1539849349, NULL, 1, 11, 1, 0);
INSERT INTO `cl_gift_record` VALUES (318, 29, 4, 29, 1, 51, 17, 1539850930, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (319, 29, 4, 29, 1, 51, 17, 1539850932, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (320, 29, 4, 12, 1, 51, 6, 1539920416, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (321, 29, 7, 12, 1, 51, 6, 1539921232, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (322, 29, 7, 23, 1, 51, 18, 1539921273, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (323, 29, 4, 12, 1, 51, 6, 1539921483, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (324, 29, 7, 23, 1, 51, 18, 1539930785, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (325, 31, 4, 12, 1, 0, 6, 1539930936, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (326, 29, 3, 41, 1, 0, 11, 1539933808, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (327, 29, 3, 41, 1, 0, 11, 1539933854, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (328, 29, 3, 41, 1, 0, 11, 1539933879, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (329, 29, 6, 31, 1, 0, 29, 1539934113, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (330, 29, 7, 31, 1, 0, 29, 1539935262, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (331, 29, 5, 31, 1, 0, 29, 1539935268, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (332, 34, 3, 29, 1, 0, 17, 1539938959, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (333, 34, 3, 29, 1, 0, 17, 1539938960, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (334, 34, 2, 29, 1, 0, 17, 1539938967, NULL, 1, 11, 1, 1);
INSERT INTO `cl_gift_record` VALUES (335, 23, 7, 31, 1, 0, 29, 1539943739, NULL, 1, 6, 1, 1);
INSERT INTO `cl_gift_record` VALUES (336, 31, 6, 7, 1, 0, 1, 1539956480, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (337, 31, 4, 9, 1, 0, 10, 1540542999, NULL, 1, 17, 1, 1);
INSERT INTO `cl_gift_record` VALUES (338, 31, 4, 41, 1, 0, 11, 1540750767, NULL, 1, 17, 1, 1);

-- ----------------------------
-- Table structure for cl_login_log
-- ----------------------------
DROP TABLE IF EXISTS `cl_login_log`;
CREATE TABLE `cl_login_log`  (
  `lid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '访问手机型号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`lid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 56 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '登陆日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_login_log
-- ----------------------------
INSERT INTO `cl_login_log` VALUES (1, 'PC', 1, '127.0.0.1', 1541122185, NULL);
INSERT INTO `cl_login_log` VALUES (2, 'PC', 1, '127.0.0.1', 1541122630, NULL);
INSERT INTO `cl_login_log` VALUES (3, 'PC', 1, '127.0.0.1', 1541122713, NULL);
INSERT INTO `cl_login_log` VALUES (4, 'PC', 1, '127.0.0.1', 1541123646, NULL);
INSERT INTO `cl_login_log` VALUES (5, 'PC', 1, '127.0.0.1', 1541124291, NULL);
INSERT INTO `cl_login_log` VALUES (6, 'PC', 2, '127.0.0.1', 1541124313, NULL);
INSERT INTO `cl_login_log` VALUES (7, 'PC', 1, '127.0.0.1', 1541124364, NULL);
INSERT INTO `cl_login_log` VALUES (8, 'PC', 1, '127.0.0.1', 1541124392, NULL);
INSERT INTO `cl_login_log` VALUES (9, 'PC', 1, '127.0.0.1', 1541124406, NULL);
INSERT INTO `cl_login_log` VALUES (10, 'PC', 5, '127.0.0.1', 1541124408, NULL);
INSERT INTO `cl_login_log` VALUES (11, 'PC', 5, '127.0.0.1', 1541124410, NULL);
INSERT INTO `cl_login_log` VALUES (12, 'PC', 5, '127.0.0.1', 1541124411, NULL);
INSERT INTO `cl_login_log` VALUES (13, 'PC', 5, '127.0.0.1', 1541124411, NULL);
INSERT INTO `cl_login_log` VALUES (14, 'PC', 5, '127.0.0.1', 1541124412, NULL);
INSERT INTO `cl_login_log` VALUES (15, 'PC', 5, '127.0.0.1', 1541124413, NULL);
INSERT INTO `cl_login_log` VALUES (16, 'PC', 5, '127.0.0.1', 1541124414, NULL);
INSERT INTO `cl_login_log` VALUES (17, 'PC', 1, '127.0.0.1', 1541124419, NULL);
INSERT INTO `cl_login_log` VALUES (18, 'PC', 6, '127.0.0.1', 1541124469, NULL);
INSERT INTO `cl_login_log` VALUES (19, 'PC', 7, '127.0.0.1', 1541124494, NULL);
INSERT INTO `cl_login_log` VALUES (20, 'PC', 8, '127.0.0.1', 1541124526, NULL);
INSERT INTO `cl_login_log` VALUES (21, 'PC', 1, '127.0.0.1', 1541124616, NULL);
INSERT INTO `cl_login_log` VALUES (22, 'PC', 1, '127.0.0.1', 1541125804, NULL);
INSERT INTO `cl_login_log` VALUES (23, 'PC', 1, '127.0.0.1', 1541125871, NULL);
INSERT INTO `cl_login_log` VALUES (24, 'PC', 1, '127.0.0.1', 1541125872, NULL);
INSERT INTO `cl_login_log` VALUES (25, 'PC', 1, '127.0.0.1', 1541125873, NULL);
INSERT INTO `cl_login_log` VALUES (26, 'PC', 1, '127.0.0.1', 1541125880, NULL);
INSERT INTO `cl_login_log` VALUES (27, 'PC', 1, '127.0.0.1', 1541125898, NULL);
INSERT INTO `cl_login_log` VALUES (28, 'PC', 2, '127.0.0.1', 1541127630, NULL);
INSERT INTO `cl_login_log` VALUES (29, 'PC', 3, '127.0.0.1', 1541127661, NULL);
INSERT INTO `cl_login_log` VALUES (30, 'PC', 3, '127.0.0.1', 1541127670, NULL);
INSERT INTO `cl_login_log` VALUES (31, 'PC', 3, '127.0.0.1', 1541127671, NULL);
INSERT INTO `cl_login_log` VALUES (32, 'PC', 3, '127.0.0.1', 1541127672, NULL);
INSERT INTO `cl_login_log` VALUES (33, 'PC', 3, '127.0.0.1', 1541127673, NULL);
INSERT INTO `cl_login_log` VALUES (34, 'PC', 3, '127.0.0.1', 1541127674, NULL);
INSERT INTO `cl_login_log` VALUES (35, 'PC', 3, '127.0.0.1', 1541127675, NULL);
INSERT INTO `cl_login_log` VALUES (36, 'PC', 3, '127.0.0.1', 1541127677, NULL);
INSERT INTO `cl_login_log` VALUES (37, 'PC', 3, '127.0.0.1', 1541127678, NULL);
INSERT INTO `cl_login_log` VALUES (38, 'PC', 3, '127.0.0.1', 1541127679, NULL);
INSERT INTO `cl_login_log` VALUES (39, 'PC', 3, '127.0.0.1', 1541127680, NULL);
INSERT INTO `cl_login_log` VALUES (40, 'PC', 3, '127.0.0.1', 1541127682, NULL);
INSERT INTO `cl_login_log` VALUES (41, 'PC', 3, '127.0.0.1', 1541127684, NULL);
INSERT INTO `cl_login_log` VALUES (42, 'PC', 3, '127.0.0.1', 1541127686, NULL);
INSERT INTO `cl_login_log` VALUES (43, 'PC', 3, '127.0.0.1', 1541127687, NULL);
INSERT INTO `cl_login_log` VALUES (44, 'PC', 3, '127.0.0.1', 1541127688, NULL);
INSERT INTO `cl_login_log` VALUES (45, 'PC', 3, '127.0.0.1', 1541127689, NULL);
INSERT INTO `cl_login_log` VALUES (46, 'PC', 3, '127.0.0.1', 1541127691, NULL);
INSERT INTO `cl_login_log` VALUES (47, 'PC', 3, '127.0.0.1', 1541127692, NULL);
INSERT INTO `cl_login_log` VALUES (48, 'PC', 3, '127.0.0.1', 1541128123, NULL);
INSERT INTO `cl_login_log` VALUES (49, 'PC', 4, '127.0.0.1', 1541128127, NULL);
INSERT INTO `cl_login_log` VALUES (50, 'PC', 5, '127.0.0.1', 1541128131, NULL);
INSERT INTO `cl_login_log` VALUES (51, 'PC', 5, '127.0.0.1', 1541128147, NULL);
INSERT INTO `cl_login_log` VALUES (52, 'PC', 6, '127.0.0.1', 1541128786, NULL);
INSERT INTO `cl_login_log` VALUES (53, 'PC', 7, '127.0.0.1', 1541128819, NULL);
INSERT INTO `cl_login_log` VALUES (54, 'PC', 1, '127.0.0.1', 1541137752, NULL);
INSERT INTO `cl_login_log` VALUES (55, 'PC', 1, '127.0.0.1', 1541137755, NULL);

-- ----------------------------
-- Table structure for cl_message
-- ----------------------------
DROP TABLE IF EXISTS `cl_message`;
CREATE TABLE `cl_message`  (
  `mid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联用户id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `type` tinyint(1) NOT NULL COMMENT '0 => 系统消息  1 => 活动通知',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1=>正常 0=>禁用',
  `update_time` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`mid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '消息中心' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_message
-- ----------------------------
INSERT INTO `cl_message` VALUES (2, 0, '系统消息', '欢迎来到soho区块链直播', 0, 1, 1539566460, 1539396697);

-- ----------------------------
-- Table structure for cl_money_detail
-- ----------------------------
DROP TABLE IF EXISTS `cl_money_detail`;
CREATE TABLE `cl_money_detail`  (
  `d_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_num` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>收入 2=>支出 ',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `money` decimal(10, 2) NOT NULL COMMENT '金额',
  `money_type` tinyint(1) NOT NULL COMMENT '1=>红包 2=>竞猜 3=>兑换 4=>拍卖 5=>直播间付费 6=>充值 7=>其它 8=>礼物赠送',
  `coin_type` tinyint(1) NOT NULL COMMENT '金币类型 1=>积分 2=>比特币  3=>以太币',
  `status` tinyint(1) NOT NULL COMMENT '1=>正常 0=>错误',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`d_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1158 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '钱包资金明细表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_money_detail
-- ----------------------------
INSERT INTO `cl_money_detail` VALUES (1, 'RELNyznyXM201810089397', 2, 18, '', -999.99, 8, 1, 1, 1538988181, NULL);
INSERT INTO `cl_money_detail` VALUES (2, 'REvqp4ZyMo201810084606', 2, 10, '', -123.00, 1, 1, 1, 1538988365, NULL);
INSERT INTO `cl_money_detail` VALUES (3, 'REvqp4ZyMo201810082711', 2, 10, '', -55.00, 1, 1, 1, 1538988389, NULL);
INSERT INTO `cl_money_detail` VALUES (4, 'RELNyznyXM201810089759', 2, 18, '', -123.00, 1, 1, 1, 1538988477, NULL);
INSERT INTO `cl_money_detail` VALUES (5, 'REvqp4ZyMo201810087089', 2, 10, '', -1233.00, 1, 1, 1, 1538988928, NULL);
INSERT INTO `cl_money_detail` VALUES (6, 'RELbyJGK5d201810088462', 2, 6, '', -200.00, 1, 1, 1, 1538988973, NULL);
INSERT INTO `cl_money_detail` VALUES (7, 'REbOygWyo9201810088490', 1, 9, '', 200.00, 1, 1, 1, 1538988977, NULL);
INSERT INTO `cl_money_detail` VALUES (8, 'RELNyznyXM201810088392', 2, 18, '', -999.99, 8, 1, 1, 1538989059, NULL);
INSERT INTO `cl_money_detail` VALUES (9, 'REvqp4ZyMo201810084496', 2, 10, '', -123.00, 1, 1, 1, 1538989076, NULL);
INSERT INTO `cl_money_detail` VALUES (10, 'RELNyznyXM201810087933', 2, 18, '', -999.99, 8, 1, 1, 1538989101, NULL);
INSERT INTO `cl_money_detail` VALUES (11, 'REvqp4ZyMo201810089790', 2, 10, '', -999.99, 8, 1, 1, 1538989110, NULL);
INSERT INTO `cl_money_detail` VALUES (12, 'RELNyznyXM201810082851', 2, 18, '', -12.00, 1, 1, 1, 1538989162, NULL);
INSERT INTO `cl_money_detail` VALUES (13, 'REvqp4ZyMo201810082096', 2, 10, '', -12.00, 1, 1, 1, 1538989176, NULL);
INSERT INTO `cl_money_detail` VALUES (14, 'REbOygWyo9201810087763', 2, 9, '', -1.00, 1, 1, 1, 1538989233, NULL);
INSERT INTO `cl_money_detail` VALUES (15, 'REbOygWyo9201810082983', 2, 9, '', -999.99, 8, 1, 1, 1538989241, NULL);
INSERT INTO `cl_money_detail` VALUES (16, 'RELNyznyXM201810084095', 2, 18, '', -999.99, 8, 1, 1, 1538989266, NULL);
INSERT INTO `cl_money_detail` VALUES (17, 'REvqp4ZyMo201810084956', 2, 10, '', -123.00, 1, 1, 1, 1538989512, NULL);
INSERT INTO `cl_money_detail` VALUES (18, 'RELNyznyXM201810087013', 2, 18, '', -123.00, 1, 1, 1, 1538993644, NULL);
INSERT INTO `cl_money_detail` VALUES (19, 'RELNyznyXM201810086659', 2, 18, '', -999.99, 8, 1, 1, 1538993676, NULL);
INSERT INTO `cl_money_detail` VALUES (20, 'REvqp4ZyMo201810082863', 2, 10, '', -123.00, 1, 1, 1, 1538993693, NULL);
INSERT INTO `cl_money_detail` VALUES (21, 'RELNyznyXM201810087362', 2, 18, '', -123.00, 1, 1, 1, 1538993731, NULL);
INSERT INTO `cl_money_detail` VALUES (22, 'REvqp4ZyMo201810081758', 2, 10, '', -123.00, 1, 1, 1, 1538994372, NULL);
INSERT INTO `cl_money_detail` VALUES (23, 'REvqp4ZyMo201810083742', 2, 10, '', -123.00, 1, 1, 1, 1538994628, NULL);
INSERT INTO `cl_money_detail` VALUES (24, 'REvqp4ZyMo201810089723', 2, 10, '', -123.00, 1, 1, 1, 1538994788, NULL);
INSERT INTO `cl_money_detail` VALUES (25, 'REvqp4ZyMo201810082525', 2, 10, '', -123.00, 1, 1, 1, 1538994845, NULL);
INSERT INTO `cl_money_detail` VALUES (26, 'REvqp4ZyMo201810087885', 2, 10, '', -123.00, 1, 1, 1, 1538995170, NULL);
INSERT INTO `cl_money_detail` VALUES (27, 'REvqp4ZyMo201810085366', 2, 10, '', -123.00, 1, 1, 1, 1538995239, NULL);
INSERT INTO `cl_money_detail` VALUES (28, 'RELNyznyXM201810082444', 2, 18, '', -1232.00, 1, 1, 1, 1538995252, NULL);
INSERT INTO `cl_money_detail` VALUES (29, 'RELNyznyXM201810086391', 2, 18, '', -123.00, 1, 1, 1, 1538995451, NULL);
INSERT INTO `cl_money_detail` VALUES (30, 'RELNyznyXM201810087177', 2, 18, '', -199.99, 8, 1, 1, 1538995529, NULL);
INSERT INTO `cl_money_detail` VALUES (31, 'REvqp4ZyMo201810084626', 2, 10, '', -123.00, 1, 1, 1, 1538995557, NULL);
INSERT INTO `cl_money_detail` VALUES (32, 'RELNyznyXM201810081425', 2, 18, '', -199.99, 8, 1, 1, 1538995588, NULL);
INSERT INTO `cl_money_detail` VALUES (33, 'RELNyznyXM201810081522', 2, 18, '', -123.00, 1, 1, 1, 1538995596, NULL);
INSERT INTO `cl_money_detail` VALUES (34, 'RELNyznyXM201810087957', 2, 18, '', -999.99, 8, 1, 1, 1538995705, NULL);
INSERT INTO `cl_money_detail` VALUES (35, 'RELNyznyXM201810083317', 2, 18, '', -42.00, 1, 1, 1, 1538995719, NULL);
INSERT INTO `cl_money_detail` VALUES (36, 'RELNyznyXM201810084792', 2, 18, '', -999.99, 8, 1, 1, 1538995781, NULL);
INSERT INTO `cl_money_detail` VALUES (37, 'RELNyznyXM201810084118', 2, 18, '', -999.99, 8, 1, 1, 1538995804, NULL);
INSERT INTO `cl_money_detail` VALUES (38, 'RELNyznyXM201810088814', 2, 18, '', -999.99, 8, 1, 1, 1538995833, NULL);
INSERT INTO `cl_money_detail` VALUES (39, 'RELbyJGK5d201810082818', 2, 6, '', -1999.98, 8, 1, 1, 1538995872, NULL);
INSERT INTO `cl_money_detail` VALUES (40, 'RELbyJGK5d201810081746', 2, 6, '', -1999.98, 8, 1, 1, 1538995948, NULL);
INSERT INTO `cl_money_detail` VALUES (41, 'RELNyznyXM201810083176', 2, 18, '', -123.00, 1, 1, 1, 1538996280, NULL);
INSERT INTO `cl_money_detail` VALUES (42, 'REvqp4ZyMo201810082552', 2, 10, '', -999.99, 8, 1, 1, 1538996331, NULL);
INSERT INTO `cl_money_detail` VALUES (43, 'REvqp4ZyMo201810082593', 2, 10, '', -999.99, 8, 1, 1, 1538996425, NULL);
INSERT INTO `cl_money_detail` VALUES (44, 'REvqp4ZyMo201810085610', 2, 10, '', -999.99, 8, 1, 1, 1538996926, NULL);
INSERT INTO `cl_money_detail` VALUES (45, 'RELNyznyXM201810081529', 2, 18, '', -12.00, 1, 1, 1, 1538996939, NULL);
INSERT INTO `cl_money_detail` VALUES (46, 'RELbyJGK5d201810084539', 2, 6, '', -999.99, 8, 1, 1, 1538997219, NULL);
INSERT INTO `cl_money_detail` VALUES (47, 'RELbyJGK5d201810086828', 2, 6, '', -9.00, 1, 4, 1, 1538997250, NULL);
INSERT INTO `cl_money_detail` VALUES (48, 'RE4ZBLXBMN201810085356', 1, 7, '', 1.50, 1, 4, 1, 1538997255, NULL);
INSERT INTO `cl_money_detail` VALUES (49, 'RELbyJGK5d201810083785', 1, 6, '', 1.50, 1, 4, 1, 1538997262, NULL);
INSERT INTO `cl_money_detail` VALUES (50, 'RELbyJGK5d201810082410', 2, 6, '', -9.00, 1, 4, 1, 1538997301, NULL);
INSERT INTO `cl_money_detail` VALUES (51, 'RELbyJGK5d201810088902', 1, 6, '', 1.50, 1, 4, 1, 1538997304, NULL);
INSERT INTO `cl_money_detail` VALUES (52, 'RE4ZBLXBMN201810087132', 1, 7, '', 6.00, 1, 4, 1, 1538997307, NULL);
INSERT INTO `cl_money_detail` VALUES (53, 'RELbyJGK5d201810081528', 2, 6, '', -9.00, 1, 4, 1, 1538997332, NULL);
INSERT INTO `cl_money_detail` VALUES (54, 'RELbyJGK5d201810085730', 2, 6, '', -9.00, 1, 4, 1, 1538997349, NULL);
INSERT INTO `cl_money_detail` VALUES (55, 'RELbyJGK5d201810081153', 2, 6, '', -9.00, 1, 4, 1, 1538997367, NULL);
INSERT INTO `cl_money_detail` VALUES (56, 'REvqp4ZyMo201810083686', 2, 10, '', -999.99, 8, 1, 1, 1538997749, NULL);
INSERT INTO `cl_money_detail` VALUES (57, 'REvqp4ZyMo201810082762', 2, 10, '', -999.99, 8, 1, 1, 1538997780, NULL);
INSERT INTO `cl_money_detail` VALUES (58, 'REvqp4ZyMo201810088118', 2, 10, '', -999.99, 8, 1, 1, 1538997855, NULL);
INSERT INTO `cl_money_detail` VALUES (59, 'REvqp4ZyMo201810082425', 2, 10, '', -999.99, 8, 1, 1, 1538998017, NULL);
INSERT INTO `cl_money_detail` VALUES (60, 'RELbyJGK5d201810083542', 2, 6, '拍卖房间小兰加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1538998547, NULL);
INSERT INTO `cl_money_detail` VALUES (61, 'RELbyJGK5d201810087335', 2, 6, '拍卖角色加价成功，冻结竞拍金额 ', -100000.00, 4, 4, 1, 1538998561, NULL);
INSERT INTO `cl_money_detail` VALUES (62, 'RELbyJGK5d201810084878', 2, 6, '升级空间扣除6000', -6000.00, 7, 1, 1, 1538998654, NULL);
INSERT INTO `cl_money_detail` VALUES (63, 'REvqp4ZyMo201810084760', 2, 10, '', -999.99, 8, 1, 1, 1538998663, NULL);
INSERT INTO `cl_money_detail` VALUES (64, 'RE201810086082', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1538998741, NULL);
INSERT INTO `cl_money_detail` VALUES (65, 'REbOygWyo9201810085560', 2, 9, '', -999.99, 8, 1, 1, 1538999995, NULL);
INSERT INTO `cl_money_detail` VALUES (66, 'REbOygWyo9201810086950', 2, 9, '', -11999.88, 8, 1, 1, 1539000001, NULL);
INSERT INTO `cl_money_detail` VALUES (67, 'RELbyJGK5d201810083038', 2, 6, '升级空间扣除6000', -6000.00, 7, 1, 1, 1539000285, NULL);
INSERT INTO `cl_money_detail` VALUES (68, 'REvqp4ZyMo201810083047', 2, 10, '', -999.99, 8, 1, 1, 1539000791, NULL);
INSERT INTO `cl_money_detail` VALUES (69, 'RELNyznyXM201810089581', 2, 18, '', -566.00, 1, 1, 1, 1539000848, NULL);
INSERT INTO `cl_money_detail` VALUES (70, 'RELNyznyXM201810085297', 2, 18, '', -123.00, 1, 1, 1, 1539000907, NULL);
INSERT INTO `cl_money_detail` VALUES (71, 'RELNyznyXM201810089569', 2, 18, '', -123.00, 1, 1, 1, 1539001134, NULL);
INSERT INTO `cl_money_detail` VALUES (72, 'RELbyJGK5d201810084124', 2, 6, '升级空间扣除4000', -4000.00, 7, 1, 1, 1539001233, NULL);
INSERT INTO `cl_money_detail` VALUES (73, 'REvqp4ZyMo201810085012', 2, 10, '', -999.99, 8, 1, 1, 1539001357, NULL);
INSERT INTO `cl_money_detail` VALUES (74, 'RELbyJGK5d201810085939', 2, 6, '升级空间扣除4000', -4000.00, 7, 1, 1, 1539001702, NULL);
INSERT INTO `cl_money_detail` VALUES (75, 'RELNyznyXM201810083657', 2, 18, '', -999.99, 8, 1, 1, 1539001731, NULL);
INSERT INTO `cl_money_detail` VALUES (76, 'RELbyJGK5d201810086242', 2, 6, '升级空间扣除4000', -4000.00, 7, 1, 1, 1539001764, NULL);
INSERT INTO `cl_money_detail` VALUES (77, 'REvqp4ZyMo201810086047', 2, 10, '', -2.00, 1, 1, 1, 1539003059, NULL);
INSERT INTO `cl_money_detail` VALUES (78, 'RELNyznyXM201810081500', 2, 18, '', -999.99, 8, 1, 1, 1539003065, NULL);
INSERT INTO `cl_money_detail` VALUES (79, 'RELbyJGK5d201810088609', 2, 6, '', -90.00, 1, 4, 1, 1539003271, NULL);
INSERT INTO `cl_money_detail` VALUES (80, 'REvqp4ZyMo201810089760', 2, 10, '', -999.99, 8, 1, 1, 1539004970, NULL);
INSERT INTO `cl_money_detail` VALUES (81, 'REjGBP6KXA201810094548', 2, 3, '认证抵押资产', -7000.00, 7, 1, 1, 1539050667, NULL);
INSERT INTO `cl_money_detail` VALUES (82, 'RE14KVWp2X201810097488', 2, 11, '', -28.00, 1, 4, 1, 1539052940, NULL);
INSERT INTO `cl_money_detail` VALUES (83, 'RE14KVWp2X201810098503', 1, 11, '', 28.00, 1, 4, 1, 1539052943, NULL);
INSERT INTO `cl_money_detail` VALUES (84, 'RE14KVWp2X201810098733', 2, 11, '', -20.00, 1, 4, 1, 1539052965, NULL);
INSERT INTO `cl_money_detail` VALUES (85, 'RE14KVWp2X201810094432', 1, 11, '', 13.34, 1, 4, 1, 1539052967, NULL);
INSERT INTO `cl_money_detail` VALUES (86, 'RELbyJGK5d201810092650', 1, 6, '', 3.33, 1, 4, 1, 1539052974, NULL);
INSERT INTO `cl_money_detail` VALUES (87, 'RE14KVWp2X201810099725', 2, 11, '', -0.20, 1, 1, 1, 1539053040, NULL);
INSERT INTO `cl_money_detail` VALUES (88, 'RE14KVWp2X201810092335', 1, 11, '', 0.06, 1, 1, 1, 1539053042, NULL);
INSERT INTO `cl_money_detail` VALUES (89, 'RELbyJGK5d201810097783', 1, 6, '', 0.14, 1, 1, 1, 1539053053, NULL);
INSERT INTO `cl_money_detail` VALUES (90, 'RE14KVWp2X201810092356', 2, 11, '', -19.99, 8, 1, 1, 1539053080, NULL);
INSERT INTO `cl_money_detail` VALUES (91, 'RE14KVWp2X201810093571', 2, 11, '', -49.99, 8, 1, 1, 1539053110, NULL);
INSERT INTO `cl_money_detail` VALUES (92, 'RE14KVWp2X201810095638', 2, 11, '', -49.99, 8, 1, 1, 1539053122, NULL);
INSERT INTO `cl_money_detail` VALUES (93, 'RE14KVWp2X201810099064', 2, 11, '', -999.99, 8, 1, 1, 1539053428, NULL);
INSERT INTO `cl_money_detail` VALUES (94, 'RE14KVWp2X201810097663', 2, 11, '', -999.99, 8, 1, 1, 1539053491, NULL);
INSERT INTO `cl_money_detail` VALUES (95, 'RE14KVWp2X201810096170', 2, 11, '', -1000.00, 1, 1, 1, 1539053579, NULL);
INSERT INTO `cl_money_detail` VALUES (96, 'RE14KVWp2X201810097342', 2, 11, '', -10.00, 1, 1, 1, 1539053591, NULL);
INSERT INTO `cl_money_detail` VALUES (97, 'RE14KVWp2X201810096342', 1, 11, '', 49.99, 1, 1, 1, 1539053594, NULL);
INSERT INTO `cl_money_detail` VALUES (98, 'RE14KVWp2X201810098485', 1, 11, '', 4999.99, 1, 1, 1, 1539053600, NULL);
INSERT INTO `cl_money_detail` VALUES (99, 'RE14KVWp2X201810093220', 2, 11, '', -20.00, 1, 4, 1, 1539053648, NULL);
INSERT INTO `cl_money_detail` VALUES (100, 'RE14KVWp2X201810091794', 1, 11, '', 99.00, 1, 4, 1, 1539053650, NULL);
INSERT INTO `cl_money_detail` VALUES (101, 'RE14KVWp2X201810093458', 2, 11, '', -999.99, 8, 1, 1, 1539053724, NULL);
INSERT INTO `cl_money_detail` VALUES (102, 'RE14KVWp2X201810092475', 2, 11, '', -999.99, 8, 1, 1, 1539053728, NULL);
INSERT INTO `cl_money_detail` VALUES (103, 'RE14KVWp2X201810099031', 2, 11, '', -49.99, 8, 1, 1, 1539053866, NULL);
INSERT INTO `cl_money_detail` VALUES (104, 'RE14KVWp2X201810092529', 2, 11, '认证抵押资产', -500.00, 7, 1, 1, 1539054514, NULL);
INSERT INTO `cl_money_detail` VALUES (105, 'RE14KVWp2X201810098830', 2, 11, '拍卖房间区块链保安加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539054602, NULL);
INSERT INTO `cl_money_detail` VALUES (106, 'RE14KVWp2X201810092746', 2, 11, '拍卖房间区块链安保加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539054616, NULL);
INSERT INTO `cl_money_detail` VALUES (107, 'REvqp4ZyMo201810094097', 2, 10, '', -999.99, 8, 1, 1, 1539054713, NULL);
INSERT INTO `cl_money_detail` VALUES (108, 'RE201810092829', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539054841, NULL);
INSERT INTO `cl_money_detail` VALUES (109, 'RE201810098132', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539054841, NULL);
INSERT INTO `cl_money_detail` VALUES (110, 'REvqp4ZyMo201810095309', 2, 10, '', -999.99, 8, 1, 1, 1539056296, NULL);
INSERT INTO `cl_money_detail` VALUES (111, 'REvqp4ZyMo201810097361', 2, 10, '', -199.99, 8, 1, 1, 1539056301, NULL);
INSERT INTO `cl_money_detail` VALUES (112, 'REjGBP6KXA201810098613', 2, 3, '直播间升级付费', -20000.00, 7, 1, 1, 1539067687, NULL);
INSERT INTO `cl_money_detail` VALUES (113, 'RE14KVWp2X201810091211', 2, 11, '', -0.02, 1, 1, 1, 1539070537, NULL);
INSERT INTO `cl_money_detail` VALUES (114, 'RE14KVWp2X201810092012', 1, 11, '', 0.01, 1, 1, 1, 1539070541, NULL);
INSERT INTO `cl_money_detail` VALUES (115, 'RE14KVWp2X201810099546', 2, 11, '', -1.00, 1, 4, 1, 1539070597, NULL);
INSERT INTO `cl_money_detail` VALUES (116, 'REPRBaZyZ2201810093448', 1, 2, '', 1.00, 1, 4, 1, 1539070649, NULL);
INSERT INTO `cl_money_detail` VALUES (117, 'REPRBaZyZ2201810093144', 1, 2, '', 0.01, 1, 1, 1, 1539070654, NULL);
INSERT INTO `cl_money_detail` VALUES (118, 'REPRBaZyZ2201810095695', 2, 2, '', -2.00, 1, 1, 1, 1539070677, NULL);
INSERT INTO `cl_money_detail` VALUES (119, 'REPRBaZyZ2201810099242', 1, 2, '', 1.00, 1, 1, 1, 1539070681, NULL);
INSERT INTO `cl_money_detail` VALUES (120, 'REPRBaZyZ2201810097866', 2, 2, '', -12.00, 1, 4, 1, 1539070739, NULL);
INSERT INTO `cl_money_detail` VALUES (121, 'REPRBaZyZ2201810091617', 1, 2, '', 4.00, 1, 4, 1, 1539070741, NULL);
INSERT INTO `cl_money_detail` VALUES (122, 'RE14KVWp2X201810096390', 2, 11, '', -3.00, 1, 4, 1, 1539070744, NULL);
INSERT INTO `cl_money_detail` VALUES (123, 'RE14KVWp2X201810094427', 2, 11, '', -0.02, 1, 1, 1, 1539070765, NULL);
INSERT INTO `cl_money_detail` VALUES (124, 'REPRBaZyZ2201810093375', 1, 2, '', 0.01, 1, 1, 1, 1539070823, NULL);
INSERT INTO `cl_money_detail` VALUES (125, 'REPRBaZyZ2201810099352', 1, 2, '', 1.00, 1, 4, 1, 1539070832, NULL);
INSERT INTO `cl_money_detail` VALUES (126, 'REPRBaZyZ2201810096136', 2, 2, '', -19.99, 8, 1, 1, 1539070870, NULL);
INSERT INTO `cl_money_detail` VALUES (127, 'REPRBaZyZ2201810097107', 2, 2, '', -19.99, 8, 1, 1, 1539070873, NULL);
INSERT INTO `cl_money_detail` VALUES (128, 'REPRBaZyZ2201810095740', 2, 2, '', -19.99, 8, 1, 1, 1539070881, NULL);
INSERT INTO `cl_money_detail` VALUES (129, 'RE14KVWp2X201810093438', 2, 11, '', -0.02, 1, 1, 1, 1539071129, NULL);
INSERT INTO `cl_money_detail` VALUES (130, 'RE14KVWp2X201810097419', 2, 11, '', -0.01, 1, 1, 1, 1539071146, NULL);
INSERT INTO `cl_money_detail` VALUES (131, 'RE14KVWp2X201810091262', 1, 11, '', 0.01, 1, 1, 1, 1539071149, NULL);
INSERT INTO `cl_money_detail` VALUES (132, 'REPRBaZyZ2201810093725', 2, 2, '', -2.00, 1, 1, 1, 1539071295, NULL);
INSERT INTO `cl_money_detail` VALUES (133, 'REPRBaZyZ2201810093287', 1, 2, '', 2.00, 1, 1, 1, 1539071298, NULL);
INSERT INTO `cl_money_detail` VALUES (134, 'RELbyJGK5d201810099085', 2, 6, '拍卖房间哈哈加价成功，冻结竞拍金额 ', -100003.00, 4, 1, 1, 1539074230, NULL);
INSERT INTO `cl_money_detail` VALUES (135, 'RE201810098095', 1, NULL, '拍卖成功,获得拍卖价100003.00', 100003.00, 4, 1, 1, 1539074461, NULL);
INSERT INTO `cl_money_detail` VALUES (136, 'REPRBaZyZ2201810095153', 2, 2, '直播间升级付费', -10000.00, 7, 1, 1, 1539074654, NULL);
INSERT INTO `cl_money_detail` VALUES (137, 'REbOygWyo9201810098782', 2, 9, '', -1.00, 1, 1, 1, 1539075799, NULL);
INSERT INTO `cl_money_detail` VALUES (138, 'REPRBaZyZ2201810098767', 2, 2, '', -20.00, 1, 1, 1, 1539075805, NULL);
INSERT INTO `cl_money_detail` VALUES (139, 'RE14KVWp2X201810097530', 1, 11, '', 20.00, 1, 1, 1, 1539075810, NULL);
INSERT INTO `cl_money_detail` VALUES (140, 'REPRBaZyZ2201810097875', 2, 2, '', -28.00, 1, 1, 1, 1539075822, NULL);
INSERT INTO `cl_money_detail` VALUES (141, 'REPRBaZyZ2201810096879', 1, 2, '', 14.00, 1, 1, 1, 1539075825, NULL);
INSERT INTO `cl_money_detail` VALUES (142, 'RE14KVWp2X201810098772', 1, 11, '', 14.00, 1, 1, 1, 1539075829, NULL);
INSERT INTO `cl_money_detail` VALUES (143, 'REPRBaZyZ2201810098927', 2, 2, '', -28.00, 1, 1, 1, 1539075839, NULL);
INSERT INTO `cl_money_detail` VALUES (144, 'REPRBaZyZ2201810098507', 1, 2, '', 14.00, 1, 1, 1, 1539075840, NULL);
INSERT INTO `cl_money_detail` VALUES (145, 'REPRBaZyZ2201810099037', 2, 2, '', -20.00, 1, 1, 1, 1539075865, NULL);
INSERT INTO `cl_money_detail` VALUES (146, 'REPRBaZyZ2201810091603', 1, 2, '', 10.00, 1, 1, 1, 1539075867, NULL);
INSERT INTO `cl_money_detail` VALUES (147, 'REPRBaZyZ2201810093902', 2, 2, '', -20.00, 1, 1, 1, 1539075906, NULL);
INSERT INTO `cl_money_detail` VALUES (148, 'RE14KVWp2X201810098223', 1, 11, '', 10.00, 1, 1, 1, 1539075910, NULL);
INSERT INTO `cl_money_detail` VALUES (149, 'REPRBaZyZ2201810099730', 1, 2, '', 10.00, 1, 1, 1, 1539075914, NULL);
INSERT INTO `cl_money_detail` VALUES (150, 'REPRBaZyZ2201810093173', 2, 2, '', -20.00, 1, 1, 1, 1539075928, NULL);
INSERT INTO `cl_money_detail` VALUES (151, 'RE14KVWp2X201810097068', 1, 11, '', 10.00, 1, 1, 1, 1539075933, NULL);
INSERT INTO `cl_money_detail` VALUES (152, 'REPRBaZyZ2201810096474', 1, 2, '', 10.00, 1, 1, 1, 1539075937, NULL);
INSERT INTO `cl_money_detail` VALUES (153, 'REPRBaZyZ2201810097994', 2, 2, '', -19.99, 8, 1, 1, 1539075945, NULL);
INSERT INTO `cl_money_detail` VALUES (154, 'RE14KVWp2X201810099653', 2, 11, '拍卖房间锤子', -100000.00, 4, 1, 1, 1539079436, NULL);
INSERT INTO `cl_money_detail` VALUES (155, 'RE201810095744', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539079622, NULL);
INSERT INTO `cl_money_detail` VALUES (156, 'REvqp4ZyMo201810095874', 1, 10, '', 0.00, 8, 1, 1, 1539080290, NULL);
INSERT INTO `cl_money_detail` VALUES (157, 'REvqp4ZyMo201810095714', 2, 10, '', -999.99, 8, 1, 1, 1539080316, NULL);
INSERT INTO `cl_money_detail` VALUES (158, 'REvqp4ZyMo201810099853', 2, 10, '', -199.99, 8, 1, 1, 1539080322, NULL);
INSERT INTO `cl_money_detail` VALUES (159, 'REvqp4ZyMo201810093137', 2, 10, '', -549.89, 8, 1, 1, 1539080334, NULL);
INSERT INTO `cl_money_detail` VALUES (160, 'REvqp4ZyMo201810093372', 2, 10, '', -10999.89, 8, 1, 1, 1539080346, NULL);
INSERT INTO `cl_money_detail` VALUES (161, 'REvqp4ZyMo201810094561', 2, 10, '', -120.00, 1, 1, 1, 1539080375, NULL);
INSERT INTO `cl_money_detail` VALUES (162, 'REvqp4ZyMo201810094360', 1, 10, '', 60.00, 1, 1, 1, 1539080380, NULL);
INSERT INTO `cl_money_detail` VALUES (163, 'REPRBaZyZ2201810099551', 2, 2, '', -19.99, 8, 1, 1, 1539080437, NULL);
INSERT INTO `cl_money_detail` VALUES (164, 'RELbyJGK5d201810093439', 1, 6, '红包未领完返还', 9.00, 7, 4, 1, 1539083762, NULL);
INSERT INTO `cl_money_detail` VALUES (165, 'RELbyJGK5d201810092184', 1, 6, '红包未领完返还', 9.00, 7, 4, 1, 1539083762, NULL);
INSERT INTO `cl_money_detail` VALUES (166, 'RELbyJGK5d201810092172', 1, 6, '红包未领完返还', 9.00, 7, 4, 1, 1539083821, NULL);
INSERT INTO `cl_money_detail` VALUES (167, 'RE201810099984', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539084962, NULL);
INSERT INTO `cl_money_detail` VALUES (168, 'RELNyznyXM201810093819', 1, 18, '红包未领完返还', 566.00, 7, 1, 1, 1539087302, NULL);
INSERT INTO `cl_money_detail` VALUES (169, 'RELNyznyXM201810094150', 1, 18, '红包未领完返还', 123.00, 7, 1, 1, 1539087361, NULL);
INSERT INTO `cl_money_detail` VALUES (170, 'RELNyznyXM201810097942', 1, 18, '红包未领完返还', 123.00, 7, 1, 1, 1539087541, NULL);
INSERT INTO `cl_money_detail` VALUES (171, 'REvqp4ZyMo201810094266', 1, 10, '红包未领完返还', 2.00, 7, 1, 1, 1539089461, NULL);
INSERT INTO `cl_money_detail` VALUES (172, 'RELbyJGK5d201810093916', 1, 6, '红包未领完返还', 90.00, 7, 4, 1, 1539089701, NULL);
INSERT INTO `cl_money_detail` VALUES (173, 'RE14KVWp2X201810103337', 2, 11, '拍卖房间...wen加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539134641, NULL);
INSERT INTO `cl_money_detail` VALUES (174, 'RE201810109490', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539134821, NULL);
INSERT INTO `cl_money_detail` VALUES (175, 'RE14KVWp2X201810105084', 1, 11, '红包未领完返还', 3.33, 7, 4, 1, 1539139382, NULL);
INSERT INTO `cl_money_detail` VALUES (176, 'RELNyznyXM201810101306', 2, 18, '', -100.00, 1, 1, 1, 1539141847, NULL);
INSERT INTO `cl_money_detail` VALUES (177, 'RELNyznyXM201810106695', 2, 18, '', -123.00, 1, 1, 1, 1539142629, NULL);
INSERT INTO `cl_money_detail` VALUES (178, 'RELNyznyXM201810105759', 2, 18, '', -12.00, 1, 1, 1, 1539142882, NULL);
INSERT INTO `cl_money_detail` VALUES (179, 'RELNyznyXM201810104061', 2, 18, '', -123.00, 1, 1, 1, 1539142919, NULL);
INSERT INTO `cl_money_detail` VALUES (180, 'REvqp4ZyMo201810105642', 2, 10, '', -23.00, 1, 1, 1, 1539143442, NULL);
INSERT INTO `cl_money_detail` VALUES (181, 'REvqp4ZyMo201810109219', 2, 10, '', -23.00, 1, 1, 1, 1539143734, NULL);
INSERT INTO `cl_money_detail` VALUES (182, 'RE14KVWp2X201810106106', 2, 11, '', -20.00, 1, 1, 1, 1539149702, NULL);
INSERT INTO `cl_money_detail` VALUES (183, 'RE14KVWp2X201810103069', 1, 11, '', 10.00, 1, 1, 1, 1539149704, NULL);
INSERT INTO `cl_money_detail` VALUES (184, 'RE14KVWp2X201810101529', 2, 11, '', -20.00, 1, 1, 1, 1539149728, NULL);
INSERT INTO `cl_money_detail` VALUES (185, 'RE14KVWp2X201810106004', 1, 11, '', 10.00, 1, 1, 1, 1539149731, NULL);
INSERT INTO `cl_money_detail` VALUES (186, 'RE14KVWp2X201810103411', 2, 11, '', -20.00, 1, 1, 1, 1539149860, NULL);
INSERT INTO `cl_money_detail` VALUES (187, 'RE14KVWp2X201810103448', 1, 11, '', 10.00, 1, 1, 1, 1539149861, NULL);
INSERT INTO `cl_money_detail` VALUES (188, 'RE14KVWp2X201810103685', 2, 11, '', -30.00, 1, 1, 1, 1539149966, NULL);
INSERT INTO `cl_money_detail` VALUES (189, 'RE14KVWp2X201810105383', 1, 11, '', 20.00, 1, 1, 1, 1539149968, NULL);
INSERT INTO `cl_money_detail` VALUES (190, 'REvqp4ZyMo201810108223', 2, 10, '', -123.00, 1, 1, 1, 1539150022, NULL);
INSERT INTO `cl_money_detail` VALUES (191, 'REvqp4ZyMo201810103093', 2, 10, '', -25.00, 1, 1, 1, 1539150051, NULL);
INSERT INTO `cl_money_detail` VALUES (192, 'REvqp4ZyMo201810105675', 1, 10, '', 4.17, 1, 1, 1, 1539150053, NULL);
INSERT INTO `cl_money_detail` VALUES (193, 'RE14KVWp2X201810109502', 1, 11, '', 4.17, 1, 1, 1, 1539150093, NULL);
INSERT INTO `cl_money_detail` VALUES (194, 'RE14KVWp2X201810106531', 2, 11, '', -19.99, 8, 1, 1, 1539150102, NULL);
INSERT INTO `cl_money_detail` VALUES (195, 'RE14KVWp2X201810108702', 2, 11, '', -19.99, 8, 1, 1, 1539150109, NULL);
INSERT INTO `cl_money_detail` VALUES (196, 'RE14KVWp2X201810107757', 2, 11, '', -19.99, 8, 1, 1, 1539150162, NULL);
INSERT INTO `cl_money_detail` VALUES (197, 'REvqp4ZyMo201810108260', 2, 10, '', -3.00, 1, 1, 1, 1539150582, NULL);
INSERT INTO `cl_money_detail` VALUES (198, 'REPRBaZyZ2201810106732', 2, 2, '', -19.99, 8, 1, 1, 1539150595, NULL);
INSERT INTO `cl_money_detail` VALUES (199, 'REPRBaZyZ2201810108686', 2, 2, '', -49.99, 8, 1, 1, 1539150601, NULL);
INSERT INTO `cl_money_detail` VALUES (200, 'REvqp4ZyMo201810102125', 2, 10, '', -23.00, 1, 1, 1, 1539150804, NULL);
INSERT INTO `cl_money_detail` VALUES (201, 'REvqp4ZyMo201810109238', 2, 10, '', -32.00, 1, 1, 1, 1539150827, NULL);
INSERT INTO `cl_money_detail` VALUES (202, 'REvqp4ZyMo201810107885', 2, 10, '', -35.00, 1, 1, 1, 1539150929, NULL);
INSERT INTO `cl_money_detail` VALUES (203, 'REvqp4ZyMo201810102184', 2, 10, '', -35.00, 1, 1, 1, 1539151083, NULL);
INSERT INTO `cl_money_detail` VALUES (204, 'REPRBaZyZ2201810107468', 2, 2, '', -999.99, 8, 1, 1, 1539151223, NULL);
INSERT INTO `cl_money_detail` VALUES (205, 'REvqp4ZyMo201810102552', 2, 10, '', -234.00, 1, 1, 1, 1539151379, NULL);
INSERT INTO `cl_money_detail` VALUES (206, 'REvqp4ZyMo201810106439', 2, 10, '', -235.00, 1, 1, 1, 1539151584, NULL);
INSERT INTO `cl_money_detail` VALUES (207, 'REvqp4ZyMo201810105364', 2, 10, '', -254.00, 1, 1, 1, 1539151737, NULL);
INSERT INTO `cl_money_detail` VALUES (208, 'REvqp4ZyMo201810106923', 2, 10, '', -254.00, 1, 1, 1, 1539151765, NULL);
INSERT INTO `cl_money_detail` VALUES (209, 'RE14KVWp2X201810102735', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539152389, NULL);
INSERT INTO `cl_money_detail` VALUES (210, 'RE14KVWp2X201810105646', 1, 11, '', 49.99, 3, 1, 1, 1539152806, NULL);
INSERT INTO `cl_money_detail` VALUES (211, 'RE1VKwmBQ3201810109349', 2, 14, '直播间付费', -20.00, 5, 1, 1, 1539154498, NULL);
INSERT INTO `cl_money_detail` VALUES (212, 'RE14KVWp2X201810109943', 2, 11, '直播间升级付费', -10000.00, 7, 1, 1, 1539155106, NULL);
INSERT INTO `cl_money_detail` VALUES (213, 'RE1VKwmBQ3201810106712', 2, 14, '活动付费', -20.00, 7, 1, 1, 1539155126, NULL);
INSERT INTO `cl_money_detail` VALUES (214, 'REbOygWyo9201810102207', 2, 9, '', -1.00, 1, 1, 1, 1539156086, NULL);
INSERT INTO `cl_money_detail` VALUES (215, 'REbOygWyo9201810105904', 2, 9, '', -1.00, 1, 1, 1, 1539156318, NULL);
INSERT INTO `cl_money_detail` VALUES (216, 'REvqp4ZyMo201810103118', 2, 10, '', -235.00, 1, 1, 1, 1539156484, NULL);
INSERT INTO `cl_money_detail` VALUES (217, 'REvqp4ZyMo201810104315', 1, 10, '', 78.34, 1, 1, 1, 1539156487, NULL);
INSERT INTO `cl_money_detail` VALUES (218, 'REbOygWyo9201810102925', 2, 9, '', -1.00, 1, 1, 1, 1539156684, NULL);
INSERT INTO `cl_money_detail` VALUES (219, 'REvqp4ZyMo201810107865', 2, 10, '', -235.00, 1, 1, 1, 1539156716, NULL);
INSERT INTO `cl_money_detail` VALUES (220, 'REbOygWyo9201810104789', 2, 9, '', -1.00, 1, 1, 1, 1539156777, NULL);
INSERT INTO `cl_money_detail` VALUES (221, 'REbOygWyo9201810103570', 2, 9, '', -1.00, 1, 1, 1, 1539156939, NULL);
INSERT INTO `cl_money_detail` VALUES (222, 'REbOygWyo9201810102255', 2, 9, '', -1.00, 1, 1, 1, 1539157084, NULL);
INSERT INTO `cl_money_detail` VALUES (223, 'REbOygWyo9201810106248', 2, 9, '', -1.00, 1, 1, 1, 1539157134, NULL);
INSERT INTO `cl_money_detail` VALUES (224, 'REbOygWyo9201810103746', 2, 9, '', -1.00, 1, 1, 1, 1539157183, NULL);
INSERT INTO `cl_money_detail` VALUES (225, 'REvqp4ZyMo201810108549', 2, 10, '', -235.00, 1, 1, 1, 1539158305, NULL);
INSERT INTO `cl_money_detail` VALUES (226, 'REvqp4ZyMo201810108707', 2, 10, '', -234.00, 1, 1, 1, 1539158380, NULL);
INSERT INTO `cl_money_detail` VALUES (227, 'REvqp4ZyMo201810103935', 2, 10, '', -234.00, 1, 1, 1, 1539159225, NULL);
INSERT INTO `cl_money_detail` VALUES (228, 'REX7yoXK1A201810103346', 2, 1, '直播间付费', -6.00, 5, 1, 1, 1539160647, NULL);
INSERT INTO `cl_money_detail` VALUES (229, 'REPRBaZyZ2201810105422', 2, 2, '', -19.99, 8, 1, 1, 1539160708, NULL);
INSERT INTO `cl_money_detail` VALUES (230, 'REPRBaZyZ2201810102211', 2, 2, '', -49.99, 8, 1, 1, 1539160712, NULL);
INSERT INTO `cl_money_detail` VALUES (231, 'REvqp4ZyMo201810109536', 2, 10, '', -123.00, 1, 1, 1, 1539161817, NULL);
INSERT INTO `cl_money_detail` VALUES (232, 'REvqp4ZyMo201810101471', 2, 10, '', -235.00, 1, 1, 1, 1539163580, NULL);
INSERT INTO `cl_money_detail` VALUES (233, 'REvqp4ZyMo201810102504', 2, 10, '', -235.00, 1, 1, 1, 1539163656, NULL);
INSERT INTO `cl_money_detail` VALUES (234, 'REvqp4ZyMo201810102168', 2, 10, '', -1335.00, 1, 1, 1, 1539163851, NULL);
INSERT INTO `cl_money_detail` VALUES (235, 'REvqp4ZyMo201810107412', 2, 10, '', -23.00, 1, 1, 1, 1539163934, NULL);
INSERT INTO `cl_money_detail` VALUES (236, 'REX7yoXK1A201810104788', 2, 1, '直播间升级付费', -1001.00, 7, 1, 1, 1539164020, NULL);
INSERT INTO `cl_money_detail` VALUES (237, 'REYdKZPBJo201810109119', 2, 27, '升级空间扣除60000', -60000.00, 7, 1, 1, 1539164234, NULL);
INSERT INTO `cl_money_detail` VALUES (238, 'RE4ZBLXBMN201810104113', 2, 7, '', -55.00, 1, 1, 1, 1539164238, NULL);
INSERT INTO `cl_money_detail` VALUES (239, 'REvqp4ZyMo201810105456', 1, 10, '', 11.50, 1, 1, 1, 1539164495, NULL);
INSERT INTO `cl_money_detail` VALUES (240, 'RE4ZBLXBMN201810103120', 2, 7, '', -666.00, 1, 1, 1, 1539164544, NULL);
INSERT INTO `cl_money_detail` VALUES (241, 'RE14KVWp2X201810108897', 2, 11, '', -20.00, 1, 1, 1, 1539165171, NULL);
INSERT INTO `cl_money_detail` VALUES (242, 'RE14KVWp2X201810103626', 1, 11, '', 10.00, 1, 1, 1, 1539165173, NULL);
INSERT INTO `cl_money_detail` VALUES (243, 'RE14KVWp2X201810101991', 2, 11, '', -30.00, 1, 1, 1, 1539165195, NULL);
INSERT INTO `cl_money_detail` VALUES (244, 'RE14KVWp2X201810105883', 1, 11, '', 5.00, 1, 1, 1, 1539165197, NULL);
INSERT INTO `cl_money_detail` VALUES (245, 'REvqp4ZyMo201810107748', 2, 10, '', -235.00, 1, 1, 1, 1539166168, NULL);
INSERT INTO `cl_money_detail` VALUES (246, 'RE4ZBLXBMN201810101306', 2, 7, '', -666.00, 1, 1, 1, 1539166372, NULL);
INSERT INTO `cl_money_detail` VALUES (247, 'RE4ZBLXBMN201810109848', 2, 7, '', -8866.00, 1, 1, 1, 1539166434, NULL);
INSERT INTO `cl_money_detail` VALUES (248, 'RELNyznyXM201810109359', 2, 18, '', -234.00, 1, 1, 1, 1539166843, NULL);
INSERT INTO `cl_money_detail` VALUES (249, 'RE4ZBLXBMN201810106338', 2, 7, '', -556.00, 1, 1, 1, 1539166978, NULL);
INSERT INTO `cl_money_detail` VALUES (250, 'RELbyJGK5d201810107713', 2, 6, '直播间升级付费', -1000.00, 7, 1, 1, 1539167139, NULL);
INSERT INTO `cl_money_detail` VALUES (251, 'RE4ZBLXBMN201810109613', 2, 7, '', -6666.00, 1, 1, 1, 1539170795, NULL);
INSERT INTO `cl_money_detail` VALUES (252, 'RE4ZBLXBMN201810107917', 2, 7, '', -666.00, 1, 1, 1, 1539171781, NULL);
INSERT INTO `cl_money_detail` VALUES (253, 'REvqp4ZyMo201810119529', 1, 10, '', 78.34, 1, 1, 1, 1539221802, NULL);
INSERT INTO `cl_money_detail` VALUES (254, 'REvqp4ZyMo201810117978', 1, 10, '', 58.05, 1, 1, 1, 1539221809, NULL);
INSERT INTO `cl_money_detail` VALUES (255, 'REvqp4ZyMo201810118516', 1, 10, '', 10.21, 1, 1, 1, 1539221813, NULL);
INSERT INTO `cl_money_detail` VALUES (256, 'RE4ZBLXBMN201810115786', 2, 7, '', -5666.00, 1, 1, 1, 1539221925, NULL);
INSERT INTO `cl_money_detail` VALUES (257, 'RE4ZBLXBMN201810119251', 2, 7, '', -3666.00, 1, 1, 1, 1539222003, NULL);
INSERT INTO `cl_money_detail` VALUES (258, 'RE4ZBLXBMN201810113164', 2, 7, '', -666.00, 1, 1, 1, 1539222217, NULL);
INSERT INTO `cl_money_detail` VALUES (259, 'RE4ZBLXBMN201810116989', 2, 7, '', -555.00, 1, 1, 1, 1539222651, NULL);
INSERT INTO `cl_money_detail` VALUES (260, 'RE14KVWp2X201810112448', 2, 11, '直播间付费', -1.00, 5, 1, 1, 1539222822, NULL);
INSERT INTO `cl_money_detail` VALUES (261, 'REPRBaZyZ2201810115256', 2, 2, '直播间付费', -20.00, 5, 1, 1, 1539225591, NULL);
INSERT INTO `cl_money_detail` VALUES (262, 'RELNyznyXM201810116194', 1, 18, '', 78.00, 1, 1, 1, 1539226273, NULL);
INSERT INTO `cl_money_detail` VALUES (263, 'REPRBaZyZ2201810113176', 2, 2, '', -999.99, 8, 1, 1, 1539226800, NULL);
INSERT INTO `cl_money_detail` VALUES (264, 'REPRBaZyZ2201810115934', 1, 2, '', 0.00, 8, 1, 1, 1539226811, NULL);
INSERT INTO `cl_money_detail` VALUES (265, 'REPRBaZyZ2201810113638', 2, 2, '', -3.00, 1, 1, 1, 1539226840, NULL);
INSERT INTO `cl_money_detail` VALUES (266, 'REPRBaZyZ2201810113767', 1, 2, '', 0.05, 1, 1, 1, 1539226853, NULL);
INSERT INTO `cl_money_detail` VALUES (267, 'REPRBaZyZ2201810115162', 2, 2, '', -99.00, 1, 1, 1, 1539226895, NULL);
INSERT INTO `cl_money_detail` VALUES (268, 'REPRBaZyZ2201810117906', 1, 2, '', 16.50, 1, 1, 1, 1539226909, NULL);
INSERT INTO `cl_money_detail` VALUES (269, 'RE14KVWp2X201810114884', 2, 11, '直播间付费', -1.00, 5, 1, 1, 1539226970, NULL);
INSERT INTO `cl_money_detail` VALUES (270, 'REPRBaZyZ2201810111853', 2, 2, '', -100.00, 1, 1, 1, 1539226984, NULL);
INSERT INTO `cl_money_detail` VALUES (271, 'RE14KVWp2X201810114841', 1, 11, '', 16.67, 1, 1, 1, 1539226987, NULL);
INSERT INTO `cl_money_detail` VALUES (272, 'REPRBaZyZ2201810119580', 1, 2, '', 66.66, 1, 1, 1, 1539226988, NULL);
INSERT INTO `cl_money_detail` VALUES (273, 'REPRBaZyZ2201810111148', 2, 2, '', -33.00, 1, 1, 1, 1539227049, NULL);
INSERT INTO `cl_money_detail` VALUES (274, 'REPRBaZyZ2201810111581', 1, 2, '', 16.50, 1, 1, 1, 1539227052, NULL);
INSERT INTO `cl_money_detail` VALUES (275, 'RE14KVWp2X201810117070', 1, 11, '', 16.50, 1, 1, 1, 1539227053, NULL);
INSERT INTO `cl_money_detail` VALUES (276, 'REPRBaZyZ2201810117048', 2, 2, '', -55.00, 1, 1, 1, 1539227073, NULL);
INSERT INTO `cl_money_detail` VALUES (277, 'REPRBaZyZ2201810111269', 1, 2, '', 54.99, 1, 1, 1, 1539227077, NULL);
INSERT INTO `cl_money_detail` VALUES (278, 'RE14KVWp2X201810117681', 1, 11, '', 0.01, 1, 1, 1, 1539227077, NULL);
INSERT INTO `cl_money_detail` VALUES (279, 'RE4ZBLXBMN201810111287', 2, 7, '', -566.00, 1, 1, 1, 1539228059, NULL);
INSERT INTO `cl_money_detail` VALUES (280, 'RExkyO7yJ6201810115301', 2, 17, '直播间付费', -1.00, 5, 1, 1, 1539228130, NULL);
INSERT INTO `cl_money_detail` VALUES (281, 'RExkyO7yJ6201810116699', 2, 17, '直播间付费', -1.00, 5, 1, 1, 1539228196, NULL);
INSERT INTO `cl_money_detail` VALUES (282, 'RELNyznyXM201810114973', 1, 18, '红包未领完返还', 123.00, 7, 1, 1, 1539229081, NULL);
INSERT INTO `cl_money_detail` VALUES (283, 'RELNyznyXM201810114554', 1, 18, '红包未领完返还', 12.00, 7, 1, 1, 1539229321, NULL);
INSERT INTO `cl_money_detail` VALUES (284, 'RELNyznyXM201810118808', 1, 18, '红包未领完返还', 123.00, 7, 1, 1, 1539229321, NULL);
INSERT INTO `cl_money_detail` VALUES (285, 'REvqp4ZyMo201810116684', 1, 10, '红包未领完返还', 23.00, 7, 1, 1, 1539229861, NULL);
INSERT INTO `cl_money_detail` VALUES (286, 'REvqp4ZyMo201810119250', 1, 10, '红包未领完返还', 23.00, 7, 1, 1, 1539230161, NULL);
INSERT INTO `cl_money_detail` VALUES (287, 'RE4ZBLXBMN201810117202', 1, 7, '', 0.06, 1, 1, 1, 1539230734, NULL);
INSERT INTO `cl_money_detail` VALUES (288, 'RE4ZBLXBMN201810113073', 2, 7, '', -899.00, 1, 1, 1, 1539231502, NULL);
INSERT INTO `cl_money_detail` VALUES (289, 'RE4ZBLXBMN201810118378', 1, 7, '', 0.01, 1, 1, 1, 1539231526, NULL);
INSERT INTO `cl_money_detail` VALUES (290, 'RELNyznyXM201810113933', 2, 18, '', -234.00, 1, 1, 1, 1539235813, NULL);
INSERT INTO `cl_money_detail` VALUES (291, 'RE4ZBLXBMN201810111924', 2, 7, '', -2555.00, 1, 1, 1, 1539235839, NULL);
INSERT INTO `cl_money_detail` VALUES (292, 'RE4ZBLXBMN201810119399', 2, 7, '', -5565.00, 1, 1, 1, 1539235893, NULL);
INSERT INTO `cl_money_detail` VALUES (293, 'RE4ZBLXBMN201810118983', 2, 7, '', -55.00, 1, 1, 1, 1539235954, NULL);
INSERT INTO `cl_money_detail` VALUES (294, 'RE4ZBLXBMN201810117042', 1, 7, '', 2.61, 1, 1, 1, 1539235971, NULL);
INSERT INTO `cl_money_detail` VALUES (295, 'RE4ZBLXBMN201810112036', 1, 7, '', 69.33, 1, 1, 1, 1539235975, NULL);
INSERT INTO `cl_money_detail` VALUES (296, 'RE4ZBLXBMN201810119824', 1, 7, '', 3.30, 1, 1, 1, 1539235979, NULL);
INSERT INTO `cl_money_detail` VALUES (297, 'RE4ZBLXBMN201810114471', 2, 7, '', -222.00, 1, 1, 1, 1539235995, NULL);
INSERT INTO `cl_money_detail` VALUES (298, 'RELNyznyXM201810115010', 2, 18, '', -234.00, 1, 1, 1, 1539236060, NULL);
INSERT INTO `cl_money_detail` VALUES (299, 'RE4ZBLXBMN201810112788', 1, 7, '', 117.00, 1, 1, 1, 1539236155, NULL);
INSERT INTO `cl_money_detail` VALUES (300, 'RE14KVWp2X201810116493', 1, 11, '红包未领完返还', 10.00, 7, 1, 1, 1539236161, NULL);
INSERT INTO `cl_money_detail` VALUES (301, 'RE14KVWp2X201810114764', 1, 11, '红包未领完返还', 10.00, 7, 1, 1, 1539236161, NULL);
INSERT INTO `cl_money_detail` VALUES (302, 'RE4ZBLXBMN201810112473', 2, 7, '', -556.00, 1, 1, 1, 1539236172, NULL);
INSERT INTO `cl_money_detail` VALUES (303, 'RE4ZBLXBMN201810117366', 1, 7, '', 78.00, 1, 1, 1, 1539236180, NULL);
INSERT INTO `cl_money_detail` VALUES (304, 'RE14KVWp2X201810117592', 1, 11, '红包未领完返还', 10.00, 7, 1, 1, 1539236281, NULL);
INSERT INTO `cl_money_detail` VALUES (305, 'RE14KVWp2X201810119075', 1, 11, '红包未领完返还', 10.00, 7, 1, 1, 1539236402, NULL);
INSERT INTO `cl_money_detail` VALUES (306, 'RE4ZBLXBMN201810114445', 2, 7, '', -663.00, 1, 1, 1, 1539236413, NULL);
INSERT INTO `cl_money_detail` VALUES (307, 'REPRBaZyZ2201810117384', 2, 2, '拍卖房间allmylive加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539236454, NULL);
INSERT INTO `cl_money_detail` VALUES (308, 'RE14KVWp2X201810118729', 2, 11, '拍卖房间allmylive加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539236454, NULL);
INSERT INTO `cl_money_detail` VALUES (309, 'REvqp4ZyMo201810112020', 1, 10, '红包未领完返还', 123.00, 7, 1, 1, 1539236461, NULL);
INSERT INTO `cl_money_detail` VALUES (310, 'REvqp4ZyMo201810112328', 1, 10, '红包未领完返还', 16.66, 7, 1, 1, 1539236461, NULL);
INSERT INTO `cl_money_detail` VALUES (311, 'RE4ZBLXBMN201810118195', 2, 7, '', -566.00, 1, 1, 1, 1539236463, NULL);
INSERT INTO `cl_money_detail` VALUES (312, 'RE4ZBLXBMN201810117583', 2, 7, '', -666.00, 1, 1, 1, 1539236480, NULL);
INSERT INTO `cl_money_detail` VALUES (313, 'RELNyznyXM201810118958', 2, 18, '', -123.00, 1, 1, 1, 1539236637, NULL);
INSERT INTO `cl_money_detail` VALUES (314, 'RE201810116037', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539236641, NULL);
INSERT INTO `cl_money_detail` VALUES (315, 'RE201810112418', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539236641, NULL);
INSERT INTO `cl_money_detail` VALUES (316, 'RE4ZBLXBMN201810119519', 2, 7, '', -333.00, 1, 1, 1, 1539236796, NULL);
INSERT INTO `cl_money_detail` VALUES (317, 'RE4ZBLXBMN201810118423', 1, 7, '', 0.01, 1, 1, 1, 1539236825, NULL);
INSERT INTO `cl_money_detail` VALUES (318, 'RE4ZBLXBMN201810114029', 1, 7, '', 61.50, 1, 1, 1, 1539236829, NULL);
INSERT INTO `cl_money_detail` VALUES (319, 'RE4ZBLXBMN201810113370', 2, 7, '', -266.00, 1, 1, 1, 1539236844, NULL);
INSERT INTO `cl_money_detail` VALUES (320, 'REvqp4ZyMo201810113037', 1, 10, '红包未领完返还', 3.00, 7, 1, 1, 1539237001, NULL);
INSERT INTO `cl_money_detail` VALUES (321, 'REvqp4ZyMo201810117814', 1, 10, '红包未领完返还', 23.00, 7, 1, 1, 1539237241, NULL);
INSERT INTO `cl_money_detail` VALUES (322, 'REvqp4ZyMo201810115636', 1, 10, '红包未领完返还', 32.00, 7, 1, 1, 1539237241, NULL);
INSERT INTO `cl_money_detail` VALUES (323, 'REvqp4ZyMo201810117700', 1, 10, '红包未领完返还', 35.00, 7, 1, 1, 1539237361, NULL);
INSERT INTO `cl_money_detail` VALUES (324, 'REPRBaZyZ2201810117092', 2, 2, '活动付费', -20.00, 7, 1, 1, 1539237389, NULL);
INSERT INTO `cl_money_detail` VALUES (325, 'RE14KVWp2X201810113211', 1, 11, '', 61.50, 1, 1, 1, 1539237519, NULL);
INSERT INTO `cl_money_detail` VALUES (326, 'REvqp4ZyMo201810113429', 1, 10, '红包未领完返还', 35.00, 7, 1, 1, 1539237541, NULL);
INSERT INTO `cl_money_detail` VALUES (327, 'RE14KVWp2X201810113309', 2, 11, '', -19.99, 8, 1, 1, 1539237725, NULL);
INSERT INTO `cl_money_detail` VALUES (328, 'RE14KVWp2X201810116113', 2, 11, '', -19.99, 8, 1, 1, 1539237729, NULL);
INSERT INTO `cl_money_detail` VALUES (329, 'RE14KVWp2X201810115330', 2, 11, '', -19.99, 8, 1, 1, 1539237752, NULL);
INSERT INTO `cl_money_detail` VALUES (330, 'RE14KVWp2X201810112703', 2, 11, '', -19.99, 8, 1, 1, 1539237755, NULL);
INSERT INTO `cl_money_detail` VALUES (331, 'RE14KVWp2X201810114569', 1, 11, '', 0.00, 8, 1, 1, 1539237766, NULL);
INSERT INTO `cl_money_detail` VALUES (332, 'RE14KVWp2X201810112581', 1, 11, '', 0.00, 8, 1, 1, 1539237770, NULL);
INSERT INTO `cl_money_detail` VALUES (333, 'RE14KVWp2X201810111074', 1, 11, '', 0.00, 8, 1, 1, 1539237771, NULL);
INSERT INTO `cl_money_detail` VALUES (334, 'RE14KVWp2X201810115307', 2, 11, '', -19.99, 8, 1, 1, 1539237773, NULL);
INSERT INTO `cl_money_detail` VALUES (335, 'REPRBaZyZ2201810111756', 2, 2, '', -999.99, 8, 1, 1, 1539237776, NULL);
INSERT INTO `cl_money_detail` VALUES (336, 'REPRBaZyZ2201810117571', 1, 2, '', 0.00, 8, 1, 1, 1539237778, NULL);
INSERT INTO `cl_money_detail` VALUES (337, 'REPRBaZyZ2201810112818', 1, 2, '', 0.00, 8, 1, 1, 1539237780, NULL);
INSERT INTO `cl_money_detail` VALUES (338, 'REvqp4ZyMo201810114243', 1, 10, '红包未领完返还', 234.00, 7, 1, 1, 1539237781, NULL);
INSERT INTO `cl_money_detail` VALUES (339, 'REPRBaZyZ2201810116719', 1, 2, '', 0.00, 8, 1, 1, 1539237785, NULL);
INSERT INTO `cl_money_detail` VALUES (340, 'RE14KVWp2X201810117082', 2, 11, '', -19.99, 8, 1, 1, 1539237786, NULL);
INSERT INTO `cl_money_detail` VALUES (341, 'REPRBaZyZ2201810111467', 1, 2, '', 0.00, 8, 1, 1, 1539237790, NULL);
INSERT INTO `cl_money_detail` VALUES (342, 'RE14KVWp2X201810113174', 2, 11, '', -19.99, 8, 1, 1, 1539237810, NULL);
INSERT INTO `cl_money_detail` VALUES (343, 'RE14KVWp2X201810111408', 2, 11, '', -19.99, 8, 1, 1, 1539237816, NULL);
INSERT INTO `cl_money_detail` VALUES (344, 'REPRBaZyZ2201810113638', 2, 2, '拍卖房间啊啊啊加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539238002, NULL);
INSERT INTO `cl_money_detail` VALUES (345, 'REvqp4ZyMo201810112474', 1, 10, '红包未领完返还', 235.00, 7, 1, 1, 1539238022, NULL);
INSERT INTO `cl_money_detail` VALUES (346, 'REvqp4ZyMo201810116228', 1, 10, '红包未领完返还', 254.00, 7, 1, 1, 1539238141, NULL);
INSERT INTO `cl_money_detail` VALUES (347, 'REvqp4ZyMo201810119155', 1, 10, '红包未领完返还', 254.00, 7, 1, 1, 1539238201, NULL);
INSERT INTO `cl_money_detail` VALUES (348, 'RE14KVWp2X201810115648', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539238604, NULL);
INSERT INTO `cl_money_detail` VALUES (349, 'REPRBaZyZ2201810115656', 2, 2, '直播间付费', -100.00, 5, 1, 1, 1539238780, NULL);
INSERT INTO `cl_money_detail` VALUES (350, 'RE14KVWp2X201810118879', 2, 11, '', -19.99, 8, 1, 1, 1539239456, NULL);
INSERT INTO `cl_money_detail` VALUES (351, 'RE201810117893', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539239461, NULL);
INSERT INTO `cl_money_detail` VALUES (352, 'RE14KVWp2X201810112484', 1, 11, '', 0.00, 8, 1, 1, 1539239462, NULL);
INSERT INTO `cl_money_detail` VALUES (353, 'RE14KVWp2X201810113204', 1, 11, '', 0.00, 8, 1, 1, 1539239465, NULL);
INSERT INTO `cl_money_detail` VALUES (354, 'RE14KVWp2X201810113078', 1, 11, '', 0.00, 8, 1, 1, 1539239471, NULL);
INSERT INTO `cl_money_detail` VALUES (355, 'RE14KVWp2X201810113942', 1, 11, '', 0.00, 8, 1, 1, 1539239474, NULL);
INSERT INTO `cl_money_detail` VALUES (356, 'RE14KVWp2X201810117485', 1, 11, '', 0.00, 8, 1, 1, 1539239632, NULL);
INSERT INTO `cl_money_detail` VALUES (357, 'RE14KVWp2X201810119629', 1, 11, '', 0.00, 8, 1, 1, 1539239635, NULL);
INSERT INTO `cl_money_detail` VALUES (358, 'RE14KVWp2X201810115748', 1, 11, '', 0.00, 8, 1, 1, 1539239656, NULL);
INSERT INTO `cl_money_detail` VALUES (359, 'RE4ZBLXBMN201810112781', 2, 7, '直播间升级付费', -1000.00, 7, 1, 1, 1539240237, NULL);
INSERT INTO `cl_money_detail` VALUES (360, 'RE14KVWp2X201810111897', 1, 11, '拍卖成功,获得拍卖价20.00', 20.00, 4, 1, 1, 1539240482, NULL);
INSERT INTO `cl_money_detail` VALUES (361, 'RE14KVWp2X201810115311', 2, 11, '', -20.00, 1, 1, 1, 1539241575, NULL);
INSERT INTO `cl_money_detail` VALUES (362, 'RE14KVWp2X201810115826', 1, 11, '', 6.66, 1, 1, 1, 1539241579, NULL);
INSERT INTO `cl_money_detail` VALUES (363, 'RE14KVWp2X201810113357', 2, 11, '', -20.00, 1, 1, 1, 1539241673, NULL);
INSERT INTO `cl_money_detail` VALUES (364, 'RE14KVWp2X201810119286', 1, 11, '', 20.00, 1, 1, 1, 1539241676, NULL);
INSERT INTO `cl_money_detail` VALUES (365, 'RE14KVWp2X201810113617', 2, 11, '', -20.00, 1, 4, 1, 1539241697, NULL);
INSERT INTO `cl_money_detail` VALUES (366, 'RE14KVWp2X201810117831', 1, 11, '', 1.00, 1, 4, 1, 1539241698, NULL);
INSERT INTO `cl_money_detail` VALUES (367, 'RE14KVWp2X201810111960', 2, 11, '', -20.00, 1, 4, 1, 1539241725, NULL);
INSERT INTO `cl_money_detail` VALUES (368, 'RE14KVWp2X201810112162', 1, 11, '', 10.00, 1, 4, 1, 1539241727, NULL);
INSERT INTO `cl_money_detail` VALUES (369, 'RE14KVWp2X201810114245', 2, 11, '', -20.00, 1, 4, 1, 1539241757, NULL);
INSERT INTO `cl_money_detail` VALUES (370, 'RE14KVWp2X201810115028', 1, 11, '', 1.00, 1, 4, 1, 1539241759, NULL);
INSERT INTO `cl_money_detail` VALUES (371, 'RE14KVWp2X201810116559', 2, 11, '', -20.00, 1, 4, 1, 1539241777, NULL);
INSERT INTO `cl_money_detail` VALUES (372, 'RE14KVWp2X201810115595', 1, 11, '', 10.00, 1, 4, 1, 1539241780, NULL);
INSERT INTO `cl_money_detail` VALUES (373, 'RE14KVWp2X201810115819', 2, 11, '', -20.00, 1, 4, 1, 1539241863, NULL);
INSERT INTO `cl_money_detail` VALUES (374, 'RE14KVWp2X201810119530', 1, 11, '', 19.00, 1, 4, 1, 1539241865, NULL);
INSERT INTO `cl_money_detail` VALUES (375, 'RE14KVWp2X201810114105', 2, 11, '', -20.00, 1, 4, 1, 1539241877, NULL);
INSERT INTO `cl_money_detail` VALUES (376, 'RE14KVWp2X201810119493', 1, 11, '', 19.00, 1, 4, 1, 1539241880, NULL);
INSERT INTO `cl_money_detail` VALUES (377, 'RE14KVWp2X201810113798', 2, 11, '直播间付费', -300.00, 5, 1, 1, 1539241948, NULL);
INSERT INTO `cl_money_detail` VALUES (378, 'REbOygWyo9201810117525', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539242521, NULL);
INSERT INTO `cl_money_detail` VALUES (379, 'REbOygWyo9201810115927', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539242761, NULL);
INSERT INTO `cl_money_detail` VALUES (380, 'REvqp4ZyMo201810111499', 1, 10, '红包未领完返还', 156.66, 7, 1, 1, 1539242941, NULL);
INSERT INTO `cl_money_detail` VALUES (381, 'REbOygWyo9201810111154', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243121, NULL);
INSERT INTO `cl_money_detail` VALUES (382, 'REvqp4ZyMo201810115380', 1, 10, '红包未领完返还', 235.00, 7, 1, 1, 1539243121, NULL);
INSERT INTO `cl_money_detail` VALUES (383, 'REbOygWyo9201810113390', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243181, NULL);
INSERT INTO `cl_money_detail` VALUES (384, 'REbOygWyo9201810112437', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243361, NULL);
INSERT INTO `cl_money_detail` VALUES (385, 'REbOygWyo9201810111054', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243541, NULL);
INSERT INTO `cl_money_detail` VALUES (386, 'REbOygWyo9201810117649', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243541, NULL);
INSERT INTO `cl_money_detail` VALUES (387, 'REbOygWyo9201810119796', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539243601, NULL);
INSERT INTO `cl_money_detail` VALUES (388, 'RELbyJGK5d201810115335', 2, 6, '直播间付费', -300.00, 5, 1, 1, 1539243613, NULL);
INSERT INTO `cl_money_detail` VALUES (389, 'RELDBbxK5n201810117321', 2, 29, '直播间付费', -300.00, 5, 1, 1, 1539244360, NULL);
INSERT INTO `cl_money_detail` VALUES (390, 'RELDBbxK5n201810115871', 2, 29, '', -233.00, 1, 4, 1, 1539244373, NULL);
INSERT INTO `cl_money_detail` VALUES (391, 'RELDBbxK5n201810114472', 2, 29, '', -999.99, 8, 1, 1, 1539244386, NULL);
INSERT INTO `cl_money_detail` VALUES (392, 'RELDBbxK5n201810113113', 1, 29, '', 0.00, 8, 1, 1, 1539244396, NULL);
INSERT INTO `cl_money_detail` VALUES (393, 'RELDBbxK5n201810118611', 2, 29, '', -999.99, 8, 1, 1, 1539244407, NULL);
INSERT INTO `cl_money_detail` VALUES (394, 'REvqp4ZyMo201810119272', 1, 10, '红包未领完返还', 235.00, 7, 1, 1, 1539244742, NULL);
INSERT INTO `cl_money_detail` VALUES (395, 'REvqp4ZyMo201810112329', 1, 10, '红包未领完返还', 234.00, 7, 1, 1, 1539244801, NULL);
INSERT INTO `cl_money_detail` VALUES (396, 'REvqp4ZyMo201810114669', 1, 10, '红包未领完返还', 234.00, 7, 1, 1, 1539245641, NULL);
INSERT INTO `cl_money_detail` VALUES (397, 'RE14KVWp2X201810119975', 2, 11, '', -20.00, 1, 4, 1, 1539245733, NULL);
INSERT INTO `cl_money_detail` VALUES (398, 'RE14KVWp2X201810119777', 1, 11, '', 1.00, 1, 4, 1, 1539245736, NULL);
INSERT INTO `cl_money_detail` VALUES (399, 'RE14KVWp2X201810114710', 2, 11, '', -10.00, 1, 1, 1, 1539246634, NULL);
INSERT INTO `cl_money_detail` VALUES (400, 'RE3oynryJV201810117927', 1, 38, '', 10.00, 1, 1, 1, 1539246640, NULL);
INSERT INTO `cl_money_detail` VALUES (401, 'RE14KVWp2X201810115947', 2, 11, '', -19.99, 8, 1, 1, 1539246709, NULL);
INSERT INTO `cl_money_detail` VALUES (402, 'RE14KVWp2X201810119083', 2, 11, '', -99.99, 8, 1, 1, 1539246721, NULL);
INSERT INTO `cl_money_detail` VALUES (403, 'RE14KVWp2X201810112273', 2, 11, '', -19.99, 8, 1, 1, 1539246744, NULL);
INSERT INTO `cl_money_detail` VALUES (404, 'RE3oynryJV201810116917', 2, 38, '直播间升级付费', -1000.00, 7, 1, 1, 1539246941, NULL);
INSERT INTO `cl_money_detail` VALUES (405, 'RE14KVWp2X201810111027', 2, 11, '活动付费', -100.00, 7, 1, 1, 1539247225, NULL);
INSERT INTO `cl_money_detail` VALUES (406, 'RELbyJGK5d201810117246', 2, 6, '活动付费', -100.00, 7, 1, 1, 1539247233, NULL);
INSERT INTO `cl_money_detail` VALUES (407, 'REvqp4ZyMo201810111853', 1, 10, '红包未领完返还', 123.00, 7, 1, 1, 1539248221, NULL);
INSERT INTO `cl_money_detail` VALUES (408, 'RE14KVWp2X201810112886', 2, 11, '拍卖角色点点点加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1539248834, NULL);
INSERT INTO `cl_money_detail` VALUES (409, 'RE14KVWp2X201810119660', 2, 11, '拍卖角色滴滴加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1539248986, NULL);
INSERT INTO `cl_money_detail` VALUES (410, 'RE14KVWp2X201810115431', 2, 11, '', -20.00, 1, 1, 1, 1539249320, NULL);
INSERT INTO `cl_money_detail` VALUES (411, 'RE14KVWp2X201810112683', 2, 11, '', -20.00, 1, 1, 1, 1539249362, NULL);
INSERT INTO `cl_money_detail` VALUES (412, 'RE14KVWp2X201810112792', 1, 11, '', 13.34, 1, 1, 1, 1539249366, NULL);
INSERT INTO `cl_money_detail` VALUES (413, 'RE14KVWp2X201810118255', 2, 11, '', -20.00, 1, 1, 1, 1539249519, NULL);
INSERT INTO `cl_money_detail` VALUES (414, 'RE14KVWp2X201810115221', 1, 11, '', 20.00, 1, 1, 1, 1539249523, NULL);
INSERT INTO `cl_money_detail` VALUES (415, 'REvqp4ZyMo201810117417', 1, 10, '红包未领完返还', 235.00, 7, 1, 1, 1539250021, NULL);
INSERT INTO `cl_money_detail` VALUES (416, 'REvqp4ZyMo201810117625', 1, 10, '红包未领完返还', 224.79, 7, 1, 1, 1539250081, NULL);
INSERT INTO `cl_money_detail` VALUES (417, 'REvqp4ZyMo201810118797', 1, 10, '红包未领完返还', 1276.95, 7, 1, 1, 1539250261, NULL);
INSERT INTO `cl_money_detail` VALUES (418, 'REvqp4ZyMo201810114306', 1, 10, '红包未领完返还', 11.50, 7, 1, 1, 1539250381, NULL);
INSERT INTO `cl_money_detail` VALUES (419, 'RE4ZBLXBMN201810113030', 1, 7, '红包未领完返还', 55.00, 7, 1, 1, 1539250681, NULL);
INSERT INTO `cl_money_detail` VALUES (420, 'RE4ZBLXBMN201810112532', 1, 7, '红包未领完返还', 666.00, 7, 1, 1, 1539250981, NULL);
INSERT INTO `cl_money_detail` VALUES (421, 'RELNyznyXM201810115079', 2, 18, '直播间付费', -300.00, 5, 1, 1, 1539251108, NULL);
INSERT INTO `cl_money_detail` VALUES (422, 'RE3oynryJV201810111328', 2, 38, '', -199.99, 8, 1, 1, 1539251307, NULL);
INSERT INTO `cl_money_detail` VALUES (423, 'RE3oynryJV201810118499', 2, 38, '', -100.00, 1, 1, 1, 1539251333, NULL);
INSERT INTO `cl_money_detail` VALUES (424, 'RE3oynryJV201810118460', 1, 38, '', 1.00, 1, 1, 1, 1539251337, NULL);
INSERT INTO `cl_money_detail` VALUES (425, 'RE14KVWp2X201810112683', 1, 11, '', 1.00, 1, 1, 1, 1539251356, NULL);
INSERT INTO `cl_money_detail` VALUES (426, 'RE14KVWp2X201810115104', 1, 11, '红包未领完返还', 10.00, 7, 1, 1, 1539251582, NULL);
INSERT INTO `cl_money_detail` VALUES (427, 'RE14KVWp2X201810115324', 1, 11, '红包未领完返还', 25.00, 7, 1, 1, 1539251641, NULL);
INSERT INTO `cl_money_detail` VALUES (428, 'REvqp4ZyMo201810117662', 1, 10, '红包未领完返还', 156.66, 7, 1, 1, 1539252601, NULL);
INSERT INTO `cl_money_detail` VALUES (429, 'RE4ZBLXBMN201810119042', 1, 7, '红包未领完返还', 666.00, 7, 1, 1, 1539252781, NULL);
INSERT INTO `cl_money_detail` VALUES (430, 'RE4ZBLXBMN201810114588', 1, 7, '红包未领完返还', 8866.00, 7, 1, 1, 1539252841, NULL);
INSERT INTO `cl_money_detail` VALUES (431, 'RE201810118432', 1, NULL, '拍卖房间小兰竞拍价格被超越，返还冻结金额', 2000.00, 4, 1, 1, 1539253160, NULL);
INSERT INTO `cl_money_detail` VALUES (432, 'RE14KVWp2X201810114004', 2, 11, '拍卖房间小兰加价成功，冻结竞拍金额 ', -2001.00, 4, 1, 1, 1539253160, NULL);
INSERT INTO `cl_money_detail` VALUES (433, 'RELNyznyXM201810115716', 1, 18, '红包未领完返还', 156.00, 7, 1, 1, 1539253261, NULL);
INSERT INTO `cl_money_detail` VALUES (434, 'RE4ZBLXBMN201810119555', 1, 7, '红包未领完返还', 556.00, 7, 1, 1, 1539253381, NULL);
INSERT INTO `cl_money_detail` VALUES (435, 'RE4ZBLXBMN201810115654', 1, 7, '红包未领完返还', 6666.00, 7, 1, 1, 1539257222, NULL);
INSERT INTO `cl_money_detail` VALUES (436, 'RE4ZBLXBMN201810112460', 1, 7, '红包未领完返还', 666.00, 7, 1, 1, 1539258241, NULL);
INSERT INTO `cl_money_detail` VALUES (437, 'RE4ZBLXBMN201810121063', 2, 7, '', -889.00, 1, 1, 1, 1539308153, NULL);
INSERT INTO `cl_money_detail` VALUES (438, 'RE4ZBLXBMN201810125953', 1, 7, '红包未领完返还', 5666.00, 7, 1, 1, 1539308341, NULL);
INSERT INTO `cl_money_detail` VALUES (439, 'RE4ZBLXBMN201810122200', 1, 7, '红包未领完返还', 3666.00, 7, 1, 1, 1539308462, NULL);
INSERT INTO `cl_money_detail` VALUES (440, 'RE4ZBLXBMN201810125745', 1, 7, '红包未领完返还', 666.00, 7, 1, 1, 1539308641, NULL);
INSERT INTO `cl_money_detail` VALUES (441, 'RE4ZBLXBMN201810127027', 1, 7, '红包未领完返还', 555.00, 7, 1, 1, 1539309061, NULL);
INSERT INTO `cl_money_detail` VALUES (442, 'REvqp4ZyMo201810121578', 2, 10, '', -999.99, 8, 1, 1, 1539311573, NULL);
INSERT INTO `cl_money_detail` VALUES (443, 'REvqp4ZyMo201810123150', 2, 10, '', -199.99, 8, 1, 1, 1539311583, NULL);
INSERT INTO `cl_money_detail` VALUES (444, 'REvqp4ZyMo201810125044', 2, 10, '', -123.00, 1, 1, 1, 1539311628, NULL);
INSERT INTO `cl_money_detail` VALUES (445, 'REvqp4ZyMo201810129705', 2, 10, '', -234.00, 1, 1, 1, 1539311687, NULL);
INSERT INTO `cl_money_detail` VALUES (446, 'REvqp4ZyMo201810128632', 2, 10, '', -123.00, 1, 1, 1, 1539311712, NULL);
INSERT INTO `cl_money_detail` VALUES (447, 'RELNyznyXM201810127074', 2, 18, '', -23.00, 1, 1, 1, 1539312089, NULL);
INSERT INTO `cl_money_detail` VALUES (448, 'RELNyznyXM201810129777', 2, 18, '', -23.00, 1, 1, 1, 1539312106, NULL);
INSERT INTO `cl_money_detail` VALUES (449, 'REvqp4ZyMo201810122111', 1, 10, '', 7.66, 1, 1, 1, 1539312123, NULL);
INSERT INTO `cl_money_detail` VALUES (450, 'RELNyznyXM201810124497', 1, 18, '', 7.67, 1, 1, 1, 1539312134, NULL);
INSERT INTO `cl_money_detail` VALUES (451, 'REPRBaZyZ2201810121796', 1, 2, '红包未领完返还', 2.95, 7, 1, 1, 1539313261, NULL);
INSERT INTO `cl_money_detail` VALUES (452, 'REPRBaZyZ2201810127457', 1, 2, '红包未领完返还', 82.50, 7, 1, 1, 1539313321, NULL);
INSERT INTO `cl_money_detail` VALUES (453, 'REPRBaZyZ2201810126924', 1, 2, '红包未领完返还', 16.67, 7, 1, 1, 1539313441, NULL);
INSERT INTO `cl_money_detail` VALUES (454, 'REvqp4ZyMo201810127760', 2, 10, '', -234.00, 1, 1, 1, 1539313626, NULL);
INSERT INTO `cl_money_detail` VALUES (455, 'RE4ZBLXBMN201810121613', 1, 7, '红包未领完返还', 565.94, 7, 1, 1, 1539314462, NULL);
INSERT INTO `cl_money_detail` VALUES (456, 'RE4ZBLXBMN201810125257', 1, 7, '红包未领完返还', 898.99, 7, 1, 1, 1539317941, NULL);
INSERT INTO `cl_money_detail` VALUES (457, 'RELbyJGK5d201810123290', 1, 6, '拍卖成功,获得拍卖价1000.00', 1000.00, 4, 1, 1, 1539320881, NULL);
INSERT INTO `cl_money_detail` VALUES (458, 'RELbyJGK5d201810122240', 1, 6, '拍卖成功,获得拍卖价2001.00', 2001.00, 4, 1, 1, 1539320941, NULL);
INSERT INTO `cl_money_detail` VALUES (459, 'RELNyznyXM201810121445', 1, 18, '红包未领完返还', 156.00, 7, 1, 1, 1539322261, NULL);
INSERT INTO `cl_money_detail` VALUES (460, 'RE4ZBLXBMN201810127565', 1, 7, '红包未领完返还', 2552.39, 7, 1, 1, 1539322261, NULL);
INSERT INTO `cl_money_detail` VALUES (461, 'RE4ZBLXBMN201810128519', 1, 7, '红包未领完返还', 5495.67, 7, 1, 1, 1539322321, NULL);
INSERT INTO `cl_money_detail` VALUES (462, 'RE4ZBLXBMN201810121302', 1, 7, '红包未领完返还', 51.70, 7, 1, 1, 1539322382, NULL);
INSERT INTO `cl_money_detail` VALUES (463, 'RE4ZBLXBMN201810126924', 1, 7, '红包未领完返还', 222.00, 7, 1, 1, 1539322441, NULL);
INSERT INTO `cl_money_detail` VALUES (464, 'RELNyznyXM201810123586', 1, 18, '红包未领完返还', 117.00, 7, 1, 1, 1539322501, NULL);
INSERT INTO `cl_money_detail` VALUES (465, 'RE4ZBLXBMN201810124588', 1, 7, '红包未领完返还', 556.00, 7, 1, 1, 1539322621, NULL);
INSERT INTO `cl_money_detail` VALUES (466, 'RE4ZBLXBMN201810129650', 1, 7, '红包未领完返还', 663.00, 7, 1, 1, 1539322861, NULL);
INSERT INTO `cl_money_detail` VALUES (467, 'RE4ZBLXBMN201810121272', 1, 7, '红包未领完返还', 566.00, 7, 1, 1, 1539322921, NULL);
INSERT INTO `cl_money_detail` VALUES (468, 'RE4ZBLXBMN201810125116', 1, 7, '红包未领完返还', 666.00, 7, 1, 1, 1539322921, NULL);
INSERT INTO `cl_money_detail` VALUES (469, 'RE4ZBLXBMN201810125927', 1, 7, '红包未领完返还', 332.99, 7, 1, 1, 1539323221, NULL);
INSERT INTO `cl_money_detail` VALUES (470, 'RE4ZBLXBMN201810127049', 1, 7, '红包未领完返还', 266.00, 7, 1, 1, 1539323281, NULL);
INSERT INTO `cl_money_detail` VALUES (471, 'REjGBP6KXA201810126667', 2, 3, '直播间付费', -100.00, 5, 1, 1, 1539324597, NULL);
INSERT INTO `cl_money_detail` VALUES (472, 'REjGBP6KXA201810121407', 2, 3, '', -3.00, 1, 4, 1, 1539324611, NULL);
INSERT INTO `cl_money_detail` VALUES (473, 'REjGBP6KXA201810121283', 1, 3, '', 2.00, 1, 4, 1, 1539324613, NULL);
INSERT INTO `cl_money_detail` VALUES (474, 'RE4ZBLXBMN201810121747', 2, 7, '', -199.99, 8, 1, 1, 1539326220, NULL);
INSERT INTO `cl_money_detail` VALUES (475, 'RE4ZBLXBMN201810128221', 2, 7, '', -999.99, 8, 1, 1, 1539326227, NULL);
INSERT INTO `cl_money_detail` VALUES (476, 'RE4ZBLXBMN201810127324', 2, 7, '', -99.99, 8, 1, 1, 1539326232, NULL);
INSERT INTO `cl_money_detail` VALUES (477, 'RE4ZBLXBMN201810122763', 2, 7, '', -9.99, 8, 1, 1, 1539326249, NULL);
INSERT INTO `cl_money_detail` VALUES (478, 'REvqp4ZyMo201810125042', 2, 10, '', -23.00, 1, 1, 1, 1539327077, NULL);
INSERT INTO `cl_money_detail` VALUES (479, 'REvqp4ZyMo201810128536', 2, 10, '', -124.00, 1, 1, 1, 1539327294, NULL);
INSERT INTO `cl_money_detail` VALUES (480, 'REvqp4ZyMo201810128724', 2, 10, '', -231.00, 1, 1, 1, 1539327476, NULL);
INSERT INTO `cl_money_detail` VALUES (481, 'REvqp4ZyMo201810121827', 1, 10, '', 10.05, 1, 1, 1, 1539327542, NULL);
INSERT INTO `cl_money_detail` VALUES (482, 'REvqp4ZyMo201810123957', 2, 10, '', -234.00, 1, 1, 1, 1539327814, NULL);
INSERT INTO `cl_money_detail` VALUES (483, 'REvqp4ZyMo201810129425', 1, 10, '', 78.00, 1, 1, 1, 1539327817, NULL);
INSERT INTO `cl_money_detail` VALUES (484, 'RE201810122540', 1, NULL, '拍卖角色区块链扫地僧竞拍价格被超越，返还冻结金额', 2000.00, 4, 1, 1, 1539327874, NULL);
INSERT INTO `cl_money_detail` VALUES (485, 'RELbyJGK5d201810126711', 2, 6, '拍卖角色区块链扫地僧加价成功，冻结竞拍金额 ', -3000.00, 4, 1, 1, 1539327874, NULL);
INSERT INTO `cl_money_detail` VALUES (486, 'RE14KVWp2X201810126911', 1, 11, '红包未领完返还', 13.34, 7, 1, 1, 1539328022, NULL);
INSERT INTO `cl_money_detail` VALUES (487, 'RE14KVWp2X201810125442', 1, 11, '红包未领完返还', 19.00, 7, 4, 1, 1539328141, NULL);
INSERT INTO `cl_money_detail` VALUES (488, 'RE14KVWp2X201810128285', 1, 11, '红包未领完返还', 10.00, 7, 4, 1, 1539328141, NULL);
INSERT INTO `cl_money_detail` VALUES (489, 'RE14KVWp2X201810125063', 1, 11, '红包未领完返还', 19.00, 7, 4, 1, 1539328201, NULL);
INSERT INTO `cl_money_detail` VALUES (490, 'RE14KVWp2X201810123333', 1, 11, '红包未领完返还', 10.00, 7, 4, 1, 1539328201, NULL);
INSERT INTO `cl_money_detail` VALUES (491, 'RE14KVWp2X201810124006', 1, 11, '红包未领完返还', 1.00, 7, 4, 1, 1539328321, NULL);
INSERT INTO `cl_money_detail` VALUES (492, 'RE14KVWp2X201810124110', 1, 11, '红包未领完返还', 1.00, 7, 4, 1, 1539328321, NULL);
INSERT INTO `cl_money_detail` VALUES (493, 'REX7yoXK1A201810128949', 2, 1, '', -22.00, 1, 1, 1, 1539328394, NULL);
INSERT INTO `cl_money_detail` VALUES (494, 'REX7yoXK1A201810124374', 2, 1, '', -222.00, 1, 4, 1, 1539328417, NULL);
INSERT INTO `cl_money_detail` VALUES (495, 'REX7yoXK1A201810128234', 2, 1, '', -12.00, 1, 4, 1, 1539328445, NULL);
INSERT INTO `cl_money_detail` VALUES (496, 'REX7yoXK1A201810127410', 2, 1, '', -254.00, 1, 1, 1, 1539328488, NULL);
INSERT INTO `cl_money_detail` VALUES (497, 'REX7yoXK1A201810126241', 1, 1, '', 84.66, 1, 1, 1, 1539328491, NULL);
INSERT INTO `cl_money_detail` VALUES (498, 'REvqp4ZyMo201810127080', 2, 10, '', -235.00, 1, 1, 1, 1539328509, NULL);
INSERT INTO `cl_money_detail` VALUES (499, 'REX7yoXK1A201810122054', 1, 1, '', 47.00, 1, 1, 1, 1539328525, NULL);
INSERT INTO `cl_money_detail` VALUES (500, 'REX7yoXK1A201810122999', 2, 1, '', -234.00, 1, 1, 1, 1539328592, NULL);
INSERT INTO `cl_money_detail` VALUES (501, 'REX7yoXK1A201810121371', 1, 1, '', 78.00, 1, 1, 1, 1539328595, NULL);
INSERT INTO `cl_money_detail` VALUES (502, 'REX7yoXK1A201810129635', 2, 1, '', -11.00, 1, 1, 1, 1539329111, NULL);
INSERT INTO `cl_money_detail` VALUES (503, 'REX7yoXK1A201810123007', 1, 1, '', 11.00, 1, 1, 1, 1539329113, NULL);
INSERT INTO `cl_money_detail` VALUES (504, 'REbOygWyo9201810128429', 2, 9, '', -999.99, 8, 1, 1, 1539329662, NULL);
INSERT INTO `cl_money_detail` VALUES (505, 'REbOygWyo9201810121779', 2, 9, '', -999.99, 8, 1, 1, 1539329762, NULL);
INSERT INTO `cl_money_detail` VALUES (506, 'REbOygWyo9201810129281', 2, 9, '', -999.99, 8, 1, 1, 1539329765, NULL);
INSERT INTO `cl_money_detail` VALUES (507, 'REbOygWyo9201810126051', 2, 9, '', -999.99, 8, 1, 1, 1539330083, NULL);
INSERT INTO `cl_money_detail` VALUES (508, 'REbOygWyo9201810126332', 2, 9, '', -999.99, 8, 1, 1, 1539330780, NULL);
INSERT INTO `cl_money_detail` VALUES (509, 'RELDBbxK5n201810127093', 1, 29, '红包未领完返还', 233.00, 7, 4, 1, 1539330782, NULL);
INSERT INTO `cl_money_detail` VALUES (510, 'REbOygWyo9201810125570', 2, 9, '', -999.99, 8, 1, 1, 1539330789, NULL);
INSERT INTO `cl_money_detail` VALUES (511, 'RELNyznyXM201810121730', 2, 18, '', -999.99, 8, 1, 1, 1539331030, NULL);
INSERT INTO `cl_money_detail` VALUES (512, 'RELNyznyXM201810128442', 2, 18, '', -2399.88, 8, 1, 1, 1539331043, NULL);
INSERT INTO `cl_money_detail` VALUES (513, 'RELNyznyXM201810126521', 2, 18, '', -999.99, 8, 1, 1, 1539331057, NULL);
INSERT INTO `cl_money_detail` VALUES (514, 'RELNyznyXM201810123619', 2, 18, '', -2399.88, 8, 1, 1, 1539331065, NULL);
INSERT INTO `cl_money_detail` VALUES (515, 'REbOygWyo9201810126592', 2, 9, '', -999.99, 8, 1, 1, 1539331154, NULL);
INSERT INTO `cl_money_detail` VALUES (516, 'REbOygWyo9201810129246', 2, 9, '', -199.99, 8, 1, 1, 1539331162, NULL);
INSERT INTO `cl_money_detail` VALUES (517, 'RELNyznyXM201810126636', 2, 18, '', -999.99, 8, 1, 1, 1539331548, NULL);
INSERT INTO `cl_money_detail` VALUES (518, 'RELNyznyXM201810125633', 2, 18, '', -999.99, 8, 1, 1, 1539331558, NULL);
INSERT INTO `cl_money_detail` VALUES (519, 'RELNyznyXM201810121213', 2, 18, '', -999.99, 8, 1, 1, 1539331564, NULL);
INSERT INTO `cl_money_detail` VALUES (520, 'REbOygWyo9201810121148', 2, 9, '', -9999.90, 8, 1, 1, 1539331701, NULL);
INSERT INTO `cl_money_detail` VALUES (521, 'REbOygWyo9201810127392', 2, 9, '', -19.99, 8, 1, 1, 1539331708, NULL);
INSERT INTO `cl_money_detail` VALUES (522, 'REbOygWyo9201810123191', 2, 9, '', -49.99, 8, 1, 1, 1539331712, NULL);
INSERT INTO `cl_money_detail` VALUES (523, 'REbOygWyo9201810127766', 2, 9, '', -199.99, 8, 1, 1, 1539331716, NULL);
INSERT INTO `cl_money_detail` VALUES (524, 'REbOygWyo9201810129698', 2, 9, '', -19.99, 8, 1, 1, 1539331721, NULL);
INSERT INTO `cl_money_detail` VALUES (525, 'REbOygWyo9201810129189', 2, 9, '', -999.99, 8, 1, 1, 1539331727, NULL);
INSERT INTO `cl_money_detail` VALUES (526, 'REbOygWyo9201810127987', 2, 9, '', -19.99, 8, 1, 1, 1539331730, NULL);
INSERT INTO `cl_money_detail` VALUES (527, 'REbOygWyo9201810126495', 2, 9, '', -999.99, 8, 1, 1, 1539331739, NULL);
INSERT INTO `cl_money_detail` VALUES (528, 'REbOygWyo9201810129456', 2, 9, '', -999.99, 8, 1, 1, 1539331742, NULL);
INSERT INTO `cl_money_detail` VALUES (529, 'REbOygWyo9201810127223', 2, 9, '', -999.99, 8, 1, 1, 1539331746, NULL);
INSERT INTO `cl_money_detail` VALUES (530, 'REbOygWyo9201810128818', 2, 9, '', -199.99, 8, 1, 1, 1539331749, NULL);
INSERT INTO `cl_money_detail` VALUES (531, 'REbOygWyo9201810123782', 2, 9, '', -999.99, 8, 1, 1, 1539331759, NULL);
INSERT INTO `cl_money_detail` VALUES (532, 'REbOygWyo9201810124594', 2, 9, '', -999.99, 8, 1, 1, 1539332083, NULL);
INSERT INTO `cl_money_detail` VALUES (533, 'RE14KVWp2X201810127174', 1, 11, '红包未领完返还', 19.00, 7, 4, 1, 1539332162, NULL);
INSERT INTO `cl_money_detail` VALUES (534, 'REbOygWyo9201810122234', 2, 9, '', -999.99, 8, 1, 1, 1539332216, NULL);
INSERT INTO `cl_money_detail` VALUES (535, 'REbOygWyo9201810122725', 2, 9, '', -999.99, 8, 1, 1, 1539332219, NULL);
INSERT INTO `cl_money_detail` VALUES (536, 'REbOygWyo9201810129586', 2, 9, '', -999.99, 8, 1, 1, 1539332223, NULL);
INSERT INTO `cl_money_detail` VALUES (537, 'RELNyznyXM201810122378', 2, 18, '', -999.99, 8, 1, 1, 1539332307, NULL);
INSERT INTO `cl_money_detail` VALUES (538, 'RELNyznyXM201810122864', 2, 18, '', -123.00, 1, 1, 1, 1539332317, NULL);
INSERT INTO `cl_money_detail` VALUES (539, 'RELNyznyXM201810124435', 2, 18, '', -199.99, 8, 1, 1, 1539332436, NULL);
INSERT INTO `cl_money_detail` VALUES (540, 'RELNyznyXM201810121745', 2, 18, '', -231.00, 1, 1, 1, 1539332443, NULL);
INSERT INTO `cl_money_detail` VALUES (541, 'RELNyznyXM201810123949', 2, 18, '', -999.99, 8, 1, 1, 1539332459, NULL);
INSERT INTO `cl_money_detail` VALUES (542, 'RELNyznyXM201810125974', 2, 18, '', -214.00, 1, 1, 1, 1539332668, NULL);
INSERT INTO `cl_money_detail` VALUES (543, 'REbOygWyo9201810129264', 2, 9, '', -999.99, 8, 1, 1, 1539332760, NULL);
INSERT INTO `cl_money_detail` VALUES (544, 'REbOygWyo9201810122301', 2, 9, '', -1.00, 1, 1, 1, 1539332773, NULL);
INSERT INTO `cl_money_detail` VALUES (545, 'REbOygWyo9201810125411', 2, 9, '', -1.00, 1, 1, 1, 1539332800, NULL);
INSERT INTO `cl_money_detail` VALUES (546, 'REbOygWyo9201810127997', 1, 9, '', 1.00, 1, 1, 1, 1539332812, NULL);
INSERT INTO `cl_money_detail` VALUES (547, 'RELNyznyXM201810129002', 2, 18, '', -23.00, 1, 1, 1, 1539332974, NULL);
INSERT INTO `cl_money_detail` VALUES (548, 'REbOygWyo9201810125775', 2, 9, '', -999.99, 8, 1, 1, 1539333020, NULL);
INSERT INTO `cl_money_detail` VALUES (549, 'REbOygWyo9201810123523', 2, 9, '', -1.00, 1, 1, 1, 1539333030, NULL);
INSERT INTO `cl_money_detail` VALUES (550, 'RELNyznyXM201810122534', 2, 18, '', -199.99, 8, 1, 1, 1539333307, NULL);
INSERT INTO `cl_money_detail` VALUES (551, 'RELNyznyXM201810128344', 2, 18, '', -32.00, 1, 1, 1, 1539333500, NULL);
INSERT INTO `cl_money_detail` VALUES (552, 'REbOygWyo9201810125528', 2, 9, '', -999.99, 8, 1, 1, 1539333539, NULL);
INSERT INTO `cl_money_detail` VALUES (553, 'REbOygWyo9201810126887', 2, 9, '', -1.00, 1, 1, 1, 1539333577, NULL);
INSERT INTO `cl_money_detail` VALUES (554, 'REbOygWyo9201810127775', 2, 9, '', -1.00, 1, 1, 1, 1539333592, NULL);
INSERT INTO `cl_money_detail` VALUES (555, 'RELNyznyXM201810127093', 2, 18, '', -321.00, 1, 1, 1, 1539333761, NULL);
INSERT INTO `cl_money_detail` VALUES (556, 'REbOygWyo9201810123492', 2, 9, '', -999.99, 8, 1, 1, 1539333881, NULL);
INSERT INTO `cl_money_detail` VALUES (557, 'REbOygWyo9201810129428', 2, 9, '', -1.00, 1, 1, 1, 1539333897, NULL);
INSERT INTO `cl_money_detail` VALUES (558, 'RELNyznyXM201810128791', 2, 18, '', -23.00, 1, 1, 1, 1539334119, NULL);
INSERT INTO `cl_money_detail` VALUES (559, 'RELNyznyXM201810122525', 2, 18, '', -199.99, 8, 1, 1, 1539334244, NULL);
INSERT INTO `cl_money_detail` VALUES (560, 'RELNyznyXM201810127395', 2, 18, '', -23.00, 1, 1, 1, 1539334252, NULL);
INSERT INTO `cl_money_detail` VALUES (561, 'REbOygWyo9201810121767', 2, 9, '', -999.99, 8, 1, 1, 1539334286, NULL);
INSERT INTO `cl_money_detail` VALUES (562, 'REbOygWyo9201810125133', 2, 9, '', -1.00, 1, 1, 1, 1539334298, NULL);
INSERT INTO `cl_money_detail` VALUES (563, 'REbOygWyo9201810129459', 2, 9, '', -999.99, 8, 1, 1, 1539334324, NULL);
INSERT INTO `cl_money_detail` VALUES (564, 'REbOygWyo9201810123332', 2, 9, '', -999.99, 8, 1, 1, 1539334329, NULL);
INSERT INTO `cl_money_detail` VALUES (565, 'RELNyznyXM201810124708', 2, 18, '', -23.00, 1, 1, 1, 1539334329, NULL);
INSERT INTO `cl_money_detail` VALUES (566, 'REbOygWyo9201810124716', 2, 9, '', -999.99, 8, 1, 1, 1539334332, NULL);
INSERT INTO `cl_money_detail` VALUES (567, 'REbOygWyo9201810121519', 2, 9, '', -1.00, 1, 1, 1, 1539334346, NULL);
INSERT INTO `cl_money_detail` VALUES (568, 'RELNyznyXM201810123509', 2, 18, '', -21.00, 1, 1, 1, 1539334348, NULL);
INSERT INTO `cl_money_detail` VALUES (569, 'REbOygWyo9201810124707', 2, 9, '', -999.99, 8, 1, 1, 1539334358, NULL);
INSERT INTO `cl_money_detail` VALUES (570, 'RELNyznyXM201810121931', 2, 18, '', -23.00, 1, 1, 1, 1539334403, NULL);
INSERT INTO `cl_money_detail` VALUES (571, 'RELNyznyXM201810128825', 2, 18, '', -12.00, 1, 1, 1, 1539334425, NULL);
INSERT INTO `cl_money_detail` VALUES (572, 'RELNyznyXM201810128635', 2, 18, '', -231.00, 1, 1, 1, 1539334608, NULL);
INSERT INTO `cl_money_detail` VALUES (573, 'RELNyznyXM201810129095', 2, 18, '', -231.00, 1, 1, 1, 1539334850, NULL);
INSERT INTO `cl_money_detail` VALUES (574, 'RE14KVWp2X201810127013', 1, 11, '拍卖成功,获得拍卖价2000.00', 2000.00, 4, 1, 1, 1539334861, NULL);
INSERT INTO `cl_money_detail` VALUES (575, 'RELNyznyXM201810129923', 2, 18, '', -32.00, 1, 1, 1, 1539334923, NULL);
INSERT INTO `cl_money_detail` VALUES (576, 'RE14KVWp2X201810127459', 1, 11, '拍卖成功,获得拍卖价3000.00', 3000.00, 4, 1, 1, 1539334981, NULL);
INSERT INTO `cl_money_detail` VALUES (577, 'RELNyznyXM201810125753', 2, 18, '', -32.00, 1, 1, 1, 1539335004, NULL);
INSERT INTO `cl_money_detail` VALUES (578, 'RELNyznyXM201810127398', 2, 18, '', -3.00, 1, 1, 1, 1539335019, NULL);
INSERT INTO `cl_money_detail` VALUES (579, 'RELNyznyXM201810126623', 2, 18, '', -23.00, 1, 1, 1, 1539335034, NULL);
INSERT INTO `cl_money_detail` VALUES (580, 'RELNyznyXM201810121985', 2, 18, '', -23.00, 1, 1, 1, 1539335061, NULL);
INSERT INTO `cl_money_detail` VALUES (581, 'RELNyznyXM201810124673', 1, 18, '', 11.50, 1, 1, 1, 1539335065, NULL);
INSERT INTO `cl_money_detail` VALUES (582, 'RE201810128270', 1, NULL, '拍卖成功,获得拍卖价10000.00', 10000.00, 4, 1, 1, 1539335281, NULL);
INSERT INTO `cl_money_detail` VALUES (583, 'RE14KVWp2X201810128099', 2, 11, '拍卖角色升国旗加价成功，冻结竞拍金额 ', -136.00, 4, 1, 1, 1539335381, NULL);
INSERT INTO `cl_money_detail` VALUES (584, 'RE201810123853', 1, NULL, '拍卖成功,获得拍卖价10000.00', 10000.00, 4, 1, 1, 1539335401, NULL);
INSERT INTO `cl_money_detail` VALUES (585, 'RELNyznyXM201810128506', 2, 18, '', -321.00, 1, 1, 1, 1539335457, NULL);
INSERT INTO `cl_money_detail` VALUES (586, 'RE14KVWp2X201810128018', 1, 11, '红包未领完返还', 20.00, 7, 1, 1, 1539335761, NULL);
INSERT INTO `cl_money_detail` VALUES (587, 'RE14KVWp2X201810124143', 1, 11, '红包未领完返还', 6.66, 7, 1, 1, 1539335822, NULL);
INSERT INTO `cl_money_detail` VALUES (588, 'REjGBP6KXA201810121718', 2, 3, '', -10.00, 1, 4, 1, 1539337199, NULL);
INSERT INTO `cl_money_detail` VALUES (589, 'REjGBP6KXA201810127533', 1, 3, '', 1.67, 1, 4, 1, 1539337202, NULL);
INSERT INTO `cl_money_detail` VALUES (590, 'RE3oynryJV201810125048', 1, 38, '红包未领完返还', 98.00, 7, 1, 1, 1539337741, NULL);
INSERT INTO `cl_money_detail` VALUES (591, 'RE4ZBLXBMN201810135855', 1, 7, '红包未领完返还', 889.00, 7, 1, 1, 1539394561, NULL);
INSERT INTO `cl_money_detail` VALUES (592, 'RELbyJGK5d201810138269', 1, 6, '拍卖成功,获得拍卖价20.00', 20.00, 4, 1, 1, 1539396481, NULL);
INSERT INTO `cl_money_detail` VALUES (593, 'REvqp4ZyMo201810137836', 1, 10, '红包未领完返还', 123.00, 7, 1, 1, 1539398042, NULL);
INSERT INTO `cl_money_detail` VALUES (594, 'REvqp4ZyMo201810136683', 1, 10, '红包未领完返还', 234.00, 7, 1, 1, 1539398101, NULL);
INSERT INTO `cl_money_detail` VALUES (595, 'REvqp4ZyMo201810131683', 1, 10, '红包未领完返还', 123.00, 7, 1, 1, 1539398161, NULL);
INSERT INTO `cl_money_detail` VALUES (596, 'RELNyznyXM201810135329', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539398521, NULL);
INSERT INTO `cl_money_detail` VALUES (597, 'RELNyznyXM201810139649', 1, 18, '红包未领完返还', 7.67, 7, 1, 1, 1539398521, NULL);
INSERT INTO `cl_money_detail` VALUES (598, 'REX7yoXK1A201810139946', 2, 1, '拍卖房间hjjhhh加价成功，冻结竞拍金额 ', -100001.00, 4, 1, 1, 1539399773, NULL);
INSERT INTO `cl_money_detail` VALUES (599, 'REvqp4ZyMo201810135851', 1, 10, '红包未领完返还', 234.00, 7, 1, 1, 1539400081, NULL);
INSERT INTO `cl_money_detail` VALUES (600, 'RE14KVWp2X201810138932', 1, 11, '', 49.99, 3, 1, 1, 1539402150, NULL);
INSERT INTO `cl_money_detail` VALUES (601, 'REjGBP6KXA201810135048', 1, 3, '红包未领完返还', 1.00, 7, 4, 1, 1539411061, NULL);
INSERT INTO `cl_money_detail` VALUES (602, 'RE14KVWp2X201810134501', 2, 11, '直播间付费', -300.00, 5, 1, 1, 1539411275, NULL);
INSERT INTO `cl_money_detail` VALUES (603, 'REX7yoXK1A201810131892', 1, 1, '拍卖房间hjjhhh竞拍价格被超越，返还冻结金额', 100001.00, 4, 1, 1, 1539411825, NULL);
INSERT INTO `cl_money_detail` VALUES (604, 'RExkyO7yJ6201810137804', 2, 17, '拍卖房间hjjhhh加价成功，冻结竞拍金额 ', -150000.00, 4, 1, 1, 1539411825, NULL);
INSERT INTO `cl_money_detail` VALUES (605, 'RExkyO7yJ6201810135407', 2, 17, '拍卖房间大呱呱加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539411843, NULL);
INSERT INTO `cl_money_detail` VALUES (606, 'RExkyO7yJ6201810133491', 2, 17, '拍卖角色大佬加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1539411853, NULL);
INSERT INTO `cl_money_detail` VALUES (607, 'RExkyO7yJ6201810135084', 2, 17, '拍卖角色大佬的男人加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1539411864, NULL);
INSERT INTO `cl_money_detail` VALUES (608, 'RE14KVWp2X201810139765', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539412779, NULL);
INSERT INTO `cl_money_detail` VALUES (609, 'REvqp4ZyMo201810138040', 1, 10, '红包未领完返还', 23.00, 7, 1, 1, 1539413521, NULL);
INSERT INTO `cl_money_detail` VALUES (610, 'REjGBP6KXA201810131393', 1, 3, '', 749.85, 3, 1, 1, 1539413591, NULL);
INSERT INTO `cl_money_detail` VALUES (611, 'RE14KVWp2X201810133651', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539413632, NULL);
INSERT INTO `cl_money_detail` VALUES (612, 'REvqp4ZyMo201810131826', 1, 10, '红包未领完返还', 124.00, 7, 1, 1, 1539413701, NULL);
INSERT INTO `cl_money_detail` VALUES (613, 'REvqp4ZyMo201810133481', 1, 10, '红包未领完返还', 220.95, 7, 1, 1, 1539413881, NULL);
INSERT INTO `cl_money_detail` VALUES (614, 'RExkyO7yJ6201810135647', 2, 17, '直播间升级付费', -1000.00, 7, 1, 1, 1539414088, NULL);
INSERT INTO `cl_money_detail` VALUES (615, 'REvqp4ZyMo201810131300', 1, 10, '红包未领完返还', 156.00, 7, 1, 1, 1539414241, NULL);
INSERT INTO `cl_money_detail` VALUES (616, 'RELbyJGK5d201810132963', 2, 6, '直播间付费', -300.00, 5, 1, 1, 1539414350, NULL);
INSERT INTO `cl_money_detail` VALUES (617, 'RE14KVWp2X201810139575', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539414692, NULL);
INSERT INTO `cl_money_detail` VALUES (618, 'REX7yoXK1A201810133781', 1, 1, '红包未领完返还', 22.00, 7, 1, 1, 1539414841, NULL);
INSERT INTO `cl_money_detail` VALUES (619, 'REX7yoXK1A201810133753', 1, 1, '红包未领完返还', 222.00, 7, 4, 1, 1539414841, NULL);
INSERT INTO `cl_money_detail` VALUES (620, 'REjGBP6KXA201810135600', 1, 3, '', 97.96, 3, 1, 1, 1539414868, NULL);
INSERT INTO `cl_money_detail` VALUES (621, 'REX7yoXK1A201810136786', 1, 1, '红包未领完返还', 12.00, 7, 4, 1, 1539414901, NULL);
INSERT INTO `cl_money_detail` VALUES (622, 'REX7yoXK1A201810136317', 1, 1, '红包未领完返还', 169.34, 7, 1, 1, 1539414901, NULL);
INSERT INTO `cl_money_detail` VALUES (623, 'RELbyJGK5d201810132849', 2, 6, '直播间付费', -300.00, 5, 1, 1, 1539414933, NULL);
INSERT INTO `cl_money_detail` VALUES (624, 'REvqp4ZyMo201810139098', 1, 10, '红包未领完返还', 188.00, 7, 1, 1, 1539414961, NULL);
INSERT INTO `cl_money_detail` VALUES (625, 'REX7yoXK1A201810139549', 1, 1, '红包未领完返还', 156.00, 7, 1, 1, 1539415021, NULL);
INSERT INTO `cl_money_detail` VALUES (626, 'RELbyJGK5d201810138652', 1, 6, '直播间付费', 0.00, 5, 1, 1, 1539415202, NULL);
INSERT INTO `cl_money_detail` VALUES (627, 'REjGBP6KXA201810135449', 1, 3, '', 384.62, 3, 1, 1, 1539415491, NULL);
INSERT INTO `cl_money_detail` VALUES (628, 'RE14KVWp2X201810139598', 2, 11, '直播间升级付费', -1000.00, 7, 1, 1, 1539415814, NULL);
INSERT INTO `cl_money_detail` VALUES (629, 'RExkyO7yJ6201810131009', 2, 17, '', -200.00, 1, 1, 1, 1539416088, NULL);
INSERT INTO `cl_money_detail` VALUES (630, 'RExkyO7yJ6201810138903', 1, 17, '', 0.01, 1, 1, 1, 1539416091, NULL);
INSERT INTO `cl_money_detail` VALUES (631, 'RExkyO7yJ6201810136986', 2, 17, '', -10.00, 1, 1, 1, 1539416119, NULL);
INSERT INTO `cl_money_detail` VALUES (632, 'RExkyO7yJ6201810132809', 2, 17, '', -22.00, 1, 1, 1, 1539416239, NULL);
INSERT INTO `cl_money_detail` VALUES (633, 'RExkyO7yJ6201810131884', 2, 17, '', -20.00, 1, 1, 1, 1539416505, NULL);
INSERT INTO `cl_money_detail` VALUES (634, 'RExkyO7yJ6201810135685', 2, 17, '', -200.00, 1, 1, 1, 1539416527, NULL);
INSERT INTO `cl_money_detail` VALUES (635, 'RE1VKwmBQ3201810133200', 2, 14, '', -12.00, 1, 1, 1, 1539416655, NULL);
INSERT INTO `cl_money_detail` VALUES (636, 'RExkyO7yJ6201810136579', 2, 17, '', -20.00, 1, 1, 1, 1539416665, NULL);
INSERT INTO `cl_money_detail` VALUES (637, 'RE1VKwmBQ3201810134300', 2, 14, '', -12.00, 1, 1, 1, 1539416790, NULL);
INSERT INTO `cl_money_detail` VALUES (638, 'RExkyO7yJ6201810137885', 2, 17, '', -20.00, 1, 1, 1, 1539416797, NULL);
INSERT INTO `cl_money_detail` VALUES (639, 'RE1VKwmBQ3201810132835', 2, 14, '', -2.00, 1, 1, 1, 1539416823, NULL);
INSERT INTO `cl_money_detail` VALUES (640, 'RExkyO7yJ6201810131005', 2, 17, '活动付费', -200.00, 7, 1, 1, 1539416842, NULL);
INSERT INTO `cl_money_detail` VALUES (641, 'RE1VKwmBQ3201810137884', 2, 14, '', -21.00, 1, 1, 1, 1539416851, NULL);
INSERT INTO `cl_money_detail` VALUES (642, 'RExkyO7yJ6201810134186', 2, 17, '', -20.00, 1, 1, 1, 1539416869, NULL);
INSERT INTO `cl_money_detail` VALUES (643, 'RExkyO7yJ6201810135441', 2, 17, '', -200.00, 1, 1, 1, 1539416888, NULL);
INSERT INTO `cl_money_detail` VALUES (644, 'RExkyO7yJ6201810138220', 2, 17, '', -199.99, 8, 1, 1, 1539416891, NULL);
INSERT INTO `cl_money_detail` VALUES (645, 'RExkyO7yJ6201810136082', 2, 17, '', -99.99, 8, 1, 1, 1539416930, NULL);
INSERT INTO `cl_money_detail` VALUES (646, 'RExkyO7yJ6201810132129', 2, 17, '', -9.99, 8, 1, 1, 1539416932, NULL);
INSERT INTO `cl_money_detail` VALUES (647, 'RExkyO7yJ6201810132265', 2, 17, '', -9.99, 8, 1, 1, 1539416936, NULL);
INSERT INTO `cl_money_detail` VALUES (648, 'RExkyO7yJ6201810136585', 2, 17, '', -20.00, 1, 1, 1, 1539417018, NULL);
INSERT INTO `cl_money_detail` VALUES (649, 'RExkyO7yJ6201810137657', 1, 17, '', 0.01, 1, 1, 1, 1539417024, NULL);
INSERT INTO `cl_money_detail` VALUES (650, 'RExkyO7yJ6201810131931', 2, 17, '', -20.00, 1, 1, 1, 1539417039, NULL);
INSERT INTO `cl_money_detail` VALUES (651, 'RExkyO7yJ6201810134560', 1, 17, '', 10.00, 1, 1, 1, 1539417042, NULL);
INSERT INTO `cl_money_detail` VALUES (652, 'RExkyO7yJ6201810138154', 2, 17, '', -20.00, 1, 1, 1, 1539417144, NULL);
INSERT INTO `cl_money_detail` VALUES (653, 'RExkyO7yJ6201810133232', 2, 17, '', -20.00, 1, 1, 1, 1539417157, NULL);
INSERT INTO `cl_money_detail` VALUES (654, 'RExkyO7yJ6201810137434', 2, 17, '', -20.00, 1, 1, 1, 1539417232, NULL);
INSERT INTO `cl_money_detail` VALUES (655, 'RExkyO7yJ6201810137944', 2, 17, '', -20.00, 1, 1, 1, 1539417444, NULL);
INSERT INTO `cl_money_detail` VALUES (656, 'RExkyO7yJ6201810133628', 2, 17, '', -20.00, 1, 1, 1, 1539417830, NULL);
INSERT INTO `cl_money_detail` VALUES (657, 'RExkyO7yJ6201810136034', 2, 17, '', -20.00, 1, 1, 1, 1539417852, NULL);
INSERT INTO `cl_money_detail` VALUES (658, 'RE1VKwmBQ3201810134420', 2, 14, '', -12.00, 1, 1, 1, 1539417875, NULL);
INSERT INTO `cl_money_detail` VALUES (659, 'RE1VKwmBQ3201810139920', 2, 14, '', -12.00, 1, 1, 1, 1539418015, NULL);
INSERT INTO `cl_money_detail` VALUES (660, 'RE1VKwmBQ3201810136515', 2, 14, '', -12.00, 1, 1, 1, 1539418057, NULL);
INSERT INTO `cl_money_detail` VALUES (661, 'RE1VKwmBQ3201810134776', 2, 14, '', -12.00, 1, 1, 1, 1539418166, NULL);
INSERT INTO `cl_money_detail` VALUES (662, 'RExkyO7yJ6201810134232', 2, 17, '', -20.00, 1, 1, 1, 1539418239, NULL);
INSERT INTO `cl_money_detail` VALUES (663, 'RExkyO7yJ6201810131721', 1, 17, '', 3.33, 1, 1, 1, 1539418242, NULL);
INSERT INTO `cl_money_detail` VALUES (664, 'RExkyO7yJ6201810137333', 2, 17, '', -20.00, 1, 1, 1, 1539418259, NULL);
INSERT INTO `cl_money_detail` VALUES (665, 'RExkyO7yJ6201810133349', 1, 17, '', 6.66, 1, 1, 1, 1539418262, NULL);
INSERT INTO `cl_money_detail` VALUES (666, 'RExkyO7yJ6201810136695', 2, 17, '', -20.00, 1, 1, 1, 1539418274, NULL);
INSERT INTO `cl_money_detail` VALUES (667, 'RExkyO7yJ6201810137600', 1, 17, '', 3.33, 1, 1, 1, 1539418276, NULL);
INSERT INTO `cl_money_detail` VALUES (668, 'RExkyO7yJ6201810136480', 2, 17, '', -20.00, 1, 1, 1, 1539418294, NULL);
INSERT INTO `cl_money_detail` VALUES (669, 'RExkyO7yJ6201810135696', 1, 17, '', 3.33, 1, 1, 1, 1539418297, NULL);
INSERT INTO `cl_money_detail` VALUES (670, 'RELNyznyXM201810136399', 1, 18, '红包未领完返还', 123.00, 7, 1, 1, 1539418741, NULL);
INSERT INTO `cl_money_detail` VALUES (671, 'RELNyznyXM201810133767', 1, 18, '红包未领完返还', 231.00, 7, 1, 1, 1539418861, NULL);
INSERT INTO `cl_money_detail` VALUES (672, 'RELNyznyXM201810132884', 1, 18, '红包未领完返还', 214.00, 7, 1, 1, 1539419101, NULL);
INSERT INTO `cl_money_detail` VALUES (673, 'REbOygWyo9201810138980', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539419221, NULL);
INSERT INTO `cl_money_detail` VALUES (674, 'RELNyznyXM201810132760', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539419401, NULL);
INSERT INTO `cl_money_detail` VALUES (675, 'REbOygWyo9201810136184', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539419461, NULL);
INSERT INTO `cl_money_detail` VALUES (676, 'REQLBRzB7n201810136858', 2, 33, '', -199.99, 8, 1, 1, 1539419525, NULL);
INSERT INTO `cl_money_detail` VALUES (677, 'REQLBRzB7n201810138562', 2, 33, '', -199.99, 8, 1, 1, 1539419527, NULL);
INSERT INTO `cl_money_detail` VALUES (678, 'REQLBRzB7n201810139869', 2, 33, '', -199.99, 8, 1, 1, 1539419534, NULL);
INSERT INTO `cl_money_detail` VALUES (679, 'REQLBRzB7n201810137606', 2, 33, '', -99.99, 8, 1, 1, 1539419536, NULL);
INSERT INTO `cl_money_detail` VALUES (680, 'REQLBRzB7n201810139231', 2, 33, '', -9.99, 8, 1, 1, 1539419538, NULL);
INSERT INTO `cl_money_detail` VALUES (681, 'REQLBRzB7n201810131898', 2, 33, '', -19.99, 8, 1, 1, 1539419543, NULL);
INSERT INTO `cl_money_detail` VALUES (682, 'RELNyznyXM201810135763', 1, 18, '红包未领完返还', 32.00, 7, 1, 1, 1539419941, NULL);
INSERT INTO `cl_money_detail` VALUES (683, 'REbOygWyo9201810134157', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539420002, NULL);
INSERT INTO `cl_money_detail` VALUES (684, 'REbOygWyo9201810133597', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539420002, NULL);
INSERT INTO `cl_money_detail` VALUES (685, 'REQLBRzB7n201810131526', 2, 33, '', -1000.00, 1, 1, 1, 1539420124, NULL);
INSERT INTO `cl_money_detail` VALUES (686, 'REQLBRzB7n201810135527', 1, 33, '', 166.67, 1, 1, 1, 1539420130, NULL);
INSERT INTO `cl_money_detail` VALUES (687, 'RELDBbxK5n201810135980', 1, 29, '', 666.66, 1, 1, 1, 1539420136, NULL);
INSERT INTO `cl_money_detail` VALUES (688, 'RELNyznyXM201810135528', 1, 18, '红包未领完返还', 321.00, 7, 1, 1, 1539420181, NULL);
INSERT INTO `cl_money_detail` VALUES (689, 'REQLBRzB7n201810138056', 2, 33, '', -99.99, 8, 1, 1, 1539420246, NULL);
INSERT INTO `cl_money_detail` VALUES (690, 'REQLBRzB7n201810137439', 2, 33, '', -99.99, 8, 1, 1, 1539420248, NULL);
INSERT INTO `cl_money_detail` VALUES (691, 'REQLBRzB7n201810138640', 2, 33, '', -99.99, 8, 1, 1, 1539420249, NULL);
INSERT INTO `cl_money_detail` VALUES (692, 'REQLBRzB7n201810131668', 2, 33, '', -199.99, 8, 1, 1, 1539420254, NULL);
INSERT INTO `cl_money_detail` VALUES (693, 'REQLBRzB7n201810132307', 2, 33, '', -199.99, 8, 1, 1, 1539420255, NULL);
INSERT INTO `cl_money_detail` VALUES (694, 'REQLBRzB7n201810136279', 2, 33, '', -199.99, 8, 1, 1, 1539420261, NULL);
INSERT INTO `cl_money_detail` VALUES (695, 'REbOygWyo9201810137704', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539420301, NULL);
INSERT INTO `cl_money_detail` VALUES (696, 'REQLBRzB7n201810138279', 2, 33, '', -199.99, 8, 1, 1, 1539420343, NULL);
INSERT INTO `cl_money_detail` VALUES (697, 'REQLBRzB7n201810135801', 2, 33, '', -199.99, 8, 1, 1, 1539420345, NULL);
INSERT INTO `cl_money_detail` VALUES (698, 'REQLBRzB7n201810138599', 2, 33, '', -199.99, 8, 1, 1, 1539420346, NULL);
INSERT INTO `cl_money_detail` VALUES (699, 'REQLBRzB7n201810136579', 2, 33, '', -19.99, 8, 1, 1, 1539420352, NULL);
INSERT INTO `cl_money_detail` VALUES (700, 'RELNyznyXM201810132075', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539420541, NULL);
INSERT INTO `cl_money_detail` VALUES (701, 'REjGBP6KXA201810134274', 2, 3, '升级空间扣除60000', -60000.00, 7, 1, 1, 1539420601, NULL);
INSERT INTO `cl_money_detail` VALUES (702, 'REQLBRzB7n201810135067', 2, 33, '', -999.99, 8, 1, 1, 1539420649, NULL);
INSERT INTO `cl_money_detail` VALUES (703, 'REQLBRzB7n201810138772', 2, 33, '', -999.99, 8, 1, 1, 1539420651, NULL);
INSERT INTO `cl_money_detail` VALUES (704, 'RELNyznyXM201810136864', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539420661, NULL);
INSERT INTO `cl_money_detail` VALUES (705, 'REQLBRzB7n201810139704', 2, 33, '', -199.99, 8, 1, 1, 1539420667, NULL);
INSERT INTO `cl_money_detail` VALUES (706, 'REQLBRzB7n201810139147', 2, 33, '', -199.99, 8, 1, 1, 1539420669, NULL);
INSERT INTO `cl_money_detail` VALUES (707, 'REQLBRzB7n201810139680', 2, 33, '', -199.99, 8, 1, 1, 1539420671, NULL);
INSERT INTO `cl_money_detail` VALUES (708, 'REbOygWyo9201810137592', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539420722, NULL);
INSERT INTO `cl_money_detail` VALUES (709, 'RELNyznyXM201810134751', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539420781, NULL);
INSERT INTO `cl_money_detail` VALUES (710, 'REbOygWyo9201810134527', 1, 9, '红包未领完返还', 1.00, 7, 1, 1, 1539420781, NULL);
INSERT INTO `cl_money_detail` VALUES (711, 'RELNyznyXM201810135746', 1, 18, '红包未领完返还', 21.00, 7, 1, 1, 1539420781, NULL);
INSERT INTO `cl_money_detail` VALUES (712, 'RELNyznyXM201810132869', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539420841, NULL);
INSERT INTO `cl_money_detail` VALUES (713, 'RELNyznyXM201810138028', 1, 18, '红包未领完返还', 12.00, 7, 1, 1, 1539420841, NULL);
INSERT INTO `cl_money_detail` VALUES (714, 'RELNyznyXM201810139389', 1, 18, '红包未领完返还', 231.00, 7, 1, 1, 1539421021, NULL);
INSERT INTO `cl_money_detail` VALUES (715, 'RELDBbxK5n201810137289', 2, 29, '', -10000.00, 1, 1, 1, 1539421250, NULL);
INSERT INTO `cl_money_detail` VALUES (716, 'RELDBbxK5n201810138599', 1, 29, '', 611.12, 1, 1, 1, 1539421253, NULL);
INSERT INTO `cl_money_detail` VALUES (717, 'REQLBRzB7n201810136094', 1, 33, '', 1711.10, 1, 1, 1, 1539421258, NULL);
INSERT INTO `cl_money_detail` VALUES (718, 'RELNyznyXM201810136049', 1, 18, '红包未领完返还', 231.00, 7, 1, 1, 1539421261, NULL);
INSERT INTO `cl_money_detail` VALUES (719, 'RELNyznyXM201810136407', 1, 18, '红包未领完返还', 32.00, 7, 1, 1, 1539421381, NULL);
INSERT INTO `cl_money_detail` VALUES (720, 'RELNyznyXM201810133736', 1, 18, '红包未领完返还', 32.00, 7, 1, 1, 1539421442, NULL);
INSERT INTO `cl_money_detail` VALUES (721, 'RELNyznyXM201810134892', 1, 18, '红包未领完返还', 3.00, 7, 1, 1, 1539421442, NULL);
INSERT INTO `cl_money_detail` VALUES (722, 'RELNyznyXM201810135315', 1, 18, '红包未领完返还', 23.00, 7, 1, 1, 1539421442, NULL);
INSERT INTO `cl_money_detail` VALUES (723, 'RELNyznyXM201810131516', 1, 18, '红包未领完返还', 11.50, 7, 1, 1, 1539421501, NULL);
INSERT INTO `cl_money_detail` VALUES (724, 'REzVpjVpla201810133678', 1, 0, '拍卖成功,获得拍卖价136.00', 136.00, 4, 1, 1, 1539421801, NULL);
INSERT INTO `cl_money_detail` VALUES (725, 'RELNyznyXM201810137749', 1, 18, '红包未领完返还', 321.00, 7, 1, 1, 1539421861, NULL);
INSERT INTO `cl_money_detail` VALUES (726, 'REjGBP6KXA201810133726', 2, 3, '', -649.87, 8, 1, 1, 1539422396, NULL);
INSERT INTO `cl_money_detail` VALUES (727, 'REjGBP6KXA201810136323', 1, 3, '', 13999.30, 3, 1, 1, 1539422417, NULL);
INSERT INTO `cl_money_detail` VALUES (728, 'REjGBP6KXA201810131451', 1, 3, '', 141398.59, 3, 1, 1, 1539422424, NULL);
INSERT INTO `cl_money_detail` VALUES (729, 'REjGBP6KXA201810131009', 1, 3, '', 349.97, 3, 1, 1, 1539422461, NULL);
INSERT INTO `cl_money_detail` VALUES (730, 'REQLBRzB7n201810135884', 2, 33, '活动付费', -100.00, 7, 1, 1, 1539422752, NULL);
INSERT INTO `cl_money_detail` VALUES (731, 'RExkyO7yJ6201810133592', 1, 17, '拍卖房间大呱呱竞拍价格被超越，返还冻结金额', 100000.00, 4, 1, 1, 1539423594, NULL);
INSERT INTO `cl_money_detail` VALUES (732, 'RE14KVWp2X201810132936', 2, 11, '拍卖房间大呱呱加价成功，冻结竞拍金额 ', -1000001.00, 4, 1, 1, 1539423594, NULL);
INSERT INTO `cl_money_detail` VALUES (733, 'REjGBP6KXA201810138463', 1, 3, '红包未领完返还', 8.33, 7, 4, 1, 1539423601, NULL);
INSERT INTO `cl_money_detail` VALUES (734, 'RExkyO7yJ6201810136894', 1, 17, '拍卖角色大佬的男人竞拍价格被超越，返还冻结金额', 10000.00, 4, 1, 1, 1539423708, NULL);
INSERT INTO `cl_money_detail` VALUES (735, 'RELDBbxK5n201810137442', 2, 29, '拍卖角色大佬的男人加价成功，冻结竞拍金额 ', -20000.00, 4, 1, 1, 1539423708, NULL);
INSERT INTO `cl_money_detail` VALUES (736, 'RELDBbxK5n201810136346', 2, 29, '', -666.00, 1, 1, 1, 1539424003, NULL);
INSERT INTO `cl_money_detail` VALUES (737, 'RELDBbxK5n201810138958', 2, 29, '', -888.00, 1, 1, 1, 1539424071, NULL);
INSERT INTO `cl_money_detail` VALUES (738, 'REQLBRzB7n201810132878', 2, 33, '', -5555.00, 1, 1, 1, 1539424087, NULL);
INSERT INTO `cl_money_detail` VALUES (739, 'RELDBbxK5n201810133412', 1, 29, '', 1111.00, 1, 1, 1, 1539424093, NULL);
INSERT INTO `cl_money_detail` VALUES (740, 'RELDBbxK5n201810139655', 1, 29, '', 111.00, 1, 1, 1, 1539424120, NULL);
INSERT INTO `cl_money_detail` VALUES (741, 'REQLBRzB7n201810139054', 2, 33, '', -222.00, 1, 1, 1, 1539424141, NULL);
INSERT INTO `cl_money_detail` VALUES (742, 'REQLBRzB7n201810139392', 2, 33, '', -222.00, 1, 1, 1, 1539424152, NULL);
INSERT INTO `cl_money_detail` VALUES (743, 'REQLBRzB7n201810138154', 2, 33, '', -5555.00, 1, 1, 1, 1539424174, NULL);
INSERT INTO `cl_money_detail` VALUES (744, 'REQLBRzB7n201810135788', 2, 33, '', -22.00, 1, 1, 1, 1539424199, NULL);
INSERT INTO `cl_money_detail` VALUES (745, 'RE14KVWp2X201810138365', 2, 11, '', -200.00, 1, 1, 1, 1539424214, NULL);
INSERT INTO `cl_money_detail` VALUES (746, 'RE14KVWp2X201810131552', 1, 11, '', 199.99, 1, 1, 1, 1539424220, NULL);
INSERT INTO `cl_money_detail` VALUES (747, 'RE14KVWp2X201810139992', 2, 11, '', -20.00, 1, 1, 1, 1539424231, NULL);
INSERT INTO `cl_money_detail` VALUES (748, 'RE14KVWp2X201810139530', 1, 11, '', 20.00, 1, 1, 1, 1539424233, NULL);
INSERT INTO `cl_money_detail` VALUES (749, 'RE14KVWp2X201810134937', 2, 11, '', -200.00, 1, 1, 1, 1539424248, NULL);
INSERT INTO `cl_money_detail` VALUES (750, 'RE14KVWp2X201810131894', 1, 11, '', 200.00, 1, 1, 1, 1539424250, NULL);
INSERT INTO `cl_money_detail` VALUES (751, 'RE14KVWp2X201810137650', 2, 11, '', -20.00, 1, 1, 1, 1539424284, NULL);
INSERT INTO `cl_money_detail` VALUES (752, 'RE14KVWp2X201810136349', 1, 11, '', 19.99, 1, 1, 1, 1539424285, NULL);
INSERT INTO `cl_money_detail` VALUES (753, 'RELDBbxK5n201810134858', 2, 29, '', -6666.00, 1, 4, 1, 1539424301, NULL);
INSERT INTO `cl_money_detail` VALUES (754, 'RE14KVWp2X201810136461', 2, 11, '', -20.00, 1, 1, 1, 1539424345, NULL);
INSERT INTO `cl_money_detail` VALUES (755, 'RE14KVWp2X201810139143', 1, 11, '', 20.00, 1, 1, 1, 1539424347, NULL);
INSERT INTO `cl_money_detail` VALUES (756, 'RELDBbxK5n201810139970', 2, 29, '', -888.00, 1, 1, 1, 1539424357, NULL);
INSERT INTO `cl_money_detail` VALUES (757, 'RE14KVWp2X201810133988', 2, 11, '', -20.00, 1, 1, 1, 1539424359, NULL);
INSERT INTO `cl_money_detail` VALUES (758, 'RE14KVWp2X201810135102', 1, 11, '', 0.01, 1, 1, 1, 1539424361, NULL);
INSERT INTO `cl_money_detail` VALUES (759, 'RE14KVWp2X201810135634', 2, 11, '', -78.00, 1, 4, 1, 1539424381, NULL);
INSERT INTO `cl_money_detail` VALUES (760, 'RE14KVWp2X201810139044', 1, 11, '', 78.00, 1, 4, 1, 1539424388, NULL);
INSERT INTO `cl_money_detail` VALUES (761, 'RE14KVWp2X201810137910', 2, 11, '', -200.00, 1, 4, 1, 1539424414, NULL);
INSERT INTO `cl_money_detail` VALUES (762, 'RELDBbxK5n201810131820', 2, 29, '', -6666.00, 1, 1, 1, 1539424435, NULL);
INSERT INTO `cl_money_detail` VALUES (763, 'RE14KVWp2X201810138368', 1, 11, '', 200.00, 1, 4, 1, 1539424455, NULL);
INSERT INTO `cl_money_detail` VALUES (764, 'REjGBP6KXA201810132050', 1, 3, '', 19.99, 1, 1, 1, 1539424483, NULL);
INSERT INTO `cl_money_detail` VALUES (765, 'RELDBbxK5n201810139955', 2, 29, '', -777.00, 1, 1, 1, 1539425516, NULL);
INSERT INTO `cl_money_detail` VALUES (766, 'RELDBbxK5n201810132494', 1, 29, '', 129.50, 1, 1, 1, 1539425518, NULL);
INSERT INTO `cl_money_detail` VALUES (767, 'RE14KVWp2X201810138598', 2, 11, '', -20.00, 1, 1, 1, 1539425863, NULL);
INSERT INTO `cl_money_detail` VALUES (768, 'RE14KVWp2X201810132172', 2, 11, '', -20.00, 1, 4, 1, 1539425871, NULL);
INSERT INTO `cl_money_detail` VALUES (769, 'REQLBRzB7n201810131622', 1, 33, '竞猜获胜,赢得1.22积分', 1.22, 2, 1, 1, 1539425881, NULL);
INSERT INTO `cl_money_detail` VALUES (770, 'RE14KVWp2X201810131916', 2, 11, '', -20.00, 1, 4, 1, 1539425884, NULL);
INSERT INTO `cl_money_detail` VALUES (771, 'RE14KVWp2X201810135734', 2, 11, '', -20.00, 1, 1, 1, 1539425997, NULL);
INSERT INTO `cl_money_detail` VALUES (772, 'RE14KVWp2X201810133080', 1, 11, '', 20.00, 1, 1, 1, 1539425999, NULL);
INSERT INTO `cl_money_detail` VALUES (773, 'RE14KVWp2X201810136135', 1, 11, '竞猜获胜,赢得20.00积分', 20.00, 2, 1, 1, 1539427128, NULL);
INSERT INTO `cl_money_detail` VALUES (774, 'RE7ZpMRBQx201810137774', 2, 25, '', -100.00, 1, 4, 1, 1539428780, NULL);
INSERT INTO `cl_money_detail` VALUES (775, 'RE7ZpMRBQx201810135242', 1, 25, '', 12.90, 1, 4, 1, 1539428784, NULL);
INSERT INTO `cl_money_detail` VALUES (776, 'RE7ZpMRBQx201810137979', 2, 25, '', -199.99, 8, 1, 1, 1539428821, NULL);
INSERT INTO `cl_money_detail` VALUES (777, 'RE201810149665', 1, NULL, '拍卖成功,获得拍卖价150000.00', 150000.00, 4, 1, 1, 1539486181, NULL);
INSERT INTO `cl_money_detail` VALUES (778, 'RE201810146055', 1, NULL, '拍卖成功,获得拍卖价1000001.00', 1000001.00, 4, 1, 1, 1539498301, NULL);
INSERT INTO `cl_money_detail` VALUES (779, 'RE201810142212', 1, NULL, '拍卖成功,获得拍卖价10000.00', 10000.00, 4, 1, 1, 1539498301, NULL);
INSERT INTO `cl_money_detail` VALUES (780, 'RE201810147044', 1, NULL, '拍卖成功,获得拍卖价20000.00', 20000.00, 4, 1, 1, 1539498301, NULL);
INSERT INTO `cl_money_detail` VALUES (781, 'RExkyO7yJ6201810145811', 1, 17, '红包未领完返还', 199.99, 7, 1, 1, 1539502501, NULL);
INSERT INTO `cl_money_detail` VALUES (782, 'RExkyO7yJ6201810148578', 1, 17, '红包未领完返还', 10.00, 7, 1, 1, 1539502561, NULL);
INSERT INTO `cl_money_detail` VALUES (783, 'RExkyO7yJ6201810148803', 1, 17, '红包未领完返还', 22.00, 7, 1, 1, 1539502681, NULL);
INSERT INTO `cl_money_detail` VALUES (784, 'RExkyO7yJ6201810145888', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539502921, NULL);
INSERT INTO `cl_money_detail` VALUES (785, 'RExkyO7yJ6201810149787', 1, 17, '红包未领完返还', 200.00, 7, 1, 1, 1539502982, NULL);
INSERT INTO `cl_money_detail` VALUES (786, 'RE1VKwmBQ3201810146234', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539503101, NULL);
INSERT INTO `cl_money_detail` VALUES (787, 'RExkyO7yJ6201810145765', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503101, NULL);
INSERT INTO `cl_money_detail` VALUES (788, 'RE1VKwmBQ3201810142843', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539503221, NULL);
INSERT INTO `cl_money_detail` VALUES (789, 'RExkyO7yJ6201810148330', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503221, NULL);
INSERT INTO `cl_money_detail` VALUES (790, 'RE1VKwmBQ3201810147847', 1, 14, '红包未领完返还', 2.00, 7, 1, 1, 1539503281, NULL);
INSERT INTO `cl_money_detail` VALUES (791, 'RE1VKwmBQ3201810146977', 1, 14, '红包未领完返还', 21.00, 7, 1, 1, 1539503281, NULL);
INSERT INTO `cl_money_detail` VALUES (792, 'RExkyO7yJ6201810149838', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503281, NULL);
INSERT INTO `cl_money_detail` VALUES (793, 'RExkyO7yJ6201810142967', 1, 17, '红包未领完返还', 200.00, 7, 1, 1, 1539503342, NULL);
INSERT INTO `cl_money_detail` VALUES (794, 'RExkyO7yJ6201810144819', 1, 17, '红包未领完返还', 19.99, 7, 1, 1, 1539503461, NULL);
INSERT INTO `cl_money_detail` VALUES (795, 'RExkyO7yJ6201810146969', 1, 17, '红包未领完返还', 10.00, 7, 1, 1, 1539503461, NULL);
INSERT INTO `cl_money_detail` VALUES (796, 'RExkyO7yJ6201810142832', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503581, NULL);
INSERT INTO `cl_money_detail` VALUES (797, 'RExkyO7yJ6201810146481', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503581, NULL);
INSERT INTO `cl_money_detail` VALUES (798, 'RExkyO7yJ6201810141701', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503641, NULL);
INSERT INTO `cl_money_detail` VALUES (799, 'RExkyO7yJ6201810142117', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539503881, NULL);
INSERT INTO `cl_money_detail` VALUES (800, 'RExkyO7yJ6201810145465', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539504241, NULL);
INSERT INTO `cl_money_detail` VALUES (801, 'RExkyO7yJ6201810148078', 1, 17, '红包未领完返还', 20.00, 7, 1, 1, 1539504301, NULL);
INSERT INTO `cl_money_detail` VALUES (802, 'RE1VKwmBQ3201810145475', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539504301, NULL);
INSERT INTO `cl_money_detail` VALUES (803, 'RE1VKwmBQ3201810146449', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539504421, NULL);
INSERT INTO `cl_money_detail` VALUES (804, 'RE1VKwmBQ3201810145982', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539504481, NULL);
INSERT INTO `cl_money_detail` VALUES (805, 'RE1VKwmBQ3201810143906', 1, 14, '红包未领完返还', 12.00, 7, 1, 1, 1539504601, NULL);
INSERT INTO `cl_money_detail` VALUES (806, 'RExkyO7yJ6201810148926', 1, 17, '红包未领完返还', 16.67, 7, 1, 1, 1539504661, NULL);
INSERT INTO `cl_money_detail` VALUES (807, 'RExkyO7yJ6201810141803', 1, 17, '红包未领完返还', 13.34, 7, 1, 1, 1539504661, NULL);
INSERT INTO `cl_money_detail` VALUES (808, 'RExkyO7yJ6201810141405', 1, 17, '红包未领完返还', 16.67, 7, 1, 1, 1539504722, NULL);
INSERT INTO `cl_money_detail` VALUES (809, 'RExkyO7yJ6201810141941', 1, 17, '红包未领完返还', 16.67, 7, 1, 1, 1539504722, NULL);
INSERT INTO `cl_money_detail` VALUES (810, 'REQLBRzB7n201810147324', 1, 33, '红包未领完返还', 166.67, 7, 1, 1, 1539506582, NULL);
INSERT INTO `cl_money_detail` VALUES (811, 'RELDBbxK5n201810148507', 1, 29, '红包未领完返还', 7677.78, 7, 1, 1, 1539507662, NULL);
INSERT INTO `cl_money_detail` VALUES (812, 'RELDBbxK5n201810146295', 1, 29, '红包未领完返还', 666.00, 7, 1, 1, 1539510421, NULL);
INSERT INTO `cl_money_detail` VALUES (813, 'RELDBbxK5n201810147358', 1, 29, '红包未领完返还', 777.00, 7, 1, 1, 1539510481, NULL);
INSERT INTO `cl_money_detail` VALUES (814, 'REQLBRzB7n201810149413', 1, 33, '红包未领完返还', 4444.00, 7, 1, 1, 1539510541, NULL);
INSERT INTO `cl_money_detail` VALUES (815, 'REQLBRzB7n201810145569', 1, 33, '红包未领完返还', 222.00, 7, 1, 1, 1539510601, NULL);
INSERT INTO `cl_money_detail` VALUES (816, 'REQLBRzB7n201810148208', 1, 33, '红包未领完返还', 222.00, 7, 1, 1, 1539510601, NULL);
INSERT INTO `cl_money_detail` VALUES (817, 'REQLBRzB7n201810149075', 1, 33, '红包未领完返还', 5555.00, 7, 1, 1, 1539510601, NULL);
INSERT INTO `cl_money_detail` VALUES (818, 'REQLBRzB7n201810143556', 1, 33, '红包未领完返还', 22.00, 7, 1, 1, 1539510601, NULL);
INSERT INTO `cl_money_detail` VALUES (819, 'RE14KVWp2X201810142266', 1, 11, '红包未领完返还', 0.01, 7, 1, 1, 1539510662, NULL);
INSERT INTO `cl_money_detail` VALUES (820, 'RE14KVWp2X201810146975', 1, 11, '红包未领完返还', 0.01, 7, 1, 1, 1539510721, NULL);
INSERT INTO `cl_money_detail` VALUES (821, 'RELDBbxK5n201810148887', 1, 29, '红包未领完返还', 6666.00, 7, 4, 1, 1539510721, NULL);
INSERT INTO `cl_money_detail` VALUES (822, 'RELDBbxK5n201810142295', 1, 29, '红包未领完返还', 888.00, 7, 1, 1, 1539510781, NULL);
INSERT INTO `cl_money_detail` VALUES (823, 'RELDBbxK5n201810144010', 1, 29, '红包未领完返还', 6666.00, 7, 1, 1, 1539510841, NULL);
INSERT INTO `cl_money_detail` VALUES (824, 'RELDBbxK5n201810145341', 1, 29, '红包未领完返还', 647.50, 7, 1, 1, 1539511921, NULL);
INSERT INTO `cl_money_detail` VALUES (825, 'RE14KVWp2X201810141301', 1, 11, '红包未领完返还', 20.00, 7, 1, 1, 1539512281, NULL);
INSERT INTO `cl_money_detail` VALUES (826, 'RE14KVWp2X201810149671', 1, 11, '红包未领完返还', 20.00, 7, 4, 1, 1539512281, NULL);
INSERT INTO `cl_money_detail` VALUES (827, 'RE14KVWp2X201810147740', 1, 11, '红包未领完返还', 20.00, 7, 4, 1, 1539512341, NULL);
INSERT INTO `cl_money_detail` VALUES (828, 'RE7ZpMRBQx201810142953', 1, 25, '红包未领完返还', 87.10, 7, 4, 1, 1539515221, NULL);
INSERT INTO `cl_money_detail` VALUES (829, 'RE14KVWp2X201810158303', 2, 11, '', -49.99, 8, 1, 1, 1539571658, NULL);
INSERT INTO `cl_money_detail` VALUES (830, 'RE14KVWp2X201810155172', 2, 11, '', -999.99, 8, 1, 1, 1539571662, NULL);
INSERT INTO `cl_money_detail` VALUES (831, 'REvqp4ZyMo201810153374', 2, 10, '', -214.00, 1, 1, 1, 1539572022, NULL);
INSERT INTO `cl_money_detail` VALUES (832, 'REvqp4ZyMo201810154925', 2, 10, '', -214.00, 1, 1, 1, 1539574689, NULL);
INSERT INTO `cl_money_detail` VALUES (833, 'REvqp4ZyMo201810157405', 1, 10, '', 142.66, 1, 1, 1, 1539574883, NULL);
INSERT INTO `cl_money_detail` VALUES (834, 'REvqp4ZyMo201810157593', 1, 10, '', 107.00, 1, 1, 1, 1539574890, NULL);
INSERT INTO `cl_money_detail` VALUES (835, 'REvqp4ZyMo201810157752', 2, 10, '', -236551.00, 1, 2, 1, 1539574947, NULL);
INSERT INTO `cl_money_detail` VALUES (836, 'REvqp4ZyMo201810154368', 2, 10, '', -236551.00, 1, 2, 1, 1539574950, NULL);
INSERT INTO `cl_money_detail` VALUES (837, 'REvqp4ZyMo201810153034', 2, 10, '', -236551.00, 1, 2, 1, 1539574958, NULL);
INSERT INTO `cl_money_detail` VALUES (838, 'REvqp4ZyMo201810151613', 1, 10, '', 13557.27, 1, 2, 1, 1539574960, NULL);
INSERT INTO `cl_money_detail` VALUES (839, 'REvqp4ZyMo201810154418', 2, 10, '', -234.00, 1, 2, 1, 1539575073, NULL);
INSERT INTO `cl_money_detail` VALUES (840, 'REvqp4ZyMo201810153174', 1, 10, '', 39.00, 1, 2, 1, 1539575078, NULL);
INSERT INTO `cl_money_detail` VALUES (841, 'RELbyJGK5d201810155348', 1, 6, '直播间付费', 0.00, 5, 1, 1, 1539585630, NULL);
INSERT INTO `cl_money_detail` VALUES (842, 'RELbyJGK5d201810153473', 1, 6, '直播间付费', 0.00, 5, 1, 1, 1539585636, NULL);
INSERT INTO `cl_money_detail` VALUES (843, 'RELbyJGK5d201810151442', 1, 6, '直播间付费', 0.00, 5, 1, 1, 1539585710, NULL);
INSERT INTO `cl_money_detail` VALUES (844, 'RELbyJGK5d201810158154', 2, 6, '直播间付费', -300.00, 5, 1, 1, 1539585750, NULL);
INSERT INTO `cl_money_detail` VALUES (845, 'REvqp4ZyMo201810155549', 2, 10, '直播间付费', -100.00, 5, 1, 1, 1539586161, NULL);
INSERT INTO `cl_money_detail` VALUES (846, 'REvqp4ZyMo201810157752', 2, 10, '', -511.00, 1, 1, 1, 1539588992, NULL);
INSERT INTO `cl_money_detail` VALUES (847, 'REvqp4ZyMo201810156904', 2, 10, '', -123.00, 1, 1, 1, 1539590283, NULL);
INSERT INTO `cl_money_detail` VALUES (848, 'REjGBP6KXA201810159545', 1, 3, '', 61.50, 1, 1, 1, 1539590289, NULL);
INSERT INTO `cl_money_detail` VALUES (849, 'RE14KVWp2X201810154480', 1, 11, '竞猜获胜,赢得0.20积分', 0.20, 2, 1, 1, 1539595258, NULL);
INSERT INTO `cl_money_detail` VALUES (850, 'RE14KVWp2X201810154077', 2, 11, '', -19.99, 8, 1, 1, 1539596080, NULL);
INSERT INTO `cl_money_detail` VALUES (851, 'RE14KVWp2X201810152799', 2, 11, '', -49.99, 8, 1, 1, 1539596084, NULL);
INSERT INTO `cl_money_detail` VALUES (852, 'RE14KVWp2X201810155153', 2, 11, '', -1999.00, 8, 1, 1, 1539596090, NULL);
INSERT INTO `cl_money_detail` VALUES (853, 'RE14KVWp2X201810155259', 2, 11, '', -49.99, 8, 1, 1, 1539596102, NULL);
INSERT INTO `cl_money_detail` VALUES (854, 'RE14KVWp2X201810156231', 2, 11, '', -1999.00, 8, 1, 1, 1539596270, NULL);
INSERT INTO `cl_money_detail` VALUES (855, 'RE14KVWp2X201810158113', 2, 11, '', -2.00, 1, 4, 1, 1539596325, NULL);
INSERT INTO `cl_money_detail` VALUES (856, 'RE14KVWp2X201810152067', 1, 11, '', 1.00, 1, 4, 1, 1539596328, NULL);
INSERT INTO `cl_money_detail` VALUES (857, 'RExkyO7yJ6201810154843', 1, 17, '', 1.00, 1, 4, 1, 1539596450, NULL);
INSERT INTO `cl_money_detail` VALUES (858, 'RE14KVWp2X201810159231', 2, 11, '', -19.99, 8, 1, 1, 1539596510, NULL);
INSERT INTO `cl_money_detail` VALUES (859, 'RE14KVWp2X201810153086', 2, 11, '', -19.99, 8, 1, 1, 1539596800, NULL);
INSERT INTO `cl_money_detail` VALUES (860, 'RE14KVWp2X201810154320', 2, 11, '', -19.99, 8, 1, 1, 1539597007, NULL);
INSERT INTO `cl_money_detail` VALUES (861, 'RE14KVWp2X201810159807', 2, 11, '拍卖房间拍卖加价成功，冻结竞拍金额 ', -100200.00, 4, 1, 1, 1539597075, NULL);
INSERT INTO `cl_money_detail` VALUES (862, 'RELbyJGK5d201810157266', 2, 6, '', -9.99, 8, 1, 1, 1539597845, NULL);
INSERT INTO `cl_money_detail` VALUES (863, 'RELbyJGK5d201810161525', 2, 6, '直播间升级付费', -1000.00, 7, 1, 1, 1539656294, NULL);
INSERT INTO `cl_money_detail` VALUES (864, 'REvqp4ZyMo201810163926', 1, 10, '红包未领完返还', 107.00, 7, 1, 1, 1539658441, NULL);
INSERT INTO `cl_money_detail` VALUES (865, 'REvqp4ZyMo201810169425', 1, 10, '红包未领完返还', 71.34, 7, 1, 1, 1539661141, NULL);
INSERT INTO `cl_money_detail` VALUES (866, 'REvqp4ZyMo201810167686', 1, 10, '红包未领完返还', 236551.00, 7, 2, 1, 1539661381, NULL);
INSERT INTO `cl_money_detail` VALUES (867, 'REvqp4ZyMo201810163826', 1, 10, '红包未领完返还', 222993.73, 7, 2, 1, 1539661381, NULL);
INSERT INTO `cl_money_detail` VALUES (868, 'REvqp4ZyMo201810169853', 1, 10, '红包未领完返还', 236551.00, 7, 2, 1, 1539661381, NULL);
INSERT INTO `cl_money_detail` VALUES (869, 'REvqp4ZyMo201810161949', 1, 10, '红包未领完返还', 195.00, 7, 2, 1, 1539661501, NULL);
INSERT INTO `cl_money_detail` VALUES (870, 'REvqp4ZyMo201810163834', 1, 10, '红包未领完返还', 511.00, 7, 1, 1, 1539675421, NULL);
INSERT INTO `cl_money_detail` VALUES (871, 'REvqp4ZyMo201810169434', 1, 10, '红包未领完返还', 61.50, 7, 1, 1, 1539676741, NULL);
INSERT INTO `cl_money_detail` VALUES (872, 'RE201810161175', 1, NULL, '拍卖成功,获得拍卖价100200.00', 100200.00, 4, 1, 1, 1539683521, NULL);
INSERT INTO `cl_money_detail` VALUES (873, 'RE7ZpMRBQx201810172387', 2, 25, '拍卖角色u21767加价成功，冻结竞拍金额 ', -11000.00, 4, 4, 1, 1539730653, NULL);
INSERT INTO `cl_money_detail` VALUES (874, 'RELDBbxK5n201810171087', 2, 29, '拍卖房间欢乐的区块链加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1539760177, NULL);
INSERT INTO `cl_money_detail` VALUES (875, 'RE7ZpMRBQx201810175699', 1, 25, '拍卖角色u21767竞拍价格被超越，返还冻结金额', 11000.00, 4, 1, 1, 1539760956, NULL);
INSERT INTO `cl_money_detail` VALUES (876, 'RELDBbxK5n201810172586', 2, 29, '拍卖角色u21767加价成功，冻结竞拍金额 ', -13000.00, 4, 1, 1, 1539760956, NULL);
INSERT INTO `cl_money_detail` VALUES (877, 'RExkyO7yJ6201810174099', 2, 17, '', -200.00, 1, 1, 1, 1539761558, NULL);
INSERT INTO `cl_money_detail` VALUES (878, 'RExkyO7yJ6201810175691', 1, 17, '', 0.63, 1, 1, 1, 1539761562, NULL);
INSERT INTO `cl_money_detail` VALUES (879, 'RELDBbxK5n201810176997', 1, 29, '', 1.80, 1, 1, 1, 1539761770, NULL);
INSERT INTO `cl_money_detail` VALUES (880, 'RELDBbxK5n201810176748', 2, 29, '', -9.99, 8, 1, 1, 1539762404, NULL);
INSERT INTO `cl_money_detail` VALUES (881, 'RELDBbxK5n201810176280', 2, 29, '', -19.99, 8, 1, 1, 1539762407, NULL);
INSERT INTO `cl_money_detail` VALUES (882, 'RELDBbxK5n201810179981', 2, 29, '', -49.99, 8, 1, 1, 1539762411, NULL);
INSERT INTO `cl_money_detail` VALUES (883, 'RELDBbxK5n201810177137', 2, 29, '', -9.99, 8, 1, 1, 1539762413, NULL);
INSERT INTO `cl_money_detail` VALUES (884, 'RELDBbxK5n201810177348', 2, 29, '', -19.99, 8, 1, 1, 1539764136, NULL);
INSERT INTO `cl_money_detail` VALUES (885, 'RELDBbxK5n201810174353', 2, 29, '', -19.99, 8, 1, 1, 1539764143, NULL);
INSERT INTO `cl_money_detail` VALUES (886, 'RELDBbxK5n201810178663', 2, 29, '', -19.99, 8, 1, 1, 1539764145, NULL);
INSERT INTO `cl_money_detail` VALUES (887, 'RELDBbxK5n201810177960', 2, 29, '', -19.99, 8, 1, 1, 1539764146, NULL);
INSERT INTO `cl_money_detail` VALUES (888, 'RELDBbxK5n201810175809', 2, 29, '', -19.99, 8, 1, 1, 1539764153, NULL);
INSERT INTO `cl_money_detail` VALUES (889, 'RELDBbxK5n201810178220', 2, 29, '', -1000.00, 1, 1, 1, 1539766168, NULL);
INSERT INTO `cl_money_detail` VALUES (890, 'RELDBbxK5n201810176073', 1, 29, '', 200.00, 1, 1, 1, 1539766185, NULL);
INSERT INTO `cl_money_detail` VALUES (891, 'RELDBbxK5n201810176751', 2, 29, '', -2222.00, 1, 1, 1, 1539766370, NULL);
INSERT INTO `cl_money_detail` VALUES (892, 'RELDBbxK5n201810174209', 1, 29, '', 555.50, 1, 1, 1, 1539766373, NULL);
INSERT INTO `cl_money_detail` VALUES (893, 'RExkyO7yJ6201810175483', 2, 17, '直播间付费', -100.00, 5, 1, 1, 1539768011, NULL);
INSERT INTO `cl_money_detail` VALUES (894, 'RExkyO7yJ6201810172428', 2, 17, '拍卖角色啊点击倒萨大顶的顶顶顶顶顶顶顶顶顶顶的顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶顶师大师大大大是到达大师的圣萨达四大四大诞节阿阿斯顿的加价成功，冻结竞拍金额 ', -123456.00, 4, 4, 1, 1539768809, NULL);
INSERT INTO `cl_money_detail` VALUES (895, 'RExkyO7yJ6201810172320', 1, 17, '', 559.89, 3, 1, 1, 1539769259, NULL);
INSERT INTO `cl_money_detail` VALUES (896, 'RELDBbxK5n201810178884', 2, 29, '', -22.00, 1, 1, 1, 1539770551, NULL);
INSERT INTO `cl_money_detail` VALUES (897, 'RELDBbxK5n201810178171', 2, 29, '', -100.00, 1, 1, 1, 1539770585, NULL);
INSERT INTO `cl_money_detail` VALUES (898, 'RE201810189140', 1, NULL, '拍卖成功,获得拍卖价13000.00', 13000.00, 4, 1, 1, 1539817081, NULL);
INSERT INTO `cl_money_detail` VALUES (899, 'RELDBbxK5n201810185433', 2, 29, '', -199.99, 8, 1, 1, 1539832393, NULL);
INSERT INTO `cl_money_detail` VALUES (900, 'RELDBbxK5n201810187604', 2, 29, '', -199.99, 8, 1, 1, 1539832396, NULL);
INSERT INTO `cl_money_detail` VALUES (901, 'RELDBbxK5n201810187006', 2, 29, '', -200.00, 1, 1, 1, 1539842362, NULL);
INSERT INTO `cl_money_detail` VALUES (902, 'RE14KVWp2X201810189808', 2, 11, '', -2000.00, 1, 4, 1, 1539842366, NULL);
INSERT INTO `cl_money_detail` VALUES (903, 'RE14KVWp2X201810182317', 1, 11, '', 2000.00, 1, 4, 1, 1539842369, NULL);
INSERT INTO `cl_money_detail` VALUES (904, 'RE14KVWp2X201810185480', 2, 11, '', -20.00, 1, 1, 1, 1539842388, NULL);
INSERT INTO `cl_money_detail` VALUES (905, 'RE14KVWp2X201810188253', 1, 11, '', 19.99, 1, 1, 1, 1539842390, NULL);
INSERT INTO `cl_money_detail` VALUES (906, 'RE14KVWp2X201810188349', 2, 11, '', -19.99, 8, 1, 1, 1539842404, NULL);
INSERT INTO `cl_money_detail` VALUES (907, 'RE14KVWp2X201810187450', 2, 11, '', -19.99, 8, 1, 1, 1539842408, NULL);
INSERT INTO `cl_money_detail` VALUES (908, 'RE14KVWp2X201810188752', 2, 11, '', -19.99, 8, 1, 1, 1539842413, NULL);
INSERT INTO `cl_money_detail` VALUES (909, 'RE14KVWp2X201810182729', 2, 11, '', -20.00, 1, 1, 1, 1539843551, NULL);
INSERT INTO `cl_money_detail` VALUES (910, 'RE14KVWp2X201810185918', 1, 11, '', 10.00, 1, 1, 1, 1539843553, NULL);
INSERT INTO `cl_money_detail` VALUES (911, 'RELDBbxK5n201810184969', 1, 29, '', 10.00, 1, 1, 1, 1539843559, NULL);
INSERT INTO `cl_money_detail` VALUES (912, 'RE14KVWp2X201810185747', 2, 11, '', -10.00, 1, 1, 1, 1539843579, NULL);
INSERT INTO `cl_money_detail` VALUES (913, 'RE14KVWp2X201810189218', 1, 11, '', 0.01, 1, 1, 1, 1539843583, NULL);
INSERT INTO `cl_money_detail` VALUES (914, 'RELDBbxK5n201810189037', 1, 29, '', 0.01, 1, 1, 1, 1539843583, NULL);
INSERT INTO `cl_money_detail` VALUES (915, 'RELDBbxK5n201810185891', 2, 29, '', -100.00, 1, 1, 1, 1539843669, NULL);
INSERT INTO `cl_money_detail` VALUES (916, 'RELDBbxK5n201810183740', 2, 29, '', -100.00, 1, 1, 1, 1539843697, NULL);
INSERT INTO `cl_money_detail` VALUES (917, 'RELDBbxK5n201810188863', 2, 29, '', -8.00, 1, 1, 1, 1539843712, NULL);
INSERT INTO `cl_money_detail` VALUES (918, 'RELDBbxK5n201810188027', 2, 29, '', -7.00, 1, 1, 1, 1539843763, NULL);
INSERT INTO `cl_money_detail` VALUES (919, 'RE14KVWp2X201810182913', 1, 11, '竞猜获胜,赢得1.14积分', 1.14, 2, 1, 1, 1539844198, NULL);
INSERT INTO `cl_money_detail` VALUES (920, 'RELDBbxK5n201810186326', 2, 29, '', -100.00, 1, 1, 1, 1539844601, NULL);
INSERT INTO `cl_money_detail` VALUES (921, 'REQLBRzB7n201810183725', 1, 33, '', 100.00, 1, 1, 1, 1539844618, NULL);
INSERT INTO `cl_money_detail` VALUES (922, 'RE14KVWp2X201810187377', 2, 11, '', -50.00, 1, 1, 1, 1539844622, NULL);
INSERT INTO `cl_money_detail` VALUES (923, 'RELDBbxK5n201810181316', 2, 29, '', -2.00, 1, 1, 1, 1539845015, NULL);
INSERT INTO `cl_money_detail` VALUES (924, 'REQLBRzB7n201810182988', 1, 33, '', 2.00, 1, 1, 1, 1539845026, NULL);
INSERT INTO `cl_money_detail` VALUES (925, 'RE14KVWp2X201810187882', 1, 11, '', 8.33, 1, 1, 1, 1539845109, NULL);
INSERT INTO `cl_money_detail` VALUES (926, 'REQLBRzB7n201810183995', 1, 33, '', 555.50, 1, 1, 1, 1539845259, NULL);
INSERT INTO `cl_money_detail` VALUES (927, 'RE201810187504', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1539846602, NULL);
INSERT INTO `cl_money_detail` VALUES (928, 'RELbyJGK5d201810184284', 2, 6, '', -200.00, 1, 1, 1, 1539846750, NULL);
INSERT INTO `cl_money_detail` VALUES (929, 'RELbyJGK5d201810183457', 1, 6, '', 200.00, 1, 1, 1, 1539846759, NULL);
INSERT INTO `cl_money_detail` VALUES (930, 'RExkyO7yJ6201810186987', 2, 17, '', -9.99, 8, 1, 1, 1539847005, NULL);
INSERT INTO `cl_money_detail` VALUES (931, 'RExkyO7yJ6201810188568', 2, 17, '', -19.99, 8, 1, 1, 1539847009, NULL);
INSERT INTO `cl_money_detail` VALUES (932, 'RExkyO7yJ6201810185014', 2, 17, '', -49.99, 8, 1, 1, 1539847014, NULL);
INSERT INTO `cl_money_detail` VALUES (933, 'RExkyO7yJ6201810189667', 1, 17, '红包未领完返还', 197.57, 7, 1, 1, 1539847981, NULL);
INSERT INTO `cl_money_detail` VALUES (934, 'RELDBbxK5n201810185479', 2, 29, '', -20.00, 1, 1, 1, 1539848001, NULL);
INSERT INTO `cl_money_detail` VALUES (935, 'RELDBbxK5n201810183655', 1, 29, '', 20.00, 1, 1, 1, 1539848003, NULL);
INSERT INTO `cl_money_detail` VALUES (936, 'RELDBbxK5n201810183681', 2, 29, '', -19.99, 8, 1, 1, 1539848012, NULL);
INSERT INTO `cl_money_detail` VALUES (937, 'RELDBbxK5n201810181952', 2, 29, '', -19.99, 8, 1, 1, 1539848017, NULL);
INSERT INTO `cl_money_detail` VALUES (938, 'RExkyO7yJ6201810188425', 2, 17, '', -19.99, 8, 1, 1, 1539849346, NULL);
INSERT INTO `cl_money_detail` VALUES (939, 'RExkyO7yJ6201810181190', 2, 17, '', -19.99, 8, 1, 1, 1539849349, NULL);
INSERT INTO `cl_money_detail` VALUES (940, 'RE14KVWp2X201810185654', 2, 11, '', -20.00, 1, 1, 1, 1539849383, NULL);
INSERT INTO `cl_money_detail` VALUES (941, 'RE14KVWp2X201810181210', 1, 11, '', 20.00, 1, 1, 1, 1539849385, NULL);
INSERT INTO `cl_money_detail` VALUES (942, 'RE14KVWp2X201810187722', 1, 11, '', 111.95, 3, 1, 1, 1539849516, NULL);
INSERT INTO `cl_money_detail` VALUES (943, 'RExkyO7yJ6201810188475', 2, 17, '', -9.99, 8, 1, 1, 1539850930, NULL);
INSERT INTO `cl_money_detail` VALUES (944, 'RExkyO7yJ6201810183836', 2, 17, '', -9.99, 8, 1, 1, 1539850932, NULL);
INSERT INTO `cl_money_detail` VALUES (945, 'RELDBbxK5n201810184004', 1, 29, '红包未领完返还', 800.00, 7, 1, 1, 1539852601, NULL);
INSERT INTO `cl_money_detail` VALUES (946, 'RELDBbxK5n201810183949', 1, 29, '红包未领完返还', 1111.00, 7, 1, 1, 1539852781, NULL);
INSERT INTO `cl_money_detail` VALUES (947, 'RE201810188790', 1, NULL, '拍卖成功,获得拍卖价123456.00', 123456.00, 4, 1, 1, 1539855241, NULL);
INSERT INTO `cl_money_detail` VALUES (948, 'RELDBbxK5n201810187798', 1, 29, '红包未领完返还', 22.00, 7, 1, 1, 1539856981, NULL);
INSERT INTO `cl_money_detail` VALUES (949, 'RELDBbxK5n201810183334', 1, 29, '红包未领完返还', 100.00, 7, 1, 1, 1539857041, NULL);
INSERT INTO `cl_money_detail` VALUES (950, 'RE14KVWp2X201810196235', 2, 11, '', -10.00, 1, 1, 1, 1539919520, NULL);
INSERT INTO `cl_money_detail` VALUES (951, 'RE14KVWp2X201810191755', 1, 11, '', 10.00, 1, 1, 1, 1539919524, NULL);
INSERT INTO `cl_money_detail` VALUES (952, 'RELbyJGK5d201810193892', 2, 6, '', -9.99, 8, 1, 1, 1539920416, NULL);
INSERT INTO `cl_money_detail` VALUES (953, 'RELbyJGK5d201810199695', 2, 6, '', -999.99, 8, 1, 1, 1539921232, NULL);
INSERT INTO `cl_money_detail` VALUES (954, 'RELNyznyXM201810191753', 2, 18, '', -999.99, 8, 1, 1, 1539921273, NULL);
INSERT INTO `cl_money_detail` VALUES (955, 'RELbyJGK5d201810198762', 2, 6, '', -9.99, 8, 1, 1, 1539921483, NULL);
INSERT INTO `cl_money_detail` VALUES (956, 'RELbyJGK5d201810198836', 2, 6, '', -100.00, 1, 1, 1, 1539921528, NULL);
INSERT INTO `cl_money_detail` VALUES (957, 'RELbyJGK5d201810199582', 2, 6, '', -10.00, 1, 1, 1, 1539921670, NULL);
INSERT INTO `cl_money_detail` VALUES (958, 'RELDBbxK5n201810191879', 1, 29, '红包未领完返还', 200.00, 7, 1, 1, 1539928802, NULL);
INSERT INTO `cl_money_detail` VALUES (959, 'RE14KVWp2X201810196631', 1, 11, '红包未领完返还', 0.01, 7, 1, 1, 1539928802, NULL);
INSERT INTO `cl_money_detail` VALUES (960, 'RE14KVWp2X201810195649', 1, 11, '红包未领完返还', 9.98, 7, 1, 1, 1539930001, NULL);
INSERT INTO `cl_money_detail` VALUES (961, 'RELDBbxK5n201810199658', 1, 29, '红包未领完返还', 100.00, 7, 1, 1, 1539930121, NULL);
INSERT INTO `cl_money_detail` VALUES (962, 'RELDBbxK5n201810192539', 1, 29, '红包未领完返还', 100.00, 7, 1, 1, 1539930121, NULL);
INSERT INTO `cl_money_detail` VALUES (963, 'RELDBbxK5n201810196734', 1, 29, '红包未领完返还', 8.00, 7, 1, 1, 1539930121, NULL);
INSERT INTO `cl_money_detail` VALUES (964, 'RELDBbxK5n201810193150', 1, 29, '红包未领完返还', 7.00, 7, 1, 1, 1539930181, NULL);
INSERT INTO `cl_money_detail` VALUES (965, 'RELbyJGK5d201810199388', 2, 6, '', -10.00, 1, 1, 1, 1539930782, NULL);
INSERT INTO `cl_money_detail` VALUES (966, 'RELNyznyXM201810196351', 2, 18, '', -999.99, 8, 1, 1, 1539930785, NULL);
INSERT INTO `cl_money_detail` VALUES (967, 'RELbyJGK5d201810199533', 2, 6, '', -10.00, 1, 1, 1, 1539930905, NULL);
INSERT INTO `cl_money_detail` VALUES (968, 'RELbyJGK5d201810199316', 2, 6, '', -9.99, 8, 1, 1, 1539930936, NULL);
INSERT INTO `cl_money_detail` VALUES (969, 'RE14KVWp2X201810194443', 1, 11, '红包未领完返还', 41.67, 7, 1, 1, 1539931081, NULL);
INSERT INTO `cl_money_detail` VALUES (970, 'RELbyJGK5d201810197410', 2, 6, '', -10.00, 1, 1, 1, 1539931101, NULL);
INSERT INTO `cl_money_detail` VALUES (971, 'RELbyJGK5d201810195767', 1, 6, '', 10.00, 1, 1, 1, 1539931114, NULL);
INSERT INTO `cl_money_detail` VALUES (972, 'RE14KVWp2X201810198874', 2, 11, '', -123456.00, 1, 1, 1, 1539933733, NULL);
INSERT INTO `cl_money_detail` VALUES (973, 'RE14KVWp2X201810199877', 1, 11, '', 10288.00, 1, 1, 1, 1539933735, NULL);
INSERT INTO `cl_money_detail` VALUES (974, 'RE14KVWp2X201810191724', 2, 11, '', -10.00, 1, 1, 1, 1539933797, NULL);
INSERT INTO `cl_money_detail` VALUES (975, 'RE14KVWp2X201810191139', 1, 11, '', 10.00, 1, 1, 1, 1539933799, NULL);
INSERT INTO `cl_money_detail` VALUES (976, 'RE14KVWp2X201810198950', 2, 11, '', -19.99, 8, 1, 1, 1539933808, NULL);
INSERT INTO `cl_money_detail` VALUES (977, 'RE14KVWp2X201810194407', 2, 11, '', -19.99, 8, 1, 1, 1539933854, NULL);
INSERT INTO `cl_money_detail` VALUES (978, 'RE14KVWp2X201810197034', 2, 11, '', -19.99, 8, 1, 1, 1539933879, NULL);
INSERT INTO `cl_money_detail` VALUES (979, 'RELDBbxK5n201810191366', 2, 29, '', -199.99, 8, 1, 1, 1539934113, NULL);
INSERT INTO `cl_money_detail` VALUES (980, 'RELDBbxK5n201810197133', 2, 29, '', -2.00, 1, 1, 1, 1539935027, NULL);
INSERT INTO `cl_money_detail` VALUES (981, 'RELDBbxK5n201810199223', 2, 29, '', -55.00, 1, 1, 1, 1539935132, NULL);
INSERT INTO `cl_money_detail` VALUES (982, 'RELDBbxK5n201810197225', 2, 29, '', -999.99, 8, 1, 1, 1539935262, NULL);
INSERT INTO `cl_money_detail` VALUES (983, 'RELDBbxK5n201810199961', 2, 29, '', -99.99, 8, 1, 1, 1539935268, NULL);
INSERT INTO `cl_money_detail` VALUES (984, 'RELDBbxK5n201810198016', 2, 29, '', -77.00, 1, 1, 1, 1539935293, NULL);
INSERT INTO `cl_money_detail` VALUES (985, 'RELDBbxK5n201810194827', 1, 29, '', 12.84, 1, 1, 1, 1539935309, NULL);
INSERT INTO `cl_money_detail` VALUES (986, 'RELDBbxK5n201810194594', 1, 29, '', 2.00, 1, 1, 1, 1539936818, NULL);
INSERT INTO `cl_money_detail` VALUES (987, 'RELDBbxK5n201810191697', 2, 29, '', -333.00, 1, 1, 1, 1539937204, NULL);
INSERT INTO `cl_money_detail` VALUES (988, 'RE14KVWp2X201810195354', 1, 11, '', 166.50, 1, 1, 1, 1539937208, NULL);
INSERT INTO `cl_money_detail` VALUES (989, 'RELDBbxK5n201810192949', 2, 29, '', -33.00, 1, 1, 1, 1539937262, NULL);
INSERT INTO `cl_money_detail` VALUES (990, 'RELDBbxK5n201810191358', 1, 29, '', 16.50, 1, 1, 1, 1539937266, NULL);
INSERT INTO `cl_money_detail` VALUES (991, 'RELDBbxK5n201810196113', 2, 29, '', -8.00, 1, 1, 1, 1539937296, NULL);
INSERT INTO `cl_money_detail` VALUES (992, 'RELDBbxK5n201810192437', 1, 29, '', 4.00, 1, 1, 1, 1539937299, NULL);
INSERT INTO `cl_money_detail` VALUES (993, 'RE14KVWp2X201810199843', 1, 11, '', 4.00, 1, 1, 1, 1539937304, NULL);
INSERT INTO `cl_money_detail` VALUES (994, 'RE14KVWp2X201810195655', 1, 11, '', 16.50, 1, 1, 1, 1539937307, NULL);
INSERT INTO `cl_money_detail` VALUES (995, 'RELDBbxK5n201810198477', 2, 29, '', -66.00, 1, 1, 1, 1539938007, NULL);
INSERT INTO `cl_money_detail` VALUES (996, 'RExkyO7yJ6201810192532', 2, 17, '', -19.99, 8, 1, 1, 1539938959, NULL);
INSERT INTO `cl_money_detail` VALUES (997, 'RExkyO7yJ6201810194518', 2, 17, '', -19.99, 8, 1, 1, 1539938960, NULL);
INSERT INTO `cl_money_detail` VALUES (998, 'RExkyO7yJ6201810199549', 2, 17, '', -49.99, 8, 1, 1, 1539938967, NULL);
INSERT INTO `cl_money_detail` VALUES (999, 'RE14KVWp2X201810192000', 2, 11, '', -20.00, 1, 1, 1, 1539938976, NULL);
INSERT INTO `cl_money_detail` VALUES (1000, 'RE14KVWp2X201810192221', 1, 11, '', 20.00, 1, 1, 1, 1539938978, NULL);
INSERT INTO `cl_money_detail` VALUES (1001, 'RE14KVWp2X201810195688', 2, 11, '', -20.00, 1, 1, 1, 1539939045, NULL);
INSERT INTO `cl_money_detail` VALUES (1002, 'RE14KVWp2X201810192568', 1, 11, '', 20.00, 1, 1, 1, 1539939047, NULL);
INSERT INTO `cl_money_detail` VALUES (1003, 'RELbyJGK5d201810191686', 2, 6, '', -346.00, 1, 1, 1, 1539943724, NULL);
INSERT INTO `cl_money_detail` VALUES (1004, 'RELDBbxK5n201810196396', 2, 29, '', -999.99, 8, 1, 1, 1539943739, NULL);
INSERT INTO `cl_money_detail` VALUES (1005, 'REX7yoXK1A201810194018', 2, 1, '', -199.99, 8, 1, 1, 1539956480, NULL);
INSERT INTO `cl_money_detail` VALUES (1006, 'RELbyJGK5d201810207226', 1, 6, '红包未领完返还', 100.00, 7, 1, 1, 1540007941, NULL);
INSERT INTO `cl_money_detail` VALUES (1007, 'RELbyJGK5d201810204921', 1, 6, '红包未领完返还', 10.00, 7, 1, 1, 1540008121, NULL);
INSERT INTO `cl_money_detail` VALUES (1008, 'REX7yoXXK1201810204263', 1, 45, '直播间付费', 0.00, 5, 1, 1, 1540015437, NULL);
INSERT INTO `cl_money_detail` VALUES (1009, 'RELbyJGK5d201810202745', 1, 6, '红包未领完返还', 10.00, 7, 1, 1, 1540017241, NULL);
INSERT INTO `cl_money_detail` VALUES (1010, 'RELbyJGK5d201810205607', 1, 6, '红包未领完返还', 10.00, 7, 1, 1, 1540017361, NULL);
INSERT INTO `cl_money_detail` VALUES (1011, 'RE14KVWp2X201810201185', 1, 11, '红包未领完返还', 113168.00, 7, 1, 1, 1540020181, NULL);
INSERT INTO `cl_money_detail` VALUES (1012, 'RELDBbxK5n201810209371', 1, 29, '红包未领完返还', 55.00, 7, 1, 1, 1540021561, NULL);
INSERT INTO `cl_money_detail` VALUES (1013, 'RELDBbxK5n201810208574', 1, 29, '红包未领完返还', 64.16, 7, 1, 1, 1540021742, NULL);
INSERT INTO `cl_money_detail` VALUES (1014, 'RELDBbxK5n201810202503', 1, 29, '红包未领完返还', 166.50, 7, 1, 1, 1540023662, NULL);
INSERT INTO `cl_money_detail` VALUES (1015, 'RELDBbxK5n201810201373', 1, 29, '红包未领完返还', 66.00, 7, 1, 1, 1540024442, NULL);
INSERT INTO `cl_money_detail` VALUES (1016, 'RELDBbxK5n201810203774', 1, 29, '拍卖成功,获得拍卖价123456.00', 123456.00, 4, 1, 1, 1540027021, NULL);
INSERT INTO `cl_money_detail` VALUES (1017, 'RELbyJGK5d201810201635', 1, 6, '拍卖成功,获得拍卖价10.00', 10.00, 4, 1, 1, 1540027321, NULL);
INSERT INTO `cl_money_detail` VALUES (1018, 'RELbyJGK5d201810205356', 1, 6, '红包未领完返还', 346.00, 7, 1, 1, 1540030141, NULL);
INSERT INTO `cl_money_detail` VALUES (1019, 'REjGBP6KXA201810233102', 1, 3, '拍卖成功,获得拍卖价7500.00', 7500.00, 4, 1, 1, 1540288321, NULL);
INSERT INTO `cl_money_detail` VALUES (1020, 'REX7yoXK1A201810248498', 2, 1, '直播间付费', -600.00, 5, 1, 1, 1540381124, NULL);
INSERT INTO `cl_money_detail` VALUES (1021, 'REjGBP6KXA201810249306', 2, 3, '拍卖房间谢谢学长加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1540381152, NULL);
INSERT INTO `cl_money_detail` VALUES (1022, 'RE3oynryJV201810242715', 2, 38, '拍卖房间啊啊啊加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1540381176, NULL);
INSERT INTO `cl_money_detail` VALUES (1023, 'REvqp4ZyMo201810245762', 2, 10, '', -100.00, 1, 1, 1, 1540382164, NULL);
INSERT INTO `cl_money_detail` VALUES (1024, 'RELbyJGK5d201810241380', 2, 6, '', -10.00, 1, 1, 1, 1540382224, NULL);
INSERT INTO `cl_money_detail` VALUES (1025, 'REvqp4ZyMo201810241029', 2, 10, '', -100.00, 1, 1, 1, 1540382226, NULL);
INSERT INTO `cl_money_detail` VALUES (1026, 'REvqp4ZyMo201810248184', 2, 10, '', -100.00, 1, 4, 1, 1540382237, NULL);
INSERT INTO `cl_money_detail` VALUES (1027, 'RELbyJGK5d201810246496', 2, 6, '', -10.00, 1, 1, 1, 1540382272, NULL);
INSERT INTO `cl_money_detail` VALUES (1028, 'REvqp4ZyMo201810241734', 1, 10, '', 34.00, 1, 4, 1, 1540382302, NULL);
INSERT INTO `cl_money_detail` VALUES (1029, 'REvqp4ZyMo201810245749', 2, 10, '', -1234.00, 1, 1, 1, 1540382840, NULL);
INSERT INTO `cl_money_detail` VALUES (1030, 'REjGBP6KXA201810244701', 1, 3, '', 205.66, 1, 1, 1, 1540382842, NULL);
INSERT INTO `cl_money_detail` VALUES (1031, 'RELbyJGK5d201810247570', 1, 6, '', 205.66, 1, 1, 1, 1540382844, NULL);
INSERT INTO `cl_money_detail` VALUES (1032, 'RE3oynryJV201810241459', 2, 38, '', -1000.00, 1, 1, 1, 1540383181, NULL);
INSERT INTO `cl_money_detail` VALUES (1033, 'REjGBP6KXA201810255464', 2, 3, '', -100.00, 4, 1, 1, 1540451797, NULL);
INSERT INTO `cl_money_detail` VALUES (1034, 'REjGBP6KXA201810257506', 1, 3, '', 33.34, 1, 4, 1, 1540451800, NULL);
INSERT INTO `cl_money_detail` VALUES (1035, 'REjGBP6KXA201810256989', 2, 3, '', -100.00, 4, 1, 1, 1540451813, NULL);
INSERT INTO `cl_money_detail` VALUES (1036, 'REjGBP6KXA201810255641', 1, 3, '', 33.33, 1, 4, 1, 1540451816, NULL);
INSERT INTO `cl_money_detail` VALUES (1037, 'REjGBP6KXA201810256421', 2, 3, '', -0.02, 4, 1, 1, 1540451869, NULL);
INSERT INTO `cl_money_detail` VALUES (1038, 'REjGBP6KXA201810256168', 1, 3, '', 0.01, 1, 4, 1, 1540451871, NULL);
INSERT INTO `cl_money_detail` VALUES (1039, 'REvqp4ZyMo201810254693', 2, 10, '', -235.00, 1, 1, 1, 1540452026, NULL);
INSERT INTO `cl_money_detail` VALUES (1040, 'REjGBP6KXA201810257576', 2, 3, '', 0.00, 4, 1, 1, 1540452105, NULL);
INSERT INTO `cl_money_detail` VALUES (1041, 'REjGBP6KXA201810254146', 1, 3, '', 0.00, 1, 4, 1, 1540452107, NULL);
INSERT INTO `cl_money_detail` VALUES (1042, 'REvqp4ZyMo201810253174', 2, 10, '', -234.00, 1, 1, 1, 1540452378, NULL);
INSERT INTO `cl_money_detail` VALUES (1043, 'REjGBP6KXA201810259772', 2, 3, '', -5.00, 1, 1, 1, 1540452380, NULL);
INSERT INTO `cl_money_detail` VALUES (1044, 'REjGBP6KXA201810254186', 1, 3, '', 4.99, 1, 1, 1, 1540452383, NULL);
INSERT INTO `cl_money_detail` VALUES (1045, 'REjGBP6KXA201810255126', 2, 3, '', -5.00, 1, 1, 1, 1540452399, NULL);
INSERT INTO `cl_money_detail` VALUES (1046, 'REjGBP6KXA201810255763', 1, 3, '', 0.01, 1, 1, 1, 1540452402, NULL);
INSERT INTO `cl_money_detail` VALUES (1047, 'RELbyJGK5d201810259631', 2, 6, '', -234.00, 1, 1, 1, 1540452517, NULL);
INSERT INTO `cl_money_detail` VALUES (1048, 'RE3oynryJV201810259263', 1, 38, '拍卖房间啊啊啊竞拍价格被超越，返还冻结金额', 100000.00, 4, 1, 1, 1540456474, NULL);
INSERT INTO `cl_money_detail` VALUES (1049, 'RELbyJGK5d201810257375', 2, 6, '拍卖房间啊啊啊加价成功，冻结竞拍金额 ', -110000.00, 4, 1, 1, 1540456475, NULL);
INSERT INTO `cl_money_detail` VALUES (1050, 'REjGBP6KXA201810251093', 1, 3, '拍卖房间谢谢学长竞拍价格被超越，返还冻结金额', 100000.00, 4, 1, 1, 1540456599, NULL);
INSERT INTO `cl_money_detail` VALUES (1051, 'RELbyJGK5d201810258728', 2, 6, '拍卖房间谢谢学长加价成功，冻结竞拍金额 ', -110000.00, 4, 1, 1, 1540456599, NULL);
INSERT INTO `cl_money_detail` VALUES (1052, 'REjGBP6KXA201810252252', 2, 3, '', -8.00, 4, 1, 1, 1540456685, NULL);
INSERT INTO `cl_money_detail` VALUES (1053, 'REjGBP6KXA201810252228', 1, 3, '', 0.69, 1, 4, 1, 1540456687, NULL);
INSERT INTO `cl_money_detail` VALUES (1054, 'RELbyJGK5d201810253509', 2, 6, '', -10.00, 4, 1, 1, 1540456782, NULL);
INSERT INTO `cl_money_detail` VALUES (1055, 'RELbyJGK5d201810258367', 2, 6, '拍卖角色哈哈加价成功，冻结竞拍金额 ', -11000.00, 4, 4, 1, 1540457125, NULL);
INSERT INTO `cl_money_detail` VALUES (1056, 'RELbyJGK5d201810255296', 2, 6, '', -0.02, 4, 1, 1, 1540457352, NULL);
INSERT INTO `cl_money_detail` VALUES (1057, 'RELbyJGK5d201810252752', 2, 6, '', -0.02, 4, 1, 1, 1540458004, NULL);
INSERT INTO `cl_money_detail` VALUES (1058, 'REjGBP6KXA201810251301', 2, 3, '', -78.00, 4, 1, 1, 1540458091, NULL);
INSERT INTO `cl_money_detail` VALUES (1059, 'REjGBP6KXA201810251801', 1, 3, '', 22.80, 1, 4, 1, 1540458094, NULL);
INSERT INTO `cl_money_detail` VALUES (1060, 'REjGBP6KXA201810259654', 2, 3, '', -9.00, 4, 1, 1, 1540458186, NULL);
INSERT INTO `cl_money_detail` VALUES (1061, 'REjGBP6KXA201810259361', 1, 3, '', 0.84, 1, 4, 1, 1540458188, NULL);
INSERT INTO `cl_money_detail` VALUES (1062, 'RELbyJGK5d201810258323', 2, 6, '', -50.00, 4, 1, 1, 1540461187, NULL);
INSERT INTO `cl_money_detail` VALUES (1063, 'RELbyJGK5d201810254410', 2, 6, '', -50.00, 1, 4, 1, 1540462110, NULL);
INSERT INTO `cl_money_detail` VALUES (1064, 'RE201810256372', 1, NULL, '拍卖成功,获得拍卖价110000.00', 110000.00, 4, 1, 1, 1540467601, NULL);
INSERT INTO `cl_money_detail` VALUES (1065, 'RE201810256961', 1, NULL, '拍卖成功,获得拍卖价110000.00', 110000.00, 4, 1, 1, 1540467601, NULL);
INSERT INTO `cl_money_detail` VALUES (1066, 'REvqp4ZyMo201810256763', 1, 10, '红包未领完返还', 100.00, 7, 1, 1, 1540468621, NULL);
INSERT INTO `cl_money_detail` VALUES (1067, 'RELbyJGK5d201810252475', 1, 6, '红包未领完返还', 10.00, 7, 1, 1, 1540468681, NULL);
INSERT INTO `cl_money_detail` VALUES (1068, 'REvqp4ZyMo201810252950', 1, 10, '红包未领完返还', 100.00, 7, 1, 1, 1540468681, NULL);
INSERT INTO `cl_money_detail` VALUES (1069, 'REvqp4ZyMo201810255709', 1, 10, '红包未领完返还', 66.00, 7, 4, 1, 1540468681, NULL);
INSERT INTO `cl_money_detail` VALUES (1070, 'RELbyJGK5d201810253734', 1, 6, '红包未领完返还', 10.00, 7, 1, 1, 1540468681, NULL);
INSERT INTO `cl_money_detail` VALUES (1071, 'REvqp4ZyMo201810259320', 1, 10, '红包未领完返还', 822.68, 7, 1, 1, 1540469281, NULL);
INSERT INTO `cl_money_detail` VALUES (1072, 'RE3oynryJV201810254639', 1, 38, '红包未领完返还', 1000.00, 7, 1, 1, 1540469641, NULL);
INSERT INTO `cl_money_detail` VALUES (1073, 'REjGBP6KXA201810261979', 2, 3, '', -33.00, 1, 1, 1, 1540538219, NULL);
INSERT INTO `cl_money_detail` VALUES (1074, 'REjGBP6KXA201810266019', 1, 3, '', 5.35, 1, 1, 1, 1540538220, NULL);
INSERT INTO `cl_money_detail` VALUES (1075, 'REjGBP6KXA201810266994', 1, 3, 'BCDN红包未领完退款66.66', 66.66, 7, 4, 1, 1540538221, NULL);
INSERT INTO `cl_money_detail` VALUES (1076, 'REjGBP6KXA201810269441', 1, 3, 'BCDN红包未领完退款66.67', 66.67, 7, 4, 1, 1540538221, NULL);
INSERT INTO `cl_money_detail` VALUES (1077, 'REjGBP6KXA201810269241', 2, 3, '', -74.00, 4, 1, 1, 1540538269, NULL);
INSERT INTO `cl_money_detail` VALUES (1078, 'REjGBP6KXA201810267188', 1, 3, '', 2.27, 1, 4, 1, 1540538271, NULL);
INSERT INTO `cl_money_detail` VALUES (1079, 'REjGBP6KXA201810267940', 1, 3, 'BCDN红包未领完退款0.01', 0.01, 7, 4, 1, 1540538281, NULL);
INSERT INTO `cl_money_detail` VALUES (1080, 'REjGBP6KXA201810266787', 1, 3, '积分红包未领完退款19.41', 19.41, 7, 1, 1, 1540538282, NULL);
INSERT INTO `cl_money_detail` VALUES (1081, 'REjGBP6KXA201810261064', 1, 3, 'BCDN红包未领完退款68.67', 68.67, 7, 4, 1, 1540538341, NULL);
INSERT INTO `cl_money_detail` VALUES (1082, 'REjGBP6KXA201810269666', 2, 3, '', -1000.00, 4, 1, 1, 1540538453, NULL);
INSERT INTO `cl_money_detail` VALUES (1083, 'REjGBP6KXA201810262770', 1, 3, '', 9.21, 1, 4, 1, 1540538458, NULL);
INSERT INTO `cl_money_detail` VALUES (1084, 'REvqp4ZyMo201810265146', 1, 10, '积分红包未领完退款235', 235.00, 7, 1, 1, 1540538461, NULL);
INSERT INTO `cl_money_detail` VALUES (1085, 'REjGBP6KXA201810265812', 1, 3, 'BCDN红包未领完退款990.79', 990.79, 7, 4, 1, 1540538522, NULL);
INSERT INTO `cl_money_detail` VALUES (1086, 'REvqp4ZyMo201810266529', 1, 10, '积分红包未领完退款234', 234.00, 7, 1, 1, 1540538821, NULL);
INSERT INTO `cl_money_detail` VALUES (1087, 'REjGBP6KXA201810266121', 1, 3, '积分红包未领完退款0.01', 0.01, 7, 1, 1, 1540538822, NULL);
INSERT INTO `cl_money_detail` VALUES (1088, 'REjGBP6KXA201810266556', 1, 3, '积分红包未领完退款4.99', 4.99, 7, 1, 1, 1540538822, NULL);
INSERT INTO `cl_money_detail` VALUES (1089, 'RELbyJGK5d201810269651', 1, 6, '积分红包未领完退款234', 234.00, 7, 1, 1, 1540538941, NULL);
INSERT INTO `cl_money_detail` VALUES (1090, 'REjGBP6KXA201810269970', 2, 3, '拍卖房间小松加价成功，冻结竞拍金额 ', -110000.00, 4, 1, 1, 1540539545, NULL);
INSERT INTO `cl_money_detail` VALUES (1091, 'REjGBP6KXA201810268311', 2, 3, '拍卖房间小兰加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1540539778, NULL);
INSERT INTO `cl_money_detail` VALUES (1092, 'REjGBP6KXA201810263816', 2, 3, '拍卖角色小兰加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1540539792, NULL);
INSERT INTO `cl_money_detail` VALUES (1093, 'REjGBP6KXA201810268849', 2, 3, '拍卖房间小松sky加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1540539947, NULL);
INSERT INTO `cl_money_detail` VALUES (1094, 'REjGBP6KXA201810269825', 2, 3, '拍卖角色小松sky加价成功，冻结竞拍金额 ', -10000.00, 4, 4, 1, 1540539961, NULL);
INSERT INTO `cl_money_detail` VALUES (1095, 'RE201810265446', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1540539962, NULL);
INSERT INTO `cl_money_detail` VALUES (1096, 'RE201810269939', 1, NULL, '拍卖成功,获得拍卖价10000.00', 10000.00, 4, 1, 1, 1540540021, NULL);
INSERT INTO `cl_money_detail` VALUES (1097, 'RE201810262469', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1540540141, NULL);
INSERT INTO `cl_money_detail` VALUES (1098, 'RE201810263551', 1, NULL, '拍卖成功,获得拍卖价10000.00', 10000.00, 4, 1, 1, 1540540142, NULL);
INSERT INTO `cl_money_detail` VALUES (1099, 'REvqp4ZyMo201810265081', 2, 10, '', -9.99, 8, 1, 1, 1540542999, NULL);
INSERT INTO `cl_money_detail` VALUES (1100, 'REvqp4ZyMo201810269796', 2, 10, '', -234.00, 4, 1, 1, 1540543116, NULL);
INSERT INTO `cl_money_detail` VALUES (1101, 'REjGBP6KXA201810266497', 1, 3, 'BCDN红包未领完退款5.95', 5.95, 7, 4, 1, 1540543141, NULL);
INSERT INTO `cl_money_detail` VALUES (1102, 'RELbyJGK5d201810262141', 1, 6, 'BCDN红包未领完退款9.31', 9.31, 7, 4, 1, 1540543201, NULL);
INSERT INTO `cl_money_detail` VALUES (1103, 'REvqp4ZyMo201810267844', 1, 10, 'BCDN红包未领完退款224.19', 224.19, 7, 4, 1, 1540543201, NULL);
INSERT INTO `cl_money_detail` VALUES (1104, 'REjGBP6KXA201810264172', 2, 3, '', -10.00, 4, 1, 1, 1540543241, NULL);
INSERT INTO `cl_money_detail` VALUES (1105, 'REjGBP6KXA201810261412', 1, 3, '', 5.48, 1, 4, 1, 1540543243, NULL);
INSERT INTO `cl_money_detail` VALUES (1106, 'REjGBP6KXA201810268023', 1, 3, 'BCDN红包未领完退款3.52', 3.52, 7, 4, 1, 1540543321, NULL);
INSERT INTO `cl_money_detail` VALUES (1107, 'RE201810267060', 1, NULL, '拍卖成功,获得拍卖价11000.00', 11000.00, 4, 1, 1, 1540543562, NULL);
INSERT INTO `cl_money_detail` VALUES (1108, 'REvqp4ZyMo201810266876', 2, 10, '', -123.00, 1, 1, 1, 1540543726, NULL);
INSERT INTO `cl_money_detail` VALUES (1109, 'REvqp4ZyMo201810265542', 1, 10, '', 58.64, 1, 1, 1, 1540543729, NULL);
INSERT INTO `cl_money_detail` VALUES (1110, 'RELbyJGK5d201810263094', 1, 6, 'BCDN红包未领完退款0.02', 0.02, 7, 4, 1, 1540543801, NULL);
INSERT INTO `cl_money_detail` VALUES (1111, 'REvqp4ZyMo201810264432', 1, 10, '积分红包未领完退款26.76', 26.76, 7, 1, 1, 1540543801, NULL);
INSERT INTO `cl_money_detail` VALUES (1112, 'REjGBP6KXA201810263602', 2, 3, '', -900.00, 4, 1, 1, 1540544039, NULL);
INSERT INTO `cl_money_detail` VALUES (1113, 'REjGBP6KXA201810263626', 1, 3, '', 404.03, 1, 4, 1, 1540544041, NULL);
INSERT INTO `cl_money_detail` VALUES (1114, 'REjGBP6KXA201810267414', 2, 3, '', -12.00, 4, 1, 1, 1540544071, NULL);
INSERT INTO `cl_money_detail` VALUES (1115, 'REjGBP6KXA201810265342', 1, 3, '', 0.75, 1, 4, 1, 1540544074, NULL);
INSERT INTO `cl_money_detail` VALUES (1116, 'RELbyJGK5d201810269803', 1, 6, '', 3.91, 1, 4, 1, 1540544075, NULL);
INSERT INTO `cl_money_detail` VALUES (1117, 'REjGBP6KXA201810269030', 1, 3, 'BCDN红包未领完退款367.59', 367.59, 7, 4, 1, 1540544101, NULL);
INSERT INTO `cl_money_detail` VALUES (1118, 'REjGBP6KXA201810266254', 1, 3, 'BCDN红包未领完退款3.88', 3.88, 7, 4, 1, 1540544161, NULL);
INSERT INTO `cl_money_detail` VALUES (1119, 'RE14KVWp2X201810262268', 2, 11, '', -100.00, 1, 1, 1, 1540544180, NULL);
INSERT INTO `cl_money_detail` VALUES (1120, 'RE14KVWp2X201810263853', 1, 11, '', 26.63, 1, 1, 1, 1540544219, NULL);
INSERT INTO `cl_money_detail` VALUES (1121, 'RE14KVWp2X201810268127', 1, 11, '积分红包未领完退款32.67', 32.67, 7, 1, 1, 1540544281, NULL);
INSERT INTO `cl_money_detail` VALUES (1122, 'RELbyJGK5d201810262225', 1, 6, 'BCDN红包未领完退款0.02', 0.02, 7, 4, 1, 1540544462, NULL);
INSERT INTO `cl_money_detail` VALUES (1123, 'REjGBP6KXA201810267340', 1, 3, 'BCDN红包未领完退款44.86', 44.86, 7, 4, 1, 1540544521, NULL);
INSERT INTO `cl_money_detail` VALUES (1124, 'REjGBP6KXA201810265210', 2, 3, '', -50.00, 4, 1, 1, 1540544536, NULL);
INSERT INTO `cl_money_detail` VALUES (1125, 'REjGBP6KXA201810266460', 1, 3, 'BCDN红包未领完退款6.31', 6.31, 7, 4, 1, 1540544641, NULL);
INSERT INTO `cl_money_detail` VALUES (1126, 'REjGBP6KXA201810268174', 1, 3, 'BCDN红包未领完退款50', 50.00, 7, 4, 1, 1540544642, NULL);
INSERT INTO `cl_money_detail` VALUES (1127, 'REvqp4ZyMo201810263055', 2, 10, '', -234.00, 1, 1, 1, 1540544809, NULL);
INSERT INTO `cl_money_detail` VALUES (1128, 'REvqp4ZyMo201810264477', 1, 10, '', 0.92, 1, 1, 1, 1540544811, NULL);
INSERT INTO `cl_money_detail` VALUES (1129, 'REvqp4ZyMo201810262161', 1, 10, '积分红包未领完退款172.45', 172.45, 7, 1, 1, 1540544881, NULL);
INSERT INTO `cl_money_detail` VALUES (1130, 'REvqp4ZyMo201810263875', 2, 10, '', -123.00, 1, 1, 1, 1540547463, NULL);
INSERT INTO `cl_money_detail` VALUES (1131, 'REvqp4ZyMo201810261000', 1, 10, '', 41.00, 1, 1, 1, 1540547469, NULL);
INSERT INTO `cl_money_detail` VALUES (1132, 'REvqp4ZyMo201810264995', 1, 10, '积分红包未领完退款82', 82.00, 7, 1, 1, 1540547581, NULL);
INSERT INTO `cl_money_detail` VALUES (1133, 'RELbyJGK5d201810266974', 1, 6, 'BCDN红包未领完退款50', 50.00, 7, 4, 1, 1540547641, NULL);
INSERT INTO `cl_money_detail` VALUES (1134, 'RELbyJGK5d201810264866', 1, 6, 'BCDN红包未领完退款50', 50.00, 7, 4, 1, 1540548541, NULL);
INSERT INTO `cl_money_detail` VALUES (1135, 'REjGBP6KXA201810271323', 2, 3, '', -10.00, 4, 1, 1, 1540607852, NULL);
INSERT INTO `cl_money_detail` VALUES (1136, 'REjGBP6KXA201810276832', 1, 3, 'BCDN红包未领完退款9.35', 9.35, 7, 4, 1, 1540607941, NULL);
INSERT INTO `cl_money_detail` VALUES (1137, 'REjGBP6KXA201810275910', 2, 3, '', -99.00, 4, 1, 1, 1540609378, NULL);
INSERT INTO `cl_money_detail` VALUES (1138, 'REjGBP6KXA201810272453', 1, 3, '', 9.59, 1, 4, 1, 1540609381, NULL);
INSERT INTO `cl_money_detail` VALUES (1139, 'REjGBP6KXA201810277396', 1, 3, 'BCDN红包未领完退款75.44', 75.44, 7, 4, 1, 1540609441, NULL);
INSERT INTO `cl_money_detail` VALUES (1140, 'REjGBP6KXA201810279312', 2, 3, '', -20.00, 4, 1, 1, 1540610695, NULL);
INSERT INTO `cl_money_detail` VALUES (1141, 'REjGBP6KXA201810271575', 1, 3, '', 12.07, 1, 4, 1, 1540610699, NULL);
INSERT INTO `cl_money_detail` VALUES (1142, 'REjGBP6KXA201810278543', 1, 3, 'BCDN红包未领完退款7.93', 7.93, 7, 4, 1, 1540610761, NULL);
INSERT INTO `cl_money_detail` VALUES (1143, 'REjGBP6KXA201810272971', 2, 3, '', -50.00, 4, 1, 1, 1540624816, NULL);
INSERT INTO `cl_money_detail` VALUES (1144, 'REjGBP6KXA201810271413', 1, 3, '', 2.64, 1, 4, 1, 1540624819, NULL);
INSERT INTO `cl_money_detail` VALUES (1145, 'REjGBP6KXA201810279624', 1, 3, 'BCDN红包未领完退款45.36', 45.36, 7, 4, 1, 1540624921, NULL);
INSERT INTO `cl_money_detail` VALUES (1146, 'RE201810273421', 1, NULL, '拍卖成功,获得拍卖价110000.00', 110000.00, 4, 1, 1, 1540626001, NULL);
INSERT INTO `cl_money_detail` VALUES (1147, 'RE14KVWp2X201810285958', 2, 11, '', -10.00, 1, 1, 1, 1540724368, NULL);
INSERT INTO `cl_money_detail` VALUES (1148, 'RE14KVWp2X201810283293', 1, 11, '积分红包未领完退款10', 10.00, 7, 1, 1, 1540724461, NULL);
INSERT INTO `cl_money_detail` VALUES (1149, 'RELbyJGK5d201810283714', 2, 6, '直播间付费', -60.00, 5, 1, 1, 1540725409, NULL);
INSERT INTO `cl_money_detail` VALUES (1150, 'RE14KVWp2X201810293490', 2, 11, '', -9.99, 8, 1, 1, 1540750767, NULL);
INSERT INTO `cl_money_detail` VALUES (1151, 'RE4ZBLXBMN201810296900', 2, 7, '直播间付费', -100.00, 5, 1, 1, 1540778448, NULL);
INSERT INTO `cl_money_detail` VALUES (1152, 'REjGBP6KXA201810291323', 2, 3, '拍卖房间绝地求生加价成功，冻结竞拍金额 ', -100000.00, 4, 1, 1, 1540804231, NULL);
INSERT INTO `cl_money_detail` VALUES (1153, 'RE201810297629', 1, NULL, '拍卖成功,获得拍卖价100000.00', 100000.00, 4, 1, 1, 1540804441, NULL);
INSERT INTO `cl_money_detail` VALUES (1154, 'REPRBaZyZ2201810309615', 1, 2, '竞猜获胜,赢得0.00积分', 0.00, 2, 1, 1, 1540893046, NULL);
INSERT INTO `cl_money_detail` VALUES (1155, 'RELbyJGK5d201810302720', 1, 6, '竞猜获胜,赢得4.00积分', 4.00, 2, 1, 1, 1540893275, NULL);
INSERT INTO `cl_money_detail` VALUES (1156, 'REPRBaZyZ2201810309516', 1, 2, '竞猜获胜,赢得0.00积分', 0.00, 2, 1, 1, 1540893292, NULL);
INSERT INTO `cl_money_detail` VALUES (1157, 'RE14KVWp2X201810301195', 1, 11, '竞猜获胜,赢得0.40积分', 0.40, 2, 1, 1, 1540893333, NULL);

-- ----------------------------
-- Table structure for cl_news
-- ----------------------------
DROP TABLE IF EXISTS `cl_news`;
CREATE TABLE `cl_news`  (
  `news_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `lihao` int(11) NOT NULL COMMENT '评论利好人数',
  `likong` int(11) NOT NULL COMMENT '评论利空人数',
  `is_top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐  1=>是 0=>否',
  `cid` tinyint(4) NOT NULL COMMENT '咨询分类id',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '封面图',
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '帖子详情',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>正常  0=>禁用',
  PRIMARY KEY (`news_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '咨询列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_news
-- ----------------------------
INSERT INTO `cl_news` VALUES (5, '纽约积极谋求区块链技术中心地位', 48, 5, 0, 1, 'http://file.51soha.com/04583201809041517509988.jpg', '<p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">在分享中，周启鹏较为系统的介绍了智能合约的产生及应用，安全事件以及安全风险分析，并提出随着智能合约在社会化大规模应用中应对智能合约安全的策略。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">周启鹏表示，未来智能合约社会化应用的难点主要有三个：一是随着“区块链+”浪潮的到来，涉及的行业将越来越广；二是行业应用所需的合约复杂度越来越高；三是未来的智能合约，除开发者外非开发者也可以编写，这将给智能合约的安全带来很大挑战。&nbsp;</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">对此，知道创宇“404”安全实验室研发了一套智能合约验证系统“昊天塔”。该系统和公链、联盟链等团队深入合作，以提供应用层的安全防护能力，支撑智能合约数量和逻辑复杂度不断增加的应用场景。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">以下为演讲全文，enjoy：</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">我今天的演讲分为四个部分，第一个是探讨一下智能合约发生了哪些引发我们思考的安全事件，为什么我们要开始讨论智能合约的安全。第二部分会对智能合约的安全风险做一个简单总结，把这个问题向大家做一个描述。第三部分是区块链+应用的情况下智能合约安全又是怎样的。最后一个是未来的智能合约要如何做，我们也提出了自己的想法和建议。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">首先是看智能合约安全的现状。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">在此之前我想先解释一个名词——智能合约。智能合约是区块链的核心技术之一。之所以称作智能合约，是因为智能合约在区块链当中是一段能够自动化执行的程序代码，嵌在区块链的顶层架构上。所以我们可以简单的理解为，如果把区块链技术比作现在大家都用智能手机操作系统，或者说区块链底层的技术可以认为是一个网络分布式的操作系统，智能合约就可以理解为是这个网络分布式操作系统中所运行的程序。所以智能合约是一个以信息化的方式传播，验证和执行合同的协议。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">智能合约基于区块链去中心化的特性，允许在没有第三方的情况下进行可信的交易，同时这些交易是可以追溯、不可逆的，这些因素促进了智能合约技术的发展。智能合约的最终目的是提供比原来纸质合同，或者说合同文本更优的安全方案，同时减少双方毁约和因此产生的纠纷，可大大提高交易的效率。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">要追溯历史的话，这个概念是 1994 年的时候由科学家尼克·萨博提出来的。当时互联网还是一个雏形，只是提出智能合约这样一个设想。2008 年区块链 1.0 版本诞生，提供一个天然可信环境，但是这个环境缺失一些东西，没有能够对外提供更多可以被第三方执行和调用的接口，所以可以说区块链1.0只支持一些简单的指令，到 2014 年区块链 2.0 版本发布，这个时候就在具备 区块链1.0 可信环境属性同时，开始支持图灵完备，设计了可供开发者调用和执行的接口，提供了与应用场景解决的可能性。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接下来我们可以回顾下智能合约真正被运行起来后，产生的对整个历史进程影响比较大的安全事件，这就是发生在 2016 年，以太坊中的 DAO 事件。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">2016年6月15日，攻击合约被创立。6月17日，攻击开始，Vitalik Buterin 得知攻击消息后立刻通知了中国社区。theDAO 监护人提议社区发送垃圾交易阻塞以太坊网络，以减缓 DAO 资产被转移的速度。随后 V 神在官方博客发布[紧急状态更新：关于 DAO 的漏洞]公告。解释了被攻击的一些细节以及提出软分叉解决方案，不会有回滚。不会有交易和区块被撤销。软分叉将从块高度 1760000 开始把任何与 the DAO 和 child DAO 相关的交易认做无效交易，以此阻止攻击者在 27天之后提走被盗的资产。这之后会有一次硬分叉将资产找回。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">我们看一下合约的本身是怎么被攻击的。首先左边是 DAO智能合约 的代码片断，这里边写了一个withdraw函数，右边是黑客攻击合约的代码片段，这个攻击合约在执行的时候，可以通过外部直接调用的方式调用DAO智能合约的withdraw函数，一层一层不断执行不断递归调用，使得黑客可以通过合约外部调用方式用攻击合约把原合约很多数字资产做了转移，由此引发 DAO 事件。最大的影响莫过于对以太坊这条公链，因为产生了一个硬分叉。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接下来分析一些智能合约的安全风险。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">先跟大家交流一下智能合约代码方面的特性，我总结了四类：</p><ul class=\" list-paddingleft-2\" style=\"padding: 0px; color: rgb(51, 51, 51); font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" font-size:=\"\" white-space:=\"\" background-color:=\"\"><li><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify;\">第一个是账户的设计，智能合约一共设计了两种账户，一种是外部账户，由公私钥体系做控制，另一种叫做合约账户，是由代码本身控制。<br/></p></li><li><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify;\">第二个是在区块链 2.0 上还有个叫 gas 的东西。合约代码越复杂的时候，我在执行这个合约过程中所需要花费的 gas 越多。这就产生一个问题，如果调用者提供的 gas 不足，这个合约里面已经执行的代码是会被回滚的，这个合约调用者也可以设计自己本身的 gasPrice，矿工优先处置 gasPrice 较高的交易，所以 gasPrice 如果设计的比较低，或者设计的太高了等等这样一些方式，都是不合理的。<br/></p></li><li><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify;\">接下来是函数，一共涉及几种函数，第一个是 fallback 函数（编者注：当我们调用某个智能合约时，如果指定的函数找不到，或者根本就没指定调用哪个函数时，fallback 函数就会被调用），同时设计 transfer、send、call.value 等等这样接收资金的函数，同时还有一个 selfdestruct 这样一个函数去做合约。<br/></p></li><li><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify;\">最后是函数调用方面，类似于传统的调用方式。<br/></p></li></ul><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接着我们来看智能合约语言的特性。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\"><img alt=\"知道创宇周启鹏：未来智能合约涉及行业会更广、逻辑复杂度更高、非开发者也能轻松编写 | 星球日报 P.O.D大会\" src=\"/public/upload/ueditor/20180915/1537003592884266.png\" style=\"border: 0px; vertical-align: top; max-width: 770px; margin: 0px auto; display: block;\"/></p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">这个语言当中本身涉及的函数默认可见性是 public，只要写出一个合约，函数如果没有设计权限，对于用户来说都是公开的 public 。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第二个里面涉及大量数值运算内容。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第三个是设计三种异常处理方式，require、assert 或者 revert 这三种。它们各有区别，require 一般是写在函数前面，用来检查输入的变量和合作状态变量是否满足条件，如果满足条件的话才会去执行。assert 这个函数，从开发者角度会写在函数的尾部，用来检查函数的内部错误，如果出现错误就会强制停止。revert 函数更特殊一些，遇到一些无效代码，会回滚之前所有的状态。这三个函数还有一个区别，revert 可以返回，合约如果没有执行的话，这个 gas 是不需要付的。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">刚才讲到了，合约本身是有一个外部账户和合约账户的区分，所以智能合约风险第一个也是我们认为比较常见的问题，叫做访问控制的问题。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">访问控制函数应该设定成只有特定的用户才能够调用这样一个情况。我是合约的用户者才能够调用一些挖矿函数，但是我们在代码过程当中能够看到，黑客这边可以通过写恶意合约或者写攻击合约来提升自己权限，使人人都可以成为一个合约拥有者，这样无形当中把整个合约内容函数，或者叫做合约账户函数暴露在外面，就产生后面一系列问题。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">下面举一个 Owner 构造函数的错误例子。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\"><img alt=\"知道创宇周启鹏：未来智能合约涉及行业会更广、逻辑复杂度更高、非开发者也能轻松编写 | 星球日报 P.O.D大会\" src=\"/public/upload/ueditor/20180915/1537003592310131.png\" style=\"border: 0px; vertical-align: top; max-width: 770px; margin: 0px auto; display: block;\"/></p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">构造函数在部署合约的时候才调用，并且本身不上链。普通函数则是能够被任意调用，同时代码也写在区块链当中。大家应该理解一个情况，数据也好，合约也好，一旦上链，都是被允许查看的，所以普通函数写在链上之后可以被任意的团队，可以被恶意黑客或者被白帽子参考和研究。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">下面做一个简单的代码梳理，这边写了一个构造函数Owner，下面这个函数定义的function中，大家能够看到这个 Owner函数的大小写变了，由于大小写原因书写错误，导致了这样一个构造函数变成了一个普通的公有函数。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接下来我们整理了一下智能合约中我们认为目前出现安全风险比较大的四个原因。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">首先第一个是智能合约在整个区块链的架构当中，属于中间协议层的最上层，在上面是我们所谓的分布式应用，所以出现的位置是位于上层应用，上层应用本身出现安全问题的概率，按照以往基于 windows 操作系统的应用出现问题概率相对会高一些。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第二个是语言的发展时间很短，语言本身不够完善。到目前为止，这个语言版本大概在 0.4.24，一般能够公开发布的开发语言版本可都是在 V1.0 或者 V1.1 等等，所以说从版本本身发展来说还需要一个很长阶段。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第三个问题属于国内项目方这边，目前经验不是很充足，语言本身发展时间又很短，基于solidity这个语言产生的示例或范示标准文件比较少，包括官方发布的也存在问题，所以导致开发人员经验更少，又不熟悉语言特性，会拿传统开发互联网的软件开发区块链，缺乏安全经验导致问题出现。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">最后就是目前并没有一个智能合约代码审核的完善标准，这个标准没有的话，实际上其实还有很多事情大家都是不清楚的，就会产生更多的奇奇怪怪的问题。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接下来一个是展示一下开源项目 DAPS 统计以及公布的分布式应用的安全问题。有递归调用漏洞，访问控制，整数溢出，未检查底层调用，错误随机等等这样十个类。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">最后一个原因是智能合约本身也是顶层应用，包括本身的安全问题都还有很多未知未觉的领域存在，需要更多项目方，更多的白帽子，更多安全厂商一起努力，不断使技术，还有上层应用更加健壮，为更多社会化应用服务。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">接下来想把我们智能合约未来应用的场景做一个大的猜想，或者做一个预期。首先现在结合我们社会化的应用来说，区块链也好，智能合约也好，其实已经和我们生活当中一部分事情结合在一起了。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">首先第一个金融属性，像之前蚂蚁金服在香港的新闻，利用区块链技术做跨境汇款，包括现在保险、证券、股权登记这样一些原有金融领域的应用，现在已经慢慢出现雏形了。第二个物联网应用，现在基于区块链的物联网、汽车租赁应用也逐渐出现。第三个供应链，上午百度介绍的时候，针对百度百科的文件编辑溯源也在落地建设过程当中。能源领域点对点的便利共享的领域，包括公共服务领域，针对我们文化、教育、产权、医疗等等这样一些领域逐渐出现了。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">下面做几张图的展示，首先介绍一下传统汇款和区块链汇款的差别。在传统汇款当中，境内都还好，速度很快，但是一旦涉及到境外的跨境汇款效率非常低，这里面涉及到一个问题，叫做中间银行和清算网络，作为一个中心化机构解决信任问题，导致效率会有所降低。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">如果把这个场景放到区块链上，用智能合约实现的话，通过链本身的去中心化信任的机制，资产转移的就可以用智能合约实现，从资产结算任何时间结算，包括资产转移，上次蚂蚁金服那边在做的时候，从菲律宾汇款到香港大概用了几十秒时间。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第二个应用在传统供应链金融，我们已经看到国内有一些机构大胆用区块链技术尝试物品溯源，比如之前曝光的疫苗事件。虽然疫苗生产厂商作为源头无法通过区块链技术进行控制，但是疫苗整个在冷链运输，在各个监督站各个医院的数据都可以上传，防止中间有一些个人的恶意行为，导致在传播当中数据的丢失和篡改。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第三个针对传统物流，原来传统物流有很多痛点，互相不信任，之前用淘宝的时候最大的问题到底是买方先付钱还是卖方先发货，后来出现了支付宝为来解决第三方信任问题，买房把钱给中间平台。如果有区块链能够和网购支付场景结合的话，互不信任这个问题可以解决，买方可以在收到货的这一刻，订单信息就会在链上做数据提交，这个时候买方账户里面的钱就可以通过智能合约方式直接打到卖方账户上面去，包括订单被篡改风险，还有隐私信息，包括现在大家遇到快递信息泄露个人隐私，将来都可以上链的话，大家面对的都是在链上隐藏数据的信息身份。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">还有针对疫苗，针对医院，针对医疗体系，从每一个药厂药品信息上链，药房售卖药片都是可以在链上确认的，患者也可以和医生做关联，甚至可以用一个 APP 知道这个人的健康信息，包括历史服药信息，在哪些医院检查，都是能够被查到的。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">前面做了一些大胆的幻想，下面看一下未来智能合约会是一个什么状态。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第一个是区块链+应用，在未来可能涉及的行业特别广泛，刚才上午百度区块链的平台介绍了几个特点，第一个和版权结合，我们现在很多商用图片都会上链，包括未来可能会有数字音乐版权，数字电影版权都会上链，包括像邮政、游戏等等，和我们生活的结合越来越深，涉及的行业也越来越广。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">第二个是随着行业越来越多，每个行业都有每个行业的特点，所以行业应用复杂度越来越高，现在智能合约的代码是 300 行到 500 行，将来智能合约应用，一个合约可能有几千或者上万行的逻辑漏洞，安全威胁肯定会越多。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">最后一个场景是开发者现在还比较少，未来的开发者越来越多，越来越成熟，将来提供很多智能合约的应用，不仅仅是对开发者，也可能对更多普通的民众开放。我们的民众就可以像现在用 APP 一样，简单输入一些数据，输入一些数量或者输入一些价格，就可以自发产生智能合约，后面其实是公链方针对智能合约、对自己项目所起的标准，这样的人越来越多。所以他们所产生的问题越来越多，通过目前的使用方式就不现实了。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">我们知道创宇404实验室也是结合之前介绍的，未来预计会有更广泛更复杂的应用，还有更多的智能合约的场景，我们设计研发了一套智能合约智能验证的系统，能够在结合人工审计情况下，更多通过自动化智能化，通过 AI 方式和很多的公链项目方一起深入的结合，通过深度结合方式，对整个链产生的智能合约标准，和未来所产生智能合约使用的应用，让他们更健康更健壮一些，减少所出现的安全漏洞，让这些智能合约能够给我们生活带来便利性的同时，减少经济上的损失。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">后面这两个是我们现在目前内部版本的截图，把名字定义为叫做昊天塔，通过这样一个产品，或者这样一个系统，来为更多智能合约开发者和使用者提供安全的服务和保障。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: \" pingfang=\"\" lantinghei=\"\" helvetica=\"\" microsoft=\"\" wenquanyi=\"\" zen=\"\" micro=\"\" white-space:=\"\" background-color:=\"\">今天介绍暂时到这里，希望后续有关心的技术方面的同学或者是项目方，如果有兴趣大家在一起多多交流，谢谢大家。&nbsp; &nbsp;&nbsp;</p><p><br/></p>', 1533112234, 1537948244, 0);
INSERT INTO `cl_news` VALUES (7, '杜均-金色财经创始人', 15, 22, 1, 0, 'http://file.51soha.com//b683920180926162917600.png', '<p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">杜均，金色财经创始人，金色财经是集行业新闻、资讯、行情、数据、百科、社区等一站式区块链产业服务平台。</p><p style=\"text-align:center;margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\"><img src=\"/public/upload/ueditor/20180926/1537950529980101.png\" alt=\"杜均-金色财经创始人\" class=\"\" style=\"display: block; margin: 0px auto; padding: 0px; vertical-align: top; border: 0px; -webkit-tap-highlight-color: transparent;\"/><br/></p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">他是全球最早从事区块链产业投资的专业投资人，也是知名的数字资产投资和管理人。他曾就职于腾讯、康盛创想等知名互联网企业，并且曾经联合创建了加密货币交易平台火币网、区块链专业媒体金色财经等公司，具有专业的行业知识、十分丰富的创业经验和行业资源，擅长市场运营、资本运作及企业孵化，投资的区块链项目已超数十个。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">杜均在投资、市场营销、运营、创意等领域均有杰出表现。30岁前完成白手起家，完成从千万富翁到亿万富翁的逆袭。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">个人经历</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">杜均从最开始在牛肉店当服务员到从Discuz的商务专员一路干到了被腾讯并购后的Discuz产品线负责人，一待就待了7年。杜均说，他在Discuz的目的不是为了赚钱，而是为了弄懂他的免费模式到底是怎么做的。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">火币数字货币交易平台</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">后来杜均离开了腾讯，2013年6月，火币网创始人李林拉杜均一起创业，火币网于2013年9月正式上线，11月获真格基金和戴志康数百万天使投资，同月单日交易额突破10亿人民币。之后获得红杉中国千万美金融资，还创造了全球比特币单日最高交易记录95亿人民币，火币网是目前全球最大的比特币交易平台，占有全球比特币交易市场50%以上的份额。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">火币网能在众多交易平台中脱颖而出，得益于他们的发展策略，通过免佣金的方式迅速打开市场，无论行情好坏，始终坚持，就积累了大批忠实用户，最终也有了回报。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">天使投资人</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">同时，杜均还是一位天使投资人，目前已经投出了三家新三板公司，但是他认为自己做天使投资和投资域名一样是非专业的。他认为执行力和人脉圈是创业的关键因素。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">2016年9月02日，杜均注册成立北京财到信息技术有限公司，公司底下的业务主要为金色财经网，这是一个集区块链和数字货币为一体的资讯网站，在业内属于龙头之一。</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">域链</p><p style=\"margin: 0px auto 15px; padding: 0px; line-height: 30px; color: rgb(85, 85, 85); font-family: &quot;microsoft yahei&quot;; white-space: normal; background-color: rgb(255, 255, 255);\">去年，杜均离开了服务四年的火币网，他创办了自己的投资公司，做起区块链产业相关的生意——域链（DOC）。</p><p><br/></p>', 1537950789, 1537950789, 1);
INSERT INTO `cl_news` VALUES (8, '金色内参 | 榜眼之争如火如荼 XRP未来还有多少空间？', 18, 10, 0, 0, 'http://file.51soha.com//29ba9201809291441122619.png', '<p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); font-family: &quot;PingFang SC&quot;, &quot;Lantinghei SC&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Microsoft YaHei&quot;, &quot;\\\\微软雅黑&quot;, STHeitiSC-Light, simsun, &quot;\\\\宋体&quot;, &quot;WenQuanYi Zen Hei&quot;, &quot;WenQuanYi Micro Hei&quot;, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);\"><strong style=\"font-size: unset; line-height: 30px; margin-bottom: 30px;\">导读：</strong>过去一周Ripple无疑是最亮的明星，单周涨幅超过60%，振幅近150%，而从底部0.2465美元计算，期间最大涨幅超过220%，市值也飙升至210亿美元之上，一度超越了ETH坐上了榜眼的宝座。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); font-family: &quot;PingFang SC&quot;, &quot;Lantinghei SC&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Microsoft YaHei&quot;, &quot;\\\\微软雅黑&quot;, STHeitiSC-Light, simsun, &quot;\\\\宋体&quot;, &quot;WenQuanYi Zen Hei&quot;, &quot;WenQuanYi Micro Hei&quot;, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);\">除去从交易的角度看，我们在背景资料里发现近期Ripple的诸多声音，包括了美国十大银行之一PNC的合作项目，包括了更广泛的应用场景预期，而众所周知的事情则是Ripple并不是一个标准去中心化项目，他本身更像一家软件公司，所以他的代币并不能完全体现公司价值，这也就是为什么SEC对其代币报以怀疑。不管未来如何，当下，Ripple无疑是大明星。</p><p style=\"margin-top: 32px; margin-bottom: 32px; padding: 0px; font-size: 18px; line-height: 36px; word-wrap: break-word; word-break: normal; color: rgb(41, 41, 59); text-align: justify; font-family: &quot;PingFang SC&quot;, &quot;Lantinghei SC&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Microsoft YaHei&quot;, &quot;\\\\微软雅黑&quot;, STHeitiSC-Light, simsun, &quot;\\\\宋体&quot;, &quot;WenQuanYi Zen Hei&quot;, &quot;WenQuanYi Micro Hei&quot;, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);\"><img src=\"https://img.jinse.com/1183940\" title=\"eqSSeObz2WZGWAO0HgQIYijquXweQ4HbMdwuFVx6.jpeg\" alt=\"eqSSeObz2WZGWAO0HgQIYijquXweQ4HbMdwuFVx6.jpeg\" style=\"border: 1px solid rgb(238, 238, 238); vertical-align: top; max-width: 770px; margin: 0px auto; display: block;\"/></p><p><br/></p>', 1538203297, 1538203297, 1);

-- ----------------------------
-- Table structure for cl_news_cate
-- ----------------------------
DROP TABLE IF EXISTS `cl_news_cate`;
CREATE TABLE `cl_news_cate`  (
  `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_name` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类名称',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '咨讯分类' ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of cl_news_cate
-- ----------------------------
INSERT INTO `cl_news_cate` VALUES (1, '数据观', NULL, NULL, 1);
INSERT INTO `cl_news_cate` VALUES (2, '总结', NULL, NULL, 1);

-- ----------------------------
-- Table structure for cl_news_collect
-- ----------------------------
DROP TABLE IF EXISTS `cl_news_collect`;
CREATE TABLE `cl_news_collect`  (
  `collect_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL COMMENT '咨询id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>正常 0=>取消收藏或删除',
  PRIMARY KEY (`collect_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '咨询收藏表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_news_collect
-- ----------------------------
INSERT INTO `cl_news_collect` VALUES (1, 7, 2, 1538114475, 1539239222, 0);
INSERT INTO `cl_news_collect` VALUES (2, 7, 1, 1538116877, 1538116878, 0);
INSERT INTO `cl_news_collect` VALUES (3, 7, 1, 1538116883, 1538116883, 1);
INSERT INTO `cl_news_collect` VALUES (4, 5, 2, 1538117686, 1538117686, 1);
INSERT INTO `cl_news_collect` VALUES (5, 8, 9, 1538573043, 1538574500, 0);
INSERT INTO `cl_news_collect` VALUES (6, 7, 9, 1538574361, 1538574500, 0);
INSERT INTO `cl_news_collect` VALUES (7, 7, 9, 1538574526, 1538574526, 1);
INSERT INTO `cl_news_collect` VALUES (8, 7, 6, 1538998426, 1538999211, 0);
INSERT INTO `cl_news_collect` VALUES (9, 8, 1, 1539058068, 1539058068, 1);
INSERT INTO `cl_news_collect` VALUES (10, 9, 2, 1539078324, 1539079950, 0);
INSERT INTO `cl_news_collect` VALUES (11, 7, 6, 1539102043, 1539144653, 0);
INSERT INTO `cl_news_collect` VALUES (12, 8, 6, 1539104725, 1539362170, 0);
INSERT INTO `cl_news_collect` VALUES (13, 9, 7, 1539151250, 1539151250, 1);
INSERT INTO `cl_news_collect` VALUES (14, 7, 7, 1539153154, 1539153154, 1);
INSERT INTO `cl_news_collect` VALUES (15, 8, 2, 1539239225, 1539239226, 0);
INSERT INTO `cl_news_collect` VALUES (16, 9, 2, 1539239236, 1539239236, 0);
INSERT INTO `cl_news_collect` VALUES (17, 7, 6, 1539395455, 1539587285, 0);
INSERT INTO `cl_news_collect` VALUES (18, 7, 17, 1539768393, 1539769203, 0);
INSERT INTO `cl_news_collect` VALUES (19, 8, 11, 1539829318, 1539829318, 1);

-- ----------------------------
-- Table structure for cl_news_reply
-- ----------------------------
DROP TABLE IF EXISTS `cl_news_reply`;
CREATE TABLE `cl_news_reply`  (
  `reply_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL COMMENT '关联咨询id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `type` tinyint(1) NOT NULL COMMENT '评论类型 1=>利好 2=>利空',
  `content` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论内容',
  `crate_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '评论状态  1=>正常  0=>删除or禁用',
  PRIMARY KEY (`reply_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '咨询评论表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_news_reply
-- ----------------------------
INSERT INTO `cl_news_reply` VALUES (1, 9, 2, 1, '', NULL, 1539239272, 1);
INSERT INTO `cl_news_reply` VALUES (2, 7, 2, 1, '', NULL, 1539079960, 1);
INSERT INTO `cl_news_reply` VALUES (3, 8, 7, 1, '', NULL, 1539244489, 1);
INSERT INTO `cl_news_reply` VALUES (4, 7, 3, 2, '', NULL, 1539153620, 1);
INSERT INTO `cl_news_reply` VALUES (5, 8, 3, 2, '', NULL, 1539153633, 1);
INSERT INTO `cl_news_reply` VALUES (6, 8, 6, 1, '', NULL, 1539333858, 1);
INSERT INTO `cl_news_reply` VALUES (7, 7, 17, 1, '', NULL, 1539768455, 1);
INSERT INTO `cl_news_reply` VALUES (8, 8, 11, 1, '', NULL, 1539833759, 1);
INSERT INTO `cl_news_reply` VALUES (9, 7, 11, 2, '', NULL, 1539846775, 1);

-- ----------------------------
-- Table structure for cl_opinion
-- ----------------------------
DROP TABLE IF EXISTS `cl_opinion`;
CREATE TABLE `cl_opinion`  (
  `oid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '反馈用户id',
  `e_mail` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '邮箱',
  `content` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已查看反馈 1 => 已查看 0 => 未查看',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否处理 1 => 已处理     0 =>未处理',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '意见反馈时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '回复时间',
  PRIMARY KEY (`oid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户反馈记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_opinion
-- ----------------------------
INSERT INTO `cl_opinion` VALUES (1, 3, '4155433@qq.com', '继续你在哪住男生女生', 1, 1, 1538962146, 1540863712);
INSERT INTO `cl_opinion` VALUES (2, 6, '43667880@qq.com', '刚刚爸爸吧', 0, 0, 1538999342, 1538999342);

-- ----------------------------
-- Table structure for cl_reason
-- ----------------------------
DROP TABLE IF EXISTS `cl_reason`;
CREATE TABLE `cl_reason`  (
  `report_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '举报者user_id',
  `role_id` int(11) NOT NULL COMMENT '举报者role_id',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '举报理由',
  `detail` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '详细内容  120字以内',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '举报类型 1=>房间  2=>角色',
  `imgs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '举报图片  最多三张  每张图片已[]分开',
  `id` int(11) NOT NULL COMMENT '被举报角色或房间id',
  `status` tinyint(1) NOT NULL COMMENT '状态  0=>未处理  1=>举报成功（封禁房间） 2=>驳回  ',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '举报时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`report_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '房间及角色举报记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_reason
-- ----------------------------
INSERT INTO `cl_reason` VALUES (1, 2, 2, '', '请详细描述被举报对象的恶意行为........', 2, '', 7, 0, 1538117517, NULL);
INSERT INTO `cl_reason` VALUES (2, 4, 4, '涉黄', '请详细描述被举报对象的恶意行为........', 2, 'http://file.51soha.com//21b61201809300135053793.jpg', 23, 0, 1538242506, NULL);
INSERT INTO `cl_reason` VALUES (3, 9, 8, '打广告', '啦啦啦啦啦', 1, '', 10, 0, 1538475169, NULL);
INSERT INTO `cl_reason` VALUES (4, 9, 8, '涉黄', '啊啊啊啊啊', 2, '', 18, 0, 1538979466, NULL);
INSERT INTO `cl_reason` VALUES (5, 1, 7, '', '请详细描述被举报对象的恶意行为........', 2, '', 27, 0, 1538981151, NULL);
INSERT INTO `cl_reason` VALUES (6, 6, 12, '涉黄', '非法 vvv 个', 2, 'http://file.51soha.com/Image201810081538998401VCSBUIKAVTHK.jpg', 8, 0, 1538998402, NULL);
INSERT INTO `cl_reason` VALUES (7, 11, 10, '口嗨', '测试', 2, 'http://file.51soha.com/Image201810091539053374JZVJRWXRNFCB.jpg[]http://file.51soha.com/Image201810091539053374SBYMHXBOXIJP.jpg[]http://file.51soha.com/Image201810091539053374VLGVNLBHJSXD.jpg', 12, 0, 1539053375, NULL);
INSERT INTO `cl_reason` VALUES (8, 2, 2, '', '请详细描述被举报对象的恶意行为....', 2, '', 10, 0, 1539077253, NULL);
INSERT INTO `cl_reason` VALUES (9, 11, 10, '口嗨', '阿胶阿胶', 2, 'http://file.51soha.com/Image201810091539077276JBYRYOFMPPCF.jpg', 10, 0, 1539077276, NULL);
INSERT INTO `cl_reason` VALUES (10, 1, 7, '', '请详细描述被举报对象的恶意行为........', 2, '', 3, 0, 1539080615, NULL);
INSERT INTO `cl_reason` VALUES (11, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228891, NULL);
INSERT INTO `cl_reason` VALUES (12, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (13, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (14, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (15, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (16, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (17, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228894, NULL);
INSERT INTO `cl_reason` VALUES (18, 11, 10, '涉黄', 'emmm', 1, '', 2, 1, 1539228895, NULL);
INSERT INTO `cl_reason` VALUES (19, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228895, NULL);
INSERT INTO `cl_reason` VALUES (20, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228895, NULL);
INSERT INTO `cl_reason` VALUES (21, 11, 10, '涉黄', 'emmm', 1, '', 2, 0, 1539228895, NULL);
INSERT INTO `cl_reason` VALUES (22, 6, 12, '口嗨', '把你那看看看\n', 2, 'http://file.51soha.com/Image201810111539237047TCZWLTYWNZMV.jpg', 3, 0, 1539237047, NULL);

-- ----------------------------
-- Table structure for cl_right
-- ----------------------------
DROP TABLE IF EXISTS `cl_right`;
CREATE TABLE `cl_right`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `act` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '方法',
  `control` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '控制器',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `update_time` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 160 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '后台菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_right
-- ----------------------------
INSERT INTO `cl_right` VALUES (55, '用户管理', 0, '', '', 'user', 1, '2018-04-14 10:16:14', 2, NULL, NULL);
INSERT INTO `cl_right` VALUES (56, '用户列表', 55, '', 'index', 'user', 1, '2018-04-14 10:16:30', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (59, '系统管理', 0, '', '', 'rule', 1, '2018-04-14 10:18:21', 11, NULL, NULL);
INSERT INTO `cl_right` VALUES (60, '权限管理', 59, '', 'index', 'rule', 1, '2018-04-14 10:18:50', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (61, '用户组管理', 59, '', 'group', 'rule', 1, '2018-04-14 10:19:18', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (62, '管理员列表', 59, '', 'admin_user_list', 'rule', 1, '2018-04-14 10:23:47', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (63, '菜单管理', 59, '', 'index', 'nav', 1, '2018-04-14 10:24:06', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (76, '登录日志', 55, '', 'log', 'user', 1, '2018-05-14 09:10:29', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (94, '系统设置', 59, '', 'money', 'rule', 1, '2018-06-06 10:23:07', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (111, '轮播图管理', 0, '', '', 'banner', 1, '2018-07-26 17:11:38', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (112, '轮播图列表', 111, '', 'index', 'banner', 1, '2018-07-26 17:11:59', 0, 1539570305, NULL);
INSERT INTO `cl_right` VALUES (113, '轮播图分类', 111, '', 'cate', 'banner', 1, '2018-07-26 17:12:12', 0, 1539570291, NULL);
INSERT INTO `cl_right` VALUES (114, '礼物管理', 0, '', '', 'gift', 1, '2018-07-27 14:21:13', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (116, '礼物配置', 114, '', 'index', 'gift', 1, '2018-07-27 14:24:58', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (117, '礼物记录', 114, '', 'gift_record_list', 'gift', 1, '2018-07-27 14:25:59', 0, 1538020327, NULL);
INSERT INTO `cl_right` VALUES (144, '资金管理', 0, '', '', 'money', 1, '2018-08-28 11:37:09', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (120, '红包管理', 0, '', '', 'red_package', 1, '2018-07-27 15:34:40', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (121, '红包发送记录', 120, '', 'index', 'red_package', 1, '2018-07-27 15:35:27', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (122, '编辑红包发送记录', 120, '', 'edit', 'red_package', 0, '2018-07-27 16:49:24', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (123, '房间管理', 0, '', '', 'play', 1, '2018-07-30 17:46:23', 0, 1533190305, NULL);
INSERT INTO `cl_right` VALUES (124, '分类管理', 123, '', 'cate', 'play', 1, '2018-07-30 17:47:29', 0, 1539158778, NULL);
INSERT INTO `cl_right` VALUES (126, '房间列表', 123, '', 'index', 'play', 1, '2018-07-31 16:50:11', 0, 1533190325, NULL);
INSERT INTO `cl_right` VALUES (127, '咨讯管理', 0, '', '', 'news', 1, '2018-07-31 17:28:49', 0, 1533030216, NULL);
INSERT INTO `cl_right` VALUES (128, '咨讯列表', 127, '', 'index', 'news', 1, '2018-07-31 17:29:54', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (129, '咨讯编辑', 127, '', 'edit', 'news', 0, '2018-07-31 17:30:34', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (130, '拍卖管理', 0, '', '', 'sale_success', 1, '2018-07-31 18:01:51', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (131, '拍卖管理', 130, '', 'index', 'sale_success', 1, '2018-07-31 18:02:11', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (132, '回放管理', 0, '', '', 'vod', 1, '2018-08-01 11:27:39', 0, 1533094086, NULL);
INSERT INTO `cl_right` VALUES (133, '视频列表', 132, '', 'index', 'vod', 1, '2018-08-01 11:28:21', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (134, '分类管理', 132, '', 'cate', 'play', 1, '2018-08-01 11:32:42', 0, 1539158791, NULL);
INSERT INTO `cl_right` VALUES (135, '系统消息', 0, '', '', 'message', 1, '2018-08-06 11:18:12', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (136, '系统消息列表', 135, '', 'index', 'message', 1, '2018-08-06 11:19:14', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (137, '发布系统消息', 135, '', 'add', 'message', 1, '2018-08-06 11:20:19', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (138, '编辑系统消息', 135, '', 'edit', 'message', 0, '2018-08-06 11:21:03', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (139, '认证管理', 0, '', '', '', 1, '2018-08-08 16:56:02', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (140, '官方认证管理', 139, '', 'index', 'role_check', 1, '2018-08-08 16:56:34', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (141, '实名认证管理', 139, '', 'index', 'user_check', 1, '2018-08-08 16:56:54', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (143, '用户反馈', 55, '', 'opinion', 'message', 1, '2018-08-13 16:22:58', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (145, '提现申请', 144, '', 'cash', 'money', 1, '2018-08-28 11:37:28', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (146, '充值记录', 144, '', 'index', 'money', 1, '2018-08-30 17:46:37', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (149, '参数配置', 59, '', 'explain', 'rule', 1, '2018-09-05 17:24:25', 0, 1536644970, NULL);
INSERT INTO `cl_right` VALUES (148, '统计管理', 144, '', 'map', 'money', 1, '2018-09-05 15:44:59', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (150, '回放空间过期列表', 132, '', 'expired_list', 'vod', 1, '2018-09-07 16:03:16', 0, 1536307413, NULL);
INSERT INTO `cl_right` VALUES (151, '举报管理', 0, '', '', 'report', 1, '2018-09-11 10:27:34', 0, 1536632886, NULL);
INSERT INTO `cl_right` VALUES (152, '举报列表', 151, '', 'index', 'report', 1, '2018-09-11 10:28:47', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (153, '版本控制', 59, '', 'version', 'rule', 1, '2018-09-21 09:43:19', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (154, '后台拍卖管理', 130, '', 'admin', 'sale_success', 1, '2018-10-12 13:41:34', 0, NULL, NULL);
INSERT INTO `cl_right` VALUES (155, '平台流水', 144, '', 'stream', 'money', 1, '2018-10-13 09:29:12', 0, NULL, NULL);

-- ----------------------------
-- Table structure for cl_room
-- ----------------------------
DROP TABLE IF EXISTS `cl_room`;
CREATE TABLE `cl_room`  (
  `room_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id 直播间id 同时作为聊天室id',
  `role_id` int(11) NOT NULL COMMENT '房主id\r\n',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `room_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房间名',
  `top` tinyint(1) NOT NULL COMMENT '是否推荐',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '详情',
  `is_close` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否处于全员禁言状态',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '封面图片',
  `cid` tinyint(4) NOT NULL COMMENT '直播分类id',
  `official` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为官方认证  1=>是  0=>否',
  `VIP` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为vip房间  1=>是  0=>否',
  `money` int(10) NOT NULL DEFAULT 0 COMMENT 'VIP房间付费金额',
  `is_charge` int(1) NOT NULL DEFAULT 0 COMMENT '房间是否收费',
  `brief` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简介',
  `fans` int(11) NOT NULL DEFAULT 0 COMMENT '粉丝数量',
  `time` datetime NULL DEFAULT NULL,
  `sale_status` tinyint(1) NULL DEFAULT NULL COMMENT '拍卖状态(0=>由后台创建1=>由搜索创建)',
  `play_status` tinyint(1) NOT NULL COMMENT '房间直播(活动)状态 1=>开启 0=>关闭  2=>预告',
  `is_mobile` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否为手机直播状态',
  `start_time` int(11) NOT NULL COMMENT '当前持有人持有开始时间',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '房间状态 1正常 0=>封禁 2=>拍卖中',
  PRIMARY KEY (`room_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_room
-- ----------------------------
INSERT INTO `cl_room` VALUES (1, 17, 3, '小松的第一个直播间', 1, '小松的第一个直播间', 0, 'http://file.51soha.com/1538114357622.png', 9, 0, 0, 0, 0, '', 2, NULL, 1, 0, 1, 1540288321, 1538114397, 1538208833, 1);
INSERT INTO `cl_room` VALUES (2, 2, 2, '区块链直播区块链直播', 1, '我是一颗自傲小小鸟', 1, 'http://file.51soha.com/1539073729237.png', 12, 0, 1, 1, 1, '', 0, NULL, 1, 0, 1, 1538114964, 1538114964, 1539238605, 0);
INSERT INTO `cl_room` VALUES (3, 3, 7, '阿里健康路哦哦哦哦哦哦哦哦哦', 0, '看看咯', 1, 'http://file.51soha.com//Image/20180928/1538116109KWVIULOCHDQC.jpg', 12, 0, 1, 100, 1, NULL, 4, NULL, 1, 0, 1, 1538115878, 1538115878, 1539239220, 0);
INSERT INTO `cl_room` VALUES (4, 5, 8, '你来啊啊啊啊啊啊啊啊啊', 0, '加工费', 1, 'http://file.51soha.com/1538239075496.png', 9, 0, 0, 0, 0, NULL, 0, NULL, 1, 0, 1, 1538116092, 1538116092, 1538243232, 0);
INSERT INTO `cl_room` VALUES (5, 4, 4, '一张图 | 给你最清晰的区块链交易结构！', 0, '一张图 | 给你最清晰的区块链交易结构！', 0, 'http://file.51soha.com//6d98d201810141103028767.png', 12, 0, 0, 0, 0, '一张图 | 给你最清晰的区块链交易结构！', 5, NULL, 1, 1, 1, 1538207821, 1538116178, 1539486184, 1);
INSERT INTO `cl_room` VALUES (6, 7, 1, '啦啦啦啦可口可乐了看看了', 0, 'high哈哈哈', 0, 'http://file.51soha.com/1538116692975.png', 14, 0, 1, 0, 0, NULL, 2, NULL, 1, 0, 1, 1538116705, 1538116705, 1538123906, 0);
INSERT INTO `cl_room` VALUES (7, 8, 9, '小小急急急急急急急急', 0, '啦啦啦啦啦啦啦啦', 1, 'http://file.51soha.com//Image201810021538467742YKZANYBKYGSE.jpg', 9, 0, 1, 0, 0, NULL, 3, NULL, 1, 0, 1, 1538117327, 1538117327, 1539241163, 0);
INSERT INTO `cl_room` VALUES (8, 9, 10, '区块链的成本', 0, '区块链的成本', 1, 'http://file.51soha.com/1538117321250.png', 14, 0, 0, 0, 0, '区块链的成本', 2, NULL, 1, 0, 1, 1538117366, 1538117366, 1539510551, 1);
INSERT INTO `cl_room` VALUES (9, 10, 11, '区块链商场直播抽奖间', 0, 'emmmm', 1, 'http://file.51soha.com/1538121145455.png', 12, 0, 1, 20, 1, NULL, 5, NULL, 1, 0, 1, 1538121170, 1538121170, 1538121170, 1);
INSERT INTO `cl_room` VALUES (10, 17, 3, '最近很火的区块链到底是什么，一张图让你秒懂！', 0, '最近很火的区块链到底是什么，一张图让你秒懂！', 0, 'http://file.51soha.com//86b39201810141104468753.jpeg', 9, 0, 0, 0, 0, '最近很火的区块链到底是什么，一张图让你秒懂！', 3, NULL, 1, 0, 0, 1540542961, 1538121439, 1539486287, 1);
INSERT INTO `cl_room` VALUES (11, 10, 11, 'IBM Blockchain Platform', 0, 'IBM Blockchain Platform', 1, 'http://file.51soha.com//394e6201810141133513323.png', 9, 0, 1, 100, 1, 'IBM Blockchain Platform', 1, NULL, 1, 0, 1, 1538207882, 1538121442, 1539510483, 1);
INSERT INTO `cl_room` VALUES (12, 18, 13, '区块链为智慧商业而生', 1, '区块链为智慧商业而生', 0, 'http://file.51soha.com//a848e201810141133268634.png', 14, 0, 0, 0, 0, '区块链为智慧商业而生', 3, NULL, 1, 0, 1, 1538126926, 1538126926, 1539510455, 1);
INSERT INTO `cl_room` VALUES (13, 17, 3, '撰写白皮书', 1, '撰写白皮书', 0, 'http://file.51soha.com//751f5201810141133095170.png', 15, 0, 1, 0, 0, '撰写白皮书', 1, NULL, 1, 0, 0, 1540626541, 1538200145, 1539566662, 1);
INSERT INTO `cl_room` VALUES (14, 23, 18, '区块链简介', 0, '区块链简介', 0, 'http://file.51soha.com//1d61c201810141115354680.png', 9, 0, 0, 0, 0, '区块链简介', 3, NULL, 1, 0, 1, 1538216656, 1538216656, 1539486939, 1);
INSERT INTO `cl_room` VALUES (15, 6, 5, '挖矿模式系统开发', 0, '2222222222', 0, 'http://file.51soha.com//555db201810141132485927.', 14, 0, 0, 0, 0, '挖矿模式系统开发', 4, NULL, 1, 0, 0, 1538235952, 1538235952, 1539510408, 1);
INSERT INTO `cl_room` VALUES (16, 12, 6, '教你用区块链防止一篇文章的消失', 0, '教你用区块链防止一篇文章的消失', 0, 'http://file.51soha.com//9e84f201810141100366111.png', 14, 0, 1, 100, 1, '教你用区块链防止一篇文章的消失', 2, NULL, 1, 1, 1, 1538986677, 1538986677, 1539486038, 1);
INSERT INTO `cl_room` VALUES (17, 10, 11, '区块链从概念到底层技术：革命的基础。', 0, '区块链从概念到底层技术：革命的基础。', 0, 'http://file.51soha.com//6049220181014110801938.jpg', 9, 0, 1, 100, 1, '区块链从概念到底层技术：革命的基础。', 0, NULL, 1, 0, 1, 1539320941, 1538998547, 1539486484, 1);
INSERT INTO `cl_room` VALUES (18, 41, 11, '区块链保安', 0, '', 0, 'http://file.51soha.com//4c127201810141132292590.jpeg', 9, 0, 1, 0, 0, '', 3, NULL, 1, 0, 1, 1539334861, 1539054602, 1539487951, 1);
INSERT INTO `cl_room` VALUES (19, 10, 11, '区块链新秀：锐角币', 0, '区块链新秀：锐角币', 1, 'http://file.51soha.com//cab42201810141111514395.jpg', 10, 0, 1, 0, 0, '区块链新秀：锐角币', 0, NULL, 1, 0, 1, 1539054841, 1539054616, 1539595979, 1);
INSERT INTO `cl_room` VALUES (20, 12, 6, '百度图腾', 0, '百度图腾', 0, 'http://file.51soha.com//4f068201810141109071993.', 15, 0, 0, 0, 0, '百度图腾', 1, NULL, 1, 2, 1, 1539320881, 1539074230, 1539566676, 1);
INSERT INTO `cl_room` VALUES (21, 10, 11, '区块链现状', 0, '区块链现状', 0, 'http://file.51soha.com//e976d201810141101377413.png', 10, 0, 1, 100, 1, '区块链现状', 1, NULL, 1, 0, 0, 1539240482, 1539079436, 1539566625, 1);
INSERT INTO `cl_room` VALUES (22, 10, 11, '炒币模式系统开发', 0, '', 1, 'http://file.51soha.com//e90ea201810141131363062.jpg', 9, 0, 0, 0, 0, '', 0, NULL, 1, 0, 1, 1539134821, 1539134641, 1539510384, 1);
INSERT INTO `cl_room` VALUES (23, 12, 6, '区块链游戏大趋势', 0, '玩游戏', 0, 'http://file.51soha.com//8fea5201810141745316727.png', 14, 0, 1, 0, 0, '', 3, NULL, 1, 2, 1, 1539234443, 1539234443, 1540631120, 1);
INSERT INTO `cl_room` VALUES (24, 2, 2, '块链生态图谱讲解', 0, '区块链生态图谱讲解', 0, 'http://file.51soha.com//848b4201810141131251443.png', 9, 0, 0, 0, 0, '区块链生态图谱讲解', 0, NULL, 1, 0, 0, 1539236641, 1539236454, 1539510213, 1);
INSERT INTO `cl_room` VALUES (25, 10, 11, '区块链生态图谱讲解', 0, '区块链生态图谱讲解', 1, 'http://file.51soha.com//1bcff201810141106272882.png', 9, 0, 1, 100, 1, '区块链生态图谱讲解', 0, NULL, 1, 0, 1, 1539236641, 1539236454, 1539486390, 1);
INSERT INTO `cl_room` VALUES (26, 2, 2, '区块链生态图谱讲解	', 0, '区块链生态图谱讲解	', 0, 'http://file.51soha.com//87cba201810141131117357.png', 9, 0, 0, 0, 0, '区块链生态图谱讲解	', 1, NULL, 1, 0, 0, 1539239461, 1539238002, 1539510131, 1);
INSERT INTO `cl_room` VALUES (27, 31, 29, '成功并不是偶然', 0, '呦～～～～～～～', 0, 'http://file.51soha.com//3f730201810141131018143.jpg', 14, 0, 0, 0, 0, '成功并不是偶然', 0, NULL, 1, 0, 1, 1540027021, 1539242070, 1539939822, 1);
INSERT INTO `cl_room` VALUES (28, 40, 38, '链上区块，共享红利', 0, '简介', 0, 'http://file.51soha.com//358d5201810141130417829.png', 14, 0, 1, 0, 0, '', 2, NULL, 1, 0, 1, 1539246086, 1539246086, 1539510294, 1);
INSERT INTO `cl_room` VALUES (29, 29, 17, '比特币', 0, '', 0, 'http://file.51soha.com//450da201810141130167338.jpeg', 9, 0, 0, 0, 0, '', 1, NULL, NULL, 0, 0, 1539486181, 1539399773, 1539510192, 1);
INSERT INTO `cl_room` VALUES (30, 10, 11, '区块链生态图谱讲结', 0, '区块链生态图谱讲解	', 0, 'http://file.51soha.com//8c2c8201810141130266297.jpg', 9, 0, 0, 0, 0, '区块链生态图谱讲解	', 2, NULL, NULL, 0, 1, 1539498301, 1539411843, 1539510172, 1);
INSERT INTO `cl_room` VALUES (31, 29, 17, '区块链如何改变我们的生活', 0, '区块链如何改变我们的生活', 0, 'http://file.51soha.com//3ad6e201810141059093421.png', 15, 0, 1, 0, 0, '区块链如何改变我们的生活', 3, NULL, NULL, 1, 1, 1539411964, 1539411964, 1539566611, 1);
INSERT INTO `cl_room` VALUES (32, 12, 6, '区块链大热下的“人才荒”', 0, '区块链大热下的“人才荒”', 0, 'http://file.51soha.com//2648e201810141112466143.jpg', 15, 0, 0, 0, 0, '区块链大热下的“人才荒”', 1, NULL, NULL, 2, 1, 1540027321, 1539417806, 1540458686, 1);
INSERT INTO `cl_room` VALUES (33, 35, 33, '区块链的现状和未来', 0, '区块链的现状和未来', 0, 'http://file.51soha.com//249d7201810141110111401.png', 14, 0, 0, 0, 0, '区块链的现状和未来', 0, NULL, NULL, 0, 0, 1539418576, 1539418576, 1539486612, 1);
INSERT INTO `cl_room` VALUES (34, 41, 11, '拍卖', 0, '大家好', 0, 'http://file.51soha.com/1539848560278.png', 9, 0, 0, 0, 0, NULL, 5, NULL, NULL, 0, 1, 1539683521, 1539597075, 1539937806, 1);
INSERT INTO `cl_room` VALUES (35, 31, 29, '欢乐的区块链', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 1539846602, 1539760177, NULL, 1);
INSERT INTO `cl_room` VALUES (36, 12, 6, '谢谢学长', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 1540467601, 1540381152, NULL, 1);
INSERT INTO `cl_room` VALUES (37, 12, 6, '啊啊啊', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 2, 0, 1540467601, 1540381176, NULL, 1);
INSERT INTO `cl_room` VALUES (38, 17, 3, '小松', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 1540626001, 1540539545, NULL, 1);
INSERT INTO `cl_room` VALUES (39, 17, 3, '小兰', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 1, 1540539962, 1540539778, NULL, 1);
INSERT INTO `cl_room` VALUES (40, 17, 3, '小松sky', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 1, 1540540141, 1540539947, NULL, 1);
INSERT INTO `cl_room` VALUES (41, 17, 3, '绝地求生', 0, NULL, 0, 'http://file.51soha.com/default_room.png', 9, 0, 0, 0, 0, NULL, 0, NULL, NULL, 0, 0, 1540804441, 1540804231, NULL, 1);

-- ----------------------------
-- Table structure for cl_room_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `cl_room_blacklist`;
CREATE TABLE `cl_room_blacklist`  (
  `bid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '被封的角色id',
  `create_time` int(11) NOT NULL COMMENT '第一次被封禁时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '封禁状态  1=>正常  0=>解除或已删除',
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '房间黑名单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cl_room_follow
-- ----------------------------
DROP TABLE IF EXISTS `cl_room_follow`;
CREATE TABLE `cl_room_follow`  (
  `follow_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `room_id` int(11) NOT NULL COMMENT '关注房间id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>正常普通成员  0 =>取消关注=>状态为0==游客  2=>房间管理员',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`follow_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 465 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间关注表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_room_follow
-- ----------------------------
INSERT INTO `cl_room_follow` VALUES (1, 0, 0, 0, 1538114951, 1538114951);
INSERT INTO `cl_room_follow` VALUES (2, 1, 1, 1, 1538114992, NULL);
INSERT INTO `cl_room_follow` VALUES (3, 2, 2, 0, 1538116200, 1539074480);
INSERT INTO `cl_room_follow` VALUES (4, 8, 0, 0, 1538116205, 1538116205);
INSERT INTO `cl_room_follow` VALUES (5, 0, 8, 0, 1538116273, 1538116273);
INSERT INTO `cl_room_follow` VALUES (6, 4, 0, 0, 1538116718, 1538116718);
INSERT INTO `cl_room_follow` VALUES (7, 7, 2, 1, 1538117091, 1539073934);
INSERT INTO `cl_room_follow` VALUES (8, 4, 5, 0, 1538117094, 1538200747);
INSERT INTO `cl_room_follow` VALUES (9, 6, 5, 1, 1538117426, 1538238115);
INSERT INTO `cl_room_follow` VALUES (10, 0, 4, 0, 1538118057, 1538118057);
INSERT INTO `cl_room_follow` VALUES (11, 14, 0, 0, 1538118150, 1538118150);
INSERT INTO `cl_room_follow` VALUES (12, 7, 5, 1, 1538118464, 1538238090);
INSERT INTO `cl_room_follow` VALUES (13, 1, 5, 1, 1538118689, 1538238032);
INSERT INTO `cl_room_follow` VALUES (14, 9, 5, 1, 1538119615, 1538238009);
INSERT INTO `cl_room_follow` VALUES (15, 5, 5, 1, 1538119957, 1538237690);
INSERT INTO `cl_room_follow` VALUES (16, 3, 0, 0, 1538127241, 1538127241);
INSERT INTO `cl_room_follow` VALUES (17, 3, 8, 2, 1538127315, 1538225771);
INSERT INTO `cl_room_follow` VALUES (18, 14, 8, 0, 1538193739, 1538193739);
INSERT INTO `cl_room_follow` VALUES (19, 3, 4, 0, 1538207899, 1538207899);
INSERT INTO `cl_room_follow` VALUES (20, 3, 7, 1, 1538209515, 1539241123);
INSERT INTO `cl_room_follow` VALUES (21, 0, 14, 0, 1538214692, 1538214692);
INSERT INTO `cl_room_follow` VALUES (22, 23, 2, 1, 1538215418, 1539073935);
INSERT INTO `cl_room_follow` VALUES (23, 23, 9, 1, 1538215660, 1538215667);
INSERT INTO `cl_room_follow` VALUES (24, 0, 1, 0, 1538216878, 1538216878);
INSERT INTO `cl_room_follow` VALUES (25, 3, 1, 0, 1538216951, 1538216951);
INSERT INTO `cl_room_follow` VALUES (26, 3, 14, 0, 1538220287, 1538220287);
INSERT INTO `cl_room_follow` VALUES (27, 0, 3, 0, 1538230198, 1538230198);
INSERT INTO `cl_room_follow` VALUES (28, 0, 6, 0, 1538230207, 1538230207);
INSERT INTO `cl_room_follow` VALUES (29, 6, 3, 2, 1538230260, 1538986977);
INSERT INTO `cl_room_follow` VALUES (30, 7, 3, 2, 1538230332, 1538986992);
INSERT INTO `cl_room_follow` VALUES (31, 8, 4, 0, 1538230337, 1538230337);
INSERT INTO `cl_room_follow` VALUES (32, 9, 14, 2, 1538230353, 1540605041);
INSERT INTO `cl_room_follow` VALUES (33, 9, 7, 0, 1538230361, 1538230361);
INSERT INTO `cl_room_follow` VALUES (34, 10, 8, 1, 1538231373, 1539913520);
INSERT INTO `cl_room_follow` VALUES (35, 10, 6, 1, 1538231382, 1539398207);
INSERT INTO `cl_room_follow` VALUES (36, 9, 1, 0, 1538231436, 1538231436);
INSERT INTO `cl_room_follow` VALUES (37, 9, 13, 2, 1538231440, 1538241127);
INSERT INTO `cl_room_follow` VALUES (38, 3, 13, 0, 1538231823, 1539071183);
INSERT INTO `cl_room_follow` VALUES (39, 8, 13, 1, 1538232119, 1538477362);
INSERT INTO `cl_room_follow` VALUES (40, 7, 1, 0, 1538232449, 1538232449);
INSERT INTO `cl_room_follow` VALUES (41, 7, 13, 0, 1538232566, 1538232566);
INSERT INTO `cl_room_follow` VALUES (42, 9, 3, 2, 1538235445, 1538986994);
INSERT INTO `cl_room_follow` VALUES (43, 3, 11, 0, 1538235672, 1538235672);
INSERT INTO `cl_room_follow` VALUES (44, 3, 3, 1, 1538235857, 1538987000);
INSERT INTO `cl_room_follow` VALUES (45, 17, 3, 1, 1538235863, 1538986999);
INSERT INTO `cl_room_follow` VALUES (46, 17, 14, 1, 1538235879, NULL);
INSERT INTO `cl_room_follow` VALUES (47, 8, 1, 0, 1538236087, 1538477038);
INSERT INTO `cl_room_follow` VALUES (48, 3, 5, 0, 1538236114, 1538236114);
INSERT INTO `cl_room_follow` VALUES (49, 17, 5, 1, 1538236151, NULL);
INSERT INTO `cl_room_follow` VALUES (50, 5, 6, 0, 1538236317, 1538236317);
INSERT INTO `cl_room_follow` VALUES (51, 5, 13, 0, 1538236328, 1538236328);
INSERT INTO `cl_room_follow` VALUES (52, 3, 9, 0, 1538236461, 1538236461);
INSERT INTO `cl_room_follow` VALUES (53, 20, 1, 0, 1538236882, 1538236882);
INSERT INTO `cl_room_follow` VALUES (54, 20, 14, 0, 1538236936, 1538236936);
INSERT INTO `cl_room_follow` VALUES (55, 20, 4, 0, 1538237189, 1538237189);
INSERT INTO `cl_room_follow` VALUES (56, 20, 2, 1, 1538237206, 1539073936);
INSERT INTO `cl_room_follow` VALUES (57, 20, 8, 0, 1538237225, 1538237225);
INSERT INTO `cl_room_follow` VALUES (58, 20, 15, 0, 1538237228, 1538237228);
INSERT INTO `cl_room_follow` VALUES (59, 20, 12, 0, 1538237233, 1538237233);
INSERT INTO `cl_room_follow` VALUES (60, 20, 5, 0, 1538237240, 1538237240);
INSERT INTO `cl_room_follow` VALUES (61, 10, 1, 1, 1538237736, 1539596254);
INSERT INTO `cl_room_follow` VALUES (62, 10, 5, 1, 1538237988, 1539136241);
INSERT INTO `cl_room_follow` VALUES (63, 10, 12, 1, 1538238056, 1539398203);
INSERT INTO `cl_room_follow` VALUES (64, 5, 15, 0, 1538238404, 1538238404);
INSERT INTO `cl_room_follow` VALUES (65, 4, 1, 0, 1538238823, 1538238823);
INSERT INTO `cl_room_follow` VALUES (66, 10, 14, 0, 1538238913, 1538238913);
INSERT INTO `cl_room_follow` VALUES (67, 4, 14, 1, 1538238913, 1538238915);
INSERT INTO `cl_room_follow` VALUES (68, 4, 15, 1, 1538238950, 1538238958);
INSERT INTO `cl_room_follow` VALUES (69, 18, 1, 0, 1538238966, 1538238966);
INSERT INTO `cl_room_follow` VALUES (70, 18, 14, 0, 1538238992, 1538238992);
INSERT INTO `cl_room_follow` VALUES (71, 18, 8, 0, 1538239013, 1538239013);
INSERT INTO `cl_room_follow` VALUES (72, 7, 14, 0, 1538239337, 1538239337);
INSERT INTO `cl_room_follow` VALUES (73, 10, 4, 0, 1538239455, 1538239455);
INSERT INTO `cl_room_follow` VALUES (74, 10, 13, 0, 1538239695, 1538239695);
INSERT INTO `cl_room_follow` VALUES (75, 5, 14, 0, 1538240743, 1538240743);
INSERT INTO `cl_room_follow` VALUES (76, 5, 4, 0, 1538240799, 1538240799);
INSERT INTO `cl_room_follow` VALUES (77, 5, 3, 0, 1538240805, 1538240805);
INSERT INTO `cl_room_follow` VALUES (78, 18, 10, 0, 1538243113, 1538243113);
INSERT INTO `cl_room_follow` VALUES (79, 5, 1, 0, 1538243287, 1538243287);
INSERT INTO `cl_room_follow` VALUES (80, 3, 10, 0, 1538243506, 1538243506);
INSERT INTO `cl_room_follow` VALUES (81, 4, 10, 0, 1538243572, 1538243572);
INSERT INTO `cl_room_follow` VALUES (82, 20, 13, 0, 1538246768, 1538246768);
INSERT INTO `cl_room_follow` VALUES (83, 7, 10, 0, 1538247894, 1538247894);
INSERT INTO `cl_room_follow` VALUES (84, 18, 3, 1, 1538248063, 1538986999);
INSERT INTO `cl_room_follow` VALUES (85, 10, 10, 1, 1538248117, 1539069262);
INSERT INTO `cl_room_follow` VALUES (86, 7, 7, 0, 1538248377, 1538248377);
INSERT INTO `cl_room_follow` VALUES (87, 1, 13, 0, 1538267196, 1538267196);
INSERT INTO `cl_room_follow` VALUES (88, 1, 9, 0, 1538267221, 1538267221);
INSERT INTO `cl_room_follow` VALUES (89, 23, 13, 0, 1538274076, 1538274076);
INSERT INTO `cl_room_follow` VALUES (90, 23, 1, 0, 1538274079, 1538274079);
INSERT INTO `cl_room_follow` VALUES (91, 23, 14, 1, 1538274081, 1539228878);
INSERT INTO `cl_room_follow` VALUES (92, 23, 5, 0, 1538274082, 1538274082);
INSERT INTO `cl_room_follow` VALUES (93, 25, 14, 1, 1538274299, NULL);
INSERT INTO `cl_room_follow` VALUES (94, 6, 1, 0, 1538276070, 1538276070);
INSERT INTO `cl_room_follow` VALUES (95, 6, 13, 0, 1538279418, 1538279418);
INSERT INTO `cl_room_follow` VALUES (96, 6, 14, 0, 1538283323, 1538283323);
INSERT INTO `cl_room_follow` VALUES (97, 24, 5, 0, 1538289115, 1538289115);
INSERT INTO `cl_room_follow` VALUES (98, 24, 4, 0, 1538294633, 1538294633);
INSERT INTO `cl_room_follow` VALUES (99, 24, 10, 0, 1538294635, 1538294635);
INSERT INTO `cl_room_follow` VALUES (100, 6, 10, 0, 1538294695, 1538294695);
INSERT INTO `cl_room_follow` VALUES (101, 24, 15, 0, 1538294781, 1538294781);
INSERT INTO `cl_room_follow` VALUES (102, 15, 13, 0, 1538302766, 1539067406);
INSERT INTO `cl_room_follow` VALUES (103, 15, 12, 0, 1538302931, 1538302931);
INSERT INTO `cl_room_follow` VALUES (104, 15, 14, 0, 1538303187, 1538303187);
INSERT INTO `cl_room_follow` VALUES (105, 9, 10, 0, 1538303756, 1538303756);
INSERT INTO `cl_room_follow` VALUES (106, 6, 7, 0, 1538303998, 1538303998);
INSERT INTO `cl_room_follow` VALUES (107, 9, 11, 0, 1538305083, 1538305083);
INSERT INTO `cl_room_follow` VALUES (108, 9, 12, 0, 1538305095, 1538305095);
INSERT INTO `cl_room_follow` VALUES (109, 15, 7, 0, 1538306401, 1538306401);
INSERT INTO `cl_room_follow` VALUES (110, 15, 1, 0, 1538306711, 1538306711);
INSERT INTO `cl_room_follow` VALUES (111, 9, 4, 0, 1538308562, 1538308562);
INSERT INTO `cl_room_follow` VALUES (112, 25, 1, 0, 1538385882, 1538385882);
INSERT INTO `cl_room_follow` VALUES (113, 25, 13, 0, 1538385888, 1538385888);
INSERT INTO `cl_room_follow` VALUES (114, 25, 10, 0, 1538385971, 1538385971);
INSERT INTO `cl_room_follow` VALUES (115, 25, 5, 0, 1538386407, 1538386407);
INSERT INTO `cl_room_follow` VALUES (116, 25, 6, 0, 1538386413, 1538386413);
INSERT INTO `cl_room_follow` VALUES (117, 25, 8, 0, 1538386415, 1538386415);
INSERT INTO `cl_room_follow` VALUES (118, 25, 4, 0, 1538441850, 1538441850);
INSERT INTO `cl_room_follow` VALUES (119, 8, 10, 1, 1538455200, 1538473310);
INSERT INTO `cl_room_follow` VALUES (120, 8, 7, 1, 1538458781, NULL);
INSERT INTO `cl_room_follow` VALUES (121, 9, 15, 0, 1538475821, 1538475821);
INSERT INTO `cl_room_follow` VALUES (122, 8, 15, 1, 1538477828, NULL);
INSERT INTO `cl_room_follow` VALUES (123, 1, 10, 0, 1538486723, 1538486723);
INSERT INTO `cl_room_follow` VALUES (124, 1, 6, 0, 1538486749, 1538486749);
INSERT INTO `cl_room_follow` VALUES (125, 18, 7, 0, 1538963055, 1538963055);
INSERT INTO `cl_room_follow` VALUES (126, 8, 11, 1, 1538978344, 1539756434);
INSERT INTO `cl_room_follow` VALUES (127, 6, 11, 0, 1538978707, 1538978707);
INSERT INTO `cl_room_follow` VALUES (128, 10, 7, 0, 1538979369, 1538979369);
INSERT INTO `cl_room_follow` VALUES (129, 6, 6, 0, 1538980093, 1538980093);
INSERT INTO `cl_room_follow` VALUES (130, 18, 5, 0, 1538980235, 1538980235);
INSERT INTO `cl_room_follow` VALUES (131, 26, 1, 0, 1538981867, 1538981867);
INSERT INTO `cl_room_follow` VALUES (132, 6, 8, 0, 1538983914, 1538983914);
INSERT INTO `cl_room_follow` VALUES (133, 9, 8, 0, 1538983983, 1539596984);
INSERT INTO `cl_room_follow` VALUES (134, 3, 15, 0, 1538985625, 1538985625);
INSERT INTO `cl_room_follow` VALUES (135, 14, 10, 0, 1538985653, 1538985653);
INSERT INTO `cl_room_follow` VALUES (136, 14, 15, 0, 1538985679, 1538985679);
INSERT INTO `cl_room_follow` VALUES (137, 19, 13, 0, 1538986037, 1538986037);
INSERT INTO `cl_room_follow` VALUES (138, 14, 13, 0, 1538986095, 1538986095);
INSERT INTO `cl_room_follow` VALUES (139, 26, 13, 0, 1538986172, 1538986172);
INSERT INTO `cl_room_follow` VALUES (140, 24, 13, 0, 1538986221, 1538986221);
INSERT INTO `cl_room_follow` VALUES (141, 18, 13, 0, 1538986304, 1538986304);
INSERT INTO `cl_room_follow` VALUES (142, 24, 7, 0, 1538986355, 1538986355);
INSERT INTO `cl_room_follow` VALUES (143, 14, 7, 0, 1538986485, 1538986485);
INSERT INTO `cl_room_follow` VALUES (144, 24, 14, 0, 1538986516, 1538986516);
INSERT INTO `cl_room_follow` VALUES (145, 14, 14, 0, 1538986701, 1538986701);
INSERT INTO `cl_room_follow` VALUES (146, 6, 16, 0, 1538986711, 1538986711);
INSERT INTO `cl_room_follow` VALUES (147, 14, 16, 0, 1538986714, 1538986714);
INSERT INTO `cl_room_follow` VALUES (148, 7, 16, 0, 1538987436, 1538987436);
INSERT INTO `cl_room_follow` VALUES (149, 1, 16, 0, 1538987523, 1538987523);
INSERT INTO `cl_room_follow` VALUES (150, 9, 16, 0, 1538987582, 1538987582);
INSERT INTO `cl_room_follow` VALUES (151, 18, 16, 0, 1538989090, 1538989090);
INSERT INTO `cl_room_follow` VALUES (152, 10, 16, 1, 1538989091, 1539136453);
INSERT INTO `cl_room_follow` VALUES (153, 14, 9, 0, 1538991665, 1538991665);
INSERT INTO `cl_room_follow` VALUES (154, 3, 16, 2, 1538998056, 1540779815);
INSERT INTO `cl_room_follow` VALUES (155, 6, 17, 0, 1538999003, 1538999003);
INSERT INTO `cl_room_follow` VALUES (156, 9, 17, 0, 1538999512, 1538999512);
INSERT INTO `cl_room_follow` VALUES (157, 7, 17, 0, 1539002476, 1539002476);
INSERT INTO `cl_room_follow` VALUES (158, 23, 8, 0, 1539004627, 1539004627);
INSERT INTO `cl_room_follow` VALUES (159, 12, 13, 0, 1539004636, 1539004636);
INSERT INTO `cl_room_follow` VALUES (160, 12, 17, 0, 1539004645, 1539004645);
INSERT INTO `cl_room_follow` VALUES (161, 12, 16, 2, 1539004766, 1539165757);
INSERT INTO `cl_room_follow` VALUES (162, 8, 16, 2, 1539004902, 1539165671);
INSERT INTO `cl_room_follow` VALUES (163, 12, 5, 0, 1539048304, 1539048311);
INSERT INTO `cl_room_follow` VALUES (164, 12, 14, 0, 1539048763, 1539048800);
INSERT INTO `cl_room_follow` VALUES (165, 17, 13, 0, 1539066475, 1539066475);
INSERT INTO `cl_room_follow` VALUES (166, 17, 1, 0, 1539066602, 1539066602);
INSERT INTO `cl_room_follow` VALUES (167, 17, 10, 0, 1539066605, 1539066605);
INSERT INTO `cl_room_follow` VALUES (168, 19, 10, 0, 1539066637, 1539066637);
INSERT INTO `cl_room_follow` VALUES (169, 19, 1, 0, 1539067005, 1539067005);
INSERT INTO `cl_room_follow` VALUES (170, 4, 3, 0, 1539067137, 1539067137);
INSERT INTO `cl_room_follow` VALUES (171, 15, 5, 0, 1539067560, 1539067560);
INSERT INTO `cl_room_follow` VALUES (172, 15, 15, 0, 1539067848, 1539067848);
INSERT INTO `cl_room_follow` VALUES (173, 15, 19, 1, 1539067942, 1539224522);
INSERT INTO `cl_room_follow` VALUES (174, 10, 15, 1, 1539069295, 1539398219);
INSERT INTO `cl_room_follow` VALUES (175, 2, 13, 0, 1539069527, 1539069527);
INSERT INTO `cl_room_follow` VALUES (176, 8, 5, 0, 1539069642, 1539069642);
INSERT INTO `cl_room_follow` VALUES (177, 19, 6, 0, 1539069978, 1539069978);
INSERT INTO `cl_room_follow` VALUES (178, 7, 6, 0, 1539069990, 1539069990);
INSERT INTO `cl_room_follow` VALUES (179, 2, 10, 1, 1539071670, 1539239036);
INSERT INTO `cl_room_follow` VALUES (180, 2, 5, 1, 1539071715, 1539077388);
INSERT INTO `cl_room_follow` VALUES (181, 3, 2, 1, 1539072461, 1539073937);
INSERT INTO `cl_room_follow` VALUES (182, 2, 1, 0, 1539072468, 1539072468);
INSERT INTO `cl_room_follow` VALUES (183, 3, 19, 1, 1539072488, 1539756013);
INSERT INTO `cl_room_follow` VALUES (184, 3, 18, 0, 1539072744, 1539072828);
INSERT INTO `cl_room_follow` VALUES (185, 10, 9, 1, 1539073114, 1539153044);
INSERT INTO `cl_room_follow` VALUES (186, 10, 2, 2, 1539073827, 1539074443);
INSERT INTO `cl_room_follow` VALUES (187, 10, 3, 1, 1539074322, 1539136344);
INSERT INTO `cl_room_follow` VALUES (188, 8, 8, 0, 1539075841, 1539075841);
INSERT INTO `cl_room_follow` VALUES (189, 10, 18, 1, 1539077765, 1539682399);
INSERT INTO `cl_room_follow` VALUES (190, 10, 19, 0, 1539077788, 1539682878);
INSERT INTO `cl_room_follow` VALUES (191, 2, 9, 1, 1539077804, 1539150673);
INSERT INTO `cl_room_follow` VALUES (192, 2, 18, 0, 1539077831, 1539077831);
INSERT INTO `cl_room_follow` VALUES (193, 8, 3, 0, 1539078397, 1539078397);
INSERT INTO `cl_room_follow` VALUES (194, 12, 18, 1, 1539078696, 1540548523);
INSERT INTO `cl_room_follow` VALUES (195, 23, 3, 0, 1539079070, 1539079070);
INSERT INTO `cl_room_follow` VALUES (196, 2, 15, 0, 1539080111, 1539080111);
INSERT INTO `cl_room_follow` VALUES (197, 10, 21, 1, 1539080168, 1539582401);
INSERT INTO `cl_room_follow` VALUES (198, 10, 11, 0, 1539080171, 1539080171);
INSERT INTO `cl_room_follow` VALUES (199, 8, 19, 1, 1539080311, 1539749159);
INSERT INTO `cl_room_follow` VALUES (200, 2, 19, 0, 1539080323, 1539226608);
INSERT INTO `cl_room_follow` VALUES (201, 9, 19, 0, 1539080517, 1539080517);
INSERT INTO `cl_room_follow` VALUES (202, 2, 7, 0, 1539080594, 1539080594);
INSERT INTO `cl_room_follow` VALUES (203, 2, 3, 1, 1539080600, 1539239989);
INSERT INTO `cl_room_follow` VALUES (204, 23, 16, 0, 1539081276, 1539081276);
INSERT INTO `cl_room_follow` VALUES (205, 12, 7, 1, 1539100756, 1539154137);
INSERT INTO `cl_room_follow` VALUES (206, 12, 4, 0, 1539102316, 1539102316);
INSERT INTO `cl_room_follow` VALUES (207, 12, 3, 0, 1539137365, 1539137365);
INSERT INTO `cl_room_follow` VALUES (208, 12, 10, 0, 1539137388, 1539137388);
INSERT INTO `cl_room_follow` VALUES (209, 3, 6, 0, 1539138255, 1539138255);
INSERT INTO `cl_room_follow` VALUES (210, 3, 20, 0, 1539138277, 1539138277);
INSERT INTO `cl_room_follow` VALUES (211, 12, 20, 0, 1539138539, 1539138539);
INSERT INTO `cl_room_follow` VALUES (212, 23, 19, 0, 1539138799, 1539138799);
INSERT INTO `cl_room_follow` VALUES (213, 12, 6, 0, 1539141108, 1539141108);
INSERT INTO `cl_room_follow` VALUES (214, 26, 3, 0, 1539142892, 1539142892);
INSERT INTO `cl_room_follow` VALUES (215, 26, 5, 0, 1539142901, 1539142901);
INSERT INTO `cl_room_follow` VALUES (216, 2, 12, 1, 1539151739, 1539154209);
INSERT INTO `cl_room_follow` VALUES (217, 19, 8, 0, 1539154161, 1539154161);
INSERT INTO `cl_room_follow` VALUES (218, 19, 9, 0, 1539154500, 1539154500);
INSERT INTO `cl_room_follow` VALUES (219, 19, 18, 0, 1539155241, 1539155241);
INSERT INTO `cl_room_follow` VALUES (220, 19, 19, 0, 1539155245, 1539155245);
INSERT INTO `cl_room_follow` VALUES (221, 8, 14, 0, 1539157382, 1539157382);
INSERT INTO `cl_room_follow` VALUES (222, 10, 22, 0, 1539159806, 1539159806);
INSERT INTO `cl_room_follow` VALUES (223, 2, 22, 1, 1539160477, 1539748827);
INSERT INTO `cl_room_follow` VALUES (224, 3, 12, 0, 1539160654, 1539160654);
INSERT INTO `cl_room_follow` VALUES (225, 3, 17, 0, 1539160670, 1539160670);
INSERT INTO `cl_room_follow` VALUES (226, 19, 7, 0, 1539160733, 1539160733);
INSERT INTO `cl_room_follow` VALUES (227, 9, 22, 0, 1539161337, 1539161337);
INSERT INTO `cl_room_follow` VALUES (228, 19, 22, 0, 1539161591, 1539161591);
INSERT INTO `cl_room_follow` VALUES (229, 15, 3, 0, 1539163104, 1539163104);
INSERT INTO `cl_room_follow` VALUES (230, 15, 10, 0, 1539163124, 1539163124);
INSERT INTO `cl_room_follow` VALUES (231, 15, 9, 1, 1539163739, 1539163876);
INSERT INTO `cl_room_follow` VALUES (232, 15, 22, 0, 1539163744, 1539163744);
INSERT INTO `cl_room_follow` VALUES (233, 15, 11, 0, 1539163751, 1539163751);
INSERT INTO `cl_room_follow` VALUES (234, 15, 18, 0, 1539163753, 1539163753);
INSERT INTO `cl_room_follow` VALUES (235, 7, 22, 0, 1539164137, 1539164137);
INSERT INTO `cl_room_follow` VALUES (236, 12, 19, 0, 1539164780, 1539336579);
INSERT INTO `cl_room_follow` VALUES (237, 12, 12, 0, 1539164796, 1539164796);
INSERT INTO `cl_room_follow` VALUES (238, 23, 7, 0, 1539165440, 1539165440);
INSERT INTO `cl_room_follow` VALUES (239, 23, 20, 0, 1539165487, 1539165487);
INSERT INTO `cl_room_follow` VALUES (240, 27, 3, 0, 1539177131, 1539177131);
INSERT INTO `cl_room_follow` VALUES (241, 27, 5, 0, 1539177133, 1539177133);
INSERT INTO `cl_room_follow` VALUES (242, 12, 15, 0, 1539186711, 1539186711);
INSERT INTO `cl_room_follow` VALUES (243, 12, 8, 2, 1539186720, 1540616535);
INSERT INTO `cl_room_follow` VALUES (244, 2, 4, 0, 1539221282, 1539221282);
INSERT INTO `cl_room_follow` VALUES (245, 3, 22, 0, 1539221932, 1539221932);
INSERT INTO `cl_room_follow` VALUES (246, 15, 4, 0, 1539222293, 1539222293);
INSERT INTO `cl_room_follow` VALUES (247, 15, 2, 0, 1539222822, 1539240063);
INSERT INTO `cl_room_follow` VALUES (248, 2, 11, 1, 1539225702, 1539238412);
INSERT INTO `cl_room_follow` VALUES (249, 2, 20, 0, 1539225706, 1539225706);
INSERT INTO `cl_room_follow` VALUES (250, 29, 2, 0, 1539228196, 1539228196);
INSERT INTO `cl_room_follow` VALUES (251, 8, 12, 0, 1539234074, 1539234074);
INSERT INTO `cl_room_follow` VALUES (252, 8, 6, 0, 1539234080, 1539234080);
INSERT INTO `cl_room_follow` VALUES (253, 12, 23, 0, 1539237263, 1539237263);
INSERT INTO `cl_room_follow` VALUES (254, 10, 23, 1, 1539237529, 1539398199);
INSERT INTO `cl_room_follow` VALUES (255, 2, 23, 0, 1539237653, 1539237653);
INSERT INTO `cl_room_follow` VALUES (256, 3, 25, 0, 1539238907, 1539238907);
INSERT INTO `cl_room_follow` VALUES (257, 3, 23, 0, 1539239518, 1539239518);
INSERT INTO `cl_room_follow` VALUES (258, 2, 24, 0, 1539239560, 1539239560);
INSERT INTO `cl_room_follow` VALUES (259, 15, 23, 0, 1539240537, 1539240537);
INSERT INTO `cl_room_follow` VALUES (260, 31, 27, 0, 1539242078, 1539845161);
INSERT INTO `cl_room_follow` VALUES (261, 31, 3, 1, 1539244360, 1539244435);
INSERT INTO `cl_room_follow` VALUES (262, 31, 10, 0, 1539244485, 1539244485);
INSERT INTO `cl_room_follow` VALUES (263, 23, 23, 0, 1539245337, 1539245337);
INSERT INTO `cl_room_follow` VALUES (264, 40, 28, 0, 1539246117, 1539246117);
INSERT INTO `cl_room_follow` VALUES (265, 9, 28, 0, 1539246194, 1539246194);
INSERT INTO `cl_room_follow` VALUES (266, 10, 28, 0, 1539246199, 1539246199);
INSERT INTO `cl_room_follow` VALUES (267, 12, 28, 0, 1539246215, 1539246215);
INSERT INTO `cl_room_follow` VALUES (268, 40, 23, 0, 1539247106, 1539247106);
INSERT INTO `cl_room_follow` VALUES (269, 29, 5, 0, 1539247457, 1539247457);
INSERT INTO `cl_room_follow` VALUES (270, 29, 28, 1, 1539247469, 1539247594);
INSERT INTO `cl_room_follow` VALUES (271, 40, 10, 0, 1539247645, 1539247645);
INSERT INTO `cl_room_follow` VALUES (272, 40, 13, 0, 1539247647, 1539247647);
INSERT INTO `cl_room_follow` VALUES (273, 10, 25, 0, 1539249350, 1539249350);
INSERT INTO `cl_room_follow` VALUES (274, 40, 25, 0, 1539251276, 1539251293);
INSERT INTO `cl_room_follow` VALUES (275, 12, 22, 0, 1539251295, 1539251295);
INSERT INTO `cl_room_follow` VALUES (276, 12, 1, 0, 1539251311, 1539251311);
INSERT INTO `cl_room_follow` VALUES (277, 12, 25, 0, 1539251381, 1539251381);
INSERT INTO `cl_room_follow` VALUES (278, 27, 23, 0, 1539257872, 1539257872);
INSERT INTO `cl_room_follow` VALUES (279, 27, 10, 0, 1539257879, 1539257879);
INSERT INTO `cl_room_follow` VALUES (280, 27, 13, 0, 1539257881, 1539257881);
INSERT INTO `cl_room_follow` VALUES (281, 27, 28, 0, 1539257886, 1539257886);
INSERT INTO `cl_room_follow` VALUES (282, 3, 27, 0, 1539307961, 1539307961);
INSERT INTO `cl_room_follow` VALUES (283, 23, 28, 0, 1539309652, 1539309652);
INSERT INTO `cl_room_follow` VALUES (284, 3, 28, 0, 1539310556, 1539310556);
INSERT INTO `cl_room_follow` VALUES (285, 8, 23, 0, 1539310817, 1539310817);
INSERT INTO `cl_room_follow` VALUES (286, 9, 23, 0, 1539311536, 1539311536);
INSERT INTO `cl_room_follow` VALUES (287, 19, 23, 0, 1539316835, 1539316835);
INSERT INTO `cl_room_follow` VALUES (288, 17, 23, 1, 1539317169, 1540545570);
INSERT INTO `cl_room_follow` VALUES (289, 7, 23, 0, 1539323840, 1539323840);
INSERT INTO `cl_room_follow` VALUES (290, 19, 5, 0, 1539326963, 1539326963);
INSERT INTO `cl_room_follow` VALUES (291, 23, 10, 0, 1539331050, 1539331050);
INSERT INTO `cl_room_follow` VALUES (292, 31, 5, 0, 1539334585, 1539334585);
INSERT INTO `cl_room_follow` VALUES (293, 31, 23, 1, 1539398000, 1539944719);
INSERT INTO `cl_room_follow` VALUES (294, 31, 28, 1, 1539398018, 1539398019);
INSERT INTO `cl_room_follow` VALUES (295, 31, 15, 1, 1539398027, 1539398029);
INSERT INTO `cl_room_follow` VALUES (296, 31, 12, 1, 1539398039, 1539398041);
INSERT INTO `cl_room_follow` VALUES (297, 31, 6, 1, 1539398044, 1539398046);
INSERT INTO `cl_room_follow` VALUES (298, 31, 8, 1, 1539398048, 1539913523);
INSERT INTO `cl_room_follow` VALUES (299, 10, 27, 2, 1539398214, 1539422989);
INSERT INTO `cl_room_follow` VALUES (300, 29, 23, 0, 1539409760, 1539409760);
INSERT INTO `cl_room_follow` VALUES (301, 29, 27, 1, 1539411468, 1539411470);
INSERT INTO `cl_room_follow` VALUES (302, 29, 10, 0, 1539412005, 1539412005);
INSERT INTO `cl_room_follow` VALUES (303, 10, 20, 0, 1539413064, 1539413064);
INSERT INTO `cl_room_follow` VALUES (304, 10, 17, 0, 1539413623, 1539413623);
INSERT INTO `cl_room_follow` VALUES (305, 17, 15, 0, 1539413948, 1539413948);
INSERT INTO `cl_room_follow` VALUES (306, 12, 31, 0, 1539414509, 1540742174);
INSERT INTO `cl_room_follow` VALUES (307, 29, 13, 0, 1539414676, 1539414676);
INSERT INTO `cl_room_follow` VALUES (308, 12, 21, 0, 1539415059, 1539775329);
INSERT INTO `cl_room_follow` VALUES (309, 7, 31, 0, 1539415382, 1539415397);
INSERT INTO `cl_room_follow` VALUES (310, 29, 31, 0, 1539416404, 1539416404);
INSERT INTO `cl_room_follow` VALUES (311, 19, 31, 0, 1539416597, 1539416597);
INSERT INTO `cl_room_follow` VALUES (312, 29, 19, 1, 1539416779, 1539417084);
INSERT INTO `cl_room_follow` VALUES (313, 19, 28, 0, 1539416844, 1539416844);
INSERT INTO `cl_room_follow` VALUES (314, 29, 18, 1, 1539416844, 1539416986);
INSERT INTO `cl_room_follow` VALUES (315, 10, 31, 1, 1539417535, 1539596137);
INSERT INTO `cl_room_follow` VALUES (316, 31, 31, 1, 1539417597, 1539846385);
INSERT INTO `cl_room_follow` VALUES (317, 10, 32, 0, 1539417840, 1539417840);
INSERT INTO `cl_room_follow` VALUES (318, 12, 32, 2, 1539417855, 1539913125);
INSERT INTO `cl_room_follow` VALUES (319, 31, 32, 2, 1539418031, 1539913169);
INSERT INTO `cl_room_follow` VALUES (320, 35, 33, 0, 1539418671, 1539418671);
INSERT INTO `cl_room_follow` VALUES (321, 35, 31, 0, 1539419423, 1539419423);
INSERT INTO `cl_room_follow` VALUES (322, 35, 27, 2, 1539419433, 1539422655);
INSERT INTO `cl_room_follow` VALUES (323, 7, 27, 0, 1539421412, 1539421412);
INSERT INTO `cl_room_follow` VALUES (324, 17, 31, 1, 1539422324, 1539422388);
INSERT INTO `cl_room_follow` VALUES (325, 17, 27, 0, 1539422343, 1539422343);
INSERT INTO `cl_room_follow` VALUES (326, 35, 19, 0, 1539422432, 1539422432);
INSERT INTO `cl_room_follow` VALUES (327, 12, 27, 2, 1539422440, 1539939310);
INSERT INTO `cl_room_follow` VALUES (328, 10, 33, 0, 1539424336, 1539424336);
INSERT INTO `cl_room_follow` VALUES (329, 18, 12, 0, 1539424404, 1539424404);
INSERT INTO `cl_room_follow` VALUES (330, 18, 6, 0, 1539424453, 1539424453);
INSERT INTO `cl_room_follow` VALUES (331, 17, 33, 0, 1539424478, 1539424478);
INSERT INTO `cl_room_follow` VALUES (332, 35, 5, 0, 1539424731, 1539424731);
INSERT INTO `cl_room_follow` VALUES (333, 35, 23, 0, 1539424937, 1539424937);
INSERT INTO `cl_room_follow` VALUES (334, 31, 33, 0, 1539426364, 1539426364);
INSERT INTO `cl_room_follow` VALUES (335, 47, 27, 0, 1539426762, 1539426762);
INSERT INTO `cl_room_follow` VALUES (336, 47, 33, 0, 1539426782, 1539426782);
INSERT INTO `cl_room_follow` VALUES (337, 31, 19, 0, 1539426789, 1539426789);
INSERT INTO `cl_room_follow` VALUES (338, 47, 32, 2, 1539427229, 1539913236);
INSERT INTO `cl_room_follow` VALUES (339, 47, 10, 0, 1539427305, 1539427305);
INSERT INTO `cl_room_follow` VALUES (340, 47, 31, 0, 1539427347, 1539427347);
INSERT INTO `cl_room_follow` VALUES (341, 27, 31, 0, 1539428754, 1539428754);
INSERT INTO `cl_room_follow` VALUES (342, 40, 1, 0, 1539495365, 1539495365);
INSERT INTO `cl_room_follow` VALUES (343, 25, 20, 0, 1539504730, 1539504730);
INSERT INTO `cl_room_follow` VALUES (344, 23, 31, 0, 1539567041, 1539567041);
INSERT INTO `cl_room_follow` VALUES (345, 9, 31, 0, 1539572890, 1539572890);
INSERT INTO `cl_room_follow` VALUES (346, 17, 29, 0, 1539573867, 1539573867);
INSERT INTO `cl_room_follow` VALUES (347, 17, 20, 0, 1539573937, 1539573937);
INSERT INTO `cl_room_follow` VALUES (348, 8, 20, 0, 1539573965, 1539573965);
INSERT INTO `cl_room_follow` VALUES (349, 9, 20, 1, 1539574153, 1539854414);
INSERT INTO `cl_room_follow` VALUES (350, 19, 20, 0, 1539586138, 1539586138);
INSERT INTO `cl_room_follow` VALUES (351, 9, 21, 0, 1539586161, 1539586161);
INSERT INTO `cl_room_follow` VALUES (352, 8, 33, 0, 1539588211, 1539588211);
INSERT INTO `cl_room_follow` VALUES (353, 19, 24, 0, 1539590919, 1539590919);
INSERT INTO `cl_room_follow` VALUES (354, 27, 20, 0, 1539591142, 1539591142);
INSERT INTO `cl_room_follow` VALUES (355, 29, 1, 0, 1539595630, 1539595630);
INSERT INTO `cl_room_follow` VALUES (356, 41, 31, 0, 1539597133, 1539597133);
INSERT INTO `cl_room_follow` VALUES (357, 41, 20, 0, 1539597137, 1539597137);
INSERT INTO `cl_room_follow` VALUES (358, 41, 5, 0, 1539597139, 1539597139);
INSERT INTO `cl_room_follow` VALUES (359, 41, 25, 0, 1539597305, 1539597305);
INSERT INTO `cl_room_follow` VALUES (360, 41, 30, 1, 1539597478, 1539933415);
INSERT INTO `cl_room_follow` VALUES (361, 41, 22, 0, 1539597506, 1539597506);
INSERT INTO `cl_room_follow` VALUES (362, 41, 11, 0, 1539597595, 1539597595);
INSERT INTO `cl_room_follow` VALUES (363, 12, 33, 0, 1539598741, 1539598741);
INSERT INTO `cl_room_follow` VALUES (364, 27, 19, 0, 1539612465, 1539612465);
INSERT INTO `cl_room_follow` VALUES (365, 27, 32, 2, 1539614499, 1539938491);
INSERT INTO `cl_room_follow` VALUES (366, 31, 20, 0, 1539662598, 1539938217);
INSERT INTO `cl_room_follow` VALUES (367, 17, 24, 0, 1539746148, 1539746148);
INSERT INTO `cl_room_follow` VALUES (368, 29, 20, 0, 1539758997, 1539758997);
INSERT INTO `cl_room_follow` VALUES (369, 31, 30, 0, 1539759574, 1539759574);
INSERT INTO `cl_room_follow` VALUES (370, 9, 27, 2, 1539764340, 1539940198);
INSERT INTO `cl_room_follow` VALUES (371, 29, 29, 1, 1539764986, 1539935846);
INSERT INTO `cl_room_follow` VALUES (372, 31, 34, 2, 1539765302, 1540743068);
INSERT INTO `cl_room_follow` VALUES (373, 31, 14, 0, 1539765314, 1539765314);
INSERT INTO `cl_room_follow` VALUES (374, 31, 18, 0, 1539767595, 1539843518);
INSERT INTO `cl_room_follow` VALUES (375, 29, 17, 0, 1539768012, 1539768012);
INSERT INTO `cl_room_follow` VALUES (376, 29, 26, 1, 1539768543, 1539768634);
INSERT INTO `cl_room_follow` VALUES (377, 29, 30, 0, 1539769487, 1539769487);
INSERT INTO `cl_room_follow` VALUES (378, 35, 20, 0, 1539770473, 1539770473);
INSERT INTO `cl_room_follow` VALUES (379, 19, 29, 0, 1539770622, 1539770622);
INSERT INTO `cl_room_follow` VALUES (380, 27, 29, 0, 1539778367, 1539778367);
INSERT INTO `cl_room_follow` VALUES (381, 53, 20, 0, 1539789755, 1539789755);
INSERT INTO `cl_room_follow` VALUES (382, 53, 29, 0, 1539789768, 1539789768);
INSERT INTO `cl_room_follow` VALUES (383, 31, 29, 0, 1539831031, 1539831031);
INSERT INTO `cl_room_follow` VALUES (384, 10, 29, 0, 1539842268, 1539842268);
INSERT INTO `cl_room_follow` VALUES (385, 10, 34, 2, 1539842511, 1540743130);
INSERT INTO `cl_room_follow` VALUES (386, 35, 29, 0, 1539842947, 1539842947);
INSERT INTO `cl_room_follow` VALUES (387, 35, 18, 0, 1539843930, 1539844861);
INSERT INTO `cl_room_follow` VALUES (388, 12, 29, 0, 1539844160, 1539844160);
INSERT INTO `cl_room_follow` VALUES (389, 9, 29, 0, 1539844855, 1539844855);
INSERT INTO `cl_room_follow` VALUES (390, 35, 12, 0, 1539844882, 1539844950);
INSERT INTO `cl_room_follow` VALUES (391, 29, 34, 1, 1539846903, 1539846944);
INSERT INTO `cl_room_follow` VALUES (392, 3, 31, 0, 1539847024, 1539847024);
INSERT INTO `cl_room_follow` VALUES (393, 3, 29, 0, 1539847158, 1539847158);
INSERT INTO `cl_room_follow` VALUES (394, 43, 34, 2, 1539847725, 1540743093);
INSERT INTO `cl_room_follow` VALUES (395, 43, 19, 0, 1539848300, 1539848300);
INSERT INTO `cl_room_follow` VALUES (396, 42, 34, 2, 1539848399, 1540743675);
INSERT INTO `cl_room_follow` VALUES (397, 41, 34, 2, 1539848591, 1539934130);
INSERT INTO `cl_room_follow` VALUES (398, 41, 9, 0, 1539849548, 1539849548);
INSERT INTO `cl_room_follow` VALUES (399, 41, 17, 0, 1539849555, 1539849555);
INSERT INTO `cl_room_follow` VALUES (400, 41, 32, 2, 1539849609, 1540733953);
INSERT INTO `cl_room_follow` VALUES (401, 29, 32, 1, 1539849631, 1539850087);
INSERT INTO `cl_room_follow` VALUES (402, 41, 19, 0, 1539849731, 1539849731);
INSERT INTO `cl_room_follow` VALUES (403, 41, 21, 0, 1539849734, 1539849734);
INSERT INTO `cl_room_follow` VALUES (404, 41, 18, 0, 1539849739, 1539849739);
INSERT INTO `cl_room_follow` VALUES (405, 41, 13, 0, 1539849801, 1539849801);
INSERT INTO `cl_room_follow` VALUES (406, 54, 31, 0, 1539849809, 1539849809);
INSERT INTO `cl_room_follow` VALUES (407, 54, 23, 0, 1539849828, 1539849828);
INSERT INTO `cl_room_follow` VALUES (408, 54, 15, 0, 1539849846, 1539849846);
INSERT INTO `cl_room_follow` VALUES (409, 19, 34, 2, 1539851510, 1540744801);
INSERT INTO `cl_room_follow` VALUES (410, 12, 30, 0, 1539851740, 1539851740);
INSERT INTO `cl_room_follow` VALUES (411, 12, 34, 2, 1539851750, 1540788060);
INSERT INTO `cl_room_follow` VALUES (412, 48, 31, 0, 1539869732, 1539869732);
INSERT INTO `cl_room_follow` VALUES (413, 9, 32, 2, 1539913908, 1539915555);
INSERT INTO `cl_room_follow` VALUES (414, 41, 29, 0, 1539919377, 1539919377);
INSERT INTO `cl_room_follow` VALUES (415, 23, 29, 0, 1539921262, 1539921262);
INSERT INTO `cl_room_follow` VALUES (416, 15, 32, 0, 1539931280, 1539931280);
INSERT INTO `cl_room_follow` VALUES (417, 15, 31, 0, 1539931393, 1539931393);
INSERT INTO `cl_room_follow` VALUES (418, 35, 32, 0, 1539931666, 1539931666);
INSERT INTO `cl_room_follow` VALUES (419, 45, 29, 0, 1539935447, 1539935447);
INSERT INTO `cl_room_follow` VALUES (420, 45, 28, 0, 1539936012, 1539936012);
INSERT INTO `cl_room_follow` VALUES (421, 42, 9, 0, 1539936336, 1539936336);
INSERT INTO `cl_room_follow` VALUES (422, 42, 29, 0, 1539936507, 1539936507);
INSERT INTO `cl_room_follow` VALUES (423, 43, 18, 0, 1539936807, 1539936828);
INSERT INTO `cl_room_follow` VALUES (424, 43, 30, 1, 1539936818, 1539936820);
INSERT INTO `cl_room_follow` VALUES (425, 43, 23, 0, 1539936864, 1539936864);
INSERT INTO `cl_room_follow` VALUES (426, 43, 9, 0, 1539938072, 1539938072);
INSERT INTO `cl_room_follow` VALUES (427, 43, 1, 0, 1539938078, 1539938078);
INSERT INTO `cl_room_follow` VALUES (428, 43, 25, 0, 1539938084, 1539938084);
INSERT INTO `cl_room_follow` VALUES (429, 43, 35, 0, 1539938086, 1539938086);
INSERT INTO `cl_room_follow` VALUES (430, 43, 17, 0, 1539938089, 1539938089);
INSERT INTO `cl_room_follow` VALUES (431, 35, 34, 0, 1539938334, 1539938334);
INSERT INTO `cl_room_follow` VALUES (432, 31, 35, 0, 1539939153, 1539939153);
INSERT INTO `cl_room_follow` VALUES (433, 41, 23, 1, 1539939157, 1540652980);
INSERT INTO `cl_room_follow` VALUES (434, 12, 35, 0, 1539939186, 1539939186);
INSERT INTO `cl_room_follow` VALUES (435, 27, 30, 0, 1539950966, 1539950966);
INSERT INTO `cl_room_follow` VALUES (436, 55, 19, 0, 1540015276, 1540015276);
INSERT INTO `cl_room_follow` VALUES (437, 55, 5, 0, 1540015440, 1540015440);
INSERT INTO `cl_room_follow` VALUES (438, 46, 1, 0, 1540020567, 1540020567);
INSERT INTO `cl_room_follow` VALUES (439, 46, 22, 0, 1540020628, 1540020628);
INSERT INTO `cl_room_follow` VALUES (440, 46, 30, 0, 1540020710, 1540020710);
INSERT INTO `cl_room_follow` VALUES (441, 12, 26, 0, 1540022386, 1540022386);
INSERT INTO `cl_room_follow` VALUES (442, 17, 30, 0, 1540201792, 1540201792);
INSERT INTO `cl_room_follow` VALUES (443, 40, 31, 0, 1540377466, 1540377466);
INSERT INTO `cl_room_follow` VALUES (444, 40, 34, 0, 1540377477, 1540377477);
INSERT INTO `cl_room_follow` VALUES (445, 9, 30, 0, 1540379397, 1540379397);
INSERT INTO `cl_room_follow` VALUES (446, 40, 30, 0, 1540380455, 1540380455);
INSERT INTO `cl_room_follow` VALUES (447, 17, 8, 0, 1540382836, 1540382836);
INSERT INTO `cl_room_follow` VALUES (448, 40, 8, 0, 1540382843, 1540382843);
INSERT INTO `cl_room_follow` VALUES (449, 40, 5, 0, 1540383783, 1540383783);
INSERT INTO `cl_room_follow` VALUES (450, 12, 37, 0, 1540518049, 1540518049);
INSERT INTO `cl_room_follow` VALUES (451, 17, 32, 0, 1540538207, 1540538207);
INSERT INTO `cl_room_follow` VALUES (452, 19, 32, 0, 1540543221, 1540543221);
INSERT INTO `cl_room_follow` VALUES (453, 17, 34, 0, 1540604370, 1540604370);
INSERT INTO `cl_room_follow` VALUES (454, 9, 34, 0, 1540605872, 1540605872);
INSERT INTO `cl_room_follow` VALUES (455, 17, 18, 0, 1540607808, 1540607808);
INSERT INTO `cl_room_follow` VALUES (456, 26, 28, 0, 1540633215, 1540633215);
INSERT INTO `cl_room_follow` VALUES (457, 26, 12, 0, 1540633286, 1540633286);
INSERT INTO `cl_room_follow` VALUES (458, 12, 9, 1, 1540725410, 1540725429);
INSERT INTO `cl_room_follow` VALUES (459, 12, 36, 0, 1540777784, 1540777784);
INSERT INTO `cl_room_follow` VALUES (460, 17, 28, 0, 1540792326, 1540792326);
INSERT INTO `cl_room_follow` VALUES (461, 17, 40, 0, 1540794766, 1540794766);
INSERT INTO `cl_room_follow` VALUES (462, 17, 39, 0, 1540795485, 1540795485);
INSERT INTO `cl_room_follow` VALUES (463, 41, 40, 0, 1540803528, 1540803528);
INSERT INTO `cl_room_follow` VALUES (464, 17, 38, 0, 1540804195, 1540804195);

-- ----------------------------
-- Table structure for cl_room_notice
-- ----------------------------
DROP TABLE IF EXISTS `cl_room_notice`;
CREATE TABLE `cl_room_notice`  (
  `notice_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL COMMENT '房间id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否置顶 1=>是 0=>否',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公告标题',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公告内容',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '公告状态  1=>正常  0=>删除或禁用',
  PRIMARY KEY (`notice_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '房间公告' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_room_notice
-- ----------------------------
INSERT INTO `cl_room_notice` VALUES (1, 2, 2, 0, '公告', '欢迎大家', 1538115929, 1538115929, 1);
INSERT INTO `cl_room_notice` VALUES (2, 5, 4, 0, '公告', '啊啊啊', 1538119790, 1538119790, 1);
INSERT INTO `cl_room_notice` VALUES (3, 4, 5, 0, '更丰富', '关系序号', 1538246924, 1538246924, 1);
INSERT INTO `cl_room_notice` VALUES (4, 4, 5, 0, '吃想', '粉粉的', 1538248875, 1538248875, 1);
INSERT INTO `cl_room_notice` VALUES (5, 4, 5, 0, '换个', '规律', 1538249133, 1538249133, 1);
INSERT INTO `cl_room_notice` VALUES (6, 3, 3, 0, '', '啦啦啦啦啦', 1538993852, 1538993852, 0);
INSERT INTO `cl_room_notice` VALUES (7, 3, 3, 0, '', '啦啦啦啦啦啦噜小心翼翼', 1538993974, 1538993974, 0);
INSERT INTO `cl_room_notice` VALUES (8, 3, 3, 0, '', '啦啦啦啦啦啦噜小心翼翼管理局', 1538993989, 1538993989, 0);
INSERT INTO `cl_room_notice` VALUES (9, 3, 3, 0, '', '净利率', 1538999342, 1538999342, 0);
INSERT INTO `cl_room_notice` VALUES (10, 3, 3, 0, '看看咯了', '啦啦啦啦啦啦', 1539000877, 1539000877, 0);
INSERT INTO `cl_room_notice` VALUES (11, 3, 3, 0, '爸爸', '啦啦啦啦啦啦噜小心翼翼', 1539001065, 1539001065, 0);
INSERT INTO `cl_room_notice` VALUES (12, 3, 3, 0, '了哦哦哦', '咯哦哦', 1539001114, 1539001114, 0);
INSERT INTO `cl_room_notice` VALUES (13, 3, 3, 0, '啦咯', '啦啦啦哦哦哦哦abdomen', 1539001212, 1539001212, 0);
INSERT INTO `cl_room_notice` VALUES (14, 3, 3, 0, '哦哦哦哦', '来咯哦', 1539001896, 1539001896, 0);
INSERT INTO `cl_room_notice` VALUES (15, 3, 3, 0, '爸爸', '来咯额', 1539002041, 1539002041, 0);
INSERT INTO `cl_room_notice` VALUES (16, 3, 3, 0, '阿克额', '途径解决', 1539002051, 1539002051, 1);
INSERT INTO `cl_room_notice` VALUES (17, 2, 2, 0, '合适的话不得不说', '说话傻瓜傻瓜睡吧睡吧僵尸叔叔舞队伍', 1539073706, 1539073706, 1);
INSERT INTO `cl_room_notice` VALUES (18, 20, 12, 0, '测试', '那你男男女女', 1539138557, 1539138557, 1);
INSERT INTO `cl_room_notice` VALUES (19, 9, 10, 0, '鹅妈妈', '等哈哈打电话度', 1539150800, 1539150800, 1);
INSERT INTO `cl_room_notice` VALUES (20, 22, 10, 0, '啊啊啊', '啊啊啊啊啊啊', 1539160533, 1539160533, 1);
INSERT INTO `cl_room_notice` VALUES (21, 19, 10, 0, '啥话', '嘻嘻嘻嘻哦', 1539162505, 1539162505, 1);
INSERT INTO `cl_room_notice` VALUES (22, 2, 2, 0, '666', '哦哦红颜色和明明', 1539229018, 1539229018, 1);
INSERT INTO `cl_room_notice` VALUES (23, 2, 2, 0, '3嗯哦你', '丁墨轰轰轰民', 1539229043, 1539229043, 1);
INSERT INTO `cl_room_notice` VALUES (24, 11, 10, 0, '啊哈哈哈哈', '153', 1539238346, 1539238346, 1);
INSERT INTO `cl_room_notice` VALUES (25, 19, 10, 0, '讲解区块链', '区块链站长讲解', 1539595317, 1539595317, 1);
INSERT INTO `cl_room_notice` VALUES (26, 34, 10, 0, '', 'testssss', 1539770344, 1539770344, 1);
INSERT INTO `cl_room_notice` VALUES (27, 34, 10, 0, '测试', '今天是开心的测试', 1539847047, 1539847047, 1);
INSERT INTO `cl_room_notice` VALUES (28, 32, 12, 0, '公告标题', '文明直播', 1539916412, 1539916412, 1);
INSERT INTO `cl_room_notice` VALUES (29, 27, 31, 0, '哈哈哈', 'ueue', 1539940647, 1539940647, 1);
INSERT INTO `cl_room_notice` VALUES (30, 37, 12, 0, '公告标题', '这是一条公告', 1540518198, 1540518198, 1);

-- ----------------------------
-- Table structure for cl_user_extend
-- ----------------------------
DROP TABLE IF EXISTS `cl_user_extend`;
CREATE TABLE `cl_user_extend`  (
  `user_id` int(11) NOT NULL COMMENT '关联用户id',
  `ETHAddr` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'eth充值地址',
  `ETHurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'eth提现地址 用户自己绑定  可更改',
  `Pwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'eth钱包密码',
  `Keystore` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'eth钱包信息',
  `UID` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '莓果app open_id',
  `web_qq` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `web_wx` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户关联数据表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cl_user_id
-- ----------------------------
DROP TABLE IF EXISTS `cl_user_id`;
CREATE TABLE `cl_user_id`  (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `real_name` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sex` tinyint(1) NOT NULL COMMENT '1=>男 0=>女',
  `ID_num` varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证号码',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地址',
  `face` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证正面照片',
  `back` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '身份证反面照片',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '识别照片',
  `status` tinyint(1) NOT NULL COMMENT '1=>实名通过 2=>接口认证失败（接口认证失败可申请人工认证） 3=>待人工审核    0=>人工认证失败 需重新提交信息  ',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户实名信息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cl_users
-- ----------------------------
DROP TABLE IF EXISTS `cl_users`;
CREATE TABLE `cl_users`  (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键标识',
  `nick_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户昵称',
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码',
  `header_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT 1 COMMENT '性别 1=>男 2=>女',
  `birthday` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生日',
  `job` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '工作',
  `sign` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '签名',
  `wx` varchar(28) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信openid',
  `qq` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'qqopenid',
  `money` decimal(11, 2) NOT NULL DEFAULT 0.00 COMMENT '账户积分余额',
  `j_push_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '极光推送id',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '用户创建日期',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '上次信息更新日期',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '用户状态 0=> 禁用 1 => 正常',
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户信息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_users
-- ----------------------------
INSERT INTO `cl_users` VALUES (1, '小松', '', 'http://daike.xingzhuosong.com/public/upload/images/20181029/d9c6ec4ddffaf1b2cc5da5b398f587ba.jpg', 1, '2018-10-29', NULL, NULL, '9991211', NULL, 0.00, NULL, 1541137752, 1541137752, 1);
INSERT INTO `cl_users` VALUES (2, '小松2', NULL, 'http://daike.xingzhuosong.com//public/upload/images/default_header.jpg', 1, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, NULL, 1);

-- ----------------------------
-- Table structure for cl_version
-- ----------------------------
DROP TABLE IF EXISTS `cl_version`;
CREATE TABLE `cl_version`  (
  `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `force` tinyint(1) NOT NULL COMMENT '是否强制更新',
  `versionCode` tinyint(255) NOT NULL COMMENT '版本号',
  `versionName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '版本名称',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接地址',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '更新内容',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cl_version
-- ----------------------------
INSERT INTO `cl_version` VALUES (1, 1, 1, '1.0', 'http://file.51soha.com//vodKO21499027.apk', '新增：\r\n    1. 游客登录\r\n    2.分享页唤醒app\r\n    3.加入ETH', '2018-07-05 15:12:50', '2018-10-29 20:53:43');

-- ----------------------------
-- Table structure for cl_vod
-- ----------------------------
DROP TABLE IF EXISTS `cl_vod`;
CREATE TABLE `cl_vod`  (
  `pid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL COMMENT '点播分类id',
  `user_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '点播名',
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '播放次数',
  `reply_num` int(11) NOT NULL DEFAULT 0 COMMENT '评论数量',
  `share_num` int(11) NOT NULL DEFAULT 0 COMMENT '分享数量',
  `up` int(11) NOT NULL DEFAULT 0 COMMENT '点赞数量',
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '封面图片',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '点播内容',
  `play_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '播放地址',
  `top` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`pid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '回放列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_vod
-- ----------------------------
INSERT INTO `cl_vod` VALUES (11, 14, 1, '区块链的发展历史', 78, 4, 1, 1, 'http://file.51soha.com//c3902201810150920179892.jpeg', '区块链的发展历史', 'http://file.51soha.com//vodgR21123922.mp4', 0, 1, 1539230580, 1539566418);
INSERT INTO `cl_vod` VALUES (13, 15, 11, '最长区块链', 11, 1, 0, 0, 'http://file.51soha.com//efca0201810150919541057.jpg', '最长区块链', 'http://file.51soha.com//vod0v21072460.mp4', 0, 1, 1539400234, 1539566395);
INSERT INTO `cl_vod` VALUES (14, 12, 11, '全球流通的区块链', 6, 0, 0, 1, 'http://file.51soha.com//719f7201810150919442257.jpg', '全球流通的区块链', 'http://file.51soha.com//vodoi20784590.mp4', 0, 1, 1539400310, 1539566385);
INSERT INTO `cl_vod` VALUES (15, 10, 11, '区块链的工作原理', 19, 0, 0, 1, 'http://file.51soha.com//5d78c201810150919321487.jpg', '区块链的工作原理', 'http://file.51soha.com//vodST21099529.mp4', 0, 1, 1539400723, 1539566374);
INSERT INTO `cl_vod` VALUES (16, 10, 11, '区块链和比特币关系？', 25, 4, 1, 2, 'http://file.51soha.com//48c0c201810150919217622.jpg', '区块链和比特币是什么关系？', 'http://file.51soha.com//voduv2120109.mp4', 0, 1, 1539400782, 1539566363);
INSERT INTO `cl_vod` VALUES (17, 9, 17, '从物物交换到比特币', 40, 5, 0, 1, 'http://file.51soha.com//452a6201810150915202871.jpg', '从物物交换到比特币', 'http://file.51soha.com//vodb220829003.mp4', 0, 1, 1539418351, 1539566123);
INSERT INTO `cl_vod` VALUES (18, 15, 3, '区块链科技视频', 11, 0, 0, 0, 'http://file.51soha.com/1539510781454.png', '区块链科技视频', 'http://file.51soha.com/1539510833863.mp4', 1, 1, 1539510850, 2018);
INSERT INTO `cl_vod` VALUES (19, 15, 3, '区块链介绍', 37, 2, 2, 2, 'http://file.51soha.com//vodOa21017815.jpeg', '区块链介绍', 'http://file.51soha.com//vodZe21132052.mp4', 1, 1, 1539512012, 2018);
INSERT INTO `cl_vod` VALUES (21, 12, 11, '区块链教程1-1', 4, 1, 0, 1, 'http://file.51soha.com//vodRP21192935.jpg', '区块链教程1-1', 'http://file.51soha.com//vodi820924461.mp4', 0, 1, 1539919246, NULL);

-- ----------------------------
-- Table structure for cl_vod_category
-- ----------------------------
DROP TABLE IF EXISTS `cl_vod_category`;
CREATE TABLE `cl_vod_category`  (
  `cid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cate_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '回放分类' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_vod_category
-- ----------------------------
INSERT INTO `cl_vod_category` VALUES (4, 'http://file.51soha.com//b211a201809281010133204.jpg', '区块链解密', 2147483647, 2018, 1);
INSERT INTO `cl_vod_category` VALUES (5, 'http://file.51soha.com//986fa201809281010292651.jpg', '数据开放计算', 2147483647, 2018, 1);
INSERT INTO `cl_vod_category` VALUES (6, 'http://file.51soha.com//780462018092810104632.jpg', '数字资产平台', 2147483647, 2018, 1);
INSERT INTO `cl_vod_category` VALUES (7, 'http://file.51soha.com//ca7bf201809281010567689.jpg', '区块链主链建设', 2147483647, 2018, 1);
INSERT INTO `cl_vod_category` VALUES (9, 'http://file.51soha.com//e33e3201809281011145634.jpg', '区块链智能合约', 2018, 2018, 1);
INSERT INTO `cl_vod_category` VALUES (10, 'http://file.51soha.com//778e5201810091714048716.jpg', '游戏板块', 2018, 2018, 1);

-- ----------------------------
-- Table structure for cl_vod_reply
-- ----------------------------
DROP TABLE IF EXISTS `cl_vod_reply`;
CREATE TABLE `cl_vod_reply`  (
  `reply_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `pid` int(11) NULL DEFAULT NULL COMMENT '评论所属视频id',
  `p_id` int(11) NULL DEFAULT NULL COMMENT '当前评论上级',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  `content` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论内容',
  `status` tinyint(1) NOT NULL COMMENT '是否有效  1=>正常 0=>删除|禁用',
  PRIMARY KEY (`reply_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '回放评论表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_vod_reply
-- ----------------------------
INSERT INTO `cl_vod_reply` VALUES (8, 8, NULL, 0, 1538371376, NULL, '哈哈', 1);
INSERT INTO `cl_vod_reply` VALUES (11, 12, NULL, 0, 1538998294, NULL, '刚刚 v', 1);
INSERT INTO `cl_vod_reply` VALUES (15, 15, NULL, 0, 1539056523, NULL, 'em me', 1);
INSERT INTO `cl_vod_reply` VALUES (24, 2, 11, 0, 1539238556, NULL, '3', 1);
INSERT INTO `cl_vod_reply` VALUES (25, 2, 11, 24, 1539238582, NULL, '2', 1);
INSERT INTO `cl_vod_reply` VALUES (26, 2, 11, 24, 1539238839, NULL, '5可口可乐了', 1);
INSERT INTO `cl_vod_reply` VALUES (27, 8, 11, 0, 1539332858, NULL, '  沟沟壑壑家', 1);
INSERT INTO `cl_vod_reply` VALUES (28, 0, 13, 0, 1539405433, NULL, '刚回家干净利落', 1);
INSERT INTO `cl_vod_reply` VALUES (29, 41, 16, 0, 1539597774, NULL, '超级厉害', 1);
INSERT INTO `cl_vod_reply` VALUES (30, 27, 17, 0, 1539730244, NULL, '', 1);
INSERT INTO `cl_vod_reply` VALUES (31, 35, 17, 0, 1539768596, NULL, '2', 1);
INSERT INTO `cl_vod_reply` VALUES (32, 10, 19, 0, 1539846493, NULL, '真棒', 1);
INSERT INTO `cl_vod_reply` VALUES (33, 10, 17, 0, 1539846518, NULL, '   \n', 1);
INSERT INTO `cl_vod_reply` VALUES (34, 10, 17, 0, 1539846519, NULL, '   \n', 1);
INSERT INTO `cl_vod_reply` VALUES (35, 10, 17, 0, 1539846526, NULL, '         \n\n\n', 1);
INSERT INTO `cl_vod_reply` VALUES (36, 10, 16, 0, 1539846538, NULL, '\n', 1);
INSERT INTO `cl_vod_reply` VALUES (37, 10, 16, 0, 1539846658, NULL, '\n\n', 1);
INSERT INTO `cl_vod_reply` VALUES (38, 10, 16, 36, 1539846699, NULL, '啊哈哈哈', 1);
INSERT INTO `cl_vod_reply` VALUES (39, 41, 21, 0, 1539933483, NULL, '\n\n\n啊哈哈哈', 1);
INSERT INTO `cl_vod_reply` VALUES (40, 27, 19, 0, 1539951033, NULL, '哈哈哈', 1);

-- ----------------------------
-- Table structure for cl_vod_up
-- ----------------------------
DROP TABLE IF EXISTS `cl_vod_up`;
CREATE TABLE `cl_vod_up`  (
  `up_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '视频id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `status` tinyint(1) NOT NULL COMMENT '1=>有效  0=>删除或取消',
  `create_time` int(11) NULL DEFAULT NULL,
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`up_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '回放视频点赞记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cl_vod_up
-- ----------------------------
INSERT INTO `cl_vod_up` VALUES (14, 11, 2, 0, 1539238171, 1539238564);
INSERT INTO `cl_vod_up` VALUES (16, 11, 8, 1, 1539309842, NULL);
INSERT INTO `cl_vod_up` VALUES (18, 13, 0, 0, 1539405411, 1539405412);
INSERT INTO `cl_vod_up` VALUES (20, 11, 31, 0, 1539421527, 1539421528);
INSERT INTO `cl_vod_up` VALUES (21, 15, 10, 1, 1539571563, NULL);
INSERT INTO `cl_vod_up` VALUES (23, 16, 41, 1, 1539597765, NULL);
INSERT INTO `cl_vod_up` VALUES (24, 17, 10, 1, 1539683917, NULL);
INSERT INTO `cl_vod_up` VALUES (25, 14, 27, 1, 1539730276, NULL);
INSERT INTO `cl_vod_up` VALUES (26, 19, 31, 1, 1539765655, NULL);
INSERT INTO `cl_vod_up` VALUES (27, 19, 10, 1, 1539845069, NULL);
INSERT INTO `cl_vod_up` VALUES (28, 16, 10, 1, 1539846702, NULL);
INSERT INTO `cl_vod_up` VALUES (29, 21, 41, 1, 1539919275, NULL);
INSERT INTO `cl_vod_up` VALUES (30, 11, 46, 0, 1540021269, 1540021270);

SET FOREIGN_KEY_CHECKS = 1;
