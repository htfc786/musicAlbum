<?php
function get_path(){	//获取请求php文件后面写的东西
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
    return $last_path;
}
//文件类型转MIME格式
function fileType2MIME($file_type){
    switch($file_type){
        //文字类型
        case "txt": return "text/plain";
        case "php": return "text/x-php";
        case "html": return "text/html";
        case "htm": return "text/html";
        case "js": return "text/javascript";
        case "css": return "text/css";
        case "rtf": return "text/rtf";
        case "rtfd": return "text/rtfd";
        case "py": return "text/x-python";
        case "java": return "text/x-java-source";
        case "rb": return "text/x-ruby";
        case "sh": return "text/x-shellscript";
        case "pl": return "text/x-perl";
        case "sql": return "text/x-sql";
        //图片
        case "gif": return "image/gif";
        case "jpg": return "image/jpeg";
        case "jpeg": return "image/jpeg";
        case "png": return "image/png";
        case "bmp": return "image/bmp";
        case "webp": return "image/webp";
        case "tif": return "image/tiff";
        case "tiff": return "image/tiff";
        //音频文件类型的
        case "mp3": return "audio/mpeg";
        case "mid": return "audio/midi";
        case "ogg": return "audio/ogg";
        case "mp4a": return "audio/mp4";
        case "wav": return "audio/wav";
        case "wma": return "audio/x-ms-wma";
        case "m4a": return "audio/mp4";
        //视频文件类型的
        case "mp4": return "video/mp4";
        case "mpeg": return "video/mpeg";
        case "mpg": return "video/mpeg";
        case "mov": return "video/quicktime";
        case "flv": return "video/x-flv";
        case "mkv": return "video/x-matroska";
        case "wm": return "video/x-ms-wmv";
        case "avi": return "video/x-msvideo";
        case "dv": return "video/x-dv";
        case "webm": return "video/webm";
        //其他
        case "": return "";
        case "pdf": return "application/pdf";
        case "xml": return "application/xml";
        case "swf": return "application/x-shockwave-flash";
        case "doc": return "application/vnd.ms-word";
        case "xls": return "application/vnd.ms-excel";
        case "ppt": return "application/vnd.ms-powerpoint";

        default: return "application/octet-stream";
    }
}

$confIniArray = parse_ini_file("./conf.ini", true); //配置文件
$imgSavePaths = $confIniArray["imgSavePaths"];   //获取图片存储文件夹路径

$file_path = $imgSavePaths . get_path();
//echo $file_path;
//1.打开文件
if(!is_file($file_path)){
    echo "文件不存在!";
    return ;
}
$fp=fopen($file_path,"r");
//2.处理文件
//获取下载文件的大小
$file_size=filesize($file_path);
if($file_size>50*1024*1024){ //最大50m
    echo "文件过大！";
    return ;
}
//返回的文件
$fileMIME = fileType2MIME(pathinfo($file_path, PATHINFO_EXTENSION));
header("Content-type: $fileMIME");
//按照字节大小返回
header("Accept-Ranges: bytes");
//返回文件大小
header("Accept-Length: $file_size");
//这里客户端的弹出对话框，对应的文件名
//header("Content-Disposition: attachment; filename=".$file_name);
//向客户端回送数据
$buffer=1024;
//为了下载的安全，我们最好做一个文件字节读取计数器
$file_count=0;
//这句话用于判断文件是否结束
while(!feof($fp) && ($file_size-$file_count>0) ){
    $file_data=fread($fp,$buffer);
    //统计读了多少个字节
    $file_count+=$buffer;
    //把部分数据回送给浏览器;
    echo $file_data;
}
//关闭文件
fclose($fp);
?>