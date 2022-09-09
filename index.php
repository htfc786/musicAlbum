<?php session_start(); // 开启Session ?>
<?php
$confIniArray = parse_ini_file("./conf.ini", true);
$PrePath = $confIniArray["PrePath"];
if (!isset($_SESSION['islogin'])) {
    // 没有登录
    header('refresh:0; url='.$PrePath.'login.php');
    $title = '请先登录';
    echo "<h4 id='page-title'>您还没有登录,请登录,3秒后自带跳转</h4>";
    return;
}
// 已经登录
$username = $_SESSION['username'];//用户名
$title = $username.' 的个人空间';
//配置数据库
//print_r($confIniArray);
$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"]; // 请在此修改数据库密码
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?>_音乐相册</title>
    <link rel="stylesheet" href="./src/css/main-index.css">
</head>
<body>
    <div id="big-border">
        <h2 id="page-title"><?php echo $title; ?></h2>
        <h3 id="little-page-title" style="margin: 8px;"><a href='../logout.php'>登出</a></h3>
        <div id="newbookbtn" onclick="newAlbumTh()">+ 制作新相册</div>
        <div style="height: 20px;"></div>
        <div id="album-list">
            <?php 
            $userid = $_SESSION['userid'];
            //连接数据库
            $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
            //mysql_select_db("my_test");  //选择数据库  
            mysqli_query($db,"set names 'utf-8'"); //设定字符集 
            //查询数据
            //使用sql倒志版本 SELECT * FROM album WHERE albumMreatorId = 4 ORDER BY album.albumCreateDate DESC
            $albumDataQuery = mysqli_query($db,"  SELECT id,albumName,albumCreateDate,albumCover FROM album WHERE albumMreatorId = $userid ORDER BY album.albumCreateDate DESC");  //执行sql！！！
            
            //$row = mysqli_fetch_array($rs,1);
            //先获取数据再次倒置循环
            //$albumArray=array();
            //while ($row=mysqli_fetch_assoc($rs)){
            //    array_push($albumArray,$row);
            //}
            //for($n=count($albumArray),$i=$n-1;$i>=0;$i--){
            
            if (mysqli_num_rows($albumDataQuery)==0){
                echo '<div class="album-red-msg">当前用户中没有相册，创建一个吧</div>';
            } else {
                while ($albumDataRow=mysqli_fetch_assoc($albumDataQuery)){
                    //$row=$albumArray[$i];
                    //echo $num."</br>";
                    //id
                    $albumId = $albumDataRow["id"];
                    //名称
                    $albumName = $albumDataRow["albumName"];
                    //创建时间
                    $albumCreateDate = $albumDataRow["albumCreateDate"];
                    $albumCreateDay = date("Y-m-d", strtotime($albumCreateDate));
                    //有多少张图片
                    $albumPhotoQuery = mysqli_query($db," SELECT count(*) FROM photos WHERE albumId = $albumId");
                    $albumPhotoNum = mysqli_fetch_assoc($albumPhotoQuery)["count(*)"];  //获取有多少个
                    //获取封面
                    if ($albumDataRow["albumCover"]){
                        //如果设置了封面
                        $albumCover = $albumDataRow["albumCover"];
                    } else {
                        //如果没有设置封面
                        if($albumPhotoNum!=0){
                            //相册里有照片
                            $firstPhotoQuery = mysqli_query($db," SELECT photoUrl FROM photos WHERE photoOrder = (SELECT min(photoOrder) FROM photos WHERE albumId = $albumId) AND albumId = $albumId");
                            $firstPhoto = mysqli_fetch_assoc($firstPhotoQuery);
                            $albumCover = $firstPhoto["photoUrl"];
                        } else {
                            //实在没有图片了
                            $albumCover = "./src/image/index-onimage.png";
                        }
                    }
                    //$albumPhotoQuery = mysqli_query($db," SELECT * FROM photos WHERE albumId = $albumId");
                    //$albumPhotoNum = mysqli_fetch_assoc($albumPhotoQuery);  //获取有多少个
                    echo <<<END
                    <div class="item-block">
                        <div class="item-date">
                            <div class="item-timediv"></div>
                            <div class="item-time">$albumCreateDay</div>
                            <div class="sharebtn" onclick="alert('/albumshow.php/$albumId?from=index');">
                                <img class="shareicon" src="./src/image/index-share.png">
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-pic checkedImg" style="background-image:url($albumCover)" onclick="location.href='/albumshow.php/$albumId?from=index';"></div>
                            <div class="item-word" onclick="location.href='/albumshow.php/$albumId?from=index';">$albumName</div>
                            <div class="item-num" onclick="location.href='/albumshow.php/$albumId?from=index';">共 $albumPhotoNum 张照片</div>
                            <div class="item-pv" onclick="location.href='/albumshow.php/$albumId?from=index';">$albumCreateDate</div>
                            <div class="get-pic" onclick="location.href='/albumphoto.php/$albumId';">提取照片</div>
                            <div class="item-edit" onclick="location.href='/albummake.php/$albumId';">编   辑</div>
                            <div class="item-del" onclick="delAlbum($albumId)"></div>
                        </div>
                    </div>
                    END;
                }
            }

            ?>
        </div>
    </div>
</body>
<script>
//节流
function throttle(delay, func, errFunc){
    let timer;
    return function () {
        let context = this;
        let args = arguments;
        if (timer){
            errFunc.apply(context, args);
            return;
        }
        func.apply(context, args);
        timer = setTimeout(function () {
            timer = null;
        }, delay);
    }
}

function newAlbum() {   //新建相册
    if (confirm("确定要新建吗？")){
        // 用FormData传输
        let fd = new FormData();

        let xhr = new XMLHttpRequest();
        xhr.open("get", "api/albummake.php?do=addNewAlbum", true);

        // 成功
        xhr.onload = function (e) {
            alert(e.currentTarget.responseText);
            if (e.currentTarget.responseText == "请先登录"){
                location.href = "./login.php";
            }
            if (e.currentTarget.responseText == "新建成功"){
                location.reload();
            }
        }
        // 失败
        xhr.onerror = function (e) {
            alert("请求失败：" + e);
        }
        xhr.send(fd);
    }
    
}
let newAlbumTh = throttle(60000, newAlbum, function () {    //新建相册 节流版本
    alert("您点击的速度过快！");
}); 

function delAlbum(aid) {
    if(!confirm("此操作将会删除此相册，确定要删除吗？")){
        return;
    }
    if(!confirm("删除后不可恢复，确定要删除吗？")){
        return;
    }
    // 用FormData传输
    let fd = new FormData();
    fd.append("aid", aid);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "api/albummake.php?do=delAlbum", true);

    // 成功
    xhr.onload = function (e) {
        alert(e.currentTarget.responseText);
        location.reload();
    }
    // 失败
    xhr.onerror = function (e) {
        alert("网络错误，请求失败");
    }
    xhr.send(fd);
}
</script>
</html>
