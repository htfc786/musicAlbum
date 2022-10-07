<?php session_start(); // 开启Session ?>
<?php
if (!(isset($_SESSION['islogin']) && isset($_SESSION['isadmin']) && $_SESSION['isadmin'])) {
    // 没有登录
    header('refresh:0; url=./login.php');
    echo '提示：请先<a href="./login.php"></a>登录！！！';
    return;
}
// 已经登录
$username = $_SESSION['username'];  //用户名

//读取配置文件
$confIniArray = parse_ini_file("../conf.ini", true);

$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];
$dbEncoding = $confIniArray["dbEncoding"];

$adminAlbumPageNum = $confIniArray["adminAlbumPageNum"];

//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

//获取page参数
$page = 1;
if (isset($_GET["page"])&&$_GET["page"]){
    $page = $_GET["page"];
}



//获取album表有多少条数据 
$albumDataNum = mysqli_query($db,"select count(*) from album;"); 
$albumDataNum = mysqli_fetch_assoc($albumDataNum)["count(*)"];

//多少页
//向上取整
$allPage = Ceil($albumDataNum / $adminAlbumPageNum);
if ($allPage==0) $allPage = 1;

if (!is_numeric($page) || $allPage<$page){
    $page = $allPage;
}

//从数据库哪一行开始 需-1
$startRow = ($page - 1) * $adminAlbumPageNum;

//从数据库理获取数据
$albumData = mysqli_query($db,"select * from album limit $startRow,$adminAlbumPageNum;");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>相册管理</title>
    <link rel="stylesheet" href="../src/css/admin-template.css">
</head>
<body>

</body>
</html>