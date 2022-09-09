<?php /*
/admin/api/user.php
系统管理-api-用户管理
POST请求
删除用户:
    请求参数
        GET方式:
        - do    做什么？ 参数：del
        POST方式：
        - uid    UID
添加用户:
    请求参数
        GET方式:
        - do    做什么？ 参数：add
        POST方式：
        - username 用户名
        - password 密码
        - confirm 密码验证
        - isAdmin 是否管理员
*/ ?>
<?php session_start(); // 开启Session ?>
<?php
if (!(isset($_SESSION['islogin']) && isset($_SESSION['isadmin']) && $_SESSION['isadmin'])) {
    // 没有登录
    //header('refresh:0; url=./login.php');
    //$codeNoLogin = $conf["codeNoLogin"];
    echo "请先登录";
    return;
}
// 已经登录
//$username = $_SESSION['username'];  //管理员用户名

//判断请求方式
if (!($_SERVER['REQUEST_METHOD'] === 'POST')){
    echo "请求方式错误";
    return;
}

//请求参数 此处只判断do
if(!(isset($_GET["do"]) && $_GET["do"])){
    echo "请求参数错误";
    return;
}

//此处按需判断其他参数
switch ($_GET["do"]) {
    case "del": //删除用户参数判断
        if(!(isset($_POST["uid"]) && $_POST["uid"])){
            echo "请求参数错误";
            return;
        }
        $uid = $_POST["uid"];
        break;
    
    case "add": //添加用户参数判断 
        if((isset($_POST["username"])&&
            isset($_POST["password"])&&
            isset($_POST["confirm"])&&
            isset($_POST["isAdmin"]))&&
            ($_POST["username"] == "" &&
            $_POST["password"] == "" &&
            $_POST["confirm"] == "" &&
            $_POST["isAdmin"] == "undefined")
        ){
            echo "请求参数错误";
            return;//$_POST["isAdmin"] == "undefined"
        }
        break;

    default:
        echo "没有此方式";
        return;

}


//连接数据库
//读取配置
$confIniArray = parse_ini_file("../../conf.ini", true);
//数据库配置
$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];
$dbEncoding = $confIniArray["dbEncoding"];
//连接!
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

switch ($_GET["do"]) {
    case "del": //此处为用户部分删除代码
        //有这人吗？
        $userInfo = mysqli_query($db,"SELECT * FROM user WHERE id = $uid");
        if(!mysqli_num_rows($userInfo)==1) {  
            echo "没这人！";
            return;
        }

        $row = mysqli_fetch_array($userInfo);
        $username = $row['username'];
        //删库
        //删除该用户下的所有数据
        $delState = mysqli_query($db,"DELETE FROM user WHERE id = $uid");

        if (!$delState){
            //删除失败
            echo "删除失败";
            return;
        }
        //删除成功
        echo "用户：$username 删除成功";
        break;

    case "add": //添加用户
        //判断两个密码是否一致
        if($_POST["password"] != $_POST["confirm"]) {
            echo '密码不一致！';  
            return;
        }
        //账号密码赋值变量
        $user = $_POST["username"];
        $psw = $_POST["password"]; 
        $isAdminText = $_POST["isAdmin"];

        $isAdmin = 0;
        if($isAdminText=="yes"){
            $isAdmin = 1;
        }else if($isAdminText=="no"){
            $isAdmin = 0;
        }

        //查询user表里是否已经有了此用户
        $result = mysqli_query($db,"select username from user where username = '$_POST[username]'");
        //如果已经存在该用户  
        if(mysqli_num_rows($result)) {  
            echo '已经存在该用户！';  
            return;
        }
        //不存在当前注册用户名称 开始注册
        //执行插入的SQL
        $res_insert = mysqli_query($db,"insert into user (username,password,isAdmin) values('$user','$psw',$isAdmin)");  
        //$num_insert = mysql_num_rows($res_insert); 
        
        //如果插入失败执行 ！！！注意：这里有可能是
        if(!$res_insert) {
            echo '系统繁忙，请稍候！';
            return;
        }
        echo '添加成功';
}

?>