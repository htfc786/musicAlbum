<?php /*
/admin/api/template.php
系统管理-api-模板管理
POST请求
添加模板
    ?do=add
    POST:
        
*/ ?>
<?php session_start(); // 开启Session ?>
<?php
//删除文件、文件夹函数
function delFile($path){
    //清空文件夹函数和清空文件夹后删除空文件夹函数的处理
    //try catch
    try {
        //如果是目录则继续
        if(is_dir($path)){
            //if (!substr($path ,-1)=="/"){}
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
            //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        deldir($path.$val.'/');
                        //目录清空后删除空文件夹
                        rmdir($path.$val.'/');
                    } else {
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
            rmdir($path); 
        } else {
            if(is_file($path)){
                unlink($path);
            }
        }
        return 1;
    } catch(Exception $_) {
        return 0;
    }
}

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

    case "del":
        if(!(isset($_POST["templateId"]) && $_POST["templateId"])){
            echo "请求参数错误";
            return;
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
        //读取文件
        $templateHtml = file_get_contents($htmlSaveFilePath); //读文件
        //查找格式
        //{{ musicUrl }} {{ textArray }}
        $canWriteText = 0;
        $canPlayMusic = 0;
        if(strstr($templateHtml,"{{ musicUrl }}")){
            $canPlayMusic = 1;
        }
        if(strstr($templateHtml,"{{ textArray }}")){
            $canWriteText = 1;
        }
        
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
            //echo "insert into templatesgroup (groupName) values('$templateGroupName')";
            $insertOk = mysqli_query($db,"insert into templatesgroup (groupName) values('$templateGroupName')");  
            if (!$insertOk){
                echo "提示：新模板分组创建失败\n";
                $templateGroupId = "0";
            } else {
                //模板分组id
                $newTemplateGroupId = mysqli_query($db,"select id from templatesgroup where groupName = '$templateGroupName'");
                //print_r($newTemplateGroupId);
                $newTemplateGroupId = mysqli_fetch_array($newTemplateGroupId);
                $templateGroupId = $newTemplateGroupId["id"];
            }
        }
        //$templateGroupId
        //echo "UPDATE templates SET templatIMG = '$coverSaveFileUrl', templatHtmlPath = '$htmlSaveFileUrl', templatFileMode = '$templateFileMode', templatFileUrl = '$srcSaveFileUrl', templatUpdateUserId = $userid, templatGroupId = $templateGroupId WHERE id = $templateId;";
        //插入数据库
        $UPDATEOk = mysqli_query($db,"UPDATE templates SET templatIMG = '$coverSaveFileUrl', templatHtmlPath = '$htmlSaveFileUrl', templatFileMode = '$templateFileMode', templatFileUrl = '$srcSaveFileUrl', templatUpdateUserId = '$userid', templatGroupId = '$templateGroupId', canWriteText = '$canWriteText', canPlayMusic = '$canPlayMusic' WHERE id = $templateId;");  
        if (!$UPDATEOk){
            echo "模板添加失败";
            return;
        }
        echo "模板添加成功";
        
        break;

    case "del":
        $templateId = $_POST["templateId"];
        //有这模板吗？
        $userInfo = mysqli_query($db,"SELECT * FROM templates WHERE id = $templateId");
        if(!mysqli_num_rows($userInfo)==1) {  
            echo "没这模板！";
            return;
        }

        $row = mysqli_fetch_array($userInfo);
        $templatName = $row['templatName'];
        $templatFileMode = $row['templatFileMode'];

        $templatIMGPath = "../..".$row['templatIMG'];
        $templatHtmlPath = "../..".$row['templatHtmlPath'];
        $templatFileUrl = "../../templates/src/$templateId/";

        //删除该模板下的所有数据
        //删除静态文件 上传上来的才删
        if ($templatFileMode == "updata"){
            $status = delFile($templatFileUrl);    
            if (!$status){
                echo "删除失败";
                return;
            }
        }
        //删除封面
        $status = delFile($templatIMGPath);    
        if (!$status){
            echo "删除失败";
            return;
        }
        //删除封面
        $status = delFile($templatHtmlPath);    
        if (!$status){
            echo "删除失败";
            return;
        }
        
        //删库跑路（不是）
        $delState = mysqli_query($db,"DELETE FROM templates WHERE id = $templateId");
        if (!$delState){
            //删除失败
            echo "删除失败";
            return;
        }

        //删除成功
        echo "模板：$templatName 删除成功";
        break;

    default:
        echo "没有此方式";
        return;
}

?>