<?php /*
/api/albummake.php
api-相册制作


*/ ?>
<?php session_start(); // 开启Session ?>
<?php
//函数
function listFiles(){   //整理$_FILES的信息，方便便利数组
    /* 返回格式：文件信息 
    文件名:$files[$i]["name"] 
    文件类型:$files[$i]["type"] 
    文件大小:$files[$i]["size"] 
    临时文件：$files[$i]["tmp_name"]
    */
    $files = [];
    foreach ($_FILES as $file) {
        if (is_string($file['name'])) {
            //如果有一个直接赋值
            $files[0] = $file;
        } elseif (is_array($file['name'])) {
            //整理
            $i = 0;
            foreach ($file['name'] as $k => $v) {
                $files[$i]['name'] = $file['name'][$k];
                $files[$i]['type'] = $file['type'][$k];
                $files[$i]['tmp_name'] = $file['tmp_name'][$k];
                $files[$i]['error'] = $file['error'][$k];
                $files[$i]['size'] = $file['size'][$k];
                $i++;
            }
        }
    }
    return $files;
}
//生成长度固定的空数组 https://blog.junphp.com/details/143.jsp?page=4
function arrayLen($length){
    $array = [];
    for ($i=1; $i<=$length; $i++){
        $array[] = ""; 
    } 
    return $array; 
} 

if (!(isset($_SESSION['islogin']))) {
    // 没有登录
    //header('refresh:0; url=./login.php');
    //$codeNoLogin = $conf["codeNoLogin"];
    echo "请先登录";
    return;
}
// 已经登录
//$username = $_SESSION['username'];  //用户名
$userid = $_SESSION['userid'];  //用户名

//判断请求方式
//不判断了，支持get

//==>可以在这里添加一个限制访问次数的代码<==


//请求参数 此处只判断do
if(!(isset($_GET["do"]) && $_GET["do"])){
    echo "请求参数错误";
    return;
}
//print_r($_GET);
//此处按需判断其他参数
switch ($_GET["do"]) {
    //相册
    //新建相册
    case "addNewAlbum":
        break;
    //删除相册
    case "delAlbum":
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo "请求方式错误";
            return;
        }
        if(!(isset($_POST["aid"]) && $_POST["aid"])){
            echo "缺少请求参数";
            return;
        }
        break;
    //模板
    //获取模板
    case "getTemplate": 
        //groupId
        if(!(isset($_GET["groupId"]) && $_GET["groupId"])){
            echo "缺少请求参数";
            return;
        }
        break;

    //改模版
    case "changeTemplate":
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo "请求方式错误";
            return;
        }
        if(!(isset($_POST["aid"]) && $_POST["aid"])){
            echo "缺少请求参数";
            return;
        }
        if(!(isset($_POST["templateId"]) && $_POST["templateId"])){
            echo "缺少请求参数";
            return;
        }
        break;

    //照片
    //获取图片api
    case "getImage":
        if(!(isset($_GET["aid"]) && $_GET["aid"])){
            echo "缺少请求参数";
            return;
        }
        break;
    
    //上传图片api
    case "uploadImage": 
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo "请求方式错误";
            return;
        }
        if(!(isset($_POST["aid"]) && $_POST["aid"])){
            echo "缺少请求参数";
            return;
        }
        //文件判断到地下在说，判断的内容比较多
        break;

    //删除图片
    case "delImage":
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo "请求方式错误";
            return;
        }
        if(!(isset($_POST["imageId"]) && $_POST["imageId"])){
            echo "缺少请求参数";
            return;
        }
        break;

    //移动图片
    case "moveImage":
        if ($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo "请求方式错误";
            return;
        }
        if(!(isset($_POST["imageId"]) && $_POST["imageId"])){
            echo "缺少请求参数";
            return;
        }
        if(!(isset($_POST["action"]) && $_POST["action"])){
            echo "缺少请求参数";
            return;
        }
        break;

    //没有此方式
    default:
        echo "没有此方式";
        return;
}

//连接数据库
//读取配置
$confIniArray = parse_ini_file("../conf.ini", true);
//获取图片存储文件夹路径
$imgSavePathsStart = $confIniArray["imgSavePaths"];
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

switch ($_GET["do"]) {
    //新建相册
    case "addNewAlbum":
        //新建相册的名称
        $alnumName = "我的相册，打开看看";
        //运行sql
        $sql=" INSERT INTO album (albumName,albumMreatorId) values('$alnumName',$userid)";
        //echo $sql;
        $res_insert = mysqli_query($db,$sql);
        //判断是否成功
        if(!$res_insert)  {
            echo "系统繁忙，请稍后";  
        }
        echo "新建成功";

        break;
    
    //删除相册
    case "delAlbum":
        $aid = $_POST["aid"];
        //有毛有
        $imageQuery = mysqli_query($db,"SELECT albumMreatorId FROM album WHERE id = $aid");
        if(!mysqli_num_rows($imageQuery)==1) {  
            echo "没这相册！";
            return;
        }
        //是否是自己的相册
        $imageRow = mysqli_fetch_array($imageQuery);
        $mreatorId = $imageRow["albumMreatorId"];
        if($mreatorId!=$userid){
            echo "您不是此相册的主人！！！";
            return;
        }
        //删除图片
        $photoQuery = mysqli_query($db,"SELECT photoPath FROM photos WHERE albumId = $aid");
        while ($photoRow=mysqli_fetch_assoc($photoQuery)){
            //删除文件
            $photoPath = $photoRow["photoPath"];
            $status = @unlink($imgSavePathsStart.$photoPath);    
            if (!$status){    
                echo "提示：图片文件删除失败\n";
                //return;
            }
        }
        //删除数据库
        $status = mysqli_query($db," DELETE FROM photos WHERE albumId = $aid;");
        if (!$status){    
            echo "删除失败";
            return;
        }
        //删除相册
        $status = mysqli_query($db," DELETE FROM album WHERE id = $aid;");
        if (!$status){    
            echo "删除失败";
            return;
        }
        echo "删除成功";
        break;

    //模板
    //获取模板
    case "getTemplate": 
        if ($_GET["groupId"]=="all"){
            $templatesGroupQuery = mysqli_query($db,"SELECT id,templatName,templatIMG,templatGroupId FROM templates");
        } else {
            $groupId = $_GET["groupId"];
            $templatesGroupQuery = mysqli_query($db,"SELECT id,templatName,templatIMG,templatGroupId FROM templates WHERE templatGroupId = $groupId");
        }
        //没有模板
        if(!mysqli_num_rows($templatesGroupQuery)){
            //生成返回的数组
            $returnTemplatesData = array(
                "code" => 200,
                "msg" => "没有任何模板",
                "data" => array(
                    "length" => 0,
                    "dataList" => array()
                )
            );
            echo json_encode($returnTemplatesData);
            break;
        }
        $templatesData = array();
        $templatesLen = 0;  //数据多少
        while ($templatesGroupRow=mysqli_fetch_assoc($templatesGroupQuery)){//$row=mysqli_fetch_assoc($rs)){
            $templatesLen++;//增加数据
            //查出的数据
            $templateId = $templatesGroupRow["id"];
            $templateName = $templatesGroupRow["templatName"];
            $templateIMG = $templatesGroupRow["templatIMG"];
            $templategroupId = $templatesGroupRow["templatGroupId"];
            //查询分类
            $templateGroupQuery = mysqli_query($db,"SELECT groupName FROM templatesgroup WHERE id = $templategroupId");
            if(!mysqli_num_rows($templateGroupQuery)==1) {
                $templateGroup = "无";
            } else {
                $templateGroup = mysqli_fetch_array($templateGroupQuery)["groupName"];
            }
            //生成数组
            $templateDataArr=array(
                "templateId" => $templateId,
                "templateName" => $templateName,
                "templateIMG" => $templateIMG,
                "templateGroup" => $templateGroup,
            );
            //放入总数组
            array_unshift($templatesData,$templateDataArr);
        }
        //生成返回的数组
        $returnTemplatesData = array(
            "code" => 200,
            "msg" => "查询成功！",
            "data" => array(
                "length" => $templatesLen,
                "dataList" => $templatesData
            )
        );
        echo json_encode($returnTemplatesData);
        break;
    
    //改模版
    case "changeTemplate":
        $aid = $_POST["aid"];
        $templateId = $_POST["templateId"];
        //有这相册吗？
        $albumQuery = mysqli_query($db,"SELECT albumMreatorId FROM album WHERE id = $aid");
        if(!mysqli_num_rows($albumQuery)==1) {  
            echo "没这相册！";
            return;
        }
        //是相册的作者吗？
        $albumMreatorId = mysqli_fetch_array($albumQuery)["albumMreatorId"];
        if($albumMreatorId!=$userid){
            echo "您不是此相册的作者！！！";
            return;
        }
        //更改数据库
        $changeOk = mysqli_query($db,"UPDATE album SET albumTemplateId = $templateId WHERE id = $aid;");
        if (!$changeOk){
            echo "对不起，系统正忙";
            return;
        }
        echo "更改成功";
        break;
    
    //照片
    //获取图片api
    case "getImage":
        $aid = $_GET["aid"];
        //数据库查询图片
        //$imagesQuery = mysqli_query($db," select id,photoOrder,photoUrl,photoText from photos where albumId = $aid");
        //排序版本sql查询 SELECT * FROM photos WHERE albumId = 1 ORDER BY photos.photoOrder ASC
        $imagesQuery = mysqli_query($db," SELECT id,photoOrder,photoUrl,photoText FROM photos WHERE albumId = $aid ORDER BY photos.photoOrder ASC");
        //没有模板
        if(!mysqli_num_rows($imagesQuery)){
            //生成返回的数组
            $returnImageData = array(
                "code" => 400,
                "msg" => "此相册没有图片，赶紧上传吧！",
                "data" => array(
                    "length" => 0,
                    "dataList" => array()
                )
            );
            echo json_encode($returnImageData);
            break;
        }
        $imageLen = mysqli_num_rows($imagesQuery);  //数据多少
        //$imageData = arrayLen($imageLen);
        $imageData = array();
        //print_r($imageData);
        while ($imageRow=mysqli_fetch_assoc($imagesQuery)){
            //查出的数据
            $photoUrl = $imageRow['photoUrl'];
            $photoId = $imageRow['id'];
            $photoOrder = $imageRow['photoOrder'];
            $photoText = $imageRow['photoText'];
            //生成数组
            $imageDataArr=array(
                "imageId" => $photoId,
                "imageOrder" => $photoOrder,
                "imageUrl" => $photoUrl,
                "imageText" => $photoText,
            );
            //放入总数组
            // https://www.php.cn/php-weizijiaocheng-98895.html
            //array_splice($imageData, array($imageDataArr));
            array_push($imageData,$imageDataArr);
        }
        //生成返回的数组
        $returnimageData = array(
            "code" => 200,
            "msg" => "查询成功！",
            "data" => array(
                "length" => $imageLen,
                "dataList" => $imageData
            )
        );
        echo json_encode($returnimageData);
        break;
    
    //上传图片
    case "uploadImage": 
        $aid = $_POST["aid"];
        //判断有没有，是否是自己的
        //有这相册吗？
        $albumQuery = mysqli_query($db,"SELECT albumMreatorId FROM album WHERE id = $aid");
        if(!mysqli_num_rows($albumQuery)==1) {  
            echo "没这相册！";
            return;
        }
        //是相册的作者吗？
        $albumMreatorId = mysqli_fetch_array($albumQuery)["albumMreatorId"];
        if($albumMreatorId!=$userid){
            echo "您不是此相册的作者！！！";
            return;
        }
        //整理文件
        $files = listFiles();
        //循环便利
        for ($i=0; $i<count($files); $i++) {
            //符合要求不？？？
            if (!((($files[$i]["type"] == "image/gif")
                || ($files[$i]["type"] == "image/jpeg")
                || ($files[$i]["type"] == "image/png")
                || ($files[$i]["type"] == "image/bmp")
                || ($files[$i]["type"] == "image/webp"))    //支持 .gif .jpg .jpeg .png .bmp .webp 格式
                && ($files[$i]["size"] <= 52428800)     //最大50M
                && ($i < 20)) //最多20张
            ){
                echo $files[$i]["name"]."：无效的文件";
                continue;
            }
            if ($files[$i]["error"]){
                //发生错误
                echo $files[$i]["name"]."：上传时发生错误 (错误代码 " . $files[$i]["error"] . " )";
                continue;
            }
            
            //如果文件夹不存在新建文件夹
            if(!(is_dir($imgSavePathsStart."/".date("Y"))||file_exists($imgSavePathsStart."/".date("Y")))){
                mkdir($imgSavePathsStart."/".date("Y"),0777,true);
            }
            if(!(is_dir($imgSavePathsStart."/".date("Y")."/".date("m"))||file_exists($imgSavePathsStart."/".date("Y")."/".date("m")))){
                mkdir($imgSavePathsStart."/"."/".date("Y")."/".date("m"),0777,true);
            }
            if(!(is_dir($imgSavePathsStart."/".date("Y")."/".date("m")."/".date("d"))||file_exists($imgSavePathsStart."/".date("Y")."/".date("m")."/".date("d")))){
                mkdir($imgSavePathsStart."/".date("Y")."/".date("m")."/".date("d"),0777,true);
            }
            //路径
            $imgSavePathsLast = "/".date("Y")."/".date("m")."/".date("d")."/";
            $imgSavePaths = $imgSavePathsStart.$imgSavePathsLast;
            //文件名
            $originalName = $files[$i]["name"];//原文件名
            $savename = date("YmdHis").rand(0,99999999).".".pathinfo($files[$i]["name"], PATHINFO_EXTENSION);//存储的文件名
            if (file_exists($imgSavePaths . $savename)){
                //是否重名=》重名改名
                while (!file_exists($imgSavePaths . $savename)){
                    //循环到没有重名
                    $savename = date("YmdHis").rand(0,99999999).".".pathinfo($files[$i]["name"], PATHINFO_EXTENSION);
                }
            } 
            //复制文件
            move_uploaded_file($files[$i]["tmp_name"], $imgSavePaths . $savename);
            
            $photoPath = $imgSavePathsLast.$savename;
            $photoUrl = "/download.php".$photoPath;
            //header('refresh:1; url=/test.php');
            //在图片表添加
            //photoOrder计算
            //获取有多少图片
            $userDataNum = mysqli_query($db,"select count(*) from photos where albumId = $aid;"); 
            $userDataNum = mysqli_fetch_assoc($userDataNum)["count(*)"];
            $photoOrder = $userDataNum + 1;

            $sql = " INSERT INTO photos (mreatorId,albumId,photoOrder,photoPath,photoUrl,originalName) VALUES($userid,$aid,$photoOrder,'$photoPath','$photoUrl','$originalName')";  
            $res_insert = mysqli_query($db,$sql);  
            //echo $res_insert;
            if (!$res_insert){
                echo $files[$i]["name"]." 上传失败";
            }
            
            echo $files[$i]["name"]." 上传成功";

        }
        break;

    //删除图片
    case "delImage":
        $imageId = $_POST["imageId"];
        //有毛有
        $imageQuery = mysqli_query($db,"SELECT mreatorId,photoPath FROM photos WHERE id = $imageId");
        if(!mysqli_num_rows($imageQuery)==1) {  
            echo "没这图！";
            return;
        }
        //是否是自己的图片
        $imageRow = mysqli_fetch_array($imageQuery);
        $mreatorId = $imageRow["mreatorId"];
        if($mreatorId!=$userid){
            echo "您不是此图片的主人！！！";
            return;
        }
        //删除文件
        $photoPath = $imageRow["photoPath"];
        $status = @unlink($imgSavePathsStart.$photoPath);    
        if (!$status){    
            echo "提示：图片文件删除失败\n";
            //return;
        }
        //删除数据库
        $status = mysqli_query($db," DELETE FROM photos WHERE id=$imageId;");
        if (!$status){    
            echo "删除失败";
            return;
        }
        echo "删除成功";

        break;
    
    //改变图片排序 直接
    case "moveImage":
        $imageId = $_POST["imageId"];
        $action = $_POST["action"];
        //有毛有
        $imageQuery = mysqli_query($db,"SELECT mreatorId,albumId,photoOrder FROM photos WHERE id = $imageId");
        if(!mysqli_num_rows($imageQuery)==1) {  
            echo "没这图！";
            return;
        }
        //是否是自己的图片
        $imageRow = mysqli_fetch_array($imageQuery);
        $mreatorId = $imageRow["mreatorId"];
        if($mreatorId!=$userid){
            echo "您不是此图片的主人！！！";
            return;
        }
        $photoOrder = $imageRow["photoOrder"];
        $albumId = $imageRow["albumId"];	
        //移动 向前
        if ($action == "up"){
            if ($photoOrder == 1){
                echo "移动成功";
                break;
            }
            //本质上只是调换位置
            $lastPhotoOrder = $photoOrder - 1; //上一张的
            //把上一张的order+1
            //update test SET test=test-1 WHERE test>=5 AND test<=8;
            $isOk = mysqli_query($db,"UPDATE photos SET photoOrder = photoOrder + 1 WHERE photoOrder = $lastPhotoOrder");
            if (!isOk){
                echo "移动失败";
                return;
            }
            //把本张的order-1
            $isOk = mysqli_query($db,"UPDATE photos SET photoOrder = photoOrder - 1 WHERE id = $imageId");
            if (!isOk){
                echo "移动失败";
                return;
            }
            
            echo "移动成功";
            break;

        } else if ($action == "down"){    //向后
            //获取有多少条数据 
            $dataNumQuery = mysqli_query($db,"SELECT max(photoOrder) FROM photos WHERE albumId = $albumId"); 
            
            $dataNum = mysqli_fetch_assoc($dataNumQuery)["max(photoOrder)"];
            print_r($dataNum);
            if ($photoOrder == $dataNum){
                echo "移动成功";
                break;
            }
            //本质上只是调换位置
            $lastPhotoOrder = $photoOrder + 1; //下一张的
            //把下一张的order-1
            //update test SET test=test-1 WHERE test>=5 AND test<=8;
            $isOk = mysqli_query($db,"UPDATE photos SET photoOrder = photoOrder - 1 WHERE photoOrder = $lastPhotoOrder");
            if (!isOk){
                echo "移动失败";
                return;
            }
            //把本张的order+1
            $isOk = mysqli_query($db,"UPDATE photos SET photoOrder = photoOrder + 1 WHERE id = $imageId");
            if (!isOk){
                echo "移动失败";
                return;
            }
            
            echo "移动成功";
            break;

        }


        break;

    //没有方法
    default:
        echo "没有此方式";
        return;
}
?>