import cloud from '@/cloud-sdk'

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const albumId = body?.albumId || "";
  const musicId = body?.musicId || "";

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
  //查数据库
  const musicQuery = await db
    .collection("album_music")
    .where({
      _id: musicId,
    })
    .field({
      musicUrl: true,
    })
    .getOne();
  if (!musicQuery.data) {
    return { code: 400, error: "请传入正确的音乐id" };
  }
  const musicUrl = musicQuery.data.musicUrl;
  console.log(musicUrl)

  //改数据库
  await db
    .collection("album_albums")
    .where({
      _id: albumId,
    })
    .update({
      musicSave: "file",
      musicUrl: musicUrl,
    })


  return { code: 200, msg: "更改成功！" };
}
