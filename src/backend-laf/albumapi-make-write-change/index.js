import cloud from '@/cloud-sdk'

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const photoId = body?.photoId || "";
  const newText = body?.photoNewText || "";

  if (!newText) {
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
    .collection("album_photos")
    .where({
      _id: photoId,
    })
    .field({
      userId: true,
      photoOrder: true,
      photoUrl: true,
    })
    .getOne();
  console.log(albumDataQuery,uid);
  if (!albumDataQuery.data) {
    return { code: 400, error: "请传入正确的照片id" };
  }
  if (albumDataQuery.data.userId !== uid){
    return { code: 400, error: "您不是此相册的作者！" };
  }

  await db
    .collection("album_photos")
    .where({
      _id: photoId,
    })
    .update({
      photoText: newText,
    })

  return { code: 200, msg: "更改成功！" };
}
