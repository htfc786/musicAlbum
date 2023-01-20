import cloud from '@/cloud-sdk';
import { createHash } from "crypto";

exports.main = async function (ctx: FunctionContext) {
  // body, query 为请求参数, auth 是授权对象
  const { auth, body, query } = ctx

  // 获取密码，没有为空
  const username = body?.username || "";
  const password = body?.password || "";
  const confirm = body?.confirm || "";

  //判断两个密码是否相等
  if (password !== confirm) return { code: 400, msg: "两次输入的密码不一样" };

  //判断是否符合标准空
  if (!/[a-zA-Z0-9]{3,16}/.test(username)) return { code: 400, msg: "用户名不符合要求" };
  if (!/[a-zA-Z0-9]{3,16}/.test(password)) return { code: 400, msg: "密码不符合要求" };

  // 数据库操作
  const db = cloud.database() //打开数据库

  // 是否已有此用户
  const exists = await db
    .collection("users")
    .where({ username: username })
    .count();
  if (exists.total > 0) return { code: 400, msg: "数据库里已经有此用户了" };

  // 在数据库里保存
  // 添加用户
  const { id } = await db.collection("album_users").add({
    username: username,
    password: createHash("sha256").update(password).digest("hex"),
    created_at: new Date(),
    is_admin: false
  });

  console.log("又有一个新的用户注册成功了, user_id: ", id);

  return { code: 200, msg: "注册成功", user_id: id }
}
