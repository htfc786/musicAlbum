import cloud from '@/cloud-sdk'

const defaultAlbumName = "我的相册，打开看看";

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  const access_token = body?.access_token || "";
  const album_name = body?.album_name || defaultAlbumName;

  // 获取数据
  const user_info = cloud.parseToken(access_token);
  // 是否有
  if (!user_info) {
    return { code: 401, error: "请传入正确的token" };
  }
  // 获取uid
  const uid = user_info["uid"];
  console.log(uid);

  const db = cloud.database() //打开数据库
  // 在数据库里保存
  // 添加相册
  const { id } = await db
    .collection("album_albums")
    .add({
      albumName: album_name,
      albumCreateDate: new Date(),
      albumUserId: uid,
    });

  return {
    code: 200,
    msg: "添加成功",
    albumId: id,
  }
}
