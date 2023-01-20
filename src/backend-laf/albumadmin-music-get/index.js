import cloud from '@/cloud-sdk'

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
  const photosData = await db
    .collection("album_music")
    .field({
      _id: true,
      musicName: true,
      musicComposer: true,
      musicUrl: true,
      userId: true,
      musicGroup: true,
    })
    .get();

  /* if (!albumDataQuery.data) {
    return { code: 400, error: "未查询到任何图片" };
  } */

  return {
    code: 200,
    mag: "查询成功",
    data: photosData.data,
  }
}
