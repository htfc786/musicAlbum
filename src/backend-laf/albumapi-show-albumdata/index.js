import cloud from '@/cloud-sdk'

const bucketName = "test";
const endpoint = "https://oss.lafyun.com/";

function getBucketName(bucketName) {
  const appid = cloud.env.APP_ID;
  return `${appid}-${bucketName}/`;
}

function dateToStr(date) {
  return date.getFullYear() + "-" + (date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1)) + "-" + (date.getDate() < 10 ? '0' + date.getDate() : date.getDate());
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const albumId = body?.albumId || "";

  const db = cloud.database();  // 数据库对象
  const albumDataQuery = await db
    .collection("album_albums")
    .where({
      _id: albumId,
    })
    .getOne();


  const albumDataFromDb = albumDataQuery.data;
  if (!albumDataFromDb) {
    return { code: 400, error: "没有此相册！" }
  }
  // 日期格式化
  const albumCreateDate = dateToStr(albumDataFromDb.albumCreateDate);
  // 图片数
  const photoNumQuery = await db
    .collection("album_photos")
    .where({
      albumId: albumId,
    })
    .count();
  const photoNum = photoNumQuery.total;
  // 封面
  const albumCoverQuery = await db
    .collection("album_photos")
    .where({
      albumId: albumId,
    })
    .orderBy("photoOrder", "asc")
    .field({
      photoUrl: true,
    })
    .getOne();
  const albumCover = albumCoverQuery.data?.photoUrl || "";
  //模板index.html
  const templateId = albumDataFromDb.templateId;
  const templateData = (await db
    .collection("album_template")
    .where({
      _id: templateId,
    })
    .field({
      templatePath: true,
    })
    .getOne()).data;
  const templateIndex = endpoint + getBucketName(bucketName) + templateData.templatePath + "index.html";
  // 放入列表
  const albumData = {
    _id: albumId,
    albumName: albumDataFromDb.albumName,
    albumCreateDate: albumCreateDate,
    albumUserId: albumDataFromDb.albumUserId,
    photoNum: photoNum,
    albumCover: albumCover,
    musicSave: albumDataFromDb.musicSave,
    musicUrl: albumDataFromDb.musicUrl,
    templateId: albumDataFromDb.templateId,
    templateIndex: templateIndex,
  }

  console.log("albumData: ", albumData);

  return {
    code: 200,
    msg: "查询成功",
    data: albumData,
  }
}
