<?php
function getId(){	//获取请求id
    $php_self_name = basename(__FILE__);//php脚本名称
	$meet_php = 0;//False;//是否遇见这个php脚本名称
	$last_path = "";//请求php文件后面写的东西
	$urls = explode("/",$_SERVER['PHP_SELF']);//用“/”分割字符串
	foreach ($urls as $i){
			//echo $i."/";
		if ($meet_php) {	//遇见这个php脚本名称之后添加今路径变量里
			$last_path = $last_path."/".$i;
		}
		if ($i == $php_self_name){//如果遇见这个php脚本名称标记一下
			$meet_php = 1;//True;
		}
	}
    if ($last_path){
        $urls = explode("/",$last_path);//用“/”分割字符串
        return $urls[1];
    }
    return "";
}

$confIniArray = parse_ini_file("./conf.ini", true); //配置文件
$PrePath = $confIniArray["PrePath"];

session_start(); // 开启Session
if (!isset($_SESSION['islogin'])) {
    // 没有登录
    header('refresh:0; url='.$PrePath.'login.php');
    echo "<h4 id='page-title'>您还没有登录,请登录,3秒后自带跳转</h4>";
    return;
}
// 已经登录
$username = $_SESSION['username'];//用户名
//判断aid
if (!getId()) {
    echo "<h4 id='page-title'>aid参数出错</h4>";
    return;
}
//配置数据库
$confIniArray = parse_ini_file("./conf.ini", true);
//print_r($confIniArray);
$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];
//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names 'utf-8'"); //设定字符集 
//-------

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提取图片</title>
    <link rel="stylesheet" href="../src/css/main-albumphoto.css">
</head>
<body>
    <div id="image-list">
    <?php 
    $aid = getId();
    //$sql=" select id,photoUrl,originalName from photos where albumId = $aid";
    $rs = mysqli_query($db," SELECT id,photoUrl,originalName FROM photos WHERE albumId = $aid ORDER BY photos.photoOrder ASC");  //执行sql！！！
    if(mysqli_num_rows($rs)) { 
        //$row = mysqli_fetch_array($rs);  //将数据以索引方式储存在数组中  
        $i=0;
        while ($row=mysqli_fetch_assoc($rs)){
            $i++;
            $photoId = $row['id'];
            $photoUrl = $row['photoUrl'];
            $originalName = $row["originalName"];
            echo <<<END
            <div class="image-card">
                <div class="image-index">$i</div>
                <img src="$photoUrl" class="image-photo">
                <a href="$photoUrl" download="$originalName" class="image-down">点击下载</a>
            </div>
            END;
        }
    } else {
        echo "此相册没有图片！";
    }
    ?>
    </div>
    <div onclick="window.location.href='/'" class="close-btn">返回</div>
</body>
</html>