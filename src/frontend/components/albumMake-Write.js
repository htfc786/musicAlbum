const Write = {
  template: /*html*/`
  <div id="write">
    <div id="write-header">
      <router-link :to="{ name: 'main' }"><div class="write-close">返回</div></router-link>
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
         <img src="/src/image/make-write-posup.png">
        </div>
        <div class="write-bigdown" @click="moveImage(photo._id, 'down')">
          <img src="/src/image/make-write-posdown.png">
        </div>
      </div>
    </div>

    <div id="write-getInfo-loading" v-show="loading.isShow">
      <span id="write-getInfo-loading-text">更改的数据保存成功！</span>
    </div>
  </div>
  `,
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
      //
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