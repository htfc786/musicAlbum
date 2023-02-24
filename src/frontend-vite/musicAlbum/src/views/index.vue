<template>
  <div id="index">
    <h2 id="page-title">{{ username }} 的个人空间</h2>
    <h3 id="little-page-title" style="margin: 8px;">
      <a @click="logout()">登出</a>
    </h3>
    <div id="newbookbtn" @click="newAlbum()">+ 制作新相册</div>
    <div id="album-list" style="margin-top: 20px;">
      <div v-show="!albums.length" class="album-red-msg">当前用户中没有相册，创建一个吧</div>
      <div class="item" v-for="album in albums">
        <div class="date">
          <div class="timediv"></div>
          <div class="time">{{ album.albumCreateDate }}</div>
          <div class="sharebtn" @click="shareAlbum(album._id)">
            <img class="shareicon" :src="image.share">
          </div>
        </div>
        <div class="info">
          <div @click="openAlbum(album._id)">
            <div class="pic" :style="{ 'background-image': 'url('+ (album.albumCover ? album.albumCover : image.onimage) +')' }"></div>
            <div class="word">{{ album.albumName }}</div>
            <div class="num">共 {{ album.photoNum }} 张照片</div>
            <div class="pv">{{ album.albumCreateDate }}</div>
          </div>
          <div class="get-pic" @click="openAlbumPhoto(album._id)">提取照片</div>
          <div class="edit" @click="openAlbumMake(album._id)">编   辑</div>
          <div class="del" @click="delAlbum(album._id)"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
//引入图片
import share from '@/assets/images/index-share.png'
import onimage from '@/assets/images/index-onimage.png'

import axios from 'axios';
export default {
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albums: [],

      image: {
        share: share,
        onimage: onimage,
      }
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      //location.href = "./login.html";
      this.$router.push({ name:"login" });
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");

    document.title = this.username + " 的个人空间"

    this.getAlbum();
  },
  methods:{ // 方法
    getAlbum: function () {
      axios({ // 请求
          method: 'POST',
          url:'/albumapi-album-getmy',
          data: {
            access_token: this.access_token,
          }
        })
        .then(res => {
          console.log(res.data)
          if (res.data.code != 200){  //不是200 处理失败
             this.$router.push({ name:"login" });
            return;
          }
          console.log(res.data.data.albumData)
          this.albums = res.data.data.albumData;
        });
    },
    newAlbum: function () {
      if(!confirm("确定要新建吗？")){
          return;
      }
      axios({ // 请求
          method: 'POST',
          url:'/albumapi-album-add',
          data: {
            access_token: this.access_token,
          }
        })
        .then(res => {
          console.log(res.data)
          if (res.data.code != 200){  //不是200 处理失败
             this.$router.push({ name:"login" });
            return;
          }
          this.getAlbum();
        });
    },
    delAlbum: function (albumId) {
      if(!confirm("此操作将会删除此相册，确定要删除吗？")){
          return;
      }
      if(!confirm("删除后不可恢复，确定要删除吗？")){
          return;
      }
      //console.log("删除：", albumId);
      axios({ // 请求
          method: 'POST',
          url:'/albumapi-album-del',
          data: {
            access_token: this.access_token,
            album_id: albumId,
          }
        })
        .then(res => {
          console.log(res.data)
          if (res.data.code != 200){  //不是200 处理失败
            console.log(res.data);
            alert(res.data.error)
            return;
          }
          this.getAlbum();
        });

    },
    openAlbum: function (albumId) {
      this.$router.push({
        name:"albumshow",
        params: {
          albumId: albumId,
        },
      });
    },
    openAlbumPhoto: function (albumId) {
      this.$router.push({
        name:"albumphoto",
        params: {
          albumId: albumId,
        },
      });
    },
    openAlbumMake: function (albumId) {
      this.$router.push({
        name:"albummake",
        params: {
          albumId: albumId,
        },
      });
    },
    shareAlbum: function (albumId) {
      let content = indexUrl+"/albumshow.html#"+albumId;

      let copy = (e)=>{
        e.preventDefault()
        e.clipboardData.setData('text/plain',content)
        document.removeEventListener('copy',copy)
      }
      document.addEventListener('copy',copy)
      document.execCommand("Copy");
      alert("已复制相册链接");
    },
    
    logout: function () {
      if (!window.confirm('是否退出?')) {
        return;
      }
      localStorage.removeItem("access_token");
      localStorage.removeItem("userid");
      localStorage.removeItem("username");
      localStorage.removeItem("isadmin");
       this.$router.push({ name:"login" });
    }
  },
}
</script>

<style scoped>
  #index {
    background: #fff;
    width: 500px;
    margin: 0 auto;
  }
  #page-title {
    margin: 10px 0 0 0;
    text-align: center;
  }
  #newbookbtn {
    width: 485px;
    height: 60px;
    background-color: #555;
    border-radius: 5px;
    font-weight: bold;
    font-size: 25px;
    text-align: center;
    line-height: 60px;
    color: #fff;
    margin: 8px;
  }
  #album-list{
    position: relative;
    width: 500px;
    min-height: 100%;
  }
  .album-red-msg{
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #F00;
    font-size: 25px;
    font-weight: bold;
  }
  .item {
    width: 100%;
    height: 306px;
    overflow: hidden;
    padding-bottom: 20px;
    position: relative;
  }
  .item .date {
      position: relative;
      width: 500px;
      height: 27px;
      margin-top: 5px;
  }
  .item .timediv {
    width: 10px;
    height: 28px;
    background-color: #444;
    position: relative;
    float: left;
  }
  .item .time {
    position: relative;
    float: left;
    left: 10px;
    font-size: 23px;
    color: rgba(0, 0, 0, 0.6);
  }
  .item .sharebtn {
    position: relative;
    top: 2px;
    left: 408px;
    width: 75px;
    height: 35.7px;
    box-shadow: 0px 1px 4px rgb(0 0 0 / 10%);
    /* opacity: 0; */
    /* display: none; */
  }
  .item .shareicon {
    width: 100%;
    position: absolute;
    margin: 0;
    padding: 0;
    top: 0;
    left: 0;
  }
  .item .info {
    position: relative;
    width: 463px;
    left: 15px;
    margin-top: 5px;
    height: 260px;
    background-color: white;
    border: 3px solid #fff;
    box-shadow: 0px 1px 4px rgb(0 0 0 / 10%);
    border-radius: 8px;
  }
  .item .pic {
    position: absolute;
    width: 180px;
    left: 10px;
    margin-top: 10px;
    height: 180px;
    /* overflow: hidden; */
    background-position: center;
    background-size: cover;
  }
  .item .word {
    position: absolute;
    left: 210px;
    top: 12px;
    font-size: 25px;
    line-height: 34px;
    width: 247px;
    height: 102px;
    color: #6dc2f9;
    overflow: hidden;
  }
  .item .num {
    position: absolute;
    width: 200px;
    text-align: left;
    /* height: 40px; */
    line-height: 25px;
    font-size: 18px;
    left: 210px;
    top: 140px;
    /* bottom: 65px; */
    color: rgba(0, 0, 0, 0.5);
  }
  .item .pv {
    position: absolute;
    width: 200px;
    text-align: left;
    /* height: 40px; */
    line-height: 25px;
    font-size: 18px;
    left: 210px;
    /* top: 110px; */
    bottom: 65px;
    color: rgba(0, 0, 0, 0.5);
  }
  .item .get-pic {
    position: absolute;
    left: 10px;
    width: 210px;
    height: 40px;
    /* float: left; */
    line-height: 40px;
    top: 206px;
    border: 1px solid #444;
    /* background-color: #D2D2D2; */
    text-align: center;
    color: #444;
    border-radius: 6px 6px 6px 6px;
    font-size: 18px;
  }
  .item .edit {
    background-color: #444;
    /* margin-left: 125px; */
    border-radius: 6px;
    width: 210px;
    height: 42px;
    position: absolute;
    color: #fff;
    text-align: center;
    line-height: 42px;
    font-size: 20px;
    top: 206px;
    left: 241px;
  }
  .item .del {
    background-image: url(../assets/images/index-del.png);
    width: 25px;
    height: 30px;
    opacity: 0.3;
    position: absolute;
    left: 420px;
    top: 156px;
    z-index: 10;
  }

</style>
