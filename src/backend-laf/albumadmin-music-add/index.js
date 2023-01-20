import cloud from '@/cloud-sdk'
const AWS = require('aws-sdk');
const fs = require("fs");

const bucketName = "test";

function getFileName() {
  return new Date().getTime()+""+Math.floor(Math.random()*899+100);
}

function uploadAppFile(bucketName, key, uplaodFileObj) {
  const s3 =  new AWS.S3({ accessKeyId: cloud.env.OSS_ACCESS_KEY, secretAccessKey: cloud.env.OSS_ACCESS_SECRET, endpoint: "https://oss.lafyun.com", s3ForcePathStyle: true, signatureVersion: 'v4', region: cloud.env.OSS_REGION });
  // 处理文件对象
  const body = fs.readFileSync(uplaodFileObj.path);
  const contentType = uplaodFileObj.mimetype;
  // 文件桶
  const appid = cloud.env.APP_ID;
  const bucket = `${appid}-${bucketName}`;
  // 存文件
  const saveLog = s3.putObject({ Bucket: bucket, Key: key, ContentType: contentType, Body: body }).promise()
  console.log(saveLog);
  // 文件url
  const res = s3.getSignedUrl('getObject', { Bucket: bucket, Key: key })
  console.log(res);
  return res.split('?')[0]
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx;
  console.log(ctx)

  const access_token = body?.access_token || "";
  const musicName = body?.musicName || "";
  const musicComposer1 = body?.musicComposer || "";

  if (!musicName) {
    return { code: 400, error: "请传入正确的参数" };
  }
  if (musicName=='undefined') {
    return { code: 400, error: "请传入正确的参数" };
  }
  if (musicComposer1=='undefined') {
    const musicComposer = "";
  }
  const musicComposer = musicComposer1;

  // 获取uid
  const user_info = cloud.parseToken(access_token);
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  if (!user_info.is_admin) {
    return { code: 401, error: "当前账号没有管理员权限！" };
  }
  const uid = user_info["uid"];

  //文件
  const file = ctx.files[0];
  if (!((file.mimetype == "audio/mpeg" || //判断文件类型
    file.mimetype == "audio/x-wav" ||
    file.mimetype == "audio/x-m4a" || // audio/mpeg audio/x-wav audio/x-m4a audio/ogg
    file.mimetype == "audio/ogg") && //支持 .mp3 .m4a .wav .ogg 格式
    (file.size <= 5*1024*1024)) //最大5m 
  ) {
    return { neme: file.originalname, code: 400, msg: "音乐格式不正确！" };
  }
  // 上传文件信息
  const originalName = file.originalname;
  const filename = getFileName() + "." + originalName.split('.').slice(-1)[0];
  const filekey = "albummusic/" + filename;
  // 上传文件到云存储
  const fileUrl = await uploadAppFile(bucketName, filekey, file);

  const db = cloud.database();  // 数据库对象
  await db
    .collection("album_music")
    .add({
      musicName: musicName,
      musicComposer: musicComposer,
      musicUrl: fileUrl,
      userId: uid,
      musicGroup: "",
    });

  return { code: 200, msg: "上传成功" };

}