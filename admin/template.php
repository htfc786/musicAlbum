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

$adminTemplatesPageNum = $confIniArray["adminTemplatesPageNum"];

//获取page参数
$page = 1;
if (isset($_GET["page"])&&$_GET["page"]){
    $page = $_GET["page"];
}

//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names 'utf-8'"); //设定字符集 

//获取templates表有多少条数据 
$templatesDataNum = mysqli_query($db,"select count(*) from templates;"); 
$templatesDataNum = mysqli_fetch_assoc($templatesDataNum)["count(*)"];

//多少页
//向上取整
$allPage = Ceil($templatesDataNum / $adminTemplatesPageNum);
if ($allPage==0) $allPage = 1;

if (!is_numeric($page) || $allPage<$page){
    $page = $allPage;
}

//从数据库哪一行开始 需-1
$startRow = ($page - 1) * $adminTemplatesPageNum;

//从数据库理获取数据
$templatesData = mysqli_query($db,"select * from templates limit $startRow,$adminTemplatesPageNum;");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>模板管理</title>
    <link rel="stylesheet" href="../src/css/admin-user.css">
</head>
<body>
    <div class="container">
        <!--面板的情景样式-->
        <div class="panel">
            <div class="panel-heading" style="position: relative;">
                <!--面板的标题-->
                <h3 class="panel-title">模板管理</h3>
                <div style="position: absolute;display: block;top: 0;right: 0;margin: 10px;">
                    <a href="">重新加载</a>
                    <a class="addUserText" href="javascript:void(0);" style="padding: 8px;">添加模板</a>
                </div>
            </div>
            <!--面板的主体-->
            <!--在面板中嵌入一个表格-->
            <?php
            if (mysqli_num_rows($templatesData)==0){   //没有数据
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
                        
                    </tr>
                </thead>
                <tbody>
            END;

            $i = 0;
            while ($userDataRow=mysqli_fetch_assoc($templatesData)){
                $i++;
                        
                $templatesRowNum = $startRow+$i;
                echo <<<END
                <tr>
                    <td>$templatesRowNum</td>
                    <td>
                        <a href="">编辑</a> |
                        <a href="javascript:void(0);" onclick="del()">删除</a>
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
        <!-- <div id="screenBlack" style="display:none;"></div>
        <!-- https://blog.csdn.net/pengxiang1998/article/details/105705755 
        <div class="addUser">
            <div class="title">
                <div class="text">添加用户</div>
                <div class="close">X</div>
            </div>
            <hr style="border: 1px solid #444;">
            <br/>
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
            <input type="submit" onclick="addTemplate();"/>
            <div id="addUserText"></div>
        </div>
        -->
    </div>
</body>
<script>
//页码输入
var input = document.getElementById("page_input");
input.addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        var page = input.value;
        window.location.href = "?page="+page;
    }
});
</script>
</html>
