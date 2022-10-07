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

$adminMusicPageNum = $confIniArray["adminMusicPageNum"];

//获取page参数
$page = 1;
if (isset($_GET["page"])&&$_GET["page"]){
    $page = $_GET["page"];
}

//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

//获取musics表有多少条数据 
$musicDataNum = mysqli_query($db,"SELECT count(*) FROM music;"); 
$musicDataNum = mysqli_fetch_assoc($musicDataNum)["count(*)"];

//多少页
//向上取整
$allPage = Ceil($musicDataNum / $adminMusicPageNum);
if ($allPage==0){ $allPage = 1; }

if (!is_numeric($page) || $allPage<$page){
    $page = $allPage;
}

//从数据库哪一行开始 需-1
$startRow = ($page - 1) * $adminMusicPageNum;

//从数据库理获取数据
$musicData = mysqli_query($db,"select * from music limit $startRow,$adminMusicPageNum;");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>音乐管理</title>
    <link rel="stylesheet" href="../src/css/admin-template.css">
</head>
<body>
    <div class="container">
        <!-- css样式代码：https://v3.bootcss.com -->
        <!--面板的情景样式-->
        <div class="panel">
            <div class="panel-heading" style="position: relative;">
                <!--面板的标题-->
                <h3 class="panel-title">音乐管理</h3>
                <div style="position: absolute;display: block;top: 0;right: 0;margin: 10px;">
                    <a href="">重新加载</a>
                    <a class="addMusicText" href="javascript:void(0);" style="padding: 8px;">添加音乐</a>
                </div>
            </div>
            <!--面板的主体-->
            <!--在面板中嵌入一个表格-->
            <?php
            if (mysqli_num_rows($musicData)==0){   //没有数据
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
                        <td>音乐id</td>
                        <td>音乐名</td>
                        <td>作曲家</td>
                        <td>音乐文件</td>
                        <td>上传用户</td>
                        <td>分类</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            END;

            $i = 0;
            while ($musicDataRow=mysqli_fetch_assoc($musicData)){
                $i++;

                $musicRowNum = $startRow+$i;
                //id
                $musicId = $musicDataRow["id"];
                //音乐名
                $musicName = $musicDataRow["musicName"];
                //作曲家
                $musicComposer = $musicDataRow["musicComposer"];
                //静态文件存储方式
                if ($musicDataRow["musicFileMode"]==0){
                    $musicFileMode = "存在本站";
                } else if ($musicDataRow["musicFileMode"]==1){
                    $musicFileMode = "存在外站";
                }
                //URL
                $musicFileUrl = $musicDataRow["musicFileUrl"];
                //上传用户
                $musicUpdateUserId = $musicDataRow["musicUpdateUserId"];
                //分类
                $musicGroupId = $musicDataRow["musicGroupId"];
                if($musicGroupId==0){
                    $musicGroup="无分组";
                }else{
                    $musicGroupQuery=mysqli_query($db,"select groupName from musicgroup where id = $musicGroupId"); 
                    $musicGroupDB = mysqli_fetch_array($musicGroupQuery);
                    $musicGroup=$musicGroupDB["groupName"];
                }
                echo <<<END
                <tr>
                    <td>$musicRowNum</td>
                    <td>$musicId</td>
                    <td>$musicName</td>
                    <td>$musicComposer</td>
                    <td>$musicFileMode<br/><a href="$musicFileUrl" download="" target="_blank">点击下载</a></td>
                    <td>UID$musicUpdateUserId</td>
                    <td>$musicGroup</td>
                    <td>
                        <a href="">编辑</a> |
                        <a href="javascript:void(0);" onclick="delMusic('$musicId')">删除</a>
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
    </div>
    <div id="screenBlack" style="display:none;"></div>
    <!-- https://blog.csdn.net/pengxiang1998/article/details/105705755 -->
    <div class="addTemplate">
        <style>
            .h1-red{
                margin: 0;
                display: inline-block;
                color: red;
                text-align: unset;
                font-size: 1.15em;
            }
        </style>
        <div class="title">
            <div class="text">音乐模板</div>
            <div class="close">X</div>
        </div>
        <hr style="border: 1px solid #444;">
        <br/>
        <div class="addTemplateFrom addMusicFrom" style="width: 380px;">
            <div id="addMusicFromName">音乐名：<h1 class="h1-red">*</h1><input type="text" name="musicName"/></div>
            <div id="addmusicFromComposer">作曲家：<input type="text" name="musicComposer"/></div>
            <div id="addMusicFromFileMode">音乐存储方式：<h1 class="h1-red">*</h1>
                <select name="musicFileMode">
                    <option value="updata">存储在本网站上 (上传文件)</option>
                    <option value="url">存储在别的网站上 (填写url)</option>
                </select></div>
            <div id="addMusicFromFile">音乐上传：<h1 class="h1-red">*</h1><input name="musicFile" type="file" multiple="" accept=".mp3,.ogg,.m4a,.wav"/></div>
            <div id="addMusicFromUrl">音乐URL：<h1 class="h1-red">*</h1><input type="text" name="musicUrl"/></div>
            <div id="addMusicFromGroup">音乐分类：<h1 class="h1-red">*</h1>
                <select name="musicGroup">
                    <option value="0">无分类</option>
                    <?php
                    $musicsgroupData = mysqli_query($db,"select id,groupName from musicgroup;");
                    while ($musicsgroupDataRow=mysqli_fetch_assoc($musicsgroupData)){
                        $musicsgroupId = $musicsgroupDataRow["id"];
                        $musicsgroupName = $musicsgroupDataRow["groupName"];
                        echo "<option value='$musicsgroupId'>$musicsgroupName</option>";
                    }
                    ?>
                    <option value="new">新建分类</option>
                </select></div>
            <div id="addMusicFromGroupName">新分类名称：<h1 class="h1-red">*</h1><input type="text" name="musicGroupName"/></div>
        </div>
        <br/>
        <input id="addMusicButton" type="submit"/>
        <div id="addMusicText"></div>
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

//添加模板选择窗口弹出
var close = document.getElementsByClassName("close");
var addMusic = document.getElementsByClassName("addTemplate");
var screenBlack = document.getElementById("screenBlack");
var addMusicText = document.getElementsByClassName("addMusicText");
addMusicText[0].addEventListener('click',function (){
    screenBlack.style.display = "block";
    addMusic[0].className="addTemplate open";
})
close[0].addEventListener('click',function(){
    screenBlack.style.display = "none";
	addMusic[0].className="addTemplate";
})

//设置模式的监听
document.getElementsByName("musicFileMode")[0].addEventListener("change", function(e) {
    if (e.target.tagName == "SELECT") {
        //console.log("inside", e.target.value)
        if(e.target.value=="updata"){
            document.getElementsByName("musicUrl")[0].value="";
            document.getElementById("addMusicFromFile").style.display = "block";
            document.getElementById("addMusicFromUrl").style.display = "none";
        } else if(e.target.value=="url"){
            document.getElementsByName("musicFile")[0].value="";
            document.getElementById("addMusicFromFile").style.display = "none";
            document.getElementById("addMusicFromUrl").style.display = "block";
        } else if(e.target.value=="none"){
            document.getElementsByName("musicFile")[0].value="";
            document.getElementsByName("musicUrl")[0].value="";
            document.getElementById("addMusicFromFile").style.display = "none";
            document.getElementById("addMusicFromUrl").style.display = "none";
        } 
    }
})
document.getElementById("addMusicFromFile").style.display = "block";
document.getElementById("addMusicFromUrl").style.display = "none";
//分组
document.getElementsByName("musicGroup")[0].addEventListener("change", function(e) {
    if (e.target.tagName == "SELECT") {
        //console.log("inside", e.target.value)
        if(e.target.value=="new"){
            document.getElementById("addMusicFromGroupName").style.display = "block";
        }else{
            document.getElementsByName("musicGroupName")[0].value="";
            document.getElementById("addMusicFromGroupName").style.display = "none";
        }
    }
})
document.getElementById("addMusicFromGroupName").style.display = "none";

document.getElementById("addMusicButton").addEventListener("click",function (){
    //获取各种元素的obj
    musicNameObj = document.getElementsByName("musicName")[0]
    musicComposerObj = document.getElementsByName("musicComposer")[0]
    musicFileModeObj = document.getElementsByName("musicFileMode")[0]
    musicFileObj = document.getElementsByName("musicFile")[0]
    musicUrlObj = document.getElementsByName("musicUrl")[0]
    musicGroupObj = document.getElementsByName("musicGroup")[0]
    musicGroupNameObj = document.getElementsByName("musicGroupName")[0]

    addMusicText = document.getElementById("addMusicText")//提示语

    // 用FormData传输
    var fd = new FormData();
    //音乐名
    musicName = musicNameObj.value;
    if(!musicName){
        addMusicText.innerText = "请输入音乐名";
        alert("请输入音乐名");
        return;
    }
    fd.append("musicName", musicName);

    //作曲家
    musicComposer = musicComposerObj.value;
    fd.append("musicComposer", musicComposer);

    //模板静态文件上传模式、文件、url
    if (!musicFileModeObj.value) {
        addTemplateText.innerText = "请选择文件上传模式";
        alert("请选择文件上传模式");
        return;
    }
    fd.append("musicFileMode", musicFileModeObj.value);
    if (musicFileModeObj.value == "updata"){
        if (!musicFileObj.files[0]) {
            addTemplateText.innerText = "请选择文件";
            alert("请选择文件");
            return;
        }
        fd.append("musicFile", musicFileObj.files[0]);
    }else if(musicFileModeObj.value == "url"){
        if (!musicUrlObj.value) {
            musicUrlObj.innerText = "请输入文件url";
            alert("请输入文件url");
            return;
        }
        fd.append("musicUrl", musicUrlObj.value);
    }

    //分组 、新分组
    if (!musicGroupObj.value) {
        addTemplateText.innerText = "请选择分组";
        alert("请选择分组");
        return;
    }
    fd.append("musicGroup", musicGroupObj.value);
    if (musicGroupObj.value == "new") {
        if (!musicGroupNameObj.value) {
            addTemplateText.innerText = "请输入新分组";
            alert("请输入新模板分组");
            return;
        }
        fd.append("musicGroupNew", musicGroupNameObj.value);
    }
    //发送请求
    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/music.php?do=add", true);

    //发生错误
    xhr.onerror = function (e) {
        alert("发生错误：" + e);
    }
    //进度
    xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            // 文件上传进度
            // 获取百分制的进度
            let filePercent = Math.round(e.loaded / e.total * 100);
            // 长度根据进度条的总长度等比例扩大
            //probg.style.width = progress.clientWidth / 100 * percent + "px";
            // 进度数值按百分制来
            addMusicText.innerText = "上传进度：" + filePercent + "%";
        }
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        addMusicText.innerText = e.currentTarget.responseText;
        if(e.currentTarget.responseText=="音乐添加成功"){
            alert(e.currentTarget.responseText);
            location.reload();
        } else {
            alert(e.currentTarget.responseText);
        }
        
    }

    xhr.send(fd);//发送请求！！！
});

function delMusic(musicId){
    //console.log(musicId)
    //提示框
    if(!confirm("此操作将会删除ID为 "+musicId+" 这个音乐，确定要继续吗？")){
        return;
    }
    //提示框2
    if(!confirm("确定要删除ID为 "+musicId+" 这个音乐吗？")){
        return;
    }
    //发送删除请求
    // 用FormData传输
    var fd = new FormData();
    fd.append("musicId", musicId);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/music.php?do=del", true);
    
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