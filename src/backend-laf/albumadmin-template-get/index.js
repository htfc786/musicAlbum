import cloud from '@/cloud-sdk'

const bucketName = "test";
const endpoint = "https://oss.lafyun.com/";

function getBucketName(bucketName) {
  const appid = cloud.env.APP_ID;
  return `${appid}-${bucketName}/`;
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  // 获取uid
  const access_token = body?.access_token || "";
  const user_info = cloud.parseToken(access_token);
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  if (!user_info.is_admin) {
    return { code: 401, error: "当前账号没有管理员权限！" };
  }
  const uid = user_info["uid"];

  // 获取数据
  const db = cloud.database();  // 数据库对象
  const photosDataList = await db
    .collection("album_template")
    .field({
      _id: true,
      templateName: true,
      templatePath: true,
      userId: true,
    })
    .get();

  /* if (!albumDataQuery.data) {
    return { code: 400, error: "未查询到任何图片" };
  } */

  var templateData = [];
  for (var i = 0; i < photosDataList.data.length; i++) {
    const photosData = photosDataList.data[i];
    //封面
    const templateCover = endpoint + getBucketName(bucketName) + photosData.templatePath + "cover.jpg";
    // 放入列表
    templateData.push({
      _id: photosData._id,
      templateName: photosData.templateName,
      templatePath: photosData.templatePath,
      templateCover: templateCover,
      userId: photosData.userId,
    });
  }

  return {
    code: 200,
    mag: "查询成功",
    data: templateData,
  }
}
