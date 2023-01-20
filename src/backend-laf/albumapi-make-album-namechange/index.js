import cloud from '@/cloud-sdk'

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const albumId = body?.albumId || "";
  const newName = body?.newName || "";

  if (!newName) {
    return { code: 400, error: "请传入正确的参数" };
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
    .collection("album_albums")
    .where({
      _id: albumId,
    })
    .field({
      albumUserId: true,
    })
    .getOne();
  console.log(albumDataQuery,uid);
  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的照片id" };
  }
  if (albumDataQuery.data.albumUserId !== uid){
    return { code: 400, error: "您不是此相册的作者！" };
  }

  await db
    .collection("album_albums")
    .where({
      _id: albumId,
    })
    .update({
      albumName: newName,
    })

  return { code: 200, msg: "更改成功！" };
}
