import cloud from '@/cloud-sdk'

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  // 获取数据
  const db = cloud.database();  // 数据库对象
  const photosData = await db
    .collection("album_music")
    .field({
      _id: true,
      musicName: true,
      musicComposer: true,
      musicUrl: true,
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
