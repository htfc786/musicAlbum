CREATE DATABASE my_test;

USE my_test;

CREATE TABLE user (
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  username char(25) NOT NULL COMMENT '用户名',
  password char(255) NOT NULL COMMENT '密码',
  isDel tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (id)
)CHARSET=utf8mb4;

CREATE TABLE album (
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  albumName char(20) NOT NULL COMMENT '名称',
  albumIntroduce char(255) DEFAULT NULL COMMENT '简介',
  albumCreateDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  albumTemplateId int unsigned NOT NULL COMMENT '模板id',
  albumTemplate char(20) COMMENT '模板',
  albumUseMusic tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否使用音乐',
  albumMusicName char(64) COMMENT '音乐名称',
  albumMusicUrl char(255) COMMENT '音乐url',
  albumMreatorId int NOT NULL COMMENT '创建用户id',
  isDel tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (id)
)CHARSET=utf8mb4;

CREATE TABLE photos (
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  mreatorId int unsigned NOT NULL COMMENT '用户id',
  albumId int unsigned NOT NULL COMMENT '相册id',
  photoOrder int unsigned NOT NULL COMMENT '图片顺序 从1开始',
  photoPath char(255) NOT NULL COMMENT '图片路径',
  photoUrl char(255) NOT NULL COMMENT '图片url',
  photoIntroduce char(16) DEFAULT NULL COMMENT '图片介绍',
  PRIMARY KEY (id)
)CHARSET=utf8mb4;

CREATE TABLE templats (
  id int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  templatName char(32) NOT NULL COMMENT '模板名称',
  templatIMG char(255) NOT NULL COMMENT '模板图片',
  PRIMARY KEY (id)
)CHARSET=utf8mb4;