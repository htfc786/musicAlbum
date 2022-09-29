<?php
function getId(){	//获取请求id
    $php_self_name = basename(__FILE__);//php脚本名称
	$meet_php = 0;//False;//是否遇见这个php脚本名称
	$last_path = "";//请求php文件后面写的东西
	$urls = explode("/",$_SERVER['PHP_SELF']);//用“/”分割字符串
	foreach ($urls as $i){
			//echo $i."/";
		if ($meet_php) {	//遇见这个php脚本名称之后添加今路径变量里
			$last_path = $last_path."/".$i;
		}
		if ($i == $php_self_name){//如果遇见这个php脚本名称标记一下
			$meet_php = 1;//True;
		}
	}
    if ($last_path){
        $urls = explode("/",$last_path);//用“/”分割字符串
        return $urls[1];
    }
    return "";
}

// 开启Session
session_start();  

$confIniArray = parse_ini_file("./conf.ini", true); //配置文件
$PrePath = $confIniArray["PrePath"];

if (!isset($_SESSION['islogin'])) {
    // 没有登录
    header('refresh:0; url='.$PrePath.'login.php');
    echo "<h4>需要登录才能访问此页面</h4>";
    //echo 'refresh:0; url='.$PrePath.'login.php';
    return;
}
$userid = $_SESSION['userid'];

//配置数据库信息
$dbHost = $confIniArray["dbHost"];
$dbUser = $confIniArray["dbUser"];
$dbPassword = $confIniArray["dbPassword"];
$dbDatabase = $confIniArray["dbDatabase"];
$dbPort = $confIniArray["dbPort"];
$dbEncoding = $confIniArray["dbEncoding"];

//获取id
$aid = getId();
if (!$aid){
    echo "没有此相册";
    return;
}

//获取数据库对象
$db = mysqli_connect($dbHost,$dbUser,$dbPassword,$dbDatabase,$dbPort);
mysqli_query($db,"set names '$dbEncoding'"); //设定字符集

$albumDataQuery = mysqli_query($db,"SELECT albumName,albumMreatorId,albumTemplateId FROM album WHERE id = $aid");
if (mysqli_num_rows($albumDataQuery)!=1){//获取有多少个
    echo "没有此相册";
    return;
}

$albumData = mysqli_fetch_array($albumDataQuery);
$albumMreatorId = $albumData["albumMreatorId"];
if($userid!=$albumMreatorId){//不是主人
    echo "提示：您不是此相册的主人";
    return;
}

$albumName = $albumData["albumName"];

$albumTemplateId = $albumData["albumTemplateId"];
$templateDataQuery = mysqli_query($db,"SELECT canWriteText,canPlayMusic FROM templates WHERE id = $albumTemplateId");
$templateDataData = mysqli_fetch_array($templateDataQuery);
//canWriteText canPlayMusic
$canWriteText = $templateDataData["canWriteText"];
$canPlayMusic = $templateDataData["canPlayMusic"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=500, initial-scale=1.0">
    <title>制作相册</title>
    <link rel="stylesheet" href="../src/css/main-albummake.css">
</head>
<body>
    <div id="main">
        <div class="main-footer">
            <div onclick="chengePage(1)" class="main-footer-btn" style="background-image: url(/src/image/make-model.png);"></div>
            <div onclick="chengePage(2)" class="main-footer-btn" style="background-image: url(/src/image/make-music.png);"></div>
            <div onclick="chengePage(3)" class="main-footer-btn" style="background-image: url(/src/image/make-pic.png);"></div>
            <div onclick="chengePage(4)" class="main-footer-btn" style="background-image: url(/src/image/make-write.png);"></div>
            <div onclick="window.open('/albumshow.php/<?php echo $aid; ?>');" class="main-footer-btn" style="background-image: url(/src/image/make-save.png);"></div>
        </div>
        <iframe id="showalbumFrame" name="ifd" onload="changeShowAlbumFrameHeight();" width="100%" frameborder="0" src="/albumshow.php/<?php echo $aid ?>?from=albummake-iframe"></iframe>
        <div onclick="location.href='../';" class="back">返回</div>
    </div>
    <div id="template" style="display: none;">
        <div id="template-header">
            <div class="template-close" onclick="chengePage(0)">返 回</div>
            <div id="template-menu">
                <div class="template-menu-btn" onclick="getTemplateGroup('all',this)">全部</div>
                <?php
                $templatesGroupQuery = mysqli_query($db,"SELECT id,groupName FROM templatesgroup");
                while ($templatesGroupRow=mysqli_fetch_assoc($templatesGroupQuery)){//$row=mysqli_fetch_assoc($rs)){
                    $groupId = $templatesGroupRow["id"];
                    $groupName = $templatesGroupRow["groupName"];
                    echo "<div class=\"template-menu-btn\" onclick=\"getTemplateGroup('$groupId',this)\">$groupName</div>";
                }
                ?>
            </div>
        </div>
        <div id="template-box">    
            
        </div>
        <div id="template-getInfo-loading" style="display:none;">
            <div id="template-getInfo-loading-background"></div>
            <img id="template-getInfo-loading-image" src="/src/image/make-image-loading.png">
            <span id="template-getInfo-loading-text">加载中</span>
        </div>
    </div>
    <div id="music" style="display: none;">
        <div id="music-header">
            <div id="music-search-btn" onclick="search_musicshow()">搜索</div>
            <div class="music-close" onclick="chengePage(0)">返回</div>
            <div id="music-menu">
                <div class="music-menu-btnon" onclick="">热门</div>
                <div class="music-menu-btn" onclick="">节日</div>
                <div class="music-menu-btn" onclick="">榜单</div>
            </div>
        </div>

        <div class="music-list">
            <div class="music-item">
                <div class="music-item-img">
                    <img src="https://s2.kagirl.cn/template/new/yinfu1.png">
                </div>
                <div class="music-item-title" onclick="">boot</div>
                <div class="music-item-ok" onclick="">确定</div>
            </div>
        </div>
    </div>
    <div id="image" style="display: none;">
        <div id="image-header">
            <div class="image-addimg-btn" onclick="uploadFiles()">添加图片</div>
            <div class="image-frush-btn" onclick="getImageTh()">刷新</div>
            <div class="image-close" onclick="chengePage(0)">返回</div>
        </div>
        <div id="image-box">

        </div>
        
        <input id="image-uploadFiles" name="files" type="file" multiple="" accept=".jpg,.jpeg,.png,.bmp,.gif,.webp" style="display:none;"/>
        <div id="image-screenBlack" style="display:none;"></div>
        <div id="image-screenLoading" style="display:none;">
            <div id="image-screenLoading-background"></div>
            <img id="image-screenLoading-image" src="/src/image/make-image-loading.png">
            <span id="image-uploadMsg">上传中</span>
        </div>
        
        <div id="image-getInfo-loading" style="display:none;">
            <div id="image-getInfo-loading-background"></div>
            <img id="image-getInfo-loading-image" src="/src/image/make-image-loading.png">
            <span id="image-getInfo-loading-text">加载中</span>
        </div>
    
    </div>
    <div id="write" style="display: none;">
        <div id="write-header">
            <div class="write-close" onclick="chengePage(0)">返回</div>
        </div>
        <div style="height: 70px;"></div>
        <div>
            <textarea id="write-albumname" maxlength="40" rows="3" placeholder="点击这里给相册写标题（限40字）"><?php echo $albumName; ?></textarea>
        </div>
        <div class="write-tip"></div>

        <?php if(!$canWriteText){   //如果不支持文字
            echo '<div style="margin: 8px; color: #f00; font-size: 20px;">提示：对不起，当前相册使用的模板不支持显示您写的文字</div>';
        } ?>
        
        <div id="write-box">
            
        </div>

        <div id="write-getInfo-loading" style="display:none;">
            <div id="write-getInfo-loading-background"></div>
            <img id="write-getInfo-loading-image" src="/src/image/make-image-loading.png">
            <span id="write-getInfo-loading-text">加载中</span>
        </div>

    </div>
</body>
<script>
//感谢：
//检测底部 https://www.jianshu.com/p/c464576a43e4
//窗口高度 https://www.jianshu.com/p/193789c14138
//图片懒加载 https://zhuanlan.zhihu.com/p/55311726
//节流 https://www.bilibili.com/video/BV17b4y1X7yp

var aid = <?php echo $aid; ?>;
var albumName = "<?php echo $albumName; ?>"
// 各个页面的div
let pageDivList = [];
pageDivList.push([document.getElementById("main")]); 
pageDivList.push([document.getElementById("template"),0,0]);//操作的对象，当前分类，当前页面
pageDivList.push([document.getElementById("music")]);
pageDivList.push([document.getElementById("image"),0]);//操作的对象,是否需要重新加载
pageDivList.push([document.getElementById("write"),0]);

//当前页面 div的
let nowPage;
nowPage=0;

//功能函数
//获取窗口可视高度
function getClientHeight(){
    var clientHeight = 0; var scrollHeight;
    var scrollHeight = Math.max(document.body.scrollHeight,document.documentElement.scrollHeight);//取文档内容实际高度
    if(document.body.clientHeight&&document.documentElement.clientHeight){
        clientHeight = (document.body.clientHeight<document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;
    } else {
        clientHeight = (document.body.clientHeight>document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;
    }
    return scrollHeight;
}
//offsetTop是元素与offsetParent的距离，循环获取直到页面顶部
function getTop(e) {
    var T = e.offsetTop;
    while(e = e.offsetParent) {
        T += e.offsetTop;
    }
    return T;
}
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
//改变frame大小
function changeShowAlbumFrameHeight(){
    document.getElementById("showalbumFrame").height=getClientHeight()-70;
    //document.getElementById("showalbumFrame").width=getClientHeight()-70;
}

//改变页面
function chengePage(id){
    var lastPage = nowPage;
    if(lastPage==4){
        changeImageText();
        changeAlbumName();
    }
    nowPage = id;
    for(var i=0;i<pageDivList.length;i++){
        pageDivList[i][0].style.display = "none";
    }
    pageDivList[id][0].style.display = "block";
    window.onscroll()
    if (id==0){
        document.getElementById("showalbumFrame").contentWindow.location.reload(true);
    } else if (id==1){
        getTemplateGroup("all",0);
    } else if (id==3) {
        if (pageDivList[3][1]==0){
            getImage();
            pageDivList[3][1]=1;
        }
    } else if (id==4){
        if (pageDivList[4][1]==0){
            getImageText();
            pageDivList[4][1]=1;
        }
    }
}

//图片懒加载
function lazyLoad(imgs) {
    var H = document.documentElement.clientHeight;  //获取可视区域高度
    var S = document.documentElement.scrollTop || document.body.scrollTop;
    for (var i = 0; i < imgs.length; i++) {
        if (H + S > getTop(imgs[i])) {
            imgs[i].src = imgs[i].getAttribute('data-src');
        }
    }
}

// 模板
function getTemplateGroup(groupId,btnObj){
    document.getElementById("template-getInfo-loading").style.display="block";
    //console.log("调用了 getTemplateGroup");
    //改横线的位置
    templateMenuBtn = document.getElementsByClassName("template-menu-btn")
    for(var i=0;i<templateMenuBtn.length;i++){
        templateMenuBtn[i].className = "template-menu-btn";
    }
    if (btnObj==0){
        document.getElementsByClassName("template-menu-btn")[0].className = "template-menu-btn template-menu-btnon";
    } else {
        btnObj.className = "template-menu-btn template-menu-btnon";
    }
    if(pageDivList[1][1] == groupId) {
        document.getElementById("template-getInfo-loading").style.display="none";
        return;
    }
    pageDivList[1][1] = groupId;
    //请求
    let xhr = new XMLHttpRequest();
    xhr.open("get", "../api/albummake.php?do=getTemplate&groupId="+groupId, true);
    // 请求成功
    xhr.onload = function (e) {
        try {var data = JSON.parse(e.currentTarget.responseText);}
        catch(err) {alert(e.currentTarget.responseText);return;}
        //console.log(data)
        if (data["data"]["length"]==0){
            alert(data["msg"]);
            return;
        }
        var dataList = data["data"]["dataList"];
        templateBox = document.getElementById("template-box");
        templateBox.innerHTML = "";
        for(var i=0;i<dataList.length;i++){
            templateData = dataList[i];
            templateMainHtml = `
            <div class="template-item" onclick="changeTemplate(${templateData.templateId})">
                <img src="${templateData.templateIMG}">
                <span>${templateData.templateName}</span>
                <p>分类：${templateData.templateGroup}</p>
            </div>
            `
            templateBox.innerHTML += templateMainHtml;
        }
        document.getElementById("template-getInfo-loading").style.display="none";
    }
    // 请求失败
    xhr.onerror = function (e) {
        //uploadMsg.innerHTML = "上传失败：" + e
        alert("请求失败");
        document.getElementById("template-getInfo-loading").style.display="none";
    }
    xhr.send();
}
function changeTemplate(templateId){
    // 用FormData传输
    var fd = new FormData();

    fd.append("aid",aid);
    fd.append("templateId",templateId);

    //发送请求
    let xhr = new XMLHttpRequest();
    xhr.open("post", "../api/albummake.php?do=changeTemplate", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("请求发生错误");
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        alert(e.currentTarget.responseText);
        if (e.currentTarget.responseText=="更改成功"){
            document.getElementById("showalbumFrame").contentWindow.location.reload(true);
            chengePage(0);
        }
    }

    xhr.send(fd);//发送请求！！！
}

// 图片
function getImage(){    //获取图片
    document.getElementById("image-getInfo-loading").style.display="block";
    //请求
    let xhr = new XMLHttpRequest();
    xhr.open("get", "../api/albummake.php?do=getImage&aid="+aid, true);
    // 请求成功
    xhr.onload = function (e) {
        try {var data = JSON.parse(e.currentTarget.responseText);}//尝试读取json
        catch(err) {alert(e.currentTarget.responseText);
            document.getElementById("image-getInfo-loading").style.display="none";return;}
        //console.log(data)
        imageBox = document.getElementById("image-box");

        if (data["data"]["length"]==0){
            imageBox.innerHTML = `<div class="image-msg">${data["msg"]}</div>`;
            document.getElementById("image-getInfo-loading").style.display="none";
            return;
        }
        var dataList = data["data"]["dataList"];
        imageBox.innerHTML = "";
        for(var i=0;i<dataList.length;i++){
            imageData = dataList[i];
            imageMainHtml = `
            <div class="image-item" id="image-order-${imageData.imageOrder}">
                <img data-src="${imageData.imageUrl}" src="${imageData.imageUrl}" alt="" class="image-pic">
                <div class="image-del" onclick="delImage(${imageData.imageId})">
                    <img src="../src/image/make-image-cha.png">
                </div>
                <!--<div class="image-turn">
                    <img onclick="" src="../src/image/make-image-turn.png">
                </div>-->
                <div class="image-next">
                    <div></div>
                    <img onclick="moveImage(${imageData.imageId}, ${imageData.imageOrder}, 'up', 'image')" class="image-next-left" src="../src/image/make-image-next.png">
                    <img onclick="moveImage(${imageData.imageId}, ${imageData.imageOrder}, 'down', 'image')" class="image-next-right" src="../src/image/make-image-next.png">
                </div>
            </div>
            `
            imageBox.innerHTML += imageMainHtml;
        }
        document.getElementById("image-getInfo-loading").style.display="none";
    }
    // 请求失败
    xhr.onerror = function (e) {
        document.getElementById("image-getInfo-loading").style.display="none";
        //uploadMsg.innerHTML = "上传失败：" + e
        alert("请求失败");
    }
    xhr.send();
    //懒加载调用
    window.onscroll();
    var imgs = document.querySelectorAll('img[data-src]');
    lazyLoad(imgs);
}
let getImageTh = throttle(0, getImage, function () {    //获取文件 节流版本
    document.getElementById("image-getInfo-loading").style.display="block";
    let beforeWidth = document.getElementById("image-getInfo-loading").style.width
    document.getElementById("image-getInfo-loading").style.width="250px";
    let beforeText = document.getElementById("image-getInfo-loading-text").innerText;
    document.getElementById("image-getInfo-loading-text").innerText="您点击的速度太快了！"
    setTimeout(function () {
        document.getElementById("image-getInfo-loading").style.display="none";
        document.getElementById("image-getInfo-loading").style.width=beforeWidth;
        document.getElementById("image-getInfo-loading-text").innerText=beforeText;
    },100);
}); 
function uploadFiles() {    //上传文件
    let uploadFiles = document.getElementById("image-uploadFiles");
    let uploadMsg = document.getElementById("image-uploadMsg");
            
    uploadFiles.click();

    uploadFiles.onchange=function () {
        //alert("1111")
        //document.getElementById("uploadTs").style.display=block;
                        
        if (!uploadFiles.files[0]) {
            alert("请选择文件！");
            return;
        }
        // 用FormData传输
        let fd = new FormData();

        fd.append("aid", aid);

        for (var i=0;i<=uploadFiles.files.length;i++) {
            fd.append("files["+i+"]", uploadFiles.files[i]);
        }

        document.getElementById("image-screenBlack").style.display="block";
        document.getElementById("image-screenLoading").style.display="block";
        // 文件上传并获取进度
        let xhr = new XMLHttpRequest();
        xhr.open("post", "/api/albummake.php?do=uploadImage", true);
        // 获取进度
        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                // 文件上传进度
                // 获取百分制的进度
                let filePercent = Math.round(e.loaded / e.total * 100);
                // 长度根据进度条的总长度等比例扩大
                //probg.style.width = progress.clientWidth / 100 * percent + "px";
                // 进度数值按百分制来
                uploadMsg.innerHTML = "上传进度：" + filePercent + "%";
            }
        }
        // 上传成功
        xhr.onload = function (e) {
            uploadMsg.innerHTML = "上传成功";
            alert(e.currentTarget.responseText);
            document.getElementById("image-screenBlack").style.display="none";
            document.getElementById("image-screenLoading").style.display="none";
            uploadFiles.value=null;
            getImage();
        }
        // 上传失败
        xhr.onerror = function (e) {
            uploadMsg.innerHTML = "上传失败：" + e
            alert("上传失败：" + e);
        }

        xhr.send(fd);
    }
}
function delImage(imageId) {    //删除图片
    //提示框
    if(!confirm("此操作将会删除此照片，确定要继续吗？")){
        return;
    }
    //发送删除请求
    // 用FormData传输
    var fd = new FormData();
    fd.append("imageId", imageId);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "../api/albummake.php?do=delImage", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("网络错误");
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        alert(e.currentTarget.responseText);
        getImage();
    }

    xhr.send(fd);//发送请求！！！
}
let moveImageCanRun = true;
function moveImage(imageId, imageOrder, action, from){
    if (!moveImageCanRun){
        //console.log("moveImage can not run")
        return;
    }
    moveImageCanRun = false;
    //console.log("moveImage before")
    //获取图片列表 image-box
    let imageBox = document.getElementById("image-box");

    var obj = document.getElementById("image-order-" + imageOrder);
    

    // 用FormData传输
    var fd = new FormData();

    fd.append("imageId", imageId);
    fd.append("action", action);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "../api/albummake.php?do=moveImage", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("网络错误");
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        //alert(e.currentTarget.responseText);
        //console.log("moveImage after")
        if (from=='image'){
            getImage();
        } else if (from=='write'){
            getImageText();
        }
        
        //imageBox.insertBefore(nextObj,obj);
        //imageBox.children
        moveImageCanRun = true;
    }

    xhr.send(fd);//发送请求！！！
}
// 写文字
function getImageText(){    //获取图片
    document.getElementById("write-getInfo-loading").style.display="block";
    //请求
    let xhr = new XMLHttpRequest();
    xhr.open("get", "../api/albummake.php?do=getImage&aid="+aid, true);
    // 请求成功
    xhr.onload = function (e) {
        try {var data = JSON.parse(e.currentTarget.responseText);}//尝试读取json
        catch(err) {alert(e.currentTarget.responseText);
            document.getElementById("write-getInfo-loading").style.display="none";return;}
        //console.log(data)
        imageBox = document.getElementById("write-box");

        if (data["data"]["length"]==0){
            imageBox.innerHTML = `<div class="image-msg">${data["msg"]}</div>`;
            document.getElementById("write-getInfo-loading").style.display="none";
            return;
        }
        var dataList = data["data"]["dataList"];
        imageBox.innerHTML = "";
        for(var i=0;i<dataList.length;i++){
            imageData = dataList[i];
            imageMainHtml = `
            <div class="write-item"  id="write-order-${imageData.imageOrder}">
                <div class="write-bigpic" style="background-image:url(${imageData.imageUrl})"></div>
                <textarea class="write-editwords" placeholder="点击这里给照片添加文字（限16字）" maxlength="16">${imageData.imageText}</textarea>
                <div class="write-bigup" onclick="moveImage(${imageData.imageId}, ${imageData.imageOrder}, 'up', 'write')">
                    <img src="/src/image/make-write-posup.png">
                </div>
                <div class="write-bigdown" onclick="moveImage(${imageData.imageId}, ${imageData.imageOrder}, 'down', 'write')">
                    <img src="/src/image/make-write-posdown.png">
                </div>
            </div>
            `
            imageBox.innerHTML += imageMainHtml;
        }
        document.getElementById("write-getInfo-loading").style.display="none";
    }
    // 请求失败
    xhr.onerror = function (e) {
        document.getElementById("write-getInfo-loading").style.display="none";
        //uploadMsg.innerHTML = "上传失败：" + e
        alert("请求失败");
    }
    xhr.send();
}
function changeAlbumName(){
    var albumnameBox = document.getElementById('write-albumname');
    albumnameBox.value = albumnameBox.value.replace("\n"," ");
    var newAlbumName = albumnameBox.value;
    
    if (newAlbumName==albumName){
        return;
    }
    if (newAlbumName==''){
        albumnameBox.value = albumName;
        return;
    }
    //发送请求
    // 用FormData传输
    var fd = new FormData();
    fd.append("aid", aid);
    fd.append("newAlbumName", newAlbumName);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "../api/albummake.php?do=changeAlbumName", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("网络错误");
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        //alert(e.currentTarget.responseText);
        //getImage();
    }

    xhr.send(fd);//发送请求！！！
}
function changeImageText(){
    var imageTextList = "";
    photoTextObj = document.getElementsByClassName("write-editwords");
    for (var i = 0; i < photoTextObj.length; i ++) {
        if (photoTextObj.length-1 == i) {imageTextList += photoTextObj[i].value; continue;}
        else{imageTextList += photoTextObj[i].value + '","';}
    }
    imageTextList = '["'+imageTextList+'"]';
    //console.log(imageTextList)
    //'["'+["1","1","123xzc","啊啊啊"].join('","')+'"]'
    // 用FormData传输
    var fd = new FormData();
    fd.append("aid", aid);
    fd.append("data", imageTextList);

    let xhr = new XMLHttpRequest();
    xhr.open("post", "../api/albummake.php?do=changeText", true);
    
    //发生错误
    xhr.onerror = function (e) {
        alert("网络错误");
    }
    //请求成功 等返回结果
    xhr.onload = function (e) {
        //alert(e.currentTarget.responseText);
        getImageText();
    }

    xhr.send(fd);//发送请求！！！
}

window.onscroll = function(){
    var scrollTop = document.documentElement.scrollTop||document.body.scrollTop;  //滚动条滚动时，距离顶部的距离
    var windowHeight = document.documentElement.clientHeight || document.body.clientHeight;  //可视区的高度
    var scrollHeight = document.documentElement.scrollHeight||document.body.scrollHeight;  //滚动条的总高度
    
    if(scrollTop+windowHeight>=scrollHeight){   //滚动到底部了
        //console.log("滚动到底部了！！！")
    }

    changeShowAlbumFrameHeight()
    if(nowPage == 3) {
        var imgs = document.querySelectorAll('img[data-src]');
        lazyLoad(imgs);
    }
    
}
window.onresize = function(){
    changeShowAlbumFrameHeight();
}
window.onload = function(){   //加载时
    window.onscroll();
    
}

</script>
</html>
