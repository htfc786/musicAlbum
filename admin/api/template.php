<?php /*
/admin/api/template.php
系统管理-api-模板管理
POST请求
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
switch ($_GET["do"])
{
    case "del":
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

?>