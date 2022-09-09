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

$adminTemplatesPageNum = $confIniArray["adminTemplatesPageNum"];

//获取page参数
$page = 1;
if (isset($_GET["page"])&&$_GET["page"]){
    $page = $_GET["page"];
}

//连接数据库
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

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
    <link rel="stylesheet" href="../src/css/admin-template.css">
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
                    <a class="addTemplateText" href="javascript:void(0);" style="padding: 8px;">添加模板</a>
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
                        <td>模板id</td>
                        <td>模板名称</td>
                        <td>模板封面</td>
                        <td>html</td>
                        <td>静态文件存储方式</td>
                        <td>上传用户</td>
                        <td>分类</td>
                        <td>是否支持音乐播放</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
            END;

            $i = 0;
            while ($userDataRow=mysqli_fetch_assoc($templatesData)){
                $i++;

                $templatesRowNum = $startRow+$i;
                //id
                $templatesId = $userDataRow["id"];
                //模板名称
                $templatName = $userDataRow["templatName"];
                //模板封面url
                $templatIMGUrl = $userDataRow["templatIMG"];
                //html
                $templatHtmlPath = $userDataRow["templatHtmlPath"];
                //静态文件存储方式和url
                if ($userDataRow["templatFileMode"]=="updata"){
                    $templatFileUrl = $userDataRow["templatFileUrl"];
                    $templatFileModeHtml = "存储在本站<br/>(<a href=''>点击下载</a>)";
                } else if ($userDataRow["templatFileMode"]=="url"){
                    $templatFileUrl = $userDataRow["templatFileUrl"];
                    $templatFileModeHtml = "存储在别的网站<br/>($templatFileUrl)";
                } else if ($userDataRow["templatFileMode"]=="none"){
                    $templatFileModeHtml = "无";
                }
                //上传用户
                $templatUpdateUserId = $userDataRow["templatUpdateUserId"];
                //分类
                $templatGroupId = $userDataRow["templatGroupId"];
                if($templatGroupId==0){
                    $templatGroup="无分组";
                }else{
                    $templatGroupDB=mysqli_query($db,"select groupName from templatesgroup where id = $templatGroupId"); 
                    $templatGroupDB = mysqli_fetch_array($templatGroupDB);
                    $templatGroup=$templatGroupDB["groupName"];
                }
                
                echo <<<END
                <tr>
                    <td>$templatesRowNum</td>
                    <td>$templatesId</td>
                    <td>$templatName</td>
                    <td><img class="templateIMG" src="$templatIMGUrl"></img></td>
                    <td>$templatHtmlPath</td>
                    <td>$templatFileModeHtml</td>
                    <td>UID$templatUpdateUserId</td>
                    <td>$templatGroup</td>
                    <td style="font-size: x-large;">√</td>
                    <td>
                        <a href="">编辑</a> |
                        <a href="javascript:void(0);" onclick="delTemplate('$templatesId')">删除</a>
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
        <div class="title">
            <div class="text">添加模板</div>
            <div class="close">X</div>
        </div>
        <hr style="border: 1px solid #444;">
        <br/>
        <div class="addTemplateFrom" style="width: 380px;">
            <div id="addTemplateFromTemplateName">模板名称：<input type="text" name="templateName"/></div>
            <div id="addTemplateFromTemplateIMG">模板封面：<input type="file" name="templateIMG"/></div>
            <div id="addTemplateFromTemplateHtml">模板html文件：<input type="file" name="templateHtml"/></div>
            <div id="addTemplateFromTemplateFileMode">模板静态文件存储方式：
                <select name="templateFileMode">
                    <option value="updata">存储在本网站上 (上传zip文件)</option>
                    <option value="url">存储在别的网站上 (填写url)</option>
                    <option value="none">无静态文件</option>
                </select></div>
            <div id="addTemplateFromTemplateFile">模板静态文件上传：<input type="file" name="templateFile"/></div>
            <div id="addTemplateFromTemplateUrl">模板静态文件url：<input type="text" name="templateUrl"/></div>
            <div id="addTemplateFromTemplateGroup">模板分类：
                <select name="templateGroup">
                    <option value="0">无分类</option>
                    <?php
                    $templatesgroupData = mysqli_query($db,"select id,groupName from templatesgroup;");
                    while ($templatesgroupDataRow=mysqli_fetch_assoc($templatesgroupData)){
                        $templatesgroupId = $templatesgroupDataRow["id"];
                        $templatesgroupName = $templatesgroupDataRow["groupName"];
                        echo "<option value='$templatesgroupId'>$templatesgroupName</option>";
                    }
                    ?>
                    <option value="new">新建分类</option>
                </select></div>
            <div id="addTemplateFromTemplateGroupName">新分类名称：<input type="text" name="templateGroupName"/></div>
        </div>
        <br/>
        <input id="addTemplateButton" type="submit"/>
        <div id="addTemplateText"></div>
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
var addTemplate = document.getElementsByClassName("addTemplate");
var screenBlack = document.getElementById("screenBlack");
var addTemplateText = document.getElementsByClassName("addTemplateText");
addTemplateText[0].addEventListener('click',function (){
    screenBlack.style.display = "block";
    addTemplate[0].className="addTemplate open";
})
close[0].addEventListener('click',function(){
    screenBlack.style.display = "none";
	addTemplate[0].className="addTemplate";
})

//设置模板文件上传模式的监听
document.getElementsByName("templateFileMode")[0].addEventListener("change", function(e) {
    if (e.target.tagName == "SELECT") {
        //console.log("inside", e.target.value)
        if(e.target.value=="updata"){
            document.getElementsByName("templateUrl")[0].value="";
            document.getElementById("addTemplateFromTemplateFile").style.display = "block";
            document.getElementById("addTemplateFromTemplateUrl").style.display = "none";
        } else if(e.target.value=="url"){
            document.getElementsByName("templateFile")[0].value="";
            document.getElementById("addTemplateFromTemplateFile").style.display = "none";
            document.getElementById("addTemplateFromTemplateUrl").style.display = "block";
        } else if(e.target.value=="none"){
            document.getElementsByName("templateFile")[0].value="";
            document.getElementsByName("templateUrl")[0].value="";
            document.getElementById("addTemplateFromTemplateFile").style.display = "none";
            document.getElementById("addTemplateFromTemplateUrl").style.display = "none";
        } 
    }
})
document.getElementById("addTemplateFromTemplateFile").style.display = "block";
document.getElementById("addTemplateFromTemplateUrl").style.display = "none";

//设置
document.getElementsByName("templateGroup")[0].addEventListener("change", function(e) {
    if (e.target.tagName == "SELECT") {
        //console.log("inside", e.target.value)
        if(e.target.value=="new"){
            document.getElementById("addTemplateFromTemplateGroupName").style.display = "block";
        }else{
            document.getElementsByName("templateGroupName")[0].value="";
            document.getElementById("addTemplateFromTemplateGroupName").style.display = "none";
        }
    }
})
document.getElementById("addTemplateFromTemplateGroupName").style.display = "none";

document.getElementById("addTemplateButton").addEventListener("click",function (){

    //获取各种元素的obj
    templateNameObj = document.getElementsByName("templateName")[0]
    templateIMGObj = document.getElementsByName("templateIMG")[0]
    templateHtmlObj = document.getElementsByName("templateHtml")[0]
    templateFileModeObj = document.getElementsByName("templateFileMode")[0]
    templateFileObj = document.getElementsByName("templateFile")[0]
    templateUrlObj = document.getElementsByName("templateUrl")[0]
    templateGroupObj = document.getElementsByName("templateGroup")[0]
    templateGroupNameObj = document.getElementsByName("templateGroupName")[0]

    addTemplateText = document.getElementById("addTemplateText")//提示语
    
    // 用FormData传输
    var fd = new FormData();
    /*
    templateGroup = templateGroupObj
    templateGroupName = templateGroupNameObj
    */
    //模板名称
    templateName = templateNameObj.value;
    if(!templateName){
        addTemplateText.innerText = "请输入模板名称";
        alert("请输入模板名称");
        return;
    }
    fd.append("templateName", templateName);

    //模板封面
    if (!templateIMGObj.files[0]) {
        addTemplateText.innerText = "请选择模板封面文件";
        alert("请选择模板封面文件！");
        return;
    }
    fd.append("templateIMG", templateIMGObj.files[0]);

    //模板html
    if (!templateHtmlObj.files[0]) {
        addTemplateText.innerText = "请选择模板html文件";
        alert("请选择模板html文件！");
        return;
    }
    fd.append("templateHtml", templateHtmlObj.files[0]);

    //模板静态文件上传模式、文件、url
    if (!templateFileModeObj.value) {
        addTemplateText.innerText = "请选择模板静态文件上传模式";
        alert("请选择模板静态文件上传模式");
        return;
    }
    fd.append("templateFileMode", templateFileModeObj.value);
    if (templateFileModeObj.value == "updata"){
        if (!templateFileObj.files[0]) {
            addTemplateText.innerText = "请选择模板静态zip文件";
            alert("请选择模板静态zip文件");
            return;
        }
        fd.append("templateFile", templateFileObj.files[0]);
    }else if(templateFileModeObj.value == "url"){
        if (!templateUrlObj.value) {
            addTemplateText.innerText = "请输入静态文件url";
            alert("请输入静态文件url");
            return;
        }
        fd.append("templateUrl", templateUrlObj.value);
    }

    //分组 、新分组
    if (!templateGroupObj.value) {
        addTemplateText.innerText = "请选择模板分组";
        alert("请选择模板分组");
        return;
    }
    fd.append("templateGroup", templateGroupObj.value);
    if (templateGroupObj.value == "new") {
        if (!templateGroupNameObj.value) {
            addTemplateText.innerText = "请输入新模板分组";
            alert("请输入新模板分组");
            return;
        }
        fd.append("templateGroupName", templateGroupNameObj.value);
    }
    //发送请求
    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/template.php?do=add", true);
    
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
            addTemplateText.innerText = "上传进度：" + filePercent + "%";
        }
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        addTemplateText.innerText = e.currentTarget.responseText;
        alert(e.currentTarget.responseText);
        if(e.currentTarget.responseText=="模板添加成功"){
            location.reload();
        }
        
    }

    xhr.send(fd);//发送请求！！！
});

function delTemplate(templateId){
    //console.log(templateId)
    //提示框
    if(!confirm("此操作将会删除ID为 "+templateId+" 这个模板，确定要继续吗？")){
        return;
    }
    //提示框2
    if(!confirm("确定要删除ID为 "+templateId+" 的这个模板吗？")){
        return;
    }
    //发送删除请求
    // 用FormData传输
    var fd = new FormData();
    fd.append("templateId", templateId);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "./api/template.php?do=del", true);
    
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
