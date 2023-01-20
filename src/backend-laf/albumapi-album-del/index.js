import cloud from '@/cloud-sdk'
const AWS = require('aws-sdk');

const bucketName = "test";

function getAppFileKey(fileUrl) {
  var url = fileUrl.split('?')[0];
  var url_list = url.split('/')
  var is_see = false;
  const appid = cloud.env.APP_ID;
  const bucket = `${appid}-${bucketName}`;
  var return_str = "";
  for (var i = 0; i < url_list.length; i++) {
    if (is_see) {
      if (i == url_list.length - 1) { return_str += url_list[i]; }
      else { return_str += url_list[i] + "/"; } 
    }
    if (url_list[i] == bucket) { is_see = true; }
  }
  return return_str;
}

function deleteAppFile(bucketName, key) {
  const s3 =  new AWS.S3({ accessKeyId: cloud.env.OSS_ACCESS_KEY, secretAccessKey: cloud.env.OSS_ACCESS_SECRET, endpoint: "https://oss.lafyun.com", s3ForcePathStyle: true, signatureVersion: 'v4', region: cloud.env.OSS_REGION });
  // 处理文件对象
  // 文件桶
  const appid = cloud.env.APP_ID;
  const bucket = `${appid}-${bucketName}`
  //删除
  const res = s3.deleteObject({ Bucket: bucket, Key: key }).promise()
  console.log(res);
  return res
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx;

  const access_token = body?.access_token || "";
  const album_id = body?.album_id || "";

  if (!album_id) {
    return { code: 400, error: "请传入相册id" };
  }

  // 获取数据
  const user_info = cloud.parseToken(access_token);
  // 是否有
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  // 获取uid
  const uid = user_info["uid"];
  console.log(uid);

  const db = cloud.database(); //打开数据库
  console.log(album_id);
  //先查询作者 是否作者本人删除
  const albumDataQuery = await db
    .collection("album_albums")
    .where({
      _id: album_id,
    })
    .getOne();
  const albumData = albumDataQuery?.data || null;

  if (!albumData) {
    return { code: 400, error: "请传入正确的相册id" };
  }

  const albumUserId = albumData.albumUserId;

  if (albumUserId != uid) {
    return { code: 400, error: "您不是此相册的主人" };
  }

  //删图片
  const photosDataList = await db
    .collection("album_photos")
    .where({
      albumId: album_id,
    })
    .field({
      _id: true,
      photoUrl: true,
    })
    .get();
  const photosDataFromDbList = photosDataList?.data || [];
  for (var i = 0; i < photosDataFromDbList.length; i++) {
    const photoUrl = photosDataFromDbList[i].photoUrl;
    const photoId = photosDataFromDbList[i]._id;
    //删除文件
    const fileKey = await getAppFileKey(photoUrl);
    await deleteAppFile(bucketName, fileKey);
    //删除数据库
    const dbLog= await db
      .collection("album_photos")
      .where({
        _id: photoId,
      })
      .remove();
    console.log(dbLog);
  }
  

  //删除数据库
  const delData = await db
    .collection("album_albums")
    .where({
      _id: album_id,
    })
    .remove();
  console.log(delData)

  return { code: 200, msg: "删除成功" };
}