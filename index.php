<?php session_start(); // 开启Session ?>
<?php
if (!isset($_SESSION['islogin'])) {
    // 没有登录
    header('refresh:0; url=./login.php');
    $title = '请先登录';
    echo "<h4 id='page-title'>您还没有登录,请登录,3秒后自带跳转</h4>";
}else if (isset($_SESSION['islogin'])) {
    // 已经登录
    $username = $_SESSION['username'];//用户名
    $title = $username.' 的个人空间';
    //配置数据库
    $confIniArray = parse_ini_file("./conf.ini", true);
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
    <style>
        body {
            background: #f3f3f3;
        }
        #big-border {
            background: #fff;
            margin: 0 auto;
            padding: 10px;
        }
		#page-title {
			text-align: center;
		}
        #little-page-title{
            text-align: right;
        }
        .album{
            margin: 5;
            position: relative;
        }
        .album fieldset{
            width:300px;
            height: 120px;
        }
        .album-name{
            font-size:20px;
        }
        .album-time{
            font-size:10px;
            color:#999;
        }
        .album-photonum{
            font-size:10px;
            color:#999;
        }
        .album-getphoto{
            position: absolute;
            left: 10px;
            width: 140px;
            height: 40px;
            line-height: 40px;
            top: 90px;
            border: 1px solid #444;
            text-align: center;
            color: #444;
            border-radius: 6px 6px 6px 6px;
            font-size: 18px;
        }
        .album-edit{
            background-color: #444;
            border-radius: 6px;
            width: 140px;
            height: 42px;
            position: absolute;
            color: #fff;
            text-align: center;
            line-height: 42px;
            font-size: 20px;
            top: 90px;
            left: 175px;
        }
        .new-album{
            width:300px;
        }
    </style>
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
    <script>
        function newAlbum() {
            // 用FormData传输
            let fd = new FormData();

            let xhr = new XMLHttpRequest();
            xhr.open("get", "make/new.php", true);

            // 成功
            xhr.onload = function (e) {
                if (e.currentTarget.responseText == "请先登录"){
                    alert("请先登录");
                    location.href = "/user/login.php";
                }
                if (e.currentTarget.responseText == "新建成功"){
                    alert("新建成功");
                    location.reload();
                }
                if (e.currentTarget.responseText == "系统繁忙，请稍后"){
                    alert("系统繁忙，请稍后");
                }
            }
            // 失败
            xhr.onerror = function (e) {
                alert("失败：" + e);
            }
            xhr.send(fd);
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
</body>
</html>
