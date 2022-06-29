<?php
header('Content-type:text/html; charset=utf-8');

// 启用session
session_start();

// 清除Session

if (isset($_SESSION['username']) and $_SESSION['username']){
    $username = $_SESSION['username'];  //用于后面的提示信息
    $_SESSION = array();
    session_destroy();//清除

    // 跳转
    header('refresh:3; url=login.php');
    // 提示信息
    $msg = "提示：账号 ".$username.' 已经退出登录<br>';
} else {
    header('refresh:3; url=login.php');
    $msg = "提示：您没有登录任何账号！";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>退出登录_音乐相册</title>
    <style>
        body {
            background: #f3f3f3;
        }
        #big-border {
            background: #fff;
            margin: 0 auto;
            padding: 10px;
        }
		#page-title {
			text-align: center;
		}
        #little-page-title{
            text-align: right;
        }
    </style>
</head>
<body>
    <div id="big-border">
        <h1 id="page-title">退出登录</h1>
        <h6 id="little-page-title">by--htfc786</h6>
        <div id="msg"></div>
        <hr>
        <h4 id="page-title"><?php echo $msg; ?></h4>
        <hr>
    </div>
</body>
</html>

