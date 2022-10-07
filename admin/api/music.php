<?php session_start(); // 开启Session ?>
<?php 

function getFileName($musicUrl){	//获取请求php文件后面写的东西
    $meet_php = 0;//False;//是否遇见这个php脚本名称
    $urls = explode("/",$musicUrl);//用“/”分割字符串
    foreach ($urls as $i){
        //echo $i."/";
        if ($meet_php) {	//遇见这个php脚本名称之后添加今路径变量里
            return $i;
        }
        if ($i=="music"){//如果遇见这个php脚本名称标记一下
            $meet_php = 1;//True;
        }
    }
    return "";
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
switch ($_GET["do"]){
    case "add":
        //模板名称 模板静态文件存储方式 模板分类 的判断
        if((!(isset($_POST["musicName"])&&
            isset($_POST["musicComposer"])&&
            isset($_POST["musicFileMode"])&&
            isset($_POST["musicGroup"])))||
            ($_POST["musicName"] == "" || 
            $_POST["musicFileMode"] == "" || 
            $_POST["musicGroup"] == "")) {  
                echo '请确认信息完整性！';  
                return;
        }
        
        if(!($_POST["musicFileMode"] == "updata" ||
             $_POST["musicFileMode"] == "url")){
                echo '信息有误！';  
                return;
        }
        //模板静态文件判断
        if($_POST["musicFileMode"] == "updata"){
            if ((($_FILES["musicFile"]["type"] == "audio/mpeg")
                || ($_FILES["musicFile"]["type"] == "audio/x-wav")
                || ($_FILES["musicFile"]["type"] == "audio/x-m4a")
                || ($_FILES["musicFile"]["type"] == "audio/ogg"))
                && ($_FILES["musicFile"]["size"] < 204800000)){    // 小于 2000000 kb
                if ($_FILES["musicFile"]["error"] > 0){
                    echo "模板静态文件：上传错误代码:" . $_FILES["musicFile"]["error"];
                    return;
                }
            } else {
                echo "模板静态文件：上传的文件是非法的文件格式";
                return;
            }
        }
        //模板静态url判断
        if($_POST["musicFileMode"] == "url"){
            if((!isset($_POST["musicUrl"]))||
                ($_POST["musicUrl"] == "")) {  
                    echo '请确认信息完整性！';  
                    return;
            }
        }
        //新模板判断
        if($_POST["musicGroup"] == "new"){
            echo $_POST["musicGroupNew"];
            if ((!isset($_POST["musicGroupNew"])) ||
                ($_POST["musicGroupNew"] == "")) {  
                    echo '请确认信息完整性！3';  
                    return;
            }
        }

        break;

    case "del":
        if(!(isset($_POST["musicId"]) && $_POST["musicId"])){
            echo "缺少请求参数";
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

$musicSavePaths = $confIniArray["musicSavePaths"];

$fileSavePaths = $confIniArray["musicSavePaths"]; 
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

/* 
$_POST["musicName"]
$_POST["musicComposer"]
$_POST["musicFileMode"]
  - $_FILES["musicFile"] update
  - $_POST["musicUrl"] url
$_POST["musicGroup"]
  - $_POST["musicGroupNew"] new
*/

switch ($_GET["do"]){
    case "add":
        //音乐名
        //$_POST["musicName"]
        $musicName = $_POST["musicName"];
        //是否已经有了
        /* $result = mysqli_query($db,"select id from templates where templatName = '$templateName'");
        if(mysqli_num_rows($result)) {  
            echo '已经存在该模板！';  
            return;
        } */
        // <--- $musicName

        //作曲家
        //$_POST["musicComposer"]
        $musicComposer = $_POST["musicComposer"];
        // <--- $musicComposer

        //文件保存模式和文件URL
        //$_POST["musicFileMode"] updata url
        $musicFileModeText = $_POST["musicFileMode"];
        if ($musicFileModeText == "updata") {
            $musicFileMode = 0;
            //文件保存模式 - 上传 updata
            // $_FILES["musicFile"] update
            //如果文件夹不存在新建文件夹
            if(!(is_dir($musicSavePaths."/".date("Y"))||file_exists($musicSavePaths."/".date("Y")))){
                mkdir($musicSavePaths."/".date("Y"),0777,true);
            }
            if(!(is_dir($musicSavePaths."/".date("Y")."/".date("m"))||file_exists($musicSavePaths."/".date("Y")."/".date("m")))){
                mkdir($musicSavePaths."/"."/".date("Y")."/".date("m"),0777,true);
            }
            if(!(is_dir($musicSavePaths."/".date("Y")."/".date("m")."/".date("d"))||file_exists($musicSavePaths."/".date("Y")."/".date("m")."/".date("d")))){
                mkdir($musicSavePaths."/".date("Y")."/".date("m")."/".date("d"),0777,true);
            }
            //路径
            $musicFilePaths = $musicSavePaths."/".date("Y")."/".date("m")."/".date("d")."/";
            //文件名
            $originalName = $_FILES["musicFile"]["name"];//原文件名
            $savename = date("YmdHis").rand(0,99999999).".".pathinfo($_FILES["musicFile"]["name"], PATHINFO_EXTENSION);//存储的文件名
            if (file_exists($musicFilePaths . $savename)){
                //是否重名=》重名改名
                while (!file_exists($musicFilePaths . $savename)){
                    //循环到没有重名
                    $savename = date("YmdHis").rand(0,99999999).".".pathinfo($files[$i]["name"], PATHINFO_EXTENSION);
                }
            } 
            //复制文件
            move_uploaded_file($_FILES["musicFile"]["tmp_name"], $musicFilePaths . $savename);

            $musicFileUrl = "/download.php/music/".$savename;

        } else if ($musicFileModeText == "url") {
            $musicFileMode = 1;
            //文件保存模式 - 外部链接 url
            // $_POST["musicUrl"] url
            $musicFileUrl = $_POST["musicUrl"];
        }
        // <--- $musicFileMode
        // <--- $musicFileUrl

        //模板分组
        //$_POST["templateGroup"] 发送过来的是id 如果是none就存0
        $musicGroupId = $_POST["musicGroup"];
        if ($musicGroupId=="none"){
            $musicGroupId = "0";
        } else if ($musicGroupId == "new") {
            // $_POST["musicGroupNew"] new
            $musicGroupNew = $_POST["musicGroupNew"];
            //插入模板分组
            $insertOk = mysqli_query($db,"INSERT INTO musicgroup (groupName) VALUES('$musicGroupNew')");  
            if (!$insertOk){
                echo "提示：新模板分组创建失败\n";
                $musicGroupId = "0";
            } else {
                //模板分组id
                $newMusicGroupIdQuery = mysqli_query($db,"SELECT id FROM musicgroup WHERE groupName = '$musicGroupNew'");
                $newMusicGroupData = mysqli_fetch_array($newMusicGroupIdQuery);
                $musicGroupId = $newMusicGroupData["id"];
            }
        }
        // <--- $musicGroupId

        //插入数据库
        $insertOk = mysqli_query($db,"INSERT INTO music (musicName,musicComposer,musicFileMode,musicFileUrl,musicUpdateUserId,musicGroupId) VALUES('$musicName','$musicComposer',$musicFileMode,'$musicFileUrl',$userid,$musicGroupId)");  
        if (!$insertOk){
            echo "音乐添加失败";
            $musicGroupId = "0";
        }
        echo "音乐添加成功";

        break;

    case "del":
        $musicId = $_POST["musicId"];
        //有毛有
        $musicQuery = mysqli_query($db,"SELECT musicFileMode,musicFileUrl FROM music WHERE id = $musicId");
        if(!mysqli_num_rows($musicQuery)==1) {  
            echo "没这图！";
            return;
        }
        $musicRow = mysqli_fetch_array($musicQuery);
        $musicFileMode = $musicRow["musicFileMode"];
        $musicUrl = $musicRow["musicFileUrl"];

        //存在本站
        if ($musicFileMode == 0){
            $fileName = getFileName($musicUrl);

            $fileDate = substr($fileName, 0, 8);
            $fileYear = substr($fileName, 0, 4);
            $fileMon = substr($fileName, 4, 2);
            $fileDay = substr($fileName, 6, 2);

            $filePath = "/".$fileYear."/".$fileMon."/".$fileDay."/".$fileName;
            
            $status = @unlink($fileSavePaths . $filePath);    
            if (!$status){    
                echo "文件删除失败";
                return;
            }
        }
        //删除数据库
        $status = mysqli_query($db," DELETE FROM music WHERE id=$musicId;");
        if (!$status){    
            echo "删除失败";
            return;
        }
        echo "删除成功";

        break;

    default:
        echo "没有此方式";
        return;
}
?>