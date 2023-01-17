# 数据库文档 LaF云开发版

#### album_users 用户表
- _id 用户id 自动生成
- username 用户名
- password 密码 经过sha256摘要
- created_at 注册时间 Date类型
- is_admin 是否管理员 bool类型

#### album_albums 相册表
- _id 相册id 自动生成
- albumName 相册名称
- albumCreateDate 创建时间 Date类型
- albumUserId 创建用户
- albumCover 相册封面图片URL
- musicSave 相册音乐存储方式
 - file 服务器音乐文件
 - music163 网易云外链
- musicUrl 相册Url
  - 如果是服务器音乐文件 直接存url
  - 网易云外链 存网易云的音乐id
- templateId 模板id

#### album_photos 照片表
- _id 图片id 自动生成
- userId 上传用户id
- albumId 所属相册id
- photoOrder 图片顺序
- photoUrl 图片URL
- originalName 图片原名
- photoText 写的文字

#### album_music 音乐表
- _id 音乐id 自动生成
- musicName 音乐名称
- musicComposer 音乐作曲家
- musicUrl 图片URL
- userId 上传用户id
- musicGroup 音乐分类

#### album_template 模板表
- _id 模板id 自动生成
- templateName 模板名称
- templatePath 模板路径
- userId 上传用户id
- templateGroup 分类
