<template>
  <div id="write">
    <div id="write-header">
      <router-link :to="{ name: 'albummake' }"><div class="write-close">返回</div></router-link>
    </div>
    <div id="write-box">
      <div>
        <textarea class="albumname" ref="albumname" v-model="albumName" v-on:input="nameChange()" maxlength="40" rows="3" placeholder="点击这里给相册写标题（限40字）"></textarea>
      </div>

      <!-- 如果不支持文字 -->
        <div style="margin: 8px; color: #f00; font-size: 20px;">提示：对不起，当前相册使用的模板可能不支持显示文字</div>
      
      <div class="write-item"  v-for="photo in photos" :key="photos.photoOrder">
        <div class="write-bigpic" :style="{ 'background-image': 'url('+ photo.photoUrl +')' }"></div>
        <textarea class="write-editwords" v-model="photo.photoText" v-on:input="textChange(photo._id, photo.photoOrder)" placeholder="点击这里给照片添加文字（限16字）" maxlength="16"></textarea>
        <div class="write-bigup" @click="moveImage(photo._id, 'up')">
         <img :src="image.posup">
        </div>
        <div class="write-bigdown" @click="moveImage(photo._id, 'down')">
          <img :src="image.posdown">
        </div>
      </div>
    </div>

    <div id="write-getInfo-loading" v-show="loading.isShow">
      <span id="write-getInfo-loading-text">更改的数据保存成功！</span>
    </div>
  </div>
</template>

<script>
import posupImg from '@/assets/images/make-write-posup.png';
import posdownImg from '@/assets/images/make-write-posdown.png';

import axios from 'axios';
import debounce from '@/tools/debounce';
export default {
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      albumName: "",
      // 照片
      photos: [],
      // loading
      loading: {
        isShow: false,
      },

      image: {
        posup: posupImg,
        posdown: posdownImg,
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

    this.getName()
    this.getImage();
  },
  methods: {
    getImage: function() {
      const that = this;
      axios({
          url: '/albumapi-show-photo-get',
          method: 'post',
          data: {
            access_token: this.access_token,
            albumId: this.albumId,
          },
        })
        .then(function (e) {
          that.photos = e.data.data;
        })
    },
    moveImage: function (photoId, photoAction) {
      if (photoAction != "up" && photoAction != "down"){
        return;
      }
      // 移动请求
      const that = this;
      axios({
          url: '/albumapi-make-photo-move',
          method: 'post',
          data: {
            access_token: this.access_token,
            photoId: photoId,
            photoAction: photoAction,
          },
        })
        .then(function (e) {
          that.getImage();
        })
    },
    textChange: debounce(1000, function (photoId, photoOrder){  //防抖
      var changePhotoText = this.photos[photoOrder - 1].photoText;
      // 请求
      const that = this;
      axios({
          url: '/albumapi-make-write-change',
          method: 'post',
          data: {
            access_token: this.access_token,
            photoId: photoId,
            photoNewText: changePhotoText,
          },
        })
        .then(function (e) {
          that.loading.isShow = true;
          setTimeout(function () {
            that.loading.isShow = false;
          }, 1000);
        })
    }),
    getName: function () {
      // 请求
      const that = this;
      axios({
          url: '/albumapi-show-albumdata',
          method: 'post',
          data: {
            access_token: this.access_token,
            albumId: this.albumId,
          },
        })
        .then(function (e) {
          that.albumName = e.data.data.albumName;
        })
    },
    nameChange: debounce(1000, function (){  //防抖
      var changeName = this.albumName;
      // 请求
      const that = this;
      axios({
          url: '/albumapi-make-album-namechange',
          method: 'post',
          data: {
            access_token: this.access_token,
            albumId: this.albumId,
            newName: changeName,
          },
        })
        .then(function (e) {
          that.loading.isShow = true;
          setTimeout(function () {
            that.loading.isShow = false;
          }, 1000);
        })
    }),
  },
}
</script>

<style scoped>
#write{
    background-color: rgb(246, 246, 246);
}
#write-header {
    position: fixed;
    margin: 0;
    top: 0;
    left: 0;
    bottom: 0px;
    width: 100%;
    height: 60px;
    background-color: #fff;
    z-index: 999;
}
.write-close {
    position: absolute;
    width: 100px;
    height: 40px;
    margin-top: 10px;
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 19px;
    line-height: 40px;
    text-align: center;
    padding-left: 0px;
    margin-left: 20px;
}
#write-box{
    margin-top: 70px;
}
#write .albumname {
    line-height: 30px;
    width: 470px;
    padding: 6px 12px;
    background: #e4e4e4;
    font-size: 20pt;
    resize: none;
    outline: none;
    margin: 6px 3px;
    border: 1px solid rgba(0,0,0,0.12);
}
.write-item {
    width: 500px;
    border-bottom: 1px solid #ddd;
    position: relative;
    background-color: white;
}
.write-bigpic {
    width: 160px;
    margin: 8px;
    height: 160px;
    background-size: cover;
    background-position: center;
    border: 1px solid #BFBFBF;
    box-shadow: 2px 2px 3px #aaaaaa;
    -webkit-touch-callout: none;
}
.write-editwords {
    position: absolute;
    top: 8px;
    left: 180px;
    height: 100px;
    width: 260px;
    resize: none;
    border: none;
    border-radius: 4px;
    font-size: 22px;
    line-height: 30px;
    color: #555;
    background-color: #fff;
    padding: 6px 12px;
    word-break: break-all;
    word-wrap: break-word;
}
.write-bigup {
    position: absolute;
    bottom: 8px;
    right: 16px;
}
.write-bigdown {
    position: absolute;
    bottom: 8px;
    right: 66px;
}
#write-getInfo-loading{
    position: absolute;
    width: 200px;
    height: 50px;
    border-radius: 5px;
    overflow: hidden;
    margin: auto;
    top: 15px;
    left: 0;
    right: 0;
    background-color: green;
    opacity: 0.6;
    z-index: 100000;
}
#write-getInfo-loading-text{
    position: absolute;
    top: 14px;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 1;
    color: white;
    font-size: 16px;
}

</style>
