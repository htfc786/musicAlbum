<?php  
    //配置数据库
    $confIniArray = parse_ini_file("../conf.ini", true);
    //print_r($confIniArray);
    $dbHost = $confIniArray["dbHost"];
    $dbUser = $confIniArray["dbUser"];
    $dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
    $dbDatabase = $confIniArray["dbDatabase"];
    $dbPort = $confIniArray["dbPort"];
    $dbEncoding = $confIniArray["dbEncoding"];

    $msgHtml = ''; //提示html
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $verification_code = strval(mt_rand());
        $verification_file = fopen("../verification.txt", "w");
        fwrite($verification_file, $verification_code);
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["verification"])) {  
            $user = $_POST["username"];  
            $psw = $_POST["password"];  
            $psw_confirm = $_POST["confirm"];  
            $verification_web = $_POST["verification"];
            if($user == "" || $psw == "" || $psw_confirm == "" || $verification_web == "") {  
                $msgHtml = '<span style="color:red;">请确认信息完整性！</span>';  
            } else {  
                $verification_file = fopen("../verification.txt", "r");
                $verification_code = fgets($verification_file);
                if($verification_web == $verification_code){
                    if($psw == $psw_confirm) {  
                        $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
                        //mysqli_select_db("my_test");  //选择数据库  
                        mysqli_query($db,"set names '$dbEncoding'"); //设定字符集  
                        $sql = "select username from user where username = '$_POST[username]'"; //SQL语句  
                        $result = mysqli_query($db,$sql);    //执行SQL语句  
                        $num = mysqli_num_rows($result); //统计执行结果影响的行数  
                        if($num) {  //如果已经存在该用户  
                            $msgHtml = '<span style="color:red;">已经存在该用户！</span>';  
                        }  else {   //不存在当前注册用户名称  
                            $sql_insert = "insert into user (username,password,isAdmin) values('$_POST[username]','$_POST[password]',1)";  
                            $res_insert = mysqli_query($db,$sql_insert);  
                            //$num_insert = mysql_num_rows($res_insert);  
                            if($res_insert)  {
                                unlink('../verification.txt');
                                header('refresh:1; url=login.php?from=register'); 
                            } else {  
                                $msgHtml = '<span style="color:red;">系统繁忙，请稍候！</span>';  
                            }  
                        }  
                    } else {  
                        $msgHtml = '<span style="color:red;">密码不一致！</span>';  
                    }  
                } else {
                    $msgHtml = '<span style="color:red;">验证码错误！</span>';
                }
            }  
        }
    }  
?><!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员账号注册_音乐相册</title>
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
        <h1 id="page-title">管理员注册</h1>
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
                验证码：<input type="text" name="verification"/>
                <br/>
                现在，需要确保您是此服务器的所有者。已在网站根目录中创建了"verification.txt"。请在验证码栏输入文件内的信息。
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
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员账号注册_音乐相册</title>
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
        <h1 id="page-title">管理员注册</h1>
        <h6 id="little-page-title">by--htfc786</h6>
        <div id="msg"></div>
        <hr>
        <div id="page-title">
            <form action="register.php" method="post"> 
                现在，需要确保您是此服务器的所有者。点击按钮在网站根目录中创建"verification.txt"。请在验证码栏输入文件内的信息。
                <br/>
                <input type="hidden" name="" value="Norway">
                验证码：<input type="text" name="verification"/>
                <br/>
                <input type="Submit" value="验证"/> 
                <?php echo $msgHtml; ?>
                <br/>
                <span>已有账号？<a href="login.php">登录</a></span>
            </form>
        </div>
        <hr>
    </div>
</body>
</html>