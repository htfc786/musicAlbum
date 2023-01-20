import cloud from '@/cloud-sdk'

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const photoId = body?.photoId || "";
  const photoAction = body?.photoAction || "";

  if (!photoAction) {
    return { code: 401, error: "请传入完整的信息" };
  }
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
      albumId: true,
      photoOrder: true,
    })
    .getOne();
  console.log(albumDataQuery,uid);
  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的照片id" };
  }
  if (albumDataQuery.data.userId !== uid){
    return { code: 400, error: "您不是此相册的作者！" };
  }

  const albumId = albumDataQuery.data.albumId;
  const old_photoOrder = albumDataQuery.data.photoOrder;

  if (photoAction == "up") {
    if (old_photoOrder == 1){ //如果是第一章无法向前移动
      return { code: 400, error: "无法移动" };
    }
    //向前移动本张照片
    var log = await db
      .collection("album_photos")
      .where({
        _id: photoId,
      })
      .update({
        photoOrder: _.inc(-1), //-1
      })
    console.log(1,log)
    //向后移动后张照片
    const last_photoOrder = old_photoOrder - 1;
    var log = await db
      .collection("album_photos")
      .where({
        albumId: albumId,
        photoOrder: last_photoOrder,
      })
      .update({
        photoOrder: _.inc(1), //+1
      })
    
    console.log(2,log)
    return { code: 200, error: "移动成功" };
  } else if (photoAction == "down") {
    //获取最后一张
    const photoNum = (await db
      .collection("album_photos")
      .where({
        albumId: albumId,
      })
      .count())
      .total;
    if (old_photoOrder == photoNum){ //如果是最后一张无法向后移动
      return { code: 400, error: "无法移动" };
    }
    //向前移动本张照片
    await db
      .collection("album_photos")
      .where({
        _id: photoId,
      })
      .update({
        photoOrder: _.inc(1), //+1
      })
    //向后移动后张照片
    const next_photoOrder = old_photoOrder + 1;
    await db
      .collection("album_photos")
      .where({
        albumId: albumId,
        photoOrder: next_photoOrder,
      })
      .update({
        photoOrder: _.inc(-1), //-1
      })
    return { code: 200, error: "移动成功" };
  }

  console.log(photoAction);
  return { code: 404, error: "无法处理当前请求" };
}