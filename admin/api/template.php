<?php /*
/admin/api/template.php
系统管理-api-模板管理
POST请求
*/ ?>
<?php session_start(); // 开启Session ?>
<?php
if (!(isset($_SESSION['islogin']) && isset($_SESSION['isadmin']) && $_SESSION['isadmin'])) {
    // 没有登录
    //header('refresh:0; url=./login.php');
    //$codeNoLogin = $conf["codeNoLogin"];
    echo "请先登录";
    return;
}
// 已经登录
$userid = $_SESSION['userid'];  //管理员id

//判断请求方式
if (!($_SERVER['REQUEST_METHOD'] === 'POST')){
    echo "请求方式错误";
    return;
}

//请求参数 此处只判断do
if(!(isset($_GET["do"]) && $_GET["do"])){
    echo "请求参数错误";
    return;
}

//此处按需判断其他参数(本来是想)
//由于参数比较复杂，所以到地下的时候在判断
switch ($_GET["do"])
{
    case "add":
        //模板名称 模板静态文件存储方式 模板分类 的判断
        if((!(isset($_POST["templateName"])&&
            isset($_POST["templateFileMode"])&&
            isset($_POST["templateGroup"])))||
            ($_POST["templateName"] == "" || 
            $_POST["templateFileMode"] == "" || 
            $_POST["templateGroup"] == "")) {  
                echo '请确认信息完整性！';  
                return;
        }
        
        if(!($_POST["templateFileMode"] == "updata" ||
             $_POST["templateFileMode"] == "url" ||
             $_POST["templateFileMode"] == "none")){
                echo '信息有误！';  
                return;
        }
        //封面文件判断
        if ((($_FILES["templateIMG"]["type"] == "image/gif")
            || ($_FILES["templateIMG"]["type"] == "image/jpeg")
            || ($_FILES["templateIMG"]["type"] == "image/jpg")
            || ($_FILES["templateIMG"]["type"] == "image/pjpeg")
            || ($_FILES["templateIMG"]["type"] == "image/x-png")
            || ($_FILES["templateIMG"]["type"] == "image/png")
            || ($_FILES["templateIMG"]["type"] == "image/bmp")
            || ($_FILES["templateIMG"]["type"] == "image/webp"))
            && ($_FILES["templateIMG"]["size"] < 204800000)){    // 小于 200000 kb
            if ($_FILES["templateIMG"]["error"] > 0){
                echo "模板封面：上传错误代码:" . $_FILES["templateIMG"]["error"];
                return;
            }
        } else {
            echo "模板封面：上传的文件是非法的文件格式";
            return;
        }
        //模板html判断
        if ((($_FILES["templateHtml"]["type"] == "text/html"))
            && ($_FILES["templateHtml"]["size"] < 204800000)){    // 小于 200000 kb
            if ($_FILES["templateHtml"]["error"] > 0){
                echo "模板html：上传错误代码:" . $_FILES["templateIMG"]["error"];
                return;
            }
        } else {
            echo "模板html：上传的文件是非法的文件格式";
            return;
        }
        //模板静态文件判断
        if($_POST["templateFileMode"] == "updata"){
            if (($_FILES["templateFile"]["size"] < 2048000000)){    // 小于 2000000 kb
            if ($_FILES["templateFile"]["error"] > 0){
                    echo "模板静态文件：上传错误代码:" . $_FILES["templateFile"]["error"];
                    return;
                }
            } else {
                echo "模板静态文件：上传的文件是非法的文件格式";
                return;
            }
        }
        //模板静态url判断
        if($_POST["templateFileMode"] == "url"){
            if((!isset($_POST["templateUrl"]))||
                ($_POST["templateUrl"] == "")) {  
                    echo '请确认信息完整性！';  
                    return;
            }
        }
        //新模板判断
        if($_POST["templateGroup"] == "new"){
            if((!isset($_POST["templateGroupName"]))||
                ($_POST["templateGroupName"] == "")) {  
                    echo '请确认信息完整性！';  
                    return;
            }
        }
        break;

    default:
        echo "没有此方式";
        return;
}


//连接数据库
//读取配置
$confIniArray = parse_ini_file("../../conf.ini", true);
//数据库配置
$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];
$dbEncoding = $confIniArray["dbEncoding"];
//连接!
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);
//mysql_select_db("my_test");  //选择数据库  
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集 

switch ($_GET["do"])
{
    case "add":
        
        $templateName = $_POST["templateName"];

        //是否已经有了
        $result = mysqli_query($db,"select id from templates where templatName = '$templateName'");
        if(mysqli_num_rows($result)) {  
            echo '已经存在该模板！';  
            return;
        }

        //模板名称
        //$_POST["templateName"]
        //先执行插入语句获得模板的id
        $res_insert = mysqli_query($db,"insert into templates (templatName,templatUpdateUserId) values('$templateName',$userid)");  
        if(!$res_insert) { //如果插入失败执行 ！！！注意：这里有可能是sql出错
            echo '系统繁忙，请稍候！';
            return;
        }
        
        //获得模板id
        $templateId = mysqli_query($db,"select id from templates where templatName = '$templateName' and templatUpdateUserId = $userid");  
        $templateId = mysqli_fetch_array($templateId)["id"];
        
        //封面文件
        //---没有存数据库
        //$_FILES["templateIMG"]
        //检查“../../templates”是否存在
        if(!(is_dir("../../templates")||file_exists("../../templates"))){
            mkdir("../../templates",0777,true); //创建
        }
        //检查cover文件夹
        if(!(is_dir("../../templates/cover")||file_exists("../../templates/cover"))){
            mkdir("../../templates/cover",0777,true);   //创建
        }
        //保存文件
        $coverFileFormat = pathinfo($_FILES["templateIMG"]["name"], PATHINFO_EXTENSION); //文件扩展名
        $coverSaveFileUrl = "/templates/cover/$templateId.$coverFileFormat";
        $coverSaveFilePath = "../.." . $coverSaveFileUrl;
        move_uploaded_file($_FILES["templateIMG"]["tmp_name"], $coverSaveFilePath);  //复制
        // $coverSaveFileUrl

        //模板html
        //---没有存数据库
        //$_FILES["templateHtml"]
        //检查html文件夹
        if(!(is_dir("../../templates/html")||file_exists("../../templates/html"))){
            mkdir("../../templates/html",0777,true);   //创建
        }
        //保存文件
        $htmlFileFormat = pathinfo($_FILES["templateHtml"]["name"], PATHINFO_EXTENSION); //文件扩展名
        $htmlSaveFileUrl = "/templates/html/$templateId.$htmlFileFormat";
        $htmlSaveFilePath = "../.." . $htmlSaveFileUrl;
        move_uploaded_file($_FILES["templateHtml"]["tmp_name"], $htmlSaveFilePath);  //复制
        // $htmlSaveFileUrl

        //文件保存模式
        //$_POST["templateFileMode"] updata url none
        $templateFileMode = $_POST["templateFileMode"];
        // $templateFileMode
        
        //文件保存模式 - zip文件 updata
        if ($templateFileMode == "updata"){
            // - $_FILES["templateFile"] updata
            $srcSaveFileUrl = "/templates/src/$templateId/";
            $srcSaveFilePath = "../.." . $srcSaveFileUrl;
            $zip = new ZipArchive();    //zip对象
            if ($zip->open($_FILES["templateFile"]["tmp_name"]) === true) { //打开文件
                $zip->extractTo($srcSaveFilePath);
                $zip->close();
            } else {
                echo "zip文件打开失败";
                return;
            }
        }
        //文件保存模式 - 外部链接 url
        if ($templateFileMode == "url"){
            // - $_POST["templateUrl"] url
            $srcSaveFileUrl = $_POST["templateUrl"];
        }
        // $srcSaveFileUrl

        //模板分组
        //$_POST["templateGroup"] 发送过来的是模板id 如果是none就存0
        $templateGroupId = $_POST["templateGroup"];
        if ($templateGroupId=="none"){
            $templateGroupId = "0";
        }
        //新模板分组
        if ($templateGroupId == "new"){
            // - $_POST["templateGroupName"] new
            $templateGroupName = $_POST["templateGroupName"];
            //插入模板分组
            $insertOk = mysqli_query($db,"insert into templatesgroup (groupName) values('$templateGroupName')");  
            if (!$insertOk){
                echo "提示：新模板分组创建失败\n";
                $templateGroupId = "0";
            }
            //模板分组id
            $newTemplateGroupId = mysqli_query($db,"select id from templatesgroup where groupName = '$templateGroupName'");
            $newTemplateGroupId = mysqli_fetch_array($newTemplateGroupId);
            $templateGroupId = $newTemplateGroupId["id"];

        }
        //$templateGroupId
        //echo "UPDATE templates SET templatIMG = '$coverSaveFileUrl', templatHtmlPath = '$htmlSaveFileUrl', templatFileMode = '$templateFileMode', templatFileUrl = '$srcSaveFileUrl', templatUpdateUserId = $userid, templatGroupId = $templateGroupId WHERE id = $templateId;";
        //插入数据库
        $UPDATEOk = mysqli_query($db,"UPDATE templates SET templatIMG = '$coverSaveFileUrl', templatHtmlPath = '$htmlSaveFileUrl', templatFileMode = '$templateFileMode', templatFileUrl = '$srcSaveFileUrl', templatUpdateUserId = '$userid', templatGroupId = '$templateGroupId' WHERE id = $templateId;");  
        if (!$UPDATEOk){
            echo "模板添加失败";
            return;
        }
        echo "模板添加成功";
        

        break;

    default:
        echo "没有此方式";
        return;
}

?>