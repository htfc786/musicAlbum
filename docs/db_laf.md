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

#### album_photos 照片表
- _id 图片id 自动生成
- userId 上传用户id
- albumId 所属相册id
- photoOrder 图片顺序
- photoUrl 图片URL
- originalName 图片原名
- photoText 写的文字
