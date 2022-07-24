<?php  
    //配置数据库
    $confIniArray = parse_ini_file("./conf.ini", true);
    //print_r($confIniArray);
    $dbHost = $confIniArray["dbHost"];
    $dbUser = $confIniArray["dbUser"];
    $dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
    $dbDatabase = $confIniArray["dbDatabase"];
    $dbPort = $confIniArray["dbPort"];
    $dbEncoding = $confIniArray["dbEncoding"];

    $msgHtml = ''; //提示html
    //请求方式为post说明要注册
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        //判断信息完整性
        if((!(isset($_POST["username"])&&
            isset($_POST["password"])&&
            isset($_POST["confirm"])))||
            ($_POST["username"] == "" || 
            $_POST["password"] == "" || 
            $_POST["confirm"] == "")) {  
                $msgHtml = '<span style="color:red;">请确认信息完整性！</span>';  
                goto end; //跳转到结束
        }

        //判断两个密码是否一致
        if($_POST["password"] != $_POST["confirm"]) {
            $msgHtml = '<span style="color:red;">密码不一致！</span>';  
            goto end; //跳转到结束
        }

        //账号密码赋值变量
        $user = $_POST["username"];$psw = $_POST["password"]; 
        //连接数据库  
        $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);
        mysqli_query($db,"set names 'utf-8'"); //设定字符集  
        //查询user表里是否已经有了此用户
        $result = mysqli_query($db,"select username from user where username = '$_POST[username]'");

        //如果已经存在该用户  
        if(mysqli_num_rows($result)) {  
            $msgHtml = '<span style="color:red;">已经存在该用户！</span>';  
            goto end; //跳转到结束
        }
        
        //不存在当前注册用户名称 开始注册
        //执行插入的SQL
        $res_insert = mysqli_query($db,"insert into user (username,password) values('$_POST[username]','$_POST[password]')");  
        //$num_insert = mysql_num_rows($res_insert); 
        
        //如果插入失败执行 ！！！注意：这里有可能是
        if(!$res_insert) {
            $msgHtml = '<span style="color:red;">系统繁忙，请稍候！</span>';
        }
        //成功跳转到登录页
        header('refresh:1; url=../login.php?from=register'); 
        //跳转到这里
        end:
    } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册_音乐相册</title>
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
        <h1 id="page-title">注册</h1>
        <h6 id="little-page-title">by--htfc786</h6>
        <div id="msg"></div>
        <hr>
        <div id="page-title">
            <form action="register.php" method="post">  
                用户名：<input type="text" name="username"/> 
                <br/>  
                密码：<input type="password" name="password"/>  
                <br/>  
                确认密码：<input type="password" name="confirm"/>  
                <br/>  
                <input type="Submit" value="注册"/> 
                <?php echo $msgHtml; ?>
                <br/>
                <span>已有账号？<a href="login.php">登录</a></span>
            </form>
        </div>
        <hr>
    </div>
</body>
</html>