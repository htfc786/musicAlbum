const Music = {
  template: /*html*/`
  <div id="music">
    <div id="music-header">
      <router-link :to="{ name: 'main' }"><div class="music-close">返回</div></router-link>
      <div id="music-menu">
        <div class="music-menu-btn music-menu-btnon">全部</div>
      </div>
    </div>

    <div class="music-list">
      <div class="music-item" v-for="music in musics" >
        <div class="music-item-img">
            <img src="./src/image/make-music-yinfu.png">
        </div>
        <div class="music-item-title" @click="musicPlayer(music.musicUrl)">{{ music.musicName }}{{ music.musicComposer ? ' - ' + music.musicComposer : '' }}</div>
        <div class="music-item-ok" @click="changeMusic(music._id);">选择</div>
      </div>
    </div>
    
    <audio ref="musicPlayer" style="display:none;"></audio>
  </div>
  `
  ,
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      // 照片
      musics: [],
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