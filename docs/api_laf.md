# API文档 LaF云开发版

### 用户前台

#### 用户登录
- 地址：`/albumapi-user-login`
- 请求方式：POST
- 请求参数：
  1. username 用户名
  2. password 密码
- 返回：json
  - code 返回请求值
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息
  - user_id  用户id
  - access_token  登录token


#### 用户注册
- 地址：`/albumapi-user-register`
- 请求方式：POST
- 请求参数：
  1. username 用户名
  2. password 密码
  3. confirm 二次密码
- 返回：json
  - code 返回请求值 200成功
  - msg  信息
  - user_id  新用户id

#### 查询我的相册
- 地址：`/albumapi-album-getmy`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息
  - data 数据 返回json 如下
    ```
      {
        length: albumData.length, 数据长度
        albumData: albumData, 个人相册数据 列表
      }
    ```
- 个人相册数据
  - _id 相册id
  - albumName 相册名称
  - albumCreateDate 创建时间
  - albumUserId 创建用户id
  - photoNum 图片张数
  - albumCover 相册封面图片URL

#### 删除相册
- 地址：`/albumapi-album-del`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. album_id 相册Id
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息

#### 添加相册
- 地址：`/albumapi-album-add`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. album_name 新相册名字 可无
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息
  - albumId 新相册Id

#### 查看-获取相册数据
- 地址：`/albumapi-show-albumdata`
- 请求方式：POST
- 请求参数：
  1. albumId 相册Id
- 返回：json
  - code 返回请求值
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息
  - data 相册数据 列表
- 相册数据
  - _id 相册id
  - albumName 相册名称
  - albumCreateDate 相册创建时间 年-月-日
  - albumUserId 创建用户Id
  - photoNum 照片张数
  - albumCover 相册封面地址

#### 查看-获取图片
- 地址：`/albumapi-show-photo-get`
- 请求方式：POST
- 请求参数：
  1. albumId 相册Id
- 返回：json
  - code 返回请求值
  - msg  信息
  - data 图片数据 列表
- 图片列表数据：
  - _id 图片id
  - photoOrder 图片顺序 (返回数据已按此排序)
  - photoUrl 图片URL
  - originalName 图片原名
  - photoText 图片写的文字

#### 制作-更改相册名
- 地址：`/albumapi-make-album-namechange`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. albumId 照片Id
  3. newName 相册新名字
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息


#### 制作-添加图片
- 地址：`/albumapi-make-photo-add`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. albumId 照片Id
  3. 图片文件，可以上传多个
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - fileType 文件处理信息 列表
- 文件处理信息
  - neme 图片原名 
  - code 代码 200成功
  - msg 返回信息

#### 制作-删除图片
- 地址：`/albumapi-make-photo-del`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. photoId 照片Id
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息

#### 制作-移动图片
- 地址：`/albumapi-make-photo-move`
- 请求方式：POST
- 请求参数：
  1. access_token 用户 token
  2. photoId 照片Id
  3. photoAction 传 up 或 down (向上或向下)
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息

#### 制作-修改文字
- 地址：`/albumapi-make-write-change`
- 请求方式：POST
- 请求参数：
  1. access_token 用户token
  2. photoId 照片Id
  3. photoNewText 图片写的文字
- 返回：json
  - code 返回请求值 200成功
  - error 处理失败时候的报错，只有处理失败是会有
  - msg  信息

### 后台管理
