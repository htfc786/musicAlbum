<template>
  <div id="image">
    <div id="image-header">
      <div class="image-addimg-btn" @click="uploadImage()">添加图片</div>
      <div class="image-frush-btn" @click="getImage()">刷新</div>
      <router-link :to="{ name: 'albummake' }"><div class="image-close">返回</div></router-link>
    </div>
    <div id="image-box">
      <div class="image-item" v-for="photo in photos" :key="photos.photoOrder">
        <img :src="photo.photoUrl" alt="" class="image-pic">
        <div class="image-del" @click="delImage(photo._id)">
          <img :src="image.del">
        </div>
        <!--<div class="image-turn">
          <img onclick="" :src="image.turn">
        </div>-->
        <div class="image-next">
          <div></div>
          <img @click="moveImage(photo._id, 'up')" class="image-next-left" :src="image.move">
          <img @click="moveImage(photo._id, 'down')" class="image-next-right" :src="image.move">
        </div>
      </div>
    </div>
    
    <input ref="imageUpload" id="image-uploadFiles" name="files" type="file" multiple="" accept=".jpg,.jpeg,.png,.bmp,.gif,.webp" style="display:none;"/>
    <div id="image-screenBlack" v-show="loading.isShow"></div>
    <div id="image-screenLoading" v-show="loading.isShow">
      <div id="image-screenLoading-background"></div>
      <img id="image-screenLoading-image" :src="image.loading">
      <span id="image-uploadMsg">{{loading.showText}}</span>
    </div>
    
    <div id="image-getInfo-loading" v-show="false">
      <div id="image-getInfo-loading-background"></div>
      <img id="image-getInfo-loading-image" :src="image.loading">
      <span id="image-getInfo-loading-text" v-show="false">加载中</span>
    </div>

  </div>
</template>

<script>
import delImg from '@/assets/images/make-image-cha.png';
import turnImg from '@/assets/images/make-image-turn.png';
import moveImg from '@/assets/images/make-image-next.png';
import loadingImg from '@/assets/images/make-image-loading.png';

import API from '@/network/API';
export default {
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      // 照片
      photos: [],
      // 加载
      loading: {
        isShow: false,
        showText: "",
      },

      image: {
        del: delImg,
        turn: turnImg,
        move: moveImg,
        loading: loadingImg,
      }
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      location.href = "./login.html";
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");
    this.albumId = this.$route.params.albumId;

    this.getImage();
  },
  methods: {
    getImage: function() {
      const that = this;
      API.show.getphoto(this.albumId)
        .then(function (e) {
          that.photos = e.data.data;
        })
    },
    delImage:function (photoId) {
      //提示框
      if(!confirm("此操作将会删除此照片，确定要继续吗？")){
        return;
      }
      //删除请求
      const that = this;
      API.make.photo.del(photoId)
      .then(function (e) {
        if (e.data.code == 200){
          alert(e.data.msg);
        } else {
          alert("发生错误："+e.data.error);
        }
        that.getImage();
      })
    },
    uploadImage: function () { // 上传图片
      const uploadFiles = this.$refs['imageUpload'];

      uploadFiles.click();

      uploadFiles.onchange = () => {
        // 没有选择文件
        if (!uploadFiles.files[0]) {
          alert("请选择文件！");
          return;
        }

        //显示加载
        this.loading.isShow = true;
        this.loading.showText = "上传中。。。";

        // 用FormData传输
        let fd = new FormData();

        fd.append("access_token", this.access_token);
        fd.append("albumId", this.albumId);

        for (var i=0;i<uploadFiles.files.length;i++) {
            fd.append("files["+i+"]", uploadFiles.files[i]);
        }

        const that = this;
        API.make.photo.add(fd, function (e) {
          let filePercent = Math.round(e.loaded / e.total * 100);
          that.loading.showText = "上传进度：" + filePercent + "%";
        })
          .then(function(e){
            // 加载
            that.loading.isShow = false;
            that.loading.showText = "上传成功！";
            // 提示框
            console.log(e)
            var fileTypes = e.data.fileType;
            var alertText = "";
            for(var i = 0, len = fileTypes.length; i < len; i++) {
              var fileType = fileTypes[i];
              alertText += fileType.neme + "：" + fileType.msg + "\n"
            }
            alert(alertText);
            
            that.getImage();
          })

      }
    },
    moveImage: function (photoId, photoAction) {
      if (photoAction != "up" && photoAction != "down"){
        return;
      }
      // 移动请求
      const that = this;
      API.make.photo.del(photoId, photoAction)
        .then(function (e) {
          that.getImage();
        })
    },
  },
}
</script>

<style scoped>
#image-header {
    position: fixed;
    margin: 0 8px 0 8px;
    top: 0;
    left: 0;
    bottom: 0px;
    width: 100%;
    height: 60px;
    background-color: #fff;
    z-index: 999;
}
.image-close {
    position: absolute;
    width: 100px;
    height: 40px;
    margin-top: 10px;
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 20px;
    line-height: 40px;
    text-align: center;
    padding-left: 0px;
    margin-left: 20px;
}
.image-frush-btn{
    position: absolute;
    width: 60px;
    height: 40px;
    margin-top: 10px;
    margin-right: 15px;
    right: 0;
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 19px;
    line-height: 39px;
    text-align: center;
    padding-left: 0px;
    margin-right: 160px;
}
.image-addimg-btn{
    position: absolute;
    width: 125px;
    height: 40px;
    margin-top: 10px;
    right: 0;
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 19px;
    line-height: 39px;
    text-align: center;
    padding-left: 0px;
    margin-right: 20px;
}
.image-list {
    /* position: absolute; */
    margin-top: 60px;
    /* height: 620px; */
    width: 100%;
    /* overflow-y: auto; */
    -webkit-overflow-scrolling: touch;
}
#image-box{
    /* position: absolute; */
    margin-top: 60px;
    /* height: 620px; */
    width: 100%;
    /* overflow-y: auto; */
    -webkit-overflow-scrolling: touch;
}
.image-msg{
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
}
.image-item{
    width: 120px; 
    height: 150px; 
    position: relative; 
    float: left; 
    width: 120px; 
    margin: 16px 0 0 8px; 
    height: 150px; 
    border: 1px solid #BFBFBF; 
    box-shadow: 2px 2px 3px #aaaaaa;
}
.image-pic{
    position: absolute; 
    width: 120px; 
    height: 150px; 
    Object-fit: cover;
}
.image-del{
    position: absolute; 
    right: -2px;
}
.image-del img{
    width: 25px; 
    height: 25px;
    margin-right: 2px;
}
.image-turn{
    position: absolute;
}
.image-turn img{
    width: 25px;
    height: 25px;
}
.image-next{
    position: absolute; 
    bottom: -2px; 
    width: 120px; 
    height: 30px;
}
.image-next-left{
    position: absolute; 
    opacity: 0.8;
    width: 60px; 
}
.image-next-right{
    position: absolute; 
    opacity: 0.8; 
    right: 0px; 
    transform: scale(-1,1);
    width: 60px; 
}
#image-screenBlack{
    position: absolute; 
    width: 100%;
    height: 100%; 
    /* background-color: black; */
    opacity: 0.5; 
    top:0;
    left:0;
    z-index: 9999;
}
#image-screenLoading{
    position: absolute;
    /* top: 40%; */
    width: 300px;
    left: 100px;
    height: 100px;
    border-radius: 10px;
    overflow: hidden;
    margin: auto;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 10000;
}
#image-screenLoading-background{
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-color: black;
    opacity: 0.68;
}
#image-screenLoading-image{
    position: absolute;
    z-index: 1;
    top: 15px;
    left: 16px; 
    animation: image-turn 1s linear infinite;
}
#image-uploadMsg{
    position: absolute;
    line-height: 70px;
    top: 15px;
    left: 102px;
    z-index: 1;
    color: white;
    font-size: 22px;
}
#image-getInfo-loading{
    position: absolute;
    /* top: 40%; */
    width: 100px;
    /* left: 100px; */
    height: 135px;
    border-radius: 5px;
    overflow: hidden;
    margin: auto;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
}
#image-getInfo-loading-background{
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-color: black;
    opacity: 0.68;
}
#image-getInfo-loading-image{
    position: absolute;
    z-index: 1;
    top: 15px;
    left: 16px;
    animation: image-turn 1s linear infinite;
}
#image-getInfo-loading-text{
    position: absolute;
    line-height: 70px;
    top: 70px;  
    left: 0;
    right: 0;
    text-align: center;
    z-index: 1;
    color: white;
    font-size: 22px;
}
</style>
