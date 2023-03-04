<template>
 <div id="main">
    <iframe ref="mainIframe" class="mainIframe" frameborder="0" :src="templateIndex"></iframe>
    <div @click="switchsound()" class="soundImage">
      <img :src="image.soundImage" ref="soundImage" class="soundImageTurn">
    </div>
    <router-link :to="{ name: 'index' }" class="close-btn">返回</router-link>
    <audio :src="musicUrl" ref="musicPlay" autoplay="autoplay" loop="true"></audio>
    <div class="footer">
      <router-link :to="{ name: 'albummake-template' }" class="template-img"></router-link>
      <router-link :to="{ name: 'albummake-music' }" class="music-img"></router-link>
      <router-link :to="{ name: 'albummake-photo' }" class="photo-img"></router-link>
      <router-link :to="{ name: 'albummake-write' }" class="write-img"></router-link>
      <a @click="WatchUrl()" class="watch-img"></a>
    </div>
  </div>
</template>

<script>
//引入图片
import soundImage from '@/assets/images/music_note_big.png'

import API from '@/network/API';
import CONF from '@/config'
export default {
  data() {
    return {
      username: "",
      access_token: "",
      albumId: "",

      albumName: "",
      templateIndex: "",
      musicUrl: "",

      image: {
        soundImage: soundImage,
      }
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      this.$router.push({ name:"login" });
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");
    this.albumId = this.$route.params.albumId;

    this.getAlbumInfo()
  },
  methods: {
    WatchUrl: function(){
      this.$router.push({
        name:"albumshow",
        params: {
          albumId: this.albumId,
        },
      });
    },
    getAlbumInfo: function(){
      const that = this;
      API.show.albumdata(this.albumId)
        .then(function (e) {
          if (e.data.code==200){
            document.title = e.data.data.albumName + " - 编辑相册";
            that.albumName = e.data.data.albumName;
            that.musicUrl = e.data.data.musicUrl;
            //拼接请求链接
            var photoRequestHost = CONF.API_BASE_URL+"/albumapi-show-photo-get";
            that.templateIndex = e.data.data.templateIndex + "?requestHost=" + photoRequestHost + "&albumName=" + that.albumName + "&albumId=" + that.albumId;

            return;
          }
          alert("没有此相册！");
          this.$router.push({ name:"index" });
        })
    },
    switchsound: function(){
      const musicPlay = this.$refs.musicPlay;
      const soundImage = this.$refs.soundImage;
      if(musicPlay.paused){
        soundImage.classList.add("soundImageTurn")
        musicPlay.play();
      } else {
        soundImage.classList.remove("soundImageTurn")
        musicPlay.pause();
      }
    },
  }
}
</script>

<style scoped>/* 动画 */
@keyframes image-turn {
    0% {
        -webkit-transform:rotate(0deg);
    }
    100% {
        -webkit-transform:rotate(360deg);
    }
}
/* 相册显示区 */
.mainIframe {
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    left: 0;
}
.soundImage {
    position: fixed;
    right: 15px;
    top: 10px;
}
.soundImageTurn {
    animation: 3s linear 0s infinite normal none running image-turn;
}
#main .footer{
    position: fixed; 
    top: 100%;
    left: 0px; 
    max-width: 500px;
    width:100%;
    height: 70px;
    display: -webkit-flex;
    margin-top: -70px;
}
#main .footer a {
    position: relative; 
    width: 20%; 
    height: 100%; 
    background-size: 100% 100%;
}
.close-btn{
    display:block; 
    width: 80px; 
    height: 40px; 
    background-color: rgba(0, 0, 0, 0.3); 
    position: fixed; 
    text-align: center; 
    line-height: 40px; 
    color: #fff; 
    border-radius: 40px; 
    font-size: 21px;
    left: 10px; 
    top: 30px; 
    border: 1px solid rgba(255,255,255,.4);
}
.template-img {
  background-image: url(../assets/images/make-model.png);
}
.music-img {
  background-image: url(../assets/images/make-music.png);
}
.photo-img {
  background-image: url(../assets/images/make-pic.png);
}
.write-img {
  background-image: url(../assets/images/make-write.png);
}
.watch-img {
  background-image: url(../assets/images/make-watch.png);
}

</style>
