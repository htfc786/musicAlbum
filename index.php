<?php session_start(); // 开启Session ?>
<?php
$confIniArray = parse_ini_file("./conf.ini", true);
$PrePath = $confIniArray["PrePath"];
if (!isset($_SESSION['islogin'])) {
    // 没有登录
    header('refresh:0; url='.$PrePath.'login.php');
    $title = '请先登录';
    echo "<h4 id='page-title'>您还没有登录,请登录,3秒后自带跳转</h4>";
}else if (isset($_SESSION['islogin'])) {
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
}
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
        <h6 id="little-page-title">by--htfc786</h6>
        <h3 id="little-page-title"><a href='../logout.php'>登出</a></h3>
        <div id="msg"></div>
        <hr>
        <div>
            <div>
            <?php 
            if (isset($_SESSION['islogin'])) {
                // 已经登录
                
                $userid = $_SESSION['userid'];
                //连接数据库
                $db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);    //连接数据库  
                //mysql_select_db("my_test");  //选择数据库  
                mysqli_query($db,"set names 'utf-8'"); //设定字符集 
                $sql = " select id,albumName,albumCreateDate from album where albumMreatorId = $userid";
                $rs = mysqli_query($db,$sql);  //执行sql！！！
                //$num = mysqli_num_rows($rs);  //获取有多少个
                //$row = mysqli_fetch_array($rs,1);
                while ($row=mysqli_fetch_assoc($rs)){
                    //echo $num."</br>";
                    $albumId = $row["id"];
                    $albumName = $row["albumName"];
                    $albumCreateDate = $row["albumCreateDate"];
                    //有多少张图片
                    $sql1 = " select * from photos where albumId = $albumId";
                    $rs1 = mysqli_query($db,$sql1);  //执行sql！！！
                    $albumPhotoNum = mysqli_num_rows($rs1);  //获取有多少个
                    echo <<<END
                    <div class="album">
                        <a onclick="window.open('/showalbum.php/$albumId?from=index','_blank');">
                            <fieldset>
                                <div class="album-name" style="">$albumName</div>   
                                <div class="album-time">创建时间：$albumCreateDate</div>
                                <div class="album-photonum">$albumPhotoNum 张照片</div>
                                <a onclick="delAlbum($albumId);">删除</a>
                                <div class="album-getphoto" onclick="window.location.href='/showphoto.php/$albumId';">提取图片</div>
                                <div class="album-edit" onclick="window.location.href='/makealbum.php/$albumId';">编辑</div>
                            </fieldset>
                        </a>
                    </div>
                    </br>
                    END;
                } 
            }
            ?>
            </div>
            <a onclick="newAlbum()">
                <fieldset class="new-album">
                +新建相册
                </fieldset>
            </a>
        </div>
        <hr>
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
