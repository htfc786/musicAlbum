import cloud from '@/cloud-sdk'

function dateToStr(date) {
  return date.getFullYear() + "-" + (date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1)) + "-" + (date.getDate() < 10 ? '0' + date.getDate() : date.getDate());
}

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  // 获取数据
  const user_info = cloud.parseToken(access_token);
  // 是否有
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  // 获取uid
  const uid = user_info["uid"];
  console.log("uid: ", uid);

  const db = cloud.database();  // 数据库对象
  const albumDataQuery = await db
    .collection("album_albums")
    .where({
      albumUserId: uid,
    })
    .orderBy("albumCreateDate", "desc")
    .get();

  const albumDataFromDbList = albumDataQuery?.data || [];
  var albumData = [];

  for (var i = 0; i < albumDataFromDbList.length; i++) {
    const albumDataFromDb = albumDataFromDbList[i];
    const albumId = albumDataFromDb._id;
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
    // 放入列表
    albumData.push({
      _id: albumId,
      albumName: albumDataFromDb.albumName,
      albumCreateDate: albumCreateDate,
      albumUserId: albumDataFromDb.albumUserId,
      photoNum: photoNum,
      albumCover: albumCover,
    });
  }

  console.log("albumData: ", albumData);

  return {
    code: 200,
    msg: "查询成功",
    data: {
      length: albumData.length,
      albumData: albumData,
    }
  }
}
