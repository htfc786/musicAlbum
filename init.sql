CREATE DATABASE musicalbum;

USE musicalbum;

CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` char(25) NOT NULL COMMENT '用户名',
  `password` char(255) NOT NULL COMMENT '密码',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `album` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `albumName` char(40) NOT NULL COMMENT '名称',
  `albumIntroduce` char(255) DEFAULT NULL COMMENT '简介',
  `albumCreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  `albumCover` text COMMENT '相册封面 (没有值为0)',
  `albumTemplateId` int unsigned NOT NULL DEFAULT '0' COMMENT '模板id',
  `albumMusicId` int unsigned NOT NULL DEFAULT '0' COMMENT '音乐id',
  `albumMusicUrl` text COMMENT '音乐url',
  `albumMreatorId` int NOT NULL COMMENT '创建用户id',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `photos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `mreatorId` int unsigned NOT NULL COMMENT '用户id',
  `albumId` int unsigned NOT NULL COMMENT '相册id',
  `photoOrder` int unsigned NOT NULL COMMENT '图片顺序 从1开始',
  `photoPath` text NOT NULL COMMENT '图片路径',
  `photoUrl` text NOT NULL COMMENT '图片url',
  `originalName` text COMMENT '原图名称',
  `photoIntroduce` char(16) DEFAULT NULL COMMENT '图片介绍',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `templatesgroup` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `groupName` char(64) NOT NULL COMMENT '模板分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `templatName` char(32) NOT NULL COMMENT '模板名称',
  `templatIMG` text COMMENT '模板封面url',
  `templatHtmlPath` text COMMENT '模板html存放地址',
  `templatFileMode` char(10) DEFAULT NULL COMMENT '模板静态文件存储方式',
  `templatFileUrl` text COMMENT '模板静态文件前置url',
  `templatUpdateUserId` int unsigned DEFAULT NULL COMMENT '上传用户id',
  `templatGroupId` int unsigned DEFAULT NULL COMMENT '模板分类id',
  `canWriteText` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否支持写文字',
  `canPlayMusic` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否支持放音乐',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `musicgroup` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `groupName` char(64) NOT NULL COMMENT '音乐分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE `music` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `musicName` char(64) NOT NULL COMMENT '音乐名称',
  `musicComposer` char(64) DEFAULT NULL COMMENT '音乐作曲家',
  `musicFileMode` tinyint(1) DEFAULT NULL COMMENT '音乐文件存储方式 0:在本站 1:在外站',
  `musicFileUrl` text COMMENT '音乐文件路径',
  `musicUpdateUserId` int unsigned DEFAULT NULL COMMENT '上传用户id',
  `musicGroupId` int unsigned DEFAULT NULL COMMENT '分类id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8mb4;