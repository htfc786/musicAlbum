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

  // 获取数据
  const db = cloud.database();  // 数据库对象
  const photosDataList = await db
    .collection("album_template")
    .field({
      _id: true,
      templateName: true,
      templatePath: true,
    })
    .get();

  /* if (!albumDataQuery.data) {
    return { code: 400, error: "未查询到任何图片" };
  } */
  var templateData = [];
  for (var i = 0; i < photosDataList.data.length; i++) {
    const photosData = photosDataList.data[i];
    console.log(photosData)
    //封面
    const templateCover = endpoint + getBucketName(bucketName) + photosData.templatePath + "cover.jpg";
    // 放入列表
    templateData.push({
      _id: photosData._id,
      templateName: photosData.templateName,
      templateCover: templateCover,
    });
  }

  return {
    code: 200,
    mag: "查询成功",
    data: templateData,
  }
}
