<template>
  <div id="music">
    <div id="music-header">
      <router-link :to="{ name: 'albummake' }"><div class="music-close">返回</div></router-link>
      <div id="music-menu">
        <div class="music-menu-btn music-menu-btnon">全部</div>
      </div>
    </div>

    <div class="music-list">
      <div class="music-item" v-for="music in musics" >
        <div class="music-item-img">
            <img :src="image.yinfu">
        </div>
        <div class="music-item-title" @click="musicPlayer(music.musicUrl)">{{ music.musicName }}{{ music.musicComposer ? ' - ' + music.musicComposer : '' }}</div>
        <div class="music-item-ok" @click="changeMusic(music._id);">选择</div>
      </div>
    </div>
    
    <audio ref="musicPlayer" style="display:none;"></audio>
  </div>
</template>

<script>
import yinfuimg from '@/assets/images/make-music-yinfu.png';

import axios from 'axios';
export default {
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      // 照片
      musics: [],

      image: {
        yinfu: yinfuimg,
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
      axios({
          url: '/albumapi-make-music-getlist',
          method: 'post',
          data: {
            access_token: this.access_token,
          },
        })
        .then(function (e) {
          that.musics = e.data.data;
        })
    },
    changeMusic: function(musicId){
      const that = this;
      axios({
          url: '/albumapi-make-music-change',
          method: 'post',
          data: {
            access_token: this.access_token,
            albumId: this.albumId,
            musicId: musicId
          },
        })
        .then(function (e) {
          if (e.data.code != 200){
            alert("发生错误："+e.data.error)
            return;
          }
          alert(e.data.msg)
          that.$router.push({ name: 'main' });
        })
    },
    musicPlayer: function(musicUrl){
      var musicPlayer = this.$refs.musicPlayer;
      musicPlayer.src = musicUrl;
      musicPlayer.play()
    },
  }
}
</script>

<style scoped>
#music-header {
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
#music-menu {
    white-space: nowrap;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    height: 60px;
    line-height: 58px;
    background-color: #fff;
    border-bottom: 1px solid #D6D4D4;
    overflow: auto hidden;
    margin: 0 80px 0 65px;
}
#music-menu div {
    font-size: 16pt;
    text-align: center;
    margin: 0 7.5px;
    display: inline-block;
}
.music-menu-btn{
    color: rgb(181, 181, 181); 
    width: auto; 
    margin: 0px 7.5px; 
    border-bottom: 0px solid rgb(255, 255, 255);
}
.music-menu-btnon{
    border-bottom: 2px solid #444;
    width: auto; 
    margin: 0px 7.5px; 
    color: #444;
}
.music-close {
    position: absolute;
    width: 60px;
    height: 40px;
    margin-top: 10px;
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 19px;
    line-height: 39px;
    text-align: center;
    padding-left: 0px;
}
#music-search-btn{
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
}
.music-list {
    /* position: absolute; */
    margin-top: 60px;
    /* height: 620px; */
    width: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
.music-item {
    position: relative;
    height: 80px;
    width: 100%;
    border-bottom: 1px ridge #c7c7c7;
    background-color: #fff;
}
.music-item-img {
    position: relative;
    float: left;
    margin: 24px 0 0 15px;
}
.music-item-title {
    position: relative;
    float: left;
    text-align: left;
    margin-left: 10px;
    max-width: 55%;
    font-size: 15pt;
    font-weight: bold;
    color: black;
    height: 80px;
    line-height: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.music-item-ok {
    position: relative;
    float: right;
    text-align: center;
    height: 40px;
    line-height: 38px;
    margin: 20px 20px 0 0;
    font-size: 20px;
    width: 70px;
    background-color: #444;
    color: #fff;
    border-radius: 20px;
}
#music-getInfo-loading{
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
#music-getInfo-loading-background{
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-color: black;
    opacity: 0.68;
}
#music-getInfo-loading-image{
    position: absolute;
    z-index: 1;
    top: 15px;
    left: 16px;
    animation: image-turn 1s linear infinite;
}
#music-getInfo-loading-text{
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
