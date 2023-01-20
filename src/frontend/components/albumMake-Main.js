const Main = {
  template: /*html*/`
  <div id="main">
    <iframe ref="mainIframe" class="mainIframe" frameborder="0" :src="templateIndex"></iframe>
    <div @click="switchsound()" class="soundImage">
      <img src="./src/image/music_note_big.png" ref="soundImage" class="soundImageTurn">
    </div>
    <div onclick="location.href='./index.html';" class="back">返回</div>
    <audio :src="musicUrl" ref="musicPlay" autoplay="autoplay" loop="true"></audio>
    <div class="footer">
      <router-link :to="{ name: 'template' }" style="background-image: url(./src/image/make-model.png);"></router-link>
      <router-link :to="{ name: 'music' }" style="background-image: url(./src/image/make-music.png);"></router-link>
      <router-link :to="{ name: 'photo' }" style="background-image: url(./src/image/make-pic.png);"></router-link>
      <router-link :to="{ name: 'write' }" style="background-image: url(./src/image/make-write.png);"></router-link>
      <a :href="WatchUrl" style="background-image: url(./src/image/make-watch.png);"></a>
    </div>
  </div>
  `,
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      WatchUrl: "",

      albumName: "",
      templateIndex: "",
      musicUrl: "",
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

    //预览 地址
    this.WatchUrl = "./albumshow.html#/"+this.albumId;

    this.getAlbumInfo()
  },
  methods: {
    getAlbumInfo: function(){
      const that = this;
      axios({
          url: '/albumapi-show-albumdata',
          method: 'post',
          data: {
            albumId: this.albumId,
          },
        })
        .then(function (e) {
          if (e.data.code==200){
            document.title = e.data.data.albumName + " - 编辑相册";
            that.albumName = e.data.data.albumName;
            that.musicUrl = e.data.data.musicUrl;
            //拼接请求链接
            var photoRequestHost = axios.defaults.baseURL+"/albumapi-show-photo-get";
            that.templateIndex = e.data.data.templateIndex + "?requestHost=" + photoRequestHost + "&albumName=" + that.albumName + "&albumId=" + that.albumId;

            return;
          }
          alert("没有此相册！");
          location.href='./index.html';
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