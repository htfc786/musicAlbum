const Photo = {
  template: /*html*/`
  <div id="image">
    <div id="image-header">
      <div class="image-addimg-btn" @click="uploadImage()">添加图片</div>
      <div class="image-frush-btn" @click="getImage()">刷新</div>
      <router-link :to="{ name: 'main' }"><div class="image-close">返回</div></router-link>
    </div>
    <div id="image-box">
      <div class="image-item" v-for="photo in photos" :key="photos.photoOrder">
        <img :src="photo.photoUrl" alt="" class="image-pic">
        <div class="image-del" @click="delImage(photo._id)">
          <img src="../src/image/make-image-cha.png">
        </div>
        <!--<div class="image-turn">
          <img onclick="" src="../src/image/make-image-turn.png">
        </div>-->
        <div class="image-next">
          <div></div>
          <img @click="moveImage(photo._id, 'up')" class="image-next-left" src="./src/image/make-image-next.png">
          <img @click="moveImage(photo._id, 'down')" class="image-next-right" src="./src/image/make-image-next.png">
        </div>
      </div>
    </div>
    
    <input ref="imageUpload" id="image-uploadFiles" name="files" type="file" multiple="" accept=".jpg,.jpeg,.png,.bmp,.gif,.webp" style="display:none;"/>
    <div id="image-screenBlack" v-show="loading.isShow"></div>
    <div id="image-screenLoading" v-show="loading.isShow">
      <div id="image-screenLoading-background"></div>
      <img id="image-screenLoading-image" src="/src/image/make-image-loading.png">
      <span id="image-uploadMsg">{{loading.showText}}</span>
    </div>
    
    <div id="image-getInfo-loading" v-show="false">
      <div id="image-getInfo-loading-background"></div>
      <img id="image-getInfo-loading-image" src="/src/image/make-image-loading.png">
      <span id="image-getInfo-loading-text" v-show="false">加载中</span>
    </div>

  </div>
  `,
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
    delImage:function (photoId) {
      //提示框
      if(!confirm("此操作将会删除此照片，确定要继续吗？")){
        return;
      }
      //删除请求
      const that = this;
      axios({
        url: '/albumapi-make-photo-del',
        method: 'post',
        data: {
          access_token: this.access_token,
          photoId: photoId,
        },
      })
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
        axios({
            url: '/albumapi-make-photo-add',
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            data: fd,
            onUploadProgress: function (e) {
              let filePercent = Math.round(e.loaded / e.total * 100);
              that.loading.showText = "上传进度：" + filePercent + "%";
            },
          })
          .then(function(e){
            // 加载
            that.loading.isShow = false;
            that.loading.showText = "上传成功！";
            // 提示框
            console.log(e)
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
  },
}