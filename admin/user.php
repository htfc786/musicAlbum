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

$adminUserPageNum = $confIniArray["adminUserPageNum"];

//获取page参数
$page = 1;
if (isset($_GET["page"])&&$_GET["page"]){
    $page = $_GET["page"];
}

//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

//获取user表有多少条数据 
$userDataNum = mysqli_query($db,"select count(*) from user;"); 
$userDataNum = mysqli_fetch_assoc($userDataNum)["count(*)"];

//多少页
//向上取整
$allPage = Ceil($userDataNum / $adminUserPageNum);

if (!is_numeric($page) || $allPage<$page){
    $page = $allPage;
}

//从数据库哪一行开始 需-1
$startRow = ($page - 1) * $adminUserPageNum;

//从数据库理获取数据
$userData = mysqli_query($db,"select * from user limit $startRow,$adminUserPageNum;"); 



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
            <div class="panel-heading" style="position: relative;">
                <!--面板的标题-->
                <h3 class="panel-title">用户管理</h3>
                <div style="position: absolute;display: block;top: 0;right: 0;margin: 10px;">
                    <a href="">重新加载</a>
                    <a class="addUserText" href="javascript:void(0);" style="padding: 8px;">添加用户</a>
                </div>
            </div>
            <!--面板的主体-->
            <!--在面板中嵌入一个表格-->
            <?php
            if (mysqli_num_rows($userData)==0){   //没有数据
                echo <<<END
                <div class="panel-heading">
                    <h3 class="panel-title">暂无数据</h3>
                </div>
                END;
                goto end;
            }
            echo <<<END
            <table class="table">
                <thead>
                    <tr class="bg-success">
                        <td style="width: 10px;">#</td>
                        <td>UID</td>
                        <td>用户名</td>
                        <td>权限</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            END;

            $i = 0;
            while ($userDataRow=mysqli_fetch_assoc($userData)){
                $i++;
                $uid = $userDataRow["id"];
                $username = $userDataRow["username"];
                $jurisdiction = "用户";
                if ($userDataRow["isAdmin"]){
                    $jurisdiction = "管理员";
                }
                $UserRowNum = $startRow+$i;
                echo <<<END
                <tr>
                    <td>$UserRowNum</td>
                    <td>$uid</td>
                    <td>$username</td>
                    <td>$jurisdiction</td>
                    <td>
                        <a href="">编辑</a> |
                        <a href="javascript:void(0);" onclick="delUser($uid)">删除</a>
                    </td>
                </tr>
                END;
            }

            echo <<<END
                </tbody>
            </table>
            END;

            end: //跳转到这里
            ?>
        </div>
        <div class="row">
            <ul class="be-pager">
                <?php
                if ($page!=1){
                    $FootLastPage = $page - 1;
                    echo <<<END
                    <li title="首页:1" class="be-pager-prev"><a href="?page=1">首页</a></li>
                    <li title="上一页:$FootLastPage" class="be-pager-prev"><a href="?page=$FootLastPage">上一页</a></li>
                    END;
                }
                /* 中间的选择块 控制逻辑比较复杂 我还没有想好
                echo <<<END
                <li title="1" class="be-pager-item"><a href="">1</a></li>
                <li class="be-pager-item-jump-prev"></li>
                <li title="3" class="be-pager-item"><a href="">3</a></li>
                <li title="4" class="be-pager-item"><a href="">4</a></li>
                <li title="5" class="be-pager-item be-pager-item-active"><a href="">5</a></li>
                <li title="6" class="be-pager-item"><a href="">6</a></li>
                <li title="7" class="be-pager-item"><a href="">7</a></li>
                <li class="be-pager-item-jump-next"></li>
                <li title="最后一页" class="be-pager-item"><a href="">12</a></li>
                END;
                */
                if ($page!=$allPage){
                    $FootNextPage = $page + 1;
                    echo <<<END
                    <li title="下一页:$FootNextPage" class="be-pager-prev"><a href="?page=$FootNextPage">下一页</a></li>
                    <li title="尾页:$allPage" class="be-pager-prev"><a href="?page=$allPage">尾页</a></li>
                    END;
                }
                echo <<<END
                <span class="be-pager-total">第 $page 页，共 $allPage 页，</span>
                <span class="be-pager-options-elevator">
                    跳至 <input type="text" class="space_input" id="page_input"> 页
                </span>
                END;
                ?>
            </ul>
        </div>
    </div>
    <div id="screenBlack" style="display:none;"></div>
    <!-- https://blog.csdn.net/pengxiang1998/article/details/105705755 -->
    <div class="addUser">
        <div class="title">
            <div class="text">添加用户</div>
            <div class="close">X</div>
        </div>
        <hr style="border: 1px solid #444;"><br/>
        <div class="addUserFrom">
            用户名：<input type="text" name="username"/><br/>
            密码：<input type="password" name="password"/><br/>
            确认密码：<input type="password" name="confirm"/><br/>
            <div style="text-align: left;">
                用户权限：
                <input type="radio" name="isAdmin" value="no"/>用户
                <input type="radio" name="isAdmin" value="yes"/>管理员
            </div><br/>
        </div>
        <br/>
        <input type="submit" onclick="addUser();"/>
        <div id="addUserText"></div>
    </div>
</body>
<script>
//获取radio值
function getRadioValue(radio){
	for (i=0; i<radio.length; i++) {
		if (radio[i].checked) {
			return radio[i].value;
		}
	}
}

var input = document.getElementById("page_input");
input.addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        var page = input.value;
        window.location.href = "?page="+page;
    }
});

// 添加用户弹窗
var close = document.getElementsByClassName("close");
var addUseBox = document.getElementsByClassName("addUser");
var screenBlack = document.getElementById("screenBlack");
var addUserText = document.getElementsByClassName("addUserText");
addUserText[0].addEventListener('click',function addUser(){
    screenBlack.style.display = "block";
    addUseBox[0].className="addUser open";
})
close[0].addEventListener('click',function(){
    screenBlack.style.display = "none";
	addUseBox[0].className="addUser";
})

/*
//msgbox测试
var btn_2=document.getElementById("btn_2");
var close_1=document.getElementsByClassName("close_1");
var dialog=document.getElementsByClassName("dialog");
btn_2.addEventListener('click',function(){
	dialog[0].style.visibility='visible';
})
close_1[0].addEventListener('click',function(){
	dialog[0].style.visibility='hidden';
})
//html代码
<!--
    <button id="btn_2"> 弹 框 提 示</button>
    <div class="dialog">
        <div class="title">恭喜：操作成功 !</div>
        <div class="btn_2">确定</div>
        <div class="close_1">取消</div>
    </div>
-->
*/

//删除用户 
function delUser(uid){
    //提示框
    if(!confirm("此操作将会删除UID为 "+uid+" 这个用户下的所有数据，确定要继续吗？")){
        return;
    }
    //提示框2
    if(!confirm("确定要删除UID为 "+uid+" 的这个用户吗？")){
        return;
    }
    //发送删除请求
    // 用FormData传输
    var fd = new FormData();
    fd.append("uid", uid);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/user.php?do=del", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("发生错误：" + e);
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        alert(e.currentTarget.responseText);
        location.reload();
    }

    xhr.send(fd);//发送请求！！！
}

//添加用户 
function addUser(){
    //获取各种数据
    var usernameFun = document.getElementsByName("username");
    var passwordFun = document.getElementsByName("password");
    var confirmFun = document.getElementsByName("confirm");
    var isAdminFun = document.getElementsByName("isAdmin");
    var addUserText = document.getElementById("addUserText");

    username = usernameFun[0].value;
    password = passwordFun[0].value;
    confirm = confirmFun[0].value;
    isAdmin = getRadioValue(isAdminFun);

    if(password!=confirm){
        alert("密码不一致！")
        return;
    }

    //发送删除请求
    // 用FormData传输
    var fd = new FormData();

    fd.append("username", username);
    fd.append("password", password);
    fd.append("confirm", confirm);
    fd.append("isAdmin", isAdmin);
    
    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/user.php?do=add", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("发生错误：" + e);
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        alert(e.currentTarget.responseText);
        location.reload();
    }

    xhr.send(fd);//发送请求！！！
}

</script>
</html>