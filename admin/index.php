<?php session_start(); // 开启Session ?>
<?php
$confIniArray = parse_ini_file("../conf.ini", true);
$PrePath = $confIniArray["PrePath"];
if (!(isset($_SESSION['islogin']) && isset($_SESSION['isadmin']) && $_SESSION['isadmin'])) {
    // 没有登录
    header('refresh:0; url='.$PrePath.'admin/login.php');
    echo '提示：请先<a href="'.$PrePath.'admin/login.php"></a>登录！！！';
    return;
}
// 已经登录
$username = $_SESSION['username'];  //用户名

?>
<!--感谢 https://www.php.cn/blog/detail/8373.html 提供的html代码-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../src/css/admin-index.css">
    <title>音乐相册后台管理</title>
</head>
<body>
<!--顶部信息区-->
<header role="header">
    <div>
        <h1>音乐相册后台管理</h1>
        <nav role="user">
            <ul>
                <li>欢迎管理员:<strong><?php echo $username; ?></strong></li>
                <li><a href="modify_pass.html" target="main">修改密码</a></li>
                <li><a href="javascript:void(0);" onclick="logout()">退出登录</a></li>
            </ul>
        </nav>
    </div>
</header>
<!--圣杯二列布局-->
<main role="main">
    <!--主体内联框架区-->
    <article role="content">
        <iframe src="user.php" name="main"></iframe>
    </article>
    <!--左侧导航区-->
    <aside>
        <nav role="option">
            <ul>
                <li>> 菜单 <</li>
                <li><a href="user.php" target="main">用户管理</a></li>
                <li><a href=".html" target="main">相册管理</a></li>
                <li><a href="template.php" target="main">模板管理</a></li>
                <li><a href=".html" target="main">音乐管理</a></li>
                <li><a href=".html" target="main">系统设置</a></li>
            </ul>
        </nav>
    </aside>
</main>
<script>
    function logout() {
        if (window.confirm('是否退出?')) {
            window.location.href = '../logout.php';
        } else {
            return false;
        }
    }
</script>
</body>
</html>
