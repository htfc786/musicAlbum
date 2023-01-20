import cloud from '@/cloud-sdk'
const AWS = require('aws-sdk');
const fs = require("fs");

const bucketName = "test";

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
  console.log(ctx);

  const access_token = body?.access_token || "";
  const templateName = body?.templateName || "";

  if (!templateName) {
    return { code: 400, error: "请传入正确的参数" };
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
  const { id } = await db
    .collection("album_template")
    .add({
      templateName: templateName,
      templatePath: "",
      userId: uid,
      musicGroup: "",
    });

  //文件
  const file = ctx.files[0];
  if (!((file.mimetype == "image/gif" ||
      file.mimetype == "image/jpeg" ||
      file.mimetype == "image/png" ||
      file.mimetype == "image/bmp" || // image/gif image/jpeg image/png image/bmp image/webp
      file.mimetype == "image/webp") && //支持 .gif .jpg .jpeg .png .bmp .webp 格式
      (file.size <= 5*1024*1024)) //最大5m 
  ) {
    return { neme: file.originalname, code: 400, msg: "格式不正确！" };
  }
  // 上传文件信息
  const filekey = "albumtemplate/" + id + "cover.jpg";
  // 上传文件到云存储
  await uploadAppFile(bucketName, filekey, file);

  const templatePath = "albumtemplate/" + id + "/";

  await db
    .collection("album_template")
    .where({
      _id: id,
    })
    .update({
      templatePath: templatePath,
    })
  
  return { code: 200, msg: "上传成功", templatePath: templatePath };

}