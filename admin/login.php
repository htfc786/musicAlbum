<?php  
    $confIniArray = parse_ini_file("../conf.ini", true);
    $PrePath = $confIniArray["PrePath"];
    //配置数据库
    //$confIniArray = parse_ini_file("../conf.ini", true);
    //print_r($confIniArray);
    $dbHost = $confIniArray["dbHost"];
    $dbUser = $confIniArray["dbUser"];
    $dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
    $dbDatabase = $confIniArray["dbDatabase"];
    $dbPort = $confIniArray["dbPort"];
    $dbEncoding = $confIniArray["dbEncoding"];

    // 开启Session
    session_start();
    $msgHtml = ''; //提示html
    $scriptHtml = '';//js (禁用输入框)
    //从注册而来给与提示信息0
    if (isset($_GET["from"])&&$_GET["from"]=="register"){
        $msgHtml = '<span style="color:green;">注册成功，请登录！</span>';
    }
    //已经登录无需重复登录
    if (isset($_SESSION['username']) && $_SESSION['username']){
        if (isset($_SESSION['isadmin']) && $_SESSION['isadmin']){
            header('refresh:1; url='.$PrePath.'admin/');
            $msgHtml = '<span style="color:red;">当前已登录，无需再次登录</span>';  
            $scriptHtml = '<script>document.getElementById("username").disabled=true;document.getElementById("password").disabled=true;document.getElementById("submitButton").disabled=true</script>';
        } else {
            $msgHtml = '<span style="color:red;">当前已登录了一个非管理员账号，请先<a href="'.$PrePath.'logout.php?from=admin-login">退出登录</a></span>';  
            $scriptHtml = '<script>document.getElementById("username").disabled=true;document.getElementById("password").disabled=true;document.getElementById("submitButton").disabled=true</script>';
        }
       
    }
    //请求方式为post说明要登录
    if($_SERVER['REQUEST_METHOD'] === 'POST') {  
        //查看用户名密码密码是否存在
        if (!(isset($_POST["username"])&&isset($_POST["password"]))){
            $msgHtml = '<span style="color:red;">提交信息有误！</span>';
            goto end; //跳转到结束 
        } 

        //用户名和密码都不能为空
        if($_POST["username"] == "" || $_POST["password"] == "")  {  
            $msgHtml = '<span style="color:red;">请输入用户名或密码！</span>';
            goto end; //跳转到结束  
        }

        //获取前端传传来的用户名密码
        $user = $_POST["username"];  
        $psw = $_POST["password"]; 
        //连接数据库  
        $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);
        //mysql_select_db("my_test");  //选择数据库  
        mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 
        
        //查询用户是否存在
        $rs = mysqli_query($db,"select id,username,password from user where username = '$_POST[username]' and password = '$_POST[password]' and isAdmin = 1");  //执行sql！！！
        //获取有多少个（正常应该有只一个）
        //没有说明户名或密码不正确或不是管理员
        if(!mysqli_num_rows($rs)==1) {  
            $msgHtml = '<span style="color:red;">用户名或密码不正确！</span>'; 
            goto end; //跳转到结束 
        }
        //读取数据
        $row = mysqli_fetch_array($rs);  //将数据以索引方式储存在数组中  
        $userid = $row['id'];
        $username = $row['username'];
        //Session
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = $username;
        $_SESSION['isadmin'] = 1;
        $_SESSION['islogin'] = 1;
        //设置Cookie
        //setcookie('username', $username, time()+7*24*60*60);
        //setcookie('code', md5($username.md5($password)), time()+7*24*60*60);
        //提示信息
        header('refresh:1; url='.$PrePath.'admin/');
        $msgHtml = '<span style="color:green;">登录成功！</span>';  
        $scriptHtml = '<script>document.getElementById("username").disabled=true;document.getElementById("password").disabled=true;document.getElementById("submitButton").disabled=true</script>';
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
    <title>管理员账号登录_音乐相册</title>
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
        <h1 id="page-title">管理员登录</h1>
        <h6 id="little-page-title">by--htfc786</h6>
        <div id="msg"></div>
        <hr>
        <div id="page-title">
            <form action="login.php" method="post">  
                用户名：<input id="username" type="text" name="username" />  
                <br/>  
                密码：<input id="password" type="password" name="password" />  
                <br/>
                <input id="submitButton" type="submit" value="登录" />  
                <?php echo $msgHtml; ?>
                <br/>  
                <span>没有账号？<a href="register.php">注册</a>  </span>
            </form>
        </div>
        <hr>
    </div>
    <?php echo $scriptHtml; ?>
</body>
</html>