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
        <div id="newbookbtn" onclick="newAlbum()">+ 制作新相册</div>
        <div style="height: 20px;"></div>
        <div id="album-list">
            <?php 
            $userid = $_SESSION['userid'];
            //连接数据库
            $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
            //mysql_select_db("my_test");  //选择数据库  
            mysqli_query($db,"set names 'utf-8'"); //设定字符集 
            $rs = mysqli_query($db," select id,albumName,albumCreateDate from album where albumMreatorId = $userid");  //执行sql！！！
            //$num = mysqli_num_rows($rs);  //获取有多少个
            //$row = mysqli_fetch_array($rs,1);
            $albumArray=array();
            while ($row=mysqli_fetch_assoc($rs)){
                array_push($albumArray,$row);
            }
            for($n=count($albumArray),$i=$n-1;$i>=0;$i--){
                $row=$albumArray[$i];
                //echo $num."</br>";
                $albumId = $row["id"];
                $albumName = $row["albumName"];
                $albumCreateDate = $row["albumCreateDate"];
                $albumCreateDay = date("Y-m-d", strtotime($albumCreateDate));
                //有多少张图片
                $sql1 = " select * from photos where albumId = $albumId";
                $rs1 = mysqli_query($db,$sql1);  //执行sql！！！
                $albumPhotoNum = mysqli_num_rows($rs1);  //获取有多少个
                echo <<<END
                <div class="item-block">
                    <div class="item-date">
                        <div class="item-timediv"></div>
                        <div class="item-time">$albumCreateDay</div>
                        <div class="sharebtn" onclick="alert('/showalbum.php/$albumId?from=index');">
                            <img class="shareicon" src="./src/image/index-share.png">
                        </div>
                    </div>
                    <div class="item">
                        <div class="item-pic checkedImg" style="background-image:url(./src/image/index-onimage.png)" onclick="location.href='/showalbum.php/$albumId?from=index';"></div>
                        <div class="item-word" onclick="location.href='/showalbum.php/$albumId?from=index';">$albumName</div>
                        <div class="item-pv" onclick="location.href='/showalbum.php/$albumId?from=index';">$albumCreateDate</div>
                        <div class="get-pic" onclick="location.href='/showphoto.php/$albumId';">提取照片</div>
                        <div class="item-edit" onclick="location.href='/makealbum.php/$albumId';">编   辑</div>
                        <div class="item-del" onclick="del_book('')"></div>
                    </div>
                </div>
                END;
            }
            ?>
        </div>
    </div>
</body>
<script>
function newAlbum() {
    isDel = confirm("确定要新建吗？")
    if (isDel){
        // 用FormData传输
        let fd = new FormData();

        let xhr = new XMLHttpRequest();
        xhr.open("get", "api/addnewalbum.php", true);

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
        
function delAlbum(aid) {
    isDel = confirm("删除后不可恢复，确定要删除吗？")
    if (isDel){
    // 用FormData传输
        let fd = new FormData();
        fd.append("aid", aid);

        let xhr = new XMLHttpRequest();
        xhr.open("post", "api/delalbum.php", true);

        // 成功
        xhr.onload = function (e) {
            alert(e.currentTarget.responseText);
            location.reload();
        }
        // 失败
        xhr.onerror = function (e) {
            alert("失败：" + e);
        }
        xhr.send(fd);
    }
}
</script>
</html>
