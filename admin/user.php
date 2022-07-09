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

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>用户管理</title>
    <link rel="stylesheet" href="../src/css/admin-user.css">
</head>
<body>
    <div class="container">
        <!--面板的情景样式-->
        <div class="panel">
            <div class="panel-heading">
            <!--面板的标题-->
            <h3 class="panel-title">用户管理</h3>
        </div>
        <!--面板的主体-->
        <!--在面板中嵌入一个表格-->
        <table class="table">
            <thead>
            <tr class="bg-success" >
                <td>ID</td>
                <td>用户名</td>
                <td>邮箱</td>
                <td>角色</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>admin</td>
                <td>admin@php.cn</td>
                <td>超级管理员</td>
                <td><a href="">编辑</a> | <a href="">删除</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>peter</td>
                <td>peter@pp.cn</td>
                <td>讲师</td>
                <td><a href="">编辑</a> | <a href="">删除</a></td>
            </tr>
            <tr>        
                <td>3</td>
                <td>zhu</td>
                <td>zhu@php.cn</td>
                <td>会员</td>
                <td><a href="">编辑</a> | <a href="">删除</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>猪哥</td>
                <td>zhuge@php.cn</td>
                <td>版主</td>
                <td><a href="">编辑</a> | <a href="">删除</a></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!--分页通常写到一对nav标签中,居中显示-->
            <nav class="text-center">
                <!--分页基类容器: .pagination-->
                <ul class="pagination pagination-md">
                    <li class="disabled"><a href="">«上一页</a></li>
                    <li class="active"><a href="">1</a></li>
                    <li><a href="">2</a></li>
                    <li><a href="">3</a></li>
                    <li><a href="">4</a></li>
                    <li><a href="">5</a></li>
                    <li><a href="">下一页»</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
</body>
</html>