const Main = {
  template: /*html*/`
  <div id="main">
    <div class="footer">
      <router-link :to="{ name: 'template' }" style="background-image: url(./src/image/make-model.png);"></router-link>
      <router-link :to="{ name: 'music' }" style="background-image: url(./src/image/make-music.png);"></router-link>
      <router-link :to="{ name: 'photo' }" style="background-image: url(./src/image/make-pic.png);"></router-link>
      <router-link :to="{ name: 'write' }" style="background-image: url(./src/image/make-write.png);"></router-link>
      <a :href="WatchUrl" style="background-image: url(./src/image/make-watch.png);"></a>
    </div>
    <iframe id="showalbumFrame" width="100%" frameborder="0" src=""></iframe>
    <div onclick="location.href='../';" class="back">返回</div>
  </div>
  `,
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      WatchUrl: "",
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
    this.WatchUrl = "./albumwatch.html#/"+this.albumId;
  },
}