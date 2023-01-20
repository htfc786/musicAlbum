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
  
  console.log(return_str)
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
  const musicId = body?.musicId || "";

  if (!musicId) {
    return { code: 401, error: "请传入正确的参数" };
  }

  // 获取uid
  const user_info = cloud.parseToken(access_token);
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  if (!user_info.is_admin) {
    return { code: 401, error: "当前账号没有管理员权限！" };
  }
  const uid = user_info["uid"];

  const db = cloud.database();  // 数据库对象
  //查询信息
  const albumDataQuery = await db
    .collection("album_music")
    .where({
      _id: musicId,
    })
    .field({
      musicUrl: true,
    })
    .getOne();
  console.log(albumDataQuery,uid);
  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的照片id" };
  }

  //删除文件
  const musicUrl = albumDataQuery.data.musicUrl;
  const fileKey = await getAppFileKey(musicUrl);
  await deleteAppFile(bucketName, fileKey);
  //删除数据库
  const dbLog= await db
    .collection("album_music")
    .where({
      _id: musicId,
    })
    .remove();
  console.log(dbLog);

  return { code: 200, msg: "删除成功" }
}
