<template>
  <div id="app">
    <div v-show="!photos.length" style="margin: 8px; color: #f00; font-size: 20px;">提示：此相册没有图片！</div>
    <div id="image-list">
      <div class="image-card" v-for="photo in photos" :key="photos.photoOrder">
        <div class="image-index">{{ photo.photoOrder }}</div>
        <img :src="photo.photoUrl" class="image-photo">
        <a :href="photo.photoUrl" :download="photo.originalName" class="image-down">点击下载</a>
      </div>
    </div>
    <router-link :to="{ name: 'index' }" class="close-btn">返回</router-link>
  </div>
</template>

<script>
import API from '@/network/API';
export default {
  data() {  //数据
    return {
      photos: []
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
  }
}
</script>

<style scoped>
.image-card{
    width: 100%; 
    position: relative;
    max-width: 500px;
}
.image-index{
    width: 40px;
    height: 30px; 
    background-color: #FF9800; 
    border-radius: 15px; 
    color: #fff; 
    line-height: 30px; 
    text-align: center; 
    position: relative; 
    float: left; 
    margin-top: 10px;
    margin-left: 8px; 
    font-size: 20px; 
    font-weight: bold;
}
.image-photo{
    width: 80%; 
    position: relative; 
    margin-top: 10px; 
    margin-left: 5px; 
    box-shadow: 2px 3px 3px #656565;
}
.image-down:hover {
    background-color: #ccc;
    color: #000;
}
.image-down{
    display: inline-block;
    position: absolute;
    width: 100px;
    height: 40px;
    line-height: 40px;
    background-color: #20b1aa;
    text-decoration: none;
    text-align: center;
    color: #fff;
    margin-top: 10px;
    margin-left: -100px;
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
</style>
