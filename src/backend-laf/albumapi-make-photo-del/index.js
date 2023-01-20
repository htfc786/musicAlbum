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
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const photoId = body?.photoId || "";

  // 获取uid
  const user_info = cloud.parseToken(access_token);
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  const uid = user_info["uid"];

  const db = cloud.database();  // 数据库对象
  const _ = db.command;
  //验证
  const albumDataQuery = await db
    .collection("album_photos")
    .where({
      _id: photoId,
    })
    .field({
      userId: true,
      photoOrder: true,
      photoUrl: true,
    })
    .getOne();
  console.log(albumDataQuery,uid);
  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的照片id" };
  }
  if (albumDataQuery.data.userId !== uid){
    return { code: 400, error: "您不是此相册的作者！" };
  }
  const photoOrder = albumDataQuery.data.photoOrder
  //删除文件
  const photoUrl = albumDataQuery.data.photoUrl;
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

  //重新排序
  console.log(photoOrder)
  const dbLog2 = await db
    .collection("album_photos")
    .where({
      photoOrder: _.gt(photoOrder),//大于
    })
    .update({
      photoOrder: _.inc(-1), //-1
    })
  console.log(dbLog2)

  return { code: 200, msg: "删除成功" }
}
