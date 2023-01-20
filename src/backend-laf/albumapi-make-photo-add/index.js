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
  return res.split('?')[0]
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx;
  //console.log(ctx);

  const access_token = body?.access_token || "";
  const albumId = body?.albumId || "";

  // 获取uid
  const user_info = cloud.parseToken(access_token);
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  const uid = user_info["uid"];

  const db = cloud.database();  // 数据库对象
  //验证
  const albumDataQuery = await db
    .collection("album_albums")
    .where({
      _id: albumId,
    })
    .field({
      albumUserId: 1,
    })
    .getOne();

  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的相册id" };
  }
  if (albumDataQuery.data.albumUserId != uid){
    return { code: 400, error: "您不是此相册的作者！" };
  }

  var fileTypes = [];
  //上传到云存储
  for (var i = 0; i < ctx.files.length; i++) {
    const file = ctx.files[i];
    // 判断是否图片 图片大小
    if (!((file.mimetype == "image/gif" ||
      file.mimetype == "image/jpeg" ||
      file.mimetype == "image/png" ||
      file.mimetype == "image/bmp" || // image/gif image/jpeg image/png image/bmp image/webp
      file.mimetype == "image/webp") && //支持 .gif .jpg .jpeg .png .bmp .webp 格式
      (file.size <= 5*1024*1024)) //5m 
    ) {
      fileTypes.push({ neme: file.originalname, code: 400, msg: "图片格式不正确！" })
      //return ;
      continue;
    }
    // 上传文件信息
    const originalName = file.originalname;
    const filename = getFileName() + "." + originalName.split('.').slice(-1)[0];
    const filekey = "albumphoto/" + filename;
    // 上传文件到云存储
    const fileUrl = await uploadAppFile(bucketName, filekey, file);
    // 查询图片张数
    const photoNum = (await db
      .collection("album_photos")
      .where({ albumId: albumId })
      .count()).total;
    const photoOrder = photoNum + 1;
    // 保存文件信息到数据库
    await db
      .collection("album_photos")
      .add({
        userId: uid,
        albumId: albumId,
        photoOrder: photoOrder,
        photoUrl: fileUrl,
        originalName: originalName,
        photoText: "",
      });

    fileTypes.push({ neme: file.originalname, code: 200, msg: "上传成功" })
    
  }
  return {
    code: 200,
    fileType: fileTypes
  }
}
